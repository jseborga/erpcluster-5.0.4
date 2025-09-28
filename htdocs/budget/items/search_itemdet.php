<?php
require ("../../main.inc.php");

dol_include_once('/budget/class/itemsdet.class.php');
dol_include_once('/budget/class/pustructure.class.php');
dol_include_once('/budget/class/html.formv.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/budget/lib/budget.lib.php');


$objectdet = new Itemsdet($db);
$pustr = new Pustructure($db);

$form=new Formv($db);

$action = GETPOST('action');
$id 	= GETPOST('id');
$fk_pu_structure = GETPOST('idreg');
$ref    = GETPOST('ref');
$tag    = GETPOST('tag');
$url    = $_SESSION['url'];
if (!empty($tag)) $tag = substr($tag,4,20);
$aStrref = unserialize($_SESSION['aStrref']);
$aStrlabel = unserialize($_SESSION['aStrlabel']);

$pustr->fetch($fk_pu_structure);

include DOL_DOCUMENT_ROOT.'/budget/items/tpl/items.tpl.php';

?>