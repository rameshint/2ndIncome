<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
include 'model/lenders.php';
if(isset($_POST)){
    (new lenders)->save($_POST);
    header('Location:lenders.php');
}
