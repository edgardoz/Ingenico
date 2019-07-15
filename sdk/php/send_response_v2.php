<?php

ob_start();print $_REQUEST['response'];$output = ob_get_clean();//mail("juan.sarria@everlivesolutions.com","Ingenico Error ".time(),$output);

$to = "mcanul@grupoexcellence.com";
$subject = "Ingenico debug CCPS Tokenizador ".time();
$txt = $output;

$headers = "From: info@secure-excellence-resorts.com";
//$headers = "From: info@secure-excellence-resorts.com" . "\r\n" .
//"CC: aruz@grupoexcellence.com";

//mail($to,$subject,$txt,$headers);

$filename = $_SERVER['DOCUMENT_ROOT']."/ingenico/sdk/debug/" . str_replace(" ","",$_REQUEST['id']) . "_debug.txt";

// http://finestresorts.com/ibe/api/ingenico/debug/5589550061484228.json
file_put_contents($filename,$output."\r\n\r\n",FILE_APPEND);


header('Content-Type: application/json');
print $_REQUEST['response'];