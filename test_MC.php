<?php

$today = date("Y-m-d");
echo $today;

//$today="2019-07-12";
$date_limit = date('Y-m-d', strtotime("+30 day"));

//debug("today: " . $today . " - date_limit: " . $date_limit);

//exit;/*
//26-04-19
$HotelFiltro="and (RES_ID like '5%' or RES_ID like '4%') ";
$HotelFiltro="";
$query = "SELECT * FROM RECORDS WHERE ";

	//$where = "STATUS = '0' AND '".$date_limit." 00:00:00' >= CHECK_IN";
	$where = "STATUS = '0' AND '".$date_limit." 00:00:00' >= CHECK_IN {$HotelFiltro}";
print $query.$where;
/*
function comando($linea){
		$cmd = $linea;
		$output = shell_exec($cmd);
		return $output;
}
print comando("top");
*/
/*
$DB_SERVER = "localhost";
$DB_USER = "juanccpsingenico";
$DB_PWD = "ijV1u!04";
$DB_NAME = "stg-ccps-ingenico";


$webservice_1 = "http://www.finestresorts.com";
$webservice_2 = "";
$webservice_3 = "";
/*
$database = "stg-ccps-ingenico";
$user = "juanccpsingenico";
$pw = "ijV1u!04";
$SITE = "Staging";
*/
/*
//-------------------->
		$DB_USER2 = "juanccpsdb";
        $DB_PWD = "ijV1u!04";
        $DB_NAME2 = "new-ccps-db";
//-------------------->

$result = "Unknown Transaction.";
$link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

$fecha_hoy="2019-04-02";

$daysLeft = 30;
//$today = date("Y-m-d");
$today=$fecha_hoy;
$date_limit = date('Y-m-d', strtotime("+".$daysLeft." day"));
$queryStr = "SELECT `UID`,`RES_ID`,`CHECK_IN` FROM RECORDS WHERE (CHECK_IN >= '$today 00:00:00' AND CHECK_IN <= '$date_limit 23:59:59')";
//$queryStr ="SELECT `UID`,`RES_ID`,`CHECK_IN` FROM RECORDS WHERE (CHECK_IN >= '2019-04-01 00:00:00' AND CHECK_IN <= '2019-05-01 23:59:59')";

echo $queryStr;

//inicia consulta 2
$result = "Unknown Transaction.";
$link2 = mysql_connect($DB_SERVER,$DB_USER2,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME2,$link2) or die('Cannot select the DB');
$retVal2 = mysql_query($queryStr,$link2) or die('Error query2:  '.$queryStr);
//fin consulta 2

  $retVal = mysql_query($queryStr,$link) or die('Error query:  '.$queryStr);
  if(!$retVal) {
    //sendEmail($debug_email, "ccps@excellence-resorts.com", "*** CCPS Failed Executing (E) ***", "Final checkup for missing reservations in the CCPS <br> $queryStr");
    //echo("No se genero reporte");
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
    *//*
	//echo("toy dentro");	
    $IDs = array();
    while ($row = mysql_fetch_array($retVal)) {
        $IDs[] = "\"" . $row['RES_ID'] . "\":\"" . $row['CHECK_IN'] . "\"";
    }
    //echo("toy dentro despues de while");
    
    	//echo "segundo siclo";
    	//while ($row = mysql_fetch_array($retVal2)) {
        //$IDs[] = "\"" . $row['RES_ID'] . "\":\"" . $row['CHECK_IN'] . "\"";
    	//}
    


	$response = "{".join(",",$IDs)."}";

	//echo "$response";
		

		## $json = new JSON;
		## $oCCPS = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

    $oCCPS = json_decode($response, true);

    //print_r($oCCPS);

		$response = "";
		$qrystr = "/ibe/index.php?HTTP_REFERER=excellence&PAGE_CODE=ws.getCCPS&CHECK_IN=".$date_limit;
		$response .= ($webservice_1 == "") ? "" : fetch_json_page($webservice_1.$qrystr);
		$response .= ($webservice_2 == "") ? "" : fetch_json_page($webservice_2.$qrystr);
		$response .= ($webservice_3 == "") ? "" : fetch_json_page($webservice_3.$qrystr);
		//$response =~ s/}{/,/g; # Join all resulting JSONs
		//$response =~ s/\[\]//g; # Remove possible []

	//echo("sigo dentro2.0");
    $response = preg_replace('/\}\s*\{/', ',', $response);
    $response = preg_replace('/\[\s*\]/', '', $response);
		$response = ($response == "") ? "{}" : $response;

		

		## $json = new JSON;
		## $oIBE = $json->allow_nonref->utf8->relaxed->escape_slash->loose->allow_singlequote->allow_barekey->decode($response);

    $oIBE = json_decode($response, true);

    //print_r($oIBE);

		if ($response != "[]") {
			$notfound = array();
	      foreach ($oIBE as $NUMBER => $o) {
					#$NUMBER = $oIBE->{$_}->{'NUMBER'];
					//print "<br>=>".$NUMBER;
					if (isset($oCCPS[$NUMBER])) {
						 //print "<br>ok"; //It exists in the CCPS results
					} else {
						 //print "<br>nope";
						//print("`NUMBER` = '".$NUMBER."' OR ");
						//if($oIBE[$NUMBER]['STATUS']!=1 && $oIBE[$NUMBER]['STATUS']!=-1){
							$notfound[] = $NUMBER." --->".$oIBE[$NUMBER]['STATUS'];
							print("`NUMBER` = '".$NUMBER."' OR ");
						//}
	          			
					}
			}
			print_r($notfound);
			if ($notfound) {
				debug("<br>Not Found: ".implode(",",$notfound));
				print_r($notfound);
				# $adminemail
				//sendEmail($debug_email, "ccps@excellence-resorts.com", "Reservations Not Found in CCPS", "The following Reservations with check-in on $date_limit were not found in the CCPS: ".implode(",",$notfound)); 
			}
		}


	}

function fetch_json_page($json_url) {
	//debug("<br>Validated against:<br>".$json_url);

	if ($json_url == "") {
		return "";
	} else {
    return file_get_contents($json_url);
	}
}
*/
?>