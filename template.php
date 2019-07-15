<? 
if (!isset($EXPORT)) $EXPORT = 0;
if (!isset($current)) $current = "";
    
if (!$EXPORT) { ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>
        <title>All Inclusive Luxury Caribbean Resorts - Excellence Group Resorts</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
        <script type="text/javascript" src="js/date.js"></script>
        <script type="text/javascript" src="js/jquery.datePicker.js"></script>
        <script type="text/javascript" src="js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="js/jquery.validate.creditcard2.pack-1.0.1.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <link rel='stylesheet' type='text/css' href='css/datePicker.css'>
    </head>
    <body>
    <div id="TopNav">
        <? if (isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN']==1) { ?>
        <ul class="nav">
            <li class="report"><a class="<? if ($current=="report") print "current" ?>" href="report.php">Report</a></li>
            <li class="submit"><a class="<? if ($current=="submit") print "current" ?>" href="submit.php">Submit</a></li>
            <li class="cancel"><a class="<? if ($current=="cancel") print "current" ?>" href="cancel.php">Cancel</a></li>
            <li class="run"><a class="<? if ($current=="run") print "current" ?>" href="run.php">Run</a></li>
            <li class="settings"><a class="<? if ($current=="settings") print "current" ?>" href="settings.php">Settings</a></li>
            <li class="logout"><a class="<? if ($current=="logout") print "current" ?>" href="logout.php">Logout</a></li>
        </ul>
        <div style="clear:both"></div>
        <? } ?>
    </div>
<? } ?>

<div id="<? if (!$EXPORT) print "mainWrapper" ?>">
    <? print $BODY; ?>
</div>

<? if (!$EXPORT) { ?>
</body>
</html>
<? } ?>