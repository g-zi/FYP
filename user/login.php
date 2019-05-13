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
$back = getVariableFromQueryStringOrSession('back');

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: ../Main.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);

	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
	}
	if($password == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}
	
	//End data validation
	if(count($errors) == 0)
	{
		//A security note here, never tell the user which credential was incorrect
		if(!usernameExists($username))
		{
			$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
		}
		else
		{
			$userdetails = fetchUserDetails($username);
		
			//See if the user's account is activation
			if($userdetails["Active"]==0)
			{
				$errors[] = lang("ACCOUNT_INACTIVE");
			}
			else
			{
				//Hash the password and use the salt from the database to compare the password.
				$entered_pass = generateHash($password,$userdetails["Password"]);

				if($entered_pass != $userdetails["Password"])
				{
					//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
					$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
				}
				else
				{
					//Passwords match! we're good to go'
					
					//Construct a new logged in user object
					//Transfer some db data to the session object
					$loggedInUser = new loggedInUser();
					$loggedInUser->email = $userdetails["Email"];
					$loggedInUser->user_id = $userdetails["User_ID"];
					$loggedInUser->hash_pw = $userdetails["Password"];
					$loggedInUser->display_username = $userdetails["Username"];
					$loggedInUser->clean_username = $userdetails["Username_Clean"];
					
					//Update last sign in
					$loggedInUser->updateLastSignIn();
	
					$_SESSION["userCakeUser"] = $loggedInUser;
					
					// load last language
					$row = $db->sql_fetchrow($db->sql_query("SELECT Language FROM Users WHERE Username = '$loggedInUser->display_username'"));
					$lang=strtoupper($row['Language']);
					$_SESSION['lang']=strtoupper($row['Language']);
					
					// load managed train station
					$DeinBahnhof = $db->sql_fetchrow($db->sql_query("SELECT Bhf_ID FROM manage WHERE MainMngr_ID = '$loggedInUser->user_id'"));
					$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$DeinBahnhof[Bhf_ID]'"));
				
					if($Bhf_ID==0) {
						$DeinBahnhof = $db->sql_fetchrow($db->sql_query("SELECT Bhf_ID FROM manage WHERE MUser_ID = '$loggedInUser->user_id'"));
						$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$DeinBahnhof[Bhf_ID]'"));}
			
					if($Bhf_ID==0) {$row = $db->sql_fetchrow($db->sql_query("SELECT id FROM bahnhof WHERE Haltestelle = 'Fritzlar' "));}
					
					$_SESSION['Bhf_ID'] = $row['id'];
					
					$_SESSION["sso"] = 0;

					//Redirect to user account page
					header("Location: ../$back");
					die();
				}
			}
		}
	}
}?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
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
					<input type="submit" value="Login" class="submit" />
				</div>

		        Yellow Pages
        		<h1>Login</h1>

                <p>
                    <label><?php echo $lang["USERNAME"]?>:</label><br>
                    <input type="text" name="username" style="width:200px" />
                </p>
                
                <p>
                     <label><?php echo $lang["PASSWORD"]?>:</label><br>
                     <input type="password" name="password" style="width:200px" />
                </p>                
                </form>
            </div>

	        <?php if(!empty($_POST))
	        	{ 	if(count($errors) > 0)
	        		{ ?>
			        <div id="errors">
	        			<?php errorBlock($errors); ?>
			        </div>     
	        <?php } } ?> 

        </div>
            <div class="clear"></div>
        </div>
</div>
</body>
</html>


