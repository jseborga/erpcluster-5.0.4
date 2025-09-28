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
 *   	\file       advancepayment/paiementfournadvance_card.php
 *		\ingroup    advancepayment
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-30 09:27
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
dol_include_once('/advancepayment/class/paiementfournadvance.class.php');

// Load traductions files requiredby by page
$langs->load("advancepayment");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_ref=GETPOST('search_ref','alpha');
$search_entity=GETPOST('search_entity','int');
$search_amount=GETPOST('search_amount','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_facture=GETPOST('search_fk_facture','int');
$search_origin=GETPOST('search_origin','alpha');
$search_originid=GETPOST('search_originid','int');
$search_fk_paiement=GETPOST('search_fk_paiement','int');
$search_num_paiement=GETPOST('search_num_paiement','alpha');
$search_note=GETPOST('search_note','alpha');
$search_fk_bank=GETPOST('search_fk_bank','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_statut=GETPOST('search_statut','int');
$search_multicurrency_amount=GETPOST('search_multicurrency_amount','alpha');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Paiementfournadvance($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('paiementfournadvance'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/advancepayment/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->ref=GETPOST('ref','alpha');
	$object->entity=GETPOST('entity','int');
	$object->amount=GETPOST('amount','alpha');
	$object->fk_user_author=GETPOST('fk_user_author','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->fk_facture=GETPOST('fk_facture','int');
	$object->origin=GETPOST('origin','alpha');
	$object->originid=GETPOST('originid','int');
	$object->fk_paiement=GETPOST('fk_paiement','int');
	$object->num_paiement=GETPOST('num_paiement','alpha');
	$object->note=GETPOST('note','alpha');
	$object->fk_bank=GETPOST('fk_bank','int');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->statut=GETPOST('statut','int');
	$object->multicurrency_amount=GETPOST('multicurrency_amount','alpha');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/advancepayment/list.php',1);
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

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		
	$object->ref=GETPOST('ref','alpha');
	$object->entity=GETPOST('entity','int');
	$object->amount=GETPOST('amount','alpha');
	$object->fk_user_author=GETPOST('fk_user_author','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->fk_facture=GETPOST('fk_facture','int');
	$object->origin=GETPOST('origin','alpha');
	$object->originid=GETPOST('originid','int');
	$object->fk_paiement=GETPOST('fk_paiement','int');
	$object->num_paiement=GETPOST('num_paiement','alpha');
	$object->note=GETPOST('note','alpha');
	$object->fk_bank=GETPOST('fk_bank','int');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->statut=GETPOST('statut','int');
	$object->multicurrency_amount=GETPOST('multicurrency_amount','alpha');

		

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
			header("Location: ".dol_buildpath('/advancepayment/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td><input class="flat" type="text" name="fk_user_author" value="'.GETPOST('fk_user_author').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.GETPOST('fk_soc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td><input class="flat" type="text" name="fk_facture" value="'.GETPOST('fk_facture').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorigin").'</td><td><input class="flat" type="text" name="origin" value="'.GETPOST('origin').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldoriginid").'</td><td><input class="flat" type="text" name="originid" value="'.GETPOST('originid').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_paiement").'</td><td><input class="flat" type="text" name="fk_paiement" value="'.GETPOST('fk_paiement').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_paiement").'</td><td><input class="flat" type="text" name="num_paiement" value="'.GETPOST('num_paiement').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote").'</td><td><input class="flat" type="text" name="note" value="'.GETPOST('note').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td><input class="flat" type="text" name="fk_bank" value="'.GETPOST('fk_bank').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.GETPOST('statut').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_amount").'</td><td><input class="flat" type="text" name="multicurrency_amount" value="'.GETPOST('multicurrency_amount').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td><input class="flat" type="text" name="fk_user_author" value="'.$object->fk_user_author.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td><input class="flat" type="text" name="fk_facture" value="'.$object->fk_facture.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorigin").'</td><td><input class="flat" type="text" name="origin" value="'.$object->origin.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldoriginid").'</td><td><input class="flat" type="text" name="originid" value="'.$object->originid.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_paiement").'</td><td><input class="flat" type="text" name="fk_paiement" value="'.$object->fk_paiement.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_paiement").'</td><td><input class="flat" type="text" name="num_paiement" value="'.$object->num_paiement.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote").'</td><td><input class="flat" type="text" name="note" value="'.$object->note.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td><input class="flat" type="text" name="fk_bank" value="'.$object->fk_bank.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_amount").'</td><td><input class="flat" type="text" name="multicurrency_amount" value="'.$object->multicurrency_amount.'"></td></tr>';

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
	print load_fiche_titre($langs->trans("MyModule"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>$object->entity</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td>$object->fk_user_author</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td>$object->fk_soc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td>$object->fk_facture</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorigin").'</td><td>$object->origin</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldoriginid").'</td><td>$object->originid</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_paiement").'</td><td>$object->fk_paiement</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_paiement").'</td><td>$object->num_paiement</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote").'</td><td>$object->note</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td>$object->fk_bank</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>$object->model_pdf</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>$object->statut</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_amount").'</td><td>$object->multicurrency_amount</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->advancepayment->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->advancepayment->delete)
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
