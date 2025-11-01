<?php
include_once 'header.php';
include_once 'model/transactions.php';
if ($_GET['date']) {
    $transactionObj = new transactions();
    $loans = $transactionObj->fetchLoans($_GET['date']);
    $transactions = $transactionObj->fetchUnSettledTransactions($_GET['date']);
}
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-offset-3">
                <form>
                    <table class="table table-striped w-50 ">
                        <tr>
                            <td><input type="date" id="interest_cal_date" name="date" class="form-control"
                                       value="<?php echo($_GET['date'] != '' ? $_GET['date'] : date("Y-m-d")) ?>"></td>
                            <td>
                                <button class="form-control btn btn-primary" onclick="fetchPendingInterest()">
                                    Submit
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <?php
        if ($_GET['date']) {
            ?>
            <div class="row">
                <form action="settlement_save.php" method="post">
                    <div class="col-md-12">

                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">UNSETTLED TRANSACTIONS</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <table class="table-striped w-100" cellpadding="5">
                                    <tr>
                                        <td>LoanId</td>
                                        <th>Borrower</th>
                                        <th style="text-align:right">Loan</th>
                                        <th style="text-align:right">Paid</th>
                                        <th style="text-align:right">Interest</th>
                                        <th>#</th>
                                    </tr>
                                    <?php
									foreach ($transactions as $transaction) {
                                        echo '<tr>
                                                <td>' . $transaction->loanid . '</td>
                                                <td>' . $transaction->borrower . '</td>
                                                <td align="right">' . number_format($transaction->loan) . '</td>
                                                <td align="right">' . number_format($transaction->settled) . '</td>
                                                <td align="right">' . number_format($transaction->amount) . '</td>
                                                <td><input type="checkbox" name="txns[' . $transaction->id . ']" data-loanid="' . $transaction->loanid . '" value="' . $transaction->amount . '" class="txns" checked> </td>
                                            </tr>';
										
                                    }
                                    ?>
									<tr>
									<th colspan=4>Net Total</th><td  align="right" id="txn_total"></td>
									</tr>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-12">

                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">SETTLEMENT</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <table class="table-striped w-100 text-sm" cellpadding="5">
                                    <tr>
                                        <th colspan="2"></th>
                                        <th>Amount</th>
                                        <th>Tot. Int.</th>
                                        <th>Lender Int.</th>
                                        <th>Comm.</th>
                                        <th>Recovery</th>
                                        <th>Lender Int</th>
                                        <th>Commission</th>
                                        <th>Recovery</th>
                                        <th>Total</th>
                                        <th>Excess</th>
                                        </td></tr>
                                    <?php
                                    $lender = '';
                                    foreach ($loans as $loan) {
                                        if ($lender != $loan->lender) {
                                            if ($lender != '') {
                                                echo '<tr><td colspan="2" align="right">Net Total</td>';
                                                echo '<th style="text-align: right ">' . $amount . '</th>';
                                                echo '<th style="text-align: right ">' . $total_interest . '</th>';
                                                echo '<th style="text-align: right ">' . $lender_interest . '</th>';
                                                echo '<th style="text-align: right ">' . $commission . '</th>';
                                                echo '<th style="text-align: right ">' . $recovery . '</th>';
                                                echo '<th style="text-align: right " id="nt_lender_interest_' . $lenderid . '">0</th>';
                                                echo '<th style="text-align: right " id="nt_commission_' . $lenderid . '">0</th>';
                                                echo '<th style="text-align: right " id="nt_recovery_' . $lenderid . '">0</th>';
                                                echo '<th style="text-align: right " id="nt_total_' . $lenderid . '">0</th>';
                                                echo '<th style="text-align: right " id="nt_excess_' . $lenderid . '">0</th>';
                                                echo '</tr>';

                                                $amount = 0;
                                                $total_interest = 0;
                                                $lender_interest = 0;
                                                $commission = 0;
                                                $recovery = 0;
                                            }
                                            echo '<tr>
                                                  <th colspan="12" class="lender_rows" data-lenderid="' . $loan->lenderid . '">' . $loan->lender . '</th> 
                                            </tr>';
                                            $lenderid = $loan->lenderid;
                                        }
                                        echo '<tr class=""><td width="5%">#' . $loan->loanid . '</td>
                                                  <td>' . $loan->borrower . '</td>
                                                  <td width="5%" style="text-align: right ">' . $loan->amount . '</td>
                                                  <td width="5%" id="total_interest_' . $loan->loanid . '" style="text-align: right ">' . $loan->total_interest . '</td>
                                                  <td width="5%" id="lender_interest_' . $loan->loanid . '" style="text-align: right ">' . $loan->lender_interest . '</td>
                                                  <td width="5%" id="commission_' . $loan->loanid . '" style="text-align: right ">' . ($loan->total_interest - $loan->lender_interest) . '</td>
                                                  <td width="5%" id="recovery_' . $loan->loanid . '" style="text-align: right ">' . $loan->recovery_amount . '</td>
                                                  <td width="10%"><input type="text" style="text-align: right" class="form-control lender_interest_' . $loan->lenderid . '" name="lender_interest[' . $loan->loanid . ']" value="0"></td>
                                                  <td width="10%"><input type="text" style="text-align: right" class="form-control lender_commission_' . $loan->lenderid . '" name="commission[' . $loan->loanid . ']" value="0"></td>
                                                  <td width="10%"><input type="text" style="text-align: right" class="form-control lender_recovery_' . $loan->lenderid . '" name="recovery[' . $loan->loanid . ']" value="0"></td>
                                                  <td width="10%"><input type="text" style="text-align: right" class="form-control lender_total_' . $loan->lenderid . '" name="total[' . $loan->loanid . ']" value="0"></td>
                                                  <td width="10%"><input type="text" style="text-align: right" class="form-control lender_excess_' . $loan->lenderid . '" name="excess[' . $loan->loanid . ']" value="0"></td>
                                                  <input type="hidden" name="lenders[' . $loan->loanid . ']" value="' . $loan->lenderid . '" />
                                              </tr>';
                                        $lender = $loan->lender;
                                        $amount += $loan->amount;
                                        $total_interest += $loan->total_interest;
                                        $lender_interest += $loan->lender_interest;
                                        $commission += ($loan->total_interest - $loan->lender_interest);
                                        $recovery += $loan->recovery_amount;
                                    }
                                    echo '<tr><td colspan="2" align="right">Net Total</td>';
                                    echo '<th style="text-align: right ">' . $amount . '</th>';
                                    echo '<th style="text-align: right ">' . $total_interest . '</th>';
                                    echo '<th style="text-align: right ">' . $lender_interest . '</th>';
                                    echo '<th style="text-align: right ">' . $commission . '</th>';
                                    echo '<th style="text-align: right ">' . $recovery . '</th>';
                                    echo '<th style="text-align: right " id="nt_lender_interest_' . $lenderid . '">0</th>';
                                    echo '<th style="text-align: right " id="nt_commission_' . $lenderid . '">0</th>';
                                    echo '<th style="text-align: right " id="nt_recovery_' . $lenderid . '">0</th>';
                                    echo '<th style="text-align: right " id="nt_total_' . $lenderid . '">0</th>';
                                    echo '<th style="text-align: right " id="nt_excess_' . $lenderid . '">0</th>';
                                    echo '</tr>';
                                    echo '<tr><td colspan="2" align="right">Grant Total</td>';
                                    echo '<th style="text-align: right "></th>';
                                    echo '<th style="text-align: right "></th>';
                                    echo '<th style="text-align: right "></th>';
                                    echo '<th style="text-align: right "></th>';
                                    echo '<th style="text-align: right "></th>';
                                    echo '<th style="text-align: right " id="grant_interest">0</th>';
                                    echo '<th style="text-align: right " id="grant_commission">0</th>';
                                    echo '<th style="text-align: right " id="grant_recovery">0</th>';
                                    echo '<th style="text-align: right " id="grant_total">0</th>';
                                    echo '<th style="text-align: right " id="grant_excess">0</th>';
                                    echo '</tr>';
                                    ?>
                                </table>

                            </div>
                            <div class="card-footer " style="text-align: center">
                                <table align=center><tr><td>                                Settlement Date:</td><td> <input type="date" style="width: 200px; " class="form-control" name="settlement_date" value="<?=date("Y-m-d")?>" /></td>
<td>                                <button type="submit" class="btn btn-primary">SUBMIT</button></td></tr></table>
                            </div>

                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </form>
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
    function calculateSettlement() {
		txn_total = 0
        $('input[name^="txns"]').each(function () {
            if ($(this).is(':checked')) {
                excess = 0
                loanid = $(this).data('loanid');

                console.log("-------------------------------------------------------------------------------------------------")
                console.log("loan = " + loanid)

                pre_txn_amount = txn_amount = parseFloat($(this).val())
                console.log("txn amount = " + txn_amount)

                lender_interest = parseFloat($("#lender_interest_" + loanid).text())
                total_interest = parseFloat($("#total_interest_" + loanid).text())
                commission = total_interest - lender_interest;
                recovery = parseFloat($("#recovery_" + loanid).text());

                pre_lender_interest = parseFloat($('input[name="lender_interest[' + loanid + ']"]').val())
                pre_commission = parseFloat($('input[name="commission[' + loanid + ']"]').val())
                pre_recovery = parseFloat($('input[name="recovery[' + loanid + ']"]').val())
                pre_loan_total = parseFloat($('input[name="total[' + loanid + ']"]').val())
                pre_excess = parseFloat($('input[name="excess[' + loanid + ']"]').val())

                if (pre_lender_interest < lender_interest) {
                    balance = lender_interest - pre_lender_interest
                    if ((pre_lender_interest + txn_amount) > lender_interest) {
                        pre_lender_interest = lender_interest;
                    } else {
                        pre_lender_interest += txn_amount;
                    }
                    console.log("lender interest = " + lender_interest)
                    txn_amount = txn_amount - balance;
                }
                console.log("txn amount = " + txn_amount)
                console.log("lender amount = " + pre_lender_interest)


                if (txn_amount > 0) {
                    balance = recovery - pre_recovery
                    if (pre_recovery < recovery) {
                        if ((pre_recovery + txn_amount) > recovery) {
                            pre_recovery = recovery;
                        } else {
                            pre_recovery += txn_amount;
                        }
                        txn_amount = txn_amount - balance;
                    }
                    console.log("txn amount = " + txn_amount)
                    console.log("recovery amount = " + pre_recovery)
                }

                if (txn_amount > 0) {
                    balance = commission - pre_commission
                    if (pre_commission < commission) {
                        if ((pre_commission + txn_amount) > commission) {
                            pre_commission = commission;
                        } else {
                            pre_commission += txn_amount;
                        }
                        txn_amount = txn_amount - balance;
                    }
                    console.log("txn amount = " + txn_amount)
                    console.log("commission amount = " + pre_commission)
                }




                net_interest = pre_lender_interest + pre_commission + pre_recovery;
                excess = pre_excess
                if (txn_amount > 0)
                    excess = pre_excess + txn_amount;
                console.log("excess amount = " + excess)

                if (parseFloat($(this).val()) == excess){
                    // When seperate transaction comes and consider both settlement and excess 
                    excess = 0
                }

                $('input[name="lender_interest[' + loanid + ']"]').val(pre_lender_interest)
                $('input[name="commission[' + loanid + ']"]').val(pre_commission)
                $('input[name="recovery[' + loanid + ']"]').val(pre_recovery)
                $('input[name="total[' + loanid + ']"]').val(net_interest)
                $('input[name="excess[' + loanid + ']"]').val(excess)
				
				
				if (pre_txn_amount == txn_amount ) {
					$(this).prop('checked', false);
				}else{
					txn_total += pre_txn_amount;
				}
            }
			$("#txn_total").text(txn_total)
        })
        grant_total = 0;
        grant_excess = 0;
        grant_commission = 0;
        grant_interest = 0;
        grant_recovery = 0;
        $('.lender_rows').each(function () {
            lenderid = $(this).data('lenderid')

            nt_lender_interest = 0;
            $('.lender_interest_' + lenderid).each(function () {
                nt_lender_interest += parseFloat($(this).val());
                console.log(nt_lender_interest)
            })
            $('#nt_lender_interest_' + lenderid).text(nt_lender_interest)

            nt_lender_commission = 0;
            $('.lender_commission_' + lenderid).each(function () {
                nt_lender_commission += parseFloat($(this).val());
            })
            $('#nt_commission_' + lenderid).text(nt_lender_commission)


            nt_lender_recovery = 0;
            $('.lender_recovery_' + lenderid).each(function () {
                nt_lender_recovery += parseFloat($(this).val());
            })
            $('#nt_recovery_' + lenderid).text(nt_lender_recovery)

            nt_lender_total = 0;
            $('.lender_total_' + lenderid).each(function () {
                nt_lender_total += parseFloat($(this).val());
            })
            $('#nt_total_' + lenderid).text(nt_lender_total)


            nt_lender_excess = 0;
            $('.lender_excess_' + lenderid).each(function () {
                nt_lender_excess += parseFloat($(this).val());
            })
            $('#nt_excess_' + lenderid).text(nt_lender_excess)

            grant_total += nt_lender_total ;
            grant_excess += nt_lender_excess ;
            grant_interest += nt_lender_interest ;
            grant_commission += nt_lender_commission ;
            grant_recovery += nt_lender_recovery ;
        })

        $("#grant_total").text(grant_total)
        $("#grant_excess").text(grant_excess)
        $("#grant_interest").text(grant_interest)
        $("#grant_commission").text(grant_commission)
        $("#grant_recovery").text(grant_recovery)

    }

    function clearAllInputs() {
        $.each(['lender_interest', 'commission', 'recovery', 'total', 'excess'], function (index, value) {
            $('input[name^="' + value + '"]').each(function () {
                $(this).val(0);
            })
        })
    }

    $(document).ready(function () {
        clearAllInputs();
        calculateSettlement();

        $('input[name^="txns"]').on("change", function () {
            clearAllInputs();
            calculateSettlement();
        })

    })

</script>
