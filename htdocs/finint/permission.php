<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/product/stock/fiche.php
 *	\ingroup    stock
 *	\brief      Page fiche entrepot
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/finint/class/accountuser.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/bank.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formbank.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

$langs->load("others");
$langs->load("companies");
$langs->load("finint");

$action=GETPOST('action');

$id = GETPOST("id",'int');

$mesg = '';

// Security check
//$result=restrictedArea($user,'stock');

$accountuser = new Accountuser($db);
// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('warehousecard'));

$id=GETPOST("id",'int');
$idr=GETPOST("idr",'int');
$fk_user = GETPOST('fk_user');
// Load object if id or ref is provided as parameter
$account=new Account($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$account->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

/*
 * Actions
 */
if ($action == 'adduser' && $user->rights->finint->bank->crear)
{
	$error = 0;
	if ($fk_user > 0 && !empty($id))
	{
		$obj = new Accountuser($db);
		$obj->fk_account = $id;
		$obj->fk_user = $fk_user;
		$obj->fk_user_create = $user->id;
		$obj->fk_user_mod = $user->id;
		$obj->date_create = dol_now();
		$obj->tms = dol_now();
		$obj->status = 1;
		$res = $obj->create($user);
		if ($res > 0)
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			setEventMessages($obj->error,$obj->errors,'errors');
			$action = 'createuser';
		}
	}
	else
	{
		setEventMessages($langs->trans('ErrorSelectuser'),null,'mesgs');
		$action = 'createuser';
	}
	
}

if ($action == 'updateuser' && $user->rights->finint->bank->crear)
{
	$error = 0;
	if ($fk_user > 0 && !empty($id))
	{
		$obj = new Accountuser($db);
		$obj->fetch($idr);
		if ($obj->id == $idr && $obj->fk_account == $id)
		{
			$obj->fk_user = $fk_user;
			$obj->fk_user_mod = $user->id;
			$obj->tms = dol_now();
			$obj->statut = 1;
			$res = $obj->update($user);
			if ($res > 0)
			{
				setEventMessages($langs->trans('Updatesuccessfull'),null,'mesgs');
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				setEventMessages($obj->error,$obj->errors,'errors');
				$action = 'edituser';
			}
		}
		else
		{
			setEventMessages($langs->trans('ErrorSelectuser'),null,'mesgs');
			$action = 'edituser';
		}
	}
	else
	{
		setEventMessages($langs->trans('ErrorSelectuser'),null,'mesgs');
		$action = 'edituser';
	}
	
}

if ($action == 'deleteuser' && $user->rights->finint->bank->crear)
{
	$error = 0;
	$obj = new Accountuser($db);
	$obj->fetch(GETPOST('idr','int'));
	if ($obj->id == GETPOST('idr','int') && $obj->fk_account == $id)
	{
		$res = $obj->delete($user);
		if ($res > 0)
		{
			setEventMessages($langs->trans('Deletesuccessfull'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			setEventMessages($obj->error,$obj->errors,'errors');
			$action = '';
		}
	}
	else
		$action = '';
}


/*
 * View
 */

//$productstatic=new Product($db);

$form=new Form($db);

$help_url='EN:Module_Finint_En|FR:Module_Finint|ES:M&oacute;dulo_Finint';
llxHeader("",$langs->trans("Bank"),$help_url);


if ($id)
{


	dol_htmloutput_mesg($mesg);
	//$account = new Account($db);
	// if ($_GET["id"])
	//   {
	// 	$account->fetch($_GET["id"]);
	//   }
	// if ($_GET["ref"])
	//   {
	// 	$account->fetch(0,$_GET["ref"]);
	// 	$_GET["id"]=$account->id;
	//   }
	
	/*
	 * Affichage onglets
	 */
	
	// Onglets
	$head=bank_prepare_head($account);
	dol_fiche_head($head, 'Permission', $langs->trans("FinancialAccount"),0,'account');
	
	/*
	 * Confirmation to delete
	 */
	if ($action == 'delete')
	{
		print $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$account->id,$langs->trans("DeleteAccount"),$langs->trans("ConfirmDeleteAccount"),"confirm_delete");

	}
	
	print '<table class="border" width="100%">';
	
	$linkback = '<a href="'.DOL_URL_ROOT.'/compta/bank/index.php">'.$langs->trans("BackToList").'</a>';
	
	// Ref
	print '<tr><td width="25%">'.$langs->trans("Ref").'</td>';
	print '<td colspan="3">';
	print $form->showrefnav($account, 'ref', $linkback, 1, 'ref');
	print '</td></tr>';
	
	// Label
	print '<tr><td>'.$langs->trans("Label").'</td>';
	print '<td colspan="3">'.$account->label.'</td></tr>';
	
	// Type
	print '<tr><td>'.$langs->trans("AccountType").'</td>';
	print '<td colspan="3">'.$account->type_lib[$account->type].'</td></tr>';
	
	// Currency
	print '<tr><td>'.$langs->trans("Currency").'</td>';
	print '<td colspan="3">';
	$selectedcode=$account->account_currency_code;
	if (! $selectedcode) $selectedcode=$conf->currency;
	print $langs->trans("Currency".$selectedcode);
	print '</td></tr>';
	
	// Status
	print '<tr><td>'.$langs->trans("Status").'</td>';
	print '<td colspan="3">'.$account->getLibStatut(4).'</td></tr>';
	
	// Country
	print '<tr><td>'.$langs->trans("BankAccountCountry").'</td><td>';
	if ($account->country_id > 0)
	{
		$img=picto_from_langcode($account->country_code);
		print $img?$img.' ':'';
		print getCountry($account->getCountryCode(),0,$db);
	}
	print '</td></tr>';
	
	// State
	print '<tr><td>'.$langs->trans('State').'</td><td>';
	if ($account->state_id > 0) print getState($account->state_id);
	print '</td></tr>';
	
	// Conciliate
	print '<tr><td>'.$langs->trans("Conciliable").'</td>';
	print '<td colspan="3">';
	$conciliate=$account->canBeConciliated();
	if ($conciliate == -2) print $langs->trans("No").' ('.$langs->trans("CashAccount").')';
	else if ($conciliate == -3) print $langs->trans("No").' ('.$langs->trans("Closed").')';
	else print ($account->rappro==1 ? $langs->trans("Yes") : ($langs->trans("No").' ('.$langs->trans("ConciliationDisabled").')'));
	print '</td></tr>';
	
	print '<tr><td>'.$langs->trans("BalanceMinimalAllowed").'</td>';
	print '<td colspan="3">'.$account->min_allowed.'</td></tr>';
	
	print '<tr><td>'.$langs->trans("BalanceMinimalDesired").'</td>';
	print '<td colspan="3">'.$account->min_desired.'</td></tr>';
	
	print '<tr><td>'.$langs->trans("Web").'</td><td colspan="3">';
	if ($account->url) print '<a href="'.$account->url.'" target="_gobank">';
	print $account->url;
	if ($account->url) print '</a>';
	print "</td></tr>\n";
	
	print '<tr><td class="tdtop">'.$langs->trans("Comment").'</td>';
	print '<td colspan="3">'.dol_htmlentitiesbr($account->comment).'</td></tr>';
	
	// Other attributes
	$parameters=array('colspan' => 3);
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$account,$action);    // Note that $action and $object may have been modified by hook
	if (empty($reshook) && ! empty($extrafields->attribute_label))
	{
		print $account->showOptionals($extrafields);
	}
	
	print '</table>';
	
	print '<br>';
	
	if ($account->type == 0 || $account->type == 1)
	{
		print '<table class="border" width="100%">';

		print '<tr><td valign="top" width="25%">'.$langs->trans("BankName").'</td>';
		print '<td colspan="3">'.$account->bank.'</td></tr>';

	// Show fields of bank account
		$fieldlists='BankCode DeskCode AccountNumber BankAccountNumberKey';
		if (! empty($conf->global->BANK_SHOW_ORDER_OPTION))
		{
			if (is_numeric($conf->global->BANK_SHOW_ORDER_OPTION))
			{
				if ($conf->global->BANK_SHOW_ORDER_OPTION == '1') $fieldlists='BankCode DeskCode BankAccountNumberKey AccountNumber';
			}
			else $fieldlists=$conf->global->BANK_SHOW_ORDER_OPTION;
		}
		$fieldlistsarray=explode(' ',$fieldlists);

		foreach($fieldlistsarray as $val)
		{
			if ($val == 'BankCode')
			{
				if ($account->useDetailedBBAN() == 1)
				{
					print '<tr><td>'.$langs->trans("BankCode").'</td>';
					print '<td colspan="3">'.$account->code_banque.'</td>';
					print '</tr>';
				}
			}
			if ($val == 'DeskCode')
			{
				if ($account->useDetailedBBAN() == 1)
				{
					print '<tr><td>'.$langs->trans("DeskCode").'</td>';
					print '<td colspan="3">'.$account->code_guichet.'</td>';
					print '</tr>';
				}
			}

			if ($val == 'BankCode')
			{
				if ($account->useDetailedBBAN() == 2)
				{
					print '<tr><td>'.$langs->trans("BankCode").'</td>';
					print '<td colspan="3">'.$account->code_banque.'</td>';
					print '</tr>';
				}
			}

			if ($val == 'AccountNumber')
			{
				print '<tr><td>'.$langs->trans("BankAccountNumber").'</td>';
				print '<td colspan="3">'.$account->number.'</td>';
				print '</tr>';
			}

			if ($val == 'BankAccountNumberKey')
			{
				if ($account->useDetailedBBAN() == 1)
				{
					print '<tr><td>'.$langs->trans("BankAccountNumberKey").'</td>';
					print '<td colspan="3">'.$account->cle_rib.'</td>';
					print '</tr>';
				}
			}
		}

		$ibankey="IBANNumber";
		$bickey="BICNumber";
		if ($account->getCountryCode() == 'IN') $ibankey="IFSC";
		if ($account->getCountryCode() == 'IN') $bickey="SWIFT";

		print '<tr><td>'.$langs->trans($ibankey).'</td>';
		print '<td colspan="3">'.$account->iban.'&nbsp;';
		if (! empty($account->iban)) {
			if (! checkIbanForAccount($account)) {
				print img_picto($langs->trans("IbanNotValid"),'warning');
			} else {
				print img_picto($langs->trans("IbanValid"),'info');
			}
		}
		print '</td></tr>';

		print '<tr><td>'.$langs->trans($bickey).'</td>';
		print '<td colspan="3">'.$account->bic.'&nbsp;';
		if (! empty($account->bic)) {
			if (! checkSwiftForAccount($account)) {
				print img_picto($langs->trans("SwiftNotValid"),'warning');
			} else {
				print img_picto($langs->trans("SwiftValid"),'info');
			}
		}
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("BankAccountDomiciliation").'</td><td colspan="3">';
		print nl2br($account->domiciliation);
		print "</td></tr>\n";

		print '<tr><td>'.$langs->trans("BankAccountOwner").'</td><td colspan="3">';
		print $account->proprio;
		print "</td></tr>\n";

		print '<tr><td>'.$langs->trans("BankAccountOwnerAddress").'</td><td colspan="3">';
		print nl2br($account->owner_address);
		print "</td></tr>\n";

		print '</table>';
		print '<br>';
	}
	
	print '<table class="border" width="100%">';
	// Accountancy code
	print '<tr><td width="25%">'.$langs->trans("AccountancyCode").'</td>';
	print '<td colspan="3">'.$account->account_number.'</td></tr>';
	
	// Accountancy journal
	if (! empty($conf->accounting->enabled))
	{
		print '<tr><td>'.$langs->trans("AccountancyJournal").'</td>';
		print '<td colspan="3">'.$account->accountancy_journal.'</td></tr>';
	}
	
	print '</table>';

	dol_fiche_end();
	//print '</div>';
	
	

	dol_fiche_head();

	print '<table class="noborder" width="100%">';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("User"),"", "p.ref","&amp;id=".$id,"","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Login"),"", "p.label","&amp;id=".$id,"","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	print "</tr>";
	
	$totalunit=0;
	$totalvalue=$totalvaluesell=0;
	$filter = array('1'=>1);
	$filterstatic = " AND t.fk_account = ".$id;
	$accountuser->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, $filtermode,$filterstatic);
	$var = true;
	$objuser = new User($db);
	
	foreach ((array) $accountuser->lines AS $j => $objp)
	{
		$var=!$var;
		$aFilter = array();
		if ($action != 'edituser')
			$aFilter[$objp->fk_user] = $objp->fk_user;
		if ($action == 'edituser' && $idr == $objp->id)
		{
			$objnew = $objp;
			include DOL_DOCUMENT_ROOT.'/finint/tpl/permission.tpl.php';	    
		}
		else
		{
			//print '<td>'.dol_print_date($objp->datem).'</td>';
			print "<tr ".$bc[$var].">";
			$objuser->fetch($objp->fk_user);
			if ($objuser->id == $objp->fk_user)
			{
				print "<td>";
				print $objuser->lastname.' '.$objuser->firstname;
				print '</td>';
				print '<td>'.$objuser->login.'</td>';
			}
			else
			{
				print "<td>";
				print $langs->trans('Notregistered');
				print '</td>';
				print '<td>&nbsp;</td>';
			}
			print '<td align="right">';
			if ($user->rights->finint->bank->crear)
			{
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objp->id.'&action=edituser">'.img_picto($langs->trans('Edit'),'button_edit').'</a>';
				print '&nbsp;';
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objp->id.'&action=deleteuser">'.img_picto($langs->trans('Edit'),'delete').'</a>';
			}
			print '</td>';
			print "</tr>";
		}
	}

	//registro nuevo
	if ($action == 'createuser')
	{
		$objnew = new Accountuser($db);
		include DOL_DOCUMENT_ROOT.'/finint/tpl/permission.tpl.php';
	}
	print "</table>\n";
	
	//ACTIONS USUARIO
	print "<div class=\"tabsAction\">\n";
	
	if (empty($action))
	{
		if ($user->rights->finint->bank->crear)
			print '<a class="butAction" href="permission.php?action=createuser&id='.$id.'">'.$langs->trans("Adduser").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Adduser")."</a>";
	}			
	print "</div>";

	dol_fiche_end();
}



llxFooter();

$db->close();
