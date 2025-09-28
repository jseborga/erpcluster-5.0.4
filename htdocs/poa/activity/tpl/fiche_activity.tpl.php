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

if (empty($_SESSION['tabs']))
	$_SESSION['tabs']='crono';
if (isset($_GET['tabs']) || isset($_POST['tabs']))
	$_SESSION['tabs']=GETPOST('tabs');

$tabs = $_SESSION['tabs'];

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

// $aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
// $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("Activity"),$help_url,'','','',$aArrjs,$aArrcss);

$display ='none';
if (isset($modal) && $modal == 'ficheactivity')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#ficheactivity").modal("show");
	});
</script>';
}

print '<div id="ficheactivity'.$tagmeta.'" class="modal modal-info" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';

print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';


print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("Activity").': '.$objact->label.'</h4>';
print '</div>';


//filtro
$idTag1 = 1;
$idTag2 = 2;

//cuerpo

if ($action == 'create' && $user->rights->poa->act->crear)
{
	$objpoa->fetch($fk_poa);
	$objpac->fetch_poa($fk_poa);
	$lLoop = count($objpac->array);
	print_fiche_titre($langs->trans("Newactivity"));
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
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

	dol_htmloutput_mesg($mesg);

	print '<div class="modal-body" style="background:#fff; color:#000; !important">';

	// pac
	//listamos todos los pac que afecten al poa
	$k = 0;

	if (count($objpac->array) > 0)
	{
		print '<div class="container">';
		print '<table class="table">';
		print '<thead>';
		print '<tr>';
		print_liste_field_titre($langs->trans("Select"),"", "","","","");
		print_liste_field_titre($langs->trans("Pac"),"", "","","","");
		print '</tr>';
		print '</thead>';
		print '<tbody>';
	//registro vacio
		print '<tr><td align="center">';
		print '<input type="radio" '.(empty($object->fk_pac)?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="0">';
		print '</td>';
		print '<td colspan="2">';
		print $langs->trans('Noselection');
		print '</td></tr>';
		$k++;
		foreach((array) $objpac->array AS $j => $obj_pac)
		{
			print '<tr><td align="center">';
			print '<input type="radio" '.($object->fk_pac == $obj_pac->id?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="'.$obj_pac->id.'">';
			print '</td>';
			print '<td colspan="2">';
			print $obj_pac->nom;
			print '</td></tr>';
			$k++;
		}
		print '</table>';
	}

	// print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
	// print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
	// print '</td></tr>';

	//fk_prev
	if ($user->admin || $user->rights->poa->act->crear)
	{
		print '<div class="form-group">';
		print '<label class="control-label col-sm-2">'.$langs->trans('Preventive').'</label>';
		print '<div class="col-sm-10">';
		print $object->select_poa_prev($object->fk_prev,'fk_prev','',100,1,$gestion,$idFather,'T');
		print '</div>';
		print '</div>';
	}
	// area
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Area').'</label>';
	if (!$user->admin)
	{
		$objarea->fetch($idFather);
		print '<div class="col-sm-10">';
		print $objarea->label;
		print '<input type="hidden" name="fk_area" value="'.$idFather.'"';
		print '</div>';
	}
	else
	{
		print '<div class="col-sm-10">';
		print $objarea->select_area((empty($object->fk_area)?$idFather:$object->fk_area),'fk_area','',120,1);
		print '</div>';
	}
	print '</div>';

	// gestion
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Gestion').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="6" maxlength="4">';
	print '</div>';
	print '</div>';
	// label
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Name').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="label500" type="text" value="'.(empty($object->label)?$objpoa->label:$object->label).'" name="label" size="120" maxlength="255">';
	print '</div>';
	print '</div>';

	// pseudonym
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Pseudonym').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="pseudonym500" type="text" value="'.(empty($object->pseudonym)?$objpoa->pseudonym:$object->pseudonym).'" name="pseudonym" size="120" maxlength="50">';
	print '</div>';
	print '</div>';

	//nro
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Nro').'</label>';
	print '<div class="col-sm-10">';
	if ($user->admin)
		print '<input id="nro_activity" type="text" value="'.(empty($object->nro_activity)?$object->fetch_next_nro($gestion):$object->nro_activity).'" name="nro_activity" size="15" maxlength="12">';
	else
	{
		print $langs->trans('Automatic');
	}
	print '</div>';
	print '</div>';

	//priority
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Priority').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="priority" type="text" value="'.$object->priority.'" name="priority" size="5" maxlength="2">';
	print '</div>';
	print '</div>';

	//requirementtype
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Requirementtype').'</label>';
	print '<div class="col-sm-10">';
	print select_requirementtype($object->code_requirement,'code_requirement','',1,0,'code');
	print '</div>';
	print '</div>';
	//date_preventive
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Date').'</label>';
	print '<div class="col-sm-10">';
	$form->select_date((empty($object->date_assign)?dol_now():$object->date_assign),'di_','','','',"date",1,1);
	print '</div>';
	print '</div>';
	//continuacion de preventivo gestiones anteriores
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Preventivemainlast').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="nro_preventive_ant" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant" size="8" maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
	print '<input id="gestion_ant" type="text" value="'.$gestion_ant.'" name="gestion_ant" size="4" maxlength="4" placeholder="'.$langs->trans('Year').'">';
	print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
	print '</div>';
	print '</div>';
	//partida
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Partida').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="partida" type="text" value="'.(empty($object->partida)?$objpoa->partida:$object->partida).'" name="partida" size="10" maxlength="12">';
	print '</div>';
	print '</div>';
	//monto
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Amount').'</label>';
	print '<div class="col-sm-10">';
	print '<input id="amount" type="number" step="any" min="0" value="'.(empty($object->amount)?$objpoa->amount:$object->amount).'" name="amount" size="10" maxlength="12">';
	print '</div>';
	print '</div>';
	//respon
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Responsible').'</label>';
	print '<div class="col-sm-10">';
	$exclude = array();
	if (empty($object->entity)) $object->entity = $conf->entity;
	if ($user->rights->poa->prev->creart)
		print $form->select_dolusers((empty($object->fk_user_create)?$user->id:$object->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);
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
	print '<center><button type="button" class="btn btn-primary">'.$langs->trans("Create").'</button></center>';
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';

	print '</div>';
	print '</form>';
}
else
{
	if ($ida)
	{
		//dol_htmloutput_mesg($mesg);

		$result = $objact->fetch($ida);
		if ($result < 0) dol_print_error($db);
		//obtenemos el proceso del preventivo
		$aProcess = getlist_process($objact->fk_prev);
		$fk_prev_pri = $objact->fk_prev;
		$fk_proc_pri = 0;
		//verificamos si tiene un preventivo principal
		if (count($aProcess['pri'])>0)
		{
			foreach ((array) $aProcess['pri'] AS $fkProcess => $fk_prev_)
			{
				$fk_proc_pri = $fkProcess;
				$fk_prev_pri = $fk_prev_;
			}
			//recuperamos el proceso
			$lProcess = false;
			$fk_type_con = 0;
			if ($fk_proc_pri)
			{
				$lProcess = true;
				$objproc->fetch($fk_proc_pri);
				$fk_type_con = $objproc->fk_type_con;
			}
		}
		/*
		* Affichage fiche
		*/
		if ($action <> 'edit' && $action <> 're-edit')
		{
			//$head = fabrication_prepare_head($object);
			//dol_fiche_head($head, 'card', $langs->trans("Activity"), 0, 'mant');

			print '<div class="modal-body" style="background:#fff; color:#000; !important">';

			print '<dl class="dl-horizontal">';
			//			 print '<table class="border" width="100%">';

			// $linkback = '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php">'.$langs->trans("BackToList").'</a>';

			// pac
			print '<dt>'.$langs->trans('PAC').'</dt>';
			print '<dd>';
			$objpac->fetch($objact->fk_pac);
			if ($objpac->id == $objact->fk_pac) print $objpac->nom;
			else print '&nbsp;NOT '.$objact->fk_pac;
			print '</dd>';

			//fk_prev
			print '<dt>'.$langs->trans('Preventive').'</dt><dd>';
			if ($object->fetch($objact->fk_prev)>0) print $object->nro_preventive.' '.$object->label;
			else print '&nbsp;';
			print '</dd>';

			// area
			print '<dt>'.$langs->trans('Area').'</dt><dd>';
			$objarea->fetch($objact->fk_area);
			if ($objarea->id == $objact->fk_area) print $objarea->label;
			else print '&nbsp;';
			print '</dd>';

			// gestion
			print '<dt>'.$langs->trans('Gestion').'</dt><dd>';
			print $objact->gestion;
			print '</dd>';

			// label
			print '<dt>'.$langs->trans('Name').'</dt><dd>';
			print $objact->label;
			print '</dd>';

			// pseudonym
			print '<dt>'.$langs->trans('Pseudonym').'</dt><dd>';
			print $objact->pseudonym;
			print '</dd>';

			//nro
			print '<dt>'.$langs->trans('Nro').'</dt><dd>';
			print $objact->nro_activity;
			print '</dd>';

			//priority
			print '<dt>'.$langs->trans('Priority').'</dt><dd>';
			print $objact->priority;
			print '</dd>';

			//requirementtype
			print '<dt>'.$langs->trans('Requirementtype').'</dt><dd>';
			print select_requirementtype($objact->code_requirement,'code_requirement','',0,1,'code');
			print '</dd>';

			//date_activity
			print '<dt>'.$langs->trans('Date').'</dt><dd>';
			print dol_print_date($objact->date_activity,"day");
			print '</dd>';

			//partida
			print '<dt>'.$langs->trans('Partida').'</dt><dd>';
			print $objact->partida;
			print '</dd>';

			//amount
			print '<dt>'.$langs->trans('Amount').'</dt><dd>';
			print price(price2num($objact->amount,"MT"));
			print '</dd>';

			//respon
			print '<dt>'.$langs->trans('Responsible').'</dt><dd>';
			$objuser->fetch($objact->fk_user_create);
			if ($objuser->id == $objact->fk_user_create) print $objuser->lastname.' '.$objuser->firstname;
			else print '&nbsp;';
			print '</dd>';

			//statut
			print '<dt>'.$langs->trans('Statut').'</dt><dd>';
			print $objact->getLibStatut();
			print '</dd>';

			print "</dl>";

			print '</div>';

			/* ********************************* */
			/*                                   */
			/* Barre d'action                    */
			/*                                   */
			/* ********************************* */

			print '<div class="modal-footer">';
			 //print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';

			if ($action == '')
			{
				//aumentar la verificacion del estadod
				if (($user->rights->poa->act->mod && $objact->statut == 0) && ($objact->fk_user_create == $user->id || $user->admin))
				{
					print '<a class="btn btn-primary btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=edit&ida='.$objact->id.'">'.$langs->trans("Modify").'</a>';
				}
				else
					print '<a class="butActionRefused" href="#">'.$langs->trans("Modify").'</a>';
				if ($user->rights->poa->act->del && $objact->statut == 0)
					print '<a class="btn btn-danger btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=confirm_delete&ida='.$objact->id.'">'.$langs->trans("Delete").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
				if ($user->rights->poa->act->val && $objact->statut == 0)
					print '<a class="btn btn-success btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=validate&ida='.$objact->id.'">'.$langs->trans("Validate").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
				if ($user->rights->poa->act->val && $objact->statut == 1)
					print '<a class="btn btn-success btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=novalidate&ida='.$objact->id.'">'.$langs->trans("Notvalidate").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Notvalidate")."</a>";

				if ($user->rights->poa->act->nul && $objact->statut > 0)
					print '<a class="btn btn-danger btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=anulate&ida='.$objact->id.'">'.$langs->trans("Cancel").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";
					///*				if ($user->rights->poa->prev->crear && $object->statut == 1 && $objact->fk_prev <=0)
				//print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?action=create&dol_hide_leftmenu=1&fk_poa='.$objact->fk_poa.'&fk_activity='.$objact->id.'">'.$langs->trans("Createprev").'</a>';

				if ($user->rights->poa->act->end && ($objact->statut > 0 && $objact->statut <9))
					print '<a class="btn btn-success btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=close&dol_hide_leftmenu=1&ida='.$objact->id.'">'.$langs->trans("Finishactivity").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Finishactivity")."</a>";

				if ($user->rights->poa->act->end && $objact->statut == 9)
					print '<a class="btn btn-success btn-flat" href="'.$_SERVER['PHP_SELF'].'?modal=ficheactivity&action=noclose&ida='.$objact->id.'">'.$langs->trans("Reopenactivity").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Reopenactivity")."</a>";
			}
			print "</div>";
		}
		//Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			//print_fiche_titre($langs->trans("Activityedit"), $mesg);
			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="ida" value="'.$objact->id.'">';
			print '<input type="hidden" name="modal" value="ficheactivity">';

			print '<div class="modal-body" style="background:#fff; color:#000; !important">';

			// poa
			//print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('POA').'</label>';
			print '<div class="col-sm-8">';
			$objpoa->fetch($objact->fk_poa);
			print $objpoa->label;
			print '<input type="hidden" value="'.$objact->fk_poa.'" name="fk_poa">';
			print '</div>';
			print '</div>';

			// area
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Area').'</label>';
			print '<div class="col-sm-8">';
			$objarea->fetch($objact->fk_area);
			print $objarea->label;
			print '<input type="hidden" value="'.$objact->fk_area.'" name="fk_area">';
			print '</div>';
			print '</div>';

			// pac
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('PAC').'</label>';
			print '<div class="col-sm-8">';
			print $objpac->select_pac($objact->fk_pac,'fk_pac','',120,1);
			print '</div>';
			print '</div>';

			//fk_prev
			if ($user->admin || $user->rights->poa->act->crear)
			{
				print '<div class="clearfix visible-xm"></div>';
				print '<div class="form-group">';
				print '<label class="control-label col-xs-3">'.$langs->trans('Preventive').'</label>';
				print '<div class="col-sm-8">';
				print $object->select_poa_prev($objact->fk_prev,'fk_prev','',100,1,$gestion,$fk_area,'T');
				print '</div>';
				print '</div>';
			}
			//print '<div class="clearfix visible-xm"></div>';
			// gestion
			//print '<div class="form-group">';
			//print '<label class="control-label col-xs-3">'.$langs->trans('Gestion').'</label>';
			//print '<div class="col-xs-4">';
			//print '<input class="form-control" type="text" value="'.(empty($objact->gestion)?date('Y'):$objact->gestion).'" name="gestion"  maxlength="4">';
			//print '</div>';
			//print '</div>';
			// label
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Name').'</label>';
			print '<div class="col-sm-8">';
			print '<input class="form-control" type="text" value="'.$objact->label.'" name="label" maxlength="255">';
			print '</div>';
			print '</div>';

			// pseudonym
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Pseudonym').'</label>';
			print '<div class="col-sm-8">';
			print '<input class="form-control" type="text" value="'.$objact->pseudonym.'" name="pseudonym" maxlength="50">';
			print '</div>';
			print '</div>';

			//nro
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Nro').'</label>';
			print '<div class="col-sm-8">';
			if ($user->admin)
			{
				print '<div class="col-xs-4">';
				print '<input class="form-control" type="number" value="'.$objact->nro_activity.'" name="nro_activity" maxlength="12">';
				print '</div>';
				print '<div class="col-xs-4">';
				print '<input class="form-control" type="year" value="'.$objact->gestion.'" name="gestion">';
				print '</div>';
			}
			else
			{
				print $objact->nro_activity.'/'.$objact->gestion;
				print '<input type="hidden" value="'.$objact->nro_activity.'" name="nro_activity">';
				print '<input type="hidden" value="'.$objact->gestion.'" name="gestion">';
			}
			print '</div>';
			print '</div>';


			//priority
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Priority').'</label>';
			print '<div class="col-sm-8">';
			print '<input class="form-control" type="text" value="'.$objact->priority.'" name="priority" maxlength="2">';
			print '</div>';
			print '</div>';


			//requirementtype
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Requirementtype').'</label>';
			print '<div class="col-sm-8">';
			print select_requirementtype($objact->code_requirement,'code_requirement','',1,0,'code');
			print '</div>';
			print '</div>';

			//date_preventive
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Date').'</label>';
			print '<div class="col-sm-8">';
			$aDate = dol_getdate($objact->date_activity);
			$date_activity = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
			print '<div class="input-group date" id="divMiCalendario">';
			print '<input type="text" name="di_" id="dateacti0" class="form-control" value="'.$date_activity.'" readonly/>';
			print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
			print '</div>';
			print '</div>';
			print '</div>';


			//continuacion de preventivo gestiones anteriores
			$nro_preventive_ant = '';
			$gestion_ant = '';
			if ($objact->fk_prev_ant)
			{
				$objnew = new Poaprev($db);
				if ($objnew->fetch($objact->fk_prev_ant)>0)
				{
					$nro_preventive_ant = $objnew->nro_preventive;
					$gestion_ant = $objnew->gestion;
				}
			}

			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Preventivemainlast').'</label>';

			print '<div class="col-sm-8">';
			print '<div class="col-xs-4 col-sm-2">';
			print '<input class="form-control" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant"  maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
			print '</div>';
			print '<div class="col-xs-4 col-sm-2">';
			print '<input class="form-control" type="text" value="'.$gestion_ant.'" name="gestion_ant"  maxlength="4" placeholder="'.$langs->trans('Year').'">';
			print '</div>';
			print '<div class="col-xs-3 col-sm-2">';
			print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
			print '</div>';

			print '</div>';
			print '</div>';

			//partida
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Partida').'</label>';
			print '<div class="col-sm-8">';
			print '<input class="form-control" type="text" value="'.(empty($objact->partida)?$objpoa->partida:$objact->partida).'" name="partida" maxlength="12">';
			print '</div>';
			print '</div>';

			//monto
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Amount').'</label>';
			print '<div class="col-sm-8">';
			print '<input class="form-control" type="number" step="any" min="0" value="'.(empty($objact->amount)?$objpoa->amount:$objact->amount).'" name="amount" size="10" maxlength="12">';
			print '</div>';
			print '</div>';

			//respon
			print '<div class="clearfix visible-xm"></div>';
			print '<div class="form-group">';
			print '<label class="control-label col-xs-3">'.$langs->trans('Responsible').'</label>';
			$exclude = array();
			if (empty($objact->entity)) $objact->entity = $conf->entity;
			print '<div class="col-sm-8">';
			print $form->select_dolusers((empty($objact->fk_user_create)?$user->id:$objact->fk_user_create),'fk_user_create',1,$exclude,0,'','',$objact->entity);
			print '</div>';
			print '</div>';

			print '</div>';

			print '<div class="modal-footer">';
			print '<a class="btn btn-danger pull-left" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$ida.'">'.$langs->trans('Cancel').'</a>';
			print '<input type="submit" class="btn btn-primary pull-right" value="'.$langs->trans("Save").'">';
			print '</div>';

			print '</form>';
		}
	}
}

print '</div>';//modal-content
print '</div>';//modal-dialog
print '</div>';//modal-success">';
print '</div>';//poa-modal
print '</div>';//activity

?>
