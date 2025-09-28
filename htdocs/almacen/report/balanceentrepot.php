<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *      \file       htdocs/almacen/report/balanceentrepot.php
 *      \ingroup    almacen
 *      \brief      Page Liste balance for entrepot
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");

require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

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

if (!$user->rights->almacen->inv->read) accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action = GETPOST('action','alpha');
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="p.label";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['contratfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);

$fk_entrepot = $_SESSION['contratfk_entrepot'];
$idprod = GETPOST('idprod');
$fk_category = GETPOST('fk_category');

//filtramos por almacenes designados segun usuario
$objecten = new Entrepot($db);

$objuser = new User($db);

$aFilterent = array();
$product = new Product($db);
$categorie = new Categorie($db);




//actions
$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();
if ($action == 'builddoc')	// En get ou en post
{
	$res = $objecten->fetch($fk_entrepot);
	if (empty($res))
	{
		$objecten->rowid = $fk_entrepot;
		$objecten->fk_entrepot_father = -1;
		$objecten->tipo = 'almacen';
		$objecten->model_pdf = GETPOST('model');
		$res = $objecten->create($user);
	}

	$objecten->fetch_thirdparty();
	//$objecten->fetch_lines();
	if (GETPOST('model'))
	{
		$objecten->setDocModel($user, GETPOST('model'));
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
		$result=almacen_pdf_create($db, $objectUrqEntrepot, $objectUrqEntrepot->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
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
		$upload_dir = $conf->fabrication->dir_output;
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

if ($action == 'excel')
{
	include_once DOL_DOCUMENT_ROOT.'/almacen/lib/format_excel.lib.php';

	$aCell = array(3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W');
	$aReport = unserialize($_SESSION['aBalanceentrepot']);
	//PRCESO 1
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	//armamos la cabecera
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('Ref'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B1',$langs->trans('Label'));
	$c = 3;
	foreach($aReport['aEntrepotdata'] AS $j =>$data)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue($aCell[$c].'1',$data['label']);
		$c++;
	}
	$objPHPExcel->getActiveSheet()->SetCellValue($aCell[$c].'1',$langs->trans('Total'));
	$objPHPExcel->getActiveSheet()->getStyle('A1:'.$aCell[$c].'1')->applyFromArray($styleArray);


	//cambiamos de fila
	$line = 2;
	$aTotal = array();
	foreach ($aReport['lines'] AS $j => $row)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['label']);
		//recorremos segun el almacen
		$c = 3;
		foreach($aReport['aEntrepotdata'] AS $j =>$data)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue($aCell[$c].$line,$row[$data['id']]);
			$aTotal[$data['id']]+=$row[$data['id']];
			$c++;
		}
		//imprimir el total de la linea
		$objPHPExcel->getActiveSheet()->SetCellValue($aCell[$c].$line,$row['total']);
		$line++;
	}
	//imprimimos en total final
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Total'));
	$c = 3;
	$sumTotal = 0;
	foreach($aReport['aEntrepotdata'] AS $j =>$data)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue($aCell[$c].$line,price2num($aTotal[$data['id']],'MT'));
		$sumTotal += $aTotal[$data['id']];
		$c++;
	}
	$objPHPExcel->getActiveSheet()->SetCellValue($aCell[$c].$line,$sumTotal);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$c = 3;
	foreach($aReport['aEntrepotdata'] AS $j =>$data)
	{
		$objPHPExcel->getActiveSheet()->getColumnDimension($aCell[$c])->setAutoSize(true);
		$c++;
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension($aCell[$c])->setAutoSize(true);

	//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/export.xlsx");

	header('Location: '.DOL_URL_ROOT.'/almacen/report/fiche_export.php');
}

if ($action == 'edit')
{
	$error=0;
	$product = new Product($db);
	if ((isset($_POST['idprod']) && $_POST['idprod']>0) || (isset($_POST['search_idprod']) && !empty($_POST['search_idprod'])))
	{
		if ($product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''))>0)
		{
			$_GET['idprod'] = $product->id;
			$_POST['idprod'] = $product->id;
			$idprod = $product->id;
		}
	}
	else
		$idprod = 0;

	if (isset($_POST['diyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(12, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));

		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 50, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		if ($dateinisel <= $datefinsel)
		{
			$_SESSION['contrat']['dateini'] = $dateini;
			$_SESSION['contrat']['dateinisel'] = $dateinisel;
			$_SESSION['contrat']['datefin'] = $datefin;
			$_SESSION['contrat']['datefinsel'] = $datefinsel;
		}
		else
		{
			$error++;
			setEventMessage($langs->trans("Errordatenovalid", GETPOST('id')), 'errors');
		}
		if (empty($error))
			setEventMessage($langs->trans("Proceso satisfactorio", GETPOST('id')));
	}
}
else
	unset($_SESSION['newContrat']);

if (!empty($_SESSION['newContrat']['dateini'])) $dateini = $_SESSION['contrat']['dateini'];
if (!empty($_SESSION['newContrat']['dateinisel'])) $dateinisel = $_SESSION['contrat']['dateinisel'];
if (!empty($_SESSION['newContrat']['datefin'])) $datefin = $_SESSION['contrat']['datefin'];
if (!empty($_SESSION['newContrat']['datefinsel'])) $datefinsel = $_SESSION['contrat']['datefinsel'];

$private=GETPOST("private","int");
if (! isset($_GET['private']) && ! isset($_POST['private'])) $private=GETPOST('private','int');
if (empty($private)) $private=0;

$formfile = new Formfile($db);
$form = new Form($db);
$formproduct = new FormProduct($db);
if ($fk_entrepot)
{
	$res = $objecten->fetch($fk_entrepot);
}
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';


//$aArrjs = array('almacen/javascript/recargar.js');
//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Report"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Balanceactual"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
		is_private='.$private.';
		if (is_private) {
			$(".trdi").hide();
			$(".trdf").hide();
		} else {
			$(".trdi").show();
			$(".trdf").show();
		}
		$("#dateyes").click(function() {
			$(".trdf").show();
			$(".trdi").show();
			document.formsoc.private.value=0;
		});
		$("#dateno").click(function() {
			$(".trdi").hide();
			$(".trdf").hide();
			document.formsoc.private.value=1;
		});
	});';
	print '</script>'."\n";
}
print '<form id="formsoc" name="formsoc" action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';

dol_fiche_head();
print '<table class="border" width="100%">';
// Entrepot Almacen
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
$empty = 1;
$event = array();
$morecss = 'minwidth200';
$showfullpath = 1;
print $formproduct->selectWarehouses(GETPOST('fk_entrepot'),'fk_entrepot',$filterstatus,$empty,0,$fk_product,$empty_label,$showstock,$forcecombo,$events,$morecss,$exclude,$showfullpath);

print '</td></tr>';
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Product').'</td><td colspan="3">';

print $form->select_produits(GETPOST('idprod'),'idprod','',0,0,1,2,'',1,array());
print '</td></tr>';


print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Category').'</td><td colspan="3">';

if ($ver == 1) $type = "'product'";
if ($ver == 2) $type = 'product';
print $form->select_all_categories($type,GETPOST('fk_category'),'fk_category',64,0,0);
print '</td></tr>';

/*
//seleccionar fechas
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Filtrar por fecha').'</td><td colspan="3">';
print '<div id="selectthirdpartytype">';
print '<label for="dateyes">';
print '<input type="radio" id="dateyes" class="flat" name="private"  value="0"'.($private?'':' checked').'>';
print '&nbsp;';
print $langs->trans("Yes");
print '</label>';
print ' &nbsp; &nbsp; ';
print '<label for="dateno">';
$text ='<input type="radio" id="dateno" class="flat" name="private" value="1"'.($private?' checked':'').'>';
$text.='&nbsp;';
print $text.= $langs->trans("No");
print '</label>';
print '</div>';
print '</td></tr>';

// desde fecha
print '<tr class="trdi"><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').' '.$langs->trans('OfDelivery').'</td><td colspan="3">';
$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);
print '</td></tr>';

// hasta fecha
print '<tr class="trdf"><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').' '.$langs->trans('OfDelivery').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);
print '</td></tr>';
*/
print '</table>';
dol_fiche_end();
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

if (($action == 'edit' || $action=='edits'))
{
	$params='';
	if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
	$params.='&action='.$action;
	print '<br>';

		//armamos un array para el reporte
	$entrepotstatic=new Entrepot($db);

	if ($fk_entrepot>0)
	{
		$aContrat['fk_entrepot'] = $fk_entrepot+0;
		$entrepotstatic->fetch($fk_entrepot);
		$aContrat['entrepot'] = $entrepotstatic->lieu;
	}
	if ($idprod>0)
	{
		$product->fetch($idprod);
		$unit = $product->getLabelOfUnit('short');
		$aContrat['fk_product'] = $idprod;
		$aContrat['productref'] = $product->ref;
		$aContrat['productlabel'] = $product->label;
		$aContrat['unit'] = $unit;

	}
	$aContrat['dateini'] = $_SESSION['contrat']['dateinisel'];
	$aContrat['datefin'] = $_SESSION['contrat']['datefinsel'];

	$aContrat['lines'] = array();


	//listamos todos los productos
	//movimiento del producto
	$sql  = "SELECT p.rowid, p.ref, p.label, p.stock";
	if ($fk_entrepot > 0)
		$sql.= ", ps.reel ";
	$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
	if ($fk_entrepot > 0)
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON ps.fk_product = p.rowid AND ps.fk_entrepot = ".$fk_entrepot;
	$sql.= " WHERE ";
	$sql.= " p.entity = ".$conf->entity;
	if ($idprod>0)
		$sql.= " AND p.rowid = ".$idprod;

	$sql.= " ORDER BY $sortfield $sortorder";
	$result = $db->query($sql);
	$aEntrepot = array();
	if ($result)
	{
		$num = $db->num_rows($result);

		$i=0;
		$var=false;
		while ($j < $num)
		{
			$obj = $db->fetch_object($result);
			$product->id = $obj->rowid;
			$product->load_stock($option='');
			$aCategorie = array();
			if ($fk_category > 0)
			{
				$cats = $categorie->containing($product->id, 'product', 'id');
				if ($ver == 1)
				{
					//es objeto
					foreach ($cats AS $a => $objcat)
					{
						$aCategorie[$objcat->id] = $objcat->id;
					}
				}
				else
				{
					foreach ($cats AS $k => $fk_cat)
					{
						$aCategorie[$fk_cat] = $fk_cat;
					}
				}
			}
			foreach ((array) $product->stock_warehouse AS $fk => $row)
			{
				if ($fk_entrepot > 0)
				{
					if ($fk_entrepot == $fk)
					{
						if ($fk_category>0)
						{
							if ($aCategorie[$fk_category])
							{
								$aWarehouse[$obj->rowid][$fk] = $row->real;
								$aEntrepot[$fk] = $fk;
							}
						}
						else
						{
							$aWarehouse[$obj->rowid][$fk] = $row->real;
							$aEntrepot[$fk] = $fk;
						}
					}
				}
				else
				{
					if ($fk_category>0)
					{
						if ($aCategorie[$fk_category])
						{
							$aWarehouse[$obj->rowid][$fk] = $row->real;
							$aEntrepot[$fk] = $fk;
						}
					}
					else
					{
						$aWarehouse[$obj->rowid][$fk] = $row->real;
						$aEntrepot[$fk] = $fk;
					}
				}
			}
			$j++;
		}
		//print_r($aWarehouse);

	}
	else
		dol_print_error($db);

	//armamos la cabecera de entrepot
	$aEntrepotdata = array();
	foreach ((array) $aEntrepot AS $fk)
	{
		$entrepotstatic->fetch($fk);
		$aEntrepotdata[$fk]=array('id'=>$entrepotstatic->id, 'label'=>$entrepotstatic->lieu);
		$aEntrepotOrder[$fk] =$entrepotstatic->lieu;
	}
	if (count($aEntrepotdata)>0)
	{
		array_multisort($aEntrepotOrder, SORT_ASC,$aEntrepotdata);
	}
	//armamos la solucíon
	dol_fiche_head();
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
	print '<tr class="liste_titre">';
    //
	print_liste_field_titre($langs->trans("Ref"),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Product"),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	foreach((array) $aEntrepotdata  AS $i => $data)
	{
		print_liste_field_titre($data['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	}
	print_liste_field_titre($langs->trans("Total"),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print '<tr>';
	$aContrat['aEntrepotdata'] = $aEntrepotdata;
	$aContrat['lines'] = array();
	//armamos el cuerpo
	$aTotal = array();
	foreach ((array) $aWarehouse AS $fk_product => $aEntrepot)
	{
		$product->fetch($fk_product);
		$var = !$var;
		print "<tr $bc[$var]>";
		print '<td>'.$product->getNomUrl(1).'</td>';
		print '<td>'.$product->label.'</td>';
		$aContrat['lines'][$fk_product]['ref'] = $product->ref;
		$aContrat['lines'][$fk_product]['label'] = $product->label;
		$totalline = 0;
		foreach ($aEntrepotdata As $j => $data)
		{
			print '<td align="right">'.price(price2num($aEntrepot[$data['id']],'MU')).'</td>';
			$aContrat['lines'][$fk_product][$data['id']] = $aEntrepot[$data['id']];

			$totalline+=price2num($aEntrepot[$data['id']],'MU');
			$aTotal[$data['id']]+=price2num($aEntrepot[$data['id']],'MU');
		}
		print '<td align="right">'.price(price2num($totalline,'MT')).'</td>';
		$aContrat['lines'][$fk_product]['total'] = price2num($totalline,'MT');
		print '<tr>';
	}
	//imprimimos totales
	print '<tr class="liste_total">';
	print '<td colspan="2">'.$langs->trans('Total').'</td>';
	foreach ($aEntrepotdata As $j => $data)
	{
		print '<td align="right">'.price(price2num($aTotal[$data['id']],'MT')).'</td>';
		$sumaTotal+= price2num($aTotal[$data['id']],'MT');
	}
	print '<td align="right">'.price(price2num($sumaTotal,'MT')).'</td>';
	print '</tr>';

	$_SESSION['aBalanceentrepot'] = serialize($aContrat);
	print "</table>";
	dol_fiche_end();


	print "<div class=\"tabsAction\">\n";
	if (count($aContrat)>0)
	{
		//if ($user->rights->poa->area->crear)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';
		//else
		//	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	}
	print '</div>';

}

$db->close();

llxFooter();
?>
