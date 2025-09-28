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
 *	\file       htdocs/addendum/fiche.php
 *	\ingroup    Addendum
 *	\brief      Page fiche addendum
 */

require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';
require_once DOL_DOCUMENT_ROOT.'/addendum/lib/addendum.lib.php';
require_once DOL_DOCUMENT_ROOT.'/addendum/lib/contrat.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/addendum/class/contratadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';


$langs->load("addendum@addendum");

if (!$user->rights->addendum->leer)
  accessforbidden();
//$db = $this->db;

$action=GETPOST('action');

$cid       = GETPOST('cid');
$id        = GETPOST("id");
$idr       = GETPOST("idr");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$url       = $_SESSION['urladd'];
$backtopage = GETPOST('backtopage');
$mesg = '';

$object  = new Addendum($db);
$objcon  = new Contrat($db);
$obuser  = new User($db);
$extrafields = new ExtraFields($db);
$thirdparty = new Societe($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objcon->table_element);

//buscamos el contrato actual
$objcon->fetch($cid);
$fk_soc = $objcon->fk_soc;
/*
 * Actions
 */
// Add
if ($action == 'add' && $user->rights->addendum->crear)
  {
    $error = 0;
    $object->fk_contrat_son = $_POST["fk_son"];
    $object->fk_contrat_father = GETPOST('fk_contrat');
    $object->fk_user_create = $user->id;
    $object->date_create    = dol_now();
    $object->tms = dol_now();
    $object->statut = 1;
    if (empty($object->fk_contrat_father))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorcontratfatherisrequired").'</div>';
      }
    if (empty($object->fk_contrat_son))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorcontratsonisrequired").'</div>';
      }

    if (empty($error)) 
      {
	$contratadd = new ContratAdd($db);
	$contratadd->fetch($object->fk_contrat_son);
	$res = $contratadd->validate($user,'',1);

	$id = $object->create($user);
	if ($id > 0 && $res>0)
	  {
	    header("Location: ".$url.'?id='.$object->fk_contrat_son);
	    exit;
	  }
	$action = 'confirm_add_contrat';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	if ($error)
	  $action="confirm_add_contrat";   // Force retour sur page creation
      }
  }

// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->addendum->del)
{
  $object->fetch($_REQUEST["idr"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/addendum/liste.php?id='.$id);
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
    header('Location: '.DOL_URL_ROOT.'/contrat/card.php?id='.$cid);
    exit;
  }

if ($action=='confirm_add_contrat'  && $_REQUEST["confirm"] == 'no')
  {
    $contratadd = new ContratAdd($db);
    $contratadd->fetch($cid);
    $contratadd->validate($user,'',1);
    header('Location: '.$url.'?id='.$cid);
    exit;
  }


/*
 * View
 */

$form=new Form($db);

//$help_url='EN:Module_Addendum_En|FR:Module_Addendum|ES:M&oacute;dulo_Addendum';
llxHeader("",$langs->trans("Addendum"),$help_url);
$aContrat = getListOfContracts($fk_soc,'others',0,$cid);
if ($action == 'confirm_add')
  {
    print_fiche_titre($langs->trans("Newaddendum"));
    dol_fiche_head();

    print $ret=$form->formconfirm(DOL_URL_ROOT.'/addendum/fiche.php'."?cid=".$objcon->id.'&action=confirm_add_contrat',
			    $langs->trans("Contracts"),
			    $langs->trans("Linktocontractfhater").' '.$objcon->ref,
			    "confirm_add_contrat",
			    '',
			    0,
			    2);
    if ($ret == 'html') print '<br>';
    dol_fiche_end(); 
  }

if ($action=='confirm_add_contrat' && $user->rights->addendum->crear && $_REQUEST["confirm"] == 'yes')
  {
    print_fiche_titre($langs->trans("Newaddendum"));

    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="fk_son" value="'.$cid.'">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // contrat
    print '<tr><td class="fieldrequired" width="15%">'.$langs->trans('Hostcontrat').'</td><td colspan="2">';

    foreach((array) $aContrat AS $j => $dataContrat)
      {
	if (empty($aCon[$dataContrat->id]))
	  //buscamos si no es hijo
	  $res = $object->getlist_son($dataContrat->id,'fk_contrat_son');
	if ($res <= 0)
	  {
	    if (!empty($dataContrat->array_options['options_ref_contrato']))
	      $aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
	    else
	      $aArray[$dataContrat->id] = $dataContrat->ref;
	  }
      }
    print $form->selectarray('fk_contrat',$aArray,$_SESSION['fk_contrat_father']);
    print '</td></tr>';
    print '</table>';
    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
    print '</form>';
  }

llxFooter();

$db->close();
?>
