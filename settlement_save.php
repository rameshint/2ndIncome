<?php
include_once 'model/settlement.php';
include_once 'model/transactions.php';
include_once 'model/investments.php';
include_once 'model/lenders.php';
include_once 'model/recoverables.php';


if (isset($_POST)) {



    $settlementObj = new settlement();
    $transactionObj = new transactions();
    $recoverableObj = new recoverables();
	
	
    foreach ($_POST['lender_interest'] as $loanid => $interest) {
        if($_POST['total'][$loanid] > 0) {
            $params = [
                'loanid' => $loanid,
                'lender_interest' => $interest,
                'recovery' => $_POST['recovery'][$loanid],
                'commission' => $_POST['commission'][$loanid],
                'excess' => $_POST['excess'][$loanid],
                'settlement_date' => date("Y-m-d")
            ];
            $settlementObj->save($params);
        }

        $lender_interest[$_POST['lenders'][$loanid]] += $interest;
        $commission += $_POST['commission'][$loanid];
        $recoveryTot += $_POST['recovery'][$loanid];

        if ($_POST['excess'][$loanid] > 0) {
            $params = [
                'loanid' => $loanid,
                'amount' => $_POST['excess'][$loanid],
                'transaction_type' => 'E',
                'transaction_date' => date("Y-m-d"),
                'narration' => 'Excess on settlement ' . date("M-Y")
            ];
            $transactionObj->save($params);
        }

        if ($_POST['recovery'][$loanid] > 0) {

            $recoveries = $recoverableObj->getRecoveries($loanid);
            $recovery_amount = $_POST['recovery'][$loanid];
            foreach ($recoveries as $recovery) {
                if($recovery_amount > 0) {
                    if(intval($recovery->balance_amount) > $recovery_amount){
                        $collected_amount = floatval($recovery->collected_amount) + $recovery_amount;
                        $balance_amount = floatval($recovery->balance_amount) - $recovery_amount;
                        $recovery_amount = 0;
                    }else {
                        $recovery_amount = $recovery_amount - floatval($recovery->balance_amount);
                        $collected_amount = floatval($recovery->collected_amount) + floatval($recovery->balance_amount);
                        $balance_amount = 0;
                    }
                    $params = [
                        'id' => $recovery->id,
                        'loanid' => $loanid,
                        'collected_amount' => $collected_amount,
                        'balance_amount' => $balance_amount,
                    ];
                    $recoverableObj->save($params);
                }else{
                    break;
                }
            }
        }
    }

    $invesmentObj = new investments();
	

	
    foreach ($lender_interest as $lenderid => $amount) {
        if ($amount > 0 && $lenderid > 0) {
            $params = [
                'lenderid' => $lenderid,
                'txn_date' => date("Y-m-d"),
                'amount' => $amount,
                'transaction_type' => 'C',
                'transaction_category' => 'Interest',
                'description' => 'Interest Capitalized for ' . date("M-Y")
            ];
            $invesmentObj->save($params);
        }
    }

    $lenderObj = new lenders();
    $owner = $lenderObj->getOwner();
    if ($commission > 0) {
        $params = [
            'lenderid' => $owner->id,
            'txn_date' => date("Y-m-d"),
            'amount' => $commission,
            'transaction_type' => 'C',
            'transaction_category' => 'Interest',
            'description' => 'Commission Capitalized for ' . date("M-Y")
        ];
        $invesmentObj->save($params);
    }
    if ($recoveryTot > 0) {
        $params = [
            'lenderid' => $owner->id,
            'txn_date' => date("Y-m-d"),
            'amount' => $recoveryTot,
            'transaction_type' => 'C',
            'transaction_category' => 'Interest',
            'description' => 'Recovery Collected for ' . date("M-Y")
        ];
        $invesmentObj->save($params);
    }

    foreach ($_POST['txns'] as $txnid => $amount){
        $params = [
            'id' => $txnid,
            'flag' => 1,
        ];
        $transactionObj->save($params);
    }

    header('Location:settlement.php');
}