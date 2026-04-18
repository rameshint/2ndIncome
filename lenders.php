<?php
include 'header.php';
include_once 'model/lenders.php'
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="lender_add.php" class="btn btn-success btn-sm">
                            <i class="fas fa-user-plus"></i> Add New
                        </a>
                        <div class="card-tools">
                            <input type="search" id="lenderSearch" class="form-control form-control-sm" placeholder="Search Lenders" style="width: 250px;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover" id="lendersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th class="text-right">Net Investment</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $lenders = (new lenders)->fetchall();
                                $i = 1;
                                foreach ($lenders as $lender) {
                                    $typeBadge = $lender->lender_type === 'Sharing' ? 'badge-warning' : 'badge-success';
                                ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><a href="lender_detail.php?id=<?php echo $lender->id ?>"><?php echo htmlspecialchars($lender->name) ?></a></td>
                                    <td><span class="badge <?php echo $typeBadge ?>"><?php echo htmlspecialchars($lender->lender_type) ?></span></td>
                                    <td class="text-right"><?php echo number_format($lender->net_investment) ?></td>
                                    <td class="text-center"><a href="lender_edit.php?id=<?php echo $lender->id ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.getElementById('lenderSearch').addEventListener('keyup', function () {
    var filter = this.value.toLowerCase();
    var rows = document.querySelectorAll('#lendersTable tbody tr');
    rows.forEach(function (row) {
        var name = row.cells[1].textContent.toLowerCase();
        row.style.display = name.indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>
<?php
include 'footer.php';
?>
