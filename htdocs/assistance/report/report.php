<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       /typemarking_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-12 08:48
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
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/assistance/class/typemarkingext.class.php');
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/assistance/lib/utils.lib.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("assistance@assistance");

$_SESSION['aReport'] = array();

// Get parameters
$id			= GETPOST('id','int');
$fk_member  = GETPOST('fk_member','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$type_marking	= GETPOST('type_marking');
$dateini = dol_mktime('00','00','01',$_POST['dimonth'],$_POST['diday'],$_POST['diyear'],'user');
$aDateAnt = dol_get_prev_day($_POST['diday'],$_POST['dimonth'],$_POST['diyear']);
//creamos los rangos de fecha

$_SESSION['aReport']['dateini'] = $dateini;

$dateini = dol_mktime(23,59,59,$aDateAnt['month'],$aDateAnt['day'],$aDateAnt['year'],'user');
$datefin = dol_mktime(23,59,59,$_POST['dfmonth'],$_POST['dfday'],$_POST['dfyear'],'user');
$_SESSION['aReport']['datefin'] = $datefin;
$_SESSION['aReport']['fk_member'] = $fk_member;

//armamos el array de fechas del periodo
$aArraydate = array();
$lLoop = true;
$datei = $_SESSION['aReport']['dateini'];
while ($lLoop == true && $datei > 0)
{
	$aDate = dol_getdate($datei);
	$aArraydate[$datei] = $aDate;
	//siguiente dia
	$aDatei = dol_get_next_day($aDate['mday'], $aDate['mon'], $aDate['year']);
	$datei = dol_mktime(12, 0, 0, $aDatei['month'], $aDatei['day'], $aDatei['year']);
	if ($datei > $_SESSION['aReport']['datefin'])
		$lLoop = false;
}
// echo dol_print_date($dateini,'dayhour').' '.dol_print_date($datefin,'dayhour');
// echo $dateini.' '.$datefin;

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='create';

// Load object if id or ref is provided as parameter
$assistance    = new Assistance($db);
$object        = new Typemarkingext($db);
$assistancedef = new Assistancedef($db);
$adherentadd   = new Adherentext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('typemarking'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{

}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Reportassitance','');

$form=new Form($db);
$formadd=new Formadd($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to show a list
if ($action == 'rep')
{
	$aMark = array(1=>'primary',
		2=>'secundary',
		3=>'third',
		4=>'fourth',
		5=>'fifth',
		6=>'sixth');
	$aMarklang = array(1=>'Primary',
		2=>'Secundary',
		3=>'Third',
		4=>'Fourth',
		5=>'Fifth',
		6=>'Sixth');
	$aMarkvar = array(1=>array('1i','1e'),
		2=>array('2i','2e'),
		3=>array('3i','3e'),
		4=>array('4i','4e'),
		5=>array('5i','5e'),
		6=>array('6i','6e'),);
	$aMarktype = array('entry','exit');
	//verificamos si es unico o es total

	print '<div class="tabsAction">';
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/assistance/report.php">'.$langs->trans("Return").'</a>';
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/assistance/report_excel.php">'.$langs->trans("Excel").'</a>';
	print '</div>';
	// Put here content of your page
	print load_fiche_titre($langs->trans('Report'));

	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql .= " t.entity,";
	$sql .= " t.ref,";
	$sql .= " t.detail,";
	$sql .= " t.mark,";
	$sql .= " t.primary_entry,";
	$sql .= " t.primary_exit,";
	$sql .= " t.secundary_entry,";
	$sql .= " t.secundary_exit,";
	$sql .= " t.third_entry,";
	$sql .= " t.third_exit,";
	$sql .= " t.fourth_entry,";
	$sql .= " t.fourth_exit,";
	$sql .= " t.fifth_entry,";
	$sql .= " t.fifth_exit,";
	$sql .= " t.sixth_entry,";
	$sql .= " t.sixth_exit,";
	$sql .= " t.additional_time,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.date_create,";
	$sql .= " t.tms,";
	$sql .= " t.statut";

	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."type_marking as t";
	$sql.= " WHERE 1 = 1";

	if (!empty($type_marking))
		$sql.= " AND t.rowid = ".$type_marking;
	$sql.= " AND t.statut = 1";

	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;

	// Count total nb of records
	$nbtotalofrecords = 0;
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}

	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);

	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	$resqltit=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		if (! empty($moreforfilter))
		{
			print '<div class="liste_titre">';
			print $moreforfilter;
			$parameters=array();
			$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);
		// Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;
			print '</div>';
		}

		print '<table class="noborder centpercent">'."\n";

	// Fields title
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.date_ass','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Mark'),'','','',$param,'');
		$objtit = $db->fetch_object($resqltit);
		$mark = $objtit->mark/2;
		$nSeq = 1;
		for($x = 1; $x <= $mark; $x++)
		{
			$y=0;
			for ($y = 0; $y <=1; $y++)
			{
				$labeltext = $aMarklang[$x].$aMarktype[$y];
				print_liste_field_titre($langs->trans($labeltext),$_SERVER['PHP_SELF'],'t.'.$aMark[$x].$aMarktype[$y],'',$param,'',$sortfield,$sortorder);
				$_SESSION['aReport']['titlemark'][$nSeq] = $langs->trans($labeltext);
				$nSeq++;
			}
		}

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
	    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";

		$i = 0;
		$aReport =array();
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$mark = $obj->mark;
				$addtimedef = $obj->additional_time;
				//recuperamos a variables los tiempos de marcad
				$marks = $mark/2;
				$nId = 0;
				$pReg = array();
				for ($x=1;$x<=$marks;$x++)
				{
					$y=0;
					for ($y = 0; $y <=1; $y++)
					{
						$variable = $aMark[$x].'_'.$aMarktype[$y];
						$aArr = dol_getdate($db->jdate($obj->$variable));
						if (empty($nId)) $nId = $x;
						else $nId = $nId+1;
						$pReg[$nId] = $aArr['hours']*60 + $aArr['minutes'];
					}

				}
				print '<tr>';
				print '<td colspan="5">'.$obj->ref.' '.$langs->trans('Mark').' '.$obj->mark.'</td>';
				print '</tr>';
				$_SESSION['aReport']['title'] = $obj->ref.' '.$langs->trans('Mark').' '.$obj->mark;

				//buscamos a los usuarios que tiene marcaciones para este tipo
				$filter = array(1=>1);
				$filterstatic = " AND t.type_marking = '".$obj->ref."'";
				$filterstatic.= " AND t.statut = 1";
				$assistancedef->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
				$aMember = array();
				$aContact = array();
				$aMemberaddtime = array();
				$aContactaddtime = array();
				foreach((array) $assistancedef->lines AS $j => $objdef)
				{
					if ($objdef->type_reg == 'm')
					{
						if ($fk_member>0)
						{
							if ($fk_member == $objdef->fk_reg)
								$aMember[$objdef->fk_reg] = $objdef->fk_reg;
						}
						else
							$aMember[$objdef->fk_reg] = $objdef->fk_reg;
						$aMemberaddtime[$objdef->fk_reg] = $objdef->aditional_time;
					}
					if ($objdef->type_reg == 'c')
					{
						$aContact[$objdef->fk_reg] = $objdef->fk_reg;
						$aContactaddtime[$objdef->fk_reg] = $objdef->aditional_time;
					}
				}
				//print_r($aMember);
				if (count($aMember)>0)
				{
					$filters = implode(',',$aMember);
					$filter = array(1=>1);
					$filterstatic = " AND d.rowid IN (".$filters.")";
					//recorremos todos los miembros
					$adherentadd->fetchAll('ASC','lastname,firstname',0,0,$filter,'AND',$filterstatic);
				}
				foreach((array) $adherentadd->lines AS $k => $lines)
				{
					//vemos que marcaciones tiene
					// $assistance->fetchAll('ASC','date_ass',0,0,array('fk_member'=>$lines->id),' AND t.date_ass >= '.$dateini );
					$filter = array(1=>1);
					$filterstatic = ' AND UNIX_TIMESTAMP(t.date_ass) BETWEEN '.$dateini.' AND '.$datefin;
					$filterstatic.= ' AND fk_member = '.$lines->id;
					$assistance->fetchAll('ASC','date_ass',0,0,$filter,'AND',$filterstatic);
					$aNewdate = array();
					$aNewCount = array();
					$nMark = count($assistance->lines);
					$nCount = 0;
					//variable para definir numero de marcados
					$nNumMark = 1;
					$dateant = 0;
					foreach((array) $assistance->lines AS $l => $line)
					{
						//recuperamos la hora de marcado
						if ($line->date_ass >= $dateini)
						{
							$aDate = dol_getdate($line->date_ass);
							//echo '<hr>'.$line->date_ass.' '.$datefin;
							//echo '<hr>'.dol_print_date($line->date_ass,'dayhour').' 	'.dol_print_date($dateini,'dayhour');
							if ($line->date_ass <= $datefin)
							{
								$nCount++;
								$aDate = dol_getdate($line->date_ass);
								$date = dol_mktime(12,0,0,$aDate['mon'],$aDate['mday'],$aDate['year'],'user');
								$timereg = zerofill($aDate['hours'],2).':'.zerofill($aDate['minutes'],2);
								//$aNewdate[$date][$line->marking_number]['timereg']=$timereg;
								//$aNewdate[$date][$line->marking_number]['aDate']=$aDate;
								if ($dateant != $date)
								{
									$dateant = $date;
									$nNumMark = 1;
								}
								$aNewdate[$date][$nNumMark]['timereg']=$timereg;
								$aNewdate[$date][$nNumMark]['aDate']=$aDate;
								$aNewCount[$date]['count']++;
								$nNumMark++;
							}
						}
					}

					foreach((array) $aArraydate AS $date => $aADate)

					//foreach((array) $aNewdate AS $date => $aData)
					{
						$aData = $aNewdate[$date];
						$nCount = $aNewCount[$date]['count'];
						//recuperamos la hora de marcado
						$aDate = dol_getdate($date);
						//$db->idate($line->date_ass).' '.$line->date_ass;
						if ($date <= $datefin)
						{
							//definimos que addtime se utilizara
							if(!is_null($aMemberaddtime[$lines->id]))
								$addtime = $aMemberaddtime[$lines->id];
							else
								$addtime = $addtimedef;
							print '<tr>';
							print '<td>'.$lines->lastname.' '.$lines->firstname.'</td>';
							print '<td>'.$langs->trans($aADate['weekday']).' '.dol_print_date($date,'day').'</td>';
							print '<td align="center"'.(($nCount>$obj->mark)?'class="errormark"':'').'>';
							if ($nCount>$obj->mark)
								print '<a href="'.DOL_URL_ROOT.'/assistance/assistance.php?search_name='.$lines->login.'&setdate=1&search_date='.$date.'" class="classfortooltip" title="'.$langs->trans('Revise, <br>tiene mas marcaciones que el requerido').'">';
							print $nCount;
							if ($nCount>$obj->mark)
								print '</a>';
							print '</td>';
							$aReport[$i][$lines->id][$date]['name'] = $lines->lastname.' '.$lines->firstname;
							$aReport[$i][$lines->id][$date]['date'] = dol_print_date($date,'day');
							$aReport[$i][$lines->id][$date]['mark'] = $nCount;
							$aReport[$i][$lines->id][$date]['wday'] = $aADate['wday'];
							$aReport[$i][$lines->id][$date]['weekday'] = $langs->trans($aADate['weekday']);

							for ($a=1; $a <=$mark; $a++)
							{
								$lPrint = true;
								$aDate = $aData[$a]['aDate'];
								if ($aADate['wday'] == 0 || $aADate['wday'] == 6)
								{
									//es sabado o domingo
									if (empty($aDate))
										$lPrint = false;
								}
								$timereg = $aDate['hours'].':'.$aDate['minutes'];
								$timeregnumber = $aDate['hours']*60 +$aDate['minutes'];
								$timeretraso = '';
								$timeanticipo = '';
								$nTimeretraso = 0;
								$nTimeanticipo = 0;
								//echo '<hr>'.$pReg[$a].'+'.$addtime.' < '.$timeregnumber;
								if (($a==1 || $a==3 || $a==5 || $a==7 || $a==9 || $a==11) && $pReg[$a]+$addtime < $timeregnumber)
								{
									$timeretraso = $timeregnumber - $pReg[$a];
									$nTimeretraso = $timeretraso;
									if ($timeretraso >60)
									{
										$resto = $timeretraso % 60;
										if (strlen($resto) == 1)
											$resto = '0'.$resto;
										$entero = floor($timeretraso/60);
										if (strlen($entero) == 1)
											$entero = '0'.$entero;
										$timeretraso = $entero.':'.$resto;

									}
									else
									{
										if (strlen($timeretraso) == 1)
											$timeretraso = '0'.$timeretraso;
										$timeretraso = '00:'.$timeretraso;
									}
								}
								if (($a==2 || $a==4 || $a==6 || $a==8 || $a==10 || $a==12 ) && $pReg[$a]+$addtime > $timeregnumber)
								{
									$timeanticipo = $pReg[$a] - $timeregnumber;
									$nTimeanticipo = $timeanticipo;
									if ($timeanticipo >= 60)
									{
										$resto = $timeanticipo % 60;
										if (strlen($resto) == 1)
											$resto = '0'.$resto;
										$entero = floor($timeanticipo/60);
										if (strlen($entero) == 1)
											$entero = '0'.$entero;

										$timeanticipo = $entero.':'.$resto;
									}
									else
									{
										if (strlen($timeanticipo)==1)
											$timeanticipo = '0'.$timeanticipo;
										$timeanticipo = '00:'.$timeanticipo;
									}
								}
								if ($lPrint)
									print '<td>'.(empty($aData[$a]['timereg'])?$langs->trans('Unregistered'):$aData[$a]['timereg'].' '.(!empty($timeretraso)?'<spam style="color:#ff0000;"> - R '.$timeretraso.'</spam>':(!empty($timeanticipo)?($nTimeanticipo>0?'<spam style="color:#0000FF;"> - A '.$timeanticipo.'</spam>':''):''))).'</td>';
								else
									print '<td>'.$langs->trans('No laboral').'</td>';
								//$aReport[$i][$lines->id]['reg'][$a]['time'] = (empty($aData[$a]['timereg'])?$langs->trans('Unregistered'):$aData[$a]['timereg'].' '.(!empty($timeretraso)?'<spam style="color:#ff0000;"> - R '.$timeretraso.'</spam>':(!empty($timeanticipo)?'<spam style="color:#0000FF;"> - A '.$timeanticipo.'</spam>':'')));
								$aReport[$i][$lines->id][$date]['reg'][$a]['time'] = (empty($aData[$a]['timereg'])?'':$aData[$a]['timereg']);
								$aReport[$i][$lines->id][$date]['reg'][$a]['retr'] = (empty($aData[$a]['timereg'])?'':$nTimeretraso);
								$aReport[$i][$lines->id][$date]['reg'][$a]['anti'] = (empty($aData[$a]['timereg'])?'':$nTimeanticipo);
							}
						}
					}
					print '</tr>';
				}


				$parameters=array('obj' => $obj);
				$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;
				print '</tr>';
			}
			$i++;
		}

		$db->free($resql);

		$parameters=array('sql' => $sql);
		$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print "</table>\n";
		print "</form>\n";
		$_SESSION['aReport']['aReport'] = $aReport;
		print '<div class="tabsAction">';
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/assistance/report.php">'.$langs->trans("Return").'</a>';
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/assistance/report_excel.php">'.$langs->trans("Excel").'</a>';
		print '</div>';
	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}



// Part to create
if ($action == 'create')
{
	print_fiche_titre($langs->trans("Newreport"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="rep">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	//dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Typemarking").'</td><td>';
	print $object->select_typemarking($type_marking,'type_marking','',40,1,0,'required','',$code='rowid',$campo='detail');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Member").'</td><td>';
	print $formadd->select_member($fk_member,'fk_member','',1,'','','','','');
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Dateini").'</td><td>';
	print $form->select_date((empty($dateini)?dol_now():$dateini),'di',0,0,1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Dateend").'</td><td>';
	print $form->select_date((empty($datefin)?dol_now():$datefin),'df',0,0,1);
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

	dol_fiche_head();

	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"></div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
	dol_fiche_head();



	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assistance->rep)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
