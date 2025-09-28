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
 * along with this program. if not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/salary/report/rplanilla.php
 *	\ingroup    Planilla
 *	\brief      Page fiche salary planilla
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");


//require_once DOL_DOCUMENT_ROOT.'/core/class/html.objAssetsformorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfield.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictable.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryaprob.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent_type.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/formula/lib/formula.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//require_once DOL_DOCUMENT_ROOT.'/salary/core/modules/modules_salary.php';


require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';






$langs->load("salary@salary");
//$action=GETPOST('action');
$action 	= GETPOST('action','alpha');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$lCopy = false;
$mesg = '';

//$object   = new Pperiod($db); //periodos
$object   = new Pformulas($db); //formula
$objectpe = new Pperiodext($db); //periodos
$objectpr = new Pproces($db); //procesos
$objecttf = new Ptypefolext($db);//procedimientos
$objectsp = new Psalarypresentext($db); //salario actual
$objectsh = new Psalaryhistoryext($db); //salario history
$objectf  = new Pformulas($db); //formula
$objecto  = new Poperator($db);
$objectU  = new Puserext($db);
$objectCo = new Pcontractext($db); //contract
$objectUb = new Puserbonus($db);
$objectUs = new User($db);
$objectAd = new Adherent($db); //Adherent
$objectCh = new Pcharge($db); //charge
$objectgf = new Pgenericfield($db); //generic field
$objectgt = new Pgenerictable($db); //generic table
$objectsa = new Psalaryaprob($db); //salary approver


$formfile = new Formfile($db);
$form = new Formv($db);

//$array=array();

//$objhistory = new Psalaryhistory($db);
//determina el pais
$aPais = explode(":",$conf->global->MAIN_INFO_SOCIETE_COUNTRY);

$cPais = $aPais[1];
$_SESSION['param']['nDiasTrab'] = $conf->global->SALARY_NRO_DIAS_LABORAL;

$fk_period = GETPOST('fk_period');

/*
 * Actions
 */

// Add
if ($action == 'proces' && $user->rights->salary->crearrsal)
{
	$fk_period = GETPOST('fk_period');
	if (empty($fk_period) || $fk_period <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Period")), null, 'errors');
		$action = 'create';
	}
	if (!$error)
	{
		//recuperamos los valores configurados en period
		$result = $objectpe->fetch($fk_period);
		if ($result)
		{
			$fk_type_fol = $objectpe->fk_type_fol;
			$fk_proces   = $objectpe->fk_proces;
		}
		s_cargamie();
		header("Location: ".$_SERVER['PHP_SELF']."?action=edit&fk_period=".$fk_period);
		exit;
	}
}

if ($action == 'builddoc')
		// En get ou en post
{
	//$res = $objGroup->fetch($fk_group);
	$result = $objectpe->fetch($fk_period);


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
	$objectpe->modelpdf = GETPOST('model');

	$result=salary_pdf_create($db, $objectpe, $objectpe->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);


	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		//header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=crate');
		exit;
	}
}

//action generalte
if ($action == 'generate' && $user->rights->salary->validsal)
{
	$fk_period   = $_SESSION['validateSalary']['fk_period'];
	$fk_proces   = $_SESSION['validateSalary']['fk_proces'];
	$fk_type_fol = $_SESSION['validateSalary']['fk_type_fol'];
	$state = GETPOST('state');
	$newState = $state - 1;
	if ($_SESSION['member_aprob'])
	{
		//verificamos el estado
		$sql = "SELECT state FROM ".MAIN_DB_PREFIX."p_salary_present WHERE ";
		$sql.= " fk_period = ".$fk_period." AND ";
		$sql.= " fk_proces = ".$fk_proces." AND ";
		$sql.= " fk_type_fol = ".$fk_type_fol." AND ";
		$sql.= " state = ".$newState;
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$obj = $db->fetch_object($resql);
			if ($num > 0)
			{
				//actualizamos el state
				$sql = " UPDATE ".MAIN_DB_PREFIX."p_salary_present ";
				$sql.= " SET state = ".$state;
				$sql.= " WHERE ";
				$sql.= " fk_period = ".$fk_period." AND ";
				$sql.= " fk_proces = ".$fk_proces." AND ";
				$sql.= " fk_type_fol = ".$fk_type_fol;
				$result = $db->query($sql);
				$_SESSION['validateSalary'] = array();
				$_SESSION['member_aprob'] = false;
				if ($_SESSION['aprob_final'] == true)
				{
					$lOk = registry_end($fk_period,$fk_proces,$fk_type_fol,$state);
					echo '<hr>lok '.$lOk;
					if ($lOk)
					{
						$objectpe->fetch($fk_period);
						if ($objectpe->id == $fk_period)
						{
							//$objectpe->ref = $objectpe->codref;
							$objectpe->date_close = dol_now();
							$objectpe->state = 5;
							$res = $objectpe->update($user);
				//eliminamos informacion de llx_p_salary_present
							$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_salary_present ";
							$sql.= " WHERE fk_period = ".$fk_period;
							$sql.= " AND fk_proces = ".$fk_proces;
							$sql.= " AND fk_type_fol = ".$fk_type_fol;
							$result = $db->query($sql);
						}
					}
				}
				else
				{
					header("Location: rplanilla.php?action=edit&fk_period=".$fk_period);
					exit;
				}
			}
		}
	}
	//recuperamos los valores configurados en period
	$result = $objectpe->fetch($fk_period);
	if ($result)
	{
		$fk_type_fol = $objectpe->fk_type_fol;
		$fk_proces   = $objectpe->fk_proces;
	}
	s_cargamie();
	header("Location: rap.php?action=edit&fk_period=".$fk_period);
	exit;

}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}


// ARMANDO EXCEL

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

	$aReportsalaryrap = unserialize($_SESSION['aReportsalaryrapdet']);
	$fk_period = GETPOST('fk_period');
	//$date_ini = unserialize($_SESSION['date_inidet']);
	//$date_fin = unserialize($_SESSION['date_findet']);

	//echo'<pre>';
	//print_r($aReportsalaryrap);
	//echo'</pre>';
	//exit;


	// TITULO
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);
	//$this->activeSheet->getDefaultRowDimension()->setRowHeight($height);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);


	// COLOR DEL TITULO
	$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->applyFromArray(
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
	$sheet->mergeCells('A2:N2');
	$sheet->setCellValueByColumnAndRow(0,2, $langs->trans('Reporte Aportes Patronales'));

	if($yesnoprice)
		$sheet->mergeCells('A2:N2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
	// ENCABEZADO
	$objectpe = new Pperiodext($db); //periodos
	$objectpe->fetch($fk_period);
	$ref=$objectpe->ref;
	$mes=$objectpe->mes;
	$anio=$objectpe->anio;

	$objectpr = new Pproces($db); //procesos
	$objectpr->fetch($objectpe->fk_proces);
	$cProceso=$objectpr->label;
	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('B3',$langs->trans("Period"));

	//$objPHPExcel->getActiveSheet()->setCellValue('B5',$langs->trans("Assets"));

	$objPHPExcel->getActiveSheet()->setCellValue('C3',$ref);


	$objPHPExcel->getActiveSheet()->setCellValue('B4',$langs->trans("Proceso"));
	$objPHPExcel->getActiveSheet()->setCellValue('C4',$objectpr->label);

	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);

	// COLOR DEL ENCABEZADO
	$objPHPExcel->getActiveSheet()->getStyle('A3:C7')->applyFromArray(
		array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '0c78bf'),
				'size'  => 12,
				'name'  => 'Verdana'
			)));



	// Numero correlativo
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("N"));
	// Cedula de identidad
	$objPHPExcel->getActiveSheet()->setCellValue('B8',$langs->trans("C_idoc"));
	// nombre y apellido
	$objPHPExcel->getActiveSheet()->setCellValue('C8',$langs->trans("Lastname").' '.$langs->trans('And').' '.$langs->trans('Name'));
	// nacionalidad
	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("Nationality"));
	// fecha de nacimiento
	$objPHPExcel->getActiveSheet()->setCellValue('E8',$langs->trans("Datenac"));
	// sexo
	$objPHPExcel->getActiveSheet()->setCellValue('F8',$langs->trans("Sexo"));
	// cargo
	$objPHPExcel->getActiveSheet()->setCellValue('G8',$langs->trans("Cargo"));
	// fecha inicial
	$objPHPExcel->getActiveSheet()->setCellValue('H8',$langs->trans("Dateini"));
	// total ingresos
	$objPHPExcel->getActiveSheet()->setCellValue('I8',html_entity_decode($langs->trans("Totalrend")));
	// FS
	$objPHPExcel->getActiveSheet()->setCellValue('J8',$langs->trans("FS"));
	// FONVIS
	$objPHPExcel->getActiveSheet()->setCellValue('K8',$langs->trans("FONVIS"));
	// CNS
	$objPHPExcel->getActiveSheet()->setCellValue('L8',html_entity_decode($langs->trans("CNS")));
	// AFP
	$objPHPExcel->getActiveSheet()->setCellValue('M8',$langs->trans("AFP"));
	// INDEMINIZACION
	$objPHPExcel->getActiveSheet()->setCellValue('N8',$langs->trans("Indeminizacion"));
	// AGUINALDO
	$objPHPExcel->getActiveSheet()->setCellValue('O8',$langs->trans("Aguinaldo"));
	// TOTAL APORTE
	$objPHPExcel->getActiveSheet()->setCellValue('P8',$langs->trans("Total Aporte"));


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
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	/*
	// Numero
	$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
	// fecha de adquisiciomn
	$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

	// FECHA DE ASIGNACION
	$objPHPExcel->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	*/

	// FECHA DE NACIMIENTO
	$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	// FECHA INICIAL
	$objPHPExcel->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

	// total ingreso
	$objPHPExcel->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// fs
	$objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// fonvis
	$objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// CNS
	$objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// AFP
	$objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// INDEMINIZACION
	$objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// AGUINALDO
	$objPHPExcel->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	// TOTAL APORTE
	$objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


	// totales
	$nTbalance=0;
	$nTfs=0;
	$nTfonvis=0;
	$nTcns=0;
	$nTafp=0;
	$nTind=0;
	$nTagui=0;
	$nTtap=0;


	// CUERPO
	$j=9;
	$contt=1;
	foreach ((array) $aReportsalaryrap AS $i => $lines)
	{

		$cSexo="";
		if($lines['Sexo']==2)
		{
			$cSexo="Mujer";
		}
		else
		{
			if($lines['Sexo']==1)
			{
				$cSexo="Hombre";
			}
		}


		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
		->setCellValue('B' .$j,$lines['Docum'])
		->setCellValue('C' .$j,$lines['Name'])
		->setCellValue('D' .$j,getCountry($lines['Nationality']))
		->setCellValue('E' .$j,dol_print_date($lines['Datenac'],'day'))
		->setCellValue('F' .$j,$cSexo)
		->setCellValue('G' .$j,$lines['Cargo'])
		->setCellValue('H' .$j,dol_print_date($lines['Dateini'],'day'))
		->setCellValue('I' .$j,$lines['Balance'])
		->setCellValue('J' .$j,$lines['FS'])
		->setCellValue('K' .$j,$lines['FONVIS'])
		->setCellValue('L' .$j,$lines['CNS'])
		->setCellValue('M' .$j,$lines['AFP'])
		->setCellValue('N' .$j,$lines['Indem'])
		->setCellValue('O' .$j,$lines['Aguinaldo'])
		->setCellValue('P' .$j,$lines['TAP']);
		$contt++;
		$nTbalance=$nTbalance+$lines['Balance'];
		$nTfs=$nTfs+$lines['FS'];
		$nTfonvis=$nTfonvis+$lines['FONVIS'];
		$nTcns=$nTcns+$lines['CNS'];
		$nTafp=$nTafp+$lines['AFP'];
		$nTind=$nTind+$lines['Indem'];
		$nTagui=$nTagui+$lines['Aguinaldo'];
		$nTtap=$nTtap+$lines['TAP'];

			// BORDES DE LA VISTA
		$objPHPExcel->getActiveSheet()->getStyle('A8:P'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$j++;
	}

	$objPHPExcel->getActiveSheet()->setCellValue('H' .$j,'TOTALES')
	->setCellValue('I' .$j,$nTbalance)
	->setCellValue('J' .$j,$nTfs)
	->setCellValue('K' .$j,$nTfonvis)
	->setCellValue('L' .$j,$nTcns)
	->setCellValue('M' .$j,$nTafp)
	->setCellValue('N' .$j,$nTind)
	->setCellValue('O' .$j,$nTagui)
	->setCellValue('P' .$j,$nTtap);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$j.':'.'P'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



	$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/reportrap.xlsx");
	header("Location: ".DOL_URL_ROOT.'/salary/report/fiche_export.php?archive=reportrap.xlsx');

}




/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);
//creando el proceso de elaboracion planilla
//formulario de configuracion
if ($action == 'create' && $user->rights->salary->crearrsal)
{
	$_SESSION['aPlanilla'] = array();

	print_fiche_titre($langs->trans("Salary sould"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="proces">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// period
	print '<tr><td class="fieldrequired">'.$langs->trans('Period').'</td><td colspan="2">';
	print $objectpe->select_period($fk_period,'fk_period','','',1);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["action"]=='edit')
	{
		dol_htmloutput_mesg($mesg);
	 //armando la planilla de sueldos
		$aPlanilla = $_SESSION['aPlanilla'];

		print_barre_liste($langs->trans("Planilla"), $page, "rplanilla.php", "", $sortfield, $sortorder,'',$num);

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("N."),"liste.php", "p.table_cod","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("C_idoc"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Lastname").' '.$langs->trans('And').' '.$langs->trans('Name'),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Nationality"),"liste.php", "p.sequen","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Datenac"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Sexo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Cargo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Dateini"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Totalrend"),"liste.php", "p.state","","","",$sortfield,$sortorder);

		print_liste_field_titre($langs->trans("FS"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("FONVIS"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("CNS"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("AFP"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Indeminizacion"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Aguinaldo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Total Aporte"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print "</tr>\n";
		$seq = 1;
	 //recuperamos el periodo
		$lPeriodClose = false;
		$result = $objectpe->fetch($fk_period);
		if ($result)
		{
			$fk_type_fol = $objectpe->fk_type_fol;
			$fk_proces   = $objectpe->fk_proces;
			$date_close  = $objectpe->date_close;
			if ($objectpe->state == 5 )
				$lPeriodClose = true;
		}
		if (!$lPeriodClose)
			$objectsp = new Psalarypresentext($db);
	   //salario actual sin aprobacion
		else
			$objectsp = new Psalaryhistoryext($db);
	   //salario history

	// suma totales
		$nTbalance=0;
		$nTfs=0;
		$nTfonvis=0;
		$nTcns=0;
		$nTafp=0;
		$nTind=0;
		$nTagui=0;
		$nTtap=0;

	//
		$nTotal=0;
		$nFs=0;
		$fonvis=0;
		$ncns=0;
		$nAfp=0;
		$nIndeminiza=0;
		$nAguinaldo=0;
		$nTap=0;


		foreach ((array) $aPlanilla AS $idUser => $dataUser)
		{
			$nTotalRend = 0;
			$nTotalDesc = 0;
			$nSumaAfp   = 0;
			$nSumaDesc  = 0;
			$objectAd->fetch($idUser);
			$objectU->fetch_user($idUser);
			$objectCo->fetch_vigent($idUser,1);
			$objectCh->fetch($objectCo->fk_charge);

			$state = $objres->state;
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>'.$seq.'</td>';
			if ($objectU->id == $idUser)
			{
				$docum = $objectU->docum;
				$lastnametwo = $objectU->lastnametwo;
			}
			else
			{
				$docum = '';
				$lastnametwo = '';
			}
			print '<td>'.$docum.'</td>';
			print '<td>'.$objectAd->lastname.' '.$lastnametwo.' '.$objectAd->firstname.'</td>';
			// Country
			print '<td>';
			$img=picto_from_langcode($objectAd->country_code);
			if ($img) print $img.' ';
			print getCountry($objectAd->country_code);
			print '</td>';
			print '<td>'.dol_print_date($objectAd->birth,'day').'</td>';
			print '<td>'.select_sex($objectU->sex,'sex','','',1,1).'</td>';
			if ($objectCh->id == $objectCo->fk_charge)
				print '<td>'.$objectCh->codref.'</td>';
			else
				print '<td>&nbsp;</td>';

			print '<td>'.dol_print_date($objectCo->date_ini,'day').'</td>';
			//total rendimiento
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'103');
			$nTotal=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//fs
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'721');
			$nFs=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//fonvis
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'722');
			$nFonvis=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//cns
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'723');
			$ncns=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//afp
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'724');
			$nAfp=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//indeminizacion
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'725');
			$nIndeminiza=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//aguinaldo
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'726');
			$nAguinaldo=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			//total aporte patronal
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'729');
			$nTap=$objectsp->amount;
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			print '</tr>';
			$seq++;

			if($nTotal>0)
			{
				$nTbalance=$nTbalance+$nTotal;
			}
			if($nFs>0)
			{
				$nTfs=$nTfs+$nFs;
			}
			if($nFonvis>0)
			{
				$nTfonvis=$nTfonvis+$nFonvis;
			}
			if($ncns>0)
			{
				$nTcns=$nTcns+$ncns;
			}
			if($nAfp>0)
			{
				$nTafp=$nTafp+$nAfp;
			}
			if($nIndeminiza>0)
			{
				$nTind=$nTind+$nIndeminiza;
			}
			if($nAguinaldo>0)
			{
				$nTagui=$nTagui+$nAguinaldo;
			}
			if($nTap>0)
			{
				$nTtap=$nTtap+$nTap;
			}

			$aReportsalaryrap[]=array('Docum'=>$docum,'Name'=>$objectAd->lastname.' '.$lastnametwo.' '.$objectAd->firstname,'Nationality'=>$objectAd->country_code,'Datenac'=>$objectAd->birth,'Sexo'=>$objectU->sex,'Cargo'=>$objectCh->codref,'Dateini'=>$objectCo->date_ini,'Balance'=>$nTotal,'FS'=>$nFs,'FONVIS'=>$nFonvis,'CNS'=>$ncns,'AFP'=>$nAfp,'Indem'=>$nIndeminiza,'Aguinaldo'=>$nAguinaldo,'TAP'=>$nTap);
		}
		print "<tr $bc[$var]>";
		print '<td>'."".'</td>';
		print '<td>'."".'</td>';
		print '<td>'."".'</td>';
		print '<td>'."".'</td>';
		print '<td>'."".'</td>';
		print '<td>'."".'</td>';
		print '<td>'."".'</td>';
		print '<td>'."TOTALES".'</td>';
		print '<td align="right">'.price($nTbalance).'</td>';
		print '<td align="right">'.price($nTfs).'</td>';
		print '<td align="right">'.price($nTfonvis).'</td>';
		print '<td align="right">'.price($nTcns).'</td>';
		print '<td align="right">'.price($nTafp).'</td>';
		print '<td align="right">'.price($nTind).'</td>';
		print '<td align="right">'.price($nTind).'</td>';
		print '<td align="right">'.price($nTtap).'</td>';
		print '</tr>';

	  //$db->free($result);
		print "</table>";

		$_SESSION['aReportsalaryrapdet'] = serialize($aReportsalaryrap);


		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction"  href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Volver").'</a>';
				//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_property='.$fk_property.'&fk_equipment='.$fk_equipment.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_period='.$fk_period.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';

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
			$model='salaryrap';
				//$ret = $objAssets->fetch($id);
				// Reload to get new records
				//$object->fetch_lines();



			$result=$objectsp->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);

			if ($result < 0) dol_print_error($db,$result);
		}




		/* **************************************** */
		/*                                          */
		/* Barre d'action                           */
		/*                                          */
		/* **************************************** */
		print '<div class="tabsAction">';
		if ($action == 'edit' && $lPeriodClose == false && $user->rights->salary->validsal)
		{
			$_SESSION['validateSalary'] = array('fk_period' => $fk_period,
				'fk_proces' => $fk_proces,
				'fk_type_fol' => $fk_type_fol);
			$lAprueba = false;
			$lAprobUlt = false;
			$nSeqAprob = 0;
			$nLoop = 0;

			$objectCo->fetch_vigent($user->fk_member,1);

			$aArrayAprob = $objectsa->getArrayAprob();

		  //contamos cuantos aprobadores son
			$nMaxAprob = count($aArrayAprob);
			$newState = $state;
		  //obtenemos quien aprueba en este estado
			$aData = $aArrayAprob[$newState];
		  //buscamos quien aprueba
			if ($aData['type'] == 1)
			{
				if ($user->fk_member == $aData['fk_value'])
				{
					$_SESSION['member_aprob'] = true;
					$lAprueba = true;
					$nSeqAprob = $newState+1;
				}
			}
			Elseif ($aData['type'] == 2)
			{
		  //buscamos quien aprueba
				if ($objectCo->fk_charge == $aData['fk_value'])
				{
					$_SESSION['member_aprob'] = true;
					$lAprueba = true;
					$nSeqAprob = $newState+1;
				}
			}
			if ($state == $nMaxAprob)
				$nSeqAprob = $nMaxAprob;
			if ($nSeqAprob == $nMaxAprob)
			{
				$lAprobUlt = true;
				$_SESSION['aprob_final'] = true;
			}
			if ($lAprueba || $user->admin == 1)
			{
				if ($user->admin == 1)
				{
					$_SESSION['member_aprob'] = true;
					$nSeqAprob = $state + 1;
					if ($nSeqAprob == $nMaxAprob)
					{
						$lAprobUlt = true;
						$_SESSION['aprob_final'] = true;
					}
				}
				if ($lAprobUlt == true)
					print '<a class="butAction" href="rplanilla.php?action=generate&state='.$nSeqAprob.'">'.$langs->trans("Approvefinal").'</a>';
				else
					print '<a class="butAction" href="rplanilla.php?action=generate&state='.$nSeqAprob.'">'.$langs->trans("Approve").'</a>';
			}
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Approve")."</a>";
		}
		print '</div>';


	}
}



print '<div class="tabsAction">';
			//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';
			// ancre

$diradd = '';
$filename = 'Period';

//$objectpe->fetch($fk_period);

//if ($fk_group>0)
//{
	//$objGroup->fetch($fk_group);
	//if ($objGroup->id == $fk_group)
		//$filename=dol_sanitizeFileName("ref").$diradd;
//}
			//cambiando de nombre al reporte
$filedir=$conf->salary->dir_output.'/'.$filename;
//echo '<hr>'.$conf->salary->dir_output;
//echo '<hr>'.$filedir;

$urlsource=$_SERVER['PHP_SELF'].'?fk_period='.$fk_period;
$genallowed=1;
$delallowed=1;


//$objGroup->modelpdf = 'fractalinventario';
$objectpe->modelpdf ='salaryrap';


print '<br>';
print $formfile->showdocuments('salary',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objectpe->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";


llxFooter();
$db->close();

?>
