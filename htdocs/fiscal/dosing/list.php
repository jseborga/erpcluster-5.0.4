<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---2013 Ramiro Queso ramiro@ubuntu-bo.com---
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
 *   	\file       dev/Vdosing/Vdosing_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2013-09-06 20:51
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
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/fiscal/class/vdosingext.class.php');
dol_include_once('/fiscal/lib/fiscal.lib.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("fiscal@fiscal");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');
$nosearch   = $_POST['nosearch_x'];
if (isset($_POST['search_subsidiary']))
	$_SESSION['search_subsidiary']	= GETPOST('search_subsidiary','alpha');
if (isset($_POST['search_serie']))
	$_SESSION['search_serie']	= GETPOST('search_serie','alpha');
if (isset($_POST['search_lote']))
{
	if (GETPOST('search_lote') == -1)
		unset($_SESSION['search_lote']);
	else
		$_SESSION['search_lote']	= GETPOST('search_lote','int');
}
if (isset($_POST['search_aprob']))
	$_SESSION['search_aprob']	= GETPOST('search_aprob','alpha');
if (isset($_POST['search_numaut']))
	$_SESSION['search_numaut']	= GETPOST('search_numaut','alpha');
if (isset($_POST['search_statut']))
{
	if (GETPOST('search_statut') == -1)
		unset($_SESSION['search_statut']);
	else
		$_SESSION['search_statut']	= GETPOST('search_statut','int');
}


if (isset($nosearch) && $nosearch > 0)
{
	unset($_SESSION['search_subsidiary']);
	unset($_SESSION['search_serie']);
	unset($_SESSION['search_lote']);
	unset($_SESSION['search_aprob']);
	unset($_SESSION['search_numaut']);
	unset($_SESSION['search_statut']);
}
$search_subsidiary = $_SESSION['search_subsidiary'];
$search_serie	= $_SESSION['search_serie'];
$search_lote	= $_SESSION['search_lote'];
$search_aprob	= $_SESSION['search_aprob'];
$search_numaut	= $_SESSION['search_numaut'];
$search_statut	= $_SESSION['search_statut'];

$action = 'list';
// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

$objectstatic = new Vdosingext($db);

$aType = array(1=>$langs->trans('NF'),
	2=>$langs->trans('NFS'),
	3=>$langs->trans('RA'));

$aActive = array(1=>$langs->trans('Yes'),
	2=>$langs->trans('Not'));
$aLote = array(1=>$langs->trans('Manual'),
	2=>$langs->trans('Automatic'));
$aStatut=array(0=>$langs->trans('StatusDraft'),1=>$langs->trans('Validated'));
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Dosing'),'');

$form=new Form($db);


// Put here content of your page


// Example 3 : List of data
if ($action == 'list')
{
	$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
	$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
	if (! $sortfield) $sortfield="s.label";
	if (! $sortorder) $sortorder="ASC";
	$page = $_GET["page"];
	if ($page < 0) $page = 0;
	$limit = $conf->liste_limit;
	$offset = $limit * $page;

	$sql = "SELECT";
	$sql.= " t.rowid AS id,";

	$sql.= " t.entity,";
	$sql.= " t.fk_subsidiaryid,";
	$sql.= " t.series,";
	$sql.= " t.num_ini,";
	$sql.= " t.num_fin,";
	$sql.= " t.num_ult,";
	$sql.= " t.num_aprob,";
	$sql.= " t.type,";
	$sql.= " t.active,";
	$sql.= " t.date_val,";
	$sql.= " t.num_autoriz,";
	$sql.= " t.cod_control,";
	$sql.= " t.lote,";
	$sql.= " t.chave,";
	$sql.= " t.status,";

	$sql.= " s.label, ";
	$sql.= " s.ref as refsubsidiary ";

	$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."subsidiary AS s ON t.fk_subsidiaryid = s.rowid ";
	$sql.= " WHERE t.entity = ".$conf->entity;
	if ($search_subsidiary) $sql.= " AND s.ref LIKE '%".$search_subsidiary."%' ";
	if ($search_serie) $sql.= " AND t.series = '".$search_serie."' ";
	if ($search_lote) $sql.= " AND t.lote = ".$search_lote;
	if ($search_aprob) $sql.= " AND t.num_aprob LIKE '%".$search_aprob."%' ";
	if ($search_numaut) $sql.= " AND t.num_autoriz LIKE '%".$search_numaut."%' ";
	if ($search_statut) $sql.= " AND t.status = ".$search_statut;

	$sql.= " ORDER BY $sortfield $sortorder";
	$sql.= $db->plimit($limit+1, $offset);
	$help_url='EN:Module_Ventas_En|FR:Module_Ventas|ES:M&oacute;dulo_Ventas';


	dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{

		$num = $db->num_rows($resql);

		print_barre_liste($langs->trans("Listedosingbill"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
		//armamos el filtro
		print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

		print '<table class="noborder">'."\n";
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Branch'),$_SERVER['PHP_SELF'],'s.label','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Serie'),$_SERVER['PHP_SELF'],'t.series','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Numini'),$_SERVER['PHP_SELF'],'t.num_ini','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Numfin'),$_SERVER['PHP_SELF'],'t.num_fin','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Numult'),$_SERVER['PHP_SELF'],'t.num_ult','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Numaprob'),$_SERVER['PHP_SELF'],'t.num_aprob','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'t.type','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Active'),$_SERVER['PHP_SELF'],'t.active','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Dateval'),$_SERVER['PHP_SELF'],'t.date_val','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Numautoriz'),$_SERVER['PHP_SELF'],'t.num_autoriz','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Codcontrol'),$_SERVER['PHP_SELF'],'t.cod_control','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Lote'),$_SERVER['PHP_SELF'],'t.lote','',$param,'',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans('Chave'),$_SERVER['PHP_SELF'],'t.chave','',$param,'',$sortfield,$sortorder);

		print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'t.status','',$param,'',$sortfield,$sortorder);
		print '</tr>';

		print '<tr class="liste_titre">';
		print '<td>';
		print '<input type="text" name="search_subsidiary" value="'.$search_subsidiary.'">';
		print '<td>';
		print '<input type="text" name="search_serie" value="'.$search_serie.'" size="2">';
		print '</td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td>';
		print '<input type="text" name="search_aprob" value="'.$search_aprob.'" size="4">';
		print '</td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td>';
		print '<input type="text" name="search_numaut" value="'.$search_numaut.'" size="4">';
		print '</td>';
		print '<td></td>';
		print '<td>';
		print select_lotebill($search_lote,'search_lote','',0,1);

//		print '<input type="text" name="search_lote" value="'.$search_lote.'" size="2">';
		print '</td>';

		print '<td nowrap valign="top" align="right">';
		print $form->selectarray('search_statut',$aStatut,$search_statut,1);
		print '&nbsp;';

		print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '&nbsp;';
		print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
		print '</td>';
		print "</tr>\n";

		$i = 0;
		if ($num)
		{
			$var=True;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$var=!$var;
				$objectstatic->id = $obj->id;
				$objectstatic->ref = $obj->refsubsidiary;
				$objectstatic->status = $obj->status;
				if ($obj)
				{
					print "<tr $bc[$var]>";


					print '<td>';
					print $objectstatic->getNomUrl(1);
					print '</td>';

					print '<td align="left">'.$obj->series.'</td>';
					print '<td align="right">'.$obj->num_ini.'</td>';
					print '<td align="right">'.$obj->num_fin.'</td>';
					print '<td align="right">'.$obj->num_ult.'</td>';
					print '<td align="left">'.$obj->num_aprob.'</td>';
					print '<td align="left">'.$aType[$obj->type].'</td>';
					print '<td align="left">';
					if ($obj->active==1)
						print $langs->trans('Yes');
					else
						print $langs->trans('Not');
					print '</td>';
					print '<td align="left">'.dol_print_date($db->jdate($obj->date_val),'day').'</td>';
					print '<td align="left">'.$obj->num_autoriz.'</td>';
					print '<td align="left">'.$obj->cod_control.'</td>';
					print '<td align="left">'.$aLote[$obj->lote].'</td>';
					//print '<td align="left">'.$obj->chave.'</td>';
					print '<td align="left">';
					print $objectstatic->getLibStatut(5);
					print '</td>';
					print '</tr>';
				}
				$i++;
			}
		}
		print '</table>'."\n";
		print '</form>';
	}
	else
	{
		$error++;
		dol_print_error($db);
	}

	/* **************************************** */
	/*                                          */
	/* Barre d'action                           */
	/*                                          */
	/* **************************************** */

	print "<div class=\"tabsAction\">\n";

	if ($user->rights->fiscal->dosi->creer)
		print "<a class=\"butAction\" href=\"card.php?action=create\">".$langs->trans("New")."</a>";
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("New")."</a>";
	print "</div>";

}

// End of page
llxFooter();
$db->close();
?>
