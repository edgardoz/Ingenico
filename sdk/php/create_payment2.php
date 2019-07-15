<?php

namespace Ingenico\Connect\Sdk;

use DateTime;
use Ingenico\Connect\Sdk\ApiException;
use Ingenico\Connect\Sdk\CallContext;
use Ingenico\Connect\Sdk\ClientTestCase;
use Ingenico\Connect\Sdk\ValidationException;
use Ingenico\Connect\Sdk\Domain\Definitions\Address;
use Ingenico\Connect\Sdk\Domain\Definitions\AmountOfMoney;
use Ingenico\Connect\Sdk\Domain\Definitions\Card;
use Ingenico\Connect\Sdk\Domain\Definitions\CompanyInformation;
//+
use Ingenico\Connect\Sdk\Domain\Definitions\FraudFields;
//-
use Ingenico\Connect\Sdk\Domain\Payment\ApprovePaymentRequest;
use Ingenico\Connect\Sdk\Domain\Payment\CancelApprovalPaymentResponse;
use Ingenico\Connect\Sdk\Domain\Payment\CancelPaymentResponse;
use Ingenico\Connect\Sdk\Domain\Payment\CreatePaymentRequest;
use Ingenico\Connect\Sdk\Domain\Payment\CreatePaymentResponse;
use Ingenico\Connect\Sdk\Domain\Payment\PaymentApprovalResponse;
use Ingenico\Connect\Sdk\Domain\Payment\PaymentResponse;
use Ingenico\Connect\Sdk\Domain\Payment\TokenizePaymentRequest;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\AddressPersonal;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\ApprovePaymentNonSepaDirectDebitPaymentMethodSpecificInput;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\CardPaymentMethodSpecificInput;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\ContactDetails;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Customer;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\LineItem;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\LineItemInvoiceData;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Order;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\OrderApprovePayment;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\OrderInvoiceData;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\OrderReferences;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\OrderReferencesApprovePayment;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\PersonalInformation;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\PersonalName;
use Ingenico\Connect\Sdk\Domain\Token\CreateTokenResponse;

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "autoloader.php";
autoload( __DIR__ . "/lib");
autoload( __DIR__ . "/src");


$merchantId = isset($_GET['merchantId']) ? $_GET['merchantId'] : "9690";

$paymentDetails = isset($_GET['paymentDetails']) ? $_GET['paymentDetails'] : null;

$encryptedCustomerInput = isset($_GET['encryptedCustomerInput']) ? $_GET['encryptedCustomerInput'] : "";

$paymentDetails = $_POST;
$merchantId=$_POST['merchantId'];


include "credentials.php";

$communicatorConfiguration = new CommunicatorConfiguration($ApiKeyId, $ApiSecret, $BaseUri, $Integrator);
$connection = new DefaultConnection();
$communicator = new Communicator($connection, $communicatorConfiguration);

$client = new Client($communicator);

$billingAddress = new Address();
$billingAddress->countryCode = "US";

$references = new OrderReferences();
$references->merchantOrderId = $paymentDetails['merchantOrderId'];
$references->merchantReference = $paymentDetails['merchantReference'];

$customer = new Customer();
$customer->billingAddress = $billingAddress;

$amountOfMoney = new AmountOfMoney();
//-050319
//$amountOfMoney->amount = 1;
$amountOfMoney->amount = 100; // In cents
//+050319
$amountOfMoney->currencyCode = $paymentDetails['currency'];

//-MC 22-02-2019
$fraudFields = new FraudFields();
$arrayReglas= array('','','','','reservation');
$fraudFields->userData=$arrayReglas;
//+MC 22-02-2019

$order = new Order();
$order->amountOfMoney = $amountOfMoney;
$order->customer = $customer;
$order->references = $references;

//+MC
$card = new Card();
$card->cardNumber = isset($paymentDetails['cardNumber']) ? $paymentDetails['cardNumber'] : "123";
$card->cardholderName = isset($paymentDetails['cardname']) ? $paymentDetails['cardname'] : "Error";
$card->cvv = isset($paymentDetails['cvv']) ? $paymentDetails['cvv'] : "321";
$card->expiryDate = isset($paymentDetails['expiryDate']) ? str_replace("/","",$paymentDetails['expiryDate']) : "0318";

//-MC


$cardPaymentMethodSpecificInput = new CardPaymentMethodSpecificInput();
$cardPaymentMethodSpecificInput->requiresApproval = true;
$cardPaymentMethodSpecificInput->tokenize = true;
//+ARUZ 27-02-19
//$cardPaymentMethodSpecificInput->skipFraudService = true;
$cardPaymentMethodSpecificInput->skipFraudService = false;
//-ARUZ 27-02-19
$cardPaymentMethodSpecificInput->card = $card;

$createPaymentRequest = new CreatePaymentRequest();
$createPaymentRequest->cardPaymentMethodSpecificInput = $cardPaymentMethodSpecificInput;
$createPaymentRequest->fraudFields=$fraudFields; //+MC 22-02-2019
$createPaymentRequest->order = $order;
$createPaymentRequest->encryptedCustomerInput = $encryptedCustomerInput;

//print "createPaymentRequest: <pre>";print_r($createPaymentRequest);print "</pre>";

//$filename = $_SERVER['DOCUMENT_ROOT']."/ingenico/sdk/debug/001_debug_on.txt";
//file_put_contents($filename,json_encode($createPaymentRequest)."\r\n\r\n",FILE_APPEND);


$result = array();

try {
    $result = $client->merchant($merchantId)->payments()->create($createPaymentRequest);
} catch (DeclinedPaymentException $e) {
    $result = $e->getPaymentResult();
} catch (ApiException $e) {
    $result = $e->getErrors();
} catch (ResponseException $e) {
    $result = $e->getErrors()[0];
}

header('Content-Type: application/json');
echo is_object($result) ? json_encode($result) : $result;