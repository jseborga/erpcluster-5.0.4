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
 *   	\file       budget/budget_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-21 18:04
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
dol_include_once('/budget/class/budget.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_calendar=GETPOST('search_fk_calendar','int');
$search_fk_country=GETPOST('search_fk_country','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_city=GETPOST('search_fk_city','int');
$search_data_type=GETPOST('search_data_type','alpha');
$search_location=GETPOST('search_location','alpha');
$search_ref=GETPOST('search_ref','alpha');
$search_version=GETPOST('search_version','alpha');
$search_type_structure=GETPOST('search_type_structure','alpha');
$search_title=GETPOST('search_title','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_public=GETPOST('search_public','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_fk_opp_status=GETPOST('search_fk_opp_status','int');
$search_opp_percent=GETPOST('search_opp_percent','alpha');
$search_fk_user_close=GETPOST('search_fk_user_close','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_opp_amount=GETPOST('search_opp_amount','alpha');
$search_budget_amount=GETPOST('search_budget_amount','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Budget($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budget'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->entity=GETPOST('entity','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_calendar=GETPOST('fk_calendar','int');
	$object->fk_country=GETPOST('fk_country','int');
	$object->fk_departament=GETPOST('fk_departament','int');
	$object->fk_city=GETPOST('fk_city','int');
	$object->data_type=GETPOST('data_type','alpha');
	$object->location=GETPOST('location','alpha');
	$object->ref=GETPOST('ref','alpha');
	$object->version=GETPOST('version','alpha');
	$object->type_structure=GETPOST('type_structure','alpha');
	$object->title=GETPOST('title','alpha');
	$object->description=GETPOST('description','alpha');
	$object->fk_user_creat=GETPOST('fk_user_creat','int');
	$object->public=GETPOST('public','int');
	$object->fk_statut=GETPOST('fk_statut','int');
	$object->fk_opp_status=GETPOST('fk_opp_status','int');
	$object->opp_percent=GETPOST('opp_percent','alpha');
	$object->fk_user_close=GETPOST('fk_user_close','int');
	$object->note_private=GETPOST('note_private','alpha');
	$object->note_public=GETPOST('note_public','alpha');
	$object->opp_amount=GETPOST('opp_amount','alpha');
	$object->budget_amount=GETPOST('budget_amount','alpha');
	$object->model_pdf=GETPOST('model_pdf','alpha');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
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

		
	$object->entity=GETPOST('entity','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_calendar=GETPOST('fk_calendar','int');
	$object->fk_country=GETPOST('fk_country','int');
	$object->fk_departament=GETPOST('fk_departament','int');
	$object->fk_city=GETPOST('fk_city','int');
	$object->data_type=GETPOST('data_type','alpha');
	$object->location=GETPOST('location','alpha');
	$object->ref=GETPOST('ref','alpha');
	$object->version=GETPOST('version','alpha');
	$object->type_structure=GETPOST('type_structure','alpha');
	$object->title=GETPOST('title','alpha');
	$object->description=GETPOST('description','alpha');
	$object->fk_user_creat=GETPOST('fk_user_creat','int');
	$object->public=GETPOST('public','int');
	$object->fk_statut=GETPOST('fk_statut','int');
	$object->fk_opp_status=GETPOST('fk_opp_status','int');
	$object->opp_percent=GETPOST('opp_percent','alpha');
	$object->fk_user_close=GETPOST('fk_user_close','int');
	$object->note_private=GETPOST('note_private','alpha');
	$object->note_public=GETPOST('note_public','alpha');
	$object->opp_amount=GETPOST('opp_amount','alpha');
	$object->budget_amount=GETPOST('budget_amount','alpha');
	$object->model_pdf=GETPOST('model_pdf','alpha');

		

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
			header("Location: ".dol_buildpath('/budget/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.GETPOST('fk_soc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.GETPOST('fk_projet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_calendar").'</td><td><input class="flat" type="text" name="fk_calendar" value="'.GETPOST('fk_calendar').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_country").'</td><td><input class="flat" type="text" name="fk_country" value="'.GETPOST('fk_country').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_departament").'</td><td><input class="flat" type="text" name="fk_departament" value="'.GETPOST('fk_departament').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_city").'</td><td><input class="flat" type="text" name="fk_city" value="'.GETPOST('fk_city').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddata_type").'</td><td><input class="flat" type="text" name="data_type" value="'.GETPOST('data_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocation").'</td><td><input class="flat" type="text" name="location" value="'.GETPOST('location').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldversion").'</td><td><input class="flat" type="text" name="version" value="'.GETPOST('version').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td><input class="flat" type="text" name="type_structure" value="'.GETPOST('type_structure').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td><input class="flat" type="text" name="title" value="'.GETPOST('title').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.GETPOST('fk_user_creat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpublic").'</td><td><input class="flat" type="text" name="public" value="'.GETPOST('public').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.GETPOST('fk_statut').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_opp_status").'</td><td><input class="flat" type="text" name="fk_opp_status" value="'.GETPOST('fk_opp_status').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_percent").'</td><td><input class="flat" type="text" name="opp_percent" value="'.GETPOST('opp_percent').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td><input class="flat" type="text" name="fk_user_close" value="'.GETPOST('fk_user_close').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.GETPOST('note_private').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.GETPOST('note_public').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_amount").'</td><td><input class="flat" type="text" name="opp_amount" value="'.GETPOST('opp_amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbudget_amount").'</td><td><input class="flat" type="text" name="budget_amount" value="'.GETPOST('budget_amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.$object->fk_projet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_calendar").'</td><td><input class="flat" type="text" name="fk_calendar" value="'.$object->fk_calendar.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_country").'</td><td><input class="flat" type="text" name="fk_country" value="'.$object->fk_country.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_departament").'</td><td><input class="flat" type="text" name="fk_departament" value="'.$object->fk_departament.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_city").'</td><td><input class="flat" type="text" name="fk_city" value="'.$object->fk_city.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddata_type").'</td><td><input class="flat" type="text" name="data_type" value="'.$object->data_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocation").'</td><td><input class="flat" type="text" name="location" value="'.$object->location.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldversion").'</td><td><input class="flat" type="text" name="version" value="'.$object->version.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td><input class="flat" type="text" name="type_structure" value="'.$object->type_structure.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td><input class="flat" type="text" name="title" value="'.$object->title.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.$object->fk_user_creat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpublic").'</td><td><input class="flat" type="text" name="public" value="'.$object->public.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.$object->fk_statut.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_opp_status").'</td><td><input class="flat" type="text" name="fk_opp_status" value="'.$object->fk_opp_status.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_percent").'</td><td><input class="flat" type="text" name="opp_percent" value="'.$object->opp_percent.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td><input class="flat" type="text" name="fk_user_close" value="'.$object->fk_user_close.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.$object->note_private.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.$object->note_public.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_amount").'</td><td><input class="flat" type="text" name="opp_amount" value="'.$object->opp_amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbudget_amount").'</td><td><input class="flat" type="text" name="budget_amount" value="'.$object->budget_amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>$object->entity</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td>$object->fk_soc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td>$object->fk_projet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_calendar").'</td><td>$object->fk_calendar</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_country").'</td><td>$object->fk_country</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_departament").'</td><td>$object->fk_departament</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_city").'</td><td>$object->fk_city</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddata_type").'</td><td>$object->data_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocation").'</td><td>$object->location</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldversion").'</td><td>$object->version</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td>$object->type_structure</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td>$object->title</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td>$object->description</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td>$object->fk_user_creat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpublic").'</td><td>$object->public</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td>$object->fk_statut</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_opp_status").'</td><td>$object->fk_opp_status</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_percent").'</td><td>$object->opp_percent</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td>$object->fk_user_close</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td>$object->note_private</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td>$object->note_public</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_amount").'</td><td>$object->opp_amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbudget_amount").'</td><td>$object->budget_amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>$object->model_pdf</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->delete)
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
