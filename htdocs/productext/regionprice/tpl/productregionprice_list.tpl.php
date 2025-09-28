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
 *   	\file       productext/productregionprice_list.php
 *		\ingroup    productext
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-12 16:24
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


$search_all=trim(GETPOST("sall"));

$search_entity=GETPOST('search_entity','int');
$search_fk_region_geographic=GETPOST('search_fk_region_geographic','alpha');
$search_fk_soc=GETPOST('search_fk_soc','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_price=GETPOST('search_price','alpha');
$search_quantity=GETPOST('search_quantity','alpha');
$search_remise_percent=GETPOST('search_remise_percent','alpha');
$search_remise=GETPOST('search_remise','alpha');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_default_vat_code=GETPOST('search_default_vat_code','alpha');
$search_info_bits=GETPOST('search_info_bits','int');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_import_key=GETPOST('search_import_key','alpha');

$day=GETPOST('day','int');
$month=GETPOST('month','int');
$year=GETPOST('year','int');

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
if (! $sortfield) $sortfield="t.datec"; // Set here default search field
if (! $sortorder) $sortorder="DESC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'productregionpricelist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('productextlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('productext');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_region_geographic'=>array('label'=>$langs->trans("Fieldfk_region_geographic"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_soc'=>array('label'=>$langs->trans("Fieldfk_soc"), 'align'=>'align="left"', 'checked'=>1),
	't.date_create'=>array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'align'=>'align="left"', 'checked'=>0),
	't.quantity'=>array('label'=>$langs->trans("Fieldquantity"), 'align'=>'align="left"', 'checked'=>1),
	't.price'=>array('label'=>$langs->trans("Fieldprice"), 'align'=>'align="left"', 'checked'=>1),
	't.remise_percent'=>array('label'=>$langs->trans("Fieldremise_percent"), 'align'=>'align="left"', 'checked'=>0),
	't.remise'=>array('label'=>$langs->trans("Fieldremise"), 'align'=>'align="left"', 'checked'=>0),
	't.tva_tx'=>array('label'=>$langs->trans("Fieldtva_tx"), 'align'=>'align="left"', 'checked'=>0),
	't.default_vat_code'=>array('label'=>$langs->trans("Fielddefault_vat_code"), 'align'=>'align="left"', 'checked'=>0),
	't.info_bits'=>array('label'=>$langs->trans("Fieldinfo_bits"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user'=>array('label'=>$langs->trans("Fieldfk_user"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	't.import_key'=>array('label'=>$langs->trans("Fieldimport_key"), 'align'=>'align="left"', 'checked'=>0),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
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
		$search_fk_region_geographic='';
		$search_fk_soc='';
		$search_fk_product='';
		$search_price='';
		$search_quantity='';
		$search_remise_percent='';
		$search_remise='';
		$search_tva_tx='';
		$search_default_vat_code='';
		$search_info_bits='';
		$search_fk_user='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_import_key='';
		$day='';
		$month='';
		$year='';

		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
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
$title = $langs->trans('MyModuleListTitle');

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
$sql .= " t.fk_region_geographic,";
$sql .= " t.fk_soc,";
$sql .= " t.date_create,";
$sql .= " t.tms,";
$sql .= " t.fk_product,";
$sql .= " t.price,";
$sql .= " t.quantity,";
$sql .= " t.remise_percent,";
$sql .= " t.remise,";
$sql .= " t.tva_tx,";
$sql .= " t.default_vat_code,";
$sql .= " t.info_bits,";
$sql .= " t.fk_user,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datem,";
$sql .= " t.import_key ";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."product_region_price as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_region_geographic as rg ON t.fk_region_geographic = rg.rowid";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."societe as a ON t.fk_soc = a.rowid";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_region_price_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_product = ".$id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_region_geographic) $sql.= natural_search(array("fk_region_geographic","rg.ref","rg.label"),$search_fk_region_geographic);
if ($search_fk_soc) $sql.= natural_search(array("fk_soc","a.nom","a.code_fournisseur"),$search_fk_soc);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_price) $sql.= natural_search("price",$search_price);
if ($search_quantity) $sql.= natural_search("quantity",$search_quantity);
if ($search_remise_percent) $sql.= natural_search("remise_percent",$search_remise_percent);
if ($search_remise) $sql.= natural_search("remise",$search_remise);
if ($search_tva_tx) $sql.= natural_search("tva_tx",$search_tva_tx);
if ($search_default_vat_code) $sql.= natural_search("default_vat_code",$search_default_vat_code);
if ($search_info_bits) $sql.= natural_search("info_bits",$search_info_bits);
if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_import_key) $sql.= natural_search("import_key",$search_import_key);
if ($day > 0)
{
	if(strlen($day)==1) $day = '0'.$day;
	$sql.= " AND date_format(t.date_create, '%d') = '$day'";
}
if ($month > 0)
{
	if ($year > 0)
	$sql.= " AND t.date_create BETWEEN '".$db->idate(dol_get_first_day($year,$month,false))."' AND '".$db->idate(dol_get_last_day($year,$month,false))."'";
	else
	$sql.= " AND date_format(t.date_create, '%m') = '$month'";
}
else if ($year > 0)
{
	$sql.= " AND t.date_create BETWEEN '".$db->idate(dol_get_first_day($year,1,false))."' AND '".$db->idate(dol_get_last_day($year,12,false))."'";
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



$arrayofselected=is_array($toselect)?$toselect:array();

$param='&id='.$id;
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_fk_region_geographic != '') $param.= '&amp;search_fk_region_geographic='.urlencode($search_fk_region_geographic);
if ($search_fk_soc != '') $param.= '&amp;search_fk_soc='.urlencode($search_fk_soc);
if ($day != '') $param.= '&amp;day='.urlencode($day);
if ($month != '') $param.= '&amp;month='.urlencode($month);
if ($year != '') $param.= '&amp;year='.urlencode($year);
if ($search_quantity != '') $param.= '&amp;search_quantity='.urlencode($search_quantity);
if ($search_price != '') $param.= '&amp;search_price='.urlencode($search_price);

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
if ($user->rights->productext->regp->del) $arrayofmassactions['delete']=$langs->trans("Delete");
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
print '<input type="hidden" name="id" value="'.$id.'">';

print_barre_liste($langs->trans('Pricesbyregion'), $page, $_SERVER ['PHP_SELF'], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_accountancy.png',0,'','',$limit);

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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,$arrayfields['t.entity']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_region_geographic']['checked'])) print_liste_field_titre($arrayfields['t.fk_region_geographic']['label'],$_SERVER['PHP_SELF'],'t.fk_region_geographic','',$params,$arrayfields['t.fk_region_geographic']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_soc']['checked'])) print_liste_field_titre($arrayfields['t.fk_soc']['label'],$_SERVER['PHP_SELF'],'t.fk_soc','',$params,$arrayfields['t.fk_soc']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.date_create']['checked'])) print_liste_field_titre($arrayfields['t.date_create']['label'],$_SERVER['PHP_SELF'],'t.date_create','',$params,$arrayfields['t.date_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,$arrayfields['t.fk_product']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.quantity']['checked'])) print_liste_field_titre($arrayfields['t.quantity']['label'],$_SERVER['PHP_SELF'],'t.quantity','',$params,$arrayfields['t.quantity']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.price']['checked'])) print_liste_field_titre($arrayfields['t.price']['label'],$_SERVER['PHP_SELF'],'t.price','',$params,$arrayfields['t.price']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.remise_percent']['checked'])) print_liste_field_titre($arrayfields['t.remise_percent']['label'],$_SERVER['PHP_SELF'],'t.remise_percent','',$params,$arrayfields['t.remise_percent']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.remise']['checked'])) print_liste_field_titre($arrayfields['t.remise']['label'],$_SERVER['PHP_SELF'],'t.remise','',$params,$arrayfields['t.remise']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.tva_tx']['checked'])) print_liste_field_titre($arrayfields['t.tva_tx']['label'],$_SERVER['PHP_SELF'],'t.tva_tx','',$params,$arrayfields['t.tva_tx']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.default_vat_code']['checked'])) print_liste_field_titre($arrayfields['t.default_vat_code']['label'],$_SERVER['PHP_SELF'],'t.default_vat_code','',$params,$arrayfields['t.default_vat_code']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.info_bits']['checked'])) print_liste_field_titre($arrayfields['t.info_bits']['label'],$_SERVER['PHP_SELF'],'t.info_bits','',$params,$arrayfields['t.info_bits']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,$arrayfields['t.fk_user']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.import_key']['checked'])) print_liste_field_titre($arrayfields['t.import_key']['label'],$_SERVER['PHP_SELF'],'t.import_key','',$params,$arrayfields['t.import_key']['align'],$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
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
//if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.entity']['align'].'><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.fk_region_geographic']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_region_geographic']['align'].'><input type="text" class="flat" name="search_fk_region_geographic" value="'.$search_fk_region_geographic.'" size="10"></td>';
if (! empty($arrayfields['t.fk_soc']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_soc']['align'].'><input type="text" class="flat" name="search_fk_soc" value="'.$search_fk_soc.'" size="10"></td>';
if (! empty($arrayfields['t.date_create']['checked']))
{
	print '<td class="liste_titre" '.$arrayfields['t.datec']['align'].'>';
	print '<input type="text" size="2" name="day" value="'.$day.'" placeholder="'.$langs->trans('Day').'">';
	print '<input type="text" size="2" name="month" value="'.$month.'" placeholder="'.$langs->trans('Month').'">';
	print '<input type="text" size="3" name="year" value="'.$year.'" placeholder="'.$langs->trans('Year').'">';
	print '</td>';
}

if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product']['align'].'><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.quantity']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.quantity']['align'].'><input type="text" class="flat" name="search_quantity" value="'.$search_quantity.'" size="10"></td>';
if (! empty($arrayfields['t.price']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.price']['align'].'><input type="text" class="flat" name="search_price" value="'.$search_price.'" size="10"></td>';
if (! empty($arrayfields['t.remise_percent']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.remise_percent']['align'].'><input type="text" class="flat" name="search_remise_percent" value="'.$search_remise_percent.'" size="10"></td>';
if (! empty($arrayfields['t.remise']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.remise']['align'].'><input type="text" class="flat" name="search_remise" value="'.$search_remise.'" size="10"></td>';
if (! empty($arrayfields['t.tva_tx']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.tva_tx']['align'].'><input type="text" class="flat" name="search_tva_tx" value="'.$search_tva_tx.'" size="10"></td>';
if (! empty($arrayfields['t.default_vat_code']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.default_vat_code']['align'].'><input type="text" class="flat" name="search_default_vat_code" value="'.$search_default_vat_code.'" size="10"></td>';
if (! empty($arrayfields['t.info_bits']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.info_bits']['align'].'><input type="text" class="flat" name="search_info_bits" value="'.$search_info_bits.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user']['align'].'><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.import_key']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.import_key']['align'].'><input type="text" class="flat" name="search_import_key" value="'.$search_import_key.'" size="10"></td>';

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
//	print '<td class="liste_titre">';
//	print '</td>';
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

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$objProductregionprice->id = $obj->rowid;
					$objProductregionprice->ref = $obj->ref;
					$objProductregionprice->label = $obj->label;
					$obj->$key2 = $objProductregionprice->getNomUrl();
				}
				if ($key2 == 'fk_region_geographic')
				{
					$restmp = $objCregiongeographic->fetch($obj->$key2);
					if ($restmp)
						$obj->$key2 = $objCregiongeographic->getNomUrl(1);
				}
				if ($key2 == 'fk_soc')
				{
					$restmp = $objSociete->fetch($obj->$key2);
					if ($restmp)
						$obj->$key2 = $objSociete->getNomUrl(1);
				}
				if ($key2 == 'date_create')
				{
					$obj->$key2 = dol_print_date($db->jdate($obj->$key2),'dayhour');
				}


				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}

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
			//print '<td align="center">';
			//print dol_print_date($db->jdate($obj->date_creation), 'dayhour');
			//print '</td>';
			//if (! $i) $totalarray['nbfield']++;
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
		if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
		{
		//	$selected=0;
		//	if (in_array($obj->rowid, $arrayofselected)) $selected=1;
		//	print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
		}
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
	}
	$i++;
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";


if ($massaction == 'builddoc' || $action == 'remove_file' || $show_files)
{
	// Show list of available documents
	$urlsource=$_SERVER['PHP_SELF'].'?sortfield='.$sortfield.'&sortorder='.$sortorder;
	$urlsource.=str_replace('&amp;','&',$param);

	$filedir=$diroutputmassaction;
	$genallowed=$user->rights->facture->lire;
	$delallowed=$user->rights->facture->lire;

	print $formfile->showdocuments('massfilesarea_productext','',$filedir,$urlsource,0,$delallowed,'',1,1,0,48,1,$param,$title,'');
}
else
{
	print '<br><a name="show_files"></a><a href="'.$_SERVER["PHP_SELF"].'?show_files=1'.$param.'#show_files">'.$langs->trans("ShowTempMassFilesArea").'</a>';
}

