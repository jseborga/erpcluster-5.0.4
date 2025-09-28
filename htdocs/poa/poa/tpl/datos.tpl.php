<?php
  //vistas r1
  //require("../../../main.inc.php");
$form = new Form($db);
print '<form method="POST" action="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1'.'" id="fo8" name="fo8">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="menu3">';
print '<input type="hidden" name="r1" value="3">';

//columna 1
print '<div class="left">';//div col1

print '<div class="left">';
print '<input type="text" name="search_all" value="'.$_SESSION['filtersearchpoa']['search_all'].'" size="39" placeholder="'.$langs->trans('Search').'" class="searchBox">';
print '</div>';

print '<div class="clear"></div>';
print '<div class="left">';
$aExcluded = array(1=>1);
print $form->select_dolusers($_SESSION['filtersearchpoa']['search_login'],'search_login',1,$aExcluded,'','','','',15);
print '</div>';

print '<div class="clear"></div>';
print '<div class="left">';
$aPriority = array(-1 => $langs->trans('Allpriority'),0=>'No definido',1=>1,2=>2,3=>3,4=>4,5=>5,6=>6);
print $form->selectarray('search_priority',$aPriority,$_SESSION['filtersearchpoa']['search_priority']);
print '</div>';

print '</div>';//fin div col1

//columna 2
print '<div class="left">';//div col2

print '<div class="left">';
print '<input type="text" name="gestion" value="'.$_SESSION['gestion'].'" size="15" placeholder="'.$langs->trans('Gestion').'" class="searchCalendar">';
print '</div>';

print '</div>';//fin div col2

//columna3
print '<div class="left">';//div col3

print '<div class="left">';
print '<input type="submit" class="button" name="'.$langs->trans('Accept').'" value="'.$langs->trans('Accept').'" />';
print '<input type="submit" class="button" name="'.$langs->trans('Cancel').'" value="'.$langs->trans('Tocancel').'" />';
print '</div>';

print '</div>';//fin div col3

print '</form>';
//print '/n';

?>