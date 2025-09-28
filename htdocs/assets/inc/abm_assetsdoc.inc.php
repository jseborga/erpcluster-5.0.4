<?php

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objAssetsdoc,$action);    // Note that $action and $objAssetsdoc may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
    if ($cancel)
    {
        if ($action != 'addlink')
        {
            $urltogo=$backtopage?$backtopage:dol_buildpath('/assets/assets/fiche.php?id='.$id,1);
            header("Location: ".$urltogo);
            exit;
        }
        if ($id > 0 || ! empty($ref)) $ret = $objAssetsdoc->fetch($id,$ref);
        $action='';
    }

    // Action to add record
    if ($action == 'adddoc')
    {
        if (GETPOST('cancel'))
        {
            $urltogo=$backtopage?$backtopage:dol_buildpath('/assets/assets/fiche.php?id='.$id,1);
            header("Location: ".$urltogo);
            exit;
        }

        $error=0;

        /* object_prop_getpost_prop */

        $objAssetsdoc->fk_asset=GETPOST('id','int');
        $objAssetsdoc->fk_cassetdoc=GETPOST('fk_cassetdoc','int');
        $objAssetsdoc->label=GETPOST('label','alpha');
        $objAssetsdoc->fk_user_create=$user->id;
        $objAssetsdoc->fk_user_mod=$user->id;
        $objAssetsdoc->dater=$dater;
        $objAssetsdoc->datec=$now;
        $objAssetsdoc->datem=$now;
        $objAssetsdoc->status=0;


        if ($objAssetsdoc->fk_cassetdoc<=0)
        {
            $error++;
            setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_assetdoc")), null, 'errors');
        }

        if (! $error)
        {
            $result=$objAssetsdoc->create($user);
            if ($result > 0)
            {
                // Creation OK
                $urltogo=$backtopage?$backtopage:dol_buildpath('/assets/assets/fiche.php?id='.$id.'&idr='.$result,1);
                header("Location: ".$urltogo);
                exit;
            }
            {
                // Creation KO
                if (! empty($objAssetsdoc->errors)) setEventMessages(null, $objAssetsdoc->errors, 'errors');
                else  setEventMessages($objAssetsdoc->error, null, 'errors');
                $action='createdoc';
            }
        }
        else
        {
            $action='createdoc';
        }
    }

    // Action to update record
    if ($action == 'updatedoc')
    {
        $objAssetsdoc->fetch($idr);

        $error=0;


        $objAssetsdoc->fk_asset=GETPOST('id','int');
        $objAssetsdoc->fk_cassetdoc=GETPOST('fk_cassetdoc','int');
        $objAssetsdoc->label=GETPOST('label','alpha');
        $objAssetsdoc->fk_user_mod=$user->id;
        $objAssetsdoc->dater=$dater;
        $objAssetsdoc->datem=$now;

        if ($objAssetsdoc->fk_cassetdoc<=0)
        {
            $error++;
            setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_assetdoc")), null, 'errors');
        }

        if (! $error)
        {
            $result=$objAssetsdoc->update($user);
            if ($result > 0)
            {
                $action='view';
            }
            else
            {
                // Creation KO
                if (! empty($objAssetsdoc->errors)) setEventMessages(null, $objAssetsdoc->errors, 'errors');
                else setEventMessages($objAssetsdoc->error, null, 'errors');
                $action='editdoc';
            }
        }
        else
        {
            $action='editdoc';
        }
    }

    // Action to delete
    if ($action == 'confirm_deletedoc')
    {
        $objAssetsdoc->fetch($idr);
        $result=$objAssetsdoc->delete($user);
        if ($result > 0)
        {
            // Delete OK
            setEventMessages("RecordDeleted", null, 'mesgs');
            header("Location: ".dol_buildpath('/assets/assets/fiche.php?id='.$id,1));
            exit;
        }
        else
        {
            if (! empty($objAssetsdoc->errors)) setEventMessages(null, $objAssetsdoc->errors, 'errors');
            else setEventMessages($objAssetsdoc->error, null, 'errors');
        }
    }
}


?>