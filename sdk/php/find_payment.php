<?php
namespace Ingenico\Connect\Sdk;
use DateTime;
use Ingenico\Connect\Sdk\Merchant\Payments\FindPaymentsParams;

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "autoloader.php";
autoload( __DIR__ . "/lib");
autoload( __DIR__ . "/src");


$merchantId = isset($_GET['merchantId']) ? $_GET['merchantId'] : "9690";
$merchantReference = isset($_GET['merchantReference']) ? $_GET['merchantReference'] : null;
$merchantOrderId = isset($_GET['merchantOrderId']) ? $_GET['merchantOrderId'] : "";

include "credentials.php";
$communicatorConfiguration = new CommunicatorConfiguration($ApiKeyId, $ApiSecret, $BaseUri, $Integrator);
$connection = new DefaultConnection();
$communicator = new Communicator($connection, $communicatorConfiguration);
$client = new Client($communicator);
$query = new FindPaymentsParams();
$query->merchantReference = $merchantReference;
$query->merchantOrderId = $merchantOrderId;
$query->offset = 0;
$query->limit = 10;


$result = array();

try {
    $result = $client->merchant($merchantId)->payments()->find($query);
} catch (DeclinedPaymentException $e) {
    $result = $e->getPaymentResult();
} catch (ApiException $e) {
    $result = $e->getErrors();
} catch (ResponseException $e) {
    $result = $e->getErrors()[0];
}

header('Content-Type: application/json');
echo is_object($result) ? json_encode($result) : $result;

?>