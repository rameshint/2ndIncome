<?php
include 'header.php';
include_once 'model/lenders.php';
$lender = (new lenders)->fetchBasic($_GET['id']);
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Lender</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" action="lender_save.php" method="post">
                    <input type="hidden" name="id" value="<?php echo (int)$lender->id ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputName">Name</label>
                            <input type="text" name="name" class="form-control" required id="exampleInputName" placeholder="Enter Name" value="<?php echo htmlspecialchars($lender->name) ?>">
                        </div>
                        <div class="form-group">
                            <label for="lenderType">Lender Type</label>
                            <select name="lender_type" id="lenderType" class="form-control" required>
                                <option value="Individual" <?php echo $lender->lender_type === 'Individual' ? 'selected' : '' ?>>Individual</option>
                                <option value="Sharing" <?php echo $lender->lender_type === 'Sharing' ? 'selected' : '' ?>>Sharing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputTel1">Contact No</label>
                            <input type="tel" name="primary_contact_no" required class="form-control" id="exampleInputTel1" placeholder="Contact Number" value="<?php echo htmlspecialchars($lender->primary_contact_no) ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputTel2">Secondary Contact No</label>
                            <input type="tel" name="secondary_contact_no" class="form-control" id="exampleInputTel2" placeholder="Secondary Contact Number" value="<?php echo htmlspecialchars($lender->secondary_contact_no) ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleAddress">Address</label>
                            <textarea class="form-control" name="address" id="exampleAddress"><?php echo htmlspecialchars($lender->address) ?></textarea>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a class="btn btn-danger" href="lenders.php">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>
