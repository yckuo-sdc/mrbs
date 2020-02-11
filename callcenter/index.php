<?php session_start();
	require_once("function.php");
	verifyBySession("account");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>會議室預約系統</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Archivo+Narrow:400,700" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="../images/logo.ico" />

<!-- add jQuery-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<!-- semantic ui -->
<link rel="stylesheet" type="text/css" href="semantic/semantic.css">
<script src="semantic/semantic.js"></script>

<!-- add my JS-->
<script src="js/index.js"></script>

<!-- add my CSS-->
<link rel="stylesheet" href="css/index.css">

<!--[if IE 6]>
<link href="default_ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body>
	<a href="index.php">
	<div id ="header">
		<div class="title">TainanGov SDC 會議室預約系統</div>
	</div>
	</a>
	<?php
	//-------------function-------------//
	require_once("mysql_connect.inc.php");	

	// 取得url參數
	if (!isset($_GET["action"]))	$action = "other";
	else							$action = $_GET["action"];

	switch($action){
		case "other":
	?>

	<div class="ui middle aligned center aligned grid">
	  <div class="column">
			<h2 class="ui sdc-blue image header">
			  <div class="content"><i class="calendar check outline icon"></i>CallCenter Booking</div>
			</h2>
		<div class="ui stacked segment">
			<button name="insert_day"  class="ui button" onclick="javascript:location.href='entry_insert.php?action=edit'" >新增單筆</button>
			<button name="insert_month" class="ui button" onclick="javascript:location.href='entry_month_insert.php?action=edit'" >新增整月</button><p></p>
			 <form class="ui form" action="index.php?action=del" method="post" enctype="multipart/form-data" onsubmit="return confirm('是否刪除選取紀錄?');">
			<button name="submit" type="submit" class="fluid ui button">刪除</button><p></p>
			<div class="record_content">
			<?php 
                    //------------pagination----------//
                    $pages=" ";
                    if (!isset($_GET['page'])){ 
                        $pages = 1; 
                    }else{
                        $pages = $_GET['page']; 
                    }
                    
                    //select row_number,and other field value
                    $sql = "SELECT a.*,b.room_name FROM mrbs_entry AS a,mrbs_room AS b WHERE a.room_id = 3 AND a.room_id = b.id ORDER by a.start_time desc";
                        
                    $result = mysqli_query($conn,$sql);
                    $rowcount = mysqli_num_rows($result);
                                
                    $per = 10; 		
                    $max_pages = 10;
                    $Totalpages = ceil($rowcount / $per); 
                    $lower_bound = ($pages <= $max_pages) ? 1 : $pages - $max_pages + 1;
                    $upper_bound = ($pages <= $max_pages) ? min($max_pages,$Totalpages) : $pages;					
                    $start = ($pages -1)*$per; //計算資料庫取資料範圍的開始值。
                    if($pages == 1)					$offset = ($rowcount < $per) ? $rowcount : $per;
                    elseif($pages == $Totalpages)	$offset = $rowcount - $start;
                    else							$offset = $per;
                                
                    $prev_page = ($pages > 1) ? $pages -1 : 1;
                    $next_page = ($pages < $Totalpages) ? $pages +1 : $Totalpages;	
                    $sql_subpage = $sql." limit ".$start.",".$offset;
                                
                    $result = mysqli_query($conn,$sql_subpage);
                                        
                    if($rowcount==0){
                        echo "查無此筆紀錄";
                    }else{
                        echo "共有".$rowcount."筆資料！";

					echo "<table style='text-align:left'>";	
						echo "<colgroup>";
							echo "<col width='5%'>";
							echo "<col width='10%'>";
							echo "<col width='10%'>";
							echo "<col width='10%'>";
							echo "<col width='10%'>";
							echo "<col width='15%'>";
							echo "<col width='25%'>";
							echo "<col width='5%'>";
							echo "<col width='5%'>";
						echo "</colgroup>";
						echo "<thead>";
						echo "<tr>";
							echo "<th>選取</th>";
							echo "<th>會議日期</th>";
							echo "<th>會議時間</th>";
							echo "<th>會議室</th>";
							echo "<th>預約人</th>";
							echo "<th>姓名</th>";
							echo "<th>描述</th>";
							echo "<th colspan='2'></th>";
						echo "</tr>";
						echo "</thead>";	
						echo "<tbody>";
						while($row = mysqli_fetch_assoc($result)) {
							echo "<tr>"; 
								echo "<td><input type='checkbox' name='id[]' value='".$row['id']."'></td>";
								echo "<td>".date('Y-m-d',$row['start_time'])."</td>";
								echo "<td>";
									if(date('H:i:s',$row['start_time'])=="08:00:00") echo "上午";
									else											 echo  "下午";
								echo "</td>";
								echo "<td>".$row['room_name']."</td>";
								echo "<td>".$row['create_by']."</td>";
								echo "<td>".$row['name']."</td>";
								echo "<td>".$row['description']."</td>";
								echo "<td class='del' title='删除' onclick='confirm_func(".$row['id'].",".$pages.")'><a href='#'>刪除</a></td>";
								echo "<td class='edit' title='编辑'><a href='entry_edit.php?action=edit&id=".$row['id']."&page=".$pages."'>編輯</a></td>"; 
							echo "</tr>";
						}
						echo "</tbody>";
					echo "</table>";
                                            
                    //The href-link of bottom pages
                    echo "<a class='item' href='?page=1'>首頁</a>";
                    echo "<a class='item' href='?page=".$prev_page."'> ← </a>";
                    for ($j = $lower_bound; $j <= $upper_bound ;$j++){
                        if($j == $pages){
                            echo"<a class='active item bold' href='?page=".$j."'>".$j."</a>";
                        }else{
                            echo"<a class='item' href='?page=".$j."'>".$j."</a>";
                        }
                    }
                    echo"<a class='item' href='?page=".$next_page."'> → </a>";		
                    //last page
                    echo"<a class='item test' href='?page=".$Totalpages."'>末頁</a>";
				   
					}
                    $conn->close();
                        
                ?>
				</div> <!-- End of record_content-->				
			</form>
			</div> <!-- End of segment-->				
			<?php
				if(isset($_SESSION["msg"])){
				echo "<div class='ui error message' style='display:block'>";
					$msg = $_SESSION["msg"];	
					echo "<span>$msg</span>";
				echo "</div>";
				}
			?>

				<button class="ui sdc-blue fluid large button" type="submit"><a href="https://sdc-mrbs.tainan.gov.tw/month.php?room=3" target="_blank">SDC-MRBS System</a></button>
				<input type="hidden" name="refer" value="<?php echo (isset($_GET['refer'])) ? $_GET['refer'] : 'login.php'; ?>">
				
		<!--<div class="ui message">
			SDC-MRBS System
		 </div>-->
	  </div>
	</div>
	<?php
			unset($_SESSION["msg"]); 	
			break;
		case "del":
			if(empty($_POST['id'])){
				$_SESSION["msg"] = "沒有輸入";
			}else{			
				$delete_id = $_POST['id'];
				$delete_id_arr = "";
				for($i=0;$i<sizeof($delete_id);$i++){
					if($i == 0){
						$delete_id_arr = $delete_id_arr.$delete_id[$i];
					}else{
						$delete_id_arr = $delete_id_arr.",".$delete_id[$i];
					}
				}	
				$sql = "DELETE FROM mrbs_entry WHERE id IN (".$delete_id_arr.")";		
				mysqli_query($conn,$sql);
				$_SESSION["msg"] = "刪除成功";
			}
			phpLocateURL("index.php");
			break;
	}
	?>
</body>
</html>
