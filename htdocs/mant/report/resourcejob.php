<?php
/* Copyright (C)  7102 no one        <example@email.com>
 *
 *
 *	\file       htdocs/mants/report/resourcejob.php
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsresource.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mequipmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assets.class.php");


//require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';
//require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
//require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';
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
$fk_estado    = GETPOST("fk_estado");
$fk_tipo    = GETPOST("fk_tipo");

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

$object      		= new Mjobsext($db);
//$objEntity   		= new Entity($db);
$objEquipos  		= new Mequipmentext($db);
$objActivos  		= new Assets($db);
$objRecursos 		= new Mjobsresource($db);
$formfile    		= new FormFile($db);
$form        	    = new Form($db);
$MjobsresourceLine  = new MjobsresourceLine($db);
$entity = $conf->entity;



if ($action == 'excel')
{

	$aReporte = unserialize($_SESSION['aReporte']);

	$objPHPExcel = new PHPExcel();
	/**/
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		//$objPHPExcel = $objReader->load('templatemantstatus.xlsx');
	$objPHPExcel = $objReader->load('excel/templateresourcejob.xlsx');

		// Indicamos que se pare en la hoja uno del libro
	$objPHPExcel->setActiveSheetIndex(0);

		//$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$aEstados[$fk_estado],4=>$aTipos[$fk_tipo]);
		//La Cabecera



	if($aReporte[3] == ""){
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', "TODOS LOS ESTADOS");
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', $aReporte[3]);
	}

	if($aReporte[4] == ""){
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', "TODOS LOS RECURSOS");
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', $aReporte[4]);
	}

	$objPHPExcel->getActiveSheet()->SetCellValue('B4', dol_print_date($aReporte[2],'day'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B5', dol_print_date($aReporte[5],'day'));


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

		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['activo']);

		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['refequi']);

		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,dol_print_date($row['date_ini'],'day'));

		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['tipo']);

		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['descripcion']);

		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$row['cantidad']);

		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$row['unidad']);

		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,price2num($row['precio'],'MT'));

		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$line,html_entity_decode($row['estado']));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':J'.$line)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$line++;
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/resourcejob.xlsx");

	header('Location: '.DOL_URL_ROOT.'/mant/report/fiche_export.php?archive=resourcejob.xlsx');
}

/*
 * Actions
 */


/*
 * View
 */


$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Resourcesforjobs"),$help_url);

print_barre_liste($langs->trans("Resourcesforjobs"), $page, "", "", $sortfield, $sortorder,'',$num);

//Generamos el Select de Estados y tipos de Recursos

$aTipos   = array("MA"=>"Materiales","MO"=>"Mano de Obra","EQ"=>"Maquinaria y Equipos");
$opcTipos .= '<option value="-1">'.$langs->trans('All').'</option>';

foreach ($aTipos as $key => $value) {
	if($key == $fk_tipo){
		$opcTipos .= "<option value=".$key." selected >".$value."</option>";
	}else{
		$opcTipos .= "<option value=".$key.">".$value."</option>";
	}
}

$aEstados = array(1=>$langs->trans('Byassigning'),2=>$langs->trans('Assigned'),3=>$langs->trans('Programmed'),4=>$langs->trans('Inexecution'),5=>$langs->trans('Terminated'),8=>$langs->trans('Rejected for other reasons'),9=>$langs->trans('Refused'));
$aEstados = array(4=>$langs->trans('Inexecution'),5=>$langs->trans('Terminated'));

$opcEstados .= '<option value="-1">'.$langs->trans('All').'</option>';

foreach ($aEstados as $kiy => $valui) {
	if($kiy== $fk_estado){
		$opcEstados .= "<option value=".$kiy." selected >".$valui."</option>";
	}else{
		$opcEstados .= "<option value=".$kiy.">".$valui."</option>";
	}
}


print "<form action=\"resourcejob.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';
// Fecha_ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
$form->select_date($date_ini,'date_ini','','','',"crea_seat",1,1);
print '</td></tr>';
// Fecha
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
print '</td></tr>';
// Select Estado
print '<tr><td class="fieldrequired">'.$langs->trans('Status').'</td><td colspan="2">';
print '<select name="fk_estado">'.$opcEstados.'</select>';
print '</td></tr>';
// Select Tipo de Recurso
print '<tr><td class="fieldrequired">'.$langs->trans('Typeofresource').'</td><td colspan="2">';
print '<select name="fk_tipo">'.$opcTipos.'</select>';
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';

if ($action == 'edit')
{
	dol_htmloutput_mesg($mesg);

	$aLineas = array();


	$sql = " SELECT ";
	$sql.= " jr.dater, jr.type_cost, jr.description, jr.quant ,jr.fk_unit, jr.price, jr.status, ";
	$sql.= " e.ref,e.label,";
	$sql.= " a.ref AS refasset, a.ref_ext, a.label AS descripasset,";
	$sql.= " j.ref as refequi,j.date_ini,j.date_fin,j.detail_problem,j.description_job,j.status, j.rowid";

	$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs_resource as jr , ".MAIN_DB_PREFIX."m_equipment as e";

	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_jobs as j ON j.fk_equipment = e.rowid ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_equipment as a ON e.fk_asset = a.rowid ";
	$sql.= " WHERE e.entity = ".$conf->entity;
	$sql.= " AND  jr.fk_jobs = j.rowid";
	if($fk_estado != -1){
		$sql .= " AND j.status = ".$fk_estado;
	}

	if($fk_tipo != -1){
		$sql .= " AND jr.type_cost = '".$fk_tipo."'";
	}

	//$sql.= " AND jr.dater BETWEEN '".$db->idate($date_ini)."'";
	$sql.= " AND jr.dater BETWEEN '".$db->idate($date_ini)."' AND '".$db->idate($date_end)."'";
	$sql.= " ORDER BY e.ref ASC, jr.dater";
	//echo $sql;
	//exit;

	$res=$db->query($sql);
	if (! $res)
	{
		dol_print_error($db);
		exit;
	}

	$num = $db->num_rows($res);

	if ($num)
	{
		dol_fiche_head($head, 'card', $langs->trans("mants"), 0, 'Maintenance');

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Equipment"),"", "","","","");
		print_liste_field_titre($langs->trans("Ref.Ext."),"", "","","","");
		print_liste_field_titre($langs->trans("Workorder"),"", "","","","");
		print_liste_field_titre($langs->trans("Date"),"", "","","","");
		print_liste_field_titre($langs->trans("Type"),"", "",'','','');
		print_liste_field_titre($langs->trans("Description"),"", "",'','','');
		print_liste_field_titre($langs->trans("Quantity"),"", "","","","");
		print_liste_field_titre($langs->trans("Unit"),"", "","","",'align="center"');
		print_liste_field_titre($langs->trans("Price"),"", "","","",'align="center"');
		print_liste_field_titre($langs->trans("Status"),"", "",'','','align="center"');
		print "</tr>\n";

		$i=0;
		$var=true;
		$totalarray=array();
		while ($i < $num)
		{
			$obj = $db->fetch_object($res);
			if ($obj)
			{
				$var = !$var;
				print '<tr '.$bc[$var].'>';

				print '<td>'.$obj->ref.'</td>';
				$aLineas[$i]['ref'] = $obj->ref;
				print '<td>'.$obj->ref_ext.'</td>';
				$aLineas[$i]['activo'] = $obj->ref_ext;
				 //" - ".$obj->label;

				$object->id = $obj->id;
				$object->ref = $obj->refequi;
				$object->detail_problem = $obj->detail_problem;
				print '<td>'.$object->getNomUrl(1).'</td>';
				//print '<td>'.$obj->refequi.'</td>';
				$aLineas[$i]['refequi'] = $obj->refequi;

				print '<td>'.dol_print_date($obj->dater,'day').'</td>';
				$aLineas[$i]['date_ini'] = $obj->dater;

				print '<td>'.$obj->type_cost.'</td>';
				$aLineas[$i]['tipo'] = $obj->type_cost;

				print '<td>'.$obj->description.'</td>';
				$aLineas[$i]['descripcion'] = $obj->description;

				print '<td align = "center">'.$obj->quant.'</td>';
				$aLineas[$i]['cantidad'] = $obj->quant;


				$MjobsresourceLine->fk_unit = $obj->fk_unit;
				print '<td align = "center">'.$langs->trans($MjobsresourceLine-> getLabelOfUnit()).'</td>';
				//print '<td>'.$obj->fk_unit.'</td>';
				$aLineas[$i]['unidad'] = $langs->trans($MjobsresourceLine-> getLabelOfUnit('short'));

				print '<td align = "center">'.price2num($obj->price).'</td>';
				$aLineas[$i]['precio'] = $obj->price;

				$object->status = $obj->status;
				print '<td align = "center">'.$object->getLibStatut(0).'</td>';
				$aLineas[$i]['estado'] = $object->getLibStatut(0);
				$var = true;
			}
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

		$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$aEstados[$fk_estado],4=>$aTipos[$fk_tipo]);
		$aReporte[5]=$date_end;
		$_SESSION['aReporte'] = serialize($aReporte);


		print '<div class="tabsAction">'."\n";
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
		print '</div>'."\n";
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';

		//Aqui estaba el reporte
		$filename='mant/'.$period_year.'/resourcejob';
		$filedir=$conf->mant->dir_output.'/mant/'.$period_year.'/resourcejob';

		//echo "Dir : ". $filedir;

		$modelpdf = "resourcejob";

		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
			//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
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



llxFooter();



$db->close();
?>