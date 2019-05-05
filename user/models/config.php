<?php
	/*
		UserCake Version: 1.4
		modified g.zi@gmx.de
		http://usercake.com
		Developed by: Adam Davis
	*/

//  error_reporting (!E_WARNING); // keine Warnungen anzeigen
    error_reporting(E_STRICT|E_ALL);
//  ini_set("display_errors", 1);

/*
	// forced to use https
	if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")
	{
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
  }
*/

	require_once("settings.php");

	//Dbal Support - Thanks phpBB ; )
	require_once(dirname(__FILE__)."/db/".$dbtype.".php");

	//Construct a db instance
	$db = new $sql_db();
	if(is_array($db->sql_connect($db_host, $db_user, $db_pass, $db_name, $db_port, false, false))) 
	{if(is_dir("install/")) {header("Location: install/");}}

	require_once("class.user.php");
	require_once("class.mail.php");
	require_once("funcs.user.php");
  require_once("funcs.general.php");
	require_once("class.newuser.php");
	require_once("funcs.sql.php");  // sql queries for FYP

	if(!isset($_SESSION))
	{
		session_start();
		//echo "X".$_SESSION['language'];
		// Directory where txt files are stored for the email templates.
		$mail_templates_dir = "models/mail-templates-".$_SESSION['language']."/";
	}

	//Global User Object Var
	//loggedInUser can be used globally if constructed
	if(isset($_SESSION["userCakeUser"]) && is_object($_SESSION["userCakeUser"]))
	{
		$loggedInUser = $_SESSION["userCakeUser"];
	}

?>