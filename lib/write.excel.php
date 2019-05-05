<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

// FYPages Excel output =========================================================================

if(@$_REQUEST['action']=="getxls")
{
	$Treffen = getVariableFromQueryStringOrSession('Treffen');

	/** Error reporting */
	error_reporting(E_ALL);

	/** PHPExcel */
	require_once 'lib/PHPExcel.php';

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');

	// Set properties
	$objPHPExcel->getProperties()->setCreator("FREMO Yellow Pages")
				->setLastModifiedBy("FREMO Yellow Pages")
				->setTitle("FREMO Yellow Pages")
				->setSubject("FREMO Yellow Pages")
				->setDescription("FREMO Yellow Pages, generated using PHP classes.")
				->setKeywords("xls php")
				->setCategory("");

	// Create header
	if($Treffen=="") $Meeting="";
	else $Meeting = $TEXT['lang-meet'].": ".$Treffen;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $TEXT['lang-head'])
				->setCellValue('A2', $TEXT['lang-subhead'])
				->setCellValue('E1', $TEXT['lang-out'])
				->setCellValue('G1', $Meeting)
				->setCellValue('A10', $TEXT['lang-nhm'])
				->setCellValue('B10', $TEXT['lang-nhm1'])
				->setCellValue('C10', $TEXT['lang-nhm2'])
				->setCellValue('D9', $languages[$lang])
				->setCellValue('D10', $TEXT['lang-desc'])
				->setCellValue('E10', $TEXT['lang-class']." (UIC)")
				->setCellValue('F10', $TEXT['lang-product'])
				->setCellValue('G10', $TEXT['lang-int'])
				->setCellValue('H10', $TEXT['lang-an'])
				->setCellValue('I10', $TEXT['lang-rem'])
				->setCellValue('J10', $TEXT['lang-ls']." (".$TEXT['lang-track'].")")
				->setCellValue('K10', $TEXT['lang-wagon'].' '.$TEXT['lang-week'])
				->setCellValue('L10', $TEXT['lang-station'])
				->setCellValue('M10', $TEXT['lang-kbz'])
				->setCellValue('N10', $TEXT['lang-bvw'])
				->setCellValue('O10', $TEXT['lang-group'])
				->setCellValue('P10', $TEXT['lang-owner'])
				->setCellValue('Q10', $TEXT['lang-email'])
				->setCellValue('R10', 'Row ID');

	// set width
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(26);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(34);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(16);
	$objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(22);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(8);

	$objPHPExcel->getActiveSheet()->getRowDimension('3')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getRowDimension('5')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getRowDimension('6')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getRowDimension('7')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getRowDimension('8')->setVisible(false);

	// set fonts
	$objPHPExcel->getActiveSheet()->getStyle('A1:R2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setSize(20);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getFill()->getStartColor()->setARGB('FFFFFF00');

//	$objPHPExcel->getActiveSheet()->getStyle('A10')->getFont()->setName('Arial Narrow');
//	$objPHPExcel->getActiveSheet()->getStyle('E10')->getFont()->setName('Arial Narrow');
//	$objPHPExcel->getActiveSheet()->getStyle('G10')->getFont()->setName('Arial Narrow');
//	$objPHPExcel->getActiveSheet()->getStyle('K10')->getFont()->setName('Arial Narrow');

	// set fill styles
	//NHM-Code
	$objPHPExcel->getActiveSheet()->getStyle('A:A')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A:A')->getFill()->getStartColor()->setARGB('FFFFFF99');
	// Gattung
	$objPHPExcel->getActiveSheet()->getStyle('E:E')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('E:E')->getFill()->getStartColor()->setARGB('FFCCFFCC');

	// Produktbeschreibung - Anschliesser
	$objPHPExcel->getActiveSheet()->getStyle('F:H')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('F:H')->getFill()->getStartColor()->setARGB('FFFFFF99');

	// Int.Prod.Desc.
	$objPHPExcel->getActiveSheet()->getStyle('G:G')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('G:G')->getFill()->getStartColor()->setARGB('FFCCFFCC');
	// Bemerkung
	$objPHPExcel->getActiveSheet()->getStyle('I:I')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('I:I')->getFill()->getStartColor()->setARGB('FFFFFFCC');

	// for table bahnhof
	// Betriebsstelle
	$objPHPExcel->getActiveSheet()->getStyle('L:L')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('L:L')->getFill()->getStartColor()->setARGB('FFAACCFF');
	// Kurzbezeichnung - Email
	$objPHPExcel->getActiveSheet()->getStyle('M:Q')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('M:Q')->getFill()->getStartColor()->setARGB('FFDDEEFF');
	// Gruppe
	$objPHPExcel->getActiveSheet()->getStyle('O:O')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('O:O')->getFill()->getStartColor()->setARGB('FFAACCFF');

	// for table gleise
	$objPHPExcel->getActiveSheet()->getStyle('J:J')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('J:J')->getFill()->getStartColor()->setARGB('FFCCFFCC');
	$objPHPExcel->getActiveSheet()->getStyle('K:K')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('K:K')->getFill()->getStartColor()->setARGB('FFDFFFDF');

	// row ID in table fyp
	$objPHPExcel->getActiveSheet()->getStyle('R:R')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('R:R')->getFill()->getStartColor()->setARGB('FFFFCCCC');

	$objPHPExcel->getActiveSheet()->getStyle('A1:R9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A1:R9')->getFill()->getStartColor()->setARGB('FFFFFFFF');

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('FREMO_Yellow_Pages');
	//  Get the current sheet with all its newly-set style properties
	$objWorkSheetBase = $objPHPExcel->getSheet();
	//  Create a clone of the current sheet, with all its style properties
	$objWorkSheetAdd = clone $objWorkSheetBase;
	//  Set the newly-cloned sheet title -> Empfang
	$objWorkSheetAdd->setTitle($TEXT['lang-in']);
	//  Attach the newly-cloned sheet to the $objPHPExcel workbook
	$objPHPExcel->addSheet($objWorkSheetAdd);
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(0);
	$objPHPExcel->getActiveSheet()->setCellValue('E1', $TEXT['lang-in']);

// definition for loops
	$xcolA='A';
	$xcolB='B';
	$xcolC='C';
	$xcolD='D';
	$xcolE='E';
	$xcolF='F';
	$xcolG='G';
	$xcolH='H';
	$xcolI='I';
	$xcolJ='J';
	$xcolK='K';
	$xcolL='L';
	$xcolM='M';
	$xcolN='N';
	$xcolO='O';
	$xcolP='P';
	$xcolQ='Q';
	$xcolR='R';

// Bahnhof Excel output vorbereiten ==========================================================================

	if($Treffen=="")
	{
		$bahnsteiglng = str_replace("<br>", "\n", $TEXT['lang-bs']);

		// Add new sheet
    	$objWorkSheet = $objPHPExcel->createSheet(2);

		// write titles
		$objPHPExcel->setActiveSheetIndex(2)
					->setCellValue('A2', $TEXT['lang-station'].':')
					->setCellValue('A3', $TEXT['lang-kbz'].':')
					->setCellValue('A4', $TEXT['lang-bvw'].':') // Bahnverwaltung
					->setCellValue('A5', $TEXT['lang-typ'].':')
					->setCellValue('A6', $TEXT['lang-rem'].':')

					->setCellValue('D3', $TEXT['lang-Besitzer'].':')
					->setCellValue('D4', $TEXT['lang-email'].':')
					
					->setCellValue('G2', $TEXT['lang-group'].':')
					->setCellValue('G3', $TEXT['lang-drawing'].':')
					->setCellValue('G4', $TEXT['lang-mts'].':') // Hauptgleise
					->setCellValue('G5', $TEXT['lang-st'].':') // Streckengleise
					->setCellValue('G6', $TEXT['lang-cl'].':') // Kreuzungslaenge

		// Gleistabelle					
					->setCellValue('A8', $TEXT['lang-track'])
					->setCellValue('B8', $TEXT['lang-typ'])
					->setCellValue('C8', $TEXT['lang-tl']) // Gleislaenge
					->setCellValue('D8', $bahnsteiglng ) // Bahnsteiglaenge
					->setCellValue('F8', $TEXT['lang-Ladestelle'])
					->setCellValue('G8', $TEXT['lang-rem'])

		// merge zells
					->mergeCells('A2:B2')
					->mergeCells('A3:B3')
					->mergeCells('A4:B4')
					->mergeCells('A5:B5')
					->mergeCells('A6:B6')
					->mergeCells('C2:F2') // Betriebsstelle
					->mergeCells('D3:E3') // Besitzer
					->mergeCells('D4:E4') // Email
					->mergeCells('C6:F6'); // Bemerkung

		// shrink to fit text
		$objPHPExcel->getActiveSheet()->getStyle('A1:I7')->getAlignment()->setShrinkToFit(true);
		// wrap text
		$objPHPExcel->getActiveSheet()->getStyle('D8')->getAlignment()->setWrapText(true); // Bahnsteiglaenge
		
		// set alignments
		$objPHPExcel->getActiveSheet()->getStyle('A2:A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); 
		$objPHPExcel->getActiveSheet()->getStyle('D3:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); 
		$objPHPExcel->getActiveSheet()->getStyle('G2:G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); 
		$objPHPExcel->getActiveSheet()->getStyle('H4:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); 
		$objPHPExcel->getActiveSheet()->getStyle('A8:I8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); 
 		
		$objValidation = $objPHPExcel->getActiveSheet()->getCell('C5')->getDataValidation();
		$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
		$objValidation->setAllowBlank(false);
		$objValidation->setShowInputMessage(true);
		$objValidation->setShowDropDown(true);
		$objValidation->setPromptTitle($TEXT['lang-typ']);
		$objValidation->setPrompt('Please select '.$TEXT['lang-typ']);
		$objValidation->setFormula1('"'.$TEXT['lang-bh'].",".$TEXT['lang-an'].",".$TEXT['lang-hp'].",".$TEXT['lang-bl'].",".$TEXT['lang-sbf'].'"');

		// set width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Gleis
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14); // Typ
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14); // Laenge
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14); // BstLaenge
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(2.5);  // Farbe
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(28); // Ladestelle
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25); // Bemerkung
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); // Bemerkung
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(2.5); // 

		// set fonts and fill styles
		$objPHPExcel->getActiveSheet()->getStyle('A1:I7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A1:I7')->getFill()->getStartColor()->setARGB('FFE0FFE0'); // Global
		$objPHPExcel->getActiveSheet()->getStyle('C2')->getFill()->getStartColor()->setARGB('FFFFFFFF'); // Input
		$objPHPExcel->getActiveSheet()->getStyle('C4:C6')->getFill()->getStartColor()->setARGB('FFFFFFFF'); // Input
		$objPHPExcel->getActiveSheet()->getStyle('F3:F4')->getFill()->getStartColor()->setARGB('FFFFFFFF'); // Input
		$objPHPExcel->getActiveSheet()->getStyle('H3')->getFill()->getStartColor()->setARGB('FFFFFFFF'); // Input
		$objPHPExcel->getActiveSheet()->getStyle('H5:H6')->getFill()->getStartColor()->setARGB('FFFFFFFF'); // Input
		
		$objPHPExcel->getActiveSheet()->getStyle('C2:F2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C6:F6')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F3:F4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H5:H6')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:H7')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A1:H7')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(14); // Betriebsstelle
		$objPHPExcel->getActiveSheet()->getStyle('A1:H7')->getFont()->setBold(true);
		
		$objPHPExcel->getActiveSheet()->getStyle('A8:H8')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('I8')->getFont()->setName('Arial Narrow');
		$objPHPExcel->getActiveSheet()->getStyle('A8:I8')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A8:H8')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A8:I8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A8:I8')->getFill()->getStartColor()->setARGB('FFA0FFA0');
		
		// set sheet title
		$objWorkSheet->setTitle($TEXT['lang-bh']);
		
// write data
		//$objPHPExcel->setActiveSheetIndex(2)->setCellValue('C3', $Bhf_ID);
		$rowB=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle = '$Betriebsstelle' and Spur = '$Spur'"));
		$Bhf_ID = $rowB['id'];
		
		if($rowB['Art']=='Stop') $Bhfart=$TEXT['lang-hp']; // Haltepunkt
		elseif($rowB['Art']=='Block') $Bhfart=$TEXT['lang-bl']; // Blockstelle
		elseif($rowB['Art']=='Connect') $Bhfart=$TEXT['lang-an']; // Anschliesser
		elseif($rowB['Art']=='SBF') $Bhfart=$TEXT['lang-sbf']; // Schattenbahnhof
		else $Bhfart=$TEXT['lang-bh']; // Bahnhof

		$objPHPExcel->setActiveSheetIndex(2)
					->setCellValue('C2', $rowB['Haltestelle'])
					->setCellValue('H2', $rowB['Spur'])
					->setCellValue('C3', $rowB['Kurzbezeichnung'])
					->setCellValue('C4', $rowB['Bahnverwaltung'])
					->setCellValue('F3', $rowB['Besitzer'])
					->setCellValue('F4', $rowB['Email'])
					->setCellValue('C5', $Bhfart)
					->setCellValue('C6', $rowB['Bhf_Bem'])
					->setCellValue('H3', $rowB['Zeichnung'])
					->setCellValue('H5', $rowB['Streckengleise'])
					->setCellValue('H6', $rowB['Kreuzung']);

		$result = getGleisliste($Bhf_ID);
		
		$xrow=9; //data starts at row 11
		while($rowT=$db->sql_fetchrow($result))
		{
			if($rowT['Gleisart']=='Main') $Glsart=$TEXT['lang-mt'];
			elseif($rowT['Gleisart']=='Depot') $Glsart=$TEXT['lang-bw'];
			else $Glsart=$TEXT['lang-rangier'];
			
			if($rowT['LadeFarbe']=="") $rowT['LadeFarbe'] = 'FFFFFF';
			
			$Bahnstg="";
			if($rowT['Bahnsteiglaenge']!=0) $Bahnstg=$rowT['Bahnsteiglaenge'];
						
			$objValidation = $objPHPExcel->getActiveSheet()->getCell($xcolB.$xrow)->getDataValidation();
			$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setPromptTitle($TEXT['lang-typ']);
			$objValidation->setPrompt('Please select '.$TEXT['lang-typ']);
			$objValidation->setFormula1('"'.$TEXT['lang-mt'].",".$TEXT['lang-rangier'].",".$TEXT['lang-bw'].'"');

			$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0');
			$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolD.$xrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); 
			$objPHPExcel->getActiveSheet()->getStyle($xcolE.$xrow)->getFill()->getStartColor()->setRGB($rowT['LadeFarbe']);
			$objPHPExcel->setActiveSheetIndex(2)
						->setCellValue($xcolA.$xrow, $rowT['Gleisname'])
						->setCellValue($xcolB.$xrow, $Glsart)
						->setCellValue($xcolC.$xrow, $rowT['Gleislaenge'])
						->setCellValue($xcolD.$xrow, $Bahnstg)
						->setCellValue($xcolF.$xrow, $rowT['Ladestelle'])
						->setCellValue($xcolG.$xrow, $rowT['Gl_Bem'])
						->mergeCells($xcolG.$xrow.':'.$xcolH.$xrow);
			$xrow++;
		}

		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('FFFFE0'); 
		$objPHPExcel->getActiveSheet()->mergeCells($xcolA.$xrow.':'.$xcolI.$xrow);

		// Einleitung
		$xrow++;
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); // 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $TEXT['lang-einleitung']);
		$xrow++;
		$objPHPExcel->getActiveSheet()->getRowDimension($xrow)->setRowHeight(100);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getFill()->getStartColor()->setRGB('FFFFFF');
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); 
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $rowB['Einleitung']);

		// Beschreibung
		$xrow++;
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); // 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $TEXT['lang-beschreibung']);
		$xrow++;
		$objPHPExcel->getActiveSheet()->getRowDimension($xrow)->setRowHeight(100);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getFill()->getStartColor()->setRGB('FFFFFF');
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); 
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $rowB['Beschreibung']);

		// Personenverkehr
		$xrow++;
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); // 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $TEXT['lang-personenverkehr']);
		$xrow++;
		$objPHPExcel->getActiveSheet()->getRowDimension($xrow)->setRowHeight(100);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getFill()->getStartColor()->setRGB('FFFFFF');
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); 
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $rowB['Personenverkehr']);

		// Frachtverkehr
		$xrow++;
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); // 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $TEXT['lang-frachtverkehr']);
		$xrow++;
		$objPHPExcel->getActiveSheet()->getRowDimension($xrow)->setRowHeight(100);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getFill()->getStartColor()->setRGB('FFFFFF');
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); 
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); 
		$objPHPExcel->setActiveSheetIndex(2)
					->mergeCells($xcolA.$xrow.':'.$xcolH.$xrow)
					->setCellValue($xcolA.$xrow, $rowB['Frachtverkehr']);
		$xrow++;
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolI.$xrow)->getFill()->getStartColor()->setRGB('E0FFE0'); // 

		// Zeichnung
		if(file_exists('./Bilder/pic_Bhf_ID_'.$Bhf_ID))
		{
			$xrow++;
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('pic_Bhf_ID_'.$Bhf_ID);
			$objDrawing->setDescription('pic_Bhf_ID_'.$Bhf_ID);
			$objDrawing->setPath('./Bilder/pic_Bhf_ID_'.$Bhf_ID);
			$objDrawing->setCoordinates($xcolA.$xrow);
			$objDrawing->setWidth(900);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}

		// Formula count main tracks
		$objPHPExcel->getActiveSheet()->setCellValue('H4', '=COUNTIF(B9:B'.$xrow.',"'.$TEXT['lang-mt'].'")');
	}

	
// Bahnhof Excel output vorbereiten end ======================================================================

	$result=$db->sql_query("SELECT DISTINCT Gleisname FROM gleise LEFT JOIN bahnhof ON gleise.Bhf_ID = bahnhof.id
	WHERE bahnhof.Haltestelle = '".escape($Betriebsstelle)."' AND bahnhof.Spur = '".escape($Spur)."'
	ORDER BY Gleisname;");
	$gls="";
	while($row=$db->sql_fetchrow($result))
	{
		$gls = $gls.", ".$row['Gleisname'];
	}

	$result=$db->sql_query("SELECT DISTINCT Ladestelle, Gleisname FROM gleise LEFT JOIN bahnhof ON gleise.Bhf_ID = bahnhof.id
	WHERE bahnhof.Haltestelle = '".escape($Betriebsstelle)."' AND bahnhof.Spur = '".escape($Spur)."'
	ORDER BY Ladestelle;");
	$lds="";
	while($row=$db->sql_fetchrow($result))
	{
		$lds = $lds.", ".$row['Ladestelle']." (".$row['Gleisname'].")";
	}

	// benutze internationalen NHM-Code (EN) wenn Sprache in der Datenbank nhm nicht vorhanden
	if($lang == "NO"){$lang="EN";}

	$i=11; //data starts at row 11

	$result = getTableContents($Treffen, $Betriebsstelle, $Spur, '> 0', 'Haltestelle, fyp.Produktbeschreibung');
	while($row=$db->sql_fetchrow($result))
	{
		$NHM_Code1=substr($row['NHM_Code'],0,-4)."0000";
		$row_NHM1=$db->sql_fetchrow($db->sql_query("SELECT * FROM nhm WHERE NHM_Code = '$NHM_Code1';"));

		$NHM_Code2=substr($row['NHM_Code'],0,-2)."00";
		$row_NHM2=$db->sql_fetchrow($db->sql_query("SELECT * FROM nhm WHERE NHM_Code = '$NHM_Code2';"));

		$xrow=$i;
		if (isset($row['An_ID']))
		{
			if($row['An_ID']!=0){
				$row['Betriebsstelle'] = $row[26];
				if($row['Anschliesser'] =="") $row['Anschliesser'] = $row[28];
		}}

		if($Treffen=="") $bem = '5'; // [5]
		else $bem = '38'; // [38]
		
		$objPHPExcel->setActiveSheetIndex(0) // Ausgangsfrachten
					->setCellValue($xcolA.$xrow, $row['NHM_Code'])
					->setCellValue($xcolB.$xrow, sprintf("%02s",substr($NHM_Code1,0,-6))." ".$row_NHM1[$languages[$lang]])
					->setCellValue($xcolC.$xrow, sprintf("%04s",substr($NHM_Code1,0,-4))." ".$row_NHM2[$languages[$lang]])
					->setCellValue($xcolD.$xrow, $row[$languages[$lang]])
					->setCellValue($xcolE.$xrow, $row['Wagengattung'])
					->setCellValue($xcolF.$xrow, $row['Produktbeschreibung'])
					->setCellValue($xcolG.$xrow, $row['Product_Description'])
					->setCellValue($xcolH.$xrow, $row['Anschliesser'])
					->setCellValue($xcolI.$xrow, $row[$bem])
					->setCellValue($xcolJ.$xrow, $row['Ladestelle']." (".$row['Gleisname'].")")
					->setCellValue($xcolK.$xrow, $row['Wagen_Woche'])
					->setCellValue($xcolL.$xrow, $row['Betriebsstelle'])
					->setCellValue($xcolM.$xrow, $row['Kurzbezeichnung'])
					->setCellValue($xcolN.$xrow, $row['Bahnverwaltung'])
					->setCellValue($xcolO.$xrow, $row['Spur'])
					->setCellValue($xcolP.$xrow, $row['Besitzer'])
					->setCellValue($xcolQ.$xrow, $row['Email'])
					->setCellValue($xcolR.$xrow, $row['id_fyp']);

		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow)->getNumberFormat()->setFormatCode('0000 0000');
		$objPHPExcel->getActiveSheet()->getStyle($xcolJ.$xrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objValidation = $objPHPExcel->getActiveSheet()->getCell($xcolI.$xrow)->getDataValidation();
		$objValidation->setAllowBlank(true);
		$objValidation->setShowInputMessage(true);
		$objValidation->setPrompt('If you want to delete the row in the database enter "delete" in this cell');

		$objValidation = $objPHPExcel->getActiveSheet()->getCell($xcolJ.$xrow)->getDataValidation();
		$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
		$objValidation->setAllowBlank(true);
		$objValidation->setShowInputMessage(true);
		$objValidation->setShowDropDown(true);
		$objValidation->setPromptTitle($TEXT['lang-Ladestelle']);
		$objValidation->setPrompt('Please select '.$TEXT['lang-Ladestelle']);
		$objValidation->setFormula1('"'.$lds.'"');

		$i++;
	}

	$i=11; //data starts at row 11
	$result = getTableContents($Treffen, $Betriebsstelle, $Spur, '= 0', 'Haltestelle, fyp.Produktbeschreibung');
	while($row=$db->sql_fetchrow($result))
	{
		$xrow=$i;
		$trenner=" / ";
		if($row['Ladestelle']=="" or $row['Gl_Bem']=="") $trenner="";
		$objPHPExcel->setActiveSheetIndex(1) // Eingangsfrachten
					->setCellValue($xcolE.$xrow, $row['Wagengattung'])
					->setCellValue($xcolF.$xrow, $row['Produktbeschreibung'])
					->setCellValue($xcolG.$xrow, $row['Product_Description'])
					->setCellValue($xcolH.$xrow, $row['Anschliesser'])
					->setCellValue($xcolI.$xrow, $row[5])
					->setCellValue($xcolJ.$xrow, $row['Ladestelle']." (".$row['Gleisname'].")")
					->setCellValue($xcolK.$xrow, $row['Wagen_Woche'])
					->setCellValue($xcolL.$xrow, $row['Betriebsstelle'])
					->setCellValue($xcolM.$xrow, $row['Kurzbezeichnung'])
					->setCellValue($xcolN.$xrow, $row['Bahnverwaltung'])
					->setCellValue($xcolO.$xrow, $row['Spur'])
					->setCellValue($xcolP.$xrow, $row['Besitzer'])
					->setCellValue($xcolQ.$xrow, $row['Email'])
					->setCellValue($xcolR.$xrow, $row['id_fyp']);

		$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow)->getNumberFormat()->setFormatCode('0000 00000');
		$objPHPExcel->getActiveSheet()->getStyle($xcolJ.$xrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objValidation = $objPHPExcel->getActiveSheet()->getCell($xcolI.$xrow)->getDataValidation();
		$objValidation->setAllowBlank(true);
		$objValidation->setShowInputMessage(true);
		$objValidation->setPrompt('If you want to delete the row in the database enter "delete" in this cell');

		$objValidation = $objPHPExcel->getActiveSheet()->getCell($xcolJ.$xrow)->getDataValidation();
		$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
		$objValidation->setAllowBlank(true);
		$objValidation->setShowInputMessage(true);
		$objValidation->setShowDropDown(true);
		$objValidation->setPromptTitle($TEXT['lang-Ladestelle']);
		$objValidation->setPrompt('Please select '.$TEXT['lang-Ladestelle']);
		$objValidation->setFormula1('"'.$lds.'"');

		$i++;
	}

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');

//	header('Content-Disposition: attachment;filename="FREMO_Yellow_Pages.xls"');
	if($Treffen=="") header('Content-Disposition: attachment;filename="YP_'.$Betriebsstelle.'.xls"');
	else header('Content-Disposition: attachment;filename="YP_'.$TEXT['lang-meet'].'_'.$Treffen.'.xls"');

	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


// Xpln Excel output =========================================================================

if(@$_REQUEST['action']=="xpln")
{	
	$Treffen = getVariableFromQueryStringOrSession('Treffen');

	/** Error reporting */
	error_reporting(E_ALL);

	/** PHPExcel */
	require_once 'lib/PHPExcel.php';

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set properties
	$objPHPExcel->getProperties()->setCreator("Georg Ziegler")
				->setLastModifiedBy("Georg Ziegler")
				->setTitle("Xpln")
				->setSubject("StationTrack")
				->setDescription("Xpln StationTrack from Yellow Pages, generated using PHP classes.")
				->setKeywords("xls php")
				->setCategory("Test result file");

	// Create header
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', "Kbz")
				->setCellValue('B1', "Enum")
				->setCellValue('C1', "PositionX/ TrackName")
				->setCellValue('D1', "PositionY/ Length")
				->setCellValue('E1', "Station")
				->setCellValue('F1', "Type")
				->setCellValue('G1', "SubType")
				->setCellValue('H1', "Owner/Remark")

				/*/ Test
				->setCellValue('I1', "Betriebsstelle")
				->setCellValue('J1', "Bhf_ID")
				->setCellValue('K1', "Anschliesser")
				->setCellValue('L1', "An_ID")
				->setCellValue('M1', "Treffen")
				*// Test
				;

	// set width
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

	// set fonts
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->getStartColor()->setRGB('D0D0D0');

	if($Treffen != "")
	{
		$Stat=$db->sql_query("
		SELECT bahnhof.Haltestelle, bahnhof.Kurzbezeichnung, bahnhof.Art, bahnhof.Besitzer, bahnhof.Bhf_Bem, gleise.*, treffen.* FROM bahnhof
		LEFT JOIN gleise ON bahnhof.id = gleise.Bhf_ID
		JOIN treffen ON treffen.Bhf_ID = bahnhof.id
		WHERE treffen.Treffen = '" . escape($Treffen) ."'
		ORDER BY Betriebsstelle, FIELD(Gleisart, 'Main', 'Siding', 'Storage Siding', 'Storage', 'Depot'), 
		(0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0)
		;");		

		$Conn=$db->sql_query("
		SELECT bahnhof.Haltestelle, bahnhof.Kurzbezeichnung, bahnhof.Art, bahnhof.Besitzer, bahnhof.Bhf_Bem, gleise.*, treffen.* FROM bahnhof
		LEFT JOIN gleise ON bahnhof.id = gleise.Bhf_ID
		JOIN treffen ON treffen.An_ID = bahnhof.id
		WHERE treffen.Treffen = '" . escape($Treffen) ."'
		ORDER BY Betriebsstelle, FIELD(Gleisart, 'Main', 'Siding', 'Storage Siding', 'Storage', 'Depot'), 
		(0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0)
		;");		
	}
	elseif($Betriebsstelle != "")
	{
		$result=$db->sql_query("SELECT bahnhof.Haltestelle, bahnhof.Kurzbezeichnung, bahnhof.Art, bahnhof.Besitzer, gleise.*
		LEFT JOIN gleise ON bahnhof.id = gleise.Bhf_ID
		WHERE bahnhof.Haltestelle = '" . escape($Betriebsstelle) . "'
		AND bahnhof.Spur = '" . escape($Spur) . "'
		ORDER BY Haltestelle, FIELD(Gleisart, 'Main', 'Siding', 'Storage Siding', 'Storage', 'Depot'), 
		(0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0)
		;");		
	}
	else
	{
		$result=$db->sql_query("SELECT bahnhof.Haltestelle, bahnhof.Kurzbezeichnung, bahnhof.Art, bahnhof.Besitzer, gleise.* 
		FROM bahnhof LEFT JOIN gleise ON bahnhof.id = gleise.Bhf_ID
		ORDER BY Haltestelle, FIELD(Gleisart, 'Main', 'Siding', 'Storage Siding', 'Storage', 'Depot'), 
		(0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0)
		;");		
	}

/*
Anschliesser:
	Kbz     = wie Bahnhof
	Enum    = fortlaufend
	Station =  (Art) Connect (Anschliesser)
	Type    = Track (aus Bahnhof.php Gleistyp, Main Track tauschen mit Siding)
	SubType = Siding
*/
	$i=2; //data starts at row 2
	$xcolA='A';
	$xcolB='B';
	$xcolC='C';
	$xcolD='D';
	$xcolE='E';
	$xcolF='F';
	$xcolG='G';
	$xcolH='H';

// TEST
	$xcolI='I';
	$xcolJ='J';
	$xcolK='K';
	$xcolL='L';
	$xcolM='M';
	$xcolN='N';
	$xcolO='O';
	$xcolP='P';
	$xcolQ='Q';
	$xcolR='R';
	$xcolS='S';
	$xcolT='T';
	$xcolU='U';
	$xcolV='V';
	$xcolW='W';
	$xcolX='X';
	$xcolY='Y';
	$xcolZ='Z';
// TEST
	

    while($row = $db->sql_fetchrow($Stat)) { $Station[] = $row; } // move to array 
    while($row = $db->sql_fetchrow($Conn)) { $Connect[] = $row; } // move to array

	$n = 0;
	while($n < count($Connect)) 
	{
		foreach($Station as $id => $value) 
		{
			if($value['Bhf_ID'] == $Connect[$n]['Bhf_ID'])
			{
				$Connect[$n]['Kurzbezeichnung'] = $value['Kurzbezeichnung']; // change Connect-Kbz to Station-Kbz
				break;
			}
		}
		$n++; 
	}

/*	foreach($Connect as $id => $value) {
	echo "id: $id - Bhf_ID: $value[Bhf_ID] : $value[Kurzbezeichnung] $value[Betriebsstelle] - An_ID: $value[12] : $value[Anschliesser]<br />\n";
//	echo "id: $id - $value[0] - $value[1] - $value[2] - $value[3] - $value[4] - $value[5] - $value[6] - $value[7] - $value[8] - $value[9] - $value[10] - $value[11] - $value[12] - $value[13] - $value[14] - $value[15] - $value[16] - $value[17] - $value[18] - $value[19] - $value[20]<br />\n";
	}
*/
	$s = 0;
	$addletter = false;
	foreach($Station as $id => $value) // combine Station and Connect
	{
//echo $Station[$id][Kurzbezeichnung]." - ".$Station[$id][Betriebsstelle]." // ".$Connect[$s][Kurzbezeichnung]." - ".$Connect[$s][Betriebsstelle]." + ".$Connect[$s][Anschliesser]."<br />\n";
		if($Station[$id][Betriebsstelle] > $Connect[$s][Betriebsstelle])
		{
			while($s < count($Connect)) 
			{
				if($Connect[$s][Betriebsstelle] >= $Station[$id][Betriebsstelle]) break;
				$Connect[$s]['Haltestelle'] = $Connect[$s]['Haltestelle']." ".$TEXT['lang-track']." ".$Connect[$s]['Gleisname']; // write track and track name after Anschliesser
				if($Connect[$s]['Gleisart']=="Main" && $Connect[$s]['Bhf_ID'] != $Connect[$s][10] || $Connect[$s]['Gleisart']=="") $Connect[$s]['Gleisart'] = "Siding"; // change Anschliesser Gleisart Main to Siding
				$Connect[$s]['Gleisname'] = substr($Connect[$s]['Anschliesser'],0,1).$Connect[$s]['Gleisname']; // write first letter of Anschliesser before track name
				$data[] = $Connect[$s]; // write Anschliesser
				$s++;
			}
		}
		$data[] = $value; // write Haltestelle
	}

/*	foreach($Connect as $id => $value) {
	echo "id: $id - Bhf_ID: $value[Bhf_ID] : $value[Kurzbezeichnung] - $value[Haltestelle] + An_ID: $value[12] : $value[Anschliesser]<br />\n";
//	echo "id: $id - $value[0] - $value[1] - $value[2] - $value[3] - $value[4] - $value[5] - $value[6] - $value[7] - $value[8] - $value[9] - $value[10] - $value[11] - $value[12] - $value[13] - $value[14] - $value[15] - $value[16] - $value[17] - $value[18] - $value[19] - $value[20]<br />\n";
	}
*/
	unset($Station);
	unset($Connect);
			
	$SameStation="";
	$SameTrack="";
	$n = 0;
	while($n < count($data)) 
	{
		$xrow=$i;

		if($SameStation!=$data[$n]['Kurzbezeichnung'])
		{
			$Enum="0";

			$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle($xcolA.$xrow.':'.$xcolH.$xrow)->getFill()->getStartColor()->setRGB('C0C0FF');

			if($data[$n]['Art']=='Connect') $data[$n]['Bhf_Bem'] = "[".$TEXT['lang-an']."] ".trim($data[$n]['Bhf_Bem']);
			if(trim($data[$n]['Bhf_Bem'])!='' && $data[$n]['Besitzer']!='') $remark = " / ".trim($data[$n]['Bhf_Bem']);
			elseif(trim($data[$n]['Bhf_Bem'])!='') $remark = trim($data[$n]['Bhf_Bem']);
			else $remark = '';
			
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($xcolA.$xrow, $data[$n]['Kurzbezeichnung'])
						->setCellValue($xcolB.$xrow, $Enum)
						->setCellValue($xcolC.$xrow, "0")
						->setCellValue($xcolD.$xrow, "0")
						->setCellValue($xcolE.$xrow, $data[$n]['Haltestelle'])
						->setCellValue($xcolF.$xrow, "Station") // Xpln Type
						->setCellValue($xcolG.$xrow, $data[$n]['Art']) // Xpln SubType
						->setCellValue($xcolH.$xrow, $data[$n]['Besitzer'].$remark)
						/*/ TEST
						->setCellValue($xcolI.$xrow, $data[$n]['Betriebsstelle'])
						->setCellValue($xcolJ.$xrow, $data[$n]['Bhf_ID'])
						->setCellValue($xcolK.$xrow, $data[$n]['Anschliesser'])
						->setCellValue($xcolL.$xrow, $data[$n]['An_ID'])
						->setCellValue($xcolM.$xrow, $data[$n]['Treffen'])
						->setCellValue($xcolN.$xrow, $data[$n][2])
						->setCellValue($xcolO.$xrow, $data[$n][3])
						->setCellValue($xcolP.$xrow, $data[$n][4])
						->setCellValue($xcolQ.$xrow, $data[$n][5])
						->setCellValue($xcolR.$xrow, $data[$n][6])
						->setCellValue($xcolS.$xrow, $data[$n][7])
						->setCellValue($xcolT.$xrow, $data[$n][8])
						->setCellValue($xcolU.$xrow, $data[$n][9])
						->setCellValue($xcolV.$xrow, $data[$n][10])
						->setCellValue($xcolW.$xrow, $data[$n][11])
						->setCellValue($xcolX.$xrow, $data[$n][12])
						->setCellValue($xcolY.$xrow, $data[$n][13])
						->setCellValue($xcolZ.$xrow, $data[$n][14])
						*// TEST
						;
			$SameStation=$data[$n]['Kurzbezeichnung'];
			$SameTrack="";
			$Enum++;
			$i++;
			$xrow=$i;
		}
		
		if(trim($data[$n]['Gl_Bem'])!='' && $data[$n]['Ladestelle']!='') $remark = " / ".trim($data[$n]['Gl_Bem']);
		elseif(trim($data[$n]['Gl_Bem'])!='') $remark = trim($data[$n]['Gl_Bem']);
		else $remark = '';
		
		if($data[$n]['Haltestelle'] == $data[$n]['Betriebsstelle']) $data[$n]['Haltestelle'] = ''; // delete Station if is Station
		
		if($SameTrack!=$data[$n]['Gleisname'])
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($xcolA.$xrow, $data[$n]['Kurzbezeichnung']) // $An_Kbz
						->setCellValue($xcolB.$xrow, $Enum)
						->setCellValue($xcolC.$xrow, $data[$n]['Gleisname'])
						->setCellValue($xcolD.$xrow, $data[$n]['Gleislaenge'])
						->setCellValue($xcolE.$xrow, $data[$n]['Haltestelle'])
						->setCellValue($xcolF.$xrow, "Track") // Xpln Type
						->setCellValue($xcolG.$xrow, $data[$n]['Gleisart']) // Xpln SubType
						->setCellValue($xcolH.$xrow, $data[$n]['Ladestelle']." ".$remark) // Bemerkung von gleise
						/*/ TEST
						->setCellValue($xcolI.$xrow, $data[$n]['Betriebsstelle'])
						->setCellValue($xcolJ.$xrow, $data[$n]['Bhf_ID'])
						->setCellValue($xcolK.$xrow, $data[$n]['Anschliesser'])
						->setCellValue($xcolL.$xrow, $data[$n]['An_ID'])
						->setCellValue($xcolM.$xrow, $data[$n]['Treffen'])
						->setCellValue($xcolN.$xrow, $data[$n][2])
						->setCellValue($xcolO.$xrow, $data[$n][3])
						->setCellValue($xcolP.$xrow, $data[$n][4])
						->setCellValue($xcolQ.$xrow, $data[$n][5])
						->setCellValue($xcolR.$xrow, $data[$n][6])
						->setCellValue($xcolS.$xrow, $data[$n][7])
						->setCellValue($xcolT.$xrow, $data[$n][8])
						->setCellValue($xcolU.$xrow, $data[$n][9])
						->setCellValue($xcolV.$xrow, $data[$n][10])
						->setCellValue($xcolW.$xrow, $data[$n][11])
						->setCellValue($xcolX.$xrow, $data[$n][12])
						->setCellValue($xcolY.$xrow, $data[$n][13])
						->setCellValue($xcolZ.$xrow, $data[$n][14])
						*// TEST
						;
			$Enum++;
			$i++;
		}
		$SameTrack=$data[$n]['Gleisname'];
		$n++; 
	}

	unset($data);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('StationTrack');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');

//	header('Content-Disposition: attachment;filename="Station_Xpln.xls"');
	if($Treffen=="") header('Content-Disposition: attachment;filename="Xpln_'.$Betriebsstelle.'.xls"');
	else header('Content-Disposition: attachment;filename="Xpln_'.$TEXT['lang-meet'].'_'.$Treffen.'.xls"');

	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}
