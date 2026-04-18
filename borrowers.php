<?php
include 'header.php';
include_once 'model/borrowers.php'
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="borrower_add.php" class="btn btn-success btn-sm">
                            <i class="fas fa-user-plus"></i> Add New
                        </a>
                        <div class="card-tools">
                            <input type="search" id="borrowerSearch" class="form-control form-control-sm" placeholder="Search Borrowers" style="width: 250px;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover" id="borrowersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $borrowers = (new borrowers)->fetchall();
                                $i = 1;
                                foreach ($borrowers as $borrower) {
                                ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><a href="borrower_detail.php?id=<?php echo $borrower->id ?>"><?php echo htmlspecialchars($borrower->name) ?></a></td>
                                    <td><?php echo htmlspecialchars($borrower->primary_contact_no) ?></td>
                                    <td><?php echo htmlspecialchars($borrower->address) ?></td>
                                    <td class="text-center"><a href="borrower_edit.php?id=<?php echo $borrower->id ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a></td>
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
document.getElementById('borrowerSearch').addEventListener('keyup', function () {
    var filter = this.value.toLowerCase();
    var rows = document.querySelectorAll('#borrowersTable tbody tr');
    rows.forEach(function (row) {
        var name = row.cells[1].textContent.toLowerCase();
        row.style.display = name.indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>
<?php
include 'footer.php';
?>
