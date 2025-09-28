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
 *   	\file       poa/poapoa_list.php
 *		\ingroup    poa
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-29 11:49
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

// Change this following line to use the correct relative path from htdocs

dol_include_once('/poa/class/poapoa.class.php');

// Load traductions files requiredby by page
$langs->load("poa");
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
$search_period_year=GETPOST('search_period_year','int');
$search_fk_structure=GETPOST('search_fk_structure','int');
$search_ref=GETPOST('search_ref','alpha');
$search_sigla=GETPOST('search_sigla','alpha');
$search_label=GETPOST('search_label','alpha');
$search_pseudonym=GETPOST('search_pseudonym','alpha');
$search_partida=GETPOST('search_partida','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_classification=GETPOST('search_classification','alpha');
$search_source_verification=GETPOST('search_source_verification','alpha');
$search_unit=GETPOST('search_unit','alpha');
$search_responsible_one=GETPOST('search_responsible_one','alpha');
$search_responsible_two=GETPOST('search_responsible_two','alpha');
$search_responsible=GETPOST('search_responsible','alpha');
$search_m_jan=GETPOST('search_m_jan','alpha');
$search_m_feb=GETPOST('search_m_feb','alpha');
$search_m_mar=GETPOST('search_m_mar','alpha');
$search_m_apr=GETPOST('search_m_apr','alpha');
$search_m_may=GETPOST('search_m_may','alpha');
$search_m_jun=GETPOST('search_m_jun','alpha');
$search_m_jul=GETPOST('search_m_jul','alpha');
$search_m_aug=GETPOST('search_m_aug','alpha');
$search_m_sep=GETPOST('search_m_sep','alpha');
$search_m_oct=GETPOST('search_m_oct','alpha');
$search_m_nov=GETPOST('search_m_nov','alpha');
$search_m_dec=GETPOST('search_m_dec','alpha');
$search_p_jan=GETPOST('search_p_jan','alpha');
$search_p_feb=GETPOST('search_p_feb','alpha');
$search_p_mar=GETPOST('search_p_mar','alpha');
$search_p_apr=GETPOST('search_p_apr','alpha');
$search_p_may=GETPOST('search_p_may','alpha');
$search_p_jun=GETPOST('search_p_jun','alpha');
$search_p_jul=GETPOST('search_p_jul','alpha');
$search_p_aug=GETPOST('search_p_aug','alpha');
$search_p_sep=GETPOST('search_p_sep','alpha');
$search_p_oct=GETPOST('search_p_oct','alpha');
$search_p_nov=GETPOST('search_p_nov','alpha');
$search_p_dec=GETPOST('search_p_dec','alpha');
$search_fk_area=GETPOST('search_fk_area','int');
$search_weighting=GETPOST('search_weighting','alpha');
$search_fk_poa_reformulated=GETPOST('search_fk_poa_reformulated','int');
$search_version=GETPOST('search_version','int');
$search_statut=GETPOST('search_statut','int');
$search_statut_ref=GETPOST('search_statut_ref','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
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

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'poalist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('poalist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('poa');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
	);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.fk_structure'=>array('label'=>$langs->trans("Fieldfk_structure"), 'checked'=>1),
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>0),
	't.sigla'=>array('label'=>$langs->trans("Fieldsigla"), 'checked'=>0),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.pseudonym'=>array('label'=>$langs->trans("Fieldpseudonym"), 'checked'=>1),
	't.partida'=>array('label'=>$langs->trans("Fieldpartida"), 'checked'=>1),
	't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
	't.classification'=>array('label'=>$langs->trans("Fieldclassification"), 'checked'=>0),
	't.source_verification'=>array('label'=>$langs->trans("Fieldsource_verification"), 'checked'=>0),
	't.unit'=>array('label'=>$langs->trans("Fieldunit"), 'checked'=>0),
	't.responsible_one'=>array('label'=>$langs->trans("Fieldresponsible_one"), 'checked'=>0),
	't.responsible_two'=>array('label'=>$langs->trans("Fieldresponsible_two"), 'checked'=>0),
	't.responsible'=>array('label'=>$langs->trans("Fieldresponsible"), 'checked'=>0),
	't.m_jan'=>array('label'=>$langs->trans("Fieldm_jan"), 'checked'=>1),
	't.m_feb'=>array('label'=>$langs->trans("Fieldm_feb"), 'checked'=>1),
	't.m_mar'=>array('label'=>$langs->trans("Fieldm_mar"), 'checked'=>1),
	't.m_apr'=>array('label'=>$langs->trans("Fieldm_apr"), 'checked'=>1),
	't.m_may'=>array('label'=>$langs->trans("Fieldm_may"), 'checked'=>1),
	't.m_jun'=>array('label'=>$langs->trans("Fieldm_jun"), 'checked'=>1),
	't.m_jul'=>array('label'=>$langs->trans("Fieldm_jul"), 'checked'=>1),
	't.m_aug'=>array('label'=>$langs->trans("Fieldm_aug"), 'checked'=>1),
	't.m_sep'=>array('label'=>$langs->trans("Fieldm_sep"), 'checked'=>1),
	't.m_oct'=>array('label'=>$langs->trans("Fieldm_oct"), 'checked'=>1),
	't.m_nov'=>array('label'=>$langs->trans("Fieldm_nov"), 'checked'=>1),
	't.m_dec'=>array('label'=>$langs->trans("Fieldm_dec"), 'checked'=>1),
	't.p_jan'=>array('label'=>$langs->trans("Fieldp_jan"), 'checked'=>0),
	't.p_feb'=>array('label'=>$langs->trans("Fieldp_feb"), 'checked'=>0),
	't.p_mar'=>array('label'=>$langs->trans("Fieldp_mar"), 'checked'=>0),
	't.p_apr'=>array('label'=>$langs->trans("Fieldp_apr"), 'checked'=>0),
	't.p_may'=>array('label'=>$langs->trans("Fieldp_may"), 'checked'=>0),
	't.p_jun'=>array('label'=>$langs->trans("Fieldp_jun"), 'checked'=>0),
	't.p_jul'=>array('label'=>$langs->trans("Fieldp_jul"), 'checked'=>0),
	't.p_aug'=>array('label'=>$langs->trans("Fieldp_aug"), 'checked'=>0),
	't.p_sep'=>array('label'=>$langs->trans("Fieldp_sep"), 'checked'=>0),
	't.p_oct'=>array('label'=>$langs->trans("Fieldp_oct"), 'checked'=>0),
	't.p_nov'=>array('label'=>$langs->trans("Fieldp_nov"), 'checked'=>0),
	't.p_dec'=>array('label'=>$langs->trans("Fieldp_dec"), 'checked'=>0),
	't.fk_area'=>array('label'=>$langs->trans("Fieldfk_area"), 'checked'=>1),
	't.weighting'=>array('label'=>$langs->trans("Fieldweighting"), 'checked'=>0),
	't.fk_poa_reformulated'=>array('label'=>$langs->trans("Fieldfk_poa_reformulated"), 'checked'=>0),
	't.version'=>array('label'=>$langs->trans("Fieldversion"), 'checked'=>0),
	't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),
	't.statut_ref'=>array('label'=>$langs->trans("Fieldstatut_ref"), 'checked'=>0),


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
$object=new Poapoa($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Formv($db);
$getUtil = new getUtil($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Presupuesto de gasto');

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
$sql .= " t.period_year,";
$sql .= " t.fk_structure,";
$sql .= " t.ref,";
$sql .= " t.sigla,";
$sql .= " t.label,";
$sql .= " t.pseudonym,";
$sql .= " t.partida,";
$sql .= " t.amount,";
$sql .= " t.classification,";
$sql .= " t.source_verification,";
$sql .= " t.unit,";
$sql .= " t.responsible_one,";
$sql .= " t.responsible_two,";
$sql .= " t.responsible,";
$sql .= " t.m_jan,";
$sql .= " t.m_feb,";
$sql .= " t.m_mar,";
$sql .= " t.m_apr,";
$sql .= " t.m_may,";
$sql .= " t.m_jun,";
$sql .= " t.m_jul,";
$sql .= " t.m_aug,";
$sql .= " t.m_sep,";
$sql .= " t.m_oct,";
$sql .= " t.m_nov,";
$sql .= " t.m_dec,";
$sql .= " t.p_jan,";
$sql .= " t.p_feb,";
$sql .= " t.p_mar,";
$sql .= " t.p_apr,";
$sql .= " t.p_may,";
$sql .= " t.p_jun,";
$sql .= " t.p_jul,";
$sql .= " t.p_aug,";
$sql .= " t.p_sep,";
$sql .= " t.p_oct,";
$sql .= " t.p_nov,";
$sql .= " t.p_dec,";
$sql .= " t.fk_area,";
$sql .= " t.weighting,";
$sql .= " t.fk_poa_reformulated,";
$sql .= " t.version,";
$sql .= " t.statut,";
$sql .= " t.statut_ref";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."poa_poa_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE t.entity = ".$conf->entity;
$sql.= " AND t.period_year = ".$_SESSION['period_year'];

if ($ids>0)
	$sql.= " AND t.fk_structure = ".$ids;

//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_period_year) $sql.= natural_search("period_year",$search_period_year);
if ($search_fk_structure) $sql.= natural_search("fk_structure",$search_fk_structure);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_sigla) $sql.= natural_search("sigla",$search_sigla);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_pseudonym) $sql.= natural_search("pseudonym",$search_pseudonym);
if ($search_partida) $sql.= natural_search("partida",$search_partida);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_classification) $sql.= natural_search("classification",$search_classification);
if ($search_source_verification) $sql.= natural_search("source_verification",$search_source_verification);
if ($search_unit) $sql.= natural_search("unit",$search_unit);
if ($search_responsible_one) $sql.= natural_search("responsible_one",$search_responsible_one);
if ($search_responsible_two) $sql.= natural_search("responsible_two",$search_responsible_two);
if ($search_responsible) $sql.= natural_search("responsible",$search_responsible);
if ($search_m_jan) $sql.= natural_search("m_jan",$search_m_jan);
if ($search_m_feb) $sql.= natural_search("m_feb",$search_m_feb);
if ($search_m_mar) $sql.= natural_search("m_mar",$search_m_mar);
if ($search_m_apr) $sql.= natural_search("m_apr",$search_m_apr);
if ($search_m_may) $sql.= natural_search("m_may",$search_m_may);
if ($search_m_jun) $sql.= natural_search("m_jun",$search_m_jun);
if ($search_m_jul) $sql.= natural_search("m_jul",$search_m_jul);
if ($search_m_aug) $sql.= natural_search("m_aug",$search_m_aug);
if ($search_m_sep) $sql.= natural_search("m_sep",$search_m_sep);
if ($search_m_oct) $sql.= natural_search("m_oct",$search_m_oct);
if ($search_m_nov) $sql.= natural_search("m_nov",$search_m_nov);
if ($search_m_dec) $sql.= natural_search("m_dec",$search_m_dec);
if ($search_p_jan) $sql.= natural_search("p_jan",$search_p_jan);
if ($search_p_feb) $sql.= natural_search("p_feb",$search_p_feb);
if ($search_p_mar) $sql.= natural_search("p_mar",$search_p_mar);
if ($search_p_apr) $sql.= natural_search("p_apr",$search_p_apr);
if ($search_p_may) $sql.= natural_search("p_may",$search_p_may);
if ($search_p_jun) $sql.= natural_search("p_jun",$search_p_jun);
if ($search_p_jul) $sql.= natural_search("p_jul",$search_p_jul);
if ($search_p_aug) $sql.= natural_search("p_aug",$search_p_aug);
if ($search_p_sep) $sql.= natural_search("p_sep",$search_p_sep);
if ($search_p_oct) $sql.= natural_search("p_oct",$search_p_oct);
if ($search_p_nov) $sql.= natural_search("p_nov",$search_p_nov);
if ($search_p_dec) $sql.= natural_search("p_dec",$search_p_dec);
if ($search_fk_area) $sql.= natural_search("fk_area",$search_fk_area);
if ($search_weighting) $sql.= natural_search("weighting",$search_weighting);
if ($search_fk_poa_reformulated) $sql.= natural_search("fk_poa_reformulated",$search_fk_poa_reformulated);
if ($search_version) $sql.= natural_search("version",$search_version);
if ($search_statut) $sql.= natural_search("statut",$search_statut);
if ($search_statut_ref) $sql.= natural_search("statut_ref",$search_statut_ref);


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
	header("Location: ".DOL_URL_ROOT.'/poapoa/card.php?id='.$id);
	exit;
}


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
if ($user->rights->poa->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="ido" value="'.$ido.'">';
print '<input type="hidden" name="ids" value="'.$ids.'">';
print '<input type="hidden" name="period_year" value="'.$_SESSION['period_year'].'">';
if ($action=='create' || $action=='createsub')
{
	print '<input type="hidden" name="action" value="add">';
}
elseif($action == 'edit')
{
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
else
	print '<input type="hidden" name="subaction" value="list">';
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
if ($action != 'create' && $action != 'edit')
{
	$moreforfilter.='<div class="divsearchfield">';
	$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
	$moreforfilter.= '</div>';
}
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    
// Note that $action and $object may have been modified by hook
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
if (! empty($arrayfields['t.period_year']['checked'])) print_liste_field_titre($arrayfields['t.period_year']['label'],$_SERVER['PHP_SELF'],'t.period_year','',$params,'',$sortfield,$sortorder);
if ($ids<=0)
{
	print_liste_field_titre($arrayfields['t.fk_structure']['label'],$_SERVER['PHP_SELF'],'t.fk_structure','',$params,'',$sortfield,$sortorder);
}
//if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.sigla']['checked'])) print_liste_field_titre($arrayfields['t.sigla']['label'],$_SERVER['PHP_SELF'],'t.sigla','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.label']['checked'])) 
print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'colspan="2"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.pseudonym']['checked'])) 
print_liste_field_titre($arrayfields['t.pseudonym']['label'],$_SERVER['PHP_SELF'],'t.pseudonym','',$params,'colspan="3"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.partida']['checked'])) 
print_liste_field_titre($arrayfields['t.partida']['label'],$_SERVER['PHP_SELF'],'t.partida','',$params,'colspan="3"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.amount']['checked'])) 
print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,'colspan="3"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.fk_area']['checked'])) 
print_liste_field_titre($arrayfields['t.fk_area']['label'],$_SERVER['PHP_SELF'],'t.fk_area','',$params,'colspan="3"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.statut']['checked'])) 
print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$params,'colspan="3"',$sortfield,$sortorder);
print '</tr>';
if ($action == 'create' || $action == 'edit')
{
	print '<tr class="liste_titre">';
	print '<td colspan="2"></td>';
//if (! empty($arrayfields['t.classification']['checked'])) print_liste_field_titre($arrayfields['t.classification']['label'],$_SERVER['PHP_SELF'],'t.classification','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.source_verification']['checked'])) print_liste_field_titre($arrayfields['t.source_verification']['label'],$_SERVER['PHP_SELF'],'t.source_verification','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.unit']['checked'])) print_liste_field_titre($arrayfields['t.unit']['label'],$_SERVER['PHP_SELF'],'t.unit','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.responsible_one']['checked'])) print_liste_field_titre($arrayfields['t.responsible_one']['label'],$_SERVER['PHP_SELF'],'t.responsible_one','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.responsible_two']['checked'])) print_liste_field_titre($arrayfields['t.responsible_two']['label'],$_SERVER['PHP_SELF'],'t.responsible_two','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.responsible']['checked'])) print_liste_field_titre($arrayfields['t.responsible']['label'],$_SERVER['PHP_SELF'],'t.responsible','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_jan']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_jan']['label'],$_SERVER['PHP_SELF'],'t.m_jan','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_feb']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_feb']['label'],$_SERVER['PHP_SELF'],'t.m_feb','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_mar']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_mar']['label'],$_SERVER['PHP_SELF'],'t.m_mar','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_apr']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_apr']['label'],$_SERVER['PHP_SELF'],'t.m_apr','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_may']['checked']))
	print_liste_field_titre($arrayfields['t.m_may']['label'],$_SERVER['PHP_SELF'],'t.m_may','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_jun']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_jun']['label'],$_SERVER['PHP_SELF'],'t.m_jun','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_jul']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_jul']['label'],$_SERVER['PHP_SELF'],'t.m_jul','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_aug']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_aug']['label'],$_SERVER['PHP_SELF'],'t.m_aug','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_sep']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_sep']['label'],$_SERVER['PHP_SELF'],'t.m_sep','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_oct']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_oct']['label'],$_SERVER['PHP_SELF'],'t.m_oct','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_nov']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_nov']['label'],$_SERVER['PHP_SELF'],'t.m_nov','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.m_dec']['checked'])) 
	print_liste_field_titre($arrayfields['t.m_dec']['label'],$_SERVER['PHP_SELF'],'t.m_dec','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_jan']['checked'])) print_liste_field_titre($arrayfields['t.p_jan']['label'],$_SERVER['PHP_SELF'],'t.p_jan','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_feb']['checked'])) print_liste_field_titre($arrayfields['t.p_feb']['label'],$_SERVER['PHP_SELF'],'t.p_feb','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_mar']['checked'])) print_liste_field_titre($arrayfields['t.p_mar']['label'],$_SERVER['PHP_SELF'],'t.p_mar','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_apr']['checked'])) print_liste_field_titre($arrayfields['t.p_apr']['label'],$_SERVER['PHP_SELF'],'t.p_apr','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_may']['checked'])) print_liste_field_titre($arrayfields['t.p_may']['label'],$_SERVER['PHP_SELF'],'t.p_may','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_jun']['checked'])) print_liste_field_titre($arrayfields['t.p_jun']['label'],$_SERVER['PHP_SELF'],'t.p_jun','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_jul']['checked'])) print_liste_field_titre($arrayfields['t.p_jul']['label'],$_SERVER['PHP_SELF'],'t.p_jul','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_aug']['checked'])) print_liste_field_titre($arrayfields['t.p_aug']['label'],$_SERVER['PHP_SELF'],'t.p_aug','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_sep']['checked'])) print_liste_field_titre($arrayfields['t.p_sep']['label'],$_SERVER['PHP_SELF'],'t.p_sep','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_oct']['checked'])) print_liste_field_titre($arrayfields['t.p_oct']['label'],$_SERVER['PHP_SELF'],'t.p_oct','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_nov']['checked'])) print_liste_field_titre($arrayfields['t.p_nov']['label'],$_SERVER['PHP_SELF'],'t.p_nov','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.p_dec']['checked'])) print_liste_field_titre($arrayfields['t.p_dec']['label'],$_SERVER['PHP_SELF'],'t.p_dec','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.weighting']['checked'])) print_liste_field_titre($arrayfields['t.weighting']['label'],$_SERVER['PHP_SELF'],'t.weighting','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.fk_poa_reformulated']['checked'])) print_liste_field_titre($arrayfields['t.fk_poa_reformulated']['label'],$_SERVER['PHP_SELF'],'t.fk_poa_reformulated','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.version']['checked'])) print_liste_field_titre($arrayfields['t.version']['label'],$_SERVER['PHP_SELF'],'t.version','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.statut_ref']['checked'])) print_liste_field_titre($arrayfields['t.statut_ref']['label'],$_SERVER['PHP_SELF'],'t.statut_ref','',$params,'',$sortfield,$sortorder);

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
//print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '<td colspan="3"></td>';
print '</tr>'."\n";
}
if ($action != 'create' && $action != 'edit')
{
// Fields title search
	print '<tr class="liste_titre">';
// 
	if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
	if (! empty($arrayfields['t.period_year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_period_year" value="'.$search_period_year.'" size="10"></td>';
	if ($ids <=0)
	{
		if (! empty($arrayfields['t.fk_structure']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_structure" value="'.$search_fk_structure.'" size="10"></td>';
	}
	//if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	//if (! empty($arrayfields['t.sigla']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sigla" value="'.$search_sigla.'" size="10"></td>';
	//if (! empty($arrayfields['t.label']['checked'])) 
	print '<td colspan="2" class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
	//if (! empty($arrayfields['t.pseudonym']['checked'])) 
	print '<td colspan="3" class="liste_titre"><input type="text" class="flat" name="search_pseudonym" value="'.$search_pseudonym.'" size="10"></td>';
	//if (! empty($arrayfields['t.partida']['checked'])) 
	print '<td colspan="3" class="liste_titre"><input type="text" class="flat" name="search_partida" value="'.$search_partida.'" size="10"></td>';
	//if (! empty($arrayfields['t.amount']['checked'])) 
	print '<td colspan="3" class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
	//if (! empty($arrayfields['t.classification']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_classification" value="'.$search_classification.'" size="10"></td>';
	//if (! empty($arrayfields['t.source_verification']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_source_verification" value="'.$search_source_verification.'" size="10"></td>';
	//if (! empty($arrayfields['t.unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_unit" value="'.$search_unit.'" size="10"></td>';
	//if (! empty($arrayfields['t.responsible_one']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_responsible_one" value="'.$search_responsible_one.'" size="10"></td>';
	//if (! empty($arrayfields['t.responsible_two']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_responsible_two" value="'.$search_responsible_two.'" size="10"></td>';
	//if (! empty($arrayfields['t.responsible']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_responsible" value="'.$search_responsible.'" size="10"></td>';
	//if (! empty($arrayfields['t.fk_area']['checked'])) 
	print '<td colspan="3" class="liste_titre"><input type="text" class="flat" name="search_fk_area" value="'.$search_fk_area.'" size="10"></td>';
	if (! empty($arrayfields['t.weighting']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_weighting" value="'.$search_weighting.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_poa_reformulated']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_poa_reformulated" value="'.$search_fk_poa_reformulated.'" size="10"></td>';
	if (! empty($arrayfields['t.version']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_version" value="'.$search_version.'" size="10"></td>';
	if (! empty($arrayfields['t.statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut" value="'.$search_statut.'" size="10"></td>';
	if (! empty($arrayfields['t.statut_ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut_ref" value="'.$search_statut_ref.'" size="10"></td>';

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
	$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";
}

$i=0;
$var=true;
$totalarray=array();
//action = create
if ($action == 'create')
{
    //WYSIWYG Editor
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

        // Show here line of result
	print '<tr '.$bc[$var].'>';
        // LIST_OF_TD_FIELDS_LIST
	if ($ids<=0)
	{
		if (! empty($arrayfields['t.fk_structure']['checked'])) 
		{
			print '<td>';
			$filterstr = " AND fk_poa_objetive IN (".$idsObjetive.")";
			print $form->select_structure(GETPOST('fk_structure'),'fk_structure','',0,1,3,$filterstr);
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
	}
	else
	{
		print '<input type="hidden" name="fk_structure" value="'.$ids.'">';
	}        	
	if (! empty($arrayfields['t.label']['checked'])) 
	{
		print '<td colspan="2">';
		$doleditor = new DolEditor('label', GETPOST('label'), '', 50, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_1, '90%');
		$doleditor->Create();
		print '</td>';

		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.pseudonym']['checked'])) 
	{
		print '<td colspan="3">';
		$doleditor = new DolEditor('pseudonym', GETPOST('pseudonym'), '', 50, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_1, '90%');
		$doleditor->Create();
		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.partida']['checked'])) 
	{
		print '<td colspan="3">'.'<input type="text" name="partida" size="6" value="'.GETPOST('partida').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.amount']['checked'])) 
	{
		print '<td colspan="3">'.'<input type="number" name="amount" min="0" step="any" value="'.GETPOST('amount').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.fk_area']['checked'])) 
	{
		print '<td colspan="3">'.$form->select_departament(GETPOST('fk_area'),'fk_area','',45,1,$filtersub,1).'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.statut']['checked'])) 
	{
		print '<td colspan="3">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	print '</tr>';

	print '<tr '.$bc[$var].'>';
	print '<td colspan="2">'.$langs->trans('Product').'</td>';
	print '<td align="right">'.'<input type="text" name="p_jan" value="'.GETPOST('p_jan').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_feb" value="'.GETPOST('p_feb').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_mar" value="'.GETPOST('p_mar').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_apr" value="'.GETPOST('p_apr').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_may" value="'.GETPOST('p_may').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_jun" value="'.GETPOST('p_jun').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_jul" value="'.GETPOST('p_jul').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_aug" value="'.GETPOST('p_aug').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_sep" value="'.GETPOST('p_sep').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_oct" value="'.GETPOST('p_oct').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_nov" value="'.GETPOST('p_nov').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="p_dec" value="'.GETPOST('p_dec').'" size="1">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td colspan="2">';
	print '</td>';
	print '</tr>';

	print '<tr '.$bc[$var].'>';
	print '<td colspan="2">'.$langs->trans('Amount').'</td>';
	print '<td align="right">'.'<input type="text" name="m_jan" value="'.GETPOST('m_jan').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_feb" value="'.GETPOST('m_feb').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_mar" value="'.GETPOST('m_mar').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_apr" value="'.GETPOST('m_apr').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_may" value="'.GETPOST('m_may').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_jun" value="'.GETPOST('m_jun').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_jul" value="'.GETPOST('m_jul').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_aug" value="'.GETPOST('m_aug').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_sep" value="'.GETPOST('m_sep').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_oct" value="'.GETPOST('m_oct').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_nov" value="'.GETPOST('m_nov').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td align="right">'.'<input type="text" name="m_dec" value="'.GETPOST('m_dec').'" size="6">'.'</td>';
	if (! $i) $totalarray['nbfield']++;
	print '<td colspan="2">';
	print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
	print '</td>';
	print '</tr>';

}

while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		$objpoa->id = $obj->rowid;
		$objpoa->statut = $obj->statut;

        // Show here line of result
		print '<tr '.$bc[$var].'>';
        // LIST_OF_TD_FIELDS_LIST

		if ($ids<=0)
		{
			if (! empty($arrayfields['t.fk_structure']['checked'])) 
			{
				print '<td>'.$obj->fk_structure.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
		}
		if (! empty($arrayfields['t.label']['checked'])) 
		{
			print '<td colspan="2">'.$obj->label.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.pseudonym']['checked'])) 
		{
			print '<td colspan="3">'.$obj->pseudonym.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.partida']['checked'])) 
		{
			print '<td colspan="3">'.$obj->partida.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.amount']['checked'])) 
		{
			print '<td colspan="3" align="right">'.price($obj->amount).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_area']['checked'])) 
		{
			if ($conf->orgman->enabled)
			{
				$departament->fetch($obj->fk_area);
				print '<td colspan="3">'.$departament->getNomUrl(0, '', 0, 24, '',1).'</td>';
			}
			else
			{
				$getUtil->fetch_departament($obj->fk_area);
				print '<td colspan="3">'.$getUtil->ref.'</td>';	
			}
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.statut']['checked'])) 
		{
			print '<td>'.$objpoa->getLibStatut(3).'</td>';
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
	    if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
	    {
	    	$selected=0;
	    	if (in_array($obj->rowid, $arrayofselected)) $selected=1;
	    	print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
	    }
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
			if ($num < $limit) print '<td align="left">'.$langs->trans("Total").'</td>';
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

print "<div class=\"tabsAction\">\n";

if ($action == '')
{
	if ($user->rights->poa->str->crear && $ido > 0 && $ids>0)
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&ido='.$ido.'&ids='.$ids.'&action=create">'.$langs->trans("Createnew").'</a>';
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
}
print '</div>';


if ($massaction == 'builddoc' || $action == 'remove_file' || $show_files)
{
    // Show list of available documents
	$urlsource=$_SERVER['PHP_SELF'].'?sortfield='.$sortfield.'&sortorder='.$sortorder;
	$urlsource.=str_replace('&amp;','&',$param);

	$filedir=$diroutputmassaction;
	$genallowed=$user->rights->facture->lire;
	$delallowed=$user->rights->facture->lire;

	print $formfile->showdocuments('massfilesarea_poa','',$filedir,$urlsource,0,$delallowed,'',1,1,0,48,1,$param,$title,'');
}
else
{
	print '<br><a name="show_files"></a><a href="'.$_SERVER["PHP_SELF"].'?show_files=1'.$param.'#show_files">'.$langs->trans("ShowTempMassFilesArea").'</a>';
}

