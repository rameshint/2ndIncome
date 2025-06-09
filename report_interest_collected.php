<?php
include_once 'header.php';
include_once 'model/loans.php';

$reportObj = new loans();

if($_GET['date']!= ''){
    $date = $_GET['date'];
}else{
    $date = date("Y-m-d");
}
$rst = $reportObj->fetchLastMonthInterest( $date);


?>
<section class="content">
    <div class="container-fluid">
        <div class="col-md-6 col-offset-3">
            <form>
                <table class="table table-striped w-50 ">
                    <tr>
                        <td>
                            <input type="date" name="date" class="form-control" value="<?php echo $date?>">
                        </td>
                        <td>
                            <input type="submit" value="Submit" class="btn btn-primary" >
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
                            <h3 class="card-title">INTEREST / COLLECTED</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">



                            <table class="table table-bordered table-striped" cellpadding="3" >
                                <thead>
                                    <tr>
                                        <th>Borrower</th>
                                        <th>Interest</th>
                                        <th>Collected</th>
                                        <th>Pending</th>
                                    </tr>
                                </thead>
                            <?php
                            $interest = 0;
                            $collected = 0;
                            $pending = 0;
                            foreach ($rst as $row) {
                                echo '<tr>
                                    <td>'.$row->borrower.'</td>
                                    <td align="right">'.$row->interest.'</td>
                                    <td align="right">'.$row->collected_amount.'</td>
                                    <td align="right">'.$row->pending.'</td>
                                </tr>';
                                $interest += $row->interest;
                                $collected += $row->collected_amount;
                                $pending += $row->pending;
                            }
                            ?>
                                <tr>
                                    <td align="right">Total</td>
                                    <th style="text-align: right"><?=$interest?></th>
                                    <th style="text-align: right"><?=$collected?></th>
                                    <th style="text-align: right"><?=$pending?></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</section>
<script type="text/javascript">
    $(document).ready(function () {

    })
</script>
<?php
include 'footer.php';
?>
