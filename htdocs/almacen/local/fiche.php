<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/product/stock/fiche.php
 *	\ingroup    stock
 *	\brief      Page fiche entrepot
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/entrepotext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/productext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
if ($conf->monprojet->enabled)
{
	//dol_include_once('/monprojet/lib/verifcontact.lib.php');
	//dol_include_once('/monprojet/class/taskadd.class.php');
	dol_include_once('/monprojet/class/projectext.class.php');
	//dol_include_once('/monprojet/class/html.formtask.class.php');
	dol_include_once('/monprojet/class/html.formprojetext.class.php');
	//dol_include_once('/monprojet/class/projettaskelement.class.php');
	//dol_include_once('/monprojet/lib/monprojet.lib.php');
}
$langs->load("almacen");
$langs->load("entrepot");
$langs->load("products");
$langs->load("stocks");
$langs->load("companies");

$action=GETPOST('action');
$id=GETPOST('id');

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

if (!$user->rights->almacen->local->read) accessforbidden();

$object = new Entrepotext($db);
/*
 * Actions
 */
// Ajout entrepot
if ($action == 'add' && $user->rights->almacen->local->write)
{
	$objrel = new Entrepotrelationext($db);

	$object->ref         = $_POST["libelle"];
	$object->libelle     = $_POST["libelle"];
	$object->description = $_POST["desc"];
	$object->statut      = $_POST["statut"];
	$object->lieu        = $_POST["lieu"];
	$object->address     = $_POST["address"];
	$object->cp          = $_POST["zipcode"];
	$object->ville       = $_POST["town"];
	$object->pays_id     = $_POST["country_id"];
	$object->zip         = $_POST["zipcode"];
	$object->town        = $_POST["town"];
	$object->country_id  = $_POST["country_id"];
	$object->fk_parent  = $_POST["fk_entrepot"];

	$objrel->fk_entrepot_father = $_POST['fk_entrepot']+0;
	$objrel->fk_projet = $_POST['fk_projet']+0;
	$objrel->tipo = dol_strtoupper(GETPOST('type'));
	if (empty($object->ref))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), 'errors');
		$error++;
		$action='create';
	}
	if (empty($object->libelle))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), 'errors');
		$error++;
		$action='create';
	}

	if (!$error)
	{
		$db->begin();
		$id = $object->create($user);
		if ($id > 0)
		{
			$objrel->rowid = $id;
			$res = $objrel->create($user);
			if ($res < 0)
			{
				$error++;
				setEventMessages($objrel->error,$objrel->errors,'errors');
				$action = 'create';
			}
		}
		else
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (empty($error))
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$db->commit();
			header("Location: fiche.php?id=".$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action='create';
		}
		$action = 'create';
	}
	else
	{
		setEventMessages($langs->trans("ErrorWarehouseRefRequired"),null,'errors');
		$action="create";
	}
}

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->local->del)
{
	$objrel = new Entrepotrelationext($db);
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		$objrel->fetch($_REQUEST['id']);
		$objrel->delete($user);
		setEventMessages($langs->trans('Successfulldelete'),null,'mesgs');
		header("Location: ".DOL_URL_ROOT.'/almacen/local/liste.php');
		exit;
	}
	else
	{
		setEventMessages($object->error,$object->errors,'errors');
		$action='';
	}
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->almacen->local->write)
{
	$objrel = new Entrepotrelationext($db);
	$res = $object->fetch($_POST["id"]);
	if ($res >0)
	{
		$object->libelle     = $_POST["libelle"];
		$object->description = $_POST["desc"];
		$object->statut      = $_POST["statut"];
		$object->lieu        = $_POST["lieu"];
		$object->address     = $_POST["address"];
		$object->cp          = $_POST["zipcode"];
		$object->ville       = $_POST["town"];
		$object->pays_id     = $_POST["country_id"];
		$object->zip         = $_POST["zipcode"];
		$object->town        = $_POST["town"];
		$object->country_id  = $_POST["country_id"];
		$object->fk_parent  = $_POST["fk_entrepot"];

		$objrel->fetch($_POST["id"]);
		$objrel->fk_projet = GETPOST('fk_projet')+0;
		$objrel->fk_entrepot_father = GETPOST('fk_entrepot_father')+0;
		$objrel->tipo = dol_strtoupper(GETPOST('type'));

		$res = $object->update($_POST['id'],$user);
		if ( $res > 0)
		{
			if (empty($objrel->id))
			{
				$objrel->rowid = $_POST["id"];
				$objrel->tipo = "almacen";
				$objrel->fk_projet = GETPOST('fk_projet')+0;
				$res = $objrel->create($user);
				if ($res <=0)
					setEventMessages($objrel->error,$objrel->errors,'errors');

			}
			else
			{
				$objrel->rowid = $_POST['id'];
				$res = $objrel->update($user);
				if ($res <=0)
					setEventMessages($objrel->error,$objrel->errors,'errors');
			}
			$action = '';
			$_GET["id"] = $_POST["id"];
			//$mesg = '<div class="ok">Fiche mise a jour</div>';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			setEventMessages($object->error,$object->errors,'errors');
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		setEventMessages($object->error,$object->errors,'errors');
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

$productstatic=new Product($db);
$form=new Form($db);
$formcompany=new FormCompany($db);
if ($conf->monprojet->enabled)
	$formproject = new FormProjetsext($db);


$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("WarehouseCard"),$help_url);

if ($action == 'create' && $user->rights->almacen->local->write)
{
	$objectrel = new Entrepotrelationext($db);
	print_fiche_titre($langs->trans("NewWarehouse"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="type" value="'.$type.'">'."\n";

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="libelle" size="20" value="'.GETPOST('libelle').'" required></td></tr>';

	print '<tr><td  class="fieldrequired">'.$langs->trans("LocationSummary").'</td><td colspan="3"><input name="lieu" size="40" value="'.$object->lieu.'" required></td></tr>';

	// Description
	print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">';
	// Editeur wysiwyg
	require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
	$doleditor=new DolEditor('desc',$object->description,'',180,'dolibarr_notes','In',false,true,$conf->fckeditor->enabled,5,70);
	$doleditor->Create();
	print '</td></tr>';


	print '<tr><td>'.$langs->trans('Address').'</td><td colspan="3"><textarea name="address" cols="60" rows="3" wrap="soft">';
	print $object->address;
	print '</textarea></td></tr>';

	// idPadre
	print '<tr><td width="25%">'.$langs->trans('localSuperior').'</td><td colspan="3">';
	print $objectrel->select_padre(GETPOST('fk_entrepot'),'fk_entrepot',1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Typeentrepot").'</td><td colspan="3">';
	//<input name="type" size="20" maxlength="30" value="'.GETPOST('type').'" required></td></tr>';
	print select_type_entrepot(GETPOST('type'),'type',' required ',0,1,0);
	if ($user->admin) print '&nbsp;'.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>';
	//projet
	if ($conf->monprojet->enabled)
	{
		print '<tr><td>'.$langs->trans("Project").'</td><td>';
		$filterkey = '';
		$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
		print '</td></tr>';
	}
	// Zip / Town
	print '<tr><td>'.$langs->trans('Zip').'</td><td>';
	print $formcompany->select_ziptown($object->zip,'zipcode',array('town','selectcountry_id','departement_id'),6);
	print '</td><td>'.$langs->trans('Town').'</td><td>';
	print $formcompany->select_ziptown($object->town,'town',array('zipcode','selectcountry_id','departement_id'));
	print '</td></tr>';

	// Country
	print '<tr><td width="25%">'.$langs->trans('Country').'</td><td colspan="3">';
	print $form->select_country($object->country_id?$object->country_id:$mysoc->country_code,'country_id');
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
	print '</td></tr>';

	// Status
	print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">';
	print '<select name="statut" class="flat">';
	foreach ($object->statuts as $key => $value)
	{
		if ($key == 1)
		{
			print '<option value="'.$key.'" selected>'.$langs->trans($value).'</option>';
		}
		else
		{
			print '<option value="'.$key.'">'.$langs->trans($value).'</option>';
		}
	}
	print '</select>';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

	print '</form>';
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);

		$object = new Entrepotext($db);
		$objectPadre = new Entrepotext($db);

		$objectrel = new Entrepotrelationext($db);

		$result = $object->fetch($id);
		$resultUr = $objectrel->fetch($id);
		if ($result < 0)
		{
			dol_print_error($db);
		}

		//fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
			$head = almacen_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Warehouse"), 0, 'stock');

		 // Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteAWarehouse"),$langs->trans("ConfirmDeleteWarehouse",$object->libelle),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

		 // Ref
			print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
			print $form->showrefnav($object,'id','',1,'rowid','libelle');
			print '</td>';

			print '<tr><td>'.$langs->trans("LocationSummary").'</td><td colspan="3">'.$object->lieu.'</td></tr>';

		 // Description
			print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">'.nl2br($object->description).'</td></tr>';

		 // Address
			print '<tr><td>'.$langs->trans('Address').'</td><td colspan="3">';
			print $object->address;
			print '</td></tr>';

		 // father
			if ($objectrel->fk_entrepot_father>0)
			{
				print '<tr><td>'.$langs->trans('Father').'</td><td colspan="3">';
				$objectPadre->fetch($objectrel->fk_entrepot_father);
				print $objectPadre->getNomUrladd(1);
				print '</td></tr>';
			}
			if ($objectrel->tipo)
			{
				print '<tr><td>'.$langs->trans('Typeentrepot').'</td><td colspan="3">';
				print select_type_entrepot($objectrel->tipo,'type',' required ',0,0,1);
				print '</td></tr>';


			}
		 // Ville
			print '<tr><td width="25%">'.$langs->trans('Zip').'</td><td width="25%">'.$object->zip.'</td>';
			print '<td width="25%">'.$langs->trans('Town').'</td><td width="25%">'.$object->town.'</td></tr>';

		 // Country
			print '<tr><td>'.$langs->trans('Country').'</td><td colspan="3">';
			$img=picto_from_langcode($object->country_code);
			print ($img?$img.' ':'');
			print $object->country;
			print '</td></tr>';

		//projet
			if ($conf->monprojet->enabled)
			{
				$projet = new Project($db);
				$projet->fetch($objectrel->fk_projet);
				print '<tr><td>'.$langs->trans("Project").'</td><td>';
				if ($projet->id == $objectrel->fk_projet)
					print $projet->title;
				print '</td></tr>';
			}
		 // Statut
			print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';

			$calcproducts=$object->nb_products();

		 // Nb of products
			print '<tr><td valign="top">'.$langs->trans("NumberOfProducts").'</td><td colspan="3">';
			print empty($calcproducts['nb'])?'0':$calcproducts['nb'];
			print "</td></tr>";

		 // Value
			print '<tr><td valign="top">'.$langs->trans("EstimatedStockValueShort").'</td><td colspan="3">';
			print empty($calcproducts['value'])?'0':$calcproducts['value'];
			print "</td></tr>";

		 // Last movement
			$sql = "SELECT max(m.datem) as datem";
			$sql .= " FROM ".MAIN_DB_PREFIX."stock_mouvement as m";
			$sql .= " WHERE m.fk_entrepot = '".$object->id."'";
			$resqlbis = $db->query($sql);
			if ($resqlbis)
			{
				$obj = $db->fetch_object($resqlbis);
				$lastmovementdate=$db->jdate($obj->datem);
			}
			else
			{
				dol_print_error($db);
			}
			print '<tr><td valign="top">'.$langs->trans("LastMovement").'</td><td colspan="3">';
			if ($lastmovementdate)
			{
				print dol_print_date($lastmovementdate,'dayhour').' ';
				print '(<a href="'.DOL_URL_ROOT.'/product/stock/mouvement.php?id='.$object->id.'">'.$langs->trans("FullList").'</a>)';
			}
			else
			{
				print $langs->trans("None");
			}
			print "</td></tr>";

			print "</table>";

			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				if ($user->rights->stock->creer)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->stock->supprimer)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}

			print "</div>";


			/* ************************************************************************** */
			/*                                                                            */
			/* Affichage de la liste des produits de l'entrepot                           */
			/*                                                                            */
			/* ************************************************************************** */
			print '<br>';

			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
			print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Units"),"", "ps.reel","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("AverageUnitPricePMPShort"),"", "p.pmp","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
		//print_liste_field_titre($langs->trans("EstimatedStockValueShort"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if (empty($conf->global->PRODUIT_MULTIPRICES)) print_liste_field_titre($langs->trans("SellPriceMin"),"", "p.price","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if (empty($conf->global->PRODUIT_MULTIPRICES)) print_liste_field_titre($langs->trans("EstimatedStockValueSellShort"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if ($user->rights->stock->mouvement->creer) print '<td>&nbsp;</td>';
			//if ($user->rights->stock->creer)            print '<td>&nbsp;</td>';
			print "</tr>";

			$totalunit=0;
			$totalvalue=$totalvaluesell=0;

			$sql = "SELECT p.rowid as rowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp, p.pmp as ppmp, p.price, p.price_ttc,";
			$sql.= " ps.reel as value";
			$sql.= " FROM ".MAIN_DB_PREFIX."product_stock ps, ".MAIN_DB_PREFIX."product p";
			$sql.= " WHERE ps.fk_product = p.rowid";
			$sql.= " AND ps.reel <> 0";
			// We do not show if stock is 0 (no product in this warehouse)
			$sql.= " AND ps.fk_entrepot = ".$object->id;
			$sql.= $db->order($sortfield,$sortorder);

			dol_syslog('List products sql='.$sql);
			$resql = $db->query($sql);
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;
				$var=True;
				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);

			 // Multilangs
					if ($conf->global->MAIN_MULTILANGS)
			 // si l'option est active
					{
						$sql = "SELECT label";
						$sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
						$sql.= " WHERE fk_product=".$objp->rowid;
						$sql.= " AND lang='". $langs->getDefaultLang() ."'";
						$sql.= " LIMIT 1";

						$result = $db->query($sql);
						if ($result)
						{
							$objtp = $db->fetch_object($result);
							if ($objtp->label != '') $objp->produit = $objtp->label;
						}
					}

					$var=!$var;
			 //print '<td>'.dol_print_date($objp->datem).'</td>';
					print "<tr ".$bc[$var].">";
					print "<td>";
					$productstatic->id=$objp->rowid;
					$productstatic->ref=$objp->ref;
					$productstatic->type=$objp->type;
					print $productstatic->getNomUrl(1,'stock',16);
					print '</td>';
					print '<td>'.$objp->produit.'</td>';

					print '<td align="right">'.$objp->value.'</td>';
					$totalunit+=$objp->value;

			 // Price buy PMP
			// print '<td align="right">'.price(price2num($objp->pmp,'MU')).'</td>';
			 // Total PMP
			// print '<td align="right">'.price(price2num($objp->pmp*$objp->value,'MT')).'</td>';
					$totalvalue+=price2num($objp->pmp*$objp->value,'MT');

			 // Price sell min
					if (empty($conf->global->PRODUIT_MULTIPRICES))
					{
						$pricemin=$objp->price;
						print '<td align="right">';
						print price(price2num($pricemin,'MU'));
						print '</td>';
			 // Total sell min
						print '<td align="right">';
						print price(price2num($pricemin*$objp->value,'MT'));
						print '</td>';
					}
					$totalvaluesell+=price2num($pricemin*$objp->value,'MT');

					if ($user->rights->stock->mouvement->creer)
					{
						//print '<td align="center"><a href="'.DOL_URL_ROOT.'/product/stock/product.php?dwid='.$object->id.'&amp;id='.$objp->rowid.'&amp;action=transfert">';
						//print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("StockMovement");
						//print "</a></td>";
					}

					if ($user->rights->stock->creer)
					{
						//print '<td align="center"><a href="'.DOL_URL_ROOT.'/product/stock/product.php?dwid='.$object->id.'&amp;id='.$objp->rowid.'&amp;action=correction">';
						//print $langs->trans("StockCorrection");
						//print "</a></td>";
					}

					print "</tr>";
					$i++;
				}
				$db->free($resql);

				print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>';
				print '<td class="liste_total" align="right">'.$totalunit.'</td>';
		// print '<td class="liste_total">&nbsp;</td>';
		// print '<td class="liste_total" align="right">'.price(price2num($totalvalue,'MT')).'</td>';
				if (empty($conf->global->PRODUIT_MULTIPRICES))
				{
					print '<td class="liste_total">&nbsp;</td>';
					print '<td class="liste_total" align="right">'.price(price2num($totalvaluesell,'MT')).'</td>';
				}
				print '<td class="liste_total">&nbsp;</td>';
				print '<td class="liste_total">&nbsp;</td>';
				print '</tr>';

			}
			else
			{
				dol_print_error($db);
			}
			print "</table>\n";
		}


		// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1 && $user->rights->almacen->local->write)
		{
			print_fiche_titre($langs->trans("WarehouseEdit"), $mesg);

			print '<form action="fiche.php" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" width="100%">';

		 // Ref
			print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="libelle" size="20" value="'.$object->libelle.'"></td></tr>';

			print '<tr><td width="20%">'.$langs->trans("LocationSummary").'</td><td colspan="3"><input name="lieu" size="40" value="'.$object->lieu.'"></td></tr>';

		 // Description
			print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">';
		 // Editeur wysiwyg
			require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
			$doleditor=new DolEditor('desc',$object->description,'',180,'dolibarr_notes','In',false,true,$conf->fckeditor->enabled,5,70);
			$doleditor->Create();
			print '</td></tr>';

			print '<tr><td>'.$langs->trans('Address').'</td><td colspan="3"><textarea name="address" cols="60" rows="3" wrap="soft">';
			print $object->address;
			print '</textarea></td></tr>';

			// idPadre
			print '<tr><td width="25%">'.$langs->trans('localSuperior').'</td><td colspan="3">';
			//print $form->select_padre($object->country_id?$object->country_id:$mysoc->country_code,'country_id');
			$fk_entrepot_father = (GETPOST('fk_entrepot_father')?GETPOST('fk_entrepot_father'):($object->fk_parent?$object->fk_parent:$objectrel->fk_entrepot_father));
			print $objectrel->select_padre($fk_entrepot_father,'fk_entrepot_father');
			print '</td></tr>';

			print '<tr><td class="fieldrequired">'.$langs->trans("Typeentrepot").'</td><td colspan="3">';
			print select_type_entrepot((GETPOST('type')?GETPOST('type'):$objectrel->tipo),'type',' required ',0,1,0);
			if ($user->admin) print '&nbsp;'.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
			print '</td></tr>';

			//projet
			if ($conf->monprojet->enabled)
			{
				print '<tr><td>'.$langs->trans("Project").'</td><td>';
				$filterkey = '';
				$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $objectrel->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
				print '</td></tr>';
			}
			// Zip / Town
			print '<tr><td>'.$langs->trans('Zip').'</td><td>';
			print $formcompany->select_ziptown($object->zip,'zipcode',array('town','selectcountry_id','departement_id'),6);
			print '</td><td>'.$langs->trans('Town').'</td><td>';
			print $formcompany->select_ziptown($object->town,'town',array('zipcode','selectcountry_id','departement_id'));
			print '</td></tr>';

			// Country
			print '<tr><td width="25%">'.$langs->trans('Country').'</td><td colspan="3">';
			print $form->select_country($object->country_id?$object->country_id:$mysoc->country_code,'country_id');
			if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
			print '</td></tr>';

			print '<tr><td width="20%">'.$langs->trans("Status").'</td><td colspan="3">';
			print '<select name="statut" class="flat">';
			print '<option value="0" '.($object->statut == 0?'selected="selected"':'').'>'.$langs->trans("WarehouseClosed").'</option>';
			print '<option value="1" '.($object->statut == 0?'':'selected="selected"').'>'.$langs->trans("WarehouseOpened").'</option>';
			print '</select>';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';

		}
	}
}


llxFooter();

$db->close();
?>
