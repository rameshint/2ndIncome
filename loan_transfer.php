<?php
include_once 'model/transactions.php';
include_once  'model/lenders.php';
include_once 'model/borrowers.php';
include_once 'model/loans.php';

if(isset($_POST)){

    $lenderDetails = (new lenders())->fetch($_POST['lenderid']);
    $lenderDetails = $lenderDetails[0];

    $loanDetails = (new loans())->fetchById($_POST['id']);

    $params = [
        'loanid' => $_POST['id'],
        'amount' => $_POST['amount'],
        'transaction_type' => 'R',
        'transaction_date' => date('Y-m-d', strtotime($_POST['opening_date']. '-1 day')),
        'bank_date' => $_POST['bank_date'],
        'narration' => 'Loan transferred to '. $lenderDetails->name,
    ];
    $res = (new transactions())->save($params);

    if($res){
        $borrowerDetails = (new borrowers())->fetch($_POST['borrowerid']);
        $params = [
            'lenderid' => $_POST['lenderid'],
            'borrowerid' => $_POST['borrowerid'],
            'opening_date' => $_POST['opening_date'],
            'bank_date' => $_POST['opening_date'],
            'agreed_closing_date'=> $_POST['agreed_closing_date'],
            'amount'=> $_POST['amount'],
            'interest_type'=> $_POST['interest_type'],
            'interest_value' => $_POST['interest_value'],
            'commission' => $_POST['commission'],
            'description'=> 'Loan transferred from ' . $borrowerDetails->name,
            'parent_loanid' => $_POST['id'],
            'loan_opening_date' => ($loanDetails->parent_loanid > 0 ? $loanDetails->loan_opening_date : $loanDetails->opening_date)
        ];
        (new loans())->save($params);
    }
    header('Location:borrower_detail.php?id='.$_POST['borrowerid']);
}
