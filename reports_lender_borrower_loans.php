<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$rst = $reportObj->lenderBorrowerLoans();

?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">LENDER BORROWER LOANS</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
					<?php
					$i=1;
					foreach($rst as $row){
						if($row->lender == ''){
							echo '<table width=60% style="font-size:20px">
								<tr><th>Total Amount</th><td> '.$row->loan_amount.'</td>
									<th>Balance</th><td> '.($row->loan_amount - $row->repaid).' </td>
									</tr></table>';
						}else{
						if($row->borrower == ''){
							if($i > 3){
							echo $header.'</tbody>						</table>';
						}
							echo '<br /><h3>'.$row->lender.'&nbsp;&nbsp;&nbsp;</h3>';
							$header = '<tr><th colspan=3 align=right>Net Amount</th><td>'.$row->loan_amount.'</td><td> '.($row->loan_amount - $row->repaid).' </td></tr>';
						
						
					?>
						
						
                        <table class="table-striped" cellpadding=5 width=60%>
						<thead>
						<tr>
							<th>Lender</th><th>Borrower</th><th>Opening Date</th><th>Loan Amount</th><th>Balance</th>
						</tr>
						</thead>
						<tbody>
						<?php
						}else{
							echo '<tr>
							
								<td>'.$row->lender.'</td>
								<td>'.$row->borrower.'</td>
								<td>'.$row->opening_date.'</td>
								<td>'.$row->loan_amount.'</td>
								<td>'.($row->loan_amount - $row->repaid).'</td>
							</tr>';
						}
						}
						$i++;
						}
						echo $header;
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
