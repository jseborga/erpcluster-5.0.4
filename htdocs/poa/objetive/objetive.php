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
 *   	\file       poa/poaplanstrategic_card.php
 *		\ingroup    poa
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-25 18:15
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');
dol_include_once('/poa/class/poaplanstrategic.class.php');
dol_include_once('/poa/class/poaobjetiveext.class.php');
dol_include_once('/poa/class/cnameobjetive.class.php');

dol_include_once('/orgman/class/pdepartamentext.class.php');
dol_include_once('/poa/lib/poa.lib.php');

// Load traductions files requiredby by page
$langs->load("poa");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');

$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_year_ini=GETPOST('search_year_ini','int');
$search_year_fin=GETPOST('search_year_fin','int');
$search_label=GETPOST('search_label','alpha');
$search_pseudonym=GETPOST('search_pseudonym','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'poa', $id);
//$result = restrictedArea($user, 'Poaplanstrategic', $id, '&societe', '', 'rowid', 'rowid', $objcanvas);

$object = new Poaplanstrategic($db);
$extrafields = new ExtraFields($db);
$objuser = new User($db);
$objcnameobj = new Cnameobjetive($db);

$objobjetive = new Poaobjetive($db);
$departament = new Pdepartamentext($db);

//verificamos el numero de niveles de los objetivos
$filterstatic = "";
$nObjetive = $objcnameobj->fetchAll('ASC', 'code', 0, 0, array('entity'=>$conf->entity, 'active'=>1), 'AND');

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('poaplanstrategic'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/objetive/objetive.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}		
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	//agregamos el abm para poa objetive
	include DOL_DOCUMENT_ROOT.'/poa/objetive/inc/abm_poaobjetive.inc.php';
	
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Managementobjetives','');

$form=new Formv($db);


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

// Part to show record
if ($object->id > 0)
{
	$res = $object->fetch_optionals($object->id, $extralabels);

	$head = planstrategic_prepare_head($object);
	dol_fiche_head($head, 'objetive', $langs->trans("Planstrategic"), 0, 'poa_plan');

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	// 
	print '<tr><td>'.$langs->trans("Fieldrefpoa").'</td><td>'.str_pad($object->ref, $conf->global->POA_CODE_SIZE_PLAN_STRATEGIC, "0", STR_PAD_LEFT).'</td>';
	print '<td>'.$langs->trans("Plan").'</td><td>'.$object->label.'</td></tr>';

	print '<tr><td>'.$langs->trans("Year").'</td><td>'.$object->year_ini.'-'.$object->year_fin.'</td>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpseudonym").'</td><td>'.$object->pseudonym.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>';
	//$objuser->fetch($object->fk_user_create);
	//print $objuser->getNomUrl(1);
	//print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>';
	//$objuser->fetch($object->fk_user_mod);
	//print $objuser->getNomUrl(1);
	//print '</td></tr>';

	print '<td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(3).'</td></tr>';

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
	$nObjetive--; 
	include DOL_DOCUMENT_ROOT.'/poa/objetive/tpl/plan_objetive.tpl.php';

}


// End of page
llxFooter();
$db->close();
