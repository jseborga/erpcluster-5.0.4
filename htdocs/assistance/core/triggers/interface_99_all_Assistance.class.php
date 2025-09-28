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
class InterfaceAssistance
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
			if ($user->fk_member>0)
			{
				if ($conf->global->ASSISTANCE_REGISTER_USER_LOGIN)
				{
					$_SESSION['urlant'] = $_SERVER['PHP_SELF'];
					header("Location: ".DOL_URL_ROOT.'/assistance/assistance.php?action=register&backtopage='.$_SERVER['PHP_SELF']);
					exit;
				}
			}
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
		// if ($user->fk_member>0)
		//   {
		// 	//se realiza un marcado cuando se cierra session
		// 	$timemax = $conf->global->ASSISTANCE_TIMEMAX_REGISTER_NEXT;
		// 	$aDatehoy = dol_getdate(dol_now());
		// 	$dHoy = $aDatehoy['mday'];
		// 	$mHoy = $aDatehoy['mon'];
		// 	$yHoy = $aDatehoy['year'];
		// 	$hhHoy = $aDatehoy['hours'];
		// 	$mmHoy = $aDatehoy['minutes'];
		// 	$timetot = $hhHoy * 60 + $mmHoy;
		// 	$error=0;
		// 	//buscamos si existe registro con tiempo -5min
		// 	if (empty($timemax)) $timemax = 5;
		// 	//buscamos el ultimo registro de la persona

		// 	dol_include_once('/assistance/class/assistance.class.php');
		// 	$objectn = new Assistance($this->db);
		// 	$idmember  = $user->fk_member;
		// 	$cm = $idcontact>0?'c':'m';
		// 	if ($idmember>0)
		// 	  $objectn->fetchAll($sortorder='DESC', $sortfield='date_ass', $limit=20, $offset=0, array('fk_member'=>$idmember), '', $filtermode='AND');
		// 	$lRegister = false;
		// 	if (count($objectn->lines) >0)
		// 	  {
		// 	    $nRegister = 0;
		// 	    //verificamos cuantos registros del dia existen
		// 	    foreach ($objectn->lines AS $m => $objr)
		// 	      {
		// 		$aDate = dol_getdate($objr->date_ass);
		// 		if ($aDate['mday'] == $dHoy &&
		// 		    $aDate['mon'] == $mHoy &&
		// 		    $aDate['year'] == $yHoy)
		// 		  $nRegister++;
		// 	      }
		// 	    $nRegister++;
		// 	    $obj = $objectn->lines[0];
		// 	    //verificamos cuando se registro por ultima vez por member
		// 	    if ($cm == 'm')
		// 	      {
		// 		if ($idmember && $obj->fk_member == $idmember)
		// 		  {
		// 		    $aDate = dol_getdate($obj->date_ass);
		// 		    if ($aDate['mday'] == $dHoy &&
		// 			$aDate['mon'] == $mHoy &&
		// 			$aDate['year'] == $yHoy)
		// 		      {
		// 			//tiene marcado de hoy
		// 			//verificamos la hora y min
		// 			$timetotreg = $aDate['hours'] * 60 + $aDate['minutes'] + $timemax+1;
		// 			if ($timetotreg >= $timetot)
		// 			  {
		// 			    $lRegister = false;
		// 			    $error++;
		// 			    setEventMessage($langs->trans("Thereisarecord",$langs->transnoentitiesnoconv("Members")),'errors');
		// 			  }
		// 			else
		// 			  $lRegister = true;
		// 		      }
		// 		    else
		// 		      {
		// 			$nRegister = 1;
		// 			$lRegister = true;
		// 		      }
		// 		  }
		// 		else
		// 		  {
		// 		    $nRegister = 1;
		// 		    $lRegister = true;
		// 		  }
		// 	      }

		// 	  }
		// 	else
		// 	  {
		// 	    $nRegister = 1;
		// 	    $lRegister = true;
		// 	  }
		// 	if ($lRegister)
		// 	  {
		// 	    $objectn->initAsSpecimen();
		// 	    $objectn->entity=$conf->entity;
		// 	    $objectn->fk_soc=0;
		// 	    $objectn->fk_contact=0;
		// 	    $objectn->fk_member=$user->fk_member;
		// 	    $objectn->date_ass=dol_now();
		// 	    $objectn->marking_number=$nRegister;
		// 	    $objectn->fk_user_create=$user->id;
		// 	    $objectn->fk_user_mod=$user->id;
		// 	    $objectn->date_create = dol_now();
		// 	    $objectn->tms = dol_now();
		// 	    $objectn->statut=1;
		// 	    if ($objectn->fk_member <=0)
		// 	      {
		// 		$error++;
		// 		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")),'errors');
		// 	      }

		// 	    if (! $error)
		// 	      {
		// 		$result=$objectn->create($user);
		// 		if ($result > 0)
		// 		  {
		// 		    // Creation OK
		// 		  }
		// 		else
		// 		  {
		// 		    // Creation KO
		// 		  }
		// 	      }
		// 	  }
		//   }
		//fin marcado
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
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROJECT_MODIFY')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROJECT_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}
		elseif ($action == 'PROJECT_CLOSE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		//recuperamos la lista de projet_task
			include_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
			$obj = new Projettaskbase($this->db);
			$obj->getlisttask($object->id);
			if (count($obj->array) > 0)
			{
				foreach((array) $obj->array AS $i => $data)
				{
					$objnew = new Projettaskbase($this->db);
					if ($objnew->fetch($data->id)>0)
					{
						$objnew->ref = $data->ref;
						$objnew->entity = $data->entity;
						$objnew->fk_projet = $data->fk_projet;
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

						if ($objnew->id == $data->id)
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

				}
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
		//actualizacion del campo rang
			$this->update_task($object);

		}
		elseif ($action == 'TASK_DELETE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		}

		// Task time spent
		elseif ($action == 'TASK_TIMESPENT_CREATE')
		{
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			include_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
			$tasktime_id = $this->db->last_insert_id(MAIN_DB_PREFIX."projet_task_time");
		// $obj = new Task($this->db);
		// $obj->fetchTimeSpent($tasktime_id);
		// if ($obj->id == $tasktime_id)
		//   {
		//actualizamos
			$sql = "UPDATE ".MAIN_DB_PREFIX."projet_task_time SET";
			$sql.= " date_create = '".$this->db->idate(dol_now())."',";
			$sql.= " active = 1 ";
			$sql.= " WHERE rowid = ".$tasktime_id;

			if ($this->db->query($sql) )
				return 1;
			else
				return -1;
		//	      }

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
