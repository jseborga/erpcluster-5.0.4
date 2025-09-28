<?php
/* Copyright (C) 2017-2017
 *
 * Importar los datos de un excel al modilo de assistance
 */

$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$selrow = GETPOST('selrow');
$cancel = GETPOST('cancel');
$typeobjetive = GETPOST('typeobjetive');
$finality = GETPOST('finality');
$mesg = '';
if (!isset($_SESSION['period_year'])) $_SESSION['period_year']= date('Y');
$period_year = $_SESSION['period_year'];
$search_name = GETPOST('search_name');
// Purge search criteria

if (GETPOST("nosearch_x") || GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter"))

{
	$search_name='';
	$search_date=dol_now();
	$aDateo = dol_getdate(dol_now());
	$aDate = dol_get_prev_day( zerofill($aDateo['mday'],2) , zerofill($aDateo['mon'],2) , $aDateo['year'] );
	$_SESSION['markdate'] = $search_date;
	$_SESSION['markdate_a'] = dol_mktime(23,59,59,$aDate['month'],$aDate['day'],$aDate['year'],'user');
	$_SESSION['markdate_b'] = dol_mktime(23,59,59,zerofill($aDateo['mon'],2) , zerofill($aDateo['mday'],2) , $aDateo['year'],'user');
}

if (!isset($_SESSION['markdate_a']))
{
	$search_date = dol_now();
	$aDateo = dol_getdate(dol_now());
	//$aDate = dol_get_prev_day( zerofill($aDateo['mday'],2) , zerofill($aDateo['mon'],2) , $aDateo['year'] );

	$_SESSION['markdate'] = $search_date;
	$_SESSION['markdate_a'] = dol_mktime(0,0,0,$aDateo['mon'],$aDateo['mday'],$aDateo['year'],'user');
	$_SESSION['markdate_b'] = dol_mktime(23,59,59,$aDateo['mon'], $aDateo['mday'], $aDateo['year'],'user');

}
else
{
	if (isset($_POST['d_year']))
	{
		$search_date = dol_mktime(12,0,0,GETPOST('d_month'),GETPOST('d_day'),GETPOST('d_year'),'user');
		$_SESSION['markdate'] = $search_date;
		//$aDate = dol_get_prev_day(GETPOST('d_day'),GETPOST('d_month'),GETPOST('d_year'));
		$aDate = dol_getdate($search_date);
		$_SESSION['markdate_a'] = dol_mktime(0,0,0,$aDate['mon'],$aDate['mday'],$aDate['year'],'user');
		$_SESSION['markdate_b'] = dol_mktime(23,59,59,GETPOST('d_month'),GETPOST('d_day'),GETPOST('d_year'),'user');
	}
}
$date_a = $_SESSION['markdate_a'];
$date_b = $_SESSION['markdate_b'];
$search_date= $_SESSION['markdate'];
if (GETPOST('rev'))
{
	$_SESSION['markdate'] = GETPOST('search_date');
	$search_date = GETPOST('search_date');
}

//Declaramos los objectos que se manejaran
$objLicences=new Licencesext($db);
$objUser  = new User($db);
$objAssistance = new Assistanceext($db);
$objAdherent = new Adherentext($db);
$objCuser = new Puser($db);
$objPuser = new Puser($db);
$objAssistancedef = new Assistancedef($db);
$objTypemarking = new Typemarkingext($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

if (empty($aDate))
	$aDate = dol_getdate(dol_now());

//variables definidas
$aMarking = array(1=>'primary_entry',2=>'primary_exit',3=>'secundary_entry',4=>'secundary_exit',5=>'third_entry',6=>'third_exit',7=>'fourth_entry',8=>'fourth_exit',9=>'fifth_entry',10=>'fifth_exit',11=>'sixth_entry',12=>'sixth_exit');
$aColordef['libre']= $conf->global->ASSISTANCE_MARK_FREE;
$aColordef['normal']= $conf->global->ASSISTANCE_MARK_NORMAL;
$aColordef['retraso']= $conf->global->ASSISTANCE_MARK_RETRASO;
$aColordef['abandono']= $conf->global->ASSISTANCE_MARK_ABANDONO;
$aColordef['licencia']= $conf->global->ASSISTANCE_MARK_LICENCE;
$aColordef['vacation']= $conf->global->ASSISTANCE_MARK_VACATION;
$aColordef['nomark']= $conf->global->ASSISTANCE_MARK_NOMARK;
$aColordef['depure']= $conf->global->ASSISTANCE_MARK_DEPURE;

//Aqui es el filtro de las marcaciones
//verificamos si el tipo de marcación de la fecha es fijo o no
$lFixeddate = false;
$restype = $objTypemarking->fetchAll('','',0,0,array(),'AND'," AND t.fixed_date = ".$db->idate($search_date),true);
if ($restype == 1) {
	$lFixeddate = true;
	$fechaBuscar = $db->idate($search_date);
	//echo "RES TYPE DE SEX : ".$markSex    = $objTypemarking->sex;
}

/************************
 *       Actions        *
 ************************/
$now = dol_now();
// AddSave
if ($action == 'confirmarLicencia')
{
	header("Location: ".dol_buildpath('/assistance/licences.php',1));

}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/********************************************
 * View
 */

//llxHeader("",$langs->trans("Consultassistance"),$help_url);

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';

// Add

$idMember = GETPOST('fk_member');
if ($search_date) $aDate = dol_getdate($search_date);

$_SESSION['date_a'] = serialize($aDate);
$_SESSION['date_b'] = serialize($date_b);
$_SESSION['member'] = serialize($idMember);


if(GETPOST('sw')==1){
	$aDate  = GETPOST('a');
	$date_b = GETPOST('b');
	$idMember = GETPOST('fk_member');
	$_SESSION['date_a'] = serialize($aDate);
	$_SESSION['date_b'] = serialize($date_b);
	$_SESSION['member'] = serialize($idMember);
}


$sql = 'SELECT a.fk_member, a.date_ass, a.marking_number, a.fk_licence, a.statut, a.active ';
$sql.= " , t.lastname, t.firstname ";
$sql.= " , p.docum ";
$sql.= ' FROM '.MAIN_DB_PREFIX.'assistance as a';
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent  AS t ON a.fk_member = t.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user  AS p ON p.fk_user = t.rowid";
$sql.= " WHERE 1 AND a.fk_member = ".$id;
if ($search_name) $sql.= natural_search(array('t.lastname','t.firstname','p.docum' ),$search_name);

if(!empty($aDate)){
    $sql .=" AND a.statut >= 1 AND a.date_ass BETWEEN '".$db->idate($date_a)."' AND '".$db->idate($date_b)."'";

}else{
    $sql .=" AND a.statut >= 1 ";
}
//$sql.= " ORDER BY a.fk_member ASC, a.date_ass ASC ";
$sql.= " ORDER BY  a.date_ass ASC ,a.marking_number ASC";

$result = $db->query($sql);

if ($result){
	$num = $db->num_rows($result);
}else{
	setEventMessages('No existe registros',null,'mesgs');
	$action='';
		//exit;
}
$arrayMar = array();
$i = 0;
$u = 0;
$w = 0;
$aDatamark = array();
$aDatastatus = array();
$aDataactive = array();
$aDatalicence = array();
$aDatamarking=array();
$idMember = 0;
while ($i < $num)
{
	$obj = $db->fetch_object($result);
	if ($obj)
	{
		if ($idMember!= $obj->fk_member)
		{
			$line = 1;
			$idMember = $obj->fk_member;
		}
		$aDatetmp[$obj->fk_member][$line] = $db->jdate($obj->date_ass);
		$aDatamark[$obj->fk_member][$db->jdate($obj->date_ass)] = $line;
		$aDatastatus[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->statut;
		$aDataactive[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->active;
		$aDatamarking[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->marking_number;
		$aDatamarkingnumber[$obj->fk_member][$obj->marking_number] = $db->jdate($obj->date_ass);

		if ($obj->fk_licence > 0)
			$aDatalicence[$obj->fk_member][$db->jdate($obj->date_ass)] = $obj->fk_licence;
		$line++;
	}
	$i++;
}
	//echo 'El contador es : '.$i;


$nro = 1;

print_barre_liste($langs->trans("Assistancesearchengine"), $page, "liste.php", "", $sortfield, $sortorder,'',0);

//armamos el filtro
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="rowid" value='.$id.'>';


print '<div style="min-width:450px;overflow-x: auto; white-space: nowrap;">';
print '<table class="noborder" width="100%">';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Nro"),"liste.php", "","","","",$sortfield,$sortorder);
////print_liste_field_titre($langs->trans("Name"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Date"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entryone"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputone"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entrytwo"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputtwo"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entrythird"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputthird"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Entryfour"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Outputfour"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Statut"),"liste.php", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans(""),"liste.php", "","","","",$sortfield,$sortorder);
//print_liste_field_titre($langs->trans("Licences"),"liste.php", "sa.statut",'','','align="right"',$sortfield,$sortorder);
print "</tr>\n";

print "<tr class=\"liste_titre\">";
print '<td>'.'</td>';
//print '<td>'.'<input type="text"name="search_name" size="8" value="'.$search_name.'">'.'</td>';
//print '<td>'.'</td>';
print '<td>';
$form->select_date($search_date,'d_',0,0,1);
print '</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
print '<td>'.'</td>';
//print '<td>'.$form->selectarray('search_statut',$aStatus,$search_statut,1).'</td>';
print '<td>'.'</td>';
print '<td nowrap valign="top" align="right">';
print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
print '&nbsp;';
//print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
print '</td>';
print "</tr>";

$seq = 1;
$line = 1;
$dataExport = array();
foreach ($aDatamark AS $fk_member => $data)
{
	$aTmp = $aDatetmp[$fk_member];
	$aStatus = $aDatastatus[$fk_member];
	$aLicence = $aDatalicence[$fk_member];
	$aActive = $aDataactive[$fk_member];
	$aMarkingnumber = $aDatamarking[$fk_member];
	$aMarkingnum = $aDatamarkingnumber[$fk_member];
	$aMark = array();
	$objAdherent->fetch($fk_member);
	$filter = " AND t.statut >= 2 AND t.fk_member = ".$fk_member." AND t.date_ini BETWEEN " .$db->idate($date_a)." AND " .$db->idate($date_b);
	$reslic = $objLicences->fetchAll('','',0,0,array(1=>1),'AND',$filter);

	$res = $objAssistancedef->fetch(0,$fk_member);
	$nroMark = 0;
	$noDef = false;
	if ($res<=0)
	{
		$noDef = true;
		setEventMessages($objAssistancedef->error,$objAssistancedef->errors,'errors');
	}
	else
	{

		//if (!$lFixeddate) {
		if ($lFixeddate) {
			//Buscamos el sexo de cada Empleado
			$rPuser  = $objPuser->fetchAll('','',0,0,array(1=>1),'AND'," AND t.fk_user = ".$fk_member,true);
			//$restype = $objTypemarking->fetch(0,$objAssistancedef->type_marking);
			$restype = $objTypemarking->fetchAll('','',0,0,array(1=>1),'AND'," AND t.sex = ".$objPuser->sex. " AND t.fixed_date =".$fechaBuscar,true);

			if($restype != 1){
				$restype = $objTypemarking->fetch(0,$objAssistancedef->type_marking);
			}

			//echo "FIX VERDAD y RES : ".$restype."<br>";
			//echo "SEXO DEL MIEMBRE : ".$objPuser->sex." RES DE ".$rPuser." FK MIEMBRO : ".$fk_member." Fecha Buscar : ".$fechaBuscar;

		}else{
			$restype = $objTypemarking->fetch(0,$objAssistancedef->type_marking);
			//echo "FIX FALSO ";
		}
		//echo "TIPO DE MARCACION : ".$objAssistancedef->type_marking;
		if ($restype > 0)
		{
			$nroMark = $objTypemarking->mark;
			$tolerancia = $objTypemarking->additional_time;
			for ($b=1; $b <= $objTypemarking->mark; $b++)
			{
						//$aDate = dol_getdate($objTypemarking->);
				$campo = $aMarking[$b];
				$aMark[$b] = $objTypemarking->$campo;
				if (empty($aMarkingnum[$b]))
					$aMarkingnum[$b] = 'F';
			}
		}
		if ($objAssistancedef->aditional_time)
			$tolerancia = $objAssistancedef->aditional_time;
	}
	$var = !$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$seq.'</td>';
	$dataExport[$line]['seq'] = $seq;

	//print $objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
	$dataExport[$line]['name'] = $objAdherent->lastname.' '.$objAdherent->firstname;
	ksort($data);
	ksort($aMarkingnum);
	$lDate = true;
	$cont = 0;
	$contreg = count($data);
	$datetmp = array();
	foreach ($data AS $datereg => $cont)
	{
		$datetmp[$cont]= $datereg;
	}


	$aLibre = array();
	if ($conf->global->ASSISTANCE_FREE_OUT)
	{
		for ($a = 2; $a<=18; $a++)
		{
			$b = $a+1;
			if ($datetmp[$a]>0 && $datetmp[$b]>0)
			{
				$aDini = dol_getdate($datetmp[$a]);
				$aDfin = dol_getdate($datetmp[$b]);
				$lReg = verif_time_range($aDini,$aDfin,$conf->global->ASSISTANCE_MINUTES_FREE,$fk_member);
				if ($lReg)
				{
					$b = $a+1;
					$aLibre[$a]=$a;
					$aLibre[$b]=$b;
				}
				$a++;
			}
		}
	}
	$nprint = 1;
	//revisamos el estado del registro por member
	$status = '';
	$lStatut = true;
	foreach ($aStatus AS $datereg => $statut)
	{
		if (empty($status)) $status = $statut;
		else
		{
			if ($status != $statut) $lStatut = false;
		}
	}
	foreach ($aTmp AS $cont => $datereg)
	{
		if ($datereg == 'F') $aActive[$datereg]=1;
		if ($aActive[$datereg]==1)
		{
			//si esta revisado mostramos
			if ($lStatut && $status == 2)
			{
				if ($datereg == 'F')
				{
					$aResult[0]='nomark';
					$aResult[1]='No registrado';
					print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.$aResult[1].'</td>';
					$dataExport[$line][$nprint]['marca'] = 0;
					$dataExport[$line][$nprint]['resultado'] = $aResult[2];
					$nprint++;
				}
				else
				{
				if ($aMarkingnumber[$datereg] == $nprint)
				{
							$aDate = dol_getdate($datereg);
							$aResult = verifica_retraso($aMark,$aDate,$nprint,$tolerancia,$fk_member);
							if ($lDate)
							{
								print '<td>'.dol_print_date($datereg,'day').'</td>';
								$dataExport[$line]['date'] = $datereg;
								$lDate = false;
							}
							print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2).' '.($aResult[1]?$aResult[0].' '.$aResult[1]:'').'</td>';
							$dataExport[$line][$nprint]['marca'] = zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2);
							$dataExport[$line][$nprint]['resultado'] = $aResult[2];

					$nprint++;
 				}
				}
			}
			else
			{
				if (!$aLibre[$cont])
				{
					if ($nprint <=8)
					{
						if (!$aLicence[$datereg])
						{
							$aDate = dol_getdate($datereg);
							$aResult = verifica_retraso($aMark,$aDate,$nprint,$tolerancia,$fk_member);
							if ($lDate)
							{
								print '<td>'.dol_print_date($datereg,'day').'</td>';
								$dataExport[$line]['date'] = $datereg;
								$lDate = false;
							}
							print '<td bgColor="#'.$aColordef[$aResult[0]].'">'.zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2).' '.($aResult[1]?$aResult[0].' '.$aResult[1]:'').'</td>';
							$dataExport[$line][$nprint]['marca'] = zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2);
							$dataExport[$line][$nprint]['resultado'] = $aResult[2];
						}
					}
					if (!$aLicence[$datereg])
						$nprint++;
				}
			}
		}

	}
	if ($nprint < $nroMark)
	{
		$nCont = $nprint;
		for($c = $nCont; $c <= $nroMark; $c++)
		{
			print '<td>'.'</td>';
			$nprint++;
		}
	}
	if ($nprint <= 8)
	{
		for ($a = $nprint; $nprint <= 8; $a++)
		{
			print '<td>'.'</td>';
			$nprint++;
		}
	}
	$seq++;
	print '<td>';
	if ($lStatut)
	{
		$objAssistance->statut = $status;
		print $objAssistance->getLibStatut(3);
	}
	print '</td>';

	print '</tr>';
	$line++;
}

$aReport [$search_date] = $dataExport;
$_SESSION['aAssistance'] = serialize($aReport);


print '</table>';

print '</form>';

print "<div class=\"tabsAction\">\n";
//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?date='.$search_date.'&action=excel">'.$langs->trans("Spreadsheet").'</a>';
print '</div>';

//llxFooter();
//$db->close();
?>