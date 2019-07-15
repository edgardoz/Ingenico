<?
$row = array();
$EDIT = (isset($_REQUEST['EDIT'])) ? trim($_REQUEST['EDIT']) : "";
if ($EDIT!="") {
    $INGENICO = true;
    include_once "db.php";

    $link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
    mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

    $query = "SELECT * FROM RECORDS WHERE UID = '{$EDIT}'";
    $retVal = mysql_query($query,$link) or die('Error query:  '.$query);
    $iCount = (int)mysql_num_rows($retVal);

    if ($iCount==1) {
        $row = mysql_fetch_array($retVal);
        if ((int)$row['STATUS']==1) {
            unset($row);
        } else {
            extract($row);
            $UPDATE = $EDIT;
            if (isset($row['card-exp'])) {
                $exp = explode("/",$row['card-exp']);
                $expMM = $exp[0];
                $expYY = $exp[1];
            }
        }        
        //print "<pre>";print_r($row);print "</pre>";
    }
}
?>
