<?php
include_once 'header.php';
include_once 'model/reports.php';
include_once 'utils.php';
$reportObj = new reports();
$result = $reportObj->fetchUnsettledInterestConvertedLoans();

?>
<style>
    .table th{
        text-align:center !important;
    }
    </style>
<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Unsettled Interest Converted Loans</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <table class="table table-striped table-bordered" cellpadding="5">
                        <thead>
                            <tr>
                                 
                                <th>Borrower</th>
                                <th>Transaction Date</th>
                                <th>Amount</th> 
                                <th>Settled</th>
                                <th>Pending</th>
                            </tr>
                            </thead>
                        <tbody>
                        <?php 
                        $total_amount = 0;
                        $total_settled = 0;
                        $total_balance = 0;
                        foreach ($result as $loans){
                            echo '<tr>
                                    <td>'.$loans->borrower.'</td>
                                    <td>'.$loans->opening_date.'</td>
                                    <td align=right>'.CurrencyFormat($loans->amount).'</td>
                                    <td align=right>'.CurrencyFormat($loans->settled).'</td>
                                    <td align=right>'.CurrencyFormat($loans->balance).'</td>
                                </tr>';
                            $total_amount += $loans->amount;
                            $total_settled += $loans->settled;
                            $total_balance += $loans->balance;
                        }
                        ?>
                        </tbody> 
                        <tfoot>
                        <tr>
                            <th colspan="2">Net Total</th>
                            <td align="right"><b><?=CurrencyFormat($total_amount)?></b></td>
                            <td align="right"><b><?=CurrencyFormat($total_settled)?></b></td>
                            <td align="right"><b><?=CurrencyFormat($total_balance)?></b></td>
                        </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>
