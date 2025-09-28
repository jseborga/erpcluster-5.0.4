<?php
//por nro
$aArray[$nro]['rowid'] = $row->id;
$aArray[$nro]['dateo'] = $row->dateo;
$aArray[$nro]['datee'] = $row->datee;
//por task
$aArrayt[$row->id]['nro'] = $row->id;
$aArrayt[$row->id]['dateo'] = $row->dateo;
$aArrayt[$row->id]['datee'] = $row->datee;

if ($lGroup)
	$htmlGroup.= '<tr class="trmark">';
else
	$htmlGroup.= "<tr $bc[$var]>";

$rowjs.= '$("#dur_'.$row->id.'").blur(function() {
					processline(this,'.$row->id.');
				});
			$("#suc_'.$row->id.'").blur(function() {
					processline(this,'.$row->id.');
				});
			$("#pre_'.$row->id.'").blur(function() {
					processline(this,'.$row->id.');
				});
				';

$htmlGroup.= '<td align="center">'.$nro.'</td>';
$htmlGroup.= '<td>'.$htmltabs.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idg='.$row->id.'&action=viewit">'.$row->ref.'</a></td>';
$htmlGroup.= '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idg='.$row->id.'&action=viewit">'.$row->label.'</a>'.'</td>';
$objectdettmp->fetch($row->fk_task_parent);
$htmlGroup.= '<td>'.($objectdettmp->id == $row->fk_task_parent?$objectdettmp->label:'').'</td>';
if ($user->rights->budget->cale->crear || $user->rights->budget->cale->mod)
{
	$htmlGroup.= '<td align="right">'.'<input id="dur_'.$row->id.'" type="number" class="len60" step="any" min="0" name="duration['.$row->id.']" value="'.$duration.'">'.'</td>';
	$htmlGroup.= '<td align="right">'.'<input id="suc_'.$row->id.'" type="text" name="successor['.$row->id.']" value="'.$successor.'" disabled size="5">'.'</td>';
	$htmlGroup.= '<td align="right">'.'<input id="pre_'.$row->id.'" type="text" name="predecessor['.$row->id.']" value="'.$predecessor.'" size="5">'.'</td>';
}
else
{
	$htmlGroup.= '<td align="right">time'.'</td>';
	$htmlGroup.= '<td align="right">suc'.'</td>';
	$htmlGroup.= '<td align="right">pred'.'</td>';
}
$htmlGroup.= '<td align="center">'.'<input type="text" id="di_'.$row->id.'" value="'.dol_print_date($row->dateo,'day').'" disabled size="7"></td>';
$htmlGroup.= '<td align="center">'.'<input type="text" id="df_'.$row->id.'" value="'.dol_print_date($row->datee,'day').'" disabled size="7"></td>';

$htmlGroup.= '<td align="right">';
if ($user->rights->budget->budm->mod && $object->fk_statut == 0)
	$htmlGroup.= '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$row->id.'&action=editgroup">'.img_picto($langs->trans('Edit'),'edit').'</a>';
if ($user->rights->budget->budm->del && $object->fk_statut == 0)
	$htmlGroup.= '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$row->id.'&action=deletegroup">'.img_picto($langs->trans('Delete'),'delete').'</a>';

$htmlGroup.= '</td>';
$htmlGroup.= '</tr>';

?>