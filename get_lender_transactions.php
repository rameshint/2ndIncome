<?php
include_once 'model/lenders.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get parameters from DataTable with defaults
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = intval($_POST['order'][0]['column'] ?? 0);
$orderDirection = $_POST['order'][0]['dir'] ?? 'desc';

// Get lender ID from request
$lenderId = intval($_POST['lender_id'] ?? 0);

if (!$lenderId) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
    ]);
    exit;
}

try {
    $lenders = new lenders();
    
    // Column mapping for ordering
    $columns = ['id', 'txn_date', 'bank_date', 'description', 'amount', 'amount', 'current_balance'];
    $orderColumn = $columns[$orderColumnIndex] ?? 'id';
    
    // Get transactions with search, pagination and ordering
    $transactions = $lenders->fetchTransactionsForDataTable($lenderId, $start, $length, $searchValue, $orderColumn, $orderDirection);
    $totalRecords = $lenders->getTotalTransactions($lenderId);
    $filteredRecords = $lenders->getFilteredTransactions($lenderId, $searchValue);
    
    $data = [];
    foreach ($transactions as $transaction) {
        $credit = '';
        $debit = '';
        
        if ($transaction->transaction_type == 'C') {
            $credit = number_format($transaction->amount, 2);
        } else {
            $debit = number_format($transaction->amount, 2);
        }
        
        $data[] = [
            $transaction->id,
            date('Y-m-d', strtotime($transaction->txn_date)),
            date('d/m/y', strtotime($transaction->bank_date)),
            htmlspecialchars($transaction->description),
            $credit,
            $debit,
            number_format($transaction->current_balance, 2)
        ];
    }
    
    $response = [
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>