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
 *   	\file       dev/Unitss/Units_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-04 05:23
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
dol_include_once('/budget/unit/class/units.class.php');
dol_include_once('/budget/lib/budget.lib.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id	    = GETPOST('id','int');
$action	    = GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam    = GETPOST('myparam','alpha');

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='create';

// Load object if id or ref is provided as parameter
$object=new Units($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
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
	$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunit/unit/unit.php',1);
	header("Location: ".$urltogo);
	exit;
      }

    $error=0;

    /* object_prop_getpost_prop */
    $object->ref=GETPOST("ref",'alpha');
    $object->detail=GETPOST("detail".'alpha');
    $object->fk_type=GETPOST("fk_type".'int');
    
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
	    $urltogo=$backtopage?$backtopage:dol_buildpath('/budget/unit/unit.php',1);
	    header("Location: ".$urltogo);
	    exit;
	  }
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
    $error=0;
    if ($object->fetch($id)>0)
      {
	/* object_prop_getpost_prop */
	$object->ref=GETPOST("ref",'alpha');
	$object->detail=GETPOST("detail".'alpha');
	$object->fk_type=GETPOST("fk_type".'int');
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
    else
      {
	$action='edit';
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
	header("Location: ".dol_buildpath('/budget/unit/unit.php',1));
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

llxHeader('','MyPageName','');

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
if ($action == 'list' || empty($id))
{
    $sql = "SELECT";
    $sql.= " t.rowid,";
    
    $sql.= " t.entity,";
    $sql.= " t.fk_type,";
    $sql.= " t.ref,";
    $sql.= " t.detail,";
    $sql.= " t.fk_user_create,";
    $sql.= " t.date_create,";
    $sql.= " t.tms,";
    $sql.= " t.active,";
    $sql.= " u.code ";
    
    $sql.= " FROM ".MAIN_DB_PREFIX."units as t";
    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_unit AS u ON t.fk_type = u.rowid";
    $sql.= " WHERE t.entity = ".$conf->entity;
    $sql.= " ORDER BY $sortfield $sortorder";
    $sql.= $db->plimit($limit+1, $offset);

    print '<table class="noborder">'."\n";
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.detail','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'t.fk_type','',$param,'',$sortfield,$sortorder);
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
                print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'">'.$obj->ref.'</a>';
                print '</td><td>';
                print $obj->detail;
                print '</td><td>';
                print $obj->code;
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
    print_fiche_titre($langs->trans("New"));
    
    dol_fiche_head();
    
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    
    print '<table class="border centpercent">'."\n";
    print '<tr><td class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
    print '<input class="flat" type="text" size="36" name="label" value="'.$label.'">';
    print '</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Detail").'</td><td>';
    print '<input class="flat" type="text" size="50" name="detail" value="'.$detail.'">';
    print '</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
    print select_typeunit($fk_unit,'fk_unit','',1);
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
    $object->fetch($id,$ref);
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="hidden" name="id" value="'.$object->id.'">';
    
    print '<table class="border centpercent">'."\n";
    print '<tr><td class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
    print '<input class="flat" type="text" size="36" name="ref" value="'.$object->ref.'">';
    print '</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Detail").'</td><td>';
    print '<input class="flat" type="text" size="50" name="detail" value="'.$object->detail.'">';
    print '</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
    print select_typeunit($object->fk_type,'fk_unit','',1);
    print '</td></tr>';
    
    print '</table>'."\n";
    
    print '<br>';
    
    print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"></center>';
    
    print '</form>';
    
    dol_fiche_end();
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
  {
    dol_fiche_head();
    $object->fetch($id,$ref);

    // Confirm delete third party
    if ($action == 'delete')
      {
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteunit"),$langs->trans("Confirmdeleteunit").' '.$object->ref,"confirm_delete",'',0,2);
	if ($ret == 'html') print '<br>';
      }

    print '<table class="border centpercent">'."\n";
    print '<tr><td class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
    print $object->ref;
    print '</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Detail").'</td><td>';
    print $object->detail;
    print '</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
    print select_typeunit($object->fk_type,'fk_unit','',0,1);
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
	if ($user->rights->budget->tea->mod)
	  {
	    print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
	  }
	
	if ($user->rights->budget->tea->del)
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
