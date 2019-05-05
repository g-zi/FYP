<?php
/*
	UserCake Version: 1.4 
	modified g.zi@gmx.de
	http://usercake.com
	Developed by: Adam Davis
*/
header ('Content-type: text/html; charset=utf8');
require_once("models/config.php");
require_once("../lib/shared.inc.php");
require_once("models/lang/".$_SESSION['language'].".php");

//Prevent the user visiting the logged in page if he/she is not logged in
if(!isUserLoggedIn()) { header("Location: login.php"); die(); }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome <?php echo $loggedInUser->display_username; ?></title>
<link href="cakestyle.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
	<div id="content">
        <div id="left-nav">
        <?php include("layout/left-nav.php"); ?>
            <div class="clear"></div>
        </div>
        <div id="main">
        	<br>
        	Yellow Pages
        	<h1><?php echo $lang["TITLE_ACCOUNT"]?></h1>
        	<p><?php echo $lang["WELCOME_ACCOUNT"]?><strong><br><?php echo $loggedInUser->display_username; ?></strong></p>
            <p><?php echo $lang["YOU_ACCOUNT"]?><strong><br><?php $group = $loggedInUser->groupID(); echo $group['Group_Name']; ?></strong></p>
            <p><?php echo $lang["JOINED_ACCOUNT"]?><br>
            	<?php // echo date("l \\t\h\e jS Y",$loggedInUser->signupTimeStamp()); ?> 
            	<strong><?php echo date("d.m.Y",$loggedInUser->signupTimeStamp()); ?> </strong>
            </p>
  		</div>
	</div>
</div>
</body>
</html>

