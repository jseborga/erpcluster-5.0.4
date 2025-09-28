<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/report/fiche.php
 *	\ingroup    Report
 *	\brief      Page fiche mant reports
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/mwcts/class/mwcts.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestuser.class.php';
require_once(DOL_DOCUMENT_ROOT."/mant/class/mworkrequestext.class.php");

require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';

// Excel
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





$langs->load("mant");
$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");

$action=GETPOST('action');

$id        = GETPOST("id");
$idu       = GETPOST("idu");
$ref       = GETPOST('ref','alpha');
$confirm = GETPOST('confirm');
$cancel = GETPOST('cancel');
$fk_equipment = GETPOST('fk_equipment');
$fk_property = GETPOST('fk_property');
//$action=GETPOST('action');
$date_ini = dol_mktime(0, 0, 1, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin = dol_mktime(12, 59, 59, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
if (empty($date_ini)) $date_ini = dol_now();
if (empty($date_fin)) $date_fin = dol_now();

$stype = GETPOST('stype');
$mesg = '';

//$object = new Mworkrequestext($db);
$object = new Mjobsext($db);
$objtmp = new Mworkrequestext($db);
$objmwork = new Mworkrequestext($db);
$objAdherent = new Adherent($db);
$objSoc = new Societe($db);
$objUser = new User($db);
$objContact = new Contact($db);
$objJobscontact = new Mjobscontact($db);
$objJobsuser = new Mjobsuser($db);

$objReqcont  = new Mworkrequestcontact($db);
$objRequser  = new Mworkrequestuser($db);

//equip
$objEquipment= new Mequipmentext($db);
// repair typo de servicio
$objTyperepair = new Mtyperepair($db);
// location
$objLocation = new Mlocation($db);
//propert
$objProperty = new Mproperty($db);


$form=new Form($db);
$limit = $conf->liste_limit;
$offset = $limit * $page;

//print_r($_POST);

// Actions

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

	$aReportdet = unserialize($_SESSION['aReportmantdet']);
	$date_ini = unserialize($_SESSION['date_inidet']);
	$date_fin = unserialize($_SESSION['date_findet']);
	$fk_equipment = unserialize($_SESSION['fk_equipment']);
	$fk_property = unserialize($_SESSION['fk_property']);


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
	$sheet->mergeCells('A2:I2');
	$sheet->setCellValueByColumnAndRow(0,2, "R E P O R T    M A N T E N I M I E N T O");

	if($yesnoprice)
		$sheet->mergeCells('A2:I2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
	// ENCABEZADO
	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('B3',$langs->trans("Dateini"));
	$objPHPExcel->getActiveSheet()->setCellValue('B4',$langs->trans("Datefin"));





	if($fk_equipment>0)
	{
		$objEquipment= new Mequipment($db);
		$objEquipment->fetch($fk_equipment);
		$cEquipo=$objEquipment->ref;
		$objPHPExcel->getActiveSheet()->setCellValue('B5',$langs->trans("Equipment"));
		$objPHPExcel->getActiveSheet()->setCellValue('C5',$cEquipo);
	}
	if($fk_property>0)
	{
		$objProperty = new Mproperty($db);
		$objProperty->fetch($fk_property);
		//$cProperty=$objProperty->label;
		$objPHPExcel->getActiveSheet()->setCellValue('B6',$langs->trans("Property"));
		$objPHPExcel->getActiveSheet()->setCellValue('C6',$objProperty->label);

	}



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

// TABLA

/*
print_fiche_titre($langs->trans("Reporte Mantenimiento"));
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print_liste_field_titre($langs->trans("Nro"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Date"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Job orders"),'','','','','align="center"');
			// Detail problem
			print_liste_field_titre($langs->trans("Trabajo Solicitado "),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Solicitante"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Dateini"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Datefin"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Trabajo Realizdo"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("status"),'','','','','align="center"');


*/
// Numero correlativo
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("Nro"));
// Ref
	$objPHPExcel->getActiveSheet()->setCellValue('B8',$langs->trans("Ref"));
// referencia
	$objPHPExcel->getActiveSheet()->setCellValue('C8',$langs->trans("Date"));
// Trabajo solicitado
	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("Trabajo Solicitado"));
// fecha
	$objPHPExcel->getActiveSheet()->setCellValue('E8',$langs->trans("Solicitante"));
// Inmueble
	$objPHPExcel->getActiveSheet()->setCellValue('F8',$langs->trans("Dateini"));
// localizcion
	$objPHPExcel->getActiveSheet()->setCellValue('G8',$langs->trans("Datefin"));
//	trabajo realizado
	$objPHPExcel->getActiveSheet()->setCellValue('H8',$langs->trans("Trabajo Realizado"));
// Problema
	$objPHPExcel->getActiveSheet()->setCellValue('I8',$langs->trans("Status"));



// TABLA COLOR

	$objPHPExcel->getActiveSheet()->getStyle('A8:I8')->applyFromArray(
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

//formato de las columnas Numero Fechas
	$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
	$objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	$objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('R')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

// CUERPO
	$j=9;
	$contt=1;
	foreach ((array) $aReportdet AS $i => $lines)
	{

		$ref = $lines['ref'];
		$date = $lines['date'];
		$Problema = $lines['Problema'];
		$Usuario = $lines['Usuario'];
		$fechaini = $lines['fechaini'];
		$fechafin = $lines['fechafin'];
		$trabajore = $lines['trabajore'];
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
	// VISTA
		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
		->setCellValue('B' .$j,$ref)
		->setCellValue('C' .$j,dol_print_date($date,'day'))
		->setCellValue('D' .$j,$Problema)
		->setCellValue('E' .$j,$Usuario)
		->setCellValue('F' .$j,dol_print_date($fechaini,'day'))
		->setCellValue('G' .$j,dol_print_date($fechafin,'day'))
		->setCellValue('H' .$j,$trabajore)
		->setCellValue('I' .$j,$cStatus);

	// BORDES DE LA VISTA
		$objPHPExcel->getActiveSheet()->getStyle('A8:I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$j++;
		$contt++;
	}

	$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/reportmant.xlsx");
	header("Location: ".DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=reportmant.xlsx');
}

// Add
if ($action == 'report' && $user->rights->mant->rep->leer)
{
	$date_ini = dol_mktime(0, 0, 1, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin = dol_mktime(12, 59, 59, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	//$level = GETPOST('level');

	//$fk_equipment = GETPOST ('fk_equipment');
	//$location = GETPOST('location')


	if ($date_fin < $date_ini)
	{
		$mesg='<div class="error">'.$langs->trans("Errortheenddatecannotbegreaterthanstartdate").'</div>';
		$action="create";
	 // Force retour sur page creation
	}
	elseif (empty($date_fin) || empty($date_ini))
	{
		$mesg='<div class="error">'.$langs->trans("Errorisnecessarydates").'</div>';
		$action="create";
	 // Force retour sur page creation
	}
}

$alevel = array(0=>'Todos',
	2=>'Validados',
	3=>'Programados',
	4=>'Concluidos',
	5=>'Validados,Programados,Concluidos');


/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Managementjobs"),$help_url);

if ($user->rights->mant->rep->leer &&($action == 'create' || empty($action)))
{

	if (empty($stype)) $stype = 0;
	//verificamos si biene de work request
	if (!empty($idw))
	{
		$result = $objwork->fetch($idw);
		if ($result > 0 && empty($tmparray))
		{
			$object->internal    = $objwork->internal;
			$object->fk_member   = $objwork->fk_member;
			$object->fk_property = $objwork->fk_property;
			$object->fk_location = $objwork->fk_location;
			$object->speciality  = $objwork->speciality_prog;
			$object->detail_problem = $objwork->detail_problem;
			$object->email       = $objwork->email;
			$object->fk_soc      = $objwork->fk_soc;
		}
	}
	print_fiche_titre($langs->trans("Mantenimiento"));
	if (empty($object->ref)) $object->ref = '(PROV)';

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		is_stype='.$stype.';
		if (is_stype) {
			$(".equipmentline").show();
			$(".propertyline").hide();
		} else {
			$(".propertyline").show();
			$(".equipmentline").hide();
		}
		$("#stype").change(function() {
			document.form_index.action.value="create";
			document.form_index.submit();
		});
	});';
	print '</script>'."\n";

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="report">';
	print '<input type="hidden" name="idw" value="'.$idw.'">';
	print '<input type="hidden" name="fk_soc" value="'.$object->fk_soc.'">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref numeracion automatica del ticket


	//print "<form action=\"reportodt.php\" method=\"post\">\n";
	//print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	//print '<input type="hidden" name="action" value="report">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// date ini
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($date_ini,'di_','','','',"dateini",1,1);
	print '</td></tr>';
	// date fin
	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	$form->select_date($date_fin,'df_','','','',"datefin",1,1);
	print '</td></tr>';


	//selector de equipo o inmueble
	$aSelect = array(0=>$langs->trans('Property'),1=>$langs->trans('Equipment'));
	print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Mantenimiento').'</td><td colspan="2">';
	print $form->selectarray('stype',$aSelect,$stype);
	print '</td></tr>';
	//equipment
	print '<tr class="equipmentline"><td  class="fieldrequired" width="20%">'.$langs->trans('Equipment').'</td><td colspan="2">';
	$res = $objEquipment->fetchAll('ASC', 'label',0,0,array('entity'=>$conf->entity,'status'=>1),'AND','',false);
	$options = '<option>'.$langs->trans('Allequipment').'</option>';
	if($res>0)
	{
		foreach($objEquipment->lines AS $j => $line)
		{
			$selected = '';
			if(GETPOST('fk_equipment') == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
		}
	}
	print '<select id="fk_equipment" name="fk_equipment">'.$options.'</select>';
	print '</td></tr>';

	if ($fk_equipment>0)
	{
		$objEquipment->fetch($fk_equipment);
		$fk_location = $objEquipment->fk_location;
		$_GET['fk_location'] = $fk_location;
		if ($fk_location>0)
		{
			$objLocation->fetch($fk_location);
			$fk_property = $objLocation->fk_property;
			$_GET['fk_property'] = $fk_property;
		}
	}
	// property
	$fk_property = GETPOST('fk_property');
	print '<tr class="propertyline"><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
	if (!empty($idw))
	{
		if ($object->fk_property)
		{
			if ($objProperty->fetch($object->fk_property) > 0)
				print $objProperty->ref;
		}
		print '<input type="hidden" name="fk_property" value="'.$object->fk_property.'">';
	}
	else
	{
		$filter = " AND t.entity = ".$conf->entity;
		$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
		$options = '<option value="-1">'.$langs->trans('Allproperty').'</option>';
		$lines =$objProperty->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if ($fk_property == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
		}
		print '<select id="fk_property" name="fk_property">'.$options.'</select>';
		 //print $objProperty->select_property($fk_property,'fk_property','',40,1);
	}

	print '</td></tr>';
	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';
	print '</form>';


}
else
{
	if ($date_ini && $date_fin)
	{

		$aDateini = dol_getdate($date_ini);
		$aDate = dol_get_prev_day($aDateini['mday'], $aDateini['mon'], $aDateini['year']);
		$date_ini = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year']);
		$aDatefin = dol_getdate($date_fin);
		$date_fin = $adatefin['year'].'-'.$adatefin['mon'].'-'.$adatefin['mday'];
		$date_fin .= ' 23:59:59';
		$date_fin = dol_mktime(23,59,59,$aDatefin['mon'],$aDatefin['mday'],$aDatefin['year']);
		dol_htmloutput_mesg($mesg);


		if ($action == 'report')
		{

			$$_SESSION['date_iniot'] = $date_ini;
			$_SESSION['date_finot'] = $date_fin;
			$_SESSION['levelot'] = $level;
			$form=new Form($db);


			$sql = "SELECT p.rowid, p.fk_work_request, p.ref, p.fk_member, p.detail_problem, p.email, p.date_create, p.fk_soc, p.speciality, p.status,p.fk_property,p.fk_location,p.fk_equipment,p.date_ini,p.date_fin,p.description_job";
			//$sql.= " FROM ".MAIN_DB_PREFIX."m_work_request as p";
			$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as p";
			$sql.= " WHERE p.entity = ".$conf->entity;
			$sql.= " AND p.date_create BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_fin)."'";
			if (empty($stype))
			{
				$sql.= " AND p.fk_equipment = 0";
				if($fk_property>0){
					$sql.= " AND p.fk_property = ".$fk_property;
				}
			}
			else
			{
				$sql.= " AND p.fk_equipment > 0";
				if($fk_equipment>0)
					$sql.= " AND p.fk_equipment = ".$fk_equipment;
			}

			$result = $db->query($sql);
			$form=new Form($db);


			$i = 0;
			if ($result)
			{

				$num = $db->num_rows($result);
				$nLoop = 0;

				print_fiche_titre($langs->trans("Reporte Mantenimiento"));
				print '<table class="noborder" width="100%">';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Nro"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Ref"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Date"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Trabajo Solicitado "),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Solicitante"),'','','','','align="left"');
				print_liste_field_titre($langs->trans("Dateini"),'','','','','align="left"');
				print_liste_field_titre($langs->trans("Datefin"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Trabajo Realizado"),'','','','','align="left"');
				print_liste_field_titre($langs->trans("status"),'','','','','align="center"');
				print '</tr>';
				if ($num)
				{
					$var=True;
					while ($i < min($num,$limit))
					{

						$lPrint = true;
						$objp = $db->fetch_object($result);

						//$objAdherent->fetch($objp->fk_member);

						$object->id = $objp->rowid;
						$object->ref = $objp->ref;
						$object->status = $objp->status;
						$htmlc = '';
						$htmlsoc = '';


						$lPrint = true;
						if ($lPrint)
						{
							$nLoop++;
							$var=!$var;

							print "<tr $bc[$var]>";
							//NUmeracion
							print '<td align="center">'.$nLoop.'</td>';

								// referencia
							print '<td align="center">'.$object->getNomUrl().'</td>';


							//fecha
							print '<td align="center">'.dol_print_date($objp->date_create,'day').'</td>';


							// trabajo solicitado
							print '<td align="center">'.$objp->detail_problem.'</td>';

							// buscamos solicitante
							$objAdherent->fetch($objp->fk_member);
							if ($objAdherent->id == $objp->fk_member)
								print '<td>'.$objAdherent->getNomUrl(1).'-'.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
							else
								print '<td>'.$objp->email.'</td>';

							//fecha inicial
							print '<td align="center">'.dol_print_date($objp->date_ini,'day').'</td>';

							//fecha final
							print '<td align="center">'.dol_print_date($objp->date_fin,'day').'</td>';

							// trabajo realizado
							print '<td align="left">'.$objp->description_job.'</td>';

							//Estado
							print '<td align="center">'.$object->LibStatut($objp->status,6).'</td>';
							print '</tr>';
						}

						$i++;
						$aReportmant[]=array('ref'=>$objp->ref,'date'=>$objp->date_create,'Problema'=>$objp->detail_problem,'Usuario'=>$objAdherent->lastname.' '.$objAdherent->firstname,'fechaini'=>$objp->date_ini,'fechafin'=>$objp->date_fin,'trabajore'=>$objp->description_job,'Estado'=>$objp->status);
					}
				}
				$db->free($result);
				print "</table>";
				$_SESSION['aReportmantdet'] = serialize($aReportmant);
				$_SESSION['date_inidet'] = serialize($date_ini);
				$_SESSION['date_findet'] = serialize($date_fin);
				$_SESSION['fk_equipment'] = serialize($fk_equipment);
				$_SESSION['fk_property'] = serialize($fk_property);

				print "<div class=\"tabsAction\">\n";
				print '<a class="butAction"  href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Volver").'</a>';
				//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_property='.$fk_property.'&fk_equipment='.$fk_equipment.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';

				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';

				print '</div>';

			}

		}
	}
}


llxFooter();

$db->close();
?>
