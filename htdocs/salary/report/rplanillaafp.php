<?php
/* Copyright (C) 2013-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/salary/report/rplanillaafp.php
 *	\ingroup    Planilla AFP
 *	\brief      Page fiche salary planilla AFP
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfield.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictable.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryaprob.class.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent_type.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/formula/lib/formula.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");
$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$lCopy = false;
$mesg = '';

$object   = new Pperiodext($db); //periodos
$objectpr = new Pproces($db); //procesos
$objecttf = new Ptypefolext($db);//procedimientos
$objectsp = new Psalarypresentext($db); //salario actual
$objectsh = new Psalaryhistoryext($db); //salario history
$objectf  = new Pformulas($db); //formula
$objecto  = new Poperator($db);
$objectU  = new Puserext($db);
$objectCo = new Pcontractext($db); //contract
$objectUb = new Puserbonus($db);
$objectUs = new User($db);
$objectAd = new Adherent($db); //Adherent
$objectCh = new Pcharge($db); //charge
$objectgf = new Pgenericfield($db); //generic field
$objectgt = new Pgenerictable($db); //generic table
$objectsa = new Psalaryaprob($db); //salary approver

//determina el pais
$aPais = explode(":",$conf->global->MAIN_INFO_SOCIETE_COUNTRY);

$cPais = $aPais[1];
$_SESSION['param']['nDiasTrab'] = $conf->global->SALARY_NRO_DIAS_LABORAL;

$fk_period = GETPOST('fk_period');

/*
 * Actions
 */

// Add
if ($action == 'proces' && $user->rights->salary->crearrsal)
{
	$fk_period = GETPOST('fk_period');
	//recuperamos los valores configurados en period
	$result = $object->fetch($fk_period);
	if ($result)
	{
		$fk_type_fol = $object->fk_type_fol;
		$fk_proces   = $object->fk_proces;
	}
	s_cargamie();
	header("Location: rplanillaafp.php?action=edit&fk_period=".$fk_period);
	exit;

}
//action generalte
if ($action == 'generate' && $user->rights->salary->validsal)
{
	$fk_period   = $_SESSION['validateSalary']['fk_period'];
	$fk_proces   = $_SESSION['validateSalary']['fk_proces'];
	$fk_type_fol = $_SESSION['validateSalary']['fk_type_fol'];
	$state = GETPOST('state');
	$newState = $state - 1;
	If ($_SESSION['member_aprob'])
	{
	//verificamos el estado
		$sql = "SELECT state FROM ".MAIN_DB_PREFIX."p_salary_present WHERE ";
		$sql.= " fk_period = ".$fk_period." AND ";
		$sql.= " fk_proces = ".$fk_proces." AND ";
		$sql.= " fk_type_fol = ".$fk_type_fol." AND ";
		$sql.= " state = ".$newState;
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$obj = $db->fetch_object($resql);
			If ($num > 0)
			{
		//actualizamos el state
				$sql = " UPDATE ".MAIN_DB_PREFIX."p_salary_present ";
				$sql.= " SET state = ".$state;
				$sql.= " WHERE ";
				$sql.= " fk_period = ".$fk_period." AND ";
				$sql.= " fk_proces = ".$fk_proces." AND ";
				$sql.= " fk_type_fol = ".$fk_type_fol;
				$result = $db->query($sql);
				$_SESSION['validateSalary'] = array();
				$_SESSION['member_aprob'] = false;
				if ($_SESSION['aprob_final'] == true)
				{
					$lOk = registry_end($fk_period,$fk_proces,$fk_type_fol,$state);
					if ($lOk)
					{
						$object->fetch($fk_period);
						if ($object->id == $fk_period)
						{
							$object->ref = $object->codref;
							$object->date_close = date('Y-m-d H:m:s');
							$object->state = 5;

							$object->update($user);

						}
					}
				}
				else
				{
					header("Location: rplanillaafp.php?action=edit&fk_period=".$fk_period);
					exit;
				}
			}
		}
	}
	//recuperamos los valores configurados en period
	$result = $object->fetch($fk_period);
	if ($result)
	{
		$fk_type_fol = $object->fk_type_fol;
		$fk_proces   = $object->fk_proces;
	}
	s_cargamie();
	header("Location: rplanillaafp.php?action=edit&fk_period=".$fk_period);
	exit;

}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);
//creando el proceso de elaboracion planilla
//formulario de configuracion
if ($action == 'create' && $user->rights->salary->crearrsal)
{
	$_SESSION['aPlanilla'] = array();

	print_fiche_titre($langs->trans("Salary sould"));

	print "<form action=\"rplanilla.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="proces">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// period
	print '<tr><td class="fieldrequired">'.$langs->trans('Period').'</td><td colspan="2">';
	print $object->select_period($fk_period,'fk_period','','',1);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Process").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["action"]=='edit')
	{
		dol_htmloutput_mesg($mesg);
	 //armando la planilla de sueldos
		$aPlanilla = $_SESSION['aPlanilla'];

		print_barre_liste($langs->trans("Planilla"), $page, "rplanilla.php", "", $sortfield, $sortorder,'',$num);

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("N."),"liste.php", "p.table_cod","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("C_idoc"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Lastname").' '.$langs->trans('And').' '.$langs->trans('Name'),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Nationality"),"liste.php", "p.sequen","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Datenac"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Sexo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Cargo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Dateini"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Vivienda"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("AFP"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Solidario"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Desconttotal"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print "</tr>\n";
		$seq = 1;
	 //recuperamos el periodo
		$lPeriodClose = false;
		$result = $object->fetch($fk_period);
		if ($result)
		{
			$fk_type_fol = $object->fk_type_fol;
			$fk_proces   = $object->fk_proces;
			$date_close  = $object->date_close;
			if ($object->state == 5 )
				$lPeriodClose = true;
		}
		foreach ((array) $aPlanilla AS $idUser => $dataUser)
		{
			$nTotalRend = 0;
			$nTotalDesc = 0;
			$nSumaAfp   = 0;
			$nSumaDesc  = 0;
			$objectAd->fetch($idUser);
			$objectU->fetch_user($idUser);
			$objectCo->fetch_vigent($idUser,1);

			$objectCh->fetch($objectCo->fk_charge);
		  //buscando el calculo realizado en salary_present
		  //verificar si el concepto sera fijo o no

		  //basico
			$objres = search_planilla($idUser,'S002',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			$state = $objres->state;
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>'.$seq.'</td>';
			if ($objectU->id == $idUser)
			{
				$docum = $objectU->docum;
				$lastnametwo = $objectU->lastnametwo;
			}
			else
			{
				$docum = '';
				$lastnametwo = '';
			}
			print '<td>'.$docum.'</td>';
			print '<td>'.$objectAd->lastname.' '.$lastnametwo.' '.$objectAd->firstname.'</td>';
		  // Country
			print '<td>';
			$img=picto_from_langcode($objectAd->country_code);
			if ($img) print $img.' ';
			print getCountry($objectAd->country_code);
			print '</td>';
			print '<td>'.dol_print_date($objectAd->naiss).'</td>';
			print '<td>'.select_sex($objectU->sex,'sex','','',1,1).'</td>';
			if ($objectCh->id == $objectCo->fk_charge)
				print '<td>'.$objectCh->codref.'</td>';
			else
				print '<td>&nbsp;</td>';
			print '<td>'.dol_print_date($objectCo->date_ini).'</td>';
		  //modificado
			$nTotalDesc = 0;

		  //vivienda
			$objres = search_planilla($idUser,'S015',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalDesc += $objres->amount;
		  // FCI
			$objres = search_planilla($idUser,'S016',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalDesc += $objres->amount;
		  //solidario
			$objres = search_planilla($idUser,'S017',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalDesc += $objres->amount;

		  //descuento total
			print '<td align="right">'.price($nTotalDesc).'</td>';
		  //$nTotalRend += $objres->amount;


			print '</tr>';
			$seq++;
		}
		$db->free($result);
		print "</table>";
		/* **************************************** */
		/*                                          */
		/* Barre d'action                           */
		/*                                          */
		/* **************************************** */
		print '<div class="tabsAction">';
		if ($action == 'edit' && $lPeriodClose == false && $user->rights->salary->validsal)
		{
			$_SESSION['validateSalary'] = array('fk_period' => $fk_period,
				'fk_proces' => $fk_proces,
				'fk_type_fol' => $fk_type_fol);
			$lAprueba = false;
			$lAprobUlt = false;
			$nSeqAprob = 0;
			$nLoop = 0;

			$objectCo->fetch_vigent($user->fk_member,1);

			$aArrayAprob = $objectsa->getArrayAprob();
		  //contamos cuantos aprobadores son
			$nMaxAprob = count($aArrayAprob);
			$newState = $state;
		  //obtenemos quien aprueba en este estado
			$aData = $aArrayAprob[$newState];

		  //buscamos quien aprueba
			if ($aData['type'] == 1)
			{
				if ($user->fk_member == $aData['fk_value'])
				{
					$_SESSION['member_aprob'] = true;
					$lAprueba = true;
					$nSeqAprob = $newState+1;
				}
			}
			Elseif ($aData['type'] == 2)
			{
		  //buscamos quien aprueba
				if ($objectCo->fk_charge == $aData['fk_value'])
				{
					$_SESSION['member_aprob'] = true;
					$lAprueba = true;
					$nSeqAprob = $newState+1;
				}
			}
			if ($state == $nMaxAprob)
				$nSeqAprob = $nMaxAprob;
			if ($nSeqAprob == $nMaxAprob)
			{
				$lAprobUlt = true;
				$_SESSION['aprob_final'] = true;
			}
			if ($lAprueba || $user->admin == 1)
			{
				if ($user->admin == 1)
				{
					$_SESSION['member_aprob'] = true;
					$nSeqAprob = $state + 1;
					if ($nSeqAprob == $nMaxAprob)
					{
						$lAprobUlt = true;
						$_SESSION['aprob_final'] = true;
					}
				}
				if ($lAprobUlt == true)
					print '<a class="butAction" href="rap.php?action=generate&state='.$nSeqAprob.'">'.$langs->trans("Approvefinal").'</a>';
				else
					print '<a class="butAction" href="rap.php?action=generate&state='.$nSeqAprob.'">'.$langs->trans("Approve").'</a>';
			}
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Approve")."</a>";
		}
		print '</div>';
	}
}


llxFooter();

$db->close();

?>
