<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
* Copyright (C) 2018      Ramiro Queso	<ramiroques@gmail.com>
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
*   	\file       budget/items_list.php
*		\ingroup    budget
*		\brief      This file is an example of a php page
*					Initialy built by build_class_from_table on 2018-04-17 16:51
*/

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
//dol_include_once('/budget/class/items.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsdet.class.php');
dol_include_once('/budget/class/itemsproductext.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');
dol_include_once('/budget/class/itemsregion.class.php');
dol_include_once('/budget/class/itemsgroup.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/budget/class/parametercalculation.class.php');
dol_include_once('/budget/class/puvariables.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/budget/lib/utils.lib.php');

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


// Load traductions files requiredby by page
$langs->load("budget@budget");
$langs->load("other");

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$type_structure = GETPOST('type_structure','alpha');
$fk_region = GETPOST('fk_region','int');
$fk_sector = GETPOST('fk_sector','int');

if (isset($_POST['fk_region'])) $_SESSION['selitem']['fk_region'] = GETPOST('fk_region','int');
if (isset($_POST['fk_sector'])) $_SESSION['selitem']['fk_sector'] = GETPOST('fk_sector','int');

$search_fk_region=$_SESSION['selitem']['fk_region'];
$search_fk_sector=$_SESSION['selitem']['fk_sector'];



// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

if (!$user->rights->budget->ite->exp) accessforbidden();

// Load object if id or ref is provided as parameter
$object=new Itemsext($db);
$objectdet=new Itemsdet($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objItemsgroup = new Itemsgroup($db);
$objItemsregion = new Itemsregion($db);
$objUser = new User($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);
$objCtypeitem=new Ctypeitemext($db);
$objItemsproduct = new Itemsproductext($db);
$objItemsproductregion = new Itemsproductregion($db);
$objPutypestructure = new Putypestructureext($db);
$objParametercalculation = new Parametercalculation($db);
$objPustructure = new Pustructureext($db);
$objPustructuredet = new Pustructuredetext($db);
$objPuvariables = new Puvariables($db);
$objItemsproduction = new Itemsproduction($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	if ($action == 'add')
	{
		if ($type_structure)
		{
			//recuperamos la estructura
			$filter = " AND t.type_structure = '".$type_structure."'";
			$res = $objPustructure->fetchAll('','',0,0,array(),'AND',$filter);
			if ($res>0)
			{
				//vamos a recuperar que formulas tiene
				$lines = $objPustructure->lines;
				foreach ($lines AS $j => $line)
				{
					$filterdet = " AND t.ref_structure ='".$line->ref."'";
					$filterdet.= " AND t.type_structure = '".$type_structure."'";
					$res = $objPustructuredet->fetchAll('','',0,0,array(),'AND',$filterdet);
					if ($res>0)
					{
						$linesdet = $objPustructuredet->lines;
						foreach ($linesdet AS $k => $linedet)
						{
							$aStructure[$line->ref][$linedet->formula]=$linedet->formula;
						}
					}
				}
			}
		}
		//recuperamos la lista de Puvariables
		$filter =" AND t.status = 1";
		$res = $objPuvariables->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
		if ($res >0)
		{
			$lines =$objPuvariables->lines;
			foreach ($lines AS $j =>$line)
			{
				$objTmp = new PuvariablesLine($db);
				$objTmp->fk_unit =$line->fk_unit;
				$aPuvariables[$line->id]['ref'] =$line->ref;
				$aPuvariables[$line->id]['label'] =$line->label;
				$aPuvariables[$line->id]['unit'] =$objTmp->getLabelOfUnit('short');
			}
		}
		//recuperamos la lista de items group
		$filter = " AND t.version = 1";
		$resItemsgroup = $objItemsgroup->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);

		$aParameter=array();
		$aProduction=array();
		//vamos a mostrar los parametros de calculo
		$filter = " AND t.type != ' ' ";
		$res = $objParametercalculation->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$lines = $objParametercalculation->lines;
			foreach ($lines AS $j => $line)
			{
				$aParameter[$line->type]['ref'] = $line->ref;
				$aParameter[$line->type]['label'] = $line->label;
				$aParameter[$line->type]['amount'] = GETPOST($line->type);
			}
		}

		$objCregiongeographic->fetch($fk_region);
		$cTitleregion = $objCregiongeographic->ref;
		$cTitleproject= $langs->trans('BASE DE PRECIOS UNITARIOS ABC');

		$lFormula = true;
		$object = new Itemsext($db);
		//vamos a recorrer todos los items
		//vamos a recuperar que region del item esta validado
		$sql = " SELECT fk_item ";
		$sql.= " FROM ".MAIN_DB_PREFIX."items_region ";
		$sql.= " WHERE fk_region = ".$fk_region;
		$sql.= " AND fk_sector = ".$fk_sector;
		$sql.= " AND status = 1 ";
		$sql.= " AND active = 1 ";
		$filter = ' AND t.type=0';
		$filter.= " AND t.rowid IN (".$sql.")";

		//$filter = " AND t.rowid IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19)";
		//$filter = " AND t.rowid IN (1)";
		$aResources = array();
		$aItemproduct=array();
		$aItem = array();
		$res = $object->fetchAll('ASC','ref',0,0,array(),'AND',$filter);

		if ($res > 0)
		{
			$lines = $object->lines;
			foreach ($lines AS $j => $line)
			{
				//valos al procedimiento de calculo
				$res = $objItemsproduct->procedure_calc($line->id,$type_structure, $fk_region,$fk_sector,true,$aResources,$aItemproduct);
				//if ($res>0)
				//{
				$aReport[$line->id] = $objItemsproduct->aSpread;
				$aReportf[$line->id] = $objItemsproduct->aSpreadf;
				$aReportform[$line->id] = $objItemsproduct->aFormuladet;
				$aReportformula[$line->id] = $objItemsproduct->aSpreadformula;
				$aReportformulapartial[$line->id] = $objItemsproduct->aSpreadformulapartial;
				$aReportCel[$line->id] = $objItemsproduct->aCel;
				$aResources = $objItemsproduct->aResources;
				$aItemproduct = $objItemsproduct->aItemproduct;
				$aSpreadresources[$line->id]=$objItemsproduct->aSpreadresources;
				$aSpreadparameter[$line->id]=$objItemsproduct->aSpreadparameter;

				//vamos a crear un array de Item
				$resir = $objItemsregion->fetch(0,$line->id,$fk_region,$fk_sector);
				if ($resir == 1)
					$aItem[$line->id]['hour_production'] =$objItemspregion->hour_production;
				//vamos a buscar en tabla producción
				$filteritem =" AND t.fk_item = ".$line->id;
				$filteritem.=" AND t.fk_region = ".$fk_region;
				$filteritem.=" AND t.fk_sector = ".$fk_sector;
				$filteritem.=" AND t.active = 1";

				$resip = $objItemsproduction->fetchAll('','',0,0,array(),'AND',$filteritem);
				if ($resip>0)
				{
					$linesip =$objItemsproduction->lines;
					foreach ($linesip AS $k =>$lineip)
					{
						$aProduction[$line->id][$lineip->fk_items_product][$lineip->fk_variable]['ref'] =$aPuvariables[$lineip->fk_variable]['ref'];
						$aProduction[$line->id][$lineip->fk_items_product][$lineip->fk_variable]['label'] =$aPuvariables[$lineip->fk_variable]['label'];
						$aProduction[$line->id][$lineip->fk_items_product][$lineip->fk_variable]['formula'] =$aItemproduct[$lineip->fk_items_product]->formula;
						$aProduction[$line->id][$lineip->fk_items_product][$lineip->fk_variable]['quantity'] =$lineip->quantity;
						$resipr = $objItemsproductregion->fetch(0, $lineip->fk_items_product,$fk_region,$fk_sector);
						if ($resipr==1)
						{
							$aProduction[$line->id][$lineip->fk_items_product]['hour_production'] =$objItemsproductregion->hour_production;
							$aProduction[$line->id][$lineip->fk_items_product]['units'] =$objItemsproductregion->units;
							$aProduction[$line->id][$lineip->fk_items_product]['performance'] =$objItemsproductregion->performance;
							$aProduction[$line->id][$lineip->fk_items_product]['price_productive'] =$objItemsproductregion->price_productive;
						}
					}
				}

				//}
			}
			//echo '<pre>';
			//print_r($aReport);
			//echo 'partial<hr>';
			//print_r($aReportformulapartial);
			//echo 'cel<hr>';
			//print_r($aReportCel);
			//print_r($aResources);
			//print_r($aSpreadresources);
			//echo '</pre>';
			//print_r($aProduction);
			//print_r($aItemproduct);
			//exit;
			$dir = $conf->budget->dir_output.'/tmp';
			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$error++;
					setEventmessages($langs->trans('Errorcreatingthedirectory'),null,'errors');
				}
			}
			//formatos
			$styleDefault = array(
				'font'  => array(
					'size'  => 9,
					'name'  => 'Verdana'
				));
			$style = array(
				'font'  => array(
					'color' => array('rgb' => '000000'),
					'size'  => 9,
					'name'  => 'Verdana'
				));
			$estilo = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				)
			);
			$styleTitle = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 10,
					'name'  => 'Verdana'
				)
			);
			$styleTitleline = array(
				'font' => array(
					'bold' => true,
					'size' => 9,
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
						'argb' => '009846'
					),
					'endcolor'   => array(
						'argb' => '009846'
					)
				)
			);
			$stylehead = array('font' => array( 'bold'  => true,'color' => array('rgb' => 'ff0000'),'size'  => 13,'name'  => 'Arial'));

			$styleDatag = array(
				'font'    => array(
					'bold'      => true
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
						'argb' => 'Fcf3cf'
					),
					'endcolor'   => array(
						'argb' => 'Fcf3cf'
					)
				)
			);
			$styleTotal = array(
				'font'    => array(
					'bold'      => true
				),
			);
			$styleTotalpu = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				),
				'font'    => array(
					'bold'      => true
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
						'argb' => 'Fcf3cf'
					),
					'endcolor'   => array(
						'argb' => 'Fcf3cf'
					)
				)
			);
			$aColumn=array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G');
			$aColumnprod=array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z');
			//array para capturar las posiciones
			$aResourcelin=array();
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("creater");
			$objPHPExcel->getProperties()->setLastModifiedBy("Middle field");
			$objPHPExcel->getProperties()->setSubject("Subject");
			$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true)
			->setName('Verdana')
			->setSize(10);
			$objWorkSheet = $objPHPExcel->createSheet();
			$work_sheet_count=7;
			//number of sheets you want to create
			$hoja=0;
			$col=1;
			$row=1;
			$i=1;


			//vamos a definir ciertos valores
			$aOrderresource = array(4=>'PU',3=>'MQ',2=>'MO',1=>'MA',0=>'DP');
			$aTitle = array(4=>$langs->trans('AnalisisPrecioUnitario'),0=>$langs->trans('Proyectdata'),1=>$langs->trans('Materials'),2=>$langs->trans('Workforce'),3=>$langs->trans('Machineryandequipment'));
			//vamos a crear la hoja 0
			$col=1;
			$row=1;
			$i=1;
			$lin=1;
			$hoja_count = count($aOrderresource);

			while($hoja<=$work_sheet_count)
			{
				$cTitle=html_entity_decode($langs->trans($aTitle[$hoja]));
				$cResource = $aOrderresource[$hoja];
				if($hoja==0)
				{
					//$objWorkSheet->setTitle($aTitle[$hoja]);


					$lin=1;
					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($aTitle[$hoja]));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':B'.$lin)->applyFromArray($stylehead);
					$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
					$sheet->mergeCells('A'.$lin.':B'.$lin);
					$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

					$lin++;
					$lin++;
					$linini=$lin;
					// titulos
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Label")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Value")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':B'.$lin)->applyFromArray($styleTitleline);
					$lin++;
					//recorremos
					foreach($aParameter AS $j => $data)
					{
						$aResourcelin[$j]['amount']= "'".$aTitle[$hoja]."'"."!B".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($data['label']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$data['amount']);
						$lin++;
					}
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('B')->setAutoSize(true);

					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':B'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.($linini+1).':B'.$linfin)->applyFromArray($style);

				}
				if($hoja==1){
					//$objWorkSheet->setTitle("Worksheet$hoja");


					$lin=1;
					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray($stylehead);
					$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
					$sheet->mergeCells('A'.$lin.':D'.$lin);
					$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

					$lin++;
					$lin++;
					$unit = '';
					$linini=$lin;
					// titulos
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Nro")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Description")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($langs->trans("Unit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Priceunit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray($styleTitleline);
					$lin++;
					//recorremos
					$num=1;
					$aResource = $aResources[$aOrderresource[$hoja]];

					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj['ref']]['amount']= "'".$aTitle[$hoja]."'"."!D".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj['label']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj['unit']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj['amount']);
						$num++;
						$lin++;
					}
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('D')->setAutoSize(true);

					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':D'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.($linini+1).':D'.$linfin)->applyFromArray($style);

				}
				if($hoja==2){
					$objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
					$objWorkSheet->setTitle("Worksheet$hoja");
					$lin=1;
					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray($stylehead);
					$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
					$sheet->mergeCells('A'.$lin.':D'.$lin);
					$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

					$lin++;
					$lin++;
					$unit = '';
					$linini=$lin;
					// titulos
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Nro")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Description")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($langs->trans("Unit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Priceunit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray($styleTitleline);
					$lin++;
					//recorremos
					$num=1;
					$aResource = $aResources[$aOrderresource[$hoja]];

					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj['ref']]['amount']= "'".$aTitle[$hoja]."'"."!D".$lin;
						//$aResourcelin[$cResource][$obj->ref]['amount']= "$'".$aTitle[$hoja].".D".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj['label']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj['unit']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj['amount']);
						$num++;
						$lin++;
					}
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('D')->setAutoSize(true);

					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':D'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.($linini+1).':D'.$linfin)->applyFromArray($style);
				}
				if($hoja==3){
					$objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
					$lin=1;
					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':E'.$lin)->applyFromArray($stylehead);
					$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
					$sheet->mergeCells('A'.$lin.':E'.$lin);
					$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

					$lin++;
					$lin++;
					$unit = '';
					$linini=$lin;
					// titulos
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Nro")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Description")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($langs->trans("Unit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Improductive")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,html_entity_decode($langs->trans("Productive")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':E'.$lin)->applyFromArray($styleTitleline);
					$lin++;
					//recorremos
					$num=1;
					$aResource = $aResources[$aOrderresource[$hoja]];

					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj['ref']]['amount']= "'".$aTitle[$hoja]."'"."!E".$lin;
						$aResourcelin[$cResource][$obj['ref']]['amount_noprod']= "'".$aTitle[$hoja]."'"."!D".$lin;
						//$aResourcelin[$cResource][$obj->ref]['amount_noprod']= "$'".$aTitle[$hoja].".D".$lin;
						//$aResourcelin[$cResource][$obj->ref]['amount']= "$'".$aTitle[$hoja].".D".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj['label']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj['unit']));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj['amount_noprod']);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$obj['amount']);
						$num++;
						$lin++;
					}
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('E')->setAutoSize(true);
					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':E'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.($linini+1).':D'.$linfin)->applyFromArray($style);
				}

				if($hoja==4)
				{
					$cTitle=html_entity_decode($langs->trans("Unitpriceanalysis"));

					$objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
					$objWorkSheet->setTitle($cTitle);
					//echo '<pre>';
					//print_r($aResourcelin);
					$lin = 2;
					foreach ($aReport AS $j => $aDatatask)
					{
						foreach ($aDatatask AS $fk_task => $lines)
						{
							//echo '<hr>fk_task '.$fk_task;
							$aCel = $aReportCel[$j][$fk_task];
							$cTitle=html_entity_decode($langs->trans("Unitpriceanalysis"));

							//$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin)->getFont()->setName('Arial');
							//$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin)->getFont()->setSize(12);
							//$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin)->getFont()->setBold(true);
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin, $cTitle)->getStyle('A1')->getFont()->setBold(true);
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':G'.$lin)->applyFromArray($stylehead);


							$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
							$sheet->mergeCells('A'.$lin.':G'.$lin);
							$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

							$lin++;

							$lin++;
							$lin++;
							$unit = '';
							$res= $object->fetch($fk_task);
							$aItem[$object->id]['ref']=$object->ref;
							$aItem[$object->id]['detail']=$object->detail;
							$objTmp = new ItemsLine($db);
							$objTmp->fk_unit = $object->fk_unit;
							$aItem[$object->id]['unit']=$objTmp->getLabelOfUnit('short');

							$objectdetline = new ItemsproductLineext($db);
							if ($object->fk_unit >0)
							{
								$objectdetline->fk_unit = $object->fk_unit;
								$unit = $objectdetline->getLabelOfUnit('short');
							}
							//$objectdetadd->fetch($id, $fk_task,$fk_region,$fk_sector);
							// ProjectO
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Project").':'));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($cTitleproject.' '.$cTitleregion));
							$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('B'.$lin.':G'.$lin);
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
							$lin++;
							//actovotu
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Activity").':'));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($object->ref.' '.$object->detail));
							$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('B'.$lin.':G'.$lin);
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
							$lin++;
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Unit")));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$unit);
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
							$lin++;
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Quantity")));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,($objectdet->quant>0?$objectdet->quant:1));
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
							$lin++;
							$moneda = html_entity_decode(ucwords(dol_strtolower(currency_name(($conf->global->ITEMS_DEFAULT_BASE_CURRENCY?$conf->global->ITEMS_DEFAULT_BASE_CURRENCY:$conf->currency),0))));

							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Currencybase")));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$moneda);
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
							$lin++;
							$linhead= $lin;
							//armamos titulos
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$langs->trans("Priceunit"));
							$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('E'.$lin.':F'.$lin);

							$lin++;
							//armamos titulos
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Description")));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Unit")));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,$langs->trans("Quantity"));
							//$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$langs->trans("Percentproductivity"));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,'% Prod.');
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$langs->trans("Improductive"));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('F'.$lin,$langs->trans("Productive"));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('G'.$lin,$langs->trans("Pricetotal"));

							$linant=$lin-1;
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linant.':G'.$lin)->applyFromArray($styleTitleline);
							$lin++;

							//mostramos el cuerpo
							$valuepu=0;
							$aLine = array();

							foreach ($lines AS $k => $aData)
							{
								$aLine[$k] = $lin;
								$lPrint= true;
								foreach ($aData AS $nom => $row)
								{
									foreach ($row AS $nReg => $value)
									{
										if ($nReg==1 && empty($value) && $nom=='data') $lPrint = false;
										if ($lPrint)
										{
											if ($nom == 'datag')
											{
												$value = DOL_STRTOUPPER($value);
												$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
												$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':G'.$lin);
												$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':G'.$lin)->applyFromArray($styleDatag);
											}
											if ($nom == 'data')
											{
												if ($lFormula)
												{
													if ($nReg==7)
													{
														//echo '<hr>celantes '.
														$cel = $aCel[$k];
														$cel = dol_strtoupper(str_replace('_'.$k.'_',$lin,$cel));
														if (strlen($cel)==0)$cel= '0';
														else $cel = '+'.$cel;
														//echo '<br>despues '.$cel.' d= '.$value;
														$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$cel);
														//if (strlen($cel)>0)
														//$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getCalculatedValue(true);
													}
													elseif($nReg==6)
													{
														//echo '<hr>para  '.$j.' '.$fk_task.' '.$k;
														$aTmp = $aSpreadresources[$j][$fk_task][$k];
														//echo '<pre>';
														//print_r($aTmp);
														//print_r($aResourcelin[$aTmp['group']][$aTmp['ref']]);
														//echo '<hr>existe  '.$aResourecelin[$aTmp['group']][$aTmp['ref']];
														if(!empty($aResourcelin[$aTmp['group']][$aTmp['ref']]))
														{
															//echo 'se registra '.$lin.' '.$aResourcelin[$aTmp['group']][$aTmp['ref']]['amount'];
															$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$aResourcelin[$aTmp['group']][$aTmp['ref']]['amount']);
														}
														else {
															$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
														}
													}
													elseif($nReg==5)
													{
														//echo '<hr>para  '.$j.' '.$fk_task.' '.$k;
														$aTmp = $aSpreadresources[$j][$fk_task][$k];
														//echo '<pre>';
														//print_r($aTmp);
														//print_r($aResourcelin[$aTmp['group']][$aTmp['ref']]);
														//echo '<hr>existe  '.$aResourecelin[$aTmp['group']][$aTmp['ref']];
														if(!empty($aResourcelin[$aTmp['group']][$aTmp['ref']]) && $aTmp['group']=='MQ')
														{
															//echo '<br>reemplazaformula '.$aResourcelin[$aTmp['group']][$aTmp['ref']];
															$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$aResourcelin[$aTmp['group']][$aTmp['ref']]['amount_noprod']);
														}
														else
														{
															$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
														}
													}
													else
													{
														$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
													}
												}
												else {
													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
												}
												
											}
										}
										if ($nom == 'partial')
										{
											if ($lFormula)
											{
												if ($nReg == 7)
												{
													//echo '<hr>celantespar '.
													$cel = $aCel[$k];
													foreach ($aLine AS $k1 => $lin1)
													{
														//echo '<br> busca _'.$k1.'_ reemplaza '.$lin1;
														$cel = str_replace('_'.$k1.'_',$lin1,$cel);
													}
													if (strlen($cel)==0)$cel= '0';
													else
													{
														if (substr($cel,0,1)!='+') $cel = '+'.$cel;
													}
													$cel = dol_strtoupper($cel);

													//echo '<br>despuespar '.$cel.' vpar= '.$value;
													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$cel);
													//if (strlen($cel)>0)
													//$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getOldCalculatedValue(true);
												}
												elseif($nReg==6)
												{
													//echo '<hr>para  '.$j.' '.$fk_task.' '.$k;
													$parameter = $aSpreadparameter[$j][$fk_task][$k];
													//echo '<pre>';
													//print_r($aTmp);
													//print_r($aResourcelin[$aTmp['group']][$aTmp['ref']]);
													//echo '<hr>existe  '.$aResourecelin[$aTmp['group']][$aTmp['ref']];
													if(!empty($aResourcelin[$parameter]))
													{
														//echo 'se registra '.$lin.' '.$aResourcelin[$aTmp['group']][$aTmp['ref']]['amount'];
														$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$aResourcelin[$parameter]['amount']);
													}
													else {
														$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
													}
												}
												else
													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
											}
											else {
												$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
											}


											$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':E'.$lin);
											$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin)->applyFromArray($estilo);
										}
										if ($nom == 'total')
										{
											if ($lFormula)
											{

												if ($nReg == 7)
												{
													//echo '<hr>celantespar '.
													$cel = $aCel[$k];
													foreach ($aLine AS $k1 => $lin1)
													{
														$cel = str_replace('_'.$k1.'_',$lin1,$cel);
													}
													if (strlen($cel)==0)$cel= '0';
													else
													{
														//if (substr($cel,0,1)!='+') $cel = '+'.$cel;
													}
													$cel = dol_strtoupper($cel);
													//echo '<br>despuestotal '.$cel.' val= '.$value;
													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$cel);
													//$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getCalculatedValue(true);
												}
												else

													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
											}
											else {
												$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
											}
											$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':E'.$lin);
											$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin)->applyFromArray($estilo);
											$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':G'.$lin)->applyFromArray($styleTotal);
										}
										if ($nom == 'totalpu')
										{
											if ($lFormula)
											{
												if ($nReg == 7)
												{
													//echo '<hr>celantestotapu '.
													$cel = $aCel[$k];
													foreach ($aLine AS $k1 => $lin1)
													{
														$cel = str_replace('_'.$k1.'_',$lin1,$cel);
													}
													if (strlen($cel)==0)$cel= '0';
													else
													{
														if (substr($cel,0,1)!='+') $cel = '+'.$cel;
													}
													$cel = dol_strtoupper($cel);
													echo '<br>despuespu '.$cel.' lin '.$lin.' valor= '.$value;
													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$cel);
													//if (strlen($cel)>0)
													//$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getCalculatedValue(true);
												}
												else
												{
													echo '<br>despuesotro '.$cel.' lin '.$lin.' valor= '.$value;
													$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
												}
												$aItempu[$object->id]="'".$cTitle."'"."!".$aColumn[$nReg].$lin;
											}
											else {
												$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
											}
											$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':F'.$lin);
											$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':G'.$lin)->applyFromArray($styleTotalpu);
											if ($nReg==7) $valuepu = $value;
										}
									}
								}
								if ($lPrint)
									$lin++;
							}

							//$lin++;
							//echo '<hr>finnnn ';
							//print_r($aLine);
							$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
							$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11);	
							$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11);
							$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(11);
							$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11);
							$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11);

							//vamos a pintar las lineas
							$linbutton = $lin-1;
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linhead.':G'.$linbutton)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linhead.':G'.$linbutton)->applyFromArray($style);
							//$lin++;
							//vamos a mostrar el valor del precio unitario
							//$cNumtext = $langs->trans('Son').': '.num2texto(price2num($valuepu,'MT'),'');
							//$cNumtext.= ' '.$moneda;
							///$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$cNumtext);
							//$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':G'.$lin);
							$lin++;
						}
					}

				}
				if($hoja==5)
				{
					//presupuesto general
					$objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
					$objWorkSheet->setTitle($langs->trans('Generalbudget'));
					$lin=1;
					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($langs->trans('Generalbudget'));

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans('Generalbudget')));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':F'.$lin)->applyFromArray($stylehead);
					$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
					$sheet->mergeCells('A'.$lin.':F'.$lin);
					$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

					$lin++;
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Project").':'));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($cTitleproject.' '.$cTitleregion));
					$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('B'.$lin.':G'.$lin);
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
					$lin++;
					$moneda = html_entity_decode(ucwords(dol_strtolower(currency_name(($conf->global->ITEMS_DEFAULT_BASE_CURRENCY?$conf->global->ITEMS_DEFAULT_BASE_CURRENCY:$conf->currency),0))));

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Currencybase")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$moneda);
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
					$lin++;
					$lin++;

					$unit = '';
					$linini=$lin;
					// titulos
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Item")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Description")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($langs->trans("Unit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Quantity")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,html_entity_decode($langs->trans("Priceunit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('F'.$lin,html_entity_decode($langs->trans("Totalcost")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':F'.$lin)->applyFromArray($styleTitleline);
					$lin++;
					//recorremos
					$num=1;
					if ($resItemsgroup>0)
					{
						//print_r($aItemspu);
						$lines = $objItemsgroup->lines;
						foreach ($lines AS $j => $line)
						{
							if($line->type == 1)
							{
								$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$line->ref);
								$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$line->detail);
								$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('B'.$lin.':F'.$lin);
								$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':F'.$lin)->applyFromArray($styleDatag);
								$lin++;
							}
							else {
								$fk_task = $line->fk_item;
								if(!empty($aItem[$line->fk_item]))
								{
									$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$aItem[$fk_task]['ref']);
									$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$aItem[$fk_task]['detail']);
									$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,$aItem[$fk_task]['unit']);
									$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,1);
									$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,'='.$aItempu[$line->fk_item]);
									$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('F'.$lin,'=D'.$lin.'*E'.$lin);
									$lin++;
								}
							}
						}
						//PARA EL Total
						$linsumini = $linini+1;
						$linsumfin = $lin-1;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$langs->trans('Totalcostproyect'));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':E'.$lin);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('F'.$lin,'=SUM(F'.$linsumini.':F'.$linsumfin.')');
						$lin++;
						$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('A')->setAutoSize(true);
						$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('B')->setAutoSize(true);
						$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('C')->setAutoSize(true);
						$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('D')->setAutoSize(true);
						$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('E')->setAutoSize(true);
						$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('F')->setAutoSize(true);
						$linfin=$lin-1;
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':F'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':F'.$linfin)->applyFromArray($style);
					}

				}
				if($hoja==6)
				{
					//productividad

					$objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
					$objWorkSheet->setTitle($langs->trans('Productivity'));
					$lin = 2;
					foreach ($aProduction AS $fk_task =>$aEquipment)
					{
						//contamos la cantidad de equipos
						$nEquip = count($aEquipment);
						$nGroup =$nEquip+3;
						$nGroupfin = $nEquip+3;
						//$objectdetadd->fetch($id, $fk_task,$fk_region,$fk_sector);
						// ProjectO
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Project").': '.$cTitleproject.' '.$cTitleregion));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':'.$aColumnprod[$nGroup].$lin);
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
						$lin++;
						//actovotu
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Activity").': '.$aItem[$fk_task]['ref'].' '.$aItem[$fk_task]['detail']));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':'.$aColumnprod[$nGroup].$lin);
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
						$lin++;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Unit").': '.$aItem[$fk_task]['unit']));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':'.$aColumnprod[$nGroup].$lin);
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
						$lin++;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Production").': '.$aItem[$fk_itask]['hour_production']));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':'.$aColumnprod[$nGroup].$lin);
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':C'.$lin)->applyFromArray($styleTitle);
						$lin++;

						//armamos el cuerpo
						$pos= 4;
						unset($aTmptitle);
						foreach($aEquipment AS $fk_items_product =>$data)
						{
							$aTmptitle[$fk_items_product]['label'] =$aItemproduct[$fk_items_product]->label;
							$aTmptitle[$fk_items_product]['formula'] =$aItemproduct[$fk_items_product]->formula;
							$aTmptitle[$fk_items_product]['pos'] =$aColumnprod[$pos];
							$pos++;
						}
						//echo '<hr>';
						//print_r($aEquipment);
						//echo '<hr>';
						//print_r($aTmptitle);

						$linini = $lin;
						//vamos a imprimir los titulos
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,'');
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans('Variable')));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($langs->trans('Unit')));
						foreach ($aTmptitle AS $l =>$data)
						{
							//echo '<hr>registra '.$data['label'].' encolumna '.$data['pos'].' fila '.$lin;
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($data['pos'].$lin,html_entity_decode($data['label']));
						}
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':'.$aColumnprod[$nGroupfin].$lin)->applyFromArray($styleTitleline);
						$lin++;
						foreach ($aPuvariables AS $fk_variable =>$datavar)
						{
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($datavar['ref']));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($datavar['label']));
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($datavar['unit']));
							//vamos a recorrer segun los $fk_items_produ
							foreach ($aTmptitle AS $fk =>$aTmpdata)
							{
								$aData =$aEquipment[$fk];

								$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aTmpdata['pos'].$lin,$aData[$fk_variable]['quantity']);
							}
							$lin++;
						}
						//imprimimos su formulas
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans('Formula')));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':C'.$lin);
						foreach ($aTmptitle AS $fk =>$aTmpdata)
						{

							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aTmpdata['pos'].$lin,$aTmpdata['formula']);
						}
						$lin++;
						//imprime hour_production
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans('Hourproduction')));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':C'.$lin);
						foreach ($aTmptitle AS $fk =>$aTmpdata)
						{
							$aData =$aEquipment[$fk];
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aTmpdata['pos'].$lin,$aData['hour_production']);
						}
						$lin++;
						//imprime la cantidad
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans('Quantity')));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':C'.$lin);
						foreach ($aTmptitle AS $fk =>$aTmpdata)
						{
							$aData =$aEquipment[$fk];
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aTmpdata['pos'].$lin,$aData['units']);
						}
						$lin++;
						//imprime performance
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans('Applicableperformance')));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':C'.$lin);
						foreach ($aTmptitle AS $fk =>$aTmpdata)
						{
							$aData =$aEquipment[$fk];
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aTmpdata['pos'].$lin,$aData['performance']);
						}
						$lin++;
						//imprime percent
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,'% '.html_entity_decode($langs->trans('Productivity')));
						$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':C'.$lin);
						foreach ($aTmptitle AS $fk =>$aTmpdata)
						{
							$aData =$aEquipment[$fk];
							$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aTmpdata['pos'].$lin,$aData['price_productive']);
						}
						$lin++;
						
						$linfin=$lin-1;
						$nGroupfin = $nEquip+3;
						$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);	
						$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);	
						for($a = 4;$a<=$nGroupfin;$a++)
						{
							$objPHPExcel->getActiveSheet()->getColumnDimension($aColumnprod[$a])->setWidth(15);	
						}
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':'.$aColumnprod[$nGroupfin].$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':'.$aColumnprod[$nGroupfin].$linfin)->applyFromArray($style);

					}
					$lin++;

				}
				if($hoja==17){
					$objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
					$objWorkSheet->setTitle("Worksheet$hoja");
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A1', 'SR No. In sheet 3')->getStyle('A1')->getFont()->setBold(true); //$objPHPExcel->setActiveSheetIndex($hoja)->setCellValueByColumnAndRow($col++, $row++, $i++);
					//setting value by column and row indexes if needed
				}
				$hoja++;
			}
			//exit;
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->setPreCalculateFormulas(true);
			//$objWriter->save("excel/ReportPOA.xlsx");
			$dir = $conf->budget->dir_output.'/tmp';

			$file = 'priceunits.xlsx';
			$objWriter->save($dir.'/'.$file);
			header("Location: ".DOL_URL_ROOT.'/budget/items/fiche_export.php?archive='.$file);
		}
	}
}


//vamos a armar el options para region geographic
$filter = " AND t.status = 1";
$res = $objCregiongeographic->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
$optionsregion= '<option value=""></option>';
if ($res>0)
{
	//if ($res == 1) $optionsregion = '';
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
	{
		$aRegion[$line->id] = $line->ref.' - '.$line->label;
		$selected = '';
		if ($search_fk_region == $line->id) $selected = ' selected';
		$optionsregion.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label;
	}
}

$filter = " AND t.active = 1";
$res = $objCclasfin->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
$optionssector= '<option value=""></option>';
if ($res>0)
{
	//if ($res == 1) $optionssector = '';
	$lines = $objCclasfin->lines;
	foreach ($lines AS $j => $line)
	{
		$aTypeStructure[$line->id] = $line->ref.' - '.$line->label;
		$selected = '';
		if ($search_fk_sector == $line->id) $selected = ' selected';
		$optionssector.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label;
	}
}


//vamos a armar el options para region geographic
$filter = " AND t.status = 1";
$res = $objPutypestructure->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
$optionstypestr= '<option value=""></option>';
if ($res>0)
{
	if ($res == 1) $optionstypestr = '';
	$lines = $objPutypestructure->lines;
	foreach ($lines AS $j => $line)
	{
		$aTypestructure[$line->code] = $line->code.' - '.$line->label;
		$selected = '';
		if ($search_type_structure == $line->code) $selected = ' selected';
		$optionstypestr.= '<option value="'.$line->code.'" '.$selected.'>'.$line->code.' - '.$line->label;
	}
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Itemsexport');



//armamos las regiones en un array
$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$linesclassfin = $objCclasfin->lines;
	foreach ($linesclassfin AS $j => $line)
		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
}

llxHeader('', $title, $help_url);

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
		$("#type_structure").change(function() {
			document.formitem.action.value="'.$action.'";
			document.formitem.submit();
			});
		});';
		print '</script>'."\n";
	}

	if ($action == 'export' || $action == 'exportresource')
	{
		print load_fiche_titre($langs->trans("Items"));
		$aType= array(/*0=>$langs->trans('All'),*/'MO'=>$langs->trans('Workforce'),'MA'=>$langs->trans('Material'),'MQ'=>$langs->trans('Machineryandequipment'));
		print '<form name="formitem" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		if ($action == 'export') print '<input type="hidden" name="action" value="add">';
		if ($action == 'exportresource') print '<input type="hidden" name="action" value="addres">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

		dol_fiche_head();

		print '<table class="border centpercent">'."\n";


		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_sector").'</td><td>';
		print $form->selectarray('fk_sector',$aInstitutional,GETPOST('fk_sector'),1);
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region").'</td><td>';
		print $form->selectarray('fk_region',$aRegion,GETPOST('fk_region'),1);
		//print '<select name="fk_region">'.$optionsregion.'</select>';
		print '</td></tr>';

		if ($action == 'exportresource')
		{
			print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>';
			print $form->selectarray('type_structure',$aType,GETPOST('type_structure'));
			print '</td></tr>';
		}
		else
		{
			print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td>';
			print $form->selectarray('type_structure',$aTypestructure,GETPOST('type_structure'),1);
			//print '<select name="type_structure">'.$optionstypestr.'</select>';
			print '</td></tr>';
		}

		//vamos a mostrar los parametros de calculo
		$filter = " AND t.type != ' ' ";
		$res = $objParametercalculation->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$lines = $objParametercalculation->lines;
			foreach ($lines AS $j => $line)
			{
				print '<tr><td class="fieldrequired">'.$langs->trans($line->label).'</td><td>';
				print '<input type="number" min="0" step="any" name="'.$line->type.'" value="'.$line->amount.'">';
				print '</td></tr>';
			}
		}

		print '</table>'."\n";
		setEventmessages($langs->trans('Thesevalueswillonlybeusediftheformulasareconfiguredtoobtainthevaluesoftheparameters'),null,'warnings');
		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';
	}


	// End of page
	llxFooter();
	$db->close();

	?>
