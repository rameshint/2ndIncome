<?php
if(isset($_GET['loanid'])){
    include_once 'model/transactions.php';
    $transactionObj = new transactions();
    $txn = $transactionObj->fetchInterestLoans($_GET['loanid']);
    header('Content-Type: application/json');
    echo json_encode($txn);
}
?>