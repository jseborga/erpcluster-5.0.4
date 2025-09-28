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
 *   	\file       dev/Productportionings/Productportioning_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-06-29 13:37
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
dol_include_once('/fabrication/class/productportioning.class.php');
require_once DOL_DOCUMENT_ROOT.'/fabrication/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load('fabrication@fabrication');

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel		= GETPOST('cancel','alpha');

$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

$object = new Productportioning($db);
$product = new Product($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add' && $user->rights->fabrication->port->crear)
{
	$fk_product = GETPOST('fk_product');
	$fk_product_portion = GETPOST('fk_product_portion');
	if (empty(GETPOST('fk_product')))
	{
		//buscamos
		$res = $product->fetch('',GETPOST('search_fk_product'));
		if ($res>0 && $product->ref == GETPOST('search_fk_product'))
			$fk_product = $product->id;
	}
	$fk_product_portion = GETPOST('fk_product_portion');
	if (empty(GETPOST('fk_product_portion')))
	{
		//buscamos
		$res = $product->fetch('',GETPOST('search_fk_product_portion'));
		if ($res>0 && $product->ref == GETPOST('search_fk_product_portion'))
			$fk_product_portion = $product->id;
	}
	$object->fk_product=$fk_product;
	$object->fk_product_portion=$fk_product_portion;
	$object->qty=GETPOST('qty');
	$object->date_create = dol_now();
	$object->tms = dol_now();
	$object->fk_user_create = $user->id;
	$object->active = 0;
	$object->statut = 0;
	$id=$object->create($user);
	if ($id > 0)
	{
		// Creation OK
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		// Creation KO
		$mesg=$object->error;
		$action = 'create';
	}
}

if ($action == 'update' && $user->rights->fabrication->port->mod && $cancel != $langs->trans('Cancel'))
{
	print_r($_POST);exit;
	$object->fetch($id);
	$fk_product = GETPOST('fk_product');
	$fk_product_portion = GETPOST('fk_product_portion');
	if (empty(GETPOST('fk_product')))
	{
		//buscamos
		$res = $product->fetch('',GETPOST('search_fk_product'));
		if ($res>0 && $product->ref == GETPOST('search_fk_product'))
			$fk_product = $product->id;
	}
	$fk_product_portion = GETPOST('fk_product_portion');
	if (empty(GETPOST('fk_product_portion')))
	{
		//buscamos
		$res = $product->fetch('',GETPOST('search_fk_product_portion'));
		if ($res>0 && $product->ref == GETPOST('search_fk_product_portion'))
			$fk_product_portion = $product->id;
	}
	$object->fk_product=$fk_product;
	$object->fk_product_portion=$fk_product_portion;
	$object->qty=GETPOST('qty');
	$object->tms = dol_now();
	$res=$object->update($user);
	if ($res > 0)
	{
		// Creation OK
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		// Creation KO
		$mesg=$object->error;
		$action = 'edit';
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Portioning'),'');

$form=new Formv($db);


// Put here content of your page

// Example 1 : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';


// Example 2 : Adding links to objects
//$somethingshown=$myobject->showLinkedObjectBlock();

if ($action == 'create' && $user->rights->fabrication->port->crear)
{
	print_fiche_titre($langs->trans("Newportioning"));

	dol_htmloutput_mesg($mesg);

	print '<form action="fiche.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="noborder" width="100%">';

    // Product
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans('Product').'</td>';
	print '<td>';
	print $form->select_produits_v('','fk_product','',$conf->product->limit_size,0,-1,2,'',1,'','');
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans('Productportion').'</td>';
	print '<td>';
	print $form->select_produits_v('','fk_product_portion','',$conf->product->limit_size,0,-1,2,'',1,'','');
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans('Qty').'</td>';
	print '<td>';
	print '<input name="qty" size="10" value="'.GETPOST("qty").'">';
	print '</td>';
	print '<tr>';
	print '<td colspan="2">';
	print '<center><input type="submit" class="button" value="'.$langs->trans('Insert').'"></center>';
	print '</td>';
	print '</tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>'; 

}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);
		$object->fetch($id);
		$product = new Product($db);
		$product->fetch($object->fk_product);
		if ($result < 0)
		{
			dol_print_error($db);
		}


		/*
		 * Affichage fiche
		 */
		if ($action <> 'edit' && $action <> 're-edit')
		{
		  //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Productportion"), 0, 'stock');

			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteProductAlternative"),$langs->trans("ConfirmDeleteProductAlternative",$objproductf->label),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

			// product father
			print '<tr><td width="25%">'.$langs->trans("Product").'</td><td>';
			print $product->getNomUrl(1).' - ';
			print $product->label;
			print '</td>';
			print '</tr>';

			// product portion
			$productp = new Product($db);
			$productp->fetch($object->fk_product_portion);
			print '<tr><td width="25%">'.$langs->trans("Productportion").'</td><td>';
			print $productp->getNomUrl(1).' - ';
			print $productp->label;
			print '</td>';
			print '</tr>';

			//qty father
			print '<tr><td>'.$langs->trans("Qty").'</td><td>';
			print $object->qty;
			print '</td></tr>';

			print "</table>";
			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{

				if (($object->statut==1 || $object->statut==0 ) && $user->rights->fabrication->port->del)
					print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
				if ($user->rights->fabrication->port->mod)
					print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'">'.$langs->trans("Edit").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Edit")."</a>";

			}

			print "</div>";
		}
		if ($action == 'edit' || $action == 're-edit')
		{
	print_fiche_titre($langs->trans("Editportioning"));

	dol_htmloutput_mesg($mesg);

	print '<form action="fiche.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_htmloutput_mesg($mesg);

	print '<table class="noborder" width="100%">';

    // Product
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans('Product').'</td>';
	print '<td>';
	print $form->select_produits_v($object->fk_product,'fk_product','',$conf->product->limit_size,0,-1,2,'',1,'','');
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans('Productportion').'</td>';
	print '<td>';
	print $form->select_produits_v($object->fk_product_portion,'fk_product_portion','',$conf->product->limit_size,0,-1,2,'',1,'','');
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans('Qty').'</td>';
	print '<td>';
	print '<input name="qty" size="10" value="'.$object->qty.'">';
	print '</td>';
	print '<tr>';
	print '<td colspan="2">';
	print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'"><input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'"></center>';
	print '</td>';
	print '</tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>'; 

		}
	}
}



// End of page
llxFooter();
$db->close();
?>
