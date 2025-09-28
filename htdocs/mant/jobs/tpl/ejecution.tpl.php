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
 *	\file       htdocs/mant/jobs/tpl/programation.tpl.php
 *	\ingroup    Ordenes de Trabajo
*	\brief      Page fiche mantenimiento programacion de trabajos
 */


//dol_fiche_head($head, 'card', $langs->trans("Programming of work"), 0, 'mant');

print '<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addejecjobs">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

dol_fiche_head();

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Speciality"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "","","",'colspan="2"');
print_liste_field_titre($langs->trans("Image"),"", "","","","");
print_liste_field_titre($langs->trans("Dateini"),"", "","","","");
print_liste_field_titre($langs->trans("Datefin"),"", "","","","");
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print '</tr>';


		  // Especiality
print '<tr><td>';
print select_speciality(GETPOST('fk_speciality'),'fk_speciality','',1,'','rowid');
print '</td>';
		  //descripcion
print '<td colspan="2">';
print '<textarea name="description" cols="30" rows="3">'.GETPOST('description').'</textarea>';
print '</td>';
print '<td>';
print $form->selectyesno('image_req',GETPOST('image_req'),1);
print '</td>';
// dateini
print '<td>';
$form->select_date($date_ini_prog,'di_','','','',"dateiniprog",1,0);
print '</td>';

// datefin
print '<td>';
$form->select_date($date_fin_prog,'fi_','','','',"datefinprog",1,0);
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';
print '</tr>';	      

print "</table>";

		  //	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveworktobeperformed").'"></center>';

print '</form>';
dol_fiche_end();

?>