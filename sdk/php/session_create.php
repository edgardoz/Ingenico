<?php

namespace Ingenico\Connect\Sdk;

use Ingenico\Connect\Sdk\ApiException;
use Ingenico\Connect\Sdk\Domain\Definitions\Address;
use Ingenico\Connect\Sdk\Domain\Definitions\AmountOfMoney;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\CreateHostedCheckoutRequest;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\CreateHostedCheckoutResponse;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\GetHostedCheckoutResponse;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\Definitions\HostedCheckoutSpecificInput;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Customer;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Order;

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "autoloader.php";
autoload( __DIR__ . "/lib");
autoload( __DIR__ . "/src");

$merchantId = $_GET['merchantId'];

include "credentials.php";

$communicatorConfiguration = new CommunicatorConfiguration($ApiKeyId, $ApiSecret, $BaseUri, $Integrator);
$connection = new DefaultConnection();
$communicator = new Communicator($connection, $communicatorConfiguration);

$client = new Client($communicator);

$body = new CreateHostedCheckoutRequest();

$session = $client->merchant($merchantId)->sessions()->create($body);

//print "session: <pre>";print_r($session);print "</pre>";

header('Content-Type: application/json');
echo json_encode($session);

