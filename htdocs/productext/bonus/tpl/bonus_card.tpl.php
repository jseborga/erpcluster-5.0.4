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
 *   	\file       productext/bonus_card.php
 *		\ingroup    productext
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-03-20 18:45
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

$search_fk_product_bonus=GETPOST('search_fk_product_bonus','int');
$search_qty_ini=GETPOST('search_qty_ini','alpha');
$search_qty_fin=GETPOST('search_qty_fin','alpha');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'productext', $id);



// Load object
//include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array




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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objBonus->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objBonus->fk_product_bonus=GETPOST('fk_product_bonus','int');
		$objBonus->qty_ini=GETPOST('qty_ini','alpha');
		$objBonus->qty_fin=GETPOST('qty_fin','alpha');
		$objBonus->active=GETPOST('active','int');
		$objBonus->fk_user_create=GETPOST('fk_user_create','int');
		$objBonus->fk_user_mod=GETPOST('fk_user_mod','int');
		$objBonus->status=GETPOST('status','int');



		if (empty($objBonus->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBonus->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objBonus->errors)) setEventMessages(null, $objBonus->errors, 'errors');
				else  setEventMessages($objBonus->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$objBonus->fk_product_bonus=GETPOST('fk_product_bonus','int');
		$objBonus->qty_ini=GETPOST('qty_ini','alpha');
		$objBonus->qty_fin=GETPOST('qty_fin','alpha');
		$objBonus->active=GETPOST('active','int');
		$objBonus->fk_user_create=GETPOST('fk_user_create','int');
		$objBonus->fk_user_mod=GETPOST('fk_user_mod','int');
		$objBonus->status=GETPOST('status','int');



		if (empty($objBonus->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBonus->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objBonus->errors)) setEventMessages(null, $objBonus->errors, 'errors');
				else setEventMessages($objBonus->error, null, 'errors');
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
		$result=$objBonus->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/productext/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objBonus->errors)) setEventMessages(null, $objBonus->errors, 'errors');
			else setEventMessages($objBonus->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_bonus").'</td><td><input class="flat" type="text" name="fk_product_bonus" value="'.GETPOST('fk_product_bonus').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty_ini").'</td><td><input class="flat" type="text" name="qty_ini" value="'.GETPOST('qty_ini').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty_fin").'</td><td><input class="flat" type="text" name="qty_fin" value="'.GETPOST('qty_fin').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.GETPOST('active').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
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
	print '<input type="hidden" name="id" value="'.$objBonus->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_bonus").'</td><td><input class="flat" type="text" name="fk_product_bonus" value="'.$objBonus->fk_product_bonus.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty_ini").'</td><td><input class="flat" type="text" name="qty_ini" value="'.$objBonus->qty_ini.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty_fin").'</td><td><input class="flat" type="text" name="qty_fin" value="'.$objBonus->qty_fin.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$objBonus->active.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objBonus->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objBonus->fk_user_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objBonus->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objBonus->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objBonus->fetch_optionals($objBonus->id, $extralabels);


	print load_fiche_titre($langs->trans("MyModule"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objBonus->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objBonus->label.'</td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_bonus").'</td><td>'.$objBonus->fk_product_bonus.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty_ini").'</td><td>'.$objBonus->qty_ini.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty_fin").'</td><td>'.$objBonus->qty_fin.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>'.$objBonus->active.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objBonus->fk_user_create.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objBonus->fk_user_mod.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>'.$objBonus->status.'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->productext->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objBonus->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->productext->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objBonus->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('bonus'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


