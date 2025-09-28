<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       dev/Tvadefs/Tvadef_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-11 18:07
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');	// If there is no menu to show
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');	// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');		// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs

dol_include_once('/fiscal/class/tvadefext.class.php');
dol_include_once('/fiscal/lib/fiscal.lib.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("fiscal");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

$object = new Tvadefext($db);
if ($id>0)
	$res = $object->fetch($id);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
if ($id && $user->rights->fiscal->conf->val && ($action == 'val' || $action == 'noval' ))
{
	$object->fetch($id);
	$object->active = $object->active?0:1;
	$res = $object->update($user);
	if ($res <=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	$action = 'list';
}

if ($action == 'add')
{
	$object->entity = $conf->entity;
	$object->code_facture=GETPOST('code_facture');
	$object->code_tva=GETPOST('code_tva');
	$object->fk_pays=$mysoc->country_id;
	$object->taux=GETPOST('taux');
	$object->register_mode=GETPOST('register_mode');
	$object->note=GETPOST('note');
	$object->accountancy_code=GETPOST('accountancy_code');
	$object->against_account=GETPOST('against_account');
	$object->active = 0;
	//revision
	if (empty($object->code_facture))
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Typefacture")),null, 'errors');
	}
	if (empty($object->code_tva))
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Codetva")),null, 'errors');
	}
	if ($object->fk_pays <= 0)
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Country")),null, 'errors');
	}
	if ($object->taux <= 0)
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Taux")),null, 'errors');
	}

	if (! $error)
	{
		$result=$object->create($user);
		if ($result <= 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
		}
		else
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$result.'&action=view');
		}
	}
	$action = 'create';
}
if ($action == 'update' && $user->rights->fiscal->conf->mod && $_REQUEST['cancel'] != $langs->trans('Cancel'))
{
	$object->entity = $conf->entity;
	$object->code_facture=GETPOST('code_facture');
	$object->code_tva=GETPOST('code_tva');
	$object->fk_pays=$mysoc->country_id;
	$object->taux=GETPOST('taux');
	$object->register_mode=GETPOST('register_mode');
	$object->note=GETPOST('note');
	$object->accountancy_code=GETPOST('accountancy_code');
	$object->against_account=GETPOST('against_account');
	$object->active = 0;
	//revision
	if (empty($object->code_facture))
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Typefacture")), null,'errors');
		$action='edit';
	}
	if (empty($object->code_tva))
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Codetva")),null, 'errors');
		$action='edit';
	}
	if ($object->fk_pays <= 0)
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Country")),null, 'errors');
		$action='edit';
	}
	if ($object->taux <= 0)
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Taux")), null,'errors');
		$action='edit';
	}

	if (! $error)
	{
		$result=$object->update($user);
		if ($result <= 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
		}
		else
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&action=view');
		}
	}
	$action = 'edit';
}
if ($action == 'confirm_delete' && $_REQUEST['confirm'] == 'yes')
{
	if ($object->id == $id)
	{
		$res = $object->delete($user);
		if ($res <=0)
		{
			setEventMessages($object->error,$object->errrors,'errors');
			$action = '';
		}
		else
		{
			header('Location: '.$_SERVER['PHP_SELF'].'?action=list');
			exit;
		}
	}
}
if ($_REQUEST['cancel'] == $langs->trans('Cancel'))
	$action = 'list';




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Fiscal','');

$form=new Formv($db);


// Put here content of your page

// Example 1 : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';


// Example 2 : Adding links to objects
//$somethingshown=$object->showLinkedObjectBlock();


// Example 3 : List of data
if ($action == 'list')
{
	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql.= " t.fk_pays,";
	$sql.= " t.code_tva,";
	$sql.= " t.code_facture,";
	$sql.= " t.taux,";
	$sql.= " t.register_mode,";
	$sql.= " t.deductible,";
	$sql.= " t.note,";
	$sql.= " t.active,";
	$sql.= " t.accountancy_code,";
	$sql.= " t.against_account ";
	$sql.= " FROM ".MAIN_DB_PREFIX."tva_def as t";
	$sql.= " WHERE t.fk_pays = ".$mysoc->country_id;
	//$sql.= " WHERE field3 = 'xxx'";
	$sql.= " ORDER BY code_facture ASC, code_tva ASC";
	$title = $langs->trans('Configuration tax bill');
	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords);
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Country'),$_SERVER['PHP_SELF'],'t.fk_pays','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Typefacture'),$_SERVER['PHP_SELF'],'t.code_facture','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Codetva'),$_SERVER['PHP_SELF'],'t.code_tva','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Taux'),$_SERVER['PHP_SELF'],'t.taux','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Automaticcalculation'),$_SERVER['PHP_SELF'],'t.register_mode','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Note'),$_SERVER['PHP_SELF'],'t.note','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Accountancycode'),$_SERVER['PHP_SELF'],'t.accountancy_code','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Againstaccount'),$_SERVER['PHP_SELF'],'t.against_account','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Active'),$_SERVER['PHP_SELF'],'t.active','',$param,'',$sortfield,$sortorder);
	print '</tr>';

	dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if ($obj)
				{
					$object->fetch($obj->rowid);
					$objfacture = fetch_type_facture('',$obj->code_facture);
					$objtva = fetch_type_tva('',$obj->code_tva);
					// Country
					$tmparray=getCountry($object->fk_pays,'all');
					$object->country_code=$tmparray['code'];
					$object->country=$tmparray['label'];
					print '<tr><td>';
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'&action=view">'.img_picto($langs->trans('Edit'),'edit').'</a>';
					print '</td><td>';
					if (! empty($object->country_code))
					{
						$img=picto_from_langcode($object->country_code);
						//$img='';
						if ($object->isInEEC()) print $form->textwithpicto(($img?$img.' ':'').$object->fy_pays,$langs->trans("CountryIsInEEC"),1,0);
						else print ($img?$img.' ':'').$object->country;
					}
					print '</td><td>';
					print $objfacture->label;
					print '</td><td>';
					print $objtva->label;
					print '</td><td align="right">';
					print price($obj->taux);
					print '</td><td align="center">';
					print $obj->register_mode?$langs->trans('Yes'):$langs->trans('No');
					print '</td><td>';
					print $obj->note;
					print '</td><td>';
					print $obj->accountancy_code;
					print '</td><td>';
					print $obj->against_account;
					print '</td><td>';
					$img = $obj->active?'on':'off';
					$switch = $obj->active?'noval':'val';
					$label = $obj->active?$langs->trans('Active'):$langs->trans('Deactivated');
					if ($user->rights->fiscal->conf->val)
					{

						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'&action='.$switch.'">'.img_picto($langs->trans($label),$img).'</a>';
					}
					else
					{
						print img_picto($langs->trans($label),$img);
					}
					print '</td></tr>';
				}
				$i++;
			}
		}
	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}

if (empty($id) && $action == 'create')
{
	print_fiche_titre($langs->trans('Configuration tax bill'));
	dol_htmloutput_events();

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#selectcountry_id").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});
		});';
		print '</script>'."\n";
	}


	print '<form name="add" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	print '<table class="border" width="100%">';

	// Country
	//print '<tr><td class="fieldrequired" ><label for="selectcountry_id">'.$langs->trans('Country').'</label></td><td colspan="2" class="maxwidthonsmartphone">';
	//print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$object->country_id));
	//if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	//print '</td></tr>';
	//print '</td>';
	//print '</tr>';

	//code_facture
	print '<tr><td valign="top" class="fieldrequired">'.$langs->trans('Typefacture').'</td><td colspan="2">';
	$typefilter=null;
	print $form->load_type_facture('code_facture',(GETPOST('code_facture')?GETPOST('code_facture'):''), 1, 'code', false,$typefilter);
	print '</td></tr>'."\n";

	//code_tva
	print '<tr><td valign="top" class="fieldrequired">'.$langs->trans('Codetva').'</td><td colspan="2">';
	print $form->load_type_tva('code_tva',(GETPOST('code_tva')?GETPOST('code_tva'):''), 1,'code', false);
	print '</td></tr>'."\n";

	// tax
	print '<tr><td>'.$langs->trans('Taux').'</td><td><input  name="taux" value="'.GETPOST('taux').'" type="number" step="any" min="0"></td></tr>';

	// Register mode
	print '<tr><td class="fieldrequired">'.$langs->trans('Automaticcalculation').'</td><td>';
	print $form->selectyesno('register_mode',GETPOST('register_mode'),1);
	print '</td></tr>';

	// Note
	print '<tr><td>'.$langs->trans('Note').'</td><td>';
	$doleditor = new DolEditor('note', GETPOST('note'), '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
	print $doleditor->Create(1);
	print '</td></tr>';

	// Accountancy
	print '<tr><td class="nowrap">'.$langs->trans('Accountancycode').'</td><td colspan="2">';
	print '<input type="text" name="accountancy_code" value="'.GETPOST('accountancy_code').'">';
	print '</td></tr>';

	// against_account
	print '<tr><td class="nowrap">'.$langs->trans('Againstaccount').'</td><td colspan="2">';
	print '<input type="text" name="against_account" value="'.GETPOST('against_account').'">';
	print '</td></tr>';

	// Bouton "Create Draft"
	print "</table>\n";

	print '<br><center><input type="submit" class="button" name="bouton" value="'.$langs->trans('CreateDraft').'"></center>';

	print "</form>\n";


	// Show origin lines
	if (is_object($objectsrc))
	{
		print '<br>';

		$title=$langs->trans('ProductsAndServices');
		print_titre($title);

		print '<table class="noborder" width="100%">';

		$objectsrc->printOriginLinesList();

		print '</table>';
	}
}

if ($id>0)
{
	dol_htmloutput_events();
	$object->fetch($id,($ref!=null?$ref:null));

	if ($action	== 'delete')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteTvaDef'), $langs->trans('ConfirmDeleteTvaDef'), 'confirm_delete', '', 0, 2);
		print $formconfirm;
	}

	if (empty($action)) $action = 'view';
	if ($action == 'view')
	{
		print_fiche_titre($langs->trans('Configuration tax bill'));

		print '<table class="border" width="100%">';

		print '<tr><td width="25%">'.$langs->trans('Ref').'</td>';
		print '<td colspan="2">';
		$link = '<a href="'.DOL_URL_ROOT.'/fiscal/tvadef.php?action=list">'.$langs->trans('Returnlist').'</a>';
		print $form->showrefnav($object, 'id', $link, ($user->societe_id?0:1), 'rowid', 'rowid');
		print '</td>';
		print '</tr>';
		// Country
		$tmparray=getCountry($object->fk_pays,'all');
		$object->country_code=$tmparray['code'];
		$object->country=$tmparray['label'];
		print '<tr><td>'.$langs->trans("Country").'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'" class="nowrap">';
		if (! empty($object->country_code))
		{
			$img=picto_from_langcode($object->country_code);
			//$img='';
			if ($object->isInEEC()) print $form->textwithpicto(($img?$img.' ':'').$object->fy_pays,$langs->trans("CountryIsInEEC"),1,0);
			else print ($img?$img.' ':'').$object->country;
		}
		print '</td></tr>';

	//code_facture
		print '<tr><td>'.$langs->trans('Typefacture').'</td><td colspan="2">';
		$objfacture = fetch_type_facture('',$object->code_facture);
		if ($objfacture->code == $object->code_facture)
			print $objfacture->label;
		else
			print '';
		print '</td></tr>'."\n";

	//code_tva
		print '<tr><td>'.$langs->trans('Codetva').'</td><td colspan="2">';
		$objtva = fetch_type_tva('',$object->code_tva);
		if ($objtva->code == $object->code_tva)
			print $objtva->label;
		else
			print '';
		print '</td></tr>'."\n";

	// tax
		print '<tr><td>'.$langs->trans('Taux').'</td><td>';
		print $object->taux;
		print '</td></tr>';

	// Register mode
		print '<tr><td>'.$langs->trans('Automaticcalculation').'</td><td>';
		print ($object->register_mode==0?$langs->trans('No'):$langs->trans('Yes'));
		print '</td></tr>';

	// Note
		print '<tr><td>'.$langs->trans('Note').'</td><td>';
		print $object->note;
		print '</td></tr>';

	// Accountancy
		print '<tr><td>'.$langs->trans('Accountancycode').'</td><td colspan="2">';
		print $object->accountancy_code;
		print '</td></tr>';

		// Against_account
		print '<tr><td>'.$langs->trans('Againstaccount').'</td><td colspan="2">';
		print $object->against_account;
		print '</td></tr>';
		print "</table>\n";
				/*
		 *  Actions
		 */
				print '<div class="tabsAction">'."\n";

				$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
		if (empty($reshook))
		{
			if (!$object->active && $user->rights->fiscal->conf->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Modify').'</a></div>';
			}
			else
			{
				print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("Edit")).'">'.$langs->trans('Modify').'</a></div>';
			}

			if ($user->rights->fiscal->conf->del)
			{
				if ($conf->use_javascript_ajax && !empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
				{
					print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
				}
				else
				{
					print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
				}
			}
		}

		print '</div>'."\n";


	}
	if ($action == 'edit')
	{
		print_fiche_titre($langs->trans('Configuration tax bill'));



		print '<form name="add" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$id.'">';

		print '<table class="border" width="100%">';

	// Country
		//print '<tr><td class="fieldrequired" ><label for="selectcountry_id">'.$langs->trans('Country').'</label></td><td colspan="2" class="maxwidthonsmartphone">';
		//print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$object->fk_pays));
		//if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
		//print '</td></tr>';
		//print '</td>';
		//print '</tr>';

	//code_facture
		print '<tr><td valign="top" class="fieldrequired">'.$langs->trans('Typefacture').'</td><td colspan="2">';
		$typefilter=null;
		print $form->load_type_facture('code_facture',(GETPOST('code_facture')?GETPOST('code_facture'):$object->code_facture), 1, 'code', false,$typefilter);
		print '</td></tr>'."\n";

	//code_tva
		print '<tr><td valign="top" class="fieldrequired">'.$langs->trans('Codetva').'</td><td colspan="2">';
		print $form->load_type_tva('code_tva',(GETPOST('code_tva')?GETPOST('code_tva'):$object->code_tva), 1,'code', false);
		print '</td></tr>'."\n";

	// tax
		print '<tr><td>'.$langs->trans('Taux').'</td><td><input  name="taux" value="'.(GETPOST('taux')?GETPOST('taux'):$object->taux).'" type="number" step="any" min="0"></td></tr>';

	// Register mode
		print '<tr><td class="fieldrequired">'.$langs->trans('Automaticcalculation').'</td><td>';
		print $form->selectyesno('register_mode',(GETPOST('register_mode')?GETPOST('register_mode'):$object->register_mode),1);
		print '</td></tr>';

	// Note
		print '<tr><td>'.$langs->trans('Note').'</td><td>';
		$doleditor = new DolEditor('note', (GETPOST('note')?GETPOST('note'):$object->note), '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
		print $doleditor->Create(1);
		print '</td></tr>';

	// Accountancy
		print '<tr><td class="nowrap">'.$langs->trans('Accountancycode').'</td><td colspan="2">';
		print '<input type="text" name="accountancy_code" value="'.(GETPOST('accountancy_code')?GETPOST('accountancy_code'):$object->accountancy_code).'">';
		print '</td></tr>';

		// Againstaccount
		print '<tr><td class="nowrap">'.$langs->trans('Againstaccount').'</td><td colspan="2">';
		print '<input type="text" name="against_account" value="'.(GETPOST('against_account')?GETPOST('against_account'):$object->against_account).'">';
		print '</td></tr>';

	// Bouton "Create Draft"
		print "</table>\n";

		print '<br><center><input type="submit" class="button" name="bouton" value="'.$langs->trans('Save').'"><input type="submit" class="butActiondelete" name="cancel" value="'.$langs->trans('Cancel').'"></center>';

		print "</form>\n";


	// Show origin lines
		if (is_object($objectsrc))
		{
			print '<br>';

			$title=$langs->trans('ProductsAndServices');
			print_titre($title);

			print '<table class="noborder" width="100%">';

			$objectsrc->printOriginLinesList();

			print '</table>';
		}
	}
}
// End of page
llxFooter();
$db->close();
?>
