<?php
include 'model/borrowers.php';
if (isset($_POST)) {
    $model = new borrowers();
    if (!empty($_POST['id'])) {
        $model->update((int)$_POST['id'], $_POST);
    } else {
        $model->save($_POST);
    }
    header('Location: borrowers.php');
}
