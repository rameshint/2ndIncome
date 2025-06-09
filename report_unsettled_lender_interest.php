<?php
include_once 'header.php';
include_once 'utils.php';
include_once 'model/reports.php';
include_once 'model/lenders.php';
$reportObj = new reports();
$rst = $reportObj->unSettledLenderInterest();
 
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
                            <h3 class="card-title">Unsettled Lender Interest</h3>
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
							$lender = '';
							$lender_total = array();
							$total_loan = 0;
							$total_interest = 0;
                            foreach ($rst as $row) {
								if ($lender != $row->lender){
									
									if($lender != ''){
										echo '<tr>
										<td colspan=4 align=right>Total</td><th style="text-align:right">'.CurrencyFormat($lender_total[$lender]['loan']).'</th><th style="text-align:right">'.CurrencyFormat($lender_total[$lender]['interest']).'</th>
									</tr>';
									}
									
									echo '<tr>
										<th colspan=6 style="text-align:center;background-color: #70A24E;color: #2C0D51;"><h2>'.$row->lender.'</h2></th>
									</tr>';
									$lender = $row->lender;
									$lender_total[$row->lender]['loan'] = 0;
									$lender_total[$row->lender]['interest'] = 0;
								}
                                echo '<tr>
                                    <td>'.$row->lender.'</td>
                                    <td>'.$row->borrower.'</td>
                                    <td>'.$row->opening_date.'</td>
                                    <td>'.$row->roi.'</td>
                                    <td align=right>'.CurrencyFormat($row->outstanding).'</td>
                                    <td align=right>'.CurrencyFormat($row->pending).'</td>
                                </tr>';
								$lender_total[$row->lender]['loan'] += $row->outstanding;
								$lender_total[$row->lender]['interest'] += $row->pending;
								$total_loan += $row->outstanding;
								$total_interest += $row->pending;
                            }
							echo '<tr>
										<td colspan=4 align=right>Total</td><th style="text-align:right">'.CurrencyFormat($lender_total[$lender]['loan']).'</th><th style="text-align:right">'.CurrencyFormat($lender_total[$lender]['interest']).'</th>
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
											<th colspan=3>LENDER WISE TOTALS</th>
										</tr>
										<tr>
											<th>LENDER</th><th>OUT STANDING</th><th>INTEREST</th>
										</tr>
									</thead>
									<tbody>
									<?php
									foreach($lender_total as $lender => $totals){
										echo '<tr>
											<td>'.$lender.'</td>
											<td style="text-align:right">'.CurrencyFormat($totals['loan']).'</td>
											<td style="text-align:right">'.CurrencyFormat($totals['interest']).'</td>
										</tr>';
									}
									?>
									</tbody>
									<?php
									echo '<tfoot><tr>
											<td style="text-align:right">Total</td>
											<th style="text-align:right">'.CurrencyFormat($total_loan).'</th>
											<th style="text-align:right">'.CurrencyFormat($total_interest).'</th>
										</tr></tfoot>';
									?>
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
