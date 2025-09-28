<?php
//estado
print '<tr>';

//structure
print '<th>';
print $langs->trans("Meta");
print '</th>';

    print '<th>';
    print $langs->trans("Label");
    print '</th>';
    print '<th>';
    print $langs->trans("Pseudonym");
    print '</th>';
//partida
print '<th>';
print $langs->trans('Partida');
print '</th>';

//presupuesto
print '<th>';//presup
if ($numCol[91])
{
  print $langs->trans('Initialbudget');
}
if ($numCol[92])      
{
  print $langs->trans('Approved budget');
}

//botones calendario
//7 total presup
if ($numCol[7])
{
  print $langs->trans('A1');
}
if ($numCol[8])
{
  print $langs->trans('A2');
}
print '</th>';//presup fin

//reformulacion y total aprobado

if ($numCol[71])
{
    print '<th class="left title yellowblack">';
    if ($lVersion)
    {
      print $langs->trans('Reformulated').' '.$nVersion;
    }
    else
    {
      print $langs->trans('Reformulated').' '.$nVersion;
    }
    print '</th>';	
    if ($lVersion)
    {
      print '<th class="left yellowblack">';
      print $langs->trans('N. Reform');
      print '</th>';
    }
  else
    {
      print '<th class="left yellowblack">';
      print $langs->trans('N. Reform');
      print '</th>';
    }

}
if ($numCol[72])
{
    print '<th>';
    if ($numCol[7]==true)
    {
      print $langs->trans("Pending approval").' '.$nVersion;
    }
    else
    {
      print $langs->trans("Approved budget").' '.$nVersion;
    }
    print '</th>';
}
if ($numCol[73])
  {
    print '<th>';
    print $langs->trans("Porcent");
    print '</th>';
  }

print '<th>';
print $langs->trans("Version");
print '</th>';
print '<th>';
print $langs->trans("Action");
print '</th>';
print '</tr>';

?>