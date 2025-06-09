<?php
include_once 'header.php';
include_once 'utils.php';
include_once 'model/reports.php';
include_once 'model/lenders.php';
$reportObj = new reports();
$rst = $reportObj->interestRateWiseLoans();
 
?>
<style>
.table td, .table th{
	padding:2px;
}
</style>
<section class="content">
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Interest Rate Wise Loans</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-6">
                        <div class="card-body">

                            <table class="table table-bordered table-striped"   cellpadding="0" >
                                <thead>
                                    <tr>
                                        <th>Lender</th>
                                        <th>Borrower</th>
                                        <th>Txn. Date</th>
                                        <th>ROI</th>
                                        <th>Amount</th>
                                        <th>Pending Interest</th>
                                    </tr>
                                </thead>
                            <?php
							$roi = -1;
							$roi_total = array();
							$total_loan = 0;
							$total_interest = 0;
                            foreach ($rst as $row) {
								if ($roi != $row->roi){
									
									if($roi >= 0){
										echo '<tr>
										<td colspan=4 align=right>Total</td><th style="text-align:right">'.CurrencyFormat($roi_total[$roi]['loan']).'</th><th style="text-align:right">'.CurrencyFormat($roi_total[$roi]['interest']).'</th>
									</tr>';
									}
									
									echo '<tr>
										<th colspan=6 style="text-align:center;background-color: #70A24E;color: #2C0D51;"><h2>'.$row->roi.'</h2></th>
									</tr>';
									$roi = $row->roi;
									$roi_total[$row->roi]['loan'] = 0;
									$roi_total[$row->roi]['interest'] = 0;
								}
                                echo '<tr>
                                    <td>'.$row->lender.'</td>
                                    <td>'.$row->borrower.'</td>
                                    <td>'.$row->opening_date.'</td>
                                    <td>'.$row->roi.'</td>
                                    <td align=right>'.CurrencyFormat($row->amount).'</td>
                                    <td align=right>'.CurrencyFormat($row->interest).'</td>
                                </tr>';
								$roi_total[$row->roi]['loan'] += $row->amount;
								$roi_total[$row->roi]['interest'] += $row->interest;
								$total_loan += $row->amount;
								$total_interest += $row->interest;
                            }
							echo '<tr>
										<td colspan=4 align=right>Total</td><th style="text-align:right">'.CurrencyFormat($roi_total[$roi]['loan']).'</th><th style="text-align:right">'.CurrencyFormat($roi_total[$roi]['interest']).'</th>
									</tr>';
							echo '<tr>
										<td colspan=4 align=right>Net Total</td><th style="text-align:right">'.CurrencyFormat($total_loan).'</th><th style="text-align:right">'.CurrencyFormat($total_interest).'</th>
									</tr>';				
                            ?>
                            </table>
                        </div>
							</div>
							 
							<div class="col-md-4">
								<table class="table table-striped" style="font-size:20px">
									<thead>
										<tr>
											<th colspan=3>ROI WISE TOTALS</th>
										</tr>
										<tr>
											<th>ROI</th><th>LOAN</th><th>INTEREST</th>
										</tr>
									</thead>
									<tbody>
									<?php
									foreach($roi_total as $roi => $totals){
										echo '<tr>
											<td>'.$roi.'</td>
											<td style="text-align:right">'.CurrencyFormat($totals['loan']).'</td>
											<td style="text-align:right">'.CurrencyFormat($totals['interest']).'</td>
										</tr>';
									}
									echo '<tr>
											<td style="text-align:right">Total</td>
											<th style="text-align:right">'.CurrencyFormat($total_loan).'</th>
											<th style="text-align:right">'.CurrencyFormat($total_interest).'</th>
										</tr>';
									?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
                </div>
            </div>
           
    </div>
</section>
<?php
include 'footer.php';
?>
