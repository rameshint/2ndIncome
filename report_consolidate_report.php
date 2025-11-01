<?php
include_once 'header.php';
include_once 'model/reports.php';
include_once 'utils.php';
$reportObj = new reports();
$result = $reportObj->consolidate_report();
?>
 
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Consolidate Report</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
                        Date : <?=date("Y-m-d")?>
                        <table class="table table-striped table-bordered" cellpadding="2">
                            <thead><tr style="text-align:center"><th>Borrower</th><th>Contact No</th><th>Loan</th><th>Paid</th><th>Balance</th><th>Interest</th></tr></thead>
                            <tbody>
                            <?php
                            
                            $borrower = NULL;
                            $loan = 0;
                            $paid = 0;
                            $balance = 0;
                            $interest = 0;
                            $total_loan = 0;
                            $total_paid = 0;
                            $total_balance = 0;
                            $total_interest = 0;
                            foreach($result as $row){
                                
                                if ($borrower !== NULL && $borrower != $row->borrower){
                                    
                                    echo '<tr >
                                        <td>'.$borrower.'</td>
                                        <td>'.$row->primary_contact_no.'</td>
                                        <td align=right>'.CurrencyFormat($loan).'</td>
                                        <td align=right>'.CurrencyFormat($paid).'</td>
                                        <td align=right>'.CurrencyFormat($balance).'</td>
                                        <td align=right>'.CurrencyFormat($interest).'</td>
                                    </tr>';
                                    $total_loan += $loan;
                                    $total_paid += $paid;
                                    $total_balance += $balance;
                                    $total_interest += $interest;

                                    $loan = 0;
                                    $paid = 0;
                                    $balance = 0;
                                    $interest = 0;
                                }

                                $borrower = $row->borrower;
                                $loan += $row->amount;
                                $paid += $row->paid_amount;
                                $balance += $row->pending_loan;
                                $interest += $row->pending_interest;
                            }
                            echo '<tr >
                                        <td>'.$borrower.'</td>
                                        <td>'.$row->primary_contact_no.'</td>
                                        <td align=right>'.CurrencyFormat($loan).'</td>
                                        <td align=right>'.CurrencyFormat($paid).'</td>
                                        <td align=right>'.CurrencyFormat($balance).'</td>
                                        <td align=right>'.CurrencyFormat($interest).'</td>
                                    </tr>';
                                    $total_loan += $loan;
                                    $total_paid += $paid;
                                    $total_balance += $balance;
                                    $total_interest += $interest;
                            echo '</tbody><tfoot>';
                                    echo '<tr >
                                        <th colspan=2>Total</th>
                                        <td align=right><b>'.CurrencyFormat($total_loan).'</b></td>
                                        <td align=right><b>'.CurrencyFormat($total_paid).'</b></td>
                                        <td align=right><b>'.CurrencyFormat($total_balance).'</b></td>
                                        <td align=right><b>'.CurrencyFormat($total_interest).'</b></td>
                                    </tr>';
                            ?>
                            </tfoot>
                        </table>
                    </div>


                    <div class="card-body">
                        <table class="table table-striped table-bordered" cellpadding="2">
                        <thead><tr style="text-align:center"><th>Lender</th><th>Date</th><th>Loan</th><th>ROI</th><th>Paid</th><th>Balance</th><th>Interest</th></tr></thead>
                            <tbody>
                            <?php
                            
                            $borrower = NULL;
                            $loan = 0;
                            $paid = 0;
                            $balance = 0;
                            $interest = 0;
                            foreach($result as $row){
                                
                                if ($borrower !== NULL && $borrower != $row->borrower){
                                    
                                    echo '<tr style="font-weight:bold">
                                        <td colspan=2>Total</td>
                                        <td align=right>'.CurrencyFormat($loan).'</td>
                                        <td></td>
                                        <td align=right>'.CurrencyFormat($paid).'</td>
                                        <td align=right>'.CurrencyFormat($balance).'</td>
                                        <td align=right>'.CurrencyFormat($interest).'</td>
                                    </tr>';

                                    $loan = 0;
                                    $paid = 0;
                                    $balance = 0;
                                    $interest = 0;
                                }
                                if($borrower === NULL or $borrower != $row->borrower){
                                    echo '<tr><th colspan=6>'.$row->borrower.'</th></tr>';
                                }
                                echo '<tr>
                                        <td>'.$row->lender.'</td>
                                        <td>'.$row->opening_date.'</td>
                                        <td align=right>'.CurrencyFormat($row->amount).'</td>
                                        <td align=right>'.$row->roi.'</td>
                                        <td align=right>'.CurrencyFormat($row->paid_amount).'</td>
                                        <td align=right>'.CurrencyFormat($row->pending_loan).'</td>
                                        <td align=right>'.CurrencyFormat($row->pending_interest).'</td>
                                    </tr>';

                                $borrower = $row->borrower;
                                $loan += $row->amount;
                                $paid += $row->paid_amount;
                                $balance += $row->pending_loan;
                                $interest += $row->pending_interest;
                            }
                            echo '<tr style="font-weight:bold">
                            <td>Total</td>
                            <td align=right>'.CurrencyFormat($loan).'</td>
                            <td></td>
                            <td align=right>'.CurrencyFormat($paid).'</td>
                            <td align=right>'.CurrencyFormat($balance).'</td>
                            <td align=right>'.CurrencyFormat($interest).'</td>
                        </tr>';

                            ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>
