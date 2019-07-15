#!/usr/bin/perl

#
# Branched to allow new checking process
# Revised: Sep 17, 2014
#          Jan 07, 2018
#          Jun 18, 2018
#

#print "Content-type: text/plain\n\n";
## print "Content-type: application/vnd.ms-excel\n";
## print "Content-Disposition: attachment;filename=charges.csv\n\n";

# IBE => 1=Booked, -1=Rebooked, 0=Cancelled, 2=No Show

# PERL MODULES WE WILL BE USING
use DBI;
use DBD::mysql;
use Time::Local;
use LWP::UserAgent;
use HTTP::Request;
use JSON -support_by_pp;

print "Content-type: text/plain\n\n";

#my $debug_email = "juan.sarria\@everlivesolutions.com";
#my $debug_email = "juan.sarria\@everlivesolutions.com, mirek\@artbymobile.com";
my $debug_email = "mirek\@artbymobile.com";

my $debug_str = "";
sub debug {
	my ($str) = @_;
	$debug_str .= $str;
	print $str;
}

#	Simple Email Function
#	($to, $from, $subject, $message)
sub sendEmail {
 	my ($to, $from, $subject, $message) = @_;
 	my $sendmail = '/usr/lib/sendmail';
	my $bcc = "mirek\@artbymobile.com, reservations\@grupoexcellence.com";

 	open(MAIL, "|$sendmail -oi -t");
 		print MAIL "From: $from\n";
 		print MAIL "To: $to\n";
		print MAIL "Bcc: $bcc\n";
 		print MAIL "Subject: $subject\n\n";
 		print MAIL "$message\n";
 	close(MAIL);
}

# Read JSON Webservice
sub fetch_json_page {
	my ($json_url) = @_;
	debug("Validated against:\n".$json_url."\n");

	if ($json_url eq "") {
		return "";
	} else {
		### my $agent = LWP::UserAgent->new(env_proxy => 1,keep_alive => 1, timeout => 30);
		### The default timeout() value is 180 seconds, i.e. 3 minutes. 
		my $agent = LWP::UserAgent->new;
		my $header = HTTP::Request->new(GET => $json_url);
		my $request = HTTP::Request->new('GET', $json_url, $header);
		my $response = $agent->request($request);

		if ($response->is_success) {
			return $response->content;
		} else {
			sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS WebService Error ***", "The IBE WebService $json_url did not respond when CCPS was trying to use it."); 
			return "";
		}
	}
}

$B_SERVER_NAME = lc($ARGV[0]);
if ($B_SERVER_NAME eq "") {
	$B_SERVER_NAME = lc($ENV{'SERVER_NAME'});
}
$daysLeft = 4; # Default days before. It will be override by the DB

require 5.001;
$|=1;

# enter a path to the location of the pnpremote.pm

# use lib '';

# if ($B_SERVER_NAME eq "secure-excellence-resorts.com" || $B_SERVER_NAME eq "live") {
# 	use lib '/var/www/vhosts/secure-excellence-resorts.com/httpdocs/ws';
# } else {
# 	use lib '/var/www/vhosts/locateandshare.com/httpdocs/ws';
# }

use lib '/var/www/vhosts/secure-excellence-resorts.com/httpdocs/ws';
use pnpremote;
## use strict;

##print "\n $ENV{'QUERY_STRING'}";
my %in = ();
$in{'RES_ID'} = "";
(@qs) = split(/&/,$ENV{'QUERY_STRING'});
foreach $param (@qs) { 
	($key, $val) = split (/\=/,$param);
	$in{$key} = $val;
}

# CONFIG VARIABLES
$platform = "mysql";
$host = "localhost";
$port = "3306";
$tablename = "RECORDS";
$SITE = "";
if ($B_SERVER_NAME eq "secure-excellence-resorts.com" || $B_SERVER_NAME eq "205.186.144.79" || $B_SERVER_NAME eq "live") {
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

$webservice_1 = "http://staging.finestresorts.com";
$webservice_2 = "";
$webservice_3 = "";
$database = "stg-ccps-ingenico";
$user = "juanccpsingenico";
$pw = "ijV1u!04";
$SITE = "Staging";

# DATA SOURCE NAME
$dsn = "dbi:mysql:$database:localhost:3306";

# PERL DBI CONNECT
$connect = DBI->connect($dsn, $user, $pw);
if (!$connect) {
	sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed to Connect (1) ***", "Connection to $database failed: $DBI::errstr"); 
	exit;
}

$line = "CREDIT CARD CHARGES REPORT\n\nProcessing \"$SITE\" Site ";
$output = $line; debug($line);

# GET DAYS BEFORE FROM SETTINGS TABLE
$queryStr = "SELECT * FROM SETTINGS WHERE id='1'";
$sth = $connect->prepare($queryStr);
$execute = $sth->execute();
if (!$execute) {
	sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed to Execute (A) ***", "$queryStr");
	exit;
}
@row = $sth->fetchrow_array;
$daysLeft = $row[1];
$adminemail = $row[2]; # silvia@savannahvilletours.com, cquevedo@grupoexcellence.com, dmunoz@grupoexcellence.com, cxc2.xrc@excellence-resorts.com, info@excellence-resorts.com
#$adminemail = $debug_email;

$sth->finish;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime(  ); # Today + 4 days
$today = (1900 + $year)."-".($mon+1)."-$mday";
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime( (time-18000) + ($daysLeft * 86400) ); # Today + 4 days
#$date_limit = (1900 + $year)."-".($mon+1)."-$mday $hour:$min:$sec";# '2007-09-08 23:59:59';
$date_limit = (1900 + $year)."-".($mon+1)."-$mday";# '2007-09-08';

debug("\ntoday: " . $today . " - date_limit: " . $date_limit);

#exit;


$queryStr = "SELECT `UID`,`RES_ID`,`CHECK_IN`,`publisher-name`,`card-number`,`card-name`,`card-address1`,`card-city`,`card-state`,`card-zip`,`card-country`,`card-type`,`card-exp`,`card-amount`,`email`,`admin-email`,CHECK_OUT,GUEST_NAME,ROOMS,GUESTS,CHILDREN,INFANTS,GUEST_EMAIL FROM RECORDS WHERE ";
if ($in{'RES_ID'} eq "") {
	$where = "STATUS = '0' AND '".$date_limit." 00:00:00' >= CHECK_IN";
} else {
	$where = "RES_ID = '$in{'RES_ID'}' AND STATUS = '0' ORDER BY CREATED DESC LIMIT 0,1";
}
$queryStr .= $where;

debug("\n\n$queryStr\n\n");

$line = "Transactions with ".$daysLeft." days before arrival on ".$date_limit."\n\n";
$output .= $line; debug($line);

$sth = $connect->prepare($queryStr);
$execute = $sth->execute();
if (!$execute) {
	sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed to Execute (B) ***", "Get from CCPS the reservations ready to be charged <br> $queryStr");
	exit;
}

$size = $sth->{NUM_OF_FIELDS}; 

#for ($i=0; $i<$size; $i++) { 
#	print "\n$sth->{NAME}->[$i]";
#} 


###############################
# Status: 0=Ready, 2=Cancelled, 
#
#
###############################
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

print "IDs: ".$IDs."\n";

if ($in{'RES_ID'} eq "") {
	my $response = "";
	my $qrystr = "/ibe/index.php?HTTP_REFERER=excellence&PAGE_CODE=ws.getCCPS&IDs=".$IDs;
	$response .= ($webservice_1 eq "") ? "" : &fetch_json_page($webservice_1.$qrystr);
	$response .= ($webservice_2 eq "") ? "" : &fetch_json_page($webservice_2.$qrystr);
	$response .= ($webservice_3 eq "") ? "" : &fetch_json_page($webservice_3.$qrystr);
	$response =~ s/}{/,/g; # Join all resulting JSONs
	$response =~ s/\[\]//g; # Remove possible []
	$response = ($response eq "") ? "{}" : $response;

	debug("\nresponse: ".$response."\n\n");

	$json = new JSON;
	$oCCPS = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);
}

##
   exit;
##
###############################

@months = ("January","February","March","April","May","June","July","August","September","October","November","December");

$execute = $sth->execute();
if (!$execute) {
	sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed to Execute (C) ***", "Get from CCPS the reservations ready to be charged <br> $queryStr");
	exit;
}

## 
## Set status for all reservations ready to be processed as On Going (-99). Status will change after charge to 1, -1 or stay as -99
## 
if ($in{'RES_ID'} eq "") {

	$IDs  = substr $IDs, 1; 
	$IDs =~ s/,/\' OR RES_ID = \'/g; 
	$IDs = "RES_ID = '" . $IDs . "'";

	$WHERE_ONGOING = $where . " AND (" . $IDs . ")";

	$updateQry = "UPDATE RECORDS SET STATUS='-99' WHERE $WHERE_ONGOING";

	##  print $updateQry . "\n";
	##  exit;

	$sthUpdate = $connect->prepare($updateQry);
	$exeUpdate = $sthUpdate->execute();
	if (!$exeUpdate) {
		sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed Updating Initial On Going Status ***", $updateQry);
	}
	$sthUpdate->finish;
}

##
##   exit;
##
###############################

my @row;
while (@row = $sth->fetchrow_array) { 
    # print join(", ", @row), "\n";
	my %query = ();
	for ($t=0; $t<$size; $t++) { 
		# print "\n$sth->{NAME}->[$t] = @row->[$t]";
		$query{$sth->{NAME}->[$t]} = @row->[$t];
	}

	my $isValid = 0;
	if ($in{'RES_ID'} eq "") {
		my $msg = "";
		my $RES_ID = $query{'RES_ID'};
		my $STATUS_STR = ($response eq "[]") ? "Do Not Exists on IBE" : $oCCPS->{$RES_ID}->{STATUS_STR};

		debug("> ".$RES_ID." STATUS_STR: ".$STATUS_STR."\n");
		if ($STATUS_STR eq "booked") {
			$isValid = 1;
		} else {
			$msg = "Reservation #".$RES_ID;

			if ($STATUS_STR eq "arrived") {
				$msg = $msg . " already arrived on ".$query{'CHECK_IN'};
			} else {
				if ($STATUS_STR eq "") {
					$msg = $msg . " with check-in on ".$query{'CHECK_IN'};
				} else {
					$msg = $msg . " has status '".$STATUS_STR."' with check-in on ".$query{'CHECK_IN'};
				}
			}

			$msg = $msg . ", Card has not been charged. Please review";

			# $adminemail
			sendEmail($debug_email, "ccps\@excellence-resorts.com", "Credit Card Not Charged", $msg); 

			$updateQry = "UPDATE RECORDS SET STATUS='0' WHERE RES_ID='$RES_ID' AND STATUS='-99'";
			$sthUpdate = $connect->prepare($updateQry);
			$exeUpdate = $sthUpdate->execute();
			if (!$exeUpdate) {
				sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed Reverting Initial 'On Going' Status ***", $updateQry);
			}
			$sthUpdate->finish;
		}	
	}

	if ($isValid == 1 || $in{'RES_ID'} ne "") {
		# the rest of this is very important
		my @array = %query;

		# does some input testing to make sure everything is set correctly
		my $payment = pnpremote->new(@array);

		# does the actual connection and purchase. Transaction result is returned in query hash.
		# variable to test for success is $query{'FinalStatus'}.  Possible values are success, badcard or problem
		print " A ";
		%query = $payment->purchase();
		print " B ";

		$publisher_name = "Undef";
		if ($query{'publisher-name'} eq "excellence" ) { 
			$publisher_name = "XPC";
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Punta Cana";
			$email_from = "reservations.puj\@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-punta-cana/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations\@excellence-resorts.com";
		}
		if ($query{'publisher-name'} eq "excellence2" ) { 
			$publisher_name = "XRC"; 
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Riviera Cancun";
			$email_from = "reservations\@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-riviera-cancun/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations\@excellence-resorts.com";
		}
		if ($query{'publisher-name'} eq "excellence3" ) { 
			$publisher_name = "XPM"; 
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Playa Mujeres";
			$email_from = "reservations\@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-playa-mujeres/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations\@excellence-resorts.com";
		}
		if ($query{'publisher-name'} eq "excellence4" ) { 
			$publisher_name = "LaAm"; 
			$parent_name = "Beloved Playa Mujeres";
			$resort_name = "Beloved Playa Mujeres";
			$email_from = "reservations\@grupoexcellence.com";
			$home_url = "http://www.belovedhotels.com";
			$legal_url = "http://www.belovedhotels.com/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://belovedhotels.com/en/playa-mujeres-cancun-spa/index.cfm";
			$mlist_url = "http://www.belovedhotels.com";
			$contact_url = "http://www.belovedhotels.com/contact-us/";
			$res_email = "reservations\@grupoexcellence.com";
		}
		if ($query{'publisher-name'} eq "excellence1" ) { 
			$publisher_name = "FPM"; 
			$parent_name = "The Finest Playa Mujeres";
			$resort_name = "The Finest Playa Mujeres";
			$email_from = "info\@finestresorts.com";
			$home_url = "http://www.finestresorts.com";
			$legal_url = "http://www.finestresorts.com/legal/";
			$res_url = "https://www.finestresorts.com/guest/";
			$spa_url = "http://www.finestresorts.com/experience-finest/one-spa/";
			$mlist_url = "http://www.finestresorts.com";
			$contact_url = "http://www.finestresorts.com/contact-us/";
			$res_email = "info\@finestresorts.com";
		}
		if ($query{'publisher-name'} eq "excellence5" ) { 
			$publisher_name = "XEC";
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence El Carmen";
			$email_from = "reservations\@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts/excellence-el-carmen/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations\@excellence-resorts.com";
		}
		if ($query{'publisher-name'} eq "excellence6" ) { 
			$publisher_name = "XOB";
			$parent_name = "The Excellence Resorts";
			$resort_name = "Excellence Oyster Bay";
			$email_from = "reservations\@excellence-resorts.com";
			$home_url = "http://www.ExcellenceResorts.com";
			$legal_url = "http://www.excellenceresorts.com/footer/legal/";
			$res_url = "http://www.excellenceresorts.com/meta/my-reservation/my-reservation/";
			$spa_url = "http://www.excellenceresorts.com/resorts//excellence-oyster-bay/miile-spa/";
			$mlist_url = "http://www.excellenceresorts.com/footer/mailing-list/";
			$contact_url = "http://www.excellenceresorts.com/meta/contact-us/";
			$res_email = "reservations\@excellence-resorts.com";
		}

		$line = "$query{'RES_ID'}\t$publisher_name\t$query{'CHECK_IN'}\t$query{'FinalStatus'}\t$query{'MErrMsg'}\n";
		$output .= $line; debug($line);

		$cclen = length($query{'card-number'}); 
		$last4 = substr $query{'card-number'}, $cclen-4, 4;

		my($day, $month, $year)=(localtime)[3,4,5];
		$now = "".($year+1900)."-".($month+1)."-".$day;
		@dd = split(/-/, substr $now, 0, 10);
		$now = $months[$dd[1]-1]." ".$dd[2].", ".$dd[0];

		@dd = split(/-/, substr $query{'CHECK_IN'}, 0, 10);
		$from = $months[$dd[1]-1]." ".$dd[2].", ".$dd[0];
		$start = timelocal(0,0,0,$dd[2],$dd[1]-1,$dd[0]);

		@dd = split(/-/, substr $query{'CHECK_OUT'}, 0, 10);
		$to = $months[$dd[1]-1]." ".$dd[2].", ".$dd[0];
		$end = timelocal(0,0,0,$dd[2],$dd[1]-1,$dd[0]);

		$night = int(($end - $start) / (60*60*24));

		$GUESTS = "";

		## print LOG "Finished thinking about $query{'UID'}.\n";

		## while ( my ($key, $value) = each(%query) ) { print "\n$key => $value"; }; print "\n\n";
		# WE WANT TO SAVE:
		#
		#	FinalStatus
		#	MErrMsg 
		#	orderID
		#	auth_date => 20110205
		#
		if (lc($query{'FinalStatus'}) eq "success") {
			$status="1";

			$CHILDREN = int($query{'CHILDREN'});
			$INFANTS = int($query{'INFANTS'});
			$GUESTS = ", ".$query{'GUESTS'};

			if ($query{'publisher-name'} eq "excellence4" ) { 
				$GUESTS .= " Guest(s)";
			} else {
				$GUESTS .= " Adult(s)";
			}

			if ($CHILDREN!=0 || $INFANTS!=0) {
				if ($CHILDREN!=0) { $GUESTS .= ", ".($CHILDREN - $INFANTS)." Child(ren)"; }
				if ($INFANTS!=0) { $GUESTS .= ", ".$INFANTS." Infant(s)"; }
			}

	$CCPS_GP_02 = "

	Dear $query{'GUEST_NAME'},

	Thank you for choosing $resort_name.

	THE PAYMENT FOR YOUR RESERVATION HAS BEEN COMPLETED.

	RESERVATION INFORMATION:
	$resort_name, Reservation #$query{'RES_ID'}
	$query{'ROOMS'} Room(s)$GUESTS
	$from to $to

	This letter serves to notify that today $now the entire cost of your reservation \$$query{'card-amount'} USD has been deducted from your Credit Card $query{'card-type'} ending $last4.
	You may cancel your reservation without a charge 7 days before your arrival date. Please note that we will assess a fee equivalent to two nights of your stay if you cancel between 6 and 5 days before arrival. No refund will apply if you cancel or modify your reservation less than 4 days before arrival date. 
		
	Terms & conditions 
	$legal_url

	Extra charges or any last minute changes to your original reservation must be paid directly at the hotel.

	TO REVIEW YOUR RESERVATION OR CHANGE YOUR BOOKING, PLEASE GO TO THIS ADDRESS: 
	$res_url

	OR CLICK ON \"VIEW MY RESERVATIONS\" LINK OF OUR HOMEPAGE $home_url for further information or suggestions, don\'t hesitate to contact us. $home_url email: $res_email 
		
	Indulge at the Spa at $resort_name, and embrace the experience of pure relaxation. Please complete the spa reservation form to make an appointment $spa_url

	Visit the Spa at $resort_name to discover the full range of deluxe spa services and facilities available.

	Join Our Mailing List
	$mlist_url

	Thank you for choosing Excellence Resorts. We look forward to having you as our guest. 

	";

	# Card processing - success - version separate payee - sent to GUEST

	$CCPS_GP_03a = "

	Dear $query{'GUEST_NAME'},

	Thank you for choosing $resort_name.

	THE PAYMENT FOR YOUR RESERVATION HAS BEEN COMPLETED.

	RESERVATION INFORMATION:
	$resort_name, Reservation #$query{'RES_ID'}
	$query{'ROOMS'} Room(s)$GUESTS
	$from to $to
	 
	This letter serves to notify that today $now the entire cost of your reservation \$$query{'card-amount'} USD has been deducted from $query{'card-name'} Credit Card $query{'card-type'} ending $last4.
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

	Dear $query{'card-name'},

	Thank you for choosing $resort_name.

	THE PAYMENT FOR $query{'GUEST_NAME'} RESERVATION HAS BEEN COMPLETED.

	RESERVATION INFORMATION:
	$resort_name, Reservation #$query{'RES_ID'}
	$query{'ROOMS'} Room(s)$GUESTS
	$from to $to
	 
	This letter serves to notify that today $now the entire cost of $query{'GUEST_NAME'} reservation \$$query{'card-amount'} USD has been deducted from your Credit Card $query{'card-type'} ending $last4.
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

	Dear $query{'GUEST_NAME'},

	Thank you for choosing $resort_name.

	RESERVATION INFORMATION:
	$resort_name, Reservation #$query{'RES_ID'}
	$query{'ROOMS'} Room(s)$GUESTS
	$from to $to

	Unfortunately we haven\'t been able to process your payment from your Credit Card $query{'card-type'} ending $last4.
	 
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

	Dear $query{'GUEST_NAME'},

	Thank you for choosing $resort_name.

	RESERVATION INFORMATION:
	$resort_name, Reservation #$query{'RES_ID'}
	$query{'ROOMS'} Room(s)$GUESTS
	$from to $to

	Unfortunately we haven\'t been able to process the payment for the reservation \#$query{'RES_ID'} to the credit card under $query{'card-name'} name $query{'card-type'} ending $last4.

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

	Dear $query{'card-name'},

	Thank you for choosing $resort_name.

	Unfortunately we haven\'t been able to process the payment for the reservation  \#$query{'RES_ID'} under $query{'GUEST_NAME'} to your Credit Card $query{'card-type'} ending $last4.

	RESERVATION INFORMATION:
	$resort_name, Reservation #$query{'RES_ID'}
	$query{'ROOMS'} Room(s)$GUESTS
	$from to $to

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

		$query = "UPDATE RECORDS SET STATUS='$status', FinalStatus='$query{'FinalStatus'}', MErrMsg='$query{'MErrMsg'}', orderID='$query{'orderID'}', auth_date='$query{'auth_date'}' WHERE UID = $query{'UID'};";
		$sth2 = $connect->prepare($query);
		$execute = $sth2->execute();
		if (!$execute) {
			sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed Updating Status (D) ***", "Reservation: ".$query{'RES_ID'}."<br>CCPS UID: ".$query{'UID'}."<BR>".$query);
		}
		$sth2->finish;

		# Send email to user
		$to = $query{'email'};
		if ($query{'admin-email'} ne "") {
			$to = $query{'admin-email'};
			sendEmail($to, $email_from, $parent_name, $msg); 
		} else {
			if ($query{'GUEST_EMAIL'} eq $query{'email'}) {
				sendEmail($to, $email_from, $parent_name, $msg); 
			} else {
				if ($status eq "1") {
					## SUCCESS
					$to = $query{'GUEST_EMAIL'};
					$msg = $CCPS_GP_03a;
					sendEmail($to, $email_from, $parent_name, $msg); 

					$to = $query{'email'};
					$msg = $CCPS_GP_03b;
					sendEmail($to, $email_from, $parent_name, $msg); 
				}
				if ($status eq "-1") {
					## FAILED
					$to = $query{'GUEST_EMAIL'};
					$msg = $CCPS_GP_05a;
					sendEmail($to, $email_from, $parent_name, $msg); 

					$to = $query{'email'};
					$msg = $CCPS_GP_05b;
					sendEmail($to, $email_from, $parent_name, $msg); 
				}
				if ($status eq "-99") {
					## ON GOING == Never finished processing
				}
			}
		}

	} else {
		# Res is not valid.

		#print "\nOps";
	}
}

$sth->finish;

## exit;

debug("\n");

if ($in{'RES_ID'} eq "") {
	$queryStr = "SELECT `UID`,`RES_ID`,`CHECK_IN` FROM RECORDS WHERE (CHECK_IN >= '$today 00:00:00' AND CHECK_IN <= '$date_limit 23:59:59')";

	debug("\n\n$queryStr\n");

	$sth = $connect->prepare($queryStr);
	$execute = $sth->execute();
	if (!$execute) {
		sendEmail($debug_email, "ccps\@excellence-resorts.com", "*** CCPS Failed Executing (E) ***", "Final checkup for missing reservations in the CCPS <br> $queryStr");
	} else {

		$size = $sth->{NUM_OF_FIELDS}; 

		my @IDs;
		my @row;
		while (@row = $sth->fetchrow_array) { 
			my %query = ();
			for ($t=0; $t<$size; $t++) { 
				$query{$sth->{NAME}->[$t]} = @row->[$t];
			} 
			push(@IDs, "\"" . $query{'RES_ID'} . "\":\"" . $query{'CHECK_IN'} . "\"");
		}
		$response = "{".join(",",@IDs)."}";
		debug("\nresponse: ".$response."\n\n");

		$json = new JSON;
		$oCCPS = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

		$response = "";
		$qrystr = "/ibe/index.php?HTTP_REFERER=excellence&PAGE_CODE=ws.getCCPS&CHECK_IN=".$date_limit;
		$response .= ($webservice_1 eq "") ? "" : &fetch_json_page($webservice_1.$qrystr);
		$response .= ($webservice_2 eq "") ? "" : &fetch_json_page($webservice_2.$qrystr);
		$response .= ($webservice_3 eq "") ? "" : &fetch_json_page($webservice_3.$qrystr);
		$response =~ s/}{/,/g; # Join all resulting JSONs
		$response =~ s/\[\]//g; # Remove possible []
		$response = ($response eq "") ? "{}" : $response;

		debug("\nresponse: ".$response."\n\n");

		$json = new JSON;
		$oIBE = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

		if ($response ne "[]") {
			my @notfound;
			for (keys %$oIBE) {
				$NUMBER = $oIBE->{$_}->{'NUMBER'};
				#print "\n=>".$NUMBER;
				if ($oCCPS->{$NUMBER}) {
					# print "\nok"; It exists in the CCPS results
				} else {
					# print "\nnope";
					push(@notfound, $NUMBER);
				}
			}

			if (scalar(@notfound)!=0) {
				debug("\nNot Found: @notfound");
				# $adminemail
				sendEmail($debug_email, "ccps\@excellence-resorts.com", "Reservations Not Found in CCPS", "The following Reservations with check-in on $date_limit were not found in the CCPS: @notfound"); 
			}
		}
	}

	sendEmail($debug_email, "ccps\@excellence-resorts.com", "CCPS Debuging Code", $debug_str); 
}

$line = "\n\nEnd\n";
$output .= $line; debug($line);

sendEmail($adminemail, "ccps\@excellence-resorts.com", "Excellence Group Resorts - CREDIT CARD CHARGES REPORT", $output); 
##print $output;


exit;
