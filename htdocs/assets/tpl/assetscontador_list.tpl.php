<?php
/* Copyright (C) 7102 No One  <example@email.com>
 *
 */
// Load traductions files requiredby by page
$langs->load("assets");
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

$search_fk_asset=GETPOST('search_fk_asset','int');
$search_period_month=GETPOST('search_period_month','int');
$search_period_year=GETPOST('search_period_year','int');
$search_quant=GETPOST('search_quant','alpha');
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
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

$period_year = $_SESSION['period_year'];

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assetslist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('assetslist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('assets');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.fk_asset'=>array('label'=>$langs->trans("Fieldfk_asset"), 'checked'=>1),
	't.period_month'=>array('label'=>$langs->trans("Fieldperiod_month"), 'checked'=>1),
	't.period_year'=>array('label'=>$langs->trans("Fieldperiod_year"), 'checked'=>1),
	't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
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
//$object=new Assetscontador($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	//$result=$object->fetch($id,$ref);
	//if ($result < 0) dol_print_error($db);
}
$objUser = new User($db);



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

		$search_fk_asset='';
		$search_period_month='';
		$search_period_year='';
		$search_quant='';
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
$title = $langs->trans('MyModuleListTitle');

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

$sql .= " t.fk_asset,";
$sql .= " t.period_month,";
$sql .= " t.period_year,";
$sql .= " t.quant,";
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
$sql.= " FROM ".MAIN_DB_PREFIX."assets_contador as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."assets_contador_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_asset = ".$id;
$sql.= " ORDER BY t.period_month ASC, t.period_year ASC";

//echo $sql;
echo "<br><br>";

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}
//MOSTRAMOS LA TABLA

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Month"),"","","","","");
print_liste_field_titre($langs->trans("Year"),"","","","","");
print_liste_field_titre($langs->trans("Depreciationperiod"),"","",'',"",'align="right"');
print_liste_field_titre($langs->trans("Depreciationacumulated"),"","","","",'align="right"');
print_liste_field_titre($langs->trans("Balance"),"","","","",'align="right"');
print '</tr>';


$depAcumu = 0;
$saldo = 0;
$aValores = unserialize($_SESSION['aValores']);
$val_coste = $aValores[4];

$fac_depre = $aValores[7];
$aLineas = array();
$i=0;
$var=true;
$totalarray=array();
while ($i < $nbtotalofrecords)
{
	$obj = $db->fetch_object($result);
	if ($obj)
	{
		$var = !$var;
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST

		print '<td>'.$obj->period_month.'</td>';
		$aLineas[$i]['mes'] = $obj->period_month;

		print '<td>'.$obj->period_year.'</td>';
		$aLineas[$i]['anio'] = $obj->period_year;


		$quant = $obj->quant * $fac_depre;
		print '<td align="right">'.price(price2num($quant,'MT')).'</td>';
		$aLineas[$i]['dep_per'] = $quant;

		$depAcumu = $quant + $depAcumu;
		print '<td align="right">'.price(price2num($depAcumu,'MT')).'</td>';
		$aLineas[$i]['dep_acu'] = $depAcumu;


		if($i == 0){
			$saldo = $val_coste - $quant;
		}else{
			$saldo = $saldo - $quant;
		}
		print '<td align="right">'.price(price2num($saldo,'MT')).'</td>';
		$aLineas[$i]['saldo'] = $saldo;

		print '</tr>';
	}
	$i++;
}

print '</table>';

$db->free($resql);
$aReporte = array(1=>$aLineas);
$_SESSION['aValores'] = serialize($aValores);
$_SESSION['aReporte'] = serialize($aReporte);

if ($nbtotalofrecords>0)
{
	print '<div class="tabsAction">'."\n";
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Spreadsheet").'</a>';
	print '</div>'."\n";
}
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';

//Aqui estaba el reporte
$filename='assets/'.$period_year.'/contador/'.$aValores[1];
$filedir=$conf->assets->dir_output.'/assets/'.$period_year.'/contador/'.$aValores[1];

//echo "Dir : ". $filedir;

$modelpdf = "contador";

$outputlangs = $langs;
$newlang = '';
if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
if (! empty($newlang)) {
	$outputlangs = new Translate("", $conf);
	$outputlangs->setDefaultLang($newlang);
}
if ($nbtotalofrecords>0)
{
	//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);
}

$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
$genallowed=$user->rights->assets->repinv->write;
$delallowed=$user->rights->assets->repinv->del;
print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

$somethingshown=$formfile->numoffiles;

print '</td></tr></table>';
