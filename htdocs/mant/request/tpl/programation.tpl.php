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


dol_fiche_head($head, 'card', $langs->trans("Programming of work"), 0, 'mant');

print '<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="upjobs">';
print '<input type="hidden" name="actionant" value="program">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Speciality"),"", "","","","");
print_liste_field_titre($langs->trans("Equipment"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "","","",'colspan="2"');
print '</tr>';


// Especiality
print '<tr><td>';
print select_speciality((empty($object->speciality_prog)?$object->speciality:$object->speciality_prog),'speciality_prog','',1);
print '</td>';


// equipment
print '<td nowrap>';
if ($conf->assets->enabled)
  {
    $var=!$var;
    print $objassets->select_assets((!empty($object->fk_equipment_prog)?$object->fk_equipment_prog:$fk_equipment),'fk_equipment_prog','',8,1,0,'');
    //buscador
    print '<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php?idot='.$id.'&amp;ssearch=1">'.img_picto($langs->trans('Search'),'search').'</a>';
  }
 else
   print '';
print '</td>';

//descripcion
print '<td colspan="2">';
print '<textarea name="description_prog" cols="30" rows="3">'.$object->description_prog.'</textarea>';
print '</td>';
print '</tr>';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Photobeforestartingwork"),"", "","","","");
print_liste_field_titre($langs->trans("Dateini"),"", "","","","");
print_liste_field_titre($langs->trans("Datefin"),"", "","","","");

print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print "</tr>\n";

// imagen ini
print '<td>';
if ($object->image_ini) print $object->showphoto('ini',$object,50);
$caneditfield=1;
if ($caneditfield)
  {
    if ($object->image_ini) print "<br>\n";
    print '<table class="nobordernopadding">';
    if ($object->image_ini) print '<tr><td><input type="checkbox" class="flat" name="deletephotoini" id="photodeleteini"> '.$langs->trans("Delete").'<br><br></td></tr>';
    //print '<tr><td>'.$langs->trans("PhotoFile").'</td></tr>';
    print '<tr><td><input type="file" class="flat" name="photoini" id="photoiniinput"></td></tr>';
    print '</table>';
  }

print '</td>';

// dateini
print '<td>';
$form->select_date($object->date_ini_prog,'di_','','','',"dateiniprog",1,0);
print '</td>';

// datefin
print '<td>';
$form->select_date($object->date_fin_prog,'fi_','','','',"datefinprog",1,0);
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';
print '</tr>';

print "</table>";

//	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveworktobeperformed").'"></center>';

print '</form>';
print '</div>';
?>