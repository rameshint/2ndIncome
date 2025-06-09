<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();

class lenders
{
    private $fields = ['name', 'address','primary_contact_no','secondary_contact_no','net_investment', 'current_balance'];
    private $tablename = 'lenders';
    public function fetchall(){
        global $db;
        return $db->table($this->tablename)->select(['id','name','net_investment'])->results();
    }

    public function getOwner(){
        global  $db;
        return $db->table($this->tablename)->where('owner',1)->select()->results()[0];
    }

    public function fetch($id){
        global $db;
        $sql = "SELECT i.id,l.name, address, primary_contact_no,net_investment, l.current_balance,
sum(case when i.transaction_type = 'C' then i.amount ELSE 0 END) interest_earnings, 
sum(case when i.transaction_type = 'D' then i.amount ELSE 0 END) interest_settled 
FROM lenders l
LEFT JOIN investments i ON i.lenderid = l.id AND i.transaction_category ='Interest'
WHERE l.id = $id
GROUP BY l.id
";

        return $db->query($sql)->results();
    }

    public function getBalance($id){
        global $db;
        $sql = "SELECT a.lender account,a.amount invesment , ifnull(d.amount,0) given,  a.amount - ifnull(d.amount,0) balance 
				FROM (
				SELECT i.lenderid,l.name lender, sum(case when i.transaction_type = 'D' then -amount ELSE amount END ) amount FROM investments i 
				INNER JOIN lenders l ON l.id = i.lenderid AND l.id = $id
				GROUP BY i.lenderid
				) a
				LEFT JOIN (
				SELECT l.lenderid , sum(l.amount - ifnull(t.amount, 0)) amount FROM loans l
				LEFT JOIN (select loanid, sum(amount) amount from transactions WHERE transaction_type = 'R' GROUP BY loanid) t  ON l.id = t.loanid
				WHERE l.lenderid = $id and l.status = 1 
				GROUP BY l.lenderid
				) d ON d.lenderid = a.lenderid";
        return $db->query($sql)->results()[0];
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
			return true;
        }else{
            $status = $db->table($this->tablename)->insert($params);
			return true;
        }
        return false;
    }

    public function fetchAllTransactions($lenderid){
        global $db;
        $sql = 'select id, amount,transaction_type,txn_date,bank_date,description, case when transaction_type = "C" then  current_balance + amount else current_balance - amount end current_balance  
            from investments where lenderid ='.$lenderid. ' order by id desc';
        return $db->query($sql);
    }
}