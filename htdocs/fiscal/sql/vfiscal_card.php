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
 *   	\file       fiscal/vfiscal_card.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-20 16:05
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
dol_include_once('/fiscal/class/vfiscal.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_nfiscal=GETPOST('search_nfiscal','alpha');
$search_serie=GETPOST('search_serie','alpha');
$search_fk_dosing=GETPOST('search_fk_dosing','int');
$search_fk_facture=GETPOST('search_fk_facture','int');
$search_fk_cliepro=GETPOST('search_fk_cliepro','int');
$search_nit=GETPOST('search_nit','alpha');
$search_razsoc=GETPOST('search_razsoc','alpha');
$search_type_op=GETPOST('search_type_op','alpha');
$search_num_autoriz=GETPOST('search_num_autoriz','alpha');
$search_cod_control=GETPOST('search_cod_control','alpha');
$search_baseimp1=GETPOST('search_baseimp1','alpha');
$search_baseimp2=GETPOST('search_baseimp2','alpha');
$search_baseimp3=GETPOST('search_baseimp3','alpha');
$search_baseimp4=GETPOST('search_baseimp4','alpha');
$search_baseimp5=GETPOST('search_baseimp5','alpha');
$search_aliqimp1=GETPOST('search_aliqimp1','alpha');
$search_aliqimp2=GETPOST('search_aliqimp2','alpha');
$search_aliqimp3=GETPOST('search_aliqimp3','alpha');
$search_aliqimp4=GETPOST('search_aliqimp4','alpha');
$search_aliqimp5=GETPOST('search_aliqimp5','alpha');
$search_valimp1=GETPOST('search_valimp1','alpha');
$search_valimp2=GETPOST('search_valimp2','alpha');
$search_valimp3=GETPOST('search_valimp3','alpha');
$search_valimp4=GETPOST('search_valimp4','alpha');
$search_valimp5=GETPOST('search_valimp5','alpha');
$search_valret1=GETPOST('search_valret1','alpha');
$search_valret2=GETPOST('search_valret2','alpha');
$search_valret3=GETPOST('search_valret3','alpha');
$search_valret4=GETPOST('search_valret4','alpha');
$search_valret5=GETPOST('search_valret5','alpha');
$search_amount_payment=GETPOST('search_amount_payment','alpha');
$search_amount_balance=GETPOST('search_amount_balance','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_statut_print=GETPOST('search_statut_print','int');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Vfiscal($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('vfiscal'));
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
		
	$object->entity=GETPOST('entity','int');
	$object->nfiscal=GETPOST('nfiscal','alpha');
	$object->serie=GETPOST('serie','alpha');
	$object->fk_dosing=GETPOST('fk_dosing','int');
	$object->fk_facture=GETPOST('fk_facture','int');
	$object->fk_cliepro=GETPOST('fk_cliepro','int');
	$object->nit=GETPOST('nit','alpha');
	$object->razsoc=GETPOST('razsoc','alpha');
	$object->type_op=GETPOST('type_op','alpha');
	$object->num_autoriz=GETPOST('num_autoriz','alpha');
	$object->cod_control=GETPOST('cod_control','alpha');
	$object->baseimp1=GETPOST('baseimp1','alpha');
	$object->baseimp2=GETPOST('baseimp2','alpha');
	$object->baseimp3=GETPOST('baseimp3','alpha');
	$object->baseimp4=GETPOST('baseimp4','alpha');
	$object->baseimp5=GETPOST('baseimp5','alpha');
	$object->aliqimp1=GETPOST('aliqimp1','alpha');
	$object->aliqimp2=GETPOST('aliqimp2','alpha');
	$object->aliqimp3=GETPOST('aliqimp3','alpha');
	$object->aliqimp4=GETPOST('aliqimp4','alpha');
	$object->aliqimp5=GETPOST('aliqimp5','alpha');
	$object->valimp1=GETPOST('valimp1','alpha');
	$object->valimp2=GETPOST('valimp2','alpha');
	$object->valimp3=GETPOST('valimp3','alpha');
	$object->valimp4=GETPOST('valimp4','alpha');
	$object->valimp5=GETPOST('valimp5','alpha');
	$object->valret1=GETPOST('valret1','alpha');
	$object->valret2=GETPOST('valret2','alpha');
	$object->valret3=GETPOST('valret3','alpha');
	$object->valret4=GETPOST('valret4','alpha');
	$object->valret5=GETPOST('valret5','alpha');
	$object->amount_payment=GETPOST('amount_payment','alpha');
	$object->amount_balance=GETPOST('amount_balance','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->statut_print=GETPOST('statut_print','int');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/list.php',1);
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
	$object->nfiscal=GETPOST('nfiscal','alpha');
	$object->serie=GETPOST('serie','alpha');
	$object->fk_dosing=GETPOST('fk_dosing','int');
	$object->fk_facture=GETPOST('fk_facture','int');
	$object->fk_cliepro=GETPOST('fk_cliepro','int');
	$object->nit=GETPOST('nit','alpha');
	$object->razsoc=GETPOST('razsoc','alpha');
	$object->type_op=GETPOST('type_op','alpha');
	$object->num_autoriz=GETPOST('num_autoriz','alpha');
	$object->cod_control=GETPOST('cod_control','alpha');
	$object->baseimp1=GETPOST('baseimp1','alpha');
	$object->baseimp2=GETPOST('baseimp2','alpha');
	$object->baseimp3=GETPOST('baseimp3','alpha');
	$object->baseimp4=GETPOST('baseimp4','alpha');
	$object->baseimp5=GETPOST('baseimp5','alpha');
	$object->aliqimp1=GETPOST('aliqimp1','alpha');
	$object->aliqimp2=GETPOST('aliqimp2','alpha');
	$object->aliqimp3=GETPOST('aliqimp3','alpha');
	$object->aliqimp4=GETPOST('aliqimp4','alpha');
	$object->aliqimp5=GETPOST('aliqimp5','alpha');
	$object->valimp1=GETPOST('valimp1','alpha');
	$object->valimp2=GETPOST('valimp2','alpha');
	$object->valimp3=GETPOST('valimp3','alpha');
	$object->valimp4=GETPOST('valimp4','alpha');
	$object->valimp5=GETPOST('valimp5','alpha');
	$object->valret1=GETPOST('valret1','alpha');
	$object->valret2=GETPOST('valret2','alpha');
	$object->valret3=GETPOST('valret3','alpha');
	$object->valret4=GETPOST('valret4','alpha');
	$object->valret5=GETPOST('valret5','alpha');
	$object->amount_payment=GETPOST('amount_payment','alpha');
	$object->amount_balance=GETPOST('amount_balance','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->statut_print=GETPOST('statut_print','int');
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
			header("Location: ".dol_buildpath('/fiscal/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnfiscal").'</td><td><input class="flat" type="text" name="nfiscal" value="'.GETPOST('nfiscal').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldserie").'</td><td><input class="flat" type="text" name="serie" value="'.GETPOST('serie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_dosing").'</td><td><input class="flat" type="text" name="fk_dosing" value="'.GETPOST('fk_dosing').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td><input class="flat" type="text" name="fk_facture" value="'.GETPOST('fk_facture').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cliepro").'</td><td><input class="flat" type="text" name="fk_cliepro" value="'.GETPOST('fk_cliepro').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.GETPOST('nit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrazsoc").'</td><td><input class="flat" type="text" name="razsoc" value="'.GETPOST('razsoc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_op").'</td><td><input class="flat" type="text" name="type_op" value="'.GETPOST('type_op').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td><input class="flat" type="text" name="num_autoriz" value="'.GETPOST('num_autoriz').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td><input class="flat" type="text" name="cod_control" value="'.GETPOST('cod_control').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp1").'</td><td><input class="flat" type="text" name="baseimp1" value="'.GETPOST('baseimp1').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp2").'</td><td><input class="flat" type="text" name="baseimp2" value="'.GETPOST('baseimp2').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp3").'</td><td><input class="flat" type="text" name="baseimp3" value="'.GETPOST('baseimp3').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp4").'</td><td><input class="flat" type="text" name="baseimp4" value="'.GETPOST('baseimp4').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp5").'</td><td><input class="flat" type="text" name="baseimp5" value="'.GETPOST('baseimp5').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp1").'</td><td><input class="flat" type="text" name="aliqimp1" value="'.GETPOST('aliqimp1').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp2").'</td><td><input class="flat" type="text" name="aliqimp2" value="'.GETPOST('aliqimp2').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp3").'</td><td><input class="flat" type="text" name="aliqimp3" value="'.GETPOST('aliqimp3').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp4").'</td><td><input class="flat" type="text" name="aliqimp4" value="'.GETPOST('aliqimp4').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp5").'</td><td><input class="flat" type="text" name="aliqimp5" value="'.GETPOST('aliqimp5').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp1").'</td><td><input class="flat" type="text" name="valimp1" value="'.GETPOST('valimp1').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp2").'</td><td><input class="flat" type="text" name="valimp2" value="'.GETPOST('valimp2').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp3").'</td><td><input class="flat" type="text" name="valimp3" value="'.GETPOST('valimp3').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp4").'</td><td><input class="flat" type="text" name="valimp4" value="'.GETPOST('valimp4').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp5").'</td><td><input class="flat" type="text" name="valimp5" value="'.GETPOST('valimp5').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret1").'</td><td><input class="flat" type="text" name="valret1" value="'.GETPOST('valret1').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret2").'</td><td><input class="flat" type="text" name="valret2" value="'.GETPOST('valret2').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret3").'</td><td><input class="flat" type="text" name="valret3" value="'.GETPOST('valret3').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret4").'</td><td><input class="flat" type="text" name="valret4" value="'.GETPOST('valret4').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret5").'</td><td><input class="flat" type="text" name="valret5" value="'.GETPOST('valret5').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_payment").'</td><td><input class="flat" type="text" name="amount_payment" value="'.GETPOST('amount_payment').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_balance").'</td><td><input class="flat" type="text" name="amount_balance" value="'.GETPOST('amount_balance').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut_print").'</td><td><input class="flat" type="text" name="statut_print" value="'.GETPOST('statut_print').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnfiscal").'</td><td><input class="flat" type="text" name="nfiscal" value="'.$object->nfiscal.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldserie").'</td><td><input class="flat" type="text" name="serie" value="'.$object->serie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_dosing").'</td><td><input class="flat" type="text" name="fk_dosing" value="'.$object->fk_dosing.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td><input class="flat" type="text" name="fk_facture" value="'.$object->fk_facture.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cliepro").'</td><td><input class="flat" type="text" name="fk_cliepro" value="'.$object->fk_cliepro.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.$object->nit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrazsoc").'</td><td><input class="flat" type="text" name="razsoc" value="'.$object->razsoc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_op").'</td><td><input class="flat" type="text" name="type_op" value="'.$object->type_op.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td><input class="flat" type="text" name="num_autoriz" value="'.$object->num_autoriz.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td><input class="flat" type="text" name="cod_control" value="'.$object->cod_control.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp1").'</td><td><input class="flat" type="text" name="baseimp1" value="'.$object->baseimp1.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp2").'</td><td><input class="flat" type="text" name="baseimp2" value="'.$object->baseimp2.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp3").'</td><td><input class="flat" type="text" name="baseimp3" value="'.$object->baseimp3.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp4").'</td><td><input class="flat" type="text" name="baseimp4" value="'.$object->baseimp4.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp5").'</td><td><input class="flat" type="text" name="baseimp5" value="'.$object->baseimp5.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp1").'</td><td><input class="flat" type="text" name="aliqimp1" value="'.$object->aliqimp1.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp2").'</td><td><input class="flat" type="text" name="aliqimp2" value="'.$object->aliqimp2.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp3").'</td><td><input class="flat" type="text" name="aliqimp3" value="'.$object->aliqimp3.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp4").'</td><td><input class="flat" type="text" name="aliqimp4" value="'.$object->aliqimp4.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp5").'</td><td><input class="flat" type="text" name="aliqimp5" value="'.$object->aliqimp5.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp1").'</td><td><input class="flat" type="text" name="valimp1" value="'.$object->valimp1.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp2").'</td><td><input class="flat" type="text" name="valimp2" value="'.$object->valimp2.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp3").'</td><td><input class="flat" type="text" name="valimp3" value="'.$object->valimp3.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp4").'</td><td><input class="flat" type="text" name="valimp4" value="'.$object->valimp4.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp5").'</td><td><input class="flat" type="text" name="valimp5" value="'.$object->valimp5.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret1").'</td><td><input class="flat" type="text" name="valret1" value="'.$object->valret1.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret2").'</td><td><input class="flat" type="text" name="valret2" value="'.$object->valret2.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret3").'</td><td><input class="flat" type="text" name="valret3" value="'.$object->valret3.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret4").'</td><td><input class="flat" type="text" name="valret4" value="'.$object->valret4.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret5").'</td><td><input class="flat" type="text" name="valret5" value="'.$object->valret5.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_payment").'</td><td><input class="flat" type="text" name="amount_payment" value="'.$object->amount_payment.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_balance").'</td><td><input class="flat" type="text" name="amount_balance" value="'.$object->amount_balance.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut_print").'</td><td><input class="flat" type="text" name="statut_print" value="'.$object->statut_print.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnfiscal").'</td><td>$object->nfiscal</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldserie").'</td><td>$object->serie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_dosing").'</td><td>$object->fk_dosing</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td>$object->fk_facture</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cliepro").'</td><td>$object->fk_cliepro</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td>$object->nit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrazsoc").'</td><td>$object->razsoc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_op").'</td><td>$object->type_op</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td>$object->num_autoriz</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td>$object->cod_control</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp1").'</td><td>$object->baseimp1</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp2").'</td><td>$object->baseimp2</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp3").'</td><td>$object->baseimp3</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp4").'</td><td>$object->baseimp4</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbaseimp5").'</td><td>$object->baseimp5</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp1").'</td><td>$object->aliqimp1</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp2").'</td><td>$object->aliqimp2</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp3").'</td><td>$object->aliqimp3</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp4").'</td><td>$object->aliqimp4</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaliqimp5").'</td><td>$object->aliqimp5</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp1").'</td><td>$object->valimp1</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp2").'</td><td>$object->valimp2</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp3").'</td><td>$object->valimp3</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp4").'</td><td>$object->valimp4</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalimp5").'</td><td>$object->valimp5</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret1").'</td><td>$object->valret1</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret2").'</td><td>$object->valret2</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret3").'</td><td>$object->valret3</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret4").'</td><td>$object->valret4</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalret5").'</td><td>$object->valret5</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_payment").'</td><td>$object->amount_payment</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_balance").'</td><td>$object->amount_balance</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut_print").'</td><td>$object->statut_print</td></tr>';
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
		if ($user->rights->fiscal->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->fiscal->delete)
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
