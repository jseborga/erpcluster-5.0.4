<?php


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
$search_fk_father=GETPOST('search_fk_father','int');
$search_fk_facture_fourn=GETPOST('search_fk_facture_fourn','int');
$search_fk_facture=GETPOST('search_fk_facture','int');
$search_type_group=GETPOST('search_type_group','alpha');
$search_type_patrim=GETPOST('search_type_patrim','alpha');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_item_asset=GETPOST('search_item_asset','int');

$search_date_adq=GETPOST('search_date_adq','alpha');

$search_date_day=GETPOST('search_date_day','int');
$search_date_month=GETPOST('search_date_month','int');
$search_date_year=GETPOST('search_date_year','int');

$search_date_reval=GETPOST('search_date_reval','alpha');

$search_useful_life_residual=GETPOST('search_useful_life_residual','int');
$search_quant=GETPOST('search_quant','alpha');
$search_coste=GETPOST('search_coste','alpha');
$search_coste_residual=GETPOST('search_coste_residual','alpha');
$search_coste_reval=GETPOST('search_coste_reval','alpha');
$search_coste_residual_reval=GETPOST('search_coste_residual_reval','alpha');
$search_useful_life_reval=GETPOST('search_useful_life_reval','alpha');
$search_dep_acum=GETPOST('search_dep_acum','alpha');
$search_amount_sale=GETPOST('search_amount_sale','alpha');
$search_descrip=GETPOST('search_descrip','alpha');
$search_number_plaque=GETPOST('search_number_plaque','alpha');
$search_trademark=GETPOST('search_trademark','alpha');
$search_model=GETPOST('search_model','alpha');
$search_anio=GETPOST('search_anio','int');
$search_fk_asset_sup=GETPOST('search_fk_asset_sup','int');
$search_fk_location=GETPOST('search_fk_location','int');
$search_code_bar=GETPOST('search_code_bar','alpha');
$search_fk_method_dep=GETPOST('search_fk_method_dep','int');
$search_type_property=GETPOST('search_type_property','alpha');
$search_code_bim=GETPOST('search_code_bim','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_useful_life=GETPOST('search_useful_life','alpha');
$search_percent=GETPOST('search_percent','alpha');
$search_account_accounting=GETPOST('search_account_accounting','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_coste_unit_use=GETPOST('search_coste_unit_use','alpha');
$search_fk_unit_use=GETPOST('search_fk_unit_use','int');
$search_codcont=GETPOST('search_codcont','int');
$search_codaux=GETPOST('search_codaux','int');
$search_orgfin=GETPOST('search_orgfin','alpha');
$search_cod_rube=GETPOST('search_cod_rube','alpha');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_resp=GETPOST('search_fk_resp','int');
$search_departament_name=GETPOST('search_departament_name','alpha');
$search_resp_name=GETPOST('search_resp_name','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_mark=GETPOST('search_mark','alpha');
$search_been=GETPOST('search_been','alpha');
$search_fk_asset_mov=GETPOST('search_fk_asset_mov','int');
$search_status_reval=GETPOST('search_status_reval','int');
$search_statut=GETPOST('search_statut','int');


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

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'assetlist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('assetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('asset');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";


// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	}
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



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

//$form=new Form($db);

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

$sql .= " t.entity,";
$sql .= " t.fk_father,";
$sql .= " t.fk_facture_fourn,";
$sql .= " t.fk_facture,";
$sql .= " t.type_group,";
$sql .= " t.type_patrim,";
$sql .= " t.ref,";
$sql .= " t.ref_ext,";
$sql .= " t.item_asset,";
$sql .= " t.date_adq,";
$sql .= " t.date_day,";
$sql .= " t.date_month,";
$sql .= " t.date_year,";
$sql .= " t.date_active,";
$sql .= " t.date_reval,";
$sql .= " t.useful_life_residual,";
$sql .= " t.quant,";
$sql .= " t.coste,";
$sql .= " t.coste_residual,";
$sql .= " t.coste_reval,";
$sql .= " t.coste_residual_reval,";
$sql .= " t.useful_life_reval,";
$sql .= " t.dep_acum,";
$sql .= " t.date_baja,";
$sql .= " t.amount_sale,";
$sql .= " t.descrip,";
$sql .= " t.number_plaque,";
$sql .= " t.trademark,";
$sql .= " t.model,";
$sql .= " t.anio,";
$sql .= " t.fk_asset_sup,";
$sql .= " t.fk_location,";
$sql .= " t.code_bar,";
$sql .= " t.fk_method_dep,";
$sql .= " t.type_property,";
$sql .= " t.code_bim,";
$sql .= " t.fk_product,";
$sql .= " t.useful_life,";
$sql .= " t.percent,";
$sql .= " t.account_accounting,";
$sql .= " t.fk_unit,";
$sql .= " t.model_pdf,";
$sql .= " t.coste_unit_use,";
$sql .= " t.fk_unit_use,";
$sql .= " t.codcont,";
$sql .= " t.codaux,";
$sql .= " t.orgfin,";
$sql .= " t.cod_rube,";
$sql .= " t.fk_departament,";
$sql .= " t.fk_resp,";
$sql .= " t.departament_name,";
$sql .= " t.resp_name,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.date_create,";
$sql .= " t.date_mod,";
$sql .= " t.mark,";
$sql .= " t.been,";
$sql .= " t.tms,";
$sql .= " t.fk_asset_mov,";
$sql .= " t.status_reval,";
$sql .= " t.statut, ";
$sql .= " ag.code,";
$sql .= " ag.useful_life as cg_useful_life, ";
$sql .= " ag.label as cg_label ";

// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_assets_group as ag ON ag.code = t.type_group";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."assets_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1 AND t.statut >= 0";
$sql.= " ORDER BY t.type_group ASC";

//echo $sql;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}
/****************************************************/
$aAssets = array();

$j = 0;

while ($j < $nbtotalofrecords)
{
	$obj = $db->fetch_object($result);
	if ($obj)
	{
		$object->statut = $obj->statut;
		$aAssets[$j]['id']=$obj->rowid;
		$aAssets[$j]['ref']=$obj->ref;
		$aAssets[$j]['ref_ext']=$obj->ref_ext;
		$aAssets[$j]['type_group']=$obj->type_group;
		$aAssets[$j]['label_group']=$obj->cg_label;
		$aAssets[$j]['group_useful_life']=$obj->cg_useful_life;
		$aAssets[$j]['descrip']=$obj->descrip;
		$aAssets[$j]['date_adq']=$db->jdate($obj->date_adq);
		$aAssets[$j]['date_active']=$db->jdate($obj->date_active);
		$aAssets[$j]['date_reval']=$db->jdate($obj->date_reval);
		$aAssets[$j]['coste']=$obj->coste;
		$aAssets[$j]['useful_life']=$obj->useful_life;
		$aAssets[$j]['status']=$object->getLibStatut(0);
	}
	$j++;
}

if ($nbtotalofrecords)
{
	//Para los Tabs del sistema

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"", "","","","");
	print_liste_field_titre($langs->trans("Refext"),"", "","","","");
	print_liste_field_titre($langs->trans("Typegroup"),"", "",'','','');
	print_liste_field_titre($langs->trans("Descrip"),"", "",'','','');
	print_liste_field_titre($langs->trans("Dateadq"),"", "","","","");
	print_liste_field_titre($langs->trans("Datereval"),"", "","","","");
	print_liste_field_titre($langs->trans("Coste"),"", "","","",'align="center"');
	print_liste_field_titre($langs->trans("Usefullife"),"", "",'','','align="center"');
	print_liste_field_titre($langs->trans("Status"),"", "",'','','align="center"');
	print_liste_field_titre($langs->trans("Dateoff"),"", "",'','','align="center"');
	print "</tr>\n";

	$var=true;
	$a = 0;
	$aLineas = array();
	$tiempo_Vida = 0;
	foreach ($aAssets as $pos => $valor) {

		if(!empty($valor['date_reval']) && !is_null($valor['date_reval'])){
			if(!empty($valor['useful_life'])){
				$dateOff = dol_time_plus_duree($valor['date_reval'], ceil($valor['useful_life']*365),'d');
				$tiempo_Vida = $valor['useful_life'];
				$dias = num_between_day(dol_now(),$dateOff);

			}else{
				$dateOff = dol_time_plus_duree($valor['date_reval'], ceil($valor['group_useful_life']*365),'d');
				$dias = num_between_day(dol_now(),$dateOff);
				$tiempo_Vida = $valor['group_useful_life'];
			}

		}elseif (!empty($valor['useful_life'])) {
			$dateOff = dol_time_plus_duree($valor['date_active'], ceil($valor['useful_life']*365),'d');
			$dias = num_between_day(dol_now(),$dateOff);
			$tiempo_Vida = ceil($valor['useful_life']);
			$tiempo_Vida = $valor['useful_life'];
		}elseif (!empty($valor['group_useful_life'])) {
			$dateOff = dol_time_plus_duree($valor['date_active'], ceil($valor['group_useful_life']*365),'d');
			$dias = num_between_day(dol_now(),$dateOff);
			$tiempo_Vida = $valor['group_useful_life'];
		}

		if( !empty($dias) && $dias <= $depreciacion && $tiempo_Vida >0){

			print '<tr '.$bc[$var].'>';

			$object->id = $valor['id'];
			$object->ref = $valor['ref'];
			//print '<td>'.$valor['ref'].'</td>';
			print '<td>'.$object->getNomUrl(1).'</td>';
			$aLineas[$valor['type_group']][$a]['ref'] = $valor['ref'];

			$object->ref = $valor['ref_ext'];
			print '<td>'.$object->getNomUrl().'</td>';
			$aLineas[$valor['type_group']][$a]['ref_ext'] = $valor['ref_ext'];

			print '<td align = "left">'.$valor['label_group'].'</td>';
			//$aLineas[$valor['type_group']][$a]['ref_ext'] = $valor['ref_ext'];

			print '<td>'.$valor['descrip'].'</td>';
			$aLineas[$valor['type_group']][$a]['descrip'] = $valor['descrip'];

			print '<td>'.dol_print_date($valor['date_adq'],'day').'</td>';
			$aLineas[$valor['type_group']][$a]['date_adq'] = $valor['date_adq'];

			print '<td>'.dol_print_date($valor['date_reval'],'day').'</td>';
			$aLineas[$valor['type_group']][$a]['date_reval'] = $valor['date_reval'];


			print '<td align = "right">'.price2num($valor['coste']).'</td>';
			$aLineas[$valor['type_group']][$a]['coste'] = $valor['coste'];


			//print '<td align = "center">'.($valor['useful_life']*365).'</td>';
			//print '<td align = "center">'.ceil($valor['useful_life']).'</td>';
			print '<td align = "center">'.$tiempo_Vida.'</td>';
			$aLineas[$valor['type_group']][$a]['useful_life'] = $tiempo_Vida;

			print '<td align = "center">'.$valor['status'].'</td>';
			//print '<td>'.dol_print_date($dateOff,'day')." - ".$dias.'</td>';

			print '<td>'.dol_print_date($dateOff,'day').'</td>';
			$aLineas[$valor['type_group']][$a]['dateoff'] = $dateOff;

			print '</tr>';

		}

		if(empty($dias) && $depreciacion == 0 && $tiempo_Vida >0){

			print '<tr '.$bc[$var].'>';
			$object->id = $valor['id'];
			$object->ref = $valor['ref'];
			//print '<td>'.$valor['ref'].'</td>';
			print '<td>'.$object->getNomUrl(1).'</td>';
			$aLineas[$valor['type_group']][$a]['ref'] = $valor['ref'];

			$object->ref = $valor['ref_ext'];
			print '<td>'.$object->getNomUrl().'</td>';
			$aLineas[$valor['type_group']][$a]['ref_ext'] = $valor['ref_ext'];

			print '<td align = "left">'.$valor['label_group'].'</td>';
			//$aLineas[$valor['type_group']][$a]['ref_ext'] = $valor['ref_ext'];

			print '<td>'.$valor['descrip'].'</td>';
			$aLineas[$valor['type_group']][$a]['descrip'] = $valor['descrip'];

			print '<td>'.dol_print_date($valor['date_adq'],'day').'</td>';
			$aLineas[$valor['type_group']][$a]['date_adq'] = $valor['date_adq'];

			print '<td>'.dol_print_date($valor['date_reval'],'day').'</td>';
			$aLineas[$valor['type_group']][$a]['date_reval'] = $valor['date_reval'];


			print '<td align = "right">'.price2num($valor['coste']).'</td>';
			$aLineas[$valor['type_group']][$a]['coste'] = $valor['coste'];


			//print '<td align = "center">'.($valor['useful_life']*365).'</td>';
			//print '<td align = "center">'.ceil($valor['useful_life']).'</td>';
			print '<td align = "center">'.$tiempo_Vida.'</td>';
			$aLineas[$valor['type_group']][$a]['useful_life'] = $tiempo_Vida;

			print '<td align = "center">'.$valor['status'].'</td>';
			//print '<td>'.dol_print_date($dateOff,'day')." - ".$dias.'</td>';

			print '<td>'.dol_print_date($dateOff,'day').'</td>';
			$aLineas[$valor['type_group']][$a]['dateoff'] = $dateOff;
			print '</tr>';
		}


		$var     = !$var;
		$dateOff = 0;
		$dias    = 0;
		$a++;


	}
	print "</table>";


	$aReporte = array(1=>$aLineas,2=>$aDepre[$depreciacion]);
	$_SESSION['aReporte'] = serialize($aReporte);

	print '<div class="tabsAction">'."\n";
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
	print '</div>'."\n";
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';

	//Aqui estaba el reporte
	$filename=$period_year.'/assetslow';
	$filedir=$conf->assets->dir_output.'/'.$period_year.'/assetslow';

	$modelpdf = "assetslow";

	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$object->depreciation = $depreciacion;
		//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);

	$urlsource=$_SERVER['PHP_SELF'];
	$genallowed=$user->rights->assets->repinv->write;
	$delallowed=$user->rights->assets->repinv->del;
	print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

	$somethingshown=$formfile->numoffiles;

	print '</td></tr></table>';
}
else
{
	setEventMessages($langs->trans('Therearenorecords'),null,'warnings');
}



