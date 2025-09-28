<?php
/* Copyright (C) 2014-2016 Ramiro Queso        <ramiro@ubuntu-bo.com>
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

require("../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

//item
//require_once DOL_DOCUMENT_ROOT.'/budget/items/class/items.class.php';
//require_once DOL_DOCUMENT_ROOT.'/budget/typeitem/class/typeitem.class.php';

//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


$langs->load("monprojet@monprojet");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$action    = GETPOST('action');


$mesg = '';

$object  = new Projectext($db);
$task    = new Task($db);
$taskadd = new Taskext($db);
$objecttaskadd = new Projettaskadd($db);
$taskstatic = new Task($db);
//$items = new Items($db);
//$typeitem = new Typeitem($db);
$objUser = new User($db);
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

// $title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
// if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
// $help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
// llxHeader("",$title,$help_url);

$form=new Form($db);


//recuperaos informacion
$object->fetch($id);


$modetask = 0;
$tasksarray=$taskadd->getTasksArray(0, 0, $object->id, $socid, $modetask);
$tasksrole=($mode=='mine' ? $taskstatic->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');

$aData = array();
$j=0; $level=0;
$aData=monprojectLineexport($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$lVista,$aData);
$numrows = count($aData);
if ($numrows > 0)
  {
    include_once DOL_DOCUMENT_ROOT.'/monprojet/lib/format_excel.lib.php';

    //PRCESO 1
    $objPHPExcel = new PHPExcel();
    // $objPHPExcel->setCreator("Cattivo");
    // $objPHPExcel->setLastModifiedBy("Cattivo");
    // $objPHPExcel->setTitle("Documento Excel de Prueba");
    // $objPHPExcel->setSubject("Documento Excel de Prueba");
    // $objPHPExcel->setDescription("Demostracion sobre como crear archivos de Excel desde PHP.");
    // $objPHPExcel->setKeywords("Excel Office 2007 openxml php");
    // $objPHPExcel->setCategory("Pruebas de Excel");

    // $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    // $objPHPExcel = $objReader->load("excel/form_2180-011_v.1.xlsx");
    // $aDate = dol_getdate($object->date_ini);

    // //imagen
    // $objDraw = new PHPExcel_Worksheet_Drawing();
    // $objDraw->setPath('../img/bcb.png');
    // $objDraw->setHeight(50);
    // $objDraw->setCoordinates('E2');
    // $objDraw->setOffsetX(10);
    // $objDraw->setWorksheet($objPHPExcel->getActiveSheet());

    //cabecera
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->SetCellValue('A1','ref');
    $objPHPExcel->getActiveSheet()->SetCellValue('B1','label');
    $objPHPExcel->getActiveSheet()->SetCellValue('C1','hilo');
    $objPHPExcel->getActiveSheet()->SetCellValue('D1','item');
    $objPHPExcel->getActiveSheet()->SetCellValue('E1','login');
    $objPHPExcel->getActiveSheet()->SetCellValue('F1','fechaini');
    $objPHPExcel->getActiveSheet()->SetCellValue('G1','fechafin');
    $objPHPExcel->getActiveSheet()->SetCellValue('H1','detail');
    $objPHPExcel->getActiveSheet()->SetCellValue('I1','group');
    $objPHPExcel->getActiveSheet()->SetCellValue('J1','type');
    $objPHPExcel->getActiveSheet()->SetCellValue('K1','typename');
    $objPHPExcel->getActiveSheet()->SetCellValue('L1','unitprogram');
    $objPHPExcel->getActiveSheet()->SetCellValue('M1','unit');
    $objPHPExcel->getActiveSheet()->SetCellValue('N1','price');
    $objPHPExcel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($styleArray);

    $row = 2;
    $var = true;
    foreach ($aData AS $i => $lines)
    {
	   $task->fetch($lines['id']);
	   $aRes = verifcontacttask($user,$task,'res',1);
	   $aUsers = $aRes[2]['user'];
	   $logins = '';
	   foreach ((array) $aUsers AS $iduser)
	   {
	       $objUser->fetch($iduser);
	       if ($logins) $logins.= '; ';
	       $logins.= $objUser->login;
	   }
	   $var = !$var;
	   $refparent = '';
	   $refitem = '';
	   $reftype = '';
	   $unit = '';
	   $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$lines['ref']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lines['label']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$lines['parent']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$lines['item']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$logins);
	   $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$lines['date_start']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row,$lines['date_end']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row,$lines['detail']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row,$lines['group']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('J'.$row,$lines['type']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('K'.$row,$lines['typename']);
       $objPHPExcel->getActiveSheet()->SetCellValue('L'.$row,$lines['unit_program']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('M'.$row,$lines['unit_code']);
	   $objPHPExcel->getActiveSheet()->SetCellValue('N'.$row,$lines['unit_amount']);

	   if ($lines['group']>0)
	   {
	       $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray($styleArrayGroup);
	       $objPHPExcel->getActiveSheet()->getStyle('L'.$row.':L'.$row)->applyFromArray($styleArrayGroupn);
	       $objPHPExcel->getActiveSheet()->getStyle('M'.$row.':M'.$row)->applyFromArray($styleArrayGroup);
	       $objPHPExcel->getActiveSheet()->getStyle('N'.$row.':N'.$row)->applyFromArray($styleArrayGroupn);
	   }
	   elseif ($var)
	   {
	       $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray($styleArrayPar);
	       $objPHPExcel->getActiveSheet()->getStyle('L'.$row.':L'.$row)->applyFromArray($styleArrayParn);
	       $objPHPExcel->getActiveSheet()->getStyle('M'.$row.':M'.$row)->applyFromArray($styleArrayPar);
	       $objPHPExcel->getActiveSheet()->getStyle('N'.$row.':N'.$row)->applyFromArray($styleArrayParn);
	   }
	   else
	   {
	       $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray($styleArrayImpar);
	       $objPHPExcel->getActiveSheet()->getStyle('L'.$row.':L'.$row)->applyFromArray($styleArrayImparn);
	       $objPHPExcel->getActiveSheet()->getStyle('M'.$row.':M'.$row)->applyFromArray($styleArrayImpar);
	       $objPHPExcel->getActiveSheet()->getStyle('N'.$row.':N'.$row)->applyFromArray($styleArrayImparn);
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
//exit;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $_SESSION['docsave'] = 'exporttask.xlsx';
    $objWriter->save("excel/".$_SESSION['docsave']);

    header('Location: '.DOL_URL_ROOT.'/monprojet/fiche_export.php');

  }

?>
