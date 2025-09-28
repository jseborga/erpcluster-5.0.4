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

require_once(DOL_DOCUMENT_ROOT."/assets/assignment/class/assetsassignmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/assignment/class/assetsassignmentdetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/lib/assets.lib.php");
if ($conf->projet->enabled)
	require_once(DOL_DOCUMENT_ROOT."/projet/class/project.class.php");
if ($conf->orgman->enabled)
{
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/mproperty.class.php");
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/mpropertyuser.class.php");
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/mlocation.class.php");
}
$langs->load("assets@assets");

if (!$user->rights->assets->read)
	accessforbidden();

$object  = new Assetsassignmentext($db);
$objass = new Assetsext($db);
$objpro = new Mproperty($db);
$objprouser = new Mpropertyuser($db);
$objloc = new Mlocation($db);
$objAdherent = new Adherent($db);

if ($conf->projet->enabled) $projet = new Project($db);
$objuser = new User($db);

$id   = GETPOST('id');
$action = GETPOST('action');

$filter      = GETPOST('filter');

if (isset($_POST['search_ref']))
	$_SESSION['actsearch_ref'] = $_POST['search_ref'];
if (isset($_POST['search_date']))
	$_SESSION['actsearch_datea'] = dol_mktime($_POST['sehour'],$_POST['semin'],0,$_POST['semonth'],$_POST['seday'],$_POST['seyear'],'user');
if (isset($_POST['search_adh']))
	$_SESSION['actsearch_adh'] = $_POST['search_adh'];
if (isset($_POST['search_det']))
	$_SESSION['actsearch_det'] = $_POST['search_det'];
if (isset($_POST['search_pro']))
	$_SESSION['actsearch_pro'] = $_POST['search_pro'];
if (isset($_POST['search_loc']))
	$_SESSION['actsearch_loc'] = $_POST['search_loc'];
if (isset($_POST['search_projet']))
	$_SESSION['actsearch_projet'] = $_POST['search_projet'];

if (isset($_POST['nosearch_x']))
{
	$_SESSION['actsearch_ref'] = '';
	$_SESSION['actsearch_datea'] = '';
	$_SESSION['actsearch_adh'] = '';
	$_SESSION['actsearch_det'] = '';
	$_SESSION['actsearch_pro'] = '';
	$_SESSION['actsearch_loc'] = '';
	$_SESSION['actsearch_projet'] = '';
}

$search_ref  = $_SESSION['actsearch_ref'];
$search_date = $_SESSION['actsearch_datea'];
$search_adh  = $_SESSION['actsearch_adh'];
$search_det  = $_SESSION['actsearch_det'];
$search_pro  = $_SESSION['actsearch_pro'];
$search_loc  = $_SESSION['actsearch_loc'];
$search_projet = $_SESSION['actsearch_projet'];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="DESC";

$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
$filter = '';
$lRegisterUser = true;

//filtramos de acuerdo al tipo de usaurio y permisos por inmuebles
$filteruser = '';
if (!$user->admin) list($filteruser,$aProperty) = userproperty($user->id);


// if (!$user->admin)
//   {
//     if($object->fetch_user($user->id,$gestion))
//       if ($object->id > 0)
// 	$lRegisterUser = false;

//     $filter .= " AND t.fk_user = ".$user->id;
//     $filter .= " AND u.login = '".$user->login."'";
//     $search_user = $user->login;
//   }
// if (!empty($id))
//   $filter.= " AND t.rowid = ".$id;
if (!empty($search_ref))
	$filter .= " AND t.ref LIKE '%".$db->escape($search_ref)."%'";
if (!empty($search_adh))
{
	$filter.= " AND (a.lastname LIKE '%".$db->escape($search_adh)."%'";
	$filter.= " OR a.firstname LIKE '%".$db->escape($search_adh)."%')";
}
if (!empty($search_det))
	$filter .= " AND t.detail LIKE '%".$db->escape($search_det)."%'";
if (!empty($search_pro))
	$filter .= " AND p.ref LIKE '%".$db->escape($search_pro)."%'";
if (!empty($search_loc))
	$filter .= " AND l.detail LIKE '%".$db->escape($search_loc)."%'";
if (!empty($search_projet))
{
	$filter .= " AND (pr.title LIKE '%".$db->escape($search_projet)."%'";
	$filter .= " OR pr.ref LIKE '%".$db->escape($search_projet)."%')";
}
$sql = "SELECT";
$sql.= " t.rowid AS id,";

$sql.= " t.entity,";
$sql.= " t.ref,";
$sql.= " t.detail,";
$sql.= " t.date_assignment,";
$sql.= " t.fk_projet,";
$sql.= " t.fk_user,";
$sql.= " t.date_create,";
$sql.= " t.fk_user_create,";
$sql.= " t.tms,";
$sql.= " t.status,";
$sql.= " a.firstname, a.lastname,";
$sql.= " p.ref AS property,";
$sql.= " l.detail AS location, ";
$sql.= " pr.title AS title ";

$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment as t";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."adherent AS a ON t.fk_user = a.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_property AS p ON t.fk_property = p.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_location AS l ON t.fk_location = l.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet AS pr ON t.fk_projet = pr.rowid";

$sql.= " WHERE t.entity = ".$conf->entity;
if ($filter)
	$sql.= $filter;
$sqluser = '';

if (!$user->rights->assets->alloc->lall && !$user->admin)
{
	//$sql.= " AND t.fk_property IN (".$filteruser.")";
	//$sql.= " OR t.fk_user = ".$user->id;
	//$sql.= " AND t.fk_property IN (".$filteruser.")";
	$sqluser.= " AND ( (t.type_assignment = 1 AND t.fk_user_from = ".$user->id.") OR ";
	$sqluser.= " (t.type_assignment = 0 AND t.fk_user_to = ".$user->id.") OR ";
	if (!$filteruser)
		$sqluser.= " ( t.fk_user = ".$user->id.")";
	else
	{
		$sqluser.= " ( t.fk_property IN (".$filteruser.")";
		$sqluser.= " OR t.fk_user = ".$user->id.")";
	}
	$sqluser.= ")";
	$sql.= $sqluser;
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);

$form=new Form($db);

if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';
	//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js');
	llxHeader("",$langs->trans("Listefixedassets"),$help_url,'','','',$aArrjs);

	print_barre_liste($langs->trans("Listefixedassets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "t.ref_ext","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Date"),"liste.php", "t.date_assignment","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Project"),"liste.php", "pr.descrip","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("User"),"liste.php", "t.fk_adherent","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Detail"),"liste.php", "t.detail","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Property"),"liste.php", "t.fk_property","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Location"),"liste.php", "t.fk_location","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"", "","","","",$sortfield,$sortorder);
	print "</tr>\n";

	print '<tr class="liste_titre">';
	print '<td><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	print '<td>';
	//print $form->select_date($search_date,'se');
	print '</td>';
	print '<td><input type="text" class="flat" name="search_projet" value="'.$search_projet.'" size="8"></td>';
	print '<td><input type="text" class="flat" name="search_adh" value="'.$search_adh.'" size="15"></td>';
	print '<td><input type="text" class="flat" name="search_det" value="'.$search_det.'" size="15"></td>';
	print '<td><input type="text" class="flat" name="search_pro" value="'.$search_pro.'" size="15"></td>';
	print '<td><input type="text" class="flat" name="search_loc" value="'.$search_loc.'" size="15"></td>';
	print '<td align="right" nowrap>';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';

	print '</td>';
	print '</tr>';
	if ($num) {
		$var=True;
		while ($i < min($num,$limit))
		{
			$obj = $db->fetch_object($result);
			$object->id = $obj->id;
			$object->status = $obj->status;
			$var=!$var;

			print "<tr $bc[$var]>";
			print '<td nowrap>';
			print '<a href="fiche.php?id='.$obj->id.'">'.img_picto($langs->trans("Assets"),DOL_URL_ROOT.'/assets/img/af','',1).' '.$obj->ref.'</a>';
			print '</td>';
			print '<td>'.dol_print_date($obj->date_assignment,'day').'</td>';
			print '<td>';
			if ($obj->fk_projet > 0)
			{
				$projet->fetch($obj->fk_projet);
				print $projet->getNomUrl(1,'',1);
			}
			else
				print '';
			print '</td>';
			$objAdherent->fetch($obj->fk_user);
	 // print '<td>'.$obj->lastname.' '.$obj->firstname.'</td>';
			print '<td>'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
			print '<td>'.$obj->detail.'</td>';
			print '<td>'.$obj->property.'</td>';
			print '<td>'.$obj->location.'</td>';
			print '<td>';
			print $object->Libstatut($obj->status,2);
			print '</td>';

			print "</tr>\n";
			$i++;
		}
	}

	print '</div>';
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();


?>
