<?php

class ActionsSales
{
	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0;
		// Error counter
		$myvalue = 'test';
		// A result value
		global $langs;
		//print_r($parameters);
		//echo "action: " . $action;
		//print_r($object);

		if (in_array('propalcard0', explode(':', $parameters['context'])))
		{
			// do something only for the context 'somecontext'
			print '<td>'.$langs->trans('adentro').'</td><td><input type="text" name="xyz" value=""></td>';
		}

		if (! $error)
		{
			$this->results = array('myreturn' => $myvalue);
			$this->resprints = 'A text to show';
			return 0;
			// or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
	}

	function showLinkedObjectBlock($parameters, &$object, &$action, $hookmanager)
	{
		global $conf,$langs,$hookmanager;
		global $bc,$db;
		$num = count($object->linkedObjects);

		$numoutput=0;

		foreach((array) $object->linkedObjects as $objecttype => $objects)
		{
			$tplpath = $element = $subelement = $objecttype;
			if ($objecttype != 'supplier_proposal' && preg_match('/^([^_]+)_([^_]+)/i',$objecttype,$regs))
			{
				$element = $regs[1];
				$subelement = $regs[2];
				$tplpath = $element.'/'.$subelement;
			}
			$tplname='linkedobjectblock';

				// To work with non standard path
			if ($objecttype == 'facture')          {
				$tplpath = 'sales/compta/'.$element;
				if (empty($conf->facture->enabled)) continue;
				//if ($conf->sales->enabled)
				//{
				//	require_once DOL_DOCUMENT_ROOT.'/sales/class/factureext.class.php';
				//	$objtmp1 = new Factureext($db);
				//	$objtmp1->fetch($obj->id);
				//	$objects[$j] = $objtmp1;
					// Do not show if module disabled
				//}
			}
			else if ($objecttype == 'facturerec')          {
				$tplpath = 'compta/facture';
				$tplname = 'linkedobjectblockForRec';
				if (empty($conf->facture->enabled)) continue;
					// Do not show if module disabled
			}
			else if ($objecttype == 'propal')           {
				$tplpath = 'comm/'.$element;
				$tplpath = 'sales/'.$element;
				$objtmp = $objects;
				foreach ((array) $objtmp AS $j => $obj)
				{
					require_once DOL_DOCUMENT_ROOT.'/sales/class/propalext.class.php';
					$objtmp1 = new Propalext($db);
					$objtmp1->fetch($obj->id);
					$objects[$j] = $objtmp1;
				}

				if (empty($conf->propal->enabled)) continue;
					// Do not show if module disabled
			}
			else if ($objecttype == 'commande')           {
				$tplpath = 'commande/'.$element;
				$tplpath = 'sales/commande/'.$element;
				$objtmp = $objects;
				foreach ((array) $objtmp AS $j => $obj)
				{
					require_once DOL_DOCUMENT_ROOT.'/sales/class/commandeext.class.php';
					$objtmp1 = new Commandeext($db);
					$objtmp1->fetch($obj->id);
					$objects[$j] = $objtmp1;
				}
				if (empty($conf->commande->enabled)) continue;
					// Do not show if module disabled
			}
			else if ($objecttype == 'supplier_proposal')           {
				if (empty($conf->supplier_proposal->enabled)) continue;
					// Do not show if module disabled
			}
			else if ($objecttype == 'shipping' || $objecttype == 'shipment') {
				$tplpath = 'expedition';
				if (empty($conf->expedition->enabled)) continue;
					// Do not show if module disabled
			}
			else if ($objecttype == 'delivery')         {
				$tplpath = 'livraison';
				if (empty($conf->expedition->enabled)) continue;
					// Do not show if module disabled
			}
			else if ($objecttype == 'invoice_supplier') {
				$tplpath = 'purchase/facture';
			}
			else if ($objecttype == 'order_supplier')   {
				$tplpath = 'purchase/commande';
			}
			else if ($objecttype == 'expensereport')   {
				$tplpath = 'expensereport';
			}
			else if ($objecttype == 'subscription')   {
				$tplpath = 'adherents';
			}
			else if ($objecttype == 'fichinter')
			{
				$tplpath = 'sales/fichinter';
				$objtmp = $objects;
				foreach ((array) $objtmp AS $j => $obj)
				{
					require_once DOL_DOCUMENT_ROOT.'/sales/class/fichinterext.class.php';
					$objtmp1 = new Fichinterext($db);
					$objtmp1->fetch($obj->id);
					if ($objtmp1->id == $obj->id)
					{
						$objects[$j] = $objtmp1;
					}
				}
				//if (empty($conf->fichinter->enabled)) continue;
			}
			else if ($objecttype == 'contrat') {
				$tplpath = 'sales/contrat';
				$objtmp = $objects;
				foreach ((array) $objtmp AS $j => $obj)
				{
					require_once DOL_DOCUMENT_ROOT.'/sales/class/contratext.class.php';
					$objtmp1 = new Contratext($db);
					$objtmp1->fetch($obj->id);
					$objects[$j] = $objtmp1;
				}
			}

			global $linkedObjectBlock;
			$linkedObjectBlock = $objects;

			if (empty($numoutput))
			{
				$numoutput++;

				print '<br>';
				print load_fiche_titre($langs->trans('RelatedObjects'), '', '');

				print '<table class="noborder allwidth">';

				print '<tr class="liste_titre">';
				print '<td>'.$langs->trans("Type").'</td>';
				print '<td>'.$langs->trans("Ref").'</td>';
				print '<td align="center"></td>';
				print '<td align="center">'.$langs->trans("Date").'</td>';
				print '<td align="right">'.$langs->trans("AmountHTShort").'</td>';
				print '<td align="right">'.$langs->trans("Status").'</td>';
				print '<td></td>';
				print '</tr>';
			}

				// Output template part (modules that overwrite templates must declare this into descriptor)
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/'.$tplpath.'/tpl'));
			foreach($dirtpls as $reldir)
			{
				$res=@include dol_buildpath($reldir.'/'.$tplname.'.tpl.php');
				if ($res) break;
			}
		}

		if ($numoutput)
		{
			print '</table>';
		}

		return 1;
	}
}
?>