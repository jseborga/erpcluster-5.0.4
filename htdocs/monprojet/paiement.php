<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */


require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/guarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskcontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresource.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaymentdeduction.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaiementext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaiementdetext.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/dict.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';



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

 if (empty($ver))
 {
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
 }




//budget
if ($conf->budget->enabled)
{
	//dol_include_once('/budget/class/html.formadd.class.php');
	dol_include_once('/budget/class/budget.class.php');
	dol_include_once('/budget/class/items.class.php');
	dol_include_once('/budget/class/cunits.class.php');
	dol_include_once('/budget/class/typeitem.class.php');
}
if ($conf->purchase->enabled)
{
	dol_include_once('/purchase/class/fournisseurcommandeext.class.php');
}
//if ($conf->addendum->enabled)
//	require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

$id=GETPOST('id','int');
$idp=GETPOST('idp','int');
$idd=GETPOST('idd','int');
$code = GETPOST('code');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$project_id = $id;
$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);
$backtopage=GETPOST('backtopage','alpha');
$sortfield = GETPOST("sortfield","alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="DESC";
$offset = $conf->liste_limit * $page ;

//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$projectstatic = new Project($db);
$object = new Task($db);
$taskext = new Taskext($db);
$mobject = new Taskext($db);
$contratadd = new Contratext($db);
$objdoc = new Projettasktimedoc($db);
$taskcontrat = new Projettaskcontrat($db);
$objecttaskadd = new Projettaskadd($db);
$objuser  = new User($db);
$objpay   = new Projetpayment($db);
$objpaytemp = new Projetpayment($db);
$objpayde = new Projetpaymentdeduction($db);
$taskpay  = new Projettaskpayment($db);
$objectptr 	 = new Projettaskresource($db);
$objpaiement = new Projetpaiementext($db);
$objpaiementdet = new Projetpaiementdetext($db);
$objpaiementdettmp = new Projetpaiementdetext($db);
$categorie = new Categorie($db);
$product = new Product($db);
$societe = new Societe($db);

//$cunits   = new Cunits($db);
if ($conf->budget->enabled)
{
	$budget = new Budget($db);
	$typeitem = new Typeitem($db);
	$items    = new Items($db);
}
//armamos los campos obligatorios
$aHeaderTpl['llx_projet_task'] = array('ref' => 'ref',
	'label' => 'label',
	'hilo' => 'hilo',
	'login' => 'login',
	'fechaini'=>'fechaini',
	'fechafin'=>'fechafin',
	'group'=>'group',
	'type'=>'type',
	'typename'=>'typename',
	'unitprogram'=>'unitprogram',
	'unit'=>'unit',
	'price' => 'price',);
$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aCampodate = array('fechaini' =>'date_start',
	'fechafin' => 'date_end');

$userstatic    = new User($db);
$companystatic = new Societe($db);
$extrafields   = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($projectstatic->table_element);
$extralabels_task=$extrafields_task->fetch_name_optionals_label($object->table_element);

//if ($conf->addendum->enabled)
//	$addendum = new Addendum($db);

//recuperamos array type_resource
$aTypeResource = load_type_resource();
//verificamos la estructura si tiene budget

	//verificamos si tiene budget
list($fk_budget,$aCat) = get_categorie($id);
$aStrbudget = unserialize($_SESSION['aStrbudget']);
$aStrgroupcat = $aStrbudget[$fk_budget]['aStrgroupcat'];

if (!empty(GETPOST('type_resource')))
{
	$aStrbudget = unserialize($_SESSION['aStrbudget']);
	$aStrgroupcat = $aStrbudget[$fk_budget]['aStrgroupcat'];
	foreach ($aTypeResource AS $j => $data)
	{
		if (GETPOST('type_resource') == $data['code'])
		{
			$nom_object = $data['object'];
			$group_resource = $data['group'];
			$fk_object = GETPOST($data['fk_object']);
			$code_structure = $aStrgroupcat[$data['group']];
			$nom_objectdet = $data['objectdet'];
			$fk_objectdet = GETPOST($data['fk_objectdet']);
		}
	}
}

if ($id || $ref)
{
	$projectstatic->fetch($id,$ref);
	$id=$projectstatic->id;
}
if ($idd)
{
	$objpayde->fetch($idd);
	if ($idd == $objpayde->id)
	{
		$idp  = $objpayde->fk_projet_payment;
		$code = $objpayde->code;
	}
}
if ($idp) $objpaiement->fetch($idp);

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);

$langs->load("monprojet@monprojet");
$langs->load("users");
$langs->load("projects");


/*
 * Actions
 */

if ($action == 'confirm_delete' && $_REQUEST['confirm'] == 'yes' && $user->rights->monprojet->paip->del)
{
	$res = $objpaiement->fetch($idp);
	$db->begin();
	$res = $objpaiement->delete($user);
	if ($res<=0)
	{
		$error++;
		setEventMessages($objpaiement->error,$objpaiement->errors,'errors');
	}
	if (!$error)
	{
		$filterstatic = " AND t.fk_projet_paiement = ".$idp;
		$res = $objpaiementdet->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
		$lines = $objpaiementdet->lines;
		foreach ((array) $lines AS $j => $obj)
		{
			$resd = $objpaiementdet->fetch($obj->id);
			if ($resd==1)
			{
				$resdel = $objpaiementdet->delete($user);
				if ($resdel<=0)
				{
					$error++;
					setEventMessages($objpaiementdet->error,$objpaiementdet->errors,'errors');
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Successfulldelete'),null,'mesgs');
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/paiement.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$db->rollback();
		$action = '';
	}
}

// EXCEL


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

 	$aReportpaimentdet = unserialize($_SESSION['aReportpaiementdet']);
 	$aReportpaiementencdet = unserialize($_SESSION['aReportpaiementencdet']);
 	$date_ini = unserialize($_SESSION['date_inidet']);
 	$fk_conc= unserialize($_SESSION['fk_conc']);

	// TITULO
 	$objPHPExcel->setActiveSheetIndex(0);
 	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
 	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);
	//$this->activeSheet->getDefaultRowDimension()->setRowHeight($height);
 	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	// COLOR DEL TITULO
 	$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray(
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
 	$sheet->mergeCells('A2:K2');
 	$sheet->setCellValueByColumnAndRow(0,2, $langs->trans("Paymentform"));
 	$sheet->mergeCells('A2:K2');
 	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
 		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
	// ENCABEZADO
	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));

 	$supplierex=$aReportpaiementencdet[0]['supplier'];
 	$planilaex=$aReportpaiementencdet[0]['planilla'];
 	$dateex=$aReportpaiementencdet[0]['date'];
 	$detailex=$aReportpaiementencdet[0]['detail'];

 	$objPHPExcel->getActiveSheet()->setCellValue('B3',$langs->trans("Supplier"));
 	//Conc
 	$objPHPExcel->getActiveSheet()->setCellValue('B4',$langs->trans("Planilla"));

 	$objPHPExcel->getActiveSheet()->setCellValue('B5',$langs->trans("Date"));
 	//Conc
 	$objPHPExcel->getActiveSheet()->setCellValue('B6',html_entity_decode($langs->trans("Detail")));


 	$objPHPExcel->getActiveSheet()->setCellValue('C3',$supplierex);
 	$objPHPExcel->getActiveSheet()->setCellValue('C4',$planilaex);
 	$objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

 	$objPHPExcel->getActiveSheet()->setCellValue('C5',dol_print_date($dateex,'day'));
 	$objPHPExcel->getActiveSheet()->setCellValue('C6',$detailex);

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

	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("Nro"));

 	$objPHPExcel->getActiveSheet()->setCellValue('B8',$langs->trans("Task"));
	// Ref
 	$objPHPExcel->getActiveSheet()->setCellValue('C8',html_entity_decode($langs->trans("Description")));
	// referencia
 	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("Unit"));
	// Trabajo solicitado
 	$objPHPExcel->getActiveSheet()->setCellValue('E8',$langs->trans("PU"));

 	// ANTERIOR
	// Quant
	$objPHPExcel->getActiveSheet()->setCellValue('F7',$langs->trans("Anterior"));
	$objPHPExcel->getActiveSheet()->mergeCells('F7:G7');
	$sheet->getStyle('F7')->getAlignment()->applyFromArray(
 		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));




 	$objPHPExcel->getActiveSheet()->setCellValue('F8',$langs->trans("Qty"));
 	// Valor
 	$objPHPExcel->getActiveSheet()->setCellValue('G8',$langs->trans("Amount"));

 	// ACTUAL
 	// Entity
 	$objPHPExcel->getActiveSheet()->setCellValue('H7',$langs->trans("Actual"));
 	$objPHPExcel->getActiveSheet()->mergeCells('H7:I7');
 	$sheet->getStyle('H7')->getAlignment()->applyFromArray(
 		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

 	$objPHPExcel->getActiveSheet()->setCellValue('H8',$langs->trans("Qty"));
 	// Valor
 	$objPHPExcel->getActiveSheet()->setCellValue('I8',$langs->trans("Amount"));

 	//ACUMULADO
 	$objPHPExcel->getActiveSheet()->setCellValue('J7',$langs->trans("Acumulado"));
 	$objPHPExcel->getActiveSheet()->mergeCells('J7:K7');
 	$objPHPExcel->getActiveSheet()->getStyle('J7')->getAlignment()->applyFromArray(
 		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));


 	$objPHPExcel->getActiveSheet()->setCellValue('J8',$langs->trans("Qty"));
 	// Valor
 	$objPHPExcel->getActiveSheet()->setCellValue('K8',$langs->trans("Amount"));

	// TABLA COLOR INFERIOR
 	$objPHPExcel->getActiveSheet()->getStyle('A7:K8')->applyFromArray(
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
 	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
 	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
 	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
 	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
 	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
 	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

	//FORMATO DE LAS COLUMNAS

 	$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

 	$objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);


 	$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

 	$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

 	$objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

 	$objPHPExcel->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

 	$objPHPExcel->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
 	$objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

 	$objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

	// CUERPO
 	$j=9;
 	$contt=0;
 	$sumaant=0;
 	$sumaact=0;
 	$sumaacu=0;

 	foreach ((array) $aReportpaimentdet AS $i => $lines)
 	{



 		$tarea = $lines['tarea'];
 		$detalle = $lines['detalle'];
 		$unidad = $lines['unidad'];
 		$pu = $lines['pu'];
 		$antcant = $lines['antcant'];
 		$antimport = $lines['antimport'];
 		$actcant = $lines['actcant'];
 		$actimport = $lines['actimport'];
 		$acumcant = $lines['acumcant'];
 		$acumimport = $lines['acumimport'];
 		$sumaant=$sumaant+$antimport;
 		$sumaact=$sumaact+$actimport;
 		$sumaacu=$sumaacu+$acumimport;
		// VISTA
 		$contt++;



 		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
 		->setCellValue('B' .$j,$tarea)
 		->setCellValue('C' .$j,$detalle)
 		->setCellValue('D' .$j,html_entity_decode($unidad))
 		->setCellValue('E' .$j,price2num($pu,'MT'))
 		->setCellValue('F' .$j,price2num($antcant,'MT'))
 		->setCellValue('G' .$j,price2num($antimport,'MT'))
 		->setCellValue('H' .$j,price2num($actcant,'MT'))
 		->setCellValue('I' .$j,price2num($actimport,'MT'))
 		->setCellValue('J' .$j,price2num($acumcant,'MT'))
 		->setCellValue('K' .$j,price2num($acumimport,'MT'));

		// BORDES DE LA VISTA
 		$objPHPExcel->getActiveSheet()->getStyle('A8:K'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
 		$j++;
 	}
 	$objPHPExcel->getActiveSheet()->setCellValue('E' .$j,"TOTAL")
 	->setCellValue('G' .$j,$sumaant)
 	->setCellValue('I' .$j,$sumaact)
 	->setCellValue('K' .$j,$sumaacu);

 	// COLOR TOTALES

 	$objPHPExcel->getActiveSheet()->getStyle('E'.$j.':'.'K'.$j)->applyFromArray(
 		array(
 			'font'  => array(
 				'bold'  => true,
 				'color' => array('rgb' => 'FF0000'),
 				'size'  => 11,
 				'name'  => 'Calibri'
 				)));

 	$objPHPExcel->getActiveSheet()->getStyle('E'.$j.':'.'K'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

 	$objPHPExcel->setActiveSheetIndex(0);
	// Save Excel 2007 file
 	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
 	$objWriter->save("excel/reportconsol.xlsx");
 	$_SESSION['docsave']='reportconsol.xlsx';
 	header("Location: ".DOL_URL_ROOT.'/monprojet/fiche_export.php?archive=reportconsol.xlsx');
 }


// Action to add record
if ($action == 'add')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/paiement.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;
	$datepay  = dol_mktime(12, 0, 0, GETPOST('dpmonth'),  GETPOST('dpday'),  GETPOST('dpyear'));

	/* object_prop_getpost_prop */
	$new = dol_now();
	$db->begin();
	$objpaiement->fk_projet=$id;
	$objpaiement->ref=GETPOST('ref');
	$objpaiement->fk_soc=GETPOST('socid');
	$objpaiement->fk_fourn_facture=0;
	$objpaiement->date_payment=$datepay;
	$objpaiement->date_request=$datepay;
	$objpaiement->amount=0;
	//$objpaiement->document=GETPOST('document','alpha');
	$objpaiement->detail=GETPOST('description','alpha');
	$objpaiement->fk_user_create=$user->id;
	$objpaiement->fk_user_mod=$user->id;
	$objpaiement->datec = $new;
	$objpaiement->datem = $new;
	$objpaiement->tms = $new;
	$objpaiement->status=0;


	if (empty($objpaiement->ref))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
	}
	if ($objpaiement->fk_soc<=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Supplier")), null, 'errors');
	}

	if (empty($objpaiement->date_payment))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Date")), null, 'errors');
	}

	if (empty($objpaiement->detail))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Detail")), null, 'errors');
	}

	if (! $error)
	{
		$idr=$objpaiement->create($user);
		if ($idr>0)
		{
			//creamos los registros detalle
			$aQty 		= GETPOST('qty');
			$aQtyant 	= GETPOST('qty_ant');
			$nameobjdet = GETPOST('nameobjdet');
			$nameobj 	= GETPOST('nameobj');
			$fkobj 		= GETPOST('fkobj');
			$product 	= GETPOST('product');
			$detail 	= GETPOST('detail');
			$unit 		= GETPOST('unit');
			$subprice 	= GETPOST('subprice');
			$price 		= GETPOST('price');
			$task 		= GETPOST('task');
			foreach ($aQty AS $fk_projet_task => $aaQty)
			{
				foreach ($aaQty AS $fk_objectdet => $value)
				{
					if ($value > 0)
					{
						$objpaiementdet->fk_projet_paiement = $idr;
						$objpaiementdet->ref = 'xxx';
						$objpaiementdet->date_paiement = $datepay;
						$objpaiementdet->fk_projet_task = $fk_projet_task;
						$objpaiementdet->fk_object = $fkobj[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->object = $nameobj[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->fk_objectdet = $fk_objectdet;
						$objpaiementdet->objectdet = $nameobjdet[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->fk_user_create = $user->id;
						$objpaiementdet->fk_user_mod = $user->id;
						$objpaiementdet->fk_user_create = $user->id;
						$objpaiementdet->fk_product = $product[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->fk_facture_fourn = 0;
						$objpaiementdet->detail = $detail[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->fk_unit = $unit[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->qty_ant = $aQtyant[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->qty = $value;
						$objpaiementdet->subprice = $subprice[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->price = $price[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->total_ht = ($subprice[$fk_projet_task][$fk_objectdet]*$value)+0;
						$objpaiementdet->total_ttc = ($price[$fk_projet_task][$fk_objectdet]*$value)+0;
						$objpaiementdet->datec = $new;
						$objpaiementdet->datem = $new;
						$objpaiementdet->tms = $new;
						$objpaiementdet->status = 0;
						$resd = $objpaiementdet->create($user);
						if ($resd <=0)
						{
							$error++;
							setEventMessages($objpaiementdet->error,$objpaiementdet->errors,'errors');
						}
					}
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
				// Creation OK
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/paiement.php?id='.$id.'&idp='.$idr,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$db->rollback();
				// Creation KO
		if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
		else  setEventMessages($object->error, null, 'errors');
		$action='create';
		$_GET['socid'] = $objpaiement->fk_soc;
	}
}

	// Action to update record
if ($action == 'update')
{
	if (isset($_POST['addapp']))
	{
		$action = 'confupdate';
	}
}
if ($action == 'update' || ($action == 'confirm_update' && $_REQUEST['confirm'] == 'yes' && $user->rights->monprojet->paip->paiapp) )
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/paiement.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	$lApp = false;
	if ($action == 'confirm_update' && $_REQUEST['confirm'] == 'yes' && $user->rights->monprojet->paip->paiapp)
	{
		$aPost = unserialize($_SESSION['aPost']);
		$_POST = $aPost[$id];
		$lApp = true;
	}
	$error=0;
	$datepay  = dol_mktime(12, 0, 0, GETPOST('dpmonth'),  GETPOST('dpday'),  GETPOST('dpyear'));

	/* object_prop_getpost_prop */
	$new = dol_now();
	$db->begin();
	//$objpaiement->ref=GETPOST('ref');
	if (!$lApp)
		$objpaiement->fk_soc=GETPOST('socid');
	$objpaiement->fk_fourn_facture=0;
	$objpaiement->date_payment=$datepay;
	$objpaiement->date_request=$datepay;
	$objpaiement->amount=0;
	//$objpaiement->document=GETPOST('document','alpha');
	$objpaiement->detail=GETPOST('description','alpha');
	$objpaiement->fk_user_mod=$user->id;
	$objpaiement->datem = $new;
	$objpaiement->tms = $new;
	$objpaiement->status=0;
	if ($lApp)
	{
		//recuperamos la sumatoria total
		$objpaiementtmp = new Projetpaiementext($db);
		$objpaiementtmp->fetch($idp);
		$res = $objpaiementtmp->fetch_lines();
		$total_ht = 0;
		$total_tva = 0;
		$total_ttc = 0;
		if ($res > 0)
		{
			foreach ($objpaiementtmp->lines AS $j => $line)
			{
				$total_tva+= $line->total_tva;
				$total_ht+= $line->total_ht;
				$total_ttc+= $line->total_ttc;
			}
		}
		$objpaiement->status=1;
		$objpaiement->total_tva = $total_tva;
		$objpaiement->total_ht  = $total_ht;
		$objpaiement->total_ttc = $total_ttc;
	}

	if (empty($objpaiement->ref))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
	}

	if (! $error)
	{
		$res=$objpaiement->update($user);
		if ($res>0 && $idp>0)
		{
			//actualizamos los registros detalle
			$aQty 		= GETPOST('qty');
			$aQtyant 	= GETPOST('qty_ant');
			$fkobj 		= GETPOST('fkobj');
			$nameobj 	= GETPOST('nameobj');
			$nameobjdet = GETPOST('nameobjdet');
			$product 	= GETPOST('product');
			$detail 	= GETPOST('detail');
			$unit 		= GETPOST('unit');
			$subprice 	= GETPOST('subprice');
			$price 		= GETPOST('price');
			$task 		= GETPOST('task');
			foreach ($aQty AS $fk_projet_task => $aaQty)
			{
				foreach ($aaQty AS $fk_objectdet => $value)
				{
					$value+=0;
					//buscamos y actualizamos
					$resd = $objpaiementdet->fetch(0,null,$idp,$fk_projet_task,$fk_objectdet,'CommandeFournisseurLigne');
					if ($resd == 1 && $value ==0)
					{
						//eliminamos el registro
						$resdel = $objpaiementdet->delete($user);
						if ($resdel <=0)
						{
							$error++;
							setEventMessages($objpaiementdet->error,$objpaiementdet->errors,'errors');
						}
					}
					elseif ($resd ==1)
					{
						$objpaiementdet->date_paiement = $datepay;
						$objpaiementdet->fk_projet_task = $fk_projet_task;
						$objpaiementdet->fk_object = $fkobj[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->object = $nameobj[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->fk_objectdet = $fk_objectdet;
						$objpaiementdet->objectdet = $nameobjdet[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->fk_user_mod = $user->id;
						$objpaiementdet->fk_user_create = $user->id;
						$objpaiementdet->fk_product = $product[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->fk_facture_fourn = 0;
						$objpaiementdet->detail = $detail[$fk_projet_task][$fk_objectdet];
						$objpaiementdet->fk_unit = $unit[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->qty_ant = $aQtyant[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->qty = $value;
						$objpaiementdet->subprice = $subprice[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->price = $price[$fk_projet_task][$fk_objectdet]+0;
						$objpaiementdet->total_ht = ($subprice[$fk_projet_task][$fk_objectdet]*$value)+0;
						$objpaiementdet->total_ttc = ($price[$fk_projet_task][$fk_objectdet]*$value)+0;
						$objpaiementdet->datem = $new;
						$objpaiementdet->tms = $new;
						if ($lApp) $objpaiementdet->status = 1;
						$resd = $objpaiementdet->update($user);
						if ($resd <=0)
						{
							$error++;
							setEventMessages($objpaiementdet->error,$objpaiementdet->errors,'errors');
						}
					}
					elseif($resd == 0)
					{
						if ($value > 0)
						{
							$objpaiementdet->fk_projet_paiement = $idp;
							$objpaiementdet->ref = 'xxx';
							$objpaiementdet->fk_projet_task = $fk_projet_task;
							$objpaiementdet->date_paiement = $datepay;
							$objpaiementdet->fk_object = $fkobj[$fk_projet_task][$fk_objectdet];
							$objpaiementdet->object = $nameobj[$fk_projet_task][$fk_objectdet];
							$objpaiementdet->fk_objectdet = $fk_objectdet;
							$objpaiementdet->objectdet = $nameobjdet[$fk_projet_task][$fk_objectdet];
							$objpaiementdet->fk_user_create = $user->id;
							$objpaiementdet->fk_user_mod = $user->id;
							$objpaiementdet->fk_user_create = $user->id;
							$objpaiementdet->fk_product = $product[$fk_projet_task][$fk_objectdet]+0;
							$objpaiementdet->fk_facture_fourn = 0;
							$objpaiementdet->detail = $detail[$fk_projet_task][$fk_objectdet];
							$objpaiementdet->fk_unit = $unit[$fk_projet_task][$fk_objectdet]+0;
							$objpaiementdet->qty_ant = $aQtyant[$fk_projet_task][$fk_objectdet]+0;
							$objpaiementdet->qty = $value;
							$objpaiementdet->subprice = $subprice[$fk_projet_task][$fk_objectdet]+0;
							$objpaiementdet->price = $price[$fk_projet_task][$fk_objectdet]+0;
							$objpaiementdet->total_ht = ($subprice[$fk_projet_task][$fk_objectdet]*$value)+0;
							$objpaiementdet->total_ttc = ($price[$fk_projet_task][$fk_objectdet]*$value)+0;
							$objpaiementdet->datec = $new;
							$objpaiementdet->datem = $new;
							$objpaiementdet->tms = $new;
							$objpaiementdet->status = 0;
							if ($lApp) $objpaiementdet->status = 1;
							$resd = $objpaiementdet->create($user);
							if ($resd <=0)
							{
								$error++;
								setEventMessages($objpaiementdet->error,$objpaiementdet->errors,'errors');
							}
						}
					}
					else
					{
						$error++;
						setEventMessages($objpaiementdet->error,$objpaiementdet->errors,'errors');
					}
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		if ($lApp)
		{
			setEventMessages($langs->trans('Se guardo y aprobo el pago, favor generar la factura correspondiente'),null,'mesgs');
		}
		// Creation OK
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/paiement.php?id='.$id.'&idp='.$objpaiement->id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$db->rollback();
				// Creation KO
		if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
		else  setEventMessages($object->error, null, 'errors');
		$action='edit';
	}
}

if ($action == 'deldoc' && $user->rights->monprojet->paip->mod)
{
	$error=0;
	if (!$objpay->id > 0 || !$projectstatic->id > 0)
	{
		setEventMessage($langs->trans("Paymentnovalid"),'errors');
		$error++;
	}
	else
	{
		$namefile = GETPOST('namedoc');
	   	//buscamos
		$db->begin();

		$aDoc = explode(';',$objpay->document);
		$document = '';
		foreach ((array) $aDoc AS $i => $name)
		{
			if ($name != $namefile)
			{
				if ($document) $document.=';';
				$document .= $name;
			}
		}
		$objpay->document = $document;
		$res = $objpay->update($user);
		if (!$res>0) $error++;

	    //del photo
		$dirproj = $projectstatic->ref;
		$dir    = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj.'/'.'pay/'.$idp;

		$fileimg=$dir.'/'.$namefile;
		dol_delete_file($fileimg);
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
	$action = '';
}

//adddoc
if ($action == 'adddoc' && $user->rights->monprojet->paip->mod)
{
	$error=0;
	if (!$objpay->id > 0 || !$projectstatic->id > 0)
	{
		setEventMessage($langs->trans("Paymentnovalid"),'errors');
		$error++;
	}
	else
	{
	   	//buscamos
		$db->begin();

	    //add photo
	    // Logo/Photo save
		$code = generarcodigo(3);
		$newDir = $idp.$code;
		$dirproj = $projectstatic->ref;
		$dir    = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj.'/'.'pay/'.$idp;
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->monprojet->pho->up4;
			if ($user->rights->monprojet->pho->up3) $mode = 3;
			if ($user->rights->monprojet->pho->up2) $mode = 2;
			if ($user->rights->monprojet->pho->up1) $mode = 1;
			if ($user->rights->monprojet->pho->up5) $mode = 5;

			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = '';
					foreach ((array) $aFile AS $j => $val)
					{
						if (empty($file))
							$file = $newDir;
						else
							$file.= '.'.$val;
					}
					//buscamos el archivo
			    	//modificamos
					if (empty($objpay->document))
						$objpay->document = $file;
					else
						$objpay->document.=';'.$file;
					$objpay->tms = dol_now();
					$res = $objpay->update($user);
					if (!$res>0) $error++;
				}
				else
				{
					$error++;
				}
				$newfile = $dir.'/'.$file;
				$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
				if (! $result > 0)
				{
					$error++;
					$errors[] = "ErrorFailedToSaveFile";
				}
				else
				{
			    	// Create small thumbs for company (Ratio is near 16/9)
			    	// Used on logon for example
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
			    	// Create mini thumbs for company (Ratio is near 16/9)
			    	// Used on menu or for setup page for example
					$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
				}
			}
			else
			{
				$error++;
			}
		}
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
}

if ($action == 'createded')
{
	$objpayde->fk_projet_payment = $idp;
	$objpayde->code = GETPOST('code');
	$objpayde->amount = GETPOST('deduction');
	$objpayde->fk_user_create = $user->id;
	$objpayde->fk_user_mod = $user->id;
	$objpayde->date_create = dol_now();
	$objpayde->tms = dol_now();
	$objpayde->statut = 1;
	$res = $objpayde->create($user);
	if (!$res>0) $error++;
	if ($error)
		$action = 'created';
}

if ($action == 'updateded')
{
	//buscamos
	$objpayde->fetch($idd);
	if ($objpayde->id == $idd && $objpayde->fk_projet_payment == $idp)
	{
		$objpayde->code = GETPOST('code');
		$objpayde->amount = GETPOST('deduction');
		$objpayde->fk_user_mod = $user->id;
		$objpayde->tms = dol_now();
		$objpayde->statut = 1;
		$res = $objpayde->update($user);
		if (!$res>0) $error++;
		if ($error)
			$action = 'editd';
		else
			unset($idd);
	}
}

if ($action == 'updatedet')
{
	//buscamos
	$objpay->fetch($idp);
	if ($objpay->id == $idp)
	{
		$objpay->detail = GETPOST('detail');
		$objpay->tms = dol_now();
		$res = $objpay->update($user);
		if (!$res>0) $error++;
		if ($error)
			$action = 'editdet';
		else
			unset($idp);
	}
}

if ($action == 'addup')
{
    //subida de tareas para el contrato
    //si variable addtask entonces se inserta o actualiza en las tareas del proyecto

	$addtask = GETPOST('addtask');
	$seldate = GETPOST('seldate');

	$error = 0;
    //buscamos el projet
	$res = $projectstatic->fetch($id);
	if (!$res>0)
		$error++;
	$error = 0;
	$aArrData = $_SESSION['aArrData'];
	$table = GETPOST('table');

	$aNewTask = array();
	$db->begin();
	foreach ((array) $aArrData AS $i => $data)
	{
	//variables
	//unit
		$fk_unit = 0;
		if (!empty($data['unit']))
		{
			$cunits->fetch('',$data['unit']);
			if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
			{
		//recuperamos el id de registro
				$fk_unit = $cunits->id;
			}
			else
			{
		//creamos
				$cunits->initAsSpecimen();
				$cunits->code= $data['unit'];
				$cunits->label= $data['unitlabel'];
				$cunits->short_label= $data['unit'];
				$cunits->active= 1;
				$resunit = $cunits->create($user);
				if ($resunit >0) $fk_unit = $resunit;
				else $error++;
			}
		}
	//verificamos si esta relacionado a un item
		$fk_item = 0;
		if (!empty($data['item']))
		{
		    //buscamos
			if ($conf->budget->enabled)
			{
				$resitem = $items->fetch('',$data['item']);
				if ($resitem>0)
				{
					if (STRTOUPPER($items->ref) == STRTOUPPER($data['item']))
					{
						$_POST['options_fk_item'] = $items->id;
						$fk_item = $items->id;
					}
				}
				else
				{
					$error++;
				}
			}
			else
				$_POST['options_fk_item'] = 0;
		}
		else
			$_POST['options_fk_item'] = 0;

	//verificamos tipo item
		$fk_type_item = 0;
		if (!empty($data['type']))
		{
	    //buscamos
			$restype = $typeitem->fetch('',$data['type']);
			if ($restype>0)
			{
				if (STRTOUPPER($typeitem->ref) == STRTOUPPER($data['type']))
				{
					$_POST['options_fk_type'] = $typeitem->id;
					$fk_type_item = $typeitem->id;
				}
			}
			elseif($restype==0)
			{
		//agregamos
				$typeitem->entity = $conf->entity;
				$typeitem->ref = $data['type'];
				$typeitem->detail = $data['typename'];
				$typeitem->fk_user_create = $user->id;
				$typeitem->fk_user_mod = $user->id;
				$typeitem->date_create = dol_now();
				$typeitem->tms = dol_now();
				$typeitem->statut = 1;
				$rest = $typeitem->create($user);
				if (!$rest>0)
					$error++;
				else
				{
					$fk_type_item = $rest;
					$_POST['options_fk_type'] = $rest;
				}
			}
			else
			{
				$error++;
			}
		}
		else
			$_POST['options_fk_type'] = 0;

	//solo se insertaran en tareas del contrato aquellos que tengan c_grupo != 1
	//verificamos las fechas
		$date_start = getformatdate($seldate,$data['fechaini']);
		$date_end   = getformatdate($seldate,$data['fechafin']);
		if ($data['group'] != 1)
		{
	    //buscamos si existe la tarea en el contrato
			$filter = array('ref'=>$data['ref']);
			$filterstatic = " AND fk_projet = ".$id;
			$filterstatic.= " AND fk_contrat = ".$idc;
			$numrow = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,True);
			if ($numrow==1)
			{
		//actualizamos
				$taskcontrat->ref = $data['ref'];
				$taskcontrat->entity = $conf->entity;
				$taskcontrat->fk_projet = $projectstatic->id;
				$taskcontrat->fk_contrat = $idc;
				$taskcontrat->datec = dol_now();
				$taskcontrat->tms = dol_now();
				$taskcontrat->dateo = $date_start;
				$taskcontrat->datee = $date_end;
				$taskcontrat->datev = dol_now();
				$taskcontrat->label = $data['label'];
				$taskcontrat->description = $data['detail'];
				$taskcontrat->priority = $data['priority']+0;
				$taskcontrat->fk_user_creat = $user->id;
				$taskcontrat->fk_user_valid = $user->id;
				$taskcontrat->c_grupo = $data['group'];
				$taskcontrat->fk_type = $fk_type_item;
				$taskcontrat->unit_program = $data['unitprogram'];
				$taskcontrat->fk_unit = $fk_unit;
				$taskcontrat->unit_amount = $data['price'];
				$taskcontrat->fk_statut = 1;
				$res = $taskcontrat->update($user);
				if (!$res>0)
					$error++;
			}
			else
			{
		//nuevo
				$taskcontrat->ref = $data['ref'];
				$taskcontrat->entity = $conf->entity;
				$taskcontrat->fk_projet = $projectstatic->id;
				$taskcontrat->fk_contrat = $idc;
				$taskcontrat->datec = dol_now();
				$taskcontrat->tms = dol_now();
				$taskcontrat->dateo = $date_start;
				$taskcontrat->datee = $date_end;
				$taskcontrat->datev = dol_now();
				$taskcontrat->label = $data['label'];
				$taskcontrat->description = $data['detail'];
				$taskcontrat->priority = $data['priority']+0;
				$taskcontrat->fk_user_creat = $user->id;
				$taskcontrat->fk_user_valid = $user->id;
				$taskcontrat->c_grupo = $data['group'];
				$taskcontrat->fk_type = $fk_type_item;
				$taskcontrat->unit_program = $data['unitprogram'];
				$taskcontrat->fk_unit = $fk_unit;
				$taskcontrat->unit_amount = $data['price'];
				$taskcontrat->fk_statut = 1;
				$res = $taskcontrat->create($user);
				if (!$res>0)
					$error++;

			}
		}
		if ($addtask && !$error)
		{
	    //agregamos o actualizamos las tareas

	    //vamos verificando la existencia de cada uno
			$fk_task_parent = 0;
			if (!empty($data['hilo']))
			{
				if (!empty($aNewTask[$data['hilo']]))
					$fk_task_parent = $aNewTask[$data['hilo']];
				else
					$error++;
			}

	    //buscamos si existe la tarea
			$taskext = new Taskext($db);
			$filter = array(1=>1);
			$filterstatic = " AND t.ref = '".trim($data['ref'])."'";
			$filterstatic.= " AND t.fk_projet = ".$projectstatic->id;
			$res = $taskext->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,1);
			if ($res>0)
			{

				foreach ($taskext->lines AS $k => $obj)
				{
		    // echo '<br>compara '.$obj->ref.' '.$data['ref'].' pro '.$obj->fk_project.' '.$projectstatic->id;
					if (STRTOUPPER(trim($obj->ref)) == STRTOUPPER(trim($data['ref'])) &&
						$obj->fk_project == $projectstatic->id)
					{
						$task = new Task($db);
						if ($task->fetch($obj->id)>0)
						{
			    //buscamos si existe el campo con formato fecha
							foreach((array) $aCampodate AS $k => $value)
							{
								if($data[$k])
								{
				    //verificamos y damos formato a la variable
									$resvalue = convertdate($aDatef,$seldate,$data[$k]);
									$task->$value = $resvalue;
								}
							}

							$aNewTask[$data['ref']] = $task->id;
			    //actualizamos el valor
							$_POST['options_c_grupo'] = $data['group'];
							$_POST['options_unit_program'] = $data['unitprogram'];
							$_POST['options_fk_unit'] = $fk_unit;
							$_POST['options_unit_amount'] = $data['price'];
							$task->dateo = $date_start;
							$task->datee = $date_end;
							$task->fk_task_parent = $fk_task_parent +0;
							$task->ref = $data['ref'];
							$task->label = $data['label'];
							$task->description = $data['detail'];
							$task->priority = $data['priority']+0;
			    // Fill array 'array_options' with data from add form
							$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
							if (!$ret > 0)
								$error++;
			    //actualizamos datos adicionales de la tarea
							$res = $objecttaskadd->fetch('',$task->id);
							if ($res>0 && $objecttaskadd->fk_task == $task->id)
							{
								$objecttaskadd->fk_item = $fk_item;
								$objecttaskadd->fk_type_item = $fk_type_item;

								$objecttaskadd->c_grupo = $data['group'];
								$objecttaskadd->unit_program = $data['unitprogram'];
								$objecttaskadd->fk_unit = $fk_unit;
								$objecttaskadd->unit_amount = $data['price'];
								$objecttaskadd->fk_user_mod = $user->id;
								$objecttaskadd->tms = dol_now();
								$res = $objecttaskadd->update($user);
								if (!$res>0)
									$error++;
							}
							else
							{
								$objecttaskadd->fk_task = $task->id;
								$objecttaskadd->fk_item = $fk_item;
								$objecttaskadd->fk_type_item = $fk_type_item;
								$objecttaskadd->c_grupo = $data['group'];
								$objecttaskadd->unit_program = $data['unitprogram'];
								$objecttaskadd->fk_unit = $fk_unit;
								$objecttaskadd->unit_amount = $data['price'];
								$objecttaskadd->fk_user_create = $user->id;
								$objecttaskadd->fk_user_mod = $user->id;
								$objecttaskadd->date_create = dol_now();
								$objecttaskadd->tms = dol_now();
								$objecttaskadd->statut = 1;
								$res = $objecttaskadd->update($user);
								if (!$res>0)
									$error++;
							}
							if (!$error)
							{
								$result = $task->update($user);
								if (!$result>0)
									$error++;
							}
						}
					}
				}
			}
			else
			{

		//creamos nuevo
				$_POST['options_c_grupo'] = $data['group'];
				$_POST['options_unit_program'] = $data['unitprogram'];
				$_POST['options_fk_unit'] = $fk_unit;
				$_POST['options_unit_amount'] = $data['price'];
				$task = new Task($db);
				$task->initAsSpecimen();

		//buscamos si existe el campo con formato fecha
				foreach((array) $aCampodate AS $k => $value)
				{
					if($data[$k])
					{
			//verificamos y damos formato a la variable
						$resvalue = convertdate($aDatef,$seldate,$data[$k]);
						$task->$value = $resvalue;
					}
				}

				$task->entity = $conf->entity;
				$task->fk_project = $id;
				$task->fk_task_parent = $fk_task_parent +0;
				$task->ref = $data['ref'];
				$task->label = $data['label'];
				$task->dateo = $date_start;
				$task->datee = $date_end;
				$task->description = $data['detail'];
				$task->fk_user_creat = $user->id;
				$task->priority = $data['priority']+0;
				$task->fk_statut = 1;
				$task->datec = dol_now();
				$task->tms = dol_now();

		// Fill array 'array_options' with data from add form
				$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
				$result = $task->create($user,1);
				if (!$result>0)
					$error++;
				if (!$error)
				{
					$objecttaskadd->fk_task = $result;
					$objecttaskadd->c_grupo = $data['group'];
					$objecttaskadd->unit_program = $data['unitprogram'];
					$objecttaskadd->fk_item = $fk_item;
					$objecttaskadd->fk_type_item = $fk_type_item;
					$objecttaskadd->fk_unit = $fk_unit;
					$objecttaskadd->unit_amount = $data['price'];
					$objecttaskadd->fk_user_create = $user->id;
					$objecttaskadd->fk_user_mod = $user->id;
					$objecttaskadd->date_create = dol_now();
					$objecttaskadd->tms = dol_now();
					$objecttaskadd->statut = 1;
					$res = $objecttaskadd->create($user);
					if (!$res>0)
						$error++;
				}

				if (!$error)
				{
					$aNewTask[$data['ref']] = $result;
		    //buscamos al usuario contacto que es un array
					$aLogin = explode(';',$data['login']);
					foreach ((array) $aLogin AS $l => $login)
					{
						$resuser = $objuser->fetch('',$login);
						if ($resuser>0)
							$result = $task->add_contact($objuser->id, 'TASKEXECUTIVE', 'internal');
					}
				}
				else
				{
					$error++;
					setEventMessages($task->error,$task->errors,'errors');
				}
			}
		}
	}
	if (empty($error))
		$db->commit();
	else
	{
		setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
		$db->rollback();
	}
	$action = 'list';
}

//cancel
if ($action == 'confirm_seltask' && GETPOST('cancel')) $action='asignc';

if ($action == 'confirm_seltask' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->task->mod && $projectstatic->statut < 2)
{
	$error = 0;
    //actualizamos las tareas con el contrato seleccionado
	$aTask = $_SESSION['aSelectcont'];
	$db->begin();
	foreach ((array) $aTask AS $fk_task)
	{
	//buscamos la tarea
		$object->fetch($fk_task);
		if ($object->id == $fk_task)
		{
	    //recuperamos los valores extrafields
	    //mismos valores
			$_POST['options_unit_declared'] = $object->array_options['options_c_grupo'];
			$_POST['options_c_grupo'] = $object->array_options['options_c_grupo'];
			$_POST['options_unit_program'] = $object->array_options['options_unit_program'];
			$_POST['options_fk_unit'] = $object->array_options['options_fk_unit'];
			$_POST['options_fk_item'] = $object->array_options['options_fk_item'];
			$_POST['options_unit_amount'] = $object->array_options['options_unit_amount'];
			$_POST['options_unit_ejecuted'] = $object->array_options['options_unit_ejecuted']+0;
			$_POST['options_fk_contrat'] = $idc;
	    // Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
			if ($ret < 0) $error++;
			if (! $error)
			{
				$result=$object->update($user);
				if (!$result > 0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		else
			$error++;
	}
	if (!$error)
	{
		$db->commit();
	// update OK
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
		$db->rollback();

}

// Add


//update
if ($action == 'update' && $user->rights->monprojet->paip->mod)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/paiement.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	if ($objpay->id == $idp)
	{
		$error = 0;
		$objpay->date_payment = dol_mktime(12, 0, 0, GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));

		$objpay->tms = dol_now();
		$objpay->fk_user_mod = $user->id;
	//antes actualizamos o registramos las deducciones
		$aDeduction = GETPOST('deduction');
		foreach ((array) $aDeduction AS $code => $amountde)
		{
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_projet_payment = ".$idp;
			$filterstatic.= " AND t.code = '".$code."'";
			$numpayde = $objpayde->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
			$objpaydenew = Projetpaymentdeduction($db);
			if ($numpayde>0)
			{
			//actualizamos
				$objpaydenew->fetch($objpayde->id);
				$objpaydenew->amount = $amountde;
				$objpaydenew->fk_user_mod = $user->id;
				$objpaydenew->tms = dol_now();
				$res = $objpaydenew->update($user);
				if (!$res > 0) $error++;
			}
			else
			{
			//nuevo
				$objpaydenew->fk_projet_payment = $idp;
				$objpaydenew->code = $code;
				$objpaydenew->amount = $amountde;
				$objpaydenew->fk_user_create = $user->id;
				$objpaydenew->fk_user_mod = $user->id;
				$objpaydenew->date_create = dol_now();
				$objpaydenew->tms = dol_now();
				$objpaydenew->statut = 1;
				$res = $objpaydenew->create($user);
				if (!$res>0) $error++;
			}
		}


		if (empty($error))
		{
			$res = $objpay->update($user);
			if ($res > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$objpay->error.'</div>';
		}
		else
		{
			if ($error)
				$action="edit";
	         // Force retour sur page creation
		}
	}
}

//updatedate
if ($action == 'updatedate' && $user->rights->monprojet->paip->mod)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	if ($objpay->id == $idp)
	{
		$error = 0;
		$objpay->date_payment = dol_mktime(12, 0, 0, GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));
		$objpay->tms = dol_now();
		$objpay->fk_user_mod = $user->id;
		if ($objpay->date_payment <=0) $error++;
		if (empty($error))
		{
			$res = $objpay->update($user);
			if ($res > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			$action = 'editdate';
			$mesg='<div class="error">'.$objpay->error.'</div>';
		}
		else
		{
			if ($error)
				$action="editdate";
	         // Force retour sur page creation
		}
	}
}
// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->paip->app && $objpay->statut == 0)
{
	$result=$objpay->delete($user);
	if ($result > 0)
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$objpay->error.'</div>';
		$action='';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/*
 * View
 */

$form=new Form($db);
$formother     = new FormOther($db);

$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');

if (! empty($conf->use_javascript_ajax))
{
	$arrayofjs=array(
		'/monprojet/js/jsgantt.js',
		'/monprojet/js/graphics.js',

		'/projet/jsgantt_language.js.php?lang='.$langs->defaultlang
		);
}

$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
$arrayofcss = array('/monprojet/css/style.css');
llxHeader("",$langs->trans("Payments"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

$form = new Formv($db);
if ($id > 0 || ! empty($ref))
{
	$projectstatic->fetch($id,$ref);
	if ($projectstatic->societe->id > 0)  $result=$projectstatic->societe->fetch($projectstatic->societe->id);

	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $projectstatic->restrictedProjectArea($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab='payment';

	$head=project_prepare_head($projectstatic);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/monprojet/list.php">'.$langs->trans("BackToList").'</a>';

    // Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
    // Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
		$projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($projectstatic, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';


    // print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
    // if (! empty($projectstatic->societe->id)) print $projectstatic->societe->getNomUrl(1);
    // else print '&nbsp;';
    // print '</td>';
    // print '</tr>';

    // // Visibility
    // print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
    // if ($projectstatic->public) print $langs->trans('SharedProject');
    // else print $langs->trans('PrivateProject');
    // print '</td></tr>';

    // // Statut
    // print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

    // // Date start
    // print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
    // print dol_print_date($projectstatic->date_start,'day');
    // print '</td></tr>';

    // // Date end
    // print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
    // print dol_print_date($projectstatic->date_end,'day');
    // print '</td></tr>';


	print '</table>';

	print '</div>';
}


/*
 * payment
 */
$product = new Product($db);
print '<br>';


// Get list of tasks in tasksarray and taskarrayfiltered
// We need all tasks (even not limited to a user because a task to user
// can have a parent that is not affected to him).
//$tasksarray = $object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
// We load also tasks limited to a particular user
//$tasksrole=($_REQUEST["mode"]=='mine' ? $task->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
//var_dump($tasksarray);
//var_dump($tasksrole);

//verificamos los items que contiene
$modetask = 0;
//$tasksarray=$taskext->getTasksArray(0, 0, $projectstatic->id, $socid, $modetask);
//verificamos si las tareas estan asociadas al contrato o contratos del proyecto
// $lContrattask = false;
// foreach ((array) $tasksarray AS $j => $objtask)
// {
//   if (!$objtask->array_options['options_fk_contrat']>0)
//     $lContrattask = true;
// }

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewPaiement"));
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#socid").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});
		});';
		print '</script>'."\n";
	}

	print '<form id="add" name="add" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

				//sumamos los valores anteriores
	$filterpay = " AND t.fk_projet = ". $id;
	$filterpay.= " AND t.fk_soc = ".GETPOST('socid');
	echo $respay=$objpaiement->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
	$idspay = '';
	$refpay = '';
	if ($respay>0)
	{
		foreach ($objpaiement->lines AS $k => $objpay)
		{
			if (!empty($idspay)) $idspay.= ',';
			$idspay.= $objpay->id;
			//verificamos que numero de pago corresponde
			if ($refpay!= $objpay->ref) $refpay = $objpay->ref;
		}
		//el ref es una sequencia de pagos
		if (empty($refpay)) $refpay = 1;
		else
			$refpay=$refpay*1+1;
	}
	else
		$refpay = 1;
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//

	// Third party
	print '<tr><td class="fieldrequired">'.$langs->trans('Supplier').'</td>';
	print '<td>';
	//recuperamos los proveedores con pedidos y que esten relacionados al proyecto
	$ids = '';
	if ($conf->purchase->enabled)
	{
		$objcommande = new Fournisseurcommandeext($db);
		$filterstatic = " AND c.fk_projet =".$id;
		$res = $objcommande->fetchOrder('','',0,0,array(1=>1),'AND',$filterstatic);
		if ($res > 0)
		{
			foreach ($objcommande->lines AS $j => $line)
			{
				if (!empty($ids)) $ids.=',';
				$ids.= $line->fk_soc;
			}
		}
	}
	if (empty($ids)) $ids = 0;
	$filtertype = 's.client = 1 OR s.client = 3';
	$filtertype = 's.fournisseur = 1';
	$filter = " s.rowid in (".$ids.")";
	print $form->select_company(GETPOST('socid'),'socid', $filter,1,0,0,array(),0,'minwidth100','');
	//print $form->select_company_v(GETPOST('socid'), 'socid', $filtertype, 0, 0, 1, 2, '', 1, array(),0,'','');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans('Planilla').'</td>';
	print '<td>';
	print $refpay;
	print '<input type="hidden" name="ref" value="'.$refpay.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td>';
	print '<td>';
	print $form->select_date(dol_now(),'dp');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td>';
	print '<td>';
	print '<input type="text" name="description" value="'.GETPOST('description','alpha').'" required>';
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	dol_fiche_head();
	//recuperamos las tareas del proyecto
	$tasks = $taskext->getTasksArray(0, 0, $id, 0,0,'',-1, '',0,0);
	$taskid = '0';
	if (count($tasks)> 0)
	{
		foreach ($tasks AS $j => $obj)
		{
			if (!empty($taskid)) $taskid.= ',';
			$taskid.=$obj->id;
		}
	}
	//listamos de la tabla projet_task_resources los no pagados
	$filterstatic = " AND t.fk_object = ".GETPOST('socid');
	$filterstatic.= " AND t.object = 'societe'";
	$filterstatic.= " AND t.fk_projet_task IN (".$taskid.")";

	$res = $objectptr->fetchAll('ASC', 'date_resource', 0, 0, array(1=>1), 'AND',$filterstatic);
	if ($res>0)
	{
		//armamos un resumen de cada linea
		$aData = array();
		$aObject = array();
		$aObjectdet = array();
		$aProduct = array();
		$aTask = array();
		$aIds = array();
		foreach((array) $objectptr->lines AS $l => $lineb)
		{
			$aData[$lineb->fk_projet_task][$lineb->fk_objectdet]+=$lineb->quant;
			$aObjectdet[$lineb->fk_projet_task][$lineb->fk_objectdet]=$lineb->objectdet;
			$aProduct[$lineb->fk_projet_task][$lineb->fk_objectdet]=$lineb->fk_product;
			$aTask[$lineb->fk_projet_task][$lineb->fk_objectdet] = $lineb->fk_projet_task;
			$aIds[$lineb->fk_projet_task][$lineb->fk_objectdet][$lineb->id] = $lineb->id;
		}
		print '<table class="table border centpercent">'."\n";

		print '<thead>';
		print '<tr>';
		print '<th rowspan="2">'.$langs->trans('Task').'</th>';
		print '<th rowspan="2">'.$langs->trans('Description').'</th>';
		print '<th rowspan="2">'.$langs->trans('Unit').'</th>';
		print '<th rowspan="2">'.$langs->trans('PU').'</th>';
		print '<th colspan="2">'.$langs->trans('Anterior').'</th>';
		print '<th colspan="2">'.$langs->trans('Actual').'</th>';
		print '<th colspan="2">'.$langs->trans('Acumulado').'</th>';
		print '<th colspan="2">'.$langs->trans('A pagar').'</th>';
		print '</tr>';

		print '<tr>';
		print '<th>'.$langs->trans('Qty').'</th>';
		print '<th>'.$langs->trans('Amount').'</th>';
		print '<th>'.$langs->trans('Qty').'</th>';
		print '<th>'.$langs->trans('Amount').'</th>';
		print '<th>'.$langs->trans('Qty').'</th>';
		print '<th>'.$langs->trans('Amount').'</th>';
		print '<th>'.$langs->trans('Qty').'</th>';
		print '<th>'.$langs->trans('Amount').'</th>';
		print '</tr>';
		print '</thead>';
		foreach((array) $aData AS $fk_projet_task => $row)
		{
			foreach ($row AS $fk_objectdet => $qty)
			{
				$qty_ant=0;
				$pay_ant=0;
				$nameobject = '';
				$nameobjectdet = $aObjectdet[$fk_projet_task][$fk_objectdet];
				if ($nameobjectdet == 'CommandeFournisseurLigne')
				{
					$element = $lineb->objectdet;
					$element = 'purchase';
					$subelement = $lineb->objectdet.'ext';
					dol_include_once('/'.$element.'/class/'.$subelement.'.class.php');
					$classname = ucfirst($subelement);
					$objectsrc = new $classname($db);
					$objectsrc->fetchline($fk_objectdet);
					$aObject[$objectsrc->fk_commande] = $objectsrc->fk_commande;
					$nameobject = 'Commandefournisseur';
				}
				//sumamos los valores anteriores
				$filterpay = " AND t.fk_projet = ". $id;
				$filterpay.= " AND t.fk_soc = ".GETPOST('socid');
				$filterpay.= " AND t.status = 1";
				$respay=$objpaiement->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
				if ($respay>0)
				{
					$idspay = '';
					foreach ($objpaiement->lines AS $k => $objpay)
					{
						if (!empty($idspay)) $idspay.= ',';
						$idspay.= $objpay->id;
					}
					$filterpay = " AND t.fk_projet_paiement IN (".$idspay.")";
					$filterpay.= " AND t.fk_objectdet = ".$fk_objectdet;
					$filterpay.= " AND t.fk_projet_task = ".$fk_projet_task;
					$filterpay.= " AND t.objectdet = '".trim($nameobjectdet)."'";
					$respayd=$objpaiementdet->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
					if ($respayd>0)
					{
						foreach ($objpaiementdet->lines AS $l => $objpayd)
						{
							$qty_ant += $objpayd->qty;
							$pay_ant += $objpayd->total_ttc;
						}
					}
				}
				//modificamos la sumatoria de la cantidad actual
				if ($qty_ant>0) $qty = $qty - $qty_ant;

				$var = !$var;
				print "<tr $bc[$var]>";
				//buscamos la tarea
				$taskext->fetch($fk_projet_task);
				print '<td align="left" class="none">'.$taskext->getNomUrl(1).'</td>';

				print '<td>';
				$fk_product = $aProduct[$fk_projet_task][$fk_objectdet];
				if ($fk_product>0)
				{
					$product->fetch($fk_product);
					print $product->getNomUrl(1);
					print '<input type="hidden" name="product['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$fk_product.'">';
				}
				print '&nbsp;'.$objectsrc->desc;
				print '<input type="hidden" name="task['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$fk_projet_task.'">';
				print '<input type="hidden" name="nameobjdet['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$nameobjectdet.'">';
				print '<input type="hidden" name="nameobj['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$nameobject.'">';
				print '<input type="hidden" name="fkobj['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->fk_commande.'">';

				print '<input type="hidden" name="detail['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->desc.'">';
				print '</td>';

				print '<td>'.$objectsrc->getLabelOfUnit('short').'</td>';
				print '<input type="hidden" name="unit['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->fk_unit.'">';

				print '<td>'.price($objectsrc->price).'</td>';
				print '<input type="hidden" name="price['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->price.'">';
				print '<input type="hidden" name="subprice['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->subprice.'">';

				print '<td align="right">'.$qty_ant.'</td>';
				print '<input type="hidden" name="qty_ant['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$qty_ant.'">';
				print '<td align="right">'.price(price2num($pay_ant,'MT')).'</td>';
				$tant+=$pay_ant;
				//actual
				print '<td align="right">'.price($qty).'</td>';
				print '<td align="right">'.price(price2num($qty * $objectsrc->price,'MU')).'</td>';
				$tpay+= $qty * $objectsrc->price;
				$totalqty = $qty+$qty_ant;
				print '<td align="right">'.$totalqty.'</td>';
				print '<td align="right">'.price($pay_ant + price2num($qty * $objectsrc->price,'MU')).'</td>';
				$tacum+= $pay_ant + $qty * $objectsrc->price;

				print '<td align="right">';
				print '<input type="number" class="len80" name="qty['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$qty.'">'.'</td>';
				print '<td>'.'<input type="number" class="len80" id="tot_'.$fk_objectdet.'" name="total" value="'.$qty*$objectsrc->price.'" disabled>'.'</td>';

				print '</tr>';
			}
		}
		print '<tr>';
		print '<td colspan="5"></td>';
		print '<td align="right">'.price(price2num($tant,'MT')).'</td>';
		print '<td>'.'</td>';
		print '<td align="right">'.price($tpay).'</td>';
		print '<td>'.'</td>';
		print '<td align="right">'.price($tacum).'</td>';
		print '</tr>';

		print '</table>';
	}


	dol_fiche_end();


	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

if ($action != 'create' && $action != 'edit')
{
//recuperamos los pagos registrados
	$objpaiementtmp = new Projetpaiementext($db);
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet = ".$projectstatic->id;
	$numpay = $objpaiementtmp->fetchAll($sortorder,$sortfield, 0, 0,$filter, 'AND',$filterstatic);

	if ($numpay>0)
	{
		dol_fiche_head();

		$params='';
		$params.= '&amp;id='.$id;

		if ($action == 'seltask')
		{
			$aSelcon = GETPOST('selcon');
			unset($_SESSION['aSelectcont']);
			foreach ((array) $aSelcon AS $j => $value)
				$_SESSION['aSelectcont'][$j]=$j;
			$_SESSION['seltask_post'] = $_POST;
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idc='.$idc,
				$langs->trans("Linktaskstocontrat"),
				$langs->trans("Confirmlinktaskstocontrat",$object->ref),
				"confirm_seltask",
				'',
				0,2);
			if ($ret == 'html') print '<br>';
		}

		print '<table class="noborder centpercent">'."\n";
    // Fields title
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('Company'),$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.date_payment','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Totalht'),$_SERVER['PHP_SELF'],'t.total_ht','',$params,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Totalttc'),$_SERVER['PHP_SELF'],'t.total_ttc','',$params,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Deductions'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Liquido'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Attachments'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'t.status','',$params,' align="right"',$sortfield,$sortorder);
		print '</tr>';
    //armamos los contratos
		$var = true;
		$aArray = array();
		$sumatot = 0;
		$sumaded = 0;
		$sumaliq = 0;
		$lines = $objpaiementtmp->lines;

		foreach ((array) $lines AS $j => $line)
		{
			$objpaiementtmp->id = $line->id;
			$objpaiementtmp->ref = $line->ref;
			$objpaiementtmp->fk_projet = $line->fk_projet;
			$objpaiementtmp->status = $line->status;
			$var = !$var;
			if ($idp != $line->id)
				print "<tr $bc[$var]>";
			else
				print '<tr class="backmark">';
			print '<td>';
			$societe->fetch($line->fk_soc);
			print $societe->getNomUrl(1);
			print '</td>';
			print '<td>';
			print $objpaiementtmp->getNomUrl(1);
		//print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$line->id.'">'.img_picto($langs->trans('View'),DOL_URL_ROOT.'/monprojet/img/payment','',true).' '.$line->ref.'</a>';
			print '</td>';
			print '<td>';
			print dol_print_date($line->date_payment,'day');
			print '</td>';
			print '<td align="right">';
			print price($line->total_ht);
			$sumatotht += $line->total_ht;
			print '</td>';
			print '<td align="right">';
			print price($line->total_ttc);
			$sumatotttc += $line->total_ttc;
			print '</td>';
			print '<td align="right">';
			$sumade = 0;
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_projet_payment =".$line->id;
			$filterstatic.= " AND t.statut = 1";
			$objpayde->fetchAll('', '', 0, 0, $filter, 'AND', $filterstatic);
			foreach ((array) $objpayde->lines AS $k =>$linede)
			{
				$sumade += $linede->amount;
			}
			print price($sumade);
			$sumaded+= $sumade;
			print '</td>';
			print '<td align="right">';
			print price(price2num($line->total_ttc - $sumade,'MT'));
			print '</td>';
			$sumaliq += $line->total_ttc - $sumade;
		//agregamos la lista de adjuntos
			print '<td>';
			if ($line->document)
			{
	    //recuperamos los nombres de archivo
				$aDoc = explode(';',$line->document);
				foreach ((array) $aDoc AS $k => $doc)
				{
					$objpaytemp->fetch($line->id);
					$aFile = explode('.',$doc);
			//extension
					$docext = STRTOUPPER($aFile[count($aFile)-1]);
					$typedoc = 'doc';
					if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
						$typedoc = 'fin';
					if ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
						$typedoc = 'doc';
					elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
						$typedoc = 'doc';

				//print '&nbsp;'.$objpaytemp->showphoto($typedoc,$objpaytemp,$doc,$object,$projectstatic, 100,$docext,1);
					$imagesize = '';
					$cache=0;
					print '&nbsp;'.$objpaytemp->showphotos($typedoc,$doc,$objpaytemp,'monprojet', $object, $projectstatic,$width=100, $height=0, $caneditfield=0, $cssclass='photowithmargin', $imagesize, 1, $cache,$docext);

				}
			}
			print '</td>';
	//fin lista adjuntos

			print '<td align="right">';
			print $objpaiementtmp->getLibStatut(1);
			print '</td>';
			print '</tr>';
		}
    //totales
		print '<tr class="liste_total" align="right">';
		print '<td colspan="3">'.$langs->trans('Total').'</td>';
		print '<td>'.price(price2num($sumatotht,'MT')).'</td>';
		print '<td>'.price(price2num($sumatotttc,'MT')).'</td>';
		print '<td>'.price(price2num($sumaded,'MT')).'</td>';
		print '<td>'.price(price2num($sumaliq,'MT')).'</td>';
		print '</tr>';
		print '</table>';
		dol_fiche_end();
		/* ******************************* */
		/*                                 */
		/* Barre d'action                  */
		/*                                 */
		/* ******************************* */

		print "<div class=\"tabsAction\">\n";
		if ($user->rights->monprojet->paip->rep)
			print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/payexcel.php'.'?id='.$id.'">'.$langs->trans("Payroladvance").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Payroladvance")."</a>";
		print '</div>';
	}
	else
	{
		print $langs->trans("Nopayment");
	}
}

if ($idp)
{
	if ($action == 'edit' || $action == 'confupdate')
	{
		print load_fiche_titre($langs->trans("Paiement"));
		if (! empty($conf->use_javascript_ajax))
		{
			print "\n".'<script type="text/javascript">';
			print '$(document).ready(function () {
				$("#socid").change(function() {
					document.add.action.value="create";
					document.add.submit();
				});
			});';
			print '</script>'."\n";
		}

		if ($action == 'confupdate')
		{
			$aRow = $_POST;
			$aPost[$id] = $aRow;
			$_SESSION['aPost'] = serialize($aPost);
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id.'&idp='.$idp, $langs->trans('Approvepaiement'), $langs->trans('Confirmapprovepaiement'), 'confirm_update', '', 0, 1);
			print $formconfirm;
		}


		print '<form id="add" name="add" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idp" value="'.$objpaiement->id.'">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

				//sumamos los valores anteriores
		$filterpay = " AND t.fk_projet = ". $id;
		$filterpay.= " AND t.fk_soc = ".(GETPOST('socid')?GETPOST('socid'):$objpaiement->fk_soc);
		$respay=$objpaiement->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
		$idspay = '';
		$refpay = '';
		if ($respay>0)
		{
			foreach ($objpaiement->lines AS $k => $objpay)
			{
				if (!empty($idspay)) $idspay.= ',';
				$idspay.= $objpay->id;
			//verificamos que numero de pago corresponde
				if ($refpay!= $objpay->ref) $refpay = $objpay->ref;
			}
		//el ref es una sequencia de pagos
			if (empty($refpay)) $refpay = 1;
			else
				$refpay=$refpay*1+1;
		}
		else
			$refpay = 1;
		dol_fiche_head();

		print '<table class="border centpercent">'."\n";
	// Third party
		print '<tr><td class="fieldrequired">'.$langs->trans('Supplier').'</td>';
		print '<td>';

		$ids = '';
		if ($conf->purchase->enabled)
		{
			$objcommande = new Fournisseurcommandeext($db);
			$filterstatic = " AND c.fk_projet =".$id;
			$res = $objcommande->fetchOrder('','',0,0,array(1=>1),'AND',$filterstatic);
			if ($res > 0)
			{
				foreach ($objcommande->lines AS $j => $line)
				{
					if (!empty($ids)) $ids.=',';
					$ids.= $line->fk_soc;
				}
			}
		}
		if (empty($ids)) $ids = 0;
		$filtertype = 's.client = 1 OR s.client = 3';
		$filtertype = 's.fournisseur = 1';
		$filter = " s.rowid in (".$ids.")";
		print $form->select_company((GETPOST('socid')?GETPOST('socid'):$objpaiement->fk_soc),'socid', $filter,1,0,0,array(),0,'minwidth100','');

		//$filtertype = 's.client = 1 OR s.client = 3';
		//$filtertype = 's.fournisseur = 1';
		//print $form->select_company_v((GETPOST('socid')?GETPOST('socid'):$objpaiement->fk_soc), 'socid', $filtertype, 0, 0, 1, 2, '', 1, array(),0,'','');
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Planilla').'</td>';
		print '<td>';
		print $objpaiement->ref;
		print '<input type="hidden" name="ref" value="'.$refpay.'">';
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td>';
		print '<td>';
		print $form->select_date($objpaiement->date_payment,'dp');
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td>';
		print '<td>';
		print '<input type="text" name="description" value="'.(GETPOST('description','alpha')?GETPOST('description','alpha'):$objpaiement->detail).'">';
		print '</td></tr>';

		print '</table>'."\n";

		dol_fiche_end();

		dol_fiche_head();
	//recuperamos las tareas del proyecto

		$tasks = $taskext->getTasksArray(0, 0, $id, 0,0,'',-1, '',0,0);
		$taskid = '0';
		if (count($tasks)> 0)
		{
			foreach ($tasks AS $j => $obj)
			{
				if (!empty($taskid)) $taskid.= ',';
				$taskid.=$obj->id;
			}
		}
	//listamos de la tabla projet_task_resources los no pagados
		$filterstatic = " AND t.fk_object = ".(GETPOST('socid')?GETPOST('socid'):$objpaiement->fk_soc);
		$filterstatic.= " AND t.object = 'societe'";
		$filterstatic.= " AND t.fk_projet_task IN (".$taskid.")";

		$res = $objectptr->fetchAll('ASC', 'date_resource', 0, 0, array(1=>1), 'AND',$filterstatic);
		if ($res>0)
		{
			//armamos un resumen de cada linea
			$aData = array();
			$aObject = array();
			$aObjectdet = array();
			$aProduct = array();
			$aTask = array();
			$aIds = array();
			foreach((array) $objectptr->lines AS $l => $lineb)
			{
				$aData[$lineb->fk_projet_task][$lineb->fk_objectdet]+=$lineb->quant;
				$aObject[$lineb->fk_projet_task][$lineb->fk_objectdet]=$lineb->object;
				$aObjectdet[$lineb->fk_projet_task][$lineb->fk_objectdet]=$lineb->objectdet;
				$aProduct[$lineb->fk_projet_task][$lineb->fk_objectdet]=$lineb->fk_product;
				$aTask[$lineb->fk_projet_task][$lineb->fk_objectdet] = $lineb->fk_projet_task;
				$aIds[$lineb->fk_projet_task][$lineb->fk_objectdet][$lineb->id] = $lineb->id;
			}
			print '<table class="table border centpercent">'."\n";

			print '<thead>';
			print '<tr>';
			print '<th rowspan="2">'.$langs->trans('Task').'</th>';
			print '<th rowspan="2">'.$langs->trans('Description').'</th>';
			print '<th rowspan="2">'.$langs->trans('Unit').'</th>';
			print '<th rowspan="2">'.$langs->trans('PU').'</th>';
			print '<th colspan="2">'.$langs->trans('Anterior').'</th>';
			print '<th colspan="2">'.$langs->trans('Actual').'</th>';
			print '<th colspan="2">'.$langs->trans('Acumulado').'</th>';
			print '<th colspan="2">'.$langs->trans('A pagar').'</th>';
			print '</tr>';

			print '<tr>';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			print '</tr>';
			print '</thead>';


			foreach((array) $aData AS $fk_projet_task => $row)
			{
				foreach ($row AS $fk_objectdet => $qty)
				{
					//buscamos que se tiene registrado en la tabla paiementdet
					$filterstatic = " AND t.fk_projet_paiement = ".$idp;
					$filterstatic.= " AND t.fk_objectdet = ".$fk_objectdet;
					$filterstatic.= " AND t.fk_projet_task = ".$fk_projet_task;
					$filterstatic.= " AND t.objectdet = 'CommandeFournisseurLigne'";
					$resdet = $objpaiementdet->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
					$qty_ant=0;
					$pay_ant=0;
					$nameobject = '';
					$nameobjectdet = $aObjectdet[$fk_projet_task][$fk_objectdet];
					if ($nameobjectdet == 'CommandeFournisseurLigne')
					{
						$element = $lineb->objectdet;
						$element = 'purchase';
						$subelement = $lineb->objectdet.'ext';
						dol_include_once('/'.$element.'/class/'.$subelement.'.class.php');
						$classname = ucfirst($subelement);
						$objectsrc = new $classname($db);
						$objectsrc->fetchline($fk_objectdet);
						$aObject[$objectsrc->fk_commande] = $objectsrc->fk_commande;
						$nameobject = 'Commandefournisseur';
					}
					//sumamos los valores anteriores

					$filterpay = " AND t.fk_projet = ". $id;
					$filterpay.= " AND t.fk_soc = ".(GETPOST('socid')?GETPOST('socid'):$objpaiement->fk_soc);
					$filterpay.= " AND t.status = 1";
					$respay=$objpaiement->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
					if ($respay>0)
					{
						$idspay = '';
						foreach ($objpaiement->lines AS $k => $objpay)
						{
							if (!empty($idspay)) $idspay.= ',';
							$idspay.= $objpay->id;
						}

						$filterpay = " AND t.fk_projet_paiement IN (".$idspay.")";
						$filterpay.= " AND t.fk_projet_task = ".$fk_projet_task;
						$filterpay.= " AND t.fk_objectdet = ".$fk_objectdet;
						$filterpay.= " AND t.objectdet = '".$nameobjectdet."'";
						$respayd=$objpaiementdettmp->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
						if ($respayd>0)
						{
							foreach ($objpaiementdettmp->lines AS $l => $objpayd)
							{
								$qty_ant += $objpayd->qty;
								$pay_ant += $objpayd->total_ttc;
							}
						}
					}
					if ($qty_ant > 0) $qty = $qty - $qty_ant;

					$var = !$var;
					print "<tr $bc[$var]>";
					//buscamos la tarea
					$taskext->fetch($fk_projet_task);
					print '<td align="left" class="none">'.$taskext->getNomUrl(1).'</td>';

					print '<td>';

					$fk_product = $aProduct[$fk_projet_task][$fk_objectdet];
					if ($fk_product>0)
					{
						$product->fetch($fk_product);
						print $product->getNomUrl(1);
						print '<input type="hidden" name="product['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$fk_product.'">';
					}
					print '&nbsp;'.$objpaiementdet->detail;
					print '<input type="hidden" name="task['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$fk_projet_task.'">';
					print '<input type="hidden" name="nameobjdet['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$nameobjectdet.'">';
					print '<input type="hidden" name="nameobj['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$nameobject.'">';
					print '<input type="hidden" name="fkobj['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->fk_commande.'">';
					print '<input type="hidden" name="detail['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->desc.'">';
					print '</td>';

					//if ($fk_product>0)
					//{
					//	print '<td>'.$product->getLabelOfUnit().'</td>';
					//}
					//else
					print '<td>'.$objpaiementdet->getLabelOfUnit('short').'</td>';
					print '<input type="hidden" name="unit['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->fk_unit.'">';

					print '<td align="right">'.price(price2num($objectsrc->price,'MT')).'</td>';
					print '<input type="hidden" name="price['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->price.'">';
					print '<input type="hidden" name="subprice['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$objectsrc->subprice.'">';

					print '<td align="right">'.$qty_ant.'</td>';
					print '<input type="hidden" name="qtyant['.$fk_projet_task.']['.$fk_objectdet.']" value="'.$qty_ant.'">';

					print '<td align="right">'.price(price2num($pay_ant,'MT')).'</td>';
					$tant+=$pay_ant;
					print '<td align="right">'.price($qty).'</td>';
					print '<td align="right">'.price(price2num($qty * $objectsrc->price,'MT')).'</td>';
					$tpay+= $qty * $objectsrc->price;
					$totalqty = $qty+$qty_ant;
					print '<td align="right">'.$totalqty.'</td>';
					print '<td align="right">'.price(price2num($pay_ant + ($qty * $objectsrc->price),'MT')).'</td>';
					$tacum+= $pay_ant + $qty * $objectsrc->price;

					print '<td align="right">';
					print '<input type="number" min="0" step="any" class="len80" name="qty['.$fk_projet_task.']['.$fk_objectdet.']" value="'.($resdet>0?$objpaiementdet->qty:'').'">'.'</td>';
					print '<td>'.'<input type="number"  min="0.0000001" step="any" class="len80" id="tot_'.$fk_objectdet.'" name="total" value="'.($resdet>0?$objpaiementdet->qty:0)*$objectsrc->price.'" disabled>'.'</td>';

					print '</tr>';
				}
			}
			print '<tr>';
			print '<td colspan="5"></td>';
			print '<td align="right">'.price(price2num($tant,'MT')).'</td>';
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($tpay,'MT')).'</td>';
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($tacum,'MT')).'</td>';
			print '</tr>';

			print '</table>';


		}

		dol_fiche_end();

		print '<div class="center">';
		print '<input type="submit" class="butAction" name="add" value="'.$langs->trans("Save").'">';
		if ($user->rights->monprojet->paip->paiapp && $objpaiement->status == 0)
			print '&nbsp;<input type="submit" class="butAction" name="addapp" value="'.$langs->trans("Saveandapprove").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</div>';

		print '</form>';
	}

	if ($action != 'edit')
	{
		print load_fiche_titre($langs->trans("Paiement"));
		//sumamos los valores anteriores
		$objpaiementtmp = new Projetpaiementext($db);
		$filterpay = " AND t.fk_projet = ". $id;
		$filterpay.= " AND t.fk_soc = ".GETPOST('socid');
		$respay=$objpaiementtmp->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
		$idspay = '';
		$refpay = '';
		if ($respay>0)
		{
			foreach ($objpaiementtmp->lines AS $k => $objpay)
			{
				if (!empty($idspay)) $idspay.= ',';
				$idspay.= $objpay->id;
				//verificamos que numero de pago corresponde
				if ($refpay!= $objpay->ref) $refpay = $objpay->ref;
			}
		//el ref es una sequencia de pagos
			if (empty($refpay)) $refpay = 1;
			else
				$refpay=$refpay*1+1;
		}
		else
			$refpay = 1;
		dol_fiche_head();

		if ($action == 'delete')
		{
			unset($_SESSION['aPost']);
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id.'&idp='.$idp, $langs->trans('Deletepaiement'), $langs->trans('ConfirmDeletepaiement'), 'confirm_delete', '', 0, 1);
			print $formconfirm;
		}


		print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//

	// Third party
		print '<tr><td class="fieldrequired">'.$langs->trans('Supplier').'</td>';
		print '<td>';

		$societe->fetch($objpaiement->fk_soc);
		print $societe->getNomUrl(1);
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Planilla').'</td>';
		print '<td>';
		print $objpaiement->ref;
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td>';
		print '<td>';
		print dol_print_date($objpaiement->date_payment,'day');
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td>';
		print '<td>';
		print $objpaiement->detail;
		print '</td></tr>';

		print '</table>'."\n";

		dol_fiche_end();

		dol_fiche_head();

		$aReportpaimentenc[]=array('supplier'=>$societe->nom,'planilla'=>$objpaiement->ref,'date'=>$objpaiement->date_payment,'detail'=>$objpaiement->detail,'type'=>"E");
	//recuperamos las tareas del proyecto

		$tasks = $taskext->getTasksArray(0, 0, $id, 0,0,'',-1, '',0,0);
		$taskid = '0';
		if (count($tasks)> 0)
		{
			foreach ($tasks AS $j => $obj)
			{
				if (!empty($taskid)) $taskid.= ',';
				$taskid.=$obj->id;
			}
		}
		//listamos de la tabla projet_task_resources los no pagados
		//buscamos que se tiene registrado en la tabla paiementdet
		$filterstatic = " AND t.fk_projet_paiement = ".$idp;
		//$filterstatic.= " AND t.object = 'CommandeFournisseurLigne'";
		$res = $objpaiementdet->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
		//vamos a determinar que tipo de compra tiene
		$aTypecode = array();
		if ($res>0)
		{
			$lines = $objpaiementdet->lines;
			print '<table class="table border centpercent">'."\n";

			print '<thead>';
			print '<tr class="liste_titre">';
			print '<th rowspan="2">'.$langs->trans('Task').'</th>';
			print '<th rowspan="2">'.$langs->trans('Description').'</th>';
			print '<th rowspan="2">'.$langs->trans('Unit').'</th>';
			print '<th rowspan="2">'.$langs->trans('PU').'</th>';
			print '<th colspan="2">'.$langs->trans('Anterior').'</th>';
			print '<th colspan="2">'.$langs->trans('Actual').'</th>';
			print '<th colspan="2">'.$langs->trans('Acumulado').'</th>';
			//print '<th colspan="2">'.$langs->trans('A pagar').'</th>';
			print '</tr>';

			print '<tr class="liste_titre">';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			print '<th>'.$langs->trans('Qty').'</th>';
			print '<th>'.$langs->trans('Amount').'</th>';
			//print '<th>'.$langs->trans('Qty').'</th>';
			//print '<th>'.$langs->trans('Amount').'</th>';
			print '</tr>';
			print '</thead>';


			foreach((array) $lines AS $j => $obj)
			{

				$objpaiementdet->fk_unit = $obj->fk_unit;
				$objpaiementdet->ref = $obj->ref;

				$fk_projet_task = $obj->fk_projet_task;
				$fk_objectdet = $obj->fk_objectdet;
				$nameobjectdet = $obj->objectdet;
				$nameobject = $obj->object;
				$fk_object = $obj->fk_object;
				if ($nameobject == 'Commandefournisseur')
				{
					$element = $obj->object;
					$element = 'purchase';
					$subelement = $obj->object.'ext';
					$subelement = 'Commandefournisseuradd';
					$subelementdoc = 'commandefournisseuradd';
					dol_include_once('/'.$element.'/class/'.$subelementdoc.'.class.php');
					$classname = ucfirst($subelement);
					$objectsrc = new $classname($db);
					$objectsrc->fetch(0,$fk_object);
					$aTypecode[$fk_object] = $objectsrc->code_facture;
				}

				$qty_ant=0;
				$pay_ant=0;
				if ($nameobjectdet == 'CommandeFournisseurLigne')
				{
					$element = $obj->object;
					$element = 'purchase';
					$subelement = $obj->objectdet.'ext';
					$subelementdoc = 'fournisseurcommandeext';
					dol_include_once('/'.$element.'/class/'.$subelementdoc.'.class.php');
					$classname = ucfirst($subelement);
					$objectsrc = new $classname($db);
					$objectsrc->fetchline($fk_objectdet);
				}
				if ($optioncalc)
				{
					//sumamos valores anteriores
					//sumamos los valores anteriores
					$objpaiementtmp = new Projetpaiementext($db);
					$filterpay = " AND t.fk_projet = ". $id;
					$filterpay.= " AND t.fk_soc = ".$objpaiement->fk_soc;
					$filterpay.= " AND t.ref < ".$objpaiement->ref;
					$respay=$objpaiementtmp->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
					if ($respay>0)
					{
						$idspay = '';
						foreach ($objpaiementtmp->lines AS $k => $objpay)
						{
							if (!empty($idspay)) $idspay.= ',';
							$idspay.= $objpay->id;
						}
						$filterpay = " AND t.fk_projet_paiement IN (".$idspay.")";
						$filterpay.= " AND t.fk_object = ".$fk_objectdet;
						$filterpay.= " AND t.object = ".$nameobjectdet;
						$respayd=$objpaiementdet->fetchAll('','',0,0,array(1=>1),'AND',$filterpay);
						if ($respayd>0)
						{
							foreach ($objpaiementdet->lines AS $l => $objpayd)
							{
								$qty_ant += $objpayd->qty;
								$pay_ant += $objpayd->ttotal_ttc;
							}
						}
					}
				}

				$var = !$var;
				print "<tr $bc[$var]>";
				//buscamos la tarea
				$taskext->fetch($fk_projet_task);
				print '<td align="left" class="none">'.$taskext->getNomUrl(1).'</td>';

				print '<td>';
				$fk_product = $obj->fk_product;
				if ($fk_product>0)
				{
					$product->fetch($fk_product);
					print $product->getNomUrl(1);
				}
				print '&nbsp;'.$obj->detail;
				print '</td>';

				print '<td>'.$objpaiementdet->getLabelOfUnit('short').'</td>';

				print '<td align="right">'.price(price2num($obj->price,'MT')).'</td>';

				if ($optioncalc)
				{
					print '<td align="right">'.$qty_ant.'</td>';
					print '<td align="right">'.price($pay_ant).'</td>';
					$qty_antaux=$qty_ant;
					$pay_antaux=$pay_ant;
				}
				else
				{
					print '<td align="right">'.$obj->qty_ant.'</td>';
					$qty_ant = $obj->qty_ant;
					$pay_ant = $qty_ant * $obj->price;
					print '<td align="right">'.price($pay_ant).'</td>';
					$qty_antaux=$obj->qty_ant;
					$pay_antaux=$pay_ant;

				}
				$tant+=$pay_ant;
				print '<td align="right">'.price($obj->qty).'</td>';


				print '<td align="right">'.price(price2num($obj->qty * $obj->price,'MT')).'</td>';
				$tpay+= $obj->qty * $obj->price;
				$totalqty = $obj->qty+$qty_ant;
				print '<td align="right">'.$totalqty.'</td>';
				print '<td align="right">'.price(price2num($pay_ant + ($obj->qty * $obj->price),'MT')).'</td>';
				$tacum+= $pay_ant + $obj->qty * $obj->price;

				//print '<td align="right">';
				//print '<input type="number" class="len80" name="qty['.$fk_objectdet.']" value="'.$qty.'">'.'</td>';
				//print '<td>'.'<input type="number" class="len80" id="tot_'.$fk_objectdet.'" name="total" value="'.$qty*$objectsrc->price.'" disabled>'.'</td>';

				$aReportpaiement[]=array('tarea'=>$taskext->ref,'detalle'=>$product->ref,'unidad'=>$objpaiementdet->getLabelOfUnit('short'),'pu'=>$obj->price,'antcant'=>$qty_antaux,'antimport'=>$pay_antaux,'actcant'=>$obj->qty,'actimport'=>$obj->qty * $obj->price,'acumcant'=>$totalqty,'acumimport'=>$pay_ant + ($obj->qty * $obj->price),'type'=>"D");



				print '</tr>';

			}
			print '<tr>';
			print '<td colspan="5"></td>';
			print '<td align="right">'.price(price2num($tant,'MT')).'</td>';
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($tpay,'MT')).'</td>';
			print '<td>'.'</td>';
			print '<td align="right">'.price(price2num($tacum,'MT')).'</td>';
			print '</tr>';

			print '</table>';

			$_SESSION['aReportpaiementdet'] = serialize($aReportpaiement);
			$_SESSION['aReportpaiementencdet'] = serialize($aReportpaimentenc);


			print "<div class=\"tabsAction\">\n";
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_group='.$fk_group.'&confint='.$confint.'&level='.$level.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';
			print '</div>';


		}
		dol_fiche_end();

		/* ******************************* */
		/*                                 */
		/* Barre d'action                  */
		/*                                 */
		/* ******************************* */

		print "<div class=\"tabsAction\">\n";
		if ($user->rights->monprojet->paip->fac && $objpaiement->status == 1)
		{
			//recorremos que tipos de documentos se generara
			if ($conf->fiscal->enabled)
			{
				require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypefacture.class.php';
				$typefact = new Ctypefacture($db);
				foreach($aTypecode AS $fk => $code)
				{
					$typefact->fetch(0,$code);
					print '&nbsp;<a class="butAction" href="'.DOL_URL_ROOT.'/purchase/facture/card.php?action=create&origin=projetpaiement&originid='.$idp.'&fk_commande='.$fk.'">'.$langs->trans($typefact->label).'</a>';

				}
			}
			else
				print '<a class="butAction" href="'.DOL_URL_ROOT.'/purchase/facture/card.php?action=create&origin=projetpaiement&originid='.$idp.'">'.$langs->trans("Createfacture").'</a>';

		}
		if ($user->rights->monprojet->paip->mod && $objpaiement->status == 0)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$objpaiement->id.'&action=edit">'.$langs->trans("Modify").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		if ($user->rights->monprojet->paip->del && $objpaiement->status == 0)
			print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$objpaiement->id.'&action=delete">'.$langs->trans("Delete").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		print '</div>';
	}


}



/* ******************************* */
/*                                 */
/* Barre d'action                  */
/*                                 */
/* ******************************* */

print "<div class=\"tabsAction\">\n";
if (empty($action) && empty($idp))
{
	if ($user->rights->monprojet->paip->pay && $projectstatic->statut == 1)
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create&id='.$id.'">'.$langs->trans("Createnew").'</a>';
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
}
print "</div>";


llxFooter();

$db->close();
