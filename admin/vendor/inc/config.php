 <?php
// Set timezone to Indian Standard Time (IST) - Himachal Pradesh
date_default_timezone_set('Asia/Kolkata');

$dbuser="root";
$dbpass="";
$host="localhost";
$db="electrozot_db";
$mysqli=new mysqli($host,$dbuser, $dbpass, $db);
?>