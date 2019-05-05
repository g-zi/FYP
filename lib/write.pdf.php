<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

if(@$_REQUEST['action']=="test")
{
if($lang=='DE') require_once('lib/tcpdf/config/lang/ger.php');
elseif($lang=='NL') require_once('lib/tcpdf/config/lang/nld.php');
elseif($lang=='DA') require_once('lib/tcpdf/config/lang/dan.php');
else require_once('lib/tcpdf/config/lang/eng.php');

require_once('lib/tcpdf/tcpdf.php');
require_once('lib/shared.inc.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
	{
		//Page header
		public function getxypos()
		{
			if(!isset($this->xypos))
			{
				$this->xypos = array();
			}
			$this->xypos[] = array($this->GetX(), $this->GetY());
		}
	}

// create new PDF document
$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(15, 10, PDF_MARGIN_RIGHT);

$pdf->AddPage();
// create some HTML content
$html = '<table border="1" cellspacing="0" cellpadding="0">
<thead>
<tr bgcolor="#E6E6E6">
<th width="100" height="200"><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="28" ><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="28" ><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="120" ><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="80" ><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="80" ><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="70" ><tcpdf method="getxypos" params="'.$params.'" /></th>
<th width="28" ><tcpdf method="getxypos" params="'.$params.'" /></th>
</tr>
</thead>
<tr bgcolor="#FFFFFF">
<td width="150"  align="center">test</td>
<td width="40" align="center">AII</td>
<td align="center">test</td>
<td align="center">test</td>
<td align="center">test</td>
<td align="center">test</td>
<td align="center">test</td>
<td align="center">test</td>
</tr>
</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// array with names of columns
$arr_names = array(
	array($TEXT['lang-ls'], 1, 63), // array(name, new X, new Y);
	array($TEXT['lang-track'], 2, 69),
	array($TEXT['lang-tl'], 2, 69),
	array($TEXT['lang-an'], 1, 63),
	array($TEXT['lang-in'], 1, 63),
	array($TEXT['lang-out'], 1, 63),
	array($TEXT['lang-class'], 1, 63),
	array($TEXT['lang-wagon'].$TEXT['lang-week'], 2, 69),
);
	$pdf->SetFont('freesans', 'B', 14);

// all columns of current page
foreach( $arr_names as $num => $arrCols )
{
	$x = $pdf->xypos[$num][0] + $arrCols[1]; // new X
	$y = $pdf->xypos[$num][1] + $arrCols[2]; // new Y
	$n = $arrCols[0]; // column name
	// transforme Rotate
	$pdf->StartTransform();
	if($num>0 and $num<3 or $num==7) $pdf->Rotate(90, $x, $y);
	$pdf->Text($x, $y, $n);
	$pdf->StopTransform();
}

//Close and output PDF document
$pdf->Output('test.pdf', 'I');

}


// FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF  FFFFFFF
// F        F        F        F        F        F        F        F        F        F        F
// FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF   FFFFFF
// F        F        F        F        F        F        F        F        F        F        F
// F        F        F        F        F        F        F        F        F        F        F
// F        F        F        F        F        F        F        F        F        F        F

// Frachtzettel PDF output =========================================================================

if(@$_REQUEST['action']=="getFz")
{
	if($lang=='DE') require_once('lib/tcpdf/config/lang/ger.php');
	elseif($lang=='NL') require_once('lib/tcpdf/config/lang/nld.php');
	elseif($lang=='DA') require_once('lib/tcpdf/config/lang/dan.php');
	else require_once('lib/tcpdf/config/lang/eng.php');

	require_once('lib/tcpdf/tcpdf.php');

	// extend TCPF with custom functions
	class MYPDF extends TCPDF
	{
		// Colored table
		public function ColoredTableFz($data,$w,$zr)
		{
			$lw='0.2';	// Linienbreite
			$this->SetLineWidth($lw);

			//                                                Z     E     G     F     VB    VS    L    B
			if($_SESSION['FzH']=="N") 		$h = array(3.35,  6.6,  5.8,  5.5,  5.8,  6.6,  5.8,  5.8, 3.5); // N Höhe
			elseif($_SESSION['FzH']=="USA") $h = array(3.35,  7,    8,    6,    7,    7,    8,    6,   3.65); // 3" Höhe
			else 							$h = array(3.35,  8.4, 10.4,  7.4, 10.5,  8.4, 10.4,  8.4, 3.65); // HO Höhe
			// N = Array( 9, 18, 20, 18, 18, 18, 20, 16) 'von Excel    //HO = Array(10, 24, 30, 26, 28, 24, 30, 24) 'von Excel

			// Data
			foreach($data as $row)
			{
				    if(strstr($row[7], 'E') == TRUE)	$Gcol = array(255,196,196);
				elseif(strstr($row[7], 'F') == TRUE)	$Gcol = array(255,168,221);
				elseif(strstr($row[7], 'G') == TRUE)	$Gcol = array(255,255,100);
				elseif(strstr($row[7], 'H') == TRUE)	$Gcol = array(255,186,121);
				elseif(strstr($row[7], 'T') == TRUE)	$Gcol = array(255,205,0);
				elseif(strstr($row[7], 'I') == TRUE)	$Gcol = array(255, 255, 255);

				elseif(strstr($row[7], 'Uai') == TRUE)	$Gcol = array(186,255,255);
				elseif(strstr($row[7], 'Ui') == TRUE)	$Gcol = array(186,255,255);
				elseif(strstr($row[7], 'L') == TRUE)	$Gcol = array(186,255,255);

				elseif(strstr($row[7], 'K') == TRUE)	$Gcol = array(229,255,196);
				elseif(strstr($row[7], 'O') == TRUE)	$Gcol = array(229,255,196);

				elseif(strstr($row[7], 'R') == TRUE)	$Gcol = array(201,255,133);
				elseif(strstr($row[7], 'S') == TRUE)	$Gcol = array(201,255,133);

				elseif(strstr($row[7], 'Uc') == TRUE)	$Gcol = array(192,192,192);
				elseif(strstr($row[7], 'Ue') == TRUE)	$Gcol = array(192,192,192);
				elseif(strstr($row[7], 'Uh') == TRUE)	$Gcol = array(192,192,192);
				elseif(strstr($row[7], 'Z') == TRUE)	$Gcol = array(192,192,192);
				else $Gcol = array(255, 255, 255);


					if($row[17]=='yellow')	$Ecol=array(255, 255, 0);
				elseif($row[17]=='orange')	$Ecol=array(255, 160, 0);
				elseif($row[17]=='red')		$Ecol=array(255, 0, 0);
				elseif($row[17]=='lime')	$Ecol=array(0, 255, 0);
				elseif($row[17]=='blue')	$Ecol=array(0, 0, 220);
				elseif($row[17]=='brown')	$Ecol=array(130, 80, 30);
				elseif($row[17]=='black')	$Ecol=array(0, 0, 0);
				else $Ecol=array(255, 255, 255);
				if($row[17]=='blue' or $row[17]=='brown' or $row[17]=='black') $EcolT = 255;
				else $EcolT = 0;
				$Eborder = array(
					'L' => array('color' => array(0, 0, 0)),
					'R' => array('color' => array(0, 0, 0)),
					'T' => array('color' => array($EcolT)));

					if($row[18]=='yellow')	$Vcol=array(255, 255, 0);
				elseif($row[18]=='orange')	$Vcol=array(255, 160, 0);
				elseif($row[18]=='red')		$Vcol=array(255, 0, 0);
				elseif($row[18]=='lime')	$Vcol=array(0, 255, 0);
				elseif($row[18]=='blue')	$Vcol=array(0, 0, 220);
				elseif($row[18]=='brown')	$Vcol=array(130, 80, 30);
				elseif($row[18]=='black')	$Vcol=array(0, 0, 0);
				else $Vcol=array(255, 255, 255);
				if($row[18]=='blue' or $row[18]=='brown' or $row[18]=='black') $VcolT = 255;
				else $VcolT = 0;
				$Vborder = array(
					'L' => array('color' => array(0, 0, 0)),
					'R' => array('color' => array(0, 0, 0)),
					'T' => array('color' => array($VcolT)));

				$Lcol=HexToRGB($row[19]);

// Zielbahnhof
				$this->SetFillColorArray($Ecol);
				$this->SetTextColor($EcolT);
				$this->SetFont('freesans', '', 8);
				$this->Cell($w, $h[0]+0.1, $row[0], 'L,R,T', 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->Ln();
				$this->Translate(0,-0.1);
				$this->SetFont('', 'B', 16);
				$this->Cell($w, $h[1], $row[1], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				$this->Ln();
// Empfänger
				$this->SetFont('', '', 8);
				$this->Cell($w, $h[0]+0.1, $row[2], $Eborder, 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->SetLineStyle(array('color' => array('color' => '0')));
				$this->Ln();
				$this->Translate(0,-0.1);
				$this->SetFont('', '', 14);
				if((strlen($row[3]) < 30 or $_SESSION['FzH']=='N' or $_SESSION['FzH']=='USA') and !strrpos(escape($row[9]),'\n'))
				{	$this->Cell($w, $h[2], $row[3], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T'); }
				else
				{	$split = explode('\n', escape($row[3]));
					if(strlen($split[0]) > 30 ) $split = explode('|', wordwrap($row[3], strlen($row[3])/2, "|", true));
					$this->SetFont('', '', 12);
					$this->Cell($w, $h[2]/2.1, $split[0], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
					$this->Ln();
					$this->Translate(0,-0.025);
					$this->Cell($w, $h[2]/1.9, $split[1]." ".$split[2], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				}	$this->Ln();
// Gewicht/Gattung
				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0);
				$this->SetFont('', '', 8);
				$this->Cell($w/3, $h[0]+0.1, $row[4], 'L,R,T', 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->SetFillColorArray($Gcol);
				$this->SetFont('', '', 8);
				$this->Cell($w/3*2, $h[0]+0.1, $row[5], 'L,R,T', 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->Ln();
				$this->Translate(0,-0.1);
				$this->SetFillColor(255, 255, 255);
				$this->SetFont('freeserif', '', 14);
				$this->Cell($w/3, $h[3], $row[6], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				$this->SetFillColorArray($Gcol);
				$this->SetFont('freesans', '', 14);
				$this->Cell($w/3*2, $h[3], $row[7], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				$this->Ln();
// Fracht
				$this->SetFillColor(255, 255, 255);
				$this->SetFont('', '', 8);
				$this->Cell($w, $h[0], $row[8], 'L,R,T', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				$this->Ln();
				$this->SetFont('', '', 14);
				if((strlen($row[9]) < 30 or $_SESSION['FzH']=='N' or $_SESSION['FzH']=='USA') and !strrpos(escape($row[9]),'\n'))
				{	$this->Cell($w, $h[4], $row[9], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T'); }
				else
				{ 	$split = explode('\n', escape($row[9]));
					if(strlen($split[0]) > 30 ) $split = explode('|', wordwrap($row[9], strlen($row[9])/2, "|", true));
					$this->SetFont('', '', 12);
					$this->Cell($w, $h[4]/2.1, $split[0], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
					$this->Ln();
					$this->Translate(0,-0.025);
					$this->Cell($w, $h[4]/1.9, $split[1]." ".$split[2], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				}	$this->Ln();
// Versandbahnhof
				$this->SetFillColorArray($Vcol);
				$this->SetTextColor($VcolT);
				$this->SetFont('', '', 8);
				$this->Cell($w, $h[0]+0.1, $row[10], 'L,R,T', 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->Ln();
				$this->Translate(0,-0.1);
				$this->SetFont('', 'B', 16);
				$this->Cell($w, $h[5], $row[11], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				$this->Ln();
// Versender
				$this->SetFont('', '', 8);
				$this->Cell($w, $h[0]+0.1, $row[12], $Vborder, 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->SetLineStyle(array('color' => array('color' => '0')));
				$this->Ln();
				$this->Translate(0,-0.1);
				$this->SetFont('', '', 14);
				if((strlen($row[13]) < 30 or $_SESSION['FzH']=='N' or $_SESSION['FzH']=='USA') and !strrpos(escape($row[9]),'\n'))
				{	$this->Cell($w, $h[6], $row[13], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T'); }
				else
				{ 	$split = explode('\n', escape($row[13]));
					if(strlen($split[0]) > 30 ) $split = explode('|', wordwrap($row[13], strlen($row[13])/2, "|", true));
					$this->SetFont('', '', 12);
					$this->Cell($w, $h[6]/2.1, $split[0], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
					$this->Ln();
					$this->Translate(0,-0.025);
					$this->Cell($w, $h[6]/1.9, $split[1]." ".$split[2], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				}	$this->Ln();
// Ladestelle
				if(relativeluminance($row[19]) > 0.21) $FcolT = 0; else $FcolT = 255;

				$this->SetFillColor(255, 255, 255);
				$this->SetFillColorArray($Lcol);
				$this->SetTextColor($FcolT);
				$this->SetFont('', '', 8);
				$this->Cell($w, $h[0], $row[14], 'L,R,T', 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->Ln();
				$this->SetFillColorArray($Lcol);
				$this->SetFont('', '', 14);
				if((strlen($row[15]) < 30 or $_SESSION['FzH']=='N' or $_SESSION['FzH']=='USA') and !strrpos(escape($row[15]),'\n'))
				{	$this->Cell($w, $h[7], $row[15], 'L,R,B', 0, 'C', 1, '', 1, 'true', 'T', 'T'); }
				else
				{ 	$split = explode('\n', escape($row[15]));
					if(strlen($split[0]) > 30 ) $split = explode('|', wordwrap($row[15], strlen($row[15])/2, "|", true));
					$this->SetFont('', '', 10);
					$this->Cell($w, $h[7]/2.1, $split[0], 'L,R', 0, 'C', 1, '', 1, 'true', 'T', 'T');
					$this->Ln();
					$this->Translate(0,-0.025);
					$this->Cell($w, $h[7]/1.9, $split[1]." ".$split[2], 'L,R,B', 0, 'C', 1, '', 1, 'true', 'T', 'T');
				}	$this->Ln();

				$this->SetFillColor(255, 255, 255);
				$this->SetFont('', '', 8);
				$this->Cell($w, $h[8], $row[16], 'L,R,T,B', 0, 'L', 1, '', 0, 'true', 'T', 'T');
				$this->Ln();

				if($row[20]=='checked')
				{
					$this->SetAlpha(0.7);
					$this->Line($this->GetX(), $this->GetY(), $this->getX()+$w, $this->GetY()-($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]), array('width'=>1.5, 'cap'=>'butt', 'color'=>array(255, 0, 0)));
					$this->SetLineStyle(array('width'=>$lw, 'color'=>array(0, 0, 0)));
					$this->SetAlpha(1);
				}

				if($row[21]=='checked')
				{
					$this->SetAlpha(0.4);
					if($_SESSION['FzH']=="N" or $_SESSION['FzH']=="USA") $this->ImageSVG($file='img/Turn_N.svg', $this->GetX()+($w-45)/2, $this->GetY()-($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]));
					else $this->ImageSVG($file='img/Turn.svg', $this->GetX()+($w-45)/2, $this->GetY()-($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]));
					$this->SetAlpha(1);
				}

				if($row[22]=='checked')
				{
					$this->SetAlpha(0.4);
					if($_SESSION['FzH']=="N" or $_SESSION['FzH']=="USA") $this->ImageSVG($file='img/Mehrfach_N.svg', $this->GetX()+($w-45)/2, $this->GetY()-($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]));
					else $this->ImageSVG($file='img/Mehrfach.svg', $this->GetX()+($w-45)/2, $this->GetY()-($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]));
					$this->SetAlpha(1);
				}

				if($row[23]=='checked')
				{
					if($row[22]=='R') $lc = array('width'=>5, 'cap'=>'butt', 'color'=>array(255, 160, 0));
					else $lc = array('width'=>5, 'cap'=>'butt', 'color'=>array(70, 180, 20));
					$this->SetAlpha(0.7);
					$this->StartTransform();
					$this->Rotate(45, $this->GetX()+$w/2, $this->GetY()-45);
					$this->Circle($this->GetX()+$w/2, $this->GetY()-45, $w/2.4, 80, 360, 'C', $lc);
					if($row[22]=='R') $this->SetFillColor(255, 160, 0);
					else $this->SetFillColor(70, 180, 20);
					$this->Arrow($this->GetX()+$w/2 + $w/2.4, $this->GetY()-47.5, $this->GetX()+$w/2 + $w/2.4, $this->GetY()-52.5, '3', '10', '41.4');
					$this->StopTransform();
					$this->SetLineStyle(array('width'=>$lw, 'color'=>array(0, 0, 0)));
					$this->SetAlpha(1);
				}

				$this->Translate(0,$zr);
				if($i % 2 == 1) $this->Translate($w+$zr,-($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])*2-2*$zr);
				if($i > 10)
				{
					if($_SESSION['Frame']=="W" or $_SESSION['Frame']=="O")
					{	//zeichne weisse Linien
						$ls = array('width' => $lw+0.2, 'color' => array(255, 255, 255));
						$this->Line($this->GetX()-$zr, $this->GetY(), $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY(), $ls);
						$this->Line($this->GetX()-$zr, $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]), $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]), $ls);
						$this->Line($this->GetX()-$zr, $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
						$this->Line($this->GetX()-$zr, $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
						for($j='0'; $j<='5'; $j++)
						{
							$this->Line($this->GetX()-$zr-$j*($w+$zr), $this->GetY(), $this->GetX()-$zr-$j*($w+$zr), $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
							$this->Line($this->GetX()-$zr-$w-$j*($w+$zr), $this->GetY(), $this->GetX()-$zr-$w-$j*($w+$zr), $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
						}
					}	//zeichne weisse Linien ende

					$this->AddPage();
					$i=0;
				}
				else $i++;
			}

			if($_SESSION['Frame']=="W" or $_SESSION['Frame']=="O")
			{	//zeichne weisse Linien
				$ls = array('width' => $lw+0.2, 'color' => array(255, 255, 255));
				$this->Line($this->GetX()-$zr, $this->GetY(), $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY(), $ls);
				$this->Line($this->GetX()-$zr, $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]), $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8]), $ls);
				$this->Line($this->GetX()-$zr, $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY()+($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
				$this->Line($this->GetX()-$zr, $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $this->GetX()-$zr-(6*$w+5*$zr), $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
				for($j='0'; $j<='5'; $j++)
				{
					$this->Line($this->GetX()-$zr-$j*($w+$zr), $this->GetY(), $this->GetX()-$zr-$j*($w+$zr), $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
					$this->Line($this->GetX()-$zr-$w-$j*($w+$zr), $this->GetY(), $this->GetX()-$zr-$w-$j*($w+$zr), $this->GetY()+2*($h[0]*7+$h[1]+$h[2]+$h[3]+$h[4]+$h[5]+$h[6]+$h[7]+$h[8])+$zr, $ls);
				}
				$this->SetLineStyle(array('width' => $lw, 'color' => array(0, 0, 0)));
			}	//zeichne weisse Linien ende

		}
	}

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($loggedInUser->display_username);
	$pdf->SetTitle('Yellow Pages');
	$pdf->SetSubject('List');
	$pdf->SetKeywords('Yellow Pages, YP, PDF');

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$zr='0.8';	// Zwischenraum
	if($_SESSION['Frame']=="D") $zr = '0.8';
	if($_SESSION['Frame']=="E") $zr = '0';
	if($_SESSION['Frame']=="O") $zr = '0';
	if($_SESSION['Frame']=="W") $zr = '0.8';

	$w = $_SESSION['FzB'];
	if($w=="") $w='45';
	$calcmargin=(298-(6*$w+5*$zr))/2;
	$pdf->SetMargins($calcmargin, '14', '0');
	$pdf->SetFooterMargin(8.2);

	//set auto page breaks
	$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('freesans', '', 8);

	// add a page
	$pdf->AddPage();

	// benutze internationalen NHM-Code (EN) wenn Sprache in der Datenbank nhm nicht vorhanden
	if($lang == "NO"){$lang="EN";}

//	$result = getTableContents($Treffen, $Betriebsstelle, $Spur, '> 0', $sort);
	$result=$db->sql_query("SELECT * FROM frachtzettel WHERE FUser_ID = '$loggedInUser->user_id' ORDER BY id DESC;");

	//Data loading
	$i=0;
	while($row=$db->sql_fetchrow($result))
	{
		for( $j=0; $j<$row['Menge']; $j++ )
		{
			$data[$i]=array(
			$TEXT['lang-Zielbahnhof'],
			trim($row['Zielbahnhof']),
			$TEXT['lang-Empfaenger'],
			trim($row['Empfaenger']),
			$TEXT['lang-Gewicht'],
			$TEXT['lang-Wagengattung']." (UIC)",
			trim($row['Gewicht'])." t",
			trim($row['Wagengattung']),
			trim($row['Freight']),
			trim($row['Ladung']),
			$TEXT['lang-Versandbahnhof'],
			trim($row['Versandbahnhof']),
			$TEXT['lang-Versender'],
			trim($row['Versender']),
			$TEXT['lang-Ladestelle']." / ".$TEXT['lang-rem'],
			trim($row['LadeEmpfang']),
			$TEXT['lang-Besitzer'].": ".ucwords(str_replace("."," ",$row['User'])),
			$row['Ecol'],
			$row['Vcol'],
			$row['Lcol'],
			$row['Eilgut'], //20
			$row['Wenden'],
			$row['Mehrfach'],
			$row['Stueckgut'],
			$row[$languages[$lang]]);
			$i++;

			if($row['Wenden']=='checked' or $row['Stueckgut']=='checked' )
			{
				if($i % 2 == 0)
				{
					$data[$i]=array(
					$TEXT['lang-Zielbahnhof'],
					trim($row['Zielbahnhof']),
					$TEXT['lang-Empfaenger'],
					trim($row['Empfaenger']),
					$TEXT['lang-Gewicht'],
					$TEXT['lang-Wagengattung']." (UIC)",
					trim($row['Gewicht'])." t",
					trim($row['Wagengattung']),
					trim($row['Freight']),
					trim($row['Ladung']),
					$TEXT['lang-Versandbahnhof'],
					trim($row['Versandbahnhof']),
					$TEXT['lang-Versender'],
					trim($row['Versender']),
					$TEXT['lang-Ladestelle']." / ".$TEXT['lang-rem'],
					trim($row['LadeEmpfang']),
					$TEXT['lang-Besitzer'].": ".ucwords(str_replace("."," ",$row['User'])),
					$row['Ecol'],
					$row['Vcol'],
					$row['Lcol'],
					$row['Eilgut'], //20
					$row['Wenden'],
					$row['Mehrfach'],
					$row['Stueckgut'],
					$row[$languages[$lang]]);
					$i++;
				}

				if($row['Wenden']=='checked') 
				{ 
					$fracht = 'Leer zurück';
					$gewicht = '';
				}

				if($row['Stueckgut']=='checked') $parcel = 'Güterschuppen';
				else $parcel = '';

//$lade=$db->sql_fetchrow($result=$db->sql_query("SELECT Ladestelle FROM fyp WHERE Betriebsstelle = ".$row['Versandbahnhof']." and Anschliesser = ".$row['Versender']));

				$data[$i]=array(
				$TEXT['lang-Zielbahnhof'],
				trim($row['Versandbahnhof']),
				$TEXT['lang-Empfaenger'],
				trim($row['Versender']),
				$TEXT['lang-Gewicht'],
				$TEXT['lang-Wagengattung']." (UIC)",
				$gewicht,
				trim($row['Wagengattung']),
				trim($row['Freight']),
				$fracht,
				$TEXT['lang-Versandbahnhof'],
				trim($row['Zielbahnhof']),
				$TEXT['lang-Versender'],
				trim($row['Empfaenger']),
				$TEXT['lang-Ladestelle']." / ".$TEXT['lang-rem'],
				$parcel,
				$TEXT['lang-Besitzer'].": ".ucwords(str_replace("."," ",$row['User'])),
				$row['Vcol'],
				$row['Ecol'],
				'', // $row['Lcol'] bei Wenden oder Mehrfach keine Farbe auf die Rückseite
				$row['Eilgut'], //20
				$row['Wenden'],
				'R', // $row['Mehrfach'],
				$row['Stueckgut'],
				$row[$languages[$lang]]);
				$i++;
			}
			if($i % 2 == 1) $data[$i]=array();
		}
	}

	// print colored table
	if($data!="") $pdf->ColoredTableFz($data,$w,$zr);

	// ---------------------------------------------------------

	//Close and output PDF document
	if($Treffen=="") $pdf->Output('Frachtzettel_'.$Betriebsstelle.'.pdf', 'I'); //I=inline, D=download
	else $pdf->Output('Frachtzettel_'.$TEXT['lang-meet'].'_'.$Treffen.'.pdf', 'I'); //I=inline, D=download

	exit;
}


// TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT  TTTTTTT
//    T        T        T        T        T        T        T        T        T        T        T
//    T        T        T        T        T        T        T        T        T        T        T
//    T        T        T        T        T        T        T        T        T        T        T
//    T        T        T        T        T        T        T        T        T        T        T
//    T        T        T        T        T        T        T        T        T        T        T

// Telefonliste PDF output =========================================================================

if(@$_REQUEST['action']=="gettel")
{
	if($lang=='DE') require_once('lib/tcpdf/config/lang/ger.php');
	elseif($lang=='NL') require_once('lib/tcpdf/config/lang/nld.php');
	elseif($lang=='DA') require_once('lib/tcpdf/config/lang/dan.php');
	else require_once('lib/tcpdf/config/lang/eng.php');

	require_once('lib/tcpdf/tcpdf.php');

	$Treffen = getVariableFromQueryStringOrSession('Treffen');

	// extend TCPF with custom functions
	class MYPDF extends TCPDF
	{
		// Colored table
		public function ColoredTelTable($header,$data)
		{
			$this->SetFillColor(204, 215, 255); // Lightblue
			$this->SetLineWidth(0.2);

			// Header
			$h = array(14, 80, 80, 11);
			$w = array(14, 80, 80, 11);
			$num_headers = count($header);

			$this->SetFont('', '', 12);
			$this->Cell($h[0], 8, $header[1], 1, 0, 'C', 1, '', 1);
			$this->SetFont('', 'B', 12); // bold
			$this->Cell($h[1], 8, $header[2], 1, 0, 'C', 1, '', 1);
			$this->SetFont('', '', 12);
			$this->Cell($h[2], 8, $header[3], 1, 0, 'C', 1, '', 1);
			$this->SetFont('', 'B', 12); // bold
			$this->Cell($h[3], 8, $header[0], 1, 0, 'C', 1, '', 1);
			$this->Ln();
			$this->Image('img/Telefon.png', '197', '27', '5', '', '',  '',  'Y', true, 300, '', false, false, '', false, false, true);					

			// Data
			$fill = 0;
			$lastkbz = "";
			
			foreach($data as $row)
			{
				// Color and font restoration
				$this->SetFillColor(224, 235, 255);
				$this->SetTextColor(0);
				$this->SetFont('', 'B', 12); // bold

				if($lastkbz!=$row[1]) {
					$this->SetFont('', '', 12);
					$this->Cell($w[0], 6.08, $row[1], 'L,R,T', 0, 'C', $fill, '', 1);
					$this->SetFont('', 'B', 12); // bold
					$this->Cell($w[1], 6.08, $row[2], 'L,R,T', 0, 'L', $fill, '', 1);
					$this->SetFont('', '', 12); 
					$this->Cell($w[2], 6.08, $row[3], 'L,R,T', 0, 'L', $fill, '', 1); 
					$this->SetFont('', 'B', 12); // bold
					$this->Cell($w[3], 6.08, $row[0].' ', 'L,R,T', 0, 'R', $fill, '', 1);
					}
				else {
					$this->SetFont('', '', 12);
					$this->Cell($w[0], 6.08, '', 'L,R', 0, 'C', $fill, '', 1);
					$this->SetFont('', 'B', 12); // bold
					$this->Cell($w[1], 6.08, '', 'L,R', 0, 'L', $fill, '', 1);
					$this->SetFont('', '', 12);
					$this->Cell($w[2], 6.08, $row[3], 'L,R', 0, 'L', $fill, '', 1); 
					$this->SetFont('', 'B', 12); // bold
					$this->Cell($w[3], 6.08, '', 'L,R', 0, 'R', $fill, '', 1);
					}
						
				$this->Ln();
				$fill=!$fill;

				$i++;
				if($i>41)
				{
					$i=0;
					$this->Cell(array_sum($w), 0, '', 'T'); // Abschlusslinie
					$this->Ln();
					$this->AddPage();
					$this->SetFillColor(204, 215, 255); // Lightblue
					$this->SetLineWidth(0.1);
					$this->SetFont('', 'B', 11); // bold

					$this->SetFont('', '', 12);
					$this->Cell($h[0], 8, $header[1], 1, 0, 'C', 1, '', 1);
					$this->SetFont('', 'B', 12); // bold
					$this->Cell($h[1], 8, $header[2], 1, 0, 'C', 1, '', 1);
					$this->SetFont('', '', 12);
					$this->Cell($h[2], 8, $header[3], 1, 0, 'C', 1, '', 1);
					$this->SetFont('', 'B', 12); // bold
					$this->Cell($h[3], 8, $header[0], 1, 0, 'C', 1, '', 1);
					$this->Ln();
					$this->Image('img/Telefon.png', '197', '27', '5', '', '',  '', 'Y',  true, 300,  '', false, false, '', false, false, true);					
				}
				$lastkbz = $row[1];
			}
			$this->Cell(array_sum($w), 0, '', 'T'); // Abschlusslinie
		}
	}

	// create new PDF document
	$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Georg Ziegler');
	$pdf->SetTitle('Yellow Pages');
	$pdf->SetSubject('List');
	$pdf->SetKeywords('Yellow Pages, PDF');

	// set default header data
	$pdf->SetHeaderData('', '', $TEXT['lang-tel']."                         ".
	$Treffen, $TEXT['lang-subhead']); //diese zwei Zeilen gehören zusammen

	// remove default header/footer
	$pdf->setPrintHeader(true);
	$pdf->setPrintFooter(true);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(20, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(8.2);

	//set auto page breaks
	$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('freesans', '', 11);

	// add a page
	$pdf->AddPage();

	//Column titles
	$header = array('', 'Kbz.', $TEXT['lang-station'], $TEXT['lang-an']." / ".$TEXT['lang-rem']);

	// benutze internationalen NHM-Code (EN) wenn Sprache in der Datenbank nhm nicht vorhanden
	if($lang == "NO"){$lang="EN";}

	$query = "SELECT treffen.*, bahnhof.Spur, bahnhof.Kurzbezeichnung, bahnhof.Art FROM treffen LEFT JOIN bahnhof on bahnhof.id = treffen.Bhf_ID 
	WHERE Treffen = '" . escape($Treffen) . "' ORDER BY Betriebsstelle;";
	$result=$db->sql_query($query);

	//Data loading
	$i=0;
	while( $row=$db->sql_fetchrow($result) )
	{
		if($row['Anschliesser'] != "") $row['Anschliesser'] = trim(substr($row['Anschliesser'],0 ,strrpos($row['Anschliesser'], '['))) ; // entferne [xx]
		$trenner=" / ";
		if($row['Anschliesser'] == "" or trim(substr($row['Trf_Bem'],strpos($row['Trf_Bem'],']')+1)) == "") $trenner = "";
		if($row['Telefon']!=0) $Tel=$row['Telefon']; else $Tel='';
		$data[$i]=array(
		$Tel,
		$row['Kurzbezeichnung'],
		$row['Betriebsstelle'],
		$row['Anschliesser'].$trenner.trim(substr($row['Trf_Bem'],strpos($row['Trf_Bem'],']')+1)),

		$row[$languages[$lang]]);
		$i++;
	}

	// print colored table
	if($data!="")
	{
//		$pdf->Rect('194', '25.5', '11', '8','F' ,'' , array(204, 215, 255));
//		$pdf->Image('img/Telefon.png', '197', '27', '5', '', '',  '',  'Y',  true, 300,  '',   false, false,  '',  false, false, true);
		$pdf->ColoredTelTable($header, $data);
	}
	// ---------------------------------------------------------

	//Close and output PDF document
	$pdf->Output('Tel_'.$TEXT['lang-meet'].'_'.$Treffen.'.pdf', 'I'); //I=inline, D=download

	exit;
}


// BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB
// B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B
// BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB
// B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B
// B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B  B    B
// BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB   BBBBB

// Bahnhofsdatenblatt PDF output ========================================================================

if(@$_REQUEST['action']=="bhf")
{
	if($lang=='DE') require_once('lib/tcpdf/config/lang/ger.php');
	elseif($lang=='NL') require_once('lib/tcpdf/config/lang/nld.php');
	elseif($lang=='DA') require_once('lib/tcpdf/config/lang/dan.php');
	else require_once('lib/tcpdf/config/lang/eng.php');

	require_once('lib/tcpdf/tcpdf.php');

	if($Bhf_ID!="")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '".round($Bhf_ID)."'"));
		$Spur = $row['Spur'];
		$Betriebsstelle = $row['Haltestelle'];
	}	else
	{
		if($Spur!="")$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle' and Spur = '$Spur';"));
		else $row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle';"));
		$Bhf_ID= $row['id'];
	}

	// extend TCPF with custom functions
	class MYPDF extends TCPDF
	{
		// Colored table
		public function ColoredBHFTable($header,$data)
		{
			//set auto page breaks
			$this->SetAutoPageBreak(FALSE);

			$this->SetFillColor(230, 230, 230); // Grey
			$this->SetLineWidth(0.2);
			$this->SetFont('', 'B', 11); // bold
			// Header
			$h = array(35, 15, 22, 40, 22.5, 22.5, 30, 15, 22, 128);
			$w = array(35, 7, 7, 40, 45, 30, 15, 7);
			$num_headers = count($header);

			//$i=0;
			//for($j = 0; $j < $num_headers; ++$j) {
			//	$this->Cell($h[$j], 7, $header[$j], 1, 0, 'C', 1,'' ,1 );
			//}

			$this->Cell($h[0], 7, $header[0], 'L,R,B', 0, 'C', 1, '', 1 );

			$this->StartTransform();
				$this->Rotate(90, 0, 0);
				$this->Translate(-82, 25);
				$this->Cell($h[1], 7, $header[1], 'L,B,T', 0, 'L', 1, '', 1 );
			$this->StopTransform();

			$this->StartTransform();
				$this->Rotate(90, 0, 0);
				$this->Translate(-97, 32);
				$this->Cell($h[2], 7, $header[2], 'L,B,T', 0, 'L', 1, '', 1 );
			$this->StopTransform();

			$this->StartTransform();
				$this->Translate(-23, 0);
				$this->Cell($h[3], 7, $header[3], 'L,R,B', 0, 'C', 1, '', 1 );
				$this->Cell($h[4], 7, $header[4], 'L,B', 0, 'L', 1, '', 1, '', '', 'T' );
				$this->Cell($h[5], 7, $header[5], 'R,B', 0, 'R', 1, '', 1, '', '', 'B' );
				$this->Cell($h[6], 7, $header[6], 'L,R,B', 0, 'L', 1, '', 1 );
				$this->Cell($h[7], 7, $header[7], 'L,R,B', 0, 'L', 1, '', 1 );
				$this->Rotate(90, 0, 0);
				$this->Translate(-249,192);
				$this->Cell($h[8], 7, $header[8], 'L,B,T', 0, 'L', 1, '', 1 );
			$this->StopTransform();

			$this->StartTransform();
				$this->Translate(-174.5, -15);
	
				$this->SetFont('', '', 18); // typ
				$swh_typ = $this->GetStringWidth($header[9]) + 2 ;
				$this->Cell($swh_typ, 8, $header[9], 0, 0, 'C', 0, '', 1 );
							
				$this->SetFont('', 'B', 18); // name
				$swh_bhf = $this->GetStringWidth($header[10]) ;
				$this->Cell($swh_bhf, 8, $header[10], 0, 0, 'C', 0, '', 0 );
	
				$this->SetFont('', '', 16); // kbz
				$swh_kbz = $this->GetStringWidth($header[11]) - 1 ;
				$this->Cell($swh_kbz, 8, $header[11], 0, 0, 'C', 0, '', 0 );
	
				$this->Translate(- $swh_typ - $swh_bhf - $swh_kbz, 6);
				$this->SetFont('', '', 13); // remark
				$swh_rem = 129.5 - $swh_typ - $swh_bhf - $swh_kbz;
				$this->Cell(129.5, 7, $header[12], 0, 0, 'L', 0, '', 1 );
				
			$this->StopTransform();

			$this->Ln();
			// Data
			$fill = 0;
			$bgfill = 1;
			foreach($data as $row)
			{
				if($row[7]==0) break; // no more yellow pages (wagen/woche)

				$Tline="";
				if($row[1]!=$SameTrack) $Tline = "T";
				if($row[2]=='1') $row[2]="";

				if($row[8]==0)
				{
					if($fill==0) $this->SetFillColor(244, 250, 255);
					else $this->SetFillColor(224, 235, 255);
				}
				else
				{
					if($fill==0) $this->SetFillColor(255, 255, 240);
					else $this->SetFillColor(255, 255, 180);
				}

				$this->SetFont('', '', 10);
				$this->Cell($w[0], 5, $row[0], 'LR'.$Tline, 0, 'C', $bgfill,'' ,1 );

				$this->SetFont('', 'B');
				if(strlen($row[1]) > 4) $this->SetFont('', '', 8);
				$this->Cell($w[1], 5, $row[1], 'LR'.$Tline, 0, 'C', $bgfill,'' ,1 );
				$this->SetFont('', '', 11);

				$this->Cell($w[2], 5, $row[2], 'LR'.$Tline, 0, 'C', $bgfill,'' ,1 );
				$this->Cell($w[3], 5, $row[3], 'LR'.$Tline, 0, 'C', $bgfill,'' ,1 );

				// Ein-/Ausgangsfracht links oder rechtsbündig schreiben
				if($row[8]==0) $this->Cell($w[4], 5, $row[4], 'LR'.$Tline, 0, 'L', $bgfill,'' ,1 );
				else $this->Cell($w[4], 5, $row[4], 'LR'.$Tline, 0, 'R', $bgfill,'' ,1 );

				$this->Cell($w[5], 5, $row[5], 'LR'.$Tline, 0, 'C', $bgfill,'' ,1 );
				$this->Cell($w[6], 5, $row[6], 'LR'.$Tline, 0, 'C', $bgfill,'' ,1 );
				$this->SetFont('', 'B' );

				// Wagen/Woche links oder rechtsbündig je nach Ein-/Ausgangsfracht schreiben
				if($row[8]==0) $this->Cell($w[7], 5, $row[7], 'LR'.$Tline, 0, 'L', $bgfill,'' ,1 );
				else $this->Cell($w[7], 5, $row[7], 'LR'.$Tline, 0, 'R', $bgfill,'' ,1 );

				$this->SetFont('', '' );
				if($row[8]==0) $this->Cell( 5, 5, '⇐', '', 0, 'C', 0,'' ,1 );
				else $this->Cell( 5, 5, '→', '', 0, 'C', 0,'' ,1 );

				$this->Ln();

				$fill=!$fill;

				$i++;
				if($i>49)
				{
					$i=0;
					
					$this->AddPage();

					$this->SetFillColor(230, 230, 230); // Grey
					$this->SetLineWidth(0.2);
					$this->SetFont('', 'B', 11); // bold

					$this->Cell($h[0], 7, $header[0], 'L,R,B', 0, 'C', 1, '', 1 );
		
					$this->StartTransform();
					$this->Rotate(90, 0, 0);
					$this->Translate(-82, 25);
					$this->Cell($h[1], 7, $header[1], 'L,B,T', 0, 'L', 1, '', 1 );
					$this->StopTransform();
		
					$this->StartTransform();
					$this->Rotate(90, 0, 0);
					$this->Translate(-97, 32);
					$this->Cell($h[2], 7, $header[2], 'L,B,T', 0, 'L', 1, '', 1 );
					$this->StopTransform();
		
					$this->StartTransform();
					$this->Translate(-23, 0);
					$this->Cell($h[3], 7, $header[3], 'L,R,B', 0, 'C', 1, '', 1 );
					$this->Cell($h[4], 7, $header[4], 'L,B', 0, 'L', 1, '', 1, '', '', 'T' );
					$this->Cell($h[5], 7, $header[5], 'R,B', 0, 'R', 1, '', 1, '', '', 'B' );
					$this->Cell($h[6], 7, $header[6], 'L,R,B', 0, 'L', 1, '', 1 );
					$this->Cell($h[7], 7, $header[7], 'L,R,B', 0, 'L', 1, '', 1 );
					$this->Rotate(90, 0, 0);
					$this->Translate(-249,192);
					$this->Cell($h[8], 7, $header[8], 'L,B,T', 0, 'L', 1, '', 1 );
					$this->StopTransform();
		
					$this->StartTransform();
					$this->Translate(-174, -15);
					$this->SetFillColor(255, 255, 255); // White
					$this->SetLineWidth(0.2);
					$this->SetFont('', 'B', 16); // bold
					$this->Cell($h[9], 7, $header[9], 0, 0, 'C', 1, '', 1 );
					$this->StopTransform();

					$this->Ln();
				}
				$SameTrack=$row[1];
			}
			$this->Cell(array_sum($w), 0, '', 'T'); // Abschlusslinie
		}


		public function ColoredTrackTable($header,$data)
		{	// Colored table for track list
			$SameTrack="";
			$SameType="";
			$this->SetFillColor(230, 230, 230); // Grey
			$this->SetLineWidth(0.2);
			$this->SetFont('', 'B', 11); // bold
			// Header
			$w = array(15, 20, 17, 17, 5, 45, 70.5);
			$num_headers = count($header);

			$i=0;
			for($j = 0; $j < $num_headers; ++$j)
			{
				if($j=='3')
				{
					$this->SetFont('', 'B', 8);
					$this->MultiCell($w[$j], 8, $header[$j], 1, 'C', 1, 0, '', '', true, 0, false, true, 8, 'M', true);
					$this->SetFont('', 'B', 11);
				}
/*				elseif($j=='4')
				{	// rotate color header
					$this->StartTransform();
					$this->Rotate(90, 0, 0);
					$this->Translate(-181.5, -5.5); // depends on picture size
					$this->Cell(8, $w[$j], $header[$j], 1, 0, 'C', 1, '', 1);
					$this->StopTransform();
				}
				elseif($j>'4')
				{	// shift cells left after rotated cell
					$this->StartTransform();
					$this->Translate(-3, 0);
					$this->Cell($w[$j], 8, $header[$j], 1, 0, 'C', 1, '', 1);
					$this->StopTransform();
				}
*/				else $this->Cell($w[$j], 8, $header[$j], 1, 0, 'C', 1, '', 1);
			}
			$this->Ln();
			// Data
			foreach($data as $row)
			{	// Color and font restoration
				$this->SetFillColor(215, 255, 215); // Lightgreen
				$this->SetTextColor(0);
				$this->SetFont('', '', 10);
				for($j = 0; $j < $num_headers; ++$j)
				{
					$Dat = '';
					$T = '';
					if($j!=4) $Dat = $row[$j]; 
					
					if($row[0]!=$SameTrack or $row[1]!=$SameType)
					{
						if($row[7]!="Main") $T = ',T';
					}
					
					$this->SetFillColor(215, 255, 215); // Lightgreen
					$fill=$fillInterlace;
					$B='';
					if($j==4)
					{	// Track color code
						if($row[4]=='') $row[4] = 'FFFFFF';
						$this->SetFillColorArray(HexToRGB($row[4]));
						$fill = 1;
						$B = ',B';
						$T = ',T';
					}
					$this->Cell($w[$j], 5, $Dat, 'L,R'.$T.$B, 0, 'C', $fill, '', 1);
				}
				$this->Ln();

				$SameTrack=$row[0];
				$SameType = $row[1];
				$fillInterlace=!$fillInterlace;
			}
			$this->Cell(array_sum($w), 0, '', 'T'); // Abschlusslinie
			$this->Ln();
		}
	}

	// create new PDF document
	$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($loggedInUser->display_username);
	$pdf->SetTitle('Bahnhofsdatenblatt');
	$pdf->SetSubject('List');
	$pdf->SetKeywords('Bahnhofsdatenblatt, YP, PDF');


	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $TEXT['lang-head']."                                                        ".
	$Treffen, $TEXT['lang-subhead']); //diese zwei Zeilen gehören zusammen

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(true);

	// set header and footer fonts
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	//set margins
	$pdf->SetFooterMargin(12);

	//set margins
	$pdf->SetMargins(15, 10, PDF_MARGIN_RIGHT);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 15);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

	$pdf->AddPage('P', 'A4');

	//Column titles
	$header = array(
	$TEXT['lang-track'],
	$TEXT['lang-typ'],
	$TEXT['lang-tl'],
	str_replace("<br>","\n",$TEXT['lang-bs']),
	$TEXT['lang-color'],
	$TEXT['lang-ls'],
	$TEXT['lang-rem'],
	);

	//Data loading
	$Gleisliste = getGleisliste($Bhf_ID);

	$i=0;
	while($rowGls=$db->sql_fetchrow($Gleisliste))
	{
		$Lng=$rowGls['Gleislaenge'];
		if($Lng=='1') $Lng='';
		$BhLng=$rowGls['Bahnsteiglaenge'];
		if($BhLng=='0') $BhLng='';

		$Typ = $rowGls['Gleisart'];
		if($Typ=='Main')$Typ_lang=$TEXT['lang-mt'];
		elseif($Typ=='Siding')$Typ_lang=$TEXT['lang-rangier'];
		elseif($Typ=='Storage Siding')$Typ_lang=$TEXT['lang-abstell'];
		elseif($Typ=='Depot')$Typ_lang=$TEXT['lang-bw'];
		else $Typ_lang=$Art;

		$data[$i]=array(
		$rowGls['Gleisname'],
		$Typ_lang,
		$Lng,
		$BhLng,
		$rowGls['LadeFarbe'],
		$rowGls['Ladestelle'],
		$rowGls['Gl_Bem'],
		$rowGls['Gleisart'],
		);
		$i++;
	}

	// Überschrift
	if($row['Art']=="Station") $BhfTyp="Bf";
	if($row['Art']=="Connect") $BhfTyp="An";
	if($row['Art']=="Stop") $BhfTyp="Hp";
	if($row['Art']=="Block")$BhfTyp="Bl";
	if($row['Art']=="SBF") $BhfTyp="Sbf";

	if($row['Bhf_Bem']!="") 
	{
		if(isset($_REQUEST['translate']) and strtolower($row['Language'])!=strtolower($lang)) 
		{
			$row['Bhf_Bem'] = ucfirst(translates(rawurlencode($row['Bhf_Bem']), strtolower($row['Language']), strtolower($lang)));
		}
		$Rem="(".$row['Bhf_Bem'].")";
	}

	$Haltestelle=$row['Haltestelle'];
	$Kbz=$row['Kurzbezeichnung'];	
	

	// calculate header text pixel width
	
	$pdf->SetFont('', '', 24);
	$text_BhfTyp_width = $pdf->GetStringWidth($BhfTyp);
	$pdf->Cell($text_BhfTyp_width + 1, 12, $BhfTyp, 0, 0, 'L', 0, '', 1, '', '', 'B' );

	$pdf->SetFont('', 'B', 24);
	$text_Haltestelle_width = $pdf->GetStringWidth($Haltestelle);
	$pdf->Cell($text_Haltestelle_width, 12, $Haltestelle, 0, 0, 'C', 0, '', 1, '', '', 'B' );

	$pdf->SetFont('', '', 22);
	$text_Kbz_width = $pdf->GetStringWidth("[".$Kbz."]");
	$pdf->Cell($text_Kbz_width - 1, 12, "[".$Kbz."]", 0, 0, 'C', 0, '', 1, '', '', 'B' );

	$pdf->SetFont('', '', 18);
	$text_Rem_width = $pdf->GetStringWidth($Rem);
	$text_width = $text_BhfTyp_width + $text_Haltestelle_width + $text_Kbz_width + $text_Rem_width;

	$shift = 0;
	$align = 'C';
	if($text_width>250) // line width = 187
	{
		$pdf->Ln();
		$shift = -4;
		$align = 'T';
	}
	$pdf->SetFont('', '', 16);
	$pdf->Cell('', 16 + $shift, $Rem, 0, 1, 'L', 0, '', 1, '', '', $align);

/*
// EOF muss am Anfang der Zeile stehen
$html = <<<EOF
<font size="24">$BhfTyp <b>$Haltestelle </b>[$Kbz] </font><font size="18">$Rem</font>
EOF;
	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');
*/
	$tr = new GoogleTranslate(strtolower($row['Language']), strtolower($lang)); // set in/out language

//	dejavusans
//	dejavusanscondensed
//	dejavusansmono
//	dejavuserif
//	dejavuserifcondensed
//	freemono
//	freesans
//	freeserif
//	a, b, blockquote, br, dd, del, div, dl, dt, em, font, h1, h2, h3, h4, h5, h6, hr, i, img, li, ol,
//	p, pre, small, span, strong, sub, sup, table, tcpdf, td, th, thead, tr, tt, u, ul

// install fonts
//$fontname = $pdf->addTTFfont('fonts/impact.ttf', 'TrueTypeUnicode', '', 32);
// ./tools/tcpdf_addfont.php -b -t TrueTypeUnicode -f 33 -i "fonts/comicsansmsb.ttf"

	if(strlen($row['Einleitung'])>4)
	{
		$pdf->setCellHeightRatio(1.2);
		if(preg_match('/[━┃┏┓┗┛┣┫┳┻╋═║╔╗╚╝╠╣╦╩╬╭╮╯╰╱╲╳▖▗▘▝▙▚▛▜▞▟▀▐◢◣◤◥]/',$row['Einleitung'])=='1')
		{
			$pdf->setCellHeightRatio(1);
		}
		if(substr($row['Einleitung'],0,2)!='<h')
		{
			$pdf->SetFont('freesans', 'B', 12);
			$pdf->writeHTML($TEXT['lang-einleitung'], true, false, true, false, '');
		}
		$pdf->SetFont('freeserif', '', 11);
//		$row['Einleitung'] = html_entity_decode($row['Einleitung']);
		$row['Einleitung'] = str_replace('<div><br></div>','<br>',$row['Einleitung']);
		$row['Einleitung'] = str_replace('size="1"','size="8pt"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('size="2"','size="10pt"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('size="4"','size="14pt"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('size="5"','size="18pt"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('size="6"','size="24pt"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('face="arial"','face="freesans"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('face="times"','face="freeserif"',$row['Einleitung']);
		$row['Einleitung'] = str_replace('face="courier"','face="freemono"',$row['Einleitung']);

		$row['Einleitung'] = str_replace('font-family: Arial','font-family: freesans',$row['Einleitung']);
		$row['Einleitung'] = str_replace('font-family: Courier','font-family: freemono',$row['Einleitung']);
		
		if(isset($_REQUEST['translate']) and strtolower($row['Language'])!=strtolower($lang)) 
		{
			$row['Einleitung'] = ucfirst(translates(rawurlencode(strip_tags($row['Einleitung'])), strtolower($row['Language']), strtolower($lang)));
		}
		
		$pdf->writeHTML($row['Einleitung'], true, false, true, false, '');
		$pdf->SetFont('', '', 4);
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, true, 0);
	}

	//Bild
	$Bild="Bilder/pic_Bhf_ID_".$Bhf_ID;
	if(file_exists($Bild))
	{
		//                 x   y   w   h  type link align resize dpi palign ismask imgmsk border fitbox hidden fitonpage alt altimgs
		$pdf->Image($Bild, '', '', '', '', '',  '',  'N',  true, 300,  '',   false, false,  '',  false, false, true);
		$pdf->SetFont('', '', 1);
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, true, 0);
	}

	if($data!="")
	{
		// set font
		$pdf->SetFont('freesans', '', 11);
		// print colored table
		$pdf->ColoredTrackTable($header, $data);
	}

	if(strlen($row['Beschreibung'])>4)
	{
		$pdf->setCellHeightRatio(1.2);
		if(preg_match('/[━┃┏┓┗┛┣┫┳┻╋═║╔╗╚╝╠╣╦╩╬╭╮╯╰╱╲╳▖▗▘▝▙▚▛▜▞▟▀▐◢◣◤◥]/',$row['Beschreibung'])=='1')
		{
			$pdf->setCellHeightRatio(1);
		}
		if(substr($row['Beschreibung'],0,2)!='<h')
		{
			$pdf->SetFont('freesans', 'B', 12);
			$pdf->writeHTML($TEXT['lang-beschreibung'], true, false, true, false, '');
		}
		$pdf->SetFont('freeserif', '', 11);
//		$row['Beschreibung'] = html_entity_decode($row['Beschreibung']);
		$row['Beschreibung'] = str_replace('<div><br></div>','<br>',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('size="1"','size="8pt"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('size="2"','size="10pt"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('size="4"','size="14pt"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('size="5"','size="18pt"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('size="6"','size="24pt"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('face="arial"','face="freesans"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('face="times"','face="freeserif"',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('face="courier"','face="freemono"',$row['Beschreibung']);
		
		$row['Beschreibung'] = str_replace('font-family: Arial','font-family: freesans',$row['Beschreibung']);
		$row['Beschreibung'] = str_replace('font-family: Courier','font-family: freemono',$row['Beschreibung']);

		if(isset($_REQUEST['translate']) and strtolower($row['Language'])!=strtolower($lang)) 
		{
			$row['Beschreibung'] = ucfirst(translates(rawurlencode(strip_tags($row['Beschreibung'])), strtolower($row['Language']), strtolower($lang)));
		}
		
		$pdf->writeHTML($row['Beschreibung'], true, false, true, false, '');
		$pdf->SetFont('', '', 4);
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, true, 0);
	}

	if(strlen($row['Personenverkehr'])>4)
	{
		$pdf->setCellHeightRatio(1.2);
		if(preg_match('/[━┃┏┓┗┛┣┫┳┻╋═║╔╗╚╝╠╣╦╩╬╭╮╯╰╱╲╳▖▗▘▝▙▚▛▜▞▟▀▐◢◣◤◥]/',$row['Personenverkehr'])=='1')
		{
			$pdf->setCellHeightRatio(1);
		}
		if(substr($row['Personenverkehr'],0,2)!='<h')
		{
			$pdf->SetFont('freesans', 'B', 12);
			$pdf->writeHTML($TEXT['lang-personenverkehr'], true, false, true, false, '');
		}
		$pdf->SetFont('freeserif', '', 11);
//		$row['Personenverkehr'] = html_entity_decode($row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('<div><br></div>','<br>',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('size="1"','size="8pt"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('size="2"','size="10pt"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('size="4"','size="14pt"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('size="5"','size="18pt"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('size="6"','size="24pt"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('face="arial"','face="freesans"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('face="times"','face="freeserif"',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('face="courier"','face="freemono"',$row['Personenverkehr']);
		
		$row['Personenverkehr'] = str_replace('font-family: Arial','font-family: freesans',$row['Personenverkehr']);
		$row['Personenverkehr'] = str_replace('font-family: Courier','font-family: freemono',$row['Personenverkehr']);

		if(isset($_REQUEST['translate']) and strtolower($row['Language'])!=strtolower($lang))
		{ 
			$row['Personenverkehr'] = ucfirst(translates(rawurlencode(strip_tags($row['Personenverkehr'])), strtolower($row['Language']), strtolower($lang)));
		}
		
		$pdf->writeHTML($row['Personenverkehr'], true, false, true, false, '');
		$pdf->SetFont('', '', 4);
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, true, 0);
	}

	if(strlen($row['Frachtverkehr'])>4)
	{
		$pdf->setCellHeightRatio(1.2);
		if(preg_match('/[━┃┏┓┗┛┣┫┳┻╋═║╔╗╚╝╠╣╦╩╬╭╮╯╰╱╲╳▖▗▘▝▙▚▛▜▞▟▀▐◢◣◤◥]/',$row['Frachtverkehr'])=='1')
		{
			$pdf->setCellHeightRatio(1);
		}
		if(substr($row['Frachtverkehr'],0,2)!='<h')
		{
			$pdf->SetFont('freesans', 'B', 12);
			$pdf->writeHTML($TEXT['lang-frachtverkehr'], true, false, true, false, '');
		}
		$pdf->SetFont('freeserif', '', 11);
//		$row['Frachtverkehr'] = html_entity_decode($row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('<div><br></div>','<br>',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('size="1"','size="8pt"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('size="2"','size="10pt"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('size="4"','size="14pt"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('size="5"','size="18pt"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('size="6"','size="24pt"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('face="arial"','face="freesans"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('face="times"','face="freeserif"',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('face="courier"','face="freemono"',$row['Frachtverkehr']);
		
		$row['Frachtverkehr'] = str_replace('font-family: Arial','font-family: freesans',$row['Frachtverkehr']);
		$row['Frachtverkehr'] = str_replace('font-family: Courier','font-family: freemono',$row['Frachtverkehr']);

		if(isset($_REQUEST['translate']) and strtolower($row['Language'])!=strtolower($lang)) 
		{
			$row['Frachtverkehr'] = ucfirst(translates((($row['Frachtverkehr'])), strtolower($row['Language']), strtolower($lang)));
		}
		
		$pdf->writeHTML($row['Frachtverkehr'], true, false, true, false, '');
	}

	//set margins
	$pdf->SetMargins(15, 25, PDF_MARGIN_RIGHT);

	// set font
	$pdf->SetFont('freesans', '', 11);

		//Column titles
		$header = array(
		$TEXT['lang-ls'],
		$TEXT['lang-track'],
		$TEXT['lang-tl'],
		$TEXT['lang-an'],
		$TEXT['lang-in'],
		$TEXT['lang-out'],
		$TEXT['lang-int'],
		$TEXT['lang-class'],
		$TEXT['lang-wagon']." ".$TEXT['lang-week'],
		$BhfTyp,
		$row[Haltestelle],
		" [".$row[Kurzbezeichnung]."] ",
		$Rem,
		);

		// benutze internationalen NHM-Code (EN) wenn Sprache in der Datenbank nhm nicht vorhanden
		if($lang == "NO"){$lang="EN";}

		$resultFYP = getTableContents('', $Betriebsstelle, $Spur, '>= 0', '(0+gleise.Gleisname)=0, LPAD(bin(gleise.Gleisname), 50, 0), LPAD(gleise.Gleisname, 50, 0), 
		(0+fyp.NHM_Code)>0, gleise.Ladestelle, fyp.Anschliesser, fyp.Produktbeschreibung');

		//Data loading
		$i=0;
		while($rowFYP=$db->sql_fetchrow($resultFYP) )
		{
			if(isset($_REQUEST['translate']) and strtolower($row['Language'])!=strtolower($lang)) 
			{
				$rowFYP['Produktbeschreibung'] = ucfirst(translates(rawurlencode($rowFYP['Produktbeschreibung']), strtolower($rowFYP['Language']), strtolower($lang)));
				if($rowFYP['Produktbeschreibung']=="") // try translating from english
				$rowFYP['Produktbeschreibung'] = ucfirst(translates(rawurlencode($rowFYP['Product_Description']), 'en', strtolower($lang)));
				if($rowFYP['Produktbeschreibung']=="") // try translating from polish site
				$Product_Description = ucfirst(translatepl(rawurlencode($row['Produktbeschreibung']), 'auto', strtolower($lang)));
			}
			
			$data[$i]=array(
			$rowFYP['Ladestelle'],
			$rowFYP['Gleisname'],
			$rowFYP['Gleislaenge'],
			$rowFYP['Anschliesser'],
			$rowFYP['Produktbeschreibung'],
			$rowFYP['Product_Description'],
			$rowFYP['Wagengattung'],
			$rowFYP['Wagen_Woche'],
			$rowFYP['NHM_Code'],
			$rowFYP[$languages[$lang]]
			);
			$i++;
		}

		if($data!="")
		{
			// add a page
			$pdf->AddPage();

			// print colored table
			$pdf->ColoredBHFTable($header, $data);
		}

	// ---------------------------------------------------------

	//Close and output PDF document
	$pdf->Output('Bhf_'.$Betriebsstelle, 'I'); //I=inline, D=download
	
	if(isset($_REQUEST['translate'])) unset($_REQUEST['translate']);

	exit;
}



// FFFFFFF  YY    YY  PPPPPPP     FFFFFFF  YY    YY  PPPPPPP     FFFFFFF  YY    YY  PPPPPPP
// F         YY  YY   P      P	  F         YY  YY   P      P	 F         YY  YY   P      P
// FFFFFF     YYYY    PPPPPPP	  FFFFFF     YYYY    PPPPPPP	 FFFFFF     YYYY    PPPPPPP
// F           YY     P			  F           YY     P			 F           YY     P
// F           YY     P			  F           YY     P			 F           YY     P
// F           YY     P			  F           YY     P			 F           YY     P

// FYPages PDF output =====================================================================

if(@$_REQUEST['action']=="getpdf")
{
	if($lang=='DE') require_once('lib/tcpdf/config/lang/ger.php');
	elseif($lang=='NL') require_once('lib/tcpdf/config/lang/nld.php');
	elseif($lang=='DA') require_once('lib/tcpdf/config/lang/dan.php');
	else require_once('lib/tcpdf/config/lang/eng.php');

	require_once('lib/tcpdf/tcpdf.php');

	if($Bhf_ID!="")
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '".round($Bhf_ID)."'"));
		$Spur = $row['Spur'];
		$Betriebsstelle = $row['Haltestelle'];
	}	else
	{
		if($Spur!="")$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle' and Spur = '$Spur';"));
		else $row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle';"));
		$Bhf_ID = $row['id'];
	}

	if(@$_REQUEST['Treffen']!="") $Treffen = getVariableFromQueryStringOrSession('Treffen');
	else $Treffen = "";

	// extend TCPF with custom functions
	class MYPDF extends TCPDF
	{
		// Colored table
		public function ColoredFYPTable($header,$data)
		{
			$this->SetFillColor(255, 255, 0); // Yellow
			$this->SetLineWidth(0.1);
			$this->SetFont('', 'B', 11); // bold

			// Header
			$h = array(18, 14, 7, 54, 45, 55, 52, 36, 6);
			$w = array(18, 21, 54, 45, 55, 52, 36, 6);
			$num_headers = count($header);

			$this->Cell($h[0], 8, $header[0], 1, 0, 'C', 1, '', 1);

			$this->SetCellPaddings('0.4','','0','');
			$this->Cell($h[1], 8, $header[1], 'L,T,B', 0, 'R', 1, '', 1);
			$this->SetFont('', '', 10); // bold
			$this->SetCellPaddings('0.2','','0.3','');
			$this->Cell($h[2], 8, $header[2], 'R,T,B', 0, 'L', 1, '', 2);
			$this->SetCellPaddings('1','','1','');
			$this->SetFont('', 'B', 11); // bold

			$this->Cell($h[3], 8, $header[3], 1, 0, 'C', 1, '', 1);
			$this->Cell($h[4], 8, $header[4], 1, 0, 'C', 1, '', 1);
			$this->Cell($h[5], 8, $header[5], 1, 0, 'C', 1, '', 1);
			$this->Cell($h[6], 8, $header[6], 1, 0, 'C', 1, '', 1);
			$this->Cell($h[7], 8, $header[7], 1, 0, 'C', 1, '', 1);

			$this->StartTransform();
			$this->Rotate(90, 0, 0);
			$this->Translate(-319.5, 260.5);
			$this->Cell(20, $h[8], $header[8], 1, 0, 'C', 1, '', 1);
			$this->Rotate(-90, 0, 0);
			$this->Cell(20, 8, '', 1, 0, 'C', 1,'' ,1 );
			$this->StopTransform();
/*			$i=0;
			for($j = 0; $j < $num_headers; ++$j)
			{
				if($j==8)
				{
					$this->StartTransform();
					$this->Rotate(90, 0, 0);
					$this->Translate(-319.5, 260.5);
					$this->Cell(20, $h[$j], $header[$j], 1, 0, 'C', 1, '', 1);
					$this->Rotate(-90, 0, 0);
					$this->Cell(20, 8, '', 1, 0, 'C', 1,'' ,1 );
					$this->StopTransform();
				}
				else
				{
					$this->Cell($h[$j], 8, $header[$j], 1, 0, 'C', 1, '', 1);
				}
			}
*/
			$this->Ln();
			// Data
			$i=0;
			$fill = 0;
			if($_SESSION['sort']=="prd") $Prod='B';
			elseif($_SESSION['sort']=="int") $Int='B';
			elseif($_SESSION['sort']=="an") $An='B';
			elseif($_SESSION['sort']=="rem") $Bem='B';
			elseif($_SESSION['sort']=="station") $Station='B';
			else $Prod='B';
			foreach($data as $row)
			{
				if($i>17)
				{
					$i=0;
					$this->Cell(array_sum($w), 0, '', 'T'); // Abschlusslinie
					$this->Ln();
					$this->AddPage();
					$this->SetFillColor(255, 255, 0); // Yellow
					$this->SetLineWidth(0.1);
					$this->SetFont('', 'B', 11); // bold

					$this->Cell($h[0], 8, $header[0], 1, 0, 'C', 1, '', 1);

					$this->SetCellPaddings('0.4','','0','');
					$this->Cell($h[1], 8, $header[1], 'L,T,B', 0, 'R', 1, '', 1);
					$this->SetFont('', '', 10); // bold
					$this->SetCellPaddings('0.2','','0.3','');
					$this->Cell($h[2], 8, $header[2], 'R,T,B', 0, 'L', 1, '', 2);
					$this->SetCellPaddings('1','','1','');
					$this->SetFont('', 'B', 11); // bold

					$this->Cell($h[3], 8, $header[3], 1, 0, 'C', 1, '', 1);
					$this->Cell($h[4], 8, $header[4], 1, 0, 'C', 1, '', 1);
					$this->Cell($h[5], 8, $header[5], 1, 0, 'C', 1, '', 1);
					$this->Cell($h[6], 8, $header[6], 1, 0, 'C', 1, '', 1);
					$this->Cell($h[7], 8, $header[7], 1, 0, 'C', 1, '', 1);

					$this->StartTransform();
					$this->Rotate(90, 0, 0);
					$this->Translate(-319.5, 260.5);
					$this->Cell(20, $h[8], $header[8], 1, 0, 'C', 1, '', 1);
					$this->Rotate(-90, 0, 0);
					$this->Cell(20, 8, '', 1, 0, 'C', 1,'' ,1 );
					$this->StopTransform();
/*					for($j = 0; $j < $num_headers; ++$j)
					{
						if($j==8)
						{
							$this->StartTransform();
							$this->Rotate(90, 0, 0);
							$this->Translate(-319.5, 260.5);
							$this->Cell(20, $h[$j], $header[$j], 1, 0, 'C', 1,'' ,1 );
							$this->Rotate(-90, 0, 0);
							$this->Cell(20, 8, '', 1, 0, 'C', 1,'' ,1 );
							$this->StopTransform();
						}
						else
						{
							$this->Cell($h[$j], 8, $header[$j], 1, 0, 'C', 1,'' ,1 );
						}
					}
*/
					$this->Ln();
				}

				// Color and font restoration
				$this->SetFillColor(224, 235, 255);
				$this->SetTextColor(0);
				$this->SetFont('', '', 12);
				$this->Cell($w[0], 5, $row[0], 'L,R,T', 0, 'C', $fill, '', 1);
				$this->Cell($w[1], 5, $row[1], 'L,R,T', 0, 'L', $fill, '', 1);
				$this->SetFont('', $Prod );
				$this->Cell($w[2], 5, $row[2], 'L,R,T', 0, 'L', $fill, '', 1);
				$this->SetFont('', $Int );
				$this->Cell($w[3], 5, $row[3], 'L,R,T', 0, 'L', $fill, '', 1);
				$this->SetFont('', $An );
				$this->Cell($w[4], 5, $row[4], 'L,R,T', 0, 'L', $fill, '', 1);
				$this->SetFont('', $Bem );
				$this->Cell($w[5], 5, $row[5], 'L,R,T', 0, 'L', $fill, '', 1);
				$this->SetFont('', $Station );
				$this->Cell($w[6], 5, $row[6], 'L,R,T', 0, 'L', $fill, '', 1);
				$this->SetFont('', 'B' );
				$this->Cell($w[7], 5, $row[7], 'L,R,T', 0, 'C', $fill, '', 1);
				$this->Ln();

				$this->SetFont('', '', 8);
				$this->Cell(287, 4, $row[8], 'L,R', 0, 'L', $fill, '', 0);
				$this->Ln();
				$fill=!$fill;

				$i++;
			}
			$this->Cell(array_sum($w), 0, '', 'T'); // Abschlusslinie
		}
	}

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($loggedInUser->display_username);
	$pdf->SetTitle('Yellow Pages');
	$pdf->SetSubject('List');
	$pdf->SetKeywords('Yellow Pages, PDF');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $TEXT['lang-head']."                                                        ".
	$Treffen, $TEXT['lang-subhead']); //diese zwei Zeilen gehören zusammen

	// remove default header/footer
	$pdf->setPrintHeader(true);
	$pdf->setPrintFooter(true);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(8.2);

	//set auto page breaks
	$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('freesans', '', 11);

	// add a page
	$pdf->AddPage();

	//Column titles
	$header = array($TEXT['lang-nhm'], $TEXT['lang-class'], '(UIC)', $TEXT['lang-product'],
	$TEXT['lang-int'], $TEXT['lang-an'], $TEXT['lang-ls']." / ".$TEXT['lang-rem'], $TEXT['lang-station'], $TEXT['lang-wagon']." ".$TEXT['lang-week']);

	// benutze internationalen NHM-Code (EN) wenn Sprache in der Datenbank nhm nicht vorhanden
	if($lang == "NO"){$lang="EN";}

	$result = getTableContents($Treffen, $Betriebsstelle, $Spur, '> 0', $sort);

	//Data loading
	$i=0;
	while( $row=$db->sql_fetchrow($result) )
	{

		if($row['Product_Description']=="")
		{
			$row['Product_Description'] = ucfirst(translates(rawurlencode($row['Produktbeschreibung']), strtolower($row['Language']), strtolower($lang)));
			if($row['Product_Description']=="") // try translating from polish site
			$row['Product_Description'] = ucfirst(translatepl(rawurlencode($row['Produktbeschreibung']), 'auto', strtolower($lang)));
		}
		
		if(isset($_REQUEST['translate'])) 
		{
			$row['Produktbeschreibung'] = ucfirst(translates(rawurlencode($row['Produktbeschreibung']), strtolower($row['Language']), strtolower($lang)));
			if($row['Produktbeschreibung']=="") // try translating from english
			$row['Produktbeschreibung'] = ucfirst(translates(rawurlencode($row['Produktbeschreibung']), 'en', strtolower($lang)));
			if($row['Produktbeschreibung']=="") // try translating from polish site
			$row['Produktbeschreibung'] = ucfirst(translatepl(rawurlencode($row['Produktbeschreibung']), 'auto', strtolower($lang)));
		}

		if (isset($row['An_ID']))
		{
			if($row['An_ID']!=0){ 
				$row['Betriebsstelle'] = $row[0];
				if($row['Anschliesser'] =="") $row['Anschliesser'] = $row[28];		
		}}

		if($Treffen=="") $bem = '5'; // [5]
		else $bem = '38'; // [38]

		if(isset($_REQUEST['translate']) and $row[$bem]!="" and strtolower($row['Language'])!=strtolower($lang))
		{ 
			$row[$bem] = ucfirst(translates(rawurlencode($row[$bem]), strtolower($row['Language']), strtolower($lang)));
		}
		
		$trenner=" / ";
		if($row['Ladestelle']=="" or $row[$bem]=="") $trenner="";
		if($row['Gleisname']!="") $LadeBem = $row['Ladestelle']." (".$TEXT['lang-track']." ".$row['Gleisname'].")".$trenner.$row[$bem];
		else $LadeBem = $row['Ladestelle'].$trenner.$row[$bem];

		if($Treffen=="") $Btrst = $row['Betriebsstelle'];
		else $Btrst = $row[26];

		$data[$i]=array(sprintf("%04s",substr($row['NHM_Code'],0,-4))." ".substr($row['NHM_Code'],-4),
		$row['Wagengattung'],
		$row['Produktbeschreibung'],
		$row['Product_Description'],
		$row['Anschliesser'],
		$LadeBem,
		$Btrst,
		$row['Wagen_Woche'],
		$row[$languages[$lang]]);
		$i++;
	}

	// print colored table
	if($data!="") $pdf->ColoredFYPTable($header, $data);

	// ---------------------------------------------------------

	//Close and output PDF document
	if($Treffen=="") $pdf->Output('FYPages_'.$Betriebsstelle.'.pdf', 'I'); //I=inline, D=download
	else $pdf->Output('FYPages_'.$TEXT['lang-meet'].'_'.$Treffen.'.pdf', 'I'); //I=inline, D=download

	if(isset($_REQUEST['translate'])) unset($_REQUEST['translate']);

	exit;
}

