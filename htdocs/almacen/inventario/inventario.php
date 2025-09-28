<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementtempext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/stock.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

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
$reportdet = GETPOST('reportdet');
$reportfacil = GETPOST('reportfacil');

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

if (empty($yesno)) $yesno = $_SESSION['selyesno'];
if (empty($yesno)) $yesno = 2;
if (empty($zeroyesno)) $zeroyesno = $_SESSION['selzeroyesno'];
if (empty($zeroyesno)) $zeroyesno = 2;

if (empty($id)) $id = $_SESSION['idEntrepot'];

if ($yesno == 1 && $id<=0)
{
	setEventMessages($langs->trans('No seleccionó un almacen, no se hara ninguna verificación de saldos'),null,'warnings');
}

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
$nDecimal = $conf->global->ALMACEN_NUMBER_DECIMAL_PRODUCT_BALANCE;
//verificamos el periodo
verif_year();
$lGestion = false;
if (!empty($typemethod)) $lGestion = true;
if (empty($conf->global->ALMACEN_FILTER_YEAR)) $lGestion=false;
$period_year = $_SESSION['period_year'];
$period_month = $_SESSION['period_month'];
$aDatecalc = dol_getdate(dol_now());
$period_month = $aDatecalc['mon'];
$dateActual = dol_mktime(23,59,59,$aDatecalc['mon'],$aDatecalc['mday'],$aDatecalc['year']);

$dateini = dol_get_first_day($period_year,1);
$datefin = dol_get_last_day($period_year,$period_month);
$dateinisel = dol_get_first_day(($period_year?$period_year:date('Y')),1);
$datefinsel = dol_get_last_day(($period_year?$period_year:date('Y')),$period_month);

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
	if (isset($_POST['diyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));
		$dategesini = dol_mktime(0, 0, 1, 1, 1, GETPOST('diyear'));
		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 59, $dimonth, $diday, $aDate['year']);
		$dateini  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$_SESSION['invdateini'] = $dateinisel;
		$_SESSION['invdatefin'] = $datefinsel;

		if ($dateinisel > $datefinsel)
		{
			$error++;
			setEventMessages($langs->trans('Theinitialdatecannotbelongerthanthefinaldate'),null,'errors');
		}
		if ($lGestion)
		{
			$now = dol_getdate(dol_now());
			$dateinimin = dol_get_first_day($period_year,1);
			if (empty($dateinisel)) $dateinisel = dol_get_first_day($period_year,1);
			if ($dateinisel < $dateinimin)
			{
				$error++;
				setEventMessages($langs->trans('La fecha es menor al permitido').' '.dol_print_date($dateinisel,'day').' < '.dol_print_date($dateinimin,'day'),null,'errors');
				$dateinisel = $dateinimin;
			}
		}
	}
}
else
	unset($_SESSION['inventory']);

//if ($id <=0)
//{
//	$error++;
//	setEventMessages($langs->trans('Seleccione un almacen'),null,'errors');
//}
if (!empty($_SESSION['invdateini']))
{
	$dateinisel = $_SESSION['invdateini'];
	$datefinsel = $_SESSION['invdatefin'];
}
//$id = $_SESSION['idEntrepot'];
$yesno = $_SESSION['selyesno'];
$yesnoact = $_SESSION['selyesnoact'];
$zeroyesno = $_SESSION['selzeroyesno'];


// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
if (isset($_GET['page']) || isset($_POST['page']))
	$page = GETPOST('page','int')+0;
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="p.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";


$object = new Entrepotrelationext($db);
$entrepot = new Entrepot($db);
$form = new Form($db);
$formfile = new Formfile($db);
$movement=new MouvementStockext($db);
$objinv = new Inventario($db);
$product = new Product($db);

$hookmanager->initHooks(array('almacen'));
//print_r($hookmanager);

//filtramos por almacenes designados segun usuario
$objentrepotuser = new Entrepotuserext($db);
$aFilterent = array();
$filteruser = '';


if ($action == 'excel')
{
	$fk_entrepot = GETPOST('fk_entrepot');

	//echo '<hr>entrepot '.$fk_entrepot;
	$inventory = unserialize($_SESSION['inventorydet']);
	$inventorysel = unserialize($_SESSION['inventorysel']);
	$labelentrepot='Todos';
	if ($fk_entrepot>0)
	{
		$entrepot = new Entrepot($db);
		$entrepot->fetch($fk_entrepot);
		$labelentrepot = $entrepot->lieu;
	}
	$yesnoprice = $inventorysel['yesnoprice'];

	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	if ($yesnoprice)
	{
		if ($reportfacil ==1)
		{
			$objPHPExcel = $objReader->load("./excel/facilito.xlsx");
		}
		else
		{
			if ($reportdet==2)
				$objPHPExcel = $objReader->load("./excel/inventariovaloradores.xlsx");
			else
				$objPHPExcel = $objReader->load("./excel/inventariovalorado.xlsx");
		}
	}
	else
		$objPHPExcel = $objReader->load("./excel/inventario.xlsx");

		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);

	if ($reportfacil!=1)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('C4',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
		$objPHPExcel->getActiveSheet()->setCellValue('C5', $labelentrepot);
		$objPHPExcel->getActiveSheet()->setCellValue('C6', dol_print_date($inventorysel['dateini'],"day",false,$outputlangs));
		$objPHPExcel->getActiveSheet()->setCellValue('C7', dol_print_date($inventorysel['datefin'],"day",false,$outputlangs));
	}

	// TABLA
	$objPHPExcel->setActiveSheetIndex(0);
	//$objPHPExcel->getActiveSheet()->setCellValue('D9',$langs->trans("Fisico"));

	if ($reportfacil !=1) $j=11;
	else $j=2;
	$k = $j;
	$sumsaav=0;
	$sumainpv=0;
	$sumaoutv=0;
	$sumabalv=0;
	$col='J';
	if ($yesnoprice)
	{
		if ($reportfacil == 1) $col='E';
		else
		{
			if ($reportdet==2)
				$col='E';
			else
				$col='L';
		}
	}
	foreach ((array) $inventory AS $i => $lines)
	{

		$codigo = $lines['ref'];
		$desc = $lines['label'];
		$unit = $lines['unit'];
		$unitshort = html_entity_decode($lines['unitshort']);
		$saa = $lines['ant'];
		$inp = ($lines['input']?$lines['input']:'');
		$out = ($lines['output']?$lines['output']:'');
		$bal = ($lines['balance']?$lines['balance']:'');

		// valorado
		$pu = $lines['pu'];
		$saav = $lines['valueant'];
		$inpv = $lines['valueinput'];
		$outv = $lines['valueoutput'];
		$balv = $lines['valuesaldofin'];

		if ($reportfacil == 1)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$codigo)
			->setCellValue('B' .$j,$desc.' ('.$unitshort.')');
		}
		else
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$codigo)
			->setCellValue('B' .$j,$desc)
			->setCellValue('C' .$j,$unitshort);
		}
		if ($reportfacil == 1)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$j,round($bal));
		}
		else
		{
			if ($reportdet==2)
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,$bal);
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$saa)
				->setCellValue('E' .$j,$inp)
				->setCellValue('F' .$j,$out)
				->setCellValue('G' .$j,$bal);
			}
		}
		if($yesnoprice)
		{
			if ($reportfacil == 1)
			{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,number_format($balv,2,'.',''));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$j,number_format(0,2,'.',''));
			}
			else
			{
				if ($reportdet == 2)
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$j,price2num($balv,'MT'));
				else
				{
					$objPHPExcel->getActiveSheet()->setCellValue('H' .$j,price2num($pu,'MT'))
					->setCellValue('I' .$j,price2num($saav,'MT'))
					->setCellValue('J' .$j,price2num($inpv,'MT'))
					->setCellValue('K' .$j,price2num($outv,'MT'))
					->setCellValue('L' .$j,price2num($balv,'MT'));
				}
			}
			$sumsaav=$sumsaav+$saav;
			$sumainpv=$sumainpv+$inpv;
			$sumaoutv=$sumaoutv+$outv;
			$sumabalv=$sumabalv+$balv;
		}

		$j++;
	}

	if ($yesnoprice)
	{
		if ($reportfacil != 1)
		{
			if ($reportdet==2)
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,$langs->trans("Total"));
				$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
				$sumabalv = $sumsaav+$sumainpv-$sumaoutv;
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$j,price2num($sumabalv));
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,"total");
				$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$j,$sumsaav);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$j,$sumainpv);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$j,$sumaoutv);
				$sumabalv = $sumsaav+$sumainpv-$sumaoutv;
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$j,$sumabalv);
			}
		}
	}

	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':'.$col.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/Inventory.xlsx");
	header("Location: ".DOL_URL_ROOT.'/almacen/inventario/excel/fiche_export.php?archive=Inventory.xlsx');
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
	//if (!$conf->global->ALMACEN_VERIFICA_SALDOS)
	//	$sql.= " AND p.stock = 0";
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
	$resnum = $movement->mouvement_period($id,$dateinisel,$datefinsel);
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

						//if ($saldoMov <> $saldocalc && $saldocalc <> 0)
						if ($saldoMov <> $saldocalc)
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
									$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = ".($nDecimal>0?price2num($saldocalc,$nDecimal):$saldocalc);
									$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$objy->rowid;

								}
								else
								{
									$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
									$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
									$sql.= " (".($nDecimal>0?price2num($saldocalc,$nDecimal):$saldocalc).", ".$fk_entrepot.", ".$objy->rowid.")";

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
$aArrjs = array('almacen/javascript/recargar.js');
$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Inventoryproducts"),$help_url,'','','',$aArrjs,$aArrcss);

//print_fiche_titre($langs->trans("Inventory"));
print_barre_liste($langs->trans("Inventory").' '.$period_year, $page, "liste.php", "", $sortfield, $sortorder,'',0);


print "<form action=\"inventario.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';

print '<table class="border" width="100%">';

// Entrepot Almacen
print '<tr><td width="25%" >'.$langs->trans('Entrepot').'</td><td colspan="3">';

print $object->select_padre($id,'id',1,'',$filteruser);
print '</td></tr>';
// desde fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);

print '</td></tr>';

// hasta fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);

print '</td></tr>';

if ($conf->global->ALMACEN_VERIFICA_SALDOS)
{
	// verifica saldos con movimiento
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Verifybalance').'</td><td colspan="3">';
	print select_yesno(($yesno?$yesno:2),'yesno','',0,0);
	print ' '.$langs->trans('Solo si la fecha final es la fecha actual y esta seleccionado un almacen');
	print '</td></tr>';
}
// mostrar saldos diferentes a cero
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Showzerobalances').'</td><td colspan="3">';
print select_yesno($zeroyesno,'zeroyesno','',0,0);
print '</td></tr>';

if ($yesnoprice)
{
	// mostrar reporte resumido
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Generar reporte detallado').'</td><td colspan="3">';
	print select_yesno($reportdet,'reportdet','',0,0);
	print '</td></tr>';

	// exportar facilito
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Reporte facilito').'</td><td colspan="3">';
	print select_yesno($reportfacil,'reportfacil','',0,0);
	print '</td></tr>';

}
if ($user->rights->almacen->inv->invv)
	print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

//procesando
if (!$error && $action == 'edit')
{
	$_SESSION['idEntrepot'] = $id;
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selyesnoact'] = $yesnoact;
	$_SESSION['selzeroyesno'] = $zeroyesno;
	$aRowid = array();
	if ($id >0)
	{
		$object = new Entrepotrelationext($db);
		$object->id = $id;
		$result = $object->fetch_entrepot();
		if ($result == 1)
		{
			$aEntrepot = $object->aArray;
		}
	}
	//movimiento de salidas y entradas
	//$object->fetch_lines();

	//listamos todos los productos
	//movimiento del producto
	$sql  = "SELECT p.rowid, p.ref, p.label, p.stock, p.fk_unit ";
	$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
	if ($id > 0)
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON ps.fk_product = p.rowid AND ps.fk_entrepot = ".$id;
	$sql.= " WHERE ";
	$sql.= " p.entity = ".$conf->entity;
	//$sql.= " ORDER BY $sortfield $sortorder";
	$sql.= " ORDER BY p.ref ";

	$nbtotalofrecords = 0;
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}
	$fk_entrepot = $id;
	//$sql.= $db->plimit($limit+1, $offset);

	$result = $db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		$params='';
		$params.= '&action='.$action;
		if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

		// print_barre_liste($langs->trans("Currentbalances"), $page, "inventario.php", "", $sortfield, $sortorder,'',$num);
		$title = $langs->trans('Inventory');
		//print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);
		print '<div style="overflow-x: auto; white-space: nowrap;">';

		print '<table class="noborder" width="100%">';
		if (!$yesnoprice)
		{
			print "<tr class=\"liste_titre\">";
			print '<th colspan="3"></th>';
			print '<th colspan="4" align="center" class="thlineleft">'.$langs->trans('Fisico').'</th>';
			if ($yesno == 1) print '<th colspan="2"></th>';
			print '</tr>';
		}
		else
		{
			if ($reportdet == 1)
			{
				print "<tr class=\"liste_titre\">";
				print '<th colspan="3"></th>';
				print '<th colspan="4" align="center" class="thlineleft">'.$langs->trans('Fisico').'</th>';
				if ($yesno == 1) print '<th colspan="2"></th>';
				if ($user->rights->almacen->inv->invv)
				{
					print '<th colspan="4" align="center" class="thlineleft">'.$langs->trans('Valorado').' '.(empty($typemethod)?$langs->trans('PPP'):($typemethod==1?$langs->trans('PEPS'):$langs->trans('UEPS'))).'</th>';
				}
				print '</tr>';
			}
		}
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Code"),"inventario.php", "","",$params,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Description"),"inventario.php", "","",$params,'',$sortfield,$sortorder);
		if ($reportfacil!=1)
			print_liste_field_titre($langs->trans("Unit"),"inventario.php", "","",$params,'',$sortfield,$sortorder);
		if (!$yesnoprice)
		{
			if ($reportfacil == 1)
			{
				print_liste_field_titre($langs->trans("Quant"),"inventario.php", "","",$params,'align="right" class="thlineleft"');
			}
			else
			{
				print_liste_field_titre($langs->trans("Balanceant"),"inventario.php", "","",$params,'align="right" class="thlineleft"');
				print_liste_field_titre($langs->trans("Input"),"inventario.php", "","",$params,'align="right"');
				print_liste_field_titre($langs->trans("Output"),"inventario.php", "","",$params,'align="right"');
				print_liste_field_titre($langs->trans("Balance"),"", "","","",'align="right"');
			}
		}
		else
		{
			if ($reportdet==1)
			{
				print_liste_field_titre($langs->trans("Balanceant"),"inventario.php", "","",$params,'align="right" class="thlineleft"');
				print_liste_field_titre($langs->trans("Input"),"inventario.php", "","",$params,'align="right"');
				print_liste_field_titre($langs->trans("Output"),"inventario.php", "","",$params,'align="right"');
				print_liste_field_titre($langs->trans("Balance"),"", "","","",'align="right"');
			}
			else
				print_liste_field_titre($langs->trans("Physical balance"),"", "","","",'align="right"');
		}
		if ($yesno == 1)
		{
			print_liste_field_titre($langs->trans("Registrado"),"", "","","",'align="right"');
			print_liste_field_titre($langs->trans("Difference"),"", "","","",'align="right"');
		}
		if ($user->rights->almacen->inv->invv)
		{
			if ($yesnoprice)
			{
				if ($reportfacil == 1)
				{
					print_liste_field_titre($langs->trans("Inventario Físico Valorado Final"),"", "","","",'align="right"');
					print_liste_field_titre($langs->trans("Bajas"),"", "","","",'align="right"');
				}
				else
				{
					if ($reportdet==1)
					{
						print_liste_field_titre($langs->trans("Balanceant"),"inventario.php", "","","",'align="right"');
						print_liste_field_titre($langs->trans("Input"),"inventario.php", "","","",'align="right"');
						print_liste_field_titre($langs->trans("Output"),"inventario.php", "","","",'align="right"');
						print_liste_field_titre($langs->trans("Balance"),"", "","","",'align="right"');
					}
					else
						print_liste_field_titre($langs->trans("Balance valued"),"", "","","",'align="right"');
				}
			}
		}

		print "</tr>\n";

		$i = 0;
		//$aSaldo = saldoanterior($id,$db->escape($db->idate($dateinisel)));
		//$dateiniges =
		/*
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
		*/
		$i = 0;
		while ($i < $num)
		{
			$print = true;
			//recorriendo los productos
			$obj = $db->fetch_object($result);
			//echo '<hr>'.$obj->rowid.' | '.$obj->produit.' | ';
			$product = new Product($db);
			$product->fetch($obj->rowid);
			//leemos saldos
			$product->load_stock();
			//sumamos saldos del almacen seleccionado
			$saldoStock = 0;
			if (count($aEntrepot)>0)
			{
				foreach ($aEntrepot AS $idEntrepot)
				{
					$saldoStock += price2num($product->stock_warehouse[$idEntrepot]->real,'MU');
				}
			}
			else
			{
				foreach ($product->stock_warehouse AS $fk_ent => $datastock)
					$saldoStock += price2num($datastock->real,'MU');
			}
			//obtenemos todo el movimiento
			//include DOL_DOCUMENT_ROOT.'/almacen/inventario/includes/mouvement.php';
			//fin obtener movimiento
			$input = $aIng[$obj->rowid]['qty']+0;
			$output = $aSal[$obj->rowid]['qty']+0;

			$saldocalc = $aSaldo[$obj->rowid]['qty']+$input+$output;

			//echo '<hr>product '.$product->ref.' saldocalc '.$saldocalc.' = '.$aSaldo[$obj->rowid]['qty'].'+'.$input.'+'.$output;
			$saldoMov='';
			$saldoMov = $product->stock_warehouse[$id]->real;


			if ($yesnoact == 1)
			{
					/*
				$label = 'Correccion automatica por inventario';
				$mouvement = 0;
				$dif = $saldocalc - $saldoMov;
				if ($dif < 0)
				{
					$mouvement = 1;
					$nbpiece = $dif * -1;
				}
				else
				{
					$nbpiece = $dif;
				}
				//se autoriza la correccion de stock
				$result=$product->correct_stock($user,$id,$nbpiece,$mouvement,$label,$priceunit);
				if ($result <=0) exit;
				*/
			}

			if ($saldocalc == 0 && $saldoMov == 0)
			{
				if ($zeroyesno == 2) $print = false;
			}
			if ($print)
			{
				//vaciamos
				$saldofin = 0;
				$valueinput = 0;
				$valueoutput = 0;
				$valueant = 0;
				//imprimimos
				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td widht="10%">'.$product->getNomUrl(1).'</td>';
				if ($reportfacil==1)
				{
					print '<td widht="60%">'.$obj->label.' ('.$product->getLabelOfUnit('short').')</td>';
				}
				else
				{
					print '<td widht="60%">'.$obj->label.'</td>';
					print '<td widht="60%">'.$langs->trans($product->getLabelOfUnit('short')).'</td>';
				}
				if (!$yesnoprice)
				{
					if ($reportfacil == 1)
					{
						print '<td widht="5%" align="right">'.price2num($saldocalc,'MT').'</td>';
					}
					else
					{
						print '<td align="right" widht="5%" class="thlineleft">'.price2num($aSaldo[$obj->rowid]['qty'],'MT').'</td>';
						print '<td align="right" widht="5%">'.price2num($input,'MT').'</td>';
						print '<td align="right" widht="5%">'.price2num(abs($output),'MT').'</td>';
						print '<td widht="5%" align="right">'.price2num($saldocalc,'MT').'</td>';
					}
				}
				else
				{
					if ($reportdet==1)
					{
						print '<td align="right" widht="5%" class="thlineleft">'.price(price2num($aSaldo[$obj->rowid]['qty'],'MT')).'</td>';
						print '<td align="right" widht="5%">'.price2num($input,'MT').'</td>';
						print '<td align="right" widht="5%">'.price2num(abs($output),'MT').'</td>';
						print '<td widht="5%" align="right">'.price2num($saldocalc,'MT').'</td>';
					}
					else
						print '<td widht="5%" align="right">'.price2num($saldocalc,'MT').'</td>';
				}
				if ($yesno == 1)
				{
					print '<td widht="5%" align="right">'.price(price2num($saldoMov,'MT')).'</td>';
					print '<td widht="5%" align="right">'.price(price2num($saldocalc - $saldoMov,'MT')).'</td>';

				}
				if ($user->rights->almacen->inv->invv)
				{
					if ($yesnoprice)
					{
						if ($reporfacil == 1)
						{

							if (empty($typemethod))
							{
								//ppp
								$saldofin = $aSaldo[$obj->rowid]['value_ppp']+$aIng[$obj->rowid]['value_ppp']+$aSal[$obj->rowid]['value_ppp'];
								$valueant = $aSaldo[$obj->rowid]['value_ppp'];
								$valueinput = $aIng[$obj->rowid]['value_ppp'];
								$valueoutput = $aSal[$obj->rowid]['value_ppp'];
							}
							elseif($typemethod==1)
							{
								//PEPS
								if ($aActualPricepeps[$obj->rowid]) $pu = $aActualPricepeps[$obj->rowid];
								else $pu = $aLastPricepeps[$obj->rowid];
								$saldofin = $aSaldo[$obj->rowid]['value_peps']+$aIng[$obj->rowid]['value_peps']+$aSal[$obj->rowid]['value_peps'];
								$valueant = $aSaldo[$obj->rowid]['value_peps'];
								$valueinput = $aIng[$obj->rowid]['value_peps'];
								$valueoutput = $aSal[$obj->rowid]['value_peps'];
							}
							else
							{
								//UEPS
								$saldofin = $aSaldo[$obj->rowid]['value_ueps']+$aIng[$obj->rowid]['value_ueps']+$aSal[$obj->rowid]['value_ueps'];
								$valueant = $aSaldo[$obj->rowid]['value_ueps'];
								$valueinput = $aIng[$obj->rowid]['value_ueps'];
								$valueoutput = $aSal[$obj->rowid]['value_ueps'];
							}
							print '<td align="right">'.price(price2num($saldofin,'MT')).'</td>';
							print '<td align="right">'.'</td>';
						}
						else
						{
							if ($reportdet==1)
							{
								if (empty($typemethod))
								{
									//ppp
									$saldofin = $aSaldo[$obj->rowid]['value_ppp']+$aIng[$obj->rowid]['value_ppp']+$aSal[$obj->rowid]['value_ppp'];
									print '<td align="right" class="thlineleft">'.''.'</td>';
									print '<td align="right" >'.price(price2num($aSaldo[$obj->rowid]['value_ppp'],'MT')).'</td>';
									print '<td align="right">'.price(price2num($aIng[$obj->rowid]['value_ppp'],'MT')).'</td>';
									print '<td align="right">'.price(price2num(abs($aSal[$obj->rowid]['value_ppp']),'MT')).'</td>';
									$valueant = $aSaldo[$obj->rowid]['value_ppp'];
									$valueinput = $aIng[$obj->rowid]['value_ppp'];
									$valueoutput = $aSal[$obj->rowid]['value_ppp'];
								}
								elseif($typemethod==1)
								{
									//PEPS
									if ($aActualPricepeps[$obj->rowid]) $pu = $aActualPricepeps[$obj->rowid];
									else $pu = $aLastPricepeps[$obj->rowid];
									$saldofin = $aSaldo[$obj->rowid]['value_peps']+$aIng[$obj->rowid]['value_peps']+$aSal[$obj->rowid]['value_peps'];

									print '<td align="right" class="thlineleft">'.price(price2num($aSaldo[$obj->rowid]['value_peps'],'MT')).'</td>';
									print '<td align="right">'.price(price2num($aIng[$obj->rowid]['value_peps'],'MT')).'</td>';
									print '<td align="right">'.price(price2num(abs($aSal[$obj->rowid]['value_peps']),'MT')).'</td>';
									$valueant = $aSaldo[$obj->rowid]['value_peps'];
									$valueinput = $aIng[$obj->rowid]['value_peps'];
									$valueoutput = $aSal[$obj->rowid]['value_peps'];
								}
								else
								{
									//UEPS
									$saldofin = $aSaldo[$obj->rowid]['value_ueps']+$aIng[$obj->rowid]['value_ueps']+$aSal[$obj->rowid]['value_ueps'];
									print '<td align="right" class="thlineleft">'.''.'</td>';
									print '<td align="right">'.price(price2num($aSaldo[$obj->rowid]['value_ueps'],'MT')).'</td>';
									print '<td align="right">'.price(price2num($aIng[$obj->rowid]['value_ueps'],'MT')).'</td>';
									print '<td align="right">'.price(price2num(abs($aSal[$obj->rowid]['value_ueps']),'MT')).'</td>';
									$valueant = $aSaldo[$obj->rowid]['value_ueps'];
									$valueinput = $aIng[$obj->rowid]['value_ueps'];
									$valueoutput = $aSal[$obj->rowid]['value_ueps'];
								}
								print '<td align="right">'.price(price2num($saldofin,'MT')).'</td>';
							}
							else
							{

								if (empty($typemethod))
								{
									//ppp
									$saldofin = $aSaldo[$obj->rowid]['value_ppp']+$aIng[$obj->rowid]['value_ppp']+$aSal[$obj->rowid]['value_ppp'];
									$valueant = $aSaldo[$obj->rowid]['value_ppp'];
									$valueinput = $aIng[$obj->rowid]['value_ppp'];
									$valueoutput = $aSal[$obj->rowid]['value_ppp'];
								}
								elseif($typemethod==1)
								{
									//PEPS
									if ($aActualPricepeps[$obj->rowid]) $pu = $aActualPricepeps[$obj->rowid];
									else $pu = $aLastPricepeps[$obj->rowid];
									$saldofin = $aSaldo[$obj->rowid]['value_peps']+$aIng[$obj->rowid]['value_peps']+$aSal[$obj->rowid]['value_peps'];

									$valueant = $aSaldo[$obj->rowid]['value_peps'];
									$valueinput = $aIng[$obj->rowid]['value_peps'];
									$valueoutput = $aSal[$obj->rowid]['value_peps'];
								}
								else
								{
									//UEPS
									$saldofin = $aSaldo[$obj->rowid]['value_ueps']+$aIng[$obj->rowid]['value_ueps']+$aSal[$obj->rowid]['value_ueps'];
									$valueant = $aSaldo[$obj->rowid]['value_ueps'];
									$valueinput = $aIng[$obj->rowid]['value_ueps'];
									$valueoutput = $aSal[$obj->rowid]['value_ueps'];
								}
								print '<td align="right">'.price(price2num($saldofin,'MT')).'</td>';
							}
						}
					}
					$sumaant+= $valueant;
					$sumaing+= $valueinput;
					$sumasal+= $valueoutput;
					$sumatot+= $saldofin;
				}
				print "</tr>\n";
					//cargando en array
				$inventory[$i] = array('rowid'=>$obj->rowid,'ref'=>$obj->ref,'label'=>$obj->label,'unit'=>$product->getLabelOfUnit(),'ant'=>$aSaldo[$obj->rowid]['qty'],'input'=>$input,'output'=>abs($output),'balance'=>$saldocalc,'pu'=>$pu,'valueant'=>$valueant,'valueinput'=>$valueinput,'valueoutput'=>abs($valueoutput),'valuesaldofin'=>$saldofin, 'unit'=>dol_trunc($langs->trans($product->getLabelOfUnit()),5),'unitshort'=>$langs->trans($product->getLabelOfUnit('short')));
			}
			$i++;
		}
		if ($user->rights->almacen->inv->invv)
		{
			if ($yesnoprice)
			{
				print '<tr class="liste_total">';
				if ($reportfacil == 1)
				{
					print '<td colspan="3">'.$langs->trans('Total').'</td>';
					if ($yesno == 1)
					{
						print '<td colspan="2"></td>';
					}
					print '<td align="right">'.price(price2num($sumatot,'MT')).'</td>';
					print '<td align="right">'.'</td>';
				}
				else
				{
					if ($reportdet==1)
					{
						print '<td colspan="7">'.$langs->trans('Total').'</td>';
						if ($yesno == 1)
						{
							print '<td colspan="2"></td>';
						}

						print '<td align="right">'.price(price2num($sumaant,'MT')).'</td>';
						print '<td align="right">'.price(price2num($sumaing,'MT')).'</td>';
						print '<td align="right">'.price(price2num(abs($sumasal),'MT')).'</td>';
						print '<td align="right">'.price(price2num($sumatot,'MT')).'</td>';
					}
					else
					{
						print '<td colspan="4">'.$langs->trans('Total').'</td>';
						if ($yesno == 1)
						{
							print '<td colspan="2"></td>';
						}
						print '<td align="right">'.price(price2num($sumatot,'MT')).'</td>';
					}
				}
				print '</tr>';
			}
		}
			//serializando

		/*
		$aData[$fk_entrepot] = $newKardex;
		$_SESSION['newKardex'] = serialize($aData);

		*/

		$_SESSION['inventorydet'] = serialize($inventory);
		$_SESSION['inventorysel'] = serialize(array('fk_entrepot'=>$id,'dateini'=>$dateinisel,'datefin'=>$datefinsel,'yesnoprice'=>$yesnoprice));

		print "</table>";
		print '</div>';
		// boton de escel

		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$id.'&reportdet='.$reportdet.'&yesno='.$yesno.'&zeroyesno='.$zeroyesno.'&reportfacil='.$reportfacil.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
		print '</div>';

	        // Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			if ($reportfacil!=1)
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}

				$model='inventario';
				if ($user->rights->almacen->inv->invv && $yesnoprice)
				{
					if ($reportdet==1)
						$model='inventariovaldet';
					else
						$model='inventarioval';
				}
				$objinv->id = $id+0;
				$objinv->yesnoprice = $yesnoprice;
				$result=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
		}


	}
}

//if ($id >= 0)
//{
print '<div class="tabsAction">';
	//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';
		// ancre
if ($id > 0)
{
	$entrepot->fetch($id);
		// Documents generes
	$filename=dol_sanitizeFileName($entrepot->libelle).($yesnoprice?'_val':'').'/'.$period_year;
	$filename=dol_sanitizeFileName($entrepot->libelle).'/'.$period_year;
		//cambiando de nombre al reporte
	$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($entrepot->libelle).'/'.$period_year;
}
else
{
	$filename=dol_sanitizeFileName('inventariototal').'/'.$period_year;
		//cambiando de nombre al reporte
	$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName('inventariototal').'/'.$period_year;
}
$urlsource=$_SERVER['PHP_SELF'].'?id='.$id.'&yesnoprice='.$yesnoprice;
$genallowed=$user->rights->almacen->creardoc;
$genallowed=false;
if (empty($_SESSION['inventorydet']))
	$genallowed=false;
$delallowed=$user->rights->almacen->deldoc;
print '<br>';
print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';

print "</div>";
//}
$db->close();

llxFooter();
?>
