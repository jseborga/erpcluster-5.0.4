<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */

require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/guarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskcontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratdeduction.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/orderbook.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/orderbookadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/dict.lib.php';

//budget
if ($conf->budget->enabled)
{
	dol_include_once('/budget/class/html.formadd.class.php');
	dol_include_once('/budget/class/items.class.php');
	dol_include_once('/budget/class/cunits.class.php');
	dol_include_once('/budget/class/typeitem.class.php');
}

if ($conf->addendum->enabled)
	require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';
if ($conf->sales->enabled)
	require_once DOL_DOCUMENT_ROOT.'/sales/class/contratext.class.php';
else
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratext.class.php';


//images
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$id=GETPOST('id','int');
$idc=GETPOST('idc','int');
$idg=GETPOST('idg','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$project_id = $id;
$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);
$backtopage=GETPOST('backtopage','alpha');

//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$projectstatic = new Project($db);
$object = new Task($db);
$taskstatic = new Task($db);
$taskadd = new Taskext($db);
$mobject = new Taskext($db);
$contratadd = new Contratext($db);
$contratded = new contratdeduction($db);
$objdoc = new Projettasktimedoc($db);
$guarantees = new Guarantees($db);
$taskcontrat = new Projettaskcontrat($db);
$objecttaskadd = new Projettaskadd($db);
$objuser = new User($db);

//$cunits = new Cunits($db);
if ($conf->budget->enabled)
{
	$typeitem = new Typeitem($db);
	$items = new Items($db);
	$cunits = new Cunits($db);
}

//armamos los campos obligatorios
$aHeaderTpl['llx_projet_task'] = array('ref' => 'ref',
	'label' => 'label',
	'hilo' => 'hilo',
	'login' => 'login',
	'fechaini'=>'fechaini',
	'fechafin'=>'fechafin',
	'group'=>'group',
	'type'=>'type',
	'typename'=>'typename',
	'unitprogram'=>'unitprogram',
	'unit'=>'unit',
	'price' => 'price',);
$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aCampodate = array('fechaini' =>'date_start', 'fechafin' => 'date_end');

$userstatic    = new User($db);
$companystatic = new Societe($db);
$extrafields   = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($projectstatic->table_element);
$extralabels_task=$extrafields_task->fetch_name_optionals_label($object->table_element);

if ($conf->addendum->enabled) $addendum = new Addendum($db);


if ($ref)
{
	$projectstatic->fetch($id,$ref);
	$id=$projectstatic->id;
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);

$langs->load("users");
$langs->load("projects");
$langs->load("monprojet@monprojet");

/* actualizacion de datos en tareas add*/
//	if ($conf->global->MONPROJET_MODIF_ORDERREF)
//	{
if ($id>0)
	$projectstatic->fetch($id);

//}
/*
 * Actions
 */

if ($action == 'addbook' && $user->rights->monprojet->book->crear)
{
	$objbook = new Orderbook($db);
	$filter = array(1=>1);
	$filterstatic = " AND fk_contrat = ".$idc;
	$numbook = $objbook->fetchAll('ASC', 't.ref', 0, 0,$filter, 'AND',$filterstatic);
	$numbook++;
	$db->begin();
	$date_order = dol_mktime($_POST['dateohour'],$_POST['dateomin'],0,$_POST['dateomonth'],$_POST['dateoday'],$_POST['dateoyear'],'user');
	$objbook->initAsSpecimen();
	$objbook->date_order = $date_order;
	$objbook->fk_projet = GETPOST('id');
	$objbook->fk_parent = GETPOST('idp')+0;
	$objbook->fk_contrat = GETPOST('idc');
	$objbook->ref = $numbook;
	$objbook->fk_user_create = $user->id;
	$objbook->fk_user_validate = 0;
	$objbook->detail = GETPOST('detail','alpha');
	$objbook->tms = dol_now();
	$objbook->date_create = dol_now();
	$objbook->statut = 0;
	$res = $objbook->create($user);
	if ($res<=0)
	{
		$error++;
		setEventMessages($objbook->error,$objbook->errors,'errors');
	}

		//add photo/////////////////////////////////////////
		// Logo/Photo save
	$newDir = $res.generarcodigoale(4);
	$projectstatic->fetch($id);
	$dirproj = $projectstatic->ref;
	$contratadd->fetch($idc);
	$dircontrat = $contratadd->ref;
		//$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask;
	$dira = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj;
		//$dirb    = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj.'/contrat/'.$dircontrat;
	$dir  = $conf->monprojet->multidir_output[$conf->entity].'/'.$dirproj.'/contrat/'.$dircontrat;

	$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
	$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
	if ($file_OKfin)
	{
			//verificamos permisos para el modo de subida de archivos
		$mode = 0;
		$mode = $user->rights->monprojet->pho->up4;
		if ($user->rights->monprojet->pho->up3) $mode = 3;
		if ($user->rights->monprojet->pho->up2) $mode = 2;
		if ($user->rights->monprojet->pho->up1) $mode = 1;
		if ($user->rights->monprojet->pho->up5) $mode = 5;


		if (GETPOST('deletedocfin'))
		{
			$fileimg=$dir.'/'.$namefile;
			$dirthumbs=$dir.'/thumbs';
			dol_delete_file($fileimg);
			dol_delete_dir_recursive($dirthumbs);
		}
		if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
		{
			dol_mkdir($dira);
				//dol_mkdir($dirb);
			dol_mkdir($dir);
			if (@is_dir($dir))
			{
				$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
				$file = '';
				foreach ((array) $aFile AS $j => $val)
				{
					if (empty($file))
						$file = $newDir;
					else
						$file.= '.'.$val;
				}
				$newfile = $dir.'/'.$file;
				$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
				if (! $result > 0)
				{
					$error++;
					$errors[] = "ErrorFailedToSaveFile";
				}
				else
				{
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
					$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
				}
			}
			else
			{
				$error++;
			}
		}
		else
		{
			$error++;
			$errors[] = "ErrorBadImageFormat";
		}
		switch($_FILES['docpdf']['error'])
		{
			case 1:
		  //uploaded file exceeds the upload_max_filesize directive in php.ini
			case 2:
		   //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			$errors[] = "ErrorFileSizeTooLarge";
			$error++;
			break;
			case 3:
		   //uploaded file was only partially uploaded
			$error++;
			$errors[] = "ErrorFilePartiallyUploaded";
			break;
		}
	}
	if ($error)
	{
		$db->rollback();
		$action = 'createbook';
	}
	else
	{
		$db->commit();
		if (!empty($file))
		{
			$objbook->fetch($res);
			$objbook->document = $file;
			$objbook->update($user);
		}
		unset($_POST);
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id.'&idc='.$idc.'&action=createbook',1);
		header("Location: ".$urltogo);
		exit;
	}
}

//editbook
if ($action == 'editbook' && $user->rights->monprojet->book->mod)
{
	$objbook = new Orderbook($db);
	$objbook->fetch(GETPOST('idr','int'));
	if ($objbook->id == GETPOST('idr') && $objbook->fk_contrat == GETPOST('idc'))
	{
		$date_order = dol_mktime($_POST['dateohour'],$_POST['dateomin'],0,$_POST['dateomonth'],$_POST['dateoday'],$_POST['dateoyear'],'user');
		$objbook->date_order = $date_order;

	}
	$objbook->detail = GETPOST('detail','alpha');
	$objbook->tms = dol_now();

	$res = $objbook->update($user);
	if ($res<=0)
	{
		$error++;
		setEventMessages($objbook->error,$objbook->errors,'errors');
	}
	if ($error)
		$action = 'bedit';
	else
	{
		unset($_POST);
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id.'&idc='.$idc.'&action=createbook',1);
		header("Location: ".$urltogo);
		exit;
	}
}

if ($action == 'confirm_sbook' && $user->rights->monprojet->book->val)
{
	$objbook = new Orderbook($db);
	$objbook->fetch(GETPOST('idr','int'));
	if ($objbook->id == GETPOST('idr') && $objbook->fk_contrat == GETPOST('idc'))
	{
		$objbook->statut = 1;
		$objbook->tms = dol_now();

		$res = $objbook->update($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($objbook->error,$objbook->errors,'errors');
		}
		if ($error)
			$action = 'obook';
		else
		{
			unset($_POST);
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id.'&idc='.$idc.'&action=obook',1);
			header("Location: ".$urltogo);
			exit;
		}
	}
}

if ($action == 'deldoc' && $user->rights->monprojet->bookimg->del)
{
	$objbook = new Orderbook($db);
	$objbook->fetch(GETPOST('linedoc'));
	$object->fetch($id);
	$error=0;
	if ($objbook->fk_projet == $id)
	{
		$contratadd->fetch($objbook->fk_contrat);
		$namefile = GETPOST('namedoc');
	   	//buscamos
		$db->begin();

		$aDoc = explode(';',$objbook->document);
		$document = '';
		foreach ((array) $aDoc AS $i => $name)
		{
			if ($name != $namefile)
			{
				if ($document) $document.=';';
				$document .= $name;
			}
		}
		$objbook->document = $document;
		$res = $objbook->update($user);
		if (!$res>0) $error++;

	    //del photo
		$dirproj = $projectstatic->ref;
		$dir    = $conf->monprojet->multidir_output[$conf->entity].'/'.$projectstatic->ref.'/contrat/'.$contratadd->ref;

		$fileimg=$dir.'/'.$namefile;
		dol_delete_file($fileimg);
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
	$action = '';
}

if ($action == 'addded')
{
	$aAmount = GETPOST('amount');
	foreach ((array) $aAmount AS $code => $value)
	{
		//buscamos las retenciones del contrato
		$filter = array(1=>1);
		$filterstatic = " AND fk_contrat = ".$idc;
		$filterstatic.= " AND t.code = '".$code."'";
		$numcded = $contratded->fetchAll('', '', 0, 0,$filter, 'AND',$filterstatic,true);
		if ($numcded==1)
		{
			//existe y actualizamos
			$contratded->amount = $value;
			$contratded->fk_user_mod = $user->id;
			$contratded->tms = dol_now();
			$res = $contratded->update($user);
			if (!$res>0)
			{
				$error++;
				setEventMessages($contratded->error,$contratded->errors,'errors');
			}
		}
		else
		{
			//existe y actualizamos
			$contratded->fk_contrat = $idc;
			$contratded->code = $code;
			$contratded->amount = $value;
			$contratded->percentage = 0;
			$contratded->fk_user_create = $user->id;
			$contratded->fk_user_mod = $user->id;
			$contratded->date_create = dol_now();
			$contratded->tms = dol_now();
			$contratded->statut = 1;
			$res = $contratded->create($user);
			if (!$res>0)
			{
				$error++;
				setEventMessages($contratded->error,$contratded->errors,'errors');
			}
		}
	}
	if ($error)
		$action = 'createded';
	else
	{
		unset($_POST);
		$action='deduc';
	}
}
if ($action == 'addup')
{
	//subida de tareas para el contrato
	//primero actualizamos el order_ref
	$aTasknumref = unserialize($_SESSION['aTasknumref'][$projectstatic->id]);
	//$tasksarray = $taskadd->lines;
	//if (count($tasksarray) > 0)
	//{
		//include DOL_DOCUMENT_ROOT.'/monprojet/tpl/modif_orderref.tpl.php';
	//}
	//fin order_ref
	//si variable addtask entonces se inserta o actualiza en las tareas del proyecto
	$taskstat = new Task($db);

	$addtask = GETPOST('addtask');
	$seldate = GETPOST('seldate');
	$error = 0;
	//buscamos el projet
	$res = $projectstatic->fetch($id);
	if (!$res>0)
	{
		setEventMessages($projectstatic->error,$projectstatic->errors,'errors');
		$error=99;
	}
	$error = 0;
	$aArrData = $_SESSION['aArrData'];
	$table = GETPOST('table');
	$lUtility = true;
	$aNewTask = array();
	$db->begin();
	$sumContrat = 0;
	foreach ((array) $aArrData AS $i => $data)
	{
		//vamos verificando la existencia de cada uno
		$fk_task_parent = 0;
		if (!empty($data['hilo']))
		{
			if (!empty($aNewTask[$data['hilo']]))
				$fk_task_parent = $aNewTask[$data['hilo']];
			else
				$error=100;
		}

		//unit
		$fk_unit = 0;
		if (!empty($data['unit']))
		{
			$cunits->fetch('',$data['unit']);
			if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
			{
				//recuperamos el id de registro
				$fk_unit = $cunits->id;
			}
			else
			{
				//creamos
				$cunits->initAsSpecimen();
				$cunits->code= $data['unit'];
				$cunits->label= $data['unitlabel'];
				$cunits->short_label= $data['unit'];
				$cunits->active= 1;
				$resunit = $cunits->create($user);
				if ($resunit >0) $fk_unit = $resunit;
				else
				{
					setEventMessages($cunits->error,$cunits->errors,'errors');
					$error=101;
				}
			}
		}
		//verificamos si esta relacionado a un item
		$fk_item = 0;
		if (!empty($data['item']))
		{
			if ($conf->budget->enabled)
			{
				//buscamos
				$resitem = $items->fetch('',$data['item']);
				if ($resitem>0)
				{
					if (STRTOUPPER($items->ref) == STRTOUPPER($data['item']))
					{
						$_POST['options_fk_item'] = $items->id;
						$fk_item = $items->id;
					}
				}
				else
				{
					$error=102;
					setEventMessages($items->error,$items->errors,'errors');
				}
			}
		}
		else
			$_POST['options_fk_item'] = 0;

		//verificamos tipo item
		$fk_type_item = 0;
		if (!empty($data['type']))
		{
			//buscamos
			if ($conf->budget->enabled)
			{
				$restype = $typeitem->fetch('',$data['type']);
				if ($restype>0)
				{
					if (STRTOUPPER($typeitem->ref) == STRTOUPPER($data['type']))
					{
						$_POST['options_fk_type'] = $typeitem->id;
						$fk_type_item = $typeitem->id;
					}
				}
				elseif($restype==0)
				{
					//agregamos
					$typeitem->entity = $conf->entity;
					$typeitem->ref = $data['type'];
					$typeitem->detail = $data['typename'];
					$typeitem->fk_user_create = $user->id;
					$typeitem->fk_user_mod = $user->id;
					$typeitem->date_create = dol_now();
					$typeitem->tms = dol_now();
					$typeitem->statut = 1;
					$rest = $typeitem->create($user);
					if ($rest<=0)
					{
						setEventMessages($typeitem->error,$typeitem->errors,'errors');
						$error=1021;
					}
					else
					{
						$fk_type_item = $rest;
						$_POST['options_fk_type'] = $rest;
					}
				}
				else
				{
					$error=103;
					setEventMessages($typeitem->error,$typeitem->errors,'errors');
				}
			}
			else
				$_POST['options_fk_type'] = 0;
		}
		else
			$_POST['options_fk_type'] = 0;

		if ($error)
		{
			if ($lUtility)
			{
				$lUtility = false;
				setEventMessages($langs->trans("Errorutilities",$langs->transnoentitiesnoconv("Items")),null,'errors');

			}
		}
		//$error = 0;
		//solo se insertaran en tareas del contrato aquellos que tengan c_grupo != 1
		//verificamos las fechas
		$date_start = getformatdate($seldate,$data['fechaini']);
		$date_end   = getformatdate($seldate,$data['fechafin']);
		if ($data['group'] != 1)
		{
			//buscamos si existe la tarea en el contrato
			$filter = array('ref'=>$data['ref']);
			$filterstatic = " AND fk_projet = ".$id;
			$filterstatic.= " AND fk_contrat = ".$idc;
			$numrow = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,True);
			if ($numrow==1)
			{
				//actualizamos
				$taskcontrat->ref = trim($data['ref']);
				$taskcontrat->entity = $conf->entity;
				$taskcontrat->fk_projet = $projectstatic->id;
				$taskcontrat->fk_contrat = $idc;
				$taskcontrat->datec = dol_now();
				$taskcontrat->tms = dol_now();
				$taskcontrat->dateo = $date_start;
				$taskcontrat->datee = $date_end;
				$taskcontrat->datev = dol_now();
				$taskcontrat->label = trim($data['label']);
				$taskcontrat->description = $data['detail'];
				$taskcontrat->priority = $data['priority']+0;
				$taskcontrat->fk_user_creat = $user->id;
				$taskcontrat->fk_user_valid = $user->id;
				$taskcontrat->c_grupo = $data['group'];
				$taskcontrat->fk_type = $fk_type_item;
				$taskcontrat->unit_program = $data['unitprogram'];
				$taskcontrat->fk_unit = $fk_unit;
				$taskcontrat->unit_amount = $data['price'];
				$taskcontrat->fk_statut = 1;
				$sumContrat += $data['price'] * $data['unitprogram'];
				$res = $taskcontrat->update($user);
				if ($res<=0)
				{
					setEventMessages($taskcontrat->error,$taskcontrat->errors,'errors');
					$error=105;
				}
			}
			else
			{
				//nuevo
				$taskcontrat->ref = trim($data['ref']);
				$taskcontrat->entity = $conf->entity;
				$taskcontrat->fk_projet = $projectstatic->id;
				$taskcontrat->fk_contrat = $idc;
				$taskcontrat->datec = dol_now();
				$taskcontrat->tms = dol_now();
				$taskcontrat->dateo = $date_start;
				$taskcontrat->datee = $date_end;
				$taskcontrat->datev = dol_now();
				$taskcontrat->label = trim($data['label']);
				$taskcontrat->description = $data['detail'];
				$taskcontrat->priority = $data['priority']+0;
				$taskcontrat->fk_user_creat = $user->id;
				$taskcontrat->fk_user_valid = $user->id;
				$taskcontrat->c_grupo = $data['group'];
				$taskcontrat->fk_type = $fk_type_item;
				$taskcontrat->unit_program = $data['unitprogram'];
				$taskcontrat->fk_unit = $fk_unit;
				$taskcontrat->unit_amount = $data['price'];
				$taskcontrat->fk_statut = 1;
				$sumContrat += $data['price'] * $data['unitprogram'];
				$res = $taskcontrat->create($user);
				if ($res<=0)
				{
					setEventMessages($taskcontrat->error,$taskcontrat->errors,'errors');
					$error=106;
				}
			}
			//echo '<hr>d '.$error;
		}
		if ($addtask && !$error)
		{
			//buscamos si existe la tarea
			$filter = array(1=>1);
			$filterstatic = " AND t.ref = '".trim($data['ref'])."'";
			$filterstatic.= " AND t.fk_projet = ".$projectstatic->id;
			$res = $taskadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,1);
			if ($res>0)
			{
				//existe
				//debemos obtener la suma total de la tarea
				//segun contrato y adendas
				$filter = array('ref'=>$data['ref']);
				$filterstatic = " AND fk_projet = ".$id;
				$filterstatic.= " AND fk_statut = 1";
				$numrow = $taskcontrat->fetchAll('','',0,0,$filter,'AND',$filterstatic,True);
				$sumatask = 0;
				if ($numrow>0)
				{
					foreach($taskcontrat->lines AS $j => $line)
						$sumatask+= $line->unit_program;
				}
				else
					$sumatask = $data['unitprogram'];
				$task = new Task($db);
				if ($task->fetch($taskadd->id)>0)
				{
					//buscamos si existe el campo con formato fecha
					foreach((array) $aCampodate AS $k => $value)
					{
						if($data[$k])
						{
							//verificamos y damos formato a la variable
							$resvalue = convertdate($aDatef,$seldate,$data[$k]);
							$task->$value = $resvalue;
						}
					}

					$aNewTask[$data['ref']] = $task->id;
						//actualizamos el valor
					$_POST['options_c_grupo'] = $data['group'];
					$_POST['options_c_view'] = $data['view'];
						//		    $_POST['options_unit_program'] = $data['unitprogram'];//modificar
					$_POST['options_unit_program'] = $sumatask;
					$_POST['options_fk_unit'] = $fk_unit;
					$_POST['options_unit_amount'] = $data['price'];
					$task->dateo = $date_start;
					$task->datee = $date_end;
					$task->fk_task_parent = $fk_task_parent +0;
					$task->ref = $data['ref'];
					$task->label = trim($data['label']);
					$task->description = $data['detail'];
					$task->priority = $data['priority']+0;
					$task->rang = $i;
					if (empty($fk_task_parent))
						$level = 0;
					else
						$level = $aLevel[$fk_task_parent]+1;
					$aLevel[$task->id] = $level;
					// Fill array 'array_options' with data from add form
					$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
					$aTasknumref[$task->id] = array('fk_task_parent'=>$fk_task_parent,'ref'=> $task->ref,'level'=>$level,'reg'=>$i,'group'=>$data['group']);
					//if (!$ret > 0) $error++;

					//actualizamos datos adicionales de la tarea
					$res = $objecttaskadd->fetch('',$task->id);
					if ($res>0 && $objecttaskadd->fk_task == $task->id)
					{
						$objecttaskadd->fk_item = $fk_item;
						$objecttaskadd->fk_type_item = $fk_type_item;
						$objecttaskadd->c_grupo = $data['group'];
						$objecttaskadd->level = $level;
						//$objecttaskadd->unit_program = $data['unitprogram'];//modificar
						$objecttaskadd->unit_program = $sumatask+0;
						$objecttaskadd->fk_unit = $fk_unit;
						$objecttaskadd->unit_amount = $data['price']+0;
						$objecttaskadd->fk_user_mod = $user->id;
						$objecttaskadd->tms = dol_now();
							//print_r($objecttaskadd);
						$res = $objecttaskadd->update($user);
						if ($res<=0)
						{
							setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
							$error=107;
						}
					}
					else
					{
							//echo '<hr>inserta ';
						$objecttaskadd->fk_task = $task->id;
						$objecttaskadd->fk_item = $fk_item;
						$objecttaskadd->fk_type_item = $fk_type_item;
						$objecttaskadd->c_grupo = $data['group'];
						$objecttaskadd->level = $level;
							//$objecttaskadd->unit_program = $data['unitprogram'];//modificar
						$objecttaskadd->unit_program = $sumatask+0;
						$objecttaskadd->fk_unit = $fk_unit;
						$objecttaskadd->unit_amount = $data['price']+0;
						$objecttaskadd->fk_user_create = $user->id;
						$objecttaskadd->fk_user_mod = $user->id;
						$objecttaskadd->date_create = dol_now();
						$objecttaskadd->tms = dol_now();
						$objecttaskadd->statut = 1;
						$res = $objecttaskadd->create($user);
						if ($res<=0)
						{
							setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
							$error=108;
						}
					}
					if (!$error)
					{
						$resup = $task->update($user,true);
						if ($resup<=0)
						{
							setEventMessages($task->error,$task-errors,'errors');
							$error=109;
						}
					}
				}
			}
			else
			{
				//creamos nuevo
				$_POST['options_c_grupo'] = $data['group'];
				$_POST['options_c_view'] = $data['view'];
				$_POST['options_unit_program'] = $data['unitprogram'];
				$_POST['options_fk_unit'] = $fk_unit;
				$_POST['options_unit_amount'] = $data['price'];
				$task = new Task($db);
				$task->initAsSpecimen();

				//buscamos si existe el campo con formato fecha
				foreach((array) $aCampodate AS $k => $value)
				{
					if($data[$k])
					{
						//verificamos y damos formato a la variable
						$resvalue = convertdate($aDatef,$seldate,$data[$k]);
						$task->$value = $resvalue;
					}
				}
				//asignamos el level
				$task->entity = $conf->entity;
				$task->fk_project = $id;
				$task->fk_task_parent = $fk_task_parent +0;
				$task->ref = trim($data['ref']);
				$task->label = trim($data['label']);
				$task->dateo = $date_start;
				$task->datee = $date_end;
				$task->description = $data['detail'];
				$task->fk_user_creat = $user->id;
				$task->priority = $data['priority']+0;
				$task->fk_statut = 1;
				$task->date_c = dol_now();
				$task->tms = dol_now();

					// Fill array 'array_options' with data from add form
				$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
				$result = $task->create($user,1);

				if ($result>0)
				{
					if (empty($fk_task_parent))
						$level = 0;
					else
						$level = $aLevel[$fk_task_parent]+1;
					$aLevel[$result] = $level;
				}
				else
				{
					setEventMessages($task->error,$task->errors,'errors');
					$error=200;
				}
				$aTasknumref[$result] = array('fk_task_parent'=>$fk_task_parent,'ref'=>$data['ref'],'level'=>$level,'reg'=>$i,'group'=>$data['group']);
				if (!$error)
				{

					$objecttaskadd->order_ref = $aOrderref[$task->id];

					$objecttaskadd->fk_task = $result;
					$objecttaskadd->c_grupo = $data['group'];
					$objecttaskadd->level = $aLevel[$result];
					$objecttaskadd->unit_program = $data['unitprogram']+0;
					$objecttaskadd->fk_item = $fk_item;
					$objecttaskadd->fk_type_item = $fk_type_item;
					$objecttaskadd->fk_unit = $fk_unit;
					$objecttaskadd->unit_amount = $data['price']+0;
					$objecttaskadd->fk_user_create = $user->id;
					$objecttaskadd->fk_user_mod = $user->id;
					$objecttaskadd->date_create = dol_now();
					$objecttaskadd->tms = dol_now();
					$objecttaskadd->statut = 1;
					$res = $objecttaskadd->create($user);
					if ($res<=0)
					{
						setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
						$error=201;
					}
				}

				if (!$error)
				{
					$aNewTask[$data['ref']] = $result;
						//buscamos al usuario contacto que es un array
					$aLogin = explode(';',$data['login']);
					foreach ((array) $aLogin AS $l => $login)
					{
						$resuser = $objuser->fetch('',$login);
						if ($resuser>0)
						{
							$res = $task->add_contact($objuser->id, 'TASKEXECUTIVE', 'internal');
							if ($res<=0)
							{
								setEventMessages($task->error,$task-errors,'errors');
								$error=202;
							}
						}
					}
						//echo '<hr>l '.$error;
				}
				else
				{
					$error=203;
					setEventMessages($task->error,$task->errors,'errors');
				}
			}
		}
		else
		{
			//buscamos si existe la tarea
			$filter = array();
			$filterstatic = " AND t.ref = '".trim($data['ref'])."'";
			$filterstatic.= " AND t.fk_projet = ".$projectstatic->id;
			$res = $taskadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,1);
			if ($res>0)
			{
				$aNewTask[trim($data['ref'])] = $taskadd->id;
			}
		}
	}

	//ordenamos y actualizamos
	$aRef = array();
	$aNumberref = array();
	$aRefnumber = array();
	foreach((array) $aTasknumref AS $i => $data)
	{
		//verificamos el orden donde debe estar la tarea
		list($aRef,$aNumberref,$aRefnumber) = get_orderlastnew($i,$projectstatic->id,$data,$aRef,$aNumberref,$aRefnumber);
	}
	//una vez que se tenga el aRefnumber
	//actualizar el order_ref

	foreach ((array) $aNumberref AS $i => $value)
	{
		$objecttaskadd->fetch('',$i);
		if ($objecttaskadd->fk_task == $i)
		{
			//echo '<br>'.$i.' '.$value;
			$objecttaskadd->order_ref = $value;
			$res = $objecttaskadd->update_orderref();
			if ($res <=0)
			{
				$error=301;
				setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
			}
		}
		else
		{
			$error=302;
			echo '<br>no encuentra '.$i;
		}
	}
	//ordenamos las tareas por el order_ref
	if (!$error)
	{
		//echo '<hr>antes de actualizar el order '.$error;
		$taskadd->get_ordertask($projectstatic->id);
		$taskaddnew = new Taskext($db);
		//echo '<br>cuentalines '.count($taskadd->lines).' del id '.$projectstatic->id;
		if (count($taskadd->lines)>0)
		{
			$j = 1;
			foreach($taskadd->lines AS $i => $data)
			{
				$fk = $data->id;
				$res = $taskaddnew->fetch($fk);
				if ($res >0)
				{
					//echo '<br>procesando el reemplazo a '.$j .' de '.$taskstatic->rang.' delid '.$data->id.'|'.$fk.' encontrado |'.$taskaddnew->id.'|';
					$taskaddnew->rang = $j;
					$res = $taskaddnew->update_rang($user);
					if ($res <= 0)
					{
						$error=310;
						setEventMessages($taskaddnew->error,$taskaddnew->errors,'errors');
					}
					$j++;
				}
				else
				{
					$error=311;
					setEventMessages($taskaddnew->error,$taskaddnew->errors,'errors');
				}
			}
		}
	}
	//actualizamos el item del contrato
	$contratadd->getlist($id);
	$linec = $contratadd->linec;
	$idcd = 0;
	foreach ((array) $linec AS $j => $contrat)
	{
		if ($idc == $contrat->id)
		{
			foreach ((array) $contrat->lines AS $k => $contratdet)
			{
				if (empty($idcd))
				{
					$idcd = $contratdet->id;
					$date_start = $contratdet->date_start;
					$date_end = $contratdet->date_end;
					$tvatx = ($contratdet->tvatx?$contratdet->tvatx:$contratdet->tva_tx);
					//$tvatx = 14.9427;
				}
			}
		}
	}

	$contratadd->fetch($idc);
	if ($contratadd->id == $idc)
	{
		//recuperamos la linea
		$pu = $sumContrat;
		$typeprice = 'HT';
		if ($conf->global->MONPROJET_TAX_INCLUDED)
		{
			//$pu = price2num(($pu * (1 + ( $tvatx / 100))) - $pu, 'MU');
			$typeprice = 'TTC';
		}
		$res = $contratadd->updateline($idcd, 0, $pu, 1, 0, $date_start, $date_end, $tvatx, 0.0, 0.0, '', '', $typeprice, 0, 0, 0,0,  null);
		if ($res <=0)
		{
			setEventMessages($contratadd->error,$contratadd->errors,'errors');
			$error=401;
		}
	}
	if (!$error)
		$db->commit();
	else
	{
		setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
		$db->rollback();
	}
	$action = 'list';

}

//cancel
if ($action == 'confirm_seltask' && GETPOST('cancel')) $action='asignc';

if ($action == 'confirm_seltask' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->task->mod && $projectstatic->statut < 2)
{
	$error = 0;
		//actualizamos las tareas con el contrato seleccionado
	$aTask = $_SESSION['aSelectcont'];
	$db->begin();
	foreach ((array) $aTask AS $fk_task)
	{
			//buscamos la tarea
		$object->fetch($fk_task);
		if ($object->id == $fk_task)
		{
				//recuperamos los valores extrafields
				//mismos valores
			$_POST['options_unit_declared'] = $object->array_options['options_c_grupo'];
			$_POST['options_c_grupo'] = $object->array_options['options_c_grupo'];
			$_POST['options_unit_program'] = $object->array_options['options_unit_program'];
			$_POST['options_fk_unit'] = $object->array_options['options_fk_unit'];
			$_POST['options_fk_item'] = $object->array_options['options_fk_item'];
			$_POST['options_unit_amount'] = $object->array_options['options_unit_amount'];
			$_POST['options_unit_ejecuted'] = $object->array_options['options_unit_ejecuted']+0;
			$_POST['options_fk_contrat'] = $idc;
				// Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
			if ($ret < 0) $error++;
			if (! $error)
			{
				$result=$object->update($user);
				if (!$result > 0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		else
			$error++;
	}
	if (!$error)
	{
		$db->commit();
			// update OK
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
		$db->rollback();

}

	// Add
if ($action == 'add' && $user->rights->monprojet->cont->crear)
{
	$error = 0;
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	$guarantees->date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$guarantees->date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

	$guarantees->ref       = $_POST["ref"];
	$guarantees->code_guarantee     = GETPOST('code_guarantee');
	$guarantees->issuer = GETPOST('issuer','alpha');
	$guarantees->concept = GETPOST('concept','alpha');
	$guarantees->amount = GETPOST('amount');
	$guarantees->fk_contrat = GETPOST('idc','int');
	$guarantees->fk_user_create = $user->id;
	$guarantees->date_create = dol_now();
	$guarantees->tms = dol_now();
	$guarantees->statut = 1;

	if (empty($guarantees->code_guarantee))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorCodeguaranteeisrequired"), 'errors');
	}
	if (empty($guarantees->ref))
	{
		$error++;
		setEventMessages(null, $langs->trans("Errorrefrequired"), 'errors');
	}
	if (empty($guarantees->issuer))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorIssuerisrequired"), 'errors');
	}
	if (empty($guarantees->concept))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorConceptisrequired"), 'errors');
	}
	if (empty($guarantees->amount))
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorAmountisrequired"), 'errors');
	}

	if (empty($error))
	{
		$idg = $guarantees->create($user);
		if ($idg > 0)
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		$action = 'createg';
		$mesg='<div class="error">'.$guarantees->error.'</div>';
	}
	else
	{
		setEventMessages(null, $langs->trans("Errornotregister"), 'errors');
		if ($error) $action="createg";
			   // Force retour sur page creation
	}
}

	//update
if ($action == 'update' && $user->rights->monprojet->cont->mod)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	if ($guarantees->fetch($idg) > 0)
	{
		$error = 0;
		$guarantees->date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$guarantees->date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));

		$guarantees->ref       = $_POST["ref"];
		$guarantees->code_guarantee     = GETPOST('code_guarantee');
		$guarantees->issuer = GETPOST('issuer','alpha');
		$guarantees->concept = GETPOST('concept','alpha');
		$guarantees->amount = GETPOST('amount');
			//$guarantees->fk_contrat = GETPOST('fk_contrat');
		$guarantees->tms = dol_now();
		$guarantees->statut     = 1;

		if (empty($guarantees->ref))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
		}
		if (empty($guarantees->issuer))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorIssuerisrequired").'</div>';
		}
		if (empty($guarantees->concept))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorConceptisrequired").'</div>';
		}
		if (empty($guarantees->amount))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorAmountisrequired").'</div>';
		}

		if (empty($error))
		{
			$res = $guarantees->update($user);
			if ($res > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$guarantees->error.'</div>';
		}
		else
		{
			if ($error)
				$action="edit";
		  			// Force retour sur page creation
		}
	}
}

	// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->cont->del)
{
	$guarantees->fetch($_REQUEST["idg"]);
	$result=$guarantees->delete($user);
	if ($result > 0)
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/contrat.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$guarantees->error.'</div>';
		$action='';
	}
}

	// Part to create upload
if ($action == 'veriffile')
{
		//verificacion
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];
	$separator = GETPOST('separator','alpha');
	$tempdir = "tmp/";

		//    if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	if(dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo,1,10,0,$nombre_archivo))
	{

			//  echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	$csvfile = $tempdir.$nombre_archivo;
	$fh = fopen($csvfile, 'r');
	$headers = fgetcsv($fh);
	$aHeaders = explode($separator,$headers[0]);
	$data = array();
	$aData = array();
	while (! feof($fh))
	{
		$row = fgetcsv($fh,'','^');
		if (!empty($row))
		{
			$aData = explode($separator,$row[0]);
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
	$action = "verifup";
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

// None

/*
 * View
 */

$form=new Form($db);
$formother     = new FormOther($db);

$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');

if (! empty($conf->use_javascript_ajax))
{
	$arrayofjs=array(
		'/monprojet/js/jsgantt.js',
		'/monprojet/js/graphics.js',

		'/projet/jsgantt_language.js.php?lang='.$langs->defaultlang
		);
}

$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$langs->trans("Tasks"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

if ($id > 0 || ! empty($ref))
{
	$projectstatic->fetch($id,$ref);
	if ($projectstatic->societe->id > 0)  $result=$projectstatic->societe->fetch($projectstatic->societe->id);

	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $projectstatic->restrictedProjectArea($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab='contrat';

	$head=project_prepare_head($projectstatic);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/monprojet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
		$projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($projectstatic, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';

	print '<tr><td>'.$langs->trans("Agenda").'</td><td>';
	print '<a class="button" href="'.DOL_URL_ROOT.'/comm/action/card.php?action=create&socid='.$projectstatic->socid.'&projectid='.$projectstatic->id.'&backtopage=1&percentage=1">'.$langs->trans('Nuevo evento').'</a>';
	print '</td></tr>';

	// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	// if (! empty($projectstatic->societe->id)) print $projectstatic->societe->getNomUrl(1);
	// else print '&nbsp;';
	// print '</td>';
	// print '</tr>';

	// // Visibility
	// print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
	// if ($projectstatic->public) print $langs->trans('SharedProject');
	// else print $langs->trans('PrivateProject');
	// print '</td></tr>';

	// // Statut
	// print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

	// // Date start
	// print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	// print dol_print_date($projectstatic->date_start,'day');
	// print '</td></tr>';

	// // Date end
	// print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	// print dol_print_date($projectstatic->date_end,'day');
	// print '</td></tr>';


	print '</table>';

	print '</div>';
}


/*
 * contrat
 */

print '<br>';


// Get list of tasks in tasksarray and taskarrayfiltered
// We need all tasks (even not limited to a user because a task to user
// can have a parent that is not affected to him).
//$tasksarray = $object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
// We load also tasks limited to a particular user
//$tasksrole=($_REQUEST["mode"]=='mine' ? $task->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
//var_dump($tasksarray);
//var_dump($tasksrole);

//verificamos los items que contiene
$modetask = 0;
//$tasksarray=$taskadd->getTasksArray(0, 0, $projectstatic->id, $socid, $modetask);
//verificamos si las tareas estan asociadas al contrato o contratos del proyecto
// $lContrattask = false;
// foreach ((array) $tasksarray AS $j => $objtask)
// {
//   if (!$objtask->array_options['options_fk_contrat']>0)
//     $lContrattask = true;
// }

//recuperamos los contratos registrados
$contratadd->getlist($id);
//recuperamos todas las tareas del proyecto
$modetask = 0;
$modepay = 0;
$tasksarray = $taskadd->getTasksArray(0, 0, $projectstatic->id, $socid,$modetask,'',-1,'',0,0,0,1,0,'',$modepay);

//verificamos si tiene ya tareas guardadas
if (count($tasksarray)>0)
{
	$aTasknumref = array();
	for ($i=0; $i < count($tasksarray); $i++)
	{
		if ($tasksarray[$i]->id>0)
			$aTasknumref[$tasksarray[$i]->id] = array('fk_task_parent'=>$tasksarray[$i]->fk_parent,'ref'=>$tasksarray[$i]->ref,'level'=>$tasksarray[$i]->array_options['options_level'],'reg'=>$i,'group'=>$tasksarray[$i]->array_options['options_c_grupo']);
	}
	$_SESSION['aTasknumref'][$object->id] = serialize($aTasknumref);
}
else
	$_SESSION['aTasknumref'][$object->id] = serialize(array());

$linec = $contratadd->linec;
if (count($linec)>0)
{
	if ($projectstatic->socid)
	{
		print '&nbsp;<a class="button" href="'.DOL_URL_ROOT.'/sales/contrat/card.php?socid='.$projectstatic->socid.'&projectid='.$projectstatic->id.'&action=create'.'">'.$langs->trans('Addendum').'</a>';
	}

	dol_fiche_head();

	if ($action == 'seltask')
	{
		$aSelcon = GETPOST('selcon');
		unset($_SESSION['aSelectcont']);
		foreach ((array) $aSelcon AS $j => $value)
			$_SESSION['aSelectcont'][$j]=$j;
		$_SESSION['seltask_post'] = $_POST;
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idc='.$idc,
			$langs->trans("Linktaskstocontrat"),
			$langs->trans("Confirmlinktaskstocontrat",$object->ref),
			"confirm_seltask",
			'',
			0,2);
		if ($ret == 'html') print '<br>';
	}

	print '<table class="noborder centpercent">'."\n";
	// Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Action'),'','','',$param,' align="left"');
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
	print_liste_field_titre($langs->trans('Notepublic'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Guarantees'),$_SERVER['PHP_SELF'],'','',$param,' align="center" colspan="6"');
	print '</tr>';
	//armamos los contratos
	$var = true;
	$aArray = array();
	$sumatot = 0;
	foreach ((array) $linec AS $j => $contrat)
	{
		if ($conf->addendum->enabled)
		{
			$addendum->getlist_son($contrat->id,'fk_contrat_father');
			if (count($addendum->array)>0)
				$link = '';
			foreach ((array) $addendum->array AS $l => $objc)
				$aArray[$objc->fk_contrat_son] = $objc->fk_contrat_son;
		}
		$var = !$var;
		if ($idc != $contrat->id)
			print "<tr $bc[$var]>";
		else
			print '<tr class="backmark">';
		print '<td>';
		if (empty($aArray[$contrat->id]))
		{
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$contrat->id.'&action=createg">'.img_picto($langs->trans('Createguarantee'),DOL_URL_ROOT.'/monprojet/img/guarantee','',true).'</a>';
		}
		else
			print '&nbsp;';
		if ($lContrattask)
		{
			print '&nbsp;';
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$contrat->id.'&action=asignc">'.img_picto($langs->trans('Linkcontracttasks'),DOL_URL_ROOT.'/monprojet/img/contrat','',true).'</a>';
		}
		else
		{
			print '&nbsp;';
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$contrat->id.'&action=asignd">'.img_picto($langs->trans('Taskofthecontract'),DOL_URL_ROOT.'/monprojet/img/contratok','',true).'</a>';
		}
		if ($user->rights->monprojet->cont->ded && empty($aArray[$contrat->id]))
		{
			print '&nbsp;';
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$contrat->id.'&action=deduc">'.img_picto($langs->trans('Retentionsthecontract'),DOL_URL_ROOT.'/monprojet/img/retention','',true).'</a>';
		}
		if ($user->rights->monprojet->book->leer && empty($aArray[$contrat->id]))
		{
			print '&nbsp;';
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$contrat->id.'&action=obook">'.img_picto($langs->trans('Orderbook'),DOL_URL_ROOT.'/monprojet/img/obook','width="15px"',true).'</a>';
		}

		print '</td>';
		print '<td>';
		$contratadd->fetch($contrat->id);
		print ($aArray[$contrat->id]?img_picto('','rightarrow').'&nbsp;&nbsp;&nbsp;':'');
		if (empty($aArray[$contrat->id]))
			print $contratadd->getNomUrladd(1);
		else
			print $contratadd->ref;
		print '</td>';
		print '<td>';
		print dol_print_date($contrat->date_contrat,'day');
		print '</td>';
		print '<td align="right">';

		//verificamos cuanto suma el contrato
		$sumac = 0;
		$lines = $contrat->lines;
		foreach ((array) $contrat->lines AS $k => $contratdet)
		{
			$sumac+= $contratdet->total_ttc;
		}
		//filtramos las tareas del contrato
		$filter = array(1=>1);
		$filterstatic = " AND fk_projet = ".$id;
		$filterstatic.= " AND fk_contrat = ".$contrat->id;
		$numtask = $taskcontrat->fetchAll('ASC', 't.ref', 0, 0,$filter, 'AND',$filterstatic);
		//listamos
		foreach ((array) $taskcontrat->lines AS $i => $line)
		{
			$total_ttc = 0;
			foreach ((array) $contrat->lines AS $k => $line)
			{
				$total_ttc += price2num($line->total_ttc,'MT');
			}
		}
		if ($total_ttc>0)
		{
			print price($total_ttc);
			$sumatot += price2num($total_ttc,'MT');
		}
		else
		{
			print price($sumac);
			$sumatot += price2num($sumac,'MT');
		}
		//$sumatot += price2num($total_ttc,'MT');
		print '</td>';
		print '<td>';
		print $contrat->note_public;
		print '</td>';
		//verificamos las garantias
		$filter= array(1=>1);
		$filterstatic = " AND fk_contrat = ".$contrat->id;
		$guarantees->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		$m = 0;
		if (count($guarantees->lines)>0)
		{
			foreach ((array) $guarantees->lines AS $n => $lineg)
			{
				if (!empty($n))
				{
					print '<tr>';
					print '<td colspan="5">&nbsp;</td>';
				}
				print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idg='.$lineg->id.'">'.$lineg->ref.'</a>'.'</td>';

				print '<td>'.select_code_guarantees($lineg->code_guarantee,'code_guarantee','',0,1).'</td>';
				print '<td>'.$lineg->issuer.'</td>';
				print '<td>'.dol_print_date($lineg->dateini,'day').'</td>';
				print '<td>'.dol_print_date($lineg->datefin,'day').'</td>';
				print '<td>'.price($lineg->amount).'</td>';
				print '</tr>';
			}
		}
		else
		{
			print '<td colspan="6">&nbsp;</td>';
			print '</tr>';
		}
		if (count($aArray)>0 && $abc)
		{
				//mostramos todos los contratos hijos
			foreach ((array) $aArray AS $fk_son)
			{
				$var = !$var;
				if ($idc != $fk_son)
					print "<tr $bc[$var]>";
				else
					print '<tr class="backmark">';
				print '<td>';
				print '&nbsp;';
				if ($lContrattask)
				{
					print '&nbsp;';
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$fk_son.'&action=asignc">'.img_picto($langs->trans('Linkcontracttasks'),DOL_URL_ROOT.'/monprojet/img/contrat','',true).'</a>';
				}
				else
				{
					print '&nbsp;';
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$fk_son.'&action=asignd">'.img_picto($langs->trans('Taskofthecontract'),DOL_URL_ROOT.'/monprojet/img/contratok','',true).'</a>';
				}

				print '</td>';
				print '<td>';
				$contratadd->fetch($fk_son);
				print ($aArray[$fk_son]?img_picto('','rightarrow').'&nbsp;&nbsp;&nbsp;':'');
				if (empty($aArray[$contrat->id]))
					print $contratadd->getNomUrl(1);
				else
					print $contratadd->ref;
				print '</td>';
				print '<td>';
				print dol_print_date($contratadd->date_contrat,'day');
				print '</td>';
				print '<td align="right">';
				$total_ttc = 0;
				foreach ((array) $contratadd->lines AS $k => $line)
					$total_ttc += price2num($line->total_ttc,'MT');
				print price($total_ttc);
				$sumatot += $total_ttc;
				print '</td>';
				print '<td>';
				print $contratadd->note_public;
				print '</td>';
				print '<td colspan="6">&nbsp;</td>';
				print '</tr>';
			}
		}
	}
		//totales
	print '<tr class="liste_total" align="right">';
	print '<td colspan="3">'.$langs->trans('Total').'</td>';
	print '<td>'.price(price2num($sumatot,'MT')).'</td>';
	print '<td colspan="7">&nbsp;</td>';
	print '</tr>';
	print '</table>';
	dol_fiche_end();
}
else
{
	print $langs->trans("NoContrat");
	if ($projectstatic->socid)
	{
		if ($conf->sales->enabled)
			print '&nbsp;<a class="button" href="'.DOL_URL_ROOT.'/sales/contrat/card.php?socid='.$projectstatic->socid.'&projectid='.$projectstatic->id.'&action=create'.'">'.$langs->trans('Newcontract').'</a>';
		else
			print '&nbsp;<a class="button" href="'.DOL_URL_ROOT.'/contrat/card.php?socid='.$projectstatic->socid.'&projectid='.$projectstatic->id.'&action=create'.'">'.$langs->trans('Newcontract').'</a>';
	}
}

	//crea garantia
if ($action == 'createg')
{
	if (empty($aArray[$fk_contrat]))
	{
		print_fiche_titre($langs->trans("Newguarantee"));
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';

		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idc" value="'.$idc.'">';

		dol_htmloutput_mesg($mesg);
		dol_fiche_head();
		print '<table class="border centpercent">';

			// contrat
		print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
		$contratadd->fetch($idc);
		print $contratadd->ref;
		print '</td></tr>';

			//type guarantee
		print '<tr><td class="fieldrequired">'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
		print select_code_guarantees($object->code_guarantee,'code_guarantee','',1,0);
		'<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
		print '</td></tr>';

			//Ref
		print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
		print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="20" maxlength="30">';
		print '</td></tr>';

			//Issuer
		print '<tr><td class="fieldrequired">'.$langs->trans('Issuer').'</td><td colspan="2">';
		print '<input id="issuer" type="text" value="'.$object->issuer.'" name="issuer" size="50" maxlength="150">';
		print '</td></tr>';

			//concept
		print '<tr><td class="fieldrequired">'.$langs->trans('Concept').'</td><td colspan="2">';
		print '<input id="concept" type="text" value="'.$object->concept.'" name="concept" size="50">';
		print '</td></tr>';

			//dateini
		print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
		$form->select_date($object->date_ini,'di_','','','',"date",1,1);
		print '</td></tr>';

			//datefin
		print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
		$form->select_date($object->date_fin,'df_','','','',"date",1,1);
		print '</td></tr>';

			//amount
		print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
		print '<input id="amount" type="number" step="any" value="'.$object->amount.'" name="amount" size="15">';
		print '</td></tr>';

		print '</table>';
		dol_fiche_end();
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
		print '</form>';
	}
}
	//edita garantia
if ($idg && $action == 'edit')
{
	$guarantees->fetch($idg);
	if (empty($aArray[$guarantees->fk_contrat]))
	{
		print_fiche_titre($langs->trans("Edit"));
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';

		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idc" value="'.$idg.'">';

		dol_htmloutput_mesg($mesg);
		dol_fiche_head();
		print '<table class="border" width="100%">';

			// contrat
		print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
		$contratadd->fetch($guarantees->fk_contrat);
		print $contratadd->ref;
		print '</td></tr>';

			//type guarantee
		print '<tr><td class="fieldrequired">'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
		print select_code_guarantees($guarantees->code_guarantee,'code_guarantee','',1,0);
		'<input id="label" type="text" value="'.$guarantees->label.'" name="label" size="50" maxlength="255">';
		print '</td></tr>';

			//Ref
		print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
		print '<input id="ref" type="text" value="'.$guarantees->ref.'" name="ref" size="20" maxlength="30">';
		print '</td></tr>';

			//Issuer
		print '<tr><td class="fieldrequired">'.$langs->trans('Issuer').'</td><td colspan="2">';
		print '<input id="issuer" type="text" value="'.$guarantees->issuer.'" name="issuer" size="50" maxlength="150">';
		print '</td></tr>';

			//concept
		print '<tr><td class="fieldrequired">'.$langs->trans('Concept').'</td><td colspan="2">';
		print '<input id="concept" type="text" value="'.$guarantees->concept.'" name="concept" size="50">';
		print '</td></tr>';

			//dateini
		print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
		$form->select_date($guarantees->date_ini,'di_','','','',"date",1,1);
		print '</td></tr>';

			//datefin
		print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
		$form->select_date($guarantees->date_fin,'df_','','','',"date",1,1);
		print '</td></tr>';

			//amount
		print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
		print '<input id="amount" type="number" step="any" value="'.$guarantees->amount.'" name="amount" size="15">';
		print '</td></tr>';

		print '</table>';
		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';

	}
}
	//retenciones al contrato
if ($action=="deduc" || $action == 'createded')
{
	print_fiche_titre($langs->trans("Retentions"));

		//listamos
	if ($action == 'createded')
	{
		print '<form  action="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$idc.'" method="POST">';
		print '<input type="hidden" name="action" value="addded">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="idc" value="'.$idc.'">';
	}
	dol_fiche_head();
	print '<table class="noborder centpercent">'."\n";
		// Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Label'),'','','',$param,' align="left"');
	print_liste_field_titre($langs->trans('Amount'),'','','',$param,' align="right"');
	print '</tr>';

	$arraydeduc = getlist_deduction('','ASC','sequence');
	foreach ((array) $arraydeduc AS $j => $data)
	{
			//filtramos las retenciones del contrato
		$filter = array(1=>1);
		$filterstatic = " AND fk_contrat = ".$idc;
		$filterstatic.= " AND t.code = '".$data['code']."'";
		$numcded = $contratded->fetchAll('', '', 0, 0,$filter, 'AND',$filterstatic,true);

		$var = !$var;
		print "<tr $b[$var]>";
		print '<td>';
		print select_type_deduction($data['code'],'','',0,1,'code');
		print '</td>';
		print '<td align="right">';
		if ($action == 'createded')
		{
			print '<input type="number" min="0" step="any" name="amount['.$data['code'].']" value="'.$contratded->amount.'">';
		}
		else
			print price($contratded->amount);
		print '</td>';
		print '</tr>';
	}
	print '</table>';
	if ($action == 'createded')
	{
		print '<br>';
		print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'">';
		print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</center>';
		print '</form>';
	}
	print dol_fiche_end();

	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */

	print "<div class=\"tabsAction\">\n";

	if ($user->rights->monprojet->cont->crear)
	{
		if ($action != 'createded')
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createded&id='.$id.'&idc='.$idc.'">'.$langs->trans("Recordretentions").'</a>';
	}
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Recordretentions")."</a>";
	print "</div>";

}

if ($action=="asignc" || $action=="asignd")
{
		//filtramos las tareas del contrato
	$filter = array(1=>1);
	$filterstatic = " AND fk_projet = ".$id;
	$filterstatic.= " AND fk_contrat = ".$idc;
	$numtask = $taskcontrat->fetchAll('ASC', 't.ref', 0, 0,$filter, 'AND',$filterstatic);
		//listamos
	$sumat = 0;
	dol_fiche_head();
	print '<table class="noborder centpercent">'."\n";
		// Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$param,' align="left"');
	print_liste_field_titre($langs->trans('Dateini'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Datefin'),$_SERVER['PHP_SELF'],'','',$param,'');
	print_liste_field_titre($langs->trans('Unit'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
	print_liste_field_titre($langs->trans('Programmed'),$_SERVER['PHP_SELF'],'','',$param,'align="right"');
	print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,' align="right"');
	print_liste_field_titre($langs->trans('Total'),$_SERVER['PHP_SELF'],'','',$param,' align="right"');
	print '</tr>';

	foreach ((array) $taskcontrat->lines AS $i => $line)
	{
		$var = !$var;
		print "<tr $b[$var]>";
		print '<td>';
		print $line->label;
		print '</td>';
		print '<td>';
		print dol_print_date($line->date_start,'day');
		print '</td>';
		print '<td>';
		print dol_print_date($line->date_end,'day');
		print '</td>';
		print '<td align="center">';
		$unit = $taskadd->getLabelOfUnit('short',$line->fk_unit);
		print $unit;
		print '</td>';
		print '<td align="right">';
		print price($line->unit_program);
		print '</td>';
		print '<td align="right">';
		print price($line->unit_amount);
		print '</td>';
		print '<td align="right">';
		print price( price2num($line->unit_program * $line->unit_amount,'MT') );
		print '</td>';

		print '</tr>';
		$sumat += price2num($line->unit_program * $line->unit_amount,'MT');
	}
		//totales
	print "<tr $b[$var]>";
	print '<td colspan="6">';
	print $langs->trans('Total');
	print '</td>';
	print '<td align="right">';
	print price(price2num($sumat,'MT'));
	print '</td>';
	print '</tr>';

	print '</table>';
	dol_fiche_end();

	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */

	print "<div class=\"tabsAction\">\n";

	if ($user->rights->monprojet->cont->crear)
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createup&id='.$id.'&idc='.$idc.'">'.$langs->trans("Uploadtask").'</a>';
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Uploadtask")."</a>";
	print "</div>";

}

if ($action == 'createup' && $user->rights->monprojet->cont->crear )
{
	print_fiche_titre($langs->trans("New"));

	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="veriffile">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idc" value="'.$idc.'">';


	print '<table class="border centpercent">'."\n";
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("File").'</td><td>';
	print '<input type="file" class="flat" name="archivo" id="archivo" required>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Dateformat');
	print '</td>';
	print '<td>';
	print $form->selectarray('seldate',$aDatef,'',1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Campos date');
	print '</td>';
	print '<td>';
	print '<input type="text" name="camposdate" size="50">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2" required>';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';
	print '<div>';
	print '<span>';
	print $langs->trans('Es necesario un archivo CSV con las siguientes columnas').':';
	print '</span>';
	print '<div>'.'ref'.' =>  <span>'.$langs->trans('Codigo de la tarea').'</span>'.'</div>';
	print '<div>'.'label'.' => <span>'.$langs->trans('Descripcion de la tarea').'</span>'.'</div>';
	print '<div>'.'hilo'.' => <span>'.$langs->trans('Hilo de la tarea').'</span>'.'</div>';
	print '<div>'.'item'.' => <span>'.$langs->trans('Registre el codigo del item').'</span>'.'</div>';
	print '<div>'.'login'.' => <span>'.$langs->trans('Asignado a, registre el login del usuario').'</span>'.'</div>';
	print '<div>'.'fechaini'.' => <span>'.$langs->trans('Fecha inicio de la tarea').'</span>'.'</div>';
	print '<div>'.'fechafin'.' => <span>'.$langs->trans('Fecha final de la tarea').'</span>'.'</div>';
	print '<div>'.'detail'.' => <span>'.$langs->trans('Descripcion de la tarea').'</span>'.'</div>';
	print '<div>'.'group'.' => <span>'.$langs->trans('0=No grupo; 1=Si grupo').'</span>'.'</div>';
	print '<div>'.'type'.' => <span>'.$langs->trans('Codigo tipo de item').'</span>'.'</div>';
	print '<div>'.'typename'.' => <span>'.$langs->trans('Nombre tipo de item').'</span>'.'</div>';
	print '<div>'.'unitprogram'.' => <span>'.$langs->trans('Unidades programadas').'</span>'.'</div>';
	print '<div>'.'unit'.' => <span>'.$langs->trans('Unidad de medida').'</span>'.'</div>';
	print '<div>'.'price'.' => <span>'.$langs->trans('Precio unitario').'</span>'.'</div>';
	print '</div>';
	print '<br>';
	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Upload").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</center>';
	print '</form>';
	dol_fiche_end();
}

if ($action == 'verifup')
{
	dol_fiche_head();
	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="addup">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="table" value="'.$table.'">';
	print '<input type="hidden" name="seldate" value="'.$seldate.'">';
	print '<input type="hidden" name="camposdate" value="'.$camposdate.'">';
	print '<input type="hidden" name="separator" value="'.$separator.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idc" value="'.$idc.'">';

	print '<table class="noborder" width="100%">';

		//encabezado
	$table = 'llx_projet_task';
	foreach($aHeaders AS $i => $value)
	{
		$aHeadersOr[trim($value)] = trim($value);
	}
	$aValHeader = array();
	foreach($aHeaderTpl[$table] AS $i => $value)
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
		print '<td>'.$langs->trans('Missingfieldss').'</td>';
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
		foreach($data AS $key){
			$var=!$var;
			print "<tr $bc[$var]>";
			$c++;
			foreach($aHeaders AS $i => $keyname)
			{
				if (empty($keyname))
					$keyname = "none";
				$phone = $key->$keyname;
				$aArrData[$c][$keyname] = $phone;
				print '<td>'.$phone.'</td>';
			}

			print '</tr>';
		}

	}
	print '</table>';

	If ($lSave)
	{
		print '<center>';
		print $langs->trans('Toupdateprojecttasks').'? '.' <input type="checkbox" name="addtask" value="1">';
		print '<br>';
		print $langs->trans('Quantitiesandunitpricesforeachtaskisupdatedornewtasksarecreated');
		print '</center>';
		$_SESSION['aArrData'] = $aArrData;
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
	}
		//validando el encabezado
	print '</form>';

	dol_fiche_end();
}

if ($idg && (empty($action) || $action=='delete'))
{
	$guarantees->fetch($idg);
	print_fiche_titre($langs->trans("Viewguarantee"));
	dol_htmloutput_mesg($mesg);
	dol_fiche_head();

		// Confirm delete third party
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idg='.$idg,$langs->trans("Deleteguarantee"),$langs->trans("Confirmdeleteguarantee").' '.$guarantees->ref.' '.$langs->trans('The').' '.$guarantees->issuer,"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	print '<table class="border" width="100%">';

		// contrat
	print '<tr><td width="15%">'.$langs->trans('Contrat').'</td><td colspan="2">';
	$contratadd->fetch($guarantees->fk_contrat);
	print $contratadd->ref;
	print '</td></tr>';

		//type guarantee
	print '<tr><td>'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
	print select_code_guarantees($guarantees->code_guarantee,'code_guarantee','',0,1);
	'<input id="label" type="text" value="'.$guarantees->label.'" name="label" size="50" maxlength="255">';
	print '</td></tr>';

		//Ref
	print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	print $guarantees->ref;
	print '</td></tr>';

		//Issuer
	print '<tr><td>'.$langs->trans('Issuer').'</td><td colspan="2">';
	print $guarantees->issuer;
	print '</td></tr>';

		//concept
	print '<tr><td>'.$langs->trans('Concept').'</td><td colspan="2">';
	print $guarantees->concept;
	print '</td></tr>';

		//dateini
	print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	print dol_print_date($guarantees->date_ini,'day');
	print '</td></tr>';

		//datefin
	print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	print dol_print_date($guarantees->date_fin,'day');
	print '</td></tr>';

		//amount
	print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
	print price($guarantees->amount);
	print '</td></tr>';

	print '</table>';
	dol_fiche_end();
	/* ******************************* */
	/*                                 */
	/* Barre d'action                  */
	/*                                 */
	/* ******************************* */

	print "<div class=\"tabsAction\">\n";
	if ($action == '')
	{
		if ($user->rights->monprojet->cont->crear)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$id.'&idg='.$guarantees->id.'">'.$langs->trans("Edit").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Edit")."</a>";

		if ($user->rights->monprojet->cont->del)
			print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$id.'&idg='.$guarantees->id.'">'.$langs->trans("Delete").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	}
	print "</div>";

}

if ($action == 'obook' || $action=='createbook' || $action == 'bedit' || $action == 'sbook')
{

	include DOL_DOCUMENT_ROOT.'/monprojet/tpl/orderbook.tpl.php';
}
llxFooter();

$db->close();
