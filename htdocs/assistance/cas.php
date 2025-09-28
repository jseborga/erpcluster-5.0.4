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
 *   	\file       /assistancedef_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-12 13:25
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
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
dol_include_once('/adherents/class/adherent.class.php');
dol_include_once('/adherents/class/adherent_type.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/membercas.class.php');

// Load traductions files requiredby by page
$langs->load("assistance");
$langs->load("companies");
$langs->load("other");
$langs->load("members");

// Get parameters
$idd = GETPOST('idd','int');
$id		= GETPOST('rowid','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel 	= GETPOST('cancel','alpha');
$confirm 	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$date  = dol_mktime(12, 0, 0, GETPOST('dr_month'), GETPOST('dr_day'), GETPOST('dr_year'));


// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}
if (!$user->rights->assistance->cas->read)	accessforbidden();

// Load object if id or ref is provided as parameter
$object=new Adherent($db);
$objMembercas=new Membercas($db);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals


$objTypemarking = new Typemarking($db);
$objAdherent=new Adherent($db);
$objAdherenttype=new AdherentType($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objMembercas->table_element);


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('membercas'));

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistancedef'));


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objMembercas,$action);
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
$now = dol_now();
if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/cas.php?rowid='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objMembercas->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add' && $user->rights->assistance->cas->write)
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/cas.php?rowid='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objMembercas->fk_member=$id;
		$objMembercas->dater=$date;
		$objMembercas->number_year=GETPOST('number_year','int');
		$objMembercas->number_month=GETPOST('number_month','int')+0;
		$objMembercas->number_day=GETPOST('number_day','int')+0;
		$objMembercas->fk_user_create=$user->id;
		$objMembercas->fk_user_mod=$user->id;
		$objMembercas->datec = $now;
		$objMembercas->datem = $now;
		$objMembercas->status=1;

		if (empty($objMembercas->number_year))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldnumber_year")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objMembercas->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/cas.php?rowid='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objMembercas->errors)) setEventMessages(null, $objMembercas->errors, 'errors');
				else  setEventMessages($objMembercas->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update' && $idd>0 && $user->rights->assistance->cas->write)
	{
		$objMembercas->fetch($idd);
		$error=0;

		$objMembercas->dater=$date;
		$objMembercas->number_year=GETPOST('number_year','int');
		$objMembercas->number_month=GETPOST('number_month','int')+0;
		$objMembercas->number_day=GETPOST('number_day','int')+0;
		$objMembercas->fk_user_mod=$user->id;
		$objMembercas->datem = $now;

		if (empty($objMembercas->number_year))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldnumber_year")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objMembercas->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objMembercas->errors)) setEventMessages(null, $objMembercas->errors, 'errors');
				else setEventMessages($objMembercas->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}
	// Action to delete
	if ($action == 'confirm_delete' && $confirm=='yes' && $idd>0 && $user->rights->assistance->cas->del)
	{
		$objMembercas->fetch($idd);
		$result=$objMembercas->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistance/cas.php?rowid='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objMembercas->errors)) setEventMessages(null, $objMembercas->errors, 'errors');
			else setEventMessages($objMembercas->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('CAS'),'');

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


//mostramos al miembro
if ($id>0)
{
	$objAdherent->fetch($id);
	$result=$objAdherenttype->fetch($objAdherent->typeid);
	if ($result > 0)
	{
	/*
	 * Affichage onglets
	 */
	if (! empty($conf->notification->enabled))
		$langs->load("mails");

	$head = member_prepare_head($objAdherent);

	$form=new Form($db);

	dol_fiche_head($head, 'cas', $langs->trans("Member"),0,'user');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/adherents/list.php">'.$langs->trans("BackToList").'</a>';

		// Ref
	print '<tr><td width="20%">'.$langs->trans("Ref").'</td>';
	print '<td class="valeur">';
	print $form->showrefnav($objAdherent, 'rowid', $linkback);
	print '</td></tr>';

		// Login
	if (empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED))
	{
		print '<tr><td>'.$langs->trans("Login").' / '.$langs->trans("Id").'</td><td class="valeur">'.$objAdherent->login.'&nbsp;</td></tr>';
	}

		// Morphy
	print '<tr><td>'.$langs->trans("Nature").'</td><td class="valeur" >'.$objAdherent->getmorphylib().'</td>';
		/*print '<td rowspan="'.$rowspan.'" align="center" valign="middle" width="25%">';
	 print $form->showphoto('memberphoto',$object);
	 print '</td>';*/
	 print '</tr>';

		// Type
	 print '<tr><td>'.$langs->trans("Type").'</td><td class="valeur">'.$objAdherenttype->getNomUrl(1)."</td></tr>\n";

		// Company
	 print '<tr><td>'.$langs->trans("Company").'</td><td class="valeur">'.$objAdherent->societe.'</td></tr>';

		// Civility
	 print '<tr><td>'.$langs->trans("UserTitle").'</td><td class="valeur">'.$objAdherent->getCivilityLabel().'&nbsp;</td>';
	 print '</tr>';

		// Lastname
	 print '<tr><td>'.$langs->trans("Lastname").'</td><td class="valeur">'.$objAdherent->lastname.'&nbsp;</td>';
	 print '</tr>';

		// Firstname
	 print '<tr><td>'.$langs->trans("Firstname").'</td><td class="valeur">'.$objAdherent->firstname.'&nbsp;</td>';
	 print '</tr>';

		// Status
	 print '<tr><td>'.$langs->trans("Status").'</td><td class="valeur">'.$objAdherent->getLibStatut(4).'</td></tr>';

	 print '</table>';
	 dol_fiche_end();
	}

	//revisamos por el id member
	if ($id>0)
	{
		$result=$objMembercas->fetch(0,$id);
		if ($result < 0)
		{
			dol_print_error($db);
		}
		else
		{
			if ($result == 0) $action = 'create';
			else $idd = $objMembercas->id;
		}
	}

	if (empty($action) && empty($idd)) $action='create';


	// Part to create
	if ($action == 'create' && $user->rights->assistance->cas->write)
	{
		print_fiche_titre($langs->trans("New"));

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="rowid" value="'.$id.'">';

		dol_fiche_head();

		print '<table class="border centpercent">'."\n";
		print '<tr><td class="fieldrequired" width="20%">'.$langs->trans("Registrationdate").'</td><td>';
		print $form->select_date(GETPOST('dater'),'dr_',0,0,1);
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Year").'</td><td>';
		print '<input type="number" min="0" max="90" name="number_year" value="'.GETPOST('number_year').'">';
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Month").'</td><td>';
		print '<input type="number" min="0" max="11" name="number_month" value="'.GETPOST('number_month').'">';
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Day").'</td><td>';
		print '<input type="number" min="0" max="31" name="number_day" value="'.GETPOST('number_day').'">';
		print '</td></tr>';

		print '</table>'."\n";

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'">';
	//print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';
	}

	// Part to edit record
	if (($id && $idd) && $action == 'edit' && $user->rights->assistance->mark->write)
	{

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

		dol_fiche_head();

		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="rowid" value="'.$id.'">';
		print '<input type="hidden" name="idd" value="'.$objMembercas->id.'">';

		print '<table class="border centpercent">'."\n";
		print '<tr><td class="fieldrequired" width="20%">'.$langs->trans("Registrationdate").'</td><td>';
		print $form->select_date((GETPOST('dater')?GETPOST('dater'):$objMembercas->dater),'dr_',0,0,1);
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Year").'</td><td>';
		print '<input type="number" min="0" max="90" name="number_year" value="'.(GETPOST('number_year')?GETPOST('number_year'):$objMembercas->number_year).'">';
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Month").'</td><td>';
		print '<input type="number" min="0" max="11" name="number_month" value="'.(GETPOST('number_month')?GETPOST('number_month'):$objMembercas->number_month).'" maxlenght="2">';
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Day").'</td><td>';
		print '<input type="number" min="0" max="31" name="number_day" value="'.(GETPOST('number_day')?GETPOST('number_day'):$objMembercas->number_day).'">';
		print '</td></tr>';
		print '</table>';

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';
	}


	// Part to show record
	if (($id && $idd) && ($action!='create' && $action != 'edit'))
	{
		//cas
		print_fiche_titre($langs->trans("CAS"));
		dol_fiche_head();

		if ($action == 'delete') {
			$formquestion = array(array('type'=>'hidden', 'name'=>'idd','value'=>$idd),);
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?rowid=' . $id, $langs->trans('DeleteCAS'), $langs->trans('ConfirmDeleteCAS'), 'confirm_delete', $formquestion, 0, 2);
			print $formconfirm;
		}

		print '<table class="border centpercent">'."\n";
		print '<tr><td class="fieldrequired" width="20%">'.$langs->trans("Registrationdate").'</td><td>';
		print dol_print_date($objMembercas->dater,'day');
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Year").'</td><td>';
		print $objMembercas->number_year;
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Month").'</td><td>';
		print $objMembercas->number_month;
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Day").'</td><td>';
		print $objMembercas->number_day;
		print '</td></tr>';

		print '</table>';
		print '<h3>';
		print img_picto($langs->trans('Warning'),'warning').' '.$langs->trans('TheCASrecorddoesnotincludethetimeworkedinthecurrentcontract');
		print '</h3>';

		dol_fiche_end();


		// Buttons
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objMembercas,$action);
		// Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if ($user->rights->assistance->cas->write)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?rowid='.$object->id.'&idd='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}

			if ($user->rights->assistance->cas->del)
			{
				if ($conf->use_javascript_ajax && !empty($conf->dol_use_jmobile))
				{
					print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
				}
				else
				{
					print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?rowid='.$object->id.'&idd='.$objMembercas->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
				}
			}
		}
		print '</div>'."\n";
	}
}

// End of page
llxFooter();
$db->close();
