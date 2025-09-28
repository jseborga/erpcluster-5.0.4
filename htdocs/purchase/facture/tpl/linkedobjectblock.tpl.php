<?php
/* Copyright (C) 2010-2011 Regis Houssin <regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Marcos García <marcosgdf@gmail.com>
 * Copyright (C) 2015      Charlie Benke <charlie@patas-monkey.com>
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
 *
 */
?>

<!-- BEGIN PHP TEMPLATE -->

<?php

global $user;

$langs = $GLOBALS['langs'];
$linkedObjectBlock = $GLOBALS['linkedObjectBlock'];

$langs->load("bills");

$total=0;
$var=true;
foreach($linkedObjectBlock as $key => $objectlink)
{
    //vamos a verificar que valor corresponde a cada objeto
	if ($objectlink->element == 'invoice_supplier' && $abc)
	{
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetadd.class.php';
		require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
		$srcobj = new Facturefourndetadd($this->db);
		$srcobjfacdet = new CommandeFournisseurLigne($this->db);
		$nItems = 0;
		foreach($objectlink->lines AS $j => $line)
		{

			$srcobj->fetch(0,$line->id);
                		//buscamos al padre
			$srcobjfacdet->fetch($srcobj->fk_object);
			if ($srcobjfacdet->fk_commande == $object->id)
			{
				$nItems++;
				$totalinvoice+= $line->total_ttc;
			}
		}
		$objectlink->total_ttc = $totalinvoice;
	}

	$var=!$var;
	?>
	<tr <?php echo $bc[$var]; ?> >
		<td><?php echo $langs->trans("SupplierInvoice"); ?></td>
		<td><a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo DOL_URL_ROOT.'/purchase/facture/card.php?facid='.$objectlink->id ?>">
		<?php echo img_object($langs->trans("ShowBill"),"bill").' '.$objectlink->ref; ?>
		<small class="badge"><?php print ($nItems>0?$nItems.' '.$langs->trans('Items'):''); ?></small>			
		</a></td>
		<td align="left"></td>
		<td align="center"><?php echo dol_print_date($objectlink->date,'day'); ?></td>
		<td align="right"><?php
			if ($user->rights->fournisseur->facture->lire) {
				$total = $total + $objectlink->total_ttc;
				echo price($objectlink->total_ttc);
			} ?></td>
			<td align="right"><?php echo $objectlink->getLibStatut(3); ?></td>
			<td align="right"><a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=dellink&dellinkid='.$key; ?>"><?php echo img_delete($langs->transnoentitiesnoconv("RemoveLink")); ?></a></td>
		</tr>
		<?php
	}
	?>

	<!-- END PHP TEMPLATE -->
