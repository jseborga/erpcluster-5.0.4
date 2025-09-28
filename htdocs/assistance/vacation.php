<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/adherents/class/adherent_type.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
dol_include_once('/assistance/lib/assistance.lib.php');
dol_include_once('/assistance/lib/utils.lib.php');

dol_include_once('/core/lib/datefractal.lib.php');

dol_include_once('/assistance/class/membervacationext.class.php');
dol_include_once('/assistance/class/membervacationdet.class.php');
dol_include_once('/assistance/class/ctypelicenceext.class.php');

dol_include_once('/user/class/user.class.php');

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

// Load traductions files requiredby by page
$langs->load("assistance");
$langs->load("companies");
$langs->load("members");
$langs->load("other");
// Get parameters
$idd = GETPOST('idd','int');
$id		= GETPOST('rowid','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$confirm = GETPOST('confirm');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$date_ini = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin = dol_mktime(0,0,0,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

if ($user->societe_id > 0)
{
	accessforbidden();
}


// Load object if id or ref is provided as parameter
$object=new Adherentext($db);
$objAssistancedef=new Assistancedef($db);
$objMembervacation = new Membervacationext($db);
$objMembervacationdet = new Membervacationdet($db);
$objCtypelicence = new Ctypelicenceext($db);


$objectLicence = new Licencesext($db);
$formfile = new Formfile($db);

//revisamos por el id member
if ($id>0)
{
	//$result=$object->fetchAll('','',1,0,array('fk_reg'=>$id,'statut'=>1));
	$result = $object->fetch($id);
	if ($result < 0)
	{
		dol_print_error($db);
	}
	$filter = " AND t.fk_reg = ".$id;
	$result = $objAssistancedef->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	if ($result < 0)
	{
		dol_print_error($db);
	}
	else
	{
		$idd = $objAssistancedef->id;
		if($idd && empty($action))
			$action='view';
	}
}
if ($idr>0)
{
	//$result=$object->fetchAll('','',1,0,array('fk_reg'=>$id,'statut'=>1));
	$result = $objMembervacation->fetch($idr);
	if ($result < 0)
	{
		dol_print_error($db);
	}
}
if (($idd > 0) && $action != 'add')
{
	$result=$objAssistancedef->fetch($idd);
	if ($result < 0)
		dol_print_error($db);
	else
	{
		$id = $objAssistancedef->fk_reg;
		if(empty($action))
			$action= 'view';
	}
}

if (empty($action) && empty($idd)) $action='create';

$typemarking = new Typemarking($db);
$adherent=new Adherentext($db);
$membert=new AdherentType($db);
$objUser=new User($db);
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistancedef'));
$extrafields = new ExtraFields($db);

if ($action == 'excel')
	{
		$aDatos      = unserialize($_SESSION['aDatos']);
		$aVacacion   = unserialize($_SESSION['aVacacion']);
		$aLicencias  = unserialize($_SESSION['aLicencias']);

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

		$objPHPExcel->getActiveSheet()->SetCellValue('A1',html_entity_decode($langs->trans('Holidaycontrol')));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);

		//Encabezados
		$objPHPExcel->getActiveSheet()->SetCellValue('A3',html_entity_decode($langs->trans('Firstname')));
		$objPHPExcel->getActiveSheet()->SetCellValue('B3',html_entity_decode($aDatos['nombres']));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:E3');

		$objPHPExcel->getActiveSheet()->SetCellValue('A4',html_entity_decode($langs->trans('Lastname')));
		$objPHPExcel->getActiveSheet()->SetCellValue('B4',html_entity_decode($aDatos['apellidos']));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:E4');

		$objPHPExcel->getActiveSheet()->SetCellValue('A5',html_entity_decode($langs->trans('Naturaleza')));
		$objPHPExcel->getActiveSheet()->SetCellValue('B5',html_entity_decode($aDatos['naturaleza']));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:E5');

		$objPHPExcel->getActiveSheet()->SetCellValue('A6',html_entity_decode($langs->trans('Type')));
		$objPHPExcel->getActiveSheet()->SetCellValue('B6',html_entity_decode($aDatos['tipo']));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:E6');

		$objPHPExcel->getActiveSheet()->SetCellValue('A7',html_entity_decode(html_entity_decode($langs->trans('Entity'))));
		$objPHPExcel->getActiveSheet()->SetCellValue('B7',html_entity_decode($aDatos['compania']));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B7:E7');

		//CABECERAS DE LA TABLA
		$line = 9;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,html_entity_decode($langs->trans('Assignedvacation')));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->getFont()->setBold(true);

		//CABECERAS DE LA TABLA
		$line = 10;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,html_entity_decode($langs->trans("Fieldvalidfrom")));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,html_entity_decode($langs->trans("Fieldvaliduntil")));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,html_entity_decode($langs->trans("Fieldgestion")));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,html_entity_decode($langs->trans("Fielddays_assigned")));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,html_entity_decode($langs->trans("Fielddays_used")));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,html_entity_decode($langs->trans("Fieldstatus")));


		$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':F'.$line)->applyFromArray(
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
		$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);

		/*$objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setBold(true);*/


		//$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
		//$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

		$line = 11;

		foreach ( $aVacacion as $i => $row)
		{

			if(!empty($row['inicio'])){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,dol_print_date($row['inicio'],'day'));
			}

			if(!empty($row['fin'])){
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,dol_print_date($row['fin'],'day'));
			}


			//$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['gestion']);
			//$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['asignados']);

			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['usados']);

			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,$row['estado']);

			$line++;

		}
		$line--;
		$objPHPExcel->getActiveSheet()->getStyle('A11:F'.$line)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

		/**************************************************************************************************/
		$line = $line + 2;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,html_entity_decode($langs->trans('Requestedvacation')));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->getFont()->setBold(true);
		$line = $line + 1;
		//CABECERAS DE LA TABLA
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,html_entity_decode($langs->trans("Fieldref")));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,html_entity_decode($langs->trans("Fielddetail")));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,html_entity_decode($langs->trans("Fielddate_ini")));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,html_entity_decode($langs->trans("Fielddate_fin")));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,html_entity_decode($langs->trans("Fieldstatut")));


		$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':E'.$line)->applyFromArray(
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

		$line++;
		$auxLine = $line;

		foreach ( $aLicencias as $j => $rows)
		{

			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$rows['ref']);

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$rows['detalle']);

			if(!empty($row['inicio'])){
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,dol_print_date($rows['inicio'],'day'));
			}

			if(!empty($row['fin'])){
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,dol_print_date($rows['fin'],'day'));
			}


			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$rows['estado']);

			$line++;

		}

		$line--;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$auxLine.':E'.$line)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);



		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		$objWriter->save("excel/vacation".$aDatos['nombres'].".xlsx");
		//$_SESSION['docsave'] =  "vacation".$aDatos['nombres'].".xlsx";
		$_SESSION['doc'] =  "vacation".$aDatos['nombres'].".xlsx";
		//header('Location: '.DOL_URL_ROOT.'/assistance/fiche_export.php?archive=vacation'.$aDatos["nombres"].'.xlsx');
		header('Location: '.DOL_URL_ROOT.'/assistance/fiche_export.php?doc=vacation'.$aDatos["nombres"].'.xlsx');
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
$now = dol_now();
if (empty($reshook))
{

	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/vacation.php?rowid='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/vacation.php?rowid='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objMembervacation->fk_member=$id;
		$objMembervacation->date_ini=$date_ini;
		$objMembervacation->date_fin=$date_fin;
		$objMembervacation->period_year=GETPOST('period_year','int');
		$objMembervacation->days_assigned=GETPOST('days_assigned','int');
		$objMembervacation->days_used=GETPOST('days_used','int')+0;
		$objMembervacation->fk_user_create=$user->id;
		$objMembervacation->fk_user_mod=$user->id;
		$objMembervacation->datec = $now;
		$objMembervacation->datem = $now;
		$objMembervacation->status=0;


		if (empty($objMembervacation->period_year))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldperiod_year")), null, 'errors');
		}
		if (empty($objMembervacation->days_assigned))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fielddays_assigned")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objMembervacation->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/vacation.php?rowid='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objMembervacation->errors)) setEventMessages(null, $objMembervacation->errors, 'errors');
				else  setEventMessages($objMembervacation->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;

		$objMembervacation->period_year=GETPOST('period_year','int');
		$objMembervacation->days_assigned=GETPOST('days_assigned','int');
		$objMembervacation->fk_user_mod=$user->id;
		$objMembervacation->datem = $now;

		if (empty($objMembervacation->period_year))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldperiod_year")), null, 'errors');
		}
		if (empty($objMembervacation->days_assigned))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fielddays_assigned")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objMembervacation->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objMembervacation->errors)) setEventMessages(null, $objMembervacation->errors, 'errors');
				else setEventMessages($objMembervacation->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// confirm_validate
	if ($action == 'confirm_validate' && $confirm == 'no')
	{
		$action = '';
	}
	if ($action == 'confirm_validate' && $confirm == 'yes' && $user->rights->assistance->vac->app)
	{
		$res = $objMembervacation->fetch($idr);
		if ($res==1)
		{
			$objMembervacation->status =1;
			$objMembervacation->fk_user_mod = $user->id;
			$objMembervacation->fk_user_app = $user->id;
			$objMembervacation->datem = $now;
			$objMembervacation->datea = $now;
			$res = $objMembervacation->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
			}
			else
			{
				setEventMessages($langs->trans('Successfullyvalidated'),null,'mesgs');
			}
		}
		else
		{
			setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
		}
		$action = '';
	}


	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objMembervacation->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistance/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objMembervacation->errors)) setEventMessages(null, $objMembervacation->errors, 'errors');
			else setEventMessages($objMembervacation->error, null, 'errors');
		}
	}

}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Vacations'),'');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
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


//mostramos al miembro
$aDatos = array();

if ($id>0)
{
	$adherent->fetch($id);
	$result=$membert->fetch($adherent->typeid);
	if ($result > 0)
	{
	/*
	 * Affichage onglets
	 */
	if (! empty($conf->notification->enabled))
		$langs->load("mails");

	$head = member_prepare_head($adherent);

	$form=new Form($db);

	dol_fiche_head($head, 'vacation', $langs->trans("Member"),0,'user');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/adherents/list.php">'.$langs->trans("BackToList").'</a>';

		// Ref
	print '<tr><td width="20%">'.$langs->trans("Ref").'</td>';
	print '<td class="valeur">';
	print $form->showrefnav($adherent, 'rowid', $linkback);
	$aDatos['nombre'] = $adherent->first_name;
	print '</td></tr>';

		// Login
	if (empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED))
	{
		print '<tr><td>'.$langs->trans("Login").' / '.$langs->trans("Id").'</td><td class="valeur">'.$adherent->login.'&nbsp;</td></tr>';
	}

		// Morphy
	print '<tr><td>'.$langs->trans("Nature").'</td><td class="valeur" >'.$adherent->getmorphylib().'</td>';
	$aDatos['naturaleza'] = $adherent->getmorphylib();
		/*print '<td rowspan="'.$rowspan.'" align="center" valign="middle" width="25%">';
	 print $form->showphoto('memberphoto',$object);
	 print '</td>';*/
	 print '</tr>';

		// Type
	 print '<tr><td>'.$langs->trans("Type").'</td><td class="valeur">'.$membert->getNomUrl(1)."</td></tr>\n";
	 //$aDatos['tipo'] = $membert->getNomUrl(1);
	 $aDatos['tipo'] = $membert->label;
		// Company
	 print '<tr><td>'.$langs->trans("Company").'</td><td class="valeur">'.$adherent->societe.'</td></tr>';
	 $aDatos['compania'] = $adherent->societe;
		// Civility
	 print '<tr><td>'.$langs->trans("UserTitle").'</td><td class="valeur">'.$adherent->getCivilityLabel().'&nbsp;</td>';
	 print '</tr>';
	 $aDatos['cortesia'] = $adherent->getCivilityLabel();
		// Lastname
	 print '<tr><td>'.$langs->trans("Lastname").'</td><td class="valeur">'.$adherent->lastname.'&nbsp;</td>';
	 print '</tr>';
	 $aDatos['apellidos'] = $adherent->lastname;
		// Firstname
	 print '<tr><td>'.$langs->trans("Firstname").'</td><td class="valeur">'.$adherent->firstname.'&nbsp;</td>';
	 print '</tr>';
	 $aDatos['nombres'] = $adherent->firstname;
		// Status
	 print '<tr><td>'.$langs->trans("Status").'</td><td class="valeur">'.$adherent->getLibStatut(4).'</td></tr>';
	 $aDatos['estado'] = $adherent->getLibStatut(0);
	 print '</table>';
	 dol_fiche_end();
	}



	$_SESSION['aDatos'] = serialize($aDatos);
	echo "<pre>";
	//print_r ($aDatos);
	echo "</pre>";



	include DOL_DOCUMENT_ROOT.'/assistance/tpl/membervacation_list.tpl.php';

	include DOL_DOCUMENT_ROOT.'/assistance/licence/tpl/licences_list.tpl.php';

	echo "<pre>";
	//print_r ($aVacacion);
	echo "</pre>";
	echo "<pre>";
	//print_r ($aLicencias);
	echo "</pre>";
}

print '<div class="tabsAction">'."\n";
print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
print '</div>'."\n";
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';

$period_year = $_SESSION['period_year'];
/*Aqui estaba el reporte*/

print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';

$filename='assistance/'.$period_year.'/vacation/'.$aDatos['nombres'];
$filedir=$conf->assistance->dir_output.'/assistance/'.$period_year.'/vacation/'.$aDatos['nombres'];
$modelpdf = "vacationuser";

$outputlangs = $langs;
$newlang = '';
if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
if (! empty($newlang)) {
	$outputlangs = new Translate("", $conf);
	$outputlangs->setDefaultLang($newlang);
}
	//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
$result=$objectLicence->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
if ($result < 0) dol_print_error($db,$result);

$urlsource=$_SERVER['PHP_SELF'];
//$genallowed=$user->rights->assistance->lic->hiddemdoc;
//$delallowed=$user->rights->assistance->lic->deldoc;
$genallowed = 0;
$delallowed = 0;
print $formfile->showdocuments('assistance',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

$somethingshown=$formfile->numoffiles;

print '</td></tr></table>';

// End of page
llxFooter();
$db->close();
