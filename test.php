<?php
/*
$str = "20180805011201";

print date("Y-m-d h:i:s", strtotime($str));

$paymentJson = '{"category":null,"code":"300620","httpStatusCode":409,"id":null,"message":"MERCHANTREFERENCE 518108228985187-10 ALREADY EXISTS","propertyName":null,"requestId":"1024815"}';
#$paymentJson = '{"message":"Err"}';
#$paymentJson = '{"foo-bar": 12345}';

print "<pre>";print_r(json_decode($paymentJson, true));print "</pre>";
*/
$PAYDATA = array();


    ob_start();
      $url = "https://secure-excellence-resorts.com/ingenico/sdk/php/make_payment.php";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $PAYDATA);
      $paymentSent = curl_exec($ch);
      curl_close($ch);
    $paymentJson = ob_get_clean();

    print $paymentSent;
    $payment = json_decode($paymentJson, true);
    print "<br>payment: <pre>";print_r($payment);print "</pre>";
