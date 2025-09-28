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
 *   	\file       sales/bankstatus_list.php
 *		\ingroup    sales
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-22 21:57
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
dol_include_once('/sales/class/bankstatus.class.php');

// Load traductions files requiredby by page
$langs->load("sales");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_bank=GETPOST('search_fk_bank','int');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_subsidiary=GETPOST('search_fk_subsidiary','int');
$search_fk_bank_historial=GETPOST('search_fk_bank_historial','int');
$search_exchange=GETPOST('search_exchange','alpha');
$search_previus_balance=GETPOST('search_previus_balance','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_text_amount=GETPOST('search_text_amount','alpha');
$search_amount_open=GETPOST('search_amount_open','alpha');
$search_text_amount_open=GETPOST('search_text_amount_open','alpha');
$search_amount_balance=GETPOST('search_amount_balance','alpha');
$search_amount_income=GETPOST('search_amount_income','alpha');
$search_amount_input=GETPOST('search_amount_input','alpha');
$search_amount_sale=GETPOST('search_amount_sale','alpha');
$search_amount_null=GETPOST('search_amount_null','alpha');
$search_amount_advance=GETPOST('search_amount_advance','alpha');
$search_amount_transf_input=GETPOST('search_amount_transf_input','alpha');
$search_amount_transf_output=GETPOST('search_amount_transf_output','alpha');
$search_amount_spending=GETPOST('search_amount_spending','alpha');
$search_amount_expense=GETPOST('search_amount_expense','alpha');
$search_amount_close=GETPOST('search_amount_close','alpha');
$search_missing_money=GETPOST('search_missing_money','alpha');
$search_leftover_money=GETPOST('search_leftover_money','alpha');
$search_amount_exchange=GETPOST('search_amount_exchange','alpha');
$search_invoice_annulled=GETPOST('search_invoice_annulled','alpha');
$search_text_exchange=GETPOST('search_text_exchange','alpha');
$search_text_close=GETPOST('search_text_close','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_var_detail=GETPOST('search_var_detail','alpha');
$search_typecash=GETPOST('search_typecash','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_close=GETPOST('search_fk_user_close','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
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

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('bankstatuslist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('sales');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Bankstatus($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_bank'=>array('label'=>$langs->trans("Fieldfk_bank"), 'checked'=>1),
't.fk_user'=>array('label'=>$langs->trans("Fieldfk_user"), 'checked'=>1),
't.fk_subsidiary'=>array('label'=>$langs->trans("Fieldfk_subsidiary"), 'checked'=>1),
't.fk_bank_historial'=>array('label'=>$langs->trans("Fieldfk_bank_historial"), 'checked'=>1),
't.exchange'=>array('label'=>$langs->trans("Fieldexchange"), 'checked'=>1),
't.previus_balance'=>array('label'=>$langs->trans("Fieldprevius_balance"), 'checked'=>1),
't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
't.text_amount'=>array('label'=>$langs->trans("Fieldtext_amount"), 'checked'=>1),
't.amount_open'=>array('label'=>$langs->trans("Fieldamount_open"), 'checked'=>1),
't.text_amount_open'=>array('label'=>$langs->trans("Fieldtext_amount_open"), 'checked'=>1),
't.amount_balance'=>array('label'=>$langs->trans("Fieldamount_balance"), 'checked'=>1),
't.amount_income'=>array('label'=>$langs->trans("Fieldamount_income"), 'checked'=>1),
't.amount_input'=>array('label'=>$langs->trans("Fieldamount_input"), 'checked'=>1),
't.amount_sale'=>array('label'=>$langs->trans("Fieldamount_sale"), 'checked'=>1),
't.amount_null'=>array('label'=>$langs->trans("Fieldamount_null"), 'checked'=>1),
't.amount_advance'=>array('label'=>$langs->trans("Fieldamount_advance"), 'checked'=>1),
't.amount_transf_input'=>array('label'=>$langs->trans("Fieldamount_transf_input"), 'checked'=>1),
't.amount_transf_output'=>array('label'=>$langs->trans("Fieldamount_transf_output"), 'checked'=>1),
't.amount_spending'=>array('label'=>$langs->trans("Fieldamount_spending"), 'checked'=>1),
't.amount_expense'=>array('label'=>$langs->trans("Fieldamount_expense"), 'checked'=>1),
't.amount_close'=>array('label'=>$langs->trans("Fieldamount_close"), 'checked'=>1),
't.missing_money'=>array('label'=>$langs->trans("Fieldmissing_money"), 'checked'=>1),
't.leftover_money'=>array('label'=>$langs->trans("Fieldleftover_money"), 'checked'=>1),
't.amount_exchange'=>array('label'=>$langs->trans("Fieldamount_exchange"), 'checked'=>1),
't.invoice_annulled'=>array('label'=>$langs->trans("Fieldinvoice_annulled"), 'checked'=>1),
't.text_exchange'=>array('label'=>$langs->trans("Fieldtext_exchange"), 'checked'=>1),
't.text_close'=>array('label'=>$langs->trans("Fieldtext_close"), 'checked'=>1),
't.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
't.var_detail'=>array('label'=>$langs->trans("Fieldvar_detail"), 'checked'=>1),
't.typecash'=>array('label'=>$langs->trans("Fieldtypecash"), 'checked'=>1),
't.model_pdf'=>array('label'=>$langs->trans("Fieldmodel_pdf"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_close'=>array('label'=>$langs->trans("Fieldfk_user_close"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
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
	
$search_fk_bank='';
$search_fk_user='';
$search_fk_subsidiary='';
$search_fk_bank_historial='';
$search_exchange='';
$search_previus_balance='';
$search_amount='';
$search_text_amount='';
$search_amount_open='';
$search_text_amount_open='';
$search_amount_balance='';
$search_amount_income='';
$search_amount_input='';
$search_amount_sale='';
$search_amount_null='';
$search_amount_advance='';
$search_amount_transf_input='';
$search_amount_transf_output='';
$search_amount_spending='';
$search_amount_expense='';
$search_amount_close='';
$search_missing_money='';
$search_leftover_money='';
$search_amount_exchange='';
$search_invoice_annulled='';
$search_text_exchange='';
$search_text_close='';
$search_detail='';
$search_var_detail='';
$search_typecash='';
$search_model_pdf='';
$search_fk_user_create='';
$search_fk_user_close='';
$search_fk_user_mod='';
$search_statut='';

	
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
			header("Location: ".dol_buildpath('/sales/list.php',1));
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

		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_subsidiary,";
		$sql .= " t.fk_bank_historial,";
		$sql .= " t.date_register,";
		$sql .= " t.date_close,";
		$sql .= " t.exchange,";
		$sql .= " t.previus_balance,";
		$sql .= " t.amount,";
		$sql .= " t.text_amount,";
		$sql .= " t.amount_open,";
		$sql .= " t.text_amount_open,";
		$sql .= " t.amount_balance,";
		$sql .= " t.amount_income,";
		$sql .= " t.amount_input,";
		$sql .= " t.amount_sale,";
		$sql .= " t.amount_null,";
		$sql .= " t.amount_advance,";
		$sql .= " t.amount_transf_input,";
		$sql .= " t.amount_transf_output,";
		$sql .= " t.amount_spending,";
		$sql .= " t.amount_expense,";
		$sql .= " t.amount_close,";
		$sql .= " t.missing_money,";
		$sql .= " t.leftover_money,";
		$sql .= " t.amount_exchange,";
		$sql .= " t.invoice_annulled,";
		$sql .= " t.text_exchange,";
		$sql .= " t.text_close,";
		$sql .= " t.detail,";
		$sql .= " t.var_detail,";
		$sql .= " t.typecash,";
		$sql .= " t.model_pdf,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.statut";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."bank_status as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_status_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_bank) $sql.= natural_search("fk_bank",$search_fk_bank);
if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_fk_subsidiary) $sql.= natural_search("fk_subsidiary",$search_fk_subsidiary);
if ($search_fk_bank_historial) $sql.= natural_search("fk_bank_historial",$search_fk_bank_historial);
if ($search_exchange) $sql.= natural_search("exchange",$search_exchange);
if ($search_previus_balance) $sql.= natural_search("previus_balance",$search_previus_balance);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_text_amount) $sql.= natural_search("text_amount",$search_text_amount);
if ($search_amount_open) $sql.= natural_search("amount_open",$search_amount_open);
if ($search_text_amount_open) $sql.= natural_search("text_amount_open",$search_text_amount_open);
if ($search_amount_balance) $sql.= natural_search("amount_balance",$search_amount_balance);
if ($search_amount_income) $sql.= natural_search("amount_income",$search_amount_income);
if ($search_amount_input) $sql.= natural_search("amount_input",$search_amount_input);
if ($search_amount_sale) $sql.= natural_search("amount_sale",$search_amount_sale);
if ($search_amount_null) $sql.= natural_search("amount_null",$search_amount_null);
if ($search_amount_advance) $sql.= natural_search("amount_advance",$search_amount_advance);
if ($search_amount_transf_input) $sql.= natural_search("amount_transf_input",$search_amount_transf_input);
if ($search_amount_transf_output) $sql.= natural_search("amount_transf_output",$search_amount_transf_output);
if ($search_amount_spending) $sql.= natural_search("amount_spending",$search_amount_spending);
if ($search_amount_expense) $sql.= natural_search("amount_expense",$search_amount_expense);
if ($search_amount_close) $sql.= natural_search("amount_close",$search_amount_close);
if ($search_missing_money) $sql.= natural_search("missing_money",$search_missing_money);
if ($search_leftover_money) $sql.= natural_search("leftover_money",$search_leftover_money);
if ($search_amount_exchange) $sql.= natural_search("amount_exchange",$search_amount_exchange);
if ($search_invoice_annulled) $sql.= natural_search("invoice_annulled",$search_invoice_annulled);
if ($search_text_exchange) $sql.= natural_search("text_exchange",$search_text_exchange);
if ($search_text_close) $sql.= natural_search("text_close",$search_text_close);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_var_detail) $sql.= natural_search("var_detail",$search_var_detail);
if ($search_typecash) $sql.= natural_search("typecash",$search_typecash);
if ($search_model_pdf) $sql.= natural_search("model_pdf",$search_model_pdf);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_close) $sql.= natural_search("fk_user_close",$search_fk_user_close);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
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
    
if ($search_fk_bank != '') $params.= '&amp;search_fk_bank='.urlencode($search_fk_bank);
if ($search_fk_user != '') $params.= '&amp;search_fk_user='.urlencode($search_fk_user);
if ($search_fk_subsidiary != '') $params.= '&amp;search_fk_subsidiary='.urlencode($search_fk_subsidiary);
if ($search_fk_bank_historial != '') $params.= '&amp;search_fk_bank_historial='.urlencode($search_fk_bank_historial);
if ($search_exchange != '') $params.= '&amp;search_exchange='.urlencode($search_exchange);
if ($search_previus_balance != '') $params.= '&amp;search_previus_balance='.urlencode($search_previus_balance);
if ($search_amount != '') $params.= '&amp;search_amount='.urlencode($search_amount);
if ($search_text_amount != '') $params.= '&amp;search_text_amount='.urlencode($search_text_amount);
if ($search_amount_open != '') $params.= '&amp;search_amount_open='.urlencode($search_amount_open);
if ($search_text_amount_open != '') $params.= '&amp;search_text_amount_open='.urlencode($search_text_amount_open);
if ($search_amount_balance != '') $params.= '&amp;search_amount_balance='.urlencode($search_amount_balance);
if ($search_amount_income != '') $params.= '&amp;search_amount_income='.urlencode($search_amount_income);
if ($search_amount_input != '') $params.= '&amp;search_amount_input='.urlencode($search_amount_input);
if ($search_amount_sale != '') $params.= '&amp;search_amount_sale='.urlencode($search_amount_sale);
if ($search_amount_null != '') $params.= '&amp;search_amount_null='.urlencode($search_amount_null);
if ($search_amount_advance != '') $params.= '&amp;search_amount_advance='.urlencode($search_amount_advance);
if ($search_amount_transf_input != '') $params.= '&amp;search_amount_transf_input='.urlencode($search_amount_transf_input);
if ($search_amount_transf_output != '') $params.= '&amp;search_amount_transf_output='.urlencode($search_amount_transf_output);
if ($search_amount_spending != '') $params.= '&amp;search_amount_spending='.urlencode($search_amount_spending);
if ($search_amount_expense != '') $params.= '&amp;search_amount_expense='.urlencode($search_amount_expense);
if ($search_amount_close != '') $params.= '&amp;search_amount_close='.urlencode($search_amount_close);
if ($search_missing_money != '') $params.= '&amp;search_missing_money='.urlencode($search_missing_money);
if ($search_leftover_money != '') $params.= '&amp;search_leftover_money='.urlencode($search_leftover_money);
if ($search_amount_exchange != '') $params.= '&amp;search_amount_exchange='.urlencode($search_amount_exchange);
if ($search_invoice_annulled != '') $params.= '&amp;search_invoice_annulled='.urlencode($search_invoice_annulled);
if ($search_text_exchange != '') $params.= '&amp;search_text_exchange='.urlencode($search_text_exchange);
if ($search_text_close != '') $params.= '&amp;search_text_close='.urlencode($search_text_close);
if ($search_detail != '') $params.= '&amp;search_detail='.urlencode($search_detail);
if ($search_var_detail != '') $params.= '&amp;search_var_detail='.urlencode($search_var_detail);
if ($search_typecash != '') $params.= '&amp;search_typecash='.urlencode($search_typecash);
if ($search_model_pdf != '') $params.= '&amp;search_model_pdf='.urlencode($search_model_pdf);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_close != '') $params.= '&amp;search_fk_user_close='.urlencode($search_fk_user_close);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_statut != '') $params.= '&amp;search_statut='.urlencode($search_statut);

	
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
if (! empty($arrayfields['t.fk_bank']['checked'])) print_liste_field_titre($arrayfields['t.fk_bank']['label'],$_SERVER['PHP_SELF'],'t.fk_bank','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_subsidiary']['checked'])) print_liste_field_titre($arrayfields['t.fk_subsidiary']['label'],$_SERVER['PHP_SELF'],'t.fk_subsidiary','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_bank_historial']['checked'])) print_liste_field_titre($arrayfields['t.fk_bank_historial']['label'],$_SERVER['PHP_SELF'],'t.fk_bank_historial','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.exchange']['checked'])) print_liste_field_titre($arrayfields['t.exchange']['label'],$_SERVER['PHP_SELF'],'t.exchange','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.previus_balance']['checked'])) print_liste_field_titre($arrayfields['t.previus_balance']['label'],$_SERVER['PHP_SELF'],'t.previus_balance','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.text_amount']['checked'])) print_liste_field_titre($arrayfields['t.text_amount']['label'],$_SERVER['PHP_SELF'],'t.text_amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_open']['checked'])) print_liste_field_titre($arrayfields['t.amount_open']['label'],$_SERVER['PHP_SELF'],'t.amount_open','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.text_amount_open']['checked'])) print_liste_field_titre($arrayfields['t.text_amount_open']['label'],$_SERVER['PHP_SELF'],'t.text_amount_open','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_balance']['checked'])) print_liste_field_titre($arrayfields['t.amount_balance']['label'],$_SERVER['PHP_SELF'],'t.amount_balance','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_income']['checked'])) print_liste_field_titre($arrayfields['t.amount_income']['label'],$_SERVER['PHP_SELF'],'t.amount_income','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_input']['checked'])) print_liste_field_titre($arrayfields['t.amount_input']['label'],$_SERVER['PHP_SELF'],'t.amount_input','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_sale']['checked'])) print_liste_field_titre($arrayfields['t.amount_sale']['label'],$_SERVER['PHP_SELF'],'t.amount_sale','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_null']['checked'])) print_liste_field_titre($arrayfields['t.amount_null']['label'],$_SERVER['PHP_SELF'],'t.amount_null','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_advance']['checked'])) print_liste_field_titre($arrayfields['t.amount_advance']['label'],$_SERVER['PHP_SELF'],'t.amount_advance','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_transf_input']['checked'])) print_liste_field_titre($arrayfields['t.amount_transf_input']['label'],$_SERVER['PHP_SELF'],'t.amount_transf_input','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_transf_output']['checked'])) print_liste_field_titre($arrayfields['t.amount_transf_output']['label'],$_SERVER['PHP_SELF'],'t.amount_transf_output','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_spending']['checked'])) print_liste_field_titre($arrayfields['t.amount_spending']['label'],$_SERVER['PHP_SELF'],'t.amount_spending','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_expense']['checked'])) print_liste_field_titre($arrayfields['t.amount_expense']['label'],$_SERVER['PHP_SELF'],'t.amount_expense','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_close']['checked'])) print_liste_field_titre($arrayfields['t.amount_close']['label'],$_SERVER['PHP_SELF'],'t.amount_close','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.missing_money']['checked'])) print_liste_field_titre($arrayfields['t.missing_money']['label'],$_SERVER['PHP_SELF'],'t.missing_money','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.leftover_money']['checked'])) print_liste_field_titre($arrayfields['t.leftover_money']['label'],$_SERVER['PHP_SELF'],'t.leftover_money','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_exchange']['checked'])) print_liste_field_titre($arrayfields['t.amount_exchange']['label'],$_SERVER['PHP_SELF'],'t.amount_exchange','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.invoice_annulled']['checked'])) print_liste_field_titre($arrayfields['t.invoice_annulled']['label'],$_SERVER['PHP_SELF'],'t.invoice_annulled','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.text_exchange']['checked'])) print_liste_field_titre($arrayfields['t.text_exchange']['label'],$_SERVER['PHP_SELF'],'t.text_exchange','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.text_close']['checked'])) print_liste_field_titre($arrayfields['t.text_close']['label'],$_SERVER['PHP_SELF'],'t.text_close','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.var_detail']['checked'])) print_liste_field_titre($arrayfields['t.var_detail']['label'],$_SERVER['PHP_SELF'],'t.var_detail','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.typecash']['checked'])) print_liste_field_titre($arrayfields['t.typecash']['label'],$_SERVER['PHP_SELF'],'t.typecash','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model_pdf']['checked'])) print_liste_field_titre($arrayfields['t.model_pdf']['label'],$_SERVER['PHP_SELF'],'t.model_pdf','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_close']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_close']['label'],$_SERVER['PHP_SELF'],'t.fk_user_close','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_bank']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_bank" value="'.$search_fk_bank.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
if (! empty($arrayfields['t.fk_subsidiary']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_subsidiary" value="'.$search_fk_subsidiary.'" size="10"></td>';
if (! empty($arrayfields['t.fk_bank_historial']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_bank_historial" value="'.$search_fk_bank_historial.'" size="10"></td>';
if (! empty($arrayfields['t.exchange']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_exchange" value="'.$search_exchange.'" size="10"></td>';
if (! empty($arrayfields['t.previus_balance']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_previus_balance" value="'.$search_previus_balance.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.text_amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_text_amount" value="'.$search_text_amount.'" size="10"></td>';
if (! empty($arrayfields['t.amount_open']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_open" value="'.$search_amount_open.'" size="10"></td>';
if (! empty($arrayfields['t.text_amount_open']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_text_amount_open" value="'.$search_text_amount_open.'" size="10"></td>';
if (! empty($arrayfields['t.amount_balance']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_balance" value="'.$search_amount_balance.'" size="10"></td>';
if (! empty($arrayfields['t.amount_income']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_income" value="'.$search_amount_income.'" size="10"></td>';
if (! empty($arrayfields['t.amount_input']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_input" value="'.$search_amount_input.'" size="10"></td>';
if (! empty($arrayfields['t.amount_sale']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_sale" value="'.$search_amount_sale.'" size="10"></td>';
if (! empty($arrayfields['t.amount_null']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_null" value="'.$search_amount_null.'" size="10"></td>';
if (! empty($arrayfields['t.amount_advance']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_advance" value="'.$search_amount_advance.'" size="10"></td>';
if (! empty($arrayfields['t.amount_transf_input']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_transf_input" value="'.$search_amount_transf_input.'" size="10"></td>';
if (! empty($arrayfields['t.amount_transf_output']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_transf_output" value="'.$search_amount_transf_output.'" size="10"></td>';
if (! empty($arrayfields['t.amount_spending']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_spending" value="'.$search_amount_spending.'" size="10"></td>';
if (! empty($arrayfields['t.amount_expense']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_expense" value="'.$search_amount_expense.'" size="10"></td>';
if (! empty($arrayfields['t.amount_close']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_close" value="'.$search_amount_close.'" size="10"></td>';
if (! empty($arrayfields['t.missing_money']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_missing_money" value="'.$search_missing_money.'" size="10"></td>';
if (! empty($arrayfields['t.leftover_money']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_leftover_money" value="'.$search_leftover_money.'" size="10"></td>';
if (! empty($arrayfields['t.amount_exchange']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_exchange" value="'.$search_amount_exchange.'" size="10"></td>';
if (! empty($arrayfields['t.invoice_annulled']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_invoice_annulled" value="'.$search_invoice_annulled.'" size="10"></td>';
if (! empty($arrayfields['t.text_exchange']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_text_exchange" value="'.$search_text_exchange.'" size="10"></td>';
if (! empty($arrayfields['t.text_close']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_text_close" value="'.$search_text_close.'" size="10"></td>';
if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['t.var_detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_var_detail" value="'.$search_var_detail.'" size="10"></td>';
if (! empty($arrayfields['t.typecash']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_typecash" value="'.$search_typecash.'" size="10"></td>';
if (! empty($arrayfields['t.model_pdf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model_pdf" value="'.$search_model_pdf.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_close']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_close" value="'.$search_fk_user_close.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
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
