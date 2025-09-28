<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/poa/appoint/fiche.php
 *  \ingroup    Guarantees
 *  \brief      Page fiche POA guarantees
 */

if ($action == 'create' && $user->rights->poa->appoint->crear)
{
	print_fiche_titre($langs->trans("Newappoint"));

	print '<form class="form-inline" action="'.DOL_URL_ROOT.'/poa/appoint/fiche.php'.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="fk_contrat" value="'.GETPOST('idc').'">';
	print '<input type="hidden" name="lastlink" value="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'">';

	dol_htmloutput_mesg($mesg);

	//type appoint
	print '<div class="form-group">';
	print '<label class="sr-only" for="code_appoint">'.$langs->trans('Company').'</label>';
	$objcont->fetch($idc);
	print $aContratname[$i].' - '.$aSocname[$objcont->fk_soc];
	print '</div>';

	//type appoint
	print '<div class="form-group">';
	print '<label class="sr-only" for="code_appoint">'.$langs->trans('Appointtype').'</label>';
	print select_code_appoint($object->code_appoint,'code_appoint','',1,0);
	print '</div>';

	//user
	print '<div class="form-group">';
	print '<label class="sr-only" for="fk_user">'.$langs->trans('User').'</label>';
	print $formadd->select_use((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user','',1,0,0);
	print '</div>';

	//user replace
	print '<div class="form-group">';
	print '<label class="sr-only" for="fk_user_replace">'.$langs->trans('Replacea').'</label>';
	print $formadd->select_use($object->fk_user_replace,'fk_user_replace','',1,0,0);
	print '</div>';

	//dateappoint
	print '<div class="form-group">';
	print '<label class="sr-only" for="di_">'.$langs->trans('Date').'</label>';
	$formadd->select_dateadd($object->date_appoint,'di_','','','',"date",1,1);
	print '</div>';


	//print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'">';
	if ($user->rights->poa->prev->leer)
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'">'.$langs->trans("Return").'</a>';
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
	print '</center>';
	print '</form>';
}
?>
