<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/tasks.php
 *	\ingroup    projet
 *	\brief      List all tasks of a project
 */

require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
//budget
if ($conf->budget->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';

$langs->load("users");
$langs->load("projects");
$langs->load('monprojet@monprojet');

$action = GETPOST('action', 'alpha');
$subaction = GETPOST('subaction', 'alpha');
if (empty($subaction)) $subaction='monthly';
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$cancel=GETPOST('cancel');
$newsel=GETPOST('newsel', 'int');

$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);

$table = 'llx_projet_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object = new Projectext($db);
$objectadd = new Projectext($db);
$objecttime = new Projettasktimedoc($db); //regisro de avances
$taskstatic = new Task($db);
$taskadd = new Taskext($db); //nueva clase para listar tareas
$objuser = new User($db);
//$cunits = new Cunits($db);

$extrafields_project = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
if ($conf->budget->enabled)
	$items = new Items($db);

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

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


if ($id > 0 || ! empty($ref))
{
	// fetch optionals attributes and labels
	$extralabels_projet=$extrafields_project->fetch_name_optionals_label($object->table_element);
}
$extralabels_task=$extrafields_task->fetch_name_optionals_label($taskstatic->table_element);
// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->monprojet->task->crear)
	$result = restrictedArea($user, 'projet', $id);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projecttaskcard','globalcard','formObjectOptions'));

$progress=GETPOST('progress', 'int');
$label=GETPOST('label', 'alpha');
$description=GETPOST('description');
$planned_workloadhour =GETPOST('planned_workloadhour');
$planned_workloadmin = GETPOST('planned_workloadmin');
if (empty($planned_workloadhour)) $planned_workloadhour=0;
if (empty($planned_workloadmin)) $planned_workloadmin=0;
$planned_workload=$planned_workloadhour*3600+$planned_workloadmin*60;

$userAccess=0;
$lDisabled = false;


/*
 * Actions
 */

if ($user->rights->monprojet->task->crear)
	$userWrite = true;

if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aLineas']);
	$aExcel   = unserialize($_SESSION['aExcel']);

	$aValores = unserialize($_SESSION['aValores']);

	$aHead = array( 1=>"A",2=>"B",3=>"C",4=>"D",5=>"E", 6=>"F",7=>"G",8=>"H",9=>"I",10=>"J",11=>"K",12=>"L",13=>"M",14=>"N",15=>"O",16=>"P",17=>"Q",18=>"R",19=>"S",20=>"T",21=>"U",22=>"V",23=>"W",24=>"X",25=>"Y",26=>"Z");
		//$aHead = array( 27=>"AF",28=>"AG",29=>"AH",30=>"AI",31=>"AJ",32=>"AK",33=>"AL",34=>"AM",35=>"AN",36=>"AO",37=>"AP",38=>"AQ",39=>"AR",40=>"AS",41=>"AT",42=>"AU",43=>"AV",44=>"AW",45=>"AX",46=>"AY",47=>"AZ");


	for ($i=65; $i <=90 ; $i++) {
		for ($j=65; $j <= 90 ; $j++) {
			$aHead[] = chr($i).chr($j);
		}
	}




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

		//Encabezados

	$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Proyect'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B2',$aValores['ref']);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:E2');
	$objPHPExcel->getActiveSheet()->SetCellValue('A3',html_entity_decode($langs->trans('Ref')));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',$aValores['ref']);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:E3');

	$objPHPExcel->getActiveSheet()->SetCellValue('A4',html_entity_decode($langs->trans('label')));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',$aValores['label']);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:E4');


	$line = 6;
			//CABECERAS DE LA TABLA

	foreach ($aExcel as $ind => $valor) {
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$ind].$line,$valor);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':'.$aHead[$ind].$line)->applyFromArray(
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
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);


		//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
		//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$idGr = 1;
	$line = 7;
	$lineini = 7;
	$bold = false;
	$estiloCeldas = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	//$line--;

	foreach ((array) $aReporte as $j => $row)
	{
		//echo '<hr>'.$j;
		//print_r($row);
		if(count($row['q'])>0)
		{
			$bold = true;
		}else{
			$bold = false;
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['c']);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->getFont()->setBold($bold);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($row['d'],'day'));
		$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->applyFromArray($estiloCeldas);
					//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,dol_print_date($row['e'],'day'));
		//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getFont()->setBold($bold);
					//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['f']);
		//$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getFont()->setBold($bold);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['g']);
		//$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->applyFromArray($estiloCeldas);

		$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getFont()->setBold($bold);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead = 6;
		foreach ($row['h'] as $y => $e) {
			$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$e);
			$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
			//$objPHPExcel->getActiveSheet()->getStyle(''.$line)->applyFromArray($estiloCeldas);

			$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$indHead++;
		}

		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['i']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['j']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['k']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['l']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['m']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['n']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['o']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$indHead++;
		$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$row['p']);
		$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getFont()->setBold($bold);
		//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
		$aColend = $aHead[$indHead];
		if(count($row['q'])>0){
			$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':'.$aHead[$ind].$line)->applyFromArray(
				array(
					'font'    => array(
						'bold'      => true
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':'.$aColend.$line)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$line++;

		if(count($row['q'])>0)
		{
			foreach ( $row['q'] as $i => $rw)
			{

				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$rw['c']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->applyFromArray($estiloCeldas);

				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($rw['d'],'day'));
				//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->applyFromArray($estiloCeldas);

								//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,dol_print_date($rw['e'],'day'));
				//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->applyFromArray($estiloCeldas);

								//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$rw['f']);
				//$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->applyFromArray($estiloCeldas);

				$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$rw['g']);
				//$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->applyFromArray($estiloCeldas);

				$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead = 6;
				foreach ($rw['h'] as $y => $e) {
					$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$e);
					//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
					$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$indHead++;
				}

				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['i']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['j']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['k']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['l']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['m']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['n']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['o']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
				$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$indHead++;
				$objPHPExcel->getActiveSheet()->SetCellValue($aHead[$indHead].$line,$rw['p']);
				//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->applyFromArray($estiloCeldas);
								//$objPHPExcel->getActiveSheet()->getStyle($aHead[$indHead].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':'.$aColend.$line)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


				$line++;
			}
		}
	}




	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			//$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/taskresp.xlsx");
	$_SESSION['docsave'] =  "taskresp.xlsx";
	header('Location: '.DOL_URL_ROOT.'/monprojet/fiche_export.php?archive=taskresp.xlsx');
}


/*
 * View
 */

// Example : Adding jquery code

$form=new Form($db);
if ($conf->budget->enabled)
	$formadd = new FormAdd($db);
$formother=new FormOther($db);
$taskstatic = new Task($db);
$userstatic=new User($db);

$title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
$aCss = array('monprojet/bootstrap/css/bootstrap.min.css','monprojet/dist/css/AdminLTE.min.css','monprojet/dist/css/skins/_all-skins.min.css');
$aCss = array('monprojet/bootstrap/css/bootstrap.min.css');
$aJse = array();
//llxHeader("",$ittle,$help_url,'','','',$aJs,$aCss);
llxHeader("",$title,$help_url);
if ($id > 0 || ! empty($ref))
{
	$object->fetch($id, $ref);
	$objectadd->fetch($id, $ref);
	$object->fetch_thirdparty();
	$res=$object->fetch_optionals($object->id,$extralabels_projet);
	$res=$objectadd->fetch_optionals($objectadd->id,$extralabels_projet);


	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $objectadd->restrictedProjectAreaadd($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab=GETPOST('tab')?GETPOST('tab'):'tasksrep';

	$head=project_prepare_head($object);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($object->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	$aValores = array();

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $object->getProjectsAuthorizedForUser($user,$mine,0);
		$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	$aValores['ref'] = $object->ref;
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->title.'</td></tr>';
	$aValores['label'] = $object->title;

	// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	// if (! empty($object->thirdparty->id)) print $object->thirdparty->getNomUrl(1);
	// else print '&nbsp;';
	// print '</td>';
	// print '</tr>';

	// // Visibility
	// print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
	// if ($object->public) print $langs->trans('SharedProject');
	// else print $langs->trans('PrivateProject');
	// print '</td></tr>';

	// // Statut
	// print '<tr><td>'.$langs->trans("Status").'</td><td>'.$object->getLibStatut(4).'</td></tr>';

	// // Date start
	// print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	// print dol_print_date($object->date_start,'day');
	// print '</td></tr>';

	// // Date end
	// print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	// print dol_print_date($object->date_end,'day');
	// print '</td></tr>';

	// Other options
	//$parameters=array();
	//$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook
	//if (empty($reshook) && ! empty($extrafields_project->attribute_label))
	//{
	//	print $object->showOptionals($extrafields_project);
	//}

	print '</table>';

	dol_fiche_end();
	/*
	 * Fiche projet en mode visu
	 */

	/*
	 * Actions
	 */
	print '<div class="tabsAction">';

	if ($user->rights->projet->all->creer || $user->rights->projet->creer || $user->rights->monprojet->task->crear)
	{
		if ($user->rights->monprojet->task->crear)
			$userWrite = true;
		if ($object->public || $userWrite > 0 && $action != 'createup')
		{
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&subaction=monthly&action=graphic'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Graphic').'</a>';

			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&subaction=daily'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Daily').'</a>';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&subaction=weekly'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Weekly').'</a>';
			// print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&subaction=fortnightly'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Quincenal').'</a>';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&subaction=monthly'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Monthly').'</a>';
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&subaction=annual'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Annual').'</a>';
		}
		else
		{
			print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerOfProject").'">'.$langs->trans('AddTask').'</a>';
		}
	}
	else
	{
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotEnoughPermissions").'">'.$langs->trans('AddTask').'</a>';
	}

	print '</div>';

	print '<br>';


	// Link to switch in "my task" / "all task"
	if ($mode == 'mine')
	{
		print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans("DoNotShowMyTasksOnly").'</a>';
		//print ' - ';
		//print $langs->trans("ShowMyTaskOnly");
	}
	else
	{
		//print $langs->trans("DoNotShowMyTaskOnly");
		//print ' - ';
		print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&mode=mine">'.$langs->trans("ShowMyTasksOnly").'</a>';
	}
	print '</td></tr></table>';

	// Get list of tasks in tasksarray and taskarrayfiltered
	// We need all tasks (even not limited to a user because a task to user can have a parent that is not affected to him).
	//$tasksarray=$taskstatic->getTasksArray(0, 0, $object->id, $socid, 0);
	$tasksarray=$taskadd->getTasksArray(0, 0, $object->id, $socid, 0);
	// We load also tasks limited to a particular user
	$tasksrole=($mode=='mine' ? $taskstatic->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
	//var_dump($tasksarray);
	//var_dump($tasksrole);

	if (! empty($conf->use_javascript_ajax))
	{
		include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
	}

	//se debe armar de acuerdo al criterio
	if ($taskadd->date_ini>0)
	{
		$aDateini = dol_getdate($taskadd->date_ini);
		$aDatefin = dol_getdate($taskadd->date_end);
	}
	else
	{
		$aDateini = dol_getdate($object->date_start);
		$aDatefin = dol_getdate($object->date_end);
	}

	$aLoop = array();
	$aDias = array();
	if ($subaction == 'daily')
	{
		// echo '<hr>day '.$aDateini['mday'].','.$aDateini['mon'].','.$aDateini['year'];
		$aX = dol_get_prev_day($aDateini['mday'],$aDateini['mon'],$aDateini['year']);
		//	    print_r($aX);
		$x = dol_stringtotime($aX['year'].''.(strlen($aX['month'])==1?'0'.$aX['month']:$aX['month']).''.(strlen($aX['day'])==1?'0'.$aX['day']:$aX['day']). '120000');
		//echo '<hr>init '.$x.' '.dol_print_date($x,'day');
		$j = 1;
		$lLoop = true;
		while ($lLoop == true)
		{
			//echo '<hr>inicia '.$x.' '.dol_print_date($x,'day');
			$aDate = dol_getdate($x);
			$aLoop[$aDate['year']][$x] = array($aDate['mday'],$aDate['mday']);
			//print_r($aDate);
			//print '<td align="center">'.$aDate['mday'].'</td>';
			$aZ = dol_get_next_day($aDate['mday'],$aDate['mon'],$aDate['year']);
			$x = dol_stringtotime($aZ['year'].''.(strlen($aZ['month'])==1?'0'.$aZ['month']:$aZ['month']).''.(strlen($aZ['day'])==1?'0'.$aZ['day']:$aZ['day']). '120000');
			if ($x > $taskadd->date_end) $lLoop = false;
		}
		//en forma diaria
	}
	//en forma semanal
	if ($subaction == 'weekly')
	{
		$aLoop = array();
		list($semana,$primerDia,$ultimoDia) = weekinifin($aDateini['year'],$aDateini['mon'],$aDateini['mday']);
		$x1 = dol_stringtotime($primerDia);
		$x2 = dol_stringtotime($ultimoDia);
		$aDate1 = dol_getdate($x1);
		$aDate2 = dol_getdate($x2);
		$aLoop[$aDate2['year']][$semana] = array($x1,$x2);
		//print '<td align="center">'.$semana.'-'.$aDate1['year'].'</td>';

		$j = 1;
		$lLoop = true;
		while ($lLoop == true)
		{
			$aDate1 = dol_getdate($x1);
			$aDate2 = dol_getdate($x2);
			// list($semana,$primerDia,$ultimoDia) = weekinifin($aDate2['year'],$aDate2['mon'],$aDate2['mday']);
			// $x1 = dol_stringtotime($primerDia);
			// $x2 = dol_stringtotime($ultimoDia);


			$aZ = dol_get_next_day($aDate2['mday'],$aDate2['mon'],$aDate2['year']);
			list($semana,$primerDia,$ultimoDia) = weekinifin($aZ['year'],(strlen($aZ['month'])==1?'0'.$aZ['month']:$aZ['month']),(strlen($aZ['day'])==1?'0'.$aZ['day']:$aZ['day']) );
			$x1 = dol_stringtotime($primerDia);
			$x2 = dol_stringtotime($ultimoDia);
			//echo '<hr>resx '.$semana.','.$primerDia.','.$ultimoDia;
			//echo '<hr>inicia '.$x1.' '.dol_print_date($x1,'day');
			$aLoop[$aDate2['year']][$semana] = array($x1,$x2);
			//print '<td align="center">'.$semana.'-'.$aDate1['year'].'</td>';

			$j++;
			//		if ($j > 10) exit;
			if ($x1 >= $taskadd->date_end) $lLoop = false;
		}
	}
	//en forma mensual
	if ($subaction == 'monthly')
	{
		$aLoop = array();
		if ($taskadd->date_ini>0)
		{
			$x1 = $taskadd->date_ini;
			$x2 = $taskadd->date_end;
		}
		else
		{
			$x1 = $object->date_start;
			$x2 = $object->date_end;
		}
		$aDate1 = dol_getdate($x1);
		$aDate2 = dol_getdate($x2);
		//echo '<hr>resinicial  '.$semana.','.$primerDia.' '.dol_print_date($x1,'day').','.$ultimoDia.' '.dol_print_date($x2,'day');
		$j = 1;
		$lLoop = true;
		$aDate1 = dol_getdate($x1);
		$aLoop[$aDate1['year']][$aDate1['mon']][0] = $aDate1['mon'];
		//print '<td align="center">'.$aDate1['mon'].'-'.$aDate1['year'].'</td>';
		while ($lLoop == true)
		{
			$aDate1 = dol_getdate($x1);
			//siguiente mes

			$aNew = dol_get_next_month($aDate1['mon'],$aDate1['year']);
				//echo '<hr>res '.$aNew['year'].'|'.$aNew['month'].'|'.'01120000';
			//print '<td align="center">'.$aNew['month'].'-'.$aNew['year'].'</td>';
			$aLoop[$aNew['year']][$aNew['month']][0] = $aNew['month'];
			//$x1 = dol_stringtotime($aNew['year'].$aNew['month'].'01T120000');
			$x1 = dol_stringtotime('01/'.$aNew['month'].'/'.$aNew['year'].' 12:00:00');
			// $j++;
			// if ($j > 10) exit;
			//echo '<hr>resss '.$x1 .' >= '.$taskadd->date_end;
			//echo '<hr>'.dol_print_date($x1,'day') .' >= '.dol_print_date($taskadd->date_end,'day');
			if ($x1 >= $taskadd->date_end) $lLoop = false;
		}
	}
	//en forma anual
	if ($subaction == 'annual')
	{
		$aLoop = array();
		$x1 = $taskadd->date_ini;
		$j = 1;
		$lLoop = true;
		$aDate1 = dol_getdate($x1);
		$yearini = $aDateini['year'];
		$yearfin = $aDatefin['year'];
		$year = $aDate1['year'];

		$aLoop[$year][$year][0] = $year;
		//print '<td align="center">'.$year.'</td>';
		while ($lLoop == true)
		{
			$year++;
			//print '<td align="center">'.$year.'</td>';
			$aLoop[$year][$year][0] = $year;
			if ($year >= $yearfin) $lLoop = false;
		}
	}

	print '<div class="fichecenter">';
	if ($action != 'graphic')
	{

		//Array para la cabecera del Excel
		$aExcel = array();
		//armamos la cabecera
		print '<table id="tablelines" class="border" width="100%">';
		print '<tr class="liste_titre nodrag nodrop">';
		// print '<td>'.$langs->trans("Project").'</td>';
		//print '<td width="100">'.$langs->trans("RefTask").'</td>';
		print '<td>'.$langs->trans("LabelTask").'</td>';
		$aExcel[1] = html_entity_decode($langs->trans("LabelTask"));
		print '<td align="center">'.$langs->trans("DateStart").'</td>';
		$aExcel[2] = html_entity_decode($langs->trans("DateStart"));
		print '<td align="center">'.$langs->trans("DateEnd").'</td>';
		$aExcel[3] = html_entity_decode($langs->trans("DateEnd"));
		print '<td align="right">'.$langs->trans("Unit").'</td>';
		$aExcel[4] = html_entity_decode($langs->trans("Unit"));
		if ($user->rights->monprojet->task->leerm){
			$aExcel[5] =html_entity_decode($langs->trans("Unitary"));
			print '<td align="right">'.$langs->trans("Unitary").'</td>';
		}

		//en forma diaria
		if ($subaction == 'daily')
		{
			$aX = dol_get_prev_day($aDateini['mday'],$aDateini['mon'],$aDateini['year']);
			$x = dol_stringtotime($aX['year'].''.(strlen($aX['month'])==1?'0'.$aX['month']:$aX['month']).''.(strlen($aX['day'])==1?'0'.$aX['day']:$aX['day']). '120000');
			$j = 1;
			$lLoop = true;
			while ($lLoop == true)
			{
				$aDate = dol_getdate($x);
				$aExcel[] = html_entity_decode($aDate['mday']);
				print '<td align="center">'.$aDate['mday'].'</td>';
				$aZ = dol_get_next_day($aDate['mday'],$aDate['mon'],$aDate['year']);
				$x = dol_stringtotime($aZ['year'].''.(strlen($aZ['month'])==1?'0'.$aZ['month']:$aZ['month']).''.(strlen($aZ['day'])==1?'0'.$aZ['day']:$aZ['day']). '120000');
				if ($x > $taskadd->date_end) $lLoop = false;
			}
		}
		//en forma semanal
		if ($subaction == 'weekly')
		{
			//	    	$aLoop = array();
			list($semana,$primerDia,$ultimoDia) = weekinifin($aDateini['year'],$aDateini['mon'],$aDateini['mday']);
			$x1 = dol_stringtotime($primerDia);
			$x2 = dol_stringtotime($ultimoDia);
			$aDate1 = dol_getdate($x1);
			$aDate2 = dol_getdate($x2);
			//	    	$aLoop[$aDate2['year']][$semana] = array($x1,$x2);
			print '<td align="center">'.$semana.'-'.$aDate1['year'].'</td>';
			$aExcel[] = html_entity_decode($semana.'-'.$aDate1['year']);
			$j = 1;
			$lLoop = true;
			while ($lLoop == true)
			{
				$aDate1 = dol_getdate($x1);
				$aDate2 = dol_getdate($x2);
				$aZ = dol_get_next_day($aDate2['mday'],$aDate2['mon'],$aDate2['year']);
				list($semana,$primerDia,$ultimoDia) = weekinifin($aZ['year'],(strlen($aZ['month'])==1?'0'.$aZ['month']:$aZ['month']),(strlen($aZ['day'])==1?'0'.$aZ['day']:$aZ['day']) );
				$x1 = dol_stringtotime($primerDia);
				$x2 = dol_stringtotime($ultimoDia);
				//$aLoop[$aDate2['year']][$semana] = array($x1,$x2);
				print '<td align="center">'.$semana.'-'.$aDate1['year'].'</td>';
				$aExcel[] = html_entity_decode($semana.'-'.$aDate1['year']);
				$j++;
				if ($x1 >= $taskadd->date_end) $lLoop = false;
			}
		}
		//en forma mensual
		if ($subaction == 'monthly')
		{
			//	    	$aLoop = array();
			$x1 = $taskadd->date_ini;
			$x2 = $taskadd->date_end;
			$aDate1 = dol_getdate($x1);
			$aDate2 = dol_getdate($x2);
			$j = 1;
			$lLoop = true;
			$aDate1 = dol_getdate($x1);
			//	    	$aLoop[$aDate1['year']][$aDate1['mon']][0] = $aDate1['mon'];
			print '<td align="center">'.$aDate1['mon'].'-'.$aDate1['year'].'</td>';
			$aExcel[] = html_entity_decode($aDate1['mon'].'-'.$aDate1['year']);
			while ($lLoop == true)
			{
				$aDate1 = dol_getdate($x1);
				//siguiente mes
				$aNew = dol_get_next_month($aDate1['mon'],$aDate1['year']);
				print '<td align="center">'.$aNew['month'].'-'.$aNew['year'].'</td>';
				$aExcel[] = html_entity_decode($aNew['month'].'-'.$aNew['year']);
				$aLoop[$aNew['year']][$aNew['month']][0] = $aNew['month'];
				$x1 = dol_stringtotime('01/'.$aNew['month'].'/'.$aNew['year'].' 12:00:00');
				if ($x1 >= $taskadd->date_end) $lLoop = false;
			}
		}
		//en forma anual
		if ($subaction == 'annual')
		{
			//		    $aLoop = array();
			$x1 = $taskadd->date_ini;
			$j = 1;
			$lLoop = true;
			$aDate1 = dol_getdate($x1);
			$yearini = $aDateini['year'];
			$yearfin = $aDatefin['year'];
			$year = $aDate1['year'];

			//	    	$aLoop[$year][$year][0] = $year;
			print '<td align="center">'.$year.'</td>';
			$aExcel[] = html_entity_decode($year);
			while ($lLoop == true)
			{
				$year++;
				print '<td align="center">'.$year.'</td>';
				$aExcel[] = html_entity_decode($year);
				$aLoop[$year][$year][0] = $year;
				if ($year >= $yearfin) $lLoop = false;
			}
		}

		print '<td align="right">'.$langs->trans("Planned").'</td>';
		$aExcel[] = html_entity_decode($langs->trans("Planned"));
		print '<td align="right">'.$langs->trans("Declared").'</td>';
		$aExcel[] = html_entity_decode($langs->trans("Declared"));
		print '<td align="right">'.$langs->trans("AmountPlaned").'</td>';
		$aExcel[] = html_entity_decode($langs->trans("AmountPlaned"));
		print '<td align="right">'.$langs->trans("AmountDeclared").'</td>';
		$aExcel[] = html_entity_decode($langs->trans("AmountDeclared"));
		print '<td align="right">'.'<a href="#" title="'.$langs->trans('Costperformanceindex').'" class="classfortooltip">'.$langs->trans("CPI").'</a>'.'</td>';
		$aExcel[] = html_entity_decode($langs->trans('Costperformanceindex'));
		print '<td align="right">'.'<a href="#" title="'.$langs->trans('Scheduleperformanceindex').'" class="classfortooltip">'.$langs->trans("SPI").'</a>'.'</td>';
		$aExcel[] = html_entity_decode($langs->trans('Scheduleperformanceindex'));
		print '<td align="right">'.'<a href="#" title="'.$langs->trans('Costandschedule').'" class="classfortooltip">'.$langs->trans("CSI").'</a>'.'</td>';
		$aExcel[] = html_entity_decode($langs->trans('Costandschedule'));
		print '<td align="right">'.$langs->trans("Statut").'</td>';
		$aExcel[] = html_entity_decode($langs->trans("Statut"));
		print '<td>&nbsp;</td>';
		print "</tr>\n";

		/****************** REPORTE EXCEL *************************/
		if (count($tasksarray) > 1)
		{
			// Show all lines in taskarray (recursive function to go down on tree)
			$j=0; $level=0;
			$nboftaskshown=monprojectLineejec($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$aLoop);
			$aLineas = monprojectLineejecLUIS($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$aLoop);
			//echo "<pre>";
			//print_r($aExcel);
			//$_SESSION['aLineas'] = serialize($aLineas);
			//echo "</pre>";
		}
		else
		{
			print '<tr '.$bc[false].'><td colspan="9">'.$langs->trans("NoTasks").'</td></tr>';
		}
		print "</table>";
		// Test if database is clean. If not we clean it.
		//print 'mode='.$_REQUEST["mode"].' $nboftaskshown='.$nboftaskshown.' count($tasksarray)='.count($tasksarray).' count($tasksrole)='.count($tasksrole).'<br>';
		if (! empty($user->rights->projet->all->lire))
		// We make test to clean only if user has permission to see all (test may report false positive otherwise)
		{
			if ($mode=='mine')
			{
				if ($nboftaskshown < count($tasksrole))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
					cleanCorruptedTree($db, 'projet_task', 'fk_task_parent');
				}
			}
			else
			{
				if ($nboftaskshown < count($tasksarray))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
					cleanCorruptedTree($db, 'projet_task', 'fk_task_parent');
				}
			}
		}
	}
	else
	{

		if (count($tasksarray) > 0)
		{
			$j=0; $level=0;
			$aLabel = array();
			$aPlan  = array();
			$aTask  = array();
			$aResult = monprojectLineejecarray($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$aLoop,$aLabel,$aPlan,$aTask);
		}

		$cn = ($subaction == 'monthly'?'m':($subaction=='dayly'?'d':($subaction=='weekly'?'w':'y')));
		foreach ($aLoop AS $y => $aDy)
		{
			foreach ($aDy AS $d => $data)
			{
				$aLabel[$cn][$y][$d] = $y.'_'.$d;
			}
		}
		//		$aLabel = $aResult['aLabel'][($subaction == 'monthly'?'m':($subaction=='dayly'?'d':($subaction=='weekly'?'w':'y')))];
		$cLabel = '';
		foreach ($aLabel[$cn] AS $y => $aDy)
		{
			foreach ($aDy AS $r => $val)
			{
				if (!empty($cLabel)) $cLabel .= ',';
				$cLabel.= '"'.$val.'"';
			}
		}
		$cPlan = '';
		$nPlan = 0;
		$j = 1;
		foreach ($aLabel[$cn] AS $y => $aDy)
		{
			foreach ($aDy AS $r => $val)
			{

				if ($j > 1) $cPlan .= ',';
				$nVal = $aResult['aPlan'][$cn][$y][$r] + 0;
				$nPlan += $aResult['aPlan'][$cn][$y][$r] + 0;
				$cPlan.= price2num($nPlan,'MT');
				$j++;
			}
		}
		$cTask = '';
		$nTask = 0;
		$j = 1;
		foreach ($aLabel[$cn] AS $y => $aDy)
		{
			foreach ($aDy AS $r => $val)
			{

				if ($j > 1) $cTask .= ',';
				$nVal = $aResult['aTask'][$cn][$y][$r] + 0;
				$nTask += $aResult['aTask'][$cn][$y][$r] + 0;
				$cTask.= price2num($nTask,'MT');
				$j++;
			}
		}

		//echo '<hr>listy '.$cY = implode(',',$aResult['aTask']);

		//impresion de grafico
		include 'chartjs.php';
	}
	print '</div>';
}

function monprojectLineejecLUIS(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$aLoop='')
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $taskadd;
	global $items, $subaction;

	$aLineasAux = array();

	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskaddext.class.php';

	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);

	// We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
			if ($lView)
			{
				if ($parent == 0) $level = 0;

			// Process line
			// print "i:".$i."-".$lines[$i]->fk_project.'<br>';
				if ($lines[$i]->fk_parent == $parent)
				{
					// Show task line.
					$showline=1;
					$showlineingray=0;
					// If there is filters to use
					if (is_array($taskrole))
					{
						// If task not legitimate to show, search if a legitimate task exists later in tree
						if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
						{
							// So search if task has a subtask legitimate to show
							$foundtaskforuserdeeper=0;
							searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
							//print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
							if ($foundtaskforuserdeeper > 0)
							{
								$showlineingray=1;		// We will show line but in gray
							}
							else
							{
								$showline=0;			// No reason to show line
							}
						}
					}
					else
					{
						// Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
						// or into all other projects if user has permission to).
						if (empty($user->rights->projet->all->lire))
						{
							// User is not allowed on this project and project is not public, so we hide line
							if (! in_array($lines[$i]->fk_project, $projectsArrayId))
							{
								// Note that having a user assigned to a task into a project user has no permission on, should not be possible
								// because assignement on task can be done only on contact of project.
								// If assignement was done and after, was removed from contact of project, then we can hide the line.
								$showline=0;
							}
						}
					}
					if ($showline)
					{
						// Break on a new project
						if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
						{
							$var = !$var;
							$lastprojectid=$lines[$i]->fk_project;
						}
						if ($lines[$i]->array_options['options_c_grupo'] == 1)
						{
							$lTask = false;
							//print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
						}
						else
							//print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

							if ($showproject)
							{
							// Project ref
							//print "<td>";
							if ($showlineingray) //print '<i>';
							$projectstatic->id=$lines[$i]->fk_project;
							$projectstatic->ref=$lines[$i]->projectref;
							$projectstatic->public=$lines[$i]->public;
							if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId)){
								//print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
								$aLineasAux[$i]['a'] = $lines[$i]->fk_project;
							}
							else{
								//print 'dddd';
								$aLineasAux[$i]['a'] = 'dddd';
							}

							if ($showlineingray) {}//print '</i>';
							//print "</td>";

							// Project status
							//print '<td>';
							$projectstatic->statut=$lines[$i]->projectstatus;
							//print $projectstatic->getLibStatut(2);
							$aLineasAux[$i]['b'] =  $projectstatic->getLibStatut(1);
							//print "</td>";
						}


						// Title of task
						//print "<td>";
						if ($showlineingray){} //print '<i>';
						else //print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1">';
						for ($k = 0 ; $k < $level ; $k++)
						{
							//print "&nbsp; &nbsp; &nbsp;";
						}
						//print $lines[$i]->label;
						$aLineasAux[$i]['c'] = $lines[$i]->label;
						if ($showlineingray){} //print '</i>';
						//elseprint '</a>';
						//print "</td>\n";

						// Date start
						//print '<td align="center">';
						//print dol_print_date($lines[$i]->date_start,'dayhour');
						$aLineasAux[$i]['d'] = $lines[$i]->date_start;
						//print '</td>';

						// Date end
						//print '<td align="center">';
						$aLineasAux[$i]['e'] = $lines[$i]->date_end;
						//print '</td>';

						//unit
						//print '<td align="center">';
						if (!$lines[$i]->array_options['options_c_grupo']){
							$aLineasAux[$i]['f'] = $lines[$i]->array_options['options_unit'];
						}

							//print $lines[$i]->array_options['options_unit'];
						//print '&nbsp;';
						//print '</td>';

						if ($user->rights->monprojet->task->leerm)
						{
							//print '<td align="right">';
							if (!$lines[$i]->array_options['options_c_grupo']){
								$aLineasAux[$i]['g'] = 	$lines[$i]->array_options['options_unit_amount'];
								//print price($lines[$i]->array_options['options_unit_amount']);
							}else{
								//print '&nbsp;';
								$aLineasAux[$i]['g'] ="";
							}

							//print '</td>';
						}

						//recuperamos los tiempos reportados
						$taskadd->getTimeSpent($lines[$i]->id);
						//armamos de acuerdo al tipo de recporte fecha
						$aTaskd = array();
						$aTaskw = array();
						$aTaskm = array();
						$aTasky = array();
						foreach ((array) $taskadd->lines AS $k => $linet)
						{
							//echo '<hr>line '.$linet->id.' == '.$lines[$i]->id;
							if ($linet->id == $lines[$i]->id)
							{
								$aZ = dol_getdate($linet->timespent_date);
								$x = dol_stringtotime($aZ['year'].''.(strlen($aZ['mon'])==1?'0'.$aZ['mon']:$aZ['mon']).''.(strlen($aZ['mday'])==1?'0'.$aZ['mday']:$aZ['mday']). '120000');
								//obtenemos la semana
								$aDatereg = dol_getdate($x);
								list($semana,$primerDia,$ultimoDia) = weekinifin($aDatereg['year'],$aDatereg['mon'],$aDatereg['mday']);
								$x1 = dol_stringtotime($primerDia);
								$x2 = dol_stringtotime($ultimoDia);

								//para diarios
								$aTaskd[$aDatereg['year']][$x]['value'] += $linet->unit_declared;
								$aTaskd[$aDatereg['year']][$x][0] = $x;
								//para semanal
								$aTaskw[$aDatereg['year']][$semana]['value'] += $linet->unit_declared;
								$aTaskw[$aDatereg['year']][$semana][0] = $x1;
								$aTaskw[$aDatereg['year']][$semana][1] = $x2;
								//para mensual
								$aTaskm[$aZ['year']][$aZ['mon']]['value'] += $linet->unit_declared;
								//para anual
								$aTasky[$aZ['year']][$aZ['year']]['value'] += $linet->unit_declared;
							}
						}


						//agregamos el tipo de reporte fecha
						$aDMASrep = array();
						foreach ($aLoop AS $year => $aDate)
						{
							foreach ($aDate AS $date => $aValue)
							{
								//print '<td align="center">';
								if ($subaction == 'daily'){
									//print $aTaskd[$year][$date]['value'];
									$aDMASrep[$date] = $aTaskd[$year][$date]['value'];
								}
								if ($subaction == 'weekly'){
									//print $aTaskw[$year][$date]['value'];
									$aDMASrep[$date] = $aTaskw[$year][$date]['value'];
								}
								if ($subaction == 'monthly'){
									//print $aTaskm[$year][$date]['value'];
									$aDMASrep[$date] = $aTaskm[$year][$date]['value'];
								}
								if ($subaction == 'annual'){
									//print $aTasky[$year][$date]['value'];
									$aDMASrep[$date] = $aTasky[$year][$date]['value'];
								}
								//print '</td>';
							}
						}

						$aLineasAux[$i]['h'] = $aDMASrep;
						unset($aDMASrep);

						$plannedworkloadoutputformat='allhourmin';
						$timespentoutputformat='allhourmin';
						if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
						if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;
						//RQC CAMBIADO
						// Planned Workload (in working hours)
						//print '<td align="right">';
						//buscamos el total programado
						//recuperamos la tarea
						$objtask1 = new Task($db);
						$objtask1add = new Projettaskaddext($db);

						$extrafield_task = new ExtraFields($db);
						$extralabel_task=$extrafield_task->fetch_name_optionals_label($objtask1->table_element);
						$objtask1->fetch($lines[$i]->id);
						$objtask1add->fetch(0,$lines[$i]->id);
						$objtask1->unit_declared = $objtask1add->unit_declared;
						$objtask1->unit_program = $objtask1add->unit_program;
						$objtask1->unit_amount = $objtask1add->unit_amount;
						$res=$objtask1->fetch_optionals($objtask1->id,$extralabel_task);
						//buscamos el item
						if ($conf->budget->enabled)
						{
							$items->fetch('',$objtask1->ref);
						}
						/*armamos variables para el calculo*/
						$datehoy = dol_now();
						list($lCalc,$PV,$EV,$AC,$CPI,$SPI,$CSI) = form_vgadd($objtask1,$extralabel_task,$datehoy);

						// $fullhour=convertSecondToTime($lines[$i]->planned_workload,$plannedworkloadoutputformat);
						// $workingdelay=convertSecondToTime($lines[$i]->planned_workload,'all',86400,7);	// TODO Replace 86400 and 7 to take account working hours per day and working day per weeks
						if ($lines[$i]->planned_workload != '')
						{
							if (!$lines[$i]->array_options['options_c_grupo'])
							//print $fullhour;
								//print $objtask1add->unit_program;
								$aLineasAux[$i]['i'] = $objtask1add->unit_program;

								//print $objtask1->array_options['options_unit_program'];
							// TODO Add delay taking account of working hours per day and working day per week
							//if ($workingdelay != $fullhour) print '<br>('.$workingdelay.')';
						}
						//else print '--:--';
						//print '</td>';

						// Progress declared
						// EJECUCION REPORTADA
						//print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo'])
							//print $objtask1add->unit_declared;
							$aLineasAux[$i]['j'] = $objtask1add->unit_declared;
							//print $objtask1->array_options['options_unit_declared'];
						//print '</td>';

						//amount planed
						//print '<td align="right">';
						$amountprog = $lines[$i]->array_options['options_unit_amount'] * $objtask1add->unit_program;
						//$amountprog = $lines[$i]->array_options['options_unit_amount'] * $objtask1->array_options['options_unit_program'];
						if (!$lines[$i]->array_options['options_c_grupo']){
							//print price(price2num($amountprog,'MT'));
							$aLineasAux[$i]['k'] = $amountprog;
						}

						//print '</td>';

						//amount declared
						//buscamos el item
						//print '<td align="right">';
						//$amountdec = $lines[$i]->array_options['options_unit_amount'] * $objtask1->array_options['options_unit_declared'];
						$amountdec = $lines[$i]->array_options['options_unit_amount'] * $objtask1add->unit_declared;
						if (!$lines[$i]->array_options['options_c_grupo']){
							//print price(price2num($amountdec,'MT'));
							$aLineasAux[$i]['l'] = $amountdec;
						}

						//print '</td>';

						//CPI
						//print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo']){
							//print $CPI;
							$aLineasAux[$i]['m'] = $CPI;
						}
						//print '</td>';
						//SPI
						//print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo']){
							$aLineasAux[$i]['n'] = $SPI;
							//print price2num($SPI,'MT');
						}
						//print '</td>';
						//CSI
						//print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo']){
							$aLineasAux[$i]['o'] = $CSI;
							//print price2num($CSI,'MT');
						}
						//print '</td>';

						//analisis de CSI
						if ($CSI > 0.9) $statut = 1;
						elseif($CSI >=0.8 AND $CSI <= 0.9) $statut = 0;
						else $statut = -1;
						$text = ($statut>0?$langs->trans('ProjetOK'):(empty($statut)?$langs->trans('Posiblearreglo'):$langs->trans('Lomasprobableesquenosearregle')));
						//CSI
						//print '<td align="right">';
						if ($lCalc)
							if (!$lines[$i]->array_options['options_c_grupo']){
								//print '<a href="#" class="classfortooltip" title="'.$text.'">'.img_picto('',DOL_URL_ROOT.'/monprojet/img/state'.$statut,'',true).'</a>';
								$aLineasAux[$i]['p'] = $statut;
							}

							else{
								//print '&nbsp;';
								$aLineasAux[$i]['p'] = $statut;
							}

							//print '</td>';
						// Tick to drag and drop
							if ($addordertick)
							{
								//print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
							}

							//print "</tr>\n";

							if (! $showlineingray) $inc++;

							$level++;
							if ($lines[$i]->id){
								$aLineasAux[$i]['q'] = monprojectLineejecLUIS($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick,$aLoop);

							}
							$level--;
							$total_projectlinesa_spent += $lines[$i]->duration;
							$total_projectlinesa_planned += $lines[$i]->planned_workload;
							if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
						}
					}
					else
					{
				//$level--;
					}
				}
			}

			if (($total_projectlinesa_planned > 0 || $total_projectlinesa_spent > 0) && $level==0)
			{
				/*print '<tr class="liste_total nodrag nodrop">';
				print '<td class="liste_total">'.$langs->trans("Total").'</td>';
				if ($showproject) print '<td></td><td></td>';
				print '<td></td>';
				print '<td></td>';
				print '<td></td>';
				print '<td align="right" class="nowrap liste_total">';
				print convertSecondToTime($total_projectlinesa_planned, 'allhourmin');
				print '</td>';
				print '<td></td>';
				print '<td align="right" class="nowrap liste_total">';
				print convertSecondToTime($total_projectlinesa_spent, 'allhourmin');
				print '</td>';
				print '<td align="right" class="nowrap liste_total">';
				if ($total_projectlinesa_planned) print round(100 * $total_projectlinesa_spent / $total_projectlinesa_planned,2).' %';
				print '</td>';
				if ($addordertick) print '<td class="hideonsmartphone"></td>';
				print '</tr>';*/
			}

			//return $inc;
			return $aLineasAux;
		}


//Reporte de Excel

		$_SESSION['aLineas'] = serialize($aLineas);
		$_SESSION['aExcel'] = serialize($aExcel);
		$_SESSION['aValores'] = serialize($aValores);
		if ($action != 'graphic')
		{
			print '<div class="tabsAction">'."\n";
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel&id='.$id.'">'.$langs->trans("Spreadsheet").'</a>';
			print '</div>'."\n";
		}
		//print '<table width="100%"><tr><td width="50%" valign="top">';
		//print '<a name="builddoc"></a>';


		llxFooter();

		$db->close();
