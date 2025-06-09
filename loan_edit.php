<?php
include 'model/loans.php';
if(isset($_POST)){
    $loan = (new loans())->fetchById($_POST['loanid']);
    header('Content-Type: application/json');
    echo json_encode($loan);
}
