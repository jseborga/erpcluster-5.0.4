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
 *	\file       htdocs/mant/jobs/tpl/programation_view.tpl.php
 *	\ingroup    Ordenes de Trabajo
*	\brief      Page fiche mantenimiento vista programacion de trabajos
 */


if ($object->statut >= 4)
  dol_fiche_head($head, 'card', $langs->trans("Materials"), 0, 'mant');

print '<table class="border" width="100%">';

//title
print "<tr class=\"liste_titre\">";
print '<td colspan="3" align="center">'.$langs->trans('Orders').'</td>';
print '<td colspan="3" align="center" >'.$langs->trans('Used').'</td>';
print '</tr>';

$objorder = new Mjobsorder($db);
$objused  = new Mjobsmaterialused($db);

require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
$objcomm = new Commande($db);
$objprod = new Product($db);
$aOrder = $objorder->list_order($object->id);

$objused->getlist($object->id);

// order
print '<tr>';
print '<td colspan="3" valign="top">';
print '<table class="noborder" width="100%">';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Nro."),"", "","","","");
print_liste_field_titre($langs->trans("Date").'/'.$langs->trans('Ref'),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "","","","");
print_liste_field_titre($langs->trans("Entregado"),"", "","","","");
print_liste_field_titre($langs->trans("Utilizado"),"", "","","","");

//recuperamos por pedido y producto
$aOrderlist = array();
foreach((array) $aOrder AS $m => $objo)
{
  $aOrderlist[$objo->order_number][$objo->fk_product]['quant'] += $objo->quant;  
  $aOrderlist[$objo->order_number][$objo->fk_product]['date'] = $objo->date_order;  
  $aOrderlist[$objo->order_number][$objo->fk_product]['description'] = $objo->description;  
}

// foreach((array) $aOrder AS $m => $objo)
// {
foreach((array) $aOrderlist AS $order_number => $arrproduct)
{
  
  print '<tr>';
  //buscamos el pedido
  $rescom = $objcomm->fetch('',$order_number);
  print '<td>'.$objo->description.'</td>';
  if ($rescom > 0)
    {
      print '<td>'.dol_print_date($objcomm->date_commande,'day').'</td>';
      print '<td>'.$objcomm->note_public.'</td>';     
      if (count($objcomm->lines) >0)
	{
	  print '</tr>';
	  foreach ((array) $objcomm->lines AS $n => $objl)
	    {
	      print '<tr>';
	      print '<td>'.$order_number.'</td>';
	      print '<td>'.$objl->product_ref.'</td>';
	      print '<td>'.$objl->product_label.'</td>';
	      print '<td>'.$objl->qty.'</td>';
	      print '<td>'.$arrproduct[$objl->fk_product]['quant'].'</td>';
	      print '</tr>';
	    }
	} 
    }
  else
    {
      print '<td>'.dol_print_date($objo->date_order,'day').'</td>';
      print '<td>'.$objo->description.'</td>';      
    }
  print '</tr>';
}
print '</table>';
print '</td>';
print '<td colspan="3" valign="top">';
print '<table>';
if (count($objused->array)>0)
  {
    foreach ((array) $objused->array AS $m => $obju)
      {
	print '<tr>';
	print '<td>'.dol_print_date($obju->date_return,'day').'</td>';
	print '<td>'.$obju->ref.'</td>';
	print '<td>'.$obju->description.'</td>';
	print '<td>'.$obju->quant.'</td>';
	print '<td>'.$obju->unit.'</td>';
	print '</tr>';
      }
  }
print '</table>';
print '</td>';
print '</tr>';
print '</div>';	      
	      
?>