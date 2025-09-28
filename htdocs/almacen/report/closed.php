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
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/mouvementstockext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementtempext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementpricemodext.class.php");
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

$langs->load("almacen");
$langs->load("stocks");


$id = GETPOST('id','int');
$yesno = GETPOST('yesno');
$yesnoact = GETPOST('yesnoact');
$zeroyesno = GETPOST('zeroyesno');
$yesnoprice = GETPOST('yesnoprice');
$reportdet = GETPOST('reportdet');
$reportfacil = GETPOST('reportfacil');
$periodmonth = GETPOST('periodmonth');

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
			$dateinimin = dol_get_first_day($now['year'],1);
			if (empty($dateinisel)) $dateinisel = dol_get_first_day($now['year'],1);
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
$objStockprice = new Stockmouvementpricemodext($db);
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
	$date_closed = $inventorysel['date_closed'];
	$reportfacil = $inventorysel['reportfacil'];


	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	if ($reportfacil ==1) $objPHPExcel = $objReader->load("./excel/facilito.xlsx");
	else $objPHPExcel = $objReader->load("./excel/inventarioclosed.xlsx");

		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);

	if ($reportfacil!=1)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('C4',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
		$objPHPExcel->getActiveSheet()->setCellValue('C6', dol_print_date($date_closed,'day',false,$outputlangs));
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
	foreach ((array) $inventory AS $i => $lines)
	{

		$codigo = $lines['ref'];
		$desc = $lines['label'];
		$unit = $lines['unit'];
		$unitshort = html_entity_decode($lines['unitshort']);

		$qty = $lines['qty'];
		$priceant = ($lines['priceant']?$lines['priceant']:'');
		$priceact = ($lines['priceact']?$lines['priceact']:'');
		$amountant = ($lines['amountant']?$lines['amountant']:'');
		$amountact = ($lines['amountact']?$lines['amountact']:'');

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
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$j,round($qty));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,round($amountact));
		}
		else
		{
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,round($qty));
			$objPHPExcel->getActiveSheet()->setCellValue('E' .$j,$priceant)
			->setCellValue('F' .$j,$amountant)
			->setCellValue('G' .$j,$priceact)
			->setCellValue('H' .$j,$amountact);
		}
		$j++;
		$sumaant+=$amountant;
		$sumaact+=$amountact;
	}

	if ($reportfacil != 1)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,$langs->trans("Total"));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$j,$sumaant);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$j,$sumaact);
	}
	else
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,$langs->trans("Total"));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,$sumaact);
	}

	$objPHPExcel->setActiveSheetIndex(0);
	if ($reportfacil==1)
	{
		$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	}
	else
	{
		$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle(D)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle(E)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle(F)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle(G)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle(H)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	}


	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	if ($reportfacil==1)
	{
		$objWriter->save("excel/reportfacilito.xlsx");
		header("Location: ".DOL_URL_ROOT.'/almacen/report/fiche_export.php?archive=reportfacilito.xlsx');
	}
	else
	{
		$objWriter->save("excel/reportclosed.xlsx");
		header("Location: ".DOL_URL_ROOT.'/almacen/report/fiche_export.php?archive=reportclosed.xlsx');
	}
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


//cargamos los periodos cerrados
$res = $objStockprice->fetch_groupperiod($period_year);
if ($res>0)
{
	$lines = $objStockprice->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		if ($periodmonth == $line->period_year.'_'.$line->month_year) $selected = ' selected';
		$options.= '<option value="'.$line->period_year.'_'.$line->month_year.'" '.$selected.'>'.$line->period_year.' - '.$line->month_year.'</option>';
	}
}
//view

$aArrjs = array('almacen/javascript/recargar.js');
$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Closedperiods"),$help_url,'','','',$aArrjs,$aArrcss);

//print_fiche_titre($langs->trans("Inventory"));
print_barre_liste($langs->trans("Closedperiods").' '.$period_year, $page, "liste.php", "", $sortfield, $sortorder,'',0);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';

print '<table class="border" width="100%">';

//periodmonth
print '<tr><td width="25%" >'.$langs->trans('Selectperiod').'</td><td colspan="3">';
print '<select name="periodmonth">'.$options.'</select>';
print '</td></tr>';

	// exportar facilito
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Reporte facilito').'</td><td colspan="3">';
print select_yesno($reportfacil,'reportfacil','',0,0);
print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';
$aPeriod = explode('_',$periodmonth);

//procesando
if (!$error && $action == 'edit')
{
	$aPeriod = explode('_',$periodmonth);
	$selPeriod = $aPeriod[0];
	$selMonth = $aPeriod[1];
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selyesnoact'] = $yesnoact;
	$_SESSION['reportfacil'] = $reportfacil;
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

	$filter = " AND t.period_year = ".$selPeriod;
	$filter.= " AND t.month_year = ".$selMonth;
	$res = $objStockprice->fetchAll('ASC','p.ref',0,0,array(),'AND',$filter);

	if ($res>0)
	{
		$lines = $objStockprice->lines;

		$params='';
		$params.= '&action='.$action;
		if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

		$title = $langs->trans('Inventory');

		print '<div style="overflow-x: auto; white-space: nowrap;">';

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Code"),"inventario.php", "","",$params,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Description"),"inventario.php", "","",$params,'',$sortfield,$sortorder);
		if ($reportfacil != 1)
			print_liste_field_titre($langs->trans("Unit"),"inventario.php", "","",$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Quantity"),"inventario.php", "","",$params,'align="right" class="thlineleft"');
		if ($reportfacil==1)
		{
			print_liste_field_titre($langs->trans("Amountact"),"", "","","",'align="right"');
		}
		else
		{
			print_liste_field_titre($langs->trans("Priceunitant"),"inventario.php", "","",$params,'align="right" class="thlineleft"');
			print_liste_field_titre($langs->trans("Amountant"),"inventario.php", "","",$params,'align="right"');
			print_liste_field_titre($langs->trans("Priceact"),"inventario.php", "","",$params,'align="right"');
			print_liste_field_titre($langs->trans("Amountact"),"", "","","",'align="right"');
		}
		print "</tr>\n";

		$i = 0;
		$ref = '';
		$date_closed = '';
		foreach ($lines AS $j => $line)
		{
			$print = true;
			$date_closed = $line->date_closed;
			if ($reportfacil==1)
			{
				if ($ref != $line->refproduct)
				{
					$i++;
					$ref = $line->refproduct;
				}
				$aPrint[$i]['ref'] = $line->refproduct;
				$aPrint[$i]['fk_product'] = $line->fk_product;
				$aPrint[$i]['label'] = $line->labelproduct;
				$aPrint[$i]['fk_unit'] = $line->fk_unit;
				$aPrint[$i]['qty']+=$line->qty;
				if ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==1)
				{
					$aPrint[$i]['amountant']+=$line->qty*$line->value_peps;
					$aPrint[$i]['amountact']+=$line->qty*$line->value_peps_new;
				}
				elseif ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==2)
				{
					$aPrint[$i]['amountant']+=$line->qty*$line->value_ueps;
					$aPrint[$i]['amountact']+=$line->qty*$line->value_ueps_new;
				}
				else
				{
					$aPrint[$i]['amountant']+=$line->qty*$line->price;
					$aPrint[$i]['amountact']+=$line->qty*$line->price_new;
				}
			}
			else
			{
				$i++;
				$ref = $line->refproduct;
				$aPrint[$i]['ref'] = $line->refproduct;
				$aPrint[$i]['fk_product'] = $line->fk_product;
				$aPrint[$i]['label'] = $line->labelproduct;
				$aPrint[$i]['fk_unit'] = $line->fk_unit;
				$aPrint[$i]['qty']+=$line->qty;
				if ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==1)
				{
					$aPrint[$i]['priceant']=$line->value_peps;
					$aPrint[$i]['priceact']=$line->value_peps_new;
					$aPrint[$i]['amountant']=$line->qty*$line->value_peps;
					$aPrint[$i]['amountact']=$line->qty*$line->value_peps_new;
				}
				elseif ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==2)
				{
					$aPrint[$i]['priceant']=$line->value_ueps;
					$aPrint[$i]['priceact']=$line->value_ueps_new;
					$aPrint[$i]['amountant']=$line->qty*$line->value_ueps;
					$aPrint[$i]['amountact']=$line->qty*$line->value_ueps_new;
				}
				else
				{
					$aPrint[$i]['priceant']=$line->price;
					$aPrint[$i]['priceact']=$line->price_new;
					$aPrint[$i]['amountant']=$line->qty*$line->price;
					$aPrint[$i]['amountact']=$line->qty*$line->price_new;
				}
			}
		}
		foreach ($aPrint AS $j => $data)
		{
			if ($data['qty']>0)
			{
			//recorriendo los productos
				$product->fk_unit = $data['fk_unit'];
				$product->id = $data['fk_product'];
				$product->ref = $data['ref'];
				$product->ref_ext = $data['ref_extproduct'];
				$product->label = $data['label'];

				//vaciamos
				$saldofin = 0;
				$valueinput = 0;
				$valueoutput = 0;
				$valueant = 0;
				//imprimimos
				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td widht="10%">'.$product->getNomUrl(1).'</td>';
				if ($reportfacil == 1)
					print '<td widht="60%">'.$product->label.' ('.$langs->trans($product->getLabelOfUnit('short')).')'.'</td>';
				else
				{
					print '<td widht="60%">'.$product->label.'</td>';
					print '<td widht="4%">'.$langs->trans($product->getLabelOfUnit('short')).'</td>';
				}
				print '<td widht="5%" align="right">'.price($data['qty']).'</td>';
				if ($reportfacil == 1)
				{
					print '<td widht="5%" align="right">'.price(price2num($data['amountact'],'MT')).'</td>';
					$sumaact+=$data['amountact'];
				}
				else
				{
					print '<td align="right" widht="5%" class="thlineleft">'.price(price2num($data['priceant'],'MT')).'</td>';
					print '<td align="right" widht="5%">'.price(price2num($data['amountant'],'MT')).'</td>';
					print '<td align="right" widht="5%">'.price(price2num($data['priceact'],'MT')).'</td>';
					print '<td widht="5%" align="right">'.price(price2num($data['amountact'],'MT')).'</td>';
					$sumaact+=$data['amountact'];
					$sumaant+=$data['amountant'];
				}
				print "</tr>\n";
					//cargando en array
				$inventory[$j] = array('rowid'=>$data['id'],'ref'=>$data['ref'],'label'=>$data['label'],'qty'=>$data['qty'],'priceant'=>$data['priceant'],'priceact'=>$data['priceact'],'amountant'=>$data['amountant'],'amountact'=>$data['amountact'],'unit'=>dol_trunc($langs->trans($product->getLabelOfUnit()),5),'unitshort'=>$langs->trans($product->getLabelOfUnit('short')));
			}
		}

		print '<tr class="liste_total">';
		if ($reportfacil ==1)
			print '<td colspan="2">'.$langs->trans('Total').'</td>';
		else
			print '<td colspan="3">'.$langs->trans('Total').'</td>';
		if ($reportfacil == 1)
		{
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($sumaact,'MT')).'</td>';
		}
		else
		{
			print '<td>'.'</td>';
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($sumaant,'MT')).'</td>';
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($sumaact,'MT')).'</td>';
		}
		print '</tr>';
	}

			//serializando

	$_SESSION['inventorydet'] = serialize($inventory);
	$_SESSION['inventorysel'] = serialize(array('date_closed'=>$date_closed,'reportfacil'=>$reportfacil));

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
				$model='reportclosed';
			}
			$objinv->id = $id+0;
			$objinv->yesnoprice = $yesnoprice;
		//$result=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
		//if ($result < 0) dol_print_error($db,$result);
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
//print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';

print "</div>";
//}
$db->close();

llxFooter();
?>
