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
if (! empty($conf->margin->enabled) && ! empty($object->element) && in_array($object->element,array('facture','propal','commande','mant'))) $usemargins=1;
$formass = new assetsext($db);
//$formass = new Form($db);
?>

<!-- BEGIN PHP TEMPLATE predefinedassetline_create.tpl.php -->


<form name="addpredefinedasset" id="addpredefinedasset" action="<?php echo $_SERVER["PHP_SELF"].'?id='.$this->id; ?>#add" method="POST">
	<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
	<input type="hidden" name="action" value="addline">
	<input type="hidden" name="mode" value="predefined">
	<input type="hidden" name="id" value="<?php echo $this->id; ?>">

	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#fk_equipment').change(function() {
				if (jQuery('#fk_equipment').val() > 0) jQuery('#np_desc').focus();
			});
		});
	</script>

	<?php

	echo '<span>';
	$filtertype='';
	if (! empty($object->element) && $object->element == 'contrat') 
		$filtertype='1';
//$formass->select_produits('','fk_equipment',$filtertype,30,0,1);
	$formass->select_assets_line('','fk_equipment',$filtertype,30,0,1);

	echo '</span>';

	if (is_object($hookmanager))
	{
		$parameters=array('fk_parent_line'=>GETPOST('fk_parent_line','int'));
		$reshook=$hookmanager->executeHooks('formCreateAssetsOptions',$parameters,$object,$action);
	}
	?>
</form>

<?php
if (! empty($usemargins)) 
{
	?>
	<script type="text/javascript">
		$("#fk_equipment").change(function() {
			$("#fournprice options").remove();
			$("#fournprice").hide();
			$("#buying_price").val("").show();
			$.post('<?php echo DOL_URL_ROOT; ?>/assets/ajax/getSupplier.php', {'fk_equipment': $(this).val()}, function(data) {
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
