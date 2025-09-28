<?php
print '<table class="liste_titre" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Documents"),"", "","","",'align="center"');
//print_liste_field_titre($langs->trans("Checklist"),"", "","","",'align="center"');
print '</tr>';
print '<tr>';
print '<td>';
if ($action != 'createdoc')
  {
    print '<form name="form_check" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="addcheck">';
    print '<input type="hidden" name="id" value="'.$object->id.'">';
    print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
  }

print '<table class="liste_titre" width="100%">';
print '<tr class="liste_titre">';
$typeprocess = select_tables($fk_type_con,'fk_tables','',0,1,"05",0);
print_liste_field_titre($typeprocess,"", "","","",'align="center"');
if ($action!='createdoc')
  print_liste_field_titre($langs->trans("Checklist"),"", "","","",'align="center" colspan="2"');
print_liste_field_titre($langs->trans("Actions"),"", "","","",'align="center"');
print '</tr>';
if ($action!='createdoc')
  {

    print '<tr class="liste_titre">';
    print_liste_field_titre("","", "","","",'align="center"');
    print_liste_field_titre($langs->trans("Yes"),"", "","","",'align="center"');
    print_liste_field_titre($langs->trans("Not"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Actions"),"", "","","",'align="center"');
print '</tr>';
  }

//registro nuevo documento
if ($user->rights->poa->doc->crear && $action=='createdoc')
  include_once DOL_DOCUMENT_ROOT.'/poa/activity/tpl/formdoc.tpl.php';
//lista los documentos por tipo
$objdoc->getlist($fk_type_con);
$var = true;
foreach ((array) $objdoc->array AS $j1 => $objd)
{
  $checkedy = '';
  $checkedn = '';
  //buscamos en tabla llx_poa_activity_checklist;
  $objectc->fetch_code($object->id,$objd->code);
  if ($objectc->fk_activity == $object->id && $objectc->code == $objd->code)
    if ($objectc->checklist == 1)
      $checkedy= 'checked';
    else
      $checkedn= 'checked';
      
  $var = !$var;
  print "<tr $bc[$var]>";
  print '<td>'.select_typeprocedure($objd->code,'code_procedure','',0,1,'code','label').'</td>';
  if ($action != 'createdoc')
    {
      print '<td align="center">'.'<input type="radio" '.$checkedy.' name="checklist['.$objd->code.']" value="1">'.'</td>';
      print '<td align="center">'.'<input type="radio" '.$checkedn.' name="checklist['.$objd->code.']" value="0">'.'</td>';
    }
  print '<td>&nbsp;</td>';  
  print '</tr>';
}
print '</table>';
print "<div class=\"tabsActionleft\">\n";

if ($action == '')
  {
    if ($user->rights->poa->doc->crear)
      print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createdoc&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Createdocument").'</a>';
    else
      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createdocument")."</a>";
  }
print '</div>';
print "<div class=\"tabsActionright\">\n";

if ($action == '')
  {
    if ($user->rights->poa->act->addc)
          print '<input type="submit" class="button" value="'.$langs->trans("Savechecklist").'">';
  }
print '</div>';

print '</form>';
print '</td>';

print '</tr>';
print '</table>';


?>