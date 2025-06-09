<?php
include_once 'header.php';
include_once 'model/reports.php';
include_once 'utils.php';
$reportObj = new reports();
$result = $reportObj->interestFreeLoans();
?>
 
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">INTEREST FREE LOANS</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
                        <table class="table table-striped table-bordered" cellpadding="2">
                            <thead><tr><th>Date</th></th><th>Lender</th><th>Borrower</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Agreed Date</th></tr></thead>
                            <tbody>
                        <?php
                        $total = 0;
                        $paid = 0;
                        $balance = 0;
                        $head_wise_loans = [];
                        foreach ($result as $row){
                            $total += $row->amount;
                            $paid += $row->paid;
                            $balance += $row->amount - $row->paid;
                            $head_wise_loans[$row->borrower]['loan'] += $row->amount;
                            $head_wise_loans[$row->borrower]['paid'] += $row->paid;
                            $head_wise_loans[$row->borrower]['balance'] += $row->amount - $row->paid;
                            echo '<tr>
                                    <td>'.$row->opening_date.'</td>
                                    <td>'.$row->lender.'</td>
                                    <td>'.$row->borrower.'</td>
                                    <td align=right>'.CurrencyFormat($row->amount).'</td>
                                    <td align=right>'.CurrencyFormat($row->paid).'</td>
                                    <td align=right>'.CurrencyFormat($row->amount - $row->paid).'</td>
                                    <td>'.$row->agreed_closing_date.'</td>
                                </tr>';
                        }
                        ?>
                        </tbody>
                        <tfood>
                            <tr>
                                <th colspan=3 style="text-align:right">Total</th>
                                <th style="text-align:right"><?=CurrencyFormat($total)?></th>
                                <th style="text-align:right"><?=CurrencyFormat($paid)?></th>
                                <th style="text-align:right"><?=CurrencyFormat($balance)?></th>
                                <th></th>
                            </tr>
                        </tfood>
                        </table>

                        
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">INTEREST FREE LOANS - BORROWER WISE</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
                        <table class="table table-striped table-bordered" cellpadding="2">
                            <thead><tr><th>Borrower</th><th>Amount</th><th>Paid</th><th>Balance</th></tr></thead>
                            <tbody>
                        <?php
                        $total = 0;
                        $paid = 0;
                        $balance = 0;
                        
                        foreach ($head_wise_loans as $borrower => $row){
                            $total += $row['loan'];
                            $paid += $row['paid'];
                            $balance += $row['balance'];
                            
                            echo '<tr>
                                    <td>'.$borrower.'</td>
                                    <td align=right>'.CurrencyFormat($row['loan']).'</td>
                                    <td align=right>'.CurrencyFormat($row['paid']).'</td>
                                    <td align=right>'.CurrencyFormat($row['balance']).'</td>
                                </tr>';
                        }
                        ?>
                        </tbody>
                        <tfood>
                            <tr>
                                <th style="text-align:right">Total</th>
                                <th style="text-align:right"><?=CurrencyFormat($total)?></th>
                                <th style="text-align:right"><?=CurrencyFormat($paid)?></th>
                                <th style="text-align:right"><?=CurrencyFormat($balance)?></th>
                            </tr>
                        </tfood>
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
