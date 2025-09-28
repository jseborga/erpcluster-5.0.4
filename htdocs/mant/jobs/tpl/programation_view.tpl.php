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

print '<table class="border" width="100%">';
//title
print "<tr class=\"liste_titre\">";

print '<td colspan="2" align="center">'.$langs->trans('Programmed').'</td>';
if ($action != 'editregjobs' && $object->statut >= 4)
	print '<td align="center" >'.$langs->trans('Realized').'</td>';
print '</tr>';


// Typemant
print '<tr>';
print '<td width="20%">'.$langs->trans('Typemant').'</td><td>';
print '&nbsp;';
//print select_typemant($object->typemant_prog,'typemant_prog','',0,1);
print '</td>';

if ($action != 'editregjobs' && $object->statut >= 4)
{
	print '<td>';
	print select_typemant($object->typemant,'typemant_job','',0,1);
	print '</td>';
}


print '</tr>';

// Especiality
print '<tr>';
print '<td width="20%">'.$langs->trans('Speciality').'</td><td>';
print select_speciality($object->speciality_prog,'speciality_prog','',0,1);
print '</td>';

if ($action != 'editregjobs' && $object->statut >= 4)
{
	print '<td>';
	print select_speciality($object->speciality_job,'speciality_job','',0,1);
	print '</td>';
}


print '</tr>';

//equipment
print '<tr>';
print '<td >'.$langs->trans('Equipment').'</td><td colspan="2">';
if ($objassets->fetch($object->fk_equipment_prog)>0)
	print $objassets->descrip;
else
	print '';
print '</td>';
print '</tr>';

//descripcion
print '<tr>';
print '<td>'.$langs->trans('Description').'</td><td>';
print $object->description_prog;
print '</td>';
if ($action != 'editregjobs' && $object->statut >= 4)
{
	//    print '<td>'.$langs->trans('Description job').'</td>';
	print '<td>'.$object->description_job.'</td>';
}
print '</tr>';

// dateini
print '<tr><td>'.$langs->trans('Dateini').'</td><td>';
print dol_print_date($object->date_ini_prog,'daytext');
print '</td>';
if ($action != 'editregjobs' && $object->statut >= 4)
{
	//    print '<td>'.$langs->trans('Dateini job').'</td>';
	print '<td>'.dol_print_date($object->date_ini,'daytext').'</td>';
}
print '</tr>';

// datefin
print '<tr><td>'.$langs->trans('Datefin').'</td><td>';
print dol_print_date($object->date_fin_prog,'daytext');
print '</td>';
if ($action != 'editregjobs' && $object->statut >= 4)
{
	//    print '<td>'.$langs->trans('Datefin job').'</td>';
	print '<td>'.dol_print_date($object->date_fin,'daytext').'</td>';
}
print '</tr>';

// imagen ini
print '<tr class="hideonsmartphone">';
print '<td>'.$langs->trans("Photobeforestartingwork").'</td>';
print '<td>';
if ($object->image_ini) print $object->showphoto('ini',$objwork,100);
print '</td>';
if ($action != 'editregjobs' && $object->statut >= 4)
{
	//    print '<td>'.$langs->trans("Phototocompletethework").'</td>';
	print '<td>';
	if ($object->image_fin) print $object->showphoto('fin',$object,100);
	print '</td>';
}
print '</tr>';

print "</table>";

print '</div>';

/* ****************************************** */
/*                                            */
/* Barre d'action                             */
/*                                            */
/* ****************************************** */

print "<div class=\"tabsAction\">\n";

if ($action == 'upjobs')
{
	if ($user->rights->mant->jobs->upjobs && $object->statut == 1)
		print "<a class=\"butAction\" href=\"fiche.php?id=".$object->id."\">".$langs->trans("Return")."</a>";
}

if ($action == '')
{
	// assign work
	if ($object->statut == 3 && $user->rights->mant->jobs->regjobs)
	{
		//buscamos los tecnicos asignados
		//autorizacion solo para el tecnico asignado o el administrador externo
		$lRunjobs = false;

		//	if ($object->typent == 'TE_BCB')
		//tecnicos internos
		if ($object->typent == 'TE_BCB' || $object->fk_soc == -1)
		{
			if ((!empty($user->array_options['options_fk_tercero']) && $object->typent_id == $user->array_options['options_fk_tercero']) || $object->fk_soc == -1)
				$objJobsuser = new Mjobsuser($db);
			$aUserlist = $objWorkuser->list_requestuser($object->fk_work_request);
			foreach((array) $aUserlist AS $j => $objus)
			{
				if ($objus->fk_user == $user->id) $lRunjobs = true;
			}

			// $lRunjobs = true;
		}
		else
		{
			//tecnicos externos
			$objJobscon = new Mjobscontact($db);
			$aContact = $objWorkcont->list_contact($object->fk_work_request);
			foreach((array) $aContact AS $j => $objContact)
			{
				//echo '<hr>'.$objContact->fk_contact .' '.$user->contact_id;
				if ($objContact->fk_contact == $user->contact_id) $lRunjobs = true;
				if ($lRunjobs == false)
				{
					//verificamos si el usuario activo es quien programo
					if ($objwork->fetch($object->fk_work_request))
					{
						if ($objwork->fk_user_prog == $user->id) $lRunjobs = true;
					}
				}
			}
		}
		if ($lRunjobs || $user->admin || $user->rights->mant->jobs->upjobs)
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editregjobs">'.$langs->trans('Runjobs').'</a>';
	}
}

print '</div>';

?>