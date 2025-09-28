<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Christophe Battarel	<christophe.battarel@altairis.fr>
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
 *
 * Need to have following variables defined:
 * $conf
 * $langs
 * $dateSelector
 * $this (invoice, order, ...)
 * $line defined
 */

$usemargins=0;
if (! empty($conf->margin->enabled) && ! empty($object->element) && in_array($object->element,array('facture','propal','commande'))) $usemargins=1;

?>

<!-- BEGIN PHP TEMPLATE predefinedproductline_create_son.tpl.php -->

<tr class="liste_titre nodrag nodrop">
	<td<?php echo (! empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="4"' : ' colspan="3"'); ?>>
	<?php
	echo $langs->trans('Productson');
	?>
</td>
<td align="right"><?php echo $langs->trans('Qty'); ?></td>

<td colspan="<?php echo $colspan; ?>">&nbsp;</td>
</tr>

<form name="addpredefinedproduct" id="addpredefinedproduct" action="<?php echo $_SERVER["PHP_SELF"].''; ?>#add" method="POST">
	<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
	<input type="hidden" name="action" value="addlinesson">
	<input type="hidden" name="mode" value="predefined">
	<input type="hidden" name="id" value="<?php print $object->id;?>">
	<input type="hidden" name="qty_father" value="<?php print $object->qty_father;?>">

	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#idprod').change(function() {
				if (jQuery('#idprod').val() > 0) jQuery('#np_desc').focus();
			});
		});
	</script>

	<tr <?php echo $bcnd[$var]; ?> >
		<td align="left" <?php echo (! empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="4"' : ' colspan="3"'); ?>>
			<?php

			echo '<span>';
			$filtertype='';
			if (! empty($object->element) && $object->element == 'contrat') $filtertype='1';
			//$form->select_produits_v('','idprod1',$filtertype,$conf->product->limit_size,$buyer->price_level,-1);
			$form->select_produits_v('', 'idprod1', $filtertype, $conf->product->limit_size, $buyer->price_level, -1, '', '', 0, array(),0,'');
			echo '</span>';

			if (is_object($hookmanager))
			{
				$parameters=array('fk_parent_line'=>GETPOST('fk_parent_line','int'));
				$reshook=$hookmanager->executeHooks('formCreateProductOptions',$parameters,$object,$action);
			}

			?>
		</td>
			<?php
				//print '<td align="left">';

			//$liste = $objunits->liste_array();
	  //print $form->selectarray('fk_unit_son',$liste,$conf->global->COMMANDE_ADDON_PDF);
			//print select_unit($object->fk_unit,'fk_unit_son','',1,0,'rowid','label');
			//print '</td>';

			?>
		<td align="right"><input type="number" min="0" step="any" name="qty_son" value="1"></td>
		<?php
		$colspan = 4;
		if (! empty($usemargins))
		{
			if (! empty($conf->global->DISPLAY_MARGIN_RATES)) $colspan++;
			if (! empty($conf->global->DISPLAY_MARK_RATES))   $colspan++;
			?>
			<td align="right">
				<select id="fournprice" name="fournprice" style="display: none;"></select>
				<input type="text" size="5" id="buying_price" name="buying_price" value="<?php echo (isset($_POST["buying_price"])?$_POST["buying_price"]:''); ?>">
			</td>
			<?php
		}
		?>
		<td align="center" valign="middle" colspan="<?php echo $colspan; ?>">
			<input type="submit" class="button" value="<?php echo $langs->trans("Add"); ?>" name="addline">
		</td>
	</tr>


</form>

<?php
if (! empty($usemargins))
{
	?>
	<script type="text/javascript">
		$("#idprod").change(function() {
			$("#fournprice options").remove();
			$("#fournprice").hide();
			$("#buying_price").val("").show();
			$.post('<?php echo DOL_URL_ROOT; ?>/fourn/ajax/getSupplierPrices.php', {'idprod': $(this).val()}, function(data) {
				if (data && data.length > 0) {
					var options = '';
					var i = 0;
					$(data).each(function() {
						i++;
						options += '<option value="'+this.id+'" price="'+this.price+'"';
						if (i == 1) {
							options += ' selected';
							$("#buying_price").val(this.price);
						}
						options += '>'+this.label+'</option>';
					});
					options += '<option value=null><?php echo $langs->trans("InputPrice"); ?></option>';
					$("#buying_price").hide();
					$("#fournprice").html(options).show();
					$("#fournprice").change(function() {
						var selval = $(this).find('option:selected').attr("price");
						if (selval)
							$("#buying_price").val(selval).hide();
						else
							$('#buying_price').show();
					});
				}
			},
			'json');
		});
	</script>
	<?php
}
?>
<!-- END PHP TEMPLATE predefinedproductline_create.tpl.php -->
