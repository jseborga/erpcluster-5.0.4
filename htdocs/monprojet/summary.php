<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */


require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';


$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$project_id = $id;
$mode = GETPOST('mode', 'alpha');
$action = GETPOST('action', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);
$date_start = dol_mktime(($_POST['di_hour']?$_POST['di_hour']:0),($_POST['di_min']?$_POST['di_min']:0),0,$_POST['di_month'],$_POST['di_day'],$_POST['di_year'],'user');
$date_end = dol_mktime(($_POST['df_hour']?$_POST['df_hour']:23),($_POST['df_min']?$_POST['df_min']:59),59,$_POST['df_month'],$_POST['df_day'],$_POST['df_year'],'user');
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects


$projectstatic = new Project($db);
$object = new Task($db);
$taskadd = new Taskext($db);
$mobject = new Taskext($db);
$projettaskadd = new ProjettaskAdd($db);

$objdoc = new Projettasktimedoc($db);


// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);

$langs->load("users");
$langs->load("projects");


/*
 * Actions
 */

// None


/*
 * View
 */

$form=new Form($db);
$formother     = new FormOther($db);
$userstatic    = new User($db);
$companystatic = new Societe($db);
$object        = new Task($db);
$projectstatic = new Project($db);
$extrafields   = new ExtraFields($db);

$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');

if (! empty($conf->use_javascript_ajax))
  {
    $arrayofjs=array(
		     '/monprojet/js/jsgantt.js',
		     '/monprojet/js/graphics.js',
		     
		     '/projet/jsgantt_language.js.php?lang='.$langs->defaultlang
		     );
  }

$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$langs->trans("Tasks"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

if ($id > 0 || ! empty($ref))
  {
    $projectstatic->fetch($id,$ref);
    if (empty($id)) $id = $projectstatic->id;
    if ($projectstatic->societe->id > 0)  $result=$projectstatic->societe->fetch($projectstatic->societe->id);

	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $projectstatic->restrictedProjectArea($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


    $tab='Summary';

    $head=project_prepare_head($projectstatic);
    dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

    $param=($mode=='mine'?'&mode=mine':'');

    print '<table class="border" width="100%">';

    $linkback = '<a href="'.DOL_URL_ROOT.'/projet/liste.php">'.$langs->trans("BackToList").'</a>';

    // Ref
    print '<tr><td width="30%">';
    print $langs->trans("Ref");
    print '</td><td>';
    // Define a complementary filter for search of next/prev ref.
    if (! $user->rights->projet->all->lire)
    {
        $projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
        $projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
    }
    print $form->showrefnav($projectstatic, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
    print '</td></tr>';

    print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';

    // print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
    // if (! empty($projectstatic->societe->id)) print $projectstatic->societe->getNomUrl(1);
    // else print '&nbsp;';
    // print '</td>';
    // print '</tr>';

    // // Visibility
    // print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
    // if ($projectstatic->public) print $langs->trans('SharedProject');
    // else print $langs->trans('PrivateProject');
    // print '</td></tr>';

    // // Statut
    // print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

    // // Date start
    // print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
    // print dol_print_date($projectstatic->date_start,'day');
    // print '</td></tr>';
    
    // // Date end
    // print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
    // print dol_print_date($projectstatic->date_end,'day');
    // print '</td></tr>';
    
    
    print '</table>';

    print '</div>';

    if (empty($date_start))
        $date_start = dol_get_first_day(date('Y'),date('m'));
    if (empty($date_end))
        $date_end = dol_get_last_day(date('Y'),date('m'));

    dol_fiche_head();
    print '<form  action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="POST">';
    print '<input type="hidden" name="action" value="report">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="hidden" name="id" value="'.$id.'">';

    print '<table class="noborder" width="100%">';

    print '<tr>';
    print '<td>';
    print $langs->trans('Dateini');
    print '</td>';
    print '<td>';
    print $form->select_date($date_start,'di_',0,0,0);
    print '</td>';

    print '<td>';
    print $langs->trans('Datefin');
    print '</td>';
    print '<td>';
    print $form->select_date($date_end,'df_',0,0,0);
    print '</td>';

    print '<td>';
    print '<center><input type="submit" class="button" value="'.$langs->trans("Ejecution").'"></center>';
    print '</td>';

    print '</tr>';
    print '</table>';
    //validando el encabezado      
    print '</form>';
    
    dol_fiche_end();

    if ($action == 'report' && $id>0)
    {
        /* ******************************* */
        /*                                 */
        /* Barre d'action                  */
        /*                                 */
        /* ******************************* */
    
        print "<div class=\"tabsAction\">\n";
        if ($user->rights->monprojet->payp->rep)
            print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/summaryexcel.php'.'?id='.$id.'&action=export">'.$langs->trans("Excel").'</a>';
        else
            print '<a class="butActionRefused" href="#">'.$langs->trans("Excel").'</a>';  
        print '</div>';
    }
}


/*
 * Summary
 */

print '<br>';

// Get list of tasks in tasksarray and taskarrayfiltered
// We need all tasks (even not limited to a user because a task to user
// can have a parent that is not affected to him).
$tasksarray = $object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
// We load also tasks limited to a particular user
//$tasksrole=($_REQUEST["mode"]=='mine' ? $task->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
//var_dump($tasksarray);
//var_dump($tasksrole);
//recuperamos las fotos
if (count($tasksarray)>0 && $action == 'report')
{
    $aTask = array();
    $aNumTask = array();
    $aSumTask = array();
    $aSumTaskid = array();
    //guardamos la fecha para la exportacion
    $_SESSION['date_startexp'] = $date_start;
    $_SESSION['date_endexp'] = $date_end;
    //covertimos la fecha para sql
    $d_start = $db->idate($date_start);
    $d_end = $db->idate($date_end);
    $aNew = array();
    foreach($tasksarray as $key => $val)
    {
	 $object->fetch($val->id);
	 $res=$object->fetch_optionals($object->id,$extralabels);

	 if ($object->id > 0)
	   {
	     //recuperamos los tiempos dedicados
	     /*
	      *  List of time spent
	      */
	     $sql = "SELECT t.rowid, t.fk_task, t.task_date, t.task_datehour, t.task_duration, t.fk_user, t.note ";
	     $sql.= ", u.lastname, u.firstname";
	     $sql .= " FROM ".MAIN_DB_PREFIX."projet_task_time as t";
	     $sql .= " , ".MAIN_DB_PREFIX."user as u";
	     $sql .= " WHERE t.fk_task =".$object->id;
	     $sql .= " AND t.fk_user = u.rowid";
         //$sql.= " AND t.task_date BETWEEN '".$d_start ."' AND '".$d_end."'";
	     $sql .= " ORDER BY t.task_date DESC, t.rowid DESC";
	     $var=true;
	     $resql = $db->query($sql);
	     //$aNew = array();
	     if ($resql)
	       {
		 $num = $db->num_rows($resql);
		 $i = 0;
		 while ($i < $num)
		   {
		     $row = $db->fetch_object($resql);
             $row->newdatehour = $db->jdate($row->task_datehour);             
		     $aNew[$db->jdate($row->task_datehour)][] = array('note'=>true,'data'=>$row,);
		     $i++;
		   }
		 
		 $db->free($resql);
	       }

	     if (! empty($projectstatic->socid)) $companystatic->fetch($projectstatic->socid);
	     
	     $userWrite  = $projectstatic->restrictedProjectArea($user,'write');
	     //armamos el nuevo array para ordenar por fecha
	     $totalsize=0;
	   }
	 else
	   {
	     header('Location: index.php');
	     exit;
	   }
      }
    // echo '<pre>';
    // print_r($aNew);
    // echo '</pre>';
    // $formfile=new FormFile($db);
    // $formfile->print_head();
    //armamos el encabezado

    print '<table width="100%" class="'.($useinecm?'nobordernopadding':'liste').'">';
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans("Date"),'',"","",$param,'align="center"');
    
    print_liste_field_titre($langs->trans("Documents2"),'',"","",$param,'align="left"');
    print_liste_field_titre($langs->trans("Task"),'',"size","",$param,'align="left"');
    if (empty($useinecm)) print_liste_field_titre('','',"","",$param,'align="center"');
    print_liste_field_titre($langs->trans('Qty'),'','','',$param,'align="right"');
    print_liste_field_titre($langs->trans('Unit'),'','','',$param,'align="center"');
    print_liste_field_titre($langs->trans('Photo'),'','','',$param,'align="right"');
    print '</tr>';
    
    KRSORT($aNew);
    foreach ((array) $aNew AS $date => $aData)
    {
        foreach((array) $aData AS $key => $data)
        {
            if (!$data['note'])
            {
            }
            else
            {
                $objdata = $data['data'];
                $taskadd->fetch($objdata->fk_task);
                $projettaskadd->fetch('',$objdata->fk_task);        
                if (!$aSumTask[$objdata->fk_task])
                    $aSumTask[$objdata->fk_task]= $projettaskadd->unit_declared;

                $res=$taskadd->fetch_optionals($taskadd->id,$extralabels);
                $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['id'] = $objdata->rowid;
                $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['date'] = $objdata->newdatehour;
                $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['newdatehour'] = $objdata->newdatehour;
                //impresion de la nota
                $objdoc->fetch('',$objdata->rowid);
                if ($objdata->newdatehour >= $date_start && $objdata->newdatehour <= $date_end)
                {
                    $aNumTask[$objdata->fk_task]++;
                    $var=!$var;
                    print '<tr '.$bc[$var].'>';
                    print '<td align="center">'.dol_print_date($objdata->task_datehour,"dayhour","tzuser").'</td>';
                    print '<td>';
                    $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['note'] = $objdata->note;
                    print $objdata->note;
                    print "</td>\n";
                    print '<td align="left">';
                    $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['task'] = $taskadd->ref.': '.$taskadd->label;
                    print $taskadd->ref.': '.$taskadd->label;
                    print '</td>';
                    if (empty($useinecm)) print '<td></td>';
                    //unidades declaradas
                    print '<td align="right">';
                    if ($objdoc->fk_task_time == $objdata->rowid)
                    {
                        $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['quant'] = $objdoc->unit_declared;
                        $aSumTaskid[$objdata->fk_task][$objdata->rowid] = $aSumTask[$objdata->fk_task];
                        $aSumTask[$objdata->fk_task] -= $objdoc->unit_declared;
                        print price($objdoc->unit_declared);
                    }
                    print '</td>';
                    //units
                    $unit = $taskadd->getLabelOfUnit('short');
		
                    print '<td align="center">';
                    if ($unit !== '') print $langs->trans($unit);
                    print '</td>';
		
                    //photo
                    print '<td align="right">';
                    //$objdoc->fetch('',$objdata->rowid);
                    if ($objdoc->fk_task_time == $objdata->rowid && !empty($objdoc->document))
                    {
                        $aPhoto = explode(';',$objdoc->document);
                        foreach ((array) $aPhoto AS $j => $doc)
                        {
                            $aFile = explode('.',$doc);
                            //extension
                            $docext = STRTOUPPER($aFile[count($aFile)-1]);
                            $typedoc = 'doc';
                            if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
                                $typedoc = 'fin';	
                            elseif ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
                                $typedoc = 'doc';
                            elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
                                $typedoc = 'doc';
		  	
                              //print $mobject->showphoto($typedoc,$objdata,$doc,$taskadd,$projectstatic, 100,$docext);
                                                    $modulepart = 'projet';
                            print '&nbsp;'.$mobject->showphoto($typedoc,$doc,$objdata,$modulepart, $taskadd,$projectstatic, 100, 0, 0, 'photowithmargin', 'small', 1, 0);

                        }
                    }
                    print "</td>";
                    print "</tr>\n";
                }
                else
                {
                    if ($objdoc->fk_task_time == $objdata->rowid)
                    {
                        $aTask[$objdata->fk_task][$objdata->newdatehour][$objdata->rowid]['quantt'] += $objdoc->unit_declared;
                        $aSumTaskid[$objdata->fk_task][$objdata->rowid] = $aSumTask[$objdata->fk_task];
                        $aSumTask[$objdata->fk_task] -= $objdoc->unit_declared;
                    }
                }
            }
        }
    }
    print '</table>';
    //$formfile->print_fother();

    $_SESSION['aTaskexport'] = $aTask;
    $_SESSION['aNumTaskexport'] = $aNumTask;
    $_SESSION['aSumTaskid'] = $aSumTaskid;
}
else
{
	print $langs->trans("NoTasks");
}


llxFooter();

$db->close();
