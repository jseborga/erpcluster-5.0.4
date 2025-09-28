<?php
include '../../main.inc.php';

$id 	= GETPOST('id');
$idsel 	= GETPOST('idsel');
$total 	= GETPOST('total');
$_SESSION['upsel'][$id][$idsel]= $total;

?>