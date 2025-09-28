<?php
  //vistas r1
  //require("../../../main.inc.php");

print '<div class="left">';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu2&r1=1'.'"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/standard2.png','',1).'<br><label>'.$langs->trans('Standard').'</label></div></a>';
print '</div>';

print '<div class="left">';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu2&r1=2'.'"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/seguimiento.png','',1).'<br><label>'.$langs->trans('Follow').'</label></div></a>';
print '</div>';

print '<div class="left">';
print '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&action=menu2&r1=3'.'"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/name.png','',1).'<br><label>'.$langs->trans('Names').'</label></div></a>';
print '</div>';
if ($user->rights->poa->str->leer)
  {
    print '<div class="left">';
    print '<a href="'.DOL_URL_ROOT.'/poa/structure/liste.php?dol_hide_leftmenu=1&action=menu2&r1=4'.'"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/structure.png','',1).'<br><label>'.$langs->trans('Structure').'</label></div></a>';
    print '</div>';
  }
if ($user->rights->poa->pac->leer)
  {
    print '<div class="left">';
    print '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?dol_hide_leftmenu=1&r1=5'.'"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/vistapac.png','',1).'<br><label>'.$langs->trans('PAC').'</label></div></a>';
    print '</div>';
  }
if ($user->rights->poa->area->leer)
  {
    print '<div class="left">';
    print '<a href="'.DOL_URL_ROOT.'/poa/area/liste.php?dol_hide_leftmenu=1&r1=5'.'"><div class="tabmenu">'.img_picto('',DOL_URL_ROOT.'/poa/img/area.png','',1).'<br><label>'.$langs->trans('Area').'</label></div></a>';
    print '</div>';
  }
?>