<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$rst = $reportObj->settlement($_GET['date']);

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
                            <button class="form-control btn btn-primary" onclick="fetchPendingInterest()">
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
                        <h3 class="card-title">SETTLEMENT</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body">
                        <?php
                        $i=1;
                        foreach($rst as $row){
                        if($row->lender == ''){
                            echo '<table width=80% style="font-size:20px">
								<tr><th>Lender Interest</th><td> '.$row->lender_interest.'</td>
									<th>Excess</th><td> '.$row->excess.' </td>
									</tr>
									<tr><th>Commission</th><td> '.$row->commission.'</td>
									<th>Recovery</th><td> '.$row->recovery.' </td>
									</tr>
									</table>';
                        }else{
                        if($row->borrower == ''){
                        if($i > 3){
                            echo $header.'</tbody>						</table>';
                        }
                        echo '<br /><h3>'.$row->lender.'&nbsp;&nbsp;&nbsp;</h3>';
                        $header = '<tr><th colspan=3 align=right>Net Amount</th>
                                <td>'.$row->lender_interest.'</td>
                                <td> '.$row->commission.' </td>
                                <td> '.$row->recovery.' </td>
                                <td> '.$row->excess.' </td>
                                </tr>';
                        ?>


                        <table class="table-striped" cellpadding=5 width=80%>
                            <thead>
                            <tr>
                                <th width="20%">Lender</th><th width="20%">Borrower</th><th>Settlement Date</th><th>Lender Interest</th><th>Commission</th><th>Recovery</th><th>Excess</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            }else{
                                echo '<tr>
							
								<td>'.$row->lender.'</td>
								<td>'.$row->borrower.'</td>
								<td>'.$row->settlement_date.'</td>
								<td>'.$row->lender_interest.'</td>
								<td>'.$row->commission.'</td>
								<td>'.$row->recovery.'</td>
								<td>'.$row->excess.'</td>
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
