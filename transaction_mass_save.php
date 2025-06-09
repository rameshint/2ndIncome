<?php
include 'model/transactions.php';
if(isset($_POST)){

    $transactionObj = new transactions();
    $narration = $_POST['narration'];
    if(!isset($_POST['narration']) or $_POST['narration']==''){
        $narration = 'Interest Credit';
        if($_POST['behalf_of'] == 1){
            $narration = 'Behalf of';
        }
        if($_POST['waiver'] == 1){
            $narration = 'Waiver';
        }
    }
    $request = [
        'transaction_date' => $_POST['transaction_date'],
        'bank_date' => $_POST['bank_date'],
        'transaction_type' => 'I',
        'narration' => $narration,
        'behalf_of' => $_POST['behalf_of'],
        'waiver' => $_POST['waiver']
        ];

    foreach($_POST['loanid'] as $loanid=>$amount){
        if(floatval($amount) > 0) {
            $request['amount'] = $amount;
            $request['loanid'] = $loanid;
            $transactionObj->save($request);
        }
    }

    header('Location:interest.php?date='.$_REQUEST['date']);
}
