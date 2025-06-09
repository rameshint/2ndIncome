<?php
include 'model/transactions.php';
if(isset($_POST)){
    (new transactions())->save($_POST);
    header('Location:transactions.php?borrower='.$_POST['borrower'].'&from_date='.$_REQUEST['from_date'].'&to_date='.$_REQUEST['to_date']);
}
