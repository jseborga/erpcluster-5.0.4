<?php
if ($user->rights->monprojet->book->leer)
{

	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/orderbook.class.php';
	$objbook = new Orderbook($db);
	if ($action == 'sbook')
	{
		$objbook->fetch(GETPOST('idr'));
		$_SESSION['aPost'] = $_POST;
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idc='.$idc.'&idr='.GETPOST('idr','int'),
			$langs->trans("Approveorderbook"),
			$langs->trans("Confirmapproverecordorderbook",$object->ref).': '.$objbook->detail,
			"confirm_sbook",
			'',
			0,2);

		if ($ret == 'html') print '<br>';
	}

		//filtramos las tareas del contrato

	$filter = array(1=>1);
	$filterstatic = " AND fk_contrat = ".$idc;
	$numtask = $objbook->fetchAll('ASC', 't.ref', 0, 0,$filter, 'AND',$filterstatic);
	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */
	if ($action == 'obook')
	{
		print "<div class=\"tabsAction\">\n";

		if ($user->rights->monprojet->cont->crear)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createbook&id='.$id.'&idc='.$idc.'">'.$langs->trans("Newinstruction").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Newinstruction")."</a>";
		print "</div>";
	}
		//listamos
	dol_fiche_head();
	print_fiche_titre($langs->trans("Orderbook"));
	//recibimos si es dependiente de
	$idp = GETPOST('idp','int');
	if ($action == 'createbook' || $action == 'bedit')
	{
		print '<form enctype="multipart/form-data" name="orderbook" method="POST" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
		if ($action == 'createbook')
			print '<input type="hidden" name="action" value="addbook">';
		if ($action == 'bedit')
		{
			print '<input type="hidden" name="action" value="editbook">';
			print '<input type="hidden" name="idr" value="'.GETPOST('idr','int').'">';		
		}
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="table" value="'.$table.'">';
		print '<input type="hidden" name="seldate" value="'.$seldate.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idc" value="'.$idc.'">';		
		print '<input type="hidden" name="idp" value="'.$idp.'">';		
	}
	print '<table class="noborder centpercent">'."\n";
		// Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Nro.'),$_SERVER['PHP_SELF'],'','',$param,' align="left"');
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$param,' align="left"');
	print_liste_field_titre($langs->trans('User'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Attachment'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
	print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
	print '</tr>';

	if ($action == 'createbook' && empty($idp))
	{
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/addbook.tpl.php';
	}
	foreach ((array) $objbook->lines AS $i => $line)
	{
		$objuser->fetch($line->fk_user_create);
		if ($action == 'bedit' && $line->id == GETPOST('idr','int'))
		{
			$objbook = $line;
			include DOL_DOCUMENT_ROOT.'/monprojet/tpl/addbook.tpl.php';
		}
		else
		{
			$var = !$var;
			print "<tr $b[$var]>";
			print '<td>';
			$parent = '';
			if ($line->fk_parent>0)
			{
				$objbook->fetch($line->fk_parent);
				$parent = $objbook->ref;
			}
			print $line->ref.($parent?' '.$langs->trans('The').' '.$parent:'');
			print '</td>';			
			print '<td>';
			print dol_print_date($line->date_order,'dayhour');
			print '</td>';
			print '<td>';
			print $objuser->login;
			print '</td>';
			print '<td>';
			print $line->detail;
			print '</td>';
			//attachment
			print '<td>';
				if (!empty($line->document))
				{
				//recuperamos los nombres de archivo
					$aDoc = explode(';',$line->document);
					foreach ((array) $aDoc AS $k => $doc)
					{
						$aFile = explode('.',$doc);
						//extension
						$docext = STRTOUPPER($aFile[count($aFile)-1]);
						$typedoc = 'doc';
						if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
							$typedoc = 'fin';
						if ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
							$typedoc = 'doc';
						elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
							$typedoc = 'doc';

						if ($action != 'editlinep')
						{
							//print '&nbsp;'.$mobject->showphoto($typedoc,$task_time,$doc,$object,$projectstatic, 100,$docext);
							$modulepart = 'monprojet';
							$mobject = new Orderbookadd($db);
							$contratadd->fetch($line->fk_contrat);
							print '&nbsp;'.$mobject->showphoto($typedoc,$doc,$contratadd,$modulepart, $line,$projectstatic, 100, 0, 0, 'photowithmargin', 'small', 1, 0,$docext);
							if ($user->admin || ($user->rights->monprojet->bookimg->del && $objbook->statut == 0))
								print '&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$projectstatic->id.'&linedoc='.$line->id.'&idc='.$line->fk_contrat.'&namedoc='.$doc.'&action=deldoc'.'">'.img_picto($langs->trans('Deleteattachment'),'edit_remove').'</a>';

						}
					}
					if ($action != 'editlinep')
					{
				//revisar permiso
						if ($lregtask)
							print '&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$task_time->rowid.'&action=editlinep'.'">'.img_picto($langs->trans('Newdoc'),'edit_add').'</a>';
					}
				}
				else
				{
					print '&nbsp;';
					if ($action != 'editlinep')
					{
						print '&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$task_time->rowid.'&action=editlinep'.'">'.img_picto($langs->trans('Newdoc'),'edit_add').'</a>';
					}
				}
			//para subir nuevo archivo
				if ($action == 'editlinep'  && $_GET['lineid'] == $task_time->rowid)
				{
				//	print '<label class="cabinet">';

				//	include DOL_DOCUMENT_ROOT.'/monprojet/tpl/adddoc.tpl.php';
				//	print '</label>';
				}
			print '</td>';
			print '<td align="right">';
			print $objbook->libStatut($line->statut,0);
			print '</td>';
			print '<td align="right">';
			if ($user->id == $line->fk_user_create || $user->admin)
			{
				if ($line->statut == 0)
				{
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$idc.'&idr='.$line->id.'&action=bedit">'.img_picto($langs->trans('Edit'),'edit').'</a>';
					print '&nbsp;';				
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$idc.'&idr='.$line->id.'&action=sbook">'.img_picto($langs->trans('Enable'),'switch_off').'</a>';
				}
				else
				{
					print img_picto($langs->trans('Accepted'),'switch_on');
					if ($user->rights->monprojet->book->crear && $action != 'createbook')
					{
						print '&nbsp;';
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$idc.'&idp='.$line->id.'&action=createbook">'.img_picto($langs->trans('Answer'),'rightarrow').'</a>';
					}
				}
			}
			print '</td>';
			print '</tr>';
			if ($action == 'createbook' && $line->id == $idp)
			{
				$objbook = new Orderbook($db);
				include DOL_DOCUMENT_ROOT.'/monprojet/tpl/addbook.tpl.php';
			}
		}
	}
	print '</table>';
	if ($action == 'createbook' || $action == 'bedit')
	{
		print '</from>';
	}
	dol_fiche_end();

	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */

	if ($action == 'obook')
	{
		print "<div class=\"tabsAction\">\n";
		if ($user->rights->monprojet->cont->crear)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createbook&id='.$id.'&idc='.$idc.'">'.$langs->trans("Newinstruction").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Newinstruction")."</a>";
		print "</div>";
	}
}
?>