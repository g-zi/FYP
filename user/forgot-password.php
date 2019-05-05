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

//Prevent the user visiting the lost password page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

/* 
	This file handles 3 tasks.

	1. Construct new request.
	2. Confirm request. - Generate new password, update the db then email the user
	3. Deny request. - Close the request
*/
	
$errors = array();
$success_message = "";
	
//User has confirmed they want their password changed
//----------------------------------------------------------------------------------------------
if(!empty($_GET["confirm"]))
{
	$token = trim($_GET["confirm"]);
	
	if($token == "" || !validateActivationToken($token,TRUE))
	{
		$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
	}
	else
	{
		$rand_pass = getUniqueCode(15);
		$secure_pass = generateHash($rand_pass);
		
		$userdetails = fetchUserDetails(NULL,$token);
		
		$mail = new userCakeMail();		
						
		//Setup our custom hooks
		$hooks = array(
				"searchStrs" => array("#GENERATED-PASS#","#USERNAME#"),
				"subjectStrs" => array($rand_pass,$userdetails["Username"])
		);
					
		if(!$mail->newTemplateMsg("your-lost-password.txt",$hooks))
		{
			$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
		}
		else
		{	
			if(!$mail->sendMail($userdetails["Email"],"Your new password"))
			{
					$errors[] = lang("MAIL_ERROR");
			}
			else
			{
					if(!updatePasswordFromToken($secure_pass,$token))
					{
						$errors[] = lang("SQL_ERROR");
					}
					else
					{	
						//Might be wise if this had a time delay to prevent a flood of requests.
						flagLostPasswordRequest($userdetails["Username_Clean"],0);
						
						$success_message  = lang("FORGOTPASS_NEW_PASS_EMAIL");
					}
			}
		}
			
	}
}

//----------------------------------------------------------------------------------------------

//User has denied this request
//----------------------------------------------------------------------------------------------
if(!empty($_GET["deny"]))
{
	$token = trim($_GET["deny"]);
	
	if($token == "" || !validateActivationToken($token,TRUE))
	{
		$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
	}
	else
	{
	
		$userdetails = fetchUserDetails(NULL,$token);
		
		flagLostPasswordRequest($userdetails['Username_Clean'],0);
		
		$success_message = lang("FORGOTPASS_REQUEST_CANNED");
	}
}




//----------------------------------------------------------------------------------------------


//Forms posted
//----------------------------------------------------------------------------------------------
if(!empty($_POST))
{
	$email = $_POST["email"];
	$username = $_POST["username"];
	
	//Perform some validation
	//Feel free to edit / change as required
	
	if(trim($email) == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
	}
	//Check to ensure email is in the correct format / in the db
	else if(!isValidEmail($email) || !emailExists($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	
	if(trim($username) == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
	}
	else if(!usernameExists($username))
	{
		$errors[] = lang("ACCOUNT_INVALID_USERNAME");
	}
	
	
	if(count($errors) == 0)
	{
	
		//Check that the username / email are associated to the same account
		if(!emailUsernameLinked($email,$username))
		{
			$errors[] =  lang("ACCOUNT_USER_OR_EMAIL_INVALID");
		}
		else
		{
			//Check if the user has any outstanding lost password requests
			$userdetails = fetchUserDetails($username);
			
			if($userdetails["LostPasswordRequest"] == 1)
			{
				$errors[] = lang("FORGOTPASS_REQUEST_EXISTS");
			}
			else
			{
				//Email the user asking to confirm this change password request
				//We can use the template builder here
				
				//We use the activation token again for the url key it gets regenerated everytime it's used.
				
				$mail = new userCakeMail();
				
				$confirm_url = lang("CONFIRM")."\n".$websiteUrl."forgot-password.php?confirm=".$userdetails["ActivationToken"];
				$deny_url = lang("DENY")."\n".$websiteUrl."forgot-password.php?deny=".$userdetails["ActivationToken"];
				
				//Setup our custom hooks
				$hooks = array(
					"searchStrs" => array("#CONFIRM-URL#","#DENY-URL#","#USERNAME#"),
					"subjectStrs" => array($confirm_url,$deny_url,$userdetails["Username"])
				);
				
				if(!$mail->newTemplateMsg("lost-password-request.txt",$hooks))
				{
					$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
				}
				else
				{
					if(!$mail->sendMail($userdetails["Email"],"Lost password request"))
					{
						$errors[] = lang("MAIL_ERROR");
					}
					else
					{
						//Update the DB to show this account has an outstanding request
						flagLostPasswordRequest($username,1);
						
						$success_message = lang("FORGOTPASS_REQUEST_SUCCESS");
					}
				}
			}
		}
	}
}	
//----------------------------------------------------------------------------------------------	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Forgot Password</title>
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
            <form name="newLostPass" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
			<div style='position:relative; top:290px; right:10px; text-align:right; font-family:Verdana'>
				<input type="submit" value="Login" class="submit" />
			</div>

			Yellow Pages
			<h1><?php echo $lang["TITLE_FORGOT_PASSWORD"]?></h1>
        
            <p>
                <label><?php echo $lang["USERNAME"]?>:</label><br>
                <input type="text" name="username" style="width:200px" />
            </p>
            <p>    
                <label><?php echo $lang["EMAIL"]?>:</label><br>
                <input type="text" name="email" style="width:200px" />
            </p>
            </form>
        </div>
        
		<?php if(!empty($_POST) || !empty($_GET))
			{	if(count($errors) > 0)
				{ ?>
					<div id="errors">
						<?php errorBlock($errors); ?>
					</div> <?php }
				else { ?>
					<div id="success">
						<p><?php echo $success_message; ?></p>
					</div>
        <?php } } ?> 
        
            <div class="clear"></div>
        </div>
    </div>
</div>
</body>
</html>


