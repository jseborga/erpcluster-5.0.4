<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/contab/period/liste.php
 *      \ingroup    Contab period
 *      \brief      Page liste des period contable
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontactext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
// require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

$langs->load("stocks");
$langs->load("mant@mant");

if (!$user->rights->mant->jobs->leer)
	accessforbidden();

//array de speciality
$aSpeciality = list_speciality('code');

if (isset($_POST['search_ref']))
	$_SESSION['msearch_ref'] = $_POST['search_ref'];
if (isset($_POST['search_pro']))
	$_SESSION['msearch_pro'] = $_POST['search_pro'];
if (isset($_POST['search_loc']))
	$_SESSION['msearch_loc'] = $_POST['search_loc'];
if (isset($_POST['search_problem']))
	$_SESSION['msearch_problem'] = $_POST['search_problem'];
if (isset($_POST['search_email']))
	$_SESSION['msearch_email'] = $_POST['search_email'];
if (isset($_POST['search_contact']))
	$_SESSION['msearch_contact'] = $_POST['search_contact'];
if (isset($_POST['search_speciality']))
	$_SESSION['msearch_speciality'] = $_POST['search_speciality'];
if (isset($_POST['search_refwork']))
	$_SESSION['msearch_refwork'] = $_POST['search_refwork'];
if (isset($_POST['fi_month']))
{
	$search_fi = dol_mktime(0, 0, 1, GETPOST('fi_month'),GETPOST('fi_day'),GETPOST('fi_year'));
	$_SESSION['msearch_fi'] = $search_fi;
	$_SESSION['msearch_ff'] = dol_now();
}
if (isset($_POST['ff_month']))
{
	$search_ff = dol_mktime(0, 0, 1, GETPOST('ff_month'),GETPOST('ff_day'),GETPOST('ff_year'));
	$_SESSION['msearch_ff'] = $search_ff;
}
if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
{
	$_SESSION["msearch_ref"] = '';
	$_SESSION["msearch_pro"] = '';
	$_SESSION["msearch_loc"] = '';
	$_SESSION["msearch_problem"] = '';
	$_SESSION["msearch_email"] = '';
	$_SESSION["msearch_contact"] = '';
	$_SESSION["msearch_fi"] = '';
	$_SESSION["msearch_ff"] = '';
	$_SESSION["msearch_refwork"] = '';
	$_SESSION["msearch_speciality"] = '';
}

$search_ref     = $_SESSION["msearch_ref"];
$search_pro     = $_SESSION["msearch_pro"];
$search_loc     = $_SESSION["msearch_loc"];
$search_problem = $_SESSION["msearch_problem"];
$search_email   = $_SESSION["msearch_email"];
$search_contact = $_SESSION["msearch_contact"];
$search_fi      = $_SESSION["msearch_fi"];
$search_ff      = $_SESSION["msearch_ff"];
$search_refwork = $_SESSION["msearch_refwork"];
$search_speciality = $_SESSION["msearch_speciality"];

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.date_create";
if (! $sortorder) $sortorder="DESC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object = new Mjobsext($db);
$objAdherent = new Adherent($db);
$objSoc = new Societe($db);
$objUser = new User($db);
$objContact = new Contact($db);
$objJobscontact = new Mjobscontactext($db);
$objJobsuser = new Mjobsuserext($db);

$objReqcont  = new Mworkrequestcontact($db);
$objRequser  = new Mworkrequestuser($db);

$sql = "SELECT p.rowid, p.fk_work_request, p.ref, p.fk_member, p.detail_problem, p.email, p.date_create, p.fk_soc, p.speciality, p.status, ";
$sql.= " s.fk_typent, ";
$sql.= " pr.ref AS property, ";
$sql.= " l.detail AS location, ";
$sql.= " w.rowid AS rowidwork, w.ref AS refwork ";
$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as p";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe AS s ON p.fk_soc = s.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_property AS pr ON p.fk_property = pr.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_location AS l ON p.fk_location = l.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_work_request AS w ON p.fk_work_request = w.rowid ";

$sql.= " WHERE p.entity = ".$conf->entity;
if ($sref)
	$sql.= " AND p.date_create like '%".$sref."%'";
if ($user->societe_id)
	$sql.= " AND p.status > 0 ";
if ($user->societe_id)
	$sql.= " AND p.fk_soc = ".$user->societe_id;
if (!$user->rights->mant->jobs->leerall)
{
	if (!empty($user->array_options['options_fk_tercero']))
		$sql.= " AND s.fk_typent = ".$user->array_options['options_fk_tercero'];
}
if ($search_ref) $sql.= " AND p.ref LIKE '%".$db->escape($search_ref)."%'";
if ($search_pro) $sql.= " AND pr.ref LIKE '%".$db->escape($search_pro)."%'";
if ($search_loc) $sql.= " AND l.detail LIKE '%".$db->escape($search_loc)."%'";
if ($search_problem) $sql.= " AND p.detail_problem LIKE '%".$db->escape($search_problem)."%'";
// if ($search_email) $sql.= " AND p.email LIKE '%".$db->escape($search_email)."%'";
if ($search_fi)
{
	$aDataf = dol_getdate($search_fi);
	$searchfi = $aDataf['year'].'-'.$aDataf['mon'].'-'.$aDataf['mday'];
	$aDataf = dol_getdate($search_ff);
	$searchff = $aDataf['year'].'-'.$aDataf['mon'].'-'.$aDataf['mday'];
	$sql.= " AND p.date_create BETWEEN '".$searchfi ."' AND '".$searchff."'";
}
$search_sp = '';
if ($search_speciality)
{
	foreach ((array) $aSpeciality AS $k => $value)
	{
		$pos = STRPOS(STRTOUPPER($value),STRTOUPPER($search_speciality));
		if ($pos === false)
		{

		}
		else
		{
			if (!empty($search_sp)) $search_sp.=',';
			$search_sp .= "'".$k."'";
		}
	}
	if (empty($search_sp)) $search_sp = "'empty'";
	$sql.= " AND p.speciality IN (".$search_sp.")";
}
if ($search_refwork) $sql.= " AND w.ref LIKE '%".$db->escape($search_refwork)."%'";

// if ($sall)
// {
//     $sql.= " AND (p.period_month like '%".$sall."%' OR p.period_year like '%".$sall."%' OR p.data_ini like '%".$sall."%')";
// }
$sql.= " ORDER BY $sortfield $sortorder";

$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
$form=new Form($db);

if ($result)
{
	$nLoop = 0;
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
	llxHeader("",$langs->trans("Maintenancemanagement"),$help_url);

	print_barre_liste($langs->trans("Listjobsorder"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	//filtro
	print '<form method="POST" name="searchFormList" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">'."\n";

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Ticket"),"liste.php", "w.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Property"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Location"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);

	print_liste_field_titre($langs->trans("Problem"),"liste.php", "p.email","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("User"),"liste.php", "p.detail_problem","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Assigned"),"liste.php", "p.fk_soc","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Esp."),"liste.php", "p.speciality","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Resp."),"", "","","","");
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	//buscador
	print '</tr>';

	//filtro colores
	print '<tr class="liste_titre">';
	print '<td >&nbsp;</td>';
	print '<td >&nbsp;</td>';

	print '<td nowrap class="liste_titre">';
	if (empty($search_fi))
		$form->select_date('-1','fi_','','',0,"searchFormList",1,0);
	else
		$form->select_date($search_fi,'fi_','','',0,"searchFormList",1,0);
	print '</td>';

	print '<td >&nbsp;</td>';
	print '<td class="liste_titre">&nbsp;</td>';

	print '<td nowrap class="liste_titre">';
	print $htmlindividual;
	print '</td>';

	print '<td align="left" class="liste_titre">';
	print '&nbsp;';
	print '</td>';

	if ($idp)
	{
		print '<td align="left" class="liste_titre">';
		print '&nbsp;';
		print '</td>';
	}
	print '<td colspan="4" align="left" class="liste_titre">';
	print $htmltotal;
	print '</td>';


	print '</tr>';

	//buscadores
	print '</tr>';

	print '<tr class="liste_titre">';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="5"></td>';
	print '<td class="liste_titre"><input type="text" class="flat" name="search_refwork" value="'.$search_refwork.'" size="5"></td>';
	print '<td class="liste_titre">';
	if (empty($search_ff))
		$form->select_date('-1','ff_','','',0,"searchFormList",1,0);
	else
		$form->select_date($search_ff,'ff_','','','',"searchFormList",1,0);
	print '</td>';

	print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_pro" value="'.$search_pro.'" size="6">';
	print '</td>';
	print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_loc" value="'.$search_loc.'" size="6">';
	print '</td>';

	print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_problem" value="'.$search_problem.'" size="15">';
	print '</td>';

	print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_email" value="'.$search_email.'" size="10">';
	print '</td>';

	print '<td align="left" class="liste_titre">';
	print '&nbsp;';
	print '</td>';
	print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_speciality" value="'.$search_speciality.'" size="6"></td>';
	print '<td nowrap class="liste_titre"><input type="text" class="flat" name="search_contact" value="'.$search_contact.'" size="6"></td>';


	print '<td nowrap class="liste_titre" align="right">';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
	print '</td>';


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

			$object->id = $objp->rowid;
			$object->ref = $objp->ref;
			$object->status = $objp->status;

				//verificamos responsables asignados
			$htmlc = '';
			$htmlsoc = '';
			if ($objp->fk_soc != -2)
			{
					//buscamos a la compania
				$lUser = false;
				if ($objSoc->fetch($objp->fk_soc)>0 && $objp->fk_soc > 0)
				{
					$objTypent = fetch_typent($objSoc->typent_id);
					if ($object->fk_soc == -2 || ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB' ))
					 //asignacion interna
						$lUser = true;
					$htmlsoc = $objSoc->name;
				}
				if (!$lUser)
				{
					//buscamos si esta asignado el contacto responsable
					$aArray = $objJobscontact->list_contact($objp->rowid);
					if (empty(count($aArray)))
						$aArray = $objReqcont->list_contact($objp->fk_work_request);
					if (!empty($search_contact)) $lPrint = false;

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
									{
										//nada
									}
									else
										$lPrint = true;
								}
								$htmlc.=$objContact->lastname.' '.$objContact->firstname;
							}
						}
					}
				}
				else
				{
					//buscamos si esta asignado el usuario responsable
					$aArray = $objJobsuser->list_jobsuser($objp->rowid);
					if (empty(count($aArray)))
						$aArray = $objRequser->list_requestuser($objp->fk_work_request);
					if (!empty($search_contact)) $lPrint = false;
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
								$htmlc.=$objUser->lastname.' '.$objUser->firstname.'</td>';
							}
						}
					}
				}
			}
			else
			{
				$htmlsoc = $langs->trans('Internalassignment');
		  			//buscamos si esta asignado el usuario responsable
				$aArray = $objJobsuser->list_jobsuser($objp->rowid);
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
							{
								//nada
							}
							else
								$lPrint = true;
						}
						if (!empty($htmlc))$htmlc.='</br>';
						if ($objUser->fetch($obju->fk_user))
							$htmlc.=$objUser->lastname.' '.$objUser->firstname;
					}
				}
			}
	  		//buscamos por filtro de nombre o email
			$nombreslog_ = '';
			$objAdherent->fetch($objp->fk_member);
			$nombreslog_ = $objAdherent->firstname.' '.$objAdherent->lastname.' '.$objp->email;
			if (!empty($search_email))
			{
				$filteruser = STRPOS(STRTOUPPER($nombreslog_),STRTOUPPER($search_email));
		  		//if ($lPrint == false)
				if ($filteruser===false)
					$lPrint = false;
				else
					$lPrint = true;
			}
			if ($lPrint)
			{
				$nLoop++;
		  		//fin revision responsables
				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td>'.$object->getNomUrl().'</td>';
				print '<td>'. $objp->refwork.'</td>';

				print '<td nowrap><a href="card.php?id='.$objp->rowid.'">'.dol_print_date($objp->date_create,'day').'</a></td>';
				print '<td>'.$objp->property.'</td>';
				print '<td>'.$objp->location.'</td>';
				print '<td>'.$objp->detail_problem.'</td>';

				print '<td>'.$objAdherent->lastname.' '.SUBSTR($objAdherent->firstname,0,1).'.'.'</td>';

				print '<td>'.$htmlsoc.'</td>';

				print '<td>';
				print $aSpeciality[$objp->speciality];
				print '</td>';

				print '<td>';
				print $htmlc;
				print '</td>';

				print '<td align="right">'.$object->LibStatut($objp->status,6).'</td>';
				print '</tr>';
			}
			$i++;
		}
	}

	$db->free($result);

	//totales
	print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td>';
	print '<td align="right">';
	print $nLoop;
	print '</td>';
	print '<td colspan="9">';
	print '&nbsp;';
	print '</td>';
	print '</tr>';

	print "</table>";
	print '</form>';
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
