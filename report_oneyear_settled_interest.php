<?php
include_once 'header.php';
include_once 'model/reports.php';
include_once 'utils.php';
$reportObj = new reports();
$result = $reportObj->OneYearSettledInterest();

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
                    <h3 class="card-title">INTEREST for Last 12 Months</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <table class="table table-striped table-bordered" cellpadding="5">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php 
                                $currentDate = new DateTime();
                                foreach(range(1,12) as $mon){
                                    $month = $currentDate->format('Y-M');
                                    echo '<th>'.$month.'</th>';
                                    $currentDate->modify('first day of this month');
                                    $currentDate->modify('-1 month');
                                
                                }
                                ?>
                                <th>Total</th>
                            </tr>

                        </thead>
                        <tbody>
                        <?php
                        $month_total = [];
                        foreach ($result as $lender => $months){
                            echo '<tr>
                                    <td><b>'.$lender.'</b></td>';
                                    $currentDate = new DateTime();
                                    $lender_total = 0;
                                    foreach(range(1,12) as $mon){
                                        echo '<td align=right>'.CurrencyFormat($months[$currentDate->format('Y-M')]).'</td>'; 
                                        $lender_total += $months[$currentDate->format('Y-M')];
                                        $month_total[$currentDate->format('Y-M')] += $months[$currentDate->format('Y-M')];
                                        $currentDate->modify('first day of this month');
                                        $currentDate->modify('-1 month');
                                        
                                    }
                                    echo '<td align=right><b>'.CurrencyFormat($lender_total).'</b></td>';
                        }
                        ?>
                        </tr>
                        <tfoot>
                        <tr>
                            <th>Net Total</th>
                            <?php
                            $currentDate = new DateTime();
                            $net_total = 0;
                            foreach(range(1,12) as $mon){
                                echo '<td align=right><b>'.CurrencyFormat($month_total[$currentDate->format('Y-M')]).'</b></td>'; 
                                $net_total += $month_total[$currentDate->format('Y-M')];
                                $currentDate->modify('first day of this month');
                                $currentDate->modify('-1 month');
                            }
                            ?>
                            <td align="right"><b><?=CurrencyFormat($net_total)?></b></td>
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
