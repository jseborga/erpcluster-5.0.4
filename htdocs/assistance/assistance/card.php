<?php
/* Copyright (C) 2017 L Mendoza <l.mendoza.liet@gmail.com>
 *    Desarrolador PHP, JAVA
 */

/*
 * Comprobacion de Licencias para que manual
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

dol_include_once('/assistance/lib/assistance.lib.php');
dol_include_once('/assistance/lib/utils.lib.php');

require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/csources.class.php");

dol_include_once('/assistance/class/typemarkingext.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/salary/class/puser.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');

$langs->load("assistance");
$langs->load("others");

$socid   = GETPOST('socid','int');
$action  = GETPOST('action');
$cancel  = GETPOST('cancel');
$confirm = GETPOST('confirm');
$save    = GETPOST('save');
$id      = GETPOST('id');
$date    = GETPOST('date');
$mc      = GETPOST('mc');
if ($date>0)
{
	$aDateo = dol_getdate($date);
	$wday = $aDateo['wday'];
	$aDate  = dol_get_prev_day( zerofill($aDateo['mday'],2) , zerofill($aDateo['mon'],2) , $aDateo['year'] );
	$date_a = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year'],'user');
	$date_b = dol_mktime(23,59,59,zerofill($aDateo['mon'],2) , zerofill($aDateo['mday'],2) , $aDateo['year'],'user');
}
if ($user->societe_id) $socid=$user->societe_id;

// Security check
$result=restrictedArea($user,'societe',0,'','','','');

$thirdparty_static = new Societe($db);

//Declaramos los objectos que se manejaran
$object           = new Licencesext($db);
$objUser          = new User($db);
$objAssistance    = new Assistance($db);
$objAdherent      = new Adherentext($db);
$objCuser         = new Puser($db);
$objPuser         = new Puser($db);
$objAssistancedef = new Assistancedef($db);
$objTypemarking   = new Typemarkingext($db);

//variables definidas
$aMarking = array(1=>'primary_entry',2=>'primary_exit',3=>'secundary_entry',4=>'secundary_exit',5=>'third_entry',6=>'third_exit',7=>'fourth_entry',8=>'fourth_exit',9=>'fifth_entry',10=>'fifth_exit',11=>'sixth_entry',12=>'sixth_exit');
$aArrayMarking = array(1=>$langs->trans('Entryone'),2=>$langs->trans('Outputone'),3=>$langs->trans('Entrytwo'),4=>$langs->trans('Outputtwo'),5=>$langs->trans('Entrythird'),6=>$langs->trans('Outputthird'),7=>$langs->trans('Entryfour'),8=>$langs->trans('Outputfour'),9=>$langs->trans('Entryfifth'),10=>$langs->trans('Outputfifth'),11=>$langs->trans('Entrysixth'),12=>$langs->trans('Outputsixth'));

$aColordef['libre']    = $conf->global->ASSISTANCE_MARK_FREE;
$aColordef['normal']   = $conf->global->ASSISTANCE_MARK_NORMAL;
$aColordef['retraso']  = $conf->global->ASSISTANCE_MARK_RETRASO;
$aColordef['abandono'] = $conf->global->ASSISTANCE_MARK_ABANDONO;
$aColordef['licencia'] = $conf->global->ASSISTANCE_MARK_LICENCE;
$aColordef['vacation'] = $conf->global->ASSISTANCE_MARK_VACATION;
$aColordef['nomark']   = $conf->global->ASSISTANCE_MARK_NOMARK;
$aColordef['depure']   = $conf->global->ASSISTANCE_MARK_DEPURE;

$linkback = '<a href="'.DOL_URL_ROOT.'/assistance/assistance/list.php'.'">'.$langs->trans('Return').'</a>';

//datos adicionales del miembro
$rPuser = $objPuser->fetch(0,$id);

//verificamos el marcado definidio para el miembor o el por defecto
$lFixeddate = false;
$sex = 0;

list($fk_typemarking,$type_marking,$nroMark,$tolerancia,$lFixeddate) =  verif_type_marking($id,$wday);
$restype = $objTypemarking->fetch($fk_typemarking);

//fin verificacion de tipo de marcado

/*****************************
 *       Actions             *
 *****************************/
$now = dol_now();

	// Action to add record
if ($action == 'addhour')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance/card.php?mc='.$mc.'&date='.$date.'&id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}
	$aDatehoy = dol_getdate($date);
	$hhHoy = GETPOST('hour');
	$mmHoy = GETPOST('min');
	$dHoy = $aDatehoy['mday'];
	$mHoy = $aDatehoy['mon'];
	$yHoy = $aDatehoy['year'];
	$newDate = dol_mktime($hhHoy,$mmHoy,0,$mHoy,$dHoy,$yHoy,$user);

	$timetot = $hhHoy * 60 + $mmHoy;
	$error=0;



	$objAssistance->entity=$conf->entity;
	$objAssistance->fk_soc=GETPOST('fk_soc','int')+0;
	$objAssistance->fk_contact=GETPOST('fk_contact','int')+0;
	$objAssistance->fk_member=GETPOST('fk_member','int')+0;
	$objAssistance->date_ass=$newDate;
	$objAssistance->marking_number=9;
	$objAssistance->fk_user_create=$user->id;
	$objAssistance->fk_user_mod=$user->id;
	$objAssistance->datec = $now;
	$objAssistance->datem = $now;
	$objAssistance->tms = $now;
	$objAssistance->active = 1;
	$objAssistance->statut=1;

	if ($objAssistance->fk_member <=0 && $mc=='m')
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")),'errors');
	}
	if ($objAssistance->fk_member <= 0 && $objAssistance->fk_contact <=0 &&  $mc=='c')
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Contact")),'errors');
	}

	if (! $error)
	{
		$result=$objAssistance->create($user);
		if ($result > 0)
		{
					// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance/card.php?mc='.$mc.'&date='.$date.'&id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
					// Creation KO
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else  setEventMessages($object->error, null, 'errors');
			$action='create';
		}
	}
	else
	{
		$action='create';
	}
}


if($action == 'update' && $id>0 && $save == $langs->trans('Save'))
{
	$regid = GETPOST('regid');
	$refLicence = GETPOST('refLicence');
	$depura = GETPOST('depura');
	$marking_number = GETPOST('marking_number');
	$db->begin();
	foreach ($regid AS $fk => $nro)
	{
		if ($fk>0)
		{
			$res = $objAssistance->fetch($fk);
			if ($res == 1)
			{
				$objAssistance->active = 1;
				if ($depura[$fk]>0)
					$objAssistance->active = 0;
				$objAssistance->marking_number = $marking_number[$fk]+0;
				$objAssistance->fk_user_mod = $user->id;
				$objAssistance->datem = $now;

				$res = $objAssistance->update($user);
				if ($res<=0)
				{
					$error++;
					setEventMessages($objAssistance->error,$objAssistance->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objAssistance->error,$objAssistance->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		//header('Location: '.DOL_URL_ROOT.'/assistance/assistance/list.php');
		//exit;
	}
	else
	{
		$db->rollback();
		//$action = 'edit';
	}
	$action ='';
}

if($action == 'confirm_update' && $id>0 && $confirm == 'yes'){

	$aPost = unserialize($_SESSION['confirmUpdate']);

	$refLicence = $aPost[$id]['refLicence'];
	$depura = $aPost[$id]['depura'];
	$regid = $aPost[$id]['regid'];
	$marking_number = $aPost[$id]['marking_number'];
	$backwardness = $aPost[$id]['backwardness'];
	$abandonment = $aPost[$id]['abandonment'];

	$db->begin();
	foreach ($regid AS $fk => $nro)
	{
		$res = $objAssistance->fetch($fk);
		if ($res == 1)
		{
			if ($refLicence[$fk])
			{
				//buscamos la licencia para reemplazar su id
				$resl = $object->fetch(0,$refLicence[$fk]);
				if ($resl==1)
				{
					$objAssistance->fk_licence = $object->id;
					//actualizamos la hora 1
					if (empty($object->date_ini_ejec) || is_null($object->date_ini_ejec))
						$object->date_ini_ejec = $objAssistance->date_ass;
					elseif(empty($object->date_fin_ejec) || is_null($object->date_fin_ejec))
						$object->date_fin_ejec = $objAssistance->date_ass;
					if (!empty($object->date_ini_ejec) && !empty($object->date_fin_ejec))
						$object->statut = 4;
					$object->fk_user_mod = $user->id;
					$object->datem = $now;
					$resup = $object->update($user);
					if ($resup<=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
				}
			}
			if ($depura[$fk])
			{
				$objAssistance->active = 0;
			}
			if ($backwardness[$fk])
			{
				$objAssistance->backwardness = $backwardness[$fk];
			}
			if ($abandonment[$fk])
			{
				$objAssistance->abandonment = $abandonment[$fk];
			}
			$objAssistance->marking_number = $marking_number[$fk]+0;
			$objAssistance->fk_user_mod = $user->id;
			$objAssistance->datem = $now;
			$objAssistance->statut = 2;
			$res = $objAssistance->update($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objAssistance->error,$objAssistance->errors,'errors');
			}
		}
		else
		{
			$error++;
			setEventMessages($objAssistance->error,$objAssistance->errors,'errors');
		}
	}
	if (!$error)
	{
		$db->commit();
		header('Location: '.DOL_URL_ROOT.'/assistance/assistance/list.php');
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'edit';
	}
}


/*****************************
 * View                      *
 *****************************/

llxHeader("",$langs->trans("CheckMarkings"),$helpurl);
$form=new Form($db);

$res = $objCuser->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_user like '.$id,true);
$msgs = '<b>'.$langs->trans('Marcaciones del(a) Sr(a)').':</b> '.$objCuser->lastname.' '.$objCuser->firstname.' <b>'.$langs->trans('Date').':</b> '.dol_print_date($date,'day');

$transAreaType = $langs->trans($msgs);
$helpurl='EN:Module_Third_Parties|FR:Module_Tiers|ES:M&oacute;dulo_Terceros';



print load_fiche_titre($msgs,$linkback,'title_commercial.png');

if($action == 'segurodepurar'){
	$depurar = GETPOST("depurar");
	$_SESSION['depurar'] = serialize($depurar);
	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?idMember=10&fecha=2017-07-03 07:48:00',
		$langs->trans('Suguro de depurar las Marcaciones'),
		$msgs,
		'depurar',
		null ,
		0,
		2);
	print $formconfirm;

}
//verificamos la lista de licencias
$aLicence = array();
$filter = " AND t.fk_member = ".$id. " AND t.date_ini >= ".$db->idate($date_a)." AND t.date_fin <= ".$db->idate($date_b);
$filter.= " AND t.statut >= 2";
$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter);
$n = 1;
if ($res>0)
{
	$aLicence = $object->lines;
}
//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {';
	foreach ($aLicence AS $i => $obj)
	{
		print '$("#regini_'.$obj->id.'").change(function() {
			document.formsoc.action.value="create";
			document.formsoc.submit();
		});';
		print '$("#regfin_'.$obj->id.'").change(function() {
			document.formsoc.action.value="create";
			document.formsoc.submit();
		});';
	}
	print '});';
	print '</script>'."\n";
}

//confirm update

if ($action == 'update' && GETPOST('save') != $langs->trans('Save')) {

	$aPost[$id]['regid'] = GETPOST('regid');
	$aPost[$id]['refLicence'] = GETPOST('refLicence');
	$aPost[$id]['depura'] = GETPOST('depura');
	$aPost[$id]['marking_number'] = GETPOST('marking_number');
	$aPost[$id]['backwardness'] = GETPOST('backwardness');
	$aPost[$id]['abandonment'] = GETPOST('abandonment');
	$_SESSION['confirmUpdate'] = serialize($aPost);
	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, $langs->trans('Recordchanges'), $langs->trans('ConfirmRecordchanges'), 'confirm_update', '', 0, 1);
	print $formconfirm;
}


print '<form name="formsoc" action="'.$_SERVER['PHP_SELF'].'" method="post" >';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="date" value="'.$date.'">';

print '<div class="fichecenter"><div class="fichethirdleft">';

/******************************
 * Area de licencias
 ******************************/

print load_fiche_titre($langs->trans('Licencias').'/'.$langs->trans('Vacaciones'),'','');

/*
 * Lista de Licencias de los usuarios
 */
if ($aLicence)
{
	$transRecordedType = $langs->trans("LastModifiedThirdParties",$max);
	print "\n<!-- last thirdparties modified -->\n";
	print '<table class="noborder" width="100%">';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Nro"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Dateini"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Datefin"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Statut"),"liste.php", "","","","",$sortfield,$sortorder);
	print '</tr>'."\n";

	$var=True;
	$regini = GETPOST('regini');
	$regfin = GETPOST('regfin');
	$aSelect = array();
	foreach ($aLicence AS $i => $obj)
	{
		$var=!$var;
		print "<tr ".$bc[$var].">";
			// Name
		print '<td class="nowrap">'.$n."</td>\n";
		print '<td class="nowrap">'.$obj->ref."</td>\n";
		print '<td class="nowrap">'.dol_print_date($obj->date_ini,'dayhour');
		print '&nbsp;&nbsp;<input id="regini_'.$obj->id.'" type="number" min="0" max="100" name="regini['.$obj->id.']" value="'.$regini[$obj->id].'">';
		$aSelect[$regini[$obj->id]] = $obj->ref;
		print '</td>';
		print '<td class="nowrap">'.dol_print_date($obj->date_fin,'dayhour');
		print '&nbsp;&nbsp;<input id="regfin_'.$obj->id.'"type="number" min="0" max="100" name="regfin['.$obj->id.']"value="'.$regfin[$obj->id].'">';
		$aSelect[$regfin[$obj->id]] = $obj->ref;
		print '</td>';
		print '<td class="nowrap">'.$obj->statut."</td>\n";

		print "</tr>\n";
		$i++;
		$nro++;
	}
	print "</table>\n";
	print "<!-- End last thirdparties modified -->\n";
}

//print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';
//verificamos que esta definido en el usuario
if($restype && $lFixeddate)
{
	$lblMarcacion = $objTypemarking->detail;
	$title = $langs->trans('Specialmarked').' : '.$lblMarcacion.' : '.$langs->trans('Numbermark').'= '.$objTypemarking->mark;
}
else
{
	$title = $langs->trans('Daymarkings').' : '.$langs->trans('Numbermark').'= '.$objTypemarking->mark;
}

print load_fiche_titre($title,'','');

$third = array(
	'customer' => 0,
	'prospect' => 0,
	'supplier' => 0,
	'other' =>0
);
$total=0;


$filter = " AND t.fk_member = ".$id. " AND t.date_ass BETWEEN ".$db->idate($date_a)." AND ".$db->idate($date_b);
$res = $objAssistance->fetchAll('ASC','t.date_ass',0,0,array(1=>1),'AND',$filter);
$datetmp = array();
$data = array();
$dataid = array();
$aDatastatus = array();
$aDatalicence = array();
$aDataactive = array();
$aDatamarking=array();
$aDatabackwardness =array();
if ($res>0)
{
	$lines = $objAssistance->lines;
	$contreg = $num;
	$i = 0;
	$nLoop = 1;
	foreach ($lines AS $i => $obj)
	{
		$date_ass = $obj->date_ass;
		$datetmp[$nLoop]= $obj->date_ass;
		$data[$obj->date_ass] = $nLoop;
		$dataid[$obj->date_ass] = $obj->id;
		$aDataactive[$obj->date_ass] = $obj->active;
		$aDatastatus[$obj->date_ass] = $obj->statut;
		$aDatamarking[$obj->date_ass] = $obj->marking_number;
		$aDatamarkingnumber[$nLoop][$obj->marking_number] = $obj->date_ass;
		if ($obj->fk_licence > 0)
			$aDatalicence[$obj->date_ass] = $obj->fk_licence;
		$nLoop++;
	}
}
if ($data)
{
	//obtenemos las marcaciones del usuario
	$aMark = array();
	$objAdherent->fetch($id);
	$filter = " AND t.statut = 2 AND t.fk_member = ".$id." AND t.date_ini BETWEEN " .$db->idate($date_a)." AND " .$db->idate($date_b);
	$reslic = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter);

	//$res = $objAssistancedef->fetch(0,$id);
	$nroMark = 0;
	$noDef = false;

	if (empty($type_marking))
	{
		$noDef = true;
		setEventMessages($objAssistancedef->error,$objAssistancedef->errors,'errors');
	}
	else
	{

		//if (!$lFixeddate) $restype = $objTypemarking->fetch(0,$objAssistancedef->type_marking);
		if ($restype > 0)
		{
			for ($b=1; $b <= $objTypemarking->mark; $b++)
			{
				$campo = $aMarking[$b];
				$aMark[$b] = $objTypemarking->$campo;
				if (empty($aDatamarkingnumber[$b]))
				{
					$aDatamarkingnumber[$b] = 'F';
					if ($b==1) $aDatamarkingtmp[$b]= $objTypemarking->primary_entry;
					if ($b==2) $aDatamarkingtmp[$b]= $objTypemarking->primary_exit;
					if ($b==3) $aDatamarkingtmp[$b]= $objTypemarking->secundary_entry;
					if ($b==4) $aDatamarkingtmp[$b]= $objTypemarking->secundary_exit;
					if ($b==5) $aDatamarkingtmp[$b]= $objTypemarking->third_entry;
					if ($b==6) $aDatamarkingtmp[$b]= $objTypemarking->third_exit;
					if ($b==7) $aDatamarkingtmp[$b]= $objTypemarking->fourth_entry;
					if ($b==8) $aDatamarkingtmp[$b]= $objTypemarking->fourth_exit;
					if ($b==9) $aDatamarkingtmp[$b]= $objTypemarking->fifth_entry;
					if ($b==10) $aDatamarkingtmp[$b]= $objTypemarking->fifth_exit;
					if ($b==11) $aDatamarkingtmp[$b]= $objTypemarking->sixth_entry;
					if ($b==12) $aDatamarkingtmp[$b]= $objTypemarking->sixth_exit;
				}
			}
		}
	}
	$transRecordedType = $langs->trans("LastModifiedThirdParties",$max);
	print "\n<!-- last thirdparties modified -->\n";
	print '<table class="noborder" width="100%">';

	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Nro"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Marking"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Hour"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Atraso"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Abandono"),"liste.php", "","","","",$sortfield,$sortorder);

	print_liste_field_titre($langs->trans("Depurar"),"liste.php", "","","","",$sortfield,$sortorder);
	print '</tr>'."\n";
	$var=True;
	$i = 0;
	$nro = 1;
	$refLic = "LIC003";
	//revisamos los libres
	$datetmp = array();
	foreach ($data AS $datereg => $cont)
	{
		$datetmp[$cont]= $datereg;
	}
	$aLibre = array();
	if ($conf->global->ASSISTANCE_FREE_OUT && count($datetmp)>0)
	{
		for ($a = 2; $a<=18; $a++)
		{
			$b = $a+1;
			if ($datetmp[$a]>0 && $datetmp[$b]>0)
			{
				$aDini = dol_getdate($datetmp[$a]);
				$aDfin = dol_getdate($datetmp[$b]);
				$lReg = verif_time_range($aDini,$aDfin,$conf->global->ASSISTANCE_MINUTES_FREE,$id);
				if ($lReg)
				{
					$aLibre[$a]=$a;
					$aLibre[$b]=$b;
				}
				$a++;
			}
		}
	}
	//revisamos el estado del registro por member
	$status = '';
	$lStatut = true;
	foreach ($aDatastatus AS $datereg => $statut)
	{
		if (empty($status)) $status = $statut;
		else
		{
			if ($status != $statut) $lStatut = false;
		}
	}
	$nprint = 1;
	$nMarking = 0;
	//ksort($aDatamarkingnumber);
	//print_r($aDatamarkingnumber);
	foreach ($datetmp AS $cont => $datareg)
	{
		$lMark = false;
		if ($aLibre[$cont]) $lMark = true;
		if ($aDataactive[$datareg]==0) $lMark = true;
		if ($aDataactive[$datareg]==1 && $aLibre[$cont]) $lMark = false;
		$var=!$var;
		print "<tr ".$bc[$var].">";
		// Name
		print '<td class="nowrap">'.$nro;
		print '<input type="hidden" name="regid['.$dataid[$datareg].']" value="'.$nro.'">';
		print '</td>';
		if ($reslic && !empty($aSelect[$nro])) $aDataactive[$datareg] = 0;
		if ($aDataactive[$datareg]==1) $nMarking++;
		if ($datareg == 'F')
		{
			$aResult[0] = $langs->trans('nomark');
			$aResult[1] = $langs->trans('Falta');
			$nMarking++;
			//$aDataactive[$aDatamarkingtmp[$nMarking]] = 1;
		}

		print '<td class="nowrap">';
		if ($aDatastatus[$datareg]==2)
			print $aArrayMarking[$aDatamarking[$datareg]];
		else
			print $form->selectarray('marking_number['.$dataid[$datareg].']',$aArrayMarking,($aDataactive[$datareg]?$nMarking:0),1);
		print '</td>';
		$lAtraso = false;
		$lAbandono = false;
		if (!$aDatalicence[$datareg])
		{
			if ($aLibre[$nro])
			{
				//print '<td bgColor=#ffff00>'.dol_print_date($datareg,'hour').'</td>';
				print '<td bgColor="#'.$aColordef['libre'].'">'.dol_print_date($datareg,'hour').'</td>';
			}
			else
			{
				if ($datareg == 'F')
				{
					$aResult[0] = $langs->trans('nomark');
					$aResult[1] = $langs->trans('No marcado');
				}
				else
				{
					$aDate = dol_getdate($datareg);
					$aResult = verifica_retraso($aMark,$aDate,$nprint,$tolerancia,$id);
				}
				if ($aDataactive[$datareg]==0) $aResult[0] = 'depure';
				if ($aDataactive[$datareg]==0 && $aSelect[$nro]) $aResult[0] = 'vacation';
				if ($aResult[0]=='retraso')
					print '<input type="hidden" name="backwardness['.$dataid[$datareg].']" value="'.$aResult[2].'">';
				if ($aResult[0]=='abandono' )
					print '<input type="hidden" name="abandonment['.$dataid[$datareg].']" value="'.$aResult[2].'">';
				if ($datareg == 'F')
					print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.$aResult[1].'</td>';
				else
				{
					print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2).' '.($aResult[0]!='depure'?($aResult[1]?$aResult[0].' '.$aResult[1]:''):'').'</td>';
					if ($aResult[0]=='abandono') $lAbandono = true;
					if ($aResult[0]=='retraso') $lAtraso = true;
				}
				if ($aDataactive[$datareg]==1) $nprint++;
			}
		}
		else
		{
			print '<td bgColor="#'.$aColordef['licencia'].'">n'.dol_print_date($datareg,'hour').'</td>';
		}
		$filter = "AND t.fk_member = ".$id." AND ".$db->idate($datareg)." BETWEEN t.date_ini AND t.date_fin";
		$lic = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
		if($lic > 0){
			$refLic = $object->ref;
		}else{
			$refLic="";
		}
		if ($lStatut && $status==1 && $reslic>0)
			print '<td class="nowrap"><input id="'.$nro.'" type="text" name="refLicence['.$dataid[$datareg].']" value="'.$aSelect[$nro].'"></td>';
		else
			print '<td></td>';
		if ($lAtraso)
		{
			print '<td>'.$aResult[1];
			print '<input type="hidden" name="atraso['.$dataid[$datareg].']" value="'.$aResult[2].'">';
			print '</td>';
		}
		else
			print '<td></td>';
		if ($lAbandono)
		{
			print '<td>'.$aResult[1];
			print '<input type="hidden" name="abandono['.$dataid[$datareg].']" value="'.$aResult[2].'">';
			print '</td>';
		}
		else
			print '<td></td>';

		if (!$aSelect[$nro])
		{
			if ((!$lStatus && $status==1))
			{
				print '<td><input type="checkbox" name="depura['.$dataid[$datareg].']" value="'.$nro.'"  '.($lMark?'checked':'').'></td>';
			}
			else
				print '<td>'.($aDataactive[$datareg]==1?$langs->trans('Vigente'):$langs->trans('Depurado')).'</td>';

		}
		else
			print '<td></td>';
		print '</tr>';
		$i++;
		$nro++;
	}
}
else dol_print_error($db);

print '<tr class="liste_total"><td>'.$langs->trans("Numero de Marcaciones").'</td><td align="right">';
$nro--;
print $nro;
print '</td></tr>';
print '</table>';

print '<center>';
if (!$lStatus && $status==1)
{
	print '<br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
	print '&nbsp;<input type="submit" class="butAction" value="'.$langs->trans("SaveAndConfirm").'">';
}
print '<a class="butAction" href="'.DOL_URL_ROOT.'/assistance/assistance/list.php'.'">'.$langs->trans('Return').'</a>';
print '</center>';

//print '</td></tr></table>';
print '</div></div></div>';
print '</form>';

$aHour = array();
for ($a = 1; $a<=23;$a++)
{
	$aHour[$a] = $a;
}
$aMin = array();
for ($a = 1; $a<=59;$a++)
{
	$aMin[$a] = $a;
}

if ($user->rights->assistance->mem->crear)
{
	if ($action == 'create')
	{
		print_fiche_titre($langs->trans("Newmark"));

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="addhour">';
		print '<input type="hidden" name="mc" value="'.$mc.'">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="date" value="'.$date.'">';

		dol_fiche_head();
		print '<table class="border centpercent">'."\n";
		print '<tr><td class="fieldrequired">'.$langs->trans("Hour").'</td><td>';
		print $form->selectarray('hour',$aHour,date('H')).':'.$form->selectarray('min',$aMin,date('i'));
		if ($mc=='m')
		{
			//print $formadd->select_member($fk_member,'fk_member','',1,'','','','','autofocus');
			print '<input type="hidden" name="fk_member" value="'.$id.'">';
		}
		if ($mc=='c')
		{
			print '<input type="hidden" name="fk_contact" value="'.$object->fk_contact.'">';
		}
		print '</td></tr>';
		print '</table>'."\n";
		dol_fiche_end();
		print '<div class="center"><input type="submit" class="butAction" name="add" value="'.$langs->trans("Create").'">';
		print '<input type="submit" class="butAction" name="cancel" value="'.$langs->trans("Cancel").'">';
		print ' </div>';
		print '</form>';
	//$action = 'list';
	}
	if ($action != 'create')
	{
		dol_fiche_head();
		print '<table class="border centpercent">'."\n";
		print '<tr><td align="right">'.'<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create&id='.$id.'&date='.$date.'&mc=m">'.$langs->trans("Newmark").'</a></td><tr>';
		print '</table>'."\n";
		dol_fiche_end();
	}
}
llxFooter();

$db->close();
