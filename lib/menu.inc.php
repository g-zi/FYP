<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

	if(!isset($_SESSION)) session_start();

// Define variables for language selection

	$lang = rawurlencode(getVariableFromQueryStringOrSession('lang'));

	$languages['BG']="Bulgarian";
	$languages['CS']="Czech";
	$languages['DA']="Dansk";
	$languages['DE']="Deutsch";
	$languages['ET']="Estonian";
	$languages['EL']="Greek";
	$languages['EN']="English";
	$languages['ES']="Espanol";
	$languages['FI']="Suomeksi";
	$languages['FR']="Francais";
	$languages['HU']="Hungarian";
	$languages['IT']="Italiano";
	$languages['LT']="Latvian";
	$languages['LV']="Lithuanian";
	$languages['NL']="Nederlands";
	$languages['NO']="Norsk";
	$languages['PL']="Polski";
	$languages['PT']="Portuges";
	$languages['RO']="Rumanian";
	$languages['RU']="Russian";
	$languages['SK']="Slovak";
	$languages['SL']="Slovene";
	$languages['SV']="Svenska";
	@include("lang/Symbol.php");
	@include("lang/DE.php");
	@include("lang/$lang.php");

	if($lang=="") { $lang="DE"; $_SESSION['lang']="DE"; }	
//	if($lang=="DE" or $lang=="EN" or $lang=="ES" or $lang=="NL" or $lang=="SV") $_SESSION['language'] = strtolower($lang); // fuer Usercake
//	else $_SESSION['language'] = "en"; // fuer Usercake
	$_SESSION['language'] = strtolower($lang); // fuer Usercake


// Define other variables

//	$Treffen=ucfirst(str_replace("'","`",getVariableFromQueryStringOrSession('Treffen')));
	$Betriebsstelle=ucfirst(str_replace("'","`",getVariableFromQueryStringOrSession('Betriebsstelle')));

if($_REQUEST['Bhf_ID']!="Sp") $Bhf_ID = getVariableFromQueryStringOrSession('Bhf_ID');

	$Kurzbezeichnung = getVariableFromQueryStringOrSession('Kurzbezeichnung');
	$Spur = getVariableFromQueryStringOrSession('Spur');
	$Sp = getVariableFromQueryStringOrSession('Sp');
	if($Sp!="") $Spur = $Sp;

	$back = getVariableFromQueryStringOrSession('back');

	$sort = getVariableFromQueryStringOrSession('sort');
	if($sort=="") $sort="fyp.Produktbeschreibung";
	elseif($sort=="nhm") $sort="fyp.NHM_Code";
	elseif($sort=="prd") $sort="fyp.Produktbeschreibung, fyp.Betriebsstelle, fyp.Anschliesser, fyp.Ladestelle";
	elseif($sort=="int") $sort="fyp.Product_Description, fyp.Betriebsstelle, fyp.Anschliesser, fyp.Ladestelle";
	elseif($sort=="an") $sort="fyp.Anschliesser, fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Ladestelle";
	elseif($sort=="rem") $sort="fyp.Ladestelle, fyp.FYP_Bem, fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Anschliesser";
	elseif($sort=="station") $sort="fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Anschliesser, fyp.Ladestelle";

	$sorting['fyp.Produktbeschreibung, fyp.Betriebsstelle, fyp.Anschliesser, fyp.Ladestelle']=$TEXT['lang-product'];
	$sorting['fyp.Product_Description, fyp.Betriebsstelle, fyp.Anschliesser, fyp.Ladestelle']=$TEXT['lang-int'];
	$sorting['fyp.Anschliesser, fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Ladestelle']=$TEXT['lang-an'];
	$sorting['fyp.Ladestelle, fyp.FYP_Bem, fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Anschliesser']=$TEXT['lang-ls']." / ".$TEXT['lang-rem'];
	$sorting['fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Anschliesser, fyp.Ladestelle']=$TEXT['lang-station'];


	require_once('lib/write.pdf.php');
	require_once('lib/write.excel.php');
	require_once('lib/read.excel.php');


// Search for Betriebsstelle ------------------------------------ Problem: beim Wechseln der Spur wird manchmal die 2te Spur der Betriebsstelle angewählt.

//echo "&nbsp&nbspB=".$Betriebsstelle."/".$_SESSION['Betriebsstelle']."[".$_REQUEST['Betriebsstelle']."] I=".$Bhf_ID."/".$_SESSION['Bhf_ID']."[".$_REQUEST['Bhf_ID']."] Sp=".$Sp."[".$_REQUEST['Sp']."] Spur=".$Spur."[".$_REQUEST['Spur']."] Last_Bhf_ID=".$_SESSION['Last_Bhf_ID'];

	if(isset($_REQUEST['Bhf_ID']))
	{ // find name of Betriebsstelle based on Bhf_ID
		if($_REQUEST['Bhf_ID']=="Sp")
		{ // used to set Betriebsstellen group selection to all.
			$Sp="";
			$_SESSION['Sp'] = "";
			$Bhf_ID = $_SESSION['Last_Bhf_ID'];
		}
		elseif($Bhf_ID!="")
		{
			$row = $db->sql_fetchrow($db->sql_query("SELECT Haltestelle, Spur FROM bahnhof WHERE id = '$Bhf_ID'"));
			$Betriebsstelle = $row['Haltestelle'];
			$Spur = $row['Spur'];
			$Kbz = $row['Kurzbezeichnung'];
		}
	}

	if(isset($_REQUEST['Betriebsstelle']))
	{
		if(@$_REQUEST['KurzBZ']!="" and strtolower($_REQUEST['KurzBZ'])!=strtolower($_SESSION['KBZ']))
		{
			$KurzBZ = getVariableFromQueryStringOrSession('KurzBZ');

			$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof 
			WHERE Kurzbezeichnung = '".escape($KurzBZ)."'"));
			if($row!='') 
			{
				$Betriebsstelle = $row['Haltestelle'];
				$Spur = $row['Spur'];
				$_SESSION['KBZ'] = $Kbz = $KurzBZ;
				$Bhf_ID = $row['id'];
			}
		}
		elseif($Betriebsstelle!="")
		{ // 1,
			if($Sp!="")
			{ // 1,1
				$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle like '%$Betriebsstelle%' and Spur = '$Sp'"));
			}
			else
			{ // 1,0
				$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle like '%$Betriebsstelle%'"));
			}
		}
		else
		{  // if no Betriebsstelle is entered
			$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof 
			INNER JOIN manage on bahnhof.id = manage.Bhf_ID 
			WHERE manage.MUser_ID = '$loggedInUser->user_id'"));
		}
		if($row['Haltestelle']=="")
		{ // Missed Betriebsstelle;
			$Bhf_ID = "";
			$_SESSION['Last_Bhf_ID']="";
			$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle like '%$Betriebsstelle%'"));
		}
		if($row['Haltestelle']!="")
		{
			$Betriebsstelle = $row['Haltestelle'];
			$Bhf_ID = $row[9];
			$Spur = $row[1];
			$_SESSION['KBZ'] = $Kbz = $row[2];
		}
	}
	elseif(isset($_REQUEST['Sp']))
	{ // if different Spur to the Betriebsstelle is selected then search for this combination
		{
			$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle' and Spur = '$Sp';"));
			if($row['Haltestelle']!="")
			{
				$Betriebsstelle = $row['Haltestelle'];
				$Spur = $row['Spur'];
				$Bhf_ID = $row['id'];
				$_SESSION['KBZ'] = $Kbz = $row['Kurzbezeichnung'];
			}
		}
	}
	



// Extend selectSpur to multiple Betriebsstellen entries
	$result=$db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle';");
	$Bhf = array();
	$i=0;
	while($row=$db->sql_fetchrow($result))
	{
		$Bhf[] = array('Name'=>$row['Haltestelle'],'Kbz'=>$row['Kurzbezeichnung'],'id'=>$row['id'],'Spur'=>$row['Spur'],'Besitzer'=>$row['Besitzer']);
		$i++;
	}

	$_SESSION['Betriebsstelle']=$Betriebsstelle;
	if($Bhf_ID!="" and $Bhf_ID!="Sp") $_SESSION['Last_Bhf_ID'] = $Bhf_ID;
	if($Bhf_ID=="") $Bhf_ID = $_SESSION['Last_Bhf_ID'];

	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$_SESSION[Last_Bhf_ID]'"));
	if($row['Haltestelle']!="")
	{
		$Betriebsstelle = $row['Haltestelle'];
		$Spur = $row['Spur'];
		$Kurzbezeichnung = $Kbz = $row['Kurzbezeichnung'];
	}

//echo "<br>*B=".$Betriebsstelle."/".$_SESSION['Betriebsstelle']."[".$_REQUEST['Betriebsstelle']."] I=".$Bhf_ID."[".$_REQUEST['Bhf_ID']."] Sp=".$Sp."[".$_REQUEST['Sp']."] Spur=".$Spur."[".$_REQUEST['Spur']."] Last_Bhf_ID=".$_SESSION['Last_Bhf_ID'];
//echo $Kbz.$_SESSION[Last_Bhf_ID].$Bhf_ID.$Betriebsstelle.$Spur;

function selectBack($width,$lang)
{	// Select language
	@include("lang/DE.php");
	@include("lang/$lang.php");
	if($lang=="")$lang="DE";

	if(!isset($_SESSION['bck'])) $_SESSION['bck'] = array('Main.php');
	if(isset($_REQUEST['bk']))
	{
		$_SESSION['back'] = array_pop($_SESSION['bck']);
		$_SESSION['back'] = array_pop($_SESSION['bck']);
		$_SESSION['back'] = end($_SESSION['bck']);
		array_push($_SESSION['bck'], str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1)));
	}
	else
	{
		if(end($_SESSION['bck']) != str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1)))
		{
			$_SESSION['back'] = end($_SESSION['bck']);
			array_push($_SESSION['bck'], str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1)));
		}
	}

	$FYpages = "gray";
	$Station = "gray";
	$Meeting = "gray";
	$Waybill = "gray";

	switch(str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1))) 
	{
		case "FYP.php": $FYpages = "blue"; break;
		case "Bahnhof.php": $Station = "blue"; break;
		case "Treffen.php": $Meeting = "blue"; break;
		case "Frachtzettel.php": $Waybill = "blue"; break;
	}

//echo print_r($_SESSION['bck']);
//print_r(array_values($_SESSION['bck']));
//echo $_SESSION['back'];
	echo"<div style='position:absolute; top:0px; left:$width; width:60px; text-align:center; font-size:70%; color:black; font-family:Verdana' onClick=self.location.href='".$_SESSION['back'] ."?bk'>
		<img valign='bottom' width='40' border='0' src='img/Back.png'>
		<br>Back</div>";

	$wdt=$width-50;
	if(str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1))!="Manage.php")
	{
		echo"<div style='position:absolute; top:0px; left:$wdt; width:60px; text-align:center; font-size:70%; color:".$Waybill,"; font-family:Verdana' onClick=self.location.href='Frachtzettel.php'>
			<img valign='bottom' width='40' border='0' src='img/Cargo_Y.png'>";
		echo"<br>Waybills</div>";

	$wdt=$width-100;
		echo"<div style='position:absolute; top:0px; left:$wdt; width:60px; text-align:center; font-size:70%; color:".$Meeting,"; font-family:Verdana' onClick=self.location.href='Treffen.php'>
			<img valign='bottom' width='40' border='0' src='img/Meeting.png'>";
		echo"<br>Meeting</div>";

	$wdt=$width-150;
		echo"<div style='position:absolute; top:0px; left:$wdt; width:60px; text-align:center; font-size:70%; color:".$Station,"; font-family:Verdana' onClick=self.location.href='Bahnhof.php'>
			<img valign='bottom' width='40' border='0' src='img/Tools.png'>";
		echo"<br>Station</div>";

	$wdt=$width-200;
		echo"<div style='position:absolute; top:0px; left:$wdt; width:60px; text-align:center; font-size:70%; color:".$FYpages,"; font-family:Verdana' onClick=self.location.href='FYP.php'>
			<img valign='bottom' width='40' border='0' src='img/Palet_R.png'>";
		echo"<br>FYPages</div>";

	$wdt=$width-250;
	}
	echo"<div style='position:absolute; top:0px; left:$wdt; width:60px; text-align:center; font-size:70%; color:black; font-family:Verdana' onClick=self.location.href='Main.php'>
		<img valign='bottom' width='40' border='0' src='img/Home.png'>";
	echo"<br>Home</div>";
}


function selectLanguage($lang)
{	// Select language
echo "<form name='frmLang' method='get'>
      <select style='font-size:12' name='lang' onchange='submit()'>   
				<option value='' >".$lang."</option>
				<option value='DE' >--------------</option>
				<option value='BG' >Bulgarian</option>
				<option value='CS' >Czech</option>
				<option value='DA' >Dansk</option>
				<option value='DE' >Deutsch</option>
				<option value='EN' >English</option>
				<option value='ES' >Español</option>
				<option value='ET' >Estonian</option>
				<option value='FR' >Français</option>
				<option value='EL' >Greek</option>
				<option value='HU' >Hungarian</option>
				<option value='IT' >Italiano</option>
				<option value='LT' >Latvian</option>
				<option value='LV' >Lithuanian</option>
				<option value='NL' >Nederlands</option>
				<option value='NO' >Norsk</option>
				<option value='PL' >Polski</option>
				<option value='PT' >Português</option>
				<option value='RO' >Rumanian</option>
				<option value='RU' >Russian</option>
				<option value='SV' >Svenska</option>
				<option value='SK' >Slovak</option>
				<option value='SL' >Slovene</option>
				<option value='FI' >Suomeksi</option>
			</select>
		</form>";
	return $lang;
}


// restore language setting
if(isUserLoggedIn())
{
	$row = $db->sql_fetchrow($db->sql_query("SELECT Language FROM Users WHERE Username = '$loggedInUser->display_username'"));
	$lang=strtoupper($row['Language']);
	if($lang!=$_SESSION['lang'])
	{
		$lang=$_SESSION['lang'];
		$db->sql_query("UPDATE Users SET Language = '$lang' WHERE Username = '$loggedInUser->display_username'");
	}
}


function selectKbz($Sp, $bgcolor, $bordercolor, $lang, $dir ,$fontweight, $width, $user_id)
{
  global $db ;

  if($width=="") $width=60;
	$IE = strstr($_SERVER["HTTP_USER_AGENT"], "IE");
	@include("lang/DE.php");
	@include("lang/$lang.php");
	if($lang=="")$lang="DE";
	if($dir=="")$dir="rtl";
	
	if($user_id!="") $whereUserID = "manage.MUser_ID = '".$user_id."'";
	if($whereUserID!="") $where = "WHERE";

echo "	<form name='selBet' method='get'>
			<td align='right' valign='middle' bgcolor=".$bgcolor." >";
				if($Sp=="")
				{
					$result=$db->sql_query("SELECT distinct manage.Betriebsstelle, bahnhof.* 
					FROM bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
					$where $whereUserID
					ORDER BY Kurzbezeichnung");
					echo"<select dir=".$dir." style='background-color:".$bgcolor.";
					width:".$width."px;border:1px solid;border-color:".$bordercolor.";
					font-size:16px;font-weight:".$fontweight."' name=Bhf_ID onchange='submit()'>";
					echo"<option>Kbz</option>";
				}
				else
				{
					$result=$db->sql_query("SELECT distinct manage.Betriebsstelle, bahnhof.* 
					FROM bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
					WHERE Spur = '".$Sp."' 
					ORDER BY Kurzbezeichnung");
					if($IE) echo"<select dir=".$dir." style='background-color:#F78181;
					width:".$width."px;border:3px solid;border-color:#F00;font-size:16px;
					font-weight:".$fontweight."' name=Bhf_ID onchange='submit()'>";
					else echo"<select dir=".$dir." style='background-color:".$bgcolor.";
					width:".$width."px;border:3px solid;border-color:#F00;font-size:16px;
					font-weight:".$fontweight."' name=Bhf_ID onchange='submit()'>";
					echo"<option>Kbz</option>";
					echo"<option value='Sp' ></option>";
				}
				$i=0;
				while( $row=$db->sql_fetchrow($result) )
				{
					if($Sp=='')
					{
						if($user_id=='')
						{	
							if($row['Art']=="Connect") echo"<option value='$row[id]' style='color:darkgreen'>".$row['Kurzbezeichnung']." [An]</option><b>";
							elseif($row['Art']=="SBF") echo"<option value='$row[id]' style='color:darkblue'>".$row['Kurzbezeichnung']." [Sbf]</option><b>";
							elseif($row['Art']=="Block") echo"<option value='$row[id]' style='color:darkred'>".$row['Kurzbezeichnung']." [Blk]</option><b>";
							else echo"<option value='$row[id]'>".$row['Kurzbezeichnung']."</option><b>";
						}
						else
						{
							if($row['Art']=="Connect") echo"<option value='$row[id]' style='color:darkgreen'>".$row['Kurzbezeichnung']." [An]</option><b>";
							elseif($row['Art']=="SBF") echo"<option value='$row[id]' style='color:darkblue'>".$row['Kurzbezeichnung']." [Sbf]</option><b>";
							elseif($row['Art']=="Block") echo"<option value='$row[id]' style='color:darkred'>".$row['Kurzbezeichnung']." [Blk]</option><b>";
							else echo"<option value='$row[id]'>".$row['Kurzbezeichnung']."</option><b>";
						}
					}
					else
					{
						if($row['Art']=="Connect") echo"<option value='$row[id]' style='color:darkgreen'>".$row['Kurzbezeichnung']." [An]</option><b>";
						elseif($row['Art']=="SBF") echo"<option value='$row[id]' style='color:darkblue'>".$row['Kurzbezeichnung']." [Sbf]</option><b>";
						elseif($row['Art']=="Block") echo"<option value='$row[id]' style='color:darkred'>".$row['Kurzbezeichnung']." [Blk]</option><b>";
						else echo"<option value='$row[id]'>".$row['Kurzbezeichnung']."</option><b>";
					}
					$i++;
				}
				echo"</select>";
echo "		</td>
		</form>";
	return $Kurzbezeichnung;
}


function selectBetriebsstelle($Sp, $bgcolor, $bordercolor, $lang, $dir ,$fontweight, $width, $user_id)
{	// Select Betriebsstelle is connected to Spur, variable Sp used.
  global $db ;

  if($width=="") $width=220;
	$IE = strstr($_SERVER["HTTP_USER_AGENT"], "IE");
	@include("lang/DE.php");
	@include("lang/$lang.php");
	if($lang=="")$lang="DE";
	if($dir=="")$dir="rtl";
	
	if($user_id!="") $whereUserID = "manage.MUser_ID = '".$user_id."'";
	if($whereUserID!="") $where = "WHERE";

echo "	<form name='selBet' method='get'>
			<td align='right' valign='middle' bgcolor=".$bgcolor." >";
				if($Sp=="")
				{
					$result=$db->sql_query("SELECT distinct manage.Betriebsstelle, bahnhof.Haltestelle, bahnhof.Spur, bahnhof.Art, bahnhof.id 
					FROM bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
					$where $whereUserID
					ORDER BY Haltestelle");
					echo"<select dir=".$dir." style='background-color:".$bgcolor.";
					width:".$width."px;border:1px solid;border-color:".$bordercolor.";
					font-size:16px;font-weight:".$fontweight."' name=Bhf_ID onchange='submit()'>";
					echo"<option>".$TEXT['lang-station']."</option>";
				}
				else
				{
					$result=$db->sql_query("SELECT distinct manage.Betriebsstelle, bahnhof.Haltestelle, bahnhof.Spur, bahnhof.Art, bahnhof.id 
					FROM bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID
					WHERE Spur = '".$Sp."' 
					ORDER BY Haltestelle");
					if($IE) echo"<select dir=".$dir." style='background-color:#F78181;
					width:".$width."px;border:3px solid;border-color:#F00;font-size:16px;
					font-weight:".$fontweight."' name=Bhf_ID onchange='submit()'>";
					else echo"<select dir=".$dir." style='background-color:".$bgcolor.";
					width:".$width."px;border:3px solid;border-color:#F00;font-size:16px;
					font-weight:".$fontweight."' name=Bhf_ID onchange='submit()'>";
					echo"<option>".$Sp."] ".$TEXT['lang-station']."]&nbsp</option>";
					echo"<option value='Sp' >".$TEXT['lang-rstgrp']."</option>";
				}
				$i=0;
				while( $row=$db->sql_fetchrow($result) )
				{
					if($Sp=='')
					{
						if($user_id=='')
						{	
							if($row['Art']=="Connect") echo"<option value='$row[id]' style='color:darkgreen'>".$row['Haltestelle']." [An]</option><b>";
							elseif($row['Art']=="SBF") echo"<option value='$row[id]' style='color:darkblue'>".$row['Haltestelle']." [Sbf]</option><b>";
							elseif($row['Art']=="Block") echo"<option value='$row[id]' style='color:darkred'>".$row['Haltestelle']." [Blk]</option><b>";
							else echo"<option value='$row[id]'>".$row['Haltestelle']."</option><b>";
						}
						else
						{
							if($row['Art']=="Connect") echo"<option value='$row[id]' style='color:darkgreen'>".$row['Betriebsstelle']." [An]</option><b>";
							elseif($row['Art']=="SBF") echo"<option value='$row[id]' style='color:darkblue'>".$row['Betriebsstelle']." [Sbf]</option><b>";
							elseif($row['Art']=="Block") echo"<option value='$row[id]' style='color:darkred'>".$row['Betriebsstelle']." [Blk]</option><b>";
							else echo"<option value='$row[id]'>".$row['Betriebsstelle']."</option><b>";
						}
					}
					else
					{
						if($row['Art']=="Connect") echo"<option value='$row[id]' style='color:darkgreen'>".$row['Haltestelle']." [An]</option><b>";
						elseif($row['Art']=="SBF") echo"<option value='$row[id]' style='color:darkblue'>".$row['Haltestelle']." [Sbf]</option><b>";
						elseif($row['Art']=="Block") echo"<option value='$row[id]' style='color:darkred'>".$row['Haltestelle']." [Blk]</option><b>";
						else echo"<option value='$row[id]'>".$row['Haltestelle']."</option><b>";
					}
					$i++;
				}
				echo"</select>";
echo "		</td>
		</form>";
	return $Betriebsstelle;
}


function selectSpur($Spur, $Sp, $bgcolor, $bordercolor, $Bhf)
{	// Select Spur
  global $db ;

  $IE = strstr($_SERVER["HTTP_USER_AGENT"], "IE");
echo "	<form name='selSpur' method='get'>
			<td align='left' valign='middle' bgcolor=".$bgcolor." width='90px'>";
				if(substr($Spur,0,2)!=substr($Sp,0,2) and $Sp!="")
				{
					if($IE)	echo "<select style='background-color:#F78181;width:90px;border:3px solid;
							border-color:red;font-size:14px;font-weight:".$fontweight."' name='Sp' value=".$Spur." onchange='submit()'>";
					else	echo "<select style='background-color:".$bgcolor.";width:90px;border:3px solid;
							border-color:red;font-size:14px;font-weight:".$fontweight."' name='Sp' value=".$Spur." onchange='submit()'>";
				}
				elseif(sizeof($Bhf)>1)
				{
					if($IE)	echo "<select style='background-color:#D0D0FF;width:90px;border:3px solid;
							border-color:blue;font-size:14px;font-weight:".$fontweight."' name='Sp' value=".$Spur." onchange='submit()'>";
					else	echo "<select style='background-color:".$bgcolor.";width:90px;
							border:3px solid;border-color:blue;font-size:14px;font-weight:".$fontweight."' name='Sp' value=".$Spur." onchange='submit()'>";
				}
				else echo "<select style='background-color:".$bgcolor.";width:90px;border:1px solid;
							border-color:".$bordercolor.";font-size:14px;font-weight:".$fontweight."' name='Sp' value=".$Spur." onchange='submit()'>";
echo "				<option value=".$Spur.">".$Spur."</option>";
					if(sizeof($Bhf)>1)
					{
						$i=0;
						while($i < sizeof($Bhf))
						{
							echo "<option value='".$Bhf[$i]['Spur']."'>-> ".$Bhf[$i]['Spur']."</option>";
							$i++;
						}
					}
echo "					<option value='' ></option>";
						selectSpurOption();
echo "			</select>";
echo "		</td></font>
		</form>";
	return $Sp;
}


function selectSpurOption()
{
echo "	<option value='I' >I</option>
		<option value='O' >O</option>
		<option value='HO-RE' >HO-RE</option>
		<option value='HO-Fine' >HO-Fine</option>
		<option value='HO-USA' >HO-USA</option>
		<option value='HO-SWE' >HO-SWE</option>
		<option value='HOm' >HOm</option>
		<option value='HOe' >HOe</option>
		<option value='HOn3' >HOn3</option>
		<option value='TT' >TT</option>
		<option value='N-RE' >N-RE</option>
		<option value='N-Fine' >N-Fine</option>
		<option value='N-USA' >N-USA</option>
		<option value='Z' >Z</option>";
}


function ErrorMessage($Bhf_ID, $Betriebsstelle, $Spur, $loggedInUser, $lang)
{
  global $db ;

  @include("lang/DE.php");
	@include("lang/$lang.php");
	if($lang=="")$lang="DE";

	if(isUserLoggedIn())
	{
		$FreieBahnhoefe=$db->sql_fetchrow($db->sql_query("SELECT Betriebsstelle FROM manage WHERE Bhf_ID = '$Bhf_ID'"));

		if($Bhf_ID=="" and $Betriebsstelle!="")
		{
?>			<font size='4' style='color:red; font-family:Helvetica' ><b><?php echo $Betriebsstelle."</b> [".$Spur."] ".$TEXT['lang-notbhf']?></font>
<?php			if(str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1))!="Bahnhof.php")
			{ ?>
				<input type='button' style='background-color:lightgreen' value='<?php echo $TEXT['lang-station']." ".strtolower($TEXT['lang-add'])?> ?'
				onClick="self.location.href='Bahnhof.php?Betriebsstelle=<?php echo $Betriebsstelle?>&Bhf_ID=<?php echo $Bhf_ID?>&Spur=<?php echo $Spur?>'"</th>
<?php			}
			else
			{
?>				<input type='button' style='background-color:orange' value='<?php echo $TEXT['lang-station']." ".strtolower($TEXT['lang-add'])?> ?'
				onClick="self.location.href='Bahnhof.php?Neu=<?php echo $loggedInUser->display_username?>&Betriebsstelle=<?php echo $Betriebsstelle?>&Bhf_ID=<?php echo $Bhf_ID?>&Spur=<?php echo $Spur?>'"</th>
<?php
			}
		}
		elseif($FreieBahnhoefe=="" and $Betriebsstelle!="")
		{
?>			<font size='4' style='color:red; font-family:Helvetica' ><?php echo $TEXT['lang-station']?>&nbsp;<b><?php echo $Betriebsstelle."</b> [".$Spur."] ".$TEXT['lang-notmngt']?></font>
<?php		if(str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1))!="Manage.php") { ?>
				&nbsp<input type='button' style='background-color:#FFC0C0; border:1px solid; border-color:red' value='<?php echo $TEXT['lang-add']?> ?'
				onClick="self.location.href='Manage.php?Betriebsstelle=<?php echo $Betriebsstelle?>&Bhf_ID=<?php echo $Bhf_ID?>&Spur=<?php echo $Spur?>'"</th><?php } ?>
<?php	}
		elseif($Betriebsstelle!="" and str_replace('FYP/','',substr($_SERVER['SCRIPT_NAME'],1))!="Treffen.php")
		{
			$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE Bhf_ID = '$Bhf_ID'"));
?>			<font size='4' style=font-family:Helvetica><?php echo $TEXT['lang-station']?>&nbsp<b><?php echo $Betriebsstelle?></b><?php echo $TEXT['lang-mngt']?><b><?php echo $row['User']?></b></font>
<?php	}
	}
}
