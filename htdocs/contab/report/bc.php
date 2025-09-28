<?php

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';
//require_once DOL_DOCUMENT_ROOT.'/contab/core/modules/contab/modules_export.php';
require_once DOL_DOCUMENT_ROOT.'/contab/core/modules/contab/modules_contab.php';
//require_once DOL_DOCUMENT_ROOT.'/core/modules/export/modules_export.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/contabperiodoext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatdetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php");
if ($conf->global->CONTAB_USE_ACCOUNT_PLAN)
	require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php");
else
	require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php");

require_once DOL_DOCUMENT_ROOT . '/core/lib/accounting.lib.php';
require_once DOL_DOCUMENT_ROOT . '/contab/class/accountingaccountext.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';



$langs->load("contab");

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

// if (!$user->rights->contab->report->leer)
//   accessforbidden();


if (!isset($_SESSION['period_year']))
	$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now());
$pastmonthyear = $period_year;
$year_current = strftime("%Y",dol_now());
if ($pastmonthyear < $year_current) $pastmonth = 12;

if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}

$year = GETPOST('date_endyear');
$action = GETPOST('action','alpha');

if (empty($year)) $year = $year_current;
$date_ini  = dol_mktime(0, 0, 1, $conf->global->SOCIETE_FISCAL_MONTH_START,  1,  $year);
$date_end  = dol_mktime(23, 59, 59, GETPOST('date_endmonth'),  GETPOST('date_endday'),  GETPOST('date_endyear'));
if (empty($date_end)) // We define date_start and date_end
{
	$date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}
$datatoexport  = 'bc_'.date('Ymd').'_'.date('His');

$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$objperiod = new Contabperiodoext($db);
$form = new Form($db);
$htmlother = new FormOther($db);
$formfile = new FormFile($db);
$objmodelexport=new ModeleContab($db);
//$objmodelexport=new ModeleExports();
$objectdet = new Contabseatdetext($db);
$objAccountingaccount = new Accountingaccountext($db);
$objEntity = new Entity($db);

$objContab=new Contab($db);
$objexport=new Contab($db);
$objexport->load_arrays($user,$datatoexport);

$upload_dir = $conf->export->dir_temp.'/'.$user->id;

if ($action == 'builddoc')
{
	$array_selected        = $_SESSION['array_selected'];
	$array_selected_type   = $_SESSION['array_selected_type'];
	$array_selected_fields = $_SESSION['array_selected_fields'];
	$array_data            = $_SESSION['array_newdata'];


	// Build export file
	$result=$objexport->build_file($user, GETPOST('model','alpha'), $datatoexport, $array_selected, $array_data,$array_selected_type,$array_selected_fields);
	if ($result < 0)
	{
		setEventMessage($objexport->error, 'errors');
	}
	else
	{
		setEventMessage($langs->trans("FileSuccessfullyBuilt"));
		$sqlusedforexport=$objexport->sqlusedforexport;
	}
}

/*REPORTE EXCEL*/
if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);
	$objEntity->fetch($conf->entity);

	   //Manejo de estilos para las celdas
	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
			),
		),
	);
		//PROCESO 1
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
		//armamos la cabecera
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

		//TITULO
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',html_entity_decode($langs->trans("Balanceofsumsandbalances")));
		//$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

		//Encabezados

		//Izquierda
	$objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Nameentity'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',$objEntity->label);

	$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Tothe'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B5',dol_print_date($aReporte[6],'day'));

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B7')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);

		//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7');

		//FORMATO
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('Q3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('R3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('J7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('K7')->getFont()->setBold(true);




		//CABECERAS DE LA TABLA
	$objPHPExcel->getActiveSheet()->SetCellValue('A9',$langs->trans('Ref'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B9',$langs->trans('Name'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C9',html_entity_decode($langs->trans('Débito')));
	$objPHPExcel->getActiveSheet()->SetCellValue('D9',html_entity_decode($langs->trans('Crédito')));
	$objPHPExcel->getActiveSheet()->SetCellValue('E9',$langs->trans('Deudor'));
	$objPHPExcel->getActiveSheet()->SetCellValue('F9',$langs->trans('Acreedor'));


	$objPHPExcel->getActiveSheet()->getStyle('A9:F9')->applyFromArray(
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


	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

		//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
		//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$line = 10;
	$color = 1;
	foreach ( $aReporte[1] as $j => $row)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['nombre']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['debito']);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['credito']);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['deudor']);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['deudor1']);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

			//COLORES A LAS CELDAS



		$line++;
	}
		//fin f$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);oreach

	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans("Total"));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$aReporte[2]);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$aReporte[3]);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$aReporte[4]);
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$aReporte[5]);

	$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


	$objPHPExcel->getActiveSheet()->getStyle('C'.$line.":F".$line)->applyFromArray(
		array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'd0f4bc')
			)
		)
	);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/balanceSumaSaldo.xlsx");
	header('Location: '.DOL_URL_ROOT.'/contab/report/fiche_export.php?archive=balanceSumaSaldo.xlsx');
}



//action
$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Trialbalanceamountsandbalances"), $page, "bc.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"bc.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="generate">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date seat
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
print '</td></tr>';

print '</table>';
print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';

//proceso de consulta

$res = $objAccountingaccount->fetchAll('','',0,0,array(1=>1),'AND',$filter);

if ($res && $action=='generate')
{
	$lines = $objAccountingaccount->lines;

	$num = $res;
	$i = 0;

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"bc.php", "","","","");
	print_liste_field_titre($langs->trans("Name"),"bc.php", "","","","");
	print_liste_field_titre($langs->trans("Débito"),"bc.php", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Crédito"),"bc.php", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Debitbalance"),"bc.php", "",'','','align="right"');
	print_liste_field_titre($langs->trans("Creditbalance"),"bc.php", "",'','','align="right"');
	print "</tr>\n";
	$_SESSION['array_selected'] = array('ref'=>1,
		'Name'=>2,
		'Debit'=>3,
		'Credit'=>4,
		'Debitbalance'=>5,
		'Creditbalance'=>6);
	$_SESSION['array_selected_fields'] = array('ref'=>'Ref',
		'Name'=>'Name',
		'Debit'=>'Debit',
		'Credit'=>'Credit',
		'Debitbalance'=>'Debitbalance',
		'Creditbalance'=>'Creditbalance');
	$_SESSION['array_selected_type'] = array('ref'=>'Text',
		'Name'=>'Text',
		'Debit'=>'Number',
		'Credit'=>'Number',
		'Debitbalance'=>'Number',
		'Creditbalance'=>'Number');

	//Array para el reporte
	$aLineas = array();
	$i = 1;

	if ($num)
	{
		$var=True;
		foreach ($lines AS $i => $objp)
		{
			$resdet = $objectdet->fetch_list_account($objp->account_number,$date_ini,$date_end);

			if ($resdet>0)
			{
				$aArray = $objectdet->aArray;
				$aArrayDet = $objectdet->aArrayDet;
				$objAccountingaccount->id = $objp->id;
				$objAccountingaccount->account_number = $objp->account_number;
				$objAccountingaccount->label = $objp->label;
				$aLineas[$i]['ref'] = $objp->account_number;

				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td>'.$objAccountingaccount->getNomUrl(1).'</td>';
				print '<td>'.$objp->label.'</td>';
				$aLineas[$i]['nombre'] = $objp->label;
				print '<td align="right">'.price($aArray['debit_amount']).'</td>';
				$aLineas[$i]['debito'] = $aArray['debit_amount'];
				print '<td align="right">'.price($aArray['credit_amount']).'</td>';
				$aLineas[$i]['credito'] = $aArray['credit_amount'];
				$objp->Name = $objp->cta_name;
				$objp->Debit = $aArray['debit_amount'];
				$objp->Credit = $aArray['credit_amount'];

				$saldoD = 0;
				$saldoC = 0;
				if ($objp->cta_normal == 1)
				{
					$saldo = price2num($aArray['debit_amount']-$aArray['credit_amount'],'MT');
					if ($saldo >= 0)
					{
						$saldoD = $saldo;
						print '<td align="right">'.price(price2num($saldoD,'MT')).'</td>';
						$aLineas[$i]['deudor'] = $saldoD;
						$aLineas[$i]['deudor1'] = "";
						print '<td align="right">&nbsp;</td>';
						$objp->Debitbalance = $saldoD;
						$objp->Creditbalance = 0;
					}
					else
					{
						$saldoC = $saldo*-1;
						print '<td align="right">&nbsp;</td>';
						print '<td align="right">'.price(price2num($saldoC,'MT')).'</td>';
						$aLineas[$i]['deudor'] = "";
						$aLineas[$i]['deudor1'] = $saldoC;
						$objp->Debitbalance = 0;
						$objp->Creditbalance = $saldoC;
					}
				}
				else
				{
					$saldo = price2num($aArray['credit_amount']-$aArray['debit_amount'],'MT');
					if ($saldo >= 0)
					{
						$saldoC = $saldo;
						print '<td align="right">&nbsp;</td>';
						print '<td align="right">'.price(price2num($saldoC,'MT')).'</td>';
						$aLineas[$i]['deudor'] = "";
						$aLineas[$i]['deudor1'] = $saldoC;
						$objp->Debitbalance = 0;
						$objp->Creditbalance = $saldoC;
					}
					else
					{
						$saldoD = $saldo*-1;
						print '<td align="right">'.price(price2num($saldoD,'MT')).'</td>';
						print '<td align="right">&nbsp;</td>';
						$aLineas[$i]['deudor'] = $saldoD;
						$aLineas[$i]['deudor1'] = "";
						$objp->Debitbalance = $saldoD;
						$objp->Creditbalance = 0;
					}
				}
				$_SESSION['array_newdata'][$i] = $objp;
				$sumDebito  += $aArray['debit_amount'];
				$sumCredito += $aArray['credit_amount'];
				$sumSaldoD+= $saldoD;
				$sumSaldoC+= $saldoC;
				print "</tr>\n";

			}
			//$i++;
		}
			//totales
		print '<tr class="liste_total"><td colspan="2">'.$langs->trans("Total").'</td>';
		print '<td align="right">';
		print price($sumDebito);
		print '</td>';
		print '<td align="right">';
		print price($sumCredito);
		print '</td>';
		print '<td align="right">';
		print price($sumSaldoD);
		print '</td>';
		print '<td align="right">';
		print price($sumSaldoC);
		print '</td>';
		print '</tr>';

		print "</table>";

		if ($num>0)
		{
			$entity = $conf->entity;
			if($objEntity->fetch($entity) > 0){
				$labelEntity = $objEntity->label;
			}else{
				$labelEntity = "";
			}

			$aReporte = array(1=>$aLineas,2=>$sumDebito,3=>$sumCredito,4=>$sumSaldoD,5=>$sumSaldoC,6=>$date_end,7=>$labelEntity);
			$_SESSION['aReporte'] = serialize($aReporte);

			print '<div class="tabsAction">'."\n";
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';
			print '</div>'."\n";
		}

	}



/*
	// Liste des formats d'exports disponibles
	$var=true;
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td colspan="2">'.$langs->trans("AvailableFormats").'</td>';
	print '<td>'.$langs->trans("LibraryUsed").'</td>';
	print '<td align="right">'.$langs->trans("LibraryVersion").'</td>';
	print '</tr>'."\n";

	$liste=$objmodelexport->liste_modeles($db);
	foreach($liste as $key => $val)
	{
		$var=!$var;
		print '<tr '.$bc[$var].'>';
		print '<td width="16">'.img_picto_common($key,$objmodelexport->getPictoForKey($key)).'</td>';
		$text=$objmodelexport->getDriverDescForKey($key);
		print '<td>'.$form->textwithpicto($objmodelexport->getDriverLabelForKey($key),$text).'</td>';
		print '<td>'.$objmodelexport->getLibLabelForKey($key).'</td><td align="right">'.$objmodelexport->getLibVersionForKey($key).'</td></tr>'."\n";
	}
	print '</table>';

	print '</div>';

	print '<table width="100%">';

	if ($sqlusedforexport && $user->admin)
	{
		print '<tr><td>';
		print info_admin($langs->trans("SQLUsedForExport").':<br> '.$sqlusedforexport);
		print '</td></tr>';
	}
	print '</table>';

	print '<table width="100%"><tr><td width="50%">';

	if (! is_dir($conf->contab->dir_temp)) dol_mkdir($conf->contab->dir_temp);
	// Affiche liste des documents
	// NB: La fonction show_documents rescanne les modules qd genallowed=1, sinon prend $liste
	$formfile->show_documents('export','',$upload_dir,$_SERVER["PHP_SELF"].'?step=5&datatoexport='.$datatoexport,$liste,1,(! empty($_POST['model'])?$_POST['model']:'csv'),1,1);

	print '</td><td width="50%">&nbsp;</td></tr>';
	print '</table>';

	$entity = $conf->entity;
	if($ObjEntity->fetch($entity) > 0){
		$labelEntity = $ObjEntity->label;
	}else{
		$labelEntity = "";
	}
*/


	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';

	/*Aqui estaba el reporte*/
	$filename='contab/'.$period_year.'/sumBalance';
	$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/sumBalance';

	$modelpdf = "balanceCSumaSaldo";

	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
		//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	$result=$objContab->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);

	$urlsource=$_SERVER['PHP_SELF'];
	//$genallowed=$user->rights->assistance->lic->hiddemdoc;
	//$delallowed=$user->rights->assistance->lic->deldoc;
	$genallowed = 0;
	$delallowed = 0;
	print $formfile->showdocuments('contab',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

	$somethingshown=$formfile->numoffiles;

	print '</td></tr></table>';

}



$db->close();

llxFooter();
?>
