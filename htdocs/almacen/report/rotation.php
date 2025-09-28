<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 * Copyright (C) 2017-2017 Yemer Colque        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *      \file       htdocs/almacen/inventario/rotacion.php
 *      \ingroup    almacen
 *      \brief      Page calculo del saldos de productos
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/mouvementstockext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuseradd.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
//require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/stock.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';


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

$langs->load("stocks");
$langs->load("almacen@almacen");


$id = GETPOST('id','int');
$yesno = GETPOST('yesno');
$yesnoact = GETPOST('yesnoact');
$zeroyesno = GETPOST('zeroyesno');
$yesnoprice = GETPOST('yesnoprice');

if (!$user->rights->almacen->inv->inv) accessforbidden();
if ($yesnoprice)
	if (!$user->rights->almacen->inv->invv) accessforbidden();

$action = GETPOST('action');
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

if ($_POST['id'])
{
	$_SESSION['idEntrepot'] = $id;
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selyesnoact'] = $yesnoact;
	$_SESSION['selzeroyesno'] = $zeroyesno;
}
//if (empty($action))
//	$action= 'edit';
if (empty($yesno)) $yesno = $_SESSION['selyesno'];
if (empty($yesno)) $yesno = 2;
if (empty($zeroyesno)) $zeroyesno = $_SESSION['selzeroyesno'];
if (empty($zeroyesno)) $zeroyesno = 2;

if (empty($id)) $id = $_SESSION['idEntrepot'];

$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
$typufv = $conf->global->ALMACEN_CHANGE_UFV;
//verificamos el periodo
verif_year();
$lGestion = false;
if (!empty($typemethod)) $lGestion = true;
$period_year = $_SESSION['period_year'];

//objetos
$objCategorie = new Categorie($db);

//actions
	// Remove file in doc form
if ($action == 'remove_file')
{
	if ($id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->almacen->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}

if ($action == 'edit')
{
	$error=0;
	if ($id <=0)
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->trans('Entrepot')), 'errors');
	}


	if (isset($_POST['dfyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');

		$dateinisel  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		//$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));

		//$dategesini = dol_mktime(0, 0, 0, '','', GETPOST('diyear'));

		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 59, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinselect  = dol_mktime(0, 0, 0, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$_SESSION['invdateini'] = $dateinisel;
		$_SESSION['invdatefin'] = $datefinsel;


		if ($lGestion)
		{
			$now = dol_getdate(dol_now());
			$dateinimin = dol_get_first_day($now['year'],1);
			if (empty($dateinisel)) $dateinisel = dol_get_first_day($now['year'],1);
			if ($dateinisel < $dateinimin)
			{
				$error++;
				setEventMessages($langs->trans('La fecha es menor al permitido').' '.dol_print_date($dateinisel,'day').' < '.dol_print_date($dateinimin,'day'),null,'errors');
				$dateinisel = $dateinimin;
			}
		}
	}

}
else
// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="p.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";


/*
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';
*/

$object = new Entrepotrelationext($db);
$entrepot = new Entrepot($db);
$form = new Form($db);
$formfile = new Formfile($db);
$movement=new MouvementStockext($db);
$objinv = new Inventario($db);
$product = new Product($db);
$multicurren = new Csindexescountryext($db);
$objCsindexescountry = new Csindexescountryext($db);

$hookmanager->initHooks(array('almacen'));
//print_r($hookmanager);
//filtramos por almacenes designados segun usuario

//$objentrepotuser = new Entrepotuseradd($db);
$objentrepotuser = new Entrepotrelationext($db);
$aFilterent = array();
$filteruser = '';


if ($action == 'excel')
{

	$aReport = unserialize($_SESSION['aRotaciondet']);
	$aRotation = $aReport['lines'];
	$id = $aReport['fk_entrepot'];
	$datefinsel = $aReport['datefinsel'];

	$entrepot->fetch($id);

	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("./excel/rotation.xlsx");

	$objPHPExcel->getProperties()->setCreator("yemer colque")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	//$sheet = $objPHPExcel->getActiveSheet();
	//$sheet->setCellValueByColumnAndRow(0,2, "Reporte de Rotación de Materiales");
	//$sheet->getStyle('A2')->getFont()->setSize(15);

	//$sheet->mergeCells('A2:E2');
	//if($yesnoprice)
	//	$sheet->mergeCells('A2:E2');
	//$sheet->getStyle('A2')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Date"));
	//$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Entrepot"));
	//$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans('Reportdate'));

	//$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);


	$objPHPExcel->getActiveSheet()->setCellValue('B3',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('B4', $entrepot->lieu);
	$objPHPExcel->getActiveSheet()->setCellValue('B5', dol_print_date($datefinsel,"day",false,$outputlangs));

	/*
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);

	$objPHPExcel->getActiveSheet()->getStyle('A2:E8')->applyFromArray($styleThickBrownBorderOutline);
	*/
	// TABLA
	$objPHPExcel->setActiveSheetIndex(0);

	/*
	$objPHPExcel->getActiveSheet()->setCellValue('A10',$langs->trans("Código"));
	$objPHPExcel->getActiveSheet()->setCellValue('B10',$langs->trans("Detalle"));
	$objPHPExcel->getActiveSheet()->setCellValue('C10',$langs->trans("Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('D10',$langs->trans("Tiempo de rotación"));
	$objPHPExcel->getActiveSheet()->setCellValue('E10',$langs->trans("Lastdate"));

	$objPHPExcel->getActiveSheet()->getStyle('A10:E10')->applyFromArray(
		array('font'    => array('bold'      => true),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),'borders' => array('allborders'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)),'fill' => array(
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
	$objPHPExcel->getActiveSheet()->getStyle('A10:E15')->applyFromArray(
		array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FFA0A0A0')
					)
				)
			)

		);
	*/
	$j=8;
	$col='E';
	$sumsaav=0;
	$sumainpv=0;
	$sumaoutv=0;
	$sumabalv=0;
	$contt=1;

	foreach ((array) $aRotation AS $i => $lines)
	{
		$code=$lines['code'];
		$desc=$lines['label'];
		$unit=html_entity_decode($lines['unitshort']);
		$timePerm=$lines['timePerm'];
		$datem=$lines['datem'];

		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$code)
		->setCellValue('B' .$j,$desc)
		->setCellValue('C' .$j,$unit)
		->setCellValue('D' .$j,$timePerm)
		->setCellValue('E' .$j,dol_print_date($datem,'day'));

		$j++;
		$contt++;

	}
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A8:E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/rotacion.xlsx");

	header("Location: ".DOL_URL_ROOT.'/almacen/report/fiche_export.php?archive=rotacion.xlsx');
}

if (!$user->admin)
{
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic.= " AND t.active = 1";
	$res = $objentrepotuser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	//$res = $objentrepot->getlistuser($user->id);
	if ($res > 0)
	{
		$num = count($objentrepotuser->lines);
		$i = 0;
		$line = $objentrepotuser->lines;
		for ($i=0; $i < $num; $i++)
		{
			if (!empty($filteruser))$filteruser.= ',';
			$filteruser.= $line[$i]->fk_entrepot;
			$aFilterent[$line[$i]->fk_entrepot] = $line[$i]->fk_entrepot;
		}
	}
}
/*
Actions
*/
if ($action == 'builddoc')	// En get ou en post
{
	$object = new Entrepotrelationext($db);
	$id = $_SESSION['idEntrepot'];

	$object->fetch($id);
	if (empty($object->id))
		$object->id = $id;
	$object->fetch_thirdparty();
	$object->fetch_lines();
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
	$result=almacen_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
		exit;
	}
}


//view
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("Productrotationtime"),$help_url);

print_fiche_titre($langs->trans("Productrotationtime"));


print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';

print '<table class="border" width="100%">';

// Entrepot Almacen
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
//$options = '<option value="0">'.$langs->trans('All').'</option>';
print $object->select_padre($id,'id',1,'',$filteruser);
print '</td></tr>';

print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);

print '</td></tr>';


print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';
if (!$error && $action == 'edit' && !empty($id))
{


	$_SESSION['idEntrepot'] = $id;
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selyesnoact'] = $yesnoact;
	$_SESSION['selzeroyesno'] = $zeroyesno;
	$aRowid = array();
	if ($id>0)
	{
		$entrepot->fetch($id);
	}
	$object = new Entrepotrelationext($db);
	$object->id = $id;
	//print_r($object);

	$dateinisel = dol_get_first_day($period_year,1,false);


	$result = $object->fetch_entrepot();
	if ($result == 1)
	{
		$aEntrepot = $object->aArray;

	}
	//movimiento de salidas y entradas
	if ($yesno == 1) $object->fetch_lines();

	//listamos todos los productos

	$params='&action=edit&id='.$id;

	//$title = $langs->trans('Inventory');
	$title = $langs->trans('Rotation');
	print '<table class="noborder" width="100%">';
	print "<tr class=\"liste_titre\">";
	print '<th colspan="2"></th>';
	print '<th colspan="7" align="center">'.$langs->trans('').'</th>';
	print '</tr>';

	print "<tr class=\"liste_titre\">";

	print_liste_field_titre($langs->trans("Code"),$_SERVER['PHP_SELF'], "p.ref","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),$_SERVER['PHP_SELF'], "p.label","",$params,'align="left"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),$_SERVER['PHP_SELF'], "u.label","",$params,'align="left"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Rotation time"),$_SERVER['PHP_SELF'], "","",$params,'align="left"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Datelastoutput"),$_SERVER['PHP_SELF'], "","",$params,'align="right"',$sortfield,$sortorder);


	print "</tr>\n";
	$aRotacion=array();


			//$objcat = $db->fetch_object($result);
	$sql  = "SELECT p.rowid, p.ref, p.label, p.stock ";
	$sql.= " , u.label AS labelunit, u.short_label ";
	$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock AS c ON c.fk_product = p.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_units AS u ON p.fk_unit = u.rowid";
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND c.fk_entrepot = ".$id;
	//$sql.= " ORDER BY p.ref";
	$sql.= " ORDER BY $sortfield $sortorder";

	$resprod = $db->query($sql);
	$ValorAct=0;
	$ValorDif=0;


	if ($resprod)
	{
		$num = $db->num_rows($resprod);

		if ($num > 0)
		{
			$o = 0;

			$res = $movement->mouvement_period($id,$dateinisel,$datefinsel,0,'',1);

					//$aMoving = $movement->aMoving;
					//$aMovsal = $movement->aMovsal;
			$aMovdet = $movement->aMovdet;

			$aIng = $movement->aIng;
			$aSal = $movement->aSal;
			while ($o < $num)
			{
				$print = true;
						//recorriendo los productos
				$obj = $db->fetch_object($resprod);
				$product = new Product($db);
				$product->fetch($obj->rowid);

				$aIngdet = $aMovdet[$obj->rowid];
				$aMovd = array();
				foreach ((array) $aIngdet AS $fk => $data)
				{
					$aMovd['datem'] = $data['datem'];
					$aMovd['type_mouvement']=$data['type_mouvement'];
					$aMovd['saldo']=$data['qty'];
				}


				$timePerm=num_between_day($aMovd['datem'],$datefinsel,1);

				$unit=$langs->trans($product->getLabelOfUnit());
				$unitshort=$langs->trans($product->getLabelOfUnit('short'));
				print "<tr $bc[$var]>";
				print '<td>'.$product->getNomUrl(1).'</td>';
				print '<td>'.$obj->label.'</td>';
				print '<td align="left">'.$unitshort.'</td>';
				if($aMovd['datem']!='')
				{
					print '<td align="left" >'.$timePerm.'</td>';
				}

				print '<td align="right" >'.dol_print_date($aMovd['datem'],'day').'</td>';
				$aRotacion[]=array('code'=>$product->ref,'label'=>$obj->label,'unit'=>$unit,'unitshort'=>$unitshort,'timePerm'=>$timePerm,'datem'=>$aMovd['datem']);
				print '</tr>';

				$o++;
			}

			$aReport['lines'] = $aRotacion;
			$aReport['fk_entrepot'] = $id;
			$aReport['datefinsel'] = $datefinsel;

			$_SESSION['aRotaciondet'] = serialize($aReport);

		}
	}
	$i++;

	print "</table>";
		//boton excel
	print "<div class=\"tabsAction\">\n";
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$id.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
	print '</div>';

			// Define output language
	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
	{
		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}

			//$model='rotacion';

		$model='rotation';
		$objinv->id = $id;
		$objinv->lieu = $entrepot->lieu;


		$resprint=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($resprint< 0) dol_print_error($db,$result);


	}

	//}



	print '<div class="tabsAction">';
	//documents
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';
		// ancre
	$entrepot->fetch($id);
	// Documents generes
	$filename=dol_sanitizeFileName($entrepot->libelle).'/'.$period_year.'/rotation';
	//cambiando de nombre al reporte
	$filedir=$conf->almacen->dir_output .'/'.dol_sanitizeFileName($entrepot->libelle).'/'.$period_year.'/rotation';

	$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
	$genallowed=$user->rights->almacen->creardoc;
	$genallowed=false;
	$delallowed=$user->rights->almacen->deldoc;
	$delallowed = false;
	$modelpdf = 'rotation';
	print '<br>';
	print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	$somethingshown=$formfile->numoffiles;
	print '</td></tr></table>';

	print "</div>";


	//}

}
$db->close();

llxFooter();
?>
