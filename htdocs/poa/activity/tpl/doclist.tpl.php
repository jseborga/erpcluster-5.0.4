<?php
print '<br>';
print '<table class="liste_titre" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Detail"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Document"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
print '</tr>';
print '<br>';
$objdoca->getlist($object->id);
$objectw = new Poaactivitydoc($db);
if (count($objdoca->array) > 0)
  {
    $var = true;
    foreach ($objdoca->array AS $j => $objectw_)
      {		  
	if ($action == 'editdoc' && $objectw_->id == $idr)
	  {
	    //buscamos rwvisar
	    $objectw->fetch($idr);
	    $objectw_ = $objectw;
	    include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/adddoc.tpl.php';
	  }
	else
	  {
	    $var=!$var;
	    print "<tr $bc[$var]>";
	    print '<td align="left">';
	    print $objectw_->detail;
	    print '</td>';
	    print '<td>';
	    $aFile = explode('.',$objectw_->name_doc);
	    $filext = '';
	    $nLoop = count($aFile)-1;
	    $fileext = STRTOUPPER($aFile[$nLoop]);
	    if ($fileext == 'DOC' || $fileext == 'DOCX')
	      $fext = 'doc.png';
	    if ($fileext == 'XLS' || $fileext == 'XLSX')
	      $fext = 'xls.png';
	    if ($fileext == 'PDF')
	      $fext = 'pdf.png';
	    
	    print '<a href="'.DOL_URL_ROOT.'/documents/poa/activity/doc/'.$objectw_->name_doc.'">'.img_picto($langs->trans('Opendoc'),DOL_URL_ROOT.'/poa/img/'.$fext.'','',1).' '.$objectw_->name_doc.'</a>';
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