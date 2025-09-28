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
 *   	\file       mant/mgroupsprogram_card.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-24 16:32
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
// Load traductions files requiredby by page
$langs->load("mant");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_group=GETPOST('search_fk_group','int');
$search_fk_parent_previous=GETPOST('search_fk_parent_previous','int');
$search_fk_type_repair=GETPOST('search_fk_type_repair','int');
$search_type=GETPOST('search_type','alpha');
$search_accountant=GETPOST('search_accountant','int');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'mant', $id);


$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabelsp = $extrafields->fetch_name_optionals_label($objectp->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('mgroupsprogram'));



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

//llxHeader('','MyPageName','');

$form=new Form($db);


$objtyperepair = new Mtyperepair($db);

//obtenemos los registrados para fk_parent_previous
$objectptmp = new Mgroupsprogram($db);
$objtyperepairtmp = new Mtyperepair($db);
$filterstatic = " AND t.fk_group = ".$object->id;
$objectptmp->fetchAll('ASC','t.fk_type_repair', 0, 0, array(1=>1),'AND',$filterstatic);
$aTypeRepair = array();
$aMEPTypeRepair = array();
foreach ((array) $objectptmp->lines AS $j => $line)
{
	$aTypeRepair[$line->fk_type_repair] = $line->fk_type_repair;
	$res = $objtyperepairtmp->fetch($line->fk_type_repair);
	//if ($res>0)
	$aMEPTypeRepair[$line->id] = $objtyperepairtmp->ref.' - '.$objtyperepairtmp->label;
}
$filterstatic = '';
$objtyperepairtmp->fetchAll('ASC','t.ref', 0, 0, array('active'=>1),'AND',$filterstatic);

$options = '<option value="0"></option>';
$lines = $objtyperepairtmp->lines;
foreach ((array) $lines AS $j => $line)
{
	if (!$aTypeRepair[$line->id])
	{
		$selected = '';
		if (GETPOST('fk_type_repair') == $line->id)
		{
			$selected = ' selected';
			$_POST['type'] = $line->type;
			$_POST['accountant'] = $line->accountant;
		}
		$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label.'</option>';
	}
}

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


	//print load_fiche_titre($langs->trans("NewMyModule"));

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#fk_type_repair").change(function() {
			document.Form.action.value="create";
			document.Form.submit();
		});
	});';
	print '</script>'."\n";

	print '<form name="Form" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";

	print '<tr><td class="fieldrequired">'.$langs->trans("Typerepair").'</td><td>';
	print '<select id="fk_type_repair" name="fk_type_repair">'.$options.'</select>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Typerepairprevious").'</td><td>';
	print $form->selectarray('fk_parent_previous',$aMEPTypeRepair,GETPOST('fk_parent_previous'),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>';
	print $form->selectarray('type',$aType,GETPOST('type'),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccountant").'</td><td><input class="flat" type="number" min="0" name="accountant" value="'.GETPOST('accountant').'" required></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',GETPOST('active'),1);
	print '</td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';
	print '</form>';
}



// Part to edit record
if ($idr && $action == 'edit')
{
	$fk_type_repair = GETPOST('fk_type_repair')?GETPOST('fk_type_repair'):$objectp->fk_type_repair;
	$options = '<option value="0"></option>';
	foreach ((array) $objtyperepairtmp->lines AS $j => $line)
	{
		$selected = '';
		if ($line->id == $fk_type_repair) $selected = ' selected';
		$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label.'</option>';
	}

	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Typerepair").'</td><td>';
	print '<select id="fk_type_repair" name="fk_type_repair">'.$options.'</select>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Typerepairprevious").'</td><td>';
	print $form->selectarray('fk_parent_previous',$aMEPTypeRepair,$objectp->fk_parent_previous,1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>';
	print $form->selectarray('type',$aType,$objectp->type,1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccountant").'</td><td><input class="flat" type="number" min="0" name="accountant" value="'.$objectp->accountant.'" required></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$objectp->description.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',$objectp->active,1);
	print '</td></tr>';
	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}


