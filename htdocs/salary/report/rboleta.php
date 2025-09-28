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
 *	\file       htdocs/salary/report/rplanilla.php
 *	\ingroup    Planilla
 *	\brief      Page fiche salary planilla
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfield.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictable.class.php';


require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent_type.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/formula/lib/formula.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/core/modules/salary/modules_salary.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");
$action=GETPOST('action');

$id        = GETPOST("id");
$fk_adherent = GETPOST('fk_adherent');
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$lCopy = false;
$mesg = '';

$object   = new Pformulas($db);
$objectsh = new Psalaryhistoryext($db);
$objectpe   = new Pperiodext($db); //periodos
$objectpr = new Pproces($db); //procesos
$objecttf = new Ptypefolext($db);//procedimientos
$objectsp = new Psalarypresentext($db); //salario actual
$objectf  = new Pformulas($db); //formula
$objecto  = new Poperator($db);
$objectU  = new Puserext($db);
$objectUb = new Puserbonus($db);
$objectUs = new User($db);
$objectAd = new Adherent($db); //Adherent
$objectCh = new Pchargeext($db); //charge
$objectgf = new Pgenericfield($db); //generic field
$objectgt = new Pgenerictable($db); //generic table

//determina el pais
$aPais = explode(":",$conf->global->MAIN_INFO_SOCIETE_PAYS);

$cPais = $aPais[1];
$_SESSION['param']['nDiasTrab'] = $conf->global->SALARY_NRO_DIAS_LABORAL;

$fk_period = GETPOST('fk_period');
$fk_adherent = GETPOST('fk_adherent');

/*
 * Actions
 */
$result = $objectpe->fetch($fk_period);

// Add
if ($action == 'proces' && $user->rights->salary->crearrsal)
{
    //recuperamos los valores configurados en period
	if (empty($fk_period) || $fk_period <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Period")), null, 'errors');
		$action = 'create';
	}
	if (!$error)
	{
		if ($result)
		{
			$fk_type_fol = $objectpe->fk_type_fol;
			$fk_proces   = $objectpe->fk_proces;
			$mes         = $objectpe->mes;
			$anio        = $objectpe->anio;
		}
		$_SESSION['aParamBoleta'] = array('fk_period'   => $fk_period,
			'fk_proces'   => $fk_proces,
			'fk_type_fol' => $fk_type_fol,
			'mes'         => $mes,
			'anio'        => $anio );
		if (!empty($fk_adherent) && $fk_adherent > 0)
		{
			$objectU->fetch_user($fk_adherent);
			$objectpe->fetch($fk_period);
			$objectpe->fk_adherent = $fk_adherent;
			$idUser = $fk_adherent;
			$_SESSION['aPlanilla'][$fk_adherent] = array('id'         => $fk_adherent,
				'basic'      => $objectU->basic,
				'date_ini'   => $objectU->date_ini,
				'date_fin'   => $objectU->date_fin,
				'date_fin_p' => $objectpe->date_fin,
				'date_ini_p' => $objectpe->date_ini);
		}
		else
		{
			s_cargamie();
		}
		header("Location: rboleta.php?action=edit&fk_period=".$fk_period.'&fk_adherent='.$fk_adherent);
		exit;
	}
}


//actions
	// Remove file in doc form
if ($action == 'remove_file')
{
	require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

	$langs->load("other");
	$upload_dir = $conf->salary->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

	$file = $upload_dir . '/' . GETPOST('file');
	$ret = dol_delete_file($file, 0, 0, 0, $product);
	if ($ret)
		setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
	else
		setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
	$action = '';
}

if ($action == 'builddoc')	// En get ou en post
{
	$objectpe->fetch($fk_period);
    //$object->fetch_thirdparty();

    // if (GETPOST('model'))
    //   {
    //     $object->setDocModel($user, GETPOST('model'));
    //   }
    // Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$objectpe->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$objectpe->fk_adherent = $fk_adherent;
	$result = $objectpe->generateDocument('boletacof', $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?fk_period='.$objectpe->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
		exit;
	}
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
$formfile = new Formfile($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);
//creando el proceso de elaboracion planilla
//formulario de configuracion
if ($action == 'create' && $user->rights->salary->crearrsal)
{
	$_SESSION['aPlanilla'] = array();

	print_fiche_titre($langs->trans("Paymentslip"));

	print "<form action=\"rboleta.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="proces">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

    // period
	print '<tr><td class="fieldrequired">'.$langs->trans('Period').'</td><td colspan="2">';
	print $objectpe->select_period($fk_period,'fk_period',' required="required"','',1,2);
	print '</td></tr>';

    // Adherent
	print '<tr><td>'.$langs->trans('Adherent').'</td><td colspan="2">';
	print select_adherent($fk_adherent,'fk_adherent','','',1);
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
	 //armando las boletas de sueldo
		$aPlanilla = $_SESSION['aPlanilla'];
		$lPeriodClose = true;
	 //impresion pdf
		print '<form action="rboleta.php" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="builddoc">';
		print '<input type="hidden" name="id" value="1">';
		print '<input type="hidden" name="fk_period" value="'.$fk_period.'">';
		print '<input type="hidden" name="fk_adherent" value="'.$fk_adherent.'">';

		print '<input type="hidden" name="model" value="crabe">';
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';
		print '</form>';

		$seq = 1;
	 	//recuperamos el periodo
		$result = $objectpe->fetch($fk_period);
		if ($result)
		{
			$fk_type_fol = $objectpe->fk_type_fol;
			$fk_proces   = $objectpe->fk_proces;
			$mes = $objectpe->mes;
			$anio = $objectpe->anio;
		}
		$htmlRes = '';
		$aHistoryref = array();
		foreach ((array) $aPlanilla AS $idUser => $dataUser)
		{
			$html = '';
	     //hoja tamaño carta portroit
			$html.= '<table  class="border" width="100%">';
			$html.= '<tr>';
			$html.= '<td colspan="6" align="center">'.$langs->trans('Planilladehaberes').'</td>';
			$html.= '</tr>';
			$html.= '<td colspan="6" align="center">'.month_literal($mes).'-'.$anio.'</td>';

			$nTotalRend = 0;
			$nTotalDesc = 0;
			$nSumaAfp   = 0;
			$nSumaDesc  = 0;
			$objectAd->fetch($idUser);
			$objectU->fetch_user($idUser);
			$objectCh->fetch($objectU->fk_charge);
	     //buscando el calculo realizado en salary_present
	     //verificar si el concepto sera fijo o no
			$objres = search_planilla($idUser,4,$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
	     //copia del period, proces, type_fol para ver estado
			if ($lCopy == false){
				$objres_cop = $objres;
				$lCopy = true;
			}
			$var=!$var;

			$html.= '<tr>';
			$html.= '<td colspan="2">'.$objectAd->firstname.'</td>';
			$html.= '<td colspan="2">'.$objectAd->nom.'</td>';
			$html.= '<td colspan="2">'.$objectAd->nom.'</td>';
			$html.= '</tr>';

			$html.= '<tr>';
			$html.= '<td colspan="2">'.$objectAd->lastname.'</td>';
			$html.= '<td colspan="2">'.$objectAd->prenom.'</td>';
			$html.= '<td colspan="2">'.$objectAd->prenom.'</td>';
			$html.= '</tr>';

			$html.= '<tr>';
			$html.= '<td colspan="2">'.$objectCh->ref.'</td>';
			$html.= '<td colspan="2">&nbsp;</td>';
			$html.= '<td colspan="2">'.$objectU->basic.'</td>';
			$html.= '</tr>';

			$html.= '<tr class="liste_titre">';
			$html.= '<td>'.$langs->trans('Concept').'</td>';
			$html.= '<td>'.$langs->trans('Factor').'</td>';
			$html.= '<td colspan="2" align="right">'.$langs->trans('Ingresos').'</td>';
			$html.= '<td colspan="2" align="right">'.$langs->trans('Desconts').'</td>';
			$html.= '</tr>';
			$sql = "SELECT p.fk_concept, p.amount, p.hours, p.ref, p.period_year, ";
			$sql.= " c.detail, c.print, c.type_cod, c.type_mov ";
			$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history AS p ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON p.fk_concept = c.rowid ";
			$sql.= " WHERE ";
			$sql.= " p.entity = ".$conf->entity ." AND ";
			$sql.= " p.fk_period = ".$fk_period ." AND ";
			$sql.= " p.fk_proces = ".$fk_proces ." AND ";
			$sql.= " p.fk_type_fol = ".$fk_type_fol ." AND ";
			$sql.= " p.fk_user = ".$idUser ;
	     //$sql.= " p.state IN (4,5) ";
			$sql.= " ORDER BY c.type_cod,c.ref ";
			$resql = $db->query($sql);

			$aReportboleta=array();
			if ($resql)
			{
				$num = $db->num_rows($resql);

				//print_r($num);
				//exit;

				$i = 0;
				if ($num)
				{
					$nSumaIng = 0;
					$nSumaEgr = 0;
					$var=True;
					while ($i < $num)
					{
						$obj = $db->fetch_object($resql);

						//print_r($obj);

						$aHistoryref[$idUser]['ref'] = $obj->ref;
						$aHistoryref[$idUser]['period_year'] = $obj->period_year;

						if ( $obj->print == 1 && $obj->amount > 0)
						{
							$html.= '<tr>';
							$html.= '<td>'.$obj->detail.'</td>';
							$html.= '<td>'.$obj->hours.'</td>';
							if ($obj->type_cod == 1)
							{
								$html.= '<td align="right" colspan="2">'.price($obj->amount).'</td>';
								$html.= '<td colspan="2">&nbsp;</td>';
								$nSumaIng += $obj->amount;
								$aReportboletaIng[$idUser][]=array('detail'=>$obj->detail,'hours'=>$obj->hours,'amount'=>$obj->amount,'type_cod'=>1,'type_mov'=>$obj->type_mov,'cont'=>$i);

							}
							if ($obj->type_cod == 2)
							{
								$html.= '<td colspan="2">&nbsp;</td>';
								$html.= '<td align="right" colspan="2">'.price($obj->amount).'</td>';
								$nSumaEgr += $obj->amount;

								$aReportboletaDes[$idUser][]=array('detail'=>$obj->detail,'hours'=>$obj->hours,'amount'=>$obj->amount,'type_cod'=>2,'type_mov'=>$obj->type_mov,'cont'=>$i);

							}
							$html.= '</tr>';
						}

						$i++;

					}
		     		//sumas totales
		     		//$aReportboletaTot[]=array('nSumaIng'=>$nSumaIng,'nSumaEgr'=>$nSumaEgr);
					$html.= '<tr>';
					$html.= '<td align="right" colspan="2">'.$langs->trans('Total').'</td>';
					$html.= '<td align="right" colspan="2">'.price($nSumaIng).'</td>';
					$html.= '<td align="right" colspan="2">'.price($nSumaEgr).'</td>';

					$html.= '</tr>';
		     		//liquido
					$nLiquid = $nSumaIng - $nSumaEgr;
					$html.= '<tr>';
					$html.= '<td align="right" colspan="2">'.$langs->trans('Liquid').'</td>';
					$html.= '<td align="right" colspan="2">'.price($nLiquid).'</td>';
					$html.= '<td align="right" colspan="2">&nbsp;</td>';

					$html.= '</tr>';

				}
			}
			$html.= '</table>';
			//$html.= '<br><br>'.$html;
			print    $htmlUsu = '<div width="1200px" height="2700px">'.$html.'</div>';
			$htmlRes .= $htmlUsu;
			$seq++;
		}
		$_SESSION['aReportpdfdetIng'] = serialize($aReportboletaIng);
		$_SESSION['aReportpdfdetDes'] = serialize($aReportboletaDes);
		$_SESSION['aHistoryref'] = serialize($aHistoryref);
		//$_SESSION['aReportpdfdetTot'] = serialize($aReportboletaTot);

		$db->free($result);

		$htmlRes;


		/* **************************************** */
		/*                                          */
		/* Barre d'action                           */
		/*                                          */
		/* **************************************** */

		/*
		print '<div class="tabsAction">';
		if ($action == 'edit' && $user->rights->salary->validsal)
		{
			$_SESSION['validateSalary'] = array('fk_period' => $fk_period,
				'fk_proces' => $fk_proces,
				'fk_type_fol' => $fk_type_fol);
			if ($objres_cop->state==0)
				print '<a class="butAction" href="rplanilla.php?action=generate&state=1">'.$langs->trans("Procesate").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Procesate")."</a>";
			print '&nbsp;';
			if ($objres_cop->state == 1)
				print '<a class="butAction" href="rplanilla.php?action=generate&state=2">'.$langs->trans("Procesate2").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Procesate2")."</a>";
			if( $objres_cop->state == 2)
				print '<a class="butAction" href="rplanilla.php?action=generate&state=3">'.$langs->trans("Procesate3")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Procesate3")."</a>";
			if ($objres_cop->state==3)
				print '<a class="butAction" href="rplanilla.php?action=generate&state=4">'.$langs->trans("Procesate4")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Procesate4")."</a>";
			if ($objres_cop->state==4)
				print '<a class="butAction" href="rplanilla.php?action=close&state=5">'.$langs->trans("Close")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Close")."</a>";
		}
		print '</div>';
		*/
	}
}




print '<div class="fichecenter">';
/*
 * Documents generes
 */
$filename = dol_sanitizeFileName('boleta');
$filedir = $conf->salary->dir_output . "/boleta/".($objectpe->anio?$objectpe->anio:date('Y'));
$urlsource = $_SERVER["PHP_SELF"] . "?fk_period=" . $fk_period;
//if ($fk_period) $genallowed = $user->rights->salary->crearbsal;
$genallowed = false;
$delallowed = $user->rights->salary->delrbsal;

$var = true;
	//$modelpdf = 'boletacofa';
$modelpdf = 'boletacof';


$somethingshown = $formfile->show_documents('salary', $filename, $filedir, $urlsource, $genallowed, $delallowed, $modelpdf, 1, 0, 0, 28, 0, '', 0, '', $soc->default_lang);
print '</div>';

llxFooter();

$db->close();
?>
