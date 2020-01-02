<?php 
$url = preg_replace("/ /", "%20", "https://sdc-mrbs.tainan.gov.tw/");
$homepage = file_get_contents($url);
echo $homepage;
?>
