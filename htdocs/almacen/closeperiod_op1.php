<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *      \file       htdocs/salary/index.php
 *      \ingroup    Salary
 *      \brief      Page index de salary
 */

require("../main.inc.php");
dol_include_once('/almacen/class/contabperiodo.class.php');
dol_include_once('/almacen/class/solalmacenext.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/product/stock/class/entrepot.class.php');
dol_include_once('/almacen/class/stockmouvementtempext.class.php');
dol_include_once('/almacen/class/stockmouvementdocext.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/mouvementstockext.class.php");
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementpricemodext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementaddext.class.php");

//excel para una versión anterior
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
$ver = 0;
if (file_exists($file))
{
	$ver = 1;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

if ($ver == 0)
{
//excel para version 4 o sup
	$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
	if (file_exists($file))
	{
		$ver = 2;
		require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
	}
	$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
	if (file_exists($file))
		include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
}

$langs->load("stocks");
$langs->load("almacen@almacen");

$cancel = GETPOST('cancel');
$year = GETPOST('year');
$action = GETPOST('action');

if (isset($_POST['year'])) $_SESSION['period_year'] = $_POST['year'];

if (!$user->rights->almacen->lirealm) accessforbidden();

$period_year = $_SESSION['period_year'];
$period_month = $_SESSION['period_month'];
$object = new Contabperiodo($db);
$objSolalm = new Solalmacenext($db);
$product = new Product($db);
$entrepot = new Entrepot($db);
$objStocktmp = new Stockmouvementtempext($db);
$objStockdoc = new Stockmouvementdocext($db);
$movement = new Mouvementstockext($db);
$objCsindexescountry = new Csindexescountryext($db);
$objStockpricemod = new Stockmouvementpricemodext($db);
$objStockadd = new Stockmouvementaddext($db);
$objStock = new Mouvementstockext($db);

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
$typufv = $conf->global->ALMACEN_CHANGE_UFV;

if ($action == 'closeperiod')
{
	$action = 'edit';
	$datefinsel  = dol_mktime(0, 0, 0, GETPOST('dc_month'),  GETPOST('dc_day'),  GETPOST('dc_year'));
	$datefinselect  = dol_mktime(23, 59, 59, GETPOST('dc_month'),  GETPOST('dc_day'),  GETPOST('dc_year'));
}
$now = dol_now();

if ($action == 'confirm_closeperiod' && $user->rights->almacen->per->actal)
{
	//vamos a recorrer cada parte de la session
	//PARA PEPS
	//actualizamos el valor para peps en la tabla stock_mouvement_add
	//creamos un registro adicional en la tabla stock_mouvement_pricemod
	//PARA PPP
	//actualizamos el valor pmp en la tabla product
	//creamos un registro adicional en la tabla product_pricemod
	$aCloseperiod = unserialize($_SESSION['aCloseperiod']);
	$periodyear = $aCloseperiod['periodyear'];
	$periodmonth = $aCloseperiod['periodmonth'];
	$periodmonthclose = 0;
	if ($conf->global->ALMACEN_CLOSE_PERIOD == 9)
		$periodmonthclose = 1;
	$lines = $aCloseperiod['lines'];
	//primero para peps
	$db->begin();
	foreach ($lines AS $j => $row)
	{
		if (!$error)
		{
			echo '<hr>rowid '.$rowid = $row['rowidstock'];
			echo ' qty= '.$row['saldoo'];
			if ($rowid>0 && $row['saldoo']>0)
			{
				$newprice = $row['valorAct'] / $row['saldoo'];
				echo '<br>ressearch '.$res = $objStockadd->fetch(0,$rowid);
				if ($res>=0)
				{
					$qty = $objStockadd->qty;
					$balance_peps = $objStockadd->balance_peps+0;
					$balance_ueps = $objStockadd->balance_ueps+0;
					$value_peps = $objStockadd->value_peps+0;
					$value_ueps = $objStockadd->value_ueps+0;
					if ($res == 0)
					{
						$objStock->fetch($rowid);
						$qty = $objStock->qty;
						$balance_peps = 0;
						$balance_ueps = 0;
						$value_peps = 0;
						$value_ueps = 0;
					}
					//guardamos en la tabla de control
					$objStockpricemod->fk_stock_mouvement = $rowid;
					$objStockpricemod->period_year = $periodyear;
					$objStockpricemod->month_year = $periodmonth;
					$objStockpricemod->fk_user_create = $user->id;
					$objStockpricemod->fk_user_mod = $user->id;
					$objStockpricemod->fk_parent_line = $objStockadd->fk_parent_line+0;
					$objStockpricemod->qty = $qty+0;
					$objStockpricemod->balance_peps = $balance_peps;
					$objStockpricemod->balance_ueps = $balance_ueps;
					$objStockpricemod->value_peps = $value_peps;
					$objStockpricemod->value_ueps = $value_ueps;
					$objStockpricemod->balance_peps_new = $balance_peps;
					$objStockpricemod->balance_ueps_new = $balance_ueps;
					$objStockpricemod->value_peps_new = $newprice;
					$objStockpricemod->value_ueps_new = $newprice;
					$objStockpricemod->datec = $now;
					$objStockpricemod->datem = $now;
					$objStockpricemod->tms = $now;
					$objStockpricemod->status = 1;
					echo '<hr>resn '.$resn = $objStockpricemod->create($user);
					if ($resn <=0)
					{
						$error=101;
						echo $error;exit;
						setEventMessages($objStockpricemod->error,$objStockpricemod->errors,'errors');
					}
					if (!$error)
					{
						if ($res>0)
						{
							$objStockadd->value_peps = $newprice;
							$objStockadd->value_ueps = $newprice;
							$objStockadd->fk_user_mod = $user->id;
							$objStockadd->date_mod = $now;
							echo '<hr>resm '.$resm = $objStockadd->update_close_period($user);
							if ($resm <=0)
							{
								$error=102;
								echo $error;exit;
								setEventMessages($objStockadd->error,$objStockad->errors,'errors');
							}
						}
					}
				}
			}
		}
		else
		{
			echo '<hr>errr '.$error.' regcreate '.$resn;
			exit;
		}
	}
	echo '<hr>errr '.$error.' regcreate '.$resn;
	//exit;
	//si no existe error
	//se cierrea contab_period
	if (empty($periodmonthclose))
	{
		//mensual
		$res = $object->fetch(0,$periodyear,$periodmonth);
		if ($res==1)
		{
			$object->status_al = 0;
			$res = $object->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		else
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
		//creamos
			if ($periodmonth==12)
			{
				$newperiodyear = $periodyear+=1;
				$newperiodmonth = 1;
			}
			else
			{
				$newperiodyear = $periodyear;
				$newperiodmonth +=1;
			}
		//buscamos
			$res = $object->fetch(0,$newperiodyear,$newperiodmonth);
			if ($res==0)
			{
				$object->entity = $conf->entity;
				$object->period_month = $newperiodmonth;
				$object->period_year = $newperiodyear;
				$object->date_ini = dol_get_first_day($newperiodyear,$newperiodmonth);
				$object->date_fin = dol_get_last_day($newperiodyear,$newperiodmonth);
				$object->statut = 1;
				$object->status_af = 1;
				$object->status_al = 1;
				$res = $object->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			else
			{
				$object->status_al = 1;
				$object->statut = 1;
				$res = $object->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}

			}
		}
	}
	else
	{
		//anual
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND'," AND t.period_year = ".$period_year." AND t.status_al = 1");
		if ($res>0)
		{
			$lines = $object->lines;
			foreach ($lines AS $j => $line)
			{
				$res = $object->fetch($line->id);
				if ($res > 0)
				{
					$object->status_al = 0;
					$res = $object->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
				}
				elseif($res <0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		elseif ($res <0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			//creamos
			$newperiodyear = $periodyear+=1;
			$newperiodmonth = 1;
			//buscamos
			$res = $object->fetch(0,$newperiodyear,$newperiodmonth);
			if ($res==0)
			{
				$object->entity = $conf->entity;
				$object->period_month = $newperiodmonth;
				$object->period_year = $newperiodyear;
				$object->date_ini = dol_get_first_day($newperiodyear,$newperiodmonth);
				$object->date_fin = dol_get_last_day($newperiodyear,$newperiodmonth);
				$object->statut = 1;
				$object->status_af = 1;
				$object->status_al = 1;
				$res = $object->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			else
			{
				$object->status_al = 1;
				$object->statut = 1;
				$res = $object->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
	}
	echo '<hr>fin '. $error;exit;
	//se crear el nuevo periodo a iniciar si no existe
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Proceso satisfactorio'),null,'mesgs');
		header('Location: '.DOL_URL_ROOT.'/almacen/index.php');
		exit;
	}
	else
		$db->rollback();

	$action = 'create';
}

if ($action == 'excel')
{
	$aCloseperiod = unserialize($_SESSION['aCloseperiod']);
	$periodyear = $aCloseperiod['periodyear'];
	$periodmonth = $aCloseperiod['periodmonth'];
	$lines = $aCloseperiod['lines'];

	$inventory = $aCloseperiod['lines'];
	$inventoryGroup = $aCloseperiod['inventoryGroup'];

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Ramiro Queso")
	->setLastModifiedBy("Ramiro Queso")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Cierre de periodos almacenes");

		//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Candara');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('A2',html_entity_decode($langs->trans("Closeperiod")));
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getStyle('A2')->getFont()->setSize(15);

	$sheet->mergeCells('A2:I2');
	if($yesnoprice)
		$sheet->mergeCells('A2:I2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);

	$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Date"));
	$objPHPExcel->getActiveSheet()->setCellValue('A5',html_entity_decode($langs->trans("Year")));
	if ($conf->global->ALMACEN_CLOSE_PERIOD==1)
		$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Month"));
	$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans('Fecha cierre'));

	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	if ($conf->global->ALMACEN_CLOSE_PERIOD==1)
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);


	$objPHPExcel->getActiveSheet()->setCellValue('B4',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('B5', $aCloseperiod['periodyear']);
	if ($conf->global->ALMACEN_CLOSE_PERIOD==1)
		$objPHPExcel->getActiveSheet()->setCellValue('B6', $aCloseperiod['periodmonth']);
	$objPHPExcel->getActiveSheet()->setCellValue('B7', dol_print_date($aCloseperiod['datefinsel'],"day",false,$outputlangs));

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

	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);

	$objPHPExcel->getActiveSheet()->getStyle('A2:I8')->applyFromArray($styleThickBrownBorderOutline);

	// TABLA
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->setCellValue('A10',$langs->trans("Grupo"));
	$objPHPExcel->getActiveSheet()->setCellValue('B10',$langs->trans("code"));
	$objPHPExcel->getActiveSheet()->setCellValue('C10',$langs->trans("Detalle"));
	$objPHPExcel->getActiveSheet()->setCellValue('D10',$langs->trans("UM"));
	$objPHPExcel->getActiveSheet()->setCellValue('E10',$langs->trans("PU"));
	$objPHPExcel->getActiveSheet()->setCellValue('F10',$langs->trans("saldo"));
	$objPHPExcel->getActiveSheet()->setCellValue('G10',$langs->trans("valor Total"));
	$objPHPExcel->getActiveSheet()->setCellValue('H10',$langs->trans("Actualizado UFV"));
	$objPHPExcel->getActiveSheet()->setCellValue('I10',$langs->trans("Diferencia UfV"));

	$objPHPExcel->getActiveSheet()->getStyle('A10:I10')->applyFromArray(
		array('font'    => array('bold'      => true),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),'borders' => array('allborders'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)),'fill' => array(
			'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
			'rotation'   => 90,
			'startcolor' => array(
				'argb' => 'FFA0A0A0'
				),
			'endcolor'   => array(
				'argb' => 'FFFFFFFF'
				)
			)
		)
		);
	$objPHPExcel->getActiveSheet()->getStyle('A10:I15')->applyFromArray(
		array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FFA0A0A0')
					)
				)
			)

		);

	$j=11;
	$sumsaav=0;
	$sumainpv=0;
	$sumaoutv=0;
	$sumabalv=0;
	$contt=1;
	/*
	echo '<pre>';
	print_r($inventoryGroup);
	print_r($inventory);
	echo '<hr>cuenta '.count($inventory);
	echo '</pre>';
	*/
	foreach ((array) $inventory AS $i => $lines)
	{
		$cGroup = $lines['Grupo'];
		$Grupo=$lines['Grupo'];
		$codigo = $lines['ref'];
		$desc = $lines['label'];
		$unit=$lines['unit'];
		$pricee=$lines['pricee'];
		$saldoo=$lines['saldoo'];
		$amountt=$lines['amountt'];
		$valorAct=$lines['valorAct'];
		$valorDif=$lines['valorDif'];

		if ($lines['type']=='g') $Grupo = '';
		if ($inventoryGroup[$cGroup])
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$Grupo)
			->setCellValue('B' .$j,$codigo)
			->setCellValue('C' .$j,$desc)
			->setCellValue('D' .$j,$unit)
			->setCellValue('E' .$j,$pricee)
			->setCellValue('F' .$j,$saldoo)
			->setCellValue('G' .$j,$amountt)

			->setCellValue('H' .$j,price2num($valorAct,'MT'))
			->setCellValue('I' .$j,price2num($valorDif,'MT'));

			$j++;
			$contt++;
		}
	}
	//echo 'reg '.$j;exit;
	$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/closeperiod.xlsx");
					//$objWriter->save("excel/export.xlsx");

	header("Location: ".DOL_URL_ROOT.'/almacen/excel/fiche_export.php?archive=closeperiod.xlsx');
}

//view
//



//search last exchange rate
// $objectcop = new Csindexes($db);
// $objectcop->fetch_last($country);

// if ($objectcop->date_ind <> $db->jdate(date('Y-m-d')))
//   {
//     header("Location: ".DOL_URL_ROOT.'/wages/exchangerate/fiche.php?action=create');
//     exit;
//   }
llxHeader("",$langs->trans("Almacenes"),$help_url);


print load_fiche_titre($langs->trans("Closeperiod"),'','title_commercial.png');

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="closeperiod">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Type"),"", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Numberofoccurrences"),"", "","","","",$sortfield,$sortorder);
print '</tr>';
$var = true;
//buscamos los movimientos de entrada
$filter = " AND t.statut = 1";
$filter.= " AND m.type = 'E'";
$res = $objStockdoc->fetchAll_type('','',0,0,array(1=>1),'AND',$filter);

$var = !$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('Movimientos de entrada').'</td>';
print '<td>'.$res.'</td>';
print '</tr>';
$var = !$var;
$filter = " AND t.statut = 1";
$filter.= " AND m.type = 'S'";
$res = $objStockdoc->fetchAll_type('','',0,0,array(1=>1),'AND',$filter);
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('Movimientos de salida').'</td>';
print '<td>'.$res.'</td>';
print '</tr>';
$var = !$var;
$filter = " AND t.statut = 1";
$filter.= " AND m.type = 'T'";
$res = $objStockdoc->fetchAll_type('','',0,0,array(1=>1),'AND',$filter);
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('Transferencias').'</td>';
print '<td>'.$res.'</td>';
print '</tr>';
$var = !$var;
$filter = " AND t.statut = 6";
$res = $objSolalm->fetchAll('','',0,0,array(1=>1),'AND',$filter);
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('Pedidos aprobados').'</td>';
print '<td>'.$res.'</td>';
print '</tr>';
print '<tr '.$bc[$var].'>';
print '<td colspan="2">'.$langs->trans('Los movimientos detallados, seran asignados al siguiente periodo cuando se apruebe o procese').'</td>';
print '</tr>';
$var = !$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('Dateclose').'</td>';
if ($conf->global->ALMACEN_CLOSE_PERIOD == 1)
	$dateclose = dol_get_last_day($period_year,$_SESSION['period_month']);
else
	$dateclose = dol_get_last_day($period_year,12);
print '<td>';
print $form->select_date($dateclose,'dc_',0,0,0,'',1,0,0,1);
print '</td>';
print '</tr>';


print '</table>';
print '<center><input type="submit" class="butAction" value="'.$langs->trans('Process').'"></center>';
print '</form>';



if ($action== 'edit' || $action == 'conf')
{
	$_SESSION['idEntrepot'] = $id;

	if ($action == 'conf')
	{
		$aCloseperiod = unserialize($_SESSION['aCloseperiod']);
		$dateinisel = $aCloseperiod['dateinisel'];
		$datefinsel = $aCloseperiod['datefinsel'];
		$datefinselect = $aCloseperiod['datefinselect'];

		$formquestion = '';
		//$formquestion = array(array('type'=>'checkbox','label'=>$langs->trans('Notas Ingreso'),'name'=>'notaing','value'=>1,),);

		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.GETPOST('idr'),
			$langs->trans("Closeperiod"),
			$langs->trans("ConfirmCloseperiod"),
			"confirm_closeperiod",
			$formquestion,
			1,
			2);
		if ($ret == 'html') print '<br>';

	}

	$aRowid = array();
	$object = new Entrepotrelationext($db);
	$object->id = $id;

	$dateinisel = dol_get_first_day($period_year,1,false);
	$result = $object->fetch_entrepot();
	if ($result == 1)
	{
		$aEntrepot = $object->aArray;
	}

	//movimiento de salidas y entradas
	if ($yesno == 1) $object->fetch_lines();


	$title = $langs->trans('Inventory');
	print '<br>';

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="conf">';

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Entrepot"),"inventario.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Grupo"),"inventario.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Rowid"),"inventario.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Code"),"inventario.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Detalle"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unidad"),"inventario.php", "","",$params,'align="left"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("PU"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Saldo"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Dateini"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("UFV inicio"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("UFV final"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Factor"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Valor Total"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Valor Actualizado UFV"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Diferencia Actualizacion"),"inventario.php", "","",$params,'align="center"',$sortfield,$sortorder);
	print "</tr>\n";



	$sql  = "SELECT t.rowid, t.label ";
	$sql.= " FROM ".MAIN_DB_PREFIX."categorie AS t";
					//$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON ps.fk_product = p.rowid";
	$sql.= " WHERE t.entity = ".$conf->entity;
	$sql.=" ORDER BY t.label";
	$result = $db->query($sql);
	if ($result)
	{
		$i =0 ;
		$numcat = $db->num_rows($result);
		$inventory = array();
		if ($numcat <=0) setEventMessages($langs->trans('No tiene creado categorias'),null,'warnings');
		while ($i < $numcat)
		{
			$objcat = $db->fetch_object($result);
			$sql  = "SELECT p.rowid, p.ref, p.label, p.stock ";
			$sql.= " FROM ".MAIN_DB_PREFIX."product AS p";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."categorie_product AS c ON c.fk_product = p.rowid";
			$sql.= " WHERE p.entity = ".$conf->entity;
			$sql.= " AND c.fk_categorie = ".$objcat->rowid;
			$sql.= " ORDER BY p.ref";

			$resprod = $db->query($sql);
			$ValorAct=0;
			$ValorDif=0;

			if ($resprod)
			{
				echo '<hr>num '.$num = $db->num_rows($resprod);
				if ($num > 0 )
				{

					$o = 0;
					while ($o < $num)
					{
						$obj = $db->fetch_object($resprod);

							//vamos a procesar para cada almacen existente
						$sql = " SELECT t.rowid, t.label";
						$sql.= " FROM ".MAIN_DB_PREFIX."entrepot AS t ";
						$sql.=" WHERE t.statut = 1";
						$sql.= " ORDER BY t.rowid";
						$resent = $db->query($sql);
						$ValorAct=0;
						$ValorDif=0;
						if ($resent)
						{
							echo '<br>nument '. $nument = $db->num_rows($resent);
							if ($nument > 0)
							{
								$e = 0;
								while ($e < $nument)
								{
									$objent = $db->fetch_object($resent);
										//fk_entrepot
									echo '<br>id '.$id = $objent->rowid;

									$aIng= array();
									$aSal=array();;
									$movement = new Mouvementstockext($db);

									$resmov = $movement->mouvement_period($id,$dateinisel,$datefinselect,$idsProduct);
									if ($resmov > 0)
									{
										$aActualPricepeps = $movement->actualPricepeps;
										$aActualPriceppp = $movement->actualPriceppp;

										$aMoving = $movement->aMoving;
										$aMovsal = $movement->aMovsal;
										$aIng = $movement->aIng;
										$aSal = $movement->aSal;
										$aIngentrepot = $movement->aIngentrepot;

										$sumParcial = 0;
										$comp=0;


										$print = true;
										//recorriendo los productos
										$product = new Product($db);
										$product->fetch($obj->rowid);

										$aIng = $aMoving[$obj->rowid];
										if ($aIngentrepot[$obj->rowid] == $id)
											echo ' revisael prod '.$aIngentrepot[$obj->rowid] == $id;
										else
											echo 'no esta dentro de '.$id;
										if ($aIngentrepot[$obj->rowid] == $id)
										{
											$aSal = $aMovsal[$obj->rowid];
											foreach ((array) $aIng AS $fk => $data)
											{
												$aMov = array();
												$aMov['price'] = $data['price_peps'];
												$aMov['datem'] = $data['datem'];
												$aMov['saldo']=$data['qty'];
												$aMovSal = $aSal[$fk];
												$aMov['saldo']+=$aMovSal['qty'];
												$aMov['amount'] = $aMov['saldo']*$aMov['price'];

												$aDate = dol_getdate($data['datem']);
												$datem = dol_mktime(0, 0, 0, $aDate['mon'], $aDate['mday'], $aDate['year']);

												$resufv = $objCsindexescountry->fetch(0,$typufv,$db->idate($datem));
												$valueufvini=0;
												if ($resufv>0)
													$valueufvini = $objCsindexescountry->amount;

												$resufv = $objCsindexescountry->fetch(0,$typufv,$db->idate($datefinsel));
												$valueufvfin=0;

												if ($resufv>0)
													$valueufvfin = $objCsindexescountry->amount;
												else
													setEventMessages($langs->trans('La fecha ').dol_print_date($datefinsel,'day').' '.$langs->trans('No cuenta con tipo de cambio'),null,'errors');
												$factor = 0;
												if ($valueufvini > 0)
													$factor = $valueufvfin / $valueufvini;
												else
												{
													setEventMessages($langs->trans('La fecha ').dol_print_date($datem,'day').' '.$langs->trans('No cuenta con tipo de cambio'),null,'errors');
												}
												// valor actualizado ufv
												$valorAct=($factor)*$aMov['amount'];
												// Valor diferencia ufv
												$valorDif=$valorAct-$aMov['amount'];


												$unit = $langs->trans($product->getLabelOfUnit());

												if ($aMov['saldo']>0)
												{

													print "<tr $bc[$var]>";
													print '<td widht="10%">'.$objent->label.'</td>';
													print '<td widht="20%">'.$objcat->label.'</td>';
													print '<td widht="10%">'.$fk.'</td>';
													print '<td widht="10%">'.$product->getNomUrl(1).'</td>';
													print '<td widht="52%">'.$obj->label.'</td>';
													print '<td align="LEFT" widht="7%">'.$unit.'</td>';
													print '<td align="right" widht="7%">'.price($aMov['price']).'</td>';
													print '<td align="right" widht="7%">'.$aMov['saldo'].'</td>';
													print '<td align="right" widht="7%">'.dol_print_date($aMov['datem'],'day').'</td>';
													print '<td align="right" widht="7%">'.$valueufvini.'</td>';
													print '<td align="right" widht="7%">'.$valueufvfin.'</td>';
													print '<td align="right" widht="7%">'.$factor.'</td>';
													print '<td align="right" widht="7%">'.price(price2num($aMov['amount'],'MT')).'</td>';
													print '<td align="right" widht="7%">'.price(price2num($valorAct,'MT')).'</td>';
													print '<td align="right" widht="7%">'.price(price2num($valorDif,'MT')).'</td>';
													$sumParcial+=$aMov['amount'];
													$sumEntrepot+=$aMov['amount'];
													$sumTotal += $aMov['amount'];

													$sumParcialufv+=$valorAct;
													$sumEntrepotufv+=$valorAct;
													//$sumTotalufv += $valorDif;
													$sumTotalufv += $valorAct;
													$sumValorDif+=$valorDif;
													$sumEntrepotDif+=$valorDif;
													$sumTotalDif+=$valorDif;


													$inventory[] = array('rowid'=>$obj->rowid,'entrepot'=>$objent->label, 'fk_entrepot'=>$objent->rowid,'ref'=>$obj->ref,'label'=>$obj->label,'unit'=>$unit,'pricee'=>$aMov['price'],'saldoo'=>$aMov['saldo'],'amountt'=>$aMov['amount'],'valorAct'=>$valorAct,'valueufvini'=>$valueufvini,'valueufvfin'=>$valueufvfin,'factor'=>$factor,'rowidstock'=>$fk,'valorDif'=>$valorDif,'Grupo'=>$objcat->label,'comp'=>$comp,'sumParcial'=>$sumParcial,'type'=>'l');

													print '</tr>';
												}
											}
										}
										//si existe
									}
									$e++;
								}
							}
						}
						$o++;
						$comp++;

						if ($sumParcial > 0)
						{
							print '<tr class="liste_total">';
							print '<td colspan="2"></td>';
							print '<td colspan="10">'.$langs->trans('Total').' '.$objcat->label.'</td>';
							print '<td align="right">'.price(price2num($sumParcial,'MT')).'</td>';
							print '<td align="right">'.price(price2num($sumParcialufv,'MT')).'</td>';
							print '<td align="right">'.price(price2num($sumValorDif,'MT')).'</td>';
							print '</tr>';
							$inventory[] = array('rowid'=>$obj->rowid,'ref'=>'','label'=>$langs->trans('Total').' '.$objcat->label,'unit'=>'','pricee'=>'','saldoo'=>'','amountt'=>$sumParcial,'valorAct'=>$sumParcialufv,'valorDif'=>$sumValorDif,'Grupo'=>$objcat->label,'type'=>'g');
							if ($sumParcial >0)
								$inventoryGroup[$objcat->label] = $objcat->label;
							else
								unset($inventoryGroup[$objcat->label]);
						}
						$sumParcial =0;
						$sumParcialufv=0;
						$sumValorDif=0;
					}
				}
				if ($sumEntrepot> 0 && $abc)
				{
					print '<tr class="liste_total">';
					print '<td></td>';
					print '<td colspan="11">'.$langs->trans('Total').' '.$objent->label.'</td>';
					print '<td align="right">'.price(price2num($sumEntrepot,'MT')).'</td>';
					print '<td align="right">'.price(price2num($sumEntrepotufv,'MT')).'</td>';
					print '<td align="right">'.price(price2num($sumEntrepotDif,'MT')).'</td>';
					print '</tr>';
					$inventory[] = array('rowid'=>$obj->rowid,'ref'=>'','label'=>$langs->trans('Total').' '.$objent->label,'unit'=>'','pricee'=>'','saldoo'=>'','amountt'=>$sumEntrepot,'valorAct'=>$sumEntrepotufv,'valorDif'=>$sumEntrepotDif,'Grupo'=>'totalEntrepot','type'=>'g','typef'=>2);
					$inventoryGroup['totalEntrepot'] = 'totalEntrepot';
				}
				$sumEntrepot=0;
				$sumEntrepotufv=0;
				$sumEntrepotDif=0;
			}
			$i++;

		}
		if ($sumTotal> 0)
		{
			print '<tr class="liste_total">';
			print '<td colspan="12">'.$langs->trans('Total').'</td>';
			print '<td align="right">'.price(price2num($sumTotal,'MT')).'</td>';
			print '<td align="right">'.price(price2num($sumTotalufv,'MT')).'</td>';
			print '<td align="right">'.price(price2num($sumTotalDif,'MT')).'</td>';
			print '</tr>';
			$inventory[] = array('rowid'=>$obj->rowid,'ref'=>'','label'=>$langs->trans('Total').' '.$langs->trans('General'),'unit'=>'','pricee'=>'','saldoo'=>'','amountt'=>$sumTotal,'valorAct'=>$sumTotalufv,'valorDif'=>$sumTotalDif,'Grupo'=>'total','type'=>'g','typef'=>1);
			$inventoryGroup['total'] = 'total';
		}


		print "</table>";
		$aCloseperiod['periodyear'] = $period_year;
		$aCloseperiod['periodmonth'] = $period_month;
		$aCloseperiod['dateinisel'] = $dateinisel;
		$aCloseperiod['datefinsel'] = $datefinsel;
		$aCloseperiod['datefinselect'] = $datefinselect;
		$aCloseperiod['inventoryGroup'] = $inventoryGroup;
		$aCloseperiod['lines'] = $inventory;
		$_SESSION['aCloseperiod'] = serialize($aCloseperiod);

		print "<div class=\"tabsAction\">\n";
		if ($user->rights->almacen->per->actal)
			print '<input type="submit" class="butAction" value="'.$langs->trans('Closeperiod').'">';

		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?fk_entrepot='.$fk_entrepot.'&action=excel">'.$langs->trans("Excel").'</a>';
		print '</div>';

		print '</form>';

	}
}
$db->close();
llxFooter();
?>
