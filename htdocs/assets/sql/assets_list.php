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
 *   	\file       assets/assets_list.php
 *		\ingroup    assets
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-05-25 16:35
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
dol_include_once('/assets/class/assets.class.php');

// Load traductions files requiredby by page
$langs->load("assets");
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
$search_fk_father=GETPOST('search_fk_father','int');
$search_fk_facture=GETPOST('search_fk_facture','int');
$search_type_group=GETPOST('search_type_group','alpha');
$search_type_patrim=GETPOST('search_type_patrim','alpha');
$search_ref=GETPOST('search_ref','alpha');
$search_item_asset=GETPOST('search_item_asset','int');
$search_useful_life_residual=GETPOST('search_useful_life_residual','int');
$search_quant=GETPOST('search_quant','alpha');
$search_coste=GETPOST('search_coste','alpha');
$search_coste_residual=GETPOST('search_coste_residual','alpha');
$search_coste_reval=GETPOST('search_coste_reval','alpha');
$search_coste_residual_reval=GETPOST('search_coste_residual_reval','alpha');
$search_amount_sale=GETPOST('search_amount_sale','alpha');
$search_descrip=GETPOST('search_descrip','alpha');
$search_number_plaque=GETPOST('search_number_plaque','alpha');
$search_trademark=GETPOST('search_trademark','alpha');
$search_model=GETPOST('search_model','alpha');
$search_anio=GETPOST('search_anio','int');
$search_fk_asset_sup=GETPOST('search_fk_asset_sup','int');
$search_fk_location=GETPOST('search_fk_location','int');
$search_code_bar=GETPOST('search_code_bar','alpha');
$search_fk_method_dep=GETPOST('search_fk_method_dep','int');
$search_type_property=GETPOST('search_type_property','alpha');
$search_code_bim=GETPOST('search_code_bim','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_useful_life=GETPOST('search_useful_life','alpha');
$search_percent=GETPOST('search_percent','alpha');
$search_account_accounting=GETPOST('search_account_accounting','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_coste_unit_use=GETPOST('search_coste_unit_use','alpha');
$search_fk_unit_use=GETPOST('search_fk_unit_use','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_mark=GETPOST('search_mark','alpha');
$search_been=GETPOST('search_been','alpha');
$search_fk_asset_mov=GETPOST('search_fk_asset_mov','int');
$search_status_reval=GETPOST('search_status_reval','int');
$search_statut=GETPOST('search_statut','int');


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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assetslist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('assetslist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('assets');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
    't.ref'=>'Ref',
    't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.fk_father'=>array('label'=>$langs->trans("Fieldfk_father"), 'checked'=>1),
't.fk_facture'=>array('label'=>$langs->trans("Fieldfk_facture"), 'checked'=>1),
't.type_group'=>array('label'=>$langs->trans("Fieldtype_group"), 'checked'=>1),
't.type_patrim'=>array('label'=>$langs->trans("Fieldtype_patrim"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.item_asset'=>array('label'=>$langs->trans("Fielditem_asset"), 'checked'=>1),
't.useful_life_residual'=>array('label'=>$langs->trans("Fielduseful_life_residual"), 'checked'=>1),
't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'checked'=>1),
't.coste'=>array('label'=>$langs->trans("Fieldcoste"), 'checked'=>1),
't.coste_residual'=>array('label'=>$langs->trans("Fieldcoste_residual"), 'checked'=>1),
't.coste_reval'=>array('label'=>$langs->trans("Fieldcoste_reval"), 'checked'=>1),
't.coste_residual_reval'=>array('label'=>$langs->trans("Fieldcoste_residual_reval"), 'checked'=>1),
't.amount_sale'=>array('label'=>$langs->trans("Fieldamount_sale"), 'checked'=>1),
't.descrip'=>array('label'=>$langs->trans("Fielddescrip"), 'checked'=>1),
't.number_plaque'=>array('label'=>$langs->trans("Fieldnumber_plaque"), 'checked'=>1),
't.trademark'=>array('label'=>$langs->trans("Fieldtrademark"), 'checked'=>1),
't.model'=>array('label'=>$langs->trans("Fieldmodel"), 'checked'=>1),
't.anio'=>array('label'=>$langs->trans("Fieldanio"), 'checked'=>1),
't.fk_asset_sup'=>array('label'=>$langs->trans("Fieldfk_asset_sup"), 'checked'=>1),
't.fk_location'=>array('label'=>$langs->trans("Fieldfk_location"), 'checked'=>1),
't.code_bar'=>array('label'=>$langs->trans("Fieldcode_bar"), 'checked'=>1),
't.fk_method_dep'=>array('label'=>$langs->trans("Fieldfk_method_dep"), 'checked'=>1),
't.type_property'=>array('label'=>$langs->trans("Fieldtype_property"), 'checked'=>1),
't.code_bim'=>array('label'=>$langs->trans("Fieldcode_bim"), 'checked'=>1),
't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>1),
't.useful_life'=>array('label'=>$langs->trans("Fielduseful_life"), 'checked'=>1),
't.percent'=>array('label'=>$langs->trans("Fieldpercent"), 'checked'=>1),
't.account_accounting'=>array('label'=>$langs->trans("Fieldaccount_accounting"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.model_pdf'=>array('label'=>$langs->trans("Fieldmodel_pdf"), 'checked'=>1),
't.coste_unit_use'=>array('label'=>$langs->trans("Fieldcoste_unit_use"), 'checked'=>1),
't.fk_unit_use'=>array('label'=>$langs->trans("Fieldfk_unit_use"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
't.mark'=>array('label'=>$langs->trans("Fieldmark"), 'checked'=>1),
't.been'=>array('label'=>$langs->trans("Fieldbeen"), 'checked'=>1),
't.fk_asset_mov'=>array('label'=>$langs->trans("Fieldfk_asset_mov"), 'checked'=>1),
't.status_reval'=>array('label'=>$langs->trans("Fieldstatus_reval"), 'checked'=>1),
't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),

    
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
$object=new Assets($db);
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
$search_fk_father='';
$search_fk_facture='';
$search_type_group='';
$search_type_patrim='';
$search_ref='';
$search_item_asset='';
$search_useful_life_residual='';
$search_quant='';
$search_coste='';
$search_coste_residual='';
$search_coste_reval='';
$search_coste_residual_reval='';
$search_amount_sale='';
$search_descrip='';
$search_number_plaque='';
$search_trademark='';
$search_model='';
$search_anio='';
$search_fk_asset_sup='';
$search_fk_location='';
$search_code_bar='';
$search_fk_method_dep='';
$search_type_property='';
$search_code_bim='';
$search_fk_product='';
$search_useful_life='';
$search_percent='';
$search_account_accounting='';
$search_fk_unit='';
$search_model_pdf='';
$search_coste_unit_use='';
$search_fk_unit_use='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_mark='';
$search_been='';
$search_fk_asset_mov='';
$search_status_reval='';
$search_statut='';

    	
    	$search_date_creation='';
    	$search_date_update='';
        $toselect='';
        $search_array_options=array();
    }

    // Mass actions
    $objectclass='Skeleton';
    $objectlabel='Skeleton';
    $permtoread = $user->rights->assets->read;
    $permtodelete = $user->rights->assets->delete;
    $uploaddir = $conf->assets->dir_output;
    include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
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
		$sql .= " t.fk_father,";
		$sql .= " t.fk_facture,";
		$sql .= " t.type_group,";
		$sql .= " t.type_patrim,";
		$sql .= " t.ref,";
		$sql .= " t.item_asset,";
		$sql .= " t.date_adq,";
		$sql .= " t.date_active,";
		$sql .= " t.date_reval,";
		$sql .= " t.useful_life_residual,";
		$sql .= " t.quant,";
		$sql .= " t.coste,";
		$sql .= " t.coste_residual,";
		$sql .= " t.coste_reval,";
		$sql .= " t.coste_residual_reval,";
		$sql .= " t.date_baja,";
		$sql .= " t.amount_sale,";
		$sql .= " t.descrip,";
		$sql .= " t.number_plaque,";
		$sql .= " t.trademark,";
		$sql .= " t.model,";
		$sql .= " t.anio,";
		$sql .= " t.fk_asset_sup,";
		$sql .= " t.fk_location,";
		$sql .= " t.code_bar,";
		$sql .= " t.fk_method_dep,";
		$sql .= " t.type_property,";
		$sql .= " t.code_bim,";
		$sql .= " t.fk_product,";
		$sql .= " t.useful_life,";
		$sql .= " t.percent,";
		$sql .= " t.account_accounting,";
		$sql .= " t.fk_unit,";
		$sql .= " t.model_pdf,";
		$sql .= " t.coste_unit_use,";
		$sql .= " t.fk_unit_use,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.mark,";
		$sql .= " t.been,";
		$sql .= " t.tms,";
		$sql .= " t.fk_asset_mov,";
		$sql .= " t.status_reval,";
		$sql .= " t.statut";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."assets_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_father) $sql.= natural_search("fk_father",$search_fk_father);
if ($search_fk_facture) $sql.= natural_search("fk_facture",$search_fk_facture);
if ($search_type_group) $sql.= natural_search("type_group",$search_type_group);
if ($search_type_patrim) $sql.= natural_search("type_patrim",$search_type_patrim);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_item_asset) $sql.= natural_search("item_asset",$search_item_asset);
if ($search_useful_life_residual) $sql.= natural_search("useful_life_residual",$search_useful_life_residual);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_coste) $sql.= natural_search("coste",$search_coste);
if ($search_coste_residual) $sql.= natural_search("coste_residual",$search_coste_residual);
if ($search_coste_reval) $sql.= natural_search("coste_reval",$search_coste_reval);
if ($search_coste_residual_reval) $sql.= natural_search("coste_residual_reval",$search_coste_residual_reval);
if ($search_amount_sale) $sql.= natural_search("amount_sale",$search_amount_sale);
if ($search_descrip) $sql.= natural_search("descrip",$search_descrip);
if ($search_number_plaque) $sql.= natural_search("number_plaque",$search_number_plaque);
if ($search_trademark) $sql.= natural_search("trademark",$search_trademark);
if ($search_model) $sql.= natural_search("model",$search_model);
if ($search_anio) $sql.= natural_search("anio",$search_anio);
if ($search_fk_asset_sup) $sql.= natural_search("fk_asset_sup",$search_fk_asset_sup);
if ($search_fk_location) $sql.= natural_search("fk_location",$search_fk_location);
if ($search_code_bar) $sql.= natural_search("code_bar",$search_code_bar);
if ($search_fk_method_dep) $sql.= natural_search("fk_method_dep",$search_fk_method_dep);
if ($search_type_property) $sql.= natural_search("type_property",$search_type_property);
if ($search_code_bim) $sql.= natural_search("code_bim",$search_code_bim);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_useful_life) $sql.= natural_search("useful_life",$search_useful_life);
if ($search_percent) $sql.= natural_search("percent",$search_percent);
if ($search_account_accounting) $sql.= natural_search("account_accounting",$search_account_accounting);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_model_pdf) $sql.= natural_search("model_pdf",$search_model_pdf);
if ($search_coste_unit_use) $sql.= natural_search("coste_unit_use",$search_coste_unit_use);
if ($search_fk_unit_use) $sql.= natural_search("fk_unit_use",$search_fk_unit_use);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_mark) $sql.= natural_search("mark",$search_mark);
if ($search_been) $sql.= natural_search("been",$search_been);
if ($search_fk_asset_mov) $sql.= natural_search("fk_asset_mov",$search_fk_asset_mov);
if ($search_status_reval) $sql.= natural_search("status_reval",$search_status_reval);
if ($search_statut) $sql.= natural_search("statut",$search_statut);


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
    header("Location: ".DOL_URL_ROOT.'/assets/card.php?id='.$id);
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
if ($user->rights->assets->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
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
$moreforfilter.='<div class="divsearchfield">';
$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
$moreforfilter.= '</div>';

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
if (! empty($arrayfields['t.fk_father']['checked'])) print_liste_field_titre($arrayfields['t.fk_father']['label'],$_SERVER['PHP_SELF'],'t.fk_father','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_facture']['checked'])) print_liste_field_titre($arrayfields['t.fk_facture']['label'],$_SERVER['PHP_SELF'],'t.fk_facture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_group']['checked'])) print_liste_field_titre($arrayfields['t.type_group']['label'],$_SERVER['PHP_SELF'],'t.type_group','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_patrim']['checked'])) print_liste_field_titre($arrayfields['t.type_patrim']['label'],$_SERVER['PHP_SELF'],'t.type_patrim','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.item_asset']['checked'])) print_liste_field_titre($arrayfields['t.item_asset']['label'],$_SERVER['PHP_SELF'],'t.item_asset','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.useful_life_residual']['checked'])) print_liste_field_titre($arrayfields['t.useful_life_residual']['label'],$_SERVER['PHP_SELF'],'t.useful_life_residual','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.coste']['checked'])) print_liste_field_titre($arrayfields['t.coste']['label'],$_SERVER['PHP_SELF'],'t.coste','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.coste_residual']['checked'])) print_liste_field_titre($arrayfields['t.coste_residual']['label'],$_SERVER['PHP_SELF'],'t.coste_residual','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.coste_reval']['checked'])) print_liste_field_titre($arrayfields['t.coste_reval']['label'],$_SERVER['PHP_SELF'],'t.coste_reval','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.coste_residual_reval']['checked'])) print_liste_field_titre($arrayfields['t.coste_residual_reval']['label'],$_SERVER['PHP_SELF'],'t.coste_residual_reval','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_sale']['checked'])) print_liste_field_titre($arrayfields['t.amount_sale']['label'],$_SERVER['PHP_SELF'],'t.amount_sale','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.descrip']['checked'])) print_liste_field_titre($arrayfields['t.descrip']['label'],$_SERVER['PHP_SELF'],'t.descrip','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.number_plaque']['checked'])) print_liste_field_titre($arrayfields['t.number_plaque']['label'],$_SERVER['PHP_SELF'],'t.number_plaque','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.trademark']['checked'])) print_liste_field_titre($arrayfields['t.trademark']['label'],$_SERVER['PHP_SELF'],'t.trademark','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model']['checked'])) print_liste_field_titre($arrayfields['t.model']['label'],$_SERVER['PHP_SELF'],'t.model','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.anio']['checked'])) print_liste_field_titre($arrayfields['t.anio']['label'],$_SERVER['PHP_SELF'],'t.anio','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_asset_sup']['checked'])) print_liste_field_titre($arrayfields['t.fk_asset_sup']['label'],$_SERVER['PHP_SELF'],'t.fk_asset_sup','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_location']['checked'])) print_liste_field_titre($arrayfields['t.fk_location']['label'],$_SERVER['PHP_SELF'],'t.fk_location','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_bar']['checked'])) print_liste_field_titre($arrayfields['t.code_bar']['label'],$_SERVER['PHP_SELF'],'t.code_bar','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_method_dep']['checked'])) print_liste_field_titre($arrayfields['t.fk_method_dep']['label'],$_SERVER['PHP_SELF'],'t.fk_method_dep','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_property']['checked'])) print_liste_field_titre($arrayfields['t.type_property']['label'],$_SERVER['PHP_SELF'],'t.type_property','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_bim']['checked'])) print_liste_field_titre($arrayfields['t.code_bim']['label'],$_SERVER['PHP_SELF'],'t.code_bim','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.useful_life']['checked'])) print_liste_field_titre($arrayfields['t.useful_life']['label'],$_SERVER['PHP_SELF'],'t.useful_life','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.percent']['checked'])) print_liste_field_titre($arrayfields['t.percent']['label'],$_SERVER['PHP_SELF'],'t.percent','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.account_accounting']['checked'])) print_liste_field_titre($arrayfields['t.account_accounting']['label'],$_SERVER['PHP_SELF'],'t.account_accounting','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model_pdf']['checked'])) print_liste_field_titre($arrayfields['t.model_pdf']['label'],$_SERVER['PHP_SELF'],'t.model_pdf','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.coste_unit_use']['checked'])) print_liste_field_titre($arrayfields['t.coste_unit_use']['label'],$_SERVER['PHP_SELF'],'t.coste_unit_use','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit_use']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit_use']['label'],$_SERVER['PHP_SELF'],'t.fk_unit_use','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.mark']['checked'])) print_liste_field_titre($arrayfields['t.mark']['label'],$_SERVER['PHP_SELF'],'t.mark','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.been']['checked'])) print_liste_field_titre($arrayfields['t.been']['label'],$_SERVER['PHP_SELF'],'t.been','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_asset_mov']['checked'])) print_liste_field_titre($arrayfields['t.fk_asset_mov']['label'],$_SERVER['PHP_SELF'],'t.fk_asset_mov','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status_reval']['checked'])) print_liste_field_titre($arrayfields['t.status_reval']['label'],$_SERVER['PHP_SELF'],'t.status_reval','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_father']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_father" value="'.$search_fk_father.'" size="10"></td>';
if (! empty($arrayfields['t.fk_facture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_facture" value="'.$search_fk_facture.'" size="10"></td>';
if (! empty($arrayfields['t.type_group']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_group" value="'.$search_type_group.'" size="10"></td>';
if (! empty($arrayfields['t.type_patrim']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_patrim" value="'.$search_type_patrim.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.item_asset']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_item_asset" value="'.$search_item_asset.'" size="10"></td>';
if (! empty($arrayfields['t.useful_life_residual']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_useful_life_residual" value="'.$search_useful_life_residual.'" size="10"></td>';
if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
if (! empty($arrayfields['t.coste']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_coste" value="'.$search_coste.'" size="10"></td>';
if (! empty($arrayfields['t.coste_residual']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_coste_residual" value="'.$search_coste_residual.'" size="10"></td>';
if (! empty($arrayfields['t.coste_reval']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_coste_reval" value="'.$search_coste_reval.'" size="10"></td>';
if (! empty($arrayfields['t.coste_residual_reval']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_coste_residual_reval" value="'.$search_coste_residual_reval.'" size="10"></td>';
if (! empty($arrayfields['t.amount_sale']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_sale" value="'.$search_amount_sale.'" size="10"></td>';
if (! empty($arrayfields['t.descrip']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_descrip" value="'.$search_descrip.'" size="10"></td>';
if (! empty($arrayfields['t.number_plaque']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_number_plaque" value="'.$search_number_plaque.'" size="10"></td>';
if (! empty($arrayfields['t.trademark']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_trademark" value="'.$search_trademark.'" size="10"></td>';
if (! empty($arrayfields['t.model']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model" value="'.$search_model.'" size="10"></td>';
if (! empty($arrayfields['t.anio']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_anio" value="'.$search_anio.'" size="10"></td>';
if (! empty($arrayfields['t.fk_asset_sup']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_asset_sup" value="'.$search_fk_asset_sup.'" size="10"></td>';
if (! empty($arrayfields['t.fk_location']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_location" value="'.$search_fk_location.'" size="10"></td>';
if (! empty($arrayfields['t.code_bar']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_bar" value="'.$search_code_bar.'" size="10"></td>';
if (! empty($arrayfields['t.fk_method_dep']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_method_dep" value="'.$search_fk_method_dep.'" size="10"></td>';
if (! empty($arrayfields['t.type_property']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_property" value="'.$search_type_property.'" size="10"></td>';
if (! empty($arrayfields['t.code_bim']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_bim" value="'.$search_code_bim.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.useful_life']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_useful_life" value="'.$search_useful_life.'" size="10"></td>';
if (! empty($arrayfields['t.percent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_percent" value="'.$search_percent.'" size="10"></td>';
if (! empty($arrayfields['t.account_accounting']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_account_accounting" value="'.$search_account_accounting.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.model_pdf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model_pdf" value="'.$search_model_pdf.'" size="10"></td>';
if (! empty($arrayfields['t.coste_unit_use']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_coste_unit_use" value="'.$search_coste_unit_use.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit_use']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit_use" value="'.$search_fk_unit_use.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.mark']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_mark" value="'.$search_mark.'" size="10"></td>';
if (! empty($arrayfields['t.been']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_been" value="'.$search_been.'" size="10"></td>';
if (! empty($arrayfields['t.fk_asset_mov']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_asset_mov" value="'.$search_fk_asset_mov.'" size="10"></td>';
if (! empty($arrayfields['t.status_reval']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status_reval" value="'.$search_status_reval.'" size="10"></td>';
if (! empty($arrayfields['t.statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut" value="'.$search_statut.'" size="10"></td>';

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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
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
        /*
        if (! empty($arrayfields['t.field1']['checked'])) 
        {
            print '<td>'.$obj->field1.'</td>';
		    if (! $i) $totalarray['nbfield']++;
        }
        if (! empty($arrayfields['t.field2']['checked'])) 
        {
            print '<td>'.$obj->field2.'</td>';
		    if (! $i) $totalarray['nbfield']++;
        }*/
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


if ($massaction == 'builddoc' || $action == 'remove_file' || $show_files)
{
    // Show list of available documents
    $urlsource=$_SERVER['PHP_SELF'].'?sortfield='.$sortfield.'&sortorder='.$sortorder;
    $urlsource.=str_replace('&amp;','&',$param);

    $filedir=$diroutputmassaction;
    $genallowed=$user->rights->facture->lire;
    $delallowed=$user->rights->facture->lire;

    print $formfile->showdocuments('massfilesarea_assets','',$filedir,$urlsource,0,$delallowed,'',1,1,0,48,1,$param,$title,'');
}
else
{
    print '<br><a name="show_files"></a><a href="'.$_SERVER["PHP_SELF"].'?show_files=1'.$param.'#show_files">'.$langs->trans("ShowTempMassFilesArea").'</a>';
}


// End of page
llxFooter();
$db->close();
