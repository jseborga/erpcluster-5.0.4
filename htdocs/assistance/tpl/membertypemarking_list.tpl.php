<?php
dol_include_once('/assistance/class/puser.class.php');
dol_include_once('/user/class/user.class.php');
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

$search_fk_user=GETPOST('search_fk_user','int');
$search_firstname=GETPOST('search_firstname','alpha');
$search_lastname=GETPOST('search_lastname','alpha');
$search_lastnametwo=GETPOST('search_lastnametwo','alpha');
$search_state_marital=GETPOST('search_state_marital','alpha');
$search_docum=GETPOST('search_docum','alpha');
$search_issued_in=GETPOST('search_issued_in','alpha');
$search_sex=GETPOST('search_sex','alpha');
$search_phone_emergency=GETPOST('search_phone_emergency','alpha');
$search_blood_type=GETPOST('search_blood_type','alpha');
$search_dependents=GETPOST('search_dependents','int');
$search_days_assigned=GETPOST('search_days_assigned','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
if (empty($page)) $page=0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.lastname"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assistancemembermarkinglist';

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

't.fk_user'=>array('label'=>$langs->trans("Fieldfk_user"), 'checked'=>0),
't.firstname'=>array('label'=>$langs->trans("Fieldfirstname"), 'checked'=>1),
't.lastname'=>array('label'=>$langs->trans("Fieldlastname"), 'checked'=>0),
't.lastnametwo'=>array('label'=>$langs->trans("Fieldlastnametwo"), 'checked'=>0),
't.state_marital'=>array('label'=>$langs->trans("Fieldstate_marital"), 'checked'=>0),
't.docum'=>array('label'=>$langs->trans("Fielddocum"), 'checked'=>0),
't.issued_in'=>array('label'=>$langs->trans("Fieldissued_in"), 'checked'=>0),
't.sex'=>array('label'=>$langs->trans("Fieldsex"), 'checked'=>0),
't.phone_emergency'=>array('label'=>$langs->trans("Fieldphone_emergency"), 'checked'=>0),
't.blood_type'=>array('label'=>$langs->trans("Fieldblood_type"), 'checked'=>0),
't.dependents'=>array('label'=>$langs->trans("Fielddependents"), 'checked'=>0),
't.days_assigned'=>array('label'=>$langs->trans("Fielddays_assigned"), 'checked'=>0),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>1),


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
//$object=new Puser($db);
//if (($id > 0 || ! empty($ref)) && $action != 'add')
//{
//	$result=$object->fetch($id,$ref);
//	if ($result < 0) dol_print_error($db);
//}
$objUser = new User($db);
//echo "Llega el ID : ".$id;


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
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{

$search_fk_user='';
$search_firstname='';
$search_lastname='';
$search_lastnametwo='';
$search_state_marital='';
$search_docum='';
$search_issued_in='';
$search_sex='';
$search_phone_emergency='';
$search_blood_type='';
$search_dependents='';
$search_days_assigned='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_status='';


		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

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
$title = $langs->trans('Listmember');

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

$type_marking =$object->ref;

$sql = "SELECT";
$sql.= " t.rowid,";

		$sql .= " t.fk_user,";
		$sql .= " t.firstname,";
		$sql .= " t.lastname,";
		$sql .= " t.lastnametwo,";
		$sql .= " t.state_marital,";
		$sql .= " t.docum,";
		$sql .= " t.issued_in,";
		$sql .= " t.sex,";
		$sql .= " t.phone_emergency,";
		$sql .= " t.blood_type,";
		$sql .= " t.dependents,";
		$sql .= " t.days_assigned,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."p_user as t";
$sql.= " , ".MAIN_DB_PREFIX."assistance_def as a";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1 AND t.fk_user = a.fk_reg AND a.type_marking = '".$type_marking."'";


if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_firstname) $sql.= natural_search("firstname",$search_firstname);
if ($search_lastname) $sql.= natural_search("lastname",$search_lastname);
if ($search_lastnametwo) $sql.= natural_search("lastnametwo",$search_lastnametwo);
if ($search_state_marital) $sql.= natural_search("state_marital",$search_state_marital);
if ($search_docum) $sql.= natural_search("docum",$search_docum);
if ($search_issued_in) $sql.= natural_search("issued_in",$search_issued_in);
if ($search_sex) $sql.= natural_search("sex",$search_sex);
if ($search_phone_emergency) $sql.= natural_search("phone_emergency",$search_phone_emergency);
if ($search_blood_type) $sql.= natural_search("blood_type",$search_blood_type);
if ($search_dependents) $sql.= natural_search("dependents",$search_dependents);
if ($search_days_assigned) $sql.= natural_search("days_assigned",$search_days_assigned);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_status) $sql.= natural_search("status",$search_status);


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
$sql.= " ORDER BY t.lastname, t.lastnametwo, t.firstname";
//$sql.=$db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit+1, $offset);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
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
	header("Location: ".DOL_URL_ROOT.'/puser/card.php?id='.$id);
	exit;
}

//llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='&id='.$id;
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
$params = $param;
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
print '<input type="hidden" name="ids" value="'.$id.'">';
print '<input type="hidden" name="actions" value="view">';


//aqui esta la numeracion
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
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.firstname']['checked'])) print_liste_field_titre($arrayfields['t.firstname']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lastname']['checked'])) print_liste_field_titre($arrayfields['t.lastname']['label'],$_SERVER['PHP_SELF'],'t.lastname','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lastnametwo']['checked'])) print_liste_field_titre($arrayfields['t.lastnametwo']['label'],$_SERVER['PHP_SELF'],'t.lastnametwo','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.state_marital']['checked'])) print_liste_field_titre($arrayfields['t.state_marital']['label'],$_SERVER['PHP_SELF'],'t.state_marital','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.docum']['checked'])) print_liste_field_titre($arrayfields['t.docum']['label'],$_SERVER['PHP_SELF'],'t.docum','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.issued_in']['checked'])) print_liste_field_titre($arrayfields['t.issued_in']['label'],$_SERVER['PHP_SELF'],'t.issued_in','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sex']['checked'])) print_liste_field_titre($arrayfields['t.sex']['label'],$_SERVER['PHP_SELF'],'t.sex','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.phone_emergency']['checked'])) print_liste_field_titre($arrayfields['t.phone_emergency']['label'],$_SERVER['PHP_SELF'],'t.phone_emergency','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.blood_type']['checked'])) print_liste_field_titre($arrayfields['t.blood_type']['label'],$_SERVER['PHP_SELF'],'t.blood_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.dependents']['checked'])) print_liste_field_titre($arrayfields['t.dependents']['label'],$_SERVER['PHP_SELF'],'t.dependents','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.days_assigned']['checked'])) print_liste_field_titre($arrayfields['t.days_assigned']['label'],$_SERVER['PHP_SELF'],'t.days_assigned','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.sta']['label'],$_SERVER['PHP_SELF'],'t.','',$params,'',$sortfield,$sortorder);

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
			//print_liste_field_titre($extralabels[$key],$_SERVER["PHP_SELF"],"ef.".$key,"",$param,($align?'align="'.$align.'"':''),$sortfield,$sortorder);
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
//print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

// Fields title search
/*print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
if (! empty($arrayfields['t.firstname']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_firstname" value="'.$search_firstname.'" size="10"></td>';
if (! empty($arrayfields['t.lastname']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lastname" value="'.$search_lastname.'" size="10"></td>';
if (! empty($arrayfields['t.lastnametwo']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lastnametwo" value="'.$search_lastnametwo.'" size="10"></td>';
if (! empty($arrayfields['t.state_marital']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_state_marital" value="'.$search_state_marital.'" size="10"></td>';
if (! empty($arrayfields['t.docum']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_docum" value="'.$search_docum.'" size="10"></td>';
if (! empty($arrayfields['t.issued_in']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_issued_in" value="'.$search_issued_in.'" size="10"></td>';
if (! empty($arrayfields['t.sex']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sex" value="'.$search_sex.'" size="10"></td>';
if (! empty($arrayfields['t.phone_emergency']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_phone_emergency" value="'.$search_phone_emergency.'" size="10"></td>';
if (! empty($arrayfields['t.blood_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_blood_type" value="'.$search_blood_type.'" size="10"></td>';
if (! empty($arrayfields['t.dependents']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_dependents" value="'.$search_dependents.'" size="10"></td>';
if (! empty($arrayfields['t.days_assigned']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_days_assigned" value="'.$search_days_assigned.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';

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
/*print '<td class="liste_titre" align="right">';
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 0);
print $searchpitco;
print '</td>';
print '</tr>'."\n";*/

$i=0;
$var=true;
$totalarray=array();
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;

		// Show here line of result
		print '<tr '.$bc[$var].'>';

		$objAdherent->fetch($obj->fk_user);
		print '<td>';
		print $objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';

		$object->status = $obj->status;
		print '<td>'.$object->getLibStatut(3).'</td>';

		// LIST_OF_TD_FIELDS_LIST
		/*foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$object->id = $obj->rowid;
					$object->ref = $obj->ref;
					$object->label = $obj->label;
					$obj->$key2 = $object->getNomUrl();
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}

				//print '<td>' . $obj->$key2 . '</td>';
				if (!$i)
					$totalarray['nbfield'] ++;
			}
		}*/

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
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
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




// End of page
//llxFooter();
//$db->close();
