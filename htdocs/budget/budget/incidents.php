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
 *   	\file       budget/budget_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-04 12:08
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');

dol_include_once('/user/class/user.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/budget/class/budgetext.class.php');

dol_include_once('/budget/class/incidentsext.class.php');
dol_include_once('/budget/class/incidentsdetext.class.php');
dol_include_once('/budget/class/incidentsres.class.php');

dol_include_once('/budget/class/budgetincidentsext.class.php');
dol_include_once('/budget/class/budgetincidentsdet.class.php');
dol_include_once('/budget/class/budgetincidentsres.class.php');


dol_include_once('/budget/lib/budget.lib.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';


//excel para una versión anterior
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
$ver = 0;
if (file_exists($file))
{
	$ver = 1;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

//excel para version 4 o sup
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
if (file_exists($file))
{
	$ver = 2;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


// Load traductions files requiredby by page
$langs->load("other");
$langs->load("products");
$langs->load("companies");
$langs->load("commercial");
$langs->load("banks");
$langs->load("users");
$langs->load("budget@budget");

if (! empty($conf->stock->enabled)) $langs->load("stocks");
if (! empty($conf->facture->enabled)) $langs->load("bills");
if (! empty($conf->productbatch->enabled)) $langs->load("productbatch");
// Get parameters
$id			= GETPOST('id','int');
$idg		= GETPOST('idg','int');
$idr		= GETPOST('idr','int');
$idrd		= GETPOST('idrd','int');
$type = GETPOST('type','alpha');
$action		= GETPOST('action','alpha');
$subaction	= GETPOST('subaction','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$fk_selitem = GETPOST('fk_selitem','int');

$selrow = GETPOST('selrow');
$sesbudget = unserialize($_SESSION['sesbudget']);
//guardamos en session si se selecciona
if (isset($_POST['id'])||isset($_GET['id']))
	$sesbudget['id_presup'] = GETPOST('id');
$id = $sesbudget['id_presup'];


if (isset($_POST['idg'])||isset($_GET['idg']))
	$sesbudget[$id]['idg'] = GETPOST('idg');

// Security check
if ($user->societe_id) $socid=$user->societe_id;
if (!$user->rights->budget->bud->read) accessforbidden();

$idg = $sesbudget[$id]['idg'];

$search_fk_soc=GETPOST('search_fk_soc','int');
$search_ref=GETPOST('search_ref','alpha');
$search_entity=GETPOST('search_entity','int');
$search_title=GETPOST('search_title','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_public=GETPOST('search_public','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_fk_opp_status=GETPOST('search_fk_opp_status','int');
$search_opp_percent=GETPOST('search_opp_percent','alpha');
$search_fk_user_close=GETPOST('search_fk_user_close','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_opp_amount=GETPOST('search_opp_amount','alpha');
$search_budget_amount=GETPOST('search_budget_amount','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');

// Load variable for pagination
$socid		= GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;

$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";
$params='';
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

$table = 'llx_budget_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
//armamos los campos obligatorios
$aHeaderTpl['llx_projet_task'] = array('ref' => 'ref',
	'label' => 'label',
	'hilo' => 'hilo',
	'login' => 'login',
	'fechaini'=>'fechaini',
	'fechafin'=>'fechafin',
	'group'=>'group',
	'type'=>'type',
	'typename'=>'typename',
	'unitprogram'=>'unitprogram',
	'unit'=>'unit',
	'price' => 'price',);
$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aCampodate = array('fechaini' =>'date_start',
	'fechafin' => 'date_end');

//RECUPERAMOS LOS COLORES
$color_ma = $conf->global->ITEMS_COLOR_CATEGORY_MA;
$color_mo= $conf->global->ITEMS_COLOR_CATEGORY_MO;
$color_mq = $conf->global->ITEMS_COLOR_CATEGORY_MQ;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
$aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');

$aDatatype[0] = $langs->trans('Project');
if ($user->rights->budget->bud->writem)
	$aDatatype[1]= $langs->trans('Databasemother');

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object 	= new Budgetext($db);
$objecttmp 	= new Budgetext($db);

$objCregiongeographic = new Cregiongeographic($db);
$objIncidents = new Incidentsext($db);
$objIncidentsdet = new Incidentsdetext($db);
$objIncidentsres = new Incidentsres($db);

$objBudgetincidents = new Budgetincidentsext($db);
$objBudgetincidentsdet = new Budgetincidentsdet($db);
$objBudgetincidentsres = new Budgetincidentsres($db);
$objIncidentsdettmp = new Budgetincidentsdet($db);

$objUser 		= new User($db);

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
}
if ($idr>0)
{
	//vamos a buscar las incidencias
	$resincidents = $objBudgetincidents->fetch($idr);
}
$aParameter=array('BENESOC'=>$langs->trans('Socialbenefits'),'HERMEN'=>$langs->trans('Minortools'),'GASGEN'=>$langs->trans('Generalexpenses'),'COSTMO'=>$langs->trans('Laborcostdirecthours'));

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budget'));
$extrafields = new ExtraFields($db);

//armamos la estructura para futuras acciones
//if (!isset($_SESSION['aStrbudget']))
//{
if ($id)
{
	//$res = get_structure_budget($id);
}
$aStrbudget = unserialize($_SESSION['aStrbudget']);

if ($fk_selitem>0) $items->fetch($fk_selitem);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/incidents.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0) $ret = $objBudgetincidents->fetch($idr);
		$action='';
	}

	if ($action == 'confirm_import')
	{
		//buscamos el incident por region
		$filter = " AND t.fk_region = ".$object->fk_region;
		$res = $objIncidents->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
		if ($res >0)
		{
			$lines = $objIncidents->lines;
			foreach ($lines AS $j => $line)
			{
				$res = $objIncidents->clone_budget($user,$id,$line->id);
				if ($res <=0)
				{
					$error++;
				}
			}
		}
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
		exit;

	}
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objBudgetincidents->entity=$conf->entity;
		$objBudgetincidents->fk_budget=$id;
		$objBudgetincidents->ref=GETPOST('ref','alpha');
		$objBudgetincidents->label=GETPOST('label','alpha');
		$objBudgetincidents->code_parameter=GETPOST('code_parameter','alpha');
		$objBudgetincidents->fk_region=GETPOST('fk_region','int');
		$objBudgetincidents->day_year=GETPOST('day_year','int');
		$objBudgetincidents->day_efective=GETPOST('day_efective','int');
		$objBudgetincidents->day_journal=GETPOST('day_journal','int');
		$objBudgetincidents->day_num=GETPOST('day_num','int');
		$objBudgetincidents->salary_min=GETPOST('salary_min','alpha');
		$objBudgetincidents->njobs=GETPOST('njobs','int');
		$objBudgetincidents->cost_direct=GETPOST('cost_direct','alpha');
		$objBudgetincidents->time_duration=GETPOST('time_duration','int');
		$objBudgetincidents->exchange_rate=GETPOST('exchange_rate','alpha');
		$objBudgetincidents->ponderation=GETPOST('ponderation','alpha');
		$objBudgetincidents->commission=GETPOST('commission','alpha');
		$objBudgetincidents->incident=GETPOST('incident','alpha');
		$objBudgetincidents->active=GETPOST('active','int');
		$objBudgetincidents->fk_user_create=$user->id;
		$objBudgetincidents->fk_user_mod=$user->id;
		$objBudgetincidents->datec= $now;
		$objBudgetincidents->datem= $now;
		$objBudgetincidents->tms= $now;
		$objBudgetincidents->status=0;



		if (empty($objBudgetincidents->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgetincidents->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objBudgetincidents->errors)) setEventMessages(null, $objBudgetincidents->errors, 'errors');
				else  setEventMessages($objBudgetincidents->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;

		$objBudgetincidents->entity=$conf->entity;
		$objBudgetincidents->ref=dol_string_nospecial(trim(dol_strtoupper(GETPOST('ref','alpha'))));
		$objBudgetincidents->label=GETPOST('label','alpha');
		$objBudgetincidents->fk_region=GETPOST('fk_region','int');
		$objBudgetincidents->code_parameter=GETPOST('code_parameter','alpha');
		$objBudgetincidents->day_year=GETPOST('day_year','int');
		$objBudgetincidents->day_efective_month=GETPOST('day_efective_month','int');
		if (empty($objBudgetincidents->day_efective_month)) $objBudgetincidents->day_efective_month=0;
		//$objBudgetincidents->day_journal=GETPOST('day_journal','int');
		//if (empty($objBudgetincidents->day_journal)) $objBudgetincidents->day_journal=0;
		$objBudgetincidents->day_num=GETPOST('day_num','int');
		if (empty($objBudgetincidents->day_num)) $objBudgetincidents->day_num=0;
		$objBudgetincidents->salary_min=GETPOST('salary_min','int');
		if (empty($objBudgetincidents->salary_min)) $objBudgetincidents->salary_min=0;
		$objBudgetincidents->njobs=GETPOST('njobs','int');
		if (empty($objBudgetincidents->njobs)) $objBudgetincidents->njobs=0;
		$objBudgetincidents->cost_direct=GETPOST('cost_direct','int');
		if (empty($objBudgetincidents->cost_direct)) $objBudgetincidents->cost_direct=0;
		$objBudgetincidents->time_duration=GETPOST('time_duration','int');
		if (empty($objBudgetincidents->time_duration)) $objBudgetincidents->time_duration=0;
		$objBudgetincidents->exchange_rate=GETPOST('exchange_rate','int');
		if (empty($objBudgetincidents->exchange_rate)) $objBudgetincidents->exchange_rate=0;
		$objBudgetincidents->tva_tx=GETPOST('tva_tx','int');
		if (empty($objBudgetincidents->tva_tx)) $objBudgetincidents->tva_tx=0;
		$objBudgetincidents->day_efective_month=GETPOST('day_efective_month','int');
		if (empty($objBudgetincidents->day_efective_month)) $objBudgetincidents->day_efective_month=0;
		$objBudgetincidents->commission=GETPOST('commission','alpha');
		if (empty($objBudgetincidents->commission)) $objBudgetincidents->commission=0;
		$objBudgetincidents->incident=GETPOST('incident','alpha');
		if (empty($objBudgetincidents->incident)) $objBudgetincidents->incident=0;
		$objBudgetincidents->active=GETPOST('active','int');
		if (empty($objBudgetincidents->active)) $objBudgetincidents->active=0;
		//$objBudgetincidents->ponderation=0;
		$objBudgetincidents->fk_user_mod=$user->id;
		$objBudgetincidents->datem = $now;
		$objBudgetincidents->tms = $now;
		$objBudgetincidents->status=0;



		if (empty($objBudgetincidents->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgetincidents->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objBudgetincidents->errors)) setEventMessages(null, $objBudgetincidents->errors, 'errors');
				else setEventMessages($objBudgetincidents->error, null, 'errors');
				$action='edit';
			}
			if ($result>0)
			{
				$objBudgetincidentstmp = new Budgetincidentsext($db);
				$objBudgetincidentstmp->fetch($idr);
				//vamos a actualizar los mismos valores a las demas incidencias
				$filter= " AND t.fk_budget = ".$object->id;
				$filter = " AND t.fk_region = ".$object->fk_region;
				$filter.= " AND t.rowid != ".$idr;
				$res = $objBudgetincidents->fetchAll('','',0,0,array(),'AND',$filter);
				if ($res>0)
				{
					$lines = $objBudgetincidents->lines;
					foreach ($lines AS $j => $line)
					{
						if (!$error)
						{
						//vamos a actualizar los valores genericos
						$res = $objBudgetincidents->fetch($line->id);
						if ($res==1)
						{
							$objBudgetincidents->day_year=$objBudgetincidentstmp->day_year;
							$objBudgetincidents->day_efective_month=$objBudgetincidentstmp->day_efective_month;
							$objBudgetincidents->day_efective=$objBudgetincidentstmp->day_efective;
							$objBudgetincidents->day_journal=$objBudgetincidentstmp->day_journal;
							$objBudgetincidents->day_num=$objBudgetincidentstmp->day_num;
							$objBudgetincidents->njobs=$objBudgetincidentstmp->njobs;
							$objBudgetincidents->salary_min=$objBudgetincidentstmp->salary_min;
							$objBudgetincidents->cost_direct=$objBudgetincidentstmp->cost_direct;
							$objBudgetincidents->time_duration=$objBudgetincidentstmp->time_duration;
							$objBudgetincidents->exchange_rate=$objBudgetincidentstmp->exchange_rate;
							$objBudgetincidents->tva_tx=$objBudgetincidentstmp->tva_tx;
							$objBudgetincidents->commission=$objBudgetincidentstmp->commission;
							if ($objBudgetincidentstmp->ponderation>0)
								$objBudgetincidents->ponderation=$objBudgetincidentstmp->ponderation;

							$res = $objBudgetincidents->update($user);

							if ($res <=0)
							{
								$error++;
								setEventMessages($objBudgetincidents->error,$objBudgetincidents->errors,'errors');
							}

						}
					}

					}
				}
			}

		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objBudgetincidents->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objBudgetincidents->errors)) setEventMessages(null, $objBudgetincidents->errors, 'errors');
			else setEventMessages($objBudgetincidents->error, null, 'errors');
		}
	}
}



$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);
// Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

//armamos las regiones en un array
//$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
//if ($res>0)
//{
//	$linesclassfin = $objCclasfin->lines;
//	foreach ($linesclassfin AS $j => $line)
//		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
//}
//armamos las regiones en un array
$filter='';
$res = $objCregiongeographic->fetchAll('ASC','t.label',0,0,array('status'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
		$aRegiongeographic[$line->id] = $line->label.' ('.$line->ref.')';
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$form = new Formv($db);
$formfile = new FormFile($db);
$formcompany = new FormCompany($db);

$link1 ='../https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css';
$link2='../https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css';
$link3 = '';
$link4 = '';
if ($action == 'viewit')
{
	$link3 = '/includes/jquery/plugins/jeditable/jquery.jeditable.js';
	$link4 = '/budget/js/js.js';
}
	//$link3 = '/budget/js/edit.js';
$morejs = array('/budget/js/priceunit.js','/budget/js/budget.js',);

//'/budget/css/bootstrap.min.css'
$morecss = array('/budget/css/style.css',);

//REVISAR SI SE ACTIVA O NO RAMIREX
//if ($idg)
$morecss = array('/budget/css/style.css','/budget/css/bootstrap.min.css','/includes/jquery//plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);

llxHeader('',$title,'','','','',$morejs,$morecss,0,0);

// Put here content of your page
$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';

if ($id > 0)
{
	$head = budget_prepare_head($object, $user);
	$titre=$langs->trans("Budget");
	$picto='budget';
 	$getcard = 'incidents';
	dol_fiche_head($head, $getcard, $titre, 0, $picto);

	if ($action == 'import')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, $langs->trans('Importincidents'), $langs->trans('ConfirmImportincidents'), 'confirm_import', '', 1, 2);
		print $formconfirm;
	}
	if ($action == 'validate')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, $langs->trans('Validateincidents'), $langs->trans('ConfirmValidateincidents'), 'confirm_validate', '', 1, 2);
		print $formconfirm;
	}

	print '<table class="noborder centpercent">'."\n";
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldversion'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldtitle'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	if ($action != 'viewcalendar')
		print_liste_field_titre($langs->trans('Fieldamount'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	else
	{
		print_liste_field_titre($langs->trans('Fielddate'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldcalendar'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	}
	print '</tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$object->getNomUrl(1).'</td>';
	print '<td>'.$object->version.'</td>';
	print '<td>'.$object->title.'</td>';
	if ($action != 'viewcalendar')
		print '<td align="right">'.price(price2num($object->budget_amount,$general->decimal_total)).'</td>';
	else
	{
		print '<td align="center">'.dol_print_date($object->dateo,'day').'</td>';
		$objcalendar->fetch($object->fk_calendar);
		if ($objcalendar->id == $object->fk_calendar)
			print $objcalendar->label;
		else
			print '';
	}
	print '</tr>';
	print '</table>';

	dol_fiche_end();

	print '<div class="div-table-responsive">';
	if ($idr>0 || (empty($idr) && $action == 'create')) include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgetincidents_card.tpl.php';
	else include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgetincidents_list.tpl.php';
	print '</div>';
}

$_SESSION['sesbudget']= serialize($sesbudget);
if ($action == 'viewgr' || $action == 'viewit' || $action=='viewre')
{
	print '
	<!-- ./wrapper -->
	<!-- Bootstrap 3.3.6 -->

	<script src="../js/bootstrap.min.js"></script>
	<!-- FastClick -->
	<script src="../plugins/fastclick/fastclick.js"></script>
	<!-- AdminLTE App -->
	<script src="../js/app.min.js"></script>
	<!-- Sparkline -->
	<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
	<!-- jvectormap -->
	<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	<!-- SlimScroll 1.3.0 -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- ChartJS 1.0.1 -->
	<script src="../plugins/chartjs/Chart.min.js"></script>
	<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
	<script src="../js/pages/dashboard2.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="../js/demo.js"></script>
	';
}
// End of page
llxFooter();
$db->close();
