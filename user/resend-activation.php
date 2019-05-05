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
	
$errors = array();
$success_message = "";

//Forms posted
//----------------------------------------------------------------------------------------------
if(!empty($_POST) && $emailActivation)
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
		$errors[] =  lang("ACCOUNT_SPECIFY_USERNAME");
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
			$errors[] = lang("ACCOUNT_USER_OR_EMAIL_INVALID");
		}
		else
		{
			$userdetails = fetchUserDetails($username);
		
			//See if the user's account is activation
			if($userdetails["Active"]==1)
			{
				$errors[] = lang("ACCOUNT_ALREADY_ACTIVE");
			}
			else
			{
				$hours_diff = round((time()-$userdetails["LastActivationRequest"]) / (3600*$resend_activation_threshold),0);

				if($resend_activation_threshold!=0 && $hours_diff <= $resend_activation_threshold)
				{
					$errors[] = lang("ACCOUNT_LINK_ALREADY_SENT",array($resend_activation_threshold));
				}
				else
				{
					//For security create a new activation url;
					$new_activation_token = generateActivationToken();
					
					if(!updateLastActivationRequest($new_activation_token,$username,$email))
					{
						$errors[] = lang("SQL_ERROR");
					}
					else
					{
						$mail = new userCakeMail();
						
						$activation_url = $websiteUrl."activate-account.php?token=".$new_activation_token;
					
						//Setup our custom hooks
						$hooks = array(
							"searchStrs" => array("#ACTIVATION-URL#","#USERNAME#"),
							"subjectStrs" => array($activation_url,$userdetails["Username"])
						);
						
						if(!$mail->newTemplateMsg("resend-activation.txt",$hooks))
						{
							$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
						}
						else
						{
							if(!$mail->sendMail($userdetails["Email"],"Activate your UserCake Account"))
							{
								$errors[] = lang("MAIL_ERROR");
							}
							else
							{
								//Success, user details have been updated in the db now mail this information out.
								$success_message = lang("ACCOUNT_NEW_ACTIVATION_SENT");
							}
						}
					}
				}
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resend Activation Email</title>
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
	
	<?php if(!$emailActivation) { 
			echo lang("FEATURE_DISABLED"); }
		else { ?>
	     	<form name="resendActivation" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<div style='position:relative; top:290px; right:10px; text-align:right; font-family:Verdana'>
					<input type="submit" value="Login" class="submit" />
				</div>
	
				Yellow Pages
				<h1><?php echo $lang["TITLE_RESEND_EMAIL"]?></h1>
	
				<p>
					<label><?php echo $lang["USERNAME"]?>:</label><br>
					<input type="text" name="username" style="width:200px" />
				</p>     
	
				<p>
					<label><?php echo $lang["EMAIL"]?>:</label><br>
					<input type="text" name="email" style="width:200px" />
				</p>    
	     	</form>
	<?php } ?> 
    </div>   

    <?php if(!empty($_POST) || !empty($_GET["confirm"]) || !empty($_GET["deny"]) && $emailActivation)
		{	if(count($errors) > 0)
            { ?>
        		<div id="errors">
            		<?php errorBlock($errors); ?>
            	</div><?php }
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


