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
 *	\file       htdocs/salary/upload/fiche.php
 *	\ingroup    salary subida archivos
 *	\brief      Page fiche upload
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pusermovim.class.php';

require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarycharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("members");
$langs->load("salary@salary");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$docum      = GETPOST('docum');
$delimiter  = GETPOST('delimiter');
$mesg = '';

$objectC  = new Pcharge($db);
$objectD  = new Pdepartament($db);
$objUser  = new User($db);

$objectpu = new Puserext($db);
$objectpe = new Pperiodext($db);
$objectsp = new Psalarypresentext($db);
$objectco = new Pconceptext($db);
$objectcon= new Pcontractext($db);

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

/*
 * Actions
 */

// AddSave
if ($action == 'addSave')
{
	$aArrData   = $_SESSION['aArrData'];
	$fk_period  = GETPOST('fk_period');
	$fk_concept = GETPOST('fk_concept');
	$docum      = GETPOST('docum');

	//buscando el periodo
	$lSave = false;
	$objectpe->fetch($fk_period);
	if ($objectpe->id == $fk_period)
	{
		$lSave = true;
		$fk_proces = $objectpe->fk_proces;
		$fk_type_fol = $objectpe->fk_type_fol;
		$date_ini = $objectpe->date_ini;
		list($date_fin,$hora_fin) = explode(' ',$db->idate($objectpe->date_fin));
	}
	if ($lSave)
	{
		//borramos los existentes con el periodo
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_salary_present ";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND fk_period = ".$fk_period;
		$sql.= " AND fk_proces = ".$objectpe->fk_proces;
		$sql.= " AND fk_type_fol = ".$objectpe->fk_type_fol;
		$sql.= " AND fk_concept = ".$fk_concept;
		$resql = $db->query($sql);
		foreach ((array) $aArrData AS $i => $data)
		{
			$fkUser = '';
			//definiendo donde buscar al empleado
			if ($docum == 1)
			{
				//por id
				$fkUser = $data['fk_user'];
			}
			if ($docum == 2)
			{
				$objectpu->fetch_user('',$data['fk_user']);
				//por login
				if ($objectpu->fk_user == $data['fk_user'])
				{
					$fkUser = $objectpu->fk_user;
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('No existe el login'),null,'errors');
				}
			}
			if ($docum == 3)
			{
				//por doc
				$filterstatic = " AND t.docum = '".trim($data['fk_user'])."'";
				//$res = $objectpu->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
				$res = $objectpu->fetch(0,'',trim($data['fk_user']));
				//if ($objectpu->docum == $data['fk_user'])
				if ($res == 1)
				{
					$fkUser = $objectpu->fk_user;
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('No existe el documento'),null,'errors');
				}
			}
			//$objectcon->fetch_vigent($data['fk_user'],1);
			if (!$error)
			{
				$objectcon->fetch_vigent($fkUser,1);
				$objectpu->fetch($fkUser);
				if ( $objectcon->fk_user == $fkUser)
				{
					$objectsp->initAsSpecimen();
					$objectsp->fk_user     = $fkUser;
					$objectsp->fk_cc       = $objectcon->fk_cc;
					$objectsp->entity      = $conf->entity;
					$objectsp->fk_proces   = $objectpe->fk_proces;
					$objectsp->fk_type_fol = $objectpe->fk_type_fol;
					$objectsp->fk_period   = $fk_period;
					$objectsp->fk_concept  = $fk_concept;
					$objectsp->fk_account  = 0;
					$objectsp->sequen 		= $i;
					$objectsp->type        = $data['type'];
					$objectsp->upload      = 1;
					$objectsp->cuota       = $data['cuota']+0;
					$objectsp->semana      = $data['semana']+0;
					$objectsp->amount_inf  = $data['amount']+0;
					$objectsp->amount      = $data['amount']+0;
					$objectsp->hours_info  = $data['hours']+0;
					$objectsp->hours       = 0;
					$objectsp->date_reg    = dol_mktime(12, 0, 0, date('m',$date_fin), date('d',$date_fin), date('Y',$date_fin));
					$objectsp->date_create = dol_now();
					$objectsp->fk_user_create = $user->id;
					$objectsp->fk_user_mod = $user->id;
					$objectsp->date_mod = dol_now();
					$objectsp->payment_state = 0;
					$objectsp->state = 0;
					$res = 	$objectsp->create($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objectsp->error,$objectsp->errors,'errors');
					}

				}
			}
		}
		if (!$error)
			setEventMessages($langs->trans('Successfulfileupload'),null,'mesgs');
		$action = "exit";
	}
}

// Add
if ($action == 'add' && !empty($delimiter))
{
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

	$tempdir = "tmp/";
	//compruebo si la extension es correcta

	if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	{

	//  echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	$csvfile = $tempdir.$nombre_archivo;
	$delimiter = trim($delimiter);
	$fh = fopen($csvfile, 'r');
	$headers = fgetcsv($fh);
	$aHeaders = explode($delimiter,$headers[0]);
	$data = array();
	$aData = array();
	while (! feof($fh))
	{
		$row = fgetcsv($fh);
		if (!empty($row))
		{
			$aData = explode($delimiter,$row[0]);
			$obj = new stdClass;
			$obj->none = "";
			foreach ($aData as $i => $value)
			{
				$key = $aHeaders[$i];
				if (!empty($key))
					$obj->$key = $value;
				else
					$obj->none = $value." xx";
			}
			$data[] = $obj;
		}
	}
	fclose($fh);

	$c=0;
	$action = "edit";
}




if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
//campos principales tabla  m
$aHeaderTpl = array('fk_user' => 'fk_user',
	'type' => 'type',
	'cuota' => 'cuota',
	'semana' => 'semana',
	'amount' => 'amount',
	'hours' => 'hours');

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' || empty($action) && $user->rights->salary->uparchive)
{
	print_fiche_titre($langs->trans("Upload archive"));
	print '<form action="fiche.php" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);


	print '<table class="border" width="100%">';
	print '<tr><td width="20%">';
	print $langs->trans('Period');
	print '</td>';
	print '<td>';
	print $objectpe->select_period($fk_period,'fk_period','',0,1,1,true);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Concept');
	print '</td>';
	print '<td>';
	print $objectco->select_concept($fk_concept,'fk_concept','',0,1,"type_cod IN (1,2)");
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Typedockey');
	print '</td>';
	print '<td>';
	print select_updoc($typedoc,'docum');
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Delimiter');
	print '</td>';
	print '<td>';
	print '<input type="text" name="delimiter" value="'.GETPOST('delimiter').'" size="1">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';
	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

	print '</form>';
}
else
{
	if ($action == 'exit')
	{
		print_barre_liste($langs->trans("Uploadarchivesave"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		print '<table class="noborder" width="100%">';
	 //encabezado
		print '<tr class="liste_titre">';

		print '</tr>';
		print '</table>';
	}
	else
	{
		print_barre_liste($langs->trans("Uploadarchive"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		print "<form action=\"fiche.php\" method=\"post\">\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addSave">';
		print '<input type="hidden" name="fk_period" value="'.$fk_period.'">';
		print '<input type="hidden" name="fk_concept" value="'.$fk_concept.'">';
		print '<input type="hidden" name="docum" value="'.$docum.'">';
		print '<input type="hidden" name="delimiter" value="'.$delimiter.'">';

		print '<table class="noborder" width="100%">';
	 //encabezado
		foreach($aHeaders AS $i => $value)
		{
			$aHeadersOr[trim($value)] = trim($value);
		}

		$aValHeader = array();

		foreach($aHeaderTpl AS $i => $value)
		{
			if (!$aHeadersOr[trim($value)])
				$aValHeader[$value] = $value;
		}
		print '<tr class="liste_titre">';
		foreach($aHeaders AS $i => $value)
		{
			print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
		}
		print '</tr>';
		if (!empty($aValHeader))
		{
			$lSave = false;
			print "<tr class=\"liste_titre\">";
			print '<td>'.$langs->trans('Missingfields').'</td>';
			foreach ((array) $aValHeader AS $j => $value)
			{
				print '<td>'.$value.'</td>';
			}
			print '</tr>';
		}
		else
		{
			$lSave = true;
			$var=True;
			$c = 0;
			foreach($data AS $key)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					if (empty($keyname))
						$keyname = "none";
					$phone = $key->$keyname;
					$aArrData[$c][$keyname] = $phone;

					if ($keyname == 'fk_user')
					{
						$fkUser = '';
						//definiendo donde buscar al empleado
						if ($docum == 1)
						{
							//por id
							$fkUser = $phone;
							$background = '';
						}
						if ($docum == 2)
						{
							$objectpu->fetch_user('',$phone);
							//por login
							if ($objectpu->fk_user == $ph)
							{
								$fkUser = $objectpu->fk_user;
							}
							else
							{
								$lSave = false;
								setEventMessages($langs->trans('No existe el login'),null,'errors');
								$background = '#ff5B5B;';
							}
						}
						if ($docum == 3)
						{
							//por doc
							$filterstatic = " AND t.docum = '".trim($phone)."'";
							$res = $objectpu->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
							//if ($objectpu->docum == $phone)
							if ($res == 1)
							{
								$fkUser = $objectpu->fk_user;
							}
							else
							{
								$lSave = false;
								setEventMessages($langs->trans('No existe el documento'),null,'errors');
								$background = '#ff5B5B;';
							}
						}
						print '<td style="background:'.$background.'">'.$phone.'</td>';
					}
					else
						print '<td>'.$phone.'</td>';
				}

				print '</tr>';
			}

		}
		print '</table>';
		if ($lSave)
		{
			$_SESSION['aArrData'] = $aArrData;
			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
			print '</form>';
		}
	 //validando el encabezado
	}
}
llxFooter();
$db->close();
?>
