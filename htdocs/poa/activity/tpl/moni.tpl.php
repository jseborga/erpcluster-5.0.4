<?php
print '<table class="table table-hover table-condensed datatable" role="grid">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Date"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Followup"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Followto"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
print '</tr>';
//registro nuevo
if ($object->statut == 1 && $user->rights->poa->act->addm && $action!='editmon')
  include_once DOL_DOCUMENT_ROOT.'/poa/activity/tpl/formw.tpl.php';

//definimos array para saldos
$aPrev = array();
$aValidate = array();
$objectw->getlist($object->id);

if (count($objectw->array) > 0)
  {
    $var = true;
    foreach ($objectw->array AS $j => $objectw_)
      {
	if ($action == 'editmon' && $objectw_->id == $idr)
	  {
	    //buscamos rwvisar
	    $objectw->fetch($idr);
	    $objectw_ = $objectw;
	    include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/formw.tpl.php';

	  }
	else
	  {
	    $var=!$var;
	    print "<tr $bc[$var]>";
	    //poa
	    //date
	    print '<td align="center">';
	    print dol_print_date($objectw_->date_tracking,'day');
	    print '</td>';
	    print '<td>';
	    print $objectw_->followup;
	    print '</td>';
	    print '<td>';
	    print $objectw_->followto;
	    print '</td>';
	    //action
	    print '<td align="center">';
	    if ($object->statut == 1 && $user->rights->poa->act->modm)
	      print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectw_->id.'&action=editmon&dol_hide_leftmenu=1'.'">'.img_picto($langs->trans('Edit'),'edit').'</a>';
	    print '&nbsp;&nbsp;';
	    if ($object->statut == 0 && $user->rights->poa->act->delm)
	      print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectd_->id.'&action=deletemon&dol_hide_leftmenu=1'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
	    print '</td>';

	    print '</tr>';
	  }
      }
  }
print "</table>";

?>