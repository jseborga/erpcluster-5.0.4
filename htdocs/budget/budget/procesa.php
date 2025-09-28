<?php
require ("../../main.inc.php");

$aPost = $_POST;
$aGet = $_GET;
$valores = '';
$formula = GETPOST('formula');

$formula = str_replace('_mas_', '+', $formula);
$formula = str_replace('_menos_', '-', $formula);

$newFormula = $formula;
foreach ((array) $aPost AS $code => $value)
{
	$newFormula = str_replace($code, $value, $newFormula);

}
eval('$formula_res = '.$newFormula.';');
echo $formula_res;
?>