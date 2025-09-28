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
 *	\file       htdocs/poa/appoint/fiche.php
 *	\ingroup    Guarantees
 *	\brief      Page fiche POA guarantees
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/appoint/class/poacontratappoint.class.php';
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

$id        = GETPOST("id");
$idr       = GETPOST("idr");
$idpro     = GETPOST('idpro'); //idProceso
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$lastlink  = GETPOST("lastlink",'alpha');

if (!empty($idpro))
  {
    $ida = $_SESSION['aListip'][$idpro]['idAct'];
    $idp = $_SESSION['aListip'][$idpro]['idPrev'];
    $idc = $_SESSION['aListip'][$idpro]['idContrat'];
  }

$mesg = '';

$object  = new Poacontratappoint($db);
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
if ($action == 'add' && $user->rights->poa->appoint->crear)
  {
    $error = 0;
    $object->date_appoint = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

    $object->fk_user       = $_POST["fk_user"];
    $object->fk_user_replace = $_POST["fk_user_replace"];
    $object->code_appoint = GETPOST('code_appoint');
    $object->fk_contrat = GETPOST('fk_contrat');
    $object->fk_user_create = $user->id;
    $object->date_create = dol_now();
    $object->tms = dol_now();
    $object->statut     = 0;

    if ($object->fk_user <=0)
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
    }
    if (empty($object->code_appoint))
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorTypeisrequired").'</div>';
    }
    if ($object->fk_user == $object->fk_user_replace)
    {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Erroruserandreplaceisidentical").'</div>';
    }
    if ($object->fk_contrat <= 0)
    {
    	$error++;
    	$mesg.='<div class="error">'.$langs->trans("ErrorContratisrequired").'</div>';
    }
    if (empty($error))
    {
		$id = $object->create($user);
		if ($id > 0)
	  	{
            if ($lastlink)
            {
                header("Location: ".$lastlink);
                exit;
            }
	    	header("Location: fiche.php?id=".$id);
	    	exit;
	  	}
        {
            if ($lastlink)
            {
                header("Location: ".$lastlink);
                exit;
            }
		  $action = 'create';
		  $mesg='<div class="error">'.$object->error.'</div>';
        }
    }
    else
    {
        if ($lastlink)
        {
            header("Location: ".$lastlink);
            exit;
        }
		if ($error)
	  		$action="create";   // Force retour sur page creation
    }
  }

//update
if ($action == 'update' && $user->rights->poa->appoint->mod && $_POST["cancel"] != $langs->trans("Cancel"))
  {
    if ($object->fetch($id) > 0)
      {
  	$error = 0;
  	$object->date_appoint = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

  	$object->fk_user         = $_POST["fk_user"];
  	$object->fk_user_replace = $_POST["fk_user_replace"];
  	$object->code_appoint    = GETPOST('code_appoint');
  	$object->fk_contrat      = GETPOST('fk_contrat');

  	if ($object->fk_user <=0)
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
	  }
  	if (empty($object->code_appoint))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("ErrorTypeisrequired").'</div>';
	  }
  	if ($object->fk_user == $object->fk_user_replace)
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Erroruserandreplaceisidentical").'</div>';
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
      header("Location: ".DOL_URL_ROOT.'/poa/appoint/liste.php');
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
  $idpro = $_POST['idpro'];
}

/*
 * View
 */

$form=new Form($db);

$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Appoint"),$help_url,'','','',$aArrjs,$aArrcss);

$aContrat = getListOfContracts();
$aArray = array();
//$aArray[0]='';
//contratos ya registrados
$objpcon->getidscontrat();
$aCon = $objpcon->array;
foreach((array) $aContrat AS $j => $dataContrat)
{
  if (!empty($dataContrat->array_options['options_ref_contrato']) && $idc == $dataContrat->id)
    $aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
}
asort($aArray);
if ($action == 'create' && $user->rights->poa->appoint->crear)
{
    print_fiche_titre($langs->trans("Newappoint"));

    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';

    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // contrat
    print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
    print $form->selectarray('fk_contrat',$aArray,$idc,(count($aArray)>0?0:1));
    print '</td></tr>';

    //type appoint
    print '<tr><td class="fieldrequired">'.$langs->trans('Appointtype').'</td><td colspan="2">';
    print select_code_appoint($object->code_appoint,'code_appoint','',1,0);
    print '</td></tr>';

    //user
    print '<tr><td class="fieldrequired">'.$langs->trans('User').'</td><td colspan="2">';
    print $form->select_dolusers((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
    print '</td></tr>';

    //user replace
    print '<tr><td>'.$langs->trans('Replacea').'</td><td colspan="2">';
    print $form->select_dolusers($object->fk_user_replace,'fk_user_replace',1,$exclude,0,'','',$object->entity);
    print '</td></tr>';

    //dateappoint
    print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
    $form->select_date($object->date_appoint,'di_','','','',"date",1,1);
	print '</td></tr>';


    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';

    print "<div class=\"tabsAction\">\n";
    if ($user->rights->poa->prev->leer)
      print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'">'.$langs->trans("Return").'</a>';
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

	     dol_fiche_head($head, 'card', $langs->trans("Appoints"), 0, 'mant');

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
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idpro='.$idpro,$langs->trans("Deleteguarantee"),$langs->trans("Confirmdeleteguarante",$object->ref.' '.$object->issuer),"confirm_delete",'',0,2);
		 if ($ret == 'html') print '<br>';
	       }

	     dol_htmloutput_mesg($mesg);

	     print '<table class="border" width="100%">';

	     // contrat
	     $objcont->fetch($object->fk_contrat);
	     print '<tr><td width="12%">'.$langs->trans('Contrat').'</td><td colspan="2">';
	     print $objcont->array_options["options_ref_contrato"];
	     print '</td></tr>';

	     //type appoint
	     print '<tr><td>'.$langs->trans('Appointtype').'</td><td colspan="2">';
	     print select_code_appoint($object->code_appoint,'code_appoint','',0,1);
	     print '</td></tr>';

	     //user
	     $res = $obuser->fetch($object->fk_user);
	     print '<tr><td>'.$langs->trans('User').'</td><td colspan="2">';
	     if ($res > 0 && $obuser->id == $object->fk_user)
	       print $obuser->lastname.' '.$obuser->firstname;
	     else
	       print '&nbsp;';
	     print '</td></tr>';

	     //user replace
	     $res = $obuser->fetch($object->fk_user_replace);
	     print '<tr><td>'.$langs->trans('Replacea').'</td><td colspan="2">';
	     if ($res > 0 && $obuser->id == $object->fk_user_replace)
	       print $obuser->lastname.' '.$obuser->firstname;
	     else
	       print '&nbsp;';
	     print '</td></tr>';

	     //dateappoint
	     print '<tr><td>'.$langs->trans('Date').'</td><td colspan="2">';
	     print dol_print_date($object->date_appoint,'day');
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
		   print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create&idpro='.$idpro.'">'.$langs->trans("Createnew").'</a>';
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

		 if ($user->rights->poa->guar->mod && $object->statut == 0)
		   print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'&idpro='.$idpro.'">'.$langs->trans("Modify")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

		 if ($user->rights->poa->guar->val)
		   if ($object->statut == 0)
		     print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=validate&id='.$object->id.'&idpro='.$idpro.'">'.$langs->trans("Authorize")."</a>";
		   else
		     print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=unvalidate&id='.$object->id.'&idpro='.$idpro.'">'.$langs->trans("Disavow")."</a>";

		 if ($user->rights->poa->guar->del && $object->statut == 1)
		   print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'&idpro='.$idpro.'">'.$langs->trans("Delete")."</a>";
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
	     print_fiche_titre($langs->trans("Appointedit"), $mesg);

	     print '<form action="fiche.php" method="POST">';
	     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	     print '<input type="hidden" name="action" value="update">';
	     print '<input type="hidden" name="id" value="'.$object->id.'">';
	     print '<input type="hidden" name="idpro" value="'.$idpro.'">';

	     print '<table class="border" width="100%">';

	     print '<table class="border" width="100%">';

	     // contrat
	     print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
	     print $form->selectarray('fk_contrat',$aArray,$object->fk_contrat);
	     print '</td></tr>';

	     //type appoint
	     print '<tr><td class="fieldrequired">'.$langs->trans('Appointtype').'</td><td colspan="2">';
	     print select_code_appoint($object->code_appoint,'code_appoint','',1,0);
	     print '</td></tr>';

	     //user
	     print '<tr><td class="fieldrequired">'.$langs->trans('User').'</td><td colspan="2">';
	     print $form->select_dolusers((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
	     print '</td></tr>';

	     //user replace
	     print '<tr><td>'.$langs->trans('Replacea').'</td><td colspan="2">';
	     print $form->select_dolusers($object->fk_user_replace,'fk_user_replace',1,$exclude,0,'','',$object->entity);
	     print '</td></tr>';

	     //dateappoint
	     print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
	     $form->select_date($object->date_appoint,'di_','','','',"date",1,1);
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