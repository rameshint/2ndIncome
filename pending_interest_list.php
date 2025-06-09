<?php
include_once 'model/loans.php';
require_once 'config.ini.php';
$loanObj = new loans();
$date = $_REQUEST['date'];
$borrowers = $loanObj->fetchPendingInterest($date);
?>
<hr />
<h5>Pending Interest</h5>
<table class="table table-striped w-100">
    <thead><tr><th>Borrower</th><th width="10%">Amount</th><th width="10%">Interest</th><th width=2%></th></tr></thead>
<?php
foreach ($borrowers as $borrower){
    
    echo '<tr style="cursor:pointer" ><td onclick="payInterest('.$borrower->id.', \''.$borrower->borrower.'\')">'.$borrower->borrower.'</td>
    <td align="right">'.number_format($borrower->amount-$borrower->settled).'</td><td align="right">'.number_format($borrower->interest).'</td>
    <td onclick="copyInterest(\''.$borrower->interest.'\')"><i class="fa fa-copy interest-copy" style="cursor:pointer"></i></td></tr>';
    
}
if(count($borrowers) == 0){
    echo '<tr><td colspan="3">All are paid... </td></tr>';
}
?>
</table>
