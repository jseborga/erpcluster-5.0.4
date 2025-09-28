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
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsmaterialused.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

$langs->load("mant@mant");
$langs->load("others");


$date_ini = $_SESSION['date_iniot'];
$date_fin = $_SESSION['date_finot'];
$level = $_SESSION['levelot'];

$object = new Mjobs($db);
$objectorder = new Mjobsorder($db);
$objectused  = new Mjobsmaterialused($db);

$objjus  = new Mjobsuser($db);
$objcont = new Mjobscontact($db);
$objsoc  = new Societe($db);

$object->getlist('',$date_ini,$date_fin,$level);
if (count($object->array) > 0)
  {
    //incluimos array estilos
    include_once DOL_DOCUMENT_ROOT.'/mant/lib/format_excel.lib.php';
     //PRCESO 1
    $objPHPExcel = new PHPExcel();
    // $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    // $objPHPExcel = $objReader->load("excel/iniproceso_mod.xlsx");
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->SetCellValue('A1',STRTOUPPER($langs->trans('Order jobs')));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:L1');

    $objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Ref'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B2',$langs->trans('Datecreate'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C2',$langs->trans('Email'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D2',$langs->trans('Detailproblem'));
    // $objPHPExcel->getActiveSheet()->SetCellValue('E2',$langs->trans('Dateassign'));
    // $objPHPExcel->getActiveSheet()->SetCellValue('F2',$langs->trans('Descriptionassign'));
    // $objPHPExcel->getActiveSheet()->SetCellValue('G2',$langs->trans('Dateiniprog'));
    // $objPHPExcel->getActiveSheet()->SetCellValue('H2',$langs->trans('Datefinprog'));
    // $objPHPExcel->getActiveSheet()->SetCellValue('I2',$langs->trans('Descriptionprogram'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E2',$langs->trans('Dateini'));
    $objPHPExcel->getActiveSheet()->SetCellValue('F2',$langs->trans('Datefin'));
    $objPHPExcel->getActiveSheet()->SetCellValue('G2',$langs->trans('Descriptionjob'));
    $objPHPExcel->getActiveSheet()->SetCellValue('H2',$langs->trans('Technicians'));
    $objPHPExcel->getActiveSheet()->SetCellValue('I2',$langs->trans('Orders'));
    $objPHPExcel->getActiveSheet()->SetCellValue('J2',$langs->trans('Materialused'));
    $objPHPExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray);

    $row = 3;
    foreach((array) $object->array AS $id => $obj)
      {	
	$objsoc->fetch($obj->fk_soc);
	$aContact = $objsoc->contact_array();    
	
	//contactos 
	$aJobsContact = $objcont->list_contact($obj->id);
	//internos
	$aJobsUsers   = $objjus->list_jobsuser($obj->id);
	$listecontact = '';
	foreach ((array) $aJobsContact AS $k => $objtmp)
	  {
	    if (!empty($listecontact))
	      $listecontact .= ', ';
	    $listecontact .= $aContact[$objtmp->fk_contact];
	  }
	foreach ((array) $aJobsUser AS $k => $objtmp)
	  {
	    if (!empty($listecontact))
	      $listecontact .= ', ';
	    $objt = $aContact[$objtmp->id];
	    $listecontact .= $objt->firstname.' '.$objt->lastname;
	  }
	

	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$obj->ref);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,dol_print_date($obj->date_create,'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$obj->email);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$obj->detail_problem);
	// $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,dol_print_date($obj->date_assign,'day'));
	
	// $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$obj->description_assign);
	// $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,dol_print_date($obj->date_ini_prog,'day'));
	// $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,dol_print_date($obj->date_fin_prog,'day'));
	// $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$obj->description_prog);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,dol_print_date($obj->date_ini,'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,dol_print_date($obj->date_fin,'day'));
	
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$obj->description_job);
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$listecontact);

	//ORDER
	//buscamos los materiales utilizados
	$aOrder = $objectorder->list_order($obj->id);
	$htmorder = '';
	if (count($aOrder)>0)
	  {
	    foreach ((array) $aOrder AS $j => $objorder)
	      {
		if ($htmorder) $htmorder.= ' | ';
		$htmorder.= $objorder->order_number.' : ';
		$htmorder.= $objorder->description;
	      }
	  }
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$htmorder);

	//MATERIALUSED

	//buscamos los materiales utilizados
	$htmused = '';
	$objectused->getlist($obj->id);
	if (count($objectused->array)>0)
	  {
	    foreach ((array) $objectused->array AS $j => $objorder)
	      {
		if ($htmused) $htmused.= ' | ';
		$htmused.= $objorder->ref.' : '.$objorder->description.'; cantidad: '.$objorder->quant. ' '.$objorder->unit;
	      }
	  }
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$htmused);

	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->applyFromArray($stylebodyArray);	
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
    // $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);


    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save("excel/exportm.xlsx");
    
    header('Location: '.DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=exportm.xlsx');
   
  }

llxFooter();

$db->close();
?>
