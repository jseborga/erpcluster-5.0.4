<?php
/* No One */
require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';

require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentext.class.php");

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
if ($conf->purchase->enabled)
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';
else
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';

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
$langs->load("product");

if (!isset($_SESSION['period_year']))
	$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now());
$pastmonthyear = $period_year;

//Rescate de Acciones y Datos
$date_ini       = dol_mktime(0, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
$date_end       = dol_mktime(23, 59, 59, GETPOST('date_endmonth'),  GETPOST('date_endday'),  GETPOST('date_endyear'));
$fk_departament = GETPOST('fk_departament');
$action         = GETPOST('action');


if (empty($date_end) && empty($date_ini)) // We define date_start and date_end
{
	$date_ini=dol_get_first_day($pastmonthyear,1,false);
	$date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$formfile       = new FormFile($db);
//$form = new Form($db);
$form           = new Formv($db);
$objContab      = new Contab($db);
$objProduct     = new Product($db);
$objUser        = new User($db);
$objDepartamemt = new Pdepartamentext($db);
if ($conf->purchase->enabled)
	$objComando     = new FournisseurCommandeext($db);
else
	$objComando     = new Commandefournisseur($db);
/*
 * Actions
 */

if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);


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
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',html_entity_decode($langs->trans("Reportofexpensesbyareaaccordingtopurchases")));
		//$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

	//Encabezados


	$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('De'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aReporte[2],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:E3');

	$objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Hasta'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',dol_print_date($aReporte[3],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:E4');
	if($aReporte[4] == -1){
		$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Departament'));
		$objPHPExcel->getActiveSheet()->SetCellValue('B5',"Todos los departamento");
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:E5');
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('A5',$langs->trans('Departament'));
		$objPHPExcel->getActiveSheet()->SetCellValue('B5',$aReporte[4]);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:E5');
	}



	$line = 7;
		//CABECERAS DE LA TABLA
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$langs->trans('Ref'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$langs->trans('Descripcion de compra de producto/servicio'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$langs->trans('Fecha'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$langs->trans('Unit'));
	if($aReporte[4] == -1)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans('Departament'));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans('Solicitante'));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$langs->trans('Valor de Pedido'));

		$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':G'.$line)->applyFromArray(
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
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans('Solicitante'));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$langs->trans('Valor de Pedido'));

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
	}





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
	$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);


	//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
	//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$idGr = 1;
	$line = 8;
	foreach ( $aReporte[1] as $j => $row)
	{

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['label']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,dol_print_date($row['fecha'],'day'));
		$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['unidad']);
		if($aReporte[4] == -1){
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['depa']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['respon']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$row['total']);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}else{
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['respon']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['total']);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

		}
		$line++;
	}
	if($aReporte[4] == -1){
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,"Total ");
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$aReporte[5]);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,"Total ");
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$aReporte[5]);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/salesarea.xlsx");
	header('Location: '.DOL_URL_ROOT.'/contab/report/fiche_export.php?archive=salesarea.xlsx');
}



/*
 * View
 */

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Reportofexpensesbyareaaccordingtopurchases"), $page, "salesarea.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"salesarea.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="consult">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';


//fk_departament
print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td>';
print $form->select_departament($fk_departament,'fk_departament','',0,1,'',0);
print '</td></tr>';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="1">';
$form->select_date($date_ini,'date_ini','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="1">';
$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

print '</form>';

print '<br><br>';

/*
 *Gastos Por area
 *Listar todos los pedidos a proveedores filtrando el area(fk_departament) y rango de fechas
 *Utilizar las tablas llx_commande_fournisseur, llx_commande_fournisseur_add
 *llx_commande_fournisseurdet, llx_commande_fournisseurdet_add
 *y las tablas necesarias como ser productos y otros
 */

if ($action == 'consult')
{
	dol_htmloutput_mesg($mesg);

	$aLineas = array();

	$sql  = "SELECT f.rowid as cid, f.ref, f.date_commande ,fd.label ,fd.fk_unit ,fa.fk_departament, f.fk_user_author, fd.total_ttc ";
	$sql.= " , fd.qty, fd.fk_product, fd.price ";
	$sql .= " FROM ".MAIN_DB_PREFIX."commande_fournisseur_add as fa ";
	$sql .= " INNER JOIN ".MAIN_DB_PREFIX."commande_fournisseur as f ON f.rowid = fa.fk_commande_fournisseur ";
	$sql .= " INNER JOIN ".MAIN_DB_PREFIX."commande_fournisseurdet as fd ON fd.fk_commande = f.rowid";
	$sql .= " WHERE fa.status > 0";
	if($fk_departament != -1){
		$sql .= " AND fa.fk_departament = ".$fk_departament;
	}
	$sql .= " AND f.date_commande BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_end)."' ";

	//echo $sql;

	$sumatotal = 0;

	$resql=$db->query($sql);

	if($resql)
	{
		$num = $db->num_rows($resql);
		if ($num)
		{
			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
            //print_liste_field_titre($langs->trans("IdProducto"),"", "","","","");
			print_liste_field_titre($langs->trans("Ref"),"", "","","","");
			print_liste_field_titre($langs->trans("Descripcion de compra de producto/servicio"),"", "","","","align='center'");
			print_liste_field_titre($langs->trans("Date"),"", "","","","align='center'");
			print_liste_field_titre($langs->trans("Unit"),"", "","","",'align="center"');
			//print_liste_field_titre($langs->trans("Qty"),"", "","","",'align="right"');
			if($fk_departament == -1){
				print_liste_field_titre($langs->trans("Departament"),"", "","","",'align="left"');
			}
			print_liste_field_titre($langs->trans("Solicitante"),"", "",'','','align="left"');
			print_liste_field_titre($langs->trans("Valor Pedido"),"", "",'','','align="right"');
			print "</tr>\n";
			$i=0;
			$colspan = 5;

			while ($i < $num)
			{
				$var = !$var;
				print '<tr '.$bc[$var].'>';
				$obj = $db->fetch_object($resql);
				if($obj)
				{
					$objComando->id = $obj->cid;
					$objComando->ref = $obj->ref;
					if ($conf->purchase->enabled)
						print '<td>'.$objComando->getNomUrladd(1).'</td>';
					else
						print '<td>'.$objComando->getNomUrl(1).'</td>';
					//print '<td>'.$obj->ref.'</td>';
					$aLineas[$i]['ref'] = $obj->ref;
					if ($obj->fk_product>0)
					{
						$objProduct->fetch($obj->fk_product);
						print '<td>'.$objProduct->getNomUrl(1).' '.$objProduct->label.'</td>';
					}
					else
						print '<td>'.$obj->label.'</td>';
					$aLineas[$i]['label'] = $obj->label;
					print '<td align="center">'.dol_print_date($db->jdate($obj->date_commande),"day").'</td>';
					$aLineas[$i]['fecha'] = $db->jdate($obj->date_commande);

					$objProduct->fk_unit = $obj->fk_unit;
					print '<td align="center">'.$objProduct->getLabelOfUnit('short').'</td>';
					$aLineas[$i]['unidad'] = $langs->trans($objProduct->getLabelOfUnit());

					//print '<td align="right">'.$obj->qty.'</td>';


					if($fk_departament == -1){
						$objDepartamemt->fetch($obj->fk_departament);
						$labelDep = $objDepartamemt->label;
						print '<td align="left">'.$objDepartamemt->getNomUrl().'</td>';
						$aLineas[$i]['depa'] = $objDepartamemt->label;
						$colspan = 6;
					}

					$objUser->fetch($obj->fk_user_author);
					print '<td align="left">'.$objUser->getNomUrl(1).'</td>';
					$aLineas[$i]['respon'] = $objUser->login;
					print '<td align="right">'.price(price2num($obj->total_ttc,'MT')).'</td>';
					$sumatotal = $sumatotal + $obj->total_ttc;
					$aLineas[$i]['total'] = $obj->total_ttc;
				}
				print '</tr>';
				$i++;
			}
			print '<tr><td align="right" colspan = '.$colspan.'>Total</td><td align="right">'.price(price2num($sumatotal,'MT')).'</td></tr>';
		}print "</table>";

	 //Seccion de Reportes

		$refDepartament = '';
		$rDp =  $objDepartamemt->fetch($fk_departament);
		if($rDp > 0){
			$refDepartament = $objDepartamemt->ref;
			$labelDepartamento = $objDepartamemt->label;
		}else{
			$labelDepartamento =-1;
		}

	//echo "Fk departament ".$fk_departament." - Label :".$labelDepartamento . " RES DEPA ".$rDp;

		$aReporte = array(1=>$aLineas, 2=>$date_ini, 3=>$date_end, 4=>$labelDepartamento, 5=>$sumatotal);
		$_SESSION['aReporte'] = serialize($aReporte);
		print '<div class="tabsAction">'."\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';
		print '</div>'."\n";
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';

		/*Aqui estaba el reporte*/


		if($fk_departament == -1){
			$filename='contab/'.$period_year.'/salesarea';
			$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/salesarea';
			$modelpdf = "salesarea";
		}else{
			$filename='contab/'.$period_year.'/salesareaadd';
			$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/salesareaadd';
			$modelpdf = "salesareaadd";
		}


		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$objContab->departament = $labelDepartamento;
		$objContab->refDepartament = $refDepartament;
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
}


llxFooter();
$db->close();
?>