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
 *   	\file       budget/pustructure_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-09-15 10:21
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
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/projet/class/project.class.php');
dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/categories/class/categorie.class.php');
dol_include_once('/budget/class/puformulasext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
require_once DOL_DOCUMENT_ROOT.'/budget/lib/utils.lib.php';

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$idf		= GETPOST('idf','int');
$id			= GETPOST('id','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_categorie=GETPOST('search_fk_categorie','int');
$search_detail=GETPOST('search_detail','alpha');
$search_ordby=GETPOST('search_ordby','int');
$search_ejecution=GETPOST('search_ejecution','int');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
if (!$user->rights->budget->par->read) accessforbidden();

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Pustructureext($db);
$typestr = new Putypestructureext($db);

$projet 	= new Project($db);
$budget 	= new Budgetext($db);
$objuser 	= new User($db);
$categorie 	= new Categorie($db);
$objectdet 	= new Pustructuredetext($db);
$objform	= new Puformulasext($db);


if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,(!empty($ref)?$ref:null));
	if ($result < 0) dol_print_error($db);
	//buscamos el tipo de estructura
	$res = $typestr->fetch(0,$object->type_structure);
	if (trim($typestr->code) == trim($object->type_structure))
		$idf = $typestr->id;
}
if ($idf>0 || GETPOST('type_structure'))
{
	$res = $typestr->fetch($idf,(!empty(GETPOST('type_structure'))?GETPOST('type_structure'):NULL));
	if ($res>0) $idf = $typestr->id;
}
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('pustructure'));
$extrafields = new ExtraFields($db);

$aGroupstr = get_group_structure();

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/typestructure/card.php?id='.$idf,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->group_structure = GETPOST('group_structure');
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->type_structure=GETPOST('type_structure','alpha');
		$object->fk_categorie=GETPOST('fk_categorie','int');
		if ($object->fk_categorie<=0|| empty($object->fk_categorie)) $object->fk_categorie=0;
		$object->detail=GETPOST('detail','alpha');
		$object->ordby=GETPOST('ordby','int');
		$object->complementary=GETPOST('complementary','int');
		$object->ejecution=GETPOST('ejecution','int')+0;
		$object->date_create = $now;
		$object->date_mod = $now;
		$object->tms = $now;
		$object->status=1;



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->group_structure))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldgroup_structure")), null, 'errors');
		}
		if (!empty($object->group_structure) && $object->group_structure != 'OT')
		{
			if ($object->fk_categorie<=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_categorie")), null, 'errors');
			}
		}
		if (empty($object->ordby))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldordby")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/typestructure/card.php?id='.$typestr->id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				setEventMessages($object->error, $object->errors, 'errors');
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

		//$object->entity=GETPOST('entity','int');
		$object->ref=GETPOST('ref','alpha');
		$object->group_structure = GETPOST('group_structure');
		$object->fk_user_mod=$user->id;
		$object->fk_categorie=GETPOST('fk_categorie','int');
		if ($object->fk_categorie<=0|| empty($object->fk_categorie)) $object->fk_categorie=0;
		$object->detail=GETPOST('detail','alpha');
		$object->ordby=GETPOST('ordby','int');
		$object->ejecution=GETPOST('ejecution','int');
		$object->complementary=GETPOST('complementary','int');
		$object->date_mod = $now;
		$object->fk_user_mod = $user->id;
		//$object->statut=GETPOST('statut','int');

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->group_structure))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldgroup_structure")), null, 'errors');
		}
		if (!empty($object->group_structure) && $object->group_structure != 'OT')
		{
			if ($object->fk_categorie<=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_categorie")), null, 'errors');
			}
		}
		if (empty($object->ordby))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldordby")), null, 'errors');
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
		$type = $object->type_structure;
		$typestr->fetch(0,$type);
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/typestructure/card.php?id='.$typestr->id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}

		// Action to add record
	if ($action == 'adddet')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/typestructure/card.php?id='.$idf,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		$objectdet->entity = $object->entity;
		$objectdet->ref_structure = $object->ref;
		$objectdet->type_structure = $object->type_structure;
		$objectdet->sequen=GETPOST('sequen','int');
		$objectdet->formula=GETPOST('formula','alpha');
		$objectdet->detail=GETPOST('detail','alpha');
		$objectdet->status_print=GETPOST('status_print','int');
		$objectdet->status_print_det=GETPOST('status_print_det','int');
		$objectdet->fk_user_create=$user->id;
		$objectdet->fk_user_mod=$user->id;
		$objectdet->date_create=dol_now();
		$objectdet->date_mod=dol_now();
		$objectdet->tms=dol_now();
		$objectdet->status=1;

		if (empty($objectdet->sequen))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Sequen")), null, 'errors');
		}
		if (empty($objectdet->formula))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Formula")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objectdet->create($user);
			if ($result > 0)
			{
				// Creation OK
				setEventMessages($langs->trans('Saverecord'),null,'mesgs');
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/structure/card.php?id='.$object->id,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='createdet';
			}
		}
		else
		{
			$action='createdet';
		}
	}
		// Action to add record
	if ($action == 'updatedet')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/typestructure/card.php?id='.$idf,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		$objectdet->fetch($idr);
		/* object_prop_getpost_prop */
		$objectdet->sequen=GETPOST('sequen','int');
		$objectdet->formula=GETPOST('formula','alpha');
		$objectdet->detail=GETPOST('detail','alpha');
		$objectdet->status_print=GETPOST('status_print','int');
		$objectdet->status_print_det=GETPOST('status_print_det','int');
		$objectdet->fk_user_mod=$user->id;
		$objectdet->date_mod=dol_now();
		$objectdet->tms=dol_now();

		if (empty($objectdet->sequen))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Sequen")), null, 'errors');
		}
		if (empty($objectdet->formula))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Formula")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objectdet->update($user);
			if ($result > 0)
			{
				// Creation OK
				setEventMessages($langs->trans('Saverecord'),null,'mesgs');
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/structure/card.php?id='.$object->id,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='editdet';
			}
		}
		else
		{
			$action='editdet';
		}
	}
		// Action to add record
	if ($action == 'deletedet')
	{
		$error=0;
		$objectdet->fetch($idr);
		$result = $objectdet->delete($user);
		if ($result > 0)
		{
				// Creation OK
			setEventMessages($langs->trans('Deleterecord'),null,'mesgs');
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/structure/card.php?id='.$object->id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
				// Creation KO
			if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
			else  setEventMessages($object->error, null, 'errors');
			$action='';
		}
	}
	if ($action == 'createload')
	{
		$action = 'create';
	}
	if ($action == 'updateload')
	{
		$action = 'edit';
	}

}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Structuretype'),'');

$form=new Form($db);





// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("New"));
	dol_htmloutput_mesg($mesg);

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () { 	$("#group_structure").change(function() { 	document.cardstr.action.value="createload";
	document.cardstr.submit(); 	}); });';
	print '</script>'."\n";
	print '<form id="cardstr" name="cardstr" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="type_structure" value="'.$typestr->code.'">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="idf" value="'.$idf.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'" required></td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup").'</td><td>';
	print $form->selectarray('group_structure',$aGroupstr,GETPOST('group_structure'),1);
	print '</td></tr>';

	if ($aGroupstr[GETPOST('group_structure')] && GETPOST('group_structure') != 'OT')
	{
		print '<tr id="fkcateg"><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td>';
		print $form->select_all_categories('product','',"fk_categorie");
		print '</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input id="detail" class="flat" type="text" name="detail" value="'.$aGroupstr[GETPOST('group_structure')].'" readOnly></td></tr>';
		print '<input id="detail" class="flat" type="hidden" name="detail" value="'.$aGroupstr[GETPOST('group_structure')].'" readOnly>';
	}
	elseif(GETPOST('group_structure') == 'OT')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input id="detail" class="flat" type="text" name="detail" value="'.GETPOST('detail').'" required></td></tr>';
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Visibleincomplementary").'</td><td>'.$form->selectyesno('complementary',$object->complementary,1).'</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldordby").'</td><td><input class="flat" type="text" name="ordby" value="'.GETPOST('ordby').'" required></td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; ';
	print '<a href="'.DOL_URL_ROOT.'/budget/typestructure/card.php?id='.$idf.'" class="button">'.$langs->trans("Cancel").'</a></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Structuretype"));
	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () { 	$("#group_structure").change(function() { document.cardstr.action.value="updateload"; document.cardstr.submit(); }); });';
	print '</script>'."\n";

	print '<form id="cardstr" name="cardstr" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idf" value="'.$idf.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'" required></td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup").'</td><td>';
	print $form->selectarray('group_structure',$aGroupstr,(GETPOST('group_structure')?GETPOST('group_structure'):$object->group_structure),1);
	print '</td></tr>';

	if (!empty(GETPOST('group_structure')))
	{
		if ($aGroupstr[GETPOST('group_structure')] && GETPOST('group_structure') != 'OT')

		{
			print '<tr id="fkcateg"><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td>';
			print $form->select_all_categories('product','',"fk_categorie");
			print '</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input id="detail" class="flat" type="text" name="detail" value="'.$aGroupstr[GETPOST('group_structure')].'" readOnly></td></tr>';
			print '<input id="detail" class="flat" type="hidden" name="detail" value="'.$aGroupstr[GETPOST('group_structure')].'" readOnly>';
		}
		elseif(GETPOST('group_structure') == 'OT')
		{
			print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input id="detail" class="flat" type="text" name="detail" value="'.GETPOST('detail').'" required></td></tr>';
		}
	}
	else
	{
		if ($aGroupstr[$object->group_structure] && $object->group_structure != 'OT')

		{
			print '<tr id="fkcateg"><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td>';
			print $form->select_all_categories('product',(GETPOST('fk_categorie')?GETPOST('fk_categorie'):$object->fk_categorie),'fk_categorie');
			print '</td></tr>';
			print '<input id="detail" class="flat" type="hidden" name="detail" value="'.$aGroupstr[$object->group_structure].'">';
		}
		elseif($object->group_structure == 'OT')
		{
			print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input id="detail" class="flat" type="text" name="detail" value="'.$object->detail.'" required></td></tr>';
		}

	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Visibleincomplementary").'</td><td>'.$form->selectyesno('complementary',$object->complementary,1).'</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldordby").'</td><td><input class="flat" type="text" name="ordby" value="'.$object->ordby.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldejecution").'</td><td><input class="flat" type="text" name="ejecution" value="'.$object->ejecution.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; ';
	print '<a href="'.DOL_URL_ROOT.'/budget/structure/card.php?id='.$id.'" class="button">'.$langs->trans("Cancel").'</a></div>';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action != 'edit' || $action != 'create'))
{
	print load_fiche_titre($langs->trans("Structuretype"));
	dol_htmloutput_mesg($mesg);

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>'.$object->entity.'</td></tr>';
	print '<tr><td width="20%">'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	//$projetlabel = '';
	//$projet->fetch($object->fk_projet);
	//if ($projet->id == $object->fk_projet) $projetlabel = $projet->label;
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td>'.$projetlabel.'</td></tr>';
	$typestr->fetch(0,$object->type_structure);
	if ($object->fk_categorie>0)
	{
		$categorie->fetch($object->fk_categorie);
		print '<tr><td>.'.$langs->trans("Fieldfk_categorie").'</td><td>'.$categorie->getNomUrl(1).'</td></tr>';
	}

	print '<tr><td>'.$langs->trans("Fieldtype_structure").'</td><td>'.$typestr->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$object->detail.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldordby").'</td><td>'.$object->ordby.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldejecution").'</td><td>'.$object->ejecution.'</td></tr>';
	print '<tr><td>'.$langs->trans("Visibleincomplementary").'</td><td>'.($object->complementary?$langs->trans('Yes'):$langs->trans('No')).'</td></tr>';
	$objuser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	$objuser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatut").'</td><td>'.$object->getLibStatut(1).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($typestr->status == 0)
		{
			if ($user->rights->budget->par->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}
			//if ($user->rights->budget->par->val)
			//	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=activate">'.$langs->trans('Deactivate').'</a></div>'."\n";

			if ($user->rights->budget->par->del)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}

		}
		else
		{
			//if ($user->rights->budget->par->act)
			//	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=noactivate">'.$langs->trans('Deactivate').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

	//recuperamos la lista det

	$object->fetch_lines($limit,$offset);
	$lines = $object->lines;

	$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
	print_barre_liste($langs->trans("Liste procedim details"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Sequen"),"fiche.php", "","","","");
	print_liste_field_titre($langs->trans("Formula"),"fiche.php", "","","","");
	print_liste_field_titre($langs->trans("Fieldtitle"),"fiche.php", "","","","");
	print_liste_field_titre($langs->trans("Fieldprint"),"fiche.php", "","","","");
	print_liste_field_titre($langs->trans("Fieldprintdet"),"fiche.php", "","","","");
	print_liste_field_titre($langs->trans("Enabled"),"fiche.php", "","","","");
	print_liste_field_titre($langs->trans("Action"),'','','','','align="center"');
	print "</tr>\n";
	if ($action == 'createdet')
	{
		$objectdet->initAsSpecimen();
		$objnew = $objectdet;
		include_once DOL_DOCUMENT_ROOT.'/budget/structure/tpl/add_field.tpl.php';
	}
	if (count($lines))
	{
		$num = count($lines);
		$i = 0;
		$var=True;

		foreach ((array) $lines AS $i => $obj)
		{
			//$obj = $lines[$i];
			if ($obj->id == $idr)
			{
				$objectdet->fetch($obj->id);
				$objnew = $objectdet;
				include_once DOL_DOCUMENT_ROOT.'/budget/structure/tpl/add_field.tpl.php';
			}
			else
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				//print '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$obj->id.'">'.img_picto($langs->trans("Ref"),DOL_URL_ROOT.'/salary/img/next','',1).' '.sprintf("%04d",$obj->sequen).'</a></td>';
				print '<td>'.sprintf("%04d",$obj->sequen).'</td>';
				print '<td>';
				$resform = $objform->fetch('',$obj->formula);
				if ($resform>0)
				{
					if ($objform->ref == $obj->formula)
					{
						print $objform->getNomUrl(1);
					}
					else
						print '';
				}
				else
					print ' err';

				print '</td>';
				print '<td>'.$obj->detail.'</td>';
				print '<td>'.($obj->status_print?$langs->trans('Yes'):$langs->trans('No')).'</td>';
				print '<td>'.($obj->status_print_det?$langs->trans('Yes'):$langs->trans('No')).'</td>';
				print '<td>'.($obj->status?img_picto('','switch_on'):img_picto('','switch_off')).'</td>';
			  		//action
				print '<td>';
				print '<center>';
				if ($user->rights->budget->par->write)
					print '<a href="'.$_SERVER['PHP_SELF'].'?action=editdet&id='.$object->id.'&idr='.$obj->id.'">'.img_picto($langs->trans("Edit"),'edit').'</a>';
				if ($user->rights->budget->par->del)
				{
					print '&nbsp;&nbsp;';
					print '<a href="'.$_SERVER['PHP_SELF'].'?action=deletedet&id='.$object->id.'&idr='.$obj->id.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
				}
				print '</center>';
				print '</td>';

				print "</tr>\n";
			}
			$i++;
		}
	}
	print "</table>";

	/* **************** */
	/*                  */
	/* Barre d'action   */
	/*                  */
	/* **************** */

	print "<div class=\"tabsAction\">\n";

	if ($action == '' && !empty($id))
	{
		if ($user->rights->budget->par->write)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createdet&id='.$object->id.'">'.$langs->trans("Create").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";
	}
	print "</div>";

}


// End of page
llxFooter();
$db->close();
