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
 *   	\file       dev/Itemss/Items_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-04 07:24
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
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
//include_once(DOL_DOCUMENT_ROOT.'/core/class/formcompany.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsregion.class.php');
dol_include_once('/budget/class/typeitemadd.class.php');
dol_include_once('/budget/class/supplies.class.php');
dol_include_once('/budget/class/pricegroup.class.php');
dol_include_once('/budget/lib/budget.lib.php');
dol_include_once('/budget/class/cunits.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("budget@budget");

// Get parameters
$id		= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$subaction      = GETPOST('subaction','alpha');
$backtopage     = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="ASC";

$page = isset($_GET["page"])? $_GET["page"]:$_POST["page"];
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;

$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

$search_ref=GETPOST("search_ref");
$search_detail=GETPOST("search_detail");

// Purge criteria
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_all='';
	$search_ref="";
	$search_detail="";
}

if (empty($action) && empty($id) && empty($ref)) $action='create';

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('printFieldListTitleproj','printFieldListOption','doActions','printFieldListValue'));
$extrafields = new ExtraFields($db);

// Load object if id or ref is provided as parameter
$object=new Itemsext($db);
$objItemsregion = new Itemsregion($db);
$typeitem=new Typeitemadd($db);
// $units=new Units($db);
$pricegroup = new Pricegroup($db);
$product = new Product($db);
$societe = new Societe($db);
$cunits = new Cunits($db);

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

if (GETPOST('upload'))
	$action = 'createup';
    //definimos los campos
$aHeaderTpl['llx_items'] = array('ref' => 'ref',
	'type' => 'type',
	'unit' => 'unit',
	'unitlabel' => 'unitlabel',
	'detail'=>'detail',
	'price' => 'price',);


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

/*Part to addup*/
if ($action == 'addup')
{
	$error = 0;
	$aArrData = $_SESSION['aArrData'];
	$table = 'llx_items';
	$db->begin();
	foreach ((array) $aArrData AS $i => $data)
	{
	//vamos verificando la existencia de cada uno
	//type_item
		$typeitem->fetch('',$data['type']);
		if (STRTOUPPER($typeitem->ref) == STRTOUPPER($data['type']))
		{
	    //recuperamos el id de registro
			$fk_type_item = $typeitem->id;
		}
		else
		{
	    	//creamos
			$typeitem->initAsSpecimen();
			$typeitem->entity = $conf->entity;
			$typeitem->ref= $data['type'];
			$typeitem->detail= $data['type'];
			$typeitem->fk_user_create= $user->id;
			$typeitem->fk_user_mod= $user->id;
			$typeitem->date_create= dol_now();
			$typeitem->tms= dol_now();
			$typeitem->statut= 1;
			$restype = $typeitem->create($user);
			if ($restype >0) $fk_type_item = $restype;
			else $error++;
		}
	//unit
		$cunits->fetch('',$data['unit']);
		if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
		{
	    //recuperamos el id de registro
			$fk_unit = $cunits->id;
		}
		else
		{
	    //creamos
			$cunits->initAsSpecimen();
			$cunits->code= $data['unit'];
			$cunits->label= $data['unitlabel'];
			$cunits->short_label= $data['unit'];
			$cunits->active= 1;
			$resunit = $cunits->create($user);
			if ($resunit >0) $fk_unit = $resunit;
			else $error++;
		}
		//buscamos el item
		$res = $object->fetch('',STRTOUPPER($data['ref']));
		if ($res>0)
		{
			if (STRTOUPPER($object->ref) == STRTOUPPER($data['ref']))
			{
				//actualizamos el valor
				$object->detail = $data['detail'];
				$object->amount = $data['price'];
				$result = $object->update($user);
				if (!$result>0)
					$error++;
			}
			else
			{
				//creamos nuevo
				$object->initAsSpecimen();
				$object->entity = $conf->entity;
				$object->ref = $data['ref'];
				$object->detail = $data['detail'];
				$object->fk_user_create = $user->id;
				$object->fk_user_mod = $user->id;
				$object->fk_type_item = $fk_type_item;
				$object->fk_unit = $fk_unit;
				$object->especification = '';
				$object->plane = '';
				$object->amount = $data['price'];
				$object->date_create = dol_now();
				$object->gestion= date('Y');
				$object->tms = dol_now();
				$object->statut = 1;
				$result = $object->create($user);
				if (!$result > 0)
					$error++;
			}
		}
		else
			$error++;
	}
	if (empty($error))
		$db->commit();
	else
	{
		setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
		$db->rollback();
	}
	$action = 'list';
}
// Part to create upload
if ($action == 'veriffile')
{
    //verificacion
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];
	$separator = GETPOST('separator','alpha');
	$tempdir = "tmp/";

	if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	{

	//  echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	$csvfile = $tempdir.$nombre_archivo;

	$fh = fopen($csvfile, 'r');
	$headers = fgetcsv($fh);
	$aHeaders = explode($separator,$headers[0]);
	$data = array();
	$aData = array();
	while (! feof($fh))
	{
		$row = fgetcsv($fh,'','^');
		if (!empty($row))
		{
			$aData = explode($separator,$row[0]);
			$obj = new stdClass;
			$obj->none = "";
			foreach ($aData as $i => $value)
			{
				$key = $aHeaders[$i];
				if (!empty($key))
					$obj->$key = $value;
				else
					$obj->none = $value." xx";
			}
			$data[] = $obj;
		}
	}
	fclose($fh);

	$c=0;
	$action = "verifup";
}

// Action to add record
if ($action == 'add')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/items.php',1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;

	/* object_prop_getpost_prop */
	$object->ref=GETPOST("ref",'alpha');
	$object->detail=GETPOST("detail",'alpha');
	$object->fk_type_item=GETPOST("fk_type_item",'int');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->amount=GETPOST('amount','int');
	$object->entity=$conf->entity;
	$object->fk_user_create=$user->id;
	$object->fk_user_mod=$user->id;
	$object->date_create=dol_now();
	$object->gestion= date('Y');
	$object->tms=dol_now();
	$object->statut=1;

	if (empty($object->ref))
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),'errors');
	}

	if (! $error)
	{
		$result=$object->create($user);
		if ($result > 0)
		{
	    // Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/items.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
	    // Creation KO
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else  setEventMessages($object->error, null, 'errors');
			$action='create';
		}
	}
	else
	{
		$action='create';
	}
}

// Cancel
if ($action == 'update' && GETPOST('cancel')) $action='view';

// Action to update record
if ($action == 'update' && ! GETPOST('cancel'))
{
	if ($object->id == $id)
	{
		$error=0;
		print_r($_POST);exit;
		/* object_prop_getpost_prop */
		$object->ref=GETPOST("ref",'alpha');
		$object->detail=GETPOST("detail",'alpha');
		$object->fk_type_item=GETPOST("fk_type_item");
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->amount=GETPOST('amount','int');
		$object->fk_user_mod=$user->id;
		$object->tms=dol_now();

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
		// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}
}

// Action to delete
if ($action == 'confirm_delete')
{
	$result=$object->delete($user);
	if ($result > 0)
	{
	// Delete OK
		setEventMessages($langs->trans("RecordDeleted"), null, 'mesgs');
		header("Location: ".dol_buildpath('/budget/items/items.php',1));
		exit;
	}
	else
	{
		if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
		else setEventMessages($object->error,null,'errors');
	}
}
// Action to addsupplies
if ($action == 'addsupplies')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/items.php',1);
		header("Location: ".$urltogo);
		exit;
	}
	$error=0;
	$objsup = new Supplies($db);

    //buscamos la categoria
	$fk_category = GETPOST('fk_category','int');
	$pricegroup->fetch_cat($fk_category);
	if ($pricegroup->fk_category == $fk_category)
		$objsup->fk_price_group = $pricegroup->id;
	else
	{
		$error++;
		setEventMessages($langs->trans("ErrorCategoryRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
	}
	$objsup->fk_product=GETPOST("fk_product",'int');
    //buscamos el producto
	if (empty($objsup->fk_product))
	{
		$refproduct = GETPOST('search_fk_product','alpha');
		$product->fetch('',$refproduct);
		if ($product->ref == $refproduct)
			$objsup->fk_product = $product->id;
	}
	/* object_prop_getpost_prop */
	$objsup->fk_item=$id;
	$objsup->fk_unit=GETPOST("fk_unit",'int');
	$object->fk_company=GETPOST("fk_company",'int');
	$object->quant=GETPOST('quant','int');
	$object->price=GETPOST('price');

	$objsup->fk_user_create=$user->id;
	$objsup->fk_user_mod=$user->id;
	$objsup->date_create=dol_now();
	$objsup->tms=dol_now();
	$objsup->statut=1;

	if ($objsup->fk_product <=0)
	{
		$error++;
		setEventMessage($langs->trans("ErrorProductRequired",$langs->transnoentitiesnoconv("Product")),'errors');
	}
	if (! $error)
	{
		$result=$objsup->create($user);
		if ($result > 0)
		{
	    // Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/items.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
	    // Creation KO
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else  setEventMessages($object->error, null, 'errors');
			$action='create';
		}
	}
	else
	{
		$action='create';
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$aArrjs = array();
$aArrcss = array('/budget/css/style.css');
llxHeader("",$langs->trans("Items"),$help_url,'','','',$aArrjs,$aArrcss);

//llxHeader('','MyPageName','');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
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


// Part to show a list
if ($action!= 'verifup' && $action!= 'createup' && ($action == 'list' || empty($id)))
{
	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql.= " t.entity,";
	$sql.= " t.ref,";
	$sql.= " t.fk_user_create,";
	$sql.= " t.fk_user_mod,";
	$sql.= " t.fk_type_item,";
	$sql.= " t.fk_unit,";
	$sql.= " t.detail,";
	$sql.= " t.especification,";
	$sql.= " t.plane,";
	$sql.= " t.amount,";
	$sql.= " t.date_create,";
	$sql.= " t.date_mod,";
	$sql.= " t.tms,";
	$sql.= " t.status,";
	$sql.= " i.ref AS reftype, ";
	$sql.= " i.detail AS detailtype, ";
	$sql.= " u.label AS longunit, ";
	$sql.= " u.short_label AS shortunit ";

	$sql.= " FROM ".MAIN_DB_PREFIX."items as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."type_item as i ON t.fk_type_item = i.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_units as u ON t.fk_unit = u.rowid";
	$sql.= " WHERE t.entity = ".$conf->entity;
	if ($search_ref) $sql .= natural_search('t.ref', $search_ref);
	if ($search_detail) $sql .= natural_search('t.detail', $search_detail);

	$sql.= " ORDER BY $sortfield $sortorder";
	$sql.= $db->plimit($conf->liste_limit+1, $offset);

	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		print_barre_liste($langs->trans("ListeItems"), $page, "items.php", "", $sortfield, $sortorder,'',$num);

		print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';

		print '<table class="noborder">'."\n";
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.detail','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'i.detail','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Unit'),'','','',$param,'');
		print_liste_field_titre($langs->trans('Price'),$_SERVER['PHP_SELF'],'u.amount','',$param,'align="right"',$sortfield,$sortorder);

		$parameters=array();
        $reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
        print $hookmanager->resPrint;
        print '</tr>'."\n";

        // Fields title search
        print '<tr class="liste_titre">';
        print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
        print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
        print '<td class="liste_titre">&nbsp;</td>';
        print '<td class="liste_titre">&nbsp;</td>';
        $parameters=array();
        $reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
        print $hookmanager->resPrint;

        print '<td align="right">';
        print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("RemoveFilter"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</td>';

        print '</tr>'."\n";

        $i = 0;
        while ($i < $num)
        {
        	$obj = $db->fetch_object($resql);
        	if ($obj)
        	{
                // You can use here results
        		print '<tr><td>';
        		print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'">'.img_picto($langs->trans('Edit'),'view').' '.$obj->ref.'</a>';
        		print '</td><td>';
        		print $obj->detail;
        		print '</td><td>';
        		print $obj->detailtype;
        		print '</td><td>';
        		print $obj->shortunit;
        		print '</td><td align="right">';
        		print price($obj->amount);
        		print '</td></tr>';
        	}
        	$i++;
        }
        print '</table>'."\n";
        print '</form>';
    }
    else
    {
    	$error++;
    	dol_print_error($db);
    }

}



// Part to create
if ($action == 'create')
{
	print_fiche_titre($langs->trans("New"));

	dol_fiche_head();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
	print '<input class="flat" type="text" size="36" name="ref" value="'.$ref.'" required>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
	print '<input class="flat" type="text" size="36" name="detail" value="'.$detail.'" required>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
	print $typeitem->select_typeitem($fk_typeitem,'fk_type_item','',1);
	print '</td></tr>';

    // Units
	print '<tr><td>'.$langs->trans('Unit').'</td>';
	print '<td>';
	print $form->selectUnits($object->fk_unit, 'fk_unit');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Price").'</td><td>';
	print '<input type="number" min="0" step="any" name="amount" value="'.$amount.'">';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';


	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	if ($user->rights->budget->ite->up)
		print '&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?action=createup" class="button" name="upload">'.$langs->trans("Uploadfile").'</a>';
	print '</center>';

	print '</form>';

	dol_fiche_end();
}

// Part to create upload
if ($action == 'createup')
{
	print_fiche_titre($langs->trans("New"));

	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="veriffile">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("File").'</td><td>';
	print '<input type="file" class="flat" name="archivo" id="archivo" required>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2" required>';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';
	print '<div>';
	print '<span>';
	print $langs->trans('Es necesario un archivo CSV con las siguientes columnas').':';
	print '</span>';
	print '<div>'.'ref'.' =>  <span>'.$langs->trans('Codigo del item').'</span>'.'</div>';
	print '<div>'.'detail'.' => <span>'.$langs->trans('Descripcion del item').'</span>'.'</div>';
	print '<div>'.'type'.' => <span>'.$langs->trans('Tipo de item').'</span>'.'</div>';
	print '<div>'.'unit'.' => <span>'.$langs->trans('Codigo unidad de medida').'</span>'.'</div>';
	print '<div>'.'unitlabel'.' => <span>'.$langs->trans('Nombre unidad de medida').'</span>'.'</div>';
	print '<div>'.'price'.' => <span>'.$langs->trans('Precio del item').'</span>'.'</div>';
	print '</div>';
	print '<br>';
	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</center>';

	print '</form>';

	dol_fiche_end();
}

if ($action == 'verifup')
{
	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="addup">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	print '<table class="noborder" width="100%">';

    //encabezado
	$table = 'llx_items';
	foreach($aHeaders AS $i => $value)
	{
		$aHeadersOr[trim($value)] = trim($value);
	}
	$aValHeader = array();
	foreach($aHeaderTpl[$table] AS $i => $value)
	{
		if (!$aHeadersOr[trim($value)])
			$aValHeader[$value] = $value;
	}
	print '<tr class="liste_titre">';
	foreach($aHeaders AS $i => $value)
	{
		print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
	}
	print '</tr>';
	if (!empty($aValHeader))
	{
		$lSave = false;
		print "<tr class=\"liste_titre\">";
		print '<td>'.$langs->trans('Missingfieldss').'</td>';
		foreach ((array) $aValHeader AS $j => $value)
		{
			print '<td>'.$value.'</td>';
		}
		print '</tr>';
	}
	else
	{
		$lSave = true;
		$var=True;
		$c = 0;
		foreach($data AS $key){
			$var=!$var;
			print "<tr $bc[$var]>";
			$c++;
			foreach($aHeaders AS $i => $keyname)
			{
				if (empty($keyname))
					$keyname = "none";
				$phone = $key->$keyname;
				$aArrData[$c][$keyname] = $phone;
				print '<td>'.$phone.'</td>';
			}

			print '</tr>';
		}

	}
	print '</table>';

	If ($lSave)
	{
		$_SESSION['aArrData'] = $aArrData;
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
	}
    //validando el encabezado
	print '</form>';

	dol_fiche_end();
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	dol_fiche_head();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
	print '<input class="flat" type="text" size="36" name="ref" value="'.$object->ref.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Detail").'</td><td>';
	print '<input class="flat" type="text" size="36" name="detail" value="'.$object->detail.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
	print $typeitem->select_typeitem($object->fk_type_item,'fk_type_item','',1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('Unit').'</td>';
	print '<td>';
	print $form->selectUnits($object->fk_unit, 'fk_unit');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Price").'</td><td>';
	print '<input type="number" min="0" step="any" name="amount" value="'.$object->amount.'">';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';

	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	print '</form>';

	dol_fiche_end();
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action =='delete'))
{
	dol_fiche_head();
    // Confirm delete request
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Delete"),$langs->trans("Confirmdeleteitem",$object->ref),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans("Ref").'</td><td>';
	print $object->ref;
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Detail").'</td><td>';
	print $object->detail;
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Type").'</td><td>';
	print $typeitem->select_typeitem($object->fk_type_item,'fk_type_item','',0,1);
	print '</td></tr>';

    // Unit
	if (! empty($conf->global->PRODUCT_USE_UNITS))
	{
		$unit = $object->getLabelOfUnit('short');

		print '<tr><td>'.$langs->trans('Unit').'</td><td>';
		if ($unit !== '') {
			print $langs->trans($unit);
		}
		print '</td></tr>';
	}


	print '<tr><td>'.$langs->trans("Price").'</td><td>';
	print price($object->amount);
	print '</td></tr>';

	print '</table>'."\n";


	dol_fiche_end();


    // Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
    $reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

    if (empty($reshook))
    {
    	if ($user->rights->budget->ite->mod)
    	{
    		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
    	}

    	if ($user->rights->budget->ite->del)
    	{
	    if ($conf->use_javascript_ajax && !empty($conf->dol_use_jmobile))
	    // We can't use preloaded confirm form with jmobile
	    {
	    	print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
	    }
	    else
	    {
	    	print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
	    }
	}
}
print '</div>'."\n";

    //listamos los insumos (supplies) por grupo
    // $head = budget_prepare_head($object,$user);

    // dol_fiche_head($head, 'priceunit', $langs->trans("Priceunits"), 0, 'budget');
    // if (empty($subaction)) $subaction = 'mat';
    // if ($subaction == 'mat')
    //   include_once DOL_DOCUMENT_ROOT.'/budget/items/tpl/materials.tpl.php';
    // if ($subaction == 'mo')
    //   include_once DOL_DOCUMENT_ROOT.'/budget/items/tpl/workforce.tpl.php';
    // if ($subaction == 'me')
    //   include_once DOL_DOCUMENT_ROOT.'/budget/items/tpl/machinery.tpl.php';

    //HABILITAR CUANDO SE PROCESE LOS DEPENDIENTES DEL ITEM
    //include_once DOL_DOCUMENT_ROOT.'/budget/supplies/tpl/supplies.tpl.php';

    // Example 2 : Adding links to objects
    // The class must extends CommonObject class to have this method available
    //$somethingshown=$object->showLinkedObjectBlock();

}


// End of page
llxFooter();
$db->close();
