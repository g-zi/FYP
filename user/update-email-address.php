<?php
/*
	UserCake Version: 1.4
	modified g.zi@gmx.de
	http://usercake.com
	Developed by: Adam Davis
*/
header ('Content-type: text/html; charset=utf8');
include("models/config.php");
require_once("../lib/shared.inc.php");
require_once("models/lang/".$_SESSION['language'].".php");

//Prevent the user visiting the logged in page if he/she is not logged in
if(!isUserLoggedIn()) { header("Location: login.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$email = $_POST["email"];

	//Perform some validation
	//Feel free to edit / change as required
	
	if(trim($email) == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
	}
	else if(!isValidEmail($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	else if($email == $loggedInUser->email)
	{
			$errors[] = lang("NOTHING_TO_UPDATE");
	}
	else if(emailExists($email))
	{
		$errors[] = lang("ACCOUNT_EMAIL_TAKEN");	
	}
	
	//End data validation
	if(count($errors) == 0)
	{
		$loggedInUser->updateEmail($email);
	}
} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Contact Details</title>
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
            <div id="regbox">
                <form name="changePass" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<div style='position:relative; top:290px; right:10px; text-align:right; font-family:Verdana'>
                    <input type="submit" value="Update Email" class="submit" />
				</div>

				Yellow Pages
				<h1><?php echo $lang["TITLE_UPDATE_EMAIL"]?></h1>

                <p>
                    <label><?php echo $lang["EMAIL"]?>:</label><br>
                    <input type="text" name="email" style="width:200px" value="<?php echo $loggedInUser->email; ?>" />
                </p>
                </form>
            </div>

	        <?php if(!empty($_POST))
	        	{ 	if(count($errors) > 0)
	        		{ ?>
		                <div id="errors">
        			        <?php errorBlock($errors); ?>
		                </div>     
            <?php } 
            	else 
            	{ ?> 
		            <div id="success">
						<p><?php echo lang("ACCOUNT_DETAILS_UPDATED"); ?></p>
					</div>
	        <?php } } ?> 

            <div class="clear"></div>
        </div>
	</div>
</div>
</body>
</html>

