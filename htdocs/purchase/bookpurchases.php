<?php
/* Copyright (C) 2016 Ramiro Queso Cusi <ramiroques@gmail.com>
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
require_once(DOL_DOCUMENT_ROOT."/core/lib/bank.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';

//tablas de compras
require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';

//tablas de fiscal
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityaddext.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/facturefourndetfiscalext.class.php';
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
$langs->load("purchase@purchase");
$langs->load('other');

if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'purchase', $socid, '', 'rep');

$month = GETPOST('month');
$year  = GETPOST('year');
$nit   = GETPOST('nit');
$code_facture = GETPOST('code_facture');

//$fk_subsidiaryid = GETPOST('fk_subsidiaryid');
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

//$objectsu = new Subsidiary($db);
$societe = new Societe($db);
//$dosing  = new Vdosing($db);
$object = new FactureFournisseurext($db);
$objectadd = new Facturefournadd($db);
$objentityadd = new Entityaddext($db);

//$societe->fetch($_SESSION['fkSocid']);

$action=GETPOST('action','alpha');

$form=new Formv($db);
$formother=new FormOther($db);

//$fecha = new DateTime($fechaIni);
//$fecha->modify('first day of this month');
//$fechaIni = $fecha->format('Y-m-d');
//$fecha->modify('last day of this month');
//$fechaFin = $fecha->format('Y-m-d');
$fechaFin = dol_get_last_day($year,$month);

if ($action == 'create' && $user->rights->purchase->rep->creer)
{
	$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
	llxHeader("",$langs->trans("Bookpurchases"),$help_url);
	print_fiche_titre($langs->trans("Bookpurchases"));
	dol_htmloutput_mesg($mesg);

	print '<form id="form_fiche" name="form_fiche" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="report">';


	print '<table class="border" width="100%">';

	//select entity

	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Company").'</td><td colspan="2">';
	list($nb,$options) = $objentityadd->select_entity('nit',(!empty($conf->global->MAIN_INFO_TVAINTRA)?trim($conf->global->MAIN_INFO_TVAINTRA):''),1,1);
	if ($nb>0)
		print '<select name="nit">'.$options.'</select>';
	else
		print 'nd';
	print "</td></tr>";
	//date delivery
	//month
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Month").'</td><td colspan="2">';
	//print select_monthv($month,'month','',15,1);
	print $formother->select_month($month,$htmlname='month',1,1);
	print "</td></tr>";
	//year
	print '<tr><td width="25%" class="field">'.$langs->trans("Year").'</td><td colspan="3">';
	print '<input type="number" name="year" value="'.$year.'">';
	print '</td></tr>';
	//code_facture
	$code_facture = $conf->global->FISCAL_CODE_FACTURE_PURCHASE;
	print '<tr><td width="25%" class="field">'.$langs->trans("Typefacture").'</td><td colspan="3">';
	$typefilter=0;
	print $form->load_type_facture('code_facture',(GETPOST('code_facture')?GETPOST('code_facture'):$code_facture),0, 'code', false,$typefilter);
	print '</td></tr>';

	// subsidiary
	//print '<tr><td width="25%">'.$langs->trans('Subsidiary').'</td><td colspan="3">';
	//print $objectsu->select_subsidiary($object->fk_subsidiaryid,'fk_subsidiaryid','',0,0);
	//print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
elseif($action=='report')
{
	$filter = " AND t.nit = '".$nit."'";
	$res = $objentityadd->fetchAll('','',0,0,array(1=>1),"AND",$filter,1);

	//vincular con facture.class.php
	//compras
	$sql = "SELECT";
	$sql.= " t.rowid,";
	$sql.= " t.ref,";
	$sql.= " t.ref_supplier,";
	$sql.= " t.entity,";
	$sql.= " t.type,";
	$sql.= " t.fk_soc,";
	$sql.= " t.datec,";
	$sql.= " t.datef,";
	$sql.= " t.tms,";
	$sql.= " t.libelle,";
	$sql.= " t.paye,";
	$sql.= " t.amount,";
	$sql.= " t.remise,";
	$sql.= " t.close_code,";
	$sql.= " t.close_note,";
	$sql.= " t.tva,";
	$sql.= " t.localtax1,";
	$sql.= " t.localtax2,";
	$sql.= " t.total,";
	$sql.= " t.total_ht,";
	$sql.= " t.total_tva,";
	$sql.= " t.total_ttc,";
	$sql.= " t.fk_statut,";
	$sql.= " t.fk_user_author,";
	$sql.= " t.fk_user_valid,";
	$sql.= " t.fk_facture_source,";
	$sql.= " t.fk_projet,";
	$sql.= " t.fk_cond_reglement,";
	$sql.= " t.fk_account,";
	$sql.= " t.fk_mode_reglement,";
	$sql.= " t.date_lim_reglement,";
	$sql.= " t.note_private,";
	$sql.= " t.note_public,";
	$sql.= " t.model_pdf,";
	$sql.= " t.import_key,";
	$sql.= " t.extraparams,";
	//$sql.= " cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle,";
	//$sql.= " p.code as mode_reglement_code, p.libelle as mode_reglement_libelle,";
	$sql.= ' s.nom as socnom, s.rowid as socid,';
	$sql.= ' t.fk_incoterms, t.location_incoterms,';
	//$sql.= " i.libelle as libelle_incoterms,";
	$sql.= ' t.fk_multicurrency, t.multicurrency_code, t.multicurrency_tx, t.multicurrency_total_ht, t.multicurrency_total_tva, t.multicurrency_total_ttc';
	$sql.= " , ta.nfiscal, ta.ndui, ta.num_autoriz, ta.nit_company, ta.nit, ta.razsoc, ta.cod_control, ta.amountfiscal, ta.amountnofiscal, ta.amount_ice, ta.discount, ta.datec ";
	$sql.= ' FROM '.MAIN_DB_PREFIX.'facture_fourn as t';
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture_fourn_add as ta ON (ta.fk_facture_fourn = t.rowid)";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON (t.fk_soc = s.rowid)";

	$sql.= " WHERE t.entity=".$conf->entity;
	$sql.= " AND YEAR(t.datef) = ".$year;
	$sql.= " AND MONTH(t.datef) = ".$month;
	$sql.= " AND ta.code_facture = '".$code_facture."'";
	$sql.= " AND ta.nit_company = '".trim($nit)."'";
	$sql.= " ORDER BY t.datef, t.tms, ta.nfiscal";

	//echo $sql.= $db->plimit($limit+1, $offset);
	//$objectsu->fetch($fk_subsidiaryid);
	//PRCESO 1
	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("excel/formatolcv.xlsx");
	$objPHPExcel->setActiveSheetIndex(0);



	//$objPHPExcel->getActiveSheet()->getStyle('A8:P8')->applyFromArray($styleArray);
	$row = 6;
	$result = $db->query($sql);
	if ($result)
	{
		//cargamos la cabecera
		$period_fiscal = $month.'/'.$year;
		$objPHPExcel->getActiveSheet()->SetCellValue('B2',$period_year);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3',$objentityadd->socialreason);
		$objPHPExcel->getActiveSheet()->SetCellValue('B4',$objentityadd->address);
		$objPHPExcel->getActiveSheet()->SetCellValue('P2',$objentityadd->nit);
		$objPHPExcel->getActiveSheet()->SetCellValue('P3','0');

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
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,1);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lin);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,dol_print_date($db->jdate($objp->datef),'day'));
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$objp->nit);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$objp->razsoc);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$objp->nfiscal);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,($objp->ndui?$objp->ndui:0));
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$objp->num_autoriz);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$objp->total_ttc+$objp->discount);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$objp->amountnofiscal+$objp->amount_ice);
				$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$objp->total_ttc+$objp->discount - $objp->amountnofiscal);
				$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$objp->discount);
				$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$objp->amountfiscal);
				$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$objp->total_tva);
				$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row,$objp->cod_control);
				$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row,1);
				//$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row,$objp->cod_control);
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
	$objPHPExcel->removeSheetByIndex(1);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/export.xlsx");

	header('Location: fiche_export.php');
}
?>
