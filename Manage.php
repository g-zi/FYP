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

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Yellow Pages</title>
</head>

<style type="text/css">
table+table {
  margin-top: -1px
}</style>

<body bgcolor="#FFF0F0">

<?php selectBack('770',$lang)?>

<div style='position:absolute; top:10px; left:160'>
	<?php selectLanguage($languages[$lang])?>
</div>

<table border='0' cellpadding='0' cellspacing='0' >
	<tr>
		<td width="320px"><b><font size='5'><?php echo $TEXT['lang-head']?></font><b></td>
		<td><font size='5'><?php echo $TEXT['lang-bvw']?></font></td>
	</tr>
	<tr>
		<td>
			<?php echo $TEXT['lang-subhead']?><br>
			<font size="4"><?php if(isUserLoggedIn()) echo $TEXT['lang-welcome'] .'<b>'.$loggedInUser->display_username;?></b></font>
		</td>
		<td style='font-family:Arial' >
			<input type=button style="background-color:lightyellow;border:1px solid;border-color:red;font-size:14"
			value="<?php echo $TEXT['lang-account']?>" onClick=self.location.href="user/account.php?llang=<?php echo strtolower($lang)?>">
		</td>
	</tr>
</table>

<br>

<font size='4' style=font-family:Arial>
<?php
	if(@$_REQUEST['action']=="delBhf")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE id=".round($_REQUEST['id'])));
		$Betriebsstelle = htmlspecialchars($row['Betriebsstelle']);
		$Kurzbezeichnung = htmlspecialchars($row['Kurzbezeichnung']);
		$Bhf_ID = htmlspecialchars($row['Bhf_ID']);
		$User = htmlspecialchars($row['User']);

		$db->sql_query("DELETE FROM manage WHERE MUser_ID = '$loggedInUser->user_id' and id=".round($_REQUEST['id']));

		if ($db->sql_affectedrows() > 0) echo $TEXT['lang-station']."&nbsp<b>".$Betriebsstelle."</b>&nbsp".$Spur."&nbsp".$TEXT['lang-mgr']."&nbsp".$TEXT['lang-deleted'];
		else {
			$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE Bhf_ID = '$Bhf_ID' and MUser_ID = '$loggedInUser->user_id'"));
			if($row['MUser_ID']==$loggedInUser->user_id and $row['MainMngr_ID']=="0") {
				$db->sql_query("DELETE FROM manage WHERE id =".round($_REQUEST['id']));
				if ($db->sql_affectedrows() > 0) echo $TEXT['lang-station']."&nbsp<b>".$Betriebsstelle."
				</b>&nbsp".$Spur."&nbsp".$TEXT['lang-mgr']."&nbsp<b>".$User."</b>&nbsp".$TEXT['lang-deleted'];
			}
		}
		echo "<br>";
	}
	elseif(@$_REQUEST['action']=="MV_Bhf")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE MUser_ID = '$loggedInUser->user_id' and Bhf_ID = ".round($_REQUEST['Bhf_ID'])));
		$Mitverwalter = ($row['Mitverwalter']? 0 : 1);
		$db->sql_query("UPDATE manage SET Mitverwalter = '$Mitverwalter' WHERE MUser_ID = '$loggedInUser->user_id' and Bhf_ID = ".round($_REQUEST['Bhf_ID']));
		echo "<br>";
	}
	elseif(@$_REQUEST['Betriebsstelle']!="" or @$_REQUEST['Kurzbezeichnung']!="" or @$_REQUEST['Bhf_ID']!="" )
	{
		if(@$_REQUEST['Betriebsstelle']!="" and strtolower($_SESSION['KBZ'])==strtolower($_REQUEST['Kurzbezeichnung'])){
			$row=$db->sql_fetchrow($db->sql_query("SELECT Haltestelle, Kurzbezeichnung, Spur, id FROM bahnhof
			WHERE Haltestelle = '".escape($Betriebsstelle)."' ORDER BY Haltestelle;"));}
		elseif(@$_REQUEST['Kurzbezeichnung']!=""){
			$row=$db->sql_fetchrow($db->sql_query("SELECT Haltestelle, Kurzbezeichnung, Spur, id FROM bahnhof
			WHERE Kurzbezeichnung = '".escape($Kurzbezeichnung)."' ORDER BY Haltestelle;"));}
		elseif(@$_REQUEST['Bhf_ID']!=""){
			$row=$db->sql_fetchrow($db->sql_query("SELECT Haltestelle, Kurzbezeichnung, Spur, id FROM bahnhof
			WHERE id = '".escape($Bhf_ID)."' ORDER BY Haltestelle;"));}

		$_SESSION['KBZ'] = @$_REQUEST['Kurzbezeichnung'];

		if($row=="")
		{
			echo $Betriebsstelle." [".$Kurzbezeichnung."] ".$TEXT['lang-notbhf'];
			$Bhf_ID= '0';
		}
		else
		{
			$Betriebsstelle = htmlspecialchars($row['Haltestelle']);
			$Kurzbezeichnung = htmlspecialchars($row['Kurzbezeichnung']);
			$Bhf_ID= round($row['id']);
			$Spur= htmlspecialchars($row['Spur']);
		}

		$result=$db->sql_query("SELECT * FROM manage WHERE Bhf_ID = '".round($Bhf_ID)."' and MUser_ID = '$loggedInUser->user_id'");
	    if($db->sql_fetchrow($result)!="")
		{
			echo $TEXT['lang-station']."&nbsp<b>".$Betriebsstelle."</b>".$TEXT['lang-exists'];
		}
		else
		{
			$seins = $db->sql_fetchrow($db->sql_query("SELECT * FROM manage WHERE MainMngr_ID = '0' and Bhf_ID = '".round($Bhf_ID)."'"));
		    if($seins=="") $MainMngr_ID = '0';

			if(@$_REQUEST['action']=="add_Bhf" and $seins['Mitverwalter']==0)
			{
				if($seins['User']!="")
				{
					$Mng_Bem = $TEXT['lang-mngt'].$seins['User'];
					$MainMngr_ID = $seins['MUser_ID'];

					$UserEmail = $db->sql_fetchrow($db->sql_query("SELECT Email FROM Users WHERE User_ID = '$seins[MUser_ID]'"));
					$email = $UserEmail['Email'];

					$subject="FYPages - ".$Betriebsstelle;

					$msg .= "Yellow Pages - Infomail \n\n";
					$msg .= "Die Betriebsstelle ".$Betriebsstelle." wird mitverwaltet von ".$loggedInUser->display_username." <".$loggedInUser->email.">\n\n";
					$msg .= "Diese Email ist an Dich versandt worden weil ".$loggedInUser->display_username."\n";
					$msg .= "deine Betriebsstelle ".$Betriebsstelle." in seine Verwaltung übernommen hat.\n\n";
					$msg .= "Solltest Du damit nicht einverstanden sein bitte kontaktiere ".$loggedInUser->display_username." <".$loggedInUser->email.">\n";
					$msg .= "oder Georg Ziegler <g.zi@gmx.de>\n\n";
					$msg .= "Zusätzliche Verwalter können jederzeit über die Bahnverwaltung gelöscht werden.\n\n";
					$msg .= "Viele Grüsse,\n";
					$msg .= "Admin FYPages\n\n\n";

					$msg .= "English version ------------------------------------------------------------------------\n\n";
					$msg .= "Yellow Pages - Infomail \n\n";
					$msg .= "The administration of the trainstation ".$Betriebsstelle." is co-managed by ".$loggedInUser->display_username." <".$loggedInUser->email.">\n\n";
					$msg .= "This email has been sent to you because ".$loggedInUser->display_username."\n";
					$msg .= "has taken your trainstation ".$Betriebsstelle." into his administration.\n\n";
					$msg .= "If you do not agree please contact ".$loggedInUser->display_username." <".$loggedInUser->email.">\n";
					$msg .= "or Georg Ziegler <g.zi@gmx.de>\n\n";
					$msg .= "Additional administrators can be deleted anytime via Manage Train Station.\n\n";
					$msg .= "Best Regards,\n";
					$msg .= "Admin FYPages\n\n\n";

					if($loggedInUser->email != 'g.zi@gmx.de') sendMail($email,$subject,$msg);
				}
				$db->sql_query("INSERT INTO manage (User, MUser_ID, Betriebsstelle, Bhf_ID, MainMngr_ID, Mng_Bem)
				VALUES('$loggedInUser->display_username','$loggedInUser->user_id','$Betriebsstelle','$Bhf_ID','$MainMngr_ID','$Mng_Bem');");
echo sql_error();
			}
			elseif($seins['Mitverwalter']==1) echo $TEXT['lang-station']." <b>".$Betriebsstelle."</b> ".$TEXT['lang-notMV'];
			else ErrorMessage($Bhf_ID, $Betriebsstelle, $Spur, $loggedInUser, $lang);
		}
	}
	else
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT Kurzbezeichnung FROM bahnhof WHERE id = '".round($Bhf_ID)."'"));
		$Kurzbezeichnung = $_SESSION['KBZ'] = $_SESSION['Kurzbezeichnung'] = trim($row['Kurzbezeichnung']);
		echo "<br>";
	}
?>

</font>

<table width="810px" border=0 cellpadding=0 cellspacing=0 bgcolor="#FFC0C0">

	<tr>
		<td colspan=10><img src=img/blank.gif width=1px height=6px></td></tr>
	</tr>

	<tr bgcolor="#FFE0E0">
		<?php selectBetriebsstelle($Sp, '#FFE0E0', 'hotpink', $lang, 'rtl', 'bold', '220', '')?>
		<form action=Manage.php method=get>
			<td>
				<?php if(sizeof($Bhf)>1) echo "<input style='width: 220px;height:24px;font-size:12pt;font-weight:bold;
					background-color:#D0D0FF' type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";
				else echo "<input style='width: 220px;height:24px;font-size:12pt;font-weight:bold;
					background-color:white' type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";?>
			</td>
			<td>
				<?php if(sizeof($Bhf)>1) echo "<input style='width: 60px;height:24px;font-size:12pt;font-weight:bold;
					background-color:#D0D0FF' type=text maxlength=10 name=Kurzbezeichnung value='$Kurzbezeichnung'>";
				else echo "<input style='width: 60px;height:24px;font-size:12pt;font-weight:bold;
					background-color:white' type=text maxlength=50 name=Kurzbezeichnung value='$Kurzbezeichnung'>";?>
			</td>
			<td>
				<input type=image src="img/key_enter.png">
			</td>
		</form>
			<td>
				<?php selectSpur($Spur, $Sp, '#FFE0E0', 'hotpink', $Bhf)?>
			</td>
			<td class='tabhead' align='right' style='width:200px' ><?php if(isUserLoggedIn()){?>
				<input type="button" style="background-color:yellow"
				value="<?php echo $TEXT['lang-add']?>"
				onclick="self.location.href='Manage.php?action=add_Bhf&Bhf_ID=<?php echo $Bhf_ID?>';"/><?php } ?>
			</td>
	</tr>
</table>

<table width="810px" border='0' cellpadding='0' cellspacing='0' >
<tr bgcolor="#FFC0C0">
	<td><img src='img/blank.gif' width='10px' ></td>
	<td class='tabhead' style='font-family:Arial' ><img src='img/blank.gif' width='180px' height='6px' ><br><b><?php echo $TEXT['lang-station']?></b></td>
	<td class='tabhead' style='font-family:Arial' ><img src='img/blank.gif' width='80px' height='6px' ><br><b><?php echo $TEXT['lang-group']?></b></td>
	<td class='tabhead' style='font-family:Arial' ><img src='img/blank.gif' width='340px' height='6px' ><br><b><?php echo $TEXT['lang-rem']?></b></td>
	<td class='tabhead' style='font-family:Arial' ><img src='img/blank.gif' width='115px' height='6px' ><br><b><?php echo $TEXT['lang-MV']?></b></td>
	<td class='tabhead' style='font-family:Arial' ><img src='img/blank.gif' width='81px' height='6px' ><br><b></b></td>
</tr>
<tr bgcolor="#FFC0C0">
	<td colspan=10><img src=img/blank.gif width=1px height=3px></td></tr>
</tr>

<?php $result=$db->sql_query(" SELECT manage.*, Users.* FROM manage 
							LEFT JOIN Users ON manage.MainMngr_ID = Users.User_ID 
							WHERE MUser_ID = '$loggedInUser->user_id' ORDER BY Betriebsstelle;");
	$i=0;
	while($row=$db->sql_fetchrow($result))
	{
		if($i>0)
		{
			echo "<tr valign=bottom>";
			echo "<td bgcolor=pink colspan=9><img src=img/blank.gif width=1px height=1px></td>";
			echo "</tr>";
		}

		echo "<tr valign=center>";
		echo "<td class=tabval><img src=img/blank.gif width=10px height=20px></td>";

		echo "<td class=tabval><a onclick=\"\"href=Bahnhof.php?Bhf_ID=".urlencode($row['Bhf_ID'])."&spur=$spur&lang=$lang>
			<span style=color:blue;font-family:Helvetica>".htmlspecialchars($row['Betriebsstelle'])."</span></td>";

		$gruppe=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = ".$row['Bhf_ID'].";"));
		echo "<td class=tabval>".$gruppe['Spur']."&nbsp;</td>";

		if($row['MainMngr_ID']=="0")
		{
			echo "<td class=tabval><b>".$TEXT['lang-mmngt']."</b></td>";
?>			<td class=tabval>
				<input type="button" style="background-color:lightyellow;border:1px solid;border-color:red;font-size:11"
				value="<?php if($row['Mitverwalter']==1) echo $TEXT['lang-no']; else echo $TEXT['lang-yes'];?>"
				onclick="self.location.href='Manage.php?action=MV_Bhf&Bhf_ID=<?php echo urlencode($row['Bhf_ID'])?>';"/>
			</td>
<?php		echo "<td class=tabval align=right><a onclick=\"return confirm('".$TEXT['lang-bvw'].'\n[ '.$row['Betriebsstelle']
				.' ]\n'.$TEXT['lang-del']."');\"
				href=Manage.php?action=delBhf&id=".$row['id']."&Mng_Bem=&lang=$lang>
				<span style=color:brown>".$TEXT['lang-del']."</span></a></td>";
			echo "<td class=tabval></td></tr>";

			$other=$db->sql_query(" SELECT manage.*, Users.* FROM manage 
								LEFT JOIN Users ON manage.MUser_ID = Users.User_ID 
								WHERE Bhf_ID = ".$row['Bhf_ID']." and MUser_ID != '$loggedInUser->user_id' ORDER BY Betriebsstelle;");			
			while($orow=$db->sql_fetchrow($other))
			{
				echo "<tr><td></td><td></td><td></td><td class=tabval >".$TEXT['lang-mngt'].
				"<a href='mailto:".$orow[Email]."?subject=FYPages - ".$TEXT['lang-MV']." ".$orow['Betriebsstelle']."'>".$orow['Username']."</td>";				
				echo "<td class=tabval align=right colspan='2'><a onclick=\"return confirm('".$TEXT['lang-station'].' [ '.$orow['Betriebsstelle']
					.' ]\n'.$TEXT['lang-mgr']." ".$orow['User'].'\n'.strtolower($TEXT['lang-del'])."');\"
					href=Manage.php?action=delBhf&id=".$orow['id']."&Mng_Bem=&lang=$lang>
					<span style=color:brown>".$TEXT['lang-mgr']." ".strtolower($TEXT['lang-del'])."</span></a></td>";
				echo "<td class=tabval></td></tr>";
			}
		}
		else
		{
			echo "<td class=tabval>".$TEXT['lang-mngt']."
			<a href='mailto:".$row[Email]."?subject=FYPages - ".$TEXT['lang-MV']." ".$row['Betriebsstelle']."'>".$row['Username']."</td>";
			echo "<td class=tabval></td>";
			echo "<td class=tabval align=right><a onclick=\"return confirm('".$TEXT['lang-station'].'\n[ '.$row['Betriebsstelle']
				.' ]\n'.$TEXT['lang-del']."');\"
				href=Manage.php?action=delBhf&id=".$row['id']."&Mng_Bem=&lang=$lang>
				<span style=color:brown>".$TEXT['lang-del']."</span></a></td>";
			echo "<td class=tabval></td></tr>";
		}
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor='#FFC0C0' colspan=10><img src=img/blank.gif width=1px height=8px></td></tr>";
	echo "</table>";
?>






<br>








<table border='0' cellpadding='0' cellspacing='0' >
<td colspan=6 style='color:#807040;font-family:Helvetica;font-size:18px'>
<?php
	$Name = getVariableFromQueryStringOrSession('Name');
	$NR = getVariableFromQueryStringOrSession('NR');
	$MdSpur = getVariableFromQueryStringOrSession('MdSpur');

	if(@$_REQUEST['action']=="delMd")
	{
		$row = $db->sql_fetchrow($db->sql_query("SELECT module.*, mngmod.* FROM module
				LEFT JOIN mngmod ON mngmod.Md_ID = module.id_module
				WHERE id=".round($_REQUEST['Mng_ID'])));
		$Name = htmlspecialchars($row['Name']);
		$NR = htmlspecialchars($row['NR']);
		$MdSpur = htmlspecialchars($row['Spur']);
		$Md_ID = htmlspecialchars($row['Md_ID']);
		$User = htmlspecialchars($row['User']);

		$db->sql_query("DELETE FROM mngmod WHERE MUser_ID = '$loggedInUser->user_id' and id =".round($_REQUEST['Mng_ID']));
		if ($db->sql_affectedrows() > 0) echo $TEXT['lang-modul']."&nbsp<b>".$Name.$NR."</b>&nbsp".$MdSpur."&nbsp".$TEXT['lang-mgr']."&nbsp".$TEXT['lang-deleted'];
		else {
			$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM mngmod WHERE Md_ID = '$Md_ID' and MUser_ID = '$loggedInUser->user_id'"));
			if($row['MUser_ID']==$loggedInUser->user_id and $row['ModRem']=="") {
				$db->sql_query("DELETE FROM mngmod WHERE id =".round($_REQUEST['Mng_ID']));
				if ($db->sql_affectedrows() > 0) echo $TEXT['lang-modul']."&nbsp<b>".$Name.$NR."
				</b>&nbsp".$MdSpur."&nbsp".$TEXT['lang-mgr']."&nbsp<b>".$User."</b>&nbsp".$TEXT['lang-deleted'];
			}
		}
	}
	elseif(@$_REQUEST['action']==$TEXT['lang-add'])
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM module WHERE Name = '".$Name."' and NR = '".$NR."' and Spur = '".$MdSpur."'"));

		if($row['id_module']=="") {echo $TEXT['lang-modul']."&nbsp<b>".$Name.$NR."</b>&nbsp".$MdSpur.$TEXT['lang-notfound'];}

		elseif($db->sql_fetchrow($db->sql_query("SELECT * FROM mngmod WHERE User = '$loggedInUser->display_username' and Md_ID = '".round($row[id_module])."'"))=="")
		{
			$seins = $db->sql_fetchrow($db->sql_query("SELECT * FROM mngmod WHERE ModRem = '' and Md_ID = '".round($row[id_module])."'"));
		    if($seins=="") $ModRem = '';

			if($seins['User']!="")
			{
				$ModRem=$TEXT['lang-mngt'].$seins['User'];

				$UserEmail = $db->sql_fetchrow($db->sql_query("SELECT Email FROM Users WHERE Username = '$seins[User]'"));
				$email = $UserEmail['Email'];

				$subject="FYPages - ".$Betriebsstelle;

				$msg .= "Yellow Pages - Infomail \n\n";
				$msg .= "Das Modul ".$Name.$NR." ".$MdSpur." wird mitverwaltet von ".$loggedInUser->display_username." <".$loggedInUser->email.">\n\n";
				$msg .= "Diese Email ist an Dich versandt worden weil ".$loggedInUser->display_username."\n";
				$msg .= "dein Modul ".$Name.$NR." ".$MdSpur." in seine Verwaltung übernommen hat.\n\n";
				$msg .= "Solltest Du damit nicht einverstanden sein bitte kontaktiere ".$loggedInUser->display_username." <".$loggedInUser->email.">\n";
				$msg .= "oder Georg Ziegler <g.zi@gmx.de>\n\n";
				$msg .= "Zusätzliche Verwalter können jederzeit über die Bahnverwaltung gelöscht werden.\n\n";
				$msg .= "Viele Grüsse,\n";
				$msg .= "Admin FYPages\n\n\n";

				$msg .= "English version ------------------------------------------------------------------------\n\n";
				$msg .= "Yellow Pages - Infomail \n\n";
				$msg .= "The administration of the module ".$Name.$NR." ".$MdSpur." is co-managed by ".$loggedInUser->display_username." <".$loggedInUser->email.">\n\n";
				$msg .= "This email has been sent to you because ".$loggedInUser->display_username."\n";
				$msg .= "has taken your module ".$Name.$NR." ".$MdSpur." into his administration.\n\n";
				$msg .= "If you do not agree please contact ".$loggedInUser->display_username." <".$loggedInUser->email.">\n";
				$msg .= "or Georg Ziegler <g.zi@gmx.de>\n\n";
				$msg .= "Additional administrators can be deleted anytime via Manage Train Station.\n\n";
				$msg .= "Best Regards,\n";
				$msg .= "Admin FYPages\n\n\n";

				if($loggedInUser->email != 'g.zi@gmx.de') sendMail($email,$subject,$msg);
			}
			$Betriebsstelle = escape($Betriebsstelle);

			$db->sql_query("INSERT INTO mngmod (User, MUser_ID, Md_ID, ModRem)
			VALUES('$loggedInUser->display_username','$loggedInUser->user_id','$row[id_module]','$ModRem');");

		echo $TEXT['lang-modul']."&nbsp<b>".$Name.$NR."</b>&nbsp".$MdSpur."&nbsp".$ModRem;
		}
		else echo $TEXT['lang-modul']."&nbsp<b>".$Name.$NR."</b>&nbsp".$MdSpur.$TEXT['lang-exists'];
	}
	elseif(@$_REQUEST['Md_ID'])
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM module WHERE id_module = '".round($_SESSION[Md_ID])."'"));
		$Name = htmlspecialchars($row['Name']);
		$NR = htmlspecialchars($row['NR']);
		$MdSpur = htmlspecialchars($row['Spur']);
	}
	else
	{
		echo "<br>";
	}

/* MODULVERWALTUNG
?>
</td>

<td><img src='img/blank.gif' width='10px' height='15px' ></td>
<tr><td bgcolor='#D0C090' colspan=6 ><img src=img/blank.gif width=1px height=6px></td></tr>

<form action=Manage.php method=get>
<tr bgcolor="#F0E0B0">
	<td class='tabhead' ><input type=text style='width:140px;text-align:right;font-size:18px;background-color:#FFEFBF' name=Name value='<?php echo $Name?>'></td>
	<td class='tabhead' ><input type=text style='width:60px;font-size:18px;background-color:#FFEFBF' name=NR value='<?php echo $NR?>'></td>
	<td class=tabhead align=left >
		<select onchange="this.form.submit()" name="MdSpur" style='width:100px;font-size:16px;font-family:Arial;
			background-color:#FFEFBF; border-color:#807040' value='<?php echo $MdSpur?>'>
			<option value=<?php echo $MdSpur?>><?php echo $MdSpur?></option>
			<option></option>
			<?php selectSpurOption()?>
		</select>
	</td>
	<td class='tabhead' bgcolor="#F0E0B0" style='width:320px;font-family:Arial;font-size:18px'>
		&nbsp<?php echo $TEXT['lang-modul']?>
	</td>
	<td class='tabhead' align='right' style='width:109px'><?php if(isUserLoggedIn()){?>
		<input type=submit style="background-color:yellow" border=0 name=action value="<?php echo $TEXT['lang-add']?>"><?php } ?>
	</td>
</tr>
</form>

<tr bgcolor="#D0C090">
<td style='font-family:Arial;text-align:right' ><b><?php echo $TEXT['lang-name']?>&nbsp/</td>
<td style='font-family:Arial' ><b>&nbsp<?php echo $TEXT['lang-NR']?></b></td>
<td style='font-family:Arial' ><b><?php echo $TEXT['lang-group']?></b></td>
<td style='font-family:Arial' ><b><?php echo $TEXT['lang-rem']?></b></td>
<td><img src='img/blank.gif' width='10px' height='25px' ></td>
</tr>

<?php

$result=$db->sql_query("SELECT module.*, bahnhof.Haltestelle, bahnhof.Kurzbezeichnung, mngmod.* FROM module
												LEFT JOIN bahnhof ON module.Bhf_ID = bahnhof.id
												LEFT JOIN mngmod ON module.id_module = mngmod.Md_ID
												WHERE MUser_ID = '$loggedInUser->user_id' ORDER BY Name, NR;");
	$i=0;
	while($row=$db->sql_fetchrow($result))
	{
		if($i>0)
		{
			echo "<tr valign=bottom>";
			echo "<td bgcolor='#D0C090' colspan=6><img src=img/blank.gif width=1px height=1px></td>";
			echo "</tr>";
		}

		echo "<tr valign=center>";

		echo "<td class=tabval style='text-align:right' ><a onclick=\"\"href=Module.php?action=edit&Md_ID=".urlencode($row['Md_ID'])."&spur=$spur&lang=$lang>
			<span style=color:blue;font-family:Helvetica>".htmlspecialchars($row['Name'])."</span></td>";

		echo "<td class=tabval><a onclick=\"\"href=Module.php?action=edit&Md_ID=".urlencode($row['Md_ID'])."&spur=$spur&lang=$lang>
			<span style=color:blue;font-family:Helvetica>".htmlspecialchars($row['NR'])."</span></td>";

		echo "<td class=tabval>".$row['Spur']."</td>";

		if($row['ModRem']=="")
		{
			echo "<td class=tabval><b>".$TEXT['lang-mmngt']."</b></td>";
			echo "<td class=tabval align=right><a onclick=\"return confirm('".$TEXT['lang-modul'].'\n[ '.$row['Name'].$row['NR']
				.' ]\n'.$TEXT['lang-del']."');\"
				href=Manage.php?action=delMd&Mng_ID=".$row['id']."&ModRem=&lang=$lang>
				<span style=color:brown>".$TEXT['lang-del']."</span></a></td>";
			echo "<td class=tabval></td></tr>";

			$other=$db->sql_query("SELECT * FROM mngmod WHERE Md_ID = ".$row['Md_ID']." and User != '$loggedInUser->display_username' ORDER BY User;");
			while($orow=$db->sql_fetchrow($other))
			{
				echo "<tr><td></td><td></td><td></td><td class=tabval>".$TEXT['lang-mngt']."<b>".$orow['User']."</b>&nbsp;</td>";
				echo "<td class=tabval align=right><a onclick=\"return confirm('".$TEXT['lang-station'].'\n[ '.$orow['Betriebsstelle']
					.' ]\n'.$TEXT['lang-mgr'].$orow['User']." ".strtolower($TEXT['lang-del'])."');\"
					href=Manage.php?action=delMd&Mng_ID=".$orow['id']."&Mng_Bem=&lang=$lang>
					<span style=color:brown>".$TEXT['lang-mgr']." ".strtolower($TEXT['lang-del'])."</span></a></td>";
				echo "<td class=tabval></td></tr>";
			}
		}
		else
		{
			echo "<td class=tabval>".$row['ModRem']."&nbsp;</td>";
			echo "<td class=tabval align=right><a onclick=\"return confirm('".$TEXT['lang-modul'].'\n[ '.$row['Name'].$row['NR']
				.' ]\n'.$TEXT['lang-del']."');\"
				href=Manage.php?action=delMd&Mng_ID=".$row['id']."&Mng_Bem=&lang=$lang>
				<span style=color:brown>".$TEXT['lang-del']."</span></a></td>";
			echo "<td class=tabval></td></tr>";
		}
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor='#D0C090' colspan=6><img src=img/blank.gif width=1px height=8px></td></tr>";
	echo "</table>";
MODULVERWALTUNG */
?>

</body>
</html>