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
 *   	\file       finint/requestcashdeplacement_list.php
 *		\ingroup    finint
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-09 14:44
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
dol_include_once('/finint/class/requestcashdeplacement.class.php');

// Load traductions files requiredby by page
$langs->load("finint");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_request_cash=GETPOST('search_fk_request_cash','int');
$search_fk_request_cash_dest=GETPOST('search_fk_request_cash_dest','int');
$search_fk_projet_dest=GETPOST('search_fk_projet_dest','int');
$search_fk_projet_task_dest=GETPOST('search_fk_projet_task_dest','int');
$search_fk_account_from=GETPOST('search_fk_account_from','int');
$search_fk_account_dest=GETPOST('search_fk_account_dest','int');
$search_url_id=GETPOST('search_url_id','int');
$search_fk_bank=GETPOST('search_fk_bank','int');
$search_fk_commande_fourn=GETPOST('search_fk_commande_fourn','int');
$search_fk_facture_fourn=GETPOST('search_fk_facture_fourn','int');
$search_fk_entrepot=GETPOST('search_fk_entrepot','int');
$search_fk_user_from=GETPOST('search_fk_user_from','int');
$search_fk_user_to=GETPOST('search_fk_user_to','int');
$search_fk_type=GETPOST('search_fk_type','alpha');
$search_fk_categorie=GETPOST('search_fk_categorie','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_parent_app=GETPOST('search_fk_parent_app','int');
$search_quant=GETPOST('search_quant','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_code_facture=GETPOST('search_code_facture','alpha');
$search_code_type_purchase=GETPOST('search_code_type_purchase','alpha');
$search_type_operation=GETPOST('search_type_operation','int');
$search_nro_chq=GETPOST('search_nro_chq','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_concept=GETPOST('search_concept','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_nit_company=GETPOST('search_nit_company','alpha');
$search_codeqr=GETPOST('search_codeqr','alpha');
$search_fourn_nit=GETPOST('search_fourn_nit','alpha');
$search_fourn_soc=GETPOST('search_fourn_soc','alpha');
$search_fourn_facture=GETPOST('search_fourn_facture','alpha');
$search_fourn_numaut=GETPOST('search_fourn_numaut','alpha');
$search_fourn_amount_ttc=GETPOST('search_fourn_amount_ttc','alpha');
$search_fourn_amount=GETPOST('search_fourn_amount','alpha');
$search_fourn_codecont=GETPOST('search_fourn_codecont','alpha');
$search_fourn_reg1=GETPOST('search_fourn_reg1','alpha');
$search_fourn_reg2=GETPOST('search_fourn_reg2','alpha');
$search_fourn_reg3=GETPOST('search_fourn_reg3','alpha');
$search_fourn_reg4=GETPOST('search_fourn_reg4','alpha');
$search_fourn_reg5=GETPOST('search_fourn_reg5','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_approved=GETPOST('search_fk_user_approved','int');
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
$hookmanager->initHooks(array('requestcashdeplacementlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('finint');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Requestcashdeplacement($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.fk_request_cash'=>array('label'=>$langs->trans("Fieldfk_request_cash"), 'checked'=>1),
't.fk_request_cash_dest'=>array('label'=>$langs->trans("Fieldfk_request_cash_dest"), 'checked'=>1),
't.fk_projet_dest'=>array('label'=>$langs->trans("Fieldfk_projet_dest"), 'checked'=>1),
't.fk_projet_task_dest'=>array('label'=>$langs->trans("Fieldfk_projet_task_dest"), 'checked'=>1),
't.fk_account_from'=>array('label'=>$langs->trans("Fieldfk_account_from"), 'checked'=>1),
't.fk_account_dest'=>array('label'=>$langs->trans("Fieldfk_account_dest"), 'checked'=>1),
't.url_id'=>array('label'=>$langs->trans("Fieldurl_id"), 'checked'=>1),
't.fk_bank'=>array('label'=>$langs->trans("Fieldfk_bank"), 'checked'=>1),
't.fk_commande_fourn'=>array('label'=>$langs->trans("Fieldfk_commande_fourn"), 'checked'=>1),
't.fk_facture_fourn'=>array('label'=>$langs->trans("Fieldfk_facture_fourn"), 'checked'=>1),
't.fk_entrepot'=>array('label'=>$langs->trans("Fieldfk_entrepot"), 'checked'=>1),
't.fk_user_from'=>array('label'=>$langs->trans("Fieldfk_user_from"), 'checked'=>1),
't.fk_user_to'=>array('label'=>$langs->trans("Fieldfk_user_to"), 'checked'=>1),
't.fk_type'=>array('label'=>$langs->trans("Fieldfk_type"), 'checked'=>1),
't.fk_categorie'=>array('label'=>$langs->trans("Fieldfk_categorie"), 'checked'=>1),
't.fk_soc'=>array('label'=>$langs->trans("Fieldfk_soc"), 'checked'=>1),
't.fk_parent_app'=>array('label'=>$langs->trans("Fieldfk_parent_app"), 'checked'=>1),
't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.code_facture'=>array('label'=>$langs->trans("Fieldcode_facture"), 'checked'=>1),
't.code_type_purchase'=>array('label'=>$langs->trans("Fieldcode_type_purchase"), 'checked'=>1),
't.type_operation'=>array('label'=>$langs->trans("Fieldtype_operation"), 'checked'=>1),
't.nro_chq'=>array('label'=>$langs->trans("Fieldnro_chq"), 'checked'=>1),
't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
't.concept'=>array('label'=>$langs->trans("Fieldconcept"), 'checked'=>1),
't.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
't.nit_company'=>array('label'=>$langs->trans("Fieldnit_company"), 'checked'=>1),
't.codeqr'=>array('label'=>$langs->trans("Fieldcodeqr"), 'checked'=>1),
't.fourn_nit'=>array('label'=>$langs->trans("Fieldfourn_nit"), 'checked'=>1),
't.fourn_soc'=>array('label'=>$langs->trans("Fieldfourn_soc"), 'checked'=>1),
't.fourn_facture'=>array('label'=>$langs->trans("Fieldfourn_facture"), 'checked'=>1),
't.fourn_numaut'=>array('label'=>$langs->trans("Fieldfourn_numaut"), 'checked'=>1),
't.fourn_amount_ttc'=>array('label'=>$langs->trans("Fieldfourn_amount_ttc"), 'checked'=>1),
't.fourn_amount'=>array('label'=>$langs->trans("Fieldfourn_amount"), 'checked'=>1),
't.fourn_codecont'=>array('label'=>$langs->trans("Fieldfourn_codecont"), 'checked'=>1),
't.fourn_reg1'=>array('label'=>$langs->trans("Fieldfourn_reg1"), 'checked'=>1),
't.fourn_reg2'=>array('label'=>$langs->trans("Fieldfourn_reg2"), 'checked'=>1),
't.fourn_reg3'=>array('label'=>$langs->trans("Fieldfourn_reg3"), 'checked'=>1),
't.fourn_reg4'=>array('label'=>$langs->trans("Fieldfourn_reg4"), 'checked'=>1),
't.fourn_reg5'=>array('label'=>$langs->trans("Fieldfourn_reg5"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_approved'=>array('label'=>$langs->trans("Fieldfk_user_approved"), 'checked'=>1),
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
$search_ref='';
$search_fk_request_cash='';
$search_fk_request_cash_dest='';
$search_fk_projet_dest='';
$search_fk_projet_task_dest='';
$search_fk_account_from='';
$search_fk_account_dest='';
$search_url_id='';
$search_fk_bank='';
$search_fk_commande_fourn='';
$search_fk_facture_fourn='';
$search_fk_entrepot='';
$search_fk_user_from='';
$search_fk_user_to='';
$search_fk_type='';
$search_fk_categorie='';
$search_fk_soc='';
$search_fk_parent_app='';
$search_quant='';
$search_fk_unit='';
$search_code_facture='';
$search_code_type_purchase='';
$search_type_operation='';
$search_nro_chq='';
$search_amount='';
$search_concept='';
$search_detail='';
$search_nit_company='';
$search_codeqr='';
$search_fourn_nit='';
$search_fourn_soc='';
$search_fourn_facture='';
$search_fourn_numaut='';
$search_fourn_amount_ttc='';
$search_fourn_amount='';
$search_fourn_codecont='';
$search_fourn_reg1='';
$search_fourn_reg2='';
$search_fourn_reg3='';
$search_fourn_reg4='';
$search_fourn_reg5='';
$search_fk_user_create='';
$search_fk_user_approved='';
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
			header("Location: ".dol_buildpath('/finint/list.php',1));
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
		$sql .= " t.ref,";
		$sql .= " t.fk_request_cash,";
		$sql .= " t.fk_request_cash_dest,";
		$sql .= " t.fk_projet_dest,";
		$sql .= " t.fk_projet_task_dest,";
		$sql .= " t.fk_account_from,";
		$sql .= " t.fk_account_dest,";
		$sql .= " t.url_id,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_commande_fourn,";
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_user_from,";
		$sql .= " t.fk_user_to,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.fk_soc,";
		$sql .= " t.dateo,";
		$sql .= " t.date_dis,";
		$sql .= " t.date_app,";
		$sql .= " t.fk_parent_app,";
		$sql .= " t.quant,";
		$sql .= " t.fk_unit,";
		$sql .= " t.code_facture,";
		$sql .= " t.code_type_purchase,";
		$sql .= " t.type_operation,";
		$sql .= " t.nro_chq,";
		$sql .= " t.amount,";
		$sql .= " t.concept,";
		$sql .= " t.detail,";
		$sql .= " t.nit_company,";
		$sql .= " t.codeqr,";
		$sql .= " t.fourn_nit,";
		$sql .= " t.fourn_soc,";
		$sql .= " t.fourn_facture,";
		$sql .= " t.fourn_numaut,";
		$sql .= " t.fourn_date,";
		$sql .= " t.fourn_amount_ttc,";
		$sql .= " t.fourn_amount,";
		$sql .= " t.fourn_codecont,";
		$sql .= " t.fourn_reg1,";
		$sql .= " t.fourn_reg2,";
		$sql .= " t.fourn_reg3,";
		$sql .= " t.fourn_reg4,";
		$sql .= " t.fourn_reg5,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.date_dest,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."request_cash_deplacement as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."request_cash_deplacement_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_request_cash) $sql.= natural_search("fk_request_cash",$search_fk_request_cash);
if ($search_fk_request_cash_dest) $sql.= natural_search("fk_request_cash_dest",$search_fk_request_cash_dest);
if ($search_fk_projet_dest) $sql.= natural_search("fk_projet_dest",$search_fk_projet_dest);
if ($search_fk_projet_task_dest) $sql.= natural_search("fk_projet_task_dest",$search_fk_projet_task_dest);
if ($search_fk_account_from) $sql.= natural_search("fk_account_from",$search_fk_account_from);
if ($search_fk_account_dest) $sql.= natural_search("fk_account_dest",$search_fk_account_dest);
if ($search_url_id) $sql.= natural_search("url_id",$search_url_id);
if ($search_fk_bank) $sql.= natural_search("fk_bank",$search_fk_bank);
if ($search_fk_commande_fourn) $sql.= natural_search("fk_commande_fourn",$search_fk_commande_fourn);
if ($search_fk_facture_fourn) $sql.= natural_search("fk_facture_fourn",$search_fk_facture_fourn);
if ($search_fk_entrepot) $sql.= natural_search("fk_entrepot",$search_fk_entrepot);
if ($search_fk_user_from) $sql.= natural_search("fk_user_from",$search_fk_user_from);
if ($search_fk_user_to) $sql.= natural_search("fk_user_to",$search_fk_user_to);
if ($search_fk_type) $sql.= natural_search("fk_type",$search_fk_type);
if ($search_fk_categorie) $sql.= natural_search("fk_categorie",$search_fk_categorie);
if ($search_fk_soc) $sql.= natural_search("fk_soc",$search_fk_soc);
if ($search_fk_parent_app) $sql.= natural_search("fk_parent_app",$search_fk_parent_app);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_code_facture) $sql.= natural_search("code_facture",$search_code_facture);
if ($search_code_type_purchase) $sql.= natural_search("code_type_purchase",$search_code_type_purchase);
if ($search_type_operation) $sql.= natural_search("type_operation",$search_type_operation);
if ($search_nro_chq) $sql.= natural_search("nro_chq",$search_nro_chq);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_concept) $sql.= natural_search("concept",$search_concept);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_nit_company) $sql.= natural_search("nit_company",$search_nit_company);
if ($search_codeqr) $sql.= natural_search("codeqr",$search_codeqr);
if ($search_fourn_nit) $sql.= natural_search("fourn_nit",$search_fourn_nit);
if ($search_fourn_soc) $sql.= natural_search("fourn_soc",$search_fourn_soc);
if ($search_fourn_facture) $sql.= natural_search("fourn_facture",$search_fourn_facture);
if ($search_fourn_numaut) $sql.= natural_search("fourn_numaut",$search_fourn_numaut);
if ($search_fourn_amount_ttc) $sql.= natural_search("fourn_amount_ttc",$search_fourn_amount_ttc);
if ($search_fourn_amount) $sql.= natural_search("fourn_amount",$search_fourn_amount);
if ($search_fourn_codecont) $sql.= natural_search("fourn_codecont",$search_fourn_codecont);
if ($search_fourn_reg1) $sql.= natural_search("fourn_reg1",$search_fourn_reg1);
if ($search_fourn_reg2) $sql.= natural_search("fourn_reg2",$search_fourn_reg2);
if ($search_fourn_reg3) $sql.= natural_search("fourn_reg3",$search_fourn_reg3);
if ($search_fourn_reg4) $sql.= natural_search("fourn_reg4",$search_fourn_reg4);
if ($search_fourn_reg5) $sql.= natural_search("fourn_reg5",$search_fourn_reg5);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_approved) $sql.= natural_search("fk_user_approved",$search_fk_user_approved);
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
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_fk_request_cash != '') $params.= '&amp;search_fk_request_cash='.urlencode($search_fk_request_cash);
if ($search_fk_request_cash_dest != '') $params.= '&amp;search_fk_request_cash_dest='.urlencode($search_fk_request_cash_dest);
if ($search_fk_projet_dest != '') $params.= '&amp;search_fk_projet_dest='.urlencode($search_fk_projet_dest);
if ($search_fk_projet_task_dest != '') $params.= '&amp;search_fk_projet_task_dest='.urlencode($search_fk_projet_task_dest);
if ($search_fk_account_from != '') $params.= '&amp;search_fk_account_from='.urlencode($search_fk_account_from);
if ($search_fk_account_dest != '') $params.= '&amp;search_fk_account_dest='.urlencode($search_fk_account_dest);
if ($search_url_id != '') $params.= '&amp;search_url_id='.urlencode($search_url_id);
if ($search_fk_bank != '') $params.= '&amp;search_fk_bank='.urlencode($search_fk_bank);
if ($search_fk_commande_fourn != '') $params.= '&amp;search_fk_commande_fourn='.urlencode($search_fk_commande_fourn);
if ($search_fk_facture_fourn != '') $params.= '&amp;search_fk_facture_fourn='.urlencode($search_fk_facture_fourn);
if ($search_fk_entrepot != '') $params.= '&amp;search_fk_entrepot='.urlencode($search_fk_entrepot);
if ($search_fk_user_from != '') $params.= '&amp;search_fk_user_from='.urlencode($search_fk_user_from);
if ($search_fk_user_to != '') $params.= '&amp;search_fk_user_to='.urlencode($search_fk_user_to);
if ($search_fk_type != '') $params.= '&amp;search_fk_type='.urlencode($search_fk_type);
if ($search_fk_categorie != '') $params.= '&amp;search_fk_categorie='.urlencode($search_fk_categorie);
if ($search_fk_soc != '') $params.= '&amp;search_fk_soc='.urlencode($search_fk_soc);
if ($search_fk_parent_app != '') $params.= '&amp;search_fk_parent_app='.urlencode($search_fk_parent_app);
if ($search_quant != '') $params.= '&amp;search_quant='.urlencode($search_quant);
if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
if ($search_code_facture != '') $params.= '&amp;search_code_facture='.urlencode($search_code_facture);
if ($search_code_type_purchase != '') $params.= '&amp;search_code_type_purchase='.urlencode($search_code_type_purchase);
if ($search_type_operation != '') $params.= '&amp;search_type_operation='.urlencode($search_type_operation);
if ($search_nro_chq != '') $params.= '&amp;search_nro_chq='.urlencode($search_nro_chq);
if ($search_amount != '') $params.= '&amp;search_amount='.urlencode($search_amount);
if ($search_concept != '') $params.= '&amp;search_concept='.urlencode($search_concept);
if ($search_detail != '') $params.= '&amp;search_detail='.urlencode($search_detail);
if ($search_nit_company != '') $params.= '&amp;search_nit_company='.urlencode($search_nit_company);
if ($search_codeqr != '') $params.= '&amp;search_codeqr='.urlencode($search_codeqr);
if ($search_fourn_nit != '') $params.= '&amp;search_fourn_nit='.urlencode($search_fourn_nit);
if ($search_fourn_soc != '') $params.= '&amp;search_fourn_soc='.urlencode($search_fourn_soc);
if ($search_fourn_facture != '') $params.= '&amp;search_fourn_facture='.urlencode($search_fourn_facture);
if ($search_fourn_numaut != '') $params.= '&amp;search_fourn_numaut='.urlencode($search_fourn_numaut);
if ($search_fourn_amount_ttc != '') $params.= '&amp;search_fourn_amount_ttc='.urlencode($search_fourn_amount_ttc);
if ($search_fourn_amount != '') $params.= '&amp;search_fourn_amount='.urlencode($search_fourn_amount);
if ($search_fourn_codecont != '') $params.= '&amp;search_fourn_codecont='.urlencode($search_fourn_codecont);
if ($search_fourn_reg1 != '') $params.= '&amp;search_fourn_reg1='.urlencode($search_fourn_reg1);
if ($search_fourn_reg2 != '') $params.= '&amp;search_fourn_reg2='.urlencode($search_fourn_reg2);
if ($search_fourn_reg3 != '') $params.= '&amp;search_fourn_reg3='.urlencode($search_fourn_reg3);
if ($search_fourn_reg4 != '') $params.= '&amp;search_fourn_reg4='.urlencode($search_fourn_reg4);
if ($search_fourn_reg5 != '') $params.= '&amp;search_fourn_reg5='.urlencode($search_fourn_reg5);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_approved != '') $params.= '&amp;search_fk_user_approved='.urlencode($search_fk_user_approved);
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
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_request_cash']['checked'])) print_liste_field_titre($arrayfields['t.fk_request_cash']['label'],$_SERVER['PHP_SELF'],'t.fk_request_cash','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_request_cash_dest']['checked'])) print_liste_field_titre($arrayfields['t.fk_request_cash_dest']['label'],$_SERVER['PHP_SELF'],'t.fk_request_cash_dest','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet_dest']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet_dest']['label'],$_SERVER['PHP_SELF'],'t.fk_projet_dest','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet_task_dest']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet_task_dest']['label'],$_SERVER['PHP_SELF'],'t.fk_projet_task_dest','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_account_from']['checked'])) print_liste_field_titre($arrayfields['t.fk_account_from']['label'],$_SERVER['PHP_SELF'],'t.fk_account_from','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_account_dest']['checked'])) print_liste_field_titre($arrayfields['t.fk_account_dest']['label'],$_SERVER['PHP_SELF'],'t.fk_account_dest','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.url_id']['checked'])) print_liste_field_titre($arrayfields['t.url_id']['label'],$_SERVER['PHP_SELF'],'t.url_id','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_bank']['checked'])) print_liste_field_titre($arrayfields['t.fk_bank']['label'],$_SERVER['PHP_SELF'],'t.fk_bank','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_commande_fourn']['checked'])) print_liste_field_titre($arrayfields['t.fk_commande_fourn']['label'],$_SERVER['PHP_SELF'],'t.fk_commande_fourn','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_facture_fourn']['checked'])) print_liste_field_titre($arrayfields['t.fk_facture_fourn']['label'],$_SERVER['PHP_SELF'],'t.fk_facture_fourn','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_entrepot']['checked'])) print_liste_field_titre($arrayfields['t.fk_entrepot']['label'],$_SERVER['PHP_SELF'],'t.fk_entrepot','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_from']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_from']['label'],$_SERVER['PHP_SELF'],'t.fk_user_from','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_to']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_to']['label'],$_SERVER['PHP_SELF'],'t.fk_user_to','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_type']['checked'])) print_liste_field_titre($arrayfields['t.fk_type']['label'],$_SERVER['PHP_SELF'],'t.fk_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_categorie']['checked'])) print_liste_field_titre($arrayfields['t.fk_categorie']['label'],$_SERVER['PHP_SELF'],'t.fk_categorie','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_soc']['checked'])) print_liste_field_titre($arrayfields['t.fk_soc']['label'],$_SERVER['PHP_SELF'],'t.fk_soc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_parent_app']['checked'])) print_liste_field_titre($arrayfields['t.fk_parent_app']['label'],$_SERVER['PHP_SELF'],'t.fk_parent_app','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_facture']['checked'])) print_liste_field_titre($arrayfields['t.code_facture']['label'],$_SERVER['PHP_SELF'],'t.code_facture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_type_purchase']['checked'])) print_liste_field_titre($arrayfields['t.code_type_purchase']['label'],$_SERVER['PHP_SELF'],'t.code_type_purchase','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_operation']['checked'])) print_liste_field_titre($arrayfields['t.type_operation']['label'],$_SERVER['PHP_SELF'],'t.type_operation','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nro_chq']['checked'])) print_liste_field_titre($arrayfields['t.nro_chq']['label'],$_SERVER['PHP_SELF'],'t.nro_chq','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.concept']['checked'])) print_liste_field_titre($arrayfields['t.concept']['label'],$_SERVER['PHP_SELF'],'t.concept','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nit_company']['checked'])) print_liste_field_titre($arrayfields['t.nit_company']['label'],$_SERVER['PHP_SELF'],'t.nit_company','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.codeqr']['checked'])) print_liste_field_titre($arrayfields['t.codeqr']['label'],$_SERVER['PHP_SELF'],'t.codeqr','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_nit']['checked'])) print_liste_field_titre($arrayfields['t.fourn_nit']['label'],$_SERVER['PHP_SELF'],'t.fourn_nit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_soc']['checked'])) print_liste_field_titre($arrayfields['t.fourn_soc']['label'],$_SERVER['PHP_SELF'],'t.fourn_soc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_facture']['checked'])) print_liste_field_titre($arrayfields['t.fourn_facture']['label'],$_SERVER['PHP_SELF'],'t.fourn_facture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_numaut']['checked'])) print_liste_field_titre($arrayfields['t.fourn_numaut']['label'],$_SERVER['PHP_SELF'],'t.fourn_numaut','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_amount_ttc']['checked'])) print_liste_field_titre($arrayfields['t.fourn_amount_ttc']['label'],$_SERVER['PHP_SELF'],'t.fourn_amount_ttc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_amount']['checked'])) print_liste_field_titre($arrayfields['t.fourn_amount']['label'],$_SERVER['PHP_SELF'],'t.fourn_amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_codecont']['checked'])) print_liste_field_titre($arrayfields['t.fourn_codecont']['label'],$_SERVER['PHP_SELF'],'t.fourn_codecont','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_reg1']['checked'])) print_liste_field_titre($arrayfields['t.fourn_reg1']['label'],$_SERVER['PHP_SELF'],'t.fourn_reg1','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_reg2']['checked'])) print_liste_field_titre($arrayfields['t.fourn_reg2']['label'],$_SERVER['PHP_SELF'],'t.fourn_reg2','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_reg3']['checked'])) print_liste_field_titre($arrayfields['t.fourn_reg3']['label'],$_SERVER['PHP_SELF'],'t.fourn_reg3','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_reg4']['checked'])) print_liste_field_titre($arrayfields['t.fourn_reg4']['label'],$_SERVER['PHP_SELF'],'t.fourn_reg4','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fourn_reg5']['checked'])) print_liste_field_titre($arrayfields['t.fourn_reg5']['label'],$_SERVER['PHP_SELF'],'t.fourn_reg5','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_approved']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_approved']['label'],$_SERVER['PHP_SELF'],'t.fk_user_approved','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.fk_request_cash']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_request_cash" value="'.$search_fk_request_cash.'" size="10"></td>';
if (! empty($arrayfields['t.fk_request_cash_dest']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_request_cash_dest" value="'.$search_fk_request_cash_dest.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet_dest']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet_dest" value="'.$search_fk_projet_dest.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet_task_dest']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet_task_dest" value="'.$search_fk_projet_task_dest.'" size="10"></td>';
if (! empty($arrayfields['t.fk_account_from']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_account_from" value="'.$search_fk_account_from.'" size="10"></td>';
if (! empty($arrayfields['t.fk_account_dest']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_account_dest" value="'.$search_fk_account_dest.'" size="10"></td>';
if (! empty($arrayfields['t.url_id']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_url_id" value="'.$search_url_id.'" size="10"></td>';
if (! empty($arrayfields['t.fk_bank']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_bank" value="'.$search_fk_bank.'" size="10"></td>';
if (! empty($arrayfields['t.fk_commande_fourn']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_commande_fourn" value="'.$search_fk_commande_fourn.'" size="10"></td>';
if (! empty($arrayfields['t.fk_facture_fourn']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_facture_fourn" value="'.$search_fk_facture_fourn.'" size="10"></td>';
if (! empty($arrayfields['t.fk_entrepot']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_entrepot" value="'.$search_fk_entrepot.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_from']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_from" value="'.$search_fk_user_from.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_to']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_to" value="'.$search_fk_user_to.'" size="10"></td>';
if (! empty($arrayfields['t.fk_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_type" value="'.$search_fk_type.'" size="10"></td>';
if (! empty($arrayfields['t.fk_categorie']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_categorie" value="'.$search_fk_categorie.'" size="10"></td>';
if (! empty($arrayfields['t.fk_soc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_soc" value="'.$search_fk_soc.'" size="10"></td>';
if (! empty($arrayfields['t.fk_parent_app']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_parent_app" value="'.$search_fk_parent_app.'" size="10"></td>';
if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.code_facture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_facture" value="'.$search_code_facture.'" size="10"></td>';
if (! empty($arrayfields['t.code_type_purchase']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_type_purchase" value="'.$search_code_type_purchase.'" size="10"></td>';
if (! empty($arrayfields['t.type_operation']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_operation" value="'.$search_type_operation.'" size="10"></td>';
if (! empty($arrayfields['t.nro_chq']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nro_chq" value="'.$search_nro_chq.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.concept']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_concept" value="'.$search_concept.'" size="10"></td>';
if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['t.nit_company']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nit_company" value="'.$search_nit_company.'" size="10"></td>';
if (! empty($arrayfields['t.codeqr']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_codeqr" value="'.$search_codeqr.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_nit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_nit" value="'.$search_fourn_nit.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_soc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_soc" value="'.$search_fourn_soc.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_facture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_facture" value="'.$search_fourn_facture.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_numaut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_numaut" value="'.$search_fourn_numaut.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_amount_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_amount_ttc" value="'.$search_fourn_amount_ttc.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_amount" value="'.$search_fourn_amount.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_codecont']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_codecont" value="'.$search_fourn_codecont.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_reg1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_reg1" value="'.$search_fourn_reg1.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_reg2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_reg2" value="'.$search_fourn_reg2.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_reg3']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_reg3" value="'.$search_fourn_reg3.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_reg4']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_reg4" value="'.$search_fourn_reg4.'" size="10"></td>';
if (! empty($arrayfields['t.fourn_reg5']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fourn_reg5" value="'.$search_fourn_reg5.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_approved']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_approved" value="'.$search_fk_user_approved.'" size="10"></td>';
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
