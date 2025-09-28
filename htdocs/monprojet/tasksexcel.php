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
 *	\file       htdocs/projet/tasks.php
 *	\ingroup    projet
 *	\brief      List all tasks of a project
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

$langs->load("users");
$langs->load("projects");

$action = GETPOST('action', 'alpha');
$subaction = GETPOST('subaction', 'alpha');
if (empty($subaction)) $subaction='monthly';
$id = GETPOST('id', 'int');
$idpay = GETPOST('idpay','int');
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
echo 'asdfasdfasdf';
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object = new Projectext($db);
$objectadd = new Projectext($db);
$objecttime = new Projettasktimedoc($db); //regisro de avances
$taskstatic = new Task($db);
$objuser = new User($db);
if ($conf->budget->enabled)
{
	$cunits = new Cunits($db);
}
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
if ($conf->budget->enabled)
	$formadd = new FormAdd($db);
$formother=new FormOther($db);
$taskstatic = new Task($db);
$userstatic=new User($db);

$title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
//llxHeader("",$title,$help_url);
echo '<hr>id '.$id;
if ($id > 0 || ! empty($ref))
{
	echo '<hr>adentor';
	$object->fetch($id, $ref);
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
	$filterstatic.= " AND t.statut = 2";
	$filterstatic.= " AND UNIX_TIMESTAMP(t.date_payment) < ".$objpay->date_payment;
	$numpay = $objpay->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	$aPayant = array();
	$sumPayant = 0;
	$sumPayact = 0;
	$aPayact = array();
	$aDeducant = array();

	if ($numpay>0)
	{
		foreach ((array) $objpay->lines AS $l => $linep)
		{
			//sumamos las deducciones anteriores
			//recuperamos la lista de deducciones
			$arraydeduc = getlist_deduction('','ASC','sequence');
			foreach ((array) $arraydeduc AS $j => $data)
			{
				$filterd = array(1=>1);
				$filterstaticd = " AND t.fk_projet_payment = ".$linep->id;
				$filterstaticd.= " AND t.code = '".$data['code']."'";
				$numpayde = $objpayde->fetchAll('','',0,0,$filterd,'AND',$filterstaticd,true);
				if ($numpayde == 1)
				{
					$aDeducant[$data['code']] += $objpayde->amount;
				}
			}

			//buscamos las tareas pagadas anteriores
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_projet_payment = ".$linep->id;
			$filterstatic.= " AND t.statut = 3";
			$numtaskpay = $taskpayment->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);

			foreach((array) $taskpayment->lines AS $m => $tlinep)
			{
				//busco la tarea
				$task->fetch($tlinep->fk_task);
				$aPayant[$task->ref]+= $tlinep->unit_declared;
			}
		}
	}
	//buscamos el pago actual
	//recuperamos la lista de deducciones
	$aDeducact = array();
	$arraydeduc = getlist_deduction('','ASC','sequence');
	foreach ((array) $arraydeduc AS $j => $data)
	{
		$filterd = array(1=>1);
		$filterstaticd = " AND t.fk_projet_payment = ".$idpay;
		$filterstaticd.= " AND t.code = '".$data['code']."'";
		$numpayde = $objpayde->fetchAll('','',0,0,$filterd,'AND',$filterstaticd,true);
		if ($numpayde == 1)
		{
			$aDeducact[$data['code']] += $objpayde->amount;
		}
	}
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet_payment = ".$idpay;
	$filterstatic.= " AND t.statut = 3";
	$numtaskpay = $taskpayment->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	foreach((array) $taskpayment->lines AS $m => $tlinep)
	{
		//busco la tarea
		$task->fetch($tlinep->fk_task);
		$aPayact[$task->ref]+= $tlinep->unit_declared;
	}
	//verificamos que contratos se tiene
	//armamos un array de contratos y tareas
	$numcontrat = count($contratadd->linec);
	$aContrat = array();
	$idContrat = 0;
	$aContrath = array();
	$aDatacontrat = array();
	$aGrupo = array();
	$aTypeContrat = array();
	//echo '<hr>contrats '.$numcontrat;
	if ($numcontrat > 1)
	{
		foreach ((array) $contratadd->linec AS $j => $linec)
		{
			$contrataddnew = new Contratext($db);
			$contrataddnew->fetch($linec->id);
			$resc=$contrataddnew->fetch_optionals($contrataddnew->id,$extralabels_contract);
			//   echo '<hr>datos '.$linec->date_contrat.' <= '.$objpay->date_payment;
			if ($linec->date_contrat <= $objpay->date_payment)
			{
				$aDatacontrat[$linec->id] = $linec;
				//vemos si tiene hijos

				$addendum->getlist_son($linec->id,'fk_contrat_father');
				if (count($addendum->array)>0)
				{
					$filter = array(1=>1);
					$filterstatic = " AND t.fk_contrat = ".$linec->id;
					$filterstatic .= " AND t.fk_projet = ".$id;
					$numtaskcontrat = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
					$aItem = array();
					foreach ((array) $taskcontrat->lines AS $k => $tline)
					{
						$aContrat['c'][$tline->ref]['unit_program']=$tline->unit_program;
						$aContrat['c'][$tline->ref]['unit_amount']=$tline->unit_amount;
						$aTypeContrat[$contrataddnew->array_options['options_type']]+=price2num($tline->unit_program * $tline->unit_amount,'MU');
					}
					//$aContrat['c'][$linec->id] = $aItem;
					$idContrat = $linec->id;
				}
				else
				{
					$filter = array(1=>1);
					$filterstatic = " AND t.fk_contrat = ".$linec->id;
					$filterstatic .= " AND t.fk_projet = ".$id;
					$numtaskcontrat = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
					$aItem = array();
					foreach ((array) $taskcontrat->lines AS $k => $tline)
					{
						$aContrat['h'][$tline->ref]['unit_program']+=$tline->unit_program;
						$aContrat['h'][$tline->ref]['unit_amount']=$tline->unit_amount;
						$aTypeContrat[$contrataddnew->array_options['options_type']]+=price2num($tline->unit_program * $tline->unit_amount,'MU');
					}
					//$aContrat['h'][$linec->id] = $aItem;
					$aContrath[$linec->id] = $linec->id;
				}
			}
		}
		//recuperamos las retenciones del contrato principal
		$aRetention = array();
		$filter = array(1=>1);
		$filterstatic = " AND fk_contrat = ".$idContrat;
		$numcded = $contratded->fetchAll('', '', 0, 0,$filter, 'AND',$filterstatic);
		foreach ((array) $contratded->lines AS $j => $line)
		{
			$aRetention[$line->code] = $line->amount;
		}
	}
	elseif($numcontrat == 1)
	{
		//      print_r($contratadd->linec);
		// foreach ((array) $contratadd->linec AS $j => $linec)
		//   {
		//     echo '<hr>dat '.$linec->date_contrat.' <= '.$objpay->date_payment;
		// 	if ($linec->date_contrat <= $objpay->date_payment)
			// 	$aContrat['c'][$linec->id] = $linec;
		//   }
		foreach ((array) $contratadd->linec AS $j => $linec)
		{
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_contrat = ".$linec->id;
			$filterstatic .= " AND t.fk_projet = ".$id;
			$numtaskcontrat = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
			$aItem = array();
			foreach ((array) $taskcontrat->lines AS $k => $tline)
			{
				$aContrat['c'][$tline->ref]['unit_program']=$tline->unit_program;
				$aContrat['c'][$tline->ref]['unit_amount']=$tline->unit_amount;
				$aTypeContrat[$contrataddnew->array_options['options_type']]+=price2num($tline->unit_program * $tline->unit_amount,'MU');
			}
			//recuperamos las retenciones del contrato principal
			$aRetention = array();
			$filter = array(1=>1);
			$filterstatic = " AND fk_contrat = ".$linec->id;
			$numcded = $contratded->fetchAll('', '', 0, 0,$filter, 'AND',$filterstatic);
			foreach ((array) $contratded->lines AS $j => $line)
			{
				$aRetention[$line->code] = $line->amount;
			}
		}
	}
	else
	{
		//procesamos sin contrato
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

				}
			}
			//$_SESSION['aTasknumref'][$object->id] = serialize($aTasknumref);
		}

			//recuperamos las retenciones del contrato principal
		$aRetention = array();
		//exit;
	}
	echo 'contratos '.count($aRetention);
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

	$modetask = 0;
	$tasksarray=$taskadd->getTasksArray(0, 0, $object->id, $socid, $modetask);
	$tasksrole=($mode=='mine' ? $taskstatic->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');

	$aData = array();
	$j=0; $level=0;
	$aData=monprojectLineexport($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$lVista,$aData);
	$numrows = count($aData)+1;
	if ($numrows > 0)
	{
		include_once DOL_DOCUMENT_ROOT.'/monprojet/lib/format_excel.lib.php';

	//PRCESO 1
		$pos = 0;
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex($pos);
		$objPHPExcel->getActiveSheet()->setTitle('Detalle certificado');
	//Establecer nombre para la pestaña
		//titulos
		$objPHPExcel->getActiveSheet()->SetCellValue('B6',$langs->trans('CONTRATANTE'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D6',$societe_contracting);
		$objPHPExcel->getActiveSheet()->SetCellValue('S6',$langs->trans('INFORME PERIODICO Nº'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('S6:U6');
		$objPHPExcel->getActiveSheet()->SetCellValue('V6',$objpay->ref);

		$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('CONTRATISTA'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D7',$societe_contratist);
		$objPHPExcel->getActiveSheet()->SetCellValue('S7',$langs->trans('FECHA DE PRESENTACION'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('S7:U7');
		$objPHPExcel->getActiveSheet()->SetCellValue('V7',dol_print_date($objpay->date_payment,'day'));
		$objPHPExcel->getActiveSheet()->getStyle('S6:U7')->applyFromArray($styleHeadRight);
		$objPHPExcel->getActiveSheet()->getStyle('V6:V7')->applyFromArray($styleHeadLeft);

		$objPHPExcel->getActiveSheet()->SetCellValue('B8',$langs->trans('OBRA'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D8',$object->description);
		$objPHPExcel->getActiveSheet()->SetCellValue('B9',$langs->trans('SUPERVISION'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D9',$societe_supervising);


		$objPHPExcel->getActiveSheet()->SetCellValue('B11',$langs->trans('DETALLE DEL CERTIFICADO DE AVANCE DE OBRA'));
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B11:V11');
		$objPHPExcel->getActiveSheet()->getStyle('B11:V11')->applyFromArray($styleHead);
		$objPHPExcel->getActiveSheet()->getStyle("B6:V11")->getFont()->setSize(10);


	//cabecera
		$objPHPExcel->getActiveSheet()->SetCellValue('B13','OBRA AUTORIZADA SEGÚN CONTRATO');
		$objPHPExcel->getActiveSheet()->SetCellValue('P13','OBRA EJECUTADA');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B13:O13');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('P13:V13');
		$objPHPExcel->getActiveSheet()->SetCellValue('H14','CONTRATO DE OBRA');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H14:O14');
		$objPHPExcel->getActiveSheet()->SetCellValue('P14','AVANCE FISICO');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('P14:S14');
		$objPHPExcel->getActiveSheet()->SetCellValue('T14','AVANCE FINANCIERO');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('T14:V14');

		$objPHPExcel->getActiveSheet()->SetCellValue('B15','Nº');
		$objPHPExcel->getActiveSheet()->SetCellValue('C15','DESCRIPCIÓN');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C15:F15');
		$objPHPExcel->getActiveSheet()->SetCellValue('G15','UNIDAD');
		$objPHPExcel->getActiveSheet()->SetCellValue('H15','CANTIDAD');
		$objPHPExcel->getActiveSheet()->SetCellValue('I15','CANTIDAD');
		$objPHPExcel->getActiveSheet()->SetCellValue('J15','CANTIDAD');
		$objPHPExcel->getActiveSheet()->SetCellValue('K15','INCIDENCIA');
		$objPHPExcel->getActiveSheet()->SetCellValue('L15','PRECIO');
		$objPHPExcel->getActiveSheet()->SetCellValue('M15','IMPORTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('N15','IMPORTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('O15','INCIDENCIA');

		$objPHPExcel->getActiveSheet()->SetCellValue('P15','ANTERIOR');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('P15:P16');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q15','EN EL');
		$objPHPExcel->getActiveSheet()->SetCellValue('R15','TOTAL A');
		$objPHPExcel->getActiveSheet()->SetCellValue('S15','INCIDENCIA');

		$objPHPExcel->getActiveSheet()->SetCellValue('T15','IMPORTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('U15','IMPORTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('V15','IMPORTE');
	//LINEA 16
		$objPHPExcel->getActiveSheet()->SetCellValue('H16','INICIAL');
		$objPHPExcel->getActiveSheet()->SetCellValue('I16','MODIFICADA');
		$objPHPExcel->getActiveSheet()->SetCellValue('J16','APROBADA');
		$objPHPExcel->getActiveSheet()->SetCellValue('K16','%');
		$objPHPExcel->getActiveSheet()->SetCellValue('L16','UNITARIO');
		$objPHPExcel->getActiveSheet()->SetCellValue('M16','INICIAL');
		$objPHPExcel->getActiveSheet()->SetCellValue('N16','APROBADO');
		$objPHPExcel->getActiveSheet()->SetCellValue('O16','%');

		$objPHPExcel->getActiveSheet()->SetCellValue('Q16','PRESENTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('R16','LA FECHA');
		$objPHPExcel->getActiveSheet()->SetCellValue('S16','A LA FECHA');

		$objPHPExcel->getActiveSheet()->SetCellValue('T16','ANTERIOR');
		$objPHPExcel->getActiveSheet()->SetCellValue('U16','ACTUAL');
		$objPHPExcel->getActiveSheet()->SetCellValue('V16','TOTAL');
	//LINEA 17
		$objPHPExcel->getActiveSheet()->SetCellValue('B17','0');
		$objPHPExcel->getActiveSheet()->SetCellValue('C17','1');
		$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C17:F17');
		$objPHPExcel->getActiveSheet()->SetCellValue('G17','2');
		$objPHPExcel->getActiveSheet()->SetCellValue('H17','3');
		$objPHPExcel->getActiveSheet()->SetCellValue('I17','4');
		$objPHPExcel->getActiveSheet()->SetCellValue('J17','5');
		$objPHPExcel->getActiveSheet()->SetCellValue('K17','6 = 5 / 3');
		$objPHPExcel->getActiveSheet()->SetCellValue('L17','7');
		$objPHPExcel->getActiveSheet()->SetCellValue('M17','8 = 3 * 7');
		$objPHPExcel->getActiveSheet()->SetCellValue('N17','9 = 5 * 7');
		$objPHPExcel->getActiveSheet()->SetCellValue('O17','10 = 9 / 8');

		$objPHPExcel->getActiveSheet()->SetCellValue('P17','7');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q17','8');
		$objPHPExcel->getActiveSheet()->SetCellValue('R17','9 = 7 + 8');
		$objPHPExcel->getActiveSheet()->SetCellValue('S17','10 = 9 / 5');

		$objPHPExcel->getActiveSheet()->SetCellValue('T17','11 = 4 * 7');
		$objPHPExcel->getActiveSheet()->SetCellValue('U17','12 = 4 * 8');
		$objPHPExcel->getActiveSheet()->SetCellValue('V17','13 = 11 + 12');

		$objPHPExcel->getActiveSheet()->getStyle('B13:V17')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("B13:V17")->getFont()->setSize(10);
	//armamos subtotales
		$idHilo = 0;
		$a = 0;
		$aDefGrupo0 = array();
		$aDefGrupo  = array();
		$aSubGrupo  = array();
		$aDefGrupoS = array();
		$aDg = array();
		$nLevel = 0;
		$aTasksum = array();
	//tercer nivel
		foreach ((array) $aData AS $i => $lines)
		{
			if (empty($nLevel)) $nLevel = $lines['level'];
			else
				if ($nLevel < $lines['level']) $nLevel = $lines['level'];

			$aDg[$lines['id']] = array('level'=>$lines['level'],'parent'=>$lines['fk_parent']);
			if ($lines['group']>0)
			{
				if (empty($lines['fk_parent']))
					$aDefGrupo0[$lines['id']] = $lines['id'];
				$aDefGrupo[$lines['fk_parent']][$lines['id']] = $lines['id'];
				$aDefGrupoS[$lines['id']] = $lines['fk_parent'];
			}
			$aSubGrupo[$lines['fk_parent']][$lines['id']] = $lines['id'];
			$task->fetch($lines['id']);
			$var = !$var;
			$refparent = '';
			$refitem = '';
			$reftype = '';
			$unit = '';

		//verificamos si es grupo
			if ($lines['group']>0)
			{
			//es grupo
				$aGrupo[$lines['fk_parent']]=0;
			}
			else
			{
			//registramos la cantidad inicial del contrato
				$h_ = $aContrat['c'][$lines['ref']]['unit_program']+0;
			//registramos la cantidad modificada del contrato
				$i_ = $aContrat['h'][$lines['ref']]['unit_program']+0;
			//aprobado
				$j_ = $h_ + $i_+0;
			//incidencia
				$k_ = 0;
				if ($h_) $k_ = $j_ / $h_ ;
			$l_ = $aContrat['c'][$lines['ref']]['unit_amount']+0; //precio unitario
			//unitario
			//inical precio
			$m_ = price2num(($h_ * $l_)+0,'MT');
			$aGrupo[$lines['fk_parent']]['m']+=$m_;
			$aTasksum[$lines['id']]['m']+=$m_;

			//aprobado precio
			$n_ = price2num(($j_ * $l_)+0,'MT');
			$aGrupo[$lines['fk_parent']]['n']+=$n_;
			$aTasksum[$lines['id']]['n']+=$n_;

			//incidencia
			$o_ = 0;
			if ($m_) $o_ = $n_ / $m_;
			//cantidad avance anterior
			$p_ = $aPayant[$lines['ref']]+0;
			//cantidad avance actual
			$q_ = $aPayact[$lines['ref']]+0;
			//cantidad avance total
			$r_ = $p_ + $q_;
			//cantidad incidencia
			$r_ = $p_ + $j_;
			//valor avance anterior
			$t_ = $l_ * $p_;
			$aGrupo[$lines['fk_parent']]['t']+=$t_;
			$aTasksum[$lines['id']]['t']+=$t_;

			//cantidad avance actual
			$u_ = $l_ * $q_;
			$aGrupo[$lines['fk_parent']]['u']+=$u_;
			$aTasksum[$lines['id']]['u']+=$u_;
			//cantidad avance total
			$v_ = $t_ + $u_;
			$aGrupo[$lines['fk_parent']]['v']+=$v_;
			$aTasksum[$lines['id']]['v']+=$v_;
		}
		$a++;
	}

	// //segundo nivel
	// foreach((array) $aDefGrupo AS $b => $array)
	//   {
	//     foreach ((array) $array AS $c => $valor)
	//       {
	// 	$aSumGrupo[$c]['m'] += $aGrupo[$c]['m'];
	// 	$aSumGrupo[$c]['n'] += $aGrupo[$c]['n'];
	// 	$aSumGrupo[$c]['t'] += $aGrupo[$c]['t'];
	// 	$aSumGrupo[$c]['u'] += $aGrupo[$c]['u'];
	// 	$aSumGrupo[$c]['v'] += $aGrupo[$c]['v'];
	//       }
	//   }
	// $aRes = array();
	// //primer nivel
	// foreach($aSumGrupo AS $c => $data)
	//   {
	//     $aRes[$aDg['parent'][$c]]['m'] += $data['m'];
	//     $aRes[$aDg['parent'][$c]]['n'] += $data['n'];
	//     $aRes[$aDg['parent'][$c]]['t'] += $data['t'];
	//     $aRes[$aDg['parent'][$c]]['u'] += $data['u'];
	//     $aRes[$aDg['parent'][$c]]['v'] += $data['v'];
	//   }
	// echo '<hr>'.$nLevel;
	// echo '<pre>';
	// print_r($aTasksum);
	// // print_r($aDefGrupo);
	// print_r($aDefGrupoS);
	// print_r($aDg);
	// print_r($aSumGrupo);
	// print_r($aRes);

	foreach ((array) $aDg AS $i => $data)
	{
		$aLevel[$data['level']][$i]= $data['parent'];
	}
	krsort($aLevel);
//	echo '<pre>';
//	print_r($aLevel);
//	echo '</pre>';
//	echo 'nLevel '.$nLevel;
	$aSum = array();
	//para la suma del nivel de tareas
	foreach ((array) $aLevel AS $o => $data)
	{
		foreach ((array) $data AS $fk_task => $fk_parent)
		{
			$aSum[$fk_parent]['m']+=$aTasksum[$fk_task]['m'];
			$aSum[$fk_parent]['n']+=$aTasksum[$fk_task]['n'];
			$aSum[$fk_parent]['t']+=$aTasksum[$fk_task]['t'];
			$aSum[$fk_parent]['u']+=$aTasksum[$fk_task]['u'];
			$aSum[$fk_parent]['v']+=$aTasksum[$fk_task]['v'];
		}
	}

	//para la suma de grupos
	foreach ((array) $aLevel AS $o => $data)
	{
		foreach ((array) $data AS $fk_task => $fk_parent)
		{
			if ($o == $nLevel)
			{
				//$aSum[$fk_parent]['m']+=$aTasksum[$fk_task]['m'];
				//$aSum[$fk_parent]['n']+=$aTasksum[$fk_task]['n'];
				//$aSum[$fk_parent]['t']+=$aTasksum[$fk_task]['t'];
				//$aSum[$fk_parent]['u']+=$aTasksum[$fk_task]['u'];
				//$aSum[$fk_parent]['v']+=$aTasksum[$fk_task]['v'];
			}
			else
			{
				$aSum[$fk_parent]['m']+=price2num($aSum[$fk_task]['m'],'MT');
				$aSum[$fk_parent]['n']+=price2num($aSum[$fk_task]['n'],'MT');
				$aSum[$fk_parent]['t']+=price2num($aSum[$fk_task]['t'],'MT');
				$aSum[$fk_parent]['u']+=price2num($aSum[$fk_task]['u'],'MT');
				$aSum[$fk_parent]['v']+=price2num($aSum[$fk_task]['v'],'MT');
			}
		}
	}
//	echo '<HR>aSum <pre>';
//	print_r($aSum);
//	echo '</pre>';

	// exit;
	$aDefg = array();
	foreach ((array) $aDefGrupo AS $l => $aDef)
	{
		foreach ((array) $aDef AS $k)
		{
			$aDefg[$l]['m']+= $aSumGrupo[$k]['m'];
			$aDefg[$l]['n']+= $aSumGrupo[$k]['n'];
			$aDefg[$l]['t']+= $aSumGrupo[$k]['t'];
			$aDefg[$l]['u']+= $aSumGrupo[$k]['u'];
			$aDefg[$l]['v']+= $aSumGrupo[$k]['v'];
		}
	}
	foreach ((array) $aDefGrupo0 AS $l => $val)
	{
		$aDefg0['m']+= $aDefg[$l]['m'];
		$aDefg0['n']+= $aDefg[$l]['n'];
		$aDefg0['t']+= $aDefg[$l]['t'];
		$aDefg0['u']+= $aDefg[$l]['u'];
		$aDefg0['v']+= $aDefg[$l]['v'];
	}

	$row = 18;
	$rowini = $row;
	//armamos el cuerpo
	foreach ((array) $aData AS $i => $lines)
	{
		// if ($idHilo != $lines['fk_parent'])
		//   {
		// 	$idHilo = $lines['fk_parent'];
		// 	if (!empty($a))
		// 	  {
		// 	    //imprimimos el subtotal
		// 	    if ($aSubGrupoS[$idHilo])
		// 	      {
		// 		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('SubTotal'));

		// 		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSubGrupo[$idHilo]['m']);
		// 		$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$aSubGrupo[$idHilo]['n']);
		// 		$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,$aSubGrupo[$idHilo]['t']);
		// 		$objPHPExcel->getActiveSheet()->SetCellValue('U'.$row,$aSubGrupo[$idHilo]['u']);
		// 		$objPHPExcel->getActiveSheet()->SetCellValue('V'.$row,$aSubGrupo[$idHilo]['v']);
		// 	      }

		// 	    $row++;
		// 	  }
		//   }
		$task->fetch($lines['id']);
		$var = !$var;
		$refparent = '';
		$refitem = '';
		$reftype = '';
		$unit = '';

		//verificamos si es grupo
		if ($lines['group']>0)
		{
			//es grupo
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lines['ref']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$lines['label']);
			$aGrupo[$lines['fk_parent']]=0;
			//escribimos totales
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSum[$lines['id']]['m']);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$aSum[$lines['id']]['n']);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,$aSum[$lines['id']]['t']);
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$row,$aSum[$lines['id']]['u']);
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$row,$aSum[$lines['id']]['v']);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':V'.$row)->applyFromArray($styleArrayGroup);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$row.':V'.$row)->applyFromArray($stylenumber);
			$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C'.$row.':F'.$row);
			// $objStyleA5 = $objActSheet->getStyle('B'.$row.':V'.$row);
			// $objStyleA5 ->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':V'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		}
		else
		{
			//es tarea
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lines['ref']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$lines['label']);
			$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C'.$row.':F'.$row);
			//vemos la unidad
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$lines['unit_code']);
		}
		if ($lines['group']<=0)
		{
			//registramos la cantidad inicial del contrato
			$h_ = $aContrat['c'][$lines['ref']]['unit_program']+0;
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$h_);
			//registramos la cantidad modificada del contrato
			$i_ = $aContrat['h'][$lines['ref']]['unit_program']+0;
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$i_);

			//aprobado
			$j_ = $h_ + $i_+0;
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$j_);
			//incidencia
			$k_ = 0;
			if ($h_) $k_ = $j_ / $h_ ;
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$k_);
			$l_ = $aContrat['c'][$lines['ref']]['unit_amount']+0; //precio unitario
			//unitario
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$l_);
			//inical precio
			$m_ = ($h_ * $l_)+0;
			//$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,"=".' H'.$row.' * L'.$row);
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$m_);
			$aGrupo[$lines['parent']]['m']+=$m_;

			//aprobado precio
			$n_ = ($j_ * $l_)+0;
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$n_);
			$aGrupo[$lines['parent']]['n']+=$n_;

			//incidencia
			$o_ = 0;
			if ($m_) $o_ = $n_ / $m_;
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row,$o_);

			//cantidad avance anterior
			$p_ = $aPayant[$lines['ref']]+0;
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row,$p_);
			//cantidad avance actual
			$q_ = $aPayact[$lines['ref']]+0;
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row,$q_);
			//cantidad avance total
			$r_ = $p_ + $q_;
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$row,$r_);
			//cantidad incidencia
			$r_ = $p_ + $j_;
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$row,$s_);

			//valor avance anterior
			$t_ = $l_ * $p_;
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,$t_);
			$aGrupo[$lines['fk_parent']]['t']+=$t_;

			//cantidad avance actual
			$u_ = $l_ * $q_;
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$row,$u_);
			$aGrupo[$lines['fk_parent']]['u']+=$u_;
			//cantidad avance total
			$v_ = $t_ + $u_;
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$row,$v_);
			$aGrupo[$lines['fk_parent']]['v']+=$v_;
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':V'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		}
		$objPHPExcel->getActiveSheet()->getStyle("B".$row.":V".$row)->getFont()->setSize(9);
		$row++;
		$a++;
	}

	$rowfin = $row;
	//totales
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('Total'));

	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSum[0]['m']);
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$aSum[0]['n']);
	$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,$aSum[0]['t']);
	$objPHPExcel->getActiveSheet()->SetCellValue('U'.$row,$aSum[0]['u']);
	$objPHPExcel->getActiveSheet()->SetCellValue('V'.$row,$aSum[0]['v']);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':V'.$row)->applyFromArray($styleArrayGroup);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':V'.$row)->applyFromArray($stylenumber);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$row.':F'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':V'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
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

	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);

	// $objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,'= suma(M'.$rowini.':M'.$rowfin.')');
	// $objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,'= suma(M'.$rowini.':N'.$rowfin.')');
	// $objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,'= suma(T'.$rowini.':T'.$rowfin.')');
	// $objPHPExcel->getActiveSheet()->SetCellValue('U'.$row,'= suma(U'.$rowini.':U'.$rowfin.')');
	// $objPHPExcel->getActiveSheet()->SetCellValue('V'.$row,'= suma(V'.$rowini.':V'.$rowfin.')');

	//segunda hoja
	$pos = 1;
	$objPHPExcel->createSheet($pos); //Loque mencionaste
	$objPHPExcel->setActiveSheetIndex($pos); //Seleccionar la pestaña deseada
	$objPHPExcel->getActiveSheet()->setTitle('Resumen certificado'); //Establecer nombre para la pestaña
	//titulos
	$objPHPExcel->getActiveSheet()->SetCellValue('B6',$langs->trans('CONTRATANTE'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D6',$societe_contracting);
	$objPHPExcel->getActiveSheet()->SetCellValue('J6',$langs->trans('INFORME PERIODICO Nº'));
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('J6:L6');
	$objPHPExcel->getActiveSheet()->SetCellValue('M6',$objpay->ref);

	$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('CONTRATISTA'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D7',$societe_contratist);
	$objPHPExcel->getActiveSheet()->SetCellValue('J7',$langs->trans('FECHA DE PRESENTACION'));
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('J7:L7');
	$objPHPExcel->getActiveSheet()->SetCellValue('M7',dol_print_date($objpay->date_payment,'day'));
	$objPHPExcel->getActiveSheet()->getStyle('J6:L7')->applyFromArray($styleHeadRight);
	$objPHPExcel->getActiveSheet()->getStyle('M6:M7')->applyFromArray($styleHeadLeft);

	$objPHPExcel->getActiveSheet()->SetCellValue('B8',$langs->trans('OBRA'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D8',$object->description);
	$objPHPExcel->getActiveSheet()->SetCellValue('B9',$langs->trans('SUPERVISION'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D9',$societe_supervising);


	$objPHPExcel->getActiveSheet()->SetCellValue('B11',$langs->trans('RESUMEN PARA CERTIFICADO DE AVANCE DE OBRA'));
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B11:M11');
	$objPHPExcel->getActiveSheet()->getStyle('B11:M11')->applyFromArray($styleHead);
	$objPHPExcel->getActiveSheet()->getStyle("B6:M11")->getFont()->setSize(10);


	//cabecera
	$objPHPExcel->getActiveSheet()->SetCellValue('B13','OBRA AUTORIZADA SEGÚN CONTRATO');
	$objPHPExcel->getActiveSheet()->SetCellValue('K13','OBRA EJECUTADA');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B13:J13');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('K13:M13');

	$objPHPExcel->getActiveSheet()->SetCellValue('H14','CONTRATO ORIGINAL');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H14:J14');
	$objPHPExcel->getActiveSheet()->SetCellValue('K14','MONTO EJECUTADO EN ');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('K14:M14');
	//nueva linea
	$objPHPExcel->getActiveSheet()->SetCellValue('B15','Nº');
	$objPHPExcel->getActiveSheet()->SetCellValue('D15','DESCRIPCIÓN');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('D15:F15');
	$objPHPExcel->getActiveSheet()->SetCellValue('G15','UNIDAD');
	$objPHPExcel->getActiveSheet()->SetCellValue('H15','CANTIDAD');
	$objPHPExcel->getActiveSheet()->SetCellValue('I15','IMPORTE');
	$objPHPExcel->getActiveSheet()->SetCellValue('J15','IMPORTE');
	$objPHPExcel->getActiveSheet()->SetCellValue('K15','IMPORTE');
	$objPHPExcel->getActiveSheet()->SetCellValue('L15','IMPORTE');
	$objPHPExcel->getActiveSheet()->SetCellValue('M15','IMPORTE');

	//LINEA 16
	$objPHPExcel->getActiveSheet()->SetCellValue('H16','INICIAL');
	$objPHPExcel->getActiveSheet()->SetCellValue('I16','INICIAL');
	$objPHPExcel->getActiveSheet()->SetCellValue('J16','APROBADA');
	$objPHPExcel->getActiveSheet()->SetCellValue('K16','ANTERIOR');
	$objPHPExcel->getActiveSheet()->SetCellValue('L16','ACTUAL');
	$objPHPExcel->getActiveSheet()->SetCellValue('M16','APROBADO');
	//LINEA 17
	$objPHPExcel->getActiveSheet()->SetCellValue('B17','0');
	$objPHPExcel->getActiveSheet()->SetCellValue('C17','1');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C17:F17');
	$objPHPExcel->getActiveSheet()->SetCellValue('G17','2');
	$objPHPExcel->getActiveSheet()->SetCellValue('H17','3');
	$objPHPExcel->getActiveSheet()->SetCellValue('I17','4');
	$objPHPExcel->getActiveSheet()->SetCellValue('J17','5');
	$objPHPExcel->getActiveSheet()->SetCellValue('K17','6');
	$objPHPExcel->getActiveSheet()->SetCellValue('L17','7');
	$objPHPExcel->getActiveSheet()->SetCellValue('M17','8 = 6 + 7');

	$objPHPExcel->getActiveSheet()->getStyle('B13:M17')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle("B13:M17")->getFont()->setSize(10);

	//CUERpo
	$row = 18;
	$rowini = $row;
	$aSumt = array();
	//armamos el cuerpo
	foreach ((array) $aData AS $i => $lines)
	{
		$task->fetch($lines['id']);
		$var = !$var;
		$refparent = '';
		$refitem = '';
		$reftype = '';
		$unit = '';

		//verificamos si es grupo
		if ($lines['group']>0)
		{
			//es grupo
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lines['ref']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$lines['label']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$langs->trans('Global'));//cantidad inicial global
			//escribimos totales
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,1);//cantidad inicial global
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$aSum[$lines['id']]['m']);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$aSum[$lines['id']]['n']);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$aSum[$lines['id']]['t']);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$aSum[$lines['id']]['u']);
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSum[$lines['id']]['v']);
			//sumando totales
			$aSumt[0]['j']+=$aSum[$lines['id']]['n'];
			$aSumt[0]['k']+=$aSum[$lines['id']]['t'];
			$aSumt[0]['l']+=$aSum[$lines['id']]['u'];
			$aSumt[0]['m']+=$aSum[$lines['id']]['v'];
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':M'.$row)->applyFromArray($styleArrayGroup);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$row.':M'.$row)->applyFromArray($stylenumber);
			$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('C'.$row.':F'.$row);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':M'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle("B".$row.":M".$row)->getFont()->setSize(9);
			$row++;
		}
	}
	//armamos los totales en Bs y porcentaje
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$langs->trans('Total en Bs.').':');
	//cantidad inicial global
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$aSumt[0]['j']);
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$aSumt[0]['k']);
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$aSumt[0]['l']);
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['m']);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$row.':I'.$row)->applyFromArray($styleRight);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$row.':M'.$row)->applyFromArray($stylenumber);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$row.':M'.$row)->applyFromArray($styleLines);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G'.$row.':I'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$row.':M'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle("B".$row.":M".$row)->getFont()->setSize(9);
	//porcentaje de avance
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$langs->trans('Porcentaje avance obra').':');
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,round($aSumt[0]['j']/$aSumt[0]['j']*100,2));
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,round($aSumt[0]['k']/$aSumt[0]['j']*100,2));
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,round($aSumt[0]['l']/$aSumt[0]['j']*100,2));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,round($aSumt[0]['m']/$aSumt[0]['j']*100,2));
	$objPHPExcel->getActiveSheet()->getStyle('G'.$row.':I'.$row)->applyFromArray($styleRight);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$row.':M'.$row)->applyFromArray($stylenumber);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$row.':M'.$row)->applyFromArray($styleLines);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('G'.$row.':I'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$row.':M'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle("B".$row.":M".$row)->getFont()->setSize(9);

	//porcentaje de avance
	$format_percent =  array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
	$row++;
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('PORCENTAJE DE AVANCE DE OBRA ACTUAL').':');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aSumt[0]['l']/$aSumt[0]['j']);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':E'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleLeft);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('F'.$row.':G'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($stylenumber);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($format_percent);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($styleLines);

	//$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

	$objPHPExcel->getActiveSheet()->getStyle("B".$row.":G".$row)->getFont()->setSize(9);
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('PORCENTAJE DE AVANCE DE OBRA ACUMULADO').':');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aSumt[0]['m']/$aSumt[0]['j']);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':E'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleLeft);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('F'.$row.':G'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($stylenumber);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($format_percent);
	//$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($styleLines);
	$objPHPExcel->getActiveSheet()->getStyle("B".$row.":G".$row)->getFont()->setSize(9);
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('PORCENTAJE DE AVANCE DE OBRA POR EJECUTAR').':');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,($aSumt[0]['j']-$aSumt[0]['m'])/$aSumt[0]['j']);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':E'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleLeft);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('F'.$row.':G'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($stylenumber);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($format_percent);
	//$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

	$objPHPExcel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->applyFromArray($styleLines);
	$objPHPExcel->getActiveSheet()->getStyle("B".$row.":G".$row)->getFont()->setSize(9);



	$rowfin = $row;

	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);

	//TERCERA hoja
	$pos = 2;
	$objPHPExcel->createSheet($pos); //Loque mencionaste
	$objPHPExcel->setActiveSheetIndex($pos); //Seleccionar la pestaña deseada
	$objPHPExcel->getActiveSheet()->setTitle('Certificado'); //Establecer nombre para la pestaña
	//titulos
	$objPHPExcel->getActiveSheet()->SetCellValue('B6',$langs->trans('CONTRATANTE'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D6',$societe_contracting);
	$objPHPExcel->getActiveSheet()->SetCellValue('J6',$langs->trans('INFORME PERIODICO Nº'));
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('J6:L6');
	$objPHPExcel->getActiveSheet()->SetCellValue('M6',$objpay->ref);

	$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('CONTRATISTA'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D7',$societe_contratist);
	$objPHPExcel->getActiveSheet()->SetCellValue('J7',$langs->trans('FECHA DE PRESENTACION'));
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('J7:L7');
	$objPHPExcel->getActiveSheet()->SetCellValue('M7',dol_print_date($objpay->date_payment,'day'));
	$objPHPExcel->getActiveSheet()->getStyle('J6:L7')->applyFromArray($styleHeadRight);
	$objPHPExcel->getActiveSheet()->getStyle('M6:M7')->applyFromArray($styleHeadLeft);

	$objPHPExcel->getActiveSheet()->SetCellValue('B8',$langs->trans('OBRA'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D8',$object->description);
	$objPHPExcel->getActiveSheet()->SetCellValue('B9',$langs->trans('SUPERVISION'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D9',$societe_supervising);


	$objPHPExcel->getActiveSheet()->SetCellValue('B11',$langs->trans('RESUMEN DEL CERTIFICADO DE PAGO'));
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B11:M11');
	$objPHPExcel->getActiveSheet()->getStyle('B11:M11')->applyFromArray($styleHead);
	$objPHPExcel->getActiveSheet()->getStyle("B6:M11")->getFont()->setSize(10);

	//datos del contrato
	$objPHPExcel->getActiveSheet()->SetCellValue('B13','FECHA DE INICIO:');
	$objPHPExcel->getActiveSheet()->SetCellValue('E13',$object->dateini);
	$objPHPExcel->getActiveSheet()->SetCellValue('K13','CODIGO:');
	$objPHPExcel->getActiveSheet()->SetCellValue('K13',$object->ref);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B13:C13');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('K13:L13');

	$objPHPExcel->getActiveSheet()->SetCellValue('B14','PLAZO S/CONTRATO:');
	$objPHPExcel->getActiveSheet()->SetCellValue('E14',$plazo);
	$objPHPExcel->getActiveSheet()->SetCellValue('F14',$tipoplazo);
	$objPHPExcel->getActiveSheet()->SetCellValue('K14','NRO. DE CONTRATO:');
	$objPHPExcel->getActiveSheet()->SetCellValue('K14',$contrat->ref);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B14:C14');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('K14:L14');

	$objPHPExcel->getActiveSheet()->SetCellValue('B15','CONCLUSION S/CONTRATO:');
	$objPHPExcel->getActiveSheet()->SetCellValue('E15',$fechafinal);
	$objPHPExcel->getActiveSheet()->SetCellValue('K15','FECHA DE CONTRATO:');
	$objPHPExcel->getActiveSheet()->SetCellValue('K15',$contrat->ref);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B15:C15');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('K15:L15');

	$objPHPExcel->getActiveSheet()->getStyle('B13:M15')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('B13:M15')->getFont()->setSize(10);

	//valores del contrato
	$objPHPExcel->getActiveSheet()->SetCellValue('I18','IMPORTE CONTRATO');
	$objPHPExcel->getActiveSheet()->SetCellValue('L18','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M18',$aTypeContrat[1]);
	//modificaciones
	$objPHPExcel->getActiveSheet()->SetCellValue('B19','Modificacion al contrato por Orden de trabajo');
	$objPHPExcel->getActiveSheet()->SetCellValue('L19','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M19',$aTypeContrat[2]);

	$objPHPExcel->getActiveSheet()->SetCellValue('B20','Modificacion al contrato por Orden de cambio');
	$objPHPExcel->getActiveSheet()->SetCellValue('L20','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M20',$aTypeContrat[3]);

	$objPHPExcel->getActiveSheet()->SetCellValue('B21','Modificacion al contrato por Contrato modificatorio');
	$objPHPExcel->getActiveSheet()->SetCellValue('L21','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M21',$aTypeContrat[4]);


	$objPHPExcel->getActiveSheet()->SetCellValue('B22','TOTAL AUTORIZADO');
	$objPHPExcel->getActiveSheet()->SetCellValue('L22','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M22',$aSum[0]['n']);

	//valores del contrato
	$objPHPExcel->getActiveSheet()->SetCellValue('I25','IMPORTE PRESENTE CERTIFICADO');
	$objPHPExcel->getActiveSheet()->SetCellValue('L25','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M25',$objpay->amount);
	//deducciones
	$objPHPExcel->getActiveSheet()->SetCellValue('B26','Deduccion por anticipo de obra');
	$objPHPExcel->getActiveSheet()->SetCellValue('L26','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M26',$aDeducact['ADVANCE']);
	//deducciones2
	$objPHPExcel->getActiveSheet()->SetCellValue('B27','Deduccion por protocolizacion de contrato');
	$objPHPExcel->getActiveSheet()->SetCellValue('L27','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M27',$aDeducact['PROTOCOLIZ']);
	//deducciones3
	$objPHPExcel->getActiveSheet()->SetCellValue('B28','Deduccion por otras retenciones');
	$objPHPExcel->getActiveSheet()->SetCellValue('L28','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M28',$aDeducact['OTHER']);

	//total por pagar
	$m29_ = $objpay->amount - $aDeducact['ADVANCE'] - $aDeducact['PROTOCOLIZ'] - $aDeducact['OTHER'];
	$objPHPExcel->getActiveSheet()->SetCellValue('J29','IMPORTE POR PAGAR');
	$objPHPExcel->getActiveSheet()->SetCellValue('L29','Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M29',$m29_);

	//importe neto
	$objPHPExcel->getActiveSheet()->SetCellValue('B30','IMPORTE NETO POR PAGAR EN EL PRESENTE CERTIFICADO: ');
	$objPHPExcel->getActiveSheet()->SetCellValue('B31','SON: ');
	//numeral monto
	$m29_ = price2num($m29_,'MT');
	$cn = new NumerosALetras();
	$leter = $cn->traducir($m29_, 'Bolivianos');

	$objPHPExcel->getActiveSheet()->SetCellValue('C31',$leter);

	//balance de obra
	$objPHPExcel->getActiveSheet()->SetCellValue('B33','BALANCE ACTUAL DE LA OBRA');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B33:M33');
	$objPHPExcel->getActiveSheet()->getStyle('B33:M33')->applyFromArray($styleCenter);
	//$objPHPExcel->getActiveSheet()->getStyle('B33:M33')->applyFromArray($styleHead);
	$objPHPExcel->getActiveSheet()->getStyle("B6:M11")->getFont()->setSize(10);

	//amortizacion
	$row = 35;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'AMORTIZACION DE ANTICIPO Y RETENCIONES');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':G'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':M'.$row)->applyFromArray($styleCenter);
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,'AVANCE DE OBRA');
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('H'.$row.':M'.$row);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row.':M'.$row)->applyFromArray($styleCenter);

	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Por protocolizacion de contrato');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aRetention['PROTOCOLIZ']+0);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Importe certificados anteriores');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['k']);

	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Retenido anterior protocolizacion:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aDeducant['PROTOCOLIZ']+0);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Incidencia');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['k']/$aSumt[0]['j']);

	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Retenido actual protocolizacion:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aDeducact['PROTOCOLIZ']+0);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$row++;
	$tprotocoliz = $aDeducant['PROTOCOLIZ']+$aDeducact['PROTOCOLIZ'];
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Total pago por protocolizacion:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$tprotocoliz);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Importe presente certificado');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['l']);

	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Total por amortizar por protocolizacion:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aRetention['PROTOCOLIZ']-$aDeducant['PROTOCOLIZ']-$aDeducact['PROTOCOLIZ']);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Incidencia');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['l']/$aSumt[0]['j']);
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Por anticipo de:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aRetention['ADVANCE']+0);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Total certificados a la fecha:');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['k']+$aSumt[0]['l']);

	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Retenido anterior por anticipo:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aDeducant['ADVANCE']+0);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Incidencia');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,($aSumt[0]['k']+$aSumt[0]['l'])/$aSumt[0]['j']);

	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Retenido actual por anticipo:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aDeducact['ADVANCE']+0);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$row++;
	$tadvance = $aDeducant['ADVANCE'] + $aDeducact['ADVANCE'];

	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Total amortizado por anticipo:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$tadvance);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,'Bs.');

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Importe por ejecutar');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$aSumt[0]['j']-$aSumt[0]['k']-$aSumt[0]['l']);

	$row++;

	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Total por amortizar por anticipo:');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$aRetention['ADVANCE']-$tadvance);

	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,'Incidencia');
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'Bs.');
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,($aSumt[0]['j']-$aSumt[0]['k']-$aSumt[0]['l'])/$aSumt[0]['j']);

	$objPHPExcel->getActiveSheet()->getStyle('M17:M47')->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('F37:F47')->getNumberFormat()->setFormatCode('#,##0.00');

	$row++;
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$objpay->detail);
	$objPHPExcel->setActiveSheetIndex($pos)->mergeCells('B'.$row.':B'.$row);
	$row++;
	$row++;
	$row++;
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,'Firma y sello solicitante');
	$row++;
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,'Contratista');
	$row++;
	$row++;
	$row++;
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,'Firma autorizado conforme');
	$row++;
	$row++;
	$row++;
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,'Supervision de obra');

	$objPHPExcel->getActiveSheet()->getStyle("B16:M".$row)->getFont()->setSize(10);




	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	//echo '<hr>escribe';exit;
	$objWriter->save("excel/exporttask.xlsx");
	$_SESSION['docsave'] = 'exporttask.xlsx';

	header('Location: '.DOL_URL_ROOT.'/monprojet/fiche_export.php');

}
}
