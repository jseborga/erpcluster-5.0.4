<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/monprojet/tpl/sendnote.tpl.php
 *	\ingroup    type note for email for task
*	\brief      Page fiche mantenimiento add sendnote
 */

  // Confirm delete request

if ($action == 'delete')
  {
    $form = new Form($db);
    $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.$idr,$langs->trans("Delete"),$langs->trans("Confirmdeleteattachment",$object->ref),"confirm_delete",'',0,2);
    if ($ret == 'html') print '<br>';
  }

print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setnote_public">';
print '<input type="hidden" name="subaction" value="send_public">';
print '<input type="hidden" name="id" value="'.$id.'">';

print '<table class="noborder" width="100%">';
print '<tr>';
print '<td colspan="3">'.$langs->trans('La nota a enviarse, reemplazara la nota (publica)').'</td>';
print '</tr>';
print '<tr>';
print '<td width="10%">'.$langs->trans('Subject').'</td>';
print '<td colspan="2">';
print '<input type="text" name="subject" value="'.$subject.'" size="30">';
print '</td>';
print '</tr>';

print '<tr>';
print '<td width="10%">'.$langs->trans('Notes').'</td>';
print '<td>';
print '<textarea name="note_public" rows="3" cols="60"></textarea>';
print '</td><td>';
print '<input type="submit" class="button" alt="'.$langs->trans('Send').'" name="save" value="'.$langs->trans('Send').'">';
print '</td>';
print '</tr>';
print '</table>';
print '</form>';
?>