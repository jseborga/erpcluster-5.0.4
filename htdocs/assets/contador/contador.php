<?php
/* Common No One  7102 <example@gmail.com>
 */

/**
 *	EL CONTADOR "ORIGINAL"
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsresource.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mequipment.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assets.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsbeen.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

//Clase EL CONTADOR OHHHHHHHH
//dol_include_once('/assets/class/assetstracing.class.php');
dol_include_once('/assets/class/assetscontador.class.php');

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

$action  = GETPOST('action');
$view    = GETPOST('view','int');
$id      = GETPOST("id",'int');
$idCard     = GETPOST("idCard",'int');
$idEdit     = GETPOST("idEdit",'int');
//$view3     = GETPOST("view3",'int');
$ref    = GETPOST('ref');
$dater  = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

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


//$objectAsstra = new Assetstracing($db);


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
$val_coste = 0;

if ($id>0)
{
	$res = $object->fetch($id,((empty($id) && !empty($ref))?$ref:null));
	if ($res>0) $id = $object->id;
}
if ($action == 'search')
	$action = 'createedit';
$now = dol_now();

/**********************************************************
 * Actions
 **********************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objectAsstra,$action);
 // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');




if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/contador/contador.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objectAsstra->fetch($id,$ref);
		$action='';
	}

//actions
	// Remove file in doc form
if ($action == 'remove_file')
{
	if ($id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->assets->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}
	// Update
	if ($action == 'update' && $user->rights->assets->dep->writeace)
	{
		if ($object->fetch($id)>0)
		{
			$error = 0;
			$object->useful_life_two = GETPOST('useful_life_two','int');
			if (is_null($object->fk_user_mod)) $object->fk_user_mod = $user->id;
			if (is_null($object->date_mod)) $object->date_mod = dol_now();
			$object->tms = dol_now();
			if (empty($error))
			{
				$res = $object->update($user);
				if ($res > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
					exit;
				}
				else
					setEventMessages($object->error,$object->errors,'errors');
				$action = 'editlife';
			}
			else
			{
				$action="editlife";
			}
		}
	}

	if ($action == 'excel')
	{
		$aReporte = unserialize($_SESSION['aReporte']);
		$aValores = unserialize($_SESSION['aValores']);

		$objPHPExcel = new PHPExcel();
		/**/
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		 //$objPHPExcel = $objReader->load('assetscontador.xlsx');
		$objPHPExcel = $objReader->load('excel/assetscontador.xlsx');

		 // Indicamos que se pare en la hoja uno del libro
		$objPHPExcel->setActiveSheetIndex(0);

		 //$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$aEstados[$fk_estado],4=>$aTipos[$fk_tipo]);
		 //La Cabecera

		$objPHPExcel->getActiveSheet()->SetCellValue('C3' , $aValores[1]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C4' , $aValores[2]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C5' , $aValores[3]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C6' , $aValores[4]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C7' , $aValores[5]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C8' , $aValores[6]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C9' , $aValores[7]);
		$objPHPExcel->getActiveSheet()->SetCellValue('C10', $aValores[8]);




		$objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);




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

		$line = 14;

		foreach ( (array) $aReporte[1] as $j => $row)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['mes']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['anio']);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,price(price2num($row['dep_per'],'MT')));
			$objPHPExcel->getActiveSheet()->getStyle('C'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,price(price2num($row['dep_acu'],'MT')));
			$objPHPExcel->getActiveSheet()->getStyle('D'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,price(price2num($row['saldo'],'MT')));
			$objPHPExcel->getActiveSheet()->getStyle('E'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$line++;
		}



		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		$objWriter->save("excel/assetscontador1.xlsx");
		header('Location: '.DOL_URL_ROOT.'/assets/contador/excel/fiche_export.php?archive=assetscontador1.xlsx');
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

if ($id>0)
{
	$aAssetId = unserialize($_SESSION['aAssetId']);
	$flag = 0;
	if (empty($aAssetId['id']) && ($id || $ref))
	{

		$head=assets_prepare_head($object);
		//Manejo de los TABSSSSSSSSSSSSSSSSSsssSS
		$tabn = 'depracelerate';

		dol_fiche_head($head, $tabn, $langs->trans("Assets"),0,($object->public?'projectpub':'project'));


		print '<table class="border" style="min-width=1000px" width="100%">';

		// ref

		$linkback = '<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';


		print '<tr><td width="15%">'.$langs->trans('Code').'</td>';
		print '<td colspan="2">';
		print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref','');
		$codigo = $object->ref;
		print '</td>';
		print '</tr>';

		//group type
		print '<tr><td width="15%">'.$langs->trans('Group').'</td><td colspan="2">';
		print select_type_group($object->type_group,'type_group','',1,1,'code');
		$grupo = select_type_group($object->type_group,'type_group','',1,1,'code');
		print '</td></tr>';

		//detail
		print '<tr><td width="15%">'.$langs->trans('Detail').'</td><td colspan="2">';
		print $object->descrip;
		$descrip = $object->descrip;
		print '</td></tr>';

		//Costo
		print '<tr><td width="15%">'.$langs->trans('Coste').'</td><td colspan="2">';
		print price(price2num($object->coste,'MT'));
		print '</td></tr>';
		$valor_coste = $object->coste;
		//Costo residual
		print '<tr><td width="15%">'.$langs->trans('Costeresidual').'</td><td colspan="2">';
		print price(price2num($object->coste_residual,'MT'));
		$coste_res = $object->coste_residual;
		$useful_life_two = $object->useful_life_two;
		print '</td></tr>';
		if(!empty($useful_life_two)){
			$flag = 1;
		}
		//Useful_life_two
		print '<tr><td width="15%">'.$langs->trans('Useful_life').'</td><td colspan="2">';
		if ($action == 'editlife')
		{
			print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print '<input type="number" id="useful_life_two" min="0" step="any" name="useful_life_two" value="'.$object->useful_life_two.'">';
			print '<input type="submit" class="butAction" value="'.$langs->trans('Save').'">';
			print '<input type="submit" class="butAction" name="cancel" value="'.$langs->trans('Cancel').'">';
			print '</form>';
		}
		else
			print $object->useful_life_two;
		if ($user->rights->assets->dep->writeace)
		{
			if (empty($action))
				print ' '.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=editlife'.'">'.img_picto('','edit').'</a>';
		}
		$garantia = $object->useful_life_two;
		print '</td></tr>';

		//Factor de Depresiacion
		print '<tr><td width="15%">'.$langs->transnoentities("Depreciationfactor").'</td><td colspan="2">';
		//print (($object->coste_residual-$object->coste_residual)/$object->garantia);
		if($flag == 1){
			print (($object->coste-$object->coste_residual)/$object->useful_life_two);
			$fact_depre = (($object->coste-$object->coste_residual)/$object->useful_life_two);
		}

		print '</td></tr>';

		//Status
		print '<tr><td width="15%">'.$langs->trans('Status').'</td><td colspan="2">';
		print $object->getLibStatut(4);
		$estado = $object->getLibStatut(1);
		print '</td></tr>';

		print '</table>';

		if($flag == 1){
			$aValores = array(1=>$codigo,2=>$grupo,3=>$descrip,4=>$valor_coste,5=>$coste_res,6=>$garantia,7=>$fact_depre,8=>$estado);
			$_SESSION['aValores'] = serialize($aValores);
			include_once DOL_DOCUMENT_ROOT."/assets/tpl/assetscontador_list.tpl.php";
		}


		print "<div class=\"tabsAction\">\n";

		print "</div>";

	}

}
llxFooter();

$db->close();
?>
