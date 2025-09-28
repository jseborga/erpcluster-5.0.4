<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/almacen/inventario/inventario.php
 *      \ingroup    almacen
 *      \brief      Page calculo del saldos de productos
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenlog.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

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

$langs->load("almacen");
$langs->load("stocks");

if (!$user->rights->almacen->rep->read) accessforbidden();

$action = GETPOST('action');

$seluser = GETPOST('seluser');
$status = GETPOST('status');
$aStatus = array(
	-9=>$langs->trans('Todos'),
	-2=>$langs->trans('StatusOrderRejected'),
	-1=>$langs->trans('StatusOrderCanceled'),
	0=>$langs->trans('StatusOrderDraft'),
	1=>$langs->trans('StatusOrderValidated'),
	6=>$langs->trans('StatusOrderApproved'),
	2=>$langs->trans('StatusOrderSent'),
	//3=>$langs->trans('StatusOrderToBill'),
	//4=>$langs->trans('StatusOrderProcessed'),
	5=>$langs->trans('StatusOrderoutofstock')
	);

if (empty($id)) $id = $_SESSION['idEntrepot'];

$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
$lGestion = false;
if (!empty($typemethod)) $lGestion = true;

//verificamos el periodo
verif_year();

$object = new Solalmacenext($db);
$objSollog = new Solalmacenlog($db);
$objUser = new User($db);

$lGestion = $conf->global->ALMACEN_FILTER_YEAR;

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

if ($action == 'excel')
{
	include_once DOL_DOCUMENT_ROOT.'/almacen/lib/format_excel.lib.php';

	$aCell = array(3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W');
	$aTimeprocess = unserialize($_SESSION['aTimeprocess']);
	$aReport = $aTimeprocess['lines'];
	//PRCESO 1
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	//armamos la cabecera
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('REPORTE TIEMPOS DE PROCESO'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
	$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('De fecha'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B2',dol_print_date($aTimeprocess['dateini'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('Hasta fecha'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aTimeprocess['datefin'],'day'));

	//titulos
	$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Ref'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B5',$langs->trans('Detail'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C5',$langs->trans('Datehour'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D5',$langs->trans('Statut'));
	$objPHPExcel->getActiveSheet()->SetCellValue('E5',$langs->trans('User'));
	//FORMATO
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
	//cambiamos de fila
	$line = 6;
	$aTotal = array();
	foreach ($aReport AS $j => $row)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['description']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,dol_print_date($row['datec'],'dayhour'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['namestatus']);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['lastname'].' '.$row['firstname']);
		$line++;
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

	//$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
	//
	//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/timeprocess.xlsx");

	header('Location: '.DOL_URL_ROOT.'/almacen/report/fiche_export.php?archive=timeprocess.xlsx');
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

if ($action == 'edit')
{
	if (isset($_POST['diyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));
		$dategesini = dol_mktime(0, 0, 1, 1, 1, GETPOST('diyear'));
		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 59, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
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
				setEventMessages($langs->trans('La fecha inicio es menor al permitido').' '.dol_print_date($dateinisel,'day').' < '.dol_print_date($dateinimin,'day'),null,'errors');
				$dateinisel = $dateinimin;
			}
		}
	}
}
else
	unset($_SESSION['inventory']);

if (!empty($_SESSION['invdateini']))
{
	$dateinisel = $_SESSION['invdateini'];
	$datefinsel = $_SESSION['invdatefin'];
}



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


$entrepot = new Entrepot($db);
$form = new Form($db);
$formfile = new Formfile($db);
$product = new Product($db);

$hookmanager->initHooks(array('almacen'));
//print_r($hookmanager);





$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("OrderProcessingTimes"),$help_url);

print_fiche_titre($langs->trans("OrderProcessingTimes"));


print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';

print '<table class="border" width="100%">';

print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);

print '</td></tr>';

// hasta fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);
print '</td></tr>';
// user
print '<tr><td width="25%" >'.$langs->trans('User').'</td><td colspan="3">';
$form->select_users($seluser,'seluser',1,'');
print '</td></tr>';
// status
print '<tr><td width="25%" >'.$langs->trans('Statut').'</td><td colspan="3">';
print $form->selectarray('status',$aStatus,$status,0);
print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';
if ($action == 'edit' && !$error)
{
	$aRowid = array();

	//movimiento de salidas y entradas
	$filter = " AND t.date_creation BETWEEN ".$db->idate($dateinisel)." AND ".$db->idate($datefinsel);
	$res = $object->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filter);

	$nbtotalofrecords = $res;

	if ($res)
	{
		$lines = $object->lines;
		$title = $langs->trans('Order Processing Times');
		dol_fiche_head();
		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Ref"),$_SERVER['PHP_SELF'], "p.ref","",$params,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Description"),$_SERVER['PHP_SELF'], "","",$params,'align="left"');
		print_liste_field_titre($langs->trans("Date"),$_SERVER['PHP_SELF'], "","",$params,'align="center"');
		print_liste_field_titre($langs->trans("Log"),"", "","",$params,'align="center"');
		print "</tr>\n";

		foreach  ($lines AS $j => $obj)
		{
			$object->id = $obj->id;
			$object->ref = $obj->ref;
			$object->label = $obj->label;
			$object->statut = $obj->statut;

			$filterlog = '';
			//buscamos el log
			if ($seluser>0) $filterlog.= " AND t.fk_user_create = ".$seluser;
			if ($status<>-9) $filterlog.= " AND t.status = ".$status;
			$filterlog.= " AND t.fk_solalmacen = ".$obj->id;
			$reslog = $objSollog->fetchAll('ASC','datec',0,0,array(1=>1),'AND',$filterlog);
			$lPrint = false;
			if ($reslog>0) $lPrint = true;

			if ($lPrint)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td>'.$object->getNomUrl(1).'</td>';
				print '<td>'.$obj->description.'</td>';
				print '<td align="center">'.dol_print_date($obj->date_creation,'day').'</td>';

				print '<td align="center">';
				if ($reslog>0)
				{
					$lineslog = $objSollog->lines;
					print '<table class="noborder" width="100%">';
					foreach ((array) $lineslog  AS $k => $linel)
					{
						print '<tr>';
						print '<td width="33%">'.dol_print_date($linel->datec,'dayhour').'</td>';
						print '<td width="33%">'.$linel->description.'</td>';
						$objUser->fetch($linel->fk_user_create);
						print '<td width="33%">'.$objUser->getNomUrl(1).'</td>';
						print '</tr>';
						$aReport[] = array('ref'=>$object->ref, 'description'=>$obj->description,'date'=>$obj->date_creation,'datec'=>$linel->datec,'namestatus'=>$linel->description,'user'=>$objUser->login,'lastname'=>$objUser->lastname, 'firstname'=>$objUser->firstname);
					}
					print '</table>';
				}
				else
				{
					$aReport[] = array('ref'=>$object->ref, 'description'=>$obj->description,'date'=>$obj->date_creation,'datec'=>'','namestatus'=>'','user'=>'','userlast'=>'', 'userfirst'=>'');
				}
				print '</td>';
				print '</tr>';
			}
		}
		print "</table>";
		dol_fiche_end();

		//serializando
		$_SESSION['aTimeprocess'] = serialize(array('dateini'=>$dateinisel,'datefin'=>$datefinsel,'lines'=>$aReport));

		print "<div class=\"tabsAction\">\n";
		if (count($aReport)>0)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';
		print '</div>';
	}


	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE) && $abc)
	{
		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}

		$model='inventario';
		if ($user->rights->almacen->inv->lirev && $yesnoprice)
			$model='inventarioval';
		$objinv->id = $id;

		$result=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result < 0) dol_print_error($db,$result);
	}

}

if ($abc)
{
	print '<div class="tabsAction">';
	//documents
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';
// ancre
// Documents generes
	$filename=dol_sanitizeFileName($entrepot->libelle).'/inv';
		//cambiando de nombre al reporte
	$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($entrepot->libelle).'/inv';
	$urlsource=$_SERVER['PHP_SELF'].'?id='.$id.'&yesnoprice='.$yesnoprice;
	$genallowed=$user->rights->almacen->creardoc;
	$genallowed=false;
	if (empty($_SESSION['inventorydet']))
		$genallowed=false;
	$delallowed=$user->rights->almacen->deldoc;
	print '<br>';
	print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	$somethingshown=$formfile->numoffiles;
	print '</td></tr>';
	print '</table>';

	print "</div>";
}
$db->close();

llxFooter();
?>
