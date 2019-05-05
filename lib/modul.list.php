<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

	$SortMD = getVariableFromQueryStringOrSession('SortMD');
	$selSpur = getVariableFromQueryStringOrSession('selSpur');
	$Ep = getVariableFromQueryStringOrSession('Ep');
	$Show = getVariableFromQueryStringOrSession('Show');
	$selStatus = getVariableFromQueryStringOrSession('selStatus');
	
	if($_SESSION['SortMD']=="") $_SESSION['SortMD']="Name";
	if($_SESSION['PreSortMD']==$_REQUEST['SortMD'] and $_SESSION['desc']=="") $_SESSION['desc']="DESC"; else $_SESSION['desc']="";
	$_SESSION['PreSortMD']=$SortMD;
	
	if($Modul=="")$Modul=$TEXT['lang-name'];
	if($SortMD=="")$SortMD="Name";
	
	if($selSpur=="") $selSpur = $TEXT['lang-group'];
	if($Ep=="") $EP = $TEXT['lang-group'];	
?>

<table style='table-layout:fixed' width='1024px' border='0' cellpadding='0' cellspacing='0' bgcolor="#F0E0B0" >
	<colgroup>
		<col width='10px'>
		<col width='30px'>
		<col width='120px'>
		<col width='40px'>
		<col width='120px'>
		<col width='100px'>
		<col width='90px'>
		<col width='90px'>
		<col width='160px'>
		<col width='190px'>
		<col width='30px'>
		<col width='30px'>
	</colgroup>

	<tr><td bgcolor='#F0E0B0' colspan=12 ><img src=img/blank.gif width=1px height=20px></td></tr>
	<tr><td bgcolor=brown colspan=12><img src=img/blank.gif width=1px height=4px></td></tr>
	<tr><td bgcolor='#D0C090' colspan=12 ><img src=img/blank.gif width=1px height=4px></td></tr>

	<tr bgcolor='#D0C090'>
		<td class=tabhead rowspan=3 colspan=2 align=left>
			<div style='font-family: Arial Narrow, Arial; font-stretch: condensed; font-size:12px'> 
				&nbsp<br>&nbsp
				<b style="font-size:16px"><?php echo $TEXT['lang-status']?></b><br>
				<img src='img/B.png'>&nbspPDF
				<img src='img/G.png'>&nbspDWG
				<img src='img/V.png'>&nbspPIC
			</div>
		</td>
		<td class=tabhead align=right >
			<input type=button style='background-color:<?php if($SortMD=="Name")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid; border-color: brown; font-size: 18px; font-family: Arial Narrow, Arial; font-stretch: condensed; font-weight:bold' 
			value=<?php echo $TEXT['lang-name']?> onClick=self.location.href='?SortMD=Name'>
		</td>

		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="NR")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid; border-color: brown; font-size: 18px; font-family: Arial Narrow, Arial; font-stretch: condensed; font-weight:bold" 
			value=<?php echo $TEXT['lang-NR']?> onClick=self.location.href='?SortMD=NR'>
		</td>
		
		<form method='get' >
		<td class=tabhead align=left >
			<select onchange="this.form.submit()" name="selSpur" style='width: 100px; font-size: 18px; font-family: Arial Narrow, Arial; font-stretch: condensed; font-weight: bold
				<?php if($selSpur!=$TEXT['lang-group']) echo ";background-color:#F0E0B0"; else echo ";background-color:#D0C090"?>;border-color:brown' value='<?php echo $SortMD?>'>
				<option value=<?php echo $selSpur?>><?php echo $selSpur?></option>
				<option></option>
				<option value='HO' >HO</option>
				<option value='N' >N</option>
				<?php selectSpurOption()?>
			</select>
		</td>
		</form>

		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Endprofil_1")echo "#F0E0B0"; else echo "#D0C090"?>;
			border: 1px solid; border-color: brown; font-size: 18px; font-family: Arial Narrow, Arial; font-stretch: condensed; font-weight: bold" 
			value=<?php echo $TEXT['lang-profile']?> onClick=self.location.href='?SortMD=Endprofil_1'>
		</td>

		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Laenge")echo "#F0E0B0"; else echo "#D0C090"?>;
			border: 1px solid; border-color: brown; font-size: 18px;font-family: Arial Narrow, Arial; font-stretch: condensed; font-weight: bold" 
			value=<?php echo $TEXT['lang-tl']?> onClick=self.location.href='?SortMD=Laenge'>
		</td>
		
		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Winkel")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed;font-weight:bold" 
			value=<?php echo $TEXT['lang-angle']?> onClick=self.location.href='?SortMD=Winkel'>
		</td>
		
		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Zeichnung")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed;font-weight:bold" 
			value=<?php echo $TEXT['lang-drawing']?> onClick=self.location.href='?SortMD=Zeichnung'>
		</td>
		
		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Besitzer")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed;font-weight:bold" 
			value=<?php echo $TEXT['lang-owner']?> onClick=self.location.href='?SortMD=Besitzer'>
		</td>
					
		<td class=tabhead colspan=2 align=left >
			<input type=button style="background-color:<?php if($Show=="All") echo '#F0E0B0'; else echo '#D0C090'?>
			;border:1px solid;border-color:brown;font-size:18px" 
			value=<?php echo $TEXT['lang-all']?><?php if($Show=="All") echo " onClick=self.location.href='?Show='>";
				  else echo " onClick=self.location.href='?Show=All'>"?>
		</td>
	</tr>
	
	<tr bgcolor='#D0C090' style='font-family:Arial Narrow'>
		<td class=tabhead colspan=3 align=center >
			<input type=button style="background-color:<?php if($SortMD=="Rem")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed" 
			value=<?php echo $TEXT['lang-beschreibung']?> onClick=self.location.href='?SortMD=Rem'>
		</td>
		
		<form name='selEndprofile' method='get'>
		<td align='left' valign='middle' bgcolor='#D0C090' width='80px'>
			<select onchange='this.form.submit()' style='<?php if($Ep!="") echo "background-color:#F0E0B0"; else echo "background-color:#D0C090"?>
			;width:80px;border:1px solid;border-color:brown;font-size:14px' name='Ep' value=".$Endprofile.">"
			<?php echo "<option>".$Ep."</option>";
			
			$result=$db->sql_query("SELECT DISTINCT Endprofil FROM (
								 SELECT DISTINCT Endprofil_1 AS Endprofil FROM module
								 UNION SELECT DISTINCT Endprofil_2 AS Endprofil FROM module
								 UNION SELECT DISTINCT Endprofil_3 AS Endprofil FROM module
								 ) AS derived ORDER BY Endprofil");
echo sql_error();
							
			while($row=$db->sql_fetchrow($result))
			{	echo"<option value='$row[Endprofil]'>".$row[Endprofil]."</option><b>";
				$i++;}?>
			</select>
		</td></font>
		</form>

		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Breite")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed" 
			value=<?php echo $TEXT['lang-breite']?> onClick=self.location.href='?SortMD=Breite'>
		</td>
		
		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Radius")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed" 
			value=<?php echo $TEXT['lang-radius']?> onClick=self.location.href='?SortMD=Radius'>
		</td>
		
		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Status")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed" 
			value=<?php echo $TEXT['lang-status']?> onClick=self.location.href='?SortMD=Status'>
		</td>
		
		<td class=tabhead align=left >
			<input type=button style="background-color:<?php if($SortMD=="Email")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed" 
			value=<?php echo $TEXT['lang-email']?> onClick=self.location.href='?SortMD=Email'>
		</td>
		
		<td class=tabhead rowspan=2 align=middle >Edit</td>
		<td class=tabhead rowspan=2 align=middle >Del</td>
	</tr>
	
	<tr bgcolor='#D0C090' style='font-family:Arial Narrow;font-style: italic'>		
		<td class=tabhead colspan=2 align=right >
			<input type=button style="background-color:<?php if($SortMD=="Haltestelle")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed;font-style: italic" 
			value=<?php echo $TEXT['lang-station']?> onClick=self.location.href='?SortMD=Haltestelle'>
		</td>


		<td class=tabhead colspan=2 align=left >
			<input type=button style="background-color:<?php if($SortMD=="Signalschacht")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed;font-style: italic" 
			value=<?php echo $TEXT['lang-signalschacht']?> onClick=self.location.href='?SortMD=Signalschacht'>
		</td>

		
		<td class=tabhead colspan=2 align=left >
			<input type=button style="background-color:<?php if($SortMD=="Bemerkung")echo "#F0E0B0"; else echo "#D0C090"?>;
			border:1px solid;border-color:brown;font-size:18px;font-family: Arial Narrow, Arial; font-stretch: condensed;font-style: italic" 
			value=<?php echo $TEXT['lang-rem']?> onClick=self.location.href='?SortMD=Bemerkung'>
		</td>
		
		<form method='get' >
		<td class=tabhead  colspan=2 align=left >
			<select onchange="this.form.submit()" name="selStatus" style='font-size:14px;font-family:Arial
				<?php if($selStatus=="") echo ";background-color:#D0C090"; else echo ";background-color:#F0E0B0"?>;border-color:brown' value='<?php echo $SortMD?>'>
				<option value=<?php echo $selStatus?>><?php echo $TEXT[$selStatus]?></option>
				<option ></option>
				<option value='lang-ready_for_use' ><?php echo $TEXT['lang-ready_for_use']?></option>
				<option value='lang-in_shell' ><?php echo $TEXT['lang-in_shell']?></option>
				<option value='lang-remeasuring' ><?php echo $TEXT['lang-remeasuring']?></option>
				<option value='lang-missing' ><?php echo $TEXT['lang-missing']?></option>
			</select>
		</td>
		</form>

	</tr>
	<tr><td bgcolor='#D0C090' colspan=12><img src=img/blank.gif width=1px height=4px></td></tr>
	<tr><td bgcolor=brown colspan=12><img src=img/blank.gif width=1px height=2px></td></tr>
<?php
	$farbe = $_SESSION['farbe'];

 
	if($selSpur!=$TEXT['lang-group']) {$whereSpur = "module.Spur LIKE '".escape($selSpur)."%'";$whereAnd=" and ";}
	
	if($Ep!="") {$whereEp = $whereAnd."(module.Endprofil_1 = '".escape($Ep)."' 
				or module.Endprofil_2 = '".escape($Ep)."' 
				or module.Endprofil_3 = '".escape($Ep)."')"; $whereAnd=" and ";}
	
	if($selStatus!="") {$whereStatus = $whereAnd."module.Status = '".$selStatus."'"; $whereAnd=" and ";}

	if($Show=="") {$whereUser = $whereAnd."mngmod.User = '".$loggedInUser->display_username."'";}
	
	if($whereSpur.$whereEp.$whereStatus.$whereUser!="") $where = "WHERE";

		$result=$db->sql_query("SELECT module.*, mngmod.*, bahnhof.Haltestelle, bahnhof.Kurzbezeichnung FROM module 
		LEFT JOIN mngmod ON mngmod.Md_ID = module.id_module 
		LEFT JOIN bahnhof ON bahnhof.id = module.Bhf_ID 
		$where $whereEp $whereSpur $whereStatus $whereUser
		GROUP BY Name, NR, Spur
		ORDER BY ".$SortMD." ".$_SESSION['desc'].", Name, NR;");

echo sql_error();

	$i=0;
	while($row=$db->sql_fetchrow($result))
	{
		if($Md_ID==$row['id_module']) echo "<tr bgcolor=lightgreen ><td colspan=12><img src=img/blank.gif width=1px height=4px></td></tr>";
		else echo "<tr><td colspan=11><img src=img/blank.gif width=1px height=4px></td></tr>";
		if($Md_ID==$row['id_module']) echo "<tr bgcolor=lightgreen >"; else echo "<tr>";
			
		$checkPDF = rawurlencode(pathinfo($row['Zeichnung'], PATHINFO_FILENAME).".pdf");
		$checkDWG = rawurlencode(pathinfo($row['Zeichnung'], PATHINFO_FILENAME).".dwg");
?>
		<td class=tabval rowspan=3 >
			<input type=image src='img/
			<?php if(file_exists('Module/'.$row['Spur'].'/'.$checkPDF)) echo "B"; else echo "W"?>.png'
			onClick=self.location.href="Module/<?php echo $row['Spur']?>/<?php echo $checkPDF?>">
		<img src=img/blank.gif width=1px height=4px>
<?php		if($row['User']==$loggedInUser->display_username and $row['ModRem']=='') {?>
			<input type=image src='img/
			<?php if(file_exists('Module/'.$row['Spur'].'/'.$checkDWG)) echo "G"; else echo "W"?>.png'
			onClick=self.location.href="Module/<?php echo $row['Spur']?>/<?php echo $checkDWG?>"><?php } ?>
		<img src=img/blank.gif width=1px height=4px>
<?php	if($row['User']==$loggedInUser->display_username and $row['ModRem']=='') {?>
			<input type=image src='img/
			<?php if(file_exists('Bilder/pic_Mod_ID_'.$row['id_module'])) echo "V"; else echo "W"?>.png'
			onClick=self.location.href="Bilder/pic_Mod_ID_<?php echo $row['id_module']?>"><?php } ?>
		</td>
<?php		echo "<td colspan=2 style='font-family:Arial;font-weight:bold' align=right >".trim($row['Name'])."</td>";
			echo "<td style='font-family:Arial;font-weight:bold' >".trim($row['NR'])."</td>";
			echo "<td style='font-family:Arial' align='center'>".trim($row['Spur'])."</td>";
			echo "<td style='font-family:Arial;font-weight:bold'>".trim($row['Endprofil_1'])."</td>";
			echo "<td style='font-family:Arial;font-weight:bold'>".trim($row['Laenge'])."</td>";
			echo "<td style='font-family:Arial;font-weight:bold'>".trim($row['Winkel'])."</td>";
			echo "<td style='font-family:Arial Narrow;font-weight:bold'>".trim($row['Zeichnung'])."</td>";
			echo "<td style='font-family:Arial;font-weight:bold'>".trim($row['Besitzer'])."</td>";
			echo "<td rowspan=2 align=middle ><a onclick=\"return\"href=?action=edit&Md_ID=".$row['id_module'].">
				<img style='height:22px;border:0px' src='img/edit.png'></a></td>";
			
			
// Modulzuordnung -> button delete
//echo $row['Name'].$row['User'];

			if($row['User']==$loggedInUser->display_username and $row['ModRem']=='')
			{
				echo "<td rowspan=2 align=middle ><a onclick=\"return confirm('".$TEXT['lang-modul'].'\n[ '.$row['Name'].$row['NR'].' ('.$row['Spur'].')'
				.' ]\n'.$TEXT['lang-del']."');\"
				href=?action=delete&Md_ID=".$row['id_module'].">
				<img style='height:22px;border:0px' src='img/del.png'></a></td>";
			}
			else echo "<td rowspan=2 ></td>";

		echo "</tr>";
		
		if($Md_ID==$row['id_module']) echo "<tr bgcolor=lightgreen >"; else echo "<tr>";

			echo "<td colspan=4 style='font-family:Arial Narrow' align=center >".trim($row['Rem'])."</td>";
			echo "<td style='font-family:Arial' >".trim($row['Endprofil_2'])."</td>";
			echo "<td style='font-family:Arial' >".trim($row['Breite'])."</td>";
			echo "<td style='font-family:Arial' >".trim($row['Radius'])."</td>";
			echo "<td style='font-family:Arial' >".$TEXT[trim($row['Status'])]."</td>";
			echo "<td style='font-family:Arial' >".trim($row['Email'])."</td>";
		echo "</tr>";

		if($Md_ID==$row['id_module']) echo "<tr bgcolor=lightgreen >"; else echo "<tr>";
			echo "<td></td>";
			if ($row['Haltestelle'] == "") {
				echo "<td colspan=2 style='font-family:Arial;font-style: italic' align=right >".$TEXT['lang-route']."</td>"; }
			else {
				echo "<td colspan=2 style='font-family:Arial Narrow;font-style: italic' align=right >".trim($row['Haltestelle'])." [".trim($row['Kurzbezeichnung'])."]</td>"; }
			echo "<td style='font-family:Arial;font-style: italic' align=center >";
			if(trim($row['Signalschacht'])==1) echo $TEXT['lang-yes']."</td>"; else echo $TEXT['lang-no']."</td>";
			echo "<td style='font-family:Arial;font-style:italic' >".trim($row['Endprofil_3'])."</td>";
			echo "<td colspan=6 style='font-family:Arial Narrow;font-style:italic' >".trim($row['Bemerkung'])."</td>";
		echo "</tr>";

		if($Md_ID==$row['id_module']) echo "<tr bgcolor=lightgreen ><td colspan=12><img src=img/blank.gif width=1px height=4px></td></tr>";
		else echo "<tr><td colspan=11><img src=img/blank.gif width=1px height=4px></td></tr>";
		echo "<tr><td bgcolor=gray colspan=12><img src=img/blank.gif width=1px height=1px></td></tr>";

		$farbe = !$farbe;
		$i++;
	}
	echo "<tr><td bgcolor=brown colspan=12 ><img src=img/blank.gif width=1px height=4px></td></tr>";
?>
</table>
