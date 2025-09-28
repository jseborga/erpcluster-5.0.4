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
 *	\file       htdocs/mant/report/fiche.php
 *	\ingroup    Report
 *	\brief      Page fiche mant reports
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontactext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsorderext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsmaterialusedext.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("mant@mant");
$langs->load("others");

$action=GETPOST('action');
$date_ini=GETPOST('date_ini');
$date_fin=GETPOST('date_fin');
$mesg = '';

$object = new Mjobsext($db);
$objectorder = new Mjobsorderext($db);
$objectused  = new Mjobsmaterialusedext($db);

$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);

$objjus  = new Mjobsuserext($db);
$objcont = new Mjobscontactext($db);
$objsoc  = new Societe($db);

/*
 * Actions
 */

// Add
if ($action == 'report' && $user->rights->mant->rep->leer)
  {
    $date_ini = dol_mktime(0, 0, 1, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
    $date_fin = dol_mktime(12, 59, 59, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
    $level = GETPOST('level');
    if ($date_fin < $date_ini)
      {
	$mesg='<div class="error">'.$langs->trans("Errortheenddatecannotbegreaterthanstartdate").'</div>';
	$action="create";   // Force retour sur page creation
      }
    elseif (empty($date_fin) || empty($date_ini))
      {
	$mesg='<div class="error">'.$langs->trans("Errorisnecessarydates").'</div>';
	$action="create";   // Force retour sur page creation
      }
    else
      $action = 'report_order';
  }

$alevel = array(0=>'Todos',
		2=>'Validados',
		3=>'Programados',
		4=>'Concluidos',
		5=>'Validados,Programados,Concluidos');


/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Orders"),$help_url);

if ($action == 'create' && $user->rights->mant->rep->leer)
  {
    print_fiche_titre($langs->trans("Orders to stock"));

    print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="report">';

    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    $fecha = new DateTime($date_ini);
    $fecha->modify('first day of this month');
    if (!isset($date_ini))
      $date_ini = $fecha->format('d/m/Y');
    $fecha->modify('last day of this month');
    if (!isset($date_fin))
      $date_fin = $fecha->format('d/m/Y');

    // date ini
    print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
    $form->select_date($date_ini,'di_','','','',"dateini",1,1);
    print '</td></tr>';

    // date fin
    print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
    $form->select_date($date_fin,'df_','','','',"datefin",1,1);
    print '</td></tr>';

    print '<tr><td class="fieldrequired">'.$langs->trans('Level').'</td><td colspan="2">';
    print $form->selectarray('level',$alevel,GETPOST('level'),1);
    print '</td>';
    print '</tr>';

    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

    print '</form>';
  }
 else
   {
     if ($date_ini && $date_fin)
       {
	  $adateini = dol_getdate($date_ini);
	  $date_ini = $adateini['year'].'-'.$adateini['mon'].'-'.$adateini['mday'];
	  $date_ini .= ' 00:00:01';
	  $adatefin = dol_getdate($date_fin);
	  $date_fin = $adatefin['year'].'-'.$adatefin['mon'].'-'.$adatefin['mday'];
	  $date_fin .= ' 23:59:59';

	 dol_htmloutput_mesg($mesg);

	 if ($action == 'report_order')
	   {
	     $_SESSION['date_iniot'] = $date_ini;
	     $_SESSION['date_finot'] = $date_fin;
	     $_SESSION['levelot'] = $level;

	     $object->getlist('',$date_ini,$date_fin,$level);
	     print "<div class=\"tabsAction\">\n";

	     print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/report/otm_excel.php"title="'.$langs->trans('Export to excel').'">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/mant/img/excel-icon.png','',true).'</a>';
	     print '</div>';

	     print '<table class="noborder" width="100%">';
	     print '<tr class="liste_titre">';
	     print_liste_field_titre($langs->trans("Ref"));
	     print_liste_field_titre($langs->trans("Datecreate"),'','','','','align="center"');
	     print_liste_field_titre($langs->trans("Email"),'','','','','align="left"');
	     print_liste_field_titre($langs->trans("Detailproblem"),'','','','','align="left"');
	     // print_liste_field_titre($langs->trans("Dateassign"),'','','','','align="center"');
	     // print_liste_field_titre($langs->trans("Descriptionassign"),'','','','','align="left"');
	     // print_liste_field_titre($langs->trans("Dateiniprog"),'','','','','align="center"');
	     // print_liste_field_titre($langs->trans("Datefinprog"),'','','','','align="center"');
	     // print_liste_field_titre($langs->trans("Descriptionprogram"),'','','','','align="left"');
	     print_liste_field_titre($langs->trans("Dateini"),'','','','','align="center"');
	     print_liste_field_titre($langs->trans("Datefin"),'','','','','align="center"');
	     print_liste_field_titre($langs->trans("Descriptionjob"),'','','','','align="left"');
	     print_liste_field_titre($langs->trans("Technicians"),'','','','','align="left"');

	     print_liste_field_titre($langs->trans("Ordernumber"),'','','','','align="left"');
	     print_liste_field_titre($langs->trans("Materialused"),'','','','','align="left"');
	     print '</tr>';

	     $num = count($object->array);
	     if ($num)
	       {
		 $var = true;
		 foreach((array) $object->array AS $i => $obj)
		   {

		     $objsoc->fetch($obj->fk_soc);
		     $aContact = $objsoc->contact_array();

		     //contactos
		     $aJobsContact = $objcont->list_contact($obj->id);
		     //internos
		     $aJobsUsers   = $objjus->list_jobsuser($obj->id);
		     $listecontact = '';
		     foreach ((array) $aJobsContact AS $k => $objtmp)
		       {
			 if (!empty($listecontact))
			   $listecontact .= ', ';
			 $listecontact .= $aContact[$objtmp->fk_contact];
		       }
		     foreach ((array) $aJobsUser AS $k => $objtmp)
		       {
			 if (!empty($listecontact))
			   $listecontact .= ', ';
			 $objt = $aContact[$objtmp->id];
			 $listecontact .= $objt->firstname.' '.$objt->lastname;
		       }

		     $var=!$var;
		     print "<tr $bc[$var]>";
		     print '<td>';
		     print '<a href="'.DOL_URL_ROOT.'/mant/jobs/fiche.php?id='.$obj->id.'">'.$obj->ref.'</a>';
		     print '</td>';

		     print '<td align="center">'.dol_print_date($obj->date_create,'day').'</td>';
		     print '<td align="left">'.$obj->email.'</td>';
		     print '<td align="left">'.$obj->detail_problem.'</td>';
		     // print '<td align="center">'.dol_print_date($obj->date_assign,'day').'</td>';
		     // print '<td align="left">'.$obj->description_assign.'</td>';
		     // print '<td align="center">'.dol_print_date($obj->date_ini_prog,'day').'</td>';
		     // print '<td align="center">'.dol_print_date($obj->date_fin_prog,'day').'</td>';
		     // print '<td align="left">'.$obj->description_prog.'</td>';
		     print '<td align="center">'.dol_print_date($obj->date_ini,'day').'</td>';
		     print '<td align="center">'.dol_print_date($obj->date_fin,'day').'</td>';
		     print '<td align="left">'.$obj->description_job.'</td>';
		     print '<td align="left">';
		     print $listecontact;
		     print '</td>';

		     //buscamos los materiales utilizados
		     $aOrder = $objectorder->list_order($obj->id);
		     print '<td align="left">';

		     if (count($aOrder)>0)
		       {
			 foreach ((array) $aOrder AS $j => $objorder)
			   {
			     print '<p>';
			     print $objorder->order_number.' : ';
			     print $objorder->description;
			     print '</p>';
			   }
		       }
		     else
		       print '';
		     print '</td>';

		     //buscamos los materiales utilizados
		     $objectused->getlist($obj->id);
		     print '<td align="left">';
		     if (count($objectused->array)>0)
		       {
			 foreach ((array) $objectused->array AS $j => $objorder)
			   {
			     print '<p>';
			     print $objorder->ref.' : ';
			     print $objorder->description;
			     print '; cantidad: ';$objorder->quant;
			     print ' '.$objorder->unit;
			     print '</p>';
			   }
		       }
		     print '</td>';

		     print '</tr>';
		   }
	       }
	     print '</table>';
	     print "<div class=\"tabsAction\">\n";

	     print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/report/otm_excel.php">'.img_picto($langs->trans('Export to excel'),DOL_URL_ROOT.'/mant/img/excel-icon.png','',true).'</a>';
	     print '</div>';
	   }
       }
   }


llxFooter();

$db->close();
?>
