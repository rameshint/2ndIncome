<?php
include 'header.php';
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Adding new lender</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" action="lender_save.php" method="post">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputName">Name</label>
                            <input type="text" name="name" class="form-control"  required id="exampleInputName" placeholder="Enter Name">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputTel1">Contact No</label>
                            <input type="tel" name="primary_contact_no" required class="form-control" id="exampleInputTel1" placeholder="Contact Number">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputTel2">Secondary Contact No</label>
                            <input type="tel" name="secondary_contact_no" class="form-control" id="exampleInputTel2" placeholder="Secondary Contact Number">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputTel2">Address</label>
                            <textarea class="form-control" name="address"></textarea>
                        </div>


                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a  class="btn btn-danger" href="lenders.php">Cancel</a>
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
