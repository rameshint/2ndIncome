<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();
print_r($_POST);
if($_POST['uname']!= '' && $_POST['upass']){
	$sql = "select id,name,password from users where username = '".$_POST['uname']."'";
	$rst = $db->query($sql)->results()[0];
	
	if($rst->password === md5(md5($_POST['upass']))){
		session_start();
		$_SESSION['userid'] = $rst->id;
		$_SESSION['user_name'] = $rst->name;
		header('Location: home.php');
	}else{
		header('Location: index.php?error=1');
	}
}

?>