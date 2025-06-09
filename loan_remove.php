<?php

include_once 'model/loans.php';
$loanObj = new loans();
$loanid = $_REQUEST['id'];
$loanObj->remove($loanid);
header('Location:borrower_detail.php?id='.$_POST['borrowerid']);

