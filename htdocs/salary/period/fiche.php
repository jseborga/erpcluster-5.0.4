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
 *	\file       htdocs/salary/proces/fiche.php
 *	\ingroup    Proces
 *	\brief      Page fiche salary proces
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/salary/core/modules/salary/modules_salary.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';

//require_once DOL_DOCUMENT_ROOT.'/salary/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistory.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';


$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';
$error = '';
$mesgerror = '';
$object  = new Pperiodext($db);
$objectp = new Pproces($db);
$objectt = new Ptypefolext($db);

$objectU  = new Puserext($db);
$objectAd = new Adherent($db); //Adherent
$objectCh = new Pcharge($db); //charge
$objectsp = new Psalarypresentext($db); //salario actual
//$objectsh = new Psalaryhistory($db); // salario historial
$objectCo = new Pcontractext($db); // contratos
$formfile = new FormFile($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->period->creer)
{
	$date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$date_pay = dol_mktime(12, 0, 0, GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));
	$object->entity = $conf->entity;
	$object->fk_proces = GETPOST('fk_proces');
	$object->fk_type_fol = GETPOST('fk_type_fol');
	$object->mes = GETPOST('mes');
	$object->anio = GETPOST('anio');
	if (strlen($object->mes) == 1)
		$mes = '0'.$object->mes;
	else
		$mes = $object->mes;
	if (empty($object->anio))
	{
		$error++;
		$mesgerror .= '<br>'.$langs->trans('Erroryearrequired');
	}
	if ($object->fk_proces <=0)
	{
		$error++;
		$mesgerror .= '<br>'.$langs->trans('Errorprocesrequired');
	}
	if ($object->fk_type_fol<=0)
	{
		$error++;
		$mesgerror .= '<br>'.$langs->trans('Errortypefolrequired');
	}
	$object->ref   = $object->anio.$mes;
	$object->date_ini = $date_ini;
	$object->date_fin = $date_fin;
	$object->date_pay = $date_pay;
	$object->status_app= 0;
	$object->state    = 0;

	if ($object->ref && empty($error))
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: fiche.php?id=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		if ($error)
			$mesg='<div class="error">'.$mesgerror.'</div>';
		else
			$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
		$action="create";
	// Force retour sur page creation
	}
}

if ($action == 'builddoc')	// En get ou en post
{
	$result = $object->fetch($id);

	if ($result)
	{
		$object->modelpdf = GETPOST('model');
		$fk_type_fol = $object->fk_type_fol;
		$fk_proces   = $object->fk_proces;
		$mes         = $object->mes;
		$anio        = $object->anio;

		$_SESSION['aParamBoleta'] = array('fk_period'   => $id,
			'fk_proces'   => $fk_proces,
			'fk_type_fol' => $fk_type_fol,
			'mes'         => $mes,
			'anio'        => $anio );
	//carga los empleados
		s_cargamie();
	// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$result=boleta_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager,$_SESSION['aPlanilla']);
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
			exit;
		}
	}
}
// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->period->del)
{
	$object = new Pperiodext($db);
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/period/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	if ($object->fetch($_POST["id"]))
	{
		$date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
		$date_pay = dol_mktime(12, 0, 0, GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));
		$object->mes = GETPOST('mes');
		$object->anio = GETPOST('anio');

		$object->ref   = $object->anio.(strlen($object->mes)==1?'0'.$object->mes:$object->mes);

		$object->fk_proces = GETPOST('fk_proces');
		$object->fk_type_fol = GETPOST('fk_type_fol');
		$object->date_ini = $date_ini;
		$object->date_fin = $date_fin;
		$object->date_pay = $date_pay;
		$object->status_app+=0;
		if ( $object->update($user) > 0)
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="ok">'.$langs->trans('Updated record').'</div>';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->period->creer)
{
	print_fiche_titre($langs->trans("Newperiod"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="6" maxlength="6" disabled="disabled"> ('.$langs->trans('Aniomes').')';
	print '</td></tr>';

	// process
	print '<tr><td class="fieldrequired">'.$langs->trans('Proceso').'</td><td colspan="2">';
	print $objectp->select_proces($object->fk_proces,'fk_proces','','',1);
	print '</td></tr>';

	// typefol
	print '<tr><td class="fieldrequired">'.$langs->trans('Typefol').'</td><td colspan="2">';
	print $objectt->select_typefol($object->fk_type_fol,'fk_type_fol','','',1);
	print '</td></tr>';

	// month
	print '<tr><td class="fieldrequired">'.$langs->trans('Month').'</td><td colspan="2">';
	print select_month($object->mes,'mes','','',1);
	print '</td></tr>';

	// year
	print '<tr><td class="fieldrequired">'.$langs->trans('Year').'</td><td colspan="2">';
	print '<input id="anio" type="text" value="'.$object->anio.'" name="anio" size="3" maxlength="4">';
	print '</td></tr>';

	// dateini
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($object->date_ini,'di_','','','',"crearperiod",1,1);
	print '</td></tr>';

	// datefin
	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	$form->select_date($object->date_fin,'df_','','','',"crearperiod",1,1);
	print '</td></tr>';

	// datepay
	print '<tr><td class="fieldrequired">'.$langs->trans('Datepay').'</td><td colspan="2">';
	$form->select_date($object->date_pay,'dp_','','','',"crearperiod",1,1);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["id"])
	{
		dol_htmloutput_mesg($mesg);
		$result = $object->fetch($_GET["id"]);
		if ($result < 0)
		{
			dol_print_error($db);
		}


	  /*
	   * Affichage fiche
	   */
	  if ($action <> 'edit' && $action <> 're-edit')
	  {
	  //$head = fabrication_prepare_head($object);

	  	dol_fiche_head($head, 'period', $langs->trans("Period"), 0, 'period');

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->state = 1;
		  //update
	  	$object->update($user);
	  	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

	  }

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'revalidate')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->state = 0;
		  //update
	  	$object->update($user);
	  	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

	  }

	  // Confirm delete third party
	  if ($action == 'delete')
	  {
	  	$form = new Form($db);
	  	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiod"),$langs->trans("Confirmdeleteperiod",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	  	if ($ret == 'html') print '<br>';
	  }

	  print '<table class="border" width="100%">';

	  // ref
	  // print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
	  // print $object->ref;
	  // print '</td></tr>';

	  print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	  $linkback = '<a href="'.DOL_URL_ROOT.'/salary/period/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  print $form->showrefnav($object, 'id', $linkback);
	  print '</td></tr>';


	  // process
	  $objectp->fetch($object->fk_proces);
	  print '<tr><td>'.$langs->trans('Proceso').'</td><td colspan="2">';
	  print $objectp->ref.' '.$objectp->label;
	  print '</td></tr>';

	  // typefol
	  $objectt->fetch($object->fk_type_fol);
	  print '<tr><td>'.$langs->trans('Typefol').'</td><td colspan="2">';
	  print $objectt->ref.' '.$objectt->label;
	  print '</td></tr>';

	  // month
	  print '<tr><td>'.$langs->trans('Month').'</td><td colspan="2">';
	  print select_month($object->mes,'mes','','',1,1);
	  print '</td></tr>';

	  // year
	  print '<tr><td>'.$langs->trans('Year').'</td><td colspan="2">';
	  print $object->anio;
	  print '</td></tr>';

	  // dateini
	  print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	  print dol_print_date($object->date_ini,'daytext');
	  print '</td></tr>';

	  // datefin
	  print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	  print dol_print_date($object->date_fin,'daytext');
	  print '</td></tr>';

	  // datepay
	  print '<tr><td>'.$langs->trans('Datepay').'</td><td colspan="2">';
	  print dol_print_date($object->date_pay,'daytext');
	  print '</td></tr>';

	  // dateclose
	  print '<tr><td>'.$langs->trans('Dateclose').'</td><td colspan="2">';
	  print dol_print_date($object->date_close,'daytext');
	  print '</td></tr>';

	  // state
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	  print libState($object->state,5);
	  print '</td></tr>';

	  print '</table>';

	  print '</div>';


	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */

	  print "<div class=\"tabsAction\">\n";

	  if ($action == '')
	  {
	  	if ($user->rights->salary->period->creer)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	  	if ($user->rights->salary->period->creer && $object->state==0)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	  	if ($user->rights->salary->period->val && $object->state == 0)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Valid")."</a>";
	  	elseif($user->rights->salary->period->val && $object->state == 1)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=revalidate&id=".$object->id."\">".$langs->trans("Change")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";

	  	if ($user->rights->salary->period->del  && $object->state==0)
	  		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	  }
	  print "</div>";

	  print "<div class=\"tabsAction\">\n";
	  //documents
	  if ($object->state >= 5)
	  {

	  	print '<table width="100%"><tr><td width="50%" valign="top">';
		  print '<a name="builddoc"></a>'; // ancre

		  /*
		   * Documents generes
		   */

		  $filename=dol_sanitizeFileName($object->ref);

		  $filedir=$conf->salary->dir_output . '/' . dol_sanitizeFileName($object->ref);
		  $urlsource=$_SERVER['PHP_SELF'].'?id='.$object->id;
		  $genallowed=$user->rights->salary->period->creer;
		  $delallowed=$user->rights->salary->period->del;
		  print '<br>';
		  print $formfile->showdocuments('salary',$filename,$filedir,$urlsource,$genallowed,$delallowed,'boleta',1,0,0,28,0,'','','',$soc->default_lang,$hookmanager,'boleta');
		  $somethingshown=$formfile->numoffiles;
		  print '</td></tr></table>';

		}
		print "</div>";

	}


	  /*
	   * Edition fiche
	   */
	  if (($action == 'edit' || $action == 're-edit') && 1)
	  {
	  	print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

	  	print '<form action="fiche.php" method="POST">';
	  	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  	print '<input type="hidden" name="action" value="update">';
	  	print '<input type="hidden" name="id" value="'.$object->id.'">';

	  	print '<table class="border" width="100%">';

	  // ref
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="3" maxlength="3" disabled="disabled">';
	  	print '</td></tr>';

	  // process
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Proceso').'</td><td colspan="2">';
	  	print $objectp->select_proces($object->fk_proces,'fk_proces','','',1);
	  	print '</td></tr>';

	  // typefol
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Typefol').'</td><td colspan="2">';
	  	print $objectt->select_typefol($object->fk_type_fol,'fk_type_fol','','',1);
	  	print '</td></tr>';

	  // month
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Month').'</td><td colspan="2">';
	  	print select_month($object->mes,'mes','','',1);
	  	print '</td></tr>';

	  // year
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Year').'</td><td colspan="2">';
	  	print '<input id="anio" type="text" value="'.$object->anio.'" name="anio" size="3" maxlength="4">';
	  	print '</td></tr>';

	  // dateini
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	  	$form->select_date($object->date_ini,'di_','','','',"crearperiod",1,1);
	  	print '</td></tr>';

	  // datefin
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	  	$form->select_date($object->date_fin,'df_','','','',"crearperiod",1,1);
	  	print '</td></tr>';

	  // datepay
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Datepay').'</td><td colspan="2">';
	  	$form->select_date($object->date_pay,'dp_','','','',"crearperiod",1,1);
	  	print '</td></tr>';

	  	print '</table>';

	  	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	  	print '</form>';

	  }
	}
}


llxFooter();

$db->close();
?>
