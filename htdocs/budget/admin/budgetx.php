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
 *   	\file       dev/Parameters/Parameter_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-05 12:06
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
dol_include_once('/budget/class/parameter.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
if (!$user->admin)
  accessforbidden();
  
if (empty($action) && empty($id) && empty($ref)) $action='create';

// Load object if id or ref is provided as parameter
$object=new Parameter($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
  {
    $result=$object->fetch($id,$user);
    if ($result < 0) dol_print_error($db);
  }
//load object with by_default = 1
if (empty($id))
  {
    $result=$object->fetch('',$user,'1');
    if ($result < 0) dol_print_error($db);
    else
      {
	$id = $object->id;
	$action = 'view';
      }
  }

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

// Action to add record
if ($action == 'add')
  {
    if (GETPOST('cancel'))
      {
	$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/admin/priceunits.php',1);
	header("Location: ".$urltogo);
	exit;
      }
    
    $error=0;
    //buscamos si existe un registro con by_default = 1
    if ($object->fetch('','',1)<=0)
      {
	/* object_prop_getpost_prop */
	$object->entity=$conf->entity;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->fk_city = GETPOST('fk_city','int');
	$object->social_benefit = GETPOST('social_benefit');
	$object->tax_labor = GETPOST('tax_labor');
	$object->tools = GETPOST('tools');
	$object->overhead = GETPOST('overhead');
	$object->utility = GETPOST('utility');
	$object->tax_transaction = GETPOST('tax_transaction');
	$object->exchange_rate = GETPOST('exchange_rate');
	$object->decimal_number = GETPOST('decimal_number');
	$object->global_item = 'Salario';
	$object->date_create = dol_now();
	$object->tms = dol_now();
	$object->by_default = '1';
	$object->statut = 1;
	
	// if (empty($object->ref))
	//   {
	//     $error++;
	//     setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),'errors');
	//   }
	
	if (! $error)
	  {
	    $result=$object->create($user);
	    if ($result > 0)
	      {
		// Creation OK
		$urltogo=$backtopage?$backtopage:dol_buildpath('/mymodule/list.php',1);
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
    else
      $action= 'edit';
  }

// Cancel
if ($action == 'update' && GETPOST('cancel')) $action='view';

// Action to update record
if ($action == 'update' && ! GETPOST('cancel'))
  {
    $error=0;
    if ($object->id == $id)
      {
	$object->entity=$conf->entity;
	$object->fk_user_mod = $user->id;
	$object->fk_city = GETPOST('fk_city','int');
	$object->social_benefit = GETPOST('social_benefit');
	$object->tax_labor = GETPOST('tax_labor');
	$object->tools = GETPOST('tools');
	$object->overhead = GETPOST('overhead');
	$object->utility = GETPOST('utility');
	$object->tax_transaction = GETPOST('tax_transaction');
	$object->exchange_rate = GETPOST('exchange_rate');
	$object->decimal_number = GETPOST('decimal_number');
	//$object->global_item = 'Salario';
	$object->tms = dol_now();
	$object->by_default = '1';
	$object->statut = 1;
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
		header("Location: ".dol_buildpath('/buildingmanagement/list.php',1));
		exit;
	}
	else
	{
		if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
		else setEventMessages($object->error,null,'errors');
	}
}





/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Priceunits','');

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
if ($action == 'list' || empty($id) && $abc)
{
    $sql = "SELECT";
    $sql.= " t.rowid,";
    
		$sql.= " t.entity,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.fk_country,";
		$sql.= " t.social_benefit,";
		$sql.= " t.tax_labor,";
		$sql.= " t.tools,";
		$sql.= " t.overhead,";
		$sql.= " t.utility,";
		$sql.= " t.tax_transaction,";
		$sql.= " t.exchange_rate,";
		$sql.= " t.decimal_number,";
		$sql.= " t.globalItem,";
		$sql.= " t.date_create,";
		$sql.= " t.date_delete,";
		$sql.= " t.tms,";
		$sql.= " t.by_default,";
		$sql.= " t.statut";

    
    $sql.= " FROM ".MAIN_DB_PREFIX."parameter as t";
    $sql.= " WHERE field3 = 'xxx'";
    $sql.= " ORDER BY field1 ASC";

    print '<table class="noborder">'."\n";
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('field1'),$_SERVER['PHP_SELF'],'t.field1','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('field2'),$_SERVER['PHP_SELF'],'t.field2','',$param,'',$sortfield,$sortorder);
    print '</tr>';

    dol_syslog($script_file, LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                print '<tr><td>';
                print $obj->field1;
                print $obj->field2;
                print '</td></tr>';
            }
            $i++;
        }
    }
    else
    {
        $error++;
        dol_print_error($db);
    }

    print '</table>'."\n";
}



// Part to create
if ($action == 'create')
{
	print_fiche_titre($langs->trans("NewParameter"));

	dol_fiche_head();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Exchangerate").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any" name="exchange_rate" value="'.$object->exchange_rate.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("City").'</td><td>';
	print '<input class="flat" type="text" size="6" name="fk_city" value="'.$object->fk_city.'">';
	print '</td></tr>';

	print '<tr><td colspan="2"><h2>'.$langs->trans("Incidencias para costos indirectos").'</h2></td>';
	print '</tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Socialbenefit").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="social_benefit" value="'.$object->social_benefit.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Impuestos IVA Mano de Obra").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="tax_labor" value="'.$object->tax_labor.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Tools").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="tools" value="'.$object->tools.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Overhead").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="overhead" value="'.$object->overhead.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Utility").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="utility" value="'.$object->utility.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Taxtransaction").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="tax_transaction" value="'.$object->tax_transaction.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Decimalnumber").'</td><td>';
	print '<input class="flat" type="number" min="0" max="5" size="6" name="decimal_number" value="'.$object->decimal_number.'">';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';

	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

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
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Exchangerate").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any" name="exchange_rate" value="'.$object->exchange_rate.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("City").'</td><td>';
	print '<input class="flat" type="text" size="6" name="fk_city" value="'.$object->fk_city.'">';
	print '</td></tr>';

	print '<tr><td colspan="2"><h2>'.$langs->trans("Incidencias para costos indirectos").'</h2></td>';
	print '</tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Socialbenefit").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="social_benefit" value="'.$object->social_benefit.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Impuestos IVA Mano de Obra").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="tax_labor" value="'.$object->tax_labor.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Tools").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="tools" value="'.$object->tools.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Overhead").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="overhead" value="'.$object->overhead.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Utility").'</td><td>';
	print '<input class="flat" type="number"  min="0" max="100"  step="any"  size="6" name="utility" value="'.$object->utility.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Taxtransaction").'</td><td>';
	print '<input class="flat" type="number" min="0" max="100" step="any"  size="6" name="tax_transaction" value="'.$object->tax_transaction.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Decimalnumber").'</td><td>';
	print '<input class="flat" type="number" min="0" max="5" size="6" name="decimal_number" value="'.$object->decimal_number.'">';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';

	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	print '</form>';

	dol_fiche_end();
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td width="20%">'.$langs->trans("Exchangerate").'</td><td>';
	print price($object->exchange_rate);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("City").'</td><td>';
	print $object->fk_city;
	print '</td></tr>';

	print '<tr><td colspan="2"><h2>'.$langs->trans("Incidencias para costos indirectos").'</h2></td>';
	print '</tr>';

	print '<tr><td>'.$langs->trans("Socialbenefit").'</td><td>';
	print price($object->social_benefit).' %';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Labortax").'</td><td>';
	print price($object->tax_labor).' %';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Tools").'</td><td>';
	print price($object->tools).' %';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Overhead").'</td><td>';
	print price($object->overhead).' %';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Utility").'</td><td>';
	print price($object->utility).' %';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Taxtransaction").'</td><td>';
	print price($object->tax_transaction).' %';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Decimalnumber").'</td><td>';
	print $object->decimal_number;
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
	    if ($user->rights->priceunits->tea->mod)
	      {
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
	      }

	    if ($user->rights->priceunits->tea->del)
	      {
		if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
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


	// Example 2 : Adding links to objects
	// The class must extends CommonObject class to have this method available
	//$somethingshown=$object->showLinkedObjectBlock();

}


// End of page
llxFooter();
$db->close();
