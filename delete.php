<?php


		$paymentJson = '{"creationOutput":{"isNewToken":null,"token":null,"additionalReference":"519108499563344-1667","externalReference":"519108499563344-1667904"},"merchantAction":null,"payment":{"hostedCheckoutSpecificOutput":null,"paymentOutput":{"amountPaid":null,"bankTransferPaymentMethodSpecificOutput":null,"cardPaymentMethodSpecificOutput":{"authorisationCode":"003703","card":{"cardNumber":"493172******5674","expiryDate":"0920"},"fraudResults":{"avsResult":"0","cvvResult":"M","retailDecisions":null,"fraudServiceResult":"no-advice"},"threeDSecureResults":null,"token":null,"paymentProductId":1},"cashPaymentMethodSpecificOutput":null,"directDebitPaymentMethodSpecificOutput":null,"eInvoicePaymentMethodSpecificOutput":null,"invoicePaymentMethodSpecificOutput":null,"mobilePaymentMethodSpecificOutput":null,"paymentMethod":"card","redirectPaymentMethodSpecificOutput":null,"sepaDirectDebitPaymentMethodSpecificOutput":null,"amountOfMoney":{"amount":44000,"currencyCode":"USD"},"references":{"merchantOrderId":null,"merchantReference":"519108499563344-1667904","paymentReference":"0","providerId":"14000","providerReference":null,"referenceOrigPayment":null}},"status":"CAPTURE_REQUESTED","statusOutput":{"isAuthorized":true,"isRefundable":false,"errors":null,"isCancellable":true,"statusCategory":"PENDING_CONNECT_OR_3RD_PARTY","statusCode":800,"statusCodeChangeDateTime":"20190129222651"},"id":"000000969040000003630000100001"}}';

    $payment = json_decode($paymentJson, true);

		print "<pre>";print_r($payment);print "</pre>";