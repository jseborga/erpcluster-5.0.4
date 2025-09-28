<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */


require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/guarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskcontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaymentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaymentdeduction.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/dict.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';
//budget
if ($conf->budget->enabled)
{
	dol_include_once('/budget/class/html.formadd.class.php');
	dol_include_once('/budget/class/items.class.php');
	dol_include_once('/budget/class/cunits.class.php');
	dol_include_once('/budget/class/typeitem.class.php');
}

if ($conf->addendum->enabled)
	require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';
//sales
if ($conf->sales->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/sales/class/factureext.class.php';
}
$id=GETPOST('id','int');
$idp=GETPOST('idp','int');
$idd=GETPOST('idd','int');
$code = GETPOST('code');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$project_id = $id;
$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);
$backtopage=GETPOST('backtopage','alpha');
$sortfield = GETPOST("sortfield","alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="DESC";
$offset = $conf->liste_limit * $page ;

//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$projectstatic = new Project($db);
$object = new Task($db);
$taskadd = new Taskext($db);
$mobject = new Taskext($db);
$contratadd = new Contratext($db);
$objdoc = new Projettasktimedoc($db);
$guarantees = new Guarantees($db);
$taskcontrat = new Projettaskcontrat($db);
$objecttaskadd = new Projettaskadd($db);
$objuser  = new User($db);
$objpay   = new Projetpaymentext($db);
$objpaytemp = new Projetpaymentext($db);
$objpayde = new Projetpaymentdeduction($db);
$taskpay  = new Projettaskpayment($db);
//$cunits   = new Cunits($db);
if ($conf->budget->enabled)
{
	$typeitem = new Typeitem($db);
	$items    = new Items($db);
}
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

$userstatic    = new User($db);
$companystatic = new Societe($db);
$extrafields   = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($projectstatic->table_element);
$extralabels_task=$extrafields_task->fetch_name_optionals_label($object->table_element);

if ($conf->addendum->enabled)
	$addendum = new Addendum($db);


if ($id || $ref)
{
	$projectstatic->fetch($id,$ref);
	$id=$projectstatic->id;
}
if ($idd)
{
	$objpayde->fetch($idd);
	if ($idd == $objpayde->id)
	{
		$idp  = $objpayde->fk_projet_payment;
		$code = $objpayde->code;
	}
}
if ($idp>0) $objpay->fetch($idp);
// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);

$langs->load("users");
$langs->load("projects");


/*
 * Actions
 */
if ($action == 'deldoc' && $user->rights->monprojet->payp->mod)
{
	$error=0;
	if (!$objpay->id > 0 || !$projectstatic->id > 0)
	{
		setEventMessage($langs->trans("Paymentnovalid"),'errors');
		$error++;
	}
	else
	{
		$namefile = GETPOST('namedoc');
	   	//buscamos
		$db->begin();

		$aDoc = explode(';',$objpay->document);
		$document = '';
		foreach ((array) $aDoc AS $i => $name)
		{
			if ($name != $namefile)
			{
				if ($document) $document.=';';
				$document .= $name;
			}
		}
		$objpay->document = $document;
		$res = $objpay->update($user);
		if (!$res>0) $error++;

	    //del photo
		$dirproj = $projectstatic->ref;
		$dir    = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj.'/'.'pay/'.$idp;

		$fileimg=$dir.'/'.$namefile;
		dol_delete_file($fileimg);
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
	$action = '';
}

//adddoc
if ($action == 'adddoc' && $user->rights->monprojet->payp->mod)
{
	$error=0;
	if (!$objpay->id > 0 || !$projectstatic->id > 0)
	{
		setEventMessage($langs->trans("Paymentnovalid"),'errors');
		$error++;
	}
	else
	{
	   	//buscamos
		$db->begin();

	    //add photo
	    // Logo/Photo save
		$code = generarcodigo(3);
		$newDir = $idp.$code;
		$dirproj = $projectstatic->ref;
		$dir    = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj.'/'.'pay/'.$idp;
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->monprojet->pho->up4;
			if ($user->rights->monprojet->pho->up3) $mode = 3;
			if ($user->rights->monprojet->pho->up2) $mode = 2;
			if ($user->rights->monprojet->pho->up1) $mode = 1;
			if ($user->rights->monprojet->pho->up5) $mode = 5;

			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = '';
					foreach ((array) $aFile AS $j => $val)
					{
						if (empty($file))
							$file = $newDir;
						else
							$file.= '.'.$val;
					}
					//buscamos el archivo
			    	//modificamos
					if (empty($objpay->document))
						$objpay->document = $file;
					else
						$objpay->document.=';'.$file;
					$objpay->tms = dol_now();
					$res = $objpay->update($user);
					if (!$res>0) $error++;
				}
				else
				{
					$error++;
				}
				$newfile = $dir.'/'.$file;
				$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
				if (! $result > 0)
				{
					$error++;
					$errors[] = "ErrorFailedToSaveFile";
				}
				else
				{
			    	// Create small thumbs for company (Ratio is near 16/9)
			    	// Used on logon for example
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
			    	// Create mini thumbs for company (Ratio is near 16/9)
			    	// Used on menu or for setup page for example
					$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
				}
			}
			else
			{
				$error++;
			}
		}
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
}

if ($action == 'createded')
{
	$objpayde->fk_projet_payment = $idp;
	$objpayde->code = GETPOST('code');
	$objpayde->amount = GETPOST('deduction');
	$objpayde->fk_user_create = $user->id;
	$objpayde->fk_user_mod = $user->id;
	$objpayde->date_create = dol_now();
	$objpayde->tms = dol_now();
	$objpayde->statut = 1;
	$res = $objpayde->create($user);
	if ($res<=0)
	{
		$error++;
		setEventMessages($objpayde->error,$objpayde->errors,'errors');
	}
	if ($error) $action = 'created';
}

if ($action == 'updateded')
{
	//buscamos
	$objpayde->fetch($idd);
	if ($objpayde->id == $idd && $objpayde->fk_projet_payment == $idp)
	{
		$objpayde->code = GETPOST('code');
		$objpayde->amount = GETPOST('deduction');
		$objpayde->fk_user_mod = $user->id;
		$objpayde->tms = dol_now();
		$objpayde->statut = 1;
		$res = $objpayde->update($user);
		if (!$res>0) $error++;
		if ($error)
			$action = 'editd';
		else
			unset($idd);
	}
}

if ($action == 'updatedet')
{
	//buscamos
	$objpay->fetch($idp);
	if ($objpay->id == $idp)
	{
		$objpay->detail = GETPOST('detail');
		$objpay->tms = dol_now();
		$res = $objpay->update($user);
		if (!$res>0)
		{
			$error++;
			setEventMessages($objpay->error,$objpay->errors,'errors');
		}
		if ($error)
			$action = 'editdet';
	}
}

if ($action == 'addup')
{
    //subida de tareas para el contrato
    //si variable addtask entonces se inserta o actualiza en las tareas del proyecto

	$addtask = GETPOST('addtask');
	$seldate = GETPOST('seldate');

	$error = 0;
    //buscamos el projet
	$res = $projectstatic->fetch($id);
	if (!$res>0)
		$error++;
	$error = 0;
	$aArrData = $_SESSION['aArrData'];
	$table = GETPOST('table');

	$aNewTask = array();
	$db->begin();
	foreach ((array) $aArrData AS $i => $data)
	{
	//variables
	//unit
		$fk_unit = 0;
		if (!empty($data['unit']))
		{
			$cunits->fetch('',$data['unit']);
			if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
			{
		//recuperamos el id de registro
				$fk_unit = $cunits->id;
			}
			else
			{
		//creamos
				$cunits->initAsSpecimen();
				$cunits->code= $data['unit'];
				$cunits->label= $data['unitlabel'];
				$cunits->short_label= $data['unit'];
				$cunits->active= 1;
				$resunit = $cunits->create($user);
				if ($resunit >0) $fk_unit = $resunit;
				else $error++;
			}
		}
	//verificamos si esta relacionado a un item
		$fk_item = 0;
		if (!empty($data['item']))
		{
		    //buscamos
			if ($conf->budget->enabled)
			{
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
				$rest = $typeitem->create($user);
				if (!$rest>0)
					$error++;
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

	//solo se insertaran en tareas del contrato aquellos que tengan c_grupo != 1
	//verificamos las fechas
		$date_start = getformatdate($seldate,$data['fechaini']);
		$date_end   = getformatdate($seldate,$data['fechafin']);
		if ($data['group'] != 1)
		{
	    //buscamos si existe la tarea en el contrato
			$filter = array('ref'=>$data['ref']);
			$filterstatic = " AND fk_projet = ".$id;
			$filterstatic.= " AND fk_contrat = ".$idc;
			$numrow = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,True);
			if ($numrow==1)
			{
		//actualizamos
				$taskcontrat->ref = $data['ref'];
				$taskcontrat->entity = $conf->entity;
				$taskcontrat->fk_projet = $projectstatic->id;
				$taskcontrat->fk_contrat = $idc;
				$taskcontrat->datec = dol_now();
				$taskcontrat->tms = dol_now();
				$taskcontrat->dateo = $date_start;
				$taskcontrat->datee = $date_end;
				$taskcontrat->datev = dol_now();
				$taskcontrat->label = $data['label'];
				$taskcontrat->description = $data['detail'];
				$taskcontrat->priority = $data['priority']+0;
				$taskcontrat->fk_user_creat = $user->id;
				$taskcontrat->fk_user_valid = $user->id;
				$taskcontrat->c_grupo = $data['group'];
				$taskcontrat->fk_type = $fk_type_item;
				$taskcontrat->unit_program = $data['unitprogram'];
				$taskcontrat->fk_unit = $fk_unit;
				$taskcontrat->unit_amount = $data['price'];
				$taskcontrat->fk_statut = 1;
				$res = $taskcontrat->update($user);
				if (!$res>0)
					$error++;
			}
			else
			{
		//nuevo
				$taskcontrat->ref = $data['ref'];
				$taskcontrat->entity = $conf->entity;
				$taskcontrat->fk_projet = $projectstatic->id;
				$taskcontrat->fk_contrat = $idc;
				$taskcontrat->datec = dol_now();
				$taskcontrat->tms = dol_now();
				$taskcontrat->dateo = $date_start;
				$taskcontrat->datee = $date_end;
				$taskcontrat->datev = dol_now();
				$taskcontrat->label = $data['label'];
				$taskcontrat->description = $data['detail'];
				$taskcontrat->priority = $data['priority']+0;
				$taskcontrat->fk_user_creat = $user->id;
				$taskcontrat->fk_user_valid = $user->id;
				$taskcontrat->c_grupo = $data['group'];
				$taskcontrat->fk_type = $fk_type_item;
				$taskcontrat->unit_program = $data['unitprogram'];
				$taskcontrat->fk_unit = $fk_unit;
				$taskcontrat->unit_amount = $data['price'];
				$taskcontrat->fk_statut = 1;
				$res = $taskcontrat->create($user);
				if (!$res>0)
					$error++;

			}
		}
		if ($addtask && !$error)
		{
	    //agregamos o actualizamos las tareas

	    //vamos verificando la existencia de cada uno
			$fk_task_parent = 0;
			if (!empty($data['hilo']))
			{
				if (!empty($aNewTask[$data['hilo']]))
					$fk_task_parent = $aNewTask[$data['hilo']];
				else
					$error++;
			}

	    //buscamos si existe la tarea
			$taskadd = new Taskext($db);
			$filter = array(1=>1);
			$filterstatic = " AND t.ref = '".trim($data['ref'])."'";
			$filterstatic.= " AND t.fk_projet = ".$projectstatic->id;
			$res = $taskadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,1);
			if ($res>0)
			{

				foreach ($taskadd->lines AS $k => $obj)
				{
		    // echo '<br>compara '.$obj->ref.' '.$data['ref'].' pro '.$obj->fk_project.' '.$projectstatic->id;
					if (STRTOUPPER(trim($obj->ref)) == STRTOUPPER(trim($data['ref'])) &&
						$obj->fk_project == $projectstatic->id)
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
							$_POST['options_unit_program'] = $data['unitprogram'];
							$_POST['options_fk_unit'] = $fk_unit;
							$_POST['options_unit_amount'] = $data['price'];
							$task->dateo = $date_start;
							$task->datee = $date_end;
							$task->fk_task_parent = $fk_task_parent +0;
							$task->ref = $data['ref'];
							$task->label = $data['label'];
							$task->description = $data['detail'];
							$task->priority = $data['priority']+0;
			    // Fill array 'array_options' with data from add form
							$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
							if (!$ret > 0)
								$error++;
			    //actualizamos datos adicionales de la tarea
							$res = $objecttaskadd->fetch('',$task->id);
							if ($res>0 && $objecttaskadd->fk_task == $task->id)
							{
								$objecttaskadd->fk_item = $fk_item;
								$objecttaskadd->fk_type_item = $fk_type_item;

								$objecttaskadd->c_grupo = $data['group'];
								$objecttaskadd->unit_program = $data['unitprogram'];
								$objecttaskadd->fk_unit = $fk_unit;
								$objecttaskadd->unit_amount = $data['price'];
								$objecttaskadd->fk_user_mod = $user->id;
								$objecttaskadd->tms = dol_now();
								$res = $objecttaskadd->update($user);
								if (!$res>0)
									$error++;
							}
							else
							{
								$objecttaskadd->fk_task = $task->id;
								$objecttaskadd->fk_item = $fk_item;
								$objecttaskadd->fk_type_item = $fk_type_item;
								$objecttaskadd->c_grupo = $data['group'];
								$objecttaskadd->unit_program = $data['unitprogram'];
								$objecttaskadd->fk_unit = $fk_unit;
								$objecttaskadd->unit_amount = $data['price'];
								$objecttaskadd->fk_user_create = $user->id;
								$objecttaskadd->fk_user_mod = $user->id;
								$objecttaskadd->date_create = dol_now();
								$objecttaskadd->tms = dol_now();
								$objecttaskadd->statut = 1;
								$res = $objecttaskadd->update($user);
								if (!$res>0)
									$error++;
							}
							if (!$error)
							{
								$result = $task->update($user);
								if (!$result>0)
									$error++;
							}
						}
					}
				}
			}
			else
			{

		//creamos nuevo
				$_POST['options_c_grupo'] = $data['group'];
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
				$task->dateo = $date_start;
				$task->datee = $date_end;
				$task->description = $data['detail'];
				$task->fk_user_creat = $user->id;
				$task->priority = $data['priority']+0;
				$task->fk_statut = 1;
				$task->datec = dol_now();
				$task->tms = dol_now();

		// Fill array 'array_options' with data from add form
				$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
				$result = $task->create($user,1);
				if (!$result>0)
					$error++;
				if (!$error)
				{
					$objecttaskadd->fk_task = $result;
					$objecttaskadd->c_grupo = $data['group'];
					$objecttaskadd->unit_program = $data['unitprogram'];
					$objecttaskadd->fk_item = $fk_item;
					$objecttaskadd->fk_type_item = $fk_type_item;
					$objecttaskadd->fk_unit = $fk_unit;
					$objecttaskadd->unit_amount = $data['price'];
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
	}
	if (empty($error))
		$db->commit();
	else
	{
		setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
		$db->rollback();
	}
	$action = 'list';
}

//cancel
if ($action == 'confirm_seltask' && GETPOST('cancel')) $action='asignc';

if ($action == 'confirm_seltask' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->task->mod && $projectstatic->statut < 2)
{
	$error = 0;
    //actualizamos las tareas con el contrato seleccionado
	$aTask = $_SESSION['aSelectcont'];
	$db->begin();
	foreach ((array) $aTask AS $fk_task)
	{
		//buscamos la tarea
		$object->fetch($fk_task);
		if ($object->id == $fk_task)
		{
		    //recuperamos los valores extrafields
		    //mismos valores
			$_POST['options_unit_declared'] = $object->array_options['options_c_grupo'];
			$_POST['options_c_grupo'] = $object->array_options['options_c_grupo'];
			$_POST['options_unit_program'] = $object->array_options['options_unit_program'];
			$_POST['options_fk_unit'] = $object->array_options['options_fk_unit'];
			$_POST['options_fk_item'] = $object->array_options['options_fk_item'];
			$_POST['options_unit_amount'] = $object->array_options['options_unit_amount'];
			$_POST['options_unit_ejecuted'] = $object->array_options['options_unit_ejecuted']+0;
			$_POST['options_fk_contrat'] = $idc;
			// Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
			if ($ret < 0) $error++;
			if (! $error)
			{
				$result=$object->update($user);
				if (!$result > 0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		else
			$error++;
	}
	if (!$error)
	{
		$db->commit();
	// update OK
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
		$db->rollback();

}

// Add
if ($action == 'add' && $user->rights->monprojet->cont->crear)
{
	$error = 0;
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	$guarantees->date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$guarantees->date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

	$guarantees->ref       = $_POST["ref"];
	$guarantees->code_guarantee     = GETPOST('code_guarantee');
	$guarantees->issuer = GETPOST('issuer','alpha');
	$guarantees->concept = GETPOST('concept','alpha');
	$guarantees->amount = GETPOST('amount');
	$guarantees->fk_contrat = GETPOST('idc','int');
	$guarantees->fk_user_create = $user->id;
	$guarantees->date_create = dol_now();
	$guarantees->tms = dol_now();
	$guarantees->statut = 1;

	if (empty($guarantees->code_guarantee))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorCodeguaranteeisrequired"), 'errors');
	}
	if (empty($guarantees->ref))
	{
		$error++;
		setEventMessages(null, $langs->trans("Errorrefrequired"), 'errors');
	}
	if (empty($guarantees->issuer))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorIssuerisrequired"), 'errors');
	}
	if (empty($guarantees->concept))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorConceptisrequired"), 'errors');
	}
	if (empty($guarantees->amount))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorAmountisrequired"), 'errors');
	}

	if (empty($error))
	{
		$idg = $guarantees->create($user);
		if ($idg > 0)
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		$action = 'createg';
		$mesg='<div class="error">'.$guarantees->error.'</div>';
	}
	else
	{
		setEventMessages(null, $langs->trans("Errornotregister"), 'errors');

		if ($error)
			$action="createg";
	     // Force retour sur page creation
	}
}

//update
if ($action == 'update' && $user->rights->monprojet->payp->mod)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	if ($objpay->id == $idp)
	{
		$error = 0;
		$objpay->date_payment = dol_mktime(12, 0, 0, GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));
		$objpay->ref = GETPOST('ref');
		$objpay->tms = dol_now();
		$objpay->fk_user_mod = $user->id;
		//antes actualizamos o registramos las deducciones
		$aDeduction = GETPOST('deduction');
		foreach ((array) $aDeduction AS $code => $amountde)
		{
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_projet_payment = ".$idp;
			$filterstatic.= " AND t.code = '".$code."'";
			$numpayde = $objpayde->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
			$objpaydenew = new Projetpaymentdeduction($db);
			if ($numpayde>0)
			{
				//actualizamos
				$objpaydenew->fetch($objpayde->id);
				$objpaydenew->amount = $amountde;
				$objpaydenew->fk_user_mod = $user->id;
				$objpaydenew->tms = dol_now();
				$res = $objpaydenew->update($user);
				if (!$res > 0) $error++;
			}
			else
			{
				//nuevo
				$objpaydenew->fk_projet_payment = $idp;
				$objpaydenew->code = $code;
				$objpaydenew->amount = $amountde;
				$objpaydenew->fk_user_create = $user->id;
				$objpaydenew->fk_user_mod = $user->id;
				$objpaydenew->date_create = dol_now();
				$objpaydenew->tms = dol_now();
				$objpaydenew->statut = 1;
				$res = $objpaydenew->create($user);
				if (!$res>0) $error++;
			}
		}


		if (empty($error))
		{
			$res = $objpay->update($user);
			if ($res > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/collect.php?id='.$id.'&idp='.$idp,1);
				header("Location: ".$urltogo);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$objpay->error.'</div>';
		}
		else
		{
			if ($error)
				$action="edit";
	         // Force retour sur page creation
		}
	}
}

//updatedate
if ($action == 'updatedate' && $user->rights->monprojet->payp->mod)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	if ($objpay->id == $idp)
	{
		$error = 0;
		$objpay->date_payment = dol_mktime(12, 0, 0, GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));
		$objpay->tms = dol_now();
		$objpay->fk_user_mod = $user->id;
		if ($objpay->date_payment <=0) $error++;
		if (empty($error))
		{
			$res = $objpay->update($user);
			if ($res > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/collect.php?id='.$id.'&idp='.$idp,1);
				header("Location: ".$urltogo);
				exit;
			}
			$action = 'editdate';
			$mesg='<div class="error">'.$objpay->error.'</div>';
		}
		else
		{
			if ($error)
				$action="editdate";
	         // Force retour sur page creation
		}
	}
}

// confirm_approve
if ($action == 'confirm_approve' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->payp->payapp && $objpay->statut == 0)
{

	//vamos a numerar
	if ($objpay->id)
	{
		$nSumpay = 0;
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_projet_payment = ".$idp;
		$numtask = $taskpay->fetchAll('', '', 0, 0,$filter, 'AND',$filterstatic,false);
		if ($numtask>0)
		{
			foreach ($taskpay->lines AS $j => $line)
			{
				echo '<hr>add '.$resadd = $objecttaskadd->fetch(0,$line->fk_task);
				if ($resadd>0)
					$nSumpay+=$line->unit_declared*$objecttaskadd->unit_amount;
			}
		}
		$objpay->amount = $nSumpay;
		$objpay->statut = 2;
		$result=$objpay->update($user);
		if ($result > 0)
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/collect.php?id='.$id.'&idp='.$objpay->id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$objpay->error.'</div>';
			$action='';
		}
	}
}

// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->payp->app && $objpay->statut == 0)
{
	$result=$objpay->delete($user);
	if ($result > 0)
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/payment.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$objpay->error.'</div>';
		$action='';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/*
 * View
 */

$form=new Form($db);
$formother     = new FormOther($db);

$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');

if (! empty($conf->use_javascript_ajax))
{
	$arrayofjs=array(
		'/monprojet/js/jsgantt.js',
		'/monprojet/js/graphics.js',

		'/projet/jsgantt_language.js.php?lang='.$langs->defaultlang
	);
}

$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$langs->trans("Tasks"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

if ($id > 0 || ! empty($ref))
{
	$projectstatic->fetch($id,$ref);
	if ($projectstatic->societe->id > 0)  $result=$projectstatic->societe->fetch($projectstatic->societe->id);

	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $projectstatic->restrictedProjectArea($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab='collect';

	$head=project_prepare_head($projectstatic);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/monprojet/list.php">'.$langs->trans("BackToList").'</a>';

    // Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
    // Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
		$projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($projectstatic, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';


    // print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
    // if (! empty($projectstatic->societe->id)) print $projectstatic->societe->getNomUrl(1);
    // else print '&nbsp;';
    // print '</td>';
    // print '</tr>';

    // // Visibility
    // print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
    // if ($projectstatic->public) print $langs->trans('SharedProject');
    // else print $langs->trans('PrivateProject');
    // print '</td></tr>';

    // // Statut
    // print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

    // // Date start
    // print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
    // print dol_print_date($projectstatic->date_start,'day');
    // print '</td></tr>';

    // // Date end
    // print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
    // print dol_print_date($projectstatic->date_end,'day');
    // print '</td></tr>';


	print '</table>';

	print '</div>';
}


/*
 * payment
 */

print '<br>';


// Get list of tasks in tasksarray and taskarrayfiltered
// We need all tasks (even not limited to a user because a task to user
// can have a parent that is not affected to him).
//$tasksarray = $object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
// We load also tasks limited to a particular user
//$tasksrole=($_REQUEST["mode"]=='mine' ? $task->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
//var_dump($tasksarray);
//var_dump($tasksrole);

//verificamos los items que contiene
$modetask = 0;
//$tasksarray=$taskadd->getTasksArray(0, 0, $projectstatic->id, $socid, $modetask);
//verificamos si las tareas estan asociadas al contrato o contratos del proyecto
// $lContrattask = false;
// foreach ((array) $tasksarray AS $j => $objtask)
// {
//   if (!$objtask->array_options['options_fk_contrat']>0)
//     $lContrattask = true;
// }

//recuperamos los pagos registrados
$filter = array(1=>1);
$filterstatic = " AND t.fk_projet = ".$projectstatic->id;
$numpay = $objpay->fetchAll($sortorder,$sortfield, 0, 0,$filter, 'AND',$filterstatic);
if ($numpay>0)
{
	dol_fiche_head();

	$params='';
	$params.= '&amp;id='.$id;

	if ($action == 'seltask')
	{
		$aSelcon = GETPOST('selcon');
		unset($_SESSION['aSelectcont']);
		foreach ((array) $aSelcon AS $j => $value)
			$_SESSION['aSelectcont'][$j]=$j;
		$_SESSION['seltask_post'] = $_POST;
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idc='.$idc,
			$langs->trans("Linktaskstocontrat"),
			$langs->trans("Confirmlinktaskstocontrat",$object->ref),
			"confirm_seltask",
			'',
			0,2);
		if ($ret == 'html') print '<br>';
	}

	print '<table class="noborder centpercent">'."\n";
    // Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$params,'');
	print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.detail','',$params,'');
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.date_payment','',$params,'');
	print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'t.amount','',$params,'align="right"');
	print_liste_field_titre($langs->trans('Deductions'),$_SERVER['PHP_SELF'],'','',$params,'align="right"');
	print_liste_field_titre($langs->trans('Liquido'),$_SERVER['PHP_SELF'],'','',$params,'align="right"');
	print_liste_field_titre($langs->trans('Attachments'),$_SERVER['PHP_SELF'],'','',$params,'align="right"');
	print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'t.statut','',$params,' align="right"');
	print '</tr>';
    //armamos los contratos
	$var = true;
	$aArray = array();
	$sumatot = 0;
	$sumaded = 0;
	$sumaliq = 0;
	foreach ((array) $objpay->lines AS $j => $line)
	{
		$var = !$var;
		if ($idp != $line->id)
			print "<tr $bc[$var]>";
		else
			print '<tr class="backmark">';
		print '<td>';
		print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$line->id.'">'.img_picto($langs->trans('View'),DOL_URL_ROOT.'/monprojet/img/payment','',true).' '.$line->ref.'</a>';
		print '</td>';
		print '<td>';
		print $line->detail;
		print '</td>';
		print '<td>';
		print dol_print_date($line->date_payment,'day');
		print '</td>';
		print '<td align="right">';
		print price($line->amount);
		$sumatot += $line->amount;
		print '</td>';
		print '<td align="right">';
		$sumade = 0;
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_projet_payment =".$line->id;
		$filterstatic.= " AND t.statut = 1";
		$objpayde->fetchAll('', '', 0, 0, $filter, 'AND', $filterstatic);
		foreach ((array) $objpayde->lines AS $k =>$linede)
		{
			$sumade += $linede->amount;
		}
		print price($sumade);
		$sumaded+= $sumade;
		print '</td>';
		print '<td align="right">';
		print price(price2num($line->amount - $sumade,'MT'));
		print '</td>';
		$sumaliq += $line->amount - $sumade;
		//agregamos la lista de adjuntos
		print '<td>';
		if ($line->document)
		{
			//recuperamos los nombres de archivo
			$aDoc = explode(';',$line->document);
			foreach ((array) $aDoc AS $k => $doc)
			{
				$objpaytemp->fetch($line->id);
				$aFile = explode('.',$doc);
			//extension
				$docext = STRTOUPPER($aFile[count($aFile)-1]);
				$typedoc = 'doc';
				if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
					$typedoc = 'fin';
				if ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
					$typedoc = 'doc';
				elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
					$typedoc = 'doc';

				//print '&nbsp;'.$objpaytemp->showphoto($typedoc,$objpaytemp,$doc,$object,$projectstatic, 100,$docext,1);
				$imagesize = '';
				$cache=0;
				print '&nbsp;'.$objpaytemp->showphotos($typedoc,$doc,$objpaytemp,'monprojet', $object, $projectstatic,$width=100, $height=0, $caneditfield=0, $cssclass='photowithmargin', $imagesize, 1, $cache,$docext);

			}
		}
		print '</td>';
	//fin lista adjuntos

		print '<td align="right">';
		print $objpay->LibStatut($line->statut);
		print '</td>';
		print '</tr>';
	}
    //totales
	print '<tr class="liste_total" align="right">';
	print '<td colspan="3">'.$langs->trans('Total').'</td>';
	print '<td>'.price(price2num($sumatot,'MT')).'</td>';
	print '<td>'.price(price2num($sumaded,'MT')).'</td>';
	print '<td>'.price(price2num($sumaliq,'MT')).'</td>';
	print '</tr>';
	print '</table>';
	dol_fiche_end();
	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */

	print "<div class=\"tabsAction\">\n";
	if ($user->rights->monprojet->payp->rep)
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/payexcel.php'.'?id='.$id.'">'.$langs->trans("Payroladvance").'</a>';
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Payroladvance")."</a>";
	print '</div>';

}
else
{
	print $langs->trans("Nocollects");
}

if ($idp && $action!='edit')
{
	print_fiche_titre($langs->trans("View"));
	dol_htmloutput_mesg($mesg);
	//$objpay->fetch($idp);
    // Confirm delete third party
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idg='.$idg,$langs->trans("Deleteguarantee"),$langs->trans("Confirmdeleteguarantee").' '.$guarantees->ref.' '.$langs->trans('The').' '.$guarantees->issuer,"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	if ($action == 'approve')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idp='.$idp,$langs->trans("Approvepayment"),$langs->trans("ConfirmApprovepayment"),"confirm_approve",'',1,2);
		if ($ret == 'html') print '<br>';
	}

	dol_fiche_head();

	print '<table class="border centpercent">';

    // Ref
	print '<tr><td width="15%">'.$langs->trans('Ref').'</td><td colspan="2">';
	print $objpay->ref;
	print '</td></tr>';

    //date
	print '<tr><td>'.$langs->trans('Date').'</td><td colspan="2">';
	if ($action == 'editdate')
	{
			//registramos la deduccion
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';
		print '<input type="hidden" name="action" value="updatedate">';
		print '<input type="hidden" name="idd" value="'.$objpayde->id.'">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idp" value="'.$idp.'">';
		dol_htmloutput_mesg($mesg);
		print $form->select_date($objpay->date_payment,'dp_');
		print '<input type="submit" class="button" name="update" value="'.$langs->trans("Save").'">';
		print '</form>';
	}
	else
	{

		print dol_print_date($objpay->date_payment,'day');
		if ($user->rights->monprojet->payp->mod && $objpay->statut == 0)
		{
			print ' '.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$idp.'&action=editdate">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		}
	}
	print '</td></tr>';
    //Amount
	print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
	print price($objpay->amount);
	print '</td></tr>';

    //verificamos si tiene deducciones
	$arraydeduc = getlist_deduction('','ASC','sequence');
	foreach ((array) $arraydeduc AS $j => $data)
	{
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_projet_payment = ".$idp;
		$filterstatic.= " AND t.code = '".$data['code']."'";
		$numpayde = $objpayde->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
		print '<tr><td>'.select_type_deduction($data['code'],'','',0,1,'code').'</td><td colspan="2">';
		if ($user->rights->monprojet->payp->mod)
		{
			if ($action == 'editd' || $action == 'created')
			{
				if ($code == $data['code'])
				{
					//registramos la deduccion
					print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';
					if ($objpayde->code == $data['code'])
					{
						print '<input type="hidden" name="action" value="updateded">';
						print '<input type="hidden" name="idd" value="'.$objpayde->id.'">';
					}
					else
						print '<input type="hidden" name="action" value="createded">';
					print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
					print '<input type="hidden" name="id" value="'.$id.'">';
					print '<input type="hidden" name="idp" value="'.$idp.'">';
					dol_htmloutput_mesg($mesg);
					print '<input type="hidden" name="code" value="'.$data['code'].'">';
					print '<input type="number" min="0" step="any" name="deduction" value="'.($numpayde==1?$objpayde->amount:0).'">';
					print '<input type="submit" class="button" name="update" value="'.$langs->trans("Save").'">';
					print '</form>';
				}
			}
			else
			{
				if ($numpayde>0)
				{
					print price($objpayde->amount);
					if (!$idd && $objpay->statut == 0)
						print ' <a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idd='.$objpayde->id.'&action=editd">'.img_picto($langs->trans('Edit'),'edit').'</a>';
				}
				else
				{
					if (!$code && $objpay->statut==0)
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$idp.'&code='.$data['code'].'&action=created">'.img_picto($langs->trans('Edit'),'edit').'</a>';
				}
			}
		}
		else
			print price($objpayde->amount);

		print '</td></tr>';
	}
	//detail
	print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="2">';
	if ($action == 'editdet')
	{
			//registramos la deduccion
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';
		print '<input type="hidden" name="action" value="updatedet">';
		print '<input type="hidden" name="idd" value="'.$objpayde->id.'">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idp" value="'.$idp.'">';
		dol_htmloutput_mesg($mesg);
		print '<textarea name="detail">';
		print $objpay->detail;
		print '</textarea>';
		print '<input type="submit" class="button" name="update" value="'.$langs->trans("Save").'">';
		print '</form>';
	}
	else
	{

		print $objpay->detail;
		if ($user->rights->monprojet->payp->mod && $objpay->statut==0)
		{
			print ' '.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$idp.'&action=editdet">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		}
	}
	print '</td></tr>';

	//mostramos si tiene factura
	$lFacture = false;
	if ($objpay->fk_facture>0)
	{
		$objFacture = new Factureext($db);
		$resf = $objFacture->fetch($objpay->fk_facture);
		//facture

		print '<tr><td>'.$langs->trans('Invoice').'</td><td colspan="2">';
		if ($resf>0)
		{
			$lFacture = true;
			print $objFacture->getNomUrladd(1);
		}
		print '</td></tr>';
	}
    //Statut
	print '<tr><td>'.$langs->trans('Statut').'</td><td colspan="2">';

	print $objpay->getLibStatut();
	print '</td></tr>';

    //archivos adjuntos
	//photo
	print '<tr><td>'.$langs->trans('Attachments').'</td><td colspan="2" align="left" nowrap class="SI-FILES-STYLIZED">';
	//$objdoc->fetch('',$task_time->rowid);
	if ($objpay->document)
	{
	    //recuperamos los nombres de archivo
		$aDoc = explode(';',$objpay->document);
		foreach ((array) $aDoc AS $k => $doc)
		{
			$aFile = explode('.',$doc);
			//extension
			$docext = STRTOUPPER($aFile[count($aFile)-1]);
			$typedoc = 'doc';
			if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
				$typedoc = 'fin';
			if ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
				$typedoc = 'doc';
			elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
				$typedoc = 'doc';

			//print '&nbsp;'.$objpay->showphoto($typedoc,$objpay,$doc,$object,$projectstatic, 100,$docext,1);
			print '&nbsp;'.$objpay->showphotos($typedoc,$doc,$objpay,'monprojet', $object, $projectstatic,100, 0, 0, 'photowithmargin', '', 1, 0,$docext);
			if ($user->rights->monprojet->payp->mod)
				print '&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$projectstatic->id.'&idp='.$objpay->id.'&namedoc='.$doc.'&action=deldoc'.'">'.img_picto($langs->trans('Deleteattachment'),'edit_remove').'</a>';
		}
	}
	//revisar permiso
	if ($user->rights->monprojet->payp->mod)
		print '<br>&nbsp;&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$projectstatic->id.'&idp='.$objpay->id.'&action=editlinep'.'">'.img_picto($langs->trans('Newattachment'),'edit_add').'</a>';
	//para subir nuevo archivo
	if ($action == 'editlinep' && $user->rights->monprojet->payp->mod &&  $idp == $objpay->id)
	{
		print '<form  enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$projectstatic->id.'">';
		print '<input type="hidden" name="id" value="'.$projectstatic->id.'">';
		print '<input type="hidden" name="idp" value="'.$objpay->id.'">';
		print '<input type="hidden" name="action" value="adddoc">';
		print '<input type="hidden" name="withproject" value="'.$withproject.'">';

		print '<label class="cabinet">';
		include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/adddoc.tpl.php';
		print '</label>';
		print '<input type="submit" value="'.$langs->trans('Save').'">';

		print '</form>';
	}


	print '</td></tr>';


	print '</table>';
	dol_fiche_end();

	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */

	print "<div class=\"tabsAction\">\n";


	if ($action == 'view')
	{
		if ($user->rights->monprojet->payp->del && $objpay->statut == 0)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$id.'&idp='.$objpay->id.'">'.$langs->trans("Edit").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Edit")."</a>";

		if ($user->rights->monprojet->cont->del && $objpay->statut == 0)
			print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$id.'&idp='.$objpay->id.'">'.$langs->trans("Delete").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	}
	print "</div>";
}

if ($idp)
{
	$nSumpay = 0;
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet_payment = ".$idp;
	$numtask = $taskpay->fetchAll('', '', 0, 0,$filter, 'AND',$filterstatic,false);
	if ($numtask>0)
	{
		print '<table class="noborder centpercent">'."\n";
		// Fields title
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$param,'');
		print_liste_field_titre($langs->trans('Task'),$_SERVER['PHP_SELF'],'','',$param,'');
		print_liste_field_titre($langs->trans('Quant'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
		print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
		print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'','',$param,' align="right"');
		print '</tr>';
		foreach ((array) $taskpay->lines AS $j => $lint)
		{
			$var = !$var;
			$taskadd->fetch($lint->fk_task);
			$objecttaskadd->fetch('',$lint->fk_task);
			print "<tr $bc[$var]>";
			print '<td>';
			print $taskadd->ref;
			print '</td>';
			print '<td>';
			print $taskadd->label;
			print '</td>';
			print '<td align="right">';
			print price($lint->unit_declared);
			print '</td>';
			print '<td align="right">';
			print price(price2num($lint->unit_declared * $objecttaskadd->unit_amount,'MT'));
			print '</td>';
			print '<td align="right">';
			print $taskpay->LibStatut($lint->statut);
			print '</td>';
			print '</tr>';
			$nSumpay+= price2num($lint->unit_declared * $objecttaskadd->unit_amount,'MT');
		}
		//totales
		print '<tr class="liste_total nodrag nodrop">';
		print '<td colspan="3">'.$langs->trans("Total").'</td>';
		print '<td align="right">'.price(price2num($nSumpay,'MT')).'</td>';
		print '<td></td>';
		print '</tr>';
		print '</table>';
		/* ******************************* */
		/*                                 */
		/* Barre d'action                  */
		/*                                 */
		/* ******************************* */

		print "<div class=\"tabsAction\">\n";

		if ($objpay->statut == 0)
		{
			if (empty($action))
			{
				if ($user->rights->monprojet->payp->mod)
				{
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&&idp='.$objpay->id.'&id='.$id.'">'.$langs->trans("Edit").'</a>';

				}
				if ($user->rights->monprojet->payp->payapp)
				{
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=approve&&idp='.$objpay->id.'&id='.$id.'">'.$langs->trans("Approve").'</a>';

				}
			}
		}
		if ($objpay->statut == 2)
		{
			if ($user->rights->monprojet->payp->fac && $objpay->statut == 2)
			{
				//verificamos el contrato principal
				$res = $contratadd->getlist($id);
				if (count($contratadd->linec)>0)
				{
					foreach ($contratadd->linec AS $j => $objcontr)
					{
						if ($conf->addendum->enabled)
						{
							require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';
							$addendum = new Addendum($db);
					//verificamos si tiene padres
							$resadd = $addendum->getlist($objcontr->id);
							if (empty($resadd))
								$objectid = $objcontr->id;
						}
						else
							$objectid = $objcontr->id;
					}
				}
				if (empty($objpay->fk_facture) || is_null($objpay->fk_facture) || !$lFacture)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/sales/compta/facture.php'.'?action=create&originid='.$objectid.'&origin=contrat&idpay='.$objpay->id.'&ppay=1&ns='.$nSumpay.'">'.$langs->trans("Createfacture").'</a>';
				}
			}
			else
			{


				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createfacture")."</a>";


			//if ($conf->sales->enabled)
			//	print '<a class="butAction" href="'.DOL_URL_ROOT.'/sales/compta/facture.php'.'?action=create&originid='.$objectid.'&origin=contrat&idpay='.$objpay->id.'&ppay=1&ns='.$nSumpay.'">'.$langs->trans("Createfacture").'</a>';
			//else
			//	print '<a class="butAction" href="'.DOL_URL_ROOT.'/compta/facture.php'.'?action=create&originid='.$objectid.'&origin=contrat&idpay='.$objpay->id.'&ppay=1&ns='.$nSumpay.'">'.$langs->trans("Createfacture").'</a>';
			}
		}

		if ($user->rights->monprojet->payp->rep && $objpay->statut == 2)
			print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/tasksexcel.php'.'?id='.$id.'&idpay='.$objpay->id.'">'.$langs->trans("Spreadsheet").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Excel")."</a>";
		print '</div>';
	}
}

//edita payment
if ($idp && $action == 'edit' && $objpay->statut == 0)
{
	$guarantees->fetch($idg);
	if (empty($aArray[$guarantees->fk_contrat]))
	{
		print_fiche_titre($langs->trans("Edit"));
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';

		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idp" value="'.$idp.'">';

		dol_htmloutput_mesg($mesg);
		dol_fiche_head();
		print '<table class="border" width="100%">';

		// ref
		print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
		print '<input type="text" name="ref" value="'.$objpay->ref.'">';
		print '</td></tr>';

		//date
		print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
		print $form->select_date($objpay->date_payment,'dp_');
		print '</td></tr>';

		//verificamos si tiene deducciones
		$arraydeduc = getlist_deduction('','ASC','sequence');
		foreach ((array) $arraydeduc AS $j => $data)
		{
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_projet_payment = ".$idp;
			$filterstatic.= " AND t.code = '".$data['code']."'";
			$numpayde = $objpayde->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
			print '<tr><td>'.select_type_deduction($data['code'],'','',0,1,'code').'</td><td colspan="2">';
			print '<input type="number" min="0" step="any" name="deduction['.$data['code'].']" value="'.$objpayde->amount.'">';
			print '</td></tr>';
		}

		print '</table>';
		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="update" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';

	}
}



llxFooter();

$db->close();
