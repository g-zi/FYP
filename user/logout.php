<?php
	/*
		UserCake Version: 1.4
		modified g.zi@gmx.de
		http://usercake.com
		Developed by: Adam Davis
	*/
	header ('Content-type: text/html; charset=utf8');
	include("models/config.php");
	
	//Log the user out
	if(isUserLoggedIn()) $loggedInUser->userLogOut();

	if(!empty($websiteUrl)) 
	{
		$add_http = "";
		
		if(strpos($websiteUrl,"http://") === false)
		{
			$add_http = "http://";
		}
	
		header("Location: ".$add_http.$websiteUrl);
		die();
	}
	else
	{
		header("Location: http://".$_SERVER['HTTP_HOST']);
		die();
	}	
?>


