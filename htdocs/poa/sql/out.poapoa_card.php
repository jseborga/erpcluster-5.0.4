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
 *   	\file       poa/poapoa_card.php
 *		\ingroup    poa
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-05-23 09:19
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
dol_include_once('/poa/class/poapoa.class.php');

// Load traductions files requiredby by page
$langs->load("poa");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_gestion=GETPOST('search_gestion','int');
$search_fk_structure=GETPOST('search_fk_structure','int');
$search_ref=GETPOST('search_ref','alpha');
$search_sigla=GETPOST('search_sigla','alpha');
$search_label=GETPOST('search_label','alpha');
$search_pseudonym=GETPOST('search_pseudonym','alpha');
$search_partida=GETPOST('search_partida','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_classification=GETPOST('search_classification','alpha');
$search_source_verification=GETPOST('search_source_verification','alpha');
$search_unit=GETPOST('search_unit','alpha');
$search_responsible_one=GETPOST('search_responsible_one','alpha');
$search_responsible_two=GETPOST('search_responsible_two','alpha');
$search_responsible=GETPOST('search_responsible','alpha');
$search_m_jan=GETPOST('search_m_jan','alpha');
$search_m_feb=GETPOST('search_m_feb','alpha');
$search_m_mar=GETPOST('search_m_mar','alpha');
$search_m_apr=GETPOST('search_m_apr','alpha');
$search_m_may=GETPOST('search_m_may','alpha');
$search_m_jun=GETPOST('search_m_jun','alpha');
$search_m_jul=GETPOST('search_m_jul','alpha');
$search_m_aug=GETPOST('search_m_aug','alpha');
$search_m_sep=GETPOST('search_m_sep','alpha');
$search_m_oct=GETPOST('search_m_oct','alpha');
$search_m_nov=GETPOST('search_m_nov','alpha');
$search_m_dec=GETPOST('search_m_dec','alpha');
$search_p_jan=GETPOST('search_p_jan','alpha');
$search_p_feb=GETPOST('search_p_feb','alpha');
$search_p_mar=GETPOST('search_p_mar','alpha');
$search_p_apr=GETPOST('search_p_apr','alpha');
$search_p_may=GETPOST('search_p_may','alpha');
$search_p_jun=GETPOST('search_p_jun','alpha');
$search_p_jul=GETPOST('search_p_jul','alpha');
$search_p_aug=GETPOST('search_p_aug','alpha');
$search_p_sep=GETPOST('search_p_sep','alpha');
$search_p_oct=GETPOST('search_p_oct','alpha');
$search_p_nov=GETPOST('search_p_nov','alpha');
$search_p_dec=GETPOST('search_p_dec','alpha');
$search_fk_area=GETPOST('search_fk_area','int');
$search_weighting=GETPOST('search_weighting','alpha');
$search_fk_poa_reformulated=GETPOST('search_fk_poa_reformulated','int');
$search_version=GETPOST('search_version','int');
$search_statut=GETPOST('search_statut','int');
$search_statut_ref=GETPOST('search_statut_ref','int');
$search_active=GETPOST('search_active','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Poapoa($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('poapoa'));
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
		
	$object->entity=GETPOST('entity','int');
	$object->gestion=GETPOST('gestion','int');
	$object->fk_structure=GETPOST('fk_structure','int');
	$object->ref=GETPOST('ref','alpha');
	$object->sigla=GETPOST('sigla','alpha');
	$object->label=GETPOST('label','alpha');
	$object->pseudonym=GETPOST('pseudonym','alpha');
	$object->partida=GETPOST('partida','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->classification=GETPOST('classification','alpha');
	$object->source_verification=GETPOST('source_verification','alpha');
	$object->unit=GETPOST('unit','alpha');
	$object->responsible_one=GETPOST('responsible_one','alpha');
	$object->responsible_two=GETPOST('responsible_two','alpha');
	$object->responsible=GETPOST('responsible','alpha');
	$object->m_jan=GETPOST('m_jan','alpha');
	$object->m_feb=GETPOST('m_feb','alpha');
	$object->m_mar=GETPOST('m_mar','alpha');
	$object->m_apr=GETPOST('m_apr','alpha');
	$object->m_may=GETPOST('m_may','alpha');
	$object->m_jun=GETPOST('m_jun','alpha');
	$object->m_jul=GETPOST('m_jul','alpha');
	$object->m_aug=GETPOST('m_aug','alpha');
	$object->m_sep=GETPOST('m_sep','alpha');
	$object->m_oct=GETPOST('m_oct','alpha');
	$object->m_nov=GETPOST('m_nov','alpha');
	$object->m_dec=GETPOST('m_dec','alpha');
	$object->p_jan=GETPOST('p_jan','alpha');
	$object->p_feb=GETPOST('p_feb','alpha');
	$object->p_mar=GETPOST('p_mar','alpha');
	$object->p_apr=GETPOST('p_apr','alpha');
	$object->p_may=GETPOST('p_may','alpha');
	$object->p_jun=GETPOST('p_jun','alpha');
	$object->p_jul=GETPOST('p_jul','alpha');
	$object->p_aug=GETPOST('p_aug','alpha');
	$object->p_sep=GETPOST('p_sep','alpha');
	$object->p_oct=GETPOST('p_oct','alpha');
	$object->p_nov=GETPOST('p_nov','alpha');
	$object->p_dec=GETPOST('p_dec','alpha');
	$object->fk_area=GETPOST('fk_area','int');
	$object->weighting=GETPOST('weighting','alpha');
	$object->fk_poa_reformulated=GETPOST('fk_poa_reformulated','int');
	$object->version=GETPOST('version','int');
	$object->statut=GETPOST('statut','int');
	$object->statut_ref=GETPOST('statut_ref','int');
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

		
	$object->entity=GETPOST('entity','int');
	$object->gestion=GETPOST('gestion','int');
	$object->fk_structure=GETPOST('fk_structure','int');
	$object->ref=GETPOST('ref','alpha');
	$object->sigla=GETPOST('sigla','alpha');
	$object->label=GETPOST('label','alpha');
	$object->pseudonym=GETPOST('pseudonym','alpha');
	$object->partida=GETPOST('partida','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->classification=GETPOST('classification','alpha');
	$object->source_verification=GETPOST('source_verification','alpha');
	$object->unit=GETPOST('unit','alpha');
	$object->responsible_one=GETPOST('responsible_one','alpha');
	$object->responsible_two=GETPOST('responsible_two','alpha');
	$object->responsible=GETPOST('responsible','alpha');
	$object->m_jan=GETPOST('m_jan','alpha');
	$object->m_feb=GETPOST('m_feb','alpha');
	$object->m_mar=GETPOST('m_mar','alpha');
	$object->m_apr=GETPOST('m_apr','alpha');
	$object->m_may=GETPOST('m_may','alpha');
	$object->m_jun=GETPOST('m_jun','alpha');
	$object->m_jul=GETPOST('m_jul','alpha');
	$object->m_aug=GETPOST('m_aug','alpha');
	$object->m_sep=GETPOST('m_sep','alpha');
	$object->m_oct=GETPOST('m_oct','alpha');
	$object->m_nov=GETPOST('m_nov','alpha');
	$object->m_dec=GETPOST('m_dec','alpha');
	$object->p_jan=GETPOST('p_jan','alpha');
	$object->p_feb=GETPOST('p_feb','alpha');
	$object->p_mar=GETPOST('p_mar','alpha');
	$object->p_apr=GETPOST('p_apr','alpha');
	$object->p_may=GETPOST('p_may','alpha');
	$object->p_jun=GETPOST('p_jun','alpha');
	$object->p_jul=GETPOST('p_jul','alpha');
	$object->p_aug=GETPOST('p_aug','alpha');
	$object->p_sep=GETPOST('p_sep','alpha');
	$object->p_oct=GETPOST('p_oct','alpha');
	$object->p_nov=GETPOST('p_nov','alpha');
	$object->p_dec=GETPOST('p_dec','alpha');
	$object->fk_area=GETPOST('fk_area','int');
	$object->weighting=GETPOST('weighting','alpha');
	$object->fk_poa_reformulated=GETPOST('fk_poa_reformulated','int');
	$object->version=GETPOST('version','int');
	$object->statut=GETPOST('statut','int');
	$object->statut_ref=GETPOST('statut_ref','int');
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgestion").'</td><td><input class="flat" type="text" name="gestion" value="'.GETPOST('gestion').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td><input class="flat" type="text" name="fk_structure" value="'.GETPOST('fk_structure').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsigla").'</td><td><input class="flat" type="text" name="sigla" value="'.GETPOST('sigla').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpseudonym").'</td><td><input class="flat" type="text" name="pseudonym" value="'.GETPOST('pseudonym').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td><input class="flat" type="text" name="partida" value="'.GETPOST('partida').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldclassification").'</td><td><input class="flat" type="text" name="classification" value="'.GETPOST('classification').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsource_verification").'</td><td><input class="flat" type="text" name="source_verification" value="'.GETPOST('source_verification').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit").'</td><td><input class="flat" type="text" name="unit" value="'.GETPOST('unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible_one").'</td><td><input class="flat" type="text" name="responsible_one" value="'.GETPOST('responsible_one').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible_two").'</td><td><input class="flat" type="text" name="responsible_two" value="'.GETPOST('responsible_two').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible").'</td><td><input class="flat" type="text" name="responsible" value="'.GETPOST('responsible').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jan").'</td><td><input class="flat" type="text" name="m_jan" value="'.GETPOST('m_jan').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_feb").'</td><td><input class="flat" type="text" name="m_feb" value="'.GETPOST('m_feb').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_mar").'</td><td><input class="flat" type="text" name="m_mar" value="'.GETPOST('m_mar').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_apr").'</td><td><input class="flat" type="text" name="m_apr" value="'.GETPOST('m_apr').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_may").'</td><td><input class="flat" type="text" name="m_may" value="'.GETPOST('m_may').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jun").'</td><td><input class="flat" type="text" name="m_jun" value="'.GETPOST('m_jun').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jul").'</td><td><input class="flat" type="text" name="m_jul" value="'.GETPOST('m_jul').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_aug").'</td><td><input class="flat" type="text" name="m_aug" value="'.GETPOST('m_aug').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_sep").'</td><td><input class="flat" type="text" name="m_sep" value="'.GETPOST('m_sep').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_oct").'</td><td><input class="flat" type="text" name="m_oct" value="'.GETPOST('m_oct').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_nov").'</td><td><input class="flat" type="text" name="m_nov" value="'.GETPOST('m_nov').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_dec").'</td><td><input class="flat" type="text" name="m_dec" value="'.GETPOST('m_dec').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jan").'</td><td><input class="flat" type="text" name="p_jan" value="'.GETPOST('p_jan').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_feb").'</td><td><input class="flat" type="text" name="p_feb" value="'.GETPOST('p_feb').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_mar").'</td><td><input class="flat" type="text" name="p_mar" value="'.GETPOST('p_mar').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_apr").'</td><td><input class="flat" type="text" name="p_apr" value="'.GETPOST('p_apr').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_may").'</td><td><input class="flat" type="text" name="p_may" value="'.GETPOST('p_may').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jun").'</td><td><input class="flat" type="text" name="p_jun" value="'.GETPOST('p_jun').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jul").'</td><td><input class="flat" type="text" name="p_jul" value="'.GETPOST('p_jul').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_aug").'</td><td><input class="flat" type="text" name="p_aug" value="'.GETPOST('p_aug').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_sep").'</td><td><input class="flat" type="text" name="p_sep" value="'.GETPOST('p_sep').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_oct").'</td><td><input class="flat" type="text" name="p_oct" value="'.GETPOST('p_oct').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_nov").'</td><td><input class="flat" type="text" name="p_nov" value="'.GETPOST('p_nov').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_dec").'</td><td><input class="flat" type="text" name="p_dec" value="'.GETPOST('p_dec').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_area").'</td><td><input class="flat" type="text" name="fk_area" value="'.GETPOST('fk_area').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldweighting").'</td><td><input class="flat" type="text" name="weighting" value="'.GETPOST('weighting').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_reformulated").'</td><td><input class="flat" type="text" name="fk_poa_reformulated" value="'.GETPOST('fk_poa_reformulated').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldversion").'</td><td><input class="flat" type="text" name="version" value="'.GETPOST('version').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.GETPOST('statut').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut_ref").'</td><td><input class="flat" type="text" name="statut_ref" value="'.GETPOST('statut_ref').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgestion").'</td><td><input class="flat" type="text" name="gestion" value="'.$object->gestion.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td><input class="flat" type="text" name="fk_structure" value="'.$object->fk_structure.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsigla").'</td><td><input class="flat" type="text" name="sigla" value="'.$object->sigla.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpseudonym").'</td><td><input class="flat" type="text" name="pseudonym" value="'.$object->pseudonym.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td><input class="flat" type="text" name="partida" value="'.$object->partida.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldclassification").'</td><td><input class="flat" type="text" name="classification" value="'.$object->classification.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsource_verification").'</td><td><input class="flat" type="text" name="source_verification" value="'.$object->source_verification.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit").'</td><td><input class="flat" type="text" name="unit" value="'.$object->unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible_one").'</td><td><input class="flat" type="text" name="responsible_one" value="'.$object->responsible_one.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible_two").'</td><td><input class="flat" type="text" name="responsible_two" value="'.$object->responsible_two.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible").'</td><td><input class="flat" type="text" name="responsible" value="'.$object->responsible.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jan").'</td><td><input class="flat" type="text" name="m_jan" value="'.$object->m_jan.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_feb").'</td><td><input class="flat" type="text" name="m_feb" value="'.$object->m_feb.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_mar").'</td><td><input class="flat" type="text" name="m_mar" value="'.$object->m_mar.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_apr").'</td><td><input class="flat" type="text" name="m_apr" value="'.$object->m_apr.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_may").'</td><td><input class="flat" type="text" name="m_may" value="'.$object->m_may.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jun").'</td><td><input class="flat" type="text" name="m_jun" value="'.$object->m_jun.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jul").'</td><td><input class="flat" type="text" name="m_jul" value="'.$object->m_jul.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_aug").'</td><td><input class="flat" type="text" name="m_aug" value="'.$object->m_aug.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_sep").'</td><td><input class="flat" type="text" name="m_sep" value="'.$object->m_sep.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_oct").'</td><td><input class="flat" type="text" name="m_oct" value="'.$object->m_oct.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_nov").'</td><td><input class="flat" type="text" name="m_nov" value="'.$object->m_nov.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_dec").'</td><td><input class="flat" type="text" name="m_dec" value="'.$object->m_dec.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jan").'</td><td><input class="flat" type="text" name="p_jan" value="'.$object->p_jan.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_feb").'</td><td><input class="flat" type="text" name="p_feb" value="'.$object->p_feb.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_mar").'</td><td><input class="flat" type="text" name="p_mar" value="'.$object->p_mar.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_apr").'</td><td><input class="flat" type="text" name="p_apr" value="'.$object->p_apr.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_may").'</td><td><input class="flat" type="text" name="p_may" value="'.$object->p_may.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jun").'</td><td><input class="flat" type="text" name="p_jun" value="'.$object->p_jun.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jul").'</td><td><input class="flat" type="text" name="p_jul" value="'.$object->p_jul.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_aug").'</td><td><input class="flat" type="text" name="p_aug" value="'.$object->p_aug.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_sep").'</td><td><input class="flat" type="text" name="p_sep" value="'.$object->p_sep.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_oct").'</td><td><input class="flat" type="text" name="p_oct" value="'.$object->p_oct.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_nov").'</td><td><input class="flat" type="text" name="p_nov" value="'.$object->p_nov.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_dec").'</td><td><input class="flat" type="text" name="p_dec" value="'.$object->p_dec.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_area").'</td><td><input class="flat" type="text" name="fk_area" value="'.$object->fk_area.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldweighting").'</td><td><input class="flat" type="text" name="weighting" value="'.$object->weighting.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_reformulated").'</td><td><input class="flat" type="text" name="fk_poa_reformulated" value="'.$object->fk_poa_reformulated.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldversion").'</td><td><input class="flat" type="text" name="version" value="'.$object->version.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut_ref").'</td><td><input class="flat" type="text" name="statut_ref" value="'.$object->statut_ref.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>$object->entity</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgestion").'</td><td>$object->gestion</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_structure").'</td><td>$object->fk_structure</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsigla").'</td><td>$object->sigla</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>$object->label</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpseudonym").'</td><td>$object->pseudonym</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpartida").'</td><td>$object->partida</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldclassification").'</td><td>$object->classification</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsource_verification").'</td><td>$object->source_verification</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit").'</td><td>$object->unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible_one").'</td><td>$object->responsible_one</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible_two").'</td><td>$object->responsible_two</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldresponsible").'</td><td>$object->responsible</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jan").'</td><td>$object->m_jan</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_feb").'</td><td>$object->m_feb</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_mar").'</td><td>$object->m_mar</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_apr").'</td><td>$object->m_apr</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_may").'</td><td>$object->m_may</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jun").'</td><td>$object->m_jun</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_jul").'</td><td>$object->m_jul</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_aug").'</td><td>$object->m_aug</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_sep").'</td><td>$object->m_sep</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_oct").'</td><td>$object->m_oct</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_nov").'</td><td>$object->m_nov</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldm_dec").'</td><td>$object->m_dec</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jan").'</td><td>$object->p_jan</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_feb").'</td><td>$object->p_feb</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_mar").'</td><td>$object->p_mar</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_apr").'</td><td>$object->p_apr</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_may").'</td><td>$object->p_may</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jun").'</td><td>$object->p_jun</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_jul").'</td><td>$object->p_jul</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_aug").'</td><td>$object->p_aug</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_sep").'</td><td>$object->p_sep</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_oct").'</td><td>$object->p_oct</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_nov").'</td><td>$object->p_nov</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldp_dec").'</td><td>$object->p_dec</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_area").'</td><td>$object->fk_area</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldweighting").'</td><td>$object->weighting</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_poa_reformulated").'</td><td>$object->fk_poa_reformulated</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldversion").'</td><td>$object->version</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>$object->statut</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut_ref").'</td><td>$object->statut_ref</td></tr>';
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
