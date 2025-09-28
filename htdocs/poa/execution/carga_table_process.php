<?php
//idTable recarga

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';

$langs->load("poa@poa");

  //definimos fecha para sacar un tipo de formulario
  //hasta el 31/08/215  $lForm = true;
  //desde el 1/09/2015 $lForm = false;
$aDatea = dol_getdate(dol_now());
$aDateact = dol_mktime(23, 59, 59, $aDatea['mon'],$aDatea['mday'],$aDatea['year']);

$aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
if ($aDateact > $aDatelim)
  $lForm = false;
else
 $lForm = true;

$form = new Form($db);
$fk_type_con = GETPOST('opcion');
print '<div id="idTable">';
include DOL_DOCUMENT_ROOT.'/poa/process/tpl/fiche_process_type.tpl.php';
print '</div>';
?>