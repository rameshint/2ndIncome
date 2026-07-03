<?php
include 'model/investments.php';
if(isset($_POST)){
     
    if(isset($_POST['interest_to_loan']) && $_POST['interest_to_loan'] == 1){
       (new investments())->interest_to_loan_save($_POST);
    }else{
        (new investments())->save($_POST);
    }
    header('Location:lender_detail.php?id='.$_POST['lenderid']);
}
