<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *   	\file       advancepayment/paiementfournadvance_list.php
 *		\ingroup    advancepayment
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-29 09:50
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
dol_include_once('/advancepayment/class/paiementfournadvanceext.class.php');
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
if ($conf->purchase->enabled)
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
else
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
// Load traductions files requiredby by page
$langs->load("advancepayment");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

$objectstatic = new Paiementfournadvanceext($db);
$objuser = new User($db);

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('paiementfournadvancelist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('advancepayment');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$objecti=new Paiementfournadvance($db);
if (($idx > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$objecti->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
if ($conf->purchase->enabled)
	$objfact = new FactureFournisseurext($db);
else
	$objfact = new FactureFournisseur($db);

// Definition of fields for list
$arrayfields=array(

	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
	't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>0),
	't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
	't.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>1),
	't.fk_soc'=>array('label'=>$langs->trans("Fieldfk_soc"), 'checked'=>0),
	't.origin'=>array('label'=>$langs->trans("Fieldorigin"), 'checked'=>0),
	't.originid'=>array('label'=>$langs->trans("Fieldoriginid"), 'checked'=>0),
	't.fk_paiement'=>array('label'=>$langs->trans("Fieldfk_paiement"), 'checked'=>0),
	't.num_paiement'=>array('label'=>$langs->trans("Fieldnum_paiement"), 'checked'=>1),
	't.note'=>array('label'=>$langs->trans("Fieldnote"), 'checked'=>1),
	't.fk_bank'=>array('label'=>$langs->trans("Fieldfk_bank"), 'checked'=>1),
	't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),
	't.fk_facture'=>array('label'=>$langs->trans("Fieldinvoice"), 'checked'=>1),
	't.multicurrency_amount'=>array('label'=>$langs->trans("Fieldmulticurrency_amount"), 'checked'=>0),


    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
    //'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
	);
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val) 
	{
		$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	}
}




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objecti,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

//include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{
	
	$search_ref='';
	$search_entity='';
	$search_amount='';
	$search_fk_user_author='';
	$search_fk_soc='';
	$search_origin='';
	$search_originid='';
	$search_fk_paiement='';
	$search_num_paiement='';
	$search_note='';
	$search_fk_bank='';
	$search_statut='';
	$search_multicurrency_amount='';

	
	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}


if (empty($reshook))
{
    // Mass actions. Controls on number of lines checked
	$maxformassaction=1000;
	if (! empty($massaction) && count($toselect) < 1)
	{
		$error++;
		setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
	}
	if (! $error && count($toselect) > $maxformassaction)
	{
		setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
		$error++;
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objecti->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/advancepayment/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objecti->errors)) setEventMessages(null,$objecti->errors,'errors');
			else setEventMessages($objecti->error,null,'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Advancepayments');

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
		init_myfunc();
	});
});
</script>';

// Part to create
if ($action == 'create' || $action == 'confirm_paiement' || $action == 'add_paiement')
{
    //$object = new FactureFournisseur($db);
    //$object->fetch($facid);

	$datefacture=dol_mktime(12, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));
	$dateinvoice=($datefacture==''?(empty($conf->global->MAIN_AUTOFILL_DATE)?-1:''):$datefacture);


	print_fiche_titre($langs->trans('DoPayment'));

	print '<form id="payment_form" name="addpaiement" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add_paiement">';
	print '<input type="hidden" name="facid" value="'.$facid.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="ref_supplier" value="'.$obj->ref_supplier.'">';
	print '<input type="hidden" name="socid" value="'.$object->socid.'">';
	print '<input type="hidden" name="societe" value="'.$soc->name.'">';

	print '<table class="border" width="100%">';

	print '<tr class="liste_titre"><td colspan="3">'.$langs->trans('Payment').'</td>';
//	print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
//	$supplierstatic->id=$obj->socid;
//	$supplierstatic->name=$obj->name;
//	print $supplierstatic->getNomUrl(1,'supplier');
//	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td>';
	$form->select_date($dateinvoice,'','','','',"addpaiement",1,1);
	print '</td>';
	print '<td>'.$langs->trans('Comments').'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('PaymentMode').'</td><td>';
	$form->select_types_paiements(empty($_POST['paiementid'])?'':$_POST['paiementid'],'paiementid');
	print '</td>';
	print '<td rowspan="3" valign="top">';
	print '<textarea name="comment" wrap="soft" cols="60" rows="'.ROWS_3.'">'.(empty($_POST['comment'])?'':$_POST['comment']).'</textarea></td></tr>';
	print '<tr><td>'.$langs->trans('Numero').'</td><td><input name="num_paiement" type="text" value="'.(empty($_POST['num_paiement'])?'':$_POST['num_paiement']).'"></td></tr>';
	if (! empty($conf->banque->enabled))
	{
		print '<tr><td class="fieldrequired">'.$langs->trans('Account').'</td><td>';
		$form->select_comptes(empty($accountid)?'':$accountid,'accountid',0,'',2);
		print '</td></tr>';
	}
	else
	{
		print '<tr><td colspan="2">&nbsp;</td></tr>';
	}

	//verificamos cuanto se pago como adelanto

	$sql = 'SELECT SUM(f.amount) as am';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'paiementfourn_advance as f';
	$sql.= " WHERE f.entity = ".$conf->entity;
	$sql.= ' AND f.fk_soc = '.$object->socid;
	$sql.= " AND f.origin = 'SupplierOrder'";
	$sql.= ' AND f.originid = '.$id; 
		//$sql.= ' GROUP BY f.rowid, f.ref, f.ref_supplier, f.total_ht, f.total_ttc, f.datef';
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num > 0)
		{
			$objp = $db->fetch_object($resql);
			$total = $objp->am;
		}
		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}
	$balance = $object->total_ttc - $total;
	print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" max="'.$balance.'" name="amount" value="'.$balance.'">';
	print '</td></tr>';

	print '</table>';


	        // Bouton Enregistrer
	if ($action != 'add_paiement')
	{
		print '<br><input type="submit" class="button" value="'.$langs->trans('Save').'"></center>';
	}

            // Form to confirm payment
	if ($action == 'add_paiement')
	{
		$preselectedchoice=$addwarning?'no':'yes';

		$_SESSION['aPost'] = serialize($_POST);
		print '<br>';
		$text=$langs->trans('ConfirmSupplierPayment',$totalpayment,$langs->trans("Currency".$conf->currency));
		if (GETPOST('closepaidinvoices'))
		{
			$text.='<br>'.$langs->trans("AllCompletelyPayedInvoiceWillBeClosed");
			print '<input type="hidden" name="closepaidinvoices" value="'.GETPOST('closepaidinvoices').'">';
		}
		$form->form_confirm($_SERVER['PHP_SELF'].'?id='.$id.'&socid='.$object->socid,$langs->trans('PayedSuppliersPayments'),$text,'confirm_paiement',$formquestion,$preselectedchoice);
	}

	print '</form>';
}

if ($action == '' || $action == 'vincule' || $action == 'add_vincule')
{
	$object->fetchObjectLinked($object->id,'order_supplier',null,'','OR',1);
	$linkedObjects = $object->linkedObjects;
	$options = '';
	$options.= '<option value="0">'.$langs->trans('Selected').'</option>';
	foreach ((array) $linkedObjects AS $j => $data)
	{
		foreach ((array) $data AS $k => $objlink)
		{
			$options.= '<option value="'.$objlink->id.'">'.$objlink->ref.'</option>';
		}
	}

	if ($action == 'add_vincule')
	{
		$array = $_POST;
		$aPost[$id] = $array;
		$_SESSION['aPost'] = serialize($aPost);

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, $langs->trans('Linkpaymentstoinvoices'), $langs->trans('ConfirmLinkpaymentstoinvoices'), 'confirm_add_vincule', '', 0, 1);

	}
	print $formconfirm;

	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql .= " t.ref,";
	$sql .= " t.entity,";
	$sql .= " t.tms,";
	$sql .= " t.datec,";
	$sql .= " t.datep,";
	$sql .= " t.amount,";
	$sql .= " t.fk_user_author,";
	$sql .= " t.fk_soc,";
	$sql .= " t.origin,";
	$sql .= " t.originid,";
	$sql .= " t.fk_paiement,";
	$sql .= " t.num_paiement,";
	$sql .= " t.note,";
	$sql .= " t.fk_bank,";
	$sql .= " t.fk_facture,";
	$sql .= " t.statut,";
	$sql .= " t.multicurrency_amount";


	foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
	$sql.= " FROM ".MAIN_DB_PREFIX."paiementfourn_advance as t";
	if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_advance_extrafields as ef on (u.rowid = ef.fk_object)";

	$sql.= " WHERE t.entity IN (".getEntity('advancepayment',1).")";
	$sql.= " AND originid = ".$id;
	$sql.= " AND origin = 'SupplierOrder'";

	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		if ($action == 'vincule')
		{
			print '<form id="payment_form" name="addpaiement" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="add_vincule">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print '<input type="hidden" name="socid" value="'.$object->socid.'">';
			print '<input type="hidden" name="societe" value="'.$soc->name.'">';
		}

		print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
		print '<tr class="liste_titre">';
		print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($arrayfields['t.num_paiement']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($arrayfields['t.note']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($arrayfields['t.fk_bank']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($arrayfields['t.fk_facture']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		if ($action == 'vincule')
		{
			print_liste_field_titre($arrayfields['t.fk_facture']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		}
		print '</tr>'."\n";



		$i=0;
		$var=true;
		$totalarray=array();
		$lLink = false;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$var = !$var;
				$objectstatic->id = $obj->id;
				$objectstatic->id = $obj->rowid;
				$objectstatic->ref = $obj->ref;
				$objectstatic->statut = $obj->statut;

            // Show here line of result
				print '<tr '.$bc[$var].'>';
            // LIST_OF_TD_FIELDS_LIST

				if (! empty($arrayfields['t.ref']['checked'])) 
				{
					print '<td>'.$objectstatic->getNomUrl(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_user_author']['checked'])) 
				{
					$objuser->fetch($obj->fk_user_author);
					print '<td>'.$objuser->getNomUrl(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_soc']['checked'])) 
				{
					$societe = new Societe($db);
					$societe->fetch($obj->fk_soc);
					print '<td>'.$societe->getNomUrl(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.origin']['checked'])) 
				{
					print '<td>'.$obj->origin.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_paiement']['checked'])) 
				{
					$paym = new Paiement($db);
					$paym->fetch($obj->fk_paiement);
					print '<td>'.$paym->getNomUrl(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.num_paiement']['checked'])) 
				{
					print '<td>'.$obj->num_paiement.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.note']['checked'])) 
				{
					print '<td>'.$obj->note.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_bank']['checked'])) 
				{
					$account = new Account($db);
					$accountline = new AccountLine($db);
					$accountline->fetch($obj->fk_bank);
					if ($accountline->id == $obj->fk_bank)
					{
						$account->fetch($accountline->fk_account);
					}
					print '<td>'.$account->getNomUrl(1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.amount']['checked'])) 
				{
					print '<td align="right">'.price($obj->amount).'</td>';
					$total+=$obj->amount;
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_facture']['checked'])) 
				{
					if (empty($obj->fk_facture)) $lLink = true;
					$objfact->fetch($obj->fk_facture);
					if ($objfact->id == $obj->fk_facture)
					{
						if ($conf->purchase->enabled)
							print '<td>'.$objfact->getNomUrladd(1).'</td>';
						else
							print '<td>'.$objfact->getNomUrl(1).'</td>';

					}
					if (! $i) $totalarray['nbfield']++;
				}

				if ($action == 'vincule' && $obj->fk_facture <=0)
				{
					print '<td>';
					print '<select name="aFacture['.$obj->rowid.']">'.$options.'</select>';
					print '</td>';
				}

            // Action column
				print '<td></td>';
				if (! $i) $totalarray['nbfield']++;
				print '</tr>';
			}
			$i++;
		}
		print '<tr>';
		print '<td>'.$langs->trans('Total').'</td>';

		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td align="right">'.price($total).'</td>';
		print '</tr>';
		$db->free($resql);

		$parameters=array('sql' => $sql);
		$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    
	// Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print "</table>\n";

		if ($action == 'vincule')
		{
			print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Bind").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';
			print "</form>\n";
		}
		// Buttons
		print '<div class="tabsAction">'."\n";
		if ($user->rights->advancepayment->fourn->creer && $action != 'vincule')
		{
			if ($num>0 && $lLink)
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&amp;action=vincule">'.$langs->trans("Linkpaymentstoinvoices").'</a></div>'."\n";
			if ($total < $object->total_ttc)
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&amp;action=create">'.$langs->trans("Create").'</a></div>'."\n";
		}

		print '</div>'."\n";
	}
	else
	{
		$error++;
		dol_print_error($db);
	}

}

