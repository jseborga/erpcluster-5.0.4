<?php
/* Copyright (C) 2005-2014 Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2014 Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2015      Bahfir Abbes        <bafbes@gmail.com>
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
 *  \file       htdocs/core/triggers/interface_90_all_Demo.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *				                           ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */
require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for demo module
 */
class InterfaceBasepriceunits extends DolibarrTriggers
{

	public $family = 'basepriceunits';
	public $picto = 'technic';
	public $description = "Triggers of this module are empty functions. They have no effect. They are provided for tutorial purpose only.";
	public $version = self::VERSION_DOLIBARR;

	/**
     * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
     *
     * @param string		$action		Event action code
     * @param Object		$object     Object concerned. Some context information may also be provided into array property object->context.
     * @param User		    $user       Object user
     * @param Translate 	$langs      Object langs
     * @param conf		    $conf       Object conf
     * @return int         				<0 if KO, 0 if no triggered ran, >0 if OK
     */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		// Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action

		switch ($action) {

		    // Users
			case 'USER_CREATE':
			break;
			case 'USER_MODIFY':
			break;
			case 'USER_NEW_PASSWORD':
			break;
			case 'USER_ENABLEDISABLE':
			break;
			case 'USER_DELETE':
			break;
			case 'USER_SETINGROUP':
			break;
			case 'USER_REMOVEFROMGROUP':
			break;
			case 'USER_LOGIN':
			break;
			case 'USER_LOGIN_FAILED':
			break;
			case 'USER_LOGOUT':
			break;
		    // Actions
			case 'ACTION_MODIFY':
			break;
			case 'ACTION_CREATE':break;
			case 'ACTION_DELETE':break;

		    // Groups
			case 'GROUP_CREATE':break;
			case 'GROUP_MODIFY':break;
			case 'GROUP_DELETE':break;

			// Companies
			case 'COMPANY_CREATE':break;
			case 'COMPANY_MODIFY':break;
			case 'COMPANY_DELETE':break;

			// Contacts
			case 'CONTACT_CREATE':break;
			case 'CONTACT_MODIFY':break;
			case 'CONTACT_DELETE':break;
			case 'CONTACT_ENABLEDISABLE':break;

			// Products
			case 'PRODUCT_CREATE':break;
			case 'PRODUCT_MODIFY':break;
			case 'PRODUCT_DELETE':break;
			case 'PRODUCT_PRICE_MODIFY':break;
			case 'PRODUCT_SET_MULTILANGS':break;
			case 'PRODUCT_DEL_MULTILANGS':break;

			//Stock mouvement
			case 'STOCK_MOVEMENT':break;

			//MYECMDIR
			case 'MYECMDIR_DELETE':break;
			case 'MYECMDIR_CREATE':break;
			case 'MYECMDIR_MODIFY':break;

			// Customer orders
			case 'ORDER_CREATE':break;
			case 'ORDER_CLONE':break;
			case 'ORDER_VALIDATE':break;
			case 'ORDER_DELETE':break;
			case 'ORDER_CANCEL':break;
			case 'ORDER_SENTBYMAIL':break;
			case 'ORDER_CLASSIFY_BILLED':break;
			case 'ORDER_SETDRAFT':break;
			case 'LINEORDER_INSERT':break;
			case 'LINEORDER_UPDATE':break;
			case 'LINEORDER_DELETE':break;

			// Supplier orders
			case 'ORDER_SUPPLIER_CREATE':break;
			case 'ORDER_SUPPLIER_CLONE':break;
			case 'ORDER_SUPPLIER_VALIDATE':break;
			case 'ORDER_SUPPLIER_DELETE':break;
			case 'ORDER_SUPPLIER_APPROVE':break;
			case 'ORDER_SUPPLIER_REFUSE':break;
			case 'ORDER_SUPPLIER_CANCEL':break;
			case 'ORDER_SUPPLIER_SENTBYMAIL':break;
			case 'ORDER_SUPPLIER_DISPATCH':break;
			case 'LINEORDER_SUPPLIER_DISPATCH':break;
			case 'LINEORDER_SUPPLIER_CREATE':break;
			case 'LINEORDER_SUPPLIER_UPDATE':break;

			// Proposals
			case 'PROPAL_CREATE':break;
			case 'PROPAL_CLONE':break;
			case 'PROPAL_MODIFY':break;
			case 'PROPAL_VALIDATE':break;
			case 'PROPAL_SENTBYMAIL':break;
			case 'PROPAL_CLOSE_SIGNED':break;
			case 'PROPAL_CLOSE_REFUSED':break;
			case 'PROPAL_DELETE':break;
			case 'LINEPROPAL_INSERT':break;
			case 'LINEPROPAL_UPDATE':break;
			case 'LINEPROPAL_DELETE':break;

			// SupplierProposal
			case 'SUPPLIER_PROPOSAL_CREATE':break;
			case 'SUPPLIER_PROPOSAL_CLONE':break;
			case 'SUPPLIER_PROPOSAL_MODIFY':break;
			case 'SUPPLIER_PROPOSAL_VALIDATE':break;
			case 'SUPPLIER_PROPOSAL_SENTBYMAIL':break;
			case 'SUPPLIER_PROPOSAL_CLOSE_SIGNED':break;
			case 'SUPPLIER_PROPOSAL_CLOSE_REFUSED':break;
			case 'SUPPLIER_PROPOSAL_DELETE':break;
			case 'LINESUPPLIER_PROPOSAL_INSERT':break;
			case 'LINESUPPLIER_PROPOSAL_UPDATE':break;
			case 'LINESUPPLIER_PROPOSAL_DELETE':break;

			// Contracts
			case 'CONTRACT_CREATE':break;
			case 'CONTRACT_ACTIVATE':break;
			case 'CONTRACT_CANCEL':break;
			case 'CONTRACT_CLOSE':break;
			case 'CONTRACT_DELETE':break;
			case 'LINECONTRACT_INSERT':break;
			case 'LINECONTRACT_UPDATE':break;
			case 'LINECONTRACT_DELETE':break;

			// Bills
			case 'BILL_CREATE':break;
			case 'BILL_CLONE':break;
			case 'BILL_MODIFY':break;
			case 'BILL_VALIDATE':break;
			case 'BILL_UNVALIDATE':break;
			case 'BILL_SENTBYMAIL':break;
			case 'BILL_CANCEL':break;
			case 'BILL_DELETE':break;
			case 'BILL_PAYED':break;
			case 'LINEBILL_INSERT':break;
			case 'LINEBILL_UPDATE':break;
			case 'LINEBILL_DELETE':break;

			//Supplier Bill
			case 'BILL_SUPPLIER_CREATE':break;
			case 'BILL_SUPPLIER_UPDATE':break;
			case 'BILL_SUPPLIER_DELETE':break;
			case 'BILL_SUPPLIER_PAYED':break;
			case 'BILL_SUPPLIER_UNPAYED':break;
			case 'BILL_SUPPLIER_VALIDATE':break;
			case 'BILL_SUPPLIER_UNVALIDATE':break;
			case 'LINEBILL_SUPPLIER_CREATE':break;
			case 'LINEBILL_SUPPLIER_UPDATE':break;
			case 'LINEBILL_SUPPLIER_DELETE':break;

			// Payments
			case 'PAYMENT_CUSTOMER_CREATE':break;
			case 'PAYMENT_SUPPLIER_CREATE':break;
			case 'PAYMENT_ADD_TO_BANK':break;
			case 'PAYMENT_DELETE':break;

		    // Online  
			case 'PAYMENT_PAYBOX_OK':break;
			case 'PAYMENT_PAYPAL_OK':break;

			// Donation
			case 'DON_CREATE':break;
			case 'DON_UPDATE':break;
			case 'DON_DELETE':break;

			// Interventions
			case 'FICHINTER_CREATE':break;
			case 'FICHINTER_MODIFY':break;
			case 'FICHINTER_VALIDATE':break;
			case 'FICHINTER_DELETE':break;
			case 'LINEFICHINTER_CREATE':break;
			case 'LINEFICHINTER_UPDATE':break;
			case 'LINEFICHINTER_DELETE':break;

			// Members
			case 'MEMBER_CREATE':break;
			case 'MEMBER_VALIDATE':break;
			case 'MEMBER_SUBSCRIPTION':break;
			case 'MEMBER_MODIFY':break;
			case 'MEMBER_NEW_PASSWORD':break;
			case 'MEMBER_RESILIATE':break;
			case 'MEMBER_DELETE':break;

			// Categories
			case 'CATEGORY_CREATE':break;
			case 'CATEGORY_MODIFY':break;
			case 'CATEGORY_DELETE':break;
			case 'CATEGORY_SET_MULTILANGS':break;

			// Projects
			case 'PROJECT_CREATE':
		    //se creara copia de la tabla pu_estructure
			require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructure.class.php';
			$objtmp = new Pustructure($this->db);
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_projet = 0 AND t.statut = 1";
			$res1 = $objtmp->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
			if ($res1>0 && $abc)
			{
				$error = 0;
				foreach ($objtmp->lines AS $j => $obj)
				{
		    		//agregamos como nuevo
					$objtmp->entity = $obj->entity;
					$objtmp->ref = $obj->ref;
					$objtmp->fk_projet = $object->id;
					$objtmp->fk_user_create = $user->id;
					$objtmp->fk_user_mod = $user->id;
					$objtmp->fk_categorie = $obj->fk_categorie;
					$objtmp->detail = $obj->detail;
					$objtmp->ordby = $obj->ordby;
					$objtmp->date_create = dol_now();
					$objtmp->ejecution = $obj->ejecution;
					$objtmp->tms = dol_now();
					$objtmp->statut = $obj->statut;
					$result = $objtmp->create($user);
					if ($result<=0) $error++;
				}
				if ($error>0) return -1;
			}
			break;
			case 'PROJECT_MODIFY':break;
			case 'PROJECT_DELETE':break;

			// Project tasks
			case 'TASK_CREATE':break;
			case 'TASK_MODIFY':break;
			case 'TASK_DELETE':break;

			// Task time spent
			case 'TASK_TIMESPENT_CREATE':break;
			case 'TASK_TIMESPENT_MODIFY':break;
			case 'TASK_TIMESPENT_DELETE':break;

			// Shipping
			case 'SHIPPING_CREATE':break;
			case 'SHIPPING_MODIFY':break;
			case 'SHIPPING_VALIDATE':break;
			case 'SHIPPING_SENTBYMAIL':break;
			case 'SHIPPING_BILLED':break;
			case 'SHIPPING_CLOSED':break;
			case 'SHIPPING_REOPEN':break;
			case 'SHIPPING_DELETE':
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			break;

		}
		return 0;
	}

}
