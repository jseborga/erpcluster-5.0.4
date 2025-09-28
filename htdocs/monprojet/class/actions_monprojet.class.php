<?php


class ActionsMonprojet
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
		global $langs,$conf,$user,$items;
		global $objecttaskadd,$typeitem;
		$error = 0; // Error counter
		$myvalue = 'test'; // A result value
		// echo "action: " . $action;
		// print_r($parameters);exit;
		if ($parameters['newaction'] == 'ejecutetask')
		{
			// print '<tr>';
			// print '<td>';
			// print $langs->trans('Group');
			// print '</td>';
			// print '<td>';
			// print ($object->array_options['options_c_grupo']==-1?$langs->trans('Not'):($object->array_options['options_c_grupo']==1?$langs->trans('Yes'):""));
			// print '<input type="hidden"name="options_c_grupo" value="'.$object->array_options['options_c_grupo'].'">';
			// print '<input type="hidden"name="options_fk_unit" value="'.$object->array_options['options_fk_unit'].'">';
			// print '<input type="hidden"name="options_unit_program" value="'.$object->array_options['options_unit_program'].'">';
			// print '</td>';
			// print '</tr>';

			//units
			$unit = $objecttaskadd->getLabelOfUnit($conf->global->REQUEST_USE_SHORT?'short':'');

			print '<tr>';
			print '<td>';
			print $langs->trans('Unit');
			print '</td>';
			print '<td>';
			if ($unit !== '') {
				print $langs->trans($unit);
			}
			print '</td>';
			print '</tr>';


			print '<tr>';
			print '<td>';
			print $langs->trans('Unitprogramed');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_program);
			print '<input type="hidden"name="options_unit_program" value="'.$objecttaskadd->unit_program.'">';
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td>';
			print $langs->trans('Unitdeclared');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_declared);
			print '<input type="hidden"name="options_unit_declared" value="'.$objecttaskadd->unit_declared.'">';
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td>';
			print $langs->trans('Unitejecuted');
			print '</td>';
			print '<td>';
			print '<input type="number" min="0" step="any" name="options_unit_ejecuted" value="'.$objecttaskadd->unit_ejecuted.'">';
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td>';
			print $langs->trans('Detailclose');
			print '</td>';
			print '<td>';
			print '<textarea rows="2" cols="30" name="options_detail_close">';
			print $objecttaskadd->detail_close;
			print '</textarea>';
			print '</td>';
			print '</tr>';

			$res = 1;
			return 1;
		}
		if ($parameters['newaction'] == 'viewprogram')
		{
			//units
			$unit = $objecttaskadd->getLabelOfUnit($conf->global->REQUEST_USE_SHORT?'short':'');
			//units programed
			print '<tr>';
			print '<td>';
			print $langs->trans('Unitprogramed');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_program).' - '.($unit !=''?$langs->trans($unit):'');
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td>';
			print $langs->trans('Unitdeclared');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_declared);
			print '</td>';
			print '</tr>';

			$res = 1;
			return 1;
		}
		if ($parameters['newaction'] == 'view')
		{
			if ($conf->budget->enabled)
			{
				print '<tr><td>'.$langs->trans("Type").'</td><td>';
				print $typeitem->select_typeitem($objecttaskadd->fk_type,'fk_type','',0,1);
				print '</td></tr>';
			}
			if ($objecttaskadd->fk_item>0)
			{
				if ($conf->budget->enabled)
				{
					//buscamos
					$items->fetch($objecttaskadd->fk_item);
					if ($items->id == $objecttaskadd->fk_item)
					{
						print '<tr>';
						print '<td>';
						print $langs->trans('Item');
						print '</td>';
						print '<td>';
						print $items->detail;
						print '</td>';
						print '</tr>';
					}
				}
			}
			print '<tr>';
			print '<td>';
			print $langs->trans('Unitprogramed');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_program);
			print '</td>';
			print '</tr>';

			//units
			$unit = $objecttaskadd->getLabelOfUnit($conf->global->REQUEST_USE_SHORT?'short':'');

			print '<tr>';
			print '<td>';
			print $langs->trans('Unit');
			print '</td>';
			print '<td>';
			if ($unit !== '') {
				print $langs->trans($unit);
			}
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td>';
			print $langs->trans('Unitdeclared');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_declared);
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td>';
			print $langs->trans('Unitejecuted');
			print '</td>';
			print '<td>';
			print price($objecttaskadd->unit_ejecuted);
			print '</td>';
			print '</tr>';
			if ($user->rights->monprojet->task->leerm)
			{
				print '<tr>';
				print '<td>';
				print $langs->trans('Unitamount');
				print '</td>';
				print '<td>';
				print price($objecttaskadd->unit_amount);
				print '</td>';
				print '</tr>';
			}
			$res = 1;
			return 1;
		}


		if (in_array('somecontext', explode(':', $parameters['context'])))
		{
			print '<tr><td>asdfasdf</td></tr>';
		  // do something only for the context 'somecontext'
		}
		if (! $error)
		{
			$this->results = array('myreturn' => $myvalue);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
		if ($res == 1)
			return 1;

	}

	function formContactTpl($parameters, &$object, &$action, $hookmanager)
	{
		global $langs,$conf,$form,$formcompany,$userstatic;
		print '<h3>'.$langs->trans('WorksAndSuppliers').'</h3>';
		print '<div class="tagtable centpercent noborder allwidth">';
		$permission = $parameters['permission'];
		if (!empty($parameters['objectelement']))
			$object->element = $parameters['objectelement'];
		if ($permission) { 

			print '<form class="tagtr liste_titre">
			<div class="tagtd">'.$langs->trans("Nature").'</div>
			<div class="tagtd">'.$langs->trans("ThirdParty").'</div>
			<div class="tagtd">'.$langs->trans("Users").'/'.$langs->trans("Contacts").'</div>
			<div class="tagtd">'.$langs->trans("ContactType").'</div>
			<div class="tagtd">&nbsp;</div>
			<div class="tagtd">&nbsp;</div>
		</form>';

		$var=true;
		if (empty($hideaddcontactforuser))
		{
			$var=!$var;

			print '<form class="tagtr impair" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">
			<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />
			<input type="hidden" name="id" value="'.$object->id.'" />
			<input type="hidden" name="action" value="addcontact" />
			<input type="hidden" name="source" value="internal" />';
			if ($withproject) print '<input type="hidden" name="withproject" value="'.$withproject.'">'; 
			print '<div class="nowrap tagtd">'.img_object('','user').' '.$langs->trans("Users").'</div>
			<div class="tagtd">'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</div>
			<div class="tagtd maxwidthonsmartphone">'.$form->select_dolusers($user->id, 'userid', 0, (! empty($userAlreadySelected)?$userAlreadySelected:null), 0, null, null, 0, 56).'</div>
			<div class="tagtd maxwidthonsmartphone">';

				$tmpobject=$object;
				if ($object->element == 'shipping' && is_object($objectsrc)) $tmpobject=$objectsrc;
				if ($parameters['objectelement'])
				{
					$objectsrc = new stdClass();
					$objectsrc->element = $parameters['objectelement'];
					$tmpobject=$objectsrc;
				}
				print_r($tmpobject);
				echo $formcompany->selectTypeContact($tmpobject, '', 'type','internal'); 
				print '</div>';
				print '<div class="tagtd">&nbsp;</div>
				<div class="tagtd" align="right"><input type="submit" class="button" value="'.$langs->trans("Add").'"></div>
			</form>';

		}

		if (empty($hideaddcontactforthirdparty))
		{
			$var=!$var;

			print '<form class="tagtr pair" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">
			<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />
			<input type="hidden" name="id" value="'.$object->id.'" />
			<input type="hidden" name="action" value="addcontact" />
			<input type="hidden" name="source" value="external" />';
			if ($withproject) print '<input type="hidden" name="withproject" value="'.$withproject.'">';
			print '<div class="tagtd nowrap">'.img_object('','contact').' '.$langs->trans("ThirdPartyContacts").'</div>
			<div class="tagtd nowrap maxwidthonsmartphone">';
				$selectedCompany = isset($_GET["newcompany"])?$_GET["newcompany"]:$object->socid;
	// add company icon for direct link 
				if ($selectedCompany && empty($conf->dol_use_jmobile)) 
				{
					$companystatic->fetch($selectedCompany);
					echo $companystatic->getNomUrl(2, '', 0, 1); 
				}
				$selectedCompany = $formcompany->selectCompaniesForNewContact($object, 'id', $selectedCompany, 'newcompany', '', 0);
				print '</div>';
				print '<div class="tagtd maxwidthonsmartphone">';
				$nbofcontacts=$form->select_contacts($selectedCompany, '', 'contactid', 0, '', '', 0, 'minwidth200');
				print '</div>
				<div class="tagtd maxwidthonsmartphone">';
					$tmpobject=$object;
					if ($object->element == 'shipping' && is_object($objectsrc)) $tmpobject=$objectsrc;
					$formcompany->selectTypeContact($tmpobject, '', 'type','external');
					print '</div>
					<div class="tagtd">&nbsp;</div>
					<div  class="tagtd" align="right">';
						print '<input type="submit" id="add-customer-contact" class="button" value="'.$langs->trans("Add").'"'.(!$nbofcontacts?' disabled':' ').'>';
						print '</div>
					</form>';
				}
			} 

			print '<form class="tagtr liste_titre liste_titre_add formnoborder">';
			print '<div class="tagtd">'.$langs->trans("Nature").'</div>';
			print '<div class="tagtd">'.$langs->trans("ThirdParty").'</div>';
			print '<div class="tagtd">'.$langs->trans("Users").'/'.$langs->trans("Contacts").'</div>';
			print '<div class="tagtd">'.$langs->trans("ContactType").'</div>';
			print '<div class="tagtd" align="center">'.$langs->trans("Status").'</div>';
			print '<div class="tagtd">&nbsp;</div>';
			print '</form>';

			$var=true;
			$arrayofsource=array('internal','external');	
	// Show both link to user and thirdparties contacts
			foreach($arrayofsource as $source) {

				$tmpobject=$object;
				if ($object->element == 'shipping' && is_object($objectsrc)) $tmpobject=$objectsrc;

				$tab = $tmpobject->liste_contact(-1,$source);
				$num=count($tab);

				$i = 0;
				while ($i < $num) {
					$var = !$var;

					print '<form class="tagtr '.($var?"pair":"impair").'">';
					print '<div class="tagtd" align="left">';
					if ($tab[$i]['source']=='internal') echo $langs->trans("User");
					if ($tab[$i]['source']=='external') echo $langs->trans("ThirdPartyContact");
					print '</div>';
					print '<div class="tagtd" align="left">';
					if ($tab[$i]['socid'] > 0)
					{
						$companystatic->fetch($tab[$i]['socid']);
						echo $companystatic->getNomUrl(1);
					}
					if ($tab[$i]['socid'] < 0)
					{
						echo $conf->global->MAIN_INFO_SOCIETE_NOM;
					}
					if (! $tab[$i]['socid'])
					{
						echo '&nbsp;';
					}
					print '</div>';
					print '<div class="tagtd">';
					$statusofcontact = $tab[$i]['status'];

					if ($tab[$i]['source']=='internal')
					{
						$userstatic->id=$tab[$i]['id'];
						$userstatic->lastname=$tab[$i]['lastname'];
						$userstatic->firstname=$tab[$i]['firstname'];
						echo $userstatic->getNomUrl(1);
					}
					if ($tab[$i]['source']=='external')
					{
						$contactstatic->id=$tab[$i]['id'];
						$contactstatic->lastname=$tab[$i]['lastname'];
						$contactstatic->firstname=$tab[$i]['firstname'];
						echo $contactstatic->getNomUrl(1);
					}
					print '</div>';
					print '<div class="tagtd">'.$tab[$i]['libelle'].'</div>';
					print '<div class="tagtd" align="center">';
	//if ($object->statut >= 0) echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=swapstatut&amp;ligne='.$tab[$i]['rowid'].'">';
					if ($tab[$i]['source']=='internal')
					{
						$userstatic->id=$tab[$i]['id'];
						$userstatic->lastname=$tab[$i]['lastname'];
						$userstatic->firstname=$tab[$i]['firstname'];
						echo $userstatic->LibStatut($tab[$i]['statuscontact'],3);
					}
					if ($tab[$i]['source']=='external')
					{
						$contactstatic->id=$tab[$i]['id'];
						$contactstatic->lastname=$tab[$i]['lastname'];
						$contactstatic->firstname=$tab[$i]['firstname'];
						echo $contactstatic->LibStatut($tab[$i]['statuscontact'],3);
					}

			//if ($object->statut >= 0) echo '</a>'; 
					print '</div>';
					print '<div class="tagtd nowrap" align="right">';
					if ($permission) { 
						print '&nbsp;<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deletecontact&amp;lineid='.$tab[$i]['rowid'].'">'.img_delete().'</a>';
					}
					print '</div>';
					print '</form>';

					$i++;
				}
			}

			print '</div>';

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
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		
		global $conf,$langs,$form,$user;
		global $objecttaskadd,$typeitem,$items,$formadd;
		$error = 0; 
		$myvalue = 'test'; 
		if ($parameters['newaction'] == 'addextra')
		{
		  // //tareas internas o externas
		  // print '<tr><td>'.$langs->trans("Internaltask").'</td><td>';
		  // print $form->selectyesno('options_c_view',$objecttaskadd->c_view,0);
		  // print '</td></tr>';

		  //opcional select item
			if ($conf->budget->enabled && $abc)
			{
				print '<tr><td>'.$langs->trans("Item").'</td><td>';
				if ($objecttaskadd->fk_item>0)
				{
					$items->fetch($objecttaskadd->fk_item);
					if ($items->id == $objecttaskadd->fk_item)
						$refite = $items->ref;
				}
				print $form->select_items_v($objecttaskadd->fk_item, 'ref', '', 0, 0, 1, 2, '', 1, array(),0,'');
				//print $formadd->select_item(($refite?$refite:$_POST['ref_item']),'ref_item',$filter,1,0,0,'','',0,0,0);
				print '</td></tr>';

				print '<tr><td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
				print $typeitem->select_typeitem($objecttaskadd->fk_type,'fk_type','',1);
				print '</td></tr>';
			}
		  //opcional select item
		//print '<tr><td>'.$langs->trans("Item").'</td><td>';
		//if ($objecttaskadd->fk_item>0)
		//{
		//	$items->fetch($objecttaskadd->fk_item);
		//	if ($items->id == $objecttaskadd->fk_item)
		//		$refite = $items->ref;
		//}
		//print $formadd->select_item(($refite?$refite:$_POST['ref_item']),'ref_item',$filter,1,0,0,'','',0,0,0);
		//print '</td></tr>';


		  // print '<tr>';
		  // print '<td class="fieldrequired">';
		  // print $langs->trans('Group');
		  // print '</td>';
		  // print '<td>';
		  // print $form->selectyesno('options_c_grupo',$object->array_options['options_c_grupo'],1,'');
		  // print '</td>';
		  // print '</tr>';
		  //unidades programadas
			print '<tr>';
			print '<td class="fieldrequired">';
			print $langs->trans('Unitprogramed');
			print '</td>';
			print '<td>';
			print '<input type="number" min="0" step="any" name="options_unit_program" value="'.$objecttaskadd->unit_program.'" required>';
			print '</td>';
			print '</tr>';

		  // // Units
		  // if($conf->global->PRODUCT_USE_UNITS && empty($parameters['newsel']))
		  //   {
			print '<tr><td class="fieldrequired">'.$langs->trans('Unit').'</td>';
			print '<td colspan="3">';
			print $form->selectUnits($objecttaskadd->fk_unit,'options_fk_unit');
			print '</td></tr>';
		  //		      }

		  //valor unitario del item
			if ($user->rights->monprojet->task->addm)
			{
				print '<tr>';
				print '<td class="fieldrequired">';
				print $langs->trans('Unitamount');
				print '</td>';
				print '<td>';
				print '<input type="number" min="0" step="any" required="required" name="options_unit_amount" value="'.$objecttaskadd->unit_amount.'">';
				print '</td>';
				print '</tr>';
			}
			return 1;
		}

		//addbudget
		if ($parameters['newaction'] == 'addbudget')
		{
			//opcional select item
			if ($conf->budget->enabled)
			{
				print '<tr><td>'.$langs->trans("Item").'</td><td>';
				if ($objecttaskadd->fk_item>0)
				{
					$items->fetch($objecttaskadd->fk_item);
					if ($items->id == $objecttaskadd->fk_item)
						$refite = $items->ref;
				}
			//print $formadd->select_item(($refite?$refite:$_POST['ref_item']),'ref_item',$filter,1,0,0,'','',0,0,0);
			//print $form->select_produits_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='')
				$filterstatic = '';
				$filtercat = $conf->global->PRICEUNITS_CODE_ITEM_DEF;
			//print 
			//$form->select_produits_v($fk_product,'idprod','',$conf->product->limit_size,0,1,2,'',1,'','','',$filterstatic,$filtercat);
				$form->select_produits('','idprod',$filtertype,$conf->product->limit_size,$buyer->price_level,-1,2,'',1);
				print '</td></tr>';
			}
		  //unidades budget
			print '<tr>';
			print '<td class="fieldrequired">';
			print $langs->trans('Unitprogramed');
			print '</td>';
			print '<td>';
			print '<input type="number" min="0" step="any" name="options_unit_budget" value="'.$objecttaskadd->unit_budget.'" required>';
			print '</td>';
			print '</tr>';

		  //valor unitario del item
			if ($user->rights->monprojet->budi->addm)
			{
				print '<tr>';
				print '<td class="fieldrequired">';
				print $langs->trans('Unitamount');
				print '</td>';
				print '<td>';
				print '<input type="number" min="0" step="any" required="required" name="options_unit_budget_amount" value="'.$objecttaskadd->unit_budget_amount.'">';
				print '</td>';
				print '</tr>';
			}
			return 1;
		}
		if ($parameters['currentcontext'] == 'projectcard' && ($action == 'create' || $action == 'edit'))
		{
			global $db,$conf;
			if (!$conf->global->MONPROJET_USE_EXTRAFIELD_DEFAULT)
			{
				$langs->load('monprojet@monprojet');
				$array = array(0=>$langs->trans('Withoutstock'),1=>$langs->trans('Withwarehouse'),2=>$langs->trans('Directmovement'));
				require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
				$objectadd = new Projetadd($db);
				if ($object->id>0)
					$objectadd->fetch(0,$object->id);
    		//agregamos los campos adicionales del proyecto
				print '<tr><td class="tdtop">'.$langs->trans("Requiresprogramming").'</td>';
				print '<td>';
				print $form->selectyesno('programmed',(GETPOST('programmed')?GETPOST('programmed'):$objectadd->programmed),1);
				print '</td></tr>';

				print '<tr><td class="tdtop">'.$langs->trans("Contractor").'</td>';
				print '<td>';
				print $form->select_company((GETPOST('fk_contracting')?GETPOST('fk_contracting'):$objectadd->fk_contracting),'fk_contracting','',1,1);
				print '</td></tr>';

				print '<tr><td class="tdtop">'.$langs->trans("Supervision").'</td>';
				print '<td>';
				print $form->select_company((GETPOST('fk_supervising')?GETPOST('fk_supervising'):$objectadd->fk_supervising),'fk_supervising','',1,1);
				print '</td></tr>';

				print '<tr><td class="tdtop">'.$langs->trans("Useresource").'</td>';
				print '<td>';
				print $form->selectarray('use_resource',$array,(GETPOST('use_resource')?GETPOST('use_resource'):$objectadd->use_resource),0,0);
				print '</td></tr>';
				return 1;
			}
			else
				return 0;
		}

		if ($parameters['currentcontext'] == 'projectcard' && (empty($action) || ($action != 'create' && $action != 'edit')))
		{
			global $db,$conf;
			if (!$conf->global->MONPROJET_USE_EXTRAFIELD_DEFAULT)
			{
				$langs->load('monprojet@monprojet');
				$array = array(0=>$langs->trans('Withoutstock'),1=>$langs->trans('Withwarehouse'),2=>$langs->trans('Directmovement'));
				require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
				require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
				$societe = new Societe($db);
				$objectadd = new Projetadd($db);
				$res = $objectadd->fetch(0,$object->id);
    		//agregamos los campos adicionales del proyecto
				print '<tr><td class="tdtop">'.$langs->trans("Requiresprogramming").'</td>';
				print '<td>';
				print ($objectadd->programmed?$langs->trans('Yes'):$langs->trans('No'));
				print '</td></tr>';

				print '<tr><td class="tdtop">'.$langs->trans("Contractor").'</td>';
				print '<td>';
				if ($objectadd->fk_contracting>0)
				{
					$societe->fetch($objectadd->fk_contracting);
					print $societe->getNomUrl(1);
				}
				print '</td></tr>';

				print '<tr><td class="tdtop">'.$langs->trans("Supervision").'</td>';
				print '<td>';
				if ($objectadd->fk_supervising>0)
				{
					$societe->fetch($objectadd->fk_supervising);
					print $societe->getNomUrl(1);
				}
				print '</td></tr>';
				print '<tr><td class="tdtop">'.$langs->trans("Useresource").'</td>';
				print '<td>';
				print $array[$objectadd->use_resource];
				print '</td></tr>';

				return 1;
			}
			else
				return 0;
		}
		if (in_array('somecontext', explode(':', $parameters['context'])))
		{
			print '<tr><td>asdfasdf</td></tr>';
		  // do something only for the context 'somecontext'
		}
		if (! $error)
		{
			$this->results = array('myreturn' => $myvalue);
			$this->resprints = 'A text to show';
		  return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
	}

}
?>