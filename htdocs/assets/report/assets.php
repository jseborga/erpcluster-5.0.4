<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
* Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
* Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
* Copyright (C) 20-10-17  yemer colque        <locoto1258@gmail.com>
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");

require_once(DOL_DOCUMENT_ROOT."/assets/core/modules/assets/modules_assets.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsgroup.class.php");

require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsbeen.class.php';

require_once(DOL_DOCUMENT_ROOT."/assets/lib/assets.lib.php");
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';

		//require_once(DOL_DOCUMENT_ROOT."/assets/assignment/class/assetsassignmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';


require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';

//require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';


		//require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsbalance.class.php'
		//require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsbalanceext.class.php'
dol_include_once('/assets/class/assetsbalanceext.class.php');
//dol_include_once('/assets/class/assetsmovext.class.php');




$langs->load("assets");
$langs->load("stocks");

		//$langs->load("fabrication@fabrication");

if (!$user->rights->assets->repinv->write) accessforbidden();

//$fk_group = GETPOST('fk_group');
$level = GETPOST('level');



$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

$date_ini = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin = dol_mktime(23,59,59,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
if(empty($date_ini)) $date_ini = dol_now();
if(empty($date_fin)) $date_fin = dol_now();

$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;




$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['kardexfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);
$fk_entrepot = $_SESSION['kardexfk_entrepot'];

		//filtramos por almacenes designados segun usuario
$objAssets = new Assetsext($db);
$objGroup  = new cAssetsgroup($db);
$objbeen   = new Cassetsbeen($db);
$object    = new Assetsassignmentext($db);
$objAss    = new Assetsassignmentext($db);
$objAssdet = new Assetsassignmentdetext($db);
$objAdherent = new Adherent($db);
$objuser = new User($db);

$objAssetsmov = new Assetsmovext($db);


$aFilterent = array();
$id = $_SESSION['kardexid'];


//actions
$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();

if ($action == 'builddoc')
		// En get ou en post
{
	$res = $objGroup->fetch($fk_group);

			//print_r($objGroup);exit;

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
	$objGroup->modelpdf = GETPOST('model');
	$result=assets_pdf_create($db, $objGroup, $objGroup->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

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
	$error=0;
	if (isset($_POST['diyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));

		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 50, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(0, 0, 0, $dfmonth,  $dfday,  GETPOST('dfyear'));
				//if ($dateinisel <= $datefinsel)
				//{
		unset($_SESSION['assetsinv']);
		$_SESSION['assetsinv']['dateini'] = $dateini;
		$_SESSION['assetsinv']['dateinisel'] = $dateinisel;
		$_SESSION['assetsinv']['datefin'] = $datefin;
		$_SESSION['assetsinv']['datefinsel'] = $datefinsel;
				//}
				//else
				//{
				//	$error++;
				//	setEventMessages($langs->trans("Errordatenovalid", GETPOST('id')),null, 'errors');
				//}
		if (empty($error))
			setEventMessages($langs->trans("Proceso satisfactorio"),null,'mesgs');

				//echo $dateinisel.' '.dol_print_date($dateinisel,'day');
				//print_r($_POST);
	}
}

if (!empty($_SESSION['assetsinv']['dateini'])) $dateini = $_SESSION['assetsinv']['dateini'];
if (!empty($_SESSION['assetsinv']['dateinisel'])) $dateinisel = $_SESSION['assetsinv']['dateinisel'];
if (!empty($_SESSION['assetsinv']['datefin'])) $datefin = $_SESSION['assetsinv']['datefin'];
if (!empty($_SESSION['assetsinv']['datefinsel'])) $datefinsel = $_SESSION['assetsinv']['datefinsel'];

// armado de excel

if ($action == 'reporteExcel')
{

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

	$aReportdetasset = unserialize($_SESSION['aReportassetdet']);
	$fk_group = GETPOST('fk_group');
	$date_ini = unserialize($_SESSION['date_inidet']);
	$date_fin = unserialize($_SESSION['date_findet']);
	$level = unserialize($_SESSION['levelass']);

	$cGroup= unserialize($_SESSION['cGroupdet']);

	print_r(dol_print_date($date_ini,'day'));
	print_r(dol_print_date($date_fin,'day'));
	//exit;

	// TITULO
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);
	//$this->activeSheet->getDefaultRowDimension()->setRowHeight($height);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);


	// COLOR DEL TITULO
	$objPHPExcel->getActiveSheet()->getStyle('A2:P2')->applyFromArray(
		array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FF0000'),
				'size'  => 20,
				'name'  => 'Verdana'
				)));


	//PIE DE PAGINA
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getStyle('A2')->getFont()->setSize(15);
	$sheet->mergeCells('A2:P2');
	$sheet->setCellValueByColumnAndRow(0,2, html_entity_decode($langs->trans('Reportassetscod')));

	if($yesnoprice)
		$sheet->mergeCells('A2:P2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
	// ENCABEZADO
	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('B3',$langs->trans("Dateini"));
	$objPHPExcel->getActiveSheet()->setCellValue('B4',$langs->trans("Datefin"));

	$objPHPExcel->getActiveSheet()->setCellValue('C3',dol_print_date($date_ini,'day'));
	$objPHPExcel->getActiveSheet()->setCellValue('C4',dol_print_date($date_fin,'day'));
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);

	// COLOR DEL ENCABEZADO
	$objPHPExcel->getActiveSheet()->getStyle('B3:C7')->applyFromArray(
		array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '0c78bf'),
				'size'  => 12,
				'name'  => 'Verdana'
				)));


	// Numero correlativo
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("Nro"));
	// Ref
	$objPHPExcel->getActiveSheet()->setCellValue('B8',$langs->trans("code"));
	// Ref extendido
	$objPHPExcel->getActiveSheet()->setCellValue('C8',$langs->trans("Fieldref_ext"));
	// descripcion
	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("Label"));
	// fecha adquisicion
	$objPHPExcel->getActiveSheet()->setCellValue('E8',$langs->trans("Fielddate_adq"));
	// costo
	$objPHPExcel->getActiveSheet()->setCellValue('F8',$langs->trans("Fieldcoste"));
	// Inmueble
	$objPHPExcel->getActiveSheet()->setCellValue('G8',$langs->trans("Property"));
	// localizcion
	$objPHPExcel->getActiveSheet()->setCellValue('H8',html_entity_decode($langs->trans("Location")));
	// fecha de asignacion
	$objPHPExcel->getActiveSheet()->setCellValue('I8',html_entity_decode($langs->trans("Dateassignment")));
	// departament
	$objPHPExcel->getActiveSheet()->setCellValue('J8',$langs->trans("Departament"));
	// responsable
	$objPHPExcel->getActiveSheet()->setCellValue('K8',$langs->trans("Responsible"));
	//condicion
	$objPHPExcel->getActiveSheet()->setCellValue('L8',html_entity_decode($langs->trans("Been")));
	//valor actualizado
	$objPHPExcel->getActiveSheet()->setCellValue('M8',$langs->trans("Valoract"));
	//Depreciacion Acumulada
	$objPHPExcel->getActiveSheet()->setCellValue('N8',$langs->trans("Depreciationacum"));
	//saldo
	$objPHPExcel->getActiveSheet()->setCellValue('O8',$langs->trans("Balance"));
	//estado
	$objPHPExcel->getActiveSheet()->setCellValue('P8',$langs->trans("Status"));


	// TABLA COLOR

	$objPHPExcel->getActiveSheet()->getStyle('A8:P8')->applyFromArray(
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
					'argb' => 'FF0000'
					),
				'endcolor'   => array(
					'argb' => 'bfb70c'
					)
				)
			)
		);
	//tama;o de las columnas
	//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	// Numero
	$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
	// fecha de adquisiciomn
	$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

	// FECHA DE ASIGNACION
	$objPHPExcel->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	// costo
	$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// valor actualizado
	$objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// depreciacion acumulado
	$objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// balance
	$objPHPExcel->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	// CUERPO
	$j=9;
	$contt=1;
	foreach ((array) $aReportdetasset AS $i => $lines)
	{
		$Codigo = $lines['Codigo'];
		$Codigoext = $lines['Codigoext'];
		$Etiqueta = $lines['Etiqueta'];
		$FechaAdquisicion = $lines['FechaAdquisicion'];
		$costo = $lines['costo'];
		$Inmueble = $lines['Inmueble'];
		$location = $lines['location'];
		$FechaAsignacion = $lines['FechaAsignacion'];
		$Departament = $lines['Departament'];
		$Responsable = $lines['Responsable'];
		$Condicion = $lines['Condicion'];
		$Valoract = $lines['Valoract'];
		$Depreacum = $lines['Depreacum'];
		$Balance = $lines['Balance'];
		$Estado = $lines['Estado'];
		//$type = $lines['type'];

		if($type = $lines['type']=='D')
		{
			// VISTA
			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
			->setCellValue('B' .$j,$Codigo)
			->setCellValue('C' .$j,$Codigoext)
			->setCellValue('D' .$j,$Etiqueta)
			->setCellValue('E' .$j,dol_print_date($FechaAdquisicion,'day'))
			->setCellValue('F' .$j,$costo)
			->setCellValue('G' .$j,$Inmueble)
			->setCellValue('H' .$j,$location)
			->setCellValue('I' .$j,dol_print_date($FechaAsignacion,'day'))
			->setCellValue('J' .$j,$Departament)
			->setCellValue('K' .$j,$Responsable)
			->setCellValue('L' .$j,$Condicion)
			->setCellValue('M' .$j,$Valoract)
			->setCellValue('N' .$j,$Depreacum)
			->setCellValue('O' .$j,$Balance)
			->setCellValue('P' .$j,$Estado);
			$contt++;
		}
		// BORDES DE LA VISTA
		$objPHPExcel->getActiveSheet()->getStyle('A8:P'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$j++;
	}

	$objPHPExcel->setActiveSheetIndex(0);
	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/reporteestateofassets.xlsx");
	header("Location: ".DOL_URL_ROOT.'/assets/report/fiche_export.php?archive=reporteestateofassets.xlsx');

}


$aEstado = array(0=>'Ref',
	1=>'Ref Extendido');


$formfile = new Formfile($db);
$form = new Formv($db);
		//$aArrjs = array('almacen/javascript/recargar.js');
		//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';

		//llxHeader("",$langs->trans("Inventario"),$help_url,'','','',$aArrjs,$aArrcss);
llxHeader("",$langs->trans("assets"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Assets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
print '<table class="border" width="100%">';
/*

		// Entrepot Almacen
print '<tr>';
print '<td width="25%" class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="3">';
$res = $objGroup->fetchAll('ASC','code',0,0,array(1=>1),'AND',$filterstatic);
$options = '<option value="0">'.$langs->trans('All').'</option>';
if ($res > 0)
{
	$lines = $objGroup->lines;
	foreach ($lines  AS $J => $line)
	{
		$selected = '';
		if ($fk_group == $line->id) $selected = ' selected';
		$options .= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
	}
}
print '<select name="fk_group">'.$options.'</select>';
print '</td>';
print '</tr>';
*/
print '<tr>';
print '<td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
print $form->select_date($date_ini,'di_',0,0,1);
print '</td>';
print '</tr>';

print '<tr>';
print '<td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
print $form->select_date($date_fin,'df_',0,0,1);
print '</td>';
print '</tr>';
/*
// level
print '<tr><td class="fieldrequired">'.$langs->trans('Asset').'</td><td colspan="2">';
print $form->selectarrayv('level',$aEstado,GETPOST('level'),1);
print '</td>';
print '</tr>';
*/
print '</table>';

print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

		//YEMER


if(($action == 'edit'||  $action=='edits'))
{

	$_SESSION['date_inidet'] = serialize($date_ini);
	$_SESSION['date_findet'] = serialize($date_fin);


	$aDateini = dol_getdate($date_ini);
	$aDate = dol_get_prev_day($aDateini['mday'], $aDateini['mon'], $aDateini['year']);
	$date_ini = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year']);
	$aDatefin = dol_getdate($date_fin);
	$date_fin = $adatefin['year'].'-'.$adatefin['mon'].'-'.$adatefin['mday'];
	$date_fin .= ' 23:59:59';
	$date_fin = dol_mktime(23,59,59,$aDatefin['mon'],$aDatefin['mday'],$aDatefin['year']);
	$aDatefin = dol_getdate($date_fin);

	$day=$aDatefin['mday'];
	$month=$aDatefin['mon'];
	$year=$aDatefin['year'];



	dol_htmloutput_mesg($mesg);


	$date_cal = dol_mktime(0,0,0,$aDatefin['mon'],$aDatefin['mday'],$aDatefin['year']);




	$filter = '';

	$filter.= " AND t.date_adq BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_fin)."'";
	/*

	if($level==0)
	{
		$res = $objAssets->fetchAll('ASC','t.ref,t.ref',0,0,array(1=>1),'AND',$filter);
	}
	else
	{
		$res = $objAssets->fetchAll('ASC','t.ref_ext,t.ref_ext',0,0,array(1=>1),'AND',$filter);
	}
	*/
	$res = $objAssets->fetchAll('ASC','t.ref,t.ref',0,0,array(1=>1),'AND',$filter);


	$Asst1 ='';
	$Asst2='';

	if($res>0)
	{
		$Asst2 = $objAssets->descrip;
	}
	if ($res>0)
	{
		$lines = $objAssets->lines;
	}
	//vamos a armar los grupos que tenga para ordenar
	$aGroupasset = array();
	if ($res>0)
	{
		foreach($lines AS $j => $line)
			$aGroupasset[$line->type_group][$line->ref] = $line;
	}
			//if ($conf->almacen->dir_output)
	//vamos a imprimir el resultado
	//ksort($aGroupasset);
	if ($res>0)
	{
		//$object->fetch_thirdparty();

		print_barre_liste($langs->trans("Assets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

		print '<table class="border" width="100%">';
		print '<tr class="liste_titre">';
		print '<td align="center">'.$langs->trans("Nro").'</td>';
		print '<td nowrap align="center">'.$langs->trans("Code").'</td>';
		print '<td nowrap align="center">'.$langs->trans("Fieldref_ext").'</td>';
		print '<td  align="center">'.$langs->trans("Label").'</td>';
		print '<td  align="center">'.$langs->trans("Fielddate_adq").'</td>';
		print '<td  align="left">'.$langs->trans("Fieldcoste").'</td>';
		print '<td  align="center">'.$langs->trans("Property").'</td>';
		print '<td  align="center">'.$langs->trans("Location").'</td>';
		print '<td  align="center">'.$langs->trans("Dateassignment").'</td>';
		print '<td  align="center">'.$langs->trans("Departament").'</td>';
		print '<td  align="center">'.$langs->trans("Responsible").'</td>';
		print '<td  align="center">'.$langs->trans("Been").'</td>';
		print '<td  align="center">'.$langs->trans("ValorAct").'</td>';
		print '<td  align="center">'.$langs->trans("DepreciationAcum").'</td>';
		print '<td  align="center">'.$langs->trans("Balance").'</td>';
		print '<td  align="center">'.$langs->trans("Status").'</td>';
		//print '<td align="right">'.$langs->trans("Responsable").'</td>';
		//print '<td align="center">'.$langs->trans("date assigne").'</td>';
		print '</tr>';

		$Cont=0;


		foreach ($objAssets->lines AS $j => $line)
		{
			$var = !$var;
			$objAssets->statut = $line->statut;
			$objAssets->id = $line->id;

			$objAssets->label = $line->descrip;
			$Cont+=1;

			$objBalance = new Assetsbalanceext($db);
							//echo '<hr>id '.$line->id;
			$resb2=$objBalance->fetch(0,$line->id);

			$Asst1 ='';
			$objuser->fetch($obj->fk_user);
							//$resb1 = $objuser->fetch($obj->fk_user);

			$Asst1 = $line->ref;
			$Asst2 = $line->descrip;
			$Asst3 = $line->date_adq;

			print "<tr $bc[$var]>";
			print '<td>'.$Cont.'</td>';
				// etiqueta
			$objAssets->ref = $line->ref;
			print '<td nowrap >'.$objAssets->getNomUrl().'</td>';
			$objAssets->ref = $line->ref_ext;
			print '<td nowrap >'.$objAssets->getNomUrl().'</td>';
			print '<td>'.$line->descrip.'</td>';
				// fecha de adquisicion
			print '<td>'.dol_print_date($line->date_adq,'day').'</td>';
				//costo
			print '<td align="right">'.price($line->coste).'</td>';

			$objMproperty =new Mproperty($db);
			$objMproperty->fetch($line->fk_property);
			$objMlocation =new Mlocation($db);
			$objMlocation->fetch($line->id);
				//
			$res = $objAssdet->fetch_ult($line->id);
			if ($res >0)
			{
				$objAss->fetch($objAssdet->fk_asset_assignment);
				$objAdherent->fetch($objAss->fk_user);
				$objMproperty =new Mproperty($db);
				$objMproperty->fetch($objAss->fk_property);
				$objMlocation =new Mlocation($db);
				$objMlocation->fetch($objAss->fk_location);
			}
				// inmueble
			print '<td>'.$objMproperty->label.'</td>';
				// location
			print '<td>'.$objMlocation->detail.'</td>';
			$responsible = '';
			$departament_name='';
			if($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER == 0)
			{
				//fecha de asignacion
				print '<td align="center">'.$langs->trans('Nodefined').'</td>';

				//departament
				print '<td align="left">'.$line->departament_name.'</td>';
				$departament_name = $line->departament_name;
				// responsable
				print '<td align="left">'.$line->resp_name.'</td>';
				$responsible = $line->resp_name;
			}
			else
			{

				$res = $objAssdet->fetch_ult($line->id);
				if ($res >0)
				{
					$objAss->fetch($objAssdet->fk_asset_assignment);
					$objAdherent->fetch($objAss->fk_user);
					//fecha de asignacion
					print '<td align="center">'.dol_print_date($objAss->date_assignment,'day').'</td>';
						//departament
					print '<td align="center">'.$departament_name.'</td>';
						// responsable
					$FechaAsignacion=$objAss->date_assignment;
					print '<td align="center">'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
					$responsible = $objAdherent->lastname.' '.$objAdherent->firstname;
				}
				else
				{
					print '<td align="center">'.'</td>';
					print '<td align="center">'.'</td>';
					$FechaAsignacion="";
					print '<td align="center">'.'</td>';
				}
			}
			//condicion
			if ($line->been)
			{
				$objbeen->fetch($line->been);
				print '<td align="center">'.$objbeen->label.'</td>';
			}
			else
				print '<td></td>';
				// Depreciation
			$type_group = $line->type_group;
			$country = $conf->global->ASSETS_CURRENCY_DEFAULT;
			$objAssetsmov     = new Assetsmovext($db);
			$res = $objAssetsmov->process_depr($month,$year,$country,$type_group,$day,$line->id);
			$aArray = $objAssetsmov->array;

				// VALOR ACTUALIZADO
			print '<td align="right">'.price(price2num($aArray[$line->id]['amount_balance'],'MT')).'</td>';
			$cValoract=$aArray[$line->id]['amount_balance'];
				// DEPRECIACION ACUMULADO
			print '<td align="right">'.price(price2num($aArray[$line->id]['amount_balance_depr'],'MT')).'</td>';
			$cDepreacum=$aArray[$line->id]['amount_balance_depr'];
				// BALANCE
			print '<td align="right">'.price(price2num($aArray[$line->id]['amount_balance']-$aArray[$line->id]['amount_balance_depr'],'MT')).'</td>';
			$cBalance=$aArray[$line->id]['amount_balance']-$aArray[$line->id]['amount_balance_depr'];
			print '<td nowrap align="right">'.$objAssets->getLibStatut(4).'</td>';
			print '</tr>';

			$aReportasset[]=array('Codigo'=>$line->ref,'Codigoext'=>$line->ref_ext,'Etiqueta'=>$line->descrip,'FechaAdquisicion'=>$line->date_adq,'costo'=>$line->coste,'Inmueble'=>$objMproperty->label,'location'=>$objMlocation->detail,'FechaAsignacion'=>$FechaAsignacion,'Departament'=>$departament_name,'Responsable'=>$responsible,'Condicion'=>($line->been?$objbeen->label:''),'Valoract'=>$cValoract,'Depreacum'=>$cDepreacum,'Balance'=>$cBalance,'Estado'=>$objAssets->getLibStatut(0),'type'=>"D");

			$i++;
		}

		print '</table>';



		$_SESSION['aReportassetdet'] = serialize($aReportasset);

		$_SESSION['cGroupdet'] = serialize($cGroup);
		$_SESSION['levelass'] = serialize($level);



		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction"  href="'.$_SERVER['PHP_SELF'].'?action=">'.$langs->trans("Volver").'</a>';
				//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_property='.$fk_property.'&fk_equipment='.$fk_equipment.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_group='.$fk_group.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';

		print '</div>';

			//generar archivo pdf
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				//if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $objAssets->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			$model='fractalassetsref';
				//$ret = $objAssets->fetch($id);
				// Reload to get new records
				//$object->fetch_lines();
			$result=$objAssets->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}

	}
			//header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
			//exit;
}

print '<div class="tabsAction">';
			//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';
			// ancre

$diradd = '';
$filename = 'ref';
//if ($fk_group>0)
//{
	//$objGroup->fetch($fk_group);
	//if ($objGroup->id == $fk_group)
		//$filename=dol_sanitizeFileName("ref").$diradd;
//}
			//cambiando de nombre al reporte
$filedir=$conf->assets->dir_output.'/'.$filename;
//echo '<hr>'.$conf->assets->dir_output;

$urlsource=$_SERVER['PHP_SELF'].'?level='.$level;
$genallowed=1;
$delallowed=1;


		//$objGroup->modelpdf = 'fractalinventario';
$objGroup->modelpdf = 'fractalassetsref';

print '<br>';
print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objGroup->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";

$db->close();

llxFooter();
?>
