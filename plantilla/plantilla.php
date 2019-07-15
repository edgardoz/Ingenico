
<?php

$request="{}";
//$request=json_decode($struct_json);
//print_r($request);
$RES_ID= (isset($_REQUEST['RES_ID'])) ? $_REQUEST['RES_ID'] : $struct_json;
$UID= (isset($_REQUEST['UID'])) ? $_REQUEST['UID'] : $struct_json;
//$request=json_decode($row);
//extract($row);
//print_r($row);
$DB_SERVER = "localhost";
$DB_USER = "juanccpsingenico";
$DB_PWD = "ijV1u!04";
$DB_NAME = "stg-ccps-ingenico";
$result = "Unknown Transaction.";

$link = mysql_connect($DB_SERVER,$DB_USER,$DB_PWD) or die('Cannot connect to the DB');
mysql_select_db($DB_NAME,$link) or die('Cannot select the DB');

$query = "SELECT * FROM RECORDS WHERE RES_ID='{$RES_ID}' AND UID={$UID}"; //Dev
//print_r($query);
$return = mysql_query($query,$link) or die('Error query:  '.$query);
$row1= array();
while ($row = mysql_fetch_array($return)) {
	$request=json_decode($row["INGENICO_RESULT"]);
	$row1[]=$row;
	//print_r($row['INGENICO_RESULT']);
}

?>



<html xmlns="http://www.w3.org/1999/xhtml">




<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />


	
		<title>Order Details</title>
	
	

<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" type="text/css" href="css/dataTables.css" />

<style type="text/css">
body{   
    margin: 0% auto;
            width: 100%;
            height: 100%;
			border: 0px solid #888;
	}

</style>
</head>
<body  >
	
<script type="text/javascript"
	src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript"
	src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript"
	src="js/jquery.cookie.min.js"></script>
	
	
<!-- main wrapper container starts -->

<div id="contentWrapper">
	<div>
		<table align="center">
			<tr>
				<td></td>
				<td><label class="headerLabel" style="width: 50px;">Order
						Details </label></td>
				<td></td>
			</tr>
		</table>
	</div>
	<div class="clear"></div>
	<div id="tabs">
		<div class="tableGrid">
			

<div class="header" id="OrderdetailSubtab">
	<div id="tab_container">
		<ul class="menutab">
			
				<li id="orderSummaryTab"><a href="#tabs-1"
					id="orderSummaryTab1">Order Summary</a></li>
			
		</ul>
	</div>
</div>
<div class="hrOrder" style="margin-top: 1.5px"></div>
<div class="clear"></div>

	<div id="tabs-1">
		



<div class="arrowlistmenu ">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		



<tr>
	<td colspan="4">
		<div
			style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 0px; width: 98%">
			<table class="overallborderpop" cellpadding="0" cellspacing="0"
				border="0" width="100%">
				<tr class="rich-table-row rowColor1">
					<td class="rich-table-cell lableBold" id="label_1_1" width="18%">Order Type :</td>
					
						
							<td class="rich-table-cell" id="label_1_2" width="32%">NORMAL</td>						
						
					
					<td class="rich-table-cell lableBold" id="label_1_3" width="18%">Merchant Id :</td>
					<td class="rich-table-cell" id="label_1_4" width="32%">9690</td>
				</tr>
				<tr class="rich-table-row rowColor2">
					<td class="rich-table-cell lableBold" id="label_2_1">Order ID :</td>
					<td class="rich-table-cell" id="label_2_2"></td>
					<td class="rich-table-cell lableBold" id="label_2_3">Merchant Reference :</td>
					<td class="rich-table-cell" id="label_2_4"><? print $request->creationOutput->externalReference; ?></td>
				</tr>
				<tr class="rich-table-row rowColor1">
					<td class="rich-table-cell lableBold" id="label_3_1">Merchant Order ID :</td>
					<td class="rich-table-cell" id="label_3_2"></td>
					<td class="rich-table-cell lableBold" id="label_3_3">Country :</td>
					<td class="rich-table-cell" id="label_3_4"><?print $row1[0]['card-country'];?></td>
				</tr>
				<tr class="rich-table-row rowColor2">
					<td class="rich-table-cell lableBold" id="label_6_1">Hosted Checkout ID :</td>
					<td class="rich-table-cell" id="label_6_2"></td>
					<td class="rich-table-cell lableBold" id="label_6_3">Variant :</td>
					<td class="rich-table-cell" id="label_6_4"></td>
				</tr>
				
				
			</table>
		</div>
	</td>
</tr>
<tr>
	<td colspan="4">
		<div
			style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 0px">
			<div class="">
				<table class="rich-table grayTable" cellpadding="0" cellspacing="0"
					border="0" width="100%">
					<thead class="rich-table-thead">
						<tr class="rich-table-subheader">
							<th class="rich-table-subheadercell" id="label_4_1_1">Request</th>
							<th class="rich-table-subheadercell" id="label_4_1_2">Amount</th>
							<th class="rich-table-subheadercell" id="label_4_1_3">Status Date</th>
						</tr>
					</thead>
					<tbody>
						<tr class="rich-table-row rowColor1">
							<td class="rich-table-cell" id="label_5_1_1" width="25%">Order
								Amount</td>
							<td class="rich-table-cell" id="label_5_1_2" width="30%">
								<div class="floatRight marginRight5">
								<? print $request->payment->paymentOutput->amountOfMoney->currencyCode. " + ".$request->payment->paymentOutput->amountOfMoney->amount/100;?>
									

								</div>
							</td>
							<td class="rich-table-cell" id="label_5_1_3"><?php print $request->payment->statusOutput->statusCodeChangeDateTime;?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</td>
</tr>
		





		


<div style="margin-left: 10px">
	
</div>

		





	<tr>
		<td colspan="4">
			<div
				style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 0px"">
				<div class="overallborderpop">
					<table class="tableGrid" cellspacing="0" width="100%"
						style="margin: 0px">
						<thead class="rich-table-thead">
							<tr class="rich-table-header" style="border: solid 10px #C0C0C0";>
								<th colspan="2" class="rich-table-headercell" id="label_6_1">Payment Summary</th>
							</tr>
						</thead>
						<tbody>
	
	
		<tr class="rich-table-row rowColor1 ">
			<td class="rich-table-cell " id="label_7_1_0" width="17%"><strong>Request Source:</strong></td>
			<td class="rich-table-cell" id="label_7_2_0" width="83%">API</td>
		</tr>
		<tr class="rich-table-row rowColor2 ">
			<td class="rich-table-cell  " id="label_7_3_0"><strong>Payment Product :</strong></td>
			<td class="rich-table-cell" id="label_7_4_0"><?print $row1[0]['card-type'];?></td>
		</tr>
		<tr class="rich-table-row rowColor1 ">
			<td class="rich-table-cell" id="label_7_5_0"><strong>Payment Reference :</strong></td>
			<td class="rich-table-cell" id="label_7_6_0">0</td>
		</tr>
		<tr class="rich-table-row rowColor2">
			<td class="rich-table-cell" id="label_7_7_0"><strong>Last Payment Status:</strong></td>
			<td class="rich-table-cell" id="label_7_8_0"><? print $request->payment->status."(".$request->payment->statusOutput->statusCode.")";?></td>
		</tr>
		
	
	<?print $row1[0]['card-type'];?>
		<tr class="rich-table-row rowColor1">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr class="rich-table-row rowColor2">
			<td class="rich-table-cell" id="label_7_9"><strong
				id="rejec_code">Rejection Code(s):</strong></td>
			<td class="rich-table-cell" id="label_7_10"><? print $request->payment->statusOutput->errors[0]->code; ?> .
 </td>
		</tr>
		<tr class="rich-table-row rowColor1">
			<td class="rich-table-cell" id="label_7_11"><strong
				id="error_type">Error Type(s):</strong></td>
			<td class="rich-table-cell" id="label_7_12">.
 </td>
		</tr>
		<tr class="rich-table-row rowColor2">
			<td class="rich-table-cell" id="label_7_13"><strong
				id="error_message">Error Message(s):</strong></td>
			<td class="rich-table-cell" id="label_7_14"><? print $request->payment->statusOutput->errors[0]->message."  ".$row1[0]['MErrMsg'];?>  .
 </td>
		</tr>
	
</tbody>
					</table>
				</div>
			</div>
		</td>
	</tr>


		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			
		</tr>
	</table>
</div>
	</div>


		</div>
	</div>
</div>
<!-- main wrapper container ends -->
<script>
	var date = new Date();
	 	date.setTime(date.getTime() + (10 * 60 * 1000));
		$(function() {
			$tabs = $( "#tabs" ).tabs({
				load: function (e, ui) {
				     $(ui.panel).find(".tab-loading").remove();
				},
				select: function (e, ui) {
					var $panel = $(ui.panel);
				    if ($panel.is(":empty")) {
				    	$panel.append("<span class='tab-loading'><img src='/wpc/resources/images/loader.gif'/></span>")
					}
				},
				cache:true,
				cookie: {
					expires:date,
					name:4000011730},
				ajaxOptions: {
					cache :false,
					error: function( xhr, status, index, anchor ) {
						$( anchor.hash ).html(
							"Couldn't load this tab. Please try to reload this page. ");
					}
				}
			});
		});
		
	</script>
</body>
</html>
