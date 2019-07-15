


    <html>
    <head>
        <title>Excellence Group Resorts</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />        


    <style>
    	.button {
		  background-color: #4CAF50; /* Green */
		  border: none;
		  color: white;
		  padding: 15px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		}
		
  		.alerta{
  			 color: #1883ba;
		    background-color: #ffffff;
		    display: none;
  		}
	</style>    
    </head>
    <body>
<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/New_York');
//include_once "db.php";


$DB_SERVER = "localhost";
$DB_USER = "juanccpsingenico";
$DB_PWD = "ijV1u!04";
$DB_NAME = "stg-ccps-ingenico";
$result = "Unknown Transaction.";
$link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

//include_once "update_cvv.php"; 
//exit;
//$query = "SELECT * FROM RECORDS WHERE (INGENICO_TOKEN='' or INGENICO_TOKEN is null) and STATUS=0 AND (MErrMsg='' or MErrMsg is null or MErrMsg='PARAMETER_NOT_FOUND_IN_REQUEST') and `CHECK_IN` >= '2019-05-03 00:00:00' and `CHECK_IN` < '2019-05-04 00:00:00'";

//Finest
//$query ="SELECT * FROM RECORDS WHERE (INGENICO_TOKEN='' or INGENICO_TOKEN is null) and STATUS=0 AND (MErrMsg='' or MErrMsg is null) and `CHECK_IN` >= '2019-05-17 00:00:00' and `CHECK_IN` < '2019-05-20 00:00:00'";
//TBH
//$query ="SELECT * FROM RECORDS WHERE (INGENICO_TOKEN='' or INGENICO_TOKEN is null) and STATUS=0 AND (MErrMsg='' or MErrMsg is null) and `CHECK_IN` >= '2019-05-12 00:00:00' and `CHECK_IN` < '2019-05-20 00:00:00' and RES_ID like '4%'";

//SELECT * FROM RECORDS WHERE (`card-cvv`='' or `card-cvv` is null ) and `CHECK_IN` >= '2019-05-08 00:00:00' and `CHECK_IN` < '2019-05-12 00:00:00' and RES_ID like '4%' and STATUS=0
//$query = "SELECT * FROM RECORDS WHERE `MErrMsg` = 'PARAMETER_NOT_FOUND_IN_REQUEST'"; //Dev
//echo $query;

$query ="SELECT * FROM RECORDS WHERE (INGENICO_TOKEN='' or INGENICO_TOKEN is null) and STATUS=0 AND (MErrMsg='' or MErrMsg is null) and `CHECK_IN` >= '2019-10-01 00:00:00' and `CHECK_IN` < '2019-10-10 00:00:00' and (RES_ID like '4%' or RES_ID like '5%')";

//$query="select * from RECORDS WHERE `RES_ID` = '518140667210391'";

$return = mysql_query($query,$link) or die('Error query:  '.$query);
$iCount = (int)mysql_num_rows($return);
//echo $iCount;

$reservations = array();
$IDs = array();
/*
//+090719ARQ  quite info
<td valign='top' nowrap>Guest Name</td>
<td valign='top' nowrap>Card Name</td>
<td valign='top' nowrap>Card CCV</td>
<td valign='top' nowrap>Card Amount</td>
<td valign='top' nowrap>Card Exp</td>
<td valign='top' nowrap>Ingenico Token</td>
<td valign='top'>Check In</td>
<td valign='top'>Check Out</td>
<td valign='top'>Reservation</td>

			<td valign='top'>{$row['RES_ID']}</td>
                    <td valign='top'>".substr($row['CHECK_IN'], 0, 10)."</td>
                    <td valign='top'>".substr($row['CHECK_OUT'], 0, 10)."</td>
					<td valign='top'>{$row['GUEST_NAME']}</td>
					 <td valign='top'>{$row['INGENICO_TOKEN']}</td>
                    <td valign='top'>{$row['card-name']}</td>
					<td valign='top'>{$row['card-exp']}</td>
                    <td valign='top'>{$row['card-cvv']}</td>
					<td valign='top'>".number_format($row['card-amount'])."</td>
					
//-090719ARQ
*/
if ($iCount!=0) {


	echo "<table id='reportTbl' >
                    <tr class='hdr'>
                        <td valign='top'>contador</td>
                        
                        <td valign='top'>Booked</td>
                        
                        
                        <td valign='top'>Charged</td>
                        <td valign='top'>Status</td>
                        <td valign='top'>Cancelled</td>
                        
                        
                       
                        
						
                        
                       
                        <td valign='top' nowrap>Card Type</td>
                        
                        
                        
                        
                    </tr>        
        ";
        $tabla = array();
        $i=0;
  while ($row = mysql_fetch_array($return)) {
  			$STATUS = "";
            if ($row['STATUS'] == "0" ) { $STATUS = "<span class='pending'>Pending</span>"; }
            if ($row['STATUS'] == "1" ) { $STATUS = "<span class='charged'>Charged</span>"; }
            if ($row['STATUS'] == "2" ) { $STATUS = "<span class='cancelled'>Cancelled</span>"; }
            if ($row['STATUS'] == "-1" ) { $STATUS = "<span class='failed'>Failed</span>"; }
            if ($row['STATUS'] == "-99" ) { $STATUS = "<span class='failed'>On Going</span>"; }
            $tabla[]=$row;
            $json_row=json_encode($row);
            $i++;
  		echo "
                <tr >
                    <td valign='top'>{$i}</td>
                    
                    <td valign='top'>{$row['CREATED']}</td>

                    <td valign='top'>".substr($row['auth_date'], 0, 10)."</td>
                    <td valign='top'>{$STATUS}</td>
                    <td valign='top'>{$row['CANCELLED']}</td>
                  
                    
                   
                    
                   
                    
                    <td valign='top'>{$row['card-type']}</td>
                    
                    

                    <td><div id='{$row['RES_ID']}' style='visibility: hidden'>
        <p>tokenizado</p>
    </div><a href='javascript:;' class='button' onclick='book_now_lanzador({$json_row});'' role='button'>Tokenizar </a></td>
                </tr>
            ";
  		//echo"<td></td>";
  		//echo"<td></td>";
  }
  $json_tabla=json_encode($tabla);
  echo "</table>";
  echo "<div id='ingenico_debug'></div>";
  //echo "<a href='javascript:;' onclick='update_row({$array_test});'' role='button'>checa Update </a>";
  echo "<a href='javascript:;' class='button' onclick='envioMasivo({$json_tabla});'' role='button'>Envio Masivo </a>";

  echo "<a href='javascript:;' class='button' onclick='findPayment();'' role='button'>Buscar Pago </a>";

}
else{
	echo"<h1>No hay registros para tokenizar</h1>";
}

echo "<td><div  id='SHOW_ERROR' style='visibility: hidden; font-size:15px; font-weight: bold; font-family: arial; color:#FF0000;'>
        
    </div>";
echo "<a href='javascript:;' class='button' onclick='escribeError();'' role='button'>escribeError</a>";


echo "<a href='javascript:;' class='button' onclick='findPayment();'' role='button'>Buscar Pago </a>";

?>
<script src="/ingenico/js/jquery.min.js"></script>
<script src="/ingenico/js/jquery-1.5.2.min.js"></script>
<script src="/ingenico/js/jquery.validate.min.js"></script>
<script src="/ingenico/js/scripts.js"></script>
 <script src="/ingenico/sdk/js/dist/connectsdk.js"></script>
 <script>
	function ingenicoStartPayment(session, merchantId,row) {

	  console.log("Ingenico session", session);
	  

	  var merchantOrderId = Math.floor((Math.random() * 4294967295) + 1);

	  var formatCC = function(type, str) {
	      // #### #### #### #### (4-4-4-4) Visa
	      // #### ###### ##### (4-6-5) AmEx
	      return (type=="AmEx") ? str.substr(0, 4) + " " +  str.substr(4, 6) + " " +  str.substr(10, 5) : str.substr(0, 4) + " " +  str.substr(4, 4) + " " +  str.substr(8, 4) + " " + str.substr(12, 4)
	    }

	   //dividimos fecha
	   var arraycardExp = row[22].split("/");
	  paymentDetails = { 
	    totalAmount: 1, // in cents
	    //countryCode: $("#card_country").val(),
	    countryCode: row[20],
	    currency: "USD", // set currency, see dropdown
	    locale: "en_US", // as specified in the config center
	    isRecurring: false, // set if recurring

	    merchantOrderId: merchantOrderId,
	    merchantReference: "AUTH_CCPS_" + merchantOrderId,

	    //cardNumber: formatCC($("#card_type").val(), $("#card_number").val()),
	    cardNumber: formatCC(row[21], row[13]),

	    //cvv: $("#card_cvv").val(),
	    cvv: row[14],

	    //expiryDate: $("#card-exp-MM").val() + " " + $("#card-exp-YY").val()
	    expiryDate: arraycardExp[0] + " " + arraycardExp[1]
	  };

	  var paymentProductId = 1; // Visa

	  //switch($("#card_type").val()) {
	  	switch(row[21]) {
	    case "AmEx":
	      paymentProductId = 2;
	      break;
	    case "MasterCard":
	      paymentProductId = 3;
	      break;
	  } 

	  console.log("paymentDetails", paymentDetails);
	  console.log("paymentProductId", paymentProductId);

	  var paymentRequest = session.getPaymentRequest();

	  session.getPaymentProduct(paymentProductId, paymentDetails, true).then(function(paymentProduct) {

	    paymentRequest.setPaymentProduct(paymentProduct);
	    paymentRequest.setValue("cardNumber", paymentDetails.cardNumber); // This should be the unmasked value. 
	    paymentRequest.setValue("cvv", paymentDetails.cvv);
	    paymentRequest.setValue("expiryDate", paymentDetails.expiryDate);
	     
	    if (!paymentRequest.isValid()) {
	      // We have validation errors.
	      console.log("We have validation errors",  paymentRequest.getErrorMessageIds()); //This is an array of all the validation errors
	    }

	    console.log("getting ready to encryptor");

	    var encryptor = session.getEncryptor();

	    encryptor.encrypt(paymentRequest).then(function(encryptedCustomerInput) {
	      
	      console.log("encryptedCustomerInput -> ", encryptedCustomerInput)
	      //$('#divEncriptedResult').html(encryptedCustomerInput);

	      console.log("paymentRequest")
	      $.get("/ingenico/sdk/php/create_payment.php", {
	        merchantId: merchantId,
	        paymentDetails: paymentDetails,
	        encryptedCustomerInput: encryptedCustomerInput
	      })
	      .done(function(response) {
	      		//var card_v=response.payment.paymentOutput.cardPaymentMethodSpecificOutput.card.cardNumber;
	      		//if(row[13].substr(-4)==card_v.substr(-4)){
					console.log("response", response)

					$.get("/ingenico/sdk/php/send_response_v2.php", {
						response:  JSON.stringify(response),
						id: paymentDetails.cardNumber
					}).done(function() {
							
								update_row(JSON.stringify(response),row[0],"INGENICO_RESULT");
								//+050319
								//if (response.payment.statusOutput.errors === null) {
                                if (typeof response.creationOutput.token != "undefined" && response.creationOutput.token
                                    && response.payment.paymentOutput.cardPaymentMethodSpecificOutput.fraudResults.cvvResult != null
                                    && response.payment.paymentOutput.cardPaymentMethodSpecificOutput.fraudResults.cvvResult === 'M'
                                    && response.payment.paymentOutput.cardPaymentMethodSpecificOutput.fraudResults.fraudServiceResult === 'accepted'
                                             
                                ) {        
	                                //-050319

									//$("#INGENICO_TOKEN").val(response.creationOutput.token);
									//$("#INGENICO_TOKEN_PREV").val(response.creationOutput.token);
									//$("#card_number").val(response.payment.paymentOutput.cardPaymentMethodSpecificOutput.card.cardNumber);

									//make_booking();
									update_row(response.creationOutput.token,row[0],"INGENICO_TOKEN");

								} else if ((paymentDetails.cardNumber=="4111 1111 1111 1111" || paymentDetails.cardNumber=="4000 0243 2959 6391" || paymentDetails.cardNumber=="4917 4845 8989 7107") && paymentDetails.cvv=="123") {

									var faketoken = "-TESTING-"+paymentDetails.cardNumber.replace(/\s+/g,"-");

									//$("#INGENICO_TOKEN").val(faketoken);
									//$("#INGENICO_TOKEN_PREV").val(faketoken);

									//make_booking();
									update_row(faketoken,row[0],"INGENICO_TOKEN");

								} else {

									var Err = [];
									for (var t=0; t < response.payment.statusOutput.errors.length; ++t) {
										Err.push(response.payment.statusOutput.errors[t].message)
									}

									//alert("Error\n * "+Err.join("\n* "));
									//$("#btn-book-now").show();
									//$("#ingenico_debug").html("");
									update_row(Err,row[0],"MErrMsg");
									delete Err;
								}

							
						})
				
				//console.log("response", response)
				//}	

	      })
	      .fail(function(result) {
	        console.log("Ingenico Create Payment Response Error\n"+JSON.stringify(result));
	        update_row(JSON.stringify(result),row[0],"INGENICO_RESULT");
	        delete result;
	        //make_booking();
	      });


	    }, function(errors) {
							//error(errors, 1)
							update_row("-1 "+errors,row[0],"MErrMsg");
							errors="";
	    });

	  }, function(errors) {
						//error(errors, 2)
						update_row("-2 "+errors,row[0],"MErrMsg");
						errors="";
	  });
	  //delete response,result,session, merchantId,row;
	}
	function envioMasivo(tabla){
		if(confirm("Desea tokenizar todo?")){
			var count=tabla.length;		
			if(count>99){
				count=99;
			}
			for (var i = 0; i < count; i++) {
	   			setTimeout(book_now(tabla[i]), 10000);
			}
		}
		
	}
	function escribeError(){
		$('#SHOW_ERROR').css('visibility', 'visible');
		$('#SHOW_ERROR').text("si funcione");
		//alert('hola');
	}
	function book_now_lanzador(row){
		if(confirm("Desea tokenizar: "+row[1]+" ?")){
			 $('#'+row[1]).css('visibility', 'visible');		    
			book_now(row);
			
			//location.reload();
		}
		
	}

	function ingenicofindPayment(session, merchantId,merchantReference,merchantOrderId) {
		console.log("paymentfind")
	      $.get("/ingenico/sdk/php/find_payment.php", {
	        merchantId: merchantId,
	        merchantReference: merchantReference,
	        merchantOrderId: merchantOrderId
	      })
	      .done(function(response) {
	      	$.get("/ingenico/sdk/php/send_response_v2.php", {
						response:  JSON.stringify(response),
						id: "1245"
					})
	      	})
	}

	function findPayment(){	

	         // INGENICO

	          //$("#btn-book-now").hide();			  

	          /*
	          1. XRC - 9694
	          2. XPM - 9692
	          3. XPC - 9696
	          4. TBH - 9695
	          5. FPM - 9690
	          6. XEC - 9691
	          7. XOB - 9693
	          */

	          var merchantIDs = {
	            "excellence2":"9694",
	            "excellence3":"9692",
	            "excellence":"9696",
	            "excellence4":"9695",
	            "excellence1":"9690",
	            "excellence5":"9691",
	            "excellence6":"9693",
	          }

	          //var publisher_name = jQuery("#publisher_name").val();
	          var publisher_name = "excellence3";

	          var merchantId = merchantIDs[publisher_name];
	          //merchantId ="9696";
	          console.log("merchant: " + merchantId );
	          			
						$.ajax({
								url: "/ingenico/sdk/php/session_create.php?merchantId="+merchantId,
								success: function(data) {									
									var session = new connectsdk.Session(data);									
									ingenicofindPayment(session, 
										merchantId,
										"AUTH_CCPS_3491510668",
										"");
								},
						error: function(result) {
									alert("Error Creating Ingenico Session FindPayment!\n" + JSON.stringify(result));
								},
						});					
        
        
    }




 	function book_now(row){

		//alert("CC " + $("#card_number").val())
		if (row[13]!="" && row[14]!="") {
			
		

	         // INGENICO

	          //$("#btn-book-now").hide();			  

	          /*
	          1. XRC - 9694
	          2. XPM - 9692
	          3. XPC - 9696
	          4. TBH - 9695
	          5. FPM - 9690
	          6. XEC - 9691
	          7. XOB - 9693
	          */

	          var merchantIDs = {
	            "excellence2":"9694",
	            "excellence3":"9692",
	            "excellence":"9696",
	            "excellence4":"9695",
	            "excellence1":"9690",
	            "excellence5":"9691",
	            "excellence6":"9693",
	          }

	          //var publisher_name = jQuery("#publisher_name").val();
	          var publisher_name = row[12];

	          var merchantId = merchantIDs[publisher_name];
	          //merchantId="2020";
	          console.log("merchant: " + merchantId );
	          			
						$.ajax({
								url: "/ingenico/sdk/php/session_create.php?merchantId="+merchantId,
								success: function(data) {									
									var session = new connectsdk.Session(data);									
									ingenicoStartPayment(session, merchantId,row);
								},
						error: function(result) {
									alert("Error Creating Ingenico Session!\n" + JSON.stringify(result));
								},
						});
						

        }
        else{
        	alert("No Cumple con numero de tarjeta o ccv");
        }
        
    }

    function update_row(token,uid,campo){
    	 
    	 	//var token='15487';
    	 	//var res_id=row[1];
	          			console.log("start update token: "+token+" uid:"+uid+" campo:"+campo);
						$.ajax({
								url: "/ingenico/update_row.php?token="+token+"&uid="+uid+"&campo="+campo,
								success: function(data) {									
										console.log(data);
								},
						
						});
    }

 </script>
 </body>
</html>