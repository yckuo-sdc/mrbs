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
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
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
		  <div class="content"><i class="edit icon"></i>Edit Entry</div>
		</h2>
	<div class="ui stacked segment">
<?php
	header('Content-type: text/html; charset=utf-8');
	//-------------function-------------//
	require_once("mysql_connect.inc.php");	
	
	// 取得url參數
	$action = $_GET["action"];
	$id = $_GET["id"];
	if (!isset($_GET['page']))	$page = 1; 
	else						$page = $_GET['page']; 
			
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
                    
	$room_id = 3;

	
	
	//判斷aciton
	switch($action){
		case "update":
			if(isset($_POST["submit"])){	
				//access all variables of $_POST
				foreach ($_POST as $key => $value){ 
					//過濾特殊字元(')
					$$key=str_replace("'","\'",$value);
				}
				if($am_or_pm == "am"){
					$start_time = strtotime($booking_date." 08:00:00"); 
					$end_time 	= strtotime($booking_date." 12:00:00"); 
				}
				else{
					$start_time = strtotime($booking_date." 13:30:00"); 
					$end_time 	= strtotime($booking_date." 17:30:00"); 
				}
				$timestamp 	= date("Y-m-d H:i:s");
				$type		="I";
				$sql_conflict ="SELECT * FROM mrbs_entry WHERE id NOT IN(".$id.") AND room_id = ".$room_id."  AND ".$start_time." < end_time AND ".$end_time." > start_time";

				$result_conflict = mysqli_query($conn,$sql_conflict);
                $rowcount = mysqli_num_rows($result_conflict);
				//echo  $rowcount."<br>";
                
				// check booking conflicts
				if($rowcount == 0){
					$sql= "UPDATE mrbs_entry SET room_id=".$room_id." ,start_time='".$start_time."' ,end_time='".$end_time."' ,create_by='".$arr_name[$create_by]."' ,name='".$arr_UserName[$create_by]."', description='".$description."' Where id=".$id;
					mysqli_query($conn,$sql);
					$_SESSION["msg"] = "修改成功";
					//phpAlert("修改成功");
				}else{
					$_SESSION["msg"] = "修改衝突";
					//phpAlert("修改衝突");
				}
				$conn->close();
			}else{
				$_SESSION["msg"] = "沒有輸入";
				//phpAlert("沒有輸入");
			}
			phpLocateURL("index.php?page=".$page);
		break;
		
		case "del":
			$sql = "DELETE FROM mrbs_entry WHERE id=".$id;
			mysqli_query($conn,$sql);
			$_SESSION["msg"] = "刪除成功";
			//phpAlert("刪除成功");
			phpLocateURL("index.php?page=".$page);
		break;
		
		case "edit":
			$sql = "SELECT a.*,b.room_name FROM mrbs_entry AS a,mrbs_room AS b WHERE a.id =".$id." AND a.room_id = b.id";
			
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
			
			$room_id 	 	= $row['room_id'];
			$room_name 	 	= $row['room_name'];
			$create_by  	= $row['create_by'];
			$name 	 		= $row['name'];
			$description 	= $row['description'];
			$start_time 	= date('H:i:s',$row['start_time']);
			$end_time 		= date('H:i:s',$row['end_time']);
			$booking_date   = date('Y-m-d',$row['start_time']);
	        if($start_time =="08:00:00") $am_or_pm = "am";
			else						 $am_or_pm = "pm";
				
	//顯示編輯表單
	?>
			
		<form id="reg-form" action="entry_edit.php?action=update&id=<?php echo $id ?>&page=<?php echo $page?>" method="post" enctype="multipart/form-data">
			<div class="register">
				<p>
				<label>預約日期</label>
				<input type="date" name="booking_date" value="<?php echo $booking_date;?>" required>
				</p>

				<p>
				<label>預約時間</label>
				<input type="radio" name="am_or_pm" value="am" <?php if($am_or_pm == "am") echo "checked";?>> 上午
				<input type="radio" name="am_or_pm" value="pm" <?php if($am_or_pm == "pm") echo "checked";?>> 下午	
				</p>
				
				<p>
				<label> 預約會議室  </label>
				<select name="announce_type" required>
					<option value="<?php echo $room_id; ?>" selected='selected'><?php echo $room_name; ?></option>;
				</select>
				</p>
				
				<p>
				<label> 值班 </label>
				<select  name="create_by" required>
				<?php
					for($i=0;$i<sizeof($arr_name);$i++){
						if($create_by == $arr_name[$i]){
							echo "<option value='".$i."' selected='selected'>".$arr_UserName[$i]."</option>";
						}
						else{
							echo "<option value='".$i."'>".$arr_UserName[$i]."</option>";
						}
					}						
				?>
				</select>  
				</p>
				
				<p>
				<label> 描述 </label>
				<input type="text" name="description" value="<?php echo $description; ?>" size="50" style="max-width:100%" required />	
				</p>
			
				<input type="submit" value="修改" name="submit" class="button">
				
				<button type="button" onclick="javascript:location.href='index.php?page=<?php echo $page;?>'" >回上一頁</button>
				
				
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
<body>
</html>
	
