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
 *   	\file       fiscal/entity_card.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-17 15:53
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/fiscal/class/entity.class.php');
dol_include_once('/fiscal/class/entityadd.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_options=GETPOST('search_options','alpha');
$search_visible=GETPOST('search_visible','int');
$search_active=GETPOST('search_active','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Entity($db);
$objectadd = new Entityadd($db);
$objuser = new User($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
	$res = $objectadd->fetch(0,$object->id);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('entity'));
$extrafields = new ExtraFields($db);



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
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->label=GETPOST('label','alpha');
		$object->description=GETPOST('description','alpha');
		$object->fk_user_creat=$user->id;
		$object->options=GETPOST('options','alpha');
		$object->visible=1;
		$object->active=1;

		$objectadd->socialreason=GETPOST('socialreason','alpha');
		$objectadd->nit=GETPOST('nit','int');
		$objectadd->activity=GETPOST('activity','alpha');
		$objectadd->address=GETPOST('address','alpha');
		$objectadd->city=GETPOST('city','alpha');
		$objectadd->phone=GETPOST('phone','alpha');
		$objectadd->message=GETPOST('message','alpha');
		$objectadd->status=1;


		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if (! $error)
		{
			$db->begin();
			$result=$object->create($user);
			if ($result > 0)
			{
				//creamos en add
				$objectadd->fk_entity = $result;
				$res = $objectadd->create($user);
				if ($res > 0)
				{
					$db->commit();
					setEventMessages($langs->trans('Saverecord'),null,'mesgs');
					// Creation OK
					$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/company/card.php?id='.$result,1);
					header("Location: ".$urltogo);
					exit;
				}
				else
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
			$db->rollback();
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;


		$object->label=GETPOST('label','alpha');
		$object->description=GETPOST('description','alpha');
		//$object->fk_user_creat=GETPOST('fk_user_creat','int');
		$object->options='0';
		$object->visible=GETPOST('visible','int');
		$object->active=GETPOST('active','int');

		$objectadd->socialreason=GETPOST('socialreason','alpha');
		$objectadd->nit=GETPOST('nit','int');
		$objectadd->activity=GETPOST('activity','alpha');
		$objectadd->address=GETPOST('address','alpha');
		$objectadd->city=GETPOST('city','alpha');
		$objectadd->phone=GETPOST('phone','alpha');
		$objectadd->message=GETPOST('message','alpha');
		$lAdd = false;
		if ($objectadd->fk_entity != $object->id)
		{
			$lAdd = true;
			$objectadd->fk_entity = $object->id;
			$objectadd->status = 1;
		}

		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if (!$lAdd)
				$res=$objectadd->update($user);
			else
				$res=$objectadd->create($user);
			if ($res <= 0)
			{
				if (! empty($objectadd->errors)) setEventMessages(null, $objectadd->errors, 'errors');
				else setEventMessages($objectadd->error, null, 'errors');
			}
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
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
		//verificamos si no hicimos uso en facturación
		$lDelete = true;
		if ($conf->purchase->enabled)
		{
			require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
			$facture = new Facturefournadd($db);
			$filterstatic = " AND t.nit_company = '".trim($objectadd->nit)."'";
			$res = $facture->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
			if ($res > 0)
				$lDelete = false;
		}
		if ($lDelete)
		{
			$db->begin();
			$result=$object->delete($user);
			$res=$objectadd->delete($user);
			if ($res <= 0)
			{
				setEventMessages($objectadd->error, $objectadd->errors, 'errors');
				$error++;
			}
			if ($result <= 0)
			{
				setEventMessages($object->error, $object->errors, 'errors');
				$error++;
			}

			if (!$error)
			{
					// Delete OK
				$db->commit();
				setEventMessages("RecordDeleted", null, 'mesgs');
				header("Location: ".dol_buildpath('/fiscal/company/list.php',1));
				exit;
			}
			else
			{
				$db->rollback();
				$action = '';
			}
		}
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Company'),'');

$form=new Form($db);


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
	print load_fiche_titre($langs->trans("Newcompany"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcompany").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	//print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsocialreason").'</td><td><input class="flat" type="text" name="socialreason" value="'.$object->socialreason.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.$object->nit.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactivity").'</td><td><input class="flat" type="text" name="activity" value="'.$object->activity.'" required></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaddress").'</td><td><input class="flat" type="text" name="address" value="'.$object->address.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcity").'</td><td><input class="flat" type="text" name="city" value="'.$object->city.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldphone").'</td><td><input class="flat" type="text" name="phone" value="'.$object->phone.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmessage").'</td><td><input class="flat" type="text" name="message" value="'.$object->message.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.GETPOST('fk_user_creat').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldoptions").'</td><td><input class="flat" type="text" name="options" value="'.GETPOST('options').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvisible").'</td><td><input class="flat" type="text" name="visible" value="'.GETPOST('visible').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.GETPOST('active').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Company"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcompany").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
	//print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsocialreason").'</td><td><input class="flat" type="text" name="socialreason" value="'.$objectadd->socialreason.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.$objectadd->nit.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactivity").'</td><td><input class="flat" type="text" name="activity" value="'.$objectadd->activity.'" required></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaddress").'</td><td><input class="flat" type="text" name="address" value="'.$objectadd->address.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcity").'</td><td><input class="flat" type="text" name="city" value="'.$objectadd->city.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldphone").'</td><td><input class="flat" type="text" name="phone" value="'.$objectadd->phone.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmessage").'</td><td><input class="flat" type="text" name="message" value="'.$objectadd->message.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.$object->fk_user_creat.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldoptions").'</td><td><input class="flat" type="text" name="options" value="'.$object->options.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvisible").'</td><td>';
	print $form->selectyesno('visible',$object->visible,1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',$object->active,1);
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("Company"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteCompany'), $langs->trans('ConfirmDeleteCompany').' '.$object->label, 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td width="20%">'.$langs->trans("Fieldcompany").'</td><td>'.$object->label.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fielddescription").'</td><td>'.$object->description.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldsocialreason").'</td><td>'.$objectadd->socialreason.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldnit").'</td><td>'.$objectadd->nit.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactivity").'</td><td>'.$objectadd->activity.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaddress").'</td><td>'.$objectadd->address.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcity").'</td><td>'.$objectadd->city.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldphone").'</td><td>'.$objectadd->phone.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmessage").'</td><td>'.$objectadd->message.'</td></tr>';
	$objuser->fetch($object->fk_user_creat);
	print '<tr><td>'.$langs->trans("Fieldfk_user_creat").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldoptions").'</td><td>'.$object->options.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldvisible").'</td><td>'.($object->visible?$langs->trans('Yes'):$langs->trans('No')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?$langs->trans('Yes'):$langs->trans('No')).'</td></tr>';

	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->fiscal->comp->mod)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->fiscal->comp->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
