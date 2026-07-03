<?php
 
include_once 'model/transactions.php';
include_once 'model/lenders.php';
include_once 'model/investments.php';
include_once 'model/loans.php';


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

    if ($_POST['converted_to_loan'] == 1) {
        $lender = (new lenders())->getOwner();

        $narration = 'Converted to Loan';
        $total_amount = array_sum(array_filter($_POST['loanid'], fn($amount) => floatval($amount) > 0));

        $params = [
                'lenderid' => $lender->id,
                'txn_date' => $_POST['transaction_date'],
                'amount' => $total_amount,
                'transaction_type' => 'C',
                'transaction_category' => 'Loan',
                'description' => 'Interest to Loan',
                'borrower_id' => $_POST['borrowerid'],
            ];
        (new investments())->save($params);

        
        
        $params = [
            'lenderid' => $lender->id,
            'borrowerid' => $_POST['borrowerid'],
            'opening_date' => $_POST['transaction_date'],
            'bank_date' => $_POST['transaction_date'],
            'agreed_closing_date'=> date('Y-m-d', strtotime('+1 year', strtotime($_POST['transaction_date']))),
            'amount'=> $total_amount,
            'interest_type'=> 'P',
            'interest_value' => $_POST['interest_rate'],
            'commission' => 0,
            'description'=> 'Interest to Loan',
            'parent_loanid' => 0,
            'loan_opening_date' => $_POST['transaction_date'],
            'interest_loan' => 1,
        ];
        $loanid = (new loans())->save($params);
    }

    $request = [
        'transaction_date' => $_POST['transaction_date'],
        'bank_date' => $_POST['bank_date'],
        'transaction_type' => 'I',
        'narration' => $narration,
        'behalf_of' => $_POST['behalf_of'],
        'waiver' => $_POST['waiver'],
        'converted_to_loan' => $loanid,
        ];
    
    if ($_POST['converted_to_loan'] == 1) {
        $request['flag'] = 1;
    }  

    foreach($_POST['loanid'] as $loanid=>$amount){
        if(floatval($amount) > 0) {
            $request['amount'] = $amount;
            $request['loanid'] = $loanid;
            $transactionObj->save($request);
        }
    }

    header('Location:interest.php?date='.$_REQUEST['date']);
}
