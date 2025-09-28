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
 *   	\file       assets/assetsdoc_card.php
 *		\ingroup    assets
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-10-31 16:10
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

$idr = GETPOST('idr');
if (empty($dater)) $dater = dol_now();
$search_fk_asset=GETPOST('search_fk_asset','int');
$search_fk_cassetdoc=GETPOST('search_fk_cassetdoc','int');
$search_label=GETPOST('search_label','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'assets', $id);


// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objAssetsdoc->table_element);

// Load object
//include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assetsdoc'));
if ($idr>0)
{
	$res = $objAssetsdoc->fetch($idr);
}
//recuperamos la lista de documentos para activo
$rescdoc = $objCassetsdoc->fetchAll('ASC','label',0,0,array(1=>1),'AND'," AND t.status = 1");
$options = '';
if ($rescdoc>0)
{
	$lines = $objCassetsdoc->lines;
	foreach ($lines AS $j => $line)
	{
		$options.= '<option value="'.$line->id.'">'.$line->label.'</option>';
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

//llxHeader('','MyPageName','');

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
if ($action == 'createdoc')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="adddoc">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset").'</td><td><input class="flat" type="text" name="fk_asset" value="'.GETPOST('fk_asset').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate").'</td><td>';
	print $form->select_date($dater,'dr_',0,0,1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cassetdoc").'</td><td>';
	print '<select name="fk_cassetdoc">'.$options.'</select>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>';
	print '<textarea name="label" id="label" class="quatrevingtpercent" rows="'._ROWS_2.'" wrap="soft">'.GETPOST('label').'</textarea>';
	print '</td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($idr) && $action == 'editdoc')
{
	print load_fiche_titre($langs->trans("Assetsdoc"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="updatedoc">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idr" value="'.$objAssetsdoc->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset").'</td><td><input class="flat" type="text" name="fk_asset" value="'.$objAssetsdoc->fk_asset.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate").'</td><td>';
	print $form->select_date($dater,'dr_',0,0,1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cassetdoc").'</td><td>';
	print '<select name="fk_cassetdoc">'.$options.'</select>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>';
	print '<textarea name="label" id="label" class="quatrevingtpercent" rows="'._ROWS_2.'" wrap="soft">'.(GETPOST('label')?GETPOST('label'):$objAssetsdoc->label).'</textarea>';
	print '</td></tr>';
	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objAssetsdoc->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objAssetsdoc->fetch_optionals($objAssetsdoc->id, $extralabels);


	//print load_fiche_titre($langs->trans("Assestsdoc"));

	dol_fiche_head();

	if ($action == 'deletedoc') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objAssetsdoc->id, $langs->trans('DeleteDocument'), $langs->trans('ConfirmDeletedocument'), 'confirm_deletedoc', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objAssetsdoc->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset").'</td><td>'.$objAssetsdoc->fk_asset.'</td></tr>';
	$objCassetsdoc->fetch($objAssetsdoc->id);
	print '<tr><td >'.$langs->trans("Fieldfk_cassetdoc").'</td><td>'.$objCassetsdoc->label.'</td></tr>';
	print '<tr><td >'.$langs->trans("Fieldlabel").'</td><td>'.$objAssetsdoc->label.'</td></tr>';
	$objUser->fetch($objAssetsdoc->fk_user_create);
	print '<tr><td >'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($objAssetsdoc->fk_user_mod);
	print '<tr><td >'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	//print '<tr><td >'.$langs->trans("Fieldstatus").'</td><td>'.$objAssetsdoc->getLibStatut(3).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objAssetsdoc,$action);    // Note that $action and $objAssetsdoc may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assets->doc->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objAssetsdoc->id.'&amp;action=editdoc">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->assets->doc->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objAssetsdoc->id.'&amp;action=deletedoc">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($objAssetsdoc, null, array('assetsdoc'));
	//$somethingshown = $form->showLinkedObjectBlock($objAssetsdoc, $linktoelem);

	//agregamos documentos
	include DOL_DOCUMENT_ROOT.'/assets/assets/tpl/document.tpl.php';
}


