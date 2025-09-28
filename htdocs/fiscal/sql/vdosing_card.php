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
 *   	\file       fiscal/vdosing_card.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-27 17:44
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
dol_include_once('/fiscal/class/vdosing.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_subsidiaryid=GETPOST('search_fk_subsidiaryid','int');
$search_series=GETPOST('search_series','alpha');
$search_num_ini=GETPOST('search_num_ini','int');
$search_num_fin=GETPOST('search_num_fin','int');
$search_num_ult=GETPOST('search_num_ult','int');
$search_num_aprob=GETPOST('search_num_aprob','alpha');
$search_type=GETPOST('search_type','int');
$search_active=GETPOST('search_active','int');
$search_num_autoriz=GETPOST('search_num_autoriz','alpha');
$search_cod_control=GETPOST('search_cod_control','alpha');
$search_lote=GETPOST('search_lote','int');
$search_chave=GETPOST('search_chave','alpha');
$search_descrip=GETPOST('search_descrip','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Vdosing($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('vdosing'));
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
	$object->fk_subsidiaryid=GETPOST('fk_subsidiaryid','int');
	$object->series=GETPOST('series','alpha');
	$object->num_ini=GETPOST('num_ini','int');
	$object->num_fin=GETPOST('num_fin','int');
	$object->num_ult=GETPOST('num_ult','int');
	$object->num_aprob=GETPOST('num_aprob','alpha');
	$object->type=GETPOST('type','int');
	$object->active=GETPOST('active','int');
	$object->num_autoriz=GETPOST('num_autoriz','alpha');
	$object->cod_control=GETPOST('cod_control','alpha');
	$object->lote=GETPOST('lote','int');
	$object->chave=GETPOST('chave','alpha');
	$object->descrip=GETPOST('descrip','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
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
	$object->fk_subsidiaryid=GETPOST('fk_subsidiaryid','int');
	$object->series=GETPOST('series','alpha');
	$object->num_ini=GETPOST('num_ini','int');
	$object->num_fin=GETPOST('num_fin','int');
	$object->num_ult=GETPOST('num_ult','int');
	$object->num_aprob=GETPOST('num_aprob','alpha');
	$object->type=GETPOST('type','int');
	$object->active=GETPOST('active','int');
	$object->num_autoriz=GETPOST('num_autoriz','alpha');
	$object->cod_control=GETPOST('cod_control','alpha');
	$object->lote=GETPOST('lote','int');
	$object->chave=GETPOST('chave','alpha');
	$object->descrip=GETPOST('descrip','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_subsidiaryid").'</td><td><input class="flat" type="text" name="fk_subsidiaryid" value="'.GETPOST('fk_subsidiaryid').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldseries").'</td><td><input class="flat" type="text" name="series" value="'.GETPOST('series').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_ini").'</td><td><input class="flat" type="text" name="num_ini" value="'.GETPOST('num_ini').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_fin").'</td><td><input class="flat" type="text" name="num_fin" value="'.GETPOST('num_fin').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_ult").'</td><td><input class="flat" type="text" name="num_ult" value="'.GETPOST('num_ult').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_aprob").'</td><td><input class="flat" type="text" name="num_aprob" value="'.GETPOST('num_aprob').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td><input class="flat" type="text" name="type" value="'.GETPOST('type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.GETPOST('active').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td><input class="flat" type="text" name="num_autoriz" value="'.GETPOST('num_autoriz').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td><input class="flat" type="text" name="cod_control" value="'.GETPOST('cod_control').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlote").'</td><td><input class="flat" type="text" name="lote" value="'.GETPOST('lote').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldchave").'</td><td><input class="flat" type="text" name="chave" value="'.GETPOST('chave').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescrip").'</td><td><input class="flat" type="text" name="descrip" value="'.GETPOST('descrip').'"></td></tr>';
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
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_subsidiaryid").'</td><td><input class="flat" type="text" name="fk_subsidiaryid" value="'.$object->fk_subsidiaryid.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldseries").'</td><td><input class="flat" type="text" name="series" value="'.$object->series.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_ini").'</td><td><input class="flat" type="text" name="num_ini" value="'.$object->num_ini.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_fin").'</td><td><input class="flat" type="text" name="num_fin" value="'.$object->num_fin.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_ult").'</td><td><input class="flat" type="text" name="num_ult" value="'.$object->num_ult.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_aprob").'</td><td><input class="flat" type="text" name="num_aprob" value="'.$object->num_aprob.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td><input class="flat" type="text" name="type" value="'.$object->type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$object->active.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td><input class="flat" type="text" name="num_autoriz" value="'.$object->num_autoriz.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td><input class="flat" type="text" name="cod_control" value="'.$object->cod_control.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlote").'</td><td><input class="flat" type="text" name="lote" value="'.$object->lote.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldchave").'</td><td><input class="flat" type="text" name="chave" value="'.$object->chave.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescrip").'</td><td><input class="flat" type="text" name="descrip" value="'.$object->descrip.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_subsidiaryid").'</td><td>$object->fk_subsidiaryid</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldseries").'</td><td>$object->series</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_ini").'</td><td>$object->num_ini</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_fin").'</td><td>$object->num_fin</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_ult").'</td><td>$object->num_ult</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_aprob").'</td><td>$object->num_aprob</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>$object->type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>$object->active</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td>$object->num_autoriz</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td>$object->cod_control</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlote").'</td><td>$object->lote</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldchave").'</td><td>$object->chave</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescrip").'</td><td>$object->descrip</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
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
