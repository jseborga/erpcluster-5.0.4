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
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//budget
if ($conf->budget->enabled)
{
	//require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
	dol_include_once('/budget/class/typeitemext.class.php');
}

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formotheradd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

//request
if ($conf->request->enabled)
{
	dol_include_once('/request/class/request.class.php');
	dol_include_once('/request/class/requestitem.class.php');
}

$langs->load("users");
$langs->load("projects");
$langs->load("monprojet@monprojet");
$langs->load("request@request");

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$idpay = GETPOST('idpay', 'int');
$ref = GETPOST('ref', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$cancel=GETPOST('cancel');
$newsel=GETPOST('newsel', 'int');
$selstatut=GETPOST('selstatut','int');
if (isset($_GET['selstatut']))
	$_SESSION['selstatut'] = $selstatut+0;

$selstatut = $_SESSION['selstatut']+0;


$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);

$table = 'llx_projet_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
//armamos los campos obligatorios
$aHeaderTpl['llx_projet_task'] = array('ref' => 'ref',
	'label' => 'label',
	'hilo' => 'hilo',
	'login' => 'login',
	'fechaini'=>'fechaini',
	'fechafin'=>'fechafin',
	'group'=>'group',
	'type'=>'type',
	'typename'=>'typename',
	'unitprogram'=>'unitprogram',
	'unit'=>'unit',
	'price' => 'price',);
$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aCampodate = array('fechaini' =>'date_start',
	'fechafin' => 'date_end');


//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object    = new Projectext($db);
$objectadd = new Projectext($db);
$objecttaskadd = new Projettaskadd($db);
$objecttime = new Projettasktimedoc($db);
//regisro de avances
$taskstatic = new Task($db);
$taskadd = new Taskext($db);
 //nueva clase para listar tareas
$objpay  = new Projetpayment($db);
$taskpay = new Projettaskpayment($db);
$objuser = new User($db);
//$cunits  = new Cunits($db);
//request
if ($conf->request->enabled)
{
	$request = new Request($db);
	$requestitem = new Requestitem($db);
}
//priceunit
if ($conf->budget->enabled)
{
	$typeitem = new Typeitemext($db);
	$items = new Itemsext($db);
}

$extrafields_project = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

if ($id > 0 || ! empty($ref))
{
	// fetch optionals attributes and labels
	$extralabels_projet=$extrafields_project->fetch_name_optionals_label($object->table_element);
}
$extralabels_task=$extrafields_task->fetch_name_optionals_label($taskstatic->table_element);
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
$planned_workloadhour=GETPOST('planned_workloadhour');
if(empty($planned_workloadhour)) $planned_workloadhour=0;
$planned_workloadmin = GETPOST('planned_workloadmin');
if (empty($planned_workloadmin)) $planned_workloadmin = 0;
$planned_workload=$planned_workloadhour*3600+$planned_workloadmin*60;

$userAccess=0;
$lDisabled = false;
$filterpay = false;

//modifica las fechas de tareas grupo
updatedategroup($id);
if ($user->admin && $conf->global->MONPROJET_ACTUALIZA_ADVANCE && GETPOST('actadv'))
{
	updatetaskadvance($id);
}
//exit;
if ($_GET['upitem'] == 'uup' && $user->admin)
{
	$object->fetch($id);
	$aTasknumref = unserialize($_SESSION['aTasknumref'][$object->id]);
	if ($object->id == $id && count($aTasknumref)>0)
	{
		$fk_projet = $object->id;
		include DOL_DOCUMENT_ROOT.'/monprojet/lib/update_orderref.lib.php';
	}
}
//actualiza($object,$extralabels_task,$objecttaskadd);

/*
 * Actions
 */

if ($user->rights->monprojet->task->crear)
	$userWrite = true;

if ($action == 'confirm_verifpay' && ($_REQUEST["confirm"] == 'no' || GETPOST('cancel'))) $action='paymen';

if ($action == 'confirm_verifpay' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->payp->pay)
{
	$object->fetch($id);

	//generar planilla
	$_POST = $_SESSION['aPost'];
	//este proceso actualiza el time_doc con el pago
	$res = $objpay->fetch(GETPOST('idpay'));
	if ($res>0 && $objpay->id == $idpay)
	{
		$_POST = $_SESSION['aPost'];
		$error = 0;
		//iniciamos
		$db->begin();
		$objpay->ref = $_POST['ref']+0;
		if (!$objpay->ref > 0) $error++;
		$objpay->date_payment = dol_mktime($_POST['dohour'],$_POST['domin'],0,$_POST['domonth'],$_POST['doday'],$_POST['doyear'],'user');
		$objpay->amount = $_POST['amount'];
		$objpay->statut = 2;
		$objpay->detail = GETPOST('detail');
		//validado
		if (!$error)
		{
			$res = $objpay->update($user);
			if (!$res>0) $error++;
		}
		if (!$error)
		{
			//vamos cambiando de estado en taskpay, time_doc
			$aSelpay = $_SESSION['aSelectlast'];
			$statut = 3;
			foreach ((array) $aSelpay AS $fk_pay => $value)
			{
				//buscamos y cambiamos de estado 1 a 2
				$res = $taskpay->fetch($fk_pay);
				if ($res>0 && $taskpay->id == $fk_pay)
				{
					//primero actualizamos la tabla time_doc
					$filter = array(1=>1);
					$filterstatic = " AND p.fk_task = ".$taskpay->fk_task;
					$filterstatic.= " AND t.statut = 1 ";
					$filterstatic.= " AND t.date_create <= ".$db->idate($objpay->date_payment);
					$numdoc = $objecttime->fetchAlltime('', '', 0, 0, $filter,'AND',$filterstatic,false);
					$objdocnew = new Projettasktimedoc($db);
					foreach((array) $objecttime->lines AS $j => $line)
					{
						$resu = $objdocnew->fetch($line->id);
						if ($resu>0 && $objdocnew->id == $line->id)
						{
							$objdocnew->fk_task_payment = $fk_pay;
							$objdocnew->statut = 2;
							$resn = $objdocnew->update($user);
							if (!$resn > 0) $error++;
						}
						else
							$error++;
					}
					//cambiamos de estado
					if (!$error)
					{
						$taskpay->statut = $statut;
						$res = $taskpay->update($user);
						if (!$res>0) $error++;
					}
				}
				else
					$error++;
			}
		  	//foreach aselpay
		}

		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=paymen');
			exit;
		}
		else $db->rollback();
	}
	$action = 'paymen';
}

if ($action == 'confirm_deletetask' && GETPOST('cancel')) $action='list';
//action giveback
if ($action == 'confirm_deletetask' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->task->del)
{
	$objectadd->fetch($id);
	if ($objectadd->id == $id && $object->statut == 0)
	// activar para validar borrado
	{
		$res = $objectadd->delete_task($user);
		if (!$res > 0)
			$error++;
		if (!$error)
		{
			$db->commit();
			header("Location: ".DOL_URL_ROOT.'/monprojet/tasks.php?id='.$id);
			exit;
		}
	}
	else
	{
		setEventMessages(null,$langs->trans("Errordonotexistproject"),'errors');
	}
	$action = 'list';
}

if ($action == 'selpay' && (isset($_REQUEST['paydis']) || isset($_REQUEST['payapp'])))
{
	$error = 0;
	$aSelpay = GETPOST('selpay');
	$_SESSION['aSelectlast'] = $aSelpay;
	if (empty($aSelpay))
		setEventMessages(null,$langs->trans("Errordonotselectanytasks"),'errors');

	$db->begin();
	foreach ((array) $aSelpay AS $fk_pay => $value)
	{
		if ($_REQUEST['paydis']) $statut = 1;
		if ($_REQUEST['payapp']) $statut = 2;
		if ($_REQUEST['paycreate']) $statut = 3;
		//buscamos y cambiamos de estado 1 a 2
		$res = $taskpay->fetch($fk_pay);
		if ($res>0 && $taskpay->id == $fk_pay)
		{
			$taskpay->statut = $statut;
			$res = $taskpay->update($user);
			if (!$res>0) $error++;
		}
		else
			$error++;
	}
	// //$res = $objecttime->update_approve($fk_task,$statut);
	// $res = $taskpay->update_approve($fk_task,$statut);
	if (!$error)
	{
		$db->commit();
	}
	else
	{
		$db->rollback();
		setEventMessage($langs->trans("Errorapprove",$langs->transnoentitiesnoconv("Items")),'errors');

	}
	$action = 'paymen';
}

if ($action == 'selpay' && isset($_REQUEST['paycreate']))
{
	$aSelpay = GETPOST('selpay');
	$_SESSION['aSelectlast'] = $aSelpay;
	if (empty($aSelpay))
	{
		setEventMessages(null,$langs->trans("Errordonotselectanytasks"),'errors');
		$action = 'paymen';
	}
	else
	{
		$_SESSION['aSelpay'] = GETPOST('selpay');
	//procesamos el pago
		$filterpay = true;
		$action = 'createpay';
	}
}


if ($action == 'addup')
{
	//MODIFICADO
	$error = 0;
	//buscamos el projet
	$res = $object->fetch($id);
	if ($res<=0) $error++;
	//recuperamos el aTasknumref
	$aTasknumref = unserialize($_SESSION['aTasknumref'][$object->id]);
	//revisamos si existe tareas en el proyecto

	$aArrData = $_SESSION['aArrData'];
	$table = GETPOST('table');
	$aNewTask = array();
	$aLevel = array();
	$lUtility = true;
	$db->begin();
	foreach ((array) $aArrData AS $i => $data)
	{
		//vamos verificando la existencia de cada uno
		$fk_task_parent = 0;
		if (!empty($data['hilo']))
		{
			if (!empty($aNewTask[$data['hilo']]))
				$fk_task_parent = $aNewTask[$data['hilo']];
			else
				$error++;
		}

		//unit
		$fk_unit = 0;
		if (!empty($data['unit']))
		{
			$cunits = fetch_unit('',$data['unit']);
			if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
			{
				//recuperamos el id de registro
				$fk_unit = $cunits->rowid;
			}
			else
			{
				//creamos
				//$cunits->initAsSpecimen();
				//$cunits->code= $data['unit'];
				//$cunits->label= $data['unitlabel'];
				//$cunits->short_label= $data['unit'];
				//$cunits->active= 1;
				//$resunit = $cunits->create($user);
				//if ($resunit >0) $fk_unit = $resunit;
				//else $error++;
				$error++;
				setEventMessages($langs->trans('Error, no existe la unidad de medida').' <b>'.$data['unit'].'</b>, '.$langs->trans('revise'),null,'errors');
			}
		}
		//verificamos si esta relacionado a un item
		if ($conf->budget->enabled)
		{
			$fk_item = 0;
			if (!empty($data['item']))
			{
			//buscamos
				$resitem = $items->fetch('',$data['item']);
				if ($resitem>0)
				{
					if (STRTOUPPER($items->ref) == STRTOUPPER($data['item']))
					{
						$_POST['options_fk_item'] = $items->id;
						$fk_item = $items->id;
					}
				}
				else
				{
					$error++;
				}
			}
			else
				$_POST['options_fk_item'] = 0;

			//verificamos tipo item
			$fk_type_item = 0;
			if (!empty($data['type']))
			{
			//buscamos
				$restype = $typeitem->fetch('',$data['type']);
				if ($restype>0)
				{
					if (STRTOUPPER($typeitem->ref) == STRTOUPPER($data['type']))
					{
						$_POST['options_fk_type'] = $typeitem->id;
						$fk_type_item = $typeitem->id;
					}
				}
				elseif($restype==0)
				{
				//agregamos
					$typeitem->entity = $conf->entity;
					$typeitem->ref = $data['type'];
					$typeitem->detail = $data['typename'];
					$typeitem->fk_user_create = $user->id;
					$typeitem->fk_user_mod = $user->id;
					$typeitem->date_create = dol_now();
					$typeitem->tms = dol_now();
					$typeitem->statut = 1;
					echo '<hr>typerest '.$rest = $typeitem->create($user);
					if ($rest<=0) $error++;
					else
					{
						$fk_type_item = $rest;
						$_POST['options_fk_type'] = $rest;
					}
				}
				else
				{
					$error++;
				}
			}
			else
				$_POST['options_fk_type'] = 0;
		}
		if ($error)
		{
			if ($lUtility)
			{
				$lUtility = false;
				setEventMessage($langs->trans("Errorutilities",$langs->transnoentitiesnoconv("Items")),'errors');
			}
		}
		//verificamos las fechas
		$date_start = getformatdate($seldate,$data['fechaini']);
		$date_end   = getformatdate($seldate,$data['fechafin']);

		//buscamos si existe la tarea
		$tasknewadd = new Taskext($db);
		$filter = array(1=>1);
		$filterstatic = " AND t.ref = '".$data['ref']."'";
		$filterstatic.= " AND t.fk_projet = ".$object->id;
		$res = $tasknewadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		if ($res>0)
		{
			foreach ($tasknewadd->lines AS $k => $obj)
			{
				if (STRTOUPPER($obj->ref) == STRTOUPPER($data['ref']) &&
					$obj->fk_project == $object->id)
				{
					$task = new Task($db);
					if ($task->fetch($obj->id)>0)
					{
						//buscamos si existe el campo con formato fecha
						foreach((array) $aCampodate AS $k => $value)
						{
							if($data[$k])
							{
								//verificamos y damos formato a la variable
								$resvalue = convertdate($aDatef,$seldate,$data[$k]);
								$task->$value = $resvalue;
							}
						}

						$aNewTask[$data['ref']] = $task->id;
						//actualizamos el valor
						$_POST['options_c_grupo'] = $data['group'];
						$_POST['options_c_view'] = $data['view'];
						$_POST['options_unit_program'] = $data['unitprogram'];
						$_POST['options_fk_unit'] = $fk_unit;
						$_POST['options_unit_amount'] = $data['price'];
						//$task->dateo = $date_start;
						//$task->datee = $date_end;
						$task->fk_task_parent = $fk_task_parent +0;
						$task->ref = $data['ref'];
						$task->label = $data['label'];
						$task->description = $data['detail'];
						$task->priority = $data['priority']+0;
						$task->rang = $i;
						$task->tms = dol_now();
						if (empty($fk_task_parent)) $level = 0;
						else $level = $aLevel[$fk_task_parent]+1;
						$aLevel[$task->id] = $level;
						// Fill array 'array_options' with data from add form
						$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
						$aTasknumref[$task->id] = array('fk_task_parent'=>$fk_task_parent,'ref'=> $task->ref,'level'=>$level,'reg'=>$i,'group'=>$data['group']);
						if (!$ret > 0)
						{
							$error++;
						}
						//actualizamos datos adicionales de la tarea
						$res = $objecttaskadd->fetch('',$task->id);
						if ($res>0 && $objecttaskadd->fk_task == $task->id)
						{
							$objecttaskadd->fk_item = $fk_item;
							$objecttaskadd->fk_type = $fk_type_item+0;
							$objecttaskadd->c_grupo = $data['group'];
							$objecttaskadd->level = $level;
							$objecttaskadd->unit_program = $data['unitprogram']+0;
							$objecttaskadd->fk_unit = $fk_unit;
							$objecttaskadd->unit_amount = $data['price']+0;
							$objecttaskadd->fk_user_mod = $user->id;
							$objecttaskadd->tms = dol_now();
							$objecttaskadd->detail_close = '';
							$res = $objecttaskadd->update($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
							}
						}
						else
						{
							$objecttaskadd->fk_task = $task->id;
							$objecttaskadd->fk_item = $fk_item;
							$objecttaskadd->fk_type = $fk_type_item+0;
							$objecttaskadd->c_grupo = $data['group'];
							$objecttaskadd->level = $level;
							$objecttaskadd->unit_program = $data['unitprogram']+0;
							$objecttaskadd->fk_unit = $fk_unit;
							$objecttaskadd->unit_amount = $data['price']+0;
							$objecttaskadd->fk_user_create = $user->id;
							$objecttaskadd->fk_user_mod = $user->id;
							$objecttaskadd->date_create = dol_now();
							$objecttaskadd->tms = dol_now();
							$objecttaskadd->statut = 1;
							$res = $objecttaskadd->create($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
							}
						}
						if (!$error)
						{

							$resup = $task->update($user,true);
							if ($resup<=0)
							{
								$error++;
								setEventMessages($task->error,$task->errors,'errors');

							}
						}
					}
				}
			}
		}
		else
		{
			//creamos nuevo
			$_POST['options_c_grupo'] = $data['group'];
			$_POST['options_c_view'] = $data['view'];
			$_POST['options_unit_program'] = $data['unitprogram'];
			$_POST['options_fk_unit'] = $fk_unit;
			$_POST['options_unit_amount'] = $data['price'];
			$task = new Task($db);
			$task->initAsSpecimen();
			//buscamos si existe el campo con formato fecha
			foreach((array) $aCampodate AS $k => $value)
			{
				if($data[$k])
				{
					//verificamos y damos formato a la variable
					$resvalue = convertdate($aDatef,$seldate,$data[$k]);
					$task->$value = $resvalue;
				}
			}

			$task->entity = $conf->entity;
			$task->fk_project = $id;
			$task->fk_task_parent = $fk_task_parent +0;
			$task->ref = $data['ref'];
			$task->label = $data['label'];
			$task->description = $data['detail'];
			$task->fk_user_creat = $user->id;
			$task->priority = $data['priority']+0;
			$task->fk_statut = 1;
			$task->date_c = dol_now();
			$task->tms = dol_now();
			$task->progress = 0;


			// Fill array 'array_options' with data from add form
			$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
			//echo '<hr>new '.
			$result = $task->create($user,1);
			$aNewTask[$data['ref']] = $result;
			if ($result<=0)
			{
				$error++;
				setEventMessages($task->error,$task->errors,'errors');
			}
			if ($result>0)
			{
				if (empty($fk_task_parent))
					$level = 0;
				else
					$level = $aLevel[$fk_task_parent]+1;
				$aLevel[$result] = $level;
			}
			$aTasknumref[$result] = array('fk_task_parent'=>$fk_task_parent,'ref'=>$data['ref'],'level'=>$level,'reg'=>$i,'group'=>$data['group']);

			if (!$error)
			{
				$objecttaskadd->fk_task = $result;
				$objecttaskadd->c_grupo = $data['group'];
				$objecttaskadd->level = $level;
				$objecttaskadd->unit_program = $data['unitprogram'];
				if (empty($objecttaskadd->unit_program))$objecttaskadd->unit_program = 0;
				$objecttaskadd->fk_item = $fk_item;
				$objecttaskadd->fk_type = $fk_type_item+0;
				$objecttaskadd->fk_unit = $fk_unit;
				$objecttaskadd->unit_amount = $data['price'];
				if (empty($objecttaskadd->unit_amount)) $objecttaskadd->unit_amount=0;
				$objecttaskadd->fk_user_create = $user->id;
				$objecttaskadd->fk_user_mod = $user->id;
				$objecttaskadd->date_create = dol_now();
				$objecttaskadd->tms = dol_now();
				$objecttaskadd->statut = 1;
				//echo '<br>newadd '.
				$res = $objecttaskadd->create($user);
				if ($res<=0)
				{
					$error++;
					setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
				}
			}

			if (!$error)
			{
				//actualizamos la nueva tarea en el campo rang
				$taskstatic->fetch($result);
				if ($taskstatic->id == $result)
				{
					$taskstatic->rang = $i;
					$res = $taskstatic->update($user,true);
					if ($res<=0) $error++;
				}

				$aNewTask[$data['ref']] = $result;

				//buscamos al usuario contacto que es un array
				$aLogin = explode(';',$data['login']);
				foreach ((array) $aLogin AS $l => $login)
				{
					$resuser = $objuser->fetch('',$login);
					if ($resuser>0)
						$result = $task->add_contact($objuser->id, 'TASKEXECUTIVE', 'internal');
				}
			}
			else
			{
				$error++;
				setEventMessages($task->error,$task->errors,'errors');
			}
		}
	}
	if (empty($error))
	{
		//ordenamos y actualizamos
		$aRef = array();
		$aNumberref = array();
		$aRefnumber = array();
		foreach((array) $aTasknumref AS $i => $data)
		{
		//verificamos el orden donde debe estar la tarea
			list($aRef,$aNumberref,$aRefnumber) = get_orderlastnew($i,$object->id,$data,$aRef,$aNumberref,$aRefnumber);
		}
		foreach ((array) $aNumberref AS $i => $value)
		{
			$objecttaskadd->fetch('',$i);
			if ($objecttaskadd->fk_task == $i)
			{
				//echo '<br>'.$i.' '.$value;
				$objecttaskadd->order_ref = $value;
				$res = $objecttaskadd->update_orderref();
				if ($res < 0) $error++;
			}
			else
			{
				$error++;
				//echo '<br>no encuentra '.$i;
			}
		}
	}
	//ordenamos las tareas por el order_ref
	if (empty($error))
	{
		//echo '<hr>antes de actualizar el order '.$error;
		$tasknewadd->get_ordertask($object->id);
		$taskaddnew = new Taskext($db);
		//echo '<br>cuentalines '.count($taskadd->lines).' del id '.$projectstatic->id;
		if (count($tasknewadd->lines)>0)
		{
			$j = 1;
			foreach($tasknewadd->lines AS $i => $data)
			{
				$fk = $data->id;
				$res = $taskaddnew->fetch($fk);
				if ($res >0)
				{
					//echo '<br>procesando el reemplazo a '.$j .' de '.$taskstatic->rang.' delid '.$data->id.'|'.$fk.' encontrado |'.$taskaddnew->id.'|';
					$taskaddnew->rang = $j;
					$res = $taskaddnew->update_rang($user);
					if ($res <= 0) $error++;
					$j++;
				}
				else
					$error++;
			}
		}
	}

	if (empty($error))
	{
		$db->commit();
	}
	else
	{
		setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
		$db->rollback();
	}
	$action = 'list';
	//echo '<br>action '.$action;
}

// Part to create upload
if ($action == 'veriffile')
{
	//verificacion
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];
	$separator = GETPOST('separator','alpha');
	$tempdir = DOL_DOCUMENT_ROOT."/monprojet/tmp/";

	//    if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	if($tmp_name && dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo,1,10,0,$nombre_archivo))
	{
		//echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	if (file_exists($tempdir.$nombre_archivo))
	{
		$csvfile = $tempdir.$nombre_archivo;
		$fh = fopen($csvfile, 'r');
		$headers = fgetcsv($fh);
		$aHeaders = explode($separator,$headers[0]);
		$data = array();
		$aData = array();
		while (! feof($fh))
		{
			$row = fgetcsv($fh,'','^');
			if (!empty($row))
			{
				$aData = explode($separator,$row[0]);
				$obj = new stdClass;
				$obj->none = "";
				foreach ($aData as $i => $value)
				{
					$key = $aHeaders[$i];
					if (!empty($key))
						$obj->$key = $value;
					else
						$obj->none = $value." xx";
				}
				$data[] = $obj;
			}
		}
		fclose($fh);

		$c=0;
		$action = "verifup";
	}
	else
	{
		echo 'no existe el archivo';
		exit;
	}
}


if ($action == 'createtask' && $user->rights->monprojet->task->crear)
{
	//MODIFICADO
	$error=0;
	$date_start = dol_mktime($_POST['dateohour'],$_POST['dateomin'],0,$_POST['dateomonth'],$_POST['dateoday'],$_POST['dateoyear'],'user');
	$date_end = dol_mktime($_POST['dateehour'],$_POST['dateemin'],0,$_POST['dateemonth'],$_POST['dateeday'],$_POST['dateeyear'],'user');
	if (! $cancel)
	{
		$ref = GETPOST('ref','alpha');
		if (!empty($newsel))
		{
			//buscamos el item
			$items->fetch('',$ref);
			if ($items->ref == $ref)
			{
				$_POST['options_fk_unit'] = $items->fk_unit;
				$_POST['options_fk_item'] = $items->id;
				$label = $items->detail;
				$ref = $items->ref;
			}
			else
				$error++;
		}
		else
		{
			$ref='';
			$label = GETPOST('label','alpha');
		}
		//le damos un nuevo numero de referencia
		$obj = empty($conf->global->PROJECT_TASK_ADDON)?'mod_task_simple':$conf->global->PROJECT_TASK_ADDON;
		if (! empty($conf->global->PROJECT_TASK_ADDON) && is_readable(DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.".php"))
		{
			require_once DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.'.php';
			$modTask = new $obj;
			$ref = $modTask->getNextValue($soc,$object);
		}

		if (empty($label))
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Label")), 'errors');
			$action='create';
			$error++;
		}
		else if (empty($_POST['task_parent']))
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("ChildOfTask")), 'errors');
			$action='create';
			$error++;
		}

		if (! $error)
		{
			$db->begin();
			$tmparray=explode('_',$_POST['task_parent']);
			$projectid=$tmparray[0];
			if (empty($projectid)) $projectid = $id;
		// If projectid is ''
			$task_parent=$tmparray[1];
			if (empty($task_parent)) $task_parent = 0;
		// If task_parent is ''

			$task = new Task($db);

			$task->fk_project = $projectid;
		//$task->ref = GETPOST('ref','alpha');
			$task->ref = $ref;
			$task->label = $label;
			$task->description = $description;
			$task->planned_workload = $planned_workload;
			$task->fk_task_parent = $task_parent;
			$task->date_c = dol_now();
			if (!$_POST['options_c_grupo'])
			{
				$task->date_start = $date_start;
				$task->date_end = $date_end;
				$task->progress = $progress+0;
			}
			else
				$task->date_start = dol_now();
		// Fill array 'array_options' with data from add form
			$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);

			$taskid = $task->create($user);
			if (!$taskid>0)
				$error++;
			if (!$error)
			{
				$objecttaskadd->fk_task = $taskid;
				$objecttaskadd->c_grupo = $_POST['options_c_grupo'];
				$objecttaskadd->c_view = $_POST['options_c_view'];
				$objecttaskadd->unit_program = $_POST['options_unit_program'];
				$objecttaskadd->unit_declared = 0;
				$objecttaskadd->fk_unit = $_POST['options_fk_unit'];
				$objecttaskadd->fk_type = $_POST['options_fk_type']+0;
				$objecttaskadd->unit_price = $_POST['options_unit_price'];
				$objecttaskadd->unit_amount = $_POST['options_unit_amount'];
				$objecttaskadd->fk_user_create = $user->id;
				$objecttaskadd->fk_user_mod = $user->id;
				$objecttaskadd->date_create = dol_now();
				$objecttaskadd->tms = dol_now();
				$objecttaskadd->statut = 1;
				$res = $objecttaskadd->create($user);
				if (!$res>0)
					$error++;

			}
			if (!$error)
			{
				$result = $task->add_contact($_POST["userid"], 'TASKEXECUTIVE', 'internal');
			}
			else
			{
				setEventMessages($task->error,$task->errors,'errors');
			}
		}

		if (! $error)
		{
			$db->commit();
			if (! empty($backtopage))
			{
				header("Location: ".$backtopage);
				exit;
			}
			else if (empty($projectid))
			{
				header("Location: ".DOL_URL_ROOT.'/projet/tasks/index.php'.(empty($mode)?'':'?mode='.$mode));
				exit;
			}
			$id = $projectid;
		}
		else
			$db->rollback();
	}
	else
	{
		if (! empty($backtopage))
		{
			header("Location: ".$backtopage);
			exit;
		}
		else if (empty($id))
		{
		// We go back on task list
			header("Location: ".DOL_URL_ROOT.'/projet/tasks/index.php'.(empty($mode)?'':'?mode='.$mode));
			exit;
		}
	}
}

if ($action == 'createrefr')
{
	//require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);

	$tmparray['label'] = GETPOST('label');
	$tmparray['ref'] = GETPOST('ref');
	$tmparray['task_parent'] = GETPOST('task_parent');
	$tmparray['userid'] = GETPOST('userid');
	$tmparray['options_c_grupo'] = GETPOST('options_c_grupo');
	$tmparray['options_c_view'] = GETPOST('options_c_view');

	if ($tmparray['options_c_grupo'] == 1)
		$lDisabled = true;
	$options_c_grupo = $tmparray['options_c_grupo'];
	$options_c_view = $tmparray['options_c_view'];
	$taskstatic->ref = $tmparray['ref'];
	$label = $tmparray['label'];
	$taskstatic->task_parent = $tmparray['task_parent'];
	$task_parent = $tmparray['task_parent'];
	$userid = $tmparray['userid'];
	$taskstatic->speciality = $tmparray['speciality'];
	$action='create';
}

/*
 * View
 */

// Example : Adding jquery code

$form=new Formv($db);
if ($conf->budget->enabled)
{
	//$formadd = new FormAdd($db);
}
$formother=new FormOther($db);

$taskstatic = new Task($db);
$userstatic=new User($db);

$title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$title,$help_url);

if ($id > 0 || ! empty($ref))
{
	$object->fetch($id, $ref);
	$objectadd->fetch($id, $ref);
	$object->fetch_thirdparty();
	$res=$object->fetch_optionals($object->id,$extralabels_projet);
	$res=$objectadd->fetch_optionals($objectadd->id,$extralabels_projet);


	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $objectadd->restrictedProjectAreaadd($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab=GETPOST('tab')?GETPOST('tab'):'taskss';

	$head=project_prepare_head($object);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($object->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $object->getProjectsAuthorizedForUser($user,$mine,0);
		$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->title.'</td></tr>';


	// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	// if (! empty($object->thirdparty->id)) print $object->thirdparty->getNomUrl(1);
	// else print '&nbsp;';
	// print '</td>';
	// print '</tr>';

	// // Visibility
	// print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
	// if ($object->public) print $langs->trans('SharedProject');
	// else print $langs->trans('PrivateProject');
	// print '</td></tr>';

	// // Statut
	// print '<tr><td>'.$langs->trans("Status").'</td><td>'.$object->getLibStatut(4).'</td></tr>';

	// // Date start
	// print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	// print dol_print_date($object->date_start,'day');
	// print '</td></tr>';

	// // Date end
	// print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	// print dol_print_date($object->date_end,'day');
	// print '</td></tr>';

	// Other options
	$parameters=array();
	//$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook
	//if (empty($reshook) && ! empty($extrafields_project->attribute_label))
	//{
	//	print $object->showOptionals($extrafields_project);
	//}
	print '<tr><td>'.$langs->trans("Agenda").'</td><td>';
//	print '<a class="button" href="'.DOL_URL_ROOT.'/comm/action/card.php?action=create&socid='.$object->socid.'&projectid='.$object->id.'&backtopage=1&percentage=1">'.$langs->trans('Nuevo evento').'</a>';
	print '<a class="button" href="'.DOL_URL_ROOT.'/monprojet/actioncomm.php?action=create&socid='.$object->socid.'&projectid='.$object->id.'&backtopage=1&percentage=1">'.$langs->trans('Nuevo evento').'</a>';
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();
}
if ($action == 'verifup')
{
	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="addup">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="table" value="'.$table.'">';
	print '<input type="hidden" name="seldate" value="'.$seldate.'">';
	print '<input type="hidden" name="camposdate" value="'.$camposdate.'">';
	print '<input type="hidden" name="separator" value="'.$separator.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	print '<table class="noborder" width="100%">';

	//encabezado
	$table = 'llx_projet_task';
	foreach($aHeaders AS $i => $value)
	{
		$aHeadersOr[trim($value)] = trim($value);
	}
	$aValHeader = array();
	foreach($aHeaderTpl[$table] AS $i => $value)
	{
		if (!$aHeadersOr[trim($value)])
			$aValHeader[$value] = $value;
	}
	print '<tr class="liste_titre">';
	foreach($aHeaders AS $i => $value)
	{
		print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
	}
	print '</tr>';
	if (!empty($aValHeader))
	{
		$lSave = false;
		print "<tr class=\"liste_titre\">";
		print '<td>'.$langs->trans('Missingfieldss').'</td>';
		foreach ((array) $aValHeader AS $j => $value)
		{
			print '<td>'.$value.'</td>';
		}
		print '</tr>';
	}
	else
	{
		$lSave = true;
		$var=True;
		$c = 0;
		foreach($data AS $key)
		{
			$var=!$var;
			print "<tr $bc[$var]>";
			$c++;
			foreach($aHeaders AS $i => $keyname)
			{
				if (empty($keyname))
					$keyname = "none";
				$phone = $key->$keyname;
				$aArrData[$c][$keyname] = $phone;
				print '<td>'.$phone.'</td>';
			}
			print '</tr>';
		}
	}
	print '</table>';

	If ($lSave)
	{
		$_SESSION['aArrData'] = $aArrData;
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
	}
	//validando el encabezado
	print '</form>';

	dol_fiche_end();
}

if ($action == 'createup' && $user->rights->monprojet->task->crear && (empty($object->thirdparty->id) || $userWrite > 0))
{
	print_fiche_titre($langs->trans("New"));

	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="veriffile">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';


	print '<table class="border centpercent">'."\n";
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("File").'</td><td>';
	print '<input type="file" class="flat" name="archivo" id="archivo" required>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Dateformat');
	print '</td>';
	print '<td>';
	print $form->selectarray('seldate',$aDatef,'',1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Campos date');
	print '</td>';
	print '<td>';
	print '<input type="text" name="camposdate" size="50">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2" required>';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';
	print '<div>';
	print '<span>';
	print $langs->trans('Es necesario un archivo CSV con las siguientes columnas').':';
	print '</span>';
	print '<div>'.'ref'.' =>  <span>'.$langs->trans('Codigo de la tarea').'</span>'.'</div>';
	print '<div>'.'label'.' => <span>'.$langs->trans('Descripcion de la tarea').'</span>'.'</div>';
	print '<div>'.'hilo'.' => <span>'.$langs->trans('Hilo de la tarea').'</span>'.'</div>';
	print '<div>'.'item'.' => <span>'.$langs->trans('Registre el codigo del item').'</span>'.'</div>';
	print '<div>'.'login'.' => <span>'.$langs->trans('Asignado a, registre el login del usuario').'</span>'.'</div>';
	print '<div>'.'fechaini'.' => <span>'.$langs->trans('Fecha inicio de la tarea').'</span>'.'</div>';
	print '<div>'.'fechafin'.' => <span>'.$langs->trans('Fecha final de la tarea').'</span>'.'</div>';
	print '<div>'.'detail'.' => <span>'.$langs->trans('Descripcion de la tarea').'</span>'.'</div>';
	print '<div>'.'group'.' => <span>'.$langs->trans('0=No grupo; 1=Si grupo').'</span>'.'</div>';
	print '<div>'.'type'.' => <span>'.$langs->trans('Codigo tipo de item').'</span>'.'</div>';
	print '<div>'.'typename'.' => <span>'.$langs->trans('Nombre tipo de item').'</span>'.'</div>';
	print '<div>'.'unitprogram'.' => <span>'.$langs->trans('Unidades programadas').'</span>'.'</div>';
	print '<div>'.'unit'.' => <span>'.$langs->trans('Unidad de medida').'</span>'.'</div>';
	print '<div>'.'price'.' => <span>'.$langs->trans('Precio unitario').'</span>'.'</div>';
	print '</div>';
	print '<br>';
	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Upload").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</center>';

	print '</form>';

	dol_fiche_end();

}
if ($action == 'create' && $user->rights->monprojet->task->crear && (empty($object->thirdparty->id) || $userWrite > 0))
{
	if ($id > 0 || ! empty($ref)) print '<br>';

	print_fiche_titre($langs->trans("NewTask"), '', 'title_project');

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#options_c_grupo").change(function() {
			document.form.action.value="createrefr";
			document.form.submit();
		});
	});';
	print '</script>'."\n";

	print '<form id="form" name="form" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="createtask">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="newsel" value="'.$newsel.'">';

	if (! empty($object->id)) print '<input type="hidden" name="id" value="'.$object->id.'">';
	if (! empty($mode)) print '<input type="hidden" name="mode" value="'.$mode.'">';

	dol_fiche_head('');

	print '<table class="border" width="100%">';

	$defaultref='';
	$obj = empty($conf->global->PROJECT_TASK_ADDON)?'mod_task_simple':$conf->global->PROJECT_TASK_ADDON;
	if (! empty($conf->global->PROJECT_TASK_ADDON) && is_readable(DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.".php"))
	{
		require_once DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.'.php';
		$modTask = new $obj;
		$defaultref = $modTask->getNextValue($soc,$object);
	}

	if (is_numeric($defaultref) && $defaultref <= 0) $defaultref='';
	print '<tr>';
	print '<td colspan="2" align="right">';
	if (empty($newsel))
	{
		//print '<a  href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=create&newsel=1'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('SelectNewTask').'</a>';
	}
	else
	{
		//print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=create'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('CreateNewTask').'</a>';
	}
	print '</td>';
	print '</tr>';
	// Ref
	if (empty($newsel))
	{
		print '<tr><td><span class="fieldrequired">'.$langs->trans("Ref").'</span></td><td>'.($_POST["ref"]?$_POST["ref"]:$defaultref).'</td></tr>';
		//label
		print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
		print '<input type="text" size="25" name="label" class="flat" value="'.$label.'" required>';
		print '</td></tr>';
	}
	else
	{
		if ($conf->budget->enabled)
		{
			print '<input type="hidden" name="ref" value="'.($_POST["ref"]?$_POST["ref"]:$defaultref).'">';
			print '<tr><td><span class="fieldrequired">'.$langs->trans("Ref").'</span></td>';
			print '<td>';
			//print $formadd->select_item($taskstatic->ref,'ref','',1);
			print $form->select_items_v($selected='', 'ref', '', 0, 0, 1, 2, '', 1, array(),0,'');
			print '</td></tr>';
		}
	}

	// List of projects
	print '<tr><td class="fieldrequired">'.$langs->trans("ChildOfTask").'</td><td>';
	$formotheradd = new FormOtherAdd($db);
	$formotheradd->selectProjectTasks_($taskstatic->fk_task_parent, $object->id, 'task_parent', ($user->admin?0:1), 0, 0, 0, $taskstatic->id);
	//print $formother->selectProjectTasks((!empty($task_parent)?$task_parent:GETPOST('task_parent')),$projectid?$projectid:$object->id, 'task_parent', 0, 0, 1, 1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("AffectedTo").'</td><td>';
	$contactsofproject=(! empty($object->id)?$object->getListContactId('internal'):'');
	$form->select_users(($userid?$userid:$user->id),'userid',0,'',0,'',$contactsofproject);
	print '</td></tr>';

	//agregamos el grupo
	print '<tr><td>'.$langs->trans("Group").'</td><td>';
	print $form->selectyesno('options_c_grupo',$options_c_grupo,1,'');
	print '</td></tr>';

	//tareas internas o externas
	print '<tr><td>'.$langs->trans("Internaltask").'</td><td>';
	print $form->selectyesno('options_c_view',$options->c_view,1,'');
	print '</td></tr>';

	if (!$lDisabled)
	{
		// Date start
		print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
		print $form->select_date(($date_start?$date_start:''),'dateo',0,0,0,'',1,1,1);
		print '</td></tr>';

		// Date end
		print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
		print $form->select_date(($date_end?$date_end:-1),'datee',0,0,0,'',1,1,1);
		print '</td></tr>';
	}
	// // planned workload
	// print '<tr><td>'.$langs->trans("PlannedWorkload").'</td><td>';
	// print $form->select_duration('planned_workload', $planned_workload?$planned_workload : $object->planned_workload,0,'text');
	// print '</td></tr>';

	// Progress
	if (!$lDisabled)
	{
		print '<tr><td>'.$langs->trans("ProgressDeclared").'</td><td colspan="3">';
		print $formother->select_percent($progress,'progress');
		print '</td></tr>';
	}
	// Description
	print '<tr><td valign="top">'.$langs->trans("Description").'</td>';
	print '<td>';
	print '<textarea name="description" wrap="soft" cols="80" rows="'.ROWS_3.'">'.$description.'</textarea>';
	print '</td></tr>';

	// Other options
	if (!$lDisabled)
	{

		$parameters=array('newaction'=>'addextra','newsel'=>$newsel);
		$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook
		if (empty($reshook) && ! empty($extrafields_task->attribute_label))
		{
			print $object->showOptionals($extrafields_task,'edit');
		}
	}
	print '</table>';

	dol_fiche_end();

	print '<div align="center">';
	print '<input type="submit" class="button" name="add" value="'.$langs->trans("Add").'">';
	print ' &nbsp; &nbsp; ';
	print '<a class="button" href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$id.'">'.$langs->trans("Cancel").'</a>';
	print '</div>';

	print '</form>';

}
else if ($action != 'createup' && $action !='verifup' && ($id > 0 || ! empty($ref)))
{
	// Confirm delete task
	if ($action == 'deletetask')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
			$langs->trans("Deletetasks"),
			$langs->trans("Confirmerasealltasks",$object->ref),
			"confirm_deletetask",
			'',
			0,2);
		if ($ret == 'html') print '<br>';
	}

	 /*
	  * Actions
	  */
	 print '<div class="tabsAction">';
	if ($user->rights->monprojet->task->del && $object->statut == 0) //quitar para activar el borrado
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/tasks.php'.'?id='.$object->id.'&action=deletetask'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Deletetasks').'</a>';

	if ($user->rights->projet->all->creer ||
		$user->rights->projet->creer ||
		$user->rights->monprojet->task->crear)
	{
		if ($user->rights->monprojet->task->crear)
			$userWrite = true;
		if ($object->public || $userWrite > 0 && $action != 'createup')
		{
			print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/export_excel.php'.'?id='.$object->id.'&action=export'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Toexport').'</a>';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=createup'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Goup').'</a>';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=create'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Create').'</a>';
			if ($conf->budget->enabled)
				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=create&newsel=1'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Toselect').'</a>';
		}
		else
		{
			print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerOfProject").'">'.$langs->trans('AddTask').'</a>';
		}
	}

	if ($user->rights->monprojet->prog->leer && $projectstatic->array_options['options_programmed'])
	{
		print '<a class="'.($action=='progv'?'butActionm':'butAction').'" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=progv'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Programmed').'</a>';
		print '<a class="'.($action=='progc'?'butActionm':'butAction').'" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=progc'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Programmedclosed').'</a>';
	}

	if ($user->rights->monprojet->pay->leer)
	{
		print '<a class="'.($action=='payadv'?'butActionm':'butAction').'" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=payadv'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Advancesforpayment').'</a>';
	}
	else
	{
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotEnoughPermissions").'">'.$langs->trans('Paymentauthorization').'</a>';
	}

	if ($user->rights->monprojet->payp->leer)
	{
		print '<a class="'.($action=='paymen'?'butActionm':'butAction').'" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=paymen'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Paymentauthorization').'</a>';
	}
	else
	{
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotEnoughPermissions").'">'.$langs->trans('Paymentauthorization').'</a>';
	}
	print '</div>';
	print '<br>';

	if ($action == 'progv' || $action == 'progc' ||
		$action=='payadv' ||
		$action=='paymen' || $action =='createpay')
	{
	 //no se muestra el modo mine
	}
	else
	{
		print '<table width="100%">';
		print '<tr>';
		if ($selstatut == 2)
		{
			print '<td align="left">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&selstatut=0">'.$langs->trans("Seeall").'</a>';
			print '</td>';
		}
		elseif($selstatut == 1)
		{
			print '<td align="left">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&selstatut=2">'.$langs->trans("Seeclosedtask").'</a>';
			print '</td>';
		}
		elseif(empty($selstatut))
		{
			print '<td align="left">';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&selstatut=1">'.$langs->trans("Seeopentask").'</a>';
			print '</td>';
		}
		print '<td align="right">';
		if ($mode == 'mine')
		{
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans("DoNotShowMyTasksOnly").'</a>';
		 //print ' - ';
		 //print $langs->trans("ShowMyTaskOnly");
		}
		else
		{
		 //print $langs->trans("DoNotShowMyTaskOnly");
		 //print ' - ';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&mode=mine">'.$langs->trans("ShowMyTasksOnly").'</a>';
		}
		print '</td></tr></table>';
	}
	 // Get list of tasks in tasksarray and taskarrayfiltered
	 // We need all tasks (even not limited to a user because a task to user can have a parent that is not affected to him).
	 //$tasksarray=$taskstatic->getTasksArray(0, 0, $object->id, $socid, 0);
	$modetask = 0;
	if ($action == 'progv') $modetask=2;
	if ($action == 'progc') $modetask=3;
	if ($action == 'paymen' || $action == 'createpay') $modetask=4;
	if ($action == 'payadv') $modetask=5; //validar avances para pago

	$lVista = 1; //vista normal
	if ($action == 'progv' || $action == 'progc') $lVista = 2; //vista program
	if ($action == 'paymen' || $action == 'createpay') $lVista = 3;
	if ($action == 'payadv') $lVista = 4;

	$modepay = 0;
	if ($modetask == 5) $modepay = 1;
	 //modo pago estado 0
   	//echo '<hr>object->id '.$object->id;
	$tasksarray = $taskadd->getTasksArray(0, 0, $object->id, $socid, $modetask,'',-1,'',0,0,0,1,0,'',$modepay);
	//subimos a memoria de session
	$_SESSION['tasksarray'][$object->id] = serialize($tasksarray);
	//	$filter = array(1=>1);
	//$aTasknumref = array();
	//$filterstatic = " AND t.fk_projet = ".$projectstatic->id;
	//$taskadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
	//$tasksprojet=$taskadd->getTasksArray(0, 0, $object->id, $socid, /*$modetask*/0,'',-1,'',0,0,0,1,0,'',/*$modepay*/0);
	//verificamos si tiene ya tareas guardadas
	if (count($tasksarray)>0)
	{
		$aTasknumref = array();
		for ($i=0; $i < count($tasksarray); $i++)
		{
			if ($tasksarray[$i]->id>0)
				$aTasknumref[$tasksarray[$i]->id] = array('fk_task_parent'=>$tasksarray[$i]->fk_parent,'ref'=>$tasksarray[$i]->ref,'level'=>$tasksarray[$i]->array_options['options_level'],'reg'=>$i,'group'=>$tasksarray[$i]->array_options['options_c_grupo']);

		}
		$_SESSION['aTasknumref'][$object->id] = serialize($aTasknumref);
	}
	else
	{
		$_SESSION['aTasknumref'][$object->id] = serialize(array());
		//print_r($aTasknumref);
	 // We load also tasks limited to a particular user
	}
	$tasksrole=($mode=='mine' ? $taskstatic->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
	 //var_dump($tasksarray);
	 //var_dump($tasksrole);
	if (! empty($conf->use_javascript_ajax))
	{
		include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
	}

	print '<table id="tablelines" class="noborder" width="100%">';
	print '<tr class="liste_titre nodrag nodrop">';
	 // print '<td>'.$langs->trans("Project").'</td>';
	print '<td width="100">'.$langs->trans("RefTask").'</td>';
	print '<td>'.$langs->trans("LabelTask").'</td>';
	print '<td align="center">'.$langs->trans("DateStart").'</td>';
	print '<td align="center">'.$langs->trans("DateEnd").'</td>';
	print '<td align="right">'.$langs->trans("Unit").'</td>';
	if ($user->rights->monprojet->task->leerm)
		print '<td align="right">'.$langs->trans("Unitary").'</td>';
	print '<td align="right">'.$langs->trans("Planned").'</td>';
	print '<td align="right">'.$langs->trans("Declared").'</td>';
	if ($lVista == 1)
	{
		if ($user->rights->monprojet->task->leerm)
		{
			print '<td align="right">'.$langs->trans("AmountPlaned").'</td>';
			print '<td align="right">'.$langs->trans("AmountDeclared").'</td>';
		}
		//print '<td align="right">'.'<a href="#" title="'.$langs->trans('Costperformanceindex').'" class="classfortooltip">'.$langs->trans("CPI").'</a>'.'</td>';
		//print '<td align="right">'.'<a href="#" title="'.$langs->trans('Scheduleperformanceindex').'" class="classfortooltip">'.$langs->trans("SPI").'</a>'.'</td>';
		//print '<td align="right">'.'<a href="#" title="'.$langs->trans('Costandschedule').'" class="classfortooltip">'.$langs->trans("CSI").'</a>'.'</td>';
	}
	elseif ($lVista == 2)
	{
		if ($user->rights->monprojet->task->leerm)
		{
			print '<td align="right">'.$langs->trans("Programmed").'</td>';
			print '<td align="right">'.$langs->trans("Declaredpresent").'</td>';
		}
	}
	elseif ($lVista == 3 || $lVista == 4)
	{
		print '<td align="right">'.$langs->trans("Paid").'</td>';
		print '<td align="right">'.$langs->trans("Approved").'</td>';
		print '<td align="right">'.$langs->trans("Payable").'<input type="image" src="'.DOL_URL_ROOT.'/monprojet/img/show.png'.'" value="" onclick="ocultarColumna(10,12,true)">'.'</td>';
		print '<td align="right">'.$langs->trans("Paidamount").'</td>';
		print '<td align="right">'.$langs->trans("Approvedamount").'</td>';
		print '<td align="right">'.$langs->trans("Payableamount").'<input type="image" src="'.DOL_URL_ROOT.'/monprojet/img/hide.png'.'" value="" onclick="ocultarColumna(10,12,false)">'.'</td>';
	}
	if ($lVista == 2)
		print '<td align="right">'.$langs->trans("Request").'</td>';
	elseif($lVista == 4)
	{

	}
	else
		print '<td align="right">'.$langs->trans("Statut").'</td>';
	print '<td>&nbsp;</td>';
	print "</tr>\n";

	if ($action =='paymen')
	{
		$selstatut = 0;
		print '<form id="form" name="form" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="selpay">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';

	}

	if (count($tasksarray) > 0)
	{

		if ($action != 'payadv' && $action != 'paymen' && $action != 'createpay')
		{
		 // Show all lines in taskarray (recursive function to go down on tree)
			$j=0; $level=0;
			if ($action == 'progv' || $action == 'progc')
			{
				$selstatut = 0;
				$aAmountres=monprojectLineprog($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$lVista,$filterpay);
			}
			else
				$nboftaskshown=monprojectLinesa($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$lVista);
		}
		else
		{
			$selstatut = 0;
			$j=0; $level=0;
			$aAmountres=monprojectLinepay($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$lVista,$filterpay);
			$sumPayment = $aAmountres[0];
			$sumApprove = $aAmountres[1];
			$sumPayable = $aAmountres[2];
		}
	}
	else
	{
		print '<tr '.$bc[false].'><td colspan="12">'.$langs->trans("NoTasks").'</td></tr>';
	}
	print "</table>";
	print '<br>';
	if ($action == 'paymen' || $action == 'createpay')
	{
		print '<div align="center">';
		if ($user->rights->monprojet->payp->app)
		{
			if ($sumApprove>0)
			{
				print '<input type="submit" class="butAction" name="paydis" value="'.$langs->trans("Disapprove").'">';
			}
			if ($sumPayable>0)
			{
				print '<input type="submit" class="butAction" name="payapp" value="'.$langs->trans("Approve").'">';
			}
		}
		if ($user->rights->monprojet->payp->payapp && $sumApprove>0)
		{
			print '<input type="submit" class="butAction" name="paycreate" value="'.$langs->trans("Generatepayment").'">';
		}
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$id.'">'.$langs->trans("Exit").'</a>';
		print '</div>';

		print '</form>';
	}
	if (! empty($user->rights->projet->all->lire) && $action != 'paymen' && $action!='createpay')
		// We make test to clean only if user has permission to see all (test may report false positive otherwise)
	{
		if ($mode=='mine')
		{
			if ($nboftaskshown < count($tasksrole) && $lVista == 1)
			{
				include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
				//cleanCorruptedTree($db, 'projet_task', 'fk_task_parent');
			}
		}
		else
		{
			if ($nboftaskshown < count($tasksarray) && $lVista == 1)
			{
				include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
			}
		}
	}
}

//creacion del pago
if ($action == 'createpay' || $action == 'verifpay')
{
	if ($action == 'verifpay')
	{
		$_SESSION['aPost'] = $_POST;
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idpay='.GETPOST('idpay','int'),
			$langs->trans("Confirmpayment"),
			$langs->trans("Confirmsgeneratepayment",$object->ref),
			"confirm_verifpay",
			'',
			0,2);
		if ($ret == 'html') print '<br>';
	}
	//verificamos que proceso de pago esta pendiente en el proyecto
	//solo puede existir un proceso de pago pendiente
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet = ".$object->id;
	$filterstatic.= " AND t.statut = 0";
	$numpay = $objpay->fetchall('', '', 0, 0,$filter, 'AND',$filterstatic,true);
	print_fiche_titre($langs->trans("New"));

	dol_fiche_head();

	print '<form  action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="verifpay">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idpay" value="'.$objpay->id.'">';


	print '<table class="border centpercent">'."\n";

	print '<tr><td>';
	print $langs->trans('Ref');
	print '</td>';
	print '<td>';
	print '<input type="text" name="ref" value="'.$objpay->ref.'">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Date');
	print '</td>';
	print '<td>';
	print $form->select_date($objpay->date_payment,'do');
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Amount');
	print '</td>';
	print '<td>';
	print '<input type="number" min="0" step="any" name="amount" value="'.$sumApprove.'">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Detail');
	print '</td>';
	print '<td>';
	print '<textarea name="detail">'.$objpay->detail.'</textarea>';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';
	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</center>';

	print '</form>';

	dol_fiche_end();

}

llxFooter();

$db->close();
