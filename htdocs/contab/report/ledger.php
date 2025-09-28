<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/contab/report/ledger.php
 *	\ingroup    Books report
 *	\brief      Page fiche contab ledger
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/accountancy/class/html.formventilation.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountadd.class.php");

require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';

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

$langs->load("contab");



$action=GETPOST('action');
$id    = GETPOST("id");
//verificamos la gestion activa
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
$date_initial = dol_get_first_day($pastmonthyear,1,false);

if (GETPOST('di_month'))
{
	$date_ini  = dol_mktime(0, 0, 0, GETPOST('di_month'),  GETPOST('di_day'),  GETPOST('di_year'));
	$date_fin  = dol_mktime(23, 59, 59, GETPOST('df_month'),  GETPOST('df_day'),  GETPOST('df_year'));
	$_SESSION['ledger_date_ini'] = $date_ini;
	$_SESSION['ledger_date_fin'] = $date_fin;
}
if (empty($date_end) && empty($date_ini)) // We define date_start and date_end
{
	$date_ini=dol_get_first_day($pastmonthyear,$pastmonth,false);
	$date_fin=dol_get_last_day($pastmonthyear,$pastmonth,false);
	$_SESSION['ledger_date_ini'] = $date_ini;
	$_SESSION['ledger_date_fin'] = $date_fin;
}
$date_ini = $_SESSION['ledger_date_ini'];
$date_fin = $_SESSION['ledger_date_fin'];


$ini = $date_ini;
$fin = $date_fin;

$mesg = '';

$object = new Contabseatext($db);
$objSeatdet = new Contabseatdetext($db);
$objAccounting = new Accountingaccountext($db);
$objAccountingadd = new Accountingaccountadd($db);
$objContab=new Contab($db);
$form = new Form($db);
$formfile = new FormFile($db);

$filteracc = '';
$res = $objAccounting->fetchAll('','',0,0,array(1=>1),'AND',$filteracc);
$options = "";

$id = GETPOST('id');

if ($res >0)
{
	$lines = $objAccounting->lines;
	foreach ($lines AS $j => $line)
	{
		$select = '';
		if ($id == $line->id) $select = ' selected';
		$options.= '<option value="'.$line->id.'" '.$select.'>'.$line->account_number.' '.$line->label.'</options>';
	}
}

	// Delete file in doc form
if ($action == 'remove_file')
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	if ($objAccounting->fetch($id))
	{
		$objAccounting->fetch_thirdparty();
		$upload_dir =	$conf->contab->dir_output . "/";
		$file =	$upload_dir	. '/' .	GETPOST('file');
		$ret=dol_delete_file($file,0,0,0,$object);
		if ($ret) setEventMessages($langs->trans("FileWasRemoved", GETPOST('urlfile')), null, 'mesgs');
		else setEventMessages($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), null, 'errors');
	}
}
if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);

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
		 $objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans("Libro Mayor"));
		//$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');

		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		 );

		 $objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('De ')." ".dol_print_date($aReporte[5],'day')." ".$langs->trans('Al ')." ".dol_print_date($aReporte[6],'day'));
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:F2');

		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		 );

		//Encabezados

		 $aCabecera = $aReporte[7];
		//Izquierda
		 $objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Ref'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B4',$aCabecera['ref']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:F4');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Name'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B5',$aCabecera['nombre']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:F5');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A6',$langs->trans('Topaccount'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B6',$aCabecera['superior']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:F6');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('Accountclass'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B7',$aCabecera['clase']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B7:F7');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A8',$langs->trans('Accountnormal'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B8',$aCabecera['normal']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:F8');


		//Para poner formato a los numeros en el excel
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('B4')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
		 );
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
		 );
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('B6')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
		 );
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('B7')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
		 );
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('B8')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
		 );
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('B9')->getAlignment()->applyFromArray(
		 	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
		 );
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('C10')->getAlignment()->applyFromArray(
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
		 $objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('A9')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('C10')->getFont()->setBold(true);




		 $objPHPExcel->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('Q3')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('R3')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('J7')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('K7')->getFont()->setBold(true);




		//CABECERAS DE LA TABLA
		 $objPHPExcel->getActiveSheet()->SetCellValue('A10',$langs->trans('Ref'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B10',$langs->trans('Date'));
		 $objPHPExcel->getActiveSheet()->SetCellValue('C10',html_entity_decode($langs->trans('Description')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('D10',html_entity_decode($langs->trans('Débito')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('E10',html_entity_decode($langs->trans('Crédito')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('F10',$langs->trans('Balance'));


		 $objPHPExcel->getActiveSheet()->getStyle('A10:F10')->applyFromArray(
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

		 $line = 11;
		 $color = 1;
		 foreach ( $aReporte[1] as $j => $row)
		 {
			//COLORES A LAS CELDAS
			/*if($color == 1){
				$objPHPExcel->getActiveSheet()->getStyle('A'.$line.":F".$line)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'd3f7c0')
						)
					)
				);
				$color = 0;
			}else{
				$color = 1;
			}*/

			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($row['fecha'],'day'));
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['descripcion']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['debito']);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['credito']);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['saldo']);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$line++;
		}//fin f$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);oreach

		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$langs->trans("Total"));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$aReporte[2]);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$aReporte[3]);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$aReporte[4]);

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

		$objWriter->save("excel/Libromayor.xlsx");
		header('Location: '.DOL_URL_ROOT.'/contab/report/fiche_export.php?archive=Libromayor.xlsx');
	}
/*
 * Actions
 */

/*
* view
*/


$form = new Form($db);
$formventilation = new Formventilation($db);
$aLineas = array();
$aCabecera = array();

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Ledger"), $page, "bc.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"ledger.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
$form->select_date($date_ini,'di_','','','',"crea_seat",1,1);

print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_fin,'df_','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Account').'</td><td colspan="2">';
//print $object->select_account($id,'id','',0,1,2);
print $formventilation->select_account(GETPOST('id'),'id',1);
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';



/*
 * View
 */

if ($id)
{
	dol_htmloutput_mesg($mesg);
	$result = $objAccounting->fetch($id);
	$resadd = $objAccountingadd->fetch(0,$id);
	if ($result < 0)
	{
		dol_print_error($db);
	}


	//$head = fabrication_prepare_head($object);
	print '<br>';
	dol_fiche_head();

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
	print $objAccounting->account_number.' id '.$objAccounting->id;
	$aCabecera['ref'] = $objAccounting->account_number;
	print '</td></tr>';
	// name
	print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	print $objAccounting->label;
	$aCabecera['nombre'] = $objAccounting->label;
	print '</td></tr>';

	//top
	print '<tr><td>'.$langs->trans('Accounttop').'</td><td colspan="2">';
	if ($objAccounting->account_parent){
		$aCabecera['superior'] = $objAccounting->account_parent;
		print $objAccounting->account_parent;
	}else{
		print '&nbsp;';
		$aCabecera['superior'] = "";
	}

	print '</td></tr>';

	//cta_class
	print '<tr><td>'.$langs->trans('Class').'</td><td colspan="2">';
	print ($objAccountingadd->cta_class=='A'?$langs->trans('Analítica'):$langs->trans('Sintetica')).' '.$objAccountingadd->fk_accounting_account.' '.$objAccountingadd->id;
	$aCabecera['clase'] = ($objAccountingadd->cta_class=='A'?$langs->trans('Analítica'):$langs->trans('Sintetica'));
	print '</td></tr>';

	//cta_normal
	print '<tr><td>'.$langs->trans('Accountnormal').'</td><td colspan="2">';
	print ($objAccountingadd->cta_normal==1?$langs->trans('Deudor'):$langs->trans('Acreedor'));
	$aCabecera['normal'] = ($objAccountingadd->cta_normal==1?$langs->trans('Deudor'):$langs->trans('Acreedor'));
	print '</td></tr>';

	//status
	//print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	//print $objAccounting->getLibStatut(6);
	//$aCabecera['status'] = $objAccountingadd->status;
	//print '</td></tr>';

	print "</table>";
	dol_fiche_end();

	//obtenemos la suma anterior
	$aDate = dol_getdate($date_ini);
	$lCalculoant = true;
	if ($aDate['mon']==1 && $aDate['mday']==1) $lCalculoant = false;

	$aDateant = dol_get_prev_day($aDate['mday'], $aDate['mon'],$aDate['year']);
	$date_ini_ant = dol_mktime(23,59,59,$aDateant['month'],$aDateant['day'],$aDateant['year']);
	$sumDa=0;
	$sumCa=0;
	if ($lCalculoant)
	{
		$res = $objSeatdet->fetch_list_account_group($objAccounting->account_number,$date_initial,$date_ini_ant);
		$sumBalance = 0;
		if ($res>0)
		{
			$aArray = $objSeatdet->aArray;
			$sumDa +=$aArray['debit_amount'];
			$sumCa +=$aArray['credit_amount'];
		}
	}
	//liste movimiento contable

	$res = $objSeatdet->fetch_list_account($objAccounting->account_number,$date_ini,$date_fin);

	if ($res>0)
	{
		$aArray = $objSeatdet->aArray;
		$aArrayDet = $objSeatdet->aArrayDet;
		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Ref"),"", "","","","");
		print_liste_field_titre($langs->trans("Date"),"", "","","","");
		print_liste_field_titre($langs->trans("Detail"),"", "","","",'align="left"');
		print_liste_field_titre($langs->trans("Debit"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Credit"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Balance"),"", "","","",'align="right"');

		print '</tr>';
		//registramos saldo anterior
		if ($sumDa>0 || $sumCa>0)
		{
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td width="6%">'.'</td>';
			print '<td width="4%">'.'</td>';
			print '<td>'.$langs->trans('Previousbalance').'</td>';
			if ($objAccountingadd->cta_normal==1) $difBalance = $sumDa-$sumCa;
			else $difBalance = $sumCa-$sumDa;
			print '<td width="5%" align="right">'.'</td>';
			print '<td width="5%" align="right">'.'</td>';
			print '<td width="5%" align="right">'.price(price2num($difBalance,'MT')).'</td>';
			$aLineas[0]['descripcion'] = $langs->trans('Previousbalance');
			$aLineas[0]['saldo'] = $difBalance;
			print '</tr>';
			$sumBalance =$difBalance;
		}
		foreach($aArrayDet AS $fk_seat => $aData)
		{
			$object = new Contabseatext($db);
			$object->fetch($fk_seat);
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td width="6%">'.$object->getNomUrl(1).'</td>';
			$aLineas[$fk_seat]['ref'] = $object->ref;

			print '<td width="4%">'.dol_print_date($object->date_seat,'day').'</td>';
			$aLineas[$fk_seat]['fecha'] = $object->date_seat;

			print '<td>'.$object->history.'</td>';
			$aLineas[$fk_seat]['descripcion'] = $object->history;

			print '<td width="5%" align="right">'.price(price2num($aData['debit_account'],'MT')).'</td>';
			$aLineas[$fk_seat]['debito'] = $aData['debit_account'];

			$sumD +=$aData['debit_account'];
			$sumC +=$aData['credit_account'];
			if ($objAccountingadd->cta_normal==1) $dif = $aData['debit_account']-$aData['credit_account'];
			else $dif = $aData['credit_account']-$aData['debit_account'];
			$sumBalance+=$dif;

			print '<td width="5%" align="right">'.price(price2num($aData['credit_account'],'MT')).'</td>';
			$aLineas[$fk_seat]['credito'] = $aData['credit_account'];

			print '<td width="5%" align="right">'.price(price2num($sumBalance,'MT')).'</td>';
			$aLineas[$fk_seat]['saldo'] = $sumBalance;

			print '</tr>';

		}
		print '<tr class="liste_total"><td align="left" colspan="3">'.$langs->trans("Total").'</td>';
		print '<td align="right">'.price(price2num($sumD,'MT')).'</td>';
		print '<td align="right">'.price(price2num($sumC,'MT')).'</td>';
		print '</tr>';

		print '<tr class="liste_total"><td align="left" colspan="3">'.$langs->trans("Accountbalances").'</td>';
		if ($objAccountingadd->cta_normal == 1)
		{
			print '<td align="right">'.price(price2num($difBalance+$sumD-$sumC,'MT')).'</td>';
			print '<td align="right">&nbsp;</td>';
			$totalTipo = $difBalance+$sumD-$sumC;
		}
		else
		{
			print '<td align="right">&nbsp;</td>';
			print '<td align="right">'.price(price2num($difBalance+$sumC-$sumD,'MT')).'</td>';
			$totalTipo = $difBalance+$sumC-$sumD;
		}
		print '</tr>';
		print '</table>';
	}
	else
	{
		print '<table class="noborder" width="100%">';
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td>'.$langs->trans('Nomovement').'</td>';
		print  '</tr>';
		print '</table>';
	}

	//echo 'fecha Inicio : '.dol_print_date($date_ini). ' Fecha Final : '.dol_print_date($date_fin);
	$aReporte = array(1=>$aLineas,2=>$sumD,3=>$sumC,4=>$totalTipo,5=>$date_ini,6=>$date_fin,7=>$aCabecera);
	$_SESSION['aReporte'] = serialize($aReporte);


	print '<div class="tabsAction">'."\n";
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
	print '</div>'."\n";
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';

	/*Aqui estaba el reporte*/
	$filename='contab/'.$period_year.'/ledger/'.$objAccounting->account_number;
	$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/ledger/'.$objAccounting->account_number;

	$modelpdf = "ledger";

	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$objContab->account_number = $objAccounting->account_number;
	$objContab->date_fin = $date_fin;
		//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	$result=$objContab->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);

	$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
	$genallowed=$user->rights->contab->pdf->write;
	$delallowed=$user->rights->contab->pdf->del;
	print $formfile->showdocuments('contab',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

	$somethingshown=$formfile->numoffiles;

	print '</td></tr></table>';
}



llxFooter();

$db->close();
?>
