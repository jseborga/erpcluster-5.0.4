<?php
  //Machinery
dol_include_once('/categories/class/categorie.class.php');
dol_include_once('/priceunits/supplies/class/supplies.class.php');
dol_include_once('/priceunits/unit/class/units.class.php');
dol_include_once('/priceunits/class/html.formv.class.php');

$objcat = new Categorie($db);
$objsup = new Supplies($db);
$objunit = new Units($db);
$form = new Form($db);
$formv = new Formv($db);

//recuperamos que se definio
$idCat = $conf->global->PRICEUNITS_MACHINERY_DEF;
$result=$objcat->fetch($idCat);

$prods = $objcat->getObjectsInCateg("product");

$res = $objsup->getlist($id);

//print_barre_liste($langs->trans("ListePAC"), $page, "items.php", "", $sortfield, $sortorder,'',$num);

print '<table class="noborder">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Type"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "","","","");
print_liste_field_titre($langs->trans("Unit"),"", "","","","");
print_liste_field_titre($langs->trans("Company"),"", "",'','','');
print_liste_field_titre($langs->trans("Rendimiento"),"", "",'','','');
print_liste_field_titre($langs->trans("Price"),"", "",'','','');
print_liste_field_titre($langs->trans("Action"),"", "",'','','');
print "</tr>\n";


if ($user->rights->priceunits->sup->crear)
  {
    include_once DOL_DOCUMENT_ROOT.'/priceunits/supplies/tpl/add.tpl.php';
  }
if ($res > 0 && count($objsup->array)>0)
  {
    foreach ($objsup->array AS $k => $objs)
      {
	print '<tr>';
	print '<td>';
	print '';
	print '</td>';
	print '</tr>';
      }
  }

print '</table>';

?>