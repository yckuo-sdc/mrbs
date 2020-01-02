<?php 
	session_start();
	//將session清空
	unset($_SESSION['account']);
	unset($_SESSION['UserName']);
	unset($_SESSION['Level']);
	echo 'logout...';
	header("refresh:0;url=login.php"); 
?>
