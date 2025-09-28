<?php
  //inicio
  //require("../../../main.inc.php");
$filtromenu = $_SESSION['filtermenupoa'];

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f11=1'.'" title="'.$langs->trans('Meta').'" ><div class="'.($filtromenu['f11']?'tabmenumark':'tabmenu').'">'.$langs->trans('Meta').'</div></a>';
print '</li>';

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f1=1'.'" title="'.$langs->trans('Insumos').'"><div class="'.($filtromenu['f1']?'tabmenumark':'tabmenu').'">'.$langs->trans('Insumos').'</div></a>';
print '</li>';

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f2=1'.'"><div class="'.($filtromenu['f2']?'tabmenumark':'tabmenu').'">'.$langs->trans('Activities').'</div></a>';
print '</li>';

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f3=1'.'" title="'.$langs->trans('Finished').'"><div class="'.($filtromenu['f3']?'tabmenumark':'tabmenu').'">'.$langs->trans('Finished').'</div></a>';
print '</li>';

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f4=1'.'" title="'.$langs->trans('Inprogress').'"><div class="'.($filtromenu['f4']?'tabmenumark':'tabmenu').'">'.$langs->trans('Inprogress').'</div></a>';
print '</li>';

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f5=1'.'" title="'.$langs->trans('Delay').'"><div class="'.($filtromenu['f5']?'tabmenumark':'tabmenu').'">'.$langs->trans('Delay').'</div></a>';
print '</li>';

print '<li>';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu1&f6=1'.'" title="'.$langs->trans('Notimetable').'"><div class="'.($filtromenu['f6']?'tabmenumark':'tabmenu').'">'.$langs->trans('Notimetable').'</div></a>';
print '</li>';

// print '<div class="left">';
// print '<a href="'.DOL_URL_ROOT.'/user/logout.php"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/logout.png','',1).'</div></a>';
// print '</div>';

?>