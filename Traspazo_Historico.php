<?php

//Destino
$DB_SERVER = "localhost";
$DB_USER = "juanccpsingenico";
$DB_PWD = "ijV1u!04";
$DB_NAME = "stg-ccps-ingenico";

//origen
//-------------------->
		$DB_USER2 = "juanccpsdb";
        $DB_PWD = "ijV1u!04";
        $DB_NAME2 = "new-ccps-db";
//-------------------->
$hoy = date("F j, Y, g:i a");
print "<b>Inicio: {$hoy}</b> <br>";
$link = mysql_connect($DB_SERVER,$DB_USER2,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME2,$link) or die('Cannot select the DB');

$link2 = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link2) or die('Cannot select the DB');
//$retVal2 = mysql_query($queryStr,$link2) or die('Error query2:  '.$queryStr);


$queryStr = "select * from RECORDS where (`RES_ID` like  '1%' or `RES_ID` like  '2%' or `RES_ID` like  '3%'  or `RES_ID` like  '6%'  or `RES_ID` like  '7%')";

$retVal = mysql_query($queryStr,$link) or die('Error query:  '.$queryStr);
$count=0;
escribe("Lista_Errores","\nInicio: \n");
while ($row = mysql_fetch_array($retVal)) {
	
				
		$insert=insertCCPSIngenico($row);
		if(mysql_query($insert,$link2)){
			$count++;
		}
		else{
			//print "Error: {$insert}";
			escribe("Lista_Errores",$insert);
		}
	
	
        

}
print "inserte : {$count}";
$hoy = date("F j, Y, g:i a");
print "<br><b>Fin: {$hoy}</b> <br>";

	function insertCCPSIngenico($row0){
		//$query="";
		$row = array();
		foreach ($row0 as $key => $campo) {
			if($campo!=''){
				$row[$key]="'{$row0[$key]}'";
			}
			else
			{
				$row[$key]="NULL";
			}
			
			//$query.="--- key: {$key} ---> {$campo} ---";
		}


		$query="INSERT INTO `RECORDS` (`RES_ID`, `TRANS_TYPE`, `STATUS`, `GUEST_NAME`, `GUEST_EMAIL`, `CHECK_IN`, `CHECK_OUT`, `ROOMS`, `GUESTS`, `CHILDREN`, `INFANTS`, `publisher-name`, `card-number`, `card-name`, `card-address1`, `card-city`, `card-state`, `card-zip`, `card-country`, `card-type`, `card-exp`, `card-amount`, `email`, `admin-email`, `FinalStatus`, `MErrMsg`, `orderID`, `auth_date`, `CREATED`, `CANCELLED`, `UPDATED`, `MSG`, `REDIRECT`) VALUES ({$row['RES_ID']},{$row['TRANS_TYPE']},{$row['STATUS']},{$row['GUEST_NAME']},{$row['GUEST_EMAIL']},{$row['CHECK_IN']},{$row['CHECK_OUT']},{$row['ROOMS']},{$row['GUESTS']},{$row['CHILDREN']},{$row['INFANTS']},{$row['publisher-name']},{$row['card-number']},{$row['card-name']},{$row['card-address1']},{$row['card-city']},{$row['card-state']},{$row['card-zip']},{$row['card-country']},{$row['card-type']},{$row['card-exp']},{$row['card-amount']},{$row['email']},{$row['admin-email']},{$row['FinalStatus']},{$row['MErrMsg']},{$row['orderID']},{$row['auth_date']},{$row['CREATED']},{$row['CANCELLED']},{$row['UPDATED']},{$row['MSG']},{$row['REDIRECT']})";
		
		return $query;	
		
	}

    function escribe($nombre,$msg){
        if($archivo = fopen($nombre.".txt", "a"))
        {
            fwrite($archivo, $msg. "\n");          
     
            fclose($archivo);
        }
    }








?>