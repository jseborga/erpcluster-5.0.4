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
 *	\file       htdocs/mant/jobs/tpl/work_performed.tpl.php
 *	\ingroup    Ordenes de Trabajo
*	\brief      Page fiche mantenimiento edit trabajos ejecutados
 */

// dol_fiche_head($head, 'card', $langs->trans("Workcarriedout"), 0, DOL_URL_ROOT.'/mant/img/ejecucion',1);
   
print '<form  action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addunit">';
print '<input type="hidden" name="id" value="'.$id.'">';
$aUnits = $objunit->liste_array();
print $form->selectarray('fk_unit',$aUnits,'',1);
print '<input type="submit" value="'.$langs->trans('Save').'">';
print '</td>';
print '</tr>';
print '</table>';

print '</form>';
//print '</div>';
?>