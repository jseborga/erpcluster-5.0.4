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
dol_include_once('/budget/class/itemsregion.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/budget/class/parametercalculation.class.php');
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

		$filter = " AND t.rowid IN (6)";
		$aResources = array();
		$res = $object->fetchAll('ASC','ref',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$lines = $object->lines;
			foreach ($lines AS $j => $line)
			{
				$res = $objItemsproduct->procedure_calc($line->id,$type_structure, $fk_region,$fk_sector,true,$aResources);
				//if ($res>0)
				//{
				$aReport[$line->id] = $objItemsproduct->aSpread;
				$aReportf[$line->id] = $objItemsproduct->aSpreadf;
				$aReportform[$line->id] = $objItemsproduct->aFormuladet;
				$aReportformula[$line->id] = $objItemsproduct->aSpreadformula;
				$aReportformulapartial[$line->id] = $objItemsproduct->aSpreadformulapartial;
				$aReportCel[$line->id] = $objItemsproduct->aCel;
				$aResources = $objItemsproduct->aResources;
				//}
			}
			//echo '<pre>';
			//print_r($aReport);
			//echo 'partial<hr>';
			//print_r($aReportformulapartial);
			//echo 'cel<hr>';
			//print_r($aReportCel);
			//print_r($aResources);
			//echo '</pre>';
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
			$estilo = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				)
			);
			$styleTitle = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 11,
					'name'  => 'Verdana'
				)
			);
			$styleTitleline = 						array(
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
						'argb' => '009846'
					),
					'endcolor'   => array(
						'argb' => '009846'
					)
				)
			);
			$styleDatag = 										array(
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

			$objPHPExcel = new PHPExcel();
			//$objReader = PHPExcel_IOFactory::createReader('Excel2007');

			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
			->setLastModifiedBy("yemer colque")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");

			//$objPHPExcel = $objReader->load("./excel/itemsbase.xlsx");
			$objWorkSheet = $objPHPExcel->createSheet();

			//vamos a definir ciertos valores
			$aColumn=array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G');
			$aOrderresource = array(0=>'MA',1=>'MO',2=>'MQ');
			$aTitle = array(0=>$langs->trans('Materials'),1=>$langs->trans('Workforce'),2=>$langs->trans('Machineryandequipment'));
			//vamos a crear la hoja 0
			foreach ($aOrderresource AS $hoja => $cResource)
			{
				$objWorkSheet->setTitle($aTitle[$hoja]);
				//$objPHPExcel->setActiveSheetIndex($hoja);
				//$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

				//$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
				//$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
				$aResource = $aResources[$cResource];
				$cTitle=html_entity_decode($langs->trans($aTitle[$hoja]));
				$lin=2;

				//$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray(array('font' => array( 'bold'  => true,'color' => array('rgb' => 'ff0000'),'size'  => 13,'name'  => 'Arial')));
				//$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$cTitle)->getStyle('A'.$lin.':D'.$lin)->applyFromArray(array('font' => array( 'bold'  => true,'color' => array('rgb' => 'ff0000'),'size'  => 13,'name'  => 'Arial')));

				//$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
				//$sheet->mergeCells('A'.$lin.':D'.$lin);
				//$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

				$lin++;
				$lin++;
				$unit = '';
				// titulos
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("Nro")));
/*
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Description")));
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($langs->trans("Unit")));
				if ($hoja==1)
				{
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Priceunit")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray($styleTitle);
				}
				elseif($hoja==2)
				{
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Hourcost")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':D'.$lin)->applyFromArray($styleTitle);
				}
				elseif($hoja==3)
				{
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,html_entity_decode($langs->trans("Improductive")));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,html_entity_decode($langs->trans("Productive")));
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':E'.$lin)->applyFromArray($styleTitle);
				}
				$lin++;
				//recorremos
				$num=1;
				foreach ($aResource AS $j => $obj)
				{
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj->label));
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj->unit));
					if ($hoja==1)
					{
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount);
					}
					elseif($hoja==2)
					{
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount);
					}
					elseif($hoja==3)
					{
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$obj->amount_noprod);
					}
					$lin++;
				}
				*/
			}
			$hoja++;

			$objWorkSheet = $objPHPExcel->createSheet($hoja);
	        $objWorkSheet->setTitle("Análisis de precio unitario");

			//Analisis precio unitario
			//$objPHPExcel->setActiveSheetIndex($hoja);
			//$objPHPExcel->setActiveSheetIndex($hoja)->setTitle("Análisis de precio unitario");

			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			// ENCABEZADO

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
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':G'.$lin)->applyFromArray(array('font' => array( 'bold'  => true,'color' => array('rgb' => 'ff0000'),'size'  => 13,'name'  => 'Arial')));

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
					$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
					$sheet->mergeCells('A'.$lin.':G'.$lin);
					$sheet->getStyle('A'.$lin)->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

					$lin++;
					$lin++;
					$unit = '';
					$res= $object->fetch($fk_task);
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
						//echo '<hr>lin '.$lin.' k '.$k;

						foreach ($aData AS $nom => $row)
						{
							foreach ($row AS $nReg => $value)
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
											if (strlen($cel)>0)
											$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getCalculatedValue(true);
										}
										else

										$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
									}
									else {
										$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
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
											if (strlen($cel)>0)
											$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getOldCalculatedValue(true);
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
											//echo '<br>despuespu '.$cel.' valor= '.$value;
											$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,'='.$cel);
											if (strlen($cel)>0)
											$objPHPExcel->setActiveSheetIndex($hoja)->getCell($aColumn[$nReg].$lin)->getCalculatedValue(true);
										}
										else

										$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue($aColumn[$nReg].$lin,$value);
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

						$lin++;
						//echo '<hr>finnnn ';
						//print_r($aLine);

					}

					//$lin++;
					//echo '<hr>finnnn ';
					//print_r($aLine);
					//vamos a pintar las lineas
					$linbutton = $lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linhead.':G'.$linbutton)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					//$lin++;
					//vamos a mostrar el valor del precio unitario
					//$cNumtext = $langs->trans('Son').': '.num2texto(price2num($valuepu,'MT'),'');
					//$cNumtext.= ' '.$moneda;
					///$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$cNumtext);
					//$objPHPExcel->setActiveSheetIndex($hoja)->mergeCells('A'.$lin.':G'.$lin);
					$lin++;
				}
				$lin++;
				$lin++;
			}
			//echo ''.$error;exit;
			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->setPreCalculateFormulas(true);
			//$objWriter->save("excel/ReportPOA.xlsx");
			$file = 'priceunits.xlsx';
			$objWriter->save($dir.'/'.$file);
			header("Location: ".DOL_URL_ROOT.'/budget/items/fiche_export.php?archive='.$file);
		}
		else
		{
			setEventmessages($langs->trans('Therearenovalidatedrecords'),null,'warnings');
			$action = 'export';
			//header("Location: ".DOL_URL_ROOT.'/budget/items/export.php?action=export');
			//exit;
		}
	}

	if ($action == 'addres')
	{
		//vamos a listar todos los insumos disponibles
		$sql = " SELECT t.rowid ";
		$sql.= " FROM ".MAIN_DB_PREFIX."items_product AS t ";
		$sql.= " WHERE t.group_structure = '".$type_structure."'";
		$sql.= " AND t.status = 1";

		$filter = " AND t.status = 1";
		$filter.= " AND t.group_structure = '".$type_structure."'";
		$filter.= " AND r.fk_region = ".$fk_region;
		$filter.= " AND r.fk_sector = ".$fk_sector;
		$res = $objItemsproduct->fetchGroupregion('ASC','t.label', 0, 0, array(), 'AND',$filter);
		$aReport= array();
		$a=0;
		if ($res > 0)
		{
			$lines = $objItemsproduct->lines;
			foreach ($lines AS $j => $line)
			{
				$objtmp = new ItemsproductLineext($db);
				$objtmp->fk_unit = $line->fk_unit;
				$aReport[$a]['ref']=$line->ref;
				$aReport[$a]['label']=$line->label;
				$aReport[$a]['unit']=$objtmp->getLabelOfUnit('short');
				$aReport[$a]['amount_noprod']=$line->amount_noprod;
				$aReport[$a]['amount']=$line->amount;
				$a++;
			}
			//armamos el excel
			$dir = $conf->budget->dir_output.'/tmp';
			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$error++;
					setEventmessages($langs->trans('Errorcreatingthedirectory'),null,'errors');
				}
			}

			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Ramiro Queso")
			->setLastModifiedBy("Yemer Colque")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");


			//formato
			$cTitle=$langs->trans('Resources').' '.$aType[$type];

			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A2')->getFont()->setName('Arial');
			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A2')->getFont()->setSize(12);
			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A2')->getFont()->setBold(true);
			$sheet = $objPHPExcel->setActiveSheetIndex($hoja);
			$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
			$sheet->getStyle('A2')->getFont()->setSize(15);


			$sheet->mergeCells('A2:F2');
			if($yesnoprice)
			$sheet->mergeCells('A2:F2');
			$sheet->getStyle('A2')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);


			$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('E')->setAutoSize(true);
			//$objPHPExcel->setActiveSheetIndex($hoja)->getColumnDimension('F')->setAutoSize(true);


			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			//cabecera
			$lin = 4;
			$col = 'D';
			$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($langs->trans("N")));
			$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($langs->trans("Label")));
			$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,$langs->trans("Unit"));
			if ($type=='MQ')
			{
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$langs->trans("Fieldamount_noprod"));
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$langs->trans("Fieldamount"));
				$col='E';
			}
			else
			$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$langs->trans("Priceunit"));
			$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$lin.':'.$col.$lin)->applyFromArray(
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

			// cuerpo
			$lin++;
			$reg=1;
			foreach ($aReport AS $j => $aData)
			{
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$reg);
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,$aData['label']);
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,$aData['unit']);
				if ($type=='MQ')
				{
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$aData['amount_noprod']);
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$aData['amount']);
				}
				else
				$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$aData['amount']);
				$lin++;
				$reg++;
			}

			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter->save("excel/ReportPOA.xlsx");
			$file = 'itemsresource.xlsx';
			$objWriter->save($dir.'/'.$file);
			header("Location: ".DOL_URL_ROOT.'/budget/items/fiche_export.php?archive='.$file);
		}
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
