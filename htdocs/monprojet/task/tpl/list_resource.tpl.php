<?php
	//mostramos el resumen de los insumos
	//recuperamos de
$aDatamat = unserialize($_SESSION['aDatamat']);
dol_fiche_head();
print '<table class="table border centpercent">'."\n";

print '<thead>';
print '<tr>';
print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldtyperesource'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);

//if ($type_resource != 'MAT' && $type_resource != 'MAQ')
//    print_liste_field_titre($langs->trans('Resources'),$_SERVER['PHP_SELF'],'','',$params,'colspan="2"',$sortfield,$sortorder);
//else
//{
print_liste_field_titre($langs->trans('Resources'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldfk_unit'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
//}
print_liste_field_titre($langs->trans('Fieldquant'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);

print '</tr>';
print '</thead>';

if ($action != 'editres' && empty(GETPOST('idreg'))  && $projectstatic->statut == 1 && $object->fk_statut < 2)
	include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/add_resource.tpl.php';

foreach((array) $aStrgroupcat AS $group => $fk_categorie)
{
	$categorie->fetch($fk_categorie);
	$opt = $linestr->fk_categorie;
	$code_structure = $fk_categorie;
	$fk_task_parent = $id;
	$loop++;
	include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/resource.tpl.php';
}
print '</table>';
dol_fiche_end();

?>