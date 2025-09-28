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
 *   	\file       purchase/purchaserequest_list.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-10 09:46
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
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.getutil.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
dol_include_once('/purchase/class/purchaserequestext.class.php');
require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';

if ($conf->orgman->enabled)
{
	dol_include_once('/orgman/class/pdepartamentext.class.php');
	dol_include_once('/orgman/class/pdepartamentuserext.class.php');
}

// Load traductions files requiredby by page
$langs->load("purchase@purchase");
if ($conf->poa->enabled) $langs->load("poa");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_date_delivery = dol_mktime(GETPOST('dd_hour'), GETPOST('dd_min'), 0, GETPOST('dd_month'), GETPOST('dd_day'), GETPOST('dd_year'));
$search_datec = dol_mktime(GETPOST('dc_hour'), GETPOST('dc_min'), 0, GETPOST('dc_month'), GETPOST('dc_day'), GETPOST('dc_year'));


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_ref_int=GETPOST('search_ref_int','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_prev=GETPOST('search_prev','int');
$search_gestion=GETPOST('search_gestion','int');
$search_fk_departament=GETPOST('search_fk_departament','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_user_cloture=GETPOST('search_fk_user_cloture','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_fk_shipping_method=GETPOST('search_fk_shipping_method','int');
$search_import_key=GETPOST('search_import_key','alpha');
$search_extraparams=GETPOST('search_extraparams','alpha');
$search_status=GETPOST('search_status','int');
if(isset($_GET['search_status_process']) || isset($_POST['search_status_process']))
	$search_status_process=GETPOST('search_status_process','int');
else
	$search_status_process = 9;

$aStatu=array();
$aStatu[9]=$langs->trans('All');
$aStatu[0]=$langs->trans('Draft');
$aStatu[1]=$langs->trans('Validated');

//status_process
$aStatusprocess=array();
$aStatusprocess[9]=$langs->trans('All');
//$aStatusprocess[0]=$langs->trans('Draft');
$aStatusprocess[0]=$langs->trans('Preventive');
$aStatusprocess[1]=$langs->trans('Processstarted');
$aStatusprocess[2]=$langs->trans('Proposalselection');
$aStatusprocess[3]=$langs->trans('Commited');




$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page)||$page == -1) { $page = 0; }
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

//filtro por area
$aFilterent = array();
$filterarea = '';
$aFilterarea = array();
$fk_areaasign = 0;
if (!$user->admin)
{
	if ($conf->orgman->enabled)
	{
		$objDepartamentuser = new Pdepartamentuserext($db);
		$res = $objDepartamentuser->getuserarea($user->id,true);
		if ($res > 0)
		{
			$fk_areaasign = $objDepartamentuser->fk_areaasign;
			foreach ($objDepartamentuser->aArea AS $j => $data)
			{
				if ($filterarea) $filterarea.= ',';
				$filterarea.= $j;
				$aFilterarea[$j]=$j;
			}
		}
	}
}


// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('purchaserequestlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('purchase');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Purchaserequestext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objFournisseurCommande = new Fournisseurcommandeext($db);

// Definition of fields for list
$arrayfields=array(
	't.ref'=>array('label'=>$langs->trans("Fieldref_"), 'checked'=>1),
	't.date_delivery'=>array('label'=>$langs->trans("Fielddatedelivery"), 'checked'=>1),
	't.datec'=>array('label'=>$langs->trans("Fielddatecreate"), 'checked'=>1),
	'p.nro_preventive'=>array('label'=>$langs->trans("Fieldpreventive"), 'checked'=>1),
	'p.gestion'=>array('label'=>$langs->trans("Fieldgestion"), 'checked'=>1),
	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>0),
	't.ref_int'=>array('label'=>$langs->trans("Fieldref_int"), 'checked'=>0),
	't.fk_projet'=>array('label'=>$langs->trans("Fieldfk_projet"), 'checked'=>0),
	't.fk_departament'=>array('label'=>$langs->trans("Fieldfk_departament"), 'checked'=>1),
	//'t.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>1),
	//'t.fk_user_modif'=>array('label'=>$langs->trans("Fieldfk_user_modif"), 'checked'=>0),
	//'t.fk_user_valid'=>array('label'=>$langs->trans("Fieldfk_user_valid"), 'checked'=>0),
	//'t.fk_user_cloture'=>array('label'=>$langs->trans("Fieldfk_user_cloture"), 'checked'=>0),
	't.note_private'=>array('label'=>$langs->trans("Fieldnote_private"), 'checked'=>1),
	't.note_public'=>array('label'=>$langs->trans("Fieldnote_public"), 'checked'=>1),
	'linkped'=>array('label'=>$langs->trans("Fieldcommande"), 'checked'=>1),
	//'t.fk_shipping_method'=>array('label'=>$langs->trans("Fieldfk_shipping_method"), 'checked'=>1),
	//'t.import_key'=>array('label'=>$langs->trans("Fieldimport_key"), 'checked'=>1),
	//'t.extraparams'=>array('label'=>$langs->trans("Fieldextraparams"), 'checked'=>1),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>1),
	't.status_process'=>array('label'=>$langs->trans("Fieldstatusprocess"), 'checked'=>1),

	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
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

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{

	$search_entity='';
	$search_ref='';
	$search_ref_ext='';
	$search_ref_int='';
	$search_fk_projet='';
	$search_prev='';
	$search_gestion='';
	$search_fk_departament='';
	$search_fk_user_author='';
	$search_fk_user_modif='';
	$search_fk_user_valid='';
	$search_fk_user_cloture='';
	$search_note_private='';
	$search_note_public='';
	$search_model_pdf='';
	$search_fk_shipping_method='';
	$search_import_key='';
	$search_extraparams='';
	$search_status=9;
	$search_status_process=9;
	$search_date_delivery='';
	$search_datec='';


	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}


if (empty($reshook))
{
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
			header("Location: ".dol_buildpath('/purchase/list.php',1));
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

$now=dol_now();

$form=new Form($db);
$getUtil = new getUtil($db);


//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Purchaserequest');
llxHeader('', $title, $help_url);

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
$sql .= " t.ref_int,";
$sql .= " t.fk_projet,";
$sql .= " t.fk_poa_prev,";
$sql .= " t.fk_departament,";
//$sql .= " d.ref as fk_departament,";
$sql .= " t.tms,";
$sql .= " t.datec,";
$sql .= " t.date_valid,";
$sql .= " t.date_cloture,";
$sql .= " t.fk_user_author,";
$sql .= " t.fk_user_modif,";
$sql .= " t.fk_user_valid,";
$sql .= " t.fk_user_cloture,";
$sql .= " t.note_private,";
$sql .= " t.note_public,";
$sql .= " t.model_pdf,";
$sql .= " t.date_delivery,";
$sql .= " t.date_livraison,";
$sql .= " t.fk_shipping_method,";
$sql .= " t.import_key,";
$sql .= " t.extraparams,";
$sql .= " t.datem,";
$sql .= " t.status,";
$sql .= " t.status_process, ";
$sql .= " t.rowid AS linkped";
if ($conf->poa->enabled)
{
	$sql .= " , p.nro_preventive";
	$sql .= " , p.gestion";
}
$sql.= " , d.ref AS refdepartament";
$sql.= " , d.label AS labeldepartament";

// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."purchase_request as t";
if ($conf->poa->enabled)
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'poa_prev AS p ON t.fk_poa_prev = p.rowid';
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'p_departament AS d ON t.fk_departament = d.rowid';
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."purchase_request_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";


if ($user->rights->poa->prev->valplanall || $user->rights->poa->prev->valpresall)
{
	//no se filtra por area. ve todo
}
else
{
	if ($user->rights->purchase->req->readall)
	{
		//no se filtra nada sobre las areas
	}
	else
		if ($filterarea) $sql.= " AND t.fk_departament IN (".$filterarea.")";
}
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("t.ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_ref_int) $sql.= natural_search("ref_int",$search_ref_int);
if ($search_fk_projet) $sql.= natural_search("fk_projet",$search_fk_projet);
if ($search_prev) $sql.= natural_search("p.nro_preventive",$search_prev);
if ($search_gestion) $sql.= natural_search("p.gestion",$search_gestion);
if ($search_fk_departament) $sql.= natural_search(array("t.fk_departament","d.ref","d.label"),$search_fk_departament);
if ($search_fk_user_author) $sql.= natural_search("fk_user_author",$search_fk_user_author);
if ($search_fk_user_modif) $sql.= natural_search("fk_user_modif",$search_fk_user_modif);
if ($search_fk_user_valid) $sql.= natural_search("fk_user_valid",$search_fk_user_valid);
if ($search_fk_user_cloture) $sql.= natural_search("fk_user_cloture",$search_fk_user_cloture);
if ($search_note_private) $sql.= natural_search("note_private",$search_note_private);
if ($search_note_public) $sql.= natural_search("note_public",$search_note_public);
if ($search_model_pdf) $sql.= natural_search("model_pdf",$search_model_pdf);
if ($search_fk_shipping_method) $sql.= natural_search("fk_shipping_method",$search_fk_shipping_method);
if ($search_import_key) $sql.= natural_search("import_key",$search_import_key);
if ($search_extraparams) $sql.= natural_search("extraparams",$search_extraparams);
if ($search_status!=9) $sql.= natural_search("t.status",$search_status);
//if ($search_status_process!=9) $sql.= natural_search("status_process",$search_status_process);
if ($search_status_process!=9 && $search_status_process >=0)
	$sql.= " AND (status_process = ".$search_status_process." AND t.status > 0)";
if ($search_date_delivery)
{
	$aDateDelivery = dol_getdate($search_date_delivery);
	$sql.= " AND month(t.date_delivery) = ".$aDateDelivery['mon'];
	$sql.= " AND year(t.date_delivery) = ".$aDateDelivery['year'];
	$sql.= " AND day(t.date_delivery) = ".$aDateDelivery['mday'];
}
if ($search_datec)
{
	$aDatec = dol_getdate($search_datec);
	$sql.= " AND month(t.datec) = ".$aDatec['mon'];
	$sql.= " AND year(t.datec) = ".$aDatec['year'];
	$sql.= " AND day(t.datec) = ".$aDatec['mday'];
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
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);

	$params='';
	$param='';
	if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
	if ($limit > 0 && $limit != $conf->liste_limit) $params.='&limit='.$limit;

	if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
	if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
	if ($search_ref_ext != '') $params.= '&amp;search_ref_ext='.urlencode($search_ref_ext);
	if ($search_ref_int != '') $params.= '&amp;search_ref_int='.urlencode($search_ref_int);
	if ($search_prev != '') $params.= '&amp;search_prev='.urlencode($search_prev);
	if ($search_gestion != '') $params.= '&amp;search_gestion='.urlencode($search_gestion);
	if ($search_fk_projet != '') $params.= '&amp;search_fk_projet='.urlencode($search_fk_projet);
	if ($search_fk_departament != '') $params.= '&amp;search_fk_departament='.urlencode($search_fk_departament);
	if ($search_fk_user_author != '') $params.= '&amp;search_fk_user_author='.urlencode($search_fk_user_author);
	if ($search_fk_user_modif != '') $params.= '&amp;search_fk_user_modif='.urlencode($search_fk_user_modif);
	if ($search_fk_user_valid != '') $params.= '&amp;search_fk_user_valid='.urlencode($search_fk_user_valid);
	if ($search_fk_user_cloture != '') $params.= '&amp;search_fk_user_cloture='.urlencode($search_fk_user_cloture);
	if ($search_note_private != '') $params.= '&amp;search_note_private='.urlencode($search_note_private);
	if ($search_note_public != '') $params.= '&amp;search_note_public='.urlencode($search_note_public);
	if ($search_model_pdf != '') $params.= '&amp;search_model_pdf='.urlencode($search_model_pdf);
	if ($search_fk_shipping_method != '') $params.= '&amp;search_fk_shipping_method='.urlencode($search_fk_shipping_method);
	if ($search_import_key != '') $params.= '&amp;search_import_key='.urlencode($search_import_key);
	if ($search_extraparams != '') $params.= '&amp;search_extraparams='.urlencode($search_extraparams);
	if ($search_status != 9) $params.= '&amp;search_status='.urlencode($search_status);
	//if ($search_status_process != '') $params.= '&amp;search_status_process='.urlencode($search_status_process);
	if ($search_status_process != 9) $params.= '&amp;search_status_process='.urlencode($search_status_process);


	if ($optioncss != '') $param.='&optioncss='.$optioncss;
	// Add $param from extra fields
	foreach ($search_array_options as $key => $val)
	{
		$crit=$val;
		$tmpkey=preg_replace('/search_options_/','',$key);
		if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
	}



	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
	if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

	if ($sall)
	{
		foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
		print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
	}

	$moreforfilter = '';
	//$moreforfilter.='<div class="divsearchfield">';
	//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
	//$moreforfilter.= '</div>';

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
	//
	if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.date_delivery']['checked'])) print_liste_field_titre($arrayfields['t.date_delivery']['label'],$_SERVER['PHP_SELF'],'t.date_delivery','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.datec']['checked'])) print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER['PHP_SELF'],'t.datec','',$params,'',$sortfield,$sortorder);
	if ($conf->poa->enabled)
	{
		if (! empty($arrayfields['p.nro_preventive']['checked'])) print_liste_field_titre($arrayfields['p.nro_preventive']['label'],$_SERVER['PHP_SELF'],'p.nro_preventive','',$params,'',$sortfield,$sortorder);
		if (! empty($arrayfields['p.gestion']['checked'])) print_liste_field_titre($arrayfields['p.gestion']['label'],$_SERVER['PHP_SELF'],'p.gestion','',$params,'',$sortfield,$sortorder);
	}
	if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.ref_int']['checked'])) print_liste_field_titre($arrayfields['t.ref_int']['label'],$_SERVER['PHP_SELF'],'t.ref_int','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_departament']['checked'])) print_liste_field_titre($arrayfields['t.fk_departament']['label'],$_SERVER['PHP_SELF'],'t.fk_departament','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_author']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'t.fk_user_author','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_modif']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_modif']['label'],$_SERVER['PHP_SELF'],'t.fk_user_modif','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_valid']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_valid']['label'],$_SERVER['PHP_SELF'],'t.fk_user_valid','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_cloture']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_cloture']['label'],$_SERVER['PHP_SELF'],'t.fk_user_cloture','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.note_private']['checked'])) print_liste_field_titre($arrayfields['t.note_private']['label'],$_SERVER['PHP_SELF'],'t.note_private','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.note_public']['checked'])) print_liste_field_titre($arrayfields['t.note_public']['label'],$_SERVER['PHP_SELF'],'t.note_public','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['linkped']['checked'])) print_liste_field_titre($arrayfields['linkped']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.model_pdf']['checked'])) print_liste_field_titre($arrayfields['t.model_pdf']['label'],$_SERVER['PHP_SELF'],'t.model_pdf','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_shipping_method']['checked'])) print_liste_field_titre($arrayfields['t.fk_shipping_method']['label'],$_SERVER['PHP_SELF'],'t.fk_shipping_method','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.import_key']['checked'])) print_liste_field_titre($arrayfields['t.import_key']['label'],$_SERVER['PHP_SELF'],'t.import_key','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.extraparams']['checked'])) print_liste_field_titre($arrayfields['t.extraparams']['label'],$_SERVER['PHP_SELF'],'t.extraparams','',$params,'',$sortfield,$sortorder);

	if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);

	if ($conf->global->PURCHASE_INTEGRATED_POA)
	{
		if (! empty($arrayfields['t.status_process']['checked'])) print_liste_field_titre($arrayfields['t.status_process']['label'],$_SERVER['PHP_SELF'],'t.status_process','',$params,'',$sortfield,$sortorder);
	}
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
	$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
	print '</tr>'."\n";

	// Fields title search
	print '<tr class="liste_titre">';
	//
	if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
	if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	if (! empty($arrayfields['t.date_delivery']['checked']))
	{
		print '<td class="liste_titre">';
		$form->select_date($search_date_delivery,'dd_',0,0,1);
		print '</td>';
	}
	if (! empty($arrayfields['t.datec']['checked']))
	{
		print '<td class="liste_titre">';
		$form->select_date($search_datec,'dc_',0,0,1);
		print '</td>';
	}
	if ($conf->poa->enabled)
	{
		if (! empty($arrayfields['p.nro_preventive']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_prev" value="'.$search_prev.'" size="4"></td>';
		if (! empty($arrayfields['p.gestion']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_gestion" value="'.$search_gestion.'" size="4"></td>';
	}
	if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
	if (! empty($arrayfields['t.ref_int']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_int" value="'.$search_ref_int.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_departament']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_departament" value="'.$search_fk_departament.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_author']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_author" value="'.$search_fk_user_author.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_modif']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_modif" value="'.$search_fk_user_modif.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_valid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_valid" value="'.$search_fk_user_valid.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_cloture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_cloture" value="'.$search_fk_user_cloture.'" size="10"></td>';
	if (! empty($arrayfields['t.note_private']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_private" value="'.$search_note_private.'" size="10"></td>';
	if (! empty($arrayfields['t.note_public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_public" value="'.$search_note_public.'" size="10"></td>';
	if (! empty($arrayfields['linkped']['checked'])) print '<td class="liste_titre"></td>';

	if (! empty($arrayfields['t.model_pdf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model_pdf" value="'.$search_model_pdf.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_shipping_method']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_shipping_method" value="'.$search_fk_shipping_method.'" size="10"></td>';
	if (! empty($arrayfields['t.import_key']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_import_key" value="'.$search_import_key.'" size="10"></td>';
	if (! empty($arrayfields['t.extraparams']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_extraparams" value="'.$search_extraparams.'" size="10"></td>';

	//status
	if (! empty($arrayfields['t.status']['checked'])) {

		//print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="5"></td>';
		print '<td class="liste_titre center">';

		print $form->selectarray('search_status', $aStatu, $search_status, 0, 0, 0, '', 0, 0, 0, '', 'maxwidth100');
		print '</td>';
	}
	if ($conf->global->PURCHASE_INTEGRATED_POA)
	{
		if (! empty($arrayfields['t.status_process']['checked'])){
		// print '<td class="liste_titre"><input type="text" class="flat" name="search_status_process" value="'.$search_status_process.'" size="5"></td>';

			print '<td class="liste_titre center">';
			print $form->selectarray('search_status_process', $aStatusprocess, $search_status_process, 0, 0, 0, '', 0, 0, 0, '', 'maxwidth100');
			print '</td>';
		}

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
	$searchpitco=$form->showFilterAndCheckAddButtons(0);
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
				//print_r($obj);
					//exit;


			$object->id = $obj->rowid;
			$object->ref = $obj->ref;
			$object->status = $obj->status;
			$object->status_process = $obj->status_process;
			$var = !$var;

			// Show here line of result
			print '<tr '.$bc[$var].'>';
			// LIST_OF_TD_FIELDS_LIST

			if (! empty($arrayfields['t.ref']['checked']))
			{
				print '<td>'.$object->getNomUrl().'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.date_delivery']['checked']))
			{
				print '<td>'.dol_print_date($db->jdate($obj->date_delivery),'dayhour').'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.datec']['checked']))
			{
				print '<td>'.dol_print_date($db->jdate($obj->datec),'day').'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if ($conf->poa->enabled)
			{
				if (! empty($arrayfields['p.nro_preventive']['checked']))
				{
					print '<td>'.$obj->nro_preventive.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['p.gestion']['checked']))
				{
					print '<td>'.$obj->gestion.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
			}
			if (! empty($arrayfields['t.ref_ext']['checked']))
			{
				print '<td>'.$obj->ref_ext.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.ref_int']['checked']))
			{
				print '<td>'.$obj->ref_int.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_projet']['checked']))
			{
				if ($conf->projet->enabled)
				{
					$objprojet = new Project($db);
					if ($obj->fk_projet)
					{
						$objprojet->fetch($obj->fk_projet);
						print '<td>'.$objprojet->getNomUrl(1).'</td>';
					}
					else
						print '<td>'.'</td>';
				}
				else
					print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}

			if (! empty($arrayfields['t.fk_departament']['checked']))
			{
				/*
				if ($conf->orgman->enabled)
				{
					$objDepartament = new Pdepartament($db);
					$objDepartament->fetch($obj->fk_departament);
					print '<td>'.$objDepartament->getNomUrl().'</td>';
				}
				else
					print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
				*/

				print '<td>'.$obj->refdepartament.'</td>';
				if (! $i) $totalarray['nbfield']++;

			}

			if (! empty($arrayfields['t.note_private']['checked']))
			{
				print '<td>'.$obj->note_private.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.note_public']['checked']))
			{
				print '<td>'.$obj->note_public.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['linkped']['checked']))
			{
				//revisamos de que solicitud viene
				$res = $getUtil->get_element_element($obj->rowid,'purchaserequest',$type='source');
				$htmlLines = '';
				if ($res > 0)
				{
					foreach ($getUtil->lines AS $j => $line)
					{
						$resp = $objFournisseurCommande->fetch($line->fk_target);
						if ($resp>0)
						{
							if (!empty($htmlLines)) $htmlLines.=' ';
							$htmlLines.= $objFournisseurCommande->getNomUrl();
						}
					}
					print '<td>'.$htmlLines.'</td>';
				}
				else
					print '<td></td>';
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
			// Date modification
			if (! empty($arrayfields['t.tms']['checked']))
			{
				print '<td align="center">';
				print dol_print_date($db->jdate($obj->date_update), 'dayhour');
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			// Status

			if (! empty($arrayfields['t.status']['checked']))
			{
				print '<td align="center">'.$object->getLibStatut(6).'</td>';
			}
			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				if ($obj->fk_poa_prev>0)
				{
					if (! empty($arrayfields['t.status_process']['checked']))
					{
						print '<td align="center">'.$object->getLibStatutprocess(3).'</td>';
					}
				}
				else
				{
					print '<td align="center">'.'</td>';
				}
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
