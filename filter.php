<?
/*
 * Revised: Apr 01, 2011
 *          Feb 10, 2014
 *          Jul 11, 2017
 */

include_once "fns.php";

$EXPORT = (isset($_REQUEST['EXPORT'])) ? (int)$_REQUEST['EXPORT'] : 0;

$todayDate = date("Y-m-d");// current date
$pageNo = (isset($_REQUEST['pageNo']) && (int)$_REQUEST['pageNo']!=0) ? (int)$_REQUEST['pageNo'] : 1;
$RES_ID = (isset($_REQUEST['RES_ID'])) ? trim($_REQUEST['RES_ID']) : "";
$STATUS = (isset($_REQUEST['STATUS'])) ? trim($_REQUEST['STATUS']) : "";
$GUEST_NAME = (isset($_REQUEST['GUEST_NAME'])) ? trim(addslashes($_REQUEST['GUEST_NAME'])) : "";
$CHECK_IN = (isset($_REQUEST['CHECK_IN'])) ? trim($_REQUEST['CHECK_IN']) : "";//date("Y-m-j");
$CHECK_OUT = (isset($_REQUEST['CHECK_OUT'])) ? trim($_REQUEST['CHECK_OUT']) : "";//date("Y-m-j");
$DATE_FIELD = (isset($_REQUEST['DATE_FIELD'])) ? $_REQUEST['DATE_FIELD'] : "CREATED";
$FROM = (isset($_REQUEST['FROM'])) ? trim($_REQUEST['FROM']) : date('Y-m-j', strtotime(date("Y-m-j", strtotime($todayDate)) . " -1 day"));
$TO = (isset($_REQUEST['TO'])) ? trim($_REQUEST['TO']) : date("Y-m-j");
$publisher_name = (isset($_REQUEST['publisher_name'])) ? trim($_REQUEST['publisher_name']) : "";
$card_number = (isset($_REQUEST['card_number'])) ? trim($_REQUEST['card_number']) : "";
$billing_email = (isset($_REQUEST['billing_email'])) ? trim($_REQUEST['billing_email']) : "";
$orderID = (isset($_REQUEST['orderID'])) ? trim($_REQUEST['orderID']) : "";
$sortBy = (isset($_COOKIE['sortBy'])) ? trim($_COOKIE['sortBy']) : "CREATED";
$sortOrd = (isset($_COOKIE['sortOrd'])) ? (int)trim($_COOKIE['sortOrd']) : -1;

$arg = array(
    "RES_ID"=>$RES_ID,
    "STATUS"=>$STATUS,
    "GUEST_NAME"=>$GUEST_NAME,
    "CHECK_IN"=>$CHECK_IN,
    "CHECK_OUT"=>$CHECK_OUT,
    "DATE_FIELD"=>$DATE_FIELD,
    "FROM"=>$FROM,
    "TO"=>$TO,
    "publisher_name"=>$publisher_name,
    "card_number"=>$card_number,
    "billing_email"=>$billing_email,
    "orderID"=>$orderID,
    "EXPORT"=>$EXPORT,
    "sortBy"=>$sortBy,
    "sortOrd"=>$sortOrd
);

$arg['count']=1;
$return = getAll($arg);
$retVal = $return["retVal"];
$row = mysql_fetch_assoc($retVal);

$itemsPerPage = 20;
$startItem = 0;
$totalItems = (int)$row['TOTAL'];

$arg['count']=0;
$pagination = "";
$noPages = ceil($totalItems / $itemsPerPage);
$startItem = abs($pageNo-1) * $itemsPerPage;
$pagination = pages($totalItems, $pageNo, $noPages, $startItem);

$arg['startItem']=$startItem;
$arg['itemsPerPage']=$itemsPerPage;

$arg['count']=-1;
$return = getAll($arg);
$retVal = $return["retVal"];
$iCount = $return["iCount"];
$cnt=$startItem+1;

if (!$EXPORT) {
    ?>
		<h2>Ingenico</h2>
    <input type="hidden" id="QUERY_STRING" value="<? print urlencode($_SERVER["QUERY_STRING"]) ?>">
    <form id="myFrm" method="get">
        <input type="hidden" id="EXPORT" name="EXPORT" value="0">
        <div id='filter'>
            <div class='criteria'>
                <select name="publisher_name" id="publisher_name">
                    <option value="" <? if ($publisher_name=="") print "selected" ?>>All Properties</option>
                    <option value="excellence" <? if ($publisher_name=="excellence") print "selected" ?>>Punta Cana</option>
                    <option value="excellence2" <? if ($publisher_name=="excellence2") print "selected" ?>>Riviera Cancun</option>
                    <option value="excellence3" <? if ($publisher_name=="excellence3") print "selected" ?>>Playa Mujeres</option>
                    <option value="excellence4" <? if ($publisher_name=="excellence4") print "selected" ?>>Beloved</option>
                    <option value="excellence1" <? if ($publisher_name=="excellence1") print "selected" ?>>Finest</option>
                    <option value="excellence5" <? if ($publisher_name=="excellence5") print "selected" ?>>El Carmen</option>
                    <option value="excellence6" <? if ($publisher_name=="excellence6") print "selected" ?>>Oyster Bay</option>
                </select>
            </div>
            <div class='criteria'>
                <select name="STATUS" id="STATUS">
                    <option value="" <? if ($STATUS=="") print "selected" ?>>All Transactions</option>
                    <option value="0" <? if ($STATUS=="0") print "selected" ?>>Pending</option>
                    <option value="1" <? if ($STATUS=="1") print "selected" ?>>Charged</option>
                    <option value="2" <? if ($STATUS=="2") print "selected" ?>>Cancelled</option>
                    <option value="-1" <? if ($STATUS=="-1") print "selected" ?>>Failed</option>
                    <option value="-99" <? if ($STATUS=="-99") print "selected" ?>>On Going</option>
                </select>
            </div>
            <div class='criteria'>
                and 
                <select name="DATE_FIELD" id="DATE_FIELD">
                    <option value="CREATED" <? if ($DATE_FIELD=="CREATED") print "selected" ?>>Booked</option>
                    <option value="auth_date" <? if ($DATE_FIELD=="auth_date") print "selected" ?>>Charged</option>
                    <option value="CHECK_IN" <? if ($DATE_FIELD=="CHECK_IN") print "selected" ?>>Check-in</option>
                    <option value="CHECK_OUT" <? if ($DATE_FIELD=="CHECK_OUT") print "selected" ?>>Check-out</option>
                    <option value="CANCELLED" <? if ($DATE_FIELD=="CANCELLED") print "selected" ?>>Cancelled</option>
                </select>
            </div>
            <div class='criteria'>on or from <input type="text" name="FROM" id="FROM" class="date-pick" value="<? print $FROM ?>"><div style='clear:both'></div></div>
            <div class='criteria'>to <input type="text" name="TO" id="TO" class="date-pick" value="<? print $TO ?>"><div style='clear:both'></div></div>
            <div class='criteria' style="padding-left:50px">Reservation: <input type="text" name="RES_ID" id="RES_ID" style="width:100px" value="<? print $RES_ID ?>"></div>
            <div class='criteria'>Order Id: <input type="text" name="orderID" name="orderID" style="width:100px" value="<? print $orderID ?>"></div>
            <div class='criteria'>Guest name: <input type="text" name="GUEST_NAME" id="GUEST_NAME" style="width:100px" value="<? print $GUEST_NAME ?>"></div>
            <div class='criteria'><input type="submit" value="Get Report &#155;" onClick="$('#EXPORT').val('0')"></div>
            <div class='criteria'><input type="submit" value="Export to Excel &#155;" onClick="$('#EXPORT').val('1')"></div>
            <div class='criteria'><input type="button" value="Clear" onClick="frmClear()"></div>
            <div style='clear:both'></div>
        </div>
    </form>
    <script>
    $(function() {
        $('.date-pick').datePicker({clickInput:true,startDate:'1996-01-01'});
    });
    </script>
    <? 
    print "<div id='pages'>{$pagination}</div>"; 
}
?>