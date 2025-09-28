<?php
/*   Copyright (C) 2017 L. Mendoza Ticona <l.mendoza.liet@gmail.com>
 *      Desarrollador PHP, JAVA
 *   Descripcion: El presente clase maneja los reporte de Licencia y Vacaciones
 *   - por miembro o todos
 *   - en vacaciones consulta si le corresponde o no, y si corresponde cuandos dias tiene asignado
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
//require_once(DOL_DOCUMENT_ROOT.'/poa/core/modules/poa/modules_poa.php');

include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');
//include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/assistance/class/puser.class.php');
dol_include_once('/assistance/class/licences.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/membervacation.class.php');
dol_include_once('/assistance/class/membervacationdet.class.php');
if ($conf->salary->enabled)
	dol_include_once('/salary/class/pcontract.class.php');
dol_include_once('/core/lib/datefractal.lib.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');

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


$langs->load("poa");
$langs->load("other");

//codLaiwett -> Metodos que se ejecutan en la pagina?

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');
$fk_member = GETPOST('fk_member');
$type = GETPOST('type');
$date_a = dol_mktime(12,0,0,GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
$aDate = dol_getdate($date_a);
$aDateAnt = dol_get_prev_day($aDate['mday'],$aDate['mon'],$aDate['year']);
$date_ini = dol_mktime(23,59,59,$aDateAnt['month'],$aDateAnt['day'],$aDateAnt['year'],'user');
$date_b = dol_mktime(23,59,59,GETPOST('db_month'),GETPOST('db_day'),GETPOST('db_year'));
$date_fin = dol_mktime(23,59,59,GETPOST('db_month'),GETPOST('db_day'),GETPOST('db_year'));
$id		= GETPOST('id','int');
if ($action != 'excel')
{
	if (empty($date_a)) $action = '';
	if (empty($date_b)) $action = '';
	if (empty($type)) $action = '';
}
//$nivel  = GETPOST('nivel');
//if ($nivel == 1)
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
/* Para capturar los datos */
if (!isset($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];

// Proteccion para usuarios externos
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}



// Objeto de la Pdepartamentext
//$object = new Poareformulatedext($db);
//$objFormulateddet = new Poareformulateddet($db);
$object = new Licencesext($db);
$objUser  = new User($db);
$objAssistance = new Assistance($db);
$objAdherent = new Adherentext($db);
$objCuser = new Puser($db);
$objAssistancedef = new Assistancedef($db);
$objMemberVacation = new Membervacation($db);
$objMemberVacDet = new Membervacationdet($db);

if ($conf->salary->enabled)
	$objContrato = new Pcontract($db);
else
{
	setEventMessages($langs->trans('No esta habilitado el módulo Salary'),null,'errors');
}

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

/**********************************************************************************
* COMPORTAMIENTO
*
* Ponga aquí todo el código para hacer de acuerdo al valor del parámetro "acción"
**********************************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action); //Tenga en cuenta que $ action y $ object pueden haber sido modificados por algunos ganchos

if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('poa/poa',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Selection of new fields
	//include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';


	if ($action == 'builddoc')
	{
		//$id = $_SESSION['idEntrepot'];

		$ref = 1;
		$object->fetch($ref);

		if (empty($object->ref))
			$object->ref = $ref;
		//$object->fetch_thirdparty();
		//$object->fetch_lines();
		if (GETPOST('model'))
		{
			//$object->setDocModel($user, GETPOST('model'));
			$object->modelpdf = GETPOST('model');
		}

	// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$result=assistance_pdf_create($db, $object, $object->modelpdf, $outputlangs);

		//codprueba_pdf_create(DoliDB $db, Commande $object, $modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			//header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
			header('Location: '.$_SERVER["PHP_SELF"]);
			//exit;
		}
	}
	if ($action == 'excel')
	{
		$aDatosAdd = unserialize($_SESSION['aDatosAdd']);
		$aLicencias = unserialize($_SESSION['aLicencias']);
		$aVacacion = unserialize($_SESSION['aVacacion']);

		if($aDatosAdd['licvac']==2){
				//Manejo de estilos para las celdas
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
			if($aDatosAdd['fk_member']!= (-1)){
				$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('LISTADO DE VACACIONES POR MIEMBRO'));
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('LISTADO DE VACACIONES SOLICITADAS'));
			}

			//$objPHPExcel->getStyle('A1')->getFont()->setSize(14);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:K1');
			//$objPHPExcel->getActiveSheet()->getStyle('A1:I3')->applyFromArray($styleThickBrownBorderOutline);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:K1');

			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);

			if($aDatosAdd['fk_member']!= (-1)){
				$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('DE :'));
				$objPHPExcel->getActiveSheet()->SetCellValue('B3',$aDatosAdd['nomMember']);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3'.$line)->getAlignment()->applyFromArray(
					array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
				);
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('DESDE FECHA :'));
				$objPHPExcel->getActiveSheet()->SetCellValue('B2',dol_print_date($aDatosAdd['fecha_a']),'daytext');

				$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('HASTA FECHA :'));
				$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aDatosAdd['fecha_b']),'daytext');
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3'.$line)->getAlignment()->applyFromArray(
					array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
				);
			}

			//titulos
			$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('Ref'));
			$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('CI'));
			$objPHPExcel->getActiveSheet()->SetCellValue('C7',$langs->trans('Paterno'));
			$objPHPExcel->getActiveSheet()->SetCellValue('D7',$langs->trans('Materno'));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('D7')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7');
			$objPHPExcel->getActiveSheet()->SetCellValue('E7',$langs->trans('Nombre'));
			if($aDatosAdd['fk_member']!= (-1)){
				$objPHPExcel->getActiveSheet()->SetCellValue('F7',$langs->trans('Fecha de Ingreso'));
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('F7',$langs->trans('Fecha de Solicitud'));
			}

			if($aDatosAdd['fk_member']!= (-1)){
				$objPHPExcel->getActiveSheet()->SetCellValue('G7',$langs->trans('Gestion'));
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('G7',$langs->trans('Dias Solicitados'));
			}

			$objPHPExcel->setActiveSheetIndex(0)->getStyle('G7')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('E7:F7');
			$objPHPExcel->getActiveSheet()->SetCellValue('H7',$langs->trans('Total dias Vacacion Asignado'));
			$objPHPExcel->getActiveSheet()->SetCellValue('I7',$langs->trans('Total dias Vacacion Utilizados'));
			$objPHPExcel->getActiveSheet()->SetCellValue('J7',$langs->trans('Saldo'));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('J7')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('G7:H7');
			$objPHPExcel->getActiveSheet()->SetCellValue('K7',$langs->trans('Estado'));

			$objPHPExcel->getActiveSheet()->getStyle('A7:K7')->applyFromArray(
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

			//FORMATO
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K7')->getFont()->setBold(true);

			//cambiamos de fila
			$line = 8;

			foreach ((array)$aVacacion AS $j => $row)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['ci']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['paterno']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['materno']);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['nombre']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['campoUno']);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$row['campoDos']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$row['diasAsig']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,$row['diasUtil']);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.$line,$row['saldo']);
				$objPHPExcel->getActiveSheet()->SetCellValue('K'.$line,$row['estado']);
				$line++;
			}

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
		//$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		//
		//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save("excel/repVacacion.xlsx");
		//echo 'Llega hasta aqui';
			header('Location: '.DOL_URL_ROOT.'/assistance/report/fiche_export.php?archive=repVacacion.xlsx');
		}
		else{
				//Manejo de estilos para las celdas
			$styleThickBrownBorderOutline = array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THICK,
						'color' => array('argb' => 'FFA0A0A0'),
					),
				),
			);
				//PRCESO 1
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
				//armamos la cabecera
			$objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('LISTADO DE LICENCIAS SOLICITADAS'));


				//$objPHPExcel->getStyle('A1')->getFont()->setSize(14);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:J1');
				//$objPHPExcel->getActiveSheet()->getStyle('A1:I3')->applyFromArray($styleThickBrownBorderOutline);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:J1');

			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);

			$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('DESDE FECHA :'));
			$objPHPExcel->getActiveSheet()->SetCellValue('B2',dol_print_date($aDatosAdd['fecha_a']),'daytext');

			$objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('HASTA FECHA :'));
			$objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($aDatosAdd['fecha_b']),'daytext');
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3'.$line)->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
			);

				//titulos
			$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('Ref'));
			$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('CI'));
			$objPHPExcel->getActiveSheet()->SetCellValue('C7',$langs->trans('Paterno'));
			$objPHPExcel->getActiveSheet()->SetCellValue('D7',$langs->trans('Materno'));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('D7')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
				//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7');
			$objPHPExcel->getActiveSheet()->SetCellValue('E7',$langs->trans('Nombre'));
			$objPHPExcel->getActiveSheet()->SetCellValue('F7',$langs->trans('Desde Fecha'));
			$objPHPExcel->getActiveSheet()->SetCellValue('G7',$langs->trans('Hasta Fecha'));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('G7')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
				//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('E7:F7');
			$objPHPExcel->getActiveSheet()->SetCellValue('H7',$langs->trans('Fecha y Hora Salida'));
			$objPHPExcel->getActiveSheet()->SetCellValue('I7',$langs->trans('Fecha y Hora de Retorno'));
			$objPHPExcel->getActiveSheet()->SetCellValue('J7',$langs->trans('Estado'));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('J7')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
				//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('G7:H7');
				//$objPHPExcel->getActiveSheet()->SetCellValue('J7',$langs->trans('Estado'));

			$objPHPExcel->getActiveSheet()->getStyle('A7:J7')->applyFromArray(
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
				//FORMATO
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J7')->getFont()->setBold(true);


				//cambiamos de fila
			$line = 8;

			foreach ((array)$aLicencias AS $j => $row)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['ref']);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['ci']);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['paterno']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['materno']);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['nombre']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['date_ini']);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$row['date_fin']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,$row['date_ini_ejec']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.$line,$row['date_fin_ejec']);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.$line,$row['estado']);
				$line++;
			}

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
				//$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
				//
				//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save("excel/repLicencias.xlsx");
				//echo 'Llega hasta aqui';
			header('Location: '.DOL_URL_ROOT.'/assistance/report/fiche_export.php?archive=repLicencias.xlsx');
		}


	}
}

/****************************************************
* VER
*
* Ponga aquí todo el código para construir la página
*****************************************************/
//$now=dol_now();

$form=new Form($db);
$formadd=new Formadd($db);
$formfile = new Formfile($db);


//$help_url="ES: Module_Customers_Orders | FR: Module_Commandes_Clients | ES: Módulo_Pedidos_de_cliente";
$help_url='';
$title = $langs->trans('Reformulated');
llxHeader('', $title, $help_url);

// Ponga aquí el contenido de su página

// Ejemplo: Añadir código jquery
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';
/* codLaiwett Aqui viene el formulario que se encuentra en la pagina */

print load_fiche_titre($langs->trans("Reporte de Licencias y Vacaciones"));

if ($conf->salary->enabled)
{


	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="genReporte">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";

	print '<tr><td class="fieldrequired">'.$langs->trans("Period").'</td><td><input class="flat" type="number" name="period_year" value="'.(GETPOST('period_year')?GETPOST('period_year'):$period_year).'"></td></tr>';

	print '<tr><td class ="fieldrequired">'.$langs->trans("Consultar las ").'</td><td>';
	print '<input class="flat" type="radio" name="type" value="1" '.(GETPOST("type")=="1"?"checked":"") .' ><label>'.$langs->trans("Licencias").'<label></br>';
	print '<input class="flat" type="radio" name="type" value="2" '.(GETPOST("type")=="2"?"checked":"") .' ><label>'.$langs->trans("Vacaciones").'<label></br>';
	print '</td></tr>';

	print '<tr><td width="15%">'.$langs->trans("Miembro(s)").'</td><td colspan="2">';
	print $formadd->select_member($fk_member,'fk_member','',1,'','','','','autofocus');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Desde Fecha").'</td><td>';
	print $form->select_date((empty($date_a)?dol_now():$date_a),'da_',0,0,1,'date_a',1,1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Hasta Fecha").'</td><td>';
	print $form->select_date((empty($date_b)?dol_now():$date_b),'db_',0,0,1,'date_b',1,1);
	print '</td></tr>';


	/* endCodLaiwett */

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="butAction" name="consultar" value="Consultar" > </div>';

	print '</form>';
}
else
{
	print '<div>';
	print $langs->trans('Itisnotpossibletoprocesstheinformation');
	print '<br>'.$langs->trans('Enablethemodule').' "'.$langs->trans('SALARY').'" ';
	print '</div>';
}

//Creamos una variable se session para mandar de que departamento es el reporte

if ($action == 'genReporte') {
    //$date_a = dol_mktime(23,59,59,$_POST['da_month'],$_POST['da_day'],$_POST['da_year'],'user');
	//$date_b = dol_mktime(23,59,59,$_POST['db_month'],$_POST['db_day'],$_POST['db_year'],'user');
	include DOL_DOCUMENT_ROOT.'/assistance/tpl/lic_vac_list.tpl.php';
}

// End of page
llxFooter();
$db->close();
?>