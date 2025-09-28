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
dol_include_once('/product/class/product.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cdepartementsregion.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/budget/class/budgettaskext.class.php');
dol_include_once('/budget/class/budgettaskproduct.class.php');
dol_include_once('/budget/class/budgettaskproduction.class.php');
dol_include_once('/budget/class/budgettaskaddext.class.php');
dol_include_once('/budget/class/budgettaskresourceext.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/productasset.class.php');

dol_include_once('/categories/class/categorie.class.php');
dol_include_once('/budget/class/puoperatorext.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/budget/class/productbudgetext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
dol_include_once('/budget/class/budgetgeneral.class.php');
dol_include_once('/budget/class/city.class.php');
dol_include_once('/budget/class/budgettaskproductivityext.class.php');

dol_include_once('/budget/lib/calcunit.lib.php');
dol_include_once('/budget/lib/budget.lib.php');
dol_include_once('/budget/lib/utils.lib.php');

dol_include_once('/budget/core/modules/budget/modules_budget.php');
dol_include_once('/budget/lib/verifcontact.lib.php');
dol_include_once('/budget/class/calendar.class.php');
dol_include_once('/budget/class/budgettaskduration.class.php');

//para los items
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';


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
$action		= GETPOST('action','alpha');
$subaction	= GETPOST('subaction','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$fk_selitem = GETPOST('fk_selitem','int');
$idsearchup = GETPOST('idg');

$selrow = GETPOST('selrow');
$sesbudget = unserialize($_SESSION['sesbudget']);
//guardamos en session si se selecciona
if (isset($_POST['id'])||isset($_GET['id']))
	$sesbudget['id_presup'] = GETPOST('id');
$id = $sesbudget['id_presup'];
if ($action == 'create') $id = 0;
if (isset($_POST['idg'])||isset($_GET['idg']))
	$sesbudget[$id]['idg'] = GETPOST('idg');

// Security check
if ($user->societe_id) $socid=$user->societe_id;

if (!$user->rights->budget->bud->read) accessforbidden();
//$result=restrictedArea($user,'budget',$id);

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
$budget 	= new Budgetext($db);
$objecttmp 	= new Budgetext($db);

$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);
$objCdepartementsregion = new Cdepartementsregion($db);

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
	$fk_sector = $object->fk_sector;
	$resultcdr=$objCdepartementsregion->fetch(0,$object->fk_departement);
	if ($resultcdr < 0) dol_print_error($db);
	$fk_region = $objCdepartementsregion->fk_region_geographic;
	if ($fk_region>0) $object->fk_region = $fk_region;
}

$objuser 		= new User($db);
$objectdet 		= new Budgettaskext($db);
$objectdettmp 	= new Budgettaskext($db);
$objectdettmp0	= new Budgettaskext($db);
$objectdetadd 	= new Budgettaskaddext($db);
$objectdetaddtmp= new Budgettaskaddext($db);
$objectbtr 		= new Budgettaskresourceext($db);
$objectbtrtmp	= new Budgettaskresourceext($db);
$pustr 			= new Pustructureext($db);
$objstr			= new Pustructureext($db);
$objstrdet		= new Pustructuredetext($db);
$objprodb		= new Productbudgetext($db);
$objprodbtmp	= new Productbudgetext($db);
$items 			= new Itemsext($db);
$itemstmp 		= new Itemsext($db);
$product 		= new Product($db);
$categorie 		= new Categorie($db);
$typestr		= new Putypestructureext($db);
$general 		= new Budgetgeneral($db);
$objproductivity= new Budgettaskproductivityext($db);
$objcity 		= new City($db);
$objcalendar 	= new Calendar($db);
$objItemsproduct = new Itemsproduct($db);

//corrigiendo
$objItemsgroup = new Itemsgroupext($db);
$objItems = new Itemsext($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproduction = new Itemsproduction($db);
$objBudgettask = new Budgettaskext($db);
$objBudgettaskadd = new Budgettaskaddext($db);
$objBudgettaskproduct = new Budgettaskproduct($db);
$objBudgettaskproduction = new Budgettaskproduction($db);
$objProductbudget = new Productbudget($db);
$objBudgettaskresource = new Budgettaskresourceext($db);
$objProductasset = new Productasset($db);
$objProduct = new Product($db);
$objCategorie = new Categorie($db);
$objGeneral = new Budgetgeneral($db);


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budget'));
$extrafields = new ExtraFields($db);

//armamos la estructura para futuras acciones
//if (!isset($_SESSION['aStrbudget']))
//{
//

if ($id)
{
	$res = get_structure_budget($id);
	$general->fetch(0,$id);

	//parametros generales
	/*
	$general->fetch(0,$id);
	//armamos la estructura a utilizar
	$filter = array(1=>1);
	$filterstatic = " AND t.type_structure = '".$object->type_structure."'";
	$filterstatic.= " AND t.fk_categorie > 0";
		//$filterstatic.= " AND t.ordby = 1";
	$pustr->fetchAll('ASC', 'ordby', 0, 0, $filter, 'AND',$filterstatic,false);

	foreach((array) $pustr->lines AS $i => $linestr)
	{
		//buscamos la categoria
		$categorie->fetch($linestr->fk_categorie);
		$aStrid[$linestr->id] = $linestr->id;
		$aStridcat[$linestr->id] = $linestr->fk_categorie;
		$aStrcatid[$linestr->fk_categorie] = $linestr->id;
		$aStrcatcode[$linestr->fk_categorie] = $linestr->ref;
		$aStrcatcolor[$linestr->fk_categorie] = $categorie->color;
		$aStr[$linestr->ref] = $linestr->ref;
		$aStrref[$linestr->ref] = $linestr->detail;
		$aStrlabel[$linestr->fk_categorie] = $linestr->detail;
	}
	$_SESSION['aStrbudget'] = serialize(array($id=>array('aStrid'=>$aStrid,'aStridcat'=>$aStridcat,'aStrcatid'=>$aStrcatid,'aStr'=>$aStr,'aStrref'=>$aStrref,'aStrlabel'=>$aStrlabel,'aStrcatcode'=>$aStrcatcode,'aStrcatcolor'=>$aStrcatcolor)));
	*/
}
$aStrbudget = unserialize($_SESSION['aStrbudget']);

if ($fk_selitem>0) $items->fetch($fk_selitem);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$general,$action);
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{

if ($action == 'exportpresupgeneral' || $action == 'exportpriceunit'|| $action == 'exportresource' /*faltaagregarlosdemas*/)
{
	include DOL_DOCUMENT_ROOT.'/budget/budget/include/export.inc.php';
	$action='';
}
if ($action == 'confirm_delete_concept')
{
	$action = 'gen';
	$subaction = 'confirm_delete_concept';
}
if ($action == 'gen')
{
	$lUpdatetask = false;
	if ($subaction == 'addconcept' || $subaction == 'updateconcept')
	{
		$lUpdatetask = true;
		$action = 'gen';
	}
	include DOL_DOCUMENT_ROOT.'/budget/general/tpl/crud.tpl.php';
	include DOL_DOCUMENT_ROOT.'/budget/concept/tpl/crud.tpl.php';

	if ($lUpdatetask)
	{
		$db->begin();
		$object->fetch($id);
		$res = $object->update_pu_all($user,$aStrbudget,'general');

		if ($res >= 0)
			$db->commit();
		else
			$db->rollback();
		$action = 'gen';
		$subaction = '';
	}
}
	//revisamos los items para agregar a la tabla items
$filter = " AND t.fk_budget = ".$id;
$res = $objectdet->fetchAll('', '', 0, 0, array(1=>1), 'AND',$filter);
if ($res > 0 && $abc)
{
	$lines = $objectdet->lines;
	foreach ($lines AS $j => $line)
	{
		$refsearch = $line->label;
		$filteritem = " AND t.detail = '".trim($refsearch)."'";
		$res = $items->fetchAll('', '', 0, 0, array(1=>1), 'AND',$filteritem,true);
		if ($res ==0)
		{
				//agregamos como item nuevo
			$items->initAsSpecimen();
			$items->entity=$conf->entity;
			$items->ref='(PROV)';
			$items->ref_ext='';
			$items->fk_user_create=$user->id;
			$items->fk_user_mod=$user->id;
			$items->fk_type_item=GETPOST('fk_type_item','int')+0;
			$items->detail=$refsearch;
			$items->fk_unit=$line->fk_unit+0;
			$items->especification='';
			$items->plane='';
			$items->quant = 1;
			$items->amount=$line->unit_amount+0;
			$items->date_create = dol_now();
			$items->status=0;
			$fk_item = $items->create($user);
			if ($fk_item>0)
			{
				$items->ref = '(PROV)'.$items->id;
				$resup = $items->update($user);
				if ($resup <=0)
				{
					$error++;
					$action = 'viewit';
					setEventMessages($itemstmp->error,$itemstmp->errors,'errors');
				}
			}
			else
			{
				$error++;
				$action = 'viewit';
				setEventMessages($items->error,$items->errors,'errors');
			}
			if ($fk_item<=0) $fk_item = 0;
		}
		else
		{
			$label = ($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
		}
	}
}
}

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);
// Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//$objectdetn = new Budgettaskext($db);
	//clone budget
	$aSt = array();
	include DOL_DOCUMENT_ROOT.'/budget/budget/include/crud.inc.php';

	// Part to create upload
	if ($action == 'veriffile')
	{

		//verificacion
		$nombre_archivo = $_FILES['archivo']['name'];
		$tipo_archivo = $_FILES['archivo']['type'];
		$tamano_archivo = $_FILES['archivo']['size'];
		$tmp_name = $_FILES['archivo']['tmp_name'];
		$separator = GETPOST('separator','alpha');


		$tempdir = $conf->budget->dir_output."/tmp/";
		if (! file_exists($tempdir))
		{
			if (dol_mkdir($tempdir) < 0)
			{
				echo ' no se creo ';
				setEventMessages($langs->trans('ErrorCanNotCreateDir'),null,'errors');
				$error++;
			}
		}
		//compruebo si la extension es correcta
		if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
		{

		//  echo "file uploaded<br>";
		}
		else
		{
			setEventMessages($langs->trans('Cannotmovethefile'),null,'errors');
			$error++;
		}

		$objPHPExcel = new PHPExcel();
		if ($type==1)
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		else
			$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($tempdir.$nombre_archivo);

		//$objReader = new PHPExcel_Reader_Excel2007();
		$objReader->setReadDataOnly(true);
		//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');

		$aCurren = array();

		$nLoop = 30;

		$line=1;
		$nLimitempty = 3;
		$nLimit = 1;
		$lLoop = true;
		$aHeaders = array();
		if ($selrow)
		{
			for ($a = 1; $a <= $nLoop; $a++)
			{
				$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getValue();
				if (!empty($dato))
				{
					$aHeaders[$a]=$dato;
				}
			}
			$line++;
		}

		while ($lLoop == true)
		{
			if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getValue()))
			{
				$nLimit=1;
				for ($a = 1; $a <= $nLoop; $a++)
				{
					$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
				}
			}
			else
			{
				if ($nLimit > $nLimitempty)
					$lLoop = false;
				else
					$nLimit++;
			}
			$line++;
		}
		$aNewdata = array();
		$aDate = $aDateimport;


		//$tempdir = DOL_DOCUMENT_ROOT."/budget/tmp/";

		//    if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
		//if($tmp_name && dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo,1,10,0,$nombre_archivo))
		//{
		//	//echo "file uploaded<br>";
		//}
		//else
		//{
		//	echo 'no se puede mover';
		//	exit;
		//}

		if (file_exists($tempdir.$nombre_archivo))
		{
			$csvfile = $tempdir.$nombre_archivo;
			$fh = fopen($csvfile, 'r');
			$headers = fgetcsv($fh);
			$aHeaders = explode($separator,$headers[0]);
			$data = array();
			$aData = array();
			while (! feof($fh))
			{
				$row = fgetcsv($fh,'','^');
				if (!empty($row))
				{
					$aData = explode($separator,$row[0]);
					$obj = new stdClass;
					$obj->none = "";
					foreach ($aData as $i => $value)
					{
						$key = $aHeaders[$i];
						if (!empty($key))
							$obj->$key = $value;
						else
							$obj->none = $value." xx";
					}
					$data[] = $obj;
				}
			}
			fclose($fh);
			$c=0;
			$action = "viewgr";
			$subaction = "verifup";
		}
		else
		{
			echo 'no existe el archivo';
			exit;
		}
	}

	if ($action == 'addup')
	{
		//MODIFICADO
		$error = 0;
		//buscamos el projet
		$res = $object->fetch($id);
		if ($res<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		//recuperamos el aTasknumref
		$aTasknumref = unserialize($_SESSION['aTasknumref'][$object->id]);
		//revisamos si existe tareas en el proyecto

		$aArrData = $_SESSION['aArrData'];
		$table = GETPOST('table');
		$aNewTask = array();
		$aLevel = array();
		$lUtility = true;
		$db->begin();
		foreach ((array) $aArrData AS $i => $data)
		{
			//vamos verificando la existencia de cada uno
			$fk_task_parent = 0;
			if (!empty($data['hilo']))
			{
				if (!empty($aNewTask[$data['hilo']])) $fk_task_parent = $aNewTask[$data['hilo']];
				else $error++;
			}

			//unit
			$fk_unit = 0;
			if (!empty($data['unit']))
			{
				$cunits = fetch_unit('',$data['unit']);
				if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
				{
				//recuperamos el id de registro
					$fk_unit = $cunits->rowid;
				}
				else
				{
				//creamos
				//$cunits->initAsSpecimen();
				//$cunits->code= $data['unit'];
				//$cunits->label= $data['unitlabel'];
				//$cunits->short_label= $data['unit'];
				//$cunits->active= 1;
				//$resunit = $cunits->create($user);
				//if ($resunit >0) $fk_unit = $resunit;
				//else $error++;
					$error++;
					setEventMessages($langs->trans('Error, no existe la unidad de medida').' <b>'.$data['unit'].'</b>, '.$langs->trans('revise'),null,'errors');
				}
			}

			//verificamos las fechas
			$date_start = getformatdate($seldate,$data['fechaini']);
			$date_end   = getformatdate($seldate,$data['fechafin']);

			//buscamos si existe la tarea
			//$objectdet = new Taskext($db);
			$filterstatic = " AND t.ref = '".$data['ref']."'";
			$filterstatic.= " AND t.fk_budget = ".$id;
			$res = $objectdet->fetchAll('', '', 0, 0, array(1=>1), 'AND',$filterstatic,true);
			if ($res > 1)
			{
				$error++;
				setEventMessages($langs->trans('Error, existe mas de un item'),null,'errors');
			}
			elseif ($res==1)
			{
				if (STRTOUPPER($objectdet->ref) == STRTOUPPER($data['ref']) && $objectdet->fk_budget == $id)
				{
					$task = new Budgettaskext($db);
					if ($task->fetch($objectdet->id)>0)
					{
						//buscamos si existe el campo con formato fecha
						foreach((array) $aCampodate AS $k => $value)
						{
							if($data[$k])
							{
								//verificamos y damos formato a la variable
								$resvalue = convertdate($aDatef,$seldate,$data[$k]);
								$task->$value = $resvalue;
							}
						}

						$aNewTask[$data['ref']] = $task->id;
						//actualizamos el valor
						$_POST['options_c_grupo'] = $data['group'];
						$_POST['options_c_view'] = $data['view'];
						$_POST['options_unit_program'] = $data['unitprogram'];
						$_POST['options_fk_unit'] = $fk_unit;
						$_POST['options_unit_amount'] = $data['price'];
						//$task->dateo = $date_start;
						//$task->datee = $date_end;
						$task->fk_task_parent = $fk_task_parent +0;
						$task->ref = $data['ref'];
						$task->label = $data['label'];
						$task->description = $data['detail'];
						$task->priority = $data['priority']+0;
						$task->rang = $i;
						$task->tms = dol_now();
						if (empty($fk_task_parent)) $level = 0;
						else $level = $aLevel[$fk_task_parent]+1;
						$aLevel[$task->id] = $level;
						// Fill array 'array_options' with data from add form
						//$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
						$aTasknumref[$task->id] = array('fk_task_parent'=>$fk_task_parent,'ref'=> $task->ref,'level'=>$level,'reg'=>$i,'group'=>$data['group']);
						//if (!$ret > 0) $error++;
						//actualizamos datos adicionales de la tarea
						$res = $objectdetadd->fetch('',$task->id);
						if ($res>0 && $objectdetadd->fk_budget_task == $task->id)
						{
							$objectdetadd->fk_item = $fk_item;
							$objectdetadd->fk_type = $fk_type_item+0;
							$objectdetadd->c_grupo = $data['group'];
							$objectdetadd->level = $level;
							$objectdetadd->unit_budget = $data['unitprogram']+0;
							$objectdetadd->fk_unit = $fk_unit;
							$objectdetadd->unit_amount = $data['price']+0;
							$objectdetadd->total_amount = 0;
							$objectdetadd->fk_user_mod = $user->id;
							$objectdetadd->tms = dol_now();
							$objectdetadd->detail_close = '';
							$res = $objectdetadd->update($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');

							}
						}
						else
						{
							$objectdetadd->fk_budget_task = $task->id;
							$objectdetadd->fk_item = $fk_item;
							$objectdetadd->fk_type = $fk_type_item+0;
							$objectdetadd->c_grupo = $data['group'];
							$objectdetadd->level = $level;
							$objectdetadd->unit_budget = $data['unitprogram']+0;
							$objectdetadd->fk_unit = $fk_unit;
							$objectdetadd->unit_amount = $data['price']+0;
							$objectdetadd->total_amount = ($data['price']+0)*($data['unitprogram']+0);
							$objectdetadd->fk_user_create = $user->id;
							$objectdetadd->fk_user_mod = $user->id;
							$objectdetadd->date_create = dol_now();
							$objectdetadd->tms = dol_now();
							$objectdetadd->status = 1;
							$res = $objectdetadd->create($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
							}
						}
						if (!$error)
						{

							$resup = $task->update($user,true);
							if ($resup<=0)
							{
								$error++;
								setEventMessages($task->error,$task->errors,'errors');
							}
						}
					}
				}

			}
			else
			{
			//creamos nuevo
				$_POST['options_c_grupo'] = $data['group'];
				$_POST['options_c_view'] = $data['view'];
				$_POST['options_unit_program'] = $data['unitprogram'];
				$_POST['options_fk_unit'] = $fk_unit;
				$_POST['options_unit_amount'] = $data['price'];
				$task = new Budgettaskext($db);
				$task->initAsSpecimen();
			//buscamos si existe el campo con formato fecha
				foreach((array) $aCampodate AS $k => $value)
				{
					if($data[$k])
					{
					//verificamos y damos formato a la variable
						$resvalue = convertdate($aDatef,$seldate,$data[$k]);
						$task->$value = $resvalue;
					}
				}

				$task->entity = $conf->entity;
				$task->fk_budget = $id;
				$task->fk_task = 0;
				$task->fk_task_parent = $fk_task_parent +0;
				$task->ref = $data['ref'];
				$task->label = $data['label'];
				$task->description = $data['detail'];
				$task->fk_user_creat = $user->id;
				$task->fk_user_valid = 0;
				$task->progress = 0;
				$task->duration_effective = 0;
				$task->planned_workload = 0;
				$task->priority = $data['priority']+0;
				$task->fk_statut = 1;
				$task->rang = 0;
				$task->date_c = dol_now();
				$task->tms = dol_now();

			// Fill array 'array_options' with data from add form
			//$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
			//echo '<hr>new '.
				$result = $task->create($user,1);
				if ($result<=0)
				{
					$error++;
					setEventMessages($task->error,$task->errors,'errors');
				}
				if ($result>0)
				{
					if (empty($fk_task_parent))
						$level = 0;
					else
						$level = $aLevel[$fk_task_parent]+1;
					$aLevel[$result] = $level;
				}
				$aTasknumref[$result] = array('fk_task_parent'=>$fk_task_parent,'ref'=>$data['ref'],'level'=>$level,'reg'=>$i,'group'=>$data['group']);

				if (!$error)
				{
					$objectdetadd->fk_budget_task = $result;
					$objectdetadd->c_grupo = $data['group'];
					$objectdetadd->level = $level;
					$objectdetadd->unit_budget = $data['unitprogram']+0;
					$objectdetadd->fk_item = $fk_item;
					$objectdetadd->fk_type = $fk_type_item+0;
					$objectdetadd->fk_unit = $fk_unit;
					$objectdetadd->complementary = 0;
					$objectdetadd->unit_amount = $data['price']+0;
					$objectdetadd->total_amount = ($data['price']+0)*($data['unitprogram']+0);
					$objectdetadd->fk_user_create = $user->id;
					$objectdetadd->fk_user_mod = $user->id;
					$objectdetadd->date_create = dol_now();
					$objectdetadd->tms = dol_now();
					$objectdetadd->status = 1;
				//echo '<br>newadd '.
					$res = $objectdetadd->create($user);
					if ($res<=0)
					{
						$error++;
						setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
					}
				}

				if (!$error)
				{
					//actualizamos la nueva tarea en el campo rang
					$objectdettmp->fetch($result);
					if ($objectdettmp->id == $result)
					{
						$objectdettmp->rang = $i;
						$res = $objectdettmp->update($user,true);
						if ($res<=0)
						{
							$error++;
							setEventMessages($objectdettmp->error,$objectdettmp->errors,'errors');
						}
					}

					$aNewTask[$data['ref']] = $result;

					//buscamos al usuario contacto que es un array
					$aLogin = explode(';',$data['login']);
					foreach ((array) $aLogin AS $l => $login)
					{
						$resuser = $objuser->fetch('',$login);
						if ($resuser>0)
							$result = $task->add_contact($objuser->id, 'TASKEXECUTIVE', 'internal');
					}
				}
				else
				{
					$error++;
					setEventMessages($task->error,$task->errors,'errors');
				}
			}
		}

		if (empty($error))
		{
			//ordenamos y actualizamos
			$aRef = array();
			$aNumberref = array();
			$aRefnumber = array();
			foreach((array) $aTasknumref AS $i => $data)
			{
			//verificamos el orden donde debe estar la tarea
				list($aRef,$aNumberref,$aRefnumber) = get_orderlastnew($i,$object->id,$data,$aRef,$aNumberref,$aRefnumber);
			}
			foreach ((array) $aNumberref AS $i => $value)
			{
				$objectdetadd->fetch('',$i);
				if ($objectdetadd->fk_budget_task == $i)
				{
				//echo '<br>'.$i.' '.$value;
					$objectdetadd->order_ref = $value;
					$res = $objectdetadd->update_orderref();
					if ($res < 0)
					{
						$error++;
						setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
					}
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('No existe el registro'),null,'errors');
				//echo '<br>no encuentra '.$i;
				}
			}
		}

		//ordenamos las tareas por el order_ref
		if (empty($error))
		{
			//echo '<hr>antes de actualizar el order '.$error;
			$objectdetaddtmp->get_ordertask($object->id);
			$objectdet = new Budgettaskext($db);
			//echo '<br>cuentalines '.count($taskadd->lines).' del id '.$projectstatic->id;
			if (count($objectdetaddtmp->lines)>0)
			{
				$j = 1;
				foreach($objectdetaddtmp->lines AS $i => $data)
				{
					$fk = $data->id;
					$res = $objectdet->fetch($fk);
					if ($res >0)
					{
						//echo '<br>procesando el reemplazo a '.$j .' de '.$taskstatic->rang.' delid '.$data->id.'|'.$fk.' encontrado |'.$taskaddnew->id.'|';
						$objectdet->rang = $j;
						$res = $objectdet->update_rang($user);
						if ($res <= 0)
						{
							$error++;
							setEventMessages($objectdet->error,$objectdet->errors,'errors');
						}
						$j++;
					}
					else
					{
						$error++;
						setEventMessages($objectdet->error,$objectdet->errors,'errors');
					}
				}
			}
		}

		if (empty($error))
		{
			$db->commit();
		}
		else
		{
			//setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
			setEventMessages($langs->trans("Errorupload").' '.$error,null,'errors');
			$db->rollback();
		}
		$action = 'viewgr';
		//echo '<br>action '.$action;
	}

	if ($action == 'process')
	// En get ou en post
	{
		$object->fetch($id);
		if (empty($object->id))
			$object->id = $id;
		$object->fetch_thirdparty();
		$seltype = GETPOST('seltype');

		$object->fetch_lines(1,$aStrbudget,$seltype);

		if ($seltype=='MA'||$seltype=='MO'||$seltype=='MQ')
		{
			$lines = array();
			$aLine = array();

			//generamos un array de resumen
			foreach ($object->linesres AS $i => $row)
			{
				$linesitem = $object->lines[$i];
				foreach ($row AS $j => $obj)
				{
					//echo '<hr>prod '.$obj->fk_product_budget.' quant '.$obj->quant.' * '.$linesitem->unit_budget;
					$aLine[$obj->fk_product_budget]['quant']+=$obj->quant*$linesitem->unit_budget;
					//unidad de medida
					$objbtr = new Budgettaskresourceext($db);
					$objbtr->fetch($obj->id);
					$unit = $langs->trans($objbtr->getLabelOfUnit('short'));
					$aLine[$obj->fk_product_budget]['unit']=$unit;
					$aLine[$obj->fk_product_budget]['amount']=$obj->amount;
					$aLine[$obj->fk_product_budget]['label']=$obj->detail;
					$aLine[$obj->fk_product_budget]['unititem']=$linesitem->unit_budget;
				}
			}

			foreach ($aLine AS $fk_product_budget => $data)
			{
				$line = new BudgettaskresourceLine($db);
				$line->fk_product_budget = $fk_product_budget;
				$line->unit = $data['unit'];
				$line->label = $data['label'];
				$line->unit_budget = $data['quant'];
				$line->unit_amount = $data['amount'];
				$lines[] = $line;
			}

		}
		elseif($seltype == 'RUB')
		{
			$lines = array();
			$aLine = array();

			$aGroup = $aStrbudget[$id]['aStrcatgroup'];
			//generamos un array de resumen
			$linesarray = $object->lines;
			if (count($linesarray)>0)
			{
				foreach ($linesarray AS $i => $line)
				{
					//echo '| '.$i.' -> '.$line->id.' '.$line->label;
					$linesres = $object->linesres[$i];
					if (count($linesres)>0)
					{
						foreach ($linesres AS $j => $obj)
						{
							//echo '<hr>id '.$line->id .' == '.$obj->fk_budget_task;
							$aLine[$line->id][$aGroup[$obj->code_structure]]['quant']+=$obj->quant*$line->unit_budget;
							$aLine[$line->id][$aGroup[$obj->code_structure]]['amounttot']+=$obj->quant*$line->unit_budget*$obj->amount;
							$aLine[$line->id][$aGroup[$obj->code_structure]]['amount']=$obj->amount;
							$aLine[$line->id][$aGroup[$obj->code_structure]]['label']=$line->label;
							$aLine[$line->id][$aGroup[$obj->code_structure]]['unititem']=$line->unit_budget;
						}
					}
					else
					{
						foreach ($aGroup AS $k => $code)
						{
							$aLine[$line->id][$code]['quant']+=0;
							$aLine[$line->id][$code]['amount']=0;
							$aLine[$line->id][$code]['amounttot']=0;
							$aLine[$line->id][$code]['label']=$line->label;
							$aLine[$line->id][$code]['unititem']=$line->unit_budget;
						}
					}
				}
			}

			foreach ($aLine AS $fk_budget_task => $row)
			{
				$line = new Budgettaskext($db);
				$line->fk_budget_task = $fk_budget_task;
				foreach ($row AS $group => $data)
				{
					$line->unit = $data['unit'];
					$line->label = $data['label'];
					if ($group == 'MA')
						$line->ma = $data['amounttot'];
					if ($group == 'MO')
						$line->mo = $data['amounttot'];
					if ($group == 'MQ')
						$line->mq = $data['amounttot'];

				}
				$lines[] = $line;
			}
		}
		elseif ($seltype == 'PU')
		{
			$objectdet->fetch($idr);
			$res = $objectdetadd->procedure_calc($id,$idr,$rep=false);
			$lines = $objectdetadd->aLine;


		}
		else
			$lines = $object->lines;

		$title = '';
		if ($seltype == 'general') $title = $langs->trans('Budgetgeneral');
		if ($seltype == 'MA') $title = $langs->trans('Desglose de insumos general: MATERIALES');
		if ($seltype == 'MO') $title = $langs->trans('Desglose de insumos general: MANO DE OBRA');
		if ($seltype == 'MQ') $title = $langs->trans('Desglose de insumos general: MAQUINARIA');
		if ($seltype == 'RUB') $title = $langs->trans('Presupuesto por Rubros');
		if ($seltype == 'PU') $title = $langs->trans('Priceunits');

		$_SESSION['linesrep'][$object->id] = array('lines'=>$lines,'title'=>$title,'seltype'=>$seltype,'ref'=>$objectdet->ref,'labelitem'=> $objectdet->label);
		if (GETPOST('model'))
		{
			$object->setDocModel($user, GETPOST('model'));
		}
		else
		{
			if ($seltype == 'PU')
				$object->modelpdf = 'fractalpu';
			else
				$object->modelpdf = 'fractalgeneral';
		}
		// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$result=budget_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
			exit;
		}
	}

	if ($action == 'builddoc')	// En get ou en post
	{
		$object->fetch($id);
		if (empty($object->id))
			$object->id = $id;
		$object->fetch_thirdparty();
		$object->fetch_lines(1);
		if (GETPOST('model'))
		{
			$object->setDocModel($user, GETPOST('model'));
		}

		// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$result=budget_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
			exit;
		}
	}
	if ($action != 'create')
	{
		//vamos a actualizar al ingresar cada uno de los items
		if ($object->fk_statut==0)
		{
			$filter = " AND t.fk_budget = ".$id;
			$filter.= " AND t.fk_task_parent = 0";
			$res = $objectdet->fetchAll('','',0,0,array(),'AND',$filter);
			if ($res >0)
			{
				$lines = $objectdet->lines;
				foreach ($lines AS $j => $line)
				{
					$idsearchup = $line->id;
					$sumBudget = $objectdetaddtmp->procedure_calculo_group($user,$id,$idsearchup,true);
					$aSumitem = $objectdetaddtmp->aSumitem;
					//vamos a actualizar
					$db->begin();
					$sum=0;
					foreach ((array) $aSumitem[$id] AS $fk => $value)
					{
						//buscamos
						$res = $objectdetadd->fetch(0,$fk);
						if ($res==1)
						{
							$objectdetadd->total_amount = price2num($value,$general->decimal_total);
							$res = $objectdetadd->update($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
							}
						}
					}
					if (!$error) $db->commit();
					else $db->rollback();
				}
			}
		}
		//vamos a actualizar el proyecto si esta en status 0
		if ($id && $object->fk_statut==0)
		{
			$total = $objectdetadd->procedure_calculo_budget($id);
			$object->budget_amount = price2num($total,$general->decimal_total);
			$resup = $object->update($user);
			if ($resup<=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
	}
}
//armamos las regiones en un array
$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$linesclassfin = $objCclasfin->lines;
	foreach ($linesclassfin AS $j => $line)
		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$form = new Formv($db);
$formfile = new FormFile($db);
$formcompany = new FormCompany($db);

//llxHeader('','MyPageName','');
//$morejs = array('/budget/js/bootstrap.min.js');
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

// Part to create
if ($action == 'create')
{
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () { 	$("#selectcountry_id").change(function() { document.formsoc.action.value="create"; 	document.formsoc.submit(); 	});
		$("#state_id").change(function() { 	document.formsoc.action.value="create"; document.formsoc.submit(); 	}); });';
		print '</script>'."\n";
	}
	print load_fiche_titre($langs->trans("Newbudget"));

	dol_htmloutput_events();

        // We set country_id, country_code and country for the selected country
	$object->fk_country=GETPOST('country_id')?GETPOST('country_id'):$mysoc->country_id;
	$object->fk_departament=GETPOST('state_id')?GETPOST('state_id'):$mysoc->state_id;
	$object->fk_city=GETPOST('fk_city')?GETPOST('fk_city'):$mysoc->fk_city;
	if ($object->fk_country)
	{
		$tmparray=getCountry($object->fk_country,'all');
		$object->country_code=$tmparray['code'];
		$object->country=$tmparray['label'];
	}


	print '<form id="formsoc" name="formsoc" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td><input class="flat" type="text" name="title" value="'.GETPOST('title').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_sector").'</td><td>';
	print $form->selectarray('fk_sector',$aInstitutional,GETPOST('fk_sector'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td>';
	$filterstatic = "";
	$typestr->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filterstatic);
	print $typestr->putype_select(GETPOST('type_structure'),'type_structure','',0,$campo='code');
	print '</td></tr>';

        // Country
	print '<tr><td class="fieldrequired" width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
	print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$mysoc->country_id));
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>';

        // State
	if (empty($conf->global->SOCIETE_DISABLE_STATE))
	{
		print '<tr><td class="fieldrequired">'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
		if ($object->fk_country) print $formcompany->select_state(GETPOST('state_id'),$object->country_code);
		else print $countrynotdefined;
		print '</td></tr>';
	}

	//city
	$filtercity = " AND t.fk_country = ".$object->fk_country;
	$filtercity.= " AND t.fk_departement = ".(GETPOST('state_id')?GETPOST('state_id'):$object->fk_departement);
	$rescity = $objcity->fetchAll('ASC', 't.label', 0,0,array(1=>1), 'AND',$filtercity);
	if (!$rescity)
	{
		print '<tr><td>'.$langs->trans("City").'</td><td><input class="flat" type="text" name="labelcity" value="'.GETPOST('labelcity').'">';
		print '&nbsp<input class="flat" type="text" name="refcity" size="5" maxlength="5" value="'.GETPOST('refcity').'" placeholder="'.$langs->trans('Codecity').'">';
		print '</td></tr>';
	}
	else
	{
		$options = '<option value="0">'.$langs->trans('Select').'</option>';
		foreach ($objcity->lines AS $j => $linecity)
		{
			$options.= '<option value="'.$linecity->id.'" '.($object->fk_city == $linecity->id?'selected':'').'>'.$linecity->label.'</option>';
		}
		print '<tr><td class="fieldrequired">'.$langs->trans("City").'</td><td>';
		print '<select name="fk_city">'.$options.'</select>';
		print '&nbsp<input class="flat" type="text" name="labelcity" value="'.GETPOST('labelcity').'" placeholder="'.$langs->trans('Newcity').'">';
		print '&nbsp<input class="flat" type="text" name="refcity" size="5" maxlength="5" value="'.GETPOST('refcity').'" placeholder="'.$langs->trans('Codecity').'">';
		print '</td></tr>';


	}

	//mostramos o no el tipo de presupuesto data_type
	if (count($aDatatype)>1)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Datatype").'</td><td>';
		print $form->selectarray('data_type',$aDatatype,(GETPOST('data_type')?GETPOST('data_type'):0));
		print '</td></tr>';
	}
	else
	{
		print '<input type="hidden" name="data_type" value="0">';
	}

	//mostramos el calendario base
	print '<tr><td class="fieldrequired">'.$langs->trans("Basecalendar").'</td><td>';
	$filtercalendar = " AND t.entity = ".$conf->entity;
	$res = $objcalendar->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filtercalendar);
	$options = '<option value="0">'.$langs->trans('Select').'</option>';
	foreach ($objcalendar->lines AS $j => $line)
	{
		$options.= '<option value="'.$line->id.'">'.$line->label.'</option>';
	}
	print '<select name="fk_calendar">'.$options.'</select>';
	print '</td></tr>';

	//mostramos la fecha de inicio
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate_ini").'</td><td>';
	print $form->select_date(dol_now(), 'do_', 0,0,0,"",1,0,0,0,'','','');
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}


// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	$form=new Formv($db);
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () { 	$("#selectcountry_id").change(function() { document.formsoc.action.value="edit"; document.formsoc.submit(); }); $("#state_id").change(function() { 	document.formsoc.action.value="edit"; document.formsoc.submit(); }); });';
		print '</script>'."\n";
	}

	print load_fiche_titre($langs->trans("Budget"));
	dol_htmloutput_events();

        // We set country_id, country_code and country for the selected country
	$object->fk_country=GETPOST('country_id')?GETPOST('country_id'):$object->fk_country;
	$object->fk_departament=GETPOST('state_id')?GETPOST('state_id'):$object->fk_departament;
	$object->fk_city=GETPOST('fk_city')?GETPOST('fk_city'):$object->fk_city;
	if ($object->fk_country)
	{
		$tmparray=getCountry($object->fk_country,'all');
		$object->country_code=$tmparray['code'];
		$object->country=$tmparray['label'];
	}

	print '<form id="formsoc" name="formsoc" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td><input class="flat" type="text" name="title" value="'.$object->title.'"></td></tr>';
	print '<tr><td >'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';

	print '<tr><td>'.$langs->trans("Version").'</td><td><input class="flat" type="text" name="version" value="'.(GETPOST('version')?GETPOST('version'):$object->version).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_sector").'</td><td>';
	print $form->selectarray('fk_sector',$aInstitutional,(GETPOST('fk_sector')?GETPOST('fk_sector'):$object->fk_sector),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td>';
	$filterstatic = "";
	$typestr->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filterstatic);
	print $typestr->putype_select($object->type_structure,'type_structure','',0,$campo='code');
	print '</td></tr>';

        // Country
	print '<tr><td width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
	print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$object->fk_country));
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>';

        // State
	if (empty($conf->global->SOCIETE_DISABLE_STATE))
	{
		print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
		if ($object->fk_country) print $formcompany->select_state((GETPOST('state_id')?GETPOST('state_id'):$object->fk_departement),$object->country_code);
		else print $countrynotdefined;
		print '</td></tr>';
	}

	//city
	$filtercity = " AND t.fk_country = ".$object->fk_country;
	$filtercity.= " AND t.fk_departement = ".(GETPOST('state_id')?GETPOST('state_id'):$object->fk_departement);
	$rescity = $objcity->fetchAll('ASC', 't.label', 0,0,array(1=>1), 'AND',$filtercity);
	if (!$rescity)
	{
		print '<tr><td>'.$langs->trans("City").'</td><td><input class="flat" type="text" name="labelcity" value="'.GETPOST('labelcity').'">';
		print '&nbsp<input class="flat" type="text" name="refcity" size="5" maxlength="5" value="'.GETPOST('refcity').'" placeholder="'.$langs->trans('Codecity').'">';
		print '</td></tr>';
	}
	else
	{
		$options = '<option value="0">'.$langs->trans('Select').'</option>';
		foreach ($objcity->lines AS $j => $linecity)
		{
			$options.= '<option value="'.$linecity->id.'" '.($object->fk_city == $linecity->id?'selected':'').'>'.$linecity->label.'</option>';
		}
		print '<tr><td class="fieldrequired">'.$langs->trans("City").'</td><td>';
		print '<select name="fk_city">'.$options.'</select>';
		print '&nbsp<input class="flat" type="text" name="labelcity" value="'.GETPOST('labelcity').'" placeholder="'.$langs->trans('Newcity').'">';
		print '&nbsp<input class="flat" type="text" name="refcity" size="5" maxlength="5" value="'.GETPOST('refcity').'" placeholder="'.$langs->trans('Codecity').'">';
		print '</td></tr>';
	}

	//mostramos o no el tipo de presupuesto data_type
	if (count($aDatatype)>1)
	{
		print '<tr><td>'.$langs->trans("Datatype").'</td><td>';
		print $form->selectarray('data_type',$aDatatype,(GETPOST('data_type')?GETPOST('data_type'):$object->data_type));
		print '</td></tr>';
	}
	else
	{
		print '<input type="hidden" name="data_type" value="'.$object->data_type.'">';
	}

	//mostramos el calendario base
	print '<tr><td class="fieldrequired">'.$langs->trans("Basecalendar").'</td><td>';
	$filtercalendar = " AND t.entity = ".$conf->entity;
	$res = $objcalendar->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filtercalendar);
	$options = '<option value="0">'.$langs->trans('Select').'</option>';
	foreach ($objcalendar->lines AS $j => $line)
	{
		$selected = '';
		if ($object->fk_calendar == $line->id) $selected = ' selected';
		$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
	}
	print '<select name="fk_calendar">'.$options.'</select>';
	print '</td></tr>';

	//mostramos la fecha de inicio
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddate_ini").'</td><td>';
	print $form->select_date($object->dateo, 'do_', 0,0,0,"",1,0,0,0,'','','');
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}


// Part to show record
//if ($id && (empty($action) || $action == 'viewit' || $action == 'creategr' || $action == 'viewgr' || $action == 'view' || $action == 'delete' || $action=='editres' || $action=='edititem' || $action=='viewre' || $action == 'confimportresource'))
if ($id > 0 && $action != 'edit' && $action != 'create')
{

	dol_htmloutput_events();
	$idr = GETPOST('idr','int');
	if (($idg || $idr) && ($action == 'viewit' || $action == 'pdfitem' || $action == 'editres'))
	{
		$objectdet->fetch(($idr?$idr:$idg));
		//$objectdetadd->fetch(0,($idr?$idr:$idg));
		$objectdetadd->fetch(0,$objectdet->id);
		$head = budget_task_prepare_head($objectdet, $user);
		$titre=$langs->trans("Home");
		$picto='budget';
		$getcard = 'b'.$objectdet->id;
		dol_fiche_head($head, $getcard, $titre, 0, $picto);
	}
	else
	{
		$head = budget_prepare_head($object, $user);
		$titre=$langs->trans("Budget");
		$picto='budget';
		$getcard = 'card';
		dol_fiche_head($head, $getcard, $titre, 0, $picto);
	}
	if ($action == 'exportresourcetmp') {
								//$aType=array(0=>1,1=>2);
		$aType= array(/*0=>$langs->trans('All'),*/'MO'=>$langs->trans('Workforce'),'MA'=>$langs->trans('Material'),'MQ'=>$langs->trans('Machineryandequipment'));
		$formquestion = array(array('type'=>'select','label'=>$langs->trans('Type'),'name'=>'type','values'=>$aType));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Budget'), $langs->trans('ConfirmExportbudget'), 'exportresource', $formquestion, 1, 2);
		print $formconfirm;
	}
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteBudget'), $langs->trans('ConfirmDeleteBudget'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'validate') {
		$formquestion = array(array('type'=>'other','label'=>$langs->trans('Message'),'value'=>$langs->trans('Al validar, la versión cambiará a 0')));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ValidateBudget'), $langs->trans('ConfirmValidateBudget'), 'confirm_validate', $formquestion, 0, 2);
		print $formconfirm;
	}
	if ($action == 'deleteitem') {
		$objectdet->fetch(GETPOST('idr'));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.GETPOST('idr'), $langs->trans('DeleteItem'), $langs->trans('ConfirmDeleteItem').' '.$objectdet->ref.' '.$objectdet->label, 'confirm_deleteitem', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'deletegroup') {
		$objectdet->fetch(GETPOST('idr'));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.GETPOST('idr'), $langs->trans('DeleteGroup'), $langs->trans('ConfirmDeleteGroup').' '.$objectdet->ref.' '.$objectdet->label, 'confirm_deletegroup', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'confclon') {
		$formquestion = array(array('type'=>'text','label'=>$langs->trans('Newversion'),'size'=>5,'name'=>'version','value'=>$object->version));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Clonebudget'), $langs->trans('ConfirmCloneBudget').': '.$object->ref, 'confirm_clon', $formquestion, 0, 1);
		print $formconfirm;
	}
	if ($action == 'clonitem') {
		$objectdet->fetch(GETPOST('idr'));
		$formquestion = array(array('type'=>'text','label'=>$langs->trans('Newitem'),'size'=>40,'name'=>'label','value'=>$objectdet->label));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.$objectdet->id, $langs->trans('Cloneitem'), $langs->trans('ConfirmCloneItem').': '.$objectdet->label, 'confirm_clonitem', $formquestion, 0, 1);
		print $formconfirm;
	}



	print '<table class="noborder centpercent">'."\n";
	if ($action != 'editres' && $action != 'viewit' && $action != 'viewgr'&& $action != 'creategr' && $action != 'edititem' && $action != 'editgroup' && $action != 'viewre' && $action != 'gen' && $action != 'pdfitem' && $action !='deleteitem' && $action != 'viewcalendar' && $action!='viewtask')
	{

		print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldtitle").'</td><td>'.$object->title.'</td></tr>';
		//print '<tr><td>'.$langs->trans("Fielddescription").'</td><td>'.$object->description.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldversion").'</td><td>'.$object->version.'</td></tr>';
		$typestr->fetch(0,$object->type_structure);
		print '<tr><td>'.$langs->trans("Fieldstructure").'</td><td>'.$typestr->getNomUrl(1).'</td></tr>';
		$resc = $objCclasfin->fetch($object->fk_sector);
		if ($resc==1)
			print '<tr><td>'.$langs->trans("Fieldfk_sector").'</td><td>'.$objCclasfin->getNomUrl(1).'</td></tr>';

		if ($object->fk_country)
		{
			$tmparray=getCountry($object->fk_country,'all');
			$object->country_code=$tmparray['code'];
			$object->country=$tmparray['label'];
		}

		print '<tr '.$bc[$var].'><td>'.$langs->trans("Country").'</td><td>';
		if ($object->country_code)
		{
			$img=picto_from_langcode($object->country_code);
			print $img?$img.' ':'';
			print getCountry($object->country_code,1);
		}
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("Province").'</td><td>';
		$objcity->fetch($object->fk_city);
		$city = '';
		if (!empty($objcity->id)) $city = $objcity->label;
		if (! empty($object->fk_departement))
		{
			print getState($object->fk_departement).' <b>'.$langs->trans('City').':</b> '.$city;
		}
		else
		{
			if ($city)
				print $langs->trans('City').' '.$city;
		}
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("Datatype").'</td><td>';
		print $aDatatype[$object->data_type];
		print '</td></tr>';

		//mostramos el calendario base
		print '<tr><td>'.$langs->trans("Basecalendar").'</td><td>';
		$objcalendar->fetch($object->fk_calendar);
		if ($objcalendar->id == $object->fk_calendar)
			print $objcalendar->label;
		else
			print '';
		print '</td></tr>';

		//mostramos la fecha de inicio
		print '<tr><td>'.$langs->trans("Fielddate_ini").'</td><td>';
		print dol_print_date($object->dateo,'day');
		print '</td></tr>';

		$objuser->fetch($object->fk_user_creat);
		//print '<tr><td>'.$langs->trans("Fieldfk_user_creat").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldfk_statut").'</td><td>'.$object->getLibStatut(2).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldbudget_amount").'</td><td>'.price(price2num($object->budget_amount,$general->decimal_total)).'</td></tr>';
	}
	else
	{
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
		//mostramos el grupo o item seleccionado
		if (GETPOST('idr') || GETPOST('idg'))
		{
			$lWriteitem = false;
			//verificamos permisos para la siguiente ventana
			$budgetTaskListId = $objectdet->getBudgetAuthorizedForUser($user,0,0,$socid);
			if ($budgetTaskListId[GETPOST('idg')]) $lWriteitem = true;
			if (!$lWriteitem)
			{
				//vamos a verificar si es item y si tiene un grupo
				if ($objectdet->fk_task_parent && $objectdetadd->c_grupo == 0)
				{
					$objectdettmp->fetch($objectdet->fk_task_parent);
					$budgetTaskListId = $objectdettmp->getBudgetAuthorizedForUser($user,0,0,$socid);
					if ($budgetTaskListId[$objectdet->fk_task_parent]) $lWriteitem = true;
				}
			}
			if ($user->admin) $lWriteitem = true;
			print '<tr>';
			print '<td align="right">'.$objectdet->ref.'</td>';
			print '<td></td>';
			print '<td align="right">'.$objectdet->label.'</td>';
			print '<td align="right">'.price(price2num($objectdetadd->total_amount,$general->decimal_total)).'</td>';
			print '</tr>';
		}
	}
	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	// Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($action != 'deletegroup' && $action != 'deleteitem' &&$action != 'editgroup' && $action != 'edititem' && $action != 'viewit' && $action != 'viewgr' && $action != 'viewre' && $action != 'pdfitem' && $action != 'gen')
		{
			if ($action != 'export' && $action != 'exportresourcetmp' && $action != 'exportpresupgeneral' && $action != 'exportpriceunit' && $action != 'compareitem' && $action != 'compareresource' && $action != 'compare')
			{
				if ($user->rights->budget->budi->exp)
				{
					//print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=desgloce">'.$langs->trans("Brokendown").'</a></div>'."\n";
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=export">'.$langs->trans("Export").'</a></div>'."\n";
				}
				if ($user->rights->budget->bud->val && $object->fk_statut == 0)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
				}
				if ($user->rights->budget->bud->app && $object->fk_statut == 1)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approve">'.$langs->trans("Approve").'</a></div>'."\n";
				}

				if ($user->rights->budget->bud->write && $object->fk_statut == 0)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
				}

				if ($user->rights->budget->bud->del && $object->fk_statut == 0)
				{
					print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
				}

				if ($user->rights->budget->bud->clone)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=confclon">'.$langs->trans("Clone").'</a></div>'."\n";
				}
				//if (empty($action) && $user->rights->budget->budi->leer)
				//{
				//	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=viewgr">'.$langs->trans("Modules").'</a></div>'."\n";
				//}
				if ($user->rights->budget->cale->lire)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=viewcalendar">'.$langs->trans("Calendar").'</a></div>'."\n";
				}
			}
		}

		if ($action == 'viewit' && $abc)
		{
			print '<a data-toggle="modal" href="#presup" class="btn btn-primary btn-large">'.$langs->trans('Selectitem').'</a>';

			print '<div id="presup" class="modal fade in" style="display: none;">';
			print '<div class="modal-dialog">';
			print '<div class="modal-content">';
			print '<div class="modal-header">';
			print '<a data-dismiss="modal" class="close">×</a>';
			print '<h3>'.$langs->trans('Presupuestos').'</h3>';
			print '</div>';
			print '<div class="modal-body">';
			print '<h4>'.$langs->trans('Seleccione los items').'</h4>';
			include DOL_DOCUMENT_ROOT.'/budget/tpl/listp.tpl.php';
			print '</div>';
			print '<div class="modal-footer">';
			print '<a href="index.html" class="btn btn-success">Guardar</a>';
			print '<a href="#" data-dismiss="modal" class="btn">Cerrar</a>';
			print '</div>';
			print '</div>';
			print '</div>';
			print '</div>';
		}
	}
	print '</div>'."\n";

	if ($action == 'export' || $action == 'exportpresupgeneral' || $action == 'exportresourcetmp'|| $action == 'exportpriceunit')
	{

		// Buttons compare
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
		// Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if ($user->rights->budget->budi->exp)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=exportresourcetmp">'.$langs->trans("Supplies").'</a></div>'."\n";
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=exportpresupgeneral">'.$langs->trans("Generalbudget").'</a></div>'."\n";
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=exportpriceunit">'.$langs->trans("Priceunits").'</a></div>'."\n";
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans("Return").'</a></div>'."\n";
			}
		}
		print '</div>';
	}

	if ($action != 'compare')
	{
		//dol_fiche_head();
			//documentos ver
		if (empty($action))
		{
				//	creamos un formulario para generar reporte
			$aType = array(
				'general'=> $langs->trans('Generalbudget'),
				'MA' => $langs->trans('Breakdownofmaterials'),
				'MO' => $langs->trans('Laborbreakdown'),
				'MQ' => $langs->trans('Breakdownofmachineryandequipment'),
				'RUB' => $langs->trans('Budgetbyitems'),
				'PU' => $langs->trans('Priceunits'),
			);

			dol_fiche_head();

			print '<form method="POST">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print '<input type="hidden" name="action" value="process">';
			print '<table>';
			print '<tr><td>'.$langs->trans('Select').'</td><td>';
			print $form->selectarray('seltype',$aType,GETPOST('seltype'));
			print '</td>';
			print '<td>';
			print '<input type="submit" name="sel" value="'.$langs->trans('Generar').'">';
			print '</td>';
			print '</tr>';
			print '</table>';
			print '</form>';
				//documents
			print '<table width="100%"><tr><td width="50%" valign="top">';
			print '<a name="builddoc"></a>';
				 // ancre
			$object->fetch($id);
				// Documents generes
			$filename=dol_sanitizeFileName($object->ref);
				//cambiando de nombre al reporte
			$filedir=$conf->budget->dir_output . '/' . dol_sanitizeFileName($object->ref);
			$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
			$genallowed=$user->rights->budget->pdf->creer;
			$delallowed=$user->rights->budget->pdf->del;
			$genallowed=0;
			$object->model_pdf = 'fractalgeneral';
			print $formfile->showdocuments('budget',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->model_pdf,1,0,0,28,0,'','','',$soc->default_lang);
			$somethingshown=$formfile->numoffiles;
			print '</td></tr></table>';

			dol_fiche_end();
		}

	}

	print '<div class="div-table-responsive">';
	if($action == 'gen')
		include DOL_DOCUMENT_ROOT.'/budget/general/card.php';
	elseif ($action == 'desgloce')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/desgloce.tpl.php';
	elseif ($action == 'pdfitem')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/pdf_item.tpl.php';
	elseif ($action == 'viewgr' && $subaction=='createup')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/uploadtask.tpl.php';
	elseif ($action == 'viewgr' && $subaction =='verifup')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/verifup.tpl.php';
	elseif ($action == 'viewcalendar')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budget_calendar.tpl.php';
	elseif ($action == 'viewtask')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgettask_card.tpl.php';
	elseif ($action == 'compare')
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgettask_compare.tpl.php';
	else
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/group_task.tpl.php';
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
