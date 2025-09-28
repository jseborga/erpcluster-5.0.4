<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/monprojet/payexcel.php
 *	\ingroup    monprojet
 *	\brief      List all payments of a project
 */

require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
//budget
if ($conf->budget->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
}
//else
//	return '';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/dict.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskcontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaymentdeduction.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/numeroaletras.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratdeduction.class.php';
if ($conf->addendum->enabled)
require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("monprojet");
$langs->load("users");
$langs->load("projects");

$action = GETPOST('action', 'alpha');
$subaction = GETPOST('subaction', 'alpha');
if (empty($subaction)) $subaction='monthly';
$id = GETPOST('id', 'int');
//$idpay = GETPOST('idpay','int');
$ref = GETPOST('ref', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$cancel=GETPOST('cancel');
$newsel=GETPOST('newsel', 'int');

$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);

$table = 'llx_projet_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');

//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object = new Projectext($db);
$objectadd = new Projectext($db);
$objecttime = new Projettasktimedoc($db); //regisro de avances
$taskstatic = new Task($db);
$objuser = new User($db);
if ($conf->budget->enabled) $cunits = new Cunits($db);
$contratadd = new Contratext($db);
$contratded = new contratdeduction($db);
$objpay = new Projetpayment($db);
$objpayde = new projetpaymentdeduction($db);
$taskpayment = new Projettaskpayment($db);
$task    = new Task($db);
$taskadd = new Taskext($db);
$societe = new Societe($db);
$objecttaskadd = new Projettaskadd($db);
if ($conf->addendum->enabled)
$addendum = new Addendum($db);
$taskcontrat = new Projettaskcontrat($db);
$extrafields_project = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
$extrafields_contract = new ExtraFields($db);

if ($conf->budget->enabled)
	$items = new Items($db);

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

if ($id > 0 || ! empty($ref))
{
	// fetch optionals attributes and labels
	$extralabels_projet=$extrafields_project->fetch_name_optionals_label($object->table_element);
}
$extralabels_task=$extrafields_task->fetch_name_optionals_label($taskstatic->table_element);
//extrafields contract
$extralabels_contract=$extrafields_contract->fetch_name_optionals_label($contratadd->table_element);

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->monprojet->task->crear)
	$result = restrictedArea($user, 'projet', $id);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projecttaskcard','globalcard','formObjectOptions'));

$progress=GETPOST('progress', 'int');
$label=GETPOST('label', 'alpha');
$description=GETPOST('description');
$planned_workload=GETPOST('planned_workloadhour')*3600+GETPOST('planned_workloadmin')*60;

$userAccess=0;
$lDisabled = false;


/*
 * Actions
 */

if ($user->rights->monprojet->task->crear)
	$userWrite = true;


/*
 * View
 */

// Example : Adding jquery code

$form=new Form($db);
if ($conf->budget->enabled) $formadd = new FormAdd($db);
$formother=new FormOther($db);
$taskstatic = new Task($db);
$userstatic=new User($db);

$title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
//llxHeader("",$title,$help_url);

if ($id > 0 || ! empty($ref))
{
	$res = $object->fetch($id, $ref);
	$objectadd->fetch($id, $ref);
	$contratadd->getlist($id);
	$object->fetch_thirdparty();
	$res=$object->fetch_optionals($object->id,$extralabels_projet);
	$res=$objectadd->fetch_optionals($objectadd->id,$extralabels_projet);
	//contratista
	$societe->fetch($object->socid);
	$societe_contratist = $societe->name;
	//contratante
	$societe->fetch($object->array_options['options_fk_contracting']);
	$societe_contracting = $societe->name;
	//supervising
	$societe->fetch($object->array_options['options_fk_supervising']);
	$societe_supervising = $societe->name;

	//recuperamos el pago
	$objpay->fetch($idpay);
	//buscamos los pagos anteriores
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet = ".$id;
	$filterstatic .= " AND t.statut = 2";
	//$filterstatic.= " AND t.date_payment < ".$db->idate($objpay->date_payment);
	$numpay = $objpay->fetchAll('ASC','t.date_payment',0,0,$filter,'AND',$filterstatic,false);
	$aPayant = array();
	$sumPayant = 0;
	$sumPayact = 0;
	$sumContratall = 0;
	$sumContratterm = 0;
	$aPayact     = array();
	$aDeducant   = array();
	$aPayment    = array();
	$aContratres = array();

	if ($numpay>0)
	{
		foreach ((array) $objpay->lines AS $l => $linep)
		{
			$aPayment[$linep->id] = array (
				'ref' => $linep->ref,
				'date_request' => $linep->date_request,
				'date_payment' => $linep->date_payment,
				'paymentant' => $sumPayant,
				'paymentact' => $linep->amount,
				'paymenttot' => $sumPayant + $linep->amount,
				'balancecontrat' => 0,
				);
			$sumPayant += $linep->amount;
			$sumPayment+= $linep->amount;
		}
	}
	$numcontrat = count($contratadd->linec);

	$lContrat = true;

		//buscamos los contratos aprobados a la fecha
	if ($numcontrat > 0)
	{
		$sumBalanceContrat = 0;
		//verificamos el monto de contrato segun fechas
		$aPaymentbk = $aPayment;
		foreach ((array) $aPayment AS $idpay => $data)
		{
			$datepay = $data['date_payment'];
			$sumContratterm = 0;
			foreach ((array) $contratadd->linec AS $j => $linec)
			{
				$contrataddnew = new Contratext($db);
				$contrataddnew->fetch($linec->id);
				$resc=$contrataddnew->fetch_optionals($contrataddnew->id,$extralabels_contract);
				if ($lContrat)
				{
					$idContrat = $linec->id;
					$contratprin = $contrataddnew;
				}

				//if ($contrataddnew->array_options['options_type'] == 1)
				//{
				//	$idContrat = $contrataddnew->id;
				//	$contratprin = $contrataddnew;
				//}
				$sumTaskamount = 0;
				if ($linec->date_contrat <= $data['date_payment'])
				{
					$aContratres[$contrataddnew->id]['id']= $contrataddnew->id;
					$aContratres[$contrataddnew->id]['ref']= $contrataddnew->ref;
					$aContratres[$contrataddnew->id]['termday']= $contrataddnew->array_options['options_plazo'];
					$aContratres[$contrataddnew->id]['cod_term'] = $contrataddnew->array_options['options_cod_plazo'];
					$aContratres[$contrataddnew->id]['date'] = $contrataddnew->date_contrat;
					$sumContratterm += $contrataddnew->array_options['options_plazo'];
					//sumamos las tareas del contrato
					$filter = array(1=>1);
					$filterstatic = " AND t.fk_contrat = ".$linec->id;
					$filterstatic .= " AND t.fk_projet = ".$id;
					$numtaskcontrat = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
					$aItem = array();
					$sumContrat = 0;
					$sumContratall = 0;
					foreach ((array) $taskcontrat->lines AS $k => $tline)
					{
						$aContrat['c'][$tline->ref]['unit_program']=$tline->unit_program;
						$aContrat['c'][$tline->ref]['unit_amount']=$tline->unit_amount;
						$aTypeContrat[$contrataddnew->array_options['options_type']]+=price2num($tline->unit_program * $tline->unit_amount,'MT');
						$sumTaskamount += price2num($tline->unit_program * $tline->unit_amount,'MT');
						$sumContrat += price2num($tline->unit_program * $tline->unit_amount,'MT');
						$sumContratall += price2num($tline->unit_program * $tline->unit_amount,'MT');
					}
					$aContratres[$contrataddnew->id]['amount'] = $sumContrat;
					$aPaymentbk[$idpay]['balancecontrat'] = price2num($sumContratall - $data['paymenttot'],'MT');
					if (!$sumBalanceContrat) $sumBalanceContrat = $sumContratall;
					else $sumBalanceContrat = $sumBalanceContrat - $data['paymentact'];
					$aPaymentbk[$idpay]['amountContrat']= $sumContratall;
				}
			}
		}
	}
	else
	{
		$sumBalanceContrat = 0;
		//verificamos el monto de contrato segun fechas
		$aPaymentbk = $aPayment;
		foreach ((array) $aPayment AS $idpay => $data)
		{
			$datepay = $data['date_payment'];
			$sumContratterm = 0;
				//procesamos sin contrato
			$aContratres = array();
			$modetask = 0;
			$modepay = 0;
			$tasksarray = $taskadd->getTasksArray(0, 0, $object->id, $socid, $modetask,'',-1,'',0,0,0,1,0,'',$modepay);
			if (count($tasksarray)>0)
			{
				$aTasknumref = array();
				for ($i=0; $i < count($tasksarray); $i++)
				{
					if ($tasksarray[$i]->id>0)
					{
						$aContrat['c'][$tasksarray[$i]->ref]['unit_program']=$tasksarray[$i]->array_options['options_unit_program'];
						$aContrat['c'][$tasksarray[$i]->ref]['unit_amount']=$tasksarray[$i]->array_options['options_unit_amount'];
						$aTypeContrat[$contrataddnew->array_options['options_type']]+=price2num($tasksarray[$i]->array_options['options_unit_program'] * $tasksarray[$i]->array_options['options_unit_amount'],'MU');
						$sumTaskamount += price2num($tasksarray[$i]->array_options['options_unit_program'] * $tasksarray[$i]->array_options['options_unit_amount'],'MT');
						$sumContrat += price2num($tasksarray[$i]->array_options['options_unit_program'] * $tasksarray[$i]->array_options['options_unit_amount'],'MT');
						$sumContratall += price2num($tasksarray[$i]->array_options['options_unit_program'] * $tasksarray[$i]->array_options['options_unit_amount'],'MT');

					}
				}
				$aPaymentbk[$idpay]['balancecontrat'] = price2num($sumContratall - $data['paymenttot'],'MT');
				if (!$sumBalanceContrat) $sumBalanceContrat = $sumContratall;
				else $sumBalanceContrat = $sumBalanceContrat - $data['paymentact'];
				$aPaymentbk[$idpay]['amountContrat']= $sumContratall;
			//$_SESSION['aTasknumref'][$object->id] = serialize($aTasknumref);
			}

			//recuperamos las retenciones del contrato principal
			$aRetention = array();
		}
	}
	// To verify role of users
	$userWrite  = $objectadd->restrictedProjectAreaadd($user,'write');


	$param=($mode=='mine'?'&mode=mine':'');

	//	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

	// // Define a complementary filter for search of next/prev ref.
	// if (! $user->rights->projet->all->lire)
	//   {
	// 	$projectsListId = $object->getProjectsAuthorizedForUser($user,$mine,0);
	// 	$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	//   }

	echo $numrows = count($aPaymentbk);
	if ($numrows > 0)
	{
		include_once DOL_DOCUMENT_ROOT.'/monprojet/lib/format_excel.lib.php';

		//PRCESO 1
		$pos = 0;
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex($pos);

		$objPHPExcel->getActiveSheet()->setTitle('Certificado'); //Establecer nombre para la pestaña

		$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('PLANILLA DE AVANCE DE OBRA - DATOS GENERALES DE LA OBRA EN EJECUCION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B7:L7');
		$objPHPExcel->getActiveSheet()->getStyle('B7:L7')->applyFromArray($styleHead);
		$objPHPExcel->getActiveSheet()->getStyle('B7:L7')->applyFromArray($styleBorder);
		//titulos
		$objPHPExcel->getActiveSheet()->SetCellValue('B9',$langs->trans('CONTRATANTE'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B9:F9');
		$objPHPExcel->getActiveSheet()->SetCellValue('G9',$societe_contracting);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G9:L9');

		$objPHPExcel->getActiveSheet()->SetCellValue('B10',$langs->trans('SUPERVISION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B10:F10');
		$objPHPExcel->getActiveSheet()->SetCellValue('G10',$societe_supervising);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G10:L10');

		$objPHPExcel->getActiveSheet()->SetCellValue('B11',$langs->trans('CONTRATISTA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B11:F11');
		$objPHPExcel->getActiveSheet()->SetCellValue('G11',$societe_contratist);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G11:L11');

		$objPHPExcel->getActiveSheet()->SetCellValue('B12',$langs->trans('DEPARTAMENTO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B12:F12');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G12:L12');
		$objPHPExcel->getActiveSheet()->SetCellValue('B13',$langs->trans('MUNICIPIO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B13:F13');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G13:L13');
		$objPHPExcel->getActiveSheet()->SetCellValue('B14',$langs->trans('ZONA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B14:F14');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G14:L14');
		$objPHPExcel->getActiveSheet()->SetCellValue('B15',$langs->trans('DIRECCION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B15:F15');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G15:L15');

		$objPHPExcel->getActiveSheet()->SetCellValue('B16',$langs->trans('OBRA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B16:F17');
		$objPHPExcel->getActiveSheet()->SetCellValue('G16',$object->description);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G16:L17');

		$objPHPExcel->getActiveSheet()->SetCellValue('B18',$langs->trans('CONTRATO Nº'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B18:F18');
		$objPHPExcel->getActiveSheet()->SetCellValue('G18',$contratprin->ref);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G18:L18');

		$objPHPExcel->getActiveSheet()->SetCellValue('B19',$langs->trans('FECHA DE CONTRATO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B19:F19');
		$objPHPExcel->getActiveSheet()->SetCellValue('G19',dol_print_date($contratprin->date_contrat,'day'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G19:L19');

		$objPHPExcel->getActiveSheet()->SetCellValue('B20',$langs->trans('ADENDA CONTRATO Nº'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B20:F20');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G20:L20');

		$objPHPExcel->getActiveSheet()->SetCellValue('B21',$langs->trans('FECHA ADENDA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B21:F21');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G21:L21');

		$objPHPExcel->getActiveSheet()->SetCellValue('B22',$langs->trans('MONTO CONTRATO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B22:F22');
		$objPHPExcel->getActiveSheet()->SetCellValue('G22',$aContratres[$idContrat]['amount']);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G22:L22');

		$objPHPExcel->getActiveSheet()->SetCellValue('B23',$langs->trans('MONTO CONTRATO MODIFICADO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B23:F23');
		$objPHPExcel->getActiveSheet()->SetCellValue('G23',$sumContratall);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G23:L23');
		$objPHPExcel->getActiveSheet()->getStyle('G22:G23')->applyFromArray($stylenumber);
		$objPHPExcel->getActiveSheet()->getStyle('G22:G23')->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->SetCellValue('B24',$langs->trans('PLAZO DE CONTRATO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B24:F24');
		$objPHPExcel->getActiveSheet()->SetCellValue('G24',$sumContratterm);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G24:L24');

		$objPHPExcel->getActiveSheet()->SetCellValue('B25',$langs->trans('FECHA DE INICIO'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B25:F25');
		$objPHPExcel->getActiveSheet()->SetCellValue('G25','');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G25:L25');

		$objPHPExcel->getActiveSheet()->SetCellValue('B26',$langs->trans('FECHA DE CONCLUSION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B26:F26');
		$objPHPExcel->getActiveSheet()->SetCellValue('G26','');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G26:L26');
		$objPHPExcel->getActiveSheet()->getStyle('B9:L26')->applyFromArray($styleBorder);

		$objPHPExcel->getActiveSheet()->SetCellValue('B28',$langs->trans('FECHA DE CONCLUSION REAL'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B28:F28');
		$objPHPExcel->getActiveSheet()->SetCellValue('G28','');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G28:L28');

		$objPHPExcel->getActiveSheet()->SetCellValue('B29',$langs->trans('FECHA DE RECEPCION PROVISIONAL'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B29:F29');
		$objPHPExcel->getActiveSheet()->SetCellValue('G29','');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G29:L29');

		$objPHPExcel->getActiveSheet()->SetCellValue('B30',$langs->trans('FECHA DE RECEPCION DEFINITIVA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B30:F30');
		$objPHPExcel->getActiveSheet()->SetCellValue('G30','');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G30:L30');
		$objPHPExcel->getActiveSheet()->getStyle('B28:L30')->applyFromArray($styleBorder);

		$rowlineini = 32;
		$objPHPExcel->getActiveSheet()->SetCellValue('B32',$langs->trans('AMPLIACIONES DE PLAZO SOLICITADAS Y AUTORIZADAS'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B32:L32');
		$objPHPExcel->getActiveSheet()->getStyle('B32:L32')->applyFromArray($styleCenter);

		$objPHPExcel->getActiveSheet()->SetCellValue('B33',$langs->trans('DETALLE'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B33:F33');
		$objPHPExcel->getActiveSheet()->SetCellValue('G33',$langs->trans('D.C.'));
		$objPHPExcel->getActiveSheet()->SetCellValue('H33',$langs->trans('FECHA DE CONCLUSION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H33:L33');
		$row = 34;
		$sumTermpartial = 0;
		foreach ((array) $aContratres AS $j => $data)
		{
			if ($data['id'] != $idContrat)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$data['ref']);
				$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':F'.$row);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$data['termday']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,dol_print_date($data['date'],'day'));
				$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);
				$sumTermpartial += $data['termday'];
				$row++;
			}
		}

		//total term
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('TOTAL DIAS AMPLIADOS'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':F'.$row);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$sumTermpartial);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('TOTAL PLAZO DE EJECUCION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':F'.$row);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$sumTermpartial+$aContratres[$idContrat]['termday']);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);

		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('MODIFICACIONES AL CONTRATO DE OBRA AUTORIZADAS'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':L'.$row);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':L'.$row)->applyFromArray($styleCenter);
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('DETALLE'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':F'.$row);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$langs->trans('Bs.'));
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$langs->trans('FECHA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);
		$row++;
		$sumPartial = 0;
		foreach ((array) $aContratres AS $j => $data)
		{
			if ($data['id'] != $idContrat)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$data['ref']);
				$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':F'.$row);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$data['amount']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,dol_print_date($data['date'],'day'));
				$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$row.':G'.$row)->applyFromArray($stylenumber);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$row.':G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

				$sumPartial += $data['amount'];
				$row++;
			}
		}
		//total term
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('MONTO FINAL'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':F'.$row);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$sumPartial+$aContratres[$idContrat]['amount']);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$row.':G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('INCIDENCIA'));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$sumPartial / $aContratres[$idContrat]['amount']);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':L'.$row);
		$rowlinefin = $row;
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rowlineini.':L'.$rowlinefin)->applyFromArray($styleBorder);

		$row++;$row++;
		$rowlineini = $row;
		$rownext = $row+1;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('AVANCE FINANCIERO DE LA OBRA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':L'.$row);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':L'.$row)->applyFromArray($styleCenter);

		$row++;
		$rownext = $row+1;

		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('Nº PLANILLA'));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$langs->trans('FECHA'));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$langs->trans('MONTO DE AVANCE EN (BOLIVIANOS)'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':B'.$rownext);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C'.$row.':D'.$row);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('E'.$row.':L'.$row);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':L'.$rownext)->applyFromArray($styleCenter);

		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$langs->trans('SOLICITUD'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$langs->trans('PAGO'));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$langs->trans('ANTERIOR'));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$langs->trans('ACTUAL'));
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$langs->trans('TOTAL'));
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$langs->trans('SALDO'));
		$objPHPExcel->getActiveSheet()->getStyle('C'.$row.':L'.$row)->applyFromArray($styleCenter);

		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('E'.$row.':F'.$row);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G'.$row.':H'.$row);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('I'.$row.':J'.$row);
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('K'.$row.':L'.$row);

		$row++;

		foreach ((array) $aPaymentbk AS $i => $data)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$data['ref']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,dol_print_date($data['date_request'],'day'));
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,dol_print_date($data['date_payment'],'day'));
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$data['paymentant']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$data['paymentant']/$data['amountContrat']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$data['paymentact']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$data['paymentact']/$data['amountContrat']);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$data['paymentant']+$data['paymentact']);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,($data['paymentant']+$data['paymentact'])/$data['amountContrat']);

			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$data['amountContrat']-($data['paymentant']+$data['paymentact']));
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,($data['amountContrat']-($data['paymentant']+$data['paymentact']))/$data['amountContrat']);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row.':L'.$row)->applyFromArray($stylenumber);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row.':L'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
			$row++;
		}
		$rowlinefin = $row-1;
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rowlineini.':L'.$rowlinefin)->applyFromArray($styleBorder);
		$row++;
		$row++;
		$row++;
		$row++;
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Firma:');
		$row++;
		$row++;
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Entregado por:');
		$row++;
		$row++;
		$row++;
		$row++;
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Firma:');
		$row++;
		$row++;
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Recibido por:');
		$row++;
		$row++;
		$row++;
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Fecha:');


		$objPHPExcel->getActiveSheet()->getStyle("B1:L".$row)->getFont()->setSize(10);

		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);


		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	//echo '<hr>escribe';exit;
		$objWriter->save("excel/exportpay.xlsx");
		$_SESSION['docsave'] = 'exportpay.xlsx';
		header('Location: '.DOL_URL_ROOT.'/monprojet/fiche_export.php');

	}
}
