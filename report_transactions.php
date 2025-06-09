<?php
include_once 'header.php';
include_once 'model/reports.php';
include_once 'utils.php';
$reportObj = new reports();
$rst = $reportObj->customer_transactions($_GET['date']);

?>
<section class="content">
    <div class="container-fluid">
        <div class="col-md-6 col-offset-3">
            <form>
                <table class="table table-striped w-50 ">
                    <tr>
                        <td>
                            <select class="form-control" name="date">
                                <?php
                                foreach(range(0,-12) as $item){
                                    $month = date('Y-M', strtotime(date('Y-m-d'). "$item month"));
                                    $monthvalue = date('Y-m-01', strtotime(date('Y-m-d'). "$item month"));
                                    $selected = '';
                                    if($_GET['date']!='' && $_GET['date'] == $monthvalue){
                                        $selected = ' selected ';
                                    }
                                    echo "<option value='$monthvalue' $selected>$month</option>";
                                }
                                ?>
                            </select>

                        </td>
                        <td>
                            <button class="form-control btn btn-primary"  >
                                Submit
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="row">


            <div class="col-md-12">

                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">TRANSACTIONS</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
                         
                        <table class="table-striped" cellpadding=5 width=100%>
                            <thead>
                            <tr>
                            <th>Created on</th><th>Txn Date</th><th width="20%">Borrower</th><th>Txn Type</th><th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($rst as $row){
                                echo '<tr>
                                <td>'.$row->created.'</td>
								<td>'.$row->transaction_date.'</td>
								<td>'.$row->name.'</td>
								<td>'.$row->narration.'</td>
                                <td>'.$row->transaction_type.'</td>
								<td align=right>'.CurrencyFormat($row->amt).'</td>
							</tr>';
                            
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
