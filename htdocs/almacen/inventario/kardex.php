<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *      \file       htdocs/almacen/liste.php
 *      \ingroup    almacen
 *      \brief      Page liste des solicitudes a almacenes
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/contabperiodo.class.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");

//excel para una versión anterior
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
$ver = 0;
if (file_exists($file))
{
	$ver = 1;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

//excel para version 4 o sup
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
if (file_exists($file))
{
	$ver = 2;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("almacen");
$langs->load("stocks");
//$langs->load("fabrication@fabrication");


$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$yesnoprice = GETPOST('yesnoprice');

if (!$user->rights->almacen->inv->kard) accessforbidden();
if ($yesnoprice)
	if (!$user->rights->almacen->inv->kardv) accessforbidden();

if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year();
$lGestion = false;
if (!empty($typemethod)) $lGestion = true;
if (empty($conf->global->ALMACEN_FILTER_YEAR)) $lGestion=false;
$period_year = $_SESSION['period_year'];
$period_month = $_SESSION['period_month'];

$dateini = dol_get_first_day($period_year,1);
$datefin = dol_get_last_day($period_year,$period_month);
//$dateinisel = dol_get_first_day($period_year,1);
//$datefinsel = dol_get_last_day($period_year,$period_month);

$lNewadd = $_SESSION['lAlmacennew'];
$lNewadd = true;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['kardexfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);
$fk_entrepot = $_SESSION['kardexfk_entrepot'];

//filtramos por almacenes designados segun usuario
$objecten = new Entrepot($db);
$objectUrqEntrepot = new Entrepotrelationext($db);
$objuser = new User($db);
$objentrepotuser = new Entrepotuserext($db);
$movement = new MouvementStockext($db);
$objinv = new Inventario($db);
$periodo = new Contabperiodo($db);

$aFilterent = array();
$product = new Product($db);
if ((isset($_POST['id']) && $_POST['id']>0 ) || (isset($_POST['search_id']) && !empty($_POST['search_id'])))
{
	$res = $product->fetch(GETPOST('id'),GETPOST('search_id'));
	if($res>0)
		$_SESSION['kardexid'] = $product->id;
}
$id = $_SESSION['kardexid'];

$filteruser = '';
if (!$user->admin)
{
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic.= " AND t.active = 1";
	$res = $objentrepotuser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	//$res = $objentrepot->getlistuser($user->id);
	if ($res > 0)
	{
		$num = count($objentrepotuser->lines);
		$i = 0;
		$lines = $objentrepotuser->lines;
		foreach ($lines AS $i=>$line)
		{
			if ($line->fk_entrepot)
			{
				if (!empty($filteruser))$filteruser.= ',';
				$filteruser.= $line->fk_entrepot;
				$aFilterent[$line->fk_entrepot] = $line->fk_entrepot;
			}
		}
	}
}
//recibiendo valores
if (isset($_POST['diyear']))
{
	//$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
	//$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
	//$diyear = GETPOST('diyear');
	$dateinisel  = dol_mktime(12, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
	//$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));

		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
	//$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
	//$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

	//$dateini  = dol_mktime(23, 59, 50, $dimonth, $diday, $aDate['year']);

	$dateini  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));

	$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
	$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');

	$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
	$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
	if ($dateinisel <= $datefinsel)
	{
		$_SESSION['kardex']['dateini'] = $dateini;
		$_SESSION['kardex']['dateinisel'] = $dateinisel;
		$_SESSION['kardex']['datefin'] = $datefin;
		$_SESSION['kardex']['datefinsel'] = $datefinsel;
	}
	else
	{
		$error++;
		setEventMessage($langs->trans("Errordatenovalid", GETPOST('id')), 'errors');
	}

	if ($lGestion)
	{
		$now = dol_getdate(dol_now());
		$dateinimin = dol_get_first_day($now['year'],1);
		if (empty($dateinisel)) $dateinisel = dol_get_first_day($now['year'],1);
		if ($dateinisel < $dateinimin)
		{
			//	$error++;
			//	setEventMessages($langs->trans('La fecha inicio es menor al permitido').' '.dol_print_date($dateinisel,'day').' < '.dol_print_date($dateinimin,'day'),null,'errors');
			//	$dateinisel = $dateinimin;
		}
	}
}


//actions


if ($action == 'builddoc')	// En get ou en post
{
	$res = $objectUrqEntrepot->fetch($fk_entrepot);
	if (empty($res))
	{
		$objectUrqEntrepot->rowid = $fk_entrepot;
		$objectUrqEntrepot->fk_entrepot_father = -1;
		$objectUrqEntrepot->tipo = 'almacen';
		$objectUrqEntrepot->model_pdf = GETPOST('model');
		$res = $objectUrqEntrepot->create($user);
	}

	$objectUrqEntrepot->fetch_thirdparty();
	//$objecten->fetch_lines();
	if (GETPOST('model'))
	{
		$objectUrqEntrepot->setDocModel($user, GETPOST('model'));
	}
	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	if (empty($objectUrqEntrepot->model_pdf))
	{
		$objectUrqEntrepot->modelpdf = GETPOST('model');
		$result=almacen_pdf_create($db, $objectUrqEntrepot, $objectUrqEntrepot->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	}
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
		exit;
	}
}

	// Remove file in doc form
if ($action == 'remove_file')
{
	if ($id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->almacen->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}

if ($action == 'edit')
{
	//$error=0;
	if ($fk_entrepot <=0)
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->trans('Entrepot')), 'errors');
	}
	if (empty($id))
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->trans('Product')), 'errors');
	}

	//if (empty($error))
	//	setEventMessage($langs->trans("Proceso satisfactorio", GETPOST('id')));
}

if (empty($dateinisel)) $dateinisel = $_SESSION['kardex']['dateinisel'];
if (empty($datefinsel)) $datefinsel = $_SESSION['kardex']['datefinsel'];
if (empty($dateinisel)) $dateinisel = dol_get_first_day($period_year,1);
if (empty($datefinsel)) $datefinsel = dol_get_last_day($period_year,$period_month);


/*
if (!empty($_SESSION['kardex']['dateini'])) $dateini = $_SESSION['kardex']['dateini'];
if (!empty($_SESSION['kardex']['dateinisel'])) $dateinisel = $_SESSION['kardex']['dateinisel'];
if (!empty($_SESSION['kardex']['datefin'])) $datefin = $_SESSION['kardex']['datefin'];
if (!empty($_SESSION['kardex']['datefinsel'])) $datefinsel = $_SESSION['kardex']['datefinsel'];
*/
//armamos excel

if ($action == 'excel')
{


	//$aData[$fk_entrepot] = $newKardex;
	//$_SESSION['newKardex'] = serialize($aData);

	$aData = unserialize($_SESSION['newKardex']);
		//$newKardex = $newKardex[$object->id];
		//$aKardex = $newKardex[$object->id];
	$newKardex = $aData[$fk_entrepot];
	$yesnoprice = $newKardex['yesnoprice'];

		//print_r($newKardex);
		//exit;

		//$object->fetch_thirdparty();
	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	if ($yesnoprice)
		$objPHPExcel = $objReader->load("./excel/kardexvalorado.xlsx");
	else
		$objPHPExcel = $objReader->load("./excel/kardex.xlsx");
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);
		// Set alignments
		//echo date('H:i:s') , " Set alignments" , PHP_EOL;

	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		//$objPHPExcel->getActiveSheet()->setCellValue('B2',$langs->trans("kardex"));
		//$objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	$sheet = $objPHPExcel->getActiveSheet();
	//$sheet->setCellValueByColumnAndRow(0,2, "Kardex");
	$sheet->getStyle('A2')->getFont()->setSize(12);

	//$sheet->mergeCells('A2:F2');
	//if($yesnoprice)
	//	$sheet->mergeCells('A2:L2');
	//$sheet->getStyle('A2')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
	/*
	$objPHPExcel->getActiveSheet()->setCellValue('A3',$langs->trans("Date"));
	$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Entrepot"));
	$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Ref"));
	$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Label"));
	$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans("Desde"));
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans('Hasta'));

	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
	*/
	$objPHPExcel->getActiveSheet()->setCellValue('B3',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('B4', $newKardex['entrepot']);
	$objPHPExcel->getActiveSheet()->setCellValue('B5', $newKardex['productref']);
	$objPHPExcel->getActiveSheet()->setCellValue('B6', $newKardex['productlabel']);
	$objPHPExcel->getActiveSheet()->setCellValue('B7', dol_print_date($newKardex['dateini'],"day",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('B8', dol_print_date($newKardex['datefin'],"day",false,$outputlangs));

	/*
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	*/
	/*
	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);
		*/
	/*
	if($yesnoprice){
		$objPHPExcel->getActiveSheet()->getStyle('A2:L8')->applyFromArray($styleThickBrownBorderOutline);
	}
	else{
		$objPHPExcel->getActiveSheet()->getStyle('A2:H8')->applyFromArray($styleThickBrownBorderOutline);
	}
	*/
		//TABLA
	/*
	$objPHPExcel->getActiveSheet()->getStyle('A10:H10')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
				),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
			'borders' => array(
				'top'     => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
			'fill' => array(
				'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation'   => 90,
				'startcolor' => array(
					'argb' => 'FFA0A0A0'
					),
				'endcolor'   => array(
					'argb' => 'FFFFFFFF'
					)
				)
			)
		);
	*/
		$objPHPExcel->setActiveSheetIndex(0);
	//$objPHPExcel->getActiveSheet()->setCellValue('F9',$langs->trans("Fisico"));

		$sheet = $objPHPExcel->getActiveSheet();
	//$sheet->setCellValueByColumnAndRow(0,9, "Fisico");
	//$sheet->getStyle('F9')->getFont()->setSize(15);
	//$sheet->mergeCells('F9:H9');
	//$sheet->getStyle('F9')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

	/*
	$objPHPExcel->getActiveSheet()->setCellValue('A10',$langs->trans("Date"));
	$objPHPExcel->getActiveSheet()->setCellValue('B10',$langs->trans("Descripcion"));
	$objPHPExcel->getActiveSheet()->setCellValue('C10',$langs->trans("User"));
	$objPHPExcel->getActiveSheet()->setCellValue('D10',$langs->trans("Doc"));
	$objPHPExcel->getActiveSheet()->setCellValue('E10',$langs->trans("MovementType"));
	$objPHPExcel->getActiveSheet()->setCellValue('F10',$langs->trans("Entrada"));
	$objPHPExcel->getActiveSheet()->setCellValue('G10',$langs->trans("Salida"));
	$objPHPExcel->getActiveSheet()->setCellValue('H10',$langs->trans("Balance"));
	*/
	if ($yesnoprice && $abc)
	{


		$objPHPExcel->getActiveSheet()->getStyle('I10:L10')->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'top'     => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation'   => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0'
					),
					'endcolor'   => array(
						'argb' => 'FFFFFFFF'
					)
				)
			)
		);

		//$objPHPExcel->getActiveSheet()->setCellValue('I9',$langs->trans("Valorado"));

		$sheet = $objPHPExcel->getActiveSheet();
		//$sheet->setCellValueByColumnAndRow(0,9, "Valorado");
		//$sheet->getStyle('I9')->getFont()->setSize(15);
		$sheet->mergeCells('I9:L9');
		$sheet->getStyle('I9')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$objPHPExcel->getActiveSheet()->setCellValue('I10',$langs->trans("Price"));
		$objPHPExcel->getActiveSheet()->setCellValue('J10',$langs->trans("Entrada"));
		$objPHPExcel->getActiveSheet()->setCellValue('K10',$langs->trans("Salida"));
		$objPHPExcel->getActiveSheet()->setCellValue('L10',$langs->trans("Balance"));
	}

	$j=11;
		// CUERPO

	$Tentv=0;
	$Tsalv=0;
	$Tvalv=0;
	foreach ((array) $newKardex['lines'] AS $i => $lines)
	{
		$date = dol_print_date($lines['date'],'dayhour');
		$desc = $lines['detail'];
		$use = $lines['user'];
		$ent = $lines['entrada'];
		$sal = $lines['salida'];
		$bal = $lines['balance'];
		$refdoc = $lines['refdoc'];

		$price = $lines['pu'];
			// entrada valorado
		$entv = $lines['vEntrada'];
			// salida valorado
		$salv = $lines['vSalida'];
			// balance valorado
		$valv = $lines['vbalance'];

		$mouvement = $lines['mouvement'];
		//$srcobjectf = $lines['srcobjectf'];

		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$date)
		->setCellValue('B' .$j,$desc)
		->setCellValue('C' .$j,$use)
		->setCellValue('D' .$j,$refdoc)
		->setCellValue('E' .$j,$mouvement)
		->setCellValue('F' .$j,$ent)
		->setCellValue('G' .$j,$sal)
		->setCellValue('H' .$j,$bal);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth('25');

		if ($yesnoprice)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('I' .$j,$price)
			->setCellValue('J' .$j,price2num($entv,'MT'))
			->setCellValue('K' .$j,price2num($salv,'MT'))
			->setCellValue('L' .$j,price2num($valv,'MT'));

			$Tentv=$Tentv+$entv;
			$Tsalv=$Tsalv+$salv;
			$Tvalv=$Tvalv+$valv;
		}

		//->setCellValue('G' . $i, $Usu1);
		$j++;

	}
	$j++;
	$col = 'H';
	if ($yesnoprice)
	{
		$col = 'L';
		$Tvalv = price2num($Tentv - $Tsalv,'MT');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,"total");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->setCellValue('J'.$j,$Tentv);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$j,$Tsalv);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$j,$Tvalv);
	}

	/** Borders for heading */
	$objPHPExcel->getActiveSheet()->getStyle('A11:'.$col.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


	$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/kardexf.xlsx");
					//$objWriter->save("excel/export.xlsx");

	header("Location: ".DOL_URL_ROOT.'/almacen/inventario/excel/fiche_export.php?archive=kardexf.xlsx');

}
////////////////////////
//view
/////////////////////////

$formfile = new Formfile($db);
$form = new Formv($db);
if ($fk_entrepot)
{
	$res = $objecten->fetch($fk_entrepot);
}
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';


$aArrjs = array('almacen/javascript/recargar.js');
$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Kardex"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Kardex").' '.$period_year, $page, "liste.php", "", $sortfield, $sortorder,'',0);

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
		$("#selectfk_entrepot").change(function() {
			document.formkard.action.value="";
			document.formkard.submit();
		});
	});';
	print '</script>'."\n";
}

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="formkard">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
print '<table class="border" width="100%">';

// Entrepot Almacen
print '<tr><td width="10%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
print $objectUrqEntrepot->select_padre($fk_entrepot,'fk_entrepot',1,'',$filteruser);
print '</td></tr>';
print '<tr>';
print '<td class="fieldrequired">'.$langs->trans('Product').'</td><td colspan="3">';
//print $form->select_produits($id,'id','',$conf->product->limit_size,0,-1);
//permisos del usuario
$filterstatic = '';
if (!$user->admin)
{
	if($user->rights->almacen->leersell)
		$filterstatic = " p.tosell = 1";
	if($user->rights->almacen->leernosell)
	{
		if (!empty($filterstatic))
			$filterstatic.= " OR p.tobuy = 1";
		else
			$filterstatic.= " p.tobuy = 1";
	}
	if(!$user->rights->almacen->leersell && !$user->rights->almacen->leernosell)
		$filterstatic = " AND p.tosell = 9 AND p.tobuy = 9";
	else
		$filterstatic = " AND ( ".$filterstatic.")";
	$_SESSION['filterstatic'] = $filterstatic;

}
print $form->select_produits_v($id,'id','0',$conf->product->limit_size,0,-1,2,'',1,'','','',$filterstatic,$fk_entrepot,1);

//print $form->select_produits_v('','id_product','',$conf->product->limit_size,0,-1,2,'',1,'','');


print '</td>';
print "</tr>\n";
if ($id > 0)
{
	//recuperamos la foto del producto
	$res = $product->fetch($id);
	$result='';
	if (! empty($product->entity))
	{
		$tmpphoto = $product->show_photos($conf->product->multidir_output[$product->entity],1,1,0,0,0,80);
		if ($product->nbphoto > 0) $label .= '<br>' . $tmpphoto;
	}
	print '<tr><td></td><td colspan="3">'.$tmpphoto.'</td></tr>';
}

// desde fecha
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);

print '</td></tr>';

// hasta fecha
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);

print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';


if (!$error && ($action == 'edit' || $action=='edits') && !empty($id) && !empty($fk_entrepot))
{
	//validamos permisos de lectura
	$product->fetch($id);
	$unit = $langs->trans($product->getLabelOfUnit());
	$lView = false;
	if($user->rights->almacen->leersell && $product->status == 1) $lView = true;
	if($user->rights->almacen->leernosell && $product->status_buy == 1) $lView = true;
	if(!$user->rights->almacen->leersell && !$user->rights->almacen->leernosell) $lView = false;
	if ($user->admin) $lView = true;
	if (!$lView)
		setEventMessage($langs->trans("No tiene permisos para ver el producto", GETPOST('urlfile')), 'errors');

	if (!$error && $lView)
	{

		$sql = "SELECT e.rowid, e.label, ps.reel, p.pmp ";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e,";
		$sql.= " ".MAIN_DB_PREFIX."product_stock as ps,";
		$sql.= " ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE ps.reel != 0";
		$sql.= " AND ps.fk_entrepot = e.rowid";
		$sql.= " AND ps.fk_product = p.rowid";
		$sql.= " AND e.entity = ".$conf->entity;
		$sql.= " AND ps.fk_product = ".$id;
		$sql.= " ORDER BY e.label";

		$entrepotstatic=new Entrepot($db);
		$total=0;
		$totalvalue=$totalvaluesell=0;
		$aSaldores = array();
		$resql=$db->query($sql);
		if ($lNewadd)
		{
			print '<br>';
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre"><td width="40%">'.$langs->trans("Warehouse").'</td>';
			print '<td align="right">'.$langs->trans("NumberOfUnit").'</td>';
			if ($user->rights->almacen->inv->kardv)
			{
				if (empty($typemethod))
				{
					print '<td align="right">'.$langs->trans("AverageUnitPricePMPShort").'</td>';
					print '<td align="right">'.$langs->trans("EstimatedStockValueShort").'</td>';
				}
				if ($typemethod==1)
				{
					print '<td align="right">'.$langs->trans("PEPS").'</td>';
					print '<td align="right">'.$langs->trans("EstimatedStockValuePEPS").'</td>';
				}
				if ($typemethod==2)
				{
					print '<td align="right">'.$langs->trans("UEPS").'</td>';
					print '<td align="right">'.$langs->trans("EstimatedStockValueUEPS").'</td>';
				}
				print '<td align="right">'.$langs->trans("SellPricesf").'</td>';
				print '<td align="right">'.$langs->trans("EstimatedStockValueSellShortsf").'</td>';
				print '<td align="right">'.$langs->trans("SellPriceMin").'</td>';
				print '<td align="right">'.$langs->trans("EstimatedStockValueSellShort").'</td>';
			}
			print '</tr>';
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$total=$totalwithpmp;
				$i=0;
				$var=false;
				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);
					$product     = new Product($db);
					$product->fetch($id);
					$entrepotstatic->id=$obj->rowid;
					$entrepotstatic->libelle=$obj->label;
					print '<tr '.$bc[$var].'>';
					print '<td><a href="kardex.php?id='.$id.'&fk_entrepot='.$obj->rowid.'&action=edits&yesnoprice='.$yesnoprice.'">'.$entrepotstatic->libelle.'</a></td>';

					//	print '<td>'.$entrepotstatic->getNomUrl(1).'</td>';
					print '<td align="right">'.price2num($obj->reel,'MU').($obj->reel<0?' '.img_warning():'').'</td>';

					$movement->saldoanterior($obj->rowid,$dateini,$id);
					$aSaldo = $movement->aSaldo[$id];
					$aSaldores[$obj->rowid] = $aSaldo;
					$movement->mouvement_period($obj->rowid,$dateini,$datefin,$id);
					$aIng = $movement->aIng[$id];
					$aSal = $movement->aSal[$id];
					if ($user->rights->almacen->inv->kardv)
					{
						// PMP
						if (empty($typemethod))
						{
							print '<td align="right">'.(price2num($obj->pmp)?price2num($obj->pmp,'MU'):'').'</td>';
						// Ditto : Show PMP from movement or from product
							print '<td align="right">'.(price2num($obj->pmp)?price(price2num($obj->pmp*$obj->reel,'MT')):'').'</td>';
						}
						if ($typemethod==1)
						{
							$valueproduct = $aSaldo['value_peps']+$aIng['value_peps']+$aSal['value_peps'];
							print '<td align="right">&nbsp;</td>';
						// Ditto : Show PMP from movement or from product
							print '<td align="right">'.(price2num($valueproduct)?price(price2num($valueproduct,'MT')):'').'</td>';
						}
						if ($typemethod==2)
						{
							$valueproduct = $aSaldo['value_peps']+$aIng['value_peps']+$aSal['value_peps'];
							print '<td align="right"></td>';
						// Ditto : Show PMP from movement or from product
							print '<td align="right">'.(price2num($valueproduct)?price(price2num($valueproduct,'MT')):'').'</td>';
						}

		 // Ditto : Show PMP from movement or from product
		// Sell price
						print '<td align="right">';
						if (empty($conf->global->PRODUIT_MUTLI_PRICES))
							print price(price2num($product->price,'MU'));
						else
							print $langs->trans("Variable");
						print '</td>';
		// Ditto : Show PMP from movement or from product
						print '<td align="right">';
						if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($product->price*$obj->reel,'MT')).'</td>';
		 // Ditto : Show PMP from movement or from product
						else
							print $langs->trans("Variable");
						print '</td>';
		// Sell price tot
						print '<td align="right">';
						if (empty($conf->global->PRODUIT_MUTLI_PRICES))
							print price(price2num($product->price_ttc,'MU'));
						else
							print $langs->trans("Variable");
						print '</td>';
		 // Ditto : Show PMP from movement or from product
						print '<td align="right">';
						if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($product->price_ttc*$obj->reel,'MT')).'</td>';
		 // Ditto : Show PMP from movement or from product
						else
							print $langs->trans("Variable");
						print '</td>';
					}
					print '</tr>'; ;
					$total += $obj->reel;
					if (price2num($obj->pmp)) $totalwithpmp += $obj->reel;
					$totalvalue = $totalvalue + price2num($obj->pmp*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
					$totalvaluesell = $totalvaluesell + price2num($product->price*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
					$totalvalue1 = $totalvalue1 + price2num($obj->pmp*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
					$totalvaluesell1 = $totalvaluesell1 + price2num($product->price_ttc*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
					$i++;
					$var=!$var;
				}
			}
			else
				dol_print_error($db);


			print '<tr class="liste_total"><td align="right" class="liste_total">'.$langs->trans("Total").':</td>';
			print '<td class="liste_total" align="right">'.price2num($total,'MT').'</td>';
			if ($user->rights->almacen->inv->kardv && $bbb)
			{
				print '<td class="liste_total" align="right">';
				print ($totalwithpmp?price($totalvalue/$totalwithpmp):'&nbsp;');
				print '</td>';
				print '<td class="liste_total" align="right">';
				print price(price2num($totalvalue,'MT'));
				print '</td>';
				print '<td class="liste_total" align="right">';
				if (empty($conf->global->PRODUIT_MUTLI_PRICES))
					print ($total?price($totalvaluesell/$total):'&nbsp;');
				else
					print $langs->trans("Variable");
				print '</td>';
				print '<td class="liste_total" align="right">';
				if (empty($conf->global->PRODUIT_MUTLI_PRICES))
					print price(price2num($totalvaluesell,'MT'));
				else
					print $langs->trans("Variable");
				print '</td>';
				print '<td class="liste_total" align="right">';
				if (empty($conf->global->PRODUIT_MUTLI_PRICES))
					print ($total?price($totalvaluesell1/$total):'&nbsp;');
				else
					print $langs->trans("Variable");
				print '</td>';
				print '<td class="liste_total" align="right">';
				if (empty($conf->global->PRODUIT_MUTLI_PRICES))
					print price(price2num($totalvaluesell1,'MT'));
				else
					print $langs->trans("Variable");
				print '</td>';
			}
			print "</tr>";
			print "</table>";
		}
		//armamos un array para el reporte
		$newKardex['fk_entrepot'] = $fk_entrepot;
		$newKardex['entrepot'] = $objecten->lieu;
		$newKardex['fk_product'] = $id;
		$newKardex['productref'] = $product->ref;
		$newKardex['productlabel'] = $product->label;
		$newKardex['yesnoprice'] = $yesnoprice;
		$newKardex['unit'] = $unit;
		$newKardex['dateini'] = $_SESSION['kardex']['dateinisel'];
		$newKardex['datefin'] = $_SESSION['kardex']['datefinsel'];

		$newKardex['lines'] = array();

		//movimiento del producto
		$sql  = "SELECT sm.rowid, sm.datem AS datem, sm.value, sm.price, sm.type_mouvement, sm.fk_user_author, sm.label, ";
		$sql.= " sm.fk_origin, sm.origintype, ";
		$sql.= " sma.balance_peps, sma.balance_ueps, sma.value_peps, sma.value_ueps ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS sma ON sma.fk_stock_mouvement = sm.rowid ";
		$sql.= " WHERE fk_entrepot = ".$fk_entrepot;
		if (! empty($dateini) && !empty($datefin))
		{
			//$sql .= " AND UNIX_TIMESTAMP(sm.datem) BETWEEN ".$dateini." AND ".$datefin;

			$sql .= " AND sm.datem BETWEEN '".$db->idate($dateini)."' AND '".$db->idate($datefin)."'";
		}
	//	if ($conf->global->ALMACEN_FILTER_YEAR)
	//		$sql.= " AND sma.period_year = ".$period_year;

		$sql.= " AND fk_product = ".$id;
		//$sql.= " ORDER BY $sortfield $sortorder";
		$sql.= " ORDER BY sm.datem ASC, sm.rowid ASC";

		$result = $db->query($sql);

		if ($result)
		{
			$num = $db->num_rows($result);
			$j = 0;
			$i = 0;
			//recuperamos el saldo para el almacen seleccionado
			$movement->saldoanterior($fk_entrepot,$dateini,$id);
			$aSaldo = $movement->aSaldo[$id];

			//$aSaldo = $aSaldores[$fk_entrepot];

			print_fiche_titre($langs->trans("MovementOfTheStock"), $mesg);

			print '<table class="noborder" width="100%">';

			print "<tr class=\"liste_titre\">";
			print '<th></th>';
			print '<th align="center" colspan="3" class="thlineleft">'.$langs->trans('Fisico').'</th>';
			if ($user->rights->almacen->inv->kardv && $yesnoprice)
			{
				print '<th align="center" colspan="4" class="thlineleft">'.$langs->trans('Valorado').'</th>';
			}
			print '<th colspan="4" class="thlineleft"></th>';
			print '</tr>';

			print "<tr class=\"liste_titre\">";
			//print_liste_field_titre($langs->trans("Date"),"kardex.php", "sm.datem","","","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Date"),"kardex.php", "","","","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Entry"),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Output"),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Balance"),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);
			if ($user->rights->almacen->inv->kardv && $yesnoprice)
			{
				print_liste_field_titre($langs->trans("P.U."),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Entry"),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Output"),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Balance"),"kardex.php", "","","",'align="right"',$sortfield,$sortorder);

			}
			print_liste_field_titre($langs->trans("Doc"),"kardex.php", "","","",'align="center"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("MovementType"),"kardex.php", "sm.type_mouvement","","",'align="center"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("User"),"kardex.php", "sm.fk_user_author","","",'align="center"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Label"),"kardex.php", "sm.label",'','','align="left"',$sortfield,$sortorder);
			print "</tr>\n";

				//mostramos el saldo anterior
			print "<tr $bc[$var]>";
			print '<td nowrap>'.dol_print_date($dateini,'dayhour').'</td>';
			print '<td class="thlineleft">&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td align="right">'.$aSaldo['qty'].'</td>';
			if ($user->rights->almacen->inv->kardv && $yesnoprice)
			{
				print '<td class="thlineleft">&nbsp;</td>';
				print '<td>&nbsp;</td>';
				print '<td>&nbsp;</td>';
				if (empty($typemethod))
				{
					print '<td align="right">'.price(price2num($aSaldo['value_ppp'],'MT')).'</td>';
					$balanceMount+=$aSaldo['value_ppp'];
				}
				if ($typemethod==1)
				{
					print '<td align="right">'.price(price2num($aSaldo['value_peps'],'MT')).'</td>';
					$balanceMount+=$aSaldo['value_peps'];
				}
				if ($typemethod==2)
				{
					print '<td align="right">'.price(price2num($aSaldo['value_ueps'],'MT')).'</td>';
					$balanceMount+=$aSaldo['value_ueps'];
				}
			}
			print '<td>&nbsp;</td>';
			print '<td>'.$langs->trans('Saldo anterior').'</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print "</tr>\n";

			$newKardex['lines'][$j]['date'] = $dateini;
			$newKardex['lines'][$j]['balance'] = $aSaldo['qty']+0;
			$newKardex['lines'][$j]['detail'] = $langs->trans('Saldo anterior');
			$newKardex['lines'][$j]['vbalance'] = $balanceMount;
			if ($num)
			{
				$product     = new Product($db);
				$var=True;
				$balanceQty = 0;
				//$balanceMount = 0;


				$balanceQty += $aSaldo['qty'];
				$j++;
				while ($i < $num)
				{
					//actualizando totales
					$objp = $db->fetch_object($result);
					$balanceQty += $objp->value;
					if (empty($typemethod))
					{
						$balanceMount += price2num($objp->value*$objp->price,'MU');
						$price = $objp->price;
					}
					if ($typemethod==1)
					{
						$balanceMount += price2num($objp->value*$objp->value_peps,'MU');
						$price = $objp->value_peps;
					}
					if ($typemethod==2)
					{
						$balanceMount += price2num($objp->value*$objp->value_ueps,'MU');
						$price = $objp->value_ueps;
					}

					$var=!$var;
					print "<tr $bc[$var]>";
					print '<td nowrap>'.dol_print_date($db->jdate($objp->datem),'dayhour').'</td>';
					$entrada = 0;
					$salida = 0;
					$ventrada = 0;
					$vsalida = 0;
					if ($objp->value < 0)
					{
						$salida = $objp->value * -1;
						$vEntrada = 0;
						$vSalida = price2num($objp->value * $price,'MU');
						print '<td class="thlineleft">&nbsp;</td>';
						print '<td align="right">'.$salida.'</td>';
					}
					elseif ($objp->value > 0)
					{
						$vSalida = 0;
						$entrada = $objp->value;
						$vEntrada = price2num($objp->value*$price,'MU');
						print '<td align="right" class="thlineleft">'.$entrada.'</td>';
						print '<td>&nbsp;</td>';
					}
					else
					{
						print '<td class="thlineleft">&nbsp;</td>';
						print '<td>&nbsp;</td>';
					}
					print '<td align="right">'.price(price2num($balanceQty,'MT')).'</td>';
					if ($user->rights->almacen->inv->kardv && $yesnoprice)
					{
						if ($price >0) print '<td align="right">'.price(price2num($price,'MT')).'</td>';
						else print '<td align="right">'.'&nbsp;</td>';
						if ($objp->value < 0)
						{
							print '<td>&nbsp;</td>';
							$vSalida=$vSalida*-1;
							if ($vSalida>0) 	print '<td align="right">'.price(price2num($vSalida,'MT')).'</td>';
							else print '<td>&nbsp;</td>';

						}
						elseif ($objp->value > 0)
						{
							if ($vEntrada >0) print '<td align="right">'.price(price2num($vEntrada,'MT')).'</td>';
							else print '<td>&nbsp;</td>';
							print '<td>&nbsp;</td>';
						}

						print '<td align="right">'.price(price2num($balanceMount,'MT')).'</td>';
					}
					//documento
					$link = '';
					$linkid = '';
					$refdoc = '';
					if ($objp->origintype=='solalmacendet')
					{
						$element = 'almacen';
						$selement = 'solalmacendet';
						$subelement = 'solalmacendet';

						$selementf = 'solalmacen';
						$subelementf = 'solalmacen';
						dol_include_once('/' . $element . '/class/' . $selement . '.class.php');
						$classname = ucfirst($subelement);
						$srcobject = new $classname($db);
						$srcobject->fetch($objp->fk_origin);
						$linkid.= ' '.$objp->fk_origin.'-'.$objp->origintype;
						dol_include_once('/' . $element . '/class/' . $selementf . '.class.php');
						$classname = ucfirst($subelementf);
						$srcobjectf = new $classname($db);
						$srcobjectf->fetch($srcobject->fk_almacen);
						$linkid.= ' alm '.$srcobject->fk_almacen;
						$refdoc = $srcobjectf->ref;
						$link = $srcobjectf->getNomUrl(1);
					}
					if ($objp->origintype=='order_supplier')
					{
						$element = 'fourn';
						$selement = 'fournisseur.commande';
						$subelement = 'CommandeFournisseur';
						dol_include_once('/' . $element . '/class/' . $selement . '.class.php');
						$classname = ucfirst($subelement);
						$srcobject = new $classname($db);
						$srcobject->fetch($objp->fk_origin);
						$link = $srcobject->getNomUrl(1);
						$refdoc = $srcobject->ref;
						$linkid.=' '.$objp->fk_origin.'-'.$objp->origintype;;
					}
					if ($objp->origintype=='stockmouvementtemp')
					{
						$element = 'almacen';
						$selement = 'stockmouvementtempext';
						$subelement = 'stockmouvementdoc';
						dol_include_once('/' . $element . '/class/' . $selement . '.class.php');
						$classname = ucfirst($selement);
						$srcobject = new $classname($db);
						$srcobject->fetch($objp->fk_origin);
						dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');
						$classname = ucfirst($subelement);
						$srcobjectf = new $classname($db);
						$srcobjectf->fetch(0,$srcobject->ref);
						$link = $srcobjectf->getNomUrl(1);
						$refdoc = $srcobjectf->ref;
						//$linkid.=' '.$objp->fk_origin.'-'.$objp->origintype;;
					}


					print '<td align="center" class="thlineleft">'.$link.'</td>';

					$mouvement = '';
					if ($objp->type_mouvement == 0) $mouvement = $langs->trans("ManualInput");
					if ($objp->type_mouvement == 1) $mouvement = $langs->trans("ManualOutput");
					if ($objp->type_mouvement == 2) $mouvement = $langs->trans("Output");
					if ($objp->type_mouvement == 3) $mouvement = $langs->trans("Input");
					print '<td align="left">'.$mouvement.'</td>';
					$login = '';
					$objuser->fetch($objp->fk_user_author);
					if ($objuser->id == $objp->fk_user_author)
						$login = $objuser->login;
					print '<td align="center">'.$login.'</td>';
					print '<td>'.$objp->label.'</td>';
					print "</tr>\n";

					$newKardex['lines'][$j]['date'] = $objp->datem;
					$newKardex['lines'][$j]['entrada'] = ($entrada?$entrada:'');
					$newKardex['lines'][$j]['salida'] = ($salida?$salida:'');
					$newKardex['lines'][$j]['balance'] = price2num($balanceQty,'MT');
					$newKardex['lines'][$j]['pu'] = $price;
					$newKardex['lines'][$j]['vEntrada'] = $vEntrada;
					$newKardex['lines'][$j]['vSalida'] = $vSalida;
					$newKardex['lines'][$j]['vbalance'] = $balanceMount;
					$newKardex['lines'][$j]['mouvement'] = $mouvement;
					$newKardex['lines'][$j]['user'] = $login;
					$newKardex['lines'][$j]['detail'] = $objp->label;
					$newKardex['lines'][$j]['srcobjectf'] = $srcobjectf;
					$newKardex['lines'][$j]['refdoc'] = $refdoc;

					$i++;
					$j++;
				}
			}
			$db->free($result);
			print "</table>";

			$aData[$fk_entrepot] = $newKardex;
			$_SESSION['newKardex'] = serialize($aData);

				//dol_fiche_end();
			print "<div class=\"tabsAction\">\n";
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
			print '</div>';
		}
		else
		{
			dol_print_error($db);
		}
			// Define output language
		if (!$error && empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$model='kardex';
			if ($user->rights->almacen->inv->kardv && $yesnoprice)
				$model='kardexval';
			$objinv->id = $fk_entrepot;
			$objinv->fk_product = $id;
			$result=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}
	}
}
print '<div class="tabsAction">';
		//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';
		// ancre
$objecten->fetch($fk_entrepot);
$diradd = '/'.$period_year.'/kar';
if ($yesnoprice && $user->rights->almacen->inv->kardv) $diradd = '/'.$period_year.'/karv';
$filename=dol_sanitizeFileName($objecten->libelle).$diradd;
		//cambiando de nombre al reporte
$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($objecten->libelle).$diradd;
$urlsource=$_SERVER['PHP_SELF'].'?id='.$id.'&yesnoprice='.$yesnoprice;
$genallowed=$user->rights->almacen->creardoc;
if (empty($_SESSION['newKardex']))
	$genallowed = false;
$genallowed = false;
$delallowed=$user->rights->almacen->deldoc;
$objecten->modelpdf = 'kardex';
print '<br>';
print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objecten->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";

$db->close();

llxFooter();
?>
