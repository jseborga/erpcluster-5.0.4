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
 *	\file       htdocs/contab/accounts/fiche.php
 *	\ingroup    Chart of Account
 *	\brief      Page fiche contab Chart of account
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';

$langs->load("contab@contab");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="ca.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Contabaccountingext($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->contab->crearperiod)
{
	
	$object->ref        = $_POST["ref"];
	$object->entity     = $conf->entity;
	$object->cta_class  = $_POST["cta_class"];
	$object->cta_normal = $_POST["cta_normal"];
	$object->cta_top    = $_POST["cta_top"];
	$object->cta_name   = $_POST["cta_name"];
	$object->statut        = 0;
	if ($object->cta_name && $object->ref) {
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
		$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
      $action="create";   // Force retour sur page creation
  }
}


/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate')
{
	$object->fetch(GETPOST('id'));
    //cambiando a validado
	$object->statut = 0;
    //update
	$object->update($user);
	header("Location: fiche.php?id=".$_GET['id']);
}

// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->delaccount)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/contab/accounts/liste.php');
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
		$object->cta_class  = $_POST["cta_class"];
		$object->cta_normal = $_POST["cta_normal"];
		$object->cta_top    = $_POST["cta_top"];
		$object->cta_name   = $_POST["cta_name"];
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

$form=new Form($db);

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

if ($action == 'create' && $user->rights->contab->crearperiod)
{
	print_fiche_titre($langs->trans("Newaccounting"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

  // ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="20" maxlength="40">';
	print '</td></tr>';
  // name
	print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
	print '<input id="cta_name" type="text" value="'.$object->cta_name.'" name="cta_name" size="40" maxlength="80">';
	print '</td></tr>';

  //top
	print '<tr><td>'.$langs->trans('Accounttop').'</td><td colspan="2">';
	print $object->select_account($object->cta_top,'cta_top','','',1);
	print '</td></tr>';

  //cta_class
	print '<tr><td class="fieldrequired">'.$langs->trans('Class').'</td><td colspan="2">';
	print select_cta_clase($object->cta_class,'cta_class','','',1);
	print '</td></tr>';

  //cta_normal
	print '<tr><td class="fieldrequired">'.$langs->trans('Accountbalance').'</td><td colspan="2">';
	print select_cta_normal($object->cta_normal,'cta_normal','','',1);
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

      //$object = new Contabaccounting($db);
		$result = $object->fetch($_GET["id"]);
		if ($result < 0)
		{
			dol_print_error($db);
		}


		if ($action <> 'edit' && $action <> 're-edit')
		{
	  //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Chartofaccounts"), 0, 'contab');

	   // Confirmation de la validation

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

	  // ref
			print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
			print $object->ref;
			print '</td></tr>';
	  // name
			print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
			print $object->cta_name;
			print '</td></tr>';

	  //top
			print '<tr><td>'.$langs->trans('Accounttop').'</td><td colspan="2">';

			$objectsup = new Contabaccounting($db);
			$objectsup->fetch($object->cta_top);
			if ($objectsup->id == $object->cta_top)
				print $objectsup->cta_name;
			else
				print '&nbsp;';
			print '</td></tr>';

	  //cta_class
			print '<tr><td>'.$langs->trans('Class').'</td><td colspan="2">';
			print select_cta_clase($object->cta_class,'cta_class','',"",1,1);
			print '</td></tr>';

	  //cta_normal
			print '<tr><td>'.$langs->trans('Accountbalance').'</td><td colspan="2">';
			print select_cta_normal($object->cta_normal,'cta_normal','',"",1,1);
			print '</td></tr>';

	  //status
			print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
			print $object->getLibStatut(1);
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
				if ($user->rights->contab->crearperiod && $object->statut == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if (($object->statut==0 ) && $user->rights->contab->delperiod)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	      // Valid
				if ($object->statut == 0 && $user->rights->contab->valperiod)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
				}
	      // ReValid
				if ($object->statut == 1 && $user->rights->contab->valperiod)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Return').'</a>';
				}

			}

			print "</div>";

		}
      //liste movimiento contable
		$objcontabdet = new Contabseatdetext($db);
		list($aArray,$aArrayDet) = $objcontabdet->get_list_account($object->ref);
		if (!empty($aArrayDet))
		{
			print '<table class="noborder" width="100%">';

			print '<tr class="liste_titre">';
			print_liste_field_titre($langs->trans("Ref"),"", "","","","");
			print_liste_field_titre($langs->trans("Date"),"", "","","","");
			print_liste_field_titre($langs->trans("Debit"),"", "","","",'align="right"');
			print_liste_field_titre($langs->trans("Credit"),"", "","","",'align="right"');
			print_liste_field_titre($langs->trans("Detail"),"", "","","",'align="left"');
			print '</tr>';

			foreach($aArrayDet AS $fk_seat => $aData)
			{
				$objseat = new Contabseatext($db);
				$objseat->fetch($fk_seat);
				print '<tr>';
				print '<td>'.$objseat->lote.'-'.$objseat->sblote.'-'.$objseat->doc.'</td>';
				print '<td>'.dol_print_date($objseat->date_seat).'</td>';
				print '<td align="right">'.price($aData['debit_account']).'</td>';
				$sumD +=$aData['debit_account'];
				$sumC +=$aData['credit_account'];

				print '<td align="right">'.price($aData['credit_account']).'</td>';
				print '<td>'.$objseat->history.'</td>';
				print '</tr>';

			}
			print '<tr class="liste_total"><td align="left" colspan="2">'.$langs->trans("Total").'</td>';
			print '<td align="right">'.price($sumD).'</td>';
			print '<td align="right">'.price($sumC).'</td>';
			print '<td align="right">&nbsp</td>';
			print '</tr>';

			print '<tr class="liste_total"><td align="left" colspan="2">'.$langs->trans("Accountbalances").'</td>';
			if ($object->cta_normal == 1)
			{
				print '<td align="right">'.price($sumD-$sumC).'</td>';
				print '<td align="right">&nbsp;</td>';
			}
			else
			{
				print '<td align="right">&nbsp;</td>';
				print '<td align="right">'.price($sumC-$sumD).'</td>';
			}

			print '<td align="right">&nbsp</td>';
			print '</tr>';
			print '</table>';
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
    	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="20" maxlength="40">';
    	print '</td></tr>';
	  // name
    	print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
    	print '<input id="cta_name" type="text" value="'.$object->cta_name.'" name="cta_name" size="40" maxlength="80">';
    	print '</td></tr>';

	  //top
    	print '<tr><td>'.$langs->trans('Accounttop').'</td><td colspan="2">';
    	print $object->select_account($object->cta_top,'cta_top','','',1);
    	print '</td></tr>';

	  //cta_class
    	print '<tr><td class="fieldrequired">'.$langs->trans('Class').'</td><td colspan="2">';
    	print select_cta_clase($object->cta_class,'cta_class','','',1);
    	print '</td></tr>';

	  //cta_normal
    	print '<tr><td class="fieldrequired">'.$langs->trans('Accountbalance').'</td><td colspan="2">';
    	print select_cta_normal($object->cta_normal,'cta_normal','','',1);
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
