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
 *   	\file       budget/putypestructure_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-25 18:37
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
dol_include_once('/budget/class/putypestructure.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/categories/class/categorie.class.php');
// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");
$langs->load("budget@budget");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_code=GETPOST('search_code','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.ordby"; // Set here default search field
if (! $sortorder) $sortorder="ASC";


// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
if (!$user->rights->budget->par->read) accessforbidden();

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Putypestructure($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,(empty($ref)?NULL:$ref));
	if ($result < 0) dol_print_error($db);
}

$objstr    		= new Pustructureext($db);
$objstrtmp 		= new Pustructureext($db);
$objstrdet 		= new Pustructuredetext($db);
$objstrdettmp 	= new Pustructuredetext($db);
$objuser 		= new User($db);
$objcat 		= new Categorie($db);
$budget 		= new Budgetext($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('putypestructure'));
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
	//confirm_clone
	if ($action == 'confirm_clon')
	{
		//habilitamos la copia
		//recibimos en nuevo nombre
		$codeorig = $object->code;
		$db->begin();
		$object->id = 0;
		$object->code = GETPOST('code');
		$object->label = GETPOST('label');
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->date_create = dol_now();
		$object->date_mod = dol_now();
		$object->tms = dol_now();
		$object->active = 1;
		$nid = $object->create($user);
		if ($nid > 0)
		{
			//recuperamos toda la estructura definida
			$filterstatic = " AND t.type_structure = '".trim($codeorig)."'";
			$filterstatic.= " AND t.entity = ".$conf->entity;
			$res = $objstr->fetchAll('ASC', 'ordby',0,0,array(1=>1),'AND',$filterstatic);
			if ($res > 0)
			{
				$lines = $objstr->lines;
				foreach ($lines AS $i => $line)
				{
					$objstr->fetch($line->id);
					$objstr->id = 0;
					$objstr->fk_user_create = $user->id;
					$objstr->fk_user_mod = $user->id;
					$objstr->type_structure = GETPOST('code');
					$objstr->date_create = dol_now();
					$objstr->date_mod = dol_now();
					$objstr->tms = dol_now();
					$res1 = $objstr->create($user);
					if ($res1 <= 0)
					{
						setEventMessages($objstr->error,$objstr->errors,'errors');
						$error++;
					}
					//registramos el objstrdet

					if (!$error)
					{
						//recuperamos toda la estructura definida
						$filterstatic = " AND t.ref_structure = '".$line->ref."'";
						$filterstatic.= " AND t.type_structure = '".$codeorig."'";

						$resd = $objstrdet->fetchAll('ASC', 'sequen',0,0,array(1=>1),'AND',$filterstatic);

						if ($resd > 0)
						{
							$linedet = $objstrdet->lines;
							foreach ($linedet AS $i => $lind)
							{
								$objstrdet->fetch($lind->id);
								$objstrdet->id = 0;
								$objstrdet->ref_structure = $line->ref;
								$objstrdet->type_structure = GETPOST('code');
								$objstrdet->fk_user_create = $user->id;
								$objstrdet->fk_user_mod = $user->id;
								$objstrdet->date_create = dol_now();
								$objstrdet->date_mod = dol_now();
								$objstrdet->tms = dol_now();
								$res2 = $objstrdet->create($user);
								if ($res2 <= 0)
								{
									setEventMessages($objstrdet->error,$objstrdet->errors,'errors');
									$error++;
								}
							}
						}
					}
					else
					{
						//nada esta vacio
					}
				}
			}
			else
			{
				setEventMessages($object->error, $object->errors,'errors');
				$error++;
			}
		}

		if (!$error)
		{
			setEventMessages($langs->trans('Clonesucessfull'),null,'mesgs');
			$db->commit();
		}
		else
		{
			$db->rollback();
		}
		$action = '';

	}
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=$conf->entity;
		$object->code=GETPOST('code','alpha');
		$object->label=GETPOST('label','alpha');
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->date_create = dol_now();
		$object->date_mod = dol_now();
		$object->tms = dol_now();
		$object->active=1;



		if (empty($object->code))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Code")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/typestructure/card.php?id='.$result,1);
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
		$object->code=GETPOST('code','alpha');
		$object->label=GETPOST('label','alpha');
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->date_mod = dol_now();
		$object->tms = dol_now();


		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
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
			header("Location: ".dol_buildpath('/budget/typestructure/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
	// Action to delete
	if ($action == 'confirm_validate' && $_REQUEST['confirm']== 'yes')
	{
		$object->status = ($object->status==0?1:0);
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			if ($object->status) setEventMessages("RecordValidate", null, 'mesgs');
			else setEventMessages("RecordNovalidate", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/typestructure/card.php?id='.$id,1));
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

llxHeader('',$langs->trans('Typestructure'),'');

$form=new Form($db);




// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewTypestructure"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode").'</td><td><input class="flat" type="text" name="code" value="'.GETPOST('code').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Typestructure"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode").'</td><td><input class="flat" type="text" name="code" value="'.$object->code.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$object->active.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action != 'edit' || $action != 'create'))
{
	print load_fiche_titre($langs->trans("Typestructure"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteStructuretype'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 1, 1);
		print $formconfirm;
	}
	if ($action == 'validate') {
		if ($object->status == 0)
		{
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ValidateStructuretype'), $langs->trans('ConfirmValidateStructuretype'), 'confirm_validate', '', 1, 1);
			print $formconfirm;
		}
		else
		{
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('NovalidateStructuretype'), $langs->trans('ConfirmNovalidateStructuretype'), 'confirm_validate', '', 1, 1);
			print $formconfirm;

		}
	}
	if ($action == 'confclon' && $object->status == 1) {
		$formquestion = array(array('type'=>'text','label'=>$langs->trans('Code'),'size'=>5,'name'=>'code','value'=>$object->code),array('type'=>'text','label'=>$langs->trans('Label'),'size'=>40,'name'=>'label','value'=>''));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Clonestructure'), $langs->trans('ConfirmCloneStructure'), 'confirm_clon', $formquestion, 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldcode").'</td><td>'.$object->code.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	$objuser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	$objuser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($object->status == 0)
		{
			if ($user->rights->budget->par->val)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
			}
			if ($user->rights->budget->par->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}

			if ($user->rights->budget->par->del)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
		}
		else
		{
			if ($user->rights->budget->par->write)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=confclon">'.$langs->trans("Clone").'</a></div>'."\n";
			}
			if ($user->rights->budget->par->val)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Novalidate").'</a></div>'."\n";
			}

		}
	}
	print '</div>'."\n";


	// Definition of fields for list
	$arrayfields=array(

		't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
		't.ref'=>array('label'=>$langs->trans("Ref"), 'checked'=>1),
		't.fk_budget'=>array('label'=>$langs->trans("Budget"), 'checked'=>0),
		't.fk_projet'=>array('label'=>$langs->trans("Project"), 'checked'=>0),
		't.fk_user_create'=>array('label'=>$langs->trans("User"), 'checked'=>1),
		't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
		't.fk_categorie'=>array('label'=>$langs->trans("Categorie"), 'checked'=>1),
		't.detail'=>array('label'=>$langs->trans("Detail"), 'checked'=>1),
		't.ordby'=>array('label'=>$langs->trans("Orderby"), 'checked'=>1),
		't.complementary'=>array('label'=>$langs->trans("Visibleincomplementary"), 'checked'=>1),
		't.date_mod'=>array('label'=>$langs->trans("date_mod"), 'checked'=>0),
		't.status'=>array('label'=>$langs->trans("Status"), 'checked'=>1),


    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
		't.date_create'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
		't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
    //'t.status'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
	);
// Extra fields
	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
	{
		foreach($extrafields->attribute_label as $key => $val)
		{
			$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
		}
	}




	/*******************************************************************
	* ACTIONS
	*
	* Put here all code to do according to value of "action" parameter
	********************************************************************/

	if (GETPOST('cancel')) { $action='list'; $massaction=''; }
	if (! GETPOST('confirmmassaction')) { $massaction=''; }

	$parameters=array();
	$reshook=$hookmanager->executeHooks('doActions',$parameters,$objstr,$action);
 // Note that $action and $object may have been modified by some hooks
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter"))
 // All test are required to be compatible with all browsers
	{

		$search_entity='';
		$search_ref='';
		$search_fk_budget='';
		$search_fk_projet='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_fk_categorie='';
		$search_detail='';
		$search_ordby='';
		$search_date_mod='';
		$search_status='';


		$search_date_creation='';
		$search_date_update='';
		$search_array_options=array();
	}


	if (empty($reshook))
	{
		$toselect = array();
    // Mass actions. Controls on number of lines checked
		$maxformassaction=1000;
		if (! empty($massaction) && count($toselect) < 1)
		{
			$error++;
			setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
		}
		if (! $error && count($toselect) > $maxformassaction)
		{
			setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
			$error++;
		}

		// Action to delete
		if ($action == 'confirm_delete')
		{
			$result=$object->delete($user);
			if ($result > 0)
			{
			// Delete OK
				setEventMessages("RecordDeleted", null, 'mesgs');
				header("Location: ".dol_buildpath('/budget/list.php',1));
				exit;
			}
			else
			{
				if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
				else setEventMessages($object->error,null,'errors');
			}
		}
	}




// VIEW structure

	$now=dol_now();

	$form=new Form($db);

// Put here content of your page



	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql .= " t.entity,";
	$sql .= " t.ref,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.fk_categorie,";
	$sql .= " t.type_structure,";
	$sql .= " t.detail,";
	$sql .= " t.ordby,";
	$sql .= " t.date_delete,";
	$sql .= " t.date_create,";
	$sql .= " t.date_mod,";
	$sql .= " t.tms,";
	$sql .= " t.status";


// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
  // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."pu_structure as t";
	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."pu_structure_extrafields as ef on (u.rowid = ef.fk_object)";
	$sql.= " WHERE 1 = 1";
	$sql.= " AND t.type_structure = '".$object->code."'";
	//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

	if ($search_entity) $sql.= natural_search("entity",STRTOUPPER($search_entity));
	if ($search_ref) $sql.= natural_search("ref",STRTOUPPER($search_ref));
	//if ($search_fk_budget) $sql.= natural_search("fk_budget",STRTOUPPER($search_fk_budget));
	//if ($search_fk_projet) $sql.= natural_search("fk_projet",STRTOUPPER($search_fk_projet));
	if ($search_fk_user_create) $sql.= natural_search("fk_user_create",STRTOUPPER($search_fk_user_create));
	if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",STRTOUPPER($search_fk_user_mod));
	if ($search_fk_categorie) $sql.= natural_search("fk_categorie",STRTOUPPER($search_fk_categorie));
	if ($search_detail) $sql.= natural_search("detail",STRTOUPPER($search_detail));
	if ($search_ordby) $sql.= natural_search("ordby",STRTOUPPER($search_ordby));
	if ($search_date_mod) $sql.= natural_search("date_mod",STRTOUPPER($search_date_mod));
	if ($search_complementary) $sql.= natural_search("complementary",STRTOUPPER($search_complementary));
	if ($search_status) $sql.= natural_search("status",STRTOUPPER($search_status));


	if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);

	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);
	// Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.=$db->order($sortfield,$sortorder);
	//$sql.= $db->plimit($conf->liste_limit+1, $offset);

	// Count total nb of records
	$nbtotalofrecords = 0;
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}

	$sql.= $db->plimit($limit+1, $offset);

	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		$params='';
		if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
		$params.='&id='.$id;
		if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
		if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
		//if ($search_fk_budget != '') $params.= '&amp;search_fk_budget='.urlencode($search_fk_budget);
		//if ($search_fk_projet != '') $params.= '&amp;search_fk_projet='.urlencode($search_fk_projet);
		if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
		if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
		if ($search_fk_categorie != '') $params.= '&amp;search_fk_categorie='.urlencode($search_fk_categorie);
		if ($search_detail != '') $params.= '&amp;search_detail='.urlencode($search_detail);
		if ($search_ordby != '') $params.= '&amp;search_ordby='.urlencode($search_ordby);
		if ($search_date_mod != '') $params.= '&amp;search_date_mod='.urlencode($search_date_mod);
		if ($search_status != '') $params.= '&amp;search_status='.urlencode($search_status);


		if ($optioncss != '') $param.='&optioncss='.$optioncss;


		//print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);


		print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
		if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
		print '<input type="hidden" name="action" value="list">';
		print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
		print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

		if ($sall)
		{
			foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
			print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
		}

		$moreforfilter = '';
		//$moreforfilter.='<div class="divsearchfield">';
		//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
		//$moreforfilter.= '</div>';

		if (! empty($moreforfilter) && $abc)
		{
			print '<div class="liste_titre liste_titre_bydiv centpercent">';
			print $moreforfilter;
			$parameters=array();
			$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);
    	  // Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;
			print '</div>';
		}

		$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
		$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);
    	// This also change content of $arrayfields

		print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    	// Fields title
		print '<tr class="liste_titre">';
    	//
		if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
		//if (! empty($arrayfields['t.fk_budget']['checked'])) print_liste_field_titre($arrayfields['t.fk_budget']['label'],$_SERVER['PHP_SELF'],'t.fk_budget','',$params,'',$sortfield,$sortorder);
		//if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.fk_categorie']['checked'])) print_liste_field_titre($arrayfields['t.fk_categorie']['label'],$_SERVER['PHP_SELF'],'t.fk_categorie','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.ordby']['checked'])) print_liste_field_titre($arrayfields['t.ordby']['label'],$_SERVER['PHP_SELF'],'t.ordby','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.date_mod']['checked'])) print_liste_field_titre($arrayfields['t.date_mod']['label'],$_SERVER['PHP_SELF'],'t.date_mod','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.complementary']['checked'])) print_liste_field_titre($arrayfields['t.complementary']['label'],$_SERVER['PHP_SELF'],'t.complementary','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'align="center"',$sortfield,$sortorder);

    	//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$params,'',$sortfield,$sortorder);
    	//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$params,'',$sortfield,$sortorder);
		// Extra fields
		if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
		{
			foreach($extrafields->attribute_label as $key => $val)
			{
				if (! empty($arrayfields["ef.".$key]['checked']))
				{
					$align=$extrafields->getAlignFlag($key);
					print_liste_field_titre($extralabels[$key],$_SERVER["PHP_SELF"],"ef.".$key,"",$param,($align?'align="'.$align.'"':''),$sortfield,$sortorder);
				}
			}
		}
    	// Hook fields
		$parameters=array('arrayfields'=>$arrayfields);
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
      // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
		if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);

		//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
		print '</tr>'."\n";

    	// Fields title search
    	/*
		print '<tr class="liste_titre">';
		//
		if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
		if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
		//if (! empty($arrayfields['t.fk_budget']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_budget" value="'.$search_fk_budget.'" size="10"></td>';
		//if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
		if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
		if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
		if (! empty($arrayfields['t.fk_categorie']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_categorie" value="'.$search_fk_categorie.'" size="10"></td>';
		if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
		if (! empty($arrayfields['t.ordby']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ordby" value="'.$search_ordby.'" size="10"></td>';
		if (! empty($arrayfields['t.date_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_date_mod" value="'.$search_date_mod.'" size="10"></td>';
		if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';

		//if (! empty($arrayfields['t.field1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_field1" value="'.$search_field1.'" size="10"></td>';
		//if (! empty($arrayfields['t.field2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_field2" value="'.$search_field2.'" size="10"></td>';
		// Extra fields
		if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
		{
			foreach($extrafields->attribute_label as $key => $val)
			{
				if (! empty($arrayfields["ef.".$key]['checked']))
				{
					$align=$extrafields->getAlignFlag($key);
					$typeofextrafield=$extrafields->attribute_type[$key];
					print '<td class="liste_titre'.($align?' '.$align:'').'">';
					if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')))
					{
						$crit=$val;
						$tmpkey=preg_replace('/search_options_/','',$key);
						$searchclass='';
						if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
						if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
						print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="search_options_'.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options['search_options_'.$tmpkey]).'">';
					}
					print '</td>';
				}
			}
		}
    	// Fields from hook
		$parameters=array('arrayfields'=>$arrayfields);
		$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);
     	// Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		if (! empty($arrayfields['t.datec']['checked']))
		{
        // Date creation
			print '<td class="liste_titre">';
			print '</td>';
		}
		if (! empty($arrayfields['t.tms']['checked']))
		{
        // Date modification
			print '<td class="liste_titre">';
			print '</td>';
		}

    	// Action column
		print '<td class="liste_titre" align="right">';
		$searchpitco=$form->showFilterAndCheckAddButtons(0);
		print $searchpitco;
		print '</td>';
		print '</tr>'."\n";
		*/

		$i=0;
		$var=true;
		$totalarray=array();
		if ($action == 'createdet')
		{

		}
		while ($i < min($num, $limit))
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$objstr->fetch($obj->rowid);
				$var = !$var;

            	// Show here line of result
				print '<tr '.$bc[$var].'>';
            	// LIST_OF_TD_FIELDS_LIST

				if (! empty($arrayfields['t.ref']['checked']))
				{
					print '<td>'.$objstr->getNomUrl(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_user_create']['checked']))
				{
					$objuser->fetch($obj->fk_user_create);
					if ($objuser->id>0)
						print '<td>'.$objuser->getNomUrl(1).'</td>';
					else
						print '<td>&nbsp;</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_categorie']['checked']))
				{
					$rescat=$objcat->fetch($obj->fk_categorie);
					if ($rescat==1)
						print '<td><span class="noborderongcategories" style="background:#'.$objcat->color.';">'.$objcat->getNomUrl(1).'</span></td>';
					else
						print '<td>&nbsp;</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.detail']['checked']))
				{
					print '<td>'.$obj->detail.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.ordby']['checked']))
				{
					print '<td>'.$obj->ordby.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.date_mod']['checked']))
				{
					print '<td align="center">'.$obj->date_mod.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.complementary']['checked']))
				{
					print '<td align="center">'.($objstr->complementary?$langs->trans('Yes'):$langs->trans('No')).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.status']['checked']))
				{
					print '<td align="center">'.$objstr->getLibstatut(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
            // Extra fields
				if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
				{
					foreach($extrafields->attribute_label as $key => $val)
					{
						if (! empty($arrayfields["ef.".$key]['checked']))
						{
							print '<td';
							$align=$extrafields->getAlignFlag($key);
							if ($align) print ' align="'.$align.'"';
							print '>';
							$tmpkey='options_'.$key;
							print $extrafields->showOutputField($key, $obj->$tmpkey, '', 1);
							print '</td>';
							if (! $i) $totalarray['nbfield']++;
						}
					}
				}
            	// Fields from hook
				$parameters=array('arrayfields'=>$arrayfields, 'obj'=>$obj);
				$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);

    		  	// Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;
        		// Date creation
				if (! empty($arrayfields['t.datec']['checked']))
				{
					print '<td align="center">';
					print dol_print_date($db->jdate($obj->date_creation), 'dayhour');
					print '</td>';
					if (! $i) $totalarray['nbfield']++;
				}
            	// Date modification
				if (! empty($arrayfields['t.tms']['checked']))
				{
					print '<td align="center">';
					print dol_print_date($db->jdate($obj->date_update), 'dayhour');
					print '</td>';
					if (! $i) $totalarray['nbfield']++;
				}
                        // Action column
				print '<td></td>';
				if (! $i) $totalarray['nbfield']++;

				print '</tr>';
			}
			$i++;
		}

		$db->free($resql);

		$parameters=array('sql' => $sql);
		$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);

	  // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print "</table>\n";
		print "</form>\n";

		$db->free($result);

				// Buttons
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
		// Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if ($object->status == 0)
			{
			if ($user->rights->budget->par->write)
			{
				$url = DOL_URL_ROOT.'/budget/structure/card.php'.'?idf='.$object->id;
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$url.'&amp;action=create">'.$langs->trans("Createstr").'</a></div>'."\n";
			}

			if ($user->rights->budget->par->del)
			{
				//print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
			}
		}
		print '</div>'."\n";
	}
	else
	{
		$error++;
		dol_print_error($db);
	}

	//LISTAMOS los presupuestos que utilizan este tipo structure
	$filterstatic = " AND t.type_structure = '".$object->code."'";
	$filterstatic.= " AND t.fk_statut >= 0";
	$resb = $budget->fetchAll('ASC', 'ref', 0, 0,array(1=>1), 'AND',$filterstatic);
	if ($resb>0)
	{
		$help_url='EN:Module_Budget_En|FR:Module_Budget|ES:M&oacute;dulo_Budget';
		print_barre_liste($langs->trans("ListBudget"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Ref"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Version"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Fieldtitle"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Fielddetail"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Statut"),"fiche.php", "","","","");

		print "</tr>\n";
		$lines = $budget->lines;
		$var = false;
		foreach ($lines AS $j => $line)
		{
			$var = !$var;
			$budget->id = $line->id;
			$budget->ref = $line->ref;
			$budget->label = $line->label;
			$budget->detail = $line->detail;
			$budget->fk_statut = $line->fk_statut;
			print "<tr $bc[$var]>";
			print '<td>'.$budget->getNomUrl(1).'</td>';
			print '<td>'.$line->version.'</td>';
			print '<td>'.$line->title.'</td>';
			print '<td>'.$line->description.'</td>';
			print '<td>'.$budget->getLibStatut(2).'</td>';
			print '</tr>';
		}
	}

	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
