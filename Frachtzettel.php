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
	$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM frachtzettel WHERE FUser_ID = '$loggedInUser->user_id' and id=".round($_REQUEST['Fz_ID'])));
	$Eilgut = $row['Eilgut'];
	$_SESSION['Eilgut'] = trim($row['Eilgut']);
	$_SESSION['Wenden'] = trim($row['Wenden']);
	$_SESSION['Mehrfach'] = trim($row['Mehrfach']);
	$_SESSION['Stueckgut'] = trim($row['Stueckgut']);
	$_SESSION['Ecol'] = $row['Ecol'];
	$_SESSION['Vcol'] = $row['Vcol'];
	$_SESSION['Lcol'] = $row['Lcol'];
	$_SESSION['Zielbahnhof'] = trim($row['Zielbahnhof']);
	$_SESSION['Empfaenger'] = trim($row['Empfaenger']);
	$_SESSION['Gewicht'] = trim($row['Gewicht']);
	$_SESSION['Wagengattung'] = trim($row['Wagengattung']);
	$_SESSION['Freight'] = trim($row['Freight']);
	$_SESSION['Ladung'] = trim($row['Ladung']);
	$_SESSION['Versandbahnhof'] = trim($row['Versandbahnhof']);
	$_SESSION['Versender'] = trim($row['Versender']);
	$_SESSION['LadeEmpfang'] = trim($row['LadeEmpfang']);
	$_SESSION['Fz_ID'] = $row['id'];

	$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE MUser_ID = '$loggedInUser->user_id' and Betriebsstelle='$_SESSION[Zielbahnhof]'"));
	$_SESSION['ZBhf_ID'] = $row['Bhf_ID'];
	$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle='$_SESSION[Versandbahnhof]'"));
	$_SESSION['VBhf_ID'] = $row['id'];
}


if(isset($_REQUEST['Eil']))
{
	if($_SESSION['Eilgut']=="checked") $_SESSION['Eilgut'] = "";
	else $_SESSION['Eilgut'] = "checked";
}

if(isset($_REQUEST['Wend']))
{
	if($_SESSION['Wenden']=="checked") $_SESSION['Wenden'] = "";
	else $_SESSION['Wenden'] = "checked";
}

if(isset($_REQUEST['Mehr']))
{
	if($_SESSION['Mehrfach']=="checked") $_SESSION['Mehrfach'] = "";
	else $_SESSION['Mehrfach'] = "checked";
}

if(isset($_REQUEST['Stck']))
{
	if($_SESSION['Stueckgut']=="checked") $_SESSION['Stueckgut'] = "";
	else $_SESSION['Stueckgut'] = "checked";
}

$Search = getVariableFromQueryStringOrSession('Search');
$All = getVariableFromQueryStringOrSession('All');
$NHMS2 = getVariableFromQueryStringOrSession('NHMS2');
$NHMS1 = getVariableFromQueryStringOrSession('NHMS1');
if(isset($_REQUEST['NHMS1'])) $NHMS2 = "";

$Treffen = getVariableFromQueryStringOrSession('Treffen');
if(isset($_REQUEST['Treffen']) or $Search!="")
{
	$NHMS1 = "";
	$NHMS2 = "";
}
if(isset($_REQUEST['Treffen']))
{
	$Search = "";
	$_SESSION['Search'] = "";
}

if($_SESSION['Treffen']=="") // if nothing selected the last edited meeting will be selected.
{
	$row=$db->sql_fetchrow($db->sql_query("SELECT Treffen, Timestamp FROM treffen ORDER BY timestamp DESC LIMIT 1"));
	$_SESSION['Treffen']=$row['Treffen'];
	$Treffen=$row['Treffen'];
}

$Menge = intval(getVariableFromQueryStringOrSession('Menge'));

$Gewicht = getVariableFromQueryStringOrSession('Gewicht');
$Gewicht = intval($Gewicht);

$ZBhf_ID = getVariableFromQueryStringOrSession('ZBhf_ID');
if(isset($_REQUEST['ZBhf_ID']))
{
	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$ZBhf_ID'"));
	$_SESSION['Zielbahnhof'] = trim($row['Haltestelle']);
	$_SESSION['Spur'] = $row['Spur'];
	$_SESSION['Betriebsstelle'] = trim($row['Haltestelle']);
	$_SESSION['Bhf_ID'] = $row['id'];
	$_SESSION['Empfaenger'] = "";
	$_SESSION['LadeEmpfang'] = "";
	$_SESSION['Lng'] = $row['Language'];
}
$Zielbahnhof = trim(getVariableFromQueryStringOrSession('Zielbahnhof'));

$Empfaenger = trim(getVariableFromQueryStringOrSession('Empfaenger'));
if(isset($_REQUEST['Empfaenger']))
{	
	if(strpos($Empfaenger,'__')==0)
	{
		$row = $db->sql_fetchrow($db->sql_query("SELECT gleise.LadeFarbe, gleise.Ladestelle, gleise.Gleisname, fyp.Anschliesser FROM fyp
		LEFT JOIN gleise ON gleise.id = fyp.Gleis_ID WHERE fyp.Bhf_ID = '$ZBhf_ID' AND fyp.Anschliesser = '$Empfaenger'"));
		$Empfaenger = $_SESSION['Empfaenger'] = trim($row['Anschliesser']);
	}
	else
	{
		$An_Em = explode("__",$Empfaenger); // Anschliesser__Empfaenger
		$Empfaenger = $_SESSION['Empfaenger'] = trim($An_Em[0]." - ".$An_Em[1]);
		$row = $db->sql_fetchrow($db->sql_query("
		SELECT treffen.Anschliesser, fyp.Anschliesser, gleise.LadeFarbe, gleise.Ladestelle, gleise.Gleisname FROM treffen 
		JOIN fyp ON treffen.An_ID = fyp.Bhf_ID JOIN gleise ON fyp.Gleis_ID = gleise.id 
		WHERE treffen.Treffen = '".escape($Treffen)."' AND treffen.Anschliesser = '$An_Em[0] [$An_Em[2]]' AND fyp.Anschliesser = '$An_Em[1]'"));
	}

	$_SESSION['LadeEmpfang'] = trim($row['Ladestelle'])." (".$TEXT['lang-track']." ".$row['Gleisname'].")";
	$_SESSION['Lcol'] = $row['LadeFarbe'];
}

$FYP_ID = getVariableFromQueryStringOrSession('FYP_ID');
if(isset($_REQUEST['FYP_ID']))
{
	if($Treffen!="")
	{
		$row = $db->sql_fetchboth($db->sql_query("SELECT fyp.*, treffen.* FROM fyp
		INNER JOIN treffen ON (treffen.Bhf_ID = fyp.Bhf_ID OR treffen.An_ID = fyp.Bhf_ID)
		WHERE fyp.id_fyp = '$FYP_ID' and treffen.Treffen = '$Treffen'"));
		$_SESSION['Versandbahnhof'] = trim($row[15]);
		$_SESSION['Versender'] = trim($row[4]);
	}
	else
	{
		$row = $db->sql_fetchrow($db->sql_query("SELECT fyp.* FROM fyp
		WHERE fyp.id_fyp = '$FYP_ID'"));
		$_SESSION['Versandbahnhof'] = trim($row['Betriebsstelle']);
		$_SESSION['Versender'] = trim($row['Anschliesser']);
	}
	$_SESSION['Freight'] = trim($row['Product_Description']);
	$_SESSION['Ladung'] = trim($row['Produktbeschreibung']);
	$_SESSION['Wagengattung'] = trim($row['Wagengattung']);
	$_SESSION['VBhf_ID'] = $row['Bhf_ID'];
//	$_SESSION['VBhf_ID'] = "";
//	$_SESSION['Versandbahnhof'] = $row['Betriebsstelle'];
//	$_SESSION['Versandbahnhof'] = $row[15];
	$_SESSION['Betriebsstelle'] = trim($row['Betriebsstelle']);
	$_SESSION['Bhf_ID'] = $row['Bhf_ID'];
//	$_SESSION['Versender'] = $row['Anschliesser'];
//	$_SESSION['Versender'] = $row[4];
	if( $_SESSION['Versender'] == "Stückgutzentrum" )
	{
		$_SESSION['Vcol'] = 'orange';
		$_SESSION['Stueckgut'] = "checked";
	}
}

$Eilgut = getVariableFromQueryStringOrSession('Eilgut');
$Wenden = getVariableFromQueryStringOrSession('Wenden');
$Mehrfach = getVariableFromQueryStringOrSession('Mehrfach');
$Stueckgut = getVariableFromQueryStringOrSession('Stueckgut');

$VBhf_ID = getVariableFromQueryStringOrSession('VBhf_ID');
if(isset($_REQUEST['VBhf_ID']))
{
	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$VBhf_ID'"));
	$_SESSION['Versandbahnhof'] = trim($row['Haltestelle']);
	$SpurVBhf = $row['Spur'];
	$_SESSION['Betriebsstelle'] = trim($row['Haltestelle']);
	$_SESSION['Bhf_ID'] = $row['id'];
	if($_SESSION['VBhf_ID']!="")
	{
		$_SESSION['Freight'] = "";
		$_SESSION['Ladung'] = "";
		$_SESSION['Versender'] = "";
	}
	else unset($_REQUEST['VBhf_ID']);
}

$Versandbahnhof = trim(getVariableFromQueryStringOrSession('Versandbahnhof'));
$Versender = trim(getVariableFromQueryStringOrSession('Versender'));
$Freight = trim(getVariableFromQueryStringOrSession('Freight'));
$Ladung = trim(getVariableFromQueryStringOrSession('Ladung'));
$Wagengattung = trim(ucwords(getVariableFromQueryStringOrSession('Wagengattung')));
$User = $loggedInUser->display_username;


if(strpos($Versender,'__')!=0)
{
	$Ve_La = explode("__",$Versender); // Versender__Ladestelle
	$Versender = $_SESSION['Versender'] = trim($Ve_La[0]." - ".$Ve_La[1]);
}


$TrackID = getVariableFromQueryStringOrSession('TrackID');
if(isset($_REQUEST['TrackID']))
{
	$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM gleise WHERE id = '$TrackID' ORDER BY Ladestelle, Gleisname"));
	$_SESSION['LadeEmpfang'] = trim($row['Ladestelle'])." (".$TEXT['lang-track']." ".$row['Gleisname'].")";
	$_SESSION['Lcol'] = $row['LadeFarbe'];
}

$Ecol = getVariableFromQueryStringOrSession('Ecol');
$Vcol = getVariableFromQueryStringOrSession('Vcol');
$Lcol = getVariableFromQueryStringOrSession('Lcol');
if($Ecol=="") $Ecol='white';
if($Vcol=="") $Vcol='white';
if($Gcol=="") $Gcol='white';
if($Lcol=="") $Lcol='FFFFFF';

$LadeEmpfang = trim(getVariableFromQueryStringOrSession('LadeEmpfang'));

$Fz_ID = getVariableFromQueryStringOrSession('Fz_ID');
if(isset($_REQUEST['Change']))
{
$Empfaenger = getVariableFromQueryStringOrSession('Empfaenger');
	if($Menge=="0") $Menge='1';
	if($Fz_ID!="")
	{
		$db->sql_query("UPDATE frachtzettel SET Menge='$Menge', Eilgut='$Eilgut', Wenden='$Wenden', Mehrfach='$Mehrfach', Stueckgut='$Stueckgut',
		Zielbahnhof='$Zielbahnhof',Empfaenger='$Empfaenger',Ecol='$Ecol',Gewicht='$Gewicht',Wagengattung='$Wagengattung',Freight='$Freight',
		Ladung='$Ladung',Versandbahnhof='$Versandbahnhof',Versender='$Versender',Vcol='$Vcol',LadeEmpfang='$LadeEmpfang',
		Lcol='$Lcol',ZBV='$ZBV',VBV='$VBV',Treffen='$Treffen',User='$User',FUser_ID='$loggedInUser->user_id' WHERE id = ".$Fz_ID);
	echo sql_error();
	}
	else @$_REQUEST['Add']="";
}

if(!isset($_SESSION['farbe'])) $_SESSION['farbe'] = 0;
if(isset($_REQUEST['Add']))
{
$Empfaenger = getVariableFromQueryStringOrSession('Empfaenger');
	$_SESSION['farbe'] = !$_SESSION['farbe'];
	if($Menge=="0") $Menge='1';
	$db->sql_query("INSERT INTO frachtzettel (Menge, Eilgut, Wenden, Mehrfach, Stueckgut, Zielbahnhof, Empfaenger, Ecol, Gewicht,
		Wagengattung, Freight, Ladung, Versandbahnhof, Versender, Vcol, LadeEmpfang, Lcol, ZBV, VBV, Treffen, User, FUser_ID)
		VALUES('$Menge', '$Eilgut', '$Wenden', '$Mehrfach', '$Stueckgut', '$Zielbahnhof', '$Empfaenger', '$Ecol', '$Gewicht',
		'$Wagengattung', '$Freight', '$Ladung', '$Versandbahnhof', '$Versender', '$Vcol', '$LadeEmpfang', '$Lcol', '$ZBV', '$VBV', '$Treffen', '$User', '$loggedInUser->user_id');");
	$_SESSION['Fz_ID'] = $db->sql_nextid();
echo sql_error();
}
$Fz_ID = getVariableFromQueryStringOrSession('Fz_ID');

if(isset($_REQUEST['action']))
{
	$NHMS1 = "";
	$NHMS2 = "";
	$Search = "";
	$_SESSION['NHMS1'] = "";
	$_SESSION['NHMS1'] = "";
	$_SESSION['Search'] = "";

	if(@$_REQUEST['action']=="update")
	{
		$result=$db->sql_query("SELECT * FROM frachtzettel WHERE FUser_ID = '$loggedInUser->user_id' ORDER BY id DESC;");
		$i=0;
		while($row=$db->sql_fetchrow($result))
		{
			$Menge=getVariableFromQueryStringOrSession('M'.$i);
			if(is_numeric($All)) $Menge=intval($All);
			if(isset($_REQUEST['E'.$i])) $EilgutListe="checked"; else $EilgutListe="";
			if(isset($_REQUEST['W'.$i])) $WendenListe="checked"; else $WendenListe="";
			if(isset($_REQUEST['N'.$i])) $MehrfachListe="checked"; else $MehrfachListe="";
			if(isset($_REQUEST['S'.$i])) $StueckgutListe="checked"; else $StueckgutListe="";
			$db->sql_query("UPDATE frachtzettel SET Menge='$Menge', Eilgut='$EilgutListe', Wenden='$WendenListe', Mehrfach='$MehrfachListe', Stueckgut='$StueckgutListe'
			WHERE id = ".$row['id']);
echo sql_error();
			$i++;
		}
		unset($_SESSION['All']);
		unset($All);
	}

	if(@$_REQUEST['action']=="delete")
	{
		$db->sql_query("DELETE frachtzettel FROM frachtzettel LEFT JOIN Users ON frachtzettel.FUser_ID = Users.User_ID
		WHERE Users.User_ID = '$loggedInUser->user_id' and frachtzettel.id = ".round($_REQUEST['Fz_ID']));
echo sql_error();
		$_SESSION['Fz_ID']="";
	}
}

$Gcol = '#FFFFFF';
switch (ucfirst(strtok($Wagengattung,',')))
{
  case 'E': $Gcol = '#FFC4C4';
		break;
	case 'F': $Gcol = '#FFA8DD';
		break;
	case 'G': $Gcol = '#FFFF64';
		break;
	case 'H': $Gcol = '#FFBA79';
		break;
	case 'T': $Gcol = '#FFCD00';
		break;
	case 'I': $Gcol = '#FFFFFF';
		break;
	case 'Uai':
	case 'Ui':
	case 'L': $Gcol = '#BAFFFF';
		break;
	case 'K':
	case 'O': $Gcol = '#E5FFC4';
		break;
	case 'R':
	case 'S': $Gcol = '#C9FF85';
		break;
	case 'Z':
	case 'Uc':
	case 'Ue':
	case 'Uh': $Gcol = '#C0C0C0';
		break;
}

$Gewicht = $Gewicht." t";

$Frame = getVariableFromQueryStringOrSession('Frame');
$FzB = getVariableFromQueryStringOrSession('FzB');

//echo "R=".$_REQUEST['FzH']." S=".$_SESSION['FzH']." N=".$_SESSION['N']."<br>";
if(isset($_REQUEST['FzH']))
{
//	if($_REQUEST['FzH']=="N" and isset($_SESSION['N']))
	if($_REQUEST['FzH']=="N" and $_SESSION['FzH']=="N")
	{
		unset($_SESSION['FzH']);
		unset($_SESSION['N']);
	}
	else $_SESSION['N'] = getVariableFromQueryStringOrSession('FzH');
}
if(!isset($_SESSION['N']))
{
	if(substr($_SESSION['Spur'],0,2)=="N-") $_SESSION['FzH']="N";
	elseif($_SESSION['Spur']=="HO-USA") $_SESSION['FzH']="USA";
	else $_SESSION['FzH']="HO";
}
//echo "R=".$_REQUEST['FzH']." S=".$_SESSION['FzH']." N=".$_SESSION['N'];

if(isset($_REQUEST['FzA']))
{
	if(isset($_SESSION['FzA'])) unset($_SESSION['FzA']);
	else $FzA = getVariableFromQueryStringOrSession('FzA');
}

if($Freight=="") $_SESSION['translate'] = $_SESSION['Ladung'];
if(isset($_REQUEST['translate']) or $Freight=="")
{
	$langIn = 'auto';
	if($_SESSION['Ladung']==$_REQUEST['translate'])
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT Language FROM bahnhof WHERE id='$_SESSION[VBhf_ID]'"));
		$langIn = $row['Language'];
	}
	$Ladung = getVariableFromQueryStringOrSession('translate');
	$Freight = ucfirst(translates(rawurlencode($Ladung), $langIn, $langOut));
	if($Freight=="") // try translating from polish site
	$Freight = ucfirst(translatepl(rawurlencode($Ladung), $langIn, $langOut));
	unset($_REQUEST['translate']);

	if((strtolower($Ladung)==strtolower($Freight) or $Freight=="")) { // search in fyp database if nothing is translated
		$row=$db->sql_fetchrow($db->sql_query("SELECT Product_Description FROM fyp
		WHERE Produktbeschreibung = '".escape($Ladung)."'"));
		$Freight = ucfirst($row['Product_Description']); }	
} ?>

<style type="text/css">
<!--
input.Bhf
{
	text-align:center;
	font-family:Arial;
	font-size:21px;
	font-weight:bold;
	width:170px;
	border:0px;
}
-->
</style>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>

<title>Yellow Pages</title>
</head>

<script src="lib/inlinepopup/inlinepopup.js" type="text/javascript"></script>

<script type="text/javascript" src="lib/jscolor/jscolor.js"></script>

<body bgcolor='lightgrey'>

<?php /*
<div id="myDiv"><h2>Let AJAX change this text</h2></div>
<button type="button" onclick="loadXMLDoc()">Change Content</button>


<a href="" onMouseover="document.getElementById('ghost').style.display='none';" onMouseout="document.getElementById('ghost').style.display='block';" style="position:absolute; top:47px; left:54px; display:block;height:356px;width:170px;border:1px solid #000;">
<div style="position:absolute; top:0px; left:0px;display:block" id="ghost">
	<img src="img/Turn.svg">
</div>
</a>

<a href="" onClick="this.style.visibility = 'hidden'">
<div style="position:absolute; top:0px; left:0px;display:block" >
	<img src="img/Turn.svg">
</div>
</a>


<div style="position:absolute; top:80px; left:45px" >
	<img src="img/Palette.png">
</div>


<div style="position:absolute; top:147px; left:104px" >
<?php					echo"<select style='width:108px;height:13px;border:0px solid;border-color:gray;background-color:$Gcol;font-size:10px' name=VBhf_ID onchange='submit()'>";
						echo"<option>".$TEXT['lang-Wagengattung']."</option>";
						$result=$db->sql_query("SELECT distinct * FROM treffen WHERE Treffen = '$Treffen' ORDER BY Betriebsstelle");
						$i=0;
						while( $row=$db->sql_fetchrow($result) )
						{
							echo"<option value='$row[Bhf_ID]'>".$row['Betriebsstelle']."</option><b>";
							$i++;
						}
						echo"</select>";
</div>
*/?>


<?php selectBack('970',$lang)?>

<noscript>
	<br><font size='5' color='red' ><b>Javascript is disabled,<br> please activate Javascript</b></font><br>
</noscript>

<table border='0' cellpadding='0' cellspacing='0'>
	<tr>
	   	<form>
		   	<td valign='top' >
<?php		echo"<select dir='rtl' style='width:260px;height:26px;border:1px solid;border-color:gray;font-size:18px;font-weight:bold' name=Treffen onchange='submit()'>";
				echo"<option>".$TEXT['lang-meet']."</option>";
				$result=$db->sql_query("SELECT distinct Treffen FROM treffen ORDER BY Treffen");
				$i=0;
				while( $row=$db->sql_fetchrow($result) )
				{
					$row_Treffen = $row['Treffen'];
					echo"<option value='$row_Treffen'>".$row_Treffen."</option><b>";
					$i++;
				}
				echo"</select>";
?>			</td>
		</form>
		<form>
			<td valign='top' >
				<input type=text maxlength=50 style='width:260px;height:26px;font-size:12pt;font-weight:bold;background-color:white' name=Treffen value='<?php echo $Treffen?>'>
			</td>
			<td valign='top' >
				<input type=image src="img/key_enter.png">
			</td>
		</form>
		<td valign='top' >
			<?php selectLanguage($languages[$lang])?>
		</td>
	</tr>
</table>

<div style='position:absolute; top:308px; left:750px; width:250px; text-align:center; font-size:120%; color:darkred; font-family:Arial'>
<img src='img/Adobe_PDF.png' height='64px' onClick="self.location.href='Frachtzettel.php?action=getFz'"><br>
	<?php echo $TEXT['lang-Print']?><br>
	<a <?php echo "style='font-family:Arial;font-size:10pt;color:gray'"?>><?php echo $TEXT['lang-Kartenhoehe']?>:</a>
	<a <?php echo "style='font-family:Arial;font-size:12pt;color:gray'"?>><b><?php echo $_SESSION['FzH']?>&nbsp;</b></a>
</div>

<table border='0' cellpadding='0' cellspacing='0' >
	<td width='220px' >
		<table style='table-layout:fixed' width='205px' border='0' cellpadding='0' cellspacing='0' >
			<colgroup>
				<col width='45px'>
				<col width='52px'>
				<col width='120px'>
			</colgroup>
			<form>
				<tr>
					<td rowspan=6 >
						<?php if(isIE()!=""){?>
							<table style='table-layout:fixed' width='40px' height='80px' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td bgcolor='white' onClick=self.location.href='Frachtzettel.php?Ecol=white'></td>
									<td bgcolor='lime' onClick=self.location.href='Frachtzettel.php?Ecol=lime'></td>
								</tr>
								<tr>
									<td bgcolor='yellow' onClick=self.location.href='Frachtzettel.php?Ecol=yellow'></td>
									<td bgcolor='blue' onClick=self.location.href='Frachtzettel.php?Ecol=blue'></td>
								</tr>
								<tr>
									<td bgcolor='orange' onClick=self.location.href='Frachtzettel.php?Ecol=orange'></td>
									<td bgcolor='brown' onClick=self.location.href='Frachtzettel.php?Ecol=brown'></td>
								</tr>
								<tr>
									<td bgcolor='red' onClick=self.location.href='Frachtzettel.php?Ecol=red'></td>
									<td bgcolor='black' onClick=self.location.href='Frachtzettel.php?Ecol=black'></td>
								</tr>
							</table>
						<?php } ?>
					</td>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height=13px' bgcolor='<?php echo $Ecol?>' >
						<font style='font-family:Arial;font-size:8pt' <?php if($Ecol=="blue" or $Ecol=="brown" or $Ecol=="black") echo "color=white"?> >
							<img src='img/blank.gif' width='1' >
							<?php echo $TEXT['lang-Zielbahnhof']?>
						</font>
					</td>
				<tr>
				</tr>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000' height='34px' valign='top' bgcolor='<?php echo $Ecol?>' >
						<input style='text-align:center;font-family:Arial<?php if(strlen($Zielbahnhof)>16) echo " Narrow, Arial; font-stretch: condensed; font-weight: bold"?>
							;font-size:20px;font-weight:bold;width:170px;border:0px;
							<?php if($Ecol=="blue" or $Ecol=="brown" or $Ecol=="black")echo"color:white;"?>
							background-color:<?php echo $Ecol?>' id='Zielbahnhof' name='Zielbahnhof' value='<?php echo $Zielbahnhof?>'>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid
						<?php if($Ecol=="blue" or $Ecol=="brown" or $Ecol=="black") echo "#FFF"; else echo "#000"?>;height:13px' bgcolor='<?php echo $Ecol?>' >
						<font style='font-family:Arial;font-size:8pt' <?php if($Ecol=="blue" or $Ecol=="brown" or $Ecol=="black") echo "color=white"?> >
							<img src='img/blank.gif' width='1' >
							<?php echo $TEXT['lang-Empfaenger']?>
						</font>
					</td>
				<tr>
				</tr>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000' height='40px' valign='top' bgcolor='<?php echo $Ecol?>' >
						<textarea style='overflow:hidden;text-align:center;font-family:Arial;font-size:16px;width:170px;height:40px;border:0px;
							<?php if($Ecol=="blue" or $Ecol=="brown" or $Ecol=="black")echo"color:white;"?>background-color:
							<?php echo $Ecol?>' id='Empfaenger' name='Empfaenger' rows='2' ><?php echo $Empfaenger?>
						</textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td style='border-left:1px solid #000; border-top:1px solid #000;height:13px' bgcolor='white' >
						<font style='font-family:Arial;font-size:8pt' >
							<img src='img/blank.gif' width='1' >
							<?php echo $TEXT['lang-Gewicht']?>
						</font>
					</td>
					<td style='border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000;height:13px' bgcolor='<?php echo $Gcol?>' >
						<font style='font-family:Arial;font-size:8pt' >
							<img src='img/blank.gif' width='1' >
							<?php echo $TEXT['lang-Wagengattung']?>
						</font>
						<font style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;font-size:8pt' >
							(UIC)
						</font>

<?php	/*				echo"<select style='width:108px;height:13px;border:0px solid;border-color:gray;background-color:$Gcol;font-size:10px' name=VBhf_ID onchange='submit()'>";
						echo"<option>".$TEXT['lang-Wagengattung']."</option>";
						$result=$db->sql_query("SELECT distinct * FROM treffen WHERE Treffen = '$Treffen' ORDER BY Betriebsstelle");
						$i=0;
						while( $row=$db->sql_fetchrow($result) )
						{
							echo"<option value='$row[Bhf_ID]'>".$row['Betriebsstelle']."</option><b>";
							$i++;
						}
						echo"</select>";
*/ ?>

					</td>
				<tr>
				</tr>
					<td></td>
					<td style='border-left:1px solid #000; height:30px' valign='top' bgcolor='white' >
						<input style='text-align:center;font-family:Times;font-size:21px;width:51px;border:0px'
						id='Gewicht' name='Gewicht' value='<?php if($Gewicht!=0) echo $Gewicht;?>'>
					</td>
					<td style='border-left:1px solid #000; border-right:1px solid #000' valign='top' bgcolor=<?php echo $Gcol?> >
						<input style='text-align:center;font-family:Arial;font-size:21px;width:118px;border:0px;background-color:
							<?php echo $Gcol?>' id='Wagengattung' name='Wagengattung' value='<?php echo $Wagengattung?>'>
						</textarea>
					</td>
				</tr>
				<tr>
					<td rowspan='2' >
						<img border='0' height='20px' width='30px' src='img/unionjack.gif' alt=<?php echo $TEXT['lang-save']?>
						onclick="self.location.href='Frachtzettel.php?translate='+document.getElementById('Ladung').value;">
						<img border='0' height='20px' src='img/right.png' alt=<?php echo $TEXT['lang-save']?>
						onclick="self.location.href='Frachtzettel.php?translate='+document.getElementById('Ladung').value;">
					</td>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; valign='top' bgcolor='white' >
						<input style='text-align:center;font-family:Arial<?php if(strlen($Freight)>36) echo " Narrow, Arial; font-stretch: condensed; font-weight: bold"?>;font-size:11px;width:170px;height:14px;border:0px'
							id='Freight' name='Freight' value='<?php echo $Freight?>'>
						</textarea>
					</td>
				<tr>
				</tr>
					<td></td>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000' valign='top' bgcolor='white' >
						<textarea style='overflow:hidden;text-align:center;font-family:Arial;font-size:16px;width:170px;height:40px;border:0px'
							id='Ladung' name='Ladung' rows='2' ><?php echo $Ladung?>
						</textarea>
					</td>
				</tr>
				<tr>
					<td rowspan=6 >
						<?php if(isIE()!=""){?>
							<table style='table-layout:fixed' width='40px' height='80px' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td bgcolor='white' onClick=self.location.href='Frachtzettel.php?Vcol=white'></td>
									<td bgcolor='lime' onClick=self.location.href='Frachtzettel.php?Vcol=lime'></td>
								</tr>
								<tr>
									<td bgcolor='yellow' onClick=self.location.href='Frachtzettel.php?Vcol=yellow'></td>
									<td bgcolor='blue' onClick=self.location.href='Frachtzettel.php?Vcol=blue'></td>
								</tr>
								<tr>
									<td bgcolor='orange' onClick=self.location.href='Frachtzettel.php?Vcol=orange'></td>
									<td bgcolor='brown' onClick=self.location.href='Frachtzettel.php?Vcol=brown'></td>
								</tr>
								<tr>
									<td bgcolor='red' onClick=self.location.href='Frachtzettel.php?Vcol=red'></td>
									<td bgcolor='black' onClick=self.location.href='Frachtzettel.php?Vcol=black'></td>
								</tr>
							</table>
						<?php } ?>
					</td>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; 
						border-top:1px solid #000; height:13px' bgcolor='<?php echo $Vcol?>' >
						<font style='font-family:Arial;font-size:8pt'
							<?php if($Vcol=="blue" or $Vcol=="brown" or $Vcol=="black") echo "color=white"?> >
							<img src='img/blank.gif' widht='1' >
							<?php echo $TEXT['lang-Versandbahnhof']?>
						</font>
					</td>
				<tr>
				</tr>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; 
						height:34px' valign='top' bgcolor='<?php echo $Vcol?>' >
						<input style='text-align:center;font-family:Arial<?php if(strlen($Versandbahnhof)>16) echo " Narrow, Arial; font-stretch: condensed; font-weight: bold"?>
							;font-size:20px;font-weight:bold;width:170px;border:0px;
							<?php if($Vcol=="blue" or $Vcol=="brown" or $Vcol=="black")echo"color:white;"?>background-color:
							<?php echo $Vcol?>' id='Versandbahnhof' name='Versandbahnhof' value='<?php echo $Versandbahnhof?>'>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid
						<?php if($Vcol=="blue" or $Vcol=="brown" or $Vcol=="black") echo "#FFF"; else echo "#000"?>; height:13px' bgcolor='<?php echo $Vcol?>' >
						<font style='font-family:Arial;font-size:8pt' <?php if($Vcol=="blue" or $Vcol=="brown" or $Vcol=="black") echo "color=white"?> >
							<img src='img/blank.gif' widht='1' >
							<?php echo $TEXT['lang-Versender']?>
						</font>
					</td>
				<tr>
				</tr>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; height:40px' valign='top' bgcolor='<?php echo $Vcol?>' >
						<textarea style='overflow:hidden;text-align:center;font-family:Arial;font-size:16px;width:170px;height:40px;border:0px;
							<?php if($Vcol=="blue" or $Vcol=="brown" or $Vcol=="black")echo"color:white;"?>background-color:
							<?php echo $Vcol?>' id='Versender' name='Versender' rows='2' ><?php echo $Versender?>
						</textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan='2' id="LadeEmpfRem" style='border-left:1px solid #000; border-right:1px solid #000;
						border-top:1px solid #000; height;13px' bgcolor=<?php echo $Lcol?> >
						<font style='font-family:Arial;font-size:8pt;color:<?php
						 if (relativeluminance($Lcol) > 0.21) echo 'black'; else echo 'white';?>;' ><img src='img/blank.gif' widht='1' >
							<?php echo $TEXT['lang-Ladestelle']?> / <?php echo $TEXT['lang-rem']?>
						</font>
					</td>
				<tr>
				</tr>
					<td>
						<table style='table-layout:fixed' width='40px' border='0' cellpadding='0' cellspacing='0'>
							<td align='center' >
								<input name=Lcol value='<?php echo $Lcol?>'
								style='cursor:crosshair;width:22px;height:22px;font-size:8px;border:2px solid;
								border-color:gray;background-color:<?php echo $Lcol?>' maxlength=6
								class="color {slider:true,pickerFaceColor:'lightgrey',pickerFace:1,pickerBorder:0,pickerInsetColor:'black'}"
								onchange="document.getElementById('LadeEmpf').style.backgroundColor = '#'+this.color,
								document.getElementById('LadeEmpfRem').style.backgroundColor = '#'+this.color,
								document.getElementById('Lcol').value = '#'+this.color">
							</td>
						</table>
					</td>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; height=36px' >
						<textarea id="LadeEmpf" style='overflow:hidden;text-align:center;font-family:Arial;color:<?php
						 if (relativeluminance($Lcol) > 0.21) echo 'black'; else echo 'white';
						 ?>;font-size:16px;width:170px;height:36px;border:0px;background-color:<?php echo $Lcol?>
						 ' id='LadeEmpfang' name='LadeEmpfang' rows='2' ><?php echo $LadeEmpfang?>
						</textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan='2' style='border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #AAA;
					border-bottom:1px solid #000; height:13px' bgcolor='white' >
						<font style='font-family:Arial;font-size:8pt' ><img src='img/blank.gif' widht='1' >
							<?php echo $TEXT['lang-Besitzer']?>: <?php echo ucwords(str_replace('.',' ',$loggedInUser->display_username))?>
						</font>
					</td>
				</tr>
				<input type=hidden id='Eilgut' name='Eilgut' value=<?php echo $Eilgut?>>
				<input type=hidden id='Wenden' name='Wenden' value=<?php echo $Wenden?>>
				<input type=hidden id='Mehrfach' name='Mehrfach' value=<?php echo $Mehrfach?>>
				<input type=hidden id='Stueckgut' name='Stueckgut' value=<?php echo $Stueckgut?>>
				<input type=hidden id='Lcol' name='Lcol' value=<?php echo $Lcol?>>
				<tr>
					<table style='table-layout:fixed' border='0' cellpadding='0' cellspacing='0' >
						<colgroup>
							<col width='45px'>
							<col width='80px'>
							<col width='80px'>
						</colgroup>
						<td width=20 class=tabhead rowspan=2 align=middle >
							<img src="img/Printer.png" height='32px' onClick="self.location.href='Frachtzettel.php?action=getFz'">
						</td>
						<td>
							<button type="submit" name="Change" style='width:86px' >
								<img height='24px' border='0' src='img/ok.png' alt=<?php echo $TEXT['lang-save']?>><br>
								<b><?php echo $TEXT['lang-save']?></b>
							</button>
						</td>
						<td align='right' >
							<button type="submit" name="Add" style='width:86px' >
								<img height='24px' border='0' src='img/addblue.png' alt=<?php echo $TEXT['lang-add']?>><br>
								<b><?php echo $TEXT['lang-add']?></b>
							</button>
						</td>
					<table>
				</tr>
			</form>
		</table>
		<?php if(isIE()==""){?>
			<div style='position:absolute; top:58px; width:40px; text-align:center; font-size:80%; color:darkred; font-family:Arial'>
				<table style='table-layout:fixed' width='40px' height='80px' border='0' cellpadding='0' cellspacing='0'>
					<tr>
						<td bgcolor='white' onClick=self.location.href='Frachtzettel.php?Ecol=white'></td>
						<td bgcolor='lime' onClick=self.location.href='Frachtzettel.php?Ecol=lime'></td>
					</tr>
					<tr>
						<td bgcolor='yellow' onClick=self.location.href='Frachtzettel.php?Ecol=yellow'></td>
						<td bgcolor='blue' onClick=self.location.href='Frachtzettel.php?Ecol=blue'></td>
					</tr>
					<tr>
						<td bgcolor='orange' onClick=self.location.href='Frachtzettel.php?Ecol=orange'></td>
						<td bgcolor='brown' onClick=self.location.href='Frachtzettel.php?Ecol=brown'></td>
					</tr>
					<tr>
						<td bgcolor='red' onClick=self.location.href='Frachtzettel.php?Ecol=red'></td>
						<td bgcolor='black' onClick=self.location.href='Frachtzettel.php?Ecol=black'></td>
					</tr>
				</table>
			</div>

			<div style='position:absolute; top:258px; width:40px; text-align:center; font-size:80%; color:darkred; font-family:Arial'>
				<table style='table-layout:fixed' width='40px' height='80px' border='0' cellpadding='0' cellspacing='0'>
					<tr>
						<td bgcolor='white' onClick=self.location.href='Frachtzettel.php?Vcol=white'></td>
						<td bgcolor='lime' onClick=self.location.href='Frachtzettel.php?Vcol=lime'></td>
					</tr>
					<tr>
						<td bgcolor='yellow' onClick=self.location.href='Frachtzettel.php?Vcol=yellow'></td>
						<td bgcolor='blue' onClick=self.location.href='Frachtzettel.php?Vcol=blue'></td>
					</tr>
					<tr>
						<td bgcolor='orange' onClick=self.location.href='Frachtzettel.php?Vcol=orange'></td>
						<td bgcolor='brown' onClick=self.location.href='Frachtzettel.php?Vcol=brown'></td>
					</tr>
					<tr>
						<td bgcolor='red' onClick=self.location.href='Frachtzettel.php?Vcol=red'></td>
						<td bgcolor='black' onClick=self.location.href='Frachtzettel.php?Vcol=black'></td>
					</tr>
				</table>
			</div>
		<?php } ?>

<?php //#########################################################################################################
//###############################################################################################################
//############################################################################################################# ?>

	</td><td>

		<table style='table-layout:fixed' border='0' cellpadding='0' cellspacing='0' >
			<colgroup>
				<col width='220px'>
				<col width='220px'>
				<col width='360px'>
				<col width='30px'>
			</colgroup>
			<tr>
			   <form>
				  <td height='47px' >
<?php				echo"<select style='width:220px;height:26px;border:1px solid;border-color:gray;font-size:18px' name=ZBhf_ID onchange='submit()'>";
						echo"<option>".$TEXT['lang-Zielbahnhof']."</option>";
						$result=$db->sql_query("SELECT Betriebsstelle, Bhf_ID FROM manage WHERE MUser_ID = '$loggedInUser->user_id' ORDER BY Betriebsstelle");
						while( $row=$db->sql_fetchrow($result) )
						{
							$row_Bhf_ID = $row['Bhf_ID'];
							echo"<option value='$row[Bhf_ID]'>".$row['Betriebsstelle']."</option><b>";
						}
						$result=$db->sql_query("SELECT distinct treffen.Betriebsstelle, treffen.Bhf_ID, bahnhof.Art 
						FROM treffen LEFT JOIN bahnhof ON bahnhof.id = treffen.Bhf_ID
						WHERE treffen.Treffen = '$Treffen' and bahnhof.Art = 'SBF' ORDER BY Betriebsstelle");
						while( $row=$db->sql_fetchrow($result) )
						{
							echo"<option value='$row[Bhf_ID]'>".$row[Betriebsstelle]." [SBF]</option><b>";
						}
						echo"</select>";
?>				</td>
				</form>
				<form>
				   	<td rowspan='2' colspan='2' >
						<table style='table-layout:fixed' border='0' cellpadding='0' cellspacing='0' >
							<colgroup>
								<col width='50px'>
								<col width='180px'>
								<col width='350px'>
							</colgroup>
					   		<tr style='font-family:Arial;font-size:10pt;color:gray' >
								<td ></td>
								<td >
								    <input type="radio" onclick='self.location.href="Frachtzettel.php?Frame=D"'
								    	<?php if($_SESSION['Frame']=="D" or $_SESSION['Frame']=="") echo "checked"?> ><?php echo $TEXT['lang-DoubleLine']?><br>
								    <input type="radio" onclick='self.location.href="Frachtzettel.php?Frame=E"'
								    	<?php if($_SESSION['Frame']=="E") echo "checked"?> ><?php echo $TEXT['lang-SingleLine']?><br>
								    <input type="radio" onclick='self.location.href="Frachtzettel.php?Frame=O"'
								    	<?php if($_SESSION['Frame']=="O") echo "checked"?> ><?php echo $TEXT['lang-NoLine']?><br>
								    <input type="radio" onclick='self.location.href="Frachtzettel.php?Frame=W"'
								    	<?php if($_SESSION['Frame']=="W") echo "checked"?> ><?php echo $TEXT['lang-WhiteLine']?>
								</td>
								<td ><br>
							   	    <input type="radio" onclick='self.location.href="Frachtzettel.php?FzB=45"' <?php if($_SESSION['FzB']=="45"
							   	    	or $_SESSION['FzB']=="") echo "checked"?> ><?php echo $TEXT['lang-45mmBreite']?><br>
								    <input type="radio" onclick='self.location.href="Frachtzettel.php?FzB=48"' <?php if($_SESSION['FzB']=="48")
								    	echo "checked"?> ><?php echo $TEXT['lang-48mmBreite']?><br>
									<br>
								    <a <?php if(isset($_SESSION['N'])) echo "style='background-color:orange'"?>>
									    <input type="checkbox" onclick='self.location.href="Frachtzettel.php?FzH=N"'
									    <?php if($_SESSION['FzH']=="N") echo "checked"?> ><?php echo $TEXT['lang-N-Frachtzettel']?>&nbsp;</a><br>

								    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $TEXT['lang-Kartenhoehe']?>:&nbsp;
								    	<a <?php echo "style='font-size:12pt;color:black; border: solid 0px black;'"?>><b><?php echo $_SESSION['FzH']?>&nbsp;</b></a><br>
							   	</td>
					   		<tr>
					   	</table>
				   	</td>
				</form>
			</tr>
			<tr>
				<form>
				   	<td height='53px' >
<?php	//				<select style='width:220px;height:26px;border:1px solid;border-color:gray;font-size:18px' name='Empfaenger' onchange="document.getElementById('Empfaenger').value = this.value">
						echo"<select style='width:220px;height:26px;border:1px solid;border-color:gray;font-size:18px' name=Empfaenger onchange='submit()'>";
						echo"<option>".$TEXT['lang-Empfaenger']."</option>";
						$result=$db->sql_query("SELECT DISTINCT Anschliesser FROM fyp WHERE Bhf_ID = '$ZBhf_ID' ORDER BY Anschliesser");
						while( $row=$db->sql_fetchrow($result) )
						{
							$row_Anschliesser = $row['Anschliesser'];
							echo"<option value='$row_Anschliesser'>".$row_Anschliesser."</option><b>";
						}
						$result=$db->sql_query("SELECT DISTINCT fyp.Anschliesser, bahnhof.Haltestelle, Bahnhof.Spur FROM treffen 
						JOIN fyp ON treffen.An_ID = fyp.Bhf_ID JOIN bahnhof ON treffen.An_ID = bahnhof.id
						WHERE treffen.Treffen = '".escape($Treffen)."' AND treffen.Bhf_ID = '$ZBhf_ID' ORDER BY Haltestelle, Anschliesser");		
						while($row=$db->sql_fetchrow($result))
						{
							$row_Anschliesser = $row['Anschliesser'];
							$row_Haltestelle = $row['Haltestelle'];
							$row_Spur = $row['Spur'];
							echo"<option value='$row_Haltestelle__$row_Anschliesser__$row_Spur'>".$row_Haltestelle." - ".$row_Anschliesser."</option><b>";
						}
						echo"</select>";
		?>			</td>
				</form>
			</tr>
			<tr>
			   	<td height='31' >
			   	</td>
			</tr>
			<tr>
				<form>
				   	<td colspan='3' height='18px' valign='bottom' >
						<select style='width:800px;height:18px;border:1px solid;border-color:gray;font-size:12px
<?php					if(isset($_REQUEST['VBhf_ID'])) echo ";color:Gray"?>' name=NHMS1 onchange='submit()'>
<?php					if($NHMS1=="") echo"<option>".$TEXT['lang-nhm1']."</option>";
						else
						{
							$NHM1=$NHMS1."000000";
							$row_NHM1=$db->sql_fetchrow($db->sql_query("SELECT distinct * FROM nhm WHERE NHM_Code = '$NHM1' ORDER BY NHM_Code;"));
							echo"<option>".sprintf("%02d", $NHMS1)." ".$row_NHM1[$languages[$lang]]."</option>";
						}
						echo"<option></option>";

						$result = getTableContents($Treffen, '', '', '> 0', 'fyp.NHM_Code');
						$NHM1_="";
						while($row = $db->sql_fetchboth($result))
						{
							$NHM_Code1=substr($row['NHM_Code'],0,-6)."000000";
							$row_NHM1=$db->sql_fetchrow($db->sql_query("SELECT distinct * FROM nhm WHERE NHM_Code = '$NHM_Code1' ORDER BY NHM_Code;"));

							$NHM1=sprintf("%02d", substr($NHM_Code1,0,-6));
							if($NHM1 != $NHM1_)
							{
								echo"<option value='".substr($NHM_Code1,0,-6)."'>".$NHM1." ".$row_NHM1[$languages[$lang]]."</option>";
							}
							$NHM1_=sprintf("%02d", substr($NHM_Code1,0,-6));
						}
						echo"</select>";
		?>			</td>
				</form>
			</tr>
			<tr>
				<form>
				   	<td colspan='3' height='18px' >
						<select style='width:800px;height:18px;border:1px solid;border-color:gray;font-size:12px
<?php					if(isset($_REQUEST['VBhf_ID'])) echo ";color:Gray"?>' name=NHMS2 onchange='submit()'>
<?php					if($NHMS2=="" or isset($_REQUEST['NHMS1'])) echo"<option>".$TEXT['lang-nhm2']."</option>";
						else
						{
							$NHM2=$NHMS2."0000";
							$row_NHM2=$db->sql_fetchrow($db->sql_query("SELECT distinct * FROM nhm WHERE NHM_Code = '$NHM2' ORDER BY NHM_Code;"));
							echo"<option>".sprintf("%04d", $NHMS2)." ".$row_NHM2[$languages[$lang]]."</option>";
						}
						echo"<option></option>";

						$result = getTableContents($Treffen, '', '', '> 0', 'fyp.NHM_Code');
						$NHM2_="";
						while($row = $db->sql_fetchboth($result))
						{
							$NHM_Code2=substr($row['NHM_Code'],0,-4)."0000";
							$row_NHM2=$db->sql_fetchrow($db->sql_query("SELECT distinct * FROM nhm WHERE NHM_Code = '$NHM_Code2' ORDER BY NHM_Code;"));

							$NHM2=sprintf("%04d", substr($NHM_Code2,0,-4));
							if($NHM2 != $NHM2_)
							{

								if($NHMS1=="")
								{
									echo"<option value='".substr($NHM_Code2,0,-4)."'>".$NHM2." ".$row_NHM2[$languages[$lang]]."</option>";
								}
								elseif(substr($NHM_Code2,0,-6)==$NHMS1)
								{
									echo"<option value='".substr($NHM_Code2,0,-4)."'>".$NHM2." ".$row_NHM2[$languages[$lang]]."</option>";
								}
							}
							$NHM2_=sprintf("%04d", substr($NHM_Code2,0,-4));
						}
						echo"</select>";
?>
					</td>
				</form>
			</tr>
			<tr>
				<form>
				   	<td colspan='2' height='36px' width='440px' valign='top' >
						<select style='width:440px;height:26px;border:1px solid;border-color:gray
<?php

						if(isset($_REQUEST['VBhf_ID'])) echo ";background-color:Yellow"?>;
<?php					if($Search!="") echo "background-color:Yellow;"?>font-size:18px' name=FYP_ID onchange='submit()'>
						<option><?php echo $TEXT['lang-Fracht']?></option>
<?php
						if(isset($_REQUEST['VBhf_ID']))
						{
							$row = $db->sql_fetchrow($result = $db->sql_query("SELECT * FROM treffen 
							WHERE treffen.Treffen = '".escape($Treffen)."' AND Bhf_ID = '$VBhf_ID'"));
					//		echo "<option value='$row['An_ID']'>".$VBhf_ID." / ".$row[Bhf_ID]." / ".$row['An_ID']." / ".$Treffen."</option><b>";
							if($row['An_ID'] != 0)
							{
								$result = $db->sql_query("SELECT DISTINCT fyp.* FROM fyp 
								INNER JOIN treffen ON (treffen.Bhf_ID = fyp.Bhf_ID OR treffen.An_ID = fyp.Bhf_ID)
								WHERE treffen.Treffen = '".escape($Treffen)."' 
								AND treffen.Bhf_ID = ".$_REQUEST['VBhf_ID']."
								AND fyp.NHM_Code > '0'
								ORDER BY Produktbeschreibung, Betriebsstelle, Anschliesser ;");
							}
							else
							{	
								$result = $db->sql_query("SELECT DISTINCT fyp.* FROM fyp 
								INNER JOIN treffen ON treffen.Bhf_ID = fyp.Bhf_ID
								WHERE treffen.Treffen = '".escape($Treffen)."' 
								AND treffen.Bhf_ID = ".$_REQUEST['VBhf_ID']."
								AND fyp.NHM_Code > '0'
								ORDER BY Produktbeschreibung, Betriebsstelle, Anschliesser ;");
							}	
							
						}
						elseif($Search!="")
						{
							if(is_numeric(preg_replace('/\s+/', '', $Search))) $Search = preg_replace('/\s+/', '', $Search);
							$result = $db->sql_query("SELECT DISTINCT nhm.*, treffen.*, fyp.*, bahnhof.*, gleise.*
							FROM fyp LEFT JOIN nhm ON nhm.NHM_Code = fyp.NHM_Code
							LEFT JOIN bahnhof ON bahnhof.id = fyp.Bhf_ID
							LEFT JOIN gleise ON gleise.id = fyp.Gleis_ID
							INNER JOIN treffen ON (treffen.Bhf_ID = bahnhof.id OR treffen.An_ID = bahnhof.id)
							WHERE treffen.Treffen = '$Treffen' and
							(fyp.Produktbeschreibung LIKE '%$Search%' or
							fyp.Product_Description LIKE '%$Search%' or
							nhm.English LIKE '%$Search%' or
							nhm.Nederlands LIKE '%$Search%' or
							nhm.Deutsch LIKE '%$Search%' or
							nhm.Francais LIKE '%$Search%' or
							nhm.Italiano LIKE '%$Search%' or
							nhm.Dansk LIKE '%$Search%' or
							nhm.Espanol LIKE '%$Search%' or
							nhm.Polski LIKE '%$Search%' or
							nhm.Bulgarian LIKE '%$Search%' or
							nhm.Greek LIKE '%$Search%' or
							nhm.Czech LIKE '%$Search%' or
							nhm.Rumanian LIKE '%$Search%' or
							nhm.Hungarian LIKE '%$Search%' or
							nhm.Russian LIKE '%$Search%' or
							nhm.Portuges LIKE '%$Search%' or
							nhm.Slovak LIKE '%$Search%' or
							nhm.Slovene LIKE '%$Search%' or
							nhm.Svenska LIKE '%$Search%' or
							nhm.Estonian LIKE '%$Search%' or
							nhm.Suomeksi LIKE '%$Search%' or
							nhm.Latvian LIKE '%$Search%' or
							nhm.Lithuanian LIKE '%$Search%' or
							nhm.NHM_Code LIKE '%$Search%')
							ORDER BY Produktbeschreibung");
echo sql_error();
						}
						elseif($NHMS2!="") $result = getTableContents($Treffen, '', '', "LIKE '".$NHMS2."____'", 'Produktbeschreibung');
						elseif($NHMS1!="") $result = getTableContents($Treffen, '', '', "LIKE '".$NHMS1."______'", 'Produktbeschreibung');
						else $result = getTableContents($Treffen, '', '', '> 0', 'Produktbeschreibung');

						$FYP_ID_="";
						while($row = $db->sql_fetchboth($result)) // problem sql_fetchboth
						{
							$row_id_fyp = $row['id_fyp'];
							if($row_id_fyp != $FYP_ID_) // vermeide doppelte Einträge
							{
								if($Treffen=="") echo"<option value='$row_id_fyp'>".$row['Produktbeschreibung']." [".$row['Betriebsstelle']."] ".$row['Anschliesser']."</option><b>";

								if(isset($_REQUEST['VBhf_ID']))
								{ 
									$An = "";
									if($Versandbahnhof != $row['Betriebsstelle']) $An = $row['Betriebsstelle']." - ";
									echo"<option value='$row_id_fyp'>".$row['Produktbeschreibung']." [".$Versandbahnhof."] ".$An.$row['Anschliesser']."</option><b>";
								}
								else echo"<option value='$row_id_fyp'>".$row['Produktbeschreibung']." [".$row[26]."] ".$row['Anschliesser']."</option><b>";
							}
							$FYP_ID_ = $row_id_fyp;
						}
						echo"</select>";
		?>			</td>
				</form>
				<form>
				   	<td height='32px' width='360px' valign='top' >
						<input type='text' style='width:360px;height:26px;<?php if($Search!="" and !isset($_REQUEST['VBhf_ID']))
						echo "background-color:Yellow;"?>font-size:16px' maxlength='100' name='Search' value='<?php echo $Search?>' >
					</td>
					<td valign='top' >
						<input type=image src="img/key_enter.png">
					</td>
				</form>
			</tr>
			<tr>
				<form>
				   	<td height='47px' >
						<select style='width:220px;height:26px;border:1px solid;border-color:gray;font-size:18px
<?php					if(isset($_REQUEST['VBhf_ID'])) echo ";background-color:Yellow"?>' name='VBhf_ID' onchange='submit()'>
<?php					echo"<option>".$TEXT['lang-Versandbahnhof']."</option>";
						echo"<option></option>";

						$result=$db->sql_query("SELECT distinct treffen.Betriebsstelle, treffen.Bhf_ID, treffen.An_ID, bahnhof.Art FROM treffen 
						LEFT JOIN bahnhof ON bahnhof.id = treffen.Bhf_ID
						WHERE treffen.Treffen = '$Treffen' and bahnhof.Art != 'SBF' ORDER BY Betriebsstelle");
						
						$Bhf_ID_="";
						while( $row=$db->sql_fetchrow($result) )
						{
							$An_ = ""; if($row['An_ID'] != 0) $An_ = " +";
							$row_Bhf_ID = $row['Bhf_ID'];
							if($row_Bhf_ID != $Bhf_ID_) echo"<option value='$row_Bhf_ID'>".$row['Betriebsstelle'].$An_."</option><b>";
							$Bhf_ID_ = $row_Bhf_ID;
						}
						$result=$db->sql_query("SELECT distinct treffen.Betriebsstelle, treffen.Bhf_ID, bahnhof.Art FROM treffen LEFT JOIN bahnhof ON bahnhof.id = treffen.Bhf_ID
						WHERE treffen.Treffen = '$Treffen' and bahnhof.Art = 'SBF' ORDER BY Betriebsstelle");
						while( $row=$db->sql_fetchrow($result) )
						{
							echo"<option value='$row[Bhf_ID]'>".$row['Betriebsstelle']." [SBF]</option><b>";
						}
						echo"</select>";
?>					</td>
				</form>
				   	<td rowspan='8' colspan='2' >
						<table style='table-layout:fixed' border='0' cellpadding='0' cellspacing='0' >
							<colgroup>
								<col width='20px'>
								<col width='80px'>
								<col width='15px'>
								<col width='140px'>
							</colgroup>
							<tr>
					   			<td ></td>
								<td rowspan=5 align=center bgcolor=white>
									<div>
										<img src='img/Frachtzettel.png' height='150' class='imgborder' style='position: relative; z-index: 1;'>
										<?php if($Eilgut=='checked') echo "<img src='img/RoteLinie.png' height='150' class='imgborder'
																 style='margin:-150px 0 0 0px; position: relative; z-index: 2;'>"?>
										<?php if($Wenden=='checked') echo "<img src='img/Turn.png' height='150' class='imgborder'
																 style='margin:-150px 0 0 0px; position: relative; z-index: 3;'>"?>
										<?php if($Mehrfach=='checked') echo "<img src='img/Mehrfach.png' height='150'
																 class='imgborder' style='margin:-150px 0 0 0px; position: relative; z-index: 4;'>"?>
										<?php if($Stueckgut=='checked') echo "<img src='img/Stueckgut_orange.png' width='76'
																 class='imgborder' style='margin:-108px 0 0 0px; position: relative; z-index: 5;'>"?>
									</div>
					   			</td>
					   		</tr>
	   						<form>
						   		<tr>
						   			<td></td>
									<td align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:pink' >E</td>
									<td>
										<button type='submit' name='Eil' style='width:140px; height:40px' >
											<img align='left' height='36px' border='0' src='img/RoteLinie.png' alt=<?php echo $TEXT['lang-Eilgut']?>>
											<b style='font: 14px Verdana;line-height:40px;vertical-align:middle'><?php echo $TEXT['lang-Eilgut']?></b>
										</button>
				   					</td>
						   		</tr>
	   						</form>
	   						<form>
						   		<tr>
						   			<td></td>
									<td align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:gray' >W</td>
				   					<td>
										<button type='submit' name='Wend' style='width:140px; height:40px' >
											<img align='left' height='36px' border='0' src='img/Turn.png' alt=<?php echo $TEXT['lang-Wenden']?>>
											<b style='font: 14px Verdana;line-height:40px;vertical-align:middle'><?php echo $TEXT['lang-Wenden']?></b>
										</button>
				   					</td>
						   		</tr>
	   						</form>
	   						<form>
						   		<tr>
						   			<td></td>
									<td align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:white' >M</td>
				   					<td>
										<button type='submit' name='Mehr' style='width:140px; height:40px' >
											<img align='left' height='36px' border='0' src='img/Mehrfach.png' alt=<?php echo $TEXT['lang-Mehrfach']?>>
											<b style='font: 14px Verdana;line-height:40px;vertical-align:middle'><?php echo $TEXT['lang-Mehrfach']?></b>
										</button>
				   					</td>
						   		</tr>
	   						</form>
	   						<form>
						   		<tr>
						   			<td></td>
									<td align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:orange' >S</td>
				   					<td>
										<button type="submit" name="Stck" style='width:140px; height:40px' >
											<img align='left' height='35px' border='0' src='img/Stueckgut_orange.png' alt=<?php echo $TEXT['lang-Stueckgut']?>>
											<b style='font: 14px Verdana;line-height:40px;vertical-align:middle'><?php echo $TEXT['lang-Stueckgut']?></b>
										</button>
									</td>
						   		</tr>
	   						</form>
						</table>
					</td>
			</tr>
			<tr>
				<form>
				   	<td height='53px' >
		<?php			echo"<select style='width:220px;height:26px;border:1px solid;border-color:gray;font-size:18px' name=Versender onchange='submit()'>";
						echo"<option>".$TEXT['lang-Versender']."</option>";
						$result=$db->sql_query("SELECT distinct Anschliesser FROM fyp WHERE Bhf_ID = '$VBhf_ID' ORDER BY Anschliesser");
						while( $row=$db->sql_fetchrow($result) )
						{
							$row_Anschliesser = $row['Anschliesser'];
							echo"<option value='$row_Anschliesser'>".$row_Anschliesser."</option><b>";
						}
						$result=$db->sql_query("SELECT DISTINCT fyp.Anschliesser, bahnhof.Haltestelle, Bahnhof.Spur FROM treffen 
						JOIN fyp ON treffen.An_ID = fyp.Bhf_ID JOIN bahnhof ON treffen.An_ID = bahnhof.id
						WHERE treffen.Treffen = '".escape($Treffen)."' AND treffen.Bhf_ID = '$VBhf_ID' ORDER BY Haltestelle, Anschliesser");		
						while($row=$db->sql_fetchrow($result))
						{
							$row_Anschliesser = $row['Anschliesser'];
							$row_Haltestelle = $row['Haltestelle'];
							$row_Spur = $row['Spur'];
							echo"<option value='$row_Haltestelle__$row_Anschliesser__$row_Spur'>".$row_Haltestelle." - ".$row_Anschliesser."</option><b>";
						}
						echo"</select>";
		?>			</td>
				</form>
			</tr>
			<tr>
				<form>
				   	<td height='49px' >
		<?php			echo"<select style='width:220px;height:26px;border:1px solid;border-color:gray;
						font-size:18px;font-family:Arial Narrow, Arial; font-stretch: condensed' name=TrackID onchange='submit()'>";
						echo"<option>".$TEXT['lang-Ladestelle']." (".$TEXT['lang-Empfaenger'].")</option>";
						$result=$db->sql_query("SELECT * FROM gleise WHERE Bhf_ID = '$ZBhf_ID' 
						ORDER BY (0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0), Ladestelle");
						while( $row=$db->sql_fetchrow($result) )
						{
							echo"<option value='$row[id]'>".$row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")</option><b>";
						}
						$result=$db->sql_query("SELECT gleise.*, bahnhof.Haltestelle, treffen.Anschliesser FROM treffen 
						JOIN gleise ON treffen.An_ID = gleise.Bhf_ID JOIN bahnhof ON treffen.An_ID = bahnhof.id
						WHERE treffen.Treffen = '".escape($Treffen)."' AND treffen.Bhf_ID = '$ZBhf_ID' 
						ORDER BY Haltestelle, (0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0), Ladestelle");
						while($row=$db->sql_fetchrow($result))
						{
							echo"<option value='$row[id]'>".trim(substr($row['Anschliesser'],0 ,strrpos($row['Anschliesser'], '['))).
							" - ".$row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")</option><b>";
						}
						echo"</select>";
		?>			</td>
				</form>
			</tr>
			</tr>
				<td height='13px' ></td>
			<tr>
			</tr>
				<td height='40px' ></td>
			<tr>
		</table>
	</td>
</table>


<form action=Frachtzettel.php method=get>
	<table style='table-layout:fixed' width='1010px' border='0' cellpadding='0' cellspacing='0' >
		<colgroup>
			<col width='45px'>
			<col width='15px'>
			<col width='15px'>
			<col width='10px'>
			<col width='190px'>
			<col width='25px'>
			<col width='70px'>
			<col width='200px'>
			<col width='190px'>
			<col width='190px'>
			<col width='30px'>
			<col width='30px'>
		</colgroup>

		<tr><td bgcolor=white colspan=12 ><img src=img/blank.gif width=1px height=6px></td></tr>
		<tr><td bgcolor=blue colspan=12><img src=img/blank.gif width=1px height=1px></td></tr>

		<input type=hidden name=action value=update>
		<tr bgcolor=lightblue>
			<td class=tabhead rowspan=1 align=middle >
				<input type='text' style='width:30px; height:24px; text-align:right; font-size:18px' maxlength='2' name='All' value=<?php echo $All?>><br>
			</td>
			<td class=tabhead align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:pink' >E</td>
			<td class=tabhead align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:gray' >W</td>
			<td class=tabhead rowspan=2 ></td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><?php echo $TEXT['lang-Zielbahnhof']?></td>
			<td class=tabhead rowspan=2 align=middle ><img style="background-color:lightblue;height:22px;border:0px" src="img/weight.ico"></td>
			<td class=tabhead rowspan=2 align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><?php echo $TEXT['lang-Wagengattung']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><?php echo $TEXT['lang-Fracht']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><?php echo $TEXT['lang-Versandbahnhof']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' ><?php echo $TEXT['lang-Ladestelle']?></td>
			<td class=tabhead rowspan=2 align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' >Edit</td>
			<td class=tabhead rowspan=2 align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' >Del</td>
		</tr>
		<tr bgcolor=lightblue>
			<td class=tabhead rowspan=1 align=middle ><input type=image height='24px' src="img/down.png"><br></td>
			<td class=tabhead align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:white' >M</td>
			<td class=tabhead align=middle style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;background-color:orange' >S</td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed' ><?php echo $TEXT['lang-Empfaenger']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed' >Freight</td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed' ><?php echo $TEXT['lang-Versender']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow, Arial; font-stretch: condensed' ><?php echo $TEXT['lang-meet']?></td>
		</tr>
		<tr><td bgcolor=blue colspan=12><img src=img/blank.gif width=1px height=1px></td></tr>
<?php

	$farbe = $_SESSION['farbe'];
	$result=$db->sql_query("SELECT * FROM frachtzettel WHERE FUser_ID = '$loggedInUser->user_id' ORDER BY id DESC;");
	$i=0;
	while( $row=$db->sql_fetchrow($result) )
	{
		if($Fz_ID==$row['id']) echo "<tr bgcolor=lightgreen valign=center >";
		else echo "<tr style='background-color: " . ($farbe ? '#ddd' : '#fff')  . "' valign=center>";
		echo "<td rowspan=2 align=middle ><input type='text' style='width:30px;text-align:right;font-size:18px' maxlength='2' name='M".$i."' value=".$row['Menge']."></div></td>";
		echo "<td align=middle style='background-color:pink' ><input type='checkbox' name='E".$i."'".$row['Eilgut']." ></div></td>";
		echo "<td align=middle style='background-color:gray' ><input type='checkbox' name='W".$i."'".$row['Wenden']." ></div></td>";
		echo "<td rowspan=2></td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold'>".trim($row['Zielbahnhof'])."</td>";
		if($row['Gewicht'] != 0) echo "<td rowspan=2 style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' align=middle ><b>".$row['Gewicht']."</b> t</td>"; else echo "<td rowspan=2 ></td>";
		echo "<td rowspan=2 style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' align=middle >".$row['Wagengattung']."</td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold'>".trim($row['Ladung'])."</td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold'>".trim($row['Versandbahnhof'])."</td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold'>".trim($row['LadeEmpfang'])."</td>";
		echo "<td rowspan=2 align=middle ><a onclick=\"return\"href=Frachtzettel.php?action=edit&Fz_ID=".$row['id']."><img style='height:22px;border:0px' src='img/edit.png'></a></td>";
		echo "<td rowspan=2 align=middle ><a onclick=\"return\"href=Frachtzettel.php?action=delete&Fz_ID=".$row['id']."><img style='height:22px;border:0px' src='img/del.png'></a></td>";
		echo "</tr>";

		if($Fz_ID==$row['id']) echo "<tr bgcolor=lightgreen valign=center >";
		else echo "<tr style='background-color: " . ($farbe ? '#ddd' : '#fff')  . "' valign=center>";
		echo "<td align=middle style='background-color:white'><input type='checkbox' name='N".$i."'".$row['Mehrfach']." ></div></td>";
		echo "<td align=middle style='background-color:orange'><input type='checkbox' name='S".$i."'".$row['Stueckgut']." ></div></td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed' >".trim($row['Empfaenger'])."</td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed;' >".trim($row['Freight'])."</td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed' >".trim($row['Versender'])."</td>";
		echo "<td style='font-family:Arial Narrow, Arial; font-stretch: condensed;' >".trim($row['Treffen'])."</td>";
		echo "</tr>";

		echo "<tr><td bgcolor=gray colspan=12><img src=img/blank.gif width=1px height=1px></td></tr>";

		$farbe = !$farbe;
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor=lightblue colspan=12 ><img src=img/blank.gif width=1px height=8px></td></tr>";
?>
	</table>
	<input type=submit value='<?php echo $TEXT['lang-save']?>' >
</form>

</body>
</html>