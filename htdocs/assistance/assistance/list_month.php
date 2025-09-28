<?php
/* Copyrigth PHUA. 2017
 * <examplexxx@email.com>
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/contact/class/contact.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/ctypelicenceext.class.php');

dol_include_once('/assistance/class/puser.class.php');

dol_include_once('/orgman/class/pholiday.class.php');
dol_include_once('/core/lib/datefractal.lib.php');

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
$langs->load("other");

// Get parameters
$id         = GETPOST('id','int');
$action     = GETPOST('action','alpha');
$month      = GETPOST('month');
$year       = GETPOST('year');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam    = GETPOST('myparam','alpha');
$mc         = GETPOST('mc','alpha');
if (empty($mc))$mc='m';
if (empty($month)) $month = date('m');
if (empty($year)) $year = date('Y');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
if (isset($_GET['page']) || isset($_POST['page']))
	$page = GETPOST('page','int')+0;
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="d.lastname"; // Set here default search field
if (! $sortorder) $sortorder="ASC";


$search_name=GETPOST('search_name','alpha');
$search_number=GETPOST('search_number','int');
if (isset($_POST['se_month']))
{
	$search_date = dol_mktime(0,0,0,$_POST['se_month'],$_POST['se_day'],$_POST['se_year'],'user');
	$_SESSION['ass_search_date'] = $search_date;
}
$search_date = $_SESSION['ass_search_date'];

if (isset($_POST['search_name']))
	$_SESSION['month_search_name'] = GETPOST('search_name');
if (isset($_POST['search_number']))
	$_SESSION['month_search_number'] = GETPOST('search_number');
if ($search_date)
	$_SESSION['month_search_date'] = $search_date;
if (empty($_SESSION['search_date']))
	$_SESSION['search_date'] = dol_now();

$aMarking= array(1=>$langs->trans('Primaryentry'),
	2=>$langs->trans('Primaryexit'),
	3=>$langs->trans('Secundaryentry'),
	4=>$langs->trans('Secundaryexit'),
	5=>$langs->trans('Thirdentry'),
	6=>$langs->trans('Thirdexit'),
	7=>$langs->trans('Fourthentry'),
	8=>$langs->trans('Fourthexit'),
	9=>$langs->trans('Fifthentry'),
	10=>$langs->trans('Fifthexit'),
	11=>$langs->trans('Sixthentry'),
	12=>$langs->trans('Sixthexit'),
);

// Purge criteria
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter") || GETPOST("nosearch")) // Both test are required to be compatible with all browsers
{
	$search_all='';
	$_SESSION['month_search_name']="";
	$_SESSION['month_search_number']="";
	$_SESSION['month_search_date']="";
}

$search_name   = $_SESSION['month_search_name'];
$search_number = $_SESSION['month_search_number'];
$search_date   = $_SESSION['month_search_date'];

$aDate = dol_getdate($search_date);
$aDateAnt = dol_get_prev_day($aDate['mday'],$aDate['mon'],$aDate['year']);
//creamos los rangos de fecha

$search_dateini = dol_mktime(23,59,59,$aDateAnt['month'],$aDateAnt['day'],$aDateAnt['year'],'user');
$search_datefin = dol_mktime(23,59,59,$aDate['mon'],$aDate['mday'],$aDate['year'],'user');

//echo '<hr>'.dol_print_date($search_dateini,'dayhour').' '.dol_print_date($search_datefin,'dayhour');
// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$typemar = new Typemarking($db);
$object=new Assistance($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistance'));
$extrafields = new ExtraFields($db);
$objAdherent = new Adherentext($db);
$objLicence  = new Licencesext($db);
$objCtypelicence = new Ctypelicenceext($db);
$objAssistance = new Assistance($db);
$objHoliday = new Pholiday($db);

$objPuser = new Puser($db);

//armamos la lista de dias a mostrar
$aDatefin = dol_getdate(dol_get_last_day($year,$month));
$dayfin = $aDatefin['mday'];
$aDays = array();
for ($a=1; $a <= $dayfin; $a++)
{
	$aDays[$a]= $a;
}
//recuperamos los feriados del mes
$filter = " AND t.date_month = ".$month;
$filter.= " AND t.status = 1";
$res = $objHoliday->fetchAll('','',0,0,array(1=>1),'AND',$filter);
$aFerie= array();
if ($res > 0)
{
	$lines = $objHoliday->lines;
	foreach ($lines AS $j => $line)
	{
		if($line->type == 0)
		{
			$aFerie[$line->date_day] = $line->date_day;
		}
		else
		{
			if ($line->date_year == $year)
				$aFerie[$line->date_day] = $line->date_day;
		}
	}
}

if ($action == 'excel')
{
	$aExcel = unserialize($_SESSION['aExcel']);
	$aDatosExtras = unserialize($_SESSION['aDatosExtras']);


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


	$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Month'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B2',$aDatosExtras[1]);

	$objPHPExcel->getActiveSheet()->SetCellValue('A3',html_entity_decode($langs->trans('Year')));
	$objPHPExcel->getActiveSheet()->SetCellValue('B3',$aDatosExtras[2]);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3'.$line)->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);

	$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans("CI"));
	$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans("Surnames"));
	$objPHPExcel->getActiveSheet()->SetCellValue('C7',$langs->trans("Firstname"));
		//$objPHPExcel->getActiveSheet()->SetCellValue('D7',$langs->trans(""));
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('C7')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);


		//FORMATO
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

		//cambiamos de fila
	$line = 8;
	$lineHead = 7;

	$aAbecedario = array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"H",9=>"I",10=>"J",11=>"K",12=>"L",13=>"M",14=>"N",15=>"O",16=>"P",17=>"Q",18=>"R",19=>"S",20=>"T",21=>"U",22=>"V",23=>"W",24=>"X",25=>"Y",26=>"Z",27=>"AA",28=>"AB",29=>"AC",30=>"AD",31=>"AE",32=>"AF",33=>"AG",34=>"AH",35=>"AI",36=>"AJ",37=>"AK",38=>"AL",39=>"AM",40=>"AN");

	foreach ((array)$aExcel AS $j => $row)
	{
		$nAbc = 1;
		$nN = 1;
		$nSw = 0;

		foreach ($row as $key => $value) {
				//$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$value);
			$objPHPExcel->getActiveSheet()->SetCellValue($aAbecedario[$nAbc].$line,$value);
			if($nAbc >= 4){
				$objPHPExcel->getActiveSheet()->SetCellValue($aAbecedario[$nAbc].$lineHead,$nN);
				$objPHPExcel->getActiveSheet()->getColumnDimension($aAbecedario[$nAbc])->setAutoSize(true);
				$nN++;
			}
			if($key === "faltas"){
				$objPHPExcel->getActiveSheet()->SetCellValue($aAbecedario[$nAbc].$lineHead,$langs->trans("Fouls"));
				$objPHPExcel->getActiveSheet()->getColumnDimension($aAbecedario[$nAbc])->setAutoSize(true);
					//	$nN++;
			}
			if($key === "atraso"){
				$objPHPExcel->getActiveSheet()->SetCellValue($aAbecedario[$nAbc].$lineHead,$langs->trans("Arrears"));
				$objPHPExcel->getActiveSheet()->getColumnDimension($aAbecedario[$nAbc])->setAutoSize(true);
				//	$nN++;
			}
			if($key === "abandono"){
				$objPHPExcel->getActiveSheet()->SetCellValue($aAbecedario[$nAbc].$lineHead,$langs->trans("Abandonments"));
				$objPHPExcel->getActiveSheet()->getColumnDimension($aAbecedario[$nAbc])->setAutoSize(true);
				//	$nN++;
			}
			$nAbc++;
		}

			/*$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['ci']);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$line,$row['estado']);*/
			$line++;
		}
		$nAbc--;
		$objPHPExcel->getActiveSheet()->SetCellValue('A1',html_entity_decode($langs->trans("CheckMarkings")));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$aAbecedario[$nAbc].'1');
		//$objPHPExcel->getStyle('A1')->getFont()->setSize(13);
		//$objPHPExcel->getActiveSheet()->getStyle('A1:I3')->applyFromArray($styleThickBrownBorderOutline);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(
			array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);


		$objPHPExcel->getActiveSheet()->getStyle('A7:'.$aAbecedario[$nAbc].'7')->applyFromArray(
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
	//echo 'Llega hasta aqui';
		$objWriter->save("excel/reporteAsistencia.xlsx");
		header('Location: '.DOL_URL_ROOT.'/assistance/assistance/fiche_export.php?archive=reporteAsistencia.xlsx');

	}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

// Part to create
if ($action == 'register')
{
	if ($user->fk_contact >0)
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php',1);
		header("Location: ".$urltogo);
		exit;
	}
}
if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php?mc='.$mc,1);
			header("Location: ".$urltogo);
			exit;
		}
		$timemax = $conf->global->ASSISTANCE_TIMEMAX_REGISTER_NEXT;
		$aDatehoy = dol_getdate(dol_now());
		$dHoy = $aDatehoy['mday'];
		$mHoy = $aDatehoy['mon'];
		$yHoy = $aDatehoy['year'];
		$hhHoy = $aDatehoy['hours'];
		$mmHoy = $aDatehoy['minutes'];
		$timetot = $hhHoy * 60 + $mmHoy;
		$error=0;
	//buscamos si existe registro con tiempo -5min
		if (empty($timemax)) $timemax = 5;
	//buscamos el ultimo registro de la persona
		$objectn = new Assistance($db);
		$idcontact = GETPOST('fk_contact','int')+0;
		$idmember  = GETPOST('fk_member','int')+0;
		$cm = $idcontact>0?'c':'m';
		if ($idmember>0)
			$objectn->fetchAll($sortorder='DESC', $sortfield='date_ass', $limit=20, $offset=0, array('fk_member'=>$idmember), '', $filtermode='AND');
		if ($idcontact>0)
			$objectn->fetchAll($sortorder='DESC', $sortfield='date_ass', $limit=20, $offset=0, array('fk_contact'=>$idcontact), $filtermode='AND');
		$lRegister = false;
		if (count($objectn->lines) >0)
		{
			$nRegister = 0;
		//verificamos cuantos registros del dia existen
			foreach ($objectn->lines AS $m => $objr)
			{
				$aDate = dol_getdate($objr->date_ass);
				if ($aDate['mday'] == $dHoy &&
					$aDate['mon'] == $mHoy &&
					$aDate['year'] == $yHoy)
					$nRegister++;
			}
			$nRegister++;
			$obj = $objectn->lines[0];
		//verificamos cuando se registro por ultima vez por member
			if ($cm == 'm')
			{
				if ($idmember && $obj->fk_member == $idmember)
				{
					$aDate = dol_getdate($obj->date_ass);
					if ($aDate['mday'] == $dHoy &&
						$aDate['mon'] == $mHoy &&
						$aDate['year'] == $yHoy)
					{
			//tiene marcado de hoy
			//verificamos la hora y min
						$timetotreg = $aDate['hours'] * 60 + $aDate['minutes'] + $timemax+1;
						if ($timetotreg >= $timetot)
						{
							$lRegister = false;
							$error++;
							setEventMessage($langs->trans("Thereisarecord",$langs->transnoentitiesnoconv("Members")),'errors');
						}
						else
							$lRegister = true;
					}
					else
					{
						$nRegister = 1;
						$lRegister = true;
					}
				}
				else
				{
					$nRegister = 1;
					$lRegister = true;
				}
			}

		}
		else
		{
			$nRegister = 1;
			$lRegister = true;
		}
		/* object_prop_getpost_prop */
		if ($lRegister)
		{

			$object->entity=$conf->entity;
			$object->fk_soc=GETPOST('fk_soc','int')+0;
			$object->fk_contact=GETPOST('fk_contact','int')+0;
			$object->fk_member=GETPOST('fk_member','int')+0;
			$object->date_ass=dol_now();
			$object->marking_number=$nRegister;
			$object->fk_user_create=$user->id;
			$object->fk_user_mod=$user->id;
			$object->datec = dol_now();
			$object->datem = dol_now();
			$object->tms = dol_now();
			$object->active = 1;
			$object->statut=1;
			if ($object->fk_member <=0 && $mc=='m')
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")),'errors');
			}
			if ($object->fk_member <= 0 && $object->fk_contact <=0 &&  $mc=='c')
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Contact")),'errors');
			}

			if (! $error)
			{
				$result=$object->create($user);
				if ($result > 0)
				{
			// Creation OK
					$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php?mc='.$mc,1);
					header("Location: ".$urltogo);
					exit;
				}
				{
		  // Creation KO
					if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
					else  setEventMessages($object->error, null, 'errors');
					$action='create';
				}
			}
			else
			{
				$action='create';
			}
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel'))
	{
		$action='create';
	}

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;
		$date_ass = dol_mktime($_POST['date_hour'],$_POST['date_min'],0,$_POST['date_month'],$_POST['date_day'],$_POST['date_year'],'user');

		$object->fk_soc=GETPOST('fk_soc','int')+0;
		$object->fk_member=GETPOST('fk_member','int')+0;
		$object->fk_contact=GETPOST('fk_contact','int')+0;
		$object->marking_number=GETPOST('marking_number','int');
		$object->date_ass=$date_ass;
		$object->fk_user_mod=$user->id;
		$object->tms = dol_now();
		$object->statut=1;

		if (empty($object->fk_member) && empty($object->fk_contact))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")),null,'errors');
		}

		if (! $error)
		{
			if ($object->fk_member>0) $mc= 'm';
			if ($object->fk_contact>0) $mc= 'c';
			$result=$object->update($user);
			if ($result > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php?mc='.$mc,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
		// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $object->statut == 1 )
	{
		if ($object->fk_member>0) $mc = 'm';
		if ($object->fk_contact>0) $mc = 'c';

		$result=$object->delete($user);
		if ($result > 0)
		{
		// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistance/assistance.php?mc='.$mc,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
	// Cancel
	if ($_REQUEST["confirm"] == 'no')
	{
		if ($object->fk_member>0) $mc = 'm';
		if ($object->fk_contact>0) $mc = 'c';
		$action='list';
		$id=0;
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$aArrcss= array('assistance/css/style.css');
//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js');
llxHeader("",$langs->trans("Assistance"),$help_url,'','','',$aArrjs,$aArrcss);

$form=new Form($db);
$formadd=new Formadd($db);


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

// Part to create
if ($action == 'register')
{
	if (!$user->admin && $user->fk_member >0)
	{
		print_fiche_titre($langs->trans("Registro de asistencia"));

	// Confirm validate request
		if ($action == 'register')
		{
			if (empty($_SESSION['urlant']))
				$_SESSION['urlant'] = $_SERVER['PHP_SELF'];;
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?fk_member=".$user->fk_member.'&backtopage='.$_SESSION['urlant'],$langs->trans("Assistance"),$langs->trans("Desea registrar su asistencia",$object->ref),"add",'',0,2);
			if ($ret == 'html') print '<br>';
		}
	}
}


$listhalfday=array('morning'=>$langs->trans("Morning"),"afternoon"=>$langs->trans("Afternoon"));

// Part to show a list
if ($action == 'list' || $action == 'delete' || (empty($id) && $action != 'register') )
{
	// Put here content of your page
	print load_fiche_titre($langs->trans('Dials'));
	// Confirm delete request
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&mc=m',$langs->trans("Delete"),$langs->trans("Confirmdeleteassistance",$object->ref),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	//recuperamos la lista de miembros

	$filter = " AND d.statut = 1";
	if ($search_name) $filter.= natural_search(array('d.lastname','d.firstname'),$search_name);

	$res = $objAdherent->fetchAll($sortorder,$sortfield,0,0,array(1=>1),'AND',$filter);
	$linesAdh = array();
	if ($res > 0)
	{
		$linesAdh = $objAdherent->lines;
	}



	//$sql.= $db->plimit($limit+1, $offset);

	$param='&month='.$month.'&year='.$year;
	if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
	//if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;





	$params='';
	$params.= '&amp;search_field1='.urlencode($search_field1);
	$params.= '&amp;search_field2='.urlencode($search_field2);

		//print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {';
		print '$("#month").change(function() {
			document.formsoc.action.value="list";
			document.formsoc.submit();
		});';
		print '$("#year").change(function() {
			document.formsoc.action.value="list";
			document.formsoc.submit();
		});';
		print '});';
		print '</script>'."\n";
	}

	print '<form name="formsoc" method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
	if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

	print '<div>';
	print '<input type="number" id="month" min="1" max="12" name="month" value="'.$month.'">';
	print '<input type="number" id="year" name="year" value="'.$year.'">';
	print '</div>';



	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre">';
		print $moreforfilter;
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);
		 // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</div>';
	}

	print '<table class="border centpercent">'."\n";

		// Fields title
	print '<tr class="liste_titre">';

	print_liste_field_titre($langs->trans('Name'),$_SERVER['PHP_SELF'],'d.lastname','',$param,'',$sortfield,$sortorder);

	foreach ($aDays AS $day)
	{
		print_liste_field_titre($day,$_SERVER['PHP_SELF'],'','',$param,'align="center"',$sortfield,$sortorder);
	}
	print_liste_field_titre($langs->trans('Faltas'),$_SERVER['PHP_SELF'],'','',$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Atrasos'),$_SERVER['PHP_SELF'],'','',$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Abandonos'),$_SERVER['PHP_SELF'],'','',$param,'align="center"',$sortfield,$sortorder);
	print '</tr>';

		// Fields title search
	print '<tr class="liste_titre">';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_name" value="'.$search_name.'" size="10"></td>';
	$colspan = count($aDays)+2;
	print '<td colspan="'.$colspan.'" class="liste_titre">';
	print '</td>';
	print '<td align="right" class="liste_titre">';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';

	print '</td>';

	print '</tr>'."\n";
		//if (empty($search_date)) $search_date = dol_now();

	$i = 0;
	$aExcel = array();
	$ni = 1;
	foreach ($linesAdh AS $j => $obj)
	{
		$aFalta = array();
		$aAssistance = array();
		$aVacation = array();
		$aVacationtype = array();
		$aBackwardness = array();
		$aAbandonment = array();
		$lView = true;
		$filter = " AND t.fk_member = ".$obj->id;
		$filter.= " AND MONTH(t.date_ass) = ".$month;
		$filter.= " AND YEAR(t.date_ass) = ".$year;
		$res = $objAssistance->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		if ($res >0)
		{
			$lines = $objAssistance->lines;
			foreach ($lines AS $k => $line)
			{
				$aDate = dol_getdate($line->date_ass);
				$aAssistance[$aDate['mday']] = $aDate['mday'];
				$aBackwardness[$aDate['mday']]+=$line->backwardness;
				$aAbandonment[$aDate['mday']]+=$line->abandonment;
			}
		}
				//verificamos las vacaciones
		$filter = " AND t.fk_member = ".$obj->id;
		$filter.= " AND MONTH(t.date_ini) = ".$month;
		$filter.= " AND YEAR(t.date_ini) = ".$year;
		$filter.= " AND t.statut >=3";
		$res = $objLicence->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		if ($res >0)
		{
			$lines = $objLicence->lines;
			foreach ($lines AS $k => $line)
			{
				$rest = $objCtypelicence->fetch(0,$line->type_licence);
				if ($objCtypelicence->type == 'V')
				{
							//armamos las fechas de licencia que tiene aprobado
							//recorremos dia a dia
					$dateini = $line->date_ini_ejec;
					$datefin = $line->date_fin_ejec;
					$lLoop = true;

					$starthalfday=GETPOST('starthalfday');
					$endhalfday=GETPOST('endhalfday');

					$halfday=0;
					if ($starthalfday == 'afternoon' && $endhalfday == 'morning') $halfday=2;
					else if ($starthalfday == 'afternoon') $halfday=-1;
					else if ($endhalfday == 'morning') $halfday=1;
						//si halfday == 0   es mañana tarde
						//si halfday == -1  es tarde tarde
						//si halfday == 1 es mañana mañana
						//si halfday == 2 es tarde mañana

					while ($lLoop == true)
					{
						if ($dateini <= $datefin)
						{
							$aDate = dol_getdate($dateini);
							$aAssistance[$aDate['mday']]= $aDate['mday'];
							if ($dateini == $line->date_ini || $dateini == $date_fin)
							{
								$aVacation[$aDate['mday']]= $aDate['mday'];
								$aVacationtype[$aDate['mday']]= $line->halfday;
							}
							else
							{
								$aVacation[$aDate['mday']]= $aDate['mday'];
								$aVacationtype[$aDate['mday']]= 'T';
							}
								//sumamos un dia
							$dateini = dol_time_plus_duree($dateini, 1, 'd');
						}
						else
							$lLoop = false;
					}
				}
			}
		}
			//aqui o arranjo

		if ($lView)
		{
				//imprimimos todos los dias
			$sumAtraso = 0;
			$sumAbandono = 0;
				// You can use here results
			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$obj->lastname.' '.$obj->firstname.'</td>';
			$rPu = $objPuser->fetchAll('','',0,0,array(1=>1),"AND","AND t.fk_user = '".$obj->id."'",true);
			if($rPu > 0){
				$aExcel[$i][] = $objPuser->docum;
			}else{
				$aExcel[$i][] = "";
			}
			$aExcel[$i][] = $obj->lastname;
			$aExcel[$i][] = $obj->firstname;

			foreach ($aDays AS $day)
			{
				$ferie = false;
					//verificamos si es sabado o domingo
				$datetmp = dol_mktime(0,0,0,$month,$day,$year);
				$jour_julien = unixtojd($datetmp);
				$jour_semaine = jddayofweek($jour_julien, 0);
				$aWorking = explode('-',$conf->global->MAIN_DEFAULT_WORKING_DAYS);

				if ($aWorking[1]==5)
					if($jour_semaine == 0 || $jour_semaine == 6) $ferie=true;
				if ($aWorking[1]==6)
					if($jour_semaine == 0) $ferie=true;
					//verificamos si el dia es feriado local
				$timestampStart = dol_mktime(0,0,0,$month,$day,$year);

				if (!$ferie)
				{
					$nbFerie = num_public_holiday_fractal($timestampStart, $timestampStart, 'BO', 1);
					if ($nbFerie) $ferie=true;
				}
				if (!$ferie)
				{
					$text = '';
					$aAuxMin;
					if ($aBackwardness[$day])
					{
						$min = $aBackwardness[$day]/60;
						$min = convertSecondToTime(convertTime2Seconds(0,$min,0));
						$aAuxMin = $min;

						$text.= '<span class="badge">'.$min.'</span>';
						$sumAtraso+=$aBackwardness[$day]/60;
					}
					if ($aAbandonment[$day])
					{
						$min = $aAbandonment[$day]/60;
						$min = convertSecondToTime(convertTime2Seconds(0,$min,0));
						$aAuxMin = $min;
						$text.= '<span id="badgeab">'.$min.'</span>';
						$sumAbandono+=$aAbandonment[$day]/60;
					}

						//$aExcel[$i][] = $min;

					$class = 'assmark';
					if ($aAssistance[$day])
					{
						if ($aVacation[$day])
						{
								//$class = 'assvac';
							$text = 'V';

							if ($aVacationtype[$day]=='T') $text='V';
							if ($aVacationtype[$day]==2) $text='v';
							if ($aVacationtype[$day]==-1) $text='v';
							if ($aVacationtype[$day]==1) $text='v';
							$aAuxMin = $text;
								//$aExcel[$i][] = "V";
						}
							//echo $text;

						print '<td id="'.$class.'">'.$text.'</td>';
						$aExcel[$i][] = $aAuxMin;
						$aAuxMin = null;

					}
					else
					{
						if ($month == date('m') && $year == date('Y'))
						{
							if ($day > date('d')){
								$aExcel[$i][] = "";
								print '<td></td>';
							}else
							{
								print '<td id="assfalta">F</td>';
								$aExcel[$i][] = "F";
								$aFalta[$day]=1;
							}
						}
						elseif($month < date('m') && $year == date('Y'))
						{
							print '<td id="assfalta">F</td>';
							$aExcel[$i][] = "F";
							$aFalta[$day]=1;
						}
						else{
							print '<td id="assfalta"></td>';
						}
					}
				}
				else
				{
					if ($aFerie[$day]){
						print '<td id="ferie">'.($aAssistance[$day]?'a':'').'</td>';
						$aExcel[$i][] = ($aAssistance[$day]?"a":"");
					}else{
						print '<td id="ferie60"></td>';
						$aExcel[$i][] = "";
					}

				}
			}
			print '<td align="center">'.count($aFalta).'</td>';
			$aExcel[$i]["faltas"] = count($aFalta);

			if ($sumAtraso>0)
			{
				$sumAtraso = convertTime2Seconds(0,$sumAtraso,0);
				$aExcel[$i]["atraso"] = ($sumAtraso/60);
				$hora = convertSecondToTime($sumAtraso);
				print '<td align="center">'.$hora.'</td>';
			}
			else{
				print '<td></td>';
				$aExcel[$i]["atraso"] = 0;
			}

			if ($sumAbandono>0)
			{
				$sumAbandono = convertTime2Seconds(0,$sumAbandono,0);
				$aExcel[$i]["abandono"] = ($sumAbandono/60);
				$hora = convertSecondToTime($sumAbandono);
				print '<td align="center">'.$hora.'</td>';
			}
			else{
				$aExcel[$i]["abandono"] = 0;
				print '<td></td>';
			}

			print '</tr>';
		}
		$i++;
	}


	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
			// Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";

		//echo "Mes : ".$month." Anio : ".$year;
	$aMeses = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
	$aDatosExtras = array(1=>$aMeses[$month*1],2=>$year);
		// Buttons
	print '<div class="tabsAction">'."\n";

	$_SESSION['aDatosExtras'] = serialize($aDatosExtras);
	$_SESSION['aExcel'] = serialize($aExcel);
	if (count($aExcel)>0)
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';

	if ($user->fk_member>0)
	{
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="register">';
		print '<input type="hidden" name="mc" value="'.$mc.'">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="fk_member" value="'.$user->fk_member.'">';
		print '<div class="inline-block divButAction"><input type="submit" class="butAction" name="add" value="'.$langs->trans("Registerassistance").'"> </div>';
		print '</form>';
	}
	print '</div>'."\n";


}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

	dol_fiche_head();

	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Name").'</td><td>';
	if ($object->fk_member>0)
	{
		print $formadd->select_member($object->fk_member,'fk_member','',1,'','','','','autofocus');
	}
	elseif ($object->fk_contact>0)
	{
		print $form->select_contacts(0,$object->fk_contact,'fk_contact',1);
	}

	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Date").'</td><td>';
	print $form->select_date($object->date_ass,'date_',1,1,0);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Markingnumber").'</td><td>';
	print $form->selectarray('marking_number',$aMarking,$object->marking_number,1);
	//print '<input type="number" min="0" max="10" name="marking_number" value="'.$object->marking_number.'">';
	print '</td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
	dol_fiche_head();



	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
		// Note that $action and $object may have been modified by hook

	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assistance->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->assistance->delete)
		{
			if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))
			// We can't use preloaded confirm form with jmobile
			{
				print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
			}
			else
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
		}
	}
	print '</div>'."\n";
	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;
}
// End of page
llxFooter();
$db->close();
