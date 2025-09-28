<?php
/* Copyright (C) 2014-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/activity/fiche.php
 *	\ingroup    Activities
 *	\brief      Page fiche POA activitie
 */


$langs->load("poa@poa");

$action=GETPOST('action');

$aExcludeArea = array();
$idFather = 0;

//areas a las que pertenece el usuario
if (!$user->admin)
{
	$aArea = $objareauser->getuserarea($user->id);
	foreach((array) $aArea AS $idArea => $objAr)
	{
	//$idFather = $objarea->getfatherarea($idArea);
		$idFather = $idArea;
	}

}



/*
 * View
 */

$form=new Form($db);


$display ='none';
if (isset($modal) && $modal == 'ficheactivity')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#ficheactivity'.$tagmeta.'").modal("show");
	});
</script>';
}

print '<div id="ficheactivity'.$tagmeta.'" class="modal" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';
//CAMBIAR a none

print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">'.$langs->trans("Activity").'</h4>
</div>';

print '<div class="modal-body">';
//print '<div class="row">';
//print '<div class="col-md-12">';
//print '<div class="inner">';



//filtro
$idTag1 = 1;
$idTag2 = 2;

//cuerpo
if ($user->rights->poa->act->crear)
{
	$fk_poa = GETPOST('fk_poa');
	$objpoa->fetch($fk_poa);
	$objstr->fetch($objpoa->fk_structure);
	$objpac->fetch_poa($fk_poa);
	$lLoop = count($objpac->array);
	print_fiche_titre($langs->trans("Newactivity") .' '.$fk_poa);
	//armamos el script para que se ejecute por todas las lineas del pac
	if ($lLoop > 0)
	{

		for ($k = 1; $k <= $lLoop; $k++)
		{
			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {';
			print '$("#selectfk_pac'.$k.'").change(function() {';
			print ' document.form_fiche.action.value="createedit";
			document.form_fiche.submit(); }); });';
			print '</script>'."\n";
		}

	}
	print '<form name="form_fiche" class="form-horizontal" role="form" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="fk_poa" value="'.$fk_poa.'">';
	print '<input type="hidden" name="modal" value="ficheactivity">';

	dol_htmloutput_mesg($mesg);

	//print '<table class="border" width="100%">';

	// pac
	//listamos todos los pac que afecten al poa
	$k = 0;

	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Select').'</label>';
	print '<div class="col-sm-10">';
	print '<span>'.$langs->trans('PAC').'</span>';
	print '</div>';
	print '</div>';

	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">';
	print '<input type="radio" '.(empty($objact->fk_pac)?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="0">';
	print '</label>';
	print '<div class="col-sm-10">';
	print $langs->trans('Noselection');
	print '</div>';
	print '</div>';

	$k++;
	foreach((array) $objpac->array AS $j => $obj_pac)
	{
		print '<div class="form-group">';
		print '<label class="control-label col-sm-2">';
		print '<input type="radio" '.($objact->fk_pac == $obj_pac->id?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="'.$obj_pac->id.'">';
		print '</label>';
		print '<div class="col-sm-10">';
		print $obj_pac->nom;
		print '</div>';
		print '</div>';
		$k++;
	}
	// print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
	// print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
	// print '</td></tr>';
	//fk_prev
/*
	if ($user->admin || $user->rights->poa->act->crear)
	{
		$objpreven = new Poaprev($db);
		print '<div class="form-group">';
		print '<label class="control-label col-sm-2">'.$langs->trans('Preventive').'</label>';
		print '<div class="col-sm-10">';
		print $objpreven->select_poa_prev($fk_prev,'fk_prev','',100,1,$gestion,$idFather,'T');
		print '</div>';
		print '</div>';
	}
	*/
	// area
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Area').'</label>';

	//if (!$user->admin)
	//{
	$objarea->fetch((empty($objact->fk_area)?(empty($idFather)?$objstr->fk_area:$idFather):$object->fk_area));
	print '<div class="col-sm-10">';
	print $objarea->label;
	print '<input type="hidden" name="fk_area" value="'.$objarea->id.'">';
	print '</div>';
	//}
	//else
	//{
	//	print '<div class="col-sm-10">';
	//	print $objarea->select_area((empty($objact->fk_area)?(empty($idFather)?$objstr->fk_area:$idFather):$object->fk_area),'fk_area','',120,1);
	//	print '</div>';
	//}
	print '</div>';


	// gestion

	//print '<div class="form-group">';
	//print '<label class="control-label col-sm-2">'.$langs->trans('Gestion').'</label>';
	//print '<div class="col-sm-10">';
	print '<input type="hidden" value="'.(empty($objact->gestion)?$objpoa->gestion:$objact->gestion).'" name="gestion" maxlength="4">';
	//print '</div>';
	//print '</div>';
	// label
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Name').'</label>';
	print '<div class="col-sm-10">';
	print '<input class="form-control" type="text" value="'.(empty($objact->label)?$objpoa->label:$objact->label).'" name="label" maxlength="255">';
	print '</div>';
	print '</div>';

	// pseudonym
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Pseudonym').'</label>';
	print '<div class="col-sm-10">';
	print '<input class="form-control" type="text" value="'.(empty($objact->pseudonym)?$obpoa->pseudonym:$objact->pseudonym).'" name="pseudonym" size="120" maxlength="50">';
	print '</div>';
	print '</div>';

	//nro
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Nro').'</label>';
	print '<div class="col-sm-6">';
	if ($user->admin)
		print '<input class="form-control" type="text" value="'.(empty($objact->nro_activity)?$objact->fetch_next_nro((empty($gestion)?$objpoa->gestion:$gestion)):$objact->nro_activity).'" name="nro_activity" maxlength="12">';
	else
	{
		print $langs->trans('Automatic');
	}
	print '</div>';
	print '</div>';

	//priority
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Priority').'</label>';
	print '<div class="col-sm-6">';
	print '<input class="form-control" type="number" min="0" max="9" value="'.$objact->priority.'" name="priority"  maxlength="2">';
	print '</div>';
	print '</div>';

	//requirementtype
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Requirementtype').'</label>';
	print '<div class="col-sm-6">';
	print select_requirementtype($objact->code_requirement,'code_requirement','',1,0,'code');
	print '</div>';
	print '</div>';
	//date_preventive
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Date').'</label>';
	print '<div class="col-sm-8">';

    //convertimos la fecha
    $aDate = dol_getdate(dol_now());
    $date_activity = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
    print '          <div class="input-group date" id="divMiCalendario">
                      <input type="text" name="di_" id="dateacti0" class="form-control" value="'.$date_activity.'" readonly/>
                      <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>';
	print '</div>';
	print '</div>';

	//continuacion de preventivo gestiones anteriores
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Preventivemainlast').'</label>';
	print '<div class="col-sm-8">';
	print '<div class="col-xs-6">';
	print '<input class="form-control" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant"  maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
	print '</div>';
	print '<div class="col-xs-6">';
	print '<input class="form-control" type="text" value="'.$gestion_ant.'" name="gestion_ant" maxlength="4" placeholder="'.$langs->trans('Year').'">';
	print '</div>';
	print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
	print '</div>';
	print '</div>';
	//partida
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Partida').'</label>';
	print '<div class="col-sm-8">';
	print '<input class="form-control" type="text" value="'.(empty($objact->partida)?$objpoa->partida:$objact->partida).'" name="partida"  maxlength="12">';
	print '</div>';
	print '</div>';
	//monto
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Amount').'</label>';
	print '<div class="col-sm-8">';
	print '<input class="form-control" type="number" step="any" min="0" value="'.(empty($objact->amount)?$object->amount:$objact->amount).'" name="amount"  maxlength="12">';
	print '</div>';
	print '</div>';
	//respon
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Responsible').'</label>';
	print '<div class="col-sm-8">';
	$exclude = array();
	if (empty($objact->entity)) $objact->entity = $conf->entity;
	if ($user->rights->poa->prev->creart)
		print $form->select_dolusers((empty($objact->fk_user_create)?$user->id:$objact->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);
	else
	{
		if ($objuser->fetch($user->id))
			print $objuser->lastname.' '.$objuser->firstname;
		print '<input type="hidden" name="fk_user_create" value="'.$user->id.'">';
	}
	print '</div>';
	print '</div>';

	//print '</table>';

	print '<center><input type="submit" class="btn btn-primary" value="'.$langs->trans("Create").'"/></center>';

	print '</form>';
	print '<div class="tabsAction">';

	print '<a class="btn btn-default" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
	print '</div>';
}

//print '</div>';
//print '</div>';
//print '</div>';
print '</div>';

print '</div>';//modal-content
print '</div>';//modal-dialog
print '</div>';//modal-success">';
print '</div>';//poa-modal
print '</div>';//activity

?>
