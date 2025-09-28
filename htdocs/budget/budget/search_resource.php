<?php
require ("../../main.inc.php");
dol_include_once('/budget/class/budgettaskresource.class.php');
dol_include_once('/budget/class/pustructure.class.php');

dol_include_once('/budget/lib/budget.lib.php');
dol_include_once('/budget/class/html.formv.class.php');

$objectdetr = new Budgettaskresource($db);
$pustr = new Pustructure($db);
$form = new Formv($db);
$action = GETPOST('action');
$id 	= GETPOST('id');
$fk_task_parent = GETPOST('idreg');
$url    = $_SESSION['url'];

include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/list_resource.tpl.php';

?>