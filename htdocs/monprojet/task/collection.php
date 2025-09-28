<?php
/* Copyright (C) 2005		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2014	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2011		Juanjo Menent			<jmenent@2byte.es>
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
 *	\file       htdocs/monprojet/task/payment.php
 *	\ingroup    project
 *	\brief      Page to add new payment valid or approved spent on a task
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/unit/class/units.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskaddext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formaddmon.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
if ($conf->budget->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
}
if ($conf->request->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/request/class/requestitem.class.php';
}
//images
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$langs->load('projects');
$langs->load('monprojet@monprojet');

$id=GETPOST('id','int');
$idr=GETPOST('idr','int');
$ref=GETPOST('ref','alpha');
$lineid=GETPOST('lineid','int');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$withproject=GETPOST('withproject','int');
$project_ref=GETPOST('project_ref','alpha');
$riid=GETPOST('riid','int'); //request_item_id

// Security check
$socid=0;
$lDisabled = false;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->projet->lire) accessforbidden();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
//echo '<hr>enviando';
$hookmanager->initHooks(array('projecttaskcard','globalcard','monprojet','doActions'));

$object = new Task($db);
$objadd = new Taskext($db);
$objecttaskadd = new Projettaskaddext($db);
$objpay  = new Projetpayment($db); //cabecera de pago
$taskpay = new Projettaskpayment($db); //tareas pago
$mobject = new Taskext($db);
$projectstatic = new Projectext($db);
$projectadd = new Projetadd($db);
$objdoc = new Projettasktimedoc($db);
$unit = new Units($db);
$extrafields = new ExtraFields($db);
if ($conf->budget->enabled) $items = new Items($db);
$objuser = new User($db);

if ($id || $ref)
{
	$object->fetch($id,$ref);
}
// fetch optionals attributes and labels
$extralabels_project=$extrafields->fetch_name_optionals_label($projectstatic->table_element);

$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

/*
 * Actions
 */

if ($action == 'addtaskpay' && $user->rights->monprojet->pay->crear)
{
	$error=0;

	//cramos el registro de pago en statut 0

	if (empty($_POST["unit_declared"]))
	{
		$langs->load("errors");
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Unitdeclared")), 'errors');
		$error++;
	}
	if (! $error)
	{
		//add photo/////////////////////////////////////////
		// Logo/Photo save
		$object->fetch($id);
		$newfile = '';
		$dirtask = $object->ref;
		$projectstatic->fetch($object->fk_project);
		$dirproj = $projectstatic->ref;
		$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask.'/pay';
		//verificamos que proceso de pago esta pendiente en el proyecto
		//solo puede existir un proceso de pago pendiente
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_projet = ".$object->fk_project;
		$filterstatic.= " AND t.statut = 0";
		$numpay = $objpay->fetchall('', '', 0, 0,$filter, 'AND',$filterstatic,true);
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			$code = generarcodigo(3);
			$newfile = $object->id.$code;
			//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->monprojet->pho->up4;
			if ($user->rights->monprojet->pho->up3) $mode = 3;
			if ($user->rights->monprojet->pho->up2) $mode = 2;
			if ($user->rights->monprojet->pho->up1) $mode = 1;
			if ($user->rights->monprojet->pho->up5) $mode = 5;


			if (GETPOST('deletedocfin'))
			{
				$fileimg=$dir.'/'.$namefile;
				$dirthumbs=$dir.'/thumbs';
				dol_delete_file($fileimg);
				dol_delete_dir_recursive($dirthumbs);
			}
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
							$file = $newfile;
						else
							$file.= '.'.$val;
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
			else
			{
				$error++;
				$errors[] = "ErrorBadImageFormat";
			}
			switch($_FILES['docpdf']['error'])
			{
				case 1:
			   //uploaded file exceeds the upload_max_filesize directive in php.ini
				case 2:
			   //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
				$errors[] = "ErrorFileSizeTooLarge";
				$error++;
				break;
				case 3:
			  //uploaded file was only partially uploaded
				$error++;
				$errors[] = "ErrorFilePartiallyUploaded";
				break;
			}
		}
		//fin photo
		$db->begin();
		if ($numpay == 0)
		{
			$code = generarcodigo(4);
			//creamos la cabecera del pago
			$objpay->initAsSpecimen();
			$objpay->fk_projet = $object->fk_project;

			$objpay->ref = '(PROV)'.$code;
			$objpay->date_payment = dol_now();
			$objpay->date_request = dol_now();
			$objpay->fk_facture = 0;
			$objpay->fk_user_create = $user->id;
			$objpay->fk_user_mod = $user->id;
			$objpay->amount = 0;
			$objpay->date_create = dol_now();
			$objpay->date_mod = dol_now();
			$objpay->tms = dol_now();
			$objpay->statut = 0;
			$idpay = $objpay->create($user);
			if ($idpay <= 0)
			{
				setEventMessages($objpay->error,$objpay->errors,'errors');
				$error++;
			}
		}
		else
			$idpay = $objpay->id;

		if ($idpay>0)
		{
			//realizando el registro nuevo
			if ($user->rights->monprojet->payp->mod || $user->admin)
				$date_create = dol_mktime(12, 0, 0, GETPOST('timemonth'),GETPOST('timeday'),GETPOST('timeyear'));
			else
				$date_create = dol_now();
			$taskpay->initAsSpecimen();
			$taskpay->fk_task = $object->id;
			$taskpay->fk_projet_payment = $idpay;
			$taskpay->detail = GETPOST('detail','alpha');
			$taskpay->unit_declared = GETPOST('unit_declared');
			$taskpay->fk_user_create = $user->id;
			$taskpay->document = $newfile;
			$taskpay->date_create = $date_create;
			$taskpay->fk_user_mod = $user->id;
			$taskpay->tms = dol_now();
			$taskpay->statut = 0;
			$result = $taskpay->create($user);
			if ($result >= 0)
			{
				setEventMessages($langs->trans("RecordSaved"),null,'mesgs');
			}
			else
			{
				setEventMessages($langs->trans($taskpay->error),null,'errors');
				$error++;
			}
		}
		else
		{
			setEventMessages($langs->trans('Error, no existe pago'),null,'errors');
			$error++;
		}

		if (!$error)
		{
			$db->commit();
			// update OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/task/payment.php?id='.$object->id.'&withproject=1',1);
			header("Location: ".$urltogo);
			exit;
		}
		else
			$db->rollback();
	}
	else
	{
		$action='';
	}
}

if ($action == 'adddoc' && $user->rights->monprojet->pay->crear)
{
	$error=0;
	if (! $error)
	{
		$object->fetch($id, $ref);
	//$res=$object->fetch_optionals($object->id,$extralabels);
		$taskpay->fetch($lineid);
		$object->fetch_projet();
		$newfile = '';
		if (empty($object->projet->statut))
		{
			setEventMessage($langs->trans("ProjectMustBeValidatedFirst"),'errors');
			$error++;
		}
		else
		{
		//buscamos
			$db->begin();

		//add photo
		// Logo/Photo save
			$code = generarcodigo(3);
			$newfile = $lineid.$code;
			$dirtask = $object->ref;
			$projectstatic->fetch($object->fk_project);
			$dirproj = $projectstatic->ref;
			$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask.'/pay';
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

				if (GETPOST('deletedocfin'))
				{
					$fileimg=$dir.'/'.$namefile;
					$dirthumbs=$dir.'/thumbs';
					dol_delete_file($fileimg);
					dol_delete_dir_recursive($dirthumbs);
				}
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
								$file = $newfile;
							else
								$file.= '.'.$val;
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
					//actualizando
					//buscamos el archivo
					if ($taskpay->id == $lineid)
					{
						//modificamos
						if (empty($taskpay->document))
							$taskpay->document = $file;
						else
							$taskpay->document.=';'.$file;
						$taskpay->tms = dol_now();
						$taskpay->statut = 0;
						$res = $taskpay->update($user);
						if (!$res>0) $error++;
					}
					else
					{
						$error++;
					}

				}
				else
				{
					$error++;
					$errors[] = "ErrorBadImageFormat";
				}
			}
		}
	}
	else
	{
		$action='';
	}
	if (!$error)
	{
		$db->commit();
		unset($_POST);
	}
	else
		$db->rollback();
}


if ($action == 'updateline' && ! $_POST["cancel"] && $user->rights->monprojet->pay->crear)
{
	$error=0;

	if (empty($_POST["unit_declared"]))
	{
		setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Unitdeclared")),'errors');
		$error++;
	}

	if (! $error)
	{
		//verificamos que proceso de pago esta pendiente en el proyecto
		//solo puede existir un proceso de pago pendiente
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_projet = ".$object->fk_project;
		$filterstatic.= " AND t.statut = 0";
		$numpay = $objpay->fetchall('', '', 0, 0,$filter, 'AND',$filterstatic,true);
		if ($numpay == 0)
		{
			//creamos la cabecera del pago
			$objpay->initAsSpecimen();
			$objpay->fk_projet = $object->fk_project;
			$objpay->ref = '(PROV)';
			$objpay->date_payment = dol_now();
			$objpay->fk_user_create = $user->id;
			$objpay->fk_user_mod = $user->id;
			$objpay->date_create = dol_now();
			$objpay->tms = dol_now();
			$objpay->statut = 0;
			$idpay = $objpay->create($user);
			if (!$idpay > 0) $error++;
		}
		else
			$idpay = $objpay->id;
		$db->begin();
		$object->fetch($id, $ref);
		$taskpay->fetch(GETPOST('lineid'));
		if ($taskpay->id == GETPOST('lineid'))
		{
						//realizando el registro nuevo
			if ($user->rights->monprojet->payp->mod || $user->admin)
				$taskpay->date_create = dol_mktime(12, 0, 0, GETPOST('timemonth'),GETPOST('timeday'),GETPOST('timeyear'));

			$taskpay->fk_projet_payment = $idpay;
			$taskpay->unit_declared = GETPOST('unit_declared');
			$taskpay->tms = dol_now();
			$taskpay->fk_user_mod = $user->id;
			$res = $taskpay->update($user);
			if (!$res > 0) $error++;
		}
	}
	$action = 'view';
	if (empty($error))
		$db->commit();
	else
		$db->rollback();
}

if ($action == 'confirm_delete' && $confirm == "yes" && $user->rights->monprojet->pay->del)
{
	$db->begin();
	$object->fetch($id, $ref);
	$taskpay->fetch(GETPOST('lineid'));
	//$res=$object->fetch_optionals($object->id,$extralabels);
	//borrando
	if ($taskpay->id == GETPOST('lineid') && $taskpay->fk_task == $id)
	{
		$res = $taskpay->delete($user);
		if (!$res > 0) $error++;
		if (!$error)
		{
			$db->commit();
			unset($_GET);
		}
		else
			$db->rollback();
	}
}
// Retreive First Task ID of Project if withprojet is on to allow project prev next to work
if (! empty($project_ref) && ! empty($withproject))
{
	//echo '<hr>'.$object->rang; exit;

	if ($projectstatic->fetch(0,$project_ref) > 0)
	{
		$tasksarray=$object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
		if (count($tasksarray) > 0)
		{
			$id=$tasksarray[0]->id;
		}
		else
		{
			header("Location: ".DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.($withproject?'&withproject=1':'').(empty($mode)?'':'&mode='.$mode));
			exit;
		}
	}
}


/*
 * View
 */

llxHeader("",$langs->trans("Task"));

$form = new Formv($db);
$formother = new FormOther($db);
$userstatic = new User($db);

if ($id > 0 || ! empty($ref))
{
	/*
	 * Fiche projet en mode visu
	 */
	if ($object->fetch($id, $ref) >= 0)
	{
		$objecttaskadd->fetch('',$id);
		$objadd->fetch($id,$ref);
		$res=$objadd->fetch_optionals($objadd->id,$extralabels);
		$res=$object->fetch_optionals($object->id,$extralabels);
		if ($object->array_options['options_c_grupo'] == 1)
			$lDisabled = true;
		$mobject->fetch($id, $ref);

		$result=$projectstatic->fetch($object->fk_project);
		$res=$projectstatic->fetch_optionals($projectstatic->id,$extralabels_project);
		$projectadd->fetch(0,$object->fk_project);
		if (! empty($projectstatic->socid)) $projectstatic->fetch_thirdparty();

		$object->project = dol_clone($projectstatic);

		$userWrite = $projectstatic->restrictedProjectArea($user,'write');
		if ($withproject)
		{
			// Tabs for project
			$tab='tasks';
			$head=project_prepare_head($projectstatic);
			dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

			$param=($mode=='mine'?'&mode=mine':'');

			print '<table class="border" width="100%">';

			// Ref
			print '<tr><td width="30%">';
			print $langs->trans("Ref");
			print '</td><td>';
			if (! $user->rights->projet->all->lire)
			{
				$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
				$projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
			}
			print $form->showrefnav($projectstatic,'project_ref','',1,'ref','ref','',$param.'&withproject=1');
			print '</td></tr>';

			// Label
			print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';

			// // Thirdparty
			// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
			// if (! empty($projectstatic->thirdparty->id)) print $projectstatic->thirdparty->getNomUrl(1);
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

			dol_fiche_end();
		}

		$head=task_prepare_head($object);
		dol_fiche_head($head, 'task_pay', $langs->trans("Task"),0,'projecttask');

		if ($action == 'deleteline')
		{
			print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&lineid='.$_GET["lineid"].($withproject?'&withproject=1':''),$langs->trans("Deletepayment"),$langs->trans("Paymentconfirmdeletetask"),"confirm_delete",'','',1);
		}

		print '<table class="border" width="100%">';

		$param=($withproject?'&withproject=1':'');
		$linkback=$withproject?'<a href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$projectstatic->id.'">'.$langs->trans("BackToList").'</a>':'';

		// Ref
		print '<tr><td width="30%">';
		print $langs->trans("Ref");
		print '</td><td colspan="3">';
		if (! GETPOST('withproject') || empty($projectstatic->id))
		{
			$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,1);
			$object->next_prev_filter=" fk_projet in (".$projectsListId.")";
			$objadd->next_prev_filter=" fk_projet in (".$projectsListId.")";
		}
		else
		{
			$object->next_prev_filter=" fk_projet = ".$projectstatic->id;
			$objadd->next_prev_filter=" fk_projet = ".$projectstatic->id;
		}
		print $form->showrefnavadd($objadd,'id',$linkback,1,'ref','ref','',$param);
		print '</td></tr>';

		// Label
		print '<tr><td>'.$langs->trans("Label").'</td><td colspan="3">'.$object->label.'</td></tr>';

		// Other options
		if (!$lDisabled)
		{
			$parameters=array('newaction'=>'viewprogram');
			$reshook=$hookmanager->executeHooks('doActions',$parameters,$objadd,$action);
		 	// Note that $action and $object may have been modified by hook
			if (empty($reshook) && ! empty($extrafields->attribute_label))
			{
				print $object->showOptionals($extrafields);
			}
		}
		print '</table>';

		dol_fiche_end();

		//button
		print '<div class="tabsAction">'."\n";
		print '<div class="inline-block divButAction">';
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$object->fk_project.'&action=payadv">'.$langs->trans("Return").'</a>';
		print '</div>';
		print '</div>';
		$lRegprogram = true;
		//verificamos si el proyecto require registro de programaciones
		if ($projectadd->programmed)
		{
			$lRegprogram = false;
			//verificamos si esta en solicitudes aprobadas vigentes
			$requestitem = new Requestitem($db);
			$filter = array('fk_statut'=>2);
			$filterstatic = " AND t.ref = '".$object->ref."'";
			$numreg = $requestitem->fetchAll('ASC', 't.datev', 0, 0, $filter, 'AND',$filterstatic);
			if ($numreg > 0)
			{
				$lItem = true;
				if (empty($riid))
				{
					foreach ($requestitem->lines AS $k => $lineitem)
					{
						if (empty($riid))
						{
							if ($lItem)
							{
								$lRegprogram = true;
								$riid = $lineitem->id;
								$lItem = false;
							}
						}
					}
				}
				else
					$lRegprogram = true;
			}
			else
				$lRegprogram = false;
		}
		else
		{
			$riid = 0;
			$lRregprogram = true;
		}

	 	//verificamos si esta habiitado para la carga de datos en la tarea
		if (!$user->admin)
			$lregtask = verifcontacttask($user,$object);
		else
			$lregtask = true;

		//sumamos todo lo pagado para la tarea
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_task = ".$object->id;
		$filterstatic.= " AND t.statut = 3";
		$numtaskp = $taskpay->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
		$numtaskp = $numtaskp + 0;
		$totalpayaprob = 0;
		for ($i= 0; $i < $numtaskp; $i++)
		{
			$line = $taskpay->lines[$i];
			$totalpayaprob+= $line->unit_declared;
		}
		// Add payment for contratist
		//solo puede existir un proceso de pago pendiente
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_projet = ".$object->fk_project;
		$filterstatic.= " AND t.statut = 0";
		$numpay = $objpay->fetchall('', '', 0, 0,$filter, 'AND',$filterstatic,true);
				//verificamos si existe registro pendiente
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_task = ".$object->id;
		$filterstatic.= " AND t.statut <= 2";
		$numtask = $taskpay->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
		$numtask = $numtask + 0;
		//solo mostramos lo que falta por pagar
		if (!$lDisabled && $user->rights->monprojet->pay->crear && $projectstatic->statut == 1 && $numtask <=0 && $object->fk_statut <= 2 && $action != 'editlinep' && $lRegprogram)
		{
			$datelimit = $db->idate(dol_now());
			//$datelimit = dol_now();
			
			if ($numpay)
				$datelimit = $db->idate($objpay->date_payment);
			//if ($numpay)
			//	$datelimit = $objpay->date_payment;
			
			//$objdoc->getsum($object->id,0,1,0,$datelimit);
			//REVISAR EN QUE AFECTA			
			$objdoc->getsum($object->id,0,0,0,$datelimit);
			//echo '<hr>aprob '.$totalpayaprob.' tot '.$objdoc->total;
			$balance = price2num($objdoc->total - $totalpayaprob,'MT') +0;
			if ($balance > 0)
			{
				print '<form enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="addtaskpay">';
				print '<input type="hidden" name="id" value="'.$object->id.'">';
				print '<input type="hidden" name="withproject" value="'.$withproject.'">';

				print '<table class="noborder" width="100%">';

				print '<tr class="liste_titre">';
				print '<td width="100">'.$langs->trans("Date").'</td>';
				print '<td>'.$langs->trans("By").'</td>';
				print '<td>'.$langs->trans("Note").'</td>';
				print '<td>'.$langs->trans("Forcollection").'</td>';
				print '<td align="center">'.$langs->trans("Attachment").'</td>';
				print '<td align="right" colspan="2">'.$langs->trans("Action").'</td>';
				print "</tr>\n";

				print '<tr '.$bc[false].'>';

					// Date
				print '<td class="nowrap">';
				$newdate=dol_mktime(12,0,0,$_POST["timemonth"],$_POST["timeday"],$_POST["timeyear"]);
				if ($user->admin || $user->rights->monprojet->payp->mod)
					print $form->select_date($newdate,'time','','','',"timespent_date");
				else
					print dol_print_date(($objpay->date_payment?$objpay->date_payment:dol_now()),'day');
				print '</td>';

					// Contributor contratist
				print '<td class="nowrap">';
				print img_object('','user','class="hideonsmartphone"');
				print $user->login;
				print '<input type="hidden" name="userid" value="'.$user->id.'">';
				print '</td>';

					// detail
				print '<td class="nowrap">';
				print '<textarea name="detail" cols="40" rows="1">'.($_POST['detail']?$_POST['detail']:$langs->trans('Accumulatedtodate')).'</textarea>';
				print '</td>';

			// unit declared
			//solo mostramos lo que falta por pagar
				$balance = price2num($objdoc->total - $totalpayaprob,'MT') +0;
				print '<td class="nowrap">';
				print '<input type="number" step="any" min="0" class="len100" name="unit_declared" value="'.$balance.'">';
				print '</td>';

					//Photo
				print '<td nowrap align="right" class="SI-FILES-STYLIZED">';
				print '<label class="cabinet">';
				include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/adddoc.tpl.php';
				print '</label>';
				print '</td>';

				print '<td align="center">';
				print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
				print '</td></tr>';

				print '</table></form>';
			}
		}
		print '<br>';
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_task = ".$object->id;
		$numtaskpay = $taskpay->fetchall('DESC', 'date_create', 0, 0,$filter, 'AND',$filterstatic,false);
		$var=true;

		if (!$lDisabled)
		{
			if ($user->rights->monprojet->pay->crear)
			{
				if ($action == 'editline')
					print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
				elseif ($action == 'editlinep')
					print '<form  enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
				print '<input type="hidden" name="id" value="'.$object->id.'">';
				print '<input type="hidden" name="lineid" value="'.$lineid.'">';
				print '<input type="hidden" name="withproject" value="'.$withproject.'">';

				if ($action == 'editline')
					print '<input type="hidden" name="action" value="updateline">';
				elseif ($action == 'editlinep')
					print '<input type="hidden" name="action" value="adddoc">';
			}

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td width="10%">'.$langs->trans("Date").'</td>';
			print '<td width="20%">'.$langs->trans("By").'</td>';
			print '<td width="30%" align="left">'.$langs->trans("Note").'</td>';
			print '<td width="5%" align="right">'.$langs->trans("Receivable").'</td>';
			print '<td width="6%" align="right">'.$langs->trans("Forcollection").'</td>';
			print '<td width="25%" align="center">'.$langs->trans("Attachment").'</td>';
			print '<td width="4%">&nbsp;'.$langs->trans('Statut').'</td>';
			print "</tr>\n";

			$total = 0;
			$totaldeclared = 0;
			$sumdeclared = 0;
			$sumforpay   = 0;
			for ($i= 0; $i < $numtaskpay; $i++)
			{
				$var=!$var;
				$line = $taskpay->lines[$i];
						//si el pago esta en estado 0 $line->statut == 0
				$declared = 0;
				if ($line->statut == 0)
				{
					$objdoc->getsum($object->id,0,1,0,dol_now());
					$declared = $objdoc->total - $totalpayaprob;
				}
				print "<tr ".$bc[$var].">";
						// Date
				print '<td>';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $line->id)
				{
					print $form->select_date($line->date_create,'time','','','',"timespent_date");
				}
				else
				{
					print dol_print_date($line->date_create,'day');
				}
				print '</td>';
						// User
				print '<td>';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $line->id)
				{
					print $user->login;
				}
				else
				{
					$objuser->fetch($line->fk_user_create);
					$userstatic->id         = $line->fk_user_create;
					$userstatic->lastname	= $objuser->lastname;
					$userstatic->firstname 	= $objuser->firstname;
					print $userstatic->getNomUrl(1);
				}
				print '</td>';

						// Note
				print '<td align="left">';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $line->id)
				{
					print '<textarea name="detail" cols="40" rows="1">'.$line->detail.'</textarea>';
				}
				else
				{
					print dol_nl2br($line->detail);
				}
				print '</td>';

						//units payable
				print '<td align="right">';
				if ($line->statut == 0)
				{
					print price($declared);
					$sumdeclared+= $declared;
				}
				else
					print '';

				print '</td>';

						//unit for payment
				print '<td align="right">';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $line->id)
				{
					print '<input type="number" min="0" step="any" name="unit_declared" value="'.$line->unit_declared.'">';
					$sumforpay+= $line->unit_declared;
				}
				else
				{
					print price($line->unit_declared);
					$sumforpay+= $line->unit_declared;
				}

				print '</td>';

						//photo
				print '<td align="center" nowrap class="SI-FILES-STYLIZED">';
						//$objdoc->fetch('',$task_time->rowid);
				if (!empty($line->document))
				{
							//recuperamos los nombres de archivo
					$aDoc = explode(';',$line->document);
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

						if ($action != 'editlinep')
							print '&nbsp;'.$taskpay->showphoto($typedoc,$line,$doc,$object,$projectstatic, 100,$docext);
					}
					if ($action != 'editlinep' && $line->statut < 3)
					{
								// //revisar permiso
								// if ($lregtask)
						print '&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$line->id.'&action=editlinep'.'&withproject=1">'.img_picto($langs->trans('Newdattachment'),'edit_add').'</a>';
					}
				}
				else
				{
					print '&nbsp;';
					if ($action != 'editlinep' && $line->statut < 3)
					{
						print '&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$line->id.'&action=editlinep'.'&withproject=1">'.img_picto($langs->trans('Newattachment'),'edit_add').'</a>';
					}
				}

						//para subir nuevo archivo
				if ($action == 'editlinep'  &&
					$_GET['lineid'] == $line->id)
				{
					print '<label class="cabinet">';
					include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/adddoc.tpl.php';
					print '</label>';
				}

				print '</td>';

						// Edit and delete icon
				print '<td align="right" nowrap valign="middle" width="80">';
				print $taskpay->LibStatut($line->statut);
				if ($action == 'editline' && $_GET['lineid'] == $line->id)
				{
					print '<input type="hidden" name="lineid" value="'.$_GET['lineid'].'">';
					print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
					print '<br>';
					print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
				}
				elseif ($action == 'editlinep' && $_GET['lineid'] == $line->id)
				{
					print '<input type="hidden" name="lineid" value="'.$_GET['lineid'].'">';
					print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
					print '<br>';
					print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
				}
				else if ($user->rights->monprojet->pay->crear && $line->statut <=1)
				{
					print '&nbsp;';
					print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->id.($withproject?'&amp;withproject=1':'').'">';
					print img_edit();
					print '</a>';

				}
				if ($action != 'editlinep' && $user->rights->monprojet->pay->del && $line->statut <= 1)
				{
					print '&nbsp;';
					print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;lineid='.$line->id.($withproject?'&amp;withproject=1':'').'">';
					print img_delete();
					print '</a>';
				}
				print '</td>';

				print "</tr>\n";
				$total += $line->unit_declared;
			}
		}
		print '<tr class="liste_total"><td colspan="3" class="liste_total">'.$langs->trans("Total").'</td>';
		print '<td align="right" class="nowrap liste_total">'.price(price2num($sumdeclared,'MT')).'</td>';
		print '<td align="right" class="nowrap liste_total">'.price(price2num($sumforpay,'MT')).'</td>';
		print '<td colspan="2"></td>';
				//		print '<td align="right" class="nowrap liste_total">'.convertSecondToTime($total,'allhourmin').'</td><td>&nbsp;</td>';
		print '</tr>';
		print "</table>";
	}
}


llxFooter();
$db->close();
