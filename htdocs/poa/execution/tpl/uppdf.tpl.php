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
print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="'.$actionvalue.'">';
print '<input type="hidden" name="idreg" value="'.$idreg.'">';
print '<input type="hidden" name="linklast" value="'.$linklast.'">';

print '<table class="border" width="100%">';
print '<tr><td><input type="file" class="flat" name="docpdf" id="docpdf">';
//print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Savework').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';
print '</tr>';
print '</table>';

print '</form>';
?>