<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Ramiro Queso        <ramiroques@gmail.com>
 *
 */

/**
 *      \file       htdocs/contratadd/tpl/view.tpl.php
 *      \ingroup    
 *      \brief      Lista las adendas al contrato 
 */

print "<tr $bc[$var]>";
if ($objview->statut == 0 && $user->rights->addendum->crear)
	print '<td><a href="liste.php?id='.$id.'&idr='.$objview->id.'&action=edit">'.img_object($langs->trans("Addendum"),'contract').' '.$objview->ref.'</a></td>';
else
	print '<td><a href="'.DOL_URL_ROOT.'/contrat/'.$cFile.'?id='.$objview->id.'">'.img_object($langs->trans("Addendum"),'contract').' '.$objview->ref.'</a></td>';

print '<td>'.dol_print_date($objview->date_contrat,'day').'</td>';
print '<td align="center">'.$objview->array_options['options_plazo'].'</td>';
if ($objview->statut == 1)
	$sumaPlazo+=$objview->array_options['options_plazo'];
print '<td align="center">'.select_type_limit($objview->array_options['options_cod_plazo'],'type_time_limit','',1,1).'</td>';
$objec_  = new Addendum($db);
$objec_->get_suma_contratdet($objview->id);
print '<td align="right">'.price($objec_->total_ht).'</td>';
if ($objview->statut == 1)
	$sumaAmount+=$objec_->total_ht;

print '<td>'.$objview->note_public.'</td>';
print '<td>'.$objview->note_private.'</td>';

print '<td>'.$object->LibStatut($objview->statut,1).'</td>';

print '<td align="right">';
if ($objview->statut == 0 && $user->rights->contratadd->del)
	print '<a href="liste.php?id='.$id.'&idr='.$objview->id.'&action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a>';
print '&nbsp;';
if ($objview->statut == 0 && $user->rights->contratadd->crear)
	print '<a href="liste.php?id='.$id.'&idr='.$objview->id.'&action=valid">'.img_object($langs->trans('Valid'),'opensurvey').'</a>';

print '</td>';
print "</tr>\n";

?>