<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/product/index.php
 *  \ingroup    product
 *  \brief      Page accueil des produits et services
 */

require '../main.inc.php';
//require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/poagraf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
//require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoauser.class.php");
//require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflowdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';

//require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
require_once DOL_DOCUMENT_ROOT.'/expedition/class/expedition.class.php';
require_once DOL_DOCUMENT_ROOT.'/expedition/class/expeditionstats.class.php';
require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
/*require liste*/
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/pac/class/poapac.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoauser.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidapre.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidacom.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidadev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocesscontrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/structure/class/poastructure.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulated.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulateddet.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulatedof.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulatedto.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivity.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivitydet.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivityworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivitywork.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaactivityprev.class.php");

if ($conf->poai->enabled)
  {
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
  }
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
/*fin require*/

$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');
// Security check
//$result=restrictedArea($user,'contab');
$lPriority = false;
if (isset($_GET['action']))
  $_SESSION['actionindex'] = $_GET['action'];

$action = $_SESSION['actionindex'];
if (empty($action)) $action = 'view';

if (isset($_POST['period_year']))
  $_SESSION['period_year'] = $_POST['period_year'];

if (empty($_SESSION['period_year']))
  $_SESSION['period_year'] = date('Y');
if (isset($_POST['search_login']))
  $_SESSION['isearch_login'] = $_POST['search_login'];
if (isset($_POST['search_text']))
  $_SESSION['isearch_text'] = $_POST['search_text'];
if (isset($_POST['search_priority']))
  {
    //echo '<hr>'.$_POST['search_priority'];
    $lPriority = true;
    if ($_POST['search_priority'] === 0)
      $_SESSION['isearch_priority'] = 0;
    elseif (trim($_POST['search_priority'])==='')
      $_SESSION['isearch_priority'] = -1;
    else
      $_SESSION['isearch_priority'] = $_POST['search_priority'];
  }
//si vacio la session de priority
if (!isset($_SESSION['isearch_priority']))
  {
    $lPriority = true;
    $_SESSION['isearch_priority'] = 1;
  }
if (isset($_POST['sel_area']))
  {
    if ($_POST['sel_area'] <0)
     $_SESSION['sel_area'] = '';
    else
      $_SESSION['sel_area'] = $_POST['sel_area'];
  }
if (isset($_POST['clear']))
  {
    unset($_SESSION['sel_area']);
    unset($_SESSION['isearch_login']);
    unset($_SESSION['isearch_text']);
    $_SESSION['isearch_priority'] = 1;
  }
$period_year      = $_SESSION['period_year'];
$search_login = $_SESSION['isearch_login'];
$sel_area     = $_SESSION['sel_area'];
$sel_text     = $_SESSION['isearch_text'];
$sel_priority = $_SESSION['isearch_priority'];
//echo '|'.$sel_priority.'|'.$lPriority;
//verificamos la seleccion de la prioridad
// if ($sel_priority>=0)
//   $lPriority = true;
$lPrincipal = false;
//dos tipos de filtro
//1 principal del index
if ($lPriority) $lPrincipal = true;
if ($period_year && !$lPrincipal) $lPrincipal = true;
if ($search_login && !$lPrincipal) $lPrincipal = true;
if ($sel_area && !$lPrincipal) $lPrincipal = true;
if ($sel_text && !$lPrincipal) $lPrincipal = true;
if ($sel_priority && !$lPrincipal) $lPrincipal = true;

//2 del buscador de lisprev


$langs->load("others");
$langs->load("poa@poa");

$object = new Poapoa($db);
$objpre = new Poaprev($db);
//$objpre = new Poapartidapre($db);
$objcom = new Poapartidacom($db);
$objdev = new Poapartidadev($db);
$objarea= new Poaarea($db);
////////////
$objectuser = new Poapoauser($db);
$objpac = new Poapac($db);
$objactw = new Poaactivityworkflow($db);
$objproc = new Poaprocess($db);
$objprocc = new Poaprocesscontrat($db);
$objprev  = new Poapartidapre($db);
$objcomp    = new Poapartidacom($db);
$objdeve    = new Poapartidadev($db);
$objrefo    = new Poareformulated($db);
$objrefodet = new Poareformulateddet($db);
$objcon     = new Contrat($db);
$objactwork = new Poaactivitywork($db);
//determinamos a que gruipo pertenece
$objusrgroup = new Usergroup($db);
$aGroup = $objusrgroup->listGroupsForUser($user->id);
foreach((array) $aGroup AS $i => $objgroup)
{
  $arrayGroup[$objgroup->name] = $objgroup->name;
}
//actualizacion de campo fk_contrato en poapartidacom
include DOL_DOCUMENT_ROOT.'/poa/lib/actualizacom.php';

//determinamos que permisos tiene el usuario
$aPriv = array();
$aArrayarea = array();
if (!$user->admin)
  {
    $objareau = new Poaareauser($db);
    $aAreau = $objareau->getuserarea($user->id);
    foreach((array) $aAreau AS $i => $objau)
      {
	$aPriv[$objau->id] = $objau->privilege;	
	$aArrayarea[$objau->id] = $objau->id;	
      }
  }
$_SESSION['userpriv'] = $aPriv;


/*
 * View
 */

$transAreaType = $langs->trans("Poa");
$helpurl='';
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/style-desktop.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js');
llxHeader("",$langs->trans("Poa"),$help_url,'','','',$aArrjs,$aArrcss);

print_fiche_titre($transAreaType);

$mesg = '';
//revisamos mensajes para cada usuario distinto al admin
if (!$user->admin)
{
  include_once DOL_DOCUMENT_ROOT."/poa/lib/mensajes.lib.php";
}

$form=new Form($db);

print '<div class="fichecenter">';

print '<div id="resum-izq">';    

// Recherche period_year
$var=false;
// print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
// print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
// print '<input type="hidden" name="defperiod_year" value="update">';
// print '<table class="noborder nohover" width="100%">';
// print '<tr class="liste_titre"><td colspan="5">'.$langs->trans("Year").'</td></tr>';
// print '<tr '.$bc[$var].'>';
// print '<td class="nowrap">'.$langs->trans("Year").':</td><td><input type="number" class="flat" name="period_year" value="'.(empty($period_year)?date('Y'):$period_year).'" size="4"></td>';
//     //user
//     print '<td>';
//     $aExcluded = array(1=>1);
//     print $form->select_dolusers($search_user,'search_user',1,$aExcluded,'','','','',20);
//     print '</td>';

//     //area
//     print '<td>';
//     $aExcluded = array(1=>1);
//     print $objarea->select_area($sel_area,'sel_area','',30,1,0,'',$aArrayarea);
//     print '</td>';

// print '<td rowspan="2"><input type="submit" value="'.$langs->trans("Select").'" class="button"></td></tr>';
// print "</table></form>\n";



print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="defperiod_year" value="update">';
print '<div id="divfilt">';
print '<span>'.$langs->trans("Year").':</span>';
print '<input type="number" class="flat" name="period_year" value="'.(empty($period_year)?date('Y'):$period_year).'" size="4">';
print '</div>';

dol_htmloutput_mesg($mesg);
//$_SESSION['dol_hide_leftmenu'] = 1;
//$_SESSION['dol_hide_topmenu'] = 1;
//user
print '<div id="divfilt">';
print '<span>'.$langs->trans("User").':</span>';
$aExcluded = array(1=>1);
print $form->select_dolusers($search_login,'search_login',1,$aExcluded,'','','','',15);
print '</div>';

    //area
print '<div id="divfilt">';
print '<span>'.$langs->trans("Area").':</span>';
$aExcluded = array(1=>1);
print $objarea->select_area($sel_area,'sel_area','',30,1,0,'',$aArrayarea);
print '</div>';

    //text
print '<div id="divfilt">';
print '<span>'.$langs->trans("Search").':</span>';
print '<input type="text" class="flat" name="search_text" value="'.$sel_text.'" size="12">';
print '</div>';

    //priority
print '<div id="divfilt">';
print '<span>'.$langs->trans("Priority").':</span>';
print '<input type="text" class="flat" name="search_priority" value="'.($sel_priority==-1?'':$sel_priority).'" size="4">';
print '</div>';

print '<div id="divfilt">';
print '<input type="submit" value="'.$langs->trans("Filter").'" class="button">';
print '&nbsp;';
print '<input type="submit" value="'.$langs->trans("Clear").'" name="clear" class="button">';
print '</div>';
print '<div style="clear:both;"></div>';
print '</form>';


print "<br>\n";

print '</div>';
print '<div style="clear:both;"></div>';
//fin recharche period_year



//lista de usuarios con presupuesto
//procesando para el reporte
$objwork  = new Poaworkflow($db);
$objworkd = new Poaworkflowdet($db);
$objpoa   = new Poapoa($db);
$objuser  = new User($db);
if ($search_login < 0)
  $search_login = 0;
$objpoa->getlist_user($period_year,$search_login,$sel_area,'A');
//rango de colores error para retraso
$cRangecolors = $conf->global->POA_COLORS_RANGE_DAYS_LATE;
list($cDays,$cColors) = explode('|',$conf->global->POA_COLORS_RANGE_DAYS_LATE);
list($cDaysall,$cColorsall) = explode('|',$conf->global->POA_COLORS_RANGE_DAYS_LATE_ALL);
$acDays = explode(',',$cDays);
$acColors = explode(',',$cColors);
$acDaysall = explode(',',$cDaysall);
$acColorsall = explode(',',$cColorsall);
$nDia = 0;
//definiciones de array
$aRetraso_ = array();
$aRetrasoall_ = array();
$nTotal = 0;
$aEstado = array();
$aPreventivo = array();
$nDiamax = 0;
$aColorsid = array();
$j = 0;
foreach ((array) $acDays AS $j => $nDay)
{
  $aRetraso_[$nDay] = 0;
  $aColors[$nDay] = $acColors[$j];

  $aDays[$nDay] = array(1=>$nDia,2=>$nDay);
  $nDia = $nDay+1;
  $nDiamax = $nDay;
  $aColorsid[$nDay] = $j;
  $j++;
}
$aDays[$nDia] = array(1=>$nDia,2=>1000);
$nDiamax+=1;
$aRetraso_[$nDiamax];
$aColors[$nDiamax] = 'FF0000';
$aColorsid[$nDiamax] = $j;

//armar la reversa de aColors
$aColorr = array();
$nLength = count($aColors)-1;
foreach((array) $aColors AS $j => $colors)
{
  $aColorr[$nLength] = $colors;
  $nLength--;
}
$nDiaall = 0;
foreach ((array) $acDaysall AS $j => $nDay)
{
  $aRetrasoall_[$nDay] = 0;
  $aColorsall[$nDay] = $acColorsall[$j];
  $aDaysall[$nDay] = array(1=>$nDiaall,2=>$nDay);
  $nDiaall = $nDay+1;
  $nDiamaxall = $nDay;
}
$aDaysall[$nDiaall] = array(1=>$nDiall,2=>1000);
$nDiamaxall+=1;
$aRetrasoall_[$nDiamaxall];
$aColorsall[$nDiamaxall] = 'FF0000';
//array preventivo
//    $aPreventivo[-1] = 0;
$aPreventivo[0] = 0;
$aPreventivo[1] = 0;
$aPreventivo[2] = 0;
$aPreventivo[3] = 0;
//fin rango de colores

//print '<div style="clear:both;"></div>';

$sumaPresup = 0;
$sumaComp = 0;
$sumaPrev = 0;
$sumaDeve = 0;
$sumaNroPresup = 0;
$sumaNroPre = 0;
$sumaNroCom = 0;
$sumaNroDev = 0;
$nTotal = 0;
$aUser = array();
$aPrevarea = array();
foreach ((array) $objpoa->array AS $fk_user => $aData)
{
  $objuser->fetch($fk_user);
  $aUser[$fk_user] = array('name'=>$objuser->lastname.' '.$objuser->firstname,
			   'login'=> $objuser->login);
  //obtenemos todos los preventivos generados por el usuario
  foreach($aData AS $j => $objpoauser)
    {
      $aDataPoa[$fk_user]['presup']+=$objpoauser->amount;
      $aDataPoa[$fk_user]['nropresup']++;
      $sumaPresup+= $objpoauser->amount;
      $sumaNroPresup++;
    }
}
//ordenando aUser
//ksort($aUser);
//ejecucion presupuestaria
$objpre->getlist($period_year,$search_login,$sel_area,$sel_text);
$aPrev = array();
//revisar
$priority = $conf->global->POA_PRIORITY;
//definicion de workflow limit
$wfone = $conf->global->POA_WORKFLOW_LIMIT_ONE;
$wftwo = $conf->global->POA_WORKFLOW_LIMIT_TWO;
$wfthr = $conf->global->POA_WORKFLOW_LIMIT_THR;

// if ($lPriority)
//   {
//     $priority = $_SESSION['isearch_priority'];
//   }
foreach((array) $objpre->array AS $fk_poa_prev => $obj)
{
  if (empty($obj->fk_father))
    {
      //armamos array para aquellos que son prioridad 1
  //$priority = 1;
  if ($priority == 0 && empty($obj->fk_father))
    $aPrev[$fk_poa_prev] = $fk_poa_prev;
  else
    {
      if ($obj->priority == $priority && empty($obj->fk_father))
	$aPrev[$fk_poa_prev] = $fk_poa_prev;
    }
  //preventivo
  $aRetraso = $aRetraso_;
  $aRetrasoall = $aRetrasoall_;
  $fk_user = $obj->fk_user_create;
  $idsArea = filter_area_user($fk_user,true);
  $aIdsarea = explode(',',$idsArea);
  //recorremos para armar el array de sigla area
  $carea = '';
  foreach($aIdsarea AS $j => $value)
      {
	//buscamos el area
	$objarea->fetch($value);
	if ($objarea->id == $value)
	  {
	    $aArea[$objarea->ref] = $objarea->ref;
	    if (!empty($carea)) $carea.=',';
	    $carea.= $objarea->ref;
	  }
      }
  $objuser->fetch($fk_user);
  $aUser[$fk_user] = array('name'  => $objuser->lastname.' '.$objuser->firstname,
			   'login' => $objuser->login,
			   'carea' => $carea);
  $objpre->getlist($fk_poa_prev);
  $aDataPoa[$fk_user]['nroprev']++;
  $daydelay = 0;
  $daydelayall = 0;

  //proceso cerrado
  if ($obj->statut == 9)
    $aDataPoa[$fk_user]['cerrado']++;
  else
    {
      //buscamos donde se encuentra el proceso
      if ($objwork->fetch_prev($obj->id)>0)
	{

	}
      //$aPrevarea[$fk_user]['arealocal'][]
    }
  //sumamos los preventivos
  foreach((array) $objpre->array AS $j => $objprev_)
    {
      $aDataPoa[$fk_user]['prev']+=$objprev_->amount;
      $sumaPrev+= $objprev_->amount;
    }
  //busqueda workflow
  $nTotal++;
  $lStatutejec = false;
  $date_tracking = '';
  $carea = '';
  $date_workflow = $db->jdate($obj->date_preventive);
  $objworkact = '';
  $workstatut = 0;
  $codeProcedure = '';
  //buscamos el workflow
  if ($objwork->fetch_prev($obj->id)>0)
    {
      if ($objwork->fk_poa_prev == $obj->id)
	{
	  $workstatut = $objwork->statut;
	  //buscamos el ultimo registro workflowdet
	  $objworkd->getlist($objwork->id,1);
	  
	  foreach((array) $objworkd->array AS $l => $objWorkDet)
	    {
	      if (empty($date_tracking))
		{
		  $date_tracking = $objWorkDet->date_tracking;
		  $carea = $objWorkDet->code_area_next;
		  $codeProcedure = $objWorkDet->code_procedure;
		  $objworkact = $objWorkDet;
		}
	    }
	  //buscamos el typeprocedure
	  $objProcedure = fetch_typeprocedure($codeProcedure,'code');
	  //analizamos si esta entre los rangos workflowone y workflowtwo
	  if (!empty($wfone) && !empty($wftwo))
	    {
	      if (($objProcedure->landmark > $wfone && $objpProcedure->landmark < $wftwo) || $objProcedure->landmark >= $wfthr)
		$lStatutejec = true;
	    }
	  //determinamos el tiempo transcurrido
	  $daydelayall = resta_fechas($date_workflow,dol_now(),1);
	  if ($workstatut < 2)  //revisa
	    $daydelay    = resta_fechas($date_tracking,dol_now(),1);
	  if ($lStatutejec)
	    $daydelay = 1;
	}
    }
  //indiviudal
  foreach ((array) $aDays AS $nDay => $aDay)
    {
      //echo '<br>daydelay '.$daydelay.' '.$aDay[1].' '.$aDay[2];
      if ($daydelay >= $aDay[1] && $daydelay <= $aDay[2])
	{
	  $aRetraso[$nDay]++;
	  if ($workstatut < 2)
	    {
	      //revisando si esta en el area que le corresponde
	      if ($aArea[$carea])
		$aDataPoa[$fk_user]['retraso'][$nDay]++;
	      if (empty($carea))
		$aDataPoa['retrasoarea'][$langs->trans('Not initiated')][$nDay]++;
	      else
		$aDataPoa['retrasoarea'][$carea][$nDay]++;
	    }
	}
    }

  if ($workstatut == 2)
    {
      $aDataPoa[$fk_user]['cerrado']++;
      $aDataPoa['cerradoarea'][$carea]++;
      $aDataPoa['cerradoareaall'][$carea]++;
    }
  //    $aRetraso[0]++;
      //all
  if ($daydelayall > 0)
    {
      foreach ((array) $aDaysall AS $nDay => $aDay)
	{
	  if ($daydelayall >= $aDay[1] && $daydelayall <= $aDay[2])
	    {
	      $aRetrasoall[$nDay]++;
	      $aDataPoa[$fk_user]['retrasoall'][$nDay]++;
	      if ($obj->statut == 1)
		if (empty($carea))
		  $aDataPoa['retrasoareaall'][$langs->trans('Not initiated')][$nDay]++;
		else
		  $aDataPoa['retrasoareaall'][$carea][$nDay]++;
	    }
	}
      // if ($obj->statut == 2)
      // 	$aDataPoa['cerradoareaall'][$carea]++;
    }
  else
    $aDataPoa[$fk_user]['retrasoall'][0]++;
  //    $aRetrasoall[0]++;
  // $aDataPoa[$fk_user]['retraso'] = $aRetraso;
  // $aDataPoa[$fk_user]['retrasoall'] = $aRetrasoall;

  //comprometido
  $objcomp = new Poapartidacom($db);
  $objcomp->getlist($fk_poa_prev);
  if (count($objcomp->array) > 0)
    $aDataPoa[$fk_user]['nrocomp']++;    
  foreach((array) $objcomp->array AS $j => $objcompp)
    {
      $aDataPoa[$fk_user]['comp']+=$objcompp->amount;
      $sumaComp+= $objcompp->amount;
    }
  //devengado
  $objdev = new Poapartidadev($db);
  $objdev->getlist($fk_poa_prev);
  if (count($objdev->array) > 0)
    $aDataPoa[$fk_user]['nrodeve']++;
    
  foreach((array) $objdev->array AS $j => $objdeve)
    {
      $aDataPoa[$fk_user]['deve']+=$objdeve->amount;
      $sumaDeve+= $objdeve->amount;
    }
    }
}
//obtenemos el presupuesto mas grande
$maxPresup = 0;
$maxnPresup = 0;
$maxnPre = 0;
$maxnCom = 0;
$maxnDev = 0;
foreach ((array) $aDataPoa AS $fk_user => $aData)
{
  if ($maxPresup <= $aData['presup'])
    $maxPresup = $aData['presup'];
  if ($maxnPresup <= $aData['nropresup'])
    $maxnPresup = $aData['nropresup'];
  if ($maxnPre <= $aData['nroprev'])
    $maxnPre = $aData['nroprev'];
  if ($maxnCom <= $aData['nrocomp'])
    $maxnCom = $aData['nrocomp'];
  if ($maxnDev <= $aData['nrodeve'])
    $maxnDev = $aData['nrodeve'];
}
$nLength = 0;

/////////////////////////////////////////////
//Resumen de actividades con prioridad
print '<br>';
print '<div id="resum-izq">';    //div1

//desarrollo
if (isset($_GET['mostrar']))
  $_SESSION['opver'] = '';
if (isset($_GET['opver']))
  $_SESSION['opver'] = $_GET['opver'];
$opver = $_SESSION['opver'];

//asignando filtro de usuario
assign_filter_user('search_login');

$action = GETPOST('action');
//period_year
$period_year = GETPOST('period_year');

//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];

//period_year definida en index.php
if (empty($_SESSION['period_year']))
  $_SESSION['period_year'] = date('Y');

$period_year = $_SESSION['period_year'];

//$aUserr = array_all_user(1,$name='login'); //recupera todos los usuarios

// Add
if ($action == 'add' && $user->rights->poa->poa->crear)
  {
    $error = 0;
    $object->fk_structure = $_POST["fk_structure"];
    //buscamos structure
    $objstr->fetch($object->fk_structure);
    if ($objstr->id == $object->fk_structure)
      $object->ref = $objstr->sigla;
    $object->label     = GETPOST('label');
    $object->partida   = GETPOST('partida');
    $object->period_year   = $period_year;
    $object->amount    = GETPOST('amount');

    $object->entity  = $conf->entity;
    $object->active  = 1;
    $object->statut  = 1;
    $object->version = GETPOST('version');
    if ($object->fk_structure <=0)
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorstructureisrequired").'</div>';
      }
    if (empty($object->label))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
      }

    if (empty($error)) 
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
	    header("Location: ".$_SERVER['PHP_SELF']);
	    exit;
	  }
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	if ($error)
	  $action="create";   // Force retour sur page creation
      }
  }
if ($_POST["cancel"] == $langs->trans("Cancel"))
{
  $action = '';
  $_GET["id"] = $_POST["id"];
}

//buscamos el maximo monto presupuestado
$object->get_maxmin($period_year,'M');
//dividimos en 5
$maximo = $object->maxmin;
$object->get_maxmin($period_year,'m');
// //dividimos en 5
// $minimo = $object->maxmin;

//array color para pac
$aColorpac = $_SESSION['aColorpac'];
//armamos en un array el valor
$sumamax = $minimo;
$aLimite[5] = $maximo / 1;
$aLimite[4] = $maximo / 2;
$aLimite[3] = $maximo / 20;
$aLimite[2] = $maximo / 200;
$aLimite[1] = $maximo / 2000;

//array de grafico
$aGrafico = array (1=>'0.png',2=>'1.png',3=>'2.png',4=>'3.png',5=>'4.png');
//ocultar columnas
$vercol = GETPOST('vercol');
$aCol = array(1=>2,
	      2=>1,
	      3=>4,
	      4=>3,
	      7=>8,
	      8=>7,
	      9=>array(10,15),
	      10=>array(9,15),
	      15=>array(9,10),
	      11=>array(12,16),
	      12=>array(11,16),
	      16=>array(11,12),
	      13=>array(14,17),
	      14=>array(13,17),
	      17=>array(13,14),
	      18=>19,
	      19=>18,
	      51=>52,
	      52=>51,
	      61=>62,
	      62=>61,
	      71=>array(72,73),
	      72=>array(71,73),
	      73=>array(71,72),
	      81=>array(82,83,84,85),
	      82=>array(81,83,84,85),
	      83=>array(81,82,84,85),
	      84=>array(81,82,83,85),
	      85=>array(81,82,83,84),
	      91=>92,
	      92=>91,
	      93=>94,
	      94=>93,
	      190=>array(191,192,193,194,195,196,197,198,199,200),
	      191=>array(190,192,193,194,195,196,197,198,199,200),
	      192=>array(191,190,193,194,195,196,197,198,199,200),
	      193=>array(191,192,190,194,195,196,197,198,199,200),
	      194=>array(191,192,193,190,195,196,197,198,199,200),
	      195=>array(191,192,193,194,190,196,197,198,199,200),
	      196=>array(191,192,193,194,195,190,197,198,199,200),
	      197=>array(191,192,193,194,195,196,190,198,199,200),
	      198=>array(191,192,193,194,195,196,197,190,199,200),
	      199=>array(191,192,193,194,195,196,197,198,190,200),
	      200=>array(191,192,193,194,195,196,197,198,199,190),
	      );
$aBalance = array(1=>array(0,40),
		  2=>array(41,70),
		  3=>array(71,100));

$aImageFondo = array(1=>'bajo.png',
		  2=>'centro.png',
		  3=>'alto.png');

$aVal = array(1=>array(1,20000),
	      2=>array(20001,200000),
	      3=>array(200001,10000000));


if (empty($_SESSION['numColi']))
  {
    $_SESSION['numColi'] = array(1=>false,
				2=>true,
				3=>true,
				4=>false,
				7=>false,
				8=>true,
				9=>true,
				10=>false,
				11=>true,
				12=>false,
				13=>true,
				14=>false,
				17=>false,
				51=>false,
				52=>true,
				61=>false,
				62=>true,
				71=>false,
				72=>false,
				73=>false,
				81=>false,
				82=>false,
				83=>false,
				84=>false,
				85=>false,
				91=>true,
				92=>false,
				93=>true,
				94=>false,
				190=>false,
				191=>false,
				192=>false,
				193=>false,
				194=>false,
				195=>false,
				196=>false,
				197=>false,
				198=>false,
				199=>false,
				200=>true,
				);    
  }

//recibiendo valores
if(isset($_GET['vercol']) || isset($_POST['vercol']))
  {
    $_SESSION['numColi'][$vercol] = true;
    if (is_array($aCol[$vercol]))
      {
	foreach($aCol[$vercol] AS $i1 => $nCol1)
	  {
	    $_SESSION['numColi'][$nCol1] = false;
	  }
      }
    else
      $_SESSION['numColi'][$aCol[$vercol]] = false;
  }
if (isset($vercol) && $vercol == 51)
  {
    $_SESSION['colorUser'] = true;
    $_SESSION['colorPartida'] = false;
  }
if (isset($vercol) && $vercol == 52)
  {
    $_SESSION['colorUser'] = false;
    $_SESSION['colorPartida'] = false;
  }
if (isset($vercol) && $vercol == 61)
  {
    $_SESSION['colorPartida'] = true;
    $_SESSION['colorUser'] = false;
  }
if (isset($vercol) && $vercol == 62)
  {
    $_SESSION['colorPartida'] = false;
    $_SESSION['colorUser'] = false;
  }

//recibiendo valores de menu
if (isset($_POST['a1'])) $_SESSION['a1'] = $_POST['a1'];//productos
if (isset($_POST['a2'])) $_SESSION['a2'] = $_POST['a2'];//calendario
if (isset($_POST['a3'])) $_SESSION['a3'] = $_POST['a3'];//usuario
if (isset($_POST['a4'])) $_SESSION['a4'] = $_POST['a4'];//
if (isset($_POST['a5'])) $_SESSION['a5'] = $_POST['a5'];//prioridad
if (isset($_POST["Enviar"]))
  {
    if (isset($_POST['searchall']))
      $_SESSION['search_all'] = STRTOUPPER($_POST['searchall']);    
    for($x=1; $x <=10; $x++)
      {
	$cCamp = 'a'.$x;
	if ($x == 1)
	  {
	    if (isset($_POST[$cCamp]))
	      $_SESSION['verProduct'] = true;
	    else
	      {
		$_SESSION['verProduct'] = false;
		$_SESSION['a1'] = false;
	      }
	  }
	if ($x == 2)
	  {
	    if (isset($_POST[$cCamp]))
	      {
		$verCol = 8;
		$_SESSION['numColi'][8] = true;
		$_SESSION['numColi'][7] = false;
	      }
	    else
	      {
		$verCol = 7;
		$_SESSION['a2'] = false;
		$_SESSION['numColi'][7] = true;
		$_SESSION['numColi'][8] = false;
	      }
	      
	  }
	// if ($x == 5)
	//   {
	//     $_SESSION['search_priority'] = true;	      
	//   }
      }
  }
$search_all = $_SESSION['search_all'];
//$search_priority = true;

if (isset($vercol) && $vercol == 8)
  {
    $_SESSION['numColidevid'] = ($_SESSION['numColi'][81]?81:($_SESSION['numColi'][82]?82:($_SESSION['numColi'][83]?83:($_SESSION['numColi'][84]?84:0))));
    $_SESSION['numColi'][71] = false;
    $_SESSION['numColi'][72] = false;
    $_SESSION['numColi'][73] = false;
    $_SESSION['numColi'][81] = false;
    $_SESSION['numColi'][82] = false;
    $_SESSION['numColi'][83] = false;
    $_SESSION['numColi'][84] = false;
    $_SESSION['numColi'][93] = false;
    
  }
if (isset($vercol) && $vercol == 7)
  {
    $_SESSION['numColi'][71] = true; //reform
    $_SESSION['numColi'][72] = false;
    $_SESSION['numColi'][73] = false;    
    $_SESSION['numColi'][81] = true; 
    $_SESSION['numColi'][82] = false;
    $_SESSION['numColi'][83] = false;
    $_SESSION['numColi'][83] = false;
    $_SESSION['numColi'][93] = true;
  }
$_SESSION['numColi'][9] = false;
$_SESSION['numColi'][11] = false;
$_SESSION['numColi'][13] = false;
$aColorUser = $_SESSION['aColorUser'];

if ($_SESSION['numColi'][8] == true)
  $opver = true;
if ($_SESSION['numColi'][7] == true)
  $opver = false;

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
  $action = '';
  $_GET["id"] = $_POST["id"];
}

$nVersion = 0;
$lVersion = false;
$aOf = array();
$aTo = array();
$aOfone = array();
$editref = GETPOST('editref');

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

if (isset($_POST['search_sigla']))
  $_SESSION['isearch_sigla'] = $_POST['search_sigla'];
if (isset($_POST['search_label']))
  $_SESSION['isearch_label'] = $_POST['search_label'];
if (isset($_POST['search_pseudonym']))
  $_SESSION['isearch_pseudonym'] = $_POST['search_pseudonym'];
if (isset($_POST['search_partida']))
  $_SESSION['isearch_partida'] = $_POST['search_partida'];
if (isset($_POST['search_amount']))
  $_SESSION['isearch_amount'] = $_POST['search_amount'];
if (isset($_POST['search_reform']))
  $_SESSION['isearch_reform'] = $_POST['search_reform'];
if (isset($_POST['search_user']))
  {
    if ($_POST['search_user'] < 0)
      $_POST['search_user'] ='';
    $_SESSION['isearch_user'] = STRTOUPPER($_POST['search_user']);
  }

if (isset($_POST['nosearch_x']))
  {
    $_SESSION['isearch_sigla'] = '';
    $_SESSION['isearch_pseudonym'] = '';
    $_SESSION['isearch_label'] = '';
    $_SESSION['isearch_partida'] = '';
    $_SESSION['isearch_amount'] = '';
    $_SESSION['isearch_reform'] = '';
    $_SESSION['isearch_user'] = '';
  }

$search_sigla     = $_SESSION["isearch_sigla"];
$search_label     = $_SESSION["isearch_label"];
$search_pseudonym = $_SESSION["isearch_pseudonym"];
$search_partida   = $_SESSION["isearch_partida"];
$search_amount    = $_SESSION["isearch_amount"];
$search_reform    = $_SESSION["isearch_reform"];
$search_user      = $_SESSION["isearch_user"];
// $search_login = '';
// if ($search_user)
//   {
//     //cambiamos a modo texto
//     $objuser->fetch($search_user);
//     $search_login = STRTOUPPER($objuser->login);
//   }
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($_GET['top']))
  $_SESSION['arrayPoa'] = array();
if ($_GET['top'] == 1)
  $_SESSION['filterrowid'] = $_GET['id'];

$sql  = "SELECT p.rowid AS id, p.period_year, p.fk_structure, p.label, p.pseudonym, p.partida, p.amount, p.classification, p.source_verification, p.unit,p.statut, ";
$sql.= " p.weighting, p.version, ";
$sql.= " p.m_jan, p.m_feb, p.m_mar, p.m_apr, p.m_may, p.m_jun, p.m_jul, p.m_aug, p.m_sep, p.m_oct, p.m_nov, p.m_dec, ";
$sql.= " p.p_jan, p.p_feb, p.p_mar, p.p_apr, p.p_may, p.p_jun, p.p_jul, p.p_aug, p.p_sep, p.p_oct, p.p_nov, p.p_dec, ";
$sql.= " s.label AS labelstructure, s.sigla ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON p.fk_structure = s.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.period_year = ".$period_year;

if ($_SESSION['sel_area'])
  $sql.= " AND s.fk_area = ".$_SESSION['sel_area'];
$sql.= " AND p.statut = 1 "; //solo los aprobados

/////////////////////////////////// revisar el filtro

// if ($search_sigla)   $sql .= " AND s.sigla LIKE '%".$db->escape($search_sigla)."%'";
// if ($search_label)   $sql .= " AND p.label LIKE '%".$db->escape($search_label)."%'";
// if ($search_pseudonym)   $sql .= " AND p.pseudonym LIKE '%".$db->escape($search_pseudonym)."%'";
// if ($search_partida)   $sql .= " AND p.partida LIKE '%".$db->escape($search_partida)."%'";
// if ($search_amount)   $sql .= " AND p.amount LIKE '%".$db->escape($search_amount)."%'";
// if ($idsArea)
//   $sql.= " AND s.fk_area IN ($idsArea)";
// if ($sall)
// {
//     $sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";
// }
////////////////////////////////////
//order
$sql.= " ORDER BY p.version, s.sigla, p.partida, p.rowid ";
//$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($limit+1, $offset);
$result = $db->query($sql);

$form=new Form($db);

$numCol = $_SESSION['numColi'];
if ($result)
  {
    $num = $db->num_rows($result);
    //recuperamos las partidas de la period_year
    $aPartida = get_partida($period_year);
    //verificamos que version de reformulado existe
    // $objrefo->fetch_version($period_year);
    // if ($objrefo->period_year == $period_year)
    //   {
    // 	$lVersion = true;
    // 	$nVersion = $objrefo->version;
    // 	$fk_poa_ref = $objrefo->id;
    // 	$aReform[$objrefo->id] = $objrefo->id;
    // 	list($aOf,$aOfone,$aOfref) = $objrefodet->get_sumaref($aReform);
    //   }
    // else
    //   {
    // 	//buscamos el numero de la nueva reformulacion
    // 	$objap = new Poareformulated($db);
    // 	$objap->fetch_version($period_year,1);
    // 	if ($objap->period_year == $period_year)
    // 	  $nVersion = $objap->version + 1;
    // 	else
    // 	  $nVersion = 1;
    //   }
    //obtenemos las modificaciones aprobadas
    //$objrefo->fetch_version_period_year($period_year);
    $aReform = array();
    // foreach ((array) $objrefo->array AS $fkid => $obj_ref)
    //   {
    // 	if ($obj_ref->period_year == $period_year)
    // 	  {
    // 	    $lVersionAp = true;
    // 	    $nVersionAp = $obj_ref->version;
    // 	    //$fk_poa_ref = $obj_ref->id;
    // 	    $aReform[$obj_ref->id] = $obj_ref->id;
    // 	  }
    //   }
    if (count($aReform)>0)
      list($aOfa,$aOfonea,$aOfrefa) = $objrefodet->get_sumaref($aReform);

    $i = 0;
    $aArrcss= array('poa/css/style.css','poa/css/title.css');
    $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js');
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    //    llxHeader("",$langs->trans("Liste POA"),$help_url,'','','',$aArrjs,$aArrcss);
    
    //print_barre_liste($langs->trans("Liste POA"), $page, $_SERVER['PHP_SELF'], "", $sortfield, $sortorder,'',$num);
    
    //filtro
    $idTagps = 1;
    $idTagps2 = 2;
    //mostrar ocultar menu de seleccion
    // print '<div>';
    // print  '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
    // include DOL_DOCUMENT_ROOT.'/poa/lib/menupoa.lib.php';
    // print '</span>';
      
    // print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_five('.$idTagps.' , '.$idTagps2.')">';

    // print '<a href="#" title="'.$cWork.'"><div style="width:25px; height:24px;">'.$langs->trans('Options').' '.$langs->trans('Show').'/'.$langs->trans('Hide').'</div></a>';
    // print '</span>';
    // print '</div>';
    // print '<span><a href="#" onclick="muestra_oculta('."'".'menu_mostrar'."'".')" title="">'.$langs->trans('Options').' '.$langs->trans('Show').'/'.$langs->trans('Hide').'</a></span>';

    // print '<div id="menu_mostrar" style="display:none;">';
    // include DOL_DOCUMENT_ROOT.'/poa/lib/menupoa.lib.php';
    // print '</div>';

    print '<div class="masteri">'; //master

    //fin menu seleccion
    print '<form name="fo3" method="POST" id="fo3" onsubmit="enviareform(); return false" action="'.$_SERVER["PHP_SELF"].'">'."\n";
    print '<input type="hidden" name="period_year" value="'.$period_year.'">';

    //modificado frame
?>

      
<script type="text/javascript">
   function CambiarURLFrame(id,fk_structure,fk_poa_poa,partida,period_year,idReg,reform){
      var idTwo = parseInt(idReg)*100000;
      var idOne = idReg;
      var inputs = getElement(idReg+"_am");
      var amount = inputs.value;
      var inputsb = getElement(idReg+"_ap");
      var valAnt  = inputsb.value;
      var sumaRef = document.getElementById('totrefo').value;
      var sumaTot = 0;
      //alert(sumaRef);
      if (amount == '')
	{
	  alert("monto vacio");
	}
      //recuperando total
      sumaTot = parseFloat(sumaRef) -parseFloat(valAnt) + parseFloat(amount);
      //alert(sumaTot);
      //asignando nuevo valor
      document.getElementById('totref').innerHTML = sumaTot;
      document.getElementById(idTwo).innerHTML = amount;
      document.getElementById(idTwo+'_').innerHTML = reform;
      //cambiando el estado de
      visual_one(idTwo,idOne);
  document.getElementById('iframe').src= 'actualiza_reform.php?id='+id+'&fk_structure='+fk_structure+'&fk_poa_poa='+fk_poa_poa+'&partida='+partida+'&action=create&period_year='+period_year+'&amount='+amount+'&reform='+reform;
}
</script>
<iframe id="iframe" src="actualiza_reform.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
    function CambiarURLFrametwo(id,idReg,pseudonym){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_poaa");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = pseudonym;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframetwo').src= 'actualiza_poa.php?action=update&id='+id+'&pseudonym='+pseudonym;
}
</script>
<iframe id="iframetwo" src="actualiza_poa.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
    function CambiarURLFramew(id,idReg,n,ctx){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_act");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = ctx;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframew').src= 'work.php?action=add&id='+id+'&ctx='+ctx+'&n='+n;
}
</script>
<iframe id="iframew" src="work.php" width="0" height="0" frameborder="0"></iframe>
    
<script type="text/javascript">
    function CambiarURLFramep(id,idReg,ctx){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_act");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = ctx;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframep').src= './poa/priority.php?action=add&id='+id+'&ctx='+ctx;
}
</script>
<iframe id="iframep" src="priority.php" width="0" height="0" frameborder="0"></iframe>

<?php
    //inicio
    ///////print '<div class="master">'; //master
    print '<section id="section-head">';

    //init head 1 buttons
    print '<span>';
    include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/head1.tpl.php';
    print '</span>';

    //nueva fila TITULOS head2
    print '<span>';
    include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/head2.tpl.php';
    print '</span>';

    //nueva fila filtros head3
    print '<span>';
    include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/head3.tpl.php';    
    print '</span>';

    print '</section>';
    
    //cuerpo
    print '<section id="section-bodyi">';
    print '<aside id="aside-bodyi">';
    if ($num)
      {
    	$var=True;
	//totales
	$sumaPresup = 0;
	$sumaRef1 = 0;
	$sumaPrev = 0;
	$sumaComp = 0;
	$sumaDeve = 0;
	$sumaPaid = 0;
	$sumaAprob = 0;
	$sumaPend = 0;
	$sumaSaldop = 0;
	$sumaSaldoc = 0;
	$sumaSaldod = 0;
	$i=0;
	$ii=0;
	$aHtml = array();
	$aTotalSAct = array();
	
    	while ($i < min($num,$limit))
    	  {
	    $lisPrev = '';
    	    $obj = $db->fetch_object($result);
	    $lContinue = true; //defecto ver todo

	    // //verificamos el filtro
	    // if ($search_sigla)
	    //   {
	    // 	$filterpri = STRPOS(STRTOUPPER($obj->sigla),$search_sigla);
	    // 	if ($filteruser===false && !$lContinue)
	    // 	  $lContinue = false;
	    //   }
	    // if ($search_label)
	    //   {
	    // 	$filterpri = STRPOS(STRTOUPPER($obj->label),$search_label);
	    // 	if ($filteruser===false && !$lContinue)
	    // 	  $lContinue = false;
	    //   }
	    // if ($search_pseudonym)
	    //   {
	    // 	$filterpri = STRPOS(STRTOUPPER($obj->pseudonym),$search_pseudonum);
	    // 	if ($filteruser===false && !$lContinue)
	    // 	  $lContinue = false;
	    //   }
	    // if ($search_partida)
	    //   {
	    // 	$filterpri = STRPOS(STRTOUPPER($obj->partida),$search_partida);
	    // 	if ($filteruser===false && !$lContinue)
	    // 	  $lContinue = false;
	    //   }

	    
	    $ii++;
	    //echo '<hr>'.$lPriority.' '.$sel_priority;
	    if ($action == 'edit' && $obj->id == $id)
	      $object = $obj;
	    //verifica usuario activo
	    //$newNombre = user_active_poa($obj); //poa.lib.php
	    $idUser     = userid_active_poa($obj); //poa.lib.php
	    $newNombre  = '';
	    $nombre     = '';
	    $nombreslog = '';
	    $filteruser = false;
	    $lViewact   = false; //ver actividades
	    
	    // if ($idUser && ($objuser->fetch($idUser) > 0))
	    //   {
	    // 	$newNombre = $objuser->login;
	    // 	$nombre = $objuser->firstname;
	    // 	$nombreslog = $objuser->firstname.' '.$objuser->lastname.' '.$objuser->login;
	    // 	if (!empty($search_user))
	    // 	  $filteruser = STRPOS(STRTOUPPER($nombreslog),$search_user);
	    //   }
	    // if ($_SESSION['search_all'])
	    //   $filteruserall = STRPOS(STRTOUPPER($nombreslog),$_SESSION['search_all']);
	    // //filtro de usuario para la meta
	    // if ((!empty($search_reform) && $search_reform != $aOfref[$obj->fk_structure][$obj->id][$obj->partida])
	    // 	|| (!empty($search_user) && $filteruser===false)
	    // 	|| (!empty($_SESSION['search_all']) && $filteruserall === false))
	    //   {
	    // 	$lContinue = false;
	    //   }
	    
	    //recuperamos las actividades
	    $objactl = new Poaactivity($db);
	    $objactl->getlist_poa($obj->id,0);
	    if (empty($objactl->array) && $search_activity)
	      $lContinue = false;
	    // foreach ((array) $objactl->array AS $objppl)
	    //   {
	    // 	//buscamos el usuario si existe filtro
	    // 	if ($objppl->fk_user_create &&
	    // 	    ($objuser->fetch($objppl->fk_user_create) > 0))
	    // 	  {
	    // 	    $newNombre_ = $objuser->login;
	    // 	    $nombre_ = $objuser->firstname;
	    // 	    $nombreslog_ = $objuser->firstname.' '.$objuser->lastname.' '.$objuser->login;
	    // 	    if (!empty($search_user))
	    // 	      $filteruser = STRPOS(STRTOUPPER($nombreslog_),$search_user);
	    // 	    if (!empty($_SESSION['search_all']))
	    // 	      $filteruserall = STRPOS(STRTOUPPER($nombreslog_),$_SESSION['search_all']);
	    // 	  }
	    // 	//    echo '<hr><hr>res '.$nombreslog_.' '.$search_user.' |'.$filteruser.'|lView '.$lViewprev.'|';		
	    // 	if (!empty($search_user))
	    // 	  if ($lViewprev == false)
	    // 	    if ($filteruser===false)
	    // 	      $lViewprev = false;
	    // 	    else
	    // 	      $lViewprev = true;
	    // 	if (!empty($_SESSION['search_all']))
	    // 	  if ($lViewprev == false)
	    // 	    if ($filteruserall===false)
	    // 	      {
	    // 		$lViewprev = false;
	    // 	      }
	    // 	    else
	    // 	      {
	    // 		$lViewprev = true;
	    // 		$lContinue = true;
	    // 	      }
	    // 	//filtramos la prioridad
	    // 	//if ($lPriority)
	    // 	if ($sel_priority == -1)
	    // 	  {
	    // 	    $lViewprev = true;
	    // 	    if (!$lPriority)
	    // 	      $lPriority = true;
	    // 	  }
	    // 	elseif ($objppl->priority == $sel_priority)
	    // 	  {
	    // 	    $lPriority = true;
	    // 	    $lViewprev = true;
	    // 	  }
	    // 	else
	    // 	  {
	    // 	    $lPriority = false;
	    // 	    $lViewprev = false;
	    // 	    $lContinue = false;
	    // 	  }
	    //   }
	    // if ($lViewprev)
	    //   $lContinue = true;
	  //   $ii--;
	  // }

	    
	    //     if (!empty($search_user))
	    //   $lViewact = true;
	    //filtramos el label
	    //echo '<hr>'.$lContinue.'| '.$lViewact.'| '.$lViewprev.' '.$obj->label;
	    $lViewact = true;
	    // if (!empty($_SESSION['search_all']))
	    //   {
	    // 	$filteruserall = STRPOS(STRTOUPPER($obj->label),$_SESSION['search_all']);
	    // 	if ($filteruserall === false && ($lContinue == false && $lViewact == false))
	    // 	  $lContinue = false;
	    // 	else
	    // 	  $lContinue = true;
	    //   }
	    if ($lContinue === true)
	      {
		$var=!$var;
		if ($var) $backg = "pair"; else $backg = "impair";
		$newClase = ' class="left '.$backg.' ';
		$newClase__ = ' class="left ';
		$lStyle = false;
		if ($_SESSION['colorUser'] == true)
		  {
		    if (empty($aColorUser[$idUser]))
		      {
			$aColorUser[$idUser] = randomColor();
			$_SESSION['aColorUser'] = $aColorUser;
		      }
		    $newClase = 'class="left" style="background-color:'.$aColorUser[$idUser].';';
		    $lStyle = true;
		  }
		if ($_SESSION['colorPartida'] == true)
		  {
		    if (empty($aColorUser[$obj->partida]))
		      {
			$aColorUser[$obj->partida] = randomColor();
			$_SESSION['aColorUser'] = $aColorUser;
		      }
		    $newClase = 'class="left" style="background-color:'.$aColorUser[$obj->partida].';';
		    $lStyle = true;
		  }
		//inicio de la fila
		/////////////////////////////////////////////////
		// print '<div class="height36">';  //div inicio
		// print '<div id="meta" '.$newClase.'">'.'<a href="#" title="'.$obj->labelstructure.'">'.$obj->sigla.'</a>'.'</div>';
		$aHtml[$i]['sigla'] = $obj->sigla;
		if ($numCol[1])
		  {
		    // print '<div id="label" '.$newClase.'">';
		    // print (strlen($obj->label)>60?'<a href="#" title="'.$obj->pseudonym.'">'.substr($obj->label,0,60).'...</a>':$obj->label);
		    // print '</div>';
		  }
		$aHtml[$i]['label'] = $obj->label;
		$aHtml[$i]['pseudonym'] = $obj->pseudonym;
		// if ($numCol[2])
		//   {
		//     //		    print '<div id="pseudo" '.$newClase.'">';
		//     $idTagps = $obj->id+100000;
		//     $idTagps2 = $idTagps+100500;
		//     if ($user->rights->poa->poa->mod || $user->admin)
		//       {
		// 	// print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_poaa" type="text" name="pseudonym" value="'.$obj->pseudonym.'" onblur="CambiarURLFrametwo('.$obj->id.','.$idTagps.','.'this.value);" size="36">'.'</span>';
			
		// 	// print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps.' , '.$idTagps2.')">';
		// 	// print (strlen(trim($obj->pseudonym))>60?'<a href="#" title="'.$obj->label.'">'.substr(trim($obj->pseudonym),0,60).'..xx.</a>':'<a href="#" title="'.$obj->label.'">'.trim($obj->pseudonym).'</a>');
		// 	// print '</span>';
		//       }
		//     else
		//       {
		// 	// print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
		// 	// print (strlen(trim($obj->pseudonym))>60?'<a href="#" title="'.$obj->label.'">'.substr(trim($obj->pseudonym),0,60).'..xx.</a>':'<a href="#" title="'.$obj->label.'">'.trim($obj->pseudonym).'</a>');		    
		// 	// print '</span>';
		//       }
		    
		//     //		print (strlen($obj->pseudonym)>30?'<a href="#" title="'.$obj->pseudonym.'">'.substr($obj->pseudonym,0,30).'...</a>':$obj->pseudonym);
		//     print '</div>';
		//   }
		$aHtml[$i]['partida'] = $obj->partida;
		// print '<div id="partida" '.$newClase.'">'.'<a href="#" title="'.$aPartida[$obj->partida].'">'.$obj->partida.'</a>'.'</div>';
		
		//presupuesto
		$nPresup = 0;
		if ($obj->version == 0)
		  {
		    $aHtml[$i]['presupuesto'] = $obj->amount;
		    $sumaPresup+=$obj->amount;
		    $nPresup = $obj->amount;
		  }


		//armamos la lista de actividades planificadas
		$lisPrev = '';
		////////////////////////////
		//armamos la lista de preventivos
		$newClase2 = $newClase;
		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/lisprev.tpl.php';
		//recuperando el estilo
		$newClase = $newClase2;
		//fin lista preventivos
		///////////////////////////
		//presupuesto budget
		//		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/budget.tpl.php';
		//////////////////////////

		///////////////////////////////
		//reform
		//		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/reform.tpl.php';
		///////////////////////////////

		////////////////////////////

		////////////////////////////
		//preventivo
		$total = 0;
		$balance=0;
		//		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/prev.tpl.php';
		$sumaPrev+=$total;
		$sumaPrevm+= $objpre->total;
		$sumaSaldop+=$balance;
		//fin preventivo
		////////////////////////////
		//comprometido
		$totalc = 0;
		$balancec = 0;
		//		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/comp.tpl.php';
		$sumaComp+=$totalc;
		$sumaCompm+=$objcompp->total;
		$sumaSaldoc+= $balancec;
		///fin comprometido
		////////////////////////////
		//devengado
		$totald = 0;
		$balanced = 0;
		//		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/dev.tpl.php';
		$sumaDeve+=$totald;
		$sumaDevem+=$objdeve->total;
		$sumaSaldod+= $balanced;
		////fun devengado
		////////////////////////////
		
		
		
		
		////////////////////////////
		//lista calendario
		//		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/calendar.tpl.php';		

		//user	    
		if ($objectuser->id > 0 && $obj->id == $objectuser->fk_poa_poa)
		  {
		    $aHtml[$i]['user'] = $newNombre;
		    
		    // print '<div id="user" '.$newClase.'"><a href="fiche_user.php?idp='.$obj->id.'&id='.$objectuser->id.'" title="'.$nombre.'">'.$newNombre.'</a></div>';
		  }
		else
		  {
		    // print '<div id="user" '.$newClase.'">';
		    // if ($user->rights->poa->poa->crear)
		    //   print '<a href="fiche_user.php?idp='.$obj->id.'&action=create">'.img_picto($langs->trans('Create'),'edit_add').'</a>';
		    // else
		    //   print '&nbsp;';
		    // print '</div>';
		  }

		//instruction
		//buscamos la ultima instruccion si existe para el poa seleccionado
		$addClase = ''; 
		$addMessage = '';
		
		//RESULTADOS PAC
		
		$newClase = $newClaseor;
		
		//print '</div>'; //REVISAR RQC
		
		//print '</div>'; //FIN
		//print '<div style="clear:both"></div>';
		if ($lisPrev)
		  {		    
		    if ($lViewact)
		      print '<div id="pre'.$obj->id.'" style="display:block;">';
		    else
		      print '<div id="pre'.$obj->id.'" style="display:none;">';
		    print $lisPrev;
		    print '</div>';
		  }
		// if ($lisPac)
		//   print $lisPac;
		
	      }
    	    $i++;
    	  }//loop 
      }
  
    print '</aside>';
    print '</section>';
    //print '</div>';//master
    //print '<div class="clear"></div>';
    print '</form>';

    print '</div>';//master
    print '<div class="clear"></div>';
    
    $_SESSION['aHtml'] = $aHtml;

    // print '<section id="section-footer">';
    // print '<span>';

    // //totales
    // print '<div id="meta_" class="left total"></div>';
    // if ($numCol[1])
    //   {
    // 	print '<div id="label_" class="left total">';
    // 	print '<span>'.$ii.'</span>';
    // 	print '</div>';
    //   }

    // if ($numCol[2])
    //   {
    // 	print '<div id="pseudo_" class="left total">';
    // 	print '<span>'.$ii.'</span>';
    // 	print '</div>';
    //   }

    // //partida
    // print '<div id="partida_" class="left total"></div>';

    // print '<div id="amount_" class="left total">';
    // if ($numCol[91])
    //   print price($sumaPresup);
    // if ($numCol[92])
    //   print price($sumaAprob);
    
    // print '</div>';


    // if ($numCol[71])
    //   {
    // 	print '<div id="amount_" class="left total">';
    // 	if ($lVersion)
    // 	  {
    // 	    print '<input type="hidden" id="totrefo" value="'.$sumaRef1.'">';
    // 	    print '<span id="totref">'.price($sumaRef1).'</span>';
    // 	    print '</div>';
    // 	    print '<div id="amount" class="left total">';
    // 	    print '&nbsp;';
    // 	  }
    // 	else
    // 	  {
    // 	    print '</div>';
    // 	    print '<div id="amount_" class="left total">';
    // 	    print '&nbsp;';
    // 	  }
    // 	print '</div>';
    //   }

    // if ($numCol[72])
    //   {
    // 	print '<div id="amount_" class="left total">';
    // 	if ($lVersionAp)
    // 	  {
    // 	    print price($sumaAprob);
    // 	  }
    // 	print '</div>';
    //   }
    // if ($numCol[73])
    //   {
    // 	print '<div id="amount_" class="left total">';
    // 	print price($sumaRef1);
    // 	print '</div>';
    //   }
    // print '<div id="amount_" class="left total">';
    // if ($numCol[9])
    //   print price(price2num($sumaPrev,'MT'));
    // if ($numCol[10])
    //   {
    // 	if ($sumaAprob>0)
    // 	  print price(price2num($sumaPrevm/$sumaAprob*100,'MT')).' %';
    // 	else
    // 	  print price(0);
    //   }   
    // if ($numCol[15])
    //   print price(price2num($sumaSaldop,'MT'));
    // print '</div>';

    // print '<div id="amount_" class="left total">';
    // if ($numCol[11])
    //   print price($sumaComp);
    // if ($numCol[12])
    //   {
    // 	if ($sumaAprob>0)
    // 	  print price(price2num($sumaCompm/$sumaAprob*100,'MT')).' %';
    // 	else
    // 	  print price(0);
    //   }
    // if ($numCol[16])
    //   print price(price2num($sumaSaldoc,'MT'));
    // print '</div>';

    // print '<div id="amount_" class="left total">';
    // if ($numCol[13])	
    //   print price($sumaDeve);
    // if ($numCol[14])
    //   {
    // 	if ($sumaAprob>0)
    // 	  print price(price2num($sumaDevem/$sumaAprob*100,'MT')).' %';
    // 	else
    // 	  print price(0);
    //   }
    // if ($numCol[17])
    //   print price(price2num($sumaSaldod,'MT'));
    // print '</div>';

    // if ($opver == true)
    //   {
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    // 	print '<div id="amountone_" class="left total"></div>';
    //   }

    // print '<div id="user_" class="left total">';
    // print '</div>';

    // // print '<div id="instruction" class="left total">';
    // // print '</div>';
    // print '<div id="action_" class="left total">';
    // print '</div>';

    // print '<div class="clear"></div>';
    // print '</span>';
    // //totales de actividades
    
    // // //agregando los totales
    // // $newClase = ' class="left"  style="background-color:#64C2FC;"';
    
    // // $lisPrev= '<span style="color:#000000;">';
    // // $lisPrev.= '<div id="meta" '.$newClase.'>'.'&nbsp;'.'</div>';
    // // if ($numCol[1] || $numCol[2])
    // //   {
    // // 	$lisPrev.= '<div id="pseudo" '.$newClase.'>';
    // // 	$lisPrev.=  $langs->trans('Totalactivity');
    // // 	$lisPrev.=  '</div>';
    // //   }
    // // $lisPrev.= '<div id="partida" '.$newClase.'>&nbsp;</div>'; //partida
    
    // // $lisPrev.= '<div id="amount" '.$newClase.'>'.price(price2num($aTotalSAct['budget'],'MT')).'</div>';//presupuesto
    // // if ($numCol[71])
    // //   {
    // // 	$lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    // // 	$lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    // //   }
    
    // // if ($numCol[72])
    // //   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    // // if ($numCol[73])
    // //   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    
    // // if($numCol[9] || $numCol[10] || $numCol[15])
    // //   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    
    // // //comprometidos	
    // // if ($numCol[11] || $numCol[12] || $numCol[16])
    // //   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    // // //deventados
    // // if ($numCol[13] || $numCol[14] || $numCol[17])
    // //   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
    
    // // //lista el cronograma por mes
    // // if ($opver == 1)
    // //   {
    // // 	for ($d = 1; $d <= 12; $d++)
    // // 	  {
    // // 	    $lisPrev.= '<div id="amountone" class="left '.$newClase.'"> &nbsp;</div>';
    // // 	  }
    // //   }
    
    // // //usuario
    // // $lisPrev.= '<div id="user" '.$newClase.'>&nbsp;</div>';
    
    // // //instruccion
    // // if ($numCol[93])
    // //   {
    // // 	$lisPrev.= '<div id="instruction" '.$newClase.'>';
    // // 	$lisPrev.= '&nbsp;';
    // // 	$lisPrev.= '</div>';
    // //   }
    // // //pac
    // // $lisPrev.= '<div id="amount" '.$newClase.'>';
    // // $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">&nbsp;</a>';
    // // $lisPrev.= '</div>';
    
    // // //action
    // // $lisPrev.= '<div id="action" '.$newClase.'>';
    // // $lisPrev.= '&nbsp;';
    // // $lisPrev.= '</div>';
    
    
    // // //finalizar la lista
    // // $lisPrev.= '</span>';
    // // //$lisPrev.= '<div style="clear:both"></div>';
    // // print $lisPrev;
    // // //fin total actividades
    
    // print '</section>';
    
    $db->free($result);
    
    // print "<div class=\"tabsAction\">\n";
    
    // if ($action == '')
    //   {
    // 	if ($user->rights->poa->poa->crear)
    // 	  print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Createnew").'</a>';
    // 	else
    // 	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
    // 	if ($user->rights->poa->exp)
    // 	  print "<a class=\"butAction\" href=\"fiche_excel.php\">".$langs->trans("Excel")."</a>";
    // 	else
    // 	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Excel")."</a>";
    //   }
    // print '</div>';
  }
 // else
 //   {
 //     dol_print_error($db);
 //   }

//fin desarrollo

print '</div>';
//fin resumen de actividades con prioridad

//Resumen de preventivos con prioridad
//reporte ejecucion presupuestaria
// print '<br>';
// print '<div id="resum-izq">';    //div1
// print '<div class="height20 floatleft" style="background-color:#8d9bac; width:280px;">';
// print '<span>';
// print $langs->trans('Priority');
// print '</span>';
// print '</div>';

// print '<div class="height20 floatleft" style="background-color:#8d9bac; width:28px;">';
// print '<span>';
// print '<a href="'.$_SERVER['PHP_SELF'].'?action='.($action=='view1'?'view':'view1').'">'.img_picto(($action=='view1'?$langs->trans('Relativetime'):$langs->trans('Absolutetime')),DOL_URL_ROOT.'/poa/img/act.png','',1).'</a>';
// print '</span>';
// print '</div>';
// $width = 51;
// if ($action == 'view1')
//   {
//     print '<div class="floatleft">';
//     for ($j = 1; $j <=12; $j++)
//       {
// 	print '<div class="height20 floatleft border1" style="text-align:center; background-color:#8d9bac; width:'.$width.'px;">'.$j.'</div>';
//       }
//     print '</div>';
//   }
//  else
//    {
//      print '<div class="floatleft">';
//      for ($j = 1; $j <=12; $j++)
//        {
// 	 print '<div class="height20 floatleft border2" style="background-color:#8d9bac; width:'.$width.'px;">&nbsp;</div>';
//        }
//      print '</div>';
//    }
// print '<div class="clear"></div>';
// print '<div>';//div2

poa_grafic_color();

//recuperamos de poa_grafic_color
$arrayc = $_SESSION['arrayc'];
$arrayl = $_SESSION['arrayl'];

$arraypri = array();
$var = true;
// foreach ((array) $aPrev AS $fk_poa_prev)
// {
//   if ($objpre->fetch($fk_poa_prev)>0)
//     if ($objpre->id == $fk_poa_prev)
//       {
// 	$var = !$var;
// 	if ($var == true)
// 	  $classcolor = 'colpair';
// 	else
// 	  $classcolor = 'colimpair';
// 	$objwork->fetch_prev($fk_poa_prev);
// 	$arraygraf = poa_grafic($objwork->id);
// 	//	$arraygraf = poa_grafic($fk_poa_prev);
// 	print '<div class="left '.$classcolor.'" style="width:300px;">';
// 	print '<a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$fk_poa_prev.'" title="'.$objpre->nro_preventive.'">'.$objpre->nro_preventive.'</a>';
// 	print '&nbsp;&nbsp;';
// 	print '<a href="'.DOL_URL_ROOT.'/poa/workflow/fiche.php?id='.$objwork->id.'" title="'.$objpre->label.'">'.(!empty($objpre->pseudonym)?$objpre->pseudonym:(strlen($objpre->label)>35?substr($objpre->label,0,35).'...':$objpre->label)).'</a>';
// 	print '</div>';
	
// 	if (count($arraygraf) > 0)
// 	  {
// 	    print '<div class="left">';
// 	    print poa_grafic_executed($arraypri,$arraygraf,$arrayc,$arrayl);
// 	    $arraypri = $_SESSION['arraypri'];
// 	    print '</div>';
// 	  }
	
// 	print '<div class="clear"></div>';
	
//       }
// }
// //resumen label
// print poa_grafic_label($arrayl,$arrayc);
// print '</div>';
// print '</div>';
// //fin resumen de preventivos con prioridad

//ejecucion presupuestaria
//reporte ejecucion presupuestaria
print '<br>';
print '<div id="resum-izq">';    //div1
print '<table class="noborder" style="width:100%;" class="tdblock" >';
print '<tr class="liste_titre">';
print '<td>';
print $langs->trans('Budget execution');
print '</td>';
print '</tr>';
print '</table>';

print '<div>';//div2

//cabecera
print '<div class="divl1 divwidth150">';
print '<table style="width:100%;" class="noborder" >';
print '<tr class="liste_titre">';
print '<td>';
print $langs->trans('Name');
print '</td>';
print '</tr>';
print '</table>';
print '</div>';
print '<div class="divl1 divwidth200 tright">';
print '<table style="width:100%;">';
print '<tr class="liste_titre">';
print '<td width="25%" align="center">';
print img_picto($langs->trans('Budget'),DOL_URL_ROOT.'/poa/img/presu.png','',1);
print '</td>';
print '<td width="25%" align="center">';
print img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre.png','',1);
print '</td>';
print '<td width="25%" align="center">';
print img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/com.png','',1);
print '</td>';
print '<td width="25%" align="center">';
print img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/dev.png','',1);
print '</td>';
print '</tr>';
print '</table>';
print '</div>';
print '<div style="clear:both;"></div>';

foreach((array) $aUser AS $fk_user => $aName)
{
  $sumapres = 0;
  $aData = $aDataPoa[$fk_user];
  print '<div class="divl1 divwidth150">';
  $objuser->fetch($fk_user);
  print $objuser->lastname.' '.$objuser->firstname;
  print '</div>';
  print '<div class="divl1 divwidth200 tcenter">';
  print '<table  style="width:100%;">';
  print '<tr>';

  //grafico presupuesto'
  $rango = 0;
  $rangom = 0;
  if ($maxPresup > 0)
  	$rango = $aData['presup'] / $maxPresup * 100;
  $graf = defgraf($rango);
  if ($maxnPresup > 0)
  	$rangon = $aData['nropresup'] / $maxnPresup * 100;
  $grafn= defgraf($rangon);

  print '<td nowrap width="25%">';
  $rango_ = ceil($rango);
  if (!empty($rango_))
    {
      $title = '';
      $aTotalpres[$fk_user] = $aData['presup'];
      $percent = $aData['presup'] / $sumaPresup * 100;
      $title.= $langs->trans('Amount').' '.price($aData['presup']).' ';
      $title.= ' | '.$langs->trans('Porcent').': '.price(round($percent,2)).' %';
      print '<table style="border:0;width:100%;">';
      print '<tr>';
      print '<td  width="50%">';
      print '<a href="#" title="'.$title.'">'.img_picto($title,DOL_URL_ROOT.'/poa/img/g'.$graf.'.png','',1).'</a>';
      print '</td>';
      print '<td  width="50%">';
      $rangon_ = ceil($rangon);
      if (!empty($rangon_))
	print '<a href="#" title="'.$aData['nropresup'].' '.$langs->trans('Metas').'">'.$aData['nropresup'].'</a>';
      //print '<a href="#" title="'.$aData['nropresup'].'">'.img_picto($aData['nropresup'],DOL_URL_ROOT.'/poa/img/h'.$grafn.'.png','',1).'</a>';
	   
      else
	print '&nbsp;';
      print '</td>';

      print '</tr>';
      print '</table>';
    }
  else
    print '&nbsp;';
  print '</td>';

  //grafico preventivo
  $rango = 0;
  $rangon = 0;
  if ($maxPresup > 0)
	$rango = $aData['prev'] / $maxPresup * 100;
  $graf = defgraf($rango);
  if ($maxnPresup>0)
	$rangon = $aData['nroprev'] / $maxnPre * 100;
  $grafn= defgraf($rangon);
	$percent = 0;
	if ($aTotalpres[$fk_user]>0)
	  $percent = $aData['prev'] / $aTotalpres[$fk_user] * 100;
  $title = $langs->trans('Amount').' '.price($aData['prev']);
  $title.= ' | '.$langs->trans('Porcent').': '.price(round($percent,2));
  print '<td nowrap width="25%">';
  $rango_ = ceil($rango);
  if (!empty($rango_))
    {
      print '<table style="border:0;width:100%;">';
      print '<tr>';
      print '<td width="50%">';
      print '<a href="#" title="'.$title.'">'.img_picto($title,DOL_URL_ROOT.'/poa/img/g'.$graf.'.png','',1).'</a>';
      print '</td>';
      print '<td  width="50%">';
      $rangon_ = ceil($rangon);
      if (!empty($rangon_))
	print '<a href="#" title="'.$aData['nroprev'].'">'.img_picto($aData['nroprev'],DOL_URL_ROOT.'/poa/img/h'.$grafn.'.png','',1).'</a>';
      else
	print '&nbsp;';
      print '</td>';
      print '</tr>';
      print '</table>';
    }
  else
    print '&nbsp;';
  print '</td>';

  //grafico comprometido
  $rango = 0;
  $rangon = 0;
  if ($maxPresup > 0)
	  $rango = $aData['comp'] / $sumaPresup * 100;
  $graf = defgraf($rango);
  if ($maxnPresup>0)
	  $rangon = $aData['nrocomp'] / $maxnPre * 100;
  $grafn= defgraf($rangon);
  $percent = 0;
  if ($aTotalpres[$fk_user]>0)
	  $percent = $aData['comp'] / $aTotalpres[$fk_user] * 100;
  $title = $langs->trans('Amount').' '.price($aData['comp']);
  $title.= ' | '.$langs->trans('Porcent').': '.price(round($percent,2));
  print '<td width="25%">';
  $rango_ = ceil($rango);
  if (!empty($rango_))
    {
      print '<table style="border:0;width:100%;">';
      print '<tr>';
      print '<td width="50%">';
      print '<a href="#" title="'.$title.'">'.img_picto($title,DOL_URL_ROOT.'/poa/img/g'.$graf.'.png','',1).'</a>';
      print '</td>';
      print '<td  width="50%">';
      $rangon_ = ceil($rangon);
      if (!empty($rangon_))
	print '<a href="#" title="'.$aData['nrocomp'].'">'.img_picto($aData['nrocomp'],DOL_URL_ROOT.'/poa/img/h'.$grafn.'.png','',1).'</a>';
      else
	print '&nbsp;';
      print '</td>';
      print '</tr>';
      print '</table>';
    }
  else
    print '&nbsp;';
  print '</td>';

  //grafico devengado
  $rango = 0;
  if ($sumaPresup>0)
	  $rango = $aData['deve'] / $sumaPresup * 100;
  $graf = defgraf($rango);
  $rangon = 0;
  if ($maxnPre>0)
  $rangon = $aData['nrodeve'] / $maxnPre * 100;
  $grafn= defgraf($rangon);
  $percent = 0;
  if ($aTotalpres[$fk_user]>0)
	  $percent = $aData['deve'] / $aTotalpres[$fk_user] * 100;
  $title = $langs->trans('Amount').' '.price($aData['deve']);
  $title.= ' | '.$langs->trans('Porcent').': '.price(round($percent,2));

  print '<td width="25%">';
  $rango_ = ceil($rango);
  if (!empty($rango_))
    {
      print '<table style="border:0;width:100%;">';
      print '<tr>';
      print '<td width="50%">';
      print '<a href="#" title="'.$title.'">'.img_picto($title,DOL_URL_ROOT.'/poa/img/g'.$graf.'.png','',1).'</a>';
      print '</td>';
      print '<td width="50%">';
      $rangon_ = ceil($rangon);
      if (!empty($rangon_))
	print '<a href="#" title="'.$aData['nrodeve'].'">'.img_picto($aData['nrodeve'],DOL_URL_ROOT.'/poa/img/h'.$grafn.'.png','',1).'</a>';
      else
	print '&nbsp;';
      print '</td>';
      print '</tr>';
      print '</table>';
    }
  else
    print '&nbsp;';
  print '</td>';
  print '</tr>';
  print '</table>';
  print '</div>';
  print '<div style="clear:both;"></div>';
  
}

print '</div>';
print '</div>';

//fin ejecucion presupuestaria

//resumen por tiempo
$seq = count($aColors);

print '<div id="resum-izq">';    //div1

//cabecera
print '<div class="divl1 divwidth150">';
print '<table class="noborder" style="width:100%;" class="tdblock" >';
print '<tr class="liste_titre">';
print '<td>';
print $langs->trans('Name');
print '</td>';
print '</tr>';
print '</table>';
print '</div>';

print '<div class="divl1 divwidth340 tright">';
print '<table class="noborder" style="width:100%;" class="tdblock" >';
print '<tr class="liste_titre">';
print '<td>';
print $langs->trans('Tiempos procesamiento');
print '</td>';
print '</tr>';
print '</table>';
print '</div>';

print '<div style="clear:both;"></div>';

//segunda cabecera
print '<div class="divl1 divwidth150">';
print '&nbsp;';
print '</div>';

print '<div class="divl1 divwidth340 tright">';
$min = 0;
foreach ((array) $aColors AS $nD1 => $cColor)
    {
      $x++;
      print '<div id="fondoi" class="tcenter" style="background-color:#'.$cColor.'">';
      if ($x == $seq)
	$cText =  '+'.$nD1;
      else
	// if ($nD1 == 0)
	//   $cText =  $nD1;
	// else
	  $cText =  $min .'-'.$nD1;

      print $cText;
      print '</div>';

      $min = $nD1+1;
    }
//para cerrados
print '<div id="fondoi" class="tcenter" style="background-color:#a1a1db;">';
print $langs->trans('End');
print '</div>';

print '</div>';
print '<div style="clear:both;"></div>';

print '<div>'; //div2
foreach((array) $aUser AS $fk_user => $aUsers)
{
  $carea = '';
  $name = $aUsers['name'];
  $login = $aUsers['login'];
  $aCarea = $aUsers['carea'];
  if (count($aCarea)>1)
    $carea = '';
  else
    $carea = $aCarea;
  $sumapres = 0;
  print '<div class="divl1 divwidth150">';
  print $name;
  print '</div>';
  $aRetraso    = $aDataPoa[$fk_user]['retraso'];
  $aRetrasoall = $aDataPoa[$fk_user]['retrasoall'];
  $cerrado = $aDataPoa[$fk_user]['cerrado'];

  print '<div class="divl1 divwidth340 tright">';
  //grafico retraso individual
  $min = 0;
  $x=0;

  //reporte
  foreach ((array) $aColors AS $nD1 => $cColor)
    {
      $x++;
      print '<div id="fondoi" style="background-color:#'.$cColor.';">';
      if ($x == $seq)
	$cText =  $langs->trans('Mayor o igual a').' '.$nD1.' '.$langs->trans('Days');
      else
	if ($nD1 == 0)
	  $cText =  $nD1.' '.$langs->trans('Days');
	else
	  $cText =  $min .' '.$langs->trans('To').' '.$nD1.' '.$langs->trans('Days');

      print '<table class="noborder tdblock" style="width:100%;" >';
      print '<tr>';
      print '<td align="center">';
      print '<a style="color:#'.$aColorr[$aColorsid[$nD1]].';" href="execution/liste.php?filterw=n'.$nD1.'&search_login='.$login.(!empty($carea)?'&search_area='.$carea:'').'" title="'.$cText.'">';
      print $aRetraso[$nD1];
      print '</a>';
      print '</td>';
      print '</tr>';
      print '</table>';
      print '</div>';

      $min = $nD1+1;
    }

  //cerrado
  print '<div id="fondoi" class="tcenter" style="background-color:#a1a1db;">';
  if (!empty($cerrado))
    {
      print '<a style="color:#000;" href="execution/liste.php?filterw=n0&amp;search_login='.$login.'" title="'.$langs->trans('Close').'">';
      print $cerrado;
      print '</a>';
    }
  //    print $cerrado;
  else
    print '&nbsp;';

  print '</div>';
  print '</div>';

  print '<div style="clear:both;"></div>';

}

print '</div>';
print '</div>';


//////////////por dpto
//
//procesando para el reporte

//resumen por dpto
$seq = count($aColors);

print '<div id="resum-izq">';    //div1

//cabecera
print '<div class="divl1 divwidth150">';
print '<table class="noborder" style="width:100%;" class="tdblock" >';
print '<tr class="liste_titre">';
print '<td>';
print $langs->trans('Area');
print '</td>';
print '</tr>';
print '</table>';
print '</div>';

print '<div class="divl1 divwidth340 tright">';
print '<table class="noborder" style="width:100%;" class="tdblock" >';
print '<tr class="liste_titre">';
print '<td>';
print $langs->trans('Tiempos procesamiento');
print '</td>';
print '</tr>';
print '</table>';
print '</div>';
print '<div style="clear:both;"></div>';

//segunda cabecera
print '<div class="divl1 divwidth150">';
print '</div>';

print '<div class="divl1 divwidth340 tright">';
$min = 0;
$x = 0;
foreach ((array) $aColors AS $nD1 => $cColor)
    {
      $x++;
      print '<div id="fondoi" class="tcenter" style="background-color:#'.$cColor.'">';
      if ($x == $seq)
	$cText =  '+'.$nD1;
      else
	$cText =  $min .'-'.$nD1;

      print $cText;
      print '</div>';

      $min = $nD1+1;
    }
//para cerrados
print '<div id="fondoi" class="tcenter" style="background-color:#a1a1db;">';
print $langs->trans('End');
print '</div>';

print '</div>';
print '<div style="clear:both;"></div>';


print '<div>'; //div2
$aWorkarea = $aDataPoa['retrasoarea'];
foreach((array) $aWorkarea AS $carea => $aData)
{
  $name = $carea;
  $sumapres = 0;
  print '<div class="divl1 divwidth150">';
  if ($objarea->fetch('',$name)>0)
    if ($objarea->ref == $name)
      print '<a href="#" title="'.$objarea->label.'">'.$langs->trans($name).'</a>';
    else
      print $langs->trans($name);
  else
    print '&nbsp;';
  print '</div>';

  print '<div class="divl1 divwidth340 tright">';
  //grafico retraso individual
  $min = 0;
  $x=0;

  //reporte
  foreach ((array) $aColors AS $nD1 => $cColor)
    {
      $x++;
      print '<div id="fondoi" style="background-color:#'.$cColor.';">';
      if ($x == $seq)
	$cText =  $langs->trans('Mayor o igual a').' '.$nD1.' '.$langs->trans('Days');
      else
	if ($nD1 == 0)
	  $cText =  $nD1.' '.$langs->trans('Days');
	else
	  $cText =  $min .' '.$langs->trans('To').' '.$nD1.' '.$langs->trans('Days');

      print '<table class="noborder tdblock" style="width:100%;" >';
      print '<tr>';
      print '<td align="center">';
      print '<a style="color:#'.$aColorr[$aColorsid[$nD1]].';" href="execution/liste.php?filterw=n'.$nD1.'&amp;search_area='.$carea.'" title="'.$cText.'">';
      print $aData[$nD1];
      print '</a>';
      print '</td>';
      print '</tr>';
      print '</table>';
      print '</div>';

      $min = $nD1+1;
    }
  //cerrado
  $cerrado = $aDataPoa['cerradoarea'][$carea];
  print '<div id="fondoi" class="tcenter" style="background-color:#a1a1db;">';
  if (!empty($cerrado))
    {
      print '<a style="color:#000;" href="execution/liste.php?filterw=n0&amp;search_area='.$carea.'" title="'.$cText.'">';
      print $aDataPoa['cerradoarea'][$carea];
      print '</a>';
    }
  //    print $cerrado;
  else
    print '&nbsp;';
  print '</div>';
  print '</div>';

  print '<div style="clear:both;"></div>';

}

print '</div>';
print '</div>';
///////////// fin por dpto


/*
 * Statistics area
 */
 print '<div style="clear:both;"></div>';
if ($xyz)
  {
print '<br>';
print '<div id="resum-izq">';    
$rowspan=2;
$third = array('pres' => 0,
	       'prev' => 0,
	       'comp' => 0,
	       'deve' => 0);
$partial = array('pres' => 0,
		 'prev' => 0,
		 'comp' => 0,
		 'deve' => 0);
$total=0;

$sql = "SELECT s.rowid AS id , s.fk_structure, s.partida, s.version, s.statut, s.amount";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa AS s";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS ps ON s.fk_structure = ps.rowid";

if (!empty($search_login))
  $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."poa_poa_user AS t ON t.fk_poa_poa = s.rowid";
$sql.= ' WHERE s.entity IN ('.getEntity('poa_prev', 1).')';
$sql.= " AND s.period_year = ".$period_year;
if (!empty($search_login) && $search_login > 0)
  {
    $sql.= " AND t.active = 1";
    $sql.= " AND t.fk_user = ".$search_login;
  }
if ($sel_area > 0)
  $sql.= " AND ps.fk_area = ".$sel_area;
$result = $db->query($sql);
if ($result)
  {
    while ($objpoa = $db->fetch_object($result))
      {
	$found=0;
	//buscamos el preventivo, comprometido, devengado
	$objprev->getsum_str_part($period_year,$objpoa->fk_structure,$objpoa->id,$objpoa->partida);
	$objprev->total;
	$partial['prev']+=$objprev->total;
	$objcomp->getsum_str_part($period_year,$objpoa->fk_structure,$objpoa->id,$objpoa->partida);
	$partial['comp']+=$objcomp->total;
	$objdev->getsum_str_part($period_year,$objpoa->fk_structure,$objpoa->id,$objpoa->partida);
	$partial['deve']+=$objdev->total;

	if ($objpoa->version == 0)
	  {
	    $partial['pres']+=$objpoa->amount;
	    $total+=$objpoa->amount;
	  }
      }
  }
 else 
   dol_print_error($db);

//resumen de la sumatoria
$third['pres'] = $partial['pres']-$partial['prev'];
$third['prev'] = $partial['prev']-$partial['comp'];
$third['comp'] = $partial['comp']-$partial['deve'];
$third['deve'] = $partial['deve'];
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistics").'</th></tr>';
if (! empty($conf->use_javascript_ajax) && 
    ((round($third['pres'])?1:0)+(round($third['prev'])?1:0)+(round($third['comp'])?1:0)+(round($third['deve'])?1:0)+(round($third['paid'])?1:0)))
  {
    print '<tr><td align="center">';
    // $dataseries=array();
    // $dataseries[]=array('label'=>$langs->trans("Budget"),'data'=>round($third['pres']));
    $dataseries[]=array('label'=>$langs->trans("Unused"),
			'data'=>round($third['pres']/$total * 100));
    $dataseries[]=array('label'=>$langs->trans("Preventive"),
			'data'=>round($third['prev']/$total * 100));
    $dataseries[]=array('label'=>$langs->trans("Committed"),
			'data'=>round($third['comp']/$total * 100));
    $dataseries[]=array('label'=>$langs->trans("Accrued"),
			'data'=>round($third['deve']/$total * 100));
    // $dataseries[]=array('label'=>$langs->trans("Paid"),'data'=>round($third['paid']));
    
    $data=array('series'=>$dataseries);
    dol_print_graph('stats',300,180,$data,1,'pie',0);
    print '</td></tr>';
  }
 else
   {
     $statstring = "<tr $bc[0]>";
     $statstring.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Unused").'</a></td><td align="right">'.round($third['pres']).'</td>';
     $statstring.= "</tr>";

     $statstring = "<tr $bc[0]>";
     $statstring.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Preventive").'</a></td><td align="right">'.round($third['prev']).'</td>';
     $statstring.= "</tr>";

     $statstring.= "<tr $bc[1]>";
     $statstring.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Committed").'</a></td><td align="right">'.round($third['comp']).'</td>';
     $statstring.= "</tr>";

     $statstring2 = "<tr $bc[0]>";
     $statstring2.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Accrued").'</a></td><td align="right">'.round($third['deve']).'</td>';
     $statstring2.= "</tr>";

     $statstring2 = "<tr $bc[0]>";
     $statstring2.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Paid").'</a></td><td align="right">'.round($third['paid']).'</td>';
     $statstring2.= "</tr>";

    print $statstring;
    print $statstring2;
}
print '<tr class="liste_total"><td>'.$langs->trans("Total POA").'</td><td align="right">';
print number_format(price2num($total,'MT'),2);
print '</td></tr>';
print '</table>';

print '</div>';
  }
//fin estadistica left


print '</div>';

llxFooter();

$db->close();

?>
