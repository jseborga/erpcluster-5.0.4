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
 *      \file       htdocs/mant/charge/liste.php
 *      \ingroup    Mantenimeinto cargos
 *      \brief      Page liste des charges
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/pac/class/poapac.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidapre.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");

if ($conf->poai->enabled)
  {
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
    require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
  }

$langs->load("poa@poa");

if (!$user->rights->poa->pac->leer)
  accessforbidden();

$object = new Poapac($db);
$objuser = new User($db);
$objprev = new Poaprev($db);
$objpartidapre = new Poapartidapre($db);
$objpoa = new Poapoa($db);

if ($conf->poai->enabled)
  {
    $objinst = new Poaiinstruction($db);
    $objmoni = new Poaimonitoring($db);
  }

//asignando filtro de usuario
assign_filter_user('csearch_user');

$id = GETPOST('id');
$idp = GETPOST('idp');
if (isset($_GET['nopoa']))
  $_SESSION['idPoa'] = '';
if (isset($_GET['idp']))
  $_SESSION['idPoa'] = $_GET['idp'];
$idp = $_SESSION['idPoa'];
if (empty($_SESSION['gestion']))
  $_SESSION['gestion'] = date('Y');
$gestion = $_SESSION['gestion'];

//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];
$action = GETPOST('action');

if (isset($_POST['search_modality']))
  $_SESSION['csearch_modality'] = $_POST['search_modality'];
if (isset($_POST['search_gestion']))
  $_SESSION['csearch_gestion'] = $_POST['search_gestion'];
if (isset($_POST['search_ref']))
  $_SESSION['csearch_ref'] = $_POST['search_ref'];
if (isset($_POST['search_nom']))
  $_SESSION['csearch_nom'] = $_POST['search_nom'];
if (isset($_POST['search_object']))
  $_SESSION['csearch_object'] = $_POST['search_object'];
if (isset($_POST['search_financer']))
  $_SESSION['csearch_financer'] = $_POST['search_financer'];
if (isset($_POST['search_partida']))
  $_SESSION['csearch_partida'] = $_POST['search_partida'];
if (isset($_POST['search_init']))
  $_SESSION['csearch_init'] = $_POST['search_init'];
if (isset($_POST['search_public']))
  $_SESSION['csearch_public'] = $_POST['search_public'];
if (isset($_POST['search_user']))
  $_SESSION['csearch_user'] = STRTOUPPER($_POST['search_user']);


if (isset($_POST['nosearch_x']))
  {
    $_SESSION['csearch_modality']='';
    $_SESSION['csearch_gestion'] = $_SESSION['gestion'];
    $_SESSION['csearch_ref']='';
    $_SESSION['csearch_nom']='';
    $_SESSION['csearch_object']='';
    $_SESSION['csearch_financer']='';
    $_SESSION['csearch_partida']='';
    $_SESSION['csearch_init']='';
    $_SESSION['csearch_public']='';
    $_SESSION['csearch_user']='';
  }

$search_modality  = $_SESSION['csearch_modality'];
$search_gestion   = $_SESSION['csearch_gestion'];
$search_ref       = $_SESSION['csearch_ref'];
$search_nom       = $_SESSION['csearch_nom'];
$search_object    = $_SESSION['csearch_object'];
$search_financer  = $_SESSION['csearch_financer'];
$search_partida   = $_SESSION['csearch_partida'];
$search_init      = $_SESSION['csearch_init'];
$search_public    = $_SESSION['csearch_public'];
$search_user      = $_SESSION['csearch_user'];

if ($search_init <0) $search_init = '';
if ($search_public <0) $search_public = '';


$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.fk_type_modality";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($filter))
  $filter = -1;
$sql  = "SELECT p.rowid AS id, p.gestion, p.ref, p.nom, p.statut, p.fk_type_modality, ";
$sql.= " p.fk_poa, p.fk_type_object, p.fk_financer, p.month_init, p.month_public, p.partida, p.amount, ";
$sql.= " p.fk_user_resp, ";
$sql.= " o.fk_structure, o.rowid AS poaid, o.label AS namepoa, ";
$sql.= " t.label AS modality, ";
$sql.= " f.label AS financer, ";
$sql.= " s.sigla, ";
$sql.= " t1.label AS object,";
$sql.= " u.login, u.firstname, u.lastname ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_pac as p ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."poa_poa AS o ON p.fk_poa = o.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON o.fk_structure = s.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."c_tables AS t ON p.fk_type_modality = t.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."c_poa_financer AS f ON p.fk_financer = f.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."c_tables AS t1 ON p.fk_type_object = t1.rowid ";
//user
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user AS u ON u.rowid = p.fk_user_resp ";

$sql.= " WHERE p.entity = ".$conf->entity;

if ($idp)
  $sql.= " AND p.fk_poa = ".$idp;

if ($idsArea)
  $sql.= " AND s.fk_area IN ($idsArea)";

if ($_SESSION['sel_area'])
  $sql.= " AND s.fk_area = ".$_SESSION['sel_area'];

$sql.= " AND p.gestion = ".$gestion;

if ($search_modality)   $sql .= " AND t.label LIKE '%".$db->escape($search_modality)."%'";
if ($search_gestion)   $sql .= " AND p.gestion = ".$search_gestion;
if ($search_ref)   $sql .= " AND p.ref LIKE '%".$db->escape($search_ref)."%'";
if ($search_nom)   $sql .= " AND p.nom LIKE '%".$db->escape($search_nom)."%'";
if ($search_object)   $sql .= " AND t1.label LIKE '%".$db->escape($search_object)."%'";
if ($search_financer)   $sql .= " AND f.label LIKE '%".$db->escape($search_financer)."%'";
if ($search_partida)   $sql .= " AND p.partida LIKE '%".$db->escape($search_partida)."%'";
if ($search_init)   $sql .= " AND p.month_init = ".$search_init;
if ($search_public)   $sql .= " AND p.month_public = ".$search_public;

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    // $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    // llxHeader("",$langs->trans("ListePAC"),$help_url);

    $aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
    $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste PAC"),$help_url,'','','',$aArrjs,$aArrcss);
    
    print_barre_liste($langs->trans("ListePAC"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

    //filtro
    print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">'."\n";
    print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Modality"),"liste.php", "t.label","","","");
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","");
    print_liste_field_titre($langs->trans("Name"),"liste.php", "p.nom","","","");
    print_liste_field_titre($langs->trans("Typeobject"),"liste.php", "p.fk_type_object","","","");
    print_liste_field_titre($langs->trans("EDT"),"liste.php", "s.sigla","","","");
    print_liste_field_titre($langs->trans("Partida"),"liste.php", "p.partida","","","");
    
    print_liste_field_titre($langs->trans("Monthinit"),"liste.php", "p.month_init","","","");
    print_liste_field_titre($langs->trans("Monthpublic"),"liste.php", "p.month_public","","","");
    print_liste_field_titre($langs->trans("Amount"),"liste.php", "p.amount","","","");
    print_liste_field_titre($langs->trans("Amountpreventive"),"", "","","","");
    print_liste_field_titre($langs->trans("User"),"", "","","","");
    print_liste_field_titre($langs->trans("Hito"),"", "","","","");
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut","","","");
    print_liste_field_titre($langs->trans("Action"),"", "","","","");
    print "</tr>\n";
   
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    
    print '</tr>';
    
    print '<tr class="liste_titre">';    
    print '<td class="liste_titre"><input type="text" class="flat" name="search_modality" value="'.$search_modality.'" size="17"></td>';
    print '<td><input type="text" class="flat" name="search_ref" width="3%" value="'.$search_ref.'" size="2"></td>';
    print '<td class="liste_titre"><input type="text" class="flat" name="search_nom" value="'.$search_nom.'" ></td>';   
    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="10" name="search_object" value="'.$search_object.'" size="25">';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="8" name="search_sigla" value="'.$search_sigla.'">';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="10" name="search_partida" value="'.$search_partida.'">';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print select_month($search_init,'search_init','',15,1,0);
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print select_month($search_public,'search_public','',15,1,0);
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="10" name="search_amount" value="'.$search_amount.'">';
    print '</td>';

    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';

    print '<td align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="4" name="search_user" value="'.$search_user.'">';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td align="left" class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    
    print '<td class="liste_titre" align="right" nowrap>';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '&nbsp;';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
    print '</td>';
    
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    
    print '</tr>';

    if ($num)
      {
	$var=True;
	while ($i < min($num,$limit))
	  {
	    $obj = $db->fetch_object($result);
	    
	    //usuario creador
	    $newNombre = $obj->login;
	    $nombre = $obj->lastname.' '.$obj->firstname;
	    // $newNombre = '';
	    // $nombre = '';
	    // if ($objuser->fetch($obj->fk_user_resp))
	    //   {
	    // 	$nombre = $objuser->firstname.' '.$objuser->lastname;
	    // 	$aNombre = explode(' ',$nombre);
	    // 	foreach($aNombre AS $k => $value)
	    // 	  {
	    // 	    $newNombre .= substr($value,0,1);
	    // 	  }
	    //   }
	    $lContinue = true;
	    $textNombre = $newNombre.' '.$nombre;
	    $pregmatch = preg_match("/".$search_user."/i",STRTOUPPER($textNombre));

	    if (!empty($search_user) && $pregmatch)
	      //if (!empty(STRTOUPPER($search_user)) && $search_user != $newNombre)
	      $lContinue = false;

	    if ($lContinue)
	      {
	      
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td>'.$obj->modality.'</td>';
		print '<td><a href="fiche.php?dol_hide_leftmenu=1&id='.$obj->id.'">'.$obj->ref.'</a></td>';
		
		print '<td>'.$obj->nom.'</td>';
		print '<td>'.select_tables($obj->fk_type_object,'fk_type_object','',0,1,'06').'</td>';
		print '<td>'.$obj->sigla.'</td>';
		print '<td>'.$obj->partida.'</td>';
		print '<td>';
		print select_month($obj->month_init,'mes','',0,0,1);
		print '</td>';
		print '<td>';
		print select_month($obj->month_public,'mes','',0,0,1);
		print '</td>';
		print '<td align="right">';
		print number_format(price2num($obj->amount,'MT'),2);
		print '</td>';

		print '<td align="right">';
		if ($objprev->fetch_pac($obj->id)>0)
		  {
		    //buscamos el total del preventivo
		    if ($objpartidapre->getsum_pac_str_part($obj->gestion,$obj->id,$obj->fk_structure,$obj->fk_poa,$obj->partida)>0)
		      print number_format(price2num($objpartidapre->total,'MT'),2);
		    else
		      print price(price2num(0,'MT'));
		  }
		else
		  print number_format(price2num(0,'MT'),2);
		  
		print '</td>';

		print '<td>';
		print '<a href="#" title="'.$nombre.'">'.$newNombre.'</a>';
		print '</td>';
		//instruction
		//instruction
		//buscamos la ultima instruccion si existe para el poa seleccionado
		$addClase = ''; 
		$addMessage = '';
		if ($conf->poai->enabled)
		  {
		    $objinst->fetch_pac($obj->id);
		    //if ($objinst->fk_poa_poa == $obj->id)
		    if ($objinst->fk_id == $obj->id)
		      {
			$idInst = $objinst->id;
			$newClaseor = $newClase;
			$detail = $objinst->detail;		      
			//verificamos si tiene monitoreo por revisar
			if ($objmoni->fetch_ult($obj->id,'PAC'))
			  {
			    if ($objmoni->fk_id == $obj->id)
			      {
				$idInst = $objmoni->fk_poai_instruction;
				$addMessage = '&#13;'.$langs->trans('Monitoring').': '.$objmoni->detail;
				if ($lStyle)
				  $newClase.= ' background:#12e539;';
				else
				  $newClase.= '" style="background:#12e539;';
			      }
			  }
			print '<td nowrap class="'.$newClase.'">';
			print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&backtopage=1&typeinst=PAC&id='.$idInst.'" title="'.trim($detail).': '.$langs->trans('Commitment date').' '.dol_print_date($objinst->commitment_date,'day').$addMessage.'">'.img_picto($langs->trans('Edit'),'next').' '.(strlen($detail)>11?substr($detail,0,5).'.':$detail).'</a>';
			print '</td>';
			$newClase = $newClaseor;
		      }
		    else
		      {
			print '<td nowrap>';
			if ($user->rights->poai->inst->crear)
			  print '<a href="'.DOL_URL_ROOT.'/poai/instruction/fiche.php?idp='.$obj->id.'&fk_user='.$obj->fk_user_resp.'&action=create&typeinst=PAC'.'&dol_hide_leftmenu=1'.'&backtopage=1">'.img_picto($langs->trans('Newinstruction'),'next').'</a>';
			else
			  print '&nbsp;';
			print '</td>';
		      }
		  }
		
		print '<td nowrap>'.$object->LibStatut($obj->statut).'</td>';
		print '<td align="right">';
		print '<a href="'.DOL_URL_ROOT.'/poa/execution/liste.php?idpa='.$obj->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Preventive"),DOL_URL_ROOT.'/poa/img/prev','',1).'</a>';
		print '</td>';
		
		print "</tr>\n";
	      }
	    $i++;
	  }
      }

    print "</table>";
    print '</form>';
    $db->free($result);
    
    print "<div class=\"tabsAction\">\n";
    print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
    
    if ($action == '')
      {
	if ($user->rights->poa->area->crear)
	  print "<a class=\"butAction\" href=\"fiche.php?action=create&dol_hide_leftmenu=1\">".$langs->trans("Createnew")."</a>";
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	
	if ($idp)
	  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php">'.$langs->trans("Return").'</a>';
      }
    print '</div>';
  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
