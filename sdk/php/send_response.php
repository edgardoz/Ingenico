<?php

ob_start();print $_REQUEST['response'];$output = ob_get_clean();//mail("juan.sarria@everlivesolutions.com","Ingenico Error ".time(),$output);

$to = "juan.sarria@everlivesolutions.com";
$subject = "Ingenico Error ".time();
$txt = $output;
$headers = "From: info@secure-excellence-resorts.com" . "\r\n" .
"CC: mirek@basedesign.com";

mail($to,$subject,$txt,$headers);


header('Content-Type: application/json');
print $_REQUEST['response'];