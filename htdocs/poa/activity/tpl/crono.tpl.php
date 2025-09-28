<?php

require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydet.class.php';
$objectd = new poaactivitydet($db);
	  print '<table class="liste_titre" width="100%">';
	  print '<tr class="liste_titre">';
	  print_liste_field_titre($langs->trans("Procedure"),"", "","","",'align="center"');
	  print_liste_field_titre($langs->trans("Date"),"", "","","",'align="center"');
	  print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	  print '</tr>';
	  //registro nuevo
	  if ($objact->statut == 0 && $user->rights->poa->act->adds && $action!='editpro')
	    include_once DOL_DOCUMENT_ROOT.'/poa/activity/tpl/form.tpl.php';

	  //definimos array para saldos
	  $aPrev = array();
	  $aValidate = array();
	  $objectd->getlist($objact->id);

	  if (count($objectd->array) > 0)
	    {
	      $var = true;
	      foreach ($objectd->array AS $j => $objectd_)
		{
		  if ($action == 'editpro' && $objectd_->id == $idr)
		    {
		      //buscamos rwvisar
		      $objectd->fetch($idr);
		      $objectd_ = $objectd;
		      include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/form.tpl.php';

		    }
		  else
		    {
		      $var=!$var;
		      print "<tr $bc[$var]>";
		      //poa
		      print '<td>';
		      print select_typeprocedure($objectd_->code_procedure,'','',0,1,'code');
		      print '</td>';
		      //date
		      print '<td align="center">';
		      print dol_print_date($objectd_->date_procedure,'day');
		      print '</td>';
		      //action
		      print '<td align="center">';
		      if ($object->statut == 0 && $user->rights->poa->act->mods)
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectd_->id.'&action=editpro&dol_hide_leftmenu=1'.'">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		      print '&nbsp;&nbsp;';
		      if ($object->statut == 0 && $user->rights->poa->act->dels)
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectd_->id.'&action=deletepro&dol_hide_leftmenu=1'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		      print '</td>';

		      print '</tr>';
		    }
		}
	    }
	  print "</table>";

?>