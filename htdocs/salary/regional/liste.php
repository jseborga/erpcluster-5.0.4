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
 *   	\file       dev/Pregional/Pregional_page.php
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
dol_include_once('/salary/class/pregionalext.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("salary@salary");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');
$action = 'list';
// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
$object = new Pregionalext($db);

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Managementsalary'),'');

$form=new Form($db);


// Put here content of your page


// Example 3 : List of data
if ($action == 'list')
{
	$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
	$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
	if (! $sortfield) $sortfield="t.ref";
	if (! $sortorder) $sortorder="ASC";
	$page = $_GET["page"];
	if ($page < 0) $page = 0;
	$limit = $conf->liste_limit;
	$offset = $limit * $page;

	$sql = "SELECT";
	$sql.= " t.rowid AS id,";

	$sql.= " t.entity,";
	$sql.= " t.ref,";
	$sql.= " t.label,";
	$sql.= " t.state";


	$sql.= " FROM ".MAIN_DB_PREFIX."p_regional as t";
	$sql.= " WHERE entity = $conf->entity ";
	$sql.= " ORDER BY $sortfield $sortorder";
	$sql.= $db->plimit($limit+1, $offset);

	$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';

	print_barre_liste($langs->trans("Liste regional"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder">'."\n";
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'t.label','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'t.state','',$param,'align="right"',$sortfield,$sortorder);
	print '</tr>';

	dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$var=True;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$var=!$var;
				$object->status = $obj->state;
				$object->id = $obj->id;
				$object->ref = $obj->ref;
				$object->label = $obj->label;
				if ($obj)
				{
					print "<tr $bc[$var]>";
					print '<td>';
					print $object->getNomUrl(1);
					print '</td>';
					//print '<a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Ref"),'globe').' '.$obj->ref.'</a></td>';
					print '<td align="left">'.$obj->label.'</td>';
					print '<td align="left">'.$object->getLibStatut(6).'</td>';
					print '</tr>';
				}
				$i++;
			}
		}
	}
	else
	{
		$error++;
		dol_print_error($db);
	}

	print '</table>'."\n";
	/* **************************************** */
	/*                                          */
	/* Barre d'action                           */
	/*                                          */
	/* **************************************** */

	print "<div class=\"tabsAction\">\n";

	if ($user->rights->salary->region->creer)
		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	print "</div>";

}



// End of page
llxFooter();
$db->close();
?>
