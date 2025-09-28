<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2014 Ramiro Queso        <ramiroques@gmail.com>
 *
 */

/**
 *      \file       htdocs/contratadd/tpl/view.tpl.php
 *      \ingroup    
 *      \brief      add adendas al contrato 
 */

print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
If ($action == 'create')
print '<input type="hidden" name="action" value="add">';
If ($action == 'edit')
{
  print '<input type="hidden" name="action" value="update">';
  print '<input type="hidden" name="idr" value="'.$object->id.'">';
}
print '<input type="hidden" name="id" value="'.$id.'">';


print "<tr $bc[$var]>";
print '<td>';
print '(PROV)';
print '<input type="hidden" name="ref" value="0">';
print '</td>';

print '<td>';
$form->select_date($object->date_contrat,'dc_','','','',"date",1,1);
print '</td>';

print '<td>';
print '<input type="number" class="numbertime" name="time_limit" value="'.$object->time_limit.'">';
print '</td>';

print '<td>';
print select_type_limit($object->type_time_limit,'type_time_limit','',1,0);
print '</td>';

print '<td>';
print '<input class="numberamount" type="number" step="any" name="amount" value="'.$object->amount.'">';
print '</td>';

print '<td>';
print '<input type="text" name="note_public" value="'.$object->note_public.'" size="30">';
print '</td>';

print '<td>';
print '<input type="text" name="note_private" value="'.$object->note_private.'" size="30">';
print '</td>';

print '<td>&nbsp;</td>';

print '<td>';
print '<center><input type="submit" class="button" value="'.(!$idr?$langs->trans("Create"):$langs->trans('Save')).'"></center>';
print '</td>';
print '</tr>';

print '</form>';
?>