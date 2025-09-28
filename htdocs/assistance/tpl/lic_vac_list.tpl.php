<?php
/* Copyright (C) 2017  L. Mendoza Ticona  <l.mendoza.liet@gmail.com>
 *  Desarrollador PHP , Java
 * Descripcion : La presente clase es la continuacion de la clase report_lic_vac.php
 * aca se mostrara los resultados que se capturaran de la vista principal
 *          Nota:  un tpl = es una plantilla (no se por que lo manejaran asi)
 */

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
$search_ref=GETPOST('search_ref','alpha');
$search_fk_member=GETPOST('search_fk_member','int');
$search_type_licence=GETPOST('search_type_licence','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_halfday=GETPOST('search_halfday','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_user_aprob=GETPOST('search_fk_user_aprob','int');
$search_fk_user_reg=GETPOST('search_fk_user_reg','int');
$search_statut=GETPOST('search_statut','int');
$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');
//Cargando mis datos de la consulta
//$date_b   = GETPOST('period_year');
$licvac = $type;
$fk_member = GETPOST('fk_member');

$aSesion=array("date_a"=>$date_a,"date_b"=>$date_b,"anio"=>$date_b,"licvac"=>$licvac,"fk_member"=>$fk_member);

$_SESSION['aSesion'] = $aSesion;

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assistancereportlicvaclist';

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
if($licvac == 1){
	$arrayfields=array(

		't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
		't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
		'p.docum'=>array('label'=>$langs->trans("CI"), 'checked'=>1),
		't.fk_member'=>array('label'=>$langs->trans("Fieldfk_member"), 'checked'=>1),
		't.type_licence'=>array('label'=>$langs->trans("Fieldtype_licence"), 'checked'=>0),

		't.date_ini'=>array('label'=>$langs->trans("Fielddate_ini"), 'checked'=>1),
		't.date_fin'=>array('label'=>$langs->trans("Fielddate_fin"), 'checked'=>1),
		't.date_ini_ejec'=>array('label'=>$langs->trans("Fielddate_ini_ejec"), 'checked'=>1),
		't.date_fin_ejec'=>array('label'=>$langs->trans("Fielddate_fin_ejec"), 'checked'=>1),

	//'t.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
		't.halfday'=>array('label'=>$langs->trans("Fieldhalfday"), 'checked'=>0),
		't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
		't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
		't.fk_user_aprob'=>array('label'=>$langs->trans("Fieldfk_user_aprob"), 'checked'=>0),
		't.fk_user_reg'=>array('label'=>$langs->trans("Fieldfk_user_reg"), 'checked'=>0),
		't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),


		//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
		't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
		't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
		//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
	);
}elseif($licvac == 2 ){
	if($fk_member > -1){
		$arrayfields=array(

			't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
			't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
			'p.docum'=>array('label'=>$langs->trans("CI"), 'checked'=>1),
			't.fk_member'=>array('label'=>$langs->trans("Fieldfk_member"), 'checked'=>1),
			't.type_licence'=>array('label'=>$langs->trans("Fieldtype_licence"), 'checked'=>0),

			't.date_ini'=>array('label'=>$langs->trans("Fecha de Ingreso"), 'checked'=>1),

			't.date_fin'=>array('label'=>$langs->trans("Gestion"), 'checked'=>1),
			't.date_ini_ejec'=>array('label'=>$langs->trans("Totaldaysallocated"), 'checked'=>1),
			't.date_fin_ejec'=>array('label'=>$langs->trans("Totaldaysused"), 'checked'=>1),

		//'t.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
			't.halfday'=>array('label'=>$langs->trans("Saldo"), 'checked'=>1),
			't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
			't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
			't.fk_user_aprob'=>array('label'=>$langs->trans("Fieldfk_user_aprob"), 'checked'=>0),
			't.fk_user_reg'=>array('label'=>$langs->trans("Fieldfk_user_reg"), 'checked'=>0),
			't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),


			//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
			't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
			't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
			//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
		);
	}else{
		$arrayfields=array(

			't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
			't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
			'p.docum'=>array('label'=>$langs->trans("CI"), 'checked'=>1),
			't.fk_member'=>array('label'=>$langs->trans("Fieldfk_member"), 'checked'=>1),
			't.type_licence'=>array('label'=>$langs->trans("Fieldtype_licence"), 'checked'=>0),

			't.date_ini'=>array('label'=>$langs->trans("Fecha Solicitud"), 'checked'=>1),

			't.date_fin'=>array('label'=>$langs->trans("Dias Solicitados"), 'checked'=>1),
			't.date_ini_ejec'=>array('label'=>$langs->trans("Totaldaysallocated"), 'checked'=>1),
			't.date_fin_ejec'=>array('label'=>$langs->trans("Totaldaysused"), 'checked'=>1),

	//'t.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
			't.halfday'=>array('label'=>$langs->trans("Saldo"), 'checked'=>1),
			't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
			't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
			't.fk_user_aprob'=>array('label'=>$langs->trans("Fieldfk_user_aprob"), 'checked'=>0),
			't.fk_user_reg'=>array('label'=>$langs->trans("Fieldfk_user_reg"), 'checked'=>0),
			't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),


		//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
			't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
			't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
		//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
		);
	}

}

// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	}
}

// Load object if id or ref is provided as parameter
//$object=new Licences($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
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
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{

		$search_entity='';
		$search_ref='';
		$search_fk_member='';
		$search_type_licence='';
		$search_detail='';
		$search_halfday='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_fk_user_aprob='';
		$search_fk_user_reg='';
		$search_statut='';


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
if($licvac == 1){
	$title = $langs->trans('Listoflicences');
}else{
	$title = $langs->trans('Holidaylist');
}



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
$sql .= " t.ref,";
$sql .= " t.fk_member,";
$sql .= " t.date_ini,";
$sql .= " t.date_fin,";
$sql .= " t.date_ini_ejec,";
$sql .= " t.date_fin_ejec,";
$sql .= " t.type_licence,";
$sql .= " t.detail,";
$sql .= " t.halfday,";
$sql .= " t.date_create,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.fk_user_aprob,";
$sql .= " t.fk_user_reg,";
$sql .= " t.datem,";
$sql .= " t.datea,";
$sql .= " t.dater,";
$sql .= " t.tms,";
$sql .= " t.statut";
$sql.= " , a.lastname AS adh_lastname, a.firstname AS adh_firstname";
$sql.= " , p.lastname, p.lastnametwo, p.firstname, p.docum ";

// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."licences as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as a ON t.fk_member = a.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user as p ON p.fk_user = a.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_type_licence as tl ON tl.code = t.type_licence AND tl.entity = t.entity ";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."licences_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

// Preguntamos si hay miembro
if($fk_member != -1){
	$sql .= " AND t.fk_member = ".$fk_member;
}

//Verificamos si quiere Licencias o Vacaciones
if($licvac == 1){
	$sql .= " AND tl.type = 'L'";
}elseif($licvac == 2){
	$sql .= " AND tl.type = 'V'";
}

$sql.= " AND t.date_ini BETWEEN ".$db->idate($date_ini) ." AND ".$db->idate($date_fin);

//echo $sql;
if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_member) $sql.= natural_search("fk_member",$search_fk_member);

if ($search_date_ini) $sql.= natural_search("date_ini",$search_date_ini);
if ($search_date_fin) $sql.= natural_search("date_fin",$search_date_fin);
if ($search_date_ini_ejec) $sql.= natural_search("date_ini_ejec",$search_date_ini_ejec);
if ($search_date_fin_ejec) $sql.= natural_search("date_fin_ejec",$search_date_fin_ejec);


if ($search_type_licence) $sql.= natural_search("type_licence",$search_type_licence);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_halfday) $sql.= natural_search("halfday",$search_halfday);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_fk_user_aprob) $sql.= natural_search("fk_user_aprob",$search_fk_user_aprob);
if ($search_fk_user_reg) $sql.= natural_search("fk_user_reg",$search_fk_user_reg);
if ($search_statut) $sql.= natural_search("statut",$search_statut);


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
//echo $sql;
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
	header("Location: ".DOL_URL_ROOT.'/licences/card.php?id='.$id);
	exit;
}

//llxHeader('', $title, $help_url);

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
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['p.docum']['checked'])) print_liste_field_titre($arrayfields['p.docum']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_member']['checked'])) print_liste_field_titre($arrayfields['t.fk_member']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);

if (! empty($arrayfields['t.date_ini']['checked'])) print_liste_field_titre($arrayfields['t.date_ini']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_fin']['checked'])) print_liste_field_titre($arrayfields['t.date_fin']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_ini_ejec']['checked'])) print_liste_field_titre($arrayfields['t.date_ini_ejec']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_fin_ejec']['checked'])) print_liste_field_titre($arrayfields['t.date_fin_ejec']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);


if (! empty($arrayfields['t.type_licence']['checked'])) print_liste_field_titre($arrayfields['t.type_licence']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.halfday']['checked'])) print_liste_field_titre($arrayfields['t.halfday']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_aprob']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_aprob']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_reg']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_reg']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);

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
/*print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['t.fk_member']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_member" value="'.$search_fk_member.'" size="10"></td>';

if (! empty($arrayfields['t.date_ini']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_date_ini" value="'.$search_date_ini.'" size="10"></td>';
if (! empty($arrayfields['t.date_fin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_date_fin" value="'.$search_date_fin.'" size="10"></td>';
if (! empty($arrayfields['t.date_ini_ejec']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_date_ini_ejec" value="'.$search_date_ini_ejec.'" size="10"></td>';
if (! empty($arrayfields['t.date_fin_ejec']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_date_fin_ejec" value="'.$search_date_fin_ejec.'" size="10"></td>';


if (! empty($arrayfields['t.type_licence']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_licence" value="'.$search_type_licence.'" size="10"></td>';
//if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['t.halfday']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_halfday" value="'.$search_halfday.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_aprob']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_aprob" value="'.$search_fk_user_aprob.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_reg']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_reg" value="'.$search_fk_user_reg.'" size="10"></td>';
if (! empty($arrayfields['t.statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut" value="'.$search_statut.'" size="10"></td>';

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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
print $searchpitco;
print '</td>';*/
print '</tr>'."\n";

$i=0;
$var=true;
// Array que almacenaran los datos para el reporte de Licencias y Vacaciones
$aLicencias = array();
$aVacacion = array();

while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;

		$object->id = $obj->rowid;
		$object->ref = $obj->ref;

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		/* Declaracion de variables necesarias*/
		$idMember = $obj->fk_member;

		// LIST_OF_TD_FIELDS_LIST
		if (! empty($arrayfields['t.entity']['checked']))
		{
			print '<td>'.$obj->entity.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.ref']['checked']))
		{
			print '<td>'.$object->getNomUrl().'</td>';
			$aLicencias[$i]['ref']=$obj->ref;
			$aVacacion[$i]['ref']=$obj->ref;
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['p.docum']['checked']))
		{
			print '<td>'.$obj->docum.'</td>';
			$aLicencias[$i]['ci']=$obj->docum;
			$aVacacion[$i]['ci']=$obj->docum;
			if (! $i) $totalarray['nbfield']++;

		}
		//Nombre Completo
		if (! empty($arrayfields['t.fk_member']['checked']))
		{
			$msgs = $obj->lastname.' '.$obj->lastnametwo.' '.$obj->firstname.'</br>';
			print '<td>'.$msgs.'</td>';
			$aLicencias[$i]['paterno']=$obj->lastname;
			$aLicencias[$i]['materno']=$obj->lastnametwo;
			$aLicencias[$i]['nombre']=$obj->firstname;

			$aVacacion[$i]['paterno']=$obj->lastname;
			$aVacacion[$i]['materno']=$obj->lastnametwo;
			$aVacacion[$i]['nombre']=$obj->firstname;
			$nomMember = $obj->firstname." ".$obj->lastnametwo." ".$obj->lastname;
			if (! $i) $totalarray['nbfield']++;
		}
		//fechas (dateini, ingreso)
		if (! empty($arrayfields['t.date_ini']['checked']))
		{
			if($licvac == 2){
				//Fecha de Ingreso
				$rc = $objContrato->fetchAll("","",0,0,array(1=>1),"AND","AND t.fk_user = ".$idMember." AND t.state = 1",true);
				if($fk_member!= (-1)){
					if($rc > 0){
						//$date_soli = dol_print_date($objContrato->date_ini,'day');
						print '<td>'.dol_print_date($objContrato->date_ini,'daytext').'</td>';
						$aVacacion[$i]['campoUno']=dol_print_date($objContrato->date_ini,'daytext');
					}else{
						print '<td> Sin Contrato </td>';
						$aVacacion[$i]['campoUno']="Sin Contrato";
					}
				}else{
					$date_soli = dol_print_date($objContrato->date_ini,'dayhour');
					print '<td>'.dol_print_date($db->jdate($obj->date_create),'day').'</td>';
					//$aVacacion[$i]['campoUno']=dol_print_date($obj->date_create,'day');
					$aVacacion[$i]['campoUno']=dol_print_date($db->jdate($obj->date_create),'day');
				}
			}else{
				print '<td>'.dol_print_date($db->jdate($obj->date_ini),'dayhour').'</td>';
				$date_soli = dol_print_date($db->jdate($obj->date_ini),'day');
				$aLicencias[$i]['date_ini']=dol_print_date($db->jdate($obj->date_ini),'dayhour');
			}

			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.date_fin']['checked']))
		{
			// Lic Dias pedidos
			if($licvac == 2){
			   //Gestion
				if($fk_member!= (-1)){
					$rg = $objMemberVacation->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_member = '.$idMember,true);
					if($rg > 0){
						print '<td>'.$objMemberVacation->period_year.'</td>';
						$aVacacion[$i]['campoDos']=$objMemberVacation->period_year;
					}else{
						print '<td> Sin Gestion </td>';
						$aVacacion[$i]['campoDos']="Sin Gestion";
					}
				}else{
					$obj->date_ini_gmt = $db->jdate($obj->date_ini,1);
					$obj->date_fin_gmt = $db->jdate($obj->date_fin,1);
					$days = num_open_day_fractal($obj->date_ini_gmt, $obj->date_fin_gmt, 0, 1, $obj->halfday);
					$aVacacion[$i]['campoDos']= $days;
					print '<td>'.$days.'</td>';
				}
			}else{
				print '<td>'.dol_print_date($db->jdate($obj->date_fin),'dayhour').'</td>';
				$aLicencias[$i]['date_fin']=dol_print_date($db->jdate($obj->date_fin),'dayhour');
			}

			if (! $i) $totalarray['nbfield']++;
		}
		//Lic date_ini_ejec
		if (! empty($arrayfields['t.date_ini_ejec']['checked']))
		{
			if($licvac == 2){
				$r = $objMemberVacation->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_member = '.$idMember,true);
				if($r > 0){
					$diasAsig = $objMemberVacation->days_assigned;
					print '<td>'.$diasAsig.'</td>';
					$aVacacion[$i]['diasAsig']= $diasAsig;
				}else{
					print '<td>No Asignado dias de Vacaciones</td>';
					$aVacacion[$i]['diasAsig']= "0";
				}
			}else{
				print '<td>'.dol_print_date($db->jdate($obj->date_ini_ejec),'dayhour').'</td>';
				$aLicencias[$i]['date_ini_ejec'] = dol_print_date($db->jdate($obj->date_ini_ejec),'dayhour');
			}

			if (! $i) $totalarray['nbfield']++;
		}
		//Lic date_fin_ejec
		if (! empty($arrayfields['t.date_fin_ejec']['checked']))
		{
			if($licvac == 2)
			{
				//Dias Utilizados
				$r = $objMemberVacation->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_member = '.$idMember,true);
				if($r > 0)
				{
					$rvd = $objMemberVacDet->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_member_vacation = '.$objMemberVacation->id,true);
					if($rvd > 0)
					{
						//echo "Entra a la vac Det";
						$diasUsa = $objMemberVacDet->day_used;
						//echo "-> ".$diasUsa;
						if(!empty($diasUsa)){
							print '<td>'.$diasUsa.'</td>';
							$aVacacion[$i]['diasUtil']= $diasUsa;
						}else{
							print '<td>0</td>';
							$aVacacion[$i]['diasUtil']= 0;
						}

					}
					else
					{
						print '<td>No se encontro ID</td>';
						//$aVacacion[$i]['diasUtil']= "0";
					}

				}else{
					print '<td>No asignado</td>';
					$aVacacion[$i]['diasUtil']= 0;
				}
			}else{
				print '<td>'.dol_print_date($db->jdate($obj->date_fin_ejec),'dayhour').'</td>';
				$aLicencias[$i]['date_fin_ejec'] = dol_print_date($db->jdate($obj->date_fin_ejec),'dayhour');
			}
		}
		if (! empty($arrayfields['t.type_licence']['checked']))
		{
			print '<td>'.$obj->type_licence.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		/*if (! empty($arrayfields['t.detail']['checked']))
		{
			print '<td>'.$obj->detail.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}*/
		if (! empty($arrayfields['t.halfday']['checked']))
		{
			if($licvac == 2){
				print '<td>'.($diasAsig - $diasUsa).'</td>';
				$aVacacion[$i]['saldo']= ($diasAsig - $diasUsa);
				$diasAsig = 0;
				$diasUsa = 0;
			}else{
				print '<td>'.$obj->halfday.'</td>';
			}

			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.date_create']['checked']))
		{
			print '<td>'.$obj->date_create.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_user_create']['checked']))
		{
			print '<td>'.$obj->fk_user_create.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_user_mod']['checked']))
		{
			print '<td>'.$obj->fk_user_mod.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_user_aprob']['checked']))
		{
			print '<td>'.$obj->fk_user_aprob.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_user_reg']['checked']))
		{
			print '<td>'.$obj->fk_user_reg.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.datem']['checked']))
		{
			print '<td>'.$obj->datem.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.datea']['checked']))
		{
			print '<td>'.$obj->datea.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.dater']['checked']))
		{
			print '<td>'.$obj->dater.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.tms']['checked']))
		{
			print '<td>'.$obj->tms.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.statut']['checked']))
		{
			$object->statut = $obj->statut;
			print '<td>'.$object->getLibStatut(3).'</td>';
			$aLicencias[$i]['estado']=$object->getLibStatut(0);
			$aVacacion[$i]['estado']=$object->getLibStatut(0);

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
			/*$selected=0;
			if (in_array($obj->rowid, $arrayofselected)) $selected=1;
			print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
		*/}
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

/*Mis pruebas de mis array*/
/*echo ("ARRAY LICENCIAS <br>");
echo "<pre>";
var_dump($aLicencias);
echo "</pre>";
echo ("ARRAY VACACIONES <br>");
echo "<pre>";
var_dump($aVacacion);
echo "</pre>";*/

//para generar reporte automaticamente

$filename='licvac/'.$period_year.'/report';
$filedir=$conf->assistance->dir_output.'/licvac/'.$period_year.'/report';
if($licvac == 1){
	$modelpdf = "licenrep";
}
if($licvac == 2){
	$modelpdf = "vacarep";
}
//Creamos variables de Sesion

$aDatosAdd = array('fecha_a'=>$date_a,'fecha_b'=>$date_b,'idMember'=>$idMember,'date_soli'=>$date_soli,'fk_member'=>$fk_member,'nomMember'=>$nomMember,'licvac'=>$licvac);
//print_r($aDatosAdd);
$_SESSION['aDatosAdd'] = serialize($aDatosAdd);
$_SESSION['aLicencias'] = serialize($aLicencias);
$_SESSION['aVacacion'] = serialize($aVacacion);



if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
{
   // echo "entra aqui";
	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}



//echo 'Modelo del reporte : '.$modelpdf;

	$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);

	if ($result < 0) dol_print_error($db,$result);
}

print '<div class="tabsAction">'."\n";
$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
// Note that $action and $object may have been modified by hook
//if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
print "<div class=\"tabsAction\">\n";
//if (count($aReport)>0)
print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
print '</div>';

print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';

$urlsource=$_SERVER['PHP_SELF'];
//$genallowed=$user->rights->assistance->lic->hiddemdoc;
//$delallowed=$user->rights->assistance->lic->deldoc;
$genallowed = 0;
$delallowed = 0;
print $formfile->showdocuments('assistance',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

$somethingshown=$formfile->numoffiles;

print '</td></tr></table>';
print '</div>'."\n";

// End of page
llxFooter();
$db->close();
?>