<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$result = $reportObj->longUnpaidLoans();
?>
<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">LONG UNSETTLED LOANS</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <table class="table table-striped table-bordered" cellpadding="5">
                        <thead><tr><th>Date</th><th>Lender</th><th>Borrower</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Agreed Date</th><th>DIFF</th></tr></thead>
                    <?php
                    foreach ($result as $r){
						foreach ($r as $row){
                        echo '<tr>
                                <td>'.$row->opening_date.'</td>
                                <td>'.$row->lender.'</td>
                                <td>'.$row->borrower.'</td>
                                <td>'.$row->amount.'</td>
                                <td>'.$row->paid.'</td>
                                <td>'.$row->balance.'</td>
                                <td>'.$row->agreed_closing_date.'</td>
								<td>'.$row->diff.'</td>
                              </tr>';
						}
                    }
                    ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>
