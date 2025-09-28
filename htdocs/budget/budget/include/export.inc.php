<?php

$dir = $conf->budget->dir_output.'/tmp';
if (! file_exists($dir))
{
	if (dol_mkdir($dir) < 0)
	{
		$error++;
		setEventmessages($langs->trans('Error al crear directorio'),null,'errors');
	}
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
->setLastModifiedBy("yemer colque")
->setTitle("Office 2007 XLSX Test Document")
->setSubject("Office 2007 XLSX Test Document")
->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
->setKeywords("office 2007 openxml php")
->setCategory("Test result file");

if ($action == 'exportpresupgeneral' )
{

	//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);
	$cTitle=$langs->trans("PresupuestoGeneral");

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
	$sheet->getStyle('A2')->getFont()->setSize(15);


	$sheet->mergeCells('A2:F2');
	if($yesnoprice)
		$sheet->mergeCells('A2:F2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);


	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);


	//$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	//$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

	// ENCABEZADO

	$res = $object->fetch($id);
	if($res>0)
	{
		$cTitle=$object->title;
		$cRef=$object->ref;
	//$cAmountpres=$$object->budget_amount;
	}


	// color encabezado
	$objPHPExcel->getActiveSheet()->getStyle('A3:C6')->applyFromArray(
		array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '0c78bf'),
				'size'  => 12,
				'name'  => 'Verdana'
			)));



	// object budget

	$resbud=$object->fetch($id);
	if($resbud>0)
	{
		$cTitle=$object->title;
		$cRef=$object->ref;
		$nAmount=$object->budget_amount;
	}

 	// Moneda y Decimales
 	// filtramos para moneda y numero de decimales
	$resge=$general->fetch(0,$id);
	if($resge>0)
	{
		$nMoneda=$general->base_currency;

		$nDecimalpu=$general->decimal_pu;
		$nDecimalquant=$general->decimal_quant;
		$nDecimaltotal=$general->decimal_total;
	}
	else
	{
		$nDecimalpu=6;
		$nDecimalquant=6;
		$nDecimaltotal=6;
	}


	if($resge>0)
	{
		switch ($general->decimal_pu)
		{
			case 0:
			$cNumeropu='';
			break;
			case 1:
			$cNumeropu='0';
			break;
			case 2:
			$cNumeropu='00';
			break;
			case 3:
			$cNumeropu='000';
			break;
			case 4:
			$cNumeropu='0000';
			break;
			case 5:
			$cNumeropu='00000';
			break;
			case 6:
			$cNumeropu='000000';
			break;
			case 7:
			$cNumeropu='0000000';
			break;
			case 8:
			$cNumeropu='00000000';
			break;
		}
		switch ($general->decimal_quant)
		{
			case 0:
			$cNumeroquant='';
			break;
			case 1:
			$cNumeroquant='0';
			break;
			case 2:
			$cNumeroquant='00';
			break;
			case 3:
			$cNumeroquant='000';
			break;
			case 4:
			$cNumeroquant='0000';
			break;
			case 5:
			$cNumeroquant='00000';
			break;
			case 6:
			$cNumeroquant='000000';
			break;
			case 7:
			$cNumeroquant='0000000';
			break;
			case 8:
			$cNumeroquant='00000000';
			break;

		}
		switch ($general->decimal_total)
		{
			case 0:
			$cNumerototal='';
			break;
			case 1:
			$cNumerototal='0';
			break;
			case 2:
			$cNumerototal='00';
			break;
			case 3:
			$cNumerototal='000';
			break;
			case 4:
			$cNumerototal='0000';
			break;
			case 5:
			$cNumerototal='00000';
			break;
			case 6:
			$cNumerototal='000000';
			break;
			case 7:
			$cNumerototal='0000000';
			break;
			case 8:
			$cNumerototal='00000000';
			break;

		}
	}










	$objPHPExcel->getActiveSheet()->getStyle('B6')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
 	// ProjectO
	$objPHPExcel->getActiveSheet()->setCellValue('A3',html_entity_decode($langs->trans("Project")));
	$objPHPExcel->getActiveSheet()->setCellValue('B3',$cTitle);
	// REFERENCIA
	$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Ref")));
	$objPHPExcel->getActiveSheet()->setCellValue('B4',$cRef);
	// monto presupuestp
	$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Budgetamount"));
	$objPHPExcel->getActiveSheet()->setCellValue('B5',price($nAmount,$nDecimal));
	// moneda
	$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Moneda"));
	$objPHPExcel->getActiveSheet()->setCellValue('B6',$nMoneda);




	// CUERPO
	$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans("Item"));
	$objPHPExcel->getActiveSheet()->setCellValue('B7',html_entity_decode($langs->trans("Description")));
	$objPHPExcel->getActiveSheet()->setCellValue('C7',$langs->trans("Unit"));
	$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Quantity"));
	$objPHPExcel->getActiveSheet()->setCellValue('E7',$langs->trans("Price"));
	$objPHPExcel->getActiveSheet()->setCellValue('F7',$langs->trans("Totalcost"));


	// TABLA COLOR
	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
			),
		),
	);


	$objPHPExcel->getActiveSheet()->getStyle('A7:F7')->applyFromArray(
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
	// AGRUPANDO PARA MOSTRAR
	$aPresupuerto==array();
	$filter = " AND t.fk_budget = ".$id;
	$res = $objectdet->fetchAll('', '', 0, 0, array(), 'AND',$filter);
	if($res>0)
	{
		$lines = $objectdet->lines;
		foreach ($lines AS $j => $line)
		{

			$aPresupuerto[$line->fk_budget][$line->fk_task_parent][$line->id]['label']=$line->label;
			$aPresupuerto[$line->fk_budget][$line->fk_task_parent][$line->id]['ref']=$line->ref;

		}
	}

	// array para titulos de los grupos
	foreach ((array) $aPresupuerto AS $id => $aTaskparent)
	{
		foreach ((array) $aTaskparent AS $row => $data)
		{
			foreach ((array)$data as $k => $linedet)
			{
				print_r($val);
				if($row==0)
				{
					$aTitle[$k]['label']=$linedet['label'];
					$aTitle[$k]['ref']=$linedet['ref'];
				}

			}
		}
	}





	$nTotal=0;
	$j=8;
	foreach ((array) $aPresupuerto AS $id => $aTaskparent)
	{
		foreach ((array) $aTaskparent AS $row => $data)
		{


			if($row!=0)
			{
			 // imprimiendo grupo
				$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$aTitle[$row]['label']);
				$j++;
			}

			foreach ((array)$data as $k => $linedet)
			{


				// imprimiendo  subgrupos
				if( $row!=0)
				{
					$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$linedet['ref']);
					// titulos subgrupos
					$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$linedet['label']);
					// unidad cantidad precio unitario costo total
					$resadd = $objectdetadd->fetch(0,$k);
					if($resadd>0)
					{


						if($objectdetadd->c_grupo==0)
						{
						// unidad
							$cUnidad=$objectdetadd->fk_unit;

							$objtmp = new ProductbudgetLineext($db);
							$objtmp->fk_unit = $objectdetadd->fk_unit;

						// cantidad
							$nCantidad=$objectdetadd->unit_budget;
						// precio unitario
							$nPreciou=$objectdetadd->unit_amount;
						// precio total
							$nPreciot=$objectdetadd->total_amount;
							$nTotal+=$objectdetadd->total_amount;



						//$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxpar, 3, price($partial,0,'',0,$nDecimaltotal), 0, 'R');

						// FORMATOS NUMERO EXCEL
							$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumeroquant);
							$objPHPExcel-> getActiveSheet () -> getStyle ('E'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumeropu);
							$objPHPExcel-> getActiveSheet () -> getStyle ('F'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);



							$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$objtmp->getLabelOfUnit('short'));
							$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$nCantidad);
							$objPHPExcel->getActiveSheet()->setCellValue('E' .$j,$nPreciou);
							$objPHPExcel->getActiveSheet()->setCellValue('F' .$j,$nPreciot);

						}





					}
					$j++;

				}
			}
		}

	// color pie pagina
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'F'.$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));


		$objPHPExcel-> getActiveSheet () -> getStyle ('F'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);
		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,'TOTAL');
		$objPHPExcel->getActiveSheet()->setCellValue('F' .$j,$nTotal);
		$j++;

	// color pie pagina dos
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'F'.$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));

		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,'SON : ');
		$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,num2texto(price2num($nTotal,'MT')));




	}
	$objPHPExcel->setActiveSheetIndex(0);
	//$objPHPExcel->getActiveSheet()->getStyle('A10:D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	//$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				//$objWriter->save("excel/ReportPOA.xlsx");
	$file = 'presupuestogeneral.xlsx';
	$objWriter->save($dir.'/'.$file);
	header("Location: ".DOL_URL_ROOT.'/budget/budget/fiche_export.php?archive='.$file);


}

if ($action == 'exportresource')
{

	//$aType= array(0=>$langs->trans('All'),'MO'=>$langs->trans('Workforce'),'MA'=>$langs->trans('Material'),'MQ'=>$langs->trans('Machinerie'));
	$type=GETPOST('type','alpha');
	//$type="MQ";

	$filter = " AND t.fk_budget = ".$id;
	if($type == 'MO' || $type == 'MA' || $type == 'MQ' )
	{
		$filter.= " AND t.group_structure = '".$type."'";
	}

	$res = $objProductbudget->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);

	if($res>0)
	{
		if($type=='0')
		{
			$cTitle=$langs->trans("AllBudget");
			$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		}
			// mano de obra
		if($type=='MO')
		{
			$cTitle=$langs->trans("Workforce");
			$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		}
			// material
		if($type=='MA')
		{
			$cTitle=$langs->trans("Materials");
			$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		}
			//	machine
		if($type=='MQ')
		{
			$cTitle=$langs->trans("Machineryandequipment");
			$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

		}
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$sheet = $objPHPExcel->getActiveSheet();
		//$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
		//$sheet->getStyle('A2')->getFont()->setSize(15);
		if($type == 'MO' || $type == 'MA')
		{
			$sheet->mergeCells('A2:D2');
			if($yesnoprice)
				$sheet->mergeCells('A2:D2');
			$sheet->getStyle('A2')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);

			// DEFINIENDO COLUMNAS
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);


			// TABLA COLOR
			$styleThickBrownBorderOutline = array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THICK,
						'color' => array('argb' => 'FFA0A0A0'),
					),
				),
			);

			$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->applyFromArray(
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
		if($type == 'MQ')
		{
			$sheet->mergeCells('A2:E2');
			if($yesnoprice)
				$sheet->mergeCells('A2:E2');
			$sheet->getStyle('A2')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);

			// DEFINIENDO COLUMNAS
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

			// colore de la tabla

			$objPHPExcel->getActiveSheet()->getStyle('A7:E7')->applyFromArray(
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

		if($type =='0')
		{
			$sheet->mergeCells('A2:C2');
			if($yesnoprice)
				$sheet->mergeCells('A2:C2');
			$sheet->getStyle('A2')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);

			// DEFINIENDO COLUMNAS
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);


			// TABLA COLOR
			$styleThickBrownBorderOutline = array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THICK,
						'color' => array('argb' => 'FFA0A0A0'),
					),
				),
			);

			$objPHPExcel->getActiveSheet()->getStyle('A7:C7')->applyFromArray(
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


		$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
		$sheet->getStyle('A2')->getFont()->setSize(15);
		// COLOR ENCABEZADO
		$objPHPExcel->getActiveSheet()->getStyle('A3:C6')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0c78bf'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));

		// object budget

		$resbud=$object->fetch($id);
		if($resbud>0)
		{
			$cTitle=$object->title;
			$cRef=$object->ref;
			$nAmount=$object->budget_amount;
		}

 		// Moneda y Decimales

		$resge=$general->fetch(0,$id);
		if($resge>0)
		{
			$nMoneda=$general->base_currency;
			$nDecimal=$general->decimal_pu;
		}

		$objPHPExcel->getActiveSheet()->getStyle('B6')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
 		// ProjectO
		$objPHPExcel->getActiveSheet()->setCellValue('A3',html_entity_decode($langs->trans("Project")));
		$objPHPExcel->getActiveSheet()->setCellValue('B3',$cTitle);
		// REFERENCIA
		$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Ref")));
		$objPHPExcel->getActiveSheet()->setCellValue('B4',$cRef);
		// monto presupuestp
		$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Budgetamount"));
		$objPHPExcel->getActiveSheet()->setCellValue('B5',price($nAmount,$nDecimal));
		// moneda
		$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Moneda"));
		$objPHPExcel->getActiveSheet()->setCellValue('B6',$nMoneda);



		// NOMBRES DE LAS COLUMNAS
		// NOMBRE DE LA TABLA
		$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans("Nro"));
		$objPHPExcel->getActiveSheet()->setCellValue('B7',html_entity_decode($langs->trans("Description")));
		$objPHPExcel->getActiveSheet()->setCellValue('C7',$langs->trans("Unid"));
		if($type=='MA')
		{
			$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Price"));
		}
		elseif($type=='MO')
		{
			$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Costtime"));
		}
		elseif($type=='MQ')
		{
			$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Amountnoprod"));
			$objPHPExcel->getActiveSheet()->setCellValue('E7',$langs->trans("Productive"));
		}
	}


	// IMPRIMIENDO CUERPO
	$seq=1;
	$j=8;

	$nTotal=0;

	// MAQUINARIA


	if($resge>0)
	{
		switch ($general->decimal_pu)
		{
			case 0:
			$cNumeropu='';
			break;
			case 1:
			$cNumeropu='0';
			break;
			case 2:
			$cNumeropu='00';
			break;
			case 3:
			$cNumeropu='000';
			break;
			case 4:
			$cNumeropu='0000';
			break;
			case 5:
			$cNumeropu='00000';
			break;
			case 6:
			$cNumeropu='000000';
			break;
			case 7:
			$cNumeropu='0000000';
			break;
			case 8:
			$cNumeropu='00000000';
			break;
		}
		switch ($general->decimal_quant)
		{
			case 0:
			$cNumeroquant='';
			break;
			case 1:
			$cNumeroquant='0';
			break;
			case 2:
			$cNumeroquant='00';
			break;
			case 3:
			$cNumeroquant='000';
			break;
			case 4:
			$cNumeroquant='0000';
			break;
			case 5:
			$cNumeroquant='00000';
			break;
			case 6:
			$cNumeroquant='000000';
			break;
			case 7:
			$cNumeroquant='0000000';
			break;
			case 8:
			$cNumeroquant='00000000';
			break;

		}
		switch ($general->decimal_total)
		{
			case 0:
			$cNumerototal='';
			break;
			case 1:
			$cNumerototal='0';
			break;
			case 2:
			$cNumerototal='00';
			break;
			case 3:
			$cNumerototal='000';
			break;
			case 4:
			$cNumerototal='0000';
			break;
			case 5:
			$cNumerototal='00000';
			break;
			case 6:
			$cNumerototal='000000';
			break;
			case 7:
			$cNumerototal='0000000';
			break;
			case 8:
			$cNumerototal='00000000';
			break;

		}


	}


	if($res>0 && $type=='MQ')
	{

		$nNoprod=0;
		$nProd=0;

		$lines = $objProductbudget->lines;
		foreach ($lines AS $z => $line)
		{

			// FORMATOS NUMERO EXCEL

			$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);
			$objPHPExcel-> getActiveSheet () -> getStyle ('E'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);


			$objtmp = new ProductbudgetLineext($db);
			$objtmp->fk_unit = $line->fk_unit;

			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
			$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
			$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$objtmp->getLabelOfUnit('short'));
			$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$line->amount_noprod);
			$objPHPExcel->getActiveSheet()->setCellValue('E' .$j,$line->amount);
			$nNoprod+=$line->amount_noprod;
			$nProd+=$line->amount;
			$j++;
			$seq++;
		}

		// COLOR DE TOTALES PIE DE PAGINA

		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'F'.$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));

		$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);
		$objPHPExcel-> getActiveSheet () -> getStyle ('E'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);

		//$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,'TOTAL');
		//$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$nNoprod);
		//$objPHPExcel->getActiveSheet()->setCellValue('E' .$j,$nProd);

	}

	// MANO DE OBRA
	//


	if($res>0 && $type=='MO')
	{
		$lines = $objProductbudget->lines;
		foreach ($lines AS $z => $line)
		{
			$objtmp = new ProductbudgetLineext($db);
			$objtmp->fk_unit = $line->fk_unit;

			// FORMATOS NUMERO EXCEL

			$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);

			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
			$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
			$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$objtmp->getLabelOfUnit('short'));
			$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$line->amount);
			$nTotal+=$line->amount;
			$j++;
			$seq++;
		}

		// COLORES DE PIE DE PAGINA

		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'D'.$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));


		$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);

		//$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,'TOTAL');
		//$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$nTotal);

	}

	// MATERIAL
	if($res>0 && $type=='MA')
	{
		$lines = $objProductbudget->lines;
		foreach ($lines AS $z => $line)
		{

			$objtmp = new ProductbudgetLineext($db);
			$objtmp->fk_unit = $line->fk_unit;


			// FORMATOS NUMERO EXCEL

			$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);

			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
			$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
			$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$objtmp->getLabelOfUnit('short'));
			$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$line->amount);
			$nTotal+=$line->amount;
			$j++;
			$seq++;
		}

		// COLORES DE PIE DE PAGINA
		//

		$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':'.'D'.$j)->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'd13112'),
					'size'  => 10,
					'name'  => 'Verdana'
				)));


		$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);

		//$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,'TOTAL');
		//$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$nTotal);
	}

	// TODO

	if($res>0 && $type=='0')
	{
		$lines = $objProductbudget->lines;
		foreach ($lines AS $z => $line)
		{
			$objtmp = new ProductbudgetLineext($db);
			$objtmp->fk_unit = $line->fk_unit;
			// FORMATO DE NUMERO
			$objPHPExcel-> getActiveSheet () -> getStyle ('D'.$j) -> getNumberFormat () -> setFormatCode ('#,###.'.$cNumerototal);

			$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
			$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
			$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$objtmp->getLabelOfUnit('short'));
			$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$line->amount);
			$nTotal+=$line->amount;
			$j++;
			$seq++;
		}
		//$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,'TOTAL');
		//$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$nTotal);
	}





	$objPHPExcel->setActiveSheetIndex(0);
		//$objPHPExcel->getActiveSheet()->getStyle('A10:D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	//$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				//$objWriter->save("excel/ReportPOA.xlsx");

	$file = 'Equipament.xlsx';

	$objWriter->save($dir.'/'.$file);

	header("Location: ".DOL_URL_ROOT.'/budget/budget/fiche_export.php?archive='.$file);


}
/*
if ($action == 'exportEquipment' || $action == 'exportWorkforce' || $action == 'exportProyectdata')
{


	//PIE DE PAGINA
	$objPHPExcel->setActiveSheetIndex(0);

	if($action == 'exportEquipment')
	{
		$cTitle=$langs->trans("Equipment");
		// formato de las celdas
		$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	}
	if($action == 'exportWorkforce')
	{
		$cTitle=$langs->trans("Workforce");
		// formatos de la celda
		$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	}
	if ($action == 'exportProyectdata') {
		//formato de la celda
		$objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
		$cTitle=$langs->trans("Projectdata");
	}



	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
	$sheet->getStyle('A2')->getFont()->setSize(15);

	if($action == 'exportEquipment' || $action == 'exportWorkforce')
	{

		$sheet->mergeCells('A2:D2');
		if($yesnoprice)
			$sheet->mergeCells('A2:D2');
		$sheet->getStyle('A2')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);

	}
	if($action == 'exportProyectdata')
	{
		$sheet->mergeCells('A2:C2');
		if($yesnoprice)
			$sheet->mergeCells('A2:C2');
		$sheet->getStyle('A2')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);

	}
	$sheet->setCellValueByColumnAndRow(0,2, $cTitle);

	//FORMATOS DE LAS  COLUMNAS
	$sheet->getStyle('A2')->getFont()->setSize(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);



	// ENCABEZADO
	$res = $object->fetch($id);
	if($res>0)
	{
		$cTitle=$object->title;
		$cRef=$object->ref;
	//$cAmountpres=$$object->budget_amount;
	}

	if($action == 'exportEquipment' || $action == 'exportWorkforce')
	{

			// color encabezado
		$objPHPExcel->getActiveSheet()->getStyle('A3:C6')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0c78bf'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));


		// ProjectO
		$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Project")));
		$objPHPExcel->getActiveSheet()->setCellValue('B4',$cTitle);
		// REFERENCIA
		$objPHPExcel->getActiveSheet()->setCellValue('A5',html_entity_decode($langs->trans("Ref")));
		$objPHPExcel->getActiveSheet()->setCellValue('B5',$cRef);
		// MONEDA
		$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Moneda"));
		$objPHPExcel->getActiveSheet()->setCellValue('B6',"");

		// CUERPO
		$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans("Nro"));
		$objPHPExcel->getActiveSheet()->setCellValue('B7',html_entity_decode($langs->trans("Description")));

		if($action=='exportEquipment')
		{
			$objPHPExcel->getActiveSheet()->setCellValue('C7',$langs->trans("Unid"));
			$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Quantity"));
		}
		else
		{
			$objPHPExcel->getActiveSheet()->setCellValue('C7',$langs->trans("Unid"));
			$objPHPExcel->getActiveSheet()->setCellValue('D7',$langs->trans("Unitprice"));
		}



	}
	if($action== 'exportProyectdata')
	{


			// color encabezado
		$objPHPExcel->getActiveSheet()->getStyle('A3:C6')->applyFromArray(
			array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0c78bf'),
					'size'  => 12,
					'name'  => 'Verdana'
				)));

		// ProjectO
		$objPHPExcel->getActiveSheet()->setCellValue('A4',html_entity_decode($langs->trans("Project")));
		$objPHPExcel->getActiveSheet()->setCellValue('B4',$cTitle);
		// REFERENCIA
		$objPHPExcel->getActiveSheet()->setCellValue('A5',html_entity_decode($langs->trans("Ref")));
		$objPHPExcel->getActiveSheet()->setCellValue('B5',$cRef);
		// MONEDA
		$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Moneda"));
		$objPHPExcel->getActiveSheet()->setCellValue('B6',"");
		// CUERPO
		$objPHPExcel->getActiveSheet()->setCellValue('A7',$langs->trans("Nro"));
		$objPHPExcel->getActiveSheet()->setCellValue('B7',html_entity_decode($langs->trans("Description")));
		$objPHPExcel->getActiveSheet()->setCellValue('C7',$langs->trans("Percentage"));

	}

	if($action == 'exportEquipment' || $action == 'exportWorkforce')
	{
		// TABLA COLOR
		$styleThickBrownBorderOutline = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);

		$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->applyFromArray(
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
	if($action== 'exportProyectdata')
	{


		$objPHPExcel->getActiveSheet()->getStyle('A7:C7')->applyFromArray(
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

	$seq=0;
	// filtrando product budget con id

	$aEquipment==array();
	$filter = " AND t.fk_budget = ".$id;
	$res = $objprodb->fetchAll('', '', 0, 0, array(1=>1), 'AND',$filter);

	//echo'<pre>';
	//print_r($objprodb);
	//echo'<pre>';
	//exit;


	$j=8;
	// MATERIALES
	if($action == 'exportEquipment' )
	{
		// 	Equipament



		if($res>0)
		{
			$lines = $objprodb->lines;
			foreach ($lines AS $z => $line)
			{
				$seq++;
				$cUnits = fetch_unit('',$line->fk_unit);
				$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
				$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
				$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$cUnits);
				$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$line->amount);
				$j++;
			}
		}
	}

	$j=8;
	// MANO DE OBRA
	// falta completar
	if($action=='exportWorkforce')
	{

		if($res>0)
		{

			$lines = $objprodb->lines;
			foreach ($lines AS $z => $line)
			{
				$seq++;
				$cUnits = fetch_unit('',$line->fk_unit);
				$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
				$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
				$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$cUnits);
				$objPHPExcel->getActiveSheet()->setCellValue('D' .$j,$line->amount);
				$j++;
			}
		}
	}

	$j=8;
	// DATOS ProjectO
	// falta completar
	if($action=='exportProyectdata')
	{

		if($res>0)
		{

			$lines = $objprodb->lines;
			foreach ($lines AS $z => $line)
			{
				$seq++;
				$cUnits = fetch_unit('',$line->fk_unit);
				$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$seq);
				$objPHPExcel->getActiveSheet()->setCellValue('B' .$j,$line->label);
				//$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$cUnits);
				$objPHPExcel->getActiveSheet()->setCellValue('C' .$j,$line->amount);
				$j++;
			}
		}
	}




	$objPHPExcel->setActiveSheetIndex(0);
	//$objPHPExcel->getActiveSheet()->getStyle('A10:D'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	//$objPHPExcel->setActiveSheetIndex(0);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				//$objWriter->save("excel/ReportPOA.xlsx");
	/*
	if($action == 'exportEquipment')
	{
		$file = 'presupuestogeneral.xlsx';
	}
	if($action == 'exportWorkforce')
	{
		$file = 'presupuestogeneral.xlsx';
	}
	if ($action == 'exportProjectdata')
	{
		$file = 'presupuestogeneral.xlsx';
	}

	$file = 'Equipament.xlsx';

	$objWriter->save($dir.'/'.$file);
	header("Location: ".DOL_URL_ROOT.'/budget/budget/fiche_export.php?archive='.$file);



*/

//}
	elseif($action == 'exportpriceunit')
	{

	//vamos a definir ciertos valores
		$aColumn=array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G');

	//PIE DE PAGINA
		$objPHPExcel->setActiveSheetIndex(0);
		$cTitle= html_entity_decode($langs->trans("Unitpriceanalysis"));

		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0,2, $cTitle);
		$sheet->getStyle('A2')->getFont()->setSize(15);


		$sheet->mergeCells('A2:G2');
		if($yesnoprice)
			$sheet->mergeCells('A2:G2');
		$sheet->getStyle('A2')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);



		$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		$objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
		// ENCABEZADO




		$res = $object->fetch($id);
		if($res>0)
		{
			$cTitle=$object->title;
			$cRef=$object->ref;
			//vamos a recuperar cada uno de los items cargados
			$filter = " AND t.fk_budget = ".$id;
			$res = $objectdet->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
			if ($res >0)
			{
				$lines = $objectdet->lines;
				foreach ($lines AS $j => $line)
				{

					$resd = $objectdetadd->fetch(0,$line->id);

					if($resd==1 && $objectdetadd->c_grupo==0)
					{
						//procesamos
						$sum = $objectdetadd->procedure_calc($id,$line->id,true);
						$aReport[] = $objectdetadd->aSpread;
					}
				}
			}
		}






		$lin = 4;
		foreach ($aReport AS $j => $aDatatask)
		{
			foreach ($aDatatask AS $fk_task => $lines)
			{
				$objectdet->fetch($fk_task);
				$objectdetadd->fetch(0,$fk_task);
				$objTmp = new BudgettaskaddLineext($db);
				$objTmp->fk_unit = $objectdetadd->fk_unit;
				// ProjectO
				$cTextnumber='';
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($langs->trans("Activity").' '.$objectdet->ref.' '.$objectdet->label));
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$lin.':G'.$lin);

				$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':C'.$lin)->applyFromArray(
					array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '0c78bf'),
							'size'  => 12,
							'name'  => 'Verdana'
						)));
				$lin++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($langs->trans("Unit")));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,html_entity_decode($langs->trans($objTmp->getLabelOfUnit('short'))));
				$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':C'.$lin)->applyFromArray(
					array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '0c78bf'),
							'size'  => 12,
							'name'  => 'Verdana'
						)));
				$lin++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($langs->trans("Quantity")));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,$objectdetadd->unit_budget);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':C'.$lin)->applyFromArray(
					array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '0c78bf'),
							'size'  => 12,
							'name'  => 'Verdana'
						)));
				$lin++;
			//armamos titulos
			// color encabezado

			// CABECERA
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,html_entity_decode($langs->trans("Description")));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$lin,html_entity_decode($langs->trans("Unit")));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$lin,$langs->trans("Quantity"));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$lin,$langs->trans("Percentproductivity"));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$lin,$langs->trans("Amountnoprod"));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$lin,$langs->trans("P.U."));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$lin,$langs->trans("Costtotal"));

			// TABLA COLOR

				$styleThickBrownBorderOutline = array(
					'borders' => array(
						'outline' => array(
							'style' => PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('argb' => 'FFA0A0A0'),
						),
					),
				);


				$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':G'.$lin)->applyFromArray(
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
				$lin++;

				//mostramos el cuerpo
				foreach ($lines AS $k => $aData)
				{
					foreach ($aData AS $nom => $row)
					{
						foreach ($row AS $nReg => $value)
						{
							if ($nom == 'datag')
							{
							// imprimiendo grupo
								$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$nReg].$lin,$value);
								$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':G'.$lin)->applyFromArray(
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
												'argb' => 'DCDCDC'
											),
											'endcolor'   => array(
												'argb' => 'C0C0C0'
											)
										)
									)
								);
							}
							if ($nom == 'data')
							{
							// imprimiendo detalle
								$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$nReg].$lin,$value);
							}
							if ($nom == 'total')
							{
								// imprimiendo detalle
								$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$nReg].$lin,$value);
								$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':G'.$lin)->applyFromArray(
									array(
										'font'    => array(
											'bold'      => true
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
												'argb' => 'A9A9A9'
											),
											'endcolor'   => array(
												'argb' => '808080'
											)
										)
									)
								);
							}
							if ($nom == 'totalf')
							{
								// imprimiendo detalle
								$cTextnumber = num2texto(price2num($value,'MT'));
								$objPHPExcel->getActiveSheet()->setCellValue($aColumn[$nReg].$lin,$value);
								$objPHPExcel->getActiveSheet()->getStyle('A'.$lin.':G'.$lin)->applyFromArray(
									array(
										'font'    => array(
											'bold'      => true
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
												'argb' => 'FFFF00'
											),
											'endcolor'   => array(
												'argb' => 'F0E68C'
											)
										)
									)
								);
							}
						}
					}
					$lin++;
				}
			}
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$lin,$langs->trans('Son').': '.$cTextnumber);
			$lin++;
			$lin++;
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

					// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				//$objWriter->save("excel/ReportPOA.xlsx");
		$file = 'priceunits.xlsx';
		$objWriter->save($dir.'/'.$file);
		header("Location: ".DOL_URL_ROOT.'/budget/budget/fiche_export.php?archive='.$file);


	}


	?>