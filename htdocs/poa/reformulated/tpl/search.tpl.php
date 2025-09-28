<?php
  //buscador de poa
print '<form name="form_of" action="fiche.php" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="'.$searchpoa.'">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="idof" value="'.$idof.'">';

print '<tr>';
// search
print '<td colspan="5">';
print '<input id="search" type="text" value="'.$search.'" name="search" size="50" maxlength="255">';
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Search').'" src="'.DOL_URL_ROOT.'/poa/img/search.png" width="14" height="14">';
print '</td>';
print '</tr>';
print '</form>';
if (!empty($search))
  {
    $objpoas = new Poapoa($db);
    $objpoa->search($search);
    foreach((array) $objpoa->array AS $j => $k)
      {
	$objpoas->fetch($j);
	$objstr->fetch($objpoas->fk_structure);
	print '<tr>';
	print '<td>';
	print '<a href="fiche.php?action='.$selpoades.'&id='.$id.'&idof='.$idof.'&idpsearch='.$j.'">'.$objstr->sigla.'</a>';
	print '</td>';
	print '<td>';
	print $objpoas->label;
	print '</td>';
	print '<td>';
	print $objpoas->partida;
	print '</td>';
	print '</tr>';
      }
  }

?>