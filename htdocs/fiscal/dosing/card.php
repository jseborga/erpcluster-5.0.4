<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---2013 Ramiro Queso ramiro@ubuntu-bo.com---
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
*   	\file       dev/Subsidiary/Subsidiary_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2013-09-06 20:51
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/fiscal/class/vdosingext.class.php');
dol_include_once('/fiscal/class/subsidiaryext.class.php');

dol_include_once('/fiscal/class/vdosinghistory.class.php');
dol_include_once('/fiscal/class/vdosinglog.class.php');

dol_include_once('/fiscal/lib/fiscal.lib.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("fiscal@fiscal");

// Get parameters
$id		= GETPOST('id','int');
$rid		= GETPOST('rid','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

$object   = new Vdosingext($db);
$objectsub= new Subsidiaryext($db);

$objVdosingHistory = new Vdosinghistory($db);
$objVdosingLog     = new Vdosinglog($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
if ($action == 'add' && $user->rights->fiscal->dosi->creer)
{
	$date_val = dol_mktime(0,0,0,$_POST["date_valmonth"],$_POST["date_valday"],$_POST["date_valyear"]);
	$object->fk_subsidiaryid = $_POST["fk_subsidiaryid"];
	$object->series      = $_POST["series"];
	$object->num_ini     = $_POST["num_ini"];
	$object->num_fin     = $_POST["num_fin"];
	$object->num_ult     = $_POST["num_ult"]+0;
	$object->entity      = $conf->entity;
	$object->num_aprob   = $_POST["num_aprob"];
	$object->type        = $_POST["type"];
	$object->active      = $_POST["active"];
	$object->date_val    = $date_val;
	$object->num_autoriz = $_POST["num_autoriz"];
	$object->cod_control = $_POST["cod_control"];
	$object->lote        = $_POST["lote"];
	$object->chave       = $_POST["chave"];
	$object->descrip     = $_POST["descrip"];
	$object->activity    = GETPOST('activity');
	$object->status      = 0;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->date_create = dol_now();
	$object->date_mod = dol_now();
	$object->tms = dol_now();
	//revision
	if ($object->fk_subsidiaryid <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errorsubsidiaryequired').'</div>';
	}
	if (empty($object->series))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errorseriesryequired').'</div>';
	}
	if (empty($object->series))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errorseriesryequired').'</div>';
	}
	if (empty($object->num_ini))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errornumberinirequired').'</div>';
	}
	if ($object->active <0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Erroractiverequired').'</div>';
	}
	if (empty($object->num_autoriz))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errornumberautorizrequired').'</div>';
	}
	if ($object->lote <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errorloterequired').'</div>';
	}
	if ($object->lote ==2 && empty($object->chave))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errorchaverequired').'</div>';
	}

	if (empty($object->activity))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Activity")), null, 'errors');
	}


	if (empty($error))
	{
		$id=$object->create($user);

		$objVdosingLog->fk_dosing           = $id;
		$objVdosingLog->description         = $object->getLibStatut(1);
		$objVdosingLog->fk_user_create      = $user->id;
		$objVdosingLog->fk_user_mod         = $user->id;
		$objVdosingLog->datec               = dol_now();
		$objVdosingLog->datem               = dol_now();
		$objVdosingLog->tms                 = dol_now();
		$objVdosingLog->status              = $object->status;

		$result = $objVdosingLog->create($user);
		if ($result > 0)
		{
			//$action='view';
		}
		else
		{
			// Creation KO
			if (! empty($objVdosingLog->errors)) setEventMessages(null, $objVdosingLog->errors, 'errors');
			else setEventMessages($objVdosingLog->error, null, 'errors');
			//exit;
		}
		if ($id > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		// Creation OK
		}
		else
		{
			// Creation KO
			setEventMessages($object->error,$object->errors,'errors');
			$action="create";
		}
	}
	else
	{
		$action = 'create';
	}
}

//update
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{


	$aaPostInfo = unserialize($_SESSION['aPostInfo']);

	$error = 0;
	$date_val = dol_mktime(0,0,0,$_POST["date_valmonth"],$_POST["date_valday"],$_POST["date_valyear"]);
	if ($object->fetch($id)>0)
	{
		$object->fk_subsidiaryid = $_POST["fk_subsidiaryid"];
		$object->series      = $_POST["series"];
		$object->num_ini     = $_POST["num_ini"];
		$object->num_fin     = $_POST["num_fin"];
		$object->num_ult     = $_POST["num_ult"]+0;
		$object->entity      = $conf->entity;
		$object->num_aprob   = $_POST["num_aprob"];
		$object->type        = $_POST["type"];
		$object->active      = $_POST["active"];
		$object->date_val    = $date_val;
		$object->num_autoriz = $_POST["num_autoriz"];
		$object->cod_control = $_POST["cod_control"];
		$object->lote        = $_POST["lote"];
		$object->chave       = $_POST["chave"];
		$object->descrip     = $_POST["descrip"];
		$object->activity    = GETPOST('activity');
		$object->state  = 0;
		$object->fk_user_mod = $user->id;
		$object->datem = $now;
		$object->date_mod = $now;
		$object->tms = $now;
		if (empty($object->fk_user_create) || is_null( $object->fk_user_create) )
		{
			$object->fk_user_create = $user->id;
			$object->datec = $now;
			$object->date_create = $now;
		}
	//revision
		if ($object->fk_subsidiaryid <=0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errorsubsidiaryequired').'</div>';
		}
		if (empty($object->series))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errorseriesryequired').'</div>';
		}
		if (empty($object->series))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errorseriesryequired').'</div>';
		}
		if (empty($object->num_ini))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errornumberinirequired').'</div>';
		}
		if ($object->active <0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Erroractiverequired').'</div>';
		}
		if (empty($object->num_autoriz))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errornumberautorizrequired').'</div>';
		}
		if ($object->lote <=0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errorloterequired').'</div>';
		}
		if ($object->lote ==2 && empty($object->chave))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Errorchaverequired').'</div>';
		}
		if (empty($object->activity))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Activity")), null, 'errors');
		}


		if (empty($error))
		{

			$result=$object->update($user);

			if ($result > 0)
			{
				/************************************************************/
				$objVdosingHistory->fk_dosing       = $aaPostInfo["fk_dosing"];
				$objVdosingHistory->fk_subsidiaryid = $aaPostInfo["branch"];
				$objVdosingHistory->series          = $aaPostInfo["Serie"];
				$objVdosingHistory->num_ini     	= $aaPostInfo["Numini"];
				$objVdosingHistory->num_fin     	= $aaPostInfo["Numfin"];
				$objVdosingHistory->num_ult     	= $aaPostInfo["Numfin"];
				$objVdosingHistory->entity      	= $conf->entity;
				$objVdosingHistory->num_aprob   	= $aaPostInfo["Numaprob"];
				$objVdosingHistory->type        	= $aaPostInfo["Type"];
				$objVdosingHistory->active      	= $aaPostInfo["Active"];
				$objVdosingHistory->date_val    	= $aaPostInfo["Dateval"];
				$objVdosingHistory->num_autoriz 	= $aaPostInfo["Numautoriz"];
				$objVdosingHistory->cod_control 	= $aaPostInfo["Codcontrol"];
				$objVdosingHistory->lote        	= $aaPostInfo["Lote"];
				$objVdosingHistory->chave       	= $aaPostInfo["Chave"];
				$objVdosingHistory->descrip     	= $aaPostInfo["Additionaltextinvoice"];
				$objVdosingHistory->activity    	= $aaPostInfo["Activity"];
				$objVdosingHistory->fk_user_create  = $aaPostInfo["fk_user_create"];
				$objVdosingHistory->fk_user_mod    	= $aaPostInfo["fk_user_mod"];
				$objVdosingHistory->datec    	    = $aaPostInfo["date_create"];
				$objVdosingHistory->datem    	    = $aaPostInfo["date_mod"];
				if (is_null($aaPostInfo['date_mod']) || empty($aaPostInfo['date_mod']))
					$objVdosingHistory->datem    	    = $now;
				$objVdosingHistory->tms    	        = $aaPostInfo["tms"];
				$objVdosingHistory->status  		= 0;
				$resultHis = $objVdosingHistory->create($user);
				if ($resultHis > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
					exit;
					// Creation OK
				}
				else
				{
					// Creation KO
					if (! empty($objVdosingHistory->errors)) setEventMessages(null, $objVdosingHistory->errors, 'errors');
					else setEventMessages($objVdosingHistory->error, null, 'errors');
					//exit;
					//$mesg=$object->error;
					//$action="edit";
				}
			/************************************************************/
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			// Creation OK
			}
			else
			{
			// Creation KO
				$mesg=$object->error;
				$action="edit";
			}

		}
		else
			$action = 'edit';
	}
	else
		$action='edit';
}

// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->fiscal->dosi->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/fiscal/dosing/list.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$help_url='EN:Module_Ventas_En|FR:Module_Ventas|ES:M&oacute;dulo_Ventas';
//llxHeader("",$langs->trans("Managementsubsidiary"),$help_url);

$morejs = array('/ventas/javascript/recargar.js');
$morecss = array();
llxHeader('',$langs->trans("Dosing"),$help_url,'','','',$morejs,$morecss,0,0);


$form=new Form($db);

if ($action == 'create' && $user->rights->fiscal->dosi->creer)
{
	print_fiche_titre($langs->trans("Newdosingbill"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// branch
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Branch').'</td><td colspan="2">';
	print $objectsub->select_subsidiary($object->fk_subsidiaryid,'fk_subsidiaryid','',0,1);
	print '</td></tr>';

	// series
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Serie').'</td><td colspan="2">';
	print '<input id="series" type="text" value="'.$object->series.'" name="series" size="2" maxlength="4" required>';
	print '</td></tr>';

	// num_ini
	print '<tr><td class="fieldrequired">'.$langs->trans('Numini').'</td><td colspan="2">';
	print '<input id="num_ini" type="number" min="1" max="9999999999" value="'.$object->num_ini.'" name="num_ini"  required>';
	print '</td></tr>';

	// num_fin
	print '<tr><td class="fieldrequired">'.$langs->trans('Numfin').'</td><td colspan="2">';
	print '<input id="num_fin" type="number" min="1" max="9999999999" value="'.$object->num_fin.'" name="num_fin"  required>';
	print '<input type="checkbox" id="maxnum" onclick="javascript: llenarnumero(this)" > '.$langs->trans('Selectmaxnumber');
	print '</td></tr>';

	// num_aprob
	print '<tr><td>'.$langs->trans('Numaprob').'</td><td colspan="2">';
	print '<input id="num_aprob" type="text" value="'.$object->num_aprob.'" name="num_aprob" size="18" maxlenght="20">';
	print '</td></tr>';

	// type
	print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
	print select_typebill($object->type,'type','',0,1);
	print '</td></tr>';

	// active
	print '<tr><td class="fieldrequired">'.$langs->trans('Active').'</td><td colspan="2">';
	print $form->selectyesno('active',$object->active,1);
	print '</td></tr>';

	// date_val
	print '<tr><td>'.$langs->trans('Dateval').'</td><td colspan="2">';
	print $form->select_date($object->date_val,'date_val',0,0,0,"perso");
	print '</td></tr>';

	// num_autoriz
	print '<tr><td class="fieldrequired">'.$langs->trans('Numautoriz').'</td><td colspan="2">';
	print '<input id="num_autoriz" type="text" value="'.$object->num_autoriz.'" name="num_autoriz" size="13" maxlength="15" required>';
	print '</td></tr>';

	// cod_control
	print '<tr><td>'.$langs->trans('Codcontrol').'</td><td colspan="2">';
	print '<input id="cod_control" type="text" value="'.$object->cod_control.'" name="cod_control" size="13" maxlenght="15">';
	print '</td></tr>';

	// lote
	print '<tr><td class="fieldrequired">'.$langs->trans('Lote').'</td><td colspan="2">';
	print select_lotebill($object->lote,'lote','',0,1);
	print '</td></tr>';

	// clave
	print '<tr><td class="fieldrequired">'.$langs->trans('Chave').'</td><td colspan="2">';
	print '<input id="chave" type="text" value="'.$object->chave.'" name="chave" size="50">';
	print '</td></tr>';

	// activity
	print '<tr><td class="fieldrequired">'.$langs->trans('Activity').'</td><td colspan="2">';
	print '<textarea name="activity" cols="40" rows="3" wrap="soft">';
	print $object->activity;
	print '</textarea>';
	print '</td></tr>';

	// descrip
	print '<tr><td>'.$langs->trans('Additionaltextinvoice').'</td><td colspan="2">';
	print '<textarea name="descrip" cols="40" rows="3" wrap="soft">';
	print $object->descrip;
	print '</textarea>';
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


	  /*
	   * Affichage fiche
	   */
	  if ($action <> 'edit' && $action <> 're-edit')
	  {
	  //$head = fabrication_prepare_head($object);

	  	dol_fiche_head($head, 'Dosing', $langs->trans("Dosing"), 0, 'generic');

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->status = 1;
		  //create
		  $object->update($user);
		  $objVdosingLog->fk_dosing           = GETPOST('id');
		  $objVdosingLog->description         = $object->getLibStatut(1);
		  $objVdosingLog->fk_user_create      = $user->id;
		  $objVdosingLog->fk_user_mod         = $user->id;
		  $objVdosingLog->datec               = dol_now();
		  $objVdosingLog->datem               = dol_now();
		  $objVdosingLog->tms                 = dol_now();
		  $objVdosingLog->status              = $object->status;

		  $result = $objVdosingLog->create($user);
		  if ($result > 0)
		  {
			  //$action='view';
		  }
		  else
		  {
			  // Creation KO
			  if (! empty($objVdosingLog->errors)) setEventMessages(null, $objVdosingLog->errors, 'errors');
			  else setEventMessages($objVdosingLog->error, null, 'errors');
			  //exit;
		  }
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
	  	$object->status = 0;
		  //update
	  	$object->update($user);


		$objVdosingLog->fk_dosing           = GETPOST('id');
		$objVdosingLog->description         = $object->getLibStatut(1);
		$objVdosingLog->fk_user_create      = $user->id;
		$objVdosingLog->fk_user_mod         = $user->id;
		$objVdosingLog->datec               = dol_now();
		$objVdosingLog->datem               = dol_now();
		$objVdosingLog->tms                 = dol_now();
		$objVdosingLog->status              = $object->status;

		$result = $objVdosingLog->create($user);
		if ($result > 0)
		{
			//$action='view';
		}
		else
		{
			// Creation KO
			if (! empty($objVdosingLog->errors)) setEventMessages(null, $objVdosingLog->errors, 'errors');
			else setEventMessages($objVdosingLog->error, null, 'errors');
			//exit;
		}
		  $action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

	  }


	  // Confirm delete third party
	  if ($action == 'delete')
	  {
	  	$form = new Form($db);
	  	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletesubsidiary"),$langs->trans("Confirmdeletesubsidiary",$object->ref.' '.$object->label),"confirm_delete",'',0,2);
	  	if ($ret == 'html') print '<br>';
	  }
	  //Array para recuperar lo que se puede llegar a modificar
	  $aPostInfo = array();
	  print '<table class="border" width="100%">';
	  $aPostInfo ['fk_dosing'] = $object->id;
	  // branch
	  $objectsub->fetch($object->fk_subsidiaryid);
	  print '<tr><td width="20%">'.$langs->trans('branch').'</td><td colspan="2">';
	  $aPostInfo ['branch'] = $object->fk_subsidiaryid;
	  print $objectsub->label;
	  print '</td></tr>';

	  // series
	  print '<tr><td width="20%">'.$langs->trans('Serie').'</td><td colspan="2">';
	  print $object->series;
	  $aPostInfo ['Serie'] = $object->series;
	  print '</td></tr>';

	  // num_ini
	  print '<tr><td>'.$langs->trans('Numini').'</td><td colspan="2">';
	  print $object->num_ini;
	  $aPostInfo ['Numini'] = $object->num_ini;
	  print '</td></tr>';

	  // num_fin
	  print '<tr><td>'.$langs->trans('Numfin').'</td><td colspan="2">';
	  print $object->num_fin;
	  $aPostInfo ['Numfin'] = $object->num_fin;
	  print '</td></tr>';

	  // num_aprob
	  print '<tr><td>'.$langs->trans('Numaprob').'</td><td colspan="2">';
	  print $object->num_aprob;
	  $aPostInfo ['Numaprob'] = $object->num_aprob;
	  print '</td></tr>';

	  // type
	  print '<tr><td>'.$langs->trans('Type').'</td><td colspan="2">';
	  print select_typebill($object->type,'type','',0,1,1);
	  $aPostInfo ['Type'] = $object->type;
	  print '</td></tr>';

	  // active
	  print '<tr><td>'.$langs->trans('Active').'</td><td colspan="2">';
	  print ($object->active?$langs->trans('Yes'):$langs->trans('No'));
	  $aPostInfo ['Active'] = $object->active;
	  //print $form->selectyesno('active',$object->active,1);
	  print '</td></tr>';

	  // date_val
	  print '<tr><td>'.$langs->trans('Dateval').'</td><td colspan="2">';
	  print dol_print_date($object->date_val,'day');
	  $aPostInfo ['Dateval'] = $object->date_val;
	  print '</td></tr>';

	  // num_autoriz
	  print '<tr><td>'.$langs->trans('Numautoriz').'</td><td colspan="2">';
	  print $object->num_autoriz;
	  $aPostInfo ['Numautoriz'] = $object->num_autoriz;
	  print '</td></tr>';

	  // cod_control
	  print '<tr><td>'.$langs->trans('Codcontrol').'</td><td colspan="2">';
	  print $object->cod_control;
	  $aPostInfo ['Codcontrol'] = $object->cod_control;
	  print '</td></tr>';

	  // lote
	  print '<tr><td>'.$langs->trans('Lote').'</td><td colspan="2">';
	  print select_lotebill($object->lote,'lote','',0,1,1);
	  $aPostInfo ['Lote'] = $object->lote;
	  print '</td></tr>';

	  // clave
	  print '<tr><td>'.$langs->trans('Chave').'</td><td colspan="2">';
	  print $object->chave;
	  $aPostInfo ['Chave'] = $object->chave;
	  print '</td></tr>';

	  // Activity
	  print '<tr><td>'.$langs->trans('Activity').'</td><td colspan="2">';
	  print $object->activity;
	  $aPostInfo ['Activity'] = $object->activity;
	  print '</td></tr>';

	  // Descrip
	  print '<tr><td>'.$langs->trans('Additionaltextinvoice').'</td><td colspan="2">';
	  print $object->descrip;
	  $aPostInfo ['Additionaltextinvoice'] = $object->descrip;
	  print '</td></tr>';


	  // state
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	  //print libStatev($object->status,5);
	  print $object->getLibStatut(5);
	  $aPostInfo ['fk_user_create'] = $object->fk_user_create;
	  $aPostInfo ['fk_user_mod'] = $object->fk_user_mod;
	  $aPostInfo ['date_create'] = $object->date_create;
	  $aPostInfo ['date_mod'] = $object->date_mod;
	  $aPostInfo ['tms'] = $object->tms;
	  $aPostInfo ['Status'] = $object->getLibStatut(0);
	  print '</td></tr>';

	  print '</table>';

	  print '</div>';

	  $_SESSION['aPostInfo'] = serialize($aPostInfo);


	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */

	  print "<div class=\"tabsAction\">\n";

	  if ($action == '')
	  {
	  	if ($user->rights->fiscal->dosi->creer)
	  		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Createnew").'</a>';
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	  	if ($user->rights->fiscal->dosi->creer && $object->status==0)
	  		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'">'.$langs->trans("Modify").'</a>';
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	  	if ($user->rights->fiscal->dosi->val && $object->status == 0)
	  		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=validate&id='.$object->id.'">'.$langs->trans("Activate").'</a>';
	  	elseif($user->rights->fiscal->dosi->val && $object->status == 1)
	  		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=revalidate&id='.$object->id.'">'.$langs->trans("Deactivate").'</a>';

	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";

	  	if ($user->rights->fiscal->dosi->del  && $object->status==0)
	  		print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	  }
	  print "</div>";
	}
	  /*
	   * Edition fiche
	   */
	  if (($action == 'edit' || $action == 're-edit') && 1)
	  {
	  //print_fiche_titre($langs->trans("ApplicationsEdit"),$mesg);

		$array = unserialize($_SESSION['aPostInfo']);

		print_fiche_titre($langs->trans("ApplicationsEdit"));

	  	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	  	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  	print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<table class="border" width="100%">';

	  // branch
	  	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Branch').'</td><td colspan="2">';
	  	print $objectsub->select_subsidiary($object->fk_subsidiaryid,'fk_subsidiaryid','',0,1);
	  	print '</td></tr>';

	  // series
	  	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Serie').'</td><td colspan="2">';
	  	print '<input id="series" type="text" value="'.$object->series.'" name="series" size="2" maxlength="4">';
	  	print '</td></tr>';

	  // num_ini
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Numini').'</td><td colspan="2">';
	  	print '<input id="num_ini" type="number" value="'.$object->num_ini.'" name="num_ini" min="1" max="9999999999" size="10" maxlength="12">';
	  	print '</td></tr>';

	  // num_fin
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Numfin').'</td><td colspan="2">';
	  	print '<input id="num_fin" type="number" value="'.$object->num_fin.'" name="num_fin" min="1" max="9999999999" size="10" maxlength="12">';
	  	print '</td></tr>';

	  // num_ult
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Numult').'</td><td colspan="2">';
	  	print '<input id="num_ult" type="number" value="'.$object->num_ult.'" name="num_ult" size="10" maxlength="12">';
	  	print '</td></tr>';

	  // num_aprob
	  	print '<tr><td>'.$langs->trans('Numaprob').'</td><td colspan="2">';
	  	print '<input id="num_aprob" type="text" value="'.$object->num_aprob.'" name="num_aprob" size="18" maxlenght="20">';
	  	print '</td></tr>';

	  // type
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
	  	print select_typebill($object->type,'type','',0,1);
	  	print '</td></tr>';

	  // active
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Active').'</td><td colspan="2">';
	  	print $form->selectyesno('active',$object->active,1);
	  	print '</td></tr>';

	  // date_val
	  	print '<tr><td>'.$langs->trans('Dateval').'</td><td colspan="2">';
	  	print $form->select_date($object->date_val,'date_val',0,0,0,"perso");
	  	print '</td></tr>';

	  // num_autoriz
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Numautoriz').'</td><td colspan="2">';
	  	print '<input id="num_autoriz" type="text" value="'.$object->num_autoriz.'" name="num_autoriz" size="13" maxlenght="15">';
	  	print '</td></tr>';

	  // cod_control
	  	print '<tr><td>'.$langs->trans('Codcontrol').'</td><td colspan="2">';
	  	print '<input id="cod_control" type="text" value="'.$object->cod_control.'" name="cod_control" size="13" maxlenght="15">';
	  	print '</td></tr>';

	  // lote
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Lote').'</td><td colspan="2">';
	  	print select_lotebill($object->lote,'lote','',0,1);
	  	print '</td></tr>';

	  // clave
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Chave').'</td><td colspan="2">';
	  	print '<input id="chave" type="text" value="'.$object->chave.'" name="chave" size="50">';
	  	print '</td></tr>';

	// activity
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Activity').'</td><td colspan="2">';
	  	print '<textarea name="activity" cols="40" rows="3" wrap="soft">';
	  	print $object->activity;
	  	print '</textarea>';
	  	print '</td></tr>';

	  // descrip
	  	print '<tr><td>'.$langs->trans('Additionaltextinvoice').'</td><td colspan="2">';
	  	print '<textarea name="descrip" cols="40" rows="3" wrap="soft">';
	  	print $object->descrip;
	  	print '</textarea>';
	  	print '</td></tr>';


	  	print '</table>';

	  	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	  	print '</form>';

	  }

	 /////
	}
}

// End of page
llxFooter();
$db->close();
?>