<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2017      Nicolas ZABOURI	<info@inovea-conseil.com>
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
 *   	\file       budget/itemsproduct_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-06-05 12:20
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
dol_include_once('/budget/class/itemsproductext.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/budget/class/itemsgroup.class.php');


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
$langs->load("budget");
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

$search_fk_item=GETPOST('search_fk_item','alpha');
$search_ref=GETPOST('search_ref','alpha');
$search_group_structure=GETPOST('search_group_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_unit=GETPOST('search_fk_unit','alpha');
$search_label=GETPOST('search_label','alpha');
$search_formula=GETPOST('search_formula','alpha');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');
$search_fk_region=GETPOST('search_fk_region','alpha');
$search_fk_sector=GETPOST('search_fk_sector','alpha');
$search_amount=GETPOST('search_amount','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
if (empty($page)) $page=0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}
$aStatus = array(1=>$langs->trans('Validated'),0=>$langs->trans('Draft'));
$aGroup = array('MA'=>$langs->trans('Materials'),'MO'=>$langs->trans('Workforce'),'MQ'=>$langs->trans('Machineryandequipment'));
// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetitemsproductresourceslist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('budgetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('budget');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.fk_item'=>array('label'=>$langs->trans("Fieldfk_item"), 'align'=>'align="left"', 'checked'=>1),
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
	't.group_structure'=>array('label'=>$langs->trans("Fieldgroup_structure"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'align'=>'align="left"', 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'align'=>'align="left"', 'checked'=>1),
	't.formula'=>array('label'=>$langs->trans("Fieldformula"), 'align'=>'align="left"', 'checked'=>0),
	'ip.fk_region'=>array('label'=>$langs->trans("Fieldfk_region"), 'align'=>'align="left"', 'checked'=>1),
	'ip.fk_sector'=>array('label'=>$langs->trans("Fieldfk_sector"), 'align'=>'align="left"', 'checked'=>1),
	'ip.amount'=>array('label'=>$langs->trans("Fieldcost_productive"), 'align'=>'align="right"', 'checked'=>1),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>1),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
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
$object=new Itemsproductext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objUser = new User($db);
$objProduct = new Product($db);
$objItems = new Itemsext($db);
$objItemsgroup = new Itemsgroup($db);

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

		$search_fk_item='';
		$search_ref='';
		$search_group_structure='';
		$search_fk_product='';
		$search_fk_unit='';
		$search_label='';
		$search_formula='';
		$search_active=-1;
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_status=-1;
		$search_fk_region='';
		$search_fk_sector='';
		$search_amount='';



		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

	if ($action == 'export')
	{
		$a = 1;
		for($i=65; $i<=90; $i++)
		{
			$aColumn[$a] = chr($i);
			$a++;
		}

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql .= " t.fk_item,";
		$sql .= " t.ref,";
		$sql .= " t.group_structure,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_unit,";
		$sql .= " t.label,";
		$sql .= " t.formula,";
		$sql .= " t.active,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		$sql .= " , i.ref AS refitem";
		$sql .= " , i.detail AS labelitem";
		$sql .= " , ip.fk_region";
		$sql .= " , ip.fk_sector";
		$sql .= " , ip.amount";
		$sql .= " , cr.ref AS refregion";
		$sql .= " , cf.ref AS refinstitutional";
		$sql .= " , cr.label AS labelregion";
		$sql .= " , cf.label AS labelinstitutional";
		$sql .= " , u.code AS refunit";



		// Add fields from extrafields
		foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
		// Add fields from hooks
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
		// Note that $action and $object may have been modified by hook
		$sql.=$hookmanager->resPrint;
		$sql.= " FROM ".MAIN_DB_PREFIX."items_product as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."items as i ON t.fk_item = i.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_product_region as ip ON ip.fk_item_product = t.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_region_geographic as cr ON ip.fk_region = cr.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_clasfin as cf ON ip.fk_sector = cf.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_units as u ON t.fk_unit = u.rowid";

		if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_product_extrafields as ef on (t.rowid = ef.fk_object)";
		$sql.= " WHERE 1 = 1";
		//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

		if ($search_fk_item) $sql.= natural_search("fk_item",$search_fk_item);
		if ($search_ref) $sql.= natural_search("t.ref",$search_ref);
		if ($search_group_structure!='-1') $sql.= natural_search("group_structure",$search_group_structure);
		if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
		if ($search_fk_unit) $sql.= natural_search(array("fk_unit","u.code","u.label","u.short_label"),$search_fk_unit);
		if ($search_label) $sql.= natural_search("t.label",$search_label);
		if ($search_formula) $sql.= natural_search("formula",$search_formula);
		if ($search_active!=-1) $sql.= natural_search("t.active",$search_active);
		if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
		if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
		if ($search_status!=-1) $sql.= natural_search("t.status",$search_status);
		if ($search_fk_region) $sql.= natural_search(array("fk_region","cr.ref","cr.label"),$search_fk_region);
		if ($search_fk_sector) $sql.= natural_search(array("fk_sector","cf.ref","cf.label"),$search_fk_sector);
		if ($search_amount) $sql.= natural_search("amount",$search_amount);


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
		$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);
		// Note that $action and $object may have been modified by hook
		$sql.=$hookmanager->resPrint;
		$sql.=$db->order($sortfield,$sortorder);
		//$sql.= $db->plimit($conf->liste_limit+1, $offset);

		dol_syslog($script_file, LOG_DEBUG);
		$resql=$db->query($sql);
		if (! $resql)
		{
			dol_print_error($db);
			exit;
		}
		$num = $db->num_rows($resql);
		//vamos a recorrer
		//para titulos
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Ramiro Queso")
		->setLastModifiedBy("Ramiro Queso")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");

			//vamos a definir ciertos valores

			//PIE DE PAGINA
		$objPHPExcel->setActiveSheetIndex(0);
		$cTitle=$langs->trans("Resources");

		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
		$sheet->getStyle('A2')->getFont()->setSize(15);



		$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			// ENCABEZADO

		$lin = 4;

		$a=1;
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'fk_item')
				{
					$key2 = $langs->trans('Fieldfk_item');
				}
				if ($key2 == 'ref')
				{
					$key2 = $langs->trans('Ref');
				}
				if ($key2 == 'label')
				{
					$key2 = $langs->trans('Label');
				}
				if ($key2 == 'group_structure')
				{
					$key2 = $langs->trans('Fieldgroup_structure');
				}
				if ($key2 == 'active')
				{
					$key2 = $langs->trans('Active');
				}
				if ($key2 == 'fk_region')
				{
					$key2 = $langs->trans('Fieldfk_region');
				}
				if ($key2 == 'fk_sector')
				{
					$key2 = $langs->trans('Fieldfk_sector');
				}
				if ($key2 == 'fk_unit')
				{
					$key2 = $langs->trans('Unit');
				}
				if ($key2 == 'fk_product')
				{
					$key2 = $langs->trans('Fieldfk_product');
				}
				if ($key2 == 'amount')
				{
					$key2 = $langs->trans('Fieldcost_productive');
				}

				if ($key2 == 'status')
				{
					$key2 = $langs->trans('Status');
				}
				if ($key2 == 'fk_user_create')
				{
					$key2 = $langs->trans('Fieldfk_user_create');
				}
				if ($key2 == 'fk_user_mod')
				{
					$key2 = $langs->trans('Fieldfk_user_mod');
				}
				$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$a].$lin,html_entity_decode($key2));
				$objPHPExcel->getActiveSheet()->getColumnDimension($aColumn[$a])->setAutoSize(true);
				$a++;
			}
		}

		//$objPHPExcel->getActiveSheet()->mergeCells('A'.$lin.':'.$aColumn[$a-1].$lin);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':'.$aColumn[$a-1].$lin)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0c78bf'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':'.$aColumn[$a-1].$lin)->applyFromArray(
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
		$sheet->mergeCells('A2:'.$aColumn[$a-1].'2');
		$sheet->getStyle('A2')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);


		$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':'.$aColumn[$a-1].$lin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$lin++;
		$lin++;
		$linini = $lin;
		$i=0;
		$var=true;
		$totalarray=array();
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$a=1;
				// LIST_OF_TD_FIELDS_LIST
				foreach ($arrayfields as $key => $value)
				{
					if (!empty($arrayfields[$key]['checked'])) {
						//$key2 = str_replace('t.', '', $key);
						$aKey = explode('.',$key);
						$key2 = $aKey[1];
						if ($key2 == 'fk_item')
						{
							$obj->$key2 = $obj->refitem;
						}
						if ($key2 == 'group_structure')
						{
							$obj->$key2 = $aGroup[$obj->$key2];
						}
						if ($key2 == 'active')
						{
							$img = $langs->trans('Not');
							if ($obj->$key2) $img = $langs->trans('Yes');
							$obj->$key2 = $img;
						}
						if ($key2 == 'fk_region')
						{
							$obj->$key2 = $obj->refregion;
						}
						if ($key2 == 'fk_sector')
						{
							$obj->$key2 = $obj->refinstitutional;
						}
						if ($key2 == 'fk_unit')
						{
							$objTmp = new ItemsproductLineext($db);
							$objTmp->fk_unit = $obj->$key2;
							$obj->$key2 = $objTmp->getLabelOfUnit('short');
						}
						if ($key2 == 'fk_product')
						{
							if ($obj->$key2>0)
							{
								$res = $objProduct->fetch($obj->$key2);
								if ($res == 1)
									$obj->$key2 = $objProduct->ref;
								else
									$obj->$key2 = '';
							}
							else
								$obj->$key2 = '';	
						}
						if ($key2 == 'status')
						{
							$object->status = $obj->$key2;
							$obj->$key2 = $object->getLibStatut(1);
						}
						if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
						{
							$res = $objUser->fetch($obj->$key2);
							if ($res == 1)
								$obj->$key2 = $objUser->login;
						}
						if ($key2 == 'amount')
							$objPHPExcel->getActiveSheet()->getStyle($aColumn[$a])->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
						$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$a].$lin,html_entity_decode($obj->$key2));
						$a++;

					}
				}
				$lin++;
			}
			$i++;
		}
		$col = $aColumn[$a-1];
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$linini.':'.$col.$lin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$dir = $conf->budget->dir_output.'/excel/';
		if (! file_exists($dir))
		{
			if (dol_mkdir($dir) < 0)
			{
				$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
				$error++;
			}
		}
		$file = "resources.xlsx";
		$objWriter->save($dir.$file);

		//$objWriter->save("excel/Inventory.xlsx");
		header("Location: ".DOL_URL_ROOT.'/budget/resources/fiche_export.php?archive='.$file);
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
$title = $langs->trans('Resources');

// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript"> jQuery(document).ready(function() { function init_myfunc() { jQuery("#myid").removeAttr(\'disabled\'); jQuery("#myid").attr(\'disabled\',\'disabled\'); 	} 	init_myfunc(); 	jQuery("#mybutton").click(function() { 		init_myfunc(); 		}); 		}); 	</script>';


$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.fk_item,";
$sql .= " t.fk_item AS fk_itemor,";
$sql .= " t.ref,";
$sql .= " t.group_structure,";
$sql .= " t.fk_product,";
$sql .= " t.fk_unit,";
$sql .= " t.label,";
$sql .= " t.formula,";
$sql .= " t.active,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.status";

$sql .= " , i.ref AS refitem";
$sql .= " , i.detail AS labelitem";
$sql .= " , ip.fk_region";
$sql .= " , ip.fk_sector";
$sql .= " , ip.amount";
$sql .= " , cr.ref AS refregion";
$sql .= " , cf.ref AS refinstitutional";
$sql .= " , cr.label AS labelregion";
$sql .= " , cf.label AS labelinstitutional";
$sql .= " , u.code AS refunit";



// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
// Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."items_product as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."items as i ON t.fk_item = i.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_product_region as ip ON ip.fk_item_product = t.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_region_geographic as cr ON ip.fk_region = cr.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_clasfin as cf ON ip.fk_sector = cf.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_units as u ON t.fk_unit = u.rowid";

if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_product_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_item) $sql.= natural_search(array("fk_item","i.ref","i.detail"),$search_fk_item);
if ($search_ref) $sql.= natural_search("t.ref",$search_ref);
if ($search_group_structure!='-1') $sql.= natural_search("group_structure",$search_group_structure);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_fk_unit) $sql.= natural_search(array("fk_unit","u.code","u.label","u.short_label"),$search_fk_unit);
if ($search_label) $sql.= natural_search("t.label",$search_label);
if ($search_formula) $sql.= natural_search("formula",$search_formula);
if ($search_active!=-1) $sql.= natural_search("t.active",$search_active);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_status!=-1) $sql.= natural_search("t.status",$search_status);
if ($search_fk_region) $sql.= natural_search(array("fk_region","cr.ref","cr.label"),$search_fk_region);
if ($search_fk_sector) $sql.= natural_search(array("fk_sector","cf.ref","cf.label"),$search_fk_sector);
if ($search_amount) $sql.= natural_search("amount",$search_amount);


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
	header("Location: ".DOL_URL_ROOT.'/itemsproduct/card.php?id='.$id);
	exit;
}

llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_ref != '') $param.= '&amp;search_ref='.urlencode($search_ref);
if ($search_group_structure != '') $param.= '&amp;search_group_structure='.urlencode($search_group_structure);
if ($search_fk_product != '') $param.= '&amp;search_fk_product='.urlencode($search_fk_product);
if ($search_label != '') $param.= '&amp;search_label='.urlencode($search_label);
if ($search_fk_region != '') $param.= '&amp;search_fk_region='.urlencode($search_fk_region);
if ($search_fk_sector != '') $param.= '&amp;search_fk_sector='.urlencode($search_fk_sector);
if ($search_amount != '') $param.= '&amp;search_amount='.urlencode($search_amount);
if ($search_active != -1) $param.= '&amp;search_active='.urlencode($search_active);
if ($search_status != -1) $param.= '&amp;search_status='.urlencode($search_status);

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
if ($user->rights->budget->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
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
//$moreforfilter.='<div class="divsearchfield">';
//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
//$moreforfilter.= '</div>';

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
print '<table style="font-size:13px;" class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_item']['checked'])) print_liste_field_titre($arrayfields['t.fk_item']['label'],$_SERVER['PHP_SELF'],'t.fk_item','',$params,$arrayfields['t.fk_item']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,$arrayfields['t.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.group_structure']['checked'])) print_liste_field_titre($arrayfields['t.group_structure']['label'],$_SERVER['PHP_SELF'],'t.group_structure','',$params,$arrayfields['t.group_structure']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,$arrayfields['t.fk_product']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,$arrayfields['t.fk_unit']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,$arrayfields['t.label']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula']['checked'])) print_liste_field_titre($arrayfields['t.formula']['label'],$_SERVER['PHP_SELF'],'t.formula','',$params,$arrayfields['t.formula']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['ip.fk_region']['checked'])) print_liste_field_titre($arrayfields['ip.fk_region']['label'],$_SERVER['PHP_SELF'],'ip.fk_region','',$params,$arrayfields['ip.fk_region']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['ip.fk_sector']['checked'])) print_liste_field_titre($arrayfields['ip.fk_sector']['label'],$_SERVER['PHP_SELF'],'ip.fk_sector','',$params,$arrayfields['ip.fk_sector']['align'],$sortfield,$sortorder);

if (! empty($arrayfields['ip.amount']['checked'])) print_liste_field_titre($arrayfields['ip.amount']['label'],$_SERVER['PHP_SELF'],'ip.amount','',$params,$arrayfields['ip.amount']['align'],$sortfield,$sortorder);

if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,$arrayfields['t.active']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,$arrayfields['t.status']['align'],$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
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
$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
// Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_item']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_item']['align'].'><input type="text" class="flat" name="search_fk_item" value="'.$search_fk_item.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.group_structure']['checked']))
{
	print '<td class="liste_titre" '.$arrayfields['t.group_structure']['align'].'>';
	print $form->selectarray('search_group_structure',$aGroup,$search_group_structure,1);
	print '</td>';
}
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product']['align'].'><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_unit']['align'].'><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.label']['align'].'><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.formula']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula']['align'].'><input type="text" class="flat" name="search_formula" value="'.$search_formula.'" size="10"></td>';
if (! empty($arrayfields['ip.fk_region']['checked'])) print '<td class="liste_titre" '.$arrayfields['ip.fk_region']['align'].'><input type="text" class="flat" name="search_fk_region" value="'.$search_fk_region.'" size="8"></td>';
if (! empty($arrayfields['ip.fk_sector']['checked'])) print '<td class="liste_titre" '.$arrayfields['ip.fk_sector']['align'].'><input type="text" class="flat" name="search_fk_sector" value="'.$search_fk_sector.'" size="8"></td>';
if (! empty($arrayfields['ip.amount']['checked'])) print '<td class="liste_titre" '.$arrayfields['ip.amount']['align'].'><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';

if (! empty($arrayfields['t.active']['checked']))
{
	print '<td class="liste_titre" '.$arrayfields['t.active']['align'].'>';
	print $form->selectyesno('search_active',$search_active,1,false,1);
	print '</td>';
}
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked']))
{
	print '<td class="liste_titre" '.$arrayfields['t.status']['align'].'>';
	print $form->selectarray('search_status',$aStatus,$search_status,1);
	print '</td>';
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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
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
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'fk_item')
				{
					$filter = " AND t.fk_item = ".$obj->fk_item;
					$res = $objItemsgroup->fetchAll('','',0,0,array(),'AND',$filter,true);
					//$objItems->id = $obj->fk_item;
					//$objItems->ref = $obj->refitem;
					//$objItems->detail = $obj->labelitem;
					if ($res == 1)
						$obj->$key2 = $objItemsgroup->getNomUrl();
				}
				if ($key2 == 'ref')
				{
					$filter = " AND t.fk_item = ".$obj->fk_item;
					$res = $objItemsgroup->fetchAll('','',0,0,array(),'AND',$filter,true);
					if ($res)
						$object->fk_item = $objItemsgroup->id;
					$object->id = $obj->rowid;
					$object->ref = $obj->ref;
					$object->label = $obj->label;
					$obj->$key2 = $object->getNomUrl();
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'fk_region')
				{
					$obj->$key2 = $obj->refregion;
				}
				if ($key2 == 'fk_sector')
				{
					$obj->$key2 = $obj->refinstitutional;
				}
				if ($key2 == 'fk_unit')
				{
					$objTmp = new ItemsproductLineext($db);
					$objTmp->fk_unit = $obj->$key2;
					$obj->$key2 = $objTmp->getLabelOfUnit('short');
				}
				if ($key2 == 'fk_product')
				{
					$res = 0;
					if ($obj->$key2>0)
						$res = $objProduct->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objProduct->getNomUrl(1);
					else
						$obj->$key2 = '';
				}
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}

				print '<td '.$arrayfields[$key]['align'].'>' . $obj->$key2 . '</td>';
				if (!$i)
					$totalarray['nbfield'] ++;
			}
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
		//if ($massactionbutton || $massaction)
		// If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
		//{
		//	$selected=0;
		//	if (in_array($obj->rowid, $arrayofselected)) $selected=1;
		//	print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
		//}
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
			if ($num < $limit && empty($offset)) print '<td align="left">'.$langs->trans("Total").'</td>';
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

	print '<div class="tabsAction">';

if ($user->rights->budget->ite->updateres)
{
	print '<a class="butAction" href="' . dol_buildpath('/budget/resources/updateprice.php',1).'?action=create'.$param.'">' . $langs->trans('Updateprices') . '</a>';
}
if ($user->rights->budget->ite->exp)
{
	print '<a class="butAction" href="' . dol_buildpath('/budget/resources/list.php',1).'?action=export'.$param.'">' . $langs->trans('Spreadsheet') . '</a>';
}
	print '</div>';

// End of page
llxFooter();
$db->close();
