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
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/mwcts/class/mwcts.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestuser.class.php';
require_once(DOL_DOCUMENT_ROOT."/mant/class/mworkrequestext.class.php");

require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
//require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
//require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';




// Excel
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



if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';

}


$langs->load("mant@mant");
$langs->load("others");



$action=GETPOST('action');

$date_ini=GETPOST('date_ini');
$date_fin=GETPOST('date_fin');
$level = GETPOST('level');
$mesg = '';


$object = new Mworkrequestext($db);
$objtmp = new Mworkrequestext($db);
$objMjobs = new Mjobsext($db);
$objAdherent = new Adherent($db);
$objSoc = new Societe($db);
$objUser = new User($db);
$objContact = new Contact($db);
$objJobscontact = new Mjobscontact($db);
$objJobsuser = new Mjobsuser($db);
$objWorkcontact = new Mworkrequestcontact($db);
$objWorkuser = new Mworkrequestuser($db);
//equip
$objEquipment= new Mequipmentext($db);
// repair typo de servicio
$objTyperepair = new Mtyperepair($db);
// location
$objLocation = new Mlocation($db);
//propert
$objProperty = new Mproperty($db);
$form=new Form($db);
$limit = $conf->liste_limit;
$offset = $limit * $page;
/*
* Actions
*/
if ($action == 'reporteExcel')
{

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

	$aReportdet = unserialize($_SESSION['aReportdet']);
	$date_ini = unserialize($_SESSION['date_inidet']);
	$date_fin = unserialize($_SESSION['date_findet']);
//$level = unserialize($_SESSION['level']);

//PIE DE PAGINA


	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setCellValueByColumnAndRow(0,2, "LISTADO DE TICKET");
	$sheet->getStyle('A2')->getFont()->setSize(15);

	$sheet->mergeCells('A2:J2');
	if($yesnoprice)
		$sheet->mergeCells('A2:J2');
	$sheet->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
// ENCABEZADO
//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Codigo Unidad"));
	$objPHPExcel->getActiveSheet()->setCellValue('B4',$langs->trans("dateini"));
	$objPHPExcel->getActiveSheet()->setCellValue('B5',$langs->trans("datefin"));
	$objPHPExcel->getActiveSheet()->setCellValue('B6',$langs->trans("Estado"));
	$objPHPExcel->getActiveSheet()->setCellValue('C4',dol_print_date($date_ini,'day'));
	$objPHPExcel->getActiveSheet()->setCellValue('C5',dol_print_date($date_fin,'day'));
	if($level==-1)
	{

		$objPHPExcel->getActiveSheet()->setCellValue('C6',"todos");
	}
	else
	{
		$objPHPExcel->getActiveSheet()->setCellValue('C6',$level);
	}



	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
// TABLA
// Numero correlativo
	$objPHPExcel->getActiveSheet()->setCellValue('A8',$langs->trans("Nro"));
// referencia
	$objPHPExcel->getActiveSheet()->setCellValue('B8',$langs->trans("Ref"));
// fecha de solicitante
	$objPHPExcel->getActiveSheet()->setCellValue('C8',$langs->trans("Fecha de solicitante"));
// quien solicito
	$objPHPExcel->getActiveSheet()->setCellValue('D8',$langs->trans("solicito"));
//interno
	$objPHPExcel->getActiveSheet()->setCellValue('E8',$langs->trans("Internal"));
// equipo
	$objPHPExcel->getActiveSheet()->setCellValue('F8',$langs->trans("Equipo"));
// Inmueble
	$objPHPExcel->getActiveSheet()->setCellValue('G8',$langs->trans("Inmueble"));
// Tipo de servicio
	$objPHPExcel->getActiveSheet()->setCellValue('H8',$langs->trans("Tipo de servicio"));
// Orden de Trabajo
	$objPHPExcel->getActiveSheet()->setCellValue('I8',$langs->trans("Orden de Trabajo"));
//Estado
	$objPHPExcel->getActiveSheet()->setCellValue('j8',$langs->trans("Statut"));

	$objPHPExcel->getActiveSheet()->getStyle('A8:J8')->applyFromArray(
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


//tama;o de las columnas
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
//formato de las columnas Numero Fechas
	$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

//$objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

	$objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('R')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	$objPHPExcel->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
//cuerpo
	$j=9;
	$contt=1;
	foreach ((array) $aReportdet AS $i => $lines)
	{
		$ref = $lines['ref'];
		$date = $lines['date'];
		$Applicant = $lines['Applicant'];
		$Internal = $lines['Internal'];
		$Equipo = $lines['Equipo'];
		$Inmueble = $lines['Inmueble'];
		$Tipos = $lines['Tipos'];
		$Orden = $lines['Orden'];
		$Estado = $lines['Estado'];


		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$contt)
		->setCellValue('B' .$j,$ref)
		->setCellValue('C' .$j,dol_print_date($date,'day'))
		->setCellValue('D' .$j,$Applicant)
		->setCellValue('E' .$j,$Internal)
		->setCellValue('F' .$j,$Equipo)
		->setCellValue('G' .$j,$Inmueble)
		->setCellValue('H' .$j,$Tipos)
		->setCellValue('I' .$j,$Orden)
		->setCellValue('J' .$j,$Estado);



		$objPHPExcel->getActiveSheet()->getStyle('A8:J'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$j++;
		$contt++;


	}

	$objPHPExcel->setActiveSheetIndex(0);
// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/list.xlsx");
	header("Location: ".DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=list.xlsx');
}

$action="report";
// Add
if ($action == 'report' && $user->rights->mant->rep->leer)
{
	$date_ini = dol_mktime(0, 0, 1, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin = dol_mktime(12, 59, 59, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$level = GETPOST('level');
	if ($date_fin < $date_ini)
	{
		$mesg='<div class="error">'.$langs->trans("Errortheenddatecannotbegreaterthanstartdate").'</div>';
$action="create";   // Force retour sur page creation

}
elseif (empty($date_fin) || empty($date_ini))
{
	$mesg='<div class="error">'.$langs->trans("Errorisnecessarydates").'</div>';
		$action="create";   // Force retour sur page creation
	}
	else
		$action = 'report_ot';
}
/*
$aEstado = array(0=>'Ninguno',
2=>'Validado',
3=>'Pendiente',
4=>'Otros');
*/
///armamos array de estados
$aEstado = array(0=>'Pendiente',
	1=>'Validado',
	2=>'Asignado',
	3=>'Asignado tec.',
	4=>'Programado',
	5=>'En ejecucion',
	6=>'Ejecutado',
	9=>'Cerrado');
$aEstado = array(0=>'Pendiente',
	4=>'Validado');
$form=new Form($db);
$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Listtickets"),$help_url);
//$action = 'create';

if ($action == 'create' && $user->rights->mant->rep->leer)
{
	print_fiche_titre($langs->trans("Listtickets"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="report">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	$fecha = new DateTime($date_ini);
	$fecha->modify('first day of this month');
	if (!isset($date_ini))
		$date_ini = $fecha->format('d/m/Y');
	$fecha->modify('last day of this month');
	if (!isset($date_fin))
		$date_fin = $fecha->format('d/m/Y');

	// date ini
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($date_ini,'di_','','','',"dateini",1,1);
	print '</td></tr>';
	// date fin
	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	$form->select_date($date_fin,'df_','','','',"datefin",1,1);
	print '</td></tr>';
	// level
	print '<tr><td class="fieldrequired">'.$langs->trans('Estado').'</td><td colspan="2">';
	print $form->selectarray('level',$aEstado,GETPOST('level'),1);
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

		$aDateini = dol_getdate($date_ini);
		$aDate = dol_get_prev_day($aDateini['mday'], $aDateini['mon'], $aDateini['year']);
		$date_ini = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year']);
		$aDatefin = dol_getdate($date_fin);
		$date_fin = $adatefin['year'].'-'.$adatefin['mon'].'-'.$adatefin['mday'];
		$date_fin .= ' 23:59:59';
		$date_fin = dol_mktime(23,59,59,$aDatefin['mon'],$aDatefin['mday'],$aDatefin['year']);
		dol_htmloutput_mesg($mesg);
		if ($action == 'report_ot')
		{
			$_SESSION['date_iniot'] = $date_ini;
			$_SESSION['date_finot'] = $date_fin;
			$_SESSION['levelot'] = $level;
			$form=new Form($db);
			$sql  = "SELECT p.rowid, p.ref, p.fk_member, p.email, p.internal, p.date_create, p.detail_problem, p.status, p.fk_soc, p.speciality,p.fk_property,p.fk_equipment, p.fk_location, p.fk_type_repair ";
			$sql.= " FROM ".MAIN_DB_PREFIX."m_work_request as p";
			$sql.= " WHERE p.entity = ".$conf->entity;
			$sql.= " AND p.date_create BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_fin)."'";
			if($level!=-1)
			{
				$sql.= " AND p.status =".$level;
			}

			$result = $db->query($sql);
			$i = 0;
			if ($result)
			{
				$num = $db->num_rows($result);

				print_fiche_titre($langs->trans("Listtickets"));
				print '<table class="noborder" width="100%">';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"));
				print_liste_field_titre($langs->trans("Date"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Applicant"),'','','','','align="left"');
				print_liste_field_titre($langs->trans("Internal"),'','','','','align="left"');
				print_liste_field_titre($langs->trans("Equipo"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Inmueble"),'','','','','align="left"');
				print_liste_field_titre($langs->trans("Tipo de Servicio"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Orden de trabajo"),'','','','','align="center"');
				print_liste_field_titre($langs->trans("Status"),'','','','','align="left"');
				print '</tr>';
				//$num = count($object->array);
				if ($num)
				{
					$var=True;
					while ($i < min($num,$limit))
					{
						$lPrint = true;
						$objp = $db->fetch_object($result);

						$objAdherent->fetch($objp->fk_member);
						//verificamos responsables asignados
						$htmlc = '';
						$htmlsoc = '';
						if ($lPrint)
						{
							$nLoop++;
							$var=!$var;
							print "<tr $bc[$var]>";
							$objtmp->id = $objp->rowid;
							$objtmp->ref = $objp->ref;
							print '<td>'.$objtmp->getNomUrl(1).'</td>';

							print '<td>'. dol_print_date($objp->date_create,'day').'</td>';
							//buscamos al usuario
							$objAdherent->fetch($objp->fk_member);
							if ($objAdherent->id == $objp->fk_member)
								print '<td>'.$objAdherent->getNomUrl(1).'-'.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
							else
								print '<td>'.$objp->email.'</td>';
							print '<td>'.$objp->internal.'</td>';

							// Equipo
							// property
							//$res = $objLocation->fetch($object->fk_location);
							//if ($res>0) $fk_property = $objLocation->fk_property;
							if ($objEquipment->fetch($objp->fk_equipment) > 0)
							//print $objEquipment->ref;
								print '<td>'.$objEquipment->ref.'</td>';
							else
								print '<td></td>';
							// Inmueble
							print '<td>';
							if ($objProperty->fetch($objp->fk_property) > 0)
								$refpro= $objProperty->ref;
							else
								$refpro= "no definido";
							print '</td>';
							//print '</td></tr>';
							print '<td>'.$refpro.'</td>';
							// tipo de servicio
							if ($objTyperepair->fetch($objp->fk_type_repair) > 0)
							//print $objTyperepair->label;
								print '<td>'.$objTyperepair->label.'</td>';
							else
								print '<td></td>';

							print '<td>';
							//buscamos si tiene ordenes de trabajo
							$filter = " AND t.fk_work_request = ".$objp->rowid;
							$res = $objMjobs->fetchAll('ASC','t.ref',0,0,array(1=>1),'AND',$filter);
							$refJobs = '';
							if ($res >0)
							{
								$lines = $objMjobs->lines;
								foreach ($lines AS $j => $line)
								{
									$objMjobs->id = $line->id;
									$objMjobs->ref = $line->ref;
									$cJobs.= $objMjobs->getNomUrl(1);
									if (!empty($refJobs)) $refJobs.='; ';
									$refJobs.= $line->ref;
								}
								print $cJobs;
							}
							print '</td>';
							$objtmp->status = $objp->status;
							print '<td nowrap align="right">'.$objtmp->getLibStatut(6).'</td>';

							print '</tr>';
						}
						$i++;
						$aReport[]=array('ref'=>$objp->ref,'date'=>$objp->date_create,'Applicant'=>$objAdherent->lastname.' '.$objAdherent->firstname,'Internal'=>$objp->internal,'Equipo'=>$objEquipment->ref,'Inmueble'=>$refpro,'Tipos'=>$objTyperepair->label,'Orden'=>$refJobs,'Estado'=>$objp->status);
					}
				}
				$db->free($result);
				print "</table>";
				$_SESSION['aReportdet'] = serialize($aReport);
				$_SESSION['date_inidet'] = serialize($date_ini);
				$_SESSION['date_findet'] = serialize($date_fin);
				$_SESSION['level'] = serialize($level);
				print "<div class=\"tabsAction\">\n";
				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?level='.$level.'&action=reporteExcel">'.$langs->trans("Spreadsheet").'</a>';
				print '</div>';

			}
		}

	}
}
print '<div class="fichecenter">';
$formfile = new Formfile($db);

/*
 * Documents generes
 */
$filename = dol_sanitizeFileName('report_ticket');
$filedir = $conf->mant->dir_output . "/report_ticket";
$urlsource = $_SERVER["PHP_SELF"] . "?level=" . $level;
if ($fk_period) $genallowed = $user->rights->mant->crearbsal;
$delallowed = $user->rights->mant->delrbsal+0;

$var = true;
	//$modelpdf = 'boletacofa';
$modelpdf = 'listt';

$somethingshown = $formfile->show_documents('mant', $filename, $filedir, $urlsource, $genallowed, $delallowed, $modelpdf, 1, 0, 0, 28, 0, '', 0, '', $soc->default_lang);
print '</div>';
llxFooter();
$db->close();
?>
