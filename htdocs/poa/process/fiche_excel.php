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

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprevprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';


$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');

if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

$object  = new Poaprocess($db);
$objarea = new Poaarea($db);
$objuser = new User($db);
$objprev = new Poaprev($db);
$objpac  = new Poapac($db);


/*
 * Actions
 */


if ($_POST["cancel"] == $langs->trans("Cancel"))
  {
    $action = '';
    $_GET["id"] = $_POST["id"];
  }
// print_r($_POST);
// exit;

/*
 * View
 */

$form=new Form($db);

// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("POA"),$help_url);

//recuperaos informacion
$object->fetch($id);
//PRCESO 1
$objPHPExcel = new PHPExcel();
// $objReader = PHPExcel_IOFactory::createReader('Excel2007');
// $objPHPExcel = $objReader->load("excel/iniproceso_mod.xlsx");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('A1','Nro.');
$objPHPExcel->getActiveSheet()->SetCellValue('B1','Dia');
$objPHPExcel->getActiveSheet()->SetCellValue('C1','Mes');
$objPHPExcel->getActiveSheet()->SetCellValue('D1','Año');
$objPHPExcel->getActiveSheet()->SetCellValue('E1','Gerencia');
$objPHPExcel->getActiveSheet()->SetCellValue('F1','Subgerencia');
$objPHPExcel->getActiveSheet()->SetCellValue('G1','Departamento');
$objPHPExcel->getActiveSheet()->SetCellValue('H1','Precio');
$objPHPExcel->getActiveSheet()->SetCellValue('I1','Tipo_adjudicacion');
$objPHPExcel->getActiveSheet()->SetCellValue('J1','PAC');
$objPHPExcel->getActiveSheet()->SetCellValue('K1','Doc Respaldo precio referencial');
$objPHPExcel->getActiveSheet()->SetCellValue('L1','Doc certificacion presupuestaria');
$objPHPExcel->getActiveSheet()->SetCellValue('M1','Doc especificacion tecnica');
$objPHPExcel->getActiveSheet()->SetCellValue('N1','Doc modelo contrato');
$objPHPExcel->getActiveSheet()->SetCellValue('O1','Doc informe tecnico legal');
$objPHPExcel->getActiveSheet()->SetCellValue('P1','metodos sel ANPE');
$objPHPExcel->getActiveSheet()->SetCellValue('Q1','Metodos sel LPNI');
$objPHPExcel->getActiveSheet()->SetCellValue('R1','Metodos sel CAE');
$objPHPExcel->getActiveSheet()->SetCellValue('S1','Titulo');
$objPHPExcel->getActiveSheet()->SetCellValue('T1','Condicion Adicional ANPE');
$objPHPExcel->getActiveSheet()->SetCellValue('U1','Condicion Adicional LPNI');

//valores
$lLoop = true;
$dpto = '';
$subgerencia = '';
$gerencia = '';
$fk_area = $object->fk_area;
while ($lLoop == true)
  {
    $objarea->fetch($fk_area);
    if ($objarea->pos == 2)
      $dpto = $objarea->label;
    if ($objarea->pos == 1)
      $subgerencia = $objarea->label;
    if ($objarea->pos == 0)
      $gerencia = $objarea->label;
    if ($objarea->pos == 0)
      $lLoop = false;
    $fk_area = $objarea->fk_father;
  }
$objPHPExcel->getActiveSheet()->SetCellValue('A2',$object->ref);
$objPHPExcel->getActiveSheet()->SetCellValue('B2',date('d',$object->date_process));
$objPHPExcel->getActiveSheet()->SetCellValue('C2',date('m',$object->date_process));
$objPHPExcel->getActiveSheet()->SetCellValue('D2',date('Y',$object->date_process));
$objPHPExcel->getActiveSheet()->SetCellValue('E2',$gerencia);
$objPHPExcel->getActiveSheet()->SetCellValue('F2',$subgerencia);
$objPHPExcel->getActiveSheet()->SetCellValue('G2',$dpto);
$objPHPExcel->getActiveSheet()->SetCellValue('H2',$object->amount);
$objPHPExcel->getActiveSheet()->SetCellValue('I2',$object->fk_type_adj);
$objPHPExcel->getActiveSheet()->SetCellValue('J2',$object->ref_pac);
$objPHPExcel->getActiveSheet()->SetCellValue('K2',$object->doc_precio_referencial);
$objPHPExcel->getActiveSheet()->SetCellValue('L2',$object->doc_certif_presupuestaria);
$objPHPExcel->getActiveSheet()->SetCellValue('M2',$object->doc_especific_tecnica);
$objPHPExcel->getActiveSheet()->SetCellValue('N2',$object->doc_modelo_contrato);
$objPHPExcel->getActiveSheet()->SetCellValue('O2',$object->doc_informe_lega);
$objPHPExcel->getActiveSheet()->SetCellValue('P2',$object->metodo_sel_anpe);
$objPHPExcel->getActiveSheet()->SetCellValue('Q2',$object->metodo_sel_lpni);
$objPHPExcel->getActiveSheet()->SetCellValue('R2',$object->metodo_sel_cae);
$objPHPExcel->getActiveSheet()->SetCellValue('S2',$object->label);
$objPHPExcel->getActiveSheet()->SetCellValue('T2',$object->condicion_adicional_anpe);
$objPHPExcel->getActiveSheet()->SetCellValue('U2',$object->condicion_adicional_lpni);


//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("excel/exportbcb.xlsx");

header('Location: '.DOL_URL_ROOT.'/poa/process/fiche_export.php');
// llxFooter();

// $db->close();
?>
