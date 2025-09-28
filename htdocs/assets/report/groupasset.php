<?php
/* Copyright (C) 7102 No One <example@email.bo>
*/
require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");

require_once(DOL_DOCUMENT_ROOT."/assets/core/modules/assets/modules_assets.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsmovext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsgroup.class.php");

require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsbeen.class.php';

require_once(DOL_DOCUMENT_ROOT."/assets/lib/assets.lib.php");
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';

		//require_once(DOL_DOCUMENT_ROOT."/assets/assignment/class/assetsassignmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';


require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartament.class.php';

dol_include_once('/assets/class/assetsbalanceext.class.php');

$langs->load("assets");
$langs->load("stocks");

//$langs->load("fabrication@fabrication");

if (!$user->rights->assets->repinv->write) accessforbidden();

$fk_departament = GETPOST('fk_departament');
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$date_ini = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin = dol_mktime(23,59,59,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

if(empty($date_ini)) $date_ini = dol_now();
if(empty($date_fin)) $date_fin = dol_now();


$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['kardexfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);
$fk_entrepot = $_SESSION['kardexfk_entrepot'];

		//filtramos por almacenes designados segun usuario
$objAssets = new Assetsext($db);
$objGroup  = new cAssetsgroup($db);
$objbeen   = new Cassetsbeen($db);
$object    = new Assetsassignmentext($db);
$objAss    = new Assetsassignmentext($db);
$objAssdet = new Assetsassignmentdetext($db);
$objAdherent = new Adherent($db);
$objmov     = new Assetsmovext($db);
$objuser = new User($db);

$objDepartament = new Pdepartament($db);

$aFilterent = array();
$id = $_SESSION['kardexid'];


		//actions
$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();

if ($action == 'builddoc')
// En get ou en post
{
	$res = $objGroup->fetch($fk_group);

			//print_r($objGroup);exit;

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
	$objGroup->modelpdf = GETPOST('model');
	$result=assets_pdf_create($db, $objGroup, $objGroup->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

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

/*if ($action == 'edit')
{
	$error=0;
	if (isset($_POST['diyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(0, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));

				//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 50, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(0, 0, 0, $dfmonth,  $dfday,  GETPOST('dfyear'));
				//if ($dateinisel <= $datefinsel)
				//{
		unset($_SESSION['assetsinv']);
		$_SESSION['assetsinv']['dateini'] = $dateini;
		$_SESSION['assetsinv']['dateinisel'] = $dateinisel;
		$_SESSION['assetsinv']['datefin'] = $datefin;
		$_SESSION['assetsinv']['datefinsel'] = $datefinsel;
				//}
				//else
				//{
				//	$error++;
				//	setEventMessages($langs->trans("Errordatenovalid", GETPOST('id')),null, 'errors');
				//}
		if (empty($error))
			setEventMessages($langs->trans("Proceso satisfactorio"),null,'mesgs');

				//echo $dateinisel.' '.dol_print_date($dateinisel,'day');
				//print_r($_POST);
	}
}*/

if (!empty($_SESSION['assetsinv']['dateini'])) $dateini = $_SESSION['assetsinv']['dateini'];
if (!empty($_SESSION['assetsinv']['dateinisel'])) $dateinisel = $_SESSION['assetsinv']['dateinisel'];
if (!empty($_SESSION['assetsinv']['datefin'])) $datefin = $_SESSION['assetsinv']['datefin'];
if (!empty($_SESSION['assetsinv']['datefinsel'])) $datefinsel = $_SESSION['assetsinv']['datefinsel'];

// armado de excel

if ($action == 'reporteExcel')
{
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

	$aGral = unserialize($_SESSION['aGral']);
	$aExtras = unserialize($_SESSION['aExtras']);
	//print_r($aReportdetasset);

	// TITULO
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);
	//$this->activeSheet->getDefaultRowDimension()->setRowHeight($height);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getStyle('A2')->getFont()->setSize(15);
	$sheet->mergeCells('A2:J2');
	$sheet->setCellValueByColumnAndRow(0,2, $langs->trans('Reportgroupasset'));
	$sheet->mergeCells('A2:J2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

	// COLOR DEL TITULO
	$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray(
		array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FF0000'),
				'size'  => 15,
				'name'  => 'Verdana'
			)));


	//PIE DE PAGINA

	// ENCABEZADO
	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('B3',$langs->trans("Tothe"));
	$objPHPExcel->getActiveSheet()->setCellValue('C3',dol_print_date($aExtras['date_ini'],'daytext'));
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);



 // COLOR DEL ENCABEZADO
	$objPHPExcel->getActiveSheet()->getStyle('B3:C7')->applyFromArray(
		array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '0c78bf'),
				'size'  => 12,
				'name'  => 'Verdana'
			)));

	//Cabecera de la hoja electronica
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("Nro"));
	$objPHPExcel->getActiveSheet()->setCellValue('B8',$langs->trans("Accountinggroup"));
	$objPHPExcel->getActiveSheet()->setCellValue('C8',$langs->trans("Quantity"));
	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("Usefullife"));
	$objPHPExcel->getActiveSheet()->setCellValue('E8',html_entity_decode($langs->trans("Historicalcost")));
	$objPHPExcel->getActiveSheet()->setCellValue('F8',html_entity_decode($langs->trans("Updatedcost")));
	$objPHPExcel->getActiveSheet()->setCellValue('G8',html_entity_decode($langs->trans("Depreciationacumulated")));
	//$objPHPExcel->getActiveSheet()->setCellValue('H8',$langs->trans("ValorAct"));
	//$objPHPExcel->getActiveSheet()->setCellValue('I8',$langs->trans("DepreciationAcum"));
	$objPHPExcel->getActiveSheet()->setCellValue('H8',$langs->trans("Balance"));


 // TABLA COLOR

	$objPHPExcel->getActiveSheet()->getStyle('A8:H8')->applyFromArray(
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
					'argb' => 'FF0000'
				),
				'endcolor'   => array(
					'argb' => 'bfb70c'
				)
			)
		)
	);



 //tama;o de las columnas
 //$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);


 // CUERPO
	$j=9;
	$contt=1;
	foreach ((array) $aGral AS $i => $lines)
	{

			// VISTA
		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
		->setCellValue('B' .$j,$lines['grupo'])
		->setCellValue('C' .$j,$lines['cantidad'])
		->setCellValue('D' .$j,$lines['util'])
		->setCellValue('E' .$j,$lines['cHistorico'])
		//->setCellValue('F' .$j,$lines['cActInicial'])
		//->setCellValue('G' .$j,$lines['dAcuTotal'])
		->setCellValue('F' .$j,$lines['cValorac'])
		->setCellValue('G' .$j,$lines['cDepreacuml'])
		->setCellValue('H' .$j,$lines['saldo']);

		$objPHPExcel->getActiveSheet()->getStyle('E' .$j)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('F' .$j)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G' .$j)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('H' .$j)->getNumberFormat()->setFormatCode('#,##0.00');
		//$objPHPExcel->getActiveSheet()->getStyle('I' .$j)->getNumberFormat()->setFormatCode('#,##0.00');
		//$objPHPExcel->getActiveSheet()->getStyle('J' .$j)->getNumberFormat()->setFormatCode('#,##0.00');
		$contt++;


	// BORDES DE LA VISTA
		$objPHPExcel->getActiveSheet()->getStyle('A8:H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$j++;


	}

	$objPHPExcel->setActiveSheetIndex(0);
 // Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/groupAsset.xlsx");
	header("Location: ".DOL_URL_ROOT.'/assets/report/fiche_export.php?archive=groupAsset.xlsx');
	//echo "Llega esta en el foreach";exit;
}

$formfile = new Formfile($db);
$form = new Formv($db);
		//$aArrjs = array('almacen/javascript/recargar.js');
		//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';

		//llxHeader("",$langs->trans("Inventario"),$help_url,'','','',$aArrjs,$aArrcss);
llxHeader("",$langs->trans("Summarybygroup"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Summarybygroup"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';

//Depresiacion
print '<table class="border" width="100%">';
print '<tr>';
print '<td width="25%" class="fieldrequired">'.$langs->trans('Tothe').'</td><td colspan="3">';
print $form->select_date($date_ini,'di_',0,0,1);
print '</td>';
print '</tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';

//YEMER

if(($action == 'edit'||  $action=='edits'))
{
	$aTotal = array();
	$aGral = array();
	$aDate = dol_getdate($date_ini);
	$day = $aDate['mday'];
	$month =$aDate['mon'];
	$year = $aDate['year'];

	$country = $conf->global->ASSETS_CURRENCY_DEFAULT;
	$nRg = $objGroup->fetchAll("ASC","code",0,0,array("active"=>1),"AND");
	if($nRg > 0)
	{
		$i = 1;


		foreach ($objGroup->lines as $key => $value) {
			$aGral[$i]['grupo'] = $value->label;
			$aGral[$i]['util'] = $value->useful_life;
			$code = $value->code;
			//Consulta
			$sql  = "SELECT ";
			$sql .= "a.rowid, a.coste ";
			$sql .= "FROM ".MAIN_DB_PREFIX."assets as a ";
			$sql .= "WHERE  a.type_group = '".$code."'";
			$sql.= " AND a.statut >=1";
			//$sql .= "WHERE  a.type_group = ". $code;

			$res = $db->query($sql);
			if (! $res){
				dol_print_error($db);
				exit;
			}
			else
			{
				$aGral[$i]['cantidad'] = $db->num_rows($res);
				$aTotal['cantidad']+=$db->num_rows($res);

				$ch = 0; //Costo Historico
				$cai = 0;//Costo actual inicial
				$cat = 0;//Dep actual total
				$cab = 0;// valor actualizado
				$cda = 0;// valor depreciacion acumulada

				$saldo = 0;//Saldo
				$a = 0;

				while ($a < $db->num_rows($res))
				{

					$objasset = $db->fetch_object($res);
					//print_r($objasset);exit;
					$ch = $ch + ($objasset->coste);

					$r = $objmov->process_depr($month,$year,$country,$code,$day,$objasset->rowid);
					if ($r > 0)
					{
						$aArray = $objmov->array[$objasset->rowid];

						//$aArray['coste']
						$cai = $cai + $aArray['amount_update'];


						//$aArray['amount_depr'];
						//$aArray['amount_depr_acum'];
						$cat = $cat + $aArray['amount_depr_acum_update'];
						//$aArray['amount_balance']
						//$aArray['amount_balance_depr'];


						// valor actualizado
						$cab = $cab + $aArray['amount_balance'];
						// depreciacion acumulada
						$cda = $cda + $aArray['amount_balance_depr'];
						// saldo
						$saldo = $saldo + ($aArray['amount_balance']-$aArray['amount_balance_depr']);
					}
					$a++;
				}
				$aTotal['ch']+=$ch;
				$aTotal['cai']+=$cai;
				$aTotal['cat']+=$cat;
				$aTotal['cab']+=$cab;
				$aTotal['cda']+=$cda;
				$aTotal['saldo']+=$saldo;
			}
			$aGral[$i]['cHistorico'] = $ch;
			$aGral[$i]['cActInicial'] = $cai;
			$aGral[$i]['dAcuTotal'] = $cat;
			$aGral[$i]['cValorac'] = $cab;
			$aGral[$i]['cDepreacuml'] = $cda;
			$aGral[$i]['saldo'] = $saldo;
			$i++;
		}
	}

	/*echo "<pre>";
	echo print_r($aGral);
	echo "</pre>";
	exit;*/


	print_barre_liste($langs->trans("Listassets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="border" width="100%">';
	print '<tr class="liste_titre">';
	print '<td align="center">'.$langs->trans("Nro").'</td>';
	print '<td align="center">'.$langs->trans("Accountinggroup").'</td>';
	print '<td align="center">'.$langs->trans("Quantity").'</td>';
	print '<td align="center">'.$langs->trans("Usefullife").'</td>';
	print '<td align="center">'.$langs->trans("Historicalcost").'</td>';
	//print '<td align="center">'.$langs->trans("Initialcurrentcost").'</td>';
	//print '<td align="center">'.$langs->trans("Accumulatedtutal").'</td>';
	print '<td align="center">'.$langs->trans("Updatecost").'</td>';
	print '<td align="center">'.$langs->trans("Accumulateddepreciation").'</td>';
	print '<td align="center">'.$langs->trans("Balance").'</td>';
	print '</tr>';

	$Cont=1;
	foreach ($aGral AS $i => $valor)
	{

		$var = !$var;
		print "<tr $bc[$var]>";

		print '<td>'.$Cont.'</td>';
		print '<td>'.$valor['grupo'].'</td>';
		print '<td align="center">'.$valor['cantidad'].'</td>';
		print '<td align="center">'.$valor['util'].'</td>';
		print '<td align="right">'.price(price2num($valor['cHistorico'],'MT')).'</td>';
		//print '<td align="right">'.price(price2num($valor['cActInicial'],'MT')).'</td>';
		//print '<td align="right">'.price(price2num($valor['dAcuTotal'],'MT')).'</td>';
		print '<td align="right">'.price(price2num($valor['cValorac'],'MT')).'</td>';
		print '<td align="right">'.price(price2num($valor['cDepreacuml'],'MT')).'</td>';
		print '<td align="right">'.price(price2num($valor['saldo'],'MT')).'</td>';

		print '</tr>';
		$Cont++;
	}
		//totales
		//
	print '<tr class="titre_total">';
	print '<td colspan="2">'.$langs->trans('Total').'</td>';
	print '<td align="center">'.$aTotal['cantidad'].'</td>';
	print '<td align="right">'.'</td>';
	print '<td align="right">'.price(price2num($aTotal['ch'],'MT')).'</td>';
	//print '<td align="right">'.price(price2num($aTotal['cai'],'MT')).'</td>';
	//print '<td align="right">'.price(price2num($aTotal['cat'],'MT')).'</td>';
	print '<td align="right">'.price(price2num($aTotal['cab'],'MT')).'</td>';
	print '<td align="right">'.price(price2num($aTotal['cda'],'MT')).'</td>';
	print '<td align="right">'.price(price2num($aTotal['saldo'],'MT')).'</td>';

	print '</tr>';
	print '</table>';


	$_SESSION['aGral'] = serialize($aGral);


	print "<div class=\"tabsAction\">\n";
	print '<a class="butAction"  href="'.$_SERVER['PHP_SELF'].'?action=">'.$langs->trans("Volver").'</a>';
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';

	print '</div>';
			//generar archivo pdf
	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
	{
		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				//if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $objAssets->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
	}

	print '<div class="tabsAction">';
		//documents
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';

	$diradd = '';
	$filename = 'groupasset';
	$nombre ="";
	$filedir=$conf->assets->dir_output."/".$filename;

	$date_ini = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		//Array extra
	$aExtras = array("filename"=>$filename,"filedir"=>$filedir,"nombre"=>$cNombre,"date_ini"=>$date_ini,"date_fin"=>$date_fin);
	$_SESSION['aExtras'] = serialize($aExtras);

	$objDepartament->modelpdf = 'groupasset';


	$result=$object->generateDocument($objDepartament->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);

	$urlsource=$_SERVER['PHP_SELF'];

	$genallowed=0;
	$delallowed=0;

	print '<br>';
	print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objDepartament->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	$somethingshown=$formfile->numoffiles;
	print '</td></tr></table>';
	print "</div>";

	//}

}
$db->close();

llxFooter();
?>

