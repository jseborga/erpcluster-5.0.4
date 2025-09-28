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
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/budget/class/productasset.class.php');

dol_include_once('/categories/class/categorie.class.php');
dol_include_once('/budget/class/puoperatorext.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/pustructuredetext.class.php');
dol_include_once('/budget/class/productbudgetext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
dol_include_once('/budget/class/budgetgeneral.class.php');
dol_include_once('/budget/class/city.class.php');
dol_include_once('/budget/class/budgetconcept.class.php');
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
$result=restrictedArea($user,'budget',$id);

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
if ($user->rights->budget->writem)
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
$objBudgetconcept = new Budgetconcept($db);
$objPustructure = new Pustructureext($db);
$objPustructuredet = new Pustructuredetext($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budget'));
$extrafields = new ExtraFields($db);

//armamos la estructura para futuras acciones
//if (!isset($_SESSION['aStrbudget']))
//{
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

	if ($action == 'spreadsheetprod')
	{

		$dir = $conf->budget->dir_output.'/tmp';
		if (! file_exists($dir))
		{
			if (dol_mkdir($dir) < 0)
			{
				$error++;
				setEventmessages($langs->trans('Error al crear directorio'),null,'errors');
			}
		}

		$aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');



		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("yemer colque")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");

		$aExcelcompare = unserialize($_SESSION['aExcelproductline']);
		$aTitulocompare = unserialize($_SESSION['aTituloproductline']);
		$sumTotal = unserialize($_SESSION['sumTotalline']);
		$nVersion = unserialize($_SESSION['nVersionline']);

		//contar array titulo para alinear el titulo
		$nTit=count($aTitulocompare);

		//TITULO

		$objPHPExcel->setActiveSheetIndex(0);
		$cTitle=$langs->trans("Compareresource");
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
		$sheet->getStyle('A2')->getFont()->setSize(15);


		$sheet->mergeCells('A2:'.$aHeader[$nTit].'2');
		if($yesnoprice)
			$sheet->mergeCells('A2:'.$aHeader[$nTit].'2');
		$sheet->getStyle('A2')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);


		// FILTRAMOS PARA ENCABEZADO

		$res = $object->fetch($id);
		if($res>0)
		{
			$cTitle=$object->title;
			$cRef=$object->ref;
			$cVersion=$object->version;
				//$cAmountpres=$$object->budget_amount;
		}

		// FILTRANDO PARA DECIMALES
		$resge=$general->fetch(0,$id);
		if($resge>0)
		{
			$nMoneda=$general->base_currency;

			$nDecimalpu=$general->decimal_pu;
			$nDecimalquant=$general->decimal_quant;
			$nDecimaltotal=$general->decimal_total;
		}
		else
		{
			$nDecimalpu=6;
			$nDecimalquant=6;
			$nDecimaltotal=6;
		}


		if($resge>0)
		{
			switch ($general->decimal_pu)
			{
				case 0:
				$cNumeropu='';
				break;
				case 1:
				$cNumeropu='0';
				break;
				case 2:
				$cNumeropu='00';
				break;
				case 3:
				$cNumeropu='000';
				break;
				case 4:
				$cNumeropu='0000';
				break;
				case 5:
				$cNumeropu='00000';
				break;
				case 6:
				$cNumeropu='000000';
				break;
				case 7:
				$cNumeropu='0000000';
				break;
				case 8:
				$cNumeropu='00000000';
				break;
			}
			switch ($general->decimal_quant)
			{
				case 0:
				$cNumeroquant='';
				break;
				case 1:
				$cNumeroquant='0';
				break;
				case 2:
				$cNumeroquant='00';
				break;
				case 3:
				$cNumeroquant='000';
				break;
				case 4:
				$cNumeroquant='0000';
				break;
				case 5:
				$cNumeroquant='00000';
				break;
				case 6:
				$cNumeroquant='000000';
				break;
				case 7:
				$cNumeroquant='0000000';
				break;
				case 8:
				$cNumeroquant='00000000';
				break;

			}
			switch ($general->decimal_total)
			{
				case 0:
				$cNumerototal='';
				break;
				case 1:
				$cNumerototal='0';
				break;
				case 2:
				$cNumerototal='00';
				break;
				case 3:
				$cNumerototal='000';
				break;
				case 4:
				$cNumerototal='0000';
				break;
				case 5:
				$cNumerototal='00000';
				break;
				case 6:
				$cNumerototal='000000';
				break;
				case 7:
				$cNumerototal='0000000';
				break;
				case 8:
				$cNumerototal='00000000';
				break;

			}
		}

		// COLOR ENCABEZADO
		$objPHPExcel->getActiveSheet()->getStyle('A3:C5')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0c78bf'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));


		// ENCABEZADO
		// REF
		$objPHPExcel->getActiveSheet()->setCellValue('A3',html_entity_decode($langs->trans("Budget")));
		$objPHPExcel->getActiveSheet()->setCellValue('B3',$cTitle);
		$objPHPExcel->getActiveSheet()->mergeCells('B3:'.$aHeader[$nTit].'3');

		//TITULO
		$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Ref")));
		$objPHPExcel->getActiveSheet()->setCellValue('B4',$cRef);
		// VERSION
		$objPHPExcel->getActiveSheet()->setCellValue('A5',html_entity_decode($langs->trans("Version")));
		$objPHPExcel->getActiveSheet()->setCellValue('B5',$cVersion);


		$aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');


		// TITULO VERSION

		$cont=3;
		for($a=1;$a<=$nVersion;$a++)
		{
			//titulo version de la tabla
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setCellValue('B6',html_entity_decode($langs->trans("Version")));

			//numeros de las versiones
			$sheet = $objPHPExcel->getActiveSheet();
			$sheet->getStyle($aHeader[$cont].'6')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].'6',$a);
			$objPHPExcel->getActiveSheet()->getStyle($aHeader[$cont].'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle($aHeader[$cont].'6')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			$sheet->setCellValueByColumnAndRow(0,6);
			$contaux=$cont+1;
			$sheet->mergeCells($aHeader[$cont].'6'.':'.$aHeader[$contaux].'6');
			$cont=$contaux+1;

		}
		// color de la fila de la version
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.$aHeader[$cont-1].'6')->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'top'     => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation'   => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0'
					),
					'endcolor'   => array(
						'argb' => 'FFFFFFFF'
					)
				)
			)
		);
		// color de la fila de titulos
		$objPHPExcel->getActiveSheet()->getStyle('C6:'.$aHeader[$cont].'6')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '63a737'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));



		// mostrando fila de los titulos de las descripciones
		$cont=1;
		for($z=1;$z<=count($aTitulocompare);$z++)
		{
			$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$z].'7',$aTitulocompare[$z-1]);
			$objPHPExcel->getActiveSheet()->getColumnDimension($aHeader[$z])->setAutoSize(true);
			$cont++;

		}
		// TABLA COLOR
		$styleThickBrownBorderOutline = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);


		// colores de las fila de los titulos de la descripciones
		$objPHPExcel->getActiveSheet()->getStyle('A7:'.$aHeader[$cont-1].'7')->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'top'     => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation'   => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0'
					),
					'endcolor'   => array(
						'argb' => 'FFFFFFFF'
					)
				)
			)
		);

		// CUERPO
		$j=8;
		foreach ((array)$aExcelcompare as $label => $linedet)
		{
			$cont=1;
			foreach ($linedet as $k => $valedet)
			{

				if($k=='label' )
				{
					$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$valedet);
					$cont++;
				}
				if($k =='fk_unit')
				{
					$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$valedet);
					$objPHPExcel->getActiveSheet()->getStyle($aHeader[$cont].$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$cont++;
				}

				foreach ($valedet as $z => $linemonto)
				{

					if($z=='price_unit')
					{
						$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,##0.'.$cNumeropu);
						$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linemonto);
						$cont++;
					}
					if($z=='cost_total')
					{
						$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,##0.'.$cNumerototal);
						$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linemonto);
						$cont++;
					}

				}

			}
			$j++;
		}
		// TOTALES
		$cont=4;
		foreach ($sumTotal as $key => $linetotal)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,'TOTALES');
			$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linetotal);
			$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,##0.'.$cNumerototal);
			$cont+=2;

		}
		// color de la fila de los totales
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.$aHeader[$cont-2].$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));
		// mostrando los recuadros de las celdas
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$aHeader[$cont-2].$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->setActiveSheetIndex(0);
		//$objPHPExcel->getActiveSheet()->getStyle('A10:D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		//$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				//$objWriter->save("excel/ReportPOA.xlsx");
		$file = 'productcompare.xlsx';
		$objWriter->save($dir.'/'.$file);
		header("Location: ".DOL_URL_ROOT.'/budget/budget/fiche_export.php?archive='.$file);

	}


	if ($action == 'spreadsheet')
	{

		$dir = $conf->budget->dir_output.'/tmp';
		if (! file_exists($dir))
		{
			if (dol_mkdir($dir) < 0)
			{
				$error++;
				setEventmessages($langs->trans('Error al crear directorio'),null,'errors');
			}
		}

		$aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');



		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("yemer colque")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");

		$aExcelcompare = unserialize($_SESSION['aExcelcomparedet']);
		$aTitulocompare = unserialize($_SESSION['aTitulocomparedet']);
		$sumTotal = unserialize($_SESSION['sumTotaldet']);
		$nVersion = unserialize($_SESSION['nVersiondet']);

		//numero del array de titulos para alinear el titulo
		$nTit=count($aTitulocompare);
		// tittulo
		$objPHPExcel->setActiveSheetIndex(0);
		$cTitle=$langs->trans("Compareitems");

		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
		$sheet->getStyle('A2')->getFont()->setSize(15);
		// alineado
		$sheet->mergeCells('A2:'.$aHeader[$nTit].'2');
		if($yesnoprice)
			$sheet->mergeCells('A2:'.$aHeader[$nTit].'2');
		$sheet->getStyle('A2')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);

		// FILTRAMOS PARA ENCABEZADO

		$res = $object->fetch($id);
		if($res>0)
		{
			$cTitle=$object->title;
			$cRef=$object->ref;
			$cVersion=$object->version;

		}

		// FILTRANDO PARA DECIMALES
		$resge=$general->fetch(0,$id);
		if($resge>0)
		{
			$nMoneda=$general->base_currency;

			$nDecimalpu=$general->decimal_pu;
			$nDecimalquant=$general->decimal_quant;
			$nDecimaltotal=$general->decimal_total;
		}
		else
		{
			$nDecimalpu=6;
			$nDecimalquant=6;
			$nDecimaltotal=6;
		}


		if($resge>0)
		{
			switch ($general->decimal_pu)
			{
				case 0:
				$cNumeropu='';
				break;
				case 1:
				$cNumeropu='0';
				break;
				case 2:
				$cNumeropu='00';
				break;
				case 3:
				$cNumeropu='000';
				break;
				case 4:
				$cNumeropu='0000';
				break;
				case 5:
				$cNumeropu='00000';
				break;
				case 6:
				$cNumeropu='000000';
				break;
				case 7:
				$cNumeropu='0000000';
				break;
				case 8:
				$cNumeropu='00000000';
				break;
			}
			switch ($general->decimal_quant)
			{
				case 0:
				$cNumeroquant='';
				break;
				case 1:
				$cNumeroquant='0';
				break;
				case 2:
				$cNumeroquant='00';
				break;
				case 3:
				$cNumeroquant='000';
				break;
				case 4:
				$cNumeroquant='0000';
				break;
				case 5:
				$cNumeroquant='00000';
				break;
				case 6:
				$cNumeroquant='000000';
				break;
				case 7:
				$cNumeroquant='0000000';
				break;
				case 8:
				$cNumeroquant='00000000';
				break;

			}
			switch ($general->decimal_total)
			{
				case 0:
				$cNumerototal='';
				break;
				case 1:
				$cNumerototal='0';
				break;
				case 2:
				$cNumerototal='00';
				break;
				case 3:
				$cNumerototal='000';
				break;
				case 4:
				$cNumerototal='0000';
				break;
				case 5:
				$cNumerototal='00000';
				break;
				case 6:
				$cNumerototal='000000';
				break;
				case 7:
				$cNumerototal='0000000';
				break;
				case 8:
				$cNumerototal='00000000';
				break;

			}
		}

		// color encabezado
		$objPHPExcel->getActiveSheet()->getStyle('A3:C5')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0c78bf'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));


		// encabezado

		$objPHPExcel->getActiveSheet()->setCellValue('A3',html_entity_decode($langs->trans("Budget")));
		$objPHPExcel->getActiveSheet()->setCellValue('B3',$cTitle);
		$objPHPExcel->getActiveSheet()->mergeCells('B3:'.$aHeader[$nTit].'3');
			// REFERENCIA
		$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Ref")));
		$objPHPExcel->getActiveSheet()->setCellValue('B4',$cRef);
			// MONEDA
		$objPHPExcel->getActiveSheet()->setCellValue('A5',html_entity_decode($langs->trans("Version")));
		$objPHPExcel->getActiveSheet()->setCellValue('B5',$cVersion);


		$aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');


		// impresion de la fila las versiones

		$cont=3;
		for($a=1;$a<=$nVersion;$a++)
		{
			// version
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setCellValue('B6',html_entity_decode($langs->trans("Version")));



			//numeros de las versiones
			$sheet = $objPHPExcel->getActiveSheet();
			$sheet->getStyle($aHeader[$cont].'6')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].'6',$a);
			$objPHPExcel->getActiveSheet()->getStyle($aHeader[$cont].'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle($aHeader[$cont].'6')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			$sheet->setCellValueByColumnAndRow(0,6);
			$contaux=$cont+2;
			$sheet->mergeCells($aHeader[$cont].'6'.':'.$aHeader[$contaux].'6');
			$cont=$contaux+1;

		}

		// color de la fila de las versiones
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.$aHeader[$cont-1].'6')->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'top'     => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation'   => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0'
					),
					'endcolor'   => array(
						'argb' => 'FFFFFFFF'
					)
				)
			)
		);

		// color fila de las versiones
		$objPHPExcel->getActiveSheet()->getStyle('C6:'.$aHeader[$cont].'6')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '63a737'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));



		// impresion de la fila de las descripciones de los titulos
		$cont=1;
		for($z=1;$z<=count($aTitulocompare);$z++)
		{
			$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$z].'7',$aTitulocompare[$z-1]);
			$objPHPExcel->getActiveSheet()->getColumnDimension($aHeader[$z])->setAutoSize(true);
			$cont++;

		}
		// TABLA COLOR
		$styleThickBrownBorderOutline = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);


		// color de la fila de las descripciones de los titulos
		$objPHPExcel->getActiveSheet()->getStyle('A7:'.$aHeader[$cont-1].'7')->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'top'     => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation'   => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0'
					),
					'endcolor'   => array(
						'argb' => 'FFFFFFFF'
					)
				)
			)
		);

		// CUERPO
		$j=8;
		foreach ((array)$aExcelcompare as $label => $linedet)
		{
			$cont=1;
			foreach ($linedet as $k => $valedet)
			{
				if($k=='label' )
				{
					$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$valedet);
					$cont++;
				}
				if($k =='fk_unit')
				{
					$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$valedet);
					$objPHPExcel->getActiveSheet()->getStyle($aHeader[$cont].$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$cont++;
				}

				foreach ($valedet as $z => $linemonto)
				{
					if($z=='unit_budget')
					{
						$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,##0.'.$cNumeroquant);
						$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linemonto);
						$cont++;
					}
					if($z=='unit_amount')
					{
						$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,##0.'.$cNumeropu);
						$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linemonto);
						$cont++;
					}
					if($z=='total_amount')
					{
						$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);
						$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linemonto);
						$cont++;
					}


				}




			}
			$j++;



		}

		// TOTALES
		$cont=5;
		foreach ($sumTotal as $key => $linetotal)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,'TOTALES');
			$objPHPExcel->getActiveSheet()->setCellValue($aHeader[$cont].$j,$linetotal);
			$objPHPExcel-> getActiveSheet () -> getStyle ($aHeader[$cont].$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);
			$cont+=3;

		}

		// colores de los totales
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.$aHeader[$cont-3].$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));

		//impresion de los marcos de las celdas
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$aHeader[$cont-3].$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


		$objPHPExcel->setActiveSheetIndex(0);
		//$objPHPExcel->getActiveSheet()->getStyle('A10:D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		//$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				//$objWriter->save("excel/ReportPOA.xlsx");
		$file = 'budgettaskcompare.xlsx';
		$objWriter->save($dir.'/'.$file);
		header("Location: ".DOL_URL_ROOT.'/budget/budget/fiche_export.php?archive='.$file);

	}
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/parameters.php?id='.$object->id,1);
		header("Location: ".$urltogo);
		exit;
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
}

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);
// Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

//armamos las regiones en un array
$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$linesclassfin = $objCclasfin->lines;
	foreach ($linesclassfin AS $j => $line)
		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
}
//armamos la structura del proyecto
$filter = " AND t.type_structure= '".$object->type_structure."'";
$res = $objPustructure->fetchAll('ASC','t.detail',0,0,array(),'AND',$filter);
if ($res>0)
{
	$linesstr = $objPustructure->lines;
	foreach ($linesstr AS $j => $line)
	{
		$aStr[$line->ref] = $line->detail;
		//vamos a buscar las variables que forman parte
		$filter = " AND t.ref_structure= '".$line->ref."'";
		$filter.= " AND t.type_structure= '".$object->type_structure."'";
		$res = $objPustructuredet->fetchAll('ASC','t.detail',0,0,array(),'AND',$filter);
		if ($res>0)
		{
			$linesstrdet = $objPustructuredet->lines;
			foreach ($linesstrdet AS $j => $linedet)
			{
				$aStrdet[$line->ref][$linedet->formula] = $linedet->formula;
			}
		}
	}
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
//$morecss = array('/budget/css/style.css','/budget/css/bootstrap.min.css','/includes/jquery//plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);

llxHeader('',$title,'','','','',$morejs,$morecss,0,0);

// Put here content of your page
$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';


// Part to show record
//if ($id && (empty($action) || $action == 'viewit' || $action == 'creategr' || $action == 'viewgr' || $action == 'view' || $action == 'delete' || $action=='editres' || $action=='edititem' || $action=='viewre' || $action == 'confimportresource'))
if ($id > 0)
{
	$head = budget_prepare_head($object, $user);
	$titre=$langs->trans("Budget");
	$picto='budget';
	$getcard = 'summary';
	dol_fiche_head($head, $getcard, $titre, 0, $picto);



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

	print '</table>';

	dol_fiche_end();
	// Buttons
	print '<div class="tabsAction">'."\n";
	if ($user->rights->budget->budi->com)
	{
	//	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=compareitem">'.$langs->trans("Compareitem").'</a></div>'."\n";
	//	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=compareresource">'.$langs->trans("Compareresource").'</a></div>'."\n";
	}
	print '</div>';

	print '<div class="div-table-responsive">';
	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/summary.tpl.php';
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
