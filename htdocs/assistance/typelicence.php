<?php
/* Copyright (C) 2017 L Miguel Mendoza  <l.mendoza.liet@gmail.com>
 *  Desarrollador PHP, Java, App Android
 */

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

dol_include_once('/assistance/class/ctypelicenceext.class.php');

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
$search_code=GETPOST('search_code','alpha');
$search_label=GETPOST('search_label','alpha');
$search_type=GETPOST('search_type','alpha');
$search_limited_time=GETPOST('search_limited_time','int');
$search_type_limited=GETPOST('search_type_limited','int');
$search_active=GETPOST('search_active','int');


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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assistancetypelicencelist';

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
	't.code'=>array('label'=>$langs->trans("Fieldcode"), 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.type'=>array('label'=>$langs->trans("Fieldtype"), 'checked'=>1),
	't.limited_time'=>array('label'=>$langs->trans("Fieldlimited_time"), 'checked'=>1),
	't.type_limited'=>array('label'=>$langs->trans("Fieldtype_limited"), 'checked'=>1),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),


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
$object=new Ctypelicenceext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$tiposrep = $object->type;
$aType = array('V'=>$langs->trans('Vacation'),'L'=>$langs->trans('Licence'));
$aTypelimited = array('1'=>$langs->trans('Monthly'),'2'=>$langs->trans('Annual'));
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
	$now = dol_now();
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{

		$search_entity='';
		$search_code='';
		$search_label='';
		$search_type='';
		$search_limited_time='';
		$search_type_limited='';
		$search_active='';
		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Action to add record

	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/typelicence.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=$conf->entity;
		$object->code=GETPOST('code','alpha');
		$object->label=GETPOST('label','alpha');
		if(GETPOST('type','alpha') == 1){
			$object->type="V";
		}
		if(GETPOST('type','alpha') == 2){
			$object->type="L";
		}
		$object->limited_time = GETPOST('limited_time')+0;
		$object->type_limited = GETPOST('type_limited')+0;
		$object->active=GETPOST('active','int');
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datec = $now;
		$object->datem = $now;
		$object->status = 1;

		if (empty($object->code))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Code")), null, 'errors');
		}

		if (! $error)
		{
			//echo('Entra a crear');exit;
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				setEventMessages(null,"Adicionado con exito!",'mesgs');
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/typelicence.php',1);
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
			echo('Entra a crear else');exit;
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;

		$object->entity=$conf->entity;
		$object->code=GETPOST('code','alpha');
		$object->label=GETPOST('label','alpha');
		if(GETPOST('type','alpha')==1){
			$object->type="V";
		}
		elseif(GETPOST('type','alpha')==2){
			$object->type="L";
		}
		else
			$object->type="";
		$object->limited_time = GETPOST('limited_time')+0;
		$object->type_limited = GETPOST('type_limited')+0;
		$object->active=GETPOST('active','int');
		//echo('Update typeLic'.$object->type);
		if ($object->fk_user_create<=0)
			$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		if ($object->datec<=0)
			$object->datec = $now;
		$object->datem = $now;
		$object->status = 1;

		if (empty($object->code))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Code")), null, 'errors');
		}
		if (empty($object->type))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
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

	/* codLaiwett */
		// Accion de editar el switch

	if ($action == 'update_switch')
	{
		$error=0;
		$object->entity=$conf->entity;
		$active = ($object->active?0:1);
		$object->active=$active;

		if (! $error)
		{
			$result=$object->update_switch($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
					 // Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'label');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}
	/* endCodLaiwett */

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistance/typelicence.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
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
$title = $langs->trans('Typelicence');

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
$sql .= " t.code,";
$sql .= " t.label,";
$sql .= " t.type,";
$sql.= " t.limited_time,";
$sql.= " t.type_limited,";
$sql .= " t.active,";
$sql.= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."c_type_licence as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_type_licence_extrafields as ef on (t.rowid = ef.fk_object)";
//$sql.= " WHERE 1 = 1";
$sql.= " WHERE t.entity IN (".getEntity('ctypelicence',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_code) $sql.= natural_search("code",$search_code);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_type != '-1')
	if ($search_type) $sql.= natural_search("type",$search_type);
if ($search_type_limited != '-1')
	if ($search_type_limited) $sql.= natural_search("type_limited",$search_type_limited);
//if (isset($_POST['search_limited_time']) && $search_limited_time>=0 && !is_null($search_limited_time)) $sql.= " AND t.limited_time = ".$search_limited_time;
if ($search_active) $sql.= natural_search("active",$search_active);


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
	header("Location: ".DOL_URL_ROOT.'/ctypelicence/card.php?id='.$id);
	exit;
}

llxHeader('', $title, $help_url);


$arrayofselected=is_array($toselect)?$toselect:array();

//Armamos un array para el tipo Vacacion o Licencia
$arrayPermisos = array(1=>"Vacaciones",2=>"Licencias");
$arrayPerm = array(1=>"V",2=>"L");
$opcPermiso = '<option value="">'.$langs->trans('Select').'</option>';

foreach ($arrayPermisos AS $a => $line)
{
		//$arrayPerm[$a] = $line;
	$selected = '';
		//echo('type > '.$tiposrep);
	if ($tiposrep == $arrayPerm[$a]) $selected = ' selected';
	$opcPermiso.= '<option value="'.$a.'" '.$selected.'>'.$line.'</option>';
}

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_code != '') $param.= '&amp;search_code='.urlencode($search_code);
if ($search_label != '') $param.= '&amp;search_label='.urlencode($search_label);
if ($search_type != '') $param.= '&amp;search_type='.urlencode($search_type);
if ($search_limited_time != '') $param.= '&amp;search_limited_time='.urlencode($search_limited_time);
if ($search_type_limited != '') $param.= '&amp;search_type_limited='.urlencode($search_type_limited);
if ($search_active != '') $param.= '&amp;search_active='.urlencode($search_active);
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

//Formulario
print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
//Aqui podemos poner las acciones de adicionar(Crear) y Editar
if ($action == 'create')
{
	print '<input type="hidden" name="action" value="add">';
}
else{
	if($action == 'edit')
	{
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$id.'">';
	}
	else{
		print '<input type="hidden" name="action" value="list">';
	}
}
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);


if ($action == 'delete') {

	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id,
		$langs->trans('DeleteMyOjbect'),
		$langs->trans('ConfirmDeleteMyObject'),
		'confirm_delete',
		$formquestion,
		0,2);
	print $formconfirm;
}


if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';
//$moreforfilter.='<div class="divsearchfield">';
//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
//$moreforfilter.= '</div>';

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

//print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code']['checked'])) print_liste_field_titre($arrayfields['t.code']['label'],$_SERVER['PHP_SELF'],'t.code','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type']['checked'])) print_liste_field_titre($arrayfields['t.type']['label'],$_SERVER['PHP_SELF'],'t.type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.limited_time']['checked'])) print_liste_field_titre($arrayfields['t.limited_time']['label'],$_SERVER['PHP_SELF'],'t.limited_time','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_limited']['checked'])) print_liste_field_titre($arrayfields['t.type_limited']['label'],$_SERVER['PHP_SELF'],'t.type_limited','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);

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

print_liste_field_titre(($user->rights->assistance->allfield?$selectedfields:''), $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

//Aqui para que no se muestre el resultado

if($action != 'create' && $action != 'edit'){
	// Fields title search
	print '<tr class="liste_titre">';
	//
	if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
	if (! empty($arrayfields['t.code']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code" value="'.$search_code.'" size="10"></td>';
	if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
	if (! empty($arrayfields['t.type']['checked']))
	{
		print '<td class="liste_titre">';
		print $form->selectarray('search_type',$aType,$search_type,1);
		print '</td>';
	}
	//if (! empty($arrayfields['t.limited_time']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_limited_time" value="'.$search_limited_time.'" size="5"></td>';
	if (! empty($arrayfields['t.limited_time']['checked'])) print '<td class="liste_titre"></td>';

	if (! empty($arrayfields['t.type_limited']['checked'])) print '<td class="liste_titre"></td>';
	if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"></td>';

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
	$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";
}

$i=0;
$var=true;
$totalarray=array();

if($action == 'create'){

	print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
	if (! empty($arrayfields['t.entity']['checked']))
	{
		print '<td><input type="text" name="entity" value="'.GETPOST('entity').'"></td>';
		if (! $i) $totalarray['nbfield']++;
	}

	if (! empty($arrayfields['t.code']['checked']))
	{
		print '<td><input type="text" name="code" value="'.GETPOST('code').'"></td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.label']['checked']))
	{
		print '<td><input type="text" name="label" value="'.GETPOST('label').'"></td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.type']['checked']))
	{
		print '<td><select name="type">'.$opcPermiso.'</select></td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.limited_time']['checked']))
	{
		print '<td><input type="number" name="limited_time" value="'.GETPOST('limited_time').'"></td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.type_limited']['checked']))
	{
		print '<td>';
		print $form->selectarray('type_limited',$aTypelimited,GETPOST('type_limited'),1);
		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.active']['checked']))
	{
		print '<td>'.$form->selectyesno('active',(GETPOST('active')?GETPOST('active'):1),1).'</td>';
		if (! $i) $totalarray['nbfield']++;
	}

	print '<td nowrap>';
	print '<input class="butAction" type="submit" name="submit" value="'.$langs->trans('Save').'">';
	print '<input class="butAction" type="submit" name="cancel" value="'.$langs->trans('Cancel').'">';
	print '</td>';
	print '</tr>';

}

while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		$object->id = $obj->rowid;
		$object->code = $obj->code;
		$object->label = $obj->label;

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST\
		if($action == 'edit' && $id == $obj->rowid)
		{
			if (! empty($arrayfields['t.code']['checked']))
			{
				print '<td><input type="text" name="code" value="'.$obj->code.'"></td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.label']['checked']))
			{
				print '<td><input type="text" name="label" value="'.$obj->label.'"></td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.type']['checked']))
			{
				$tiposrep = $obj->type;
				//echo $tiposrep;
				print '<td><select name="type">'.$opcPermiso.'</select></td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.limited_time']['checked']))
			{
				print '<td><input type="number" name="limited_time" value="'.(GETPOST('limited_time')?GETPOST('limited_time'):$obj->limited_time).'"></td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.type_limited']['checked']))
			{
				print '<td>';
				print $form->selectarray('type_limited',$aTypelimited,(GETPOST('type_limited')?GETPOST('type_limited'):$obj->type_limited),1);
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.active']['checked']))
			{
				print '<td>'.$form->selectyesno('active',$obj->active,1).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}

			print '<td>';
			print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
			print '</td>';

		}
		else
		{
			if (! empty($arrayfields['t.entity']['checked']))
			{
				print '<td>'.$obj->entity.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.code']['checked']))
			{
				//print '<td>'.$obj->code.'</td>';
				if ($user->rights->assistance->def->mod)
					print '<td>'.$object->getNomUrladd(1,'',0,24,'','&action=edit').'</td>';
				else
					print '<td>'.$object->getNomUrladd(1,'',0,24,'','').'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.label']['checked']))
			{
				print '<td>'.$obj->label.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.type']['checked']))
			{
				if($obj->type=='V')print '<td>Vacacion</td>';
				elseif($obj->type=='L')print '<td>Licencia</td>';
				else print '<td></td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.limited_time']['checked']))
			{
				print '<td>'.$obj->limited_time.' '.$langs->trans('Hours').'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.type_limited']['checked']))
			{
				print '<td>'.$aTypelimited[$obj->type_limited].'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.active']['checked']))
			{
				$img = 'switch_off';
				if ($obj->active) $img = 'switch_on';
				//
				print '<td><a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=update_switch">'.img_picto('',$img).'</a></td>';
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
			// Accion del eliminar
			print '<td>';
			print '<div class="inline-block divButAction"><a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.img_picto('',DOL_URL_ROOT.'/assistance/img/delete','',1).'</a></div>';
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
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
//print '</div>'."\n";

print '</form>'."\n";


print '<div class="tabsAction">'."\n";
$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//if ($user->rights->assistance->write)
	//{
	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("New").'</a></div>'."\n";
	//}
}
print '</div>'."\n";

// End of page
llxFooter();
$db->close();
