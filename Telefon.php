<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

	header ('Content-type: text/html; charset=utf-8');
	require_once("user/models/config.php");
	require_once("lib/shared.inc.php");
	require_once("lib/menu.inc.php");
	require_once("SQL/backup.php");

	if(isset($_GET['Treffen'])) $Treffen = htmlspecialchars($_GET['Treffen']);
	if(isset($_GET['id'])) $id = htmlspecialchars($_GET['id']);
	if(isset($_GET['Telefon'])) $Telefon = htmlspecialchars($_GET['Telefon']);

//================================================================================

	$lastkbz='';
	if(@$_REQUEST['action']=="update" and isUserLoggedIn())
	{
		$result=$db->sql_query("SELECT t.*, b.Spur, b.Kurzbezeichnung, b.Art FROM treffen t LEFT JOIN bahnhof b on b.id = t.Bhf_ID WHERE Treffen = '".escape($Treffen)."' ORDER BY Betriebsstelle;");
		$i=0;
		while( $row=$db->sql_fetchrow($result) )			
		{
			if($lastkbz!=$row['Kurzbezeichnung']) $Tel=intval(getVariableFromQueryStringOrSession('T'.$i));
			$db->sql_query("UPDATE treffen SET Telefon = '$Tel' WHERE id = ".$row['id']);
echo sql_error();
			$lastkbz=$row['Kurzbezeichnung'];
			$i++;
		}
		echo "<script type = 'text/javascript'> window.parent.location.reload() </script>";
	}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Yellow Pages</title>
</head>

<script src="lib/inlinepopup/inlinepopup_close.js" type="text/javascript"></script>

<body bgcolor="#F5F5FF">

<form action=Telefon.php method=get>
	<table border=0 cellpadding=0 cellspacing=0>
		<input type=hidden name=action value=update>
		<input type=hidden name=Treffen value="<?php echo $Treffen?>">
		<tr bgcolor=lightblue>
			<td><img src=img/blank.gif width=5px height=25px></td>
			<td class=tabhead align=middle width=40px><img style="background-color:lightblue;height:22px;border:0px" src="img/Telefon.png"></td>
			<td class=tabhead align=middle><img src=img/blank.gif width=50px height=6px><br>Kbz.</td>
			<td class=tabhead><img src=img/blank.gif width=200px height=6px><br><b><?php echo $TEXT['lang-station']?></b></td>
			<td class=tabhead><img src=img/blank.gif width=370px height=6px><br><b><?php echo $TEXT['lang-an']?> / <?php echo $TEXT['lang-rem']?></b></td>
			<td><img src=img/blank.gif width=5px height=25px></td>
		</tr>
<?php
		$result=$db->sql_query("SELECT t.*, b.Spur, b.Kurzbezeichnung, b.Art FROM treffen t LEFT JOIN bahnhof b on b.id = t.Bhf_ID 
		WHERE Treffen = '".escape($Treffen)."' ORDER BY Betriebsstelle;");
		$i=0;
		$lastkbz='';
		$line="2px";
		while( $row=$db->sql_fetchrow($result) )
		{
			if($lastkbz==$row['Kurzbezeichnung'])
			{
				$row['Telefon']='';
				$row['Kurzbezeichnung']='';
				$row['Betriebsstelle']='';
				$line="0px";
			}
			
			if($i>0)
			{
				echo "<tr valign=bottom>";
				echo "<td bgcolor=lightblue colspan=11><img src=img/blank.gif width=1px height=$line></td>";
				echo "</tr>";
			}
	
			echo "<tr valign=center>";
			echo "<td class=tabval><img src=img/blank.gif width=10px height=20px></td>";

//			if(isUserLoggedIn() and $loggedInUser->isGroupMember(2)) // Meeting User
			if(isUserLoggedIn())
			{
				$checkFYP = $db->sql_fetchrow($db->sql_query("SELECT id_fyp FROM fyp WHERE Bhf_ID = '$row[Bhf_ID]'"));
				$checkGleise = $db->sql_fetchrow($db->sql_query("SELECT Gleisname FROM gleise WHERE Bhf_ID = '$row[Bhf_ID]'"));

				if($row['Kurzbezeichnung']=='')
				{
					echo "<td></td><td style='width:40px;height:26px;></td>";
				}
				else
				{
					echo "<td><input type=text name=T".$i." maxlength=3 
							style='width:40px;height:26px;font-size:12pt;font-weight:bold;background-color:white' 
							value=".$row['Telefon']."></td>";
					$lastkbz=$row['Kurzbezeichnung'];
				}
				echo "<td class=tabval align=middle style='font-size:90%; color:black; font-family:Helvetica'>".$row['Kurzbezeichnung']."&nbsp;</td>";
			}
			else
			{
				if($row['Telefon']==0) echo "<td class=tabval align=middle></td>";
				else echo "<td class=tabval align=middle><div style='font-size:90%; color:black; font-family:Helvetica-bold'>".$row['Telefon']."</div></td>";
				echo "<td class=tabval style='font-size:90%; color:black; font-family:Helvetica'>".$row['Kurzbezeichnung']."&nbsp;</td>";	
			}
	
			echo "<td class=tabval>".$row['Betriebsstelle']."&nbsp;</td>";
			$trenner=" / ";
			if($row['Anschliesser']=="" or trim(substr($row['Bemerkung'],strpos($row['Bemerkung'],']')+1))=="") $trenner="";
			echo "<td class=tabval>".$row['Anschliesser'].$trenner.trim(substr($row['Bemerkung'],strpos($row['Bemerkung'],']')+1))."</td>";
			echo "<td class=tabval></td></tr>";
			$i++;
			$line="2px";
		}
		echo "<tr valign=bottom>";
		echo "<td bgcolor=lightblue colspan=12><img src=img/blank.gif width=1px height=8px></td></tr>";
?>
	</table>
	<td>
<?php
//			if(isUserLoggedIn() and $loggedInUser->isGroupMember(2)) // Meeting User
			if(isUserLoggedIn()) echo "<input type=submit value=".$TEXT['lang-save'].">";
?>
	</td>
	<td>
		<input type=button value="<?php echo $TEXT['lang-back']?>" onClick="window.parent.location.reload()">
	</td>
</form>	
</body>
</html>