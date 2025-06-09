<?php
include 'model/borrowers.php';
if(isset($_POST)){
    (new borrowers())->save($_POST);
    header('Location:borrowers.php');
}
