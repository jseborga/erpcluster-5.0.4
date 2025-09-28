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
*   	\file       dev/Csindexes/Csindexes_page.php
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
dol_include_once('/contab/class/cscurrencytypeext.class.php');
dol_include_once('/contab/class/csindexesext.class.php');
dol_include_once('/contab/lib/contab.lib.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("contab@contab");

// Get parameters
$id		= GETPOST('id','int');
$rid		= GETPOST('rid','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;
// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
$object   = new Csindexesext($db);
$objectct = new Cscurrencytypeext($db);

$currency_array = $objectct->get_currency_type_array();
//search last exchange rate
$objectcop = new Csindexesext($db);
$objectcop->fetch_last($country);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
if ($action == 'add' && $user->rights->contab->currency->crear)
{
	$error = 0;
	$date_ind=dol_mktime(12, 0, 0, $_POST["date_indmonth"], $_POST["date_indday"], $_POST["date_indyear"]);

	$object->date_ind  = $date_ind;
	$object->currency1 = $_POST["currency1"]+0;
	$object->currency2 = $_POST["currency2"]+0;
	$object->currency3 = $_POST["currency3"]+0;
	$object->currency4 = $_POST["currency4"]+0;
	$object->currency5 = $_POST["currency5"]+0;
	$object->currency6 = $_POST["currency6"]+0;
	$object->country   = $_POST["country"];
	$object->state     = 1;
	$j = 1;
	foreach((array) $currency_array AS $i => $data)
	{
		If ($data['registry'] == 2)
		{
			$currency = 'currency'.$j;
			if ($objectcop->$currency > 0 && $objectcop->$currency != $_POST['currency'.$j])
			{
				$db->begin();
				$sql = " UPDATE ".MAIN_DB_PREFIX."cs_indexes SET ";
				$sql.= " ".$currency." = ".$_POST['currency'.$j] ;
				$sql.= " WHERE MONTH(date_ind) = ".$_POST["date_indmonth"];
				$sql.= " AND YEAR(date_ind) = ".$_POST["date_indyear"] ;
				$rsql = $db->query($sql);
				if ($rsql > 0)
					$db->commit();
				else
				{
					$db->rollback();
					$error++;
				}
			}
		}
		$j++;
	}
	if (empty($error))
	{
		$id=$object->create($user);
		if ($id > 0)
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header("Location: fiche.php?id=".$id);
			exit;
	    // Creation OK
		}
		else
		{
			setEventMessages($object->error,$object->errors,'errors');
	    // Creation KO
			$mesg=$object->error;
			$action="create";
		}
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans('Errorinupdatingdatabase').'</div>';
		$action='create';

	}
}

//update
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$object->fetch($_REQUEST["id"]);
	$date_ind=dol_mktime(12, 0, 0, $_POST["date_indmonth"], $_POST["date_indday"], $_POST["date_indyear"]);

	$object->date_ind  = $date_ind;
	$object->currency1 = $_POST["currency1"]+0;
	$object->currency2 = $_POST["currency2"]+0;
	$object->currency3 = $_POST["currency3"]+0;
	$object->currency4 = $_POST["currency4"]+0;
	$object->currency5 = $_POST["currency5"]+0;
	$object->currency6 = $_POST["currency6"]+0;
	$object->country   = $_POST["country"];
	$object->state     = 1;
	$result=$object->update($user);
	if ($result > 0)
	{
		header("Location: fiche.php?id=".$id);
		exit;
	// Creation OK
	}
	else
	{
	// Creation KO
		$mesg=$object->error;
		$action="edit";
	}
}


// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->currency->crear)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salaries/currency/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Manageraccounts"),$help_url);

$form=new Form($db);
// echo '<pre>';
// print_r($conf);
// echo '</pre>';

if ($action == 'create' && $user->rights->contab->currency->crear)
{
	print_fiche_titre($langs->trans("Newexchangerate"));


	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="country" value="'.$country.'">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

    // date_ind
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
	print $form->select_date($object->date_ind,'date_ind',0,0,0,"perso");

	print '</td></tr>';
	$i = 1;
	foreach((array) $currency_array AS $j => $data)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans($data['ref']).'</td><td colspan="2">';
		$currency = 'currency'.$i;
		If ($data['registry'] == 2)
		{
			print '<input id="currency'.$i.'" type="text" value="'.$objectcop->$currency.'" name="currency'.$i.'" size="5">';	    
		}
		else
			print '<input id="currency'.$i.'" type="text" value="'.$object->$currency.'" name="currency'.$i.'" size="8">';
		print '</td></tr>';
		$i++;
	}

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["id"])
	{
		dol_htmloutput_mesg($mesg);      
		$result = $object->fetch($_GET["id"]);
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

	 	dol_fiche_head($head, 'Currencytype', $langs->trans("Currencytype"), 0, 'generic');


	     // Delete course materials
	 	if ($action == 'deldet' && $user->rights->college->delcourses)
	 	{
	 		$objectcm->fetch($_REQUEST["rid"]);
	 		$result=$objectcm->delete($user);
	 		if ($result <= 0)
	 		{
	 			$mesg='<div class="error">'.$objectcm->error.'</div>';
	 		}
	 		$action='createdet';
	 	}

	     // Confirm delete third party
	 	if ($action == 'delete')
	 	{
	 		$form = new Form($db);
	 		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletecurrencytype"),$langs->trans("Confirmdeletecurrencytype",$object->ref.' '.$object->label),"confirm_delete",'',0,2);
	 		if ($ret == 'html') print '<br>';
	 	}

	 	print '<table class="border" width="100%">';

	     // date_ind
	 	print '<tr><td widht="20%">'.$langs->trans('Date').'</td><td colspan="2">';
	 	print dol_print_date($object->date_ind,'day');

	 	print '</td></tr>';
	 	$i = 1;
	 	foreach((array) $currency_array AS $j => $data)
	 	{
	 		print '<tr><td>'.$langs->trans($data['ref']).'</td><td colspan="2">';
	 		$currency = 'currency'.$i;
	 		print price($object->$currency,4);
	 		print '</td></tr>';
	 		$i++;
	 	}


	 	print '</table>';

	 	print '</div>';


	 	/* ************************************** */
	 	/*                                        */
	 	/* Barre d'action                         */
	 	/*                                        */
	 	/* ************************************** */

	 	print "<div class=\"tabsAction\">\n";

	 	if ($action == '')
	 	{
	 		if ($user->rights->contab->currency->crear)
	 			print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	 		else
	 			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	 		if ($user->rights->contab->currency->crear && $object->state==0)
	 			print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	 		else
	 			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";


	 		if ($user->rights->contab->currency->crear  && $object->state==0)
	 			print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	 		else
	 			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	 	}	  
	 	print "</div>";
	 }
	 /*
	  * Edition fiche
	  */
	 if (($action == 'edit' || $action == 're-edit') && 1)
	 {
	     //print_fiche_titre($langs->trans("ApplicationsEdit"),$mesg);
	 	print_fiche_titre($langs->trans("ApplicationsEdit"));

	 	print '<form action="fiche.php" method="POST">';
	 	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	 	print '<input type="hidden" name="action" value="update">';
	 	print '<input type="hidden" name="id" value="'.$object->id.'">';
	 	print '<input type="hidden" name="country" value="'.$country.'">';

	 	print '<table class="border" width="100%">';

	     // date_ind
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
	 	print $form->select_date($object->date_ind,'date_ind',0,0,0,"perso");

	 	print '</td></tr>';
	 	$i = 1;
	 	foreach((array) $currency_array AS $j => $data)
	 	{
	 		print '<tr><td class="fieldrequired">'.$langs->trans($data['ref']).'</td><td colspan="2">';
	 		$currency = 'currency'.$i;
	 		print '<input id="currency'.$i.'" type="text" value="'.$object->$currency.'" name="currency'.$i.'" size="8">';
	 		print '</td></tr>';
	 		$i++;
	 	}

	 	print '</table>';

	 	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	 	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	 	print '</form>';

	 }

      /////
	}
}

// End of page
llxFooter();
$db->close();
?>
