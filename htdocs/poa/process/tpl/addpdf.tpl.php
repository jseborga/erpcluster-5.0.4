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
 *	\file       htdocs/poa/process/tpl/addpdf.tpl.php
 *	\ingroup    Add pdf contrat
*	\brief      Page fiche contrat preventive
 */

// dol_fiche_head($head, 'card', $langs->trans("Workcarriedout"), 0, DOL_URL_ROOT.'/mant/img/ejecucion',1);

//print '<form  enctype="multipart/form-data" action="'.DOL_URL_ROOT.'/poa/process/fiche.php'.'" method="POST">';
print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'" method="POST">';

print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="uppdf">';
print '<input type="hidden" name="idreg" value="'.$idreg.'">';
print '<input type="hidden" name="ida" value="'.$ida.'">';
print '<input type="hidden" name="linklast" value="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'">';
print '<input type="hidden" name="modal" value="ficheprocess">';

print '<div class="col-xs-6">';
print '<tr><td><input type="file" class="form-control" name="docpdf" id="docpdf">';
print '</div>';
print '<div class="col-xs-3">';

print '<input type="image" alt="'.$langs->trans('Savework').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</div>';

print '</form>';
//print '</div>';
?>