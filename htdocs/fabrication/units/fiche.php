<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/fabrication/fiche.php
 *	\ingroup    fabrication
 *	\brief      Page fiche fabrication
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/lib/fabrication.lib.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/productunit.class.php");

$langs->load("fabrication@fabrication");

$action=GETPOST('action');

$id = GETPOST('id','int');
if (empty($id)) $id = GETPOST('rowid','int');

$product = new Product($db);
$object = new Productunit($db);
$result = $product->fetch($id);

$mesg = '';

/*
 * Actions
 */

// Ajout entrepot
if ($action == 'add' && $user->rights->fabrication->uni->crear)
{
	$object->fk_product = GETPOST("id");
	$object->fk_unit   = GETPOST("fk_unit");
	$object->active = 1;
	if ($object->fk_unit>0) {
		$idr = $object->create($user);
		if ($idr > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("ErrorUnitRequired").'</div>';
	  $action="create";   // Force retour sur page creation
	}
}

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->fabrication->delunidad)
{
	$object = new Units($db);
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/fabrication/units/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Modification units
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$idr = GETPOST('idr');
	if ($object->fetch($idr))
	{
		$object->fk_unit     = GETPOST('fk_unit');

		if ( $object->update($user) > 0)
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="ok">'.$langs->trans('Successfullyrecorded').'</div>';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Fabrication_En|FR:Module_Fabrication|ES:M&oacute;dulo_Fabrication';
llxHeader("",$langs->trans("ApplicationsUnits"),$help_url);


if ($id)
{
	$idr = GETPOST('idr');
	dol_htmloutput_mesg($mesg);

	if ($result < 0)
	{
		dol_print_error($db);
	}
	$head = fabrication_prepare_head($product);
	dol_fiche_head($head, 'unit', $langs->trans("ApplicationUnits"), 0, 'stock');

	print '<table class="border" width="100%"';
	print '<tr>';
	print '<td width="15%">'.$langs->trans('Ref').'</td>';
	print '<td>';
	print $product->ref;
	print '</td></tr>';
	print '<tr>';
	print '<td>'.$langs->trans('Label').'</td>';
	print '<td>';
	print $product->label;
	print '</td></tr>';
	print '<tr>';
	print '<td>'.$langs->trans('Status').'</td>';
	print '<td>';
	print $product->getLibStatut();
	print '</td></tr>';
	print '</table>';
	$object->fetch($idr,$product->id);
	if ($object->fk_product == $product->id)
	{
		if ($action <> 'edit' && $action <> 're-edit')
		{
			print '<table class="border" width="100%">';
			print '<tr><td width="15%">'.$langs->trans("Unit").'</td><td>';
			print select_unit($object->fk_unit,'fk_unit','',0,1,'rowid','label');
			print '</td>';
			print "</table>";

				//barre action
			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				if ($user->rights->fabrication->uni->crear)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->fk_product.'&idr='.$object->id.'">'.$langs->trans("Modify").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->fabrication->uni->del)
					print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->fk_product.'&idr='.$object->id.'">'.$langs->trans("Delete").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}

			print "</div>";
		}

				// Edition fiche
		if (($action == 'edit' || $action == 're-edit'))
		{

			print '<form action="fiche.php" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="idr" value="'.$object->id.'">';
			print '<input type="hidden" name="id" value="'.$object->fk_product.'">';

			print '<table class="border" width="100%">';

		 			// Ref
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("Unit").'</td><td>';
			print select_unit($object->fk_unit,'fk_unit','',1,0,'rowid','label');
			print '</td></tr>';
			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
			print '</form>';

		}
	}
	else
	{
		if ($user->rights->fabrication->uni->crear)
		{
			//print_fiche_titre($langs->trans("NewUnit"));

			print "<form action=\"fiche.php\" method=\"post\">\n";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="add">';
			print '<input type="hidden" name="id" value="'.$id.'">';

			dol_htmloutput_mesg($mesg);

			print '<table class="border" width="100%">';

	//
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("Unit").'</td><td>';
			print select_unit($object->fk_unit,'fk_unit','',1,0,'rowid','label');
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

			print '</form>';
		}
	}
	dol_fiche_end();
}



llxFooter();

$db->close();
?>
