<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

	header ('Content-type: text/html; charset=utf-8');
	require_once("user/models/config.php");
	require_once("lib/shared.inc.php");
	require_once("lib/menu.inc.php");
	require_once("SQL/backup.php");
	
$Fz_ID = getVariableFromQueryStringOrSession('Fz_ID');
if(isset($_REQUEST['Change']))
{
	if($Menge=="0") $Menge='1';
	if($Fz_ID!="")
	{
		$db->sql_query("UPDATE frachtzettel SET Menge='$Menge', Eilgut='$Eilgut', Wenden='$Wenden', Mehrfach='$Mehrfach', Stueckgut='$Stueckgut',
		Zielbahnhof='$Zielbahnhof',Empfaenger='$Empfaenger',Ecol='$Ecol',Gewicht='$Gewicht',Wagengattung='$Wagengattung',Freight='$Freight',
		Ladung='$Ladung',Versandbahnhof='$Versandbahnhof',Versender='$Versender',Vcol='$Vcol',LadeEmpfang='$LadeEmpfang',
		Lcol='$Lcol',ZBV='$ZBV',VBV='$VBV',Treffen='$Treffen',User='$User' WHERE id = ".$Fz_ID);
	echo sql_error();
	}
	else @$_REQUEST['Add']="";
}

if(!isset($_SESSION['farbe'])) $_SESSION['farbe'] = 0;
if(isset($_REQUEST['Add']))
{
	$_SESSION['farbe'] = !$_SESSION['farbe'];
	if($Menge=="0") $Menge='1';
	$db->sql_query("INSERT INTO frachtzettel (Menge, Eilgut, Wenden, Mehrfach, Stueckgut, Zielbahnhof, Empfaenger, Ecol, Gewicht, 
		Wagengattung, Freight, Ladung, Versandbahnhof, Versender, Vcol, LadeEmpfang, Lcol, ZBV, VBV, Treffen, User)
		VALUES('$Menge', '$Eilgut', '$Wenden', '$Mehrfach', '$Stueckgut', '$Zielbahnhof', '$Empfaenger', '$Ecol', '$Gewicht', 
		'$Wagengattung', '$Freight', '$Ladung', '$Versandbahnhof', '$Versender', '$Vcol', '$LadeEmpfang', '$Lcol', '$ZBV', '$VBV', '$Treffen', '$User');");
	$_SESSION['Fz_ID'] = $db->sql_nextid();
echo sql_error();
}
$Fz_ID = getVariableFromQueryStringOrSession('Fz_ID');


?>

<form id='FZliste' action=Frachtzettel.php method=get>
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
				<input type='text' style='width:30px; height:24px; text-align:right; font-size:18px' maxlength='2' name='All' value=<?=$All?>><br>
			</td>
			<td class=tabhead align=middle style='font-family:Arial Narrow;font-weight:bold;background-color:pink' >E</td>
			<td class=tabhead align=middle style='font-family:Arial Narrow;font-weight:bold;background-color:gray' >W</td>
			<td class=tabhead rowspan=2 ></td>
			<td class=tabhead align=left style='font-family:Arial Narrow;font-weight:bold' ><?=$TEXT['lang-Zielbahnhof']?></td>
			<td class=tabhead rowspan=2 align=middle ><img style="background-color:lightblue;height:22px;border:0px" src="img/weight.ico"></td>
			<td class=tabhead rowspan=2 align=middle style='font-family:Arial Narrow' ><?=$TEXT['lang-Wagengattung']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow;font-weight:bold' ><?=$TEXT['lang-Fracht']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow;font-weight:bold' ><?=$TEXT['lang-Versandbahnhof']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow;font-weight:bold' ><?=$TEXT['lang-Ladestelle']?></td>
			<td class=tabhead rowspan=2 align=middle style='font-family:Arial Narrow;font-weight:bold' >Edit</td>
			<td class=tabhead rowspan=2 align=middle style='font-family:Arial Narrow;font-weight:bold' >Del</td>
		</tr>
		<tr bgcolor=lightblue>
			<td class=tabhead rowspan=1 align=middle ><input type=image height='24px' src="img/down.png"><br></td>
			<td class=tabhead align=middle style='font-family:Arial Narrow;font-weight:bold;background-color:white' >M</td>
			<td class=tabhead align=middle style='font-family:Arial Narrow;font-weight:bold;background-color:orange' >S</td>
			<td class=tabhead align=left style='font-family:Arial Narrow' ><?=$TEXT['lang-Empfaenger']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow' >Freight</td>
			<td class=tabhead align=left style='font-family:Arial Narrow' ><?=$TEXT['lang-Versender']?></td>
			<td class=tabhead align=left style='font-family:Arial Narrow' ><?=$TEXT['lang-meet']?></td>
		</tr>
		<tr><td bgcolor=blue colspan=12><img src=img/blank.gif width=1px height=1px></td></tr>
<?
	$farbe = $_SESSION['farbe'];
	$result=$db->sql_query("SELECT * FROM frachtzettel WHERE User = '$loggedInUser->display_username' ORDER BY id DESC;");
	$i=0;
	while( $row=$db->sql_fetchrow($result) )
	{
		if($Fz_ID==$row['id']) echo "<tr bgcolor=lightgreen valign=center >";
		else echo "<tr style='background-color: " . ($farbe ? '#ddd' : '#fff')  . "' valign=center>";
		echo "<td rowspan=2 align=middle ><input type='text' style='width:30px;text-align:right;font-size:18px' maxlength='2' name='M".$i."' value=".$row['Menge']."></div></td>";
		echo "<td align=middle style='background-color:pink' ><input type='checkbox' name='E".$i."'".$row['Eilgut']." ></div></td>";
		echo "<td align=middle style='background-color:gray' ><input type='checkbox' name='W".$i."'".$row['Wenden']." ></div></td>";
		echo "<td rowspan=2></td>";
		echo "<td style='font-family:Arial Narrow;font-weight:bold'>".trim($row['Zielbahnhof'])."</td>";
		echo "<td rowspan=2 style='font-family:Arial Narrow' align=middle ><b>".$row['Gewicht']."</b>t</td>";
		echo "<td rowspan=2 style='font-family:Arial Narrow;font-weight:bold' align=middle >".$row['Wagengattung']."</td>";
		echo "<td style='font-family:Arial Narrow;font-weight:bold'>".trim($row['Ladung'])."</td>";
		echo "<td style='font-family:Arial Narrow;font-weight:bold'>".trim($row['Versandbahnhof'])."</td>";
		echo "<td style='font-family:Arial Narrow;font-weight:bold'>".trim($row['LadeEmpfang'])."</td>";
		echo "<td rowspan=2 align=middle ><a onclick=\"return\"href=Frachtzettel.php?action=edit&Fz_ID=".$row['id']."><img style='height:22px;border:0px' src='img/edit.png'></a></td>";
		echo "<td rowspan=2 align=middle ><a onclick=\"return\"href=Frachtzettel.php?action=delete&Fz_ID=".$row['id']."><img style='height:22px;border:0px' src='img/del.png'></a></td>";
		echo "</tr>";

		if($Fz_ID==$row['id']) echo "<tr bgcolor=lightgreen valign=center >";
		else echo "<tr style='background-color: " . ($farbe ? '#ddd' : '#fff')  . "' valign=center>";
		echo "<td align=middle style='background-color:white'><input type='checkbox' name='N".$i."'".$row['Mehrfach']." ></div></td>";
		echo "<td align=middle style='background-color:orange'><input type='checkbox' name='S".$i."'".$row['Stueckgut']." ></div></td>";
		echo "<td style='font-family:Arial Narrow' >".trim($row['Empfaenger'])."</td>";
		echo "<td style='font-family:Arial Narrow' >".trim($row['Freight'])."</td>";
		echo "<td style='font-family:Arial Narrow' >".trim($row['Versender'])."</td>";
		echo "<td style='font-family:Arial Narrow' >".trim($row['Treffen'])."</td>";
		echo "</tr>";

		echo "<tr><td bgcolor=gray colspan=12><img src=img/blank.gif width=1px height=1px></td></tr>";

		$farbe = !$farbe;
		$i++;
	}
	echo "<tr valign=bottom>";
	echo "<td bgcolor=lightblue colspan=12 ><img src=img/blank.gif width=1px height=8px></td></tr>";
?>
	</table>
	<input type=submit value='<?=$TEXT['lang-save']?>' >
</form>
