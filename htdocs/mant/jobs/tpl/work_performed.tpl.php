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

dol_fiche_head($head, 'card', $langs->trans("Workcarriedout"), 0, DOL_URL_ROOT.'/mant/img/ejecucion',1);

print '<form  enctype="multipart/form-data" action="fiche.php" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="upwork">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="speciality_job" value="'.$object->speciality_prog.'">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Typeofwork"),"", "","","","");
print_liste_field_titre($langs->trans("Equipment"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "",'','','colspan="3"');
print '</tr>';

// typemant
print '<tr><td>';
print select_typemant(isset($_POST['typemant'])?$_POST['typemant']:(empty($object->typemant)?$object->typemant_prog:$object->typemant),'typemant','',1);
print '</td>';

// equipment
// print '<td>';
// print $objEquipment->select_equipment((empty($object->fk_equipment)?$object->fk_equipment_prog:$object->fk_equipment),'fk_equipment','',40);
// print '</td>';

// equipment
print '<td nowrap>';
if ($conf->assets->enabled)
  {
    $var=!$var;
    print $objassets->select_assets(isset($_POST['fk_equipment'])?$_POST['fk_equipment']:(!empty($object->fk_equipment)?$object->fk_equipment:(empty($fk_equipment)?$object->fk_equipment_prog:$fk_equipment)),'fk_equipment','',8,1,0,'');
    //buscador
    print '<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php?idot='.$id.'&amp;ssearch=2">'.img_picto($langs->trans('Search'),'search').'</a>';
  }
 else
   print '';
print '</td>';

//descripcion job
print '<td colspan="3">';
print '<textarea name="description_job" cols="65" rows="2">'.(isset($_POST['description_job'])?$_POST['description_job']:$object->description_job).'</textarea>';
print '</td>';
print '</tr>';

print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Dateini"),"", "",'','','');
print_liste_field_titre($langs->trans("Photos"),"", "",'','','');
print_liste_field_titre($langs->trans("Datefin"),"", "",'','','');
print_liste_field_titre($langs->trans("Tasksperformed"),"", "",'','','align="center"');
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print '</tr>';

print '<tr>';
// dateini
print '<td nowrap>';
$form->select_date((empty($object->date_ini)?$object->date_ini_prog:$object->date_ini),'di_',1,0,1,"fiche_index",1,0);
print '</td>';

// imagen ini
print '<td>';

// imagen fin
if ($object->image_fin) print $object->showphoto('fin',$object,50);
$caneditfield=1;
if ($caneditfield)
  {
    if ($object->image_fin) print "<br>\n";
    print '<table class="nobordernopadding">';
    if ($object->image_fin) print '<tr><td><input type="checkbox" class="flat" name="deletephotofin" id="photodeletefin"> '.$langs->trans("Delete").'<br><br></td></tr>';
    //print '<tr><td>'.$langs->trans("PhotoFile").'</td></tr>';
    print '<tr><td><input type="file" class="flat" name="photofin" id="photofininput"></td></tr>';
    print '</table>';
  }
print '</td>';

// datefin
print '<td nowrap>';
$form->select_date((empty($object->date_fin)?$object->date_fin_prog:$object->date_fin),'df_',1,0,1,"fiche_index",1,0);
print '</td>';

// task
print '<td align="center">';
if ($object->group_task == 1)
  print '<input type="number" class="len50" min="1" id="task" name="task" value="'.(empty($object->task)?1:$object->task).'">';
 else
   {
     print 1;
     print '<input type="hidden" name="task" value="1">';
   }
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Savework').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';
print '</tr>';
print "</table>";

print '</form>';
print '</div>';
