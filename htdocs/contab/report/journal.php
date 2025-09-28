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
 *	\file       htdocs/contab/report/journal.php
 *	\ingroup    Books report
 *	\brief      Page fiche contab journal
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccounting.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountadd.class.php");
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';

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


$action       = GETPOST('action');
$type_seat    = GETPOST("type_seat");

if (!isset($_SESSION['period_year']))
	$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now());
$pastmonthyear = $period_year;
if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}
$date_ini  = dol_mktime(0, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
$date_end  = dol_mktime(23, 59, 59, GETPOST('date_endmonth'),  GETPOST('date_endday'),  GETPOST('date_endyear'));
if (empty($date_end) && empty($date_ini)) // We define date_start and date_end
{
	$date_ini=dol_get_first_day($pastmonthyear,$pastmonth,false);
	$date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$aType = array(1=>$langs->trans('Ingreso'),2=>$langs->trans('Egreso'),3=>$langs->trans('Traspaso'));
$mesg = '';

$object = new Contabseatext($db);
$objSeatdet = new Contabseatdetext($db);
$objAccounting = new Accountingaccountext($db);
$objAccountingadd = new Accountingaccountext($db);
$ObjEntity = new Entity($db);
$objContab=new Contab($db);
$formfile = new FormFile($db);
$form = new Form($db);



if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);

	$aSub = $aReporte[2];

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
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans("Journalbook"));
		//$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

		//Encabezados

		//$aCabecera = $aReporte[7];
		//Izquierda
	$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('Unidad Ejecutora'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',$aReporte[6]);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:F3');

	$objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Typeseat'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',$aType[$aReporte[5]]);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:F4');

	$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('De'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B5',dol_print_date($aReporte[3],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:F5');

	$objPHPExcel->getActiveSheet()->SetCellValue('A6',$langs->trans('Hasta'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B6',dol_print_date($aReporte[4],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:F6');

	$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('Moneda'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B7',$aReporte[7]);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B7:F7');


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
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
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

		//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
		//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$line = 9;
	$color = 1;
	$sumaDebito  = 0;
	$sumaCredito = 0;
	foreach ( $aReporte[1] as $j => $row)
	{

			//CABECERAS DE LA TABLA
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Ref'));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('Date'));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$langs->trans('Divisa'));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$langs->trans('Codigo'));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans('FechaComprobante'));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans('Respaldo'));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$langs->trans('Debit'));
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$langs->trans('Credit'));
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,$langs->trans('Estado'));


		$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':I'.$line)->applyFromArray(
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
		$line++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);

		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($row['fecha'],'day'));
			//$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['fecha']);
			//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['divisa']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['codigo']);

		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,dol_print_date($row['comprobante'],'day'));
			//$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['comprobante']);
			//$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['respaldo']);

		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$row['debito']);
		$sumaDebito = $sumaDebito + $row['debito'];
		$objPHPExcel->getActiveSheet()->getStyle('G'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$row['credito']);
		$sumaCredito = $sumaCredito + $row['credito'];
		$objPHPExcel->getActiveSheet()->getStyle('H'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,$row['estado']);


		$line++;

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('History'));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['historia']);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$line.':I'.$line);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->applyFromArray(
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

		$line++;

		$sw = 1;
			//LISTADO LOS DETALLES
		foreach ((array) $aSub[$j] as $key => $value)
		{
			if($sw == 1)
			{
				//$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Ref'));
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('Cuenta Debito'));
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$langs->trans(''));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$line.':C'.$line);

				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$langs->trans('Cuenta Credito'));
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans(''));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('D'.$line.':E'.$line);


				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans('Parcial'));
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$langs->trans('Monto Debito'));
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$langs->trans('Monto Credito'));
					//$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$langs->trans('Historia'));
				$objPHPExcel->getActiveSheet()->getStyle('A'.$line.":H".$line)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '0a92dc')
						)
					)
				);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$line.":H".$line)->getAlignment()->applyFromArray(
					array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);
				$line++;
				$sw = 0;
			}

			//$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$value['ref']);
			//$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$line)->getAlignment()->applyFromArray(
			//	array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
			//);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$value['cuentadebito']." ". $value['detalledebito'] );
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,"");
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$line.':C'.$line);

			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$value['cuentacredito']." ". $value['detallecredito']);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('D'.$line.':E'.$line);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$value['parcial']);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$value['montodebito']);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$value['montocredito']);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				//$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$value['historia']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$line.":H".$line)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'cde7f5')
					)
				)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$line++;
		}

		}//fin f$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);oreach

		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans("Total"));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$sumaDebito);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$sumaCredito);


		//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


		$objPHPExcel->getActiveSheet()->getStyle('F'.$line.":H".$line)->applyFromArray(
			array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'ddfcbf')
				)
			)
		);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		$objWriter->save("excel/libroDiario.xlsx");
		header('Location: '.DOL_URL_ROOT.'/contab/report/fiche_export.php?archive=libroDiario.xlsx');
	}

/*
 * Actions
 */


/*
 * View
 */


$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Journal"), $page, "journal.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"journal.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
$form->select_date($date_ini,'date_ini','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
print '</td></tr>';

//type seat
print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td colspan="2">';
print select_type_seat($type_seat,'type_seat','','',1);
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

print '</form>';




if ($action == 'edit')
{
	dol_htmloutput_mesg($mesg);

	$aPapas = array();
	$aHijos = array();

	$filter = " AND  t.entity = ".$conf->entity;
	$filter.= " AND t.date_seat BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_end)."' ";
	if (!empty($type_seat) && $type_seat != -1)
		$filter.= " AND t.type_seat = ".$type_seat;

	$num = $object->fetchAll('ASC','t.date_seat, t.ref',0,0,array(1=>1),'AND',$filter);

	if ($num)
	{
		$lines = $object->lines;
		$i = 0;
		$divisa = 0;

		dol_fiche_head($head, 'card', $langs->trans("Journal"), 0, 'journal');

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Ref"),"", "","","","");
		//print_liste_field_titre($langs->trans("Sblote"),"", "","","","");
		//print_liste_field_titre($langs->trans("Doc"),"", "","","","");
		print_liste_field_titre($langs->trans("Date"),"", "","","","");
		print_liste_field_titre($langs->trans("Currency"),"", "","","","");
		print_liste_field_titre($langs->trans("Typeseat"),"", "","","","");
		print_liste_field_titre($langs->trans("History"),"", "","","","");
		print_liste_field_titre($langs->trans("Debito"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Credito"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Status"),"", "",'','','align="right"');
		print "</tr>\n";
		$var=True;
		foreach ($lines AS $j => $objp)
		{
			$object->id = $objp->id;
			$object->ref = $objp->ref;
			$object->label = $objp->history;
			$object->status = $objp->status;

			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>'.$object->getNomUrl().'</a></td>';
			$aPapas[$j]['ref'] = $object->ref;
				//print '<td>'.$objp->sblote.'</td>';
				//print '<td>'.$objp->doc.'</td>';
			print '<td>'.dol_print_date($objp->date_seat,'day').'</td>';
			$aPapas[$j]['fecha'] = $objp->date_seat;
			print '<td>'.$objp->currency.'</td>';
			$aPapas[$j]['divisa'] = $objp->currency;
			print '<td>'.select_type_seat($objp->type_seat,'type_seat','','',1,1).'</td>';
			//$aPapas[$j]['tipo'] = $objp->type_seat;
			$divisa = $objp->currency;
			print '<td>'.$objp->history.'</td>';
			$aPapas[$j]['historia'] = $objp->history;
			print '<td align="right">'.price($objp->debit_total).'</td>';
			$aPapas[$j]['debito'] = $objp->debit_total;
			print '<td align="right">'.price($objp->credit_total).'</td>';
			$aPapas[$j]['credito'] = $objp->credit_total;
			print '<td align="right">'.$object->LibStatut($objp->state,'',4).'</td>';
			//$aPapas[$j]['estado'] = $object->status;
			$aPapas[$j]['estado'] = $object->LibStatut($objp->state,'',1);
			print "</tr>\n";
			$aPapas[$j]['respaldo'] = $objp->document_backing;
			$aPapas[$j]['codigo'] = $objp->codtr;
			$aPapas[$j]['comprobante'] = $objp->date_seat;

				//buscamos las cuentas de cada asiento
			$filterdet = " AND t.fk_contab_seat = ".$objp->id;
			$resdet = $objSeatdet->fetchAll('ASC','t.credit_account, t.debit_account',0,0,array(1=>1),'AND',$filterdet);
				  //define variables para suma total
			$sumDebit = 0;
			$sumCredit = 0;

			if ($resdet>0)
			{
				$linesdet = $objSeatdet->lines;
				$var1=true;

				print '<tr>';
				print '<td colspan="10">';
				print '<div class="" style="width:90%;">';

				print '<table class="nobordernopadding" align="right" width="100%">';
				print '<tr class="liste_titre" style="background:#80a9e0;">';
				print '<td width="2%">'.$langs->trans('Ref').'</td>';
				//print '<td width="3%"align="center">'.$langs->trans('Type').'</td>';
				print '<td width="10%">'.$langs->trans('Debitaccount').'</td>';
				print '<td width="10%">'.$langs->trans('Creditaccount').'</td>';
				print '<td width="20%">'.$langs->trans('Debitdetail').'</td>';
				print '<td width="20%">'.$langs->trans('Creditdetail').'</td>';
				print '<td width="8%">'.$langs->trans('Partial').'</td>';
				print '<td width="8%" align="right">'.$langs->trans('Amountdebit').'</td>';
				print '<td width="8%" align="right">'.$langs->trans('Amountcredit').'</td>';
				print '<td width="14%" align="right">'.$langs->trans('History').'</td>';
				print '</tr>';

				//$j = 0;
				foreach ($linesdet AS $k => $objc)
				{
					if (!empty($objc->debit_account))
						$objc->type_seat = 1;
					if (!empty($objc->credit_account))
						$objc->type_seat = 2;
					if (!empty($objc->debit_account) && !empty($objc->credit_account))
						$objc->type_seat = 3;

					$var1=!$var1;
					print "<tr ".$bc[$var1].">";
					print '<td nowrap="nowrap">';
					print $objc->id."</td>\n";
					$aHijos[$j][$k]['ref'] = $objc->id;
					//print '<td align="left">';
					//print select_seat($objc->type_seat,'type_seat','','',1,1);
					//print '</td>';
					$amountAux = '';
					$refAuxd = '';
					$refAuxc = '';
					if (!empty($objc->debit_account))
					{
						$objc->type_seat = 1;
						$objAccounting->fetch('',$objc->debit_account);
						$objc->debit_detail = $objAccounting->label;
						$refAuxd = $objc->ref_ext_auxd;
						print '<td>'.$objc->debit_account.'</td>';
						$aHijos[$j][$k]['cuentadebito'] = $objc->debit_account;
					}
					else
					{
						$aHijos[$j][$k]['cuentadebito'] = "";
						print '<td>&nbsp;</td>';
					}
					if (!empty($objc->credit_account))
					{
						$objc->type_seat = 2;
						$objAccounting->fetch('',$objc->credit_account);
						$objc->credit_detail = $objAccounting->label;
						$refAuxc = $objc->ref_ext_auxc;
						print '<td>'.$objc->credit_account.'</td>';
						$aHijos[$j][$k]['cuentacredito'] = $objc->credit_account;
					}
					else
					{
						print '<td>&nbsp;</td>';
						$aHijos[$j][$k]['cuentacredito'] = "";
					}
					if (!empty($objc->debit_account) && !empty($objc->credit_account))
					{
						$objc->type_seat = 3;
					}
					print "<td>".$objc->debit_detail."</td>\n";
					$aHijos[$j][$k]['detalledebito'] = $objc->debit_detail;
					print "<td>".$objc->credit_detail."</td>\n";
					$aHijos[$j][$k]['detallecredito'] = $objc->credit_detail;

					if ($objc->type_seat == 1)
						$objc->amountdebit = $objc->amount;
					if ($objc->type_seat == 2)
						$objc->amountcredit = $objc->amount;
					if ($objc->type_seat == 3)
					{
						$objc->amountcredit = $objc->amount;
						$objc->amountdebit = $objc->amount;
					}
					print '<td align="right">'.'</td>';
					print '<td align="right">'.price($objc->amountdebit)."</td>\n";
					$aHijos[$j][$k]['montodebito'] = $objc->amountdebit;
					print '<td align="right">'.price($objc->amountcredit)."</td>\n";
					$aHijos[$j][$k]['montocredito'] = $objc->amountcredit;
					if ($refAuxd) $amountAux = $objc->amountdebit;
					if ($refAuxc) $amountAux = $objc->amountcredit;
					//sumando totales
					$sumDebit  += $objc->amountdebit;
					$sumCredit += $objc->amountcredit;

					print "<td>".dol_trunc($objc->history,20)."</td>\n";
					$aHijos[$j][$k]['historia'] = dol_trunc($objc->history,20);
					print '</tr>';
					if ($refAuxd || $refAuxc)
					{
						$var1=!$var1;
						print "<tr ".$bc[$var1].">";
						print '<td nowrap="nowrap">';
						print "</td>";
						//print '<td align="left">';
						//print '';
						//print '</td>';
						if (!empty($objc->debit_account))
						{
							print '<td>'.$refAuxd.'</td>';
							$aHijos[$j][$k+20]['cuentadebito'] = $refAuxd;
						}
						else
						{
							print '<td>&nbsp;</td>';
						}
						if (!empty($objc->credit_account))
						{
							print '<td>'.$refAuxc.'</td>';
							$aHijos[$j][$k+20]['cuentacredito'] = $refAuxc;
						}
						else
						{
							print '<td>&nbsp;</td>';
						}
						print "<td>"."</td>\n";
						print "<td>"."</td>\n";
						print '<td>'.price(price2num($amountAux,'MT')).'</td>';
						$aHijos[$j][$k+20]['parcial'] = $amountAux;
						print '<td align="right">'."</td>\n";
						print '<td align="right">'."</td>\n";

						print '<td>'.'</td>';
						print '</tr>';
					}
					//$j++;
				}
				print '<tr class="liste_total"><td align="right" colspan="6">'.$langs->trans("Total").'</td>';
				print '<td align="right">'.price($sumDebit).'</td>';
				print '<td align="right">'.price($sumCredit).'</td>';
				print '</tr>';
				print "</table>";
				print '</div>';
				print '</td>';
				print '</tr>';

			}
		}

		print "</table>";

		$entity = $conf->entity;
		if($ObjEntity->fetch($entity) > 0){
			$labelEntity = $ObjEntity->label;
		}else{
			$labelEntity = "";
		}

		$aReporte = array(1=>$aPapas,2=>$aHijos,3=>$date_ini,4=>$date_end,5=>$type_seat,6=>$labelEntity,7 => divisa($divisa,$db));
		$_SESSION['aReporte'] = serialize($aReporte);


		print '<div class="tabsAction">'."\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
		print '</div>'."\n";
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';

		/*Aqui estaba el reporte*/
		$filename='contab/'.$period_year.'/journal';
		$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/journal';

		$modelpdf = "journal";

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
	else
	{
		setEventMessages($langs->trans('No existe registros'),null,'warnings');
	}
}



llxFooter();

function divisa($curent, $db){

	$sql  = "SELECT";
	$sql .= " t.label";
	$sql .= " FROM ".MAIN_DB_PREFIX."c_currencies as t";
	$sql .= " WHERE t.code_iso = '".$curent."'";
	//echo $sql;
	$res  = $db->query($sql);
	if ($res)
	{
		$num = $db->num_rows($res);
		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($res);
			$curentName = $obj->label;
			$i++;
		}
	}
	return $curentName;
}

$db->close();
?>