<?php
include_once 'model/borrowers.php';
require_once 'config.ini.php';
$borrowerObj = new borrowers();
$borrowerid = intval($_REQUEST['borrowerid']);
$date = $_REQUEST['date'];
$loans = $borrowerObj->fetchPendingInterest($borrowerid, $date);

?>
<input type="hidden" name="date" value="<?=$date?>" />
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
    <label for="narration">Narration</label>
    <input type="text" name="narration" class="form-control"  id="narration">
</div>
<div class="form-group">

</div>
<div class="row">
    <div class="col-3">
<div class="form-check">
    <input type="checkbox" name="behalf_of" class="form-check-input"  id="behalf" value="1">
    <label class="form-check-label" for="behalf"> Behalf of  </label>
</div>
    </div>
    <div class="col-3">
<div class="form-check">
    <input type="checkbox" name="waiver" class="form-check-input"  id="waiver" value="1">
    <label class="form-check-label" for="waiver"> Waiver  </label>

</div>
    </div>
</div>
<table class=" table-striped" cellpadding="5">
    <thead>
    <tr>
        <th>Lender</th>
        <th width="10%">Loan</th>
        <th width="10%">Pending</th>
        <th width="10%">Interest</th>
        <th width="25%"></th>
		<th width="5%"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($loans as $loan) {
        echo '<tr>
                <td>' . $loan->lender . '</td>
                <td style="text-align: right">' . number_format($loan->amount) . '</td>
                <td style="text-align: right">' . number_format($loan->amount - $loan->settled) . '</td>
                <td style="text-align: right">' . number_format($loan->interest) . '</td>
                <td><input style="text-align: center" type="number" step="any" name="loanid[' . $loan->id . ']"  class="form-control loan-input" > </td>
				<td><i class="fa fa-copy interest-copy" style="cursor:pointer"></i></td>
            </tr>';
        $amount += $loan->amount;
        $settled += $loan->settled;
        $interest += $loan->interest;

    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th style="text-align:right">Total Interest</th>
        <th style="text-align:right" class="text-md"><?=number_format($amount)?></th>
        <th style="text-align:right" class="text-md"><?=number_format($amount - $settled)?></th>
        <th style="text-align:right" class="text-md"><?=number_format($interest)?></th>
        <th id="total-interest" style="text-align:center" class="text-md"></th>
		<th><i class="fa fa-copy interest-copy-total" style="cursor:pointer"></i></th>
    </tr>
    </tfoot>
</table>
