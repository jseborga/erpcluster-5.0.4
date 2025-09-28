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
			//array para capturar las posiciones
			$aResourcelin=array();

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
			$aOrderresource = array(3=>'PU',2=>'MQ',1=>'MO',0=>'MA');
			$aTitle = array(3=>$langs->trans('AnalisisPrecioUnitario'),0=>$langs->trans('Materials'),1=>$langs->trans('Workforce'),2=>$langs->trans('Machineryandequipment'));
			//vamos a crear la hoja 0
			$col=1;
			$row=1;
			$i=1;
			$lin=1;
			$hoja_count = count($aOrderresource);
			foreach ($aOrderresource AS $hoja => $cResource)
			{
				$cTitle=html_entity_decode($langs->trans($aTitle[$hoja]));

				if($hoja==0)
				{
					$lin=1;
					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
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
					$aResource = $aResources[$cResource];

					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj->id]['amount']= "$'".$aTitle[$hoja].".D".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj->label));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj->unit));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount);
						$num++;
						$lin++;
					}
					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':D'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


				}
				if($hoja==1)
				{
					$objWorkSheet = $objPHPExcel->createSheet($hoja_count);

					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);
					$lin=1;

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
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
					$aResource = $aResources[$cResource];
					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj->id]['amount']= "$'".$aTitle[$hoja].".D".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj->label));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj->unit));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount);
						$num++;
						$lin++;
					}
					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':D'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				}
				if($hoja==2)
				{
					$objWorkSheet = $objPHPExcel->createSheet($hoja_count);

					//$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);
					$lin=1;
					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
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
					$aResource = $aResources[$cResource];
					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj->id]['amount_noprod']= "$'".$aTitle[$hoja].".D".$lin;
						$aResourcelin[$cResource][$obj->id]['amount']= "$'".$aTitle[$hoja].".E".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj->label));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj->unit));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount_noprod);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('E'.$lin,$obj->amount);
						$num++;
						$lin++;
					}
					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':D'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				}
				if($hoja==3)
				{
					$objWorkSheet = $objPHPExcel->createSheet($hoja_count);

					$lin=1;
					$objWorkSheet->setTitle($aTitle[$hoja]);
					$objPHPExcel->setActiveSheetIndex($hoja)->setTitle($aTitle[$hoja]);

					$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,html_entity_decode($cTitle));
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
					$aResource = $aResources[$cResource];

					foreach ((array) $aResource AS $j => $obj)
					{
						$aResourcelin[$cResource][$obj->id]['amount']= "$'".$aTitle[$hoja].".D".$lin;
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('A'.$lin,$num);
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('B'.$lin,html_entity_decode($obj->label));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('C'.$lin,html_entity_decode($obj->unit));
						$objPHPExcel->setActiveSheetIndex($hoja)->setCellValue('D'.$lin,$obj->amount);
						$num++;
						$lin++;
					}
					$linfin=$lin-1;
					$objPHPExcel->setActiveSheetIndex($hoja)->getStyle('A'.$linini.':D'.$linfin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


				}

			}
			//echo '<pre>';
			//print_r($aResourcelin);
			//echo '</pre>';exit;
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
