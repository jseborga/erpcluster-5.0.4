<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       mant/mequipment_card.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-07 18:07
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/mant/class/mequipmentext.class.php');
dol_include_once('/mant/class/mequipmentmant.class.php');
dol_include_once('/mant/class/mgroups.class.php');
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

if ($conf->assets->enabled)
{
	dol_include_once('/assets/class/assetsext.class.php');
	dol_include_once('/assets/class/cassetsgroup.class.php');
}

// Load traductions files requiredby by page
$langs->load("mant");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$fk_asset = GETPOST('fk_asset','int');

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_label=GETPOST('search_label','alpha');
$search_metered=GETPOST('search_metered','int');
$search_accountant=GETPOST('search_accountant','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_margin=GETPOST('search_margin','int');
$search_trademark=GETPOST('search_trademark','alpha');
$search_model=GETPOST('search_model','alpha');
$search_anio=GETPOST('search_anio','alpha');
$search_fk_location=GETPOST('search_fk_location','int');
$search_fk_asset=GETPOST('search_fk_asset','int');
$search_hour_cost=GETPOST('search_hour_cost','alpha');
$search_code_program=GETPOST('search_code_program','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');
$search_status=GETPOST('search_status','int');


if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'mant', $id);


$object = new Mequipmentext($db);
$extrafields = new ExtraFields($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objGroup = new Mgroups($db);
$objMant = new Mequipmentmant($db);
if($conf->assets->enabled)
{
	$objAsset = new Assets($db);
	$objCassetgroup = new Cassetsgroup($db);
}

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('mequipment'));
//verificamos el parametro global
$lIntegratedasset = false;
if ($conf->global->MANT_EQUIPMENT_INTEGRATED_WITH_ASSET)
	$lIntegratedasset = true;

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/equipment/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/equipment/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		if ($lIntegratedasset && empty($fk_asset))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Asset")), null, 'errors');
		}
		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->label=GETPOST('label','alpha');
		$object->metered=GETPOST('metered','int');
		$object->accountant=GETPOST('accountant','int');
		if(empty($object->accountant)) $object->accountant = 0;
		$object->accountant_last=GETPOST('accountant_last','int');
		if(empty($object->accountant_last)) $object->accountant_last = 0;
		$object->accountant_mant=GETPOST('accountant_mant','int');
		if(empty($object->accountant_mant)) $object->accountant_mant = 0;
		$object->accountant_mante=GETPOST('accountant_mante','int');
		if(empty($object->accountant_mante)) $object->accountant_mante = 0;
		$object->fk_unit=GETPOST('fk_unit','int');
		if(empty($object->fk_unit)) $object->fk_unit = 0;
		$object->margin=GETPOST('margin','int');
		if(empty($object->margin)) $object->margin = 0;
		$object->trademark=GETPOST('trademark','alpha');
		$object->model=GETPOST('model','alpha');
		$object->anio=GETPOST('anio','alpha');
		if(empty($object->anio)) $object->anio = 0;
		$object->fk_location=GETPOST('fk_location','int');
		if(empty($object->fk_location)) $object->fk_location = 0;
		$object->fk_asset=GETPOST('fk_asset','int');
		if(empty($object->fk_asset)) $object->fk_asset = 0;
		$object->fk_group=GETPOST('fk_group','int');
		if(empty($object->fk_group)) $object->fk_group = 0;
		$object->hour_cost=GETPOST('hour_cost','int');
		if(empty($object->hour_cost)) $object->hour_cost = 0;
		$object->fk_equipment_program=GETPOST('fk_equipment_program','int');
		if(empty($object->fk_equipment_program)) $object->fk_equipment_program = 0;
		$object->code_program=GETPOST('code_program','alpha');
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->active=1;
		$object->status=1;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		$db->begin();
		if (! $error)
		{
			$result=$object->create($user);
			if ($result <=0)
			{
				// Creation KO
				$error++;
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
			}
		}
		if (!$error)
		{
			if($object->accountant_mant>=0)
			{
				$objMant->fk_equipment = $result;
				$objMant->fk_jobs = 0;
				$objMant->type = 'C';
				$objMant->accountant = $object->accountant_mant;
				$objMant->dater = dol_now();
				$objMant->fk_user_create = $user->id;
				$objMant->fk_user_mod = $user->id;
				$objMant->datec = dol_now();
				$objMant->datem = dol_now();
				$objMant->tms = dol_now();
				$objMant->status = 1;
				$res = $objMant->create($user);
				if ($res <=0)
				{
					$error++;
					if (! empty($objMant->errors)) setEventMessages(null, $objMant->errors, 'errors');
					else  setEventMessages($objMant->error, null, 'errors');
				}
			}
		}
		if (!$error)
		{
			if($object->accountant_mante>=0)
			{
				$objMant->fk_equipment = $result;
				$objMant->fk_jobs = 0;
				$objMant->type = 'E';
				$objMant->accountant = $object->accountant_mante;
				$objMant->dater = dol_now();
				$objMant->fk_user_create = $user->id;
				$objMant->fk_user_mod = $user->id;
				$objMant->datec = dol_now();
				$objMant->datem = dol_now();
				$objMant->tms = dol_now();
				$objMant->status = 1;
				$res = $objMant->create($user);
				if ($res <=0)
				{
					$error++;
					if (! empty($objMant->errors)) setEventMessages(null, $objMant->errors, 'errors');
					else  setEventMessages($objMant->error, null, 'errors');
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/equipment/card.php?id='.$result,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;

		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->label=GETPOST('label','alpha');
		$object->metered=GETPOST('metered','int');
		$object->accountant=GETPOST('accountant','int');
		if (empty($object->accountant))$object->accountant=0;
		$object->accountant_last=GETPOST('accountant_last','int');
		if (empty($object->accountant_last))$object->accountant_last=0;
		$object->accountant_mant=GETPOST('accountant_mant','int');
		if (empty($object->accountant_mant))$object->accountant_mant=0;
		$object->accountant_mante=GETPOST('accountant_mante','int');
		if (empty($object->accountant_mante))$object->accountant_mante=0;
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->margin=GETPOST('margin','int');
		$object->trademark=GETPOST('trademark','alpha');
		$object->model=GETPOST('model','alpha');
		$object->anio=GETPOST('anio','int');
		if (empty($object->anio))$object->anio=0;
		$object->fk_location=GETPOST('fk_location','int');
		if (empty($object->fk_location))$object->fk_location=0;
		$object->fk_asset=GETPOST('fk_asset','int');
		if (empty($object->fk_asset))$object->fk_asset=0;
		$object->fk_group=GETPOST('fk_group','int');
		if (empty($object->fk_group))$object->fk_group=0;
		$object->hour_cost=GETPOST('hour_cost','int');
		if (empty($object->hour_cost))$object->hour_cost=0;
		$object->code_program=GETPOST('code_program','alpha');
		$object->fk_user_mod=$user->id;
		$object->datem = dol_now();
		$object->active=GETPOST('active','int');


		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}
		if ($object->metered)
		{
			if ($object->accountant<0)
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldaccountant")), null, 'errors');
			}
			if ($object->fk_unit <=0)
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_unit")), null, 'errors');
			}
			if ($object->margin <0)
			{
				$error++;
				setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldmargin")), null, 'errors');
			}

		}
		$db->begin();
		if (! $error)
		{
			$result=$object->update($user);
			if ($result <= 0)
			{
				// Creation KO
				$error++;
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
			}
		}
		if (!$error)
		{
			if($object->accountant_mant>=0)
			{
				$filter = " AND t.fk_equipment = ".$object->id;
				$filter.= " AND t.fk_jobs >= 0";
				$res = $objMant->fetchAll('','',0,0,array('status'=>1,'type'=>'C'),'AND',$filter,true);
				if (empty($res))
				{
					$objMant->fk_equipment = $object->id;
					$objMant->fk_jobs = 0;
					$objMant->type = 'C';
					$objMant->accountant = $object->accountant_mant;
					$objMant->dater = dol_now();
					$objMant->fk_user_create = $user->id;
					$objMant->fk_user_mod = $user->id;
					$objMant->datec = dol_now();
					$objMant->datem = dol_now();
					$objMant->tms = dol_now();
					$objMant->status = 1;
					$res = $objMant->create($user);
					if ($res <=0)
					{
						$error++;
						if (! empty($objMant->errors)) setEventMessages(null, $objMant->errors, 'errors');
						else  setEventMessages($objMant->error, null, 'errors');
					}
				}
				else
				{
					if ($res == 1 && empty($objMant->fk_jobs))
					{
						$objMant->accountant = $object->accountant_mant;
						$objMant->fk_user_mod = $user->id;
						$objMant->datem = dol_now();
						$objMant->tms = dol_now();
						$objMant->status = 1;
						$res = $objMant->update($user);
						if ($res <=0)
						{
							$error++;
							if (! empty($objMant->errors)) setEventMessages(null, $objMant->errors, 'errors');
							else  setEventMessages($objMant->error, null, 'errors');
						}
					}
				}
			}
		}
		if (!$error)
		{
			if($object->accountant_mante>=0)
			{
				$filter = " AND t.fk_equipment = ".$object->id;
				$filter.= " AND t.fk_jobs >= 0";
				$res = $objMant->fetchAll('','',0,0,array('status'=>1,'type'=>'E'),'AND',$filter,true);
				if (empty($res))
				{
					$objMant->initAsSpecimen();
					$objMant->fk_equipment = $object->id;
					$objMant->fk_jobs = 0;
					$objMant->type = 'E';
					$objMant->accountant = $object->accountant_mante;
					$objMant->dater = dol_now();
					$objMant->fk_user_create = $user->id;
					$objMant->fk_user_mod = $user->id;
					$objMant->datec = dol_now();
					$objMant->datem = dol_now();
					$objMant->tms = dol_now();
					$objMant->status = 1;
					$res = $objMant->create($user);
					if ($res <=0)
					{
						$error++;
						if (! empty($objMant->errors)) setEventMessages(null, $objMant->errors, 'errors');
						else  setEventMessages($objMant->error, null, 'errors');
					}
				}
				else
				{
					if ($res == 1 && empty($objMant->fk_jobs))
					{
						$objMant->accountant = $object->accountant_mante;
						$objMant->fk_user_mod = $user->id;
						$objMant->datem = dol_now();
						$objMant->tms = dol_now();
						$objMant->status = 1;
						$res = $objMant->update($user);
						if ($res <=0)
						{
							$error++;
							if (! empty($objMant->errors)) setEventMessages(null, $objMant->errors, 'errors');
							else  setEventMessages($objMant->error, null, 'errors');
						}
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			$action = '';
		}
		else
		{
			$db->rollback();
			$action = 'edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/mant/equipment/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Teams'),'');

$form=new Formv($db);

$fk_asset = GETPOST('fk_asset');

if ($fk_asset>0)
{
	$res = $objAsset->fetch($fk_asset);
	if ($res == 1)
	{
		$ref_asset = $objAsset->ref;
		$_POST['ref'] = $objAsset->ref;
		$_POST['ref_ext'] = $objAsset->ref_ext;
		$_POST['label'] = $objAsset->descrip;

		$resg = $objCassetgroup->fetch(0,$objAsset->type_group);
		$_POST['fk_group'] = $objCassetgroup->id;

		$_POST['trademark'] = $objAsset->trademark;
		$_POST['model'] = $objAsset->model;
		$_POST['anio'] = $objAsset->anio;
		$_POST['fk_property'] = $objAsset->fk_property;
		$_POST['fk_location'] = $objAsset->fk_location;
	}
	elseif(empty($res))
	{
		$error++;
		setEventMessages($langs->trans('No existe activo'),null,'errors');
	}
	if ($action == 'create')
	{
		//buscamos si esta ya registrado
		$res = $object->fetch(0,null,$fk_asset);
		if ($res >0)
		{
			$error++;
			setEventMessages($langs->trans('Ya se encuentra registrado'),null,'errors');
		}
	}
}

$res = $objGroup->fetchAll('ASC','label',0,0,array('active'=>1),'AND');
$optionsgroup = '<option value="0">'.$langs->trans('Selectgroup').'</option>';
if ($res >0)
{
	foreach ($objGroup->lines AS $j => $line)
	{
		$selected = '';
		if (GETPOST('fk_group')) $selected= ' selected';
		$optionsgroup.='<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
	}
}
// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Newequipment"));

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#fk_property").change(function() {
			document.form.action.value="create";
			document.form.submit();
		});
		$("#metered").change(function() {
			document.form.action.value="create";
			document.form.submit();
		});
		$("#fk_asset").change(function() {
			document.form.action.value="create";
			document.form.submit();
		});
	});';
	print '</script>'."\n";


	print '<form name="form" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	if ($lIntegratedasset)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Asset").'</td><td>';
		print $form->select_asset($fk_asset, 'fk_asset', '', 20, 0, 1, 2, '', 1, array(),0,'','',0);
		print '</td></tr>';
	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'" required></td></tr>';
	print '<tr><td >'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.GETPOST('ref_ext').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup").'</td><td>';
	print '<select name="fk_group">'.$optionsgroup.'</select>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmetered").'</td><td>';
	print $form->selectyesno('metered',GETPOST('metered'),1);
	print '</td></tr>';
	if (GETPOST('metered'))
	{
		print '<tr><td class="'.(GETPOST('metered')?'fieldrequired':'').'">'.$langs->trans("Fieldaccountant").'</td><td><input class="flat" type="text" name="accountant" value="'.GETPOST('accountant').'" '.(GETPOST('metered')?'required':'').'></td></tr>';
		print '<tr><td class="'.(GETPOST('metered')?'fieldrequired':'').'">'.$langs->trans("Fieldfk_unit").'</td><td>';
		print $form->selectUnits(GETPOST('fk_unit'),'fk_unit',1);
		print '</td></tr>';
		print '<tr><td class="'.(GETPOST('metered')?'fieldrequired':'').'">'.$langs->trans("Fieldmargin").'</td><td><input class="flat" type="text" name="margin" value="'.GETPOST('margin').'" '.(GETPOST('metered')?'required':'').'></td></tr>';
		print '<tr><td>'.$langs->trans("Last continuous maintenance accountant").'</td><td><input class="flat" type="text" name="accountant_mant" value="'.GETPOST('accountant_mant').'"></td></tr>';
		print '<tr><td>'.$langs->trans("Last fixed maintenance accountant").'</td><td><input class="flat" type="text" name="accountant_mante" value="'.GETPOST('accountant_mante').'"></td></tr>';
	}

	print '<tr><td>'.$langs->trans("Fieldtrademark").'</td><td><input class="flat" type="text" name="trademark" value="'.GETPOST('trademark').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmodel").'</td><td><input class="flat" type="text" name="model" value="'.GETPOST('model').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldanio").'</td><td><input class="flat" type="text" name="anio" value="'.GETPOST('anio').'"></td></tr>';

	// property
	$fk_property = GETPOST('fk_property');
	print '<tr><td >'.$langs->trans('Property').'</td><td colspan="2">';
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
		 //print $objProperty->select_property($fk_property,'fk_property','',40,1);
	print '</td></tr>';

	// location
	print '<tr><td >'.$langs->trans('Location').'</td><td colspan="2">';
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
	//print $objLocation->select_location(GETPOST('fk_location'),'fk_location','',40,1,GETPOST('fk_property'));
	print '</td></tr>';


	print '<tr><td>'.$langs->trans("Fieldhour_cost").'</td><td><input class="flat" type="text" name="hour_cost" value="'.GETPOST('hour_cost').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcode_program").'</td><td><input class="flat" type="text" name="code_program" value="'.GETPOST('code_program').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();
	print '<div class="center">';
	if (!$error)
		print '<input type="submit" class="button" name="add" value="'.$langs->trans("Create").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	$optionsgroup = '<option value="0">'.$langs->trans('Selectgroup').'</option>';
	foreach ((array) $objGroup->lines AS $j => $line)
	{
		$selected = '';
		if ($line->id == $object->fk_group) $selected = ' selected';
		$optionsgroup.='<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
	}

	print load_fiche_titre($langs->trans("Mant"));

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#fk_property").change(function() {
			document.form.action.value="edit";
			document.form.submit();
		});
		$("#metered").change(function() {
			document.form.action.value="edit";
			document.form.submit();
		});
	});';
	print '</script>'."\n";

	print '<form name="form" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$object->ref).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.(GETPOST('ref_ext')?GETPOST('ref_ext'):$object->ref_ext).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.(GETPOST('label')?GETPOST('label'):$object->label).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup").'</td><td>';
	print '<select name="fk_group">'.$optionsgroup.'</select>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmetered").'</td><td>';
	if (isset($_POST['metered']))
		$metered = GETPOST('metered');
	else
		$metered = $object->metered;
	print $form->selectyesno('metered',$metered,1);
	print '</td></tr>';
	//echo $metered = GETPOST('metered')?GETPOST('metered'):$object->metered;
	$lMetered = false;
	if($metered) $lMetered = true;
	if($lMetered)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccountant").'</td><td><input class="flat" type="text" name="accountant" value="'.(GETPOST('accountant')?GETPOST('accountant'):$object->accountant).'" required></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>';
		print $form->selectUnits((GETPOST('fk_unit')?GETPOST('fk_unit'):$object->fk_unit),'fk_unit',1);
		print '</td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmargin").'</td><td><input class="flat" type="text" name="margin" value="'.(GETPOST('margin')?GETPOST('margin'):$object->margin).'" required></td></tr>';
		print '<tr><td>'.$langs->trans("Last continuous maintenance accountant").'</td><td><input class="flat" type="text" name="accountant_mant" value="'.(GETPOST('accountant_mant')?GETPOST('accountant_mant'):$object->accountant_mant).'"></td></tr>';
		print '<tr><td>'.$langs->trans("Last fixed maintenance accountant").'</td><td><input class="flat" type="text" name="accountant_mante" value="'.(GETPOST('accountant_mante')?GETPOST('accountant_mante'):$object->accountant_mante).'"></td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldtrademark").'</td><td><input class="flat" type="text" name="trademark" value="'.(GETPOST('trademark')?GETPOST('trademark'):$object->trademark).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmodel").'</td><td><input class="flat" type="text" name="model" value="'.(GETPOST('model')?GETPOST('model'):$object->model).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldanio").'</td><td><input class="flat" type="text" name="anio" value="'.(GETPOST('anio')?GETPOST('anio'):$object->anio).'"></td></tr>';

	$res = $objLocation->fetch((GETPOST('fk_location')?GETPOST('fk_location'):$object->fk_location));
	if ($res > 0) $fk_property = $objLocation->fk_property;

	// property
	$fk_property = GETPOST('fk_property')?GETPOST('fk_property'):$fk_property;
	print '<tr><td>'.$langs->trans('Property').'</td><td colspan="2">';
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
	print '</td></tr>';

	// location
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
	//print $objLocation->select_location(GETPOST('fk_location'),'fk_location','',40,1,GETPOST('fk_property'));
	print '</td></tr>';

	if ($conf->assets->enabled)
	{
		print '<tr><td>'.$langs->trans("Fieldfk_asset").'</td><td>';
		print $form->select_asset($object->fk_asset,'fk_asset','',0,0,1,2,'',1,array(),0,'','',0);
		print '</td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldhour_cost").'</td><td><input class="flat" type="text" name="hour_cost" value="'.GETPOST('hour_cost')?GETPOST('hour_cost'):$object->hour_cost.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_equipment_program").'</td><td><input class="flat" type="text" name="fk_equipment_program" value="'.$object->fk_equipment_program.'"></td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',GETPOST('active')?GETPOST('active'):$object->active,1);
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals($object->id, $extralabels);

	$head = equipment_prepare_head($object);
	dol_fiche_head($head, 'card', $langs->trans("Equipment"), 0, 'equipment');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Deleteequipment'), $langs->trans('ConfirmDeleteequipment'), 'confirm_delete', '', 1, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	print '<tr><td width="20%">'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldgroup").'</td><td>';
	$objGroup->fetch($object->fk_group);
	if ($objGroup->id == $object->fk_group)
		print $objGroup->getNomUrl().' '.$objGroup->label;
	else
		print '';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmetered").'</td><td>'.($object->metered?$langs->trans('Yes'):$langs->trans('No')).'</td></tr>';
	if ($object->metered)
	{
		$objecttmp = new MequipmentLine($db);
		$objecttmp->fk_unit = $object->fk_unit;
		$unit = $langs->trans($objecttmp->getLabelOfUnit());
		//buscamos registro del ultimo contador reportado
		$filter = " AND t.fk_equipment = ".$object->id;
		$filter.= " AND t.fk_jobs = 0";

		print '<tr><td>'.$langs->trans("Fieldaccountant").'</td><td>'.$object->accountant .' '.$unit.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldmargin").'</td><td>'.$object->margin.' '.$unit.'</td></tr>';
		print '<tr><td>'.$langs->trans("Ultimo contador reportado").'</td><td>'.$object->accountant_last.'</td></tr>';
		$res = $objMant->fetchAll('','',0,0,array('status'=>1,'type'=>'C'),'AND',$filter,true);
		if ($res==1)
			print '<tr><td>'.$langs->trans("Last continuous maintenance accountant").'</td><td>'.$objMant->accountant.'</td></tr>';
		else
			print '<tr><td>'.$langs->trans("Last continuous maintenance accountant").'</td><td>'.$object->accountant_mant.'</td></tr>';
		//para E
		$res = $objMant->fetchAll('','',0,0,array('status'=>1,'type'=>'E'),'AND',$fiter,true);
		if ($res==1)
			print '<tr><td>'.$langs->trans("Last fixed maintenance accountant").'</td><td>'.$objMant->accountant.'</td></tr>';
		else
			print '<tr><td>'.$langs->trans("Last fixed maintenance accountant").'</td><td>'.$object->accountant_mante.'</td></tr>';
	}
	if(!empty($object->trademark))
		print '<tr><td>'.$langs->trans("Fieldtrademark").'</td><td>'.$object->trademark.'</td></tr>';
	if(!empty($object->model))
		print '<tr><td>'.$langs->trans("Fieldmodel").'</td><td>'.$object->model.'</td></tr>';
	if(!empty($object->anio))
		print '<tr><td>'.$langs->trans("Fieldanio").'</td><td>'.$object->anio.'</td></tr>';
	if($object->fk_location>0)
	{
		$res = $objLocation->fetch($object->fk_location);
		if ($res > 0) $fk_property = $objLocation->fk_property;

		print '<tr><td>'.$langs->trans("Fieldfk_location").'</td><td>'.$objLocation->detail.'</td></tr>';
	}
	if($object->fk_asset>0)
		print '<tr><td>'.$langs->trans("Fieldfk_asset").'</td><td>'.$object->fk_asset.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldhour_cost").'</td><td>'.$object->hour_cost.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcode_program").'</td><td>'.$object->code_program.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->mant->equ->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->mant->equ->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";

	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('mequipment'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
