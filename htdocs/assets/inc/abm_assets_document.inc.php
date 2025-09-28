<?<?php

/*
 * Actions
 */

$parameters=array('id'=>$id);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//Delete line if product propal merge is linked to a file
	if (!empty($conf->global->ASSETS_PDF_MERGE_PROPAL))
	{
		if ($action == 'confirm_deletefile' && $confirm == 'yes')
		{
			//extract file name
			$urlfile = GETPOST('urlfile', 'alpha');
			$filename = basename($urlfile);
			$filetomerge = new Propalmergepdfproduct($db);
			$filetomerge->fk_product=$object->id;
			$filetomerge->file_name=$filename;
			$result=$filetomerge->delete_by_file($user);
			if ($result<0) {
				setEventMessages($filetomerge->error, $filetomerge->errors, 'errors');
			}
		}
	}
	// Action submit/delete file/link
	include_once DOL_DOCUMENT_ROOT.'/core/actions_linkedfiles.inc.php';
}

if ($action=='filemerge')
{
	$is_refresh = GETPOST('refresh');
	if (empty($is_refresh)) {

		$filetomerge_file_array = GETPOST('filetoadd');

		$filetomerge_file_array = GETPOST('filetoadd');

		if ($conf->global->MAIN_MULTILANGS) {
			$lang_id = GETPOST('lang_id');
		}

		// Delete all file already associated
		$filetomerge = new Propalmergepdfproduct($db);

		if ($conf->global->MAIN_MULTILANGS) {
			$result=$filetomerge->delete_by_product($user, $object->id, $lang_id);
		} else {
			$result=$filetomerge->delete_by_product($user, $object->id);
		}
		if ($result<0) {
			setEventMessages($filetomerge->error, $filetomerge->errors, 'errors');
		}

		// for each file checked add it to the product
		if (is_array($filetomerge_file_array)) {
			foreach ( $filetomerge_file_array as $filetomerge_file ) {
				$filetomerge->fk_product = $object->id;
				$filetomerge->file_name = $filetomerge_file;

				if ($conf->global->MAIN_MULTILANGS) {
					$filetomerge->lang = $lang_id;
				}

				$result=$filetomerge->create($user);
				if ($result<0) {
					setEventMessages($filetomerge->error, $filetomerge->errors, 'errors');
				}
			}
		}
	}
}
?>