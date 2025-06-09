<?php
include_once 'header.php';
include_once 'model/transactions.php';
include_once 'model/borrowers.php';
if ($_GET['borrower']) {
    $transactionObj = new transactions();
    $transactions = $transactionObj->fetchBorrowerTransactions($_GET);
}
$borrowerObj = new borrowers();
$borrowers = $borrowerObj->fetchall();
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form>
                    <table class="table table-striped w-100 ">
                        <tr>
                            <td style="width:25%"><select name="borrower" class="form-control" required>
                                    <option value="0">Choose borrowers</option>
                                    <?php
                                    foreach ($borrowers as $borrower) {
                                        $selected = '';
                                        if ($borrower->id == $_GET['borrower']) {
                                            $selected = 'selected';
                                            $borrowerName = $borrower->name;
                                        }
                                        echo '<option value="' . $borrower->id . '" ' . $selected . '>' . $borrower->name . '</option>';
                                    }
                                    ?>
                                </select></td>
                            <td style="width:25%"><input type="date" id="interest_cal_date" name="from_date" class="form-control"
                                       value="<?php echo($_GET['from_date'] != '' ? $_GET['from_date'] : date("Y-m-d", strtotime(date("Y-m-d") . " -1 month"))) ?>">
                            </td>
                            <td style="width:25%"><input type="date" id="interest_cal_date" name="to_date" class="form-control"
                                       value="<?php echo($_GET['to_date'] != '' ? $_GET['to_date'] : date("Y-m-d")) ?>">
                            </td>
                            <td>
                                <button class="form-control btn btn-primary">
                                    Submit
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <?php
        if ($_GET['borrower']) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo strtoupper($borrowerName) ?>'s TRANSACTIONS</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <div class="card-body">
                            <table class="table-striped w-100" cellpadding="5">
                                <thead>
                                <tr>
                                    <th width="1%">#</th>
                                    <th width="10%">Lender</th>
                                    <th width="10%">Txn. Date</th>
                                    <th width="10%">Bank Date</th>
                                    <th>Narration</th>
                                    <th width="1%"></th>
                                    <th width="10%">Cr.</th>
                                    <th width="10%">Dr.</th>
                                    <th width="5%">Action</th>
                                </tr>
                                </thead>
                                <?php
                                foreach ($transactions as $transaction) {
                                    $tr = '';
                                    if($transaction->flag == 0){
                                        $tr = 'class="txn_row" data-txnid="'.$transaction->id.'"';
                                    }

                                    echo '<tr '.$tr.'>
                                            <td>' . $transaction->id . '</td>
                                            <td>' . $transaction->lender . '</td>
                                            <td>' . $transaction->transaction_date . '</td>
                                            <td>' . $transaction->bank_date . '</td>
                                            <td>' . $transaction->narration . '</td>
                                            <td>' . $transaction->transaction_type . '</td>';
                                    if ($transaction->transaction_type == 'B')
                                        echo '<td></td><td>' . $transaction->amount . '</td>';
                                    else
                                        echo '<td>' . $transaction->amount . '</td><td></td>';
                                    echo '<td>';
                                    if($transaction->flag == 0 and $transaction->transaction_type != 'B') {
                                        echo '<span style="cursor: pointer" onclick="editTransaction(' . $transaction->id . ')"><i class="fas fa-edit"></i></span>
                                             &nbsp;&nbsp;<span onclick="deleteTransaction(' . $transaction->id . ')"  style="cursor: pointer"><i class="fas fa-trash"></i></span>';
                                    }else{
                                        echo 'Settled';
                                    }

                                    echo '</td></tr>';
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal-edit">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">TRANSACTION EDIT</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form id="transaction_form" action="transaction_edit.php" method="post">
                            <input type="hidden" name="borrower" value="<?=$_REQUEST['borrower']?>">
                            <input type="hidden" name="transaction_type" value="">
							<input type="hidden" name="from_date" value="<?=$_REQUEST['from_date']?>">
                            <input type="hidden" name="to_date" value="<?=$_REQUEST['to_date']?>">
							<input type="hidden" name="loanid" value="">
                            <input type="hidden" name="id" value="">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="repay-amount">Amount</label>
                                    <input type="number" name="amount" class="form-control" required id="repay-amount">
                                </div>

                                <table width="100%">
                                    <tr>
                                        <td width="50%">
                                            <div class="form-group">
                                                <label for="exampleInputTxnDate">Txn Date</label>
                                                <input type="date" name="transaction_date" class="form-control"  required id="exampleInputTxnDate"  value="">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label for="exampleInputBankDate">Bank Date</label>
                                                <input type="date" name="bank_date" class="form-control"  id="exampleInputBankDate"  value="">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="form-group">
                                    <label for="exampleInputDesc">Narration</label>
                                    <input type="text" name="narration" class="form-control" required
                                           id="exampleInputDesc">
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" id="loan-submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</section>
<?php
include_once 'footer.php';
?>
<script type="text/javascript">
function editTransaction(txnid){
    $.ajax({
        url: 'transaction_single.php',
        type: 'post',
        data: { "txnid": txnid },
        success: function( data, textStatus, jQxhr ){
            //data  = JSON.parse(data);
            $("#transaction_form").find('input[name="id"]').val(data.id);
            $("#transaction_form").find('input[name="amount"]').val(data.amount);
            $("#transaction_form").find('input[name="transaction_date"]').val(data.transaction_date);
            $("#transaction_form").find('input[name="bank_date"]').val(data.bank_date);
            $("#transaction_form").find('input[name="narration"]').val(data.narration);
			$("#transaction_form").find('input[name="loanid"]').val(data.loanid);
			$("#transaction_form").find('input[name="transaction_type"]').val(data.transaction_type);
            $("#modal-edit").modal('show')
        },
        error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    })
}
function deleteTransaction(txnid){
    if(confirm("Are u sure to remove this transactions?")) {
        $.ajax({
            url: 'transaction_delete.php',
            type: 'post',
            data: {"txnid": txnid},
            success: function (data, textStatus, jQxhr) {
                //data  = JSON.parse(data);
                location.reload();
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })
    }
}
</script>
