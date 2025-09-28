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
 *   	\file       fiscal/ctypetva_card.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-30 08:54
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
dol_include_once('/fiscal/class/ctypetva.class.php');
dol_include_once('/fiscal/class/ctypetvadet.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr			= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_code=GETPOST('search_code','alpha');
$search_label=GETPOST('search_label','alpha');
$search_active=GETPOST('search_active','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Ctypetva($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objectdet=new Ctypetvadet($db);
if ($idr > 0  && $action != 'add')
{
	$result=$objectdet->fetch($idr);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array

$hookmanager->initHooks(array('ctypetvadet'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/ctypetva_card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/ctypetva_card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objectdet->fk_type_tva=$id;
		$objectdet->ref=GETPOST('ref','alpha');
		$objectdet->label=GETPOST('label','alpha');
		$objectdet->type=GETPOST('type','int');
		$objectdet->fk_user_create=$user->id;
		$objectdet->fk_user_mod=$user->id;
		$objectdet->datec = dol_now();
		$objectdet->datem = dol_now();
		$objectdet->tms = dol_now();
		$objectdet->status=1;



		if (empty($objectdet->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objectdet->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/ctypetva_card.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else  setEventMessages($objectdet->error, null, 'errors');
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
		//verificamos si esta cambiando a type = 1
		$db->begin();
		$objectdet->ref=GETPOST('ref','alpha');
		$objectdet->label=GETPOST('label','alpha');
		$objectdet->type=GETPOST('type','int');
		$objectdet->fk_user_mod=$user->id;
		$objectdet->datem = dol_now();
		$objectdet->tms = dol_now();
		$objectdet->status=GETPOST('status','int');;

		if ($objectdet->type)
		{
			$objecttmp = new Ctypetvadet($db);
			$filter = " AND t.fk_type_tva = ".$id;
			$filter.= " AND t.rowid != ".$idr;
			$res = $objecttmp->fetchAll('','',0,0,array('status'=>1,'type'=>1),'AND',$filter,true);
			if ($res==1)
			{
				//actualizamos
				$objecttmp->type = 0;
				$objecttmp->fk_user_mod = $user->id;
				$objecttmp->datem = dol_now();
				$resup = $objecttmp->update($user);
				if ($resup <=0)
				{
					// Creation KO
					$error++;
					if (! empty($objecttmp->errors)) setEventMessages(null, $objecttmp->errors, 'errors');
					else setEventMessages($objecttmp->error, null, 'errors');
					$action='edit';
				}

			}
		}
		if (empty($objectdet->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objectdet->update($user);
			if ($result > 0)
			{
				$db->commit();
				$action='view';
			}
			else
			{
				$db->rollback();
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else setEventMessages($objectdet->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$db->rollback();
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objectdet->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/fiscal/ctypetva_card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
			else setEventMessages($objectdet->error, null, 'errors');
		}
	}
}





/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Fiscal','');

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

// Part to show record
if ($id>0)
{
	print load_fiche_titre($langs->trans("Typevat"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode").'</td><td>'.$object->code.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>'.($object->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td></tr>';

	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/fiscal/typevat/list.php">'.$langs->trans("Return").'</a></div>'."\n";
	print '</div>'."\n";


	include_once DOL_DOCUMENT_ROOT.'/fiscal/tpl/ctypetvadet_list.tpl.php';
	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
