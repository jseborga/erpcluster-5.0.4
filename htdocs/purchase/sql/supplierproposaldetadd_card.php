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
 *   	\file       purchase/supplierproposaldetadd_card.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-05-04 13:57
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
dol_include_once('/purchase/class/supplierproposaldetadd.class.php');

// Load traductions files requiredby by page
$langs->load("purchase");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_supplier_proposaldet=GETPOST('search_fk_supplier_proposaldet','int');
$search_fk_object=GETPOST('search_fk_object','int');
$search_object=GETPOST('search_object','alpha');
$search_fk_fabrication=GETPOST('search_fk_fabrication','int');
$search_fk_fabricationdet=GETPOST('search_fk_fabricationdet','int');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_projet_task=GETPOST('search_fk_projet_task','int');
$search_fk_jobs=GETPOST('search_fk_jobs','int');
$search_fk_jobsdet=GETPOST('search_fk_jobsdet','int');
$search_fk_structure=GETPOST('search_fk_structure','int');
$search_fk_poa=GETPOST('search_fk_poa','int');
$search_partida=GETPOST('search_partida','alpha');
$search_amount_ice=GETPOST('search_amount_ice','alpha');
$search_discount=GETPOST('search_discount','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'purchase', $id);


$object = new Supplierproposaldetadd($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('supplierproposaldetadd'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_supplier_proposaldet=GETPOST('fk_supplier_proposaldet','int');
	$object->fk_object=GETPOST('fk_object','int');
	$object->object=GETPOST('object','alpha');
	$object->fk_fabrication=GETPOST('fk_fabrication','int');
	$object->fk_fabricationdet=GETPOST('fk_fabricationdet','int');
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_projet_task=GETPOST('fk_projet_task','int');
	$object->fk_jobs=GETPOST('fk_jobs','int');
	$object->fk_jobsdet=GETPOST('fk_jobsdet','int');
	$object->fk_structure=GETPOST('fk_structure','int');
	$object->fk_poa=GETPOST('fk_poa','int');
	$object->partida=GETPOST('partida','alpha');
	$object->amount_ice=GETPOST('amount_ice','alpha');
	$object->discount=GETPOST('discount','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->status=GETPOST('status','int');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
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

		
	$object->fk_supplier_proposaldet=GETPOST('fk_supplier_proposaldet','int');
	$object->fk_object=GETPOST('fk_object','int');
	$object->object=GETPOST('object','alpha');
	$object->fk_fabrication=GETPOST('fk_fabrication','int');
	$object->fk_fabricationdet=GETPOST('fk_fabricationdet','int');
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_projet_task=GETPOST('fk_projet_task','int');
	$object->fk_jobs=GETPOST('fk_jobs','int');
	$object->fk_jobsdet=GETPOST('fk_jobsdet','int');
	$object->fk_structure=GETPOST('fk_structure','int');
	$object->fk_poa=GETPOST('fk_poa','int');
	$object->partida=GETPOST('partida','alpha');
	$object->amount_ice=GETPOST('amount_ice','alpha');
	$object->discount=GETPOST('discount','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->status=GETPOST('status','int');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
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
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/purchase/list.php',1));
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

llxHeader('','MyPageName','');

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
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_supplier_proposaldet").'</td><td><input class="flat" type="text" name="fk_supplier_proposaldet" value="'.GETPOST('fk_supplier_proposaldet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td><input class="flat" type="text" name="fk_object" value="'.GETPOST('fk_object').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldobject").'</td><td><input class="flat" type="text" name="object" value="'.GETPOST('object').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_fabrication").'</td><td><input class="flat" type="text" name="fk_fabrication" value="'.GETPOST('fk_fabrication').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_fabricationdet").'</td><td><input class="flat" type="text" name="fk_fabricationdet" value="'.GETPOST('fk_fabricationdet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.GETPOST('fk_projet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task").'</td><td><input class="flat" type="text" name="fk_projet_task" value="'.GETPOST('fk_projet_task').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_jobs").'</td><td><input class="flat" type="text" name="fk_jobs" value="'.GETPOST('fk_jobs').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_jobsdet").'</td><td><input class="flat" type="text" name="fk_jobsdet" value="'.GETPOST('fk_jobsdet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td><input class="flat" type="text" name="fk_structure" value="'.GETPOST('fk_structure').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa").'</td><td><input class="flat" type="text" name="fk_poa" value="'.GETPOST('fk_poa').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td><input class="flat" type="text" name="partida" value="'.GETPOST('partida').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_ice").'</td><td><input class="flat" type="text" name="amount_ice" value="'.GETPOST('amount_ice').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiscount").'</td><td><input class="flat" type="text" name="discount" value="'.GETPOST('discount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_supplier_proposaldet").'</td><td><input class="flat" type="text" name="fk_supplier_proposaldet" value="'.$object->fk_supplier_proposaldet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td><input class="flat" type="text" name="fk_object" value="'.$object->fk_object.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldobject").'</td><td><input class="flat" type="text" name="object" value="'.$object->object.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_fabrication").'</td><td><input class="flat" type="text" name="fk_fabrication" value="'.$object->fk_fabrication.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_fabricationdet").'</td><td><input class="flat" type="text" name="fk_fabricationdet" value="'.$object->fk_fabricationdet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.$object->fk_projet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task").'</td><td><input class="flat" type="text" name="fk_projet_task" value="'.$object->fk_projet_task.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_jobs").'</td><td><input class="flat" type="text" name="fk_jobs" value="'.$object->fk_jobs.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_jobsdet").'</td><td><input class="flat" type="text" name="fk_jobsdet" value="'.$object->fk_jobsdet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td><input class="flat" type="text" name="fk_structure" value="'.$object->fk_structure.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa").'</td><td><input class="flat" type="text" name="fk_poa" value="'.$object->fk_poa.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td><input class="flat" type="text" name="partida" value="'.$object->partida.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_ice").'</td><td><input class="flat" type="text" name="amount_ice" value="'.$object->amount_ice.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiscount").'</td><td><input class="flat" type="text" name="discount" value="'.$object->discount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

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

	$head = commande_prepare_head($object);
	dol_fiche_head($head, 'order', $langs->trans("CustomerOrder"), 0, 'order');
		
	print load_fiche_titre($langs->trans("MyModule"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_supplier_proposaldet").'</td><td>$object->fk_supplier_proposaldet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td>$object->fk_object</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldobject").'</td><td>$object->object</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_fabrication").'</td><td>$object->fk_fabrication</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_fabricationdet").'</td><td>$object->fk_fabricationdet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td>$object->fk_projet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task").'</td><td>$object->fk_projet_task</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_jobs").'</td><td>$object->fk_jobs</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_jobsdet").'</td><td>$object->fk_jobsdet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td>$object->fk_structure</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa").'</td><td>$object->fk_poa</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td>$object->partida</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_ice").'</td><td>$object->amount_ice</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiscount").'</td><td>$object->discount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>$object->status</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->purchase->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->purchase->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('supplierproposaldetadd'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
