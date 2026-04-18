<?php
include 'header.php';
include_once 'model/borrowers.php';
 
$borrower = (new borrowers)->fetch((int)$_GET['id']);

?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Borrower</h3>
                </div>
                <form role="form" action="borrower_save.php" method="post">
                    <input type="hidden" name="id" value="<?php echo (int)$borrower->id ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="inputName">Name</label>
                            <input type="text" name="name" class="form-control" required id="inputName" placeholder="Enter Name" value="<?php echo htmlspecialchars($borrower->name) ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputTel1">Contact No</label>
                            <input type="tel" name="primary_contact_no" required class="form-control" id="inputTel1" placeholder="Contact Number" value="<?php echo htmlspecialchars($borrower->primary_contact_no) ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputTel2">Secondary Contact No</label>
                            <input type="tel" name="secondary_contact_no" class="form-control" id="inputTel2" placeholder="Secondary Contact Number" value="<?php echo htmlspecialchars($borrower->secondary_contact_no) ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputRef">Referer Name</label>
                            <input type="text" name="referenced_by" class="form-control" id="inputRef" placeholder="Enter Referred Name" value="<?php echo htmlspecialchars($borrower->referenced_by) ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputRefContact">Referrer Contact No</label>
                            <input type="tel" name="referenced_contactno" class="form-control" id="inputRefContact" placeholder="Enter Referrer Contact No" value="<?php echo htmlspecialchars($borrower->referenced_contactno) ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Address</label>
                            <textarea class="form-control" name="address" id="inputAddress"><?php echo htmlspecialchars($borrower->address) ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a class="btn btn-danger" href="borrowers.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>
