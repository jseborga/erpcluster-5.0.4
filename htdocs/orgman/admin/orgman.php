<?php
/* Copyright (C) 2018      Ramiro Queso             <ramiroques@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *  \file       htdocs/almacen/admin/almacen.php
 *  \ingroup    Almacen
 *  \brief      Setup page of module Almacen Pedidos
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


$langs->load('orgman');
$langs->load("admin");
$langs->load("errors");
$langs->load('other');

if (! $user->admin) accessforbidden();

$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');

/*
 * Actions
 */

if ($action == 'updateMask')
{
    $maskconstorder=GETPOST('maskconstorder','alpha');
    $maskorder=GETPOST('maskorder','alpha');

    if ($maskconstorder) $res = dolibarr_set_const($db,$maskconstorder,$maskorder,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}

if ($action == 'specimen')
{
    $modele=GETPOST('module','alpha');

    $commande = new Poapoa($db);
    $commande->initAsSpecimen();

    // Search template files
    $file=''; $classname=''; $filefound=0;
    $dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);
    foreach($dirmodels as $reldir)
    {
        $file=dol_buildpath($reldir."poa/core/modules/doc/pdf_".$modele.".modules.php",0);
        if (file_exists($file))
        {
            $filefound=1;
            $classname = "pdf_".$modele;
            break;
        }
    }

    if ($filefound)
    {
        require_once $file;

        $module = new $classname($db);

        if ($module->write_file($commande,$langs) > 0)
        {
            header("Location: ".DOL_URL_ROOT."/document.php?modulepart=almacen&file=SPECIMEN.pdf");
            return;
        }
        else
        {
            $mesg='<font class="error">'.$module->error.'</font>';
            dol_syslog($module->error, LOG_ERR);
        }
    }
    else
    {
        $mesg='<font class="error">'.$langs->trans("ErrorModuleNotFound").'</font>';
        dol_syslog($langs->trans("ErrorModuleNotFound"), LOG_ERR);
    }
}
if ($action == 'setother')
{
    $type='poa';
}
if ($action == 'set')
{
    $label = GETPOST('label','alpha');
    $scandir = GETPOST('scandir','alpha');

    $res = dolibarr_set_const($db,"ORGMAN_NUMBER_FAILED_LOGIN",GETPOST('ORGMAN_NUMBER_FAILED_LOGIN','int'),'chaine',0,'',$conf->entity);
    if (! $res > 0) $error++;

}
if ($action == 'del')
{
    $type='orgman';
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
    $sql.= " WHERE nom = '".$db->escape($value)."'";
    $sql.= " AND type = '".$type."'";
    $sql.= " AND entity = ".$conf->entity;

    if ($db->query($sql))
    {
        if ($conf->global->ORGMAN_ADDON_PDF == "$value") dolibarr_del_const($db, 'ORGMAN_ADDON_PDF',$conf->entity);
    }
}

if ($action == 'setdoc')
{
    $label = GETPOST('label','alpha');
    $scandir = GETPOST('scandir','alpha');

    $db->begin();

    if (dolibarr_set_const($db, "ORGMAN_ADDON_PDF",$value,'chaine',0,'',$conf->entity))
    {
        $conf->global->ORGMAN_ADDON_PDF = $value;
    }

    // On active le modele
    $type='orgman';

    $sql_del = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
    $sql_del.= " WHERE nom = '".$db->escape($value)."'";
    $sql_del.= " AND type = '".$type."'";
    $sql_del.= " AND entity = ".$conf->entity;
    dol_syslog("Delete from model table ".$sql_del);
    $result1=$db->query($sql_del);

    $sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
    $sql.= " VALUES ('".$value."', '".$type."', ".$conf->entity.", ";
    $sql.= ($label?"'".$db->escape($label)."'":'null').", ";
    $sql.= (! empty($scandir)?"'".$scandir."'":"null");
    $sql.= ")";
    dol_syslog("Insert into model table ".$sql);
    $result2=$db->query($sql);
    if ($result1 && $result2)
    {
        $db->commit();
    }
    else
    {
        dol_syslog("Error ".$db->lasterror(), LOG_ERR);
        $db->rollback();
    }
}

if ($action == 'setmod')
{
  // TODO Verifier si module numerotation choisi peut etre active
  // par appel methode canBeActivated

    dolibarr_set_const($db, "ORGMAN_ADDON",$value,'chaine',0,'',$conf->entity);
}

if ($action == 'set_ORGMAN_DRAFT_WATERMARK')
{
    $draft = GETPOST("ORGMAN_DRAFT_WATERMARK");
    $res = dolibarr_set_const($db, "ORGMAN_DRAFT_WATERMARK",trim($draft),'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}

if ($action == 'set_ORGMAN_FREE_TEXT')
{
    $freetext = GETPOST("ORGMAN_FREE_TEXT"); // No alpha here, we want exact string

    $res = dolibarr_set_const($db, "ORGMAN_FREE_TEXT",$freetext,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}


/*
 * View
 */

$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);

llxHeader("",$langs->trans("BudgetgobSegup"));

$form=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("PoaSetup"),$linkback,'setup');
print '<br>';




// Mode other
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td><td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Numberofaccessrepetitionstoblockuser").'</td>';
print '<td width="60" align="center">';
print '<input type="text" size="7" maxlength="6" name="ORGMAN_NUMBER_FAILED_LOGIN" value="'.$conf->global->ORGMAN_NUMBER_FAILED_LOGIN.'" >';
print "</td>";
print "</tr>";

print '</table>';
print '<br>';

print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

print "</form>\n";
dol_htmloutput_mesg($mesg);



/*
 * Numbering module
 */

print_titre($langs->trans("OrgmanNumberingModules"));
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap="nowrap">'.$langs->trans("Example").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="16">'.$langs->trans("Infos").'</td>';
print '</tr>'."\n";

clearstatcache();

foreach ($dirmodels as $reldir)
{
    $dir = dol_buildpath($reldir."orgman/core/modules/");
    if (is_dir($dir))
    {
        $handle = opendir($dir);
        if (is_resource($handle))
        {
            $var=true;

            while (($file = readdir($handle))!==false)
            {
                if (substr($file, 0, 8) == 'mod_poa_' && substr($file, dol_strlen($file)-3, 3) == 'php')
                {
                    $file = substr($file, 0, dol_strlen($file)-4);

                    require_once DOL_DOCUMENT_ROOT ."/budgetgob/core/modules/".$file.'.php';

                    $module = new $file;
              // Show modules according to features level
                    if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
                    if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;

                    if ($module->isEnabled())
                    {
                        $var=!$var;
                        print '<tr '.$bc[$var].'><td>'.$module->nom."</td><td>\n";
                        print $module->info();
                        print '</td>';

              // Show example of numbering module
                        print '<td nowrap="nowrap">';
                        $tmp=$module->getExample();
                        if (preg_match('/^Error/',$tmp)) print '<div class="error">'.$langs->trans($tmp).'</div>';
                        elseif ($tmp=='NotConfigured') print $langs->trans($tmp);
                        else print $tmp;
                        print '</td>'."\n";

                        print '<td align="center">';
                        if ($conf->global->ORGMAN_ADDON == $file)
                        {
                            print img_picto($langs->trans("Activated"),'switch_on');
                        }
                        else
                        {
                            print '<a href="'.$_SERVER["PHP_SELF"].'?action=setmod&amp;value='.$file.'">';
                            print img_picto($langs->trans("Disabled"),'switch_off');
                            print '</a>';
                        }
                        print '</td>';

                        $commande=new Solalmacen($db);
                        $commande->initAsSpecimen();

                        // Info
                        $htmltooltip='';
                        $htmltooltip.=''.$langs->trans("Version").': <b>'.$module->getVersion().'</b><br>';
                        $commande->type=0;
                        $nextval=$module->getNextValue($mysoc,$commande);
                        if ("$nextval" != $langs->trans("NotAvailable"))
                        // Keep " on nextval
                        {
                            $htmltooltip.=''.$langs->trans("NextValue").': ';
                            if ($nextval)
                            {
                                $htmltooltip.=$nextval.'<br>';
                            }
                            else
                            {
                                $htmltooltip.=$langs->trans($module->error).'<br>';
                            }
                        }

                        print '<td align="center">';
                        print $form->textwithpicto('',$htmltooltip,1,0);
                        print '</td>';

                        print '</tr>';
                    }
                }
            }
            closedir($handle);
        }
    }
}

print '</table><br>';





dol_htmloutput_mesg($mesg);

llxFooter();

$db->close();
?>
