<?
/*
 * Revised: Feb 10, 2014
 */
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/New_York');

include_once "db.php";
include_once "fns.php";

$RES_ID = (isset($_REQUEST['RES_ID'])) ? trim($_REQUEST['RES_ID']) : "";
$sortBy = (isset($_REQUEST['sortBy'])) ? trim($_REQUEST['sortBy']) : "";

$arg = array(
    "RES_ID"=>$RES_ID,
    "sortBy"=>$sortBy,
);

$return = getAll($arg);
$retVal = $return["retVal"];

$row = mysql_fetch_assoc($retVal);

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Content-Type:application/json");

print json_encode($row);

?>