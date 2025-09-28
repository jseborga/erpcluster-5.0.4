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
if ($object->fk_soc<=0)
{
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
}
else
{
	//recuperamos los tecnicos externos asignados
	$filterstatic = " AND t.fk_jobs = ".$object->id;
	$res = $objJobscontact->fetchAll('','', 0, 0, array(1=>1),'AND',$filterstatic);
	$idsMember = '';
	if ($res>0)
	{
		$lines = $objJobscontact->lines;
		foreach ($lines AS $j => $line)
		{
			if (!empty($idsMember)) $idsMember.=',';
			$idsMember.= $line->fk_contact;
		}
	}
	if (empty($idsMember)) $idsMember = 0;
	$filterstatic = " AND c.rowid IN (".$idsMember.")";
	$res = $objContact->fetchAll('ASC', 'lastname',0,0,array(1=>1),'AND',$filterstatic);
	$options = '';
	if ($res>0)
	{
		foreach ($objContact->lines AS $j => $line)
		{
			$options.= '<option value="'.$line->id.'">'.$line->lastname.' '.$line->firstname.'</option>';
		}
	}

}
//recuperamos que estan programados
$filterstatic = " AND t.fk_jobs = ".$object->id;
$res = $objectprogram->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic);
$aJobsprogram = array();
$aJobsimage=array();
if ($res > 0)
{
	$lines = $objectprogram->lines;
	foreach ($lines AS $j => $line)
	{
		$aJobsprogram[$line->fk_speciality] = $line->fk_speciality;
		$aJobsimage[$line->id] = $line->image_req;
	}
}

print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () {
	$("#fk_jobs_program").change(function() {
		document.form_job.action.value="advance";
		document.form_job.submit();
	});
});';
print '</script>'."\n";

print '<form name="form_job" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addadvance">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="speciality_job" value="'.$object->speciality_prog.'">';

dol_fiche_head();
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Speciality"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "",'','','colspan="2"');
print_liste_field_titre($langs->trans("Usedtime"),"", "",'','','');
print_liste_field_titre($langs->trans("Technics"),"", "",'','','');
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="center"');
print '</tr>';
print '<tr><td colspan="5">';
print '<select id="fk_jobs_program" name="fk_jobs_program">'.$optionsprog.'</select>';
print '</td>';
print '</tr>';

// typemant
print '<tr><td>';
print select_speciality(GETPOST('fk_speciality'),'fk_speciality','',1,'','rowid',$aJobsprogram);
print '</td>';


// equipment
// print '<td>';
// print $objEquipment->select_equipment((empty($object->fk_equipment)?$object->fk_equipment_prog:$object->fk_equipment),'fk_equipment','',40);
// print '</td>';

//descripcion job
print '<td colspan="2">';
print '<textarea name="description" cols="40" rows="2">'.GETPOST('description').'</textarea>';
print '</td>';
print '<td>';
//print '<input type="number" class="len50" name="used_time" min="0" max="999" value="'.GETPOST('used_time','int').'">';
print $form->select_date(GETPOST('used_time'),'ut_',1,1,0,'',0);
print '</td>';

print '<td rowspan="3">';
print '<select multiple name="members[]">'.$options.'</select>';
print '</td>';
print '<td rowspan="3" align="center">';
print '<input type="image" alt="'.$langs->trans('Savework').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
print '</td>';

print '</tr>';

print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Dateini"),"", "",'','','');
print_liste_field_titre($langs->trans("Photos"),"", "",'','','');
print_liste_field_titre($langs->trans("Datefin"),"", "",'','','colspan="2"');
print '</tr>';

print '<tr>';
// dateini
print '<td nowrap>';
$form->select_date($date_ini,'di_',1,0,1,"fiche_index",1,0);
print '</td>';

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
	print '<tr><td><input type="file" class="flat" name="photofin" id="photofininput" '.($aJobsimage[GETPOST('fk_jobs_program')]==1?'required':'').'></td></tr>';
	print '</table>';
}
print '</td>';

// datefin
print '<td nowrap colspan="2">';
$form->select_date($date_fin,'df_',1,0,1,"fiche_index",1,0);
print '</td>';

print '</tr>';
print "</table>";
dol_fiche_end();
print '</form>';
