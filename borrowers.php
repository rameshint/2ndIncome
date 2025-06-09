<?php
include 'header.php';
include_once 'model/borrowers.php'
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row" style="margin: 10px 0 10px 0">
            <div class="col-12">
                <input type="search" name="search" class="form-control" placeholder="Search Lenders">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-user-plus"></i></span>

                    <div class="info-box-content">
                        <a class="info-box-text"><a  href="borrower_add.php" style="color: #000;position: relative;top: 8px;font-size: 23px;" >Add New</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <?php
            $borrowers = (new borrowers)->fetchall();
            foreach ($borrowers as $borrower) {
                ?>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-<?php echo $colors[array_rand($colors)] ?>"><?php echo strtoupper(substr($borrower->name,0,1)) ?></span>

                        <div class="info-box-content">
                            <a class="info-box-text"><a href="borrower_detail.php?id=<?php echo $borrower->id?>" style="color: #000" ><?php echo $borrower->name?></a></span>
                                <span class="info-box-number text-sm " style="font-weight: normal"><?php echo ($borrower->primary_contact_no)?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>
