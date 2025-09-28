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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/cregions.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefol.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfield.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictable.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryaprob.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/core/modules/salary/modules_salary.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent_type.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/formula/lib/formula.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

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



if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';

}
$langs->load("salary");


//$object   = new Pperiod($db); //periodos
$object   = new Pformulas($db); //formula
$objectpe = new Pperiodext($db); //periodos
$objectpr = new Pproces($db); //procesos
$objecttf = new Ptypefol($db);//procedimientos
$objectsp = new Psalarypresentext($db); //salario actual
$objectsh = new Psalaryhistoryext($db); //salario history
$objectf  = new Pformulas($db); //formula
$objecto  = new Poperator($db);
$objectU  = new Puserext($db);
$objectCo = new Pcontractext($db); //contract
$objectUb = new Puserbonus($db);
$objectUs = new User($db);
$objectAd = new Adherent($db); //Adherent
$objectgf = new Pgenericfield($db); //generic field
$objectgt = new Pgenerictable($db); //generic table
$objectsa = new Psalaryaprob($db); //sa
$objCregions = new Cregions($db);

if ($conf->orgman->enabled)
	$objectCh = new Pchargeext($db); //charge

$action=GETPOST('action');
$confirm = GETPOST('confirm');
$cancel = GETPOST('cancel');
$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$fk_period = GETPOST('fk_period');
$lCopy = false;
$mesg = '';

//$objhistory = new Psalaryhistory($db);
//determina el pais
$aPais = explode(":",$conf->global->MAIN_INFO_SOCIETE_COUNTRY);

$cPais = $aPais[1];
$_SESSION['param']['nDiasTrab'] = $conf->global->SALARY_NRO_DIAS_LABORAL;

if ($fk_period>0)
	$result = $objectpe->fetch($fk_period);

/*
 * Actions
 */


if ($action == 'builddoc')	// En get ou en post
{
	$objectpe->fetch($fk_period);
	//$object->fetch_thirdparty();

	// if (GETPOST('model'))
	//   {
	//     $object->setDocModel($user, GETPOST('model'));
	//   }
	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$objectpe->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$result=boleta_pdf_create($db, $objectpe, 'planilla'/*$object->modelpdf*/, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager,$_SESSION['aPlanilla']);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?fk_period='.$objectpe->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
		exit;
	}
}

// Add
if ($action == 'proces' && $user->rights->salary->crearrsal)
{
	$fk_period = GETPOST('fk_period');
	//recuperamos los valores configurados en period
	if ($result)
	{
		$fk_type_fol = $objectpe->fk_type_fol;
		$fk_proces   = $objectpe->fk_proces;
	}
	s_cargamie();
	header("Location: rplanillacof.php?action=edit&fk_period=".$fk_period);
	exit;
}

//action generalte
if ($action == 'confirm_generate' && $user->rights->salary->validsal)
{
	$fk_period   = $_SESSION['validateSalary']['fk_period'];
	$fk_proces   = $_SESSION['validateSalary']['fk_proces'];
	$fk_type_fol = $_SESSION['validateSalary']['fk_type_fol'];
	$state = GETPOST('state');
	$newState = $state - 1;
	if ($_SESSION['member_aprob'])
	{
		//verificamos el estado
		$filter.= " AND t.fk_period = ".$fk_period;
		$filter.= " AND t.fk_proces = ".$fk_proces;
		$filter.= " AND t.fk_type_fol = ".$fk_type_fol;
		$filter.= " AND t.state = ".$newState;

		$res = $objectsp->fetchAll('','',0,0,array(1=>1),'AND',$filter);

		if ($res > 0)
		{
			$db->begin();
			//actualizamos el state
			$res = $objectsp->update_state($fk_period,$fk_proces, $fk_type_fol, $state);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectsp->error,$objectsp->errors,'errors');
			}

			$_SESSION['validateSalary'] = array();
			$_SESSION['member_aprob'] = false;
			if ($_SESSION['aprob_final'] == true)
			{
				if (!$error)
				{
					//echo '<hr>'.$lOk = registry_end($fk_period,$fk_proces,$fk_type_fol,$state);
					$res = $objectsp->registry_end($fk_period, $fk_proces,$fk_type_fol,$state);
					if ($res>0)
					{
						$res = $objectpe->fetch($fk_period);
						if ($res == 1)
						{
							//$objectpe->ref = $objectpe->codref;
							$objectpe->date_close = dol_now();
							$objectpe->state = 5;
							$res = $objectpe->update($user);
							if ($res <=0)
							{
								$error=101;
								setEventMessages($objectpe->error,$objectpe->errors,'errors');
							}
							if (!$error)
							{
								$res = $objectsp->delete_group($fk_period,$fk_proces, $fk_type_fol);
								if ($res <=0)
								{
									$error=102;
									setEventMessages($objectsp->error,$objectsp->errors,'errors');
								}
							}
						}
					}
					elseif($res <0)
					{
						$error=103;
						setEventMessages($objectsp->error,$objectsp->errors,'errors');
					}
				}
			}
			else
			{
				if (!$error)
				{
					$res = $objectpe->fetch($fk_period);
					if ($res == 1)
					{
							//$objectpe->ref = $objectpe->codref;
							//$objectpe->date_close = dol_now();
						$objectpe->status_app = $state;
						$res = $objectpe->update($user);
						if ($res <=0)
						{
							$error=201;
							setEventMessages($objectpe->error,$objectpe->errors,'errors');
						}
					}
				}
			}
			if (!$error)
			{
				$db->commit();
				setEventMessages($langs->trans('Satisfactoryapproval'),null,'mesgs');
			}
			else $db->rollback();
		}
		elseif ($res <0)
		{
			setEventMessages($objectsp->error,$objectsp->errors,'errors');
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
	header("Location: rplanillacof.php?action=edit&fk_period=".$fk_period);
	exit;
}

if ($confirm == 'no')
	$action = 'edit';
if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

	// Generando Excel
if ($action == 'reporteExcel')
{


		//$aPlanilla = unserialize($_SESSION['aPlanilladet']);

	dol_htmloutput_mesg($mesg);
		//armando la planilla de sueldos
	$aPlanilla = $_SESSION['aPlanilla'];
	$aReport = unserialize($_SESSION['aReportplanilla']);
	$aReportplanilla = $aReport[$fk_period];
	$res = $objectpe->fetch($fk_period);


	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setCellValueByColumnAndRow(0,2, "P  L  A  N  I  L  L  A");
	$sheet->getStyle('A2')->getFont()->setSize(15);

	$sheet->mergeCells('A2:V2');
	if($yesnoprice)
		$sheet->mergeCells('A2:V2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);


	$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Fecha de elaboración")));
	$objPHPExcel->getActiveSheet()->setCellValue('D4',dol_print_date(dol_now(),'day'));

		//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Period"));
	$objPHPExcel->getActiveSheet()->setCellValue('D5',$objectpe->mes.'/'.$objectpe->anio);
		//$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Responsable de la elaboracion"));




	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);





		//$objPHPExcel->getActiveSheet()->setCellValue('B4',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
		//$objPHPExcel->getActiveSheet()->setCellValue('B4', $fk_departament);
		//$objPHPExcel->getActiveSheet()->setCellValue('B5',$period_year);
		//$objPHPExcel->getActiveSheet()->setCellValue('B6',$type);

		//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);



		//$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);


	$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	$objPHPExcel->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

	$objPHPExcel->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	//$objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('R')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('T')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);



		// TABLA DE LOS VALORES
	$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans("N"));

	$objPHPExcel->getActiveSheet()->setCellValue('B7',$langs->trans("C_idoc"));
	$objPHPExcel->getActiveSheet()->setCellValue('C7',$langs->trans("Exp"));
	$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Lastname"));
	$objPHPExcel->getActiveSheet()->setCellValue('E7',$langs->trans("Lastnametwo"));
	$objPHPExcel->getActiveSheet()->setCellValue('F7',$langs->trans("Firstname"));
	$objPHPExcel->getActiveSheet()->setCellValue('G7',$langs->trans("Datenac"));
	$objPHPExcel->getActiveSheet()->setCellValue('H7',$langs->trans("Item"));
	$objPHPExcel->getActiveSheet()->setCellValue('I7',$langs->trans("Denominación del puesto"));
	$objPHPExcel->getActiveSheet()->setCellValue('J7',$langs->trans("Ocupación"));
	$objPHPExcel->getActiveSheet()->setCellValue('K7',$langs->trans("Dateini"));
	$objPHPExcel->getActiveSheet()->setCellValue('L7',$langs->trans("Diastrabajados"));
	$objPHPExcel->getActiveSheet()->setCellValue('M7',$langs->trans("Basico"));
	$objPHPExcel->getActiveSheet()->setCellValue('N7',$langs->trans("Bonoant"));
	$objPHPExcel->getActiveSheet()->setCellValue('O7',$langs->trans("Totalrend"));
	$objPHPExcel->getActiveSheet()->setCellValue('P7',$langs->trans("AFP Futuro"));
	$objPHPExcel->getActiveSheet()->setCellValue('Q7',$langs->trans("AFP Previsión"));

	$objPHPExcel->getActiveSheet()->setCellValue('R7',$langs->trans("Sanciones Otros"));
	$objPHPExcel->getActiveSheet()->setCellValue('S7',$langs->trans("Faltas Atrasos"));
	$objPHPExcel->getActiveSheet()->setCellValue('T7',$langs->trans("Desconttotal"));
	$objPHPExcel->getActiveSheet()->setCellValue('U7',$langs->trans("Liquido"));
	$objPHPExcel->getActiveSheet()->setCellValue('V7',$langs->trans("Firma"));
	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
			),
		),
	);


	$objPHPExcel->getActiveSheet()->getStyle('A7:X7')->applyFromArray(
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


	$seq = 1;
	$j=8;


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

	foreach ((array) $aReportplanilla AS $idUser => $data)
	{


		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$data['seq']);
		$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$data['ci']);
		$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$data['issued_in']);
		$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$data['lastname']);
		$objPHPExcel->getActiveSheet()->setCellValue('E' .$j,$data['lastnametwo']);
		$objPHPExcel->getActiveSheet()->setCellValue('F' .$j,$data['firstname']);
		$objPHPExcel->getActiveSheet()->setCellValue('G' .$j,dol_print_date($data['birth'],'day'));
		$objPHPExcel->getActiveSheet()->setCellValue('H' .$j,$data['item']);
		$objPHPExcel->getActiveSheet()->setCellValue('I' .$j,$data['nivel']);
		$objPHPExcel->getActiveSheet()->setCellValue('J' .$j,$data['charge']);
		$objPHPExcel->getActiveSheet()->setCellValue('K' .$j,dol_print_date($data['date_ini'],'day'));
		$objPHPExcel->getActiveSheet()->setCellValue('L' .$j,$data['diastrab']);
		$objPHPExcel->getActiveSheet()->setCellValue('M' .$j,$data['basico']);
		$objPHPExcel->getActiveSheet()->setCellValue('N' .$j,$data['bonoant']);
		$objPHPExcel->getActiveSheet()->setCellValue('O' .$j,$data['totalrend']);
		$objPHPExcel->getActiveSheet()->setCellValue('P' .$j,$data['futuro']);
		$objPHPExcel->getActiveSheet()->setCellValue('Q' .$j,$data['prevision']);
		$objPHPExcel->getActiveSheet()->setCellValue('R' .$j,$data['sanciones']);
		$objPHPExcel->getActiveSheet()->setCellValue('S' .$j,$data['faltas']);
		$objPHPExcel->getActiveSheet()->setCellValue('T' .$j,$data['totaldesc']);
		$objPHPExcel->getActiveSheet()->setCellValue('U' .$j,$data['liquido']);
		$objPHPExcel->getActiveSheet()->setCellValue('V' .$j,'');
		$j++;
	}

	$objPHPExcel->setActiveSheetIndex(0);
		//$objPHPExcel->getActiveSheet()->getStyle('A10:D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		//$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save("excel/ReportPOA.xlsx");
	$objWriter->save("excel/rplanilla.xlsx");
	header("Location: ".DOL_URL_ROOT.'/salary/report/fiche_export.php?archive=rplanilla.xlsx');


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
	// Confirm reject third party
	if ($action == 'generate')
	{
		$formquestion = '';
		$formquestion = array(array('type'=>'hidden','name'=>'fk_period','value'=>$fk_period),
			array('type'=>'hidden','name'=>'state','value'=>GETPOST('state')));
		//$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Approveform"),$langs->trans("ConfirmApproveform",$object->ref),"confirm_generate",$formquestion,1,2);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?fk_period='.$fk_period,
			$langs->trans('Approveform'),
			$langs->trans('ConfirmApproveform').' '.$objectpe->mes.'-'.$objectpe->anio,
			'confirm_generate',
			$formquestion ,
			1,
			2);
		print $formconfirm;
	}




	if ($action=='edit' || $action == 'generate')
	{
		dol_htmloutput_mesg($mesg);
		//armando la planilla de sueldos
		$aPlanilla = $_SESSION['aPlanilla'];

		print_barre_liste($langs->trans("Planilla"), $page, "rplanillacof.php", "", $sortfield, $sortorder,'',$num);

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("N."),"liste.php", "p.table_cod","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("C_idoc"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Exp"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Lastname"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Lastnametwo"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Name"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Nationality"),"liste.php", "p.sequen","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Datenac"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Item"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Denominacion del puesto (Jerarquico)"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Ocupación Cargo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Dateini"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Diastrabajados"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Hoursday"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Basico"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Bonoant"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Horasnum"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Horasamount"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Bonoprod"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Bonootro"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Domindias"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Dominamount"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Totalrend"),"liste.php", "p.state","","","",$sortfield,$sortorder);


		//if ($cPais == "BO")
		//	print_liste_field_titre($langs->trans("DescontAFP"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//else
		//{
		//	print_liste_field_titre($langs->trans("DescontAFPRiesgo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//	print_liste_field_titre($langs->trans("DescontAFPVejez"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		//}
		print_liste_field_titre($langs->trans("AFP Futuro"),"liste.php", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("AFP Previsión"),"liste.php", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("SancionesOtros"),"liste.php", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("FaltasAtrasos"),"liste.php", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Desconttotal"),"liste.php", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Liquido"),"liste.php", "","","","",$sortfield,$sortorder);
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

		foreach ((array) $aPlanilla AS $idUser => $dataUser)
		{
			$nTotalRend = 0;
			$nTotalDesc = 0;
			$nSumaAfp   = 0;
			$nSumaDesc  = 0;
			$objectAd->fetch($idUser);
			$resm = $objectU->fetch(0,$idUser);
			$objectCo->fetch_vigent($idUser,1);
			$objectCh->fetch($objectCo->fk_charge);
			$issued_in = '';
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>'.$seq.'</td>';
			if ($resm)
			{
				$docum = $objectU->docum;
				$lastname = $objectU->lastname;
				$lastnametwo = $objectU->lastnametwo;
				$firstname = $objectU->firstname;
				$objCregions->fetch(0,$objectU->issued_in);
				$issued_in = $objCregions->cheflieu;
			}
			else
			{
				$docum = '';
				$lastnametwo = '';
				$lastname = $objectAd->lastname;
				$firstname = $objectAd->firstname;
			}
			print '<td>'.$docum.'</td>';
			print '<td>'.$issued_in.'</td>';
			print '<td>'.$lastname.'</td>';
			print '<td>'.$lastnametwo.'</td>';
			print '<td>'.$firstname.'</td>';
			print '<td>'.dol_print_date($objectAd->birth,'day').'</td>';
			print '<td>'.$objectAd->item.'</td>';
			print '<td>'.$objectCo->nivel.'</td>';
			if ($objectCh->id == $objectCo->fk_charge)
				print '<td>'.$objectCh->label.'</td>';
			else
				print '<td>&nbsp;</td>';
			print '<td>'.dol_print_date($objectCo->date_ini,'day').'</td>';
			//dias trabajados
			$res105 = $objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'105');
			print '<td align="center">'.$objectsp->hours.'</td>';

			//para el reporte
			$aReportplanilla[$idUser]['seq'] = $seq;
			$aReportplanilla[$idUser]['ci'] = $docum;
			$aReportplanilla[$idUser]['issued_in'] = $issued_in;
			$aReportplanilla[$idUser]['lastname'] = $lastname;
			$aReportplanilla[$idUser]['lastnametwo'] = $lastnametwo;
			$aReportplanilla[$idUser]['firstname'] = $firstname;
			$aReportplanilla[$idUser]['birth'] = $objectAd->birth;
			$aReportplanilla[$idUser]['item'] = $objectAd->item;
			$aReportplanilla[$idUser]['nivel'] = $objectCo->nivel;
			$aReportplanilla[$idUser]['charge'] = $objectCh->label;
			$aReportplanilla[$idUser]['date_ini'] = $objectCo->date_ini;
			$aReportplanilla[$idUser]['diastrab'] = $objectsp->hours;
			//horas pagadas
			//$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'102');
			//print '<td align="center">'.$objectCo->hours.'</td>';
			//salario base

			//print_r($idUser);
			//print_r($fk_period);
			//print_r($fk_proces);
			//print_r($fk_type_fol);
			//exit;
			//haber basico
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'503');
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['basico'] = $objectsp->amount;

			$nTotalRend += $objres->amount;
			//bono antiguedad
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'507');

			//$objres = search_planilla($idUser,'S007',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['bonoant'] = $objectsp->amount;
			$nTotalRend += $objres->amount;

			//total rendimiento
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'103');
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['totalrend'] = $objectsp->amount;

			//afp
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'410');
			if (strtoupper(trim($objectCo->afp)) == 'FUTURO')
			{
				print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
				print '<td align="right">'.'</td>';
				$aReportplanilla[$idUser]['futuro'] = $objectsp->amount;
			}
			else
			{
				print '<td align="right">'.'</td>';
				print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
				$aReportplanilla[$idUser]['prevision'] = $objectsp->amount;
			}

			//SANCIONES
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'727');
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['sanciones'] = $objectsp->amount;
			//FALTAS Y ATRASOS
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'728');
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['faltas'] = $objectsp->amount;

			//descuento total mes
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'499');
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['totaldesc'] = $objectsp->amount;

			//liquido
			$objectsp->initAsSpecimen();
			$objectsp->fetch_salary_concept($idUser,$fk_period,$fk_proces,$fk_type_fol,'599');
			print '<td align="right">'.price(price2num($objectsp->amount,'MT')).'</td>';
			$aReportplanilla[$idUser]['liquido'] = $objectsp->amount;
			print '</tr>';
			$state = $objectsp->state;

			$seq++;
		}
		//$db->free($result);
		print "</table>";
		$aReport[$fk_period] = $aReportplanilla;
		$_SESSION['aReportplanilla'] = serialize($aReport);
		//$_SESSION['aPlanilladet'] = serialize($aPlanilla);

		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_period='.$fk_period.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';
		print '</div>';


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
			$newState = $state+0;
			//$user->fk_member;
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
			elseif ($aData['type'] == 2)
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
			if ($lAprueba || $user->admin)
			{
				if ($user->admin)
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
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=generate&fk_period='.$fk_period.'&state='.$nSeqAprob.'">'.$langs->trans("Approvefinal").'</a>';
				else
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=generate&fk_period='.$fk_period.'&state='.$nSeqAprob.'">'.$langs->trans("Approve").'</a>';
			}
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Approve")."</a>";
		}
		print '</div>';
	}
}


print '<div class="fichecenter">';
$formfile = new Formfile($db);

/*
 * Documents generes
 */
$filename = dol_sanitizeFileName('planilla');
$filedir = $conf->salary->dir_output . "/planilla";
$urlsource = $_SERVER["PHP_SELF"] . "?fk_period=" . $fk_period;
if ($fk_period) $genallowed = $user->rights->salary->crearbsal;
$delallowed = $user->rights->salary->delrbsal+0;

$var = true;
	//$modelpdf = 'boletacofa';
$modelpdf = 'planilla';


//$somethingshown = $formfile->show_documents('salary', $filename, $filedir, $urlsource, $genallowed, $delallowed, $modelpdf, 1, 0, 0, 28, 0, '', 0, '', $soc->default_lang);
print '</div>';


llxFooter();

$db->close();

?>
