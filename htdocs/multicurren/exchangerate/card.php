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

dol_include_once('/multicurren/class/cscurrencytypeext.class.php');
dol_include_once('/multicurren/class/csindexescountryext.class.php');
dol_include_once('/multicurren/lib/multicurrency.lib.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("multicurren");

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
$object   = new Csindexescountryext($db);
$objectct = new Cscurrencytypeext($db);

//search last exchange rate
$objectcop = new Csindexescountryext($db);

//$objectct->get_currency_type_array();
$filterstatic = " AND t.entity = ".$conf->entity;
$objectct->fetchAll('ASC', 'order_currency', 0,0,array(1=>1),'AND',$filterstatic);
$aRegistry = array();
foreach((array) $objectct->lines AS $i => $objdata)
{
	$aRegistry[$objdata->ref] = $objdata->registry;
}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
if ($action == 'add')
{
	$error = 0;
	$date_ind=dol_mktime(12, 0, 0, $_POST["date_indmonth"], $_POST["date_indday"], $_POST["date_indyear"]);

	$aPost = $_POST;
	foreach ($aPost AS $j => $value)
	{
		$aData = explode('_',$j);
		if ($aData[0] == 'currency')
		{
			$aCurrency[$aData[1]] = $value;
		}
	}
	if (count($aCurrency)>0)
	{
		$db->begin();
		$new = dol_now();
		foreach ($aCurrency AS $ref => $value)
		{
			$lAdd = true;
			if ($aRegistry[$ref] == 2)
			{
				//es de tipo mensual buscamos, actualizamos o registro nuevo
				$filter = " AND MONTH(t.date_ind) = ".$_POST["date_indmonth"];
				$filter.= " AND YEAR(t.date_ind) = ".$_POST["date_indyear"] ;
				$filter.= " AND t.ref = '".$ref."'" ;
				$filter.= " AND t.entity = '".$conf->entity."'" ;
				$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				if ($res == 1)
				{
					$lAdd = false;
					$object->amount = $value;
					$object->fk_user_mod = $user->id;
					$object->dateu = $new;
					$res = $object->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
				}
				if ($res > 1)
				{
					foreach ($object->lines AS $j => $obj)
					{
						$res = $obj->delete($user);
						if ($res <=0)
						{
							$error++;
							setEventMessages($object->error,$object->errors,'errors');
						}
					}
					if (!$error)
						$lAdd = true;
				}
			}
			else
			{
				//buscamos si existe el registro diario
				$filter = " AND MONTH(t.date_ind) = ".$_POST["date_indmonth"];
				$filter.= " AND YEAR(t.date_ind) = ".$_POST["date_indyear"] ;
				$filter.= " AND DAY(t.date_ind) = ".$_POST["date_indday"] ;
				$filter.= " AND t.ref = '".$ref."'" ;
				$filter.= " AND t.entity = '".$conf->entity."'" ;
				$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				if ($res == 1)
				{
					$lAdd = false;
					$object->amount = $value;
					$object->fk_user_mod = $user->id;
					$object->dateu = $new;
					$res = $object->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
				}
			}
			if ($lAdd)
			{
				$object->entity = $conf->entity;
				$object->ref = $ref;
				$object->date_ind  = $date_ind;
				$object->amount = $value;
				$object->fk_user_create = $user->id;
				$object->fk_user_mod = $user->id;
				$object->datec = $new;
				$object->dateu = $new;
				$object->tms = $new;
				$object->status = 1;
				$res = $object->create($user);
				if ($res <=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}

			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Savesuccessfull'),null,'mesgs');
			header('Location: '.DOL_URL_ROOT.'/multicurren/exchangerate/list.php');
			exit;
		}
	}
	if (empty($error))
	{
		$id=$object->create($user);
		if ($id > 0)
		{
			header("Location: fiche.php?id=".$id);
			exit;
		// Creation OK
		}
		else
		{
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
	$object->currency1 = $_POST["currency1"];
	$object->currency2 = $_POST["currency2"];
	$object->currency3 = $_POST["currency3"];
	$object->currency4 = $_POST["currency4"];
	$object->currency5 = $_POST["currency5"];
	$object->currency6 = $_POST["currency6"];
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
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salaries->currtype->del)
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
$aRegistry = array(1=>$langs->trans('Dialy'),2=>$langs->trans('Monthly'));

$help_url='EN:Module_Salaries_En|FR:Module_Salaries|ES:M&oacute;dulo_Salaries';
llxHeader("",$langs->trans("Exchangerate"),$help_url);

$form=new Form($db);
// echo '<pre>';
// print_r($conf);
// echo '</pre>';

if ($action == 'create')
{
	print_fiche_titre($langs->trans("Newexchangerate"));


	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// date_ind
	print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
	print $form->select_date($object->date_ind,'date_ind',0,0,0,"perso");

	print '</td></tr>';
	$i = 1;
	foreach((array) $objectct->lines AS $j => $objdata)
	{
		print '<tr>';
		if ($i == 1)
			print '<td class="fieldrequired">';
		else
			print '<td>';
		print ($objdata->label?$objdata->label:'');
		print ' ';
		print currency_name($objdata->ref,1);
		print ' ('.$langs->getCurrencySymbol($objdata->ref).')';
		print '</td>';
		print '<td colspan="2">';
		$currency = 'currency'.$i;
		if ($objdata->registry == 2)
		{
			$objectcop->fetch_last($objdata->ref);
			print '<input id="currency_'.$i.'" type="number" min="0" step="any" value="'.(GETPOST('currency_'.$objdata->ref)?GETPOST('currency_'.$objdata->ref):$objectcop->amount).'" name="currency_'.$objdata->ref.'">'.' '.$aRegistry[$objdata->registry];
		}
		else
			print '<input id="currency_'.$i.'" type="number" min="0" step="any" value="'.(GETPOST('currency_'.$objdata->ref)?GETPOST('currency_'.$objdata->ref):$objectcop->amount).'" name="currency_'.$objdata->ref.'">';
		print '</td></tr>';
		$i++;
	}

	print '</table>';
	if (count($objectct->lines)>0)
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	else
		setEventMessages($langs->trans('Nocoinsaredefined'),null,'warnings');
	print '</form>';
}
else
{
	if ($id)
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

	 	dol_fiche_head($head, 'Currencytype', $langs->trans("Exchangerate"), 0, 'generic');


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

	 	print '<tr><td widht="20%">'.$langs->trans('Amount').'</td><td colspan="2">';
	 	print price($object->amount);

	 	print '</td></tr>';


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
	 		if ($user->rights->multicurrency->exch->crear)
	 			print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	 		else
	 			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	 		if ($user->rights->multicurrency->exch->crear && $object->state==0)
	 			print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	 		else
	 			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";


	 		if ($user->rights->multicurrency->exch->del  && $object->state==0)
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

	 	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
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
	 	foreach((array) $objectct->array AS $j => $objdata)
	 	{
	 		print '<tr>';
	 		if ($i == 1)
	 			print '<td class="fieldrequired">';
	 		else
	 			print '<td>';
	 		print currency_name($objdata->ref,1);
	 		print ' ('.$langs->getCurrencySymbol($objdata->ref).')';
	 		print '</td><td colspan="2">';
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
