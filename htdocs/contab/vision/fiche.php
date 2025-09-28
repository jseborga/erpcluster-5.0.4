<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       dev/Contabvisions/Contabvision_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2013-12-13 01:02
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');	// If there is no menu to show
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');	// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');		// If this page is public (can be called outside logged session)

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
dol_include_once('/contab/class/contabvisionext.class.php');
dol_include_once('/contab/lib/contab.lib.php');
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");

//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("companies");
$langs->load("other");

// Get parameters
$id		= GETPOST('id','int');
$idr		= GETPOST('idr','int');
$ref		= GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$cancel	= GETPOST('cancel','alpha');
$confirm	= GETPOST('confirm','alpha');
$myparam	= GETPOST('myparam','alpha');
$cta_identifier = '';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
if (!$user->rights->contab->vision->read)
	accessforbidden();

$object=new Contabvisionext($db);
$objtmp=new Contabvisionext($db);
$objAccounting = new AccountingAccountext($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$now = dol_now();

if ($action == 'validate' && $id>0)
{
	$res = $object->fetch($id);
	if ($res==1)
	{
					//cambiando a validado
		$object->status = 1;
		$object->fk_user_mod = $user->id;
		$object->datem = $now;
					//update
		$res = $object->update($user);

		if ($res<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		$action = '';
	}
}
if ($action == 'revalidate')
{
	$res = $object->fetch($id);
	if ($res == 1)
	{
				//cambiando a validado
	$object->status = 0;
				//update
	$object->update($user);
	$action = '';
	}
}

if ($action == 'add' && !$cancel && $user->rights->contab->vision->write)
{
	$object->entity = $conf->entity;
	$object->ref = strtoupper(trim(GETPOST('ref')));
	$object->fk_parent = GETPOST('fk_parent','int')+0;

	$object->sequence = GETPOST('sequence');
	$object->account = GETPOST('account');
	$object->account_sup = GETPOST('account_sup');
	$object->detail_managment = GETPOST('detail_managment');
	$object->cta_normal = GETPOST('cta_normal');
	$object->cta_column = GETPOST('cta_column')+0;
	$object->cta_class = GETPOST('cta_class');
	$object->cta_balances = GETPOST('cta_balances');
	$object->cta_totalvis = GETPOST('cta_totalvis');
	$object->name_vision = GETPOST('name_vision');
	$line  = GETPOST('line');
	if (empty($line)) $object->line = '001';
	else $object->line = $line;
	//identifier
	$aIdentifier = GETPOST('cta_identifier');
	foreach ((array) $aIdentifier AS $i => $value)
	{
		if (!empty($cta_identifier)) $cta_identifier .= '|';
		$cta_identifier .= $i;
	}
	$object->cta_identifier = $cta_identifier;
	//if ($object->cta_class == 2) $object->cta_identifier = '';
	$object->status = 0;
	$object->cta_operation = GETPOST('cta_operation')+0;
	$object->fk_accountini = GETPOST('fk_accountini')+0;
	$object->fk_accountfin = GETPOST('fk_accountfin')+0;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->datec = $now;
	$object->datem = $now;

	$newid=$object->create($user);
	//exit;
	if ($newid > 0)
	{
		$action = '';
		setEventMessages($langs->trans('Saverecord'),null,'mesgs');
		header('Location: '.DOL_URL_ROOT.'/contab/vision/fiche.php?id='.($id?$id:$newid));
		exit;
	// Creation OK
	}
	else
	{
		// Creation KO
		setEventMessages($object->error,$object->errors,'errors');
		$mesg=$object->error;
		$action = 'create';
	}
}

if ($action == 'update' && $user->rights->contab->vision->mod)
{
	$res = $object->fetch($idr);
	if ($res==1)
	{
		$db->begin();
		$object->fk_parent = GETPOST('fk_parent','int')+0;
		$object->ref = strtoupper(trim(GETPOST('ref','alpha')));
		$object->sequence = GETPOST('sequence');
		$object->account = GETPOST('account');
		$object->account_sup = GETPOST('account_sup');
		$object->detail_managment = GETPOST('detail_managment');
		$object->cta_normal = GETPOST('cta_normal');
		$object->cta_column = GETPOST('cta_column');
		$object->cta_class = GETPOST('cta_class');
		$object->cta_balances = GETPOST('cta_balances');
		$object->cta_totalvis = GETPOST('cta_totalvis');
		$object->name_vision = GETPOST('name_vision');
		$object->line = GETPOST('line');
		$aIdentifier = GETPOST('cta_identifier');
		foreach ((array) $aIdentifier AS $i => $value)
		{
			if (!empty($cta_identifier)) $cta_identifier .= '|';
			$cta_identifier .= $i;
		}
		$object->cta_identifier = $cta_identifier;
	//if ($object->cta_class == 2) $object->cta_identifier = '';
		$object->fk_accountini = GETPOST('fk_accountini')+0;
		$object->fk_accountfin = GETPOST('fk_accountfin')+0;
		$object->cta_operation = GETPOST('cta_operation')+0;

		if ($object->fk_user_create<=0 || is_null($object->fk_user_create))
			$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		if ($object->datec<=0 || is_null($object->datec))
			$object->datec = $now;
		$object->datem = $now;

		$res=$object->update($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (empty($error))
		{
			$res = $objtmp->fetch($idr);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objtmp->error,$objtmp->errors,'errors');
			}
		//vamos a actualizar los hijos
			$filter = " AND t.fk_parent = ".$idr;
			$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter);
			if ($res>0)
			{
				$lines = $object->lines;
				foreach ($lines AS $j => $line)
				{
					$res = $object->fetch($line->id);
					if ($res == 1)
					{
						$object->name_vision = $objtmp->name_vision;
						$object->account = $objtmp->account;
						$object->account_sup = $objtmp->account_sup;
						$object->detail_managment = $objtmp->detail_managment;
						$object->cta_identifier = $objtmp->cta_identifier;
						$object->cta_normal = $objtmp->cta_normal;
						$object->cta_column = $objtmp->cta_column;
						$object->cta_class = $objtmp->cta_class;
						$object->fk_user_mod = $user->id;
						$object->datem = $now;
						$res = $object->update($user);
						if ($res <=0)
						{
							$error++;
							setEventMessages($object->error,$object->errors,'errors');
						}
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Successfullyupdate'),null,'mesgs');
			header('Location: '.DOL_URL_ROOT.'/contab/vision/fiche.php?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
		// Creation KO
			setEventMessages($object->error,$object->errors,'errors');
			$action = 'edit';
		}
	}
}

if ($action == 'confirm_delete' && $id>0 && $confirm == 'yes' && $user->rights->contab->vision->del)
{
	$db->begin();
	$object->fetch($id);
	$res = $object->delete($user);
	if ($res <=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		$filter = " AND t.fk_parent = ".$id;
		$res = $objtmp->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		if ($res > 0)
		{
			foreach ($objtmp->lines AS $j => $line)
			{
				$object->fetch($line->id);
				$res = $object->delete($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
	}
	if (empty($error))
	{
		$db->commit();
		$action = '';
		header('Location: '.DOL_URL_ROOT.'/contab/vision/list.php');
		exit;
	}
	else
		$db->rollback();
	$action = '';
}
if ($action == 'delitem' && $idr>0 && $user->rights->contab->vision->del)
{
	$object->fetch($idr);
	$object->delete($user);
	$action = '';
	$object->fetch(0,GETPOST('ref'));
	header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET['id'] = $_SESSION["idvision"];
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
$aArrjs = array('contab/javascript/app.js');

llxHeader("",$langs->trans("Managementaccounting"),$help_url,'','','',$aArrjs);
$object = new Contabvisionext($db);
$form=new Form($db);

if ($action == 'create' && $user->rights->contab->vision->write)
{
	print_fiche_titre($langs->trans("Newvision"));

	print '<form name="frm" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	//dol_htmloutput_mesg($mesg);
	if ($ref)
	{
		$filter = " AND t.ref = '".$ref."'";
		$filter.= " AND t.entity = ".$conf->entity;
		$res = $object->fetchAll('','',1,1,array(1=>1),'AND',$filter,true);
		if ($res)
		{
			$name_vision = $object->name_vision;
			$sequence = $object->sequence + 10;
			$account = $object->account+1;
		}
	}

	?>
	<script type="text/javascript">
		function CambiarURLFrame(ref){
			document.getElementById('iframe').src= 'consultaref.php?ref=' + ref;
		}
	</script>
	<iframe id="iframe" src="contab/vision/consultaref.php" width="0" height="0" frameborder="0"></iframe>


	<?php

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="3">';
	print '<input id="ref" type="text" value="'.GETPOST('ref').'" name="ref" size="2" maxlength="3" onblur="CambiarURLFrame(this.value);">';
	print '</td></tr>';

	//sequence
	print '<tr><td class="fieldrequired">'.$langs->trans('Sequence').'</td><td colspan="3">';
	print '<input id="sequence" type="text" value="'.(GETPOST('sequence')?GETPOST('sequence'):$sequence).'" name="sequence" size="10" maxlength="12">';
	print '</td></tr>';

	//acount
	print '<tr><td class="fieldrequired">'.$langs->trans('Account').'</td><td colspan="3">';
	print '<input id="account" type="text" value="'.(GETPOST('account')?GETPOST('account'):$account).'" name="account" size="18" maxlength="20">';
	print '</td></tr>';

	//account_sup
	print '<tr><td>'.$langs->trans('Resultingaccount').'</td><td colspan="3">';
	print '<input id="account_sup" type="text" value="'.GETPOST('account_sup').'" name="account_sup" size="18" maxlength="20">';
	print '</td></tr>';

	//name vision
	print '<tr><td class="fieldrequired">'.$langs->trans('Namevision').'</td><td colspan="3">';
	print '<input id="name_vision" type="text" value="'.(GETPOST('name_vision')?GETPOST('name_vision'):$name_vision).'" name="name_vision" size="78" maxlength="80" '.(!empty($name_vision)?' readonly':'').'>';
	print '</td></tr>';

	//detail
	print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="3">';
	print '<input id="detail_managment" type="text" value="'.GETPOST('detail_managment').'" name="detail_managment" size="40" maxlength="100">';
	print '</td></tr>';

	//cta_normal
	print '<tr><td class="fieldrequired">'.$langs->trans('Normalbalance').'</td><td>';
	print select_cta_normal(GETPOST('cta_normal'),'cta_normal','','',1);
	print '</td>';

	//cta_class
	print '<td class="fieldrequired">'.$langs->trans('Class').'</td><td>';
	print select_cta_clase(GETPOST('cta_class'),'cta_class','','',1);
	print '</td></tr>';

	//columnas
	print '<tr><td>'.$langs->trans('Column').'</td><td>';
	print '<input id="cta_column" type="text" value="'.GETPOST('cta_column').'" name="cta_column" size="1" maxlength="1">';
	print '</td>';

	//Identifier
	print '<td>'.$langs->trans('Identifier').'</td><td nowrap>';
	print '<input id="c_id1" type="checkbox"  name="cta_identifier[1]">&nbsp;<label>'.$langs->trans('Negrita').'</label>&nbsp;';
	print '<input id="c_id2" type="checkbox"  name="cta_identifier[2]" onClick="app2()">&nbsp;<label>'.$langs->trans('Suma Total').'</label>&nbsp;';
	print '<input id="c_id3" type="checkbox"  name="cta_identifier[3]" onClick="app3()">&nbsp;<label>'.$langs->trans('Sin Valor').'</label>';
	print '</td></tr>';

	//cta_balances
	print '<tr><td class="fieldrequired">'.$langs->trans('Balances').'</td><td>';
	print select_balances(GETPOST('cta_balances'),'cta_balances','','',1);
	print '</td>';

	//cta_totalvis
	print '<td class="fieldrequired">'.$langs->trans('Totalyesno').'</td><td>';
	print select_yesno((GETPOST('cta_totalvis')?GETPOST('cta_totalvis'):2),'cta_totalvis','','',1);
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
		$_SESSION['refvision'] = $object->ref;
		$_SESSION['idvision'] = $object->id;

	 // Affichage fiche

		if ($action <> 'edit' && $action <> 're-edit')
		{
		 //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Vision"), 0, 'contab');


			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletevision"),$langs->trans("ConfirmDeletevision",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

			// ref
			print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
			print $object->ref;
			print '</td></tr>';

			//sequence
			print '<tr><td>'.$langs->trans('Sequence').'</td><td colspan="3">';
			print $object->sequence;
			print '</td></tr>';

			//acount
			print '<tr><td>'.$langs->trans('Account').'</td><td colspan="3">';
			print $object->account;
			print '</td></tr>';

			//account_sup
			print '<tr><td>'.$langs->trans('Accountsup').'</td><td colspan="3">';
			print $object->account_sup;
			print '</td></tr>';

			//name vision
			print '<tr><td>'.$langs->trans('Namevision').'</td><td colspan="3">';
			print $object->name_vision;
			print '</td></tr>';

			//detail
			print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="3">';
			print $object->detail_managment;
			print '</td></tr>';

			//cta_normal
			print '<tr><td>'.$langs->trans('Normalbalance').'</td><td>';
			print select_cta_normal($object->cta_normal,'cta_normal','','',1,1);
			print '</td>';

			//cta_class
			print '<td>'.$langs->trans('Class').'</td><td>';
			print select_cta_clase($object->cta_class,'cta_class','','',1,1);
			print '</td></tr>';

			//columnas
			print '<tr><td>'.$langs->trans('Column').'</td><td>';
			print $object->cta_column;
			print '</td>';

			//Identifier
			print '<td>'.$langs->trans('Identifier').'</td><td nowrap>';
			$aIdentifier = explode('|',$object->cta_identifier);
			$j = 1;
			foreach((array) $aIdentifier AS $i => $value)
			{
				if ($j > 1) print ' | ';
				if ($value == 1) print $langs->trans('Fila en Negrita');
				if ($value == 2) print $langs->trans('Suma Total');
				if ($value == 3) print $langs->trans('Sin Valor');
				$j++;
			}

			//cta_balances
			print '<tr><td>'.$langs->trans('Balances').'</td><td>';
			print select_balances($object->cta_balances,'cta_balances','','',1,1);
			print '</td>';

			//cta_totalvis
			print '<td>'.$langs->trans('Totalyesno').'</td><td>';
			print select_yesno($object->cta_totalvis,'cta_totalvis','','',1,1);
			print '</td></tr>';


			print "</table>";

			print '</div>';

			if ($object->cta_class == '2')
			{
				$filter = " AND t.fk_parent = $id";
				$num = $objtmp->fetchAll('ASC','line',0,0,array(1=>1),'AND',$filter);
			}
			/* *************************************************** */
			/*                                                     */
			/* Barre d'action                                      */
			/*                                                     */
			/* *************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{

				if ($user->rights->contab->vision->write)
					print "<a class=\"butAction\" href=\"fiche.php?action=create&ref=".$object->ref."\">".$langs->trans("New")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("New")."</a>";
				if ($user->rights->contab->vision->mod && $object->status == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($object->status==0 && $user->rights->contab->vision->del && empty($num))
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
				// Valid
				if ($object->status == 0 && $user->rights->contab->vision->val)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
				}
				// ReValid
				if ($object->status == 1 && $user->rights->contab->vision->val)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Changetodraft').'</a>';
				}
			}

			print "</div>";
		 	//registro de los items de la vision
			if ($object->cta_class == '2')
			{
				print '<table class="noborder" width="100%">';

				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Line"),"liste.php", "t.ref","","",'align="left"');
				print_liste_field_titre($langs->trans("Accountini"),"liste.php", "t.sequence","","",'align="left"');
				print_liste_field_titre($langs->trans("Accountfin"),"liste.php", "t.account","","",'align="left"');
				print_liste_field_titre($langs->trans("Operation"),"liste.php", "t.account_sup","","",'align="left"');
				print_liste_field_titre($langs->trans("Action"),"liste.php", "t.name_vision","","",'align="right"');
				print "</tr>\n";


				//registro nuevo
				if ($action == 'new')
				{
					//formulario de registro item nuevo
					//registro_item($object);
					$obj = clone $object;
					include DOL_DOCUMENT_ROOT.'/contab/vision/tpl/vision_create.tpl.php';
				}
				if ($num)
				{
					$lines = $objtmp->lines;
					$var=True;
					foreach ($lines AS $i => $obj)
					{
						if ($obj)
						{
							if ($action == 'edititem' && $idr == $obj->id)
							{
								include DOL_DOCUMENT_ROOT.'/contab/vision/tpl/vision_create.tpl.php';
							}
							else
							{
								print '<tr>';
								print '<td>'.$obj->line.'</td>';

								$objAccounting->fetch($obj->fk_accountini);
								print '<td>'.$objAccounting->account_number.'</td>';
								$objAccounting->fetch($obj->fk_accountfin);
								print '<td>'.$objAccounting->account_number.'</td>';
								print '<td>'.select_operation($obj->cta_operation,'cta_operation','','',1,1).'</td>';
								print '<td align="right">';
								if ($obj->line != '001' && $object->statut == 0)
								{
									print '<a href="'.DOL_URL_ROOT.'/contab/vision/fiche.php?id='.$id.'&idr='.$obj->id.'&amp;ref='.$obj->ref.'&amp;action=edititem">';
									print img_picto($langs->trans("Edit"),'edit');
									print '</a>';
									print '&nbsp;';

									print '<a href="'.DOL_URL_ROOT.'/contab/vision/fiche.php?id='.$id.'&idr='.$obj->id.'&amp;ref='.$obj->ref.'&amp;action=delitem">';
									print img_picto($langs->trans("Delete"),'delete');
									print '</a>';

								}
								print '</td>';

								print '</tr>';
							}

							$_SESSION['lineult'] = $obj->line;
						}
						$i++;
					}
				}

				print '</table>';
		 		//button action
				/* *************************************************** */
				/*                                                     */
				/* Barre d'action                                      */
				/*                                                     */
				/* *************************************************** */

				print "<div class=\"tabsAction\">\n";

				if ($action == '')
				{
					if ($user->rights->contab->vision->write && $object->statut == 0)
						print "<a class=\"butAction\" href=\"fiche.php?action=new&id=".$object->id."\">".$langs->trans("Create")."</a>";
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
				}

				print "</div>";

			}
		 //fin registro items de la vision
		}


	  // Edition fiche

		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

			print '<form action="fiche.php" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idr" value="'.$object->id.'">';
			print '<input type="hidden" name="line" value="'.$object->line.'">';
			print '<input type="hidden" name="cta_operation" value="'.$object->cta_operation.'">';
			print '<input type="hidden" name="fk_accountini" value="'.$object->fk_accountini.'">';
			print '<input type="hidden" name="fk_accountfin" value="'.$object->fk_accountfin.'">';

			print '<table class="border" width="100%">';

		 // ref
			print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="3">';
			print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="2" maxlength="3">';
			print '</td></tr>';

		 //sequence
			print '<tr><td class="fieldrequired">'.$langs->trans('Sequence').'</td><td colspan="3">';
			print '<input id="sequence" type="text" value="'.$object->sequence.'" name="sequence" size="10" maxlength="12">';
			print '</td></tr>';

		 //acount
			print '<tr><td class="fieldrequired">'.$langs->trans('Account').'</td><td colspan="3">';
			print '<input id="account" type="text" value="'.$object->account.'" name="account" size="18" maxlength="20">';
			print '</td></tr>';

		 //account_sup
			print '<tr><td>'.$langs->trans('Accountsup').'</td><td colspan="3">';
			print '<input id="account_sup" type="text" value="'.$object->account_sup.'" name="account_sup" size="18" maxlength="20">';
			print '</td></tr>';


		 //name vision
			print '<tr><td class="fieldrequired">'.$langs->trans('Namevision').'</td><td colspan="3">';
			print '<input id="name_vision" type="text" value="'.$object->name_vision.'" name="name_vision" size="78" maxlength="80">';
			print '</td></tr>';

		 //detail
			print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="3">';
			print '<input id="detail_managment" type="text" value="'.$object->detail_managment.'" name="detail_managment" size="40" maxlength="100">';
			print '</td></tr>';

		 //cta_normal
			print '<tr><td class="fieldrequired">'.$langs->trans('Normalbalance').'</td><td>';
			print select_cta_normal($object->cta_normal,'cta_normal','','',1);
			print '</td>';

		 //cta_class
			print '<td class="fieldrequired">'.$langs->trans('Class').'</td><td>';
			print select_cta_clase($object->cta_class,'cta_class','','',1);
			print '</td></tr>';

		 //columnas
			print '<tr><td>'.$langs->trans('Column').'</td><td>';
			print '<input id="cta_column" type="text" value="'.$object->cta_column.'" name="cta_column" size="1" maxlength="1">';
			print '</td>';

		 //Identifier
			$aIdentifier = explode('|',$object->cta_identifier);
			$checked1 = '';
			$checked2 = '';
			$checked3 = '';
			foreach((array) $aIdentifier AS $ij => $value)
			{
				if ($value == 1)
					$checked1 = 'checked';
				if ($value == 2)
					$checked2 = 'checked';
				if ($value == 3)
					$checked3 = 'checked';
				$j++;
			}
			print '<td class="fieldrequired">'.$langs->trans('Identifier').'</td><td nowrap>';
			print '<input id="cta_identifier" type="checkbox"  name="cta_identifier[1]" '.$checked1.'>&nbsp;<label>'.$langs->trans('Negrita').'</label>&nbsp;';
			print '<input id="cta_identifier" type="checkbox"  name="cta_identifier[2]" '.$checked2.'>&nbsp;<label>'.$langs->trans('Suma Total').'</label>&nbsp;';
			print '<input id="cta_identifier" type="checkbox"  name="cta_identifier[3]" '.$checked3.'>&nbsp;<label>'.$langs->trans('Sin Valor').'</label>';
			print '</td></tr>';

		 //cta_balances
			print '<tr><td class="fieldrequired">'.$langs->trans('Balances').'</td><td>';
			print select_balances($object->cta_balances,'cta_balances','','',1);
			print '</td>';

		 //cta_totalvis
			print '<td class="fieldrequired">'.$langs->trans('Totalyesno').'</td><td>';
			print select_yesno($object->cta_totalvis,'cta_totalvis','','',1);
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';

		}
	}
}


// End of page
llxFooter();
$db->close();

function registro_item($obj)
{
	global $db,$langs;
	$objectaccount = new Contabaccountingext($db);
  //edicion
	print '<form action="fiche.php" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	if ($_GET['action'] == 'new')
	{
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="sequence" value="'.$obj->sequence.'">';
		$fk_accountini = '';
		$fk_accountfin = '';
		$cta_operation = '';

		$line = str_pad($_SESSION['lineult']+1, 3, "0", STR_PAD_LEFT);
	}
	else
	{
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$obj->rowid.'">';
		print '<input type="hidden" name="sequence" value="'.$obj->sequence.'">';
		$fk_accountini = $obj->fk_accountini;
		$fk_accountfin = $obj->fk_accountfin;
		$cta_operation = $obj->cta_operation;
		$line = $obj->line;
	}
	print '<input type="hidden" name="ref" value="'.$obj->ref.'">';
	print '<input type="hidden" name="name_vision" value="'.$obj->name_vision.'">';
	print '<input type="hidden" name="account" value="'.$obj->account.'">';
	print '<input type="hidden" name="account_sup" value="'.$obj->account_sup.'">';
	print '<input type="hidden" name="detail_managment" value="'.$obj->detail_managment.'">';
	print '<input type="hidden" name="identifier" value="'.$obj->identifier.'">';
	print '<input type="hidden" name="cta_normal" value="'.$obj->cta_normal.'">';
	print '<input type="hidden" name="cta_class" value="'.$obj->cta_class.'">';
	print '<input type="hidden" name="cta_column" value="'.$obj->cta_column.'">';
	print '<input type="hidden" name="cta_balances" value="'.$obj->cta_balances.'">';
	print '<input type="hidden" name="cta_totalvis" value="'.$obj->cta_totalvis.'">';

	print '<tr>';
  //line
	print '<td>';
	print '<input id="" type="text" value="'.$line.'" name="line" size="2" maxlength="3">';
	print '</td>';

  //account ini
	print '<td>';
	print $objectaccount->select_account($fk_accountini,'fk_accountini','',25,1,2,2);
	print '</td>';

  //account fin
	print '<td>';
	print $objectaccount->select_account($fk_accountfin,'fk_accountfin','',25,1,2,2);
	print '</td>';

  //operation
	print '<td>';
	print select_operation($cta_operation,'cta_operation','','',1);
	print '</td>';
	print '<td>';
	print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
	print '</td>';
	print '</tr>';

	print '</form>';
	return;
}
?>
