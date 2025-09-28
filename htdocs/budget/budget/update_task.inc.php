<?php 
$res = '';
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

dol_include_once('/budget/class/budgettask.class.php');
$objectdet = new Budgettask($db);

$action = GETPOST('action');
$id 	= GETPOST('id');
$column = GETPOST('column');
$value  = GETPOST('newValue');
$fk_task_parent = GETPOST('idreg');
$url    = $_SESSION['url'];


$data  = explode("-",$_POST['id']);
$campo = $data[0]; // nombre del campo
$id    = $data[1]; // id del registro
$value = $_POST['value']; // valor por el cual reemplazar

$sql = " UPDATE ".MAIN_DB_PREFIX."budget_task_resource SET $campo = '$value' WHERE rowid = $id";
$resql = $db->query($sql);
$response['success'] = $resql;
$response['value'] = $value;

if ($resql) echo "<span class='ok'>Valores modificados correctamente.</span>";
		else echo "<span class='ko'>".$db->error."</span>";
//echo json_encode($resonse);
?>