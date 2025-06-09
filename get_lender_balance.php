<?php
include 'model/lenders.php';
if(isset($_POST)){
    $lender = (new lenders())->getBalance($_POST['lenderid']);
    header('Content-Type: application/json');
    echo json_encode($lender);
}
