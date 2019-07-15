<?
/*
 * Revised: Mar 25, 2011
 */
 session_start();
$_SESSION['LOGGEDIN'] = 0;
header('Location: report.php');
?>
