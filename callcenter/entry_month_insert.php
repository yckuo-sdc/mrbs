<?php session_start();
	require_once("function.php");
	verifyBySession("account");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>會議室預約系統</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=big5" />
		<title>臺南市政府 智慧發展中心</title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Archivo+Narrow:400,700" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="../images/logo.ico" />
		<link rel="stylesheet" href="css/mystyle.css">
		<link rel="stylesheet" href="css/form_add.css">

		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8 "> 
		<!-- add jQuery-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		
		<!-- semantic ui -->
		<link rel="stylesheet" type="text/css" href="semantic/semantic.css">
		<script src="semantic/semantic.js"></script>

		<!-- add my JS-->
		<script src="js/index.js"></script>

		<!-- add my CSS-->
		<link rel="stylesheet" href="css/index.css">
			
	</head>


	<body>
	<a href="index.php">
	<div id ="header">
		<div class="title">TainanGov SDC 會議室預約系統</div>
	</div>
	</a>
	<div class="ui middle aligned center aligned grid">
	  <div class="column">
			<h2 class="ui sdc-blue image header">
			  <div class="content"><i class="sign-in icon"></i>Insert Monthly Entry</div>
			</h2>
		<div class="ui stacked segment">
	
	<?php
		//connect database
		require_once("mysql_connect.inc.php");
		header('Content-type: text/html; charset=utf-8');
		
		// 取得url參數
		$action = $_GET["action"];
		if (!isset($_GET['page']))	$page = 1; 
		else						$page = $_GET['page']; 
		if (!isset($_GET['year']))	$year = date("Y"); 
		else						$year = $_GET['year']; 
		if (!isset($_GET['month']))	$month = date("n");
		else						$month = $_GET['month']; 
		

		$arr_name = [];
		$arr_UserName = [];
		$count = 0;
		$sql = "SELECT * FROM mrbs_users WHERE level in(0,1)";
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)) {
			$arr_name[$count] 		= $row['name'];
			$arr_UserName[$count] 	= $row['UserName'];
			$count = $count + 1;
		}
		
		//判斷aciton
		switch($action){
			case "insert":
			if(isset($_POST["submit"])){	
				//access all variables of $_POST
				foreach ($_POST as $key => $value) {
					//過濾特殊字元(')
					$$key=str_replace("'","\'",$value);
				}
				//update multiple records
				$start_time_am 	= [];				
				$end_time_pm 	= [];				
				$start_time_pm 	= [];				
				$end_time_pm 	= [];				
				$room_id = 3;
				$type="I";
				$conflict = 0;	
				$size = sizeof($id);
				$value_str_am = "";
				$value_str_pm = "";
				
				// check booking conflicts
				for($i=0;$i<$size;$i++){
					$start_time_am[$i]	= strtotime($booking_date[$i]." 08:00:00"); 
					$end_time_am[$i]	= strtotime($booking_date[$i]." 12:00:00"); 
					$start_time_pm[$i]	= strtotime($booking_date[$i]." 13:30:00"); 
					$end_time_pm[$i] 	= strtotime($booking_date[$i]." 17:30:00"); 
					$sql_am_conflict ="SELECT * FROM mrbs_entry WHERE room_id = ".$room_id."  AND ".$start_time_am[$i]." < end_time AND ".$end_time_am[$i]." > start_time";
					$sql_pm_conflict ="SELECT * FROM mrbs_entry WHERE room_id = ".$room_id."  AND ".$start_time_pm[$i]." < end_time AND ".$end_time_pm[$i]." > start_time";
					$result_am_conflict = mysqli_query($conn,$sql_am_conflict);
					$result_pm_conflict = mysqli_query($conn,$sql_pm_conflict);
					$rowcount_am = mysqli_num_rows($result_am_conflict);
					$rowcount_pm = mysqli_num_rows($result_pm_conflict);
					if($rowcount_am !=0 || $rowcount_pm !=0 ){
						$conflict = 1;
						break;
					}
				}

				if($conflict == 0){
					$timestamp = date("Y-m-d H:i:s");
					for($i=0;$i<$size;$i++){
						if($i==$size-1){   
							$value_str_am = $value_str_am."('".$start_time_am[$i]."','".$end_time_am[$i]."','".$room_id."','".$timestamp."','".$arr_name[$create_by_am[$i]]."','".$arr_UserName[$create_by_am[$i]]."','".$type."','".$description[$i]."')";
							$value_str_pm = $value_str_pm."('".$start_time_pm[$i]."','".$end_time_pm[$i]."','".$room_id."','".$timestamp."','".$arr_name[$create_by_pm[$i]]."','".$arr_UserName[$create_by_pm[$i]]."','".$type."','".$description[$i]."')";
						}else{   						  
							$value_str_am = $value_str_am."('".$start_time_am[$i]."','".$end_time_am[$i]."','".$room_id."','".$timestamp."','".$arr_name[$create_by_am[$i]]."','".$arr_UserName[$create_by_am[$i]]."','".$type."','".$description[$i]."'),";
							$value_str_pm = $value_str_pm."('".$start_time_pm[$i]."','".$end_time_pm[$i]."','".$room_id."','".$timestamp."','".$arr_name[$create_by_pm[$i]]."','".$arr_UserName[$create_by_pm[$i]]."','".$type."','".$description[$i]."'),";
						}
					}
					$sql_am = "INSERT INTO mrbs_entry (start_time,end_time,room_id,timestamp,create_by,name,type,description) VALUES ".$value_str_am; 	
					$sql_pm = "INSERT INTO mrbs_entry (start_time,end_time,room_id,timestamp,create_by,name,type,description) VALUES ".$value_str_pm; 	
					echo $sql_am."<br>";
					echo $sql_pm."<br>";
					mysqli_query($conn,$sql_am);
					mysqli_query($conn,$sql_pm);
					
					$_SESSION["msg"] = "預約成功";
				}else{
					$_SESSION["msg"] = "預約衝突";
					//phpAlert("預約衝突");
				}
				$conn->close();
			}else{
				$_SESSION["msg"] = "沒有輸入";
				//phpAlert("沒有輸入");
			}
			phpLocateURL("index.php");
		break;
		case "edit":
	?>
	
	
		<form id="reg-form" action="entry_month_insert.php?action=insert" method="post" enctype="multipart/form-data" >
			<div class="register">
			<?php
			    $thismonth = new DateTime($year."-".$month."-01");
				$day_ini	= $thismonth->modify("first day of this month")->format("j");
				$day_end	= $thismonth->modify("last day of this month")->format("j");
				$month_text = $thismonth->format("m");
				list($prev_year,$next_year,$prev_month,$next_month) = get_Year_Month($year,$month);
				echo "<p>";
				echo "<a class='item' href='?action=edit&year=".$prev_year."&month=".$prev_month."'> ← </a>";
					echo $year."年".$month."月";
				echo "<a class='item' href='?action=edit&year=".$next_year."&month=".$next_month."'> → </a>";
				echo "</p>";

				echo "<input type='checkbox' name='checkAll' id='checkAll' checked>全選";
				echo "<table style='text-align:left'>";	
					echo "<colgroup>";
						echo "<col width='5%'>";
						echo "<col width='10%'>";
						echo "<col width='10%'>";
						echo "<col width='10%'>";
						echo "<col width='15%'>";
						echo "<col width='25%'>";
					echo "</colgroup>";
					echo "<thead>";
					echo "<tr>";
						echo "<th>選取</th>";
						echo "<th>預約日期</th>";
						echo "<th>會議室</th>";
						echo "<th>上午值班</th>";
						echo "<th>下午值班</th>";
						echo "<th>描述</th>";
					echo "</tr>";
					echo "</thead>";	
					echo "<tbody>";
					for($i=$day_ini;$i<=$day_end;$i++){
						echo "<tr>"; 
							echo "<td><input type='checkbox' name='id[]' class='box' value='".$row['id']."' checked></td>";
							if($i<10){
								echo "<td>".$year."-".$month_text."-0".$i."</td>";
								echo "<input type='hidden' name='booking_date[]' class='flip' value='".$year."-".$month_text."-0".$i."'>";
							}else{ 	  
								echo "<td>".$year."-".$month_text,"-".$i."</td>";	
								echo "<input type='hidden' name='booking_date[]' class='flip' value='".$year."-".$month_text."-".$i."'>";
							}
							echo "<td>Call Center</td>";
							echo "<td>";
								echo "<select name='create_by_am[]' class='flip' required>";
									echo "<option value='' selected>請選擇</option>";
							    		for($j=0;$j<sizeof($arr_name);$j++){
										echo "<option value='".$j."'>".$arr_UserName[$j]."</option>";
									}
								echo "</select>";
							echo "</td>";
							echo "<td>";
								echo "<select name='create_by_pm[]' class='flip' required>";
									echo "<option value='' selected>請選擇</option>";
							    		for($j=0;$j<sizeof($arr_name);$j++){
										echo "<option value='".$j."'>".$arr_UserName[$j]."</option>";
									}
								echo "</select>";
							echo "</td>";
							echo "<td>";
							echo "<INPUT type='text' name='description[]' class='flip' value='友善平台值班' size='' style='max-width:100%' required />";
							echo "</td>";	
						echo "</tr>";
					}
					echo "</tbody>";
				echo "</table>";
                                            
				?>
				</p>
				
				<input type="submit" value="送出" name="submit">
				<button type="button" onclick="javascript:location.href='index.php'" >回上一頁</button>
			
			</div>
			
		</form>
	<?php
		$conn->close();
		break;
	}
	
    ?>
		</div> <!-- End of segment-->				
		<button class="ui sdc-blue fluid large button" type="submit"><a href="https://sdc-mrbs.tainan.gov.tw/month.php?room=3" target="_blank">SDC-MRBS System</a></button>
	  </div>
	</div>
	</body>
</html>
