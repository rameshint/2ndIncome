<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();
class reports{
    public function lenderBorrowerLoans(){
		global $db;
		$sql = "SELECT @rowid:=@rowid+1 as rowid, lender, If(id IS NOT NULL, borrower,NULL) borrower , If(id IS NOT NULL, opening_date,NULL) opening_date,loan_amount,repaid  FROM (
SELECT l.id,a.name lender , b.name borrower, if(l.loan_opening_date is not null,l.loan_opening_date,l.opening_date)opening_date,l.closing_date, sum(l.amount) loan_amount, IFNULL(SUM(t.amount),0) repaid  FROM loans l
left JOIN (select loanid,sum(amount) amount from transactions WHERE transaction_type = 'R' GROUP BY loanid) t ON l.id = t.loanid 
LEFT JOIN lenders a ON a.id = l.lenderid
LEFT JOIN borrowers b ON b.id = l.borrowerid
WHERE l.closing_date IS null and l.status = 1
GROUP BY a.name,l.id WITH ROLLUP ) c , (SELECT @rowid:=0) as init
ORDER BY rowid desc
            ";
        return $db->query($sql)->results();
	}

    public function settlement($date){
        global $db;

        $fromDate = date('Y-m-01', strtotime($date));
        $toDate = date('Y-m-t', strtotime($date));


        $sql = "SELECT @rowid:=@rowid+1 as rowid, lender, If(id IS NOT NULL, borrower,NULL) borrower , If(id IS NOT NULL, settlement_date,NULL) settlement_date,lender_interest,recovery,commission,excess  FROM (
SELECT s.id,l.name lender, b.name borrower, sum(s.lender_interest)lender_interest,sum(s.recovery)recovery,sum(s.commission)commission,sum(s.excess)excess,s.description,s.settlement_date FROM settlement s 
INNER JOIN loans a ON s.loanid = a.id
INNER JOIN lenders l ON a.lenderid = l.id
INNER JOIN borrowers b ON a.borrowerid = b.id
WHERE s.settlement_date BETWEEN ? AND ?
GROUP BY l.name,b.name WITH ROLLUP ) c , (SELECT @rowid:=0) as init
ORDER BY rowid desc";
        return $db->query($sql, array($fromDate,$toDate))->results();
    }

    public function lender_transactions($lenderid, $fromDate, $toDate, $txnCategory){
        global $db;
        $sql = "SELECT * FROM (
                    SELECT 'Loan' head, b.name,t.amount,case when t.transaction_type IN('R','I') then 'C' ELSE 'D' END  transaction_type ,case when t.transaction_type IN( 'R','B') then 'Loan' ELSE 'Interest' END transaction_category, t.transaction_date,t.bank_date,t.behalf_of,t.waiver,t.flag,t.narration FROM loans l
                    INNER JOIN transactions t ON t.loanid = l.id
                    INNER JOIN borrowers b ON b.id = l.borrowerid
                    WHERE l.lenderid = ?
                    UNION all
                    SELECT 'Investment' head, null name,amount,transaction_type,transaction_category,txn_date,bank_date,0 behalf_of,0 waiver, 1 flag,i.description FROM investments i 
                    WHERE i.lenderid = ?
                ) a
                where a.transaction_date between ? and ? ";
                if ( $txnCategory != ''){
                  $sql .= " and a.transaction_category = '".$txnCategory."'";
                }
                $sql .= " ORDER BY transaction_date desc";
              
        return $db->query($sql, array($lenderid, $lenderid, $fromDate, $toDate))->results();
    }

    public function interestFreeLoans(){
        global $db;
        $sql = "SELECT a.name lender, b.name borrower, l.amount,ifnull(t.amount,0) paid,
case when l.loan_opening_date IS NULL then l.opening_date ELSE l.loan_opening_date end opening_date,
l.agreed_closing_date FROM loans l 
INNER JOIN lenders a ON a.id = l.lenderid
INNER JOIN borrowers b ON b.id = l.borrowerid
LEFT JOIN (SELECT loanid, SUM(amount) amount FROM transactions WHERE TRANSACTION_type = 'R' GROUP BY loanid) t ON t.loanid = l.id
WHERE l.interest_value = 0 AND l.closing_date IS NULL";
        return $db->query($sql)->results();
    }
	
	public function interestRateWiseLoans(){
		global $db;
		$sql = "SELECT * FROM (
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
               ) v ORDER BY v.roi,v.borrower  ";
		return $db->query($sql)->results();
	}

  
	
	public function unSettledLenderInterest(){
		global $db;
		
		$date = date("Y-m-d", strtotime("last day of previous month"));
		
		$sql = "SELECT g.*,l.name lender, b.name borrower FROM (
				SELECT a.id,a.opening_date, a.roi, a.lenderid,a.borrowerid, a.outstanding, a.interest, ifnull(k.settled_interest,0) settled_interest,ifnull(a.interest,0) - ifnull(k.settled_interest,0) pending FROM (
				SELECT l.id, l.opening_date, l.interest_value - ifnull(l.commission,0) roi,l.lenderid,l.borrowerid, l.amount - SUM(case when t.transaction_type = 'R'  then t.amount ELSE 0 END) outstanding,t.transaction_type, calculate_interest('C', l.amount,l.interest_value - ifnull(l.commission,0) ,l.interest_type,l.opening_date, '$date')  - sum(CASE  WHEN t.transaction_type = 'R' THEN  
							calculate_interest('C', t.amount,l.interest_value - ifnull(l.commission,0),l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),'$date') 
										   ELSE 0
										 END) interest
				FROM loans l
				left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date > '$date' then 0 ELSE 1 END = 1 
				WHERE l.`status` = 1
				GROUP BY l.id) a
				LEFT JOIN (SELECT s.loanid, SUM(lender_interest) settled_interest FROM settlement s 
				GROUP BY s.loanid ) k ON k.loanid = a.id ) g 
				LEFT JOIN borrowers b ON b.id = g.borrowerid
				LEFT JOIN lenders l ON l.id = g.lenderid
				WHERE g.pending > 0 
				ORDER BY l.name,b.name";
    
		return $db->query($sql)->results();
	}
	
	public function longUnpaidLoans(){
		global $db;
		$sql = "SELECT a.name lender, b.name borrower, l.opening_date, CONCAT(if(TIMESTAMPDIFF( YEAR, opening_date, NOW()) > 0, concat(TIMESTAMPDIFF( YEAR, opening_date, NOW()), 'y '),''), if( TIMESTAMPDIFF( MONTH, opening_date, NOW()) % 12 > 0, CONCAT(TIMESTAMPDIFF( MONTH, opening_date, NOW()) % 12,'m '),''),if(round(if(EXTRACT(MONTH FROM opening_date) IN (1,3,5,7,8,9,11) and TIMESTAMPDIFF( DAY, opening_date, NOW()) % 30.4375 = 30, 0, TIMESTAMPDIFF( DAY, opening_date, NOW()) % 30.4375),0) >0,CONCAT( round(if(EXTRACT(MONTH FROM opening_date) IN (1,3,5,7,8,9,11) and TIMESTAMPDIFF( DAY, opening_date, NOW()) % 30.4375 = 30, 0, TIMESTAMPDIFF( DAY, opening_date, NOW()) % 30.4375),0), 'd'), ''))  diff, l.agreed_closing_date,l.amount, l.amount - ifnull(s.settled,0) balance,ifnull(s.settled,0) paid FROM loans l 
INNER JOIN borrowers b ON b.id = l.borrowerid
INNER JOIN lenders a ON a.id = l.lenderid
LEFT JOIN (SELECT loanid, SUM(amount) settled FROM transactions t WHERE t.transaction_type='R'  GROUP BY t.loanid) s ON s.loanid = l.id
WHERE closing_date IS NULL AND l.status = 1 AND l.agreed_closing_date <= NOW() ORDER BY opening_date";
		$loans = [];
		foreach($db->query($sql)->results() as $obj){
			$loans[$obj->borrower][] = $obj;
		}
		return $loans;
	}

  public function OneYearSettledInterest(){
		global $db;
		$sql = "SELECT DATE_FORMAT( i.txn_date,'%Y-%b') mon, l.name, SUM(amount) amt FROM investments i 
    inner join lenders l ON l.id = i.lenderid
    WHERE transaction_category = 'Interest' AND transaction_type = 'C' AND i.txn_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), '%Y-%m-01')
    GROUP BY DATE_FORMAT( i.txn_date,'%Y-%b'), l.name
    ORDER BY i.txn_date desc, l.name";
		$Interest = [];
		foreach($db->query($sql)->results() as $obj){
			$Interest[$obj->name][$obj->mon] = $obj->amt;
		}
		return $Interest;
	}

  public function customer_transactions($month){
		global $db;

    $fromDate = date('Y-m-01', strtotime($month));
    $toDate = date('Y-m-t', strtotime($month));

		$sql = "SELECT name, transaction_date, date_format(created_on, '%Y-%m-%d') created, narration, SUM(amount) amt FROM (
SELECT b.name, transaction_date, created_on ,narration,  t.amount  FROM transactions t 
INNER JOIN loans l ON l.id = t.loanid
INNER JOIN borrowers b ON b.id = l.borrowerid
WHERE narration IS NOT NULL AND created_on BETWEEN '$fromDate' AND '$toDate'
UNION ALL 
SELECT l.name, i.txn_date,  i.created_on ,DESCRIPTION, amount FROM investments i
INNER JOIN lenders l ON l.id = i.lenderid
WHERE description IS NOT NULL AND i.created_on BETWEEN '$fromDate' AND '$toDate'
) a 
GROUP BY a.name, DATE_FORMAT(created_on, '%Y-%m-%d'),narration
order BY transaction_date desc,created_on desc"; 
		$data = [];
		foreach($db->query($sql)->results() as $obj){
		  $data[] = $obj;
		}
		return $data;
	}

  public function consolidate_report(){
		global $db;

		$sql = "WITH loan_settle as (SELECT loanid, SUM(amount) amount FROM transactions WHERE transaction_type = 'R' GROUP BY loanid ),
interests AS (SELECT id loanid, tot_int, interest, tot_int - ifnull(interest,0) AS pending_interest  FROM (
                SELECT l.id, 
                        calculate_interest('C', l.amount,l.interest_value,l.interest_type,l.opening_date, current_date) -
                            sum(CASE  WHEN t.transaction_type = 'R' THEN  
                              calculate_interest('C', t.amount,l.interest_value,l.interest_type,date_add(t.transaction_date, INTERVAL 1 DAY),current_date) 
                                             ELSE 0
                                           END)  tot_int,
                                                      sum(CASE
                                                        WHEN t.transaction_type IN ( 'I', 'E') THEN
                                                        t.amount
                                                        ELSE 0
                                                      END) interest
                from loans l
                left join transactions t ON t.loanid = l.id AND case when t.transaction_type='R' AND t.transaction_date > current_date then 0 ELSE 1 END = 1 and t.behalf_of = 0
                WHERE  l.`status` = 1 and l.opening_date <= CURRENT_DATE 
					 GROUP BY l.id
) c  WHERE c.tot_int - c.interest > 10)
SELECT l.id,  b.name borrower,b.primary_contact_no, l.opening_date, l.interest_value roi,  l.amount, ifnull(s.amount,0) paid_amount, l.amount- ifnull(s.amount,0) pending_loan, ifnull(i.pending_interest,0) pending_interest FROM loans l 
INNER JOIN borrowers b ON b.id = l.borrowerid
LEFT JOIN loan_settle s ON s.loanid = l.id 
LEFT JOIN interests i ON i.loanid = l.id
WHERE l.`status` = 1 AND (l.amount- ifnull(s.amount,0) >0 OR  ifnull(i.pending_interest,0) > 10)
ORDER BY b.name,l.opening_date
"; 
		$data = [];
		foreach($db->query($sql)->results() as $obj){
		  $data[] = $obj;
		}
		return $data;
	}  

}
