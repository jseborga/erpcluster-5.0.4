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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
dol_include_once('/adherents/class/adherent.class.php');
dol_include_once('/adherents/class/adherent_type.class.php');
//dol_include_once('/assistance/class/assistancedef.class.php');
//dol_include_once('/assistance/class/typemarking.class.php');
//dol_include_once('/assistance/class/membercas.class.php');

/*ABC*/

//dol_include_once('/eva/class/cregions.class.php');
//dol_include_once('eva/class/subsidiary.class.php');
//dol_include_once('/eva/class/cdepartements.class.php');
//dol_include_once('/eva/class/eva.class.php');
dol_include_once('/assistance/class/puser.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
//dol_include_once('/eva/class/beneficiaries.class.php');
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

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'evalist';

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

// Load object if id or ref is provided as parameter
$object=new Adherent($db);
//$objMembercas=new Membercas($db);
//$objEva = new Eva($db);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

$objLicence = new Licencesext($db);
$objecttype=new AdherentType($db);
$extrafields = new ExtraFields($db);

/*teil*/
//$objDepartamento = new Cregions($db);
//$objProvMuni = new Cdepartements($db);
$aMesesLabel = array(1=>"ENERO",2=>"FEBRERO",3=>"MARZO",4=>"ABRIL",5=>"MAYO",6=>"JUNIO",7=>"JULIO",8=>"AGOSTO",9=>"SEPTIEMBRE",10=>"OCTUBRE",11=>"NOVIEMBRE",12=>"DICIEMBRE");
//$objSubsidiario = new Subsidiary($db);
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

}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Licence'),'');

$form=new Form($db);
$formother = new FormOther($db);


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
	if (! empty($conf->notification->enabled))
		$langs->load("mails");

	$head = member_prepare_head($object);

	$form=new Form($db);

	dol_fiche_head($head, 'licence', $langs->trans("Member"),0,'user');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/adherents/list.php">'.$langs->trans("BackToList").'</a>';

		// Ref
	print '<tr><td width="20%">'.$langs->trans("Ref").'</td>';
	print '<td class="valeur">';
	print $form->showrefnav($object, 'rowid', $linkback);
	print '</td></tr>';

		// Login
	if (empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED))
	{
		print '<tr><td>'.$langs->trans("Login").' / '.$langs->trans("Id").'</td><td class="valeur">'.$object->login.'&nbsp;</td></tr>';
	}

		// Morphy
	print '<tr><td>'.$langs->trans("Nature").'</td><td class="valeur" >'.$object->getmorphylib().'</td>';
	print '</tr>';

		// Type
	print '<tr><td>'.$langs->trans("Type").'</td><td class="valeur">'.$objecttype->getNomUrl(1)."</td></tr>\n";

		// Company
	print '<tr><td>'.$langs->trans("Company").'</td><td class="valeur">'.$object->societe.'</td></tr>';

		// Civility
	print '<tr><td>'.$langs->trans("UserTitle").'</td><td class="valeur">'.$object->getCivilityLabel().'&nbsp;</td>';
	print '</tr>';

		// Lastname
	print '<tr><td>'.$langs->trans("Lastname").'</td><td class="valeur">'.$object->lastname.'&nbsp;</td>';
	print '</tr>';

		// Firstname
	print '<tr><td>'.$langs->trans("Firstname").'</td><td class="valeur">'.$object->firstname.'&nbsp;</td>';
	print '</tr>';

		// Status
	print '<tr><td>'.$langs->trans("Status").'</td><td class="valeur">'.$object->getLibStatut(4).'</td></tr>';

	print '</table>';
	dol_fiche_end();
	$licvac=1;
	include DOL_DOCUMENT_ROOT.'/assistance/tpl/licence_list.tpl.php';
}


// End of page
llxFooter();
$db->close();
