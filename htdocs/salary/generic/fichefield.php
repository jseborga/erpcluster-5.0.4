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
 *	\file       htdocs/salary/generic/fichefield.php
 *	\ingroup    generic field
 *	\brief      Page fiche salary generic field
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictableext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfieldext.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");

$action=GETPOST('action');
$sequen=GETPOST('sequen');

$id        = GETPOST("id");
if ($action == "select")
{
	if (isset($_POST['table_cod']))
	{
		$table_cod = GETPOST("table_cod");
		$_SESSION['table_cod'] = $table_cod;
	}
	else
	{
		$table_cod = $_SESSION['table_cod'];
	}
	$action = "create";
}
$table_cod = $_SESSION['table_cod'];

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object  = new Pgenerictableext($db);
$objectf = new Pgenericfieldext($db);

/*
 * Actions
 */

// Add
$seque = 0;
if ($action == 'add' && $user->rights->salary->generic->creer)
{
	$array_generic_table = $_POST["fk_generic_table"];
	$j = 0;
	//buscando la secuencia mas alta
	foreach ((array) $array_generic_table AS $i => $campo)
	{
		$object->fetch($i);

		$resultj = $objectf->fetch_sequen_max($object->ref,true);
		if ($objectf->sequen >=$seque)
			$seque = $objectf->sequen;
	}
	If (empty($seque)) $seque = 1;
	$j = $seque;

	foreach($array_generic_table AS $i => $value)
	{
		$object->fetch($i);
		if (!empty($value))
		{
			$objectf->generic_table_ref = $object->ref;
			$objectf->field_value = $value;
			$objectf->sequen = $j;
			$objectf->create($user);
		}
	}
	header("Location: fichefield.php?action=create");
	exit;
}


// Delete concept
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->generic->del)
{
	$idr = GETPOST('idr');
	$res = $objectf->fetch($idr);
	if ($res > 0)
	{
		if ($objectf->id == $idr)
			$objectf->delete($objectf->id);

	}
	// $sequen = GETPOST('sequen');
	// $table_cod = GETPOST('table_cod');
	// $aArray = $object->array_table($table_cod);
	// foreach ((array) $aArray AS $i => $campo)
	//   {
	// 	$result = $objectf->fetch_line($i,$sequen);
	// 	if ($objectf->fk_generic_table == $i && $objectf->sequen == $sequen)
	// 	  $objectf->delete($objectf->id);
	//   }
	header("Location: fichefield.php?action=create");
	exit;
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$error=0;
	$sequen = GETPOST('sequen');
	$array_rowid = $_POST["rowid"];
	$array_generic_table = $_POST["fk_generic_table"];
	$db->begin();
	foreach($array_rowid AS $i => $fk_generic_table )
	{
		//buscando en field
		$object->fetch($fk_generic_table);
		if ($i == 0)
		{
			//registro nuevo
			if (!empty($array_generic_table[$fk_generic_table]))
			{
				$objectf->generic_table_ref = $object->ref;
				$objectf->field_value = $array_generic_table[$fk_generic_table];
				$objectf->sequen = $sequen;
				$res = $objectf->create($user);
				if ($res <= 0) $error++;
			}
		}
		else
		{
			$resf = $objectf->fetch($i);
			if ($resf>0)
			{
				if($objectf->id == $i)
				{
					$objectf->field_value = $array_generic_table[$fk_generic_table];
					$objectf->sequen = $sequen;
					$res = $objectf->update($user);
					if ($res <= 0) $error++;
				}
			}
		}
	}
	if (empty($error)) $db->commit();
	else
	{
		 $db->rollback();
	}
	header("Location: fichefield.php?action=create");
	exit;
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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

print "<form action=\"fichefield.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="select">';

print '<table class="border" width="100%">';
print '<tr><td class="fieldrequired">'.$langs->trans('Table').'</td><td colspan="2">';
print $object->select_generic_table($table_cod,'table_cod');
print '</td>';
print '<td>';
print '<center><input type="submit" class="button" value="'.$langs->trans("Select").'"></center>';
print '</td>';
print '</tr>';
print '</table>';

print '</form>';
print '<br/>';
if (!empty($table_cod))
{
	$aArray = $object->array_table($table_cod);
	print "<form action=\"fichefield.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	if ($action == 'create')
		print '<input type="hidden" name="action" value="add">';
	if ($action == 'edit')
	{
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="sequen" value="'.$sequen.'">';
	}
	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';
	print "<tr class=\"liste_titre\">";

	foreach ((array) $aArray AS $i => $campo)
	{
		print_liste_field_titre($langs->trans($campo),"", "","","","");
	}
	print_liste_field_titre($langs->trans('Action'),"", "","","","");

	print '</tr>';

	if ($action == 'create' && $user->rights->salary->generic->creer)
	{

	// new registered
		print '<tr>';

		foreach ((array) $aArray AS $i => $campo)
		{
			$campo = "fk_generic_table[".$i."]";
			print '<td>';
			print '<input id="'.$campo.'" type="text" value="" name="'.$campo.'" size="7" maxlength="40">';
			print '</td>';
		}
		print '<td>';
		print '<center><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
		print '</td>';
		print '</tr>';
	}

	if ($action == 'edit' && $user->rights->salary->generic->creer)
	{
	//valores a buscar
		$sequen = GETPOST('sequen');
	// edicion
		print '<tr>';
		foreach ((array) $aArray AS $i => $campo)
		{
		//buscampos primero el ref de object
			$objectCop = new Pgenerictable($db);
			$objectCop->fetch($i);
		// $result = $objectf->fetch_line($objectCop->ref,$x);

			$campo = "fk_generic_table[".$i."]";
		//buscando
			$sql = "SELECT rowid, field_value FROM ".MAIN_DB_PREFIX."p_generic_field ";
			$sql.= " WHERE generic_table_ref = '".$objectCop->ref."'";
			$sql.= " AND sequen = ".$sequen;
			$result = $db->query($sql);
			$campoValue = "";
			if ($result)
			{
				$num = $db->num_rows($result);
				$obj = $db->fetch_object($result);
				$campoValue = $obj->field_value;
				$campoRowid = $obj->rowid+0;
			}

			print '<td>';
			print '<input  type="hidden" value="'.$i.'" name="rowid['.$campoRowid.']" size="7" maxlength="40">';
			print '<input id="'.$campo.'" type="text" value="'.$campoValue.'" name="'.$campo.'" size="7" maxlength="40">';
			print '</td>';
		}
		print '<td>';
		print '<center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';
		print '</td>';
		print '</tr>';
	}

   // registered
	$seque = 0;
	// Confirm delete third party
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?idr=".GETPOST('idr')."&sequen=".$sequen.'&table_cod='.$table_cod,$langs->trans("Deletefieldgeneric"),$langs->trans("Confirmdeletefieldgeneric Secuencia: ".$sequen.' Cod: '.$table_cod),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	//buscando la secuencia mas alta
	foreach ((array) $aArray AS $i => $campo)
	{
	//buscampos primero el ref de object
		$objectCop = new Pgenerictable($db);
		$objectCop->fetch($i);
		$resultj = $objectf->fetch_sequen_max($objectCop->ref,False);
		if ($objectf->sequen >= $seque)
			$seque = $objectf->sequen;
	}
	if ($seque > 0)
	{
		for ($x = 1; $x <= $seque; $x++)
		{
			if ($x != $sequen)
			{
				print '<tr>';
				foreach ((array) $aArray AS $i => $campo)
				{
			//buscampos primero el ref de object
					$objectCop = new Pgenerictable($db);
					$objectCop->fetch($i);
					$result = $objectf->fetch_line($objectCop->ref,$x);
					print '<td>';
					if ($objectf->generic_table_ref == $objectCop->ref && $objectf->sequen == $x)
						print $objectf->field_value;
					else
						print "&nbsp;";
					print '</td>';
				}
				print '<td>';
				print '<center><a href="fichefield.php?action=edit&sequen='.$x.'&table_cod='.$table_cod.'">'.img_picto($langs->trans("Modify"),'edit','').'</a>';
				print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				print '<a href="fichefield.php?action=delete&idr='.$objectf->id.'&sequen='.$x.'&table_cod='.$table_cod.'">'.img_picto($langs->trans("Delete"),'delete','').'</a></center>';

				print '</td>';
				print '</tr>';
			}
		}
	}
	print '</table>';
	print '</form>';
	// if ($_GET["id"])
	//    {
	//   dol_htmloutput_mesg($mesg);

	//   $result = $object->fetch($_GET["id"]);
	//   if ($result < 0)
	// 	{
	// 	  dol_print_error($db);
	// 	}


	//   /*
	//    * Affichage fiche
	//    */
	//   if ($action <> 'edit' && $action <> 're-edit')
	// 	{
	// 	  //$head = fabrication_prepare_head($object);

	// 	  dol_fiche_head($head, 'card', $langs->trans("Charge"), 0, 'salary');

	// 	  /*
	// 	   * Confirmation de la validation
	// 	   */
	// 	  if ($action == 'validate')
	// 	    {
	// 	      $object->fetch(GETPOST('id'));
	// 	      //cambiando a validado
	// 	      $object->state = 1;
	// 	      //update
	// 	      $object->update($user);
	// 	      $action = '';
	// 	      //header("Location: fiche.php?id=".$_GET['id']);

	// 	    }

	// 	  // Confirm delete third party
	// 	  if ($action == 'delete')
	// 	    {
	// 	      $form = new Form($db);
	// 	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	// 	      if ($ret == 'html') print '<br>';
	// 	    }

	// 	  print '<table class="border" width="100%">';

	// 	  // table cod
	// 	  print '<tr><td>'.$langs->trans('Tablecod').'</td><td colspan="2">';
	// 	  print $object->table_cod;
	// 	  print '</td></tr>';
	// 	  // table name
	// 	  print '<tr><td>'.$langs->trans('Tablename').'</td><td colspan="2">';
	// 	  print $object->table_name;
	// 	  print '</td></tr>';

	// 	  // field name
	// 	  print '<tr><td>'.$langs->trans('Fieldname').'</td><td colspan="2">';
	// 	  print $object->field_name;
	// 	  print '</td></tr>';

	// 	  // sequen
	// 	  print '<tr><td>'.$langs->trans('Sequen').'</td><td colspan="2">';
	// 	  print $object->sequen;
	// 	  print '</td></tr>';

	// 	  print "</table>";

	// 	  print '</div>';


	// 	  /* ************************************************************************** */
	// 	  /*                                                                            */
	// 	  /* Barre d'action                                                             */
	// 	  /*                                                                            */
	// 	  /* ************************************************************************** */

	// 	  print "<div class=\"tabsAction\">\n";

	// 	  if ($action == '')
	// 	    {
	// 	      if ($user->rights->salary->creargeneric)
	// 		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	// 	      else
	// 		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	// 	      if ($user->rights->salary->valgeneric)
	// 		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";

	// 	      if ($user->rights->salary->delgeneric)
	// 		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	// 	      else
	// 		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	// 	    }
	// 	  print "</div>";
	// 	}


	//   /*
	//    * Edition fiche
	//    */
	//   if (($action == 'edit' || $action == 're-edit') && 1)
	// 	{
	// 	  print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

	// 	  print '<form action="fiche.php" method="POST">';
	// 	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	// 	  print '<input type="hidden" name="action" value="update">';
	// 	  print '<input type="hidden" name="id" value="'.$object->id.'">';

	// 	  print '<table class="border" width="100%">';


	// 	  // table cod
	// 	  print '<tr><td class="fieldrequired">'.$langs->trans('Tablecod').'</td><td colspan="2">';
	// 	  print '<input id="table_cod" type="text" value="'.$object->table_cod.'" name="table_cod" size="3" maxlength="3">';
	// 	  print '</td></tr>';
	// 	  // table name
	// 	  print '<tr><td class="fieldrequired">'.$langs->trans('Tablename').'</td><td colspan="2">';
	// 	  print '<input id="table_name" type="text" value="'.$object->table_name.'" name="table_name" size="30" maxlength="40">';
	// 	  print '</td></tr>';

	// 	  // field name
	// 	  print '<tr><td class="fieldrequired">'.$langs->trans('Fieldname').'</td><td colspan="2">';
	// 	  print '<input id="field_name" type="text" value="'.$object->field_name.'" name="field_name" size="30" maxlength="20">';
	// 	  print '</td></tr>';

	// 	  // sequen
	// 	  print '<tr><td class="fieldrequired">'.$langs->trans('Sequen').'</td><td colspan="2">';
	// 	  print '<input id="sequen" type="text" value="'.$object->sequen.'" name="sequen" size="2" maxlength="2">';
	// 	  print '</td></tr>';

	// 	  print '</table>';

	// 	  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	// 	  print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	// 	  print '</form>';

	// 	}
}


llxFooter();

$db->close();
?>
