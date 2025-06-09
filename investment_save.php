<?php
include 'model/investments.php';
if(isset($_POST)){
    (new investments())->save($_POST);
    header('Location:lender_detail.php?id='.$_POST['lenderid']);
}
