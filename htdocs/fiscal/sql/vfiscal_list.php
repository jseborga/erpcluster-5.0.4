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
 *   	\file       fiscal/vfiscal_list.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-20 16:05
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
dol_include_once('/fiscal/class/vfiscal.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_nfiscal=GETPOST('search_nfiscal','alpha');
$search_serie=GETPOST('search_serie','alpha');
$search_fk_dosing=GETPOST('search_fk_dosing','int');
$search_fk_facture=GETPOST('search_fk_facture','int');
$search_fk_cliepro=GETPOST('search_fk_cliepro','int');
$search_nit=GETPOST('search_nit','alpha');
$search_razsoc=GETPOST('search_razsoc','alpha');
$search_type_op=GETPOST('search_type_op','alpha');
$search_num_autoriz=GETPOST('search_num_autoriz','alpha');
$search_cod_control=GETPOST('search_cod_control','alpha');
$search_baseimp1=GETPOST('search_baseimp1','alpha');
$search_baseimp2=GETPOST('search_baseimp2','alpha');
$search_baseimp3=GETPOST('search_baseimp3','alpha');
$search_baseimp4=GETPOST('search_baseimp4','alpha');
$search_baseimp5=GETPOST('search_baseimp5','alpha');
$search_aliqimp1=GETPOST('search_aliqimp1','alpha');
$search_aliqimp2=GETPOST('search_aliqimp2','alpha');
$search_aliqimp3=GETPOST('search_aliqimp3','alpha');
$search_aliqimp4=GETPOST('search_aliqimp4','alpha');
$search_aliqimp5=GETPOST('search_aliqimp5','alpha');
$search_valimp1=GETPOST('search_valimp1','alpha');
$search_valimp2=GETPOST('search_valimp2','alpha');
$search_valimp3=GETPOST('search_valimp3','alpha');
$search_valimp4=GETPOST('search_valimp4','alpha');
$search_valimp5=GETPOST('search_valimp5','alpha');
$search_valret1=GETPOST('search_valret1','alpha');
$search_valret2=GETPOST('search_valret2','alpha');
$search_valret3=GETPOST('search_valret3','alpha');
$search_valret4=GETPOST('search_valret4','alpha');
$search_valret5=GETPOST('search_valret5','alpha');
$search_amount_payment=GETPOST('search_amount_payment','alpha');
$search_amount_balance=GETPOST('search_amount_balance','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_statut_print=GETPOST('search_statut_print','int');
$search_status=GETPOST('search_status','int');


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

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('vfiscallist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('fiscal');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Vfiscal($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.nfiscal'=>array('label'=>$langs->trans("Fieldnfiscal"), 'checked'=>1),
't.serie'=>array('label'=>$langs->trans("Fieldserie"), 'checked'=>1),
't.fk_dosing'=>array('label'=>$langs->trans("Fieldfk_dosing"), 'checked'=>1),
't.fk_facture'=>array('label'=>$langs->trans("Fieldfk_facture"), 'checked'=>1),
't.fk_cliepro'=>array('label'=>$langs->trans("Fieldfk_cliepro"), 'checked'=>1),
't.nit'=>array('label'=>$langs->trans("Fieldnit"), 'checked'=>1),
't.razsoc'=>array('label'=>$langs->trans("Fieldrazsoc"), 'checked'=>1),
't.type_op'=>array('label'=>$langs->trans("Fieldtype_op"), 'checked'=>1),
't.num_autoriz'=>array('label'=>$langs->trans("Fieldnum_autoriz"), 'checked'=>1),
't.cod_control'=>array('label'=>$langs->trans("Fieldcod_control"), 'checked'=>1),
't.baseimp1'=>array('label'=>$langs->trans("Fieldbaseimp1"), 'checked'=>1),
't.baseimp2'=>array('label'=>$langs->trans("Fieldbaseimp2"), 'checked'=>1),
't.baseimp3'=>array('label'=>$langs->trans("Fieldbaseimp3"), 'checked'=>1),
't.baseimp4'=>array('label'=>$langs->trans("Fieldbaseimp4"), 'checked'=>1),
't.baseimp5'=>array('label'=>$langs->trans("Fieldbaseimp5"), 'checked'=>1),
't.aliqimp1'=>array('label'=>$langs->trans("Fieldaliqimp1"), 'checked'=>1),
't.aliqimp2'=>array('label'=>$langs->trans("Fieldaliqimp2"), 'checked'=>1),
't.aliqimp3'=>array('label'=>$langs->trans("Fieldaliqimp3"), 'checked'=>1),
't.aliqimp4'=>array('label'=>$langs->trans("Fieldaliqimp4"), 'checked'=>1),
't.aliqimp5'=>array('label'=>$langs->trans("Fieldaliqimp5"), 'checked'=>1),
't.valimp1'=>array('label'=>$langs->trans("Fieldvalimp1"), 'checked'=>1),
't.valimp2'=>array('label'=>$langs->trans("Fieldvalimp2"), 'checked'=>1),
't.valimp3'=>array('label'=>$langs->trans("Fieldvalimp3"), 'checked'=>1),
't.valimp4'=>array('label'=>$langs->trans("Fieldvalimp4"), 'checked'=>1),
't.valimp5'=>array('label'=>$langs->trans("Fieldvalimp5"), 'checked'=>1),
't.valret1'=>array('label'=>$langs->trans("Fieldvalret1"), 'checked'=>1),
't.valret2'=>array('label'=>$langs->trans("Fieldvalret2"), 'checked'=>1),
't.valret3'=>array('label'=>$langs->trans("Fieldvalret3"), 'checked'=>1),
't.valret4'=>array('label'=>$langs->trans("Fieldvalret4"), 'checked'=>1),
't.valret5'=>array('label'=>$langs->trans("Fieldvalret5"), 'checked'=>1),
't.amount_payment'=>array('label'=>$langs->trans("Fieldamount_payment"), 'checked'=>1),
't.amount_balance'=>array('label'=>$langs->trans("Fieldamount_balance"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.statut_print'=>array('label'=>$langs->trans("Fieldstatut_print"), 'checked'=>1),
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
$search_nfiscal='';
$search_serie='';
$search_fk_dosing='';
$search_fk_facture='';
$search_fk_cliepro='';
$search_nit='';
$search_razsoc='';
$search_type_op='';
$search_num_autoriz='';
$search_cod_control='';
$search_baseimp1='';
$search_baseimp2='';
$search_baseimp3='';
$search_baseimp4='';
$search_baseimp5='';
$search_aliqimp1='';
$search_aliqimp2='';
$search_aliqimp3='';
$search_aliqimp4='';
$search_aliqimp5='';
$search_valimp1='';
$search_valimp2='';
$search_valimp3='';
$search_valimp4='';
$search_valimp5='';
$search_valret1='';
$search_valret2='';
$search_valret3='';
$search_valret4='';
$search_valret5='';
$search_amount_payment='';
$search_amount_balance='';
$search_fk_user_create='';
$search_statut_print='';
$search_status='';

	
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
			header("Location: ".dol_buildpath('/fiscal/list.php',1));
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

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('MyModuleListTitle');
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
		$sql .= " t.nfiscal,";
		$sql .= " t.serie,";
		$sql .= " t.fk_dosing,";
		$sql .= " t.fk_facture,";
		$sql .= " t.fk_cliepro,";
		$sql .= " t.nit,";
		$sql .= " t.razsoc,";
		$sql .= " t.date_exp,";
		$sql .= " t.type_op,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.baseimp1,";
		$sql .= " t.baseimp2,";
		$sql .= " t.baseimp3,";
		$sql .= " t.baseimp4,";
		$sql .= " t.baseimp5,";
		$sql .= " t.aliqimp1,";
		$sql .= " t.aliqimp2,";
		$sql .= " t.aliqimp3,";
		$sql .= " t.aliqimp4,";
		$sql .= " t.aliqimp5,";
		$sql .= " t.valimp1,";
		$sql .= " t.valimp2,";
		$sql .= " t.valimp3,";
		$sql .= " t.valimp4,";
		$sql .= " t.valimp5,";
		$sql .= " t.valret1,";
		$sql .= " t.valret2,";
		$sql .= " t.valret3,";
		$sql .= " t.valret4,";
		$sql .= " t.valret5,";
		$sql .= " t.amount_payment,";
		$sql .= " t.amount_balance,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.statut_print,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."v_fiscal_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_nfiscal) $sql.= natural_search("nfiscal",$search_nfiscal);
if ($search_serie) $sql.= natural_search("serie",$search_serie);
if ($search_fk_dosing) $sql.= natural_search("fk_dosing",$search_fk_dosing);
if ($search_fk_facture) $sql.= natural_search("fk_facture",$search_fk_facture);
if ($search_fk_cliepro) $sql.= natural_search("fk_cliepro",$search_fk_cliepro);
if ($search_nit) $sql.= natural_search("nit",$search_nit);
if ($search_razsoc) $sql.= natural_search("razsoc",$search_razsoc);
if ($search_type_op) $sql.= natural_search("type_op",$search_type_op);
if ($search_num_autoriz) $sql.= natural_search("num_autoriz",$search_num_autoriz);
if ($search_cod_control) $sql.= natural_search("cod_control",$search_cod_control);
if ($search_baseimp1) $sql.= natural_search("baseimp1",$search_baseimp1);
if ($search_baseimp2) $sql.= natural_search("baseimp2",$search_baseimp2);
if ($search_baseimp3) $sql.= natural_search("baseimp3",$search_baseimp3);
if ($search_baseimp4) $sql.= natural_search("baseimp4",$search_baseimp4);
if ($search_baseimp5) $sql.= natural_search("baseimp5",$search_baseimp5);
if ($search_aliqimp1) $sql.= natural_search("aliqimp1",$search_aliqimp1);
if ($search_aliqimp2) $sql.= natural_search("aliqimp2",$search_aliqimp2);
if ($search_aliqimp3) $sql.= natural_search("aliqimp3",$search_aliqimp3);
if ($search_aliqimp4) $sql.= natural_search("aliqimp4",$search_aliqimp4);
if ($search_aliqimp5) $sql.= natural_search("aliqimp5",$search_aliqimp5);
if ($search_valimp1) $sql.= natural_search("valimp1",$search_valimp1);
if ($search_valimp2) $sql.= natural_search("valimp2",$search_valimp2);
if ($search_valimp3) $sql.= natural_search("valimp3",$search_valimp3);
if ($search_valimp4) $sql.= natural_search("valimp4",$search_valimp4);
if ($search_valimp5) $sql.= natural_search("valimp5",$search_valimp5);
if ($search_valret1) $sql.= natural_search("valret1",$search_valret1);
if ($search_valret2) $sql.= natural_search("valret2",$search_valret2);
if ($search_valret3) $sql.= natural_search("valret3",$search_valret3);
if ($search_valret4) $sql.= natural_search("valret4",$search_valret4);
if ($search_valret5) $sql.= natural_search("valret5",$search_valret5);
if ($search_amount_payment) $sql.= natural_search("amount_payment",$search_amount_payment);
if ($search_amount_balance) $sql.= natural_search("amount_balance",$search_amount_balance);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_statut_print) $sql.= natural_search("statut_print",$search_statut_print);
if ($search_status) $sql.= natural_search("status",$search_status);


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


dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
    
if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
if ($search_nfiscal != '') $params.= '&amp;search_nfiscal='.urlencode($search_nfiscal);
if ($search_serie != '') $params.= '&amp;search_serie='.urlencode($search_serie);
if ($search_fk_dosing != '') $params.= '&amp;search_fk_dosing='.urlencode($search_fk_dosing);
if ($search_fk_facture != '') $params.= '&amp;search_fk_facture='.urlencode($search_fk_facture);
if ($search_fk_cliepro != '') $params.= '&amp;search_fk_cliepro='.urlencode($search_fk_cliepro);
if ($search_nit != '') $params.= '&amp;search_nit='.urlencode($search_nit);
if ($search_razsoc != '') $params.= '&amp;search_razsoc='.urlencode($search_razsoc);
if ($search_type_op != '') $params.= '&amp;search_type_op='.urlencode($search_type_op);
if ($search_num_autoriz != '') $params.= '&amp;search_num_autoriz='.urlencode($search_num_autoriz);
if ($search_cod_control != '') $params.= '&amp;search_cod_control='.urlencode($search_cod_control);
if ($search_baseimp1 != '') $params.= '&amp;search_baseimp1='.urlencode($search_baseimp1);
if ($search_baseimp2 != '') $params.= '&amp;search_baseimp2='.urlencode($search_baseimp2);
if ($search_baseimp3 != '') $params.= '&amp;search_baseimp3='.urlencode($search_baseimp3);
if ($search_baseimp4 != '') $params.= '&amp;search_baseimp4='.urlencode($search_baseimp4);
if ($search_baseimp5 != '') $params.= '&amp;search_baseimp5='.urlencode($search_baseimp5);
if ($search_aliqimp1 != '') $params.= '&amp;search_aliqimp1='.urlencode($search_aliqimp1);
if ($search_aliqimp2 != '') $params.= '&amp;search_aliqimp2='.urlencode($search_aliqimp2);
if ($search_aliqimp3 != '') $params.= '&amp;search_aliqimp3='.urlencode($search_aliqimp3);
if ($search_aliqimp4 != '') $params.= '&amp;search_aliqimp4='.urlencode($search_aliqimp4);
if ($search_aliqimp5 != '') $params.= '&amp;search_aliqimp5='.urlencode($search_aliqimp5);
if ($search_valimp1 != '') $params.= '&amp;search_valimp1='.urlencode($search_valimp1);
if ($search_valimp2 != '') $params.= '&amp;search_valimp2='.urlencode($search_valimp2);
if ($search_valimp3 != '') $params.= '&amp;search_valimp3='.urlencode($search_valimp3);
if ($search_valimp4 != '') $params.= '&amp;search_valimp4='.urlencode($search_valimp4);
if ($search_valimp5 != '') $params.= '&amp;search_valimp5='.urlencode($search_valimp5);
if ($search_valret1 != '') $params.= '&amp;search_valret1='.urlencode($search_valret1);
if ($search_valret2 != '') $params.= '&amp;search_valret2='.urlencode($search_valret2);
if ($search_valret3 != '') $params.= '&amp;search_valret3='.urlencode($search_valret3);
if ($search_valret4 != '') $params.= '&amp;search_valret4='.urlencode($search_valret4);
if ($search_valret5 != '') $params.= '&amp;search_valret5='.urlencode($search_valret5);
if ($search_amount_payment != '') $params.= '&amp;search_amount_payment='.urlencode($search_amount_payment);
if ($search_amount_balance != '') $params.= '&amp;search_amount_balance='.urlencode($search_amount_balance);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_statut_print != '') $params.= '&amp;search_statut_print='.urlencode($search_statut_print);
if ($search_status != '') $params.= '&amp;search_status='.urlencode($search_status);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 

    print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);


	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	
    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
    }
    
    $moreforfilter = '';
    $moreforfilter.='<div class="divsearchfield">';
    $moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
    $moreforfilter.= '</div>';
    
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
if (! empty($arrayfields['t.nfiscal']['checked'])) print_liste_field_titre($arrayfields['t.nfiscal']['label'],$_SERVER['PHP_SELF'],'t.nfiscal','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.serie']['checked'])) print_liste_field_titre($arrayfields['t.serie']['label'],$_SERVER['PHP_SELF'],'t.serie','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_dosing']['checked'])) print_liste_field_titre($arrayfields['t.fk_dosing']['label'],$_SERVER['PHP_SELF'],'t.fk_dosing','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_facture']['checked'])) print_liste_field_titre($arrayfields['t.fk_facture']['label'],$_SERVER['PHP_SELF'],'t.fk_facture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_cliepro']['checked'])) print_liste_field_titre($arrayfields['t.fk_cliepro']['label'],$_SERVER['PHP_SELF'],'t.fk_cliepro','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nit']['checked'])) print_liste_field_titre($arrayfields['t.nit']['label'],$_SERVER['PHP_SELF'],'t.nit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.razsoc']['checked'])) print_liste_field_titre($arrayfields['t.razsoc']['label'],$_SERVER['PHP_SELF'],'t.razsoc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_op']['checked'])) print_liste_field_titre($arrayfields['t.type_op']['label'],$_SERVER['PHP_SELF'],'t.type_op','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_autoriz']['checked'])) print_liste_field_titre($arrayfields['t.num_autoriz']['label'],$_SERVER['PHP_SELF'],'t.num_autoriz','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cod_control']['checked'])) print_liste_field_titre($arrayfields['t.cod_control']['label'],$_SERVER['PHP_SELF'],'t.cod_control','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.baseimp1']['checked'])) print_liste_field_titre($arrayfields['t.baseimp1']['label'],$_SERVER['PHP_SELF'],'t.baseimp1','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.baseimp2']['checked'])) print_liste_field_titre($arrayfields['t.baseimp2']['label'],$_SERVER['PHP_SELF'],'t.baseimp2','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.baseimp3']['checked'])) print_liste_field_titre($arrayfields['t.baseimp3']['label'],$_SERVER['PHP_SELF'],'t.baseimp3','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.baseimp4']['checked'])) print_liste_field_titre($arrayfields['t.baseimp4']['label'],$_SERVER['PHP_SELF'],'t.baseimp4','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.baseimp5']['checked'])) print_liste_field_titre($arrayfields['t.baseimp5']['label'],$_SERVER['PHP_SELF'],'t.baseimp5','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.aliqimp1']['checked'])) print_liste_field_titre($arrayfields['t.aliqimp1']['label'],$_SERVER['PHP_SELF'],'t.aliqimp1','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.aliqimp2']['checked'])) print_liste_field_titre($arrayfields['t.aliqimp2']['label'],$_SERVER['PHP_SELF'],'t.aliqimp2','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.aliqimp3']['checked'])) print_liste_field_titre($arrayfields['t.aliqimp3']['label'],$_SERVER['PHP_SELF'],'t.aliqimp3','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.aliqimp4']['checked'])) print_liste_field_titre($arrayfields['t.aliqimp4']['label'],$_SERVER['PHP_SELF'],'t.aliqimp4','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.aliqimp5']['checked'])) print_liste_field_titre($arrayfields['t.aliqimp5']['label'],$_SERVER['PHP_SELF'],'t.aliqimp5','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valimp1']['checked'])) print_liste_field_titre($arrayfields['t.valimp1']['label'],$_SERVER['PHP_SELF'],'t.valimp1','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valimp2']['checked'])) print_liste_field_titre($arrayfields['t.valimp2']['label'],$_SERVER['PHP_SELF'],'t.valimp2','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valimp3']['checked'])) print_liste_field_titre($arrayfields['t.valimp3']['label'],$_SERVER['PHP_SELF'],'t.valimp3','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valimp4']['checked'])) print_liste_field_titre($arrayfields['t.valimp4']['label'],$_SERVER['PHP_SELF'],'t.valimp4','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valimp5']['checked'])) print_liste_field_titre($arrayfields['t.valimp5']['label'],$_SERVER['PHP_SELF'],'t.valimp5','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valret1']['checked'])) print_liste_field_titre($arrayfields['t.valret1']['label'],$_SERVER['PHP_SELF'],'t.valret1','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valret2']['checked'])) print_liste_field_titre($arrayfields['t.valret2']['label'],$_SERVER['PHP_SELF'],'t.valret2','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valret3']['checked'])) print_liste_field_titre($arrayfields['t.valret3']['label'],$_SERVER['PHP_SELF'],'t.valret3','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valret4']['checked'])) print_liste_field_titre($arrayfields['t.valret4']['label'],$_SERVER['PHP_SELF'],'t.valret4','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.valret5']['checked'])) print_liste_field_titre($arrayfields['t.valret5']['label'],$_SERVER['PHP_SELF'],'t.valret5','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_payment']['checked'])) print_liste_field_titre($arrayfields['t.amount_payment']['label'],$_SERVER['PHP_SELF'],'t.amount_payment','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_balance']['checked'])) print_liste_field_titre($arrayfields['t.amount_balance']['label'],$_SERVER['PHP_SELF'],'t.amount_balance','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut_print']['checked'])) print_liste_field_titre($arrayfields['t.statut_print']['label'],$_SERVER['PHP_SELF'],'t.statut_print','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);

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
	if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
    print '</tr>'."\n";

    // Fields title search
	print '<tr class="liste_titre">';
	// 
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.nfiscal']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nfiscal" value="'.$search_nfiscal.'" size="10"></td>';
if (! empty($arrayfields['t.serie']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_serie" value="'.$search_serie.'" size="10"></td>';
if (! empty($arrayfields['t.fk_dosing']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_dosing" value="'.$search_fk_dosing.'" size="10"></td>';
if (! empty($arrayfields['t.fk_facture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_facture" value="'.$search_fk_facture.'" size="10"></td>';
if (! empty($arrayfields['t.fk_cliepro']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_cliepro" value="'.$search_fk_cliepro.'" size="10"></td>';
if (! empty($arrayfields['t.nit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nit" value="'.$search_nit.'" size="10"></td>';
if (! empty($arrayfields['t.razsoc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_razsoc" value="'.$search_razsoc.'" size="10"></td>';
if (! empty($arrayfields['t.type_op']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_op" value="'.$search_type_op.'" size="10"></td>';
if (! empty($arrayfields['t.num_autoriz']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_autoriz" value="'.$search_num_autoriz.'" size="10"></td>';
if (! empty($arrayfields['t.cod_control']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cod_control" value="'.$search_cod_control.'" size="10"></td>';
if (! empty($arrayfields['t.baseimp1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_baseimp1" value="'.$search_baseimp1.'" size="10"></td>';
if (! empty($arrayfields['t.baseimp2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_baseimp2" value="'.$search_baseimp2.'" size="10"></td>';
if (! empty($arrayfields['t.baseimp3']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_baseimp3" value="'.$search_baseimp3.'" size="10"></td>';
if (! empty($arrayfields['t.baseimp4']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_baseimp4" value="'.$search_baseimp4.'" size="10"></td>';
if (! empty($arrayfields['t.baseimp5']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_baseimp5" value="'.$search_baseimp5.'" size="10"></td>';
if (! empty($arrayfields['t.aliqimp1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_aliqimp1" value="'.$search_aliqimp1.'" size="10"></td>';
if (! empty($arrayfields['t.aliqimp2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_aliqimp2" value="'.$search_aliqimp2.'" size="10"></td>';
if (! empty($arrayfields['t.aliqimp3']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_aliqimp3" value="'.$search_aliqimp3.'" size="10"></td>';
if (! empty($arrayfields['t.aliqimp4']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_aliqimp4" value="'.$search_aliqimp4.'" size="10"></td>';
if (! empty($arrayfields['t.aliqimp5']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_aliqimp5" value="'.$search_aliqimp5.'" size="10"></td>';
if (! empty($arrayfields['t.valimp1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valimp1" value="'.$search_valimp1.'" size="10"></td>';
if (! empty($arrayfields['t.valimp2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valimp2" value="'.$search_valimp2.'" size="10"></td>';
if (! empty($arrayfields['t.valimp3']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valimp3" value="'.$search_valimp3.'" size="10"></td>';
if (! empty($arrayfields['t.valimp4']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valimp4" value="'.$search_valimp4.'" size="10"></td>';
if (! empty($arrayfields['t.valimp5']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valimp5" value="'.$search_valimp5.'" size="10"></td>';
if (! empty($arrayfields['t.valret1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valret1" value="'.$search_valret1.'" size="10"></td>';
if (! empty($arrayfields['t.valret2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valret2" value="'.$search_valret2.'" size="10"></td>';
if (! empty($arrayfields['t.valret3']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valret3" value="'.$search_valret3.'" size="10"></td>';
if (! empty($arrayfields['t.valret4']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valret4" value="'.$search_valret4.'" size="10"></td>';
if (! empty($arrayfields['t.valret5']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_valret5" value="'.$search_valret5.'" size="10"></td>';
if (! empty($arrayfields['t.amount_payment']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_payment" value="'.$search_amount_payment.'" size="10"></td>';
if (! empty($arrayfields['t.amount_balance']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_balance" value="'.$search_amount_balance.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.statut_print']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut_print" value="'.$search_statut_print.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';

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
