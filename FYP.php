<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

//  error_reporting(E_ALL);
//  ini_set("display_errors", 1);

	header ('Content-type: text/html; charset=utf8');
	require_once("user/models/config.php");
	require_once("lib/shared.inc.php");
	require_once("lib/menu.inc.php");
	require_once("SQL/backup.php");

	//Prevent the user visiting the logged in page if he/she is not logged in
	if(!isUserLoggedIn()) { header("Location: user/login.php"); die(); }

//====================================================================================================

//unset($_SESSION['Gleis_ID']);

	if(@$_REQUEST['action']=="edit")
	{
		$row=$db->sql_fetchrow($result=$db->sql_query("SELECT fyp.*, manage.* FROM fyp
		LEFT JOIN manage ON manage.Bhf_ID = fyp.Bhf_ID WHERE manage.MUser_ID = '$loggedInUser->user_id'
		and fyp.id_fyp=".round($_REQUEST['FYP_ID'])));
//		$row=$db->sql_fetchrow($result=$db->sql_query("SELECT * FROM fyp WHERE id_fyp=".round($_REQUEST['FYP_ID'])));
echo sql_error();
		$_SESSION['NHM_Code'] = $row['NHM_Code'];
		$_SESSION['Wagengattung'] = $row['Wagengattung'];
		$_SESSION['Produktbeschreibung'] = $row['Produktbeschreibung'];
		$_SESSION['Product_Description'] = $row['Product_Description'];
		$_SESSION['Anschliesser'] = $row['Anschliesser'];
		$_SESSION['Ladestelle'] = $row[Ladestelle];
		$_SESSION['Gleis_ID'] = $row['Gleis_ID'];
		$_SESSION['FYP_Bem'] = $row['FYP_Bem'];
		$_SESSION['Wagen_Woche'] = $row['Wagen_Woche'];
		$_SESSION['FYP_ID'] = $row['id_fyp'];
	}

	$NHM_Code = getVariableFromQueryStringOrSession('NHM_Code');
	$Wagengattung = getVariableFromQueryStringOrSession('Wagengattung');
	$Produktbeschreibung = getVariableFromQueryStringOrSession('Produktbeschreibung');
	$Product_Description = getVariableFromQueryStringOrSession('Product_Description');
	$Anschliesser = getVariableFromQueryStringOrSession('Anschliesser');
	$Ladestelle = getVariableFromQueryStringOrSession('Ladestelle');
	$Gleis_ID = intval(getVariableFromQueryStringOrSession('Gleis_ID'));
	$FYP_Bem = getVariableFromQueryStringOrSession('FYP_Bem');
	$Wagen_Woche = intval(getVariableFromQueryStringOrSession('Wagen_Woche'));
	if($Wagen_Woche=="") $Wagen_Woche="1";

	$nhm = getVariableFromQueryStringOrSession('nhm');

	$row=$db->sql_fetchrow($result=$db->sql_query("SELECT * FROM bahnhof WHERE id = '".round($Bhf_ID)."'"));
	$Art = $row['Art'];

	if($Art=="Station")$Art_lang=$TEXT['lang-bh'];
	elseif($Art=="Connect")$Art_lang=$TEXT['lang-an'];
	elseif($Art=="Stop")$Art_lang=$TEXT['lang-hp'];
	elseif($Art=="Block")$Art_lang=$TEXT['lang-bl'];
	else $Art_lang=$Art;

	$FYP_ID = intval(getVariableFromQueryStringOrSession('FYP_ID'));

	if($Gleis_ID=="")$Gleis_ID="0";
	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM gleise WHERE id = '$Gleis_ID' "));
	$Ladestelle = $row['Ladestelle'];

	if(isset($_REQUEST['Change']))
	{
//echo $FYP_ID;
//echo $Ladestelle;
		$NHM_Code = preg_replace('/[^0-9]/', '', $NHM_Code);
		if($NHM_Code=="") $NHM_Code = "99000000"; // GÜTER, ANDERWEITIG WEDER GENANNT NOCH INBEGRIFFEN
		if(@$_REQUEST['Wagengattung'] == "")
		{
			$NHM_Code = (int)$NHM_Code;
			$result=$db->sql_query("SELECT UIC_Wagengattung FROM nhm WHERE NHM_Code = '$NHM_Code';");
			$row=$db->sql_fetchrow($result);
			$Wagengattung = $row['UIC_Wagengattung'];
		}
		if($FYP_ID!="")
		{
			$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID
			SET fyp.NHM_Code='$NHM_Code', fyp.Wagengattung='$Wagengattung', fyp.Produktbeschreibung='$Produktbeschreibung',
			fyp.Product_Description='$Product_Description',fyp.Anschliesser='$Anschliesser',fyp.FYP_Bem='$FYP_Bem',fyp.Ladestelle='$Ladestelle',
			fyp.Gleis_ID='$Gleis_ID',fyp.Wagen_Woche='$Wagen_Woche',fyp.Betriebsstelle='$Betriebsstelle',fyp.Bhf_ID='$Bhf_ID'
			WHERE manage.MUser_ID = '$loggedInUser->user_id' and fyp.id_fyp = ".$FYP_ID);
echo sql_error();
		}
		else @$_REQUEST['Add']="";
	}

	if(@$_REQUEST['Produktbeschreibung']!="" and @$_REQUEST['Betriebsstelle']!="" and isset($_REQUEST['Add']))
	{
		$NHM_Code = preg_replace('/[^0-9]/', '', $NHM_Code);
		if($NHM_Code=="") $NHM_Code = "99000000"; // GÜTER, ANDERWEITIG WEDER GENANNT NOCH INBEGRIFFEN
		if(@$_REQUEST['Wagengattung'] == "")
		{
			$NHM_Code = (int)$NHM_Code;
			$result=$db->sql_query("SELECT UIC_Wagengattung FROM nhm WHERE NHM_Code = '$NHM_Code';");
			$row=$db->sql_fetchrow($result);
			$Wagengattung = $row['UIC_Wagengattung'];
		}
//		$check=$db->sql_fetchrow($db->sql_query("SELECT NHM_Code FROM fyp WHERE NHM_Code = '".$NHM_Code."' AND Produktbeschreibung = '".$Produktbeschreibung."' AND Anschliesser = '".$Anschliesser."' AND Ladestelle = '".$Ladestelle."' AND FYP_Bem = '".$FYP_Bem."' AND Betriebsstelle = '".$Betriebsstelle."' "));
//		if($check['NHM_Code']=="")
		{
			$db->sql_query("INSERT INTO fyp (NHM_Code, Wagengattung, Produktbeschreibung, Product_Description,
						Anschliesser, FYP_Bem, Ladestelle, Gleis_ID, Wagen_Woche, Betriebsstelle, Bhf_ID)
						VALUES('$NHM_Code', '$Wagengattung', '$Produktbeschreibung', '$Product_Description',
						'$Anschliesser', '$FYP_Bem', '$Ladestelle', '$Gleis_ID', '$Wagen_Woche', '$Betriebsstelle', '$Bhf_ID');");
			$_SESSION['FYP_ID'] = $db->sql_nextid();
		}
	}

	if(@$_REQUEST['action']=="delete")
	{
		$db->sql_query("DELETE fyp FROM fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID
		WHERE manage.MUser_ID = '$loggedInUser->user_id' and id_fyp=".round($_REQUEST['FYP_ID']));
echo sql_error();
		$_SESSION['Fz_ID']="";
	}


	if($Product_Description=="") $_SESSION['translate'] = $_SESSION['Produktbeschreibung'];
	if(isset($_REQUEST['translate']) or $Product_Description=="")
	{
		if($lang=="EN") $Product_Description = $Produktbeschreibung;
		else
		{
			$Produktbeschreibung = ucfirst(getVariableFromQueryStringOrSession('translate'));
			$Product_Description = ucfirst(translates(rawurlencode($Produktbeschreibung), strtolower($lang),'')); // googletranslate
			//$Product_Description = ucfirst(translategoogle(rawurlencode($Produktbeschreibung), strtolower($lang)));

			//if($Product_Description=="") // try translating from polish site
			//$Product_Description = ucfirst(translatepl(rawurlencode($Produktbeschreibung), strtolower($lang)));

			unset($_REQUEST['translate']);

			if((strtolower($Produktbeschreibung)==strtolower($Product_Description) or $Product_Description=="")) { // search in fyp database if nothing is translated
				$row=$db->sql_fetchrow($db->sql_query("SELECT Product_Description FROM fyp
				WHERE Produktbeschreibung = '".escape($Produktbeschreibung)."'"));
				$Product_Description = ucfirst($row['Product_Description']); }

//		echo "Yes".$Produktbeschreibung.$row['Product_Description'];
		}
	}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<title>Yellow Pages</title>
</head>

<body bgcolor='lightyellow' >

<?php selectBack('970',$lang)?>

<div style='position:absolute; top:10px; left:160'>
	<?php selectLanguage($languages[$lang])?>
</div>

<table style='table-layout:fixed' border='0' cellpadding='0' cellspacing='0' >
	<tr>
		<td width='400px'><font size='5'><b><?php echo $TEXT['lang-head']?></font></b></td>
	<tr>
	</tr>
		<td><?php echo $TEXT['lang-subhead']?><br></td>
	</tr>
	<tr>
		<td><font size="4"><?php if(isUserLoggedIn()) echo $TEXT['lang-welcome'] .'<b>'.$loggedInUser->display_username;?></b></font></td>
	</tr>
</table>

<noscript>
	<br><font size='5' color='red'><b>Javascript is disabled,<br> please activate Javascript</b></font>
</noscript>


<?php if(isUserLoggedIn()){?>

	<div style='position:absolute; top:0px; left:572; width:150px; text-align:center; font-size:80%; color:darkred; font-family:Verdana'>
		<img src='img/Adobe_PDF.png' height='64px' onClick="self.location.href='FYP.php?action=getpdf&Betriebsstelle=<?php echo $Betriebsstelle?>
		&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=&lang=<?php echo $lang?>'"><br>
		<?php echo $TEXT['lang-pdf']?>
	</div>
<?php /*
	<div style='position:absolute; top:5px; left:450px; width:32px; text-align:center; font-size:80%; color:darkred; font-family:Verdana'>
		<img src='img/translate.jpg' height='32px'
		onClick="self.location.href='FYP.php?action=getpdf&Betriebsstelle=<?php echo $Betriebsstelle?>&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>
		&Treffen=&lang=<?php echo $lang?>&translate=<?php echo strtolower($lang)?>'"><br>
		<?php echo $TEXT['lang-translate']?>
	</div>
*/?>
	<div style='position:absolute; top:0px; left:275px; width:150px; text-align:center; font-size:80%; color:gray; font-family:Verdana'>
		<img src='img/Upload_or.png' height='64px' onClick="self.location.href='FYP.php?action=rdr'"><br>
		<?php echo $TEXT['lang-rdr']?>
	</div>

	<div style='position:absolute; top:0px; left:420px; width:150px; text-align:center; font-size:80%; color:darkgreen; font-family:Verdana'>
		<img src='img/xls.png' height='64px' onClick="self.location.href='FYP.php?action=getxls&Betriebsstelle=<?php echo $Betriebsstelle?>
		&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=&lang=<?php echo $lang?>'"><br>
		<?php echo $TEXT['lang-xls']?>
	</div>

<?php } ?>


<?php /*
<font size='3' color=orange>Please help translating the FYPages. If you can speak one of the 23 languages used please download <a style='color:darkorange'
href='http://g-zi.de/FYP/lang/FYP_Translations.zip'>FYP_Translations.zip</a> and send the corrected version to <a style='color:darkorange' href='mailto:g.zi@gmx.de'>g.zi@gmx.de</a></font>
<br>
*/?>

<table width='1048px' border='0' cellpadding='0' cellspacing='0' >
	<tr>
		<td colspan='6' height='30px' valign='bottom' >
			<?php $DeinBahnhof=$db->sql_fetchrow($db->sql_query("SELECT Betriebsstelle FROM manage
				WHERE Bhf_ID = '$Bhf_ID' and MUser_ID = '$loggedInUser->user_id'"));

//echo $Bhf_ID." ".$loggedInUser->user_id." ".$DeinBahnhof;

//			if($DeinBahnhof!="") echo "<br>"; //"<font size='4' style=font-family:Arial>".$TEXT['lang-headfyp']."&nbsp<b>".$Betriebsstelle."</font></b>";
//			else ErrorMessage($Bhf_ID, $Betriebsstelle, $Spur, $loggedInUser, $lang)?>
		</td>
	</tr>
	<tr><td bgcolor=yellow colspan=10><img src=img/blank.gif width=1px height=3px></td></tr>
	<tr>
		<?php selectBetriebsstelle($Sp, 'lightyellow', 'orange', $lang, 'rtl', 'bold', '220', $loggedInUser->user_id)?>
		<form action='FYP.php' method='get' >
			<td bgcolor=yellow align='left' valign='middle' width='150px' >
				<?php if(sizeof($Bhf)>1) echo "<input style='width: 255px;height:24px;font-size:12pt;font-weight:bold;background-color:#D0D0FF'
					type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";
				else echo "<input style='width: 255px;height:24px;font-size:12pt;font-weight:bold;background-color:white'
					type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";?>
				<input type='hidden' name='Bhf_ID' value=''>
			</td>
			<td bgcolor=yellow >
				<?php if(sizeof($Bhf)>1) echo "<input style='width: 60px; height:24px; font-size:12pt; font-weight:bold'
					type=text maxlength=10 name=KurzBZ value='$Kbz'>";
				else echo "<input style='width: 60px; height:24px; font-size:12pt; font-weight:bold'
					type=text maxlength=10 name=KurzBZ value='$Kbz'>";?>
			</td>
			<td bgcolor=yellow >
				<input type='image' src='img/key_enter.png'>
			</td>
		</form>

		<?php selectSpur($Spur, $Sp, 'lightyellow', 'orange', $Bhf)?>

		<td bgcolor=yellow width=130 align='left' valign='middle' >

<?php	/*	if(isUserLoggedIn() and $DeinBahnhof!="")
			{
?>				<input type='button' style='background-color:lightgreen' value='<?php echo $TEXT['lang-change']?>'
				onClick="self.location.href='Bahnhof.php?Betriebsstelle=<?php echo $Betriebsstelle?>&Bhf_ID=<?php echo $Bhf_ID?>&Spur=<?php echo $Spur?>'"
<?php		}
			//else echo <br>";
*/ ?>		</td>
		<td bgcolor=yellow width=320>
		</td>
	</tr>
	<tr><td bgcolor=yellow colspan=10><img src=img/blank.gif width=1px height=3px></td></tr>
</table>
</font>

<table border='0' cellpadding='0' cellspacing='0'>
<?php if(isUserLoggedIn())
	{
		if($DeinBahnhof!="")
		{
?>		<form action='FYP.php' method='get' >
			<tr>
				<td colspan='11' >
					<select style='position:relative; margin: 0px 0px -2px 0px;width:1048px;height:18px;font-family:Arial;color:black;border:1px solid;border-color:orange;
					<?php if($NHM_Code=="99000000") echo "background-color:lightblue;";
					elseif($Produktbeschreibung=="" or $NHM_Code=="0") echo "background-color:lightyellow;";
					else echo "background-color:#CFC;"?>font-size:12px' name=NHM_Code onchange='submit()'>
					<option></option>
<?php
					if($Produktbeschreibung!="")
					{
						$result = $db->sql_query("SELECT DISTINCT nhm.*, fyp.*
						FROM fyp LEFT JOIN nhm ON nhm.NHM_Code = fyp.NHM_Code
						WHERE fyp.NHM_Code >0 and
						(fyp.Produktbeschreibung LIKE '%$Produktbeschreibung%' or
						fyp.Product_Description LIKE '%$Produktbeschreibung%' or
						nhm.English LIKE '%$Produktbeschreibung%' or
						nhm.Nederlands LIKE '%$Produktbeschreibung%' or
						nhm.Deutsch LIKE '%$Produktbeschreibung%' or
						nhm.Francais LIKE '%$Produktbeschreibung%' or
						nhm.Italiano LIKE '%$Produktbeschreibung%' or
						nhm.Dansk LIKE '%$Produktbeschreibung%' or
						nhm.Espanol LIKE '%$Produktbeschreibung%' or
						nhm.Polski LIKE '%$Produktbeschreibung%' or
						nhm.Bulgarian LIKE '%$Produktbeschreibung%' or
						nhm.Greek LIKE '%$Produktbeschreibung%' or
						nhm.Czech LIKE '%$Produktbeschreibung%' or
						nhm.Rumanian LIKE '%$Produktbeschreibung%' or
						nhm.Hungarian LIKE '%$Produktbeschreibung%' or
						nhm.Russian LIKE '%$Produktbeschreibung%' or
						nhm.Portuges LIKE '%$Produktbeschreibung%' or
						nhm.Slovak LIKE '%$Produktbeschreibung%' or
						nhm.Slovene LIKE '%$Produktbeschreibung%' or
						nhm.Svenska LIKE '%$Produktbeschreibung%' or
						nhm.Estonian LIKE '%$Produktbeschreibung%' or
						nhm.Suomeksi LIKE '%$Produktbeschreibung%' or
						nhm.Latvian LIKE '%$Produktbeschreibung%' or
						nhm.Lithuanian LIKE '%$Produktbeschreibung%')
						ORDER BY nhm.NHM_Code");
echo sql_error();
					}
					$i=0;
					$NHM_ID_="";
					while($row=$db->sql_fetchrow($result))
					{
						$row_NHM_Code = $row['NHM_Code'];
						if($row_NHM_Code != $NHM_ID_)
						{
							echo"<option value='$row_NHM_Code'>".$row_NHM_Code." ".$row[$languages[$lang]]."</option><b>";
							//if($Product_Description=="") echo "<input type='hidden' name='Product_Description' value='".$row[Product_Description]."' >";
						}
						$NHM_ID_ = $row_NHM_Code;
						$i++;
					}
					echo"</select>";?>
				</td>
			</tr>
			<tr>
<?php			echo "<div style='position:relative; margin: 0px 0 -18px 0px;z-index:1;width:290px;font-family:Arial;font-size:14px' >";
//				if($NHM_Code!="99000000") {echo"<A style='text-decoration:none' HREF='http://agp.dbcargo.com/agp/nhmsuche_code.jsp?
//					searchtype=CODE&lang=$lang&searchparam=$NHM_Code' target=_blank><b>&nbsp;&nbsp;".$TEXT['lang-nhmsearch']."</A>&nbsp(0 = ".$TEXT['lang-in'].")</b></A></td>";}
				if($Produktbeschreibung!="") {echo"<A HREF='http://agp.dbcargo.com/agp/pages/nhmsuche_code.jsp?
					searchtype=TEXT&lang=$lang&searchparam=$Produktbeschreibung&searchmatch=normal' target=_blank>".$TEXT['lang-nhmsearch']."</A>&nbsp(0 = ".$TEXT['lang-in'].")</A></td>";}
//				else {echo"<A style='text-decoration:none' HREF='http://agp.myrailion.com/agp/NHMSuche?searchtype=FULL&lang=$lang'
				else {echo"<A style='text-decoration:none' HREF='http://agp.dbcargo.com/agp/pages/nhmsuche.jsp?lang=$lang'
					target=_blank><b>&nbsp;&nbsp;".$TEXT['lang-nhmsearch']."</A>&nbsp(0 = ".$TEXT['lang-in'].")</b></A></td>";}
?>			</tr>
		</form>
		<form action='FYP.php' method='get' >
			<th>
				<input type='hidden' name='Betriebsstelle' value='<?php echo $Betriebsstelle?>' >
				<input type='hidden' name='Bhf_ID' value='<?php echo $Bhf_ID?>' >
				<input type='hidden' name='Spur' value='<?php echo $Spur?>' >
				<input type='hidden' name='Treffen' value='<?php echo $Treffen?>' >
				<input type='hidden' name='lang' value='<?php echo $lang?>' >

				<td><img src='img/blank.gif' style='width:2px' ></td>
				<td><input type='text' style='width:90px;font-size:14px' maxlength='10' name='NHM_Code' value='<?php echo $NHM_Code?>' ></td>
				<td><input type='text' style='width:100px;font-size:14px' maxlength='50' name='Wagengattung' value='<?php echo $Wagengattung?>' ></td>
				<td><input type='text' style='width:200px;font-size:14px' maxlength='100' name='Produktbeschreibung' id='pbs_up' value='<?php echo $Produktbeschreibung?>' ></td>
				<td><input type='text' style='width:200px;font-size:14px' maxlength='100' name='Product_Description' value='<?php echo $Product_Description?>' ></td>
				<td><input type='text' style='width:150px;font-size:14px' maxlength='100' name='Anschliesser' value='<?php echo $Anschliesser?>' ></td>
				<td>
<?php				if(strstr($_SERVER["HTTP_USER_AGENT"], "IE"))
						echo "<input type='text' style='width:200px;font-size:14px' maxlength='100' name='FYP_Bem' value='".$FYP_Bem."' >";
					else
					{
						echo "<div style='position:relative; margin: -10px 0 0 0px'>";
						echo "<input type='text' style='width:200px;font-size:12px' maxlength='100' name='FYP_Bem' value='".$FYP_Bem."' >";
						echo "</div>";
					}
					echo "<div style='position:relative; margin: -44px 0 0 0px'>";
						echo "<select style='width:200px;background-color:snow;border:1px solid;border-color:orange;font-size:14px' name='Gleis_ID'>";
						$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM gleise WHERE id = '$Gleis_ID'"));
						if($row['Gleisname']!="")
						{ 
							echo "<option value='$row[id]'>".$row['Ladestelle']."  (".$TEXT['lang-track']." ".$row['Gleisname'].")</option><b>";
							echo "<option></option>";
						}
						else echo "<option >-> ".$TEXT['lang-ls']." (".$TEXT['lang-track'].")</option>";
						
						$result=$db->sql_query("SELECT * FROM gleise WHERE Bhf_ID = '$Bhf_ID' ORDER BY Ladestelle, Gleisname");
						$i=0;
						while( $row=$db->sql_fetchrow($result) )
						{
							echo "<option value='$row[id]'>".$row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")</option><b>";
							$i++;
						}
						echo "</select>";
?>					</div>
				</td>
				<td><input type='text' style='width:35px;font-size:14px' maxlength='3' name='Wagen_Woche' value='<?php echo $Wagen_Woche?>' ></td>
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
			</th>
		</form>
	</table>
<?php } } ?>

<table style='table-layout:fixed' width='1048' border='0' cellpadding='0' cellspacing='0' >
<colgroup>
	<col width='15px' >
	<col width='82px' >
	<col width='100px' >
	<col width='200px' >
	<col width='200px' >
	<col width='150px' >
	<col width='200px' >
	<col width='33px' >
	<col width='35px' >
	<col width='35px' >
</colgroup>

<tr bgcolor=yellow style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold' >
	<td><img src=img/blank.gif><input type="checkbox" <?php if($nhm=="y") echo "checked onClick=self.location.href='FYP.php?nhm=n&Bhf_ID=$Bhf_ID&lang=$lang'"?> <?php if($nhm!="y") echo"onClick=self.location.href='FYP.php?nhm=y&Bhf_ID=$Bhf_ID'" ?> ></td>
	<td class='tabhead'><img src='img/blank.gif' ><br>&nbsp<?php echo $TEXT['lang-nhm']?></td>
	<td class='tabhead'><img src='img/blank.gif' ><br><b><?php echo $TEXT['lang-class']?></b><font size=-1> (UIC)</font></td>
	<td class='tabhead'><img src='img/blank.gif' ><br>
		<input type="radio" <?php if($sort=="fyp.Produktbeschreibung, fyp.Betriebsstelle, fyp.Anschliesser, fyp.Ladestelle")echo "checked"?>
		onClick=self.location.href='FYP.php?sort=prd&Bhf_ID=<?php echo $Bhf_ID?>'>&nbsp<b><?php echo $TEXT['lang-product']?></b></td>
	<td class='tabhead'><img src='img/blank.gif' ><br>
		<input type="radio" <?php if($sort=="fyp.Product_Description, fyp.Betriebsstelle, fyp.Anschliesser, fyp.Ladestelle")echo "checked"?>
		onClick=self.location.href='FYP.php?sort=int&Bhf_ID=<?php echo $Bhf_ID?>'>&nbsp<b><?php echo $TEXT['lang-int']?>&nbsp</b>

<?php	if($DeinBahnhof!="") { ?>
			<img border='0' height='20px' width='30px' valign='bottom' src='img/unionjack.gif' alt=<?php echo $TEXT['lang-save']?>
			onclick="self.location.href='FYP.php?translate='+document.getElementById('pbs_up').value;">

			<img border='0' height='20px' valign='bottom' src='img/up.png' alt=<?php echo $TEXT['lang-save']?>
			onclick="self.location.href='FYP.php?translate='+document.getElementById('pbs_up').value;">
<?php 	} ?>

	</td>
	<td class='tabhead'><img src='img/blank.gif' ><br>
		<input type="radio" <?php if($sort=="fyp.Anschliesser, fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Ladestelle")echo "checked"?>
		onClick=self.location.href='FYP.php?sort=an&Bhf_ID=<?php echo $Bhf_ID?>'>&nbsp<b><?php echo $TEXT['lang-an']?></b></td>
	<td class='tabhead'><img src='img/blank.gif' ><br>
		<input type="radio" <?php if($sort=="fyp.Ladestelle, fyp.FYP_Bem, fyp.Betriebsstelle, fyp.Produktbeschreibung, fyp.Anschliesser")echo "checked"?>
		onClick=self.location.href='FYP.php?sort=rem&Bhf_ID=<?php echo $Bhf_ID?>'>&nbsp<b><?php echo $TEXT['lang-ls']?> / <?php echo $TEXT['lang-rem']?></b></td>
	<td colspan='3' class='tabhead' ><img src='img/blank.gif' ><br><?php echo $TEXT['lang-wagon']." ".$TEXT['lang-week']?></td>
</tr>

<?php
	if($Betriebsstelle=="")$Betriebsstelle=" ";
	if($_SESSION['sort']=="prd") $Prod='<b>';
	if($_SESSION['sort']=="int") $Int='<b>';
	if($_SESSION['sort']=="an") $An='<b>';
	if($_SESSION['sort']=="rem") $Bem='<b>';
	$result = getTableContents('', $Betriebsstelle, $Spur, '> 0', $sort);

//echo "D=".$DeinBahnhof." B=".$Betriebsstelle." ".$Spur." ".$row=$db->sql_fetchrow($result) ;
/*if(mysqli_num_rows($result) == 0)
{
	$result=$db->sql_query("SELECT DISTINCT fyp.*, nhm.* FROM fyp LEFT JOIN nhm ON fyp.NHM_Code = nhm.NHM_Code
	WHERE fyp.Betriebsstelle = '".escape($Betriebsstelle)."'");
	if(mysqli_num_rows($result) != 0)
	{
		$_SESSION['FYP'] = $Betriebsstelle;
	}
}
*/
	$i=0;
	$farbe = 0;
	while($row=$db->sql_fetchrow($result))
	{
		if($i>0)
		{
			echo "<tr valign=bottom>";
			echo "<td bgcolor=orange colspan=10><img src=img/blank.gif width=1px height=1px></td>";
			echo "</tr>";
		}

		if($FYP_ID==$row['id_fyp']) echo "<tr valign=center bgcolor=lightgreen >";
		else echo "<tr style='background-color:".($farbe?'#FFFFBF':'lightyellow')."'valign=center >";

		echo "<td colspan='2' align='right' ><font color='gray'><b>".sprintf("%04s",substr($row['NHM_Code'],0,-4)." "
		.substr($row['NHM_Code'],-4))."&nbsp;&nbsp;&nbsp;</font></b></td>";
		echo "<td>".$row['Wagengattung']."&nbsp;</td>";
		echo "<td>".$Prod.$row['Produktbeschreibung']."&nbsp;</b></td>";
		echo "<td>".$Int.$row['Product_Description']."&nbsp;</b></td>";
		echo "<td>".$An.$row['Anschliesser']."&nbsp;</b></td>";
		$trenner=" / "; if($row['Ladestelle']=="" or $row[5]=="") $trenner="";
		if($row['Gleisname']!="") echo "<td class=tabval>".$Bem.$row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")".$trenner.$row[5]."&nbsp;</b></td>";
		else echo "<td>".$Bem.$row['Ladestelle'].$trenner.$row[5]."&nbsp;</b></td>";
		echo "<td>".$row['Wagen_Woche']."&nbsp;</td>";

		if(isUserLoggedIn())
		{
			if($DeinBahnhof!="")
			{
				echo "<td align=middle ><a onclick=\"return\"href=FYP.php?action=edit&FYP_ID=".
					$row['id_fyp']."><img style='height:18px;border:0px' src='img/edit.png'></a></td>";
				if($row['Anschliesser'] != "") $PrAn_ = ' '.$TEXT['lang-von'].$row['Anschliesser'];
				echo "<td align=middle><a onclick=\"return confirm('".$row['Produktbeschreibung'].$PrAn_.' \n'.$TEXT['lang-del']."?');\"
				href=FYP.php?action=delete&FYP_ID=".$row['id_fyp']."&Betriebsstelle=".$Betriebsstelle.">
				<img style='height:18px;border:0px' src='img/del.png'></a></td>";
			}
			else
			{
				echo "<td align=middle ></td>";
				echo "<td align=middle ></td>";
			}
			if($row['Bhf_ID']==0 and $Bhf_ID!=0)
			{
				$db->sql_query("UPDATE fyp SET Bhf_ID = ".round($Bhf_ID)." WHERE id_fyp = ".round($row['id']));
			}
		}
		echo "</tr>";
		if($FYP_ID==$row['id_fyp']) echo "<tr valign=center bgcolor=lightgreen >";
		else echo "<tr valign=center >";
		if($lang=="NO") {if($nhm=="y") echo "<td></td><td colspan='10' style='font-family:Arial;font-size:10pt;color:darkblue' >".$row[$languages["EN"]]."</td></tr>";}
		else {if($nhm=="y") echo "<td></td><td colspan='10' style='font-family:Arial;font-size:10pt;color:darkblue' >".$row[$languages[$lang]]."</td></tr>";}
		$farbe = !$farbe;
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor=yellow colspan=10><img src=img/blank.gif width=1px height=6px></td></tr>";
	$result = getTableContents('', $Betriebsstelle, $Spur, '= 0', $sort);

	$i=0;
	$farbe = 0;
	while($row = $db->sql_fetchrow($result))
	{
		if($i>0)
		{
			echo "<tr valign=bottom>";
			echo "<td bgcolor=orange colspan=10><img src=img/blank.gif width=1px height=1px></td>";
			echo "</tr>";
		}

		if($FYP_ID==$row['id_fyp']) echo "<tr bgcolor=lightgreen valign=center>";
		else echo "<tr style='background-color:".($farbe?'#DFDFDF':'#EFEFEF')."'valign=center>";

		echo "<td><img src=img/blank.gif width=10px height=20px></td>";
		echo "<td align='center'><font color='darkgreen'><b>".$TEXT['lang-in']."</b></font>";
		echo "<td>".$row['Wagengattung']."&nbsp;</td>";
		echo "<td>".$Prod.$row['Produktbeschreibung']."&nbsp;</b></td>";
		echo "<td>".$Int.$row['Product_Description']."&nbsp;</b></td>";
		echo "<td>".$An.$row['Anschliesser']."&nbsp;</b></td>";
		$trenner=" / "; if($row['Ladestelle']=="" or $row['FYP_Bem']=="") $trenner="";
		if($row['Gleisname']!="") echo "<td>".$Bem.$row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")".$trenner.$row[5]."&nbsp;</b></td>";
		else echo "<td>".$Bem.$row['Ladestelle'].$trenner.$row[5]."&nbsp;</b></td>";
		echo "<td>".$row['Wagen_Woche']."&nbsp;</td>";

		if(isUserLoggedIn())
		{
			if($DeinBahnhof!="")
			{
				echo "<td align=middle ><a onclick=\"return\"href=FYP.php?action=edit&FYP_ID="
					.$row['id_fyp']."><img style='height:18px;border:0px' src='img/edit.png'></a></td>";
				if($row['Anschliesser'] != "") $PrAn_ = ' '.$TEXT['lang-fuer'].$row['Anschliesser'];
				echo "<td align=middle><a onclick=\"return confirm('".$row['Produktbeschreibung'].$PrAn_.'\n'.$TEXT['lang-del']."?');\"
				href=FYP.php?action=delete&FYP_ID=".$row['id_fyp']."&Betriebsstelle=".$Betriebsstelle.">
				<img style='height:18px;border:0px' src='img/del.png'></a></td>";
			}
			else
			{
				echo "<td align=middle ></td>";
				echo "<td align=middle ></td>";
			}
			if($row['Bhf_ID']==0 and $Bhf_ID!=0)
			{
				$db->sql_query("UPDATE fyp SET Bhf_ID = ".round($Bhf_ID)." WHERE id_fyp = ".round($row['id_fyp']));
			}
		}
		$farbe = !$farbe;
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor=yellow colspan=10><img src=img/blank.gif width=1px height=6px></td></tr>";
	echo "</Table>";

	if(isUserLoggedIn())
	{
		if($DeinBahnhof!="")
		{
?>			<table align='left' border='0' cellpadding='0' cellspacing='0' >
			<h2><?php echo $TEXT['lang-newgoods']?></h2>
			<form>
				<input type='hidden' name='Betriebsstelle' value='<?php echo $Betriebsstelle?>'>
				<input type='hidden' name='Bhf_ID' value='<?php echo $Bhf_ID?>'>
				<input type='hidden' name='Spur' value='<?php echo $Spur?>'>
				<input type='hidden' name='Treffen' value='<?php echo $Treffen?>'>
				<input type='hidden' name='lang' value='<?php echo $lang?>'>
				<tr>
					<td align='right' width='160px' ><?php echo $TEXT['lang-nhm']?>:&nbsp</td><td><input type='text' size='10' maxlength='10' name='NHM_Code' value='<?php echo $NHM_Code?>'>
<?php				$result = $db->sql_query("SELECT * FROM bahnhof WHERE id = '$Bhf_ID' ");
					$row = $db->sql_fetchrow($result);
					if($Bhf_ID != "") $Spur = $row['Spur'];
					$Kurzbezeichnung = $row['Kurzbezeichnung'];
					$Bahnverwaltung = $row['Bahnverwaltung'];
					$Besitzer = $row['Besitzer'];
					$Email = $row['Email'];
					$Art = $row['Art'];
					$Bhf_Bem = $row['Bhf_Bem'];

					if($NHM_Code!="")
					{	echo"<A HREF='http://agp.dbcargo.com/agp/pages/nhmsuche_code.jsp?searchtype=CODE&lang=$lang
						&searchparam=$NHM_Code' target=_blank>".$TEXT['lang-nhmsearch']."</A>&nbsp(0 = ".$TEXT['lang-in'].")</td>";}
					elseif($Produktbeschreibung!="")
					{	echo"<A HREF='http://agp.dbcargo.com/agp/pages/nhmsuche_code.jsp?searchtype=TEXT&lang=$lang
						&searchparam=$Produktbeschreibung&searchmatch=normal' target=_blank>".$TEXT['lang-nhmsearch']."</A>&nbsp(0 = ".$TEXT['lang-in'].")</td>";}
					else
					{	echo"<A HREF='http://agp.dbcargo.com/agp/NHMSuche?searchtype=FULL&lang=$lang' target=_blank>".$TEXT['lang-nhmsearch']."</A>&nbsp(0 = ".$TEXT['lang-in'].")</td>";}
?>				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-class']?>:&nbsp</td><td>
					<input style='width:200px' type='text' size='30' maxlength='50' name='Wagengattung' value='<?php echo $Wagengattung?>' ></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-product']?>:&nbsp</td><td>
						<input style='width:200px' type='text' size='100' maxlength='50' name='Produktbeschreibung' id='pbs_dn' value='<?php echo $Produktbeschreibung?>' >
					</td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-int']?>:&nbsp
						<img border='0' height='20px' width='30px' valign='bottom' src='img/unionjack.gif' alt=<?php echo $TEXT['lang-save']?>
						onclick="self.location.href='FYP.php?translate='+document.getElementById('pbs_dn').value;">
						<img border='0' height='20px' valign='bottom' src='img/right.png' alt=<?php echo $TEXT['lang-save']?>
						onclick="self.location.href='FYP.php?translate='+document.getElementById('pbs_dn').value;">
					</td>
					<td><input style='width:200px' type='text' size='100' maxlength='50' name='Product_Description' value='<?php echo $Product_Description?>' ></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-an']?>:&nbsp</td><td>
					<input type='text' style='width:200px' size='30' maxlength='50' name='Anschliesser' value='<?php echo $Anschliesser?>' ></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-ls']." (".$TEXT['lang-track'].")" ?>:&nbsp</td><td>
<?php
					echo"<select style='width:200px' name='Gleis_ID'>";
					$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM gleise WHERE id = '$Gleis_ID'"));
					if($row['Gleisname']!="") echo"<option value='$row[id]'>".$row['Ladestelle']."  (".$TEXT['lang-track']." ".$row['Gleisname'].")</option><b>";
					echo"<option value=''></option>";
					$result=$db->sql_query("SELECT * FROM gleise WHERE Bhf_ID = '$Bhf_ID' ORDER BY Ladestelle, Gleisname");
					$i=0;
					while( $row=$db->sql_fetchrow($result) )
					{
						echo"<option value='$row[id]'>".$row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")</option><b>";
						$i++;
					}
					echo"</select>";
?>				</tr>
				<tr>
					<td align='right'><?php echo $TEXT['lang-rem']?>:&nbsp</td><td>
					<input style='width:200px' type='text' size='30' maxlength='100' name='FYP_Bem' value='<?php echo $FYP_Bem?>' ></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-wagon']?>:&nbsp</td><td>
					<input type='text' size='3' maxlength='3' name='Wagen_Woche' value='<?php echo $Wagen_Woche?>' > <?php echo $TEXT['lang-week']?></td>
				</tr>
				<tr>
				<td>
				</td>
				<td>
					<button type='submit' name='Change' style='width:80px' >
						<img height='22px' border='0' src='img/ok.png' alt=<?php echo $TEXT['lang-save']?>><br>
						<b><?php echo $TEXT['lang-save']?></b>
					</button>
					<button type='submit' name='Add' style='width:80px' >
						<img height='22px' border='0' src='img/addblue.png' alt=<?php echo $TEXT['lang-add']?>><br>
						<b><?php echo $TEXT['lang-add']?></b>
					</button>
				</td>
				</tr>
			</form>
			</table>

			<table align='left' border='0' cellpadding='0' cellspacing='0' >
				<td width='5px' ></td>
			</table>

			<table border=0 cellpadding=0 cellspacing=0>
				<input type=hidden name=Betriebsstelle value='<?php echo $Betriebsstelle?>' >
				<input type=hidden name=Bhf_ID value='<?php echo $Bhf_ID?>' >
				<input type=hidden name=Spur value='<?php echo $Spur?>' >
				<input type=hidden name=Treffen value='<?php echo $Treffen?>' >
				<input type=hidden name=lang value='<?php echo $lang?>' >
				<tr>
					<td width='200px' ></td>
					<td align='left' colspan='4' style='color:red' ><?php if(sizeof($Bhf)==0 and $Bhf_ID==0) echo $TEXT['lang-notbhf']; else echo "<br>"?></td>
				</tr>
				<tr>
					<td align='right' width='200px' ><?php echo $TEXT['lang-station']?>:&nbsp</td>
					<th align='left' width='180px' colspan='2' ><font size='+1' ><?php echo $Betriebsstelle?></font></th>

					<td align='right' ><?php echo $TEXT['lang-group']?>:&nbsp</td>
					<td align='left' width='100px' ><?php echo $Spur?></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-kbz']?>:&nbsp</td><td width='100px' ><b><?php echo $Kurzbezeichnung?></b></td>
					<td align='right' ><?php echo $TEXT['lang-owner']?>:&nbsp</td><td width='180px' ><b><?php echo $Besitzer?></b></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-bvw']?>:&nbsp</td><td><?php echo $Bahnverwaltung?></td>
					<td align='right' ><?php echo $TEXT['lang-email']?>:&nbsp</td><td><b><?php echo $Email?></b></td>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-typ']?>:&nbsp</td><th align='left' colspan='3' ><?php echo $Art_lang?></th></tr>
				</tr>
				<tr>
					<td align='right' ><?php echo $TEXT['lang-rem']?>:&nbsp</td><th align='left' colspan='4' ><?php echo $Bhf_Bem?></th>
				</tr>
				<tr>
				</tr>
				<tr>
					<td></td>
				</tr>
			</table>
<?php } } ?>
<br>
</body>
</html>
