<?php
/* Copyright (C)  7102 no one        <example@email.com>
 *
 *
 *	\file       htdocs/mants/report/equipmentjob.php
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mequipmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsadvance.class.php");


//require_once DOL_DOCUMENT_ROOT.'/core/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

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


$langs->load("mants");


$action       = GETPOST('action');
$type_seat    = GETPOST("type_seat");
$fk_equipo    = GETPOST("fk_equipo");

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
	$date_ini=dol_get_first_day($pastmonthyear,$pastmonth,false);
	$date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$mesg = '';

$object      = new Mjobsext($db);
//$objEntity   = new Entity($db);
$objEquipment  = new Mequipmentext($db);
$objAsset  = new Assets($db);
$objAdvance = new Mjobsadvance($db);
$formfile    = new FormFile($db);
$form        = new Formv($db);
$entity = $conf->entity;



if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);

	$objPHPExcel = new PHPExcel();
	/**/
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		//$objPHPExcel = $objReader->load('templatemantstatus.xlsx');
	$objPHPExcel = $objReader->load('excel/templateequipmentjob.xlsx');

		// Indicamos que se pare en la hoja uno del libro
	$objPHPExcel->setActiveSheetIndex(0);

		//La Cabecera
	$objPHPExcel->getActiveSheet()->SetCellValue('B3', dol_print_date($aReporte[2],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4', dol_print_date($aReporte[3],'day'));

	if($aReporte[4] == ""){
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', "TODOS LOS EQUIPOS");
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', $aReporte[4]);
	}

	$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		// Color rojo al texto
		//$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		// Texto alineado a la derecha
		//$objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		// Damos un borde a la celda
		//$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		//$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		//$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		//Guardamos el archivo en formato Excel 2007
		//Si queremos trabajar con Excel 2003, basta cambiar el 'Excel2007' por 'Excel5' y el nombre del archivo de salida cambiar su formato por '.xls'

		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save("Archivo_salida.xlsx");

	/**/

	$line = 8;

	foreach ( (array) $aReporte[1] as $j => $row)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);

		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['refasset']);

		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['refequi']);

		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,dol_print_date($row['date_ini'],'day'));

		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,dol_print_date($row['date_fin'],'day'));

		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['problema']);

		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$row['solucion']);

		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,html_entity_decode($row['estado']));
		$objPHPExcel->getActiveSheet()->getStyle('F'.$line)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$line)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

		$line++;
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save("excel/equipmentjob.xlsx");
	header('Location: '.DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=equipmentjob.xlsx');
}

/*
 * Actions
 */


/*
 * View
 */


$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Equipmentjob"),$help_url);

print_barre_liste($langs->trans("Equipmentmaintenance"), $page, "", "", $sortfield, $sortorder,'',$num);

print "<form action=\"equipmentjob.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Equipment').'</td><td colspan="2">';
print $form->select_equipment($fk_equipo, 'fk_equipo', '', 20, 0, 0, 2, '', 1, array(),0,'','',0);

print '</td></tr>';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
$form->select_date($date_ini,'date_ini','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';
if ($action == 'edit' && $fk_equipo<=0)
{
	setEventMessages($langs->trans('Selectequipment'),null,'warnings');
}
if ($action == 'edit' && $fk_equipo>0)
{
	dol_htmloutput_mesg($mesg);

	$aLineas = array();


	$sql = " SELECT ";
	$sql.= " e.ref,e.label,";
	$sql.= " a.ref AS refasset,a.descrip AS descripasset,";
	$sql.= " j.ref as refequi,j.date_ini,j.date_fin,j.detail_problem,j.description_job,j.status, j.rowid";

	$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment as e ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_jobs as j ON j.fk_equipment = e.rowid ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."assets as a ON e.fk_asset = a.rowid ";
	$sql.= " WHERE e.entity = ".$conf->entity;
	if($fk_equipo != -1){
		$sql .= " AND e.rowid = ".$fk_equipo;
	}
	$sql .= " AND j.date_ini BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_end)."' ";
	$sql.= " ORDER BY e.ref ASC, j.date_ini, j.date_fin";

	if ($fk_equipo>0)
	{
		$objEquipment->fetch($fk_equipo);
	}
	$resql=$db->query($sql);
	if (! $resql)
	{
		dol_print_error($db);
		exit;
	}

	$num = $db->num_rows($resql);

	if ($num)
	{
		dol_fiche_head($head, 'card', $langs->trans("Maintenance"), 0, 'Maintenance');

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Equipment"),"", "","","","");
		print_liste_field_titre($langs->trans("Assets"),"", "","","","");
		print_liste_field_titre($langs->trans("Workorder"),"", "","","","");
		print_liste_field_titre($langs->trans("Dateini"),"", "","","","");
		print_liste_field_titre($langs->trans("Datefin"),"", "",'','','');
		print_liste_field_titre($langs->trans("Problem"),"", "",'','','');
		print_liste_field_titre(html_entity_decode($langs->trans("Actiontaken")),"", "","","","");
		print_liste_field_titre($langs->trans("Status"),"", "",'','','align="center"');
		print "</tr>\n";

		$i=0;
		$var=true;
		$totalarray=array();
		while ($objtmp = $db->fetch_object($resql))
		{

				//buscamos el avance
			$textadvance = '';
			$textadvancehtml = '';
			$filterj = " AND t.fk_jobs = ".$objtmp->rowid;
			$resadv = $objAdvance->fetchAll('DESC','date_ini',2,0,array(1=>1),'AND',$filterj);
			if ($resadv>0)
			{
				foreach ($objAdvance->lines AS $j => $line)
				{
					if ($textadvance) $textadvance.='<br>';
					$textadvance.= ($line->date_ini>0?dol_print_date($line->date_ini,'day').' - '.dol_print_date($line->date_fin,'day').': ':'').$line->description;
				}
				$textadvancehtml = htmlspecialchars(str_replace('<br>',"\n",$textadvance));
			}
			$var = !$var;
			print '<tr '.$bc[$var].'>';

			print '<td>'.$objtmp->ref.'</td>';
			$aLineas[$i]['ref'] = $objtmp->ref;
			$aLineas[$i]['label'] = $objtmp->label;
			print '<td>'.$objtmp->refasset.'</td>';
			$aLineas[$i]['refasset'] = $objtmp->refasset;

			$object->id = $objtmp->rowid;
			$object->ref = $objtmp->refequi;
			$object->detail_problem = $objtmp->detail_problem;
			$object->status = $objtmp->status;
			print '<td>'.$object->getNomUrl(1).'</td>';
				//print '<td>'.$objtmp->refequi.'</td>';
			$aLineas[$i]['refequi'] = $objtmp->refequi;
			print '<td>'.dol_print_date($objtmp->date_ini,'day').'</td>';
			$aLineas[$i]['date_ini'] = $objtmp->date_ini;
			print '<td>'.dol_print_date($objtmp->date_fin,'day').'</td>';
			$aLineas[$i]['date_fin'] = $objtmp->date_fin;
			print '<td>'.$objtmp->detail_problem.'</td>';
			$aLineas[$i]['problema'] = $objtmp->detail_problem;
			print '<td>'.$textadvance.'</td>';
			$aLineas[$i]['solucion'] = html_entity_decode($textadvancehtml);
			$object->status = $objtmp->status;
			print '<td>'.$object->getLibStatut(6).'</td>';
			$aLineas[$i]['estado'] = $object->getLibStatut(0);
			$i++;
		}
		//$db->free($result);

		print "</table>";




		/*$entity = $conf->entity;
		if($ObjEntity->fetch($entity) > 0){
			$labelEntity = $ObjEntity->label;
		}else{
			$labelEntity = "";
		}*/

		$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$date_end,4=>$aEquipos[$fk_equipo]);
		$_SESSION['aReporte'] = serialize($aReporte);


		print '<div class="tabsAction">'."\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
		print '</div>'."\n";
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';

		//Aqui estaba el reporte
		$filename='mant/'.$period_year.'/equipmentjobs';
		$filedir=$conf->mant->dir_output.'/mant/'.$period_year.'/equipmentjobs';

		//echo "Dir : ". $filedir;

		$modelpdf = "equipmentjobs";

		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
			//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		$object->fk_equipment = $fk_equipo;
		$object->ref_equipment = $objEquipment->ref;
		$object->label_equipment = $objEquipment->label;

		$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result < 0) dol_print_error($db,$result);

		$urlsource=$_SERVER['PHP_SELF'];
		//$genallowed=$user->rights->assistance->lic->hiddemdoc;
		//$delallowed=$user->rights->assistance->lic->deldoc;
		$genallowed = 1;
		$delallowed = 1;
		print $formfile->showdocuments('mant',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

		$somethingshown=$formfile->numoffiles;

		print '</td></tr></table>';

	}
	else
	{
		setEventMessages($langs->trans('No existe registros de mantenimiento'),null,'warnings');
	}
}

$db->close();
llxFooter();
?>