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
 *	\file       htdocs/salary/payment/fiche.php
 *	\ingroup    Payments
 *	\brief      Page fiche salary payment
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once(DOL_DOCUMENT_ROOT."/salary/class/pconceptext.class.php");

if ($conf->banque->enabled)
  require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once(DOL_DOCUMENT_ROOT."/salary/class/bankurladvance.class.php");

//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';

/*
Paso 1
Paso 2
Paso 3
Paso 4
*/
$langs->load("bills");
$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id",'int');
$ref       = GETPOST("ref",'alpha');
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$nivel     = GETPOST("nivel"); //pasos
if (empty($nivel)) $nivel = 1;
$mesg = '';
$mesgerror = '';
$error = '';

$object      = new Psalaryhistoryext($db);
$objContract = new Pcontractext($db);
$objAccount  = new Account($db);


/*
 * Actions
 */

if ($_POST["back"] == $langs->trans("Back"))
{
  $action = 'create';
  $nivel = $nivel -1;
}
//addMember
if ($action == 'addMember' && $user->rights->salary->pay->creer)
  {
    $aArraySalary = $_SESSION['aArraySalary'];
    $aArraySalary[$_POST['fk_payment']] = $_POST['fk_payment'];
    $_SESSION['aArraySalary'] = $aArraySalary;
    $action = 'create';
  }

//addMember
if ($action == 'delMember' && $user->rights->salary->pay->creer)
  {
    unset($_SESSION['aArraySalary'][$id]);
    $action = 'create';
  }

//addBank
if ($action == 'addBank' && $user->rights->salary->pay->creer)
  {
    $aArraySalary = $_SESSION['aArraySalary'];
    $account = GETPOST('fk_account');
    $amount  = GETPOST('amount');
    foreach((array) $account AS $i => $fk_account)
      {
	$aAccount[$fk_account] += $amount[$i];
      }

    $action = 'create';
    $nivel = 3;
    //guardando en session
    $_SESSION['aHistoryBank'] = $account;
    //guardando total por banco
    $_SESSION['aAccount'] = $aAccount;
  }
// Add
if ($action == 'add' && ! isset($_POST["cancel"]) && $user->rights->salary->pay->creer)
  {
    if (!empty($_SESSION['aAccount']))
      {
	$opmonth = date('m');
	$opday   = date('d');
	$opyear  = date('Y');
	$dateop = dol_mktime(12,0,0,$opmonth,$opday,$opyear);
	//tipo operacion
	$operation=$_POST["operation"];
	//concepto
	$type    = $_POST["type"];
	$aNumchq = $_POST["num_chq"];
	$label   = $_POST["label"];
	$cat1    = $_POST["cat1"];
	if (! $dateop)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Date"));
	if (! $operation) $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Type"));
	if (! $type || $type < 0)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Concept"));
	foreach((array) $_SESSION['aAccount'] AS $id => $amount)
	  {
	    $mesg = '';
	    $amount = price2num($amount);
	    if (! $amount)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Amount"));
	    if (! $mesg)
	      {
		$db->begin();
		$objAccount->fetch($id);
		$objAccount->fk_user_author = $user->id;
		$num_chq = $aNumchq[$id];
		$insertid = $objAccount->addline($dateop, $operation, $label, $amount, $num_chq, $cat1, $user);
		if ($insertid > 0)
		  {
		    // Creation of payment line
		    $bankurladv = new Bankurladvance($db);
		    $paiement_id = $bankurladv->createpaiement($dateop,$amount,4,'',$label);
		    if ($paiement_id < 0)
		      {
			$errmsg=$paiement->error;
			$error++;
		      }

		    //agregando a lista honorarios
		    //tabla llx_deplacement
		    $error = '';
		    require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
		    $objectDep = new Deplacement($db);

		    $objectDep->date	     = $dateop;
		    $objectDep->km	     = $amount * -1;
		    $objectDep->type	     = GETPOST('type','alpha');
		    $objectDep->socid	     = GETPOST('socid','int');
		    $objectDep->fk_user	     = $user->id;
		    $objectDep->note_private = GETPOST('label','alpha');
		    $objectDep->note_public  = GETPOST('note_public','alpha');
		    $objectDep->statut     	= 1;
		    $idDep = $objectDep->create($user);

		    if ($idDep < 0)
		      {
			$errmsg=$objectDep->error;
			$error++;
		      }
		    if (! $error)
		      {
			$bankurladv = new Bankurladvance($db);
			$url='';
			$mode = 'spending';
			$url=DOL_URL_ROOT.'/compta/paiement/fiche.php?id=';
			$result=$bankurladv->add_url_line($insertid, $paiement_id, $url, '(spending)', $mode);
			if ($result <= 0)
			  {
			    $error++;
			    dol_print_error($db);
			  }
			//deplacement
			$result=$bankurladv->add_url_line(
							  $insertid,
							  $idDep,
							  DOL_URL_ROOT.'/deplacement/fiche.php?id=',
							  GETPOST('type','alpha'),
							  'deplacement'
							  );
			if ($result <= 0)
			  {
			    $error++;
			    dol_print_error($db);
			  }

		      }
		    //actualizando registro de pago en salary_history
		    $aHistoryBank = $_SESSION['aHistoryBank'];
		    foreach((array) $aHistoryBank AS $a => $fk_account)
		      {
			if ($fk_account == $id)
			  {
			    $object->fetch($a);
			    $object->fk_account = $fk_account;
			    $object->fk_bank = $insertid;
			    $object->payment_state = 1;
			    $object->update($user);
			  }
		      }
		    if (! $error)
		      {
			$db->commit();
			//header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			//exit;
		      }
		    else
		      {
			$db->rollback();
			$mesg=$errmsg;
			$action = 'create';
		      }
		    //exit;
		  }
		else
		  {
		    $db->rollback();
		    $mesg=$object->error;
		    $action = 'create';
		  }
	      } //mesg
	    else
	      {
		$action='create';
	      }
	  }
      }
    if (!$error)
      {
	unset($_SESSION['aArraySalary']);
	unset($_SESSION['aHistoryBank']);
	unset($_SESSION['aAccount']);
      }
    header("Location: ".DOL_URL_ROOT.'/salary/payment/liste.php');
    exit;
  }

// Delete concept
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->pay->del)
{
  $object->fetch($id);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/salary/charge/liste.php');
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
    if ($object->fetch($_POST["id"]))
      {
	$object->ref        = $_POST["ref"];
	$object->detail     = GETPOST('detail');
	$object->details    = GETPOST('details');
	$object->type_cod   = GETPOST('type_cod');
	$object->type_mov   = GETPOST('type_mov');
	$object->ref_formula = GETPOST('ref_formula');
	$object->print      = GETPOST('print');
	$object->fk_codfol  = GETPOST('fk_codfol');
	$object->income_tax = GETPOST('income_tax')+0;
	$object->percent    = GETPOST('percent') + 0;
	$object->entity     = $conf->entity;
	$fk_contab_account  = GETPOST('fk_contab_account');
	if ($fk_contab_account && $conf->contab->enabled)
	  {
	    $objAccount->fetch($fk_contab_account);
	    $object->contab_account_ref = $objAccount->ref;
	  }

	if ( $object->update($user) > 0)
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

// select

//recuperar parametro concepto liquido pagable
$concept = $conf->global->SALARY_CONCEPT_LIQUID_PAYMENT;
$objConcept = new Pconceptext($db);
$objConcept->fetch_ref($concept);
$fk_concept = $objConcept->id;

//$fk_concept = 73;
$aArray = $object->array_payment($fk_concept,'lastname,firstname');

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);
if ($action == 'create' && $user->rights->salary->pay->creer)
  {
    if ($nivel == 1)
      {
	print_fiche_titre($langs->trans("Level1"));

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Members"),"", "");
	print_liste_field_titre($langs->trans("Action"),"", "","","",'align="center"');
	print '</tr>';

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addMember">';

	print '<tr><td>';
	print $form->selectarray('fk_payment',$aArray,$fk_payment);
	print '</td>';
	print '<td>';
	print '<center><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	print '</td></tr>';
	print '</form>';
	$aArraySalary = $_SESSION['aArraySalary'];
	foreach((array) $aArraySalary AS $i => $data)
	  {
	    print '<tr><td>'.$aArray[$i].'</td>';
	    print '<td align="center">';
	    print '<a href="'.DOL_URL_ROOT.'/salary/payment/fiche.php?action=delMember&id='.$i.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
	    print '</td></tr>>';
	  }

	print '</table>';
	print '<center><a href="'.DOL_URL_ROOT.'/salary/payment/fiche.php?action=create&nivel=2'.'">'.$langs->trans("Next").'</a></center>';


      }
    if ($nivel == 2)
      {
	print_fiche_titre($langs->trans("Level2"));

	dol_htmloutput_mesg($mesg);


	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addBank">';
	print '<input type="hidden" name="nivel" value="2">';

	print '<table class="border" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Members"),"", "");
	print_liste_field_titre($langs->trans("Bank"),"", "");
	print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Action"),"", "","","",'align="center"');
	print '</tr>';

	$aArraySalary = $_SESSION['aArraySalary'];
	foreach((array) $aArraySalary AS $i => $data)
	  {
	    print '<tr><td>'.$aArray[$i].'</td>';
	    //buscamos el pago a realizar
	    $object->fetch($i);
	    $objContract->fetch_vigent($object->fk_user);
	    // bank
	    //bank account
	    if ($conf->banque->enabled)
	      {
		if (!empty($_SESSION['aHistoryBank'][$i]))
		  $fk_account = $_SESSION['aHistoryBank'][$i];
		else
		  $fk_account = $objContract->fk_account;
		print '<td>';
		print $form->select_comptes($fk_account,'fk_account['.$i.']',0,'',1);
		print '</td>';
	      }
	    print '<td>';
	    print '<input type="text" name="amount['.$i.']" value="'.$object->amount.'" size="8"> ';
	    print '</td>';
	    print '</tr>';

	    // print '<td align="center">';
	    // print '<a href="'.DOL_URL_ROOT.'/salary/payment/fiche.php?action=delMember&id='.$i.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
	    // print '</td></tr>>';
	  }

	print '</table>';

	print '<center><input type="submit" class="button" name="back" value="'.$langs->trans("Back").'">&nbsp;';
	print '<input type="submit" class="button" value="'.$langs->trans("Next").'"></center>';
	print '</form>';
      }

    if ($nivel == 3)
      {
	print_fiche_titre($langs->trans("Level3"));

	dol_htmloutput_mesg($mesg);

	// Chargement des categories bancaires dans $options
	$nbcategories=0;

	$sql = "SELECT rowid, label";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank_categ";
	$sql.= " WHERE entity = ".$conf->entity;
	$sql.= " ORDER BY label";

	$result = $db->query($sql);
	if ($result)
	  {
	    $var=True;
	    $num = $db->num_rows($result);
	    $i = 0;
	    $options = '<option value="0" selected="true">&nbsp;</option>';
	    while ($i < $num)
	      {
		$obj = $db->fetch_object($result);
		$options.= '<option value="'.$obj->rowid.'">'.$obj->label.'</option>'."\n";
		$nbcategories++;
		$i++;
	      }
	    $db->free($result);
	  }

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="nivel" value="3">';

	print '<table class="border" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Bank"),"", "");
	print_liste_field_titre($langs->trans("Numberdoc"),"", "");
	print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="right"');
	print '</tr>';

	$aAccount = $_SESSION['aAccount'];
	foreach((array) $aAccount AS $i => $ammount)
	  {
	    //Bank name
	    $objAccount->fetch($i);
	    print '<tr><td>'.$objAccount->label.'</td>';
	    print '<td><input type="text" name="num_chq['.$i.']" value="" size="12">'.'</td>';
	    //buscamos el pago a realizar
	    print '<td align="right">'.price($ammount,'MU').'</td>';
	    print '</tr>';
	  }

	print '</table>';

	print '<table width="100%">';
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Date").'</td>';
	print '<td>'.$langs->trans("Type").'</td>';
	if ($nbcategories)
	  {
	    print '<td>'.$langs->trans("Category").'</td>';
	  }
	print '<td>'.$langs->trans("Description").'</td>';
	print '<td>'.$langs->trans("Concept").'</td>';
	print '</tr>';

	print '<tr '.$bc[false].'>';
	print '<td nowrap="nowrap">';
	$form->select_date('','op','','','','transaction',1,1,0,1);
	print '</td>';
	print '<td nowrap="nowrap">';
	$form->select_types_paiements((GETPOST('operation')?GETPOST('operation'):($object->courant == 2 ? 'LIQ' : '')),'operation','1,2',2,1);
	print '</td>';
	print '<td>';
	print '<input name="label" class="flat" type="text" size="35"  value="'.GETPOST("label").'">';
	print '</td>';
	print '<td>';
	print $form->select_type_fees(GETPOST('type','int'),'type',1);
	print '</td>';

	if ($nbcategories)
	  {
	    print '<td>'.$langs->trans("Category").': <select class="flat" name="cat1">'.$options.'</select></td>';
	  }
	print '</tr>';
	print '</table>';

	print '<center><input type="submit" class="button" name="back" value="'.$langs->trans("Back").'">&nbsp;';
	print '<input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
	print '</form>';
      }

  }
 else
   {
     if ($id > 0 || ! empty($ref) )
       {
      dol_htmloutput_mesg($mesg);

      $result = $object->fetch($id,$ref);
      if ($result < 0)
	{
	  dol_print_error($db);
	}

      /*
       * Affichage fiche
       */
      if ($action <> 'edit' && $action <> 're-edit')
	{
	  $head = concept_prepare_head($object);

	  dol_fiche_head($head, 'card', $langs->trans("Concept"), 0, 'salary');

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	    {
	      $object->fetch(GETPOST('id'));
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

	  // // ref
	  // print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	  // print $object->ref;
	  // print '</td></tr>';

	  // ref
	  print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';

	  $linkback = '<a href="'.DOL_URL_ROOT.'/salary/concept/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  print $form->showrefnav($object, 'ref', '',1,'ref');
	  print '</td></tr>';


	  // detail
	  print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="2">';
	  print $object->detail.' ('.$object->ref.')';
	  print '</td></tr>';

	  //details
	  print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'" disabled="disabled">';
	  print $object->details;
	  print '</textarea>';
	  print '</td></tr>';

	  //tipocod
	  print '<tr><td>'.$langs->trans('Typecod').'</td><td colspan="2">';
	  print select_typecod($object->type_cod,'type_cod','','',1,1);
	  print '</td></tr>';

	  //print
	  print '<tr><td>'.$langs->trans('Print').'</td><td colspan="2">';
	  print select_yesno($object->print,'print','','',1,1);
	  print '</td></tr>';

	  //type fol
	    print '<tr><td>'.$langs->trans('Typefol').'</td><td colspan="2">';
	  If ($objectT->fetch($object->fk_codfol))
	    print $objectT->ref;
	  else
	    print "";
	  print '</td></tr>';

	  //tipomov
	  print '<tr><td>'.$langs->trans('Typemov').'</td><td colspan="2">';
	  print select_typemov($object->type_mov,'type_mov','','',1,1);
	  print '</td></tr>';

	  //formula
	  $objectF->fetch_ref($object->ref_formula);
	  print '<tr><td>'.$langs->trans('Formula').'</td><td colspan="2">';
	  if ($objectF->ref == $object->ref_formula)
	    print $objectF->ref.' '.$objectF->detail;
	  else
	    print '';
	  print '</td></tr>';

	  if ($conf->contab->enabled)
	    {
	      print '<tr><td>'.$langs->trans('Account').'</td><td colspan="2">';
	      $objAccount->fetch('',$object->contab_account_ref);
	      print $objAccount->cta_name;
	      print '</td></tr>';

	    }
	  // //income tax
	  // print '<tr><td>'.$langs->trans('Incometax').'</td><td colspan="2">';
	  // print select_incometax($object->income_tax,'income_tax','','',1,1);
	  // print '</td></tr>';
	  // // percent
	  // print '<tr><td>'.$langs->trans('Percent').'</td><td colspan="2">';
	  // print $object->percent;
	  // print '</td></tr>';

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
	      if ($user->rights->salary->pay->creer)
		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      if ($user->rights->salary->pay->creer)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	      if ($user->rights->salary->pay->del)
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
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="3" maxlength="3">';
	  print '</td></tr>';
	  // detail
	  print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
	  print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="30" maxlength="40">';
	  print '</td></tr>';

	  //details
	  print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'">';
	  print $object->details;
	  print '</textarea>';
	  print '</td></tr>';

	  //tipocod
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typecod').'</td><td colspan="2">';
	  print select_typecod($object->type_cod,'type_cod','','',1);
	  print '</td></tr>';

	  //print
	  print '<tr><td class="fieldrequired">'.$langs->trans('Print').'</td><td colspan="2">';
	  print select_yesno($object->print,'print','','',1);
	  print '</td></tr>';

	  //type fol
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typefol').'</td><td colspan="2">';
	  print $objectT->select_typefol($object->fk_codfol,'fk_codfol','','',1);
	  print '</td></tr>';

	  //tipomov
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typemov').'</td><td colspan="2">';
	  print select_typemov($object->type_mov,'type_mov','','',1);
	  print '</td></tr>';
	  //formula
	  //buscando el id formula
	  // $objectF->fetch_ref($object->ref_formula);
	  print '<tr><td>'.$langs->trans('Formula').'</td><td colspan="2">';
	  print $objectF->select_ref_formula($object->ref_formula,'ref_formula','','',1);
	  print '</td></tr>';

	  if ($conf->contab->enabled)
	    {
	      $objAccount->fetch('',$object->contab_account_ref);
	      print '<tr>';
	      print '<td>';
	      print $langs->trans('Account');
	      print '</td>';
	      print '<td colspan="2">';
	      print $objAccount->select_account($objAccount->id,'fk_contab_account','',0,1);
	      print '</td>';
	      print '</tr>';

	    }

	  // //income tax
	  // print '<tr><td>'.$langs->trans('Incometax').'</td><td colspan="2">';
	  // print select_incometax($object->income_tax,'income_tax','','',1);
	  // print '</td></tr>';
	  // // percent
	  // print '<tr><td class="fieldrequired">'.$langs->trans('Percent').'</td><td colspan="2">';
	  // print '<input id="percent" type="text" value="'.$object->percent.'" name="percent" size="5" maxlength="15">';
	  // print '</td></tr>';

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
