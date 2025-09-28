<?php
/* Copyright (C) 7102 No One <example@email.bo>
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
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';


dol_include_once('/assets/class/assetsbalanceext.class.php');




$langs->load("assets");
$langs->load("stocks");

		//$langs->load("fabrication@fabrication");

if (!$user->rights->assets->repinv->write) accessforbidden();

$fk_departament = GETPOST('fk_departament');
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$date_ini = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin = dol_mktime(23,59,59,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

if(empty($date_ini)) $date_ini = dol_now();
if(empty($date_fin)) $date_fin = dol_now();


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

$objDepartament = new Pdepartament($db);

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
	$aExtras = unserialize($_SESSION['aExtras']);
	//print_r($aReportdetasset);

	// TITULO
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);
	//$this->activeSheet->getDefaultRowDimension()->setRowHeight($height);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);


	// COLOR DEL TITULO
	$objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray(
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
	$sheet->mergeCells('A2:O2');
	if(empty($aExtras['nombre'])){
		$sheet->setCellValueByColumnAndRow(0,2, $langs->trans("Reportassetsdepartment"));
	}else{
		$sheet->setCellValueByColumnAndRow(0,2, $langs->trans("DEPARTAMENTO ".$aExtras['nombre']));
	}

	if($yesnoprice)
		$sheet->mergeCells('A2:K2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
	// ENCABEZADO
	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('B3',$langs->trans("Dateini"));
	$objPHPExcel->getActiveSheet()->setCellValue('B4',$langs->trans("Datefin"));


	$objPHPExcel->getActiveSheet()->setCellValue('C3',dol_print_date($aExtras['date_ini'],'daytext'));
	$objPHPExcel->getActiveSheet()->setCellValue('C4',dol_print_date($aExtras['date_fin'],'daytext'));

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

	//Cabecera de la hoja electronica
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("Nro"));
	$objPHPExcel->getActiveSheet()->setCellValue('B8',html_entity_decode($langs->trans("Code")));
	$objPHPExcel->getActiveSheet()->setCellValue('C8',$langs->trans("Fieldref_ext"));
	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("Label"));
	$objPHPExcel->getActiveSheet()->setCellValue('E8',html_entity_decode($langs->trans("Dateofadquisition")));
	$objPHPExcel->getActiveSheet()->setCellValue('F8',$langs->trans("Cost"));
	$objPHPExcel->getActiveSheet()->setCellValue('G8',$langs->trans("Property"));
	$objPHPExcel->getActiveSheet()->setCellValue('H8',html_entity_decode($langs->trans("Location")));
	$objPHPExcel->getActiveSheet()->setCellValue('I8',html_entity_decode($langs->trans("Dateofassignment")));
	$objPHPExcel->getActiveSheet()->setCellValue('J8',$langs->trans("Responsable"));
	$objPHPExcel->getActiveSheet()->setCellValue('K8',html_entity_decode($langs->trans("Been")));
	//valor actualizado
	$objPHPExcel->getActiveSheet()->setCellValue('L8',$langs->trans("Valoract"));
	//Depreciacion Acumulada
	$objPHPExcel->getActiveSheet()->setCellValue('M8',html_entity_decode($langs->trans("Depreciationacum")));
	//saldo
	$objPHPExcel->getActiveSheet()->setCellValue('N8',$langs->trans("Balance"));

	$objPHPExcel->getActiveSheet()->setCellValue('O8',$langs->trans("State"));


 	// TABLA COLOR

	$objPHPExcel->getActiveSheet()->getStyle('A8:O8')->applyFromArray(
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

		// Numero
	$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
	// fecha de adquisiciomn
	$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

	// FECHA DE ASIGNACION
	$objPHPExcel->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	// costo
	$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// valor actualizado
	$objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// depreciacion acumulado
	$objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// balance
	$objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

 	// CUERPO
	$j=9;
	$contt=1;
	foreach ((array) $aReportdetasset AS $i => $lines)
	{

		$Estado = $lines['Estado'];

		$cStatus="";
		switch ($Estado) {
			case -1:
			$cStatus="todos";
			break;
			case 0:
			$cStatus="Pendiente";
			break;
			case 2:
			$cStatus="Asignado";
			break;
			case 4:
			$cStatus="En Ejecucion";
			break;
		}
		if(empty($lines['Codigo'])){
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$j,$lines['Etiqueta']);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);
			$sheet->mergeCells('B'.$j.':D'.$j);
			$sheet->getStyle('B'.$j)->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
		}else{
				// VISTA
			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
			->setCellValue('B' .$j,$lines['Codigo'])
			->setCellValue('C' .$j,$lines['Codigoext'])
			->setCellValue('D' .$j,$lines['Etiqueta'])
			->setCellValue('E' .$j,dol_print_date($lines['FechaAdquisicion'],'day'))
			->setCellValue('F' .$j,$lines['costo'])
			->setCellValue('G' .$j,$lines['Inmueble'])
			->setCellValue('H' .$j,$lines['location'])
			->setCellValue('I' .$j,dol_print_date($lines['FechaAsignacion'],'day'))
			->setCellValue('J'.$j,$lines['Responsable'])
			->setCellValue('K'.$j,$lines['Condicion'])
			->setCellValue('L'.$j,$lines['Valoract'])
			->setCellValue('M'.$j,$lines['Depreacum'])
			->setCellValue('N'.$j,$lines['Balance'])
			->setCellValue('O' .$j,$cStatus);
			$contt++;
		}

	// BORDES DE LA VISTA
		$objPHPExcel->getActiveSheet()->getStyle('A8:O'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$j++;


	}

	$objPHPExcel->setActiveSheetIndex(0);
 // Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/reportDepartament.xlsx");
	header("Location: ".DOL_URL_ROOT.'/assets/report/fiche_export.php?archive=reportDepartament.xlsx');
	//echo "Llega esta en el foreach";exit;
}



$formfile = new Formfile($db);
$form = new Formv($db);
//$aArrjs = array('almacen/javascript/recargar.js');
//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';

		//llxHeader("",$langs->trans("Inventario"),$help_url,'','','',$aArrjs,$aArrcss);
llxHeader("",$langs->trans("Bydepartament"),$help_url,'','','',$aArrjs,$aArrcss);


print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
dol_fiche_head($head, 'card', $langs->trans("Bydepartament"), 0, DOL_URL_ROOT.'/assets/img/assets',1);
print '<table class="border" width="100%">';
// Entrepot Almacen
print '<tr>';
print '<td width="25%" class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="3">';
$options = '<option value="0">'.$langs->trans('All').'</option>';
if($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER == 0)
{
	$filter = " AND t.statut >= 0";
	$res = $objAssets->fetchAll('ASC','departament_name',0,0,array(1=>1),'AND',$filter);
	if ($res> 0)
	{
		$lines = $objAssets->lines;
		foreach ($lines AS $j => $line)
		{
			$aDepart[$line->departament_name] = $line->departament_name;
		}
		if (count($aDepart)>0)
		{
			foreach ($aDepart AS $departament=>$value)
			{
				$selected='';
				if ($fk_departament == $departament) $selected = ' selected';
				$options.= '<option value="'.$departament.'" '.$selected.'>'.$departament.'</option>';
			}
		}
	}
}
else
{
	$nRdep = $objDepartament->fetchAll("ASC","rowid",0,0,array(1=>1),"AND",$filterstatic);

	if ($nRdep > 0)
	{
		$lines = $objDepartament->lines;
		foreach ($lines  AS $J => $line)
		{
			$selected = '';
			if ($fk_departament == $line->id) $selected = ' selected';
			$options .= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
		}
	}
}
print '<select name="fk_departament">'.$options.'</select>';
print '</td>';
print '</tr>';

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

print '</table>';
dol_fiche_end();
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

if(($action == 'edit'||  $action=='edits'))
{

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


	$filter = '';

	if($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER == 1)
	{
		//departamentos unicos
		$nRd = $objDepartament->fetchAll("ASC","rowid",0,0,array("active"=>1),"AND");
		//$res = $objGroup->fetchAll('ASC','code',0,0,array('active'=>1),'AND');
		if ($nRd > 0)
		{
			foreach ($objDepartament->lines AS $j => $line){
				$aDepart[$line->id] = $line->label;
			}
		}
	}
	//if ($fk_group > 0)
	if (!empty($fk_departament))
	{
		if($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER == 0)
		{
			$filter = " AND t.departament_name LIKE '%".$fk_departament."%'";
		}
		else
		{
			$res = $objDepartament->fetch($fk_departament);
			if ($res > 0) $filter = " AND t.fk_departament = '".$objDepartament->id."'";
		}
	}

	$filter.= " AND t.date_adq BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_fin)."'";
	$res = $objAssets->fetchAll("ASC","t.fk_departament,t.ref_ext",0,0,array(1=>1),"AND",$filter);

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
			//$aGroupasset[$line->type_group][$line->ref] = $line;
			if($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER == 0)
				$aGroupasset[$line->departament_name][$line->ref] = $line;
			else
				$aGroupasset[$line->fk_departament][$line->ref] = $line;
		}


		ksort($aGroupasset);
		if ($res>0)
		{
			print_barre_liste($langs->trans("Estate of assets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
			print '<table class="border" width="100%">';
			print '<tr class="liste_titre">';
			print '<td align="center">'.$langs->trans("Nro").'</td>';
			print '<td align="center">'.$langs->trans("Code").'</td>';
			print '<td align="center">'.$langs->trans("Fieldref_ext").'</td>';
			print '<td align="center">'.$langs->trans("Label").'</td>';
			print '<td align="center">'.$langs->trans("Fielddate_adq").'</td>';
			print '<td align="left">'.$langs->trans("Fieldcoste").'</td>';
			print '<td align="center">'.$langs->trans("Property").'</td>';
			print '<td align="center">'.$langs->trans("Location").'</td>';
			print '<td align="center">'.$langs->trans("Dateassignment").'</td>';
			print '<td align="center">'.$langs->trans("Responsible").'</td>';
			print '<td align="center">'.$langs->trans("Been").'</td>';
			print '<td  align="center">'.$langs->trans("ValorAct").'</td>';
			print '<td  align="center">'.$langs->trans("DepreciationAcum").'</td>';
			print '<td  align="center">'.$langs->trans("Balance").'</td>';
			print '<td align="center">'.$langs->trans("Status").'</td>';
			print '</tr>';
			$Cont=0;
			foreach ($aGroupasset AS $fk_dep => $obj)
			{

				$var = !$var;
				print '<tr '.$bc[$var].'>';
				//print '<td colspan="11">'.$aGroup[$type_group].'</td>';;
				print '<td colspan="11">'.$aDepart[$fk_dep].'</td>';
				print '</tr>';
				$aReportasset[]=array('Codigo'=>"",'Codigoext'=>"",'Etiqueta'=>$aDepart[$fk_dep],'FechaAdquisicion'=>"",'costo'=>"",'Inmueble'=>"",'location'=>"",'FechaAsignacion'=>"",'Responsable'=>"",'Condicion'=>"",'Estado'=>"",'type'=>"TT");
				foreach ($obj AS $ref => $line)
				{
					$var = !$var;
					$objAssets->statut = $line->statut;
					$objAssets->id = $line->id;
					$objAssets->ref = $line->ref;
					$objAssets->label = $line->descrip;
					$Cont+=1;

					$objBalance = new Assetsbalanceext($db);
					$resb2=$objBalance->fetch(0,$line->id);
					$Asst1 ='';
					$objuser->fetch($obj->fk_user);
					//$resb1 = $objuser->fetch($obj->fk_user);
					$Asst1 = $line->ref;
					$Asst2 = $line->descrip;
					$Asst3 = $line->date_adq;

					print "<tr $bc[$var]>";
					print '<td>'.$Cont.'</td>';
					$objAssets->ref = $line->ref;
					$refaux=$line->ref;
					print '<td>'.$objAssets->getNomUrl().'</td>';
					$objAssets->ref = $line->ref_ext;
					$refaux=$line->ref_ext;
					print '<td>'.$objAssets->getNomUrl().'</td>';
					print '<td>'.$line->descrip.'</td>';
					// fecha de adquisicion
					print '<td>'.dol_print_date($line->date_adq,'day').'</td>';
					//print '<td>'.$been.'</td>';
					print '<td align="right">'.price($line->coste).'</td>';
					$objMproperty =new Mproperty($db);
					$objMproperty->fetch($line->fk_property);
					$objMlocation =new Mlocation($db);
					$objMlocation->fetch($line->id);
					//assignment
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
					print '<td>'.$objMproperty->label.'</td>';
					print '<td>'.$objMlocation->detail.'</td>';

					$responsible = '';
					$departament_name='';
					if($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER == 0)
					{
						print '<td align="center">'.$langs->trans('Nodefined').'</td>';
						// responsable
						print '<td align="left">'.$line->resp_name.'</td>';
						$responsible = $line->resp_name;
					}
					else
					{

						//assignment
						$res = $objAssdet->fetch_ult($line->id);
						if ($res >0)
						{
							$objAss->fetch($objAssdet->fk_asset_assignment);
							$objAdherent->fetch($objAss->fk_user);
							print '<td align="center">'.dol_print_date($objAss->date_assignment,'day').'</td>';
							print '<td align="center">'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
							//$nResponsable = $objAdherent->lastname.' '.$objAdherent->firstname;
							$responsible = $objAdherent->lastname.' '.$objAdherent->firstname;
							$dFechaAsignacion = $objAss->date_assignment;
						}
						else
						{
							print '<td align="center">'.'</td>';
							print '<td align="center">'.'</td>';
							$responsible = "";
							$dFechaAsignacion = "";
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

					print '<td align="right" nowrap>'.$objAssets->getLibStatut(4).'</td>';
					$statut = $objAssets->getLibStatut(0);
					print '</tr>';

					$aReportasset[]=array('Codigo'=>$line->ref,'Codigoext'=>$line->ref_ext,'Etiqueta'=>$line->descrip,'FechaAdquisicion'=>$line->date_adq,'costo'=>$line->coste,'Inmueble'=>$objMproperty->label,'location'=>$objMlocation->detail,'FechaAsignacion'=>$dFechaAsignacion,'Responsable'=>$responsible,'Condicion'=>($line->been?$objbeen->label:''),'Valoract'=>$cValoract,'Depreacum'=>$cDepreacum,'Balance'=>$cBalance,'Estado'=>$statut,'type'=>"NN");


					$i++;
				}
			}
			print '</table>';


			$_SESSION['aReportassetdet'] = serialize($aReportasset);


			print "<div class=\"tabsAction\">\n";
			print '<a class="butAction"  href="'.$_SERVER['PHP_SELF'].'?action=">'.$langs->trans("Volver").'</a>';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';

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
			}

			print '<div class="tabsAction">';
		//documents
			print '<table width="100%"><tr><td width="50%" valign="top">';
			print '<a name="builddoc"></a>';
				// ancre

			$diradd = '';

		if (!empty($fk_departament))
		{
			if ($conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
			{
			$objDepartament->fetch($fk_departament);
			if ($objDepartament->id == $fk_departament){
				//$filename=dol_sanitizeFileName($objDepartament->ref."-".$objDepartament->label).$diradd;
				//$filename=dol_sanitizeFileName($objDepartament->ref).$diradd;
				//$filename=dol_sanitizeFileName($objDepartament->ref);
				$filename=$objDepartament->ref;
				$filename='departament';
			$cNombre=$objDepartament->label;
			}
			}
			else
			{
				$filename = $fk_departament;
				$cNombre = $fk_departament;
			}
		}else{
			$filename = 'allDepartament';
			$nombre ="";
			$cNombre = $langs->trans('Everybody');
		}


		$filedir=$conf->assets->dir_output."/".$filename;

		//echo "</br>Dir de FileDir : ".$filedir;

		$date_ini = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$date_fin = dol_mktime(23,59,59,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
		//echo "FECHA INI : ".$date_ini." / FECHA FIN : ".$date_fin;
		//Array extra
		$aExtras = array("filename"=>$filename,"filedir"=>$filedir,"nombre"=>$cNombre,"date_ini"=>$date_ini,"date_fin"=>$date_fin);
		$_SESSION['aExtras'] = serialize($aExtras);

		$modelpdf = 'departamentasset';


		$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result < 0) dol_print_error($db,$result);

		$urlsource=$_SERVER['PHP_SELF'];
		$filename = 'departament';
		$filedir = $conf->assets->dir_output.'/'.$filename;
		$genallowed=0;
		$delallowed=0;

		print '<br>';
		print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
		$somethingshown=$formfile->numoffiles;
		print '</td></tr></table>';
		print "</div>";

	}

}
$db->close();

llxFooter();
?>
