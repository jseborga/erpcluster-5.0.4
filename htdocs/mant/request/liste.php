<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/mant/request/liste.php
 *      \ingroup    Mantenimiento
 *      \brief      Page liste des liste work request
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/mant/request/class/mworkrequest.class.php");
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestuser.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

$langs->load("stocks");
$langs->load("mant@mant");

if (!$user->rights->mant->tick->leer)
	accessforbidden();

$aseletick = GETPOST('seletick');
if (isset($aseletick) && !empty($aseletick))
{
	$statut = GETPOST('filter_statut');
	$_SESSION['seletick'] = $aseletick;
	if ($statut == 1)
	{
		header("Location: ".DOL_URL_ROOT."/mant/request/fichegroup.php?action=create");
		exit;
	}
	if ($statut == 4)
	{
		header("Location: ".DOL_URL_ROOT."/mant/request/fichegroupot.php?action=create");
		exit;
	}
}

//array de speciality
$aSpeciality = list_speciality('code');

if (isset($_POST['search_ref']))
	$_SESSION['tsearch_ref'] = $_POST['search_ref'];
if (isset($_POST['search_int']))
	$_SESSION['tsearch_int'] = $_POST['search_int'];
if (isset($_POST['sd_day']))
{
	$date_ass = dol_mktime(12, 0, 0, GETPOST('sd_month'),GETPOST('sd_day'),GETPOST('sd_year'));
	$_SESSION['tsearch_date'] = $date_ass;
}
if (isset($_POST['search_problem']))
	$_SESSION['tsearch_problem'] = $_POST['search_problem'];
if (isset($_POST['search_email']))
	$_SESSION['tsearch_email'] = $_POST['search_email'];
if (isset($_POST['search_ot']))
	$_SESSION['tsearch_ot'] = $_POST['search_ot'];
if (isset($_POST['search_statut']))
	$_SESSION['tsearch_statut'] = $_POST['search_statut'];

if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
{
	$_SESSION["tsearch_ref"] = '';
	$_SESSION["tsearch_date"] = '';
	$_SESSION["tsearch_int"] = '';
	$_SESSION["tsearch_problem"] = '';
	$_SESSION["tsearch_email"] = '';
	$_SESSION["tsearch_ot"] = '';
	$_SESSION["tsearch_statut"] = '';
}

$search_ref     = $_SESSION["tsearch_ref"];
$search_date    = $_SESSION["tsearch_date"];
$search_int     = $_SESSION["tsearch_int"];
$search_problem = $_SESSION["tsearch_problem"];
$search_email   = $_SESSION["tsearch_email"];
$search_ot      = $_SESSION["tsearch_ot"];
$search_statut  = $_SESSION["tsearch_statut"];


$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$mesg = '';
if (isset($_GET['mesg'])) $mesg = $_GET['mesg'];

//armamos array de estados
$aStatut = array(0=>'Pendiente',
	1=>'Validado',
	2=>'Asignado',
	3=>'Asignado tec.',
	4=>'Programado',
	5=>'En ejecucion',
	6=>'Ejecutado',
	9=>'Cerrado');

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.date_create";
if (! $sortorder) $sortorder="DESC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object = new Mworkrequest($db);
$objMjobs = new Mjobs($db);
$objAdherent = new Adherent($db);
$objSoc = new Societe($db);
$objUser = new User($db);
$objContact = new Contact($db);
$objJobscontact = new Mjobscontact($db);
$objJobsuser = new Mjobsuser($db);
$objWorkcontact = new Mworkrequestcontact($db);
$objWorkuser = new Mworkrequestuser($db);

$form=new Form($db);

$sql  = "SELECT p.rowid, p.ref, p.fk_member, p.email, p.internal, p.date_create, p.detail_problem, p.statut, p.fk_soc, p.speciality, ";
$sql.= " s.ref AS refjobs, s.fk_work_request, s.date_create AS date_createjobs, s.rowid AS rowidjobs, ";
$sql.= " s.fk_soc AS fk_socjobs, s.speciality AS specialityjobs, s.statut AS statutjobs ";
$sql.= " FROM ".MAIN_DB_PREFIX."m_work_request as p";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_jobs AS s ON s.fk_work_request = p.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;

if ($search_ref)
	$sql.= " AND p.ref like '%".$search_ref."%'";
if ($search_date)
{
	$aDate = dol_getdate($search_date);
	$search_daten1 = $aDate['year'].'-'.$aDate['mon'].'-'.$aDate['mday'].' 00:00:01';
	$search_daten2 = $aDate['year'].'-'.$aDate['mon'].'-'.$aDate['mday'].' 23:59:59' ;
	$sql.= " AND p.date_create BETWEEN '".$search_daten1."' AND '".$search_daten2."'";
}
if ($search_int)
	$sql.= " AND p.internal like '%".$search_int."%'";
if ($search_problem)
	$sql.= " AND p.detail_problem like '%".$search_problem."%'";
if ($search_email)
	$sql.= " AND p.email like '%".$search_email."%'";
if ($search_ot)
	$sql.= " AND s.ref like '%".$search_ot."%'";
if ($search_statut)
	$sql.= " AND p.statut = ".$search_statut;
if (!$user->admin && !$user->rights->mant->tick->selus)
{
	if ($user->fk_member>0)
		$sql.= " AND p.fk_member = ".$user->fk_member;
}
if ($user->societe_id)
{
	$sql.= " AND p.fk_soc = ".$user->societe_id;
	$sql.= " AND p.statut > 0 ";
}

// if ($user->societe_id)
//   $sql.= " AND p.fk_soc = ".$user->societe_id;
// if (!empty($user->array_options['options_fk_tercero']))
//   $sql.= " AND s.fk_typent = ".$user->array_options['options_fk_tercero'];

// if ($sall)
// {
//     $sql.= " AND (p.period_month like '%".$sall."%' OR p.period_year like '%".$sall."%' OR p.data_ini like '%".$sall."%')";
// }
$sql.= " ORDER BY $sortfield $sortorder";

$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
	llxHeader("",$langs->trans("Maintenancemanagement"),$help_url);

	print_barre_liste($langs->trans("Listtickets"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	//filtro
	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input type="hidden" name="filter_statut" value="'.$search_statut.'">';
	print '<input type="hidden" name="action" value="ticket">';
	dol_htmloutput_mesg($mesg);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);

	print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Applicant"),"liste.php", "p.email","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Internal"),"liste.php", "p.internal","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Problem"),"liste.php", "p.detail_problem","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Assigned"),"liste.php", "p.fk_soc","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Speciality"),"liste.php", "p.speciality","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Responsible"),"", "","","","");
	print_liste_field_titre($langs->trans("Jobs"),"liste.php", "s.ref","","","");
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
	if ($user->rights->mant->tick->ass)
		print_liste_field_titre($langs->trans("Action"),"", "",'','','align="center"');
	print "</tr>\n";

	//buscadores
	print '<tr class="liste_titre">';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="6"></td>';
	print '<td class="liste_titre" nowrap>';
	$form->select_date($search_date,'sd_','','',1,"dateassign",1,0);
	print '</td>';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_email" value="'.$search_email.'" size="8">';
	print '</td>';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_int" value="'.$search_int.'" size="3">';
	print '</td>';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_problem" value="'.$search_problem.'" size="20">';
	print '</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';
	print '</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';
	print '<td>';
	print '&nbsp;';
	print '</td>';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_ot" value="'.$search_ot.'" size="8">';
	print '</td>';

	print '<td align="left" class="liste_titre">';
	print $form->selectarray('search_statut',$aStatut,$search_statut,1);
	print '</td>';
	if ($user->rights->mant->tick->ass)
	{
		print '<td nowrap class="liste_titre" align="right">';
		print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '&nbsp;';
		print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
		print '</td>';
	}

	print '</tr>';

	//fin buscador

	if ($num)
	{
		$var=True;
		while ($i < min($num,$limit))
		{
			$lPrint = true;
			$objp = $db->fetch_object($result);
			$objAdherent->fetch($objp->fk_member);

			//verificamos la asignacion de la OT
			if ($conf->global->MANT_ACTUALIZATION && $user->admin && ((empty($objp->fk_soc) && $objp->fk_socjobs) || ($objp->statut <> $objp->statutjobs && $objp->statutjobs>0)))
			{
				//actualizamos wokr_request
				$object->fetch($objp->rowid);
				if ($object->id == $objp->rowid)
				{
					$objMjobs->fetch($objp->rowidjobs);
					if ($objMjobs->id == $objp->rowidjobs)
					{
						$object->description_prog = $objMjobs->description_prog;
						$object->date_ini_prog = $objMjobs->date_ini_prog;
						$object->date_fin_prog = $objMjobs->date_fin_prog;
						$object->speciality_prog = $objMjobs->speciality_prog;
						$object->fk_equipment_prog = $objMjobs->fk_equipment_prog;
						$object->fk_property_prog = $objMjobs->fk_property_prog;
						$object->fk_location_prog = $objMjobs->fk_location_prog;
						$object->typemant_prog = $objMjobs->typemant_prog;
					}
					$object->fk_soc = $objp->fk_socjobs;
					$object->speciality = $objp->specialityjobs;
					//revisamos los estados
					switch ($objp->statutjobs)
					{
						case 4:
						$object->statut = 6;
						break;
						case 3:
						$object->statut = 4;
						break;
						default:
						$object->statut = $objp->statutjobs;
						break;
					}
					$object->update($user);
					$objp->fk_soc = $objp->fk_socjobs;
					$objp->speciality = $objp->specialityjobs;
				}
			}
			//verificamos responsables asignados
			$htmlc = '';
			$htmlsoc = '';
			if ($objp->fk_soc > 0)
			{
		    //buscamos a la compania
				$lUser = false;
				if ($objSoc->fetch($objp->fk_soc) > 0 && $objp->fk_soc > 0)
				{
					$objTypent = fetch_typent($objSoc->typent_id);
					if ($object->fk_soc == -2 ||
						($objTypent->id == $objSoc->typent_id &&
			     $objTypent->code == 'TE_BCB' )) //asignacion interna
						$lUser = true;
					$htmlsoc = $objSoc->nom;
				}
				if (!$lUser)
				{
			//buscamos si esta asignado el contacto responsable
					$aArray = $objWorkcontact->list_contact($objp->rowid);
					if (!empty($search_contact))
						$lPrint = false;

					if (count($aArray) > 0)
					{
						foreach((array) $aArray AS $j => $objc)
						{
							if (!empty($htmlc))$htmlc.='</br>';
							if ($objContact->fetch($objc->fk_contact))
							{
								if (!empty($search_contact))
								{
									$cname = $objContact->lastname.' '.$objContact->firstname;
									$pos = STRPOS(STRTOUPPER($cname),STRTOUPPER($search_contact));
									if ($pos === false)
									{}
								else
									$lPrint = true;

							}
							$htmlc.= $objContact->lastname.' '.SUBSTR($objContact->firstname,0,1).'.';
						}
					}
				}
			}
			else
			{
		      //buscamos si esta asignado el usuario responsable
				$aArray = $objWorkuser->list_requestuser($objp->rowid);
				if (!empty($search_contact))
					$lPrint = false;

				if (count($aArray) > 0)
				{
					foreach((array) $aArray As $j => $obju)
					{
						if (!empty($htmlc))$htmlc.='</br>';
						if ($objUser->fetch($obju->fk_user)>0)
						{
							if ($objUser->id == $obju->fk_user)
								if (!empty($search_contact))
								{
									$cname = $objUser->lastname.' '.$objUser->firstname;
									$pos = STRPOS(STRTOUPPER($cname),STRTOUPPER($search_contact));
									if ($pos === false)
									{}
								else
									$lPrint = true;
							}
				  $htmlc.=$objUser->login.'</td>';//$objUser->lastname.' '.$objUser->firstname.'</td>';
				}
			}
		}
	}
}
else
{
	if ($objp->fk_soc == -1)
		  //asignacion interna
		$htmlsoc = $langs->trans('Internalassignment');
	else
		$htmlsoc = $langs->trans('Pending');
		  //buscamos si esta asignado el usuario responsable
	$aArray = $objWorkuser->list_requestuser($objp->fk_work_request);
	if (!empty($search_contact))
		$lPrint = false;
	if (count($aArray) > 0)
	{
		foreach((array) $aArray As $j => $obju)
		{
			if (!empty($search_contact))
			{
				$cname = $objUser->lastname.' '.$objUser->firstname;
				$pos = STRPOS(STRTOUPPER($cname),STRTOUPPER($search_contact));
				if ($pos === false)
				{}
			else
				$lPrint = true;
		}
		if (!empty($htmlc))$htmlc.='</br>';
		if ($objUser->fetch($obju->fk_user) && $obju->fk_user>0)
			    $htmlc.=$objUser->login;//$objUser->lastname.' '.$objUser->firstname;
		}
	}
}

if ($lPrint)
{
	$nLoop++;
	$var=!$var;
	print "<tr $bc[$var]>";
	print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_picto($langs->trans('Ticket'),DOL_URL_ROOT.'/mant/img/ticket','',1). $objp->ref.'</a></td>';

	print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Showjobsorder"),'calendar').' '. dol_print_date($objp->date_create,'day').'</a></td>';
		//buscamos al usuario
	$objAdherent->fetch($objp->fk_member);
	if ($objAdherent->id == $objp->fk_member)
		print '<td>'.$objAdherent->login.'</td>';
	else
		print '<td>'.$objp->email.'</td>';
	print '<td>'.$objp->internal.'</td>';
	print '<td>'.$objp->detail_problem.'</td>';
	print '<td>'.$htmlsoc.'</td>';

	print '<td>';
	print $aSpeciality[$objp->speciality];
	print '</td>';

	print '<td>';
	print $htmlc;
	print '</td>';

	print '<td>'.'<a href="'.DOL_URL_ROOT.'/mant/jobs/fiche.php?id='.$objp->rowidjobs.'">'.$objp->refjobs.'</a>'.'</td>';

	print '<td nowrap align="right">'.$object->LibStatut($objp->statut,6).'</td>';
	      if ($objp->statut == 4 && $search_statut == 4) //tecnico asignado
	      if ($user->rights->mant->jobs->crear)
	      {
	      	print '<td align="center">';
		    //se debe crear una asignacion para un ticket
	      	print '&nbsp;<input type="checkbox" name="seletick['.$objp->rowid.']" value="1">';
	      	print '</td>';

	      }
	      if ($objp->statut == 1 && $search_statut == 1)//validado
	      if ($user->rights->mant->tick->ass)
	      {
	      	print '<td align="center">';
		    //se debe crear una asignacion para un ticket
		    // print '<a href="'.DOL_URL_ROOT.'/mant/jobs/fiche.php?action=create&idw='.$objp->rowid.'">'.img_picto($langs->trans('Order jobs'),DOL_URL_ROOT.'/mant/img/ot','',1).'</a>';
	      	print '&nbsp;<input type="checkbox" name="seletick['.$objp->rowid.']" value="1">';
	      	print '</td>';

	      }
	      print '</tr>';
	  }
	  $i++;
	}
}
$db->free($result);
print "</table>";
if ($user->rights->mant->tick->ass && $search_statut == 1)
{
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Assign tickets").'">';
	print '</center>';
}
if ($user->rights->mant->jobs->crear && $search_statut == 4)
{
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Createworkorder").'">';
	print '</center>';
}
print '</form>';

}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
