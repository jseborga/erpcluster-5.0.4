<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/report/fiche.php
 *	\ingroup    Report
 *	\brief      Page fiche mant reports
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontactext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsprogram.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsadvance.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsresource.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mwctsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

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


$langs->load("mant");
$langs->load("others");

$action=GETPOST('action');
$date_ini=dol_mktime(0,0,1,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin=dol_mktime(0,0,1,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
$mesg = '';

$object      = new Mjobsext($db);
$objectcont  = new Mjobscontactext($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objjus  = new Mjobsuserext($db);
$objcont = new Mjobscontactext($db);
$objsoc  = new Societe($db);
$objmwcts= new Mwctsext($db);
$objProgram = new Mjobsprogram($db);
$objAdvance = new Mjobsadvance($db);
$objAdherent = new Adherent($db);
$objTyperepair = new Mtyperepair($db);
$objResource = new Mjobsresource($db);
$objProduct = new Product($db);

/*
 * Actions
 */

if ($action == 'excel')
{
	$aReporte  = unserialize($_SESSION['aReporte']);

	/*echo "<pre>";
	print_r($aReporte);
	echo "</pre>";
	echo "Entra al Reporte de excel ";exit;*/

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

	$objPHPExcel->getActiveSheet()->SetCellValue('A1',html_entity_decode($langs->trans('Useofresourcesforworkorders')));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:O1');
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);

	//Encabezados
	$objPHPExcel->getActiveSheet()->SetCellValue('A3',html_entity_decode($langs->trans('Dateini')));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aReporte[2],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:E3');

	$objPHPExcel->getActiveSheet()->SetCellValue('A4',html_entity_decode($langs->trans('Datefin')));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',dol_print_date($aReporte[3],'day'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:E4');

	$objPHPExcel->getActiveSheet()->SetCellValue('A5',html_entity_decode($langs->trans('Level')));
	$objPHPExcel->getActiveSheet()->SetCellValue('B5',html_entity_decode($aReporte[4]));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:E5');

	//Para poner formato a los numeros en el excel
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);

	//CABECERAS DE LA TABLA
	$line = 7;

	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,html_entity_decode($langs->trans("Ref")));
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,html_entity_decode($langs->trans("Datecreate")));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,html_entity_decode($langs->trans("Email")));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,html_entity_decode($langs->trans("Detailproblem")));
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,html_entity_decode($langs->trans("Dateassign")));
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,html_entity_decode($langs->trans("Dateiniprog")));
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,html_entity_decode($langs->trans("Datefinprog")));
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,html_entity_decode($langs->trans("Dateini")));
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,html_entity_decode($langs->trans("Datefin")));
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$line,html_entity_decode($langs->trans("Descriptionjob")));
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$line,html_entity_decode($langs->trans("Typemant")));
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$line,html_entity_decode($langs->trans("Type")));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$line,html_entity_decode($langs->trans("Label")));
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$line,html_entity_decode($langs->trans("Qty")));
	$objPHPExcel->getActiveSheet()->SetCellValue('O'.$line,html_entity_decode($langs->trans("Total")));



	$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':O'.$line)->applyFromArray(
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



	/*$objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setBold(true);*/


	//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
	//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$line = 8;

	foreach ( (array)$aReporte[1] as $i => $row)
	{

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['a']);

		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($row['b'],'day'));

		//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['c']);
		//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['d']);

		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,dol_print_date($row['e'],'day'));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,dol_print_date($row['f'],'day'));
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,dol_print_date($row['g'],'day'));
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,dol_print_date($row['h'],'day'));
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,dol_print_date($row['i'],'day'));

		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$line,$row['j']);

		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$line,$row['k']);

		if (is_array($row['l']))
		{
			$aData = $row['l'];
			foreach ($aData AS $j => $data)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('L'.$line,$data['type']);
				$objPHPExcel->getActiveSheet()->SetCellValue('M'.$line,$data['label']);
				$objPHPExcel->getActiveSheet()->SetCellValue('N'.$line,$data['qty']);
				$objPHPExcel->getActiveSheet()->SetCellValue('O'.$line,$data['total']);
				$line++;
			}
		}


		//$objPHPExcel->getActiveSheet()->SetCellValue('M'.$line,$row['m']);

		//$objPHPExcel->getActiveSheet()->SetCellValue('N'.$line,$row['n']);

		//$objPHPExcel->getActiveSheet()->SetCellValue('O'.$line,$row['o']);

		//$objPHPExcel->getActiveSheet()->SetCellValue('P'.$line,$row['p']);


		$line++;

	}

	$line--;
	$objPHPExcel->getActiveSheet()->getStyle('A8:O'.$line)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

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
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	//$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);


	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/workorders.xlsx");
	//$_SESSION['docsave'] =  "vacation".$aDatos['nombres'].".xlsx";
	//$_SESSION['doc'] =  "vacation".$aDatos['nombres'].".xlsx";
	//header('Location: '.DOL_URL_ROOT.'/assistance/fiche_export.php?archive=vacation'.$aDatos["nombres"].'.xlsx');
	header('Location: '.DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=workorders.xlsx');
}



// Add
if ($action == 'report' && $user->rights->mant->rep->leer)
{
	$date_ini = dol_mktime(0, 0, 1, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin = dol_mktime(23, 59, 59, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$level = GETPOST('level');
	if ($date_fin < $date_ini)
	{
		$mesg='<div class="error">'.$langs->trans("Errortheenddatecannotbegreaterthanstartdate").'</div>';
		$action="create";
	  // Force retour sur page creation
	}
	elseif (empty($date_fin) || empty($date_ini))
	{
		$mesg='<div class="error">'.$langs->trans("Errorisnecessarydates").'</div>';
		$action="create";
	  // Force retour sur page creation
	}
	else
		$action = 'report_ot';
}

$alevel = array(0=>'Todos',
	2=>'Validados',
	3=>'Programados',
	4=>'Concluidos',
	5=>'Validados,Programados,Concluidos');


/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Managementjobs"),$help_url);

if ($action == 'create' && $user->rights->mant->rep->leer)
{
	print_fiche_titre($langs->trans("Job orders"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="report">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	if (!isset($date_ini))
		$date_ini = dol_now();

	if (!isset($date_fin))
		$date_fin = dol_now();

	// date ini
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($date_ini,'di_','','','',"dateini",1,1);
	print '</td></tr>';

	// date fin
	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	$form->select_date($date_fin,'df_','','','',"datefin",1,1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans('Level').'</td><td colspan="2">';
	print $form->selectarray('level',$alevel,GETPOST('level'),0);
	print '</td>';
	print '</tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

	print '</form>';
}
else
{
	if ($date_ini && $date_fin)
	{

		dol_htmloutput_mesg($mesg);

		if ($action == 'report_ot')
		{
			$_SESSION['date_iniot'] = $date_ini;
			$_SESSION['date_finot'] = $date_fin;
			$_SESSION['levelot'] = $level;

			$aLineas = array();

			 //echo "-> Fecha inicio : ".$date_ini." -> Fecha Final : ".$date_fin." -> Nivel :".$alevel[$level];
			$object->getlist('',$date_ini,$date_fin,$level);

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print_liste_field_titre($langs->trans("Ref"));
			print_liste_field_titre($langs->trans("Fielddate_create"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Email"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Detailproblem"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Dateassign"),'','','','','align="center"');
		//print_liste_field_titre($langs->trans("Descriptionassign"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Dateiniprog"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Datefinprog"),'','','','','align="center"');
		 //print_liste_field_titre($langs->trans("Descriptionprogram"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Dateini"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Datefin"),'','','','','align="center"');
			print_liste_field_titre($langs->trans("Descriptionjob"),'','','','','align="left"');
		 //print_liste_field_titre($langs->trans("Workingclass"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Typerepair"),'','','','','align="left"');
			print_liste_field_titre($langs->trans("Resources"),'','','','','align="left"');

			print '</tr>';
			$lines = $object->array;
			$num = count($lines);
			if ($num)
			{
				$var = true;
				foreach((array) $lines AS $i => $obj)
				{
					$objsoc->fetch($obj->fk_soc);
					$aContact = $objsoc->contact_array();

			 //contactos
					$aJobsContact = $objcont->list_contact($obj->id);
			 //internos
					$aJobsUsers   = $objjus->list_jobsuser($obj->id);
					$listecontact = '';
					$listeContact = '';
					foreach ((array) $aJobsContact AS $k => $objtmp)
					{
						if (!empty($listecontact)) $listecontact .= ', ';
						if (!empty($listeContact)) $listeContact .= ', ';
						$listecontact .= $aContact[$objtmp->fk_contact];
						$listeContact .= $aContact[$objtmp->fk_contact];
					}
					foreach ((array) $aJobsUsers AS $k => $objtmp)
					{
						if (!empty($listecontact)) $listecontact .= ', ';
						if (!empty($listeContact)) $listeContact .= ', ';
						$resa = $objAdherent->fetch($objtmp->fk_user);
						if ($resa>0)
						{
							$listecontact .= $objAdherent->firstname.' '.$objAdherent->lastname;
							$listeContact .= $objAdherent->getNomUrl(1);
						}
					}
					$object->id = $obj->id;
					$object->ref = $obj->ref;
				//verificamos la programación
					$filterprog = " AND t.fk_jobs = ".$obj->id;
					$resprog = $objProgram->fetchAll('','',0,0,array(1=>1),'AND',$filterprog);
					$date_ini_prog = 0;
					$date_fin_prog = 0;
					if ($resprog>0)
					{
						foreach ($objProgram->lines AS $k => $linep)
						{
							if (empty($date_ini_prog)) $date_ini_prog = $linep->date_ini;
							if (empty($date_fin_prog)) $date_fin_prog = $linep->date_fin;
							if ($linep->date_ini <= $date_ini_prog) $date_ini_prog = $linep->date_ini;
							if ($linep->date_fin >= $date_fin_prog) $date_fin_prog = $linep->date_fin;
						}
					}
				//verificamos el avance
					$filterprog = " AND t.fk_jobs = ".$obj->id;
					$resadd = $objAdvance->fetchAll('','',0,0,array(1=>1),'AND',$filterprog);
					$date_ini = 0;
					$date_fin = 0;
					$description_job = '';
					if ($resprog>0)
					{
						foreach ($objAdvance->lines AS $k => $linep)
						{
							if (!empty($description_job)) $description_job.= '<br>';
							if (empty($date_ini)) $date_ini = $linep->date_ini;
							if (empty($date_fin)) $date_fin = $linep->date_fin;
							if ($linep->date_ini <= $date_ini) $date_ini = $linep->date_ini;
							if ($linep->date_fin >= $date_fin) $date_fin = $linep->date_fin;
							$description_job.= $linep->description;
						}
					}
					$var=!$var;
					print "<tr $bc[$var]>";
					print '<td>';
				//print '<a href="'.DOL_URL_ROOT.'/mant/jobs/fiche.php?id='.$obj->id.'">'.$obj->ref.'</a>';
					print $object->getNomUrl();
					print '</td>';
					$aLineas[$i]['a']=$obj->ref;
					print '<td align="center">'.dol_print_date($obj->date_create,'day').'</td>';
					$aLineas[$i]['b']=$obj->date_create;
					print '<td align="left">'.$obj->email.'</td>';
					$aLineas[$i]['c']=$obj->email;
					print '<td align="left">'.$obj->detail_problem.'</td>';
					$aLineas[$i]['d']=$obj->detail_problem;
					print '<td align="center">'.dol_print_date($obj->date_assign,'day').'</td>';
					$aLineas[$i]['e']=$obj->date_assign;
				//print '<td align="left">'.$obj->description_assign.'</td>';
					//$aLineas[$i]['f']=$obj->description_assign;
					if ($date_ini_prog>0)
					{
						$aLineas[$i]['f']=$date_ini_prog;
						print '<td align="center">'.dol_print_date($date_ini_prog,'day').'</td>';
					}
					else
						print '<td></td>';

					if ($date_fin_prog>0)
					{
						$aLineas[$i]['g']=$date_fin_prog;
						print '<td align="center">'.dol_print_date($date_fin_prog,'day').'</td>';
					}
					else print '<td></td>';

				//print '<td align="left">'.$obj->description_prog.'</td>';
					//$aLineas[$i]['i']=$obj->description_prog;
					if ($date_ini > 0)
					{
						$aLineas[$i]['h']=$date_ini;
						print '<td align="center">'.dol_print_date($date_ini,'day').'</td>';
					}
					else print '<td></td>';

					if ($date_fin > 0)
					{
						$aLineas[$i]['i']=$date_fin;
						print '<td align="center">'.dol_print_date($date_fin,'day').'</td>';
					}
					else print '<td></td>';
					print '<td align="left">'.$description_job.'</td>';
					$aLineas[$i]['j']=$description_job;
				//$res = $objmwcts->fetch_working_class($obj->typemant,$obj->speciality_job);
				//$workingclass = $langs->trans('Generic');
				//if ($res > 0 && $objmwcts->typemant == $obj->typemant && $objmwcts->speciality == $obj->speciality_job)
				//	$workingclass = select_working_class($objmwcts->working_class,'','',0,1);
				//print '<td align="left">'.$obj->typemant.'|'.$obj->speciality_job;
				//print $workingclass;
					//$aLineas[$i]['n']=$workingclass;
				//print '</td>';
					print '<td align="left">';
					$rest = $objTyperepair->fetch($obj->fk_type_repair);
					if ($rest>0)
					{
						print $objTyperepair->label;
					//print select_typemant($obj->typemant,'','',0,1);
				//$aLineas[$i]['o']=select_typemant($obj->typemant,'','',0,1);
						$aLineas[$i]['k']=$objTyperepair->label;
					}
					print '</td>';
				//print '<td align="left">';
				//print select_speciality($obj->speciality_job,'','',0,1);
				//$aLineas[$i]['p']=$obj->speciality_job;
				//print '</td>';
					//uso de recursos
					$filterres = " AND t.fk_jobs = ".$obj->id;
					$aRresource = '';
					$resu = $objResource->fetchAll('','',0,0,array(1=>1),'AND',$filterres);
					if ($resu >0)
					{
						foreach($objResource->lines AS $k => $liner)
						{
							if ($liner->type_cost == 'MA')
							{
								if ($liner->fk_product>0)
								{
									$objProduct->fetch($liner->fk_product);
									$aResource[$liner->type_cost][$objProduct->label]['qty']+=$liner->quant;
									$aResource[$liner->type_cost][$objProduct->label]['total']+=$liner->quant*$liner->price;
								}
								else
								{
									$aResource[$liner->type_cost][$liner->description]['qty']+=$liner->quant;
									$aResource[$liner->type_cost][$liner->description]['total']+=$liner->quant*$liner->price;
								}
							}
							else
							{
								$aResource[$liner->type_cost][$liner->description]['qty']+=$liner->quant;
								$aResource[$liner->type_cost][$liner->description]['total']+=$liner->quant*$liner->price;
							}
						}
						print '<td>';
						print '<table>';
						print '<tr class="liste_titre">';
						print '<td>'.$langs->trans('Type').'</td>';
						print '<td>'.$langs->trans('Label').'</td>';
						print '<td>'.$langs->trans('Qty').'</td>';
						print '<td>'.$langs->trans('Amount').'</td>';
						print '</tr>';
						$aLineasdet=array();
						$m = 0;
						foreach ($aResource AS $typecost => $aProd)
						{
							foreach ($aProd AS $label => $data)
							{
								print '<tr>';
								print '<td>'.$typecost.'</td>';
								print '<td>'.$label.'</td>';
								print '<td align="right">'.$data['qty'].'</td>';
								print '<td align="right">'.price(price2num($data['total'],'MT')).'</td>';
								print '</tr>';
								$aLineasdet[$m]['type'] = $typecost;
								$aLineasdet[$m]['label'] = $label;
								$aLineasdet[$m]['qty'] = $data['qty'];
								$aLineasdet[$m]['total'] = $data['total'];
								$m++;
							}
						}
						$aLineas[$i]['l'] = $aLineasdet;
						print '</table>';
						print '</td>';
					}
					print '</tr>';
				}
			}
			print '</table>';

			$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$date_fin,4=>$alevel[$level]);
			$_SESSION['aReporte'] = serialize($aReporte);
			print "<div class=\"tabsAction\">\n";
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans('Spreadsheet').'</a>';
			print '</div>';
		}
	}
}


llxFooter();

$db->close();
?>
