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

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);

	//Perform some validation
	//Feel free to edit / change as required
	
	if(minMaxRange(5,25,$username))
	{
		$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,25));
	}
	if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
	{
		$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
	}
	else if($password != $confirm_pass)
	{
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}
	if(!isValidEmail($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	//End data validation
	if(count($errors) == 0)
	{	
		//Construct a user object
		$user = new User($username,$password,$email);
			
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
			if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));		
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			if(!$user->userCakeAddUser())
			{
				if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
				if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
			}
		}
	}
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registration</title>
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
                <form name="newUser" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<div style='position:relative; top:290px; right:10px; text-align:right; font-family:Verdana'>
                    <input type="submit" value="Register" />
				</div>

				Yellow Pages
				<h1><?php echo $lang["TITLE_REGISTRATION"]?></h1>

                <p>
                    <label><?php echo $lang["USERNAME"]?>:</label><br>
                    <input type="text" name="username" style="width:200px" />
                </p>
                <p>
                    <label><?php echo $lang["PASSWORD"]?>:</label><br>
                    <input type="password" name="password" style="width:200px" />
                </p>
                <p>
                    <label><?php echo $lang["CONFIRM"]?>:</label><br>
                    <input type="password" name="passwordc" style="width:200px" />
                </p>
                <p>
                    <label><?php echo $lang["EMAIL"]?>:</label><br>
                    <input type="text" name="email" style="width:200px" />
                </p>
                </form>
            </div>

	        <?php if(!empty($_POST))
	        	{ 	if(count($errors) > 0) { ?>
						<div id="errors">
							<?php errorBlock($errors); ?>
						</div><?php } 
            	else
            	{	$message = lang("ACCOUNT_REGISTRATION_COMPLETE_TYPE1");
					if($emailActivation)
					{	$message = lang("ACCOUNT_REGISTRATION_COMPLETE_TYPE2"); } ?>
					<div id="success">
						<p><?php echo $message ?></p>
					</div>
	        <?php } } ?>

			<div class="clear"></div>
	 	</div>
	</div>
</div>
</body>
</html>


