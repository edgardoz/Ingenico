<?
/*
 * Revised: May 27, 2011
 */
include_once "authenticate.php";
if (isset($isOk) && $isOk) {
    $current = "run";
    ?>
    <style>
        input[type="text"], textarea {width:200px;}
    </style>
    <form method="get" action="process.php" target="_blank">
        <h2>Run Processing Manually</h2>
        <h3>Reservation Information</h3>
        <table>
        <tr>
            <td>Reservation ID <i>(Empty to process all)<i></td>
            <td><input type="text" name="RES_ID" id="RES_ID"></td>
        </tr>
        </table>
        <p>
            <input type="submit" value="Process Now">
        </p>
    </form>
    <? 
}
include_once "close.php";
?>
