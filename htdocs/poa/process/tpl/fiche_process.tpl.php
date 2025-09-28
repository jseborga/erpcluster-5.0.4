<?php
//fiche_process
$search = GETPOST('search');
$objprev = new Poaprev($db);

$display ='none';
if (isset($modal) && $modal == 'ficheprocess')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#ficheprocess").modal("show");
	});
</script>';
}
//$display = 'block';
print '<div id="ficheprocess" class="modal modal-warning" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760;" aria-hidden="false">';
print '<div class="poa-modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.($idProcess > 0 ?$langs->trans('Process'):$langs->trans("Newprocess")).'</h4>';
print '</div>';

include DOL_DOCUMENT_ROOT.'/poa/process/tpl/script_process.tpl.php';
  //definimos fecha para sacar un tipo de formulario
  //hasta el 31/08/215  $lForm = true;
  //desde el 1/09/2015 $lForm = false;
$aDatea = dol_getdate(dol_now());
$aDateact = dol_mktime(23, 59, 59, $aDatea['mon'],$aDatea['mday'],$aDatea['year']);

$aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
if ($aDateact > $aDatelim)
	$lForm = false;
else
	$lForm = true;

if (empty($search)) $search = $objact->id;
if (empty($objproc->id)) $action = 'create';
$fk_poa_prev = $objact->fk_prev;
$amount = $object->amount;
if ($action == 'create' && $user->rights->poa->proc->crear)
{
	//print_fiche_titre($langs->trans("Newprocess"));
	if ($fk_poa_prev)
	{
		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {
			$("#selectfk_type_con").change(function() {
				document.fiche_process.action.value="createedit";
				document.fiche_process.submit();
			});
			$("#amount").change(function() {
				document.fiche_process.action.value="createedit";
				document.fiche_process.submit();
			});

		});';
		print '</script>'."\n";
		print '<form id="fiche_process" class="form-horizontal col-sm-12" role="form" name="fiche_process" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="fk_poa_prev" value="'.$fk_poa_prev.'">';
		print '<input type="hidden" name="ida" value="'.$ida.'">';
		print '<input type="hidden" name="fk_area" value="'.$object->fk_area.'">';
		print '<input type="hidden" name="modal" value="ficheprocess">';

		dol_htmloutput_mesg($mesg);

		print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

			//mostramos
			//preventivo seleccionado
			//ref
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Ref').'</label>';
		print '<div class="col-sm-8">';
		print '<div class="col-md-2">';
		print '(PROV)';
		print '<input type="hidden" name="ref" value="0">';
		print '</div>';
		print '<div class="col-md-1">';
		print ' / ';
		print '</div>';
		print '<div class="col-md-3">';
		print '<input type="year" class="form-control" name="gestion" value="'.$object->gestion.'" maxlenght="4">';
		print '</div>';
		print '</div>';
		print '</div>';

		print '<div class="form-group">';
		print '<label class="control-label col-xs-3">'.$langs->trans('Date').'</label>';
		print '<div class="col-xs-8">';
			//date
	    //convertimos la fecha
		$aDate = dol_getdate(dol_now());
		if (!empty($objproc->date_process))
			$aDate = dol_getdate($objproc->date_process);
		$date_ = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
		print '<div class="well well-sm col-sm-6">';
		print '<div class="input-group date" id="divMiCalendario">';
		print '<input type="text" name="di_" id="dateproc" class="form-control" value="'.$date_.'" readonly/>';
		print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
		print '</div>';
		print '</div>';
		print '</div>';
		print '</div>';

			//amount
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Reference price').'</label>';
		print '<div class="col-sm-8">';
		print '<input id="price" class="form-control" step="any" max="'.$amount.'" type="number" value="'.$amount.'" name="amount" maxlength="12" required>';
		print '</div>';
		print '</div>';

			//type modality
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Modality').'</label>';
		print '<div class="col-sm-8">';
		if ($_POST['fk_type_con'] != $objproc->fk_type_con && !empty($_POST['fk_type_con']))
			$objproc->fk_type_con = $_POST['fk_type_con'];
		print select_tables($objproc->fk_type_con,'fk_type_con',' onChange="cargaContenidopro(this.id)"',1,0,'05',$amount);
		print '</div>';
		print '</div>';

			//label
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Title').'</label>';
		print '<div class="col-sm-8">';
		print '<input id="labelprocess" class="form-control" type="text" value="'.$object->label.'" name="label" maxlength="255" placeholder="'.$langs->trans('Title').'" required>';
		print '</div>';
		print '</div>';

			//justification
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Justification').'</label>';
		print '<div class="col-sm-8">';
		print '<textarea class="form-control" name="justification" required>'.$justification.'</textarea>';
		print '</div>';
		print '</div>';

			//type adj
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Type of adjuditacion').'</label>';
		print '<div class="col-sm-8">';
		print select_tables((empty($objproc->fk_type_adj)?3:$objproc->fk_type_adj),'fk_type_adj','',0,0,'01');
		print '</div>';
		print '</div>';

		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label">'.$langs->trans('Ref. PAC').'</label>';
		print '<div class="col-sm-8">';
		print '<input id="ref_pac" class="form-control" type="text" value="'.$objproc->ref_pac.'" name="ref_pac" maxlength="255" placeholder="'.$langs->trans('Refpac').'">';
		print '</div>';
		print '</div>';

			//moviendo codigo
		//print '</div>';
		print '<div class="row">';
			//respaldo de documentos segun tipo de contratacion
			//buscamos el tipo de contratacion
		print '<div id="idTable">';
		print '<p>'.$langs->trans('Pendiente').'</p>';
		print '</div>';
		print '</div>';

		print '</div>';

		print '<div class="modal-footer">';
		print '<center><input type="submit" class="btn btn-primary" value="'.$langs->trans("Create").'"></center>';
		if ($user->rights->poa->prev->leer)
		{
			if ($objact->fetch('',$fk_poa_prev)>0)
			{
				print '<a class="btn btn-primary" href="'.$_SERVER['PHP_SELF'].(isset($_GET['nopac'])?'?nopac=1&ida='.$objact->id:'?ida='.$objact->id).'">'.$langs->trans("Return").'</a>';
			}
		}
		else
			print '<a class="btn btn-primary disabled" href="#">'.$langs->trans("Return").'</a>';
		print '</div>';
		print '</form>';
	}
}
else
{
	if ($idProcess)
	{
		//dol_htmloutput_mesg($mesg);
		//$result = $objproc->fetch($id);
		if ($objproc->id <= 0) dol_print_error($db);

		//definimos fecha para sacar un tipo de formulario
		//hasta el 31/08/215  $lForm = true;
		//desde el 1/09/2015 $lForm = false;
		$aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
		$aDateobj = dol_getdate($objproc->date_process);
		$aDateobj = dol_mktime(0, 0, 1, $aDateobj['mon'],$aDateobj['mday'],$aDateobj['year']);
		if ($aDateobj <= $aDatelim) $lForm = true;
		else $lForm = false;
		if ( ($action == 'createeditdos') )
		{
			$objproc->fetch($idProcess);
			require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			$tmparray['fk_type_con'] = GETPOST('fk_type_con');
			$tmparray['fk_poa_prev'] = GETPOST('fk_poa_prev');
			$tmparray['gestion'] = GETPOST('gestion');
			$tmparray['ref'] = GETPOST('ref');
			$tmparray['amount'] = GETPOST('amount');

			$fk_poa_prev = GETPOST('fk_poa_prev');
			if (!empty($tmparray['fk_poa_prev']))
			{
				//buscamos
				if ($objprev->fetch($tmparray['fk_poa_prev']))
				{
					$tmparray['gestion'] = $objprev->gestion;
					$tmparray['fk_pac'] = $objprev->fk_pac;
					$tmparray['fk_area'] = $objprev->fk_area;
					//recuperamos la gerencia, subgerencia y dpto
					$tmparray = $objarea->getarea($objprev->fk_area,$tmparray);
					$tmparray['label'] = $objprev->label;
					$tmparray['nro_preventive'] = $objprev->nro_preventive;
					if (empty($tmparray['amount'])) $tmparray['amount'] = $objprev->amount;
					$tmparray['fk_user_create'] = $objprev->fk_user_create;
					//buscamos el pac si corresponde
					if ($objprev->fk_pac > 0)
					{
						if ($objpac->fetch($objprev->fk_pac))
						{
							$tmparray['fk_poa'] = $objpac->fk_poa;
							//$tmparray['fk_type_con'] = $objpac->fk_type_modality;
							$tmparray['fk_type_object'] = $objpac->fk_type_object;
							$tmparray['partida'] = $objpac->partida;
							$tmparray['ref_pac'] = $objpac->ref.': '.$objpac->nom;
						}
						else
							$tmparray['ref_pac'] = $langs->trans('Notrequired');

					}
					$objproc->fk_poa_prev = $tmparray['fk_poa_prev'];
					$objproc->gestion = $tmparray['gestion'];
					$objproc->fk_pac = $tmparray['fk_pac'];
					$objproc->fk_type_con = $tmparray['fk_type_con'];
					$objproc->fk_area = $tmparray['fk_area'];
					$objproc->label = $tmparray['label'];
					$objproc->nro_preventive = $tmparray['nro_preventive'];
					$objproc->area = $tmparray['area'];
					$objproc->amount = $tmparray['amount'];
					$objproc->gestion = $tmparray['gestion'];
					$objproc->ref = $tmparray['ref'];
					$objproc->fk_user_create = $tmparray['fk_user_create'];

					$objproc->ref_pac = $tmparray['ref_pac'];
				}
			}
			$action='edit';
		}
		/*
		* Affichage fiche
		*/
		if ($action <> 'edit' && $action <> 're-edit')
		{
			//$head = fabrication_prepare_head($object);
			//dol_fiche_head($head, 'card', $langs->trans("Process"), 0, 'mant');

			if ($modal == 'ficheprocess')
			{
				/*
				* Confirmation de la validation
				*/
				if ($action == 'validate')
				{
					//$objproc->fetch(GETPOST('id'));
					//cambiando a validado
					$db->begin();
					//cambiando el preventivo a statut 1
					if ($objprev->fetch($objproc->fk_poa_prev))
					{
						$objprev->active = 2;
						$objprev->update($db);
					}
					//update
					if ($objproc->ref == 0)
					{
						$objectproces = new Poaprocess($db);
						$objectproces->get_maxref($objproc->gestion);
						$objproc->ref   = $objectproces->maximo;
					}
					$objproc->statut = 1;
					$res = $objproc->update($user);
					if ($res > 0) $db->commit();
					else $db->rollback();
					$action = '';
				}

			 	// Confirm delete third party
				if ($action == 'delete')
				{
					$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?ida='.$ida.'id='.$objproc->id,$langs->trans("Deleteprocess"),$langs->trans("Confirmdeleteprocess",$objproc->ref.' '.$objproc->detail),"confirm_delete",'',0,2);
					if ($ret == 'html') print '<br>';
				}
			}

			print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

			print '<dl class="dl-horizontal">';
			// Confirm cancel proces
			if ($action == 'anulate')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$objproc->id,$langs->trans("Cancelprocess"),$langs->trans("Confirmcancelprocess",$objproc->ref.' '.$objproc->detail),"confirm_cancel",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			//ref
			print '<dt>'.$langs->trans('Ref').'</dt>';
			print '<dd>';
			print $objproc->ref;
			print ' / ';
			print $objproc->gestion;
			print '</dd>';

			//date
			print '<dt>'.$langs->trans('Date').'</dt>';
			print '<dd>';
			print dol_print_date($objproc->date_process,'day');
			print '</dd>';
			$date1 = $objproc->date_process;
			$numDay = $conf->global->POA_PREVENTIVE_DAY_DELAY;
			$date0 = strtotime("+$numDay day",$date1);

			//amount
			print '<dt>'.$langs->trans('Reference price').'</dt>';
			print '<dd>';
			print number_format(price2num($objproc->amount,'MT'),2);
			print '</dd>';

			//type modality
			//revisamos si el tipo contrato del pac esta idem al seleccionado
			$typecontrat = '';
			$lValid = true;
			if ($objprev->fk_pac > 0)
			{
				//buscamos el pac
				if ($objpac->fetch($objprev->fk_pac))
				{
					//verificamos
					if ($objpac->fk_type_modality != $objproc->fk_type_con)
					{
						//analizamos el tipo de contrato
						$aTable = fetch_tables($objpac->fk_type_modality);
						$typecontrat = $aTable['label'];
					}
				}
				else
					$typecontrat = $langs->trans('Notdefined');
			}

			print '<dt>'.$langs->trans('Modality').'</dt>';
			print '<dd>';
			print select_tables($objproc->fk_type_con,'fk_type_con','',0,1,'05');
			if (!empty($typecontrat))
			{
				$lValid = false;
				print '&nbsp;';
				print ' <> ';
				print '<span class="textred">'.$typecontrat.'</span>';
			}
			print '</dd>';

			//label
			print '<dt>'.$langs->trans('Title').'</dt>';
			print '<dd>';
			print $objproc->label;
			print '</dd>';

			//justification
			print '<dt>'.$langs->trans('Justification').'</dt>';
			print '<dd>';
			print $objproc->justification;
			print '</dd>';

			//type adj
			print '<dt>'.$langs->trans('Type of adjudication').'</dt>';
			print '<dd>';
			print select_tables($objproc->fk_type_adj,'fk_type_adj','',0,1,'01');
			print '</dd>';
			print '<dt>'.$langs->trans('Refpac').'</td>';
			print '<dd>';
			print $objproc->ref_pac;
			print '</dd>';

			//registro del cuce y codigo del proceso
			if ($objproc->amount > $conf->global->POA_PAC_MINIMUM)
			{
				print '<dt>'.$langs->trans('CUCE').'</dt>';
				print '<dd>';
				$idTagps = 'di_'.$objproc->id;
				$idTagps2 = 'di_'.$objproc->id.'_';
				$idTagps3 = 'dpp'.$objproc->id;
				if (($user->rights->poa->proc->mod && $objproc->statut == 1 && $user->id == $objprev->fk_user_create) || $user->admin)
				{
					print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
					print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaproc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="updateproc">';
					print '<input type="hidden" name="id" value="'.$objproc->id.'">';
					print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

					// //original
					print '<input type="text" name="di_'.$objproc->id.'" id="di_'.$objproc->id.'" value="'
					.$objproc->cuce
					.'" onblur='."'".'CambiarURLFramecuce("'.$idTagps.'","'.$idTagps2.'","'
					.$objproc->id.'",'.'this.value);'."'". 'size="14" maxlength="16" placeholder="'
					.$langs->trans('CUCE').'">';
					print ' '.info_admin($langs->trans("Recordthenumbersnodashes"),1);
					print '</form>';
					print '</span>';


					print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'".'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
					print (empty($objproc->cuce)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$objproc->cuce);
					print '</span>';
				}
				else
				{
					print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
					print (empty($objproc->cuce)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$objproc->cuce);
					print '</span>';
				}
				print '</dd>';
				//registro del codigo del proceso
				print '<dt>'.$langs->trans('Codeprocess').'</dt>';
				print '<dd>';
				$idTagps = 'df_'.$objproc->id;
				$idTagps2 = 'df_'.$objproc->id.'_';
				$idTagps3 = 'dpf'.$objproc->id;
				if (($user->rights->poa->proc->mod && $objproc->statut == 1 && $user->id == $objprev->fk_user_create) || $user->admin)
				{
					print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
					print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaproc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="updatecode">';
					print '<input type="hidden" name="id" value="'.$objproc->id.'">';
					print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

					// //original
					print '<input type="text" name="df_'.$objproc->id.'" id="df_'.$objproc->id.'" value="'
					.$objproc->code_process
					.'" onblur='."'".'CambiarURLFramecode("'.$idTagps.'","'.$idTagps2.'","'
					.$objproc->id.'",'.'this.value);'."'". 'size="20" maxlength="30" placeholder="'
					.$langs->trans('Codeprocess').'">';
					print ' '.info_admin($langs->trans("Registerthecodeoftheentitytoidentifytheprocess"),1);
					print '</form>';
					print '</span>';


					print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'".'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
					print (empty($objproc->code_process)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$objproc->code_process);
					print '</span>';
				}
				else
				{
					print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
					print (empty($objproc->code_process)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$objproc->code_process);
					print '</span>';
				}
				print '</dt>';
			}
			  //subir imagen
			print '<dt>'.$langs->trans('PDF').'</dt>';
			print '<dd>';
			$dir = $conf->poa->dir_output.'/process/pdf/'.$objproc->id.'.pdf';
			$url = DOL_URL_ROOT.'/documents/poa/process/pdf/'.$objproc->id.'.pdf';
			if ($user->admin || $user->rights->poa->proc->mod)
			{
				if ($action !='upload')
				{
					print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&id='.$idProcess.'&action=upload'.'&modal=ficheprocess">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
					//mostramos el archivo
					if (file_exists($dir))
					{
						print '&nbsp;&nbsp;';
						print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
					}
				}
				else
				{
					$idreg = $objproc->id;
					include DOL_DOCUMENT_ROOT.'/poa/process/tpl/addpdf.tpl.php';
				}
			}
			else
			{
				//mostramos el archivo
				if (file_exists($dir))
				{
					print '&nbsp;&nbsp;';
					print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
				}
			}
			print '</dt>';

			if ($action == 'edit')
			{
				print '<table class="table">';
				//respaldo de documentos segun tipo de contratacion
				//buscamos el tipo de contratacion
				$aTable = fetch_tables($objproc->fk_type_con);
				print '<tr>';
				print '<th colspan="4">'.$langs->trans("Necessary documentation").'</th>';
				print '</tr>';
				print '<tr>';
				print '<th colspan="3">&nbsp;'.'</th>';
				print '<th>'.$aTable['label'].'</th>';
				print '</tr>';
				//generico
				if ($aTable['type'] == 'MENSPAC' || $aTable['type'] == 'MEN')
					$value = 1;
				elseif (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY')
					$value = 2;
				elseif (STRTOUPPER($aTable['type']) == 'LP')
					$value = 3;
				elseif (STRTOUPPER($aTable['type']) == 'DIREC')
					$value = 4;
				elseif (STRTOUPPER($aTable['type']) == 'CAE')
					$value = 5;

				//type certif presup
				print '<tr><td colspan="3">'.$langs->trans('doc_cp').'</td><td align="center">';
				if ($objproc->doc_certif_presupuestaria > 0)
					print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				else
					print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
				print '</td></tr>';
				//precio referencial
				print '<tr><td colspan="3">'.$langs->trans('doc_pr').'</td><td align="center">';
				if ($objproc->doc_precio_referencial > 0)
					print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				else
					print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
				print '</td></tr>';
				//type especif tecnica
				print '<tr><td colspan="3">'.$langs->trans('doc_et').'</td><td align="center">';
				if ($objproc->doc_especific_tecnica > 0)
					print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				else
					print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
				print '</td></tr>';
				//modelo contrato
				if ($lForm)
				{
					print '<tr><td colspan="3">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
					if ($objproc->doc_modelo_contrato > 0)
						print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
					else
						print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
				}
				else
				{
					//nuevo formulario
					if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' || STRTOUPPER(trim($aTable['type'])) == 'LP' || STRTOUPPER(trim($aTable['type'])) == 'CEA' )
					{
						print '<tr><td colspan="3">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
						if ($objproc->doc_modelo_contrato > 0)
							print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
						else
							print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
					}

				}
				print '</td></tr>';

				//fotocopia PAC
				if (!$lForm)
				{
					print '<tr><td colspan="3">'.$langs->trans('Fotocopia hoja PAC donde se encuentra incluido proceso de contratacion').'</td><td nowrap align="center">';
					if ($objproc->doc_pac > 0)
						print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
					else
						print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
				}

				if (!$lForm)
				{
					if (STRTOUPPER($aTable['type']) == 'DIREC')
					{
						//informe tecnico LEGAL
						print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td align="center">';
						if ($objproc->doc_informe_lega > 0)
							print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
						else
							print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
						print '</td></tr>';
					}
				}
				else
				{
					if (STRTOUPPER($aTable['type']) != 'MENSPAC' && STRTOUPPER($aTable['type']) != 'MEN')
					{
						//informe tecnico LEGAL
						print '<tr><td colspan="3">'.$langs->trans('doc_it').'</td><td align="center">';
						if ($objproc->doc_informe_lega > 0)
							print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
						else
							print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
						print '</td></tr>';
					}
				}
				if (!$lForm)
				{
					//Seleccion de mepresa proponente
					if (STRTOUPPER($aTable['type']) == 'DIREC' || STRTOUPPER($aTable['type']) == 'EXCEP')
					{
						print '<tr><td colspan="3">'.$langs->trans('Proponente Seleccionado').'</td><td align="center">'.'|'.$lForm.'|';
						;
						print $form->select_company('','fk_soc','',1,0,0);
						print '</td></tr>';
					}
				}
				if (!$lForm)
				{
					//lista de proponentes para CM
					if (STRTOUPPER($aTable['type']) == 'MENSPAC')
					{
						print '<tr><td colspan="3">'.$langs->trans('Lista de proponentes').'</td><td align="center">';
						if ($objproc->doc_prop > 0)
							print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
						else
							print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
						print '</td></tr>';
					}
				}

				//metodo de seleccion
				if (STRTOUPPER($aTable['type']) != 'MENSPAC' && STRTOUPPER($aTable['type']) != 'MEN' && STRTOUPPER($aTable['type']) != 'DIREC' && !empty($objproc->fk_type_con))
				{

					// print '<tr class="liste_titre">';
					// print_liste_field_titre($langs->trans("Selection method"),"", "","","",'colspan="4"');
					// print '</tr>';
					print '<tr>';
					print '<th colspan="3">'.$langs->trans('Method selection and award').'</th>';
					print '<th colspan="3">&nbsp;'.'</th>';
					print '</tr>';

					if (STRTOUPPER($aTable['type']) != 'CEA')
					{
						include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/title_cea.tpl.php';
						if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' || STRTOUPPER($aTable['type']) == 'LP' )
						{
							//calidad propuesta tecnica y costo
							print '<tr>';
							include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/cptc.tpl.php';
							print '<td align="center">';
							if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
							{
								if ($objproc->metodo_sel_anpe == 1)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);

							}
							elseif (STRTOUPPER($aTable['type']) == 'LP' )
							{
								if ($objproc->metodo_sel_lpni == 1)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							print '</td></tr>';

							//calidad
							print '<tr>';
							include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/c.tpl.php';
							print '<td align="center">';

							if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
							{
								if ($objproc->metodo_sel_anpe == 2)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							elseif (STRTOUPPER($aTable['type']) == 'LP' )
							{
								if ($objproc->metodo_sel_lpni == 2)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							print '</td></tr>';

							//Presupuesto Fijo
							print '<tr>';
							include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pf.tpl.php';
							print '<td align="center">';
							if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
							{
								if ($objproc->metodo_sel_anpe == 3)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							elseif (STRTOUPPER($aTable['type']) == 'LP' )
							{
								if ($objproc->metodo_sel_lpni == 3)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							print '</td></tr>';

							//Menor Costo
							print '<tr>';
							include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/mc.tpl.php';

							print '<td align="center">';

							if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
							{
								if ($objproc->metodo_sel_anpe == 4)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							elseif (STRTOUPPER($aTable['type']) == 'LP' )
							{
								if ($objproc->metodo_sel_lpni == 4)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							print '</td></tr>';

							//Prcio evaluado mas bajo (PEMB)
							print '<tr>';
							include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pemb.tpl.php';

							print '<td align="center">';

							if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
							{
								if ($objproc->metodo_sel_anpe == 5)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							elseif (STRTOUPPER($aTable['type']) == 'LP' )
							{
								if ($objproc->metodo_sel_lpni == 5)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							print '</td></tr>';

							//formulario de condiciones... (PEMB)
							print '<tr><td  colspan="3">'.$langs->trans('Formulario de Condiciones Adicionales (Excepto para el metodo de PEMB)').'</td>';
							print '<td align="center">';

							if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
							{
								if ($objproc->condicion_adicional_anpe > 0)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							elseif (STRTOUPPER($aTable['type']) == 'LP' )
							{
								if ($objproc->condicion_adicional_lpni > 0)
									print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
								else
									print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
							}
							print '</td></tr>';

						}
					}
					else
					{
						//modelo CAE
						print '<tr><td  colspan="3" class="fieldrequired">'.$langs->trans('mod_cae').'</td>';
						print '<td align="center">';
						if ($objproc->metodo_sel_cae > 0)
							print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
						else
							print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
						print '</td></tr>';

					}
				}
				print '</table>';
			}
			print '</dl>';
			print '</div>';

			//	print '</div>';


			/* ************************************** */
			/*                                        */
			/* Barre d'action                         */
			/*                                        */
			/* ************************************** */
			print '<div class="modal-footer">';

			if ($user->rights->poa->prev->leer)
				print '<a class="btn btn-default btn-flat" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
			if ($action == '')
			{
				if ($user->admin && $objproc->statut == 0 ||
					($user->rights->poa->proc->mod && $objproc->statut == 0))
					print '<a class="btn btn-primary btn-flat" href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&action=edit&id='.$objproc->id.'&modal=ficheprocess">'.$langs->trans("Modify").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->poa->proc->del && $objproc->statut == 0)
					print '<a class="btn btn-danger btn-flat" href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&action=delete&id='.$objproc->id.'&modal=ficheprocess">'.$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
				if ($user->rights->poa->proc->val && $objproc->statut == 0 && $lValid)
					print '<a class="btn btn-success btn-flat" href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&action=validate&id='.$objproc->id.'&modal=ficheprocess">'.$langs->trans("Validate")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
				if ($user->rights->poa->proc->nul && $objproc->statut == 1)
					print '<a class="btn btn-danger btn-flat" href="'.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&action=anulate&id='.$objproc->id.'&modal=ficheprocess">'.$langs->trans("Cancel")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";

				if ($objproc->statut >= 1)
				{
					if ($lForm)
						print '<a class="btn btn-flat" href="'.DOL_URL_ROOT.'/poa/process/fiche_iniproc.php?id='.$objproc->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/poa/img/excel','',1)."</a>";
					else
						print '<a class="btn btn-flat" href="'.DOL_URL_ROOT.'/poa/process/fiche_iniproc_20150901.php?id='.$objproc->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/poa/img/excel','',1)."</a>";
				}
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Excel")."</a>";

			}
			print "</div>";
		}

		/*
		* Edition fiche
		*/
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				$("#selectfk_type_con").change(function() {
					document.fiche_process.action.value="createedit";
					document.fiche_process.submit();
				});
				$("#amount").change(function() {
					document.fiche_process.action.value="createedit";
					document.fiche_process.submit();
				});
			});';
			print '</script>'."\n";
			print '<form id="fiche_process" class="form-horizontal col-sm-12" role="form" name="fiche_process" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="fk_poa_prev" value="'.$fk_poa_prev.'">';
			print '<input type="hidden" name="ida" value="'.$ida.'">';
			print '<input type="hidden" name="fk_area" value="'.$objproc->fk_area.'">';
			print '<input type="hidden" name="modal" value="ficheprocess">';

			dol_htmloutput_mesg($mesg);
			print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

			if ($fk_poa_prev >0)
			{
				//ref
				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Ref').'</label>';
				print '<div class="col-xs-4">';
				print '<input class="form-control" type="number" name="ref" value="'.$objproc->ref.'">';
				//print '<input type="hidden" name="ref" value="0">';
				print '</div>';
				print '<div class="col-xs-4">';
				print '<input type="year" class="form-control" name="gestion" value="'.$objproc->gestion.'" maxlenght="4">';
				print '</div>';
				print '</div>';

				//date
				print '<div class="form-group">';
				print '<label class="control-label col-xs-3">'.$langs->trans('Date').'</label>';
				print '<div class="col-xs-8">';
			    //convertimos la fecha
				$aDate = dol_getdate(dol_now());
				if (!empty($objproc->date_process))
					$aDate = dol_getdate($objproc->date_process);
				$date_ = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
				print '<div class="well well-sm col-sm-6">';
				print '<div class="input-group date" id="divMiCalendario">';
				print '<input type="text" name="di_" id="dateproc" class="form-control" value="'.$date_.'" readonly/>';
				print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
				print '</div>';
				print '</div>';
				print '</div>';
				print '</div>';

				//amount
				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Reference price').'</label>';
				print '<div class="col-xs-8">';
				print '<input id="price" class="form-control" step="any" max="'.$amount.'" type="number" value="'.$amount.'" name="amount" maxlength="12" required>';
				print '</div>';
				print '</div>';

				//type modality
				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Modality').'</label>';
				print '<div class="col-xs-8">';
				if ($_POST['fk_type_con'] != $objproc->fk_type_con && !empty($_POST['fk_type_con']))
					$objproc->fk_type_con = $_POST['fk_type_con'];
				print select_tables($objproc->fk_type_con,'fk_type_con',' onChange="cargaContenidopro(this.id)"',1,0,'05',$amount);
				print '</div>';
				print '</div>';

				//label
				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Title').'</label>';
				print '<div class="col-xs-8">';
				print '<input id="labelprocess" class="form-control" type="text" value="'.$object->label.'" name="label" maxlength="255" placeholder="'.$langs->trans('Title').'" required>';
				print '</div>';
				print '</div>';

				//justification
				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Justification').'</label>';
				print '<div class="col-xs-8">';
				print '<textarea class="form-control" name="justification" required>'.$objproc->justification.'</textarea>';
				print '</div>';
				print '</div>';

				//type adj
				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Type of adjuditacion').'</label>';
				print '<div class="col-xs-8">';
				print select_tables((empty($objproc->fk_type_adj)?3:$objproc->fk_type_adj),'fk_type_adj','',0,0,'01');
				print '</div>';
				print '</div>';

				print '<div class="form-group">';
				print '<label class="col-sm-3 control-label">'.$langs->trans('Ref. PAC').'</label>';
				print '<div class="col-xs-8">';
				print '<input id="ref_pac" class="form-control" type="text" value="'.$objproc->ref_pac.'" name="ref_pac" maxlength="255" placeholder="'.$langs->trans('Refpac').'">';
				print '</div>';
				print '</div>';
				//moviendo codigo
			}

			print '<div class="row">';
			//respaldo de documentos segun tipo de contratacion
			//buscamos el tipo de contratacion
			$fk_type_con = $objproc->fk_type_con;
			print '<div id="idTable">';
			include DOL_DOCUMENT_ROOT.'/poa/process/tpl/fiche_process_type.tpl.php';
			print '</div>';

			print '</div>';
			//row

			print '</div>';

			print '<div class="modal-footer">';
			if ($user->rights->poa->prev->leer)
			{
				if ($objact->fetch('',$fk_poa_prev)>0)
				{
					print '<a class="btn btn-danger pull-left" href="'.$_SERVER['PHP_SELF'].(isset($_GET['nopac'])?'?nopac=1&ida='.$objact->id:'?ida='.$objact->id).'">'.$langs->trans("Return").'</a>';
				}
			}
			else
				print '<a class="btn btn-primary pull-left disabled" href="#">'.$langs->trans("Return").'</a>';

			print '<input type="submit" class="btn btn-primary margin" value="'.$langs->trans("Update").'"></center>';
			print '</div>';
			print '</form>';
		}
	}
}

print '</div>'; //modal-content
print '</div>'; //modal-dialog
print '</div>'; //modal-dialog
print '</div>';
//print '</div>';

?>