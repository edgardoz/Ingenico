<?
/*
 * Revised: Jan 19, 2013
 *          Jul 11, 2017
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/New_York');

$current = "report"; 
include_once "authenticate.php";
if (isset($isOk) && $isOk) {
    $current = "report";
    $cnt = 1;
    $cls = 1;
    $output = "";
    include_once "filter.php";

    if ($iCount!=0) {
        $output = "<table id='reportTbl'>
                    <tr class='hdr'>
                        <td valign='top'>&nbsp;</td>
                        <td valign='top'><a onClick=\"sortBy('RES_ID')\" href='javascript:void(0)'>Reservation</a></td>
                        <td valign='top'><a onClick=\"sortBy('CREATED')\" href='javascript:void(0)'>Booked</a></td>
                        <td valign='top'><a onClick=\"sortBy('CHECK_IN')\" href='javascript:void(0)'>Check In</a></td>
                        <td valign='top'><a onClick=\"sortBy('CHECK_OUT')\" href='javascript:void(0)'>Check Out</a></td>
                        <td valign='top'><a onClick=\"sortBy('auth_date')\" href='javascript:void(0)'>Charged</a></td>
                        <td valign='top'><a onClick=\"sortBy('STATUS')\" href='javascript:void(0)'>Status</a></td>
                        <td valign='top'><a onClick=\"sortBy('CANCELLED')\" href='javascript:void(0)'>Cancelled</a></td>
                        <td valign='top'><a onClick=\"sortBy('orderID')\" href='javascript:void(0)'>Order Id</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('GUEST_NAME')\" href='javascript:void(0)'>Guest Name</a></td>
                        <td valign='top'><a onClick=\"sortBy('ROOMS')\" href='javascript:void(0)'>Rooms</a></td>
                        <td valign='top'><a onClick=\"sortBy('GUESTS')\" href='javascript:void(0)'>Guests</a></td>
                        <td valign='top'><a href='javascript:void(0)'>Nights</a></td>
                        <td valign='top'><a onClick=\"sortBy('publisher-name')\" href='javascript:void(0)'>Property</a></td>
												<td valign='top' nowrap><a onClick=\"sortBy('INGENICO_TOKEN')\" href='javascript:void(0)'>Ingenico Token</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-name')\" href='javascript:void(0)'>Card Name</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-address1')\" href='javascript:void(0)'>Card Address</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-city')\" href='javascript:void(0)'>Card City</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-state')\" href='javascript:void(0)'>Card State</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-zip')\" href='javascript:void(0)'>Card Zip</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-country')\" href='javascript:void(0)'>Card Country</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-type')\" href='javascript:void(0)'>Card Type</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-exp')\" href='javascript:void(0)'>Card Exp</a></td>
                        <td valign='top' nowrap><a onClick=\"sortBy('card-amount')\" href='javascript:void(0)'>Card Amount</a></td>
                        <td valign='top'>Messages</td>
                        ".($EXPORT?"<td valign='top' nowrap>Final Status</td>":"")."
                        ".($EXPORT?"<td valign='top' nowrap>Err. Message</td>":"")."
                        <!-- +290319 -->
                        <td valign='top'>Ingenico</td>
                        
                    </tr>        
        ";

        //while ($row = mysql_fetch_assoc($retVal)) {
        while ($row = mysql_fetch_array($retVal)) {
            //+290319
            $json_row=json_encode($row);
            extract($row);
            
            
            $publisher_name = "Undef";
            if ($row['publisher-name'] == "excellence" ) { $publisher_name = "XPC"; }
            if ($row['publisher-name'] == "excellence2" ) { $publisher_name = "XRC"; }
            if ($row['publisher-name'] == "excellence3" ) { $publisher_name = "XPM"; }
            if ($row['publisher-name'] == "excellence4" ) { $publisher_name = "LaAm"; }
            if ($row['publisher-name'] == "excellence1" ) { $publisher_name = "FPM"; }
            if ($row['publisher-name'] == "excellence5" ) { $publisher_name = "XEC"; }
            if ($row['publisher-name'] == "excellence6" ) { $publisher_name = "XOB"; }
            $STATUS = "";
            if ($row['STATUS'] == "0" ) { $STATUS = "<span class='pending'>Pending</span>"; }
            if ($row['STATUS'] == "1" ) { $STATUS = "<span class='charged'>Charged</span>"; }
            if ($row['STATUS'] == "2" ) { $STATUS = "<span class='cancelled'>Cancelled</span>"; }
            if ($row['STATUS'] == "-1" ) { $STATUS = "<span class='failed'>Failed</span>"; }
            if ($row['STATUS'] == "-99" ) { $STATUS = "<span class='failed'>On Going</span>"; }
            $NIGHTS = dateDiff(substr($row['CHECK_IN'], 0, 10),substr($row['CHECK_OUT'], 0, 10));
            $output .= "
                <tr class='color{$cls} itemRow' rel='{$row['UID']}' status='{$row['STATUS']}'>
                    <td valign='top'>{$cnt}</td>
                    <td valign='top'>{$row['RES_ID']}</td>
                    <td valign='top'>{$row['CREATED']}</td>
                    <td valign='top'>".substr($row['CHECK_IN'], 0, 10)."</td>
                    <td valign='top'>".substr($row['CHECK_OUT'], 0, 10)."</td>
                    <td valign='top'>".substr($row['auth_date'], 0, 10)."</td>
                    <td valign='top'>{$STATUS}</td>
                    <td valign='top'>{$row['CANCELLED']}</td>
                    <td valign='top'>{$row['orderID']}</td>
                    <td valign='top'>{$row['GUEST_NAME']}</td>
                    <td valign='top'>{$row['ROOMS']}</td>
                    <td valign='top'>{$row['GUESTS']}</td>
                    <td valign='top'>{$NIGHTS}</td>
                    <td valign='top'>{$publisher_name}</td>
                    <td valign='top'>{$row['INGENICO_TOKEN']}</td>
                    <td valign='top'>{$row['card-name']}</td>
                    <td valign='top'>{$row['card-address1']}</td>
                    <td valign='top'>{$row['card-city']}</td>
                    <td valign='top'>{$row['card-state']}</td>
                    <td valign='top'>{$row['card-zip']}</td>
                    <td valign='top'>{$row['card-country']}</td>
                    <td valign='top'>{$row['card-type']}</td>
                    <td valign='top'>{$row['card-exp']}</td>
                    <td valign='top'>".(($EXPORT)?$row['card-amount']:number_format($row['card-amount']))."</td>
                    <td valign='top'>{$MSG}</td>
                    ".($EXPORT?"<td valign='top' nowrap>{$FinalStatus}</td>":"")."
                    ".($EXPORT?"<td valign='top' nowrap>{$MErrMsg}</td>":"")."
                    <!-- +290319 -->
                    <td valign='top' >";
                    if($row['INGENICO_RESULT']!=""){
                        $output .="<a href='javascript:;' class='button' onclick='lanzador({$json_row});' role='button'>Open</a>";
                    }
                    $output .= "</td>
                    
                </tr>
            ";
            $cls *= -1;
            ++$cnt;
        }    
        $output .= "
            </table>
            <script>
                initEditTbl();

            </script>
        ";

        if ($EXPORT) {
            header("Content-Disposition: attachment; filename=\"report.xls\"\n\n");
            header("Content-Type: application/vnd.ms-excel\n\n");
        }

        print $output;
        include_once "plantilla/lanzador.php";
    }
}
include_once "close.php";

function dateDiff($start_date,$end_date="now",$unit="D") {
    /*
    "Y" The number of complete years in the period. 
    "M" The number of complete months in the period. 
    "D" The number of complete days in the period. 
    "MD" The difference between the days in start_date and end_date. The months and years of the dates are ignored. 
    "YM" The difference between the months in start_date and end_date. The days and years of the dates are ignored. 
    "YD" The difference between the days in start_date and end_date. The years of the dates are ignored. 
    */

    //print "<p>start_date: $start_date -- end_date: $end_date</p>";

    $start_date .= " 01:01:01";
    $end_date .= " 01:01:01";

    $unit = strtoupper($unit);
    $start=strtotime($start_date.' GMT');
    if ($start === -1) {
        return "invalid start date";
    }
    
    $end=strtotime($end_date.' GMT');			
    if ($end === -1) {
        return "invalid end date";
    }
    
    if ($start > $end) {
        $temp = $start;
        $start = $end;
        $end = $temp;
    }
    
    $diff = $end-$start;

    //print "<p>diff: $diff -- $start -- $end</p>";
    
    $day1 = date("j", $start);
    $mon1 = date("n", $start);
    $year1 = date("Y", $start);
    $day2 = date("j", $end);
    $mon2 = date("n", $end);
    $year2 = date("Y", $end);
    
    switch($unit) {
        case "D":
    // 86400
            //return intval($diff/(24*60*60));
            return floor($diff/(24*60*60));
            break;
        case "M":
            if($day1>$day2) {
                $mdiff = (($year2-$year1)*12)+($mon2-$mon1-1);
            } else {
                $mdiff = (($year2-$year1)*12)+($mon2-$mon1);
            }
            return $mdiff;
            break;
        case "Y":
            if(($mon1>$mon2) || (($mon1==$mon2) && ($day1>$day2))){
                $ydiff = $year2-$year1-1;
            } else {
                $ydiff = $year2-$year1;
            }
            return $ydiff;
            break;
        case "YM":
            if($day1>$day2) {
                if($mon1>=$mon2) {
                    $ymdiff = 12+($mon2-$mon1-1);
                } else {
                    $ymdiff = $mon2-$mon1-1;
                }
            } else {
                if($mon1>$mon2) {
                    $ymdiff = 12+($mon2-$mon1);
                } else {
                    $ymdiff = $mon2-$mon1;
                }
            }
            return $ymdiff;
            break;
        case "YD":
            if(($mon1>$mon2) || (($mon1==$mon2) &&($day1>$day2))) {
                $yddiff = intval(($end - mktime(0, 0, 0, $mon1, $day1, $year2-1))/(24*60*60));						
            } else {
                $yddiff = intval(($end - mktime(0, 0, 0, $mon1, $day1, $year2))/(24*60*60));
            }
            return $yddiff;
            break;
        case "MD":
            if($day1>$day2) {
                $mddiff = intval(($end - mktime(0, 0, 0, $mon2-1, $day1, $year2))/(24*60*60));						
            } else {
                $mddiff = intval(($end - mktime(0, 0, 0, $mon2, $day1, $year2))/(24*60*60));
            }
            return $mddiff;
            break;
        default:
        //return "{Datedif Error: Unrecognized \$unit parameter. Valid values are 'Y', 'M', 'D', 'YM'. Default is 'D'.}";
    }
}    
?>
