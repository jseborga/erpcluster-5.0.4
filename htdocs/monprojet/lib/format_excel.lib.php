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
$styleArrayTitle = array(
		    'font' => array(
				    'bold' => true,
				    'size' => 9,
				    'color'=>array('argb'=>'000000'),
				    ),
		    'alignment' => array(
					 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
							  'argb' => '8D91D3',
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
$styleLines = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
							     'color'=>array('argb'=>'000000'),
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

//para cabecera central
$styleHead = array(
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


//para cabecera Left
$styleHeadLeft = array(
		       'font' => array(
				       'bold' => true,
				       'color'=>array('argb'=>'FFFFFF'),
				       ),
		       'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
//para cabecera Right
$styleHeadRight = array(
		       'font' => array(
				       'bold' => true,
				       'color'=>array('argb'=>'FFFFFF'),
				       ),
		       'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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
//para bordes
$styleBorder = array(
		       'borders' => array(
					  'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								'color'=>array('argb'=>'10145B'),
								),
					  ),
		       );

//para cabecera Right
$styleRight = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,),);
//para cabecera Left
$styleLeft = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),);
//para cabecera Left
$styleCenter = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),);
//para numerosRight
$stylenumber = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,),);

//para grupos
$styleArrayGroup = array(
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
								 'color'=>array('argb'=>'000000'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => '1792a4',
							      ),
					'endcolor' => array(
							    'argb' => '5ebdb2',
							    ),
					),
			);
$styleArrayGroupn = array(
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
								 'color'=>array('argb'=>'000000'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => '1792a4',
							      ),
					'endcolor' => array(
							    'argb' => '5ebdb2',
							    ),
					),
			);

//para impares
$styleArrayImpar = array(
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
								 'color'=>array('argb'=>'000000'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => 'FFFFF0',
							      ),
					'endcolor' => array(
							    'argb' => 'FFFFFF',
							    ),
					),
			);
$styleArrayImparn = array(
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
								 'color'=>array('argb'=>'000000'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => 'FFFFF0',
							      ),
					'endcolor' => array(
							    'argb' => 'FFFFFF',
							    ),
					),
			);
$styleArrayParx = array(
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

$styleArrayPar = array(
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
								 'color'=>array('argb'=>'000000'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => 'E8FFFF',
							      ),
					'endcolor' => array(
							    'argb' => 'FFFFFF',
							    ),
					),
			);
$styleArrayParn = array(
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
								 'color'=>array('argb'=>'000000'),
								 ),
					   ),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
							      'argb' => 'E8FFFF',
							      ),
					'endcolor' => array(
							    'argb' => 'FFFFFF',
							    ),
					),
			);

?>