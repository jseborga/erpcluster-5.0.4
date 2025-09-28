<?php
//supplies.tpl.php
// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_fk_parent=GETPOST('search_fk_parent','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_country=GETPOST('search_fk_country','int');
$search_price=GETPOST('search_price','alpha');
$search_price_ttc=GETPOST('search_price_ttc','alpha');
$search_price_min=GETPOST('search_price_min','alpha');
$search_price_min_ttc=GETPOST('search_price_min_ttc','alpha');
$search_price_base_type=GETPOST('search_price_base_type','alpha');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_recuperableonly=GETPOST('search_recuperableonly','int');
$search_localtax1_tx=GETPOST('search_localtax1_tx','alpha');
$search_localtax1_type=GETPOST('search_localtax1_type','alpha');
$search_localtax2_tx=GETPOST('search_localtax2_tx','alpha');
$search_localtax2_type=GETPOST('search_localtax2_type','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_product_type=GETPOST('search_fk_product_type','int');
$search_pmp=GETPOST('search_pmp','alpha');
$search_finished=GETPOST('search_finished','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_cost_price=GETPOST('search_cost_price','alpha');
$search_status=GETPOST('search_status','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid"; 
// Set here default search field
if (! $sortorder) $sortorder="ASC";
// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Definition of fields for list
$arrayfields=array(

	't.fk_projet'=>array('label'=>$langs->trans("Fieldfk_projet"), 'checked'=>0),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>0),
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>0),
	't.fk_parent'=>array('label'=>$langs->trans("Fieldfk_parent"), 'checked'=>0),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
	't.fk_country'=>array('label'=>$langs->trans("Fieldfk_country"), 'checked'=>1),
	't.price'=>array('label'=>$langs->trans("Fieldprice"), 'checked'=>0),
	't.price_ttc'=>array('label'=>$langs->trans("Fieldprice_ttc"), 'checked'=>0),
	't.price_min'=>array('label'=>$langs->trans("Fieldprice_min"), 'checked'=>0),
	't.price_min_ttc'=>array('label'=>$langs->trans("Fieldprice_min_ttc"), 'checked'=>0),
	't.price_base_type'=>array('label'=>$langs->trans("Fieldprice_base_type"), 'checked'=>0),
	't.tva_tx'=>array('label'=>$langs->trans("Fieldtva_tx"), 'checked'=>0),
	't.recuperableonly'=>array('label'=>$langs->trans("Fieldrecuperableonly"), 'checked'=>0),
	't.localtax1_tx'=>array('label'=>$langs->trans("Fieldlocaltax1_tx"), 'checked'=>0),
	't.localtax1_type'=>array('label'=>$langs->trans("Fieldlocaltax1_type"), 'checked'=>0),
	't.localtax2_tx'=>array('label'=>$langs->trans("Fieldlocaltax2_tx"), 'checked'=>0),
	't.localtax2_type'=>array('label'=>$langs->trans("Fieldlocaltax2_type"), 'checked'=>0),
	't.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>0),
	't.fk_user_modif'=>array('label'=>$langs->trans("Fieldfk_user_modif"), 'checked'=>0),
	't.fk_product_type'=>array('label'=>$langs->trans("Fieldfk_product_type"), 'checked'=>1),
	't.pmp'=>array('label'=>$langs->trans("Fieldpmp"), 'checked'=>0),
	't.finished'=>array('label'=>$langs->trans("Fieldfinished"), 'checked'=>0),
	't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>0),
	't.cost_price'=>array('label'=>$langs->trans("Fieldcost_price"), 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>1),


    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
    //'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
	);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);  
  	// Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter"))
 	// All test are required to be compatible with all browsers
{
	$search_fk_projet='';
	$search_fk_product='';
	$search_ref='';
	$search_ref_ext='';
	$search_fk_parent='';
	$search_label='';
	$search_description='';
	$search_fk_country='';
	$search_price='';
	$search_price_ttc='';
	$search_price_min='';
	$search_price_min_ttc='';
	$search_price_base_type='';
	$search_tva_tx='';
	$search_recuperableonly='';
	$search_localtax1_tx='';
	$search_localtax1_type='';
	$search_localtax2_tx='';
	$search_localtax2_type='';
	$search_fk_user_author='';
	$search_fk_user_modif='';
	$search_fk_product_type='';
	$search_pmp='';
	$search_finished='';
	$search_fk_unit='';
	$search_cost_price='';
	$search_status='';

	
	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();


}


if (empty($reshook))
{
    	// Mass actions. Controls on number of lines checked
	$maxformassaction=1000;
	if (! empty($massaction) && count($toselect) < 1)
	{
		$error++;
		setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
	}
	if (! $error && count($toselect) > $maxformassaction)
	{
		setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
		$error++;
	}

		// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/monprojet/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}

//VIEW

$now=dol_now();

$form=new Form($db);

if ($search_fk_projet) $sql.= natural_search("fk_projet",$search_fk_projet);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_fk_parent) $sql.= natural_search("fk_parent",$search_fk_parent);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_fk_country) $sql.= natural_search("fk_country",$search_fk_country);
if ($search_price) $sql.= natural_search("price",$search_price);
if ($search_price_ttc) $sql.= natural_search("price_ttc",$search_price_ttc);
if ($search_price_min) $sql.= natural_search("price_min",$search_price_min);
if ($search_price_min_ttc) $sql.= natural_search("price_min_ttc",$search_price_min_ttc);
if ($search_price_base_type) $sql.= natural_search("price_base_type",$search_price_base_type);
if ($search_tva_tx) $sql.= natural_search("tva_tx",$search_tva_tx);
if ($search_recuperableonly) $sql.= natural_search("recuperableonly",$search_recuperableonly);
if ($search_localtax1_tx) $sql.= natural_search("localtax1_tx",$search_localtax1_tx);
if ($search_localtax1_type) $sql.= natural_search("localtax1_type",$search_localtax1_type);
if ($search_localtax2_tx) $sql.= natural_search("localtax2_tx",$search_localtax2_tx);
if ($search_localtax2_type) $sql.= natural_search("localtax2_type",$search_localtax2_type);
if ($search_fk_user_author) $sql.= natural_search("fk_user_author",$search_fk_user_author);
if ($search_fk_user_modif) $sql.= natural_search("fk_user_modif",$search_fk_user_modif);
if ($search_fk_product_type) $sql.= natural_search("fk_product_type",$search_fk_product_type);
if ($search_pmp) $sql.= natural_search("pmp",$search_pmp);
if ($search_finished) $sql.= natural_search("finished",$search_finished);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_cost_price) $sql.= natural_search("cost_price",$search_cost_price);
if ($search_status) $sql.= natural_search("status",$search_status);


if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);


// Add where from extra fields
//precios unitarios structura
$filter = array(1=>1);
$filterstatic = " AND t.entity = ".$conf->entity;
$filterstatic.= " AND t.fk_projet = ".$id;
$filterstatic.= " AND t.fk_categorie > 0 ";
$res = $pustr->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
if ($res>0)
{
	$idscat='';
	foreach ($pustr->lines AS $j => $line)
	{
		if (!empty($idscat)) $idscat.=',';
		$idscat.= $line->fk_categorie;
	}
}
$filter = array(1=>1);
$filterstatic = '';
if ($idscat)
	$filterstatic.= " AND t.fk_categorie IN (".$idscat.")";
$filterstatic.= " AND t.fk_projet = ".$object->id.' '.$sql;
$lSearch = false;
if (!empty($sql)) $lSearch = true;
$res = $projetprod->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic,$lView);
if ($res>0 || $lSearch)
{
	$lines = $projetprod->lines;
	$nbtotalofrecords = $projetprod->nbtotalofrecords;
	$num = count($lines);

	$params='&amp;id='.$object->id.'&amp;action=suppliesproj';
	if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

	if ($search_fk_projet != '') $params.= '&amp;search_fk_projet='.urlencode($search_fk_projet);
	if ($search_fk_product != '') $params.= '&amp;search_fk_product='.urlencode($search_fk_product);
	if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
	if ($search_ref_ext != '') $params.= '&amp;search_ref_ext='.urlencode($search_ref_ext);
	if ($search_fk_parent != '') $params.= '&amp;search_fk_parent='.urlencode($search_fk_parent);
	if ($search_label != '') $params.= '&amp;search_label='.urlencode($search_label);
	if ($search_description != '') $params.= '&amp;search_description='.urlencode($search_description);
	if ($search_fk_country != '') $params.= '&amp;search_fk_country='.urlencode($search_fk_country);
	if ($search_price != '') $params.= '&amp;search_price='.urlencode($search_price);
	if ($search_price_ttc != '') $params.= '&amp;search_price_ttc='.urlencode($search_price_ttc);
	if ($search_price_min != '') $params.= '&amp;search_price_min='.urlencode($search_price_min);
	if ($search_price_min_ttc != '') $params.= '&amp;search_price_min_ttc='.urlencode($search_price_min_ttc);
	if ($search_price_base_type != '') $params.= '&amp;search_price_base_type='.urlencode($search_price_base_type);
	if ($search_tva_tx != '') $params.= '&amp;search_tva_tx='.urlencode($search_tva_tx);
	if ($search_recuperableonly != '') $params.= '&amp;search_recuperableonly='.urlencode($search_recuperableonly);
	if ($search_localtax1_tx != '') $params.= '&amp;search_localtax1_tx='.urlencode($search_localtax1_tx);
	if ($search_localtax1_type != '') $params.= '&amp;search_localtax1_type='.urlencode($search_localtax1_type);
	if ($search_localtax2_tx != '') $params.= '&amp;search_localtax2_tx='.urlencode($search_localtax2_tx);
	if ($search_localtax2_type != '') $params.= '&amp;search_localtax2_type='.urlencode($search_localtax2_type);
	if ($search_fk_user_author != '') $params.= '&amp;search_fk_user_author='.urlencode($search_fk_user_author);
	if ($search_fk_user_modif != '') $params.= '&amp;search_fk_user_modif='.urlencode($search_fk_user_modif);
	if ($search_fk_product_type != '') $params.= '&amp;search_fk_product_type='.urlencode($search_fk_product_type);
	if ($search_pmp != '') $params.= '&amp;search_pmp='.urlencode($search_pmp);
	if ($search_finished != '') $params.= '&amp;search_finished='.urlencode($search_finished);
	if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
	if ($search_cost_price != '') $params.= '&amp;search_cost_price='.urlencode($search_cost_price);
	if ($search_status != '') $params.= '&amp;search_status='.urlencode($search_status);

	
	if ($optioncss != '') $param.='&optioncss='.$optioncss;


    	// Add $param from extra fields
	//foreach ($search_array_options as $key => $val)
	//{
	//	$crit=$val;
	//	$tmpkey=preg_replace('/search_options_/','',$key);
	//	if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
	//} 
	$title = $langs->trans('Supplies');
	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'supplies', 0, '', '', $limit);


	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
	if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="action" value="suppliesproj">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	if ($sall)
	{
		foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
		print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
	}

	$moreforfilter = '';
	$moreforfilter.='<div class="divsearchfield">';
	$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
	$moreforfilter.= '</div>';

	if (! empty($moreforfilter) && $abc)
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    
    	// Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</div>';
	}

	$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
	$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	
    	// This also change content of $arrayfields

	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    	// Fields title
	print '<tr class="liste_titre">';
    	// 
	if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_parent']['checked'])) print_liste_field_titre($arrayfields['t.fk_parent']['label'],$_SERVER['PHP_SELF'],'t.fk_parent','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_country']['checked'])) print_liste_field_titre($arrayfields['t.fk_country']['label'],$_SERVER['PHP_SELF'],'t.fk_country','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.price']['checked'])) print_liste_field_titre($arrayfields['t.price']['label'],$_SERVER['PHP_SELF'],'t.price','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.price_ttc']['checked'])) print_liste_field_titre($arrayfields['t.price_ttc']['label'],$_SERVER['PHP_SELF'],'t.price_ttc','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.price_min']['checked'])) print_liste_field_titre($arrayfields['t.price_min']['label'],$_SERVER['PHP_SELF'],'t.price_min','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.price_min_ttc']['checked'])) print_liste_field_titre($arrayfields['t.price_min_ttc']['label'],$_SERVER['PHP_SELF'],'t.price_min_ttc','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.price_base_type']['checked'])) print_liste_field_titre($arrayfields['t.price_base_type']['label'],$_SERVER['PHP_SELF'],'t.price_base_type','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tva_tx']['checked'])) print_liste_field_titre($arrayfields['t.tva_tx']['label'],$_SERVER['PHP_SELF'],'t.tva_tx','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.recuperableonly']['checked'])) print_liste_field_titre($arrayfields['t.recuperableonly']['label'],$_SERVER['PHP_SELF'],'t.recuperableonly','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.localtax1_tx']['checked'])) print_liste_field_titre($arrayfields['t.localtax1_tx']['label'],$_SERVER['PHP_SELF'],'t.localtax1_tx','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.localtax1_type']['checked'])) print_liste_field_titre($arrayfields['t.localtax1_type']['label'],$_SERVER['PHP_SELF'],'t.localtax1_type','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.localtax2_tx']['checked'])) print_liste_field_titre($arrayfields['t.localtax2_tx']['label'],$_SERVER['PHP_SELF'],'t.localtax2_tx','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.localtax2_type']['checked'])) print_liste_field_titre($arrayfields['t.localtax2_type']['label'],$_SERVER['PHP_SELF'],'t.localtax2_type','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_author']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'t.fk_user_author','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_modif']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_modif']['label'],$_SERVER['PHP_SELF'],'t.fk_user_modif','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_product_type']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_type']['label'],$_SERVER['PHP_SELF'],'t.fk_product_type','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.pmp']['checked'])) print_liste_field_titre($arrayfields['t.pmp']['label'],$_SERVER['PHP_SELF'],'t.pmp','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.finished']['checked'])) print_liste_field_titre($arrayfields['t.finished']['label'],$_SERVER['PHP_SELF'],'t.finished','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.cost_price']['checked'])) print_liste_field_titre($arrayfields['t.cost_price']['label'],$_SERVER['PHP_SELF'],'t.cost_price','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');




	print '</tr>'."\n";

    	// Fields title search
	print '<tr class="liste_titre">';
		// 
	if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
	if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_parent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_parent" value="'.$search_fk_parent.'" size="10"></td>';
	if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
	if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_country']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_country" value="'.$search_fk_country.'" size="10"></td>';
	if (! empty($arrayfields['t.price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price" value="'.$search_price.'" size="10"></td>';
	if (! empty($arrayfields['t.price_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_ttc" value="'.$search_price_ttc.'" size="10"></td>';
	if (! empty($arrayfields['t.price_min']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_min" value="'.$search_price_min.'" size="10"></td>';
	if (! empty($arrayfields['t.price_min_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_min_ttc" value="'.$search_price_min_ttc.'" size="10"></td>';
	if (! empty($arrayfields['t.price_base_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_base_type" value="'.$search_price_base_type.'" size="10"></td>';
	if (! empty($arrayfields['t.tva_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_tva_tx" value="'.$search_tva_tx.'" size="10"></td>';
	if (! empty($arrayfields['t.recuperableonly']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_recuperableonly" value="'.$search_recuperableonly.'" size="10"></td>';
	if (! empty($arrayfields['t.localtax1_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax1_tx" value="'.$search_localtax1_tx.'" size="10"></td>';
	if (! empty($arrayfields['t.localtax1_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax1_type" value="'.$search_localtax1_type.'" size="10"></td>';
	if (! empty($arrayfields['t.localtax2_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax2_tx" value="'.$search_localtax2_tx.'" size="10"></td>';
	if (! empty($arrayfields['t.localtax2_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax2_type" value="'.$search_localtax2_type.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_author']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_author" value="'.$search_fk_user_author.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_modif']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_modif" value="'.$search_fk_user_modif.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_product_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product_type" value="'.$search_fk_product_type.'" size="10"></td>';
	if (! empty($arrayfields['t.pmp']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_pmp" value="'.$search_pmp.'" size="10"></td>';
	if (! empty($arrayfields['t.finished']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_finished" value="'.$search_finished.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
	if (! empty($arrayfields['t.cost_price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cost_price" value="'.$search_cost_price.'" size="10"></td>';
	if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';




   		// Action column
	print '<td class="liste_titre" align="right">';
	$searchpitco=$form->showFilterAndCheckAddButtons(0);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";


	$i=0;
	$var=true;
	$totalarray=array();
	foreach ((array) $lines AS $j => $obj)
//	while ($i < min($num, $limit))
	{
		if ($obj)
		{
			$projetprod->fetch($obj->id);
			$var = !$var;
            // Show here line of result
			print '<tr '.$bc[$var].'>';
            // LIST_OF_TD_FIELDS_LIST

			if (! empty($arrayfields['t.fk_projet']['checked'])) 
			{
				print '<td>'.$obj->fk_projet.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.ref']['checked'])) 
			{
				print '<td>'.$obj->ref.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.label']['checked'])) 
			{
				print '<td>'.$obj->label.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked'])) 
			{
				print '<td>'.$obj->description.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_country']['checked'])) 
			{
				print '<td>'.getCountry($obj->fk_country,0,$db).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}

			if (! empty($arrayfields['t.fk_product_type']['checked'])) 
			{
            	// TODO change for compatibility with edit in place
            	$typeformat='select;0:'.$langs->trans("Product").',1:'.$langs->trans("Service");
            	print '<td>';
                print $form->editfieldval("Type",'fk_product_type',$obj->fk_product_type,$obj,$user->rights->produit->creer||$user->rights->service->creer,$typeformat);
                print '</td>';

				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.status']['checked'])) 
			{
				print '<td>'.$projetprod->getLibStatut(0).'</td>';
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
            // Action column
    		print '<td></td>';
    		if (! $i) $totalarray['nbfield']++;

    		print '</tr>';
    	}
    	$i++;
    }

    //$db->free($resql);

    print "</table>\n";
    print "</form>\n";

    //$db->free($result);
}
else
{
	//mostramos opciones para carga de insumos
	/*
	* Actions
	*/
	print '<div class="tabsAction">';
	if ($user->rights->monprojet->bud->crear) $userWrite = true;
	if ($object->public || $userWrite > 0 && $action != 'createup')
	{
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=addsupplies'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('ImportSuppliesBD').'</a>';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=copysupplies'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('CopySuppliesProjet').'</a>';
	}
	print '</div>';
}
?>