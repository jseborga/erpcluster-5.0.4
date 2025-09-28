<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2005-2009 Yemer Colque        <locoto125@gmail.com>
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
 *      \file       htdocs/almacen/liste.php
 *      \ingroup    almacen
 *      \brief      Page liste des solicitudes a almacenes
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/contabperiodo.class.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");

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

if (empty($ver))
{
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
}



$langs->load("stocks");
$langs->load("almacen@almacen");
//$langs->load("fabrication@fabrication");

if (!$user->rights->almacen->inv->read) accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;



 $typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['mingfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);
$fk_entrepot = $_SESSION['mingfk_entrepot'];

$now = dol_getdate(dol_now());
$dateinimin = dol_get_first_day($now['year'],1);
if (empty($dateinisel)) $dateinisel = dol_get_first_day($now['year'],1);

//filtramos por almacenes designados segun usuario
$objecten = new Entrepot($db);
$objectUrqEntrepot = new Entrepotrelationext($db);
$objuser = new User($db);
$objentrepotuser = new Entrepotuserext($db);
$movement = new MouvementStockext($db);
$objinv = new Inventario($db);
$periodo = new Contabperiodo($db);

//verificamos el periodo
verif_year();

$aFilterent = array();
$product = new Product($db);

$filteruser = '';
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
		$lines = $objentrepotuser->lines;
		foreach ($lines AS $i=>$line)
		{
			if ($line->fk_entrepot)
			{
				if (!empty($filteruser))$filteruser.= ',';
				$filteruser.= $line->fk_entrepot;
				$aFilterent[$line->fk_entrepot] = $line->fk_entrepot;
			}
		}
	}
}

//actions
if ($action == 'builddoc')	// En get ou en post
{
	$res = $objectUrqEntrepot->fetch($fk_entrepot);
	if (empty($res))
	{
		$objectUrqEntrepot->rowid = $fk_entrepot;
		$objectUrqEntrepot->fk_entrepot_father = -1;
		$objectUrqEntrepot->tipo = 'almacen';
		$objectUrqEntrepot->model_pdf = GETPOST('model');
		$res = $objectUrqEntrepot->create($user);
	}

	$objectUrqEntrepot->fetch_thirdparty();
	//$objecten->fetch_lines();
	if (GETPOST('model'))
	{
		$objectUrqEntrepot->setDocModel($user, GETPOST('model'));
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
	if (empty($objectUrqEntrepot->model_pdf))
	{
		$objectUrqEntrepot->modelpdf = GETPOST('model');
		$result=almacen_pdf_create($db, $objectUrqEntrepot, $objectUrqEntrepot->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	}
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

//excel

if ($action == 'excel')
{

	$aAmaxmin = unserialize($_SESSION['aAmaxmindet']);
	//$entrepot->fetch($inventorysel['fk_entrepot']);
	$entrepot = new Entrepot($db);
	$entrepot->fetch($fk_entrepot);


	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Yemer Colque")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Fractal Solutions");

	$objPHPExcel->setActiveSheetIndex(0);



	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setCellValueByColumnAndRow(0,2, "REPORTE SALDOS MAXIMOS MINIMOS");
	$sheet->getStyle('A2')->getFont()->setSize(15);

	$sheet->mergeCells('A2:I2');
	if($yesnoprice)
		$sheet->mergeCells('A2:I2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);


	$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Reportdate"));
	if($entrepot->lieu!='')
		$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Entrepot"));

	$objPHPExcel->getActiveSheet()->setCellValue('B4',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
	if($entrepot->lieu!='')
		$objPHPExcel->getActiveSheet()->setCellValue('B5', $entrepot->label);



	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);


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

	$objPHPExcel->getActiveSheet()->getStyle('A2:I8')->applyFromArray($styleThickBrownBorderOutline);

	// TABLA
	$j = 7;
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,$langs->trans("Código"));
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$j,$langs->trans("Detalle"));
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$j,$langs->trans("Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$j,$langs->trans("Almacen"));
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$j,$langs->trans("Mínino"));
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$j,$langs->trans("Máximo"));
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$j,$langs->trans("Saldo"));
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$j,$langs->trans("Alerta min."));
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$j,$langs->trans("Alerta max."));
	$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':I'.$j)->applyFromArray(
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

	$j++;
	$k = $j;
	$col='I';
	$contt=1;

	foreach ((array) $aAmaxmin AS $i => $lines)
	{
		$code = $lines['code'];
		$label = $lines['label'];
		$unit = html_entity_decode($lines['unitshort']);
		$labelentrepot =$lines['entrepot'];;
		$min = $lines['min'];
		$max = $lines['max'];
		$saldoStock = $lines['saldoStock'];
		$AlertMin = $lines['AlertMin'];
		$AlertMax =$lines['AlertMax'];

		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$code)
		->setCellValue('B' .$j,$label)
		->setCellValue('C' .$j,$unit)
		->setCellValue('D' .$j,$labelentrepot)
		->setCellValue('E' .$j,price($min))
		->setCellValue('F' .$j,price($max))
		->setCellValue('G' .$j,$saldoStock)
		->setCellValue('H' .$j,$AlertMin)
		->setCellValue('I' .$j,$AlertMax);
		$j++;
	}
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':'.$col.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/balancemaxmin.xlsx");
	header("Location: ".DOL_URL_ROOT.'/almacen/report/fiche_export.php?archive=balancemaxmin.xlsx');
}


///view

$formfile = new Formfile($db);
$form = new Formv($db);
if ($fk_entrepot)
{
	$res = $objecten->fetch($fk_entrepot);
}
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';


$aArrjs = array('almacen/javascript/recargar.js');
$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Balancemaxmin"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Balancemaxmin"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
print '<table class="border" width="100%">';

// Entrepot Almacen
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
print $objectUrqEntrepot->select_padre($fk_entrepot,'fk_entrepot',1,'',$filteruser);
print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';




if (($action == 'edit' || $action=='edits'))
{

	print '<br>';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Code").'</td>';
	print '<td align="left">'.$langs->trans("Label").'</td>';
	print '<td align="left">'.$langs->trans("Unidad").'</td>';
	print '<td>'.$langs->trans("Warehouse").'</td>';
	print '<td align="center">'.$langs->trans("Minino").'</td>';
	print '<td align="center">'.$langs->trans("Maximo").'</td>';
	print '<td align="center">'.$langs->trans("Balance").'</td>';
	//print '<td align="center">'.$langs->trans("CantidadDifMinimo").'</td>';
	//print '<td align="center">'.$langs->trans("CantidadDifMaximo").'</td>';
	print '<td align="center">'.$langs->trans("Alerta Min").'</td>';
	print '<td align="center">'.$langs->trans("Alerta Max").'</td>';
	print "</tr>\n";

	//print '</tr>';

	$lView = true;
	if ($lView)
	{
		$sql = " SELECT p.rowid, p.ref, p.label,  p.seuil_stock_alerte, ";
		$sql.= " ps.reel, ps.fk_entrepot, ps.tms, ";
		$sql.= " e.label AS entrepotlabel ";

		$sql.= " FROM ".MAIN_DB_PREFIX."product as p ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON p.rowid = ps.fk_product";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entrepot AS e ON ps.fk_entrepot = e.rowid";
		$sql.= " WHERE p.entity = ".$conf->entity;
		if ($fk_entrepot > 0) $sql.= " AND ps.fk_entrepot = ".$fk_entrepot;
		$sql.= " ORDER BY p.ref ASC ";


		$entrepotstatic=new Entrepot($db);
		$total=0;
		$totalvalue=$totalvaluesell=0;
		$aSaldores = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$total=$totalwithpmp;
			$i=0;
			$var=false;


			//print_r($num);
			while ($i < $num)
			{
				$dif1 = 0;
				$dif2 = 0;
				$var=!$var;
				$obj = $db->fetch_object($resql);

				$objProduct= new Product($db);
				$objProduct->fetch($obj->rowid);

				$objProduct->load_stock();
				//$saldoStock = price2num($objProduct->stock_warehouse[$idEntrepot]->real,'MU');
				$saldoStock = price2num($objProduct->stock_warehouse[$obj->fk_entrepot]->real,'MU');
				$min=$objProduct->seuil_stock_alerte;
				$max=$objProduct->desiredstock;
				if ($min > 0) $dif1=$saldoStock - $min;
				if ($max > 0) $dif2= $max-$saldoStock;


				//$objProduct->id = $obj->rowid;
				//$objProduct->ref = $obj->ref;
				//$objProduct->label = $obj->label;
				$entrepotstatic->id=$obj->fk_entrepot;
				$entrepotstatic->libelle=$obj->entrepotlabel;
				$unit = $langs->trans($objProduct->getLabelOfUnit());
				$unitshort = $langs->trans($objProduct->getLabelOfUnit('short'));


				print '<tr '.$bc[$var].'>';
				print '<td>'.$objProduct->getNomUrl(1).'</td>';
				print '<td>'.$objProduct->label.'</td>';
				print '<td align="left">'.$unitshort.'</td>';
				print '<td align="left">'.$entrepotstatic->getNomUrl(1).'</td>';
				print '<td align="right">'.price($min).'</td>';
				print '<td align="right">'.price($max).'</td>';
				print '<td align="right">'.$saldoStock.'</td>';
				//print '<td align="right">'.$dif1.'</td>';
				//print '<td align="right">'.$dif2.'</td>';
				if($dif1<0)
				{
					$alertMin=$langs->trans('Alertmin');
					print '<td>'.$alertMin.'</td>';
				}
				else
				{
					$alertMin= '';
					print '<td></td>';
				}
				if($dif2<0)
				{
					$alertMax=$langs->trans('Alertmax');
					print '<td>'.$alertMax.'</td>';
				}
				else
				{
					$alertMax = '';
					print '<td></td>';
				}
				print '</tr>'; ;
				$i++;

				$aAmaxmin[] = array('code'=>$objProduct->ref,'label'=>$objProduct->label,'unit'=>$unit,'unitshort'=>$unitshort,'entrepot'=>$obj->entrepotlabel,'min'=>$min,'max'=>$max,'saldoStock'=>$saldoStock,'AlertMin'=>$alertMin,'AlertMax'=>$alertMax);
			}
		}
		else
			dol_print_error($db);

		print "</table>";

		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
		print '</div>';

		//armamos un array para el reporte


		$aKardex['fk_entrepot'] = $fk_entrepot;
		$aKardex['entrepot'] = $objecten->lieu;
		$aKardex['fk_product'] = $id;
		$aKardex['productlabel'] = $product->label;
		$aKardex['unit'] = $unit;
		$aKardex['min'] = $min;
		$aKardex['max'] = $max;
		$aKardex['saldoStock'] = $saldoStock;

		//$aKardex['lines'] = array();
		$_SESSION['aAmaxmindet'] = serialize($aAmaxmin);
	}

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

		$model='balancemaxmin';
		$objinv->id = $fk_entrepot;

		$resprint=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($resprint< 0) dol_print_error($db,$resprint);

	}
	print '<div class="tabsAction">';
	//documents
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';
		// ancre
		//$entrepot->fetch($id);
		// Documents generes
	//$filename=dol_sanitizeFileName($entrepot->libelle).'/inv';


	$filename='maxmin/'.$period_year;
		//cambiando de nombre al reporte
	$filedir=$conf->almacen->dir_output . '/' . 'maxmin/'.$period_year;

	//$urlsource=$_SERVER['PHP_SELF'].'?id='.$id.'&yesnoprice='.$yesnoprice;
	$urlsource=$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot;
	$genallowed=$user->rights->almacen->creardoc;
	$genallowed=false;
	if (empty($_SESSION['inventorydet']))
		$genallowed=false;
	$genallowed=false;
	$delallowed=$user->rights->almacen->deldoc;
	$delallowed = false;
	//$modelpdf = 'rotacion';
	$modelpdf = 'balancemaxmin';
	print '<br>';
	print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	$somethingshown=$formfile->numoffiles;
	print '</td></tr></table>';

	print "</div>";
}



$db->close();

llxFooter();
?>
