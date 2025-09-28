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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

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




$langs->load("assets");
$langs->load("stocks");

		//$langs->load("fabrication@fabrication");

if (!$user->rights->assets->repinv->write) accessforbidden();

$fk_group = GETPOST('fk_group');
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
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
$objGroup = new cAssetsgroup($db);
		//$objbeen = new Cassetsbeen($this->db);
$objbeen = new Cassetsbeen($db);


$objuser = new User($db);


$aFilterent = array();
$id = $_SESSION['kardexid'];


		//actions
$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();

if ($action == 'builddoc')
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
					//echo dol_print_date($_SESSION['assetsinv']['dateinisel'],'day').' '.dol_print_date($_SESSION['assetsinv']['datefinsel'],'day');

if (!empty($_SESSION['assetsinv']['dateini'])) $dateini = $_SESSION['assetsinv']['dateini'];
if (!empty($_SESSION['assetsinv']['dateinisel'])) $dateinisel = $_SESSION['assetsinv']['dateinisel'];
if (!empty($_SESSION['assetsinv']['datefin'])) $datefin = $_SESSION['assetsinv']['datefin'];
if (!empty($_SESSION['assetsinv']['datefinsel'])) $datefinsel = $_SESSION['assetsinv']['datefinsel'];


			// armado de excel

if ($action == 'excel')
{
	if ($conf->assets->dir_output)
	{

				//$object->fetch_thirdparty();

				//$deja_regle = 0;

		$objAssets = new Assetsext($db);
		$objbeen = new Cassetsbeen($db);
		$object  = new Assetsassignmentext($db);
		$objuser = new User($db);

		$objAss = new Assetsassignmentext($db);
		$objAssdet = new Assetsassignmentdetext($db);

		$objGroup->fetch ($fk_group);

					//FILTROS
		$i=5;
		$filter = '';
		if ($objGroup->id > 0)
			$filter = " AND t.type_group = '".$objGroup->code."'";
					//filtro de fechas
		if ($dateinisel >0 && $datefinsel > 0)
			$filter.= " AND t.date_adq BETWEEN ".$db->idate($dateinisel) ." AND ".$db->idate($datefinsel);


		$res = $objAssets->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filter);
		$Asst1 ='';
		$Asst2='';

		if($res>0)
		{
			$Asst2 = $objAssets->descrip;
		}


			//include_once DOL_DOCUMENT_ROOT.'/almacen/lib/format_excel.lib.php';
				// Create new PHPExcel object
					//echo date('H:i:s') , " Create new PHPExcel object" , PHP_EOL;
		$objPHPExcel = new PHPExcel();

					// Set document properties
					//echo date('H:i:s') , " Set properties" , PHP_EOL;
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("yemer colque")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");

					// Create a first sheet
					//echo date('H:i:s') , " Add data" , PHP_EOL;
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A4', "Nro");

		$objPHPExcel->getActiveSheet()->setCellValue('B4', "Code Assets");
		$objPHPExcel->getActiveSheet()->setCellValue('C4', "Name assets");
		$objPHPExcel->getActiveSheet()->setCellValue('D4', "date adq");
		$objPHPExcel->getActiveSheet()->setCellValue('E4', "Estate");
		$objPHPExcel->getActiveSheet()->setCellValue('F4', "Code Assets");
		$objPHPExcel->getActiveSheet()->setCellValue('G4', "Responsable");

		$objPHPExcel->getActiveSheet()->setCellValue('H4', "date assigne");
					//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);


		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setName('Candara');
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(20);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$objPHPExcel->getActiveSheet()->setCellValue('C1',"Assigned Assets");

		$objPHPExcel->getActiveSheet()->setCellValue('G1',"Grupo");
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize();
		$objPHPExcel->getActiveSheet()->setCellValue('G2',"Fecha de Inicio");
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize();
		$objPHPExcel->getActiveSheet()->setCellValue('G3',"Fecha Final");

		$objPHPExcel->getActiveSheet()->setCellValue('H1',$fk_group);
		$objPHPExcel->getActiveSheet()->setCellValue('I1',$option);
		$objPHPExcel->getActiveSheet()->setCellValue('H2',dol_print_date($dateinisel,'day'));
		$objPHPExcel->getActiveSheet()->setCellValue('h3',dol_print_date($datefinsel,'day'));

					//echo date('H:i:s') , " Rows to repeat at top" , PHP_EOL;
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
					// Set column widths
					//establecer ancho de columnas
					//echo date('H:i:s') , " Set column widths" , PHP_EOL;
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
					// Set alignments
				//establecer aliniaciones

					//echo date('H:i:s') , " Set alignments" , PHP_EOL;
		$objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				// Set fonts
				//establecer fuentes
					//echo date('H:i:s') , " Set fonts" , PHP_EOL;
		$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);

				//echo date('H:i:s') , " Set style for header row using alternative method" , PHP_EOL;

		$objPHPExcel->getActiveSheet()->getStyle('A1:H3')->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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



				//echo date('H:i:s') , " Set thick brown border outline around Total" , PHP_EOL;
		$styleThickBrownBorderOutline = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('argb' => 'FF993300'),
					),
				),
			);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleThickBrownBorderOutline);
				//echo date('H:i:s') , " Set thin black border outline around column" , PHP_EOL;
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF000000'),
					),
				),
			);


		foreach ($objAssets->lines AS $j => $line)
		{
			$Cont+=1;
					//$resb = $objbeen->fetch(0,$line->been);


			$been = '';
			$Asst1 ='';


			$resb = $objbeen->fetch(0,$line->been);
			$objuser->fetch($obj->fk_user);
				//$resb1 = $objuser->fetch($obj->fk_user);




			if($resb>0)
			{
				$Asst1 = $line->ref;
				$Asst2 = $line->descrip;
				$Asst3 = $line->date_adq;

				$been = $objbeen->label;

			}

			$objAss = new Assetsassignmentext($db);
			$objAssdet = new Assetsassignmentdetext($db);
			$filter = " AND t.fk_asset = ".$line->id;
			$res = $objAssdet->fetchAll('DESC','t.date_assignment',0,0,array(1=>1),'AND',$filter,true);
			$dateAssignment = 0;
			$Asign="";

			if ($res == 1)
			{
				$objAss->fetch($objAssdet->fk_asset_assignment);

				$dateAssignment = $objAss->date_assignment;
						//print '<td>'.$objAss->ref.'</td>';

				$Asign=$objAss->ref;

			}
			elseif ($res > 1)
			{
				$fk_asset_assignment = 0;
				foreach($objAssdet->lines AS $j => $line)
				{
					if (empty($fk_asset_assignment))
						$fk_asset_assignment = $line->fk_asset_assignment;
					else
						continue;
				}
				$objAss->fetch($fk_asset_assignment);
				$dateAssignment = $objAss->date_assignment;
						//print '<td>'.$objAss->ref.'</td>';
				$Asign=$objAss->ref;
						 // excel
			}
			else
			{
				$dateAssignment = 0;
						//print '<td>&nbsp;</td>';
				$Asign="";
					//$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, "");
			}

					// usuario
			$objUser = new User($db);
			$resy = $objUser->fetch($objAss->fk_user);
			$Usu1="";
			if ($resy > 0)
			{

				$Usu1=$objUser->lastname.' '.$objUser->firstname;

						//print '<td>'.$objUser->getNomUrl(1).'</td>';
			}
			else
						//print '<td>&nbsp;</td>';
				$Usu1=$objUser->lastname.' '.$objUser->firstname;


			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $Cont)
			->setCellValue('B' . $i, $Asst1)
			->setCellValue('C' . $i, $Asst2)
			->setCellValue('D' . $i, dol_print_date($Asst3,'day'))
			->setCellValue('E' . $i, $been)
			->setCellValue('F' . $i, $Asign)
			->setCellValue('G' . $i, $Usu1);

			if ($dateAssignment>0)
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, dol_print_date($dateAssignment,'day'));
			else
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $i, "");
					// excel
			$i++;




		}
		$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save("excel/inventario.xlsx");

		header("Location: ".DOL_URL_ROOT.'/assets/report/excel/fiche_export.php?archive=inventario.xlsx');

	}







}



$formfile = new Formfile($db);
$form = new Formv($db);
		//$aArrjs = array('almacen/javascript/recargar.js');
		//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';

llxHeader("",$langs->trans("Inventario"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans(""), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';

print '<table class="border" width="100%">';
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

		//echo '<hr>'.$dateinisel.' '.dol_print_date($dateinisel,'day');
		// desde fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);

print '</td></tr>';

		// hasta fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);

print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

		//YEMER


if(($action == 'edit'||  $action=='edits'))
{

	$objAssets = new Assetsext($db);
	$objbeen = new Cassetsbeen($db);
	$object  = new Assetsassignmentext($db);
	$objuser = new User($db);

	$objAss = new Assetsassignmentext($db);
	$objAssdet = new Assetsassignmentdetext($db);

	$objGroup->fetch ($fk_group);

			//FILTROS

	$filter = '';
	if ($objGroup->id > 0)
		$filter = " AND t.type_group = '".$objGroup->code."'";
			//filtro de fechas
	if ($dateinisel >0 && $datefinsel > 0)
		$filter.= " AND t.date_adq BETWEEN ".$db->idate($dateinisel) ." AND ".$db->idate($datefinsel);
			//echo $filter;

	$res = $objAssets->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filter);

	$Asst1 ='';
	$Asst2='';

	if($res>0)
	{
		$Asst2 = $objAssets->descrip;
	}


			//if ($conf->almacen->dir_output)
	if ($conf->assets->dir_output)
	{

				//$object->fetch_thirdparty();

		$deja_regle = 0;

				// Definition of $dir and $file
		if ($object->specimen)
		{
			$dir = $conf->assets->dir_output;
			$file = $dir . "/SPECIMEN.pdf";
		}
		else
		{
			$objectref = 'inventory';
			if (!empty($object->ref))
				$objectref .= dol_sanitizeFileName($object->ref);
			$dir = $conf->assets->dir_output;
			$file = $dir . "/" . $objectref . ".pdf";
		}

		if (! file_exists($dir))
		{
			if (dol_mkdir($dir) < 0)
			{
				$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
				return 0;
			}
		}

		if (file_exists($dir))
		{

			print_barre_liste($langs->trans("Assigned Assets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);


			print '<table class="border" width="100%">';
			//print '<tr class="liste_titre">';

			//print '<th align="center" colspan="8" class="thlineleft">'.$langs->trans('Assigned   Assets').'</th>';
			//print '</tr>';
			print '<tr class="liste_titre">';
			print '<td align="center">'.$langs->trans("Nro").'</td>';
			print '<td align="center">'.$langs->trans("code Assets").'</td>';
			print '<td align="center">'.$langs->trans("Name Assets").'</td>';
			print '<td align="center">'.$langs->trans("date adq").'</td>';
			print '<td align="right">'.$langs->trans("Estate").'</td>';
			print '<td align="right">'.$langs->trans("Code Assets").'</td>';
			print '<td align="right">'.$langs->trans("Responsable").'</td>';
			print '<td align="center">'.$langs->trans("date assigne").'</td>';
			print '</tr>';


			$Cont=0;
			$nblignes = count($objAssets->lines);
			$i= 5;
			$var = true;

			foreach ($objAssets->lines AS $j => $line)
			{
				$Cont+=1;
					//$resb = $objbeen->fetch(0,$line->been);


				$been = '';
				$Asst1 ='';


				$resb = $objbeen->fetch(0,$line->been);
				$objuser->fetch($obj->fk_user);
					//$resb1 = $objuser->fetch($obj->fk_user);

				if($resb>0)
				{
					$Asst1 = $line->ref;
					$Asst2 = $line->descrip;
					$Asst3 = $line->date_adq;

					$been = $objbeen->label;



				}
					//pantalla
				$var = !$var;
				print "<tr $bc[$var]>";

				print '<td>'.$Cont.'</td>';
				print '<td>'.$Asst1.'</td>';
				print '<td>'.$Asst2.'</td>';
					// fecha de asignacion
				print '<td>'.dol_print_date($Asst3,'day').'</td>';
				print '<td>'.$been.'</td>';
				$objAss = new Assetsassignmentext($db);
				$objAssdet = new Assetsassignmentdetext($db);
				$filter = " AND t.fk_asset = ".$line->id;
				$res = $objAssdet->fetchAll('DESC','t.date_assignment',0,0,array(1=>1),'AND',$filter,true);
				$dateAssignment = 0;
				$Asign="";

				if ($res == 1)
				{
					$objAss->fetch($objAssdet->fk_asset_assignment);

					$dateAssignment = $objAss->date_assignment;
					print '<td>'.$objAss->ref.'</td>';

					$Asign=$objAss->ref;

				}
				elseif ($res > 1)
				{
					$fk_asset_assignment = 0;
					foreach($objAssdet->lines AS $j => $line)
					{
						if (empty($fk_asset_assignment))
							$fk_asset_assignment = $line->fk_asset_assignment;
						else
							continue;
					}
					$objAss->fetch($fk_asset_assignment);
					$dateAssignment = $objAss->date_assignment;
					print '<td>'.$objAss->ref.'</td>';
					$Asign=$objAss->ref;
						 // excel
				}
				else
				{
					$dateAssignment = 0;
					print '<td>&nbsp;</td>';
					$Asign="";
					//$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, "");
				}

					// usuario
				$objUser = new User($db);
				$resy = $objUser->fetch($objAss->fk_user);
				$Usu1="";
				if ($resy > 0)
				{

					$Usu1=$objUser->lastname.' '.$objUser->firstname;

					print '<td>'.$objUser->getNomUrl(1).'</td>';
				}
				else
					print '<td>&nbsp;</td>';
				$Usu1=$objUser->lastname.' '.$objUser->firstname;

				if ($dateAssignment>0)
					print '<td>'.dol_print_date($dateAssignment,'day').'</td>';
				else
					print '<td>&nbsp;</td>';
					//$objPHPExcel->getActiveSheet()->setCellValue('G' . $i, "");
				print '</tr>';
					//excel

				$i++;
			}

			print '</table>';
					//dol_fiche_end();
			print "<div class=\"tabsAction\">\n";
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_group='.$fk_group.'&action=excel">'.$langs->trans("Excel").'</a>';
			print '</div>';
		}
	}
}

print '<div class="tabsAction">';
//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';

$objGroup->fetch($fk_group);
$diradd = '';
$filename = '';
if ($objGroup->id == $fk_group)
	$filename=dol_sanitizeFileName($objGroup->code).$diradd;
			//cambiando de nombre al reporte
$filedir=$conf->assets->dir_output;
$urlsource=$_SERVER['PHP_SELF'].'?fk_group='.$fk_group;
$genallowed=$user->rights->assets->repinv->write;
$delallowed=$user->rights->assets->repinv->del;
$objGroup->modelpdf = 'fractalinventario';
print '<br>';
print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objGroup->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";

$db->close();

llxFooter();
?>
