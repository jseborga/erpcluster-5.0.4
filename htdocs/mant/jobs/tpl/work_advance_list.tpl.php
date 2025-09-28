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
//recuperamos la lista de tecnicos asignados
$filterstatic = " AND t.fk_jobs = ".$object->id;
$res = $objJobuser->fetchAll('','', 0, 0, array(1=>1),'AND',$filterstatic);
$idsMember = '';
if ($res>0)
{
	$lines = $objJobuser->lines;
	foreach ($lines AS $j => $line)
	{
		if (!empty($idsMember)) $idsMember.=',';
		$idsMember.= $line->fk_user;
	}
}
if (empty($idsMember)) $idsMember = 0;
$filterstatic = " AND d.rowid IN (".$idsMember.")";
$res = $objAdherent->fetchAll('ASC', 'lastname',0,0,array(1=>1),'AND',$filterstatic);
$options = '';
if ($res>0)
{
	foreach ($objAdherent->lines AS $j => $line)
	{
		$options.= '<option value="'.$line->id.'">'.$line->lastname.' '.$line->firstname.'</option>';
	}
}
//recuperamos la lista de trabajos realizados
$filterstatic = " AND t.fk_jobs = ".$object->id;
$res = $objectadvance->fetchAll('ASC', $sortfield='ref',0,0,array(1=>1),'AND',$filterstatic,false);

dol_fiche_head();
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Speciality"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "",'','','');
print_liste_field_titre($langs->trans("Usedtime"),"", "",'','','');
print_liste_field_titre($langs->trans("Technics"),"", "",'','','');
print_liste_field_titre($langs->trans("Dateini"),"", "",'','','');
print_liste_field_titre($langs->trans("Datefin"),"", "",'','','');
print_liste_field_titre($langs->trans("Photos"),"", "",'','','');
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="center"');
print '</tr>';
$var = true;
if ($res > 0)
{
	$lines = $objectadvance->lines;
	foreach ($lines AS $j => $line)
	{

		$var = !$var;
		$object->image_adv = $line->image_adv;

		if ($action == 'editadvance' && $idr == $line->id)
		{
			$aMembers = explode(',',$line->members);
			if (count($aMembers)>0)
			{
				foreach ($aMembers AS $k => $value)
					$aSelected[$value] = $value;
			}
			$aTime = explode(':',$line->used_time);
			$date_ini = $line->date_ini;
			$date_fin = $line->date_fin;
			$date_used_time = dol_mktime($aTime[0],$aTime[1],$aTime[2],date('m'),date('d'),date('Y'));
			include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/work_edit_advance.tpl.php';
		}
		else
		{
		// typemant
			print "<tr $bc[$var]>";
			print '<td>';
			print select_speciality($line->fk_speciality,'fk_speciality','',0,1,'rowid');
			print '</td>';

		//descripcion job
			print '<td>';
			print $line->description;
			print '</td>';
			print '<td>';
			print $line->used_time.' ';
			print '</td>';

			print '<td>';
			$cMember = '';
			$aMember = explode(',',$line->members);
			if (!empty($line->members) && count($aMember)>0)
			{
				foreach ($aMember AS $i => $fk_member)
				{
					if (!empty($cMember)) $cMember.= ',<br>';
					$objAdherent->fetch($fk_member);
					$cMember.= $objAdherent->lastname.' '.$objAdherent->firstname;
				}
			}
			print $cMember;
			print '</td>';
			print '<td>';
			print dol_print_date($line->date_ini,'dayhour');
			print '</td>';
			print '<td>';
			print dol_print_date($line->date_fin,'dayhour');
			print '</td>';
			print '<td>';
			print $object->showphoto('image_adv',$object,$width=100);
		//print $line->image_adv.' '.img_picto('',$line->image_adv,'',1);
			print '</td>';
			print '<td>';
			if (empty($action))
			{
				if ($user->rights->mant->jobs->modjobs && $object->status == 4)
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=editadvance">'.img_picto('','edit').'</a>';
				print '&nbsp;';
				if ($user->rights->mant->jobs->deljobs && $object->status == 4)
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=deljobs">'.img_picto('','delete').'</a>';
		//print img_picto('','edit');
			}
			print '</td>';
			print '</tr>';
		}
	}
}

print "</table>";
dol_fiche_end();
