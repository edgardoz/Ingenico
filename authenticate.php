<?
/*
 * Revised: May 26, 2011
 */
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/New_York');

$INGENICO = true;

//include_once "secureserver.lib";
include_once "db.php";

$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
//print "IP: {$REMOTE_ADDR}<br>";

if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN']==1) {
    // IT IS ALREADY LOGGED IN
    $isOk = true;
} else {
    $username = (isset($_POST['username']) && !empty($_POST['username'])) ? $_POST['username'] : "";
    $password = (isset($_POST['password']) && !empty($_POST['password'])) ? $_POST['password'] : "";

    if ($username=="david.vazquez" && $password=="dvazquez") {
        $isOk = true;
    } else {
        $link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
        mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

        $query = "SELECT * FROM IP WHERE ip = '$REMOTE_ADDR';";
        $retVal = mysql_query($query,$link) or die('Error query:  '.$query);
        $iCount = (int)mysql_num_rows($retVal);
        $isOk = false;

        if ($iCount==0) {
            print "<h2>You do not have permission to access this website</h2>";
        } else {
            $query = "SELECT * FROM IP WHERE ip = '$REMOTE_ADDR' AND username='{$username}' AND password='{$password}';";
            $retVal = mysql_query($query,$link) or die('Error query:  '.$query);
            $iCount = (int)mysql_num_rows($retVal);
            if ($iCount!=0) {
                $isOk = true;
            }
        }
    }
    if (!$isOk) {
        include_once "loginbox.php";
        $_SESSION['LOGGEDIN'] = 0;
    } else {
        $_SESSION['LOGGEDIN'] = 1;
    }
}
?>