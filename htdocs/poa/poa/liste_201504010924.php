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
 *      \file       htdocs/poa/poa/liste.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page liste des poa
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
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
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaactivityprev.class.php");

if ($conf->poai->enabled)
  {
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
  }
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");

$langs->load("poa@poa");
if ($conf->poai->enabled)
  $langs->load("poai@poai");

$outputlangs = $langs;
$monthArray = monthArray($outputlangs);
if (!$user->rights->poa->poa->leer)
  accessforbidden();

$object     = new Poapoa($db);
$objpre     = new Poaprev($db);
$objprev    = new Poapartidapre($db);
$objcomp    = new Poapartidacom($db);
$objdeve    = new Poapartidadev($db);
$objrefo    = new Poareformulated($db);
$objrefodet = new Poareformulateddet($db);
$objpac     = new Poapac($db);
$objproc    = new Poaprocess($db);
$objprocc   = new Poaprocesscontrat($db);
$objrefoof  = new Poareformulatedof($db);
$objrefoto  = new Poareformulatedto($db);
$objectuser = new Poapoauser($db);
$objuser    = new User($db);
$objstr     = new Poastructure($db);
$objact     = new Poaactivity($db);
$objactdet  = new Poaactivitydet($db);
$objwork    = new Poaworkflow($db);
$objacpr    = new Poaactivityprev($db);

if ($conf->poai->enabled)
  {
    $objinst = new Poaiinstruction($db);
    $objmoni = new Poaimonitoring($db);
  }
$id = GETPOST('id');
$action = GETPOST('action');
if (isset($_GET['mostrar']))
  $_SESSION['opver'] = '';
if (isset($_GET['opver']))
  $_SESSION['opver'] = $_GET['opver'];
$opver = $_SESSION['opver'];

//asignando filtro de usuario
assign_filter_user('search_user');

$action = GETPOST('action');
//gestion
$gestion = GETPOST('gestion');

//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];

//gestion definida en index.php
if (empty($_SESSION['gestion']))
  $_SESSION['gestion'] = date('Y');

$gestion = $_SESSION['gestion'];

$aUser = array_all_user(1,$name='login'); //recupera todos los usuarios

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
    $object->gestion   = $gestion;
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
	    header("Location: liste.php");
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
$object->get_maxmin($gestion,'M');
//dividimos en 5
$maximo = $object->maxmin;
$object->get_maxmin($gestion,'m');
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
	      91=>92,
	      92=>91,
	      93=>94,
	      94=>93,
	      71=>array(72,73),
	      72=>array(71,73),
	      73=>array(71,72),
	      81=>array(82,83,84,85),
	      82=>array(81,83,84,85),
	      83=>array(81,82,84,85),
	      84=>array(81,82,83,85),
	      85=>array(81,82,83,84));
$aBalance = array(1=>array(0,40),
		  2=>array(41,70),
		  3=>array(71,100));

$aImageFondo = array(1=>'bajo.png',
		  2=>'centro.png',
		  3=>'alto.png');

$aVal = array(1=>array(1,20000),
	      2=>array(20001,200000),
	      3=>array(200001,10000000));


if (empty($_SESSION['numCol']))
  {
    $_SESSION['numCol'] = array(1=>true,
				2=>false,
				3=>true,
				4=>false,
				7=>true,
				8=>false,
				9=>true,
				10=>false,
				11=>true,
				12=>false,
				13=>true,
				14=>false,
				51=>false,
				52=>true,
				61=>false,
				62=>true,
				71=>true,
				72=>false,
				73=>false,
				81=>true,
				82=>false,
				83=>false,
				84=>false,
				85=>false,
				91=>true,
				92=>false,
				93=>true,
				94=>false,
				);
    
  }
if(isset($_GET['vercol']) || isset($_POST['vercol']))
  {
    $_SESSION['numCol'][$vercol] = true;
    if (is_array($aCol[$vercol]))
      {
	foreach($aCol[$vercol] AS $i1 => $nCol1)
	  {
	    $_SESSION['numCol'][$nCol1] = false;
	  }
      }
    else
      $_SESSION['numCol'][$aCol[$vercol]] = false;
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
if (isset($vercol) && $vercol == 8)
  {
    $_SESSION['numColdevid'] = ($_SESSION['numCol'][13]?13:($_SESSION['numCol'][14]?14:($_SESSION['numCol'][17]?17:0)));
    $_SESSION['numCol'][13] = false;
    $_SESSION['numCol'][14] = false;
    $_SESSION['numCol'][17] = false;
    $_SESSION['numCol'][93] = false;
    
  }
if (isset($vercol) && $vercol == 7)
  {
    $_SESSION['numCol'][13] = true;
    $_SESSION['numCol'][14] = false;
    $_SESSION['numCol'][17] = false;
    $_SESSION['numCol'][93] = true;
  }

$aColorUser = $_SESSION['aColorUser'];

if ($_SESSION['numCol'][8] == true)
  $opver = true;
if ($_SESSION['numCol'][7] == true)
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
  $_SESSION['search_sigla'] = $_POST['search_sigla'];
if (isset($_POST['search_label']))
  $_SESSION['search_label'] = $_POST['search_label'];
if (isset($_POST['search_pseudonym']))
  $_SESSION['search_pseudonym'] = $_POST['search_pseudonym'];
if (isset($_POST['search_partida']))
  $_SESSION['search_partida'] = $_POST['search_partida'];
if (isset($_POST['search_amount']))
  $_SESSION['search_amount'] = $_POST['search_amount'];
if (isset($_POST['search_reform']))
  $_SESSION['search_reform'] = $_POST['search_reform'];
if (isset($_POST['search_user']))
  {
    if ($_POST['search_user'] < 0)
      $_POST['search_user'] ='';
    $_SESSION['search_user'] = STRTOUPPER($_POST['search_user']);
  }

if (isset($_POST['nosearch_x']))
  {
    $search_sigla     = '';
    $search_label     = '';
    $search_pseudonym = '';
    $search_partida   = '';
    $search_amount    = '';
    $search_reform    = '';
    $search_user      = '';
    $_SESSION['search_sigla'] = '';
    $_SESSION['search_pseudonym'] = '';
    $_SESSION['search_label'] = '';
    $_SESSION['search_partida'] = '';
    $_SESSION['search_amount'] = '';
    $_SESSION['search_reform'] = '';
    $_SESSION['search_user'] = '';
  }
$search_sigla     = $_SESSION["search_sigla"];
$search_label     = $_SESSION["search_label"];
$search_pseudonym = $_SESSION["search_pseudonym"];
$search_partida   = $_SESSION["search_partida"];
$search_amount    = $_SESSION["search_amount"];
$search_reform    = $_SESSION["search_reform"];
$search_user      = $_SESSION["search_user"];

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

$sql  = "SELECT p.rowid AS id, p.gestion, p.fk_structure, p.label, p.pseudonym, p.partida, p.amount, p.classification, p.source_verification, p.unit,p.statut, ";
$sql.= " p.weighting, p.version, ";
$sql.= " p.m_jan, p.m_feb, p.m_mar, p.m_apr, p.m_may, p.m_jun, p.m_jul, p.m_aug, p.m_sep, p.m_oct, p.m_nov, p.m_dec, ";
$sql.= " p.p_jan, p.p_feb, p.p_mar, p.p_apr, p.p_may, p.p_jun, p.p_jul, p.p_aug, p.p_sep, p.p_oct, p.p_nov, p.p_dec, ";
$sql.= " s.label AS labelstructure, s.sigla ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON p.fk_structure = s.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.gestion = ".$gestion;
if ($_SESSION['sel_area'])
  $sql.= " AND s.fk_area = ".$_SESSION['sel_area'];

$sql.= " AND p.statut = 1 "; //solo los aprobados

if ($search_sigla)   $sql .= " AND s.sigla LIKE '%".$db->escape($search_sigla)."%'";
if ($search_label)   $sql .= " AND p.label LIKE '%".$db->escape($search_label)."%'";
if ($search_pseudonym)   $sql .= " AND p.pseudonym LIKE '%".$db->escape($search_pseudonym)."%'";
if ($search_partida)   $sql .= " AND p.partida LIKE '%".$db->escape($search_partida)."%'";
if ($search_amount)   $sql .= " AND p.amount LIKE '%".$db->escape($search_amount)."%'";
if ($idsArea)
  $sql.= " AND s.fk_area IN ($idsArea)";
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";
}
//order
$sql.= " ORDER BY p.version, s.sigla, p.partida ";
//$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($limit+1, $offset);
$result = $db->query($sql);

$form=new Form($db);

$numCol = $_SESSION['numCol'];
if ($result)
  {
    $num = $db->num_rows($result);
    //recuperamos las partidas de la gestion
    $aPartida = get_partida($gestion);
    //verificamos que version de reformulado existe
    $objrefo->fetch_version($gestion);
    if ($objrefo->gestion == $gestion)
      {
	$lVersion = true;
	$nVersion = $objrefo->version;
	$fk_poa_ref = $objrefo->id;
	$aReform[$objrefo->id] = $objrefo->id;
	list($aOf,$aOfone,$aOfref) = $objrefodet->get_sumaref($aReform);
      }
    else
      {
	//buscamos el numero de la nueva reformulacion
     $objap = new Poareformulated($db);
     $objap->fetch_version($gestion,1);
     if ($objap->gestion == $gestion)
       $nVersion = $objap->version + 1;
     else
       $nVersion = 1;
      }
    //obtenemos las modificaciones aprobadas
    $objrefo->fetch_version_gestion($gestion);
    $aReform = array();
    foreach ((array) $objrefo->array AS $fkid => $obj_ref)
      {
	if ($obj_ref->gestion == $gestion)
	  {
	    $lVersionAp = true;
	    $nVersionAp = $obj_ref->version;
	    //$fk_poa_ref = $obj_ref->id;
	    $aReform[$obj_ref->id] = $obj_ref->id;
	  }
      }
    if (count($aReform)>0)
      list($aOfa,$aOfonea,$aOfrefa) = $objrefodet->get_sumaref($aReform);

    $i = 0;
    $aArrcss= array('poa/css/style.css');
    $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js');
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste POA"),$help_url,'','','',$aArrjs,$aArrcss);
    
    print_barre_liste($langs->trans("Liste POA"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    //filtro
    print '<form name="fo3" method="POST" id="fo3" onsubmit="enviareform(); return false" action="'.$_SERVER["PHP_SELF"].'">'."\n";
    print '<input type="hidden" name="gestion" value="'.$gestion.'">';

    //modificado frame
?>
<script type="text/javascript">
   function CambiarURLFrame(id,fk_structure,fk_poa_poa,partida,gestion,idReg,reform){
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
  document.getElementById('iframe').src= 'actualiza_reform.php?id='+id+'&fk_structure='+fk_structure+'&fk_poa_poa='+fk_poa_poa+'&partida='+partida+'&action=create&gestion='+gestion+'&amount='+amount+'&reform='+reform;
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

<?php
    //fin modificado frame
    // Filter on categories
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
    
    //inicio
    print '<div class="master">';
    print '<section id="section-head">';

    //init comandos ocultar mostrar
    print '<span>';
    print '<div id="meta" class="left title"></div>';
    if ($numCol[1]==true)
      {
	print '<div id="label" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="2">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';

	print '</div>';
      }
    if ($numCol[2]==true)
      {
	print '<div id="pseudo" class="left yellowblack">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="1">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '</div>';
      }
    //partida
    print '<div id="partida" class="left title">';
    if ($numCol[61])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="62">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[62])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="61">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    print '</div>';

    //presupuesto
    print '<div id="amount" class="left center title">';
    if ($numCol[91])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="92">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[92])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="91">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    print '</div>';

    //reformulacion y total aprobado
    if ($numCol[71])
      {
	print '<div id="reform" class="left title yellowblack">';

	// print '<button class="btn_trans" type="submit" name="editref">'.img_picto($langs->trans('edit'),DOL_URL_ROOT.'/poa/img/edit.png','',1).'</button>';
	// print '&nbsp;';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="72">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '</div>';
	print '<div id="amount" class="left center title">';
	
      }
    if ($numCol[72])
      {
	print '<div id="reform" class="left center title">';

      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="73">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
      }
    if ($numCol[73])
      {
	print '<div id="reform" class="left center title">';

      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="71">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
      print '&nbsp;';
      //7 total presup
      if ($numCol[7])
	{
	  print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="8">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	}
      if ($numCol[8])
	{
	  print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="7">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	}

      }
    print '</div>';
    // if ($lVersion)
    //   {
    // 	print '<div id="amount" class="left title">';
    // 	//print '&nbsp;';
    // 	print '</div>';
    //   }

    // if ($opver == true)
    //   {
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    // 	print '<div id="amounttwo" class="left title"></div>';
    //   }

    if ($numCol[9]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="10">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[10]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="15">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[15]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="9">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[11]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="12">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[12]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="16">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[16]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="11">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '</div>';
      }

    if ($numCol[13]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="14">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[14]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="17">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	print '</div>';
      }
    if ($numCol[17]==true)
      {
	print '<div id="amount" class="left title">';
	print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="13">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '</div>';
      }
    //modificado para las fechas
    if ($numCol[73])
      {
	if ($opver == true)
	  {
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	    print '<div id="amounttwo" class="left title"></div>';
	  }
      }
    
    //user
    print '<div id="user" class="left title">';
    if ($numCol[51])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="52">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[52])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="51">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';

    print '</div>';

    //instruction
    if ($conf->poai->enabled)
      {
	if ($numCol[93])
	  print '<div id="instruction" class="left title"></div>';    
      }
    //pac
    //    print '<div id="pac" class="left title"></div>';    
    print '<div id="pac" class="left title">';
    if ($numCol[81])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="82">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[82])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="83">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[83])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="84">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[84])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="85">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    if ($numCol[85])
      print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="81">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
    print '</div>';
    //action
    print '<div id="action" class="left title"></div>';    

    print '<div class="clear"></div>';
   
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListTitle',$parameters);
    // Note that $action and $object may have been modified by hook    
    print '</span>';

    //nueva fila TITULOS
    print '<span>';

    print '<div id="meta" class="left title">';
    print $langs->trans("Meta");
    print '</div>';    

    if ($numCol[1]==true)
      {
	print '<div id="label" class="left title">';
	print $langs->trans("Label");
	print '</div>';
      }
    if ($numCol[2]==true)
      {
	print '<div id="pseudo" class="left yellowblack">';
	print $langs->trans("Pseudonym");
	print '</div>';
      }

    //partida
    print '<div id="partida" class="left title">';
    print $langs->trans('Partida');
    print '</div>';

    //presupuesto
    print '<div id="amount" class="left title">';
    if ($numCol[91])
      print $langs->trans('Initialbudget');
    if ($numCol[92])
      print $langs->trans('Approved budget');
    print '</div>';

    if ($numCol[71])
      {
	if ($lVersion)
	  {
	    print '<div id="reform" class="left yellowblack">';
	    print $langs->trans('Reformulated').' '.$nVersion;
	    print '</div>';
	    print '<div id="reform" class="left yellowblack">';
	    print $langs->trans('N. Reform');
	    print '</div>';
	  }
	else
	  {
	    print '<div id="reform" class="left yellowblack">';
	    print $langs->trans('Reformulated').' '.$nVersion;
	    print '</div>';
	    print '<div id="reform" class="left yellowblack">';
	    print $langs->trans('N. Reform');
	    print '</div>';
	  }
      }

    if ($numCol[72])
      {
	//7 total presup
	if ($numCol[7]==true)
	  {
	    print '<div id="amount" class="left title">';
	    print $langs->trans("Pending approval").' '.$nVersion;
	    print '</div>';
	  }
	else
	  {
	    print '<div id="amount" class="left title">';
	    print $langs->trans("Approved budget").' '.$nVersion;
	    print '</div>';
	  }
      }
    if ($numCol[73])
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Porcent");
	print '</div>';
      }
    // if ($opver == true)
    //   {
    // 	print '<div id="amountone" class="left title">'.$langs->trans('En').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Fe').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Ma').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Ap').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('My').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Ju').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Jl').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Au').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Se').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('Oc').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('No').'</div>';
    // 	print '<div id="amountone" class="left title">'.$langs->trans('De').'</div>';
    //   }

    if ($numCol[9]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Preventive");
	print '</div>';
      }
    if ($numCol[10]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Preventive").'<br>'.'%';
	print '</div>';
      }
    if ($numCol[15]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Balance");
	print '</div>';
      }
    if ($numCol[11]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Committed");
	print '</div>';
      }
    if ($numCol[12]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Committed").'<br>'.'%';
	print '</div>';
      }
    if ($numCol[16]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Balancecommitted");
	print '</div>';
      }

    if ($numCol[13]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Accrued");
	print '</div>';
      }
    if ($numCol[14]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Accrued").'<br>'.'%';
	print '</div>';
      }
    if ($numCol[17]==true)
      {
	print '<div id="amount" class="left title">';
	print $langs->trans("Balanceaccrued");
	print '</div>';
      }
    //modificado para las fechas
    if ($numCol[73])
      {
	if ($opver == true)
	  {
	    print '<div id="amountone" class="left title">'.$langs->trans('En').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Fe').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Ma').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Ap').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('My').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Ju').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Jl').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Au').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Se').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('Oc').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('No').'</div>';
	    print '<div id="amountone" class="left title">'.$langs->trans('De').'</div>';
	  }

      }
    //user
    print '<div id="user" class="left title">';
    print $langs->trans("User");
    print '</div>';    

    //instruction
    if ($conf->poai->enabled)
      {
	if ($numCol[93])
	  {
	    print '<div id="instruction" class="left title">';
	    print $langs->trans("Hito");
	    print '</div>';
	  }
      }
    //pac
    if ($numCol[81]==true)
      {
	print '<div id="pac" class="left title">';
	print $langs->trans("PAC");
	print '</div>';
      }
    if ($numCol[82]==true)
      {
	print '<div id="pac" class="left title">';
	print $langs->trans("PAC Ini");
	print '</div>';
      }
    if ($numCol[83]==true)
      {
	print '<div id="pac" class="left title">';
	print $langs->trans("PAC Pub");
	print '</div>';
      }
    if ($numCol[84]==true)
      {
	print '<div id="pac" class="left title">';
	print $langs->trans("PAC Total");
	print '</div>';
      }
    if ($numCol[85]==true)
      {
	print '<div id="pac" class="left title">';
	print $langs->trans("PAC Saldo");
	print '</div>';
      }
    
    //action
    print '<div id="action" class="left title">';
    print $langs->trans("Action");
    print '</div>';    

    print '<div class="clear"></div>';

    print '</span>';

    //nueva fila filtros
    print '<span>';    
    print '<div id="meta" class="left title"><input type="text" class="flat" size="3" name="search_sigla" value="'.$search_sigla.'"></div>';
    if ($numCol[1])
      print '<div id="label" class="left title"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="37"></div>';
    if ($numCol[2])
      print '<div id="pseudo" class="left title"><input type="text" class="flat" name="search_pseudonym" value="'.$search_pseudonym.'" size="37"></div>';
    
    print '<div id="partida" class="left title">';
    print '<input class="flat" type="text" size="3" name="search_partida" value="'.$search_partida.'">';
    print '</div>';
    
    //presupuesto
    print '<div id="amount" class="left title">';
    print '</div>';
    if ($numCol[71])
      {
	print '<div id="amount" class="left title">';
	print '</div>';
	print '<div id="amount" class="left title">';
	print '<input class="flat" type="text" size="8" name="search_reform" value="'.$search_reform.'">';
	print '</div>';
      }
    if ($numCol[72])
      {
	print '<div id="amount" class="left title">';
	print '</div>';
      }
    if ($numCol[73])
      {
	print '<div id="amount" class="left title">';
	print '</div>';
      }
    // if ($opver == 1)
    //   {
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    // 	print '<div id="month" class="left title"></div>';
    //   }
    print '<div id="amount" class="left title"></div>';
    print '<div id="amount" class="left title"></div>';
    if ($opver != 1)
      print '<div id="amount" class="left title"></div>';
    // print '<div id="amount" class="left title"></div>';
    // print '<div id="amount" class="left title"></div>';
    if ($opver == 1)
      {
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
    	print '<div id="month" class="left title"></div>';
      }

    //user
    print '<div id="user" class="left title">';
    print '<input class="flat" type="text" size="7" name="search_user" value="'.$search_user.'">';
    // $aExcluded = array(1=>1);
    // print $form->select_dolusers($search_user,'search_user',1,$aExcluded,'','','','',9);

    //    print $form->selectarray('search_user',$aUser);
    print '</div>';

    //instruction
    if ($conf->poai->enabled)
      if ($numCol[93])
	print '<div id="instruction" class="left title"></div>';
    //pac
    print '<div id="pac" class="left title"></div>';
    //pac

    //action
    print '<div id="action" class="left title">';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '&nbsp;';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
    print '</div>';
    
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    print '<div class="clear"></div>';
    print '</span>';

    print '</section>';
    
    //cuerpo
    print '<section id="section-body">';
    print '<aside id="aside-body">';
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
    	while ($i < min($num,$limit))
    	  {
	    $ii++;
    	    $obj = $db->fetch_object($result);
	    if ($action == 'edit' && $obj->id == $id)
	      $object = $obj;
	    //verifica usuario activo
	    //$newNombre = user_active_poa($obj); //poa.lib.php
	    $idUser     = userid_active_poa($obj); //poa.lib.php
	    $newNombre  = '';
	    $nombre     = '';
	    $nombreslog = '';
	    $filteruser = false;
	    if ($idUser && ($objuser->fetch($idUser) > 0))
	      {
		$newNombre = $objuser->login;
		$nombre = $objuser->firstname;
		$nombreslog = $objuser->firstname.' '.$objuser->lastname.' '.$objuser->login;
		if (!empty($search_user))
		  $filteruser = STRPOS(STRTOUPPER($nombreslog),$search_user);
	      }
	    //echo '<br>|'.STRPOS(STRTOUPPER($nombreslog),$search_user).'|'.strtoupper($nombreslog).'|'.$search_user.'|'.$filteruser.'|';
	    $lContinue = true;
	    //filtro de search_reform
	    //echo '<br>|'.STRPOS(STRTOUPPER($newNombre),$search_user).'|'.$newNombre.'|'.$search_user.'|';
	    if ((!empty($search_reform) && $search_reform != $aOfref[$obj->fk_structure][$obj->id][$obj->partida]) ||
		(!empty($search_user) && $filteruser===false))
	      {
		$lContinue = false;
		$ii--;
	      }
	    if ($lContinue)
	      {
    	    $var=!$var;
	    if ($var) $backg = "pair"; else $backg = "impair";
	    $newClase = ' class="left '.$backg.'';
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
	    //inicio
    	    print "<div>";
    	    print '<div id="meta" '.$newClase.'">'.'<a href="#" title="'.$obj->labelstructure.'">'.$obj->sigla.'</a>'.'</div>';
	    $aHtml[$i]['sigla'] = $obj->sigla;
	    if ($numCol[1])
	      {
    	      print '<div id="label" '.$newClase.'">'.(strlen($obj->label)>30?'<a href="#" title="'.$obj->label.'">'.substr($obj->label,0,30).'...</a>':$obj->label).'</div>';
	      }
	    $aHtml[$i]['label'] = $obj->label;
	    $aHtml[$i]['pseudonym'] = $obj->pseudonym;

	    if ($numCol[2])
	      {
		print '<div id="pseudo" '.$newClase.'">';
		$idTagps = $obj->id+100000;
		$idTagps2 = $idTagps+100500;
		if ($user->rights->poa->poa->mod || $user->admin)
		  {
		    print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_poaa" type="text" name="pseudonym" value="'.$obj->pseudonym.'" onblur="CambiarURLFrametwo('.$obj->id.','.$idTagps.','.'this.value);" size="36">'.'</span>';
		    
		    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps.' , '.$idTagps2.')">';
		    print (strlen($obj->pseudonym)>30?'<a href="#" title="'.$obj->pseudonym.'">'.substr($obj->pseudonym,0,30).'...</a>':(empty($obj->pseudonym)?'&nbsp;':$obj->pseudonym));
		    print '</span>';
		  }
		else
		  {
		    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
		    print (strlen($obj->pseudonym)>30?'<a href="#" title="'.$obj->pseudonym.'">'.substr($obj->pseudonym,0,30).'...</a>':(empty($obj->pseudonym)?'&nbsp;':$obj->pseudonym));
		    print '</span>';
		  }
		
		//		print (strlen($obj->pseudonym)>30?'<a href="#" title="'.$obj->pseudonym.'">'.substr($obj->pseudonym,0,30).'...</a>':$obj->pseudonym);
		print '</div>';
	      }
	    $aHtml[$i]['partida'] = $obj->partida;
    	    print '<div id="partida" '.$newClase.'">'.'<a href="#" title="'.$aPartida[$obj->partida].'">'.$obj->partida.'</a>'.'</div>';

	    //presupuesto
	    $nPresup = 0;
	    if ($obj->version == 0)
	      {
		$aHtml[$i]['presupuesto'] = $obj->amount;
		$sumaPresup+=$obj->amount;
		$nPresup = $obj->amount;
	      }

	    if ($numCol[91])
	      {
		if ($obj->version == 0)
		  print '<div id="amount" '.$newClase.'">'.number_format(price2num($obj->amount,'MT'),2).'</div>';
		else
		  print '<div id="amount" '.$newClase.'">&nbsp;</div>';
	      }
	    $nReformap = $aOfa[$obj->fk_structure][$obj->id][$obj->partida];
	    $nTotalAp = $nPresup+$nReformap;
	    //$nTotalAp = $nPresup;
	    $sumaAprob+=$nTotalAp;

	    if ($numCol[92])
	      {
		$aHtml[$i]['nTotalAp'] = $nTotalAp;
		print '<div id="amount" '.$newClase.'">'.price(price2num($nTotalAp,'MT')).'</div>';
	      }

	    if ($numCol[71])
	      {
		if ($lVersion)
		  {
		    //buscamos que suma y que resta		
		    // $nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida] - 
		    //   $aTo[$obj->fk_structure][$obj->id][$obj->partida];
		    $nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida];
		    $reformtext = $aOfref[$obj->fk_structure][$obj->id][$obj->partida];
		    $idReform = $aOfone[$obj->fk_structure][$obj->id][$obj->partida];
		    $idReform+=0;
		    $idTag1 = $obj->id;
		    $idTag2 = $obj->id * 100000;
		    $aHtml[$i]['reform'] = $nReform;

		    print '<div id="amount" '.$newClase.'">';
		    if ($user->rights->poa->poa->mod || $user->admin)
		      {
			print '<span id="'.$idTag1.'" style="visibility:hidden; display:none;">'.'<input id="'.$obj->id.'_am" type="number" name="reform['.$idReform.']['.$obj->fk_structure.']['.$obj->id.']['.$obj->partida.']" value="'.price2num($nReform).'" size="7">'.'<input id="'.$obj->id.'_ap" type="hidden" name="reformx" value="'.price2num($nReform).'">'.'</span>';
			
			print '<span  id="'.$idTag2.'" style="visibility:visible; display:block;" onclick="visual_one('.$idTag1.' , '.$idTag2.')">'.price(price2num($nReform,'MT')).'</span>';
		      }
		    else
		      print '<span  id="'.$idTag2.'" style="visibility:visible; display:block;">'.price(price2num($nReform,'MT')).'</span>';

		    print '</div>';
		    $sumaRef1 += $nReform;
		    $aHtml[$i]['reformtext'] = $reformtext;

		    //numero de reformulado
		    print '<div id="amount" '.$newClase.'">';

		    print '<span id="'.$idTag1.'_'.'" style="visibility:hidden; display:none;">'.'<input type="text" name="reformtext" size="7" onblur="CambiarURLFrame('.$idReform.','.$obj->fk_structure.','.$obj->id.','.$obj->partida.','.$gestion.','.$obj->id.','.'this.value);" value="'.$reformtext.'">'.'</span>';

		    print '<span id="'.$idTag2.'_'.'" style="visibility:visible; display:block;" onclick="visual_one('.$idTag1.' , '.$idTag2.')">'.(empty($reformtext)?'&nbsp;':$reformtext).'</span>';
		    print '</div>';
		  }
		else
		  {
		    print '<div id="amount" '.$newClase.'">&nbsp;</div>';
		    print '<div id="amount" '.$newClase.'">&nbsp;</div>';
		  }
	      }

	    if ($numCol[72])
	      {
		$nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida];
		$aHtml[$i]['nTotalAppen'] = $nTotalAp+$nReform;		
		print '<div id="amount" '.$newClase.'">'.price(price2num($nTotalAp+$nReform,'MT')).'</div>';
	      }
	    if ($numCol[73])
	      {
		//total reformulado pendiente
		$nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida];
	    	print '<div id="amount" '.$newClase.'">'.($nTotalAp>0?price(price2num($nReform/$nTotalAp*100,'MT')):'').'</div>';
	      }

    	    // if ($opver == 1)
    	    //   {
	    // 	$newClaseor = $newClase;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_jan);
	    // 	if ($obj->p_jan) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_jan)?'<a href="#">'.img_picto(price($obj->m_jan),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_feb);
	    // 	if ($obj->p_feb) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_feb)?'<a href="#">'.img_picto(price($obj->m_feb),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_mar);
	    // 	if ($obj->p_mar) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_mar)?'<a href="#">'.img_picto(price($obj->m_mar),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_apr);
	    // 	if ($obj->p_apr) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_apr)?'<a href="#">'.img_picto(price($obj->m_apr),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_may);
	    // 	if ($obj->p_may) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_may)?'<a href="#">'.img_picto(price($obj->m_may),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_jun);
	    // 	if ($obj->p_jun) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_jun)?'<a href="#">'.img_picto(price($obj->m_jun),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_jul);
	    // 	if ($obj->p_jul) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_jul)?'<a href="#">'.img_picto(price($obj->m_jul),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_aug);
	    // 	if ($obj->p_aug) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_aug)?'<a href="#">'.img_picto(price($obj->m_aug),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_sep);
	    // 	if ($obj->p_sep) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_sep)?'<a href="#">'.img_picto(price($obj->m_sep),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_oct);
	    // 	if ($obj->p_oct) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_oct)?'<a href="#">'.img_picto(price($obj->m_oct),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_nov);
	    // 	if ($obj->p_nov) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_nov)?'<a href="#">'.img_picto(price($obj->m_nov),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;
	    // 	$iGrafico = tipo_grafico($aLimite,$obj->m_dec);
	    // 	if ($obj->p_dec) $newClase = ' class="left product"';
    	    // 	print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_dec)?'<a href="#">'.img_picto(price($obj->m_dec),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
	    // 	$newClase = $newClaseor;

	    // 	$sumaEne+=$obj->m_ene;
	    // 	$sumaFeb+=$obj->m_feb;
	    // 	$sumaMar+=$obj->m_mar;
	    // 	$sumaApr+=$obj->m_apr;
	    // 	$sumaMay+=$obj->m_may;
	    // 	$sumaJun+=$obj->m_jun;
	    // 	$sumaJul+=$obj->m_jul;
	    // 	$sumaAug+=$obj->m_aug;
	    // 	$sumaSep+=$obj->m_sep;
	    // 	$sumaOct+=$obj->m_oct;
	    // 	$sumaNov+=$obj->m_nov;
	    // 	$sumaDec+=$obj->m_dec;

    	    //   }
	    $total = 0;
	    $balance=0;
    	    if ($objprev->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
    	      {
		if($numCol[9])
		  {
		    $newClaseor = $newClase;
		    $nFondo = 0;
		    $total = $objprev->total;
		    if ($total >=0)
		      $nFondo = porcGrafico($aVal,$total);
		    if ($nFondo)
		      {
		
			if ($lStyle)
			  {
			    if ($_SESSION['colorUser'] == true)
			      {
				// $newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
				$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
			      }
			    if ($_SESSION['colorPartida'] == true)
			      {
				$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
			      }

			  }
			else
			  {
			    $newClase.= ' '.'imgval'.$nFondo;
			  }
		      }

		    $aHtml[$i]['preventivo'] = $objprev->total;

		    print '<div id="amount" '.$newClase.'">';
		    //	    print '<a href="'.DOL_URL_ROOT.'/poa/execution/liste.php?nopac=1&idp='.$obj->id.'">';
		    print '<a id="miEnlace'.$obj->id.'" href="javascript:toggleEnlace('."'".'mostrar'."'".', '.$obj->id.','.$objprev->total.')">';
		    print price(price2num($objprev->total,'MT'));
		    print '</a>';
		    print '</div>';
		    $newClase = $newClaseor;

		  }
		if($numCol[10])
		  {
		    
		    if ($nTotalAp > 0)
		      $total = $objprev->total / $nTotalAp * 100;
		    else
		      $total = 0;

		    $nFondo = 0;
		    $newClaseor = $newClase;

		    if ($total >=0)
		      $nFondo = porcGrafico($aBalance,$total);
		    if ($nFondo)
		      {

			if ($lStyle)
			  {
			    if ($_SESSION['colorUser'] == true)
			      {
				// $newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
				$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
			      }
			    if ($_SESSION['colorPartida'] == true)
			      {
				$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
			      }

			    
			  }
			else
			  {
			    $newClase.= ' '.'imgfondo'.$nFondo;
			  }
		      }
		    
		    print '<div id="amount" '.$newClase.'">'.price(price2num($total,'MT')).' %'.'</div>';
		    $newClase = $newClaseor;
		  }
		$balance = $nTotalAp - $objprev->total;
		$newClaseor = $newClase;
		$nFondo = '';
		if ($balance < 0)
		  {
		    if ($lStyle)
		      $newClase.= ' color:#ff0000;';
		    else
		      $newClase.= '" style="color:#ff0000;';
		  }
		if ($numCol[15])
		    print '<div id="amount" '.$newClase.'">'.price(price2num($balance,'MT')).'</div>';
		$newClase = $newClaseor;
    		$total = $objprev->total;

    	      }
    	    else
    	      {
		$listaPrev = '';
    		$total = 0;
    		print '<div id="amount" '.$newClase.'">&nbsp;</div>';
    	      }
	    $sumaPrev+=$total;
	    $sumaPrevm+= $objprev->total;
	    $sumaSaldop+=$balance;

	    //comprometido
	    $totalc = 0;
	    $balancec = 0;
	    if ($objcomp->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	      {
		if($numCol[11])
		  {
		    $totalc = $objcomp->total;

		    $newClaseor = $newClase;
		    if ($totalc >=0)
		      $nFondo = porcGrafico($aVal,$totalc);
		    if ($nFondo)
		      {
		
			if ($lStyle)
			  {
			    if ($_SESSION['colorUser'] == true)
			      {
				// $newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
				$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
			      }
			    if ($_SESSION['colorPartida'] == true)
			      {
				$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
			      }

			  }
			else
			  {
			    $newClase.= ' '.'imgval'.$nFondo;
			  }
		      }
		    $aHtml[$i]['comprometido'] = $objcomp->total;

		    print '<div id="amount" '.$newClase.'">'.price(price2num($objcomp->total,'MT')).'</div>';
		$newClase = $newClaseor;

		  }
		if($numCol[12])
		  {
		    //porcent compro 
		    if ($nTotalAp > 0)
		      $totalc = $objcomp->total / $nTotalAp * 100;
		    else
		      $totalc = 0;

		    $nFondo = 0;
		    $newClaseor = $newClase;

		    if ($totalc >=0)
		      $nFondo = porcGrafico($aBalance,$totalc);
		    if ($nFondo)
		      {

			if ($lStyle)
			  {
			    if ($_SESSION['colorUser'] == true)
			      {
				// $newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
				$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
			      }
			    if ($_SESSION['colorPartida'] == true)
			      {
				$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
			      }

			    
			  }
			else
			  {
			    $newClase.= ' '.'imgfondo'.$nFondo;
			  }
		      }


		    print '<div id="amount" '.$newClase.'">'.price(price2num($totalc,'MT')).' %'.'</div>';
		    $newClase = $newClaseor;
		  }

		$balancec = $nTotalAp - $objcomp->total;
		$newClaseor = $newClase;
		if ($balancec < 0)
		    if ($lStyle)
		      $newClase.= ' color:#ff0000;';
		    else
		      $newClase.= '" style="color:#ff0000;';
		if ($numCol[16])
		    print '<div id="amount" '.$newClase.'">'.price(price2num($balancec,'MT')).'</div>';
	      }
	    else
	      {
		$totalc = 0;
		    print '<div id="amount" '.$newClase.'">&nbsp;</div>';
	      }
	    $sumaComp+=$totalc;
	    $sumaCompm+=$objcomp->total;
	    $sumaSaldoc+= $balancec;

	    //devengado
	    $totald = 0;
	    $balanced = 0;
	    if ($objdeve->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	      {
		if($numCol[13])
		  {
		    $totald = $objdeve->total;

		    $newClaseor = $newClase;
		    if ($totald >=0)
		      $nFondo = porcGrafico($aVal,$totald);
		    if ($nFondo)
		      {
		
			if ($lStyle)
			  {
			    if ($_SESSION['colorUser'] == true)
			      {
				//$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
				$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
			      }
			    if ($_SESSION['colorPartida'] == true)
			      {
				$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
			      }

			  }
			else
			  {
			    $newClase.= ' '.'imgval'.$nFondo;
			  }
		      }
		    $aHtml[$i]['devengado'] = $objdeve->total;

		    print '<div id="amount" '.$newClase.'">'.price(price2num($objdeve->total,'MT')).'</div>';
		    $newClase = $newClaseor;
		  }
		if($numCol[14])
		  {
		    
		    if ($nTotalAp > 0)
		      $totald = $objdeve->total / $nTotalAp * 100;
		    else
		      $totald = 0;

		    $nFondo = 0;
		    $newClaseor = $newClase;

		    if ($totald >=0)
		      $nFondo = porcGrafico($aBalance,$totald);
		    if ($nFondo)
		      {

			if ($lStyle)
			  {
			    if ($_SESSION['colorUser'] == true)
			      {
				// $newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
				$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
			      }
			    if ($_SESSION['colorPartida'] == true)
			      {
				$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
			      }

			    
			  }
			else
			  {
			    $newClase.= ' '.'imgfondo'.$nFondo;
			  }
		      }


		    print '<div id="amount" '.$newClase.'">'.price(price2num($totald,'MT')).' %'.'</div>';
		    $newClase = $newClaseor;
		  }
		$balanced = $nTotalAp - $objdeve->total;
		$newClaseor = $newClase;
		if ($balanced < 0)
		  {
		    if ($lStyle)
		      $newClase.= ' color:#ff0000;';
		    else
		      $newClase.= '" style="color:#ff0000;';
		  }
		if ($numCol[17])
		    print '<div id="amount" '.$newClase.'">'.price(price2num($balanced,'MT')).'</div>';
		$newClase = $newClaseor;
	      }
	    else
	      {
		$totald = 0;
		    print '<div id="amount" '.$newClase.'">&nbsp;</div>';
	      }
	    $sumaDeve+=$totald;
	    $sumaDevem+=$objdeve->total;
	    $sumaSaldod+= $balanced;


	    //armamos la lista de actividades planificadas
	    $lisPrev = '';
	    $objactl = new Poaactivity($db);
	    $objactl->getlist_poa($obj->id,0);
	    
	    //armamos la lista de preventivos
	    $lisPrev = '';
	    // $objprevl = new Poapartidapre($db);
	    // $objprevl->getlist_poa($obj->id,0);
	    $lisPrev.= '<div id="pre'.$obj->id.'" style="display:none;">';
	    $newClase2 = $newClase;
	    foreach ((array) $objactl->array AS $objppl)
	      {
		//buscamos el preventivo
		$idPreventivo = 0;
		$lCreateprev = true;
		if ($objacpr->fetch('',$objppl->id)>0)
		  {
		    if ($objacpr->fk_activity == $objppl->id)
		      {
			$lCreateprev = false;
			$idPreventivo = $objacpr->fk_prev;
		      }
		  }
		$newClase = ' class="left"  style="background-color:#a9f5d0;"';
		$lisPrev.= '<div>';
		$lisPrev.= '<div id="meta" '.$newClase.'">'.'<a href="'.DOL_URL_ROOT.'/poa/activity/fiche.php?id='.$objppl->id.'" title="'.$objppl->label.'">'.'AC-'.$objppl->nro_activity.'</a>'.'</div>';
		if ($numCol[1] || $numCol[2])
		  {
		    $lisPrev.= '<div id="pseudo" '.$newClase.'">';
		    $lisPrev.=  (strlen($objppl->label)>30?'<a href="#" title="'.$objppl->label.'">'.substr($objppl->label,0,30).'...</a>':(empty($objppl->pseudonym)?$objppl->label:$objppl->pseudonym));
		    $lisPrev.=  '</div>';
		  }
		$lisPrev.= '<div id="partida" '.$newClase.'">'.$objppl->partida.'</div>'; //partida
		
		$lisPrev.= '<div id="amount" '.$newClase.'">'.price(price2num($objppl->amount,'MT')).'</div>';//presupuesto
		
		if ($numCol[71])
		  {
		    if ($lVersion)
		      {
			
			$lisPrev.= '<div id="amount" '.$newClase.'">&nbsp;';
			$lisPrev.= '</div>';
			//numero de reformulado
			$lisPrev.= '<div id="amount" '.$newClase.'">&nbsp;';
			$lisPrev.= '</div>';
		      }
		    else
		      {
			$lisPrev.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
			$lisPrev.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		      }
		  }
		
		if ($numCol[72])
		  {
		    $lisPrev.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		  }
		if ($numCol[73])
		  {
		    $lisPrev.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		  }
		
		if($numCol[9] || $numCol[10] || $numCol[15])
		  {
		    if ($objprev->getsum_str_part_prev($idPreventivo,$obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
		      {
			$lisPrev.= '<div id="amount" '.$newClase.'">'.price(price2num($objprev->total,'MT')).'</div>';
		      }
		    else
		      $lisPrev.= '<div id="amount" '.$newClase.'">'.price(price2num(0,'MT')).'</div>';
		  }
		
		//comprometidos	
		//rqcc
		$objcomp->getsum_prev_str_part($idPreventivo,$obj->fk_structure,$obj->id, $objppl->partida);
		if ($numCol[11] || $numCol[12] || $numCol[16])
		  {
		    $lisPrev.= '<div id="amount" '.$newClase.'">'.price(price2num($objcomp->total,'MT')).'</div>';

		  }
		//deventados
		
		$objdeve->getsum_prev_str_part($idPreventivo,$obj->fk_structure,$obj->id, $objppl->partida);
		if ($numCol[13] || $numCol[14] || $numCol[17])
		  {
		    $lisPrev.= '<div id="amount" '.$newClase.'">'.price(price2num($objdeve->total,'MT')).'</div>';

		  }

		if ($opver == 1)
		  {
		    $aGraphic = array();
		    $aGraphiccode = array();
		    $aGraphictitle = array();
		    if (count($objppl->array_options)>0)
		      {
			foreach((array)$objppl->array_options AS $p => $objActd)
			  {
			    //armamos array de fechas
			    $aDateact = dol_getdate($objActd->date_procedure);
			    
			    if ($aDateact['year'] == $_SESSION['gestion'])
			      {
				$nWeek = ceil($aDateact['mday']/30*100);
				//				$value = (($nWeek>0 && $nWeek <=25)?1:(($nWeek>25 && $nWeek <=50)?2:(($nWeek>51 && $nWeek <=75)?3:4)));
				$value = (($nWeek>0 && $nWeek <=20)?1:(($nWeek>20 && $nWeek <=40)?2:(($nWeek>41 && $nWeek <=60)?3:(($nWeek>61 && $nWeek <=80)?4:5))));
				//primera opcion
				//$aGraphic[$aDateact['mon']][$value] = $value;
				$aGraphic[$aDateact['mon']][$objActd->code_procedure] = $value;
				//buscamos el color del procedimiento
				$objColor = fetch_typeprocedure($objActd->code_procedure,'code');
				//$aGraphiccode[$aDateact['mon']][$value] = 'style="background:#'.$objColor->colour.'; float:left; width:3px; text-align:right; height:7px;"';

				$aGraphiccode[$aDateact['mon']][$objActd->code_procedure] = 'style="background:#'.$objColor->colour.'; float:left; width:3px; text-align:right; height:7px;"';
				$title = $langs->trans('Title').' '.$objColor->label;
				$title.= '&#13;'.$langs->trans('Datetracking').': '.dol_print_date($objActd->date_procedure,'day');
				//$title.= '&#13;'.$langs->trans('Detail').': '.$objEjecd->detail;
				
				//$aGraphictitle[$aDateact['mon']][$value] = $title;
				$aGraphictitle[$aDateact['mon']][$objActd->code_procedure] = $title;
			      }
			  }
		      }
		    for ($d = 1; $d <= 12; $d++)
		      {
			$lisPrev.= '<div id="amountone" '.$newClase.'">';
			//planificacion
			// $title = (($aGraphictitle[$d][1])?$aGraphictitle[$d][1]:'');
			// $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][1])?$aGraphiccode[$d][1]:'id="amountplan"').'">&nbsp;';
			// $lisPrev.= '</div></a>';
			// $title = (($aGraphictitle[$d][2])?$aGraphictitle[$d][2]:'');
			// $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][2])?$aGraphiccode[$d][2]:'id="amountplan"').'">&nbsp;';
			// $lisPrev.= '</div></a>';
			// $title = (($aGraphictitle[$d][3])?$aGraphictitle[$d][3]:'');
			// $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][3])?$aGraphiccode[$d][3]:'id="amountplan"').'">&nbsp;';
			// $lisPrev.= '</div></a>';
			// $title = (($aGraphictitle[$d][4])?$aGraphictitle[$d][4]:'');
			// $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][4])?$aGraphiccode[$d][4]:'id="amountplan"').'">&nbsp;';

			$aGraphicnew = $aGraphic[$d];
			if (count($aGraphicnew)>0)
			  {
			    foreach ((array) $aGraphicnew AS $cpa => $val)
			      {
				$title = (($aGraphictitle[$d][$cpa])?$aGraphictitle[$d][$cpa]:'');
				$lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][$cpa])?$aGraphiccode[$d][$cpa]:'id="amountplan"').'">&nbsp;';
				$lisPrev.= '</div></a>';
			      }
			  }
			else
			  $lisPrev.= "&nbsp;";
			  //   // $lisPrev.= '</div></a>';
			  //   $title = (($aGraphictitle[$d][2])?$aGraphictitle[$d][2]:'');
			  //   $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][2])?$aGraphiccode[$d][2]:'id="amountplan"').'">&nbsp;';
			  //   // $lisPrev.= '</div></a>';
			  //   $title = (($aGraphictitle[$d][3])?$aGraphictitle[$d][3]:'');
			  //   $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][3])?$aGraphiccode[$d][3]:'id="amountplan"').'">&nbsp;';
			  //   // $lisPrev.= '</div></a>';
			  //   $title = (($aGraphictitle[$d][4])?$aGraphictitle[$d][4]:'');
			  //   $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphic[$d][4])?$aGraphiccode[$d][4]:'id="amountplan"').'">&nbsp;';
			  // }
			$lisPrev.= '</div></a>';

			// //ejecucion
			$aGraphice = array();
			$aGraphicecode = array();
			$aGraphicetitle = array();
			$aDateact = array();
			$objwork->fetch_prev($idPreventivo);
			if (count($objwork->array_options)>0 && $idPreventivo)
			  {
			    foreach((array)$objwork->array_options AS $p => $objEjecd)
			      {
				//armamos array de fechas
				$aDateact = dol_getdate($objEjecd->date_tracking);
				if ($aDateact['year'] == $_SESSION['gestion'])
				  {
				    $nWeek = ceil($aDateact['mday']/30*100);
				    $value = (($nWeek>0 && $nWeek <=25)?1:(($nWeek>25 && $nWeek <=50)?2:(($nWeek>51 && $nWeek <=75)?3:4)));
				    $aGraphice[$aDateact['mon']][$value] = $value;
				    //buscamos el color del procedimiento
				    $objColor = fetch_typeprocedure($objEjecd->code_procedure,'code');
				    $aGraphicecode[$aDateact['mon']][$value] = 'style="background:#'.$objColor->colour.'; float:left; width:3px; text-align:right; height:7px;"';
				    $title = $langs->trans('Title').' '.$objColor->label;
				    $title.= '&#13;'.$langs->trans('Datetracking').': '.dol_print_date($objEjecd->date_tracking,'day');
				    $title.= '&#13;'.$langs->trans('Detail').': '.$objEjecd->detail;
				    
				    $aGraphicetitle[$aDateact['mon']][$value] = $title;
				  }
			      }
			    $title = (($aGraphicetitle[$d][1])?$aGraphicetitle[$d][1]:'');
			    $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphice[$d][1])?$aGraphicecode[$d][1]:'id="amountplan"').'">&nbsp;';
			    $lisPrev.= '</div></a>';
			    $title = (($aGraphicetitle[$d][2])?$aGraphicetitle[$d][2]:'');
			    $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphice[$d][2])?$aGraphicecode[$d][2]:'id="amountplan"').'">&nbsp;';
			    $lisPrev.= '</div></a>';
			    $title = (($aGraphicetitle[$d][3])?$aGraphicetitle[$d][3]:'');
			    $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphice[$d][3])?$aGraphicecode[$d][3]:'id="amountplan"').'">&nbsp;';
			    $lisPrev.= '</div></a>';
			    $title = (($aGraphicetitle[$d][4])?$aGraphicetitle[$d][4]:'');
			    $lisPrev.= '<a href="#" title="'.$title.'"><div '.(($aGraphice[$d][4])?$aGraphicecode[$d][4]:'id="amountplan"').'">&nbsp;';
			    $lisPrev.= '</div></a>';
			  }
			$lisPrev.= '</div>';
		      }
		  }

		//usuario
		if ($objuser->fetch($objppl->fk_user_create) > 0)
		    $lisPrev.= '<div id="user" '.$newClase.'">'.$objuser->login.'</div>';
		else
		  $lisPrev.= '<div id="user" '.$newClase.'">&nbsp;</div>';

		//instruccion
		if ($numCol[93])
		  {
		    $lisPrev.= '<div id="instruction" '.$newClase.'">';
		    $lisPrev.= '&nbsp;';
		    $lisPrev.= '</div>';
		  }
		    //pac
		if ($objppl->fk_pac && $objpac->fetch($objppl->fk_pac)>0)
		  {
		    if ($objpac->id == $objppl->fk_pac)
		      {
			if (empty($aColorpac[$objppl->fk_pac]))
			  $aColorpac[$objppl->fk_pac] = randomColor();
			$newClase3 = $newClase;
			$newClase = ' class="left" style="background: '.$aColorpac[$objppl->fk_pac].'"';
			if ($numCol[81])
			  {
			    $lisPrev.= '<div id="amount" '.$newClase.'">';
			    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$objpac->nom.'">'.$objpac->ref.'</a>';
			    $lisPrev.= '</div>';
			  }
			if ($numCol[82])
			  {
			    $lisPrev.= '<div id="amount" '.$newClase.'">';
			    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$objpac->nom.'">'.substr($monthArray[$objpac->month_init],0,3).'</a>';
			    $lisPrev.= '</div>';
			  }
			if ($numCol[84]) // pac total
			  {
			    $lisPrev.= '<div id="amount" '.$newClase.'">';
			    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$title.'">'.price(price2num($objpac->amount,'MT')).'</a>';
			    $lisPrev.= '</div>';
			  }
			if ($numCol[85]) // pac total
			  {
			    $lisPrev.= '<div id="amount" '.$newClase.'">';
			    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$objpac->nom.'">---</a>';
			    $lisPrev.= '</div>';
			  }
			$newClase = $newClase3;
		      }
		    else
		      {
			$lisPrev.= '<div id="amount" '.$newClase.'">';
			$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">&nbsp;</a>';
			$lisPrev.= '</div>';
		      }
		  }
		else
		  {
		    $lisPrev.= '<div id="amount" '.$newClase.'">';
		    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">&nbsp;</a>';
		    $lisPrev.= '</div>';
		  }
		//action
		$lisPrev.= '<div id="action" '.$newClase.'">';
		if ($user->admin || ($obj->statut == 0 && $user->rights->poa->poa->crear))
		  if ($idPreventivo)
		    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$idPreventivo.'">'.img_picto($langs->trans('Seepreventive'),'view').'</a>';
		else
		  $lisPrev.= '&nbsp;&nbsp;';
		//crear un nuevo preventivo
		if ($lCreateprev)
		  if ($user->admin || ($obj->statut == 1 && $user->rights->poa->act->crear))
		    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php'.'?fk_poa='.$obj->id.'&fk_activity='.$objppl->id.'&action=create">'.img_picto($langs->trans('Createpreventive'),'next').'</a>';
		$lisPrev.= '</div>';

	    
		//finalizar la lista
		$lisPrev.= '</div>';
		$lisPrev.= '<div style="clear:both"></div>';

	      }
	    $lisPrev.= '</div>';
	    $newClase = $newClase2;
	    //fin lista preventivos

	    if ($opver == 1 && $numCol[73])
	      {
		$newClaseor = $newClase;
		$iGrafico = tipo_grafico($aLimite,$obj->m_jan);
		if ($obj->p_jan) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_jan)?'<a href="#">'.img_picto(price($obj->m_jan),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_feb);
		if ($obj->p_feb) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_feb)?'<a href="#">'.img_picto(price($obj->m_feb),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_mar);
		if ($obj->p_mar) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_mar)?'<a href="#">'.img_picto(price($obj->m_mar),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_apr);
		if ($obj->p_apr) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_apr)?'<a href="#">'.img_picto(price($obj->m_apr),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_may);
		if ($obj->p_may) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_may)?'<a href="#">'.img_picto(price($obj->m_may),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_jun);
		if ($obj->p_jun) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_jun)?'<a href="#">'.img_picto(price($obj->m_jun),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_jul);
		if ($obj->p_jul) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_jul)?'<a href="#">'.img_picto(price($obj->m_jul),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_aug);
		if ($obj->p_aug) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_aug)?'<a href="#">'.img_picto(price($obj->m_aug),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_sep);
		if ($obj->p_sep) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_sep)?'<a href="#">'.img_picto(price($obj->m_sep),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_oct);
		if ($obj->p_oct) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_oct)?'<a href="#">'.img_picto(price($obj->m_oct),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_nov);
		if ($obj->p_nov) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_nov)?'<a href="#">'.img_picto(price($obj->m_nov),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_dec);
		if ($obj->p_dec) $newClase = ' class="left product"';
    		print '<div id="amountone" '.$newClase.'">'.(!empty($obj->m_dec)?'<a href="#">'.img_picto(price($obj->m_dec),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'').'</div>';
		$newClase = $newClaseor;

		$sumaEne+=$obj->m_ene;
		$sumaFeb+=$obj->m_feb;
		$sumaMar+=$obj->m_mar;
		$sumaApr+=$obj->m_apr;
		$sumaMay+=$obj->m_may;
		$sumaJun+=$obj->m_jun;
		$sumaJul+=$obj->m_jul;
		$sumaAug+=$obj->m_aug;
		$sumaSep+=$obj->m_sep;
		$sumaOct+=$obj->m_oct;
		$sumaNov+=$obj->m_nov;
		$sumaDec+=$obj->m_dec;

    	      }

	    //user	    
	    if ($objectuser->id > 0 && $obj->id == $objectuser->fk_poa_poa)
	      {
		$aHtml[$i]['user'] = $newNombre;

		print '<div id="user" '.$newClase.'"><a href="fiche_user.php?idp='.$obj->id.'&id='.$objectuser->id.'" title="'.$nombre.'">'.$newNombre.'</a></div>';
	      }
	    else
	      {
		print '<div id="user" '.$newClase.'">';
		if ($user->rights->poa->poa->crear)
		  print '<a href="fiche_user.php?idp='.$obj->id.'&action=create">'.img_picto($langs->trans('Create'),'edit_add').'</a>';
		else
		  print '&nbsp;';
		print '</div>';
	      }

	    //instruction
	    //buscamos la ultima instruccion si existe para el poa seleccionado
	    $addClase = ''; 
	    $addMessage = '';
	    if ($conf->poai->enabled && $numCol[93])
	      {
		$objinst->fetch_poa($obj->id);
		if ($objinst->fk_id == $obj->id)
		  {
		    $idInst = $objinst->id;
		    $newClaseor = $newClase;
		    $detail = $objinst->detail;		      
		    //verificamos si tiene monitoreo por revisar
		    if ($objmoni->fetch_ult($obj->id,'POA'))
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
		    print '<div id="instruction" '.$newClase.'">';
		    print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&backtopage=1&id='.$idInst.'" title="'.trim($detail).': '.$langs->trans('Commitment date').' '.dol_print_date($objinst->commitment_date,'day').$addMessage.'">'.img_picto($langs->trans('Edit'),'next').' '.(strlen($detail)>11?substr($detail,0,5).'.':$detail).'</a>';
		    print '</div>';
		    $newClase = $newClaseor;
		  }
		else
		  {
		    print '<div id="instruction" '.$newClase.'"><a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&fk_user='.$objectuser->fk_user.'&action=create&typeinst=POA'.'&backtopage=1">'.img_picto($langs->trans('Newinstruction'),'next').'</a></div>';
		  }
	      }

	    //RESULTADOS PAC

	    $newClaseor = $newClase;
	    $objpac->fetch_poa($obj->id);
	    $title = '';
	    $month_init = 0;
	    $month_public = 0;
	    $amountPac = 0;
	    $saldoPac = $obj->amount; //igual al presupuesto
	    $meshoy = (date('m') * 1);
	    $lisPac = '';
	    
	    if (count($objpac->array) > 0)
	      {
		$newClase2 = $newClase;
	    
		$lisPac.= '<div id="pac'.$obj->id.'" style="display:none;">';

		foreach ((array) $objpac->array AS $k => $objpaclist)
		  {
		    $newClase = ' class="left"  style="background-color:#81bef7;"';
		    if (!empty($aColorpac[$objpaclist->id]))
		      $newClase = ' class="left"  style="background-color:'.$aColorpac[$objpaclist->id].'"';
		    // $title='&#13;'.'-----------------------------';
		    // $title.='&#13;';

		    $title = $langs->trans('Title').' '.$objpaclist->nom;
		    $title.= '&#13;'.$langs->trans('Monthinit').': '.$monthArray[$objpaclist->month_init];
		    $title.= '&#13;'.$langs->trans('Monthpublic').': '.$monthArray[$objpaclist->month_public];
		    $title.= '&#13;'.$langs->trans('Amount').': '.price($objpaclist->amount);
		    if (empty($month_init))
		      $month_init = $objpaclist->month_init;
		    if (empty($month_public))
		      $month_public = $objpaclist->month_public;
		    $amountPac+=$objpaclist->amount;

		    //armando lista de pac
		    $lisPac.= '<div>';
		    $lisPac.= '<div id="meta" '.$newClase.'">'.'<a href="'.DOL_URL_ROOT.'/poa/pac/fiche.php?id='.$objpaclist->id.'" title="'.$objpaclist->nom.'">'.'PA-'.$objpaclist->ref.'</a>'.'</div>';
		    if ($numCol[1] || $numCol[2])
		      {
			$lisPac.= '<div id="pseudo" '.$newClase.'">';
			$lisPac.=  (strlen($objpaclist->nom)>30?'<a href="#" title="'.$objpaclist->nom.'">'.substr($objpaclist->nom,0,30).'...</a>':$objpaclist->nom);
			$lisPac.=  '</div>';
		      }
		    $lisPac.= '<div id="partida" '.$newClase.'">&nbsp;</div>'; //partida
		    $lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';//presupuesto
		
		    if ($numCol[71])
		      {
			if ($lVersion)
			  {
			    
			    $lisPac.= '<div id="amount" '.$newClase.'">&nbsp;';
			    $lisPac.= '</div>';
			    //numero de reformulado
			    $lisPac.= '<div id="amount" '.$newClase.'">&nbsp;';
			    $lisPac.= '</div>';
			  }
			else
			  {
			    $lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
			    $lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
			  }
		      }
		
		    if ($numCol[72])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		      }
		    if ($numCol[73])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		      }
		
		    if($numCol[9] || $numCol[10] || $numCol[15])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		    
		      }
		    //comprometidos	
		    //rqcc
		    if ($numCol[11] || $numCol[12] || $numCol[16])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';

		      }
		    //deventados
		    if ($numCol[13] || $numCol[14] || $numCol[17])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">&nbsp;</div>';
		      }
		    if ($opver == 1)
		      {
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[1].'a</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[2].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[3].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[4].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[5].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[6].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[7].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[8].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[9].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[10].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[11].'</div>';
			$lisPac.= '<div id="amountone" '.$newClase.'">&nbsp;'.$aGraphic[12].'</div>';
		      }
		    //usuario
		    if ($objuser->fetch($objpaclist->fk_user_resp) > 0)
		      $lisPac.= '<div id="user" '.$newClase.'">'.$objuser->login.'</div>';
		    else
		      $lisPac.= '<div id="user" '.$newClase.'">&nbsp;</div>';

		    //instruccion
		    $lisPac.= '<div id="instruction" '.$newClase.'">';
		    $lisPac.= '&nbsp;';
		    $lisPac.= '</div>';


		    //pac
		    $aHtml[$i]['pac'] = $objpaclist->amount;
		    if ($numCol[81])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">';
			$lisPac.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">1</a>';
			$lisPac.= '</div>';
		      }
		    if ($numCol[82])
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">';
			$lisPac.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.substr($monthArray[$objpaclist->month_init],0,3).'</a>';
			$lisPac.= '</div>';
		      }
		    if ($numCol[84]) // pac total
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">';
			$lisPac.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.price(price2num($objpaclist->amount,'MT')).'</a>';
			$lisPac.= '</div>';
		      }
		    if ($numCol[85]) // pac total
		      {
			$lisPac.= '<div id="amount" '.$newClase.'">';
			$lisPac.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">---</a>';
			$lisPac.= '</div>';
		      }

		    //finalizar la lista
		    $lisPac.= '</div>';
		    $lisPac.= '<div style="clear:both"></div>';




		    //final lista de pac
		  }
		$newClase = $newClase2;
		$lisPac .= '</div>';

		$saldoPac = $obj->amount - $amountPac;
		
		//buscamos el inicio del poa y pac
		if ($objprev->total <= 0 && ($month_init < $meshoy)) 
		  if ($lStyle)
		    $newClase.= ' background:#ff0000; color:#ffffff;';
		  else
		    $newClase.= '" style="background:#ff7070; color:#ffffff;';

		//buscamos el inicio del poa y pac
		if ($objprev->total <= 0 && ($month_init == $meshoy)) 
		  if ($lStyle)
		    $newClase.= ' background:#ffa100; color:#ffffff;';
		   else
		    $newClase.= '" style="background:#ffae00; color:#ffffff;';
		
		if ($numCol[81])
		  {
		    print '<div id="amount" '.$newClase.'">';
		    //		    print '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.count($objpac->array).'</a>';
		    print '<a id="miEnlacep'.$obj->id.'" href="javascript:toggleEnlacep('."'".'mostrar'."'".', '.$obj->id.','.$objprev->total.')">';
		    print count($objpac->array);
		    print '</a>';
		  }

		// $newClase = $newClaseor;
		// if ($objprev->total <= 0 && ($objpac->month_init < $meshoy)) 
		//   if ($lStyle)
		//     $newClase.= ' background:#ff0000; color:#ffffff;';
		//    else
		//     $newClase.= '" style="background:#ff7070; color:#ffffff;';

		// if ($objprev->total <= 0 && ($objpac->month_init == $meshoy)) 
		//   if ($lStyle)
		//     $newClase.= ' background:#ffae00; color:#ffffff;';
		//    else
		//     $newClase.= '" style="background:#ffae00; color:#ffffff;';

		if ($numCol[82])
		  {
		    print '<div id="amount" '.$newClase.'">';
		    //		    print '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.substr($monthArray[$month_init],0,3).'</a>';
		    print '<a id="miEnlacep'.$obj->id.'" href="javascript:toggleEnlacep('."'".'mostrar'."'".', '.$obj->id.','.$objprev->total.')">';
		    print substr($monthArray[$month_init],0,3);
		    print '</a>';
		  }


		// $newClase = $newClaseor;
		// //buscamos el inicio del poa y pac
		// if ($objprev->total <= 0 && ($month_public < (date('m') *1))) 
		//   if ($lStyle)
		//     $newClase.= ' background:#ff7070; color:#ffffff;';
		//   else
		//     $newClase.= '" style="background:#ff7070; color:#ffffff;';

		// if ($objprev->total <= 0 && ($month_public == (date('m') *1))) 
		//   if ($lStyle)
		//     $newClase.= ' background:#ffae00; color:#ffffff;';
		//   else
		//     $newClase.= '" style="background:#ffae00; color:#ffffff;';
		
		// if (empty($month_public) || $month_public < 0)
		//    $newClase = $newClaseor;
		if ($numCol[83])
		  {
		    print '<div id="amount" '.$newClase.'">';
		    //		    print '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.substr($monthArray[$month_public],0,3).'</a>';
		    print '<a id="miEnlacep'.$obj->id.'" href="javascript:toggleEnlacep('."'".'mostrar'."'".', '.$obj->id.','.$objprev->total.')">';
		    print substr($monthArray[$month_public],0,3);
		    print '</a>';
		  }
		
		if ($numCol[84]) // pac total
		  {
		    // if ($amountPac > $obj->amount)
		    //   $newClase.= ' background:#ff7070; color:#ffffff;';
		    // else
		    //   $newClase = $newClaseor;
		    print '<div id="amount" '.$newClase.'">';
		    //		    print '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.price(price2num($amountPac,'MT')).'</a>';
		    print '<a id="miEnlacep'.$obj->id.'" href="javascript:toggleEnlacep('."'".'mostrar'."'".', '.$obj->id.','.$objprev->total.')">';
		    print price(price2num($amountPac,'MT'));
		    print '</a>';
		  }
		if ($numCol[85])//saldo pac
		  {
		    // if ($saldoPac  < 0)
		    //   $newClase.= ' background:#ff7070; color:#ffffff;';
		    // else
		    //   $newClase = $newClaseor;

		    print '<div id="amount" '.$newClase.'">';
		    //		    print '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">'.price(price2num($saldoPac,'MT')).'</a>';
		    print '<a id="miEnlacep'.$obj->id.'" href="javascript:toggleEnlacep('."'".'mostrar'."'".', '.$obj->id.','.$objprev->total.')">';
		    print price(price2num($saldoPac,'MT'));
		    print '</a>';

		  }
	      }
	    else
	      {
		print '<div id="amount" '.$newClase.'">';
		print '&nbsp;';
	      }
	    $newClase = $newClaseor;

	    print '</div>';

	    //action
    	    print '<div id="action" '.$newClase.'">';
	    if ($user->admin || ($obj->statut == 0 && $user->rights->poa->poa->crear))
	      print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->id.'&action=edit">'.img_picto($langs->trans('Edit'),'edit').'</a>';
	    else
	      print '&nbsp;&nbsp;';
	    //crear una nueva actividad
	    if ($user->admin || ($obj->statut == 1 && $user->rights->poa->act->crear))
	      print '<a href="'.DOL_URL_ROOT.'/poa/activity/fiche.php'.'?fk_poa='.$obj->id.'&action=create">'.img_picto($langs->trans('Createactivity'),'next').'</a>';

	    print '</div>';
	    print '<div class="clear"></div>';
	    //    	    print '</span>';
	    print '</div>'; //FIN
	    print '<div style="clear:both"></div>';
	    if ($lisPrev)
	      print $lisPrev;
	    if ($lisPac)
	      print $lisPac;
	      
	      }
    	    $i++;
    	  }
      }
  
    print '</aside>';
    print '</section>';
    print '</div>';
    print '<div class="clear"></div>';
    print '</form>';
    $_SESSION['aHtml'] = $aHtml;
    if ($action == 'create' || $action == 'edit')
      {
	print '<section id="section-add">';
	print '<form action="liste.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	if ($action == 'create')
	  print '<input type="hidden" name="action" value="add">';
	if ($action == 'edit')
	  {
	    print '<input type="hidden" name="action" value="update">';
	    print '<input type="hidden" name="id" value="'.$id.'">';
	  }
	print '<input type="hidden" name="gestion" value="'.$gestion.'">';
	dol_htmloutput_mesg($mesg);

	print "<span>";
	print '<div id="meta" '.$newClase.'">';
	print $objstr->select_structure($object->fk_structure,'fk_structure','',1,1);
	print '</div>';

	print '<div id="label" '.$newClase.'">';
	print '<input type="text" name="label" value="'.$object->label.'" size="36">';
	print '</div>';

	// print '<div id="pseudo" '.$newClase.'">';
	// print '<input type="text" name="pseudonym" value="'.$object->pseudonym.'">';
	// print '</div>';

	print '<div id="partida" '.$newClase.'">';
	print '<input type="text" name="partida" value="'.$object->partida.'" size="4">';
	print '</div>';

	print '<div id="amount" '.$newClase.'">';
	print '<input type="text" name="amount" value="'.$object->amount.'" size="9">';
	print '</div>';

	print '<div id="amount" '.$newClase.'">';
	print $langs->trans('Version');
	print '</div>';

	print '<div id="amount" '.$newClase.'">';
	print '<input type="text" name="version" value="'.$object->version.'" size="9">';
	print '</div>';

	if ($numCol[71])
	  {
	    print '<div id="reform" '.$newClase.'">';
	    print '</div>';
	    print '<div id="reform" '.$newClase.'">';
	    print '</div>';
	  }
	if ($numCol[72])
	  {
	    print '<div id="amount" '.$newClase.'">';
	    print '</div>';
	  }
	if ($numCol[73])
	  {
	    print '<div id="amount" '.$newClase.'">';
	    print '</div>';
	  }
	// print '<div id="amount" '.$newClase.'">';
	// print '</div>';
	print '<div id="amount" '.$newClase.'">';
	print '</div>';
	print '<div id="amount" '.$newClase.'">';
	print '</div>';
	print '<div id="user" '.$newClase.'">';
	print '</div>';
	print '<div id="amount" '.$newClase.'">';
	print '<button class="btn_trans" title="'.$langs->trans('Save').'" type="submit" name="save">'.img_picto($langs->trans('Save'),DOL_URL_ROOT.'/poa/img/save.png','',1).'</button>';
	print '<button class="btn_trans" title="'.$langs->trans('Cancel').'" type="submit" name="cancel" value="'.$langs->trans('Cancel').'">'.img_picto($langs->trans('Cancel'),'off').'</button>';

	print '</div>';

	print '</span>';
	print '</form>';
	print '</section>';
      }

    print '<section id="section-footer">';
    print '<span>';

    //totales
    print '<div id="meta" class="left total"></div>';
    if ($numCol[1])
      {
	print '<div id="label" class="left total">';
	print '<span>'.$ii.'</span>';
	print '</div>';
      }

    if ($numCol[2])
      {
	print '<div id="pseudo" class="left total">';
	print '<span>'.$ii.'</span>';
	print '</div>';
      }

    //partida
    print '<div id="partida" class="left total"></div>';

    print '<div id="amount" class="left total">';
    print price($sumaPresup);
    print '</div>';
    print '<div id="amount" class="left total">';

    if ($numCol[71])
      {
	if ($lVersion)
	  {
	    print '<input type="hidden" id="totrefo" value="'.$sumaRef1.'">';
	    print '<span id="totref">'.price($sumaRef1).'</span>';
	    print '</div>';
	    print '<div id="amount" class="left total">';
	    print '&nbsp;';
	  }
	else
	  {
	    print '</div>';
	    print '<div id="amount" class="left total">';
	    print '&nbsp;';
	  }
      }

    if ($numCol[72])
      {
	if ($lVersionAp)
	  {
	    print price($sumaAprob);
	  }
      }
    if ($numCol[73])
      print price($sumaRef1);

    print '</div>';
    if ($numCol[73])
      print '&nbsp;';
    // if ($opver == true)
    //   {
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    // 	print '<div id="amountone" class="left total"></div>';
    //   }
    print '<div id="amount" class="left total">';
    if ($numCol[9])
      print price(price2num($sumaPrev,'MT'));
    if ($numCol[10])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaPrevm/$sumaAprob*100,'MT')).' %';
	else
	  print price(0);
      }   
    if ($numCol[15])
      print price(price2num($sumaSaldop,'MT'));
    print '</div>';

    print '<div id="amount" class="left total">';
    if ($numCol[11])
      print price($sumaComp);
    if ($numCol[12])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaCompm/$sumaAprob*100,'MT')).' %';
	else
	  print price(0);
      }
    if ($numCol[16])
      print price(price2num($sumaSaldoc,'MT'));
    print '</div>';

    print '<div id="amount" class="left total">';
    if ($numCol[13])	
      print price($sumaDeve);
    if ($numCol[14])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaDevem/$sumaAprob*100,'MT')).' %';
	else
	  print price(0);
      }
    if ($numCol[17])
      print price(price2num($sumaSaldod,'MT'));
    print '</div>';

        if ($opver == true)
      {
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
	print '<div id="amountone" class="left total"></div>';
      }

    print '<div id="user" class="left total">';
    print '</div>';

    print '<div id="instruction" class="left total">';
    print '</div>';
    print '<div id="action" class="left total">';
    print '</div>';

    print '<div class="clear"></div>';
    print '</span>';
    print '</section>';

    $db->free($result);

    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if ($user->rights->poa->poa->crear)
	  print "<a class=\"butAction\" href=\"liste.php?action=create\">".$langs->trans("Createnew")."</a>";
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	if ($user->rights->poa->exp)
	  print "<a class=\"butAction\" href=\"fiche_excel.php\">".$langs->trans("Excel")."</a>";
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Excel")."</a>";
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
