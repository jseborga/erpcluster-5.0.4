<?php

//Contrato
/////////////////////////////
if ($idProcess > 0)
{
    $a = true;
    $lAddcontrat = false;
    $lAdvance = false;
    $sumacont = array();
    $nSumacont = 0;
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
                        $aContratAdd[$objcontade->id] = array('ref' => $objcontade->array_options['options_ref_contrato'],'note' => $objcontade->note_private,'amount' => $objadden->aSuma['parcial_ttc'][$obja->fk_contrat_son]);
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
        $nSumacont += $total_ttc;
        if ($nSumacont >= $sumapre) $lAddcontrat = false;
        else $lAddcontrat = true;

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
        print '<a  class="btn btn-primary btn-sm bg-green" href="'.DOL_URL_ROOT.'/poa/process/fiche_pas1.php?ida='.$ida.'&id='.$idProcess.'&idp='.$object->id.'&dol_hide_leftmenu=1" title="'.$langs->trans('Committed').'">';
        print $langs->trans('Comp').'&nbsp;'.price($total_ttc);
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

            $objpcon->fetch('',$idProcess,$i);
            if ($action == 'editorder')
            {
                if($selidrc == $idProcess && $selidc == $i)
                {
                    print $langs->trans('Ordertoproceed');
                    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'">';
                    print '<input type="hidden" name="action" value="updateprocescontrat">';
                    print '<input type="hidden" name="idpc" value="'.$objpcon->id.'">';
                    print $form->select_date($objpcon->date_order_proceed,'op_',0,0,1);
                    print '<input class="button" type="submit" value="'.$langs->trans('Save').'">';
                    print '</form>';
                }
            }
            else
            {
                if (empty($objpcon->date_order_proceed) || is_null($objpcon->date_order_proceed))
                {
                    print '<a  class="btn btn-primary btn-sm bg-green" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&selidrc='.$idProcess.'&selidc='.$i.'&dol_hide_leftmenu=1&action=editorder" title="'.$langs->trans('Ordertoproceed').'">';

                    print '<button class="">'.$langs->trans('Ordertoproceed').'</button>';
                    print '</a>';
                }
                else
                {
                    print $langs->trans('Ordertoproceed').'&nbsp;';
                    print '<a  class="btn btn-primary btn-sm bg-green" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&selidrc='.$idProcess.'&selidc='.$i.'&dol_hide_leftmenu=1&action=editorder" title="'.$langs->trans('Ordertoproceed').'">';
                    print dol_print_date($objpcon->date_order_proceed,'day');
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
    if ($lAddcontrat)
    {
        print '<li>';
        print '<div class="timeline-item">';
        print '<div class="box box-solid bg-green">';
        print '<h3>'.$langs->trans('Newcontrat').'&nbsp;';
        print '</h3>';

        //link para crear uno nuevo
        if ($user->rights->poa->comp->crear )
            if ($user->admin || ($user->id == $objprev->fk_user_create && $objact->statut>0 && $objact->statut < 9))
            {
                print '<a  class="btn btn-primary btn-sm bg-green" href="'.DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$idProcess.'&action=create&dol_hide_leftmenu=1">';
                print img_picto($langs->trans('New'),'edit_add');
                print '</a>';
                print '<a href="#responsive" role="button" class="btn" data-toggle="modal">Launch demo modal</a>';

                print '<button class="btn btn-primary btn-lg" href="#responsive" role="button" data-toggle="modal">Add</button>';

                //insertamos el formulario para cargar contratos
                include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_contrat.tpl.php';
                /*print '    <div id="responsive" class="modal fade" tabindex="-1" data-width="760" style="display: none;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Responsive</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h4>Some Input</h4>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
          </div>
          <div class="col-md-6">
            <h4>Some More Input</h4>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
            <p><input class="form-control" type="text"></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>';
    */
            }
        print '</div>';
        print '</div>';
        print '</li>';
    }
}
?>