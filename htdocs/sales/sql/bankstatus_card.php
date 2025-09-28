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
 *   	\file       sales/bankstatus_card.php
 *		\ingroup    sales
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-22 21:57
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
dol_include_once('/sales/class/bankstatus.class.php');

// Load traductions files requiredby by page
$langs->load("sales");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_bank=GETPOST('search_fk_bank','int');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_subsidiary=GETPOST('search_fk_subsidiary','int');
$search_fk_bank_historial=GETPOST('search_fk_bank_historial','int');
$search_exchange=GETPOST('search_exchange','alpha');
$search_previus_balance=GETPOST('search_previus_balance','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_text_amount=GETPOST('search_text_amount','alpha');
$search_amount_open=GETPOST('search_amount_open','alpha');
$search_text_amount_open=GETPOST('search_text_amount_open','alpha');
$search_amount_balance=GETPOST('search_amount_balance','alpha');
$search_amount_income=GETPOST('search_amount_income','alpha');
$search_amount_input=GETPOST('search_amount_input','alpha');
$search_amount_sale=GETPOST('search_amount_sale','alpha');
$search_amount_null=GETPOST('search_amount_null','alpha');
$search_amount_advance=GETPOST('search_amount_advance','alpha');
$search_amount_transf_input=GETPOST('search_amount_transf_input','alpha');
$search_amount_transf_output=GETPOST('search_amount_transf_output','alpha');
$search_amount_spending=GETPOST('search_amount_spending','alpha');
$search_amount_expense=GETPOST('search_amount_expense','alpha');
$search_amount_close=GETPOST('search_amount_close','alpha');
$search_missing_money=GETPOST('search_missing_money','alpha');
$search_leftover_money=GETPOST('search_leftover_money','alpha');
$search_amount_exchange=GETPOST('search_amount_exchange','alpha');
$search_invoice_annulled=GETPOST('search_invoice_annulled','alpha');
$search_text_exchange=GETPOST('search_text_exchange','alpha');
$search_text_close=GETPOST('search_text_close','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_var_detail=GETPOST('search_var_detail','alpha');
$search_typecash=GETPOST('search_typecash','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_close=GETPOST('search_fk_user_close','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Bankstatus($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('bankstatus'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/sales/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_bank=GETPOST('fk_bank','int');
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_subsidiary=GETPOST('fk_subsidiary','int');
	$object->fk_bank_historial=GETPOST('fk_bank_historial','int');
	$object->exchange=GETPOST('exchange','alpha');
	$object->previus_balance=GETPOST('previus_balance','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->text_amount=GETPOST('text_amount','alpha');
	$object->amount_open=GETPOST('amount_open','alpha');
	$object->text_amount_open=GETPOST('text_amount_open','alpha');
	$object->amount_balance=GETPOST('amount_balance','alpha');
	$object->amount_income=GETPOST('amount_income','alpha');
	$object->amount_input=GETPOST('amount_input','alpha');
	$object->amount_sale=GETPOST('amount_sale','alpha');
	$object->amount_null=GETPOST('amount_null','alpha');
	$object->amount_advance=GETPOST('amount_advance','alpha');
	$object->amount_transf_input=GETPOST('amount_transf_input','alpha');
	$object->amount_transf_output=GETPOST('amount_transf_output','alpha');
	$object->amount_spending=GETPOST('amount_spending','alpha');
	$object->amount_expense=GETPOST('amount_expense','alpha');
	$object->amount_close=GETPOST('amount_close','alpha');
	$object->missing_money=GETPOST('missing_money','alpha');
	$object->leftover_money=GETPOST('leftover_money','alpha');
	$object->amount_exchange=GETPOST('amount_exchange','alpha');
	$object->invoice_annulled=GETPOST('invoice_annulled','alpha');
	$object->text_exchange=GETPOST('text_exchange','alpha');
	$object->text_close=GETPOST('text_close','alpha');
	$object->detail=GETPOST('detail','alpha');
	$object->var_detail=GETPOST('var_detail','alpha');
	$object->typecash=GETPOST('typecash','int');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_close=GETPOST('fk_user_close','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->statut=GETPOST('statut','int');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/sales/list.php',1);
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

		
	$object->fk_bank=GETPOST('fk_bank','int');
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_subsidiary=GETPOST('fk_subsidiary','int');
	$object->fk_bank_historial=GETPOST('fk_bank_historial','int');
	$object->exchange=GETPOST('exchange','alpha');
	$object->previus_balance=GETPOST('previus_balance','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->text_amount=GETPOST('text_amount','alpha');
	$object->amount_open=GETPOST('amount_open','alpha');
	$object->text_amount_open=GETPOST('text_amount_open','alpha');
	$object->amount_balance=GETPOST('amount_balance','alpha');
	$object->amount_income=GETPOST('amount_income','alpha');
	$object->amount_input=GETPOST('amount_input','alpha');
	$object->amount_sale=GETPOST('amount_sale','alpha');
	$object->amount_null=GETPOST('amount_null','alpha');
	$object->amount_advance=GETPOST('amount_advance','alpha');
	$object->amount_transf_input=GETPOST('amount_transf_input','alpha');
	$object->amount_transf_output=GETPOST('amount_transf_output','alpha');
	$object->amount_spending=GETPOST('amount_spending','alpha');
	$object->amount_expense=GETPOST('amount_expense','alpha');
	$object->amount_close=GETPOST('amount_close','alpha');
	$object->missing_money=GETPOST('missing_money','alpha');
	$object->leftover_money=GETPOST('leftover_money','alpha');
	$object->amount_exchange=GETPOST('amount_exchange','alpha');
	$object->invoice_annulled=GETPOST('invoice_annulled','alpha');
	$object->text_exchange=GETPOST('text_exchange','alpha');
	$object->text_close=GETPOST('text_close','alpha');
	$object->detail=GETPOST('detail','alpha');
	$object->var_detail=GETPOST('var_detail','alpha');
	$object->typecash=GETPOST('typecash','int');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_close=GETPOST('fk_user_close','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
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
			header("Location: ".dol_buildpath('/sales/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td><input class="flat" type="text" name="fk_bank" value="'.GETPOST('fk_bank').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.GETPOST('fk_user').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_subsidiary").'</td><td><input class="flat" type="text" name="fk_subsidiary" value="'.GETPOST('fk_subsidiary').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank_historial").'</td><td><input class="flat" type="text" name="fk_bank_historial" value="'.GETPOST('fk_bank_historial').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange").'</td><td><input class="flat" type="text" name="exchange" value="'.GETPOST('exchange').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprevius_balance").'</td><td><input class="flat" type="text" name="previus_balance" value="'.GETPOST('previus_balance').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_amount").'</td><td><input class="flat" type="text" name="text_amount" value="'.GETPOST('text_amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_open").'</td><td><input class="flat" type="text" name="amount_open" value="'.GETPOST('amount_open').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_amount_open").'</td><td><input class="flat" type="text" name="text_amount_open" value="'.GETPOST('text_amount_open').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_balance").'</td><td><input class="flat" type="text" name="amount_balance" value="'.GETPOST('amount_balance').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_income").'</td><td><input class="flat" type="text" name="amount_income" value="'.GETPOST('amount_income').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_input").'</td><td><input class="flat" type="text" name="amount_input" value="'.GETPOST('amount_input').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_sale").'</td><td><input class="flat" type="text" name="amount_sale" value="'.GETPOST('amount_sale').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_null").'</td><td><input class="flat" type="text" name="amount_null" value="'.GETPOST('amount_null').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_advance").'</td><td><input class="flat" type="text" name="amount_advance" value="'.GETPOST('amount_advance').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_transf_input").'</td><td><input class="flat" type="text" name="amount_transf_input" value="'.GETPOST('amount_transf_input').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_transf_output").'</td><td><input class="flat" type="text" name="amount_transf_output" value="'.GETPOST('amount_transf_output').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_spending").'</td><td><input class="flat" type="text" name="amount_spending" value="'.GETPOST('amount_spending').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_expense").'</td><td><input class="flat" type="text" name="amount_expense" value="'.GETPOST('amount_expense').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_close").'</td><td><input class="flat" type="text" name="amount_close" value="'.GETPOST('amount_close').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmissing_money").'</td><td><input class="flat" type="text" name="missing_money" value="'.GETPOST('missing_money').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldleftover_money").'</td><td><input class="flat" type="text" name="leftover_money" value="'.GETPOST('leftover_money').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_exchange").'</td><td><input class="flat" type="text" name="amount_exchange" value="'.GETPOST('amount_exchange').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinvoice_annulled").'</td><td><input class="flat" type="text" name="invoice_annulled" value="'.GETPOST('invoice_annulled').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_exchange").'</td><td><input class="flat" type="text" name="text_exchange" value="'.GETPOST('text_exchange').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_close").'</td><td><input class="flat" type="text" name="text_close" value="'.GETPOST('text_close').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.GETPOST('detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvar_detail").'</td><td><input class="flat" type="text" name="var_detail" value="'.GETPOST('var_detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtypecash").'</td><td><input class="flat" type="text" name="typecash" value="'.GETPOST('typecash').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td><input class="flat" type="text" name="fk_user_close" value="'.GETPOST('fk_user_close').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.GETPOST('statut').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td><input class="flat" type="text" name="fk_bank" value="'.$object->fk_bank.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.$object->fk_user.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_subsidiary").'</td><td><input class="flat" type="text" name="fk_subsidiary" value="'.$object->fk_subsidiary.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank_historial").'</td><td><input class="flat" type="text" name="fk_bank_historial" value="'.$object->fk_bank_historial.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange").'</td><td><input class="flat" type="text" name="exchange" value="'.$object->exchange.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprevius_balance").'</td><td><input class="flat" type="text" name="previus_balance" value="'.$object->previus_balance.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_amount").'</td><td><input class="flat" type="text" name="text_amount" value="'.$object->text_amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_open").'</td><td><input class="flat" type="text" name="amount_open" value="'.$object->amount_open.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_amount_open").'</td><td><input class="flat" type="text" name="text_amount_open" value="'.$object->text_amount_open.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_balance").'</td><td><input class="flat" type="text" name="amount_balance" value="'.$object->amount_balance.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_income").'</td><td><input class="flat" type="text" name="amount_income" value="'.$object->amount_income.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_input").'</td><td><input class="flat" type="text" name="amount_input" value="'.$object->amount_input.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_sale").'</td><td><input class="flat" type="text" name="amount_sale" value="'.$object->amount_sale.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_null").'</td><td><input class="flat" type="text" name="amount_null" value="'.$object->amount_null.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_advance").'</td><td><input class="flat" type="text" name="amount_advance" value="'.$object->amount_advance.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_transf_input").'</td><td><input class="flat" type="text" name="amount_transf_input" value="'.$object->amount_transf_input.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_transf_output").'</td><td><input class="flat" type="text" name="amount_transf_output" value="'.$object->amount_transf_output.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_spending").'</td><td><input class="flat" type="text" name="amount_spending" value="'.$object->amount_spending.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_expense").'</td><td><input class="flat" type="text" name="amount_expense" value="'.$object->amount_expense.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_close").'</td><td><input class="flat" type="text" name="amount_close" value="'.$object->amount_close.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmissing_money").'</td><td><input class="flat" type="text" name="missing_money" value="'.$object->missing_money.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldleftover_money").'</td><td><input class="flat" type="text" name="leftover_money" value="'.$object->leftover_money.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_exchange").'</td><td><input class="flat" type="text" name="amount_exchange" value="'.$object->amount_exchange.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinvoice_annulled").'</td><td><input class="flat" type="text" name="invoice_annulled" value="'.$object->invoice_annulled.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_exchange").'</td><td><input class="flat" type="text" name="text_exchange" value="'.$object->text_exchange.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_close").'</td><td><input class="flat" type="text" name="text_close" value="'.$object->text_close.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.$object->detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvar_detail").'</td><td><input class="flat" type="text" name="var_detail" value="'.$object->var_detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtypecash").'</td><td><input class="flat" type="text" name="typecash" value="'.$object->typecash.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td><input class="flat" type="text" name="fk_user_close" value="'.$object->fk_user_close.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td>$object->fk_bank</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td>$object->fk_user</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_subsidiary").'</td><td>$object->fk_subsidiary</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank_historial").'</td><td>$object->fk_bank_historial</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange").'</td><td>$object->exchange</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprevius_balance").'</td><td>$object->previus_balance</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_amount").'</td><td>$object->text_amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_open").'</td><td>$object->amount_open</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_amount_open").'</td><td>$object->text_amount_open</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_balance").'</td><td>$object->amount_balance</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_income").'</td><td>$object->amount_income</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_input").'</td><td>$object->amount_input</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_sale").'</td><td>$object->amount_sale</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_null").'</td><td>$object->amount_null</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_advance").'</td><td>$object->amount_advance</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_transf_input").'</td><td>$object->amount_transf_input</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_transf_output").'</td><td>$object->amount_transf_output</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_spending").'</td><td>$object->amount_spending</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_expense").'</td><td>$object->amount_expense</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_close").'</td><td>$object->amount_close</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmissing_money").'</td><td>$object->missing_money</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldleftover_money").'</td><td>$object->leftover_money</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_exchange").'</td><td>$object->amount_exchange</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinvoice_annulled").'</td><td>$object->invoice_annulled</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_exchange").'</td><td>$object->text_exchange</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtext_close").'</td><td>$object->text_close</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td>$object->detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvar_detail").'</td><td>$object->var_detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtypecash").'</td><td>$object->typecash</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>$object->model_pdf</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td>$object->fk_user_close</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>$object->statut</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->sales->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->sales->delete)
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
