<?php
	/*
		UserCake Version: 1.4
		modified g.zi@gmx.de
		http://usercake.com
		Developed by: Adam Davis
	*/
	
  // General Settings
  //--------------------------------------------------------------------------
	date_default_timezone_set('Europe/Berlin');
  
  // Database Information
  $dbtype = 'mysqli';
  $db_host = 'localhost';
  $db_user = 'root';
  $db_pass = 'root';
  $db_name = 'xxxx';
  $db_port = '';
  
  $language = $_SESSION['language'];
  
	// Generic website variables
  $websiteName = 'FREMO Yellow Pages';
  $websiteUrl = 'http://g-zi.de/FYP/'; //including trailing slash
  
	// Do you wish to send out emails for confirmation of registration?
	// We recommend this be set to true to prevent spam bots.
	// False = instant activation. If this variable is falses the resend-activation file not work.
  $emailActivation = true;
  
	// In hours, how long before UserCake will allow a user to request another account activation email
	// Set to 0 to remove threshold
  $resend_activation_threshold = 1;
  
	// Tagged on outgoing emails
  $emailAddress = 'fyp@g-zi.de';
  
  // Date format used on email's
  date_default_timezone_set('UTC');
  $emailDate = date('d.m.Y');
  
	// Directory where txt files are stored for the email templates.
  $mail_templates_dir = 'models/mail-templates-'.$language.'/';
  
  $default_hooks = array('#WEBSITENAME#', '#WEBSITEURL#', '#DATE#');
  $default_replace = array($websiteName,$websiteUrl,$emailDate);
  
	// Display explicit error messages?
  $debug_mode = false;
  
	//---------------------------------------------------------------------------
?>