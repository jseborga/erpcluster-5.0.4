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
//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/productext/class/unitconv.class.php");
require_once(DOL_DOCUMENT_ROOT."/productext/lib/units.lib.php");


require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/product/modules_product.php';





$langs->load("productext@productext");
$langs->load("products");


$action=GETPOST('action');
$id = GETPOST('id','int');
if (empty($id)) $id = GETPOST('rowid','int');

if (!$user->rights->productext->conv->lire) accessforbidden();

$product = new Product($db);
$prodtmp = new Product($db);
$object = new Unitconv($db);

$result = $product->fetch($id);

$mesg = '';

$arrayfc = array('M'=>'Multiplicar','D'=>'Dividir');
/*
 * Actions
 */

// Ajout entrepot
if ($action == 'add' && $user->rights->productext->conv->write)
{
	//creamos

	$object->fk_product = GETPOST("id");
	$object->fk_unit   = $product->fk_unit;
	$object->fk_unit_ext   = GETPOST("fk_unit_ext");
	$object->fc = GETPOST('fc')+0;
	$object->type_fc = GETPOST('type_fc');
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->date_create = dol_now();
	$object->date_mod = dol_now();
	$object->tms = dol_now();
	$object->status = 1;
	//revisamos
	if ($object->fk_unit_ext <=0)
	{
		setEventMessages($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Unitext'))), null, 'errors');
		$error++;
	}
	if ($object->fc <=0)
	{
		setEventMessages($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Factorconversion'))), null, 'errors');
		$error++;
	}

	if (!$error) {
		$idr = $object->create($user);
		if ($idr > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			setEventMessages($object->error,$object->errors,'errors');	
			$action = 'create';
		}
	}
	else
	{
		setEventMessages($object->error,$object->errors,'errors');	
		$action = 'create';
	}
}

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->productext->conv->del)
{
	$object->fetch($_REQUEST["idr"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		setEventMessages($langs->trans('Deleterecord'),null,'mesgs');
		header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		setEventMessages($object->error,$object->errors,'errors');
		$action='';
	}
}

// Modification units
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$idr = GETPOST('idr');
	if ($object->fetch($idr))
	{
		$object->fk_unit   = $product->fk_unit;
		$object->fk_unit_ext   = GETPOST("fk_unit_ext");
		$object->fc = GETPOST('fc')+0;
		$object->type_fc = GETPOST('type_fc');
		$object->fk_user_mod = $user->id;
		$object->date_mod = dol_now();
		$object->tms = dol_now();
		if ( $object->update($user) > 0)
		{
			setEventMessages($langs->trans('Updaterecord'),null,'mesgs');
			$action = '';
		}
		else
		{
			$action = 'edit';
			setEventMessages($object->error,$object->errors,'errors');
		}
	}
	else
	{
		$action = 'edit';
		setEventMessages($object->error,$object->errors,'errors');
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
	$head = product_prepare_head($product);
	dol_fiche_head($head, 'convert', $langs->trans("ApplicationUnits"), 0, 'stock');

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
	dol_fiche_end();
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_product = ".$id;
	$object->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	$object->fetch($idr,$product->id);

	// Confirmation de la suppression de la facture fournisseur
	if ($action == 'delete')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$id.'&idr='.GETPOST('idr'), $langs->trans('DeleteUnits'), $langs->trans('ConfirmDeleteUnit'), 'confirm_delete', '', 0, 1);
	}
	print $formconfirm;
	dol_fiche_head();
	if ($user->rights->productext->conv->write)
	{
		print '<form action="fiche.php" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		if ($action == 'edit')
		{
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="idr" value="'.$idr.'">';
		}
		else
			print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="id" value="'.$product->id.'">';
	}

	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';
	print '<tr>';
	print_liste_field_titre($langs->trans('Unit'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('By'),$_SERVER['PHP_SELF'],'','',$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Toget'),$_SERVER['PHP_SELF'],'','',$params,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print '</tr>';

	foreach ((array) $object->lines AS $i => $line)
	{
		$product->fetch($line->fk_product);
		if (GETPOST('idr')>0 && GETPOST('idr') == $line->id && $action == 'edit')
		{
			print '<tr>';
			print '<td>';
			if (! empty($conf->global->PRODUCT_USE_UNITS))
			{
				$unit = $product->getLabelOfUnit();
				if ($unit !== '') {
					print $langs->trans($unit);
				}
			}
			print '</td>';
			print '<td>';
			print $form->selectarray('type_fc',$arrayfc,$line->type_fc);
			print '</td>';
			print '<td><input type="number" step="any" min="0" name="fc" value="'.$line->fc.'"></td>';
			print '<td>';
			print $form->selectUnits($line->fk_unit_ext,'fk_unit_ext');
			print '</td>';
			print '<td>';
			print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
			print '</td>';
			print '</tr>';
		}
		else
		{
			print '<tr>';

			// Unit
			print '<td>'.$langs->trans('Un').' ';
			if (! empty($conf->global->PRODUCT_USE_UNITS))
			{
				$unit = $product->getLabelOfUnit();
				if ($unit !== '') {
					print $langs->trans($unit);
				}
			}
			print '</td>';
			print '<td align="center">'.$arrayfc[$line->type_fc].'</td>';
			print '<td align="center">'.$line->fc.'</td>';

			$objunit = fetch_unit($line->fk_unit_ext);
			if ($objunit->rowid == $line->fk_unit_ext)
				print '<td align="center">'.$objunit->short_label.'</td>';
			else
				print '<td>'.$langs->trans('Nodefined').'</td>';
			print '<td align="right">';
			if ($user->rights->productext->conv->write)
			{
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=edit">'.img_picto($langs->trans('Edit'),'edit').'</a>';
			}
			if ($user->rights->productext->conv->del)
			{
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a>';
			}
			print '</td>';
			print '</tr>';
		}
	}
	if ($user->rights->productext->conv->write && $action != 'edit')
	{
		print '<tr>';
		print '<td>';
		if (! empty($conf->global->PRODUCT_USE_UNITS))
		{
			$unit = $product->getLabelOfUnit();
			if ($unit !== '') {
				print $langs->trans($unit);
			}
		}
		print '</td>';
		print '<td>';
		print $form->selectarray('type_fc',$arrayfc,$line->type_fc);
		print '</td>';
		print '<td><input type="number" step="any" min="0" name="fc" value=""></td>';
		print '<td>';
		print $form->selectUnits($line->fk_unit_ext,'fk_unit_ext');
		print '</td>';
		print '<td>';
		print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
		print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
		print '</td>';
		print '</tr>';

	}
	print '</table>';
	if ($user->rights->productext->conv->write)
		print '</form>';
	dol_fiche_end();
}



llxFooter();

$db->close();
?>
