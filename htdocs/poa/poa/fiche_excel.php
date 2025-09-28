<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/process/fiche_exce.php
 *	\ingroup    Process export excel
 *	\brief      Page fiche poa process export excel
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

//excel

require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';


$langs->load("poa@poa");

$action=GETPOST('action');


if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

// $object  = new Mjobs($db);
// $objarea = new Poaarea($db);
// $objuser = new User($db);
// $objadh  = new Adherent($db);
// $objord  = new Mjobsorder($db);
// $objjus  = new Mjobsuser($db);
// $objcont = new Mjobscontact($db);
// $objsoc  = new Societe($db);
// $objass  = new Assets($db);
//echo '<hr>id '.$id;
/*
 * Actions
 */


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/*
 * View
 */

$form=new Form($db);


//PRCESO 1
//incluimos array estilos
include_once DOL_DOCUMENT_ROOT.'/poa/lib/format_excel.lib.php';

$objPHPExcel = new PHPExcel();

//imagen
$objDraw = new PHPExcel_Worksheet_Drawing();
$objDraw->setPath('../img/bcb.png');
$objDraw->setHeight(50);
$objDraw->setCoordinates('A1');
$objDraw->setOffsetX(10);
$objDraw->setWorksheet($objPHPExcel->getActiveSheet());

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('L4',$langs->trans('Planificado'));
$objPHPExcel->getActiveSheet()->SetCellValue('Q4',$langs->trans('Ejecutado'));
$objPHPExcel->getActiveSheet()->getStyle('A4:W4')->applyFromArray($styleArray);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('L4:P4');
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q4:U4');
$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Meta'));
$objPHPExcel->getActiveSheet()->SetCellValue('B5',$langs->trans('Descripcion'));
$objPHPExcel->getActiveSheet()->SetCellValue('C5',$langs->trans('Pseudonym'));
$objPHPExcel->getActiveSheet()->SetCellValue('D5',$langs->trans('Partida'));
$objPHPExcel->getActiveSheet()->SetCellValue('E5',$langs->trans('Presupuesto Aprob.'));
$objPHPExcel->getActiveSheet()->SetCellValue('F5',$langs->trans('Saldo al presup. Aprob.'));
$objPHPExcel->getActiveSheet()->SetCellValue('G5',$langs->trans('Preventivo'));
$objPHPExcel->getActiveSheet()->SetCellValue('H5',$langs->trans('Comprometido'));
$objPHPExcel->getActiveSheet()->SetCellValue('I5',$langs->trans('Devengado'));
$objPHPExcel->getActiveSheet()->SetCellValue('J5',$langs->trans('Pac'));
$objPHPExcel->getActiveSheet()->SetCellValue('K5',$langs->trans('Usuario'));
//planificacion
$objPHPExcel->getActiveSheet()->SetCellValue('L5',$langs->trans('Preventive'));
$objPHPExcel->getActiveSheet()->SetCellValue('M5',$langs->trans('Ini.Proceso'));
$objPHPExcel->getActiveSheet()->SetCellValue('N5',$langs->trans('Contrat'));
$objPHPExcel->getActiveSheet()->SetCellValue('O5',$langs->trans('Provisional'));
$objPHPExcel->getActiveSheet()->SetCellValue('P5',$langs->trans('Concluido'));
//ejecucion
$objPHPExcel->getActiveSheet()->SetCellValue('Q5',$langs->trans('Preventive'));
$objPHPExcel->getActiveSheet()->SetCellValue('R5',$langs->trans('Ini.Proceso'));
$objPHPExcel->getActiveSheet()->SetCellValue('S5',$langs->trans('Contrat'));
$objPHPExcel->getActiveSheet()->SetCellValue('T5',$langs->trans('Provisional'));
$objPHPExcel->getActiveSheet()->SetCellValue('U5',$langs->trans('Concluido'));
$objPHPExcel->getActiveSheet()->SetCellValue('V5',$langs->trans('Followup'));
$objPHPExcel->getActiveSheet()->SetCellValue('W5',$langs->trans('Followto'));
//agregando nuevas columnas
$objPHPExcel->getActiveSheet()->SetCellValue('X5',$langs->trans('Mespac'));
$objPHPExcel->getActiveSheet()->SetCellValue('Y5',$langs->trans('Estado'));
$objPHPExcel->getActiveSheet()->SetCellValue('Z5',$langs->trans('Localizacion'));
$objPHPExcel->getActiveSheet()->SetCellValue('AA5',$langs->trans('docum_verif'));
$objPHPExcel->getActiveSheet()->SetCellValue('AB5',$langs->trans('Pac_requiere'));

$objPHPExcel->getActiveSheet()->getStyle('A5:AB5')->applyFromArray($styleArray);

$row = 6;
foreach((array) $_SESSION['aHtml'] AS $i => $data)
{
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$data['sigla']);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$data['label']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$data['pseudonym']);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$data['partida']);
	if ($data['filameta'])
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$data['nTotalAp']);
	else
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$data['presupuesto']);
	if ($data['filameta'])
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,round($data['nTotalAp']-$data['preventivo'],2));
	else
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,'');
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$data['preventivo']);
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$data['comprometido']);
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$data['devengado']);
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$data['pac']);
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$data['user']);
  	//planificacion
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,dol_print_date($data['programed']['PREVENTIVE'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,dol_print_date($data['programed']['INI_PROCES'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,dol_print_date($data['programed']['RECEP_PRODUCTS'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row,dol_print_date($data['programed']['PARTIAL_REPORT_ACCORDANCE'],'day'));
	if (!empty($data['programed']['FINISH1']))
		$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row,dol_print_date($data['programed']['FINISH1'],'day'));
	if (!empty($data['programed']['ENDED2']))
		$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row,dol_print_date($data['programed']['ENDED2'],'day'));
  	//EJECUTION
	$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row,dol_print_date($data['PREVENTIVE'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('R'.$row,dol_print_date($data['INI_PROCES'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('S'.$row,dol_print_date($data['RECEP_PRODUCTS'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row,dol_print_date($data['PARTIAL_REPORT_ACCORDANCE'],'day'));
	if (!empty($data['CLOSED']))
		$objPHPExcel->getActiveSheet()->SetCellValue('U'.$row,dol_print_date($data['CLOSED'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('V'.$row,$data['followup']);
	$objPHPExcel->getActiveSheet()->SetCellValue('W'.$row,$data['followto']);
	$objPHPExcel->getActiveSheet()->SetCellValue('X'.$row,$data['mespac']);
	$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$row,$data['estadoact']);
	$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$row,$data['code_area_next']);
	$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$row,$data['doc_verif']);
	$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$row,$data['paqreq']);

	if ($data['filameta'])
	{
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':C'.$row)->applyFromArray($styleArrayMeta);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$row.':J'.$row)->applyFromArray($styleArrayMetar);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$row.':V'.$row)->applyFromArray($styleArrayMeta);
	}
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
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("excel/exportpoa.xlsx");

header('Location: '.DOL_URL_ROOT.'/poa/poa/fiche_export.php');
// llxFooter();

// $db->close();
?>
