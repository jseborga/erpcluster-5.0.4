<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/jobs/tpl/orders.tpl.php
 *	\ingroup    Ordenes de Trabajo
*	\brief      Page fiche mantenimiento agregar y listar los pedidos de materiales
 */

  //registro de trabajos ejecutados
$objproduct = new Product($db);
dol_fiche_head($head, 'card', $langs->trans("Ordermaterials"), 0, DOL_URL_ROOT.'/mant/img/salida',1);

print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () {
              $("#order_number").change(function() {
                document.form_o.action.value="createo";
                document.form_o.submit();
              });
          });';
print '</script>'."\n";

print '<form action="fiche.php" method="POST" name="form_o">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addordern">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';

print_liste_field_titre($langs->trans("Ordernumber"),"", "","","","");
print_liste_field_titre($langs->trans("Date"),"", "","","","");
print_liste_field_titre($langs->trans("Product"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "",'','','');
print_liste_field_titre($langs->trans("Out of stock"),"", "",'','','');
print_liste_field_titre($langs->trans("Unit"),"", "",'','','');
print_liste_field_titre($langs->trans("Quant"),"", "",'','','');
print_liste_field_titre($langs->trans("Action"),"", "",'','','');
print "</tr>\n";
//registro nuevo
$var = true;
print "<tr $bc[$var]>";
print '<td>';
print '<input id="order_number" type="text" value="'.$order_number.'" name="order_number" size="7" maxlength="15" required>';
print '</td>';
print '<td>';
$form->select_date((empty($date_order)?dol_now():$date_order),'do_','','','',"regjobs",1,1);
print '</td>';

print '<td>';
$form->select_produits('','fk_product','',$conf->product->limite_size,0,1,2,'',1);
print '</td>';

print '<td>';
print '<textarea name="description" cols="25" rows="2">'.$description.'</textarea>';
print '</td>';

print '<td>';
print '</td>';

print '<td>';
print '<input type="text" name="unit" value="'.$unit.'" size="10" required>';
print '</td>';

print '<td>';
print '<input type="number" class="len50" min="0.1" step="any" name="quant" value="" required>';
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Saveorder').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';

print '</tr>';
$objJobsOrder = new Mjobsorder($db);
$objcomm = new Commande($db);
$aOrder = $objJobsOrder->list_order($object->id);
foreach ((array) $aOrder AS $j => $objOrder)
{
  $var = !$var;
  print "<tr $bc[$var]>";
  print '<td>'.$objOrder->order_number.'</td>';
  print '<td>'.dol_print_date($objOrder->date_order,'day').'</td>';
  $objproduct->fetch($objOrder->fk_product);
  if ($objproduct->id == $objOrder->fk_product)
    print '<td>'.$objproduct->libelle.'</td>';
  else
    print '<td></td>';
  print '<td>'.$objOrder->description.'</td>';
  print '<td align="center">';
  $rescom = $objcomm->fetch('',$objOrder->order_number);
  if ($rescom == 1)
    {
      foreach ((array) $objcomm->lines AS $k => $objc)
	{
	  if ($objc->fk_product == $objOrder->fk_product)
	    print $objc->qty;
	}
    }
  print '</td>';

  print '<td>'.$objOrder->unit.'</td>';
  print '<td align="right">'.$objOrder->quant.'</td>';
  if ($object->statut == 3)
    {
      print '<td align="center">';
      print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objOrder->id.'&action=delorder'.'">'.img_picto($langs->trans('Deleteitemorder'),'delete').'</a>';
      print '</td>';
    }


  print '</tr>';
}
print "</table>";

print '</form>';

print '</div>';

?>