<?
$debug_email = "mcanul@theexcellencecollection.com";
# $debug_email = "juan.sarria@everlivesolutions.com, mirek@artbymobile.com";
# $debug_email = "mirek@artbymobile.com";



#	Simple Email Function
function sendEmail($to, $from, $subject, $message) {
	//+200319
	//$bcc = "mirek@artbymobile.com, reservations@grupoexcellence.com, mcanul@grupoexcellence.com, aruz@grupoexcellence.com";
  $bcc = "aruz@theexcellencecollection.com";
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
$updateQry="Este es un saludo desde Los servers de Mirek";
sendEmail($debug_email, "juanmcanul@gmail.com", "*** Test Envio Mail ***", $updateQry);

?>