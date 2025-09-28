<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/guarantees/fiche.php
 *	\ingroup    Guarantees
 *	\brief      Page fiche POA guarantees
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/guarantees/class/poaguarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/contrat.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocesscontrat.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

if (!$user->rights->poa->guar->leer)
  accessforbidden();

$action = GETPOST('action');

$id        = GETPOST("id",'int');
$idr       = GETPOST("idr",'int');
$idpro     = GETPOST('idpro','int');
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$lastlink  = GETPOST("lastlink");

if (!empty($idpro))
  {
    $ida = $_SESSION['aListip'][$idpro]['idAct'];
    $idp = $_SESSION['aListip'][$idpro]['idPrev'];
    $idc = $_SESSION['aListip'][$idpro]['idContrat'];
  }

$mesg = '';

$object  = new Poaguarantees($db);
$objcont = new Contrat($db);
$obuser  = new User($db);
$objpcon = new Poaprocesscontrat($db);
$objpcom = new Poapartidacom($db);
$objprev = new Poaprev($db);
$extrafields = new ExtraFields($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->guar->crear)
  {
    $error = 0;
    $object->date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
    $object->date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

    $object->ref       = $_POST["ref"];
    $object->code_guarantee     = GETPOST('code_guarantee');
    $object->issuer = GETPOST('issuer','alpha');
    $object->concept = GETPOST('concept','alpha');
    $object->amount = GETPOST('amount');
    $object->fk_contrat = GETPOST('fk_contrat');
    $object->fk_user_create = $user->id;
    $object->date_create = dol_now();
    $object->tms = dol_now();
    $object->statut     = 0;

    if ($object->fk_contrat <= 0)
    {
    	$error++;
    	$mesg.='<div class="error">'.$langs->trans("ErrorContratisrequired").'</div>';
    }
    if (empty($object->code_guarantee))
    {
    	$error++;
    	$mesg.='<div class="error">'.$langs->trans("ErrorCodeguaranteeisrequired").'</div>';
    }
    if (empty($object->ref))
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
    }
    if (empty($object->issuer))
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorIssuerisrequired").'</div>';
    }
    if (empty($object->concept))
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorConceptisrequired").'</div>';
    }
    if (empty($object->amount))
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorAmountisrequired").'</div>';
    }

    if (empty($error))
    {
		$id = $object->create($user);
        if ($id > 0)
	  	{
            if (!empty($lastlink))
            {
                header("Location: ".$lastlink);
            }
            else
            {
                if (!empty($_SESSION['localuri']))
                    header("Location: ".$_SESSION['localuri']);
                else
                    header("Location: fiche.php?id=".$id);
                exit;
            }
	  	}
        if (!empty($lastlink))
        {
            header("Location: ".$lastlink);
            exit;
        }

		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
    }
    else
    {
            if (!empty($lastlink))
            {
                header("Location: ".$lastlink);
                exit;
            }
		if ($error)
	  		$action="create";   // Force retour sur page creation
    }
  }

//update
if ($action == 'update' && $user->rights->poa->guar->crear)
  {
    if ($object->fetch($id) > 0)
      {
  	$error = 0;
  	$object->date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
  	$object->date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

  	$object->ref       = $_POST["ref"];
  	$object->code_guarantee     = GETPOST('code_guarantee');
  	$object->issuer = GETPOST('issuer','alpha');
  	$object->concept = GETPOST('concept','alpha');
  	$object->amount = GETPOST('amount');
  	$object->fk_contrat = GETPOST('fk_contrat');
  	$object->tms = dol_now();
  	$object->statut     = 0;

  	if (empty($object->ref))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
	  }
  	if (empty($object->issuer))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("ErrorIssuerisrequired").'</div>';
	  }
  	if (empty($object->concept))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("ErrorConceptisrequired").'</div>';
	  }
  	if (empty($object->amount))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("ErrorAmountisrequired").'</div>';
	  }
  	if ($object->fk_contrat <= 0)
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("ErrorContratisrequired").'</div>';
	  }

  	if (empty($error))
	  {
	    $res = $object->update($user);
	    if ($res > 0)
	      {
		header("Location: fiche.php?id=".$id);
		exit;
	      }
	    $action = 'edit';
	    $mesg='<div class="error">'.$object->error.'</div>';
	  }
  	else
	  {
	    if ($error)
	      $action="edit";   // Force retour sur page creation
	  }
      }
  }

// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->guar->del)
  {
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/guarantees/liste.php');
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


/*
 * View
 */

$form=new Form($db);

$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Guarantees"),$help_url,'','','',$aArrjs,$aArrcss);

$aContrat = getListOfContracts();
$aArray = array();
//contratos ya registrados
$objpcon->getidscontrat();
$aCon = $objpcon->array;
foreach((array) $aContrat AS $j => $dataContrat)
{
  if (!empty($dataContrat->array_options['options_ref_contrato']) && $idc == $dataContrat->id)
    $aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
}
asort($aArray);
if ($action == 'create' && $user->rights->poa->guar->crear)
{
    print_fiche_titre($langs->trans("Newguarantee"));

    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';

    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // contrat
    print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
    print $form->selectarray('fk_contrat',$aArray,$idc,(count($aArray)>0?0:1));
    print '</td></tr>';

    //type guarantee
    print '<tr><td class="fieldrequired">'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
    print select_code_guarantees($object->code_guarantee,'code_guarantee','',1,0);
    '<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
    print '</td></tr>';

    //Ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="20" maxlength="30">';
    print '</td></tr>';

    //Issuer
    print '<tr><td class="fieldrequired">'.$langs->trans('Issuer').'</td><td colspan="2">';
    print '<input id="issuer" type="text" value="'.$object->issuer.'" name="issuer" size="50" maxlength="150">';
    print '</td></tr>';

    //concept
    print '<tr><td class="fieldrequired">'.$langs->trans('Concept').'</td><td colspan="2">';
    print '<input id="concept" type="text" value="'.$object->concept.'" name="concept" size="50">';
    print '</td></tr>';

    //dateini
    print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
    $form->select_date($object->date_ini,'di_','','','',"date",1,1);
	print '</td></tr>';

    //datefin
    print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
    $form->select_date($object->date_fin,'df_','','','',"date",1,1);
	print '</td></tr>';

	//amount
    print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
    print '<input id="amount" type="number" step="any" value="'.$object->amount.'" name="amount" size="15">';
    print '</td></tr>';

    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
    print "<div class=\"tabsAction\">\n";
    if ($user->rights->poa->prev->leer)
      print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$ida.'">'.$langs->trans("Return").'</a>';
    else
      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
    print '</div>';
 }
else
  {
    if ($id)
      {

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

	    dol_fiche_head($head, 'card', $langs->trans("Guarantee"), 0, 'mant');

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
	      }
	    if ($action == 'unvalidate')
	      {
		$object->fetch(GETPOST('id'));
		//cambiando a validado
		$object->statut = 0;
		//update
		$object->update($user);
		$action = '';
	      }

	    // Confirm delete third party
	    if ($action == 'delete')
	      {
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteguarantee"),$langs->trans("Confirmdeleteguarante",$object->ref.' '.$object->issuer),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	      }

	     	dol_htmloutput_mesg($mesg);

		     print '<table class="border" width="100%">';
	     // contrat
		     $objcont->fetch($object->fk_contrat);
	    	 print '<tr><td width="12%">'.$langs->trans('Contrat').'</td><td colspan="2">';
		     print $objcont->array_options["options_ref_contrato"];
		     print '</td></tr>';

	    	 //type guarantee
	     	print '<tr><td>'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
	     	print select_code_guarantees($object->code_guarantee,'code_guarantee','',0,1);
	     	print '</td></tr>';

	     //Ref
	     	print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	     	print $object->ref;
	     	print '</td></tr>';

	     //Issuer
	     	print '<tr><td>'.$langs->trans('Issuer').'</td><td colspan="2">';
	     	print $object->issuer;
	     	print '</td></tr>';

	     //concept
	     	print '<tr><td>'.$langs->trans('Concept').'</td><td colspan="2">';
	     	print $object->concept;
	     	print '</td></tr>';

	     //dateini
	     	print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	     	print dol_print_date($object->date_ini,'day');
	     	print '</td></tr>';

	     //datefin
	    	print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	     	print dol_print_date($object->date_fin,'day');
	     	print '</td></tr>';

	     //amount
		     print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
		     print price($object->amount);
	    	 print '</td></tr>';

	     	print '</table>';

	     	print '</div>';


	     /* ************************************************************************** */
	     /*                                                                            */
	     /* Barre d'action                                                             */
	     /*                                                                            */
	     /* ************************************************************************** */

	     	print "<div class=\"tabsAction\">\n";
		if ($user->rights->poa->prev->leer)
		  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'">'.$langs->trans("Return").'</a>';
		else
		  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";

	     	if ($action == '')
	       	{
		 if ($user->rights->poa->guar->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

		 if ($user->rights->poa->guar->mod && $object->statut == 0)
		   print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

		 if ($user->rights->poa->guar->val)
		 	if ($object->statut == 0)
		 		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Authorize")."</a>";
		 	else
		 		print "<a class=\"butAction\" href=\"fiche.php?action=unvalidate&id=".$object->id."\">".$langs->trans("Disavow")."</a>";

		 if ($user->rights->poa->guar->del && $object->statut == 1)
		   print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	       }
	     print "</div>";

	     //primero los procesos contrato
	     $res = $objpcon->fetch_contrat($object->fk_contrat);
	     if ($res>0)
	     {
	     	//encabezado
	     	print_barre_liste($langs->trans("Preventives"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

	     	print '<table class="noborder" width="100%">';

	     	print "<tr class=\"liste_titre\">";
	     	print_liste_field_titre($langs->trans("Nro."),"", "","","","");
	     	print_liste_field_titre($langs->trans("Name"),"", "","","","");
	     	print_liste_field_titre($langs->trans("Respon"),"", "","","","");
	     	print "</tr>\n";

	     	$aArray = $objpcon->array;
	     	foreach ((array) $aArray AS $i => $objdata)
	     	{
	     		//comprometidos
	     		$res1 = $objpcom->fetch_contrat($objdata->id);
	     		if ($res1 >0)
	     		{
	     			//preventivo
	     			$aComp = $objpcom->array;
	     			foreach ((array) $aComp AS $j => $objcom)
	     			{
	     				$res2 = $objprev->fetch($objcom->fk_poa_prev);
	     				if ($res2 > 0 && $objprev->id == $objcom->fk_poa_prev)
	     				{
	     					print '<tr>';
	     					print '<td>';
	     					print '<a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$objcom->fk_poa_prev.'">'.img_picto($langs->trans("Preventive"),DOL_URL_ROOT.'/poa/img/prev.png','',1).'&nbsp;'.$objprev->nro_preventive.'</a>';
	     					print '</td>';
	     					print '<td>';
	     					print $objprev->label;
	     					print '</td>';
	     					print '<td>';
	     					$obuser->fetch($objprev->fk_user_create);
	     					print $obuser->lastname.' '.$obuser->firstname;
	     					print '</td>';

	     					print '</tr>';

	     				}
	     			}
	     		}
	     	}
	     	print '</table>';
	    }
	     //fin registro preventivos
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

	     print '<table class="border" width="100%">';

	     // contrat
	     print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
	     print $form->selectarray('fk_contrat',$aArray,$object->fk_contrat);
	     print '</td></tr>';

	     //type guarantee
	     print '<tr><td class="fieldrequired">'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
	     print select_code_guarantees($object->code_guarantee,'code_guarantee','',1,0);
	     '<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
	     print '</td></tr>';

	     //Ref
	     print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	     print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="20" maxlength="30">';
	     print '</td></tr>';

	     //Issuer
	     print '<tr><td class="fieldrequired">'.$langs->trans('Issuer').'</td><td colspan="2">';
	     print '<input id="issuer" type="text" value="'.$object->issuer.'" name="issuer" size="50" maxlength="150">';
	     print '</td></tr>';

	     //concept
	     print '<tr><td class="fieldrequired">'.$langs->trans('Concept').'</td><td colspan="2">';
	     print '<input id="concept" type="text" value="'.$object->concept.'" name="concept" size="50">';
	     print '</td></tr>';

	     //dateini
	     print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	     $form->select_date($object->date_ini,'di_','','','',"date",1,1);
	     print '</td></tr>';

	     //datefin
	     print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	     $form->select_date($object->date_fin,'df_','','','',"date",1,1);
	     print '</td></tr>';

	     //amount
	     print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
	     print '<input id="amount" type="number" steep="any" value="'.$object->amount.'" name="amount" size="15">';
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