<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       dev/Contabvisions/Contabvision_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2013-12-13 02:58
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');	// If there is no menu to show
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');	// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');		// If this page is public (can be called outside logged session)

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
dol_include_once('/contab/class/contabvisionext.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load('contab@contab');

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
if (!$user->rights->contab->vision->read)
	accessforbidden();




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Managementaccounting'),'');

$form=new Form($db);
$object = new Contabvisionext($db);

$action = 'list';

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="t.ref,t.sequence";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

// Example 3 : List of data
if ($action == 'list')
{
	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql.= " t.entity,";
	$sql.= " t.ref,";
	$sql.= " t.sequence,";
	$sql.= " t.account,";
	$sql.= " t.account_sup,";
	$sql.= " t.detail_managment,";
	$sql.= " t.cta_normal,";
	$sql.= " t.cta_column,";
	$sql.= " t.cta_class,";
	$sql.= " t.cta_identifier,";
	$sql.= " t.cta_operation,";
	$sql.= " t.cta_balances,";
	$sql.= " t.cta_totalvis,";
	$sql.= " t.name_vision,";
	$sql.= " t.line,";
	$sql.= " t.fk_accountini,";
	$sql.= " t.fk_accountfin";

	$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as t";
	$sql.= " ORDER BY $sortfield $sortorder";
	$sql.= $db->plimit($limit+1, $offset);

	print_barre_liste($langs->trans("Listperiods"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "t.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Sequence"),"liste.php", "t.sequence","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Account"),"liste.php", "t.account","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Accountsup"),"liste.php", "t.account_sup","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Namevision"),"liste.php", "t.name_vision","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Detail"),"liste.php", "t.detail_managment","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Line"),"liste.php", "t.line","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";


	dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$var=True;
			while ($i < min($num,$limit))
			{
				$obj = $db->fetch_object($resql);
				if ($obj)
				{
					$var=!$var;
					print "<tr $bc[$var]>";
					if ($obj->line == '001')
						print '<td><a href="fiche.php?id='.$obj->rowid.'">'.img_object($langs->trans("Showvision"),'vision').' '.$obj->ref.'</a></td>';
					else
						print '<td>'.img_object($langs->trans("Showvision"),'vision').' '.$obj->ref.'</td>';

					print '<td>'.str_pad($obj->sequence, 10, "0", STR_PAD_LEFT).'</td>';
					print '<td>'.$obj->account.'</td>';
					print '<td>'.$obj->account_sup.'</td>';
					print '<td>'.$obj->name_vision.'</td>';
					print '<td>'.$obj->detail_managment.'</td>';
					print '<td>'.$obj->line.'</td>';
					print '<td>'.$object->LibStatut($obj->statut,4).'</td>';

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
}



// End of page
llxFooter();
$db->close();
?>
