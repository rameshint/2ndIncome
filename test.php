<?php
include_once('vendor/autoload.php');
include_once('model/transactions.php');

use PHPtricks\Orm\Database;

$db = Database::connect();
ini_set('display_errors', 1);

$sql = "select id from loans where id not in (131,132)";
$rst =  $db->query($sql)->results();
foreach ($rst as $loan) {
    $loanid = $loan->id;
    echo "Loan ID: $loanid <br>";
    $sql = " 
		SELECT
			l.id,
			b.id bid,
			b.name borrower,
			a.name lender,
			l.amount,
			SUM(
				CASE WHEN t.transaction_type = 'R' THEN t.amount ELSE 0 END
			) settled,
			calculate_interest(
				'C', l.amount, l.interest_value, l.interest_type,
				l.opening_date, CASE WHEN l.closing_date IS NOT NULL THEN l.closing_date ELSE '2026-07-03' END
			) - SUM(
				CASE WHEN t.transaction_type = 'R' THEN calculate_interest(
					'C',
					t.amount,
					l.interest_value,
					l.interest_type,
					DATE_ADD(
						t.transaction_date, INTERVAL 1 DAY
					),
					CASE WHEN l.closing_date IS NOT NULL THEN l.closing_date ELSE '2026-07-03' END
				) ELSE 0 END
			) - SUM(
				CASE WHEN t.transaction_type = 'I' THEN t.amount ELSE 0 END
			) interest
		FROM
			loans l
			LEFT JOIN transactions t ON t.loanid = l.id
			AND CASE WHEN t.transaction_type = 'R'
			AND t.transaction_date > '2026-07-03' THEN 0 ELSE 1 END = 1
			AND t.behalf_of = 0
			LEFT JOIN lenders a ON a.id = l.lenderid
			LEFT JOIN borrowers b ON b.id = l.borrowerid
		WHERE
			l.id  = $loanid
			and l.opening_date <= '2026-07-03'
			AND l.`status` = 1
		GROUP BY
			l.id";
    $result =  $db->query($sql)->results();
     
}
?>