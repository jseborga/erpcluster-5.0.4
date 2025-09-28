<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *   	\file       codprueba/cclasfin_list.php
 *		\ingroup    codprueba
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-06-27 10:05
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
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
dol_include_once('/orgman/class/cclasfinext.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';


// Load traductions files requiredby by page
$langs->load("orgman");
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
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_father=GETPOST('search_fk_father','int');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_active=GETPOST('search_active','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');


$aActive=array();
$aActive[9]=$langs->trans('All');
$aActive[1]=$langs->trans('Activated');
$aActive[0]=$langs->trans('Disabled');

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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'cclasfinlist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('cclasfinlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('cclasfin');
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
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
	't.fk_father'=>array('label'=>$langs->trans("Fieldfk_father"), 'checked'=>1),
	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>1),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	//'t.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
	//'t.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
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

/* codLaiwett Aqui ponemos las clase que extienda si es el caso */
// Load object if id or ref is provided as parameter
$object=new Cclasfinext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objecttmp = new Cclasfinext($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

//if (GETPOST('cancel')) { $action='list'; $massaction=''; }
//if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

/* codLaiwett Aqui ponemos los codigos bnecesarios para poder hacer una alta en el mismo list */

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
		$search_label='';
		$search_detail='';
		$search_fk_father='';
		$search_ref_ext='';
		$search_active=9;

	}

	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/institutional/list.php',1);
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

			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/institutional/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->label=GETPOST('label','alpha');
		$object->detail=GETPOST('detail','alpha');
		$object->fk_father=GETPOST('fk_father','int')+0;
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->active=GETPOST('active','int');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/institutional/list.php',1);
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

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->label=GETPOST('label','alpha');
		$object->detail=GETPOST('detail','alpha');
		$object->fk_father=GETPOST('fk_father','int')+0;
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->active=GETPOST('active','int');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
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

	if ($action == 'checking')
	{
		$error=0;

		$object->entity=$conf->entity;
		$active = ($object->active?0:1);
		$object->active=$active;

		if (! $error)
		{
			$result=$object->checking($user);
			if ($result > 0)
			{
				setEventMessages("Registro ". $l = $object->label ." ".$u = $object->ref." modificado con exito", null, 'mesgs');
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
			$acLabeln='edit';
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
			header("Location: ".dol_buildpath('/orgman/institutional/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}


	/* donde se quiera ejecutar el reporte se debe poner*/

	if ($action == 'builddoc')
	{
		$id = $_SESSION['idEntrepot'];

		$id = 1;
		$object->fetch($id);

		if (empty($object->id))
			$object->id = $id;
		//$object->fetch_thirdparty();
	//$object->fetch_lines();
		if (GETPOST('model'))
		{
		//$object->setDocModel($user, GETPOST('model'));
			$object->modelpdf = GETPOST('model');
		}

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
		//$result=codprueba_pdf_create($db, $object, $object->modelpdf, $outputlangs);

		//codprueba_pdf_create(DoliDB $db, Commande $object, $modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
			exit;
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
$title = $langs->trans('Institutional');

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
$sql .= " t.label,";
$sql .= " t.detail,";
$sql .= " t.fk_father,";
$sql .= " t.ref_ext,";
$sql .= " t.active";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."c_clasfin as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_clasfin_extrafields as ef on (t.rowid = ef.fk_object)";
//$sql.= " WHERE 1 = 1";
$sql.= " WHERE t.entity IN (".getEntity('clasfin',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_fk_father) $sql.= natural_search("fk_father",$search_fk_father);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_active!=9) $sql.= natural_search("active",$search_active);


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
//	$obj = $db->fetch_object($resql);
//	$id = $obj->rowid;
//	header("Location: ".DOL_URL_ROOT.'/orgman/institutional/list.php?id='.$id);
//	exit;
}

llxHeader('', $title, $help_url);

$formfile = new Formfile($db);

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
if ($user->rights->orgman->inst->del) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';

if ($action == 'create') {
	print '<input type="hidden" name="action" value="add">';
} elseif ($action == 'edit'){
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$id.'">';
}
else{
	print '<input type="hidden" name="action" value="list">';
}


print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);


$formquestion = array(array('type'=>'label', 'label'=> $i = $object->ref .' '.$i = $object->label)
	);

/* codLaiwett confirm_delete */
/* aqui ponemos el codigo de mensaje para poder confirmar si queremos eliminar
Nota al parecer se debe llenar todos los datos que te pide la funcion fromconfirm */
if ($action == 'delete') {

	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id,
		$langs->trans('DeleteInstitutional'),
		$langs->trans('ConfirmDeleteInstitutional'),
		'',
		$formquestion,
		0,2);
	print $formconfirm;
}
/* endCodLaiwett */


if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';
/* codLaiwett -> Aqui Hacemos que el filtrado desapareca*/
/*$moreforfilter.='<div class="divsearchfield">';
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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_father']['checked'])) print_liste_field_titre($arrayfields['t.fk_father']['label'],$_SERVER['PHP_SELF'],'t.fk_father','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
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
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

/* codLaiwett Aqui preguntamos si estamos editando o creando un nuevo registro para poder mostrar el filtrado */
// Fields title search

if ($action !='create' && $action != 'edit')
{
	print '<tr class="liste_titre">';
		//
	if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
	if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
	if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_father']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_father" value="'.$search_fk_father.'" size="10"></td>';
	if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
	
	// active
	//if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';


	if (! empty($arrayfields['t.active']['checked'])){
		print '<td class="liste_titre center">';
		
		print $form->selectarray('search_active', $aActive, $search_active, 0, 0, 0, '', 0, 0, 0, '', 'maxwidth100');
		print '</td>';
		
	}

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
	/* 1.codLaiwett son dos formas de hacer un select la 1ra es la mas simplificada */
	$res = $object->fetchAll('ASC','label',0,0,array('active'=>1),'AND');
	/* esta segunda es u poco mas larga pero el resultado es el mismo*/
	$array = array();
	$options = '<option value="">'.$langs->trans('Select').'</option>';
	if ($res >0)
	{
		foreach ($object->lines AS $j => $line)
		{
			$array[$line->id] = $line->ref.' '.$line->label;
			$options.= '<option value="'.$line->id.'">'.$line->ref.' '.$line->label.'</option>';
		}
	}
	/* 1. endCodLaiwett*/

	/* codLiet -> aqui ponemos las acciones que hara */
	/* acccion de boton crear */
	if ($action == 'create')
	{
			// Show here line of result
		print '<tr '.$bc[$var].'>';
		if (! empty($arrayfields['t.entity']['checked']))
		{
			print '<td>'.'<input type="text" name="entity" value="'.GETPOST('entity').'">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.ref']['checked']))
		{
			print '<td>'.'<input type="text" name="ref" value="'.GETPOST('ref').'">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.label']['checked']))
		{
			print '<td>'.'<input type="text" name="label" value="'.GETPOST('label').'">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.detail']['checked']))
		{
			print '<td>'.'<input type="text" name="detail" value="'.GETPOST('detail').'">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_father']['checked']))
		{

			print '<td>'.'<select name="fk_father">'.$options.'</select>';
		//print '<td>'.$form->selectarray('fk_father',$array,GETPOST('fk_father'),1).'</td>';
		//print '<td>'.'<input type="text" name="fk_father" value="'.GETPOST('fk_father').'">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.ref_ext']['checked']))
		{
			print '<td>'.'<input type="text" name="ref_ext" value="'.GETPOST('ref_ext').'">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.active']['checked']))
		{
			print '<td>'.$form->selectyesno('active',(GETPOST('active')?GETPOST('active'):1),1).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}


		print '<td nowrap align="right">';
		print '<input class="butAction" type="submit" name="submit" value="'.$langs->trans('Save').'">';
		print '<input class="butActionDelete" type="submit" name="cancel" value="'.$langs->trans('Cancel').'">';
		print '</td>';
		print '</tr>';
	}
	/* endCodLaiwett */

	$arraycodprueba = array();

	while ($i < min($num, $limit))
	{
		$obj = $db->fetch_object($resql);
		if ($obj)
		{
			$var = !$var;
			$object->id = $obj->rowid;
			$object->ref = $obj->ref;
			$object->label = $obj->label;

		// Show here line of result
			print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST

			if ($action == 'edit' && $id == $obj->rowid)
			{
				if (! empty($arrayfields['t.ref']['checked']))
				{
					print '<td>'.'<input type="text" name="ref" value="'.$obj->ref.'">'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.label']['checked']))
				{
					print '<td>'.'<input type="text" name="label" value="'.$obj->label.'">'.'</td>';

					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.detail']['checked']))
				{
					print '<td>'.'<input type="text" name="detail" value="'.$obj->detail.'">'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				/*codLaiwett -> aqui vemos como cambiar en select para ser llenado en la base de datos*/
				if (! empty($arrayfields['t.fk_father']['checked']))
				{
					/* Aqui va mi prueba de que si puede funcionar*/
					$array1 = array();
					$options1 = '<option value="">'.$langs->trans('Select').'</option>';
					if ($res >0)
					{
						foreach ($object->lines AS $j => $line)
						{
							$array1[$line->id] = $line->ref.' '.$line->label;

							if ($line->id == $obj->fk_father) {
								$options1.= '<option value="'.$line->id.' " selected>'.$line->ref.' '.$line->label.'</option>';
							}else{
								$options1.= '<option value="'.$line->id.'">'.$line->ref.' '.$line->label.'</option>';
							}

						}
					}
				//print '<td>'.'<input type="text" name="fk_father" value="'.$obj->fk_father.'">'.'</td>';
					print '<td>'.'<select name="fk_father">'.$options1.'</select>';
					if (! $i) $totalarray['nbfield']++;
				}

				if (! empty($arrayfields['t.ref_ext']['checked']))
				{
					print '<td>'.'<input type="text" name="ref_ext" value="'.$obj->ref_ext.'">'.'</td>';
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

			}else{

				if (! empty($arrayfields['t.entity']['checked']))
				{
					print '<td>'.$obj->entity.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.ref']['checked']))
				{

					print '<td>'.$object->getNomUrl(1, '', 0,24,'','&action=edit').'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.label']['checked']))
				{
					print '<td>'.$obj->label.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.detail']['checked']))
				{
					print '<td>'.$obj->detail.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_father']['checked']))
				{
					if ($obj->fk_father>0)
					{
						$objecttmp->fetch($obj->fk_father);
						print '<td>'.$objecttmp->getNomUrl().'</td>';
					}
					else
						print '<td>'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.ref_ext']['checked']))
				{
					print '<td>'.$obj->ref_ext.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.active']['checked']))
				{
					$img = 'switch_off';
					if ($obj->active) $img = 'switch_on';
					$img = 'switch_off';
					if ($obj->active) $img = 'switch_on';
				//
					print '<td><a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=checking">'.img_picto('',$img).'</a></td>';
					if (! $i) $totalarray['nbfield']++;
				}


				/*aqui armamos el array*/
				$arraycodprueba[$i] = array('ref'=>$obj->ref,'label'=>$obj->label,'detail'=>$obj->detail);
				/*hasta aqui armamos el array*/



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
		//inline-block divButAction

		print '<td nowrap>';
		if ($user->rights->orgman->inst->del)
		{
			print '<input type="hidden" name="espacio" value="na">';
			print '<div class="right"><a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.img_picto('','delete').'</a></div>';
		}
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
print '</div>'."\n";

print '</form>'."\n";
/*
echo '<pre>';
print_r ($arraycodprueba);
echo '</pre>';
*/

print '<div class="tabsAction">'."\n";
$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	// Note that $action and $object may have been modified by hook
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($user->rights->orgman->inst->write)
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("New").'</a></div>'."\n";
	}
}
print '</div>'."\n";




// End of page
llxFooter();
$db->close();
