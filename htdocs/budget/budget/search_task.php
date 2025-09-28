<?php
require ("../../main.inc.php");

dol_include_once('/budget/class/budgettask.class.php');
dol_include_once('/budget/lib/budget.lib.php');

$objectdet = new Budgettask($db);

$action = GETPOST('action');
$id 	= GETPOST('id');
$fk_task_parent = GETPOST('idreg');
$url    = $_SESSION['url'];
include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/task.tpl.php';

?>