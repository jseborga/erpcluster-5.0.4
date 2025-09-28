<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/poa/activity/liste.php
 *      \ingroup    Activity
 *      \brief      Page liste des activity
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivity.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivitydet.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivityworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidapre.class.php");
//require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

// if ($conf->poai->enabled)
//   {
//     require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
//     require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
//   }

$langs->load("poa@poa");

if (!$user->rights->poa->prev->leer)
  accessforbidden();

$object = new Poaactivity($db);
$objectd= new Poaactivitydet($db);
$objectw= new Poaactivityworkflow($db);
$objprev = new Poaprev($db);
//$objproc = new Poaprocess($db);
$objuser = new User($db);
$objpre = new Poapartidapre($db);

// if ($conf->poai->enabled)
//   {
//     $objinst = new Poaiinstruction($db);
//     $objmoni = new Poaimonitoring($db);
//   }

//asignando filtro de usuario
assign_filter_user('psearch_user');

$id = GETPOST('id');
$idp = GETPOST('idp');
$action = GETPOST('action');
if (isset($_GET['nopac']))
  {
    $_SESSION['idPac'] = '';
    $_SESSION["pfilterw"] = '';
    $_SESSION["pfilterx"] = '';
    $_SESSION["pfilters"] = '';
  }
if (isset($_GET['idpa']))
  $_SESSION['idPac'] = $_GET['idpa'];
$idpa = $_SESSION['idPac'];

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];

//gestion definidia en index
$gestion = $_SESSION['gestion'];
if (isset($_POST['search_gestion']))
  $_SESSION['psearch_gestion'] = $_POST['search_gestion'];
elseif (isset($_GET['search_gestion']))
  $_SESSION['psearch_gestion'] = $_GET['search_gestion'];
if (isset($_POST['search_label']))
  $_SESSION['psearch_label'] = $_POST['search_label'];
if (isset($_POST['search_nro']))
  $_SESSION['psearch_nro'] = $_POST['search_nro'];
elseif (isset($_GET['search_nro']))
  $_SESSION['psearch_nro'] = $_GET['search_nro'];
if (isset($_POST['search_statut']))
  $_SESSION['psearch_statut'] = $_POST['search_statut'];
if (isset($_POST['search_code']))
  $_SESSION['psearch_code'] = $_POST['search_code'];
if (isset($_POST['search_priority']))
  $_SESSION['psearch_priority'] = $_POST['search_priority'];
if (isset($_POST['search_amount']))
  $_SESSION['psearch_amount'] = $_POST['search_amount'];
if (isset($_POST['search_partida']))
  $_SESSION['psearch_partida'] = $_POST['search_partida'];
if (isset($_GET['filterw']))
  {
    $_SESSION['psearch_nro'] = '';
    $_SESSION["psearch_label"] = '';
    $_SESSION["psearch_amount"] = '';
    $_SESSION["psearch_code"] = '';
    $_SESSION["psearch_partida"] = '';
    $_SESSION['psearch_priority'] = '';
    $_SESSION['pfilterx'] = '';
    $_SESSION['pfilters'] = '';
    $_SESSION['pfiltery'] = '';
    $_SESSION['pfilterw'] = ($_GET['filterw']);
    $_SESSION['psearch_area'] = '';
    $_SESSION['psearch_user'] = '';
    //$_SESSION['psearch_priority'] = '';
  }
if (isset($_GET['filterx']))
  {
    $_SESSION['psearch_nro'] = '';
    $_SESSION["psearch_label"] = '';
    $_SESSION["psearch_code"] = '';
    $_SESSION["psearch_amount"] = '';
    $_SESSION['psearch_priority'] = '';
    $_SESSION['pfilters'] = '';
    $_SESSION['pfilterw'] = '';
    $_SESSION['pfiltery'] = '';
    $_SESSION['pfilterx'] = ($_GET['filterx']);
    $_SESSION['psearch_area'] = '';
    $_SESSION['psearch_user'] = '';
    //$_SESSION['psearch_priority'] = '';
  }
if (isset($_GET['filtery']))
  {
    $_SESSION['psearch_nro'] = '';
    $_SESSION["psearch_label"] = '';
    $_SESSION["psearch_code"] = '';
    $_SESSION["psearch_amount"] = '';
    $_SESSION["psearch_partida"] = '';
    $_SESSION['psearch_priority'] = '';
    $_SESSION['pfilters'] = '';
    $_SESSION['pfilterw'] = '';
    $_SESSION['pfilterx'] = '';
    $_SESSION['pfiltery'] = ($_GET['filtery']);
    $_SESSION['psearch_area'] = '';
    $_SESSION['psearch_user'] = '';
    //$_SESSION['psearch_priority'] = '';
  }
if (isset($_GET['filters']))
  {
    $_SESSION['psearch_nro'] = '';
    $_SESSION["psearch_label"] = '';
    $_SESSION["psearch_amount"] = '';
    $_SESSION["psearch_code"] = '';
    $_SESSION["psearch_partida"] = '';
    $_SESSION['psearch_priority'] = '';
    $_SESSION['pfilterw'] = '';
    $_SESSION['pfilterx'] = '';
    $_SESSION['pfiltery'] = '';
    $_SESSION['pfilters'] = ($_GET['filters']);
    $_SESSION['psearch_area'] = '';
    $_SESSION['psearch_user'] = '';
    //$_SESSION['psearch_priority'] = '';
  }
if (isset($_POST['search_area']) || isset($_GET['search_area']))
  $_SESSION['psearch_area'] = STRTOUPPER(($_POST['search_area']?$_POST['search_area']:$_GET['search_area']));
if (isset($_POST['search_user']) || isset($_GET['search_user']))
  $_SESSION['psearch_user'] = STRTOUPPER(($_POST['search_user']?$_POST['search_user']:$_GET['search_user']));

if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
  {
    $_SESSION["psearch_gestion"] = '';
    $_SESSION["psearch_label"] = '';
    $_SESSION["psearch_nro"] = '';
    $_SESSION["psearch_statut"] = '';
    $_SESSION["psearch_code"] = '';
    $_SESSION["psearch_amount"] = '';
    $_SESSION["psearch_partida"] = '';
    $_SESSION["psearch_user"] = '';
    $_SESSION["psearch_area"] = '';
    $_SESSION['psearch_priority'] = '';
    $_SESSION["pfilterw"] = '';
    $_SESSION["pfilterx"] = '';
    $_SESSION["pfilters"] = '';
    $_SESSION["pfiltery"] = '';
    $_SESSION['idPac'] = '';
  }

$search_gestion   = $_SESSION["psearch_gestion"];
$search_label     = $_SESSION["psearch_label"];
$search_nro       = $_SESSION["psearch_nro"];
$search_statut    = $_SESSION["psearch_statut"];
$search_code      = $_SESSION["psearch_code"];
$search_amount    = $_SESSION["psearch_amount"];
$search_partida   = $_SESSION["psearch_partida"];
$search_user      = $_SESSION["psearch_user"];
$search_area      = $_SESSION["psearch_area"];
$search_priority  = $_SESSION['psearch_priority'];
$filterw          = $_SESSION["pfilterw"];
$filterx          = $_SESSION["pfilterx"];
$filters          = $_SESSION["pfilters"];
$filtery          = $_SESSION["pfiltery"];

//definicion de workflow limit
$wfone = $conf->global->POA_WORKFLOW_LIMIT_ONE;
$wftwo = $conf->global->POA_WORKFLOW_LIMIT_TWO;
$wfthr = $conf->global->POA_WORKFLOW_LIMIT_THR;

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.nro_activity";
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

$sql = "SELECT p.rowid AS id, p.gestion, p.fk_poa, p.fk_pac, p.fk_prev, p.label, p.pseudonym, p.nro_activity, p.date_activity, p.statut, p.active, p.fk_user_create, p.code_requirement,  ";
$sql.= " p.amount, p.partida, p.priority, p.date_create, p.fk_user_create, p.fk_prev_ant,";
$sql.= " u.login, u.firstname, u.lastname, ";
$sql.= " s.label as structure, s.sigla ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa AS pp ON pp.rowid = p.fk_poa ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON s.rowid = pp.fk_structure ";
//user
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user AS u ON u.rowid = p.fk_user_create ";

$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.statut != -1";
if ($filter)
  $sql.= $filter;

// if ($idsArea)
//   $sql.= " AND p.fk_area IN ($idsArea)";

if ($idp)
  $sql.= " AND p.fk_poa = ".$idp;
if ($idpa)
  $sql.= " AND p.fk_pac = ".$idpa;
if ($_SESSION['sel_area'])
  $sql.= " AND p.fk_area = ".$_SESSION['sel_area'];

if ($search_gestion)   
  $sql .= " AND p.gestion LIKE '%".$db->escape($search_gestion)."%'";
 else
   if (!empty($gestion))
     $sql.= " AND p.gestion LIKE '%".$db->escape($gestion)."%'";
if ($search_label)   $sql .= " AND p.label LIKE '%".$db->escape($search_label)."%'";
if ($search_nro)   $sql .= " AND p.nro_activity LIKE '%".$db->escape($search_nro)."%'";
if ($search_statut)   $sql .= " AND p.statut LIKE '%".$db->escape($search_statut)."%'";
if ($search_amount)   $sql .= " AND p.amount LIKE '%".$db->escape($search_amount)."%'";
if ($search_partida)   $sql .= " AND p.partida LIKE '%".$db->escape($search_partida)."%'";
if ($search_priority)   $sql .= " AND p.priority = ".$db->escape($search_priority);

$sql.= $db->order($sortfield,$sortorder);
 $sql.= $db->plimit($limit+1, $offset);

// if (STRTOUPPER($user->firstname) == 'ROMINA')
// if ($user->admin)
//$sql;
$result = $db->query($sql);
//procesando informacion de colores
//include_once DOL_DOCUMENT_ROOT.'/poa/lib/execution.lib.php';

$result = $db->query($sql);

if ($result)
  {
    $htmlother=new FormOther($db);

    $form=new Form($db);
    //totales para monto y conteo
    $nTotalPrev = 0;
    $nCountPrev = 0;

    // //procesando para el reporte
    // $objwork = new Poaworkflow($db);
    // $objworkd = new Poaworkflowdet($db);
    //rango de colores error para retraso
    $cRangecolors = $conf->global->POA_COLORS_RANGE_DAYS_LATE;
    list($cDays,$cColors) = explode('|',$conf->global->POA_COLORS_RANGE_DAYS_LATE);
    list($cDaysall,$cColorsall) = explode('|',$conf->global->POA_COLORS_RANGE_DAYS_LATE_ALL);

    $acDays = explode(',',$cDays);
    $acColors = explode(',',$cColors);
    $acDaysall = explode(',',$cDaysall);
    $acColorsall = explode(',',$cColorsall);
    $nDia = -1;
    foreach ((array) $acDays AS $j => $nDay)
      {
	$aColors[$nDay] = $acColors[$j];
	$aDays[$nDay] = array(1=>$nDia,2=>$nDay);
	$nDia = $nDay;
	$nDiamax = $nDay;
      }
    $nDiamax+=1;
    $aColors[$nDiamax] = 'FF0000';
    $nDiaall = -1;
    foreach ((array) $acDaysall AS $j => $nDay)
      {
	$aColorsall[$nDay] = $acColorsall[$j];
	$aDaysall[$nDay] = array(1=>$nDiaall,2=>$nDay);
	$nDiaall = $nDay;
	$nDiamaxall = $nDay;
      }
    $nDiamaxall+=1;
    $aColorsall[$nDiamaxall] = 'FF0000';


    $num = $db->num_rows($result);
    $i = 0;

    $aArrcss= array('poa/css/style.css');
    $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviarformmod.js','poa/js/jquery-1.3.min.js');

    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Prevent"),$help_url,'','','',$aArrjs,$aArrcss);
    
    // //listando colores
    // if (!empty($aColors))
    //   {
    // 	$htmlColors = '';
    // 	$htmlColors.= '<div>';
    // 	$nCount = count($aColors);
    // 	$n=0;
    // 	foreach((array) $aColors AS $nD1 => $cColor)
    // 	  {
    // 	    $n++;
    // 	    $htmlColors.= '<div style="margin:0 auto;color:#304775;text-align:center;float:left; width:50px; height:20px; background:#'.$cColor.';">';
    // 	    if ($n == $nCount)
    // 	      $htmlColors.= $langs->trans('+').' '.$nD1;
    // 	    else
    // 	      $htmlColors.= $nD1;
    // 	    $htmlColors.= '</div>';
    // 	  }
    // 	$htmlColors.= '</div>';
    //   }
    // if (!empty($aColorsall) && $filterx)
    //   {
    // 	$htmlColors = '';
    // 	$htmlColors.= '<div>';
    // 	$htmlColors.= '<div style="float:left; width:200px; height:20px; background:#FFFFFF;">';
    // 	$htmlColors.= $langs->trans('Colors days late');
    // 	$htmlColors.= '</div>';
    // 	foreach((array) $aColorsall AS $nD1 => $cColor)
    // 	  {
    // 	    $htmlColors.= '<div style="margin:0 auto;color:#304775;text-align:center;float:left; width:50px; height:20px; background:#'.$cColor.';">';
    // 	    $htmlColors.= $nD1;
    // 	    $htmlColors.= '</div>';
    // 	  }
	
    // 	$htmlColors.= '</div>';
    //   }
    // $htmlColors.= '<div style="float:clear;"></div>';



    print_barre_liste($langs->trans("Activities"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    //filtro
    print '<form name="fo2" id="fo2" onsubmit="enviarformmod(), return false" method="POST" action="'.$_SERVER["PHP_SELF"].'">'."\n";

?>
<script type="text/javascript">
    function CambiarURLFrametwo(id,idReg,priority){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_poaa");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = priority;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframetwo').src= 'actualiza_prev.php?action=update&id='+id+'&priority='+priority;
}
</script>
<iframe id="iframetwo" src="actualiza_prev.php" width="0" height="0" frameborder="0"></iframe>
    
<script type="text/javascript">
    function CambiarURLFrameup(id,idReg,followup){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_poaa");
      //asignando nuevo valor
      if (followup)
	document.getElementById(idTwo).innerHTML = followup;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframeup').src= 'add_workflow.php?action=add&id='+id+'&followup='+followup;
}
</script>
<iframe id="iframeup" src="add_workflow.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
    function CambiarURLFrameto(idr,idReg,followto){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_poaa");
      //asignando nuevo valor
      if (idr)
	document.getElementById(idTwo).innerHTML = followto;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      if (idr)
	document.getElementById('iframeto').src= 'add_workflow.php?action=update&idr='+idr+'&followto='+followto;
}
</script>
<iframe id="iframeto" src="add_workflow.php" width="0" height="0" frameborder="0"></iframe>

<?php

    // // Filter on categories
    // $moreforfilter='';
    // if (! empty($conf->categorie->enabled))
    //   {
    // 	$moreforfilter.=$langs->trans('Categories'). ': ';
    // 	$moreforfilter.=$htmlother->select_categories(1,$search_categ,'search_categ',1);
    // 	$moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
    //   }
    // if ($moreforfilter)
    //   {
    // 	print '<div class="liste_titre">';
    // 	print $moreforfilter;
    // 	print '</div>';
    //   }
    


    print '<table class="noborder" id="tabla" width="100%">';

    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Nro"),"liste.php", "p.nro_preventive","","","");
    print_liste_field_titre($langs->trans("Gestion"),"liste.php", "p.gestion","","","");
    print_liste_field_titre($langs->trans("Name"),"liste.php", "p.label","","","");
    $celcolor = '';
    if ($user->rights->poa->prev->mod)
      $celcolor = ' style="background:#ffff00"';
    print_liste_field_titre($langs->trans("Priority"),"liste.php", "p.priority","","",'align="center" '.$celcolor);
    // print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_preventive","","","");
    print_liste_field_titre($langs->trans("Requirementtype"),"liste.php", "p.date_preventive","","","");
    print_liste_field_titre($langs->trans("Meta"),"liste.php", "p.date_preventive","","","");
    print_liste_field_titre($langs->trans("Structure"),"liste.php", "p.date_preventive","","","");
    //if ($idp)
    print_liste_field_titre($langs->trans("Partida"),"liste.php", "p.date_preventive","","","");
    print_liste_field_titre($langs->trans("Amount"),"liste.php", "p.amount","","","");
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut","","","");
    print_liste_field_titre($langs->trans("User"),"liste.php", "p.fk_user_create","","","");
    //recupera los procedimientos por orden
    $aProcedure = getlist_typeprocedure('code',1,'f.landmark');
    $aProc = array();
    //armando los hitos utiizados en activitydet
    $objectd->getcode($gestion);
    //armando
    foreach((array) $aProcedure AS $code => $objtypeproc)
      {
	if ($objectd->aCode[$code])
	  if (empty($objtypeproc->sigla))
	    $aProc[$code] = $objtypeproc->label;
	  else
	    $aProc[$code] = $objtypeproc->sigla;
      }
    //armamos el encabezado
    foreach ((array) $aProc AS $code => $labelproc)
      print_liste_field_titre($labelproc,"", "","","",'align="center"');
    print_liste_field_titre($langs->trans("Followup"),"", "","","","");
    print_liste_field_titre($langs->trans("Followto"),"", "","","","");

    print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
    
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListTitle',$parameters);
    // Note that $action and $object may have been modified by hook
    

    print '</tr>';
    
    //filtro colores
    print '<tr class="liste_titre">';    
    print '<td >&nbsp;</td>';
    print '<td class="liste_titre">&nbsp;</td>';
    
    print '<td nowrap class="liste_titre">';
    print $htmlindividual;
    print '</td>';
    
    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    // if ($idp)
    //  {
    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
	//  }
    //armamos el encabezado
    foreach ((array) $aProc AS $code => $labelproc)
    	print '<td align="left" class="liste_titre">&nbsp;</td>';

    print '<td colspan="5" align="left" class="liste_titre">';
    print $htmltotal;
    print '</td>';

    print '<td  class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td  class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    
    print '</tr>';

    //buscadores
    print '</tr>';
    
    print '<tr class="liste_titre">';    
    print '<td class="liste_titre"><input type="text" class="flat" name="search_nro" value="'.$search_nro.'" size="2"></td>';
    print '<td class="liste_titre"><input type="text" class="flat" name="search_gestion" value="'.$search_gestion.'" size="2"></td>';
    
    print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="20">';
    print '</td>';

    print '<td align="center" nowrap class="liste_titre"><input type="text" class="flat" name="search_priority" value="'.$search_priority.'" size="5">';
    print '</td>';
    
    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="7" name="search_code" value="'.$search_code.'">';
    print '</td>';

    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';


    //if ($idp)
    //  {
	print '<td align="left" class="liste_titre">';
	print '&nbsp;';
	print '</td>';
	//  }

	print '<td align="left" class="liste_titre">';
	print '<input class="flat" type="text" size="3" name="search_partida" value="'.$search_partida.'">';
	print '</td>';

    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="10" name="search_amount" value="'.$search_amount.'">';
    print '</td>';
    
    print '<td nowrap width="80px;" align="left" class="liste_titre">';
    print $htmlpre;
    print '</td>';

    print '<td align="left" class="liste_titre">';
    print '<input class="user" type="text" size="5" name="search_user" value="'.$search_user.'">';
    print '</td>';
    //armamos el encabezado
    foreach ((array) $aProc AS $code => $labelproc)
    	print '<td align="left" class="liste_titre">&nbsp;</td>';

    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
        print '<td  class="liste_titre">';
    print '&nbsp;';
    print '</td>';

    print '<td nowrap class="liste_titre" align="right">';
    // print '<input class="user" type="text" size="5" name="search_area" value="'.$search_area.'">';

    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '&nbsp;';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
    print '</td>';
    
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    
    print '</tr>';

    if ($num)
      {
	$sumaAmount = 0;
	$var=True;
	$i = 0;
	$aHtml = array();
	while ($i < min($num,$limit))
	  {
	    $login = $obj->login;

	    $lContinue = true;
	    $obj = $db->fetch_object($result);
	    //preventivo
	    $objprev = new Poaprev($db);
	    $objprev->fetch($obj->fk_prev);

	    $newNombre = $obj->login;
	    $nombre = $obj->lastname.' '.$obj->firstname;

	    //revision del workflow
	    //workflow
	    $filtercode = false;
	    $lStatutejec = false;
	    $daydelay = 0;
	    $daydelayall = 0;
	    $date_tracking = '';
	    $date_workflow = $db->jdate($obj->date_preventive);
	    $cArea = '';
	    $cMessage = '';
	    $cProcess = '';
	    $objworkact = '';
	    $iniproceso = false;
	    $iniwork    = false;
	    $workstatut = 0;
	    //buscamos el workflow
	    $idPrev = !empty($obj->fk_prev_ant)?$obj->fk_prev_ant:$obj->id;
	    //if ($objwork->fetch_prev($idPrev)>0)
	    if ($x)
	      {
		if ($objwork->fk_poa_prev == $idPrev)
		  {
		    $workstatut = $objwork->statut;

		    //buscamos el ultimo registro workflowdet
		    $objworkd->getlist($objwork->id,1);
		    foreach((array) $objworkd->array AS $l => $objWorkDet)
		      {
			if (empty($date_tracking))
			  {
			    $date_tracking = $objWorkDet->date_tracking;
			    $cArea = $objWorkDet->code_area_next;
			    $cMessage = $objWorkDet->detail;
			    $codeProcedure = $objWorkDet->code_procedure;
			    $objworkact = $objWorkDet;
			  }
		      }
		    //buscamos el typeprocedure
		    $objProcedure = fetch_typeprocedure($codeProcedure,'code');
		    $cProcess = $objProcedure->label;
		    //analizamos si esta entre los rangos workflowone y workflowtwo
		    if (!empty($wfone) && !empty($wftwo))
		      {
			if (($objProcedure->landmark > $wfone && $objpProcedure->landmark < $wftwo) || $objProcedure->landmark >= $wfthr)
			  $lStatutejec = true;
		      }

		    // echo '<hr>list '.$workstatut .' '.$cArea;
		    //determinamos el tiempo transcurrido
		    $daydelayall = resta_fechas($date_workflow,dol_now(),1);
		    $daydelay    = resta_fechas($date_tracking,dol_now(),1);
		    if ($objwork->statut == 2) $daydelay=-5;
		    if ($objwork->fk_poa_prev == $idPrev)
		      {
			if (is_null($objwork->contrat))
			  $iniwork = 2;
			if ($objwork->contrat == '1' || $objwork->contrat == '0')
			  $iniwork = 3;
			//si no pertenece al usuario
			if ($user->id != $obj->fk_user_create)
			  {
			    $iniwork = false;
			    if ($objwork->fk_poa_prev == $idPrev)
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
	    //analizamos si esta en plena ejecucion
	    if ($lStatutejec)
	      {
		$daydelay = 1;
	      }

	    $cClass = "";
	    $cClassall = '';
	    $lFather = false;
	    if (!empty($search_area))
	      {
		$lContinueAnt = $lContinue;
		if (STRTOUPPER($langs->trans('Not initiated')) == STRTOUPPER($search_area))
		  {
		    if (empty($cArea) && $lContinueAnt)
		      $lContinue = true;
		    else
		      $lContinue = false;
		  }
		else
		  {
		    if (STRTOUPPER($cArea) == STRTOUPPER($search_area) && $lContinueAnt)
		      $lContinue = true;
		    else
		      $lContinue = false;
		  }
	      }


	    //revision de usuario
	    $textNombre = $newNombre.' '.$nombre;
	    $pregmatch = preg_match("/".$search_user."/i",STRTOUPPER($textNombre));
	    if (!empty($search_user) && !$pregmatch)
	      {
		$lContinue = false;
	      }
	    //verificamos si viene del filtrow
	    if ($lFather && $obj->fk_father>0)
	      $lContinue = false;
	    //cambiamos la clase si tiene fk_father
	    if ($obj->fk_father)
	      {
		$cClass = '';
		$cClassall = '';
	      }
	    if ($search_code)
	      {
		$ccode = select_requirementtype($obj->code_requirement,'','',0,1);
		$search_code;
		$filtercode = STRPOS(STRTOUPPER($ccode),STRTOUPPER($search_code));
		if ($filtercode === false)
		  $lContinue = false;
	      }
	    if ($lContinue)
	      {
		$aHtml[$i]['id'] = $obj->id;
		$var=!$var;
		//contador y total monto
		//obtenemos la suma de preventivos
		$sumaprev = $objpre->getsum($obj->fk_prev);
		
		$nCountPrev++;
		$nTotalPrev+= $obj->amount;
		
		if (empty($obj->fk_father))
		  print "<tr $bc[$var]>";
		else
		  print '<tr class="impairfather">';

		print '<td nowrap>'.'<a href="fiche.php?id='.$obj->id.'" title="'.$langs->trans('Create for').' '.$nombre.'">'.img_picto($langs->trans("Preventive"),DOL_URL_ROOT.'/poa/img/prev.png','',1).'&nbsp;'.$obj->nro_activity.'</a>'.'</td>';
		$aHtml[$i]['nro'] = $obj->nro_activity;	
		print '<td>'.$obj->gestion.'</td>';
		$aHtml[$i]['year'] = $obj->gestion;
		if ($cClass && $objwork->statut < 2)
		  print '<td style="background:#'.$cClass.'">'.$obj->label.'</td>';
		else
		  print '<td>'.$obj->label.'</td>';
		$aHtml[$i]['label'] = $obj->label;
		$idTagps = $obj->id+100000;
		$idTagps2 = $idTagps+100500;
		print '<td align="center">';
		if (empty($obj->fk_father))
		  {
		    if ($user->rights->poa->act->mod || $user->admin)
		      {
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_poaa" type="text" name="priority" value="'.$obj->priority.'" onblur="CambiarURLFrametwo('.$obj->id.','.$idTagps.','.'this.value);" size="5">'.'</span>';
			print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps.' , '.$idTagps2.')">';
			print (strlen($obj->priority)>0?'<a href="#" title="'.$obj->priority.'">'.$obj->priority.'</a>':(empty($obj->priority)?'&nbsp;':$obj->priority));
			
		      }
		    else
		      {
			print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
			print (strlen($obj->priority)>0?'<a href="#" title="'.$obj->priority.'">'.$obj->priority.'...</a>':(empty($obj->priority)?'&nbsp;':$obj->priority));
			
			print '</span>';
		      }
		  }
		$aHtml[$i]['priority'] = $obj->priority;
		print '</td>';
		// print '<td>'.dol_print_date($obj->date_activity,'day').'</td>';
		print '<td>'.select_requirementtype($obj->code_requirement,'','',0,1).'</td>';
		
		print '<td>'.$obj->fk_poa.'</td>';
		print '<td>'.$obj->sigla.'</td>';		
		$aHtml[$i]['date_preventive'] = $obj->date_activity;
		//if ($idp)
		  print '<td>'.$obj->partida.'</td>';
		$aHtml[$i]['partida'] = $obj->partida;
		if ($cClassall && $objwork->statut < 2)
		  print '<td align="right" style="background:#'.$cClassall.'">';
		else		
		  print '<td align="right">';
		//amount activity
		// if ($idp)
		//   {
		//     print number_format(price2num($obj->amountpartida,'MT'),2);
		//     $aHtml[$i]['amount'] = $obj->amountpartida;
		//   }
		// else
		//   {
		//     print number_format(price2num($sumaprev,'MT'),2);
		//     $aHtml[$i]['amount'] = $sumaprev;
		//   }
		print price($obj->amount);
		print '</td>';

		if ($cClassall && $objwork->statut < 2 && empty($obj->fk_father))
		  print '<td nowrap align="center" style="background:#'.$cClassall.'">';
		else
		  print '<td nowrap align="center">';
		print $object->LibStatut($objprev->statut,0,1).'</td>';
		$aHtml[$i]['statut'] = $object->LibStatut($obj->statut,9,0);
		if ($idp)
		  $sumaAmount += $obj->amountpartida;
		
		if ($cClassall && $objwork->statut < 2 && empty($obj->fk_father))
		  print '<td align="left" style="background:#'.$cClassall.'">';
		else
		  print '<td align="left">';
		print '<a href="#" title="'.$nombre.'">'.$newNombre.'</a>';
		print '</td>';
		$aHtml[$i]['user'] = $newNombre;
		//hito instruction
		//instruction
		//buscamos la ultima instruccion si existe para el poa seleccionado

		$objectdd = new Poaactivitydet($db);
		$objectdd->getlist_code_date($obj->id);
		//armamos el code_procedure date
		foreach ((array) $aProc AS $code => $labelproc)
		  {
		    if ($cClassall && $objwork->statut < 2 && empty($obj->fk_father))
		      print '<td align="center" style="background:#'.$cClassall.'">';
		    else
		      print '<td align="left">';
		    print dol_print_date($objectdd->array[$code],'day');
		    print '</td>';
		  }
		$addClase = ''; 
		$addMessage = '';

		//followup
		$objectw->getlast($obj->id);
		$followup = '';
		$followto = '';
		$idworkflow = 0;
		if ($objectw->fk_activity == $obj->id)
		  {
		    $followup = $objectw->followup;
		    $followto = $objectw->followto;
		    $idworkflow = $objectw->id;
		  }
		$idTagps_ = $obj->id+200000;
		$idTagps2_ = $idTagps+200500;
		print '<td align="left">';
		if ($user->rights->poa->act->adds || $user->admin)
		  {
		    print '<span id="'.$idTagps_.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps_.'_poaa" type="text" name="followup" value="'.$followup.'" onblur="CambiarURLFrameup('.$obj->id.','.$idTagps_.','.'this.value);" size="25">'.'</span>';
		    print '<span  id="'.$idTagps2_.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps_.' , '.$idTagps2_.')">';
		    if (empty($followup))
		      print img_picto($langs->trans('Edit'),'edit');
		    else
		      print $followup;
		  }
		else
		  {
		    print '<span  id="'.$idTagps2_.'" style="visibility:visible; display:block;">';
		    if (empty($followup))
		      print img_picto($langs->trans('Edit'),'edit');
		    else
		      print $followup;
		    print '</span>';
		  }
		print '</td>';
		
		//followto
		$idTagps_ = $obj->id+300000;
		$idTagps2_ = $idTagps+300500;
		print '<td align="left">';
		if ($idworkflow)
		  {
		    if ($user->rights->poa->act->adds || $user->admin)
		      {
			print '<span id="'.$idTagps_.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps_.'_poaa" type="text" name="followto" value="'.$followto.'" onblur="CambiarURLFrameto('.$idworkflow.','.$idTagps_.','.'this.value);" size="25">'.'</span>';
			print '<span  id="'.$idTagps2_.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps_.' , '.$idTagps2_.')">';
			if (empty($followto))
			  print img_picto($langs->trans('Edit'),'edit');
			else
			  print $followto;
		      }
		    else
		      {
			print '<span  id="'.$idTagps2_.'" style="visibility:visible; display:block;">';
			if (empty($followto))
			  print img_picto($langs->trans('Edit'),'edit');
			else
			  print $followto;
			print '</span>';
		      }
		  }
		else
		  print '';
		print '</td>';
		
		if (empty($obj->fk_father))
		  {
		    // if ($conf->poai->enabled)
		    //   {
		    // 	$objinst->fetch_pre($obj->id);
		    // 	if ($objinst->fk_id == $obj->id)
		    // 	  {
		    // 	    $objinst->fk_id.' '.$obj->id;
			    
		    // 	    $idInst = $objinst->id;
		    // 	    $newClaseor = $newClase;
		    // 	    $detail = $objinst->detail;		      
		    // 	    //verificamos si tiene monitoreo por revisar
		    // 	    if ($objmoni->fetch_ult($obj->id,'PRE'))
		    // 	      {
		    // 		if ($objmoni->fk_id == $obj->id)
		    // 		  {
		    // 		    $idInst = $objmoni->fk_poai_instruction;
		    // 		    $addMessage = '&#13;'.$langs->trans('Monitoring').': '.$objmoni->detail;
		    // 		    if ($lStyle)
		    // 		      $newClase.= ' background:#12e539;';
		    // 		    else
		    // 		      $newClase.= '" style="background:#12e539;';
		    // 		  }
		    // 	      }
		    // 	    if ($cClassall && $objwork->statut < 2)
		    // 	      print '<td nowrap style="background:#'.$cClassall.'">';
		    // 	    else
		    // 	      print '<td nowrap class="'.$newClase.'">';
		    // 	    print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&backtopage=1&typeinst=PRE&id='.$idInst.'" title="'.trim($detail).': '.$langs->trans('Commitment date').' '.dol_print_date($objinst->commitment_date,'day').$addMessage.'">'.img_picto($langs->trans('Edit'),'next').' '.(strlen($detail)>11?substr($detail,0,5).'.':$detail).'</a>';
		    // 	    print '</td>';
		    // 	    $newClase = $newClaseor;
		    // 	  }
		    // 	else
		    // 	  {
		    // 	    if ($cClassall && $objwork->statut < 2)
		    // 	      print '<td nowrap style="background:#'.$cClassall.'">';
		    // 	    else
		    // 	      print '<td nowrap>';
		    // 	    if ($user->rights->poai->inst->crear)
		    // 	      print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&fk_user='.$obj->fk_user_create.'&action=create&typeinst=PRE'.'&backtopage=1">'.img_picto($langs->trans('Newinstruction'),'next').'</a>';
		    // 	    else
		    // 	      print '&nbsp;';
		    // 	    print '</td>';
		    // 	  }
		    //   }
		    print '<td></td>';
		  }
		else
		  {
		    print '<td></td>';
		  }

		//----------------------------------//
		//action
		if (empty($obj->fk_father) && $x)
		  {
		    if ($cClassall && $objwork->statut < 2)
		      print '<td nowrap align="right" style="background:#'.$cClassall.'">';
		    else 
		      print '<td align="right" nowrap>';
		    //se movio a la parte inicial del workflow		    
		    $message = $daydelay .'/'.$daydelayall;
		    if ($objwork->statut == 2 && $objwork->fk_poa_prev == $idPrev)
		      {
			$daydelay = 0;
			print '<a href="'.DOL_URL_ROOT.'/poa/workflow/fiche.php?id='.$objwork->id.'&idp='.$obj->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'" title="'.$langs->trans('View workflow').'">'.img_picto($langs->trans("View workflow").' '.$daydelayall,'tick').' '.$form->textwithpicto('',texthtmlworkflow($objworkact,$daydelay,$daydelayall),1,0,'','',3).' '.$daydelayall.'</a>';
			$aHtml[$i]['area'] = '';
			$aHtml[$i]['tiempo'] = $daydelayall;
		      }
		    else
		      {
			if ($daydelay > $nDay)
			  $cClass = $aColors[$nDay];
			if ($iniwork == 1 && $obj->statut != -1)//create
			  print '<a href="'.DOL_URL_ROOT.'/poa/workflow/fiche.php?action=create&fk_poa_prev='.$obj->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'" title="'.$langs->trans('Create workflow').'">'.img_picto($langs->trans("Create workflow"),DOL_URL_ROOT.'/poa/img/workflow.png','',1).'</a>';
			if ($iniwork == 2 && $obj->statut != -1) //edicion
			  print '<a href="'.DOL_URL_ROOT.'/poa/workflow/fiche.php?action=edit&id='.$objwork->id.'&fk_poa_prev='.$obj->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'" title="'.$langs->trans('Edit workflow').'">'.img_picto($langs->trans("Edit workflow"),DOL_URL_ROOT.'/poa/img/workflow.png','',1).'</a>';
			if ($iniwork == 3 && $obj->statut != -1) //vista
			  print '<a href="'.DOL_URL_ROOT.'/poa/workflow/fiche.php?id='.$objwork->id.'&idp='.$obj->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'" title="'.$langs->trans('View workflow').'">'.img_picto($langs->trans("View workflow").' '.$message,DOL_URL_ROOT.'/poa/img/workf.png','',1).' '.$form->textwithpicto('',texthtmlworkflow($objworkact,$daydelay,$daydelayall),1,0,'','',3).' '.$cArea.' '.$message.'</a>';
			$aHtml[$i]['area'] = $cArea;
			$aHtml[$i]['tiempo'] = $message;
		      }
		    print '&nbsp;';

		    $aHtml[$i]['cProcess'] = $cProcess;
		    $aHtml[$i]['cMessage'] = $cMessage;


		    //buscamos el proceso
		    $lViewcontrat = false;
		    if ($obj->statut != -1 && $x)
		      {
			$idPrev = $obj->id;
			if (!empty($obj->fk_prev_ant))
			  $idPrev = $obj->fk_prev_ant;
			$objproc->fetch_prev($idPrev);
			if ($objproc->fk_poa_prev == $idPrev)
			  {
			    $lViewcontrat = true;
			    $addMessage = '&nbsp;'.$langs->trans('Doc').': '.$objproc->ref.'/'.$objproc->gestion;
			    if ($obj->statut > 0)
			      print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche.php?id='.$objproc->id.'&idp='.$obj->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'" title="'.$langs->trans('Process initiated').$addMessage.'">'.img_picto($langs->trans("Process initiated").$addMessage,DOL_URL_ROOT.'/poa/img/process.png','',1).'</a>';
			  }
			else
			  if ( $user->admin || 
			       (!$user->admin && $user->id == $obj->fk_user_create))
			    {
			      $iniproceso = true;
			      if ($iniproceso)
				print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche.php?action=search&fk_poa_prev='.$obj->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'" title="'.$langs->trans('Create process').'">'.img_picto($langs->trans("Init process"),DOL_URL_ROOT.'/poa/img/noprocess.png','',1).'</a>';
			    }
		      }
		    else
		      {
			print '&nbsp;';
		      }
		    print '&nbsp;';
		    
		    //if ($objproc->fk_poa_prev == $obj->id && $obj->statut != -1)
		    if ($lViewcontrat && $obj->statut != -1)
		      {
			print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$objproc->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'">'.img_picto($langs->trans("Contract"),DOL_URL_ROOT.'/poa/img/comp.png','',1).'</a>';
			//payment
			if ($obj->statut >=2)
			  {
			    print '&nbsp;';
			    print '&nbsp;';
			    print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_pas2.php?id='.$objproc->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$idp:'').'">'.img_picto($langs->trans("Payments"),DOL_URL_ROOT.'/poa/img/deve.png','',1).'</a>';
			  }
		      }
		    else
		      print '&nbsp;';
		    print '</td>';
		  }
		else
		  print '<td></td>';
		print '</tr>';
	      }
	    $i++;
	  }
      }
    if ($idp)
      {
	//totales
	print '<tr class="liste_total"><td colspan="5">'.$langs->trans("Total").'</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';
	
	print '<td align="right">';
	print price($sumaAmount);
	print '</td>';
	print '<td colspan="3">';
	print '&nbsp;';
	print '</td>';
	print '</tr>';
      }
    else
      {
	print '<tr class="black liste_total">';
	print '<td colspan="3" align="right">'.$langs->trans("Total").'</td>';
	print '<td align="center">';
	print $nCountPrev;
	print '</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';

	print '<td align="right">';
	print price($nTotalPrev);
	print '</td>';
	print '<td colspan="4">';
	print '&nbsp;';
	print '</td>';
	print '</tr>';
      }

    print '</table>';
    print '</form>';
    $_SESSION['aHtmlprev'] = $aHtml;
    $db->free($result);

    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if (empty($idp))
	  {

	    if ($user->rights->poa->poa->crear)
	      print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	    else
	      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	  }
	else
	  {
	    if ($user->rights->poa->poa->leer)
	      print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php">'.$langs->trans("Liste POA").'</a>';
	    else
	      print '<a class="butActionRefused" href="#">'.$langs->trans("Createnew").'</a>';
	  }
	if (!empty($idpa))
	    print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/pac/liste.php">'.$langs->trans("Return").'</a>';
	
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/fiche_excel.php">'.$langs->trans("Excel").'</a>';

	print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/index.php">'.$langs->trans("Returnmenu").'</a>';
      }

    print '</div>';
  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
