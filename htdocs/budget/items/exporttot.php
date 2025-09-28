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
require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';


//dol_include_once('/budget/class/items.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsdet.class.php');
dol_include_once('/budget/class/itemsproductext.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');
dol_include_once('/budget/class/itemsregion.class.php');
dol_include_once('/budget/class/itemsgroup.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
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
$objItemsgroup = new Itemsgroup($db);
$objItemsregion = new Itemsregion($db);

$objUnit = new Cunits($db);

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
		$filter = "";
		$res = $objItemsgroup->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$linesgroup = $objItemsgroup->lines;
			foreach ($linesgroup AS $a => $linegroup)
			{
				$fk_item = $linegroup->fk_item;
				if ($fk_item > 0)
				{
					$object = new Itemsext($db);
					$res = $object->fetch($fk_item);
					$objItemsregion->fetch(0, $fk_item,$fk_region,$fk_sector);
					if ($res > 0 && $objItemsregion->status==1 && $objItemsregion->active==1)
					{
						//echo '<hr>'.$type_structure.' '.$fk_region.' '.$fk_sector;
						$suma = $objItemsproduct->procedure_calc($fk_item,$type_structure, $fk_region,$fk_sector,false);
						$aReport[$linegroup->id]['data']['ref'] = $linegroup->ref;
						$aReport[$linegroup->id]['data']['label'] = $linegroup->detail;
						$aReport[$linegroup->id]['data']['fk_unit'] = $linegroup->fk_unit;
						$aReport[$linegroup->id]['data']['quantity'] = 1;
						$aReport[$linegroup->id]['data']['price'] = $suma;
						$aReport[$linegroup->id]['data']['total'] = $suma;
					}
				}
				else
				{
					$aReport[$linegroup->id]['datag']['ref'] = $linegroup->ref;
					$aReport[$linegroup->id]['datag']['label'] = $linegroup->detail;
					$aReport[$linegroup->id]['datag']['fk_unit'] = '';
					$aReport[$linegroup->id]['datag']['quantity'] = '';
					$aReport[$linegroup->id]['datag']['price'] = '';
					$aReport[$linegroup->id]['datag']['total'] = '';
					//$aReport[$linegroup->id]['datag']['total'] = '';

				}
			}

			//echo '<pre>';
			//print_r($aReport);
			//echo '</pre>';exit;

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
			$styleDatag = 							array(
				'font'    => array(
					'bold'      => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
						'argb' => 'd6e584'
					),
					'endcolor'   => array(
						'argb' => 'd6e584'
					)
				)
			);
			$objPHPExcel = new PHPExcel();
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');

			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
			->setLastModifiedBy("yemer colque")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");

			//$objPHPExcel = $objReader->load($conf->budget->dir_output."/tmp/itemsbase.xlsx");

			//vamos a definir ciertos valores
			$aColumn=array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G');

			//PIE DE PAGINA
			$objPHPExcel->setActiveSheetIndex(0);

			//titulo
			$objPHPExcel->setActiveSheetIndex(0);
			$cTitle=$langs->trans("Generalbudget");

			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$sheet = $objPHPExcel->getActiveSheet();
			$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
			$sheet->getStyle('A2')->getFont()->setSize(15);


			$sheet->mergeCells('A2:F2');
			if($yesnoprice)
			$sheet->mergeCells('A2:F2');
			$sheet->getStyle('A2')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);

			// titulos de la fila
			$aTitulo=array();
			//$aTitulo[]=$langs->trans("Nro");

			$aTitulo[]=$langs->trans("Item");
			$aTitulo[]=$langs->trans("Description");
			$aTitulo[]=$langs->trans("Unid");
			$aTitulo[]=$langs->trans("Quantity");
			$aTitulo[]=$langs->trans("P.U");
			$aTitulo[]=$langs->trans("TOTAL");


			$aColumn=array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H');


			// impresion fila de los titulos
			$lin=7;
			$j=1;
			foreach ($aTitulo as $z => $linet)
			{
				$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$j].$lin,html_entity_decode($linet));
				//$lin++;
				$j++;
			}


			// fila de titulos color
			$objPHPExcel->getActiveSheet()->getStyle('A7'.':'.'F7')->applyFromArray($styleTitle);

			// CUERPO
			$j=1;
			$lin=8;

			foreach ((array)$aReport as $id => $group)
			{
				$j=1;
				foreach ($group as $k => $linedet)
				{

					if($k=='datag')
					{
						//referencia
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($linedet['ref']));
						$j++;
						// label
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,html_entity_decode($linedet['label']));
						$j++;
						$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':'.'F'.$lin)->applyFromArray($styleDatag);
					}
					elseif($k=='data')
					{
						//referencia
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($linedet['ref']));
						$j++;
						// label
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,html_entity_decode($linedet['label']));
						$j++;
						// fk_unit
						$cUnidad="";
						$resadd = $objUnit->fetch($linedet['fk_unit']);
						if($resadd>0)$cUnidad=$objUnit->code;
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$lin,$cUnidad);
						$j++;
						// quantity
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$lin,$linedet['quantity']);
						$j++;
						//price
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$lin,$linedet['price']);
						$j++;
						//total
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$lin,$linedet['total']);
						$j++;
					}
					$lin++;
				}

			}

			//FORMATOS DE LAS COLUMNAS
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			// FORMATO DE LAS COLUMANAS DE NUMEROS
			$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			// bordes de las celdas
			$linbutton=$lin-1;
			$objPHPExcel->getActiveSheet()->getStyle('A8'.':'.'F'.$linbutton)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			// Save Excel 2007 file
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter->save("excel/ReportPOA.xlsx");
			$dir = $conf->budget->dir_output.'/tmp/';
			$file = 'itemspresupuestogeneral.xlsx';

			$objWriter->save($dir.$file);

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
			$objPHPExcel->setActiveSheetIndex(0);
			$cTitle=$langs->trans('Resources').' '.$aType[$type];

			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$sheet = $objPHPExcel->getActiveSheet();
			$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
			$sheet->getStyle('A2')->getFont()->setSize(15);


			$sheet->mergeCells('A2:F2');
			if($yesnoprice)
			$sheet->mergeCells('A2:F2');
			$sheet->getStyle('A2')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);


			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			//$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);


			$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			//cabecera
			$lin = 4;
			$col = 'D';
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($langs->trans("N")));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,html_entity_decode($langs->trans("Label")));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$lin,$langs->trans("Unit"));
			if ($type=='MQ')
			{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$lin,$langs->trans("Fieldamount_noprod"));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$lin,$langs->trans("Fieldamount"));
				$col='E';
			}
			else
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$lin,$langs->trans("Priceunit"));
			$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':'.$col.$lin)->applyFromArray(
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
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,$reg);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,$aData['label']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$lin,$aData['unit']);
				if ($type=='MQ')
				{
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$lin,$aData['amount_noprod']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$lin,$aData['amount']);
				}
				else
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$lin,$aData['amount']);
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
