<?php
include_once 'model/loans.php';
require_once 'config.ini.php';
include_once 'model/borrowers.php';
include_once 'utils.php';
$borrowerObj = new borrowers();
$loanObj = new loans();
$date = $_REQUEST['date'];
$borrowers = $loanObj->fetchPendingInterest($date);

?>
<hr />
<h5>Pending Interest</h5>
<table class="table table-striped w-100">
    <thead><tr><th>Borrower</th><th>Primary Contact</th><th width="10%">Amount</th><th width="10%">Interest</th><th width=2%>Copy</th><th width=20%>WhatsApp</th></tr></thead>
<?php
foreach ($borrowers as $borrower){
    $total_loans = $borrowerObj->getTotalLoanDetails($borrower->id);    
    $loan_pending = CurrencyFormat($total_loans->loan_borrow - $total_loans->loan_paid);
    $interest_pending_till_date = CurrencyFormat($total_loans->total_interest - $total_loans->interest_paid);
    $total_pending = CurrencyFormat(($total_loans->total_interest - $total_loans->interest_paid) + ($total_loans->loan_borrow - $total_loans->loan_paid));
    $interest_pending_as_on_last_month = CurrencyFormat($total_loans->total_interest_as_on_last_month - $total_loans->interest_paid);
    $message = 'Loan Pending : '.$loan_pending.'%0AInterest Pending till Date : '.$interest_pending_till_date.'%0ATotal Pending : '.$total_pending.'%0AInterest Pending as on '.$date.' : '.$interest_pending_as_on_last_month . ' (To be paid)';
    $interest_message = CurrencyFormat($borrower->interest).' Interest';
    echo '<tr style="cursor:pointer" ><td onclick="payInterest('.$borrower->id.', \''.$borrower->borrower.'\')">'.$borrower->borrower.'</td>
    <td>'.$borrower->primary_contact_no.'</td>
    <td align="right">'.number_format($borrower->amount-$borrower->settled).'</td><td align="right">'.number_format($borrower->interest).'</td>
    <td onclick="copyInterest(\''.$borrower->interest.'\')"><i class="fa fa-copy interest-copy" style="cursor:pointer"></i></td>
    <td><i onclick="sendWhatsApp(\''.$interest_message.'\', \''.$borrower->primary_contact_no.'\')" class="fab fa-whatsapp" style="cursor:pointer"> Interest</i> / 
    <i onclick="sendWhatsApp(\''.$message.'\', \''.$borrower->primary_contact_no.'\')" class="fab fa-whatsapp" style="cursor:pointer"> Loan Details</i> </td>
    </tr>';
    
}
if(count($borrowers) == 0){
    echo '<tr><td colspan="3">All are paid... </td></tr>';
}
?>
</table>
