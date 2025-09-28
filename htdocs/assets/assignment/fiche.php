<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/assets/assignment/fiche.php
 *	\ingroup    Assets
 *	\brief      Page fiche assets assignment
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/core/modules/assets/modules_assets.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/email.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mpropertyuser.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
}
if ($conf->projet->enabled && $conf->monprojet->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formprojetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
}
elseif ($conf->projet->enabled)
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

//para envio correo
require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("assets");

$action = GETPOST('action','alpha');
$id     = GETPOST('id','int');
$idr    = GETPOST('idr','int');
$aid 	= GETPOST('aid','int');
$sel 	= GETPOST('sel','int');
$sol 	= GETPOST('sol','int');
$fk_equipment = GETPOST('fk_equipment','int');

if ($sel != 1) unset($_SESSION['aPost']);

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

$mesg = '';
if (!$user->rights->assets->alloc->leer)
	accessforbidden();

$object = new Assetsassignmentext($db);
$objass  = new Assetsext($db);
$objProperty  = new Mproperty($db);
$objprouser = new Mpropertyuser($db);
$objLocation  = new Mlocation($db);
$objDepartamentuser = new Pdepartamentuserext($db);
$objgroup = new Cassetsgroup($db);
$objassigndet = new Assetsassignmentdetext($db);
$objuser = new User($db);
$objadh  = new Adherent($db);
if ($conf->monprojet->enabled)
	$projet = new Projectext($db);
elseif ($conf->projet->enabled)
	$projet = new Project($db);

if ($action == 'search')
	$action = 'createedit';

$filteruser = '';
if (!$user->admin)
{
	list($filteruser,$aProperty) = userproperty($user->id);
}

if ($id)
	$result = $object->fetch($id);
/*
 * Actions
 */
if ($action == 'builddoc')
// En get ou en post
{
	$id = GETPOST('id');
	$object->fetch($id);
	$object->fetch_thirdparty();
	$object->fetch_lines();

	if (GETPOST('model'))
	{
		$object->setDocModel($user, GETPOST('model'));
	}
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
	$result=assets_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
		exit;
	}
}


// Add
if ($action == 'add' && $user->rights->assets->alloc->crear)
{
	//si es por devolucion se recibe $aid
	$lines = array();
	if ($aid>0)
	{
		$objtmp = new Assetsassignmentext($db);
		$objtmp->fetch($aid);
		$objtmp->fetch_lines();
		$lines = $objtmp->lines;
	}
	//generamos el codigo para marcar activos
	$mark = generarcodigo(5);
	$now = dol_now();
	$error = 0;
	$object->date_assignment = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
	if (!$user->admin) $object->date_assignment = dol_now();

	$object->entity   = $conf->entity;
	//tipo de registro con varios items de activo
	$object->ref   			= '(PROV)';
	$object->fk_user   		= GETPOST('fk_user')+0;
	$object->fk_user_from   = GETPOST('fk_user_from')+0;
	$object->fk_user_to   	= GETPOST('fk_user_to')+0;
	if ($sol)
	{
		$object->fk_user   		= GETPOST('fk_user')+0;
		$object->fk_user_from   = GETPOST('fk_user')+0;
	}
	if ($aid>0)
	{
		$object->fk_user   		= GETPOST('fk_user_to')+0;
	}
	$object->fk_projet_from	= GETPOST('fk_projet_from')+0;
	$object->fk_projet   	= GETPOST('fk_projet')+0;
	$object->fk_property_from   = GETPOST('fk_property_from')+0;
	$object->type_assignment = GETPOST('type_assignment','int');
	$object->fk_property   	= GETPOST('fk_property')+0;
	$object->fk_location   	= GETPOST('fk_location')+0;
	$object->detail        	= GETPOST('detail');
	$object->origin        	= GETPOST('origin');
	$object->originid     	= GETPOST('originid')+0;
	$object->model_pdf 		= 'fractalassignment';
	$object->date_create = $now;
	$object->date_mod = $now;
	$object->mark = $mark;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->tms = $now;
	$object->status = 0;
	//validacion
	if ($object->fk_property<=0 && $object->fk_location<=0 && $object->fk_projet <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorPropertyLocationProjectisrequired"),null,'errors');
	}
	if (!$error)
	{
		$db->begin();
		$id = $object->create($user);
		if ($id <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			//revisamos si no viene de una seleccion de la lsita de activos
			$sel = GETPOST('sel');
			if ($sel>0)
			{
				$aSelass = unserialize(($_SESSION['aSelass']));

				if (count($aSelass)>0)
				{
					$objtemp = new Assetsassignmentdetext($db);

					foreach($aSelass AS $fk_asset => $value)
					{
						if (!$error)
						{
						//buscamos que no exista
							$filterstatic = " AND t.fk_asset_assignment = ".$id." AND t.fk_asset = ".$fk_asset;
							$restemp = $objtemp->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
							if ($value == 'on' && empty($restemp))
							{
								$objass->fetch($fk_asset);
								$objassigndet->fk_asset_assignment = $id;
								$objassigndet->fk_asset = $fk_asset;
								$objassigndet->date_assignment = $object->date_assignment;
								$objassigndet->date_create = $now;
								$objassigndet->fk_user_create = $user->id;
								$objassigndet->fk_user_mod = $user->id;
								$objassigndet->tms = $now;
								$objassigndet->date_mod = $now;
								$objassigndet->been = $objass->been;
								$objassigndet->status = 0;
								$objassigndet->active = 1;
								$idass = $objassigndet->create($user);
								if ($idass <=0)
								{
									$error++;
									setEventMessages($objassigdet->error,$objassigdet->errors,'errors');
								}
							}
						}
					}
				}
			}
			if ($aid && count($lines)>0)
			{
				//importamos todos los activos del anterior registro
				foreach ($lines AS $j => $line)
				{
					$objass->fetch($line->fk_asset);
					$objassigndet->fetch($line->id);
					$objassigndet->id = 0;
					$objassigndet->fk_asset_assignment = $id;
					$objassigndet->date_assignment = $now;
					$objassigndet->date_create = $now;
					$objassigndet->fk_user_create = $user->id;
					$objassigndet->fk_user_mod = $user->id;
					$objassigndet->tms = $now;
					$objassigndet->date_mod = $now;
					$objassigndet->been = $objass->been;
					$objassigndet->status = 0;
					$objassigndet->active = 1;
					$idass = $objassigndet->create($user);
					if ($idass <=0)
					{
						$error++;
						setEventMessages($objassigdet->error,$objassigdet->errors,'errors');
					}
				}
			}
			$object->fetch($id);
			$object->ref = '(PROV'.$object->id.')';
			$res = $object->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
			if ($aid>0) $action = 'createf';
			else $action="create";
		}
	}
	else
	{
		if ($aid>0) $action = 'createf';
		else $action="create";
	}
}

// Addassign //registro individual de asignacion
if ($action == 'addassign' && $user->rights->assets->alloc->crear)
{
	$error = 0;
	$now = dol_now();
	if ($result > 0)
	{

		$objassigndet->fk_asset_assignment = $id;
		$objassigndet->fk_asset = GETPOST('fk_asset');
		$objassigndet->date_assignment = $object->date_assignment;
		$objassigndet->date_create = $now;
		$objassigndet->fk_user_create = $user->id;
		$objassigndet->fk_user_mod = $user->id;
		$objassigndet->tms = $now;
		$objassigndet->date_mod = $now;
		$objassigndet->status = 0;
		$objassigndet->active = 1;
		//verificamos si el activo no esta bloqueado
		if ($objass->fetch($objassigndet->fk_asset)>0)
		{
			if ($objass->id == $objassigndet->fk_asset && (!empty(trim($objass->mark))))
			{
				$error++;
				setEventMessages($langs->trans('Activo reservado en otro requerimiento'),null,'errors');
			}
			$objassigndet->been = $objass->been;
		}
		if (empty($error))
		{
			$db->begin();
			$idass = $objassigndet->create($user);
			if ($idass > 0)
			{
				$objass->mark = $object->mark;
				$objass->statut = 1;
				$res = $objass->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objass->error,$objass->errors,'errors');
				}
				if (empty($error))
				{
					$db->commit();
					setEventMessage($objassigndet->error,'mesgs');
					header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
					exit;
				}
				else
				{
					$db->rollback();
					$action='';
				}
			}
			else
			{
				$db->rollback();
				setEventMessages($objassigndet->error,$objassigndet->errors,'errors');

				$action='';
			}
		}
		else
		{
			setEventMessage($langs->trans('The asset is no longer available'),null,'error');
			$action='';
		}
	}
	else
	{
		$action = '';
		setEventMessages($object->error,$object->errors,'error');
	}
}

// Add
if ($action == 'update' && $user->rights->assets->alloc->mod)
{
	if ($object->fetch($id))
	{
		$error = 0;
		if (empty($object->mark))
			$object->mark = generarcodigo(5);
		$object->date_assignment = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
		$object->ref   		= GETPOST('ref');
		$object->fk_user   	= GETPOST('fk_user');
		$object->fk_property   = GETPOST('fk_property');
		$object->fk_location   = GETPOST('fk_location');
		$object->detail        = GETPOST('detail');

		$object->tms = dol_now();

		//validacion
		if (empty($object->detail))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errordetailisrequired").'</div>';
		}
		if ($object->fk_property<=0)
		{
		//	$error++;
		//	$mesg.='<div class="error">'.$langs->trans("Errorpropertyisrequired").'</div>';
		}
		if ($object->fk_location<=0)
		{
		//	$error++;
		//	$mesg.='<div class="error">'.$langs->trans("Errorlocationisrequired").'</div>';
		}
		if ($object->fk_property<=0 && $object->fk_location <=0 && $object->fk_projet<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("PropertyProject")), null, 'errors');
		}
		if (empty($error))
		{
			$res = $object->update($user);
			if ($res > 0)
			{
				header("Location: fiche.php?id=".$id);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			if ($error)
				$action="edit";
		  // Force retour sur page creation
		}
	}
}

//confirm applyfor
if ($action == 'confirm_applyfor' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->sol)
{
	$error = 0;
	if ($object->fetch($id))
	{
		//iniciamos el guardado
		//verificamos la numeracion
		$ref = substr($object->ref, 1, 4);
		if ($ref == 'PROV')
		{
			$numref = $object->getNextNumRef($object);
		}
		else
		{
			$numref = $object->ref;
		}
		if ($numref == -1)
		{
			setEventMessages($langs->trans('Assignmentisnotcodedcorrectly, check'),null,'errors');
			$errror++;
			$action='';
		}
		else
		{
			if (empty($object->mark)) $object->mark = generarcodigo(5);

			$db->begin();
			//cambiando a solicitado
			$object->status = 1;
			$object->ref = $numref;
			//update
			$res = $object->update($user);
			if ($res <= 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			//lo segunod dejamos para cuando se apruebe
			if (!$error)
			{
				//iniciamos quitar la marca de los activos seleccionados
				$objassigndet->getlistassignment($object->id,0);
				$aArray = $objassigndet->array;
				foreach ((array) $aArray AS $j => $objdet)
				{
					//cambiamos de estado
					if ($objass->fetch($objdet->fk_asset) > 0)
					{
						$objass->statut = 1;
						$res = $objass->update($objuser);
						if ($res <= 0)
						{
							setEventMessages($objass->error,$objass->errors,'errors');
							$error++;
						}
						if (!$error)
						{
							//guardamos en historial
							add_historial($objass,$user,$db,$objdet->fk_asset,$object->ref,$objass->statut,'confirm applyfor');
						}
					}
				}
			}
			//enviamos correo para aceptacion
			$url = dol_buildpath('/assets/assignment/ficheapp.php?id='.$id,1);
			$url = 'pruebavisual.cluster.com.bo/assets/assignment/ficheapp.php';
			//para el envio de correo de a
			if ($object->origin)
			{
				$objuser->fetch($object->fk_user_from);
				$object->email = $objuser->email;
			}
			else
			{
				$objuser->fetch($object->fk_user);
				$object->email = $objuser->email;
			}
			$res = send_email_assignment($object,$url);
			if ($res<0)
			{
				$error++;
			}
			if (empty($error))
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$langs->trans('Itisnotpossibletovalidatethisassignment,pleasetryagain').'</div>';
				$action = '';
			}
		}
	}
}

//confirm transreturn
if ($action == 'confirm_transreturn' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->trans)
{
	$error = 0;
	if ($object->fetch($id))
	{
		//iniciamos el guardado
		//verificamos la numeracion
		$ref = substr($object->ref, 1, 4);
		if ($ref == 'PROV')
		{
			$numref = $object->getNextNumRef($object);
		}
		else
		{
			$numref = $object->ref;
		}
		if ($numref == -1)
		{
			setEventMessages($langs->trans('Assignmentisnotcodedcorrectly,check'),null,'errors');
			$errror++;
			$action='';
		}
		else
		{
			if (empty($object->mark)) $object->mark = generarcodigo(5);

			$db->begin();
			//cambiando a validado
			$object->status = 2;
			$object->ref = $numref;
			//update
			$res = $object->update($user);
			if ($res <= 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			//lo segunod dejamos para cuando se apruebe
			if (!$error)
			{
				//iniciamos quitar la marca de los activos seleccionados
				$objassigndet->getlistassignment($object->id,0);
				$aArray = $objassigndet->array;
				foreach ((array) $aArray AS $j => $objdet)
				{
					//cambiamos de estado
					//statut = 1  assignado
					if ($objass->fetch($objdet->fk_asset) > 0)
					{
						$objass->statut = 1;
						$res = $objass->update($objuser);
						if ($res <= 0)
						{
							setEventMessages($objass->error,$objass->errors,'errors');
							$error++;
						}
						if (!$error)
						{
							//guardamos en historial
							add_historial($object,$user,$db,$objdet->fk_asset,$object->ref,$objass->statut,'confirm_transreturn');
						}
					}
				}
			}
			//enviamos correo para aceptacion
			$url = dol_buildpath('/assets/assignment/ficheapp.php?id='.$id,1);
			$url = 'pruebavisual.cluster.com.bo/assets/assignment/ficheapp.php';
			//para el envio de correo de a
			if ($object->origin)
			{
				$objuser->fetch($object->fk_user_from);
				$object->email = $objuser->email;
			}
			else
			{
				$objuser->fetch($object->fk_user);
				$object->email = $objuser->email;
			}
			$res = send_email_assignment($object,$url);
			if ($res<0)
			{
				$error++;
			}
			if (empty($error))
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$langs->trans('It is not possible to validate this assignment, please try again').'</div>';
				$action = '';
			}
		}
	}
}

// confirm validate
if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->val)
{
	$error = 0;
	if ($object->fetch($id))
	{
		//iniciamos el guardado
		//verificamos la numeracion
		$ref = substr($object->ref, 1, 4);
		if ($ref == 'PROV')
		{
			$numref = $object->getNextNumRef($object);
		}
		else
		{
			$numref = $object->ref;
		}
		if ($numref == -1)
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
		else
		{
			if (empty($object->mark)) $object->mark = generarcodigo(5);
			$db->begin();
			//cambiando a validado
			$object->status = 2;
			$object->ref = $numref;
			//update
			$res = $object->update($user);
			if ($res <= 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			//lo segunod dejamos para cuando se apruebe
			if (!$error)
			{
				//iniciamos quitar la marca de los activos seleccionados
				$objassigndet->getlistassignment($object->id,0);
				$aArray = $objassigndet->array;
				foreach ((array) $aArray AS $j => $objdet)
				{
					//cambiamos de estado
					if ($objass->fetch($objdet->fk_asset) > 0)
					{
						$objass->statut = 2;
						$res = $objass->update($objuser);
						if ($res <= 0)
						{
							setEventMessages($objass->error,$objass->errors,'errors');
							$error++;
						}
						if (!$error)
						{
							//guardamos en historial
							add_historial($object,$user,$db,$objdet->fk_asset,$object->ref,$objass->statut,'confirm validate');
						}
					}
				}
			}

	        // Define output language
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$model=$object->modelpdf;
				//$ret = $object->fetch($id);
	    		// Reload to get new records
				$object->fetch_lines();
				$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
			//$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);


			//enviamos correo para aceptacion
			$url = dol_buildpath('/assets/assignment/ficheapp.php?id='.$id,1);
			$url = 'pruebavisual.cluster.com.bo/assets/assignment/ficheapp.php';
			$objuser->fetch($object->fk_user);
			$object->email = $objuser->email;
			$res = send_email_assignment($object,$url);
			if ($res<0)
			{
				setEventMessages($langs->trans('Error al enviar correo'),null,'errors');
				$error++;
			}
			if (empty($error))
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$langs->trans('It is not possible to validate this assignment, please try again').'</div>';
				$action = '';
			}
		}
	}
}

// confirm no accept
if ($action == 'confirm_noapproved' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->apr)
{

	$error = 0;
	if ($object->fetch($id))
	{
		//iniciamos el guardado
		$db->begin();
			//cambiando a borrador
		$object->status = -1;
		$object->mark = ' ';
		//update
		$res = $object->update($user);
		if ($res <= 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$error++;
		}
		if (empty($error))
		{
			//iniciamos quitar la marca de los activos seleccionados
			$objassigndet->getlistassignment($object->id,0);
			$aArray = $objassigndet->array;
			foreach ((array) $aArray AS $j => $objdet)
			{
				//quitamos la marka del activo para habilitar
				if ($objass->fetch($objdet->fk_asset) > 0)
				{
					$objass->mark = ' ';
					$objass->statut = 9;
					$res = $objass->update($user);
					if ($res <= 0)
					{
						setEventMessages($objass->error,$objass->errors,'errors');
						$error++;
					}
					if (!$error)
					{
						//guardamos en historial
						add_historial($object,$user,$db,$objdet->fk_asset,$object->ref,$objass->statut,'confirm_noapproved');
					}
				}
			}
		}
		if (empty($error))
		{
			$db->commit();
			unset($_SESSION['aTemp']);
			setEventMessages($langs->trans('Proceso concluido'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
}

// confirm aproved
if ($action == 'confirm_approved' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->apr)
{
	$error = 0;
	if ($object->fetch($_REQUEST["id"]))
	{
		$array = unserialize($_SESSION['aPostapproved']);
		$_POST = $array[$object->id];
		$selasset = GETPOST('selasset');
		$sel_released = GETPOST('sel_released');

		//iniciamos el guardado
		$db->begin();
		//cambiando a validado
		$object->status = 3;
		$object->mark = ' ';
			//update
		$res = $object->update($user);
		if ($res > 0)
		{
			//iniciamos la asignacion de los activos y su respectiva baja del anterior responsable
			$objassigndet->getlistassignment($object->id,0);
			$aArray = $objassigndet->array;
			//print_r($aArray);
			foreach ((array) $aArray AS $j => $objdet)
			{
				if ($selasset[$objdet->id])
				{
					//buscamos el ultimo activo asignado
					if (empty($error) && $objassigndet->fetch_active($objdet->fk_asset)>0)
					{
						if ($objassigndet->fk_asset == $objdet->fk_asset && !empty($objassigndet->id))
						{
							//damos de baja
							$objassigndet->date_end = dol_now();
							$objassigndet->status = 2;
							$res = $objassigndet->update($user);
							if ($res <=0)
							{
								setEventMessages($objassigndet->error,$objassigndet->errors,'errors');
								$error++;
							}
						}
						else
						{
							setEventMessages($langs->trans('No existe como ocupado'),null,'errors');
							$error++;
						}
					}
					if (!$error)
					{
						if ($objassigndet->fetch($objdet->id)>0)
						{
							//damos de alta
							$objassigndet->status = 1;
							if ($sel_released) $objassigndet->active = 0;
							$res = $objassigndet->update($user);
							if ($res <=0)
							{
								setEventMessages($objassigndet->error,$objassigndet->errors,'errors');
								$error++;
							}
							//quitamos la marka del activo para habilitar

							if ($objass->fetch($objdet->fk_asset)> 0)
							{
								//buscamos el departamentuser
								$filteruser = " AND t.fk_user = ".$object->fk_user;
								$res = $objDepartamentuser->fetchAll('','',0,0,array(1=>1),'AND',$filteruser,true);
								$objass->mark = ' ';
								$objass->statut = 3;
								$objass->fk_departament = $objDepartamentuser->fk_departament;
								$objass->fk_resp = $object->fk_user;
								if ($sel_released) $objass->statut = 9;

								$res = $objass->update($user);
								if ($res <=0)
								{
									setEventMessages($objass->error,$objass->errors,'errors');
									$error++;
								}
								if (!$error)
								{
									//guardamos en historial
									add_historial($object,$user,$db,$objdet->fk_asset,$object->ref,$objass->statut,'confirm_approved');
								}
							}
						}
					}
				}
			}
			if (empty($error))
			{
				unset($_SESSION['aTemp']);
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$action = '';
			}
		}
		else
		{
			$db->rollback();
			setEventMessages($object->error,$object->errors,'errors');
			$action='';
		}
	}
}

// confirm aproved
if ($action == 'confirm_free' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->lib)
{
	$error = 0;
	if ($result > 0)
	{
		$array = unserialize($_SESSION['aPost']);
		$_POST = $array[$object->id];
		$selasset = GETPOST('selasset');
		//iniciamos el guardado
		$db->begin();
		//no se cambia nada en object
			//iniciamos la liberación de los activos para asignacion
		$objassigndet->getlistassignment($object->id,0,1);

		$aArray = $objassigndet->array;
		foreach ((array) $aArray AS $j => $objdet)
		{
			if ($selasset[$objdet->id])
			{
					//quitamos la marka del activo para habilitar
				if ($objass->fetch($objdet->fk_asset)> 0)
				{
					//cambiamos active = 0;
					if ($objassigndet->fetch($objdet->id))
					{
						$objassigndet->active = 0;
						$res = $objassigndet->update($user);
						if ($res <=0)
						{
							setEventMessages($objassigndet->error,$objassigndet->errors,'errors');
							$error++;
						}
					}
					if (!$error)
					{
						//cambiamos el estado en assets
						$objass->mark = ' ';
						$objass->statut = 9;
						$res = $objass->update($user);
						if ($res <=0)
						{
							setEventMessages($objass->error,$objass->errors,'errors');
							$error++;
						}
						if (!$error)
						{
							//guardamos en historial
							add_historial($object,$user,$db,$objdet->fk_asset,$object->ref,$objass->statut,'confirm_free');
						}
					}
				}
			}
		}
		if (empty($error))
		{
			unset($_SESSION['aTemp']);
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
}
// Delete assets
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->alloc->del)
{
	if ($object->fetch($id)>0)
	{
		$db->begin();
		$res=$object->delete($user);
		if ($res > 0)
		{
			//iniciamos el borrado de la asignacion de los activos y su respectiva baja del nuevo registro
			$objassigndet->getlistassignmentact($id);
			$aArray = $objassigndet->array;
			foreach ((array) $aArray AS $j => $objdet)
			{
				//quitamos la marka del activo para habilitar
				if ($objass->fetch($objdet->fk_asset)> 0 && empty($error))
				{
					$objass->mark = '';
					$res = $objass->update($user);
					if ($res <=0) $error++;
				}
				if (!$error)
				{
					//guardamos en historial
					add_historial($object,$user,$db,$objdet->fk_asset,$object->ref,-1,'confirm_delete');
				}
				//eliminamos el registro
				if ($objassigndet->fetch($objdet->id)>0 && empty($error))
				{
					$res = $objassigndet->delete($user);
					if ($res <=0) $error++;
				}
			}
			if (empty($error))
			{
				$db->commit();
				header("Location: ".DOL_URL_ROOT.'/assets/assignment/liste.php');
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$langs->trans('The process concluded with errors, try again to').'</div>';
				$action == '';
			}
		}
		else
		{
			$db->rollback();
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
	}
}
// Delete assets assignment
if ($action == 'confirm_delete_asset' && $_REQUEST["confirm"] == 'yes' && ($user->rights->assets->alloc->del || $user->rights->assets->alloc->crear && $object->status == 0))
{
	$error = 0;
	$objassigndet->fetch($_REQUEST["idr"]);
	if ($objassigndet->fk_asset_assignment == $id)
	{
	  	//habilitamos el activo
		if ($objass->fetch($objassigndet->fk_asset) > 0)
		{
			$db->begin();
			if ($objass->id == $objassigndet->fk_asset)
			{
				$objass->mark = ' ';
				$objass->status = 9;
				$res = $objass->update($user);
				if ($res <= 0)
				{
					setEventMessages($objass->error,$objass->errors,'errros');
					$error++;
				}
				if (!$error)
				{
					//guardamos en historial
					add_historial($objassigndet,$user,$db,$objassigndet->fk_asset,'none',$objass->statut,'confirm_delete_asset');
				}
			}
			if (!$error)
			{
				$res=$objassigndet->delete($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($objassigndet->error,$objassigndet->errors,'errros');
				}
			}
			if (!$error)
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$action = '';
			}
		}
		else
		{
			setEventMessages($objass->error,$objass->errors,'errros');
			$action='';
		}
	}
}

if ( ($action == 'createedit') )
{
	//require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	$date_adq = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
	$tmparray['ref'] = GETPOST('ref');
	$tmparray['fk_user'] = GETPOST('fk_user');
	$tmparray['fk_property'] = GETPOST('fk_property');
	$tmparray['fk_property_from'] = GETPOST('fk_property_from');
	$tmparray['fk_projet'] = GETPOST('fk_projet');
	$tmparray['fk_location'] = GETPOST('fk_location');
	$tmparray['date_assignment'] = $date_adq;
	$tmparray['detail'] = GETPOST('detail');

	//buscamos la codificacion del activo
	if ($tmparray['fk_property'])
	{
		$object->ref             = $tmparray['ref'];
		$object->fk_user         = $tmparray['fk_user'];
		$object->fk_property_from= $tmparray['fk_property_from'];
		$object->fk_property     = $tmparray['fk_property'];
		$object->fk_projet       = $tmparray['fk_projet'];
		$object->fk_location     = $tmparray['fk_location'];
		$object->date_assignment = $tmparray['date_assignment'];
		$object->detail          = $tmparray['detail'];
	}
	$action='create';
}

if ( ($action == 'createassignsearch') )
{
	$date_assign = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));

	$tmparray['fk_user'] = GETPOST('fk_user');
	$tmparray['fk_property'] = GETPOST('fk_property');
	$tmparray['date_assign'] = $date_assign;

	//buscamos la codificacion del activo
	if ($tmparray['fk_property'])
	{
		$objassigndet->fk_user = $tmparray['fk_user'];
		$objassigndet->fk_property = $tmparray['fk_property'];
		$objassigndet->date_assignment = $tmparray['date_assign'];
	}
	$action='create';
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/*
 * View
*/


$form=new Formv($db);
$formfile = new FormFile($db);

if ($conf->projet->enabled && $conf->monprojet->enabled)
	$formproject = new FormProjetsext($db);

$aArrcss= array('assets/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';
llxHeader("",$langs->trans("Assets"),$help_url,'','','','',$aArrcss);

if (!$user->admin)
{
	if (empty($aProperty))
	{
		setEventMessages($langs->trans('No tiene definido Inmuebles a su cargo, consulte con el Administrador del Sistema'),null,'mesgs');
	}
}

if ($user->rights->assets->alloc->crear && ($action == 'create' || $action == 'createf'))
{
	if ($action == 'createf' && $aid)
	{
		$objtmp = new Assetsassignmentext($db);
		$objtmp->fetch($aid);
	}
	if (GETPOST('origin') && GETPOST('originid'))
	{
		if (GETPOST('origin') == 'projet')
		{
			$element = 'monprojet';
			$subelement = 'projectext';
		}
		if (GETPOST('origin') == 'assignment')
		{
			$element = 'assets';
			$subelement = 'assetsassignment';
		}
		dol_include_once('/'.$element.'/class/'.$subelement.'.class.php');
		$classname = ucfirst($subelement);
		$objectsrc = new $classname($db);

		$objectsrc->fetch(GETPOST('originid'));
		if ($action == 'createf')
		{
			$fk_projet_from = $objectsrc->fk_projet;
		}
		else
		{
			$fk_projet = $objectsrc->id;
		}
		$_POST['options_place_event'] = $objectsrc->array_options['options_place_event'];
		$aDate = dol_getdate($db->jdate($objectsrc->array_options['options_date_event']));

		$_POST['options_date_event'] = $db->jdate($objectsrc->array_options['options_date_event']);
		$_POST['options_date_eventday'] = $aDate['mday'];
		$_POST['options_date_eventmonth'] = $aDate['mon'];
		$_POST['options_date_eventyear'] = $aDate['year'];
		$_POST['options_date_eventhour'] = $aDate['hours'];
		$_POST['options_date_eventmin'] = $aDate['minutes'];
	}

	if ($sol)
		print_fiche_titre($langs->trans("Newrequest"));
	else
		print_fiche_titre($langs->trans("Newassignment"));

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#fk_property").change(function() {
			document.fiche_assig.action.value="'.$action.'";
			document.fiche_assig.submit();
		});

	});';
	print '</script>'."\n";

	print '<form name="fiche_assig" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="origin" value="'.GETPOST('origin').'">';
	print '<input type="hidden" name="originid" value="'.GETPOST('originid').'">';
	print '<input type="hidden" name="sol" value="'.$sol.'">';
	print '<input type="hidden" name="sel" value="'.GETPOST('sel').'">';

	if ($action == 'createf')
		print '<input type="hidden" name="aid" value="'.GETPOST('aid').'">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border centpercent">';

	//ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Code').'</td><td colspan="2">';
	print '(PROV)';
	print '<input type="hidden" name="ref" value="'.(empty($object->ref)?'(PROV)':$object->ref).'">';
	print '</td></tr>';

	//date assignment
	if ($sol)
		print '<tr><td class="fieldrequired">'.$langs->trans('Requestdate').'</td><td colspan="2">';
	else
		print '<tr><td class="fieldrequired">'.$langs->trans('Dateassignment').'</td><td colspan="2">';

	if ($user->admin)
	{
		$form->select_date($object->date_assignment,'da_','','','',"date",1,1);
	}
	else
	{
		print dol_print_date(dol_now(),'dayhour');
	}
	print '</td></tr>';
	//projet
	if ($conf->projet->enabled && $conf->monprojet->enabled)
	{
		if ($fk_projet_from)
		{
			$projet->fetch($fk_projet_from);
			print '<tr><td>'.$langs->trans("Fromproject").'</td><td>';
			if ($conf->monprojet->enabled)
				print $projet->getNomUrladd(1);
			elseif($conf->projet->enabled)
				print $projet->getNomUrl(1);
			print '<input type="hidden" name="fk_projet_from" value="'.$fk_projet_from.'">';

			print '</td></tr>';
		}
		print '<tr><td>'.$langs->trans("Toproject").'</td><td>';
		if (!$fk_projet)
		{
			$filterkey = '';
			//$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);

			$numprojet = $formproject->select_projects(($user->societe_id>0?$soc->id:-1), $object->fk_projet, 'fk_projet', 16, 0, 1, 0, 0, 0, 0, $filterkey, 0, 0, '');
		}
		else
		{
			print $objectsrc->getNomUrladd(1);
			print '<input type="hidden" name="fk_projet" value="'.$fk_projet.'">';
		}
		print '</td></tr>';
	}
	if (!$fk_projet)
	{
		//property from
		if (!$fk_projet_from)
		{
			if (count($aProperty)>0)
			{
				print '<tr><td >'.$langs->trans('Propertyfrom').'</td><td colspan="2">';
				$fk_property_from = GETPOST('fk_property_from','int');
				$filter = " AND t.entity = ".$conf->entity;
				$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
				$options = '<option value="-1">'.$langs->trans('Selectproperty').'</option>';
				$lines =$objProperty->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$selected = '';
					if ($fk_property_from == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
				}
				print '<select id="fk_property_from" name="fk_property_from">'.$options.'</select>';

				//print $objpro->select__property($object->fk_property_from,'fk_property_from','',40,($user->admin?1:0),'',($user->admin?'':$aProperty));
				print '</td></tr>';
			}
		}
		//property
		print '<tr><td>o '.$langs->trans('Propertyto').'</td><td colspan="2">';
		$fk_property = GETPOST('fk_property','int');
		$filter = " AND t.entity = ".$conf->entity;
		$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
		$options = '<option value="-1">'.$langs->trans('Selectproperty').'</option>';
		$lines =$objProperty->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if ($fk_property == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
		}
		print '<select id="fk_property" name="fk_property">'.$options.'</select>';

		//print $objpro->select__property((GETPOST('fk_property')?GETPOST('fk_property'):$object->fk_property),'fk_property','',40,1,($user->admin?'':$aProperty),'');
		print '</td></tr>';

		//location
		print '<tr><td>'.$langs->trans('Location').'</td><td colspan="2">';
		$filter = " AND t.fk_property = ".$fk_property;
		$res = $objLocation->fetchAll('ASC','detail',0,0,array('status'=>1),'AND',$filter);
		$options = '';
		$lines =$objLocation->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if (GETPOST('fk_location') == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->detail.'</option>';
		}
		print '<select id="fk_location" name="fk_location">'.$options.'</select>';

		//print $objloc->select__location($object->fk_location,'fk_location','',40,1,(GETPOST('fk_property')?GETPOST('fk_property'):$object->fk_property));
		print '</td></tr>';
	}

	//buscamos al usuario
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_property = ".$object->fk_property;
	$objprouser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	foreach((array) $objprouser->lines AS $i => $line)
		$include[$line->fk_user] = $line->fk_user;

	if (GETPOST('origin'))
	{
		$exclude[$user->id] = $user->id;
		if ($action == 'createf')
		{
			//transferencia o devolucion
			//requestby
			print '<tr><td class="fieldrequired">'.$langs->trans('Userreturns').'</td><td colspan="2">';
			if (!$user->admin)
			{
				print $user->login;
				print '<input type="hidden" name="fk_user_from" value="'.$user->id.'">';
			}
			else
			{
				if ($action == 'createf') $fk_user_from = $objtmp->fk_user;
				//print $form->select_dolusers((GETPOST('fk_user_from')?GETPOST('fk_user_from'):$fk_user_from),'fk_user_from',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
				print $form->select_member((GETPOST('fk_user_from')?GETPOST('fk_user_from'):$fk_user_from), 'fk_user_from', $filter='', 1,0,0,array(),0);
			}
			print '</td></tr>';
			//solicitar a
			print '<tr><td class="fieldrequired">'.$langs->trans('Transferto').'</td><td colspan="2">';

			if ($action == 'createf') $fk_user = $objtmp->fk_user_from;
			//print $form->select_dolusers((GETPOST('fk_user_to')?GETPOST('fk_user_to'):$fk_user),'fk_user_to',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
			print $form->select_member((GETPOST('fk_user_to')?GETPOST('fk_user_to'):$fk_user), 'fk_user_to', $filter='', 1,0,0,array(),0);
			print '</td></tr>';

		}
		else
		{
			//requestby
			print '<tr><td class="fieldrequired">'.$langs->trans('Requestedby').'</td><td colspan="2">';
			print '<input type="hidden" name="fk_user_from" value="'.$user->id.'">';
			if (!$user->admin)
			{
				print $user->login;
				print '<input type="hidden" name="fk_user" value="'.$user->id.'">';

			}
			else
			{
				//print $form->select_dolusers((GETPOST('fk_user')?GETPOST('fk_user'):$fk_user),'fk_user',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
				print $form->select_member((GETPOST('fk_user')?GETPOST('fk_user'):$fk_user), 'fk_user', $filter='', 1,0,0,array(),0);
			}
			print '</td></tr>';

			//solicitar a
			print '<tr><td class="fieldrequired">'.$langs->trans('Requestedto').'</td><td colspan="2">';

			if ($action == 'createf') $fk_user_from = $objtmp->fk_user;
			//print $form->select_dolusers((GETPOST('fk_user_to')?GETPOST('fk_user_to'):$fk_user_to),'fk_user_to',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
			print $form->select_member((GETPOST('fk_user_to')?GETPOST('fk_user_to'):$fk_user_to), 'fk_user_to', $filter='', 1,0,0,array(),0);
			print '</td></tr>';
		}
	}
	else
	{
		if ($sol)
		{
			//requestby
			print '<tr><td class="fieldrequired">'.$langs->trans('Requestedby').'</td><td colspan="2">';
			if (!$user->admin)
			{
				print $user->login;
				print '<input type="hidden" name="fk_user" value="'.$user->id.'">';
				$exclude[$user->id] = $user->id;
			}
			else
			{
				if ($action == 'createf') $fk_user_from = $objtmp->fk_user;
				//print $form->select_dolusers((GETPOST('fk_user')?GETPOST('fk_user'):$fk_user_from),'fk_user',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
				print $form->select_member((GETPOST('fk_user')?GETPOST('fk_user'):$fk_user), 'fk_user', $filter='', 1,0,0,array(),0);
			}
			print '</td></tr>';
			//solicitar a
			print '<tr><td class="fieldrequired">'.$langs->trans('Requestto').'</td><td colspan="2">';

			if ($action == 'createf') $fk_user = $objtmp->fk_user_from;
			//print $form->select_dolusers((GETPOST('fk_user_to')?GETPOST('fk_user_to'):$fk_user_to),'fk_user_to',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
			print $form->select_member((GETPOST('fk_user_to')?GETPOST('fk_user_to'):$fk_user_to), 'fk_user_to', $filter='', 1,0,0,array(),0);
			print '</td></tr>';
		}
		else
		{
			//responsible
			print '<tr><td class="fieldrequired">'.$langs->trans('Responsible').'</td><td colspan="2">';
			//print $form->select_dolusers($selected,'fk_user',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
			print $form->select_member($selected, 'fk_user', $filter='', 1,0,0,array(),0);
			print '<input type="hidden" name="fk_user_from" value="'.$user->id.'">';
			print '</td></tr>';
		}
	}

	//detail
	if ($fk_projet) $detail = $langs->trans('Asignación de activos para el proyecto').' '.$objectsrc->title;
	print '<tr><td >'.$langs->trans('Detail').'</td><td colspan="2">';
	print '<input type="text" name="detail" value="'.($detail?$detail:$object->detail).'" size="60">';
	print '</td></tr>';

	//type_assignment
	if ($fk_projet) $detail = $langs->trans('Asignación de activos para el proyecto').' '.$objectsrc->title;

	print '<tr><td >'.$langs->trans('Typeassignment').'</td><td colspan="2">';
	if ($action == 'createf')
		print '<input type="radio" name="type_assignment" value="1" checked>'.$langs->trans('Transferorreturn');
	else
		print '<input type="radio" name="type_assignment" value="0" checked>'.$langs->trans('Assignment');
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	print '</form>';
}
else
{
	if ($id || $_GET['id'])
	{
	 	//dol_htmloutput_mesg($mesg);
		if (empty($id)) $id = $_GET['id'];
		//$result = $object->fetch($id);
		if ($result < 0)
		{
			dol_print_error($db);
		}

		dol_htmloutput_mesg($mesg);

		// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
			if (isset($_POST['Toaccept']) && $action == 'accept') $action = 'approved';
			if (isset($_POST['Notaccept']) && $action == 'accept') $action = 'noapproved';

			if ($object->status == 2 && ($user->rights->assets->alloc->apr || $user->rights->assets->alloc->rech))
			{
				print '<form name="fiche_assig" action="'.$_SERVER['PHP_SELF'].'" method="post">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="accept">';
				print '<input type="hidden" name="id" value="'.$object->id.'">';
				print '<input type="hidden" name="code" value="'.$object->mark.'">';
			}
			if ($action == 'applyfor')
			{
				$formquestion=array();
				$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id=' . $object->id, $langs->trans('Applyforassets'), $langs->trans('ConfirmApplyforassets'), 'confirm_applyfor', $formquestion, 0, 1, 220);
			}
			if ($action == 'transreturn')
			{
				$formquestion=array();
				$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id=' . $object->id, $langs->trans('Transfer or return'), $langs->trans('Confirm transfer or return'), 'confirm_transreturn', $formquestion, 0, 1, 220);
			}

		 	// Confirm validate third party
			if ($action == 'validate')
			{
				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Validateassetassignment"),
					$langs->trans("Confirmvalidateassetassignment"),
					"confirm_validate",
					'',
					0,
					2);
			}
			//approved
			if ($action == 'approved')
			{
				$formquestion=array();
				$array = $_POST;
				$aPost[$object->id] = $array;
				$_SESSION['aPostapproved'] = serialize($aPost);
				$qtysel = count(GETPOST('selasset'));
				$text = $qtysel.' '.$langs->trans('The').' '.GETPOST('quantassets').' '.$langs->trans('Assets');
				if ($user->rights->assets->alloc->aprlib)
				{
					$formquestion = array(array('values'=>array(0=>$langs->trans('No Liberar'),1=>$langs->trans('Liberar')),'type'=>'radio','label'=>$langs->trans('You want to release the assets once accepted').'<br>'.$langs->trans('By default it is not released'),'size'=>5,'name'=>'sel_released','value'=>0));
				}
				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Acceptassignment"),
					$langs->trans("Confirmacceptassignment").': '.$text,
					"confirm_approved",
					$formquestion,
					0,
					2);
			}
			if ($action == 'uliberate')
			{
				$_POST['selasset'][GETPOST('idr')] = 'on';
				$array = $_POST;
				$aPost[$object->id] = $array;
				$_SESSION['aPost'] = serialize($aPost);
				$qtysel = count(GETPOST('selasset'));
				$text = $qtysel.' '.$langs->trans('The').' '.GETPOST('quantassets').' '.$langs->trans('allocated');

				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Releasing assets"),
					$langs->trans("Confirm releasing assets").': '.$text,
					"confirm_free",
					'',
					0,
					2);
			}
			if ($action == 'free')
			{
				$array = $_POST;
				$aPost[$object->id] = $array;
				$_SESSION['aPost'] = serialize($aPost);
				$qtysel = count(GETPOST('selasset'));
				$text = $qtysel.' '.$langs->trans('The').' '.GETPOST('quantassets').' '.$langs->trans('allocated');

				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Releasing assets"),
					$langs->trans("Confirm releasing assets").': '.$text,
					"confirm_free",
					'',
					0,
					2);
			}

			if ($action == 'noapproved')
			{
				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Do not accept assignment"),
					$langs->trans("Confirm not accepting assignment"),
					"confirm_noapproved",
					'',
					0,
					2);
			}

		 	// Confirm delete assignment
			if ($action == 'delete')
			{
				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteprocess"),$langs->trans("Confirmdeleteprocess",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
			}

		 	// Confirm delete assignment asset
			if ($action == 'delasset')
			{
				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.$idr,$langs->trans("Deleteassetassignment"),$langs->trans("Confirmdeleteassetassignment",$object->ref),"confirm_delete_asset",'',0,2);
			}

		 	// Confirm cancel proces
			if ($action == 'anulate')
			{
				$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Cancelprocess"),$langs->trans("Confirmcancelprocess",$object->ref.' '.$object->detail),"confirm_cancel",'',0,2);

			}
			print $formconfirm;

			dol_fiche_head();

		 	//vista del registro
			print '<table class="border" style="min-width=1000px" width="100%">';

		 	//ref
			print '<tr><td width="15%">'.$langs->trans('Code').'</td><td colspan="2">';
			print $object->ref.' '.$object->mark;
			print '</td></tr>';

		 	//date assignment
			print '<tr><td width="15%">'.$langs->trans('Date assignment').'</td><td colspan="2">';
			print dol_print_date($object->date_assignment,'day');
			print '</td></tr>';

		 	//projet
			if ($conf->projet->enabled || $conf->monprojet->enabled)
			{
				if ($object->fk_projet > 0)
				{
					$projet->fetch($object->fk_projet);
					print '<tr><td width="15%">'.$langs->trans('Project').'</td><td colspan="2">';
					if ($conf->monprojet->enabled)
						print $projet->getNomUrladd(1,'',1);
					elseif ($conf->projet->enabled)
						print $projet->getNomUrl(1,'',1);
					print '</td></tr>';
				}
			}


		 	//property from
			if ($object->fk_property_from>0)
			{
				print '<tr><td width="15%">'.$langs->trans('Propertyfrom').'</td><td colspan="2">';
				if ($objProperty->fetch($object->fk_property_from)>0)
					print $objProperty->ref;
				else
					print '&nbsp;';
				print '</td></tr>';
			}
		 	//user_from
			//print '<tr><td width="15%">'.(!$object->type_assignment?$langs->trans('Requestedto'):$langs->trans('Usercreator')).'</td><td colspan="2">';
			//print '<tr><td width="15%">'.$langs->trans('Usercreator').'</td><td colspan="2">';
			//if ($objuser->fetch($object->fk_user_from)>0)
			//	print $objuser->getNomUrl(1);
			//else
			//	print '&nbsp;';
			//	print '</td></tr>';

			if ($object->fk_property > 0)
			{
			 	//property
				print '<tr><td width="15%">'.$langs->trans('Propertyto').'</td><td colspan="2">';
				if ($objProperty->fetch($object->fk_property)>0)
					print $objProperty->ref;
				else
					print '&nbsp;';
				print '</td></tr>';
			 	//location
				print '<tr><td width="15%">'.$langs->trans('Location').'</td><td colspan="2">';
				if ($objLocation->fetch($object->fk_location)>0)
					print $objLocation->detail;
				else
					print '&nbsp;';
				print '</td></tr>';
			}
		 	//user
			//print '<tr><td width="15%">'.(!$object->type_assignment?$langs->trans('Requestedby'):$langs->trans('Assignedto')).'</td><td colspan="2">';
			print '<tr><td width="15%">'.$langs->trans('Assignedto').'</td><td colspan="2">';
			if ($objadh->fetch($object->fk_user)>0)
				print $objadh->getNomUrl(1).' '.$objadh->lastname.' '.$objadh->firstname;
			else
				print '&nbsp;';
			print '</td></tr>';
			if ($object->fk_user_to >0)
			{
				print '<tr><td width="15%">'.$langs->trans('Requestedto').'</td><td colspan="2">';
				if ($objadh->fetch($object->fk_user_to)>0)
					print $objadh->getNomUrl(1).' '.$objadh->lastname.' '.$objadh->firstname;
				else
					print '&nbsp;';
				print '</td></tr>';
			}
		 	//detail
			print '<tr><td width="15%" >'.$langs->trans('Detail').'</td><td colspan="2">';
			print $object->detail;
			print '</td></tr>';

		 	//status
			print '<tr><td width="15%" >'.$langs->trans('Status').'</td><td colspan="2">';
			print $object->getLibStatut(2);
			print '</td></tr>';

			print '</table>';

		 	//activos disponibles para asignacion
			$aInclude = array();
			$aExclude = array();

			$objassigd = new Assetsassignmentdetext($db);
			if ($object->status>0)
				$objassigd->getlistassignment($id,'',"1,2");
			else
			{
				$objassigd->getlistassetsassignment((!empty($object->fk_property_from)?$object->fk_property_from:''),1);
				if ($object->fk_property_from > 0)
				{
					if (count($objassigd->array) > 0)
					{
						foreach ((array) $objassigd->array AS $i => $objn)
						{
							$aInclude[$objn->fk_asset] = $objn->fk_asset;
						}
					}
				}
				if ($object->fk_property_from <=0)
				{
					if (count($objassigd->array) > 0)
					{
						foreach ((array) $objassigd->array AS $i => $objn)
						{
							$aExclude[$objn->fk_asset] = $objn->fk_asset;
						}
					}
				}
			}
			//activos asignados
			$objassigdact = new Assetsassignmentdetext($db);
			$objassigdact->getlistassignmentact($id,(!empty($object->fk_property)?$object->fk_property:''),0);
			if (count($objassigdact->array) > 0)
			{
				foreach ((array) $objassigdact->array AS $i => $objn)
					$aExclude[$objn->fk_asset] = $objn->fk_asset;
			}
			$userWriter = false;
			if ($user->admin || $user->id == $object->fk_user_from || ($user->id == $object->fk_user && $object->origin))
				$userWriter = true;
			if ($object->type_assignment && $object->status == 2)
			{
				$userWriter = false;
				if ($user->admin || $user->id == $object->fk_user)
					$userWriter = true;
			}
			$lLibre = false;
			include_once DOL_DOCUMENT_ROOT."/assets/assignment/tpl/assetassignment.tpl.php";

			dol_fiche_end();


			/* ************************************** */
			/*                                        */
			/* Barre d'action                         */
			/*                                        */
			/* ************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				//if ($user->rights->assets->alloc->crear)
				//	print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
				//else
				//	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
				//if (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->mod && $object->status == 1) || ($user->rights->assets->alloc->mod && $object->status == 0 && $user->id == $object->fk_user_from))

				if ($user->rights->assets->alloc->mod && $object->status == 0 && $user->id == $object->fk_user_from)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				//if (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->mod && $object->status == 1) || ($user->rights->assets->alloc->del && $object->status == 0 && $user->id == $object->fk_user_from))
				if ($user->rights->assets->alloc->del && $object->status == 0 && $user->id == $object->fk_user_from)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";

				if ($object->type_assignment)
				{
					if ($object->status == 0 && (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->trans && count($objassigdact->array) > 0)  || ($user->rights->assets->alloc->trans && $user->id == $object->fk_user_from && $object->origin && count($objassigdact->array) > 0)))
						print "<a class=\"butAction\" href=\"fiche.php?action=transreturn&id=".$object->id."\">".$langs->trans("Transfer or return")."</a>";
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Transfer or return")."</a>";
				}
				else
				{
					if ($object->status == 0 && (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->sol && count($objassigdact->array) > 0)  || ($user->rights->assets->alloc->sol && $user->id == $object->fk_user_from && count($objassigdact->array) > 0)))
					{
						if ($object->fk_user_to && $object->fk_user_from)
							print "<a class=\"butAction\" href=\"fiche.php?action=applyfor&id=".$object->id."\">".$langs->trans("Applyfor")."</a>";
						else
							print "<a class=\"butAction\" href=\"fiche.php?action=applyfor&id=".$object->id."\">".$langs->trans("Allocate")."</a>";

					}
					else
					{
						if ($object->fk_user_to && $object->fk_user_from)
							print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Applyfor")."</a>";
						else
							print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Allocate")."</a>";
					}
				}
				if ($object->status == 1 && (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->val && count($objassigdact->array) > 0)  || ($user->rights->assets->alloc->val && $user->id == $object->fk_user_from && count($objassigdact->array) > 0)))
					print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";

				if ($object->status == 3 && (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->lib && count($objassigdact->array) > 0)  || ($user->rights->assets->alloc->lib && $user->id == $object->fk_user && count($objassigdact->array) > 0)))
					if ($lLibre)
						print "<a class=\"butAction\" href=\"fiche.php?action=liberate&id=".$object->id."\">".$langs->trans("Leavefree")."</a>";
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Leavefree")."</a>";

					if ($object->status == 3 && (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->trans && count($objassigdact->array) > 0)  || ($user->rights->assets->alloc->trans && $user->id == $object->fk_user && count($objassigdact->array) > 0)))
						print "<a class=\"butAction\" href=\"fiche.php?aid=".$object->id."&action=createf&origin=assignment&originid=".$object->id."\">".$langs->trans("Totransfer")."</a>";
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Totransfer")."</a>";

					//if ($object->status == 2 && (($user->rights->assets->alloc->lall && $user->rights->assets->alloc->apr) || ($user->rights->assets->alloc->apr && $user->id == $object->fk_user )))
					//{
					//print "<a class=\"butAction\" href=\"fiche.php?action=approved&id=".$object->id."\">".$langs->trans("Toaccept")."</a>";
					//print "<a class=\"butActionDelete\" href=\"fiche.php?action=noapproved&id=".$object->id."\">".$langs->trans("Notaccept")."</a>";
					//}
					//else
					//	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";

				}
				if ($object->status == 2 && ($user->rights->assets->alloc->apr || $user->rights->assets->alloc->rech))
				{
					$lApp = false;
					if ($user->admin || ($user->rights->assets->alloc->lall && !$object->type_assignment) || ($object->fk_user == $user->id))
						$lApp = true;
					if ($lApp)
					{
						print '<center>';
						if ($user->rights->assets->alloc->apr)
							print '<input type="submit" class="butAction" name="Toaccept" value="'.$langs->trans("Toaccept").'">';
						print '&nbsp;';
						if ($user->rights->assets->alloc->rech)
							print '<input type="submit" class="butActionDelete" name="Notaccept" value="'.$langs->trans("Notaccept").'">';
						print '</center>';
					}
				}
			}
			if ($object->status == 2 && ($user->rights->assets->alloc->apr || $user->rights->assets->alloc->rech))
				print '</form>';

			print '</div>';

			//DOCUMENTS
			print "<div class=\"tabsAction\">\n";
		  	//documents
			if ($object->status>=1 && $action!='deliver')
			{
				print '<table width="100%"><tr><td width="50%" valign="top">';
				print '<a name="builddoc"></a>';
		  		// ancre
		   		// Documents generes
				$filename=dol_sanitizeFileName($object->ref);
				$filedir=$conf->assets->dir_output . '/' . dol_sanitizeFileName($object->ref);
				$urlsource=$_SERVER['PHP_SELF'].'?id='.$object->id;
				$genallowed=$user->rights->assets->rep->creaassign;
				$delallowed=$user->rights->assets->rep->delassign;
				$object->modelpdf = 'fractalassignment';
				print '<br>';
				print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
				$somethingshown=$formfile->numoffiles;
				print '</td></tr></table>';
			}
			print "</div>";

		}
			// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{

			print_fiche_titre($langs->trans("Editasset"));

			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				$("#fk_property").change(function() {
					document.fiche_assig.action.value="createedit";
					document.fiche_assig.submit();
				});

			});';
			print '</script>'."\n";

			print '<form name="fiche_assig" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" style="min-width=1000px" width="100%">';

		 //ref
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Code').'</td><td colspan="2">';
			print '<input type="text" name="ref" value="'.(empty($object->ref)?'(PROV)':$object->ref).'" size="30">';
			print '</td></tr>';

		 //date assignment
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Date assignment').'</td><td colspan="2">';
			$form->select_date($object->date_assignment,'da_','','','',"date",1,1);
			print '</td></tr>';

		 //adherent
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Responsible').'</td><td colspan="2">';
			if ($user->admin)
				//print $form->select_dolusers($object->fk_user,'fk_user',1,$exclude,0,$include,'',0,0,0,'',0,'','',1 );
				print $form->select_member($object->fk_user, 'fk_user', $filter='', 1,0,0,array(),0);
			else
			{
				print $user->login;
				print '<input type="hidden" name="fk_user" value="'.$user->id.'">';
			}
			print '</td></tr>';

			//projet
			if ($conf->projet->enabled)
			{
				print '<tr><td>'.$langs->trans("Project").'</td><td>';
				$filterkey = '';
				$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
				print '</td></tr>';
			}

		 //property
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
			print $objProperty->select_property($object->fk_property,'fk_property','',40,1);
			print '</td></tr>';

		 //location
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
			print $objLocation->select_location($object->fk_location,'fk_location','',40,1,$object->fk_property);
			print '</td></tr>';

		 //detail
			print '<tr><td width="15%" >'.$langs->trans('Detail').'</td><td colspan="2">';
			print '<input type="text" name="detail" value="'.$object->detail.'" size="60">';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">';
			print '&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">'.'</center>';
			print '</form>';

		}
	}
	llxFooter();

	$db->close();
	?>
