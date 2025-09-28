<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *   	\file       contab/contabseat_list.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-09-26 16:39
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
dol_include_once('/contab/class/contabseatext.class.php');
dol_include_once('/contab/class/contabtransaction.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

if (!$user->rights->contab->seat->read)
	accessforbidden();

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$seatyear=GETPOST("seatyear","int");
$seatmonth=GETPOST("seatmonth","int");
$seatday=GETPOST("seatday","int");

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_lote=GETPOST('search_lote','alpha');
$search_sblote=GETPOST('search_sblote','alpha');
$search_doc=GETPOST('search_doc','alpha');
$search_currency=GETPOST('search_currency','alpha');
$search_type_seat=GETPOST('search_type_seat','int');
$search_type_numeric=GETPOST('search_type_numeric','alpha');
$search_sequential=GETPOST('search_sequential','alpha');
$search_seat_month=GETPOST('search_seat_month','alpha');
$search_seat_year=GETPOST('search_seat_year','int');
$search_document_backing=GETPOST('search_document_backing','alpha');
$search_beneficiary=GETPOST('search_beneficiary','alpha');
$search_spending=GETPOST('search_spending','int');
$search_resource=GETPOST('search_resource','int');
$search_accountant=GETPOST('search_accountant','int');
$search_codtr=GETPOST('search_codtr','alpha');
$search_cbter=GETPOST('search_cbter','alpha');
$search_cbtterone=GETPOST('search_cbtterone','alpha');
$search_debit_total=GETPOST('search_debit_total','alpha');
$search_credit_total=GETPOST('search_credit_total','alpha');
$search_history=GETPOST('search_history','alpha');
$search_manual=GETPOST('search_manual','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
if (isset($_GET['page']) || isset($_POST['page']))
	$page = GETPOST('page','int')+0;
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}
if (!isset($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'seatlist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('contablist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('contab');
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
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>0),
	't.date_seat'=>array('label'=>$langs->trans("Fielddate_seat"), 'checked'=>1),
	't.lote'=>array('label'=>$langs->trans("Fieldlote"), 'checked'=>0),
	't.sblote'=>array('label'=>$langs->trans("Fieldsblote"), 'checked'=>0),
	't.doc'=>array('label'=>$langs->trans("Fielddoc"), 'checked'=>0),
	't.currency'=>array('label'=>$langs->trans("Fieldcurrency"), 'checked'=>0),
	't.type_seat'=>array('label'=>$langs->trans("Fieldtype_seat"), 'checked'=>0),
	't.type_numeric'=>array('label'=>$langs->trans("Fieldtype_numeric"), 'checked'=>0),
	't.sequential'=>array('label'=>$langs->trans("Fieldsequential"), 'checked'=>0),
	't.seat_month'=>array('label'=>$langs->trans("Fieldseat_month"), 'checked'=>0),
	't.seat_year'=>array('label'=>$langs->trans("Fieldseat_year"), 'checked'=>0),
	't.document_backing'=>array('label'=>$langs->trans("Fielddocument_backing"), 'checked'=>1),
	't.beneficiary'=>array('label'=>$langs->trans("Fieldbeneficiary"), 'checked'=>0),
	't.spending'=>array('label'=>$langs->trans("Fieldspending"), 'checked'=>0),
	't.resource'=>array('label'=>$langs->trans("Fieldresource"), 'checked'=>0),
	't.accountant'=>array('label'=>$langs->trans("Fieldaccountant_"), 'checked'=>0),
	't.codtr'=>array('label'=>$langs->trans("Fieldcodtr"), 'checked'=>0),
	't.cbter'=>array('label'=>$langs->trans("Fieldcbter"), 'checked'=>0),
	't.cbtterone'=>array('label'=>$langs->trans("Fieldcbtterone"), 'checked'=>0),
	't.debit_total'=>array('label'=>$langs->trans("Fielddebit_total"), 'checked'=>1),
	't.credit_total'=>array('label'=>$langs->trans("Fieldcredit_total"), 'checked'=>1),
	't.history'=>array('label'=>$langs->trans("Fieldhistory"), 'checked'=>1),
	't.manual'=>array('label'=>$langs->trans("Fieldmanual"), 'checked'=>0),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>1),


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
$object=new Contabseatext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref,$period_year);
	if ($result < 0) dol_print_error($db);
}

$objTransaction = new Contabtransaction($db);
$aStatus = array(0=>$langs->trans('Draft'),1=>$langs->trans('Validated'));
$aTypeseat = array(1=>$langs->trans('Egreso'),2=>$langs->trans('Ingreso'),3=>$langs->trans('Traspaso'));


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
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{

		$search_entity='';
		$search_ref='';
		$search_ref_ext='';
		$search_lote='';
		$search_sblote='';
		$search_doc='';
		$search_currency='';
		$search_type_seat='';
		$search_type_numeric='';
		$search_sequential='';
		$search_seat_month='';
		$search_seat_year='';
		$search_document_backing='';
		$search_beneficiary='';
		$search_spending='';
		$search_resource='';
		$search_accountant='';
		$search_codtr='';
		$search_cbter='';
		$search_cbtterone='';
		$search_debit_total='';
		$search_credit_total='';
		$search_history='';
		$search_manual='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_status='';
		$seatyear = '';
		$seatmonth = '';
		$seatday = '';

		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

	// Mass actions
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);
$formother = new Formother($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Seats');

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
$sql .= " t.ref,";
$sql .= " t.ref_ext,";
$sql .= " t.date_seat,";
$sql .= " t.lote,";
$sql .= " t.sblote,";
$sql .= " t.doc,";
$sql .= " t.currency,";
$sql .= " t.type_seat,";
$sql .= " t.type_numeric,";
$sql .= " t.sequential,";
$sql .= " t.seat_month,";
$sql .= " t.seat_year,";
$sql .= " t.document_backing,";
$sql .= " t.beneficiary,";
$sql .= " t.spending,";
$sql .= " t.resource,";
$sql .= " t.accountant,";
$sql .= " t.codtr,";
$sql .= " t.codtrone,";
$sql .= " t.cbter,";
$sql .= " t.debit_total,";
$sql .= " t.credit_total,";
$sql .= " t.history,";
$sql .= " t.manual,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_seat_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//vamos a filtrar por gestión
$sql.= " AND YEAR(t.date_seat) = ".$period_year;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_lote) $sql.= natural_search("lote",$search_lote);
if ($search_sblote) $sql.= natural_search("sblote",$search_sblote);
if ($search_doc) $sql.= natural_search("doc",$search_doc);
if ($search_currency) $sql.= natural_search("currency",$search_currency);
if ($search_type_seat) $sql.= natural_search("type_seat",$search_type_seat);
if ($search_type_numeric) $sql.= natural_search("type_numeric",$search_type_numeric);
if ($search_sequential) $sql.= natural_search("sequential",$search_sequential);
if ($search_seat_month) $sql.= natural_search("seat_month",$search_seat_month);
if ($search_seat_year) $sql.= natural_search("seat_year",$search_seat_year);
if ($search_document_backing) $sql.= natural_search("document_backing",$search_document_backing);
if ($search_beneficiary) $sql.= natural_search("beneficiary",$search_beneficiary);
if ($search_spending) $sql.= natural_search("spending",$search_spending);
if ($search_resource) $sql.= natural_search("resource",$search_resource);
if ($search_accountant) $sql.= natural_search("accountant",$search_accountant);
if ($search_codtr) $sql.= natural_search("codtr",$search_codtr);
if ($search_cbter) $sql.= natural_search("cbter",$search_cbter);
if ($search_cbtterone) $sql.= natural_search("cbtterone",$search_cbtterone);
if ($search_debit_total) $sql.= natural_search("debit_total",$search_debit_total);
if ($search_credit_total) $sql.= natural_search("credit_total",$search_credit_total);
if ($search_history) $sql.= natural_search("history",$search_history);
if ($search_manual) $sql.= natural_search("manual",$search_manual);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_status!= -1) $sql.= natural_search("status",$search_status);
if ($seatmonth > 0)
{
	if ($seatyear > 0 && empty($seatday))
		$sql.= " AND t.date_seat BETWEEN '".$db->idate(dol_get_first_day($seatyear,$seatmonth,false))."' AND '".$db->idate(dol_get_last_day($seatyear,$seatmonth,false))."'";
	else if ($seatyear > 0 && ! empty($seatday))
		$sql.= " AND t.date_seat BETWEEN '".$db->idate(dol_mktime(0, 0, 0, $seatmonth, $seatday, $seatyear))."' AND '".$db->idate(dol_mktime(23, 59, 59, $seatmonth, $seatday, $seatyear))."'";
	else
		$sql.= " AND date_format(t.date_seat, '%m') = '".$seatmonth."'";
}
else if ($seatyear > 0)
{
	$sql.= " AND t.date_seat BETWEEN '".$db->idate(dol_get_first_day($seatyear,1,false))."' AND '".$db->idate(dol_get_last_day($seatyear,12,false))."'";
}


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
	header("Location: ".DOL_URL_ROOT.'/contabseat/card.php?id='.$id);
	exit;
}
/*
*view
 */

llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

foreach ($arrayfields as $key => $value) {
	if (!empty($arrayfields[$key]['checked'])) {
		$aKey = explode('.',$key);
		$key2 = str_replace('t.', '', $key);
		$key1 = $aKey[0];
		$key2 = $aKey[1];
		$variable = 'search_'.$key2;
		$valVariable = GETPOST($variable);
		if (GETPOST($variable) != '') $param.= '&amp;search_'.$key2.'='.urlencode($valVariable);
	}
}
if ($seatday>0) $param.= '&amp;seatday='.urlencode($seatday);
if ($seatmonth>0) $param.= '&amp;seatmonth='.urlencode($seatmonth);
if ($seatyear>0) $param.= '&amp;seatyear='.urlencode($seatyear);

//if ($search_field1 != '') $param.= '&amp;search_field1='.urlencode($search_field1);
//if ($search_field2 != '') $param.= '&amp;search_field2='.urlencode($search_field2);
if ($optioncss != '') $param.='&optioncss='.$optioncss;
// Add $param from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
}
$params = $param;
$arrayofmassactions =  array(
	'presend'=>$langs->trans("SendByMail"),
	'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->contab->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_seat']['checked'])) print_liste_field_titre($arrayfields['t.date_seat']['label'],$_SERVER['PHP_SELF'],'t.date_seat','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lote']['checked'])) print_liste_field_titre($arrayfields['t.lote']['label'],$_SERVER['PHP_SELF'],'t.lote','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sblote']['checked'])) print_liste_field_titre($arrayfields['t.sblote']['label'],$_SERVER['PHP_SELF'],'t.sblote','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.doc']['checked'])) print_liste_field_titre($arrayfields['t.doc']['label'],$_SERVER['PHP_SELF'],'t.doc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.currency']['checked'])) print_liste_field_titre($arrayfields['t.currency']['label'],$_SERVER['PHP_SELF'],'t.currency','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_seat']['checked'])) print_liste_field_titre($arrayfields['t.type_seat']['label'],$_SERVER['PHP_SELF'],'t.type_seat','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_numeric']['checked'])) print_liste_field_titre($arrayfields['t.type_numeric']['label'],$_SERVER['PHP_SELF'],'t.type_numeric','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sequential']['checked'])) print_liste_field_titre($arrayfields['t.sequential']['label'],$_SERVER['PHP_SELF'],'t.sequential','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.seat_month']['checked'])) print_liste_field_titre($arrayfields['t.seat_month']['label'],$_SERVER['PHP_SELF'],'t.seat_month','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.seat_year']['checked'])) print_liste_field_titre($arrayfields['t.seat_year']['label'],$_SERVER['PHP_SELF'],'t.seat_year','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.document_backing']['checked'])) print_liste_field_titre($arrayfields['t.document_backing']['label'],$_SERVER['PHP_SELF'],'t.document_backing','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.beneficiary']['checked'])) print_liste_field_titre($arrayfields['t.beneficiary']['label'],$_SERVER['PHP_SELF'],'t.beneficiary','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.spending']['checked'])) print_liste_field_titre($arrayfields['t.spending']['label'],$_SERVER['PHP_SELF'],'t.spending','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.resource']['checked'])) print_liste_field_titre($arrayfields['t.resource']['label'],$_SERVER['PHP_SELF'],'t.resource','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.accountant']['checked'])) print_liste_field_titre($arrayfields['t.accountant']['label'],$_SERVER['PHP_SELF'],'t.accountant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.codtr']['checked'])) print_liste_field_titre($arrayfields['t.codtr']['label'],$_SERVER['PHP_SELF'],'t.codtr','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cbter']['checked'])) print_liste_field_titre($arrayfields['t.cbter']['label'],$_SERVER['PHP_SELF'],'t.cbter','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cbtterone']['checked'])) print_liste_field_titre($arrayfields['t.cbtterone']['label'],$_SERVER['PHP_SELF'],'t.cbtterone','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.debit_total']['checked'])) print_liste_field_titre($arrayfields['t.debit_total']['label'],$_SERVER['PHP_SELF'],'t.debit_total','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.credit_total']['checked'])) print_liste_field_titre($arrayfields['t.credit_total']['label'],$_SERVER['PHP_SELF'],'t.credit_total','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.history']['checked'])) print_liste_field_titre($arrayfields['t.history']['label'],$_SERVER['PHP_SELF'],'t.history','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.manual']['checked'])) print_liste_field_titre($arrayfields['t.manual']['label'],$_SERVER['PHP_SELF'],'t.manual','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['t.date_seat']['checked']))
{
	print '<td class="liste_titre" align="center">';
	print '<input class="flat" type="text" size="1" maxlength="2" name="seatday" value="'.$seatday.'">';
	print '<input class="flat" type="text" size="1" maxlength="2" name="seatmonth" value="'.$seatmonth.'">';
	$formother->select_year($seatyear?$seatyear:-1,'seatyear',1, 20, 5);
	print '</td>';
}
if (! empty($arrayfields['t.lote']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lote" value="'.$search_lote.'" size="10"></td>';
if (! empty($arrayfields['t.sblote']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sblote" value="'.$search_sblote.'" size="10"></td>';
if (! empty($arrayfields['t.doc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_doc" value="'.$search_doc.'" size="10"></td>';
if (! empty($arrayfields['t.currency']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_currency" value="'.$search_currency.'" size="10"></td>';
if (! empty($arrayfields['t.type_seat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_seat" value="'.$search_type_seat.'" size="10"></td>';
if (! empty($arrayfields['t.type_numeric']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_numeric" value="'.$search_type_numeric.'" size="10"></td>';
if (! empty($arrayfields['t.sequential']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sequential" value="'.$search_sequential.'" size="10"></td>';
if (! empty($arrayfields['t.seat_month']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_seat_month" value="'.$search_seat_month.'" size="10"></td>';
if (! empty($arrayfields['t.seat_year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_seat_year" value="'.$search_seat_year.'" size="10"></td>';
if (! empty($arrayfields['t.document_backing']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_document_backing" value="'.$search_document_backing.'" size="10"></td>';
if (! empty($arrayfields['t.beneficiary']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_beneficiary" value="'.$search_beneficiary.'" size="10"></td>';
if (! empty($arrayfields['t.spending']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_spending" value="'.$search_spending.'" size="10"></td>';
if (! empty($arrayfields['t.resource']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_resource" value="'.$search_resource.'" size="10"></td>';
if (! empty($arrayfields['t.accountant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_accountant" value="'.$search_accountant.'" size="10"></td>';
if (! empty($arrayfields['t.codtr']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_codtr" value="'.$search_codtr.'" size="10"></td>';
if (! empty($arrayfields['t.cbter']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cbter" value="'.$search_cbter.'" size="10"></td>';
if (! empty($arrayfields['t.cbtterone']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cbtterone" value="'.$search_cbtterone.'" size="10"></td>';
if (! empty($arrayfields['t.debit_total']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_debit_total" value="'.$search_debit_total.'" size="10"></td>';
if (! empty($arrayfields['t.credit_total']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_credit_total" value="'.$search_credit_total.'" size="10"></td>';
if (! empty($arrayfields['t.history']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_history" value="'.$search_history.'" size="10"></td>';
if (! empty($arrayfields['t.manual']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_manual" value="'.$search_manual.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked']))
{
	print '<td class="liste_titre">';
	print $form->selectarray('search_status',$aStatus,$search_status,1);
	print '</td>';
}

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
		$object->ref = $obj->ref;
		$object->label = $obj->history;
		$object->status = $obj->status;
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			$lAmount = false;
			if (!empty($arrayfields[$key]['checked'])) {
				$aKey = explode('.',$key);
				$key2 = str_replace('t.', '', $key);
				$key2 = $aKey[1];
				if ($key2 == 'date_seat')
					$obj->$key2 = dol_print_date($db->jdate($obj->date_seat),'day');
				if ($key2 == 'ref')
				{
					$object->id = $obj->rowid;
					$object->ref = $obj->ref;
					$object->label = $obj->label;
					$obj->$key2 = $object->getNomUrl();
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'codtr')
				{
					$rest = $objTransaction->fetch(0,$obj->$key2);
					if ($rest>0)
						$obj->$key2 = $objTransaction->getNomUrl();
				}
				if ($key2 == 'debit_total' || $key2 =='credit_total')
				{
					$lAmount = true;
				}
				if ($key2 == 'type_seat')
					$obj->$key2 = $aTypeseat[$obj->$key2];
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($lAmount)
				{
					print '<td align="right">' . price(price2num($obj->$key2,'MT')) . '</td>';
				}
				else
					print '<td>' . $obj->$key2 . '</td>';
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

		// Action column
		print '<td class="nowrap" align="center">';
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
