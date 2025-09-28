<?php
/* Copyright (C) 2014-2017 Ramiro Queso        <ramiroques@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/assistance/licence/card.php
 *	\ingroup    Assistance
 *	\brief      Page fiche insert assistance
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');

dol_include_once('/assistance/core/modules/assistance/modules_assistance.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/licenceslog.class.php');
dol_include_once('/assistance/class/ctypelicenceext.class.php');
dol_include_once('/assistance/class/membervacationext.class.php');
dol_include_once('/assistance/class/membervacationdet.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/assistance/lib/assistance.lib.php');
dol_include_once('/assistance/lib/utils.lib.php');

dol_include_once('/core/lib/datefractal.lib.php');

dol_include_once('/adherents/class/adherent.class.php');
dol_include_once('/orgman/class/pdepartament.class.php');
dol_include_once('/orgman/class/pdepartamentuser.class.php');
dol_include_once('/orgman/lib/departament.lib.php');

dol_include_once('/salary/class/pgenerictableext.class.php');
dol_include_once('/salary/class/pgenericfieldext.class.php');

include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');

// Load traductions files requiredby by page
$langs->load("assistance");
$langs->load("companies");
$langs->load("other");
// Get parameters
$id			= GETPOST('id','int');
$ref		= GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$fk_member = GETPOST('fk_member');
$type_licence=GETPOST('type_licence','alpha');
$date_ini = dol_mktime(GETPOST('di_hour'),GETPOST('di_min'),0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
$date_fin = dol_mktime(GETPOST('df_hour'),GETPOST('df_min'),0,GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
if (isset($_GET['page']) || isset($_POST['page']))
	$page = GETPOST('page','int')+0;
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="ASC";

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_member=GETPOST('search_member','alpha');
$search_type=GETPOST('search_type','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_user=GETPOST('search_user','alpha');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_user_aprob=GETPOST('search_fk_user_aprob','int');
$search_fk_user_reg=GETPOST('search_fk_user_reg','int');
$search_statut=GETPOST('search_statut','int');

// El select del buscador
$aStatut = array (99=>$langs->trans('All'),
	-1=>$langs->trans('Annulled'),
	0=>$langs->trans('Draft'),
	9=>$langs->trans('Rejected'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Reviewed'),
	3=>$langs->trans('Approved'),
	4=>$langs->trans('Running'),
	5=>$langs->trans('Finished'),);

// Purge criteria
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_ref='';
	$search_member ='';
	$search_type_licence="";
	$search_user="";
	$search_type="";
	$search_detail ="";
	$search_statut=99;
}

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Licencesext($db);
$objAdherent = new Adherent($db);
$objLicencelog = new Licenceslog($db);
$objDepartament = new Pdepartament($db);
$objDeptUser = new Pdepartamentuser($db);
$objCtypelicence = new Ctypelicenceext($db);
$objMembervacation = new Membervacationext($db);
$objMembervacationdet = new Membervacationdet($db);
if (!$user->admin) list($aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp) = verif_departament();

$formfile = new Formfile($db);


$now = dol_now();
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	if (empty($ref))$ref = NULL;
	$result=$object->fetch($id,$ref);

	if ($result < 0) {
		dol_print_error($db);
	}

	$aCalc = calc_vacation($user, $object->fk_member);

	if ($aCalc[0])
	{
		$nYearcas = $aCalc[1];

		$objGenerictable = new Pgenerictableext($db);
		$objGenericfield = new Pgenericfieldext($db);
		//verificamos si existe registro
		$newdate = dol_time_plus_duree($aCalc[3], 1, 'd');
		$aDate = dol_getdate(dol_now());

		$year = $aDate['year'];
		$res = $objMembervacation->fetch(0,$object->fk_member,$year);
		//nueva fecha inicio
		$newdateini = dol_mktime(12,0,0,$aDate['mon'],$aDate['mday'],$year);

		//fecha fin
		$newYear = $year +2;
		$newdatefin = dol_mktime(12,0,0,$aDate['mon'],$aDate['mday'],$newYear);
		if ($res == 0)
		{
			$filter = " AND t.table_cod = '".$conf->global->SALARY_CODE_VACATION."'";
			$res = $objGenerictable->getTable($filter);
			if (count($objGenerictable->aTable)>0)
			{
				$aTable = $objGenerictable->aTable;
				$newData = array();
				foreach ($aTable AS $seq => $data)
				{
					if ($nYearcas>$data[1] && $nYearcas<=$data[2])
					{

						$nVacation = $data[3];
					}
				}
			}
			if ($nVacation>0)
			{
				//agregamos a la tabla
				$objMembervacation->fk_member = $object->fk_member;
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
				$res = $objMembervacation->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
				}
			}
		}
	}
}

$adherent = new Adherent($db);
if (!$user->admin)
	if($user->fk_member)
		$adherent->fetch($user->fk_member);
	else
		accessforbidden();


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
	$hookmanager->initHooks(array('licences'));
	$extrafields = new ExtraFields($db);
	$_SESSION['period_year'] = date('Y');
	$period_year = $_SESSION['period_year'];

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');



if (empty($reshook))
{
	if ($action == 'builddoc')
	// En get ou en post
	{
		$object->fetch($id);
		$object->fetch_thirdparty();
		$object->typelicence =select_type_licence($object->type_licence,'type_licence','',0,1,'code','label');
		if (GETPOST('model'))
		{
			//$object->setDocModel($user, GETPOST('model'));
		}
		if (GETPOST('model') == 'licence')
			$object->model_pdf = 'licence';
		$object->model_pdf = GETPOST('model');
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
		$result=assistance_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
			exit;
		}
	}
	/*Nuevos cambios que se esta realizando por LMendoza*/

	/**************************************************/
		//Metodo de Anular una solicitud
	if($id && $action == 'anule'&& $object->statut == 0)
	{
		$db->begin();
			//Cambiamos el statut a -1 ya que se arrepentio
		$object->statut = -1;
		$object->tms = dol_now();

		$objLicencelog->fk_licence = $id;
		$objLicencelog->description = $langs->trans('Annulled');
		$objLicencelog->fk_user_create=$user->id;
		$objLicencelog->fk_user_mod=$user->id;
		$objLicencelog->datec=dol_now();
		$objLicencelog->datem=dol_now();
		$objLicencelog->tms=dol_now();
		$objLicencelog->status = $object->statut;
			//hacemos la alta

		$res = $object->update($user);
		if($res <= 0){
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$result=$objLicencelog->create($user);

			if ($result > 0)
			{
				$db->commit();
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/licence/card.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
				$db->rollback();
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null,$objLicencelog->errors, 'errors');
				else  setEventMessages($objLicencelog->error, null, 'errors');
				$action='';
			}
		}
		$action = 'list';
	}

		// Remove file in doc form de parte de los reportes
	if ($action == 'remove_file')
	{

		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->assistance->dir_output;
				//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
				//echo('file'.$file);
		$ret = dol_delete_file($file, 0, 0, 0, $object);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
			//}
	}
		//Accion de Rechazar una Solicitud
	if ($action == 'confirm_refuse' && GETPOST('cancel')) $action='view';
		//print_r($_REQUEST);
		//echo $action; exit;
	if($id && $action == 'confirm_refuse' && $confirm == 'yes')
	{
		$date_modi = dol_mktime($_POST['di_hour'],$_POST['di_min'],0,$_POST['di_month'],$_POST['di_day'],$_POST['di_year'],'user');
		$db->begin();
			//Cambiamos el statut a -1 ya que se arrepentio
		$object->statut = 9;
		$object->tms = dol_now();

		$objLicencelog->fk_licence = $id;
		$objLicencelog->description = $_REQUEST["refuse"];
		$objLicencelog->fk_user_create=$user->id;
		$objLicencelog->fk_user_mod=$user->id;
		$objLicencelog->datec=dol_now();
		$objLicencelog->datem=dol_now();
		$objLicencelog->tms=dol_now();
		$objLicencelog->status = $object->statut;

				//hacemos la alta
		$res = $object->update($user);
		if($res <= 0){
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$result=$objLicencelog->create($user);
					//print_r('Mensaje de Rechazo : '.$_REQUEST["refuse"]);

			if ($result > 0)
			{
				$db->commit();
						// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/licence/card.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
			{
				$db->rollback();
						// Creation KO
				if (! empty($object->errors)) setEventMessages(null,$objLicencelog->errors, 'errors');
				else  setEventMessages($objLicencelog->error, null, 'errors');
				$action='';
			}
		}
		$action = 'list';
	}

	/**************************************************/
	/*
	 * Confirmation de la validation
	 */
	// Cancel
	if ($action == 'confirm_validate' && GETPOST('cancel')) $action='view';
	if ($id && $action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $object->statut == 0)
	{
		$db->begin();
		$ref = substr($object->ref, 1, 4);
		$res = $objCtypelicence->fetch(0,$object->type_licence);
		if ($ref == 'PROV')
		{
			if ($res>0) $object->type = $objCtypelicence->type;
			$numref = $object->getNextNumRef($soc);
		}
		else
		{
			$numref = $object->ref;
		}
		if (empty($numref))
		{
			$error++;
			setEventMessages($langs->trans('No esta activo la numeración, favor verifique'),null,'errors');
		}
		//cambiando a validado
		if ($objCtypelicence->type=='L')
			$object->statut = 2;
		else
			$object->statut = 1;
		$object->ref = $numref;
		//update
		if (!$error)
		{
			$res = $object->update($user);
			if ($res <= 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		if (!$error)
		{
			$objLicencelog->fk_licence = $id;
			$objLicencelog->description = $object->getLibStatut(1);
			$objLicencelog->fk_user_create=$user->id;
			$objLicencelog->fk_user_mod=$user->id;
			$objLicencelog->datec=dol_now();
			$objLicencelog->datem=dol_now();
			$objLicencelog->tms=dol_now();
			$objLicencelog->status = $object->statut;
			//hacemos la alta
			$result=$objLicencelog->create($user);
			if ($result<=0)
			{
				$error++;
				setEventMessages($objLicencelog->error,$objLicencelog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			$object->ref = $numref;
			$resType=$objCtypelicence->fetchAll('','',0,0,array(1=>1),'AND',"AND t.code ='".$object->type_licence."'", true);
			if($resType > 0)
			{
				if($objCtypelicence->type === 'V')
					$modelpdf = 'vacacion';

				if($objCtypelicence->type === 'L')
					$modelpdf = 'licencia';
			}
			else{
				setEventMessages("Error al genera los reporte de tipo",$resType->errors,'errors');
			}
			$object->typelicence =select_type_licence($object->type_licence,'type_licence','',0,1,'code','label');
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
			$db->rollback();
		$action = 'view';
	}
	/*
	 * Confirmation approval
	 */
	// Cancel
	if ($action == 'confirm_approval' && GETPOST('cancel')) $action='view';
	if ($id && $action == 'confirm_approval' && $_REQUEST["confirm"] == 'yes' && $object->statut == 2 && $user->rights->assistance->lic->app)
	{
		print_r($_REQUEST);
		$nDays = GETPOST('nDays','int');
		$db->begin();
		//cambiando a aprovado
		$object->statut = 3;
		$object->fk_user_aprob = $user->id;
		$object->datea = $now;
		$object->tms = $now;
		//update
		$res = $object->update($user);
		if ($res <= 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}

		//registramos en detalle
		$filter = " AND t.fk_member = ".$object->fk_member;
		$filter.= " AND t.status >=0";
		echo '<hr>resl '.$res = $objMembervacation->fetchAll('ASC','t.period_year',0,0,array(1=>1),'AND',$filter);
		$nVac =0;
		if ($conf->global->ASSISTANCE_ALLOW_NEGATIVE_HOLIDAY && $res==0)
		{
			$objMembervacation->fk_member = $object->fk_member;
			$objMembervacation->date_ini = $now;
			$objMembervacation->date_fin = $now;
			$objMembervacation->period_year = date('Y');
			$objMembervacation->days_assigned = 0;
			$objMembervacation->days_used = 0;
			$objMembervacation->fk_user_create = $user->id;
			$objMembervacation->fk_user_mod = $user->id;
			$objMembervacation->fk_user_app = $user->id;
			$objMembervacation->datec = $now;
			$objMembervacation->datem = $now;
			$objMembervacation->datea = $now;
			$objMembervacation->status = 1;
			$res = $objMembervacation->create($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objMembervacation->error,$objMembervacation->errors,'errors');
			}
			$res = $objMembervacation->fetchAll('ASC','t.period_year',0,0,array(1=>1),'AND',$filter);
		}
		if ($res >0)
		{
			$lines = $objMembervacation->lines;
			foreach ($lines AS $j => $line)
			{
				$nVacation = $line->days_assigned;
				$nUsed = 0;
				//obtenemos cuanto se utilizara con esta vacacion asignada
				$filterdet = " AND t.fk_member_vacation = ".$line->id;
				$resdet = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet);
				if ($resdet)
				{
					foreach ($objMembervacationdet->lines AS $k => $linek)
					{
						$nUsed+= $linek->day_used;
					}
				}
				$nVacation-=$nUsed;

				if ($nDays > 0)
				{
					if ($nDays <= $nVacation)
					{
						//registramos y cerramos
						$objMembervacationdet->initAsSpecimen();
						$objMembervacationdet->fk_member_vacation = $line->id;
						$objMembervacationdet->fk_licence = $id;
						$objMembervacationdet->day_used = $nDays;
						$objMembervacationdet->fk_user_create = $user->id;
						$objMembervacationdet->fk_user_mod = $user->id;
						$objMembervacationdet->datec = $now;
						$objMembervacationdet->datem = $now;
						$objMembervacationdet->tms = $now;
						$objMembervacationdet->status = 1;
						$resdet = $objMembervacationdet->create($user);
						if ($resdet<=0)
						{
							$error=101;
							setEventMessages($objMembervacationdet->error,$objMembervacationdet->errors,'errors');
						}
					}
					else
					{
						$nDays = $nDays-$nVacation;
						//registramos y cerramos
						$objMembervacationdet->initAsSpecimen();
						$objMembervacationdet->fk_member_vacation = $line->id;
						$objMembervacationdet->fk_licence = $id;
						$objMembervacationdet->day_used = abs($nVacation);
						$objMembervacationdet->fk_user_create = $user->id;
						$objMembervacationdet->fk_user_mod = $user->id;
						$objMembervacationdet->datec = $now;
						$objMembervacationdet->datem = $now;
						$objMembervacationdet->tms = $now;
						$objMembervacationdet->status = 1;
						$resdet = $objMembervacationdet->create($user);
						if ($resdet<=0)
						{
							$error=102;
							setEventMessages($objMembervacationdet->error,$objMembervacationdet->errors,'errors');
						}
					}
				}
				//actualizamos en membervacation
				$filtertmp = " AND t.fk_member_vacation = ".$line->id;
				$filtertmp.= " AND t.status >= 1";
				$restmp = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp);
				$nUsedapp = 0;
				if ($restmp>0)
				{
					foreach ($objMembervacationdet->lines AS $k => $linetmp)
						$nUsedapp+= $linetmp->day_used;
				}
				$resm = $objMembervacation->fetch($line->id);
				if ($resm>0)
				{
					$objMembervacation->days_used = $nUsedapp;
					$objMembervacation->fk_user_mod = $user->id;
					$objMembervacation->datem = $now;
					$objMembervacation->tms = $now;
					$objMembervacation->update($user);
				}
			}
		}

		if (!$error)
		{
			$sendemail = GETPOST('se');
			if ($sendemail)
			{
				//enviamos correo
				$to = '';
				$adherent->fetch($object->fk_member);
				if ($adherent->id == $object->fk_member)
					$to = $adherent->email;
				$from = $user->email;
				$subject = $langs->trans('Licenceapproval').' '.$object->ref;
				$body = htmlsendapprovallicence();
				if ($conf->global->ASSISTANCE_MESSAGE_SENDMAIL)
				{
					$aRes = send_email($from,$to,$subject,$body);
					$res = $aRes[0];
					$mesg = $aRes[1];
					if ($res <= 0)
					{
						$error++;
						setEventMessages($mesg,null,'errors');
					}
					$action = '';
				}
			}
			//fin envio de correo
		}
		if (!$error)
		{
			$objLicencelog->fk_licence = $id;
			$objLicencelog->description = $object->getLibStatut(1);
			$objLicencelog->fk_user_create=$user->id;
			$objLicencelog->fk_user_mod=$user->id;
			$objLicencelog->datec=dol_now();
			$objLicencelog->datem=dol_now();
			$objLicencelog->tms=dol_now();
			$objLicencelog->status = $object->statut;
			//hacemos la alta
			$result=$objLicencelog->create($user);
			if ($result<=0)
			{
				$error++;
				setEventMessages($objLicencelog->error,$objLicencelog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			$resType=$objCtypelicence->fetchAll('','',0,0,array(1=>1),'AND',"AND t.code ='".$object->type_licence."'", true);
			if($resType > 0)
			{
				if($objCtypelicence->type === 'V')
					$modelpdf = 'vacacion';

				if($objCtypelicence->type === 'L')
					$modelpdf = 'licencia';
			}
			else{
				setEventMessages("Error al genera los reporte de tipo",$resType->errors,'errors');
			}

			//$object->fetch($id);
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
						// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/licence/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
			$db->rollback();

		$action = '';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/licence/card.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$type = GETPOST('type');
		$time_limited = GETPOST('time_limited');
		$time_used = GETPOST('time_used');
		$lLimit = GETPOST('lLimit');
		$error=0;
		$starthalfday=GETPOST('starthalfday');
		$endhalfday=GETPOST('endhalfday');

		$halfday=0;
		if ($starthalfday == 'afternoon' && $endhalfday == 'morning') $halfday=2;
		else if ($starthalfday == 'afternoon') $halfday=-1;
		else if ($endhalfday == 'morning') $halfday=1;

		if ($lLimit)
		{
			//sumamos los permisos del periodo
			$cConf = $conf->global->ASSISTANCE_FORMAT_LIMITED_TIME;
				//mensual es 1
			$cConf = '1';
			if ($cConf == 1)
			{
				$aDateini = dol_getdate($date_ini);
				$aDatefin = dol_getdate($date_fin);
				$nMonthini = $aDateini['mon'];
				$nMonthfin = $aDatefin['mon'];
				$nYearini = $aDateini['year'];
				if ($nMonthini != $nMonthfin)
				{
					$error++;
					setEventMessages($langs->trans('Se esta solicitando licencia en meses diferentes'),null,'errors');
				}
				$date_ini.' '.$date_fin.' '.dol_print_date($date_ini,'dayhour').' '.dol_print_date($date_fin,'dayhour');
				$dif = $date_fin-$date_ini;
				$nUsednew = (convertSecondToTime($dif, 'fullhour' )*60);

				$nTotal = $time_used+$nUsednew;
				if ($nTotal > $time_limited)
				{
					$error++;
					setEventMessages($langs->trans('No puede hacer uso de licencia ya que sobrepasa al permitido'),null,'errors');
				}
			}
		}

		$code = generarcodigo(3);
		$object->entity=$conf->entity;
		$object->ref= '(PROV)'.$code;
		$object->fk_member=GETPOST('fk_member','int');
		$object->type_licence=GETPOST('type_licence','alpha');
		$object->detail=GETPOST('detail','alpha');
		$object->date_ini=$date_ini;
		$object->date_fin=$date_fin;
		$object->halfday = $halfday;
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->date_create = dol_now();
		$object->tms = dol_now();
		$object->statut=0;

		$object->fk_user_aprob=0;
		$object->fk_user_reg=0;
		$object->datem= dol_now();
		//verificamos tipo de licencia
		$resType=$objCtypelicence->fetchAll('','',0,0,array(1=>1),'AND',"AND t.code ='".$object->type_licence."'", true);
		$lValiddate = true;
		if($resType > 0)
		{
			if($objCtypelicence->type === 'V')
				$lValiddate = false;
		}
		else
		{
			$error=101;
			setEventMessages($objCtypelicence->error,$objCtypelicence->errors,'errors');
		}
		if($date_fin <= $date_ini && $lValiddate){
			$error=102;
			setEventMessages('Theenddateandtimemustbegreaterthanthestartdate', null, 'errors');
		}

		if (empty($object->ref))
		{
			$error=103;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
		}
		if ($object->date_ini > $object->date_fin)
		{
			$error++;
			setEventMessages($langs->trans("Theinitialdatecannotbelongerthanthefinaldate"), null, 'errors');
		}

		//vamos a validar si existe una solicitud de licencia que este dentro del rango de fecha de inicio
		$objTmp=new Licencesext($db);
		$filter = " AND ".$db->idate($date_ini) ." BETWEEN t.date_ini AND t.date_fin ";
		$filter.= " AND t.statut >=0 ";
		$filter.= " AND t.fk_member = ".$object->fk_member;
		$res = $objTmp->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		if ($res>0)
		{
			$error++;
			setEventMessages($langs->trans("Thereisarequestdatedandsimilartimecheck"),null,'errors');
		}
		$db->begin();
		if (! $error)
		{
			$result=$object->create($user);
			if ($result <=0)
			{
				$error=104;
				if (! empty($object->errors)) setEventMessages(null,$object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
			}
		}
		if (!$error)
		{
			$objLicencelog->fk_licence = $result;
			$objLicencelog->description = $object->getLibStatut(1);
			$objLicencelog->fk_user_create=$user->id;
			$objLicencelog->fk_user_mod=$user->id;
			$objLicencelog->datec=dol_now();
			$objLicencelog->datem=dol_now();
			$objLicencelog->tms=dol_now();
			$objLicencelog->status = $object->statut;
			//hacemos la alta
			$resl=$objLicencelog->create($user);
			if ($resl<=0)
			{
				$error=105;
				setEventMessages($objLicencelog->error,$objLicencelog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
								// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/licence/card.php?id='.$result,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			$db->rollback();
			$action='create';
		}

	}

	if ($action == 'update_review' && GETPOST('cancel')) $action='';
	if ($action == 'update_review' && ! GETPOST('cancel'))
	{
		$error=0;
		$db->begin();
		$halfday_ejec=0;
		if ($starthalfday == 'afternoon' && $endhalfday == 'morning') $halfday_ejec=2;
		else if ($starthalfday == 'afternoon') $halfday_ejec=-1;
		else if ($endhalfday == 'morning') $halfday_ejec=1;

		/* object_prop_getpost_prop */
		$date_ini_ejec = dol_mktime(($_POST['dir_hour']?$_POST['dir_hour']:0),($_POST['dir_min']?$_POST['dir_min']:0),0,$_POST['dir_month'],$_POST['dir_day'],$_POST['dir_year'],'user');
		$date_fin_ejec = dol_mktime(($_POST['dfr_hour']?$_POST['dfr_hour']:0),($_POST['dfr_min']?$_POST['dfr_min']:0),0,$_POST['dfr_month'],$_POST['dfr_day'],$_POST['dfr_year'],'user');

		//$object->fk_member=GETPOST('fk_member','int');
		//$object->type_licence=GETPOST('type_licence','alpha');
		//$object->detail=GETPOST('detail','alpha');
		$object->date_ini_ejec=$date_ini_ejec;
		$object->date_fin_ejec=$date_fin_ejec;
		$object->halfday_ejec=$halfday_ejec;
		$object->fk_user_mod=$user->id;
		$object->fk_user_rev=$user->id;
		$object->statut = 2;
		$object->datem = dol_now();
		$object->datev = dol_now();
		$object->tms = dol_now();
		if (empty($object->date_ini_ejec))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fielddate_ini_ejec")),null,'errors');
		}
		if (!$error)
		{
			$objLicencelog->fk_licence = $id;
			$objLicencelog->description = $object->getLibStatut(1);
			$objLicencelog->fk_user_create=$user->id;
			$objLicencelog->fk_user_mod=$user->id;
			$objLicencelog->datec=dol_now();
			$objLicencelog->datem=dol_now();
			$objLicencelog->tms=dol_now();
			$objLicencelog->status = $object->statut;
			//hacemos la alta
			$result=$objLicencelog->create($user);
			if ($result<=0)
			{
				$error++;
				setEventMessages($objLicencelog->error,$objLicencelog->errors,'errors');
			}
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='review';
			}
		}
		else
		{
			$action='review';
		}
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		$halfday=0;
		if ($starthalfday == 'afternoon' && $endhalfday == 'morning') $halfday=2;
		else if ($starthalfday == 'afternoon') $halfday=-1;
		else if ($endhalfday == 'morning') $halfday=1;

		/* object_prop_getpost_prop */
		$date_ini = dol_mktime(($_POST['di_hour']?$_POST['di_hour']:0),($_POST['di_min']?$_POST['di_min']:0),0,$_POST['di_month'],$_POST['di_day'],$_POST['di_year'],'user');
		$date_fin = dol_mktime(($_POST['df_hour']?$_POST['df_hour']:0),($_POST['df_min']?$_POST['df_min']:0),0,$_POST['df_month'],$_POST['df_day'],$_POST['df_year'],'user');

		$object->fk_member=GETPOST('fk_member','int');
		$object->type_licence=GETPOST('type_licence','alpha');
		$object->detail=GETPOST('detail','alpha');
		$object->date_ini=$date_ini;
		$object->date_fin=$date_fin;
		$object->halfday=$halfday;

		$object->fk_user_mod=$user->id;
		$object->tms = dol_now();
		if ($object->statut == 9) $object->statut = 0;
		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
		}
		if (!$error)
		{
			$objLicencelog->fk_licence = $id;
			$objLicencelog->description = $object->getLibStatut(1);
			$objLicencelog->fk_user_create=$user->id;
			$objLicencelog->fk_user_mod=$user->id;
			$objLicencelog->datec=dol_now();
			$objLicencelog->datem=dol_now();
			$objLicencelog->tms=dol_now();
			$objLicencelog->status = $object->statut;
			//hacemos la alta
			$result=$objLicencelog->create($user);
			if ($result<=0)
			{
				$error++;
				setEventMessages($objLicencelog->error,$objLicencelog->errors,'errors');
			}
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='';
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
	if ($action == 'delete')
	{
		if (!$error)
		{
			$objLicencelog->fk_licence = $id;
			$objLicencelog->description = $langs->trans('Delete');
			$objLicencelog->fk_user_create=$user->id;
			$objLicencelog->fk_user_mod=$user->id;
			$objLicencelog->datec=dol_now();
			$objLicencelog->datem=dol_now();
			$objLicencelog->tms=dol_now();
			$objLicencelog->status = -2;
			//hacemos la alta
			$result=$objLicencelog->create($user);
			if ($result<=0)
			{
				$error++;
				setEventMessages($objLicencelog->error,$objLicencelog->errors,'errors');
			}
		}
		$result=$object->delete($user);

		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistance/licence/card.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}

	//Accion de registrar la salida y ingreso de la solicitud
  // Action to update record
if ($action == 'reg_Solicitud' && GETPOST('cancel')) $action='list';
if ($action == 'reg_Solicitud'&& !GETPOST('cancel'))
{

	$error=0;
	$id = GETPOST('id');
		//echo('Entra aqui id'.$id);
		//echo('Entra aqui $user->id '.$user->id);
		//exit;
	/* object_prop_getpost_prop */
		//$date_reg = dol_mktime(GETPOST('dr_hour'),GETPOST('dr_min'),0,GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'),'user');
	$date_reg = dol_mktime($_POST['dr_hour'],$_POST['dr_min'],0,$_POST['dr_month'],$_POST['dr_day'],$_POST['dr_year'],'user');
		//echo('La fecha es : '.$date_reg);exit;
		//$db->begin();
	$object->fk_user_reg=GETPOST('id');
	$object->tms = dol_now();

	$objLicencelog->fk_licence = GETPOST('id');
	$objLicencelog->description = '';
	$objLicencelog->fk_user_create=$user->id;
	$objLicencelog->fk_user_mod=$user->id;
	$objLicencelog->datec=dol_now();
	$objLicencelog->datem=dol_now();
	$objLicencelog->tms=dol_now();

	switch (GETPOST('statut')) {
		case 2:
		$object->statut = 4;
		$object->date_ini_ejec=$date_reg;

		break;
		case 3:
		$object->statut = 5;
		$object->date_fin_ejec=$date_reg;
		break;
	}
	$objLicencelog->status = $object->statut;
	  //print_r ('object : '.dol_now());exit;


	if (! $error)
	{
		$result=$object->update($user);
		if ($result > 0)
		{
			$resulte=$objLicencelog->create($user);
						//print_r('Mensaje de Rechazo : '.$_REQUEST["refuse"]);

			if ($resulte > 0)
			{
							// Creation OK
				setEventMessages("Se ingreso la fecha y hora del permiso", null, 'mesgs');
				$action='list';
			}
			else
			{
							// Creation KO
				if (! empty($object->errors)) setEventMessages(null,$objLicencelog->errors, 'errors');
				else  setEventMessages($objLicencelog->error, null, 'errors');
				$action='';
			}
		}
	}
	else
	{
		$action='list';
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Licences'),'');

$form = new Formv($db);

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

if ($id>0)
{
	$result = $object->fetch($id);
	if ($result<=0)
	{
	}
	else
		$type_licence = (GETPOST('type_licence')?GETPOST('type_licence'):$object->type_licence);

}
//armamos el select para typelicence
$optionstype = '<option value="">'.$langs->trans('Select').'</option>';
$res = $objCtypelicence->fetchAll('ASC','t.label',0,0,array(1=>1),'AND');
if ($res > 0)
{
	foreach ($objCtypelicence->lines AS $j => $line)
	{
		$selected = '';
		$typelicence = (GETPOST('type_licence')?GETPOST('type_licence'):$object->type_licence);
		if ( $typelicence == $line->code) $selected = ' selected';
		$optionstype.= '<option value="'.$line->code.'" '.$selected.'>'.$line->label.'</option>';
	}
}
$lHour = true;
if (!empty($type_licence))
{
	$res = $objCtypelicence->fetch(0,$type_licence);
	if ($res == 1 && $objCtypelicence->type == 'V') $lHour = false;
}
// Part to show a list
if ($action == 'list' || (empty($id) && $action != 'create'))
{
	// Put here content of your page
	print load_fiche_titre($langs->trans('Licencespermissions'));

	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql .= " t.entity,";
	$sql .= " t.ref,";
	$sql .= " t.fk_member,";
	$sql .= " t.date_ini,";
	$sql .= " t.date_fin,";
	$sql .= " t.date_ini_ejec,";
	$sql .= " t.date_fin_ejec,";
	$sql .= " t.type_licence,";
	$sql .= " t.detail,";
	$sql .= " t.date_create,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.fk_user_aprob,";
	$sql .= " t.fk_user_reg,";
	$sql .= " t.tms,";
	$sql .= " t.datem,";
	$sql .= " t.datea,";
	$sql .= " t.dater,";
	$sql .= " t.statut,";

	$sql.= " a.lastname,";
	$sql.= " a.firstname,";
	$sql.= " a.email,";
	$sql.= " a.birth,";
	$sql.= " a.phone,";
	$sql.= " a.phone_mobile, ";
	$sql.= " l.code AS codetype, l.label AS detailtype ";
	$sql.= " , d.rowid AS rowiddep, d.ref AS departamentref, d. label AS departamentlabel ";
	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."licences as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as a ON t.fk_member = a.rowid ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_licence as l ON t.type_licence = l.code ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_departament_user AS u ON a.rowid = u.fk_user ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_departament AS d ON u.fk_departament = d.rowid ";

	$sql.= " WHERE t.entity = ".$conf->entity;
	//$sql.= " AND l.entity = ".$conf->entity;
	$sqltmp = "";
	if ($user->rights->assistance->lic->crear)
	{
		if (!$user->admin)
		{
			if (!$user->rights->assistance->lic->all) $sqltmp.= " t.fk_member = ".$user->fk_member;
		}
	}

	if ($user->rights->assistance->lic->app)
	{
		if (!$user->rights->assistance->lic->all)
		{
			if (!empty($sqltmp)) $sqltmp.= " OR ";
			if (count($aAreadirect)>0)
				$sqltmp.= " d.rowid IN (".implode(',',$aAreadirect).")";
			else
				$sqltmp.= " d.rowid IN (0)";
		}
	}
	if (!$user->admin)
	{
		if (!empty($sqltmp))
			$sql.= " AND (".$sqltmp.")";
	}

	if ($search_ref) $sql.= natural_search("t.ref",$search_ref);
	if ($search_member) $sql.= natural_search(array("a.lastname","a.firstname","a.login"),$search_member);
	if ($search_type) $sql.= natural_search("l.label",$search_type);
	if ($search_detail) $sql.= natural_search("t.detail",$search_detail);
	if ($search_statut != 99) $sql.= natural_search("t.statut",$search_statut);

	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.=$db->order($sortfield,$sortorder);

	// Count total nb of records
	$nbtotalofrecords = 0;
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}

	//$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);


	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		$params='';
		$params.= '&amp;search_ref='.urlencode($search_ref);
		$params.= '&amp;search_member='.urlencode($search_member);

		print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');


		print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';

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

		print '<table class="noborder centpercent">'."\n";

	// Fields title
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Member'),$_SERVER['PHP_SELF'],'a.lastname','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Typelicence'),$_SERVER['PHP_SELF'],'t.type_licence','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Justification'),$_SERVER['PHP_SELF'],'t.detail','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Dateini'),$_SERVER['PHP_SELF'],'t.date_ini','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Datefin'),$_SERVER['PHP_SELF'],'t.date_fin','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'t.statut','',$param,'align="right"',$sortfield,$sortorder);

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
	    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";

	// Fields title search
		print '<tr class="liste_titre">';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_member" value="'.$search_member.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_type" value="'.$search_type.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td class="liste_titre" align="right">';
		print $form->selectarray('search_statut',$aStatut,$search_statut);
		print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("RemoveFilter"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
		print '</td>';

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);
	    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";


		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				// you can use here results
				$objAdherent->id = $obj->fk_member;
				$objAdherent->ref = $obj->fk_member;
				$objAdherent->lastname = $obj->lastname;
				$objAdherent->firstname = $obj->firstname;
				print '<tr>';
				$object->fetch($obj->rowid);
				print '<td>'.$object->getNomUrl().'</td>';
				print '<td>'.$objAdherent->getNomUrl(1).' '.$obj->lastname.' '.$obj->firstname.'</td>';
				print '<td>'.select_type_licence($obj->type_licence,'type_licence','',0,1,'code','label').'</td>';
				print '<td>'.$obj->detail.'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->date_ini),'dayhour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->date_fin),'dayhour').'</td>';
				print '<td align="right">'.$object->getLibStatut(6).'</td>';


				$parameters=array('obj' => $obj);
				$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);
				// Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;
				print '</tr>';
			}
			$i++;
		}

		$db->free($resql);

		$parameters=array('sql' => $sql);
		$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
	    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print "</table>\n";
		print "</form>\n";

	// Buttons
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	    // Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if ($user->rights->assistance->lic->crear)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create">'.$langs->trans("New").'</a></div>'."\n";
			}
		}
		print '</div>'."\n";

	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}
$listhalfday=array('morning'=>$langs->trans("Morning"),"afternoon"=>$langs->trans("Afternoon"));

// Part to create
if ($action == 'create' && $user->rights->assistance->lic->crear)
{
	print_fiche_titre($langs->trans("New"));
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#type_licence").change(function() {
				document.formlic.action.value="create";
				document.formlic.submit();
			});
		});';
		print '</script>'."\n";
	}

	print '<form name="formlic" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";

	print '<tr><td class="fieldrequired">'.$langs->trans("Member").'</td><td>';
	if ($user->admin || $user->rights->assistance->lic->crearall)
	{
		print $form->select_member(($fk_member?$fk_member:GETPOST('fk_member')), 'fk_member', '', 1, 0, 0, array(), 0,'autofocus');
	}
	else
	{
		print $adherent->lastname.' '.$adherent->firstname;
		print '<input class="flat" type="hidden" name="fk_member" value="'.$user->fk_member.'">';
	}
	print '</td></tr>';
	//obtenemos el tipo de licencia
	$lLimit = false;
	if (!empty($type_licence))
	{
		$res = $objCtypelicence->fetch(0,$type_licence);
		if ($res == 1)
		{
			$type = $objCtypelicence->type;
			$nLimit = $objCtypelicence->limited_time;
			$typeLimit = $objCtypelicence->type_limited;
		}
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
	print '<select id="type_licence" name="type_licence">'.$optionstype.'</select>';
	print '<input type="hidden" name="type" value="'.$type.'">';

	//print select_type_licence($type,'type_licence','',1,0,'code','label');
			//print select_type_licence($type_licence,'type_licence','',1,0,'code','label');
	print '</td></tr>';

	if ($type == 'L')
	{
		if ($nLimit > 0)
		{
			$lLimit = true;
				//sumamos los permisos del periodo
			$cConf = $typeLmit;
				//mensual es 1
			$cConf = '1';
			if ($cConf == 1)
			{
				$aDateini = dol_getdate($date_ini);
				$aDatefin = dol_getdate($date_fin);
				$nMonthini = $aDateini['mon'];
				$nMonthfin = $aDatefin['mon'];
				$nYearini = $aDateini['year'];
				if ($nMonthini != $nMonthfin)
				{
					$error++;
					setEventMessages($langs->trans('Se esta solicitando licencia en meses diferentes'),null,'errors');
				}
			}
			else
			{
				$aDateini = dol_getdate($date_ini);
				$aDatefin = dol_getdate($date_fin);
				$nMonthini = $aDateini['mon'];
				$nMonthfin = $aDatefin['mon'];
				$nYearini = $aDateini['year'];
				$nYearfin = $aDatefin['year'];
				if ($nyearini != $nYearfin)
				{
					$error++;
					setEventMessages($langs->trans('Se esta solicitando licencia en periodos diferentes'),null,'errors');
				}
			}
			$objTmp =new Licencesext($db);
			$filter = " AND t.fk_member = ".$fk_member;
			if ($cConf == 1)
				$filter.= " AND MONTH(t.date_ini_ejec) = ".$nMonthini;
			$filter.= " AND YEAR(t.date_ini_ejec) = ".$nYearini;
			$filter.= " AND t.type_licence = '".$type_licence."'";
			$filter.= " AND t.status >= 2";
			$res = $objTmp->fetchAll('','',0,0,array(1=>1),'AND',$filter);
			if ($res > 0)
			{
				$lines = $objTmp->lines;
				foreach ($lines AS $j => $line)
				{
					$dif = $line->date_fin-$line->date_ini;
					$nUsed += convertSecondToTime($dif, 'fullhour' );
				}
			}
			$nUsed = $nUsed * 60;
			$nMinutes = $nLimit * 60;
			print '<tr><td class="fieldrequired">'.$langs->trans("Usedtime").'</td><td>';
			print $nUsed.' '.$langs->trans('The').' '.$nMinutes.' '.$langs->trans('Minutes');
			print '<input type="hidden" name="time_used" value="'.$nUsed.'">';
			print '<input type="hidden" name="time_limited" value="'.$nMinutes.'">';
			print '<input type="hidden" name="lLimit" value="'.$lLimit.'">';
			print '</td></tr>';
		}
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Motive").'</td><td>';
			//print '<textarea name="detail" rows="2" cols="50" >'.$detail.'</textarea>';
	print '<textarea name="detail" rows="2" cols="50" >'.GETPOST('detail').'</textarea>';

	print '</td></tr>';
	$viewHour = 1;
	if (!$lHour) $viewHour = 0;
	print '<tr><td class="fieldrequired">'.$langs->trans("Dateini").'</td><td>';
	print $form->select_date((empty($date_ini)?dol_now():$date_ini),'di_',$viewHour,$viewHour,1,'date_ini',1);
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $form->selectarray('starthalfday', $listhalfday, (GETPOST('starthalfday')?GETPOST('starthalfday'):'morning'));
	}
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Datefin").'</td><td>';
	print $form->select_date((empty($date_fin)?dol_now():$date_fin),'df_',$viewHour,$viewHour,1,'date_fin',1);
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $form->selectarray('endhalfday', $listhalfday, (GETPOST('endhalfday')?GETPOST('endhalfday'):'afternoon'));
	}
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp;
	<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#type_licence").change(function() {
				document.formlic.action.value="edit";
				document.formlic.submit();
			});
		});';
		print '</script>'."\n";
	}

	print '<form name="formlic" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';


	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";

	print '<tr><td class="fieldrequired">'.$langs->trans("Member").'</td><td>';
	if ($user->admin || $user->rights->assistance->lic->crearall)
	{
		print $form->select_member((GETPOST('fk_member')?GETPOST('fk_member'):$object->fk_member), 'fk_member', '', 1, 0, 0, array(), 0,'autofocus');
	}
	else
	{
		print $adherent->lastname.' '.$adherent->firstname;
		print '<input class="flat" type="hidden" name="fk_member" value="'.$user->fk_member.'">';
	}
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
	print '<select id="type_licence" name="type_licence">'.$optionstype.'</select>';
	//print select_type_licence($object->type_licence,'type_licence','',1,0,'code','label');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Justification").'</td><td>';
	print '<textarea name="detail" rows="2" cols="50" >'.$object->detail.'</textarea>';
	print '</td></tr>';
	$viewHour = 1;
	if (!$lHour) $viewHour = 0;

	print '<tr><td class="fieldrequired">'.$langs->trans("Dateini").'</td><td>';
	print $form->select_date((!empty($object->date_ini)?$object->date_ini:dol_now()),'di_',$viewHour,$viewHour,1,'date_ini',1);
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $form->selectarray('starthalfday', $listhalfday, (GETPOST('starthalfday')?GETPOST('starthalfday'):'morning'));
	}
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Datefin").'</td><td>';
	print $form->select_date((!empty($object->date_fin)?$object->date_fin:dol_now()),'df_',$viewHour,$viewHour,1,'date_fin',1);
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $form->selectarray('endhalfday', $listhalfday, (GETPOST('endhalfday')?GETPOST('endhalfday'):'afternoon'));
	}
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="update" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

// Parte de Validar y Aprobacion una licencia solicitada
if ($id && (empty($action) ||($action != 'create' && $action != 'edit') || $action == 'review'))
{
	$error=0;
	$lHour = true;
	$res = $objCtypelicence->fetch(0,$object->type_licence);
	if ($res <=0)
		setEventMessages($objCtypelicence->error,$objCtypelicence->errors,'errors');
	elseif($objCtypelicence->type == 'V') $lHour = false;

	//Parte de rechazar una solicitud
	if($action == 'refuse'){
		$formquestion = array(
			array('type'=>'text','label'=>$langs->trans('Reasonforrejection'),'size'=>40,'name'=>'refuse','value'=>'','placeholder'=>$langs->trans('Enterreasonforrejection'))
		);
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Refuse"),$langs->trans("Confirmrefuse",$object->ref),"confirm_refuse",$formquestion,1,2);
		if ($ret == 'html') print '<br>';
	}

	if ($action == 'validate')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Validate"),$langs->trans("Confirmvalidate",$object->ref),"confirm_validate",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	//formato de fechas
	if ($lHour)
	{
		$object->date_ini = $db->jdate($object->dateini);
		$object->date_fin = $db->jdate($object->datefin);
	}
	else
	{
		$aDateini = dol_getdate($object->dateini);
		$object->date_ini_gmt = $db->jdate($object->dateini,1);
		$object->date_fin_gmt = $db->jdate($object->datefin,1);
	}
	//dias solicitados
	$halfday = $object->halfday;
	$days = num_open_day_fractal($object->date_ini_gmt, $object->date_fin_gmt, 0, 1, $object->halfday);
	 // Confirm approval request
	if ($action == 'approval' || $action == 'approvaltwo')
	{
		$formquestion = '';
		$lStatus = true;
		$nVac = 0;
		if ($objCtypelicence->type == 'V')
		{
			//verificamos si tiene aprobado las vacaciones por miembro
			$filter = " AND t.fk_member = ".$object->fk_member;
			$filter.= " AND t.status >=0";
			$res = $objMembervacation->fetchAll('ASC','t.period_year',0,0,array(1=>1),'AND',$filter);
			if ($res >0)
			{
				foreach ($objMembervacation->lines AS $j => $line)
				{
					if ($line->status == 0) $lStatus = false;
					$nVacation = $line->days_assigned;
					$nUsed = 0;
					//obtenemos cuanto se utilizara con esta vacacion asignada
					$filterdet = " AND t.fk_member_vacation = ".$line->id;
					$resdet = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet);
					if ($resdet)
					{
						foreach ($objMembervacationdet->lines AS $k => $linek)
						{
							$nUsed+= $linek->day_used;
						}
					}
					$nVacation-=$nUsed;

					$formquestion[$nVac] = array('type'=>"other",'label'=>$langs->trans('Gestion').' '.$line->period_year.' '.$langs->trans('Availabledays'),'value'=>$nVacation);
					$nVac++;
				}
			}
			else
				$lStatus=false;
		}

		if ($conf->global->ASSISTANCE_MESSAGE_SENDMAIL)
		{
			$nVac++;
			$formquestion[$nVac]= array('type'=>'checkbox','name'=>'se','label'=>$langs->trans('Sendemail'));
			$lSendmail = true;
		}
		$actionnext = 'confirm_approval';
		$titleHead = $langs->trans("ConfirmApprove").' '.$object->ref;
		if (!$lStatus)
		{
			if ($action != 'approvaltwo')
			{
				$actionnext = 'approvaltwo';
				$formquestion = array();
				$nVac++;
				$formquestion[$nVac]= array('type'=>'other','label'=>$langs->trans('Warnings'),'value'=>$langs->trans('No estan verificados las vacaciones, solicite la verificación al area de Recursos Humanos'));
				$titleHead = $langs->trans('Areyousuretocontinuewiththeapproval');
				echo '<hr>entra ';
			}
		}
		$nVac++;
		$formquestion[$nVac]=array('type'=>'hidden','name'=>'nDays','value'=>$days);
			//$formquestion = array($aQuestion);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&nDays='.$days.($lSendmail?'&se='.$langs->trans('Sendmail'):''),
			($objCtypelicence->type == 'L'?$langs->trans("Licence"):$langs->trans('Vacation')),
			$titleHead,
			$actionnext,
			$formquestion,
			1,2);
		if ($ret == 'html') print '<br>';
	}

	$head = licence_prepare_head($object);
	dol_fiche_head($head, 'card', $langs->trans("Licences"), 0, '');

	$starthalfday=($object->halfday == -1 || $object->halfday == 2)?'afternoon':'morning';
	$endhalfday=($object->halfday == 1 || $object->halfday == 2)?'morning':'afternoon';
	$starthalfdayejec=($object->halfday_ejec == -1 || $object->halfday_ejec == 2)?'afternoon':'morning';
	$endhalfdayejec=($object->halfday_ejec == 1 || $object->halfday_ejec == 2)?'morning':'afternoon';

	print '<table class="border centpercent">'."\n";

	print '<tr><td width="15%">'.$langs->trans("Ref").'</td><td colspan="2">';
	print $object->ref;
	print '</td></tr>';

	print '<tr><td width="15%">'.$langs->trans("Name").'</td><td colspan="2">';
	$adherent->fetch($object->fk_member);
	print $adherent->getNomUrl(1).' '.$adherent->lastname.' '.$adherent->firstname;
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Type").'</td><td colspan="2">';
	print select_type_licence($object->type_licence,'type_licence','',0,1,'code','label');
	$typelicence =select_type_licence($object->type_licence,'type_licence','',0,1,'code','label');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Justification").'</td><td colspan="2">';
	print $object->detail;
	print '</td></tr>';
	$viewHour = 1;
	if (!$lHour) $viewHour = 0;
	$object->lHour = $lHour;

	print '<tr><td>'.$langs->trans("Dateini").'</td><td>';
	print dol_print_date($object->date_ini,($viewHour?'dayhour':'day'));
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $langs->trans($listhalfday[$starthalfday]);
		$object->halfdayininame = $langs->trans($listhalfday[$starthalfday]);
	}

	print '</td>';

	if ($object->statut>1)
	{
		if ($lHour)
			print '<td> Registro de Salida  : '.dol_print_date($object->date_ini_ejec,'dayhour').'</td>';
		else
		{
			print '<td> Registro Salida Vacación : '.dol_print_date($object->date_ini_ejec,'day');
			print ' &nbsp; &nbsp; ';
			print $langs->trans($listhalfday[$starthalfdayejec]);
			$object->halfdayininame = $langs->trans($listhalfday[$starthalfdayejec]);
			print '</td>';
		}
	}
	print '</tr>';

	print '<tr><td>'.$langs->trans("Datefin").'</td><td>';
	print dol_print_date($object->date_fin,($viewHour?'dayhour':'day'));
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $langs->trans($listhalfday[$endhalfday]);
		$object->halfdayejecfinname = $langs->trans($listhalfday[$endhalfday]);
	}
	print '</td>';
	if ($object->statut>1)
	{
		if ($lHour)
			print '<td> Registro de Regreso : '.dol_print_date($object->date_fin_ejec,'dayhour').'</td>';
		else
		{
			print '<td> Registro Regreso Vacación : '.dol_print_date($object->date_fin_ejec,'day');
			print ' &nbsp; &nbsp; ';
			print $langs->trans($listhalfday[$endhalfdayejec]);
			$object->halfdayejecininame = $langs->trans($listhalfday[$endhalfdayejec]);
			print '</td>';
		}
	}

	if (!$lHour && $object->date_ini && $object->date_fin)
	{
		print '<tr><td>'.$langs->trans("Daysrequested").'</td><td>';
		print $days;
		$object->days = $days;
		print '</td>';
		echo $object->statut;
		if (!$lHour && $object->statut >=3)
		{
			//buscamos cuanto se le asigno
			$filterdet = " AND t.fk_licence = ".$object->id ;
			$res = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet,true);
			if ($res == 1)
			{
				print '<td>';
				print $langs->trans('Daysapproved').': '.$objMembervacationdet->day_used;
				print '</td>';
			}

		}
		print '</tr>';
	}
	if ($user->rights->assistance->lic->app || $user->rights->assistance->vac->rev)
	{
		if ($objCtypelicence->type == 'V')
		{
			print '<tr><td>'.$langs->trans("Daysassigned").'</td><td>';
			//calculamos el tiempo de vacacion que cuenta el miembro
			$filter = " AND t.fk_member = ".$object->fk_member;
			$filter.= " AND t.status >=0";
			$res = $objMembervacation->fetchAll('ASC','t.period_year',0,0,array(1=>1),'AND',$filter);
			$nVac =0;
			if ($res >0)
			{
				$lines = $objMembervacation->lines;
				foreach ($lines AS $j => $line)
				{
					$objMembervacation->status = $line->status;
					$nVacation = $line->days_assigned;
					$nUsed = 0;
					//obtenemos cuanto se utilizo con esta vacacion asignada
					$filterdet = " AND t.fk_member_vacation = ".$line->id;
					$resdet = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet);
					if ($resdet)
					{
						foreach ($objMembervacationdet->lines AS $k => $linek)
						{
							$nUsed+= $linek->day_used;
						}
					}
					$nVacation-=$nUsed;

					$nVac++;
					print '<i>'.$langs->trans('Gestion').':</i> '.$line->period_year.';<i> '.$langs->trans('Balance').':</i> '.$nVacation.' '.$langs->trans('Days').'; <i>'.$langs->trans('Statut').': </i> '.$objMembervacation->getLibStatut(3);
					print '<br>';
				}
				print '</td>';
			}
			else
			{
				print $langs->trans('Notassignedvacation').'</td>';
			}
			print '</tr>';
		}
	}

	print '<tr><td>'.$langs->trans("Statut").'</td><td colspan="2">';
	print $object->getLibStatut(3);
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	if ($action == 'review' && $user->rights->assistance->vac->rev)
	{
		print '<form name="formlic" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';

		print '<input type="hidden" name="action" value="update_review">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';

		dol_fiche_head();

		print '<table class="border centpercent">'."\n";

		$viewHour = 1;
		if (!$lHour) $viewHour = 0;
		$date_ini_ejec = (GETPOST('date_ini_ejec')?GETPOST('date_ini_ejec'):($object->date_ini_ejec?$object->date_ini_ejec:$object->date_ini));
		$date_fin_ejec = (GETPOST('date_fin_ejec')?GETPOST('date_fin_ejec'):($object->date_fin_ejec?$object->date_fin_ejec:$object->date_fin));
		print '<tr><td class="fieldrequired">'.$langs->trans("Dateini").'</td><td>';
		print $form->select_date($date_ini_ejec,'dir_',$viewHour,$viewHour,1,'date_ini',1);
		if (!$lHour)
		{
			print ' &nbsp; &nbsp; ';
			print $form->selectarray('starthalfday', $listhalfday, (GETPOST('starthalfday')?GETPOST('starthalfday'):'morning'));
		}
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Datefin").'</td><td>';
		print $form->select_date($date_fin_ejec,'dfr_',$viewHour,$viewHour,1,'date_fin',1);
		if (!$lHour)
		{
			print ' &nbsp; &nbsp; ';
			print $form->selectarray('endhalfday', $listhalfday, (GETPOST('endhalfday')?GETPOST('endhalfday'):'afternoon'));
		}
		print '</td></tr>';

		print '</table>'."\n";

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="update" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';

	}
		//print_r($object);
		//Variables para generar el reporte

	$filename='assitance/'.$period_year.'/'.$object->fk_member.'/rrhh';
	$filedir=$conf->assistance->dir_output.'/assitance/'.$period_year.'/'.$object->fk_member.'/rrhh';

	$resType=$objCtypelicence->fetchAll('','',0,0,array(1=>1),'AND',"AND t.code ='".$object->type_licence."'", true);
	if($resType > 0)
	{
				//echo "valor permiso: ".$objCtypelicence->type;
		if($objCtypelicence->type === 'V'){
			$modelpdf = 'vacacion';
		}
		if($objCtypelicence->type === 'L'){
			$modelpdf = 'licencia';
		}
	}
	else{
		setEventMessages("Error al genera los reporte de tipo",$resType->errors,'errors');
	}


		// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');


		//print_r($object);
	//$user->rights->assistance->lic->app.' && '.$object->statut.' == 1';
		//Aqui armamos el array para el reporte
		// Aqui sacamos el nombre
	$res = $adherent->fetch($object->fk_member);
	if ($res<=0)
		setEventMessages($langs->trans('No existe o falla'),null,'errors');

		//Aqui buscamos al user en departamentUser
	$resDeptUser = $objDeptUser->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_user ='.$object->fk_member, true);
	if ($resDeptUser<=0)
	{
		$error++;
		setEventMessages($langs->trans('No esta asignado a un Departamento'),null,'warnings');
	}
		//Aqui buscamos ala tabla departament y buscamos el nombre del departamento que pertenece
	else
	{
		$resDept = $objDepartament->fetch($objDeptUser->fk_departament);
		if ($resDept<=0)
		{
			$error++;
			setEventMessages($langs->trans('No existe o falla Dept'),null,'errors');
		}
	}
		//Array para el reporte
	$arrayReporte = array('fecha'=>$object->date_create,'nombre'=>$adherent->firstname.' '.$adherent->lastname,'area'=>' '.$objDepartament->ref.' - '.$objDepartament->label,'fechaini'=>$object->date_ini,'fechafin'=>$object->date_fin,'motivo'=>$object->detail,'fechaAut'=>$object->datea,'fechaDesde'=>$object->date_ini_ejec,'fechaHasta'=>$object->date_fin_ejec,'fechaReg'=>$object->dater,'fechaApro'=>$object->datea, 'halfdayfinname'=> $object->halfdayfinname,'halfdayininame'=>$object->halfdayininame,'lHour'=>$object->lHour,'days'=>$object->days,'typelicence'=>$typelicence);
	$_SESSION['arrayReporte'] = serialize($arrayReporte);
	$_SESSION['fk_member'] = serialize($object->fk_member);
	$_SESSION['ref'] = serialize($object->ref);


	if (empty($action) || $action=='view')
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'">'.$langs->trans("Return").'</a></div>'."\n";

		if (($user->rights->assistance->lic->crearall && ($object->statut == 0||$object->statut == 9)) || ($user->rights->assistance->lic->mod && ($object->statut == 0||$object->statut == 9) && ($user->fk_member == $object->fk_member || $user->admin)))
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}
		if (($user->rights->assistance->lic->crearall && ($object->statut == 0||$object->statut == 9)) || ($user->rights->assistance->lic->val && empty($error) && ($object->statut == 0||$object->statut == 9) && ($user->fk_member == $object->fk_member || $user->admin)))
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
		}
		if ($objCtypelicence->type == 'V' && $user->rights->assistance->vac->rev && $object->statut == 1)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=review">'.$langs->trans("Toreview").'</a></div>'."\n";
		}
		if ($user->rights->assistance->lic->app && $object->statut == 2)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approval">'.$langs->trans("Approve").'</a></div>'."\n";
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refuse">'.$langs->trans("Torefuse").'</a></div>'."\n";
		}

		if (($user->rights->assistance->lic->crearall && ($object->statut == 0||$object->statut == 9)) || ($user->rights->assistance->lic->mod && ($object->statut == 0||$object->statut == 9) && ($user->fk_member == $object->fk_member || $user->admin)))
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=anule">'.$langs->trans("Cancel").'</a></div>'."\n";
		}

		if ($object->statut == 2 && ($user->rights->assistance->lic->regini || $user->rights->assistance->lic->modsalida))
		{
			//include DOL_DOCUMENT_ROOT.'/assistance/tpl/procesoasistencia_rrhh.tpl.php';
		}
		if ($object->statut == 3 && ($user->rights->assistance->lic->regfin || $user->rights->assistance->lic->modretorno))
		{
			//include DOL_DOCUMENT_ROOT.'/assistance/tpl/procesoasistencia_rrhh.tpl.php';
		}
	}
	print '</div>'."\n";
}
print '<br>';

if ($object->statut >= 1)
{
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
										// Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';
								//echo '<hr>filedir '.$filedir;
	$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
	$genallowed=$user->rights->assistance->pdf->creardoc;
	$delallowed=$user->rights->assistance->pdf->deldoc;
								//echo("modelPDF : ".$modelpdf);
	print $formfile->showdocuments('assistance',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);


	print '</td></tr></table>';
	print '</div>'."\n";
	/*Hasta aqui el generador del reporte*/
}

// End of page
llxFooter();
$db->close();
