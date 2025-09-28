<?php
/* Copyright (C) 2014 Ramiro Queso Cusi <ramiroques@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *       \file       htdocs/rhuman/book_sales.php
 *       \ingroup    export excel
 *       \brief      Page of member
 */

// if (!isset($_GET['dol_hide_topmenu']))
//   {
//     $_GET['dol_hide_topmenu']=1;
//     $_GET['dol_hide_leftmenu']=1;
//   }
require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/bank.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
//require_once(DOL_DOCUMENT_ROOT.'/ventas/lib/account.lib.php');
//require_once(DOL_DOCUMENT_ROOT.'/ventas/lib/ventas.lib.php');

require_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");

require_once(DOL_DOCUMENT_ROOT."/fiscal/class/subsidiaryext.class.php");
//require_once(DOL_DOCUMENT_ROOT."/ventas/permiso/class/entrepotbanksoc.class.php");
require_once(DOL_DOCUMENT_ROOT."/fiscal/class/vdosing.class.php");

require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
//require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';

//excel
//verificamos si existe
$res = 0;
if (! $res && file_exists(DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php'))
	 $res=@require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
elseif(file_exists(DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php'))
	$res=@require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
$res = 0;
if (! $res && file_exists(DOL_DOCUMENT_ROOT.'/includes/phpexcel/IOFactory.php'))
	 $res=@require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/IOFactory.php';
elseif(file_exists(DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php'))
	$res=@require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


$langs->load("banks");
$langs->load("ventas@ventas");
$langs->load('other');

if (! $user->rights->sales->lire)
	accessforbidden();

$month = GETPOST('month');
$year  = GETPOST('year');
$fk_subsidiaryid = GETPOST('fk_subsidiaryid');
if (empty($month)) $month = date('m');
if (empty($year)) $year = date('Y');
$fechaIni = $year.'-'.$month.'-'.'01';
$fechaIni = dol_get_first_day($year,$month);

$borders = array(
	'borders'=>
	array('allborders'=>
		array('style'=>PHPExcel_Style_Border::BORDER_THIN,
			'color'=> array('argb'=>'FF0000')
			)
		),
	'font'=>array('bold'=>true,)
	);

$styleArray = array(
	'font' => array(
		'bold' => true,
		'color'=>array('argb'=>'FF0000'),
		),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color'=>array('argb'=>'FF0000'),
			),
		),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		'rotation' => 90,
		'startcolor' => array(
			'argb' => 'FFA0A0',
			),
		'endcolor' => array(
			'argb' => 'FFFFFF',
			),
		),
	);

$stylebodyArray = array(
	'font' => array(
		'bold' => false,
		'color'=>array('argb'=>'000000'),
		),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color'=>array('argb'=>'000000'),
			),
		),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		'rotation' => 90,
		'startcolor' => array(
			'argb' => 'FFA0A0',
			),
		'endcolor' => array(
			'argb' => 'FFFFFF',
			),
		),
	);

$styletotalArray = array(
	'font' => array(
		'bold' => true,
		'color'=>array('argb'=>'000000'),
		),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color'=>array('argb'=>'000000'),
			),
		),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		'rotation' => 90,
		'startcolor' => array('argb' => '259e39',
			),
		'endcolor' => array('argb' => 'FFFFFF',
			),
		),
	);


$objectsu = new Subsidiaryext($db);
$societe = new Societe($db);
$dosing  = new Vdosing($db);
$objfacture = new Facture($db);
$societe->fetch($_SESSION['fkSocid']);

$action=GETPOST('action','alpha');

$form=new Form($db);
$formother=new FormOther($db);

//$fecha = new DateTime($fechaIni);
//$fecha->modify('first day of this month');
//$fechaIni = $fecha->format('Y-m-d');
//$fecha->modify('last day of this month');
//$fechaFin = $fecha->format('Y-m-d');
$fechaFin = dol_get_last_day($year,$month);

if ($action == 'create' && $user->rights->sales->lire)
{
	$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
	llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);
	print_fiche_titre($langs->trans("Booksales"));

	//armamos para cuando se seleccione una actividad verifique si ya tiene creado la ejecucion (preventivo)
/*	print "\n".'<script type="text/javascript" language="javascript">';
//	print '$(document).ready(function () {';
//	print '$("#selectfk_subsidiaryid'.$k.'").change(function() {';
//	print ' document.form_fiche.action.value="create_a";
				document.form_fiche.submit();
			  });
		  });';
//	print '</script>'."\n";
*/
	print '<form id="form_fiche" name="form_fiche" action="booksales.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="report">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	//date delivery
	// Date de livraison
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Month").'</td><td colspan="2">';
	//print select_monthv($month,'month','',15,1);
	print $formother->select_month($month,'month',1,1);
	print "</td></tr>";

	//description
	print '<tr><td width="25%" class="field">'.$langs->trans("Year").'</td><td colspan="3">';
	print '<input type="number" name="year" value="'.$year.'">';
	print '</td></tr>';

	// subsidiary
	print '<tr><td width="25%">'.$langs->trans('Subsidiary').'</td><td colspan="3">';
	print $objectsu->select_subsidiary($object->fk_subsidiaryid,'fk_subsidiaryid','',0,0);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
elseif($action=='report')
{



//vincular con facture.class.php
//ventas
	$sql = " SELECT f.rowid, CONVERT(f.nfiscal, SIGNED) AS nfiscal, f.serie, f.fk_dosing, f.fk_facture, fk_cliepro, f.nit, f.razsoc, f.date_exp, f.type_op, f.num_autoriz, f.cod_control, f.baseimp1, f.baseimp2, f.aliqimp1, f.aliqimp2, f.valimp1, f.valimp2, f.valret1, f.valret2, f.status, ";
	$sql.= " ff.tva AS tvaf, ff.total AS totalf, ff.total_ttc AS total_ttcf, ";
	$sql.= " d.lote, ";
	$sql.= " s.label, s.socialreason ";

	$sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal AS f ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture AS ff ON f.fk_facture = ff.rowid";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."v_dosing AS d ON f.fk_dosing = d.rowid";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."subsidiary AS s ON d.fk_subsidiaryid = s.rowid";

	$sql.= " WHERE f.entity = ".$conf->entity;
	//$sql.= " AND f.date_exp BETWEEN ".$fechaIni." AND ".$fechaFin;
	//$sql.= " AND f.date_exp >= ".$fechaIni." AND f.date_exp <= ".$fechaFin;
	$sql.= " AND YEAR(f.date_exp) = ".$year;
	$sql.= " AND MONTH(f.date_exp) = ".$month;
	$sql.= " AND d.fk_subsidiaryid = ".$fk_subsidiaryid;

	$sql.= " ORDER BY f.num_autoriz, f.date_exp, nfiscal, f.serie";

	//echo $sql.= $db->plimit($limit+1, $offset);
	$objectsu->fetch($fk_subsidiaryid);
	//PRCESO 1
	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("excel/formatolcv.xlsx");
	$objPHPExcel->setActiveSheetIndex(1);

	//$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('LIBRO DE VENTAS'));
	//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
	//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');

	//$objPHPExcel->getActiveSheet()->SetCellValue('C3',$langs->trans('Periodo'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('C4',$langs->trans('Year'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('D4',$year);
	//$objPHPExcel->getActiveSheet()->SetCellValue('E4',$langs->trans('Month'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('F4',$month);

	//$objPHPExcel->getActiveSheet()->SetCellValue('C6',$langs->trans('NOMBRE O RAZON SOCIAL'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('D6',$objectsu->label);
	//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('D6:J6');

	//$objPHPExcel->getActiveSheet()->SetCellValue('K6',$langs->trans('NIT'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('L6',$objectsu->nit);
	//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('L6:M6');

	//fila de titulos

	//$objPHPExcel->getActiveSheet()->SetCellValue('A8',$langs->trans('N°'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('B8',$langs->trans('FECHA FACTURA'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('C8',$langs->trans('N° DE FACTURA'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('D8',$langs->trans('N° DE AUTORIZACION'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('E8',$langs->trans('ESTADO'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('F8',$langs->trans('NIT/CI CLIENTE'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('G8',$langs->trans('NOMBRE O RAZON SOCIAL'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('H8',$langs->trans('IMPORTE TOTAL DE VENTA'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('I8',$langs->trans('IMPORTE ICE/IEMD/TASAS'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('J8',$langs->trans('EXPORTACIONES Y OPERACIONES EXENTAS'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('K8',$langs->trans('VENTAS GRAVADAS A TASA CERO'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('L8',$langs->trans('SUBTOTAL'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('M8',$langs->trans('DESCUENTOS, BONIFICACIONES Y REBAJAS OTORGADAS'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('N8',$langs->trans('IMPORTE BASE PARA DEBITO FISCAL'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('O8',$langs->trans('DEBITO FISCAL'));
	//$objPHPExcel->getActiveSheet()->SetCellValue('P8',$langs->trans('CODIGO DE CONTROL'));


	//$objPHPExcel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray);
	$row = 2;
	$result = $db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		$i = 0;
		$lin = 1;
		//$help_url='EN:Module_CajaChica_En|FR:Module_CajaChica|ES:M&oacute;dulo_CajaChica';
		if ($num)
		{
			$var=True;
			while ($i < $num)
			{
				//echo '<hr>i '.$i;
				$objp = $db->fetch_object($result);
				$baseimp1 = $objp->baseimp1;
				$valimp1 = $objp->valimp1;
				//echo ' '.$baseimp1.' '.$valimp1;
				//if (empty($objp->baseimp1) && $objp->baseimp1 != $objp->total_ttc)
				//{
				//	$baseimp1 = $objp->total_ttc;
				//	$valimp1 = $objp->tvaf;
				//}

					$status =  ($objp->status==1?'V':'A');
					if($objp->status == 1 && $objp->lote == 1 && !empty($conf->global->FISCAL_CODE_FACTURE_SALES_MANUAL))
						$status = $conf->global->FISCAL_CODE_FACTURE_SALES_MANUAL;
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,3);
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lin);
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,dol_print_date($db->jdate($objp->date_exp),'day'));
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$objp->nfiscal);
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$objp->num_autoriz);
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$status);
					$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,($status=='A'?0:$objp->nit));
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,($status=='A'?'ANULADA':STRTOUPPER($objp->razsoc)));
					$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,($status=='A'?0:$baseimp1));
					$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,'0');
					$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,'0');
					$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,'0');
					$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,($status=='A'?0:$baseimp1));
					$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,'0');
					$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row,($status=='A'?0:$baseimp1));
					$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row,($status=='A'?0:$valimp1));
					$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row,($objp->lote==1?0:$objp->cod_control));
					//$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row,($status=='A'?0:$objp->cod_control));
					$lin++;
					$row++;
					$sumaVenta += $objp->baseimp1;
					$i++;

			}
			// //suma total
			// $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,STRTOUPPER($langs->trans('Total')));
			// $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,price2num($sumaVenta,'MU'));
			// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row.':H'.$row);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray($styletotalArray);
			// $row++;
		}
		$db->free($result);

	}
	else
	{
		dol_print_error($db);
	}

	//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/export.xlsx");

	header('Location: fiche_export.php');
}
?>
