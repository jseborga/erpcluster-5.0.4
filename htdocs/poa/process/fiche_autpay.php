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
 *	\file       htdocs/poa/process/fiche_autpay.php
 *	\ingroup    Process export excel authorization payment
 *	\brief      Page fiche poa process export excel
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprevprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once(DOL_DOCUMENT_ROOT."/poa/class/numbertoletterconverter.class.php");

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once(DOL_DOCUMENT_ROOT."/poa/class/numeroaletras.class.php");

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id"); //proceso
$idr       = GETPOST("idr"); //registro de pago
$idc       = GETPOST("idc"); //contrato

$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');

if (!empty($id))
{
	$ida = $_SESSION['aListip'][$id]['idAct'];
	$idp = $_SESSION['aListip'][$id]['idPrev'];
	$idc = $_SESSION['aListip'][$id]['idContrat'];
	$lAnticipo = $_SESSION['aListip'][$id]['anticipo'];
}

if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

$object  = new Poaprocess($db);
//$objectc = new Poaprocesscontrat($db);
$objarea = new Poaarea($db);
$objuser = new User($db);
$objprev = new Poaprev($db);
$objpac  = new Poapac($db);
$objdev  = new Poapartidadev($db); //pago
$objcont = new Contrat($db);
$objsoc  = new Societe($db);

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
//$objprev->fetch($object->fk_poa_prev);
$objdev->fetch($idr);

$objprev->fetch($objdev->fk_poa_prev);
$aDate = dol_getdate($objdev->date_dev);
//echo '<hr>dev '.$idr.' process '.$id.' prev '.$objdev->fk_poa_prev;

//PRCESO 1
$objPHPExcel = new PHPExcel();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("excel/form-2840-002.xlsx");

//imagen
$objDraw = new PHPExcel_Worksheet_Drawing();
$objDraw->setPath('../img/bcb.png');
$objDraw->setHeight(50);
$objDraw->setCoordinates('E2');
$objDraw->setOffsetX(10);
$objDraw->setWorksheet($objPHPExcel->getActiveSheet());

$objPHPExcel->setActiveSheetIndex(0);

//escribiendo en excel
$objPHPExcel->getActiveSheet()->SetCellValue('X4',$aDate['mday']);
$objPHPExcel->getActiveSheet()->SetCellValue('Y4',$aDate['mon']);
$objPHPExcel->getActiveSheet()->SetCellValue('Z4',$aDate['year']);
$objPHPExcel->getActiveSheet()->SetCellValue('Y13',$objdev->nro_dev.'/'.$objdev->gestion);
//area
$objarea->fetch($objprev->fk_area);
$objPHPExcel->getActiveSheet()->SetCellValue('J16',$objarea->label);
//contrato
$objcont = new Contrat($db);
//$objcont->fetch($idc);
$res = $objcont->fetch($objdev->fk_contrato);
if ($res < 0)
{
	dol_print_error($db,$objcont->error);
	exit;
}
$res=$objcont->fetch_optionals($objcont->id,$extralabels);
$celdacont = '';
$codcont = '';
//if ($objcont->id == $idc)
if ($objcont->id == $objdev->fk_contrato)
{
	$codcont = $objcont->array_options['options_ref_contrato'] ;
	if ($objcont->array_options['options_type'] == 1)
	{
		$celdacont = 'M38'; //contrato
		//$codcont = substr($codcont,5,100);
	}
	if ($objcont->array_options['options_type'] == 2)
	{
		$celdacont = 'M39';//orden de compra
		$codcont = substr($codcont,3,100);
	}
	if ($objcont->array_options['options_type'] == 3)
	{
		$codcont = substr($codcont,4,100);
		$celdacont = 'M40'; //orden de contratacion servicio
	}
	if ($objsoc->fetch($objcont->fk_soc))
	  $objPHPExcel->getActiveSheet()->SetCellValue('J20',$objsoc->nom);
	else
	$objPHPExcel->getActiveSheet()->SetCellValue('J20','No defined');
}
//numeral monto
$cn = new NumerosALetras();
$leter = $cn->traducir($objdev->amount, 'Bolivianos');

$objPHPExcel->getActiveSheet()->SetCellValue('J23',price2num($objdev->amount));
$objPHPExcel->getActiveSheet()->SetCellValue('J25',$leter);
//$objPHPExcel->getActiveSheet()->SetCellValue('J25',num2texto(price2num($objdev->amount)));
$objPHPExcel->getActiveSheet()->SetCellValue('B28',$objprev->label);
$objPHPExcel->getActiveSheet()->SetCellValue('M34',$objprev->nro_preventive);
$objPHPExcel->getActiveSheet()->SetCellValue('M35',$objdev->invoice);
$objPHPExcel->getActiveSheet()->SetCellValue($celdacont,$codcont);

//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("excel/exp_autpay.xlsx");

header('Location: '.DOL_URL_ROOT.'/poa/process/fiche_export_autpay.php');
// llxFooter();

// $db->close();
?>
