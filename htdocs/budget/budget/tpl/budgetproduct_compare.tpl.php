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

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

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
$aNroresource = array();
$nVersion=1;
$nItem = 1;
$nResource=1;
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
					if (empty($aNroresource[$linebt->detail]))
					{
						$aNroresource[$linebt->detail] = $nResource;
						$nResource++;
					}

					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['label']= $linebt->label;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['nResource']= $aNroresource[$linebt->detail];
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['fk_unit']= $objTmp->getLabelOfUnit('short');

					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['code_structure']= $linebt->code_structure;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['quant']= $linebt->quant;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['percent_prod']= $linebt->percent_prod;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['amount_noprod']= $linebt->amount_noprod;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['amount']= $linebt->amount;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['performance']= $linebt->performance;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['price_productive']= $linebt->price_productive;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['price_improductive']= $linebt->price_improductive;
					$aBudgettask[$aNroitem[$line->label]][$linebt->detail]['version'][$objBudget->version]['quantity']= $objBudgettaskadd->unit_budget;
					$nprod = price2num($linebt->quant * $linebt->percent_prod * $linebt->amount / 100,$general->decimal_total);
					$nnprod = price2num($linebt->quant * (100-$linebt->percent_prod) * $linebt->amount_noprod / 100,$general->decimal_total);
					$total = $nprod + $nnprod;

					$aBudgetresource[$linebt->detail]['label']= $linebt->detail;
					$aBudgetresource[$linebt->detail]['nResource']= $aNroresource[$linebt->detail];
					$aBudgetresource[$linebt->detail]['fk_unit']= $objTmp->getLabelOfUnit('short');

					$aBudgetresource[$linebt->detail]['code_structure']= $linebt->code_structure;
					$aBudgetresource[$linebt->detail]['group_structure']= $linebt->group_structure;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['quant']= $linebt->quant;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['percent_prod']= $linebt->percent_prod;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['amount_noprod']= $linebt->amount_noprod;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['amount']= $linebt->amount;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['performance']= $linebt->performance;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['price_productive']= $linebt->price_productive;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['price_improductive']= $linebt->price_improductive;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['quantity']= $objBudgettaskadd->unit_budget;
					$nprod = price2num($linebt->quant * $linebt->percent_prod * $linebt->amount / 100,$general->decimal_total);
					$nnprod = price2num($linebt->quant * (100-$linebt->percent_prod) * $linebt->amount_noprod / 100,$general->decimal_total);
					$total = $nprod + $nnprod;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['price_unit']= $total;
					$aBudgetresource[$linebt->detail]['version'][$objBudget->version]['cost_total']= $total*$objBudgettaskadd->unit_budget;


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


$aTituloproduct=array();
$aExcelproduct=array();



print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
print '<td colspan="2" align="right">'.$langs->trans('Version').'</td>';
for ($a=1;$a<=$nVersion; $a++)
{
	print '<td colspan="2" align="center" bgcolor="'.$aColor[$a].'">'.'<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$aVersion[$a].'" title="'.$langs->trans('Goversion').' '.$a.'">'.$a.'</a></td>';
}
print '</tr>'."\n";
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('Label').'</td>';
$aTituloproduct[]=$langs->trans('Label');
print '<td>'.$langs->trans('Fieldfk_unit').'</td>';
$aTituloproduct[]=$langs->trans('Fieldfk_unit');
for ($a=1;$a<=$nVersion; $a++)
{
	print '<td align="right" bgcolor="'.$aColor[$a].'">'.$langs->trans('Unit').'</td>';
	$aTituloproduct[]=$langs->trans('Unit');
	print '<td align="right" bgcolor="'.$aColor[$a].'">'.$langs->trans('Total').'</td>';
	$aTituloproduct[]=$langs->trans('Total');
}
print '</tr>'."\n";



$i=0;
$var=true;
$sumTotal=array();
foreach ($aBudgetresource AS $label => $data)
{
	$var = !$var;
	$nItem=$data['nItem'];
	$nResource=$data['nResource'];
	// Show here line of result
	print '<tr '.$bc[$var].'>';
	print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&search_label='.$nResource.'&action=compare">'.$label.'</a></td>';
	$aExcelproduct[$label]['label']=$label;
	print '<td>'.$data['fk_unit'].'</td>';
	$aExcelproduct[$label]['fk_unit']=$data['fk_unit'];
	for ($a=1; $a<=$nVersion; $a++)
	{
		//print '<td align="right" bgcolor="'.$aColor[$a].'">'.$data['version'][$a]['unit_budget'].'</td>';
		print '<td align="right" bgcolor="'.$aColor[$a].'">'.$data['version'][$a]['price_unit'].'</td>';
		$aExcelproduct[$label][$a]['price_unit']=$data['version'][$a]['price_unit'];
		print '<td align="right" bgcolor="'.$aColor[$a].'">'.price2num($data['version'][$a]['cost_total'],$general->decimal_total).'</td>';
		$aExcelproduct[$label][$a]['cost_total']=$data['version'][$a]['cost_total'];
		$sumTotal[$a]+=$data['version'][$a]['cost_total'];
	}
	print '</tr>';
	if (!empty($search_label) && $search_label==$nItem && $abc)
	{
		$aResource = $aBudgetresource[$search_label];
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
//impresion de totales
print '<tr class="liste_total">';
print '<td colspan="2">'.$langs->trans('Total').'</td>';
for ($a=1; $a<=$nVersion; $a++)
{
	print '<td colspan="2" align="right" bgcolor="'.$aColor[$a].'">'.$sumTotal[$a].'</td>';
}
print '</tr>';

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";




print '<div class="tabsAction">'."\n";
if ($user->rights->budget->budi->com)
{
	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=spreadsheetprod">'.$langs->trans("Spreadsheet").'</a></div>'."\n";


}
print '</div>';

$_SESSION['aExcelproductline'] = serialize($aExcelproduct);
$_SESSION['aTituloproductline'] = serialize($aTituloproduct);
$_SESSION['sumTotalline'] = serialize($sumTotal);
$_SESSION['nVersionline'] = serialize($nVersion);