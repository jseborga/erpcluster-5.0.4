<?php
/* Copyright (C) 2017-2017
 *
 * Importar los datos de un excel al modilo de assistance
 */



require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';


require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/csources.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/cpartida.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/partidaproductext.class.php");

dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/assistance/class/puser.class.php');



//excel para una versión anterior
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
$ver = 0;
if (file_exists($file))
{
	$ver = 1;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

//excel para version 4 o sup
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
if (file_exists($file))
{
	$ver = 2;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


$langs->load("almacen");
$langs->load("members");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$type  = GETPOST("type");
$fk_departament  = GETPOST("fk_departament");
$fk_concept = GETPOST("fk_concept");
$fk_entrepot = GETPOST("fk_entrepot");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$selrow = GETPOST('selrow');
$cancel = GETPOST('cancel');
$typeobjetive = GETPOST('typeobjetive');
$finality = GETPOST('finality');
$mesg = '';
if (!isset($_SESSION['period_year'])) $_SESSION['period_year']= date('Y');
$period_year = $_SESSION['period_year'];
if (GETPOST('period_year')) $period_year = GETPOST('period_year');

$dateimport  = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

$aDateimport = dol_getdate($dateimport);

//Declaramos los objectos que se manejaran
$objUser  = new User($db);
$objAssistance = new Assistance($db);
$objAdherent = new Adherentext($db);
$objCuser = new Puser($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aDate = dol_getdate(dol_now());



//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
 $aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB');
 $aCampoeje = array(1=>'area',2=>'siglaoperation',3=>'description',4=>'siglastructure',5=>'partida',6=>'cuenta',7=>'presupuesto',8=>'modificacion',9=>'aprobado',10=>'ejecutado',11=>'saldo',12=>'fk_objetive',13=>'fk_structure');

$aCampo= array(1=>'tiempo',2=>'usuario',3=>'nombre',4=>'apellido',5=>'tarjeta',6=>'dispositivo',7=>'punto',8=>'verificacion',9=>'estado',10=>'evento',11=>'nota',12=>'fechagen');
$aCamporev= array('tiempo'=>1,'usuario'=>2,'nombre'=>3,'apellido'=>4,'tarjeta'=>5,'dispositivo'=>6,'punto'=>7,'verificacion'=>8,'estado'=>9,'evento'=>10,'nota'=>11,'fechagen'=>12);

$aCampo= array(1=>'tiempo',2=>'usuario',3=>'nombre',4=>'apellido',5=>'tarjeta',6=>'dispositivo',7=>'punto',8=>'verificacion',9=>'estado',10=>'evento',11=>'nota',12=>'fechagen');
$aCamporev= array('tiempo'=>1,'usuario'=>2,'nombre'=>3,'apellido'=>4,'tarjeta'=>5,'dispositivo'=>6,'punto'=>7,'verificacion'=>8,'estado'=>9,'evento'=>10,'nota'=>11,'fechagen'=>12);


/************************
 *       Actions        *
 ************************/
$now = dol_now();
// AddSave
if ($action == 'add' && GETPOST('save') == $langs->trans('Save'))
{
	/*verificamos los tipos*/

	$error = 0;
	$arrayAssistance   = unserialize($_SESSION['arrayAsistencia']);

	//$valorPrimero = $arrayAssistance[1][2];
	$indMarcacion = 1;
	$c=0;
	$swValorPrimero = 0;
	$valorPrimero = 0;
	$feAcceso = 0;

	$db->begin();

	foreach ($arrayAssistance AS $r => $data)
	{
		$indMarcacion = 1;
		$ci = $r;

		foreach ($data 	AS $fecha => $row)
		{
			$fechamod = $row['tiempo'];
			$res = $objCuser->fetchAll('','',0,0,array(1=>1),'AND',"AND t.docum = '".$ci."'",true);
			if($res>0)
			{
				$fkmember = $objCuser->fk_user;
				$objtmp = new Assistance($db);
				$restmp = $objtmp->fetchAll('','',0,0,array(1=>1),'AND'," AND t.fk_member = ".$fkmember." AND t.date_ass = '".$db->idate($fechamod)."'" ,true);
				if (empty($restmp))
				{
					$objAssistance->entity = $conf->entity;
					$objAssistance->fk_soc = 0;
					$objAssistance->fk_contact = 0;
					$objAssistance->fk_member=$fkmember;
					$objAssistance->marking_number=$indMarcacion;
					$objAssistance->date_ass = $fechamod;
					$objAssistance->images = null;
					$objAssistance->fk_user_create = $user->id;
					$objAssistance->fk_user_mod = $user->id;
					$objAssistance->datec = dol_now();
					$objAssistance->datem = dol_now();
					$objAssistance->tms = dol_now();
					$objAssistance->active = 1;
					$objAssistance->statut = 1;
					$resQ = $objAssistance->create($user);
					$indMarcacion++;
					if($resQ <=0){
						$error++;
						setEventMessages($objAssistance->error,$objAssistance->errors,'errors');
					}
				}
			}
		}
	}
	if (!$error)
	{
		setEventMessages($langs->trans('Proceso satisfactorio de importación'),null,'mesgs');
		$db->commit();
		$action = '';
		//echo 'Contador : '.$c;
		//header('Location: '.$_SERVER['PHP_SELF']);
	}
	else
	{
		$action = '';
		$db->rollback();
	}

}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/********************************************
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Importmovements"),$help_url);

// Add
if ($action == 'edit')
{
	$selrow = GETPOST('selrow');
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

	$objPHPExcel = new PHPExcel();
	if ($type==1)
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	else
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	//phpinfo();
	//echo "Nombre de Archivo". $nombre_archivo;
	$objPHPExcel = $objReader->load('tmp/'.$nombre_archivo);

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');

	$aCurren = array();
	$loop = 11;

	$line=1;
	if ($selrow == 1)
	{
		$line++;
	}
	$nLimitempty = 3;
	$nLimit = 1;
	$lLoop = true;
	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getValue()))
		{
			$nLimit=1;
			//echo 'entra al if';exit;
			for ($a = 1; $a <= $loop; $a++)
			{
				$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
			}
		}
		else
		{
			if ($nLimit > $nLimitempty)
				$lLoop = false;
			else
				$nLimit++;
		}
		$line++;
	}
	$aNewdata = array();
	$aDate = $aDateimport;
	//vamos a armar un nuevo array agrupando por usuario y fecha
	foreach ($aCurren AS $line => $aData)
	{
		$aLines = array();
		for ($a = 1; $a <= $loop; $a++)
		{
			$aLines[$a] = $aData[$a];
		}
		$aNewdata[] = $aLines;
	}

	foreach ($aNewdata AS $j => $data)
	{
		foreach ($data AS $k => $value)
		{
			$row[$aCampo[$k]] = $value;
			if ($aCampo[$k] == 'tiempo')
			{
				//paso 1
				$aTime = explode(' ',$value);
				//para fecha
				//$aDatereg = dol_getdate(dol_stringtotime($aTime[0],1));
				$aDatereg = explode('/',$aTime[0]);
				$aHour = explode(':',$aTime[1]);

				if (($aDatereg[0] ==$aDate['mday'] && $aDatereg[1] == $aDate['mon']) || ($aDatereg[1] ==$aDate['mday'] && $aDatereg[0] == $aDate['mon']))
					$datenew  = dol_mktime($aHour[0],$aHour[1],$aHour[2],$aDate['mon'],$aDate['mday'],$aDate['year']);
				else
				{
					$error++;
					setEventMessages($langs->trans('Error, la fecha del archivo no coincide con el seleccionado, revise').' '.dol_print_date($dateimport,'day'),null,'errors');
				}
				//$row['fechamod'] = dol_stringtotime($value);
				//$row['fechamod'] = $datenew;
				//$data[12] = $datenew;
				$row['tiempo'] = $datenew;
				//$row['fechamodificada'] = dol_print_date($row['fechamod'],'dayhour');
			}
		}
		$aNew[$row['usuario']][$row['tiempo']] = $row;
	}
	$aArray = array();
	//ordenamos por fecha
	foreach ((array) $aNew AS $id => $data)
	{
		ksort($data);
		$aArray[$id] = $data;
	}

	//tabla ordenada

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<th>Nro</th><th>Fecha Hora</th><th>ID Usuario</th><th>Nombre</th><th>Apellido</th><th>Numero de Tarjeta</th><th>Dispositivo</th><th>Punto del Evento</th><th>Verificacion</th><th>Estado</th><th>Evento</th><th>Notas</th>';
	Print '</tr>';
	$nro = 1;
	foreach ($aArray AS $r => $data)
	{
		foreach ($data 	AS $fecha => $row)
		{
			$var =!$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$nro.'</td>';
			$i =0;
			foreach ($row 	AS $campo => $value)
			{
				if ($campo == 'tiempo')
					print '<td>'.dol_print_date($value,'dayhoursec').'</td>';
				else
					print '<td>'.$value.'</td>';
			}
			print '</tr>';
			$nro++;
		}
	}
	print '</table>';


	echo '<br>Errores encontrados : '.$error;
	//echo '<br>Numero de registros : '.;

	/*foreach ($aArray AS $r => $data)
	{


		foreach ($data 	AS $fecha => $row)
		{
			$i =0;
			foreach ($row 	AS $campo => $value)
			{
				if($i == 1){
					print('-> '.$value);
				}
				$i++;
			}

		}
	}*/

	/*echo ('<pre>');
	var_dump($aArray);
	echo ('</pre>');*/

	if (!$error)
	{
		$_SESSION['arrayAsistencia'] = serialize($aArray);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<input type="hidden" name="type" value="'.$type.'">';
		print '<input type="hidden" name="typeobjetive" value="'.GETPOST('typeobjetive').'">';
		print '<input type="hidden" name="finality" value="'.GETPOST('finality').'">';
		print '<input type="hidden" name="fk_departament" value="'.GETPOST('fk_departament').'">';
		print '<input type="hidden" name="dateimport" value="'.$dateimport.'">';
		print '<center><br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
		print '</form>';
	}




	dol_fiche_end();

}

$c=0;
	//$action = "edit";
	//***}

$aType = array(1=>$langs->trans('XLSX'),2=>$langs->trans('XLS'));
$aFinality = array(0=>$langs->trans('Gastos'));
if ($action == 'create' || empty($action))
{

	print_fiche_titre($langs->trans("Importassistances"));
	print '<form name="formrep" action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';

	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="edit">';

	dol_htmloutput_mesg($mesg);


	print '<table class="border" width="100%">';

	print '<tr><td>';
	print $langs->trans('Day');
	print '</td>';
	//print '<td>';
	//print '<input type="number" name="period_year" value="'.$period_year.'">';
	//print '</td></tr>';

	print '<td>';
	print $form->select_date(dol_now(),'di_',0,0,1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Primera fila es titulo');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Typefile');
	print '</td>';
	print '<td>';
	print $form->selectarray('type',$aType,GETPOST('type'),0);
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

llxFooter();
$db->close();

?>
