<?
function getAll($arg) {
    extract($arg);

    /*
    global $DB_SERVER;
    global $DB_USER;
    global $DB_PWD;
    global $DB_NAME;
    */
    //+020519
    $DB_SERVER = "localhost";
    $DB_USER = "juanccpsingenico";
    $DB_PWD = "ijV1u!04";
    $DB_NAME = "stg-ccps-ingenico";
    //-020519

    $count = isset($count) ? $count : 0;
    $FROM = isset($FROM) ? $FROM : "";
    $TO = isset($TO) ? $TO : "";
    $sortBy = isset($sortBy) ? $sortBy : "";
    $sortOrd = isset($sortOrd) ? $sortOrd : "";
    $result = "Unknown Transaction.";
    $link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
    mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

    if ($count==1) {
        $query = "SELECT count(1) AS TOTAL ";
    } else {
        $query = "SELECT * ";
    }

    $query .= "FROM RECORDS";

    $WHERE = array();
    if (isset($publisher_name) && $publisher_name!="") array_push($WHERE, "`publisher-name`='{$publisher_name}'");
    if (isset($STATUS) && $STATUS!="") array_push($WHERE, "STATUS='{$STATUS}'");
    if (isset($RES_ID) && $RES_ID!="") array_push($WHERE, "RES_ID='{$RES_ID}'");
    if (isset($orderID) && $orderID!="") array_push($WHERE, "orderID='{$orderID}'");
    if (isset($GUEST_NAME) && $GUEST_NAME!="") array_push($WHERE, "GUEST_NAME like '%{$GUEST_NAME}%'");

    if ($FROM=="" || $TO=="") {
        if ($FROM!="") array_push($WHERE, "({$DATE_FIELD} >= '{$FROM} 00:00:00' AND {$DATE_FIELD} <= '{$FROM} 23:59:59')");
        if ($TO!="") array_push($WHERE, "({$DATE_FIELD} >= '{$TO} 00:00:00' AND {$DATE_FIELD} <= '{$TO} 23:59:59')");
    } else {
        array_push($WHERE, "({$DATE_FIELD} >= '{$FROM} 00:00:00' AND {$DATE_FIELD} <= '{$TO} 23:59:59')");
    }

    if (count($WHERE)!=0) {
        $query .= " WHERE ".join(" AND ", $WHERE);
    }

    if ($sortBy!="") {
      $query .= " ORDER BY `{$sortBy}` ".($sortOrd==1?"ASC":"DESC");
      if ($sortBy!="RES_ID") $query .= ", RES_ID";
      if ($sortBy!="CREATED") $query .= ", CREATED DESC";
      if ($sortBy!="STATUS") $query .= ", STATUS";
    }

    if (isset($startItem) && isset($itemsPerPage) && (int)$EXPORT==0) $query .= " LIMIT $startItem, $itemsPerPage";

    //if ($count!=1) print "<div>{$query}</div>";

    $retVal = mysql_query($query,$link) or die('Error query:  '.$query);
    $iCount = (int)mysql_num_rows($retVal);

    return array(
        "retVal"=>$retVal,
        "iCount"=>$iCount
    );
}
function pages($totalItems, $pageNo, $noPages, $startItem, $fnc="changePage") {
    $retVal = "";

    $prevPage = $pageNo - 1;
    $nextPage = $pageNo + 1;

    if ($pageNo > 1 && $noPages != 2) $retVal .= "<SPAN class='pagSpan'><A HREF='javascript:void(0)' onClick='{$fnc}(1)'>&#171; FIRST</A></SPAN>";
    if ($prevPage > 0) $retVal .= "<SPAN class='pagSpan'><A HREF='javascript:void(0)' onClick='{$fnc}($prevPage)'>&#139; PREV</A></SPAN>"; 

    $retVal .= "<SPAN class='pagSpan'>Page $pageNo of $noPages, ".number_format($totalItems)." Records</SPAN>";

    if ($nextPage <= $noPages) $retVal .= "<SPAN class='pagSpan'><A HREF='javascript:void(0)' onClick='{$fnc}($nextPage)'>NEXT &#155;</A></SPAN>"; 
    if ($pageNo < $noPages && $noPages != 2) $retVal .= "<SPAN class='pagSpan'><A HREF='javascript:void(0)' onClick='{$fnc}($noPages)'>LAST &#187;</A></SPAN>";
        
    return $retVal;
}
?>