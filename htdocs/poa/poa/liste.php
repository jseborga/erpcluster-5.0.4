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
require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");

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
require_once(DOL_DOCUMENT_ROOT."/poa/lib/main.lib.php");

// require_once(DOL_DOCUMENT_ROOT."/poa/lib/numberletter.lib.php");
// require_once(DOL_DOCUMENT_ROOT."/poa/class/numbertoletterconverter.class.php");
// require_once(DOL_DOCUMENT_ROOT."/poa/class/numeroaletras.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poamenu.class.php");

//verificadion de tipo equipo
$lMobile = false;
$navigator_user_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? strtolower($_SERVER['HTTP_USER_AGENT']):'';
if(stristr($navigator_user_agent, "iphone") or stristr($navigator_user_agent, "ipad") or stristr($navigator_user_agent, "kindle") or stristr($navigator_user_agent, "mobile")
	)
	$lMobile = true;

$langs->load("poa@poa");
if ($conf->poai->enabled)
	$langs->load("poai@poai");

$outputlangs = $langs;
$monthArray = monthArray($outputlangs);
if (!$user->rights->poa->poa->leer)
	accessforbidden();
// echo '<hr>num '.$number = 121121.05;
// $cn = new NumberToLetterConverter();
// echo '<hr>'.$leter = $cn->to_word($number, $miMoneda);
// echo'<hr>'. $leter = valorEnLetras($number,'Bolivianos');
// $cn = new NumerosALetras();
// echo '<hr>'.$leter = $cn->traducir($number);
// $_SESSION['dol_hide_topmenu'] = 1;
// $_SESSION['dol_hide_leftmenu'] = 1;
$object     = new Poapoa($db);
$objpre     = new Poaprev($db); //preventivos
$objprev    = new Poapartidapre($db);
$objcomp    = new Poapartidacom($db);
$objdeve    = new Poapartidadev($db);
$objrefo    = new Poareformulated($db);
$objrefodet = new Poareformulateddet($db);
$objpac     = new Poapac($db);
$objproc    = new Poaprocess($db); //procesos
$objprocc   = new Poaprocesscontrat($db);
$objrefoof  = new Poareformulatedof($db);
$objrefoto  = new Poareformulatedto($db);
$objectuser = new Poapoauser($db);
$objuser    = new User($db);
$objstr     = new Poastructure($db);
$objact     = new Poaactivity($db);
$objactdet  = new Poaactivitydet($db);
$objactw    = new Poaactivityworkflow($db);
$objwork    = new Poaworkflow($db);
$objmenu    = new Poamenu($db);

//$objacpr    = new Poaactivityprev($db);
//$objactwor  = new Poaactivityworkflow($db);//REVISAR
$objactwork = new Poaactivitywork($db); //grupos de trabajo

$objcon     = new Contrat($db);
if ($conf->poai->enabled)
{
	$objinst = new Poaiinstruction($db);
	$objmoni = new Poaimonitoring($db);
}

//actualizacion de campo fk_contrato en poapartidacom
include DOL_DOCUMENT_ROOT.'/poa/lib/actualizacom.php';

$lPriority = false;
$id = GETPOST('id');
$action = GETPOST('action');
if (empty($action)) $action = 'menu2';
if (isset($_GET['mostrar']))
	$_SESSION['opver'] = '';
if (isset($_GET['opver']))
	$_SESSION['opver'] = $_GET['opver'];
$opver = $_SESSION['opver'];

//asignando filtro de usuario
assign_filter_user('search_user');

//gestion
$gestion = GETPOST('gestion');

//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];

//mes actual
$aDateActual = dol_getdate(dol_now());
$monActual = $aDateActual['mon'];

//gestion definida en index.php
if (isset($_POST['gestion']))
	$_SESSION['gestion'] = $_POST['gestion'];
if (empty($_SESSION['gestion']))
	$_SESSION['gestion'] = $aDateActual['year'];

$gestion = $_SESSION['gestion'];


$_SESSION['aActivityreg'] = array();

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
			header("Location: ".$_SERVER['PHP_SELF'].'?dol_hide_leftmenu=1');
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
//update
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel") &&
	$user->rights->poa->poa->mod)
{
	$error = 0;
	if ($object->fetch(GETPOST('id'))>0)
	{
		$object->fk_structure = GETPOST("fk_structure");
	//buscamos structure
		$objstr->fetch($object->fk_structure);
		if ($objstr->id == $object->fk_structure)
			$object->ref = $objstr->sigla;
		$object->label     = GETPOST('label');
		$object->partida   = GETPOST('partida');
	//$object->gestion   = $gestion;
		$object->amount    = GETPOST('amount');

	//$object->entity  = $conf->entity;
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
			$res = $object->update($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
			}
			$action = 'update';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			if ($error)
		  $action="update";   // Force retour sur page creation
	}
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
$minimo = $object->maxmin;

//armamos la structura

//array color para pac
$aColorpac = $_SESSION['aColorpac'];
//armamos en un array el valor
$objstr->getliststr($gestion);
// echo '<pre>';
// print_r($objstr->aList);
// echo '</pre>';
$aStructure = array();
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
	301=>302,
	321=>array(322,323),
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

if (empty($_SESSION['numCol'])) //vista seguimiento
{
	$_SESSION['numCol'] = array(1=>true,//label
				 2=>false,//pseudonum
				 3=>true,//LIBRE
				 4=>false,//LIBRE
				 7=>false,//calendario FALSE
				 8=>true,//calendario TRUE
				 9=>true,//preventive
				 10=>false,//preventiver %
				 15=>false,//preventive balance
				 11=>true,//comprometido
				 12=>false,//comprometido %
				 16=>false,//comprometido balance
				 13=>true,//devengado
				 14=>false,//devengado %
				 17=>false,//deventado balance
				 51=>false,//color user TRUE /partida FALSE
				 52=>true,//color user FALSE/partida FALSE
				 61=>false,//color partida TRUE/user FALSE
				 62=>true,//color partida FALSE/user FALSE
				 71=>false,//reformulated
				 72=>false,//pendiente aprobacion
				 73=>false,//porcent reformulated
				 81=>false,//PAC
				 82=>false,//PAC ini
				 83=>false,//PAC pub
				 84=>false,//PAC total
				 85=>false,//PAC balance
				 91=>false,//presupuesto inicial
				 92=>true,//presupuesto aprobado
				 93=>true,//LIBRE  instruction
				 94=>false,//LIBRE
				 190=>true,//action
				 191=>false,//trab 1
				 192=>false,//trab 2
				 193=>false,//trab 3
				 194=>false,//trab 4
				 195=>false,//trab 5
				 196=>false,//trab 6
				 197=>false,//trab 7
				 198=>false,//trab 8
				 199=>false,//trab 9
				 200=>false,//priority
				 301=>false,//nro preventivo TRUE
				 302=>false,//nombre preventivo FALSE
				 311=>false,//
				321=>false,//ocultar seguimiento fecha false
				322=>false,//ocultar seguimiento false
				323=>false,//ocultar seguimiento accion false
				);
}

//recibiendo valores
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

//filtro principal
if (isset($_POST['mr1'])) $_SESSION['mr1'] = $_POST['mr1'];
if (isset($_GET['mr1'])) $_SESSION['mr1'] = $_GET['mr1'];
//recibiendo valores de m1
if (isset($_POST['m1'])) $_SESSION['m1'] = $_POST['m1'];
if (isset($_GET['m1'])) $_SESSION['m1'] = $_GET['m1'];
//recibiendo valores de r1
if (isset($_POST['r1'])) $_SESSION['r1'] = $_POST['r1'];
if (isset($_GET['r1'])) $_SESSION['r1'] = $_GET['r1'];

//recibiendo valores de menu
if (isset($_POST['a1'])) $_SESSION['a1'] = $_POST['a1'];
if (isset($_POST['a2'])) $_SESSION['a2'] = $_POST['a2'];
if (isset($_POST['a3'])) $_SESSION['a3'] = $_POST['a3'];
if (isset($_POST['a4'])) $_SESSION['a4'] = $_POST['a4'];
if (isset($_POST['search_priority']))
	$_SESSION['search_priority'] = $_POST['search_priority'];
$action_search = false;
if (isset($_POST['search_sigla']) && !empty($_POST['search_sigla']))
	$action_search = true;
if (isset($_POST['search_label']) && !empty($_POST['search_label']))
	$action_search = true;
if (isset($_POST['search_pseudonym']) && !empty($_POST['search_pseudonym']))
	$action_search = true;
if (isset($_POST['search_partida']) && !empty($_POST['search_partida']))
	$action_search = true;
if (isset($_POST['search_amount']) && !empty($_POST['search_amount']))
	$action_search = true;
if (isset($_POST['search_reform']) && !empty($_POST['search_reform']))
	$action_search = true;

if (isset($_POST['search_user']) && !empty($_POST['search_user']))
{
	if ($_POST['search_user'] < 0)
		$_POST['search_user'] ='';
	$_SESSION['poasearch_user'] = STRTOUPPER($_POST['search_user']);
	$action_search = true;
}
if (isset($_POST['nosearch_x']))
{
	$action_search = true;
}

//almacenamos el filtro en la base llx_poa_menu
if ($action_search == true)
{
	//$_SESSION['menusel'] = 1;
	//recuperamos
	$objmenu->fetch('',$user->id);
	$aDetail = array();
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			if (isset($_POST['nosearch_x']))
			{
				if (substr($aYy[0],0,6) == 'search')
				{
			//no se carga nada $aDetail[$aYy[0]] = '';
			//echo '<hr>no se carga';
				}
				else
					$aDetail[$aYy[0]] = $aYy[1];
			}
			else
				$aDetail[$aYy[0]] = $aYy[1];
		}
	}
	if (!isset($_POST['nosearch_x']))
	{
		if (isset($_POST['search_sigla']))
			$aDetail['search_sigla'] = $_POST['search_sigla'];
		if (isset($_POST['search_label']))
			$aDetail['search_label'] = $_POST['search_label'];
		if (isset($_POST['search_pseudonym']))
			$aDetail['search_pseudonym'] = $_POST['search_pseudonym'];
		if (isset($_POST['search_partida']))
			$aDetail['search_partida'] = $_POST['search_partida'];
		if (isset($_POST['search_amount']))
			$aDetail['search_amount'] = $_POST['search_amount'];
		if (isset($_POST['search_reform']))
			$aDetail['search_reform'] = $_POST['search_reform'];
		if (isset($_POST['search_user']))
			$aDetail['search_user'] = STRTOUPPER($_POST['search_user']);
	}
	//armamos cDetail
	$cDetail = '';
	foreach ((array) $aDetail AS $k => $value)
	{
		if (!empty($cDetail)) $cDetail.= '|';
		$cDetail.= $k.':'.$value;
	}
	//insertamos o actualizamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
	//actualizamos
		$objmenu->detail = $cDetail;
		$objmenu->update($user);
	}
	else
	{
	//insertamos
		$objmenu->fk_user = $user->id;
		$objmenu->detail = $cDetail;
		$objmenu->create($user);
	}
}

//menu1
if ($action == 'menu1')
{
	$_SESSION['menusel'] = 1;
	//recuperamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			$aDetail[$aYy[0]] = $aYy[1];
		}
	}
	if (isset($_GET['f1']))
		$aDetail['f1'] = !$aDetail['f1'];
	if (isset($_GET['f2']))
		$aDetail['f2'] = !$aDetail['f2'];
	if (isset($_GET['f3']))
		$aDetail['f3'] = !$aDetail['f3'];
	if (isset($_GET['f4']))
		$aDetail['f4'] = !$aDetail['f4'];
	if (isset($_GET['f5']))
		$aDetail['f5'] = !$aDetail['f5'];
	if (isset($_GET['f6']))
		$aDetail['f6'] = !$aDetail['f6'];
	if (isset($_GET['f11']))
		$aDetail['f11'] = !$aDetail['f11'];

	//armamos cDetail
	$cDetail = '';
	foreach ((array) $aDetail AS $k => $value)
	{
		if (!empty($cDetail)) $cDetail.= '|';
		$cDetail.= $k.':'.$value;
	}
	//insertamos o actualizamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
	//actualizamos
		$objmenu->detail = $cDetail;
		$objmenu->update($user);
	}
	else
	{
	//insertamos
		$objmenu->fk_user = $user->id;
		$objmenu->detail = $cDetail;
		$objmenu->create($user);
	}
}
//menu2
if ($action == 'menu2')
{
	$_SESSION['menusel'] = 2;

	//insertamos o actualizamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			$aDetail[$aYy[0]] = $aYy[1];
		}
	}
	if (isset($_SESSION['r1']))
	{
		if ($_SESSION['r1'] == 1 || $_SESSION['r1'] == 4 || $_SESSION['r1'] == 5)
		{
			$aDetail['r1'] = $_SESSION['r1'];
			$opver = true;
		//standard
			if ($_SESSION['numCol'][1])
				$_SESSION['numCol'][2] = false;
			else
				$_SESSION['numCol'][2] = true;
			$_SESSION['numCol'][321] = false;
			$_SESSION['numCol'][322] = false;
			$_SESSION['numCol'][323] = false;
		}
		if ($_SESSION['r1'] == 2)
	 //Seguimiento
		{
			$opver = false;
			$aDetail['r1'] = 2;
			if ($_SESSION['numCol'][1])
				$_SESSION['numCol'][2] = false;
			else
				$_SESSION['numCol'][2] = true;
		//calendario ocultar
			$_SESSION['numCol'][7] = false;
			$_SESSION['numCol'][8] = false;
		//seguimiento
			$_SESSION['numCol'][321] = true;
			$_SESSION['numCol'][322] = true;
			$_SESSION['numCol'][323] = true;

			$aDetail['a1'] = true;
			$aDetail['a2'] = true;
			$aDetail['a3'] = true;
			$aDetail['a4'] = true;
		}
		if ($_SESSION['r1'] == 3)
	 //nombres
		{
			$aDetail['r1'] = 3;
			$_SESSION['numCol'][1] = true;
			$_SESSION['numCol'][2] = true;
			$_SESSION['numCol'][7] = true;
			$_SESSION['numCol'][8] = false;
		//seguimiento
			$_SESSION['numCol'][321] = false;
			$_SESSION['numCol'][322] = false;
			$_SESSION['numCol'][323] = false;
			$aDetail['a1'] = false;
			$aDetail['a2'] = false;
			$aDetail['a3'] = false;
			$aDetail['a4'] = false;
		}

	//armamos cDetail
		$cDetail = '';
		foreach ((array) $aDetail AS $k => $value)
		{
			if (!empty($cDetail)) $cDetail.= '|';
			$cDetail.= $k.':'.$value;
		}
	//insertamos o actualizamos
		$objmenu->fetch('',$user->id);
		if ($objmenu->fk_user == $user->id)
		{
		//actualizamos
			$objmenu->detail = $cDetail;
			$objmenu->update($user);
		}
		else
		{
		//insertamos
			$objmenu->fk_user = $user->id;
			$objmenu->detail = $cDetail;
			$objmenu->create($user);
		}
	}
}

//menu3
if ($action == 'menu3')
{
	$_SESSION['menusel'] = 3;
	if (isset($_POST['Anular']))
	{
		$_POST['search_all'] = '';
		$_POST['search_login'] = -1;
		$_POST['search_priority'] = -1;
	}
	//insertamos o actualizamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			$aDetail[$aYy[0]] = $aYy[1];
		}
	}
	if (isset($_POST['search_all']))
	{
		if (!empty($_POST['search_all']))
			$aDetail['search_all'] = STRTOUPPER($_POST['search_all']);
		else
			unset($aDetail['search_all']);
	}
	if (isset($_POST['search_priority']))
	{
		if (!empty($_POST['search_priority']))
			$aDetail['search_priority'] = $_POST['search_priority'];
		else
			unset($aDetail['search_priority']);
	}
	//user
	if (isset($_POST['search_login']))
	{
		if (!empty($_POST['search_login']))
			$aDetail['search_login'] = $_POST['search_login'];
		else
			unset($aDetail['search_login']);
	}
	//armamos cDetail
	$cDetail = '';
	foreach ((array) $aDetail AS $k => $value)
	{
		if (!empty($cDetail)) $cDetail.= '|';
		$cDetail.= $k.':'.$value;
	}
	//insertamos o actualizamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
	//actualizamos
		$objmenu->detail = $cDetail;
		$objmenu->update($user);
	}
	else
	{
	//insertamos
		$objmenu->fk_user = $user->id;
		$objmenu->detail = $cDetail;
		$objmenu->create($user);
	}
}
if (isset($_POST["Enviar"]))
{
	$cDetail = '';
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			$aDetail[$aYy[0]] = $aYy[1];
		}
	}

	if (isset($_POST['r1']))
	{
		$cDetail.= 'r1:'.$_POST['r1'].'|';
		if ($filtromenu['r1'] == 2)
	 //Seguimiento
		{
			if ($_SESSION['numCol'][1])
				$_SESSION['numCol'][2] = false;
			else
				$_SESSION['numCol'][2] = true;

			$_SESSION['numCol'][7] = false;
			$_SESSION['numCol'][8] = false;
			$_POST['a1'] = false;
			$_POST['a2'] = false;
			$_POST['a3'] = false;
			$_POST['a4'] = false;
		}
		if ($filtromenu['r1'] == 3)
	 //nombres
		{
			$_SESSION['numCol'][1] = true;
			$_SESSION['numCol'][2] = true;
			$_SESSION['numCol'][7] = true;
			$_SESSION['numCol'][8] = false;
			$_POST['a1'] = false;
			$_POST['a2'] = false;
			$_POST['a3'] = false;
			$_POST['a4'] = false;
		}
	}
}
if (isset($_POST['search_all']) || isset($_GET['search_all']))
{
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			$aDetail[$aYy[0]] = $aYy[1];
		}
	}

	$search_all = GETPOST('search_all');
	if (!empty($search_login))
		$aDetail['search_login'] = $search_login;
	else
		unset($aDetail['search_login']);

	$cDetail = '';
	foreach ((array) $aDetail AS $k => $value)
	{
		if (!empty($cDetail)) $cDetail.= '|';
		$cDetail.= $k.':'.$value;
	}
	//insertamos o actualizamos
	if ($objmenu->fk_user == $user->id)
	{
	//actualizamos
		$objmenu->detail = $cDetail;
		$objmenu->update($user);
	}
	else
	{
	//insertamos
		$objmenu->fk_user = $user->id;
		$objmenu->detail = $cDetail;
		$objmenu->create($user);
	}
}

	//user
if (isset($_POST['search_login']) || isset($_GET['search_login']))
{
	//insertamos o actualizamos
	$objmenu->fetch('',$user->id);
	if ($objmenu->fk_user == $user->id)
	{
		$aX = explode('|',$objmenu->detail);
		foreach ((array) $aX AS $k => $aY)
		{
			$aYy = explode(':',$aY);
			$aDetail[$aYy[0]] = $aYy[1];
		}
	}
	$search_login = GETPOST('search_login');
	if (!empty($search_login))
		$aDetail['search_login'] = $search_login;
	else
		unset($aDetail['search_login']);
	//armamos cDetail
	$cDetail = '';
	foreach ((array) $aDetail AS $k => $value)
	{
		if (!empty($cDetail)) $cDetail.= '|';
		$cDetail.= $k.':'.$value;
	}
	//insertamos o actualizamos
	if ($objmenu->fk_user == $user->id)
	{
	//actualizamos
		$objmenu->detail = $cDetail;
		$objmenu->update($user);
	}
	else
	{
	//insertamos
		$objmenu->fk_user = $user->id;
		$objmenu->detail = $cDetail;
		$objmenu->create($user);
	}

}

//recuperamos de llx_poa_menu
$objmenu->fetch('',$user->id);
$lPrincipall = false;
if ($objmenu->fk_user == $user->id)
{
	$_SESSION['filtersearchpoa'] = '';
	$_SESSION['filtermenupoa'] = '';
	$aDetail = explode('|',$objmenu->detail);
	foreach ((array) $aDetail AS $jd => $cValue)
	{
	//$lPrincipall = true;
		$aValue = explode(':',$cValue);
		if (SUBSTR($aValue[0],0,6) == 'search')
			$_SESSION['filtersearchpoa'][$aValue[0]] = $aValue[1];
		else
			$_SESSION['filtermenupoa'][$aValue[0]] = $aValue[1];
		if ($aValue[0] == 'r1') $_SESSION['r1'] = $aValue[1];
	}
}
$filtromenu   = $_SESSION['filtermenupoa'];
$filtrosearch = $_SESSION['filtersearchpoa'];
//vistas
//r1 = standard
//r2 = seguimiento
if (empty($_SESSION['r1'])) $_SESSION['r1'] = 1;
// $search_all = $filtromenu['search_all'];
// $search_priority = $filtromenu['search_priority'];
//fin seleccion principal
//exit;
//verificamos el filtro principal
$lPrincipall = false;
//echo '<hr>ini '.$nSearch = 0;
if (isset($filtrosearch['search_all']) && $filtrosearch['search_all'])
{
	$lPrincipall = true;
	$nSearch++;
}
//echo '<hr>1a '.$nSearch;
if (isset($filtrosearch['search_priority']) && $filtrosearch['search_priority']>=0)
{
	$lPrincipall = true;
	$nSearch++;
}
//echo '<hr>1b '.$nSearch;
if (isset($filtrosearch['search_login']) && $filtrosearch['search_login'] > 0)
{
	$lPrincipall = true;
	$nSearch++;
}
//echo '<hr>1c '.$nSearch;
if ($filtromenu['a2'])
{
	$_SESSION['numCol'][8] = true;
	$_SESSION['numCol'][7] = false;
}
//echo '<hr>1d '.$nSearch;
//fin filtro principal
//echo '<hr>fin '.$nSearch;
if (isset($vercol) && $vercol == 8)
{
	$_SESSION['numColdevid'] = ($_SESSION['numCol'][81]?81:($_SESSION['numCol'][82]?82:($_SESSION['numCol'][83]?83:($_SESSION['numCol'][84]?84:0))));
	$_SESSION['numCol'][71] = false;
	$_SESSION['numCol'][72] = false;
	$_SESSION['numCol'][73] = false;
	$_SESSION['numCol'][81] = false;
	$_SESSION['numCol'][82] = false;
	$_SESSION['numCol'][83] = false;
	$_SESSION['numCol'][84] = false;
	$_SESSION['numCol'][93] = false;

}
if (isset($vercol) && $vercol == 7)
{
	$_SESSION['numCol'][71] = true; //reform
	$_SESSION['numCol'][72] = false;
	$_SESSION['numCol'][73] = false;
	$_SESSION['numCol'][81] = true;
	$_SESSION['numCol'][82] = false;
	$_SESSION['numCol'][83] = false;
	$_SESSION['numCol'][83] = false;
	$_SESSION['numCol'][93] = true;
}

//armamos las columnas de acuerdo a r1
if (isset($_SESSION['r1']))
{
	if ($_SESSION['r1'] == 1 || $_SESSION['r1'] == 4 || $_SESSION['r1'] == 5)
	{
		$opver = true;
	//standard
		if ($_SESSION['numCol'][1])
			$_SESSION['numCol'][2] = false;
		else
			$_SESSION['numCol'][2] = true;
		$_SESSION['numCol'][321] = false;
		$_SESSION['numCol'][322] = false;
		$_SESSION['numCol'][323] = false;
	}
	if ($_SESSION['r1'] == 2) //Seguimiento
	{
		$opver = false;
		if ($_SESSION['numCol'][1])
			$_SESSION['numCol'][2] = false;
		else
			$_SESSION['numCol'][2] = true;
	//calendario ocultar
		$_SESSION['numCol'][7] = false;
		$_SESSION['numCol'][8] = false;
	//seguimiento
		$_SESSION['numCol'][321] = true;
		$_SESSION['numCol'][322] = true;
		$_SESSION['numCol'][323] = true;
	}
	if ($_SESSION['r1'] == 3) //nombres
	{
		$_SESSION['numCol'][1] = true;
		$_SESSION['numCol'][2] = true;
		$_SESSION['numCol'][7] = true;
		$_SESSION['numCol'][8] = false;
	//seguimiento
		$_SESSION['numCol'][321] = false;
		$_SESSION['numCol'][322] = false;
		$_SESSION['numCol'][323] = false;
	}
}

$aColorUser = $_SESSION['aColorUser'];

// if ($_SESSION['numCol'][8] == true)
//   $opver = true;
// if ($_SESSION['numCol'][7] == true)
//   $opver = false;
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

foreach ((array) $_SESSION['filtersearchpoa'] AS $jk =>$valuemenu)
{
	if ($jk == 'search_sigla') $search_sigla = $valuemenu;
	if ($jk == 'search_label') $search_label = $valuemenu;
	if ($jk == 'search_pseudonym') $search_pseudonym = $valuemenu;
	if ($jk == 'search_partida') $search_partida = $valuemenu;
	if ($jk == 'search_amount') $search_amount = $valuemenu;
	if ($jk == 'search_reform') $search_reform = $valuemenu;
	if ($jk == 'search_user') $search_user = $valuemenu;
	if ($jk == 'search_all') $search_all = $valuemenu;
	if ($jk == 'search_priority') $search_priority = $valuemenu;

}

//filtro secundario eliminando el filtro
$search_sigla     = '';
$search_label     = '';
$search_pseudonym = '';
$search_partida   = '';
$search_amount    = '';
$search_reform    = '';
$search_user      = '';
//$nSearch = 0;
if ($search_sigla) $nSearch++;
if ($search_label) $nSearch++;
if ($search_pseudonym) $nSearch++;
if ($search_partida) $nSearch++;
if ($search_amount) $nSearch++;
if ($search_reform) $nSearch++;
if ($search_user) $nSearch++;
//echo '<hr>nrosearch '.$nSearch;
//$sel_priority  = $_SESSION["search_priority"];
//$sel_priority  = ($search_priority>0)?$search_priority:'';

if (empty($sel_priority))$sel_priority = -1;
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
$sql.= " s.label AS labelstructure, s.sigla, s.fk_area ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON p.fk_structure = s.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.gestion = ".$gestion;
//filtro de acuerdo a las areas que pertenece
if (!$user->admin && !empty($idsArea))
	$sql.= " AND s.fk_area IN ($idsArea)";
if ($_SESSION['idsAarea'])
	$sql.= " AND s.fk_area = ".$_SESSION['sel_area'];
$sql.= " AND p.statut = 1 "; //solo los aprobados
//order
$sql.= " ORDER BY s.sigla, s.label, p.version,p.partida, p.rowid ";
//$sql.= $db->order($sortfield,$sortorder);
//$sql.= $db->plimit($limit+1, $offset);
$respoa = $db->query($sql);
$form=new Form($db);

$numCol = $_SESSION['numCol'];
if ($respoa)
{
	//recuperamos las partidas de la gestion
	if (!isset($_SESSION['aPartida'])) $_SESSION['aPartida'] = get_partida($gestion);
	$aPartida = $_SESSION['aPartida'];
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
		if ($objap->gestion == $gestion) $nVersion = $objap->version + 1;
		else $nVersion = 1;
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
	if (count($aReform)>0) list($aOfa,$aOfonea,$aOfrefa) = $objrefodet->get_sumaref($aReform);
	$i = 0;

	header("Content-type: text/html; charset=".$conf->file->character_set_client);

//    $aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css');
	$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css');
	$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');

	top_htmlheadv($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

	//filtro
	$idTag1 = 1;
	$idTag2 = 2;

	//impresion de submenu segun seleccion
	include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';

	//cuerpo
	print '<br>';
	print '<section class="content">';
	print '<div class="box">';
	print '<div class="box-body table-responsive">';
	print '<form name="fo3b" method="POST" id="fo3b" onsubmit="enviareform(); return false" action="'.$_SERVER["PHP_SELF"].'?dol_hide_leftmenu=1">'."\n";
	print '<input type="hidden" name="gestion" value="'.$gestion.'">';
	print '</form>';
	print '<table id="data1" class="table table-bordered table-hover">';

	//fin menu seleccion


	//modificado frame
	//include FRAMES
	include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/frames.tpl.php';

	print '<thead>';
	//init head 1 buttons
	include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/head1.tpl.php';
	print '</thead>';
	//print '</form>';

//    print '<form name="fo3" method="POST" id="fo3" onsubmit="enviareform(); return false" action="'.$_SERVER["PHP_SELF"].'?dol_hide_leftmenu=1">'."\n";
//    print '<input type="hidden" name="gestion" value="'.$gestion.'">';
//    print '<tbody>';

	//print '</section>';

	$respoa = $db->query($sql);
	$num = $db->num_rows($respoa);
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
		$a = true;
		$aHtml = array();
		$aTotalSAct = array();

		$aActivity = array();
		$aActivityf = array();
		$aActivityres = array();
		$fk_structure = 0;
		print '<tbody>';
		while ($i < $num)
		{
			$aPoa = array();

			$obj = $db->fetch_object($respoa);
			$lContinue = true;
			$ii++;
			//recuperando la estructura
			if ($filtromenu['f11'])
			{
				if ($fk_structure != $obj->fk_structure)
				{
					$fk_structure = $obj->fk_structure;
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta1.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta2.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta3.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta4.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta5.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta6.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta7.tpl.php';
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/meta8.tpl.php';
				}
			}
			//filtro principal
			if ($lPrincipall)
			{
				include DOL_DOCUMENT_ROOT.'/poa/poa/lib/filterpoaprin.lib.php';
			}
			//filtro secundario de liste
			include DOL_DOCUMENT_ROOT.'/poa/poa/lib/filterpoa.lib.php';
			if (empty($nSearch)) $aPoa[$obj->id] = true;

			if ($action == 'edit' && $obj->id == $id) $object = $obj;


			//recuperamos las actividades
			$objactl = new Poaactivity($db);
			$objactl->getlist_poa($obj->id,0);
			//buscamos en las actividades los filtros
			$lViewprev = True;
			//echo '<br>antes de actividades lContinue = |'.$lContinue.'|';
			// echo '<br>count '.count($objactl->array);
			//print_r($objactl->array);
			if (count($objactl->array)<=0) $lViewprev = false;
			// echo '<pre>';
			// print_r($objactl->array);
			// echo '</pre>';
			// echo '<hr>antes de activitdad '.$lContinue;
			//array para validar si es visible el insumo
			$aTotalAct       = array();
			$aResumpoaplan   = array();
			$aResumpoaejec   = array();
			$aResumpoacolor  = array();
			$aGraphice_      = array();
			$aGraphicecode_  = array();
			$aGraphicetitle_ = array();
			$aDatawork = array();
			foreach ((array) $objactl->array AS $jl => $objppl)
			{
				$idPreventivo = $objppl->fk_prev;
				$aActejec = array();
				//verificamos el filtro principal
				if ($lPrincipall)
				{
					include DOL_DOCUMENT_ROOT.'/poa/poa/lib/filterprevprin.lib.php';
				}
				//revisamos el filtro local
				include DOL_DOCUMENT_ROOT.'/poa/poa/lib/filterprev.lib.php';
				if (empty($nSearch)) $aActivityf[$obj->id][$objppl->id] = true;

				//armamos el preventivo
				//preventivos
				//obtenemos el preventivo de la gestion
				$objpre     = new Poaprev($db); //preventivos
				if ($objpre->fetch($objppl->fk_prev)>0)
				{
					if ($objpre->id == $objppl->fk_prev)
					{
						$_SESSION['aActivityreg'][$objppl->id]['objpre'] = $objpre;
						$aDatepre = dol_getdate($objpre->date_preventive);
						if (!empty($objpre->date_preventive))
							$aActejec['PREVENTIVE'] = $objpre->date_preventive;
					}
				}
				//obtenemos el proceso del preventivo
				$aProcess = getlist_process($objppl->fk_prev);
				$_SESSION['aActivityreg'][$objppl->id]['aProcess']=$aProcess;
				$fk_prev_pri = $objppl->fk_prev;
				$fk_proc_pri = 0;
				//verificamos si tiene un preventivo principal
				if (count($aProcess['pri'])>0)
					foreach ((array) $aProcess['pri'] AS $fkProcess => $fk_prev_)
					{
						$fk_proc_pri = $fkProcess;//proceso id
						$fk_prev_pri = $fk_prev_;//preventivo id
					}

				//recuperamos el proceso
				$objproc    = new Poaprocess($db); //procesos
				if ($fk_proc_pri>0)	$objproc->fetch($fk_proc_pri);
				if ($objproc->id == $fk_proc_pri)
				{
					//unset($_SESSION['aActivityreg'][$objppl->id]['objproc'][$fk_proc_pri]);
					// $_SESSION['aActivityreg'][$objppl->id]['objproc'][$fk_proc_pri] = $objproc;
					// $_SESSION['aActivityprocess'][$objproc->id] = $objproc;
					$aDatepro = dol_getdate($objproc->date_process);
					if (!empty($objproc->date_process))
						$aActejec['INI_PROCES'] = $objproc->date_process;
				}

				//CONTRATOS
				$objprocc->getlist_contrat2($fk_proc_pri);
				$_SESSION['aActivityreg'][$objppl->id]['objprocc'] = $objprocc->aContrat;
				//obtenemos los contratos
				if (count($objprocc->aContrat)>0 && $fk_proc_pri >0)
				{
					foreach ((array) $objprocc->aContrat AS $fk_c => $value)
					{
						$objcon     = new Contrat($db);
						$objcon->fetch($fk_c);
						if ($objcon->id == $fk_c && $fk_c>0)
						{
							$objcon->fetch_lines();
							$_SESSION['aActivityreg'][$objppl->id]['objcon'][$fk_c] = $objcon;
							$aDatecon = dol_getdate($objcon->date_contrat);
							if (!empty($objcon->date_contrat))
								$aActejec['RECEP_PRODUCTS'][] = $objcon->date_contrat;
						}
					}
				}
				//devengados
				$objdeve    = new Poapartidadev($db);
				$objdeve->getlist($objppl->fk_prev);
				$_SESSION['aActivityreg'][$objppl->id]['objdeve'] = $objdeve;
				if (count($objdeve->array)>0)
				{
					$nro_dev = 0;
					foreach ((array) $objdeve->array AS $o => $objd)
					{
						$aDatedev = dol_getdate($objd->date_dev);
						if ($objd->nro_dev != $nro_dev)
						{
							$nro_dev = $objd->nro_dev;
							if (!empty($objd->date_dev))
								$aActejec['AUTO_PAYMENT'][] = $objd->date_dev;
						}
					}
				}

				//verificamos la planificacion del mes
				$objactdet->getlist_code_date($objppl->id);
				$aActplanif = $objactdet->array;
				$monthActual = date('m') * 1;
				$cCodeAct = ''; //codigo vigente de la fecha
				if (count($aActplanif)>0)
				{
				//tiene planificacion
					if (count($aActejec) > 0)
					{
					//inicio de plan = falso
						$lPlan = false;

					//tiene ejecucion
					//recorremos la planificacion
						foreach ((array) $aActplanif AS $cCode => $dPlan)
						{
							$cCodeAct = $cCode;
						//armamos lo planificado del mes
							$aDateplan = dol_getdate($dPlan);
							if ($aDateplan['mon']> 0 && ($aDateplan['mon'] <= $monthActual && $aDateplan['year'] == date('Y')))
							{
								$lPlan = true;
								unset($aEstado[$objppl->id]);
								if ($aActejec[$cCode])
								{
									if (!is_array($aActejec[$cCode]))
									{
										if ($dPlan >= $aActejec[$cCode])
											$aEstado[$objppl->id] = 1;
										else
										{
											$aDateeje = dol_getdate($aActejec[$cCode]);
											if ($aDateeje['mday'] == $aDateplan['mday'] &&
												$aDateeje['mon'] == $aDateplan['mon'] &&
												$aDateeje['year'] == $aDateplan['year'])
											$aEstado[$objppl->id] = 1;//en fecha
										else
										{
											$aEstado[$objppl->id] = 3;//demorado
											if ($aDateeje['mon'] <= $aDateplan['mon'] && $aDateeje['year'] == $aDateplan['year'])
												$aEstado[$objppl->id] = 1;
										}
									}
								}
								else
									foreach ($aActejec[$cCode] AS $j3 => $dEjec)
									{
										if ($dPlan >= $dEjec)
										{
											$aEstado[$objppl->id] = 1;
										}
										else
										{
											$aDateeje = dol_getdate($dEjec);
											if ($aDateeje['mday'] == $aDateplan['mday'] &&
												$aDateeje['mon'] == $aDateplan['mon'] &&
												$aDateeje['year'] == $aDateplan['year'])
											{
												$aEstado[$objppl->id] = 1;
											}
											else
											{
												$aEstado[$objppl->id] = 3;
											}
										}
									}
								}
								else
								{
									$aEstado[$objppl->id] = 3;
								}
							}
							else
							{
								if (!$lPlan)
								{
									//verificamos la ultima ejecucion
									if (count($aActejec) <=0)
									{
										$aEstado[$objppl->id]  = 3;
									}
									else
										$aEstado[$objppl->id] = 1;
								}
							}
						}
						if (empty($cCodeAct))
						{
							$aEstado[$objppl->id] = 3;
						}
					}
					else
					{
						//no tiene ejecucion si planificacion
						//revisamos si la planificacion esta a tiempo
						$lLoop = true;
						foreach ((array) $aActplanif AS $cCode => $dPlan)
						{
							if ($lLoop)
							{
								//armamos lo planificado del mes
								$aDateplan = dol_getdate($dPlan);
								if ($aDateplan['year'] == $_SESSION['gestion'])
									if ($aDateplan['mon']>0)
										if ($aDateplan['mon'] >= $monthActual)
										{
											$aEstado[$objppl->id] = 1;
										}
										else
										{
											$lLoop = false;
											$aEstado[$objppl->id] = 3;
										}
									}
								}
							}
						}
						else
						{
					//sin planificacion
							$aEstado[$objppl->id] = -1;
						}
						if ($objppl->statut == 9) $aEstado[$objppl->id] = 9;
					}
				//verificamos la revision para activar o no
					$lContinue = false;
				//principal
					$nppSearch=0;
					$aPoaf[$obj->id] = false;
					if ($nSearch)
					{
						foreach ((array) $aPoa[$obj->id] AS $j1 => $val)
						{
							if ($val) $nppSearch++;
						}
						if ($nSearch && $nSearch == $nppSearch)
						{
							$lContinue = true;
							$aPoaf[$obj->id] = true;
						}
					}
					else
						$aPoaf[$obj->id] = true;
					$n_search = 0;
					if (count($aActivity[$obj->id])>0)
					{
				//principal
				foreach ((array) $aActivity[$obj->id] AS $j1 => $aP)//$j1 es el id de la actividad
				{
					$n_search= 0;
					$aActivityf_ = array();
					foreach ((array) $aP AS $j2 => $val)//$j2 es el search
					{
						if ($val)
						{
							$n_search++;
							$aActivityf_[$j2] =true;
						}
					}
					if ($nSearch && $nSearch == $n_search)
					{
						$lContinue = true;
						$aActivityf[$obj->id][$j1] = $lContinue;
						$aPoaf[$obj->id] = $lContinue;
					}
				}
			}
				if (!empty($filtromenu['f2']))//actividades
				$lViewact = true;
				if (empty($nSearch))
				{
					$aPoaf[$obj->id] = true;
				}
				if ($aPoaf[$obj->id])
				{
					$var=!$var;
					if ($var) $backg = 'rpair'; else $backg = 'rimpair';
					$newClase = ' class=" '.$backg.' ';
					$newClasen = ' class="'.$backg.' ';
					$newClase__ = ' class=" ';
					$lStyle = false;
					if ($_SESSION['colorUser'] == true)
					{
						if (empty($aColorUser[$idUser]))
						{
							$aColorUser[$idUser] = randomColor();
							$_SESSION['aColorUser'] = $aColorUser;
						}
						$newClase = ' style="background-color:'.$aColorUser[$idUser].';';
						$lStyle = true;
					}

					if ($_SESSION['colorPartida'] == true)
					{
						if (empty($aColorUser[$obj->partida]))
						{
							$aColorUser[$obj->partida] = randomColor();
							$_SESSION['aColorUser'] = $aColorUser;
						}
						$newClase = ' style="background-color:'.$aColorUser[$obj->partida].';';
						$lStyle = true;
					}
					print '<tr '.$newClase.'">';
					//inicio de la fila
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/liste1.tpl.php';

					//presupuesto
					$nPresup = 0;
					if ($obj->version == 0)
					{
						if ($filtromenu['f1'] == True) $aHtml[$i]['presupuesto'] = $obj->amount;
						$sumaPresup+=$obj->amount;
						$nPresup = $obj->amount;
					}

					//armamos la lista de actividades planificadas
					$lisPrev = '';
					//armamos la lista de preventivos
					$newClase2 = $newClase;
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/lisprev.tpl.php';
					//recuperando el estilo
					$newClase = $newClase2;
					//fin lista preventivos
					///////////////////////////
					//presupuesto budget
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/budget.tpl.php';
					//////////////////////////

					///////////////////////////////
					//reform
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/reform.tpl.php';
					///////////////////////////////

					//preventivo
					$total = 0;
					$balance=0;
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/prev.tpl.php';
					$sumaPrev+=$total;
					$sumaPrevm+= $objprev->total;
					$sumaSaldop+=$balance;
					//fin preventivo
					////////////////////////////
					//comprometido
					$totalc = 0;
					$balancec = 0;
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/comp.tpl.php';
					$sumaComp+=$totalc;
					$sumaCompm+=$objcomp->total;
					$sumaSaldoc+= $balancec;
					///fin comprometido
					////////////////////////////
					//devengado
					$totald = 0;
					$balanced = 0;
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/dev.tpl.php';
					$sumaDeve+=$totald;
					$sumaDevem+=$objdeve->total;
					$sumaSaldod+= $balanced;
					////fun devengado
					////////////////////////////

					//lista calendario
					if (!$lMobile)
						include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/calendar.tpl.php';

					/////////////////////////
					//liste2
					include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/liste2.tpl.php';

					//RESULTADOS PAC

					$newClaseor = $newClase;
					//$objpac->fetch_poa($obj->id);
					$title = '';
					$month_init = 0;
					$month_public = 0;
					$amountPac = 0;
					$saldoPac = $obj->amount; //igual al presupuesto
					$meshoy = (date('m') * 1);
					$lisPac = '';

					$newClase = $newClaseor;
					print '</tr>';
					//lisprev
					print $lisPrev;
				}
				$i++;
				//print '</tr>';
			}//loop
		}

		print '</tbody>';


	//totales de actividades
		//init head 1 buttons
		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/totales.tpl.php';


		print '</table>';
	//print '</div>';
	print '</div>'; //box-body
	print '</div>'; //box
	print '</section>';

	//print '</div>';//master
	//agregamos el datatable de jquery
	print '<!-- DATA TABES SCRIPT -->';
	print '<script src="'.DOL_URL_ROOT.'/poa/js/datatables/jquery.dataTables.js" type="text/javascript"></script>';
	print '<script src="'.DOL_URL_ROOT.'/poa/js/datatables/dataTables.bootstrap.js" type="text/javascript"></script>';
	include DOL_DOCUMENT_ROOT.'/poa/js/datatable.js.php';

	//incluimos el registro nuevo
	include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/newupdate.tpl.php';


	$_SESSION['aHtml'] = $aHtml;
	//aqui se encuentra el registro create o update

	print '<footer class="footer">';
	print '<div class="container">';

	// //agregando los totales
	// $newClase = ' class="left"  style="background-color:#64C2FC;"';

	// $lisPrev= '<span style="color:#000000;">';
	// $lisPrev.= '<div id="meta" '.$newClase.'>'.'&nbsp;'.'</div>';
	// if ($numCol[1] || $numCol[2])
	//   {
	// 	$lisPrev.= '<div id="pseudo" '.$newClase.'>';
	// 	$lisPrev.=  $langs->trans('Totalactivity');
	// 	$lisPrev.=  '</div>';
	//   }
	// $lisPrev.= '<div id="partida" '.$newClase.'>&nbsp;</div>'; //partida

	// $lisPrev.= '<div id="amount" '.$newClase.'>'.price(price2num($aTotalSAct['budget'],'MT')).'</div>';//presupuesto
	// if ($numCol[71])
	//   {
	// 	$lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
	// 	$lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
	//   }

	// if ($numCol[72])
	//   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
	// if ($numCol[73])
	//   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';

	// if($numCol[9] || $numCol[10] || $numCol[15])
	//   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';

	// //comprometidos
	// if ($numCol[11] || $numCol[12] || $numCol[16])
	//   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';
	// //deventados
	// if ($numCol[13] || $numCol[14] || $numCol[17])
	//   $lisPrev.= '<div id="amount" '.$newClase.'>&nbsp;</div>';

	// //lista el cronograma por mes
	// if ($opver == 1)
	//   {
	// 	for ($d = 1; $d <= 12; $d++)
	// 	  {
	// 	    $lisPrev.= '<div id="amountone" class="left '.$newClase.'"> &nbsp;</div>';
	// 	  }
	//   }

	// //usuario
	// $lisPrev.= '<div id="user" '.$newClase.'>&nbsp;</div>';

	// //instruccion
	// if ($numCol[93])
	//   {
	// 	$lisPrev.= '<div id="instruction" '.$newClase.'>';
	// 	$lisPrev.= '&nbsp;';
	// 	$lisPrev.= '</div>';
	//   }
	// //pac
	// $lisPrev.= '<div id="amount" '.$newClase.'>';
	// $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">&nbsp;</a>';
	// $lisPrev.= '</div>';

	// //action
	// $lisPrev.= '<div id="action" '.$newClase.'>';
	// $lisPrev.= '&nbsp;';
	// $lisPrev.= '</div>';


	// //finalizar la lista
	// $lisPrev.= '</span>';
	// //$lisPrev.= '<div style="clear:both"></div>';
	// print $lisPrev;
	// //fin total actividades



	//    $db->free($result);

	print "<div class=\"tabsAction\">\n";

	if ($action != 'create' || $action != 'edit')
	{
		if ($user->rights->poa->poa->crear)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create&dol_hide_leftmenu=1">'.$langs->trans("Createnew").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
		if ($user->rights->poa->exp)
			print "<a class=\"butAction\" href=\"fiche_excel.php\">".$langs->trans("Excel")."</a>";
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Excel")."</a>";
	}
	print '</div>';

	print '</div>';
	print '</footer>';
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
