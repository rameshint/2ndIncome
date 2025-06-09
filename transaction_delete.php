<?php
if($_POST['txnid'] > 0) {
    include_once 'model/transactions.php';
    $transactionObj = new transactions();
    $transactionObj->delete($_POST['txnid']);
}
