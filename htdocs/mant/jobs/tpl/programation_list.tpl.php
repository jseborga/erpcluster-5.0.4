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
//if ($action != 'editregjobs' && $object->statut >= 4)
//	dol_fiche_head($head, 'card', $langs->trans("Work performed"), 0, 'mant');
//else
//	dol_fiche_head($head, 'card', $langs->trans("Programming of work"), 0, 'mant');

dol_fiche_head();
print '<table class="border" width="100%">';
//title
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Nro"),$_SERVER['PHP_SELF'], "t.ref","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Speciality"),$_SERVER['PHP_SELF'], "t.fk_speciality","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Description"),$_SERVER['PHP_SELF'], "t.description","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Image"),$_SERVER['PHP_SELF'], "t.image_req","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Dateini"),$_SERVER['PHP_SELF'], "t.date_ini","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Datefin"),$_SERVER['PHP_SELF'], "t.date_fin","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Action"),$_SERVER['PHP_SELF'], "","","","",$sortfield,$sortorder);
print '</tr>';

$filterstatic = " AND t.fk_jobs = ".$object->id;
$res = $objectprogram->fetchAll('ASC','ref', 0,0,array(1=>1),'AND',$filterstatic,false);
$lines = $objectprogram->lines;
if ($res > 0)
{

	foreach ($lines AS $j => $line)
	{
		$var = !$var;
		print "<tr $bc[$var]>";
		print '<td>'.$line->ref.'</td>';
		print '<td>';
		if ($line->fk_speciality) print select_speciality($line->fk_speciality,'fk_speciality','',0,1,'rowid');
		else print '';
		print '</td>';
		print '<td>'.$line->description.'</td>';
		print '<td>'.($line->image_req?$langs->trans('Yes'):$langs->trans('No')).'</td>';
		print '<td>'.dol_print_date($line->date_ini,'day').'</td>';
		print '<td>'.dol_print_date($line->date_fin,'day').'</td>';
		print '<td>'.'</td>';
		print '</tr>';
	}
}
print '</table>';

dol_fiche_end();

/* **************************** */
/*                                            */
/* Barre d'action                       */
/*                                            */
/* **************************** */

print "<div class=\"tabsAction\">\n";

if ($action == '')
{
	//programacion de trabajos
	if ($object->status == 3 && $user->rights->mant->tick->prog && $res && count($lines)>0)
	{
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=valprogram&amp;id='.$object->id.'">'.$langs->trans('Validateprogramming').'</a>';
	}
}

print '</div>';

?>