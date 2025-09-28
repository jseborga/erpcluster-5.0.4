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
 *	\file       htdocs/salary/genera/planilla.php
 *	\ingroup    Planilla
 *	\brief      Page fiche salary planilla
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfieldext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictableext.class.php';

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

$mesg = '';

$objectpe = new Pperiodext($db); //periodos
$objectpr = new Pproces($db); //procesos
$objecttf = new Ptypefolext($db);//procedimientos
$objectsp = new Psalarypresentext($db); //salario actual
$objectsh = new Psalaryhistoryext($db); //salario actual
$objectf  = new Pformulas($db); //formula
$objecto  = new Poperator($db);
$objectC  = new Pconceptext($db);
$objectU  = new Puserext($db);
$objectUb = new Puserbonus($db);
$objectUs = new User($db);
$objectCo = new Pcontractext($db); //contract
$objectAd = new Adherent($db); //Adherent
$objectCh = new Pcharge($db); //charge
$objectgf = new Pgenericfieldext($db); //generic field
$objectgt = new Pgenerictableext($db); //generic table

//determina el pais
$aPais = explode(":",$conf->global->MAIN_INFO_SOCIETE_PAYS);

$cPais = $aPais[1];
$_SESSION['param']['nDiasTrab'] = $conf->global->SALARY_NRO_DIAS_LABORAL;

$fk_period = GETPOST('fk_period');

/*
 * Actions
 */
// Process
if ($action == 'proces' && $user->rights->salary->generasal)
{
	$fk_period = GETPOST('fk_period');
	//recuperamos los valores configurados en period
	$result = $objectpe->fetch($fk_period);
	if ($result>0)
	{
		$fk_type_fol = $objectpe->fk_type_fol;
		$fk_proces   = $objectpe->fk_proces;
	}
	//recuperamos el nombre del reporte
	$res = $objecttf->fetch($fk_type_fol);
	if ($res >0) $creport = $objecttf->name_report;
	//ejecutamos la formula s_cargamie(); //lib/report.lib.php
	$lRet = s_cargamie();
	$aPlanilla = $_SESSION['aPlanilla'];
	//recuperando el listado de formulas
	$sql = "SELECT p.sequen, p.ref_concept, p.details, p.state ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_type_fol_seq AS p ";
	$sql.= " WHERE p.fk_type_fol = ".$fk_type_fol;
	$sql.= " AND p.state = 1 ";
 	$sql.=" ORDER BY p.sequen ";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$ref_concept = $obj->ref_concept;
				$lRetc = s_calcula($ref_concept,$aPlanilla);
				$i++;
			}
		}
	}

	header('Location: '.DOL_URL_ROOT.'/salary/report/'.($creport?$creport:'rplanilla').'.php?action=edit&fk_period='.$fk_period);
	exit;

}

if ($action == 'proces_anterior' && $user->rights->salary->generasal)
{
	$fk_period = GETPOST('fk_period');
	//recuperamos los valores configurados en period
	$result = $objectpe->fetch($fk_period);
	if ($result>0)
	{
		$fk_type_fol = $objectpe->fk_type_fol;
		$fk_proces   = $objectpe->fk_proces;
	}
	//recuperando el listado de formulas
	$sql = "SELECT p.sequen, p.detail, p.formula, p.state ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_type_fol_det AS p ";
	$sql.= " WHERE p.fk_type_fol = ".$fk_type_fol;
	$sql.= " AND p.state = 1 ";
	$sql.=" ORDER BY p.sequen ";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$formula = $obj->formula;
				$formula = $formula."()";
				eval('$res = '.$formula.';');
		//echo '<hr>resultado '.$res;
				$i++;
			}
		}
	}
	//exit;
	header('Location: '.DOL_URL_ROOT.'/salary/report/rplanillacof.php?action=edit&fk_period='.$fk_period);
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
if ($action == 'create' && $user->rights->salary->generasal)
{
	$_SESSION['aPlanilla'] = array();
	print_fiche_titre($langs->trans("Proces salary"));

	print "<form action=\"planilla.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="proces">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// period
	print '<tr><td class="fieldrequired">'.$langs->trans('Period').'</td><td colspan="2">';
	print $objectpe->select_period($fk_period,'fk_period','','',1,1);
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

		print_barre_liste($langs->trans("Planilla"), $page, "planilla.php", "", $sortfield, $sortorder,'',$num);

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
		print_liste_field_titre($langs->trans("Diaspagado"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Hoursday"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Basico"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Bonoant"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Horasnum"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Horasamount"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Bonoprod"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Bonootro"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Domindias"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Dominamount"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Totalrend"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		if ($cPais == "BO")
			print_liste_field_titre($langs->trans("DescontAFP"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		else
		{
			print_liste_field_titre($langs->trans("DescontAFPRiesgo"),"liste.php", "p.state","","","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("DescontAFPVejez"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		}
		print_liste_field_titre($langs->trans("DescontRCIVA"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Descontotros"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Desconttotal"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Liquido"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Firma"),"liste.php", "p.state","","","",$sortfield,$sortorder);
		print "</tr>\n";
		$seq = 1;
	 //recuperamos el periodo
		$result = $objectpe->fetch($fk_period);
		if ($result)
		{
			$fk_type_fol = $objectpe->fk_type_fol;
			$fk_proces   = $objectpe->fk_proces;
		}
		foreach ((array) $aPlanilla AS $idUser => $dataUser)
		{
			$nTotalRend = 0;
			$nTotalDesc = 0;
			$nSumaAfp   = 0;
			$nSumaDesc  = 0;
			$objectAd->fetch($idUser);
			$objectU->fetch_user($idUser);
			$objectCh->fetch($objectU->fk_charge);
		 //buscando el calculo realizado en salary_present
		 //verificar si el concepto sera fijo o no
			$objres = search_planilla($idUser,4,$fk_period,$fk_proces,$fk_type_fol);

			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>'.$seq.'</td>';
			print '<td>'.$objectU->docum.'</td>';
			print '<td>'.$objectAd->nom.' '.$objectAd->prenom.'</td>';
		  // Country
			print '<td>';
			$img=picto_from_langcode($objectAd->country_code);
			if ($img) print $img.' ';
		  //print getCountry($objectAd->country_code);
			print '</td>';
			print '<td>'.dol_print_date($objectAd->naiss).'</td>';
			print '<td>'.select_sex($objectU->sex,'sex','','',1,1).'</td>';
			print '<td>'.$objectCh->ref.'</td>';
			print '<td>'.dol_print_date($objectU->date_ini).'</td>';
			print '<td align="right">'.$objres->hours.'</td>';
			print '<td align="right">'.$objres->hoursday.'</td>';
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalRend += $objres->amount;
		  //bono antiguedad
			$objres = search_planilla($idUser,6,$fk_period,$fk_proces,$fk_type_fol);
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalRend += $objres->amount;
		  //horas extras
			print '<td align="right">'.price(0).'</td>';
		  //monto horas extras
			print '<td align="right">'.price(0).'</td>';
		  //bono produccion
			print '<td align="right">'.price(0).'</td>';
		  //$nTotalRend += $objres->amount;

		  //otros bonos
			$objres = search_planilla($idUser,5,$fk_period,$fk_proces,$fk_type_fol,5);
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalRend += $objres->amount;

		  //dominical dias
			$objres = search_planilla($idUser,9,$fk_period,$fk_proces,$fk_type_fol,5);
			print '<td align="right">'.$objres->hours_info.'</td>';
		  //dominical monto
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalRend += $objres->amount;

		  //total rendimiento
			print '<td align="right">'.price($nTotalRend).'</td>';

			if ($cPais == "BO")
			{
		  //afp descont
				$objres = search_planilla($idUser,10,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaAfp += $objres->amount;
				$objres = search_planilla($idUser,11,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaAfp += $objres->amount;
				$objres = search_planilla($idUser,3,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaAfp += $objres->amount;
				$objres = search_planilla($idUser,2,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaAfp += $objres->amount;

				print '<td align="right">'.price($nSumaAfp).'</td>';
			}
			else
			{
		  //afp descont
				$objres = search_planilla($idUser,9,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaAfp += $objrs->amount;
				print '<td>'.$nSumaAfp.'</td>';

			}
			$nTotalDesc += $nSumaAfp;

		  //RC-IVA
			$objres = search_planilla($idUser,13,$fk_period,$fk_proces,$fk_type_fol,5);
			print '<td align="right">'.price($objres->amount).'</td>';
			$nTotalDesc += $objres->amount;

			if ($cPais == "BO")
			{
		  //DESCUENTO ANTICIPO
				$objres = search_planilla($idUser,14,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaDesc += $objres->amount;
				$objres = search_planilla($idUser,15,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaDesc += $objres->amount;
				$objres = search_planilla($idUser,16,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaDesc += $objres->amount;
				$objres = search_planilla($idUser,17,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaDesc += $objres->amount;
		  //otros descuentos
				$objres = search_planilla($idUser,70,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaDesc += $objres->amount;

				print '<td align="right">'.price($nSumaDesc).'</td>';
			}
			else
			{
		  //descuento anticipo
				$objres = search_planilla($idUser,14,$fk_period,$fk_proces,$fk_type_fol,5);
				$nSumaDesc += $objres->amount;
				print '<td>'.$nSumaDesc.'</td>';
			}
			$nTotalDesc += $nSumaDesc;
		  //total descuento
			print '<td align="right">'.price($nTotalDesc).'</td>';

		  //liquido
			print '<td align="right">'.price($nTotalRend - $nTotalDesc).'</td>';
			print '<td>_________________________________________</td>';

			print '</tr>';
			$seq++;
		}
		$db->free($result);
		print "</table>";
	 //boton action for aprobated

	}
}


llxFooter();

$db->close();
?>
