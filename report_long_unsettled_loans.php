<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$result = $reportObj->longUnpaidLoans();

// Compute summary stats
$total_loans    = 0;
$total_balance  = 0;
$total_amount   = 0;
$worst_overdue_days = 0;

foreach ($result as $loans) {
    foreach ($loans as $loan) {
        $total_loans++;
        $total_balance += $loan->balance;
        $total_amount  += $loan->amount;
        $overdue_days   = (int) floor((time() - strtotime($loan->agreed_closing_date)) / 86400);
        if ($overdue_days > $worst_overdue_days) $worst_overdue_days = $overdue_days;
    }
}

// Returns Bootstrap color class based on how long the loan is overdue past agreed date
function overdueColor($agreed_closing_date): string {
    $days = (int) floor((time() - strtotime($agreed_closing_date)) / 86400);
    if ($days > 365) return 'danger';
    if ($days > 180) return 'warning';
    return 'info';
}

function overdueLabel($agreed_closing_date): string {
    $days = (int) floor((time() - strtotime($agreed_closing_date)) / 86400);
    if ($days > 365) return round($days / 365, 1) . 'y overdue';
    if ($days > 30)  return (int) floor($days / 30) . 'm overdue';
    return $days . 'd overdue';
}
?>
<section class="content">
    <div class="container-fluid">

        <!-- Summary Info Boxes -->
        <div class="row mb-2">
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Overdue Loans</span>
                        <span class="info-box-number"><?= $total_loans ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Borrowers Affected</span>
                        <span class="info-box-number"><?= count($result) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Loan Amount</span>
                        <span class="info-box-number"><?= number_format($total_amount, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger"><i class="fas fa-hourglass-end"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Outstanding Balance</span>
                        <span class="info-box-number text-danger"><?= number_format($total_balance, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recovery progress bar -->
        <?php $recovery_pct = $total_amount > 0 ? round(($total_amount - $total_balance) / $total_amount * 100, 1) : 0; ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="font-weight-bold">Overall Recovery Progress</small>
                            <small><?= $recovery_pct ?>% recovered &nbsp;|&nbsp;
                                   Paid: <strong><?= number_format($total_amount - $total_balance, 2) ?></strong> &nbsp;|&nbsp;
                                   Remaining: <strong class="text-danger"><?= number_format($total_balance, 2) ?></strong>
                            </small>
                        </div>
                        <div class="progress" style="height:14px;">
                            <div class="progress-bar bg-success" style="width:<?= $recovery_pct ?>%"><?= $recovery_pct ?>%</div>
                            <div class="progress-bar bg-danger" style="width:<?= 100 - $recovery_pct ?>%"><?= 100 - $recovery_pct ?>%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrower Accordion -->
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock mr-1 text-danger"></i>LONG UNSETTLED LOANS — Borrower Wise</h5>
                <div>
                    <button class="btn btn-xs btn-outline-secondary mr-1" onclick="$('.collapse').collapse('show')">Expand All</button>
                    <button class="btn btn-xs btn-outline-secondary" onclick="$('.collapse').collapse('hide')">Collapse All</button>
                </div>
            </div>
        </div>

        <div class="accordion" id="borrowerAccordion">
            <?php $idx = 0; foreach ($result as $borrower_name => $loans):
                $card_color       = 'info';
                $borrower_balance = 0;
                $borrower_amount  = 0;
                foreach ($loans as $loan) {
                    $borrower_balance += $loan->balance;
                    $borrower_amount  += $loan->amount;
                    $c = overdueColor($loan->agreed_closing_date);
                    if ($c === 'danger') $card_color = 'danger';
                    elseif ($c === 'warning' && $card_color !== 'danger') $card_color = 'warning';
                }
                $b_pct      = $borrower_amount > 0 ? round(($borrower_amount - $borrower_balance) / $borrower_amount * 100) : 0;
                $panel_id   = 'panel-' . $idx++;
            ?>
            <div class="card card-<?= $card_color ?> shadow-sm mb-1" style="border-left: 4px solid;">
                <!-- Accordion header — uniform height regardless of loan count -->
                <div class="card-header py-2 px-3" id="hdr-<?= $panel_id ?>"
                     data-toggle="collapse" data-target="#<?= $panel_id ?>"
                     aria-expanded="false" style="cursor:pointer;">
                    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

                        <i class="fas fa-chevron-right accordion-icon mr-1" style="font-size:10px;transition:transform .2s;"></i>
                        <span class="font-weight-bold mr-2"><?= htmlspecialchars($borrower_name) ?></span>

                        <span class="badge badge-secondary"><?= count($loans) ?> loan<?= count($loans) > 1 ? 's' : '' ?></span>
                        <span class="badge badge-<?= $card_color ?>">Bal: <?= number_format($borrower_balance, 2) ?></span>
                        <span class="badge badge-light text-muted">Amt: <?= number_format($borrower_amount, 2) ?></span>

                        <!-- Inline mini progress -->
                        <div class="flex-grow-1 mx-2" style="min-width:80px;max-width:160px;">
                            <div class="progress" style="height:6px;" title="<?= $b_pct ?>% recovered">
                                <div class="progress-bar bg-success" style="width:<?= $b_pct ?>%"></div>
                                <div class="progress-bar bg-<?= $card_color ?>" style="width:<?= 100 - $b_pct ?>%"></div>
                            </div>
                            <small style="font-size:10px;line-height:1;"><?= $b_pct ?>% recovered</small>
                        </div>

                        <?php
                        // Show the worst overdue badge in the header
                        $worst_agreed = null;
                        foreach ($loans as $loan) {
                            if (!$worst_agreed || strtotime($loan->agreed_closing_date) < strtotime($worst_agreed))
                                $worst_agreed = $loan->agreed_closing_date;
                        }
                        ?>
                        <span class="badge badge-<?= overdueColor($worst_agreed) ?> ml-auto">
                            <?= overdueLabel($worst_agreed) ?>
                        </span>
                    </div>
                </div>

                <!-- Loan detail — collapsed by default -->
                <div id="<?= $panel_id ?>" class="collapse" data-parent="#borrowerAccordion">
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped table-hover mb-0" style="font-size:12px;">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Lender</th>
                                    <th>Opened</th>
                                    <th>Agreed Date</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Paid</th>
                                    <th class="text-right">Balance</th>
                                    <th>Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n = 1; foreach ($loans as $loan):
                                    $row_color = overdueColor($loan->agreed_closing_date);
                                ?>
                                <tr>
                                    <td class="text-muted"><?= $n++ ?></td>
                                    <td><?= htmlspecialchars($loan->lender) ?></td>
                                    <td class="text-nowrap"><?= date('d/m/y', strtotime($loan->opening_date)) ?></td>
                                    <td class="text-nowrap text-<?= $row_color ?>"><?= date('d/m/y', strtotime($loan->agreed_closing_date)) ?></td>
                                    <td class="text-right"><?= number_format($loan->amount, 2) ?></td>
                                    <td class="text-right text-success"><?= number_format($loan->paid, 2) ?></td>
                                    <td class="text-right font-weight-bold text-<?= $row_color ?>"><?= number_format($loan->balance, 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $row_color ?>" title="Open duration: <?= $loan->diff ?>">
                                            <?= overdueLabel($loan->agreed_closing_date) ?>
                                        </span>
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
        // Rotate chevron icon on accordion expand/collapse
        $('#borrowerAccordion').on('show.bs.collapse', function(e) {
            $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(90deg)');
        }).on('hide.bs.collapse', function(e) {
            $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(0deg)');
        });
        </script>

    </div>
</section>
<?php include 'footer.php'; ?>
