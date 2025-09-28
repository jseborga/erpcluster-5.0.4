<?php
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
//dol_include_once('/assistance/class/adherent.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/puser.class.php');
dol_include_once('/orgman/class/pdepartamentuserext.class.php');
dol_include_once('/orgman/class/pdepartamentext.class.php');
dol_include_once('/assistance/class/membervacation.class.php');
dol_include_once('/assistance/class/membervacationdet.class.php');
require_once DOL_DOCUMENT_ROOT.'/assistance/lib/utils.lib.php';

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
$langs->load("other");

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$search_entity=GETPOST('search_entity','int');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_civility=GETPOST('search_civility','alpha');
$search_lastname=GETPOST('search_lastname','alpha');
$search_firstname=GETPOST('search_firstname','alpha');
$search_login=GETPOST('search_login','alpha');
$search_pass=GETPOST('search_pass','alpha');
$search_pass_crypted=GETPOST('search_pass_crypted','alpha');
$search_fk_adherent_type=GETPOST('search_fk_adherent_type','int');
$search_morphy=GETPOST('search_morphy','alpha');
$search_societe=GETPOST('search_societe','alpha');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_address=GETPOST('search_address','alpha');
$search_zip=GETPOST('search_zip','alpha');
$search_town=GETPOST('search_town','alpha');
$search_state_id=GETPOST('search_state_id','int');
$search_country=GETPOST('search_country','int');
$search_email=GETPOST('search_email','alpha');
$search_skype=GETPOST('search_skype','alpha');
$search_phone=GETPOST('search_phone','alpha');
$search_phone_perso=GETPOST('search_phone_perso','alpha');
$search_phone_mobile=GETPOST('search_phone_mobile','alpha');
$search_photo=GETPOST('search_photo','alpha');
$search_statut=GETPOST('search_statut','int');
$search_public=GETPOST('search_public','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_canvas=GETPOST('search_canvas','alpha');
$search_import_key=GETPOST('search_import_key','alpha');
$search_docum=GETPOST('search_docum','alpha');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');


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
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assistancelist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('assistancelist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('assistance');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>0),
	't.civility'=>array('label'=>$langs->trans("CI"), 'checked'=>1),
	't.lastname'=>array('label'=>$langs->trans("Fieldlastname"), 'checked'=>1),
	't.firstname'=>array('label'=>$langs->trans("Fieldfirstname"), 'checked'=>1),
	't.login'=>array('label'=>$langs->trans("Area"), 'checked'=>1),
	't.pass'=>array('label'=>$langs->trans("Fieldpass"), 'checked'=>0),
	't.pass_crypted'=>array('label'=>$langs->trans("Fieldpass_crypted"), 'checked'=>0),
	't.fk_adherent_type'=>array('label'=>$langs->trans("Holiday"), 'checked'=>1),
	't.morphy'=>array('label'=>$langs->trans("Fieldmorphy"), 'checked'=>0),
	't.societe'=>array('label'=>$langs->trans("Fieldsociete"), 'checked'=>0),
	't.fk_soc'=>array('label'=>$langs->trans("Fieldfk_soc"), 'checked'=>0),
	't.address'=>array('label'=>$langs->trans("Fieldaddress"), 'checked'=>0),
	't.zip'=>array('label'=>$langs->trans("Fieldzip"), 'checked'=>0),
	't.town'=>array('label'=>$langs->trans("Fieldtown"), 'checked'=>0),
	't.state_id'=>array('label'=>$langs->trans("Fieldstate_id"), 'checked'=>0),
	't.country'=>array('label'=>$langs->trans("Fieldcountry"), 'checked'=>0),
	't.email'=>array('label'=>$langs->trans("Fieldemail"), 'checked'=>0),
	't.skype'=>array('label'=>$langs->trans("Fieldskype"), 'checked'=>0),
	't.phone'=>array('label'=>$langs->trans("Fieldphone"), 'checked'=>0),
	't.phone_perso'=>array('label'=>$langs->trans("Fieldphone_perso"), 'checked'=>0),
	't.phone_mobile'=>array('label'=>$langs->trans("Fieldphone_mobile"), 'checked'=>0),
	't.photo'=>array('label'=>$langs->trans("Fieldphoto"), 'checked'=>0),
	't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>0),
	't.public'=>array('label'=>$langs->trans("Fieldpublic"), 'checked'=>0),
	't.note_private'=>array('label'=>$langs->trans("Fieldnote_private"), 'checked'=>0),
	't.note_public'=>array('label'=>$langs->trans("Fieldnote_public"), 'checked'=>0),
	't.model_pdf'=>array('label'=>$langs->trans("Fieldmodel_pdf"), 'checked'=>0),
	't.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
	't.fk_user_valid'=>array('label'=>$langs->trans("Fieldfk_user_valid"), 'checked'=>0),
	't.canvas'=>array('label'=>$langs->trans("Fieldcanvas"), 'checked'=>0),
	't.import_key'=>array('label'=>$langs->trans("Fieldimport_key"), 'checked'=>0),

	'u.docum'=>array('label'=>$langs->trans("Fieldci"), 'checked'=>1),
	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
	//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
);
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	}
}


// Load object if id or ref is provided as parameter
// Object declaration
$object = new Adherentext($db);
$objPuser = new Puser($db);
$objDepartamento = new Pdepartamentext($db);
$objDepUser = new Pdepartamentuserext($db);
$objMemberVacation = new Membervacation($db);
$objMemberVacDet = new Membervacationdet($db);

//Array declaration
$aVacation =array();

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
//verificamos que tenga asignado un valor en la variable
if (!$conf->global->SALARY_CODE_VACATION)
{
	setEventMessages($langs->trans('No esta configurado la variable').' SALARY_CODE_VACATION',null,'warnings');
}
/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//vamos a calcular las vacaciones de cada empleado

	$res = $object->fetchAll('','',0,0,array(1=>1),'AND');
	if ($res > 0)
	{
		require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictableext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfieldext.class.php';
		dol_include_once('/assistance/class/membervacationext.class.php');
		dol_include_once('/assistance/class/membervacationdet.class.php');
		$objMembervacation = new Membervacationext($db);
		$objMembervacationdet = new Membervacationdet($db);

		$now = dol_now();
		$lines = $object->lines;
		foreach ($lines AS $j => $object)
		{
			$aCalc = calc_vacation($user, $object->id);
			if ($aCalc[0])
			{
				$nYearcas = floor($aCalc[1]);

				if ($object->id == 1)
				//echo 'year '.$nYearcas.' '.dol_print_date($aCalc[3]);
				$nVacation = 0;
				$objGenerictable = new Pgenerictableext($db);
				$objGenericfield = new Pgenericfieldext($db);
				//verificamos si existe registro
				$newdate = dol_time_plus_duree($aCalc[3], 1, 'd');
				$aDate = dol_getdate(dol_now());

				$year = $aDate['year'];
				$res = $objMembervacation->fetch(0,$object->id,$year);
				//if ($object->id == 1) echo '<hr>res '.$res;
				//nueva fecha inicio
				$newdateini = dol_mktime(12,0,0,$aDate['mon'],$aDate['mday'],$year);
				//fecha de inicio si le corresponde vacación es de su fecha de contrato mas un dia
				$aDatecontract = dol_getdate($aCalc[3]);
				$aDate = dol_get_next_day($aDatecontract['mday'], $aDatecontract['mon'], $year);
				$newdateini = dol_mktime(12,0,0,$aDate['month'],$aDate['day'],$aDate['year']);

				//fecha fin
				$newYear = $aDate['year'] +2;
				$newdatefin = dol_mktime(12,0,0,$aDatecontract['mon'],$aDatecontract['mday'],$newYear);
				if ($res >= 0)
				{
					$filter = " AND t.table_cod = '".$conf->global->SALARY_CODE_VACATION."'";
					$resg = $objGenerictable->getTable($filter);
					//if ($object->id == 1) echo '<hr>gty '.$resg;
					if (count($objGenerictable->aTable)>0)
					{
						$aTable = $objGenerictable->aTable;
						$newData = array();
						foreach ($aTable AS $seq => $data)
						{
							if ($nYearcas >= $data[1] && $nYearcas <= $data[2])
							{
								$nVacation = $data[3];
							}
						}
					}
					//if ($object->id == 1) echo $nVacation;

					if ($nVacation>0)
					{
						//si existe mandamos mensaje

						//agregamos a la tabla
						if ($res==0)
						{
							$objMembervacation->fk_member = $object->id;
							$objMembervacation->date_ini = $newdateini;
							$objMembervacation->date_fin = $newdatefin;
							$objMembervacation->period_year = $year;
							$objMembervacation->days_assigned = $nVacation;
							$objMembervacation->days_used = 0;
							$objMembervacation->fk_user_create = $user->id;
							$objMembervacation->fk_user_mod = $user->id;
							$objMembervacation->datec = $now;
							$objMembervacation->datem = $now;
							$objMembervacation->tms = $now;
							$objMembervacation->status = 0;
							$resadd = $objMembervacation->create($user);
							if ($resadd <=0)
							{
								$error++;
								setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
							}
						}
						elseif($res == 1 && $nVacation != $objMembervacation->days_assigned)
						{
							$objMembervacation->date_ini = $newdateini;
							$objMembervacation->date_fin = $newdatefin;
							$objMembervacation->period_year = $year;
							$objMembervacation->days_assigned = $nVacation;
							//$objMembervacation->days_used = 0;
							$objMembervacation->fk_user_mod = $user->id;
							$objMembervacation->datem = $now;
							$objMembervacation->tms = $now;
							$objMembervacation->status = 0;
							$resmod = $objMembervacation->update($user);
							if ($resmod <=0)
							{
								$error++;
								setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
							}
						}
					}
				}
				else
				{
					$error++;
					setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
				}

			}
		}

	}
//fin calculo vacaciones



	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{

		$search_entity='';
		$search_ref_ext='';
		$search_civility='';
		$search_lastname='';
		$search_firstname='';
		$search_login='';
		$search_pass='';
		$search_pass_crypted='';
		$search_fk_adherent_type='';
		$search_morphy='';
		$search_societe='';
		$search_fk_soc='';
		$search_address='';
		$search_zip='';
		$search_town='';
		$search_state_id='';
		$search_country='';
		$search_email='';
		$search_skype='';
		$search_phone='';
		$search_phone_perso='';
		$search_phone_mobile='';
		$search_photo='';
		$search_statut='';
		$search_public='';
		$search_note_private='';
		$search_note_public='';
		$search_model_pdf='';
		$search_fk_user_author='';
		$search_fk_user_mod='';
		$search_fk_user_valid='';
		$search_canvas='';
		$search_import_key='';
		$search_docum = '';

		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

}

if ($action == 'excel')
{
	$aVacation = unserialize($_SESSION['aVacation']);

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

	$objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Reportvacationbalance'));


		//$objPHPExcel->getStyle('A1')->getFont()->setSize(14);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:D2');


	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);


	$objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Tothe'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B4',dol_print_date(dol_now(),'daytext'));

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3'.$line)->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,)
	);


		//titulos
	$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('CI'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B7',$langs->trans('Fullname'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C7',$langs->trans('Area'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D7',$langs->trans('Vacationbalance'));
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('D7')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);
		//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7');

	$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->applyFromArray(
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


		//cambiamos de fila
	$line = 8;

	foreach ((array)$aVacation AS $j => $row)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$row['dni']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$row['allname']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$row['area']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$line,$row['saldo']);
		$line++;
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

	//$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
	//
	//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/repVacationBalance.xlsx");
	//echo 'Llega hasta aqui';
	header('Location: '.DOL_URL_ROOT.'/assistance/report/fiche_export.php?archive=repVacationBalance.xlsx');
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('VacationBalance');

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


$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.entity,";
$sql .= " t.ref_ext,";
$sql .= " t.civility,";
$sql .= " t.lastname,";
$sql .= " t.firstname,";
$sql .= " t.login,";
$sql .= " t.pass,";
$sql .= " t.pass_crypted,";
$sql .= " t.fk_adherent_type,";
$sql .= " t.morphy,";
$sql .= " t.societe,";
$sql .= " t.fk_soc,";
$sql .= " t.address,";
$sql .= " t.zip,";
$sql .= " t.town,";
$sql .= " t.state_id,";
$sql .= " t.country,";
$sql .= " t.email,";
$sql .= " t.skype,";
$sql .= " t.phone,";
$sql .= " t.phone_perso,";
$sql .= " t.phone_mobile,";
$sql .= " t.birth,";
$sql .= " t.photo,";
$sql .= " t.statut,";
$sql .= " t.public,";
$sql .= " t.datefin,";
$sql .= " t.note_private,";
$sql .= " t.note_public,";
$sql .= " t.model_pdf,";
$sql .= " t.datevalid,";
$sql .= " t.datec,";
$sql .= " t.tms,";
$sql .= " t.fk_user_author,";
$sql .= " t.fk_user_mod,";
$sql .= " t.fk_user_valid,";
$sql .= " t.canvas,";
$sql .= " t.import_key,";
$sql .= " u.docum,";
$sql .= " du.label";



// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."adherent as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."adherent_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user as u ON t.rowid = u.fk_user";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_departament_user as pd ON t.rowid = pd.fk_user";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_departament as du ON pd.fk_departament = du.rowid";
$sql.= " WHERE 1 = 1";

//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_civility) $sql.= natural_search("docum",$search_civility);
if ($search_lastname) $sql.= natural_search(array("u.lastname","t.lastname"),$search_lastname);
if ($search_firstname) $sql.= natural_search("firstname",$search_firstname);
if ($search_login) $sql.= natural_search("label",$search_login);
if ($search_pass) $sql.= natural_search("pass",$search_pass);
if ($search_pass_crypted) $sql.= natural_search("pass_crypted",$search_pass_crypted);
if ($search_fk_adherent_type) $sql.= natural_search("fk_adherent_type",$search_fk_adherent_type);
if ($search_morphy) $sql.= natural_search("morphy",$search_morphy);
if ($search_societe) $sql.= natural_search("societe",$search_societe);
if ($search_fk_soc) $sql.= natural_search("fk_soc",$search_fk_soc);
if ($search_address) $sql.= natural_search("address",$search_address);
if ($search_zip) $sql.= natural_search("zip",$search_zip);
if ($search_town) $sql.= natural_search("town",$search_town);
if ($search_state_id) $sql.= natural_search("state_id",$search_state_id);
if ($search_country) $sql.= natural_search("country",$search_country);
if ($search_email) $sql.= natural_search("email",$search_email);
if ($search_skype) $sql.= natural_search("skype",$search_skype);
if ($search_phone) $sql.= natural_search("phone",$search_phone);
if ($search_phone_perso) $sql.= natural_search("phone_perso",$search_phone_perso);
if ($search_phone_mobile) $sql.= natural_search("phone_mobile",$search_phone_mobile);
if ($search_photo) $sql.= natural_search("photo",$search_photo);
if ($search_statut) $sql.= natural_search("statut",$search_statut);
if ($search_public) $sql.= natural_search("public",$search_public);
if ($search_note_private) $sql.= natural_search("note_private",$search_note_private);
if ($search_note_public) $sql.= natural_search("note_public",$search_note_public);
if ($search_model_pdf) $sql.= natural_search("model_pdf",$search_model_pdf);
if ($search_fk_user_author) $sql.= natural_search("fk_user_author",$search_fk_user_author);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_fk_user_valid) $sql.= natural_search("fk_user_valid",$search_fk_user_valid);
if ($search_canvas) $sql.= natural_search("canvas",$search_canvas);
if ($search_import_key) $sql.= natural_search("import_key",$search_import_key);
if ($search_docum) $sql.= natural_search("u.docum",$search_docum);

//echo $sql;
if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);
// Add where from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	$typ=$extrafields->attribute_type[$tmpkey];
	$mode=0;
	if (in_array($typ, array('int','double'))) $mode=1;    // Search on a numeric
	if ($val && ( ($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit)))
	{
		$sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
	}
}
// Add where from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.=$db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit+1, $offset);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
	$nAll = $nbtotalofrecords;
	$resqall=$db->query($sql);
	if (! $resqall)
	{
		dol_print_error($db);
		exit;
	}
}

$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all)
{
	$obj = $db->fetch_object($resql);
	$id = $obj->rowid;
	header("Location: ".DOL_URL_ROOT.'/adherent/card.php?id='.$id);
	exit;
}

llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_field1 != '') $param.= '&amp;search_field1='.urlencode($search_field1);
if ($search_field2 != '') $param.= '&amp;search_field2='.urlencode($search_field2);
if ($optioncss != '') $param.='&optioncss='.$optioncss;
// Add $param from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
}

$arrayofmassactions =  array(
	'presend'=>$langs->trans("SendByMail"),
	'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->assistance->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

/*$moreforfilter = '';
$moreforfilter.='<div class="divsearchfield">';
$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
$moreforfilter.= '</div>';*/

$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
else $moreforfilter = $hookmanager->resPrint;

if (! empty($moreforfilter))
{
	print '<div class="liste_titre liste_titre_bydiv centpercent">';
	print $moreforfilter;
	print '</div>';
}

$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
//$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.civility']['checked'])) print_liste_field_titre($arrayfields['t.civility']['label'],$_SERVER['PHP_SELF'],'t.civility','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lastname']['checked'])) print_liste_field_titre($arrayfields['t.lastname']['label'],$_SERVER['PHP_SELF'],'t.lastname','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.firstname']['checked'])) print_liste_field_titre($arrayfields['t.firstname']['label'],$_SERVER['PHP_SELF'],'t.firstname','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.login']['checked'])) print_liste_field_titre($arrayfields['t.login']['label'],$_SERVER['PHP_SELF'],'t.login','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.pass']['checked'])) print_liste_field_titre($arrayfields['t.pass']['label'],$_SERVER['PHP_SELF'],'t.pass','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.pass_crypted']['checked'])) print_liste_field_titre($arrayfields['t.pass_crypted']['label'],$_SERVER['PHP_SELF'],'t.pass_crypted','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_adherent_type']['checked'])) print_liste_field_titre($arrayfields['t.fk_adherent_type']['label'],$_SERVER['PHP_SELF'],'t.fk_adherent_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.morphy']['checked'])) print_liste_field_titre($arrayfields['t.morphy']['label'],$_SERVER['PHP_SELF'],'t.morphy','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.societe']['checked'])) print_liste_field_titre($arrayfields['t.societe']['label'],$_SERVER['PHP_SELF'],'t.societe','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_soc']['checked'])) print_liste_field_titre($arrayfields['t.fk_soc']['label'],$_SERVER['PHP_SELF'],'t.fk_soc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.address']['checked'])) print_liste_field_titre($arrayfields['t.address']['label'],$_SERVER['PHP_SELF'],'t.address','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.zip']['checked'])) print_liste_field_titre($arrayfields['t.zip']['label'],$_SERVER['PHP_SELF'],'t.zip','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.town']['checked'])) print_liste_field_titre($arrayfields['t.town']['label'],$_SERVER['PHP_SELF'],'t.town','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.state_id']['checked'])) print_liste_field_titre($arrayfields['t.state_id']['label'],$_SERVER['PHP_SELF'],'t.state_id','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.country']['checked'])) print_liste_field_titre($arrayfields['t.country']['label'],$_SERVER['PHP_SELF'],'t.country','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.email']['checked'])) print_liste_field_titre($arrayfields['t.email']['label'],$_SERVER['PHP_SELF'],'t.email','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.skype']['checked'])) print_liste_field_titre($arrayfields['t.skype']['label'],$_SERVER['PHP_SELF'],'t.skype','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.phone']['checked'])) print_liste_field_titre($arrayfields['t.phone']['label'],$_SERVER['PHP_SELF'],'t.phone','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.phone_perso']['checked'])) print_liste_field_titre($arrayfields['t.phone_perso']['label'],$_SERVER['PHP_SELF'],'t.phone_perso','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.phone_mobile']['checked'])) print_liste_field_titre($arrayfields['t.phone_mobile']['label'],$_SERVER['PHP_SELF'],'t.phone_mobile','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.photo']['checked'])) print_liste_field_titre($arrayfields['t.photo']['label'],$_SERVER['PHP_SELF'],'t.photo','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.public']['checked'])) print_liste_field_titre($arrayfields['t.public']['label'],$_SERVER['PHP_SELF'],'t.public','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_private']['checked'])) print_liste_field_titre($arrayfields['t.note_private']['label'],$_SERVER['PHP_SELF'],'t.note_private','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_public']['checked'])) print_liste_field_titre($arrayfields['t.note_public']['label'],$_SERVER['PHP_SELF'],'t.note_public','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model_pdf']['checked'])) print_liste_field_titre($arrayfields['t.model_pdf']['label'],$_SERVER['PHP_SELF'],'t.model_pdf','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_author']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'t.fk_user_author','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_valid']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_valid']['label'],$_SERVER['PHP_SELF'],'t.fk_user_valid','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.canvas']['checked'])) print_liste_field_titre($arrayfields['t.canvas']['label'],$_SERVER['PHP_SELF'],'t.canvas','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.import_key']['checked'])) print_liste_field_titre($arrayfields['t.import_key']['label'],$_SERVER['PHP_SELF'],'t.import_key','',$params,'',$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,'',$sortfield,$sortorder);
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		if (! empty($arrayfields["ef.".$key]['checked']))
		{
			$align=$extrafields->getAlignFlag($key);
			print_liste_field_titre($extralabels[$key],$_SERVER["PHP_SELF"],"ef.".$key,"",$param,($align?'align="'.$align.'"':''),$sortfield,$sortorder);
		}
	}
}
// Hook fields
$parameters=array('arrayfields'=>$arrayfields);
$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['u.docum']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_docum" value="'.$search_docum.'" size="10"></td>';
if (! empty($arrayfields['t.lastname']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lastname" value="'.$search_lastname.'" size="10"></td>';
if (! empty($arrayfields['t.firstname']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_firstname" value="'.$search_firstname.'" size="10"></td>';
if (! empty($arrayfields['t.login']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_login" value="'.$search_login.'" size="10"></td>';
if (! empty($arrayfields['t.pass']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_pass" value="'.$search_pass.'" size="10"></td>';
if (! empty($arrayfields['t.pass_crypted']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_pass_crypted" value="'.$search_pass_crypted.'" size="10"></td>';
if (! empty($arrayfields['t.fk_adherent_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_adherent_type" value="'.$search_fk_adherent_type.'" size="10"></td>';
if (! empty($arrayfields['t.morphy']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_morphy" value="'.$search_morphy.'" size="10"></td>';
if (! empty($arrayfields['t.societe']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_societe" value="'.$search_societe.'" size="10"></td>';
if (! empty($arrayfields['t.fk_soc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_soc" value="'.$search_fk_soc.'" size="10"></td>';
if (! empty($arrayfields['t.address']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_address" value="'.$search_address.'" size="10"></td>';
if (! empty($arrayfields['t.zip']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_zip" value="'.$search_zip.'" size="10"></td>';
if (! empty($arrayfields['t.town']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_town" value="'.$search_town.'" size="10"></td>';
if (! empty($arrayfields['t.state_id']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_state_id" value="'.$search_state_id.'" size="10"></td>';
if (! empty($arrayfields['t.country']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_country" value="'.$search_country.'" size="10"></td>';
if (! empty($arrayfields['t.email']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_email" value="'.$search_email.'" size="10"></td>';
if (! empty($arrayfields['t.skype']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_skype" value="'.$search_skype.'" size="10"></td>';
if (! empty($arrayfields['t.phone']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_phone" value="'.$search_phone.'" size="10"></td>';
if (! empty($arrayfields['t.phone_perso']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_phone_perso" value="'.$search_phone_perso.'" size="10"></td>';
if (! empty($arrayfields['t.phone_mobile']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_phone_mobile" value="'.$search_phone_mobile.'" size="10"></td>';
if (! empty($arrayfields['t.photo']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_photo" value="'.$search_photo.'" size="10"></td>';
if (! empty($arrayfields['t.statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut" value="'.$search_statut.'" size="10"></td>';
if (! empty($arrayfields['t.public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_public" value="'.$search_public.'" size="10"></td>';
if (! empty($arrayfields['t.note_private']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_private" value="'.$search_note_private.'" size="10"></td>';
if (! empty($arrayfields['t.note_public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_public" value="'.$search_note_public.'" size="10"></td>';
if (! empty($arrayfields['t.model_pdf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model_pdf" value="'.$search_model_pdf.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_author']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_author" value="'.$search_fk_user_author.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_valid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_valid" value="'.$search_fk_user_valid.'" size="10"></td>';
if (! empty($arrayfields['t.canvas']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_canvas" value="'.$search_canvas.'" size="10"></td>';
if (! empty($arrayfields['t.import_key']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_import_key" value="'.$search_import_key.'" size="10"></td>';

//if (! empty($arrayfields['t.field1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_field1" value="'.$search_field1.'" size="10"></td>';
//if (! empty($arrayfields['t.field2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_field2" value="'.$search_field2.'" size="10"></td>';
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		if (! empty($arrayfields["ef.".$key]['checked']))
		{
			$align=$extrafields->getAlignFlag($key);
			$typeofextrafield=$extrafields->attribute_type[$key];
			print '<td class="liste_titre'.($align?' '.$align:'').'">';
			if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')))
			{
				$crit=$val;
				$tmpkey=preg_replace('/search_options_/','',$key);
				$searchclass='';
				if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
				if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
				print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="search_options_'.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options['search_options_'.$tmpkey]).'">';
			}
			print '</td>';
		}
	}
}
// Fields from hook
$parameters=array('arrayfields'=>$arrayfields);
$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))
{
	// Date creation
	print '<td class="liste_titre">';
	print '</td>';
}
if (! empty($arrayfields['t.tms']['checked']))
{
	// Date modification
	print '<td class="liste_titre">';
	print '</td>';
}
/*if (! empty($arrayfields['u.statut']['checked']))
{
	// Status
	print '<td class="liste_titre" align="center">';
	print $form->selectarray('search_statut', array('-1'=>'','0'=>$langs->trans('Disabled'),'1'=>$langs->trans('Enabled')),$search_statut);
	print '</td>';
}*/
// Action column
print '<td class="liste_titre" align="right">';
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 0);
print $searchpitco;
print '</td>';
print '</tr>'."\n";


$i=0;
$var=true;
$totalarray=array();
$object = new Adherentext($db);
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST

		$rU = $objPuser->fetchAll("","",0,0,array(1=>1),"AND","AND t.fk_user = ".$obj->rowid,true);
		if($rU){
			$nDni = $objPuser->docum;
		}else{
			$nDni = 0;
		}
		//$aVacation[$i]["dni"]=$nDni;
		$object->id = $obj->rowid;
		$object->lastname = $obj->lastname;
		$object->firstname = $obj->firstname;
		$object->ref = $obj->docum;
		if (! empty($arrayfields['u.docum']['checked']))
		{
			print '<td>'.$object->getNomUrl(1).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.lastname']['checked']))
		{
			print '<td>'.$obj->lastname.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.firstname']['checked']))
		{
			print '<td>'.$obj->firstname.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		//$aVacation[$i]["allname"]=$obj->lastname." ".$obj->firstname;

		if (! empty($arrayfields['t.login']['checked']))
		{
			$rDu = $objDepUser->fetchAll("","",0,0,array(1=>1),"AND","AND t.fk_user = ".$obj->rowid,true);
			if($rDu >0){
				$rDun = $objDepartamento->fetchAll("","",0,0,array(1=>1),"AND","AND t.rowid = ".$objDepUser->fk_departament,true);
				if($rDun > 0){
					print '<td>'.$objDepartamento->label.'</td>';
					//$aVacation[$i]["area"]=$objDepartamento->label;
				}else{
					print '<td></td>';
				}
			}else{
				print '<td></td>';
				//$aVacation[$i]["area"]=" ";
			}

			if (! $i) $totalarray['nbfield']++;
		}

		if (! empty($arrayfields['t.fk_adherent_type']['checked']))
		{
			$nDiasAsig = 0;
			$nDiasUsa = 0;
			$rMv = $objMemberVacation->fetchAll("","",0,0,array(1=>1),"AND","AND t.status >= 0 AND t.fk_member = ".$obj->rowid);
			if($rMv > 0)
			{
				$lines = $objMemberVacation->lines;
				foreach ($lines as $key => $value)
				{
					$lValidation = true;
					if ($value->status == 0) $lValidation = false;

					$nDiasAsig += $value->days_assigned;

					$rMvd = $objMemberVacDet->fetchAll("","",0,0,array(1=>1),"AND"," AND t.status >= 1 AND t.fk_member_vacation = ".$value->id);
					if($rMvd > 0 ){
						foreach ($objMemberVacDet->lines AS $k => $linedet)
							$nDiasUsa = $nDiasUsa + $linedet->day_used;
					}
				}
				$balance = $nDiasAsig - $nDiasUsa;
				print '<td>'.$balance.' '.$langs->trans('Days').' '.(!$lValidation?$langs->trans('No validado'):'').'</td>';
			   // $aVacation[$i]["saldo"]=($nDiasAsig - $nDiasUsa);
			}else{
				print '<td></td>';
				//$aVacation[$i]["saldo"]=0;
			}
			if (! $i) $totalarray['nbfield']++;
		}

		// Extra fields
		if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
		{
			foreach($extrafields->attribute_label as $key => $val)
			{
				if (! empty($arrayfields["ef.".$key]['checked']))
				{
					print '<td';
					$align=$extrafields->getAlignFlag($key);
					if ($align) print ' align="'.$align.'"';
					print '>';
					$tmpkey='options_'.$key;
					print $extrafields->showOutputField($key, $obj->$tmpkey, '', 1);
					print '</td>';
					if (! $i) $totalarray['nbfield']++;
				}
			}
		}
		// Fields from hook
		$parameters=array('arrayfields'=>$arrayfields, 'obj'=>$obj);
		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		// Date creation
		if (! empty($arrayfields['t.datec']['checked']))
		{
			print '<td align="center">';
			print dol_print_date($db->jdate($obj->date_creation), 'dayhour');
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		// Date modification
		if (! empty($arrayfields['t.tms']['checked']))
		{
			print '<td align="center">';
			print dol_print_date($db->jdate($obj->date_update), 'dayhour');
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		// Status
		/*
		if (! empty($arrayfields['u.statut']['checked']))
		{
		  $userstatic->statut=$obj->statut;
		  print '<td align="center">'.$userstatic->getLibStatut(3).'</td>';
		}*/

		// Action column
		print '<td class="nowrap" align="center">';
		if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
		{
			$selected=0;
			if (in_array($obj->rowid, $arrayofselected)) $selected=1;
			//print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
		}
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
	}
	$i++;
}

//Array of all
$i=0;
$var=true;
$totalarray=array();
//while ($i < min($num, $limit))
while ($i < $nAll)
{
	$obj = $db->fetch_object($resqall);
	if ($obj)
	{
		$rU = $objPuser->fetchAll("","",0,0,array(1=>1),"AND","AND t.fk_user = ".$obj->rowid,true);
		if($rU){
			$nDni = $objPuser->docum;

		}else{
			$nDni = 0;
		}
		$aVacation[$i]["dni"]=$nDni;

		$aVacation[$i]["allname"]=$obj->lastname." ".$obj->firstname;


		$rDu = $objDepUser->fetchAll("","",0,0,array(1=>1),"AND","AND t.fk_user = ".$obj->rowid,true);
		if($rDu >0){
			$rDun = $objDepartamento->fetchAll("","",0,0,array(1=>1),"AND","AND t.rowid = ".$objDepUser->fk_departament,true);
			if($rDun > 0){
				$aVacation[$i]["area"]=$objDepartamento->label;
			}else{

			}
		}else{
			$aVacation[$i]["area"]=" ";
		}

		$nDiasAsig = 0;
		$nDiasUsa = 0;
		$rMv = $objMemberVacation->fetchAll("","",0,0,array(1=>1),"AND","AND t.status >= 1 AND t.fk_member = ".$obj->rowid);
		if($rMv > 0){

			foreach ($objMemberVacation->lines as $key => $value) {
				$nDiasAsig = $nDiasAsig + $value->days_assigned;
				$rMvd = $objMemberVacDet->fetchAll("","",0,0,array(1=>1),"AND","AND t.status >= 1 AND t.fk_member_vacation = ".$value->id,true);
				if($rMvd > 0 ){
					$nDiasUsa = $nDiasUsa + $objMemberVacDet->day_used;
				}
			}


			$aVacation[$i]["saldo"]=($nDiasAsig - $nDiasUsa);
		}else{

			$aVacation[$i]["saldo"]=0;
		}
	}
	$i++;
}


// Show total line
if (isset($totalarray['totalhtfield']))
{
	print '<tr class="liste_total">';
	$i=0;
	while ($i < $totalarray['nbfield'])
	{
		$i++;
		if ($i == 1)
		{
			if ($num < $limit && empty($offset)) print '<td align="left">'.$langs->trans("Total").'</td>';
			else print '<td align="left">'.$langs->trans("Totalforthispage").'</td>';
		}
		elseif ($totalarray['totalhtfield'] == $i) print '<td align="right">'.price($totalarray['totalht']).'</td>';
		elseif ($totalarray['totalvatfield'] == $i) print '<td align="right">'.price($totalarray['totalvat']).'</td>';
		elseif ($totalarray['totalttcfield'] == $i) print '<td align="right">'.price($totalarray['totalttc']).'</td>';
		else print '<td></td>';
	}
	print '</tr>';
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";

print '<div class="tabsAction">'."\n";


$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
// Note that $action and $object may have been modified by hook
//if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
print "<div class=\"tabsAction\">\n";

$_SESSION['aVacation'] = serialize($aVacation);
if (count($aVacation)>0)
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';
print '</div>';



// End of page
llxFooter();
$db->close();