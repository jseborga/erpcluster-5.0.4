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
 *	\file       htdocs/poa/process/fiche_inipro.php
 *	\ingroup    Process export excel process init
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

$aDate = dol_getdate($object->date_process);

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


//PRCESO 1
$objPHPExcel = new PHPExcel();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("excel/form-2340-005.xlsx");
$celtypeadj = 'P20';

//logo cabecera
$objDraw = new PHPExcel_Worksheet_Drawing();
$objDraw->setPath('../img/bcb.png');
$objDraw->setHeight(70);
$objDraw->setCoordinates('C3');
$objDraw->setOffsetX(3);
$objDraw->setOffsetY(3);
$objDraw->setWorksheet($objPHPExcel->getActiveSheet());

//tipo de adjudicacion
for ($x=1;$x<=3;$x++)
  {
    $mark = false;
    if ($x==1) $celtypeadj='P20';
    if ($x==2) $celtypeadj='R20';
    if ($x==3) $celtypeadj='T20';
    if ($object->fk_type_adj == 1 && $x==1) $mark=true;
    if ($object->fk_type_adj == 2 && $x==2) $mark=true;
    if ($object->fk_type_adj == 3 && $x==3) $mark=true;
    //imagen
    $objDraw = new PHPExcel_Worksheet_Drawing();
    if ($mark)
      $objDraw->setPath('../img/cuadrox.png');
    else
      $objDraw->setPath('../img/cuadro.png');

    $objDraw->setHeight(18);
    $objDraw->setCoordinates($celtypeadj);
    $objDraw->setOffsetX(15);
    $objDraw->setOffsetY(10);
    $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
  }
//type contratacion
for($x=1; $x<=5; $x++)
{
  $celdocpr='';
  $mark = false;
  if ($x == 1) $celdocpr='L30';
  if ($x == 2) $celdocpr='N30';
  if ($x == 3) $celdocpr='P30';
  if ($x == 4) $celdocpr='R30';
  if ($x == 5) $celdocpr='T30';

  if ($object->doc_precio_referencial == $x) $mark=true;;
  if ($object->doc_precio_referencial == $x) $mark=true;;
  if ($object->doc_precio_referencial == $x) $mark=true;;
  if ($object->doc_precio_referencial == $x) $mark=true;;
  if ($object->doc_precio_referencial == $x) $mark=true;;

  $objDraw = new PHPExcel_Worksheet_Drawing();
  if ($mark)
    $objDraw->setPath('../img/cuadrox.png');
  else
    $objDraw->setPath('../img/cuadro.png');
  $objDraw->setHeight(18);
  $objDraw->setCoordinates($celdocpr);
  $objDraw->setOffsetX(60);
  $objDraw->setOffsetY(1);
  $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
}

//type certificacion presup
for($x=1; $x<=5; $x++)
{
  $celdocpr='';
  $mark = false;
  if ($x == 1) $celdocpr='L31';
  if ($x == 2) $celdocpr='N31';
  if ($x == 3) $celdocpr='P31';
  if ($x == 4) $celdocpr='R31';
  if ($x == 5) $celdocpr='T31';

  if ($object->doc_certif_presupuestaria == $x) $mark=true;;
  if ($object->doc_certif_presupuestaria == $x) $mark=true;;
  if ($object->doc_certif_presupuestaria == $x) $mark=true;;
  if ($object->doc_certif_presupuestaria == $x) $mark=true;;
  if ($object->doc_certif_presupuestaria == $x) $mark=true;;

  $objDraw = new PHPExcel_Worksheet_Drawing();
  if ($mark)
    $objDraw->setPath('../img/cuadrox.png');
  else
    $objDraw->setPath('../img/cuadro.png');
  $objDraw->setHeight(18);
  $objDraw->setCoordinates($celdocpr);
  $objDraw->setOffsetX(60);
  $objDraw->setOffsetY(1);
  $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
}

//type specif tecnica
for($x=1; $x<=5; $x++)
{
  $celdocpr='';
  $mark = false;
  if ($x == 1) $celdocpr='L32';
  if ($x == 2) $celdocpr='N32';
  if ($x == 3) $celdocpr='P32';
  if ($x == 4) $celdocpr='R32';
  if ($x == 5) $celdocpr='T32';

  if ($object->doc_especific_tecnica == $x) $mark=true;;
  if ($object->doc_especific_tecnica == $x) $mark=true;;
  if ($object->doc_especific_tecnica == $x) $mark=true;;
  if ($object->doc_especific_tecnica == $x) $mark=true;;
  if ($object->doc_especific_tecnica == $x) $mark=true;;

  $objDraw = new PHPExcel_Worksheet_Drawing();
  if ($mark)
    $objDraw->setPath('../img/cuadrox.png');
  else
    $objDraw->setPath('../img/cuadro.png');
  $objDraw->setHeight(18);
  $objDraw->setCoordinates($celdocpr);
  $objDraw->setOffsetX(60);
  $objDraw->setOffsetY(5);
  $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
}

//type modelo contrato
for($x=1; $x<=5; $x++)
{
  $celdocpr='';
  $mark = false;
  if ($x == 1) $celdocpr='L33';
  if ($x == 2) $celdocpr='N33';
  if ($x == 3) $celdocpr='P33';
  if ($x == 4) $celdocpr='R33';
  if ($x == 5) $celdocpr='T33';

  if ($object->doc_modelo_contrato == $x) $mark=true;;
  if ($object->doc_modelo_contrato == $x) $mark=true;;
  if ($object->doc_modelo_contrato == $x) $mark=true;;
  if ($object->doc_modelo_contrato == $x) $mark=true;;
  if ($object->doc_modelo_contrato == $x) $mark=true;;

  $objDraw = new PHPExcel_Worksheet_Drawing();
  if ($mark)
    $objDraw->setPath('../img/cuadrox.png');
  else
    $objDraw->setPath('../img/cuadro.png');
  $objDraw->setHeight(18);
  $objDraw->setCoordinates($celdocpr);
  $objDraw->setOffsetX(60);
  if ($x == 3 || $x == 5)
    $objDraw->setOffsetY(35);
  else
    $objDraw->setOffsetY(1);
  $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
}

//type informe_lega
for($x=1; $x<=5; $x++)
{
  $celdocpr='';
  $mark = false;
  if ($x == 1) $celdocpr='L35';
  if ($x == 2) $celdocpr='N35';
  if ($x == 3) $celdocpr='P35';
  if ($x == 4) $celdocpr='R35';
  if ($x == 5) $celdocpr='T35';

  if ($object->doc_informe_lega == $x) $mark=true;;
  if ($object->doc_informe_lega == $x) $mark=true;;
  if ($object->doc_informe_lega == $x) $mark=true;;
  if ($object->doc_informe_lega == $x) $mark=true;;
  if ($object->doc_informe_lega == $x) $mark=true;;

  $objDraw = new PHPExcel_Worksheet_Drawing();
  if ($mark)
    $objDraw->setPath('../img/cuadrox.png');
  else
    $objDraw->setPath('../img/cuadro.png');
  $objDraw->setHeight(18);
  $objDraw->setCoordinates($celdocpr);
  $objDraw->setOffsetX(60);
  $objDraw->setOffsetY(1);
  $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
}

//metodo sel cae
$celdocpr='';
$mark = false;
$celdocpr='T36';

if ($object->doc_metodo_sel_cae) $mark=true;;

$objDraw = new PHPExcel_Worksheet_Drawing();
if ($mark)
  $objDraw->setPath('../img/cuadrox.png');
 else
   $objDraw->setPath('../img/cuadro.png');
$objDraw->setHeight(18);
$objDraw->setCoordinates($celdocpr);
$objDraw->setOffsetX(60);
$objDraw->setOffsetY(1);
$objDraw->setWorksheet($objPHPExcel->getActiveSheet());


//metodo sel anpe
for($x=39; $x<=44; $x++)
{
  for($y=1; $y<=2; $y++)
    {
      if ($y == 1)$celdocpr='N'.$x;
      if ($y == 2)$celdocpr='P'.$x;

      $mark = false;
      if ($y == 1)
	{
	  if ($object->metodo_sel_anpe == 1 && $x==39) $mark=true;;
	  if ($object->metodo_sel_anpe == 2 && $x==40) $mark=true;;
	  if ($object->metodo_sel_anpe == 3 && $x==41) $mark=true;;
	  if ($object->metodo_sel_anpe == 4 && $x==42) $mark=true;;
	  if ($object->metodo_sel_anpe == 5 && $x==43) $mark=true;;
	}
      if ($y == 2)
	{
	  if ($object->metodo_sel_lpni == 1 && $x==39) $mark=true;;
	  if ($object->metodo_sel_lpni == 2 && $x==40) $mark=true;;
	  if ($object->metodo_sel_lpni == 3 && $x==41) $mark=true;;
	  if ($object->metodo_sel_lpni == 4 && $x==42) $mark=true;;
	  if ($object->metodo_sel_lpni == 5 && $x==43) $mark=true;;
	}
      if ($x == 44)
	{
	  $mark = false;
	  if ($y == 1)
	    if ($object->condicion_adicional_anpe == 1) $mark=true;;
	  if ($y == 2)
	    if ($object->condicion_adicional_lpni == 1) $mark=true;;
	}
      $objDraw = new PHPExcel_Worksheet_Drawing();
      if ($mark)
	$objDraw->setPath('../img/cuadrox.png');
      else
	$objDraw->setPath('../img/cuadro.png');
      $objDraw->setHeight(18);
      $objDraw->setCoordinates($celdocpr);
      $objDraw->setOffsetX(60);
      $objDraw->setOffsetY(1);
      $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
    }
 }

//rellenado de metodo de adjudicacion
//metodo sel anpe
$array = array(39=>array(1,1,1,1,1,1,0),
	       40=>array(1,0,1,1,1,1,0),
	       41=>array(0,0,1,1,0,1,0),
	       42=>array(0,0,0,1,0,1,0),
	       43=>array(1,1,0,0,1,0,1));
for($x=39; $x<=43; $x++)
{
  for($y=1; $y<=7; $y++)
    {
      $aarray = $array[$x];
      
      if ($y == 1)$cely='E'.$x;
      if ($y == 2)$cely='F'.$x;
      if ($y == 3)$cely='G'.$x;
      if ($y == 4)$cely='H'.$x;
      if ($y == 5)$cely='I'.$x;
      if ($y == 6)$cely='J'.$x;
      if ($y == 7)$cely='K'.$x;

      $image = 'x.png';
      $pos = $y-1;
      if ($aarray[$pos])
	$image = 'ok.png';
      $objDraw = new PHPExcel_Worksheet_Drawing();
	$objDraw->setPath('../img/'.$image);
      $objDraw->setHeight(15);
      $objDraw->setCoordinates($cely);
      $objDraw->setOffsetX(15);
      $objDraw->setOffsetY(2);
      $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
    }
  //okis inicial
  $okcel='C'.$x;
  $objDraw = new PHPExcel_Worksheet_Drawing();
  $objDraw->setPath('../img/ok.png');
  $objDraw->setHeight(10);
  $objDraw->setCoordinates($okcel);
  $objDraw->setOffsetX(0);
  $objDraw->setOffsetY(3);
  $objDraw->setWorksheet($objPHPExcel->getActiveSheet());

 }

//manitos
for($y=1; $y<=6; $y++)
  {
    if ($y == 1)$cely='C30';
    if ($y == 2)$cely='C31';
    if ($y == 3)$cely='C32';
    if ($y == 4)$cely='C33';
    if ($y == 5)$cely='C35';
    if ($y == 6)$cely='C36';
    
    $image = 'senal.png';
    $objDraw = new PHPExcel_Worksheet_Drawing();
    $objDraw->setPath('../img/'.$image);
    $objDraw->setHeight(12);
    $objDraw->setCoordinates($cely);
    $objDraw->setOffsetX(0);
    if ($y==4)
      $objDraw->setOffsetY(50);
    elseif ($y==6)
      $objDraw->setOffsetY(15);
    else
      $objDraw->setOffsetY(3);
    $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
  }


$objPHPExcel->setActiveSheetIndex(0);

//escribiendo en excel
$objPHPExcel->getActiveSheet()->SetCellValue('S5',$object->ref.'/'.$object->gestion);
$objPHPExcel->getActiveSheet()->SetCellValue('E20',price($object->amount));
$objPHPExcel->getActiveSheet()->getStyle('E20:K20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->SetCellValue('G11',$gerencia);
$objPHPExcel->getActiveSheet()->SetCellValue('G12',$subgerencia);
$objPHPExcel->getActiveSheet()->SetCellValue('G13',$dpto);

$objPHPExcel->getActiveSheet()->SetCellValue('S4',$aDate['mday']);
$objPHPExcel->getActiveSheet()->SetCellValue('T4',$aDate['mon']);
$objPHPExcel->getActiveSheet()->SetCellValue('U4',$aDate['year']);
//titulo
$objPHPExcel->getActiveSheet()->SetCellValue('C18',$object->label);
$objPHPExcel->getActiveSheet()->SetCellValue('M18',$object->ref_pac);

//justification
$objPHPExcel->getActiveSheet()->SetCellValue('C24',$object->justification);

$objPHPExcel->getActiveSheet()->getStyle('L30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('L30')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("excel/exp_inipro.xlsx");
header('Location: '.DOL_URL_ROOT.'/poa/process/fiche_export_inipro.php');
// llxFooter();

// $db->close();
?>
