<?php
include_once('vendor/autoload.php');
include_once('model/transactions.php');

use PHPtricks\Orm\Database;

$db = Database::connect();


class loans
{
    private $fields = ['lenderid', 'borrowerid', 'opening_date', 'agreed_closing_date', 'amount', 'interest_type', 'interest_value', 'commission', 'description','parent_loanid','loan_opening_date'];
    private $tablename = 'loans';

    public function fetchall()
    {
        global $db;
        return $db->table($this->tablename)->where('status', '1')->select()->results();
    }

    public function fetchById($id)
    {
        global $db;
        $sql = "SELECT s.* , t.bank_date FROM loans s
LEFT JOIN transactions t ON t.loanid = s.id AND t.transaction_type = 'B' 
WHERE s.id = $id";
        return $db->query($sql)->results()[0];
    }

    public function fetch($id)
    {
        global $db;
        $sql = "SELECT l.* , a.name 'lender',sum(CASE
             WHEN t.transaction_type = 'R' THEN t.amount
             ELSE 0
           END)                                settled,
       	calculate_interest('C', l.amount,l.interest_value,l.interest_type,l.opening_date, l.closing_date) -
		  	sum(CASE  WHEN t.transaction_type = 'R' THEN  
			  calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),l.closing_date) 
                             ELSE 0
                           END)  total_interest,
                                      sum(CASE
                                        WHEN t.transaction_type = 'I' THEN
                                        t.amount
                                        ELSE 0
                                      END) interest_paid 
                FROM loans l
                LEFT JOIN lenders a ON a.id = l.lenderid
                left join transactions t on t.loanid = l.id and t.behalf_of = 0
                WHERE l.id = $id";
        return $db->query($sql)->results()[0];
    }

    public function save($request)
    {
        global $db;
        $params = Array();
        foreach ($this->fields as $field) {
            if (isset($request[$field])) {
                $params[$field] = $request[$field];
            }
        }

        if (isset($request['id']) && $request['id'] > 0) {
            $status = $db->table($this->tablename)->where('id', $request['id'])->update($params);
            $txn = $db->query("select id from transactions where loanid = " . $request['id'] . " and transaction_type = 'B'")->results()[0];
            $request['loanid'] = $request['id'];
            $request['id'] = 1;
        } else {
            $status = $db->table($this->tablename)->insert($params);
            $request['loanid'] = $db->lastInsertedId();
			
			$sql = "select current_balance from lenders where id = ". $request['lenderid'];
			$row = $db->query($sql)->results()[0];
			
			$current_balance = $row->current_balance - $request['amount'];
			(new lenders())->save(['id' => $request['lenderid'], 'current_balance' => $current_balance]);
			
        }
        if ($status) {
            $request['transaction_date'] = $request['opening_date'];
            $request['bank_date'] = $request['opening_date'];
            $request['narration'] = $request['description'];
            $request['transaction_type'] = 'B';
            $transactionObj = new transactions();
            $transactionObj->save($request);
        }
        return $status;
    }

    public function getAllTransactions($loanid)
    {
        global $db;
        $sql = "SELECT * FROM transactions WHERE loanid = $loanid and behalf_of = 0 ORDER BY transaction_date desc";
        return $db->query($sql)->results();
    }

    public function remove($id)
    {
        global $db;
        $db->query('update loans set status = 0 where id = ' . intval($id));
    }

    public function fetchPendingInterest($date)
    {
        global $db;

        $sql = "select id, borrower, sum(amount) amount,SUM(interest) interest from (SELECT c.id,c.borrower,sum(c.amount)amount,sum(c.tot_int )- SUM(interest) interest FROM (
                SELECT b.id,l.id loanid, b.name borrower,
                       l.amount,
                       
                       sum(CASE
                             WHEN t.transaction_type = 'R' THEN t.amount
                             ELSE 0
                           END)                                settled,
                        calculate_interest('C', l.amount,l.interest_value,l.interest_type,l.opening_date, '$date') -
                            sum(CASE  WHEN t.transaction_type = 'R' THEN  
                              calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),'$date') 
                                             ELSE 0
                                           END)  tot_int,
                                                      sum(CASE
                                                        WHEN t.transaction_type = 'I' THEN
                                                        t.amount
                                                        ELSE 0
                                                      END) interest
                FROM   borrowers b 
                INNER join loans l ON b.id  = l.borrowerid AND l.`status` = 1
                left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date > '$date' then 0 ELSE 1 END = 1 and t.behalf_of = 0
                left join lenders a ON a.id = l.lenderid
                WHERE l.opening_date <= '$date' AND b.`status` = 1
                GROUP  BY l.id) c
                GROUP  BY c.loanid HAVING sum(c.tot_int )- SUM(interest) >0 )d   
                where d.interest > 1
				GROUP BY d.id  
                ";
        

        return $db->query($sql)->results();
    }

    public function fetchLastMonthInterest($date)
    {
        global $db;

        $from = date("Y-m-01", strtotime($date. "-1 month"));
        $to = date("Y-m-t", strtotime($date. "-1 month"));
        $collected_from = date("Y-m-01", strtotime($date));
        $collected_to = date("Y-m-t", strtotime($date));

        $sql = "SELECT d.* , ifnull(f.collected_amount,0) collected_amount , if(d.interest - ifnull(f.collected_amount,0)>0,d.interest - ifnull(f.collected_amount,0),0) pending from (SELECT c.id,c.borrower,sum(c.amount)amount,sum(c.tot_int )- SUM(interest) interest FROM (
                SELECT b.id, b.name borrower,
                       l.amount,
                       sum(CASE
                             WHEN t.transaction_type = 'R' THEN t.amount
                             ELSE 0
                           END)                                settled,
                        calculate_interest('C', l.amount - IFNULL(s.amount,0),l.interest_value,l.interest_type,if(l.opening_date>'$from', l.opening_date, '$from'), '$to') -
                            sum(CASE  WHEN t.transaction_type = 'R' THEN  
                              calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),'$to') 
                                             ELSE 0
                                           END)  tot_int,
                                                      sum(CASE
                                                        WHEN t.transaction_type = 'I' THEN
                                                        t.amount
                                                        ELSE 0
                                                      END) interest
                FROM   borrowers b 
                INNER join loans l ON b.id  = l.borrowerid AND l.`status` = 1
                left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date BETWEEN '$from' and '$to' then 1 ELSE 0 END = 1 and t.behalf_of = 0
                left JOIN (SELECT loanid, sum(amount) amount from transactions t WHERE transaction_date<'$from' AND transaction_type = 'R' GROUP BY loanid) s ON s.loanid = l.id 
                left join lenders a ON a.id = l.lenderid
                WHERE l.opening_date <= '$to' AND 
					 case when l.closing_date IS NOT NULL AND l.closing_date < '$from' then 0 ELSE 1 END = 1 AND b.`status` = 1
                GROUP  BY l.id) c 
                GROUP  BY c.id)d 
                LEFT JOIN (
					 SELECT l.borrowerid  id, SUM(t.amount) collected_amount FROM loans l 
					 INNER JOIN transactions t ON t.loanid = l.id  AND t.transaction_type = 'I' AND t.behalf_of = 0 AND t.waiver = 0
					 WHERE t.transaction_date BETWEEN '$collected_from' AND '$collected_to'
					 GROUP BY l.borrowerid
					 ) f ON f.id = d.id 
                where d.interest > 0
                ";

        return $db->query($sql)->results();
    }
	
	public function fetchLenderInterestAnalysis($date)
    {
        global $db;

        $from = date("Y-m-01", strtotime($date. "-1 month"));
        $to = date("Y-m-t", strtotime($date. "-1 month"));
        $collected_from = date("Y-m-01", strtotime($date));
        $collected_to = date("Y-m-t", strtotime($date));

        $sql = "SELECT d.* , ifnull(f.collected_amount,0) collected_amount , if(d.interest - ifnull(f.collected_amount,0)>0,d.interest - ifnull(f.collected_amount,0),0) pending 
from (SELECT c.id,c.lender,sum(c.amount)amount, sum(c.act_int ) act_int,sum(c.tot_int )- SUM(interest) interest FROM (
                SELECT l.lenderid id, a.name lender,
                       l.amount,
                       sum(CASE
                             WHEN t.transaction_type = 'R' THEN t.amount
                             ELSE 0
                           END)                                settled,
                        calculate_interest('C', l.amount - IFNULL(s.amount,0),l.interest_value,l.interest_type,if(l.opening_date>'$from', l.opening_date, '$from'), '$to') -
                            sum(CASE  WHEN t.transaction_type = 'R' THEN  
                              calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),'$to') 
                                             ELSE 0
                                           END)  tot_int,
                        calculate_interest('C', l.amount - IFNULL(s.amount,0),2.4,l.interest_type,if(l.opening_date>'$from', l.opening_date, '$from'), '$to') -
                            sum(CASE  WHEN t.transaction_type = 'R' THEN  
                              calculate_interest('C', t.amount,2.4,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),'$to') 
                                             ELSE 0
                                           END)  act_int,
                                                      sum(CASE
                                                        WHEN t.transaction_type = 'I' THEN
                                                        t.amount
                                                        ELSE 0
                                                      END) interest
                FROM   borrowers b 
                INNER join loans l ON b.id  = l.borrowerid AND l.`status` = 1
                left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date BETWEEN '$from' and '$to' then 1 ELSE 0 END = 1 and t.behalf_of = 0
                left JOIN (SELECT loanid, sum(amount) amount from transactions t WHERE transaction_date<'$from' AND transaction_type = 'R' GROUP BY loanid) s ON s.loanid = l.id 
                left join lenders a ON a.id = l.lenderid
                WHERE l.opening_date <= '$to' AND 
					 case when l.closing_date IS NOT NULL AND l.closing_date < '$from' then 0 ELSE 1 END = 1 AND b.`status` = 1
                GROUP  BY l.id) c 
                GROUP  BY c.id)d 
                LEFT JOIN (
					 SELECT l.lenderid  id, SUM(t.amount) collected_amount FROM loans l 
					 INNER JOIN transactions t ON t.loanid = l.id  AND t.transaction_type = 'I' AND t.behalf_of = 0 AND t.waiver = 0
					 WHERE t.transaction_date BETWEEN '$collected_from' AND '$collected_to'
					 GROUP BY l.lenderid
					 ) f ON f.id = d.id 
                where d.interest > 0
                ";
		
        return $db->query($sql)->results();
    }

    public function getLoanDetails($loanid){
        global $db;
        $sql = "SELECT l.amount,l.opening_date,a.name lender, b.name borrower FROM loans l 
                INNER JOIN lenders a ON a.id = l.lenderid
                INNER JOIN borrowers b ON b.id = l.borrowerid
                WHERE l.id = ". $loanid;
        return $db->query($sql)->results()[0];
    }
}