<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2017      Nicolas ZABOURI	<info@inovea-conseil.com>
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
 *   	\file       budget/budgettask_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-18 15:01
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


$search_all=trim(GETPOST("sall"));

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_fk_task=GETPOST('search_fk_task','int');
$search_fk_task_parent=GETPOST('search_fk_task_parent','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_duration_effective=GETPOST('search_duration_effective','alpha');
$search_planned_workload=GETPOST('search_planned_workload','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_formula=GETPOST('search_formula','alpha');
$search_manual_performance=GETPOST('search_manual_performance','int');
$search_progress=GETPOST('search_progress','int');
$search_priority=GETPOST('search_priority','int');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_rang=GETPOST('search_rang','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');
$search_label = GETPOST('search_label');

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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetbudgettasktpllist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('budgetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('budget');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'align'=>'align="left"', 'checked'=>1),
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_budget'=>array('label'=>$langs->trans("Fieldfk_budget"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_task'=>array('label'=>$langs->trans("Fieldfk_task"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_task_parent'=>array('label'=>$langs->trans("Fieldfk_task_parent"), 'align'=>'align="left"', 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'align'=>'align="left"', 'checked'=>1),
	't.description'=>array('label'=>$langs->trans("Fielddescription"), 'align'=>'align="left"', 'checked'=>1),
	't.duration_effective'=>array('label'=>$langs->trans("Fieldduration_effective"), 'align'=>'align="left"', 'checked'=>1),
	't.planned_workload'=>array('label'=>$langs->trans("Fieldplanned_workload"), 'align'=>'align="left"', 'checked'=>1),
	't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'align'=>'align="left"', 'checked'=>1),
	't.formula'=>array('label'=>$langs->trans("Fieldformula"), 'align'=>'align="left"', 'checked'=>1),
	't.manual_performance'=>array('label'=>$langs->trans("Fieldmanual_performance"), 'align'=>'align="left"', 'checked'=>1),
	't.progress'=>array('label'=>$langs->trans("Fieldprogress"), 'align'=>'align="left"', 'checked'=>1),
	't.priority'=>array('label'=>$langs->trans("Fieldpriority"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_creat'=>array('label'=>$langs->trans("Fieldfk_user_creat"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_valid'=>array('label'=>$langs->trans("Fieldfk_user_valid"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_statut'=>array('label'=>$langs->trans("Fieldfk_statut"), 'align'=>'align="left"', 'checked'=>1),
	't.note_private'=>array('label'=>$langs->trans("Fieldnote_private"), 'align'=>'align="left"', 'checked'=>1),
	't.note_public'=>array('label'=>$langs->trans("Fieldnote_public"), 'align'=>'align="left"', 'checked'=>1),
	't.rang'=>array('label'=>$langs->trans("Fieldrang"), 'align'=>'align="left"', 'checked'=>1),
	't.model_pdf'=>array('label'=>$langs->trans("Fieldmodel_pdf"), 'align'=>'align="left"', 'checked'=>1),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
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
		$search_fk_budget='';
		$search_fk_task='';
		$search_fk_task_parent='';
		$search_label='';
		$search_description='';
		$search_duration_effective='';
		$search_planned_workload='';
		$search_amount='';
		$search_formula='';
		$search_manual_performance='';
		$search_progress='';
		$search_priority='';
		$search_fk_user_creat='';
		$search_fk_user_valid='';
		$search_fk_statut='';
		$search_note_private='';
		$search_note_public='';
		$search_rang='';
		$search_model_pdf='';


		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

}

$objBudget = new Budgetext($db);
//vamos a procesar segun las versiones

$filter=" AND t.ref = '".$object->ref."'";
$res = $objBudget->fetchAll('asc','t.version',0,0,array(),'AND',$filter);
$aVersion = array();
$ids = '';
if ($res > 0)
{
	$lines = $objBudget->lines;
	foreach ($lines AS $j => $line)
	{
		$aVersion[$line->version] = $line->id;
		$aIds[$line->id] = $line->id;
	}
	$ids = implode(',',$aIds);
}
//vamos a formar un array unico de los items utilizados
$filter = " AND t.fk_budget IN (".$ids.") ";
$res = $objBudgettask->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
$aItem = array();
$aNroitem = array();
$nVersion=1;
$nItem = 1;
if ($res > 0)
{
	$lines = $objBudgettask->lines;
	foreach ($lines AS $j => $line)
	{
		if (empty($aNroitem[$line->label]))
		{
			$aNroitem[$line->label] = $nItem;
			$nItem++;
		}
		$objBudget->fetch($line->fk_budget);
		if (empty($nVersion)) $nVersion = $objBudget->version;
		else
		{
			if ($nVersion <= $objBudget->version)
				$nVersion = $objBudget->version;
		}
		$objBudgettaskadd->fetch(0,$line->id);
		if (!$objBudgettaskadd->c_grupo)
		{
			$objTmp = new BudgettaskaddLineext($db);
			$objTmp->fk_unit = $objBudgettaskadd->fk_unit;
			$aItem[$line->label]['label'] = $line->label;
			$aItem[$line->label]['id'] = $line->id;
			$aItem[$line->label]['nItem'] = $aNroitem[$line->label];
			$aItem[$line->label]['fk_task'] = $line->fk_task;
			$aItem[$line->label]['fk_unit'] = $objTmp->getLabelOfUnit('short');
			$aItem[$line->label]['c_grupo'] = $objBudgettaskadd->c_grupo;
			$aItem[$line->label]['version'][$objBudget->version]['unit_budget'] = $objBudgettaskadd->unit_budget;
			$aItem[$line->label]['version'][$objBudget->version]['unit_amount'] = $objBudgettaskadd->unit_amount;
			$aItem[$line->label]['version'][$objBudget->version]['total_amount'] = $objBudgettaskadd->total_amount;

			//vamos a recuperar sus insumos
			$filter = " AND t.fk_budget_task = ".$line->id;
			$resbt = $objBudgettaskresource->fetchAll('ASC','t.code_structure,t.ref',0,0,array(),'AND',$filter);
			if ($resbt>0)
			{
				$linesbt = $objBudgettaskresource->lines;
				foreach ($linesbt AS $k => $linebt)
				{
					$objTmp = new BudgettaskresourceLineext($db);
					$objTmp->fk_unit = $linebt->fk_unit;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['label']= $linebt->label;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['fk_unit']= $objTmp->getLabelOfUnit('short');

					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['code_structure']= $linebt->code_structure;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['quant']= $linebt->quant;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['percent_prod']= $linebt->percent_prod;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['amount_noprod']= $linebt->amount_noprod;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['amount']= $linebt->amount;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['performance']= $linebt->performance;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['price_productive']= $linebt->price_productive;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['price_improductive']= $linebt->price_improductive;
				}
			}
		}
	}
}
for ($a = 1; $a <= $nVersion; $a++)
{
	$a1 = $a*123;
	$a2 = $a1*778899;
	$time = $a * $a1*$a2;
	$aColor[$a] = '#'.substr(md5($time),0,6);
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
if ($user->rights->budget->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
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


$moreforfilter = '';


// yemer
$aTitulocompare=array();
$aExcelcompare=array();
///

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";
// Fields title
print '<tr class="liste_titre">';
print '<td colspan="2" align="right">'.$langs->trans('Version').'</td>';
for ($a=1;$a<=$nVersion; $a++)
{
	print '<td colspan="3" align="center" bgcolor="'.$aColor[$a].'">'.'<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$aVersion[$a].'" title="'.$langs->trans('Goversion').' '.$a.'">'.$a.'</a></td>';
}
print '</tr>'."\n";
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('Label').'</td>';
$aTitulocompare[]=$langs->trans('Label');
print '<td>'.$langs->trans('Fieldfk_unit').'</td>';
$aTitulocompare[]=$langs->trans('Fieldfk_unit');
for ($a=1;$a<=$nVersion; $a++)
{
	print '<td align="right" bgcolor="'.$aColor[$a].'">'.$langs->trans('Quant').'</td>';
	$aTitulocompare[]=$langs->trans('Quant');
	print '<td align="right" bgcolor="'.$aColor[$a].'">'.$langs->trans('Unit').'</td>';
	$aTitulocompare[]=$langs->trans('Unit');
	print '<td align="right" bgcolor="'.$aColor[$a].'">'.$langs->trans('Total').'</td>';
	$aTitulocompare[]=$langs->trans('Total');
}
print '</tr>'."\n";

//echo'<pre>';
//print_r($aItem);
//echo'</pre>';
//exit;

$i=0;
$cont=0;
$var=true;
$sumTotal=array();
foreach ($aItem AS $label => $data)
{
	$var = !$var;
	$nItem=$data['nItem'];
	// Show here line of result
	print '<tr '.$bc[$var].'>';
	print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&search_label='.$nItem.'&action=compareitem">'.$label.'</a></td>';

	$aExcelcompare[$label]['label']=$label;
	print '<td>'.$data['fk_unit'].'</td>';
	$aExcelcompare[$label]['fk_unit']=$data['fk_unit'];
	for ($a=1; $a<=$nVersion; $a++)
	{
		print '<td align="right" bgcolor="'.$aColor[$a].'">'.$data['version'][$a]['unit_budget'].'</td>';
		$aExcelcompare[$label][$a]['unit_budget']=$data['version'][$a]['unit_budget'];
		print '<td align="right" bgcolor="'.$aColor[$a].'">'.$data['version'][$a]['unit_amount'].'</td>';
		$aExcelcompare[$label][$a]['unit_amount']=$data['version'][$a]['unit_amount'];
		print '<td align="right" bgcolor="'.$aColor[$a].'">'.price2num($data['version'][$a]['total_amount'],$general->decimal_total).'</td>';
		$aExcelcompare[$label][$a]['total_amount']=$data['version'][$a]['total_amount'];
		$sumTotal[$a]+=$data['version'][$a]['total_amount'];

	}
	print '</tr>';
	if (!empty($search_label) && $search_label==$nItem)
	{
		$aResource = $aBudgettask[$search_label];
		if (is_array($aResource) && count($aResource)>0)
		{
			foreach ($aResource AS $labelr => $row)
			{
				print '<tr '.$bc[$var].'>';
				print '<td><i>'.'&nbsp;&nbsp;&nbsp;'.$labelr.'</i></td>';
				print '<td><i>'.$row['fk_unit'].'</i></td>';
				for ($a=1; $a<=$nVersion; $a++)
				{
					$nprod = price2num($row['version'][$a]['quant'] * $row['version'][$a]['percent_prod'] * $row['version'][$a]['amount'] / 100,$general->decimal_total);
					$nnprod = price2num($row['version'][$a]['quant'] * (100-$row['version'][$a]['percent_prod']) * $lineb->$row['version'][$a]['amount_noprod'] / 100,$general->decimal_total);
					$ntotal = $nprod + $nnprod;

					print '<td align="right" bgcolor="'.$aColor[$a].'">'.$row['version'][$a]['quant'].'</td>';
					print '<td align="right" bgcolor="'.$aColor[$a].'">'.$row['version'][$a]['amount'].'</td>';
					print '<td align="right" bgcolor="'.$aColor[$a].'">'.$ntotal.'</td>';
							//$sumTotal[$a]+=$data['version'][$a]['total_amount'];
				}
				print '</tr>';
			}
		}
	}


}
//echo'<pre>';
//print_r($aExcelcompare);
//echo'</pre>';
//exit;



//impresion de totales
print '<tr class="liste_total">';
print '<td colspan="2">'.$langs->trans('Total').'</td>';
for ($a=1; $a<=$nVersion; $a++)
{
	print '<td colspan="3" align="right" bgcolor="'.$aColor[$a].'">'.$sumTotal[$a].'</td>';
}
print '</tr>';


$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
 // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";


////yemer//////

print '<div class="tabsAction">'."\n";
if ($user->rights->budget->budi->com)
{
	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=spreadsheet">'.$langs->trans("Spreadsheet").'</a></div>'."\n";


}
print '</div>';

$_SESSION['aExcelcomparedet'] = serialize($aExcelcompare);
$_SESSION['aTitulocomparedet'] = serialize($aTitulocompare);
$_SESSION['sumTotaldet'] = serialize($sumTotal);
$_SESSION['nVersiondet'] = serialize($nVersion);



////////////
