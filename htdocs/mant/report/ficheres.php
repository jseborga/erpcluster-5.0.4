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
 *	\file       htdocs/mant/report/ficheres.php
 *	\ingroup    Report resumen
 *	\brief      Page fiche mant reports
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontactext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mwctsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/html.formadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuser.class.php';


$langs->load("mant@mant");
$langs->load("others");

$action=GETPOST('action');
$date_ini=GETPOST('date_ini');
$date_fin=GETPOST('date_fin');
$fk_contact = GETPOST('fk_contact','int');
$fk_user    = GETPOST('fk_user','int');
$mesg = '';

$object      = new Mjobsext($db);
$objectcont  = new Mjobscontactext($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objjus  = new Mjobsuserext($db);
$objcont = new Mjobscontactext($db);
$objsoc  = new Societe($db);
$objmwcts = new Mwctsext($db);
$objWorkcontact = new Mworkrequestcontact($db);
$objWorkuser = new Mworkrequestuser($db);

$objUser = new User($db);
$objContact = new Contact($db);

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
        $action = 'report_otr';
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
llxHeader("",$langs->trans("Managementjobs"),$help_url);

if ($action == 'create' && $user->rights->mant->rep->leer)
{
    print_fiche_titre($langs->trans("Job orders"));

    print "<form action=\"ficheres.php\" method=\"post\">\n";
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

    $formadd = new Formadd($db);
    // date fin
    print '<tr><td>'.$langs->trans('User').'</td><td colspan="2">';
    print $formadd->select_use('','fk_user'," admin=0 AND (fk_socpeople <= 0 OR fk_socpeople IS NULL)",1,0,0,'',0);
    print '</td></tr>';

    // date fin
    print '<tr><td>'.$langs->trans('Contact').'</td><td colspan="2">';
    print $form->select_contacts($object->fk_soc,'','fk_contact',1);
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
        //$adateini = dol_getdate($date_ini);
        //$date_ini = $adateini['year'].'-'.$adateini['mon'].'-'.$adateini['mday'];
        //$date_ini .= ' 00:00:01';
        //$adatefin = dol_getdate($date_fin);
        //$date_fin = $adatefin['year'].'-'.$adatefin['mon'].'-'.$adatefin['mday'];
        //$date_fin .= ' 23:59:59';
        //convirtiendo las fechas

        dol_htmloutput_mesg($mesg);

        if ($action == 'report_otr')
        {
            $_SESSION['date_iniot'] = $date_ini;
            $_SESSION['date_finot'] = $date_fin;
            $_SESSION['levelot'] = $level;
            $_SESSION['fk_contactot'] = $fk_contact;
            $_SESSION['fk_userot'] = $fk_user;

            $date_ini = $db->idate($date_ini);
            $date_fin = $db->idate($date_fin);

            $object->getlist('',$date_ini,$date_fin,$level);
            //armando el resumen
            $num = count($object->array);
            if ($num)
            {
                $var = true;
                foreach((array) $object->array AS $i => $obj)
                {
                    $lView = true;
                    if ($fk_contact > 0 || $fk_user > 0)
                        $lView = false;

                    //verificamos si tiene filtro de contactos
                    if ($fk_contact > 0)
                    {
                        $lView = false;
                        //buscamos si esta asignado el contacto responsable
                        $aArray = $objWorkcontact->list_contact($obj->fk_work_request);
                        if (count($aArray) > 0)
                        {
                            foreach((array) $aArray AS $j => $objc)
                            {
                                if ($objContact->fetch($objc->fk_contact))
                                {

                                    if ($objContact->id == $objc->fk_contact && $objContact->id == $fk_contact)
                                        $lView = true;
                                }
                            }
                        }
                    }
                    //revisamos si se filtro fk_user
                    if ($fk_user > 0)
                    {
                        //buscamos si esta asignado el usuario responsable
                        $aArray = $objWorkuser->list_requestuser($obj->fk_work_request);
                        if (count($aArray) > 0)
                        {
                            foreach((array) $aArray As $j => $obju)
                            {
                                if ($objUser->fetch($obju->fk_user)>0)
                                {
                                    if ($objUser->id == $obju->fk_user && $objUser->id == $fk_user)
                                        $lView = true;
                                }
                            }
                        }
                    }
                    //solo con lView == true
                    if ($lView)
                    {
                        //obtenemos la clase de trabajo
                        $res = $objmwcts->fetch_working_class($obj->typemant,$obj->speciality_job);
                        if ($res > 0 && $objmwcts->typemant == $obj->typemant && $objmwcts->speciality == $obj->speciality_job) $workingclass = $objmwcts->working_class;
                        else $workingclass = 'generic';
                        $aDatatask[$workingclass][$obj->typemant][$obj->speciality_job]+=$obj->task;
                        $aDataot[$workingclass][$obj->typemant][$obj->speciality_job]+=1;
                        if (!empty($aDatares[$workingclass][$obj->typemant][$obj->speciality_job]))
                            $aDatares[$workingclass][$obj->typemant][$obj->speciality_job].= ';';
                        $aDatares[$workingclass][$obj->typemant][$obj->speciality_job].=$obj->ref;
                    }
                }
            }
            $_SESSION['aDatatask'] = $aDatatask;
            $_SESSION['aDataot']   = $aDataot;

            print "<div class=\"tabsAction\">\n";

            print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/report/otr_excel.php"title="'.$langs->trans('Export to excel').'">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/mant/img/excel-icon.png','',true).'</a>';
            print '</div>';

            print '<table class="noborder" width="100%">';
            print '<tr class="liste_titre">';
            print_liste_field_titre($langs->trans("Clasif 1"));
            print_liste_field_titre($langs->trans("Clasif 2"));
            print_liste_field_titre($langs->trans("Speciality"),'','','','','align="left"');
            print_liste_field_titre($langs->trans("Nro. OT"),'','','','','align="right"');
            print_liste_field_titre($langs->trans("Nro. Tareas"),'','','','','align="right"');
            print '</tr>';
            $totalot = 0;
            $totaltask = 0;
            //recorriendo adata
            foreach((array) $aDatatask AS $working => $aDatatype)
            {
                foreach ((array) $aDatatype AS $typemant => $aDataspec)
                {
                    foreach ((array) $aDataspec AS $speciality => $value)
                    {
                        print '<tr>';
                        print '<td>';
                        print ($working == 'generic'?$langs->trans('Generic'):select_working_class($working,'','',0,1));
                        print '</td>';
                        print '<td>';
                        print select_typemant($typemant,'','',0,1);
                        print '</td>';
                        print '<td>';
                        print select_speciality($speciality,'','',0,1);
                        print '</td>';
                        print '<td align="right">';
                        print '<a href="#" title="'.$aDatares[$working][$typemant][$speciality].'">'.$aDataot[$working][$typemant][$speciality].'</a>';
                        print '</td>';
                        print '<td align="right">';
                        print $value;
                        print '</td>';
                        print '</tr>';
                        $totalot += $aDataot[$working][$typemant][$speciality];
                        $totaltask += $value;
                    }
                }
            }
            //totales
            print '<tr class="liste_total">';
            print '<td align="right" colspan="3" class="liste_total">'.$langs->trans("Total").':</td>';
            print '<td align="right">';
            print $totalot;
            print '</td>';
            print '<td align="right">';
            print $totaltask;
            print '</td>';
            print '</table>';
            print "<div class=\"tabsAction\">\n";
            print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/report/otr_excel.php">'.$langs->trans('Spreadsheet').'</a>';
            print '</div>';
        }
    }
}


llxFooter();

$db->close();
?>
