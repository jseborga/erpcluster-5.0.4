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

$dir     = $conf->poa->multidir_output[$object->entity]."/".$object->id."/images";
print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="updoc">';
print '<input type="hidden" name="id" value="'.$id.'">';

print '<table class="border" width="100%">';
print '<tr>';
print '<td>'.$langs->trans('Documenttype').'</td>';
print '<td>'.'<input type="text" class="form-control" name="detail" maxlength="50" value="" placeholder="'.$langs->trans('Recordthetypeofdocument').'" required>'.'</td>';

print '<td><input type="file" class="filestyle" name="docpdf" id="docpdf">';
print '</td>';
print '<td align="right">';
print '<input type="submit" class="button" alt="'.$langs->trans('Savework').'" name="save" value="'.$langs->trans('Save').'">';
print '</td>';
print '</tr>';
print '</table>';
print '</form>';
//print '</div>';
?>