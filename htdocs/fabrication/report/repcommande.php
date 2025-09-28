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
 *      \file       htdocs/fabrication/repcommande.php
 *      \ingroup    fabrication
 *      \brief      Page Liste commande status 1 and 2
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/commande/class/commande.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationext.class.php");

require_once(DOL_DOCUMENT_ROOT."/fabrication/core/modules/fabrication/modules_fabrication.php");
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

if ($ver != 1)
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
$langs->load("fabrication@fabrication");
//$langs->load("fabrication@fabrication");

//if (!$user->rights->sales->rep->lire) accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action = GETPOST('action','alpha');
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="c.date_livraison";
if (! $sortorder) $sortorder="DESC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['contratfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);

$fk_entrepot = $_SESSION['contratfk_entrepot'];
$idprod = GETPOST('idprod');

//filtramos por almacenes designados segun usuario
$objecten = new Entrepot($db);
$object = new Commande($db);
$objFabrication = new Fabricationext($db);

$objuser = new User($db);

$aFilterent = array();
$product = new Product($db);




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

if ($action == 'edit')
{
	$error=0;
	$product = new Product($db);
	if ((isset($_GET['idprod']) && $_GET['idprod']>0) || (isset($_POST['idprod']) && $_POST['idprod']>0) || (isset($_POST['search_idprod']) && !empty($_POST['search_idprod'])))
	{
		$idprod = GETPOST('idprod');
		$search_idprod = GETPOST('search_idprod');
		if ($idprod>0 || (!empty($search_idprod) || !is_null($search_idprod)))
		{
			$res = $product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''));
			if ($product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''))>0)
			{
				$_GET['idprod'] = $product->id;
				$_POST['idprod'] = $product->id;
				$idprod = $product->id;
			}
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

if (!empty($_SESSION['contrat']['dateini'])) $dateini = $_SESSION['contrat']['dateini'];
if (!empty($_SESSION['contrat']['dateinisel'])) $dateinisel = $_SESSION['contrat']['dateinisel'];
if (!empty($_SESSION['contrat']['datefin'])) $datefin = $_SESSION['contrat']['datefin'];
if (!empty($_SESSION['contrat']['datefinsel'])) $datefinsel = $_SESSION['contrat']['datefinsel'];

if ($action == 'excel')
{

	include_once DOL_DOCUMENT_ROOT.'/fabrication/lib/format_excel.lib.php';

	$parameter = unserialize($_SESSION['parameter']);
	$aData = unserialize($_SESSION['aCommanderep']);
	$aCommande = $aData['lines'];

	$aCell = array(3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W');
	//$aReport = unserialize($_SESSION['aBalanceentrepot']);

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	if (count($aCommande) >0)
	{
		$line = 1;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Entrepot'));
		if ($parameter['fk_entrepot']>0)
		{
			$objecten->fetch($parameter['fk_entrepot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$objecten->label);
		}
		else
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('All'));
		$line++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Product'));
		if ($parameter['fk_product']>0)
		{
			$product->fetch($parameter['fk_product']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$product->ref.' '.$product->label);
		}
		else
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('All'));
		$line++;
		if (!$parameter['private'])
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('dateini'));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($aData['dateini'],'day'));
			$line++;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('datefin'));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($aData['datefin'],'day'));
			$line++;

		}

		$line = 5;
		//armamos la cabecera
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Ref'));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('Entrepot'));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$langs->trans('Date'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$langs->trans('Product'));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans('Qty'));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans('Description'));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$langs->trans('Notepublic'));
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$langs->trans('Noteprivate'));

		$line++;
		//armamos el cuerpo
		foreach ((array) $aCommande AS $j => $data)
		{
			$sTotal = 0;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$data['ref']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$data['entrepot']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,dol_print_date($data['date'],'dayhour'));
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$data['label']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$data['qty']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$data['description']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$data['note_public']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$data['note_private']);

			$line++;
		}
		//$objPHPExcel->setActiveSheetIndex(0);

		// guardamos
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/repcommande.xlsx");

	header("Location: ".DOL_URL_ROOT.'/fabrication/report/excel/fiche_export.php?archive=repcommande.xlsx');
}

$private=GETPOST("private","int");
if (! isset($_GET['private']) && ! isset($_POST['private'])) $private=GETPOST('private','int');
if (empty($private)) $private=0;

$formfile = new Formfile($db);
$form = new Form($db);
$formproduct = new FormProduct($db);
if ($fk_entrepot>0)
{
	$res = $objecten->fetch($fk_entrepot);
}
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';


//$aArrjs = array('almacen/javascript/recargar.js');
//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Report"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Commandereport"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

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
//print $form->select_produits_v(GETPOST('idprod'),'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','','',$filterstatic);

print $form->select_produits(GETPOST('idprod'),'idprod','',0,0,1,2,'',1,array());
print '</td></tr>';

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

print '</table>';
dol_fiche_end();
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

if (($action == 'edit' || $action=='edits'))
{
	$_SESSION['parameter'] = serialize($_POST);

	$params='';
	if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
	$params.='&action='.$action;
	$params.= '&fk_entrepot='.$fk_entrepot;
	$params.='&idprod='.$idprod;
	$params.='&private='.$private;
	print '<br>';
	dol_fiche_head();
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

	// Fields title
	print '<tr class="liste_titre">';
	//
	print_liste_field_titre($langs->trans("Ref"),$_SERVER['PHP_SELF'],'c.ref','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Warehouse"),$_SERVER['PHP_SELF'],'e.lieu','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Date"),$_SERVER['PHP_SELF'],'c.date_livraison','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Product"),$_SERVER['PHP_SELF'],'p.label','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Qty"),$_SERVER['PHP_SELF'],'cd.qty','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),$_SERVER['PHP_SELF'],'cd.description','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Notepublic"),$_SERVER['PHP_SELF'],'c.note_public','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Noteprivate"),$_SERVER['PHP_SELF'],'c.note_private','',$params,'',$sortfield,$sortorder);
	print '</tr>';

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
		$res = $product->fetch($idprod);
		if ($res <=0)
		{
			$error++;
			setEventMessages($product->error,$product->errors,'errors');
		}
		$unit = $product->getLabelOfUnit('short');
		$aContrat['fk_product'] = $idprod;
		$aContrat['productref'] = $product->ref;
		$aContrat['productlabel'] = $product->label;
		$aContrat['unit'] = $unit;
	}
	$aContrat['dateini'] = $_SESSION['contrat']['dateinisel'];
	$aContrat['datefin'] = $_SESSION['contrat']['datefinsel'];

	$aContrat['lines'] = array();

	$sql = "SELECT c.rowid, c.ref, cs.date_livraison, c.note_public, c.note_private, ";
	$sql.= " cs.date_livraison AS dateliv, cs.fk_entrepot_end, ";
	$sql.= " e.lieu AS entrepot, ";
	$sql.= " cd.fk_product, cd.label, cd.description, cd.qty, ";
	$sql.= " p.ref AS refproduct, p.label AS labelproduct ";
	$sql.= " FROM ".MAIN_DB_PREFIX."commande as c ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_sale as cs ON cs.fk_commande = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as e ON cs.fk_entrepot_end = e.rowid";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."commandedet as cd ON cd.fk_commande = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON cd.fk_product = p.rowid ";
	$sql.= " WHERE c.entity = ".$conf->entity;
	$sql.= " AND c.fk_statut > 0 AND c.fk_statut < 3";
	if ($private == 0)
	{
		if (! empty($dateini) && !empty($datefin))
		{
			$sql .= " AND c.date_livraison BETWEEN '".$db->idate($dateini)."' AND '".$db->idate($datefin)."'";
		}

	}
	if ($fk_entrepot>0) $sql.= " AND cs.fk_entrepot_end = ".$fk_entrepot;
	if ($idprod>0) $sql.=" AND cd.fk_product = ".$idprod;

	$sql.=$db->order($sortfield,$sortorder);

		//	$sql.= " ORDER BY c.date_livraison DESC ";

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$total=$totalwithpmp;
		$i=0;
		$var=false;
		while ($j < $num)
		{
			$obj = $db->fetch_object($resql);
			$product = new Product($db);

			$resstat = $entrepotstatic->fetch($obj->fk_entrepot_end);

			$object->id = $obj->rowid;
			$object->ref = $obj->ref;

			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$object->getNomUrl(1).'</td>';
			print '<td>'.$entrepotstatic->getNomUrl(1).'</td>';
			print '<td>'.dol_print_date($db->jdate($obj->date_livraison),'dayhour').'</td>';
			if ($obj->fk_product>0)
			{
				$product->fetch($obj->fk_product);
				print '<td>'.$product->getNomUrl(1).' '.$obj->labelproduct.'</td>';
				$label = $product->ref.' '.$product->label;
			}
			else
			{
				print '<td>'.$obj->label.'</td>';
				$label = $obj->label;
			}
			print '<td>'.$obj->qty.'</td>';
			print '<td>'.$obj->description.'</td>';
			print '<td>'.$obj->note_public.'</td>';
			print '<td>'.$obj->note_private.'</td>';
			print '</tr>'; ;
			$aContrat['lines'][$j]['ref'] = $obj->ref;
			$aContrat['lines'][$j]['entrepot'] = $entrepotstatic->lieu;
			$aContrat['lines'][$j]['date'] = $db->jdate($obj->date_livraison);
			$aContrat['lines'][$j]['label'] = $label;
			$aContrat['lines'][$j]['qty'] = $obj->qty;
			$aContrat['lines'][$j]['description'] = $obj->description;
			$aContrat['lines'][$j]['note_public'] = $obj->note_public;
			$aContrat['lines'][$j]['note_private'] = $obj->note_private;

			$j++;
		}
	}
	else
		dol_print_error($db);

	$_SESSION['aCommanderep'] = serialize($aContrat);
	print "</table>";
	dol_fiche_end();

	//boton para generar archivo excel
	print "<div class=\"tabsAction\">\n";
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Excel").'</a>';
	print '</div>';

	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	//if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	//$result=fabrication_pdf_create($db, $entrepotstatic, 'repcommande', $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

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

				$model='repcommande';

				$objFabrication->id = $id+0;
				$objFabrication->yesnoprice = $yesnoprice;
				$result=$objFabrication->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
		}




}



print '<div class="tabsAction">';
		//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';
		// ancre
//$objecten->fetch($fk_entrepot);
$diradd = '/rep';
$filename=$diradd;
		//cambiando de nombre al reporte
$filedir=$conf->fabrication->dir_output .$diradd;
$urlsource=$_SERVER['PHP_SELF'].'?action='.$action;
$genallowed=1;
$delallowed=1;
$modelpdf = 'repcommande';
print '<br>';
print $formfile->showdocuments('fabrication',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";

$db->close();

llxFooter();
?>
