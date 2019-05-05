<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP


// FFFFFFF  YY    YY  PPPPPPP    FFFFFFF  YY    YY  PPPPPPP    FFFFFFF  YY    YY  PPPPPPP   
// FF        YY  YY   PP    PP	 FF        YY  YY   PP    PP	 FF        YY  YY   PP    PP	
// FFFFFF     YYYY    PPPPPPP	   FFFFFF     YYYY    PPPPPPP	   FFFFFF     YYYY    PPPPPPP	  
// FF          YY     PP			   FF          YY     PP			   FF          YY     PP			  
// FF          YY     PP			   FF          YY     PP			   FF          YY     PP			  
// FF          YY     PP			   FF          YY     PP			   FF          YY     PP			  

// FYPages Excel input =========================================================================

if(@$_REQUEST['action']=="rdr")
{
	if(!isUserLoggedIn()) { header("Location: user/login.php"); die(); }

	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	set_time_limit(0);
	date_default_timezone_set('Europe/Berlin');

	/** PHPExcel */
	require_once 'lib/PHPExcel.php';

	?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>FYPages data loading</title>
	</head>
	
	<div style='position:absolute; top:0px; left:772; width:60px; text-align:center; font-size:70%; color:black; font-family:Verdana' 
		onClick=self.location.href='<?php echo str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))?>'>
		<img valign='bottom' width='40' border='0' src='img/Back.png'>
		<br><?php echo $TEXT['lang-back']?>
	</div>

	<div style='position:absolute; top:0px; left:722; width:60px; text-align:center; font-size:70%; color:black; font-family:Verdana' 
		onClick=self.location.href='Main.php'>
		<img valign='bottom' width='40' border='0' src='img/Home.png'>
		<br>Home
	</div>

	<body bgcolor=gainsboro>

<?php if(str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))=="Bahnhof.php")
	{
		echo "<h1>".$TEXT['lang-station']." ".$TEXT['lang-change']."</h1>";
		echo "<h2>".$TEXT['lang-bhf_subrdr']."</h2>";
		echo '<hr>';
		echo $TEXT['lang-hello'].' <b>'.$loggedInUser->display_username.'</b>, '.$TEXT['lang-bhf_verwaltung'].'<br><font size="1"><br></font>';
	}
	else
	{
		echo "<h1>".$TEXT['lang-headrdr']."</h1>";
		echo "<h2>".$TEXT['lang-subrdr']."</h2>";
		echo '<hr>';
		echo $TEXT['lang-hello'].' <b>'.$loggedInUser->display_username.'</b>, '.$TEXT['lang-import'].'<br><font size="1"><br></font>';
	}

	$DeineBahnhoefe=$db->sql_query("SELECT bahnhof.Haltestelle, bahnhof.Spur FROM manage
	LEFT JOIN bahnhof ON manage.Bhf_ID = bahnhof.id
	WHERE manage.MUser_ID = '$loggedInUser->user_id' ORDER BY bahnhof.Art, bahnhof.Haltestelle");

	echo "<table border=0 cellpadding=0 cellspacing=0 style='font-family:Arial;font-size:18px'>";
	while($Bahnhof=$db->sql_fetchrow($DeineBahnhoefe))
	{
		echo "<tr><td width='300' align='right'><b>".$Bahnhof['Haltestelle']."</b>
		</td><td width='15'></td><td style='font-size:16px' >[ ".$Bahnhof['Spur']." ]</td></tr>";
	}
	echo '</table><font size="1"><br></font>';

	if(str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))=="Bahnhof.php"){
		echo $TEXT['lang-bhf_iother'].'<br><font size="1"><br></font>';}
	else echo $TEXT['lang-iother'].'<br><font size="1"><br></font>';

?>		<div style="height:23px; overflow:hidden;">
			<input type=button style="background-color:lightgrey; font-size:12; width:200px; height:23px" value="<?php echo $TEXT['lang-file']?>">
<form method="POST" enctype="multipart/form-data">
	<input type="file" name="datei" size="1" style="width:200px; height:25px; opacity:0; filter:alpha(opacity:0); position:relative; top:<?php if(isIE()!="") echo"-44px"; else echo"-25px";?>;" />
		</div>
		<input type="submit" style="background-color:lightgrey; font-size:12; width:200px; height:23px" value="<?php echo $TEXT['lang-wrkbook']?>">
<?php

	if(pathinfo($_FILES["datei"]["name"], PATHINFO_EXTENSION)!="xls" and is_array($_FILES["datei"]))
	{
		if(pathinfo($_FILES["datei"]["name"], PATHINFO_EXTENSION)=="") 
		{
			echo "<br><font style='font-family:Verdana' size='4'>".$TEXT['lang-fileupload_select']."</font>";
		}
		else
		{
			echo "<br><font style='font-family:Verdana' color='red' size='4'>".
			$TEXT['lang-fileupload_wrongmime']."<b>".pathinfo($_FILES["datei"]["name"], PATHINFO_EXTENSION)."</b></font>";
		}
	}

	if(!isset($_FILES['datei']['tmp_name'])) die;
	$inputFileName = $_FILES['datei']['tmp_name'];

	// Create new PHPExcel object
	$filetype = PHPExcel_IOFactory::identify($inputFileName);
	$objReader = PHPExcel_IOFactory::createReader($filetype);
	$objReader->setReadDataOnly(FALSE);  // to be set to FALSE to read in cell color
	$objPHPExcel = $objReader->load($inputFileName);

	echo '<hr>';

	echo 'Workbook contains ' .$objPHPExcel->getSheetCount(), ' worksheets:<br>';
	$loadedSheetNames = $objPHPExcel->getSheetNames();
	foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName)
	{
		echo $sheetIndex, ' -> <b>', $loadedSheetName, '</b><br>';
	}

	$objPHPExcel->setActiveSheetIndex(0);
	$checklang=readCell($objPHPExcel,'D9');
	if($checklang!=$languages[$lang]) echo '<font color="red">'.$TEXT["lang-lang"].' different = '.$languages[$lang].' # '.$checklang.'<br></font>';
	echo '<hr>';

	foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName)
	{
		if($loadedSheetName=="FREMO_Yellow_Pages" and str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))=="FYP.php" 
		or $loadedSheetName==$TEXT['lang-in'] and str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))=="FYP.php")
		{			
			if(str_replace("FREMO","",substr(readCell($objPHPExcel,'A1'),0)) != "Yellow Pages")
			{
				echo $TEXT['lang-colorlegend2']; // keine Yellow Pages gefunden
			}
			else
			{
				echo '<table border=0 cellpadding=0 cellspacing=0>';
				echo $TEXT['lang-wrkbook'].'&nbsp<b>'.$loadedSheetName.'</b>';
				echo "<tr><td width='400'><b>".$TEXT['lang-product']."</b></td><td width='40' align='left'></td><td width='400'><b>".$TEXT['lang-an']."</b></td>";
				
				$objPHPExcel->setActiveSheetIndex($sheetIndex);
				$i=10;
				while($objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue()!="")
				{
					$NHM_Code = readCell($objPHPExcel,'A'.$i); //NHM
					$Wagengattung = readCell($objPHPExcel,'E'.$i); //Gattung
					$Produktbeschreibung = readCell($objPHPExcel,'F'.$i); //Produktbeschreibung
					$Product_Description = readCell($objPHPExcel,'G'.$i); //Int. Prod. Desc.
					$Anschliesser = readCell($objPHPExcel,'H'.$i); //Anschliesser
					$FYP_Bem = readCell($objPHPExcel,'I'.$i); //Bemerkung
					$Ladestelle_Gleis = readCell($objPHPExcel,'J'.$i); //Ladestelle
					$Wagen_Woche = readCell($objPHPExcel,'K'.$i); //Wagen / Woche
					$Betriebsstelle = readCell($objPHPExcel,'L'.$i); //Betriebsstelle
					$Kurzbezeichnung = readCell($objPHPExcel,'M'.$i); //Kurzbezeichnung *
					$Bahnverwaltung = readCell($objPHPExcel,'N'.$i); //Bahnverwaltung
					$Spur = readCell($objPHPExcel,'O'.$i); //Gruppe *
					$Besitzer = readCell($objPHPExcel,'P'.$i); //Besitzer
					$Email = readCell($objPHPExcel,'Q'.$i); //Email
					$id_fyp = readCell($objPHPExcel,'R'.$i).'<br>'; //id_fyp

					if($NHM_Code=="" and $loadedSheetName=="FREMO_Yellow_Pages") $NHM_Code="99000000"; // GÜTER, ANDERWEITIG WEDER GENANNT NOCH INBEGRIFFEN
					elseif($NHM_Code=="") $NHM_Code="0"; // Eingangsfrachten

					$Ladestelle = trim(strtok($Ladestelle_Gleis,"("));
					$Gleisname = trim(strtok(")"));

					if($Wagen_Woche=="")$Wagen_Woche="1";

					$DeineBahnhoefe=$db->sql_query("SELECT bahnhof.Haltestelle, bahnhof.Spur FROM manage
					LEFT JOIN bahnhof ON manage.Bhf_ID = bahnhof.id
					WHERE manage.MUser_ID = '$loggedInUser->user_id'");

					while($Bahnhof=$db->sql_fetchrow($DeineBahnhoefe))
					{
						if($Bahnhof['Haltestelle']==$Betriebsstelle and $Bahnhof['Spur']==$Spur)
						{
							$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM fyp
							LEFT JOIN bahnhof ON fyp.Bhf_ID = bahnhof.id
							LEFT JOIN gleise ON gleise.Bhf_ID = fyp.Gleis_ID
							WHERE id_fyp = '$id_fyp'"));

							$bhf = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof
							WHERE Haltestelle = '".escape($Betriebsstelle)."'
							AND Spur = '".escape($Spur)."'"));
							if($bhf['id']!="") $Bhf_ID=$bhf['id'];
							else $Bhf_ID='0';

							$gls = $db->sql_fetchrow($db->sql_query("SELECT * FROM gleise
							WHERE Ladestelle = '".escape($Ladestelle)."'
							AND Gleisname = '".escape($Gleisname)."'"));
							if($gls['id']!="") $Gleis_ID=$gls['id'];
							else $Gleis_ID='0';

							if($id_fyp > 0 and $i > '10' and $Produktbeschreibung!="")
							{
								if($row['Kurzbezeichnung']==$Kurzbezeichnung and $row['Spur']==$Spur)
								{
									if($FYP_Bem != 'delete')
									{
										$db->sql_query("UPDATE fyp SET NHM_Code = '$NHM_Code', Wagengattung = '$Wagengattung',
										Produktbeschreibung = '$Produktbeschreibung', Product_Description = '$Product_Description',
										Anschliesser = '$Anschliesser', FYP_Bem = '$FYP_Bem', Gleis_ID = '$Gleis_ID', Bhf_ID = '$Bhf_ID',
										Ladestelle = '$Ladestelle', Wagen_Woche = '$Wagen_Woche' WHERE id_fyp = '$id_fyp'");
										echo "<tr><td><font color='darkblue'>".$row['Produktbeschreibung']."</font></td><td></td><td width='400'>
										<font color='darkblue'>".$row['Anschliesser']."</font></td><td>
										<font color='darkblue'>".$TEXT['lang-updated'].": ".$TEXT['lang-row']." = ".$i."</font></td></tr>";

										$kbz = $db->sql_fetchrow($db->sql_query("SELECT Kurzbezeichnung FROM bahnhof
										WHERE Kurzbezeichnung = '$Kurzbezeichnung' AND Spur = '$Spur' AND NOT id = '$Bhf_ID'"));
echo sql_error();
										if($kbz['Kurzbezeichnung']!="")
										{
											echo "<tr><td colspan='3' align='center' ><font color='red'>- <b>".$Kurzbezeichnung."</b> - ".$TEXT['lang-kbzerr']."</font></td>
											<td><font color='red'>".$TEXT['lang-declined'].": ".$TEXT['lang-row']." = ".$i."</font></td></tr>";
										}
										else
										{
											$db->sql_query("UPDATE bahnhof SET Kurzbezeichnung = '$Kurzbezeichnung', Bahnverwaltung = '$Bahnverwaltung',
											Besitzer = '$Besitzer', Email = '$Email', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id'
											WHERE Haltestelle = '$Betriebsstelle' and Spur = '$Spur'");
										}
									}
									else
									{
										$db->sql_query("DELETE FROM fyp WHERE id_fyp = '".$id_fyp."'");
										echo "<tr><td colspan='3' align='center' >
										<font color='red'> ".$TEXT['lang-row']." ".$TEXT['lang-deleted']." (".$Produktbeschreibung." / ".$Anschliesser.")</font></td>
										<td><font color='red'> ".$TEXT['lang-deleted'].": Row ID = ".$id_fyp."; ".$TEXT['lang-row']." = ".$i."</font></td></tr>";
									}
								}
								else
								{
									echo "<tr><td colspan='3' align='center' >
									<font color='darkred'> ".$TEXT['lang-rowerror']." (".$Produktbeschreibung." / ".$Anschliesser.")</font></td>
									<td><font color='darkred'> ".$TEXT['lang-declined'].": Row ID = ".$id_fyp."; ".$TEXT['lang-row']." = ".$i."</font></td></tr>";
								}
							}
							elseif($i > '10' and $Produktbeschreibung!="")
							{
								$db->sql_query("INSERT INTO fyp (NHM_Code, Wagengattung, Produktbeschreibung, Product_Description,
								Anschliesser, FYP_Bem, Ladestelle, Gleis_ID, Wagen_Woche, Betriebsstelle, Bhf_ID)
								VALUES('$NHM_Code', '$Wagengattung', '$Produktbeschreibung', '$Product_Description', '$Anschliesser', 
								'$FYP_Bem', '$Ladestelle', '$Gleis_ID', '$Wagen_Woche', '$Betriebsstelle', '$Bhf_ID');");
echo sql_error();

								$kbz = $db->sql_fetchrow($db->sql_query("SELECT Kurzbezeichnung FROM bahnhof
								WHERE Kurzbezeichnung = '$Kurzbezeichnung' AND Spur = '$Spur' AND NOT id = '$Bhf_ID'"));

								if($kbz['Kurzbezeichnung']!="")
								{
									echo "<tr><td colspan='3' align='center' ><font color='red'>- <b>".$Kurzbezeichnung."</b> - ".$TEXT['lang-kbzerr']."</font></td>
									<td><font color='red'>".$TEXT['lang-declined'].": ".$TEXT['lang-row']." = ".$i."</font></td></tr>";
								}
								else
								{
									$db->sql_query("UPDATE bahnhof SET Kurzbezeichnung = '$Kurzbezeichnung', Bahnverwaltung = '$Bahnverwaltung',
									Besitzer = '$Besitzer', Email = '$Email', LastUser = '$loggedInUser->display_username', LastUser_ID = '$loggedInUser->user_id' 
									WHERE Haltestelle = '$Betriebsstelle' and Spur = '$Spur'");

									echo "<tr><td><font color='darkgreen'>".$Produktbeschreibung."</font></td><td></td>
									<td width='400'><font color='darkgreen'>".$Anschliesser."</font></td><td>
									<font color='darkgreen'>".$TEXT['lang-added'].": ".$TEXT['lang-row']." = ".$i."</font></td></tr>";
								}
							}
						}
					}
					$i++;
				}
				echo '</table><hr>';
			}
		}

// BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB
// B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B
// BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB
// B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B
// B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B
// BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB

// Betriebsstellen Excel input =========================================================================

		elseif($loadedSheetName==$TEXT['lang-bh'] and str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))=="Bahnhof.php") // load station only from Bahnhof.php
		{
		
			$objPHPExcel->setActiveSheetIndex($sheetIndex);

			if(readCell($objPHPExcel,'A2')!=$TEXT['lang-station'].":")
			{
				echo $TEXT['lang-colorlegend5']; // keine Betriebsstelle gefunden
			}
			else
			{
				$Haltestelle = readCell($objPHPExcel,'C2'); // Betriebsstelle
				$Spur = readCell($objPHPExcel,'H2'); // Gruppe
				$Kurzbezeichnung = readCell($objPHPExcel,'C3');
				$Bahnverwaltung = readCell($objPHPExcel,'C4');
				$Besitzer = readCell($objPHPExcel,'F3');
				$Email = readCell($objPHPExcel,'F4');
				$Art = readCell($objPHPExcel,'C5');
				$Bhf_Bem = readCell($objPHPExcel,'C6');
				$Zeichnung = readCell($objPHPExcel,'H3');
				$Hauptgleise = readCell($objPHPExcel,'H4');
				$Streckengleise = readCell($objPHPExcel,'H5');
				$Kreuzung = readCell($objPHPExcel,'H6');

				if($Art==$TEXT['lang-hp']) $Typ = 'Stop'; // Haltepunkt
				elseif($Art==$TEXT['lang-bl']) $Typ = 'Block'; // Blockstelle
				elseif($Art==$TEXT['lang-an']) $Typ = 'Connect'; // Anschliesser
				elseif($Art==$TEXT['lang-sbf']) $Typ = 'SBF'; // Schattenbahnhof
				else $Typ='Station'; // Bahnhof

				// search Bhf_ID for Kbz and Spur
				$Bhf_ID_Kbz_Spur = $db->sql_fetchrow($db->sql_query("SELECT id FROM bahnhof
				WHERE Kurzbezeichnung = '$Kurzbezeichnung' and Spur = '$Spur'"));
echo sql_error();

				$BhfWrite=0;
				$BhfUpdate=0;

// insert new train station
				if($Bhf_ID_Kbz_Spur['id']=="") 
				{
					$BhfWrite++;
					$db->sql_query("INSERT INTO bahnhof (Haltestelle, Kurzbezeichnung, Spur, Email, Art, Kreuzung)
					VALUES('$Haltestelle', '$Kurzbezeichnung', '$Spur', '$loggedInUser->email', '$Typ', '$Kreuzung')");
echo sql_error();
					$Mitverwalter = 1; // keine Mitverwalter
					$Bhf_ID = $db->sql_nextid();
					$Mng_Bem = $Art." ".$TEXT['lang-added']." ".date("d.m.Y - H:i", time());
					$db->sql_query("INSERT INTO manage (User, MUser_ID, Betriebsstelle, Bhf_ID, MainMngr_ID, Mng_Bem, Mitverwalter)
					VALUES('$loggedInUser->display_username','$loggedInUser->user_id','$Betriebsstelle','$Bhf_ID', '0', '$Mng_Bem', '$Mitverwalter');");
					
echo sql_error();
					$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Betriebsstelle = manage.Betriebsstelle
					SET fyp.Bhf_ID = '$Bhf_ID' WHERE manage.MUser_ID = '$loggedInUser->user_id' and fyp.Betriebsstelle = '$Haltestelle' and fyp.Bhf_ID = '0'");
echo sql_error();
					$FYPupdates = $db->sql_affectedrows();
				}				
				else
				{
					$Bhf_ID = $Bhf_ID_Kbz_Spur['id'];
				}
				
				
				// filter managed stations
				$DeineBahnhoefe=$db->sql_query("SELECT Haltestelle, Kurzbezeichnung, Spur, manage.Bhf_ID FROM bahnhof
				LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID 
				WHERE manage.MUser_ID = '$loggedInUser->user_id'");
echo sql_error();
				
				while($Bahnhof=$db->sql_fetchrow($DeineBahnhoefe))
				{
					if($Bahnhof['Kurzbezeichnung']==$Kurzbezeichnung and $Bahnhof['Spur']==$Spur)
					{
						$BhfUpdate++;
						$db->sql_query("UPDATE bahnhof LEFT JOIN manage ON bahnhof.id = manage.Bhf_ID LEFT JOIN fyp ON bahnhof.id = fyp.Bhf_ID 
						SET bahnhof.Haltestelle = '$Haltestelle', bahnhof.Spur = '$Spur', bahnhof.Kurzbezeichnung = '$Kurzbezeichnung',
						bahnhof.Bahnverwaltung = '$Bahnverwaltung', bahnhof.Besitzer = '$Besitzer', bahnhof.Email = '$Email', bahnhof.Art = '$Typ', 
						bahnhof.Bhf_Bem = '$Bhf_Bem', bahnhof.Zeichnung = '$Zeichnung', bahnhof.Streckengleise = '$Streckengleise', 
						bahnhof.Kreuzung = '$Kreuzung', bahnhof.LastUser = '$loggedInUser->display_username', bahnhof.LastUser_ID = '$loggedInUser->user_id', 
						manage.Betriebsstelle = '$Haltestelle', fyp.Betriebsstelle = '$Haltestelle'
						WHERE manage.MUser_ID = '$loggedInUser->user_id' and bahnhof.id = '$Bhf_ID'");
												
						if($Bhf_ID_Kbz_Spur['id']=="") {
							echo $sheetIndex, " -> <b>".$loadedSheetName."&nbsp&nbsp&nbsp&nbsp
							</b><font style='font-family:Verdana' >".$TEXT['lang-station']." <b>".$Haltestelle."&nbsp</font>
							</b><font style='font-family:Verdana' color='green'>".$TEXT['lang-added'];
							if($FYPupdates!="") echo "</a><b><a style='color:darkred; font-family:Verdana'>&nbsp&nbsp".$FYPupdates."&nbsp</a></b>".$TEXT['lang-bhf_FYPfound'];
							echo "<a></font><hr>";	
						}
						else echo $sheetIndex, " -> <b>".$loadedSheetName."&nbsp&nbsp&nbsp&nbsp
							</b><font style='font-family:Verdana' >".$TEXT['lang-station']." <b>".$Haltestelle."
							&nbsp</font></b><font style='font-family:Verdana' color='darkblue'>".$TEXT['lang-updated']."</font><hr>";

?>						<table align="left" style='font-family:Verdana' border=0 cellpadding=0 cellspacing=0>
							<colgroup>
								<col width="200px">
								<col width="65px">
								<col width="130px">
								<col width="190px">
								<col width="170px">
								<col width="120px">
							</colgroup>
						
							<tr>
								<td align='right' ><?php echo $TEXT['lang-station']?>:&nbsp</td>
								<td align='left' colspan='3' ><b><?php echo $Haltestelle?></b>
								<td align='right' style='font-family:Verdana' ><?php echo $TEXT['lang-group']?>:&nbsp</td>
								<td align='left'><b><?php echo $Spur?></b></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td align='right' ><?php echo $TEXT['lang-kbz']?>:&nbsp</td>
								<td align="left"><b><?php echo $Kurzbezeichnung?></b></td>
								<td align='right' ><?php echo $TEXT['lang-owner']?>:&nbsp</td>
								<td align='left'><b><?php echo $Besitzer?></b></td>
								<td align='right' ><?php echo $TEXT['lang-drawing']?>:&nbsp</td>
								<td align='left'><b><?php echo $Zeichnung?></b></td>
							</tr>
							<tr>
								<td align='right' ><?php echo $TEXT['lang-bvw']?>:&nbsp</td>
								<td align='left' ><b><?php echo $Bahnverwaltung?></b></td>
								<td align='right' ><?php echo $TEXT['lang-email']?>:&nbsp</td>
								<td align='left' ><b><?php echo $Email?></b></td>
								<td align='right' ><?php echo $TEXT['lang-mts']?>:&nbsp</td>
								<td align='left' ><b><?php echo $Hauptgleise?></b></td>
							</tr>
							<tr>
								<td align='right' ><?php echo $TEXT['lang-typ']?>:&nbsp</td>
								<td align='left' colspan='3' ><b><?php echo $Typ?></b></td>
								<td align='right' ><?php echo $TEXT['lang-st']?>:&nbsp</td>
								<td align='left' ><b><?php echo $Streckengleise?></b></td>
							</tr>
							<tr>
								<td align='right' ><?php echo $TEXT['lang-rem']?>:&nbsp</td>
								<td align='left' colspan='3' ><b><?php echo $Bhf_Bem?></b></td>
								<td align='right' ><?php echo $TEXT['lang-cl']?>:&nbsp</td>
								<td align='left' ><b><?php echo $Kreuzung?> cm</b></td>
							</tr>
						</table><br clear=all><hr>
						
						<table align="left" style='font-family:Verdana' border=0 cellpadding=0 cellspacing=0>
							<colgroup>
								<col width="80px">
								<col width="120px">
								<col width="60px">
								<col width="60px">
								<col width="20px">
								<col width="10px">
								<col width="200px">
								<col width="200px">
							</colgroup>
						
							<tr>
								<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-track']?></b></td>
								<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-typ']?></b></td>
								<td style='font-family:Arial Narrow' ><img src=img/blank.gif><br><b><?php echo $TEXT['lang-tl']?></b></td>
								<td style='font-family:Arial Narrow' ><img src=img/blank.gif><br><b><?php echo $TEXT['lang-bs']?></b></td>
								<td><img src=img/blank.gif><br><b></b></td>
								<td><img src=img/blank.gif><br><b></b></td>
								<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-ls']?></b></td>
								<td><img src=img/blank.gif><br><b><?php echo $TEXT['lang-rem']?></b></td>
								<td><img src=img/blank.gif><br></td>
								<td><img src=img/blank.gif><br></td>
								
							</tr>					
<?php						
						$objPHPExcel->setActiveSheetIndex($sheetIndex);
						$xrow=9; //data starts at row 9
						while($objPHPExcel->getActiveSheet()->getCell('A'.$xrow)->getCalculatedValue()!="")
						{
							$Gleisname = readCell($objPHPExcel,'A'.$xrow);
							$Gleisart = readCell($objPHPExcel,'B'.$xrow);
							$Gleislaenge = readCell($objPHPExcel,'C'.$xrow);
							$Bahnsteiglaenge = readCell($objPHPExcel,'D'.$xrow);
							$LadeFarbe = $objPHPExcel->getActiveSheet()->getStyle('E'.$xrow)->getFill()->getStartColor()->getRGB();
							$Ladestelle = readCell($objPHPExcel,'F'.$xrow);
							$Gl_Bem = readCell($objPHPExcel,'G'.$xrow);
							
							if($Gleislaenge=="")$Gleislaenge=0;
							if($Bahnsteiglaenge=="")$Bahnsteiglaenge=0;

							if($Gleisart==$TEXT['lang-mt']) $Glsart='Main';
							elseif($Gleisart==$TEXT['lang-bw']) $Glsart='Depot';
							else $Glsart='Siding';

							$gls=$db->sql_fetchrow($db->sql_query("SELECT gleise.Gleisname, gleise.Ladestelle, gleise.id FROM gleise 
							LEFT JOIN manage ON manage.Bhf_ID = gleise.Bhf_ID 
							WHERE manage.MUser_ID = '$loggedInUser->user_id' and gleise.Bhf_ID = '$Bhf_ID' 
							and gleise.Gleisname = '$Gleisname' and gleise.Ladestelle = '$Ladestelle'"));
echo sql_error();
							$Gleis_ID=$gls['id'];
							if($Gleis_ID=="")
							{
								$db->sql_query("INSERT INTO gleise (Gleisname, Gleislaenge, Gleisart, Bahnsteiglaenge, LadeFarbe, Ladestelle, Gl_Bem, Bhf_ID)
								VALUES('$Gleisname', '$Gleislaenge', '$Glsart', '$Bahnsteiglaenge', '$LadeFarbe', '$Ladestelle', '$Gl_Bem', '$Bhf_ID');");
								$Gleis_ID = $db->sql_nextid();
echo sql_error();		
								if($Ladestelle!=""){
									$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID 
									SET fyp.Gleis_ID = '".$Gleis_ID."' 
									WHERE fyp.Ladestelle = '$Ladestelle' and WHERE manage.MUser_ID = '$loggedInUser->user_id'");}
echo sql_error();			}
							else
							{
								$db->sql_query("UPDATE gleise LEFT JOIN manage ON gleise.Bhf_ID = manage.Bhf_ID 
								SET gleise.Gleisname='$Gleisname', gleise.Gleislaenge='$Gleislaenge', 
								gleise.Gleisart='$Glsart', gleise.Bahnsteiglaenge='$Bahnsteiglaenge', 
								gleise.LadeFarbe='$LadeFarbe', gleise.Ladestelle='$Ladestelle', gleise.Gl_Bem='$Gl_Bem'
								WHERE manage.MUser_ID = '$loggedInUser->user_id' and gleise.id = ".$Gleis_ID);
echo sql_error();		
								$db->sql_query("UPDATE fyp LEFT JOIN manage ON fyp.Bhf_ID = manage.Bhf_ID 
								SET fyp.Ladestelle = '".$Ladestelle."' 
								WHERE fyp.Gleis_ID = '$Gleis_ID' and manage.MUser_ID = '$loggedInUser->user_id'");
echo sql_error();			}

							if($Gleislaenge==0) $Gleislaenge="";
							if($Bahnsteiglaenge==0) $Bahnsteiglaenge="";
														
?>							<tr>
								<td align='left' ><?php echo $Gleisname?></td>
								<td align='left' ><?php echo $Gleisart?></td>
								<td align='left' ><?php echo $Gleislaenge?></td>
								<td align='left' ><?php echo $Bahnsteiglaenge?></td>
								<td align='left' bgcolor='#<?php echo $LadeFarbe?>'</td><td></td>
								<td align='left' ><?php echo $Ladestelle?></td>
								<td align='left' ><?php echo $Gl_Bem?></td>
								<td align='left' ><?php if($Gleis_ID!="") echo '<font color="darkblue">'.$TEXT['lang-updated'].'<font>'; 
																				else echo '<font color="green">'.$TEXT['lang-added'].'<font>'; ?></td>
							</tr>
<?php							
							$xrow++;
						}
						
?>						</table><br clear=all><hr>
<?php						
						$xrow++;
						while($objPHPExcel->getActiveSheet()->getCell('A'.$xrow)->getCalculatedValue()!="")
						{
							if(readCell($objPHPExcel,'A'.$xrow)==$TEXT['lang-einleitung'])
							{
								echo "<a style='font-family:Verdana' ><b>".$TEXT['lang-einleitung']."</a></b><br>";
								$xrow++;
								while($objPHPExcel->getActiveSheet()->getStyle('A'.$xrow)->getFill()->getStartColor()->getRGB()=='FFFFFF')
								{
									$Einleitung = $Einleitung.readCell($objPHPExcel,'A'.$xrow);
									$xrow++;
								}
								$Einleitung = str_replace('>n', ">", html_entity_decode(getVariableFromString($Einleitung)));
								$Einleitung = str_replace('>n', ">", html_entity_decode(getVariableFromString($Einleitung)));
								$db->sql_query("UPDATE bahnhof SET Einleitung = '$Einleitung', LastUser = '$loggedInUser->display_username' 
								WHERE id = '$Bhf_ID'");
echo sql_error();		
								echo $Einleitung."<br>";
							}
							
 							if(file_exists('Bilder/pic_Bhf_ID_'.$Bhf_ID)) echo "<img src='Bilder/pic_Bhf_ID_".$Bhf_ID."' width='1024px' ><br>";
							
							if(readCell($objPHPExcel,'A'.$xrow)==$TEXT['lang-beschreibung'])
							{
								echo "<a style='font-family:Verdana' ><b>".$TEXT['lang-beschreibung']."</a></b><br>";
								$xrow++;
								while($objPHPExcel->getActiveSheet()->getStyle('A'.$xrow)->getFill()->getStartColor()->getRGB()=='FFFFFF')
								{
									$Beschreibung = $Beschreibung.readCell($objPHPExcel,'A'.$xrow);
									$xrow++;
								}
								$Beschreibung = str_replace('>n', ">", html_entity_decode(getVariableFromString($Beschreibung)));
								$Beschreibung = str_replace('>n', ">", html_entity_decode(getVariableFromString($Beschreibung)));
								$db->sql_query("UPDATE bahnhof SET Beschreibung = '$Beschreibung', LastUser = '$loggedInUser->display_username' 
								WHERE id = '$Bhf_ID'");
echo sql_error();		
								echo $Beschreibung."<br>";
							}
							if(readCell($objPHPExcel,'A'.$xrow)==$TEXT['lang-personenverkehr'])
							{
								echo "<a style='font-family:Verdana' ><b>".$TEXT['lang-personenverkehr']."</a></b><br>";
								$xrow++;
								while($objPHPExcel->getActiveSheet()->getStyle('A'.$xrow)->getFill()->getStartColor()->getRGB()=='FFFFFF')
								{
									$Personenverkehr = $Personenverkehr.readCell($objPHPExcel,'A'.$xrow);
									$xrow++;
								}
								$Personenverkehr = str_replace('>n', ">", html_entity_decode(getVariableFromString($Personenverkehr)));
								$Personenverkehr = str_replace('>n', ">", html_entity_decode(getVariableFromString($Personenverkehr)));
								$db->sql_query("UPDATE bahnhof SET Personenverkehr = '$Personenverkehr', LastUser = '$loggedInUser->display_username' 
								WHERE id = '$Bhf_ID'");
echo sql_error();		
								echo $Personenverkehr."<br>";
							}
							if(readCell($objPHPExcel,'A'.$xrow)==$TEXT['lang-frachtverkehr'])
							{
								echo "<a style='font-family:Verdana' ><b>".$TEXT['lang-frachtverkehr']."</a></b><br>";
								$xrow++;
								while($objPHPExcel->getActiveSheet()->getStyle('A'.$xrow)->getFill()->getStartColor()->getRGB()=='FFFFFF')
								{
									$Frachtverkehr = $Frachtverkehr.readCell($objPHPExcel,'A'.$xrow);
									$xrow++;
								}
								$Frachtverkehr = str_replace('>n', ">", html_entity_decode(getVariableFromString($Frachtverkehr)));
								$Frachtverkehr = str_replace('>n', ">", html_entity_decode(getVariableFromString($Frachtverkehr)));
								$db->sql_query("UPDATE bahnhof SET Frachtverkehr = '$Frachtverkehr', LastUser = '$loggedInUser->display_username' 
								WHERE id = '$Bhf_ID'");
echo sql_error();		
								echo $Frachtverkehr."<br>";
							}
							$xrow++;
						}
						
						// read pictures
						$i = 0;
						foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) 
						{
						    if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) 
						    {
						        ob_start();
						        call_user_func(
						            $drawing->getRenderingFunction(),
						            $drawing->getImageResource()
						        );
						        $imageContents = ob_get_contents();
						        ob_end_clean();
						        switch ($drawing->getMimeType()) {
						            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
						                $extension = 'png'; 
						                break;
						            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
						                $extension = 'gif'; 
						                break;
						            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
						                $extension = 'jpg'; 
						                break;
						        }
						    } 
						    else 
						    {
						        $zipReader = fopen($drawing->getPath(),'r');
						        $imageContents = '';
						        while (!feof($zipReader)) 
						        {
						            $imageContents .= fread($zipReader,1024);
						        }
						        fclose($zipReader);
						        $extension = $drawing->getExtension();
						    }
						    //$myFileName = 'pic_Bhf_ID_'.++$i.'.'.$extension;
						    $myFileName = 'Bilder/pic_Bhf_ID_'.$Bhf_ID;
						    file_put_contents($myFileName,$imageContents);
						}
					}
				}
			}
		}
		elseif(str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))=="FYP.php")
		{
			echo "<b>".$loadedSheetName."</b>&nbsp".$TEXT['lang-wrkbook']."&nbsp<font color='red'>".$TEXT['lang-colorlegend2']."!</font><br>";
		}
	}

//$db->sql_affectedrows()." ### ".$db->sql_nextid();
//echo $BhfWrite." ".$BhfUpdate; 
	if($BhfWrite + $BhfUpdate == 0 and str_replace("FYP/","",substr($_SERVER['SCRIPT_NAME'],1))!="FYP.php")
	{
		echo "<font style='font-family:Verdana' color='red' size='4'>".$TEXT['lang-fileupload_error']."</font>";
	}
	exit;

}
