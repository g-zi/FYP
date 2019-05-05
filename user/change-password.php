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

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$password = $_POST["password"];
	$password_new = $_POST["passwordc"];
	$password_confirm = $_POST["passwordcheck"];

	//Perform some validation
	//Feel free to edit / change as required
	
/*	if(trim($password) == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}
	else*/ if(trim($password_new) == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_NEW_PASSWORD");
	}
	else if(minMaxRange(8,50,$password_new))
	{	
		$errors[] = lang("ACCOUNT_NEW_PASSWORD_LENGTH",array(8,50));
	}
	else if($password_new != $password_confirm)
	{
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}
	
	//End data validation
	if(count($errors) == 0)
	{
		//Confirm the hash's match before updating a users password
		$entered_pass = generateHash($password,$loggedInUser->hash_pw);
		
		//Also prevent updating if someone attempts to update with the same password
		$entered_pass_new = generateHash($password_new,$loggedInUser->hash_pw);
	
/*		if($entered_pass != $loggedInUser->hash_pw)
		{
			//No match
			$errors[] = lang("ACCOUNT_PASSWORD_INVALID");
		}
		else*/ if($entered_pass_new == $loggedInUser->hash_pw)
		{
			//Don't update, this fool is trying to update with the same password ¬¬
			$errors[] = lang("NOTHING_TO_UPDATE");
		}
		else
		{
			//This function will create the new hash and update the hash_pw property.
			$loggedInUser->updatePassword($password_new);
		}
	}
} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Password</title>
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
                    <input type="submit" value="Update Password" class="submit" />
				</div>

				Yellow Pages
		        <h1><?php echo $lang["TITLE_CHANGE_PASSWORD"]?></h1>
<?php /*
                <p>
                    <label>Password:</label>
                    <input type="password" name="password" />
                </p>
*/ ?>                
                <p>
                    <label><?php echo $lang["NEW_PASSWORD"]?>:</label><br>
                    <input type="password" name="passwordc" />
                </p>
                <p>
                    <label><?php echo $lang["CONFIRM"]?>:</label><br>
                    <input type="password" name="passwordcheck" />
                </p>
            </form>
    
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
</div>
</body>
</html>


