<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2017      Nicolas ZABOURI	<info@inovea-conseil.com>
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
 *   	\file       fiscal/tvadef_list.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-01-25 08:29
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
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
dol_include_once('/fiscal/class/tvadefext.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$search_entity=GETPOST('search_entity','int');
$search_fk_pays=GETPOST('search_fk_pays','int');
$search_code_facture=GETPOST('search_code_facture','alpha');
$search_code_tva=GETPOST('search_code_tva','alpha');
$search_taux=GETPOST('search_taux','alpha');
$search_register_mode=GETPOST('search_register_mode','int');
$search_deductible=GETPOST('search_deductible','int');
$search_note=GETPOST('search_note','alpha');
$search_active=GETPOST('search_active','int');
$search_accountancy_code=GETPOST('search_accountancy_code','alpha');
$search_against_account=GETPOST('search_against_account','alpha');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
if (empty($page)) $page=0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid";
// Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}
if (!$user->rights->fiscal->conf->leer)
{

	accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'fiscallist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('fiscallist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('fiscal');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
	't.fk_pays'=>array('label'=>$langs->trans("Fieldfk_pays"), 'checked'=>1),
	't.code_facture'=>array('label'=>$langs->trans("Fieldcode_facture"), 'checked'=>1),
	't.code_tva'=>array('label'=>$langs->trans("Fieldcode_tva"), 'checked'=>1),
	't.taux'=>array('label'=>$langs->trans("Fieldtaux"), 'checked'=>1),
	't.register_mode'=>array('label'=>$langs->trans("Fieldregister_mode"), 'checked'=>1),
	//'t.deductible'=>array('label'=>$langs->trans("Fielddeductible"), 'checked'=>1),
	't.note'=>array('label'=>$langs->trans("Fieldnote"), 'checked'=>1),
	't.accountancy_code'=>array('label'=>$langs->trans("Fieldaccountancy_code"), 'checked'=>1),
	't.against_account'=>array('label'=>$langs->trans("Fieldagainst_account"), 'checked'=>1),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
	//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
);
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	}
}


// Load object if id or ref is provided as parameter
$object=new Tvadefext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objUser = new User($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x")
		|| GETPOST("button_removefilter.x")
		||GETPOST("button_removefilter"))
	// All tests are required to be compatible with all browsers
	{

		$search_entity='';
		$search_fk_pays='';
		$search_code_facture='';
		$search_code_tva='';
		$search_taux='';
		$search_register_mode='';
		$search_deductible='';
		$search_note='';
		$search_active='';
		$search_accountancy_code='';
		$search_against_account='';


		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}
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
		else
			setEventMessages($langs->trans('Actualización satisfactoria'),null,'mesgs');
		$action = 'list';
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Configurationtaxbill');

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


$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.entity,";
$sql .= " t.fk_pays,";
$sql .= " t.code_facture,";
$sql .= " t.code_tva,";
$sql .= " t.taux,";
$sql .= " t.register_mode,";
//$sql .= " t.deductible,";
$sql .= " t.note,";
$sql .= " t.active,";
$sql .= " t.accountancy_code,";
$sql .= " t.against_account,";
$sql.= " ctf.code AS code_typefacture,";
$sql.= " ctf.label AS label_typefacture,";
$sql.= " ctt.code AS code_typetva,";
$sql.= " ctt.label AS label_typetva";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."tva_def as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_facture as ctf ON t.code_facture = ctf.code AND t.fk_pays = ctf.fk_pays ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_tva as ctt ON t.code_tva = ctt.code AND t.fk_pays = ctt.fk_pays ";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."tva_def_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_pays) $sql.= natural_search("fk_pays",$search_fk_pays);
if ($search_code_facture) $sql.= natural_search("code_facture",$search_code_facture);
if ($search_code_tva) $sql.= natural_search("code_tva",$search_code_tva);
if ($search_taux) $sql.= natural_search("taux",$search_taux);
if ($search_register_mode) $sql.= natural_search("register_mode",$search_register_mode);
if ($search_deductible) $sql.= natural_search("deductible",$search_deductible);
if ($search_note) $sql.= natural_search("note",$search_note);
if ($search_active) $sql.= natural_search("active",$search_active);
if ($search_accountancy_code) $sql.= natural_search("accountancy_code",$search_accountancy_code);
if ($search_against_account) $sql.= natural_search("against_account",$search_against_account);


if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);
// Add where from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	$typ=$extrafields->attribute_type[$tmpkey];
	$mode=0;
	if (in_array($typ, array('int','double'))) $mode=1;    // Search on a numeric
	if ($val && ( ($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit)))
	{
		$sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
	}
}
// Add where from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.=$db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit+1, $offset);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all)
{
	$obj = $db->fetch_object($resql);
	$id = $obj->rowid;
	header("Location: ".DOL_URL_ROOT.'/tvadef/card.php?id='.$id);
	exit;
}

llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_field1 != '') $param.= '&amp;search_field1='.urlencode($search_field1);
if ($search_field2 != '') $param.= '&amp;search_field2='.urlencode($search_field2);
if ($optioncss != '') $param.='&optioncss='.$optioncss;
// Add $param from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
}

$arrayofmassactions =  array(
	'presend'=>$langs->trans("SendByMail"),
	'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->fiscal->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';
//$moreforfilter.='<div class="divsearchfield">';
//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
//$moreforfilter.= '</div>';

$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
else $moreforfilter = $hookmanager->resPrint;

if (! empty($moreforfilter))
{
	print '<div class="liste_titre liste_titre_bydiv centpercent">';
	print $moreforfilter;
	print '</div>';
}

$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.rowid','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_pays']['checked'])) print_liste_field_titre($arrayfields['t.fk_pays']['label'],$_SERVER['PHP_SELF'],'t.fk_pays','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_facture']['checked'])) print_liste_field_titre($arrayfields['t.code_facture']['label'],$_SERVER['PHP_SELF'],'t.code_facture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_tva']['checked'])) print_liste_field_titre($arrayfields['t.code_tva']['label'],$_SERVER['PHP_SELF'],'t.code_tva','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.taux']['checked'])) print_liste_field_titre($arrayfields['t.taux']['label'],$_SERVER['PHP_SELF'],'t.taux','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.register_mode']['checked'])) print_liste_field_titre($arrayfields['t.register_mode']['label'],$_SERVER['PHP_SELF'],'t.register_mode','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.deductible']['checked'])) print_liste_field_titre($arrayfields['t.deductible']['label'],$_SERVER['PHP_SELF'],'t.deductible','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note']['checked'])) print_liste_field_titre($arrayfields['t.note']['label'],$_SERVER['PHP_SELF'],'t.note','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.accountancy_code']['checked'])) print_liste_field_titre($arrayfields['t.accountancy_code']['label'],$_SERVER['PHP_SELF'],'t.accountancy_code','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.against_account']['checked'])) print_liste_field_titre($arrayfields['t.against_account']['label'],$_SERVER['PHP_SELF'],'t.against_account','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,'',$sortfield,$sortorder);
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
$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

// Fields title search
print '<tr class="liste_titre">';
//
print '<td class="liste_titre"><input type="text" class="flat" name="search_rowid" value="'.$search_rowid.'" size="5"></td>';
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="5"></td>';
if (! empty($arrayfields['t.fk_pays']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_pays" value="'.$search_fk_pays.'" size="10"></td>';
if (! empty($arrayfields['t.code_facture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_facture" value="'.$search_code_facture.'" size="10"></td>';
if (! empty($arrayfields['t.code_tva']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_tva" value="'.$search_code_tva.'" size="10"></td>';
if (! empty($arrayfields['t.taux']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_taux" value="'.$search_taux.'" size="10"></td>';
if (! empty($arrayfields['t.register_mode']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_register_mode" value="'.$search_register_mode.'" size="10"></td>';
if (! empty($arrayfields['t.deductible']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_deductible" value="'.$search_deductible.'" size="10"></td>';
if (! empty($arrayfields['t.note']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note" value="'.$search_note.'" size="10"></td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';
if (! empty($arrayfields['t.accountancy_code']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_accountancy_code" value="'.$search_accountancy_code.'" size="10"></td>';
if (! empty($arrayfields['t.against_account']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_against_account" value="'.$search_against_account.'" size="10"></td>';

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
$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
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
/*if (! empty($arrayfields['u.statut']['checked']))
{
	// Status
	print '<td class="liste_titre" align="center">';
	print $form->selectarray('search_statut', array('-1'=>'','0'=>$langs->trans('Disabled'),'1'=>$langs->trans('Enabled')),$search_statut);
	print '</td>';
}*/
// Action column
print '<td class="liste_titre" align="right">';
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
print $searchpitco;
print '</td>';
print '</tr>'."\n";


$i=0;
$var=true;
$totalarray=array();
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		$object->id = $obj->rowid;
		$object->ref = $obj->rowid;
		// Country
		$tmparray=getCountry($obj->fk_pays,'all');
		$object->country_code=$tmparray['code'];
		$object->country=$tmparray['label'];

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		print '<td>' . $object->getNomUrladd(1) . '</td>';

		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				$lAlign=false;
				$lFormat = false;
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$object->id = $obj->rowid;
					$object->ref = $obj->ref;
					$object->label = $obj->label;
					$obj->$key2 = $object->getNomUrladd();
				}
				if ($key2 == 'fk_pays')
				{
					if (! empty($object->country_code))
					{
						$img=picto_from_langcode($object->country_code);
						//$img='';
						if ($object->isInEEC())
							$obj->$key2 = $form->textwithpicto(($img?$img.' ':'').$object->fy_pays,$langs->trans("CountryIsInEEC"),1,0);
						else
							$obj->$key2 = ($img?$img.' ':'').$object->country;
					}

				}
				if ($key2 == 'active')
				{
					$img = $obj->$key2?'switch_on':'switch_off';
					$switch = $obj->$key2?'noval':'val';
					$label = $obj->$key2?$langs->trans('Active'):$langs->trans('Deactivated');
					if ($user->rights->fiscal->conf->val)
					{
						$obj->$key2 = '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'&action='.$switch.'">'.img_picto($langs->trans($label),$img).'</a>';
					}
					else
					{
						$obj->$key2 = img_picto('',$img);
					}

				}
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($key2 == 'code_facture')
				{
					$obj->$key2 = $obj->label_typefacture;
				}
				if ($key2 == 'code_tva')
				{
					$obj->$key2 = $obj->label_typetva;
				}
				if ($key2 == 'register_mode')
				{
					$lAlign=2;
					$obj->$key2 = ($obj->$key2==1?$langs->trans('Yes'):$langs->trans('Not'));
				}
				if ($key2 == 'deductible')
				{
					$lAlign=2;
					$obj->$key2 = ($obj->$key2==1?$langs->trans('Yes'):$langs->trans('Not'));
				}
				if ($key2 == 'taux')
				{
					$lAlign=true;
					$lFormat = true;
				}

				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}
				$align = '';
				if ($lAlign) $align = ($lAlign==1?' align="right"': ' align="center"');
					print '<td '.$align.'>' . ($lFormat?price(price2num($obj->$key2,'MT')):$obj->$key2) . '</td>';
					if (!$i)
						$totalarray['nbfield'] ++;
				}
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
		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
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
		// Status
		/*
		if (! empty($arrayfields['u.statut']['checked']))
		{
		  $userstatic->statut=$obj->statut;
		  print '<td align="center">'.$userstatic->getLibStatut(3).'</td>';
		}*/

		// Action column
		print '<td class="nowrap" align="center">';
		//if ($massactionbutton || $massaction)
		   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
		//{
		//	$selected=0;
		//	if (in_array($obj->rowid, $arrayofselected)) $selected=1;
		//	print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
		//}
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
	}
	$i++;
}

// Show total line
if (isset($totalarray['totalhtfield']))
{
	print '<tr class="liste_total">';
	$i=0;
	while ($i < $totalarray['nbfield'])
	{
		$i++;
		if ($i == 1)
		{
			if ($num < $limit && empty($offset)) print '<td align="left">'.$langs->trans("Total").'</td>';
			else print '<td align="left">'.$langs->trans("Totalforthispage").'</td>';
		}
		elseif ($totalarray['totalhtfield'] == $i) print '<td align="right">'.price($totalarray['totalht']).'</td>';
		elseif ($totalarray['totalvatfield'] == $i) print '<td align="right">'.price($totalarray['totalvat']).'</td>';
		elseif ($totalarray['totalttcfield'] == $i) print '<td align="right">'.price($totalarray['totalttc']).'</td>';
		else print '<td></td>';
	}
	print '</tr>';
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";


// End of page
llxFooter();
$db->close();
