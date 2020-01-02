<?php
	if(!empty($_GET)){ 
		$apcode = 'sdc-mrbs';
		$srcToken = $_GET["token"];
		$encryToken = hash_hmac('sha256', $srcToken, $apcode);
		$url = "http://vision.tainan.gov.tw/common/sso_verify.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("token"=>$encryToken))); 
		$account = trim(curl_exec($ch)); 
		curl_close($ch);
		
		require_once("mysql_connect.inc.php");	
		$sql = "SELECT * FROM mrbs_users where name = '".$account."' and level=1" ;
		$result = mysqli_query($conn,$sql);
		$row = @mysqli_fetch_assoc($result);
		echo "2:".$account;
		echo "3:".$row['name'];

		if($row['name'] == $account){
			session_start();
			$_SESSION['account']	= $account;
			$_SESSION['UserName']   = $row['UserName'];
			$conn->close();
			echo "true";
			header("refresh:0;url=index.php"); 
		}else{
			session_start();
			$conn->close();
			echo "false";
			header("refresh:0;url=error.html"); 
		}

	}
