<?php
	function sso_verify_vision(){
		if(!empty($_GET)){ 
			$apcode = 'sdc-iss';
			$srcToken = $_GET["token"];
			$encryToken = hash_hmac('sha256', $srcToken, $apcode);
			$url = "http://vision.tainan.gov.tw/common/sso_verify.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("token"=>$encryToken))); 
			$output = curl_exec($ch); 
			curl_close($ch);
			return $output;
		}
	}

	function verify_mrbs_users($account){
		//搜尋資料庫資料
		require_once("mysql_connect.inc.php");	
		$sql = "SELECT * FROM mrbs_users where name = '".$account."'" ;
		$result = mysqli_query($conn,$sql);
		$row = @mysqli_fetch_assoc($result);
		//echo "2:".$account;
		//echo "3:".$row['name'];

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
			//header("refresh:0;url=error.html"); 

		}
	}
	
	
	function verifyBySession($var){
		if(isset($_SESSION[$var])){
			return true;
		}
		else{
			echo 'You Do Not Have Permission To Access!';
			header("Location:error.html"); 
			return false;
		}
	}	

	function issetBySession($var){
		if(isset($_SESSION[$var])){
			return true;
		}
		else{
			return false;
		}
	}	
		
	function checkAccountByLDAP($user, $ldappass){
		$ldaphost = "tainan.gov.tw";
		$ldapconn = ldap_connect($ldaphost);
		//$ldapconn = ldap_connect("10.6.2.1");
		$ldaprdn = $user . "@" . $ldaphost;
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		if ($ldapconn){
			//binding to ldap server
			//'@' removes the warning
			@$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
			// verify binding
			if ($ldapbind) {
				//echo "LDAP bind successful...";
				ldap_close($ldapconn);
				return true;
			} else {
				//echo "LDAP bind failed...";														
				ldap_close($ldapconn);
				return false;	
			}
		}else{
			ldap_close($ldapconn);
			return false;	
		}	
	}
	//alert message
	function phpAlert($msg) {
		echo '<script type="text/javascript">alert("' . $msg . '")</script>';
	}

	function phpLocateURL($url) {
		echo "<script type='text/javascript'>window.location.href='".$url."'</script>";
	}

	function getFullTextSearchSQL($conn,$table,$key) {
		$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '".$table."'";
		$result = mysqli_query($conn,$sql);
		$rowcount = mysqli_num_rows($result);
		$result_sql = "";
		$count = 0;

		while($row = mysqli_fetch_assoc($result)){
			if (++$count == $rowcount) {
				//last row
				$result_sql = $result_sql." ".$row['column_name']." LIKE '%".$key."%'";
			} else {
				//not last row
				$result_sql = $result_sql." ".$row['column_name']." LIKE '%".$key."%' OR";
			}
		}
		return $result_sql;
	}

	function get_Year_Month($year,$month){
		if($month==1){
			$prev_year = $year - 1;
			$next_year = $year;
			$prev_month = 12;
			$next_month = $month + 1;
		}elseif($month==12){
			$prev_year = $year;
			$next_year = $year + 1;
			$prev_month = $month - 1;
			$next_month = 1;
		}else{
			$prev_year = $year;
			$next_year = $year;
			$prev_month = $month - 1;
			$next_month = $month + 1;
		}

		return array($prev_year,$next_year,$prev_month,$next_month);
	}

?>
