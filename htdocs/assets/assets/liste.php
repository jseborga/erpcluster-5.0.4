<?php
/* Copyright (C) 2013-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *      \file       htdocs/poai/poai/liste.php
 *      \ingroup    Programa Operativo Anual Individual
 *      \brief      Page liste de POAI
 */

require("../../main.inc.php");
//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsbeen.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/assignment/class/assetsassignmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/assignment/class/assetsassignmentdetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/lib/assets.lib.php");
require_once(DOL_DOCUMENT_ROOT."/projet/class/project.class.php");
if ($conf->orgman->enabled)
{
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/mproperty.class.php");
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/mlocation.class.php");
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/mpropertyuser.class.php");
}
$langs->load("assets");
$langs->load("orgman");

if (!$user->rights->assets->read)
	accessforbidden();

$object  = new Assetsext($db);
$objpro = new Mproperty($db);
$objprouser = new Mpropertyuser($db);
$objloc = new Mlocation($db);
$objass = new Assetsassignmentext($db);
$objassdet = new Assetsassignmentdetext($db);
$projet = new Project($db);
$objCassetsbeen = new Cassetsbeen($db);


$id   = GETPOST('id');
$idot = GETPOST('idot');

$action = GETPOST('action');
$gestion = GETPOST('gestion');
if (isset($_GET['gestion']) || isset($_POST['gestion']))
	$_SESSION['gestion'] = $gestion;
if (isset($_GET['ssearch']))
{
	$lnk = DOL_URL_ROOT.'/mant/jobs/fiche.php?id=';
	//opcion de busqueda para
	//asignacion de activos
	if ($_GET['ssearch'] == 3)
		$lnk = DOL_URL_ROOT.'/assets/assignment/fiche.php?id=';
	$_SESSION['ssearch'][$idot]['id'] = $_GET['idot'];
	$_SESSION['ssearch'][$idot]['link'] = $lnk.$_GET['idot'].'&amp;action='.($_GET['ssearch']==1?'editjobs':($_GET['ssearch']==2?'editregjobs':''));
}
$filter         = GETPOST('filter');
$search_ref    = STRTOUPPER(GETPOST('search_ref'));
$search_ref_ext = STRTOUPPER(GETPOST('search_ref_ext'));
$search_item    = GETPOST('search_item');
$search_group   = GETPOST('search_group');
$search_descrip = GETPOST('search_descrip');
$search_property = GETPOST('search_property');
$search_projet 	= GETPOST('search_projet');
$search_been   = GETPOST('search_been');
if (isset($_GET['search_status']) || isset($_POST['search_status']))
	$_SESSION['asset_search_status'] = GETPOST('search_status');
else
{
	if (!isset($_SESSION['asset_search_status'])) $_SESSION['asset_search_status'] = 99;
}
$search_status = $_SESSION['asset_search_status'];

if (isset($_GET['sda_']) || isset($_POST['sda_']))
	$search_dateadq = (dol_mktime(12, 0, 0, GETPOST('sda_month'),GETPOST('sda_day'),GETPOST('sda_year')));

if (isset($_POST['nosearch_x']))
{
	$search_ref     = '';
	$search_ref_ext = '';
	$search_item     = '';
	$search_group = '';
	$search_descrip   = '';
	$search_dateadq    = '';
	$search_property = '';
	$search_projet = '';
	$search_status = 99;
	$search_been = '99';
	$search_dateadq = '';
}

if ($action == 'sub')
	$filter.= ','.$id;

//action
if (GETPOST('assign') == $langs->trans('Assign'))
{
	//envia en session para la creacion de toda la seleccion realizada
	$_SESSION['aSelass'] = serialize($_POST['selass']);
	header('Location: '.DOL_URL_ROOT.'/assets/assignment/fiche.php?action=create&sel=1');
	exit;
}

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;

$limit = $_SESSION['limitassets'];
if (isset($_GET['limit']) || isset($_POST['limit']))
{
	$_SESSION['limitassets'] = GETPOST('limit');
	$limit = GETPOST('limit');
}
if (empty($limit)) $_SESSION['limitassets'] = $conf->liste_limit;
$limit = $_SESSION['limitassets'];
$offset = $limit * $page;
$filter = '';
$lRegisterUser = true;

$filteruser = '';
$filterasset='';
$aInclude = array();

//armamos assetsbenn
$res = $objCassetsbeen->fetchAll('ASC','label',0,0,array('active'=>1),'AND');
$optionsBeen = '<option value="99">'.$langs->trans('All').'</option>';
if ($res>0)
{
	$lines = $objCassetsbeen->lines;
	foreach ($lines AS $j => $line)
	{
		$aBeen[$line->code] = $line->label;
		$selected = '';
		if ($search_been === $line->code) $selected = ' selected';
		$optionsBeen.= '<option value="'.$line->code.'" '.$selected.'>'.$line->label.'</option>';
	}
}

$aStatus = array(0=>$langs->trans('Pending'),1=>$langs->trans('Tobeaccepted'),2=>$langs->trans('Accepted'),9=>$langs->trans('Free'),-1=>$langs->trans('Disabled'));
$optionsStatus = '<option value="99">'.$langs->trans('All').'</option>';

foreach ($aStatus AS $j => $value)
{
	$selected = '';
	if ($search_status == $j) $selected = ' selected';
	$optionsStatus.= '<option value="'.$j.'" '.$selected.'>'.$value.'</option>';
}
if (!$user->admin)
{
	list($filteruser,$aProperty) = userproperty($user->id);
	$objassdet->getlistassignment_user($user,$filteruser,'0,1');
	if (count($objassdet->aAsset)>0)
		$aInclude = $objassdet->aAsset;
	$filterasset = implode(',',$objassdet->aAsset);
	if (empty($filterasset)) $filterasset = 0;
}
// if (!$user->admin)
//   {
//     if($object->fetch_user($user->id,$gestion))
//       if ($object->id > 0)
// 	$lRegisterUser = false;

//     $filter .= " AND t.fk_user = ".$user->id;
//     $filter .= " AND u.login = '".$user->login."'";
//     $search_user = $user->login;
//   }

if (!empty($id))
	$filter.= " AND t.rowid = ".$id;
if (!empty($search_area))
{
	$filter .= " AND (a.ref LIKE '%".$db->escape($search_area)."%'";
	$filter .= " OR a.label LIKE '%".$db->escape($search_area)."%' ) ";
}
//filtro search
if (!empty($search_ref))
	$filter .= " AND t.ref LIKE '%".$db->escape($search_ref)."%'";
if (!empty($search_ref_ext))
	$filter .= " AND t.ref_ext LIKE '%".$db->escape($search_ref_ext)."%'";
if (!empty($search_item))
	$filter .= " AND t.item LIKE '%".$db->escape($search_item)."%'";
if (!empty($search_group))
	$filter .= " AND g.label LIKE '%".$db->escape($search_group)."%'";
if (!empty($search_descrip))
	$filter .= " AND t.descrip LIKE '%".$db->escape($search_descrip)."%'";

if (!empty($search_been) && $search_been != 99)
	$filter .= " AND b.code LIKE '%".$db->escape($search_been)."%'";
if (!empty($search_dateadq))
{
	//$filter .= " AND t.date_adq = '".$db->escape($db->idate($search_dateadq))."'";
	$month = $db->escape(date('m',$search_dateadq))+0;
	$day = $db->escape(date('d',$search_dateadq))+0;
	$year = $db->escape(date('Y',$search_dateadq))+0;
	$filter .= " AND month(t.date_adq) LIKE '%".$$month."%'";
	$filter .= " AND day(t.date_adq) LIKE '%".$day."%'";
	$filter .= " AND year(t.date_adq) LIKE '%".$year."%'";
}
if ($search_status != 99)
	$filter .= " AND t.statut = ".$search_status;
if (GETPOST('ssearch')==3) $filter.= " AND t.statut = 9 ";
$sql = "SELECT";
$sql.= " t.rowid AS id,";

$sql.= " t.entity,";
$sql.= " t.type_group,";
$sql.= " t.type_patrim,";
$sql.= " t.ref,";
$sql.= " t.ref_ext,";
$sql.= " t.item_asset,";
$sql.= " t.date_adq,";
$sql.= " t.quant,";
$sql.= " t.date_baja,";
$sql.= " t.descrip,";
$sql.= " t.number_plaque,";
$sql.= " t.fk_asset_sup,";
$sql.= " t.code_bar,";
$sql.= " t.fk_method_dep,";
$sql.= " t.type_property,";
$sql.= " t.code_bim,";
$sql.= " t.fk_product,";
$sql.= " t.fk_user_create,";
$sql.= " t.date_create,";
$sql.= " t.tms,";
$sql.= " t.been,";
$sql.= " t.statut, ";
$sql.= " g.code,";
$sql.= " g.label AS grouplabel ";
$sql.= " , b.label AS beenlabel ";

$sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_assets_group AS g ON t.type_group = g.code ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_assets_been AS b ON t.been = b.code AND t.entity = b.entity";
$sql.= " WHERE t.entity = ".$conf->entity;
if ($search_projet)
{
	$sql1 = "SELECT fk_asset FROM ".MAIN_DB_PREFIX."assets_assignment AS a ";
	$sql1.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment_det AS ad ON ad.fk_asset_assignment = a.rowid ";
	$sql1.= " INNER JOIN ".MAIN_DB_PREFIX."projet AS p ON a.fk_projet = p.rowid ";
	$sql1.= " WHERE p.ref like '%".$search_projet."%' OR p.title like '%".$search_projet."%'";
	//vamos a buscar por el proyecto
	$sql.= " AND t.rowid IN (".$sql1.")";
}

if ($filter) $sql.= $filter;
if (!$user->admin)
{
	if (!$user->rights->assets->ass->lall)
		$sql.= " AND t.rowid IN (".$filterasset.")";
}

$sql.= " ORDER BY $sortfield $sortorder";

$result = $db->query($sql);
if ($result)
{
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
$result = $db->query($sql);

$form=new Form($db);

if ($result)
{
 	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
	//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js');
	llxHeader("",$langs->trans("Listeassets"),$help_url,'','','',$aArrjs);
	$params='';
	if ($search_projet) $params.= '&search_projet='.$search_projet;
	if ($search_ref) $params.= '&search_ref='.$search_ref;
	if ($search_ref_ext) $params.= '&search_ref_ext='.$search_ref_ext;
	if ($search_status != 99) $params.= '&search_status='.$search_status;
	print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input type="hidden" name="idot" value="'.$idot.'">';

	print_barre_liste($langs->trans("Listeassets"), $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Code"),"liste.php", "t.ref","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Fieldref_ext"),"liste.php", "t.ref_ext","",$params,"",$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Item"),"liste.php", "t.item_asset","","","");
	if ($conf->browser->layout != 'phone')
		print_liste_field_titre($langs->trans("Group"),"liste.php", "g.label","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Detail"),"liste.php", "t.descrip","",$params,"",$sortfield,$sortorder);
	if ($user->admin && $conf->browser->layout != 'phone')
	{
		print_liste_field_titre($langs->trans("Dateadq"),"liste.php", "t.date_adq","",$params,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Dateassig"),"liste.php", "","",$params,"",$sortfield,$sortorder);
	}
	print_liste_field_titre($langs->trans("Property"),"liste.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Project"),"liste.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"liste.php", "","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Been"),"liste.php", "b.code","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Statut"),"liste.php", "t.statut","",$params,'align="center"',$sortfield,$sortorder);
	print "</tr>\n";

	//filtro
	if (empty($search_gestion)) $search_gestion = $gestion;
	print '<tr class="liste_titre">';
	print '<td><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="5"></td>';
	print '<td><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="5"></td>';
	//print '<td><input type="text" class="flat" name="search_item" value="'.$search_item.'" size="4"></td>';
	if ($conf->browser->layout != 'phone')
		print '<td><input type="text" class="flat" name="search_group" value="'.$search_group.'" size="10"></td>';
	print '<td><input type="text" class="flat" name="search_descrip" value="'.$search_descrip.'" size="10"></td>';
	if ($user->admin && $conf->browser->layout != 'phone')
	{
		print '<td>';
		$form->select_date($search_dateadq,'sda_','','',(empty($search_dateadq)?1:0),"date",1,0,0);
		print '</td>';
		print '<td></td>';
	}
	print '<td><input type="text" class="flat" name="search_property" value="'.$search_property.'" size="10"></td>';
	print '<td><input type="text" class="flat" name="search_projet" value="'.$search_projet.'" size="10"></td>';
	print '<td>'.'<input type="checkbox" onclick="selydestodos(this.form,this.checked);">'.'</td>';
	print '<td>';
	print '<select name="search_been">'.$optionsBeen.'</select>';
	print '</td>';
	print '<td align="center" nowrap>';
	print '<select name="search_status">'.$optionsStatus.'</select>';
	//print '<input type="text" class="flat" name="search_status" value="'.$search_status.'" size="8">';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';

	print '</td>';
	print '</tr>';
	$lAssign = false;
	if ($num)
	{
		$var=True;
		$sel = 0;
		while ($i < min($num,$limit))
		{
			$lView = true;
			$obj = $db->fetch_object($result);
			$var=!$var;
			$object->id = $obj->id;
			$object->ref = $obj->ref;
			$object->label = $obj->descrip;
			$object->statut = $obj->statut;
			$date_assig = '';
			$objassdet->initAsSpecimen();
			$objass->initAsSpecimen();
			$objpro->initAsSpecimen();
			$projet->initAsSpecimen();
			//verificamos permisos de property y status
			//buscamos si esta asigando
			$resadd = $objassdet->fetch_ult($obj->id,'0,1');
			if ($resadd>0 && $objassdet->fk_asset == $obj->id)
			{
				$objass->fetch($objassdet->fk_asset_assignment);
				$objpro->fetch($objass->fk_property);
				if ($objass->fk_projet>0)
					$projet->fetch($objass->fk_projet);
				else
				{
					$projet->initAsSpecimen();
					$projet->title='';
					$projet->ref = '';
				}
			}
			else
			{
				$objpro->initAsSpecimen();
				$projet->initAsSpecimen();
				$projet->title='';
				$projet->ref = '';
			}
			if (empty($resadd))
				$statut = 9;
			else
			{
				if (empty($objass->statut)) $statut = 0;
				else $statut = $objass->statut;
			}
			if (empty($obj->statut)) $statut = 0;

			if ($statut > 0) $lAssign = true;

			$status = $object->LibStatut($object->statut,4);
			if (!empty($search_property))
			{
				$cadena = $objpro->label.' '.$objpro->ref;
				$pos = stripos($cadena,$search_property);
				if ($pos === false) $lView = false;
				else $lView = true;
			}
			if (!empty($search_projet))
			{
				$cadena = $projet->title.' '.$projet->ref;
				$pos = stripos($cadena,$search_projet);
				if ($pos === false) $lView = false;
				else $lView = true;
			}
			if (!empty($search_status))
			{
				//$cadena = $status;
				//$pos = stripos($cadena,$search_status);
				//if ($pos === false) $lView = false;
				//else $lView = true;
			}
			if ($lView)
			{
				print "<tr $bc[$var]>";
				print '<td nowrap>';
				if (!empty($_SESSION['ssearch'][$idot]['id']))
					print '<a href="'.$_SESSION['ssearch'][$idot]['link'].'&amp;fk_equipment='.$obj->id.'">'.img_picto($langs->trans("Assets"),DOL_URL_ROOT.'/assets/img/af','',1).' '.$obj->ref.'</a>';
				else
				{
					if ($user->rights->assets->alloc->apr && $aProperty[$objass->fk_property] && $objass->statut == 1)
					{
						print $objass->getNomUrl(1);
					}
					else
						print $object->getNomUrl(1);
				}
				print '</td>';
				print '<td nowrap>';
				$object->ref = $obj->ref_ext;
				if (GETPOST('ssearch') == 3)
					print $obj->ref_ext;
				else
					print $object->getNomUrl();
				print '</td>';
				//print '<td>'.$obj->item_asset.'</td>';
				if ($conf->browser->layout != 'phone')
					print '<td>'.$obj->grouplabel.'</td>';
				print '<td>'.$obj->descrip.'</td>';
				if ($user->admin && $conf->browser->layout != 'phone')
					print '<td>'.dol_print_date($db->jdate($obj->date_adq),'day').'</td>';

				//buscamos si esta asigando
				$resadd = $objassdet->fetch_ult($obj->id,'0,1');
				if ($resadd && $objassdet->fk_asset == $obj->id)
				{
					if ($conf->browser->layout != 'phone')
						print '<td>'.dol_print_date($objassdet->date_assignment,'day').'</td>';
					if ($objpro->id>0)
					{
						print '<td>'.$objpro->getNomUrl(1).'</td>';
					}
					else
						print '<td></td>';
				}
				else
				{
					print '<td></td>';
					if ($conf->browser->layout != 'phone')
						print '<td></td>';
				}

				print '<td>'.($projet->id>0?$projet->getNomUrl(1):'').'</td>';

				print '<td>';
				if (($user->rights->assets->ass->lall && $user->rights->assets->alloc->crear) || ($user->rights->assets->alloc->crear && $aProperty[$objass->fk_property] && $objass->statut == 2))
				{
					if ($statut == 9)
					{
						$sel++;
						print '<input type="checkbox" name="selass['.$obj->id.']">';
					}
				}
				print '</td>';
				print '<td>'.$obj->beenlabel.'</td>';
				print '<td align="center">'.$status.'</td>';

				print "</tr>\n";
			}
			$i++;
		}
	}
	print '</table>';
	if (($user->rights->assets->ass->lall && $user->rights->assets->alloc->crear) || ($user->rights->assets->alloc->crear && $aProperty[$objass->fk_property] && $sel>0))
	{
		if ($lAssign)
		{
			if (GETPOST('ssearch') != 3)
				print '<center><br><input type="submit" name="assign" class="button" value="'.$langs->trans("Assign").'"></center>';
		}
	}
	print '</form>';
	print '</div>';
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();


?>
