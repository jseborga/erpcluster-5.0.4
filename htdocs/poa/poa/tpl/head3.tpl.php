<?php
//estado
print '<tr>';
print '<th>';
print '</th>';
//structure
print '<th><input type="text" class="flat" size="3" name="search_sigla" value="'.$search_sigla.'"></th>';
if ($numCol[1])
  print '<th><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="37"></th>';
if ($numCol[2])
  print '<th><input type="text" class="flat" name="search_pseudonym" value="'.$search_pseudonym.'" size="37"></th>';

print '<th>';
print '<input class="flat" type="text" size="3" name="search_partida" value="'.$search_partida.'">';
print '</th>';

//presupuesto
print '<th></th>';
if ($numCol[71])//preventivo
  {
    print '<th>1</th>';
    print '<th></th>';
    print '<th>';
    print '<input class="flat" type="text" size="8" name="search_reform" value="'.$search_reform.'">';
    print '</th>';
    //print '</div>';
  }
if ($numCol[72])//comprometido
  {
    print '<th>1</th>';
    print '<th>';
    print '</th>';
    //print '</div>';
  }
if ($numCol[73])
  {
    print '<th>1</th>';
    print '<th>';
    print '</th>';
    //print '</div>';
  }
if ($numCol[9] ||$numCol[10] || $numCol[15])
  print '<th></th>';
if ($numCol[11] ||$numCol[12] || $numCol[16])
  print '<th></th>';
if ($numCol[13] ||$numCol[14] || $numCol[17])
  print '<th></th>';
// if ($opver != 1)
//   print '<div id="amount_t" class="left title"></div>';
// print '<div id="amount" class="left title"></div>';
if ($opver == 1)
  {
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
    print '<th></th>';
  }

//user
print '<th>';
print '<input class="flat" type="text" size="7" name="search_user" value="'.$search_user.'">';
// $aExcluded = array(1=>1);
// print $form->select_dolusers($search_user,'search_user',1,$aExcluded,'','','','',9);

//    print $form->selectarray('search_user',$aUser);
print '</th>';

//seguimiento
if ($numCol[321])
  print '<th></th>';
if ($numCol[322])
  print '<th></th>';
if ($numCol[323])
  print '<th></th>';

// //instruction
// if ($conf->poai->enabled)
//   if ($numCol[93])
// 	print '<div id="instruction" class="left title"></div>';
//pac
//print '<div id="pac" class="left title"></div>';
//pac

//action
$nAction = 3;
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/action.tpl.php';
// print '<div id="action_" class="left title">';
// print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
// print '&nbsp;';
// print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
// print '</div>';

$parameters=array();
$formconfirm=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
//print '<div class="clear"></div>';
print '</tr>';

?>