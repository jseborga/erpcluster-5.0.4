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
dol_include_once('/fabrication/class/productportioningadd.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/fabrication/class/productunit.class.php';
require_once(DOL_DOCUMENT_ROOT."/fabrication/lib/fabrication.lib.php");

require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load('fabrication@fabrication');

// Get parameters
$id			= GETPOST('id','int');
$idr        = GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel		= GETPOST('cancel','alpha');

$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

$object  = new Productportioningadd($db);
$product = new Product($db);
$objpu   = new Productunit($db);
if ($id>0)
	$product->fetch($id);
/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add' && $user->rights->fabrication->port->crear)
{
	$error = 0;
	$fk_product = GETPOST('id');
	$fk_product_portion = GETPOST('fk_product_portion');
	if (empty(GETPOST('fk_product_portion')))
	{
		//buscamos
		$res = $product->fetch('',GETPOST('search_fk_product_portion'));
		if ($res>0 && $product->ref == GETPOST('search_fk_product_portion'))
			$fk_product_portion = $product->id;
	}
	//cambiamos a todos a estado inactivo
	$filter = array(1=>1);
	$filterstatic= " AND t.fk_product = ".$fk_product;
	$objtemp = new Productportioningadd($db);
	$result = $objtemp->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
	$lines = $objtemp->lines;
	$db->begin();
	for($i=0;$i<count($lines);$i++)
	{
		$line = $lines[$i];
		$objtemp->fetch($line->id);
		$objtemp->active= 0;
		$objtemp->statut=-1;
		$res = $objtemp->update($user);
		if ($res <=0) $error++;
	}
	//registro nuevo
	$object->fk_product=$fk_product;
	$object->fk_product_portion=$fk_product_portion;
	$object->qty=GETPOST('qty');
	$object->date_create = dol_now();
	$object->tms = dol_now();
	$object->fk_user_create = $user->id;
	$object->active = 1;
	$object->statut = 1;
	$res=$object->create($user);
	if ($res > 0)
	{
		$db->commit();
		// Creation OK
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		$db->rollback();
		// Creation KO
		$mesg=$object->error;
		$action = 'create';
	}
}

if ($action == 'update' && $user->rights->fabrication->port->mod && $cancel != $langs->trans('Cancel'))
{
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

if ($id)
{
	dol_htmloutput_mesg($mesg);
		//$product = new Product($db);
	$product->fetch($id);

	$head = fabrication_prepare_head($product);
	dol_fiche_head($head, 'portioning', $langs->trans("ApplicationUnits"), 0, 'stock');

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

	$sortorder = 'DESC';
	$sortfield = 'tms';
	$filter = array(1=>1);
	$filterstatic = ' AND t.fk_product = '.$product->id;
	$result = $object->fetchAll($sortorder, $sortfield, 0, 0, $filter, 'AND',$filterstatic);

	if ($result < 0)
	{
		dol_print_error($db);
	}

	//$head = fabrication_prepare_head($product);

	//dol_fiche_head($head, 'card', $langs->trans("Productportion"), 0, 'stock');

		// Confirm delete third party
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteProductAlternative"),$langs->trans("ConfirmDeleteProductAlternative",$objproductf->label),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	print_barre_liste($langs->trans("ListePortioningProduct"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Name"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Active"),"liste.php", "","","",'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	if ($action == 'create' && $user->rights->fabrication->port->crear)
	{
		print '<form action="fiche.php" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="id" value="'.$product->id.'">';

	    // Product
		print '<tr>';
		print '<td>';
		print $form->select_produits_v('','fk_product_portion','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '</td>';
		print '<td>';
		print '';
		print '</td>';
		print '<td>';
		print '';
		print '</td>';
		print '<td align="right">';
		print '<input name="qty" size="10" value="'.GETPOST("qty").'">';
		print '</td>';
		print '<td></td>';
		print '<td align="right">';
		print '<center><input type="submit" class="button" value="'.$langs->trans('Create').'"></center>';
		print '</td>';
		print '</tr>';
		print '</form>'; 
	}

	$lines = $object->lines;
	$objtemp = new Product($db);
	for ($i=0; $i< count($lines);$i++)
	{
		$obj = $lines[$i];
		$objtemp->fetch($obj->fk_product_portion);
		// product father
		print '<tr>';
		print '<td>';
		print $objtemp->getNomUrl(1);
		print '</td>';
		print '<td>';
		print $objtemp->label;
		print '</td>';
		$objpu->fetch('',$objtemp->id);
		print '<td>';
		if ($objpu->fk_product == $objtemp->id)
		{
			//busco la unidad
			print select_unit($objpu->fk_unit,'code','',0,1,'rowid','label');
		}
		else
			print '';
		print '</td>';
			//qty father
			//qty father
		print '<td align="right">';
		print $obj->qty;
		print '</td>';
		print '<td align="center">';
		print ($obj->active?img_picto('','switch_on'):img_picto('','switch_off'));
		print '</td>';		
		print '<td align="right">';
		print '';
		print '</td>';

		print '</tr>';
	}


	print "</table>";
	dol_fiche_end();


		// Barre d'action
	print "<div class=\"tabsAction\">\n";

	if ($action == '')
	{

		if ($user->rights->fabrication->port->crear)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create&id='.$product->id.'">'.$langs->trans("Create").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";

	}

	print "</div>";
}




// End of page
llxFooter();
$db->close();
?>
