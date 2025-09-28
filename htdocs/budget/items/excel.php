<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

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

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("creater");
$objPHPExcel->getProperties()->setLastModifiedBy("Middle field");
$objPHPExcel->getProperties()->setSubject("Subject");
$objWorkSheet = $objPHPExcel->createSheet();
$work_sheet_count=3;
//number of sheets you want to create
$work_sheet=0;
$col=1;
$row=1;
$i=1;
while($work_sheet<=$work_sheet_count)
{
	if ($work_sheet==0)
	{
        $objWorkSheet->setTitle("Worksheet$work_sheet");
        $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'SR No. In sheet '.$work_sheet)->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValueByColumnAndRow($col++, $row++, $i++);
        //setting value by column and row indexes if needed
	}
	if ($work_sheet==1)
	{
        $objWorkSheet->setTitle("Worksheet$work_sheet");
        $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'SR No. In sheet '.$work_sheet)->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValueByColumnAndRow($col++, $row++, $i++);
        //setting value by column and row indexes if needed
	}
	if ($work_sheet==2)
	{
        $objWorkSheet->setTitle("Worksheet$work_sheet");
        $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'SR No. In sheet '.$work_sheet)->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValueByColumnAndRow($col++, $row++, $i++);
        //setting value by column and row indexes if needed
	}
	$work_sheet++;
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->setPreCalculateFormulas(true);
//$objWriter->save("excel/ReportPOA.xlsx");
$dir = $conf->budget->dir_output.'/tmp';

$file = 'priceunits.xlsx';
$objWriter->save($dir.'/'.$file);
header("Location: ".DOL_URL_ROOT.'/budget/items/fiche_export.php?archive='.$file);

?>
