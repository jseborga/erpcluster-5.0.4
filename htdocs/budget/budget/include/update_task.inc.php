<?php 
require ("../../main.inc.php");
dol_include_once('/budget/class/budgettask.class.php');

$objectdet = new Budgettask($db);

$action = GETPOST('action');
$id 	= GETPOST('id');
$column = GETPOST('column');
$value  = GETPOST('newValue');
$fk_task_parent = GETPOST('idreg');
$url    = $_SESSION['url'];

$sql = " UPDATE ".MAIN_DB_PREFIX."budget_task SET $column = '$value' WHERE rowid = $id";
$resql = $db->query($sql);
$response['success'] = $resql;
$response['value'] = $value;

echo json_encode($resonse);
?>