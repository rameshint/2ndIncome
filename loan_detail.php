<?php
include_once 'model/loans.php';
require_once 'config.ini.php';
$loanObj = new loans();
$loanid = intval($_REQUEST['loanid']);
$loan = $loanObj->fetch($loanid);
$transactions = $loanObj->getAllTransactions($loanid);

echo '
<table class="w-100" style="margin-bottom: 3px"><tr>
<td width="33.6%"><button data-id="'.$loanid.'" class="loan-edit btn btn-success w-100 "><strong>EDIT</strong></button> </td>
<td width="33.6%"><button data-id="'.$loanid.'" class="loan-transfer btn btn-primary w-100 "><strong>TRANSFER</strong></button> </td>
<td width="33.6%"><form action="loan_remove.php" onsubmit="return getConfirmation()" method="post">
<input type="hidden" name="id" value="'.$loanid.'">
<input type="hidden" name="borrowerid" value="'.$loan->borrowerid.'">
<button type="submit" class="loan-remove btn btn-danger w-100"><strong>REMOVE</strong></button> </form>
</td></tr></table>
<table class=" table-striped w-100" cellpadding=3>
<tr><th align="left">Lender</th><td>'.$loan->lender.'</td></tr>
<tr><th align="left">Opening date</th><td>'.date($date_format,strtotime($loan->opening_date)).'</td></tr>
<tr><th align="left">Closed on</th><td>'.($loan->closing_date!="" ? date($date_format,strtotime($loan->closing_date)):'Not closed yet').'</td></tr>
<tr><th align="left">Agreed on</th><td>'.date($date_format,strtotime($loan->agreed_closing_date)).'</td></tr>
<tr><th align="left">Interest Type</th><td>'.($loan->interest_type=='P'?'Percentage':'Fixed').'</td></tr>
<tr><th align="left">ROI</th><td>'.$loan->interest_value.'</td></tr>
<tr><th align="left">Commission</th><td>'.$loan->commission.'</td></tr>
<tr><th align="left">Loan Amount</th><td>'.number_format($loan->amount).'</td></tr>
<tr><th align="left">Paid</th><td>'.number_format($loan->settled).'</td></tr>
<tr><th align="left">Interest Paid</th><td>'.number_format($loan->interest_paid).'</td></tr>
<tr><th align="left">Pending Interest</th><td>'.number_format($loan->total_interest - $loan->interest_paid).'</td></tr>
<tr><th align="left">Narration</th><td>'.$loan->description.'</td></tr>
<tr><th align="left">Created On</th><td>'.$loan->createdon.'</td></tr>
</table> ';

echo '<br /><h5>Transactions</h5>';
echo '<table  class=" table-striped w-100" cellpadding=3>';
echo '<thead><tr><th width="10%">Txn Date</th><th width="10%">Bank Date</th><th>Narration</th><th width="1%"></th><th width="10%">Cr</th><th width="10%">Dr</th></tr></thead>';
foreach ($transactions as $transaction){
    echo '<tr>
    <td>'.date('d/m/y', strtotime($transaction->transaction_date)).'</td>
    <td>'.date('d/m/y', strtotime($transaction->bank_date)).'</td>
    <td>'. $transaction->narration.'</td>
    <td><i>'.$transaction->transaction_type.'</i></td>';

    if($transaction->transaction_type == 'B')
        echo '<td></td><td align="right">'.number_format($transaction->amount).'</td>';
    else
        echo '<td align="right">'.number_format($transaction->amount).'</td><td></td>';

echo '</tr>';
}
echo '</table><br ><br />';