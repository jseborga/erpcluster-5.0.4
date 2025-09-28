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
 *	\file       htdocs/salary/user/fiche.php
 *	\ingroup    salary user
 *	\brief      Page fiche salary user
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pusermovim.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pproces.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcentrocosto.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pregionalext.class.php';

//require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarycharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
if ($conf->banque->enabled)
	require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("members");
$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$rid       = GETPOST("rid");

$mesg = '';

$object  = new Pcontractext($db); //user contrato
$objectCo= new Pconceptext($db); //conceptos
$objectF = new Ptypefolext($db); //type procedim
$objectP = new Pproces($db); //procesos
$objectUb= new Puserbonus($db); //bonos
$objectre= new Pregionalext($db); //regional

$objUser = new User($db); //usuarios

$objectcc= new Pcentrocosto($db); //centro costo
$objAdh  = new Adherent($db); //members

if ($conf->orgman->enabled)
{
	$objDepartamentuser = new Pdepartamentuserext($db);
	$objectD = new Pdepartamentext($db); //departamentos
	$objectC = new Pchargeext($db); //cargos
}

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->contract->creer)
{
	$object->ref            = $_POST['ref'];
	$object->fk_user        = $_POST['rowid'];
	$object->fk_cc          = GETPOST('fk_cc');
	$object->fk_departament = GETPOST('fk_departament');
	$object->fk_charge      = GETPOST('fk_charge');
	$object->fk_proces      = GETPOST('fk_proces');

	$object->entity         = $conf->entity;
	$object->date_ini       = dol_mktime(12, 0, 0, GETPOST('dateimonth'),  GETPOST('dateiday'),  GETPOST('dateiyear'));
	if (GETPOST('datefmonth')>0)
		$object->date_fin       = dol_mktime(12, 0, 0, GETPOST('datefmonth'),  GETPOST('datefday'),  GETPOST('datefyear'));
	$object->state          = 0;
	$object->number_item          = GETPOST('number_item','alpha');
	$object->fk_regional    = GETPOST('fk_regional');
	$object->fk_unit    	= GETPOST('fk_unit');
	$object->basic          = GETPOST('basic');
	$object->basic_fixed    = GETPOST('basic_fixed')+0;
	$object->unit_cost    = GETPOST('unit_cost','int')+0;
	$object->bonus_old      = GETPOST('bonus_old');
	$object->nivel          = GETPOST('nivel');
	$object->hours          = GETPOST('hours');
	$object->nua_afp        = GETPOST('nua_afp')+0;
	$object->afp            = GETPOST('afp');
	$object->fk_account     = GETPOST('fk_account')+0;
	$object->fk_user_create	= $user->id;
	$object->fk_user_mod	= $user->id;
	$object->date_create 	= dol_now();
	$object->date_mod 		= dol_now();
	$object->tms 			= dol_now();
	$error=0;
	$mesgerror = '';
	if ($object->fk_cc<=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorcostcenterrequired');
	}
	if ($object->fk_regional<=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorregionalrequired');
	}

	if ($object->fk_departament<=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errordepartamentrequired');
	}
	if ($object->fk_charge<=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorchargerequired');
	}
	if ($object->fk_proces<=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorprocesrequired');
	}
	if (empty($object->basic))
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorbasicrequired');
	}
	if ($object->bonus_old<=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorbonusoldrequired');
	}
	if (empty($object->hours))
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorhoursrequired');
	}
	else
	{
		if ($object->hours < 1 || $object->hours > 23)
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorhoursrang_1_23');
		}
	}
	if (!empty($object->ref) && empty($error))
	{
		$db->begin();
		$result = $object->create($user);
		if ($result > 0)
		{
			$db->commit();
			$rid=$object->id;
			header("Location: fiche.php?rowid=".$id.'&rid='.$rid);
			exit;
		}
		else
		{
			$db->rollback();
			if ($object->error)
				$mesg=$object->error;
			else
				$mesg=$object->errors;
			$action = 'create';
		}
	}
	else
	{
		if ($error)
			$mesg='<div class="error">'.$mesgerror.'</div>';
		else
			$mesg='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
		$action = 'create';
	}

}

// Addbonus
if ($action == 'addbonus'  && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->salary->bonus->creer)
{
	$object->fk_charge  = GETPOST('fk_charge');
	$dateini  = dol_mktime(12, 0, 0, GETPOST('dateimonth'),  GETPOST('dateiday'),  GETPOST('dateiyear'));
	$datefin  = dol_mktime(12, 0, 0, GETPOST('datefmonth'),  GETPOST('datefday'),  GETPOST('datefyear'));
	$objectUb->fk_puser   = $rid;
	$objectUb->fk_concept = GETPOST('fk_concept');
	$objectUb->detail     = GETPOST('detail');
	$objectUb->amount     = GETPOST('amount');
	$objectUb->type   = 1;
	$objectUb->date_ini   = $dateini;
	$objectUb->date_fin   = $datefin;
	$objectUb->state      = 0;
	if ($objectUb->fk_puser && $objectUb->amount && $objectUb->date_ini)
	{
		$bid = $objectUb->create($user);
		if ($bid > 0)
		{
			header("Location: fiche.php?rowid=".$id."&rid=".$rid);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorbonusdetailrequired").'</div>';
		$action="createbonus";
	// Force retour sur page creation
	}
}

// Delete contract
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->contract->del)
{
	$object->fetch($rid);
	$result=$object->delete($user);
	if ($result > 0)
	{
		setEventMessages($langs->trans('Registro eliminado'),null,'mesgs');
		header("Location: ".DOL_URL_ROOT.'/salary/contract/liste.php?rowid='.$id);
		exit;
	}
	else
	{
		setEventMessages($object->error,$object->errors,'errors');
		$action='';
	}
}

// Delete bonus
if ($action == 'deletebonus' && $user->rights->salary->bonus->del)
{
	$objectUb->fetch($_REQUEST["bid"]);
	$result=$objectUb->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/contract/fiche.php?rowid='.$id.'&rid='.$rid);
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}
// Modification entrepot
if ($action == 'update' && $user->rights->salary->contract->creer)
{
	$res = $object->fetch($rid);
	if ($res>0)
	{
		$dateini  = dol_mktime(12, 0, 0, GETPOST('dateimonth'),  GETPOST('dateiday'),  GETPOST('dateiyear'));
		$datefin  = dol_mktime(12, 0, 0, GETPOST('datefmonth'),  GETPOST('datefday'),  GETPOST('datefyear'));
		//exit;
		$object->ref            = $_POST['ref'];
		$object->fk_cc          = GETPOST('fk_cc');
		$object->fk_departament = GETPOST('fk_departament');
		$object->fk_charge      = GETPOST('fk_charge');
		$object->fk_proces      = GETPOST('fk_proces');
		$object->entity         = $conf->entity;
		$object->date_ini       = $dateini;
		$object->date_fin       = $datefin;
		$object->state          = 0;
		$object->number_item    = GETPOST('number_item','alpha');
		$object->basic          = GETPOST('basic');
		$object->fk_regional    = GETPOST('fk_regional');
		$object->basic          = GETPOST('basic');
		$object->basic_fixed    = GETPOST('basic_fixed')+0;
		$object->unit_cost    = GETPOST('unit_cost','int')+0;
		$object->bonus_old      = GETPOST('bonus_old');
		$object->hours          = GETPOST('hours');
		$object->nivel          = GETPOST('nivel');
		$object->nua_afp        = GETPOST('nua_afp');
		$object->afp            = GETPOST('afp');
		$object->fk_account     = GETPOST('fk_account')+0;
		$object->fk_unit		= GETPOST('fk_unit');
		if (empty($object->fk_user_create) || is_null($object->fk_user_create))
			$object->fk_user_create	= $user->id;
		$object->fk_user_mod	= $user->id;
		if (empty($object->date_create) || is_null($object->date_create))
			$object->date_create	= dol_now();
		$object->date_mod		= dol_now();
		$object->tms			= dol_now();

		$error=0;
		$mesgerror = '';
		if ($object->fk_cc<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Centro de costo")), null, 'errors');
		}
		if ($object->fk_regional<=0)
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorregionalrequired');
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Regional")), null, 'errors');
		}

		if ($object->fk_departament<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Departament")), null, 'errors');

		}
		if ($object->fk_charge<=0)
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorchargerequired');
		}
		if ($object->fk_proces<=0)
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorprocesrequired');
		}
		if (empty($object->basic))
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorbasicrequired');
		}
		if ($object->bonus_old<=0)
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorbonusoldrequired');
		}
		if (empty($object->hours))
		{
			$error++;
			$mesgerror.= '<br>'.$langs->trans('Errorhoursdrequired');
		}
		else
		{
			if ($object->hours < 1 || $hours > 23)
			{
				$error++;
				$mesgerror.= '<br>'.$langs->trans('Errorhoursrang_1_23');
			}
		}
		if ( !$error)
		{
			$res = $object->update($user);
			$action = '';
			if ($res <=0)
			{
				setEventMessages($object->error,$object->errors,'errors');
			}
			else
			{
				$_GET["id"] = $_POST["id"];
				setEventMessages($langs->trans('Successfullyupdate'),null,'mesgs');
				header("Location: ".$_SERVER['PHP_SELF'].'?rowid='.$id.'&rid='.$rid);
			}

		//$mesg = '<div class="ok">Fiche mise a jour</div>';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			if ($error)
				$mesg = '<div class="error">'.$mesgerror.'</div>';
			else
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
	$_GET["rid"] = $_POST["rid"];

}

if ($id > 0) $result = $objAdh->fetch($id);
if ($rid > 0)
{
	$result = $object->fetch($rid);
	if (empty($object->id))
		$action = "create";
	else
		$rid = $object->id;
}
/*
 * View
 */

$form=new Formv($db);
$formcompany=new Formcompany($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

$head=member_prepare_head($objAdh);
dol_fiche_head($head, 'tabname2', $langs->trans("Member"),0,'user');

if ($action == 'create' && $user->rights->salary->contract->creer)
{
	/* ******************************* */
	/*                                 */
	/* Fiche creation                  */
	/*                                 */
	/* ******************************* */

	print_fiche_titre($langs->trans("Contratinformation"));
	//verificamos si esta asignado a algun departamento
	$filter = " AND t.fk_user = ".$id;
	$res = $objDepartamentuser->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	if ($res==1)
		$fk_departament = $objDepartamentuser->fk_departament;

	print '<form name="formsoc" action="fiche.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	if (!empty($id))
		print '<input type="hidden" name="rowid" value="'.$id.'">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	//ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input type="text" id="ref" name="ref" value="'.$object->ref.'" maxlength="20" size="18">';
	print '</td></tr>';
	//item
	print '<tr><td>'.$langs->trans('Item').'</td><td colspan="2">';
	print '<input type="text" id="number_item" name="number_item" value="'.$object->number_item.'" maxlength="20" size="18">';
	print '</td></tr>';

	//centrocosto
	print '<tr><td class="fieldrequired">'.$langs->trans('Costcenter').'</td><td colspan="2">';
	print $objectcc->select_cc($object->fk_cc,'fk_cc','','',1);
	print '</td></tr>';

	//regional
	print '<tr><td class="fieldrequired">'.$langs->trans('Regional').'</td><td colspan="2">';
	print $objectre->select_regional($object->fk_regional,'fk_regional','','',1);
	print '</td></tr>';

	//departament
	print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
	print $form->select_departament((GETPOST('fk_departament')?GETPOST('fk_departament'):$fk_departament),'fk_departament','','',1);
	print '</td></tr>';

	//charge
	print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
	print $form->select_charge_v($object->fk_charge,'fk_charge','','',1);
	print '</td></tr>';

	//proces
	print '<tr><td class="fieldrequired">'.$langs->trans('Proces').'</td><td colspan="2">';
	print $objectP->select_proces($object->fk_proces,'fk_proces','','',1);
	print '</td></tr>';

	//date ini
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	print $form->select_date($object->date_ini,'datei');
	print '</td></tr>';

	//date fin
	print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	print $form->select_date($object->date_fim,'datef',"","",1);
	print '</td></tr>';

	//basic
	print '<tr><td class="fieldrequired">'.$langs->trans('Basic').'</td><td colspan="2">';
	print '<input type="text" id="basic" name="basic" value="'.$object->basic.'" >';
	print '</td></tr>';

	//basic_fixed
	//print '<tr><td>'.$langs->trans('Basicfixed').'</td><td colspan="2">';
	//print '<input type="text" id="basic_fixed" name="basic_fixed" value="'.$object->basic_fixed.'" >';
	//print '</td></tr>';

	//bonus_old
	print '<tr><td class="fieldrequired">'.$langs->trans('Bonusold').'</td><td colspan="2">';
	print select_yesno($object->bonus_old,'bonus_old','',0,1);
	print '</td></tr>';

	//hours
	print '<tr><td class="fieldrequired">'.$langs->trans('Workinghours').'</td><td colspan="2">';
	print '<input type="text" id="hours" name="hours" value="'.$object->hours.'" size="1" maxlength="2">';
	print '</td></tr>';


	//fk_unit
	print '<tr><td class="fieldrequired">'.$langs->trans('Unit').'</td><td colspan="2">';
	print $form->selectUnits('','fk_unit');
	print '</td></tr>';

	//unit_cost
	print '<tr><td>'.$langs->trans('Unitcostperhour').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" id="unit_cost" name="unit_cost" value="'.$object->unit_cost.'" >';
	print '</td></tr>';

	//nivel
	print '<tr><td>'.$langs->trans('Hierarchicallevel').'</td><td colspan="2">';
	print '<input type="text" id="nivel" name="nivel" value="'.$object->nivel.'" size="28" maxlength="30">';
	print '</td></tr>';

	//nuaafp
	print '<tr><td>'.$langs->trans('Nua AFP').'</td><td colspan="2">';
	print '<input type="text" id="nua_afp" name="nua_afp" value="'.$object->nua_afp.'" size="13" maxlength="15">';
	print '</td></tr>';

	//afp
	print '<tr><td>'.$langs->trans('AFP').'</td><td colspan="2">';
	print '<input type="text" id="afp" name="afp" value="'.$object->afp.'" size="38" maxlength="40">';
	print '</td></tr>';

	//bank account
	if ($conf->banque->enabled)
	{
		print '<tr><td>'.$langs->trans('Bank account').'</td><td colspan="2">';
		print $form->select_comptes($object->fk_account,'fk_account',0,'',1);
		print '</td></tr>';
	}

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);

		//$objUser->fetch($id);
		$objAdh->fetch($id);
		$result = $object->fetch($rid);
		if ($result < 0)
		{
			dol_print_error($db);
		}


	/*
	 * Affichage fiche
	 */
	if ($rid && $action <> 'edit' && $action <> 're-edit')
	{
		/*
		 * Confirmation de la validation
		*/
		if ($action == 'validate')
		{
			$object->fetch(GETPOST('rid'));
		 //cambiando a validado
			$object->state = 1;
		 //update
			$object->update($user);
			$action = '';
		 //header("Location: fiche.php?id=".$_GET['id']);

		}

		if ($action == 'validatebonus')
		{
			$objectUb->fetch(GETPOST('bid'));
		 //cambiando a validado
			$objectUb->state = 1;
		 //update
			$objectUb->update($user);
			$action = '';
		 //header("Location: fiche.php?id=".$_GET['id']);

		}
		if ($action == 'devalidatebonus')
		{
			$objectUb->fetch(GETPOST('bid'));
		 //cambiando a validado
			$objectUb->state = 0;
		 //update
			$objectUb->update($user);
			$action = '';
		 //header("Location: fiche.php?id=".$_GET['id']);

		}

		 // Confirm delete third party
		if ($action == 'delete')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?rowid=".$id.'&rid='.$object->id,$langs->trans("Deletecontract"),$langs->trans("Confirmdeletecontract").' '.$object->ref.' '.$object->date_ini,"confirm_delete",'',0,2);
			if ($ret == 'html') print '<br>';
		}

		print '<table class="border" width="100%">';


		 //ref
		print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
		print $object->ref;
		print '</td></tr>';
		 //number_item
		print '<tr><td width="20%">'.$langs->trans('Item').'</td><td colspan="2">';
		print $object->number_item;
		print '</td></tr>';
		 //centrocosto
		print '<tr><td>'.$langs->trans('Costcenter').'</td><td colspan="2">';
		if ($objectcc->fetch($object->fk_cc)) print $objectcc->label;
		else print "";
		print '</td></tr>';
		 //regional
		print '<tr><td>'.$langs->trans('Regional').'</td><td colspan="2">';
		If ($objectre->fetch($object->fk_regional))
		print $objectre->label.' ('.$objectre->ref.')';
		else
			print '';
		print '</td></tr>';

		 //departament
		print '<tr><td>'.$langs->trans('Departament').'</td><td colspan="2">';
		if ($objectD->fetch($object->fk_departament))
			print $objectD->getNomUrl();
		else
			print "";
		print '</td></tr>';

		 //charge
		print '<tr><td>'.$langs->trans('Charge').'</td><td colspan="2">';
		if ($objectC->fetch($object->fk_charge)) print $objectC->getNomUrl();
		else
			print "";
		print '</td></tr>';

		 //proces
		print '<tr><td>'.$langs->trans('Proces').'</td><td colspan="2">';
		if ($objectP->fetch($object->fk_proces))
		print $objectP->label;
		else
			print '&nbsp;';
		print '</td></tr>';

		 //date ini
		print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
		print dol_print_date($object->date_ini,'day');
		print '</td></tr>';

		 //date fini
		print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
		print dol_print_date($object->date_fin,'day');
		print '</td></tr>';

		 //basic
		print '<tr><td>'.$langs->trans('Basic').'</td><td colspan="2">';
		print price($object->basic);
		print '</td></tr>';

		 //basic_fixed
		//print '<tr><td>'.$langs->trans('Basicfixed').'</td><td colspan="2">';
		//print $object->basic_fixed;
		//print '</td></tr>';

		 //bonus_old
		print '<tr><td>'.$langs->trans('Bonusold').'</td><td colspan="2">';
		print select_yesno($object->bonus_old,'bonus_old','',0,0,1);
		print '</td></tr>';

		 //hours
		print '<tr><td>'.$langs->trans('Workinghours').'</td><td colspan="2">';
		print $object->hours;
		print '</td></tr>';


		 //unit
		print '<tr><td>'.$langs->trans('Unit').'</td><td colspan="2">';
		print $object->getLabelOfUnit('long');
		print '</td></tr>';

		//unit_cost
		print '<tr><td>'.$langs->trans('Unitcostperhour').'</td><td colspan="2">';
		print price($object->unit_cost);
		print '</td></tr>';

		 //nivel
		print '<tr><td>'.$langs->trans('Hierarchicallevel').'</td><td colspan="2">';
		print $object->nivel;
		print '</td></tr>';

		 //nuaafp
		print '<tr><td>'.$langs->trans('Nua AFP').'</td><td colspan="2">';
		print $object->nua_afp;
		print '</td></tr>';

		 //afp
		print '<tr><td>'.$langs->trans('AFP').'</td><td colspan="2">';
		print $object->afp;
		print '</td></tr>';

		 //bank account
		if ($conf->banque->enabled)
		{
			$objAccount = new Account($db);
			$objAccount->fetch($object->fk_account);
			print '<tr><td>'.$langs->trans('Bank account').'</td><td colspan="2">';
			print $objAccount->label;
			print '</td></tr>';
		}
		 //state
		print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
		print LibState($object->state,5);
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
			if ($user->rights->salary->contract->creer)
				print "<a class=\"butAction\" href=\"fiche.php?action=edit&rowid=".$id.'&rid='.$object->id."\">".$langs->trans("Modify")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

			if ($user->rights->salary->contract->val && $object->state == 0)
				print '<a class="butActionDelete" href="fiche.php?action=validate&rowid='.$id.'&rid='.$object->id.'">'.$langs->trans("Validate").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";

			if ($user->rights->salary->contract->del && $object->state == 0)
				print '<a class="butActionDelete" href="fiche.php?action=delete&rowid='.$id.'&rid='.$object->id.'">'.$langs->trans("Delete").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		}
		print "</div>";

		 //listando los bonos

		if ($object->state > 0 )
		{
			print "<br/>";
			print "<div>";


		 // $sql = "SELECT ub.rowid as rid, ub.amount, ub.date_pay, ub.time_info, ub.amount_base, ub.date_pay, ub.sequen, ";
		 // $sql.= " c.ref AS ref, c.detail, ";
		 // $sql.= " p.ref AS refperiodo, p.mes, p.anio, ";
		 // $sql.= " t.ref AS reffol, t.detail AS detailfol ";
		 // $sql.= " FROM ".MAIN_DB_PREFIX."p_user_movim AS ub ";
		 // $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON ub.fk_concept = c.rowid ";
		 // $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_period AS p ON ub.fk_period = p.rowid ";
		 // $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS t ON ub.fk_type_fol = t.rowid ";
		 // $sql.= " WHERE ";
		 // $sql.= " c.entity = ".$conf->entity;
		 // $sql.= " AND ub.fk_user = ".$id;
		 // $sql.= " AND state = 1 ";

			$sql = "SELECT ub.rowid as bid, ub.amount, ub.date_ini, ub.date_fin, ub.detail, ub.state, ";
			$sql.= " c.ref AS ref, c.detail AS conceptdetail, ";
			$sql.= " t.ref AS reffol, t.detail AS detailfol ";
			$sql.= " FROM ".MAIN_DB_PREFIX."p_user_bonus AS ub ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON ub.fk_concept = c.rowid ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS t ON c.fk_codfol = t.rowid ";
			$sql.= " WHERE ";
			$sql.= " t.entity = ".$conf->entity;
			$sql.= " AND ub.fk_puser = ".$rid;
			$sql.= " AND ub.state >=0 ";

			$sql.= $db->order($sortfield,$sortorder);

			dol_syslog('List user bonus sql='.$sql);
			$resql = $db->query($sql);

			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
			print_liste_field_titre($langs->trans("Concept"),"", "c.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Nameconcept"),"", "c.detail","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Detailfol"),"", "t.detail","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Detail"),"", "ub.detail","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Amount"),"", "ub.amount","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Dateini"),"", "ub.date_ini","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);

			print_liste_field_titre($langs->trans("Datefin"),"", "ub.date_fin","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Status"),"", "ub.state","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Action"),"", "","","","");

			print "</tr>";
			if ($action == "createbonus" && $user->rights->salary->bonus->creer)
			{
				print '<form action="fiche.php" method="POST">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="addbonus">';
				print '<input type="hidden" name="rid" value="'.$object->id.'">';
				print '<input type="hidden" name="rowid" value="'.$id.'">';

				print '<tr>';
			 //nombre
				print '<td>';
				print $objectCo->select_concept('','fk_concept','',0,1,"type_cod IN(1)");
				print '</td>';

				print '<td>';
				print '&nbsp;';
				print '</td>';

				print '<td>';
				print '&nbsp;';
				print '</td>';

			 //detail
				print '<td>';
				print '<input type="text" id="detail" name="detail" size="20">';
				print '</td>';

			 //amount
				print '<td>';
				print '<input type="text" id="amount" name="amount" size="8">';
				print '</td>';

		  //date ini
				print '<td>';
				print $form->select_date('','datei');
				print '</td>';

			 //date fin
				print '<td>';
				print $form->select_date('','datef',"","",1);
				print '</td>';

				print '<td>';
				print '&nbsp;';
				print '</td>';

			 //action
				print '<td>';
				print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
				print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
				print '</td></tr>';
				print '</form>';

			}

			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;
				$var=True;
				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);
					$var=!$var;
			 //print '<td>'.dol_print_date($objp->datem).'</td>';
					print "<tr ".$bc[$var].">";
					print "<td>".$objp->ref.'</td>';
					print '<td>'.$objp->conceptdetail.'</td>';
					print '<td>'.$objp->detailfol.'</td>';
					print '<td>'.$objp->detail.'</td>';
					print '<td align="right">'.price($objp->amount).'&nbsp;</td>';
					print '<td>'.$objp->date_ini.'</td>';
					print '<td>'.$objp->date_fin.'</td>';
					print '<td align="center">'.LibState($objp->state,4).'</td>';
					print '<td align="center">';
					If ($user->rights->salary->bonus->del && $objp->state == 0)
					print '<a href="fiche.php?action=deletebonus&rowid='.$object->id.'&rid='.$rid.'&bid='.$objp->bid.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
					else
						print '&nbsp;';

					print '&nbsp;&nbsp;';
					If ($user->rights->salary->bonus->val && $objp->state == 0)
					print '<a class=\"butAction\" href="fiche.php?action=validatebonus&rowid='.$object->id.'&rid='.$rid.'&bid='.$objp->bid.'">'.img_picto($langs->trans("Valid"),'interrog').'</a>';
			 // else
			 //   print '<a class=\"butActionRefused\" href="#">'.img_picto($langs->trans("Valid"),'interrog').'</a>';


					print '&nbsp;&nbsp;';
					If ($user->rights->salary->bonus->val &&  $objp->state == 1)
					{
						print '<a class=\"butAction\" href="fiche.php?action=devalidatebonus&rowid='.$object->id.'&rid='.$rid.'&bid='.$objp->bid.'">'.img_picto($langs->trans("Novalid"),'disable').'</a>';

					}

					print '</td>';

					print '</tr>';
					$i++;
				}
			}
			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */
			print '</table>';
			print "</div>";
			print "<div class=\"tabsAction\">\n";
			if ($action == '')
			{
				if ($user->rights->salary->bonus->creer)
					print "<a class=\"butAction\" href=\"fiche.php?action=createbonus&rowid=".$id.'&rid='.$object->id."\">".$langs->trans("Create")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";
			}
			print "</div>";
		}
	}


	 /*
	  * Edition fiche
	  */
	 if ($rid && ($action == 'edit' || $action == 're-edit') && 1)
	 {
	 	$object->fetch($rid);
	 	print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

	 	print '<form action="fiche.php" method="POST">';
	 	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	 	print '<input type="hidden" name="action" value="update">';
	 	print '<input type="hidden" name="rid" value="'.$object->id.'">';
	 	print '<input type="hidden" name="rowid" value="'.$id.'">';

	 	print '<table class="border" width="100%">';

		 //ref
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	 	print '<input type="text" id="ref" name="ref" value="'.$object->ref.'" >';
	 	print '</td></tr>';
	 	//nuimber_item
		print '<tr><td>'.$langs->trans('Item').'</td><td colspan="2">';
		print '<input type="text" id="number_item" name="number_item" value="'.$object->number_item.'" maxlength="20" size="18">';
		print '</td></tr>';

		 //centrocosto
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Costcenter').'</td><td colspan="2">';
	 	print $objectcc->select_cc($object->fk_cc,'fk_cc','','',1);
	 	print '</td></tr>';

		 //regional
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Regional').'</td><td colspan="2">';
	 	print $objectre->select_regional($object->fk_regional,'fk_regional','','',1);
	 	print '</td></tr>';

		 //departament
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
	 	print $form->select_departament((GETPOST('fk_departament')?GETPOST('fk_departament'):$object->fk_departament),'fk_departament','','',1);
	 	print '</td></tr>';

		 //charge
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
	 	print $form->select_charge_v($object->fk_charge,'fk_charge','','',1);
	 	print '</td></tr>';

		 //proces
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Proces').'</td><td colspan="2">';
	 	print $objectP->select_proces($object->fk_proces,'fk_proces','','',1);
	 	print '</td></tr>';

		 //date ini
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	 	print $form->select_date($object->date_ini,'datei');
	 	print '</td></tr>';

		 //date fin
	 	print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	 	print $form->select_date($object->date_fin,'datef',"","",1);
	 	print '</td></tr>';

		 //basic
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Basic').'</td><td colspan="2">';
	 	print '<input type="text" id="basic" name="basic" value="'.$object->basic.'" >';
	 	print '</td></tr>';

		 //basic_fixed
	 	//print '<tr><td>'.$langs->trans('Basicfixed').'</td><td colspan="2">';
	 	//print '<input type="text" id="basic_fixed" name="basic_fixed" value="'.$object->basic_fixed.'" >';
	 	//print '</td></tr>';

		 //bonus_old
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Bonusold').'</td><td colspan="2">';
	 	print select_yesno($object->bonus_old,'bonus_old','',0,1);
	 	print '</td></tr>';

		 //hours
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Workinghours').'</td><td colspan="2">';
	 	print '<input type="text" id="hours" name="hours" value="'.$object->hours.'" size="1" maxlength="2">';
	 	print '</td></tr>';

		//fk_unit
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Unit').'</td><td colspan="2">';
	 	print $form->selectUnits($object->fk_unit,'fk_unit');
	 	print '</td></tr>';

		//unit_cost
		print '<tr><td>'.$langs->trans('Unitcostperhour').'</td><td colspan="2">';
		print '<input type="number" min="0" step="any" id="unit_cost" name="unit_cost" value="'.$object->unit_cost.'" >';
		print '</td></tr>';

		 //nivel
	 	print '<tr><td>'.$langs->trans('Hierarchicallevel').'</td><td colspan="2">';
	 	print '<input type="text" id="nivel" name="nivel" value="'.$object->nivel.'" size="28" maxlength="30">';
	 	print '</td></tr>';

		 //nuaafp
	 	print '<tr><td>'.$langs->trans('Nua AFP').'</td><td colspan="2">';
	 	print '<input type="text" id="nua_afp" name="nua_afp" value="'.$object->nua_afp.'" size="13" maxlength="15">';
	 	print '</td></tr>';

		 //afp
	 	print '<tr><td>'.$langs->trans('AFP').'</td><td colspan="2">';
	 	print '<input type="text" id="afp" name="afp" value="'.$object->afp.'" size="38" maxlength="40">';
	 	print '</td></tr>';

		 //bank account
	 	if ($conf->banque->enabled)
	 	{
	 		print '<tr><td>'.$langs->trans('Bank account').'</td><td colspan="2">';
	 		print $form->select_comptes($object->fk_account,'fk_account',0,'',1);
	 		print '</td></tr>';
	 	}

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
