<?php
include_once 'model/transactions.php';

$transactionObj = new transactions();
$transactions = $transactionObj->fetchBorrowerTransactions(['borrower' => $_REQUEST['borrowerid'], 'transaction_type' => 'I', 'limit' => 10]);

foreach ($transactions as $transaction) {
	echo '<tr >
			<td>' . $transaction->transaction_date . '</td>
			<td>' . $transaction->narration . '</td>	
			<td>' . $transaction->amount . '</td>';
	echo '</tr>';
}

?>