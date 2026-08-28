<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$result = $reportObj->badLoans();

// Group by lender and compute summary stats
$lenders_data   = [];
$total_amount   = 0;
$total_settled  = 0;
$total_interest = 0;

foreach ($result as $loan) {
    $lenders_data[$loan->lender][] = $loan;
    $total_amount   += $loan->amount;
    $total_settled  += $loan->settled;
    $total_interest += $loan->interest_settled;
}

$total_balance  = $total_amount - $total_settled;
$recovery_pct   = $total_amount > 0 ? round($total_settled / $total_amount * 100, 1) : 0;
?>
<section class="content">
    <div class="container-fluid">

        <!-- Summary Info Boxes -->
        <div class="row mb-2">
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger"><i class="fas fa-skull-crossbones"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bad Loans</span>
                        <span class="info-box-number"><?= count($result) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-dark"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Exposure</span>
                        <span class="info-box-number"><?= number_format($total_amount, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-hand-holding-usd"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recovered</span>
                        <span class="info-box-number"><?= number_format($total_settled, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger"><i class="fas fa-fire"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Outstanding Loss</span>
                        <span class="info-box-number text-danger"><?= number_format($total_balance, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall recovery progress -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm border-left border-danger">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="font-weight-bold text-danger">
                                <i class="fas fa-exclamation-circle mr-1"></i>Overall Recovery Progress
                            </small>
                            <small>
                                Recovered: <strong class="text-success"><?= number_format($total_settled, 2) ?></strong>
                                &nbsp;|&nbsp; Outstanding: <strong class="text-danger"><?= number_format($total_balance, 2) ?></strong>
                                &nbsp;|&nbsp; Interest Collected: <strong class="text-info"><?= number_format($total_interest, 2) ?></strong>
                                &nbsp;|&nbsp; Lenders affected: <strong><?= count($lenders_data) ?></strong>
                            </small>
                        </div>
                        <div class="progress" style="height:16px; border-radius:4px;">
                            <div class="progress-bar bg-success" style="width:<?= $recovery_pct ?>%" title="Recovered <?= $recovery_pct ?>%">
                                <?= $recovery_pct ?>%
                            </div>
                            <div class="progress-bar bg-danger" style="width:<?= 100 - $recovery_pct ?>%" title="Outstanding <?= 100 - $recovery_pct ?>%">
                                <?= 100 - $recovery_pct ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lender Accordion -->
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-danger"><i class="fas fa-skull-crossbones mr-2"></i>BAD LOANS — Lender Wise</h5>
                <div>
                    <button class="btn btn-xs btn-outline-secondary mr-1" onclick="$('.collapse').collapse('show')">Expand All</button>
                    <button class="btn btn-xs btn-outline-secondary" onclick="$('.collapse').collapse('hide')">Collapse All</button>
                </div>
            </div>
        </div>

        <div class="accordion" id="badLoanAccordion">
            <?php $idx = 0; foreach ($lenders_data as $lender_name => $loans):
                $l_amount  = array_sum(array_column((array) array_map(fn($l) => (array)$l, $loans), 'amount'));
                $l_settled = array_sum(array_column((array) array_map(fn($l) => (array)$l, $loans), 'settled'));
                $l_bal     = $l_amount - $l_settled;
                $l_pct     = $l_amount > 0 ? round($l_settled / $l_amount * 100) : 0;
                $panel_id  = 'bad-panel-' . $idx++;
            ?>
            <div class="card shadow-sm mb-1" style="border-left:4px solid #dc3545;">
                <div class="card-header py-2 px-3" id="hdr-<?= $panel_id ?>"
                     data-toggle="collapse" data-target="#<?= $panel_id ?>"
                     aria-expanded="false" style="cursor:pointer;">
                    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

                        <i class="fas fa-chevron-right accordion-icon mr-1" style="font-size:10px;transition:transform .2s;"></i>
                        <span class="font-weight-bold mr-2">
                            <i class="fas fa-user-tie mr-1 text-danger"></i><?= htmlspecialchars($lender_name) ?>
                        </span>

                        <span class="badge badge-secondary"><?= count($loans) ?> loan<?= count($loans) > 1 ? 's' : '' ?></span>
                        <span class="badge badge-dark" title="Total Exposure">Amt: <?= number_format($l_amount, 2) ?></span>
                        <span class="badge badge-success" title="Recovered">Rec: <?= number_format($l_settled, 2) ?></span>
                        <span class="badge badge-<?= $l_bal > 0 ? 'danger' : 'success' ?>" title="Outstanding Balance">
                            Bal: <?= number_format($l_bal, 2) ?>
                        </span>

                        <div class="flex-grow-1 mx-2" style="min-width:80px;max-width:180px;">
                            <div class="progress" style="height:6px;" title="<?= $l_pct ?>% recovered">
                                <div class="progress-bar bg-success" style="width:<?= $l_pct ?>%"></div>
                                <div class="progress-bar bg-danger"  style="width:<?= 100 - $l_pct ?>%"></div>
                            </div>
                            <small style="font-size:10px;line-height:1;"><?= $l_pct ?>% recovered</small>
                        </div>

                    </div>
                </div>

                <div id="<?= $panel_id ?>" class="collapse" data-parent="#badLoanAccordion">
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped table-hover mb-0" style="font-size:12px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Borrower</th>
                                    <th>Opening Date</th>
                                    <th class="text-right">Loan Amount</th>
                                    <th class="text-right">Recovered</th>
                                    <th class="text-right">Balance</th>
                                    <th class="text-right">Interest Collected</th>
                                    <th>Recovery</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n = 1; foreach ($loans as $loan):
                                    $bal       = $loan->amount - $loan->settled;
                                    $loan_pct  = $loan->amount > 0 ? round($loan->settled / $loan->amount * 100) : 0;
                                    $row_class = $bal <= 0 ? 'table-success' : ($loan_pct >= 50 ? '' : 'table-danger');
                                ?>
                                <tr class="<?= $row_class ?>">
                                    <td class="text-muted"><?= $n++ ?></td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($loan->borrower) ?></td>
                                    <td class="text-nowrap"><?= date('d/m/Y', strtotime($loan->opening_date)) ?></td>
                                    <td class="text-right"><?= number_format($loan->amount, 2) ?></td>
                                    <td class="text-right text-success"><?= number_format($loan->settled, 2) ?></td>
                                    <td class="text-right font-weight-bold <?= $bal > 0 ? 'text-danger' : 'text-success' ?>">
                                        <?= number_format($bal, 2) ?>
                                    </td>
                                    <td class="text-right text-info"><?= number_format($loan->interest_settled, 2) ?></td>
                                    <td style="min-width:90px;">
                                        <div class="progress" style="height:8px;" title="<?= $loan_pct ?>%">
                                            <div class="progress-bar <?= $loan_pct >= 100 ? 'bg-success' : ($loan_pct >= 50 ? 'bg-warning' : 'bg-danger') ?>"
                                                 style="width:<?= $loan_pct ?>%"></div>
                                        </div>
                                        <small style="font-size:10px;"><?= $loan_pct ?>%</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="font-weight-bold" style="background:#f4f4f4;">
                                <tr>
                                    <td colspan="3" class="text-right">Lender Total</td>
                                    <td class="text-right"><?= number_format($l_amount, 2) ?></td>
                                    <td class="text-right text-success"><?= number_format($l_settled, 2) ?></td>
                                    <td class="text-right text-danger"><?= number_format($l_bal, 2) ?></td>
                                    <td class="text-right text-info"><?= number_format(array_sum(array_map(fn($l) => $l->interest_settled, $loans)), 2) ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script>
$('#badLoanAccordion').on('show.bs.collapse', function(e) {
    $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(90deg)');
}).on('hide.bs.collapse', function(e) {
    $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(0deg)');
});
</script>

<?php include 'footer.php'; ?>
