<?php
include 'model/transactions.php';
if(isset($_POST)){
    (new transactions())->save($_POST);
    header('Location:borrower_detail.php?id='.$_POST['borrowerid']);
}
