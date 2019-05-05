<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

//  error_reporting(E_ALL);
//  ini_set("display_errors", 1);

	header ('Content-type: text/html; charset=utf-8');
	require_once("user/models/config.php");
	require_once("lib/shared.inc.php");
	require_once("lib/menu.inc.php");
	require_once("SQL/backup.php");

	//Prevent the user visiting the logged in page if he/she is not logged in
	if(!isUserLoggedIn()) { header("Location: user/login.php"); die(); }

//====================================================================================================

	if(@$_REQUEST['action']=="edit")
	{
		$row=$db->sql_fetchrow($result=$db->sql_query("SELECT gleise.*, manage.* FROM gleise
		LEFT JOIN manage ON manage.Bhf_ID = gleise.Bhf_ID WHERE manage.MUser_ID = '$loggedInUser->user_id'
		and gleise.id=".round($_REQUEST['Gleis_ID'])), MYSQL_BOTH);
echo sql_error();
		$_SESSION['Gleisname'] = $row['Gleisname'];
		$_SESSION['Gleislaenge'] = $row['Gleislaenge'];
		$_SESSION['Gleisart'] = $row['Gleisart'];
		$_SESSION['Bahnsteiglaenge'] = $row['Bahnsteiglaenge'];
		$_SESSION['LadeFarbe'] = $row['LadeFarbe'];
		$_SESSION['Ladestelle'] = $row['Ladestelle'];
		$_SESSION['Gl_Bem'] = $row['Gl_Bem'];
		$_SESSION['Gleis_ID'] = $row['id'];
		$_SESSION['Bhf_ID'] = $row['Bhf_ID'];
	}

	$Bhf_ID = intval(getVariableFromQueryStringOrSession('Bhf_ID'));

	$Gleisname = getVariableFromQueryStringOrSession('Gleisname');
	$Gleislaenge = intval(getVariableFromQueryStringOrSession('Gleislaenge'));
	if($Gleislaenge=="")$Gleislaenge="1";

	$Gleisart = getVariableFromQueryStringOrSession('Gleisart');
	if($Gleisart=="Main")$Gleisart_lang=$TEXT['lang-mt'];
	elseif($Gleisart=="Siding")$Gleisart_lang=$TEXT['lang-rangier'];
	elseif($Gleisart=="Storage Siding")$Gleisart_lang=$TEXT['lang-abstell'];
	elseif($Gleisart=="Storage")$Gleisart_lang=$TEXT['lang-abstell'];
	elseif($Gleisart=="Depot")$Gleisart_lang=$TEXT['lang-bw'];
	else $Gleisart_lang=$Gleisart;

	$Bahnsteiglaenge = intval(getVariableFromQueryStringOrSession('Bahnsteiglaenge'));
	$LadeFarbe = getVariableFromQueryStringOrSession('LadeFarbe');
	$Ladestelle = getVariableFromQueryStringOrSession('Ladestelle');
	$Gl_Bem = getVariableFromQueryStringOrSession('Gl_Bem');
	$Gleis_ID = getVariableFromQueryStringOrSession('Gleis_ID');

	$Besitzer = getVariableFromQueryStringOrSession('Besitzer');
	$Art = getVariableFromQueryStringOrSession('Art');
	$Bahnverwaltung = getVariableFromQueryStringOrSession('Bahnverwaltung');
	$Email = getVariableFromQueryStringOrSession('Email');
	$Streckengleise = round(intval(getVariableFromQueryStringOrSession('Streckengleise')));
	$Kreuzung = intval(getVariableFromQueryStringOrSession('Kreuzung'));
	$Bhf_Bem = getVariableFromQueryStringOrSession('Bhf_Bem');

	$Einleitung = getVariableFromQueryStringOrSession('Einleitung');
	$Beschreibung = getVariableFromQueryStringOrSession('Beschreibung');
	$Personenverkehr = getVariableFromQueryStringOrSession('Personenverkehr');
	$Frachtverkehr = getVariableFromQueryStringOrSession('Frachtverkehr');

	$Hauptgleise = 0;
	$result = $db->sql_query("SELECT Gleisart FROM gleise WHERE Bhf_ID = '".escape($Bhf_ID)."' AND Gleisart = 'Main';");
	while($row=$db->sql_fetchrow($result)) { $Hauptgleise++; }
echo "BhfID".$Bhf_ID;

	if(@$_REQUEST['Einleitung']!="") $db->sql_query("UPDATE bahnhof
		SET Einleitung = '$Einleitung', Language = '$lang', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id' WHERE id = '$Bhf_ID'");
	if(@$_REQUEST['Beschreibung']!="") $db->sql_query("UPDATE bahnhof
		SET Beschreibung = '$Beschreibung', Language = '$lang', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id'  WHERE id = '$Bhf_ID'");
	if(@$_REQUEST['Personenverkehr']!="") $db->sql_query("UPDATE bahnhof
		SET Personenverkehr = '$Personenverkehr', Language = '$lang', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id'  WHERE id = '$Bhf_ID'");
	if(@$_REQUEST['Frachtverkehr']!="") $db->sql_query("UPDATE bahnhof
		SET Frachtverkehr = '$Frachtverkehr', Language = '$lang', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id' WHERE id = '$Bhf_ID'");


	if($Bhf_ID != "")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '".round($Bhf_ID)."'"));
	}
	elseif($Spur != "")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '".escape($Betriebsstelle)."'
		and Spur = '".escape($Spur)."'"));
	}
	else
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '".escape($Betriebsstelle)."'"));
	}

	$Bhf_ID = $row['id'];
	if($Bhf_ID != "") $Spur = $row['Spur'];
	$Kurzbezeichnung = trim($row['Kurzbezeichnung']);
	$Bahnverwaltung = $row['Bahnverwaltung'];
	$Besitzer = $row['Besitzer'];
	$Email = $row['Email'];
	$Art = $row['Art'];
	$Bhf_Bem = $row['Bhf_Bem'];
	$Zeichnung = $row['Zeichnung'];
	$Streckengleise = $row['Streckengleise'];
	$Kreuzung = $row['Kreuzung'];
	if($Kreuzung=="0")$Kreuzung="";

	if(isUserLoggedIn())
	{
		if($db->sql_fetchrow($db->sql_query("SELECT Bhf_ID FROM manage
		WHERE Bhf_ID = '$Bhf_ID' and MUser_ID = '$loggedInUser->user_id'"))!="") $SaveStation = 'Yes';
	}

	$html=getVariableFromQueryStringOrSession('html');
	if($html=='' || $SaveStation != 'Yes')$html='off';

	if(isset($_REQUEST['DelPic']) and $SaveStation = 'Yes')
	{
		$del_file = "Bilder/".$_REQUEST['DelPic'];
		unlink($del_file);
		echo "DelPic".$_REQUEST['DelPic'];
	}

//echo "B=".$Bhf_ID." K=".$Kurzbezeichnung." k=".$kbz." S=".$SaveStation;
	if($Bhf_ID!="" and $Kurzbezeichnung=="" and $SaveStation == "Yes")
	{
//echo " New";
		$SpurNeu = getVariableFromQueryStringOrSession('Spur');
		$db->sql_query("UPDATE bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
		SET bahnhof.Spur = '$SpurNeu', Language = '$lang', LastUser = '$loggedInUser->display_username' , LastUser_ID = '$loggedInUser->user_id'
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and bahnhof.id = '$Bhf_ID'");
	echo sql_error();
	}

	$Einleitung = $row['Einleitung'];
	if($Einleitung == "" && strstr($_SERVER['HTTP_USER_AGENT'],'Firefox')) $Einleitung = $TEXT['lang-einleitung'];
	$Beschreibung = $row['Beschreibung'];
	if($Beschreibung == "" && strstr($_SERVER['HTTP_USER_AGENT'],'Firefox')) $Beschreibung = $TEXT['lang-beschreibung'];
	$Personenverkehr = $row['Personenverkehr'];
	if($Personenverkehr == "" && strstr($_SERVER['HTTP_USER_AGENT'],'Firefox')) $Personenverkehr = $TEXT['lang-personenverkehr'];
	$Frachtverkehr = $row['Frachtverkehr'];
	if($Frachtverkehr == "" && strstr($_SERVER['HTTP_USER_AGENT'],'Firefox')) $Frachtverkehr = $TEXT['lang-frachtverkehr'];

	if($Zeichnung=="")
	{
		$result=$db->sql_query("SELECT Zeichnung FROM module WHERE Name LIKE '%".escape($Betriebsstelle)."%' and Spur = '".escape($Spur)."'");
		$row=$db->sql_fetchrow($result);
		$Zeichnung = $row['Zeichnung'];
	}

	if($Betriebsstelle!="" and in_array(pathinfo($_FILES["datei"]["name"], PATHINFO_EXTENSION), array_keys($_SESSION['mimetypen'])))
	{	// save only name of *.dwg or *.pdf
		$Zeichnung=pathinfo($_FILES["datei"]["name"], PATHINFO_FILENAME);
//echo $Zeichnung;
		$db->sql_query("UPDATE bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
		SET bahnhof.Zeichnung = '".escape($Zeichnung)."', bahnhof.Language = '$lang', LastUser = '$loggedInUser->display_username' , LastUser_ID = '$loggedInUser->user_id'
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and bahnhof.id = '$Bhf_ID'");
echo sql_error();
	}

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<meta http-equiv="cache-control" content="no-cache"/>
<title>Yellow Pages</title>
</head>
<body bgcolor="#E0FFE0">

<script src="lib/jscolor/jscolor.js"></script>
<script src="lib/ckeditor_full/ckeditor.js"></script>

<?php selectBack('970',$lang)?>

<div style='position:absolute; top:10px; left:160'>
	<?php selectLanguage($languages[$lang])?>
</div>

<table style="table-layout:fixed" width=1020px border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td width="400px"><font size="5"><b><?php echo $TEXT['lang-head']?></font></b></td>
	<tr>
	</tr>
		<td><?php echo $TEXT['lang-subhead']?><br></td>
	</tr>
	<tr>
		<td><font size="4"><?php if(isUserLoggedIn()) echo $TEXT['lang-welcome'] .'<b>'.$loggedInUser->display_username;?></b></font></td>
	</tr>
</table>




<?php if(isUserLoggedIn()){?>
	<div style='position:absolute; top:0px; left:572; width:150px; text-align:center; font-size:80%; color:darkred; font-family:Verdana'>
		<img src='img/Adobe_PDF.png' height='64px'
		onClick="self.location.href='FYP.php?action=bhf&Betriebsstelle=<?php echo $Betriebsstelle?>&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=&lang=<?php echo $lang?>'"><br>
		<?php echo $TEXT['lang-bhf']?>
	</div>
<?php /*
    <div style='position:absolute; top:5px; left:375px; width:32px; text-align:left; font-size:80%; color:darkred; font-family:Verdana'>
		<img src='img/translate.jpg' height='32px'
		onClick="self.location.href='FYP.php?action=bhf&Betriebsstelle=<?php echo $Betriebsstelle?>&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>
		&Treffen=&lang=<?php echo $lang?>&translate=<?php echo strtolower($lang)?>'"><br>
		<?php echo $TEXT['lang-translate']?>
	</div>
*/?>
    <div style='position:absolute; top:0px; left:275px; width:150px; text-align:center; font-size:80%; color:gray; font-family:Verdana'>
		<img src='img/Upload_gn.png' height='64px' onClick="self.location.href='Bahnhof.php?action=rdr'"><br>
		<?php echo $TEXT['lang-wrkbook']?>
	</div>


	<div style='position:absolute; top:0px; left:420px; width:150px; text-align:center; font-size:80%; color:darkgreen; font-family:Verdana'>
		<img src='img/xls.png' height='64px' onClick="self.location.href='Bahnhof.php?action=getxls&Betriebsstelle=<?php echo $Betriebsstelle?>
		&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=&lang=<?php echo $lang?>'"><br>
		<?php echo $TEXT['lang-xlsdown']?>
	</div>

<?php } ?>

<noscript>
	<br><font size='5' color='red'><b>Javascript is disabled,<br> please activate Javascript</b></font>
</noscript>

<table width='1014px' border=0 cellpadding=0 cellspacing=0>
<tr>
	<td colspan='5' height='30px' valign='bottom'>
<?php if(@$_REQUEST['Kurzbezeichnung']!="" and @$_REQUEST['Betriebsstelle']!="")
		{
			$Betriebsstelle = getVariableFromQueryStringOrSession('Betriebsstelle');
			$Spur = getVariableFromQueryStringOrSession('Spur');
			
			$Kurzbezeichnung = trim(getVariableFromQueryStringOrSession('Kurzbezeichnung'));
			$Kurzbezeichnung = preg_replace('/[^a-zA-Z0-9_äöüÄÖÜ]/i', '', $Kurzbezeichnung);
			 
			$Bahnverwaltung = getVariableFromQueryStringOrSession('Bahnverwaltung');
			$Besitzer = getVariableFromQueryStringOrSession('Besitzer');
			$Email = getVariableFromQueryStringOrSession('Email');
			$Art = getVariableFromQueryStringOrSession('Art');
			$Bhf_Bem = getVariableFromQueryStringOrSession('Bhf_Bem');
			$Zeichnung = getVariableFromQueryStringOrSession('Zeichnung');
			$Bhf_ID = getVariableFromQueryStringOrSession('Bhf_ID');

			$Streckengleise = getVariableFromQueryStringOrSession('Streckengleise');
			$Kreuzung = getVariableFromQueryStringOrSession('Kreuzung');
			if($Streckengleise=="")$Streckengleise="1";
			if($Kreuzung=="")$Kreuzung="0";

//			echo $_REQUEST['Neu'];
			
			if(isset($_REQUEST['Neu']) and $_REQUEST['Neu']=='')
			{
				$Bahnverwaltung = '';
				$Art = 'Station';
				$Spur ='HO-RE';
				$Bhf_Bem = '';
				$Zeichnung = '';
				$Kreuzung = 0;
				$Email = $loggedInUser->email;
				$SaveStation = 'Yes';
				$Bhf_ID = '';
			}

			if($Spur=='HO-USA') { // Ho-USA ist eine eigenständige Gruppe
				$result=$db->sql_query("SELECT Kurzbezeichnung FROM bahnhof WHERE Kurzbezeichnung = '$Kurzbezeichnung' AND Spur = $Spur AND NOT id = '$Bhf_ID'");}
			else {
				$result=$db->sql_query("SELECT Kurzbezeichnung FROM bahnhof WHERE Kurzbezeichnung = '$Kurzbezeichnung' AND left(Spur,2) = left('$Spur',2) AND NOT id = '$Bhf_ID'");}
			$kbz = $db->sql_fetchrow($result);
			if($kbz['Kurzbezeichnung']!='' or $Kurzbezeichnung=='')
			{
				if($Spur=='HO-USA') { // Ho-USA ist eine eigenständige Gruppe
					$result=$db->sql_query("SELECT Kurzbezeichnung, Haltestelle FROM bahnhof WHERE Spur = $Spur AND NOT id = '$Bhf_ID' ORDER BY Kurzbezeichnung");}
				else {
					$result=$db->sql_query("SELECT Kurzbezeichnung, Haltestelle FROM bahnhof WHERE left(Spur,2) = left('$Spur',2) AND NOT id = '$Bhf_ID' ORDER BY Kurzbezeichnung");}
				
				echo "<select style='width:220px;background-color:snow;border:2px solid;border-color:orange;font-size:14px' name='Kbz'>";
				echo"<option>Kbz. - ".$TEXT['lang-station']."</option><b>";
				while( $row=$db->sql_fetchrow($result) )
				{
					echo"<option>".$row[Kurzbezeichnung]." - ".$row[Haltestelle]."</option><b>";
				}
				echo"</select>";
				echo " <font size='4' style=color:red>".$TEXT['lang-kbzerr']."</font>";
			}
			else
			{
				if($Bhf_ID!="")
				{
					$db->sql_query("UPDATE bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
					SET bahnhof.Haltestelle = '$Betriebsstelle', bahnhof.Spur = '$Spur', bahnhof.Kurzbezeichnung = '$Kurzbezeichnung',
					bahnhof.Bahnverwaltung = '$Bahnverwaltung', bahnhof.Besitzer = '$Besitzer', bahnhof.Email = '$Email',
					bahnhof.Art = '$Art', bahnhof.Bhf_Bem = '$Bhf_Bem', bahnhof.Zeichnung = '$Zeichnung',
					bahnhof.Streckengleise = '$Streckengleise', bahnhof.Kreuzung = '$Kreuzung',
					bahnhof.Language = '$lang', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id'
					WHERE manage.MUser_ID = '$loggedInUser->user_id' and bahnhof.id = '$Bhf_ID'");
echo sql_error();
					$db->sql_query("UPDATE manage SET Betriebsstelle = '$Betriebsstelle' WHERE MUser_ID = '$loggedInUser->user_id' and Bhf_ID = '$Bhf_ID'");
					$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID
					SET fyp.Betriebsstelle = '$Betriebsstelle' WHERE manage.MUser_ID = '$loggedInUser->user_id' and fyp.Bhf_ID = '$Bhf_ID'");
echo sql_error();
				}
				elseif(isset($_REQUEST['Neu']))
				{
					if(@$_REQUEST['Neu'] == $loggedInUser->display_username)
					{
						$db->sql_query("INSERT INTO bahnhof (Haltestelle, Spur, Kurzbezeichnung, Bahnverwaltung, Besitzer, Email, Art, Bhf_Bem, Zeichnung, Language, LastUser, LastUser_ID)
						VALUES('$Betriebsstelle', '$Spur', '$Kurzbezeichnung', '$Bahnverwaltung', '$Besitzer', '$loggedInUser->email', '$Art', '$Bhf_Bem', '$Zeichnung', '$lang', '$loggedInUser->display_username', '$loggedInUser->user_id')");
echo sql_error();
						$SaveStation = 'Yes';
						$Mitverwalter = 1; // keine Mitverwalter
						$Bhf_ID = $db->sql_nextid();
						//$Rem=$Art." ".$TEXT['lang-added']." ".date("d.m.Y - H:i", time());
//						$db->sql_query("INSERT INTO manage (User, MUser_ID, Betriebsstelle, Bhf_ID, Mng_Bem, Mitverwalter)
	//					VALUES('$loggedInUser->display_username','$loggedInUser->user_id','$Betriebsstelle','$Bhf_ID','$Mng_Bem', '$Mitverwalter');");
						$db->sql_query("INSERT INTO manage (User, MUser_ID, Betriebsstelle, Bhf_ID, MainMngr_ID, Mng_Bem, Mitverwalter)
						VALUES('$loggedInUser->display_username','$loggedInUser->user_id','$Betriebsstelle','$Bhf_ID','','$Mng_Bem', '$Mitverwalter');");
echo sql_error();
						$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Betriebsstelle = manage.Betriebsstelle
						SET fyp.Bhf_ID = '$Bhf_ID' WHERE manage.MUser_ID = '$loggedInUser->user_id' and fyp.Betriebsstelle = '$Betriebsstelle' and fyp.Bhf_ID = '0'");
echo sql_error();
					}
				}
			}
		}

		if($Art=="Station")$Art_lang=$TEXT['lang-bh'];
		elseif($Art=="Connect")$Art_lang=$TEXT['lang-an'];
		elseif($Art=="Stop")$Art_lang=$TEXT['lang-hp'];
		elseif($Art=="Block")$Art_lang=$TEXT['lang-bl'];
		elseif($Art=="SBF")$Art_lang=$TEXT['lang-sbf'];
		else $Art_lang=$Art;

 		$DeinBahnhof=$db->sql_fetchrow($db->sql_query("SELECT Betriebsstelle FROM manage WHERE Bhf_ID = '$Bhf_ID' and MUser_ID = '$loggedInUser->user_id'"));
		if($DeinBahnhof!="" and $kbz['Kurzbezeichnung']=="") echo "<br>";
		elseif($kbz['Kurzbezeichnung']=="" and !isset($_REQUEST['Neu'])) ErrorMessage($Bhf_ID, $Betriebsstelle, $Spur, $loggedInUser, $lang);

//echo $SaveStation." ".$Bhf_ID;

?>
	</td>
	<td align='right'>
<?php	if($Email==$loggedInUser->email and $SaveStation=='Yes')
		{ ?>
			<input type="button" style="background-color:lightyellow;border:1px solid;border-color:red;font-size:11"
			value="<?php echo $TEXT['lang-station']?>&nbsp;<?php echo $TEXT['lang-del']?>"
			onclick="if(confirm('<?php echo $TEXT['lang-station']?>&nbsp;<?php echo $Betriebsstelle?>&nbsp;<?php echo $TEXT['lang-del']?> ?'))
			self.location.href='Bahnhof.php?action=Del_Bhf&Bhf_ID=<?php echo $Bhf_ID?>';"/>
<?php	} ?>
	</td>
</tr>
</table>

<table width='1015px' border=0 cellpadding=0 cellspacing=0>
<tr><td bgcolor=lightgreen colspan=8 ><img src=img/blank.gif width=1px height=3px></td></tr>
<tr>
	<?php selectBetriebsstelle($Sp, 'lightgreen', 'green', $lang, 'rtl', 'bold', '220', $loggedInUser->user_id)?>
	<form action=Bahnhof.php method=get>
		<td align='left' colspan='2' valign='middle' bgcolor='lightgreen'>
			<?php if(sizeof($Bhf)>1) echo "<input style='width: 255px; height:24px; font-size:12pt; font-weight:bold;
				background-color:#D0D0FF' type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";
			else echo "<input style='width: 255px; height:24px; font-size:12pt; font-weight:bold; background-color:#D0FFD0'
				type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";?>
			<input type=hidden name=Bhf_ID value=''>
			<input type=hidden name=Spur value='<?php echo $Spur?>'>
			<input type=hidden name=Treffen value='<?php echo $Treffen?>'>
			<input type=hidden size=1 maxlength=2 name=lang value='<?php echo $lang?>'>
		</td>
		<td bgcolor='lightgreen'>
			<?php if(sizeof($Bhf)>1) echo "<input style='width: 60px; height:24px; font-size:12pt; font-weight:bold;
				background-color:#D0D0FF' type=text maxlength=10 name=KurzBZ value='$Kurzbezeichnung'>";
			else echo "<input style='width: 60px; height:24px; font-size:12pt; font-weight:bold;
				background-color:#D0FFD0' type=text maxlength=10 name=KurzBZ value='$Kurzbezeichnung'>";?>
		</td>
		<td align='right' bgcolor=lightgreen >
			<input type=image src="img/key_enter.png">
		</td>
	</form>

	<?php selectSpur($Spur, $Sp, 'lightgreen', 'green', $Bhf)?>
	<td bgcolor='lightgreen'>
		<input type=button name=Neu style="background-color:green;border:1px solid;border-color:green;font-size:16px;color:white" value="<?php echo $TEXT['lang-Neu']?>"
		onClick=self.location.href="Bahnhof.php?Neu&Betriebsstelle=<?php echo $TEXT['lang-Neu']?>&Kurzbezeichnung=<?php echo $TEXT['lang-Neu']?>&Spur=&Sp=&Bhf_ID=">
	</td>
	<td bgcolor='lightgreen' width='427px'></td>
</tr>
</table>

<table width='1015px' align="left" style="float:left" border=0 cellpadding=0 cellspacing=0>
<colgroup>
	<col width="220px">
	<col width="60px">
	<col width="130px">
	<col width="150px">
	<col width="200px">
	<col width="80px">
	<col width="175px">
</colgroup>

<td align='right' style=font-family:Verdana ><?php echo $TEXT['lang-name']?>:&nbsp</td>
<form action='Bahnhof.php' method='POST' id='Bhf_Text' >

<?php if(@$_REQUEST['action']=="Del_Bhf" and isUserLoggedIn())
	{
		$seins = $db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE Mng_Bem = '' and Bhf_ID = '".round($Bhf_ID)."'"));

		if($seins['User']!="")
		{	// send email if train station deleted
			$UserEmail = $db->sql_fetchrow($db->sql_query("SELECT Email FROM Users WHERE Username = '$seins[User]'"));
			$email = $UserEmail['Email'];
			
			$subject="FYPages - Trainstation ".$Betriebsstelle." DELETED from ".$loggedInUser->display_username;
			
			$msg .= "Yellow Pages - Infomail \n\n";
			$msg .= "Die Betriebsstelle ".$Betriebsstelle." wurde von ".$loggedInUser->display_username." <".$loggedInUser->email."> gelöscht\n\n";
			$msg .= "Diese Email ist an Dich versandt worden weil ".$loggedInUser->display_username."\n";
			$msg .= "deine Betriebsstelle ".$Betriebsstelle." gelöscht hat.\n\n";
			$msg .= "Solltest Du damit nicht einverstanden sein kontaktiere bitte Georg Ziegler <g.zi@gmx.de>\n\n";
			$msg .= "Viele Grüsse,\n";
			$msg .= "Admin FYPages\n\n\n";
			
			$msg .= "English version ------------------------------------------------------------------------\n\n";
			$msg .= "Yellow Pages - Infomail \n\n";
			$msg .= "The trainstation ".$Betriebsstelle." has been deleted by ".$loggedInUser->display_username." <".$loggedInUser->email.">\n\n";
			$msg .= "This email has been sent to you because ".$loggedInUser->display_username."\n";
			$msg .= "has been deleted your trainstation ".$Betriebsstelle."\n\n";
			$msg .= "If you do not agree please contact Georg Ziegler <g.zi@gmx.de>\n\n";
			$msg .= "Best Regards,\n";
			$msg .= "Admin FYPages\n\n\n";
			
//			if($loggedInUser->email != 'g.zi@gmx.de') sendMail($email,$subject,$msg);
			if($loggedInUser->email != $email) sendMail($email,$subject,$msg);
//			sendMail($email,$subject,$msg);
		}

		$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID SET fyp.Bhf_ID = '0'
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and manage.Bhf_ID = ".round($_REQUEST['Bhf_ID']));
echo sql_error();

		$db->sql_query("DELETE gleise, manage, bahnhof FROM bahnhof
		LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID LEFT JOIN gleise ON bahnhof.id = gleise.Bhf_ID
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and bahnhof.id = ".round($_REQUEST['Bhf_ID']));
echo sql_error();

		$Mng_Bem = "<b>deleted from ".$loggedInUser->display_username." </b><".$loggedInUser->email."> at ".date("d.m.Y - H:i", time());
		$db->sql_query("UPDATE manage SET Mng_Bem = '$Mng_Bem'
		WHERE manage.Bhf_ID = ".round($_REQUEST['Bhf_ID']));

		unset($_SESSION['Bhf_ID']);
		unset($_SESSION['Betriebsstelle']);
		unset($Betriebsstelle);
		echo '<th align="left" colspan="3"><font size="+1" color="red">'.$Betriebsstelle.'  '.$TEXT['lang-deleted'].'</font></th>';
	}
	else
	{ ?>
		<th align="left" colspan="3"><input type=text style="font-size:14px;width:340px;
		<?php if(($Bhf_ID!="" and ($Kurzbezeichnung=="" or $kbz['Kurzbezeichnung']!="") or isset($_REQUEST['Neu'])))
		echo';background-color:yellow'?>" name=Betriebsstelle value='<?php echo ucfirst($Betriebsstelle)?>'></th>
<?php } ?>

<?php
//echo "B=".$Bhf_ID." K=".$Kurzbezeichnung." k=".$kbz." S=".$SaveStation;
?>
		<input type=hidden name=Treffen value='<?php echo $Treffen?>'>
		<input type=hidden name=Bhf_ID value='<?php echo $Bhf_ID?>'>
		<input type=hidden name=lang value='<?php echo $lang?>'>
		<input type=hidden name=Zeichnung value='<?php echo $Zeichnung?>'>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-group']?>:&nbsp</td><td align='left'><b>
			<select name=Spur style='width:80px;font-size:12px
			<?php if(($Bhf_ID!="" and ($Kurzbezeichnung=="" or $kbz['Kurzbezeichnung']!="") or isset($_REQUEST['Neu']))) echo';background-color:yellow';
			else echo';background-color:snow'?>' value='<?php echo $Spur?>' <?php if($SaveStation == "Yes") echo "onchange='submit()'"?>>
				<option value=<?php echo $Spur?>><?php echo $Spur?></option><?php selectSpurOption()?>
			</select>
		</td>
	</tr>
	<tr>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-kbz']?>:&nbsp</td><td align="left"><b>
		<input type='text' style="width:60px; font-size:14px
		<?php if(($Bhf_ID!="" and ($Kurzbezeichnung=="" or $kbz['Kurzbezeichnung']!="") or isset($_REQUEST['Neu'])))
		echo';background-color:yellow'?>" maxlength=10 name=Kurzbezeichnung value='<?php echo $Kurzbezeichnung?>'></b></td>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-owner']?>:&nbsp</td><td align="left">
		<input type='text' style="width:150px;font-size:14px" maxlength=50 name=Besitzer value='<?php echo $Besitzer?>'></td>
		<td align='right'>
			<?php $Zeichnung_DWG = rawurlencode(pathinfo($Zeichnung, PATHINFO_FILENAME).".dwg"); ?>
			<input type=button id='Zeichnung_DWG' style="background-color:lightyellow;border:1px solid;border-color:lightgreen;font-size:14px"
			value="<?php echo $TEXT['lang-drawing']?>:" onClick=self.location.href="Module/<?php echo $Spur?>/<?php echo $Zeichnung_DWG?>">
		</td>
		<td align='left'>
			<input type='text' style="width:80px;font-size:14px" maxlength='50' name='Zeichnung' id='Zeichnung' value='<?php echo $Zeichnung?>'>
		</td>
		<td align='left'>
			<?php $Zeichnung_PDF = rawurlencode(pathinfo($Zeichnung, PATHINFO_FILENAME).".pdf"); ?>
			<input type=button id='Zeichnung_PDF' style="background-color:lightyellow;border:1px solid;border-color:lightgreen;font-size:14px"
			value="PDF" onClick=self.location.href="Module/<?php echo $Spur?>/<?php echo $Zeichnung_PDF?>">
		</td><td></td>
	</tr>
	<tr>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-bvw']?>:&nbsp</td><td align='left' >
		<input type='text' style='width:60px; font-size:14px' maxlength='30' name='Bahnverwaltung' value='<?php echo $Bahnverwaltung?>'></td>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-email']?>:&nbsp</td><td>
		<input type='text' style='width:150px;font-size:14px' maxlength='50' name='Email' value='<?php echo $Email?>'></td>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-mts']?>:&nbsp</td>
		<td align='left' style='font-family:Verdana;font-size:16px' ><b><?php echo $Hauptgleise?></b></td>
	</tr>
	<tr>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-typ']?>:&nbsp</td>
		<td align='left' colspan='2' >
			<select style='font-size:14;background-color:snow' name='Art' value='<?php echo $Art?>' <?php if($SaveStation == "Yes") echo "onchange='submit()'"?>>
				<option value='<?php echo $Art?>' ><?php echo $Art_lang?></option>
				<option value='Station' ><?php echo $TEXT['lang-bh']?></option>
				<option value='Connect' ><?php echo $TEXT['lang-an']?></option>
				<option value='Stop' ><?php echo $TEXT['lang-hp']?></option>
				<option value='Block' ><?php echo $TEXT['lang-bl']?></option>
				<option value='SBF' ><?php echo $TEXT['lang-sbf']?></option>
			</select>
		<td></td>
		</td>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-st']?>:&nbsp</td>
		<td align='left'>
			<select style='font-size:14' name='Streckengleise' value='<?php echo $Streckengleise?>' <?php if($SaveStation == "Yes") echo "onchange='submit()'"?>>
				<option value='<?php echo $Streckengleise?>' ><?php echo $Streckengleise?></option>
				<option value='1' >1</option>
				<option value='2' >2</option>
				<option value='3' >3</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-rem']?>:&nbsp</td>
		<td align='left' colspan='3' ><input type='text' style="font-size:14px;width:340px;" maxlength=1000 name=Bhf_Bem value='<?php echo $Bhf_Bem?>'></td>
		<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-cl']?>:&nbsp</td>
		<td align='left' style='font-family:Verdana'><input type='text' style='width:40px;font-size:14px' maxlength='4' name='Kreuzung' value='<?php echo $Kreuzung?>'> cm</b></td>
	</tr>
	<tr>
		<td></td>
		<?php if(isset($back)) echo"<input type=hidden size=1 maxlength=12 name=back value=$back>"; ?>
		<td align='left' colspan='6' style='font-family:Verdana;font-size:12px' height='24px' >

<?php		$rowuser=$db->sql_fetchrow($db->sql_query("SELECT bahnhof.*, Users.Email, Users.Username FROM bahnhof
				LEFT JOIN Users ON Users.User_ID = bahnhof.LastUser_ID
				WHERE id = '".round($Bhf_ID)."'"));
			echo $TEXT['lang-lastchange'].' '.date('d.m.Y (G:i)',strtotime($rowuser['Timestamp'])).' '.$TEXT['lang-von'].
			' <a href="mailto:'.$rowuser['Email'].'?subject=FYPages - '.$TEXT['lang-station'].' '.$Betriebsstelle.'">'.$rowuser['Username'].'</a>';?>
			</td>
		</tr>
	</table>
<br clear=all>
	<table width='1020px' border='0' cellpadding='0' cellspacing='0' >
	<tr>
			<td style="font-family:Arial; font-size:16px; height:30px" valign="bottom" >
				<?php if($SaveStation!="Yes") echo "<br>"?>
				<b>&nbsp;<?php echo $TEXT['lang-einleitung']?>:</b>
			</td>
			<td align=right>
				<?php if($SaveStation=="Yes" || isset($_REQUEST['Neu']))
				{
					if(isset($_REQUEST['Neu']))
					{
						echo "<input type='hidden' name='Neu' value='".$loggedInUser->display_username."'>";
						echo "<input type='submit' style='background-color:orange; font-weight:bold; font-size:18px' ' value=".$TEXT['lang-save']."> ";
					}
					elseif($SaveStation=='Yes')
					{
						if($html=='off') echo "<input type='button' value='HTML' onClick=self.location.href='Bahnhof.php?html=on' style='background-color:lightblue; font-size:16px' ' >";
						else echo "<input type='button' value='EDIT' onClick=self.location.href='Bahnhof.php?html=off' style='background-color:orange; font-size:16px' ' >";
						echo "<input type='submit' style='background-color:yellow; font-weight:bold; font-size:18px' value=".$TEXT['lang-save']."> ";
					}
				} ?>
			</td>
		</tr>
		<tr>
			<td colspan='2' bgcolor='white' >
				<textarea form='Bhf_Text' id='Einleitung' style='width:1022px; height:200px; font-size:12pt' name='Einleitung'><?php echo $Einleitung ?></textarea>
			</td>
		</tr>
	</table>
</form>

<div style='position:absolute; top:135px; left:892px; width:130px; text-align:center; color:gray; font-family:Verdana'>
<?php
$verzeichnis = "Module/".$Spur."/";
if(!dirname($verzeichnis)!=$verzeichnis) {
	mkdir($verzeichnis);
	chmod($verzeichnis, 0777);}

$backup = "SQL/backup/Module/".$Spur."/";
if(!dirname($backup)!=$backup) {
	mkdir($backup);
	chmod($backup, 0777);}

$maxgroesse = 20480000; // 1024 Bytes = 1 KB

// Unter http://de.selfhtml.org/diverses/mimetypen.htm ist eine Liste der Mimetypen zu Finden.
$mimetypen = array(
 "dwg" => "application/octet-stream",
 "pdf" => "application/pdf",);
$_SESSION['mimetypen'] = $mimetypen;

if($SaveStation=="Yes"){?>
		<a style="font-family:Arial"><?php echo $TEXT['lang-fileupload_format']." ".implode(", ", array_unique(array_keys($mimetypen)))?></a><br>
		<div style="height:23px; overflow:hidden;">
			<input type=button style="background-color:lightgrey; font-size:12; width:130px; height:23px" value="<?php echo $TEXT['lang-file']?>">
<form method="POST" enctype="multipart/form-data">
	<input type="file" name="datei" size="1" style="width:130px; height:25px; opacity:0; filter:alpha(opacity:0); position:relative; top:<?php if(isIE()!="") echo"-44px"; else echo"-25px";?>;" />
		</div>
		<input type="submit" style="background-color:lightgrey; font-size:12; width:130px; height:23px" value="<?php echo $TEXT['lang-upload_dwg']?>"><br>
		<a style="font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold" >
<?php
	if ($Betriebsstelle!="" and is_array($_FILES["datei"]))
	{	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{	if (is_writeable($verzeichnis))
			{	if ($_FILES["datei"]["name"] != "" && $_FILES["datei"]["error"] == 0)
				{	if ($_FILES["datei"]["size"] <= $maxgroesse)
					{	$array = explode(".", basename($_FILES["datei"]["name"]));
						$dateiendung = strtolower(end($array));
						if (in_array($dateiendung, array_keys($mimetypen)))
						{	if (in_array($_FILES["datei"]["type"], $mimetypen))
							{	if (move_uploaded_file($_FILES["datei"]["tmp_name"], $verzeichnis.$Kurzbezeichnung."_".$Spur.substr($_FILES["datei"]["name"],-4)))
								{	chmod($verzeichnis.$Kurzbezeichnung."_".$Spur.substr($_FILES["datei"]["name"],-4), 0666);
									copy($verzeichnis.$_FILES["datei"]["name"], $backup.$Kurzbezeichnung."_".$Spur.substr($_FILES["datei"]["name"],-4));
									chmod($backup.$Kurzbezeichnung."_".$Spur.substr($_FILES["datei"]["name"],-4), 0666);
									$Zeichnung=$Kurzbezeichnung."_".$Spur;
									$db->sql_query("UPDATE bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
									SET bahnhof.Zeichnung = '$Zeichnung', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id'
									WHERE manage.MUser_ID = '$loggedInUser->user_id' and bahnhof.id = '$Bhf_ID'");
								echo sql_error();
?><script>
document.getElementById("Zeichnung").value = '<?php echo $Zeichnung?>';
document.getElementById("Zeichnung_DWG").setAttribute('onclick', 'self.location.href="Module/<?php echo $Spur?>/<?php echo $Zeichnung?>.dwg"');
document.getElementById("Zeichnung_PDF").setAttribute('onclick', 'self.location.href="Module/<?php echo $Spur?>/<?php echo $Zeichnung?>.pdf"');
</script><?php
									echo '<font size="4">'.$TEXT["lang-fileupload_executed"].'<br><b>'
									.number_format(($_FILES["datei"]["size"] / 1024), 2, ",", ".").' </b>KB</font>';}
								else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_error"].'</font>';}}
							else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_wrongmime"].$_FILES["bild"]["type"].'</font>';}}
						else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_dataformat"]."<b>".$dateiendung."</b><br>".$TEXT["lang-fileupload_notallowed"];}}
					else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_file"].
						number_format(($_FILES["bild"]["size"] / 1024), 2, ",", ".").$TEXT["lang-fileupload_toobig"].'</font>';}}
				else {echo '<font size="4">'.$TEXT["lang-fileupload_select"];}}
			else {echo $TEXT['lang-fileupload_subdir'].$verzeichnis.$TEXT['lang-fileupload_norights'];}}}}?>
</a></form>

<?php /* if($SaveStation=="Yes" and $_SESSION["sso"]==0 and !is_array($_FILES["datei"])) {?>
	<img src=img/blank.gif ><img src='img/puzzle.png' height='<?php if(isIE()!="") echo"48px"; else echo"64px";?>'
	onClick="self.location.href='Module.php?lang=<?php echo $lang?>'"><br>
	<img src=img/blank.gif height=1px><?php echo $TEXT['lang-module']?>
<?php } else echo "<img src=img/blank.gif >"; */?>

</div>

<?php if(@$_REQUEST['action']=="delete")
	{
		$db->sql_query("DELETE gleise FROM gleise LEFT JOIN manage ON gleise.Bhf_ID=manage.Bhf_ID
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and gleise.id=".round($_REQUEST['Gleis_ID']));
echo sql_error();
	}

	if(isset($_REQUEST['Change']) and trim($Gleisname)!="")
	{
		$db->sql_query("UPDATE gleise LEFT JOIN manage ON gleise.Bhf_ID = manage.Bhf_ID SET
		gleise.Gleisname='$Gleisname', gleise.Gleislaenge='$Gleislaenge', gleise.Gleisart='$Gleisart',
		gleise.Bahnsteiglaenge='$Bahnsteiglaenge',gleise.LadeFarbe='$LadeFarbe',gleise.Ladestelle='$Ladestelle',gleise.Gl_Bem='$Gl_Bem'
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and gleise.id = ".$Gleis_ID);
		$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID SET Ladestelle = '".$Ladestelle."'
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and Gleis_ID = ".$Gleis_ID);
		// setze alle Gleise mit demselben Namen auf die gleiche Gleisart
		$db->sql_query("UPDATE gleise LEFT JOIN manage ON gleise.Bhf_ID = manage.Bhf_ID
		SET gleise.Gleisart='$Gleisart' WHERE manage.MUser_ID = '$loggedInUser->user_id' and gleise.Gleisname = '".$Gleisname."' and gleise.Bhf_ID = ".$Bhf_ID);

		$db->sql_query("UPDATE bahnhof SET LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id' WHERE id = '$Bhf_ID'");
		if(sql_error()!="") @$_REQUEST['Add']="";
	}

	if(isset($_REQUEST['Add']) and trim($Gleisname)!="")
	{
		if($Gleislaenge=="")$Gleislaenge="0";
		if($Bahnsteiglaenge=="")$Bahnsteiglaenge="0";

		$check=$db->sql_fetchrow($db->sql_query("SELECT Gleisname FROM gleise WHERE Gleisname = '".$Gleisname."'
		AND Gleisart = '".$Gleisart."' AND Ladestelle = '".$Ladestelle."' AND Gl_Bem = '".$Gl_Bem."' AND Bhf_ID = '".$Bhf_ID."' "));
		if($check['Gleisname']=="")
		{
			$db->sql_query("INSERT INTO gleise (Gleisname, Gleislaenge, Gleisart, Bahnsteiglaenge, LadeFarbe, Ladestelle, Gl_Bem, Bhf_ID)
			VALUES('$Gleisname', '$Gleislaenge', '$Gleisart', '$Bahnsteiglaenge', '$LadeFarbe', '$Ladestelle', '$Gl_Bem', '$Bhf_ID');");
			$NewID = $db->sql_nextid();
			$_SESSION['Gleis_ID'] = round($NewID);
		}
		else echo "<font size='4' color='red'>&nbsp".$TEXT['lang-track']." <b>".$Gleisname."</b>".$TEXT['lang-exists']."</font>";
	}

	$Gleis_ID = intval(getVariableFromQueryStringOrSession('Gleis_ID'));

	if($Bahnsteiglaenge=="0")$Bahnsteiglaenge="";
?>

<?php if($SaveStation == "Yes")	{ ?>
<table width='1020px' border='0' cellpadding='0' cellspacing='0' >
	<form action=Bahnhof.php method=get>
		<tr valign=bottom><td bgcolor=green colspan=11><img src=img/blank.gif width=1px height=8px></td></tr>
		<th>
			<td><img src=img/blank.gif style='width:5px'></td>
			<td><input type=text style='width:80px;font-size:14px;<?php if(trim($Gleisname)=="")echo'background-color:yellow'?>' maxlength=50 name=Gleisname value='<?php echo $Gleisname?>'></td>
			<td align="left" style='width:130px'>
				<select style='font-size:14;background-color:snow;width:130px' name=Gleisart value='<?php echo $Gleisart?>'">
					<?php if($Gleisart=="") $Gleisart="Main" ?>
					<option value=<?php echo $Gleisart?>><?php echo $Gleisart_lang?></option>
					<option value="Main"><?php echo $TEXT['lang-mt']?></option>
					<option value="Siding"><?php echo $TEXT['lang-rangier']?></option>
					<option value="Storage Siding"><?php echo $TEXT['lang-abstell']?></option>
					<option value="Depot"><?php echo $TEXT['lang-bw']?></option>
				</select>
			</td>
			<td><input type=text style='width:100px;font-size:14px' maxlength=4 name=Gleislaenge value='<?php echo $Gleislaenge?>'></td>
			<td><input type=text style='width:100px;font-size:14px' maxlength=4 name=Bahnsteiglaenge value='<?php echo $Bahnsteiglaenge?>'></td>
			<td>
				<input name=LadeFarbe value='<?php echo $LadeFarbe?>'
				style='cursor:crosshair;width:22px;height:22px;font-size:8px;border:2px solid;border-color:gray;'
				maxlength=6 class="color {slider:true,pickerFaceColor:'lightgrey',pickerFace:1,pickerBorder:0,pickerInsetColor:'black'}">
			</td>
			<td><input type=text style='width:225px;font-size:14px' maxlength=50 name=Ladestelle value='<?php echo $Ladestelle?>'></td>
			<td><input type=text style='width:285px;font-size:14px' maxlength=100 name=Gl_Bem value='<?php echo $Gl_Bem?>'></td>
			<td>
				<button type='submit' name='Change' style='width:35px' >
					<img height='18px' border='0' src='img/ok.png' alt=<?php echo $TEXT['lang-save']?>><br>
				</button>
			</td>
			<td align='right' >
				<button type='submit' name='Add' style='width:35px' >
					<img height='18px' border='0' src='img/addblue.png' alt=<?php echo $TEXT['lang-add']?>><br>
				</button>
			</td>
			<td><img src=img/blank.gif style='width:15px'></td>
		</th>
	</form>
</table>
<?php } ?>

<table width='1020px' border='0' cellpadding='0' cellspacing='0' >
<colgroup>
	<col width="10px">
	<col width="80px">
	<col width="130px">
	<col width="100px">
	<col width="100px">
	<col width="20px">
	<col width="5px">
	<col width="225px">
	<col width="280px">
	<col width="35px">
	<col width="35px">
</colgroup>
	<tr bgcolor=lightgreen style='font-family:Arial' >
		<td><img src=img/blank.gif></td>
		<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-track']?></b></td>
		<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-typ']?></b></td>
		<td style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><img src=img/blank.gif><br><b><?php echo $TEXT['lang-tl']?></b></td>
		<td style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><img src=img/blank.gif><br><b><?php echo $TEXT['lang-bs']?></b></td>
		<td><img src=img/blank.gif><br><b></b></td>
		<td><img src=img/blank.gif><br><b></b></td>
		<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-ls']?></b></td>
		<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-rem']?></b></td>
		<td><img src=img/blank.gif><br></td>
		<td><img src=img/blank.gif><br></td>
	</tr>

<?php

$result = getGleisliste($Bhf_ID);
echo sql_error();

	$i=0;
	$SameTrack="";
	$SameType="";
	$farbe = 0;
	while( $row=$db->sql_fetchrow($result) )
	{
		if($SameTrack==$row['Gleisname'] and $SameType==$row['Gleisart'])
		{
			if($Gleis_ID==$row['id']) echo "<tr style='background-color: " . (1 ? '#bbb' : '#efe')  . "' valign=center>";
			else echo "<tr style='background-color: " . ($farbe ? '#cfc' : '#efe')  . "' valign=center>";

			echo "<td><img src=img/blank.gif width=10px height=20px></td>";
			echo "<td>";
			echo "<td>";
			echo "<td>";
			echo "<td>";
		}
		else
		{
			if($i>0)
			{
				echo "<tr valign=bottom>";
				if($SameType!=$row['Gleisart'])
				{
					echo "<td bgcolor=green colspan='11' ><img src=img/blank.gif width=1px height=2px></td>";
				}
				else
				{
					if($row['Gleisart']!='Main') echo "<td bgcolor=green colspan='11' ><img src=img/blank.gif width=1px height=1px></td>";
				}
				echo "</tr>";
			}

			if($Gleis_ID==$row['id']) echo "<tr style='background-color:#bbb' valign=center>";
			else echo "<tr style='background-color:".($farbe ? '#cfc' : '#efe')."' valign=center>";

			echo "<td><img src=img/blank.gif width=10px height=20px></td>";
			echo "<td>".$row['Gleisname']."&nbsp;</td>";

			if($row['Gleisart']=="Main") echo "<td>".$TEXT['lang-mt']."&nbsp;</td>";
			elseif($row['Gleisart']=="Siding") echo "<td>".$TEXT['lang-rangier']."&nbsp;</td>";
			elseif($row['Gleisart']=="Storage Siding") echo "<td>".$TEXT['lang-abstell']."&nbsp;</td>";
			elseif($row['Gleisart']=="Storage") echo "<td>".$TEXT['lang-abstell']."&nbsp;</td>";
			elseif($row['Gleisart']=="Depot") echo "<td>".$TEXT['lang-bw']."&nbsp;</td>";
			else echo "<td>".$row['Gleisart']."&nbsp;</td>";

			echo "<td>".htmlspecialchars($row['Gleislaenge'])."&nbsp;</td>";
			if($row['Bahnsteiglaenge']=="0") echo "<td>";
			else echo "<td>".$row['Bahnsteiglaenge']."&nbsp;</td>";
		}
		$SameTrack = $row['Gleisname'];
		$SameType = $row['Gleisart'];
		$farbe = !$farbe;

		if($row['LadeFarbe'] and $row['LadeFarbe'] != 'FFFFFF' )
		{
			if($Gleis_ID==$row['id']) echo "<td bgcolor=".$row['LadeFarbe']." style='font-size:0px;border:4px solid #bbb'></td>";
			else echo "<td bgcolor=".$row['LadeFarbe']." style='font-size:0px;border:4px solid ".(!$farbe ? '#cfc' : '#efe')."'></td>";
		}
		else echo "<td></td>";

		echo "<td></td>";
		echo "<td>".$row['Ladestelle']."&nbsp;</td>";
		echo "<td>".$row['Gl_Bem']."&nbsp;</td>";

		if(isUserLoggedIn())
		{
			$DeinBahnhoef=$db->sql_query("SELECT Betriebsstelle FROM manage
			WHERE Betriebsstelle = '".escape($Betriebsstelle)."' and MUser_ID = '$loggedInUser->user_id'");
			$FreieBahnhoefe=$db->sql_query("SELECT Betriebsstelle FROM manage
			WHERE Betriebsstelle = '".escape($Betriebsstelle)."'");

			if($db->sql_fetchrow($DeinBahnhoef)!="" or ($SaveStation == "Yes"))
			{
				echo "<td align=middle ><a onclick=\"return\"href=Bahnhof.php?action=edit&Gleis_ID=".$row['id'].">
					<img style='height:18px;border:0px' src='img/edit.png'></a></td>";
				if($row['Ladestelle']!="") $Ls_ = '\n'.$TEXT['lang-ls'].": ".$row['Ladestelle'];
				echo "<td align=middle><a onclick=\"return confirm('".$TEXT['lang-track'].': '.$row['Gleisname'].$Ls_
				.'\n'.$TEXT['lang-del']."?');\"
				href=Bahnhof.php?action=delete&Gleis_ID=".$row['id']."&Betriebsstelle=".$Betriebsstelle.">
				<img style='height:18px;border:0px' src='img/del.png'></a></td>";
			}
			else
			{
				echo "<td align=middle></td>";
				echo "<td align=middle></td>";
			}

			if($row['Bhf_ID']==0 and $Bhf_ID!=0)
			{
				$db->sql_query("UPDATE gleise SET Bhf_ID = ".round($Bhf_ID)." WHERE id = ".round($row['id']));
			}
		}
		echo "</tr>";
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor=green colspan='11' ><img src=img/blank.gif width=1px height=8px></td></tr>";
	echo '</table>';


$verzeichnis = "Bilder/";
$backup = "SQL/backup/Bilder/";
$maxgroesse = 10240000; // 1024 Bytes = 1 KB

// Unter http://de.selfhtml.org/diverses/mimetypen.htm ist eine Liste der Mimetypen zu Finden.
$mimetypen = array(
 "png" => "image/png",
 "jpg" => "image/jpeg",
 "jpg" => "image/pjpeg",
 "jpeg" => "image/jpeg",
 "gif" => "image/gif",);
?>


 
<?php 
if($SaveStation=="Yes"){?>
	<table width='1020px' border='0' cellpadding='0' cellspacing='0' >
	<td bgcolor="gainsboro" style="font-family:Arial; font-size:16px" >
		<div style="height:23px; overflow:hidden;">
			<input type=button style="background-color:lightgrey; font-size:12; width:150px; height:23px" value="<?php echo $TEXT['lang-file']?>">
			<?php echo $TEXT['lang-fileupload_format']." ".implode(", ", array_unique(array_keys($mimetypen))).
			" - ".$TEXT['lang-fileupload_maxsize']." ".(number_format(($maxgroesse / 1024000), 0, ",", "."))?> MB
<form method="POST" enctype="multipart/form-data" id="Pic_Glpln" >
	<input form='Pic_Glpln' type="file" name="pic" size="1" style="width:150px; height:25px; opacity:0; filter:alpha(opacity:0);  position:relative; top:<?php if(isIE()!="") echo"-44px"; else echo"-25px";?>;" />
		</div>
		<input form='Pic_Glpln' type="submit" style="background-color:lightgrey; font-size:12; width:150px; height:23px" value="<?php echo $TEXT['lang-upload_tracklayout']?>">
<?php if ($Betriebsstelle!="" and is_array($_FILES["pic"]))
	{	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{	if (is_writeable($verzeichnis))
			{	if ($_FILES["pic"]["name"] != "" && $_FILES["pic"]["error"] == 0)
				{	if ($_FILES["pic"]["size"] <= $maxgroesse)
					{	$array = explode(".", basename($_FILES["pic"]["name"]));
						$dateiendung = strtolower(end($array));
						if (in_array($dateiendung, array_keys($mimetypen)))
						{	if (in_array($_FILES["pic"]["type"], $mimetypen))
							{	if (move_uploaded_file($_FILES["pic"]["tmp_name"], $verzeichnis."pic_Bhf_ID_".$Bhf_ID ))
								{	chmod($verzeichnis."pic_Bhf_ID_".$Bhf_ID, 0666);
									copy($verzeichnis."pic_Bhf_ID_".$Bhf_ID, $backup."pic_Bhf_ID_".$Bhf_ID);
									chmod($backup."pic_Bhf_ID_".$Bhf_ID, 0666);
									echo '<font size="4">'.$TEXT["lang-fileupload_executed"].'<b>'
									.$_FILES["pic"]["name"]. '</b> - ' . number_format(($_FILES["pic"]["size"] / 1024), 2, ",", ".") . ' KB</font>';}
								else
								{	echo '<font size="4" color="red">'.$TEXT["lang-fileupload_error"].'</font>';}}
							else
							{	echo '<font size="4" color="red">'.$TEXT["lang-fileupload_wrongmime"].$_FILES["pic"]["type"].'</font>';}}
						else
						{	echo '<font size="4" color="red">'.$TEXT["lang-fileupload_dataformat"]."<b>".$dateiendung."</b>".$TEXT["lang-fileupload_notallowed"];}}
					else
					{	echo '<font size="4" color="red">'.$TEXT["lang-fileupload_file"].
						number_format(($_FILES["pic"]["size"] / 1024), 2, ",", ".").$TEXT["lang-fileupload_toobig"].'</font>';}}
				else
				{	echo '<font size="4">'.$TEXT["lang-fileupload_select"];}}
			else
			{	echo $TEXT['lang-fileupload_subdir'].$verzeichnis.$TEXT['lang-fileupload_norights'];}}}?>
	</td>
	<td bgcolor='gainsboro' align='right' width='300px' style="font-family:Arial; font-size:28px" >
		<?php if(file_exists('Bilder/pic_Bhf_ID_'.$Bhf_ID)){
			echo "<a onclick=\"return confirm('".$TEXT['lang-tracklayout'].'\n[ '.$Betriebsstelle
			.' ]\n'.$TEXT['lang-del']."?');\"
			href=Bahnhof.php?DelPic=pic_Bhf_ID_".$Bhf_ID.">
			<img style='height:18px;border:0px' src='img/del.png'></a></td>";}
			else echo $TEXT['lang-upload_tracklayout']; ?>
	</td>
	<td bgcolor='gainsboro' align='right' width='10px' >
	</td>
	</table>
</form>
<?php if(file_exists('Bilder/pic_Bhf_ID_'.$Bhf_ID)) echo "<img src='Bilder/pic_Bhf_ID_".$Bhf_ID."' width='1024px' >"?>
<?php } ?>


<table width='1020px' border='0' cellpadding='0' cellspacing='0' >
	<tr>
		<td style='font-family:Arial; font-size:16px; height:30px' valign="bottom" >
			<b>&nbsp;<?php echo $TEXT['lang-beschreibung']?>:</b>
		</td>
		<td align=right>
		</td>
	</tr>
	<tr>
		<td colspan='2' bgcolor='white' >
			<textarea form='Bhf_Text' id='Beschreibung' style='width:1022px; height:200px; font-size:12pt; overflow:hidden' name='Beschreibung'><?php echo $Beschreibung ?></textarea>
		</td>
	</tr>
	<tr>
		<td style="font-family:Arial; font-size:16px; height:30px" valign="bottom" >
			<b>&nbsp;<?php echo $TEXT['lang-personenverkehr']?>:</b>
		</td>
		<td align=right>
		</td>
	</tr>
	<tr>
		<td colspan='2' bgcolor='white' >
			<textarea form='Bhf_Text' id='Personenverkehr' style='width:1022px; height:200px; font-size:12pt' name='Personenverkehr'><?php echo $Personenverkehr ?></textarea>
		</td>
	</tr>
	<tr>
		<td style="font-family:Arial; font-size:16px; height:30px" valign="bottom" >
			<b>&nbsp;<?php echo $TEXT['lang-frachtverkehr']?>:</b>
		</td>
		<td align=right>
		</td>
	</tr>
	<tr>
		<td colspan='2' bgcolor='white' >
			<textarea form='Bhf_Text' id='Frachtverkehr' style='width:1022px; height:200px; font-size:12pt' name='Frachtverkehr'><?php echo $Frachtverkehr ?></textarea>
		</td>
	</tr>
</table>


<?php
$verzeichnis = "Bilder/";
$backup = "SQL/backup/Bilder/";
$maxgroesse = 10240000; // 1024 Bytes = 1 KB

// Unter http://de.selfhtml.org/diverses/mimetypen.htm ist eine Liste der Mimetypen zu Finden.
$mimetypen = array(
 "png" => "image/png",
 "jpg" => "image/jpeg",
 "jpg" => "image/pjpeg",
 "jpeg" => "image/jpeg",
 "gif" => "image/gif",);

if($SaveStation=="Yes"){?>
	<table width='1020px' border='0' cellpadding='0' cellspacing='0' >
	<td bgcolor="gainsboro" style="font-family:Arial; font-size:16px" >
		<div style="height:23px; overflow:hidden;">
			<input type=button style="background-color:lightgrey; font-size:12; width:150px; height:23px" value="<?php echo $TEXT['lang-file']?>">
			<?php echo $TEXT['lang-fileupload_format']." ".implode(", ", array_unique(array_keys($mimetypen))).
			" - ".$TEXT['lang-fileupload_maxsize']." ".(number_format(($maxgroesse / 1024000), 0, ",", "."))?> MB
<form method="POST" enctype="multipart/form-data">
	<input type="file" name="bild" size="1" style="width:150px; height:25px; opacity:0; filter:alpha(opacity:0);  position:relative; top:<?php if(isIE()!="") echo"-44px"; else echo"-25px";?>;" />
		</div>
		<input type="submit" style="background-color:lightgrey; font-size:12; width:150px; height:23px" value="<?php echo $TEXT['lang-upload_pic']?>">
<?php if ($Betriebsstelle!="" and is_array($_FILES["bild"]))
	{	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{	if (is_writeable($verzeichnis))
			{	if ($_FILES["bild"]["name"] != "" && $_FILES["bild"]["error"] == 0)
				{	if ($_FILES["bild"]["size"] <= $maxgroesse)
					{	$array = explode(".", basename($_FILES["bild"]["name"]));
						$dateiendung = strtolower(end($array));
						if (in_array($dateiendung, array_keys($mimetypen)))
						{	if (in_array($_FILES["bild"]["type"], $mimetypen))
							{	if (move_uploaded_file($_FILES["bild"]["tmp_name"], $verzeichnis."bild_Bhf_ID_".$Bhf_ID ))
								{	chmod($verzeichnis."bild_Bhf_ID_".$Bhf_ID, 0666);
									copy($verzeichnis."bild_Bhf_ID_".$Bhf_ID, $backup."bild_Bhf_ID_".$Bhf_ID);
									chmod($backup."bild_Bhf_ID_".$Bhf_ID, 0666);
									echo '<font size="4">'.$TEXT["lang-fileupload_executed"].'<b>'
									.$_FILES["bild"]["name"]. '</b> - ' . number_format(($_FILES["bild"]["size"] / 1024), 2, ",", ".") . ' KB</font>';}
								else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_error"].'</font>';}}
							else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_wrongmime"].$_FILES["bild"]["type"].'</font>';}}
						else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_dataformat"]."<b>".$dateiendung."</b>".$TEXT["lang-fileupload_notallowed"];}}
					else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_file"].
						number_format(($_FILES["bild"]["size"] / 1024), 2, ",", ".").$TEXT["lang-fileupload_toobig"].'</font>';}}
				else {echo '<font size="4">'.$TEXT["lang-fileupload_select"];}}
			else {echo $TEXT['lang-fileupload_subdir'].$verzeichnis.$TEXT['lang-fileupload_norights'];}}}?>
	</td>
	<td bgcolor="gainsboro" align='right' width="300px" style="font-family:Arial; font-size:28px" >
		<?php if(file_exists('Bilder/bild_Bhf_ID_'.$Bhf_ID)){
			echo "<a onclick=\"return confirm('".$TEXT['lang-picture'].'\n[ '.$Betriebsstelle
			.' ]\n'.$TEXT['lang-del']."?');\"href=Bahnhof.php?DelPic=bild_Bhf_ID_".$Bhf_ID.">
			<img style='height:18px;border:0px' src='img/del.png'></a></td>";}
			else echo $TEXT['lang-upload_pic']; ?>
	</td>
	<td bgcolor='gainsboro' align='right' width='10px' >
	</td>
	</table>
</form>
<?php } if(file_exists('Bilder/bild_Bhf_ID_'.$Bhf_ID)) echo "<img src='Bilder/bild_Bhf_ID_".$Bhf_ID."' width='1024px' >"?>

<br>
<script>
<?php

/*	ckeditor.com/download -> Full Package + exchange config.js + copy allowsave ->plugins
		or
	upload build-config.js to ckeditor.com/builder
		or
	ckeditor.com/builder
		Allow Save
		Color Button
		Color Dialog
		Equation Editor
		Font Size and Family
		Format
		Horizontal Rule
		Image
		Justify
		Page Break
		Remove Format
		Table
		Table Resize
		Table Tools
*/

if($html=='off') { ?>
	CKEDITOR.inline('Einleitung', {language:'<?php echo strtolower($lang)?>', uiColor:'#E0FFE0'<?php if($SaveStation=="Yes"){?>, extraPlugins:'allowsave'<?php } ?>});
	CKEDITOR.inline('Beschreibung', {language:'<?php echo strtolower($lang)?>', uiColor:'#E0FFE0'<?php if($SaveStation=="Yes"){?>, extraPlugins:'allowsave'<?php } ?>});
	CKEDITOR.inline('Personenverkehr', {language:'<?php echo strtolower($lang)?>', uiColor:'#E0FFE0'<?php if($SaveStation=="Yes"){?>, extraPlugins:'allowsave'<?php } ?>});
	CKEDITOR.inline('Frachtverkehr', {language:'<?php echo strtolower($lang)?>', uiColor:'#E0FFE0'<?php if($SaveStation=="Yes"){?>, extraPlugins:'allowsave'<?php } ?>});
<?php } ?>
</script>
</body>
</html>
