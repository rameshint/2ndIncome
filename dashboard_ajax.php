<?php
include_once 'model/dashboard.php'; 

$dashboardObj = new dashboard();
if($_POST['widget'] == 'current_balance'){
    $data = $dashboardObj->getCurrentBalance();
    header('Content-Type: application/json');
    echo json_encode($data);
}else if($_POST['widget'] == 'pending_interest'){
    include_once 'model/loans.php';
    $loanObj = new loans();
    $date = date('Y-m-d', strtotime('last day of previous month'));
    $data = $loanObj->fetchPendingInterest($date);
    header('Content-Type: application/json');
    echo json_encode($data);
}else if($_POST['widget'] == 'uncleared_amount'){
    $data = $dashboardObj->getUnclearedAmount();
    header('Content-Type: application/json');
    echo json_encode($data);
}else if($_POST['widget'] == 'recoverables'){
    $data = $dashboardObj->getRecoverables();
    header('Content-Type: application/json');
    echo json_encode($data);
}else if($_POST['widget'] == 'unsettled_transactions'){
    include_once 'model/loans.php';
    $loanObj = new loans();
    $date = date('Y-m-d');
    $data = $loanObj->fetchLastMonthInterest($date);
    header('Content-Type: application/json');
    echo json_encode($data);
}else if($_POST['widget'] == 'turnover'){
    $data = $dashboardObj->getTurnover();
    $details = [];
    foreach($data as $d){
        $details[$d->month][$d->transaction_type] = $d->amount;
    }

    $dataPoints = array();

    for($i =11; $i>=0; $i--){
        $dataPoints['labels'][] = strtoupper(date("Y-M", strtotime(date("Y-m-01"). "-$i month")));
    }

    foreach ($dataPoints['labels'] as $month){
        $dataPoints['borrow'][] = floatval($details[$month]['B']);
        $dataPoints['return'][] = floatval($details[$month]['R']);
    }

    header('Content-Type: application/json');
    echo json_encode($dataPoints);
}else if($_POST['widget'] == 'roi_wise_loans'){
    $data = $dashboardObj->interestRateWiseLoans();
    $details = [];
    foreach($data as $d){
        $details[] = ['label' => $d->roi, 'y' => $d->amount];
    }
 

    header('Content-Type: application/json');
    echo json_encode($details);
}else if($_POST['widget'] == 'interest_collected_trend'){
    $data = $dashboardObj->getInterestCollectedTrend();
    $details = [];
    
    // Create array indexed by month for easy lookup
    $monthlyData = [];
    foreach($data as $d){
        $monthlyData[$d->month] = floatval($d->interest);
    }

    $dataPoints = array();
    
    // Generate last 12 months labels and data
    for($i = 11; $i >= 0; $i--){
        $monthKey = date("Y-M", strtotime(date("Y-m-01"). "-$i month"));
        $dataPoints['labels'][] = $monthKey;
        $dataPoints['data'][] = isset($monthlyData[$monthKey]) ? $monthlyData[$monthKey] : 0;
    }

    header('Content-Type: application/json');
    echo json_encode($dataPoints);
}



