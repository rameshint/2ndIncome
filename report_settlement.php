<?php
include_once 'header.php';
include_once 'model/reports.php';
$reportObj = new reports();
$rst = $reportObj->settlement($_GET['date']);

// Pre-process flat ROLLUP data into grouped structure
$lenders_data = [];
$grand_total = null;
$current_lender = null;

foreach ($rst as $row) {
    if ($row->lender == '') {
        $grand_total = $row;
    } elseif ($row->borrower == '') {
        $current_lender = $row->lender;
        $lenders_data[$current_lender] = ['subtotal' => $row, 'settlements' => []];
    } else {
        if ($current_lender) {
            $lenders_data[$current_lender]['settlements'][] = $row;
        }
    }
}
?>
<section class="content">
    <div class="container-fluid">

        <!-- Date filter -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="get" class="form-inline">
                    <select class="form-control mr-2" name="date">
                        <?php
                        foreach (range(0, -12) as $item) {
                            $month      = date('Y-M', strtotime(date('Y-m-d') . "$item month"));
                            $monthvalue = date('Y-m-01', strtotime(date('Y-m-d') . "$item month"));
                            $selected   = (!empty($_GET['date']) && $_GET['date'] == $monthvalue) ? ' selected' : '';
                            echo "<option value='$monthvalue'$selected>$month</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <?php if ($grand_total): ?>
        <div class="row mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary"><i class="fas fa-hand-holding-usd"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lender Interest</span>
                        <span class="info-box-number"><?= number_format($grand_total->lender_interest, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Commission</span>
                        <span class="info-box-number"><?= number_format($grand_total->commission, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-undo-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recovery</span>
                        <span class="info-box-number"><?= number_format($grand_total->recovery, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-plus-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Excess</span>
                        <span class="info-box-number"><?= number_format($grand_total->excess, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list mr-2"></i>SETTLEMENT</h5>
                <div>
                    <button class="btn btn-xs btn-outline-secondary mr-1" onclick="$('.collapse').collapse('show')">Expand All</button>
                    <button class="btn btn-xs btn-outline-secondary" onclick="$('.collapse').collapse('hide')">Collapse All</button>
                </div>
            </div>
        </div>

        <div class="accordion" id="settlementAccordion">
            <?php $idx = 0; foreach ($lenders_data as $lender_name => $lender):
                $panel_id = 'lender-panel-' . $idx++;
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
                        <span class="badge badge-secondary"><?= count($lender['settlements']) ?> settlement<?= count($lender['settlements']) > 1 ? 's' : '' ?></span>
                        <span class="badge badge-primary" title="Lender Interest">Int: <?= number_format($lender['subtotal']->lender_interest, 2) ?></span>
                        <span class="badge badge-info" title="Commission">Com: <?= number_format($lender['subtotal']->commission, 2) ?></span>
                        <span class="badge badge-success" title="Recovery">Rec: <?= number_format($lender['subtotal']->recovery, 2) ?></span>
                        <span class="badge badge-warning" title="Excess">Exc: <?= number_format($lender['subtotal']->excess, 2) ?></span>
                    </div>
                </div>

                <div id="<?= $panel_id ?>" class="collapse" data-parent="#settlementAccordion">
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped table-hover mb-0" style="font-size:12px;">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Borrower</th>
                                    <th>Settlement Date</th>
                                    <th class="text-right">Lender Interest</th>
                                    <th class="text-right">Commission</th>
                                    <th class="text-right">Recovery</th>
                                    <th class="text-right">Excess</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n = 1; foreach ($lender['settlements'] as $s): ?>
                                <tr>
                                    <td class="text-muted"><?= $n++ ?></td>
                                    <td><?= htmlspecialchars($s->borrower) ?></td>
                                    <td class="text-nowrap"><?= $s->settlement_date ? date('d/m/y', strtotime($s->settlement_date)) : '' ?></td>
                                    <td class="text-right"><?= number_format($s->lender_interest, 2) ?></td>
                                    <td class="text-right"><?= number_format($s->commission, 2) ?></td>
                                    <td class="text-right"><?= number_format($s->recovery, 2) ?></td>
                                    <td class="text-right"><?= number_format($s->excess, 2) ?></td>
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
        $('#settlementAccordion').on('show.bs.collapse', function(e) {
            $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(90deg)');
        }).on('hide.bs.collapse', function(e) {
            $(e.target).prev().find('.accordion-icon').css('transform', 'rotate(0deg)');
        });
        </script>

    </div>
</section>
<?php include 'footer.php'; ?>
