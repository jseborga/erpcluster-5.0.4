<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/report/otr_excel.php
 *	\ingroup    Report excel
 *	\brief      Page fiche mant reports
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

$langs->load("mant@mant");
$langs->load("others");


$date_ini  = $_SESSION['date_iniot'];
$date_fin  = $_SESSION['date_finot'];
$level     = $_SESSION['levelot'];
$aDataot   = $_SESSION['aDataot'];
$aDatatask = $_SESSION['aDatatask'];
$fk_user   = $_SESSION['fk_userot'];
$fk_contact= $_SESSION['fk_contactot'];

$alevel = array(0=>'Todos',
        2=>'Validados',
        3=>'Programados',
        4=>'Concluidos',
        5=>'Validados,Programados,Concluidos');

$object  = new Mjobs($db);
$objjus  = new Mjobsuser($db);
$objcont = new Mjobscontact($db);
$objsoc  = new Societe($db);
$objUser = new User($db);
$objContact = new Contact($db);

//$object->getlist('',$date_ini,$date_fin,$level);
if (!empty($aDatatask))
{
    //incluimos array estilos
    include_once DOL_DOCUMENT_ROOT.'/mant/lib/format_excel.lib.php';
     //PRCESO 1
    $objPHPExcel = new PHPExcel();
    // $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    // $objPHPExcel = $objReader->load("excel/iniproceso_mod.xlsx");
    $objPHPExcel->setActiveSheetIndex(0);

    //variables
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',STRTOUPPER($langs->trans('Dateini')));
    $objPHPExcel->getActiveSheet()->SetCellValue('B1',dol_print_date($date_ini,'dayhour'));
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',STRTOUPPER($langs->trans('Datefin')));
    $objPHPExcel->getActiveSheet()->SetCellValue('B2',dol_print_date($date_fin,'dayhour'));
    $row = 3;
    if ($fk_contact > 0)
    {
        $objContact->fetch($fk_contact);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,STRTOUPPER($langs->trans('Contact')));
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$objContact->lastname.' '.$objContact->firstname);
        $row++;
    }
    if($fk_user >0)
    {
        $objUser->fetch($fk_user);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,STRTOUPPER($langs->trans('User')));
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$objUser->lastname.' '.$objUser->firstname);
        $row++;
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,STRTOUPPER($langs->trans('Nivel')));
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$alevel[$level]);
    $row++;
    $row++;
    //titulo
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,STRTOUPPER($langs->trans('Resumen Ordenes de Trabajo y Tareas ejecutadas')));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row.':E'.$row);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray);
    $row++;
    $row++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$langs->trans('Clasif 1'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$langs->trans('Clasif 2'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$langs->trans('Speciality'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$langs->trans('Nro. OT'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$langs->trans('Nro. Tareas'));

    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray);

    $row++;
    foreach((array) $aDatatask AS $working => $aDatatype)
      {
	foreach((array) $aDatatype AS $typemant => $aDataspec)
	  {
	    foreach((array) $aDataspec AS $speciality => $value)
	      {

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,($working == 'generic'?$langs->trans('Generic'):select_working_class($working,'','',0,1)));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,select_typemant($typemant,'','',0,1));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,select_speciality($speciality,'','',0,1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$aDataot[$working][$typemant][$speciality]);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$value);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($stylebodyArray);
		$row++;
		$totalot += $aDataot[$working][$typemant][$speciality];
		$totaltask += $value;
	      }
	  }
      }
    //totales
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$langs->trans('Total'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row.':C'.$row);

    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$totalot);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$totaltask);

    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($stylebodyArray);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save("excel/exportr.xlsx");

    header('Location: '.DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=exportr.xlsx');

  }

llxFooter();

$db->close();
?>
