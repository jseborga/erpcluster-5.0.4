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
 *   	\file       wsassist/adherentauth_card.php
 *		\ingroup    wsassist
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-01-23 15:38
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
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
dol_include_once('/wsassist/class/adherentauth.class.php');
dol_include_once('/adherents/class/adherent.class.php');

if ($conf->orgman->enabled)
	dol_include_once('/orgman/class/mproperty.class.php');





// Load traductions files requiredby by page
$langs->load("wsassist");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_adherent=GETPOST('search_fk_adherent','int');
$search_fk_property=GETPOST('search_fk_property','int');
$search_code_mobile=GETPOST('search_code_mobile','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'wsassist', $id);


$object = new Adherentauth($db);
$extrafields = new ExtraFields($db);
$objUser = new User($db);
$objAdherent = new Adherent($db);

if ($conf->orgman->enabled)
	$objMpropery = new Mproperty($db);
//$form=new Formv($db);


// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('adherentauth'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/wsassist/auth/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}



	$new = dol_now();
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/wsassist/auth/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */






		$object->fk_adherent=GETPOST('fk_adherent','int');
		$object->fk_property=GETPOST('fk_property','int');
		$object->code_mobile=GETPOST('code_mobile','alpha');
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->datec=$new;
		$object->datem=$new;
		$object->status=0;

		if (empty($object->fk_adherent))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("fk_adherent")), null, 'errors');
		}

		if (empty($object->fk_property))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("fk_property")), null, 'errors');
		}

		if (empty($object->code_mobile))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("code_mobile")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);

			if ($result > 0)
			{
				// Creation OK


				$urltogo=$backtopage?$backtopage:dol_buildpath('/wsassist/auth/card.php?id='.$result,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
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

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$object->fk_adherent=GETPOST('fk_adherent','int');
		$object->fk_property=GETPOST('fk_property','int');
		$object->code_mobile=GETPOST('code_mobile','alpha');

		/*

		if($action == 'update' && ($id || $ref))
		{
			$object->status=1;

		}
		*/

		//$object->fk_user_mod=$user->id;
		//$object->datem=$new;
		//$object->status=GETPOST('status','int');



		/*

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		*/

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
			header("Location: ".dol_buildpath('/wsassist/auth/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
	// Action to delete
	if ($action == 'confirm_validate')
	{
		$object->status = 1;
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordUpdate", null, 'mesgs');
			header("Location: ".dol_buildpath('/wsassist/auth/card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
		$action = '';
	}
	// Action to delete
	if ($action == 'confirm_novalidate')
	{
		$object->status = 0;
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordUpdate", null, 'mesgs');
			header("Location: ".dol_buildpath('/wsassist/auth/card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
		$action = '';
	}
}

$filter=" AND t.status=1";
$res = $objMpropery->fetchAll('DESC','t.label',1,0,array(),'AND',$filter,true);
$options = '<option value="">'.$langs->trans('Select').'</option>';
if ($res >0)
{
	$lines = $objMpropery->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		$fk_property = (GETPOST('fk_property')?GETPOST('fk_property'):$object->fk_property);
		if ($fk_property==$line->id)
			$selected = ' selected';
		$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label.'</option>';
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

//$form=new Form($db);
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



	print '<tr><td class="fieldrequired">' . $langs->trans('Fieldfk_adherent') . '</td><td colspan="2">';
	print $form->select_member(GETPOST('fk_adherent'),'fk_adherent','',0,0,0,array(),0);
		//$form->select_member('',GETPOST('fk_adherent'),'',0,0,0,array(),0);
	print '</td></tr>';



		//$var=!$var;
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_property").'</td>';
	print '<tr><td class="fieldrequired">' . $langs->trans('Fieldfk_property') . '</td><td colspan="2">';
			//print '<td colspan="2">';
	print '<select name="fk_property">'.$options.'</select>';
	print '</td></tr>';



		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_adherent").'</td><td><input class="flat" type="text" name="fk_adherent" value="'.GETPOST('fk_adherent').'"></td></tr>';



		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_property").'</td><td><input class="flat" type="text" name="fk_property" value="'.GETPOST('fk_property').'"></td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_mobile").'</td><td><input class="flat" type="text" name="code_mobile" value="'.GETPOST('code_mobile').'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

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
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_adherent").'</td><td><input class="flat" type="text" name="fk_adherent" value="'.$object->fk_adherent.'"></td></tr>';
	print '<tr><td class="fieldrequired">' . $langs->trans('Fieldfk_adherent') . '</td><td colspan="2">';
	print $form->select_member($object->fk_adherent,'fk_adherent','',0,0,0,array(),0);
		//$form->select_member('',GETPOST('fk_adherent'),'',0,0,0,array(),0);
	print '</td></tr>';

		//$var=!$var;
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_property").'</td>';
	print '<tr><td class="fieldrequired">' . $langs->trans('Fieldfk_property') . '</td><td colspan="2">';
			//print '<td colspan="2">';
	print '<select name="fk_property">'.$options.'</select>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_mobile").'</td><td><input class="flat" type="text" name="code_mobile" value="'.$object->code_mobile.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';



}






// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals($object->id, $extralabels);


	print load_fiche_titre($langs->trans("MyModule"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validateadherentauth'), $langs->trans('ConfirmValidateadherentauth'), 'confirm_validate', '', 1, 2);
		print $formconfirm;
	}
	if ($action == 'novalidate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Novalidateadherentauth'), $langs->trans('ConfirmNovalidateadherentauth'), 'confirm_novalidate', '', 1, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	$objAdherent->fetch($object->fk_adherent);

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_adherent").'</td><td>'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td></tr>';
	$res = $objMpropery->fetch($object->fk_property);
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_property").'</td><td>'.($res>0?$objMpropery->getNomUrl():'').'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_mobile").'</td><td>'.$object->code_mobile.'</td></tr>';
	$objUser->fetch($object->fk_user_create);
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($object->fk_user_mod);
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(3).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/wsassist/auth/list.php">'.$langs->trans("Return").'</a></div>'."\n";

		if (empty($object->status))
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
		else
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=novalidate">'.$langs->trans("Novalidate").'</a></div>'."\n";


		//if ($user->rights->wsassist->write)
		//{
		if (empty($object->status))
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		//}

		//if ($user->rights->wsassist->delete)
		//{
		if (empty($object->status))
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		//}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('adherentauth'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
