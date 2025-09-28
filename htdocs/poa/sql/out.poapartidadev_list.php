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
 *   	\file       poa/poapartidadev_list.php
 *		\ingroup    poa
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-07-12 16:09
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
dol_include_once('/poa/class/poapartidadev.class.php');

// Load traductions files requiredby by page
$langs->load("poa");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_poa_partida_com=GETPOST('search_fk_poa_partida_com','int');
$search_gestion=GETPOST('search_gestion','int');
$search_fk_poa_prev=GETPOST('search_fk_poa_prev','int');
$search_fk_structure=GETPOST('search_fk_structure','int');
$search_fk_poa=GETPOST('search_fk_poa','int');
$search_fk_contrat=GETPOST('search_fk_contrat','int');
$search_fk_contrato=GETPOST('search_fk_contrato','int');
$search_type_pay=GETPOST('search_type_pay','int');
$search_nro_dev=GETPOST('search_nro_dev','int');
$search_partida=GETPOST('search_partida','alpha');
$search_invoice=GETPOST('search_invoice','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_statut=GETPOST('search_statut','int');
$search_active=GETPOST('search_active','int');


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

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('poapartidadevlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('poa');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Poapartidadev($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_poa_partida_com'=>array('label'=>$langs->trans("Fieldfk_poa_partida_com"), 'checked'=>1),
't.gestion'=>array('label'=>$langs->trans("Fieldgestion"), 'checked'=>1),
't.fk_poa_prev'=>array('label'=>$langs->trans("Fieldfk_poa_prev"), 'checked'=>1),
't.fk_structure'=>array('label'=>$langs->trans("Fieldfk_structure"), 'checked'=>1),
't.fk_poa'=>array('label'=>$langs->trans("Fieldfk_poa"), 'checked'=>1),
't.fk_contrat'=>array('label'=>$langs->trans("Fieldfk_contrat"), 'checked'=>1),
't.fk_contrato'=>array('label'=>$langs->trans("Fieldfk_contrato"), 'checked'=>1),
't.type_pay'=>array('label'=>$langs->trans("Fieldtype_pay"), 'checked'=>1),
't.nro_dev'=>array('label'=>$langs->trans("Fieldnro_dev"), 'checked'=>1),
't.partida'=>array('label'=>$langs->trans("Fieldpartida"), 'checked'=>1),
't.invoice'=>array('label'=>$langs->trans("Fieldinvoice"), 'checked'=>1),
't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),
't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),

    
    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
    't.datec'=>array('label'=>$langs->trans("DateCreation"), 'checked'=>0, 'position'=>500),
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




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{
	
$search_fk_poa_partida_com='';
$search_gestion='';
$search_fk_poa_prev='';
$search_fk_structure='';
$search_fk_poa='';
$search_fk_contrat='';
$search_fk_contrato='';
$search_type_pay='';
$search_nro_dev='';
$search_partida='';
$search_invoice='';
$search_amount='';
$search_statut='';
$search_active='';

	
	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}


if (empty($reshook))
{
	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/poa/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

$form=new Form($db);

// Put here content of your page
$title = $langs->trans('MyModuleListTitle');

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

		$sql .= " t.fk_poa_partida_com,";
		$sql .= " t.gestion,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.fk_contrato,";
		$sql .= " t.type_pay,";
		$sql .= " t.nro_dev,";
		$sql .= " t.date_dev,";
		$sql .= " t.partida,";
		$sql .= " t.invoice,";
		$sql .= " t.amount,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut,";
		$sql .= " t.active";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."poa_partida_dev_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_poa_partida_com) $sql.= natural_search("fk_poa_partida_com",$search_fk_poa_partida_com);
if ($search_gestion) $sql.= natural_search("gestion",$search_gestion);
if ($search_fk_poa_prev) $sql.= natural_search("fk_poa_prev",$search_fk_poa_prev);
if ($search_fk_structure) $sql.= natural_search("fk_structure",$search_fk_structure);
if ($search_fk_poa) $sql.= natural_search("fk_poa",$search_fk_poa);
if ($search_fk_contrat) $sql.= natural_search("fk_contrat",$search_fk_contrat);
if ($search_fk_contrato) $sql.= natural_search("fk_contrato",$search_fk_contrato);
if ($search_type_pay) $sql.= natural_search("type_pay",$search_type_pay);
if ($search_nro_dev) $sql.= natural_search("nro_dev",$search_nro_dev);
if ($search_partida) $sql.= natural_search("partida",$search_partida);
if ($search_invoice) $sql.= natural_search("invoice",$search_invoice);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_statut) $sql.= natural_search("statut",$search_statut);
if ($search_active) $sql.= natural_search("active",$search_active);


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
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}	

$sql.= $db->plimit($conf->liste_limit+1, $offset);


dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
	
if ($search_fk_poa_partida_com != '') $params.= '&amp;search_fk_poa_partida_com='.urlencode($search_fk_poa_partida_com);
if ($search_gestion != '') $params.= '&amp;search_gestion='.urlencode($search_gestion);
if ($search_fk_poa_prev != '') $params.= '&amp;search_fk_poa_prev='.urlencode($search_fk_poa_prev);
if ($search_fk_structure != '') $params.= '&amp;search_fk_structure='.urlencode($search_fk_structure);
if ($search_fk_poa != '') $params.= '&amp;search_fk_poa='.urlencode($search_fk_poa);
if ($search_fk_contrat != '') $params.= '&amp;search_fk_contrat='.urlencode($search_fk_contrat);
if ($search_fk_contrato != '') $params.= '&amp;search_fk_contrato='.urlencode($search_fk_contrato);
if ($search_type_pay != '') $params.= '&amp;search_type_pay='.urlencode($search_type_pay);
if ($search_nro_dev != '') $params.= '&amp;search_nro_dev='.urlencode($search_nro_dev);
if ($search_partida != '') $params.= '&amp;search_partida='.urlencode($search_partida);
if ($search_invoice != '') $params.= '&amp;search_invoice='.urlencode($search_invoice);
if ($search_amount != '') $params.= '&amp;search_amount='.urlencode($search_amount);
if ($search_statut != '') $params.= '&amp;search_statut='.urlencode($search_statut);
if ($search_active != '') $params.= '&amp;search_active='.urlencode($search_active);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 
    
    print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');
    

	print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	
    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
    }
    
	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
    	$parameters=array();
    	$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	    print $hookmanager->resPrint;
	    print '</div>';
	}

    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
	
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
    print '<tr class="liste_titre">';
    
if (! empty($arrayfields['t.fk_poa_partida_com']['checked'])) print_liste_field_titre($arrayfields['t.fk_poa_partida_com']['label'],$_SERVER['PHP_SELF'],'t.fk_poa_partida_com','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.gestion']['checked'])) print_liste_field_titre($arrayfields['t.gestion']['label'],$_SERVER['PHP_SELF'],'t.gestion','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_poa_prev']['checked'])) print_liste_field_titre($arrayfields['t.fk_poa_prev']['label'],$_SERVER['PHP_SELF'],'t.fk_poa_prev','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_structure']['checked'])) print_liste_field_titre($arrayfields['t.fk_structure']['label'],$_SERVER['PHP_SELF'],'t.fk_structure','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_poa']['checked'])) print_liste_field_titre($arrayfields['t.fk_poa']['label'],$_SERVER['PHP_SELF'],'t.fk_poa','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_contrat']['checked'])) print_liste_field_titre($arrayfields['t.fk_contrat']['label'],$_SERVER['PHP_SELF'],'t.fk_contrat','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_contrato']['checked'])) print_liste_field_titre($arrayfields['t.fk_contrato']['label'],$_SERVER['PHP_SELF'],'t.fk_contrato','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_pay']['checked'])) print_liste_field_titre($arrayfields['t.type_pay']['label'],$_SERVER['PHP_SELF'],'t.type_pay','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nro_dev']['checked'])) print_liste_field_titre($arrayfields['t.nro_dev']['label'],$_SERVER['PHP_SELF'],'t.nro_dev','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.partida']['checked'])) print_liste_field_titre($arrayfields['t.partida']['label'],$_SERVER['PHP_SELF'],'t.partida','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.invoice']['checked'])) print_liste_field_titre($arrayfields['t.invoice']['label'],$_SERVER['PHP_SELF'],'t.invoice','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$param,'',$sortfield,$sortorder);

    
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
	if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($langs->trans("DateCreationShort"),$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($langs->trans("DateModificationShort"),$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
    print '</tr>'."\n";

    // Fields title search
	print '<tr class="liste_titre">';
	
if (! empty($arrayfields['t.fk_poa_partida_com']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_poa_partida_com" value="'.$search_fk_poa_partida_com.'" size="10"></td>';
if (! empty($arrayfields['t.gestion']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_gestion" value="'.$search_gestion.'" size="10"></td>';
if (! empty($arrayfields['t.fk_poa_prev']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_poa_prev" value="'.$search_fk_poa_prev.'" size="10"></td>';
if (! empty($arrayfields['t.fk_structure']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_structure" value="'.$search_fk_structure.'" size="10"></td>';
if (! empty($arrayfields['t.fk_poa']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_poa" value="'.$search_fk_poa.'" size="10"></td>';
if (! empty($arrayfields['t.fk_contrat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_contrat" value="'.$search_fk_contrat.'" size="10"></td>';
if (! empty($arrayfields['t.fk_contrato']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_contrato" value="'.$search_fk_contrato.'" size="10"></td>';
if (! empty($arrayfields['t.type_pay']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_pay" value="'.$search_type_pay.'" size="10"></td>';
if (! empty($arrayfields['t.nro_dev']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nro_dev" value="'.$search_nro_dev.'" size="10"></td>';
if (! empty($arrayfields['t.partida']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_partida" value="'.$search_partida.'" size="10"></td>';
if (! empty($arrayfields['t.invoice']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_invoice" value="'.$search_invoice.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut" value="'.$search_statut.'" size="10"></td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';

	
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
	print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
	print '</td>';
	print '</tr>'."\n";
        
    
    $i = 0;
    while ($i < $num)
    {
        $obj = $db->fetch_object($resql);
        if ($obj)
        {
            // You can use here results
            print '<tr>';
            
if (! empty($arrayfields['t.fk_poa_partida_com']['checked'])) print '<td>'.$obj->fk_poa_partida_com.'</td>';
if (! empty($arrayfields['t.gestion']['checked'])) print '<td>'.$obj->gestion.'</td>';
if (! empty($arrayfields['t.fk_poa_prev']['checked'])) print '<td>'.$obj->fk_poa_prev.'</td>';
if (! empty($arrayfields['t.fk_structure']['checked'])) print '<td>'.$obj->fk_structure.'</td>';
if (! empty($arrayfields['t.fk_poa']['checked'])) print '<td>'.$obj->fk_poa.'</td>';
if (! empty($arrayfields['t.fk_contrat']['checked'])) print '<td>'.$obj->fk_contrat.'</td>';
if (! empty($arrayfields['t.fk_contrato']['checked'])) print '<td>'.$obj->fk_contrato.'</td>';
if (! empty($arrayfields['t.type_pay']['checked'])) print '<td>'.$obj->type_pay.'</td>';
if (! empty($arrayfields['t.nro_dev']['checked'])) print '<td>'.$obj->nro_dev.'</td>';
if (! empty($arrayfields['t.partida']['checked'])) print '<td>'.$obj->partida.'</td>';
if (! empty($arrayfields['t.invoice']['checked'])) print '<td>'.$obj->invoice.'</td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td>'.$obj->amount.'</td>';
if (! empty($arrayfields['t.statut']['checked'])) print '<td>'.$obj->statut.'</td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td>'.$obj->active.'</td>';

            
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
            }
            // Date modification
            if (! empty($arrayfields['t.tms']['checked']))
            {
                print '<td align="center">';
                print dol_print_date($db->jdate($obj->date_update), 'dayhour');
                print '</td>';
            }
            // Status
            /*
            if (! empty($arrayfields['u.statut']['checked']))
            {
    		  $userstatic->statut=$obj->statut;
              print '<td align="center">'.$userstatic->getLibStatut(3).'</td>';
            }*/
            // Action column
            print '<td></td>';
    		print '</tr>';
        }
        $i++;
    }
    
    $db->free($resql);

	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";
	
	$db->free($result);
}
else
{
    $error++;
    dol_print_error($db);
}


// End of page
llxFooter();
$db->close();
