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
 *   	\file       poa/poapartidadev_card.php
 *		\ingroup    poa
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-07-12 16:09
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
dol_include_once('/poa/class/poapartidadev.class.php');

// Load traductions files requiredby by page
$langs->load("poa");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_poa_partida_com=GETPOST('search_fk_poa_partida_com','int');
$search_gestion=GETPOST('search_gestion','int');
$search_fk_poa_prev=GETPOST('search_fk_poa_prev','int');
$search_fk_structure=GETPOST('search_fk_structure','int');
$search_fk_poa=GETPOST('search_fk_poa','int');
$search_fk_contrat=GETPOST('search_fk_contrat','int');
$search_fk_contrato=GETPOST('search_fk_contrato','int');
$search_type_pay=GETPOST('search_type_pay','int');
$search_nro_dev=GETPOST('search_nro_dev','int');
$search_partida=GETPOST('search_partida','alpha');
$search_invoice=GETPOST('search_invoice','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_statut=GETPOST('search_statut','int');
$search_active=GETPOST('search_active','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Poapartidadev($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('poapartidadev'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_poa_partida_com=GETPOST('fk_poa_partida_com','int');
	$object->gestion=GETPOST('gestion','int');
	$object->fk_poa_prev=GETPOST('fk_poa_prev','int');
	$object->fk_structure=GETPOST('fk_structure','int');
	$object->fk_poa=GETPOST('fk_poa','int');
	$object->fk_contrat=GETPOST('fk_contrat','int');
	$object->fk_contrato=GETPOST('fk_contrato','int');
	$object->type_pay=GETPOST('type_pay','int');
	$object->nro_dev=GETPOST('nro_dev','int');
	$object->partida=GETPOST('partida','alpha');
	$object->invoice=GETPOST('invoice','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->statut=GETPOST('statut','int');
	$object->active=GETPOST('active','int');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/list.php',1);
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

		
	$object->fk_poa_partida_com=GETPOST('fk_poa_partida_com','int');
	$object->gestion=GETPOST('gestion','int');
	$object->fk_poa_prev=GETPOST('fk_poa_prev','int');
	$object->fk_structure=GETPOST('fk_structure','int');
	$object->fk_poa=GETPOST('fk_poa','int');
	$object->fk_contrat=GETPOST('fk_contrat','int');
	$object->fk_contrato=GETPOST('fk_contrato','int');
	$object->type_pay=GETPOST('type_pay','int');
	$object->nro_dev=GETPOST('nro_dev','int');
	$object->partida=GETPOST('partida','alpha');
	$object->invoice=GETPOST('invoice','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->statut=GETPOST('statut','int');
	$object->active=GETPOST('active','int');

		

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
			header("Location: ".dol_buildpath('/poa/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_partida_com").'</td><td><input class="flat" type="text" name="fk_poa_partida_com" value="'.GETPOST('fk_poa_partida_com').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgestion").'</td><td><input class="flat" type="text" name="gestion" value="'.GETPOST('gestion').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_prev").'</td><td><input class="flat" type="text" name="fk_poa_prev" value="'.GETPOST('fk_poa_prev').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td><input class="flat" type="text" name="fk_structure" value="'.GETPOST('fk_structure').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa").'</td><td><input class="flat" type="text" name="fk_poa" value="'.GETPOST('fk_poa').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrat").'</td><td><input class="flat" type="text" name="fk_contrat" value="'.GETPOST('fk_contrat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrato").'</td><td><input class="flat" type="text" name="fk_contrato" value="'.GETPOST('fk_contrato').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_pay").'</td><td><input class="flat" type="text" name="type_pay" value="'.GETPOST('type_pay').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_dev").'</td><td><input class="flat" type="text" name="nro_dev" value="'.GETPOST('nro_dev').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td><input class="flat" type="text" name="partida" value="'.GETPOST('partida').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinvoice").'</td><td><input class="flat" type="text" name="invoice" value="'.GETPOST('invoice').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.GETPOST('statut').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.GETPOST('active').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_partida_com").'</td><td><input class="flat" type="text" name="fk_poa_partida_com" value="'.$object->fk_poa_partida_com.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgestion").'</td><td><input class="flat" type="text" name="gestion" value="'.$object->gestion.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_prev").'</td><td><input class="flat" type="text" name="fk_poa_prev" value="'.$object->fk_poa_prev.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td><input class="flat" type="text" name="fk_structure" value="'.$object->fk_structure.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa").'</td><td><input class="flat" type="text" name="fk_poa" value="'.$object->fk_poa.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrat").'</td><td><input class="flat" type="text" name="fk_contrat" value="'.$object->fk_contrat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrato").'</td><td><input class="flat" type="text" name="fk_contrato" value="'.$object->fk_contrato.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_pay").'</td><td><input class="flat" type="text" name="type_pay" value="'.$object->type_pay.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_dev").'</td><td><input class="flat" type="text" name="nro_dev" value="'.$object->nro_dev.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td><input class="flat" type="text" name="partida" value="'.$object->partida.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinvoice").'</td><td><input class="flat" type="text" name="invoice" value="'.$object->invoice.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$object->active.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_partida_com").'</td><td>$object->fk_poa_partida_com</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgestion").'</td><td>$object->gestion</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_prev").'</td><td>$object->fk_poa_prev</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td>$object->fk_structure</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa").'</td><td>$object->fk_poa</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrat").'</td><td>$object->fk_contrat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrato").'</td><td>$object->fk_contrato</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_pay").'</td><td>$object->type_pay</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_dev").'</td><td>$object->nro_dev</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td>$object->partida</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinvoice").'</td><td>$object->invoice</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>$object->statut</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>$object->active</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->poa->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->poa->delete)
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
