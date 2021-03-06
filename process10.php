<?php
#
# Branched to allow new checking process
# Revised: Sep 17, 2014
#          Jan 07, 2018
#          Aug 01, 2018 - Translated to PHP from PERL
#

# IBE => 1=Booked, -1=Rebooked, 0=Cancelled, 2=No Show

# print "Content-type: text/plain\n\n";

 $debug_email = "juan.sarria@everlivesolutions.com";
# $debug_email = "juan.sarria@everlivesolutions.com, mirek@artbymobile.com";
# $debug_email = "mirek@artbymobile.com";

global $debug_str;
$debug_str = "";
function debug($str) {
  global $debug_str;
	$debug_str .= $str;
	print $str . "<br>";
}

#	Simple Email Function
function sendEmail($to, $from, $subject, $message) {
	//+200319
	//$bcc = "mirek@artbymobile.com, reservations@grupoexcellence.com, mcanul@grupoexcellence.com, aruz@grupoexcellence.com";
  //MC+ 140619
  $bcc = "reservations@grupoexcellence.com, aruz@grupoexcellence.com";
  	//$bcc = "mirek@artbymobile.com";
  	//-200319
  //$to = "jaunsarria@gmail.com"; 
  //$subject = "INGENICO :: ". $subject;
	$subject = $subject;

  // Always set content-type when sending HTML email
  // $headers = "MIME-Version: 1.0" . "\r\n";
  // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

  // More headers
  $headers .= "From: " . $from . "\r\n";
  $headers .= "Bcc: " . $bcc . "\r\n";

  //debug("<br>sendEmail: ".$to);

  mail($to,$subject,$message,$headers);

	//print str_replace("\\n","<br>",$message);
}

# Read JSON Webservice
function fetch_json_page($json_url) {
	debug("<br>Validated against:<br>".$json_url);

	if ($json_url == "") {
		return "";
	} else {
    return file_get_contents($json_url);
	}
}

$B_SERVER_NAME = strtolower($ARGV[0]);
if ($B_SERVER_NAME == "") {
	$B_SERVER_NAME = strtolower($_SERVER["HTTP_HOST"]);
}
$daysLeft = 4; # Default days before. It will be override by the DB

#require 5.001;
#$|=1;

# enter a path to the location of the pnpremote.pm

# use lib '';

# if ($B_SERVER_NAME == "secure-excellence-resorts.com" || $B_SERVER_NAME == "live") {
# 	use lib '/var/www/vhosts/secure-excellence-resorts.com/httpdocs/ws';
# } else {
# 	use lib '/var/www/vhosts/locateandshare.com/httpdocs/ws';
# }

#use lib '/var/www/vhosts/secure-excellence-resorts.com/httpdocs/ws';
#use pnpremote;
## use strict;

##print "\n $ENV{'QUERY_STRING'}";
/*
my %in = ();
$in{'RES_ID'} = "";
(@qs) = split(/&/,$ENV{'QUERY_STRING'});
foreach $param (@qs) { 
	($key, $val) = split (/\=/,$param);
	$in{$key} = $val;
}
*/

parse_str($_SERVER["QUERY_STRING"], $in);

/*
# CONFIG VARIABLES
$platform = "mysql";
$host = "localhost";
$port = "3306";
$tablename = "RECORDS";
$SITE = "";
if ($B_SERVER_NAME == "secure-excellence-resorts.com" || $B_SERVER_NAME == "205.186.144.79" || $B_SERVER_NAME == "live") {
	$webservice_1 = "http://excellence-resorts.com";
	$webservice_2 = "http://secure-belovedhotels.com";
	$webservice_3 = "http://www.finestresorts.com";
	$database = "new-ccps-db";
	$user = "juanccpsdb";
	$pw = "ijV1u!04";
	$SITE = "LIVE";
} else {
	$webservice_1 = "http://locateandshare.com";
	$webservice_2 = "";
	$webservice_3 = "";
	$database = "er_test_cc";
	$user = "juan2";
	$pw = "NewJuan2";
	$SITE = "Staging";
}
# DATA SOURCE NAME
$dsn = "dbi:mysql:$database:localhost:3306";

# PERL DBI CONNECT
$connect = DBI->connect($dsn, $user, $pw);
if (!$connect) {
	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed to Connect (1) ***", "Connection to $database failed: $DBI::errstr"); 
	exit;
}
*/

$INGENICO = true;
include_once "db.php";

$result = "Unknown Transaction.";
$link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

$line = "CREDIT CARD CHARGES REPORT\n\nProcessing \"$SITE\" Site ";
$output = $line; debug($line);
debug($DB_SERVER);
debug($DB_USER);
debug($DB_PWD);
debug($DB_NAME);
print "<h1>uno</h1>";
exit;

/*
# GET DAYS BEFORE FROM SETTINGS TABLE
$queryStr = "SELECT * FROM SETTINGS WHERE id='1'";
$sth = $connect->prepare($queryStr);
$execute = $sth->execute();
if (!$execute) {
	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed to Execute (A) ***", "$queryStr");
	exit;
}
@row = $sth->fetchrow_array;
$daysLeft = $row[1];
$adminemail = $row[2]; # silvia@savannahvilletours.com, cquevedo@grupoexcellence.com, dmunoz@grupoexcellence.com, cxc2.xrc@excellence-resorts.com, info@excellence-resorts.com
#$adminemail = $debug_email;

$sth->finish;
*/

$query = "SELECT * FROM SETTINGS WHERE id='1'";
$retVal = mysql_query($query,$link) or die('Error query:  '.$query);
if(!$retVal) {
	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed to Execute (A) ***", "$query");
	exit;
}
$iCount = (int)mysql_num_rows($retVal);

if ($iCount==1) {
  $row = mysql_fetch_array($retVal);
  $daysLeft = $row['days_before'];
  $adminemail = $row['notify_email']; # silvia@savannahvilletours.com, cquevedo@grupoexcellence.com, dmunoz@grupoexcellence.com, cxc2.xrc@excellence-resorts.com, info@excellence-resorts.com
}

debug("$daysLeft Days left - $adminemail");

# today: 2018-8-1 - date_limit: 2018-8-31

$today = date("Y-m-d");
$date_limit = date('Y-m-d', strtotime("+".$daysLeft." day"));

debug("today: " . $today . " - date_limit: " . $date_limit);

//exit;/*
//26-04-19
$HotelFiltro="and (RES_ID like '5%' or RES_ID like '4%') ";
$HotelFiltro="";
$query = "SELECT * FROM RECORDS WHERE ";
if ($in['RES_ID'] == "") {
	//26-04-19
	//$where = "STATUS = '0' AND '".$date_limit." 00:00:00' >= CHECK_IN";
	$where = "STATUS = '0' AND '".$date_limit." 00:00:00' >= CHECK_IN {$HotelFiltro}";
} else {
	//26-04-19
	//$where = "RES_ID = '{$in['RES_ID']}' AND STATUS = '0' ORDER BY CREATED DESC LIMIT 0,1";
	$where = "RES_ID = '{$in['RES_ID']}' AND STATUS = '0' {$HotelFiltro} ORDER BY CREATED DESC LIMIT 0,1";

}
$query .= $where;

debug("\n\n$query");

//exit;/*

$line = "Transactions with ".$daysLeft." days before arrival on ".$date_limit."\n\n";
$output .= $line; debug($line);

//exit;/*
/*
$sth = $connect->prepare($queryStr);
$execute = $sth->execute();
if (!$execute) {
	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed to Execute (B) ***", "Get from CCPS the reservations ready to be charged <br> $queryStr");
	exit;
}

$size = $sth->{NUM_OF_FIELDS}; 
*/

$return = mysql_query($query,$link) or die('Error query:  '.$query);
if(!$return) {
	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed to Execute (B) ***", "Get from CCPS the reservations ready to be charged <br> $query");
	exit;
}
$iCount = (int)mysql_num_rows($return);

#for ($i=0; $i<$size; $i++) { 
#	print "\n$sth->{NAME}->[$i]";
#} 


###############################
# Status: 0=Ready, 2=Cancelled, 
#
#
###############################
/*
my $IDs = "";
my @row;
while (@row = $sth->fetchrow_array) { 
	my %query = ();
	for ($t=0; $t<$size; $t++) { 
		# print "\n$sth->{NAME}->[$t] = @row->[$t]";
		$query{$sth->{NAME}->[$t]} = @row->[$t];
	} 
	$IDs .= ",".$query{'RES_ID'};
	#print $query{'RES_ID'} . "\n";
}
*/

$reservations = array();
$IDs = array();
if ($iCount!=0) {
  while ($row = mysql_fetch_array($return)) {
      $reservations[] = $row;
      $IDs[] = $row{'RES_ID'};
  }
}

$IDstring = implode(",", $IDs);

print "IDs: $IDstring <br>";

if ($in['RES_ID'] == "") {
	$response = "";
	$qrystr = "/ibe/index.php?HTTP_REFERER=excellence&PAGE_CODE=ws.getCCPS&IDs=,".$IDstring;
	$response .= ($webservice_1 == "") ? "" : fetch_json_page($webservice_1.$qrystr);
	$response .= ($webservice_2 == "") ? "" : fetch_json_page($webservice_2.$qrystr);
	$response .= ($webservice_3 == "") ? "" : fetch_json_page($webservice_3.$qrystr);
	//$response =~ s/}{/,/g; # Join all resulting JSONs
	//$response =~ s/\[\]//g; # Remove possible []
  $response = preg_replace('/\}\s*\{/', ',', $response);
  $response = preg_replace('/\[\s*\]/', '', $response);
	$response = ($response == "") ? "{}" : $response;

	debug("<br>response: ".$response."<br><br>");

	//$json = new JSON;
	//$oCCPS = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

  $oCCPS = json_decode($response, true);
}

##
##   exit;
##
###############################

$months = array("January","February","March","April","May","June","July","August","September","October","November","December");

#$execute = $sth->execute();
#if (!$execute) {
#	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed to Execute (C) ***", "Get from CCPS the reservations ready to be charged <br> $queryStr");
#	exit;
#}

## 
## Set status for all reservations ready to be processed as On Going (-99). Status will change after charge to 1, -1 or stay as -99
## 
if ($in['RES_ID'] == "") {

	#$IDs  = substr $IDs, 1; 
	#$IDs =~ s/,/\' OR RES_ID = \'/g; 
	#$IDs = "RES_ID = '" . $IDs . "'";

  $IDs = "RES_ID = '" . implode("' OR RES_ID = '", $IDs) . "'";

	$WHERE_ONGOING = $where . " AND (" . $IDs . ")";

	$updateQry = "UPDATE RECORDS SET STATUS='-99' WHERE $WHERE_ONGOING";

  print "<br>" . $updateQry . "<br>";
  //exit;

  /*
	$sthUpdate = $connect->prepare($updateQry);
	$exeUpdate = $sthUpdate->execute();
	if (!$exeUpdate) {
		sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Updating Initial On Going Status ***", $updateQry);
	}
	$sthUpdate->finish;
  */

  $retValUpdate = mysql_query($updateQry,$link) or die('Error query:  '.$updateQry);
  if(!$retValUpdate) {
    sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Updating Initial On Going Status ***", $updateQry);
    exit;
  }
}

##
##   exit;/*
##
###############################

foreach($reservations as $reservation) {
  # print join(", ", @row), "\n";
  #$IDs[] = $row{'RES_ID'};

	$isValid = 0;
	if ($in['RES_ID'] == "") {
		$msg = "";
		$RES_ID = $reservation['RES_ID'];
		$STATUS_STR = $response == "[]" ? "Do Not Exists on IBE" : $oCCPS[$RES_ID]['STATUS_STR'];

		debug("<br> > ".$RES_ID." STATUS_STR: ".$STATUS_STR."\n");
		if ($STATUS_STR == "booked") {
			$isValid = 1;
		} else {
			$msg = "Reservation #".$RES_ID;

			if ($STATUS_STR == "arrived") {
				$msg = $msg . " already arrived on ".$reservation['CHECK_IN'];
			} else {
				if ($STATUS_STR == "") {
					$msg = $msg . " with check-in on ".$reservation['CHECK_IN'];
				} else {
					$msg = $msg . " has status '".$STATUS_STR."' with check-in on ".$reservation['CHECK_IN'];
				}
			}

			$msg = $msg . ", Card has not been charged. Please review";

			# $adminemail
			sendEmail($debug_email, "ccps@excellence-resorts.com", "Credit Card Not Charged", $msg);

			$updateQry = "UPDATE RECORDS SET STATUS='0' WHERE RES_ID='$RES_ID' AND STATUS='-99'";
      /*
			$sthUpdate = $connect->prepare($updateQry);
			$exeUpdate = $sthUpdate->execute();
			if (!$exeUpdate) {
				sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Reverting Initial 'On Going' Status ***", $updateQry);
			}
			$sthUpdate->finish;
      */
      $retValUpdate = mysql_query($updateQry,$link) or die('Error query:  '.$updateQry);
      if(!$retValUpdate) {
        sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Reverting Initial 'On Going' Status ***", $updateQry);
      }
		}	
	}

	if ($isValid == 1 || $in['RES_ID'] != "") {
    /*
		# the rest of this is very important
		@array = %query;

		# does some input testing to make sure everything is set correctly
		$payment = pnpremote->new(@array);

		# does the actual connection and purchase. Transaction result is returned in query hash.
		# variable to test for success is $reservation['FinalStatus'].  Possible values are success, badcard or problem
		print " A ";
		%query = $payment->purchase();
		print " B ";
    */
  

		$publisher_name = "Undef";
		if ($reservation['publisher-name'] == "excellence" ) { 
      $merchantId = "9696";
			$publisher_name = "XPC";
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Punta Cana";
			$email_from = "reservations.puj@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-punta-cana/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations@excellence-resorts.com";
		}
		if ($reservation['publisher-name'] == "excellence2" ) { 
      $merchantId = "9694";
			$publisher_name = "XRC"; 
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Riviera Cancun";
			$email_from = "reservations@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-riviera-cancun/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations@excellence-resorts.com";
		}
		if ($reservation['publisher-name'] == "excellence3" ) { 
      $merchantId = "9692";
			$publisher_name = "XPM"; 
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Playa Mujeres";
			$email_from = "reservations@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-playa-mujeres/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations@excellence-resorts.com";
		}
		if ($reservation['publisher-name'] == "excellence4" ) { 
      $merchantId = "9695";
			$publisher_name = "LaAm"; 
			$parent_name = "Beloved Playa Mujeres";
			$resort_name = "Beloved Playa Mujeres";
			$email_from = "reservations@grupoexcellence.com";
			$home_url = "http://www.belovedhotels.com";
			$legal_url = "http://www.belovedhotels.com/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://belovedhotels.com/en/playa-mujeres-cancun-spa/index.cfm";
			$mlist_url = "http://www.belovedhotels.com";
			$contact_url = "http://www.belovedhotels.com/contact-us/";
			$res_email = "reservations@grupoexcellence.com";
		}
		if ($reservation['publisher-name'] == "excellence1" ) { 
      $merchantId = "9690";
			$publisher_name = "FPM"; 
			$parent_name = "The Finest Playa Mujeres";
			$resort_name = "The Finest Playa Mujeres";
			$email_from = "info@finestresorts.com";
			$home_url = "http://www.finestresorts.com";
			$legal_url = "http://www.finestresorts.com/legal/";
			$res_url = "https://www.finestresorts.com/guest/";
			$spa_url = "http://www.finestresorts.com/experience-finest/one-spa/";
			$mlist_url = "http://www.finestresorts.com";
			$contact_url = "http://www.finestresorts.com/contact-us/";
			$res_email = "info@finestresorts.com";
		}
		if ($reservation['publisher-name'] == "excellence5" ) { 
      $merchantId = "9691";
			$publisher_name = "XEC";
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence El Carmen";
			$email_from = "reservations@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-el-carmen/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations@excellence-resorts.com";
		}
		if ($reservation['publisher-name'] == "excellence6" ) { 
      $merchantId = "9693";
			$publisher_name = "XOB";
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Oyster Bay";
			$email_from = "reservations@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts//excellence-oyster-bay/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations@excellence-resorts.com";
		}

    $paymentJson = '{"message":"Err","httpStatusCode":"000"}';
    $GUEST_NAME = explode(" ", $reservation['GUEST_NAME']." - -");
    //+210319
    	$sub_token=substr($reservation['INGENICO_TOKEN'], -2);
    	$second=$rest = substr(time(), -2);
    //-210319
    $PAYDATA = array(
          "merchantId" => $merchantId,

          "cvv" => $reservation['card-cvv'],
					"expiryDate" => $reservation['card-exp'],
          "token" => $reservation['INGENICO_TOKEN'],

          "firstName" => $GUEST_NAME[0],
          "surname" => $GUEST_NAME[1],
          "emailAddress" => !empty($reservation['email']) ? $reservation['email'] : $reservation['GUEST_EMAIL'],

          "invoiceNumber" => $reservation['RES_ID'],
          //+210319
          //"merchantReference" => $reservation['RES_ID'] . "-" . $reservation['UID'],
          "merchantReference" => $reservation['RES_ID'] . "-" . $reservation['UID']. "-".$sub_token.$second,
          //-210319

          "amount" => (int)$reservation['card-amount'] * 100, // in cents
          "currencyCode" => "USD", // set currency, see dropdown
    );

    print "PAYDATA: <pre>";print_r($PAYDATA);print "</pre>";

    ob_start();
        $url = "https://secure-excellence-resorts.com/ingenico/sdk/php/make_payment.php";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PAYDATA);
        $paymentSent = curl_exec($ch);
        curl_close($ch);
    $paymentJson = ob_get_clean();
		//-050319
		//$paymentJson = '{"creationOutput":{"isNewToken":null,"token":null,"additionalReference":"519108499563344-1667","externalReference":"519108499563344-1667904"},"merchantAction":null,"payment":{"hostedCheckoutSpecificOutput":null,"paymentOutput":{"amountPaid":null,"bankTransferPaymentMethodSpecificOutput":null,"cardPaymentMethodSpecificOutput":{"authorisationCode":"003703","card":{"cardNumber":"493172******5674","expiryDate":"0920"},"fraudResults":{"avsResult":"0","cvvResult":"M","retailDecisions":null,"fraudServiceResult":"no-advice"},"threeDSecureResults":null,"token":null,"paymentProductId":1},"cashPaymentMethodSpecificOutput":null,"directDebitPaymentMethodSpecificOutput":null,"eInvoicePaymentMethodSpecificOutput":null,"invoicePaymentMethodSpecificOutput":null,"mobilePaymentMethodSpecificOutput":null,"paymentMethod":"card","redirectPaymentMethodSpecificOutput":null,"sepaDirectDebitPaymentMethodSpecificOutput":null,"amountOfMoney":{"amount":44000,"currencyCode":"USD"},"references":{"merchantOrderId":null,"merchantReference":"519108499563344-1667904","paymentReference":"0","providerId":"14000","providerReference":null,"referenceOrigPayment":null}},"status":"CAPTURE_REQUESTED","statusOutput":{"isAuthorized":true,"isRefundable":false,"errors":null,"isCancellable":true,"statusCategory":"PENDING_CONNECT_OR_3RD_PARTY","statusCode":800,"statusCodeChangeDateTime":"20190129222651"},"id":"000000969040000003630000100001"}}';$reservation['FinalStatus'] = "success";

    $payment = json_decode($paymentJson, true);
    print "payment: <pre>";print_r($payment);print "</pre>";

    //MC+ 140619
    sendEmail("mcanul@theexcellencecollection.com", "ccps@excellence-resorts.com", "*** CCPS Log Make_Payment {$reservation['RES_ID']} ***", $paymentJson);

    $reservation['INGENICO_RESULT'] = $paymentJson;
	//+050319
    //if ($paymentSent==1 && isset($payment['payment'])) {
	if ($paymentSent==1 && isset($payment['payment']) 
		&& $payment['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['fraudResults']['cvvResult']!=null
		&& $payment['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['fraudResults']['cvvResult']==='M'
		&& $payment['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['fraudResults']['fraudServiceResult']==='accepted'
		) {	
	//-050319
        $reservation['FinalStatus'] = $payment['payment']["status"]=="CAPTURE_REQUESTED" ? "success" : "";
        $reservation['MErrMsg'] = "";
        $reservation['orderID'] = $payment['payment']["id"];
        $reservation['auth_date'] = date("Y-m-d h:i:s", strtotime($payment['payment']["statusOutput"]["statusCodeChangeDateTime"]));

        $reservation['INGENICO_STATUS'] = $payment['payment']["status"];
   
    } else {

        $reservation['FinalStatus'] = "";
        $reservation['MErrMsg'] = $payment["message"];
        $reservation['orderID'] = "";
        $reservation['auth_date'] = "";

        $reservation['INGENICO_STATUS'] = $payment["httpStatusCode"];
    
    }
  
		$line = "{$reservation['RES_ID']}\t$publisher_name\t{$reservation['CHECK_IN']}\t{$reservation['FinalStatus']}\t{$reservation['MErrMsg']}\n";
		$output .= $line; debug("<BR>$line");

		$last4 = substr($reservation['card-number'], -4);

    /*
		$cclen = length($reservation['card-number']); 
		$last4 = substr $reservation['card-number'], $cclen-4, 4;

		my($day, $month, $year)=(localtime)[3,4,5];
		$now = "".($year+1900)."-".($month+1)."-".$day;
		@dd = split(/-/, substr $now, 0, 10);
		$now = $months[$dd[1]-1]." ".$dd[2].", ".$dd[0];

		@dd = split(/-/, substr $reservation['CHECK_IN'], 0, 10);
		$from = $months[$dd[1]-1]." ".$dd[2].", ".$dd[0];
		$start = timelocal(0,0,0,$dd[2],$dd[1]-1,$dd[0]);

		@dd = split(/-/, substr $reservation['CHECK_OUT'], 0, 10);
		$to = $months[$dd[1]-1]." ".$dd[2].", ".$dd[0];
		$end = timelocal(0,0,0,$dd[2],$dd[1]-1,$dd[0]);

		$night = int(($end - $start) / (60*60*24));
    */

		$GUESTS = "";
    $DATES = date("M j, Y", strtotime($reservation['CHECK_IN'])) . " to " . date("M j, Y", strtotime($reservation['CHECK_OUT']));

		## print LOG "Finished thinking about {$reservation['UID']}.\n";

		## while ( ($key, $value) = each(%query) ) { print "\n$key => $value"; }; print "\n\n";
		# WE WANT TO SAVE:
		#
		#	FinalStatus
		#	MErrMsg 
		#	orderID
		#	auth_date => 20110205
		#
		if (strtolower($reservation['FinalStatus']) == "success") {
			$status="1";

			$CHILDREN = (int)$reservation['CHILDREN'];
			$INFANTS = (int)$reservation['INFANTS'];
			$GUESTS = ", ".$reservation['GUESTS'];

			if ($reservation['publisher-name'] == "excellence4" ) { 
				$GUESTS .= " Guest(s)";
			} else {
				$GUESTS .= " Adult(s)";
			}

			if ($CHILDREN!=0 || $INFANTS!=0) {
				if ($CHILDREN!=0) { $GUESTS .= ", ".($CHILDREN - $INFANTS)." Child(ren)"; }
				if ($INFANTS!=0) { $GUESTS .= ", ".$INFANTS." Infant(s)"; }
			}

      $CCPS_GP_02 = "

Dear {$reservation['GUEST_NAME']},

Thank you for choosing $resort_name.

THE PAYMENT FOR YOUR RESERVATION HAS BEEN COMPLETED.

RESERVATION INFORMATION:
$resort_name, Reservation #{$reservation['RES_ID']}
{$reservation['ROOMS']} Room(s)$GUESTS
$DATES

This letter serves to notify that today $now the entire cost of your reservation \${$reservation['card-amount']} USD has been deducted from your Credit Card {$reservation['card-type']} ending $last4.
You may cancel your reservation without a charge 7 days before your arrival date. Please note that we will assess a fee equivalent to two nights of your stay if you cancel between 6 and 5 days before arrival. No refund will apply if you cancel or modify your reservation less than 4 days before arrival date. 

Terms & conditions 
$legal_url

Extra charges or any last minute changes to your original reservation must be paid directly at the hotel.

TO REVIEW YOUR RESERVATION OR CHANGE YOUR BOOKING, PLEASE GO TO THIS ADDRESS: 
$res_url

OR CLICK ON \"VIEW RESERVATIONS\" LINK OF OUR HOMEPAGE $home_url for further information or suggestions, don\'t hesitate to contact us. $home_url email: $res_email 

Indulge at the Spa at $resort_name, and embrace the experience of pure relaxation. Please complete the spa reservation form to make an appointment $spa_url

Visit the Spa at $resort_name to discover the full range of deluxe spa services and facilities available.

Join Our Mailing List
$mlist_url

Thank you for choosing Excellence Resorts. We look forward to having you as our guest. 

      ";

      # Card processing - success - version separate payee - sent to GUEST

      $CCPS_GP_03a = "

Dear {$reservation['GUEST_NAME']},

Thank you for choosing $resort_name.

THE PAYMENT FOR YOUR RESERVATION HAS BEEN COMPLETED.

RESERVATION INFORMATION:
$resort_name, Reservation #{$reservation['RES_ID']}
{$reservation['ROOMS']} Room(s)$GUESTS
$DATES

This letter serves to notify that today $now the entire cost of your reservation \${$reservation['card-amount']} USD has been deducted from {$reservation['card-name']} Credit Card {$reservation['card-type']} ending $last4.
You may cancel your reservation without a charge 7 days before your arrival date. Please note that we will assess a fee equivalent to two nights of your stay if you cancel between 6 and 5 days before arrival. No refund will apply if you cancel or modify your reservation less than 4 days before arrival date. 
 
Terms & conditions
$legal_url

Extra charges or any last minute changes to your original reservation must be paid directly at the hotel.

For further information or suggestions, don\'t hesitate to contact us.

$home_url
email: $res_email

Visit the Spa at $resort_name to discover the full range of deluxe spa services and facilities available.

Join Our Mailing List
$mlist_url

Thank you for choosing Excellence Resorts. We look forward to having you as our guest. 

      ";

      # Card processing - success - version separate payee - sent to CARD OWNER

      $CCPS_GP_03b = "

Dear {$reservation['card-name']},

Thank you for choosing $resort_name.

THE PAYMENT FOR {$reservation['GUEST_NAME']} RESERVATION HAS BEEN COMPLETED.

RESERVATION INFORMATION:
$resort_name, Reservation #{$reservation['RES_ID']}
{$reservation['ROOMS']} Room(s)$GUESTS
$DATES

This letter serves to notify that today $now the entire cost of {$reservation['GUEST_NAME']} reservation \${$reservation['card-amount']} USD has been deducted from your Credit Card {$reservation['card-type']} ending $last4.
You may cancel your reservation without a charge 7 days before your arrival date. Please note that we will assess a fee equivalent to two nights of your stay if you cancel between 6 and 5 days before arrival. No refund will apply if you cancel or modify your reservation less than 4 days before arrival date. 
 
Terms & conditions
$legal_url

Extra charges or any last minute changes to your original reservation must be paid directly at the hotel.

For further information or suggestions, don\'t hesitate to contact us.

$home_url
email: $res_email

Visit the Spa at $resort_name to discover the full range of deluxe spa services and facilities available.

Join Our Mailing List
$mlist_url

Thank you for choosing Excellence Resorts. We look forward to having you as our guest. 

      ";

      $msg = $CCPS_GP_02;

		} else {
			$status="-1";

      $CCPS_GP_04 = "

Dear {$reservation['GUEST_NAME']},

Thank you for choosing $resort_name.

RESERVATION INFORMATION:
$resort_name, Reservation #{$reservation['RES_ID']}
{$reservation['ROOMS']} Room(s)$GUESTS
$DATES

Unfortunately we haven\'t been able to process your payment from your Credit Card {$reservation['card-type']} ending $last4.

Please contact us $contact_url or call us within the next 24 hours to provide a valid payment information. If we don\'t hear from you within the next 24 hours we may cancel the reservation as per our Term & conditions $legal_url

In case of rebooking room and rates will be subject to hotel availability.

T +52 998 872-8638
Toll Free US +1 866 540 25 85
Toll Free MX +01 800 966 36 70
Toll Free CA +1 866 451 15 92
Toll Free UK +0 800 051 6244

We look forward to having you as our guests,

      ";

      # Card processing - failed - version separate payee - GUEST version

      $CCPS_GP_05a = "

Dear {$reservation['GUEST_NAME']},

Thank you for choosing $resort_name.

RESERVATION INFORMATION:
$resort_name, Reservation #{$reservation['RES_ID']}
{$reservation['ROOMS']} Room(s)$GUESTS
$DATES

Unfortunately we haven\'t been able to process the payment for the reservation #{$reservation['RES_ID']} to the credit card under {$reservation['card-name']} name {$reservation['card-type']} ending $last4.

Please contact us $contact_url or call us within the next 24 hours to provide a valid payment information. If we don\'t hear from you within the next 24 hours we may cancel the reservation as per our Term & conditions $legal_url

In case of rebooking  room and rates will be subject to hotel availability.

T +52 998 872-8638
Toll Free US +1 866 540 25 85
Toll Free MX +01 800 966 36 70
Toll Free CA +1 866 451 15 92
Toll Free UK +0 800 051 6244

We look forward to having you as our guests,

      ";

      # Card processing - failed - version separate payee - CARD OWNER version

      $CCPS_GP_05b = "

Dear {$reservation['card-name']},

Thank you for choosing $resort_name.

Unfortunately we haven\'t been able to process the payment for the reservation  #{$reservation['RES_ID']} under {$reservation['GUEST_NAME']} to your Credit Card {$reservation['card-type']} ending $last4.

RESERVATION INFORMATION:
$resort_name, Reservation #{$reservation['RES_ID']}
{$reservation['ROOMS']} Room(s)$GUESTS
$DATES

Please contact us $contact_url or call us within the next 24 hours to provide a valid payment information. If we don\'t hear from you within the next 24 hours we may cancel the reservation as per our Term & conditions $legal_url

In case of rebooking  room and rates will be subject to hotel availability.

T +52 998 872-8638
Toll Free US +1 866 540 25 85
Toll Free MX +01 800 966 36 70
Toll Free CA +1 866 451 15 92
Toll Free UK +0 800 051 6244

We look forward to having you as our guests,

      ";

      $msg = $CCPS_GP_04;

		}



		$msg .= " 

Kind Regards,

$parent_name team
$res_email
		";

		$updateQuery = "UPDATE RECORDS SET STATUS='$status', FinalStatus='{$reservation['FinalStatus']}', MErrMsg='{$reservation['MErrMsg']}', orderID='{$reservation['orderID']}', auth_date='{$reservation['auth_date']}', INGENICO_STATUS='{$reservation['INGENICO_STATUS']}', INGENICO_RESULT='{$reservation['INGENICO_RESULT']}' WHERE UID = {$reservation['UID']};";
    
		## $sth2 = $connect->prepare($query);
		## $execute = $sth2->execute();
		## if (!$execute) {
		## 	sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Updating Status (D) ***", "Reservation: ".$reservation['RES_ID']."<br>CCPS UID: ".$reservation['UID']."<BR>".$query);
		## }
		## $sth2->finish;
    

    $update = mysql_query($updateQuery,$link) or die('Error query:  '.$updateQuery);
    if(!$update) {
      sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Updating Status (D) ***", "Reservation: ".$reservation['RES_ID']."<br>CCPS UID: ".$reservation['UID']."<BR>".$updateQuery);
    }

		# Send email to user
		$to = $reservation['email'];
		if (!empty($reservation['admin-email'])) {
			$to = $reservation['admin-email'];
      //debug("<br>1. Will send to : ".$to);
			sendEmail($to, $email_from, $parent_name, $msg); 
		} else {
			if ($reservation['GUEST_EMAIL'] == $reservation['email']) {
        //print "query: <pre>";print_r($query);print "</pre>";
        //debug("<br>2. Will send to [{$reservation['GUEST_EMAIL']}] == [{$reservation['email']}]: ".$to);
				sendEmail($to, $email_from, $parent_name, $msg); 
			} else {
				if ($status == "1") {
					## SUCCESS
					$to = $reservation['GUEST_EMAIL'];
					$msg = $CCPS_GP_03a;
          //debug("<br>3. Will send to : ".$to);
					sendEmail($to, $email_from, $parent_name, $msg); 

					$to = $reservation['email'];
					$msg = $CCPS_GP_03b;
          //debug("<br>4. Will send to : ".$to);
					sendEmail($to, $email_from, $parent_name, $msg); 
				}
				if ($status == "-1") {
					## FAILED
					$to = $reservation['GUEST_EMAIL'];
					$msg = $CCPS_GP_05a;
          //debug("<br>5. Will send to : ".$to);
					sendEmail($to, $email_from, $parent_name, $msg); 

					$to = $reservation['email'];
					$msg = $CCPS_GP_05b;
          //debug("<br>6. Will send to : ".$to);
					sendEmail($to, $email_from, $parent_name, $msg); 
				}
				if ($status == "-99") {
					## ON GOING == Never finished processing
				}
			}
		}

	} else {
		# Res is not valid.

		#print "\nOps";
	}

}

#$sth->finish;

## exit;/*

debug("<br>");

if ($in['RES_ID'] == "") {
	$queryStr = "SELECT `UID`,`RES_ID`,`CHECK_IN` FROM RECORDS WHERE (CHECK_IN >= '$today 00:00:00' AND CHECK_IN <= '$date_limit 23:59:59')";

	debug("<br><br>$queryStr<br>");

  $retVal = mysql_query($queryStr,$link) or die('Error query:  '.$queryStr);
  if(!$retVal) {
    sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Executing (E) ***", "Final checkup for missing reservations in the CCPS <br> $queryStr");
	} else {
    /*
		$size = $sth->{NUM_OF_FIELDS}; 

		@IDs;
		@row;
		while (@row = $sth->fetchrow_array) { 
			%query = ();
			for ($t=0; $t<$size; $t++) { 
				$query[$sth->{NAME}->[$t]} = @row->[$t];
			} 
			push(@IDs, "\"" . $query['RES_ID'] . "\":\"" . $query['CHECK_IN'] . "\"");
		}
    */

    $IDs = array();
    while ($row = mysql_fetch_array($retVal)) {
        $IDs[] = "\"" . $row['RES_ID'] . "\":\"" . $row['CHECK_IN'] . "\"";
    }

		$response = "{".join(",",$IDs)."}";
		debug("<br>response: ".$response."<br><br>");

		## $json = new JSON;
		## $oCCPS = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

    $oCCPS = json_decode($response, true);

		$response = "";
		$qrystr = "/ibe/index.php?HTTP_REFERER=excellence&PAGE_CODE=ws.getCCPS&CHECK_IN=".$date_limit;
		$response .= ($webservice_1 == "") ? "" : fetch_json_page($webservice_1.$qrystr);
		$response .= ($webservice_2 == "") ? "" : fetch_json_page($webservice_2.$qrystr);
		$response .= ($webservice_3 == "") ? "" : fetch_json_page($webservice_3.$qrystr);
		//$response =~ s/}{/,/g; # Join all resulting JSONs
		//$response =~ s/\[\]//g; # Remove possible []
    $response = preg_replace('/\}\s*\{/', ',', $response);
    $response = preg_replace('/\[\s*\]/', '', $response);
		$response = ($response == "") ? "{}" : $response;

		debug("<br>response: ".$response."<br><br>");

		## $json = new JSON;
		## $oIBE = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

    $oIBE = json_decode($response, true);

    //print "<pre>";print_r($oIBE);print "<pre>";

		if ($response != "[]") {
			$notfound = array();
      foreach ($oIBE as $NUMBER => $o) {
				#$NUMBER = $oIBE->{$_}->{'NUMBER'];
				#print "<br>=>".$NUMBER;
				if (isset($oCCPS[$NUMBER])) {
					# print "<br>ok"; It exists in the CCPS results
				} else {
					# print "<br>nope";
          $notfound[] = $NUMBER;
				}
			}

			if ($notfound) {
				debug("<br>Not Found: ".implode(",",$notfound));
				# $adminemail
				sendEmail($debug_email, "ccps@excellence-resorts.com", "Reservations Not Found in CCPS", "The following Reservations with check-in on $date_limit were not found in the CCPS: ".implode(",",$notfound)); 
			}
		}


	}

  global $debug_str;
	sendEmail($debug_email, "ccps@excellence-resorts.com", "CCPS Debuging Code", $debug_str); 
}

$line = "End";
$output .= $line; debug($line);

sendEmail($adminemail, "ccps@excellence-resorts.com", "Excellence Group Resorts - CREDIT CARD CHARGES REPORT", $output); 
##print $output;


exit;
