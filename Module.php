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

	$_SESSION['Spur'] = "HO-RE";

	if(@$_REQUEST['action']=="edit")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT module.*, bahnhof.Haltestelle, bahnhof.Kurzbezeichnung, mngmod.* FROM module
													LEFT JOIN bahnhof ON module.Bhf_ID = bahnhof.id
													LEFT JOIN mngmod ON module.id_module = mngmod.Md_ID
													WHERE id_module=".round($_REQUEST['Md_ID'])));
		$_SESSION['Name'] = trim($row['Name']);
		$_SESSION['NR'] = trim($row['NR']);
		$_SESSION['Rem'] = trim($row['Rem']);
		$_SESSION['Spur'] = trim($row['Spur']);
		$_SESSION['Radius'] = trim($row['Radius']);
		$_SESSION['Winkel'] = trim($row['Winkel']);
		$_SESSION['Laenge'] = trim($row['Laenge']);
		$_SESSION['Breite'] = trim($row['Breite']);
		$_SESSION['Endprofil_1'] = trim($row['Endprofil_1']);
		$_SESSION['Endprofil_2'] = trim($row['Endprofil_2']);
		$_SESSION['Endprofil_3'] = trim($row['Endprofil_3']);
		$_SESSION['Signalschacht'] = trim($row['Signalschacht']);
		$_SESSION['Status'] = trim($row['Status']);
		$_SESSION['Besitzer'] = trim($row['Besitzer']);
		$_SESSION['Email'] = trim($row['Email']);
		$_SESSION['Bemerkung'] = trim($row['Bemerkung']);
		$_SESSION['Zeichnung'] = trim($row['Zeichnung']);
		$_SESSION['MD_ID'] = trim($row['id_module']);
		$_SESSION['Bhf_ID'] = trim($row['Bhf_ID']);
		$Bhf_ID=$_SESSION['Bhf_ID'];
		$_SESSION['Betriebsstelle'] = trim($row['Haltestelle']);
		$Betriebsstelle = trim($row['Haltestelle']);
		$Kurzbezeichnung = trim($row['Kurzbezeichnung']);
		$User = trim($row['User']);
		$ModRem = trim($row['ModRem']);
	}
	elseif(isset($_REQUEST['Bhf_ID']))
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT bahnhof.id, bahnhof.Haltestelle, bahnhof.Kurzbezeichnung FROM bahnhof WHERE bahnhof.id = '".$_REQUEST['Bhf_ID']."'"));
		$Betriebsstelle = $_SESSION['Betriebsstelle'];
		$Kurzbezeichnung=$row['Kurzbezeichnung'];
		$Bhf_ID = $_REQUEST['Bhf_ID'];
	}
	elseif($_REQUEST['Betriebsstelle']!="")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT bahnhof.id, bahnhof.Haltestelle, bahnhof.Kurzbezeichnung FROM bahnhof WHERE bahnhof.Haltestelle = '".$Betriebsstelle."' and bahnhof.Spur = '".$Spur."'"));
		$Bhf_ID=$row['id'];
		$Betriebsstelle=$row['Haltestelle'];
		$Kurzbezeichnung=$row['Kurzbezeichnung'];
	}
	elseif($_REQUEST['Kurzbezeichnung']!="")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT bahnhof.id, bahnhof.Haltestelle, bahnhof.Kurzbezeichnung FROM bahnhof WHERE bahnhof.Kurzbezeichnung = '".$Kurzbezeichnung."' and bahnhof.Spur = '".$Spur."'"));
		$Bhf_ID=$row['id'];
		$Betriebsstelle=$row['Haltestelle'];
		$Kurzbezeichnung=$row['Kurzbezeichnung'];
	}
	elseif($_REQUEST['Betriebsstelle']=="" and $_REQUEST['Kurzbezeichnung']=="")
	{
		$Bhf_ID=0;
	}


	$Email = $loggedInUser->email;

	$Name = getVariableFromQueryStringOrSession('Name');
	$NR = getVariableFromQueryStringOrSession('NR');
	$Rem = getVariableFromQueryStringOrSession('Rem');
	$Spur = getVariableFromQueryStringOrSession('Spur');
	$Radius = getVariableFromQueryStringOrSession('Radius');
	$Winkel = getVariableFromQueryStringOrSession('Winkel');
	$Laenge = getVariableFromQueryStringOrSession('Laenge');
	$Breite = getVariableFromQueryStringOrSession('Breite');
	$Endprofil_1 = getVariableFromQueryStringOrSession('Endprofil_1');
	$Endprofil_2 = getVariableFromQueryStringOrSession('Endprofil_2');
	$Endprofil_3 = getVariableFromQueryStringOrSession('Endprofil_3');
	$Signalschacht = getVariableFromQueryStringOrSession('Signalschacht');
	$Status = getVariableFromQueryStringOrSession('Status');
	$Besitzer = getVariableFromQueryStringOrSession('Besitzer');
	$Email = getVariableFromQueryStringOrSession('Email');
	$Bemerkung = getVariableFromQueryStringOrSession('Bemerkung');
	$Zeichnung = getVariableFromQueryStringOrSession('Zeichnung');
	$Md_ID = getVariableFromQueryStringOrSession('Md_ID');
	$Betriebsstelle = getVariableFromQueryStringOrSession('Betriebsstelle');

	$Signalschacht = getVariableFromQueryStringOrSession('Signalschacht');
	$Pictures = getVariableFromQueryStringOrSession('Pictures');

	if($Laenge=="")$Laenge=100;
	if($Breite=="")$Breite=40;
	if($Radius=="")$Radius=0;
	if($Winkel=="")$Winkel=0;
	if($Signalschacht=="") $Signalschacht = 0;
	if($Pictures=="") $Pictures = 1;
	if($Modul=="")$Modul=$TEXT['lang-name'];
	if($SortMD=="")$SortMD="Name";

	if(isUserLoggedIn() and $Md_ID!="")
	{	$result = $db->sql_query("SELECT * FROM mngmod WHERE Md_ID = '$Md_ID'");
		while($row=$db->sql_fetchrow($result))
		{
			if($row['ModRem']=='') $User = trim($row['User']);
			if($row['MUser_ID']==$loggedInUser->user_id)
			{
				$SaveModule = "Yes";
				$ModRem = trim($row['ModRem']);
			}
		}
	}

	if(isset($_REQUEST['Change'])) //and $loggedInUser->isGroupMember(1))
	{	$result=$db->sql_fetchrow($db->sql_query("SELECT module.* FROM module WHERE module.Name = '".$Name."' and module.NR = '".$NR."' and module.Spur = '".$Spur."'"));
		if($result['id_module']==$Md_ID or $result==""){
			if($Email=="") $Email = $loggedInUser->email;
			$db->sql_query("UPDATE module LEFT JOIN bahnhof ON module.Bhf_ID = bahnhof.id SET
			module.Name='$Name', module.NR='$NR', module.Rem='$Rem',
			module.Spur='$Spur', module.Radius='$Radius', module.Winkel='$Winkel',
			module.Laenge='$Laenge', module.Breite='$Breite',
			module.Endprofil_1='$Endprofil_1', module.Endprofil_2='$Endprofil_2',
			module.Endprofil_3='$Endprofil_3', module.Signalschacht='$Signalschacht',
			module.Status='$Status', module.Besitzer='$Besitzer', module.Email='$Email',
			module.Bemerkung='$Bemerkung', module.Zeichnung='$Zeichnung', module.Bhf_ID='$Bhf_ID'
			WHERE module.id_module = ".$Md_ID);
	echo sql_error();
		if(sql_error()!="") @$_REQUEST['Add']="";
	}
	else {echo "<div style='position:absolute; top:78px; left:10px; font-size:120%; color:red; font-family:Verdana'><td>".$TEXT['lang-modulexists']."</td></div>";}
	}

	if(isset($_REQUEST['Add']))
	{	$result=$db->sql_fetchrow($db->sql_query("SELECT module.* FROM module WHERE module.Name = '".$Name."' and module.NR = '".$NR."' and module.Spur = '".$Spur."'"));
		if($result==""){
			if($Email=="") $Email = $loggedInUser->email;
			$db->sql_query("INSERT INTO module (Name, NR, Rem, Spur, Radius, Winkel, Laenge, Breite,
				Endprofil_1, Endprofil_2, Endprofil_3, Signalschacht, Zeichnung, Besitzer, Email, Status, Bemerkung, Bhf_ID)
				VALUES('$Name', '$NR', '$Rem', '$Spur', '$Radius', '$Winkel', '$Laenge', '$Breite',
				'$Endprofil_1', '$FEndprofil_2', '$FEndprofil_3', '$Signalschacht', '$Zeichnung', '$Besitzer', '$Email', '$Status', '$Bemerkung', '$Bhf_ID');");
			$_SESSION['Md_ID'] = $db->sql_nextid();
			$Md_ID = $_SESSION['Md_ID'];
	echo sql_error();
			$db->sql_query("INSERT INTO mngmod (User, MUser_ID, Md_ID)
				VALUES('$loggedInUser->display_username', '$loggedInUser->user_id', '$_SESSION[Md_ID]');");
	echo sql_error();
	}
	else {echo "<div style='position:absolute; top:78px; left:10px; font-size:120%; color:red; font-family:Verdana'><td>".$TEXT['lang-modulexists']."</td></div>";}
	}

	if(isset($_REQUEST['AddModule']))
	{
		$db->sql_query("INSERT INTO mngmod (User, MUser_ID, Md_ID, ModRem)
		VALUES('$loggedInUser->display_username', '$loggedInUser->user_id', '$_SESSION[Md_ID]', '');");
	echo sql_error();
		$SaveModule = "Yes";
	}

	if(@$_REQUEST['action']=="delete")
	{
		$db->sql_query("DELETE FROM mngmod WHERE Md_ID=".round($_REQUEST['Md_ID']));
	echo sql_error();
		$db->sql_query("DELETE FROM module WHERE id_module=".round($_REQUEST['Md_ID']));
	echo sql_error();
		$_SESSION['Md_ID']="";
	}

	unset($_SESSION['All']);
	unset($All);

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<title>Yellow Pages</title>
</head>
<meta http-equiv="cache-control" content="no-cache">
<body bgcolor="#F0E0B0">

<?php selectBack('970',$lang)?>

<div style='position:absolute; top:10px; left:160'>
	<?php selectLanguage($languages[$lang])?>
</div>

<table style="table-layout:fixed" width=1020px border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td width="290px"><font size="5"><b>Yellow Pages</font></b></td>
	<tr>
	</tr>
		<td><?php echo $TEXT['lang-subhead']?><br></td>
	</tr>
	<tr>
		<td><font size="4"><?php if(isUserLoggedIn()) echo $TEXT['lang-welcome'] .'<b>'.$loggedInUser->display_username;?></b></font></td>
	</tr>
</table>


<div style='position:absolute; top:30px; left:10px; width:1000px; text-align:center; font-size:200%; color:red; font-family:Verdana'>
	<td>Under Construction</td>
</div>

<noscript>
	<br><font size='5' color='red'><b>Javascript is disabled,<br> please activate Javascript</b></font>
</noscript>

<table width='1014px' border=0 cellpadding=0 cellspacing=0>
<tr>
	<td colspan='5' height='30px' valign='bottom'>

	<?php if($SaveModule=="Yes" and $ModRem=="" and !isset($_REQUEST['Add'])) echo "<font size='4' style='font-family:Helvetica' >".$TEXT['lang-mmngt'];

		elseif($User=="" and $Name.$NR!="" and !isset($_REQUEST['Add'])) {
			echo "<font size='4' style='color:red; font-family:Helvetica' >".$TEXT['lang-modul']." <b>".$Name.$NR."</b> [".$Spur."] ".$TEXT['lang-notmngt'];?>
			&nbsp<input type='button' style='background-color:#FFC0C0; border:1px solid; border-color:red' value='<?php echo $TEXT['lang-add']?> ?'
			onClick="self.location.href='Module.php?AddModule&Md_ID=<?php echo $Md_ID?>'"</th><?php }

		elseif($User.$Name.$NR!="" and !isset($_REQUEST['Add'])) echo "<font size='4' style='font-family:Helvetica' >"
		.$TEXT['lang-modul']." <b>".$Name.$NR."</b> [".$Spur."] ".$TEXT['lang-mngt']." ".$User;
	?>

	</td>
	<td align='right'>

	</td>
</tr>
</table>

<table width='1015px' border=0 cellpadding=0 cellspacing=0>
<tr>
	<td bgcolor='#D0C090' width='427px'><div align='right' ><font size='5' ><?php echo $TEXT['lang-headmodule']?>&nbsp;&nbsp;&nbsp;</div></font></td>
</tr>
</table>

<table style="float:left; width:170px" border=0 cellpadding=0 cellspacing=0>
	<form action=Module.php method=get>
		<tr style='height:26px'>
			<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-name']?>&nbsp/&nbsp<?php echo $TEXT['lang-NR']?>&nbsp</td>
		</tr>
		<tr style='height:26px'>
			<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-beschreibung']?>&nbsp</td>
		</tr>
		<tr style='height:26px'>
			<?php selectBetriebsstelle('', '#F0E0B0', 'brown', $lang, 'rtl', '', '170')?>
		</tr>
		<tr style='height:26px'>
			<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-rem']?>&nbsp</td>
		</tr>
	</form>
</table>

<table align="left" style="float:left" border=0 cellpadding=0 cellspacing=0>
<form action=Module.php method=get>
	<tr style='height:26px'>
		<td><input type=text style='width:140px;text-align:right;font-size:14px
			<?php if($SaveModule != "Yes"){echo ";background-color:lightgrey";}?>' name=Name value='<?php echo $Name?>'></td>
		<td><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule != "Yes"){echo ";background-color:lightgrey";}?>' name=NR value='<?php echo $NR?>'></td>
		<td align='right' style='font-family:Verdana;width:160px'><?php echo $TEXT['lang-tl']?>&nbsp</td>
		<td><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Laenge value='<?php echo $Laenge?>'></td>
		<td align='right' style='font-family:Verdana;width:145px'><?php echo $TEXT['lang-group']?>&nbsp</td><td align='left'><b>
			<select name=Spur style='width:80px;font-size:14px<?php if($SaveModule!="Yes")
			echo ";background-color:lightgrey"; else echo ";background-color:Snow"?>' value='<?php echo $Spur?>'>
				<option value=<?php echo $Spur?>><?php echo $Spur?></option><?php selectSpurOption()?>
			</select>
		</td>
	</tr>

	<tr style='height:26px'>
		<td colspan=2 ><input type=text style='width:200px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Rem value='<?php echo $Rem?>'></td>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-breite']?>&nbsp</td>
		<td><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Breite value='<?php echo $Breite?>'></td>
		<td align='right'>
			<?php $Zeichnung_DWG = rawurlencode(pathinfo($Zeichnung, PATHINFO_FILENAME).".dwg"); ?>
			<input type='button' id='Zeichnung_DWG' style="background-color:lightyellow;border:1px solid;border-color:'#DDDD4D';font-size:14px"
			value="<?php echo $TEXT['lang-drawing']?>" onClick=self.location.href="Module/<?php echo $Spur?>/<?php echo $Zeichnung_DWG?>">
		<td align='left'>
			<input type='text' style="width:100px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>" maxlength='50' name='Zeichnung' id='Zeichnung' value='<?php echo $Zeichnung?>'>

			<?php $Zeichnung_PDF = rawurlencode(pathinfo($Zeichnung, PATHINFO_FILENAME).".pdf"); ?>
			<input type='button' id='Zeichnung_PDF' style="background-color:lightyellow;border:1px solid;border-color:'#DDDD4D';font-size:14px"
			value="PDF" onClick=self.location.href="Module/<?php echo $Spur?>/<?php echo $Zeichnung_PDF?>">
		</td>
	</tr>

	<tr style='height:26px'>
		<td align='left' colspan='1' valign='middle'>
			<input style='width:140px;font-size:14px<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>'
			type=text maxlength=50 name=Betriebsstelle value='<?php echo $Betriebsstelle?>'>
		</td>
		<td align='left' valign='middle'>
			<input style='width:60px;font-size:14px<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>'
			type=text maxlength=10 name=Kurzbezeichnung value='<?php echo $Kurzbezeichnung?>'>
		</td>
		<td align='right' style='font-family:Verdana;border-top:1px solid gray'><?php echo $TEXT['lang-angle']?>&nbsp</td>
		<td style='border-top:1px solid gray'><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Winkel value='<?php echo $Winkel?>'></td>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-owner']?>&nbsp</td>
		<td colspan=2 ><input type=text style='width:150px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Besitzer value='<?php echo $Besitzer?>'></td>
	</tr>

	<tr style='height:26px'>
		<td colspan=2 ><input type=text style='width:200px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Bemerkung value='<?php echo $Bemerkung?>'></td>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-radius']?>&nbsp</td>
		<td><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Radius value='<?php echo $Radius?>'></td>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-email']?>&nbsp</td>
		<td colspan=2 ><input type=text style='width:150px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' maxlength='50' name=Email value='<?php echo $Email?>'></td>
	</tr>

	<tr style='height:26px'>
		<td>
<?php
//echo "Bhf: ".$_SESSION['Bhf_ID']." ".$Bhf_ID;
?>
		</td>
		<td></td>
		<td rowspan=3 align='center' style='font-family:Verdana;border-top:1px solid gray; border-bottom:1px solid gray'><?php echo $TEXT['lang-profile']?></td>
		<td align='left' style='border-top:1px solid gray'><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Endprofil_1 value='<?php echo $Endprofil_1?>'></td>
		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-status']?>&nbsp</td>
		<td>
			<select name=Status style='width:150px;font-size:14px<?php if($SaveModule!="Yes")
			echo ";background-color:lightgrey"; else echo ";background-color:Snow"?>' value='<?php echo $Status?>'>
				<option value=<?php echo $Status?>><?php echo $TEXT[$Status]?></option>
				<option ></option>
				<option value='lang-ready_for_use' ><?php echo $TEXT['lang-ready_for_use']?></option>
				<option value='lang-in_shell' ><?php echo $TEXT['lang-in_shell']?></option>
				<option value='lang-remeasuring' ><?php echo $TEXT['lang-remeasuring']?></option>
				<option value='lang-missing' ><?php echo $TEXT['lang-missing']?></option>
			</select>
		</td>
	</tr>

	<tr style='height:26px'>
		<td rowspan=2 colspan=2 >
<?php 	if($SaveModule=="Yes") { ?>
		 	<button type="submit" name="Change" style='width:86px' >
				<img height='24px' border='0' src='img/ok.png' alt=<?php echo $TEXT['lang-save']?>><br>
				<b><?php echo $TEXT['lang-save']?></b>
			</button><?php }
	else echo "<img src=img/blank.gif width=86px height=1px>";?>
			<button type="submit" name="Add" style='width:86px' >
				<img height='24px' border='0' src='img/addblue.png' alt=<?php echo $TEXT['lang-add']?>><br>
				<b><?php echo $TEXT['lang-add']?></b>
			</button>

		</td>
		<td align='left' ><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Endprofil_2 value='<?php echo $Endprofil_2?>'></td>

		<td align='right' style='font-family:Verdana'><?php echo $TEXT['lang-signalschacht']?>&nbsp</td>
		<td colspan=2 >
			<input type=button style="background-color:<?php if($SaveModule!="Yes") echo 'lightgrey'; else echo 'Snow'?>
			;border:1px solid;border-color:'#DDDD4D';font-size:14px"
			value=<?php if($Signalschacht==1) echo $TEXT['lang-yes']." onClick=self.location.href='Module.php?Signalschacht=0'>";
				  else echo $TEXT['lang-no']." onClick=self.location.href='Module.php?Signalschacht=1'>"?>
		</td>
	</tr>

	<tr style='height:26px'>
		<td align='left' style='border-bottom:1px solid gray'><input type=text style='width:60px;font-size:14px
			<?php if($SaveModule!="Yes"){echo ";background-color:lightgrey";}?>' name=Endprofil_3 value='<?php echo $Endprofil_3?>'></td>
		<td>&nbsp</td>
	</tr>
</form>
</table>


<div style='float:left; text-align:center; color:gray; font-family:Verdana; width:130px'>
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

if($SaveModule=="Yes")
{?>		<a style="font-family:Arial"><?php echo $TEXT['lang-fileupload_format']." ".implode(", ", array_unique(array_keys($mimetypen)))?></a><br>
		<div style="height:23px; overflow:hidden;">
			<input type=button style="background-color:lightgrey; font-size:10; width:130px; height:23px" value="<?php echo $TEXT['lang-file']?>">
<form method="POST" enctype="multipart/form-data">
	<input type="file" name="datei" size="1" style="width:130px; height:25px; opacity:0; filter:alpha(opacity:0); position:relative; top:<?php if(isIE()!="") echo"-25px"; else echo"-25px";?>;" />
		</div>
		<input type="submit" style="background-color:lightgrey; font-size:10; width:130px; height:23px" value="<?php echo $TEXT['lang-upload_dwg']?>"><br>
		<a style="font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold" >
<?php if ($Betriebsstelle!="" and is_array($_FILES["datei"]))
	{	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{	if (is_writeable($verzeichnis))
			{	if ($_FILES["datei"]["name"] != "" && $_FILES["datei"]["error"] == 0)
				{	if ($_FILES["datei"]["size"] <= $maxgroesse)
					{	$array = explode(".", basename($_FILES["datei"]["name"]));
						$dateiendung = strtolower(end($array));
						if (in_array($dateiendung, array_keys($mimetypen)))
						{	if (in_array($_FILES["datei"]["type"], $mimetypen))
							{	if (move_uploaded_file($_FILES["datei"]["tmp_name"], $verzeichnis."M_".$Name.$NR."_".$Spur.substr($_FILES["datei"]["name"],-4)))
								{	chmod($verzeichnis."M_".$Name.$NR."_".$Spur.substr($_FILES["datei"]["name"],-4), 0666);
									copy($verzeichnis.$_FILES["datei"]["name"], $backup."M_".$Name.$NR."_".$Spur.substr($_FILES["datei"]["name"],-4));
									chmod($backup."M_".$Name.$NR."_".$Spur.substr($_FILES["datei"]["name"],-4), 0666);
									$Zeichnung="M_".$Name.$NR."_".$Spur;
									$db->sql_query("UPDATE module LEFT JOIN mngmod ON module.id_module = mngmod.Md_ID
									SET module.Zeichnung = '$Zeichnung'
									WHERE mngmod.MUser_ID = '$loggedInUser->user_id' and module.id_module = '$Md_ID'");
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
			else {echo $TEXT['lang-fileupload_subdir'].$verzeichnis.$TEXT['lang-fileupload_norights'];}}}
echo "</a></form>";}

else echo "<div style='height:64px'></div>";

if($_SESSION["sso"]==0 and !is_array($_FILES["datei"])) { ?>
	<br>
	<img src=img/blank.gif ><img src='img/Manage.png' height='64px'
	onClick="self.location.href='Manage.php?Md_ID=<?php echo $Md_ID?>&lang=<?php echo $lang?>'"><br>
	<img src=img/blank.gif height=1px><?php echo $TEXT['lang-bvw']?>
<?php } ?>

</div>

<br clear=all>

<?php
$verzeichnis = "Bilder/";
$backup = "SQL/backup/Bilder/";
$maxgroesse = 20480000; // 1024 Bytes = 1 KB

// Unter http://de.selfhtml.org/diverses/mimetypen.htm ist eine Liste der Mimetypen zu Finden.
$mimetypen = array(
 "png" => "image/png",
 "jpg" => "image/jpeg",
 "jpg" => "image/pjpeg",
 "jpeg" => "image/jpeg",
 "gif" => "image/gif",);

//if($SaveModule=="Yes" and $Md_ID!="")
{?>
	<table style="float:left; width:978px" border='0' cellpadding='0' cellspacing='0' >
	<td bgcolor="gainsboro" style="font-family:Arial; font-size:16px" >
		<div style="height:23px; overflow:hidden;">
			<input type=button style="background-color:lightgrey; font-size:12; width:150px; height:23px" value="<?php echo $TEXT['lang-file']?>">
			<?php echo $TEXT['lang-fileupload_format']." ".implode(", ", array_unique(array_keys($mimetypen))).
			" - ".$TEXT['lang-fileupload_maxsize']." ".(number_format(($maxgroesse / 1024000), 0, ",", "."))?> MB
<form method="POST" enctype="multipart/form-data">
	<input type="file" name="bild" size="1" style="width:150px; height:25px; opacity:0; filter:alpha(opacity:0);  position:relative; top:<?php if(isIE()!="") echo"-25px"; else echo"-25px";?>;" />
		</div>
		<input type="submit" style="background-color:lightgrey; font-size:12; width:150px; height:23px" value="<?php echo $TEXT['lang-upload_pic']?>">
<?php if($Md_ID!="" and is_array($_FILES["bild"]))
	{	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{	if (is_writeable($verzeichnis))
			{	if ($_FILES["bild"]["name"] != "" && $_FILES["bild"]["error"] == 0)
				{	if ($_FILES["bild"]["size"] <= $maxgroesse)
					{	$array = explode(".", basename($_FILES["bild"]["name"]));
						$dateiendung = strtolower(end($array));
						if (in_array($dateiendung, array_keys($mimetypen)))
						{	if (in_array($_FILES["bild"]["type"], $mimetypen))
							{	if (move_uploaded_file($_FILES["bild"]["tmp_name"], $verzeichnis."pic_Mod_ID_".$Md_ID ))
								{	chmod($verzeichnis."pic_Mod_ID_".$Md_ID, 0666);
									copy($verzeichnis."pic_Mod_ID_".$Md_ID, $backup."pic_Mod_ID_".$Md_ID);
									chmod($backup."pic_Mod_ID_".$Md_ID, 0666);
									echo '<font size="4">'.$TEXT["lang-fileupload_executed"].'<b>'
									.$_FILES["bild"]["name"]. '</b> - ' . number_format(($_FILES["bild"]["size"] / 1024), 2, ",", ".") . ' KB</font>';}
								else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_error"].'</font>';}}
							else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_wrongmime"].$_FILES["bild"]["type"].'</font>';}}
						else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_dataformat"]."<b>".$dateiendung."</b>".$TEXT["lang-fileupload_notallowed"];}}
					else {echo '<font size="4" color="red">'.$TEXT["lang-fileupload_file"].
						number_format(($_FILES["bild"]["size"] / 1024), 2, ",", ".").$TEXT["lang-fileupload_toobig"].'</font>';}}
				else {echo '<font size="4">'.$TEXT["lang-fileupload_select"];}}
			else {echo $TEXT['lang-fileupload_subdir'].$verzeichnis.$TEXT['lang-fileupload_norights'];}}}}?>
	</td></table>
</form>

<input type="image" height="46px" style="background-color:<?php if($Pictures=="1") echo "lightgrey"; else echo "orange";?>" src="img/Pictures.png"
<?php if($Pictures=="1") echo "onClick=self.location.href='Module.php?Pictures=0'>"; else echo "onClick=self.location.href='Module.php?Pictures=1'>";?>

<?php if(file_exists('Bilder/pic_Mod_ID_'.$Md_ID) and $Pictures=="1") echo "<img src='Bilder/pic_Mod_ID_".$Md_ID."' width='1024px' >"?>

<br clear=all>

<?php include("lib/modul.list.php")?>

</body>
</html>
