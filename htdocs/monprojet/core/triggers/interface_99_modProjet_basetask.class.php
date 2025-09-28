<?php
/* Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2014-2014 Ramiro Queso        <ramiroques@gmail.com>
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
 *  \file       htdocs/core/triggers/interface_90_modProjet_basetask.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du projet
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *				                           ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */


/**
 *  Class of triggers for demo module
 */
class InterfaceBasetask
{
	var $db;
	
	/**
	 *   Constructor
	 *
	 *   @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i','',get_class($this));
		$this->family = "demo";
		$this->description = "Triggers of this module are empty functions. They have no effect. They are provided for tutorial purpose only.";
		$this->version = 'dolibarr';            // 'development', 'experimental', 'dolibarr' or version
		$this->picto = 'technic';
	}
	
	
	/**
	 *   Return name of trigger file
	 *
	 *   @return     string      Name of trigger file
	 */
	function getName()
	{
		return $this->name;
	}
	
	/**
	 *   Return description of trigger file
	 *
	 *   @return     string      Description of trigger file
	 */
	function getDesc()
	{
		return $this->description;
	}

	/**
	 *   Return version of trigger file
	 *
	 *   @return     string      Version of trigger file
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'development') return $langs->trans("Development");
		elseif ($this->version == 'experimental') return $langs->trans("Experimental");
		elseif ($this->version == 'dolibarr') return DOL_VERSION;
		elseif ($this->version) return $this->version;
		else return $langs->trans("Unknown");
	}
	
	/**
	 *      Function called when a Dolibarrr business event is done.
	 *      All functions "run_trigger" are triggered if file is inside directory htdocs/core/triggers
	 *
	 *      @param	string		$action		Event action code
	 *      @param  Object		$object     Object
	 *      @param  User		$user       Object user
	 *      @param  Translate	$langs      Object langs
	 *      @param  conf		$conf       Object conf
	 *      @return int         			<0 if KO, 0 if no triggered ran, >0 if OK
	 */
	function run_trigger($action,$object,$user,$langs,$conf)
	{
		// Put here code you want to execute when a Dolibarr business events occurs.
		// Data and type of action are stored into $object and $action
		// Users
		if ($action == 'USER_LOGIN')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_UPDATE_SESSION')
		{
			// Warning: To increase performances, this action is triggered only if 
			// constant MAIN_ACTIVATE_UPDATESESSIONTRIGGER is set to 1.
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_CREATE_FROM_CONTACT')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_NEW_PASSWORD')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_ENABLEDISABLE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_LOGOUT')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_SETINGROUP')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'USER_REMOVEFROMGROUP')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Groups
		elseif ($action == 'GROUP_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'GROUP_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'GROUP_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Companies
		elseif ($action == 'COMPANY_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'COMPANY_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'COMPANY_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Contacts
		elseif ($action == 'CONTACT_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CONTACT_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CONTACT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Products
		elseif ($action == 'PRODUCT_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PRODUCT_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PRODUCT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Customer orders
		elseif ($action == 'ORDER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_CLONE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_BUILDDOC')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SENTBYMAIL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_CLASSIFY_BILLED')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEORDER_INSERT')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEORDER_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEORDER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Supplier orders
		elseif ($action == 'ORDER_SUPPLIER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_CLONE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}      
		elseif ($action == 'ORDER_SUPPLIER_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_DELETE')
		{   
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_APPROVE')
		{   
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_REFUSE')
		{   
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_CANCEL')
		{   
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_SENTBYMAIL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ORDER_SUPPLIER_BUILDDOC')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEORDER_SUPPLIER_DISPATCH')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEORDER_SUPPLIER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEORDER_SUPPLIER_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Proposals
		elseif ($action == 'PROPAL_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_CLONE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_BUILDDOC')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_SENTBYMAIL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_CLOSE_SIGNED')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_CLOSE_REFUSED')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROPAL_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEPROPAL_INSERT')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEPROPAL_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEPROPAL_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Contracts
		elseif ($action == 'CONTRACT_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CONTRACT_ACTIVATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CONTRACT_CANCEL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CONTRACT_CLOSE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CONTRACT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINECONTRACT_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINECONTRACT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Bills
		elseif ($action == 'BILL_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_CLONE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_UNVALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_BUILDDOC')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_SENTBYMAIL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_CANCEL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_PAYED')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEBILL_INSERT')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEBILL_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEBILL_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		//Supplier Bill
		elseif ($action == 'BILL_SUPPLIER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_SUPPLIER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_SUPPLIER_PAYED')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_SUPPLIER_UNPAYED')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'BILL_SUPPLIER_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEBILL_SUPPLIER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEBILL_SUPPLIER_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEBILL_SUPPLIER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Payments
		elseif ($action == 'PAYMENT_CUSTOMER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PAYMENT_SUPPLIER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PAYMENT_ADD_TO_BANK')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PAYMENT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Interventions
		elseif ($action == 'FICHINTER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'FICHINTER_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'FICHINTER_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'FICHINTER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEFICHINTER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEFICHINTER_UPDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'LINEFICHINTER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Members
		elseif ($action == 'MEMBER_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'MEMBER_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'MEMBER_SUBSCRIPTION')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'MEMBER_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'MEMBER_NEW_PASSWORD')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'MEMBER_RESILIATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'MEMBER_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Categories
		elseif ($action == 'CATEGORY_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CATEGORY_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'CATEGORY_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Projects
		elseif ($action == 'PROJECT_CREATE')
		{
			global $mysoc;
			if ($conf->almacen->enabled)
			{
				include_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
				include_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
				$error=0;
				$entrepot = new Entrepot($this->db);
				$entrepotrel = new Entrepotrelationext($this->db);
				$entrepot->ref = $object->ref;
				$entrepot->libelle = $object->title;
				$entrepot->entity = $object->entity;
				$entrepot->description = $object->title;
				$entrepot->lieu = $object->title;
				$entrepot->pays_id = $mysoc->id;
				$entrepot->country_id = $mysoc->country_id;
				$entrepot->datec = dol_now();
				$entrepot->tms = dol_now();
				$entrepot->statut = 1;

				$entrepotrel->fk_entrepot = $_POST['fk_entrepot'];
				$entrepotrel->fk_entrepot_father = -1;
				$entrepotrel->fk_projet = $object->id;
				$entrepotrel->tipo = 'almacen';
				$entrepotrel->model_pdf = 'inventario';
				if ($entrepot->libelle) 
				{

					$fk = $entrepot->create($user);
					if ($fk > 0)
					{
						$entrepotrel->rowid = $fk;
						$resent = $entrepotrel->create($user);
						if ($resent <0)
						{
							$error++;
							setEventMessages($entrepotrel->error,$entrepotrel->errors,'errors');
						}
					}
					else
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
				}
				else 
				{
					$error++;
					setEventMessages($langs->trans("ErrorWarehouseRefRequired"),null,'errors');
				}
			}
			//creamos la tabla adicional de proyectos
			if ($conf->monprojet->enabled)
			{
				$now = dol_now();
				require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
				$objectadd = new Projetadd($this->db);
				$objectadd->fk_projet 		= $object->id;
				$objectadd->fk_entrepot		= $fk;
				$objectadd->programmed 		= GETPOST('programmed')+0;
				$objectadd->fk_contracting 	= GETPOST('fk_contracting')+0;
				$objectadd->fk_supervising 	= GETPOST('fk_supervising')+0;
				$objectadd->use_resource 	= GETPOST('use_resource')+0;
				$objectadd->origin 			= GETPOST('origin');
				$objectadd->originid 		= GETPOST('originid')+0;
				$objectadd->fk_user_create 	= $user->id;
				$objectadd->fk_user_mod 	= $user->id;
				$objectadd->date_create 	= $now;
				$objectadd->date_mod 		= $now;
				$objectadd->tms 			= $now;
				$objectadd->status 			= 1;

				$res = $objectadd->create($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
				}
			}	
			if (!$error) return 1;
			else return $error*-1;
			//dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROJECT_MODIFY')
		{
			//creamos la tabla adicional de proyectos
			if ($conf->monprojet->enabled)
			{
				require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
				$objectadd = new Projetadd($this->db);
				$res = $objectadd->fetch(0,$object->id);
				if ($res>0 && $objectadd->fk_entrepot <=0)
				{
					if ($conf->almacen->enabled)
					{
						//buscamos en entrepot relation
						require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
						$entrepotrel = new Entrepotrelationext($this->db);
						$filterstatic = " AND t.fk_projet = ".$object->id;
						$reser = $entrepotrel->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
						if ($reser==1)
						{
							$objectadd->fk_entrepot = $entrepotrel->id;
						}
						else
						{
							foreach($entrepotrel->lines AS $j => $line)
							{
								if ($objectadd->fk_entrepot<=0) $objectadd->fk_entrepot = $line->id;
							}
						}
					}
					$objectadd->fk_entrepot+=0;
				}
				if ($objectadd->fk_projet == $object->id)
				{
					$objectadd->programmed 		= GETPOST('programmed')+0;
					$objectadd->fk_contracting 	= GETPOST('fk_contracting')+0;
					$objectadd->fk_supervising 	= GETPOST('fk_supervising')+0;
					$objectadd->use_resource 	= GETPOST('use_resource')+0;
					$objectadd->fk_user_mod 	= $user->id;
					$objectadd->date_mod 		= dol_now();
					$objectadd->tms 			= dol_now();
					$objectadd->status 			= 1;
				}
				if ($res == 0)
				{
					$objectadd->programmed 		= GETPOST('programmed')+0;
					$objectadd->fk_contracting 	= GETPOST('fk_contracting')+0;
					$objectadd->fk_supervising 	= GETPOST('fk_supervising')+0;
					$objectadd->use_resource 	= GETPOST('use_resource')+0;
					$objectadd->fk_user_mod 	= $user->id;
					$objectadd->date_mod 		= dol_now();
					$objectadd->tms 			= dol_now();
					$objectadd->status 			= 1;
					$objectadd->fk_projet 		= $object->id;
					$objectadd->fk_user_create 	= $user->id;
					$objectadd->date_create 	= dol_now();
					$res = $objectadd->create($user);
					if ($res <= 0)
					{
						$error++;
						setEventMessages($objectadd->error,$objectadd->errors,'errors');
					}
				}
				elseif($res == 1)
				{
					$res = $objectadd->update($user);
				}
				if ($res < 0)
				{
					$error++;
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
				}
			}	
			if (!$error) return 1;
			else return $error*-1;
		}
		elseif ($action == 'PROJECT_DELETE')
		{
			//creamos la tabla adicional de proyectos
			if ($conf->monprojet->enabled)
			{
				require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
				$objectadd = new Projetadd($this->db);
				$res = $objectadd->fetch(0,$object->id);
				if ($objectadd->id == $object->id)
				{
					$res = $objectadd->delete($user);
					if ($res <= 0)
					{
						$error++;
						setEventMessages($objectadd->error,$objectadd->errors,'errors');
					}
				}
			}	
			if (!$error) return 1;
			else return $error*-1;
		}
		elseif ($action == 'PROJECT_CLOSE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROJECT_VALIDATE')
		{
	   		//dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			//recuperamos la lista de projet_task
			include_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
			$obj = new Projettaskbase($this->db);
			$obj->getlisttask($object->id);
			if (count($obj->array) > 0)
			{
				foreach((array) $obj->array AS $i => $data)
				{
					$objnew = new Projettaskbase($this->db);
					$lNew = true;
					if ($objnew->fetch('',$data->id)>0)
					{
						if ($objnew->fk_projet_task == $data->id)
							$lNew = false;
					}
					$objnew->ref = $data->ref;
					$objnew->entity = $data->entity;
					$objnew->fk_projet = $data->fk_projet;
					$objnew->fk_projet_task = $data->id;
					$objnew->fk_task_parent = $data->fk_task_parent;
					$objnew->datec = $data->datec;
					$objnew->tms = $data->tms;
					$objnew->dateo = $data->dateo;
					$objnew->datee = $data->datee;
					$objnew->datev = $data->datev;
					$objnew->label = $data->label;
					$objnew->description = $data->description;
					$objnew->duration_effective = $data->duration_effective;
					$objnew->planned_workload = $data->planned_workload;
					$objnew->progress = $data->progress;
					$objnew->priority = $data->priority;
					$objnew->fk_user_creat = $data->fk_user_creat;
					$objnew->fk_user_valid = $data->fk_user_valid;
					$objnew->fk_statut = $data->fk_statut;
					$objnew->note_private = $data->note_private;
					$objnew->note_public = $data->note_public;
					$objnew->rang = $data->rang;
					$objnew->model_pdf = $data->model_pdf;

					if (!$lNew)
					{
			//actualizamos
						$res = $objnew->update($user);
						if ($res <=0)
							$error++;
					}
					else
					{
			//insertamos como nuevo
						$res = $objnew->create($user);
						if ($res <=0)
							$error++;
					}
				}
		  			//foreach
			}
		}

			// Project tasks
		elseif ($action == 'TASK_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		   //actualizacion del campo rang
			$this->update_task($object);
		}
		elseif ($action == 'TASK_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		   		//actualizamos el fk_statut de projet_task
			include_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
			$objtask = new Taskext($this->db);
			$restask = $objtask->update_statut($object->id,$object->fk_statut);
			if (!$restask >0)
				$error++;
		}
		elseif ($action == 'TASK_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

			// Task time spent
		elseif ($action == 'TASK_TIMESPENT_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			return 1;
			exit;

		}
		elseif ($action == 'TASK_TIMESPENT_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'TASK_TIMESPENT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

			// Shipping
		elseif ($action == 'SHIPPING_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'SHIPPING_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'SHIPPING_VALIDATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'SHIPPING_SENTBYMAIL')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'SHIPPING_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'SHIPPING_BUILDDOC')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'ACTION_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id.'&fk_projet='.$object->fk_project);
			$error=0;
			$aUser = array();
			if(isset($_SESSION['aUseraction']))
				$aUser = unserialize($_SESSION['aUseraction']);
			$val = array('mandatory'=>0,'transparency'=>1,'answer_status'=>0);
			foreach((array) $aUser as $key => $value)
			{
				if ($value)
				{
					$sql ="INSERT INTO ".MAIN_DB_PREFIX."actioncomm_resources(fk_actioncomm, element_type, fk_element, mandatory, transparency, answer_status)";
					$sql.=" VALUES(".$object->id.", 'user', ".$key.", ".(empty($val['mandatory'])?'0':$val['mandatory']).", ".(empty($val['transparency'])?'0':$val['transparency']).", ".(empty($val['answer_status'])?'0':$val['answer_status']).")";

					$resql = $this->db->query($sql);
					if (! $resql)
					{
						$error++;
					}
				}
			}
			if ($error>0)
				return -1;
		}

		return 0;
	}

	function update_task($object)
	{
		global $user;
		$objnew = new Task($this->db);
		$objnew->fetch($object->id);
		$idsearch = $object->id;
		$lLoop = true;
		$nLevel = 0;
		while ($lLoop == true)
		{
			$objnew->fetch($idsearch);
			if ($objnew->id == $idsearch)
			{
				if ($objnew->fk_task_parent > 0)
				{
					$nLevel++;
					$idsearch = $objnew->fk_task_parent;
				}
				else
					$lLoop = false;
			}
			else
				$lLoop = false;
		}
	  		//actualizacion del nLevel
		$objup = new Task($this->db);
		$objup->fetch($object->id);
		$objup->rang = $nLevel;
		$res = $objup->update($user,1);
		return 1;
	}
}
?>
