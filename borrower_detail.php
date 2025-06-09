<?php
include 'header.php';
include_once 'model/borrowers.php';
include_once 'model/lenders.php';
include_once 'utils.php';
$borrowerid = intval($_GET['id']);
$borrowerObj = new borrowers();
$lenderObj = new lenders();
$borrower = $borrowerObj->fetch($borrowerid);
$total_loans = $borrowerObj->getTotalLoanDetails($borrowerid);
$loans = $borrowerObj->getAllLoans($borrowerid);
$color = $colors[array_rand($colors)];
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->

                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-<?= $color ?>">
                        <!-- /.widget-user-image -->
                        <h4 class="widget-user-username"><?= $borrower->name ?></h4>
                        <span class="float-right"> <a href="#" style="color: #FFF" data-toggle="modal"
                                                      data-target="#more-details">more details..</a></span>
                        <h6 class="widget-user-desc"><?= $borrower->address ?> </h6>
                    </div>
                    <div class="modal fade" id="more-details">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title"><?php echo strtoupper($borrower->name) ?></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body" style="padding: 3px">
                                    <table class="table-striped w-100" cellpadding="5">
                                        <tr>
                                            <th align="left">Name</th>
                                            <td><?php echo $borrower->name ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left">Address</th>
                                            <td><?php echo $borrower->address ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left">Primary Contact</th>
                                            <td><?php echo $borrower->primary_contact_no ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left">Secondary Contact</th>
                                            <td><?php echo $borrower->secondary_contact_no ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left">Referrer Name</th>
                                            <td><?php echo $borrower->referenced_by ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left">Referrer Contact</th>
                                            <td><?php echo $borrower->referenced_contactno ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Loan Amount <span
                                            class="float-right badge bg-<?= $color ?> text-md"><?= CurrencyFormat($total_loans->loan_borrow) ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Loan Paid <span
                                            class="float-right badge bg-<?= $color ?> text-md"><?= CurrencyFormat($total_loans->loan_paid) ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Loan Pending <span
                                            class="float-right badge bg-<?= $color ?> text-md"><?= CurrencyFormat($total_loans->loan_borrow - $total_loans->loan_paid) ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Interest Paid <span
                                            class="float-right badge bg-<?= $color ?> text-md"><?= CurrencyFormat($total_loans->interest_paid) ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Interest Pending <span
                                            class="float-right badge bg-<?= $color ?> text-md"><?= CurrencyFormat($total_loans->total_interest - $total_loans->interest_paid) ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <table class="w-100">
                            <tr>
                                <td width="50%">
                                    <button type="button" class="btn btn-success w-100" data-toggle="modal" id="new-loan"
                                            data-target="#modal-loan">
                                        <strong>LOAN</strong>
                                    </button>
                                </td>
                                <td >
                                    <button type="button" class="btn btn-danger w-100" data-toggle="modal"
                                            data-target="#modal-repay" id="repay-loan">
                                        <strong>REPAY</strong>
                                    </button>
                                </td>

                            </tr>
                        </table>
                    </div>
                    <div class="modal fade" id="modal-loan-preview">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">LOAN DETAILS</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                    <div class="modal-body" style="padding: 3px; overflow-y: auto">
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-loan-transfer">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">LOAN TRANSFER</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="loan-transfer-form" action="loan_transfer.php" method="post">
                                    <input type="hidden" name="transaction_type" value="R">
                                    <input type="hidden" name="id" value="0">
                                    <input type="hidden" name="borrowerid" value="0">
                                    <div class="modal-body" style="padding: 3px; overflow-y: auto">
                                        <table width="100%"><tr class="text-danger">
                                                <td width="50%" style="text-align: right">
                                                    <strong>Loan Balance</strong>
                                                </td>
                                                <th id="loan-transfer-loan-balance" style="text-align: center;font-size: x-large;">0.00
                                                </th>
                                            </tr>
                                        </table>
                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                    <div class="form-group">
                                                        <label for="exampleInputAmount">Lender</label>
                                                        <select name="lenderid" id="loan-transfer-lenderid" class="form-control" required>
                                                            <option value="">Choose</option>
                                                            <?php
                                                            $lenders = $lenderObj->fetchall();
                                                            foreach ($lenders as $lender) {
                                                                echo '<option value="' . $lender->id . '">' . $lender->name . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="loan-transfer-amount">Transfer Amount</label>
                                                        <input type="number" name="amount" class="form-control" required id="loan-transfer-amount">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-danger">
                                                <td width="50%" style="text-align: right">
                                                    <strong>Lender Balance</strong>
                                                </td>
                                                <th id="loan-transfer-lender-balance" style="text-align: center;font-size: x-large;">0.00
                                                </th>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputROIType">Type of Interest</label>
                                                        <select name="interest_type" class="form-control"
                                                                id="exampleInputROIType">
                                                            <option value="P">Percentage</option>
                                                            <option value="F">Fixed</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputROI">ROI</label>
                                                        <input type="number" step="any" name="interest_value"
                                                               class="form-control" required id="exampleInputROI">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="exampleInputComm">Commission</label>
                                                        <input type="number" step="any" name="commission"
                                                               class="form-control" required id="exampleInputComm">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table width="100%">
                                            <tr>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputTxnDate">Txn Date</label>
                                                        <input type="date" name="opening_date" class="form-control"
                                                               required id="trs_loan-txn-date"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputBankDate">Bank Date</label>
                                                        <input type="date" name="bank_date" class="form-control"
                                                               id="trs_loan-bank-date"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                                <td width="33%">
                                                    <div class="form-group">
                                                        <label for="exampleInputBankDate">Agreed Closing Date</label>
                                                        <input type="date" name="agreed_closing_date"
                                                               class="form-control" id="trs_loan-agreed-date"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" id="loan-transfer-submit" class="btn btn-success">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="modal-repay">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">LOAN REPAY</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form id="transaction_form" action="transaction_save.php" method="post">
                                    <input type="hidden" name="transaction_type" value="R">
                                    <input type="hidden" name="borrowerid" value="<?=$borrowerid?>">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="exampleInputAmount">Loan</label>
                                            <select name="loanid" class="form-control" required>
                                                <option value="">Choose</option>
                                                <?php
                                                foreach ($loans as $loan) {
                                                    if(($loan->amount - $loan->settled) > 0)
                                                        echo '<option value="' . $loan->id . '" data-amount="'.($loan->amount - $loan->settled).'">'.$loan->id.'-->'. $loan->lender.' - '.($loan->amount - $loan->settled) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="repay-amount">Amount</label>
                                            <input type="number" name="amount" class="form-control" required id="repay-amount">
                                        </div>

                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                    <div class="form-group">
                                                        <label for="exampleInputTxnDate">Txn Date</label>
                                                        <input type="date" name="transaction_date" class="form-control"
                                                               required id="exampleInputTxnDate"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="exampleInputBankDate">Bank Date</label>
                                                        <input type="date" name="bank_date" class="form-control"
                                                               id="exampleInputBankDate"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="form-group">
                                            <label for="repay-description">Narration</label>
                                            <input type="text" name="narration" class="form-control" required
                                                   id="repay-description">
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
                    <div class="modal fade" id="modal-loan">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">GIVE LOAN TO <?php echo strtoupper($borrower->name) ?></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="loan_form" action="loan_save.php" method="post">
                                    <input type="hidden" name="borrowerid" value="<?= $borrowerid ?>">
                                    <input type="hidden" name="id" value="">
                                    <div class="modal-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                    <div class="form-group">
                                                        <label for="exampleInputAmount">Lender</label>
                                                        <select name="lenderid" id="loan-lenderid" class="form-control" required>
                                                            <option value="">Choose</option>
                                                            <?php
                                                            $lenders = $lenderObj->fetchall();
                                                            foreach ($lenders as $lender) {
                                                                echo '<option value="' . $lender->id . '">' . $lender->name . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="loan-amount">Amount</label>
                                                        <input type="number" name="amount" class="form-control"
                                                               required id="loan-amount">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-danger">
                                                <td width="50%" style="text-align: right">
                                                    <strong>Current Balance</strong>
                                                </td>
                                                <th id="loan-lender-balance" style="text-align: center;font-size: x-large;">0.00
                                                </th>
                                            </tr>
                                        </table>


                                        <table>
                                            <tr>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputROIType">Type of Interest</label>
                                                        <select name="interest_type" class="form-control"
                                                                id="exampleInputROIType">
                                                            <option value="P">Percentage</option>
                                                            <option value="F">Fixed</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputROI">ROI</label>
                                                        <input type="number" step="any" name="interest_value"
                                                               class="form-control" required id="exampleInputROI">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="exampleInputComm">Commission</label>
                                                        <input type="number" step="any" name="commission"
                                                               class="form-control" required id="exampleInputComm">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table width="100%">
                                            <tr>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputTxnDate">Txn Date</label>
                                                        <input type="date" name="opening_date" class="form-control"
                                                               required id="loan-txn-date"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                                <td width="33.66%">
                                                    <div class="form-group">
                                                        <label for="exampleInputBankDate">Bank Date</label>
                                                        <input type="date" name="bank_date" class="form-control"
                                                               id="loan-bank-date"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                                <td width="33%">
                                                    <div class="form-group">
                                                        <label for="exampleInputBankDate">Agreed Closing Date</label>
                                                        <input type="date" name="agreed_closing_date"
                                                               class="form-control" id="loan-agreed-date"
                                                               value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="form-group">
                                            <label for="newloan-description">Narration</label>
                                            <input type="text" name="description" class="form-control" required
                                                   id="newloan-description">
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close
                                        </button>
                                        <button type="submit" id="loan-submit" class="btn btn-success">Save</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Loans</h3>
                            </div>
                            <!-- /.card-header -->
                                <table class="table-striped w-100" cellpadding="5">
                                <thead><tr><th width="5%">#</th><th width="10%">Opening Date</th><th width="10%">Closing Date</th><th>Lender</th><th width="10%">ROI</th><th width="10%">Loan Amount</th><th width="10%">Balance Amount</th><th width="10%">Pending Interest</th><th width="10%">Status</th></tr></thead>
                                    <tbody>
                                    <?php
                                    foreach ($loans as $loan){

                                        echo '<tr style="cursor:pointer" class="loan-row" data-loanid="'.$loan->id.'">
                                             <td>'.$loan->id.'</td>
                                            <td>'.date('d/m/y', strtotime($loan->opening_date)).'</td>
                                            <td>'.($loan->closing_date!= '' ? date('d/m/y', strtotime($loan->closing_date)) : '').'</td>
                                            <td>'.$loan->lender.'</td>
                                            <td>'.$loan->roi.'</td>
                                            <td align="right">'.CurrencyFormat($loan->amount).'</td>
                                            <td align="right">'.CurrencyFormat(($loan->amount - $loan->settled)).'</td>
                                            <td align="right">'.CurrencyFormat(($loan->total_interest - $loan->interest_paid)).'</td>
                                            <td align="center">'.($loan->closing_date!='' ? '<span class="badge badge-success">Closed</span>' : '<span class="badge badge-info">Active</span>').'</td>
                                            </tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            <!-- /.card-body -->
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

<script type="text/javascript">

    function clearLoanForm(){
        formEl = $("#loan_form");
        curDate = $.datepicker.formatDate('yy-m-d', new Date());

        formEl.find('select[name="lenderid"]').val('');
        formEl.find('input[name="amount"]').val('');
        formEl.find('select[name="interest_type"]').val('P');
        formEl.find('input[name="interest_value"]').val('');
        formEl.find('input[name="commission"]').val('');
        formEl.find('input[name="opening_date"]').val(curDate);
        formEl.find('input[name="bank_date"]').val(curDate);
        formEl.find('input[name="agreed_closing_date"]').val(curDate);
        formEl.find('input[name="description"]').val('');
        formEl.find('input[name="id"]').val('');
    }

    function clearLoanTransferForm(){
        formEl = $("#loan-transfer-form");
        curDate = $.datepicker.formatDate('yy-m-d', new Date());
        formEl.find('#loan-transfer-loan-balance').text('0.00');
        formEl.find('select[name="lenderid"]').val('');
        formEl.find('input[name="amount"]').val('');
        formEl.find('select[name="interest_type"]').val('P');
        formEl.find('input[name="interest_value"]').val('');
        formEl.find('input[name="commission"]').val('');
        formEl.find('input[name="opening_date"]').val(curDate);
        formEl.find('input[name="bank_date"]').val(curDate);
        formEl.find('input[name="agreed_closing_date"]').val(curDate);
        formEl.find('input[name="description"]').val('');
        formEl.find('input[name="id"]').val('0');
    }

    $(document).ready(function(){
        $(".loan-row").click(function(){
            loanid = $(this).data('loanid');
            $.ajax({
                url: 'loan_detail.php',
                type: 'post',
                data:  { "loanid": loanid},
                success: function( data, textStatus, jQxhr ){
                    $("#modal-loan-preview").find('.modal-body').html(data);
                    $("#modal-loan-preview").modal('show')
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            })
        })

        $('select[name="loanid"]').on("change", function(){
            amount = $(this).find(':selected').data('amount')
            $("#repay-amount").attr("max", amount)
        })

        $("body").on("click", ".loan-edit", function(){
            loanid = $(this).data('id');
            $("#modal-loan-preview").modal('hide')
            clearLoanForm();
            $.ajax({
                url: 'loan_edit.php',
                type: 'post',
                data: { "loanid": loanid },
                success: function( data, textStatus, jQxhr ){
                    //data  = JSON.parse(data);
                    $("#loan_form").find('input[name="id"]').val(data.id);
                    $("#loan_form").find('select[name="lenderid"]').val(data.lenderid);
                    $("#loan_form").find('input[name="amount"]').val(data.amount);
                    $("#loan_form").find('select[name="interest_type"]').val(data.interest_type);
                    $("#loan_form").find('input[name="interest_value"]').val(data.interest_value);
                    $("#loan_form").find('input[name="commission"]').val(data.commission);
                    $("#loan_form").find('input[name="opening_date"]').val(data.opening_date);
                    $("#loan_form").find('input[name="bank_date"]').val(data.bank_date);
                    $("#loan_form").find('input[name="agreed_closing_date"]').val(data.agreed_closing_date);
                    $("#loan_form").find('input[name="description"]').val(data.description);
                    $("#modal-loan").modal('show')
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            })
        })

        $("body").on("click", ".loan-transfer", function(){
            loanid = $(this).data('id');
            $("#modal-loan-preview").modal('hide')
            clearLoanTransferForm();
            $.ajax({
                url: 'loan_transfer_detail.php',
                type: 'post',
                data: { "loanid": loanid },
                success: function( data, textStatus, jQxhr ){
                    //data  = JSON.parse(data);
                    $("#loan-transfer-form").find('input[name="id"]').val(data.id);
                    $("#loan-transfer-form").find('input[name="borrowerid"]').val(data.borrowerid);
                    $("#loan-transfer-form").find('#loan-transfer-loan-balance').text(parseFloat(data.amount)-parseFloat(data.settled));
                    $("#modal-loan-transfer").modal('show')
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            })

        })


        $('#loan-lenderid').on('change', function(){
            balance = getLenderBalance($(this).val())
            $('#loan-lender-balance').text(balance)
            //$('#loan-amount').attr('max', parseFloat(balance))
        })

        $('#loan-transfer-lenderid').on('change', function(){
            balance = getLenderBalance($(this).val())
            $('#loan-transfer-lender-balance').text(balance)
            $('#loan-transfer-amount').attr('max', parseFloat(balance))
        })

        $("body").on("click", ".loan-remove", function(){

        });

        $("#loan-txn-date").on("blur", function(){
            $("#loan-bank-date").val($(this).val());
            var txn_date = $(this).val();
            var myDate = new Date($(this).val());
            myDate.setFullYear(myDate.getFullYear() + 1);

            var month = myDate.getMonth()+1;
            var day = myDate.getDate();

            var agreed_date = myDate.getFullYear() + '-' +
                (month<10 ? '0' : '') + month + '-' +
                (day<10 ? '0' : '') + day;

            $("#loan-agreed-date").val(agreed_date);
        })

        $("#new-loan").on('click', function(){
            clearLoanForm();
        })
		
		$("#trs_loan-txn-date").on("blur", function(){
            $("#trs_loan-bank-date").val($(this).val());
            var txn_date = $(this).val();
            var myDate = new Date($(this).val());
            myDate.setFullYear(myDate.getFullYear() + 1);

            var month = myDate.getMonth()+1;
            var day = myDate.getDate();

            var agreed_date = myDate.getFullYear() + '-' +
                (month<10 ? '0' : '') + month + '-' +
                (day<10 ? '0' : '') + day;

            $("#trs_loan-agreed-date").val(agreed_date);
        })

        $("#new-loan").on('click', function(){
            clearLoanForm();
        })
    })

    function getConfirmation(){
        return confirm("Are you sure...")
    }
    function getLenderBalance(lenderid){
        balance = 0;
        $.ajax({
            url: 'get_lender_balance.php',
            type: 'post',
            async : false,
            data: { "lenderid": lenderid },
            success: function( data, textStatus, jQxhr ){
                //data  = JSON.parse(data);
                balance = data.balance;

            },
            error: function( jqXhr, textStatus, errorThrown ){
                console.log( errorThrown );
            }
        })
        return balance;
    }
	
	$(document).ready(function(){
		$("#new-loan").on("click", function(){
			navigator.clipboard.readText()
			  .then(text => {
				// `text` contains the text read from the clipboard
				textArr = text.split("\t")
                let pattern1 = /^\d{2}-[a-zA-Z]{3}-\d{2}$/g
                let pattern2 = /^\d{2}-\d{2}-\d{4}$/g
                if(textArr[1].match(pattern1)){
                    dateVal = $.format.date(new Date(textArr[1]), "yyyy-MM-dd")
                }else if(textArr[1].match(pattern2)){
                    dateValArr = textArr[1].split("-")
                    dateVal = $.format.date(new Date(dateValArr[2]+"-"+dateValArr[1]+'-'+dateValArr[0]), "yyyy-MM-dd")
                }else{
                    dateVal = textArr[1]
                }
				$("#loan-amount").val(textArr[5].replace(",",""))
				$("#loan-txn-date").val(dateVal)
				$("#newloan-description").val($.trim(textArr[4]))
				$("#loan-txn-date").trigger("blur")
			  })
			  .catch(err => {
				// maybe user didn't grant access to read from clipboard
				console.log('Something went wrong', err);
			  });
		});
		
		$("#repay-loan").on("click", function(){
			navigator.clipboard.readText()
			  .then(text => {
				// `text` contains the text read from the clipboard
					textArr = text.split("\t")
                    let pattern1 = /^\d{2}-[a-zA-Z]{3}-\d{2}$/g
                    let pattern2 = /^\d{2}-\d{2}-\d{4}$/g
                    if(textArr[1].match(pattern1)){
                        dateVal = $.format.date(new Date(textArr[1]), "yyyy-MM-dd")
                    }else if(textArr[1].match(pattern2)){
                        dateValArr = textArr[1].split("-")
                        dateVal = $.format.date(new Date(dateValArr[2]+"-"+dateValArr[1]+'-'+dateValArr[0]), "yyyy-MM-dd")
                    }else{
                        dateVal = textArr[1]
                    }
				
					$("#repay-amount").val(textArr[6].replace(",",""))
					$("#exampleInputTxnDate").val(dateVal)
					$("#exampleInputBankDate").val(dateVal)
					$("#repay-description").val($.trim(textArr[4]))
			  })
			  .catch(err => {
				// maybe user didn't grant access to read from clipboard
				console.log('Something went wrong', err);
			  });
		});
	});
</script>