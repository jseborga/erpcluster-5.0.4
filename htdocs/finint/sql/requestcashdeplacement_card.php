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
 *   	\file       finint/requestcashdeplacement_card.php
 *		\ingroup    finint
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-09 14:44
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
dol_include_once('/finint/class/requestcashdeplacement.class.php');

// Load traductions files requiredby by page
$langs->load("finint");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_request_cash=GETPOST('search_fk_request_cash','int');
$search_fk_request_cash_dest=GETPOST('search_fk_request_cash_dest','int');
$search_fk_projet_dest=GETPOST('search_fk_projet_dest','int');
$search_fk_projet_task_dest=GETPOST('search_fk_projet_task_dest','int');
$search_fk_account_from=GETPOST('search_fk_account_from','int');
$search_fk_account_dest=GETPOST('search_fk_account_dest','int');
$search_url_id=GETPOST('search_url_id','int');
$search_fk_bank=GETPOST('search_fk_bank','int');
$search_fk_commande_fourn=GETPOST('search_fk_commande_fourn','int');
$search_fk_facture_fourn=GETPOST('search_fk_facture_fourn','int');
$search_fk_entrepot=GETPOST('search_fk_entrepot','int');
$search_fk_user_from=GETPOST('search_fk_user_from','int');
$search_fk_user_to=GETPOST('search_fk_user_to','int');
$search_fk_type=GETPOST('search_fk_type','alpha');
$search_fk_categorie=GETPOST('search_fk_categorie','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_parent_app=GETPOST('search_fk_parent_app','int');
$search_quant=GETPOST('search_quant','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_code_facture=GETPOST('search_code_facture','alpha');
$search_code_type_purchase=GETPOST('search_code_type_purchase','alpha');
$search_type_operation=GETPOST('search_type_operation','int');
$search_nro_chq=GETPOST('search_nro_chq','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_concept=GETPOST('search_concept','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_nit_company=GETPOST('search_nit_company','alpha');
$search_codeqr=GETPOST('search_codeqr','alpha');
$search_fourn_nit=GETPOST('search_fourn_nit','alpha');
$search_fourn_soc=GETPOST('search_fourn_soc','alpha');
$search_fourn_facture=GETPOST('search_fourn_facture','alpha');
$search_fourn_numaut=GETPOST('search_fourn_numaut','alpha');
$search_fourn_amount_ttc=GETPOST('search_fourn_amount_ttc','alpha');
$search_fourn_amount=GETPOST('search_fourn_amount','alpha');
$search_fourn_codecont=GETPOST('search_fourn_codecont','alpha');
$search_fourn_reg1=GETPOST('search_fourn_reg1','alpha');
$search_fourn_reg2=GETPOST('search_fourn_reg2','alpha');
$search_fourn_reg3=GETPOST('search_fourn_reg3','alpha');
$search_fourn_reg4=GETPOST('search_fourn_reg4','alpha');
$search_fourn_reg5=GETPOST('search_fourn_reg5','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_approved=GETPOST('search_fk_user_approved','int');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Requestcashdeplacement($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('requestcashdeplacement'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->entity=GETPOST('entity','int');
	$object->ref=GETPOST('ref','alpha');
	$object->fk_request_cash=GETPOST('fk_request_cash','int');
	$object->fk_request_cash_dest=GETPOST('fk_request_cash_dest','int');
	$object->fk_projet_dest=GETPOST('fk_projet_dest','int');
	$object->fk_projet_task_dest=GETPOST('fk_projet_task_dest','int');
	$object->fk_account_from=GETPOST('fk_account_from','int');
	$object->fk_account_dest=GETPOST('fk_account_dest','int');
	$object->url_id=GETPOST('url_id','int');
	$object->fk_bank=GETPOST('fk_bank','int');
	$object->fk_commande_fourn=GETPOST('fk_commande_fourn','int');
	$object->fk_facture_fourn=GETPOST('fk_facture_fourn','int');
	$object->fk_entrepot=GETPOST('fk_entrepot','int');
	$object->fk_user_from=GETPOST('fk_user_from','int');
	$object->fk_user_to=GETPOST('fk_user_to','int');
	$object->fk_type=GETPOST('fk_type','alpha');
	$object->fk_categorie=GETPOST('fk_categorie','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->fk_parent_app=GETPOST('fk_parent_app','int');
	$object->quant=GETPOST('quant','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->code_facture=GETPOST('code_facture','alpha');
	$object->code_type_purchase=GETPOST('code_type_purchase','alpha');
	$object->type_operation=GETPOST('type_operation','int');
	$object->nro_chq=GETPOST('nro_chq','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->concept=GETPOST('concept','alpha');
	$object->detail=GETPOST('detail','alpha');
	$object->nit_company=GETPOST('nit_company','alpha');
	$object->codeqr=GETPOST('codeqr','alpha');
	$object->fourn_nit=GETPOST('fourn_nit','alpha');
	$object->fourn_soc=GETPOST('fourn_soc','alpha');
	$object->fourn_facture=GETPOST('fourn_facture','alpha');
	$object->fourn_numaut=GETPOST('fourn_numaut','alpha');
	$object->fourn_amount_ttc=GETPOST('fourn_amount_ttc','alpha');
	$object->fourn_amount=GETPOST('fourn_amount','alpha');
	$object->fourn_codecont=GETPOST('fourn_codecont','alpha');
	$object->fourn_reg1=GETPOST('fourn_reg1','alpha');
	$object->fourn_reg2=GETPOST('fourn_reg2','alpha');
	$object->fourn_reg3=GETPOST('fourn_reg3','alpha');
	$object->fourn_reg4=GETPOST('fourn_reg4','alpha');
	$object->fourn_reg5=GETPOST('fourn_reg5','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_approved=GETPOST('fk_user_approved','int');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/list.php',1);
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
	$object->ref=GETPOST('ref','alpha');
	$object->fk_request_cash=GETPOST('fk_request_cash','int');
	$object->fk_request_cash_dest=GETPOST('fk_request_cash_dest','int');
	$object->fk_projet_dest=GETPOST('fk_projet_dest','int');
	$object->fk_projet_task_dest=GETPOST('fk_projet_task_dest','int');
	$object->fk_account_from=GETPOST('fk_account_from','int');
	$object->fk_account_dest=GETPOST('fk_account_dest','int');
	$object->url_id=GETPOST('url_id','int');
	$object->fk_bank=GETPOST('fk_bank','int');
	$object->fk_commande_fourn=GETPOST('fk_commande_fourn','int');
	$object->fk_facture_fourn=GETPOST('fk_facture_fourn','int');
	$object->fk_entrepot=GETPOST('fk_entrepot','int');
	$object->fk_user_from=GETPOST('fk_user_from','int');
	$object->fk_user_to=GETPOST('fk_user_to','int');
	$object->fk_type=GETPOST('fk_type','alpha');
	$object->fk_categorie=GETPOST('fk_categorie','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->fk_parent_app=GETPOST('fk_parent_app','int');
	$object->quant=GETPOST('quant','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->code_facture=GETPOST('code_facture','alpha');
	$object->code_type_purchase=GETPOST('code_type_purchase','alpha');
	$object->type_operation=GETPOST('type_operation','int');
	$object->nro_chq=GETPOST('nro_chq','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->concept=GETPOST('concept','alpha');
	$object->detail=GETPOST('detail','alpha');
	$object->nit_company=GETPOST('nit_company','alpha');
	$object->codeqr=GETPOST('codeqr','alpha');
	$object->fourn_nit=GETPOST('fourn_nit','alpha');
	$object->fourn_soc=GETPOST('fourn_soc','alpha');
	$object->fourn_facture=GETPOST('fourn_facture','alpha');
	$object->fourn_numaut=GETPOST('fourn_numaut','alpha');
	$object->fourn_amount_ttc=GETPOST('fourn_amount_ttc','alpha');
	$object->fourn_amount=GETPOST('fourn_amount','alpha');
	$object->fourn_codecont=GETPOST('fourn_codecont','alpha');
	$object->fourn_reg1=GETPOST('fourn_reg1','alpha');
	$object->fourn_reg2=GETPOST('fourn_reg2','alpha');
	$object->fourn_reg3=GETPOST('fourn_reg3','alpha');
	$object->fourn_reg4=GETPOST('fourn_reg4','alpha');
	$object->fourn_reg5=GETPOST('fourn_reg5','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_approved=GETPOST('fk_user_approved','int');
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
			header("Location: ".dol_buildpath('/finint/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_request_cash").'</td><td><input class="flat" type="text" name="fk_request_cash" value="'.GETPOST('fk_request_cash').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_request_cash_dest").'</td><td><input class="flat" type="text" name="fk_request_cash_dest" value="'.GETPOST('fk_request_cash_dest').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_dest").'</td><td><input class="flat" type="text" name="fk_projet_dest" value="'.GETPOST('fk_projet_dest').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task_dest").'</td><td><input class="flat" type="text" name="fk_projet_task_dest" value="'.GETPOST('fk_projet_task_dest').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_from").'</td><td><input class="flat" type="text" name="fk_account_from" value="'.GETPOST('fk_account_from').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_dest").'</td><td><input class="flat" type="text" name="fk_account_dest" value="'.GETPOST('fk_account_dest').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldurl_id").'</td><td><input class="flat" type="text" name="url_id" value="'.GETPOST('url_id').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td><input class="flat" type="text" name="fk_bank" value="'.GETPOST('fk_bank').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_commande_fourn").'</td><td><input class="flat" type="text" name="fk_commande_fourn" value="'.GETPOST('fk_commande_fourn').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture_fourn").'</td><td><input class="flat" type="text" name="fk_facture_fourn" value="'.GETPOST('fk_facture_fourn').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td><input class="flat" type="text" name="fk_entrepot" value="'.GETPOST('fk_entrepot').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_from").'</td><td><input class="flat" type="text" name="fk_user_from" value="'.GETPOST('fk_user_from').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_to").'</td><td><input class="flat" type="text" name="fk_user_to" value="'.GETPOST('fk_user_to').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td><input class="flat" type="text" name="fk_type" value="'.GETPOST('fk_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td><input class="flat" type="text" name="fk_categorie" value="'.GETPOST('fk_categorie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.GETPOST('fk_soc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent_app").'</td><td><input class="flat" type="text" name="fk_parent_app" value="'.GETPOST('fk_parent_app').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.GETPOST('quant').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_facture").'</td><td><input class="flat" type="text" name="code_facture" value="'.GETPOST('code_facture').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_type_purchase").'</td><td><input class="flat" type="text" name="code_type_purchase" value="'.GETPOST('code_type_purchase').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_operation").'</td><td><input class="flat" type="text" name="type_operation" value="'.GETPOST('type_operation').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_chq").'</td><td><input class="flat" type="text" name="nro_chq" value="'.GETPOST('nro_chq').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconcept").'</td><td><input class="flat" type="text" name="concept" value="'.GETPOST('concept').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.GETPOST('detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit_company").'</td><td><input class="flat" type="text" name="nit_company" value="'.GETPOST('nit_company').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcodeqr").'</td><td><input class="flat" type="text" name="codeqr" value="'.GETPOST('codeqr').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_nit").'</td><td><input class="flat" type="text" name="fourn_nit" value="'.GETPOST('fourn_nit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_soc").'</td><td><input class="flat" type="text" name="fourn_soc" value="'.GETPOST('fourn_soc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_facture").'</td><td><input class="flat" type="text" name="fourn_facture" value="'.GETPOST('fourn_facture').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_numaut").'</td><td><input class="flat" type="text" name="fourn_numaut" value="'.GETPOST('fourn_numaut').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_amount_ttc").'</td><td><input class="flat" type="text" name="fourn_amount_ttc" value="'.GETPOST('fourn_amount_ttc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_amount").'</td><td><input class="flat" type="text" name="fourn_amount" value="'.GETPOST('fourn_amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_codecont").'</td><td><input class="flat" type="text" name="fourn_codecont" value="'.GETPOST('fourn_codecont').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg1").'</td><td><input class="flat" type="text" name="fourn_reg1" value="'.GETPOST('fourn_reg1').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg2").'</td><td><input class="flat" type="text" name="fourn_reg2" value="'.GETPOST('fourn_reg2').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg3").'</td><td><input class="flat" type="text" name="fourn_reg3" value="'.GETPOST('fourn_reg3').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg4").'</td><td><input class="flat" type="text" name="fourn_reg4" value="'.GETPOST('fourn_reg4').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg5").'</td><td><input class="flat" type="text" name="fourn_reg5" value="'.GETPOST('fourn_reg5').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_approved").'</td><td><input class="flat" type="text" name="fk_user_approved" value="'.GETPOST('fk_user_approved').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_request_cash").'</td><td><input class="flat" type="text" name="fk_request_cash" value="'.$object->fk_request_cash.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_request_cash_dest").'</td><td><input class="flat" type="text" name="fk_request_cash_dest" value="'.$object->fk_request_cash_dest.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_dest").'</td><td><input class="flat" type="text" name="fk_projet_dest" value="'.$object->fk_projet_dest.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task_dest").'</td><td><input class="flat" type="text" name="fk_projet_task_dest" value="'.$object->fk_projet_task_dest.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_from").'</td><td><input class="flat" type="text" name="fk_account_from" value="'.$object->fk_account_from.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_dest").'</td><td><input class="flat" type="text" name="fk_account_dest" value="'.$object->fk_account_dest.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldurl_id").'</td><td><input class="flat" type="text" name="url_id" value="'.$object->url_id.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td><input class="flat" type="text" name="fk_bank" value="'.$object->fk_bank.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_commande_fourn").'</td><td><input class="flat" type="text" name="fk_commande_fourn" value="'.$object->fk_commande_fourn.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture_fourn").'</td><td><input class="flat" type="text" name="fk_facture_fourn" value="'.$object->fk_facture_fourn.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td><input class="flat" type="text" name="fk_entrepot" value="'.$object->fk_entrepot.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_from").'</td><td><input class="flat" type="text" name="fk_user_from" value="'.$object->fk_user_from.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_to").'</td><td><input class="flat" type="text" name="fk_user_to" value="'.$object->fk_user_to.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td><input class="flat" type="text" name="fk_type" value="'.$object->fk_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td><input class="flat" type="text" name="fk_categorie" value="'.$object->fk_categorie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent_app").'</td><td><input class="flat" type="text" name="fk_parent_app" value="'.$object->fk_parent_app.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$object->quant.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_facture").'</td><td><input class="flat" type="text" name="code_facture" value="'.$object->code_facture.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_type_purchase").'</td><td><input class="flat" type="text" name="code_type_purchase" value="'.$object->code_type_purchase.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_operation").'</td><td><input class="flat" type="text" name="type_operation" value="'.$object->type_operation.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_chq").'</td><td><input class="flat" type="text" name="nro_chq" value="'.$object->nro_chq.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconcept").'</td><td><input class="flat" type="text" name="concept" value="'.$object->concept.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.$object->detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit_company").'</td><td><input class="flat" type="text" name="nit_company" value="'.$object->nit_company.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcodeqr").'</td><td><input class="flat" type="text" name="codeqr" value="'.$object->codeqr.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_nit").'</td><td><input class="flat" type="text" name="fourn_nit" value="'.$object->fourn_nit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_soc").'</td><td><input class="flat" type="text" name="fourn_soc" value="'.$object->fourn_soc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_facture").'</td><td><input class="flat" type="text" name="fourn_facture" value="'.$object->fourn_facture.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_numaut").'</td><td><input class="flat" type="text" name="fourn_numaut" value="'.$object->fourn_numaut.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_amount_ttc").'</td><td><input class="flat" type="text" name="fourn_amount_ttc" value="'.$object->fourn_amount_ttc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_amount").'</td><td><input class="flat" type="text" name="fourn_amount" value="'.$object->fourn_amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_codecont").'</td><td><input class="flat" type="text" name="fourn_codecont" value="'.$object->fourn_codecont.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg1").'</td><td><input class="flat" type="text" name="fourn_reg1" value="'.$object->fourn_reg1.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg2").'</td><td><input class="flat" type="text" name="fourn_reg2" value="'.$object->fourn_reg2.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg3").'</td><td><input class="flat" type="text" name="fourn_reg3" value="'.$object->fourn_reg3.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg4").'</td><td><input class="flat" type="text" name="fourn_reg4" value="'.$object->fourn_reg4.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg5").'</td><td><input class="flat" type="text" name="fourn_reg5" value="'.$object->fourn_reg5.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_approved").'</td><td><input class="flat" type="text" name="fk_user_approved" value="'.$object->fk_user_approved.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_request_cash").'</td><td>$object->fk_request_cash</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_request_cash_dest").'</td><td>$object->fk_request_cash_dest</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_dest").'</td><td>$object->fk_projet_dest</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task_dest").'</td><td>$object->fk_projet_task_dest</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_from").'</td><td>$object->fk_account_from</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_dest").'</td><td>$object->fk_account_dest</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldurl_id").'</td><td>$object->url_id</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bank").'</td><td>$object->fk_bank</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_commande_fourn").'</td><td>$object->fk_commande_fourn</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture_fourn").'</td><td>$object->fk_facture_fourn</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td>$object->fk_entrepot</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_from").'</td><td>$object->fk_user_from</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_to").'</td><td>$object->fk_user_to</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td>$object->fk_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td>$object->fk_categorie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td>$object->fk_soc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent_app").'</td><td>$object->fk_parent_app</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td>$object->quant</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>$object->fk_unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_facture").'</td><td>$object->code_facture</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_type_purchase").'</td><td>$object->code_type_purchase</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_operation").'</td><td>$object->type_operation</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_chq").'</td><td>$object->nro_chq</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconcept").'</td><td>$object->concept</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td>$object->detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit_company").'</td><td>$object->nit_company</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcodeqr").'</td><td>$object->codeqr</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_nit").'</td><td>$object->fourn_nit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_soc").'</td><td>$object->fourn_soc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_facture").'</td><td>$object->fourn_facture</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_numaut").'</td><td>$object->fourn_numaut</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_amount_ttc").'</td><td>$object->fourn_amount_ttc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_amount").'</td><td>$object->fourn_amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_codecont").'</td><td>$object->fourn_codecont</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg1").'</td><td>$object->fourn_reg1</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg2").'</td><td>$object->fourn_reg2</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg3").'</td><td>$object->fourn_reg3</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg4").'</td><td>$object->fourn_reg4</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfourn_reg5").'</td><td>$object->fourn_reg5</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_approved").'</td><td>$object->fk_user_approved</td></tr>';
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
		if ($user->rights->finint->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->finint->delete)
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
