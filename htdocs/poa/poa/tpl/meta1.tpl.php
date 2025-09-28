<?php
	//meta
// $res_1 = True;
// $fk_struc = $obj->fk_structure;
// while ($res_1)
//   {
//     $aStructure[$objstr->aList[$fk_struc]['pos']] = $fk_struc;
//     if ($objstr->aList[$fk_struc]['father'] != -1)
//       {
// 	$fk_struc = $objstr->aList[$fk_struc]['father'];
//       }
//     else
//       {
// 	$res_1 = False;
//       }
//   }
// //$obj->fk_structure;
// $lLoop = count($objstr->aList);
// print_r($objstr->aList);
// foreach ((array) $objstr->aList AS $i1 => $aF1)
// {
//   foreach ((array) $aF1 AS $i2 => $f2)
//     $aStructure[$f2] = $i2;
// }
// echo '<pre>';
// print_r($aStructure);
// echo '</pre>';
//primer nivel
  //$obstr = $objstr->aList[$aStructure[1]]['obj'];
$newClaseorm = $newClase;
  //habilitamos el fondo para diferenciar metas
$backg = "backgroundmeta";
$newClase = ' class="left '.$backg.' ';

$objstr->fetch($fk_structure);
if ($objstr->id == $fk_structure)
	$obstr = $objstr;
// print_r($obstr);
print '<div class="height36 rmeta">';
// //Estado
print '<div id="estado" '.$newClase.'">&nbsp;</div>';
// //structure
print '<div id="meta" '.$newClase.'">'.'<a class="textwhite" href="#" title="'.$obstr->label.'">'.$obstr->sigla.'</a>'.'</div>';
//label
if ($numCol[1])
{
	print '<div id="label" '.$newClase.'">';
	print '<a class="textwhite" href="#" title="'.STRTOUPPER($obstr->pseudonym).'">';
	print (strlen(trim($obstr->label))>50?substr(STRTOUPPER($obstr->label),0,48).'..':STRTOUPPER($obstr->label));
	print '</a>';
	print '</div>';
}
//pseudonym
if ($numCol[2])
{
	print '<div id="pseudo" '.$newClase.'">';
	$idTagps = $obj->id+100000;
	$idTagps2 = $idTagps+100500;
	if ($user->rights->poa->poa->mod || $user->admin)
	{
		print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_poaa" type="text" name="pseudonym" value="'.$obstr->pseudonym.'" onblur="CambiarURLFrametwo('.$obstr->id.','.$idTagps.','.'this.value);" size="36">'.'</span>';
		
		print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps.' , '.$idTagps2.')">';
		print (strlen(trim($obstr->pseudonym))>60?'<a class="textwhite" href="#" title="'.$obstr->label.'">'.substr(trim($obstr->pseudonym),0,60).'..xx.</a>':'<a href="#" title="'.$obstr->label.'">'.trim($obstr->pseudonym).'</a>');
// 	print '</span>';
	}
	else
	{
		print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
		print (strlen(trim($obstr->pseudonym))>60?'<a class="textwhite" href="#" title="'.$obstr->label.'">'.substr(trim($obstr->pseudonym),0,60).'..xx.</a>':'<a href="#" title="'.$obstr->label.'">'.trim($obstr->pseudonym).'</a>');		    
		print '</span>';
	}
	print '</div>';
}
//partida
print '<div id="partida" '.$newClase.'">&nbsp;</div>';
// }
?>