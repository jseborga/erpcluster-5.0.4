<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville	        <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur          <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio          <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier               <benoit.mortier@opensides.be>
 * Copyright (C) 2004      Andre Cianfarani             <acianfa@free.fr>
 * Copyright (C) 2005-2012 Regis Houssin                <regis.houssin@capnetworks.com>
 * Copyright (C) 2008 	   Raphael Bertrand (Resultic)  <raphael.bertrand@resultic.fr>
 * Copyright (C) 2011-2012 Juanjo Menent			    <jmenent@2byte.es>
 * Copyright (C) 2011 	   Philippe Grand			    <philippe.grand@atoo-net.com>
 * Copyright (C) 2013 	   Ramiro Queso			    <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/almacen/admin/almacen.php
 *	\ingroup    Almacen
 *	\brief      Setup page of module Almacen Pedidos
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacen.class.php';

$langs->load("admin");
$langs->load("errors");
$langs->load("orders");
$langs->load('other');
$langs->load('almacen@almacen');

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

	$commande = new Solalmacen($db);
	$commande->initAsSpecimen();

	// Search template files
	$file=''; $classname=''; $filefound=0;
	$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);
	foreach($dirmodels as $reldir)
	{
		$file=dol_buildpath($reldir."almacen/core/modules/doc/pdf_".$modele.".modules.php",0);
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
if ($action == 'set')
{
	$label = GETPOST('label','alpha');
	$scandir = GETPOST('scandir','alpha');

	$res = dolibarr_set_const($db,"ALMACEN_FOUNDING_SOURCE",GETPOST('ALMACEN_FOUNDING_SOURCE','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_FILTER_YEAR",GETPOST('ALMACEN_FILTER_YEAR','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_METHOD_VALUATION_INVENTORY",GETPOST('ALMACEN_METHOD_VALUATION_INVENTORY','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT",GETPOST('ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT','int'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_CODE_DEFAULT_STORE",GETPOST('ALMACEN_CODE_DEFAULT_STORE','alpha'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_MOUVEMENT_INPUT_DISPLAY_DEPARTAMENT",GETPOST('ALMACEN_MOUVEMENT_INPUT_DISPLAY_DEPARTAMENT','alpha'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_MOUVEMENT_INPUT_DISPLAY_SOCIETE",GETPOST('ALMACEN_MOUVEMENT_INPUT_DISPLAY_SOCIETE','alpha'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_INTEGER_WITH_POA",GETPOST('ALMACEN_INTEGER_WITH_POA','int'),'yesno',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_CHANGE_UFV",GETPOST('ALMACEN_CHANGE_UFV','alpha'),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_CLOSE_PERIOD",GETPOST('ALMACEN_CLOSE_PERIOD','int'),'int',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_ALERT_DAY_NUMBER_CLOSE_PERIOD",GETPOST('ALMACEN_ALERT_DAY_NUMBER_CLOSE_PERIOD','int'),'int',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_NUMBER_DECIMAL_PRODUCT_BALANCE",GETPOST('ALMACEN_NUMBER_DECIMAL_PRODUCT_BALANCE','int'),'int',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"ALMACEN_ACTIVE_BUTTON_WITHOUT_EXISTENCE",GETPOST('ALMACEN_ACTIVE_BUTTON_WITHOUT_EXISTENCE','int'),'yesno',0,'',$conf->entity);
	if (! $res > 0) $error++;


	//$type='almacen';
	//$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
	//$sql.= " VALUES ('".$db->escape($value)."','".$type."',".$conf->entity.", ";
	//$sql.= ($label?"'".$db->escape($label)."'":'null').", ";
	//$sql.= (! empty($scandir)?"'".$db->escape($scandir)."'":"null");
	//$sql.= ")";
	//i/f ($db->query($sql))
	//{

	//}
}

if ($action == 'del')
{
	$type='almacen';
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql.= " WHERE nom = '".$db->escape($value)."'";
	$sql.= " AND type = '".$type."'";
	$sql.= " AND entity = ".$conf->entity;

	if ($db->query($sql))
	{
		if ($conf->global->ALMACEN_ADDON_PDF == "$value") dolibarr_del_const($db, 'ALMACEN_ADDON_PDF',$conf->entity);
	}
}

if ($action == 'setdoc')
{
	$label = GETPOST('label','alpha');
	$scandir = GETPOST('scandir','alpha');

	$db->begin();

	if (dolibarr_set_const($db, "ALMACEN_ADDON_PDF",$value,'chaine',0,'',$conf->entity))
	{
		$conf->global->ALMACEN_ADDON_PDF = $value;
	}

	// On active le modele
	$type='almacen';

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

	dolibarr_set_const($db, "ALMACEN_ADDON",$value,'chaine',0,'',$conf->entity);
}

if ($action == 'set_ALMACEN_DRAFT_WATERMARK')
{
	$draft = GETPOST("ALMACEN_DRAFT_WATERMARK");
	$res = dolibarr_set_const($db, "ALMACEN_DRAFT_WATERMARK",trim($draft),'chaine',0,'',$conf->entity);

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
if ($action == 'set_SOL_ALMACEN_WITHOUTSTOCK_WATERMARK')
{
	$draft = GETPOST("ALMACEN_WITHOUTSTOCK_WATERMARK");
	$res = dolibarr_set_const($db, "ALMACEN_WITHOUTSTOCK_WATERMARK",trim($draft),'chaine',0,'',$conf->entity);

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
if ($action == 'set_ALMACEN_FREE_TEXT')
{
	$freetext = GETPOST("ALMACEN_FREE_TEXT");	// No alpha here, we want exact string

	$res = dolibarr_set_const($db, "ALMACEN_FREE_TEXT",$freetext,'chaine',0,'',$conf->entity);

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

llxHeader("",$langs->trans("AlmacenSetup"));

$form=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("AlmacenSetup"),$linkback,'setup');
print '<br>';


// Mode
$var=true;
$aMethod = array(0=>$langs->trans('PPP'),1=>$langs->trans('PEPS'),2=>$langs->trans('UEPS'));
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td><td>'.$langs->trans("Value").'</td>';
print "</tr>\n";


$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Filterbyaccountingmanagement").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_FILTER_YEAR',$conf->global->ALMACEN_FILTER_YEAR,1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("InventoryValuationMethod").'</td>';
print '<td width="60" align="center">';
print $form->selectarray('ALMACEN_METHOD_VALUATION_INVENTORY',$aMethod,$conf->global->ALMACEN_METHOD_VALUATION_INVENTORY);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Ininputmovementsentertotalvaluesbyproduct").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT',$conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT,1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Defaultdeliverystore").'. '.$langs->trans('Register reference').'</td>';
print '<td width="60" align="center">';
print '<input type="text" name="ALMACEN_CODE_DEFAULT_STORE" value="'.$conf->global->ALMACEN_CODE_DEFAULT_STORE.'" size="15">';
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("InputmovementsshowdepartmentsorExecutingUnits").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_MOUVEMENT_INPUT_DISPLAY_DEPARTAMENT',$conf->global->ALMACEN_MOUVEMENT_INPUT_DISPLAY_DEPARTAMENT
	,1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Ininputmovementsshowsuppliers").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_MOUVEMENT_INPUT_DISPLAY_SOCIETE',$conf->global->ALMACEN_MOUVEMENT_INPUT_DISPLAY_SOCIETE,1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("AlmacenintegerwithmodulePOA").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_INTEGER_WITH_POA',$conf->global->ALMACEN_INTEGER_WITH_POA,1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Intheinputmovementsincludesourceoffinancing").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_FOUNDING_SOURCE',$conf->global->ALMACEN_FOUNDING_SOURCE,1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Currencytouseforvalueupdate").'</td>';
print '<td width="60" align="center">';
print '<input type="text" name="ALMACEN_CHANGE_UFV" value="'.$conf->global->ALMACEN_CHANGE_UFV.'" size="15">';
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Closingstoreforvalueupdate").'</td>';
$aData = array(1=>$langs->trans('Mensual'),9=>$langs->trans('Anual'));
print '<td width="60" align="center">';
print $form->selectarray('ALMACEN_CLOSE_PERIOD',$aData,($conf->global->ALMACEN_CLOSE_PERIOD?$conf->global->ALMACEN_CLOSE_PERIOD:9),1);
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Numberofdaystoalertperiodclosing").'</td>';
print '<td width="60" align="center">';
print '<input type="number" min="1" max="28" name="ALMACEN_ALERT_DAY_NUMBER_CLOSE_PERIOD" value="'.$conf->global->ALMACEN_ALERT_DAY_NUMBER_CLOSE_PERIOD.'">';
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Numberofdecimalstobestoredinproductbalance").'</td>';
print '<td width="60" align="center">';
print '<input type="number" min="1" max="9" name="ALMACEN_NUMBER_DECIMAL_PRODUCT_BALANCE" value="'.$conf->global->ALMACEN_NUMBER_DECIMAL_PRODUCT_BALANCE.'">';
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("Activatebuttonwithoutexistenceinwarehouseorder").'</td>';
print '<td width="60" align="center">';
print $form->selectyesno('ALMACEN_ACTIVE_BUTTON_WITHOUT_EXISTENCE',$conf->global->ALMACEN_ACTIVE_BUTTON_WITHOUT_EXISTENCE,1);
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

print_titre($langs->trans("WarehouseNumberingModules"));
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
	$dir = dol_buildpath($reldir."almacen/core/modules/");
	if (is_dir($dir))
	{
		$handle = opendir($dir);
		if (is_resource($handle))
		{
			$var=true;

			while (($file = readdir($handle))!==false)
			{
				if (substr($file, 0, 12) == 'mod_almacen_' && substr($file, dol_strlen($file)-3, 3) == 'php')
				{
					$file = substr($file, 0, dol_strlen($file)-4);

					require_once DOL_DOCUMENT_ROOT ."/almacen/core/modules/".$file.'.php';

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
						if ($conf->global->ALMACEN_ADDON == $file)
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


/*
 * Document templates generators
 */
print_titre($langs->trans("Documentos para almacenes"));

// Load array def with activated templates
$type='almacen';
$def = array();
$sql = "SELECT nom";
$sql.= " FROM ".MAIN_DB_PREFIX."document_model";
$sql.= " WHERE type = '".$type."'";
$sql.= " AND entity = ".$conf->entity;
$resql=$db->query($sql);
if ($resql)
{
	$i = 0;
	$num_rows=$db->num_rows($resql);
	while ($i < $num_rows)
	{
		$array = $db->fetch_array($resql);
		array_push($def, $array[0]);
		$i++;
	}
}
else
{
	dol_print_error($db);
}


print "<table class=\"noborder\" width=\"100%\">\n";
print "<tr class=\"liste_titre\">\n";
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status")."</td>\n";
print '<td align="center" width="60">'.$langs->trans("Default")."</td>\n";
print '<td align="center" width="38" colspan="2">'.$langs->trans("Infos").'</td>';
print "</tr>\n";

clearstatcache();

$var=true;
foreach ($dirmodels as $reldir)
{
	foreach (array('','/doc') as $valdir)
	{
		$dir = dol_buildpath($reldir."almacen/core/modules".$valdir);

		if (is_dir($dir))
		{
			$handle=opendir($dir);
			if (is_resource($handle))
			{
				while (($file = readdir($handle))!==false)
				{
					$filelist[]=$file;
				}
				closedir($handle);
				arsort($filelist);
				foreach($filelist as $file)
				{
					if (preg_match('/\.modules\.php$/i',$file) && preg_match('/^(pdf_|doc_)/',$file))
					{
						if (file_exists($dir.'/'.$file))
						{
							$name = substr($file, 4, dol_strlen($file) -16);
							$classname = substr($file, 0, dol_strlen($file) -12);

							require_once $dir.'/'.$file;
							$module = new $classname($db);

							$modulequalified=1;
							if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) $modulequalified=0;
							if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) $modulequalified=0;

							if ($modulequalified)
							{
								$var = !$var;
								print '<tr '.$bc[$var].'><td width="100">';
								print (empty($module->name)?$name:$module->name);
								print "</td><td>\n";
								if (method_exists($module,'info')) print $module->info($langs);
								else print $module->description;
								print '</td>';

				  // Active
								if (in_array($name, $def))
								{
									print '<td align="center">'."\n";
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=del&value='.$name.'">';
									print img_picto($langs->trans("Enabled"),'switch_on');
									print '</a>';
									print '</td>';
								}
								else
								{
									print '<td align="center">'."\n";
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&value='.$name.'&amp;scandir='.$module->scandir.'&amp;label='.urlencode($module->name).'">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
									print "</td>";
								}

				  // Defaut
								print '<td align="center">';
								if ($conf->global->ALMACEN_ADDON_PDF == $name)
								{
									print img_picto($langs->trans("Default"),'on');
								}
								else
								{
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&value='.$name.'&amp;scandir='.$module->scandir.'&amp;label='.urlencode($module->name).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
								}
								print '</td>';

				  // Info
								$htmltooltip =    ''.$langs->trans("Name").': '.$module->name;
								$htmltooltip.='<br>'.$langs->trans("Type").': '.($module->type?$module->type:$langs->trans("Unknown"));
								if ($module->type == 'pdf')
								{
									$htmltooltip.='<br>'.$langs->trans("Width").'/'.$langs->trans("Height").': '.$module->page_largeur.'/'.$module->page_hauteur;
								}
								$htmltooltip.='<br><br><u>'.$langs->trans("FeaturesSupported").':</u>';
								$htmltooltip.='<br>'.$langs->trans("Logo").': '.yn($module->option_logo,1,1);
								$htmltooltip.='<br>'.$langs->trans("PaymentMode").': '.yn($module->option_modereg,1,1);
								$htmltooltip.='<br>'.$langs->trans("PaymentConditions").': '.yn($module->option_condreg,1,1);
								$htmltooltip.='<br>'.$langs->trans("MultiLanguage").': '.yn($module->option_multilang,1,1);
						//$htmltooltip.='<br>'.$langs->trans("Discounts").': '.yn($module->option_escompte,1,1);
						//$htmltooltip.='<br>'.$langs->trans("CreditNote").': '.yn($module->option_credit_note,1,1);
								$htmltooltip.='<br>'.$langs->trans("WatermarkOnDraftOrders").': '.yn($module->option_draft_watermark,1,1);


								print '<td align="center">';
								print $form->textwithpicto('',$htmltooltip,1,0);
								print '</td>';

						// Preview
								print '<td align="center">';
								if ($module->type == 'pdf')
								{
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=specimen&module='.$name.'">'.img_object($langs->trans("Preview"),'bill').'</a>';
								}
								else
								{
									print img_object($langs->trans("PreviewNotAvailable"),'generic');
								}
								print '</td>';

								print "</tr>\n";
							}
						}
					}
				}
			}
		}
	}
}

print '</table>';

//Autres Options
print "<br>";
print_titre($langs->trans("OtherOptions"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td align="center" width="60">'.$langs->trans("Value").'</td>';
print "<td>&nbsp;</td>\n";
print "</tr>\n";
$var=true;

$var=! $var;
// print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
// print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
// print '<input type="hidden" name="action" value="set_COMMANDE_FREE_TEXT">';
// print '<tr '.$bc[$var].'><td colspan="2">';
// print $langs->trans("FreeLegalTextOnOrders").' ('.$langs->trans("AddCRIfTooLong").')<br>';
// print '<textarea name="COMMANDE_FREE_TEXT" class="flat" cols="120">'.$conf->global->COMMANDE_FREE_TEXT.'</textarea>';
// print '</td><td align="right">';
// print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
// print "</td></tr>\n";
// print '</form>';

//Use draft Watermark
$var=!$var;
print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print "<input type=\"hidden\" name=\"action\" value=\"set_SOL_ALMACEN_WITHOUTSTOCK_WATERMARK\">";
print '<tr '.$bc[$var].'><td colspan="2">';
print $langs->trans("Watermark without stock").'<br>';
print '<input size="50" class="flat" type="text" name="ALMACEN_WITHOUTSTOCK_WATERMARK" value="'.$conf->global->ALMACEN_WITHOUTSTOCK_WATERMARK.'">';
print '</td><td align="right">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print "</td></tr>\n";
print '</form>';

print '</table>';

print '<br>';

dol_htmloutput_mesg($mesg);

llxFooter();

$db->close();
?>
