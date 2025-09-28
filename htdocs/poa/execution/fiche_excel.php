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

require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';

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

$form   = new Form($db);
$objcom = new Poapartidacom($db);
$objdev = new Poapartidadev($db);

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
$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Nro'));
$objPHPExcel->getActiveSheet()->SetCellValue('B5',$langs->trans('Gestion'));
$objPHPExcel->getActiveSheet()->SetCellValue('C5',$langs->trans('Label'));
$objPHPExcel->getActiveSheet()->SetCellValue('D5',$langs->trans('Priority'));
$objPHPExcel->getActiveSheet()->SetCellValue('E5',$langs->trans('Date'));
$objPHPExcel->getActiveSheet()->SetCellValue('F5',$langs->trans('Preventive'));
$objPHPExcel->getActiveSheet()->SetCellValue('G5',$langs->trans('Committed'));
$objPHPExcel->getActiveSheet()->SetCellValue('H5',$langs->trans('Accrued'));
$objPHPExcel->getActiveSheet()->SetCellValue('I5',$langs->trans('Statut'));
$objPHPExcel->getActiveSheet()->SetCellValue('J5',$langs->trans('User'));
$objPHPExcel->getActiveSheet()->SetCellValue('K5',$langs->trans('Area'));
$objPHPExcel->getActiveSheet()->SetCellValue('L5',$langs->trans('Tiempo'));
$objPHPExcel->getActiveSheet()->SetCellValue('M5',$langs->trans('Process'));
$objPHPExcel->getActiveSheet()->SetCellValue('N5',$langs->trans('Accion'));

$objPHPExcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArray);

$row = 6;

foreach((array) $_SESSION['aHtmlprev'] AS $i => $data)
{
  $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$data['nro']);
  $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$data['year']);
  $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$data['label']);
  $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$data['priority']);
  $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,dol_print_date($data['date_preventive'],'day'));
  $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$data['amount']);
  //buscamos el comprometido
  $objcom->getlist($data['id']);
  $sumacom = 0;
  foreach ((array) $objcom->array AS $k => $objc)
    $sumacom+=$objc->amount;
  $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$sumacom);
  //buscamos el devengado
  $objdev->getlist($data['id']);
  $sumadev = 0;
  foreach ((array) $objdev->array AS $k => $objc)
    $sumadev+=$objc->amount;
  $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$sumadev);

  $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$data['statut']);
  $objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$data['user']);
  $objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$data['area']);
  $objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$data['tiempo']);
  $objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$data['cProcess']);
  $objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$data['cMessage']);
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

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("excel/exportpre.xlsx");

header('Location: '.DOL_URL_ROOT.'/poa/execution/fiche_export.php');
// llxFooter();

// $db->close();
?>
