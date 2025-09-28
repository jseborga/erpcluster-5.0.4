<?php

$borders = array(
		 'borders'=>
		 array('allborders'=>
		       array('style'=>PHPExcel_Style_Border::BORDER_THIN,
			     'color'=> array('argb'=>'FF0000')
			     )
		       ),
		 'font'=>array('bold'=>true,)
		 );

$styleArray = array(
		    'font' => array(
				    'bold' => true,
				    'color'=>array('argb'=>'FFFFFF'),
				    ),
		    'alignment' => array(
					 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					 ),
		    'borders' => array(
				       'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_THIN,
						      'color'=>array('argb'=>'10145B'),
						      ),
				       ),
		    'fill' => array(
				    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				    'rotation' => 90,
				    'startcolor' => array(
							  'argb' => '10145B',
							  ),
				    'endcolor' => array(
							'argb' => 'FFFFFF',
							),
				    ),
		    );

$stylebodyArray = array(
		    'font' => array(
				    'bold' => false,
				    'color'=>array('argb'=>'000000'),
				    ),
		    'borders' => array(
				       'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_THIN,
						      'color'=>array('argb'=>'000000'),
						      ),
				       ),
		    'fill' => array(
				    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				    'rotation' => 90,
				    'startcolor' => array(
							  'argb' => 'E6E1EF',
							  ),
				    'endcolor' => array(
							'argb' => 'B6BBC1',
							),
				    ),
		    );

$styletotalArray = array(
		    'font' => array(
				    'bold' => true,
				    'color'=>array('argb'=>'000000'),
				    ),
		    'borders' => array(
				       'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_THIN,
						      'color'=>array('argb'=>'000000'),
						      ),
				       ),
		    'fill' => array(
				    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				    'rotation' => 90,
				    'startcolor' => array('argb' => '259e39',
							  ),
				    'endcolor' => array('argb' => 'FFFFFF',
							),
				    ),
		    );
$styleArrayMeta = array(
			'font' => array(
					'bold' => false,
					'color'=>array('argb'=>'000000'),
					),
			'alignment' => array(
					     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
					     ),
			'borders' => array(
					   'allborders' => array(
								 'style' => PHPExcel_Style_Border::BORDER_THIN,
								 'color'=>array('argb'=>'371AA3'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => 'A0A5FF',
							      ),
					'endcolor' => array(
							    'argb' => 'FFFFFF',
							    ),
					),
		    );
$styleArrayMetar = array(
			'font' => array(
					'bold' => false,
					'color'=>array('argb'=>'000000'),
					),
			'alignment' => array(
					     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
					     ),
			'borders' => array(
					   'allborders' => array(
								 'style' => PHPExcel_Style_Border::BORDER_THIN,
								 'color'=>array('argb'=>'371AA3'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => 'A0A5FF',
							      ),
					'endcolor' => array(
							    'argb' => 'FFFFFF',
							    ),
					),
		    );

?>