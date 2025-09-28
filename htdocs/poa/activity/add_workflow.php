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
 *	\file       htdocs/poa/activity/add_workflow.php
 *	\ingroup    activity workflow
 *	\brief      Page fiche poa actualizacion activity worklow
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivityworkflow.class.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id           = GETPOST('id');
$idr          = GETPOST('idr');
$followup     = GETPOST('followup');
$followto     = GETPOST('followto');
$mesg = '';
$object  = new Poaactivityworkflow($db);

/*
 * Actions
 */
//refo
if ($action == 'add' && $user->rights->poa->act->adds)
  {
    
    //buscando
    $object->fk_activity = $id;
    $object->date_tracking = dol_now();
    $object->followup = GETPOST('followup');
    $object->fk_user_create = $user->id;
    $object->date_create = dol_now();
    $object->tms = dol_now();
    $object->statut = 1;
    if (empty(trim($object->followup)))
      {
	$error++;
      }
    if (empty($error))
      {
	$object->create($user);
      }
  }

if ($action == 'update' && $user->rights->poa->act->adds)
  {
    if ($object->fetch($idr)>0)
      {
	//buscando
	$object->followto = GETPOST('followto');
	$object->tms = dol_now();
	if (empty($object->followto))
	  {
	    $error++;
	  }
	if (empty($error))
	  {
	    $object->update($user);
	  }
      }
  }

llxHeaderVierge($langs->trans("Update"),1);

print llxFooterVierge();


/**
 * Show header for new member
 *
 * @param 	string		$title				Title
 * @param 	string		$head				Head array
 * @param 	int    		$disablejs			More content into html header
 * @param 	int    		$disablehead		More content into html header
 * @param 	array  		$arrayofjs			Array of complementary js files
 * @param 	array  		$arrayofcss			Array of complementary css files
 * @return	void
 */
function llxHeaderVierge($title, $head="", $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='')
{
    global $user, $conf, $langs, $mysoc;
    top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss); // Show html headers
    print '<body id="mainbody" class="publicnewmemberform" style="margin-top: 10px;">';
    print '<div style="margin-left: 50px; margin-right: 50px;">';
}

/**
 * Show footer for new member
 *
 * @return	void
 */
function llxFooterVierge()
{
    print '</div>';

    printCommonFooter('public');

    print "</body>\n";
    print "</html>\n";
}

?>
