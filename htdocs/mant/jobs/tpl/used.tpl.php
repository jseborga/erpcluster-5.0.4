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
 *	\file       htdocs/mant/jobs/tpl/used.tpl.php
 *	\ingroup    Ordenes de Trabajo
*	\brief      Page fiche mantenimiento agregar y listar los materiales usados de materiales
 */

  //registro de trabajos ejecutados
dol_fiche_head($head, 'card', $langs->trans("Refund material used"), 0, DOL_URL_ROOT.'/mant/img/cambios',1);

print '<form action="fiche.php" method="POST" name="form_index">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addused">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Document"),"", "","","","");
print_liste_field_titre($langs->trans("Date"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "",'','','');
print_liste_field_titre($langs->trans("Unit"),"", "",'','','');
print_liste_field_titre($langs->trans("Quantity"),"", "",'','','align="right"');
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print "</tr>\n";
$var = true;
if (empty($used_ref))
  {
    $adatenew = dol_getdate(dol_now());
    $used_ref = $adatenew['year'].str_pad($adatenew['mon'],2,"0",STR_PAD_LEFT);
  }
if (empty($used_datereturn)) $used_datereturn = dol_now();
if (empty($used_unit)) $used_unit = 'Pieza';
if (empty($used_quant)) $used_quant = 1;
//registro nuevo
print "<tr $bc[$var]>";
print '<td>';
print '<input id="used_ref" type="text" value="'.$used_ref.'" name="used_ref" required size="15" maxlength="15">';
print '</td>';
print '<td>';
$form->select_date($used_datereturn,'dm_','','',0,"form_index",1,1);
print '</td>';
print '<td>';
print '<input id="used_description" type="text" name="used_description" value="'.$used_description.'" required size="30" maxlength="200">';
print '</td>';
print '<td>';
print '<input id="used_unit" type="text" name="used_unit" value="'.$used_unit.'" required size="10" maxlength="20">';
print '</td>';
print '<td align="right">';
print '<input id="used_quant" type="number" name="used_quant" step="any" min="0,1" value="'.$used_quant.'" required >';
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Saveorder').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';

print '</tr>';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsmaterialused.class.php';
$objUsed = new Mjobsmaterialused($db);
$objUsed->getlist($object->id);
foreach ((array) $objUsed->array AS $j => $obju)
{
  $var=!$var;
  print "<tr $bc[$var]>";
  print '<td>'.$obju->ref.'</td>';
  print '<td>'.dol_print_date($obju->date_return,'day').'</td>';
  print '<td>'.$obju->description.'</td>';
  print '<td>'.$obju->unit.'</td>';
  print '<td align="right">'.$obju->quant.'</td>';
  if ($object->statut == 3)
    {
      print '<td align="center">';
      print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$obju->id.'&action=delused'.'">'.img_picto($langs->trans('Deleteitemused'),'delete').'</a>';
      print '</td>';
    }
  print '</tr>';
}
print "</table>";

print '</form>';

print '</div>';

?>