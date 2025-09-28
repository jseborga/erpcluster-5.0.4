<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/poa/poa/index.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page index des poa
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");

if ($conf->poai->enabled)
  {
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
  }

$langs->load("poa@poa");

if (!$user->rights->poa->prev->leer)
  accessforbidden();

$object = new Poaprev($db);
$objproc = new Poaprocess($db);
$objuser = new User($db);
if ($conf->poai->enabled)
  {
    $objinst = new Poaiinstruction($db);
    $objmoni = new Poaimonitoring($db);
  }

//asignando filtro de usuario
assign_filter_user('psearch_user');

$id = GETPOST('id');
$idp = GETPOST('idp');
$action = GETPOST('action');
if (isset($_GET['nopac']))
  $_SESSION['idPac'] = '';
if (isset($_GET['idpa']))
  $_SESSION['idPac'] = $_GET['idpa'];
$idpa = $_SESSION['idPac'];


//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];

//gestion definidia en index
$gestion = $_SESSION['gestion'];
if (isset($_POST['search_gestion']))
  $_SESSION['psearch_gestion'] = $_POST['search_gestion'];
if (isset($_POST['search_label']))
  $_SESSION['psearch_label'] = $_POST['search_label'];
if (isset($_POST['search_nro']))
  $_SESSION['psearch_nro'] = $_POST['search_nro'];
if (isset($_POST['search_statut']))
  $_SESSION['psearch_statut'] = $_POST['search_statut'];
if (isset($_POST['search_amount']))
  $_SESSION['psearch_amount'] = $_POST['search_amount'];
if (isset($_POST['search_user']))
  $_SESSION['psearch_user'] = STRTOUPPER($_POST['search_user']);

if (isset($_POST['nosearch_x']))
  {
    $_SESSION["psearch_gestion"] = '';
    $_SESSION["psearch_label"] = '';
    $_SESSION["psearch_nro"] = '';
    $_SESSION["psearch_statut"] = '';
    $_SESSION["psearch_amount"] = '';
    $_SESSION["psearch_user"] = '';
  }

$search_gestion   = $_SESSION["psearch_gestion"];
$search_label     = $_SESSION["psearch_label"];
$search_nro       = $_SESSION["psearch_nro"];
$search_statut    = $_SESSION["psearch_statut"];
$search_amount    = $_SESSION["psearch_amount"];
$search_user      = $_SESSION["psearch_user"];


$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.nro_preventive";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($_GET['top']))
  $_SESSION['arrayPoa'] = array();
if ($_GET['top'] == 1)
  $_SESSION['filterrowid'] = $_GET['id'];
$filter = '';


// if (!$user->admin)
//   $filter = " AND p.fk_user_create = ".$user->id;

$sql = "SELECT p.rowid AS id, p.gestion, p.fk_pac, p.label, p.nro_preventive, p.date_preventive, p.statut, p.active, p.fk_user_create, ";
$sql.= " p.amount, p.date_create, p.fk_user_create ";
if ($idp)
  $sql.= ", r.partida, r.amount AS amountpartida ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as p ";
if ($idp)
  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_partida_pre AS r ON r.fk_poa_prev = p.rowid ";

$sql.= " WHERE p.entity = ".$conf->entity;

if ($idp)
  $sql.= " AND r.statut = 1";
if ($filter)
  $sql.= $filter;

if ($idsArea)
  $sql.= " AND p.fk_area IN ($idsArea)";

if ($idp)
  $sql.= " AND r.fk_poa = ".$idp;
if ($idpa)
  $sql.= " AND p.fk_pac = ".$idpa;

if ($search_gestion)   
  $sql .= " AND p.gestion LIKE '%".$db->escape($search_gestion)."%'";
 else
   if (!empty($gestion))
     $sql.= " AND p.gestion LIKE '%".$db->escape($gestion)."%'";
if ($search_label)   $sql .= " AND p.label LIKE '%".$db->escape($search_label)."%'";
if ($search_nro)   $sql .= " AND p.nro_preventive LIKE '%".$db->escape($search_nro)."%'";
if ($search_statut)   $sql .= " AND p.statut LIKE '%".$db->escape($search_statut)."%'";
if ($search_amount)   $sql .= " AND p.amount LIKE '%".$db->escape($search_amount)."%'";

$sql.= $db->order($sortfield,$sortorder);
 $sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);

if ($result)
  {
    
    $form=new Form($db);

    $objwork = new Poaworkflow($db);
    $objworkd = new Poaworkflowdet($db);
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
    $aRetraso = array();
    $nTotal = 0;
    $aEstado = array();
    $aPreventivo = array();
    $nDiamax = 0;
    foreach ((array) $acDays AS $j => $nDay)
      {
	$aRetraso[$nDay] = 0;
	$aColors[$nDay] = $acColors[$j];
	$aDays[$nDay] = array(1=>$nDia,2=>$nDay);
	$nDia = $nDay;
	$nDiamax = $nDay;
      }
    $nDiamax+=1;
    $aRetraso[$nDiamax];
    $aColors[$nDiamax] = 'FF0000';
    $nDiaall = 0;
    foreach ((array) $acDaysall AS $j => $nDay)
      {
	$aRetrasoall[$nDay] = 0;
	$aColorsall[$nDay] = $acColorsall[$j];
	$aDaysall[$nDay] = array(1=>$nDiaall,2=>$nDay);
	$nDiaall = $nDay;
	$nDiamaxall = $nDay;
      }
    $nDiamaxall+=1;
    $aRetrasoall[$nDiamaxall];
    $aColorsall[$nDiamaxall] = 'FF0000';

    //array preventivo
    $aPreventivo[-1] = 0;
    $aPreventivo[0] = 0;
    $aPreventivo[1] = 0;
    $aPreventivo[2] = 0;
    $aPreventivo[3] = 0;

    $num = $db->num_rows($result);
    $i = 0;

    $aArrcss= array('poa/css/style.css','poa/css/style-desktop.css');
    $aArrjs = array('poa/js/config.js');
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Budget execution"),$help_url,'','','',$aArrjs,$aArrcss);
    
    //listando colores
    if (!empty($aColors))
      {
	$htmlColors = '';
	$htmlColors.= '<div>';
	$htmlColors.= '<div style="float:left; width:200px; height:20px; background:#FFFFFF;">';
	$htmlColors.= $langs->trans('Colors days late');
	$htmlColors.= '</div>';
	foreach((array) $aColors AS $nD1 => $cColor)
	  {
	    $htmlColors.= '<div style="margin:0 auto;color:#304775;text-align:center;float:left; width:50px; height:20px; background:#'.$cColor.';">';
	    $htmlColors.= $nD1;
	    $htmlColors.= '</div>';
	  }
	$htmlColors.= '<div style="text-align:center;float:left; width:50px; height:20px; background:#ffaaaa;">';
	$htmlColors.= ' > '.$nD1;
	$htmlColors.= '</div>';
	
	$htmlColors.= '</div>';
      }
    $htmlColors.= '<div style="float:clear;"></div>';

    print_barre_liste($langs->trans("Budget execution"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListTitle',$parameters);
    // Note that $action and $object may have been modified by hook
    

    if ($num)
      {
	$sumaAmount = 0;
	$var=True;
	while ($i < min($num,$limit))
	  {	      
	    $obj = $db->fetch_object($result);	
	    $newNombre = '';
	    $nombre = '';
	    if ($objuser->fetch($obj->fk_user_create))
	      {
		$nombre = $objuser->firstname.' '.$objuser->lastname;
		$aNombre = explode(' ',$nombre);
		foreach($aNombre AS $k => $value)
		  {
		    $newNombre .= substr($value,0,1);
		  }
	      }
	    $lContinue = true;
	    if (!empty(STRTOUPPER($search_user)) && $search_user != $newNombre)
	      {
		$lContinue = false;
	      }
	    if ($lContinue)
	      {
		$var=!$var;
		
		$nTotal++;
		//workflow
		$daydelay = 0;
		$daydelayall = 0;
		$date_tracking = '';
		$date_workflow = $db->jdate($obj->date_preventive);
		$cArea = '';
		$objworkact = '';
		$iniproceso = false;
		$iniwork    = false;

		//buscamos el workflow
		if ($objwork->fetch_prev($obj->id)>0)
		  {
		    if ($objwork->fk_poa_prev == $obj->id)
		      {
			//buscamos el ultimo registro workflowdet
			$objworkd->getlist($objwork->id,1);
			foreach((array) $objworkd->array AS $l => $objWorkDet)
			  {
			    if (empty($date_tracking))
			      {
				$date_tracking = $objWorkDet->date_tracking;
				$cArea = $objWorkDet->code_area_next;
				$objworkact = $objWorkDet;
			      }
			  }
			//determinamos el tiempo transcurrido
			$daydelayall = resta_fechas($date_workflow,dol_now(),1);
			if ($objwork->statut < 2)
			  $daydelay    = resta_fechas($date_tracking,dol_now(),1);
			if ($objwork->fk_poa_prev == $obj->id)
			  {
			    if (is_null($objwork->contrat))
			      $iniwork = 2;
			    if ($objwork->contrat == '1' || $objwork->contrat == '0')
			      $iniwork = 3;
			    //si no pertenece al usuario
			    if ($user->id != $obj->fk_user_create)
			      {
				$iniwork = false;
				if ($objwork->fk_poa_prev == $obj->id)
				  $iniwork = 3;
			      }

			  }
			else
			  $iniwork = 1;
		      }
		    else
		      {
			if ($user->admin || ($user->id == $obj->fk_user_create))
			  $iniwork = 1;
		      }
		  }
		else
		  $iniwork = 1;
		$cClass = "";
		foreach ((array) $aDays AS $nDay => $aDay)
		  {
		    if ($daydelay > $aDay[1] && $daydelay <= $aDay[2])
		      {
			$cClass = $aColors[$nDay];
			$aRetraso[$nDay]++;
		      }
		  }
		if ($daydelay <= 0)
		  $aRetraso[0]++;
		if ($daydelay > $nDiamax)
		  $aRetraso[$nDiamax]++;

		//all
		foreach ((array) $aDaysall AS $nDay => $aDay)
		  {
		    if ($daydelayall > $aDay[1] && $daydelayall <= $aDay[2])
		      {
			$aRetrasoall[$nDay]++;
		      }
		  }
		if ($daydelayall <= 0)
		  $aRetrasoall[0]++;
		if ($daydelayall > $nDiamaxall)
		  $aRetrasoall[$nDiamaxall]++;

		//fin workflow

		if (empty($cClass) && $daydelay > $nDay) $cClass = 'ffaaaa';
		$aPreventivo[$obj->statut]++;
		// if ($obj->statut == -1) $aPreventivo[$obj->statut]++;
		// if ($obj->statut == 0) $aPreventivo['B']++;
		// if ($obj->statut == 1) $aPreventivo['P']++;
		// if ($obj->statut == 2) $aPreventivo['C']++;
		// if ($obj->statut == 3) $aPreventivo['D']++;
		// if ($obj->statut == 4) $aPreventivo['E']++;

		if ($idp)
		  $sumaAmount += $obj->amountpartida;
		
		//instruction
		//buscamos la ultima instruccion si existe para el poa seleccionado
		$addClase = ''; 
		$addMessage = '';
		if ($conf->poai->enabled)
		  {
		    $objinst->fetch_pre($obj->id);
		    if ($objinst->fk_id == $obj->id)
		      {
		    $objinst->fk_id.' '.$obj->id;

			$idInst = $objinst->id;
			$newClaseor = $newClase;
			$detail = $objinst->detail;		      
			//verificamos si tiene monitoreo por revisar
			if ($objmoni->fetch_ult($obj->id,'PRE'))
			  {
			    if ($objmoni->fk_id == $obj->id)
			      {
				$idInst = $objmoni->fk_poai_instruction;
				$addMessage = '&#13;'.$langs->trans('Monitoring').': '.$objmoni->detail;
				if ($lStyle)
				  $newClase.= ' background:#12e539;';
				else
				  $newClase.= '" style="background:#12e539;';
			      }
			  }
			// print '<td nowrap class="'.$newClase.'">';
			// print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&backtopage=1&typeinst=PRE&id='.$idInst.'" title="'.trim($detail).': '.$langs->trans('Commitment date').' '.dol_print_date($objinst->commitment_date,'day').$addMessage.'">'.img_picto($langs->trans('Edit'),'next').' '.(strlen($detail)>11?substr($detail,0,5).'.':$detail).'</a>';
			// print '</td>';
			$newClase = $newClaseor;
		      }
		    else
		      {
			// print '<td nowrap>';
			// if ($user->rights->poai->inst->crear)
			//   print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&fk_user='.$obj->fk_user_create.'&action=create&typeinst=PRE'.'&backtopage=1">'.img_picto($langs->trans('Newinstruction'),'next').'</a>';
			// else
			//   print '&nbsp;';
			// print '</td>';
		      }
		  }

		//----------------------------------//
		//action
		//		print '<td align="right" nowrap>';
		//se movio a la parte inicial del workflow
		
		$message = $daydelay .'/'.$daydelayall;
		if ($objwork->statut == 2 && $objwork->fk_poa_prev == $obj->id)
		  {
		    $daydelay = 0;
		  }
		else
		  {
		    if ($daydelay > $nDay)
		      $cClass = $aColors[$nDay];
		  }
		//buscamos el proceso
		if ($obj->statut != -1)
		  {
		    $objproc->fetch_prev($obj->id);
		    if ($objproc->fk_poa_prev == $obj->id)
		      {
			$addMessage = '&nbsp;'.$langs->trans('Doc').': '.$objproc->ref.'/'.$objproc->gestion;
		      }
		    else
		      if ( $user->admin || 
			   (!$user->admin && $user->id == $obj->fk_user_create))
			{
			  $iniproceso = true;
			}
		  }		
	      }
	    $i++;
	  }
      }
    // if ($idp)
    //   {
    // 	//totales
    // 	print '<tr class="liste_total"><td colspan="5">'.$langs->trans("Total").'</td>';
    // 	print '<td align="right">';
    // 	print price($sumaAmount);
    // 	print '</td>';
    // 	print '<td colspan="3">';
    // 	print '&nbsp;';
    // 	print '</td>';
    // 	print '</tr>';
    //   }
    
    // print '</table>';

    // print '</form>';

    print '<div id="section-der">';    
    print '<nav id="header">'.$langs->trans('Execution with individual delay days').'</nav>';
    print '<div>';
    $min = 0;
    $seq = count($aColors);
    $x=0;
    foreach((array) $aColors AS $nD1 => $cColor)
      {
	$x++;
	print '<div id="divl" style="background-color:#'.$cColor.'">';
	if ($x == $seq)
	  print $langs->trans('Mayor o igual a').' '.$nD1.' '.$langs->trans('Days');
	else
	  if ($nD1 == 0)
	    print $nD1.' '.$langs->trans('Days');
	  else
	    print $min .' '.$langs->trans('To').' '.$nD1.' '.$langs->trans('Days');
	print '</div>';

	$rango = $aRetraso[$nD1] / $nTotal * 100;

	print '<div id="divl" class="size15">';
	print '<table style="width:'.ceil($rango).'%;" class="tdblock">';
	print '<tr><td id="fondo1">';
	print '<a href="liste.php?filterw=n'.$nD1.'">';
	print $aRetraso[$nD1];
	print '</a>';
	print '</td></tr>';
	print '</table>';
	print '</div>';
	$min = $nD1+1;
	print '<div style="clear:both;"></div>';
      }
    print '</div>';
    print '</div>';

    print '<div id="section-der">';    
    print '<nav id="header">'.$langs->trans('Execution with days of total delay').'</nav>';
    print '<div>';
    $min = 0;
    $seq = count($aColorsall);
    $x=0;
    foreach((array) $aColorsall AS $nD1 => $cColor)
      {
	$x++;
	print '<div id="divl" style="background-color:#'.$cColor.'" >';
	if ($x == $seq)

	  print $langs->trans('Mayor o igual a').' '.$nD1.' '.$langs->trans('Days');
	else
	  if ($nD1 == 0)
	    print $nD1.' '.$langs->trans('Days');
	    else
	      print $min .' '.$langs->trans('To').' '.$nD1.' '.$langs->trans('Days');
	print '</div>';

	$rango = $aRetrasoall[$nD1] / $nTotal * 100;

	print '<div id="divl" class="size15">';
	print '<table style="width:'.ceil($rango).'%;" class="tdblock" >';
	print '<tr><td id="fondo1">';
	print '<a href="liste.php?filterx=n'.$nD1.'">';

	print $aRetrasoall[$nD1];
	print '</a>';

	print '</td></tr>';
	print '</table>';
	print '</div>';
	$min = $nD1+1;
	print '<div style="clear:both;"></div>';
      }
    print '</div>';
    print '</div>';

    //presupuesto
    print '<div id="section-der">';    
    print '<nav id="header">'.$langs->trans('Monitoring budget execution').'</nav>';
    print '<div>';
    $min = 0;
    $seq = count($aColorsall);
    $x=0;
    foreach((array) $aPreventivo AS $i => $valor)
      {
	if ($i == -1) {$cColor = 'FF0000';$cImg = 'imganu';$cText=$langs->trans('Canceled');}
	if ($i == 0) {$cColor = 'FF7700';$cImg = 'imgpen';$cText=$langs->trans('Pending');}
	if ($i == 1) {$cColor = 'FFF600';$cImg = 'imgpre';$cText=$langs->trans('Preventive');}
	if ($i == 2) {$cColor = '0030FF';$cImg = 'imgcom';$cText=$langs->trans('Committed');}
	if ($i == 3) {$cColor = '1BBDD5';$cImg = 'imgdev';$cText=$langs->trans('Accrued');}
	if ($i == 4) {$cColor = '1ABD21';$cImg = 'imgall';$cText=$langs->trans('All');}

	$x++;
	print '<div id="divl"  class="'.$cImg.' tright">';
	  print $cText;
	print '</div>';

	$rango = $valor / $nTotal * 100;

	print '<div id="divl" class="size15">';
	print '<table style="width:'.ceil($rango).'%;" class="tdblock" >';
	print '<tr><td id="fondo1">';
	print '<a href="liste.php?filters=n'.$i.'">';
	print $valor;
	print '</a>';
	print '</td></tr>';
	print '</table>';
	print '</div>';
	$min = $i+1;
	print '<div style="clear:both;"></div>';
      }
    //total
    print '<div id="divl"  class="imgall tright ">';
    print $langs->trans('Total');
    print '</div>';
    print '<div id="divl" class="tleft size15">';
    print '<a href="liste.php?nosearch_x=n">';

    print $nTotal;
    PRINT '</a>';
    print '</div>';
    print '<div style="clear:both;"></div>';

    print '</div>';
    print '</div>';

    // print '<section id="section-der">';
    // print '<h2>'.$langs->trans('Monitoring budget execution').'</h2>';
    // foreach((array) $aPreventivo AS $i => $valor)
    //   {

    // 	print '<div id="cuadro1" style="background-color:#'.$cColor.'">';
    // 	print '<span class="size25">';
    // 	print $valor;
    // 	print '</span>';
    // 	print '</div>';
    //   }
    // //total
    // print '<div id="cuadro1" style="background-color:#ADB4B5">';
    // print '<span class="size25">';
    // print $nTotal;
    // print '</span>';
    // print '</div>';
    
    print '<div style="clear:both;"></div>';

    print '</section>';





    // print '<section id="section-der">';    
    // print '<h2>'.$langs->trans('Budget execution with days late').'</h2>';
    // foreach((array) $aColors AS $nD1 => $cColor)
    //   {
    // 	print '<div id="cuadro1" style="background-color:#'.$cColor.'">';
    // 	print '<span class="size25">';
    // 	print $nD1 .' -> '.$aRetraso[$nD1];
    // 	print '</span>';
    // 	print '</div>';
    //   }
    // print '<div id="cuadro1" style="background-color:#FF0000">';
    // print '<span class="size25">';
    // print '> '.$nD1 .' -> '.$aRetraso[$nD1];
    // print '</span>';
    // print '</div>';
    // print '<div style="float:clear;"></div>';
    // print '</section>';

    // print '<section id="section-der">';
    // print '<h2>'.$langs->trans('Monitoring budget execution').'</h2>';
    // foreach((array) $aPreventivo AS $i => $valor)
    //   {
    // 	if ($i == 'A') $cColor = 'FF0000';
    // 	if ($i == 'B') $cColor = 'FF7700';
    // 	if ($i == 'P') $cColor = 'FFF600';
    // 	if ($i == 'C') $cColor = '0030FF';
    // 	if ($i == 'D') $cColor = '1BBDD5';
    // 	if ($i == 'E') $cColor = '1ABD21';

    // 	print '<div id="cuadro1" style="background-color:#'.$cColor.'">';
    // 	print '<span class="size25">';
    // 	print $valor;
    // 	print '</span>';
    // 	print '</div>';
    //   }
    // //total
    // print '<div id="cuadro1" style="background-color:#ADB4B5">';
    // print '<span class="size25">';
    // print $nTotal;
    // print '</span>';
    // print '</div>';
    
    // print '<div style="float:clear;"></div>';

    // print '</section>';

    $db->free($result);

  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
