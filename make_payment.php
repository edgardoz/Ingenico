<?php


$communicatorConfiguration = new CommunicatorConfiguration($ApiKeyId, $ApiSecret, $BaseUri, $Integrator);
$connection = new DefaultConnection();
$communicator = new Communicator($connection, $communicatorConfiguration);

$client = new Client($communicator);
//print "client: <pre>";print_r($client);print "</pre>";

/*
$card = new Card();
$card->cvv = isset($paymentDetails['cvv']) ? $paymentDetails['cvv'] : "321";

$cardPaymentMethodSpecificInput = new CardPaymentMethodSpecificInput();
$cardPaymentMethodSpecificInput->card = $card;
$cardPaymentMethodSpecificInput->requiresApproval = false;
$cardPaymentMethodSpecificInput->token = isset($paymentDetails['token']) ? $paymentDetails['token'] : "0edbf87e-ade8-4166-b37d-3f241e3e7178";

$name = new PersonalName();
$name->title = isset($paymentDetails['title']) ? $paymentDetails['title'] : "Mr.";
$name->firstName = isset($paymentDetails['firstName']) ? $paymentDetails['firstName'] : "Juan";
$name->surname = isset($paymentDetails['surname']) ? $paymentDetails['surname'] : "Sarria";

$contactDetails = new ContactDetails();
$contactDetails->emailAddress = isset($paymentDetails['emailAddress']) ? $paymentDetails['emailAddress'] : "jaunsarria@gmail.com";
$contactDetails->emailMessageType = "html";

$personalInformation = new PersonalInformation();
$personalInformation->name = $name;

$invoiceData = new OrderInvoiceData();
$invoiceData->invoiceNumber = isset($paymentDetails['invoiceNumber']) ? $paymentDetails['invoiceNumber'] : "518108228985187";

$references = new OrderReferences();
$references->invoiceData = $invoiceData;
$references->merchantReference = isset($paymentDetails['merchantReference']) ? $paymentDetails['merchantReference'] : "518108228985187-8"; // CHANGE THIS EVERY TIME

$amountOfMoney = new AmountOfMoney();
$amountOfMoney->amount = isset($paymentDetails['amount']) ? (int)$paymentDetails['amount'] : 155000;
$amountOfMoney->currencyCode = isset($paymentDetails['currencyCode']) ? $paymentDetails['currencyCode'] : "USD";

$customer = new Customer();
$customer->personalInformation = $personalInformation;
$customer->contactDetails = $contactDetails;

$order = new Order();
$order->amountOfMoney = $amountOfMoney;
$order->customer = $customer;
$order->references = $references;

$createPaymentRequest = new CreatePaymentRequest();
$createPaymentRequest->cardPaymentMethodSpecificInput = $cardPaymentMethodSpecificInput;
$createPaymentRequest->order = $order;

//print "createPaymentRequest: <pre>";print_r($createPaymentRequest);print "</pre>";

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
