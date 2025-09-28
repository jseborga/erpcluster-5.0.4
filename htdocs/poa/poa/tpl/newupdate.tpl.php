<?php
if ($action == 'create' || $action == 'edit')
  {
    $newClase = ' class="left '.$backg.' ';
    
    print '<section id="section-add">';
    print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
    dol_htmloutput_mesg($mesg);
    print '<table class="table table-bordered table-hover dataTable" aria-describedby="data1_info">';
    print '<thead>';
    //init head 1 buttons
    include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/head1n.tpl.php';
    print '</thead>';
    print "<tr>";
    print '<td>';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    if ($action == 'create')
      print '<input type="hidden" name="action" value="add">';
    if ($action == 'edit')
      {
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$id.'">';
      }
    print '<input type="hidden" name="gestion" value="'.$gestion.'">';
    
    print $objstr->select_structure($object->fk_structure,'fk_structure','',1,1);
    print '</td>';
    
    print '<td>';
    print '<input type="text" name="label" value="'.$object->label.'" >';
    print '</td>';
    
    print '<td>';
    print '<input type="text" name="pseudonym" value="'.$object->pseudonym.'">';
    print '</td>';
    
    print '<td>';
    print '<input type="text" name="partida" value="'.$object->partida.'" size="4">';
    print '</td>';
    
    print '<td>';
    print '<input type="text" name="amount" value="'.$object->amount.'" size="9">';
    print '</td>';
    
    print '<td>';
    print '<input type="text" name="version" value="'.$object->version.'" size="9">';
    print '</td>';
    
    print '<td>';
    print '<button class="btn_trans" title="'.$langs->trans('Save').'" type="submit" name="save">'.img_picto($langs->trans('Save'),DOL_URL_ROOT.'/poa/img/save.png','',1).'</button>';
    print '<button class="btn_trans" title="'.$langs->trans('Cancel').'" type="submit" name="cancel" value="'.$langs->trans('Cancel').'">'.img_picto($langs->trans('Cancel'),'off').'</button>';
    
    print '</td>';
    
    print '</tr>';
    print '</table>';
    print '</form>';
    print '</section>';
  }

?>