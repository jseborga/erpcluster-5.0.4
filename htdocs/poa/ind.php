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
 *      \file       htdocs/poa/execution/ficheprev.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page edit all list workflow
 */

require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once(DOL_DOCUMENT_ROOT."/poa/class/poastructureext.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/class/html.formadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoauser.class.php';
require_once(DOL_DOCUMENT_ROOT."/poa/class/poaactivityext.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/activity/class/poaactivitydet.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poaprevext.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poapartidapreext.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidacom.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidadevadd.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapredetext.class.php';
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");
require_once DOL_DOCUMENT_ROOT."/poa/process/class/poaprocesscontrat.class.php";
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivityworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulated.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulateddet.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulatedof.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulatedto.class.php");

require_once DOL_DOCUMENT_ROOT.'/poa/guarantees/class/poaguarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/appoint/class/poacontratappoint.class.php';
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/main.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poagraf.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/contrat.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/doc.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/html.formadd.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poamenu.class.php");

require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if ($conf->addendum->enabled)
	require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

if ($conf->poai->enabled)
{
	require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
	require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
}
if ($conf->orgman->enabled)
{
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentext.class.php");
	require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentuserext.class.php");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';

$langs->load("poa@poa");

if (!$user->rights->poa->prev->leer) accessforbidden();

$_SESSION['localuri'] = $_SERVER['REQUEST_URI'];

$object     = new Poapoaext($db);
$objpre     = new Poaprevext($db); //preventivos
$objprev    = new Poapartidapreext($db);
$objcomp    = new Poapartidacom($db);
$objdeve    = new Poapartidadevadd($db);
$objrefo    = new Poareformulated($db);
$objrefodet = new Poareformulateddet($db);
$objpac     = new Poapac($db);
$objpacc    = new Poapac($db);
$objproc    = new Poaprocess($db); //procesos
$objprocc   = new Poaprocesscontrat($db);
$objrefoof  = new Poareformulatedof($db);
$objrefoto  = new Poareformulatedto($db);
$objectuser = new Poapoauser($db);
$objuser    = new User($db);
$objstr     = new Poastructureext($db);
$objact     = new Poaactivityext($db);
$objactdet  = new Poaactivitydet($db);
$objactw    = new Poaactivityworkflow($db);
$objwork    = new Poaworkflow($db);
$objarea = new Pdepartamentext($db);
$objareauser = new Pdepartamentuserext($db);
$departament = new Pdepartamentext($db);

$objsoc      = new Societe($db);
$objmenu    = new Poamenu($db);

$formv = new Formadd($db);
//$objacpr    = new Poaactivityprev($db);
//$objactwor  = new Poaactivityworkflow($db);//REVISAR
//$objactwork = new Poaactivitywork($db); //grupos de trabajo

$objcon     = new Contrat($db);
$extrafields = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($objcon->table_element);

if ($conf->poai->enabled)
{
	$objinst = new Poaiinstruction($db);
	$objmoni = new Poaimonitoring($db);
}
$lAllArea = false;
if ($user->admin) $lAllArea = true;
$aArea = $objareauser->getuserarea($user->id,false);
//verificamos que priviletio tiene
foreach ((array) $aArea AS $i => $obj)
{
	$aPriv[$user->id] = $obj->privilege;
	$aPrivarea[$user->id][$obj->id] = $obj->privilege;
}
//asignando filtro de usuario
assign_filter_user('psearch_user');
$id = GETPOST('id','int');
//echo 'admin '.$user->admin;
if (!$user->admin)
{
	$idsArea = filter_area_user($user->id,false);
	$aIds = explode(',',$idsArea);
	$aArea = array();
	foreach ($aIds AS $j => $idarea)
	{
		//agregamos el que tiene
		$objarea->fetch($idarea);
		$aArea[$idarea] = $objarea;
		$objarea ->getlist_son($idarea);
		//armamos en un array las gerencias, subgerencias y deptos.
		foreach ($objarea->array AS $i => $obj)
		{
			$aArea[$i] = $obj;
		}
	}
}
elseif($lAllArea || $id>0)
{
	//recorremos todas las areas
	/*
	if ($lAllArea && empty($id))
		$objarea ->getlist_son(-1,0);
	if ($id>0)
		$objarea ->getlist_son($id);
	//armamos en un array las gerencias, subgerencias y deptos.
	$aArea = array();
	foreach ($objarea->array AS $i => $obj)
	{
		$aArea[$i] = $obj;
		//nuevamente buscamos si tiene hijos del hijo
	}
	*/

	//recorremos todas las areas
	if ($lAllArea && empty($id))
		$departament ->getlist_son(-1,0);
	if ($id>0)
		$departament ->getlist_son($id);
	//armamos en un array las gerencias, subgerencias y deptos.
	$aArea = array();
	foreach ($departament->array AS $i => $obj)
	{
		$aArea[$i] = $obj;
		//nuevamente buscamos si tiene hijos del hijo
	}

}

//echo '<hr>idsarea '.$idsArea;
//print_r($aArea);
//foreach ((array) $aArea AS $i)
//	echo '<hr>i '.$i;
//exit;
//period_year
$period_year = GETPOST('period_year');

//mes actual
$aDateActual = dol_getdate(dol_now());
$monActual = $aDateActual['mon'];

//period_year definida en index.php
if (isset($_POST['period_year']))
	$_SESSION['period_year'] = $_POST['period_year'];
if (empty($_SESSION['period_year']))
	$_SESSION['period_year'] = $aDateActual['year'];

$period_year = $_SESSION['period_year'];





$action_s = GETPOST('action_s');
$a_s = GETPOST('a_s');
if (isset($_POST['a_s']) && $_POST['a_s'] == 1)
{
	unset($_SESSION['action_s']);
	//solo search_all
	$cDetail = '';
	$objmenu->fetch('',$user->id);
	$aDetail = explode('|',$objmenu->detail);
	foreach ((array) $aDetail AS $i1 => $value)
	{
		if (substr($value,0,10) != 'search_all')
		{
			if (!empty($cDetail)) $cDetail.= '|';
			$cDetail.= $value;
		}
	}
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
$modal  = GETPOST('modal','alpha');
$action = GETPOST('action','alpha');
$search_login = GETPOST('search_login');
$search = GETPOST('search_all');
if (isset($_POST['action_s']))
	$_SESSION['action_s'] = $_POST['action_s'];
if (isset($_POST['search_all']))
	$_SESSION['search_all'] = $_POST['search_all'];
if (!isset($action_s) || empty($action_s))
	$action_s = $_SESSION['action_s'];
if ($action_s == 'search')
{
	$search = GETPOST('search_all');
	if (empty($seach))
		$search = $_SESSION['search_all'];
}
$aDateactual = dol_getdate(dol_now());
$nMesactual = $aDateactual['mon'];
$aLabelspan = array();
$aType = array(1=>'Desarrollo',2=>'Funcionamiento');

//actualizamos tabla poapartidadev
if ($conf->global->POA_UPDATE_DEV)
	$res = $objdeve->actualizadev($period_year);
//exit;

//actions
if ($modal == 'fichepoa')
	include DOL_DOCUMENT_ROOT.'/poa/poa/lib/crud_poa.lib.php';

//cabecera
header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/bootstrap/css/bootstrap.css','poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css','poa/css/AdminLTE.css');

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css','poa/css/select_dependientes.css','poa/css/bootstrapadd.css','poa/css/slider/css/slider.css');
//nueva v.3.3.3
$aArrayofcss= array('poa/bootstrap/css/bootstrap.css','poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/dist/css/AdminLTE.css','poa/dist/css/AdminLTE.min.css','poa/dist/css/skins/_all-skins.min.css','poa/css/select_dependientes.css','poa/css/bootstrapadd.css','poa/css/slider/css/slider.css');


$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js','poa/js/select_dependientes.js','poa/js/fiche_process.js');

top_htmlheadv($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';

$form=new Form($db);
$formadd = new Formadd($db);

//cuerpo
print '<br><br><br>';
print '<section class="content">';
//buscador
print '<div class="row">';
print '<div class="col-md-12">';
print '<div class="box box-info">';
print '<div class="box-header with-border">';
print '<div class="box-body">';
print '<div class="input-group">';
print '<span class="input-group-addon">';
print '<i class="fa fa-search"></i>';
print '</span>';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action_s" value="search">';

//print '<form method="POST" action="'.DOL_URL_ROOT.'/poa/poa/liste.php'.'">';
print '<input class="form-control" type="text" name="search_all" value="'.$search.'" placeholder="'.$langs->trans('Search').'">';
print '</form>';
print '</div>';
print '</div>';
print '</div>';
print '</div>';
print '</div>';

print '</div>'; //row
//pagina principal
print '<div class="row">';
if ($search || $search_login)
{
	print '<div class="col-md-12">';
	include DOL_DOCUMENT_ROOT.'/poa/tpl/liste.tpl.php';
	print '</div>';
}
else
{
	$aEst = array();
	$aEstructure = array();
	//obtenemos las modificaciones al presupuesto aprobadas 
	$objrefo->fetch_version_gestion($period_year);
	$aReform = array();
	foreach ((array) $objrefo->array AS $fkid => $obj_ref)
	{
		if ($obj_ref->period_year == $period_year)
		{
			$lVersionAp = true;
			$nVersionAp = $obj_ref->version;
			$aReform[$obj_ref->id] = $obj_ref->id;
		}
	}
	if (count($aReform)>0) list($aOfa,$aOfonea,$aOfrefa) = $objrefodet->get_sumaref($aReform);
	//fin reformulado
	if (count($aArea)>0)
	{
		if (!$user->admin) $aPrivilege['privilege'] = $aPriv[$user->id];
		//armamos el primer nivel

		foreach ($aArea AS $i => $obj)
		{
			$aCountmeta = array();
			$aSummeta = array();
			$aCounttask = array();
			$aSumtask = array();
			$aCounttaskfin = array();
			$aSumtaskfin = array();
			//otra forma
			$aCuentameta     = array();
			$aSumameta       = array();
			$aCuentatarea    = array();
			$aSumatarea      = array();
			$aCuentatareafin = array();
			$aSumatareafin   = array();
			//resumen de pac
			$aCuentapac      = array();
			$aCuentapacfin   = array();
			$aSumapac        = array();
			$aSumapacfin     = array();
			$aCuentapacej[$obj->id] = 0;
			$aCuentapacne[$obj->id] = 0;
			$aCuentapacse[$obj->id] = 0;
			$aCuentapacpen[$obj->id] = 0;
			if (($obj->fk_father == -1 || empty($obj->fk_father)) || $id>0 || (!$user->admin && count($aArea)>0))
			{
				$aListarea = getlistareason($obj->id);
				//obtenemos las sumas de metas (insumos) por cada area
				foreach ((array) $aListarea AS $fk_area => $aPrivilege_)
				{
					//lista la estructura por area
					$objstr->getlist_area($fk_area,$period_year);
					foreach ((array) $objstr->array AS $k => $objs)
					{
						$aEstructure[$obj->id][$objs->id]['sigla'] = $objs->sigla;
						$aEstructure[$obj->id][$objs->id]['id'] = $objs->id;
						$aEstructure[$obj->id][$objs->id]['fk_father'] = $objs->fk_father;
						$aEstructure[$obj->id][$objs->id]['type'] = $objs->type;
						$aEst[$objs->type][$objs->id] = $objs->id;
						//verificamos la reformulacion del poa

						//lista el poa por structure
						$object->getlist_structure($objs->id);
						//recorremos y sumamos las metas
						foreach((array) $object->array AS $l => $objm)
						{
							$lAct = false;
							if ($user->admin)
							{
								$aCountmeta[$obj->id]++;
								$aSummeta[$obj->id]+=$objm->amount;
								//verificamos con la reformulacion
								$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
								$nTotalAp = $objm->amount+$nReformap;
								$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;

								$objact->resume_activity_user($objm->id,0,$period_year);
								$objpac->resume_pac_user($objm->id,0,$period_year);
								$objdeve->resume_deve_user($period_year,0,$objs->id,$objm->id,$objm->partida,1);
							}
							else
							{
								switch ($aPrivilege['privilege'])
								{
									case 1:
									//es administrador
									$aCountmeta[$obj->id]++;
									$aSummeta[$obj->id]+=$objm->amount;
									//verificamos con la reformulacion
									$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
									$nTotalAp = $objm->amount+$nReformap;
									$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;

									//lista y cuenta las tareas
									$objact->resume_activity_user($objm->id,0,$period_year);
									$objpac->resume_pac_user($objm->id,0,$period_year);
									$objdeve->resume_deve_user($period_year,0,$objs->id,$objm->id,$objm->partida,1);

									break;
									case 2:
									//es usuario
									//lista las metas por usuario
									$objectuser->getlist($objm->id,1);
									$lMeta = true;
									foreach ((array) $objectuser->array AS $l1 => $objpu)
									{
										//si es la meta creada para el usuario,
										if ($objpu->fk_user == $user->id)
										{
											if ($lMeta)
											{
												$aCountmeta[$obj->id]++;
												$aSummeta[$obj->id]+=$objm->amount;
												//verificamos con la reformulacion
												$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
												$nTotalAp = $objm->amount+$nReformap;
												$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;
												//$aEstructure[$obj->id][$objs->id]['amount'] += $objm->amount;

												$lMeta = false;

												//listamos las actividades
												$objact->resume_activity_user($objm->id,$user->id,$period_year);
												$objpac->resume_pac_user($objm->id,$user->id,$period_year);
												$objdeve->resume_deve_user($period_year,$user->id,$objs->id,$objm->id,$objm->partida,1);
												$lAct = true;
											}
										}
									}
									break;
									case 3:
									//es visitante
									$aCountmeta[$obj->id]++;
									$aSummeta[$obj->id]+=$objm->amount;
									//verificamos con la reformulacion
									$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
									$nTotalAp = $objm->amount+$nReformap;
									$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;
									//$aEstructure[$obj->id][$objs->id]['amount'] += $objm->amount;
									$objact->resume_activity_user($objm->id,0,$period_year);
									$objpac->resume_pac_user($objm->id,0,$period_year);
									$objdeve->resume_deve_user($period_year,0,$objs->id,$objm->id,$objm->partida,1);
									break;
									default:
									//$aCountmeta[$obj->id]++;
									//$aSummeta[$obj->id]+=$objm->amount;
									//$aCounttask[$obj->id] += $objact->getlist_poa($objm->id);
									break;
								}
							}
							if ($user->admin)
							{
								$aCuentatarea[$obj->id] += $objact->aCount[0];
								$aCuentatareafin[$obj->id] += $objact->aCountfin[0];
								$aSumatarea[$obj->id] += $objact->aSum[0];
								$aSumatareafin[$obj->id] += $objact->aSumfin[0];
								$aEstructure[$obj->id][$objs->id]['ejecuted'] += $objdeve->aSum[0];
								//pac
								$aCuentapac[$obj->id] += $objpac->aCount[0];
								$aSumapac[$obj->id] += $objpac->aSum[0];
								//revisamos si pac esta cumplido o no
								$aPacact = $objact->aPac[0];
								foreach((array) $aPacact AS $pacid => $aDate)
								{
									$b++;
									$nperiod_year = $objpac->aPacg[0][$pacid];
									$mes      = $objpac->aPacm[0][$pacid];
									if ($nperiod_year > 0 && $nperiod_year == $aDate['year'])
									{
										if ($aDate['mon'] > $mes)
											$aCuentapacne[$obj->id]++;
										else
											$aCuentapacej[$obj->id]++;
									}
								}
								//pac no ejecutado, se revisa la fecha inicio con el mes actual
								$aPacne = $objact->aPacne[0];
								foreach ((array) $aPacne AS $p1)
								{
									$objpacc->fetch($p1);
									if ($objpacc->id == $p1)
									{
										if ($objpacc->month_init < $nMesactual)
											$aCuentapacse[$obj->id]++;
										else
											$aCuentapacpen[$obj->id]++;
									}
								}
							}
							else
							{
								switch ($aPrivilege['privilege'])
								{
									case 1:
									$aCuentatarea[$obj->id] += $objact->aCount[0];
									$aCuentatareafin[$obj->id] += $objact->aCountfin[0];
									$aSumatarea[$obj->id] += $objact->aSum[0];
									$aSumatareafin[$obj->id] += $objact->aSumfin[0];
									$aEstructure[$obj->id][$objs->id]['ejecuted'] += $objdeve->aSum[0];
									//pac
									$aCuentapac[$obj->id] += $objpac->aCount[0];
									$aSumapac[$obj->id] += $objpac->aSum[0];
									//revisamos si pac esta cumplido o no
									$aPacm = $objpac->aPacm[0];
									$aPacact = $objact->aPac[0];
									foreach((array) $aPacact AS $pacid => $aDate)
									{
										$nperiod_year = $objpac->aPacg[0][$pacid];
										$mes      = $objpac->aPacm[0][$pacid];
										if ($nperiod_year == $aDate['year'])
										{
											if ($aDate['mon'] > $mes)
												$aCuentapacne[$obj->id]++;
											else
												$aCuentapacej[$obj->id]++;
										}
									}
									//pac no ejecutado, se revisa la fecha inicio con el mes actual
									$aPacne = $objact->aPacne[0];
									foreach ((array) $aPacne AS $p1)
									{
										$objpacc->fetch($p1);
										if ($objpacc->id == $p1)
										{
											if ($objpacc->month_init < $nMesactual)
												$aCuentapacse[$obj->id]++;
											else
												$aCuentapacpen[$obj->id]++;
										}
									}
									break;
									case 2:
									$aCuentatarea[$obj->id] = $objact->aCount[$user->id];
									$aCuentatareafin[$obj->id] = $objact->aCountfin[$user->id];
									$aSumatarea[$obj->id] = $objact->aSum[$user->id];
									$aSumatareafin[$obj->id] = $objact->aSumfin[$user->id];
									$aEstructure[$obj->id][$objs->id]['ejecuted'] += $objdeve->aSum[$user->id];
									//pac
									$aCuentapac[$obj->id] = $objpac->aCount[$user->id];
									$aSumapac[$obj->id] = $objpac->aSum[$user->id];
									//revisamos si pac esta cumplido o no
									$aPacm = $objpac->aPacm[$user->id];
									$aPacact = $objact->aPac[$user->id];
									foreach((array) $aPacact AS $pacid => $aDate)
									{
										$nperiod_year = $objpac->aPacg[0][$pacid];
										$mes      = $objpac->aPacm[0][$pacid];
										if ($nperiod_year == $aDate['year'])
										{
											if ($aDate['mon'] > $mes)
												$aCuentapacne[$obj->id]++;
											else
												$aCuentapacej[$obj->id]++;
										}
									}
									//pac no ejecutado, se revisa la fecha inicio con el mes actual
									$aPacne = $objact->aPacne[$user->id];
									foreach ((array) $aPacne AS $p1)
									{
										$objpacc->fetch($p1);
										if ($objpacc->id == $p1)
										{
											if ($objpacc->month_init < $nMesactual)
												$aCuentapacse[$obj->id]++;
											else
												$aCuentapacpen[$obj->id]++;
										}
									}
									break;
									case 3:
									$aCuentatarea[$obj->id] = $objact->aCount[0];
									$aCuentatareafin[$obj->id] = $objact->aCountfin[0];
									$aSumatarea[$obj->id] = $objact->aSum[0];
									$aSumatareafin[$obj->id] = $objact->aSumfin[0];
									$aEstructure[$obj->id][$objs->id]['ejecuted'] += $objdeve->aSum[0];
									//pac
									$aCuentapac[$obj->id] = $objpac->aCount[0];
									$aSumapac[$obj->id] = $objpac->aSum[0];
									//revisamos si pac esta cumplido o no
									$aPacm = $objpac->aPacm[0];
									$aPacact = $objact->aPac[0];
									foreach((array) $aPacact AS $pacid => $aDate)
									{
										$nperiod_year = $objpac->aPacg[0][$pacid];
										$mes      = $objpac->aPacm[0][$pacid];
										if ($nperiod_year == $aDate['year'])
										{
											if ($aDate['mon'] > $mes)
												$aCuentapacne[$obj->id]++;
											else
												$aCuentapacej[$obj->id]++;
										}
									}
									//pac no ejecutado, se revisa la fecha inicio con el mes actual
									$aPacne = $objact->aPacne[0];
									foreach ((array) $aPacne AS $p1)
									{
										$objpacc->fetch($p1);
										if ($objpacc->id == $p1)
										{
											if ($objpacc->month_init < $nMesactual)
												$aCuentapacse[$obj->id]++;
											else
												$aCuentapacpen[$obj->id]++;
										}
									}
									break;
								}
							}

						}
					}

					//$objact->resume_activity_user($objm->id,0,$period_year);
					//$objpac->resume_pac_user($objm->id,0,$period_year);

						// += $objpoa->getlist_structure($objs->id,1);
						//buscamos las tareas
							//$aSummeta[$obj->id]+=$objm->amount;
							//nuevo resumen

				}
				$nCountStructure = 0;
				$aStr =array();
				$aRes = array();
				$aStruc = $aEstructure[$obj->id];
				foreach((array) $aEst AS $type =>$aData)
				{
					foreach ($aData AS $fkid)
					{
						$aStr[$type]['amount']+=$aStruc[$fkid]['amount'];
						$aStr[$type]['ejecuted']+= $aStruc[$fkid]['ejecuted'];
					}
				}
				//$aTypestructure = array();
				//para enviar los objetivos calculamos
				foreach ((array) $aEstructure[$obj->id] AS $o => $row)
				{
					if ($row['fk_father'] < 0)
					{
						$aTypestructure[$row['type']]['count']++;
					}
					//$aStr[$row['type']]+=$row['amount'];
				}
				include DOL_DOCUMENT_ROOT.'/poa/tpl/ind.tpl.php';
				$aStr =array();
				$aRes = array();
				$aTypestructure = array();
			}
		}
	}
	else
	{
		if (!$user->admin)
			$aPrivilege['privilege'] = $aPriv[$user->id];
		//listamos por usuario
		//obtenemos los usuarios del area
		$objareauser->getareauser($id,'user');
		foreach ((array) $objareauser->lines AS $j => $obj)
		{
			$fk_user = $obj->fk_user;
			$objuser->fetch($fk_user);
			//recorremos por usuario
			//otra forma
			$aCuentameta     = array();
			$aSumameta       = array();
			$aCuentatarea    = array();
			$aSumatarea      = array();
			$aCuentatareafin = array();
			$aSumatareafin   = array();
			//resumen de pac
			$aCuentapac      = array();
			$aCuentapacfin   = array();
			$aSumapac        = array();
			$aSumapacfin     = array();
			$aCuentapacej[$obj->id] = 0;
			$aCuentapacne[$obj->id] = 0;
			$aCuentapacse[$obj->id] = 0;
			$aCuentapacpen[$obj->id] = 0;
			$aEst = array();
			$aEstructure = array();
			$fk_area = $id;
			$aListarea = getlistareason($obj->id);
			//obtenemos las sumas de metas (insumos) por cada area
			//lista la estructura por area
			$objstr->getlist_area($fk_area,$period_year);
			foreach ((array) $objstr->array AS $k => $objs)
			{
				$aEstructure[$obj->id][$objs->id]['sigla'] = $objs->sigla;
				$aEstructure[$obj->id][$objs->id]['id'] = $objs->id;
				$aEstructure[$obj->id][$objs->id]['fk_father'] = $objs->fk_father;
				$aEstructure[$obj->id][$objs->id]['type'] = $objs->type;
				$aEst[$objs->type][$objs->id] = $objs->id;

				//lista el poa por structure
				$object->getlist_structure($objs->id);
				//recorremos y sumamos las metas
				foreach((array) $object->array AS $l => $objm)
				{
					//poa user
					$objectuser->getlist($objm->id,1);
					foreach ((array) $objectuser->array AS $l1 => $objpu)
					{
						//si es la meta creada para el usuario,
						//if ($objpu->fk_user == $fk_user)
						//{
						$lAct = false;
						if ($user->admin)
						{
							if($fk_user == $objpu->fk_user)
							{
								$aCountmeta[$obj->id]++;
								$aSummeta[$obj->id]+=$objm->amount;
								//verificamos con la reformulacion
								$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
								$nTotalAp = $objm->amount+$nReformap;
								$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;
							}
						}
						else
						{
							switch ($aPrivilege['privilege'])
							{
								case 1:
									//es administrador
								if ($fk_user == $objpu->fk_user)
								{
									$aCountmeta[$obj->id]++;
									$aSummeta[$obj->id]+=$objm->amount;
									//verificamos con la reformulacion
									$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
									$nTotalAp = $objm->amount+$nReformap;
									$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;
								}
								break;
								case 2:
									//es usuario
									//lista las metas por usuario
								$lMeta = true;
								if ($lMeta)
								{
									if ($fk_user == $objpu->fk_user)
									{
										$aCountmeta[$obj->id]++;
										$aSummeta[$obj->id]+=$objm->amount;
										//verificamos con la reformulacion
										$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
										$nTotalAp = $objm->amount+$nReformap;
										$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;
										$lMeta = false;
									}
								}
								break;
								case 3:
									//es visitante
								if ($fk_user == $objpu->fk_user)
								{
									$aCountmeta[$obj->id]++;
									$aSummeta[$obj->id]+=$objm->amount;
									//verificamos con la reformulacion
									$nReformap = $aOfa[$objs->id][$objm->id][$objm->partida];
									$nTotalAp = $objm->amount+$nReformap;
									$aEstructure[$obj->id][$objs->id]['amount'] += $nTotalAp;
								}
								break;
								default:
									//$aCountmeta[$obj->id]++;
									//$aSummeta[$obj->id]+=$objm->amount;
									//$aCounttask[$obj->id] += $objact->getlist_poa($objm->id);
								break;
							}
						}
					}
				}
			}
					//buscamos las tareas por usuario
			$objact->resume_activity_user(0,$fk_user,$period_year);
			$objpac->resume_pac_user(0,$fk_user,$period_year);

			//$objact->resume_activity_user($objm->id,0,$period_year);
			//$objpac->resume_pac_user($objm->id,0,$period_year);
			$objdeve->resume_deve_user($period_year,$fk_user,$objs->id,0,'',1);

							//verificamos rsultado
			if ($user->admin)
			{
				$aCuentatarea[$obj->id] += $objact->aCount[$fk_user];
				$aCuentatareafin[$obj->id] += $objact->aCountfin[$fk_user];
				$aSumatarea[$obj->id] += $objact->aSum[$fk_user];
				$aSumatareafin[$obj->id] += $objact->aSumfin[$fk_user];
							//pac
				$aCuentapac[$obj->id] += $objpac->aCount[$fk_user];
				$aSumapac[$obj->id] += $objpac->aSum[$fk_user];
				$aSumapay[$obj->id] += $objdeve->aSum[$fk_user];
						//revisamos si pac esta cumplido o no
				$aPacact = $objact->aPac[$fk_user];
				foreach((array) $aPacact AS $pacid => $aDate)
				{
					$b++;
					$nperiod_year = $objpac->aPacg[$fk_user][$pacid];
					$mes      = $objpac->aPacm[$fk_user][$pacid];
					if ($nperiod_year > 0 && $nperiod_year == $aDate['year'])
					{
						if ($aDate['mon'] > $mes)
							$aCuentapacne[$obj->id]++;
						else
							$aCuentapacej[$obj->id]++;
					}
				}
						//pac no ejecutado, se revisa la fecha inicio con el mes actual
				$aPacne = $objact->aPacne[$fk_user];
				foreach ((array) $aPacne AS $p1)
				{
					$objpacc->fetch($p1);
					if ($objpacc->id == $p1)
					{
						if ($objpacc->month_init < $nMesactual)
							$aCuentapacse[$obj->id]++;
						else
							$aCuentapacpen[$obj->id]++;
					}
				}
			}
			else
			{
				switch ($aPrivilege['privilege'])
				{
					case 1:
					$aCuentatarea[$obj->id] += $objact->aCount[$fk_user];
					$aCuentatareafin[$obj->id] += $objact->aCountfin[$fk_user];
					$aSumatarea[$obj->id] += $objact->aSum[$fk_user];
					$aSumatareafin[$obj->id] += $objact->aSumfin[$fk_user];
								//pac
					$aCuentapac[$obj->id] += $objpac->aCount[$fk_user];
					$aSumapac[$obj->id] += $objpac->aSum[$fk_user];
					$aSumapay[$obj->id] += $objdeve->aSum[$fk_user];
								//revisamos si pac esta cumplido o no
					$aPacm = $objpac->aPacm[$fk_user];
					$aPacact = $objact->aPac[$fk_user];
					foreach((array) $aPacact AS $pacid => $aDate)
					{
						$nperiod_year = $objpac->aPacg[$fk_user][$pacid];
						$mes = $objpac->aPacm[$fk_user][$pacid];
						if ($nperiod_year == $aDate['year'])
						{
							if ($aDate['mon'] > $mes)
								$aCuentapacne[$obj->id]++;
							else
								$aCuentapacej[$obj->id]++;
						}
					}
					//pac no ejecutado, se revisa la fecha inicio con el mes actual
					$aPacne = $objact->aPacne[$fk_user];
					foreach ((array) $aPacne AS $p1)
					{
						$objpacc->fetch($p1);
						if ($objpacc->id == $p1)
						{
							if ($objpacc->month_init < $nMesactual)
								$aCuentapacse[$obj->id]++;
							else
								$aCuentapacpen[$obj->id]++;
						}
					}
					break;
					case 2:
					$aCuentatarea[$obj->id] += $objact->aCount[$fk_user];
					$aCuentatareafin[$obj->id] += $objact->aCountfin[$fk_user];
					$aSumatarea[$obj->id] += $objact->aSum[$fk_user];
					$aSumatareafin[$obj->id] += $objact->aSumfin[$fk_user];
								//pac
					$aCuentapac[$obj->id] += $objpac->aCount[$fk_user];
					$aSumapac[$obj->id] += $objpac->aSum[$fk_user];
					$aSumapay[$obj->id] += $objdeve->aSum[$fk_user];

								//revisamos si pac esta cumplido o no
					$aPacm = $objpac->aPacm[$fk_user];
					$aPacact = $objact->aPac[$fk_user];
					foreach((array) $aPacact AS $pacid => $aDate)
					{
						$nperiod_year = $objpac->aPacg[0][$pacid];
						$mes = $objpac->aPacm[0][$pacid];
						if ($nperiod_year == $aDate['year'])
						{
							if ($aDate['mon'] > $mes)
								$aCuentapacne[$obj->id]++;
							else
								$aCuentapacej[$obj->id]++;
						}
					}
					//pac no ejecutado, se revisa la fecha inicio con el mes actual
					$aPacne = $objact->aPacne[$fk_user];
					foreach ((array) $aPacne AS $p1)
					{
						$objpacc->fetch($p1);
						if ($objpacc->id == $p1)
						{
							if ($objpacc->month_init < $nMesactual)
								$aCuentapacse[$obj->id]++;
							else
								$aCuentapacpen[$obj->id]++;
						}
					}
					break;
					case 3:
					$aCuentatarea[$obj->id] += $objact->aCount[$fk_user];
					$aCuentatareafin[$obj->id] += $objact->aCountfin[$fk_user];
					$aSumatarea[$obj->id] += $objact->aSum[$fk_user];
					$aSumatareafin[$obj->id] += $objact->aSumfin[$fk_user];
								//pac
					$aCuentapac[$obj->id] += $objpac->aCount[$fk_user];
					$aSumapac[$obj->id] += $objpac->aSum[$fk_user];
					$aSumapay[$obj->id] += $objdeve->aSum[$fk_user];
								//revisamos si pac esta cumplido o no
					$aPacm = $objpac->aPacm[$fk_user];
					$aPacact = $objact->aPac[$fk_user];
					foreach((array) $aPacact AS $pacid => $aDate)
					{
						$nperiod_year = $objpac->aPacg[$fk_user][$pacid];
						$mes = $objpac->aPacm[$fk_user][$pacid];
						if ($nperiod_year == $aDate['year'])
						{
							if ($aDate['mon'] > $mes)
								$aCuentapacne[$obj->id]++;
							else
								$aCuentapacej[$obj->id]++;
						}
					}
					//pac no ejecutado, se revisa la fecha inicio con el mes actual
					$aPacne = $objact->aPacne[$fk_user];
					foreach ((array) $aPacne AS $p1)
					{
						$objpacc->fetch($p1);
						if ($objpacc->id == $p1)
						{
							if ($objpacc->month_init < $nMesactual)
								$aCuentapacse[$obj->id]++;
							else
								$aCuentapacpen[$obj->id]++;
						}
					}
					break;
				}
			}
			$nCountStructure = 0;
			$aStr =array();
			$aRes = array();
			$aStruc = $aEstructure[$obj->id];
			foreach((array) $aEst AS $type =>$aData)
			{
				foreach ($aData AS $fkid)
				{
					$aStr[$type]['amount']+=$aStruc[$fkid]['amount'];
					$aStr[$type]['ejecuted']+= $aStruc[$fkid]['ejecuted'];
				}
			}
			foreach ((array) $aEstructure[$obj->id] AS $o => $row)
			{
				if ($row['fk_father'] < 0)
				{
					$aTypestructure[$row['type']]['count']++;
				}
			}
			include DOL_DOCUMENT_ROOT.'/poa/tpl/induser.tpl.php';
			$aStr =array();
			$aRes = array();
			$aTypestructure = array();
			//nuevo resumen
		}
	}
}

//print '</div>';
//print '</div>';
print '</section>';
print '</div>';
include DOL_DOCUMENT_ROOT.'/poa/lib/js.lib.php';

$db->close();



llxFooter();

?>