<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$rst = $reportObj->lenderBorrowerLoans();

// Pre-process flat ROLLUP data into grouped structure
$lenders_data = [];
$grand_total = null;
$current_lender = null;

foreach ($rst as $row) {
    if ($row->lender == '') {
        $grand_total = $row;
    } elseif ($row->borrower == '') {
        $current_lender = $row->lender;
        $lenders_data[$current_lender] = ['subtotal' => $row, 'loans' => []];
    } else {
        if ($current_lender) {
            $lenders_data[$current_lender]['loans'][] = $row;
        }
    }
}
?>
<section class="content">
    <div class="container-fluid">

        <?php if ($grand_total): ?>
        <div class="row mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lenders</span>
                        <span class="info-box-number"><?= count($lenders_data) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="fas fa-file-invoice-dollar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Loans</span>
                        <span class="info-box-number"><?= array_sum(array_map(fn($l) => count($l['loans']), $lenders_data)) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Loan Amount</span>
                        <span class="info-box-number"><?= number_format($grand_total->loan_amount, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Balance</span>
                        <span class="info-box-number"><?= number_format($grand_total->loan_amount - $grand_total->repaid, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list mr-2"></i>LENDER BORROWER LOANS</h5>
                <div>
                    <button class="btn btn-xs btn-outline-secondary mr-1" onclick="$('.collapse').collapse('show')">Expand All</button>
                    <button class="btn btn-xs btn-outline-secondary" onclick="$('.collapse').collapse('hide')">Collapse All</button>
                </div>
            </div>
        </div>

        <div class="accordion" id="lenderAccordion">
            <?php $idx = 0; foreach ($lenders_data as $lender_name => $lender):
                $balance    = $lender['subtotal']->loan_amount - $lender['subtotal']->repaid;
                $repaid_pct = $lender['subtotal']->loan_amount > 0
                    ? round(($lender['subtotal']->repaid / $lender['subtotal']->loan_amount) * 100)
                    : 0;
                $bal_color  = $balance > 0 ? 'warning' : 'success';
                $panel_id   = 'lender-panel-' . $idx++;
            ?>
            <div class="card shadow-sm mb-1" style="border-left: 4px solid #007bff;">
                <div class="card-header py-2 px-3" id="hdr-<?= $panel_id ?>"
                     data-toggle="collapse" data-target="#<?= $panel_id ?>"
                     aria-expanded="false" style="cursor:pointer;">
                    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

                        <i class="fas fa-chevron-right accordion-icon mr-1" style="font-size:10px;transition:transform .2s;"></i>
                        <span class="font-weight-bold mr-2">
                            <i class="fas fa-user-tie mr-1 text-primary"></i><?= htmlspecialchars($lender_name) ?>
                        </span>

                        <span class="badge badge-secondary"><?= count($lender['loans']) ?> loan<?= count($lender['loans']) > 1 ? 's' : '' ?></span>
                        <span class="badge badge-primary" title="Total Loan Amount">Amt: <?= number_format($lender['subtotal']->loan_amount, 2) ?></span>
                        <span class="badge badge-<?= $bal_color ?>" title="Outstanding Balance">Bal: <?= number_format($balance, 2) ?></span>

                        <!-- Inline mini recovery progress -->
                        <div class="flex-grow-1 mx-2" style="min-width:80px;max-width:180px;">
                            <div class="progress" style="height:6px;" title="<?= $repaid_pct ?>% repaid">
                                <div class="progress-bar bg-success" style="width:<?= $repaid_pct ?>%"></div>
                                <div class="progress-bar bg-<?= $bal_color ?>" style="width:<?= 100 - $repaid_pct ?>%"></div>
                            </div>
                            <small style="font-size:10px;line-height:1;"><?= $repaid_pct ?>% repaid</small>
                        </div>

                    </div>
                </div>

                <div id="<?= $panel_id ?>" class="collapse" data-parent="#lenderAccordion">
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped table-hover mb-0" style="font-size:12px;">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Borrower</th>
                                    <th>Date</th>
                                    <th class="text-right">Loan Amount</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n = 1; foreach ($lender['loans'] as $loan):
                                    $loan_bal = $loan->loan_amount - $loan->repaid;
                                ?>
                                <tr>
                                    <td class="text-muted"><?= $n++ ?></td>
                                    <td><?= htmlspecialchars($loan->borrower) ?></td>
                                    <td class="text-nowrap"><?= $loan->opening_date ? date('d/m/y', strtotime($loan->opening_date)) : '' ?></td>
                                    <td class="text-right"><?= number_format($loan->loan_amount, 2) ?></td>
                                    <td class="text-right font-weight-bold <?= $loan_bal > 0 ? 'text-warning' : 'text-success' ?>">
                                        <?= number_format($loan_bal, 2) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script>
        $('#lenderAccordion').on('show.bs.collapse', function(e) {
            $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(90deg)');
        }).on('hide.bs.collapse', function(e) {
            $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(0deg)');
        });
        </script>

    </div>
</section>
<?php include 'footer.php'; ?>
