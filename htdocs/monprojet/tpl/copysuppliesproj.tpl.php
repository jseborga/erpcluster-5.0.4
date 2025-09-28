<?php
//supplies.tpl.php
// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$tag	    = GETPOST('tag','int');
$seltype    = GETPOST('seltype','int');
$fk_projet  = GETPOST('fk_projet');
$search     = GETPOST('search');
$selins     = GETPOST('selins');
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
$aProductcat = unserialize($_SESSION['aProductcat']);
$selectins = unserialize($_SESSION['selectins']);
/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
if ($action == 'selsupplies')
{
	$aIns = GETPOST('ins');
	if (count($aIns))
	{
		foreach((array) $aIns AS $fk_product =>$value)
		{
			$selectins[$fk_product] = $fk_product;
		}
		$_SESSION['selectins'] = serialize($selectins);
	}
	$action = 'copysupplies';
}
if ($action == 'delsupplies')
{
	$aIns = GETPOST('ins');
	if (count($aIns))
	{
		foreach((array) $aIns AS $fk_product => $value)
		{
			unset($selectins[$fk_product]);
		}
		$_SESSION['selectins'] = serialize($selectins);
	}
	$action = 'copysupplies';
}
	//VIEW

$now=dol_now();

$form=new Form($db);
dol_include_once('/monprojet/class/html.formprojetext.class.php');
$formproject = new FormProjetsext($db);

	//preguntamos
print '<div>';
print '<div class="cajabone">';
print '<h2>'.$langs->trans('Select').'</h2>';
//if (empty($tag))
//{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="copysupplies">';
	print '<input type="hidden" name="tag" value="1">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
//}
print '<div>'.'<input class="flat" type="radio" name="seltype" value="1" '.($seltype==1?'checked':'').' onclick="ocultarproj();">'.$langs->trans('BD Mother').'</div>';
print '<div style="clear:left;"></div>';
print '<div>'.'<input class="flat" type="radio" name="seltype" value="2" '.($seltype==2?'checked':'').' onclick="mostrarproj();">'.$langs->trans('Project').'</div>';
print '<div style="clear:left;"></div>';

$display = (($tag>0 && $seltype == 2)?'block':'none');
print '<div id="tagprojet" class="tagprojet" style="display:'.$display.';">';
$filterkey = '';
$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
print '</div>';
print '<div style="clear:left;"></div>';
//if (empty($tag))
//{
	print '<div>';
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';
	print '</div>';

	print '</form>';
//}
print '</div>';

if ($tag == '1' && $seltype)
{
	print '<div class="cajabtwo">';
	print '<h2>'.$langs->trans('Typeselect').'</h2>';
	if ($tag == 1)
	{
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="copysupplies">';
		print '<input type="hidden" name="tag" value="1">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="seltype" value="'.$seltype.'">';
		print '<input type="hidden" name="fk_projet" value="'.$fk_projet.'">';
		print '<input type="hidden" name="selins" value="'.$selins.'">';
		print '<div>'.'<input class="flat" type="text" name="search" value="'.$search.'" >'.'<input type="submit" value="'.$langs->trans('Search').'">'.'</div>';
		print '</form>';
	}
	print '<div style="clear:left;"></div>';

	if ($tag == 1)
	{
		print '<form method="POST" id="FrmSelectAllChecbox" name="FrmSelectAllChecbox" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="selsupplies">';
		print '<input type="hidden" name="tag" value="1">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="seltype" value="'.$seltype.'">';
		print '<input type="hidden" name="fk_projet" value="'.$fk_projet.'">';
		print '<input type="hidden" name="selins" value="'.$selins.'">';
		print '<input type="hidden" name="search" value="'.$search.'">';
	}
	print '<div>'.'<input class="flat" type="radio" name="selins" value="1" '.($selins==1?'checked':'').' onclick="ocultarins();" >'.$langs->trans('All').'</div>';
	print '<div style="clear:left;"></div>';
	print '<div>'.'<input class="flat" type="radio" name="selins" value="2" '.($selins==2?'checked':'').' onclick="mostrarins();">'.$langs->trans('Select').'</div>';
	print '<div style="clear:left;"></div>';
	$display1 = 'none';
	if ($search) $display1 = 'block';
	print '<div id="listins" style="display:'.$display1.';">';
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
	if ($seltype == 1)
	{
		if ($search)
		{
			$sql.=" AND (t.ref LIKE '%".$search."%'";
			$sql.=" OR t.label LIKE '%".$search."%'";
			$sql.= " OR t.description LIKE '%".$search."%'";
			$sql.= ")";
		}
		$filter = array(1=>1);
		$lSearch = false;
		if (!empty($sql)) $lSearch = true;
		$filterstatic = " AND t.entity = ".$conf->entity.' '.$sql;
		$filtergroup = '';
		if ($idscat)
			$filtergroup.= " AND c.fk_categorie IN (".$idscat.")";
		$res = $product->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic,$lView,$filtergroup);
		$nbtotalofrecords = $product->nbtotalofrecords;
		if ($res>0 || $lSearch)
		{
			$lines = $product->lines;
			//$nbtotalofrecords = count($lines);
			$num = count($lines);
		}
	}
	else
	{
		if ($search)
		{
			$sql.=" AND (t.ref LIKE '%".$search."%'";
			$sql.=" OR t.label LIKE '%".$search."%'";
			$sql.= " OR t.description LIKE '%".$search."%'";
			$sql.= ")";
		}
		$filter = array(1=>1);
		$filterstatic = '';
		if ($idscat)
			$filterstatic.= " AND t.fk_categorie IN (".$idscat.")";
		$filterstatic.= " AND t.fk_projet = ".$fk_projet.' '.$sql;
		$lSearch = false;
		if (!empty($sql)) $lSearch = true;
		$res = $projetprod->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic,$lView);
		if ($res>0)
		{
			$lines = $projetprod->lines;
			$nbtotalofrecords = $projetprod->nbtotalofrecords;
			$num = count($lines);
		}
	}
	print '<table class="noborder centpercent">'."\n";
	print '<tr class="liste_titre">';

	print '<td>';
	print '<input type=checkbox onclick="selydestodos(this.form,this.checked);">';
	print '</td>';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print '</tr>';
	$var =true;
	foreach ((array) $lines AS $j => $obj)
	{
		if ($obj)
		{
			$aProductcat[$obj->fk_product] = $obj->fk_categorie;
			//$projetprod->fetch($obj->id);
			$var = !$var;
            // Show here line of result
			print '<tr '.$bc[$var].'>';
			print '<td>'.'<input type="checkbox" name="ins['.($obj->fk_product?$obj->fk_product:$obj->id).']" value=1>'.'</td>';
			print '<td>'.$obj->ref.'</td>';
			print '<td>'.$obj->label.'</td>';
			print '</tr>';
		}
	}
	print '</table>';
	print '</div>';
	print '<div style="clear:left;"></div>';
	if ($tag==1)
	{
		print '<div>';
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Select").'"></center>';
		print '</div>';
		print '</form>';
	}
	print '</div>';

	print '<div class="cajabthr">';
	print '<h2>'.$langs->trans('Selected').'</h2>';
		//recorremos los seleccionados

	if ($tag == 1)
	{
		print '<form method="POST" id="FrmSelectAllChecbox" name="FrmSelectAllChecbox" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="delsupplies">';
		print '<input type="hidden" name="tag" value="1">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="seltype" value="'.$seltype.'">';
		print '<input type="hidden" name="fk_projet" value="'.$fk_projet.'">';
		print '<input type="hidden" name="selins" value="'.$selins.'">';
		print '<input type="hidden" name="search" value="'.$search.'">';
	}

	print '<table class="noborder centpercent">'."\n";
	print '<tr class="liste_titre">';

	print_liste_field_titre($langs->trans('Sel'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print '</tr>';
	$var =true;
	$selectins = unserialize($_SESSION['selectins']);
	foreach ((array) $selectins AS $j =>$value)
	{
		if (is_numeric($j) && $j > 0)
		{
			$res = $product->fetch($j);

			if ($res >0 && $product->id == $j)
			{
				$var = !$var;
            // Show here line of result
				print '<tr '.$bc[$var].'>';
				print '<td>'.'<input type="checkbox" name="ins['.$product->id.']">'.'</td>';
				print '<td>'.$product->ref.'</td>';
				print '<td>'.$product->label.'</td>';
				print '</tr>';
			}
		}
	}
	print '</table>';	
	if ($tag==1)
	{
		print '<div>';
		print '<center><br><input type="submit" class="button" name="delins" value="'.$langs->trans("Delete").'">&nbsp;<input type="submit" class="button" name="saveins" value="'.$langs->trans("Save").'"></center>';
		print '</div>';
		print '</form>';
	}
	print '</div>';
	print '<div class="clear"></div>';
	$_SESSION['aProductcat'] = serialize($aProductcat);
}


?>