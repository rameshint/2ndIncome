<?php


if(isset($_POST)){
    include_once 'model/transactions.php';
    $transactionObj = new transactions();
    $txn = $transactionObj->fetch($_POST['txnid']);
    header('Content-Type: application/json');
    echo json_encode($txn);
}