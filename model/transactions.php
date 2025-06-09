<?php
include_once('vendor/autoload.php');
include_once 'model/recoverables.php';
include_once 'model/investments.php';
include_once 'model/comments.php';
include_once 'model/lenders.php';
include_once 'model/loans.php';
use PHPtricks\Orm\Database;
$db = Database::connect();

class transactions
{
    private $fields = ['loanid','transaction_date','bank_date', 'amount','transaction_type', 'narration','behalf_of', 'flag', 'waiver'];
    private $tablename = 'transactions';
    public function fetchall(){
        global $db;
        return $db->table($this->tablename)->select()->results();
    }

    public function fetch($id){
        global $db;
        return $db->table($this->tablename)->find($id)->results();
    }

    public function save($request){
        global $db;
        $params = Array();
        foreach ($this->fields as $field){
            if(isset($request[$field])){
                $params[$field] = $request[$field];
            }
        }

        if(isset($request['id']) && $request['id'] > 0){
            $status = $db->table($this->tablename)->where('id', $request['id'])->update($params);
        }else{
            $status = $db->table($this->tablename)->insert($params);
        }
		
		if($params['transaction_type'] == 'R'){
			$this->updateClosingDate($params);
		}

        if($params['behalf_of'] == 1){

            $investmentObj = new investments();
            $lenderObj = new lenders();
            $owner = $lenderObj->getOwner();
            $loanObj = new loans();
            $loanDetails = $loanObj->getLoanDetails($params['loanid']);
            $tmp_params = [
                'txn_date' => $params['transaction_date'],
                'bank_date' => $params['bank_date'],
                'amount' => $params['amount'],
                'transaction_type' => 'D',
                'lenderid' => $owner->id,
                'description' => 'Interest sent behalf of '.$loanDetails->borrower.' to '.$loanDetails->lender,
                'transaction_category' => 'Loan'
            ];
            $investmentObj->save($tmp_params);

            $recoverableObj = new recoverables();
            $tmp_params = [
                'loanid' => $params['loanid'],
                'recovery_amount' => $params['amount'],
                'balance_amount' => $params['amount'],
            ];
            $recoverableObj->save($tmp_params);
        }

        return $status;
    }
	
	
	public function updateClosingDate($params){
		global $db;
		if(intval($params['loanid']) > 0){
			$sql = "SELECT l.id, l.amount - ifnull(SUM(t.amount),0) balance FROM loans l 
					LEFT JOIN transactions t ON l.id = t.loanid AND t.transaction_type = 'R'
					WHERE l.id = ".$params['loanid']."
					GROUP BY l.id";
			$row = $db->query($sql)->results()[0];
			if($row->balance == 0){
				$sql = "update loans set closing_date = '".$params['transaction_date']."' where closing_date is null and id = ".$params['loanid'];
				$db->query($sql);
			}
		}
	}

    public function fetchLoans($date){
        global $db;
        $sql = "SELECT * FROM (SELECT c.id loanid,b.name borrower,a.id lenderid, a.name lender ,sum(c.amount)amount,sum(c.lender_int )-ifnull(s.lender_interest,0) -ifnull(wv.amount,0) lender_interest ,SUM(tot_int) - ifnull(s.lender_interest,0)-ifnull(wv.amount,0) total_interest ,ifnull(r.recovery_amount,0)recovery_amount  FROM (
SELECT l.id, l.lenderid  ,l.borrowerid,     l.amount,     
     sum(CASE
           WHEN t.transaction_type = 'R' THEN t.amount
           ELSE 0
         END)                                settled,
      calculate_interest('L', l.amount,l.interest_value - l.commission,l.interest_type,l.opening_date, if(l.closing_date is not null AND l.closing_date <= '$date', l.closing_date, '$date')) -
          sum(ifnull(calculate_interest('L', t.amount,l.interest_value - l.commission,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),if(l.closing_date is not null AND l.closing_date <= '$date', l.closing_date, '$date')),0) ) lender_int ,
          calculate_interest('L', l.amount,l.interest_value ,l.interest_type,l.opening_date, if(l.closing_date is not null AND l.closing_date <= '$date', l.closing_date, '$date')) -
          sum(ifnull(calculate_interest('L', t.amount,l.interest_value ,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),if(l.closing_date is not null AND l.closing_date <= '$date', l.closing_date, '$date')),0) ) tot_int
FROM    loans l 
left join transactions t ON t.loanid = l.id  AND t.transaction_type='R' AND t.transaction_date <= '$date' 
WHERE l.opening_date <= '$date' and l.status = 1 AND EXISTS(SELECT 1 FROM transactions tt WHERE flag = 0 AND tt.loanid = l.id)
GROUP  BY l.id) c
left join lenders a ON a.id = c.lenderid
LEFT JOIN borrowers b ON b.id = c.borrowerid
LEFT JOIN (SELECT loanid, SUM(lender_interest) lender_interest FROM settlement GROUP BY loanid) s ON s.loanid = c.id
LEFT JOIN (SELECT loanid, ifnull(SUM(amount),0) amount FROM transactions WHERE flag = 1 AND behalf_of = 0 GROUP BY loanid) bt ON bt.loanid = c.id
LEFT JOIN (SELECT loanid, ifnull(SUM(amount),0) amount FROM transactions WHERE  waiver = 1 GROUP BY loanid) wv ON wv.loanid = c.id
LEFT JOIN (SELECT loanid, IFNULL(SUM(balance_amount),0) recovery_amount FROM recoverables WHERE balance_amount > 0 GROUP BY loanid) r ON r.loanid  = c.id
              GROUP  BY c.id
            ORDER BY a.name, b.name
			) f WHERE (lender_interest+total_interest+recovery_amount) > 0";
            
        
        return $db->query($sql)->results();
    }
    public function fetchUnSettledTransactions($date){
        global $db;
        $sql = "SELECT l.id loanid, t.id, b.name borrower,l.amount loan,s.settled , t.amount,t.transaction_date FROM transactions t 
                LEFT JOIN loans l ON l.id = t.loanid and l.status = 1
                LEFT JOIN borrowers b ON b.id = l.borrowerid
                LEFT JOIN (SELECT loanid, SUM(amount) settled FROM transactions WHERE transaction_type = 'R' GROUP BY loanid) s ON s.loanid = t.loanid
                WHERE flag = 0 AND t.transaction_type in('I','E') and waiver = 0 order by l.id, transaction_date ";
        return $db->query($sql)->results();
    }
	
	public function clearReturn($id){
		global $db;
		if(intval($id)>0){
			$sql = "select  l.id,l.name lender, b.name borrower, l.current_balance, l.net_investment, a.amount loan_amount, t.amount from transactions t 
					inner join loans a on a.id = t.loanid 
					inner join lenders l on l.id = a.lenderid
					INNER JOIN borrowers b ON b.id = a.borrowerid
					WHERE t.id = $id";
			$row = $db->query($sql)->results()[0];
			
			$note = "Clearing returns<br/>";
			$note .= '<table class="table-striped" cellpadding=3>
				<tr><th>#REFID</th><th>Lender</th><th>Borrower</th><th>Current Balance</th><th>Investment</th><th>Amount</th></tr>
				<tr><td>'.$id.'</td><td>'.$row->lender.'</td><td>'.$row->borrower.'</td><td>'.$row->current_balance.'</td><td>'.$row->net_investment.'</td><td>'.$row->amount.'</td></tr>
			</table>';
			$commentObj = new comments();
			$commentObj->save(['module'=>'lenders', 'category' => 'clearReturn', 'notes' => $note, 'rel'=>['transactions'=> $id, 'lenders'=> $row->id]]);
			
			$current_balance = $row->current_balance + $row->amount;
			(new lenders())->save(['id' => $row->id, 'current_balance' => $current_balance]);
			
			
			$this->save(['id'=>$id, 'flag' => 1]);
			
		}
	}
	
	public function unclearedReturns(){
        global $db;
        $sql = "SELECT t.id,l.name lender,t.transaction_type, b.name borrower,a.opening_date, t.transaction_date, t.bank_date, t.amount , a.amount loan_amount 
				FROM transactions t 
				INNER JOIN loans a ON a.id = t.loanid and a.status= 1
				INNER JOIN lenders l ON l.id = a.lenderid
				INNER JOIN borrowers b ON b.id = a.borrowerid
				WHERE t.transaction_type = 'R' AND t.flag = 0";
        return $db->query($sql)->results();
    }

    public function fetchBorrowerTransactions($request){
        global $db;
        $sql = "SELECT t.id, t.transaction_date,t.bank_date, a.name lender, t.amount , t.transaction_type,t.narration,t.flag,l.id loanid FROM borrowers b
                INNER JOIN loans l ON l.borrowerid = b.id  and l.status= 1
                INNER JOIN transactions t ON t.loanid = l.id AND t.transaction_type IN ('B','I','R') ";
		if($request['from_date']!= ''){
			$sql .=" AND t.transaction_date BETWEEN '".$request["from_date"]."' AND '".$request["to_date"]."'";
		}
         $sql .=" INNER JOIN lenders a ON a.id = l.lenderid
                WHERE b.id  = ".$request["borrower"];
		if($request['transaction_type'] != ''){
			$sql .= " AND t.transaction_type = '".$request['transaction_type']."' AND t.behalf_of = 0 ";
		}
                $sql .= " ORDER BY transaction_date desc";
		if(intval($request['limit']) > 0){
			$sql .= " LIMIT ".intval($request['limit']);
		}
        return $db->query($sql)->results();
    }
	
	
    public function delete($txnid){
        global $db;
		
		$txn = $this->fetch($txnid);
		
        $sql = "delete from transactions where id = ".$txnid;
        $db->query($sql);
		
		if($txn->transaction_type == 'R'){
			$sql = "SELECT l.id, l.amount - ifnull(SUM(t.amount),0) balance FROM loans l 
					LEFT JOIN transactions t ON l.id = t.loanid AND t.transaction_type = 'R'
					WHERE l.id = ".$txn->loanid."
					GROUP BY l.id";
			$row = $db->query($sql)->results()[0];
			if(intval($row->balance)>0){
				$sql = "update loans set closing_date = null where id = ".$txn->loanid." and closing_date is not null ";
				$db->query($sql);
			}
		}
    }
}