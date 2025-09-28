<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *   	\file       /typemarking_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-12 08:48
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("assistance@assistance");

$aDatareport = $_SESSION['aReport'];
$aReport = $aDatareport['aReport'];
$cColumn = 'E';
$nColumn = 1;
$aTitle = array(1=>'E',2=>'F',3=>'G',4=>'H',5=>'I',6=>'J',7=>'K',8=>'L',9=>'M',10=>'N');

if (count($aReport)>0)
{
			//excel
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
	include DOL_DOCUMENT_ROOT.'/repbank/lib/format_excel.lib.php';


		    //PRCESO 1
	$objPHPExcel = new PHPExcel();

		    //cabecera
	$objPHPExcel->setActiveSheetIndex(0);
			//titulo
	$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('Type'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B1',$aDatareport['title']);
	$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Dateini'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B2',dol_print_date($aDatareport['dateini'],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('Datefin'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aDatareport['datefin'],'day'));
	//encabezado
	$objPHPExcel->getActiveSheet()->SetCellValue('A6',$langs->trans('Name'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B6',$langs->trans('Day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C6',$langs->trans('Date'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D6',$langs->trans('Mark'));
	foreach ((array) $_SESSION['aReport']['titlemark'] AS $i => $label)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$i].'6',$label);
		$cColumn = $aTitle[$i];
		$nColumn = $i;
	}
	//armamos columna de atrasos
	$nColumn++;
	$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$nColumn].'6',$langs->trans('Atrasos'));

	$objPHPExcel->getActiveSheet()->getStyle('A6:'.$aTitle[$nColumn].'6')->applyFromArray($styleArray);
	$row = 7;

	$row++;
	$var = false;
	foreach ((array) $aReport AS $fk_type => $aData)
	{
		$nSumaatraso = 0;
		$nSumaanticipo = 0;
		foreach ($aData AS $fk_member => $aDate)
		{
			foreach ($aDate AS $date =>$lines)
			{
				$cname = $lines['name'];
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$lines['name']);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$lines['weekday']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$lines['date']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$lines['mark']);
				//recorremos las marcaciones
				$nSumaatr = 0;
				$timeretraso = '';
				foreach ((array) $lines['reg'] AS $j => $aMark)
				{
					$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$j].$row,$aMark['time']);
					$nSumaatraso+= $aMark['retr'];
					$nSumaanticipo+= $aMark['anti'];
					$nSumaatr+= $aMark['retr'];
				}

				//columna atrasos
				if ($nSumaatr >60)
				{
					$resto = $nSumaatr % 60;
					if (strlen($resto) == 1) $resto = '0'.$resto;
					$entero = floor($nSumaatr/60);
					if (strlen($entero) == 1) $entero = '0'.$entero;
					$timeretraso = $entero.':'.$resto;

				}
				else
				{
					$timeretraso = $nSumaatr;
					if (strlen($nSumaatr) == 1) $timeretraso = '0'.$nSumaatr;
					$timeretraso = '00:'.$timeretraso;
				}
				if ($lines['wday'] == 0 || $lines['wday'] == 6)
				{
					if (empty($lines['mark']))
					$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$nColumn].$row,$langs->trans('No laboral'));
				}
				else
					$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$nColumn].$row,$timeretraso);

				$row++;
			}
			//imprimimos totals por member
			//calculamos el atraso en horas minutos
			if ($nSumaatraso >60)
			{
				$resto = $nSumaatraso % 60;
				if (strlen($resto) == 1)
					$resto = '0'.$resto;
				$entero = floor($nSumaatraso/60);
				if (strlen($entero) == 1)
					$entero = '0'.$entero;
				$timeretraso = $entero.':'.$resto;

			}
			else
			{
				if (strlen($nSumaatraso) == 1)
					$timeretraso = '0'.$nSumaatraso;
				$timeretraso = '00:'.$timeretraso;
			}
			if ($nSumaanticipo >= 60)
			{
				$resto = $nSumaanticipo % 60;
				if (strlen($resto) == 1)
					$resto = '0'.$resto;
				$entero = floor($nSumaanticipo/60);
				if (strlen($entero) == 1)
					$entero = '0'.$entero;

				$timeanticipo = $entero.':'.$resto;
			}
			else
			{
				if (strlen($nSumaanticipo)==1)
					$timeanticipo = '0'.$nSumaanticipo;
				$timeanticipo = '00:'.$timeanticipo;
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,'total '.$cname);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Atrasos');
			$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$nColumn].$row,$nSumaatraso);
			//$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,'Antes de hora');
			//$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$timeanticipo);
			$row++;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,'total '.$cname);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Salidas antes de hora');
			$objPHPExcel->getActiveSheet()->SetCellValue($aTitle[$nColumn].$row,$nSumaanticipo);
			//$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,'Antes de hora');
			//$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$nSumaanticipo);
			$row++;
		}
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	foreach ((array) $_SESSION['aReport']['titlemark'] AS $i => $label)
	{
		$objPHPExcel->getActiveSheet()->getColumnDimension($aTitle[$i])->setAutoSize(true);
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$docexport = 'export.xlsx';
	$objWriter->save("excel/".$docexport);
	header('Location: '.DOL_URL_ROOT.'/assistance/fiche_export.php?doc='.$docexport);
}

?>
