<?php
/* Copyright (C) 2014-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/activity/fiche.php
 *	\ingroup    Activities
 *	\brief      Page fiche POA activitie
 */


/*
 * View
 */

$form=new Form($db);

// $aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
// $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("Activity"),$help_url,'','','',$aArrjs,$aArrcss);

$display ='none';
if (isset($modal) && $modal == 'ficheprev_addproduct')
{
	print '<script type="text/javascript">
            $(window).load(function(){
                $("#ficheprev_addproduct").modal("show");
            });
        </script>';
}

print '<div id="ficheprev_add_product" class="modal" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';
//CAMBIAR a none

//print '<form id="InfroText" class="form-horizontal col-sm-12"  name="fiche_comp" action="'.$_SERVER['PHP_SELF'].'" method="post">';
//      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
//      print '<input type="hidden" name="action" value="addcontrat">';
//      print '<input type="hidden" name="id" value="'.$object->id.'">';
//      print '<input type="hidden" name="ida" value="'.$ida.'">';
print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">'.$langs->trans("Activity").'</h4>
</div>';

print '<div class="modal-body" style="background:#fff; color:#000; !important">';
print '<div class="row">';
print '<div class="col-md-12">';
print '<div class="inner">';



//filtro
$idTag1 = 1;
$idTag2 = 2;

//cuerpo

//registro nuevo
print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'editproduct')
  {
    print '<input type="hidden" name="action" value="updatepartidaprod">';
    print '<input type="hidden" name="idppp" value="'.$_GET['idppp'].'">';
  }
 else
   print '<input type="hidden" name="action" value="addpartidaprod">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="idp" value="'.$objpartidapre->id.'">';
print '<input type="hidden" name="ida" value="'.$objact->id.'">';
print '<input type="hidden" name="modal" value="fichepreventive">';

//product
print '<div class="form-group">';
print '<label class="control-label col-sm-2">'.$langs->trans('Product').'</label>';
print '<div class="col-sm-10">';
print '<input class="form-control" type="text" value="'.$objprevdetclon->detail.'" name="detail" maxlength="255" placeholder="'.$langs->trans('Nameproduct').'">';
print '</div>';
print '</div>';

//Quant
print '<div class="form-group">';
print '<label class="control-label col-sm-2">'.$langs->trans('Quantity').'</label>';
print '<div class="col-sm-10">';
print '<input class="form-control" type="number" value="'.$objprevdetclon->quant.'" name="quant" maxlength="12" placeholder="'.$langs->trans('Quantity').'">';
print '</div>';
print '</div>';

//Amount
print '<div class="form-group">';
print '<label class="control-label col-sm-2">'.$langs->trans('Amount').'</label>';
print '<div class="col-sm-10">';
print '<input class="form-control" type="number" step="any" value="'.$objprevdetclon->amount_base.'" name="amount_base" maxlength="12" placeholder="'.$langs->trans('Amount').'">';
print '</div>';
print '</div>';

print '<div class="tabsAction">';
print '<input type="submit"  class="btn btn-primary margin" value="'.$langs->trans('Save').'">';
print '</div>';
print '</form>';


print '</div>';
print '</div>';
print '</div>';
print '</div>';

print '</div>';//modal-content
print '</div>';//modal-dialog
print '</div>';//modal-success">';
print '</div>';//poa-modal
print '</div>';//activity

?>
