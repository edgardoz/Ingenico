<?
/*
 * Revised: Mar 27, 2011
 */
include_once "authenticate.php";
if (isset($isOk) && $isOk) {
    $current = "settings"; 
    print "<h2>Settings</h2>";

    $link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
    mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

    if (isset($_POST)&&isset($_POST['POST'])&&$_POST['POST']=="POST") {
        $days_before = (isset($_POST['days_before'])&&!empty($_POST['days_before'])) ? $_POST['days_before'] : "4";
        $notify_email = (isset($_POST['notify_email'])&&!empty($_POST['notify_email'])) ? $_POST['notify_email'] : "";

        $query = "
            UPDATE SETTINGS SET 
                days_before='{$days_before}', 
                notify_email='{$notify_email}'
            WHERE id='1'
        ";
        //print "query : {$query}";
        $retVal = mysql_query($query,$link) or die('Error query:  '.$query);
        if (((int)$retVal==1)) {
            print "<h3>Settings saved</h3>";
        } else {
            print "<h3>Error saving settings</h3>";
        }
    }

    $query = "SELECT * FROM SETTINGS WHERE id='1'";
    $retVal = mysql_query($query,$link) or die('Error query:  '.$query);
    $iCount = (int)mysql_num_rows($retVal);
    if ($iCount==1) {
        $row = mysql_fetch_assoc($retVal);
        ?>
        <div id='settingsWrap'>
            <form method="post">
                <input type='hidden' name='POST' value='POST'>
                <div>
                    Process records <input type='text' name='days_before' value='<? print $row['days_before'] ?>' style='width:25px'> days before arrival
                </div>
                <div>
                    Send daily report to <input type='text' name='notify_email' value='<? print $row['notify_email'] ?>' style='width:300px'>
                </div>
                <p><input type="submit" value="Save"></p>
            </form>
        </div>
        <?
    }
}
include_once "close.php";
?>
