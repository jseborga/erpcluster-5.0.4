<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/product/index.php
 *  \ingroup    product
 *  \brief      Page accueil des produits et services
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsresource.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestext.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

$objJobs = new Mjobsext($db);
$object = new Mequipmentext($db);

$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');
$action = GETPOST('action');
//integrated_asset($db,$action);


/*PRUEBA CONECCION PDO*/
// $db2 = new PDO('mysql:host=localhost;dbname=ubuntubo_51','root','dsoGmlp123');

// //prueba conexcion pdo informix
// $inf_host = '10.5.11.11';
// $inf_port = '9160';
// $inf_database = 'sap2000';
// $inf_server = 'central1';
// $inf_protocol = 'onsoctcp';
// $inf_login = 'c_sap';
// $inf_passwd = '12345678';
// $dbi = new PDO('informix>host='.$inf_host.'; service='.$inf_port.
// 	       '; database='.$inf_database.'; server='.$inf_server.
// 	       '; protocol='.$inf_protocol,$inf_login,$inf_passwd);

// //prueba de conexion a base de datos informix
// $sql = " SELECT * FROM v_empleado";
// foreach($dbi->query($sql) as $fila) {
//   echo '<pre>';
//   print_r($fila);
//   echo '</pre>';
// }

// Security check
//$result=restrictedArea($user,'contab');

/*prueba conexcion a base de datos mysql*/
// $newhost = 'localhost';
// echo	$conec = 'mysql:host='.$newhost.';dbname=ubuntubo_51';
// //exit;
// $login = 'root';
// $passwd = 'dsoGmlp123');
// $db3  = new PDO($conec,$login,$passwd);
// print_r($db3);
$langs->load("mant");

//action
//
/////
if ($action =='update')
{
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assets.class.php';

	$now = dol_now();
	$objAsset = new Assets($db);
	$filter = " AND t.statut > 0";
	$filter.= " AND t.entity = ".$conf->entity;
	$res = $objAsset->fetchAll('','',0,0,array(),'AND',$filter);
	if ($res > 0)
	{
		$db->begin();
		$lines = $objAsset->lines;
		foreach ($lines AS $j => $line)
		{

			$object->entity=$conf->entity;
			$object->ref=$line->ref;
			$object->ref_ext=$line->ref_ext;
			$object->label=$line->descrip;
			$object->metered=$line->metered;
			if(empty($object->metered)) $object->metered = 0;
			$object->accountant=$line->accountant;
			if(empty($object->accountant)) $object->accountant = 0;
			$object->accountant_last=$line->accountant_last;
			if(empty($object->accountant_last)) $object->accountant_last = 0;
			$object->accountant_mant=$line->accountant_mant;
			if(empty($object->accountant_mant)) $object->accountant_mant = 0;
			$object->accountant_mante=$line->accountant_mante;
			if(empty($object->accountant_mante)) $object->accountant_mante = 0;
			$object->fk_unit=$line->fk_unit;
			if(empty($object->fk_unit)) $object->fk_unit = 0;
			$object->margin=$line->margin;
			if(empty($object->margin)) $object->margin = 0;
			$object->trademark=$line->trademark;
			$object->model=$line->model;
			$object->anio=$line->anio;
			if(empty($object->anio)) $object->anio = 0;
			$object->fk_location=$line->fk_location;
			if(empty($object->fk_location)) $object->fk_location = 0;
			$object->fk_asset=$line->fk_asset;
			if(empty($object->fk_asset)) $object->fk_asset = 0;
			$object->fk_group=$line->fk_group;
			if(empty($object->fk_group)) $object->fk_group = 0;
			$object->hour_cost=$line->hour_cost;
			if(empty($object->hour_cost)) $object->hour_cost = 0;
			$object->fk_equipment_program=$line->fk_equipment_program;
			if(empty($object->fk_equipment_program)) $object->fk_equipment_program = 0;
			$object->code_program=$line->code_program;
			$object->fk_user_create=$user->id;
			$object->fk_user_mod=$user->id;
			$object->active=1;
			$object->status=1;
			$object->datec = $now;
			$object->datem = $now;
			$object->tms = $now;

			if (empty($object->ref))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
			}
			if (!$error)
			{
				$res = $object->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Importsuccessfull'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
		else
		{
			$db->rollback();
			$action='';
		}
	}
}

/*
 * View
 */

$transAreaType = $langs->trans("Maintenance");
$helpurl='';
$helpurl='EN:Module_Mant|FR:Module_Mant|ES:M&oacute;dulo_Mant';

llxHeader("",$langs->trans("Maintenance"),$helpurl);

print_fiche_titre($transAreaType);

//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Zone recherche produit/service
 */
$rowspan=2;
/*
 * Statistics area
 */

$third = array(
	'pend'=>0,
	'pasi' => 0,
	'asig' => 0,
	'prog' => 0,
	'ejec' => 0,
	'conc' => 0
);
$total=0;

$sql = "SELECT s.rowid, s.status";
$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as s";
if (!$user->rights->mant->tick->selus && !$user->admin)
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_work_request AS w ON s.fk_work_request = w.rowid ";
$sql.= ' WHERE s.entity IN ('.getEntity('m_jobs', 1).')';
if ($user->societe_id)
{
	$sql.= " AND s.status > 0 ";
	$sql.= " AND s.fk_soc = ".$user->societe_id;
}
else
	$sql.= " AND s.status > 0 ";
if (!$user->rights->mant->tick->selus && !$user->admin)
{
	if ($user->fk_member>0)
		$sql.= " AND s.fk_member = ".$user->fk_member;
}

$result = $db->query($sql);
if ($result)
{
	while ($objp = $db->fetch_object($result))
	{
		$found=0;

		if ($objp->status == 0)
		{
			$found = 1;
			$third['pend']++;
		}
		if ($objp->status == 1)
		{
			$found = 1;
			$third['pasi']++;
		}
		if ($objp->status == 2)
		{
			$found = 1;
			$third['asig']++;
		}
		if ($objp->status == 3)
		{
			$found = 1;
			$third['prog']++;
		}
		if ($objp->status == 4)
		{
			$found = 1;
			$third['ejec']++;
		}
		if ($objp->status == 5)
		{
			$found = 1;
			$third['conc']++;
		}
		if ($found) $total++;
	}
}
else
	dol_print_error($db);

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistics").'</th></tr>';
if (! empty($conf->use_javascript_ajax) &&
	((round($third['pend'])?1:0)+(round($third['pasi'])?1:0)+(round($third['asig'])?1:0)+(round($third['prog'])?1:0)+(round($third['ejec'])?1:0)+(round($third['conc'])?1:0) >= 2))
{
	print '<tr><td align="center">';
	$dataseries=array();
	$dataseries[]=array('label'=>$langs->trans("Pending"),'data'=>round($third['pend']));
	$dataseries[]=array('label'=>$langs->trans("Byassigning"),'data'=>round($third['pasi']));
	$dataseries[]=array('label'=>$langs->trans("Assigned"),'data'=>round($third['asig']));
	$dataseries[]=array('label'=>$langs->trans("Programmed"),'data'=>round($third['prog']));
	$dataseries[]=array('label'=>$langs->trans("Execution"),'data'=>round($third['ejec']));
	$dataseries[]=array('label'=>$langs->trans("Concluded"),'data'=>round($third['conc']));
	$data=array('series'=>$dataseries);
	dol_print_graph('stats',300,180,$data,1,'pie',0);
	print '</td></tr>';
}
else
{
	$statstring = "<tr $bc[0]>";
	$statstring.= '<td><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php">'.$langs->trans("Pending").'</a></td><td align="right">'.round($third['pend']).'</td>';
	$statstring.= "</tr>";

	$statstring.= "<tr $bc[1]>";
	$statstring.= '<td><a href="'.DOL_URL_ROOT.'/comm/list.php">'.$langs->trans("Byassigning").'</a></td><td align="right">'.round($third['pasi']).'</td>';
	$statstring.= "</tr>";

	$statstring2 = "<tr $bc[0]>";
	$statstring2.= '<td><a href="'.DOL_URL_ROOT.'/fourn/liste.php">'.$langs->trans("Assigned").'</a></td><td align="right">'.round($third['asig']).'</td>';
	$statstring2.= "</tr>";

	$statstring2 = "<tr $bc[0]>";
	$statstring2.= '<td><a href="'.DOL_URL_ROOT.'/fourn/liste.php">'.$langs->trans("Programmed").'</a></td><td align="right">'.round($third['prog']).'</td>';
	$statstring2.= "</tr>";

	$statstring2 = "<tr $bc[0]>";
	$statstring2.= '<td><a href="'.DOL_URL_ROOT.'/fourn/liste.php">'.$langs->trans("Execution").'</a></td><td align="right">'.round($third['ejec']).'</td>';
	$statstring2.= "</tr>";

	$statstring2 = "<tr $bc[0]>";
	$statstring2.= '<td><a href="'.DOL_URL_ROOT.'/fourn/liste.php">'.$langs->trans("Concluded").'</a></td><td align="right">'.round($third['conc']).'</td>';
	$statstring2.= "</tr>";

	print $statstring;
	print $statstring2;
}
print '<tr class="liste_total"><td>'.$langs->trans("Total order jobs").'</td><td align="right">';
print $total;
print '</td></tr>';
print '</table>';
print '<br>';

//equipos que estan en mantenimiento
$filter = " AND t.status >= 3 AND t.fk_equipment > 0";
$res = $objJobs->fetchAll('DESC','t.ref',0,10,array(),'AND',$filter);
if ($res >0)
{
	$objEquipment = new Mequipmentext($db);
	$lines = $objJobs->lines;
	$num = count($lines);
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.'<span class="badge">'.$num.'</span> '.$langs->trans("Equipmentinmaintenance").'</td><td align="center">'.$langs->trans("OT").'</td><td align="center">'.$langs->trans("Date").'</td><td align="right">'.$langs->trans("Status").'</td><td>&nbsp;</td>';
	print "</tr>\n";

	foreach($lines AS $j => $line)
	{
		$var = !$var;
		print '<tr '.$bc[$var].'>';
		$objEquipment->fetch($line->fk_equipment);
		$objJobs->id = $line->id;
		$objJobs->ref = $line->ref;
		$objJobs->status = $line->status;
		print '<td>'.$objEquipment->getNomUrl().' '.$objEquipment->label.'</td>';
		print '<td align="center">'.$objJobs->getNomUrl().'</td>';
		print '<td align="center">'.dol_print_date($line->date_create,'day').'</td>';
		print '<td align="right">'.$objJobs->getLibStatut(6).'</td>';
		print '<td></td>';
		print '</tr>';
	}
	print '</table>';
	print '<br>';
}

//10 materiales solicitados
$filter = " AND t.fk_product > 0";
$objJobsresource = new Mjobsresource($db);
$res = $objJobsresource->fetchAll('DESC','t.dater',0,15,array(),'AND',$filter);
if ($res >0)
{
	$objProduct = new Product($db);
	$objEquipment = new Mequipmentext($db);
	$lines = $objJobsresource->lines;
	$num = count($lines);
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.'<span class="badge">'.$num.'</span> '.$langs->trans("Productsrequestedformaintenance").'</td><td align="center">'.$langs->trans("Equipment").'</td><td align="center">'.$langs->trans("OT").'</td><td align="center">'.$langs->trans("Date").'</td><td>&nbsp;</td>';
	print "</tr>\n";

	foreach($lines AS $j => $line)
	{
		$var = !$var;
		print '<tr '.$bc[$var].'>';
		$objProduct->fetch($line->fk_product);
		$objJobs->fetch($line->fk_jobs);
		$objEquipment->fetch($objJobs->fk_equipment);
		print '<td>'.$objProduct->getNomUrl().' '.$objProduct->label.'</td>';
		print '<td>'.$objEquipment->getNomUrl().' '.$objEquipment->label.'</td>';
		print '<td align="center">'.$objJobs->getNomUrl().'</td>';
		print '<td align="center">'.dol_print_date($line->dater,'day').'</td>';
		print '</tr>';
	}
	print '</table>';
	print '<br>';
}

//print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
print '<div class="fichethirdleft">';
if ($user->rights->mant->update)
{
	if ($conf->global->MANT_EQUIPMENT_INTEGRATED_WITH_ASSET)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=update" class="butAction">'.$langs->trans('Toupdate').'</a>';
	}
}
print '</div>';
print '</div>';
//estadisticas fin2
print '<div class="fichetwothirdright">';
print '<div class="ficheaddleft">';

/*
 * Last modified request
 */
$max=10;
if ($user->rights->mant->tick->leer)
{
	$sql = "SELECT p.rowid as id, p.ref, p.email, p.detail_problem, p.date_create, p.status ";
	$sql.= " FROM ".MAIN_DB_PREFIX."m_work_request AS p ";
	$sql.= ' WHERE p.entity IN ('.getEntity('mjobs', 1).')';
	$sql.= " AND  p.status > 0 ";
	if (!$user->rights->mant->tick->selus && !$user->admin)
	{
		if ($user->fk_member>0)
			$sql.= " AND p.fk_member = ".$user->fk_member;
	}
	$sql.= " ORDER BY p.tms DESC ";
	$sql.= $db->plimit($max,0);
	//print $sql;
	$result = $db->query($sql);
	if ($result)
	{
		if (!$user->societe_id)
		{
			$num = $db->num_rows($result);
			$i = 0;
			if ($num > 0)
			{
				$transRecordedType = $langs->trans("Ticket modified",$max);
				print '<table class="noborder" width="100%">';
				$colnb=7;
				print '<tr class="liste_titre">';
				print '<td colspan="'.$colnb.'" align="center">'.$transRecordedType.'</td>';
				print '</tr>';

				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);

				print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);
				//print_liste_field_titre($langs->trans("Email"),"liste.php", "p.email","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Description"),"liste.php", "p.detail_problem","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Job orders"),"liste.php", "p.detail_problem","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
				print "</tr>\n";
				$objWork = new Mworkrequestext($db);
				$var=True;
				while ($i < $num)
				{
					$var = !$var;
					$objp = $db->fetch_object($result);
					//buscamos si tiene orden de trabajo
					$object = new Mjobsext($db);
					$object->getlist($objp->id);
					$objWork->id = $objp->id;
					$objWork->ref = $objp->ref;
					print "<tr $bc[$var]>";
					print '<td>'.$objWork->getNomUrl().'</td>';

					print '<td>'.dol_print_date($db->jdate($objp->date_create),'day').'</td>';
					//print '<td>'.$objp->email.'</td>';
					print '<td>'.(strlen($objp->detail_problem) > 40?'<a href="#" title="'.$objp->detail_problem.'">'.substr($objp->detail_problem,0,40).'...</a>':$objp->detail_problem).'</td>';
					print '<td>';
					if (count($object->array) > 0)
					{
						$cWorkorder= '';
						foreach ((array) $object->array AS $j => $objjobs)
						{
							if ($objjobs->fk_work_request == $objp->id)
							{
								$object->id = $objjobs->id;
								$object->ref = $objjobs->ref;
								if (!empty($cWorkorder)) $cWorkorder.=', ';
								$cWorkorder.= $object->getNomUrl();
							}
						}
						print $cWorkorder;
					}
					else
						print '&nbsp;';
					print '</td>';
					print '<td align="right">'.$object->LibStatut($objp->status,6).'</td>';
					print '</tr>';
					$i++;
				}

		//$db->free();
				print "</table>";
			}
		}
	}
	else
	{
		dol_print_error($db);
	}
}
//print '</td></tr></table>';
print '</div>';

//ot
print '<div class="ficheaddleft">';

/*
 * Last modified products
 */
$max=10;

$sql = "SELECT p.rowid as id, p.ref, p.email, p.detail_problem, p.date_create, p.date_ini, p.date_fin, p.status ";
$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs p ";
if (!$user->rights->mant->tick->selus && !$user->admin)
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_work_request AS w ON p.fk_work_request = w.rowid ";
$sql.= ' WHERE p.entity IN ('.getEntity($object->element, 1).')';
$sql.= " AND  p.status > 0 ";
if (!$user->rights->mant->tick->selus && !$user->admin)
{
	if ($user->fk_member>0)
		$sql.= " AND w.fk_member = ".$user->fk_member;
}
if ($user->societe_id)
	$sql.= " AND p.fk_soc = ".$user->societe_id;

$sql.= " ORDER BY p.tms DESC ";
$sql.= $db->plimit($max,0);

//print $sql;
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	if ($num > 0)
	{
		$transRecordedType = $langs->trans("Job orders",$max);
		print '<table class="noborder" width="100%">';
		$colnb=7;
		if (empty($conf->global->PRODUIT_MULTIPRICES)) $colnb++;
		print '<tr class="liste_titre">';
		print '<td colspan="'.$colnb.'" align="center">'.$transRecordedType.'</td>';
		print '</tr>';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);

		print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Email"),"liste.php", "p.email","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Description"),"liste.php", "p.detail_problem","","","",$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("Dateini"),"", "","","","");
		//print_liste_field_titre($langs->trans("Datefin"),"", "","","","");
		print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
		print "</tr>\n";

		$var=True;
		$object = new Mjobsext($db);
		while ($i < $num)
		{
			$objp = $db->fetch_object($result);
			$object->id = $objp->id;
			$object->ref = $objp->ref;
			print "<tr $bc[$var]>";
			print '<td nowrap>'.$object->getNomUrl().'</td>';

			print '<td>'.dol_print_date($db->jdate($objp->date_create),'day').'</td>';
			//print '<td>'.$objp->email.'</td>';
			print '<td nowrap>'.(strlen($objp->detail_problem) > 40?'<a href="#" title="'.$objp->detail_problem.'">'.substr($objp->detail_problem,0,40).'...</a>':$objp->detail_problem).'</td>';
			//print '<td>'.dol_print_date($db->jdate($objp->date_ini),'day').'</td>';
			//print '<td>'.dol_print_date($db->jdate($objp->date_fin),'day').'</td>';
			print '<td nowrap align="right">'.$object->LibStatut($objp->status,6).'</td>';
			print '</tr>';

			$i++;
		}

		$db->free();

		print "</table>";
	}
}
else
{
	dol_print_error($db);
}

//print '</td></tr></table>';
print '</div>';

print '</div>';

llxFooter();

$db->close();


?>
