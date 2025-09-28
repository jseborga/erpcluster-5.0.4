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
 *	\file       htdocs/salary/concept/incident.php
 *	\ingroup    Concept incident
 *	\brief      Page fiche salary concept incident
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object  = new Pconceptext($db);
$objectT = new Ptypefolext($db);

/*
 * Actions
 */


// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->salary->concept->creer)
  {
    if ($object->fetch($_POST["id"]))
      {
	$object->calc_oblig = GETPOST('calc_oblig');
	$object->calc_afp   = GETPOST('calc_afp');
	$object->calc_rciva = GETPOST('calc_rciva');
	$object->calc_agui  = GETPOST('calc_agui');
	$object->calc_vac   = GETPOST('calc_vac');
	$object->calc_indem = GETPOST('calc_indem');
	$object->calc_afpvejez  = GETPOST('calc_afpvejez');
	$object->calc_contrpat  = GETPOST('calc_contrpat');
	$object->calc_afpriesgo = GETPOST('calc_afpriesgo');
	$object->calc_aportsol  = GETPOST('calc_aportsol');
	$object->calc_quin      = GETPOST('calc_quin');

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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($_GET["id"])
  {
    dol_htmloutput_mesg($mesg);

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
	$head = concept_prepare_head($object);

	dol_fiche_head($head, 'card', $langs->trans("Concept"), 0, 'salary');

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

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td width="30%">'.$langs->trans('Ref').'</td><td colspan="2">';
	print $object->ref;
	print '</td></tr>';
	// detail
	print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="2">';
	print $object->detail;
	print '</td></tr>';
	print '</table>';
	print '<br/>';

	print '<table class="border" width="100%">';
	// calc oblig
	print '<tr><td width="30%">'.$langs->trans('Calcoblig').'</td><td colspan="2">';
	print select_yesno($object->calc_oblig,'calc_oblig','','',1,1);
	print '</td></tr>';
	// calc_afp
	print '<tr><td>'.$langs->trans('Calcafp').'</td><td colspan="2">';
	print select_yesno($object->calc_afp,'calc_afp','','',1,1);
	print '</td></tr>';
	// calc_rciva
	print '<tr><td>'.$langs->trans('Calcrciva').'</td><td colspan="2">';
	print select_yesno($object->calc_rciva,'calc_rciva','','',1,1);
	print '</td></tr>';
	// calc_agui
	print '<tr><td>'.$langs->trans('Calcagui').'</td><td colspan="2">';
	print select_yesno($object->calc_agui,'calc_agui','','',1,1);
	print '</td></tr>';
	// calc_vac
	print '<tr><td>'.$langs->trans('Calcvac').'</td><td colspan="2">';
	print select_yesno($object->calc_vac,'calc_vac','','',1,1);
	print '</td></tr>';
	// calc_indem
	print '<tr><td>'.$langs->trans('Calcindem').'</td><td colspan="2">';
	print select_yesno($object->calc_indem,'calc_indem','','',1,1);
	print '</td></tr>';
	// calc_afpvejez
	print '<tr><td>'.$langs->trans('Calcafpvejez').'</td><td colspan="2">';
	print select_yesno($object->calc_afpvejez,'calc_afpvejez','','',1,1);
	print '</td></tr>';
	// calc_contrpat
	print '<tr><td>'.$langs->trans('Calccontrpat').'</td><td colspan="2">';
	print select_yesno($object->calc_contrpat,'calc_contrpat','','',1,1);
	print '</td></tr>';
	// calc_afpriesgo
	print '<tr><td>'.$langs->trans('Calcafpriesgo').'</td><td colspan="2">';
	print select_yesno($object->calc_afpriesgo,'calc_afpriesgo','','',1,1);
	print '</td></tr>';
	// calc_aportsol
	print '<tr><td>'.$langs->trans('Calcaportsol').'</td><td colspan="2">';
	print select_yesno($object->calc_aportsol,'calc_aportsol','','',1,1);
	print '</td></tr>';
	// calc_quin
	print '<tr><td>'.$langs->trans('Calcquin').'</td><td colspan="2">';
	print select_yesno($object->calc_quin,'calc_quin','','',1,1);
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
	    if ($user->rights->salary->concept->creer)
	      print "<a class=\"butAction\" href=\"incident.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	    else
	      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	  }
	print "</div>";
      }


      /*
       * Edition fiche
       */
    if (($action == 'edit' || $action == 're-edit') && 1)
      {
	print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td width="30%">'.$langs->trans('Ref').'</td><td colspan="2">';
	print $object->ref;
	print '</td></tr>';
	// detail
	print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="2">';
	print $object->detail;
	print '</td></tr>';
	print '</table>';
	print '<br/>';

	print '<form action="incident.php" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border" width="100%">';

	// calc oblig
	print '<tr><td width="30%">'.$langs->trans('Calcoblig').'</td><td colspan="2">';
	print select_yesno($object->calc_oblig,'calc_oblig','','',1);
	print '</td></tr>';
	// calc_afp
	print '<tr><td>'.$langs->trans('Calcafp').'</td><td colspan="2">';
	print select_yesno($object->calc_afp,'calc_afp','','',1);
	print '</td></tr>';
	// calc_rciva
	print '<tr><td>'.$langs->trans('Calcrciva').'</td><td colspan="2">';
	print select_yesno($object->calc_rciva,'calc_rciva','','',1);
	print '</td></tr>';
	// calc_agui
	print '<tr><td>'.$langs->trans('Calcagui').'</td><td colspan="2">';
	print select_yesno($object->calc_agui,'calc_agui','','',1);
	print '</td></tr>';
	// calc_vac
	print '<tr><td>'.$langs->trans('Calcvac').'</td><td colspan="2">';
	print select_yesno($object->calc_vac,'calc_vac','','',1);
	print '</td></tr>';
	// calc_indem
	print '<tr><td>'.$langs->trans('Calcindem').'</td><td colspan="2">';
	print select_yesno($object->calc_indem,'calc_indem','','',1);
	print '</td></tr>';
	// calc_afpvejez
	print '<tr><td>'.$langs->trans('Calcafpvejez').'</td><td colspan="2">';
	print select_yesno($object->calc_afpvejez,'calc_afpvejez','','',1);
	print '</td></tr>';
	// calc_contrpat
	print '<tr><td>'.$langs->trans('Calccontrpat').'</td><td colspan="2">';
	print select_yesno($object->calc_contrpat,'calc_contrpat','','',1);
	print '</td></tr>';
	// calc_afpriesgo
	print '<tr><td>'.$langs->trans('Calcafpriesgo').'</td><td colspan="2">';
	print select_yesno($object->calc_afpriesgo,'calc_afpriesgo','','',1);
	print '</td></tr>';
	// calc_aportsol
	print '<tr><td>'.$langs->trans('Calcaportsol').'</td><td colspan="2">';
	print select_yesno($object->calc_aportsol,'calc_aportsol','','',1);
	print '</td></tr>';
	// calc_quin
	print '<tr><td>'.$langs->trans('Calcquin').'</td><td colspan="2">';
	print select_yesno($object->calc_quin,'calc_quin','','',1);
	print '</td></tr>';


	  print '</table>';

	  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	  print '</form>';

	}
  }


llxFooter();

$db->close();
?>
