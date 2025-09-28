<?php
/* No One */
require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/factureext.class.php';

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';


	//require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';


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


$langs->load("contab");
$langs->load("almacen");
$langs->load("product");


$action       = GETPOST('action');
$type_seat    = GETPOST("type_seat");

if (!isset($_SESSION['period_year']))
	$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now());
$pastmonthyear = $period_year;
if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}
$date_ini  = dol_mktime(0, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
$date_end  = dol_mktime(23, 59, 59, GETPOST('date_endmonth'),  GETPOST('date_endday'),  GETPOST('date_endyear'));
if (empty($date_end) && empty($date_ini)) // We define date_start and date_end
{
	$date_ini=dol_get_first_day($pastmonthyear,1,false);
	$date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$aType = array(1=>$langs->trans('Ingreso'),2=>$langs->trans('Egreso'),3=>$langs->trans('Traspaso'));
$mesg = '';


$object     = new Factureext($db);
$objStockM  = new Mouvementstockext($db);
$objStockMA = new Stockmouvementaddext($db);
$objContab  = new Contab($db);

/*if ($conf->almacen->enabled)
{
	$objStock = new Mouvementstockext($db);
	$objStockadd = new Stockmouvementaddext($db);
}
else
$objStock = new Mouvementstock($db);*/

$objProduct = new Product($db);

$formfile = new FormFile($db);
$form = new Form($db);
/*
 * Actions
 */

if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);
	$aGrupos =array(1=>"PRODUCTOS",2=>"SERVICIOS");

	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
			),
		),
	);
		//PROCESO 1
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
    //armamos la cabecera

	//TITULO
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans("Rentabilidad Bruta"));
		//$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

	//Encabezados


	$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('De'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aReporte[2],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:F3');

	$objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Hasta'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',dol_print_date($aReporte[3],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:F4');
	$line = 6;
		//CABECERAS DE LA TABLA
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Produc'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('Unit'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$langs->trans('Qty'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$langs->trans('Totalsale'));
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans('Totalcost '));
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans('Rendimiento'));

	$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':F'.$line)->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'top'     => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'fill' => array(
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




	//Para poner formato a los numeros en el excel
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);

	//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7');

	//FORMATO
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);



	//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
	//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$idGr = 1;
	$line = 7;
	foreach ( $aReporte[1] as $j => $row)
	{
		if($row['producto'] == -1){
			//$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$aGrupos[$idGr]);
			//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$line.':E'.$line);
			//$idGr++;
		}else{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['producto']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['unidad']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['qty']);
			$sumQty+= $row['qty'];
			$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['total_venta']);
			$sumTotal+= $row['total_venta'];
			$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['total_costo']);
			$sumCost+= $row['total_costo'];
			$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['rendimiento']);
			$sumRend+= $row['rendimiento'];

			$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}
		$line++;
	}
	//TOTALES
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Total'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$sumQty);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$sumTotal);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$sumCost);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$sumRend);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/sales.xlsx");
	header('Location: '.DOL_URL_ROOT.'/contab/report/fiche_export.php?archive=sales.xlsx');
}



/*
 * View
 */


$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Grossprofitabilityofproducts"), $page, "sales.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"sales.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
$form->select_date($date_ini,'date_ini','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

print '</form>';




if ($action == 'edit')
{
	dol_htmloutput_mesg($mesg);

	$aIdProductos    = array();
	$aProductos      = array();
	$aProductosOk    = array();
	$aServicios      = array();
	$cantidad        = 0;


	/**************************************/
	$sql  = 'SELECT ';
	$sql .= ' t.rowid, t.facnumber';
	$sql .= ' FROM '.MAIN_DB_PREFIX.'facture as t';
	$sql .= ' WHERE 1 = 1';
	$sql.= " AND t.fk_statut >0";
	$sql .= " AND t.datec BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_end)."' ";

	//echo 'sql : '.$sql;
	$resql=$db->query($sql);
	$num = $db->num_rows($resql);

	if($num)
	{
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i=0;
			$indSer = 0;
			$indPro = 0;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);

				$res = $object->fetch($obj->rowid);
				foreach ($object->lines AS $j =>$line)
				{
					if(!empty($line->fk_product))
					{
						if (in_array($line->fk_product, $aIdProductos)) {
							//si existe el valor en el array
						}else{
							$aIdProductos [] = $line->fk_product;
						}
						//Si la factura tiene producto
						$rP = $objProduct->fetch($line->fk_product);
						if($rP > 0){
							$label = $objProduct->label;
						}else{
							$label = "";
						}
						$aProductos[$indPro][$line->fk_product]['unidad']     = $line->fk_unit;
						$aProductos[$indPro][$line->fk_product]['decripcion'] = $label;
						$aProductos[$indPro][$line->fk_product]['total']      = $line->total_ttc;
						$aProductos[$indPro][$line->fk_product]['qty']      = $line->qty;

						$cantidad = $line->qty;
						//echo $line->id." ". $line->qty." ".$line->total_ttc."<br>";
						//echo "cantidad factureDet : ".$cantidad;

						$condicion = " AND t.fk_product = ".$line->fk_product . " AND t.fk_origin = ".$line->fk_facture. " AND t.origintype='facture'";
						$rS = $objStockM->fetchAll("","",0,0,array(1=>1),"AND",$condicion,true);
						//echo " valor res ".$rS."<br>";
						if($rS > 0)
						{
							$pricebase = 0;
							foreach ($objStockM->lines AS $j => $linem)
							{
								if (empty($pricebase))
								{
									if($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY == 0){
								//Metodo PPP
										$pricebase = $linem->price;
									}elseif($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY == 1){
								//Metodo PEPS

										$rSA = $objStockMA->fetchAll("","",0,0,array(1=>1),"AND"," AND t.fk_stock_mouvement = ".$linem->id,true);
										if($rSA > 0){
										//echo "precio segun PEPS : ".$objStockMA->value_peps." * ".$cantidad."<br>";
											$pricebase = $objStockMA->value_peps;
										}else{
											$error++;
											$pricebase = -1;
										}
									}
								}
							}
							$aProductos[$indPro][$line->fk_product]['costo'] = $pricebase*$cantidad;

						}else{
							//echo "FALSO DE MOEUVENET";
						}


						$indPro++;
						$cantidad = 0;

					}else{
						//Si la factura no tiene producto y es servicio
						$aServicios[$indSer]['unidad']      = $line->fk_unit;
						$aServicios[$indSer]['descripcion'] = $line->description;
						$aServicios[$indSer]['precio']      = $line->total_ttc;
						$aServicios[$indSer]['costo']       = 0;
						$indSer++;
					}
				}
				$i++;
			}
		}

		$sumaTotales = 0;
		$costo       = 0;
		$descripcion = "";
		$unidad      = "";
		$qty = 0;
		foreach ($aIdProductos as $k => $val) {
			foreach ($aProductos as $key => $value) {
				foreach ($value as $i => $valor) {
					if($val == $i){
						$sumaTotales += $valor['total'];
						$descripcion = $valor['decripcion'];
						$unidad      = $valor['unidad'];
						$qty      += $valor['qty'];
						$costo       += $valor['costo'];

					}
				}
			}

			$aProductosOk[$val]['decripcion']  = $descripcion;
			$aProductosOk[$val]['unidad']       = $unidad;
			$aProductosOk[$val]['qty']       = $qty;
			$aProductosOk[$val]['total']       = $sumaTotales;
			$aProductosOk[$val]['costo']       = $costo;

			$sumaTotales = 0;
			$costo       = 0;
			$descripcion = "";
			$unidad      = "";
		}

		dol_fiche_head($head, 'card', $langs->trans("Sales"), 0, 'journal');

		$aLineas = array();
		$indLin  = 0;
		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
			//print_liste_field_titre($langs->trans("IdProducto"),"", "","","","");
		print_liste_field_titre($langs->trans("Producto"),"", "","","","");
		print_liste_field_titre($langs->trans("Unit"),"", "","","","align='center'");
		print_liste_field_titre($langs->trans("Qty"),"", "","","","align='center'");
		print_liste_field_titre($langs->trans("Totalsales"),"", "","","","align='right'");
		print_liste_field_titre($langs->trans("Totalcost"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Rendimiento"),"", "","","",'align="right"');
			//print_liste_field_titre($langs->trans("Status"),"", "",'','','align="right"');
		print "</tr>\n";
		$var=True;
		//print '<tr '.$bc[$var].'><td colspan = 6 align = "left">PRODUCTOS</td></tr>';

		$aLineas[$indLin]['producto'] = -1;
		$indLin++;

		foreach ($aProductosOk as $y => $dato)
		{
			$var = !$var;
			print '<tr '.$bc[$var].'>';
			$objProduct->fetch($y);
			$refProd = $objProduct->ref;
			$objProduct->id = $y;
			$objProduct->ref = $refProd;
				//print '<td>'.$dato['decripcion'].'</td>';
			print '<td>'.$objProduct->getNomUrl(1)."-".$dato['decripcion'].'</td>';
			$aLineas[$indLin]['producto'] = $dato['decripcion'];

			$objProduct->fk_unit = $dato['unidad'];
			if($objProduct-> getLabelOfUnit('short') != -1){
				print '<td align = "center">'.$objProduct-> getLabelOfUnit('long').'</td>';
				$aLineas[$indLin]['unidad'] = $objProduct-> getLabelOfUnit('long');
			}else{
				print '<td align = "center">'.'</td>';
				$aLineas[$indLin]['unidad'] = "";
			}
			print '<td align="right">'.price(price2num($dato['qty'],'MT')).'</td>';
			$sumQty+= $dato['qty'];
			print '<td align="right">'.price(price2num($dato['total'],'MT')).'</td>';
			$sumTotal+= $dato['total'];
			$aLineas[$indLin]['total_venta'] = $dato['total'];
			print '<td align="right">'.price(price2num($dato['costo'],'MT')).'</td>';
			$sumCost+= $dato['costo'];
			$aLineas[$indLin]['total_costo'] = $dato['costo'];
			print '<td align="right">'.price(price2num($dato['total'] - $dato['costo'],'MT')).'</td>';
			$aLineas[$indLin]['rendimiento'] = $dato['total'] - $dato['costo'];
			$sumRend+=$dato['total'] - $dato['costo'];
			print '</tr>';
			$indLin++;
		}
		//mostramos totales
		print '<tr class="liste_total">';
		print '<td>'.$langs->trans('Total').'</td>';
		print '<td>'.'</td>';
		print '<td align="right">'.price(price2num($sumQty,'MT')).'</td>';
		print '<td align="right">'.price(price2num($sumTotal,'MT')).'</td>';
		print '<td align="right">'.price(price2num($sumCostRend,'MT')).'</td>';
		print '<td align="right">'.price(price2num($sumRend,'MT')).'</td>';
		print '<td>'.'</td>';
		print '</tr>';


		//$var = !$var;
	//print '<tr '.$bc[$var].'><td colspan = 6 align = "left">SERVICIOS</td></tr>';
	//$indLin++;
	//$aLineas[$indLin]['producto'] = -1;
	//$indLin++;
	//$aServicios= array();
	//foreach ((array) $aServicios as $ey => $datos) {
	//	$var = !$var;
	//	print '<tr '.$bc[$var].'>';
	//			//print '<td>'.$ey.'</td>';
	//	print '<td>'.$datos['descripcion'].'</td>';
	//	$aLineas[$indLin]['producto'] = $datos['descripcion'];
//
	//			//print '<td>'.$datos['unidad'].'</td>';
	//	$objProduct->fk_unit = $datos['unidad'];
	//	if($objProduct-> getLabelOfUnit('short') != -1){
	//		print '<td align = "center">'.$objProduct-> getLabelOfUnit('long').'</td>';
	//		$aLineas[$indLin]['unidad'] = $objProduct-> getLabelOfUnit('long');
	//	}else{
	//		print '<td align = "center">'.'</td>';
	//		$aLineas[$indLin]['unidad'] = "";
	//	}


	//	print '<td align="right">'.price(price2num($datos['precio'],'MT')).'</td>';
	//	$aLineas[$indLin]['total_venta'] = $datos['precio'];
	//	print '<td align="right">'.price(price2num($datos['costo'],'MT')).'</td>';
	//	$aLineas[$indLin]['total_costo'] = $datos['costo'];
	//	print '<td align="right">'.price(price2num($datos['precio'] - $datos['costo'],'MT')).'</td>';
	//	$aLineas[$indLin]['rendimiento'] = $datos['precio'] - $datos['costo'];
	//	print '</tr>';
	//	$indLin++;
	//}

		print "</table>";


		$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$date_end);
		$_SESSION['aReporte'] = serialize($aReporte);


		print '<div class="tabsAction">'."\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
		print '</div>'."\n";
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';

		/*Aqui estaba el reporte*/
		$filename='contab/'.$period_year.'/sales';
		$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/sales';

		$modelpdf = "sales";

		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
				//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		$result=$objContab->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result < 0) dol_print_error($db,$result);

		$urlsource=$_SERVER['PHP_SELF'];
			//$genallowed=$user->rights->assistance->lic->hiddemdoc;
			//$delallowed=$user->rights->assistance->lic->deldoc;
		$genallowed = 0;
		$delallowed = 0;
		print $formfile->showdocuments('contab',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

		$somethingshown=$formfile->numoffiles;

		print '</td></tr></table>';

	}
	else
	{
		setEventMessages($object->error,$object->errors,'errors');
				//setEventMessages($langs->trans('No existe registros'),null,'warnings');
	}
}

llxFooter();
$db->close();
?>