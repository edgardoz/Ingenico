<?php  

$DB_SERVER = "localhost";
$DB_USER = "juanccpsingenico";
$DB_PWD = "ijV1u!04";
$DB_NAME = "stg-ccps-ingenico";
$result = "Unknown Transaction.";
$link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');
	
//$query = "SELECT * FROM RECORDS WHERE RES_ID='518142116811797'"; //dev
//$query = "SELECT * FROM RECORDS WHERE `card-cvv`='' and STATUS=0"; 
//+090719ARQ
//$query = "SELECT * FROM RECORDS WHERE (`card-cvv`='' or `card-cvv` is null ) and `CHECK_IN` >= '2019-08-25 00:00:00' and `CHECK_IN` < '2019-10-01 00:00:00' and (`RES_ID` like  '1%' or `RES_ID` like  '2%' or `RES_ID` like  '3%'  or `RES_ID` like  '6%'  or `RES_ID` like  '7%') and STATUS=0";
$query = "SELECT * FROM RECORDS WHERE (`card-cvv`='' or `card-cvv` is null ) and `CHECK_IN` >= '2019-10-01 00:00:00' and `CHECK_IN` < '2019-10-02 00:00:00' and STATUS=0";
//-090719ARQ
//echo $query;


$return = mysql_query($query,$link) or die('Error query:  '.$query);
$iCount = (int)mysql_num_rows($return);
if ($iCount!=0) {
	while ($row = mysql_fetch_array($return)) {
		$RES_ID=$row[1];
		$PROP_ID=substr($RES_ID,0,1);
		$YEAR=substr($RES_ID,1,2);
		
		$URL = "https://www.excellence-resorts.com"."/ibe/index.php?PAGE_CODE=ws.getByNumber&PROP_ID=$PROP_ID&YEAR=20$YEAR&NUMBER=$RES_ID";
		
		//https://secure-belovedhotels.com/ibe/
		
		//$URL = "https://secure-belovedhotels.com"."/ibe/index.php?PAGE_CODE=ws.getByNumber&PROP_ID=$PROP_ID&YEAR=20$YEAR&NUMBER=$RES_ID";       

		//echo $URL."\n";
	    $RESERVATION = json_decode(file_get_contents($URL), true);
	    //print_r($RESERVATION['RESERVATION']['PAYMENT']['CC_CODE']);
	    if(isset($RESERVATION['RESERVATION']['PAYMENT']['CC_CODE']) && $RESERVATION['RESERVATION']['PAYMENT']['CC_CODE']!=null && 
			$RESERVATION['RESERVATION']['PAYMENT']['CC_CODE']!=''){
	    	echo actualiza_cvv($link,$DB_NAME,$RESERVATION['RESERVATION']['PAYMENT']['CC_CODE'],$RES_ID);
	    }
	}
}

function actualiza_cvv($link,$DB_NAME,$cvv,$id_res){
	$result = "Unknown Transaction.";
	mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');
	$query = "UPDATE RECORDS SET `card-cvv`='".$cvv."' WHERE RES_ID='".$id_res."'";
	//echo $query;

	$return = mysql_query($query,$link) or die('Error query:  '.$query);
	if($return==1){
		echo "<h4>Actualice cvv en $id_res</h4> \n";
		return "<h4>Actualice cvv en $id_res</h4> \n";
	}
}


?>