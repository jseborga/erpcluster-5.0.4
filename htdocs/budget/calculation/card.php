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
 *   	\file       budget/parametercalculation_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-16 18:21
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
dol_include_once('/budget/class/parametercalculation.class.php');
dol_include_once('/budget/class/ctypeparameter.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_code=GETPOST('search_code','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
if (!$user->rights->budget->par->read) accessforbidden();

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Parametercalculation($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,(empty($ref)?NULL:$ref));
	if ($result < 0) dol_print_error($db);
}
$objCtypeparameter = new Ctypeparameter($db);

$objuser = new User($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('parametercalculation'));
$extrafields = new ExtraFields($db);

//recuperamos la lista de tipo de parametros
$res = $objCtypeparameter->fetchAll('ASC','t.label',0,0,array(),'AND');
if ($res > 0)
{
	$lines = $objCtypeparameter->lines;
	foreach ($lines AS $j => $line)
		$aParameter[$line->code] = $line->label;
}
//$aParameter=array('BENESOC'=>$langs->trans('Socialbenefits'),'HERMEN'=>$langs->trans('Minortools'),'GASGEN'=>$langs->trans('Generalexpenses'),'COSTMO'=>$langs->trans('Laborcostdirecthours'));

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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/calculation/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=$conf->entity;
		$object->code=GETPOST('code','alpha');
		$object->label=GETPOST('label','alpha');
		$object->type=GETPOST('type','alpha');
		$object->amount=GETPOST('amount','int');
		if (empty($object->amount))$object->amount=0;
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();
		$object->active=0;
		$object->status=1;



		if (empty($object->code))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Code")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}
		if (empty($object->amount) || $object->amount <=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldamount")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/calculation/list.php',1);
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


		$object->entity=$conf->entity;
		$object->code=GETPOST('code','alpha');
		$object->label=GETPOST('label','alpha');
		$object->type=GETPOST('type','alpha');
		$object->amount=GETPOST('amount','int');
		if (empty($object->amount))$object->amount=0;
		$object->fk_user_mod=$user->id;
		$object->datem = dol_now();
		$object->tms = dol_now();

		if (empty($object->code))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Code")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}
		if (empty($object->amount) || $object->amount <=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldamount")), null, 'errors');
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
	if ($action == 'confirm_delete' && $_REQUEST['confirm']== 'yes')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/calculation/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
	// Action to delete
	if ($action == 'confirm_validate' && $_REQUEST['confirm']== 'yes')
	{
		$object->status = ($object->status==0?1:0);
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			if ($object->status) setEventMessages("RecordValidate", null, 'mesgs');
			else setEventMessages("RecordNovalidate", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/calculation/card.php?id='.$id,1));
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

llxHeader('',$langs->trans('Calculationparameters'),'');

$form=new Form($db);


// Put here content of your page


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Newparameter"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode").'</td><td><input class="flat" type="text" name="code" value="'.GETPOST('code').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>';
	print $form->selectarray('type',$aParameter,GETPOST('type'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="number" min="0" step="any" name="amount" value="'.GETPOST('amount').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Parametercalculation"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode").'</td><td><input class="flat" type="text" name="code" value="'.$object->code.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>';
	print $form->selectarray('type',$aParameter,(GETPOST('type')?GETPOST('type'):$object->type),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="number" min="0" step="any" name="amount" value="'.(GETPOST('amount')?GETPOST('amount'):$object->amount).'"></td></tr>';
	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && ($action != 'edit' && $action != 'create'))
{
	print load_fiche_titre($langs->trans("Parametercalculation"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Deleteparameter'), $langs->trans('ConfirmDeleteparameter'), 'confirm_delete', '', 1, 2);
		print $formconfirm;
	}
	if ($object->status == 0)
	{
		if ($action == 'validate') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validateparameter'), $langs->trans('ConfirmValidateparameter'), 'confirm_validate', '', 1, 2);
			print $formconfirm;
		}
	}
	else
	{
		if ($action == 'validate') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Novalidateparameter'), $langs->trans('ConfirmNovalidateparameter'), 'confirm_validate', '', 1, 2);
			print $formconfirm;
		}
	}
	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans("Fieldcode").'</td><td>'.$object->code.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtype").'</td><td>'.$aParameter[$object->type].'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldamount").'</td><td>'.price($object->amount).'</td></tr>';
	$objuser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	$objuser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(2).'</td></tr>';

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
		if ($object->status == 0)
		{
			if ($user->rights->budget->par->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}
			if ($user->rights->budget->par->val)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
			}
			if ($user->rights->budget->par->del)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
		}
		else
		{
			if ($user->rights->budget->par->val)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Novalidate").'</a></div>'."\n";
			}

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
