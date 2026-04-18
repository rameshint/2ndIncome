<?php
include_once 'header.php';
include_once 'model/lenders.php';

$allLenders = (new lenders)->fetchall();

$lenderId = isset($_GET['lender_id']) && $_GET['lender_id'] !== '' ? (int)$_GET['lender_id'] : 0;
$fromDate  = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : date('2026-03-01');

$rows    = [];
$details = [];
$totals  = ['debit' => 0, 'credit' => 0];

if ($lenderId > 0) {
    global $db;

    $sql = "
        SELECT b.id, b.name, i.txn_date AS txn_date, SUM(i.amount) AS amount, i.transaction_type AS interest_type
        FROM investments i
        INNER JOIN borrowers b ON b.id = i.borrower_id
        WHERE i.transaction_category = 'Interest'
          AND i.lenderid = ?
          AND i.txn_date >= ?
        GROUP BY b.id, b.name, i.transaction_type, i.txn_date

        UNION ALL

        SELECT b.id, b.name, s.settlement_date AS txn_date, SUM(s.lender_interest) AS amount, 'C' AS interest_type
        FROM settlement s
        INNER JOIN loans l ON l.id = s.loanid
        INNER JOIN borrowers b ON b.id = l.borrowerid
        WHERE l.lenderid = ?
          AND s.settlement_date >= ?
        GROUP BY b.id, b.name, s.settlement_date
    ";

    $rawRows = $db->query($sql, [$lenderId, $fromDate, $lenderId, $fromDate])->results();

    $tally = [];
    foreach ($rawRows as $r) {
        $bid = (int)$r->id;
        if (!isset($tally[$bid])) {
            $tally[$bid]   = ['name' => $r->name, 'debit' => 0, 'credit' => 0];
            $details[$bid] = [];
        }
        if ($r->interest_type === 'D') {
            $tally[$bid]['debit'] += $r->amount;
        } else {
            $tally[$bid]['credit'] += $r->amount;
        }
        $details[$bid][] = [
            'date'   => $r->txn_date,
            'type'   => $r->interest_type === 'C' ? 'Credit' : 'Debit',
            'amount' => (float)$r->amount,
        ];
    }

    // Sort each borrower's details by date desc
    foreach ($details as &$detailRows) {
        usort($detailRows, fn($a, $b) => strcmp($b['date'], $a['date']));
    }
    unset($detailRows);

    uasort($tally, fn($a, $b) => strcmp($a['name'], $b['name']));

    foreach ($tally as $bid => $row) {
        $net = $row['credit'] - $row['debit'];
        $totals['debit']  += $row['debit'];
        $totals['credit'] += $row['credit'];
        $rows[] = [
            'id'     => $bid,
            'name'   => $row['name'],
            'debit'  => $row['debit'],
            'credit' => $row['credit'],
            'net'    => $net,
        ];
    }
}
?>
<section class="content">
    <div class="container-fluid">

        <!-- Filter Form -->
        <div class="card card-default">
            <div class="card-header"><h3 class="card-title">Borrower Interest Tally</h3></div>
            <div class="card-body">
                <form method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label class="mr-2">Lender</label>
                        <select name="lender_id" class="form-control" required>
                            <option value="">-- Select Lender --</option>
                            <?php foreach ($allLenders as $l): ?>
                                <option value="<?= (int)$l->id ?>" <?= $lenderId === (int)$l->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($l->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </form>
            </div>
        </div>

        <!-- Results -->
        <?php if ($lenderId > 0): ?>
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Interest Tally &mdash; From: <?= htmlspecialchars($fromDate) ?></h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Borrower</th>
                            <th class="text-right">Debit (Interest Charged)</th>
                            <th class="text-right">Credit (Settled)</th>
                            <th class="text-right">Net Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No data found for the selected filters.</td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($rows as $row): ?>
                                <?php $netClass = $row['net'] > 0 ? 'text-success' : ($row['net'] < 0 ? 'text-danger' : ''); ?>
                                <tr class="borrower-summary-row" data-target="detail-<?= $row['id'] ?>" style="cursor:pointer;">
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['name']) ?>
                                        <i class="fas fa-chevron-down float-right text-muted toggle-icon"></i>
                                    </td>
                                    <td class="text-right"><?= number_format($row['debit'], 2) ?></td>
                                    <td class="text-right"><?= number_format($row['credit'], 2) ?></td>
                                    <td class="text-right <?= $netClass ?>">
                                        <strong><?= number_format($row['net'], 2) ?></strong>
                                    </td>
                                </tr>
                                <tr id="detail-<?= $row['id'] ?>" class="detail-row" style="display:none;">
                                    <td colspan="5" class="p-0 bg-light">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="bg-secondary text-white">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($details[$row['id']] as $d): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($d['date']) ?></td>
                                                        <td>
                                                            <span class="badge <?= $d['type'] === 'Credit' ? 'badge-success' : 'badge-danger' ?>">
                                                                <?= $d['type'] ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-right"><?= number_format($d['amount'], 2) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($rows)): ?>
                    <tfoot>
                        <?php $totalNet = $totals['credit'] - $totals['debit']; ?>
                        <tr class="font-weight-bold bg-light">
                            <th colspan="2" class="text-right">Total</th>
                            <th class="text-right"><?= number_format($totals['debit'], 2) ?></th>
                            <th class="text-right"><?= number_format($totals['credit'], 2) ?></th>
                            <th class="text-right <?= $totalNet >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($totalNet, 2) ?>
                            </th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
    $('.borrower-summary-row').on('click', function () {
        var targetId = $(this).data('target');
        var detailRow = $('#' + targetId);
        var icon = $(this).find('.toggle-icon');
        if (detailRow.is(':visible')) {
            detailRow.slideUp(150);
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            detailRow.slideDown(150);
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });
});
</script>
