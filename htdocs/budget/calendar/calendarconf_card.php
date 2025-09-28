<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *   	\file       budget/calendarconf_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-21 18:44
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)

// Load traductions files requiredby by page
dol_include_once('/budget/class/calendarconf.class.php');

$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
//$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_calendar=GETPOST('search_fk_calendar','int');
$search_working_day=GETPOST('search_working_day','alpha');
$search_working_day_hours=GETPOST('search_working_day_hours','alpha');
$search_nonwork_day=GETPOST('search_nonwork_day','alpha');
$search_hours_day=GETPOST('search_hours_day','int');
$search_hours_week=GETPOST('search_hours_week','int');
$search_days_month=GETPOST('search_days_month','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');

$aDays = array("domingo","lunes","martes","mi&eacute;rcoles","jueves","viernes","s&aacute;bado");
$aHour = array();
$aMin = array();
$a = 0;
for ($a =0; $a <=23; $a++) $aHour[$a]=(strlen($a)==1?'0'.$a:$a);
	$a = 0;
for ($a =0; $a <=59; $a++) $aMin[$a]=(strlen($a)==1?'0'.$a:$a);

// Protection if external user
	if ($user->societe_id > 0)
	{
	//accessforbidden();
	}

	if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
	$objectconf=new Calendarconf($db);
	if (($id > 0) && $action != 'add')
	{
		$result=$objectconf->fetch(0,$id);
		if ($result < 0) dol_print_error($db);
		elseif ($result == 0) $action = 'createconf';

	}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
	$hookmanager->initHooks(array('calendarconf'));
	$extrafields = new ExtraFields($db);



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'createconf')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="addconf">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="fk_calendar" value="'.$id.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldworking_day").'</td><td>';
	$htmltitle = '<tr>';
	$htmlworkingday = '<tr>';
	$htmlnoworkingday = '<tr>';
	foreach ($aDays AS $codeday => $value)
	{
		$htmltitle.='<td align="center" width="25px">';
		$htmltitle.=$langs->trans($value);
		$htmltitle.='</td>';
		$htmlworkingday.='<td align="center">';
		$htmlworkingday.='<input type="radio" name="working_day['.$codeday.']" value="1">';
		$htmlworkingday.='</td>';
		$htmlnoworkingday.='<td align="center">';
		$htmlnoworkingday.='<input type="radio" name="working_day['.$codeday.']" value="2">';
		$htmlnoworkingday.='</td>';
	}
	$htmltitle.='</tr>';
	$htmlworkingday.='</tr>';
	$htmlnoworkingday.='</tr>';
	print '<table class="border centpercent">'.$htmltitle.$htmlworkingday.'</table>';
	//print '<input class="flat" type="text" name="working_day" value="'.GETPOST('working_day').'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldworking_day_hours").'</td><td>';
	print '<div class="input-group">';
	for ($j=1; $j <=8; $j++)
	{
		if ($j == 1)
		{
			$defaulthour = 8;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 2)
		{
			$defaulthour = 12;
			$defaultmin = 0;
		}
		if ($j == 3)
		{
			$defaulthour = 14;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 4)
		{
			$defaulthour = 18;
			$defaultmin = 0;
		}
		if ($j == 5)
		{
			$defaulthour = 0;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 6)
		{
			$defaulthour = 0;
			$defaultmin = 0;
		}
		if ($j == 7)
		{
			$defaulthour = 0;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 8)
		{
			$defaulthour = 0;
			$defaultmin = 0;
		}
		print '<div class="col-md-5">';
		print $form->selectarray('hour_'.$j,$aHour,(GETPOST('hour_'.$j)?GETPOST('hour_'.$j):$defaulthour));
		print ':';
		print $form->selectarray('min_'.$j,$aMin,(GETPOST('min_'.$j)?GETPOST('min_'.$j):$defaultmin));
		print '</div>';
		if ($j == 1 ||$j == 3 ||$j == 5 ||$j == 7)
			print '<div class="col-md-1">'.$langs->trans('To').'</div>';
		if ($j == 2 ||$j == 4 ||$j == 6 ||$j == 8)
			print '</div>';
	}
	print '</div>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnonwork_day").'</td><td>';
	print '<table class="border centpercent">'.$htmltitle.$htmlnoworkingday.'</table>';
	print '</td></tr>';
	//print '<input class="flat" type="text" name="nonwork_day" value="'.GETPOST('nonwork_day').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_day").'</td><td><input class="flat" type="text" name="hours_day" value="'.GETPOST('hours_day').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_week").'</td><td><input class="flat" type="text" name="hours_week" value="'.GETPOST('hours_week').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddays_month").'</td><td><input class="flat" type="text" name="days_month" value="'.GETPOST('days_month').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';


}



// Part to edit record
if ($result && $id && $action == 'editconf')
{
	print load_fiche_titre($langs->trans("Parameters"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="updateconf">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="idr" value="'.$objectconf->id.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldworking_day").'</td><td>';
	$htmltitle = '<tr>';
	$htmlworkingday = '<tr>';
	$htmlnoworkingday = '<tr>';

	$aWorking_day 		= explode('|',$objectconf->working_day);
	$aNonwork_day 		= explode('|',$objectconf->nonwork_day);
	$aWorkingdayhour 	= explode('|',$objectconf->working_day_hours);

	foreach ((array) $aWorking_day AS $j => $value) $aWorkingday[$value] = $value;
	foreach ((array) $aNonwork_day AS $j => $value) $aNonworkday[$value] = $value;
	foreach ((array) $aWorkingdayhour AS $j => $value)
	{
		$aWorkingday_hour 	= explode(';',$value);
		$aWorkingdayhours[$aWorkingday_hour[0]] = $aWorkingday_hour[1];
	}
	foreach ($aDays AS $codeday => $value)
	{
		$htmltitle.='<td align="center" width="25px">';
		$htmltitle.=$langs->trans($value);
		$htmltitle.='</td>';
		$htmlworkingday.='<td align="center">';
		$checked = '';
		if (isset($aWorkingday[$codeday])) $checked = 'checked';
		$htmlworkingday.='<input type="radio" name="working_day['.$codeday.']" '.$checked.' value="1">';
		$htmlworkingday.='</td>';
		$htmlnoworkingday.='<td align="center">';
		$checked = '';
		if (isset($aNonworkday[$codeday])) $checked = 'checked';
		$htmlnoworkingday.='<input type="radio" name="working_day['.$codeday.']" '.$checked.' value="2">';
		$htmlnoworkingday.='</td>';
	}
	$htmltitle.='</tr>';
	$htmlworkingday.='</tr>';
	$htmlnoworkingday.='</tr>';
	print '<table class="border centpercent">'.$htmltitle.$htmlworkingday.'</table>';
	//print '<input class="flat" type="text" name="working_day" value="'.GETPOST('working_day').'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldworking_day_hours").'</td><td>';
	print '<div class="input-group">';
	for ($j=1; $j <=8; $j++)
	{
		if ($j == 1)
		{
			$defaulthour = 8;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 2)
		{
			$defaulthour = 12;
			$defaultmin = 0;
		}
		if ($j == 3)
		{
			$defaulthour = 14;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 4)
		{
			$defaulthour = 18;
			$defaultmin = 0;
		}
		if ($j == 5)
		{
			$defaulthour = 0;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 6)
		{
			$defaulthour = 0;
			$defaultmin = 0;
		}
		if ($j == 7)
		{
			$defaulthour = 0;
			$defaultmin = 0;
			print '<div class="col-md-5">';
		}
		if ($j == 8)
		{
			$defaulthour = 0;
			$defaultmin = 0;
		}
			if ($aWorkingdayhours[$j])
			{
				$data = explode(':',$aWorkingdayhours[$j]);
				$defaulthour = $data[0];
				$defaultmin = $data[1];
			}
		print '<div class="col-md-5">';
		print $form->selectarray('hour_'.$j,$aHour,(GETPOST('hour_'.$j)?GETPOST('hour_'.$j):$defaulthour));
		print ':';
		print $form->selectarray('min_'.$j,$aMin,(GETPOST('min_'.$j)?GETPOST('min_'.$j):$defaultmin));
		print '</div>';
		if ($j == 1 ||$j == 3 ||$j == 5 ||$j == 7)
			print '<div class="col-md-1">'.$langs->trans('To').'</div>';
		if ($j == 2 ||$j == 4 ||$j == 6 ||$j == 8)
			print '</div>';
	}
	print '</div>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnonwork_day").'</td><td>';
	print '<table class="border centpercent">'.$htmltitle.$htmlnoworkingday.'</table>';
	print '</td></tr>';
	//print '<input class="flat" type="text" name="nonwork_day" value="'.GETPOST('nonwork_day').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_day").'</td><td><input class="flat" type="text" name="hours_day" value="'.(GETPOST('hours_day')?GETPOST('hours_day'):$objectconf->hours_day).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_week").'</td><td><input class="flat" type="text" name="hours_week" value="'.(GETPOST('hours_week')?GETPOST('hours_week'):$objectconf->hours_week).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddays_month").'</td><td><input class="flat" type="text" name="days_month" value="'.(GETPOST('days_month')?GETPOST('days_month'):$objectconf->days_month).'"></td></tr>';

	print '</table>'."\n";


	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($result && $id && (empty($action) || $action == 'view' || $action == 'deleteconf'))
{
	print load_fiche_titre($langs->trans("Parameters"));

	dol_fiche_head();

	if ($action == 'deleteconf') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objectconf->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_deleteconf', '', 0, 1);
		print $formconfirm;
	}
	$aWorking_day 		= explode('|',$objectconf->working_day);
	$aNonwork_day 		= explode('|',$objectconf->nonwork_day);
	$aWorkingdayhour 	= explode('|',$objectconf->working_day_hours);

	foreach ((array) $aWorking_day AS $j => $value) $aWorkingday[$value] = $value;
	foreach ((array) $aNonwork_day AS $j => $value) $aNonworkday[$value] = $value;
	foreach ((array) $aWorkingdayhour AS $j => $value)
	{
		$aWorkingday_hour 	= explode(';',$value);
		$aWorkingdayhours[$aWorkingday_hour[0]] = $aWorkingday_hour[1];
	}
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldworking_day").'</td><td>';
	$htmltitle = '<tr>';
	$htmlworkingday = '<tr>';
	$htmlnoworkingday = '<tr>';
	foreach ($aDays AS $codeday => $value)
	{
		$htmltitle.='<td align="center" width="25px">';
		$htmltitle.=$langs->trans($value);
		$htmltitle.='</td>';
		$htmlworkingday.='<td align="center">';
		$checked = '';
		if (isset($aWorkingday[$codeday])) $checked = 'checked';
		$htmlworkingday.='<input type="radio" name="working_day['.$codeday.']" value="1" '.$checked.' disabled>';
		$htmlworkingday.='</td>';
		$htmlnoworkingday.='<td align="center">';
		$checked = '';
		if (isset($aNonworkday[$codeday])) $checked = 'checked';
		$htmlnoworkingday.='<input type="radio" name="working_day['.$codeday.']" value="2" '.$checked.' disabled>';
		$htmlnoworkingday.='</td>';
	}
	$htmltitle.='</tr>';
	$htmlworkingday.='</tr>';
	$htmlnoworkingday.='</tr>';
	print '<table class="border centpercent">'.$htmltitle.$htmlworkingday.'</table>';
	//print '<input class="flat" type="text" name="working_day" value="'.GETPOST('working_day').'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldworking_day_hours").'</td><td>';
	print '<div class="input-group">';
	foreach ($aWorkingdayhours AS $j => $value)
	{
		$data = explode(':',$value);
		if (empty($data[0]) && empty($data[1])) continue;
		else
		{
			print '<div class="col-md-3">';
			print (strlen($data[0])==1?'0'.$data[0]:$data[0]);
			print ':';
			print (strlen($data[1])==1?'0'.$data[1]:$data[1]);
			print '</div>';
			if ($j == 1 ||$j == 3 ||$j == 5 ||$j == 7)
				print '<div class="col-md-1">'.$langs->trans('To').'</div>';
		}
	}
	print '</div>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnonwork_day").'</td><td>';
	print '<table class="border centpercent">'.$htmltitle.$htmlnoworkingday.'</table>';
	print '</td></tr>';
	//print '<input class="flat" type="text" name="nonwork_day" value="'.GETPOST('nonwork_day').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_day").'</td><td>'.$objectconf->hours_day.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_week").'</td><td>'.$objectconf->hours_week.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddays_month").'</td><td>'.$objectconf->days_month.'</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objectconf,$action);    // Note that $action and $objectconf may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->par->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objectconf->id.'&amp;action=editconf">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->par->del)
		{
			//print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objectconf->id.'&amp;action=deleteconf">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($objectconf);
	//$linktoelem = $form->showLinkToObjectBlock($objectconf);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


