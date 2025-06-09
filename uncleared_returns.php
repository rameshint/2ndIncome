<?php
include_once 'header.php';
include_once 'model/transactions.php';
$transactionObj = new transactions();
if(intval($_GET['id'])>0){
	$transactionObj->clearReturn($_GET['id']);
}


$rst = $transactionObj->unclearedReturns();



?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">UNCLEARED RETURNS</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
					<table class="table-striped" cellpadding=5 width=100% >
						<thead>
						<tr>
							<th>Lender</th><th>Borrower</th><th>Opening Date</th><th>Txn. Date</th><th>Bank Date</th><th>Txn.Type</th><th>Amount</th><th>Loan Amount</th><th></th>
						</tr>
						</thead>
						<tbody>
						
					<?php
					$i=1;
					foreach($rst as $row){
						
							echo '<tr>
								<td>'.$row->lender.'</td>
								<td>'.$row->borrower.'</td>
								<td>'.$row->opening_date.'</td>
								<td>'.$row->transaction_date.'</td>
								<td>'.$row->bank_date.'</td>
								<td>'.$row->transaction_type.'</td>
								<td>'.$row->amount.'</td>
								<td>'.$row->loan_amount.'</td>
								<td><a href="uncleared_returns.php?id='.$row->id.'">Clear</a></td>
							</tr>';
						
						$i++;
						}
						
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


