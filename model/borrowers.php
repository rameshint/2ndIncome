<?php
include_once('vendor/autoload.php');

use PHPtricks\Orm\Database;

$db = Database::connect();

class borrowers
{
    private $fields = ['name', 'address', 'primary_contact_no', 'secondary_contact_no', 'referenced_by', 'referenced_contactno'];
    private $tablename = 'borrowers';

    public function fetchall()
    {
        global $db;
        return $db->table($this->tablename)->select()->results();
    }

    public function fetch($id)
    {
        global $db;
        return $db->table($this->tablename)->find($id)->results();
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

        if ($db->table($this->tablename)->insert($params)) {
            return true;
        }
        return false;
    }

    public function fetchAllTransactions($borrowerid)
    {
        global $db;
        return $db->table('transactions')->where("borrowerid", "=", $borrowerid)->select(['amount', 'transaction_type', 'transaction_date']);
    }

    public function getTotalLoanDetails($borrowerid)
    {
        global $db;
        $sql = "SELECT SUM(amount) loan_borrow,SUM(settled) loan_paid, SUM(interest_paid) interest_paid, SUM(total_interest) total_interest FROM (
 SELECT l.id,l.interest_type,l.interest_value,
       a.name                                  lender,
       case when t.transaction_type = 'B' AND narration NOT LIKE 'Loan transferred from%' then l.amount ELSE 0 end amount,
       
       sum(CASE
             WHEN t.transaction_type = 'R'  AND narration NOT LIKE 'Loan transferred to%' THEN t.amount
             ELSE 0
           END)                               settled,
       	calculate_interest('C', l.amount,l.interest_value,l.interest_type,l.opening_date, l.closing_date) -
		  	sum(CASE  WHEN t.transaction_type = 'R' THEN  
			  calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),l.closing_date) 
                             ELSE 0
                           END)   - SUM(CASE WHEN t.transaction_type = 'I' AND t.waiver = 1 thEN t.amount ELSE 0 END) total_interest,
                                      sum(CASE
                                        WHEN t.transaction_type = 'I' AND t.waiver = 0 THEN
                                        t.amount
                                        ELSE 0
                                      END) interest_paid,

					       l.opening_date,
			       l.closing_date,
       l.agreed_closing_date
FROM   loans l
       left join transactions t
              ON t.loanid = l.id and t.behalf_of = 0 
       left join lenders a
              ON a.id = l.lenderid
WHERE  l.borrowerid = $borrowerid and l.status= 1
GROUP  BY l.id
ORDER  BY l.opening_date DESC 
) a ";

        return $db->query($sql)->results()[0];
    }

    public function getAllLoans($borrowerid)
    {
        global $db;

        $sql = "SELECT l.id,
       a.name lender,
       l.amount,
       l.interest_value roi,
       sum(CASE
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
                                      END) interest_paid,

					       l.opening_date,
			       l.closing_date
FROM   loans l
left join transactions t ON t.loanid = l.id and t.behalf_of = 0 
left join lenders a ON a.id = l.lenderid
WHERE  l.borrowerid = $borrowerid and l.status= 1
GROUP  BY l.id
ORDER  BY l.opening_date DESC ";
        return $db->query($sql)->results();
    }
    public function fetchPendingInterest($borrowerid, $date){
        global $db;
        $sql = "SELECT * FROM (
                SELECT l.id, a.name lender,
                       l.amount,
                       sum(CASE
                             WHEN t.transaction_type = 'R' THEN t.amount
                             ELSE 0
                           END)                                settled,
                        calculate_interest('C', l.amount,l.interest_value,l.interest_type,l.opening_date,  case when l.closing_date IS NOT null then l.closing_date ELSE '$date' end) -
                            sum(CASE  WHEN t.transaction_type = 'R' THEN  
                              calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY), case when l.closing_date IS NOT null then l.closing_date ELSE '$date' end) 
                                             ELSE 0
                                           END)  -
                                                      sum(CASE
                                                        WHEN t.transaction_type = 'I' THEN
                                                        t.amount
                                                        ELSE 0
                                                      END) interest
                FROM   loans l 
                left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date > '$date' then 0 ELSE 1 END = 1 and t.behalf_of = 0
                left join lenders a ON a.id = l.lenderid
                WHERE l.borrowerid = $borrowerid and l.opening_date <= '$date' AND l.`status` = 1
                GROUP  BY l.id) c WHERE c.interest>0";
        return $db->query($sql)->results();
    }
}