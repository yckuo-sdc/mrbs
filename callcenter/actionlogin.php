<?php

include("function.php");
include("mysql_connect.inc.php");
	
$account  = $_POST['account'];
$password = $_POST['password'];
 //特殊字元跳脫(NUL (ASCII 0), \n, \r, \, ', ", and Control-Z)
$account  = mysqli_real_escape_string($conn,$account);
$password = mysqli_real_escape_string($conn,$password);
//搜尋資料庫資料
$sql = "SELECT * FROM mrbs_users where name = '$account'";
$result = mysqli_query($conn,$sql);
$row = @mysqli_fetch_assoc($result);

if(checkAccountByLDAP($account, $password) && $row['name'] == $account){
	session_start();
	$_SESSION['account']	= $account;
	$_SESSION['UserName']   = $row['UserName'];
	//echo $row['Level']; 
	if($row['level'] == 2){
		$_SESSION['Level'] = $row['Level'];
	}
	header("refresh:0;url=index.php"); 
}else{
	session_start();
	$_SESSION["error"] = "invalid account or password";
	header("refresh:0;url=login.php"); 
}

?>
