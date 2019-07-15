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
print "<br>".__DIR__ . "/sdk/php/lib"."<br>";
autoload( __DIR__ . "/sdk/php/lib");
autoload( __DIR__ . "/sdk/php/src");

include "credentials.php";

