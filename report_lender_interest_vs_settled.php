<?php
include_once 'header.php';
include_once 'model/lenders.php';

$allLenders = (new lenders)->fetchall();

$lenderId = isset($_GET['lender_id']) && $_GET['lender_id'] !== '' ? (int)$_GET['lender_id'] : 0;
$toDate   = isset($_GET['to_date'])   && $_GET['to_date']   !== '' ? $_GET['to_date']   : date('Y-m-d');

$segments        = [];
$totalInterest   = 0;
$totalSettled    = 0;
$totalCollected  = 0;
$investmentRows  = [];
$settledRows     = [];
$collectedRows   = [];

if ($lenderId > 0) {
    global $db;

    // ── 1. Fetch all Loan-category investment entries (C = invest, D = withdraw)
    $sql = "
        SELECT i.txn_date, i.transaction_type, i.amount, i.interest_rate
        FROM investments i
        WHERE i.transaction_category = 'Loan'
          AND i.lenderid = ?
        ORDER BY i.txn_date, i.id
    ";
    $investmentRows = $db->query($sql, [$lenderId])->results();

    // ── 2. Fetch interest settled to lender (Interest category, D = paid out)
    $sqlSettled = "
        SELECT i.txn_date, i.amount
        FROM investments i
        WHERE i.transaction_category = 'Interest'
          AND i.transaction_type = 'D'
          AND i.lenderid = ?
        ORDER BY i.txn_date
    ";
    $settledRows = $db->query($sqlSettled, [$lenderId])->results();
    foreach ($settledRows as $sr) {
        $totalSettled += (float)$sr->amount;
    }

    // ── 3. Fetch interest collected from borrowers (Interest category, C = received)
    $sqlCollected = "
        SELECT i.txn_date, i.amount, i.description
        FROM investments i
        WHERE i.transaction_category = 'Interest'
          AND i.transaction_type = 'C'
          AND i.lenderid = ?
        ORDER BY i.txn_date
    ";
    $collectedRows = $db->query($sqlCollected, [$lenderId])->results();
    foreach ($collectedRows as $cr) {
        $totalCollected += (float)$cr->amount;
    }

    /*
     * ── 4. Build interest segments ──────────────────────────────────────────
     *
     * Each C entry creates a new "active slice" starting from its txn_date.
     * Each D entry reduces the active balance (applied to slices FIFO).
     * For each active slice, interest runs from its start_date until:
     *   - the slice is fully cleared by a D entry  → end = D entry date - 1 day
     *   - OR the report to_date                    → end = to_date
     *
     * Interest per slice = sum over calendar months of:
     *   amount * rate/100 / days_in_month * days_active_in_month
     */

    // Each entry: [start_date, amount, interest_rate, end_date|null]
    $slices = [];

    foreach ($investmentRows as $row) {
        $date   = $row->txn_date;
        $amount = (float)$row->amount;
        $rate   = (float)$row->interest_rate;

        if ($row->transaction_type === 'C') {
            $slices[] = ['start' => $date, 'amount' => $amount, 'rate' => $rate, 'end' => null];
        } else {
            // D = debit/withdrawal: reduce oldest slices first (FIFO)
            $remaining = $amount;
            foreach ($slices as &$slice) {
                if ($slice['end'] !== null) continue; // already closed
                if ($remaining <= 0) break;

                if ($slice['amount'] <= $remaining) {
                    $slice['end'] = $date; // closed on this date
                    $remaining   -= $slice['amount'];
                } else {
                    // Partial withdrawal: close this slice, create new reduced one from same date
                    $slice['end'] = $date;
                    $newAmount    = $slice['amount'] - $remaining;
                    $slices[]     = ['start' => $date, 'amount' => $newAmount, 'rate' => $slice['rate'], 'end' => null];
                    $remaining    = 0;
                }
            }
            unset($slice);
        }
    }

    // ── 5. Calculate interest per slice
    foreach ($slices as $slice) {
        $startDate = new DateTime($slice['start']);
        $endDate   = $slice['end'] !== null
            ? (new DateTime($slice['end']))->modify('-1 day')
            : new DateTime($toDate);

        if ($endDate < $startDate) {
            // Zero-day slice (debit on same day as credit)
            $segments[] = [
                'start'    => $slice['start'],
                'end'      => $slice['end'] ?? $toDate,
                'amount'   => $slice['amount'],
                'rate'     => $slice['rate'],
                'interest' => 0,
            ];
            continue;
        }

        $interest = calculateInterestForPeriod($slice['amount'], $slice['rate'], $startDate, $endDate);
        $totalInterest += $interest;

        $segments[] = [
            'start'    => $slice['start'],
            'end'      => $endDate->format('Y-m-d'),
            'amount'   => $slice['amount'],
            'rate'     => $slice['rate'],
            'interest' => round($interest, 2),
        ];
    }
}

/**
 * Calculate interest for an amount over a date range using monthly rate.
 * rate = 1.5 means ₹1500/month per ₹1,00,000 (i.e. 1.5% monthly).
 * Days within each calendar month are prorated: rate/100 * amount / days_in_month * days_active.
 */
function calculateInterestForPeriod(float $amount, float $rate, DateTime $from, DateTime $to): float
{
    if ($rate <= 0 || $amount <= 0) return 0;

    $total   = 0.0;
    $current = clone $from;

    while ($current <= $to) {
        $year      = (int)$current->format('Y');
        $month     = (int)$current->format('m');
        $daysInMon = (int)(new DateTime("$year-$month-01"))->format('t');

        // Last day of this month
        $endOfMonth = new DateTime($current->format('Y-m-t'));
        $segEnd     = $endOfMonth < $to ? $endOfMonth : $to;

        $daysActive = (int)$current->diff($segEnd)->days + 1;
        $total     += ($amount * $rate / 100) / $daysInMon * $daysActive;

        // Move to first of next month
        $current = (clone $endOfMonth)->modify('+1 day');
    }

    return $total;
}
?>
<section class="content">
    <div class="container-fluid">

        <!-- Filter -->
        <div class="card card-default">
            <div class="card-header"><h3 class="card-title">Lender Interest vs Settled</h3></div>
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
                        <label class="mr-2">Calculate Up To</label>
                        <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </form>
            </div>
        </div>

        <?php if ($lenderId > 0): ?>

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Actual Interest (Calculated)</span>
                        <span class="info-box-number">₹<?= number_format($totalInterest, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <?php $shortfall = $totalInterest - $totalCollected; ?>
                <div class="info-box <?= $shortfall > 0 ? 'bg-warning' : 'bg-success' ?>">
                    <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Collected from Borrowers</span>
                        <span class="info-box-number">₹<?= number_format($totalCollected, 2) ?>
                            <?php if ($shortfall > 0): ?>
                                <small>(Short: ₹<?= number_format($shortfall, 2) ?>)</small>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check-double"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Interest Settled to Lender</span>
                        <span class="info-box-number">₹<?= number_format($totalSettled, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <?php $pending = $totalInterest - $totalSettled; ?>
                <div class="info-box <?= $pending > 0 ? 'bg-danger' : 'bg-success' ?>">
                    <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending to Lender</span>
                        <span class="info-box-number">₹<?= number_format(abs($pending), 2) ?>
                            <?= $pending < 0 ? '<small>(Over-paid)</small>' : '' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Segments -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Interest Calculation Breakdown (up to <?= htmlspecialchars($toDate) ?>)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th class="text-right">Amount (₹)</th>
                            <th class="text-right">Rate (%/mo)</th>
                            <th class="text-right">Interest (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($segments)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No investment data found.</td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($segments as $seg): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($seg['start']) ?></td>
                                <td><?= htmlspecialchars($seg['end']) ?></td>
                                <td class="text-right"><?= number_format($seg['amount'], 2) ?></td>
                                <td class="text-right"><?= number_format($seg['rate'], 2) ?></td>
                                <td class="text-right"><strong><?= number_format($seg['interest'], 2) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($segments)): ?>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <th colspan="5" class="text-right">Total Calculated Interest</th>
                            <th class="text-right">₹<?= number_format($totalInterest, 2) ?></th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Settlement History -->
        <?php if (!empty($settledRows)): ?>
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Interest Settlement History (Paid to Lender)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th class="text-right">Amount Settled (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $j = 1; foreach ($settledRows as $sr): ?>
                        <tr>
                            <td><?= $j++ ?></td>
                            <td><?= htmlspecialchars($sr->txn_date) ?></td>
                            <td class="text-right"><?= number_format($sr->amount, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <th colspan="2" class="text-right">Total Settled</th>
                            <th class="text-right">₹<?= number_format($totalSettled, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Collected from Borrowers -->
        <?php if (!empty($collectedRows)): ?>
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    Interest Collected from Borrowers
                    <?php $shortfall = $totalInterest - $totalCollected; ?>
                    <?php if ($shortfall > 0): ?>
                        <span class="badge badge-danger ml-2">Short by ₹<?= number_format($shortfall, 2) ?></span>
                    <?php else: ?>
                        <span class="badge badge-success ml-2">Fully Collected</span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Narration</th>
                            <th class="text-right">Amount Collected (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $k = 1; foreach ($collectedRows as $cr): ?>
                        <tr>
                            <td><?= $k++ ?></td>
                            <td><?= htmlspecialchars($cr->txn_date) ?></td>
                            <td><?= htmlspecialchars($cr->description) ?></td>
                            <td class="text-right"><?= number_format($cr->amount, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <th colspan="3" class="text-right">Total Collected</th>
                            <th class="text-right">₹<?= number_format($totalCollected, 2) ?></th>
                        </tr>
                        <?php if ($shortfall > 0): ?>
                        <tr class="font-weight-bold text-danger bg-light">
                            <th colspan="3" class="text-right">Shortfall (Calculated − Collected)</th>
                            <th class="text-right">₹<?= number_format($shortfall, 2) ?></th>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>
<?php include 'footer.php'; ?>
