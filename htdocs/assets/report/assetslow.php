<?php
/* Common No One  7102 <example@gmail.com>
 */
require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/assets/class/assets.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsbeen.class.php");
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
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

$langs->load("assets");

$action = GETPOST('action');
$id     = GETPOST("id",'int');
$depreciacion = GETPOST("depreciacion",'int');

$ref    = GETPOST('ref');
$dater  = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));


$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

if (empty($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];

$mesg = '';
$object  = new Assetsext($db);
$objUser = new User($db);
$extrafields = new ExtraFields($db);
$entity = $conf->entity;


$formfile    		= new FormFile($db);
$form        	    = new Formv($db);

$objCassetsbeen     = new Cassetsbeen($db);
$objCgroup          = new Cassetsgroup($db);
$objAdherent        = new Adherent($db);



/*
 * Actions
 */

 $parameters=array();
 $reshook=$hookmanager->executeHooks('doActions',$parameters,$objectAsstra,$action);    // Note that $action and $object may have been modified by some hooks
 if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

 if ($action == 'excel')
 {

		 $aReporte = unserialize($_SESSION['aReporte']);

		 $objPHPExcel = new PHPExcel();
		 /**/
		 $objReader = PHPExcel_IOFactory::createReader('Excel2007');
		 //$objPHPExcel = $objReader->load('templatemantstatus.xlsx');
		 $objPHPExcel = $objReader->load('excel/assetslow.xlsx');

		 // Indicamos que se pare en la hoja uno del libro
		 $objPHPExcel->setActiveSheetIndex(0);

		 //$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$aEstados[$fk_estado],4=>$aTipos[$fk_tipo]);
		 //La Cabecera

		 $objPHPExcel->getActiveSheet()->SetCellValue('B3', html_entity_decode($aReporte[2]));
		 $objPHPExcel->getActiveSheet()->SetCellValue('B4', dol_print_date(dol_now(),'day'));


		 $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		 $objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);




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

		 	$filter =" AND t.code = '".$j."' AND t.entity = ".$conf->entity;

			$resCG = $objCgroup->fetchAll("","",0,0,array(1=>1),"AND",$filter,true);
			if($resCG > 0){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,"REF : ".$objCgroup->label);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$line.':H'.$line)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$line.':H'.$line);
			}
			$line++;

			foreach ($row as $cg => $value)
			{

				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$value['ref']);

				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$value['ref_ext']);

				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$value['descrip']);

				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,dol_print_date($value['date_adq'],'day'));

				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$line,dol_print_date($value['date_reval'],'day'));

				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$line,price2num($value['coste'],'MT'));

				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$line,$value['useful_life']);

				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$line,dol_print_date($value['dateoff'],'day'));

				$line++;
			}
			 //$line++;
		 }


	 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	 $objWriter->save("excel/assetslow3.xlsx");
	 header('Location: '.DOL_URL_ROOT.'/assets/report/fiche_export.php?archive=assetslow3.xlsx');
 }


/*
 * View
 */

$formfile = new Formfile($db);

$aArrcss= array('assets/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';
llxHeader("",$langs->trans("Assets"),$help_url,'','','','',$aArrcss);


$aDepre = array(0=>$langs->trans('Assetstowritteoff'),30=>$langs->trans('Assetstowritteoff').' '.$langs->trans('in').' 30 '.$langs->trans('Days'),60=>$langs->trans('Assetstowritteoff').' '.$langs->trans('in').' 60 '.$langs->trans('Days'));
$opcDepre .= "<option value='-1'></option>";

foreach ($aDepre as $kiy => $valui) {
		if($kiy== $depreciacion){
			$opcDepre .= "<option value=".$kiy." selected >".$valui."</option>";
		}else{
			$opcDepre .= "<option value=".$kiy.">".$valui."</option>";
		}
	}


print "<form action=\"assetslow.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="listAssets">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// Activos proximos a dar de baja
print '<tr><td class="fieldrequired">'.$langs->trans('Select').'</td><td colspan="2">';
print '<select name="depreciacion">'.$opcDepre.'</select>';
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

print '</form>';

if ($action == 'listAssets' && $depreciacion >= 0)
{
    include_once DOL_DOCUMENT_ROOT."/assets/report/tpl/assets_list.tpl.php";
}



llxFooter();

$db->close();
?>
