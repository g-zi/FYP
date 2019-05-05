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

	if(@$_REQUEST['action']=="update")
	{
		$db->sql_query("UPDATE treffen SET Telefon = '$Telefon' WHERE id = '$id'");
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

<form action=TelNr.php method=get>
	<table border=0 cellpadding=0 cellspacing=0>
		<input type=hidden name=action value=update>
		<td>
			<img style="background-color:#F5F5FF;height:22px;border:0px" src="img/Telefon.png">
		</td>
<?php
		$result=$db->sql_query("SELECT * FROM treffen WHERE id = '$id'");
		$row=$db->sql_fetchrow($result);
		echo "<td><input type=text name=Telefon maxlength=3 style='width:40px;height:26px;font-size:12pt;font-weight:bold;background-color:white' value=".$row['Telefon']."></td>";
		echo "<td width=10px>&nbsp</td>";
		echo "<td align=left width=250px>".$row['Betriebsstelle']."</td>";
?>
	</table>
	<td>
		<input type=hidden name=id value='<?php echo $id?>'>
		<input type=submit value="<?php echo $TEXT['lang-save']?>">
	</td>
</form>
</body>
</html>