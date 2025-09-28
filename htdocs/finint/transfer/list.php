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
 *   	\file       /request/card_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-13 18:11
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');

dol_include_once('/finint/class/requestcashext.class.php');
dol_include_once('/finint/class/requestcashdet.class.php');
dol_include_once('/finint/class/requestcashdeplacementext.class.php');
if (! empty($conf->monprojet->enabled))
{
	dol_include_once('/monprojet/class/projectext.class.php');
	dol_include_once('/monprojet/class/taskext.class.php');
	dol_include_once('/monprojet/class/html.formprojetext.class.php');
	dol_include_once('/monprojet/class/html.formtask.class.php');
}

if (! empty($conf->societe->enabled))
{
	require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/finint/class/accountuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

dol_include_once('/monprojet/lib/verifcontact.lib.php');
dol_include_once('/finint/lib/utils.lib.php');
dol_include_once('/finint/lib/finint.lib.php');
dol_include_once('/finint/lib/discharg.lib.php');

require_once(DOL_DOCUMENT_ROOT."/core/lib/bank.lib.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/sociales/class/chargesociales.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/tva/class/tva.class.php");

require_once(DOL_DOCUMENT_ROOT."/finint/class/deplacementext.class.php");
require_once(DOL_DOCUMENT_ROOT."/finint/class/accountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/finint/core/modules/finint/modules_finint.php");
require_once(DOL_DOCUMENT_ROOT."/finint/lib/account.lib.php");
require_once(DOL_DOCUMENT_ROOT."/finint/lib/doc.lib.php");
dol_include_once('/finint/class/request/carddet.class.php');


//images
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

// Load traductions files requiredby by page
$langs->load("finint");
$langs->load("companies");
$langs->load("other");
$langs->load("errors");
$langs->load("admin");
$langs->load("holiday");
$langs->load("bills");
$langs->load('banks');
$langs->load('main');
// Get parameters
$id			= GETPOST('id','int');//id finint_cash
$idrcd		= GETPOST('idrcd','int');//id deplacement
$ref		= GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$confirm   	= GETPOST('confirm','alpha');
$backtopage	= GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_proj=GETPOST('search_proj','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_account=GETPOST('search_fk_account','int');
$search_user=GETPOST('search_user','alpha');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_detail=GETPOST('search_detail','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_amount_authorized=GETPOST('search_amount_authorized','alpha');
$search_status=GETPOST('search_status','int');

$sortfield = GETPOST("sortfield","alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;

if (!$user->rights->finint->trans->leer) accessforbidden();

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="DESC";
$offset = $conf->liste_limit * $page ;

$aStatut = array(9=>$langs->trans('All'),
	-1=>$langs->trans('Torefused'),
	0=>$langs->trans('Draft'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Approved'),
	3=>$langs->trans('Disbursed'),
	4=>$langs->trans('Approveclosure'),
	5=>$langs->trans('Closed'),);

// Purge criteria
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_ref='';
	$search_proj="";
	$search_user="";
	$search_detail = "";
	$search_status="";
}

if ($conf->societe->enabled)
	$soc = new Societe($db);
if ($conf->monprojet->enabled)
	$projet = new Project($db);


// Protection if external user
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	$soc->fetch($user->societe_id);
	//accessforbidden();
}

//if (empty($action) && empty($id) && empty($ref)) $action='create';

// Load object if id or ref is provided as parameter
$object 		= new Requestcashext($db);
$objectdet 		= new Requestcashdet($db);
$objaccount 	= new Account($db);
$categorie 		= new Categorie($db);
$deplacement 	= new Requestcashdeplacementext($db);

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$resultp=$object->fetch($id,(!empty($ref)?$ref:null));
	if ($resultp < 0) dol_print_error($db);
	//verificamos la cuenta del requerimiento
	$objaccount->fetch($object->fk_account);
	$minallowed = $objaccount->min_allowed;
	$maxallowed = $object->amount;
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('request/card'));
$objuser = new User($db);
$extrafields = new ExtraFields($db);

if ($conf->monprojet->enabled)
{
	$formproject = new FormProjetsext($db);
	$formtask = new FormTask($db);
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$morecss=array('/finint/css/style.css','/finint/css/bootstrap.min.css');
$morecss=array('/finint/css/style.css');
$morejs=array("/finint/js/finint.js");
llxHeader('',$langs->trans('Request'),'','','','',$morejs,$morecss,0,0);

$form=new Formv($db);
$formfile=new Formfile($db);

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


// Part to show a list
//if ($action == 'list' || (empty($id) && empty($ref)))
if (empty($action))
{
	$object = new Requestcashext($db);
	//$objectdep = new Deplacementext($db);
	// Put here content of your page
	print load_fiche_titre($langs->trans('Requestcash'));

	$sql = "SELECT";
	$sql.= " t.rowid,";
	$sql.= " t.rowid AS id,";

	$sql .= " t.entity,";
	$sql .= " t.ref,";
	$sql .= " t.fk_projet,";
	$sql .= " t.fk_account,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.detail,";
	$sql .= " t.amount,";
	$sql .= " t.amount_authorized,";
	$sql .= " t.date_create,";
	$sql .= " t.date_delete,";
	$sql .= " t.tms,";
	$sql .= " t.status, ";
	$sql .= " p.title AS titleprojet, p.ref AS refprojet, p.description, ";
	$sql .= " u.lastname, u.firstname, u.login ";
	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."request_cash as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user as u ON t.fk_user_create = u.rowid ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet as p ON t.fk_projet = p.rowid ";
	$sql.= " WHERE t.entity = ".$conf->entity;
	//filtro por tipo usuario y permiso
	if (!$user->admin && !$user->rights->finint->efe->all)
		$sql.= " AND (t.fk_user_create = ".$user->id." OR t.fk_user_assigned = ".$user->id.")";

	if ($search_entity) $sql.= natural_search("t.entity",$search_entity);
	if ($search_ref) $sql.= natural_search("t.ref",$search_ref);
	if ($search_proj) $sql.= natural_search(array("p.ref","p.title","p.description"),$search_proj);
	if ($search_fk_projet) $sql.= natural_search("t.fk_projet",$search_fk_projet);
	if ($search_fk_account) $sql.= natural_search("t.fk_account",$search_fk_account);
	if ($search_user) $sql.= natural_search(array("u.lastname","u.firstname","u.login"),$search_user);
	if ($search_fk_user_mod) $sql.= natural_search("t.fk_user_mod",$search_fk_user_mod);
	if ($search_detail) $sql.= natural_search("t.detail",$search_detail);
	if ($search_amount) $sql.= natural_search("t.amount",$search_amount);
	if ($search_amount_authorized) $sql.= natural_search("t.amount_authorized",$search_amount_authorized);
	if (isset($search_status) && $search_status != 9) $sql.= natural_search("t.status",$search_status);

	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;

	// Count total nb of records
	$nbtotalofrecords = 0;

	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}

	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);
	//echo $sql;
	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		$params='';
		$params='&amp;action=list';
		$params.= '&amp;search_ref='.urlencode($search_ref);
		$params.= '&amp;search_proj='.urlencode($search_proj);
		$params.= '&amp;search_user='.urlencode($search_user);
		$params.= '&amp;search_detail='.urlencode($search_detail);
		$params.= '&amp;search_status='.urlencode($search_status);

		print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');

		print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="list">';
		if (! empty($moreforfilter))
		{
			print '<div class="liste_titre">';
			print $moreforfilter;
			$parameters=array();
			$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;
			print '</div>';
		}

		print '<table class="noborder centpercent">'."\n";

		// Fields title
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Projet'),$_SERVER['PHP_SELF'],'t.fk_projet','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('User'),$_SERVER['PHP_SELF'],'u.lastname','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.date_create','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.detail','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'t.amount','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Authorized'),$_SERVER['PHP_SELF'],'t.amount_authorized','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Discharge'),$_SERVER['PHP_SELF'],'t.amount_authorized','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Balance'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'t.status','',$param,'align="right"',$sortfield,$sortorder);

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";

		// Fields title search
		print '<tr class="liste_titre">';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_proj" value="'.$search_proj.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_user" value="'.$search_user.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_date" value="'.$search_date.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
		print '<td class="liste_titre">&nbsp;</td>';
		print '<td class="liste_titre">&nbsp;</td>';
		print '<td class="liste_titre">&nbsp;</td>';
		print '<td class="liste_titre">&nbsp;</td>';
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print '<td align="right">';

		print $form->selectarray('search_status',$aStatut,$search_status,0);
		print '<br>';
		print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("RemoveFilter"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
		print '</td>';

		print '</tr>'."\n";

		$var = false;
		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$var = !$var;
				//buscamos los gastos
				//$filter = " AND r.fk_finint_cash = ".$obj->id;
				//$objectdep->getlist(0, $filter);
				$totalg = 0;
				$cashdeplac = new Requestcashdeplacementext($db);
				$filter = array(1=>1);
				$filterstatic = " AND t.fk_request_cash = ".$obj->id;
				$filterstatic.= " AND t.concept = 'deplacement'";
				$res = $cashdeplac->fetchAll('ASC','rowid',0,0,$filter,'AND',$filterstatic,true);
				foreach ((array) $cashdeplac->lines AS $j => $lined)
				{
					$totalg+= $lined->amount;
				}


				// You can use here results
				print "<tr $bc[$var]>";

				$object->fetch($obj->id);
				$objtypecash = fetch_typecash($object->fk_type_cash);

				//solo para actualizar informacion de autorized
				if ($object->id == $obj->id && $object->status >= 3)
				{
					//$object->date_authorized = $object->tms;
					//$object->fk_user_authorized = $object->approved;
					//$object->update($user);
					//vamos a buscar con que numero de documento se hizo la transferencia
					if (empty($object->nro_chq))
					{
						$cashdeplac = new Requestcashdeplacementext($db);
						$filter = array(1=>1);
						$filterstatic = " AND t.fk_request_cash_dest = ".$obj->id;
						$filterstatic.= " AND t.concept = 'banktransfert'";
						$res = $cashdeplac->fetchAll('ASC','rowid',0,0,$filter,'AND',$filterstatic,true);
						if (!empty($cashdeplac->fk_bank))
						{
							$objbank = new Accountline($db);
							$objbank->fetch($cashdeplac->fk_bank);
							if ($objbank->id == $cashdeplac->fk_bank)
							{
								$object->nro_chq = $objbank->num_chq;
								$object->update($user);
							}
						}
					}
				}
				print '<td>'.$object->getNomUrl(1).'</td>';
				if ($conf->monprojet->enabled)
					$projet->fetch($obj->fk_projet);

				if ($projet->id == $obj->fk_projet)
					print '<td>'.$projet->getNomUrl(1,'',1).'</td>';
				else
					print '<td>&nbsp;</td>';
				$objuser->fetch($obj->fk_user_create);
				if ($objuser->id == $obj->fk_user_create)
					print '<td>'.$objuser->getNomUrl(1).'</td>';
				else
					print '<td>'.$obj->lastname.' '.$obj->firstname.'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->date_create),'day').'</td>';
				print '<td>'.$obj->detail.'</td>';
				print '<td align="right">'.price($obj->amount).'</td>';
				print '<td align="right">'.price($obj->amount_authorized).'</td>';
				print '<td align="right">'.price($totalg).'</td>';
				$balance = price2num($obj->amount_authorized - $totalg,'MT');
				$style = '';
				if ($balance >0) $style = 'style="color:#ff0000;"';
				print '<td align="right" '.$style.'>'.price(price2num($obj->amount_authorized - $totalg,'MT')).'</td>';

				print '<td align="right">'.$object->getLibStatut($objtypecash->recharge?3:2).'</td>';


				$parameters=array('obj' => $obj);
				$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);
				// Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;
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

	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}

// End of page
llxFooter();
$db->close();
