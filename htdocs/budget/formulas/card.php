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
 *   	\file       budget/itemsformula_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-05 12:40
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
dol_include_once('/budget/class/itemsformula.class.php');
dol_include_once('/budget/class/pustructureadd.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/categories/class/categorie.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
//variables
$aFormula = array(
	'sum'=>$langs->trans('Sumar columnas'),
	'sumar' =>'+',
	'restar'=> '-',
	'multiplicar'=>'*',
	'dividir'=>'/');

$search_fk_budget=GETPOST('search_fk_budget','int');
$search_fk_pu_structure=GETPOST('search_fk_pu_structure','int');
$search_formula=GETPOST('search_formula','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_sequen=GETPOST('search_sequen','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_date_create=GETPOST('search_date_create','int');
$search_date_mod=GETPOST('search_date_mod','int');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if (!$user->rights->budget->form->read) accessforbidden();

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Itemsformula($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objuser = new User($db);
$objcat = new Categorie($db);
$pustr = new Pustructureadd($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('itemsformula'));
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
		print_r($_POST);
		$object->fk_budget=GETPOST('fk_budget','int');
		$object->fk_pu_structure=GETPOST('fk_pu_structure');
		$object->formula=GETPOST('formula');
		$object->quant=GETPOST('quant','int');
		$object->sequen=GETPOST('sequen','int');
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->date_create=dol_now();
		$object->date_mod=dol_now();
		$object->tms = dol_now();
		$object->statut=0;



		if (empty($object->ref))
		{
			//$error++;
			//setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/formula/card.php?id='.$result,1);
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


		$object->fk_budget=GETPOST('fk_budget','int');
		$object->fk_pu_structure=GETPOST('fk_pu_structure','int');
		$object->formula=GETPOST('formula','alpha');
		$object->quant=GETPOST('quant','alpha');
		$object->sequen=GETPOST('sequen','int');
		$object->fk_user_create=GETPOST('fk_user_create','int');
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->date_create=GETPOST('date_create','int');
		$object->date_mod=GETPOST('date_mod','int');
		$object->statut=GETPOST('statut','int');



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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.GETPOST('fk_budget').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequen").'</td><td><input class="flat" type="number" min="1" name="sequen" value="'.GETPOST('sequen').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_pu_structure").'</td><td>';
	$filter = array(1=>1);
	$filterstatic = " AND fk_budget = 0";
	$pustr->fetchAll('ASC', 'ordby', 0, 0, $filter, 'AND',$filterstatic);
	print $pustr->pu_select('','fk_pu_structure','',1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td>';
	print $form->selectarray('formula',$aFormula,'',1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.GETPOST('quant').'"></td></tr>';

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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.$object->fk_budget.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_pu_structure").'</td><td>';

	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.$object->formula.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$object->quant.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequen").'</td><td><input class="flat" type="text" name="sequen" value="'.$object->sequen.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate_create").'</td><td><input class="flat" type="text" name="date_create" value="'.$object->date_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate_mod").'</td><td><input class="flat" type="text" name="date_mod" value="'.$object->date_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';

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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td>'.$object->fk_budget.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_pu_structure").'</td><td>'.$object->fk_pu_structure.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td>'.$object->formula.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td>'.$object->quant.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequen").'</td><td>'.$object->sequen.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate_create").'</td><td>'.dol_print_date($object->date_create,'day').'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>'.$object->getLibStatut(1).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->form->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->form->del)
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
