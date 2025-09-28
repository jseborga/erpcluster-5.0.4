<?php
/* Copyrigth  2017
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
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
dol_include_once('/adherents/class/adherent.class.php');
dol_include_once('/adherents/class/adherent_type.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/membercas.class.php');

include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
/*ABC*/
dol_include_once('/eva/class/eva.class.php');
dol_include_once('/eva/class/evadet.class.php');
dol_include_once('/eva/class/cregions.class.php');
dol_include_once('eva/class/subsidiary.class.php');
dol_include_once('/eva/class/cdepartements.class.php');
dol_include_once('/eva/class/eva.class.php');
dol_include_once('/assistance/class/puser.class.php');
dol_include_once('/eva/class/beneficiaries.class.php');

dol_include_once('/orgman/class/pdepartamentext.class.php');
dol_include_once('/orgman/class/pdepartamentuserext.class.php');
dol_include_once('/orgman/class/pdepartamentuserlog.class.php');

dol_include_once('/orgman/lib/departament.lib.php');



//dol_include_once('assistance/class/adherentext.class.php');

// Load traductions files requiredby by page
$langs->load("assistance");
$langs->load("companies");
$langs->load("other");
$langs->load("members");

// Get parameters
$idd = GETPOST('idd','int');
$id		= GETPOST('rowid','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel 	= GETPOST('cancel','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$date  = dol_mktime(12, 0, 0, GETPOST('dr_month'), GETPOST('dr_day'), GETPOST('dr_year'));


$fk_father = GETPOST('fk_father');
$view      = GETPOST('view');

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'evalist';

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

// Load object if id or ref is provided as parameter
$object=new Adherent($db);
$objMembercas=new Membercas($db);
//$objEva = new Eva($db);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals


$objTypemarking = new Typemarking($db);
$objAdherent=new Adherent($db);
$objAdherenttype=new AdherentType($db);
$extrafields = new ExtraFields($db);


/* Objectos Ddepartament*/

$objDepar     = new Pdepartamentext ($db);
$objDeparUser = new Pdepartamentuserext($db);
$objDeparUserLog = new Pdepartamentuserlog($db);

/*teil*/
//$objDepartamento = new Cregions($db);
//$objProvMuni = new Cdepartements($db);
$aMesesLabel = array(1=>"ENERO",2=>"FEBRERO",3=>"MARZO",4=>"ABRIL",5=>"MAYO",6=>"JUNIO",7=>"JULIO",8=>"AGOSTO",9=>"SEPTIEMBRE",10=>"OCTUBRE",11=>"NOVIEMBRE",12=>"DICIEMBRE");
//$objSubsidiario = new Subsidiary($db);
//$objEva = new Eva($db);
//$objEvadet = new Evadet($db);
//$objBeneficiario = new Beneficiaries($db);
$objPuser = new Puser($db);
/**/

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objMembercas->table_element);


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('membercas'));

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistancedef'));


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objMembercas,$action);
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
$now = dol_now();
if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/eva.php?rowid='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objMembercas->fetch($id,$ref);
		$action='';
	}

	if($action == 'consult'){
		$cMoth = GETPOST('number_month');
		$cYear = GETPOST('number_year');
		//echo "Mes y Ano ".$cMoth ."/".$cYear;

		$action = 'listView';

	}

	if($action == 'update')
	{
		$error=0;
		$db->begin();
		$fk_departament     = GETPOST("fk_departament");
		$fk_departament_ant     = GETPOST("fk_departament_ant");
		$id_Update = GETPOST("id_Update");
		if ($fk_departament != $fk_departament_ant)
		{
			$objDeparUserLog->fk_departament_user = $fk_departament_ant;
			if (empty($objDeparUserLog->fk_departament_user)) $objDeparUserLog->fk_departament_user = 0;
			$objDeparUserLog->fk_member           = $id;
			$objDeparUserLog->description         = $langs->trans('Newassignment');
			$objDeparUserLog->fk_user_create      = $user->id;
			$objDeparUserLog->fk_user_mod         = $user->id;
			$objDeparUserLog->datec               = $now;
			$objDeparUserLog->datem               = $now;
			$objDeparUserLog->tms                 = $now;
			$objDeparUserLog->status              = 1;

			$result=$objDeparUserLog->create($user);

			if ($result > 0)
			{
				// Creation OK
				if ($id_Update>0)
				{
					$objDeparUser->fetch($id_Update);
					$objDeparUser->fk_departament = $fk_departament;
					$resultU = $objDeparUser->updateUserDep($user);
					if ($resultU<=0)
					{
						// Creation KO
						$error++;
						if (! empty($objDeparUser->errors)) setEventMessages(null, $objDeparUser->errors, 'errors');
						else setEventMessages($objDeparUser->error, null, 'errors');
					}
				}
				else
				{
					//vamos a crear la asignación en el departamento
					$objDeparUser->fk_departament = $fk_departament;
					$objDeparUser->fk_user = $id;
					$objDeparUser->fk_user_create = $user->id;
					$objDeparUser->fk_user_mod = $user->id;
					$objDeparUser->datec = $now;
					$objDeparUser->datem = $now;
					$objDeparUser->tms = $now;
					$objDeparUser->active = 1;
					$objDeparUser->privilege = 0;
					$res = $objDeparUser->create($user);
					if ($res<=0)
					{
						$error++;
						setEventMessages($objDeparUser->error,$objDeparUser->errors,'errors');
					}

				}

			}else{
				// Creation KO
				$error++;
				if (! empty($objDeparUserLog->errors)) setEventMessages(null, $objDeparUserLog->errors, 'errors');
				else  setEventMessages($objDeparUserLog->error, null, 'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/departament/departament.php?rowid='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'edit';
		}
	}
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Departament'),'');

//$form=new Form($db);
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


//mostramos al miembro
if ($id>0)
{
	$objAdherent->fetch($id);
	$result=$objAdherenttype->fetch($objAdherent->typeid);
	if ($result > 0)
	{
	/*
	 * Affichage onglets
	 */
	if (! empty($conf->notification->enabled))
		$langs->load("mails");

	$head = member_prepare_head($objAdherent);

	//$form=new Formv($db);

	dol_fiche_head($head, 'departament', $langs->trans("Member"),0,'user');

	$rDU = $objDeparUser->fetchALl("","",0,0,array(1=>1),"AND","AND t.fk_user = ".$id,true);

	if($rDU == 1){
		$fk_departament_ant   = $objDeparUser->fk_departament;
		$id_Update = $objDeparUser->id;
		$rD        = $objDepar->fetch($objDeparUser->fk_departament);

		if($rD == 1){
			$lblDep = $objDepar->label;
		}else{
			$lblDep = "No existe el departamento&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		}
	}else{
		$lblDep = "No Asignado a un departamento&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
	}

	/***********************************************/
	echo "<pre>";

	//$objobj = $objDepar->getlist_son($objDeparUser->fk_departament);
	//$objobj = $objDepar->liste_son($objDeparUser->fk_departament);
	$objobj = $objDepar->obtenerHijos($objDeparUser->fk_departament);

	//var_dump($objobj);
	//foreach ($objobj as $key => $value) {
	//		print $value->Pdepartament;

	//}

	echo "</pre>";
	/***********************************************/




	if ($action == 'edit')
	{

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="rowid" value="'.$id.'">';
		print '<input type="hidden" name="fk_departament_ant" value="'.$fk_departament_ant.'">';
		print '<input type="hidden" name="id_Update" value="'.$id_Update.'">';

	}
	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/adherents/list.php">'.$langs->trans("BackToList").'</a>';

		// Ref
	print '<tr><td width="20%">'.$langs->trans("Ref").'</td>';
	print '<td class="valeur">';
	print $form->showrefnav($objAdherent, 'rowid', $linkback);
	print '</td></tr>';

		// Login
	if (empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED))
	{
		print '<tr><td>'.$langs->trans("Login").' / '.$langs->trans("Id").'</td><td class="valeur">'.$objAdherent->login.'&nbsp;</td></tr>';
	}

		// Morphy
	print '<tr><td>'.$langs->trans("Nature").'</td><td class="valeur" >'.$objAdherent->getmorphylib().'</td>';
		/*print '<td rowspan="'.$rowspan.'" align="center" valign="middle" width="25%">';
	 print $form->showphoto('memberphoto',$object);
	 print '</td>';*/
	 print '</tr>';

		// Type
	 print '<tr><td>'.$langs->trans("Type").'</td><td class="valeur">'.$objAdherenttype->getNomUrl(1)."</td></tr>\n";

		// Company
	 print '<tr><td>'.$langs->trans("Company").'</td><td class="valeur">'.$objAdherent->societe.'</td></tr>';

		// Civility
	 print '<tr><td>'.$langs->trans("UserTitle").'</td><td class="valeur">'.$objAdherent->getCivilityLabel().'&nbsp;</td>';
	 print '</tr>';

		// Lastname
	 print '<tr><td>'.$langs->trans("Lastname").'</td><td class="valeur">'.$objAdherent->lastname.'&nbsp;</td>';
	 print '</tr>';

		// Firstname
	 print '<tr><td>'.$langs->trans("Firstname").'</td><td class="valeur">'.$objAdherent->firstname.'&nbsp;</td>';
	 print '</tr>';

	 $linkC = '<a href="'.DOL_URL_ROOT.'/orgman/departament/departament.php?rowid='.$id.'&action=edit">'.img_picto($langs->trans("Change"),'edit').'</a>';
	 $linkU = '<a href="'.DOL_URL_ROOT.'/orgman/departament/departament.php?rowid='.$id.'&view=U">'.$langs->trans("Save").'</a>';
	 	// Assing Departament
	 print '<tr><td>'.$langs->trans("Assigneddepartament").'</td>';

	 if($action == "edit"){
	 	print '<td class="valeur">';
	 	print $form-> select_departament($fk_departament_ant,'fk_departament','',0,0,'',0);
	 	print '<input type="submit" class="butAction" name="Guardar" value="Guardar" ></td>';

	 }else{
	 	print '<td class="valeur">'.$lblDep.'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp;';
	 	if ($user->rights->orgman->dpto->write) print $linkC;
	 	print '</td>';
	 }

	 print '</tr>';

		// Status
	 print '<tr><td>'.$langs->trans("Status").'</td><td class="valeur">'.$objAdherent->getLibStatut(1).'</td></tr>';

	 print '</table>';
	 dol_fiche_end();

	 if ($action == 'edit')
	 	print '</form>';

	}

	//revisamos por el id member
	if ($id>0)
	{
		//$result=$objMembercas->fetch(0,$id);
		if ($result < 0)
		{
			dol_print_error($db);
		}
		else
		{
			if ($result == 0) $action = 'listView';//create
			else $idd = $objMembercas->id;
		}
	}

	if (empty($action) && empty($idd)) $action='listView';//create

	if($action != 'edit'){
		include DOL_DOCUMENT_ROOT.'/orgman/departament/tpl/pdepartamentuserlog_list.tpl.php';
	}
}


// End of page
llxFooter();
$db->close();

?>