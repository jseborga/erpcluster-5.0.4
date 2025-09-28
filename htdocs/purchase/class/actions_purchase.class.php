<?php

class ActionsPurchase
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
	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function doActionsz($parameters, &$object, &$action, $hookmanager)
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
	function showLinkedObjectBlockfractal($parameters, &$object, &$action, $hookmanager)
	{
		global $conf,$langs,$hookmanager;
		global $bc,$db;
		$num = count($object->listObject);

		$numoutput=0;

		foreach((array) $object->listObject as $i => $objects)
		{
			$tplpath = $element = $subelement = $objecttype;

			if ($object->type_element == 'target')
			{
				if ($objects->sourcetype=='purchaserequest')
				{
					$tplpath = 'purchase';
					require_once DOL_DOCUMENT_ROOT.'/purchase/class/purchaserequestext.class.php';
					$objtmp1 = new Purchaserequestext($db);
					$objtmp1->fetch($objects->fk_source);
					$objects= $objtmp1;
				}
			}
			else
			{

			}
			global $linkedObjectBlock;
			$linkedObjectBlock[$j] = $objects;
			$tplname = 'linkedobjectblock';
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