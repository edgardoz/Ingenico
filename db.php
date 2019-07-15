<?
global $DB_SERVER;
global $DB_USER;
global $DB_PWD;
global $DB_NAME;

$B_SERVER_NAME = $_SERVER["SERVER_NAME"];

if (isset($INGENICO) && $INGENICO) {

    $webservice_1 = "http://locateandshare.com";
    $webservice_2 = "";
    $webservice_3 = "";
    $SITE = "Staging";

    $DB_SERVER = "localhost";
    $DB_USER = "juanccpsingenico";
    $DB_PWD = "ijV1u!04";
    $DB_NAME = "stg-ccps-ingenico";

} else {

    if ($B_SERVER_NAME=="secure-excellence-resorts.com" || $B_SERVER_NAME=="205.186.144.79") {

        $webservice_1 = "http://excellence-resorts.com";
        $webservice_2 = "http://secure-belovedhotels.com";
        $webservice_3 = "http://www.finestresorts.com";
        $SITE = "LIVE";

        $DB_SERVER = "localhost";
        $DB_USER = "juanccpsdb";
        $DB_PWD = "ijV1u!04";
        $DB_NAME = "new-ccps-db";

    }

}

$webservice_1 = "http://www.finestresorts.com";
$webservice_2 = "http://secure-belovedhotels.com";
$webservice_3 = "http://excellence-resorts.com";
$database = "stg-ccps-ingenico";
$user = "juanccpsingenico";
$pw = "ijV1u!04";
$SITE = "Staging";

	