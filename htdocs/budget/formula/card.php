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
 *	\file       htdocs/salary/formula/card.php
 *	\ingroup    Formulas
 *	\brief      Page fiche salary formulas
 */

require("../../main.inc.php");

include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/budget/class/itemsformula.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/budget/class/puformulasext.class.php');
dol_include_once('/budget/class/puformulasdetext.class.php');
dol_include_once('/budget/class/puoperatorext.class.php');
dol_include_once('/budget/class/parametercalculation.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/categories/class/categorie.class.php');


require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


$conf_db_type = $dolibarr_main_db_type;

$langs->load("budget@budget");

$action=GETPOST('action');

$id        = GETPOST("id");
$idr       = GETPOST("idr");

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$mesg = '';

$object  = new Puformulasext($db);
$objtmp  = new Puformulasext($db);
$objectd = new Puformulasdetext($db);
$objecto = new Puoperatorext($db);
$objstr  = new Pustructureext($db);
$objparam  = new Parametercalculation($db);
$categorie = new Categorie($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('puformulas'));
$extrafields = new ExtraFields($db);
if (!$user->rights->budget->form->read) accessforbidden();

if ($id>0)
{
	$result = $object->fetch($id);
}

/*
 * Actions
 */

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);
// Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//confirm_clone
	if ($action == 'confirm_clon')
	{
		//habilitamos la copia
		//recibimos en nuevo nombre
		$codeorig = $object->ref;
		$db->begin();
		$object->id = 0;
		$object->ref = GETPOST('code');
		$object->detail = GETPOST('label');
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->date_create = dol_now();
		$object->date_mod = dol_now();
		$object->tms = dol_now();
		$object->active = 1;
		$object->statut = 0;
		$nid = $object->create($user);
		if ($nid > 0)
		{
			//recuperamos todas las formulasdet definida
			$filterstatic = " AND t.ref_formula = '".trim($codeorig)."'";
			$filterstatic.= " AND t.entity = ".$conf->entity;
			$filterstatic.= " AND t.status >= 0";
			$res = $objectd->fetchAll('ASC', 'sequen',0,0,array(1=>1),'AND',$filterstatic);
			if ($res > 0)
			{
				$lines = $objectd->lines;
				foreach ($lines AS $i => $line)
				{
					$objectd->fetch($line->id);
					$objectd->id = 0;
					$objectd->fk_user_create = $user->id;
					$objectd->fk_user_mod = $user->id;
					$objectd->ref_formula = GETPOST('code');
					$objectd->date_create = dol_now();
					$objectd->date_mod = dol_now();
					$objectd->tms = dol_now();
					$res1 = $objectd->create($user);
					if ($res1 <= 0)
					{
						setEventMessages($objectd->error,$objectd->errors,'errors');
						$error++;
					}
				}
			}
			else
			{
				setEventMessages($objectd->error, $objectd->errors,'errors');
				$error++;
			}
		}
		else
		{
			$error++;
			setEventMessages($object->error, $object->errors,'errors');
		}
		if (!$error)
		{
			setEventMessages($langs->trans('Clonesucessfull'),null,'mesgs');
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$nid);
			exit;
		}
		else
		{
			$db->rollback();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		$action = '';
	}

// Add
	if ($action == 'add' && $user->rights->budget->form->write)
	{
		$object->ref     = $_POST["ref"];
		$object->detail  = GETPOST('detail');
		$object->entity  = $conf->entity;
		$object->statut   = 0;

		if ($object->ref)
		{
			$id = $object->create($user);
			if ($id > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			$action = 'create';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
			$action="create";
	  // Force retour sur page creation
		}
	}

// Adddet
	if ($action == 'adddet' && $user->rights->budget->form->write && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$sequen = GETPOST('sequen','int');
		$object->fetch($id);
		$cStructure  = GETPOST('fk_pu_structure');
		if ($cStructure == -1) $cStructure = 0;
		$cFormula    = GETPOST('fk_formula');
		if ($cFormula == -1) $cformula = 0;
		$code_parameter = GETPOST('code_parameter');

		$nValor      = GETPOST('nValor');

		$nValida = !empty($cStructure) + !empty($cFormula)+ !empty($code_parameter);


		//validando los campos
		if ($nValida > 1) $error++;
		if (empty($error))
		{
			//registrando el valor a la variable
			if (!empty($cStructure))
			{
			//$objstr->fetch($cStructure);
				$objectd->changefull = $objstr->entity.'|'.$objstr->ref;
				$objectd->changefull = $conf->entity.'|'.$cStructure;
				$objectd->type       = 'pu_structure';
			}
			if (!empty($cFormula))
			{
				$objtmp->fetch($cFormula);
				$objectd->changefull = $objtmp->entity.'|'.$objtmp->ref;
				$objectd->type       = 'pu_formulas';
			}
			if (!empty($code_parameter))
			{
				$objparam->fetch(0,$code_parameter);
				$objectd->changefull = $objparam->entity.'|'.$objparam->code;
				$objectd->type       = 'parameter_calculation';
			}
			if (!empty($nValor))
			{
				$objectd->changefull = $nValor;
				$objectd->type       = 'valor';
			}

			$objectd->entity = $conf->entity;
			$objectd->ref_formula = $object->ref;

			$objectd->fk_operator = GETPOST('fk_operator');
			$objectd->sequen  = $objectd->sequen_det($object->ref);
			$objectd->sequen  = $sequen;
			$objectd->status   = 1;
			if ($objectd->type)
			{
				$idr = $objectd->create($user);
				if ($idr > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
					exit;
				}
				$action = 'createdet';
				setEventMessages($objectd->error,$objectd->errors,'errors');
			}
			else
			{
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")), null, 'errors');
				$action="createdet";
			}
		}
		else
		{
			setEventMessages($langs->trans("Errorselectusergenericvaluemustbeunique"),null,'errors');
			$action="createdet";
			$_GET['id'] = $_POST['id'];
		}
	}

	if ($action == 'deletedet' && $user->rights->budget->form->del)
	{
		$objectd->fetch($idr);
		$objectd->status = -1;
		$resd = $objectd->update($user);
		if ($resd<=0)
		{
			setEventMessages($objectd->error,$objectd->errors,'errors');
		}
		$action = '';
	}

// Adddet
	if ($action == 'updatedet' && $user->rights->budget->form->mod)
	{
		$sequen = GETPOST('sequen','int');
		$objectd->fetch(GETPOST('idr'));

		$cStructure  = GETPOST('fk_pu_structure');
		if ($cStructure == -1) $cStructure = 0;
		$cFormula    = GETPOST('fk_formula');
		if ($cFormula == -1) $cformula = 0;
		$code_parameter = GETPOST('code_parameter');
		$nValor      = GETPOST('nValor');

		$nValida = !empty($cStructure) + !empty($cFormula) + !empty($code_parameter);


	//$cCampouser  = GETPOST('cCampouser');
	//if ($cCampuser == -1)
	//	$cCampuser = 0;
	//$cGeneric    = GETPOST('cGeneric');
	//if ($cGeneric == -1)
	//	$cGeneric = 0;
	//$cConcept    = GETPOST('cConcept');
	//if ($cConcept == -1)
	//	$cConcept = 0;
	//$nValor      = GETPOST('nValor');
	//$cFormula    = GETPOST('cFormula');
	//$nValida = !empty($cCampuser) + !empty($cGeneric) + !empty($cConcept) + !empty($nValor) + !empty($cFormula);
	//$andor       = GETPOST('andor');

	//validando los campos
		if ($nValida > 1)
		{
			setEventMessages($langs->trans('Error, no esta permitido seleccionar mas de una opcion'),null,'errors');
			$error++;
		}
	//if (empty($andor))
	//	$error++;
		if (empty($error))
		{
		//registrando el valor a la variable
		//registrando el valor a la variable
			if (!empty($cStructure))
			{
			//$objstr->fetch($cStructure);
				$objectd->changefull = $objstr->entity.'|'.$objstr->ref;
				$objectd->changefull = $conf->entity.'|'.$cStructure;
				$objectd->type       = 'pu_structure';
			}
			if (!empty($cFormula))
			{
				$objtmp->fetch($cFormula);
				$objectd->changefull = $objtmp->entity.'|'.$objtmp->ref;
				$objectd->type       = 'pu_formulas';
			}
			if (!empty($code_parameter))
			{
				$objparam->fetch(0,$code_parameter);
				$objectd->changefull = $objparam->entity.'|'.$objparam->code;
				$objectd->type       = 'parameter_calculation';
			}
			if (!empty($nValor) || (isset($_POST['nValor']) && empty($cFormula) && empty($cStructure) && empty($code_parameter)))
			{
				$objectd->changefull = $nValor;
				$objectd->type       = 'valor';
			}
			$objectd->sequen = $sequen;
			$objectd->fk_operator = GETPOST('fk_operator');
			$objectd->andor   = GETPOST('andor');

			if ($objectd->type)
			{
				$res = $objectd->update($user);
				if ($res<=0)
				{
					setEventMessages($objectd->error,$objectd->errors,'errors');
				}
				else
				{
					setEventMessages($langs->trans('Updatesuccesfull'),null,'mesgs');
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
					exit;
				}
			}
			else
			{
				setEventMessages($langs->trans("Errorrefnamerequired"),null,'errors');
				$action="editdet";
			}
		}
		else
		{
			setEventMessages($langs->trans("Errorselectusergenericvaluemustbeunique"),null,'errors');
			$action="editdet";
			$_GET['id'] = $_POST['id'];
			$_GET['idr'] = $_POST['idr'];
		}
	}

// Delete period
	if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->budget->form->del)
	{
		$object->fetch($_REQUEST["id"]);
		$result=$object->delete($user);
		if ($result > 0)
		{
			header("Location: ".DOL_URL_ROOT.'/budget/formula/list.php');
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
			if ( $object->update($user) > 0)
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

$title=$langs->trans('Formulas');
$morejs = array('/budget/js/priceunit.js',);
$morecss = array('/budget/css/style.css',);
llxHeader('',$title,$help_url,'','','',$morejs,$morecss,0,0);

if ($action == 'create' && $user->rights->budget->form->write)
{
	print_fiche_titre($langs->trans("Newformula"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
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

		if ($result < 0)
		{
			dol_print_error($db);
		}


		//
		if ($action <> 'edit' && $action <> 're-edit')
		{
			//$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Formula"), 0, 'formula');

			// Confirmation de la validation
			if ($action == 'validate')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->statut = 1;
				//update
				$object->update($user);
				$action = '';
				//header("Location: fiche.php?id=".$_GET['id']);
			}

			if ($action == 'revalidate')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->statut = 0;
				//update
				$object->update($user);
				$action = '';
			}

			// Confirm delete third party
			if ($action == 'clone')
			{
				$formquestion = array(array('type'=>'text','label'=>$langs->trans('Code'),'size'=>5,'name'=>'code','value'=>$object->code),array('type'=>'text','label'=>$langs->trans('Label'),'size'=>40,'name'=>'label','value'=>''));
				$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Cloneformula'), $langs->trans('ConfirmCloneformula'), 'confirm_clon', $formquestion, 0, 1);
				print $formconfirm;
			}
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteformula"),$langs->trans("ConfirmDeleteformula",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
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
			print $object->getLibStatut(1);
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
				if ($user->rights->budget->form->write && $object->statut == 1)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=clone&id='.$object->id.'">'.$langs->trans("Clone").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Clone")."</a>";
				if ($user->rights->budget->form->mod && $object->statut == 0)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'">'.$langs->trans("Modify").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->budget->form->val && $object->statut==0)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=validate&id='.$object->id.'">'.$langs->trans("Valid").'</a>';
				elseif ($user->rights->budget->form->val && $object->statut==1)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=revalidate&id='.$object->id.'">'.$langs->trans("Change").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";

				if ($user->rights->budget->form->del && $object->statut==0)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}
			print "</div>";

			if ($action == "createdet" || $action == 'editdet')
			{
				if ($action == 'editdet')
				{
					$cRef = '';
					$cCampouser = -1;
					$cGeneric  = -1;
					$cConcept  = -1;
					$nValor    = '';
					$cFormula  = "";
					$objectd->fetch($idr);
					$sequential = $objectd->sequen;
					if ($objectd->type == 'pu_structure')
					{
						list($nEntity,$cRef) = explode('|',$objectd->changefull);
						$filterstatic = " AND ";
						//$objstr->fetch_ref($cRef);
						$cConcept = $objectC->id;
					}
					elseif($objectd->type == 'pu_formulas')
					{
						list($nEntity,$cFormula) = explode('|',$objectd->changefull);
						$objtmp->fetch(0,$cFormula);
						$cFormula = $objtmp->id;
					}
					elseif($objectd->type == 'parameter_calculation')
					{
						//la estructura viene de las categorias
						list($nEntity,$code_parameter) = explode('|',$objectd->changefull);
						$cParam = $code_parameter;
					}
					elseif($objectd->type == 'valor')
						$nValor = $objectd->changefull;
					elseif($objectd->type == 'formula')
						$cFormula = $objectd->changefull;
				}
				else
					$sequential = $objectd->sequen_det($object->ref);

				//armamos los parametros
				$filterstatic = " AND t.entity = ".$conf->entity;
				$objparam->fetchAll('ASC', 't.label', 0, 0, array(1=>1), 'AND',$filterstatic);
				$options = '<option value="0">&nbsp;</option>';
				foreach ($objparam->lines AS $j => $line)
				{
					$options.= '<option value="'.$line->code.'" '.($cParam == $line->code?'selected':'').'>'.$line->label.'</option>';
				}
				//$aArrayCam = listColumn(MAIN_DB_PREFIX.'p_contract',$conf_db_type);
				$x2 = 1;

				print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

				print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

				if ($action == 'editdet')
					print '<input type="hidden" name="action" value="updatedet">';
				else
					print '<input type="hidden" name="action" value="adddet">';

				print '<input type="hidden" name="id" value="'.$object->id.'">';
				if ($action == 'editdet')
					print '<input type="hidden" name="idr" value="'.$objectd->id.'">';

				print '<table class="border" width="100%">';

				// sequential
				print '<tr><td class="fieldrequired">'.$langs->trans('Position').'</td><td colspan="2">';
				print '<input type="number" min="0" name="sequen" value="'.$sequential.'">';
				print '</td></tr>';

				// operator
				print '<tr><td class="fieldrequired">'.$langs->trans('Operator').'</td><td colspan="2">';
				print $objecto->select_operator($objectd->fk_operator,'fk_operator','onblur="javascript: revoperator(this);"',0,0);
				print '</td></tr>';
				// pu_structure
				print '<tr><td>'.$langs->trans('Structure').'</td><td colspan="2">';
				$filter = array(1=>1);
				$filterstatic = " AND t.fk_categorie > 0";
				$objstr->fetchAll('ASC', 'ordby', 0, 0,$filter,'AND',$filterstatic);
				//armamos todos los que tienen categoria

				foreach ((array) $objstr->lines AS $j => $line)
				{
					$obj = new StdClass();
					$obj->fk_categorie = $line->fk_categorie;
					$obj->ref = $line->ref;
					$obj->detail = $line->detail;
					//buscamos la categoria
					$categorie->fetch($line->fk_categorie);
					if ($categorie->id == $line->fk_categorie)
					{
						$obj->detail = $categorie->label;
						$aData[$line->fk_categorie] = $obj;
					}
				}
				$objnew = new stdClass();
				$objnew->fk_categorie = -9;
				$objnew->ref = $langs->trans('Complementary');
				$objnew->detail = $langs->trans('Complementary activities');
				$aData[-9] = $objnew;
				print $objstr->pu_select($cRef,'fk_pu_structure','',1,'fk_categorie',$aData);
				print '</td></tr>';

				// formula
				print '<tr><td>o '.$langs->trans('Formula').'</td><td colspan="2">';
				$filter = array(1=>1);
				$filterstatic = " AND t.statut = 1";
				$objtmp->fetchAll('ASC', 'ref', 0, 0,$filter,'AND',$filterstatic);
				print $objtmp->form_select($cFormula,'fk_formula','',1);
				print '</td></tr>';

				// Parameter
				print '<tr><td>o '.$langs->trans('Calculationparameter').'</td><td colspan="2">';
				print '<select name="code_parameter">'.$options.'</select>';
				print '</td></tr>';

				//amount
				print '<tr><td>'.$langs->trans('oValue').'</td><td colspan="2">';
				print '<input id="nValor" type="number" step="any" min="0" value="'.$nValor.'" name="nValor" size="7">';
				print '</td></tr>';

				print '</table>';

				print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
				print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

				print '</form>';

			}

	  //lista el detalle de formulas
			$sql = "SELECT a.ref_formula,a.rowid AS id, a.fk_operator, a.type, a.changefull, a.sequen, a.status, ";
			$sql.= " b.detail, b.operator, b.type AS typeOperator ";
			$sql.= " FROM ".MAIN_DB_PREFIX."pu_formulas_det AS a ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."pu_operator AS b ON a.fk_operator = b.rowid ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."pu_formulas AS c ON a.ref_formula = c.ref AND a.entity = c.entity ";
			$sql.= " WHERE c.entity = ".$conf->entity;
			$sql.= " AND a.ref_formula = '".$object->ref."' ";
			$sql.= " AND a.status != -1";
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
				if ($num)
				{
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
						print '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idd='.$obj->id.'">'.img_object($langs->trans("Ref"),'generic').' '.$obj->sequen.'</a></td>';
						print '<td>'.$obj->operator.'</td>';
						print '<td>'.$obj->detail.'</td>';
						print '<td>'.$obj->type.'</td>';
						if ($obj->type == 'p_concept')
						{
							list($nEntity,$cRef) = explode('|',$obj->changefull);
						//$objectC->fetch('',$cRef);
							if ($objectC->ref == $cRef)
								print '<td>'.$objectC->detail.'</td>';
							else
								print '<td>&nbsp;</td>';
						}
						elseif ($obj->type == 'p_generic_table')
						{
							list($nEntity,$cTableCod,$nSequen) = explode('|',$obj->changefull);
						//$objectgt->fetch_table_cod($cTableCod,$nSequen);
							print '<td>'.$objectgt->table_name.' : '.$objectgt->field_name.'</td>';
						}
						elseif ($obj->type == 'pu_formulas')
						{
							list($nEntity,$fk_formula) = explode('|',$obj->changefull);
							$objtmp->fetch(0,$fk_formula);
							print '<td>'.$objtmp->detail.'</td>';
						}
						elseif ($obj->type == 'pu_structure')
						{
							//la estructura viene de las categorias
							list($nEntity,$fk_categorie) = explode('|',$obj->changefull);
							if ($fk_categorie == -9)
							{
								print '<td>'.$langs->trans('Complementary activities').'</td>';
							}
							else
							{
								$categorie->fetch($fk_categorie);
								print '<td>'.$categorie->label.'</td>';
							}
						}
						elseif ($obj->type == 'parameter_calculation')
						{
							//la estructura viene de las categorias
							list($nEntity,$code_parameter) = explode('|',$obj->changefull);
							$objparam->fetch(0,$code_parameter);
							print '<td>'.$objparam->label.'</td>';
						}
						else
							print '<td>'.$obj->changefull.'</td>';
						//action

						print '<td>';
						if ($object->statut != 1)
						{
							print '<center>';
							print '<a href="'.$_SERVER['PHP_SELF'].'?action=editdet&id='.$object->id.'&idr='.$obj->id.'">'.img_picto($langs->trans("Edit"),'edit').'</a>';
							print '&nbsp;&nbsp;';
							print '<a href="'.$_SERVER['PHP_SELF'].'?action=deletedet&id='.$object->id.'&idr='.$obj->id.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';

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
					if ($user->rights->budget->form->write && $object->statut != 1)
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createdet&id='.$object->id.'">'.$langs->trans("Create").'</a>';
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";
				}
				print "</div>";
				$fk_adherent = GETPOST('fk_adherent');
				$fk_period   = GETPOST('fk_period');

				//registro de prueba
				if ($num && $abc)
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
				//$objectU->fetch_user($fk_adherent);
				//$objectCo->fetch_vigent($fk_adherent,1);

				//$objectpe->fetch($fk_period);
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
				//$res = proc_formula($object->ref,GETPOST('fk_adherent'),GETPOST('fk_period'),true);

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


		//
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
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
