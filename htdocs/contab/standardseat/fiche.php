<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/contab/standardseat/fiche.php
 *	\ingroup    Standard seat
 *	\brief      Page fiche contab Standard seat
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabstandardseat.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabpointentry.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");

$langs->load("contab@contab");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Contabstandardseat($db);
$objpoint = new Contabpointentry($db);
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->contab->crearperiod)
  {
    $object = new Contabstandardseat($db);
    $object->entity        = $conf->entity;
    $object->fk_point_entry = $_POST["fk_point_entry"];
    $sequence = $object->fetch_max_sequence($object->fk_point_entry)+1;
    $object->sequence       = $sequence;
    $object->status         = $_POST["status"];
    $object->description    = $_POST["description"];
    $object->type_seat      = $_POST["type_seat"];
    $object->type_balance   = $_POST["type_balance"];
    $object->currency       = $_POST["currency"];
    $object->debit_account  = $_POST["debit_account"];
    $object->credit_account = $_POST["credit_account"];
    $object->currency_value1= $_POST["currency_value1"];
    $object->currency_value2= $_POST["currency_value2"];
    $object->history        = $_POST["history"];
    $object->history_group  = $_POST["history_group"];
    $object->origin         = $_POST["origin"];


    if ($object->fk_point_entry && $object->description) {
      $id = $object->create($user);
      if ($id > 0)
	{
	  header("Location: fiche.php?id=".$id);
	  exit;
	}
      $action = 'create';
      $mesg='<div class="error">'.$object->error.'</div>';
    }
    else {
      $mesg='<div class="error">'.$langs->trans("Errorpointentrydescriptionrequired").'</div>';
      $action="create";   // Force retour sur page creation
    }
  }



// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->delperiod)
{
  $object = new Contabstandardseat($db);
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/contab/standardseat/liste.php');
      exit;
    }
  else
    {
      $mesg='<div class="error">'.$object->error.'</div>';
      $action='';
    }
 }

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
    
    $object = new Contabstandardseat($db);
    if ($object->fetch($_POST["id"]))
      {
	$object->entity        = $conf->entity;
	$object->status         = $_POST["status"];
	$object->description    = $_POST["description"];
	$object->type_seat      = $_POST["type_seat"];
	$object->type_balance   = $_POST["type_balance"];
	$object->currency       = $_POST["currency"];
	
	$object->debit_account  = $_POST["debit_account"];
	$object->credit_account = $_POST["credit_account"];
	$object->currency_value1= $_POST["currency_value1"];
	$object->currency_value2= $_POST["currency_value2"];
	$object->history        = $_POST["history"];
	$object->history_group  = $_POST["history_group"];
	$object->origin         = $_POST["origin"];

	if ( $object->update($_POST["id"], $user) > 0)
	  {
	    $action = '';
	    $_GET["id"] = $_POST["id"];
	    //$mesg = '<div class="ok">Fiche mise a jour</div>';
	  }
	else
	  {
	    $action = 'edit';
	    $_GET["id"] = $_POST["id"];
	    $mesg = '<div class="error">'.$object->error.'</div>';
	  }
      }
    else
      {
	$action = 'edit';
	$_GET["id"] = $_POST["id"];
	$mesg = '<div class="error">'.$object->error.'</div>';
      }
  }


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

if ($action == 'create' && $user->rights->contab->crearperiod)
{
  print_fiche_titre($langs->trans("Newstandardseat"));
  
  print "<form action=\"fiche.php\" method=\"post\">\n";
  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
  print '<input type="hidden" name="action" value="add">';
  
  dol_htmloutput_mesg($mesg);

  print '<table class="border" width="100%">';

  // ref
  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td>';
  print $objpoint->select_pointentry($object->ref,'fk_point_entry','',0,1);
  print '</td>';

  // sequence
  print '<td class="fieldrequired">'.$langs->trans('Sequence').'</td><td>';
  print '<input id="sequence" type="text" value="PROV()" name="sequence" size="3" maxlength="3" disabled>';
  print '</td></tr>';

  //status
  print '<tr><td class="fieldrequired">'.$langs->trans('Status').'</td><td>';
  print select_status($object->status,'status','','',1);
  print '</td>';

  // description
  print '<td class="fieldrequired">'.$langs->trans('Description').'</td><td>';
  print '<input id="description" type="text" value="'.$object->description.'" name="description" size="30" maxlength="40">';
  print '</td></tr>';

  //type seat
  print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td>';
  print select_seat($object->type_seat,'type_seat','','',1);
  print '</td>';

  //type balance
  print '<td class="fieldrequired">'.$langs->trans('Typebalance').'</td><td>';
  print select_balance($object->type_seat,'type_balance','','',1);
  print '</td></tr>';

  print '<tr><td colspan="4">'.$langs->trans('Entitys');
  print '</td></tr>';

  //account debit
  print '<tr><td>'.$langs->trans('Debitaccount').'</td><td>';
  print '<input type="text" id="debit_account" name="debit_account" value="'.$object->debit_account.'" size="50">';
  print '</td>';

  //account credit
  print '<td>'.$langs->trans('Creditaccount').'</td><td>';
  print '<input type="text" id="credit_account" name="credit_account" value="'.$object->credit_account.'" size="50">';
  print '</td></tr>';

  print '<tr><td colspan="4">'.$langs->trans('Values');
  print '</td></tr>';

  //currency
  print '<tr><td class="fieldrequired">'.$langs->trans('Astocurrency').'</td><td colspan="3">';
  print '<input type="text" id="currency" name="currency" value="'.$object->currency.'" size="5" maxlength="5">';
  print '</td></tr>';

  //currency value 1
  print '<tr><td class="fieldrequired">'.$langs->trans('Currencyvalue1').'</td><td>';
  print '<input type="text" id="currency_value1" name="currency_value1" value="'.$object->currency_value1.'" size="50">';
  print '</td>';

  //currency value 2
  print '<td>'.$langs->trans('Currencyvalue2').'</td><td>';
  print '<input type="text" id="currency_value2" name="currency_value2" value="'.$object->currency_value2.'" size="50">';
  print '</td></tr>';

  print '<tr><td colspan="4">'.$langs->trans('History');
  print '</td></tr>';

  //currency
  print '<tr><td class="fieldrequired">'.$langs->trans('History').'</td><td>';
  print '<input type="text" id="history" name="history" value="'.$object->history.'" size="50" maxlength="150">';
  print '</td>';

  print '<td class="fieldrequired">'.$langs->trans('Historygroup').'</td><td>';
  print '<input type="text" id="history_group" name="history_group" value="'.$object->history_group.'" size="50" maxlength="150">';
  print '</td></tr>';

  print '</table>';

  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

  print '</form>';
}
else
{
  if ($_GET["id"])
    {
      dol_htmloutput_mesg($mesg);
      
      $object = new Contabstandardseat($db);
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
	  
	  dol_fiche_head($head, 'card', $langs->trans("Accountingperiods"), 0, 'contab');
	  
	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->statut = 1;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }
	  
	  // Confirm delete third party
	  if ($action == 'delete')
	    {
	      $form = new Form($db);
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletestandardseat"),$langs->trans("Confirmdeletestandardseat",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  

	  print_fiche_titre($langs->trans("Standardseat"));
	  
	  print '<table class="border" width="100%">';
	  
	  // ref
	  print '<tr><td>'.$langs->trans('Ref').'</td><td>';
	  $objpoint->fetch($object->fk_point_entry);
	  print $objpoint->description;
	  print '</td>';
	  
	  // sequence
	  print '<td>'.$langs->trans('Sequence').'</td><td>';
	  print $object->sequence;
	  print '</td></tr>';
	  
	  //status
	  print '<tr><td width="10%">'.$langs->trans('Status').'</td><td width="40%">';
	  print select_status($object->status,'status','','',1,1);
	  print '</td>';
	  
	  // description
	  print '<td width="10%">'.$langs->trans('Description').'</td><td  width="40%">';
	  print $object->description;
	  print '</td></tr>';
	  
	  //type seat
	  print '<tr><td>'.$langs->trans('Typeseat').'</td><td>';
	  print select_seat($object->type_seat,'type_seat','','',1,1);
	  print '</td>';
	  
	  //type balance
	  print '<td>'.$langs->trans('Typebalance').'</td><td>';
	  print select_balance($object->type_seat,'type_balance','','',1,1);
	  print '</td></tr>';
	  
	  print '<tr><td colspan="4">'.$langs->trans('Entitys');
	  print '</td></tr>';
	  
	  //account debit
	  print '<tr><td>'.$langs->trans('Debitaccount').'</td><td>';
	  print $object->debit_account;
	  print '</td>';
	  
	  //account credit
	  print '<td>'.$langs->trans('Creditaccount').'</td><td>';
	  print $object->credit_account;
	  print '</td></tr>';
	  
	  print '<tr><td colspan="4">'.$langs->trans('Values');
	  print '</td></tr>';
	  
	  //currency
	  print '<tr><td>'.$langs->trans('Astocurrency').'</td><td colspan="3">';
	  print $object->currency;
	  print '</td></tr>';
	  
	  //currency value 1
	  print '<tr><td>'.$langs->trans('Currencyvalue1').'</td><td>';
	  print $object->currency_value1;
	  print '</td>';
	  
	  //currency value 2
	  print '<td>'.$langs->trans('Currencyvalue2').'</td><td>';
	  print $object->currency_value2;
	  print '</td></tr>';
	  
	  print '<tr><td colspan="4">'.$langs->trans('History');
	  print '</td></tr>';
	  
	  //currency
	  print '<tr><td>'.$langs->trans('History').'</td><td>';
	  print $object->history;
	  print '</td>';
	  
	  print '<td>'.$langs->trans('Historygroup').'</td><td>';
	  print $object->history_group;
	  print '</td></tr>';
	  
	  print '</table>';
  
	  print '</div>';
	  
	  
	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == '')
	    {
	      if ($user->rights->contab->crearseatst)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	      
	      if ($user->rights->contab->delseatst)
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
	  print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
	  
	  print '<form action="fiche.php" method="POST">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="id" value="'.$object->id.'">';
	  
	  print '<table class="border" width="100%">';
	  
	  // ref
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td>';
	  print $objpoint->select_pointentry($object->fk_point_entry,'fk_point_entry','',0,1);
	  print '</td>';
	  
	  // sequence
	  print '<td class="fieldrequired">'.$langs->trans('Sequence').'</td><td>';
	  print '<input id="sequence" type="text" value="'.$object->sequence.'" name="sequence" size="3" maxlength="3" disabled>';
	  print '</td></tr>';
	  
	  //status
	  print '<tr><td class="fieldrequired">'.$langs->trans('Status').'</td><td>';
	  print select_status($object->status,'status','','',1);
	  print '</td>';
	  
	  // description
	  print '<td class="fieldrequired">'.$langs->trans('Description').'</td><td>';
	  print '<input id="description" type="text" value="'.$object->description.'" name="description" size="30" maxlength="40">';
	  print '</td></tr>';
	  
	  //type seat
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td>';
	  print select_seat($object->type_seat,'type_seat','','',1);
	  print '</td>';
	  
	  //type balance
	  print '<td class="fieldrequired">'.$langs->trans('Typebalance').'</td><td>';
	  print select_balance($object->type_seat,'type_balance','','',1);
	  print '</td></tr>';
	  
	  print '<tr><td colspan="4">'.$langs->trans('Entitys');
	  print '</td></tr>';
	  
	  //account debit
	  print '<tr><td>'.$langs->trans('Debitaccount').'</td><td>';
	  print '<input type="text" id="debit_account" name="debit_account" value="'.$object->debit_account.'" size="50">';
	  print '</td>';
	  
	  //account credit
	  print '<td>'.$langs->trans('Creditaccount').'</td><td>';
	  print '<input type="text" id="credit_account" name="credit_account" value="'.$object->credit_account.'" size="50">';
	  print '</td></tr>';
	  
	  print '<tr><td colspan="4">'.$langs->trans('Values');
	  print '</td></tr>';
	  
	  //currency
	  print '<tr><td class="fieldrequired">'.$langs->trans('Astocurrency').'</td><td colspan="3">';
	  print '<input type="text" id="currency" name="currency" value="'.$object->currency.'" size="5" maxlength="5">';
	  print '</td></tr>';
	  
	  //currency value 1
	  print '<tr><td class="fieldrequired">'.$langs->trans('Currencyvalue1').'</td><td>';
	  print '<input type="text" id="currency_value1" name="currency_value1" value="'.$object->currency_value1.'" size="50">';
	  print '</td>';
	  
	  //currency value 2
	  print '<td>'.$langs->trans('Currencyvalue2').'</td><td>';
	  print '<input type="text" id="currency_value2" name="currency_value2" value="'.$object->currency_value2.'" size="50">';
	  print '</td></tr>';
	  
	  print '<tr><td colspan="4">'.$langs->trans('History');
	  print '</td></tr>';
	  
	  //currency
	  print '<tr><td class="fieldrequired">'.$langs->trans('History').'</td><td>';
	  print '<input type="text" id="history" name="history" value="'.$object->history.'" size="50" maxlength="150">';
	  print '</td>';
	  
	  print '<td class="fieldrequired">'.$langs->trans('Historygroup').'</td><td>';
	  print '<input type="text" id="history_group" name="history_group" value="'.$object->history_group.'" size="50" maxlength="150">';
	  print '</td></tr>';
	  
	  print '</table>';
	  
	  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
	  
	  print '</form>';
	  
	}
    }
}


llxFooter();

$db->close();
?>
