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
 *	\file       htdocs/mant/report/ot_excel.php
 *	\ingroup    Report excel
 *	\brief      Page fiche mant reports
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("assets");
$langs->load("others");

$ref = GETPOST('ref');
$aDepr = unserialize($_SESSION['depr']);
$month = $aDepr[$ref]['month'];
$year = $aDepr[$ref]['year'];
$type_group = $aDepr[$ref]['type_group'];
$country = $aDepr[$ref]['country'];
$filterstatic = " AND t.ref = ".$ref;
$objmov = new Assetsmovext($db);
$assets = new Assetsext($db);

$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
if ($resm > 0)
{
	$lines = $objmov->lines;

	//incluimos array estilos
	include_once DOL_DOCUMENT_ROOT.'/assets/export/lib/format_excel.lib.php';
	 //PRCESO 1
	$objPHPExcel = new PHPExcel();
	// $objReader = PHPExcel_IOFactory::createReader('Excel2007');
	// $objPHPExcel = $objReader->load("excel/iniproceso_mod.xlsx");
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->SetCellValue('A1',STRTOUPPER($langs->trans('Depreciación')));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:L1');

	$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Ref'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B2',$langs->trans('Label'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C2',$langs->trans('Dateadq'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D2',$langs->trans('Date_ini'));
	$objPHPExcel->getActiveSheet()->SetCellValue('E2',$langs->trans('Date_fin'));
	$objPHPExcel->getActiveSheet()->SetCellValue('F2',$langs->trans('Coste'));
	$objPHPExcel->getActiveSheet()->SetCellValue('G2',$langs->trans('Costeresidual'));
	$objPHPExcel->getActiveSheet()->SetCellValue('H2',$langs->trans('Tcini'));
	$objPHPExcel->getActiveSheet()->SetCellValue('I2',$langs->trans('Tcend'));
	$objPHPExcel->getActiveSheet()->SetCellValue('J2',$langs->trans('Amountupdate'));
	$objPHPExcel->getActiveSheet()->SetCellValue('K2',$langs->trans('Actualizado'));
	$objPHPExcel->getActiveSheet()->SetCellValue('L2',$langs->trans('Total meses'));
	$objPHPExcel->getActiveSheet()->SetCellValue('M2',$langs->trans('Consumida meses'));
	$objPHPExcel->getActiveSheet()->SetCellValue('N2',$langs->trans('Saldo de vida Sgte Periodo'));
	$objPHPExcel->getActiveSheet()->SetCellValue('O2',$langs->trans('Depr. Periodo'));
	$objPHPExcel->getActiveSheet()->SetCellValue('P2',$langs->trans('Depr. Acum.'));
	$objPHPExcel->getActiveSheet()->SetCellValue('Q2',$langs->trans('Act. Depr. Acum.'));
	$objPHPExcel->getActiveSheet()->SetCellValue('R2',$langs->trans('Depr. Acum. Actualizada'));
	$objPHPExcel->getActiveSheet()->SetCellValue('S2',$langs->trans('Depr. Acum. Actual'));
	$objPHPExcel->getActiveSheet()->SetCellValue('T2',$langs->trans('Costo Neto'));

	$objPHPExcel->getActiveSheet()->getStyle('A2:T2')->applyFromArray($styleArray);

	$row = 3;
	foreach((array) $lines AS $id => $obj)
	{	
		$assets->fetch($obj->fk_asset);
		

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$assets->ref);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$assets->descrip);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,dol_print_date($assets->date_adq,'day'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,dol_print_date($obj->date_ini,'day'));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,dol_print_date($obj->date_end,'day'));
		//$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('C', $row)->getNumberFormat()->setFormatCode('[$-C09]d mmm yyyy;@');
		//$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('D', $row)->getNumberFormat()->setFormatCode('[$-C09]d mmm yyyy;@');
		//$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('E', $row)->getNumberFormat()->setFormatCode('[$-C09]d mmm yyyy;@');

		$objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
		$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
		$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$obj->coste);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$obj->coste_residual);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$obj->tcini);
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$obj->tcend);
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$obj->amount_update);
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$obj->amount_balance);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$obj->month_depr);
		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$obj->time_consumed);
		$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$obj->month_depr-$obj->time_consumed);
		$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row,$obj->amount_depr);
		//$objPHPExcel->getActiveSheet()->getStyle('O'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//$objPHPExcel->getActiveSheet()->getStyle('O'.$row)->getNumberFormat()->setFormatCode('#.##0,00');
		$objPHPExcel->getActiveSheet()->getStyle('0'.$row)->getNumberFormat()->setFormatCode('[Blue][>=30]$#,##0;[Red][<0]$#,##0;$#,##0');
		$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row,$obj->amount_depr_acum);
		$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row,$obj->amount_depr_acum_update);
		$objPHPExcel->getActiveSheet()->SetCellValue('R'.$row,$obj->amount_balance_depr);
		$objPHPExcel->getActiveSheet()->SetCellValue('S'.$row,$obj->amount_depr_acum_update+$obj->amount_depr);
		$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,'=+K'.$row.'-R'.$row);


		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':B'.$row)->applyFromArray($stylebodyArray);	
		$row++;
	}

	
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);


	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/exportm.xlsx");

	header('Location: '.DOL_URL_ROOT.'/assets/export/excel/fiche_export.php?archive=exportm.xlsx');

}

llxFooter();

$db->close();
?>
