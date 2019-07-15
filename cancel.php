<?
/*
 * Revised: Mar 25, 2011
 */
include_once "authenticate.php";
if (isset($isOk) && $isOk) {
    $current = "cancel";
    ?>
    <script type="text/javascript" charset="utf-8">
        $(function() {
            $('.date-pick').datePicker({clickInput:true});
            $("#myform").validate({
                rules: {
                    RES_ID: "required"
                },
                messages: {
                    RES_ID: "Please enter Reservation Number"
                }
            });
        });
    </script>
    <form id="myform" method="get" action="record.php" target="_blank">
        <h2>Submit Cancellation Manually</h2>
        <div>* = Required</div>
        <h3>Reservation Information</h3>
        <input type="hidden" name="TRANS_TYPE" id="TRANS_TYPE" value="2">
        <table>
        <tr>
            <td>Reservation ID <span>*<span></td>
            <td><input type="text" name="RES_ID" id="RES_ID"></td>
        </tr>
        <tr>
            <td>Admin Message</td>
            <td><textarea name="MSG" id="MSG"></textarea></td>
        </tr>
        </table>

        <p>
            <input type="submit" value="Cancel">
        </p>
    </form>
    <? 
}
include_once "close.php";
?>
