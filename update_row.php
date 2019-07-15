<?php
$TOKEN = $_GET['token'];
$UID = $_GET['uid'];
$CAMPO = $_GET['campo'];
$DB_SERVER = "localhost";
$DB_USER = "juanccpsingenico";
$DB_PWD = "ijV1u!04";
$DB_NAME = "stg-ccps-ingenico";

$result = "Unknown Transaction.";
$link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');
$query = "UPDATE RECORDS SET ".$CAMPO."='".$TOKEN."' WHERE UID='".$UID."'";
//echo $query;

$return = mysql_query($query,$link) or die('Error query:  '.$query);
echo $return;
?>