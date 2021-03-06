<?
/*
 ********** JetPay
 * Revised: Jan 09. 2012
 *
 * Revised: Jan 19, 2013
 *          Aug 04, 2016
 *          Jul 11, 2017
 *          Aug 05, 2018 # INGENICO
 *          Nov 11, 2018
 *
 */
include_once "authenticate.php";
$abbrMonthNames = array('01-Jan', '02-Feb', '03-Mar', '04-Apr', '05-May', '06-Jun', '07-Jul', '08-Aug', '09-Sep', '10-Oct', '11-Nov', '12-Dec');
if (isset($isOk) && $isOk) {
    $current = "submit";
    
    include_once "edit.php";

    ?>
    <script type="text/javascript" charset="utf-8">
        $(function() {
            var EDIT = <?=$EDIT!=""?"true":"false"?>;
            $('.date-pick').datePicker({clickInput:true,startDate:'1996-01-01'});
            $("#card-exp-MM,#card-exp-YY").change(function() { setCardExp() });
            $("#card_country").change(function() { showStates() });
            $("#card_state_US,#card_state_CA").change(function() { setState($(this).val()) });

            var validate = {
                rules: {
                    card_name: "required",
                    card_country: "required",
                    card_state: "required",
                    card_address1: "required",
                    card_city: "required",
                    card_zip: "required",
                    card_amount: "required",
                    //card_number: {creditcard2: function() { return $('#card_type').val(); }}
                },
                messages: {
                    card_name: "Please enter Name on the Credit Card",
                    card_country: "Please enter Billing Country",
                    card_state: "Please enter Billing State",
                    card_address1: "Please enter Billing Address",
                    card_city: "Please enter Billing City",
                    card_zip: "Please enter Billing Zip Cde",
                    card_amount: "Please enter Total Amount to Charge",
                    //card_number: "Please enter a valid Credit Card Number"
                }
            }

            if (!EDIT) {
              validate.rules.card_number = "required";
              validate.rules.card_cvv = "required";

              validate.messages.card_number = "Please enter Reservation Number";
              validate.messages.card_cvv = "Please enter Name on the CVV";
            }

            $("#myform").validate(validate);
        });
        function setCardExp() {
            //MM/YY
            var MM = $("#card-exp-MM"),
                YY = $("#card-exp-YY"),
                Exp = $("#card_exp");
            Exp.val(MM.val()+"/"+YY.val());
        }
        function showStates() {
            var country = $("#card_country").val(),
                code = (country=="US" || country=="CA") ? "_"+country : "";
            $(".card_state").hide();
            $("#card_state"+code).show();
            $("#card_state_US,#card_state_CA").get(0).selectedIndex = 0;
            //$("#card_state").val("");
        }
        function setState(code) {
            $("#card_state").val(code);
        }
        function validate() {
            setCardExp();
        }
    </script>

    <form id="myform" method="post" action="record.php" <? /*if (!isset($UPDATE))*/ print "target='ws'" ?> onSubmit="validate()">
        <input type="hidden" name="UPDATE" id="UPDATE" value="<? if (isset($UPDATE)) print $UPDATE ?>">
        <input type="hidden" name="QS" id="QS" value="<? if (isset($_REQUEST['qs'])) print $_REQUEST['qs'] ?>">
        <? 
        if (isset($UPDATE)) {
            print "
                <h2>Edit/Modify Transaction</h2>
                <p>
                <input type='button' value='Go Back' onClick='editGoBack()'>
                </p>
            ";
        } else {
            print "<h2>Submit Transaction Manually</h2>";
        }
        ?>
        <div>* = Required</div>
        <?
        if (isset($UPDATE) && isset($row['FinalStatus']) && !empty($row['FinalStatus']) ) { ?>
            <h3>Authorization</h3>
            <table>
            <tr>
                <td>Final Status</td>
                <td><? if (isset($row['FinalStatus'])) print $row['FinalStatus'] ?></td>
            </tr>
            <tr>
                <td>Err. Message</td>
                <td><? if (isset($row['MErrMsg'])) print $row['MErrMsg'] ?></td>
            </tr>
            </table>
        <? } ?>

        <h3>Reservation Information</h3>
        <table>

        <? if (!isset($UPDATE)) { ?>
        <tr>
            <td>Transaction Date (<i><span style='font-size:10px'>Default Today</span></i>)</td>
            <td><input type="text" name="CREATED" id="CREATED" class="date-pick"></td>
        </tr>
        <? } else { ?>
        <tr>
            <td>Status</td>
            <td>
                <select name="STATUS" id="STATUS">
                    <option value="0" <? if (isset($row['STATUS'])&&(int)$row['STATUS']==0) print "selected" ?>>Pending</option>
                    <option value="2" <? if (isset($row['STATUS'])&&(int)$row['STATUS']==2) print "selected" ?>>Cancelled</option>
                    <option value="-1" <? if (isset($row['STATUS'])&&(int)$row['STATUS']==-1) print "selected" ?>>Failed</option>
                    <option value="1" <? if (isset($row['STATUS'])&&(int)$row['STATUS']==1) print "selected" ?>>Charged</option>
                    <option value="-99" <? if (isset($row['STATUS'])&&(int)$row['STATUS']==-99) print "selected" ?>>On Going</option>
                </select>
            </td>
        </tr>
        <? } ?>
        <tr>
            <td>Reservation ID <span>*<span></td>
            <td><input type="text" name="RES_ID" id="RES_ID" value="<? if (isset($RES_ID)) print $RES_ID ?>"></td>
        </tr>
        <tr>
            <td>Property<span>*<span></td>
            <td style="height:20px;">
            <? if (!isset($UPDATE)) { ?>
                <select name="publisher_name" id="publisher_name">
                    <option value="excellence" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence") print "selected" ?>>Punta Cana, Dominican Rep.</option>
                    <option value="excellence2" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence2") print "selected" ?>>Riviera Cancun, Mexico</option>
                    <option value="excellence3" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence3") print "selected" ?>>Playa Mujeres, Mexico</option>
                    <option value="excellence4" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence4") print "selected" ?>>Beloved Playa Mujeres</option>
                    <option value="excellence1" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence1") print "selected" ?>>Finest Resorts Playa Mujeres</option>
                    <option value="excellence5" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence5") print "selected" ?>>Excellence El Carmen</option>
                    <option value="excellence6" <? if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence6") print "selected" ?>>Excellence Oyster Bay</option>
                </select>
            <? } else { 
                //$row['publisher-name'] = $row['publisher-name']=="excellence6" ? "excellence5":$row['publisher-name'];
                ?>
                <input type="hidden" name="publisher_name" id="publisher_name" value="<? if (isset($row['publisher-name'])) print $row['publisher-name'] ?>">&nbsp;
                <b>
                <?
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence") print "Punta Cana, Dominican Rep.";
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence2") print "Riviera Cancun, Mexico";
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence3") print "Playa Mujeres, Mexic";
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence4") print "Beloved Playa Mujeres";
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence1") print "Finest Resorts Playa Mujeres";
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence5") print "Excellence El Carmen";
                if (isset($row['publisher-name'])&&$row['publisher-name']=="excellence6") print "Excellence Oyster Bay";
                ?>  
                </b>
            <? } ?>
            </td>
        </tr>
        <tr>
            <td>Guest Name</td>
            <td><input type="text" name="GUEST_NAME" id="GUEST_NAME" value="<? if (isset($GUEST_NAME)) print $GUEST_NAME ?>"></td>
        </tr>
        <tr>
            <td>Guest Email</td>
            <td><input type="text" name="GUEST_EMAIL" id="GUEST_EMAIL" value="<? if (isset($GUEST_EMAIL)) print $GUEST_EMAIL ?>"></td>
        </tr>
        <tr>
            <td>Check In Date</td>
            <td><input type="text" name="CHECK_IN" id="CHECK_IN" class="date-pick" value="<? if (isset($CHECK_IN)) print substr($CHECK_IN,0,10) ?>"></td>
        </tr>
        <tr>
            <td>Check Out Date</td>
            <td><input type="text" name="CHECK_OUT" id="CHECK_OUT" class="date-pick" value="<? if (isset($CHECK_OUT)) print substr($CHECK_OUT,0,10) ?>"></td>
        </tr>
        <tr>
            <td>Number of Rooms</td>
            <td>
                <select name="ROOMS" id="ROOMS">
                    <? for ($t=1;$t<=10;++$t) print "<option value='{$t}'".((isset($ROOMS)&&(int)$ROOMS==$t)?"selected":"").">{$t}</option>";?>
                </select>        
            </td>
        </tr>
        <tr>
            <td>Number of Guests</td>
            <td>
                <select name="GUESTS" id="GUESTS">
                    <? for ($t=1;$t<=10;++$t) print "<option value='{$t}'".((isset($GUESTS)&&(int)$GUESTS==$t)?"selected":"").">{$t}</option>";?>
                </select>        
            </td>
        </tr>
        <tr>
            <td>Admin Message</td>
            <td>
                <div><? if (isset($MSG)) print $MSG ?></div>
                <textarea name="MSG" id="MSG"></textarea>
            </td>
        </tr>
        </table>

        <h3>Credit Card Information</h3>
        <table>
        <tr>
            <td>Type</td>
            <td>
                <select id="card_type" name="card_type" <?//=$EDIT!=""?"disabled":""?>>
                    <option value="Visa" <? if (isset($row['card-type'])&&$row['card-type']=="Visa") print "selected" ?>>Visa</option>
                    <option value="MasterCard" <? if (isset($row['card-type'])&&$row['card-type']=="MasterCard") print "selected" ?>>MasterCard</option>
                    <option value="AmEx" <? if (isset($row['card-type'])&&$row['card-type']=="AmEx") print "selected" ?>>American Express</option>
                    <!--
                    <option value="Discover">Discover</option>
                    <option value="DinersClub">DinersClub</option>
                    <option value="CarteBlanche">CarteBlanche</option>
                    <option value="enRoute">enRoute</option>
                    <option value="JCB">JCB</option>
                    <option value="LaserCard">LaserCard</option>
                    <option value="Maestro">Maestro</option>
                    <option value="Solo">Solo</option>
                    <option value="Switch">Switch</option>
                    <option value="VisaElectron">VisaElectron</option>
                    -->
                </select>
            </td>
        </tr>
        <tr>
            <td>Number <span>*<span></td>
            <td><input type="text" name="card_number" id="card_number" value="<?// print (isset($row['card-number'])) ? $row['card-number'] : "" ?>" <?//=$EDIT!=""?"disabled":""?>></td>
        </tr>
        <tr>
            <td>Credit Card Expiration Date <span>*<span></td>
            <td>
                <input type="hidden" name="card_exp" id="card_exp" value="<?// if (isset($row['card-exp'])) print $row['card-exp'] ?>" <?//=$EDIT!=""?"disabled":""?>>
                <select name="card-exp-MM" id="card-exp-MM" <?//=$EDIT!=""?"disabled":""?>>
                    <?
                    $MM=1;
                    foreach($abbrMonthNames as $key) {
                        print "<option value='".(($MM>9)?$MM:"0".$MM)."'".((isset($expMM)&&(int)$expMM==$MM)?"selected":"").">{$key}</option>";
                        ++$MM;
                    }
                    ?>
                </select>
                /
                <select name="card-exp-YY" id="card-exp-YY" <?//=$EDIT!=""?"disabled":""?>>
                    <?
                    for ($YY=date("Y");$YY<=date("Y")+10;++$YY) print "<option value='".($YY - 2000)."'".((isset($expYY)&&(int)$expYY==($YY - 2000))?"selected":"").">{$YY}</option>";
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>CVV <span>*<span></td>
            <td><input type="text" name="card_cvv" id="card_cvv" value="<? print (isset($row['card-cvv'])) ? $row['card-cvv'] : "" ?>" <?//=$EDIT!=""?"disabled":""?>></td>
        </tr>
        <tr>
            <td>TOKEN</td>
            <td>
              <input type="text" id="INGENICO_TOKEN_PREV" value="<? print (isset($row['INGENICO_TOKEN'])) ? $row['INGENICO_TOKEN'] : "" ?>" disabled>
              <input type="hidden" name="INGENICO_TOKEN" id="INGENICO_TOKEN" value="<? print (isset($row['INGENICO_TOKEN'])) ? $row['INGENICO_TOKEN'] : "" ?>">
            </td>
        </tr>
        <tr>
            <td>Total Amount <span>*<span></td>
            <td><input type="text" name="card_amount" id="card_amount" value="<? if (isset($row['card-amount'])) print $row['card-amount'] ?>"></td>
        </tr>
        <tr>
            <td>Name as it Appears on CC <span>*<span></td>
            <td><input type="text" name="card_name" id="card_name" value="<? print (isset($row['card-name'])) ? $row['card-name'] : "" ?>"></td>
        </tr>
        <tr>
            <td>Billing Country <span>*<span></td>
            <td>
                <select id="card_country" name="card_country">
                    <option value=""></option>
                    <option value="US">United States</option>
                    <option value="CA">Canada</option>
                    <option value="GB">United Kingdom</option>
                    <option value="MX">Mexico</option>
                    <option value="AD">Andorra</option>
                    <option value="AE">United Arab Emirates</option>
                    <option value="AF">Afghanistan</option>
                    <option value="AG">Antigua and Barbuda</option>
                    <option value="AI">Anguilla</option>
                    <option value="AL">Albania</option>
                    <option value="AM">Armenia</option>
                    <option value="AW">Aruba</option>
                    <option value="AN">Netherlands Antilles</option>
                    <option value="AO">Angola</option>
                    <option value="AQ">Antarctica</option>
                    <option value="AR">Argentina</option>
                    <option value="AS">American Samoa</option>
                    <option value="AT">Austria</option>
                    <option value="AU">Australia</option>
                    <option value="AW">Aruba</option>
                    <option value="AZ">Azerbaidjan</option>
                    <option value="BA">Bosnia-Herzegovina</option>
                    <option value="BB">Barbados</option>
                    <option value="BD">Bangladesh</option>
                    <option value="BE">Belgium</option>
                    <option value="BF">Burkina Faso</option>
                    <option value="BG">Bulgaria</option>
                    <option value="BH">Bahrain</option>
                    <option value="BI">Burundi</option>
                    <option value="BJ">Benin</option>
                    <option value="BM">Bermuda</option>
                    <option value="BN">Brunei Darussalam</option>
                    <option value="BO">Bolivia</option>
                    <option value="BR">Brazil</option>
                    <option value="BS">Bahamas</option>
                    <option value="BT">Bhutan</option>
                    <option value="BV">Bouvet Island</option>
                    <option value="BW">Botswana</option>
                    <option value="BY">Belarus</option>
                    <option value="BZ">Belize</option>
                    <option value="BM">Bermuda</option>                    
                    <option value="CC">Cocos (Keeling) Islands</option>
                    <option value="CF">Central African Republic</option>
                    <option value="CG">Congo</option>
                    <option value="CH">Switzerland</option>
                    <option value="CI">Ivory Coast (Cote D'Ivoire)</option>
                    <option value="CK">Cook Islands</option>
                    <option value="CL">Chile</option>
                    <option value="CM">Cameroon</option>
                    <option value="CN">China</option>
                    <option value="CO">Colombia</option>
                    <option value="CR">Costa Rica</option>
                    <option value="CU">Cuba</option>
                    <option value="CV">Cape Verde</option>
                    <option value="CX">Christmas Island</option>
                    <option value="CY">Cyprus</option>
                    <option value="CZ">Czech Republic</option>
                    <option value="DE">Germany</option>
                    <option value="DJ">Djibouti</option>
                    <option value="DK">Denmark</option>
                    <option value="DM">Dominica</option>
                    <option value="DO">Dominican Republic</option>
                    <option value="DZ">Algeria</option>
                    <option value="EC">Ecuador</option>
                    <option value="EE">Estonia</option>
                    <option value="EG">Egypt</option>
                    <option value="EH">Western Sahara</option>
                    <option value="ES">Spain</option>
                    <option value="ET">Ethiopia</option>
                    <option value="FI">Finland</option>
                    <option value="FJ">Fiji</option>
                    <option value="FK">Falkland Islands</option>
                    <option value="FM">Micronesia</option>
                    <option value="FO">Faroe Islands</option>
                    <option value="FR">France</option>
                    <option value="FX">France (European Territory)</option>
                    <option value="GA">Gabon</option>
                    <option value="GD">Grenada</option>
                    <option value="GE">Georgia</option>
                    <option value="GF">French Guyana</option>
                    <option value="GH">Ghana</option>
                    <option value="GI">Gibraltar</option>
                    <option value="GL">Greenland</option>
                    <option value="GM">Gambia</option>
                    <option value="GN">Guinea</option>
                    <option value="GP">Guadeloupe (French)</option>
                    <option value="GQ">Equatorial Guinea</option>
                    <option value="GR">Greece</option>
                    <option value="GS">S. Georgia & S. Sandwich Isls.</option>
                    <option value="GT">Guatemala</option>
                    <option value="GU">Guam (USA)</option>
                    <option value="GW">Guinea Bissau</option>
                    <option value="GY">Guyana</option>
                    <option value="HK">Hong Kong</option>
                    <option value="HM">Heard and McDonald Islands</option>
                    <option value="HN">Honduras</option>
                    <option value="HR">Croatia</option>
                    <option value="HT">Haiti</option>
                    <option value="HU">Hungary</option>
                    <option value="ID">Indonesia</option>
                    <option value="IE">Ireland</option>
                    <option value="IL">Israel</option>
                    <option value="IN">India</option>
                    <option value="IO">British Indian Ocean Territory</option>
                    <option value="IQ">Iraq</option>
                    <option value="IR">Iran</option>
                    <option value="IS">Iceland</option>
                    <option value="IT">Italy</option>
                    <option value="JM">Jamaica</option>
                    <option value="JO">Jordan</option>
                    <option value="JP">Japan</option>
                    <option value="KE">Kenya</option>
                    <option value="KG">Kyrgyzstan</option>
                    <option value="KH">Cambodia</option>
                    <option value="KI">Kiribati</option>
                    <option value="KM">Comoros</option>
                    <option value="KN">Saint Kitts & Nevis Anguilla</option>
                    <option value="KP">North Korea</option>
                    <option value="KR">South Korea</option>
                    <option value="KW">Kuwait</option>
                    <option value="KY">Cayman Islands</option>
                    <option value="KZ">Kazakhstan</option>
                    <option value="LA">Laos</option>
                    <option value="LB">Lebanon</option>
                    <option value="LC">Saint Lucia</option>
                    <option value="LI">Liechtenstein</option>
                    <option value="LK">Sri Lanka</option>
                    <option value="LR">Liberia</option>
                    <option value="LS">Lesotho</option>
                    <option value="LT">Lithuania</option>
                    <option value="LU">Luxembourg</option>
                    <option value="LV">Latvia</option>
                    <option value="LY">Libya</option>
                    <option value="MA">Morocco</option>
                    <option value="MC">Monaco</option>
                    <option value="MD">Moldavia</option>
                    <option value="MG">Madagascar</option>
                    <option value="MH">Marshall Islands</option>
                    <option value="MI">USA Military</option>
                    <option value="MK">Macedonia</option>
                    <option value="ML">Mali</option>
                    <option value="MM">Myanmar</option>
                    <option value="MN">Mongolia</option>
                    <option value="MO">Macau</option>
                    <option value="MP">Northern Mariana Islands</option>
                    <option value="MQ">Martinique (French)</option>
                    <option value="MR">Mauritania</option>
                    <option value="MS">Montserrat</option>
                    <option value="MT">Malta</option>
                    <option value="MU">Mauritius</option>
                    <option value="MV">Maldives</option>
                    <option value="MW">Malawi</option>
                    <option value="MY">Malaysia</option>
                    <option value="MZ">Mozambique</option>
                    <option value="NA">Namibia</option>
                    <option value="NC">New Caledonia (French)</option>
                    <option value="NE">Niger</option>
                    <option value="NF">Norfolk Island</option>
                    <option value="NG">Nigeria</option>
                    <option value="NI">Nicaragua</option>
                    <option value="NL">Netherlands</option>
                    <option value="NO">Norway</option>
                    <option value="NP">Nepal</option>
                    <option value="NR">Nauru</option>
                    <option value="NT">Neutral Zone</option>
                    <option value="NU">Niue</option>
                    <option value="NZ">New Zealand</option>
                    <option value="OM">Oman</option>
                    <option value="PA">Panama</option>
                    <option value="PE">Peru</option>
                    <option value="PF">Polynesia (French)</option>
                    <option value="PG">Papua New Guinea</option>
                    <option value="PH">Philippines</option>
                    <option value="PK">Pakistan</option>
                    <option value="PL">Poland</option>
                    <option value="PM">Saint Pierre and Miquelon</option>
                    <option value="PN">Pitcairn Island</option>
                    <option value="PR">Puerto Rico</option>
                    <option value="PT">Portugal</option>
                    <option value="PW">Palau</option>
                    <option value="PY">Paraguay</option>
                    <option value="QA">Qatar</option>
                    <option value="RE">Reunion (French)</option>
                    <option value="RO">Romania</option>
                    <option value="RU">Russian Federation</option>
                    <option value="RW">Rwanda</option>
                    <option value="SA">Saudi Arabia</option>
                    <option value="SB">Solomon Islands</option>
                    <option value="SC">Seychelles</option>
                    <option value="SD">Sudan</option>
                    <option value="SE">Sweden</option>
                    <option value="SG">Singapore</option>
                    <option value="SH">Saint Helena</option>
                    <option value="SI">Slovenia</option>
                    <option value="SJ">Svalbard and Jan Mayen Islands</option>
                    <option value="SK">Slovak Republic</option>
                    <option value="SL">Sierra Leone</option>
                    <option value="SM">San Marino</option>
                    <option value="SN">Senegal</option>
                    <option value="SO">Somalia</option>
                    <option value="SR">Suriname</option>
                    <option value="ST">Saint Tome (Sao Tome) and Principe</option>
                    <option value="SU">Former USSR</option>
                    <option value="SV">El Salvador</option>
                    <option value="SY">Syria</option>
                    <option value="SZ">Swaziland</option>
                    <option value="TC">Turks and Caicos Islands</option>
                    <option value="TD">Chad</option>
                    <option value="TF">French Southern Territories</option>
                    <option value="TG">Togo</option>
                    <option value="TH">Thailand</option>
                    <option value="TJ">Tadjikistan</option>
                    <option value="TK">Tokelau</option>
                    <option value="TM">Turkmenistan</option>
                    <option value="TN">Tunisia</option>
                    <option value="TO">Tonga</option>
                    <option value="TP">East Timor</option>
                    <option value="TR">Turkey</option>
                    <option value="TT">Trinidad and Tobago</option>
                    <option value="TV">Tuvalu</option>
                    <option value="TW">Taiwan</option>
                    <option value="TZ">Tanzania</option>
                    <option value="UA">Ukraine</option>
                    <option value="UG">Uganda</option>
                    <option value="UM">USA Minor Outlying Islands</option>
                    <option value="UY">Uruguay</option>
                    <option value="UZ">Uzbekistan</option>
                    <option value="VA">Vatican City State</option>
                    <option value="VC">Saint Vincent & Grenadines</option>
                    <option value="VE">Venezuela</option>
                    <option value="VG">Virgin Islands (British)</option>
                    <option value="VI">Virgin Islands (USA)</option>
                    <option value="VN">Vietnam</option>
                    <option value="VU">Vanuatu</option>
                    <option value="WF">Wallis and Futuna Islands</option>
                    <option value="WS">Samoa</option>
                    <option value="YE">Yemen</option>
                    <option value="YT">Mayotte</option>
                    <option value="YU">Yugoslavia</option>
                    <option value="ZA">South Africa</option>
                    <option value="ZM">Zambia</option>
                    <option value="ZR">Zaire</option>
                    <option value="ZW">Zimbabwe</option>
                </select>
            </td>
        </tr>
       <tr>
            <td>Billing State <span>*<span></td>
            <td>
                <input type="text" class="card_state" name="card_state" id="card_state" value="<? if (isset($row['card-state'])) print $row['card-state'] ?>" style="display:">
                <select class="card_state" id="card_state_US" style="display:none">
                    <option value=""></option>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CA">California</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="DC">District of Columbia</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="ME">Maine</option>
                    <option value="MD">Maryland</option>
                    <option value="MA">Massachusetts</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NJ">New Jersey</option>
                    <option value="NM">New Mexico</option>
                    <option value="NY">New York</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OH">Ohio</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="PR">Puerto Rico</option>
                    <option value="RI">Rhode Island</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VT">Vermont</option>
                    <option value="VI">Virgin Islands</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                    <option value="AA">Armed Forces America</option>
                    <option value="AE">Armed Forces Other Areas</option>
                    <option value="AS">American Samoa</option>
                    <option value="AP">Armed Forces Pacific</option>
                    <option value="GU">Guam</option>
                    <option value="MH">Marshall Islands</option>
                    <option value="FM">Micronesia</option>
                    <option value="MP">Northern Mariana Islands</option>
                    <option value="PW">Palau</option>
                </select>
                <select class="card_state" id="card_state_CA" style="display:none">
                    <option value=""></option>
                    <option value="AB">Alberta</option>
                    <option value="BC">British Columbia</option>
                    <option value="NB">New Brunswick</option>
                    <option value="MB">Manitoba</option>
                    <option value="NF">Newfoundland</option>
                    <option value="NT">Northwest Territories</option>
                    <option value="NS">Nova Scotia</option>
                    <option value="ON">Ontario</option>
                    <option value="PE">Prince Edward Island</option>
                    <option value="QC">Quebec</option>
                    <option value="SK">Saskatchewan</option>
                    <option value="YT">Yukon</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Billing Street <span>*<span></td>
            <td><input type="text" name="card_address1" id="card_address1" value="<? if (isset($row['card-address1'])) print $row['card-address1'] ?>"></td>
        </tr>
        <tr>
            <td>Billing City <span>*<span></td>
            <td><input type="text" name="card_city" id="card_city" value="<? if (isset($row['card-city'])) print $row['card-city'] ?>"></td>
        </tr>
        <tr>
            <td>Billing Zip <span>*<span></td>
            <td><input type="text" name="card_zip" id="card_zip" value="<? if (isset($row['card-zip'])) print $row['card-zip'] ?>"></td>
        </tr>
        <tr>
            <td>Billing Email</td>
            <td><input type="text" name="email" id="email" value="<? if (isset($row['email'])) print $row['email'] ?>"></td>
        </tr>
        </table>

        <h3>Administration</h3>
        <table>
        <tr>
            <td>Admin Email</td>
            <td><input type="text" name="admin-email" id="admin-email" value="<? if (isset($row['admin-email'])) print $row['admin-email'] ?>"></td>
        </tr>
        </table>

        <p>

            <input type="submit" value="Submit" id="submit" style="display:none<?//=isset($UPDATE)?"block":"none"?>">
            <input id="btn-book-now" type="button" value="Book Now" onClick="book_now()" style="display:<?//=isset($UPDATE)?"none":"block"?>">
            <div id="ingenico_debug"></div>

        </p>
    </form>

		<?//= isset($UPDATE) ? "UPDATE" : "OPS"; ?>

    <?// if (!isset($UPDATE)) { ?>

    <script src="/ingenico/sdk/js/dist/connectsdk.js"></script>

    <script>

        function ingenicoStartPayment(session, merchantId) {

          console.log("Ingenico session", session);

          var merchantOrderId = Math.floor((Math.random() * 4294967295) + 1);

          var formatCC = function(type, str) {
              // #### #### #### #### (4-4-4-4) Visa
              // #### ###### ##### (4-6-5) AmEx
              return (type=="AmEx") ? str.substr(0, 4) + " " +  str.substr(4, 6) + " " +  str.substr(10, 5) : str.substr(0, 4) + " " +  str.substr(4, 4) + " " +  str.substr(8, 4) + " " + str.substr(12, 4)
            }

          paymentDetails = { 
            totalAmount: 1, // in cents
            countryCode: $("#card_country").val(),
            currency: "USD", // set currency, see dropdown
            locale: "en_US", // as specified in the config center
            isRecurring: false, // set if recurring

            merchantOrderId: merchantOrderId,
            merchantReference: "AUTH_CCPS_" + merchantOrderId,

            cardNumber: formatCC($("#card_type").val(), $("#card_number").val()),
            cvv: $("#card_cvv").val(),
            expiryDate: $("#card-exp-MM").val() + " " + $("#card-exp-YY").val()
          };

          var paymentProductId = 1; // Visa

          switch($("#card_type").val()) {
            case "AmEx":
              paymentProductId = 2;
              break;
            case "MasterCard":
              paymentProductId = 3;
              break;
          } 

          console.log("paymentDetails", paymentDetails);
          console.log("paymentProductId", paymentProductId);

          var paymentRequest = session.getPaymentRequest();

          session.getPaymentProduct(paymentProductId, paymentDetails, true).then(function(paymentProduct) {

            paymentRequest.setPaymentProduct(paymentProduct);
            paymentRequest.setValue("cardNumber", paymentDetails.cardNumber); // This should be the unmasked value. 
            paymentRequest.setValue("cvv", paymentDetails.cvv);
            paymentRequest.setValue("expiryDate", paymentDetails.expiryDate);
             
            if (!paymentRequest.isValid()) {
              // We have validation errors.
              console.log("We have validation errors",  paymentRequest.getErrorMessageIds()); //This is an array of all the validation errors
            }

            console.log("getting ready to encryptor");

            var encryptor = session.getEncryptor();

            encryptor.encrypt(paymentRequest).then(function(encryptedCustomerInput) {
              
              console.log("encryptedCustomerInput -> ", encryptedCustomerInput)
              $('#divEncriptedResult').html(encryptedCustomerInput);

              console.log("paymentRequest")

              $.get("/ingenico/sdk/php/create_payment.php", {
                merchantId: merchantId,
                paymentDetails: paymentDetails,
                encryptedCustomerInput: encryptedCustomerInput
              })
              .done(function(response) {

									console.log("response", response)
                                    //+050319
									//if (response.payment.statusOutput.errors === null) {
                                    //MC- 26-06-19    
                                    //if (typeof response.creationOutput.token != "undefined" && response.creationOutput.token
                                    //    && response.payment.paymentOutput.cardPaymentMethodSpecificOutput.fraudResults.cvvResult != null
                                    //    && response.payment.paymentOutput.cardPaymentMethodSpecificOutput.fraudResults.cvvResult === 'M'
                                    //    && response.payment.paymentOutput.cardPaymentMethodSpecificOutput.fraudResults.fraudServiceResult === 'accepted'                                                 
                                    //) {
                                    //MC+ 26-06-19
                                    if (typeof response.creationOutput.token != "undefined" && response.creationOutput.token
                                          && response.payment.status === 'ACCOUNT_VERIFIED' && response.payment.statusOutput.statusCategory === 'ACCOUNT_VERIFIED'        
                                    ) {        
                                    //-050319
										$("#INGENICO_TOKEN").val(response.creationOutput.token);
										$("#INGENICO_TOKEN_PREV").val(response.creationOutput.token);
										//$("#card_number").val(response.payment.paymentOutput.cardPaymentMethodSpecificOutput.card.cardNumber);

										make_booking();


									} else if ((paymentDetails.cardNumber=="4111 1111 1111 1111" || paymentDetails.cardNumber=="4000 0243 2959 6391" || paymentDetails.cardNumber=="4917 4845 8989 7107") && paymentDetails.cvv=="123") {

										var faketoken = "-TESTING-"+paymentDetails.cardNumber.replace(/\s+/g,"-");

										$("#INGENICO_TOKEN").val(faketoken);
										$("#INGENICO_TOKEN_PREV").val(faketoken);

										make_booking();

									} else {

										var Err = [];
										for (var t=0; t < response.payment.statusOutput.errors.length; ++t) {
											Err.push(response.payment.statusOutput.errors[t].message)
										}

										alert("Error\n * "+Err.join("\n* "));
										$("#btn-book-now").show();
										$("#ingenico_debug").html("");

									}

              })
              .fail(function(result) {
                console.log("Ingenico Create Payment Response Error\n"+JSON.stringify(result));
                make_booking();
              });


            }, function(errors) {
								error(errors, 1)
            });

          }, function(errors) {
							error(errors, 2)
          });

        }

				function error(errors, n) {
              console.log("Ingenico promise failed 1\n", JSON.stringify(errors))
							alert("Error "+n+"\n\n" + errors.join(","))
							$("#btn-book-now").show();
							$("#ingenico_debug").html("");
              //make_booking();				
				}

        function make_booking() {
          $("#btn-book-now").show();
					$("#ingenico_debug").html("");
          $("#submit").click();
        }

        function book_now() {

					//alert("CC " + $("#card_number").val())

					if ($("#card_number").val()=="") {
						$("#submit").click();
						return true;
					}

          // INGENICO

          $("#btn-book-now").hide();
					$("#ingenico_debug").html("<h2>Wait... Getting new token</h2>");

          /*
          1. XRC - 9694
          2. XPM - 9692
          3. XPC - 9696
          4. TBH - 9695
          5. FPM - 9690
          6. XEC - 9691
          7. XOB - 9693
          */

          var merchantIDs = {
            "excellence2":"9694",
            "excellence3":"9692",
            "excellence":"9696",
            "excellence4":"9695",
            "excellence1":"9690",
            "excellence5":"9691",
            "excellence6":"9693",
          }

          var publisher_name = jQuery("#publisher_name").val();

          var merchantId = merchantIDs[publisher_name];

					$.ajax({
							url: "/ingenico/sdk/php/session_create.php?merchantId="+merchantId,
							success: function(data) {
								console.log("data", data);

								var session = new connectsdk.Session(data);

								ingenicoStartPayment(session, merchantId);
							},
					error: function(result) {
								alert("Error Creating Ingenico Session!\n" + JSON.stringify(result));
							},
					});

        }
    </script>
      <?// } ?>

    <?
    if (isset($UPDATE)) { ?>
        <script>
            $("#card_country").val('<? print $row['card-country'] ?>');
            showStates();
            $("#card_state_US").val('<? print $row['card-state'] ?>')
            $("#card_state_CA").val('<? print $row['card-state'] ?>')
        </script>
    <? }
}
include_once "close.php";
?>
