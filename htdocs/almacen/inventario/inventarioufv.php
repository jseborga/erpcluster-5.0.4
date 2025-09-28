<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 * Copyright (C) 2017-2017 Yemer Colque        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/almacen/inventario/inventario.php
 *      \ingroup    almacen
 *      \brief      Page calculo del saldos de productos
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/mouvementstockext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuseradd.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
//require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/stock.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';



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

$langs->load("stocks");
$langs->load("almacen@almacen");


$id = GETPOST('id','int');
$yesno = GETPOST('yesno');
$yesnoact = GETPOST('yesnoact');
$zeroyesno = GETPOST('zeroyesno');
$yesnoprice = GETPOST('yesnoprice');




if (!$user->rights->almacen->inv->inv) accessforbidden();
if ($yesnoprice)
	if (!$user->rights->almacen->inv->invv) accessforbidden();

$action = GETPOST('action');
if ($_POST['id'])
{
	$_SESSION['idEntrepot'] = $id;
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selyesnoact'] = $yesnoact;
	$_SESSION['selzeroyesno'] = $zeroyesno;
}
//if (empty($action))
//	$action= 'edit';
if (empty($yesno)) $yesno = $_SESSION['selyesno'];
if (empty($yesno)) $yesno = 2;
if (empty($zeroyesno)) $zeroyesno = $_SESSION['selzeroyesno'];
if (empty($zeroyesno)) $zeroyesno = 2;

if (empty($id)) $id = $_SESSION['idEntrepot'];

$dateini = dol_get_first_day($period_year,1);
$datefin = dol_now();
$dateinisel = dol_get_first_day($period_year,1);
$datefinsel = dol_now();

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
$typufv = $conf->global->ALMACEN_CHANGE_UFV;
//verificamos el periodo
verif_year();
$lGestion = false;
if (!empty($typemethod)) $lGestion = true;
$period_year = $_SESSION['period_year'];

//objetos
$objCategorie = new Categorie($db);

$dateinisel = dol_get_first_day(1990,1,false);

//actions
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
	if (isset($_POST['dfyear']))
	{
		//$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		//$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		//$diyear = GETPOST('diyear');
		//$dateinisel  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));

		$aDate = dol_get_prev_day(1, 1, $period_year);
		$dategesini = dol_mktime(0, 0, 1, 1, 1, $period_year);
		$aDate = dol_get_prev_day(1, 1, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 59, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinselect  = dol_mktime(0, 0, 0, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$_SESSION['invdateini'] = $dateinisel;
		$_SESSION['invdatefin'] = $datefinsel;


	}
}
else
	unset($_SESSION['inventory']);

/*
if ($id <=0)
{
	$error++;
	setEventMessages($langs->trans('Seleccione un almacen'),null,'errors');
}
*/


if (!empty($_SESSION['invdateini']))
{
//	$dateinisel = $_SESSION['invdateini'];
//	$datefinsel = $_SESSION['invdatefin'];
}
$id = $_SESSION['idEntrepot'];
$yesno = $_SESSION['selyesno'];
$yesnoact = $_SESSION['selyesnoact'];
$zeroyesno = $_SESSION['selzeroyesno'];


// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="p.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";


/*
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';
*/

$object = new Entrepotrelationext($db);
$entrepot = new Entrepot($db);
$form = new Form($db);
$formfile = new Formfile($db);
$movement=new MouvementStockext($db);
$objinv = new Inventario($db);
$product = new Product($db);
$multicurren = new Csindexescountryext($db);
$objCsindexescountry = new Csindexescountryext($db);

$hookmanager->initHooks(array('almacen'));
//print_r($hookmanager);
//filtramos por almacenes designados segun usuario

//$objentrepotuser = new Entrepotuseradd($db);
$objentrepotuser = new Entrepotrelationext($db);
$aFilterent = array();
$filteruser = '';


if ($action == 'excel')
{
	$inventory = unserialize($_SESSION['inventorydet']);
	$inventoryGroup = unserialize($_SESSION['inventoryGroup']);
	$aIng=unserialize($_SESSION['aIng']);
	$inventorysel = unserialize($_SESSION['inventorysel']);
	$entrepot = new Entrepot($db);
	$entrepot->fetch($inventorysel['fk_entrepot']);

	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("excel/inventoryufv.xlsx");
	/*
	$objPHPExcel->getProperties()->setCreator("Ramiro Queso")
	->setLastModifiedBy("Yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");
	*/
		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);



	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$sheet = $objPHPExcel->getActiveSheet();
	//$sheet->setCellValueByColumnAndRow(0,2, "Inventario");
	//$sheet->getStyle('A2')->getFont()->setSize(15);

	//$sheet->mergeCells('A2:G2');
	//if($yesnoprice)
	//	$sheet->mergeCells('A2:G2');
	//$sheet->getStyle('A2')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

	/*
	$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Date"));
	$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Entrepot"));
	$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Desde"));
	$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans('Hasta'));

	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
	*/

	$objPHPExcel->getActiveSheet()->setCellValue('C4',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('C5', $entrepot->lieu);
	$objPHPExcel->getActiveSheet()->setCellValue('C6', dol_print_date($inventorysel['dateini'],"day",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('C7', dol_print_date($inventorysel['datefin'],"day",false,$outputlangs));

	/*
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);

	$objPHPExcel->getActiveSheet()->getStyle('A2:I8')->applyFromArray($styleThickBrownBorderOutline);
	*/

	// TABLA
	$objPHPExcel->setActiveSheetIndex(0);

	/*
	$objPHPExcel->getActiveSheet()->setCellValue('A10',$langs->trans("Grupo"));
	$objPHPExcel->getActiveSheet()->setCellValue('B10',$langs->trans("code"));
	$objPHPExcel->getActiveSheet()->setCellValue('C10',$langs->trans("Detalle"));
	$objPHPExcel->getActiveSheet()->setCellValue('D10',$langs->trans("UM"));
	$objPHPExcel->getActiveSheet()->setCellValue('E10',$langs->trans("PU"));
	$objPHPExcel->getActiveSheet()->setCellValue('F10',$langs->trans("saldo"));
	$objPHPExcel->getActiveSheet()->setCellValue('G10',$langs->trans("valor Total"));
	$objPHPExcel->getActiveSheet()->setCellValue('H10',$langs->trans("Actualizado UFV"));
	$objPHPExcel->getActiveSheet()->setCellValue('I10',$langs->trans("Deferencia UfV"));

	$objPHPExcel->getActiveSheet()->getStyle('A10:I10')->applyFromArray(
		array('font'    => array('bold'      => true),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),'borders' => array('allborders'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)),'fill' => array(
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
	$objPHPExcel->getActiveSheet()->getStyle('A10:I15')->applyFromArray(
		array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FFA0A0A0')
					)
				)
			)

		);
	*/
		$j=11;
		$sumsaav=0;
		$sumainpv=0;
		$sumaoutv=0;
		$sumabalv=0;
		$contt=1;
		foreach ((array) $inventory AS $i => $lines)
		{
			$cGroup = $lines['Grupo'];
			$Grupo=$lines['Grupo'];
			$codigo = $lines['ref'];
			$desc = $lines['label'];
			$unit=$lines['unit'];
			$pricee=$lines['pricee'];
			$saldoo=$lines['saldoo'];
			$amountt=$lines['amountt'];
			$valorAct=$lines['valorAct'];
			$valorDif=$lines['valorDif'];

			if ($lines['type']=='g') $Grupo = '';
			//if ($inventoryGroup[$cGroup])
			//{
				$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$Grupo)
				->setCellValue('A' .$j,$codigo)
				->setCellValue('B' .$j,$desc)
				->setCellValue('C' .$j,$unit)
				->setCellValue('D' .$j,price2num($pricee,'MT'))
				->setCellValue('E' .$j,$saldoo)
				->setCellValue('F' .$j,price2num($amountt,'MT'))
				->setCellValue('G' .$j,price2num($valorAct,'MT'))
				->setCellValue('H' .$j,price2num($valorDif,'MT'));

				$j++;
				$contt++;
			//}
		}
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->getStyle('A11:H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save("excel/ufv.xlsx");

		header("Location: ".DOL_URL_ROOT.'/almacen/inventario/excel/fiche_export.php?archive=ufv.xlsx');
	}

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
/*
Actions
*/
if ($action == 'builddoc')	// En get ou en post
{
	$object = new Entrepotrelationext($db);
	$id = $_SESSION['idEntrepot'];

	$object->fetch($id);
	if (empty($object->id))
		$object->id = $id;
	$object->fetch_thirdparty();
	$object->fetch_lines();
	if (GETPOST('model'))
	{
		$object->setDocModel($user, GETPOST('model'));
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
	$result=almacen_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
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


//verificacion adicional
if ($action == 'edit')
{

	$sql  = "SELECT p.rowid, p.ref, p.label, p.stock, p.fk_unit ";
	$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
	if ( $id > 0)
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."stock_mouvement AS ps ON ps.fk_product = p.rowid AND ps.fk_entrepot = ".$id;
	$sql.= " WHERE ";
	$sql.= " p.entity = ".$conf->entity;
	if (!$conf->global->ALMACEN_VERIFICA_SALDOS)
		$sql.= " AND p.stock = 0";
	$sql.= " GROUP BY p.rowid, p.ref, p.label, p.stock, p.fk_unit";
	$sql.= " ORDER BY p.ref ";

	$nbtotalofrecords = 0;
	$fk_entrepot = $id;
	$rest = $db->query($sql);

	$movement->saldoanterior($id,$dateinisel);
	$aSaldo = $movement->aSaldo;
	$aLastPricepeps = $movement->lastPricepeps;
	$aLastPriceppp = $movement->lastPriceppp;
		//$aActual = saldorange($id,$db->escape($db->idate($dateinisel)),$db->escape($db->idate($datefinsel)));
	$movement->mouvement_period($id,$dateinisel,$datefinsel);
	$aActualPricepeps = $movement->actualPricepeps;
	$aActualPriceppp = $movement->actualPriceppp;

	$aIng = $movement->aIng;
	$aSal = $movement->aSal;
	$inventory = array();
	$sumaant = 0;
	$sumaing = 0;
	$sumasal = 0;
	$sumatot = 0;

	if ($rest)
	{
		$num = $db->num_rows($rest);
		$procesa = false;
		if ($conf->global->ALMACEN_VERIFICA_SALDOS)
		{
			if ($num >0 && $id >0) $procesa = true;
		}
		else
			if ($num >0) $procesa = true;
		$fk_entrepot = $id;
		$i = 0;
		if ($procesa)
		{
			while ($i < $num)
			{
				$objy = $db->fetch_object($rest);
				$fk_product = $objy->rowid;

				$product = new Product($db);
				$product->fetch($objy->rowid);
				//leemos saldos
				$product->load_stock();

				$saldoStock = 0;
				$saldoStock += price2num($product->stock_warehouse[$id]->real,'MU');

				$input = $aIng[$objy->rowid]['qty'];
				$output = $aSal[$objy->rowid]['qty'];
				$saldocalc = $aSaldo[$objy->rowid]['qty']+$input+$output;
				//echo '<hr>product '.$product->ref.' saldocalc '.$saldocalc.' = '.$aSaldo[$obj->rowid]['qty'].'+'.$input.'+'.$output;
				$saldoMov='';

				if ($yesno == 1)
				{
					//$saldoMov = $object->linesprod[$obj->rowid]->saldo;
					//$saldoMov = $product->;
					$saldoMov = $product->stock_warehouse[$fk_entrepot]->real;

					if ($dateActual == $datefinsel)
					{
						if ($saldoMov <> $saldocalc && $saldocalc >= 0)
						{
							//buscamos y actualizamos el product_stock
							$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
							$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$objy->rowid;
							$resql=$db->query($sql);
							$alreadyarecord = 0;
							if ($resql)
							{
								$objx = $db->fetch_object($resql);
								if ($objx)
								{
									$alreadyarecord = 1;
									$oldqtywarehouse = $objx->reel;
									$fk_product_stock = $objx->rowid;
								}
								$db->free($resql);
							}
							else
							{
								$error = -2;
							}
							if (! $error)
							{
								if ($alreadyarecord > 0)
								{
									$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = ".$saldocalc;
									$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$objy->rowid;
								}
								else
								{
									$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
									$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
									$sql.= " (".$saldocalc.", ".$fk_entrepot.", ".$objy->rowid.")";
								}
								$resql=$db->query($sql);
								if (! $resql)
								{
									$error = -3;
								}
							}
						}
						elseif ($saldocalc == 0)
						{
							$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
							$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$objy->rowid;
							$resql=$db->query($sql);
							$alreadyarecord = 0;
							if ($resql)
							{
								$objx = $db->fetch_object($resql);
								if ($objx) $alreadyarecord = 1;
								$db->free($resql);
							}
							else
							{
								$error = -2;
							}
							if (! $error)
							{
								if ($alreadyarecord <=0)
								{
									$saldo = 0;
									$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
									$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
									$sql.= " (".$saldo.", ".$fk_entrepot.", ".$objy->rowid.")";
									$resql=$db->query($sql);
									if (! $resql)
									{
										$error = -3;
									}
								}
							}
						}
					}
				}
				else
				{
						//buscamos y actualizamos el product_stock
					$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
					$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$objy->rowid;

					$resql=$db->query($sql);
					$alreadyarecord = 0;
					if ($resql)
					{
						$objx = $db->fetch_object($resql);
						if ($objx) $alreadyarecord = 1;
						$db->free($resql);
					}
					else
					{
						$error = -2;
					}
					if (! $error)
					{
						if ($alreadyarecord <= 0)
						{
							$saldo = 0;
							$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
							$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
							$sql.= " (".$saldo.", ".$fk_entrepot.", ".$objy->rowid.")";
						}
						$resql=$db->query($sql);
						if (!$resql)
						{
							$error = -3;
						}
					}
				}
				$i++;

			}
		}
	}
}

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("Inventoryproducts"),$help_url);

print_fiche_titre($langs->trans("Inventory"));


print "<form action=\"inventarioufv.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';

print '<table class="border" width="100%">';

// Entrepot Almacen
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
//$options = '<option value="0">'.$langs->trans('All').'</option>';
print $object->select_padre($id,'id',1,'',$filteruser);
print '</td></tr>';
// desde fecha
//print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
//$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);
//print '</td></tr>';
// hasta fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);

print '</td></tr>';

// verifica saldos con movimiento
//print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Verifybalance').'</td><td colspan="3">';
//print select_yesno($yesno,'yesno','',0,0);
//print ' '.$langs->trans('Solo si la fecha final es la fecha actual');
//print '</td></tr>';
// mostrar saldos diferentes a cero
//print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Showzerobalances').'</td><td colspan="3">';
//print $form->selectyesno('zeroyesno',($zeroyesno?$zeroyesno:0),1);
//print '</td></tr>';
if ($user->rights->almacen->inv->invv)
	print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';
if (!$error && $action == 'edit' && !empty($id))
{
	$_SESSION['idEntrepot'] = $id;
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selyesnoact'] = $yesnoact;
	$_SESSION['selzeroyesno'] = $zeroyesno;
	$aRowid = array();
	$object = new Entrepotrelationext($db);
	$object->id = $id;
	//print_r($object);

	$result = $object->fetch_entrepot();
	if ($result == 1)
	{
		$aEntrepot = $object->aArray;

	}
	//movimiento de salidas y entradas
	if ($yesno == 1) $object->fetch_lines();

	//listamos todos los productos
	//movimiento del producto
	/*
	$sql  = "SELECT p.rowid, p.ref, p.label, p.stock, ps.reel ";
	$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON ps.fk_product = p.rowid AND ps.fk_entrepot = ".$id;
	$sql.= " WHERE ";
	$sql.= " p.entity = ".$conf->entity;
	*/
		$title = $langs->trans('Inventory');
		print '<table class="noborder" width="100%">';
		print "<tr class=\"liste_titre\">";
		print '<th colspan="2"></th>';
		print '<th colspan="7" align="center">'.$langs->trans('').'</th>';
		print '</tr>';

		print "<tr class=\"liste_titre\">";

		//print_liste_field_titre($langs->trans("Grupo"),"inventario.php", "p.ref","",$params,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Code"),"inventario.php", "p.ref","",$params,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Detalle"),"inventario.php", "p.label","",$params,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Unidad"),"inventario.php", "p.label","",$params,'align="left"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("PU"),"inventario.php", "p.label","",$params,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Saldo"),"inventario.php", "p.label","",$params,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Valor Total"),"inventario.php", "p.label","",$params,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Valor Actualizado UFV"),"inventario.php", "p.label","",$params,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Diferencia Actualizacion"),"inventario.php", "p.label","",$params,'align="center"',$sortfield,$sortorder);
		print "</tr>\n";

		$i =0 ;
		$numcat = $db->num_rows($result);
		$inventory = array();

			$sql  = "SELECT p.rowid, p.ref, p.label, p.stock ";
			$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
			$sql.= " WHERE p.entity = ".$conf->entity;
			$sql.= " ORDER BY p.ref";

			$resprod = $db->query($sql);
			$resprodtmp = $db->query($sql);
			$ValorAct=0;
			$ValorDif=0;

			$num = $db->num_rows($resprodtmp);

			if ($num > 0)
			{
				$x = 0;
				$idsproduct = '';
				while ($x < $num)
				{
					$obj = $db->fetch_object($resprodtmp);
					if (!empty($idsproduct)) $idsproduct.= ',';
					$idsproduct.= $obj->rowid;
					$x++;
				}
			}


			if ($resprod)
			{
				$num = $db->num_rows($resprod);

				if ($num > 0)
				{
					$o = 0;
					//print  '<hr>res '.
					$res = $movement->mouvement_period($id,$dateinisel,$datefinsel,0);

					$aActualPricepeps = $movement->actualPricepeps;
					$aActualPriceppp = $movement->actualPriceppp;

					$aMoving = $movement->aMoving;
					$aMovsal = $movement->aMovsal;
					$aIng = $movement->aIng;
					$aSal = $movement->aSal;
					//print_r($movement);

					$sumParcial = 0;
					$comp=0;
					while ($o < $num)
					{
						$print = true;
						//recorriendo los productos
						$obj = $db->fetch_object($resprod);
						$product = new Product($db);
						$product->fetch($obj->rowid);


						if (!empty($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY))
						{
							$aIng = $aMoving[$obj->rowid];
							$aSal = $aMovsal[$obj->rowid];

							foreach ((array) $aIng AS $fk => $data)
							{

								$aMov = array();
								$aMov['price'] = $data['price_peps'];
								$aMov['datem'] = dol_print_date($data['datem'],'day');
								if (empty($typemethod))
									$aMov['saldo']=$data['qty'];
								elseif($typemethod==1)
									$aMov['saldo']=$data['qtypeps'];
								elseif($typemethod==2)
									$aMov['saldo']=$data['qtyueps'];
								$aMovSal_ = $aSal[$fk];
								$aMov['saldo']+=$aMovSal_['qty'];

								$aMov['amount'] = $aMov['saldo']*$aMov['price'];

								$aDate = dol_getdate($data['datem']);
								$datem = dol_mktime(0, 0, 0, $aDate['mon'], $aDate['mday'], $aDate['year']);

								$resufv = $objCsindexescountry->fetch(0,$typufv,$db->idate($datem));
								$valueufvini=0;
								if ($resufv>0)
									$valueufvini = $objCsindexescountry->amount;

								$resufv = $objCsindexescountry->fetch(0,$typufv,$db->idate($datefinselect));
								$valueufvfin=0;

								if ($resufv>0)
									$valueufvfin = $objCsindexescountry->amount;
								$factor = 0;
								if ($valueufvini > 0)
									$factor = $valueufvfin / $valueufvini;
								else
								{
									setEventMessages($langs->trans('La fecha ').dol_print_date($datem).' '.$langs->trans('No cuenta con tipo de cambio'),null,'errors');
								}
							// valor actualizado ufv
								$valorAct=($factor)*$aMov['amount'];
							// Valor diferencia ufv
								$valorDif=$valorAct-$aMov['amount'];


								$unit = $langs->trans($product->getLabelOfUnit());

								if (price2num($aMov['saldo'],'MU')>0)
								//if ($aMov['saldo']>0)
								{
									print "<tr $bc[$var]>";
									//print '<td widht="20%">'.$objcat->label.'</td>';
									print '<td widht="10%">'.$product->getNomUrl(1).'</td>';
									print '<td widht="52%">'.$obj->label.'</td>';
									print '<td align="LEFT" widht="7%">'.$unit.'</td>';
									print '<td align="right" widht="7%">'.price(price2num($aMov['price'],'MT')).'</td>';
									print '<td align="right" widht="7%">'.$aMov['saldo'].'</td>';
									print '<td align="right" widht="7%">'.price(price2num($aMov['amount'],'MT')).'</td>';
									print '<td align="right" widht="7%">'.price(price2num($valorAct,'MT')).'</td>';
									print '<td align="right" widht="7%">'.price(price2num($valorDif,'MT')).'</td>';
									$sumParcial+=$aMov['amount'];
									$sumTotal += $aMov['amount'];
									$sumParcialufv+=$valorAct;
								//$sumTotalufv += $valorDif;
									$sumTotalufv += $valorAct;
									$sumValorDif+=$valorDif;
									$sumTotalDif+=$valorDif;


									$inventory[] = array('rowid'=>$obj->rowid,'ref'=>$obj->ref,'label'=>$obj->label,'unit'=>$unit,'pricee'=>$aMov['price'],'saldoo'=>$aMov['saldo'],'amountt'=>$aMov['amount'],'valorAct'=>$valorAct,'valorDif'=>$valorDif,'Grupo'=>$objcat->label,'comp'=>$comp,'sumParcial'=>$sumParcial,'type'=>'l');

									print '</tr>';
								}



								$k++;
							}
						}
						else
						{
							//metodo ppp
							$data = $aIng[$obj->rowid];

							$aMov = array();
							$aMov['price'] = $data['value_ppp'];
							$aMov['datem'] = dol_print_date($data['datem'],'day');
							$aMov['saldo']=$data['qty'];
							$aMovSal_ = $aSal[$obj->rowid];
							$aMov['saldo']+=$aMovSal_['qty'];
							$aMov['amount']+=$aMovSal_['value_ppp'];

							$aDate = dol_getdate($data['datem']);
							$datem = dol_mktime(0, 0, 0, $aDate['mon'], $aDate['mday'], $aDate['year']);

							$resufv = $objCsindexescountry->fetch(0,$typufv,$db->idate($datem));
							$valueufvini=0;
							if ($resufv>0)
								$valueufvini = $objCsindexescountry->amount;

							$resufv = $objCsindexescountry->fetch(0,$typufv,$db->idate($datefinselect));
							$valueufvfin=0;

							if ($resufv>0)
								$valueufvfin = $objCsindexescountry->amount;
							$factor = 0;
							if ($valueufvini > 0)
								$factor = $valueufvfin / $valueufvini;
							else
							{
								setEventMessages($langs->trans('La fecha ').dol_print_date($datem).' '.$langs->trans('No cuenta con tipo de cambio'),null,'errors');
							}
							// valor actualizado ufv
							$valorAct=($factor)*$aMov['amount'];
							// Valor diferencia ufv
							$valorDif=$valorAct-$aMov['amount'];


							$unit = $langs->trans($product->getLabelOfUnit());

							if (price2num($aMov['saldo'],'MT')>0)
							{
								print "<tr $bc[$var]>";
								//print '<td widht="20%">'.$objcat->label.'</td>';
								print '<td widht="10%">'.$product->getNomUrl(1).'</td>';
								print '<td widht="52%">'.$obj->label.'</td>';
								print '<td align="LEFT" widht="7%">'.$unit.'</td>';
								print '<td align="right" widht="7%">'.price(price2num($aMov['price'],'MT')).'</td>';
								print '<td align="right" widht="7%">'.price2num($aMov['saldo'],'MT').'</td>';
								print '<td align="right" widht="7%">'.price(price2num($aMov['amount'],'MT')).'</td>';
								print '<td align="right" widht="7%">'.price(price2num($valorAct,'MT')).'</td>';
								print '<td align="right" widht="7%">'.price(price2num($valorDif,'MT')).'</td>';
								$sumParcial+=$aMov['amount'];
								$sumTotal += $aMov['amount'];
								$sumParcialufv+=$valorAct;
								//$sumTotalufv += $valorDif;
								$sumTotalufv += $valorAct;
								$sumValorDif+=$valorDif;
								$sumTotalDif+=$valorDif;


								$inventory[] = array('rowid'=>$obj->rowid,'ref'=>$obj->ref,'label'=>$obj->label,'unit'=>$unit,'pricee'=>$aMov['price'],'saldoo'=>$aMov['saldo'],'amountt'=>$aMov['amount'],'valorAct'=>$valorAct,'valorDif'=>$valorDif,'Grupo'=>$objcat->label,'comp'=>$comp,'sumParcial'=>$sumParcial,'type'=>'l');

								print '</tr>';
							}

						}
						$o++;
						$comp++;
					}
					if ($sumParcial<>0 && $abc)
					{
						print '<tr class="liste_total">';
						print '<td colspan="5">'.$langs->trans('Total').' '.$objcat->label.'</td>';
						print '<td align="right">'.price($sumParcial).'</td>';
						print '<td align="right">'.price(price2num($sumParcialufv,'MT')).'</td>';
						print '<td align="right">'.price(price2num($sumValorDif,'MT')).'</td>';
						print '</tr>';
						$inventory[] = array('rowid'=>$obj->rowid,'ref'=>'','label'=>$langs->trans('Total').' '.$objcat->label,'unit'=>'','pricee'=>'','saldoo'=>'','amountt'=>$sumParcial,'valorAct'=>$sumParcialufv,'valorDif'=>$sumValorDif,'Grupo'=>$objcat->label,'type'=>'g');
					}
					if ($sumParcial >0)
						$inventoryGroup[$objcat->label] = $objcat->label;
					else
						unset($inventoryGroup[$objcat->label]);

					$sumParcial =0;
					$sumParcialufv=0;
					$sumValorDif=0;
				}
			}
			$i++;

			//yemer


		if ($sumTotal> 0)
		{
			print '<tr class="liste_total">';
			print '<td colspan="5">'.$langs->trans('Total').'</td>';
			print '<td align="right">'.price(price2num($sumTotal,'MT')).'</td>';
			print '<td align="right">'.price(price2num($sumTotalufv,'MT')).'</td>';
			print '<td align="right">'.price(price2num($sumTotalDif,'MT')).'</td>';
			print '</tr>';
			$inventory[] = array('rowid'=>$obj->rowid,'ref'=>'','label'=>$langs->trans('Total').' '.$langs->trans('General'),'unit'=>'','pricee'=>'','saldoo'=>'','amountt'=>$sumTotal,'valorAct'=>$sumTotalufv,'valorDif'=>$sumTotalDif,'Grupo'=>'total','type'=>'g');
			$inventoryGroup['total'] = 'total';

		}
		//yemer
		$_SESSION['inventorydet'] = serialize($inventory);
		$_SESSION['inventoryGroup'] = serialize($inventoryGroup);
		$_SESSION['inventorysel'] = serialize(array('fk_entrepot'=>$id,'dateini'=>$dateinisel,'datefin'=>$datefinsel,'yesnoprice'=>$yesnoprice));

		print "</table>";
		// boton de escel
		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
		print '</div>';

			// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$model='inventarioufv';
			$objinv->id = $id;

			$resprint=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($resprint< 0) dol_print_error($db,$result);
		}



	//if ($id >= 0)
	//{
	print '<div class="tabsAction">';
	//documents
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';
		// ancre
		//$entrepot->fetch($id);
		// Documents generes
	$filename=dol_sanitizeFileName($entrepot->libelle).'/inv';
	$filename='inv/'.$period_year;
		//cambiando de nombre al reporte
	$filedir=$conf->almacen->dir_output . '/' . 'inv/'.$period_year;

	$urlsource=$_SERVER['PHP_SELF'].'?id='.$id.'&yesnoprice='.$yesnoprice;
	$genallowed=$user->rights->almacen->creardoc;
	$genallowed=false;
	if (empty($_SESSION['inventorydet']))
		$genallowed=false;
	$genallowed=true;
	$delallowed=$user->rights->almacen->deldoc;
	$delallowed = true;
	$modelpdf = 'inventarioufv';
	print '<br>';
	print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	$somethingshown=$formfile->numoffiles;
	print '</td></tr></table>';

	print "</div>";
	//}
}
$db->close();

llxFooter();
?>
