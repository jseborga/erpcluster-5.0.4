<?php
//recibiendo valores
$idp = GETPOST('idp');
$idpp = GETPOST('idpp');
//addpreventive
$display ='none';
if (isset($modal) && $modal == 'fichepreventive')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#fichepreventive").modal("show");
	});
</script>';
}
//$display = 'block';
print '<div id="fichepreventive" class="modal modal-primary fade in" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760;" aria-hidden="false">';
print '<div class="poa-modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("Preventive").'</h4>';
print '</div>';


if ($objact->fk_prev <=0 )
{
	if ($user->rights->poa->prev->crear)
	{
		//armamos el script para que se ejecute por todas las lineas del pac
		if ($lLoop > 0)
		{
			//for ($k = 1; $k <= $lLoop; $k++)
			//{
			//	print "\n".'<script type="text/javascript" language="javascript">';
			//	print '$(document).ready(function () {';
			//	print '$("#selectfk_pac'.$k.'").change(function() {';
			//	print ' document.form_fiche.action.value="createedit";
			//	document.form_fiche.submit(); }); });';
			//	print '</script>'."\n";
			//}
		}
		//armamos para cuando se seleccione una actividad verifique si ya tiene creado la ejecucion (preventivo)
		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {';
		print '$("#selectfk_activity'.$k.'").change(function() {';
		print ' document.form_fiche.action.value="createedit_a";
		document.form_fiche.submit();});});';
		print '</script>'."\n";

		print '<form class="form-horizontal col-sm-12" name="form_fiche" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="modal" value="fichepreventive">';
		print '<input type="hidden" name="ida" value="'.$ida.'">';

		dol_htmloutput_mesg($mesg);

		print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

		//$res = $objact->fetch($fk_activity);
		$fk_activity = $objact->id;
		$fk_poa = $objact->fk_poa;
		if ($objact->fk_pac)
		{
			$objpac->fetch($objact->fk_pac);

		}
		$lLoop = count($objpac->array);

		// pac
		//listamos todos los pac que afecten al poa
		$k = 0;
		if ($objpac->id == $objact->fk_pac)
		{
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Select').' '.$langs->trans('Pac').'</label>';
			print '<div class="col-sm-8">';
			print $objpac->label;
			print '<input type="hidden" name="fk_pac" value="'.$objact->fk_pac.'">';
			print '</div>';
			print '</div>';
		}
		/*
		$fk_pac = (empty($object->fk_pac)?$objact->fk_pac:$object->fk_pac);
		foreach((array) $objpac->array AS $j => $obj_pac)
		{
			if ($fk_pac == $obj_pac->id)
			{
				print '<div class="form-group">';
				print '<div class="col-xs-offset-3 col-xs-9">';
				print '<label class="radio-inline">';
				print '<input id="optionsRadios1" type="radio" '.($fk_pac == $obj_pac->id?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="'.$obj_pac->id.'">';
				print $obj_pac->nom;
				print '</label>';
				print '</div>';
				print '</div>';
			}
			$k++;
		}
		*/
		//activity
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Activity').'</label>';
		print '<div class="col-xs-4 col-sm-8">';
		//if (!$user->admin)
		//{
		//	if($res>0)
		//	{
		print $objact->label;
		print '<input type="hidden" name="fk_activity" value="'.$fk_activity.'">';
		//	}
		//	else
		//		$mesg = '<div class="error">'.$langs->trans("Erroractivityisnull").'</div>';
		//}
		//else
		//{
		//	if ($fk_poa)
		//		print $objact->select_activity((empty($object->fk_activity)?$fk_activity:$object->fk_activity),'fk_activity','',120,1,0,1,''," AND fk_poa = ".$fk_poa);
		//	else
		//		print $langs->trans('Errorpoaisrequired');
		//}
		print '</div>';
		print '</div>';

		// area
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Area').'</label>';
		print '<div class="col-xs-4 col-sm-8">';
		//if (!$user->admin)
		//{
		$objarea->fetch($objact->fk_area);
		print $objarea->label;
		print '<input type="hidden" name="fk_area" value="'.$objact->fk_area.'">';
		//}
		//else
		//	print $objarea->select_area((empty($object->fk_area)?(!empty($objact->fk_area)?$objact->fk_area:$idFather):$object->fk_area),'fk_area','',120,1);
		print '</div>';
		print '</div>';

		// gestion
		//print '<div class="form-group">';
		//print '<label class="control-label col-xs-3">'.$langs->trans('Gestion').'</label>';
		//print '<div class="col-xs-8">';
		//print '<input class="form-control" id="gestion" type="number" max="'.date('Y').'" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" maxlength="4" requided>';
		//print '</div>';
		//print '</div>';
		// father
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Preventive top').'</label>';
		print '<div class="col-xs-4 col-sm-8">';
		print '<input class="form-control" type="text" value="'.$object->fk_father.'" name="fk_father" maxlength="4" placeholder="'.$langs->trans('Preventivemainact').'">';
		print '</div>';
		print '</div>';
		// label
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Name').'</label>';
		print '<div class="col-xs-8">';
		print '<input class="form-control" type="text" id="name" value="'.(empty($object->label)?$objact->label:$object->label).'" name="label" maxlength="255" required>';
		print '</div>';
		print '</div>';
		// pseudonym
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Pseudonym').'</label>';
		print '<div class="col-xs-8">';
		print '<input class="form-control" id="pseudonim" type="text" value="'.(empty($object->pseudonym)?$objact->pseudonym:$object->pseudonym).'" name="pseudonym" maxlength="50">';
		print '</div>';
		print '</div>';
		//nro
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Nro').'</label>';
		print '<div class="col-xs-4 col-sm-8">';
		print '<div class="col-xs-6">';
		print '<input class="form-control" id="nro_preventive" type="number" value="'.(empty($object->nro_preventive)?$objact->nro_activity:$object->nro_preventive).'" name="nro_preventive" maxlength="12">';
		print '</div>';
		print '<div class="col-xs-6">';
		print '<input class="form-control" type="number" max="'.$_SESSION['gestion'].'" value="'.(empty($objact->gestion)?$objpoa->gestion:$objact->gestion).'" name="gestion" maxlength="4" requided>';
		print '</div>';
		print '</div>';
		print '</div>';

		//priority
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Priority').'</label>';
		print '<div class="col-xs-4 col-sm-8">';
		print '<input class="form-control" type="number" max="20" value="'.(empty($object->priority)?$objact->priority:$object->priority).'" name="priority" maxlength="2">';
		print '</div>';
		print '</div>';
		//requirementtype
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Requirementtype').'</label>';
		print '<div class="col-xs-4 col-sm-8">';
		print select_requirementtype((empty($object->code_requirement)?$objact->code_requirement:$object->code_requirement),'code_requirement','',0,1,'code');
		print '</div>';
		print '</div>';
		//date_preventive
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Date').'</label>';
		print '<div class="col-xs-8">';

	    //convertimos la fecha
		$aDate = dol_getdate(dol_now());
		if (!empty($object->date_preventive))
			$aDate = dol_getdate($object->date_preventive);
		$date_ = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
		print '<div class="well well-sm col-sm-6">';
		print '<div class="input-group date" id="divMiCalendario">';
		print '<input type="text" name="di_" id="dateprev" class="form-control" value="'.$date_.'" readonly/>';
		print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
		print '</div>';
		print '</div>';

		//$formadd->select_dateadd((empty($object->date_assign)?dol_now():$object->date_assign),'di_','','','',"date",1,1);
		print '</div>';
		print '</div>';

		//continuacion de preventivo gestiones anteriores
		if ($objact->nro_preventive_ant)
		{
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Preventivemainlast').'</label>';
			print '<div class="col-xs-8">';
			print $objact->nro_preventive_ant.'/'.$objact->gestion_ant;
			print '<input type="hidden" value="'.$objact->nro_preventive_ant.'" name="nro_preventive_ant" >';
			print '<input type="hidden" value="'.$objact->gestion_ant.'" name="gestion_ant">';
			print '</div>';
			print '</div>';
		}
		//respon
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Responsible').'</label>';
		$exclude = array();
		if (empty($object->entity)) $object->entity = $conf->entity;
		print '<div class="col-xs-8">';
		if ($user->rights->poa->prev->creart)
			print $formadd->select_use((empty($object->fk_user_create)?$objact->fk_user_create:$object->fk_user_create), 'fk_user_create', '', 1);
			//      print $form->select_dolusers((empty($object->fk_user_create)?$user->id:$object->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);
		else
		{
			if ($objuser->fetch($user->id))
				print $objuser->lastname.' '.$objuser->firstname;
			print '<input type="hidden" name="fk_user_create" value="'.$user->id.'">';
		}
		print '</div>';
		print '</div>';

		print '</div>';

		print '<div class="modal-footer">';
		print '<center><br><input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Create").'"></center>';
		print '</div>';

		print '</form>';
	}
}
else
{
	if ($action == 'edit')
	{
		print '<form class="form-horizontal col-sm-12" name="form_fiche" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="ida" value="'.$ida.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		//print '<input type="hidden" name="fk_poa" value="'.$fk_poa.'">';
		print '<input type="hidden" name="modal" value="fichepreventive">';
		dol_htmloutput_mesg($mesg);

		print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

		//$res = $objact->fetch($fk_activity);
		$fk_activity = $objact->id;
		$fk_poa = $objact->fk_poa;
		$objpac->fetch_poa($fk_poa);
		$lLoop = count($objpac->array);



		// pac
		//listamos todos los pac que afecten al poa
		$k = 0;
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Select').' '.$langs->trans('Pac').'</label>';
		if (count($objpac->array) > 0)
		{
			print '<div class="col-xs-2">';
			print '<label class="radio-inline">';
			print '<input id="optionsRadios1" type="radio" '.(empty($object->fk_pac)?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="0">';
			print $langs->trans('Noselection');
			print '</label>';
			print '</div>';
			$k++;
		}
		print '</div>';
		$fk_pac = (empty($object->fk_pac)?$objact->fk_pac:$object->fk_pac);
		foreach((array) $objpac->array AS $j => $obj_pac)
		{
			if ($fk_pac == $obj_pac->id)
			{
				print '<div class="form-group">';
				print '<div class="col-xs-offset-3 col-xs-9">';
				print '<label class="radio-inline">';
				print '<input id="optionsRadios1" type="radio" '.($fk_pac == $obj_pac->id?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="'.$obj_pac->id.'">';
				print $obj_pac->nom;
				print '</label>';
				print '</div>';
				print '</div>';
			}
			$k++;
		}

		//activity
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Activity').'</label>';
		print '<div class="col-xs-8">';
		if (!$user->admin)
		{
			if($res>0)
			{
				print $objact->label;
				print '<input type="hidden" name="fk_activity" value="'.$fk_activity.'"';
			}
			else
				$mesg = '<div class="error">'.$langs->trans("Erroractivityisnull").'</div>';
		}
		else
		{
			if ($fk_poa)
				print $objact->select_activity((empty($object->fk_activity)?$fk_activity:$object->fk_activity),'fk_activity','',120,1,0,1,''," AND fk_poa = ".$fk_poa);
			else
				print $langs->trans('Errorpoaisrequired');
		}
		print '</div>';
		print '</div>';
			// area
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Area').'</label>';
		print '<div class="col-xs-8">';
		if (!$user->admin)
		{
			$objarea->fetch($idFather);
			print $objarea->label;
			print '<input type="hidden" name="fk_area" value="'.$idFather.'"';
		}
		else
			print $objarea->select_area((empty($object->fk_area)?(!empty($objact->fk_area)?$objact->fk_area:$idFather):$object->fk_area),'fk_area','',120,1);
		print '</div>';
		print '</div>';

		// gestion
		//print '<div class="form-group">';
		//print '<label class="control-label col-xs-3">'.$langs->trans('Gestion').'</label>';
		//print '<div class="col-xs-8">';
		//print '<input class="form-control" id="gestion" type="number" max="'.date('Y').'" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" maxlength="4" requided>';
		//print '</div>';
		//print '</div>';
		// father
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Preventive top').'</label>';
		print '<div class="col-xs-8">';
		print '<input class="form-control" id="fk_father" type="text" value="'.$object->fk_father.'" name="fk_father" maxlength="4" placeholder="'.$langs->trans('Preventivemain').'">';
		print '</div>';
		print '</div>';
		// label
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Name').'</label>';
		print '<div class="col-xs-8">';
		print '<input class="form-control" type="text" id="name" value="'.(empty($object->label)?$objact->label:$object->label).'" name="label" maxlength="255" required>';
		print '</div>';
		print '</div>';
		// pseudonym
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Pseudonym').'</label>';
		print '<div class="col-xs-8">';
		print '<input class="form-control" id="pseudonim" type="text" value="'.(empty($object->pseudonym)?$objact->pseudonym:$object->pseudonym).'" name="pseudonym" maxlength="50">';
		print '</div>';
		print '</div>';
		//nro
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Nro').'</label>';
		print '<div class="col-xs-8">';
		print '<div class="col-xs-4">';
		print '<input class="form-control" id="nro_preventive" type="number" value="'.(empty($object->nro_preventive)?$objact->nro_activity:$object->nro_preventive).'" name="nro_preventive" maxlength="12">';
		print '</div>';
		print '<div class="col-xs-4">';
		print '<input class="form-control" id="gestion" type="number" max="'.date('Y').'" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" maxlength="4" requided>';
		print '</div>';
		print '</div>';
		print '</div>';

		//priority
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Priority').'</label>';
		print '<div class="col-xs-8">';
		print '<input class="form-control" id="priority" type="number" max="20" value="'.(empty($object->priority)?$objact->priority:$object->priority).'" name="priority" maxlength="2">';
		print '</div>';
		print '</div>';
		//requirementtype
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Requirementtype').'</label>';
		print '<div class="col-xs-8">';
		print select_requirementtype((empty($object->code_requirement)?$objact->code_requirement:$object->code_requirement),'code_requirement','',1,0,'code');
		print '</div>';
		print '</div>';
		//date_preventive
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Date').'</label>';
		print '<div class="col-xs-8">';

	    //convertimos la fecha
		$aDate = dol_getdate(dol_now());
		if (!empty($object->date_preventive))
			$aDate = dol_getdate($object->date_preventive);
		$date_ = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
		print '<div class="well well-sm col-sm-6">';
		print '<div class="input-group date" id="divMiCalendario">
		<input type="text" name="di_" id="dateprev" class="form-control" value="'.$date_.'" readonly/>
		<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
		print '</div>';
		print '</div>';

		//$formadd->select_dateadd((empty($object->date_assign)?dol_now():$object->date_assign),'di_','','','',"date",1,1);
		print '</div>';
		print '</div>';
		//continuacion de preventivo gestiones anteriores
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Preventivemainlast').'</label>';
		print '<div class="col-xs-8">';
		print '<div class="col-xs-3">';
		print '<input class="form-control" id="nro_preventive_ant" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant" maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
		print '</div>';
		print '<div class="col-xs-3">';
		print '<input class="form-control" id="gestion_ant" type="text" value="'.$gestion_ant.'" name="gestion_ant" maxlength="4" placeholder="'.$langs->trans('Year').'">';
		print '</div>';
		print '<div class="col-xs-3">';
		print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
		print '</div>';
		print '</div>';
		print '</div>';

		//respon
		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Responsible').'</label>';
		$exclude = array();
		if (empty($object->entity)) $object->entity = $conf->entity;
		print '<div class="col-xs-8">';
		if ($user->rights->poa->prev->creart)
			print $formadd->select_use((empty($object->fk_user_create)?$objact->fk_user_create:$object->fk_user_create), 'fk_user_create', '', 1);
		else
		{
			if ($objuser->fetch($user->id))
				print $objuser->lastname.' '.$objuser->firstname;
			print '<input type="hidden" name="fk_user_create" value="'.$user->id.'">';
		}
		print '</div>';
		print '</div>';

		print '</div>';

		print '<div class="modal-footer">';
		print '<center><br><input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Update").'"></center>';
		print '</div>';
		print '</form>';
	}
	else
	{
		$k = 0;
		print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

		print '<dl class="dl-horizontal">';
		print '<dt>'.$langs->trans('Pac').'</dt>';
		print '<dd>';
		$respac = $objpac->fetch($object->fk_pac);
		if ($respac >0)
		{
			print $objpac->label;
		}
		print '</dd>';

		//activity
		//print '<dt>'.$langs->trans('Activity').'</dt>';
		//print '<dd>'.$objact->label.'</dd>';

		// area
		print '<dt>'.$langs->trans('Area').'</dt>';
		print '<dd>';
		$objarea->fetch($object->fk_area);
		print $objarea->label;
		print '</dd>';

		// father
		if ($object->fk_father)
		{
			print '<dt>'.$langs->trans('Preventive top').'</dt>';
			print '<dd>';
			$objectsup = new Poaprev($db);
			$objectsup->fetch($object->fk_father);
			if ($objectsup->id == $object->fk_father)
				print $objectsup->nro_preventive.'-'.$objectsup->gestion;
			else
				print '&nbsp;';
			print '</dd>';
		}
		// label
		print '<dt>'.$langs->trans('Name').'</dt>';
		print '<dd>';
		print $object->label;
		print '</dd>';

		// pseudonym
		print '<dt>'.$langs->trans('Pseudonym').'</dt>';
		print '<dd>';
		print $object->pseudonym;
		print '</dd>';

		//nro
		print '<dt>'.$langs->trans('Nro').'</dt>';
		print '<dd>';
		print $object->nro_preventive.'/'.$object->gestion;
		print '</dd>';

		//priority
		if ($object->priority)
		{
			print '<dt>'.$langs->trans('Priority').'</dt>';
			print '<dd>';
			print $object->priority;
			print '</dd>';
		}
		//requirementtype
		print '<dt>'.$langs->trans('Requirementtype').'</dt>';
		print '<dd>';
		print select_requirementtype($object->code_requirement,'code_requirement','',0,1,'code');
		print '</dd>';

		//date_preventive
		print '<dt>'.$langs->trans('Date').'</dt>';
		print '<dd>';
		print dol_print_date($object->date_preventive,'day');
		print '</dd>';

		//continuacion de preventivo gestiones anteriores
		if ($object->nro_preventive_ant)
		{
			print '<dt>'.$langs->trans('Preventivemainlast').'</dt>';
			print '<dd>';
			print $object->nro_preventive_ant.'-'.$object->gestion_ant;
			print '</dd>';
		}
		//respon
		print '<dt>'.$langs->trans('Responsible').'</dt>';
		print '<dd>';
		$objuser->fetch($object->fk_user_create);
		if ($objuser->id == $object->fk_user_create)
			print $objuser->lastname.' '.$objuser->firstname;
		else
			print '&nbsp;';
		print '</dd>';
		print '</dl>';

		print '</div>';
			//agregamos los botones del preventivo
		/* ********************************* */
		/*                                   */
		/* Barre d'action                    */
		/*                                   */
		/* ********************************* */

		print '<div class="modal-footer">';

		print '<a class="btn btn-default btn-flat" href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'">'.$langs->trans("Return").'</a>';
		if (empty($action))
		{
				 // if ($user->rights->poa->prev->crear)
				 // 	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Createnew").'</a>';
				 // else
				 // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

				 //aumentar la verificacion del estadod
			if ($user->admin ||
				($user->rights->poa->prev->mod && $object->fk_user_create == $user->id))
				print '<a class="btn btn-primary btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=fichepreventive&action=edit&ida='.$ida.'&id='.$object->id.'">'.$langs->trans("Modify").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

			if ($user->rights->poa->prev->del && $object->statut == 0)
				print '<a class="btn btn-danger btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=fichepreventive&action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		 		// if ($user->rights->poa->prev->val && $object->statut == 0 && $lValidate)
		 		// 	print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
		 		// else
		 		// 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";

			if ($user->rights->poa->prev->nul && $object->statut > 0 && $objact->statut < 9)
				print '<a class="btn btn-danger btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=fichepreventive&action=anulate&ida='.$ida.'&id='.$object->id.'">'.$langs->trans("Cancel").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";

				 // if ($user->rights->poa->prev->nul && $object->statut > 0)
				 // 	print "<a class=\"butAction\" href=\"fiche.php?action=anulate&id=".$object->id."\">".$langs->trans("Cancel")."</a>";
				 // else
				 // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";
				 //disminucion del preventivo con autorizacion
			if ($user->rights->poa->prev->dis && $object->statut > 0)
				print '<a class="btn btn-default btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=fichepreventive&action=reduc&ida='.$ida.'&id='.$object->id.'&modal=fichepreventive">'.$langs->trans("Add reduction").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Add modify")."</a>";
		}
		elseif($action=='eproduct')
		{
		 // if ($object->statut == 0)
		 // 	{
		 // 	  if ($user->rights->poa->prev->val && $lValidate)
		 // 	    print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
		 // 	  else
		 // 	    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
		 // 	}
		}

		print "</div>";

	}
	//mostramos registro de reducciones
	if ($action == 'reduc')
	{
		print '<br>';
		print '<h3>';
		print $langs->trans('Nuevo registro de disminucion preventivo');
		print '</h3>';

		 //registro nuevo
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addmodify">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="modal" value="fichepreventive">';
		print '<input type="hidden" name="ida" value="'.$ida.'">';

		print '<table class="table table-striped">';
		print '<tr>';
		print '<th>'.$langs->trans("Modify prev",$cursorline).'</th>';
		print '<th>'.$langs->trans("Structure",$cursorline).'</th>';
		print '<th align="center">'.$langs->trans("Partida").'</th>';
		print '<th align="center">'.$langs->trans("Amount").'</th>';
		print '</tr>';

		 //listado
		$objpre->getlist($object->id,'S');
		if (count($objpre->array) > 0)
		{
			$var = true;
			foreach ($objpre->array AS $j => $objpartidapre)
			{
				$var=!$var;
				print "<tr>";
			 //poa
				print '<td>';
				$objpoa->fetch($objpartidapre->fk_poa);
				if ($objpoa->id == $objpartidapre->fk_poa)
					print $objpoa->label;
				else
					print '&nbsp;';
				print '</td>';
			 //structure
				print '<td>';
				$objstr->fetch($objpartidapre->fk_structure);
				if ($objstr->id == $objpartidapre->fk_structure)
					print $objstr->sigla;
				else
					print '&nbsp;';
				print '</td>';

			 // partida
				print '<td>';
				print $objpartidapre->partida;
				print '</td>';

			 // amount
				print '<td align="right">';
				if ($action == 'reduc')
				{
					print '<input type="number" min="0" max="'.
					$aPrev[$objpartidapre->id].'" step="any" name="amount['.
					$objpartidapre->id.']" value="0"';
				}
				else
					print price(price2num($objpartidapre->amount,'MT'));
				print '</td>';

				print '</tr>';
			}
		}
		print "</table>";
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
		print '</form>';
	}

		//mostramos las partidas que componen el preventivo
	if ($action != 'edit' && $action != 'reduc')
	{
		print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

		print '<div class="col-md-12">';
		//print '<div class="callout callout-success">';
		//print '<div class="box-header with-border">';
		//print '<i class="fa fa-text-width"></i>';
		//print '<h3 class="box-title">'.$langs->trans('Partidas').'</h3>';
		//print '</div>';
		print '<div class="table-responsive">';

		print '<table data-toggle="table" data-url="data1.json" data-height="299" class="table table-condensed price-table">';
		print '<thead>';
		print '<tr>';
		print '<th>'.$langs->trans("Meta",$cursorline).'</th>';
		print '<th>'.$langs->trans("Structure",$cursorline).'</th>';
		print '<th data-field="Partida" class="text-center">'.$langs->trans("Partida").'</th>';
		print '<th data-field="Amount" class="text-right">'.$langs->trans("Amount").'</th>';
		print '<th data-field="Action" class="text-right">'.$langs->trans("Action").'</th>';
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		 	//registro nuevo
		if ($object->statut == 0  && ($action!='editpartida' && $action!='eproduct' && $action != 'editproduct'))
		{
			$objpre->initAsSpecimen();
			include_once DOL_DOCUMENT_ROOT.'/poa/execution/tpl/form.tpl.php';
		}

		 	//definimos array para saldos
		$aPrev = array();
		 	//para habilitar boton de validacion
		$lValidate = true;
		$aValidate = array();
		$aSumPartida = array();
		 	//listado partidas
		$sumaPartida = 0;
		$obj = new Poapartidapre($db);
		$obj->getlist($object->id);
		if (count($obj->array) > 0)
		{
			$var = true;
			foreach ($obj->array AS $j => $objpartidapre)
			{
				$sumaPartida+=$objpartidapre->amount;
			 	//obtenemos la suma de la partida en insumos producto
				$sumaParcial = $objppd->getsum($objpartidapre->id,0);
				if ($sumaParcial != $objpartidapre->amount || empty($sumaParcial))
				{
					$lValidate = false;
					$aValidate[$objpartidapre->id] = false;
				}
				else
					$aValidate[$objpartidapre->id] = true;

				if ($action == 'editpartida' && $objpartidapre->id == $idp)
				{
					//buscamos
					$objpre->fetch($idp);
					include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/form.tpl.php';
				}
				else
				{
					$var=!$var;
					print "<tr>";
				 	//poa
					print '<td>';
					$objpoa->fetch($objpartidapre->fk_poa);
					if ($objpoa->id == $objpartidapre->fk_poa)
						print $objpoa->label;
					else
						print '&nbsp;';
					print '</td>';
			 		//structure
					print '<td>';
					$objstr->fetch($objpartidapre->fk_structure);
					if ($objstr->id == $objpartidapre->fk_structure)
						print $objstr->sigla;
					else
						print '&nbsp;';
					print '</td>';
			 		// partida
					print '<td class="text-center">';
					print $objpartidapre->partida;
					print '</td>';
			 		// amount
					print '<td class="text-right">';
					print price(price2num($objpartidapre->amount,'MT'));
					print '</td>';
			 		//agregamos al array de saldos
					$aPrev[$objpartidapre->id] += $objpartidapre->amount;
					print '<td class="text-right" width="100px">';
					print '<div class="col-md-1">';
					print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&id='.$id.'&idp='.$objpartidapre->id.'&action=eproduct&modal=fichepreventive" alt="'.$langs->trans('Product').'">'.img_picto($langs->trans('Product'),DOL_URL_ROOT.'/poa/img/product.png','',1).'</a>';
					print '</div>';
					if ($object->statut == 0 && $user->rights->poa->prev->delit)
					{
						print '<div class="col-md-1">';
						print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idp='.$objpartidapre->id.'&action=delpartida" alt="'.$langs->trans('Delete').'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
						print '</div>';
					}
					if ($user->admin && $user->rights->poa->prev->crear)
					{
						print '<div class="col-md-1">';
						print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idp='.$objpartidapre->id.'&action=editpartida&modal=fichepreventive" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit').'</a>';
						print '</div>';
					}
					print '</td>';
					print '</tr>';
					//verificamos los productos de la partida
				 	//listando
					$sumaProd = 0;
					$objppd->getlist($objpartidapre->id,'N');
					foreach((array) $objppd->array AS $k => $objprevpro)
					{
						$sumaProd+= $objprevpro->amount_base;
					}
					if ($_GET['idp'] == $objpartidapre->id && ($action == 'eproduct' || $action == 'editproduct'))
					{
						$lView = true;
						if ($sumaProd >= $objpartidapre->amount) $lView = false;
				 		//editamos el registro de productos
						if (($user->admin && $lView) || ($action != 'editproduct' && $object->statut == 0 && !$aValidate[$objpartidapre->id] && $lView))
						{
							//registro nuevo
							$objprevdetclon = $objppd;
							include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/addproduct.tpl.php';
						}
						$objppd->getlist($objpartidapre->id,'N');
						foreach((array) $objppd->array AS $k => $objprevpro)
						{
							if ($action == 'editproduct' && $objprevpro->id == $_GET['idppp'])
							{
								$objprevdetclon = $objprevpro;
								include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/addproduct.tpl.php';
							}
							else
							{
								$var=!$var;
								print '<tr class="color-product">';
					 			// producto
								print '<td colspan="2">';
								print '&nbsp;&nbsp;'.$objprevpro->detail;
								print '</td>';

					 			// Quant
								print '<td align="right">';
								print price2num($objprevpro->quant,'MT');
								print '</td>';

					 			// amount base
								print '<td align="right">';
								print price(price2num($objprevpro->amount_base,'MT'));
								print '</td>';

								print '<td align="right">';
								if ($user->admin || $object->statut == 0)
								{
									if ($user->admin || $user->id == $object->fk_user_create)
									{
										print '<div class="col-md-1">';
										print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&idp='.$objpartidapre->id.'&idppp='.$objprevpro->id.'&action=editproduct'.'&modal=fichepreventive">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
										print '</div>';
									}
									print '<div class="col-md-1">';
									print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&idppp='.$objprevpro->id.'&action=confirm_delete_product&modal=fichepreventive'.'">'.img_picto($langs->trans('Delete'),'delete.png').'</a>';
									print '</div>';
								}
								else
									print '&nbsp;';
								print '</td>';
								print '</tr>';
							}

						}
				 		//totales
						print '<tr class="color_total">';
						print '<td colspan="3"></td>';
						print '<td align="right">';
						print price(price2num($sumaProd,'MT'));
						print '</td>';
						print '<td></td>';
						print '</tr>';
					}
				}
			}
		}
		else
			$lValidate = false;
		print '</tbody>';
		print "</table>";
			//validando para sacar boton
		$lClose = true;
		foreach ((array) $aValidate AS $j =>$value)
		{
			if ($value == false) $lClose = false;
		}
		if ($lClose)
		{
			if ($object->statut == 0)
			{
				if ($lValidate)
				{
					print '<div class="col-xs-3">';
					print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
					print '<input type="hidden" name="action" value="confirm_validate_prev">';
					print '<input type="hidden" name="modal" value="fichepreventive">';
					print '<input type="hidden" name="ida" value="'.$ida.'">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';
					print '<button class="btn btn-outline">'.$langs->trans('Validate').'</button>';
					print '</form>';
					print '</div>';
				}
			}
			else
			{
				//print '<div class="col-xs-3">';
				//print '<a class="btn btn-primary" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&action=editmod&modal=fichepreventive" alt="'.$langs->trans('Modificaciones al preventivo').'">'.$langs->trans('Modifypreventive').'</a>';
				//print '</div>';
			}
		}
		if ($object->statut > 0)
		{
		 		//nuevo para modificaciones preventivo
			print '<table class="table">';
			print '<thead>';
			print '<tr>';
			print '<th>'.$langs->trans("Modify prev",$cursorline).'</th>';
			print '<th>'.$langs->trans("Structure",$cursorline).'</th>';
			print '<th class="text-center">'.$langs->trans("Partida").'</th>';
			print '<th class="text-right">'.$langs->trans("Amount").'</th>';
			print '<th class="text-right">'.$langs->trans("Action").'</th>';
			print '</tr>';
			print '</thead>';
			print '<tbody>';
		 	//listado
			$objpre->getlist($object->id,'N');
			if (count($objpre->array) > 0)
			{
				$var = true;
				foreach ($objpre->array AS $j => $objpartidapre)
				{
					if ($action == 'editmod' && $objpartidapre->id == $idpp)
					{
				 		//registro a modificar
						print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="addmodifyr">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';
						print '<input type="hidden" name="ida" value="'.$objact->id.'">';

						print '<input type="hidden" name="idpp" value="'.$objpartidapre->id.'">';
						print '<input type="hidden" name="modal" value="fichepreventive">';

						$var=!$var;
						print "<tr>";
				 		//poa
						print '<td>';
						$objpoa->fetch($objpartidapre->fk_poa);
						if ($objpoa->id == $objpartidapre->fk_poa)
							print $objpoa->label;
						else
							print '&nbsp;';
						print '</td>';
				 		//structure
						print '<td>';
						$objstr->fetch($objpartidapre->fk_structure);
						if ($objstr->id == $objpartidapre->fk_structure)
							print $objstr->sigla;
						else
							print '&nbsp;';
						print '</td>';

				 		// partida
						print '<td class="text-center">';
						print $objpartidapre->partida;
						print '</td>';

				 		// amount
						print '<td class="text-right">';
						$amount = $objpartidapre->amount*-1;
						print '<input type="number" class="form-control" min="0" step="any" name="amount" value="'.$amount.'">';
						print '</td>';
						print '<td class="text-right">';
						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
						print '</td>';
						print '</tr>';
						print '</form>';
					}
					else
					{
						$var=!$var;
						print "<tr>";
				 		//poa
						print '<td>';
						$objpoa->fetch($objpartidapre->fk_poa);
						if ($objpoa->id == $objpartidapre->fk_poa)
							print $objpoa->label;
						else
							print '&nbsp;';
						print '</td>';
				 		//structure
						print '<td>';
						$objstr->fetch($objpartidapre->fk_structure);
						if ($objstr->id == $objpartidapre->fk_structure)
							print $objstr->sigla;
						else
							print '&nbsp;';
						print '</td>';

				 		// partida
						print '<td class="text-center">';
						print $objpartidapre->partida;
						print '</td>';

				 		// amount
						print '<td class="text-right">';
						print price(price2num($objpartidapre->amount,'MT'));
						print '</td>';
				 		//restamos las disminuciones
						$aPrev[$objpartidapre->id] += $objpartidapre->amount;
						print '<td class="text-right" style="width:100px;">';
						if ($user->admin || $object->statut == 1)
						{
							print '<div class="col-md-1">';
							print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idpp='.$objpartidapre->id.'&action=editmod&modal=fichepreventive" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
							print '</div>';
							print '<div class="col-md-1">';
							print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idpp='.$objpartidapre->id.'&action=deletemod&modal=fichepreventive" alt="'.$langs->trans('Delete').'">'.img_picto($langs->trans('Delete'),'delete.png').'</a>';
							print '</div>';
						}
						print '</td>';
						print '</tr>';
					}
				}
			}
			print '</tbody>';
			print "</table>";
		}
		print '</div>';
		//table-responsive
		//print '</div>';//callout
		print '</div>';
		//col-md-12
		print '</div>';
	}
	//print '</div>';//modal-body
}
print '</div>'; //modal-content
print '</div>'; //modal-dialog
print '</div>'; //modal modal-source
print '</div>'; //modal fade
?>