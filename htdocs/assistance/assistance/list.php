<?php
/* Copyright (C) 2017-2017
 *
 * Importar los datos de un excel al modilo de assistance
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';


require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/assistance/class/typemarkingext.class.php');
dol_include_once('/assistance/lib/assistance.lib.php');
dol_include_once('/assistance/lib/utils.lib.php');

require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/csources.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/cpartida.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/partidaproductext.class.php");

dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/assistanceext.class.php');
dol_include_once('/salary/class/puser.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');



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


$langs->load("assistance");
$langs->load("members");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$type  = GETPOST("type");
$fk_departament  = GETPOST("fk_departament");
$fk_concept = GETPOST("fk_concept");
$fk_entrepot = GETPOST("fk_entrepot");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$selrow = GETPOST('selrow');
$cancel = GETPOST('cancel');
$typeobjetive = GETPOST('typeobjetive');
$finality = GETPOST('finality');
$search_statut = GETPOST('search_statut');
$mesg = '';
if (!isset($_SESSION['period_year'])) $_SESSION['period_year']= date('Y');
$period_year = $_SESSION['period_year'];
$search_name = GETPOST('search_name');
// Purge search criteria

if (GETPOST("nosearch_x") || GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter"))

{
	$search_name='';
	$search_statut = -1;
	$search_date=dol_now();
	$aDateo = dol_getdate(dol_now());
	$aDate = dol_get_prev_day( zerofill($aDateo['mday'],2) , zerofill($aDateo['mon'],2) , $aDateo['year'] );
	$_SESSION['markdate'] = $search_date;
	$_SESSION['markdate_a'] = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year'],'user');
	$_SESSION['markdate_b'] = dol_mktime(23,59,59,zerofill($aDateo['mon'],2) , zerofill($aDateo['mday'],2) , $aDateo['year'],'user');
}

if (!isset($_SESSION['markdate_a']))
{
	$search_date = dol_now();
	$aDateo = dol_getdate(dol_now());
	$aDate = dol_get_prev_day( zerofill($aDateo['mday'],2) , zerofill($aDateo['mon'],2) , $aDateo['year'] );
	$_SESSION['markdate'] = $search_date;
	$_SESSION['markdate_a'] = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year'],'user');
	$_SESSION['markdate_b'] = dol_mktime(23,59,59,zerofill($aDateo['mon'],2) , zerofill($aDateo['mday'],2) , $aDateo['year'],'user');

}
else
{
	if (isset($_POST['d_year']))
	{
		$search_date = dol_mktime(0,0,0,GETPOST('d_month'),GETPOST('d_day'),GETPOST('d_year'),'user');
		$_SESSION['markdate'] = $search_date;
		$aDate = dol_get_prev_day(GETPOST('d_day'),GETPOST('d_month'),GETPOST('d_year'));
		$_SESSION['markdate_a'] = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year'],'user');
		$_SESSION['markdate_b'] = dol_mktime(23,59,59,GETPOST('d_month'),GETPOST('d_day'),GETPOST('d_year'),'user');
	}
}
$date_a = $_SESSION['markdate_a'];
$date_b = $_SESSION['markdate_b'];
$search_date= $_SESSION['markdate'];
if (GETPOST('rev'))
{
	$_SESSION['markdate'] = GETPOST('search_date');
	$search_date = GETPOST('search_date');
}

//Declaramos los objectos que se manejaran
$objLicences=new Licencesext($db);
$objUser  = new User($db);
$objAssistance = new Assistanceext($db);
$objAdherent = new Adherentext($db);
$objCuser = new Puser($db);
$objAssistancedef = new Assistancedef($db);
$objTypemarking = new Typemarkingext($db);
$objTmp = new Typemarkingext($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aDate = dol_getdate(dol_now());
$aStatus = array(-1=>$langs->trans('All'),0=>$langs->trans('Draft'),1=>$langs->trans('Pending'),2=>$langs->trans('Reviewed'));
//variables definidas
$aMarking = array(1=>'primary_entry',2=>'primary_exit',3=>'secundary_entry',4=>'secundary_exit',5=>'third_entry',6=>'third_exit',7=>'fourth_entry',8=>'fourth_exit',9=>'fifth_entry',10=>'fifth_exit',11=>'sixth_entry',12=>'sixth_exit');
$aColordef['libre']= $conf->global->ASSISTANCE_MARK_FREE;
$aColordef['normal']= $conf->global->ASSISTANCE_MARK_NORMAL;
$aColordef['retraso']= $conf->global->ASSISTANCE_MARK_RETRASO;
$aColordef['abandono']= $conf->global->ASSISTANCE_MARK_ABANDONO;
$aColordef['licencia']= $conf->global->ASSISTANCE_MARK_LICENCE;
$aColordef['vacation']= $conf->global->ASSISTANCE_MARK_VACATION;
$aColordef['nomark']= $conf->global->ASSISTANCE_MARK_NOMARK;
$aColordef['depure']= $conf->global->ASSISTANCE_MARK_DEPURE;
//verificamos si el tipo de marcación de la fecha es fijo o no
$lFixeddate = false;
$sex = 0;
$aDay = array();
$restype = $objTypemarking->fetchAll('','',0,0,array('statut'=>1),'AND'," AND t.fixed_date = ".$db->idate($search_date),true);
if ($restype == 1)
{
	$lFixeddate = true;
	if ($objTypemarking->sex !=0) $sex = $objTypemarking->sex;
}

/************************
 *       Actions        *
 ************************/
$now = dol_now();
// AddSave
if ($action == 'confirmarLicencia')
{
	header("Location: ".dol_buildpath('/assistance/licences.php',1));

}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

if ($action == 'excel')
{
	$dateReport = GETPOST('date');
	$aAssistance = unserialize($_SESSION['aAssistance']);
	$aReport = $aAssistance[$dateReport];

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Yemer Colque")
	->setLastModifiedBy("yemer colque")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Fractal Solutions");

	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
	//$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	//$sheet = $objPHPExcel->getActiveSheet();
	//$sheet->setCellValueByColumnAndRow(0,2, "Reporte de Rotación de Materiales");
	//$sheet->getStyle('A2')->getFont()->setSize(15);

	//$sheet->mergeCells('A2:E2');
	//if($yesnoprice)
	//	$sheet->mergeCells('A2:E2');
	//$sheet->getStyle('A2')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));

	//$objPHPExcel->getActiveSheet()->setCellValue('A4',$langs->trans("Date"));
	//$objPHPExcel->getActiveSheet()->setCellValue('A5',$langs->trans("Entrepot"));
	//$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans('Reportdate'));

	//$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);


	$objPHPExcel->getActiveSheet()->setCellValue('B3',dol_print_date(dol_now(),"dayhour",false,$outputlangs));
	$objPHPExcel->getActiveSheet()->setCellValue('B4', $entrepot->lieu);
	$objPHPExcel->getActiveSheet()->setCellValue('B5', dol_print_date($datefinsel,"day",false,$outputlangs));

	/*
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

	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
				),
			),
		);

	$objPHPExcel->getActiveSheet()->getStyle('A2:E8')->applyFromArray($styleThickBrownBorderOutline);
	*/
	// TABLA
	$objPHPExcel->setActiveSheetIndex(0);


	$objPHPExcel->getActiveSheet()->setCellValue('A6',$langs->trans("Nro"));
	$objPHPExcel->getActiveSheet()->setCellValue('B6',$langs->trans("Name"));
	$objPHPExcel->getActiveSheet()->setCellValue('C6',$langs->trans("Date"));
	$objPHPExcel->getActiveSheet()->setCellValue('D6',$langs->trans("Entry"));
	$objPHPExcel->getActiveSheet()->setCellValue('E6',$langs->trans("Exit"));
	$objPHPExcel->getActiveSheet()->setCellValue('F6',$langs->trans("Entry"));
	$objPHPExcel->getActiveSheet()->setCellValue('G6',$langs->trans("Exit"));
	$objPHPExcel->getActiveSheet()->setCellValue('H6',$langs->trans("Entry"));
	$objPHPExcel->getActiveSheet()->setCellValue('I6',$langs->trans("Exit"));
	$objPHPExcel->getActiveSheet()->setCellValue('J6',$langs->trans("Entry"));
	$objPHPExcel->getActiveSheet()->setCellValue('K6',$langs->trans("Exit"));

	$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->applyFromArray(
		array('font'    => array('bold'      => true),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),'borders' => array('allborders'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)),'fill' => array(
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
	$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->applyFromArray(
		array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FFA0A0A0')
					)
				)
			)

		);

	$j=8;
	$col='E';
	$sumsaav=0;
	$sumainpv=0;
	$sumaoutv=0;
	$sumabalv=0;
	$contt=1;

	foreach ((array) $aReport AS $i => $line)
	{

		$objPHPExcel->getActiveSheet()->setCellValue('A' .$j,$line['seq'])
		->setCellValue('B' .$j,$line['name'])
		->setCellValue('C' .$j,dol_print_date($line['date'],'day'))
		->setCellValue('D' .$j,0)
		->setCellValue('E' .$j,0);

		$j++;
		$contt++;

	}
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A8:E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/mark.xlsx");

	header("Location: ".DOL_URL_ROOT.'/assistance/assistance/fiche_export.php?archive=mark.xlsx');
}

/********************************************
 * View
 */

llxHeader("",$langs->trans("CheckMarkings"),$help_url);

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';

if($action == 'buscarLicencia'){

	$res=$objLicences->fetch(GETPOST('idLic'));
	$aDate = unserialize($_SESSION['date_a']);
	$date_b = unserialize($_SESSION['date_b']);
	$member = unserialize($_SESSION['member']);
	$res=$objLicences->fetch(GETPOST('idLic'));
	if($res > 0){
		$res = $objCuser->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_user like '.$objLicences->fk_member,true);
		$msgs  = '</br>';
		$msgs .= 'Licencia Otorgada al Sr(a) :'.$objCuser->lastname.' '.$objCuser->firstname.'</br>';
		$msgs .= 'con Fecha '.dol_print_date($objLicences->date_ini,'daytext').'</br>';
		$msgs .= 'desde las '.dol_print_date($objLicences->date_ini,'hour').'</br>';
		$msgs .= 'hasta las '.dol_print_date($objLicences->date_fin,'hour').'</br>';
		$msgs .= 'de Fecha '.dol_print_date($objLicences->date_fin,'daytext').'</br>';
		$msgs .= 'Desea comprobar la Licencia ? </br>';
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?action=edit&sw=1&a='.$aDate.'&b='.$date_b.'&fk_member='.$member,
			$langs->trans('Desglose de Licencia'),
			$msgs,
			'confirmarLicencia',
			null ,
			0,
			2);
		print $formconfirm;
	}else{
		setEventMessages('No se tiene una licencia en esa fecha ',null,'errors');
		$action = '';
	}
}


// Add

$idMember = GETPOST('fk_member');
$aDate = dol_getdate($search_date);
$wday = $aDate['wday'];

$_SESSION['date_a'] = serialize($aDate);
$_SESSION['date_b'] = serialize($date_b);
$_SESSION['member'] = serialize($idMember);


if(GETPOST('sw')==1){
	$aDate  = GETPOST('a');
	$date_b = GETPOST('b');
	$idMember = GETPOST('fk_member');
	$_SESSION['date_a'] = serialize($aDate);
	$_SESSION['date_b'] = serialize($date_b);
	$_SESSION['member'] = serialize($idMember);
}


$sql = 'SELECT a.fk_member, a.date_ass, a.marking_number, a.fk_licence, a.statut, a.active ';
$sql.= " , t.lastname, t.firstname ";
$sql.= " , p.docum, p.sex ";
$sql.= ' FROM llx_assistance as a';
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent  AS t ON a.fk_member = t.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user  AS p ON p.fk_user = t.rowid";
$sql.= " WHERE 1";
if ($search_name) $sql.= natural_search(array('t.lastname','t.firstname','p.docum' ),$search_name);
if ($search_statut===0 ||$search_statut==1 ||$search_statut==2) $sql.= " AND t.statut = ".$search_statut;
$sql .=" AND a.statut >= 1 AND a.date_ass BETWEEN '".$db->idate($date_a)."' AND '".$db->idate($date_b)."'";

$sql.= " ORDER BY a.fk_member ASC, a.date_ass ASC ";

$result = $db->query($sql);

if ($result){
	$num = $db->num_rows($result);
}else{
	setEventMessages('No existe registros',null,'mesgs');
	$action='';
		//exit;
}

$arrayMar = array();
$i = 0;
$u = 0;
$w = 0;
$aDatamark = array();
$aDatastatus = array();
$aDataactive = array();
$aDatalicence = array();
$aDatamarking=array();
$aDatasex=array();
$idMember = 0;
while ($i < $num)
{
	$obj = $db->fetch_object($result);
	if ($obj)
	{
		if ($idMember!= $obj->fk_member)
		{
			$line = 1;
			$idMember = $obj->fk_member;
		}
		$aDatetmp[$obj->fk_member][$line] = $db->jdate($obj->date_ass);
		$aDatamark[$obj->fk_member][$db->jdate($obj->date_ass)] = $line;
		$aDatastatus[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->statut;
		$aDataactive[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->active;
		$aDatamarking[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->marking_number;
		$aDatamarkingnumber[$obj->fk_member][$obj->marking_number] = $db->jdate($obj->date_ass);
		$aDatasex[$obj->fk_member] = $obj->sex;
		if ($obj->fk_licence > 0)
			$aDatalicence[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->fk_licence;
		$line++;
	}
	$i++;
}


$nro = 1;

print_barre_liste($langs->trans("Listassistance"), $page, "liste.php", "", $sortfield, $sortorder,'',0);

	//armamos el filtro
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<div style="min-width:450px;overflow-x: auto; white-space: nowrap;">';
print '<table class="noborder" width="100%">';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Nro"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Name"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Date"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entryone"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputone"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entrytwo"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputtwo"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entrythird"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputthird"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entryfour"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputfour"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Statut"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Licences"),"liste.php", "sa.statut",'','','align="right"',$sortfield,$sortorder);
print "</tr>\n";

print "<tr class=\"liste_titre\">";
print '<td>'.'</td>';
print '<td>'.'<input type="text"name="search_name" size="8" value="'.$search_name.'">'.'</td>';
print '<td>';
$form->select_date($search_date,'d_',0,0,1);
print '</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.$form->selectarray('search_statut',$aStatus,$search_statut,1).'</td>';
print '<td nowrap valign="top" align="right">';
print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
print '&nbsp;';
print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
print '</td>';
print "</tr>";

$seq = 1;
$line = 1;
$dataExport = array();
$aMarkingdefault = $conf->global->ASSISTANCE_MARK_DEFAULT;
foreach ($aDatamark AS $fk_member => $data)
{
	$aTmp = $aDatetmp[$fk_member];
	$aStatus = $aDatastatus[$fk_member];
	$aLicence = $aDatalicence[$fk_member];
	$aActive = $aDataactive[$fk_member];
	$aMarkingnumber = $aDatamarking[$fk_member];
	$aMarkingnum = $aDatamarkingnumber[$fk_member];
	$aMark = array();
	$lPrint = true;
	$objAdherent->fetch($fk_member);
	$filter = " AND t.statut >= 2 AND t.fk_member = ".$fk_member." AND t.date_ini BETWEEN " .$db->idate($date_a)." AND " .$db->idate($date_b);
	$reslic = $objLicences->fetchAll('','',0,0,array(1=>1),'AND',$filter);


	//verificamos el marcado definidio para el miembor o el por defecto
	$lFixeddate = false;
	$sex = 0;
	list($fk_typemarking,$type_marking,$nroMark,$tolerancia,$lFixeddate) =  verif_type_marking($fk_member,$wday);
	$restype = $objTypemarking->fetch($fk_typemarking);
	//$resdef = $objAssistancedef->fetch(0,$fk_member);
	//$nroMark = 0;
	$noDef = false;
	//if ($resdef<=0 && !empty($conf->global->ASSISTANCE_MARK_DEFAULT))
	//{
	//	$type_marking = $conf->global->ASSISTANCE_MARK_DEFAULT;
	//}
	//else
	//	$type_marking = $objAssistancedef->type_marking;
	if (empty($type_marking))
	{
		$noDef = true;
		setEventMessages($objAssistancedef->error,$objAssistancedef->errors,'errors');
	}
	else
	{
		//$restype = $objTypemarking->fetch(0,$type_marking);
		//$aDaytmp = explode(',',$objTypemarking->day_def);
		//$aDay = array();
		//foreach ((array) $aDaytmp AS $k => $value)
		//	$aDay[$value] = $value;
		//verfiicamos si corresponde el dia
		//if (!$aDay[$wday])
		//{
			//se toma el marcado por defecto
		//	$restype = $objTypemarking->fetch(0,$conf->global->ASSISTANCE_MARK_DEFAULT);
		//	$type_marking = $conf->global->ASSISTANCE_MARK_DEFAULT;
		//}

		//if ($lFixeddate)
		//{
		//	if ($sex === $aDatasex[$fk_member])
		//	{
		//		$restype = $objTypemarking->fetchAll('','',0,0,array('statut'=>1),'AND'," AND t.fixed_date = ".$db->idate($search_date),true);
		//		$type_marking = $objTypemarking->ref;
		//	}
		//}

		if ($restype > 0)
		{
			//$nroMark = $objTypemarking->mark;
			//$tolerancia = $objTypemarking->additional_time;
			for ($b=1; $b <= $objTypemarking->mark; $b++)
			{
				//$aDate = dol_getdate($objTypemarking->);
				$campo = $aMarking[$b];
				$aMark[$b] = $objTypemarking->$campo;
				if (empty($aMarkingnum[$b]))
					$aMarkingnum[$b] = 'F';
			}
		}
		//if ($objAssistancedef->aditional_time) $tolerancia = $objAssistancedef->aditional_time;
	}
	$var = !$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$seq.'</td>';
	$dataExport[$line]['seq'] = $seq;
	if (!$noDef) print '<td>';
	else print '<td bgColor="#819FF7">';

	print $objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
	//.' '.$aDatasex[$fk_member].' '.$varx.' '.$nroMark.' id='.$objTypemarking->id.' '.$type_marking.' '.$wday.'</td>';
	$dataExport[$line]['name'] = $objAdherent->lastname.' '.$objAdherent->firstname;
	ksort($data);
	ksort($aMarkingnum);
	$lDate = true;
	$cont = 0;
	$contreg = count($data);
	$datetmp = array();
	foreach ($data AS $datereg => $cont)
	{
		$datetmp[$cont]= $datereg;
	}
	$aLibre = array();
	if ($conf->global->ASSISTANCE_FREE_OUT)
	{
		for ($a = 2; $a<=18; $a++)
		{
			$b = $a+1;
			if ($datetmp[$a]>0 && $datetmp[$b]>0)
			{
				$aDini = dol_getdate($datetmp[$a]);
				$aDfin = dol_getdate($datetmp[$b]);
				$lReg = verif_time_range($aDini,$aDfin,$conf->global->ASSISTANCE_MINUTES_FREE,$fk_member);
				if ($lReg)
				{
					$b = $a+1;
					$aLibre[$a]=$a;
					$aLibre[$b]=$b;
				}
				$a++;
			}
		}
	}
	$nprint = 1;
	//revisamos el estado del registro por member
	$status = '';
	$lStatut = true;
	foreach ($aStatus AS $datereg => $statut)
	{
		if (empty($status)) $status = $statut;
		else
		{
			if ($status != $statut) $lStatut = false;
		}
	}
	foreach ($aTmp AS $cont => $datereg)
	//foreach ($aMarkingnum AS $cont => $datereg)
	{
		if ($datereg == 'F') $aActive[$datereg]=1;
		if ($aActive[$datereg]==1)
		{
			//si esta revisado mostramos
			if ($lStatut && $status == 2)
			{
				if ($datereg == 'F')
				{
					$aResult[0]='nomark';
					$aResult[1]='No registrado';
					print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.$aResult[1].'</td>';
					$dataExport[$line][$nprint]['marca'] = 0;
					$dataExport[$line][$nprint]['resultado'] = $aResult[2];
					$nprint++;
				}
				else
				{
				if ($aMarkingnumber[$datereg] == $nprint)
				{
							$aDate = dol_getdate($datereg);
							$aResult = verifica_retraso($aMark,$aDate,$nprint,$tolerancia,$fk_member);
							if ($lDate)
							{
								print '<td>'.dol_print_date($datereg,'day').'</td>';
								$dataExport[$line]['date'] = $datereg;
								$lDate = false;
							}
							print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2).' '.($aResult[1]?$aResult[0].' '.$aResult[1]:'').'</td>';
							$dataExport[$line][$nprint]['marca'] = zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2);
							$dataExport[$line][$nprint]['resultado'] = $aResult[2];

					$nprint++;
				}
				}
			}
			else
			{

				if (!$aLibre[$cont])
				{
					if ($nprint <=8)
					{
						if (!$aLicence[$datereg])
						{
							$aDate = dol_getdate($datereg);
							$aResult = verifica_retraso($aMark,$aDate,$nprint,$tolerancia,$fk_member);
							if ($lDate)
							{
								print '<td>'.dol_print_date($datereg,'day').'</td>';
								$dataExport[$line]['date'] = $datereg;
								$lDate = false;
							}
							print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2).' '.($aResult[1]?$aResult[0].' '.$aResult[1]:'').'</td>';
							$dataExport[$line][$nprint]['marca'] = zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2);
							$dataExport[$line][$nprint]['resultado'] = $aResult[2];

						//print '<td '.($aResult[0]=='retraso'?'bgColor=#'.$aColordef[''].'B2ff59':($aResult[0]=='abandono'?'bgColor=#ff0000':'bgColor=#b2ff59')).'>'.zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2).' '.($aResult[1]?$aResult[0].' '.$aResult[1]:'').'</td>';
						}
					}
					if (!$aLicence[$datereg])
						$nprint++;
				}
			}
		}

	}
	if ($nprint < $nroMark)
	{
		$nCont = $nprint;
		for($c = $nCont; $c <= $nroMark; $c++)
		{
			print '<td>'.'</td>';
			$nprint++;
		}
	}
	if ($nprint <= 8)
	{
		for ($a = $nprint; $nprint <= 8; $a++)
		{
			print '<td>'.'</td>';
			$nprint++;
		}
	}
	$seq++;
	print '<td>';
	if ($lStatut)
	{
		$objAssistance->statut = $status;
		print $objAssistance->getLibStatut(3);
	}
	print '</td>';

	if (!$noDef)
		print '<td align="right">'.'<a class="butAction" title="'.$langs->trans('Licencias y/o Vacaciones').'" href="'.DOL_URL_ROOT.'/assistance/assistance/card.php?id='.$fk_member.'&date='.$search_date.'">'.$reslic.'</a>'.'</td>';
	else
		print '<td></td>';
	print '</tr>';
	$line++;
}

$aReport [$search_date] = $dataExport;
$_SESSION['aAssistance'] = serialize($aReport);


print '</table>';

print '</form>';

print "<div class=\"tabsAction\">\n";
print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?date='.$search_date.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
print '</div>';

llxFooter();
$db->close();
?>