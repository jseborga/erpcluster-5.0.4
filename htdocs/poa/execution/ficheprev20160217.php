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
 *      \file       htdocs/poa/poa/liste.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page liste des poa
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/structure/class/poastructure.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivity.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidapre.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidacom.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");
require_once DOL_DOCUMENT_ROOT."/poa/process/class/poaprocesscontrat.class.php";
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/guarantees/class/poaguarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/appoint/class/poacontratappoint.class.php';
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poagraf.lib.php");

require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if ($conf->addendum->enabled)
  require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

if ($conf->poai->enabled)
  {
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
  }
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load("poa@poa");

if (!$user->rights->poa->prev->leer)
    accessforbidden();
$_SESSION['localuri'] = $_SERVER['REQUEST_URI'];
$object = new Poaprev($db);
$objact = new Poaactivity($db);
$objpoa = new Poapoa($db);
$objstr = new Poastructure($db);
$objproc = new Poaprocess($db);
$objproccont = new Poaprocesscontrat($db);
$objgua = new Poaguarantees($db);
$objapp = new Poacontratappoint($db);
$objuser = new User($db);
$objpre = new Poapartidapre($db);
$objcom = new Poapartidacom($db);
$objcont = new Contrat($db);
$objsoc  = new Societe($db);
$extrafields = new ExtraFields($db);

if ($conf->addendum->enabled)
  $objadden = new Addendum($db);
$extralabels=$extrafields->fetch_name_optionals_label($objcont->table_element);

//unset($_SESSION['aLisprev']);
if ($conf->poai->enabled)
{
    $objinst = new Poaiinstruction($db);
    $objmoni = new Poaimonitoring($db);
}

//asignando filtro de usuario
assign_filter_user('psearch_user');

$id     = GETPOST('id' ,'int'); //preventivo id
$ida    = GETPOST('ida','int'); //actividad id //esto se recibe
$idpc   = GETPOST('idpc','int');
$action = GETPOST('action');

$selidrc = GETPOST('selidrc','int');
$selidc  = GETPOST('selidc','int');

//variables fijas
$aContratpay = array();
//color
poa_grafic_color();
//recuperamos la actividad
if ($ida > 0) $objact->fetch($ida);
if ($objact->fk_prev) $id = $objact->fk_prev;

/*
ACTIONS
*/
if ($action == 'updateprocescontrat' && $idpc)
{
  $date_order_proceed = dol_mktime(12, 0, 0, GETPOST('op_month'),GETPOST('op_day'),GETPOST('op_year'));

  $objproccont->fetch($idpc);
  if ($objproccont->id == $idpc)
  {
    $objproccont->date_order_proceed = $date_order_proceed;
    $res = $objproccont->update($user);
    if (!$res)
      $error++;
    else
      $action = '';
  }
}
//cabecera
//$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
//$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
//llxHeader("",$langs->trans("Activity"),$help_url,'','','',$aArrjs,$aArrcss);

header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css','poa/css/AdminLTE.css');
$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css');
$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');

top_htmlhead($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';

//cuerpo
print '<br><br><br>';
print '<section class="content">';
print '<div class="row">';


//structure
print '<div class="col-md-6">';
print '<div class="small-box bg-aqua">';
print '<div class="inner">';

print '<table class="noborder" id="tabla" width="100%">';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Code"),"", "","","",'width="5%" align="center" ');
print_liste_field_titre($langs->trans("Structure POA"),"", "","","",'width="95%" align="center"');
print '</tr>';

//detalla la actividad
$loop = true;
$objpoa->fetch($objact->fk_poa);
$fk_str = $objpoa->fk_structure;
$aStr = array();
while ($loop == true)
{
    $objstr->fetch($fk_str);
    $aStr[$objstr->pos]['label'] = $objstr->label;
    $aStr[$objstr->pos]['sigla'] = $objstr->sigla;
    if ($objstr->fk_father >0) $fk_str = $objstr->fk_father;
    else $loop = false;
}
ksort($aStr);
foreach ((array) $aStr AS $pos => $aData)
{
  print '<tr>';
  print '<td align="left">';
  print $aData['sigla'];
  print '</td>';
  print '<td>';
  print $aData['label'];
  print '</td>';
  print '</tr>';
}
print '</table>';

print '</div>';
print '</div>';
print '</div>';


//actividad
print '<div class="col-md-6">';
print '<div class="small-box bg-aqua">';
print '<div class="inner">';

print '<table class="noborder" id="tabla" width="100%">';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Activity"),"", "","","",'width="6%" align="center" ');
print_liste_field_titre($langs->trans("Name"),"", "","","",'width="35%" align="center"');
//print_liste_field_titre($langs->trans("Meta"),"", "","","",'width="35%" align="center"');
print_liste_field_titre($langs->trans("Partida"),"", "","","",'width="8%" align="center"');
print_liste_field_titre($langs->trans("Amount"),"", "","","",'width="8%" align="center"');
print '</tr>';
//detalla la actividad
print '<tr>';
print '<td align="center">';
print '<a href="'.DOL_URL_ROOT.'/poa/activity/fiche.php?id='.$objact->id.'&dol_hide_leftmenu=1">'.$objact->nro_activity.'</a>';
print '</td>';
print '<td>';
print $objact->label;
print '</td>';
//print '<td>';
//print $objpoa->ref.' '.$objpoa->label;
//print '</td>';
print '<td align="center">';
print $objact->partida;
print '</td>';
print '<td align="right">';
print price($objact->amount);
print '</td>';
print '</tr>';
print '</table>';
print '</div>';
print '</div>';
print '</div>';
//fin actividad
print '</div>'; //row

print '<div class="row">';
print '<div class="col-md-12">';
print '<ul class="timeline">';

//si existe ID es el preventivo
if ($id > 0)
{
    $sumapre = 0;
    $sumacont = array();
    $sumacom = array();
    $sumapay = array();
    $aSocname = array();
    $aContratcode = array();
    $aProcesscontrat = array();
    $aFactdoc['Payments'] = 0;
    $aFactdoc['anticipo'] = 0;
    $_SESSION['aLisprev'] = prev_ant($id,$_SESSION['aLisprev']);
    $data = $_SESSION['aLisprev'][$id];
    //proceso
    if ($data['idprocessant'])
      $idProcess = $data['idprocessant'];
    else
      $idProcess = $data['idprocess'];

    //armamos el array para procesar los contratos, comprometidos, pagos
    unset($_SESSION['aListip'][$idProcess]);
    $_SESSION['aListip'][$idProcess]['idAct']=$ida;
    $_SESSION['aListip'][$idProcess]['gestion']=$objact->gestion;
    $_SESSION['aListip'][$idProcess]['idPrev']=$id;
    $_SESSION['aListip'][$idProcess]['idPrevant'] = $data['idprevant'];

    $objproc->fetch($idProcess);
    //verificando tipo de formulario excel
    $aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
    $aDateobj = dol_getdate($objproc->date_process);
    $aDateobj = dol_mktime(0, 0, 1, $aDateobj['mon'],$aDateobj['mday'],$aDateobj['year']);
    if ($aDateobj <= $aDatelim)
      $lForm = true;
    else
      $lForm = false;

    $aFact = array();

    //contratos
    $aContrat = array();
    if (count($data['contrat'])>0)
    {
      foreach ($data['contrat'] AS $fkcontrat => $idc)
      {
        $aContrat[$fkcontrat] = $fkcontrat;
        $aProcesscontrat[$fkcontrat] = $idc;
        $_SESSION['aListip'][$idProcess]['idContrat'] = $fkcontrat;
        $_SESSION['aListip'][$idProcess]['idc'] = $idc;
      }
    }

    // if (count($aContrat)>0)
    //   ksort($aContrat);
    $htmlother=new FormOther($db);

    $form=new Form($db);
    //totales para monto y conteo
    $nTotalPrev = 0;
    $nCountPrev = 0;


    $celcolor1a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['PREVENTIVE']].'; color:#000;"';
    $celcolor2a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['INI_PROCES']].'; color:#000;"';
    $celcolor3a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['RECEP_PRODUCTS']].';color:#FFF;"';
    $celcolor4a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['RECEP_PRODUCTS']].';color:#FFF;"';
    $celcolor5a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['AUT_PAYMENT']].';color:#000;"';
    $celcolor6a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['PARTIAL_REPORT_ACCORDANCE']].';color:#000;"';

    $celcolor1 = ' style="background:#D2E7E7;color:#000;"';
    $celcolor2 = ' style="background:#D7DDDD;color:#000;"';
    $celcolor3 = ' style="background:#84CCE0;color:#000;"';
    $celcolor4 = ' style="background:#73ABCF;color:#000;"';
    $celcolor5 = ' style="background:#6C8DC7;color:#000;"';

    //preventivo
    //buscamos el preventivo
    if ($object->fetch($id)>0)
    {
      print '<li class="time-label">';
      print '<span class="bg-red">'.dol_print_date($object->date_preventive,'day') .'</span>';
      print '</li>';
      print '<li>';
      print '<i class="fa fa-envelope -blue"></i>';
      print '<div class="timeline-item" >';
      print '<div class="box box-solid bg-maroon">';
      print '<div class="inner">';
      print '<h3 class="box-title">'.$langs->trans('Preventive').'</h3>';
      print '<table width="100%">';

      //buscamos la suma del preventivo
      $totalp = $objpre->getsum($id);
      print '<tr>';
      print '<td>'.'<a class="btn btn-primary btn-sm bg-maroon" href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$object->id.'&dol_hide_leftmenu=1">'.$object->nro_preventive.'/'.$object->gestion.'</a>';
      print '<br>';
      print $object->label;
      print '</td>';
      print '<td align="right">'.price($totalp).'</td>';
      print '</tr>';
      print '</td>';
      print '<td align="left"><b>'.$langs->trans('Statut').'</b> '.$langs->trans($object->getLibStatut(0)).'</td>';
      print '</tr>';

      $sumapre += $totalp;
      //verificamos si tiene hijos
      $objprevh = new Poaprev($db);
      $objprevh->getlistfather($id);
      foreach ((array) $objprevh->arrayf AS $j => $objp)
      {
        print '<tr>';
        print '<td>'.'<a class="btn btn-primary btn-sm bg-maroon" href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$objp->id.'&dol_hide_leftmenu=1">'.$objp->nro_preventive.'/'.$objp->gestion.'</a>';
        print '<br>';
        print $objp->label.'</td>';
        print '<td align="right">'.price($objp->amount).'</td>';
        print '</tr>';
        $sumapre += $objp->amount;
      }
      //total
      print '<tr>';
      print '<td>';
      print $langs->trans('Total');
      print '</td>';
      print '<td align="right">';
      print price($sumapre);
      print '</td>';
      print '</tr>';
      print '</table>';

      print '</div>';
      print '</div>';
      print '</div>';
      print '</li>';
    }

    //process
    print '<li class="time-label">';
    print '<span class="bg-yellow">'.dol_print_date($objproc->date_process,'day') .'</span>';
    print '</li>';
    print '<li>';
    print '<div class="timeline-item">';
    print '<div class="box box-solid bg-yellow">';
    print '<h3>'.$langs->trans('Inicio Proceso').'</h3>';
    print '<div class="inner">';

    if ($objproc->id)
    {
        print '<table width="100%">';
        print '<tr>';
        print '<td width="50%">';
        print $langs->trans('Nro. Process');
        if ($lForm)
        {
            print '&nbsp;<a class="btn btn-primary btn-sm bg-yellow" href="'.DOL_URL_ROOT.'/poa/process/fiche_iniproc.php?id='.$idProcess.'&dol_hide_leftmenu=1" title="'.$langs->trans('Excel').'">';
            print '&nbsp;'.img_picto($langs->trans('Exportexcel'),DOL_URL_ROOT.'/poa/img/excel-icon','',true);
            print '</a>';
        }
        else
        {
            print '&nbsp;<a class="btn btn-primary btn-sm bg-yellow" href="'.DOL_URL_ROOT.'/poa/process/fiche_iniproc_20150901.php?id='.$idProcess.'&dol_hide_leftmenu=1" title="'.$langs->trans('Excel').'">';
            print '&nbsp;'.img_picto($langs->trans('Exportexcel'),DOL_URL_ROOT.'/poa/img/excel-icon','',true);
            print '</a>';
        }
        print '</td>';
        print '<td width="40%" align="right">';
        print '<a class="btn btn-primary btn-sm bg-yellow" href="'.DOL_URL_ROOT.'/poa/process/fiche.php?id='.$idProcess.'&dol_hide_leftmenu=1">'.$objproc->ref.'</a>';
        print '</td>';
        print '</tr>';
        print '</table>';
    }
    else
    {
        //link para crear uno nuevo
        print '<a class="btn btn-primary btn-sm" href="'.DOL_URL_ROOT.'/poa/process/fiche.php?fk_poa_prev='.$object->id.'&ida='.$ida.'&action=search&dol_hide_leftmenu=1">';
        print img_picto($langs->trans('New'),'edit_add');
        print '</a>';
    }
    print '</div>';
    print '</div>';
    print '</div>';
    print '</li>';

  //Contrato
  /////////////////////////////
    if ($idProcess > 0)
    {
        $a = true;
        $lAddcontrat = false;
        $lAdvance = false;
        $sumacont = array();
        $aContratpay = array();
        if (count($aContrat) <= 0)
            if ($objproc->statut > 0)
                $lAddcontrat = true;
        foreach((array) $aContrat AS $i => $ni)
        {
            $objcont->fetch($i);
            $objcont->fetch_lines();
            $a = !$a;
            $contratAdd = '';
            $aContratAdd = array();
            $total_ht = 0;
            $total_tva = 0;
            $total_localtax1 = 0;
            $total_localtax2 = 0;
            $total_ttc = 0;
            //revisamos el contrato
            $res=$objcont->fetch_optionals($i,$extralabels);
            if ($objcont->array_options['options_advance']) $lAdvance = true;
            if (!$objcont->array_options['options_order_proced']) $aContratpay[$i] = true;
            $contratAdd.= $objcont->array_options['options_ref_contrato'];
            $aContratname[$i] = $objcont->array_options['options_ref_contrato'];
            if ($objcont->id == $i)
            {
                $total_plazo += $objcont->array_options['options_plazo'];
                //recuperamos el valor de contrato
                foreach ($objcont->lines AS $olines)
                {
                    if (empty($olines->qty)) $lAddcontrat = true;
                    $total_ttc += $olines->$total_ttc;
                }

                $datecontrat= $objcont->date_contrat;
                //buscamos si tiene addendum
                if ($conf->addendum->enabled)
                {
                    $objadden = new Addendum($db);
                    $res = $objadden->getlist($i);
                    if ($res>0)
                    {
                        $total_ht += $objadden->aSuma['total_ht'];
                        $total_tva += $objadden->aSuma['total_tva'];
                        $total_localtax1 += $objadden->aSuma['total_localtax1'];
                        $total_localtax2 += $objadden->aSuma['total_localtax2'];

                        $total_ttc += $objadden->aSuma['total_ttc'];
                        $aContratAdd[$objcont->id] = array('ref' => $objcont->array_options['options_ref_contrato'], 'note' => $objcont->note_private, 'amount' => $objadden->aSuma['parcial_ttc'][$i]);

                        //verificamos los plazos adicionales
              foreach ((array) $objadden->array AS $j1 => $obja)
              {
                $objcontade = new Contrat($db);
                $objcontade->fetch($obja->fk_contrat_son);
                if ($objcontade->id == $obja->fk_contrat_son)
                  $total_plazo += $objcontade->array_options['options_plazo'];
                $aContratAdd[$objcontade->id] = array('ref' => $objcontade->array_options['options_ref_contrato'],
				       'note' => $objcontade->note_private,
				       'amount' => $objadden->aSuma['parcial_ttc'][$obja->fk_contrat_son]);
                if (!empty($contratAdd))$contratAdd.=', ';
                $contratAdd.= $objcontade->array_options['options_ref_contrato'];
              }
            }
            else
            {
              //recuperamos el valor de contrato
              foreach ($objcont->lines AS $olines)
              {
                $total_ht += $olines->total_ht;
                $total_tva += $olines->total_tva;
                $total_localtax1 += $olines->total_localtax1;
                $total_localtax2 += $olines->total_localtax2;
                $total_ttc += $olines->total_ttc;
              }
            }
          }
          else
          {
            //recuperamos el valor de contrato
            foreach ($objcont->lines AS $olines)
            {
              $total_ht += $olines->total_ht;
              $total_tva += $olines->total_tva;
              $total_localtax1 += $olines->total_localtax1;
              $total_localtax2 += $olines->total_localtax2;
              $total_ttc += $olines->total_ttc;
            }
          }
        }
        $aContratcode[$i] = $contratAdd;
        print '<li class="time-label">';
        print '<span class="bg-green">'.dol_print_date($datecontrat,'day') .'</span>';
        print '</li>';
        print '<li>';
        print '<div class="timeline-item">';
        print '<div class="box box-solid bg-green">';
        print '<h3>'.$langs->trans('Contrat').'&nbsp;';
        if ($lAddcontrat)
        {
          //link para crear uno nuevo
          if ($user->rights->poa->comp->crear )
            if ($user->admin || ($user->id == $objprev->fk_user_create && $objact->statut>0 && $objact->statut < 9))
            {
              print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$idProcess.'&action=create&dol_hide_leftmenu=1">';
              print img_picto($langs->trans('New'),'edit_add');
              print '</a>';
            }
        }

        print '</h3>';
        print '<div class="table-responsive dataTables_wrapper">';
        print '<table class="table table-condensed dataTable" role="grid">';

        print '<tr role="row">';
        print '<td>';
        if (!empty($aContratname[$i]))
          print '<a class="btn btn-primary btn-sm bg-green" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" title="'.$langs->trans('Contrat').'" target="blank_">'.$aContratname[$i].'</a>';
        else
          print '<a class="btn btn-primary btn-sm bg-green" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" title="'.$langs->trans('Contrat').'" target="blank_">'.$obj->ref.'</a>';
        print '</td>';
        print '<td>';
        $objsoc->fetch($objcont->fk_soc);
        $aSocname[$objcont->fk_soc] = $objsoc->nom;
        print $objsoc->nom;
        print '</td>';
        print '<td align="right">';
        print price($total_ttc);
        print '</td>';
        $sumacont[$i] += $total_ttc;

        //agregamos tema de comprometido
        $objcom->get_sum_pcp2($id,$i);
        $total_ttc = $objcom->total;
        print '<td align="right">';
        print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$idProcess.'&idp='.$object->id.'&dol_hide_leftmenu=1" title="'.$langs->trans('Committed').'">';
        print $langs->trans('Committed').'&nbsp;'.price($total_ttc);
        print '&nbsp;'.img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/process','',1);
        print '</a>';
        print '</td>';
        $sumacom[$i] += $total_ttc;
        //fin comprometido

        //orden de proceder
        if ($objcont->array_options['options_order_proced'])
        {
          $aContratpay[$i] = false;
          print '<td align="center" style ="color:#000;">';

          $objproccont->fetch('',$idProcess,$i);
          if ($action == 'editorder')
          {
            if($selidrc == $idProcess && $selidc == $i)
            {
              print $langs->trans('Ordertoproceed');
              print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'">';
              print '<input type="hidden" name="action" value="updateprocescontrat">';
              print '<input type="hidden" name="idpc" value="'.$objproccont->id.'">';
              print $form->select_date($objproccont->date_order_proceed,'op_',0,0,1);
              print '<input class="button" type="submit" value="'.$langs->trans('Save').'">';
              print '</form>';
            }
          }
          else
          {
            if (empty($objproccont->date_order_proceed) || is_null($objproccont->date_order_proceed))
            {
              print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&selidrc='.$idProcess.'&selidc='.$i.'&dol_hide_leftmenu=1&action=editorder" title="'.$langs->trans('Ordertoproceed').'">';

              print '<button class="">'.$langs->trans('Ordertoproceed').'</button>';
              print '</a>';
            }
            else
            {
              print $langs->trans('Ordertoproceed').'&nbsp;';
              print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&selidrc='.$idProcess.'&selidc='.$i.'&dol_hide_leftmenu=1&action=editorder" title="'.$langs->trans('Ordertoproceed').'">';
              print dol_print_date($objproccont->date_order_proceed,'day');
              print '</a>';
              $aContratpay[$i] = true;
            }
          }
          print '</td>';
        }
        print '</tr>';

        //si existe addendum recorremos los mismos para ver
        if (count($aContratAdd)>0)
        {
          print '<tr>';
          print '<td colspan="3">'.$langs->trans('Detailcontrat').' '.$langs->trans('And').' '.$langs->trans('Addendums').'</td>';
          print '</tr>';
        }
        foreach ((array) $aContratAdd AS $fk_c => $aCoderef)
        {
          $a != $a;
          print "<tr>";
          print '<td>';
          print '<a class="btn btn-primary btn-sm bg-green" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$fk_c.'" target="blank_">'.$aCoderef['ref'].'</a>';
          print '</td>';
          print '<td>';
          print $aCoderef['note'];
          print '</td>';
          print '<td align="right">';
          print price($aCoderef['amount']);
          print '</td>';
          print '</tr>';
        }
        print '</table>';
        print '</div>';
        print '</div>';
        print '</div>';
        print '</li>';
      }
      //se tiene que agregar para nuevo
      print '<li>';
      print '<div class="timeline-item">';
      print '<div class="box box-solid bg-green">';
      print '<h3>'.$langs->trans('Newcontrat').'&nbsp;';
      if ($lAddcontrat)
      {
	      //link para crear uno nuevo
	      if ($user->rights->poa->comp->crear )
          if ($user->admin || ($user->id == $objprev->fk_user_create && $objact->statut>0 && $objact->statut < 9))
          {
            print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$idProcess.'&action=create&dol_hide_leftmenu=1">';
            print img_picto($langs->trans('New'),'edit_add');
            print '</a>';
          }
      }
      print '</h3>';
      print '</div>';
      print '</div>';
      print '</li>';
  }


	//designations
	if ($idProcess > 0)
	{
	    print '<li>';
	    print '<div class="timeline-item">';
	    print '<div class="box box-solid bg-light-blue">';
	    print '<h3>'.$langs->trans('Designations').'</h3>';
	    print '<div class="inner">';

	    print '<table  width="100%">';
	    print "<tr class=\"liste_titre\">";
	    print_liste_field_titre($langs->trans("Type"),"", "","","",'width="20%" align="center" ');
	    print_liste_field_titre($langs->trans("Name"),"", "","","",'width="20%" align="center" ');
	    print_liste_field_titre($langs->trans("Date"),"", "","","",'width="20%" align="center" ');
	    print '</tr>';
	    $a = true;
	    foreach((array) $aContrat AS $i => $ni)
	      {
		//contrato
		$objcont->fetch($i);
		$a = !$a;
		//$aContratname[$i] = $objcont->array_options['options_ref_contrato'];
		//$aContratcode[$i] = $contratAdd;
		print "<tr $bc[$a]>";
		print '<td colspan="3">';
		if (!empty($aContratname[$i]))
		  print '<a href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
		else
		  print '<a href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$obj->ref.'</a>';
		print '&nbsp;';
		print $aSocname[$objcont->fk_soc];
		print '</td>';
		print '</tr>';

		//lista designaciones appoint
		$objapp->getlist($i);
		if (count($objapp->array)>0)
		  $a = !$a;
		foreach ((array) $objapp->array AS $j=> $objg)
		  {
		    //type guarantee
		    print "<tr $bc[$a]>";
		    print '<td>'. select_code_appoint($objg->code_appoint,'code_appoint','',0,1);
		    print '</td>';

		    //user
		    print '<td>';
		    $res = $objuser->fetch($objg->fk_user);
		    if ($res > 0 && $objuser->id == $objg->fk_user)
		      print '<a href="'.DOL_URL_ROOT.'/poa/appoint/fiche.php?id='.$objg->id.'&idpro='.$idProcess.'">'.$objuser->lastname.' '.$objuser->firstname.'</a>';
		    print '</td>';

		    //date
		    print '<td>';
		    print dol_print_date($objg->date_appoint,'day');
		    print '</td>';

		    print '</tr>';
		  }
	      }
	    if ($user->rights->poa->appoint->crear)
	      {
		if ($user->admin || $objact->statut>0 && $objact->statut < 9)
		  {
		    print '<tr>';
		    print '<td colspan="3" align="center">';
		    print '<a href="'.DOL_URL_ROOT.'/poa/appoint/fiche.php?idc='.$i.'&idpro='.$idProcess.'&action=create'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('New'),'edit_add').'</a>';
		    print '</td>';
		    print '</tr>';
		  }
	      }
	    print '</table>';
	    print '</div>';
	    print '</div>';
	    print '</div>';
	  }


  foreach((array) $aContrat AS $i => $ni)
  {
    if ($aContratpay[$i])
    {
	    $objcont->fetch($i);
	    $objcont->fetch_lines();

	    $array = $arraypay[$obj->id];
	    //obtenemos la suma de acuerdo al tipo de factura

	    //payment
	    if (count($data['idprev'])>0) ksort($data['idprev']);
	    $a = true;
	    foreach ($data['idprev'] AS $k)
      {
        $a = !$a;
        $apay = listpayment($k,$i);
        foreach ((array) $apay AS $l => $objp)
        {
          if (!empty($objp->invoice))
          {
            $type = 'Payments';
            if (($objp->date_dev*1) >= ($aFactdoc['Payments']*1))
              $aFactdoc['Payments'] = $objp->date_dev;
            $aFact['Payments']+= $objp->amount;
		      }
          else
		      {
            if ($lAdvance)
            {
              $_SESSION['aListip'][$idProcess]['anticipo'] = true;
              $type = 'anticipo';
              if ($objp->date_dev >= $aFactdoc['anticipo'])
                $aFactdoc['anticipo'] = $objp->date_dev;
              $aFact['anticipo']+= $objp->amount;
            }
            else
            {
              $_SESSION['aListip'][$idProcess]['Payments'] = true;
              $type = 'Payments';
              if ($objp->date_dev >= $aFactdoc['Payments'])
                $aFactdoc['Payments'] = $objp->date_dev;
              $aFact['Payments']+= $objp->amount;
            }
          }

  		    print '<li class="time-label">';
	 	      print '<span class="bg-aqua">'.dol_print_date($objp->date_dev,'day') .'</span>';
	   	    print '</li>';
		      print '<li>';
		      print '<div class="timeline-item">';
		      print '<div class="box box-solid bg-aqua">';
		      print '<h3>'.$langs->trans('Payments').'</h3>';
		      print '<div class="inner">';
		      print '<table width="100%">';

		      print "<tr $bc[$a]>";
		      print '<td>';
		      if (!empty($aContratcode[$i]))
		        print $aContratcode[$i];
		      else
		        print $objcont->ref;
		      print '</td>';
		      print '<td>';
		      print $langs->trans($type);
		      print '</td>';
		      print '<td>';
		      print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas2.php?id='.$idProcess.'&dol_hide_leftmenu=1" title="'.$langs->trans('Viewpayment').'">';
		      print $objp->nro_dev.'/'.$objp->gestion;
		      print '</a>';
		      print '</td>';
  		    print '<td align="right">';
          print price($objp->amount);
		      print '</td>';
		      print '<td align="right">';
		      print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_autpay.php?id='.$idProcess.'&idr='.$objp->id.'" title="'.$langs->trans('Excel').'">';
		      print '&nbsp;'.img_picto($langs->trans('Exportexcel'),DOL_URL_ROOT.'/poa/img/excel-icon','',true);
		      print '</a>';
		      print '</td>';
		      print '</tr>';
		      print '</table>';
		      print '</div>';
		      print '</div>';
		      print '</div>';
		      print '</li>';
		      if ($type == 'Payments' && $object->gestion == $objp->gestion)
		        $sumapay[$i] += $objp->amount;
		      if ($type == 'Payments')
		        $sumapayt[$i] += $objp->amount;
		    }
      }

	    //saldo final gestion actual
	    print '<li>';
	    print '<div class="timeline-item">';
	    print '<div class="box box-solid bg-aqua">';
	    print '<h3>'.$langs->trans('Balance').'</h3>';
	    print '<div class="inner">';
	    print '<table width="100%">';
	    print '<tr>';
	    print '<td colspan="3">';
	    $saldo = $sumacom[$i] - $sumapay[$i];
	    if ($saldo > 0)
      {
          print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas2.php?id='.$idProcess.'&idrc='.$idrc.'&dol_hide_leftmenu=1" title="'.$langs->trans('Createpayment').'">';
		      print $langs->trans('Createpayment');
		      print '&nbsp;'.img_picto($langs->trans('Createpayment'),DOL_URL_ROOT.'/poa/img/deve','',true);
		      print '</a>';
      }
	    else
	      print $langs->trans('Balance');

	    print '</td>';
	    print '<td align="right">';
	    print price(price2num($saldo,'MT'));
	    print '</td>';
	    print '</tr>';

	    print '<tr>';
	    print '<td colspan="3">';
	    print $langs->trans('Balance contract');
	    print '</td>';
	    print '<td align="right">';
	    print price($sumacont[$i] - $sumapayt[$i]);
	    print '</td>';
	    print '</tr>';

	    print '</table>';
	    print '</div>';
	    print '</div>';
	    print '</div>';
	    print '</li>';
    }
  }

  //receptions
	if ($idProcess > 0)
	  {
	    $a = true;
	    $lAddcontrat = false;
	    if (count($aContrat) <= 0)
	      if ($objproc->statut > 0)
		$lAddcontrat = true;
	    // print "<tr class=\"liste_titre\">";

	    // print_liste_field_titre($langs->trans("Contrat"),"", "","","",'width="25%" align="center" ');
	    // print_liste_field_titre($langs->trans("Ini"),"", "","","",'width="25%" align="center" ');
	    // print_liste_field_titre($langs->trans("Fin"),"", "","","",'width="25%" align="center" ');
	    // print_liste_field_titre($langs->trans("Recep."),"", "","","",'width="25%" align="center" ');
	    // print '</tr>';

	    foreach((array) $aContrat AS $i => $ni)
	      {
		$objcont->fetch($i);
		$objcont->fetch_lines();

		$a = !$a;
		$lClosecontrat = true;
		$date_cloture = '';
		foreach ((array) $objcont->lines AS $k => $objl)
		  {
		    $objcontline = new Contratligne($db);
		    $objcontline->fetch($objl->id);
		    if ($objcontline->id == $objl->id)
		      {
			$fk_cl = $objcontline->id;
			$date_ouverture = $objcontline->date_ouverture;
			$date_fin_validite = $objcontline->date_fin_validite;
			$date_cloture = $objcontline->date_cloture;
			if (empty($objcontline->date_cloture) ||
			    is_null($objcontline->date_cloture)
			    )
			  {
			    $lClosecontrat = false;
			  }
		      }
		    else
		      $lClosecontrat = false;
		  }
		if ($lClosecontrat)
		  {
		    //imprimimos

		  }
		print '<li>';
		print '<div class="timeline-item">';
		print '<div class="box box-solid bg-purple">';
		print '<h3>'.$langs->trans('Reception').'</h3>';
		print '<div class="table-responsive">';

		print '<table class="table table-condensed">';

		print "<tr>";
		print '<td>';
		if (!empty($aContratname[$i]))
		  print '<a class="btn btn-primary btn-sm bg-purple" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
		else
		  print '<a class="btn btn-primary btn-sm btn.bg-purple" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$objcont->ref.'</a>';
		print '</td>';
		print '<td width="20%">';
		print $langs->trans('Ini').' '.dol_print_date($date_ouverture,'day');
		print '</td>';
		print '<td width="20%">';
		print $langs->trans('Fin').' '.dol_print_date($date_fin_validite,'day');
		print '</td>';
		print '<td width="20%">';
		print $langs->trans('Recep.').' '.dol_print_date($date_cloture,'day');
		print '</td>';
		print '</tr>';
		print '</table>';
		print '</div>';
		print '</div>';
		print '</div>';
		print '</li>';
	      }
	  }

	//guarantees
	if ($idProcess > 0)
	  {
	    //      print '<li class="time-label">';
	    //      print '<span class="bg-navy">'.dol_print_date($objproc->date_process,'day') .'</span>';
	    //      print '</li>';
	    print '<li>';
	    print '<div class="timeline-item">';
	    print '<div class="box box-solid bg-navy">';
	    print '<h3>'.$langs->trans('Guarantees').'</h3>';
	    print '<div class="inner">';

	    print '<table  width="100%">';
	    print "<tr class=\"liste_titre\">";
	    print_liste_field_titre($langs->trans("Type"),"", "","","",'width="30%" align="center" ');
	    print_liste_field_titre($langs->trans("Ref"),"", "","","",'width="10%" align="center" ');
	    print_liste_field_titre($langs->trans("Issuer"),"", "","","",'width="20%" align="center" ');
	    print_liste_field_titre($langs->trans("Dateinicio"),"", "","","",'width="10%" align="center" ');
	    print_liste_field_titre($langs->trans("Datefinal"),"", "","","",'width="10%" align="center" ');
	    print_liste_field_titre($langs->trans("Amount"),"", "","","",'width="20%" align="center" ');
	    print '</tr>';
	    $a = true;
	    foreach((array) $aContrat AS $i => $ni)
	      {
		//contrato
		$objcont->fetch($i);
		$a = !$a;
		$aContratname[$i] = $objcont->array_options['options_ref_contrato'];
		$aContratcode[$i] = $contratAdd;
		print "<tr $bc[$a]>";
		print '<td colspan="6">';
		if (!empty($aContratname[$i]))
		  print '<a href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
		else
		  print '<a href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$obj->ref.'</a>';
		print '&nbsp;';
		//$objsoc->fetch($objcont->fk_soc);
		print $aSocname[$objcont->fk_soc];
		print '</td>';
		print '</tr>';
		//lista las garantias
		//garantias
		$objgua->getlist($i);
		if (count($objgua->array)>0)
		  $a = !$a;
		foreach ((array) $objgua->array AS $j=> $objg)
		  {
		    //type guarantee
		    print "<tr $bc[$a]>";
		    print '<td>'. select_code_guarantees($objg->code_guarantee,'code_guarantee','',0,1);
		    print '</td>';

		    //Ref
		    print '<td>'.'<a href="'.DOL_URL_ROOT.'/poa/guarantees/fiche.php?id='.$objg->id.'&idpro='.$idProcess.'">'.$objg->ref.'</a>';
		    print '</td>';

		    //Issuer
		    print '<td>'.$objg->issuer;
		    print '</td>';

		    // //dateini
		    print '<td>'.dol_print_date($objg->date_ini,'day');
		    print '</td>';

		    //datefin
		    print '<td>'.dol_print_date($objg->date_fin,'day');
		    print '</td>';

		    //amount
		    print '<td align="right">'.price($objg->amount);
		    print '</td>';

		    print '</tr>';
		  }
	      }
	    if ($user->rights->poa->guar->crear)
	      {
		if ($user->admin || $objact->statut>0 && $objact->statut < 9)
		  {
		    print '<tr>';
		    print '<td colspan="6" align="center">';
		    print '<a href="'.DOL_URL_ROOT.'/poa/guarantees/fiche.php?idpro='.$idProcess.'&action=create'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('New'),'edit_add').'</a>';
		    print '</td>';
		    print '</tr>';
		  }
	      }
	    print '</table>';
	    print '</div>';
	    print '</div>';
	    print '</div>';
	    print '</li>';
	  }

	print '<li>';
	print '<i class="fa fa-clock-o"></i>';
	print '</li>';
	print '</ul>';
	print '</div>';
	print '</div>';
	print '</section>';

	print "<div class=\"tabsAction\">\n";
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';

	print '</div>';
      }

    $db->close();

    llxFooter();
?>
