<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();
class dashboard{
    public function getCurrentBalance(){
        global $db;
        $sql = "SELECT * FROM (
                SELECT a.lender account,a.amount invesment , ifnull(d.amount,0) given,  a.amount - ifnull(d.amount,0) balance 
                FROM (
                SELECT i.lenderid,l.name lender, sum(case when i.transaction_type = 'D' then -amount ELSE amount END ) amount FROM investments i 
                INNER JOIN lenders l ON l.id = i.lenderid
                GROUP BY i.lenderid
                order by i.lenderid
                ) a
                LEFT JOIN (
                SELECT l.lenderid , sum(l.amount - ifnull(t.amount, 0)) amount FROM loans l
                LEFT JOIN (select loanid, sum(amount) amount from transactions WHERE transaction_type = 'R' GROUP BY loanid) t  ON l.id = t.loanid
                where l.status = 1
                GROUP BY l.lenderid
                ) d ON d.lenderid = a.lenderid
                UNION ALL 
                SELECT 'Unsettled Interest' account,0 invesment,0 given, sum(amount) balance FROM transactions t WHERE t.flag = 0 AND t.transaction_type IN ('E','I') and waiver = 0
                ) v WHERE invesment <>0 OR given <> 0 OR balance <> 0";
        return $db->query($sql)->results();
    }
    public function getUnclearedAmount(){
        global $db;
        $sql = "SELECT a.lender , a.amount - ifnull(d.amount,0) balance FROM (
                SELECT i.lenderid,l.name lender, sum(case when i.transaction_type = 'D' then -amount ELSE amount END ) amount FROM investments i 
                INNER JOIN lenders l ON l.id = i.lenderid AND l.owner = 0
                GROUP BY i.lenderid) a
                LEFT JOIN (
						SELECT l.lenderid , sum(l.amount - ifnull(t.amount, 0)) amount FROM loans l
						LEFT JOIN (select loanid, sum(amount) amount from transactions WHERE transaction_type = 'R' GROUP BY loanid) t  ON l.id = t.loanid
						where l.status = 1
						GROUP BY l.lenderid
				) d ON d.lenderid = a.lenderid
            ";
        return $db->query($sql)->results();
    }
	
	public function getRecoverables(){
		global $db;
		$sql = "SELECT * FROM (
				SELECT l.name lender, b.name borrower,SUM(r.balance_amount) amount FROM recoverables r 
				INNER JOIN loans a ON a.id = r.loanid
				INNER JOIN borrowers b ON b.id = a.borrowerid
				INNER JOIN lenders l ON l.id = a.lenderid
				GROUP BY l.name, b.name
				) f WHERE amount > 0";
		return $db->query($sql)->results();
	}

    public function getUnsettledTransactions(){
        global $db;
        $sql = "SELECT b.name borrower,SUM( t.amount)amount FROM transactions t 
LEFT JOIN loans l ON l.id = t.loanid and l.status = 1
LEFT JOIN borrowers b ON b.id = l.borrowerid
WHERE flag = 0 AND t.transaction_type IN ('I','E') and waiver = 0
GROUP BY b.id";
        return $db->query($sql)->results();
    }

    public function getTurnover(){
        global $db;
        $sql = "SELECT upper(DATE_FORMAT(transaction_date,'%Y-%b')) month,transaction_type,SUM(amount) amount FROM transactions WHERE transaction_type IN ('B', 'R')
AND transaction_date >= date_add(last_day(DATE_SUB(CURDATE(),INTERVAL 13 MONTH)),INTERVAL 1 DAY)
GROUP BY upper(DATE_FORMAT(transaction_date,'%b')),transaction_type";
        return $db->query($sql)->results();
    }
	
	public function getGlobalSearchResults($q){
		global $db;
		$sql = "SELECT l.id, l.name, 'Lender' type FROM lenders l WHERE l.name LIKE '%$q%'
				UNION ALL 
				SELECT b.id, b.name, 'Borrower' type FROM borrowers b WHERE b.name LIKE '%$q%'";
		return $db->query($sql)->results();
	}

    public function interestRateWiseLoans(){
		global $db;
		$sql = "SELECT roi,sum(amount) amount FROM (
                    select * from (SELECT c.id,c.lender, c.borrower,c.opening_date, c.roi,sum(c.amount)amount,sum(c.tot_int )- SUM(interest) interest FROM (
                    SELECT b.id,a.name lender,l.opening_date, b.name borrower,l.interest_value roi,
                        l.amount-IFNULL(r.amount,0) amount,
                        
                        sum(CASE
                                WHEN t.transaction_type = 'R' THEN t.amount
                                ELSE 0
                            END)                                settled,
                            calculate_interest('C', l.amount,l.interest_value,l.interest_type,l.opening_date, current_date) -
                                sum(CASE  WHEN t.transaction_type = 'R' THEN  
                                calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),current_date) 
                                                ELSE 0
                                            END)  tot_int,
                                                        sum(CASE
                                                            WHEN t.transaction_type = 'I' THEN
                                                            t.amount
                                                            ELSE 0
                                                        END) interest
                    FROM   borrowers b 
                    INNER join loans l ON b.id  = l.borrowerid AND l.`status` = 1
                    left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date > current_date then 0 ELSE 1 END = 1 and t.behalf_of = 0
                    LEFT JOIN (SELECT loanid, SUM(amount) amount FROM transactions WHERE   transaction_type = 'R' GROUP BY loanid) r ON r.loanid = l.id
                    left join lenders a ON a.id = l.lenderid
                    WHERE l.opening_date <= current_date AND b.`status` = 1
                    GROUP  BY l.id,l.interest_value) c 
                    where c.amount > 0 or c.interest > 0
                    GROUP  BY c.id, c.roi)d 
                    where d.amount > 0  
               ) v GROUP BY v.roi  ";
		return $db->query($sql)->results();
	}
}
