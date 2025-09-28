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
 *	\file       htdocs/salary/user/fiche.php
 *	\ingroup    salary user
 *	\brief      Page fiche salary user
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pusermovim.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("members");
$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$rid       = GETPOST("rid");

$mesg = '';

$object  = new Puserext($db);
$objectC = new Pcharge($db);
$objectD = new Pdepartament($db);
$objUser = new User($db);
$objAdh  = new Adherent($db);
$objjectsp = new Psalarypresentext($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->creardpto)
  {
    $object->fk_charge  = GETPOST('fk_charge');
    $dateini  = dol_mktime(12, 0, 0, GETPOST('dateimonth'),  GETPOST('dateiday'),  GETPOST('dateiyear'));
    $datefin  = dol_mktime(12, 0, 0, GETPOST('datefmonth'),  GETPOST('datefday'),  GETPOST('datefyear'));
    $object->fk_user    = $id;
    $object->date_ini   = $dateini;
    $object->date_fin   = $datefin;
    $object->basic      = GETPOST('basic');
    $object->state      = 1;
    if ($object->fk_charge && $object->basic && $object->date_ini) 
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	  }
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	$mesg='<div class="error">'.$langs->trans("Errorchargedetailrequired").'</div>';
	$action="create";   // Force retour sur page creation
      }
  }


// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->deldpto)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/salary/salarycharge/liste.php');
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
    if ($object->fetch($_POST["rid"]))
      {
	$dateini  = dol_mktime(12, 0, 0, GETPOST('dateimonth'),  GETPOST('dateiday'),  GETPOST('dateiyear'));
	$datefin  = dol_mktime(12, 0, 0, GETPOST('datefmonth'),  GETPOST('datefday'),  GETPOST('datefyear'));

	$object->fk_charge  = GETPOST('fk_charge');
	$object->date_ini   = $dateini;
	$object->date_fin   = $datefin;
	$object->basic      = GETPOST('basic');

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

$result = $object->fetch_user($id);
if (empty($object->id))
  $action = "create";

$objUser->fetch($id);
/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

$head=salary_prepare_head($objUser);
dol_fiche_head($head, 'member', $langs->trans("Member"),0,'user');

if ($action == 'create' && $user->rights->salary->crearuser)
  {
    print_fiche_titre($langs->trans("Newsalarycharge"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="rowid" value="'.$id.'">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    //nombre
    print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
    print $objUser->nom." ".$objUser->lastname;
    print '</td></tr>';
    
    //charge
    print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
    print $objectC->select_charge($object->fk_charge,'fk_charge','','',1);
    print '</td></tr>';
    
    //date ini
    print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
    print $form->select_date($object->date_ini,'datei');
    print '</td></tr>';
    
    //date fin
    print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
    print $form->select_date("",'datef',"","",1);
    print '</td></tr>';
    
    //basic
    print '<tr><td class="fieldrequired">'.$langs->trans('Basic').'</td><td colspan="2">';
    print '<input type="text" id="basic" name="basic" value="'.$object->basic.'" >';
    print '</td></tr>';
    
    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
  }
 else
   {
     if ($id)
       {
      dol_htmloutput_mesg($mesg);
      
      $objUser->fetch($id);
      $objAdh->fetch($id);
      //listando las planillas
      $sql = " SELECT ";

      $result = $object->fetch_user($id);
      if ($result < 0)
	{
	  dol_print_error($db);
	}
      
      
      /*
       * Affichage fiche
       */
      if ($action <> 'edit' && $action <> 're-edit')
	{
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
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';


	  //nombre
	  print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	  print $objAdh->firstname." ".$objAdh->lastname;
	  print '</td></tr>';

	  //charge
	  print '<tr><td>'.$langs->trans('Charge').'</td><td colspan="2">';
	  If ($objectC->fetch($object->fk_charge))
	    print $objectC->ref;
	  else
	    print "";
	  print '</td></tr>';
	  
	  //date ini
	  print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	  print dol_print_date($object->date_ini,'daytext');
	  print '</td></tr>';
	  
	  //date fini
	  print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	  print dol_print_date($db->jdate($object->date_fin),'day');
	  print '</td></tr>';

	  //basic
	  print '<tr><td>'.$langs->trans('Basic').'</td><td colspan="2">';
	  print price($object->basic);
	  print '</td></tr>';
	  
	  //state
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	  print $object->state;
	  print '</td></tr>';
	  	  
	  print "</table>";
	  
	  print '</div>';
	  
	  
	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == '')
	    {
	      // if ($user->rights->salary->crearsacharge)
	      // 	print "<a class=\"butAction\" href=\"fiche.php?action=edit&rowid=".$id.'&rid='.$object->id."\">".$langs->trans("Modify")."</a>";
	      // else
	      // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	      
	      // if ($user->rights->salary->delsacharge)
	      // 	print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	      // else
	      // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	    }	  
	  print "</div>";		

	  //listando los bonos
	  
	  if ($object->state > 0 )
	    {
	      print "<br/>";
	      print "<div>";

	      $sql = "SELECT ub.rowid as rid, ub.amount, ub.date_pay, ub.time_info, ub.amount_base, ub.date_pay, ub.sequen, ";
	      $sql.= " c.ref AS ref, c.detail, ";
	      $sql.= " p.ref AS refperiodo, p.mes, p.anio, ";
	      $sql.= " t.ref AS reffol, t.detail AS detailfol ";
	      $sql.= " FROM ".MAIN_DB_PREFIX."p_user_movim AS ub ";
	      $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON ub.fk_concept = c.rowid ";
	      $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_period AS p ON ub.fk_period = p.rowid ";
	      $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS t ON ub.fk_type_fol = t.rowid ";


	      $sql.= " WHERE ";
	      $sql.= " c.entity = ".$conf->entity;
	      $sql.= " AND ub.fk_user = ".$id;
	      $sql.= " AND state = 1 ";
	      
	      $sql.= $db->order($sortfield,$sortorder);
	      
	      dol_syslog('List user bonus sql='.$sql);
	      $resql = $db->query($sql);

	      print '<table class="noborder" width="100%">';
	      print "<tr class=\"liste_titre\">";
	      print_liste_field_titre($langs->trans("Concept"),"", "c.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Detail"),"", "c.detail","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Detailfol"),"", "t.detailfol","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Period"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Year"),"", "p.anio","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Month"),"", "p.mes","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);

	      print_liste_field_titre($langs->trans("Datepay"),"", "ub.date_pay","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Amount"),"", "ub.amount","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Amountbase"),"", "ub.amount_base","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
	      print_liste_field_titre($langs->trans("Sequen"),"", "ub.sequen","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
	      print "</tr>";


	      if ($resql)
		{
		  $num = $db->num_rows($resql);
		  $i = 0;
		  $var=True;
		  while ($i < $num)
		    {
		      $objp = $db->fetch_object($resql);		    
		      $var=!$var;
		      //print '<td>'.dol_print_date($objp->datem).'</td>';
		      print "<tr ".$bc[$var].">";
		      print "<td>".$objp->ref.'</td>';
		      print '<td>'.$objp->detail.'</td>';
		      print '<td>'.$objp->detailfol.'</td>';
		      print '<td>'.$objp->refperiod.'</td>';
		      print '<td>'.$objp->anio.'</td>';
		      print '<td>'.$objp->mes.'</td>';
		      print '<td>'.$objp->date_pay.'</td>';
		      print '<td align="right">'.price($objp->amount).'</td>';
		      print '<td align="right">'.price($objp->amount_base).'</td>';
		      print '<td align="center">'.$objp->sequen.'</td>';
		      print '</tr>';
		      $i++;
		    }
		}
	      /* ************************************************************************** */
	      /*                                                                            */
	      /* Barre d'action                                                             */
	      /*                                                                            */
	      /* ************************************************************************** */	
	      print '</table>';
	      print "</div>";
	      print "<div class=\"tabsAction\">\n";
	      if ($action == '')
		{
		  if ($user->rights->salary->crearconcept)
		    print "<a class=\"butAction\" href=\"fiche.php?action=createbonus&rowid=".$id.'&rid='.$object->id."\">".$langs->trans("Create")."</a>";
		  else
		    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";		  
		}	  
	      print "</div>"; 
	    }
	}
      
      
      /*
       * Edition fiche
       */
      if (($action == 'edit' || $action == 're-edit') && 1)
	{
	  $object->fetch($rid);
	  print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
	  
	  print '<form action="fiche.php" method="POST">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="rid" value="'.$object->id.'">';
	  print '<input type="hidden" name="rowid" value="'.$id.'">';
	  
	  print '<table class="border" width="100%">';
	  

	  //nombre
	  print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	  print $objAdh->firstname." ".$objAdh->lastname;
	  print '</td></tr>';

	  //charge
	  print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
	  print $objectC->select_charge($object->fk_charge,'fk_charge','','',1);
	  print '</td></tr>';
	  
	  //date ini
	  print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	  print $form->select_date($object->date_ini,'datei');
	  print '</td></tr>';
	  
	  //date fin
	  print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	  print $form->select_date($object->date_fim,'datef',"","",1);
	  print '</td></tr>';

	  //basic
	  print '<tr><td class="fieldrequired">'.$langs->trans('Basic').'</td><td colspan="2">';
	  print '<input type="text" id="basic" name="basic" value="'.$object->basic.'" >';
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
