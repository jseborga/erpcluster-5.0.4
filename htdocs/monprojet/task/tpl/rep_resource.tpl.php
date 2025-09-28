<?php
	//mostramos el resumen de los insumos
	//recuperamos de 
$aDatamat = unserialize($_SESSION['aDatamat']);
print '<table class="table border centpercent">'."\n";

print '<thead>';
print '<tr>';
print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldunit'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldquant'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldamount'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);

print '</tr>';
print '</thead>';

$categorie->fetch($fk_categorie);
$opt = $linestr->fk_categorie;
$code_structure = $fk_categorie;
$aDet = $aGroupdet[$group];
include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/resourceg.tpl.php';
print '</table>';
?>