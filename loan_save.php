<?php
include 'model/loans.php';
if(isset($_POST)){
    (new loans)->save($_POST);
    header('Location:borrower_detail.php?id='.$_POST['borrowerid']);
}
