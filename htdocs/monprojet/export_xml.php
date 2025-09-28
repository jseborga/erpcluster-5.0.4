<?php

/* Copyright (C) 2014-2016 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *  \file       htdocs/poa/process/fiche_exce.php
 *  \ingroup    Process export excel
 *  \brief      Page fiche poa process export excel
 */

require("../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

//item
//require_once DOL_DOCUMENT_ROOT.'/budget/items/class/items.class.php';
//require_once DOL_DOCUMENT_ROOT.'/budget/typeitem/class/typeitem.class.php';

//excel
//require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
//include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';


$langs->load("monprojet@monprojet");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$action    = GETPOST('action');


$mesg = '';

$object  = new Projectext($db);
$task    = new Task($db);
$taskadd = new Taskext($db);
$objecttaskadd = new Projettaskadd($db);
$taskstatic = new Task($db);
//$items = new Items($db);
//$typeitem = new Typeitem($db);
$objUser = new User($db);

//array de datos para exportar a xml
$aProject = array(
	'SaveVersion'=>14,
	'Name'=>'',
	'CreationDate'=>date('Y-m-d').'T'.date('H:m:s'),
	'LastSaved'=>date('Y-m-d').'T'.date('H:m:s'),
	'ScheduleFromStart'=>1,
	'StartDate'=>'',
	'FinishDate'=>'',
	'FYStartDate'=>1,
	'CriticalSlackLimit'=>0,
	'CurrencyDigits'=>2,
	'CurrencySymbol'=>'Bs',
	'CurrencyCode'=>'BOB',
	'CurrencySymbolPosition'=>0,
	'CalendarUID'=>1,
	'DefaultStartTime'=>'08:00:00',
	'DefaultFinishTime'=>'18:00:00',
	'MinutesPerDay'=>480,
	'MinutesPerWeek'=>2400,
	'DaysPerMonth'=>20,
	'DefaultTaskType'=>0,
	'DefaultFixedCostAccrual'=>2,
	'DefaultStandardRate'=>10,
	'DefaultOvertimeRate'=>15,
	'DurationFormat'=>7,
	'WorkFormat'=>3,
	'EditableActualCosts'=>0,
	'HonorConstraints'=>0,
	'EarnedValueMethod'=>0,
	'InsertedProjectsLikeSummary'=>0,
	'MultipleCriticalPaths'=>0,
	'NewTasksEffortDriven'=>0,
	'NewTasksEstimated'=>1,
	'SplitsInProgressTasks'=>0,
	'SpreadActualCost'=>0,
	'SpreadPercentComplete'=>0,
	'TaskUpdatesResource'=>1,
	'FiscalYearStart'=>0,
	'WeekStartDay'=>1,
	'MoveCompletedEndsBack'=>0,
	'MoveRemainingStartsBack'=>0,
	'MoveRemainingStartsForward'=>0,
	'MoveCompletedEndsForward'=>0,
	'BaselineForEarnedValue'=>0,
	'AutoAddNewResourcesAndTasks'=>1,
	'CurrentDate'=>date('Y-m-d').'T'.date('H:m:s'),
	'MicrosoftProjectServerURL'=>1,
	'Autolink'=>1,
	'NewTaskStartDate'=>0,
	'NewTasksAreManual'=>1,
	'DefaultTaskEVMethod'=>0,
	'ProjectExternallyEdited'=>0,
	'ExtendedCreationDate'=>'',
	'ActualsInSync'=>0,
	'RemoveFileProperties'=>0,
	'AdminProject'=>0
	);
$aProjectAdd = array('<OutlineCodes/>',
	'<WBSMasks/>',
	'<ExtendedAttributes/>'
	);
$aDayLaboral = array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1);
//calendars
$aWeekDay = array(
	'DayType' => 1,
	'DayWorking'=>0,
	);
$aWorkingTime = array(
	'FromTime' => '08:00:00',
	'ToTime'=>'12:00:00',
	);
$aWorkingTime2 = array(
	'FromTime' => '14:00:00',
	'ToTime'=>'18:00:00',
	);

$cWeekDay = '
<WeekDay>
	<DayType>1</DayType>
	<DayWorking>0</DayWorking>
</WeekDay>
<WeekDay>
	<DayType>2</DayType>
	<DayWorking>1</DayWorking>
	<WorkingTimes>
		<WorkingTime>
			<FromTime>09:00:00</FromTime>
			<ToTime>13:00:00</ToTime>
		</WorkingTime>
		<WorkingTime>
			<FromTime>15:00:00</FromTime>
			<ToTime>19:00:00</ToTime>
		</WorkingTime>
	</WorkingTimes>
</WeekDay>
<WeekDay>
	<DayType>3</DayType>
	<DayWorking>1</DayWorking>
	<WorkingTimes>
		<WorkingTime>
			<FromTime>09:00:00</FromTime>
			<ToTime>13:00:00</ToTime>
		</WorkingTime>
		<WorkingTime>
			<FromTime>15:00:00</FromTime>
			<ToTime>19:00:00</ToTime>
		</WorkingTime>
	</WorkingTimes>
</WeekDay>
<WeekDay>
	<DayType>4</DayType>
	<DayWorking>1</DayWorking>
	<WorkingTimes>
		<WorkingTime>
			<FromTime>09:00:00</FromTime>
			<ToTime>13:00:00</ToTime>
		</WorkingTime>
		<WorkingTime>
			<FromTime>15:00:00</FromTime>
			<ToTime>19:00:00</ToTime>
		</WorkingTime>
	</WorkingTimes>
</WeekDay>
<WeekDay>
	<DayType>5</DayType>
	<DayWorking>1</DayWorking>
	<WorkingTimes>
		<WorkingTime>
			<FromTime>09:00:00</FromTime>
			<ToTime>13:00:00</ToTime>
		</WorkingTime>
		<WorkingTime>
			<FromTime>15:00:00</FromTime>
			<ToTime>19:00:00</ToTime>
		</WorkingTime>
	</WorkingTimes>
</WeekDay>
<WeekDay>
	<DayType>6</DayType>
	<DayWorking>1</DayWorking>
	<WorkingTimes>
		<WorkingTime>
			<FromTime>09:00:00</FromTime>
			<ToTime>13:00:00</ToTime>
		</WorkingTime>
		<WorkingTime>
			<FromTime>15:00:00</FromTime>
			<ToTime>19:00:00</ToTime>
		</WorkingTime>
	</WorkingTimes>
</WeekDay>
<WeekDay>
	<DayType>7</DayType>
	<DayWorking>0</DayWorking>
</WeekDay>';
$aCalendar = array(
	'UID'=>1,
	'Name'=>'Estándar',
	'IsBaseCalendar'=>1,
	'IsBaselineCalendar'=>0,
	'BaseCalendarUID'=>0,
	'WeekDays'=>'aWeekDay',
	'WorkWeeks' => 'aWorkWeeks',
	);

/*
<Calendars>
	<Calendar>
		<WorkWeeks>
		</WorkWeeks>
	</Calendar>
</Calendars>
*/

$aTask = array (
	'UID'=>0,
	'ID'=>0,
	'Name'=>'',
	'Type'=>0,
	'IsNull'=>0,
	'CreateDate'=>date('Y-m-d').'T'.date('H:M:S'),
	'WBS'=>'',
	'OutlineNumber'=>0,
	'OutlineLevel'=>0,
	'Priority'=>500,
	'Start'=>'',
	'Finish'=>'',
	'Duration'=>'',
	'ManualStart'=>'',
	'ManualFinish'=>'',
	'ManualDuration'=>'',
	'DurationFormat'=>7,
	'ResumeValid'=>0,
	'EffortDriven'=>0,
	'Recurring'=>0,
	'OverAllocated'=>0,
	'Estimated'=>0,
	'Milestone'=>0,
	'Summary'=>0,
	'DisplayAsSummary'=>0,
	'Critical'=>1,
	'IsSubproject'=>0,
	'IsSubprojectReadOnly'=>0,
	'ExternalTask'=>0,
	'EarlyStart'=>'',
	'EarlyFinish'=>'',
	'LateStart'=>'',
	'LateFinish'=>'',
	'StartVariance'=>0,
	'FinishVariance'=>0,
	'WorkVariance'=>0,
	'FreeSlack'=>0,
	'TotalSlack'=>0,
	'StartSlack'=>0,
	'FinishSlack'=>0,
	'FixedCost'=>0,
	'FixedCostAccrual'=>3,
	'PercentComplete'=>0,
	'PercentWorkComplete'=>0,
	'Cost'=>0,
	'OvertimeCost'=>0,
	'OvertimeWork'=>'PT0H0M0S',
	'ActualStart'=>'',
	'ActualDuration'=>'PT0H0M0S',
	'ActualCost'=>0,
	'ActualOvertimeCost'=>0,
	'ActualWork'=>'PT0H0M0S',
	'ActualOvertimeWork'=>'PT0H0M0S',
	'RegularWork'=>'PT0H0M0S',
	'RemainingDuration'=>'PT0H0M0S',
	'RemainingCost'=>0,
	'RemainingWork'=>'PT0H0M0S',
	'RemainingOvertimeCost'=>0,
	'RemainingOvertimeWork'=>'PT0H0M0S',
	'ACWP'=>0,
	'CV'=>0,
	'ConstraintType'=>0,
	'CalendarUID'=>-1,
	'LevelAssignments'=>1,
	'LevelingCanSplit'=>1,
	'LevelingDelay'=>0,
	'LevelingDelayFormat'=>8,
	'IgnoreResourceCalendar'=>0,
	'HideBar'=>0,
	'Rollup'=>1,
	'BCWS'=>0,
	'BCWP'=>0,
	'PhysicalPercentComplete'=>0,
	'EarnedValueMethod'=>0,
	'IsPublished'=>0,
	'CommitmentType'=>0,
	'Active'=>1,
	'Manual'=>0
	);
$aPred = array(
	'PredecessorUID'=>0,
	'Type'=>1,
	'CrossProject'=>0,
	'LinkLag'=>0,
	'LagFormat'=>7
	);
$aTimep = array(
	'Type'=>11,
	'UID'=>0,
	'Start'=>'',
	'Finish'=>'',
	'Unit'=>2,
	'Value'=>5
	);


//echo '<hr>id '.$id;
/*
 * Actions
 */


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/*
 * View
 */

// $title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
// if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
// $help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
// llxHeader("",$title,$help_url);

$form=new Form($db);


//recuperaos informacion
$object->fetch($id);


$modetask = 0;
$tasksarray=$taskadd->getTasksArray(0, 0, $object->id, $socid, $modetask);
$tasksrole=($mode=='mine' ? $taskstatic->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');

$aData = array();
$j=0; $level=0;
$aData=monprojectLineexport($j, 0, $tasksarray, $level, true, 0, $tasksrole, $id, 1,$lVista,$aData);
$numrows = count($aData);
//variables vacias
$anewProject = array();
$anewTasks = array();
$anewPred = array();
$anewTimep = array();
//obtenemos las fechas limites
$dateini = '';
$horaini = '';
$datefin = '';
$horafin = '';
foreach($aData AS $j => $data)
{
  //echo '<hr>'.$dateini.' dateinidb '.$data['datestart'];
  //echo '<br>'.$datefin.' datefindb '.$data['dateend'].' compara '.dol_print_date($datefin,'day').' '.dol_print_date($data['dateend'],'day');
	if (empty($dateini) ||  $data['datestart'] <= $dateini)
		$dateini = $data['datestart'];
	if (empty($datefin) || $data['dateend'] >= $datefin )
		$datefin = $data['dateend'];

}
//echo '<hr>res '.dol_print_date($dateini,'day').' '.dol_print_date($datefin,'day');
//variablew cworkweeks
$aTimePeriod = array(
	'FromDate' => dol_print_date($dateini,'dayrfc').'T00:00:00',
	'ToDate' => dol_print_date($datefin,'dayrfc').'T00:00:00',
	);
$aWorkWeek = array(
	'TimePeriod' => 'aTimePeriod',
	'Name'=>'LABORAL',
	'WeekDays'=> 'aWeekDays',
	);
$aDayLaboraladd = array(1=>1, 7=>1);
$aWeekDay = array(
	'DayType'=>1,
	'DayWorking'=>1,
	'WorkingTimes' => 'aWorkingTimes',
	);

$cWorkWeeks = '<WorkWeek>
<TimePeriod>
	<FromDate>'.dol_print_date($dateini,'dayrfc').'T00:00:00</FromDate>
	<ToDate>'.dol_print_date($datefin,'dayrfc').'T23:59:00</ToDate>
</TimePeriod>
<Name>LABORAL</Name>
<WeekDays>
	<WeekDay>
		<DayType>1</DayType>
		<DayWorking>1</DayWorking>
		<WorkingTimes>
			<WorkingTime>
				<FromTime>08:00:00</FromTime>
				<ToTime>19:00:00</ToTime>
			</WorkingTime>
		</WorkingTimes>
	</WeekDay>
	<WeekDay>
		<DayType>7</DayType>
		<DayWorking>1</DayWorking>
		<WorkingTimes>
			<WorkingTime>
				<FromTime>08:00:00</FromTime>
				<ToTime>19:00:00</ToTime>
			</WorkingTime>
		</WorkingTimes>
	</WeekDay>
</WeekDays>
</WorkWeek>
';


if ($numrows > 0)
{
	//armamos el arreglo para la cabecera project
	$i = 0;
	foreach ($aProject AS $campo => $value)
	{
		switch ($campo)
		{
			case 'Name':
			$anewProject[$i][$campo] = $object->ref;
			break;
			case 'StartDate':
			$anewProject[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			case 'FinishDate':
			$anewProject[$i][$campo] = dol_print_date($datefin,'dayrfc').'T12:00:00';
			break;
			case 'CurrentDate':
			$anewProject[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			default:
			$anewProject[$i][$campo] = $value;
		}
	}
	//armamos el arreglo para calendar
	foreach ($aCalendar AS $campo => $value)
	{
		switch ($campo)
		{
			case 'WeekDays':
				$anewCalendar[$i][$campo] = $cWeekDay;
				break;
			case 'WorkWeeks':
				$anewCalendar[$i][$campo] = $cWorkWeeks;
				break;
			default:
				$anewCalendar[$i][$campo] = $value;
				break;
		}
	}
	//armamos el arreglo para las tasks
	//primero con valor 0
	$datehoy = dol_now();
	foreach ($aTask AS $campo => $value)
	{
		switch ($campo)
		{
			case 'CreateDate':
			$anewTasks[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08H0M0S';
			break;
			case 'Name':
			$anewTasks[$i][$campo] = $object->ref;
			break;
			case 'WBS':
			$anewTasks[$i][$campo] = 0;
			break;
			case 'Start':
			$anewTasks[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			case 'Finish':
			$anewTasks[$i][$campo] = dol_print_date($datefin,'dayrfc').'T12:00:00';
			break;
			case 'Duration':
			$anewTasks[$i][$campo] = 'PT500H0M0S';
			break;
			case 'ManualStart':
			$anewTasks[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			case 'ManualFinish':
			$anewTasks[$i][$campo] = dol_print_date($datefin,'dayrfc').'T12:00:00';
			break;
			case 'ManualDuration':
			$anewTasks[$i][$campo] = 'PT500H0M0S';
			break;
			case 'Stop':
			$anewTasks[$i][$campo] = dol_print_date($datehoy,'dayrfc').'T08:00:00';
			break;
			case 'Resume':
			$anewTasks[$i][$campo] = dol_print_date($datehoy,'dayrfc').'T18:00:00';
			break;
			case 'ActualStart':
			$anewTasks[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			case 'EarlyStart':
			$anewTasks[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			case 'EarlyFinish':
			$anewTasks[$i][$campo] = dol_print_date($datefin,'dayrfc').'T18:00:00';
			break;
			case 'LateStart':
			$anewTasks[$i][$campo] = dol_print_date($dateini,'dayrfc').'T08:00:00';
			break;
			case 'LateFinish':
			$anewTasks[$i][$campo] = dol_print_date($datefin,'dayrfc').'T18:00:00';
			break;

			default:
			$anewTasks[$i][$campo] = $value;
			break;
		}
	}
	$seq = 1;

//echo '<pre>';
//print_r($aTask);
//    print_r($aData);
//echo '</pre>';exit;
	foreach ($aData AS $i => $data)
	{
		$datestart = (!empty($data['datestart'])?$data['datestart']:$dateini);
		$dateend = (!empty($data['dateend'])?$data['dateend']:$datefin);
		//echo '<hr>original '.$i.' '.dol_print_date($data['datestart'],'day').' '.dol_print_date($data['dateend'],'day');
		//echo '<br>modificado '.$i.' '.dol_print_date($datestart,'day').' '.dol_print_date($dateend,'day');

		foreach ($aTask AS $campo => $value)
		{
			//echo '<hr>seq '.$seq;
			switch ($campo)
			{
				case 'UID':
			  //$anewTasks[$i][$campo] = $data['id'];
				$anewTasks[$i][$campo] = $seq;
				break;
				case 'ID':
					//$anewTasks[$i][$campo] = $data['id'];
					$anewTasks[$i][$campo] = $seq;
					break;
				case 'WBS':
					$anewTasks[$i][$campo] = $data['ref'];
					break;
				case 'Manual':
					//if ($data['group']>0)
					$anewTasks[$i][$campo] = 1;
					break;
				case 'CreateDate':
					//$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08H0M0S';
					break;
				case 'Name':
					$labelname = str_replace("&#xD","bbb",$data['label']);
					$anewTasks[$i][$campo] = filter_var($labelname,FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
					break;
				case 'Start':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:00';
					break;
				case 'Finish':
					$anewTasks[$i][$campo] = dol_print_date($dateend,'dayrfc').'T18:00:00';
					break;
				case 'Duration':
					$nday = num_between_day($datestart, $dateend, 1);
					$nhour = $nday * 8;
					$anewTasks[$i][$campo] = 'PT'.$nhour.'H0M0S';
					break;
				case 'ManualStart':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:00';
					break;
				case 'ManualFinish':
					$anewTasks[$i][$campo] = dol_print_date($dateend,'dayrfc').'T18:00:00';
					break;
				case 'ManualDuration':
					$nday = num_between_day($datestart, $dateend, 1);
					$nhour = $nday * 8;
					  //$nhour++;
					  //echo '<br>day '.$nday.' hour '.$nhour;
					$anewTasks[$i][$campo] = 'PT'.$nhour.'H0M0S';
			  		//            $anewTasks[$i][$campo] = 'PT500H0M0S';
					break;
				case 'Stop':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:00';
					break;
				case 'Resume':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:36';
					break;
				case 'ActualStart':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:00';
					break;
				case 'OutlineNumber':
					$anewTasks[$i][$campo] = $data['line_number'];
					break;
				case 'OutlineLevel':
					$anewTasks[$i][$campo] = $data['level'];
					break;
				case 'EarlyStart':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:00';
					break;
				case 'EarlyFinish':
					$anewTasks[$i][$campo] = dol_print_date($dateend,'dayrfc').'T08:00:00';
					break;
				case 'LateStart':
					$anewTasks[$i][$campo] = dol_print_date($datestart,'dayrfc').'T08:00:00';
					break;
				case 'LateFinish':
					$anewTasks[$i][$campo] = dol_print_date($dateend,'dayrfc').'T12:00:00';
					break;
				case 'Critical':
					$anewTasks[$i][$campo] = 0;
					break;
				case 'Summary':
					//verificamos si tiene hijos
					$filterstatic = " AND t.fk_task_parent = ".$data['id'];
					$numtask = $taskadd->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
					if ($numtask > 0)
						$anewTasks[$i][$campo] = 1;
					else
						$anewTasks[$i][$campo] = 0;
					break;
				default:
					$anewTasks[$i][$campo] = $value;
					break;
			}
		}
		$seq++;
	}
	//exit;
	//echo '<hr>procesado ahora finalizamos';
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$r = $doc->createElement("Project");
	$doc->appendChild( $r );
	foreach ($anewProject AS $project)
	{
		foreach ($project AS $campo => $value)
		{
			$b = $doc->createElement($campo);
			$newcampo = $doc->createElement($campo);
			$newcampo->appendChild($doc->createTextNode($value));
			$r->appendChild( $newcampo );
		}
	  	//agregamos el calendars

		$ca = $doc->createElement('Calendars');
		$ca1 = $doc->createElement('Calendar');
		foreach ($aCalendar AS $campo => $value)
		{
			switch ($campo)
			{
				case 'WeekDays':
					//agregamos los WeekDays
					$wd = $doc->createElement('WeekDays');
					//recorremos los dias de la semana
					foreach ($aDayLaboral AS $nDay => $nValue)
					{
						$wd1 = $doc->createElement('WeekDay');
						foreach ($aWeekDay AS $campowd => $valuewd)
						{
							switch ($campowd)
							{
								case 'DayType':
									$newcampowd = $doc->createElement($campowd);
									$newcampowd->appendChild($doc->createTextNode($nDay));
									$wd1->appendChild($newcampowd);
									break;
								case 'DayWorking':
									$newcampowd = $doc->createElement($campowd);
									$newcampowd->appendChild($doc->createTextNode($nValue));
									$wd1->appendChild($newcampowd);
									break;
								case 'WorkingTimes':
									$newcampowd = $doc->createElement($campowd);
									$newcampowd->appendChild($doc->createTextNode($valuewd));
									if ($nValue == 1)
									{
										//agregamos un nuevo element
										$wt = $doc->createElement('WorkingTimes');
										$wt1 = $doc->createElement('WorkingTime');
										foreach ($aWorkingTime AS $campowt => $valuewt)
										{
											$newcampowt = $doc->createElement($campowt);
											$newcampowt->appendChild($doc->createTextNode($valuewt));
											$wt1->appendChild($newcampowt);
										}
										$wt->appendChild($wt1);
										$wt1 = $doc->createElement('WorkingTime');
										//segundo horario
										foreach ($aWorkingTime2 AS $campowt => $valuewt)
										{
											$newcampowt = $doc->createElement($campowt);
											$newcampowt->appendChild($doc->createTextNode($valuewt));
											$wt1->appendChild($newcampowt);
										}
										$wt->appendChild($wt1);
										$wd1->appendChild($wt);
									}
									break;
								default:
									$newcampowd = $doc->createElement($campowd);
									$newcampowd->appendChild($doc->createTextNode($valuewd));
									$wd1->appendChild($newcampowd);
									break;
							}
						}
						$wd->appendChild($wd1);
					}
					$ca1->appendChild($wd);
					break;
				case 'WorkWeeks':
					//registramos
					$ww = $doc->createElement('WorkWeeks');
					$ww1 = $doc->createElement('WorkWeek');
					foreach ($aWorkWeek AS $campoww => $valueww)
					{
						switch ($campoww)
						{
							case 'TimePeriod':
								$tp = $doc->createElement('TimePeriod');
								foreach ($aTimePeriod AS $campotp => $valuetp)
								{
									$newcampotp = $doc->createElement($campotp);
									$newcampotp->appendChild($doc->createTextNode($valuetp));
									$tp->appendChild($newcampotp);
								}
								$ww1->appendChild($tp);
								break;
							case 'WeekDays':
								//agregamos los WeekDays
								$wd = $doc->createElement('WeekDays');
								//recorremos los dias de la semana
								foreach ($aDayLaboraladd AS $nDay => $nValue)
								{
									$wd1 = $doc->createElement('WeekDay');
									foreach ($aWeekDay AS $campowd => $valuewd)
									{
										switch ($campowd)
										{
											case 'DayType':
												$newcampowd = $doc->createElement($campowd);
												$newcampowd->appendChild($doc->createTextNode($nDay));
												$wd1->appendChild($newcampowd);
												break;
											case 'DayWorking':
												$newcampowd = $doc->createElement($campowd);
												$newcampowd->appendChild($doc->createTextNode($nValue));
												$wd1->appendChild($newcampowd);
												break;
											case 'WorkingTimes':
												//$newcampowd = $doc->createElement($campowd);
												//$newcampowd->appendChild($doc->createTextNode($valuewd));
												if ($nValue == 1)
												{
													//agregamos un nuevo element
													$wt = $doc->createElement('WorkingTimes');
													$wt1 = $doc->createElement('WorkingTime');
													foreach ($aWorkingTime AS $campowt => $valuewt)
													{
														$newcampowt = $doc->createElement($campowt);
														$newcampowt->appendChild($doc->createTextNode($valuewt));
														$wt1->appendChild($newcampowt);
													}
													$wt->appendChild($wt1);
													$wd1->appendChild($wt);
												}
												break;
											default:
												$newcampowd = $doc->createElement($campowd);
												$newcampowd->appendChild($doc->createTextNode($valuewd));
												$wd1->appendChild($newcampowd);
												break;
										}
									}
									$wd->appendChild($wd1);
								}
								$ww1->appendChild($wd);
								break;
							default:
								$newcampoww = $doc->createElement($campoww);
								$newcampoww->appendChild($doc->createTextNode($valueww));
								$ww1->appendChild($newcampoww);
								break;
						}
					}
					$ww->appendChild($ww1);
					$ca1->appendChild($ww);
					break;
				default:
					//$ca1a = $doc->createElement($campo);
					$newcampo = $doc->createElement($campo);
					$newcampo->appendChild($doc->createTextNode($value));
					$ca1->appendChild( $newcampo );
					break;
			}
		}
		$ca->appendChild( $ca1 );
		$r->appendChild( $ca );

		//agregamos las tareas
		$c = $doc->createElement( "Tasks" );
		foreach ($anewTasks AS $tasks)
		{
			$d = $doc->createElement( "Task" );
			foreach ($tasks AS $campo => $value)
			{
				$b = $doc->createElement($campo);
				$newcampo = $doc->createElement($campo);
				$newcampo->appendChild($doc->createTextNode($value));
		  		//$b->appendChild($newcampo);
				$d->appendChild( $newcampo );
			}
			//agregamos las tareas
			$c->appendChild( $d );
		}
		$r->appendChild( $c );
		//echo '<hr>paso final ';
	}
	//echo '<hr>antes de guardar ';
	$doc->save('excel/'.$object->ref.'.xml');
	$doc->saveXML();
	$_SESSION['docsave'] = $object->ref.'.xml';
	header('Location: '.DOL_URL_ROOT.'/monprojet/fiche_export.php');

	//echo '<HR>GUARDADO ';
}


?>