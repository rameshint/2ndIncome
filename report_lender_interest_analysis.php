<?php
include_once 'header.php';
include_once 'model/loans.php';

$reportObj = new loans();

if($_GET['date']!= ''){
    $date = $_GET['date'];
}else{
    $date = date("Y-m-d");
}
$rst = $reportObj->fetchLenderInterestAnalysis( $date);


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
                            <h3 class="card-title">LENDER INTEREST / COLLECTED</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">



                            <table class="table table-bordered table-striped" cellpadding="3" >
                                <thead>
                                    <tr>
                                        <th>Lender</th>
										<th>Investment</th>
										<th>2.4 Interest</th>
                                        <th>Interest</th>
                                        <th>Collected</th>
                                        <th>Pending</th>
										<th>Interest Cal.<br />Deviation</th>
										<th>Interest Col.<br />Deviation</th>
                                    </tr>
                                </thead>
                            <?php
                            $interest = 0;
                            $collected = 0;
                            $pending = 0;
                            foreach ($rst as $row) {
								$calc_dev = round(100-($row->interest/($row->act_int/100)),2);
								$colc_dev = round(100-($row->collected_amount/($row->interest/100)),2);
								
                                echo '<tr>
                                    <td>'.$row->lender.'</td>
									<td align="right">'.$row->amount.'</td>
									<td align="right">'.$row->act_int.'</td>
                                    <td align="right">'.$row->interest.'</td>
                                    <td align="right">'.$row->collected_amount.'</td>
                                    <td align="right">'.$row->pending.'</td>
									<td align="right">'.$calc_dev.'</td>
									<td align="right">'.$colc_dev.'</td>
                                </tr>';
                            }
                            ?>
                                
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
