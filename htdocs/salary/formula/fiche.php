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
 *	\file       htdocs/salary/formula/fiche.php
 *	\ingroup    Formulas
 *	\brief      Page fiche salary formulas
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulasdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictableext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
include_once DOL_DOCUMENT_ROOT.'/salary/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/formula/lib/formula.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';

$conf_db_type = $dolibarr_main_db_type;

$langs->load("salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$rid       = GETPOST("rid");

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$mesg = '';

$object  = new Pformulas($db);
$objectd = new Pformulasdetext($db);
$objecto = new Poperator($db);
$objectUb= new Puserbonus($db);
$objectgt= new Pgenerictableext($db);
$objectC = new Pconceptext($db);
$objectU = new Puserext($db);
$objectpe = new Pperiodext($db); //periodo
$objectCo = new Pcontractext($db);
$objectsp = new Psalarypresentext($db);
$objectsh = new Psalaryhistoryext($db);

// $sql = "SELECT rowid, type, changefull FROM ".MAIN_DB_PREFIX."p_formulas_det ";
// echo $sql.= " WHERE type IN ('p_concept','p_generic_table') ";

// $result = $db->query($sql);
// if ($result)
//   {
//     echo '<hr>x '.$num = $db->num_rows($result);
//     $i = 0;
//     if ($num)
//       {
// 	$var=True;
// 	while ($i < min($num,$limit))
// 	  {
// 	    $obj = $db->fetch_object($result);
// 	    if ($obj->type == 'p_concept')
// 	      {
// 		$aArray = explode('|',$obj->changefull);
// 		if (count($aArray) == 1)
// 		  {
// 		    //remplazamos
// 		    $objectC->fetch(trim($obj->changefull));

// 		    $objectd->fetch($obj->rowid);
// 		    $objectd->changefull = $objectC->entity.'|'.$objectC->codref;
// 		    $objectd->update($user);
// 		  }
// 	      }
// 	    elseif($obj->type == 'p_generic_table')
// 	      {
// 		$aArray = explode('|',$obj->changefull);
// 		if (count($aArray) == 1)
// 		  {
// 		    //remplazamos
// 		    $objectgt->fetch($obj->changefull);
// 		    $objectd->fetch($obj->rowid);
// 		    $objectd->changefull = $objectgt->entity.'|'.$objectgt->table_cod.'|'.$objectgt->sequen;
// 		    $objectd->update($user);
// 		  }
// 	      }
// 	    $i++;
// 	  }
//       }
//   }
// 	  exit;


/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->formula->creer)
{
	$object->ref     = $_POST["ref"];
	$object->detail  = GETPOST('detail');
	$object->entity  = $conf->entity;
	$object->state   = 0;

	if ($object->ref)
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
		$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
	$action="create";   // Force retour sur page creation
}
}

// Adddet
if ($action == 'adddet' && $user->rights->salary->formula->creer && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$object->fetch($id);
	$cCampuser  = GETPOST('cCampuser');
	if ($cCampuser == -1)
		$cCampuser = 0;
	$cGeneric    = GETPOST('cGeneric');
	if ($cGeneric == -1)
		$cGeneric = 0;
	$cConcept    = GETPOST('cConcept');
	if ($cConcept == -1)
		$cConcept = 0;

	$nmonth = GETPOST('nmonth');
	if ($nmonth<0 || empty($nmonth)) $nmonth = 0;
	$nValor      = GETPOST('nValor');
	$cFormula    = GETPOST('cFormula');

	$nValida = !empty($cCampuser) + !empty($cGeneric) + !empty($cConcept) + !empty($nValor) + !empty($cFormula);


	$andor       = GETPOST('andor');
	//validando los campos
	if ($nValida > 1)
		$error++;
	if (empty($andor))
		$error++;
	if (empty($error))
	{
		//registrando el valor a la variable
		if (!empty($cCampuser))
		{
			$objectd->changefull = $cCampuser;
			$objectd->type       = 'p_users';
		}
		if (!empty($cGeneric))
		{
			$objectgt->fetch($cGeneric);
			$objectd->changefull = $objectgt->entity.'|'.$objectgt->table_cod.'|'.$objectgt->sequen;
			$objectd->type       = 'p_generic_table';
		}
		if (!empty($cConcept))
		{
			$objectC->fetch($cConcept);
			$objectd->changefull = $objectC->entity.'|'.$objectC->ref;
			$objectd->type       = 'p_concept';
		}
		if (!empty($nValor))
		{
			$objectd->changefull = $nValor;
			$objectd->type       = 'valor';
		}
		if (!empty($cFormula))
		{
			$objectd->changefull = $cFormula;
			$objectd->type       = 'formula';
		}
		//$objectd->fk_formula = $id;
		$objectd->entity = $conf->entity;
		$objectd->ref_formula = $object->ref;
		$objectd->nmonth = $nmonth;
		$objectd->fk_operator = GETPOST('fk_operator');
		$objectd->andor   = GETPOST('andor');
		$objectd->sequen  = $objectd->sequen_det($object->ref);
		$objectd->state   = 1;
		if ($objectd->type)
		{
			$rid = $objectd->create($user);
			if ($rid > 0)
			{
				header("Location: fiche.php?id=".$id);
				exit;
			}
			$action = 'createdet';
			$mesg='<div class="error">'.$objectd->error.'</div>';
			setEventMessages($objectd->error,$objectd->errors,'errors');

		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
			setEventMessages($langs->trans("Errorrefnamerequired"),null,'errors');

			$action="createdet";
		}
	}
	else
	{
		setEventMessages($langs->trans("Errorselectusergenericvaluemustbeunique"),null,'errors');
		$mesg='<div class="error">'.$langs->trans("Errorselectusergenericvaluemustbeunique").'</div>';
		$action="createdet";
		$_GET['id'] = $_POST['id'];
	}
}

// Adddet
if ($action == 'updatedet' && $user->rights->salary->formula->creer)
{
	$objectd->fetch(GETPOST('rid'));
	$cCampuser  = GETPOST('cCampuser');
	if ($cCampuser == -1)
		$cCampuser = 0;
	$cGeneric    = GETPOST('cGeneric');
	if ($cGeneric == -1)
		$cGeneric = 0;
	$cConcept    = GETPOST('cConcept');
	if ($cConcept == -1)
		$cConcept = 0;
	$nmonth = GETPOST('nmonth');
	if ($nmonth<0 || empty($nmonth)) $nmonth = 0;
	$nValor      = GETPOST('nValor');
	$cFormula    = GETPOST('cFormula');
	$nValida = !empty($cCampuser) + !empty($cGeneric) + !empty($cConcept) + !empty($nValor) + !empty($cFormula);
	$andor       = GETPOST('andor');

	//validando los campos
	if ($nValida > 1)
		$error++;
	if (empty($andor))
		$error++;
	if (empty($error))
	{
	//registrando el valor a la variable
		if (!empty($cCampuser))
		{
			$objectd->changefull = $cCampuser;
			$objectd->type       = 'p_users';
		}
		if (!empty($cGeneric))
		{
			$objectgt->fetch($cGeneric);
			$objectd->changefull = $objectgt->entity.'|'.$objectgt->table_cod.'|'.$objectgt->sequen;
			$objectd->type       = 'p_generic_table';
		}
		if (!empty($cConcept))
		{
			$objectC->fetch($cConcept);
			$objectd->changefull = $objectC->entity.'|'.$objectC->ref;
			$objectd->type       = 'p_concept';
		}

	// if (!empty($cGeneric))
	//   {
	//     $objectd->changefull = $cGeneric;
	//     $objectd->type       = 'p_generic_table';
	//   }
	// if (!empty($cConcept))
	//   {
	//     $objectd->changefull = $cConcept;
	//     $objectd->type       = 'p_concept';
	//   }
		if (!empty($nValor))
		{
			$objectd->changefull = $nValor;
			$objectd->type       = 'valor';
		}
		if (!empty($cFormula))
		{
			$objectd->changefull = $cFormula;
			$objectd->type       = 'formula';
		}
		$objectd->nmonth = $nmonth;
		$objectd->fk_operator = GETPOST('fk_operator');
		$objectd->andor   = GETPOST('andor');
		if ($objectd->type)
		{
			$objectd->update($user);
			header("Location: fiche.php?id=".$id);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
		$action="editdet";   // Force retour sur page creation
	}
}
else
{
	echo 'mensaje de error';
	$mesg='<div class="error">'.$langs->trans("Errorselectusergenericvaluemustbeunique").'</div>';
	$action="editdet";   // Force retour sur page creation
	$_GET['id'] = $_POST['id'];
	$_GET['rid'] = $_POST['rid'];
}
}

// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->formula->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/formula/liste.php');
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
		$object->ref          = $_POST["ref"];
		$object->detail  = GETPOST('detail');
		if ( $object->update($_POST["id"], $user) > 0)
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
		//$mesg = '<div class="ok">Fiche mise a jour</div>';
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
$aMonth = monthArray($langs,0);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->formula->creer)
{
	print_fiche_titre($langs->trans("Newformula"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="4" maxlength="4">';
	print '</td></tr>';
	// detail
	print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
	print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="40" maxlength="40">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);

		$result = $object->fetch($id);
		if ($result < 0)
		{
			dol_print_error($db);
		}


	// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
	  //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Formula"), 0, 'formula');

	//Confirmation de la validation
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
		// Confirmation de la validation
			if ($action == 'revalidate')
			{
				$object->fetch(GETPOST('id'));
		 //cambiando a validado
				$object->state = 0;
		 //update
				$object->update($user);
				$action = '';
			}

	  // Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}
			if ($action == 'deletedet' && $user->rights->salary->formula->del)
			{
				$objectd->fetch($rid);
				$objectd->state = -1;
				$objectd->update($user);
				$action = '';
			}
			print '<table class="border" width="100%">';

	  // ref
			print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';

			$linkback = '<a href="'.DOL_URL_ROOT.'/salary/formula/liste.php">'.$langs->trans("BackToList").'</a>';

			print '<td class="valeur"  colspan="2">';
			print $form->showrefnav($object, 'ref', '',1,'ref');
			print '</td></tr>';

			print '</tr>';
	  // detail
			print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="2">';
			print $object->detail.' ('.$object->ref.')';
			print '</td></tr>';
	  // state
			print '<tr><td>'.$langs->trans('Statut').'</td><td colspan="2">';
			print libState($object->state,5);
			print '</td></tr>';

			print "</table>";

			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				if ($user->rights->salary->formula->creer && $object->state == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->salary->formula->val && $object->state==0)
					print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Valid")."</a>";
				elseif ($user->rights->salary->formula->val && $object->state==1)
					print "<a class=\"butAction\" href=\"fiche.php?action=revalidate&id=".$object->id."\">".$langs->trans("Change")."</a>";

				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";

				if ($user->rights->salary->formula->del && $object->state==0)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}
			print "</div>";

			if ($action == "createdet" || $action == 'editdet')
			{
				if ($action == 'editdet')
				{
					$cCampuser = -1;
					$cGeneric  = -1;
					$cConcept  = -1;
					$nValor    = 0;
					$cFormula  = "";
					$objectd->fetch($rid);
					$nmonth = $objectd->nmonth;
					$andor = $objectd->andor;
					if ($objectd->type == 'p_users')
						$cCampuser = $objectd->changefull;
					elseif($objectd->type == 'p_concept')
					{
						list($nEntity,$cRef) = explode('|',$objectd->changefull);
						$objectC->fetch_ref($cRef);
						$cConcept = $objectC->id;
					}
					elseif($objectd->type == 'p_generic_table')
					{
						list($nEntity,$cTableCod,$nSequen) = explode('|',$objectd->changefull);
						$objectgt->fetch_table_cod($cTableCod,$nSequen);
			  //$cConcept = $objectgt->id;
						$cGeneric = $objectgt->id;
			  //$cGeneric = $objectd->changefull;
					}
					elseif($objectd->type == 'valor')
						$nValor = $objectd->changefull;
					elseif($objectd->type == 'formula')
						$cFormula = $objectd->changefull;
				}
		 	//$aArrayCam = listColumn(MAIN_DB_PREFIX.'p_contract',$conf_db_type);
				$x2 = 1;
				$aTable = $db->DDLInfoTable (MAIN_DB_PREFIX.'p_contract');
				foreach ((array) $aTable AS $j => $data)
				{
					$aArrayCam[$j]['column_name'] = $data[0];
				}
				foreach ((array) $aArrayCam AS $x1 => $dataCam)
				{
					if ($dataCam['column_name'] != 'date_fin' &&
						$dataCam['column_name'] != 'ref' && $dataCam['column_name'] != 'state' &&
						$dataCam['column_name'] != 'rowid' && substr($dataCam['column_name'],0,3) != 'fk_')

//		 		if ($dataCam['column_name'] != 'date_ini' && $dataCam['column_name'] != 'date_fin' &&
//		 			$dataCam['column_name'] != 'ref' && $dataCam['column_name'] != 'state' &&
//		 			$dataCam['column_name'] != 'rowid' && substr($dataCam['column_name'],0,3) != 'fk_')
					{
						$countryArray[$x2]['rowid'] = $dataCam['column_name'];
						$countryArray[$x2]['label'] = $langs->trans($dataCam['column_name']);
						$label[$x2] = $countryArray[$x2]['label'];
						$x2++;
					}
				}
				print "\n".'<script type="text/javascript" language="javascript">';
				print '$(document).ready(function () {

					$("#selectcConcept").change(function() {
						document.formf.action.value="'.$action.'";
						document.formf.submit();
					});
				});';
				print '</script>'."\n";

				print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

				print '<form name="formf" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="id" value="'.$object->id.'">';

				if ($action == 'editdet') print '<input type="hidden" name="action" value="updatedet">';
				else print '<input type="hidden" name="action" value="adddet">';
				if ($action == 'editdet') print '<input type="hidden" name="rid" value="'.$objectd->id.'">';

				dol_fiche_head();
				print '<table class="border" width="100%">';
		  // operator
				print '<tr><td class="fieldrequired">'.$langs->trans('Operator').'</td><td colspan="2">';
				print $objecto->select_operator((GETPOST('fk_operator')?GETPOST('fk_operator'):$objectd->fk_operator),'fk_operator','',0,0);
				print '</td></tr>';

		  // campoPUser
				print '<tr><td>'.$langs->trans('Campouser').'</td><td colspan="2">';
				$cCampuser = (GETPOST('cCampuser')?GETPOST('cCampuser'):$cCampuser);
				$out = print_select($cCampuser,'cCampuser','',0,1,0,$countryArray,$label);
				print $out;
				print '</td></tr>';

		  // generic
				$cGeneric = (GETPOST('cGeneric')?GETPOST('cGeneric'):$cGeneric);
				print '<tr><td>'.$langs->trans('oGeneric').'</td><td colspan="2">';
				print $objectgt->select_generic_table_field($cGeneric,'cGeneric','',0,1);
				print '</td></tr>';

		  // concept
				$cConcept = (GETPOST('cConcept')?GETPOST('cConcept'):$cConcept);
				print '<tr><td>'.$langs->trans('oConcept').'</td><td colspan="2">';
				print $objectC->select_concept($cConcept,'cConcept','',0,1);
				print '</td></tr>';

				if ($cConcept>0)
				{
					$nmonth = (GETPOST('nmonth')?GETPOST('nmonth'):$nmonth);
					print '<tr><td>'.$langs->trans('Monthconcept').'</td><td colspan="2">';
					print $form->selectarray('nmonth',$aMonth,$nmonth,1);
					print '</td></tr>';
				}
		  //amount
				$nValor = (GETPOST('nValor')?GETPOST('nValor'):$nValor);
				print '<tr><td>'.$langs->trans('oValue').'</td><td colspan="2">';
				print '<input id="nValor" type="text" value="'.$nValor.'" name="nValor" size="7">';
				print '</td></tr>';

		  //cformula
				$cFormula = (GETPOST('cFormula')?GETPOST('cFormula'):$cFormula);
				print '<tr><td>'.$langs->trans('oFormula').'</td><td colspan="2">';
				print '<input id="cFormula" type="text" value="'.$cFormula.'" name="cFormula" size="7">';
				print '</td></tr>';

		  // andor
				$andor = (GETPOST('andor')?GETPOST('andor'):$andor);
				print '<tr><td class="fieldrequired">'.$langs->trans('Andor').'</td><td colspan="2">';
				print select_andor($andor,'andor',$htmloption='',0,1);
				print '</td></tr>';

				print '</table>';
				dol_fiche_end();
				print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
				print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

				print '</form>';

			}

			//lista el detalle de formulas
			$sql = "SELECT a.ref_formula,a.rowid AS id, a.fk_operator, a.type, a.changefull, a.sequen, a.state, ";
			$sql.= " b.detail, b.operator, b.type AS typeOperator ";
			$sql.= " FROM ".MAIN_DB_PREFIX."p_formulas_det AS a ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_operator AS b ON a.fk_operator = b.rowid ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_formulas AS c ON a.ref_formula = c.ref AND a.entity = c.entity ";
			$sql.= " WHERE c.entity = ".$conf->entity;
			$sql.= " AND a.ref_formula = '".$object->ref."' ";
			$sql.= " AND a.state != -1";
			//$sql.= " AND c.state = 1";
			$sql.= " ORDER BY a.sequen ";
			$sql.= $db->plimit($limit+1, $offset);

			$result = $db->query($sql);
			if ($result)
			{
				$num = $db->num_rows($result);
				$i = 0;
				$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
				print_barre_liste($langs->trans("Liste formulas details"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

				print '<table class="noborder" width="100%">';

				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Sequen"),"liste.php", "a.sequen","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Operator"),"liste.php", "b.operator","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Details"),"liste.php", "b.detail","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Type"),"liste.php", "a.type","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Id"),"liste.php", "a.changefull","","","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Action"));
				print "</tr>\n";
				if ($num) {
					$var=True;
					while ($i < min($num,$limit))
					{
						$obj = $db->fetch_object($result);
			// if (empty($obj->ref_formula))
			//   {
			// 	$objectd->fetch($obj->id);
			// 	$objectd->ref_formula = $object->codref;
			// 	$objectd->update($user);
			//   }
						$var=!$var;
						print "<tr $bc[$var]>";
						print '<td><a href="fiche.php?id='.$object->id.'&idd='.$obj->id.'">'.img_object($langs->trans("Ref"),'generic').' '.$obj->sequen.'</a></td>';
						print '<td>'.$obj->operator.'</td>';
						print '<td>'.$obj->detail.'</td>';
						print '<td>'.$obj->type.'</td>';
						if ($obj->type == 'p_concept')
						{
							list($nEntity,$cRef) = explode('|',$obj->changefull);
							$objectC->fetch('',$cRef);
							if ($objectC->ref == $cRef)
								print '<td>'.$objectC->detail.'</td>';
							else
								print '<td>&nbsp;</td>';
						}
						elseif ($obj->type == 'p_generic_table')
						{
							list($nEntity,$cTableCod,$nSequen) = explode('|',$obj->changefull);
							$objectgt->fetch_table_cod($cTableCod,$nSequen);
							print '<td>'.$objectgt->table_name.' : '.$objectgt->field_name.'</td>';
						}
						else
							print '<td>'.$obj->changefull.'</td>';
			 //action

						print '<td>';
						if ($object->state != 1)
						{
							print '<center>';
							print '<a href="fiche.php?action=editdet&id='.$object->id.'&rid='.$obj->id.'">'.img_picto($langs->trans("Edit"),'edit').'</a>';
							print '&nbsp;&nbsp;';
							print '<a href="fiche.php?action=deletedet&id='.$object->id.'&rid='.$obj->id.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';

							print '</center>';
						}
						print '</td>';

						print "</tr>\n";
						$i++;
					}
				}

				$db->free($result);

				print "</table>";

				/* ************************************************************************** */
				/*                                                                            */
				/* Barre d'action                                                             */
				/*                                                                            */
				/* ************************************************************************** */

				print "<div class=\"tabsAction\">\n";

				if ($action == '' && !empty($id))
				{
					if ($user->rights->salary->formula->creer && $object->state != 1)
						print '<a class="butAction" href="fiche.php?action=createdet&id='.$object->id.'">'.$langs->trans("Create").'</a>';
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";
				}
				print "</div>";
				$fk_adherent = GETPOST('fk_adherent');
				$fk_period   = GETPOST('fk_period');

		  //registro de prueba
				if ($num)
				{
					print_fiche_titre($langs->trans("Prueba"), $mesg);

					print '<form action="fiche.php" method="POST">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="ejecutedet">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';

					print '<table class="border" width="100%">';

		  // User
					print '<tr><td class="fieldrequired">'.$langs->trans('User').'</td><td colspan="2">';
					print select_adherent($fk_adherent,'fk_adherent');
					print '</td></tr>';

		  // period
					print '<tr><td class="fieldrequired">'.$langs->trans('Period').'</td><td colspan="2">';
					print $objectpe->select_period($fk_period,'fk_period','','',1);
					print '</td></tr>';

					print '</table>';

					print '<center><br><input type="submit" class="button" value="'.$langs->trans("Proces").'"></center>';

					print '</form>';

				}

				if ($action == 'ejecutedet' &&
					$fk_adherent > 0 &&
					$fk_period > 0)
				{
					$objectU->fetch_user($fk_adherent);
					$objectCo->fetch_vigent($fk_adherent,1);

					$objectpe->fetch($fk_period);
					$idUser = $fk_adherent;
					$_SESSION['aPlanilla'][$fk_adherent] = array('id'         => $fk_adherent,
						'basic'      => $objectCo->basic,
						'date_ini'   => $objectCo->date_ini,
						'date_fin'   => $objectCo->date_fin,
						'date_fin_p' => $objectpe->date_fin,
						'date_ini_p' => $objectpe->date_ini);
					print_fiche_titre($langs->trans("Result"), $mesg);
		  // ṕrocesando
					print '<table class="border" width="100%">';
					$res = proc_formula($object->ref,GETPOST('fk_adherent'),GETPOST('fk_period'),true);

					print '<tr><td>'.$langs->trans('Result').'</td>';
					print '<td></td>';
					print '<td colspan="2">';
					print $res;
					print '</td></tr>';

					print '</table>';
				}

			}
			else
			{
				dol_print_error($db);
			}

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
	  	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="4" maxlength="4">';
	  	print '</td></tr>';
	  // detail
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
	  	print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="40" maxlength="40">';
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
