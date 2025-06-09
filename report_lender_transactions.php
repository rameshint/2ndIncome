<?php
include_once 'header.php';
include_once 'model/reports.php';
include_once 'model/lenders.php';
$reportObj = new reports();
$rst = $reportObj->lender_transactions($_GET['lenderid'], $_GET['fromDate'], $_GET['toDate'], $_GET['txn_category']);

$lenderObj = new lenders();
$lenders = $lenderObj->fetchall();
?>
<section class="content">
    <div class="container-fluid">
        <div class="col-md-6 col-offset-3">
            <form>
                <table class="table table-striped w-50 ">
                    <tr>
                        <td>
                            <select name="lenderid" class="form-control" style="width: auto">
                                <option value="0">Select lender</option>
                                <?php
                                foreach($lenders as $lender){
                                    $selected = '';
                                    if($_GET['lenderid'] == $lender->id) $selected = ' selected ';
                                    echo '<option value="'.$lender->id.'" '.$selected.'>'.$lender->name.'</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="date" name="fromDate" class="form-control" value="<?php echo $_GET['fromDate'] ?>">
                        </td>
                        <td>
                            <input type="date" name="toDate" class="form-control" value="<?php echo $_GET['toDate'] ?>">
                        </td>
                        <td>
                            <select class="form-control" name="txn_category"  style="width: auto">
                                <option value="">Select Txn Category</option>
                                <option >Loan</option>
                                <option >Interest</option>
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Submit" class="btn btn-primary" >
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
        if($_GET['lenderid'] > 0) {
            ?>
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
                            <table class="table table-bordered table-striped" cellpadding="3" >
                                <thead>
                                    <tr>
                                        <th>Head</th>
                                        <th>Sub-Head</th>
                                        <th>Txn. Date</th>
                                        <th>Bank Date</th>
                                        <th>Amount</th>
                                        <th>Txn. Type</th>
                                        <th>Txn. Category</th>
                                        <th>Behalf Of</th>
                                        <th>Waiver</th>
                                        <th>Flag</th>
                                        <th>Narration</th>
                                    </tr>
                                </thead>
                            <?php
                            foreach ($rst as $row) {
                                echo '<tr>
                                    <td>'.$row->head.'</td>
                                    <td>'.$row->name.'</td>
                                    <td>'.$row->transaction_date.'</td>
                                    <td>'.$row->bank_date.'</td>
                                    <td>'.$row->amount.'</td>
                                    <td>'.$row->transaction_type.'</td>
                                    <td>'.$row->transaction_category.'</td>
                                    <td>'.$row->behalf_of.'</td>
                                    <td>'.$row->Waiver.'</td>
                                    <td>'.$row->flag.'</td>
                                    <td>'.$row->narration.'</td>
                                </tr>';
                            }
                            ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</section>
<?php
include 'footer.php';
?>
