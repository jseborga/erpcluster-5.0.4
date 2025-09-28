<?php
/* Common No One  7102 <example@gmail.com>
 */

/**
 *	uso de dos view en una sola
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';


require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsresource.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mequipment.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assets.class.php");
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsbeen.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");


require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

//Clase tracing
dol_include_once('/assets/class/assetstracing.class.php');

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
$langs->load("assets");

$action = GETPOST('action');
$view   = GETPOST('view','int');
$id     = GETPOST("id",'int');
$idr     = GETPOST("idr",'int');
$idCard     = GETPOST("idCard",'int');
$idEdit     = GETPOST("idEdit",'int');
//$view3     = GETPOST("view3",'int');
$ref    = GETPOST('ref');
$dater  = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

$confirm     = GETPOST('confirm');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

//echo "action : ".$action;
$search_fk_asset=GETPOST('search_fk_asset','int');
$search_fk_user_resp=GETPOST('search_fk_user_resp','int');
$search_been=GETPOST('search_been','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');




if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'assets', $id);


$objectAsstra = new Assetstracing($db);


$mesg = '';
$object  = new Assetsext($db);
$objUser = new User($db);
$extrafields = new ExtraFields($db);

$objectDos      	= new Mjobsext($db);
$objEntity   		= new Entity($db);
$objEquipos  		= new Mequipment($db);
$objActivos  		= new Assets($db);
$objRecursos 		= new Mjobsresource($db);
$formfile    		= new FormFile($db);
$form        	    = new Formv($db);
$MjobsresourceLine  = new MjobsresourceLine($db);
$objCassetsbeen = new Cassetsbeen($db);
$objAdherent = new Adherent($db);
$entity = $conf->entity;
$objUser = new User($db);

if ($id>0)
{
	$res = $object->fetch($id,((empty($id) && !empty($ref))?$ref:null));
	if ($res>0) $id = $object->id;
}
if ($idr>0)
{
	$res = $objectAsstra->fetch($idr);
}
if ($action == 'search')
	$action = 'createedit';
$now = dol_now();

if (!isset($_SESSION['period_year']))
$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

/*
 * Actions
 */
$now = dol_now();
$parameters=array();
 $reshook=$hookmanager->executeHooks('doActions',$parameters,$objectAsstra,$action);    // Note that $action and $object may have been modified by some hooks
 if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

 if (empty($reshook))
 {
 	if ($cancel)
 	{
 		if ($action != 'addlink')
 		{
 			$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/tracing/tracing.php?id='.$id,1);
 			header("Location: ".$urltogo);
 			exit;
 		}
 		if ($id > 0 || ! empty($ref)) $ret = $objectAsstra->fetch($id,$ref);
 		$action='';
 	}

	 // Action to add record
 	if ($action == 'add')
 	{

 		if (GETPOST('cancel'))
 		{
 			$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/tracing/tracing.php?id='.$id,1);
 			header("Location: ".$urltogo);
 			exit;
 		}

 		$error=0;

 		/* object_prop_getpost_prop */

	 //$object->fk_asset=GETPOST('fk_asset','int');
 		$date_ini  = dol_mktime(0, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
 		$objectAsstra->fk_asset = $id;
 		$objectAsstra->fk_user_resp = GETPOST('fk_user_resp');
 		$objectAsstra->been=GETPOST('been','alpha');
 		$objectAsstra->description=GETPOST('description','alpha');
 		$objectAsstra->fk_user_create = $user->id;
 		$objectAsstra->fk_user_mod = $user->id;
 		$objectAsstra->dater =  $date_ini;
 		$objectAsstra->datec = dol_now();
 		$objectAsstra->datem = dol_now();
 		$objectAsstra->tms   = dol_now();
	 //$object->fk_user_create=GETPOST('fk_user_create','int');
 		$objectAsstra->status= 0;



 		if ($objectAsstra->fk_user_resp<=0)
 		{
 			$error++;
 			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_user_resp")), null, 'errors');
 		}
 		if (empty($objectAsstra->been))
 		{
 			$error++;
 			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldbeen")), null, 'errors');
 		}

 		if (! $error)
 		{
 			$result=$objectAsstra->create($user);
 			if ($result > 0)
 			{
				 // Creation OK
 				$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/tracing/tracing.php?id='.$id,1);
 				header("Location: ".$urltogo);
 				setEventMessages("Satisfactoryrecord", null, 'mesgs');
 				exit;
 			}
 			{
				 // Creation KO
 				if (! empty($objectAsstra->errors)) setEventMessages(null, $objectAsstra->errors, 'errors');
 				else  setEventMessages($objectAsstra->error, null, 'errors');
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
 		if (GETPOST('cancel'))
 		{
 			$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/tracing/tracing.php?id='.$id,1);
 			header("Location: ".$urltogo);
 			exit;
 		}
 		$error=0;
 		$date_ini  = dol_mktime(0, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
 		$res = $objectAsstra->fetch(GETPOST('idEdit'));
 		if ($res >0)
 		{
 			$objectAsstra->fk_user_resp=GETPOST('fk_user_resp','int');
 			$objectAsstra->been=GETPOST('been','alpha');
 			$objectAsstra->description=GETPOST('description','alpha');
 			$objectAsstra->dater =  $date_ini;
 			$objectAsstra->datem =  $now;
 			$objectAsstra->fk_user_mod=$user->id;


 			if ($objectAsstra->fk_user_resp<=0)
 			{
 				$error++;
 				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_user_resp")), null, 'errors');
 			}
 			if (empty($objectAsstra->been))
 			{
 				$error++;
 				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldbeen")), null, 'errors');
 			}

 			if (! $error)
 			{
 				$result=$objectAsstra->update($user);
 				if ($result > 0)
 				{

 					setEventMessages("Modificado con exito", null, 'mesgs');
 					$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/tracing/tracing.php?id='.$id,1);
 					header("Location: ".$urltogo);
				//exit;
 				}
 				else
 				{
				 // Creation KO
 					if (! empty($objectAsstra->errors)) setEventMessages(null, $objectAsstra->errors, 'errors');
 					else setEventMessages($objectAsstra->error, null, 'errors');
 					$action='edit';
 				}
 			}
 			else
 			{
 				$action='';
 			}
 		}
 	}
	 // Action to delete
 	if ($action == 'confirm_validate' && $confirm == 'yes' && $user->rights->assets->tra->val)
 	{
 		$objectAsstra->status = 1;
 		$result=$objectAsstra->update($user);
 		if ($result > 0)
 		{
			 // Delete OK
 			setEventMessages($langs->trans('Satisfactoryvalidation'), null, 'mesgs');
 		}
 		else
 		{
 			if (! empty($objectAsstra->errors)) setEventMessages(null, $objectAsstra->errors, 'errors');
 			else setEventMessages($objectAsstra->error, null, 'errors');
 		}
 		$action = '';
 	}
	 // Action to delete
 	if ($action == 'confirm_novalidate' && $confirm == 'yes' && $user->rights->assets->tra->val)
 	{
 		$objectAsstra->status = 0;
 		$result=$objectAsstra->update($user);
 		if ($result > 0)
 		{
			 // Delete OK
 			setEventMessages($langs->trans("Satisfactorynovalidation"), null, 'mesgs');
 		}
 		else
 		{
 			if (! empty($objectAsstra->errors)) setEventMessages(null, $objectAsstra->errors, 'errors');
 			else setEventMessages($objectAsstra->error, null, 'errors');
 		}
 		$action = '';
 	}

	 // Action to delete
 	if ($action == 'confirm_delete')
 	{
 		$objectAsstra->id = GETPOST('idr');
 		$result=$objectAsstra->delete($user);
 		if ($result > 0)
 		{
			 // Delete OK
 			setEventMessages("Record//excel para una versión anterior
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
				 include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';Deleted", null, 'mesgs');
 			header("Location: ".dol_buildpath('/assets/tracing/tracing.php?id='.$id,1));
 			exit;
 		}
 		else
 		{
 			if (! empty($objectAsstra->errors)) setEventMessages(null, $objectAsstra->errors, 'errors');
 			else setEventMessages($objectAsstra->error, null, 'errors');
 		}
 		$action = '';
 	}

	 if ($action == 'excel')
	 {

		 $aReporte = unserialize($_SESSION['aReporte']);

		 $headerss = $aReporte[2];
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
		 $objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans("TRACING"));
			 //$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
		 $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
			 array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		 );

		 //Encabezados


		 $objPHPExcel->getActiveSheet()->SetCellValue('A3',html_entity_decode($langs->trans('Code')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B3',$headerss['ref']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:E3');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A4',html_entity_decode($langs->trans('Group')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B4',$headerss['grupo']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B4:E4');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A5',html_entity_decode($langs->trans('Item')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B5',$headerss['intem']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:E5');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A6',html_entity_decode($langs->trans('Qualifiter')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B6',$headerss['calificacion']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:E6');

		 $objPHPExcel->getActiveSheet()->SetCellValue('A7',html_entity_decode($langs->trans('Description')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B7',$headerss['descripcion']);
		 $objPHPExcel->getActiveSheet()->getColumnDimension('B7')->setAutoSize(true);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B7:E7');


		 $objPHPExcel->getActiveSheet()->SetCellValue('A8',html_entity_decode($langs->trans('Status')));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B8',$headerss['estado']);
		 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:E8');




		 $line = 11;
			 //CABECERAS DE LA TABLA
			 $objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,html_entity_decode($langs->trans('Useresp')));
			 $objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,html_entity_decode($langs->trans('Filedbeen')));
			 $objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,html_entity_decode($langs->trans('Description')));
			 $objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,html_entity_decode($langs->trans('Date')));

			 $objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$langs->trans('Estatus'));


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
		 //$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
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
		 $objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
		 $objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setBold(true);


		 //$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
		 //$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			 $idGr = 1;
			 $line = 12;
			 foreach ( $aReporte[1] as $j => $row)
			 {

					 $objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['nombre']);
					 $objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['label']);
					 $objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['descripcion']);
					 $objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['fecha']);
					 $objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
					 $objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,$row['estado']);
					 //$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

					 $line++;
			 }



			 $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			 $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			 $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			 $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			 $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			 //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

			 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

			 $objWriter->save("excel/tracing.xlsx");
			 header('Location: '.DOL_URL_ROOT.'/assets/tracing/fiche_export.php?archive=tracing.xlsx');
		 }
}

 if ($_POST["cancel"] == $langs->trans("Cancel"))
 {
 	$action = '';
 	$_GET["id"] = $_POST["id"];
 }

/*
 * View
 */

$formfile = new Formfile($db);

$aArrcss= array('assets/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';
llxHeader("",$langs->trans("Assets"),$help_url,'','','','',$aArrcss);

/* codLaiwett confirm_delete */
/* aqui ponemos el codigo de mensaje para poder confirmar si queremos eliminar
Nota al parecer se debe llenar todos los datos que te pide la funcion fromconfirm */



if ($id>0)
{

	$aCabecera = array();
	$aAssetId = unserialize($_SESSION['aAssetId']);

	if (empty($aAssetId['id']) && ($id || $ref))
	{

		$head=assets_prepare_head($object);
		$tabn = 'Tracing';

		dol_fiche_head($head, $tabn, $langs->trans("Assets"),0,($object->public?'projectpub':'project'));

		if ($action == 'delete') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?idr='.$idr.'&id='.$id, $langs->trans('Deletetracing'), $langs->trans('ConfirmDeletetracing'),'confirm_delete', '', 0, 1);
			print $formconfirm;
		}
		if ($action == 'validate') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?idr='.$idr.'&id='.$id, $langs->trans('Validatetracing'), $langs->trans('ConfirmValidatetracing'),'confirm_validate', '', 0, 1);
			print $formconfirm;
		}
		if ($action == 'novalidate') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?idr='.$idr.'&id='.$id, $langs->trans('Novalidatetracing'), $langs->trans('ConfirmNovalidatetracing'),'confirm_novalidate', '', 0, 1);
			print $formconfirm;
		}

		print '<table class="border" style="min-width=1000px" width="100%">';

		// ref

		$linkback = '<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';


		print '<tr><td width="15%">'.$langs->trans('Code').'</td>';
		print '<td colspan="2">';
		print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref','');
		$aCabecera['ref'] = $object->ref;
		print '</td>';
		print '</tr>';

		//group type
		print '<tr><td width="15%">'.$langs->trans('Group').'</td><td colspan="2">';
		print select_type_group($object->type_group,'type_group','',1,1,'code');
		$aCabecera['grupo'] = select_type_group($object->type_group,'type_group','',1,1,'code');
		print '</td></tr>';

		//ref item
		print '<tr><td width="15%">'.$langs->trans('Item').'</td><td colspan="2">';
		print $object->item_asset;
		$aCabecera['intem'] = $object->item_asset;
		print '</td></tr>';

		//patrim type
		print '<tr><td width="15%">'.$langs->trans('Clasification').'</td><td colspan="2">';
		if (!empty($object->type_patrim)){}
			print select_type_patrim($object->type_patrim,'type_patrim','',0,1,'code');
			$aCabecera['calificacion'] = select_type_patrim($object->type_patrim,'type_patrim','',0,1,'code');}
		else
			print '&nbsp;';
		print '</td></tr>';

		//detail
		print '<tr><td width="15%">'.$langs->trans('Detail').'</td><td colspan="2">';
		print $object->descrip;
		$aCabecera['descripcion'] = $object->descrip;
		print '</td></tr>';


		//Status
		print '<tr><td width="15%">'.$langs->trans('Statut').'</td><td colspan="2">';
		print $object->getLibStatut(4);
		$aCabecera['estado'] = $object->getLibStatut(0);
		print '</td></tr>';

		print '</table>';

		if($action == 'edit'){
			include_once DOL_DOCUMENT_ROOT."/assets/tracing/tpl/card.tpl.php";

		}else{
			if($action == 'create'){
				$action = "create";
				include_once DOL_DOCUMENT_ROOT."/assets/tracing/tpl/card.tpl.php";

			}else{
				include_once DOL_DOCUMENT_ROOT."/assets/tracing/tpl/tracing_list.tpl.php";
			}
		}

		//dol_fiche_end();

		/* ************************************** */
		/*                                        */
		/* Barre d'action                         */
		/*                                        */
		/* ************************************** */

		print "<div class=\"tabsAction\">\n";

		print "</div>";
	}


llxFooter();

$db->close();
?>
