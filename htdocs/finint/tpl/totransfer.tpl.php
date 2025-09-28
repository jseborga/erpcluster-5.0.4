<?php
/* * Copyright (C) 2016-2016 Ramiro Queso        <ramiroques@gmail.com>
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
 *	    \file       htdocs/request/tpl/discharg.tpl.php
 *		\ingroup    requestcash
 *		\brief      List of details of bank transactions for an account
 */

$langs->load("bills");
$langs->load("finint@finint");
$langs->load("compta");

//$id = (GETPOST('id','int') ? GETPOST('id','int') : GETPOST('account','int'));
// $ref = GETPOST('ref','alpha');
// $action=GETPOST('action','alpha');
// $user_to = GETPOST('user_to','int');
// $fk_account = GETPOST('fk_account','int');
// $confirm=GETPOST('confirm','alpha');

//if (!$user->rights->request->desc->leer) accessforbidden();

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref :''));
$fieldtype = (! empty($ref) ? 'ref' :'rowid');

$paiementtype=GETPOST('paiementtype','alpha',3);
$req_nb=GETPOST("req_nb",'',3);
$thirdparty=GETPOST("thirdparty",'',3);
$vline=GETPOST("vline");
$page=GETPOST('page','int');
$negpage=GETPOST('negpage','int');
if ($negpage)
{
	$page=$_GET["nbpage"] - $negpage;
	if ($page > $_GET["nbpage"]) $page = $_GET["nbpage"];
}

//verificacion de version
$version = substr($conf->global->MAIN_VERSION_LAST_INSTALL,0,5);
if (!empty($conf->global->MAIN_VERSION_LAST_UPGRADE))
	$version = substr($conf->global->MAIN_VERSION_LAST_UPGRADE,0,5);
//cambiamos la version de texto a numero
$version = substr($version,0,3) * 1;
$lVersion = false;
if ($version >= 3.7)
	$lVersion = true;

$mesg='';

//$object = new Account($db);
$objuser = new User($db);
$deplacement = new Deplacementext($db);
$projet = new Project($db);
if ($conf->societe->enabled)
	$soc = new Societe($db);

if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	$soc->fetch($user->societe_id);
}

$dateop=-1;

/*
 * View
 */

$societestatic=new Societe($db);
$chargestatic=new ChargeSociales($db);
$memberstatic=new Adherent($db);

$account = new Account($db);

$form = new Form($db);
$formproject = new FormProjetsAdd($db);

//recuperamos la y el usuario
$lView = true;
if (empty($object->fk_account))
	$lView = false;

//listamos los gastos realizados por el usuario
$filter = " AND k.fk_account = ".$object->fk_account;
$filter.= " AND t.fk_projet = ".$object->fk_projet;
$filter.= " AND r.fk_request_cash = ".$object->id;
$deplacement->getlist(($object->statut==4?0:$object->fk_user_create),$filter);
// //calculamos los saldos en caja y caja usuario
// //saldo bank
// $saldoBank = saldoAccount($fk_account);

// $saldoBankUser = saldoAccount($fk_account,$user_to);
$saldoBankUser = 0;
$sumadep = $sumatransf;
$sumagas = 0;
print '<table class="noborder centpercent">'."\n";
print '<tr>';
print '<td width="50%" valign="top">';
if ($lView && $user->rights->request->desc->leer)
{
	print '<table class="noborder centpercent">'."\n";

    // Fields title
	print '<tr class="liste_titre">';

    //print_liste_field_titre($langs->trans('entity'),$_SERVER['PHP_SELF'],'t.entity','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
    // print_liste_field_titre($langs->trans('Project'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
    // print_liste_field_titre($langs->trans('User'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Doc'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'t.ref','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Action'),'','','',$param,'align="right"');

	print '</tr>';
	$var = true;
	foreach ((array) $deplacement->lines AS $j => $line)
	{
		$var = !$var;
		print "<tr $bc[$var]>";
		print '<td>';
		print $line->ref;
		print '</td>';
		print '<td>';
		print dol_print_date($line->date,'day');
		print '</td>';
		print '<td>';
		print $line->num_chq;
		print '</td>';
		print '<td>';
		print $line->note_private;
		print '</td>';
		print '<td align="right">';
		print price($line->km);
		print '</td>';
		print '<td align="right">';
		if ($object->statut >=3 && $object->statut < 4)
		{
			if ($user->admin || (($object->fk_user_create == $user->id || $object->fk_user_assigned == $user->id) && $user->rights->request->desc->del))
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idrcd='.$line->rcd_id.'&action=delete_dep">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		}
		print '</td>';
		print '</tr>';
		$sumadep -= $line->km;
		$sumagas += $line->km*-1;
	}
	//armamos el total
	print '<tr class="liste_total">';
	print '<td colspan="4">'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price($sumagas).'</td>';
	print '</tr>';
	print '</table>';
}
print '</td>';
print '<td width="50%" valign="top">';
if ($lView && $user->rights->request->trans->leer)
{
		//$accountadd = new AccountAdd($db);
		//$accountadd->getlist($object->fk_account,$object->id);
	$objdeplac = new Requestcashdeplacementext($db);
	$objdeplac->getlisttransfer($object->id);
	print '<table class="noborder centpercent">'."\n";    
    // Fields title
	print '<tr class="liste_titre">';

	print_liste_field_titre($langs->trans('TransferTo'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);

	print '</tr>';
	$objaccount = new Account($db);
	$sumapar = 0;
	$var = true;
	foreach ((array) $objdeplac->lines AS $j => $line)
	{
		$var = !$var;
		print "<tr $bc[$var]>";
		print '<td>';
		$objuser->fetch($line->fk_user_to);
		if ($objuser->id == $line->fk_user_to)
		{
			$objreq = new Requestcashadd($db);
			$objreq->fetch($line->fk_request_cash_dest);
			$objaccount->fetch($line->fk_account_dest);
			print $objreq->getNomUrl(1).' '.($objaccount->id ==$line->fk_account_dest?$objaccount->getNomUrl(1):$objuser->login);
		}
		else
			print $langs->trans('Nodefined');
		print '</td>';
		print '<td>';
		print dol_print_date($line->dateo,'day');
		print '</td>';

		print '<td>';
		print $line->detail;
		print '</td>';
		print '<td align="right">';
		print price(price2num($line->amount*-1,'MT'));
		print '</td>';
		print '<td align="right">';
		if ($line->statut == 0)
			print img_picto($langs->trans('Pendiente'),DOL_URL_ROOT.'/request/img/interrogacion','',1);
		if ($line->statut == -1)
			print img_picto($langs->trans('Rejected'),DOL_URL_ROOT.'/request/img/ko','',1);
		if ($line->statut == 1)
			print img_picto($langs->trans('Accepted'),DOL_URL_ROOT.'/request/img/ok','',1);
		print '</td>';
		print '</tr>';
		if ($line->statut == 1)
		{
			$sumadep += $line->amount*-1;
			$sumapar += $line->amount*-1;
		}
	}
	//armamos el total
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price($sumapar).'</td>';
	print '</tr>';
	print '</table>';
}

print '</td>';
print '</tr>';

$saldoBankUser = price2num($sumadep,'MT');  
//revisamos el saldo real
$lAdd = true;
if ($saldoBankUser + $sumapar0 <= 0)
{
	$lAdd = false;
}

if ($action == 'close')
{
	print '<tr>';
	print '<td colspan="2">';
	include DOL_DOCUMENT_ROOT.'/request/tpl/close.tpl.php';
	print '</td>';
	print '</tr>';
}
elseif ($action == 'transfd')
{
	print '<tr>';
	print '<td colspan="2">';
	include DOL_DOCUMENT_ROOT.'/request/tpl/transfd.tpl.php';
	print '</td>';
	print '</tr>';
}
else
{
	print '<tr>';
	print '<td valign="top">';

	if ($object->statut < 4 && $saldoBankUser > 0)
	{

			//proceso para crear gastos
			//buscamos la cuenta del que desembolsa
		$accountuser = new Accountuser($db);

		if (!empty($object->fk_account))
		{
			if ($user->rights->request->desc->crear)
			{
				if ($vline)
				{
					$viewline = $vline;
				}
				else
				{
					$viewline = empty($conf->global->MAIN_SIZE_LISTE_LIMIT)?20:$conf->global->MAIN_SIZE_LISTE_LIMIT;
				}
				$result=$object->fetch($id, $ref);

				dol_htmloutput_errors($mesg);

				print_fiche_titre($langs->trans("Newspending"));

				if ($lAdd)
				{
					print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="adddeplacement">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';
					print '<input type="hidden" name="balance" value="'.$saldoBankUser.'">';
				
				dol_fiche_head();

				print '<table class="border centpercent">'."\n";
	    		// Ref
				print '<tr><td class="fieldrequired" valign="top" width="15%">'.$langs->trans("Balance").'</td>';
				print '<td>';
				$account->fetch($object->fk_account);
				//print $account->label;
				print price($saldoBankUser + $sumapar0);
				print '</td></tr>';
				print '<tr>';
				print '<td class="fieldrequired">'.$langs->trans('Qty').'</td><td>';
				print '<input type="number" class="form-control" name="quant" value="'.$objectdet->quant.'" size="5" required autofocus>';
				print '</td>';
				print '</tr>';
				print '<tr>';
				print '<td class="fieldrequired">'.$langs->trans('Unit').'</td><td>';
				print $form->selectUnits('','fk_unit');
				print '</td>';
				print '</tr>';
				print '<tr>';
				print '<td class="fieldrequired">'.$langs->trans("Date").'</td>';
				print '<td>';
				$form->select_date('','do','','','','transaction',1,1,0,0);
				print '</td>';
				print '</tr>';
				print '<tr>';
				print '<td class="fieldrequired">'.$langs->trans("Type").'</td>';
				print '<td>';
				$form->select_types_paiements((GETPOST('operation')?GETPOST('operation'):($object->courant == 2 ? 'LIQ' : 'LIQ')),'operation','2',2,1);
				print '</td></tr>';
				print '<tr>';
				print '<td>'.$langs->trans("Numero").'</td>';
				print '<td>';
				print '<input name="num_chq" class="flat" type="text" size="4" value="'.GETPOST("num_chq").'"></td>';
				print '</tr>';
				print '<tr>';
				print '<td class="fieldrequired">'.$langs->trans("Description").'</td>';
				print '<td>';
				print '<input name="label" class="flat" type="text" size="24"  value="'.GETPOST("label").'" required>';
				print '<td>';
				print '</tr>';
				print '<tr>';	
				print '<td class="fieldrequired">'.$langs->trans("Concept").'</td>';
				print '<td>';
				print $form->select_type_fees(GETPOST('type','int'),'type',1);
				print '</td>';
				print '</tr>';
				print '<tr>';		
				print '<td class="fieldrequired">'.$langs->trans("Amount").'</td>';
				print '<td><input name="debit" class="flat" type="number" min="0" max="'.$saldoBankUser.'"step="any" value="'.GETPOST("debit").'" required></td>';
				print '</tr>';
				print '</table>';

				dol_fiche_end();
					print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"></div>';
					print "</form>";
				}
				else
				{
					print '<div class="center">'.$langs->trans('Requestconfirmationoftransfer').'</div>';
				}
			}
		}
	}
	print '</td>';
	print '<td valign="top">';
	if ($object->statut < 4 && $saldoBankUser > 0)
	{
 		   	//proceso para crear gastos
    		//buscamos la cuenta del que desembolsa
		$accountuser = new Accountuser($db);
		if (!empty($object->fk_account))
		{
				//transferencia
			if ($user->rights->request->trans->crear)
			{
	    			//buscamos la cuenta del que desembolsa
				$filterfrom = " rowid IN (".$object->fk_account.")";


				print_fiche_titre($langs->trans("Newtransfer"));

				print "\n".'<script type="text/javascript" language="javascript">';
				print '$(document).ready(function () {
					$("#user_to").change(function() {
						document.addcon.action.value="createrefr";
						document.addcon.submit();
					});
				});';
				print '</script>'."\n";

				print "\n".'<script type="text/javascript" language="javascript">';
				print '$(document).ready(function () {
					$("#fk_projet").change(function() {
						document.addcon.action.value="createrefr";
						document.addcon.submit();
					});
				});';
				print '</script>'."\n";

				if ($lAdd)
				{
					print '<form id="addcon" name="addcon" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="transfconf">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';

					dol_fiche_head();

					print '<table class="border centpercent">';

				//print '<tr><td>'.$langs->trans('TransferForm').'</td>';
				//print '<td>';
				//$account->fetch($object->fk_account);
				//print $account->label;
				//print "</td>";
				//print '</tr>';
					print '<tr><td class="fieldrequired">'.$langs->trans('For').'</td>';	
					print "<td>";
					$included = array();
					print $form->select_users($user_to,'user_to',1,array(1,$object->fk_user_create),0,$included);
					print "</td>";

					print '<tr><td class="fieldrequired">'.$langs->trans("Project").'</td><td>';
					//$filterkey = '';
					$aIds = array_element_contact('project',$user_to);
					//buscamos los projectos
					$ids = implode(',',$aIds);
					$aProjet = array_projet($ids);
					//$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $fk_projet, 'fk_projet', 0,0,1,0,0,0,1,$filterkey);
					print $form->selectarray('fk_projet',$aProjet,$fk_projet,1);
					// $numprojet = $formproject->select_projects(($user->societe_id>0?$soc->id:-1), $fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
					print '</td></tr>';

					//requerimiento
					print '</tr>';
					print '<tr><td>'.$langs->trans('Request').'</td>';
					print "<td>";
	    			//revisamos
					$objectnew=new Requestcash($db);
					$filterto = '';
					$filter = array(1=>1);
					$filterstatic = " AND t.fk_user_create = ".$user_to;
					$filterstatic.= " AND t.fk_projet = ".$fk_projet;
					$filterstatic.= " AND t.statut = 3 ";
					$objectnew->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
					$aRequest = array();
					foreach($objectnew->lines AS $j => $line)
					{
						$aRequest[$line->id] = $line->ref.' '.$line->detail;
					}
					print $form->selectarray('fk_request_cash_to',$aRequest,$fk_request_cash_to,1);
					print "</td>";
					print '</tr>';
					//cuenta
					print '</tr>';
					print '<tr><td class="fieldrequired">'.$langs->trans('TransferTo').'</td>';
					print "<td>";
	    			//revisamos
					$accountusert = new Accountuser($db);
					$filterto = '';
					$filter = array(1=>1);
					$filterstatic = " AND t.fk_user = ".$user_to;
					$accountusert->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
					$ids = '';
					foreach($accountusert->lines AS $j => $line)
					{
						if(!empty($ids)) $ids.= ',';
						$ids.=$line->fk_account;
					}
					if (!empty($ids))
						$filterto = " rowid IN (".$ids.")";
					if ($user->admin)
					{
						$filterfrom = '';
						$filterto = '';
					}

					print $form->select_comptes($account_to,'account_to',0,$filterto,0);
					print "</td>";
					print '</tr>';
				//categories
					list($nbcategories,$options) = getselcategorie();
					print '<tr><td class="fieldrequired">'.$langs->trans('Category').'</td>';	
					print "<td>";
					if ($nbcategories)
					{
						print '<select class="flat" name="cat1">'.$options.'</select>';
					}
					print "</td>\n";
					print '</tr>';

					print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td>';	
					print "<td>";
					$form->select_date((empty($dateo)?dol_now():$dateo),'do_','','','','addcon',1,1,0,0);
	    			//$form->select_date('','do','','','','transaction',1,1,0,1);

					print "</td>\n";
					print '</tr>';
					print '<tr><td>'.$langs->trans('Label').'</td>';
					print '<td><input name="label" class="flat" type="text" size="40" value="'.$label.'"></td>';
					print '</tr>';
					print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td>';
					$saldoReal = $saldoBankUser + $sumapar0;		
					print '<td><input name="amount" class="flat" type="number" min="0" step="any" max="'.$saldoReal.'" value="'.$amount.'"></td>';
					print '</tr>';
					print "</table>";
					dol_fiche_end();
					print '<br><center><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
					print "</form>";
				}
				else
				{
					print '<div class="center">'.$langs->trans('Requestconfirmationoftransfer').'</div>';
	    			//fin transferencia
				}
			}
		}
	}

	print '</td>';
	print '</tr>';
}
print '</table>';

?>
