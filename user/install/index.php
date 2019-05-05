<html>
<head>
<title>Database Setup</title>
<style type="text/css">
<!--
html, body {
	margin-top:15px;
	background: #fff;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size:20px;
	color:#4d4948;
	text-align:center;
}
-->
</style>
</head>
<body>
<p><img src="FREMO-Logo.gif"></p>
<H1>FREMO Yellow Pages</H1>
<?php

//  error_reporting(E_ALL);
//  ini_set("display_errors", 1);

	$dbtype = "mysqli"; 
  $tzn = str_replace('\r\n', "\n", htmlspecialchars_decode(stripslashes(htmlspecialchars($_GET["timezone"])))); // Database Host
  $dbh = str_replace('\r\n', "\n", htmlspecialchars_decode(stripslashes(htmlspecialchars($_GET["dbh"])))); // Database Host
  $dbu = str_replace('\r\n', "\n", htmlspecialchars_decode(stripslashes(htmlspecialchars($_GET["dbu"])))); // Database User
  $dbp = str_replace('\r\n', "\n", htmlspecialchars_decode(stripslashes(htmlspecialchars($_GET["dbp"])))); // Database Password
  $dbn = str_replace('\r\n', "\n", htmlspecialchars_decode(stripslashes(htmlspecialchars($_GET["dbn"])))); // Database Name
  $wsu = str_replace('\r\n', "\n", htmlspecialchars_decode(stripslashes(htmlspecialchars($_GET["wsu"])))); // websiteUrl	
	
  $settings = fopen("../models/settings.php", "w") or die("Unable to open file!");
	
  $txt = "<?php
	/*
		UserCake Version: 1.4
		modified g.zi@gmx.de
		http://usercake.com
		Developed by: Adam Davis
	*/
	
  // General Settings
  //--------------------------------------------------------------------------
	date_default_timezone_set('".$tzn."');
  
  // Database Information
  ".chr(36)."dbtype = '".$dbtype."';
  ".chr(36)."db_host = '".$dbh."';
  ".chr(36)."db_user = '".$dbu."';
  ".chr(36)."db_pass = '".$dbp."';
  ".chr(36)."db_name = '".$dbn."';
  ".chr(36)."db_port = '';
  
  ".chr(36)."language = ".chr(36)."_SESSION['language'];
  
	// Generic website variables
  ".chr(36)."websiteName = 'FREMO Yellow Pages';
  ".chr(36)."websiteUrl = 'http://g-zi.de/FYP/'; //including trailing slash
  
	// Do you wish to send out emails for confirmation of registration?
	// We recommend this be set to true to prevent spam bots.
	// False = instant activation. If this variable is falses the resend-activation file not work.
  ".chr(36)."emailActivation = true;
  
	// In hours, how long before UserCake will allow a user to request another account activation email
	// Set to 0 to remove threshold
  ".chr(36)."resend_activation_threshold = 1;
  
	// Tagged on outgoing emails
  ".chr(36)."emailAddress = 'fyp@g-zi.de';
  
  // Date format used on email's
  date_default_timezone_set('UTC');
  ".chr(36)."emailDate = date('d.m.Y');
  
	// Directory where txt files are stored for the email templates.
  ".chr(36)."mail_templates_dir = 'models/mail-templates-'.".chr(36)."language.'/';
  
  ".chr(36)."default_hooks = array('#WEBSITENAME#', '#WEBSITEURL#', '#DATE#');
  ".chr(36)."default_replace = array(".chr(36)."websiteName,".chr(36)."websiteUrl,".chr(36)."emailDate);
  
	// Display explicit error messages?
  ".chr(36)."debug_mode = false;
  
	//---------------------------------------------------------------------------
?>";
  
  fwrite($settings, $txt);
  fclose($settings);

	//  Primitive installer
	require_once("../models/settings.php");
	
	//Dbal Support - Thanks phpBB ; )
	require_once("../models/db/".$dbtype.".php");
	require_once("../models/funcs.user.php");
//	$db_name = str_replace('\r\n', "\n", htmlspecialchars_decode(mysqli_real_escape_string(stripslashes(htmlspecialchars($_GET[$install])))));
	
  
	//Construct a db instance
	$db = new $sql_db();

//  if($_GET["install"]=='true')
  if(isset($_GET["install"]))
  {
    // connect to MySQL server
    $con = new mysqli($db_host, $db_user, $db_pass);

    // check connection
    if (mysqli_connect_errno()) { exit('Connect failed: '. mysqli_connect_error()); }

		// sql query with CREATE DATABASE
    $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

    // Performs the $sql query on the server to create the database
    if (!$con->query($sql) === TRUE) {
      echo 'Error: '. $con->error; }
  }
    
	if(is_array($db->sql_connect($db_host, $db_user, $db_pass, $db_name, $db_port,	false, false))) 
  {
    echo "<strong>Unable to connect to the database</strong>";	

    if (!empty($_POST)){
      echo "We are importing the tables...one moment please!"; } ?>
	<br><strong>Please fill in database connection</strong>
    <form>
      <input type='hidden' name='install' value='true'>
      <table align="center">
        <tr>
          <td align="right"><form class="form" action="" method="post"><label for="dbh">Region/Timezone&nbsp</label></td>
          <td><?php
            $regions = array(
              'Africa' => DateTimeZone::AFRICA,
              'America' => DateTimeZone::AMERICA,
              'Antarctica' => DateTimeZone::ANTARCTICA,
              'Asia' => DateTimeZone::ASIA,
              'Atlantic' => DateTimeZone::ATLANTIC,
              'Europe' => DateTimeZone::EUROPE,
              'Indian' => DateTimeZone::INDIAN,
              'Pacific' => DateTimeZone::PACIFIC
            );
            $timezones = array();
            foreach ($regions as $name => $mask)
            {
              $zones = DateTimeZone::listIdentifiers($mask);
              foreach($zones as $timezone)
              {
                // Lets sample the time there right now
                $time = new DateTime(NULL, new DateTimeZone($timezone));
                // Us dumb Americans can't handle millitary time
                $ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';
                // Remove region name and add a sample time
                $timezones[$name][$timezone] = substr($timezone, strlen($name) + 1) . ' - ' . $time->format('H:i') . $ampm;
              }
            }
            
            // View
            echo "<select class='form-control' id='timezone' name='timezone' required>";
            echo "<option value='Europe/Berlin' name='".$timezone."'>".$timezones['Europe']['Europe/Berlin']."</option>";
            foreach($timezones as $region => $list)
            {
              echo "<optgroup label='".$region."'>";
              if(!empty($_POST['timezone'])){?>
                <option value="<?=$_POST['timezone']?>" selected="Europe/Berlin"><?=$_POST['timezone']?></option><?php }
              foreach($list as $timezone => $name) {
                echo "<option value='".$timezone."' name='".$timezone."'>".$name."</option>"; }
              echo "<optgroup>";
            }
            echo "</select>";?>
            </td>
        </tr>
        <tr>
          <td align="right"><label for="dbh">Database Host&nbsp</label></td>
          <td><input required class="form-control" type="text" name="dbh" 
              value="<?php if(!empty($_POST['dbh'])){echo $_POST['dbh'];} else{echo "localhost";} ?>" required></label>
          </td>
        </tr>
        <tr>
          <td align="right"><label for="dbu">Database User&nbsp</label></td>
          <td><input required class="form-control" type="text" name="dbu" 
              value="<?php if (!empty($_POST['dbu'])){echo $_POST['dbu'];} else{echo "root";} ?>" required></label></td>
        </tr>
        <tr>
          <td align="right"><label for="dbp">Database Password&nbsp</label></td>
          <td><input class="form-control" type="text" name="dbp" 
              value="<?php if (!empty($_POST['dbp'])){echo $_POST['dbp'];} else{echo "root";} ?>" required></label></td>
        </tr>
        <tr>
          <td align="right"><label required for="dbn">Database Name&nbsp</label></td>
          <td><input class="form-control" type="text" name="dbn" 
              value="<?php if (!empty($_POST['dbn'])){echo $_POST['dbn'];} else{echo "fremo_yellow_pages";} ?>" required></label></td>
        </tr>
        <tr>
          <td align="right"><label required for="yer">Website URL&nbsp</label></td>
          <td><input class="form-control" type="text" name="wsu" 
              value="<?php if (!empty($_POST['wsu'])){echo $_POST['wsu'];} else{echo "http://g-zi.de/FYP/";} ?>" required></label></td>
        </tr>
      </table>
      <br>
      <input type="submit" name="CreateDB" value="Create database"><br><br>
    </form>
    <?php
  }
	else
	{
	
	if(returns_result("SELECT * FROM Groups LIMIT 1") > 0)
	{
		echo "<form action='../../main.php'>";
		echo "<strong><p>Database has already been installed</p><p>Please remove / rename the install directory</p></strong>";	
		echo "<input type='submit' name='Back' value='Back'></form>";
	}
	else
	{
      if(isset($_GET["install"]))
      {	
        $db_issue = false;
        
				$groups_sql = "
          CREATE TABLE IF NOT EXISTS `Groups` (
						`Group_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  					`Group_Name` varchar(225) NOT NULL,
  					PRIMARY KEY (`Group_ID`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
				";
				
				$groups_entry = "
          INSERT INTO `Groups` (`Group_ID`, `Group_Name`) VALUES
						(1,'Standard User'),
						(2,'Modul User');
        ";
				
				$users_sql = "
          CREATE TABLE IF NOT EXISTS `Users` (
						`User_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`Username` varchar(150) NOT NULL,
						`Username_Clean` varchar(150) NOT NULL,
						`Password` varchar(225) NOT NULL,
						`Email` varchar(150) NOT NULL,
						`ActivationToken` varchar(225) NOT NULL,
						`LastActivationRequest` int(11) NOT NULL,
						`LostPasswordRequest` int(1) NOT NULL DEFAULT '0',
						`Active` int(1) NOT NULL,
						`Group_ID` int(11) NOT NULL,
						`SignUpDate` int(11) NOT NULL,
						`LastSignIn` int(11) NOT NULL,
						`Language` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de',
						PRIMARY KEY (`User_ID`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
				";
				
				$users_entry = "
          INSERT INTO `Users` (`User_ID`, `Username`, `Username_Clean`, `Password`, `Email`, `ActivationToken`, `LastActivationRequest`, `LostPasswordRequest`, `Active`, `Group_ID`, `SignUpDate`, `LastSignIn`, `Language`) VALUES
          (1, 'AdminFYP', 'adminfyp', '54854ab94e8a67bbaae0244db190e9c931a7d1c42a50b383c06cdcd5eb6f5668e', 'fyp@g-zi.de', 'fcdbabe98139536448aa07f9617761c9', 1556735738, 0, 1, 2, 1556735738, 0, 'de');
        ";
				
				$bahnhof_sql = "
					CREATE TABLE IF NOT EXISTS `bahnhof` (
						`Haltestelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Spur` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Kurzbezeichnung` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Bahnverwaltung` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Besitzer` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Art` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Bhf_Bem` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Zeichnung` varchar(255) DEFAULT NULL,
						`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						`Streckengleise` int(2) unsigned NOT NULL DEFAULT '1',
						`Kreuzung` int(4) unsigned DEFAULT NULL,
						`Einleitung` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Beschreibung` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Personenverkehr` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Frachtverkehr` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Language` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de',
						`LastUser` varchar(150) DEFAULT NULL,
						`LastUser_ID` bigint(20) DEFAULT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ";
				
				$frachtzettel_sql = "
					CREATE TABLE IF NOT EXISTS `frachtzettel` (
						`Menge` smallint(2) unsigned DEFAULT '1',
						`Eilgut` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Wenden` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Mehrfach` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Stueckgut` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Zielbahnhof` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Empfaenger` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Ecol` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Gewicht` smallint(3) unsigned DEFAULT '1',
						`Wagengattung` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Freight` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Ladung` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Versandbahnhof` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Versender` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Vcol` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`LadeEmpfang` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Lcol` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`ZBV` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`VBV` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Treffen` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`User` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`FUser_ID` bigint(20) NOT NULL,
						`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ";
				
				$fyp_sql = "
					CREATE TABLE IF NOT EXISTS `fyp` (
						`NHM_Code` int(8) NOT NULL DEFAULT '99000000',
						`Wagengattung` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Produktbeschreibung` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Product_Description` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Anschliesser` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`FYP_Bem` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Betriebsstelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Gleis_ID` bigint(20) DEFAULT '0',
						`Bhf_ID` bigint(20) unsigned DEFAULT '0',
						`id_fyp` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						`Ladestelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Wagen_Woche` int(3) NOT NULL DEFAULT '1',
						PRIMARY KEY (`id_fyp`),
						KEY `NHM_Code` (`NHM_Code`),
						FULLTEXT KEY `Betriebsstelle` (`Betriebsstelle`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ";
				
				$nhm_sql = "		
					CREATE TABLE `nhm` (
						`NHM_Code` int(8) NOT NULL,
						`UIC_Wagengattung` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`English` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Nederlands` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Deutsch` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Francais` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Italiano` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Dansk` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Espanol` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Polski` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Bulgarian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Greek` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Czech` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Rumanian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Hungarian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Russian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Portuges` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Slovak` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Slovene` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Svenska` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Estonian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Suomeksi` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Latvian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						`Lithuanian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
						PRIMARY KEY (`NHM_Code`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
				";
				
				$gleise_sql = "
					CREATE TABLE IF NOT EXISTS `gleise` (
						`Gleisname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
						`Gleislaenge` int(4) unsigned NOT NULL DEFAULT '1',
						`Gleisart` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Bahnsteiglaenge` int(4) unsigned NOT NULL,
						`LadeFarbe` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
						`Ladestelle` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
						`Gl_Bem` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
						`Bhf_ID` bigint(20) NOT NULL DEFAULT '0',
						`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						PRIMARY KEY (`id`),
						KEY `Name` (`Gleisname`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
				
				$manage_sql = "
					CREATE TABLE IF NOT EXISTS `manage` (
						`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`User` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`MUser_ID` bigint(20) DEFAULT NULL,
						`Betriebsstelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`Bhf_ID` bigint(20) DEFAULT '0',
						`MainMngr_ID` bigint(20) DEFAULT '0',
						`Mng_Bem` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
						`Mitverwalter` tinyint(1) DEFAULT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ";
				
				$treffen_sql = "
					CREATE TABLE IF NOT EXISTS `treffen` (
						`Treffen` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Telefon` int(3) unsigned DEFAULT '0',
						`Betriebsstelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`Bhf_ID` bigint(20) unsigned DEFAULT '0',
						`Anschliesser` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`An_ID` bigint(20) unsigned DEFAULT '0',
						`Trf_Bem` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
						`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ";
				
				$module_sql = "
					CREATE TABLE IF NOT EXISTS `module` (
						`Name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
						`NR` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Rem` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Spur` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Radius` int(10) DEFAULT NULL,
						`Winkel` int(10) DEFAULT NULL,
						`Laenge` int(6) DEFAULT NULL,
						`Breite` int(6) DEFAULT NULL,
						`Endprofil_1` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Endprofil_2` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Endprofil_3` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Signalschacht` int(1) DEFAULT NULL,
						`Status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Besitzer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						`Bemerkung` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Zeichnung` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
						`Bhf_ID` bigint(20) unsigned DEFAULT NULL,
						`Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`id_module` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						PRIMARY KEY (`id_module`),
						UNIQUE KEY `id_module` (`id_module`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
				
				$mngmod_sql = "
					CREATE TABLE IF NOT EXISTS `mngmod` (
						`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						`User` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
						`MUser_ID` bigint(20) NOT NULL,
						`Md_ID` bigint(20) DEFAULT '0',
						`ModRem` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        
        if($con->query($sql) === TRUE) {
          echo "Database <b>".$db_name."</b> successfully created...<br>"; }

        if($db->sql_query($groups_sql))	{
					echo "<b>Groups</b> table created...<br>"; }
				else {
					echo "<p>Error constructing Groups table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($groups_entry)) {
					echo "Standard Groups into Groups table inserted...<br>"; }
				else {
					echo "<p>Error constructing Groups table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true; }
				
				if($db->sql_query($users_sql)) {
					echo "<b>Users</b> table created...<br>";	}
				else {
					echo "<p>Error constructing Users table.</p><br /><br /> DBMS said: ";					
					echo print_r($db->_sql_error());
					$db_issue = true; }

				if($db->sql_query($users_entry)) {
					echo "Standard Users into Users table inserted...<br>"; }
				else {
					echo "<p>Error constructing Users table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true; }
				
				if($db->sql_query($bahnhof_sql))	{
					echo "<b>bahnhof</b> table created...<br>"; }
				else {
					echo "<p>Error constructing bahnhof table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($frachtzettel_sql))	{
					echo "<b>frachtzettel</b> table created...<br>"; }
				else {
					echo "<p>Error constructing frachtzettel table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($fyp_sql))	{
					echo "<b>fyp</b> table created...<br>"; }
				else {
					echo "<p>Error constructing fyp table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($nhm_sql))	{
					echo "<b>nhm</b> table created...<br>"; }
				else {
					echo "<p>Error constructing nhm table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($gleise_sql))	{
					echo "<b>gleise</b> table created...<br>"; }
				else {
					echo "<p>Error constructing gleise table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($manage_sql))	{
					echo "<b>manage</b> table created...<br>"; }
				else {
					echo "<p>Error constructing manage table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($treffen_sql))	{
					echo "<b>treffen</b> table created...<br>"; }
				else {
					echo "<p>Error constructing treffen table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($module_sql))	{
					echo "<b>module</b> table created...<br>"; }
				else {
					echo "<p>Error constructing module table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if($db->sql_query($mngmod_sql))	{
					echo "<b>mngmod</b> table created...<br>"; }
				else {
					echo "<p>Error constructing mngmod table.</p><br /><br /> DBMS said: ";
					echo print_r($db->_sql_error());
					$db_issue = true;	}
				
				if(!$db_issue) 
				{
					echo "<form action='../../main.php'>";
					echo "<p><strong>Database setup completed</strong><br>Please log in as AdminFYP and change the password</p>";
				  echo "<input type='submit' name='Back' value='Back'></form>";
				}
				else echo "<p><a href=\"?install=true\">Try again</a></p>";
      }
      else
      {
				?><a href="?install=true">Install database</a><?php 
			} 
		} 
	} 
?>
</body>
</html>