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
dol_include_once('/mant/class/mequipment.class.php');
dol_include_once('/mant/class/mequipmenthistorial.class.php');
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/lib/orgman.lib.php';
if ($conf->assets->enabled)
	dol_include_once('/assets/class/assetsext.class.php');

// Load traductions files requiredby by page
$langs->load("mant");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


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


$object = new Mequipment($db);
$objecth = new Mequipmenthistorial($db);
$extrafields = new ExtraFields($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array

$hookmanager->initHooks(array('mequipmenthistorial'));

if ($id>0)
	$object->fetch($id);
else
{
	setEventMessages($langs->trans('Error, registro no valido'),null,'errors');
	exit;
}
if ($idr>0)
{
	$objecth->fetch($idr);
}
/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objecth,$action);
// Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/equipment/history.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objecth->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/equipment/history.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objecth->fk_equipment=$object->id;
		$objecth->ref_ext=GETPOST('ref_ext','alpha');
		$objecth->accountant=GETPOST('accountant');
		$objecth->accountant_last=GETPOST('accountant_last');
		if (empty($objecth->accountant_last)) $objecth->accountant_last = 0;
		$objecth->description=GETPOST('description','alpha');
		$objecth->pc_ip=getRealIP();
		$objecth->origin=GETPOST('origin','alpha');
		$objecth->originid=GETPOST('originid','int')+0;
		$objecth->fk_user_create=$user->id;
		$objecth->fk_user_mod=$user->id;
		$objecth->status=1;
		$objecth->datec = dol_now();
		$objecth->datem = dol_now();
		$objecth->tms = dol_now();

		if (empty($objecth->ref_ext))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldref_ext")), null, 'errors');
		}
		if ($objecth->accountant <= $objecth->accountant_last)
		{
			$error++;
			setEventMessages($langs->trans("The current counter can not be less than or equal to the previous counter"), null, 'errors');
		}

		if (! $error)
		{
			$db->begin();
			$result=$objecth->create($user);
			if ($result > 0)
			{
				//actualizamos en object
				$object->accountant_last = $objecth->accountant;
				$res = $object->update($user);
				if ($res <=0)
				{
					// Creation KO
					if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
					else  setEventMessages($object->error, null, 'errors');
					$action='create';
				}
			}
			else
			{
				// Creation KO
				if (! empty($objecth->errors)) setEventMessages(null, $objecth->errors, 'errors');
				else  setEventMessages($objecth->error, null, 'errors');
				$action='create';
			}

			if (!$error)
			{
				$db->commit();
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/equipment/history.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
				$db->rollback();
				$action = 'create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		//$objecth->fk_equipment=GETPOST('fk_equipment','int');
		$objecth->ref_ext=GETPOST('ref_ext','alpha');
		$objecth->accountant=GETPOST('accountant','alpha');
		$objecth->accountant_last=GETPOST('accountant_last','alpha');
		$objecth->description=GETPOST('description','alpha');
		//$objecth->pc_ip=GETPOST('pc_ip','alpha');
		$objecth->origin=GETPOST('origin','alpha');
		$objecth->originid=GETPOST('originid','int');
		$objecth->fk_user_mod=$user->id;
		$objecth->datem=dol_now();
		$objecth->tms=dol_now();


		if (empty($objecth->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objecth->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objecth->errors)) setEventMessages(null, $objecth->errors, 'errors');
				else setEventMessages($objecth->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objecth->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/mant/equipment/history.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objecth->errors)) setEventMessages(null, $objecth->errors, 'errors');
			else setEventMessages($objecth->error, null, 'errors');
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

// Part to show record
if ($object->id > 0)
{
	$res = $object->fetch_optionals($object->id, $extralabels);

	$objectline = new MequipmentLine($db);

	$objectline->fk_unit = $object->fk_unit;

	$head = equipment_prepare_head($object);
	dol_fiche_head($head, 'log', $langs->trans("Equipment"), 0, 'equipment');

	print load_fiche_titre($langs->trans("History"));

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	print '<tr><td width="20%">'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmetered").'</td><td>'.($object->metered?$langs->trans('Yes'):$langs->trans('No')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Last account reported").'</td><td>'.$object->accountant_last.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>';
	print $langs->trans($objectline->getLabelOfUnit());
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcode_program").'</td><td>'.$object->code_program.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>'.$object->active.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>'.$object->status.'</td></tr>';

	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->mant->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->mant->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";

	if ($object->metered)
	{
		include DOL_DOCUMENT_ROOT.'/mant/equipment/tpl/equipmenthistory.tpl.php';
	}

}


// End of page
llxFooter();
$db->close();
