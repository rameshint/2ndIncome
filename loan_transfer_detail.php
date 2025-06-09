<?php
include_once 'model/loans.php';
require_once 'config.ini.php';

if(isset($_POST)){
    $loanObj = new loans();
    $loanid = intval($_REQUEST['loanid']);
    $loan = $loanObj->fetch($loanid);
    header('Content-Type: application/json');
    echo json_encode($loan);
}


