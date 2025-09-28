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
class InterfaceOrgman extends DolibarrTriggers
{

	public $family = 'stock';
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
			case 'USER_MODIFY':
			case 'USER_NEW_PASSWORD':
			case 'USER_ENABLEDISABLE':
			case 'USER_DELETE':
			case 'USER_SETINGROUP':
			case 'USER_REMOVEFROMGROUP':

			case 'USER_LOGIN':
			//toda la accion para registrar en la tabla llx_user_session
			//si no existe crear
			//si existe actualizar el tries en 0
			//
			$now=dol_now();
			require_once DOL_DOCUMENT_ROOT.'/orgman/class/usersessionlogext.class.php';

			$objUsersession = new Usersessionlogext($this->db);
			$filteruser = " AND t.fk_user = ".$object->id;
			$resuser = $objUsersession->fetchAll('','',0,0,array(),'AND',$filteruser,true);
				//$resuser = $objUsersession->fetch(0,$object->id);

			if($resuser==1)
			{
				$objUsersession->datelog=$now;
				$objUsersession->datem=$now;
				$objUsersession->tries=0;
				$resultuser=$objUsersession->update($user);
				if($resultuser<=0) $error++;
			}
			elseif ($resuser==0)
			{
				$objUsersession->fk_user=$object->id;
				$objUsersession->datelog=$now;
				$objUsersession->tries=0;
				$objUsersession->datec=$now;
				$objUsersession->datem=$now;
				$objUsersession->tms=$now;
				$objUsersession->status=1;
				$resultuser=$objUsersession->create($user);
				if($resultuser<=0) $error++;
			}
			else $error++;
			if (!$error) return 1;
			else return -1;
			break;
			//
			//si todo esta correcto
			//retornas 1 con return 1;
			case 'USER_LOGIN_FAILED':
				//buscamos el registro en la tabla llx_user_session
			$now=dol_now();
			require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
			require_once DOL_DOCUMENT_ROOT.'/orgman/class/usersessionlogext.class.php';
			$objUser = new User($this->db);
			$resuser = $objUser->fetch(0,$_POST['username']);
			if ($resuser==1)
			{
				$user = $objUser;

				$objUsersession = new Usersessionlogext($this->db);
				$filteruser = " AND t.fk_user = ".$objUser->id;
				$resuser = $objUsersession->fetchAll('','',0,0,array(),'AND',$filteruser,true);

				if($resuser==1)
				{
					if ($objUsersession->tries >=($conf->global->ORGMAN_NUMBER_FAILED_LOGIN?$conf->global->ORGMAN_NUMBER_FAILED_LOGIN:3))
					{

						$objUser->setstatus(0);

						//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
						//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
						require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

						require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
						require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
						require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
						require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
						require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
						require_once DOL_DOCUMENT_ROOT.'/orgman/lib/orgman.lib.php';

						$url =$dolibarr_main_url_root;

						$emailto=$objUser->email;
						//$description="Su usuario fue bloqueado comuniquese con el administrador";

						if ($objUser->email)
						{
							//sendmail
							// Define output language
							$outputlangs = $langs;
							$newlang='';
							if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
							if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
							if (! empty($newlang))
							{
								$outputlangs = new Translate("",$conf);
								$outputlangs->setDefaultLang($newlang);
							}

							$arr_file = array();
							$arr_mime = array();
							$arr_name = array();
							$arr_mime[] = 'text/html';
		  					//$arr_mime[] = 'aplication/rtf';

							$tmpsujet = $langs->trans('Sendemailrequest');
							$sendto   = $emailto;
							$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
							//$tmpbody = htmlsendemailrech($id,$object->description_job,$url);
							$tmpbody = htmlsendemailrechuser($objUser,$url);

							$msgishtml = 1;
							$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
							$arr_css = array('bgcolor' => '#FFFFCC');
							$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);

							//$result=$mailfile->sendfile();


							if ($conf->global->ORGMAN_SEND_EMAIL)
							{
								$result=$mailfile->sendfile();
							}
							else
							{
								$result = 1;
							}
							if ($result)
							{
								$mesg='<div class="ok">'.
								$langs->trans("MailSuccessfulySent",
									$mailfile->getValidAddress($object->email_from,2),
									$mailfile->getValidAddress($object->sendto,2)).
								'</div>';
								return 1;
							}
							else
							{
								$error++;
								$mesg='<div class="error">'.$langs->trans("ResultKo").
								'<br>'.$mailfile->error.' '.$result.'</div>';
								$action = 'create';
							}
						}
						else
						{
							$action = '';
							$mesg='<div class="error">'.$object->error.'</div>';
						}

						//header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
						//exit;
						return -1;
					}
					else
					{
						$objUsersession->datelog=$now;
						$objUsersession->datem=$now;
						$objUsersession->tries+=1;
						$resultuser=$objUsersession->update($user);
						if($resultuser<=0) $error++;
					}
				}
				elseif ($resuser==0)
				{
					$objUsersession->fk_user=$object->id;
					$objUsersession->datelog=$now;
					$objUsersession->tries=1;
					$objUsersession->datec=$now;
					$objUsersession->datem=$now;
					$objUsersession->tms=$now;
					$objUsersession->status=1;
					$resultuser=$objUsersession->create($user);
					if($resultuser<=0) $error++;
				}
				else $error++;

				if (!$error) return 1;
				else return -1;
			}
			else
				return 1;
			break;



			//si no existe crear
			//si existe actualizar el campo tries +1
			//si el tries es > 4
			//entonces deshabilitar al usuario
			case 'USER_LOGOUT':

		    // Actions
			case 'ACTION_MODIFY':
			case 'ACTION_CREATE':
			case 'ACTION_DELETE':

		    // Groups
			case 'GROUP_CREATE':
			case 'GROUP_MODIFY':
			case 'GROUP_DELETE':

			// Companies
			case 'COMPANY_CREATE':
			case 'COMPANY_MODIFY':
			case 'COMPANY_DELETE':

			// Contacts
			case 'CONTACT_CREATE':
			case 'CONTACT_MODIFY':
			case 'CONTACT_DELETE':
			case 'CONTACT_ENABLEDISABLE':

			// Products
			case 'PRODUCT_CREATE':
			case 'PRODUCT_MODIFY':
			break;
			case 'PRODUCT_DELETE':
			$lDel = true;
			if ($conf->orgman->enabled && $action == 'PRODUCT_DELETE')
			{
				require_once DOL_DOCUMENT_ROOT.'/orgman/class/partidaproductext.class.php';
				$objPartidaproduct = new Partidaproductext($this->db);
				$filter = " AND t.fk_product = ".$object->id;
				$resp = $objPartidaproduct->fetch($object->id);
				if ($resp>0)
				{
					$res = $objPartidaproduct->delete($user);
					if ($res <=0)
					{
						$lDel = false;
						setEventMessages($objPartidaproduct->error,$objPartidaproduct->errors,'errors');
					}
				}
			}
			if ($lDel) return 1;
			else return -1;
			break;

			case 'PRODUCT_PRICE_MODIFY':
			case 'PRODUCT_SET_MULTILANGS':
			case 'PRODUCT_DEL_MULTILANGS':

			//Stock mouvement
			case 'STOCK_MOVEMENT':
			//MYECMDIR
			case 'MYECMDIR_DELETE':
			case 'MYECMDIR_CREATE':
			case 'MYECMDIR_MODIFY':

			// Customer orders
			case 'ORDER_CREATE':
			case 'ORDER_CLONE':
			case 'ORDER_VALIDATE':
			case 'ORDER_DELETE':
			case 'ORDER_CANCEL':
			case 'ORDER_SENTBYMAIL':
			case 'ORDER_CLASSIFY_BILLED':
			case 'ORDER_SETDRAFT':
			case 'LINEORDER_INSERT':
			case 'LINEORDER_UPDATE':
			case 'LINEORDER_DELETE':

			// Supplier orders
			case 'ORDER_SUPPLIER_CREATE':
			case 'ORDER_SUPPLIER_CLONE':
			case 'ORDER_SUPPLIER_VALIDATE':
			case 'ORDER_SUPPLIER_DELETE':
			case 'ORDER_SUPPLIER_APPROVE':
			case 'ORDER_SUPPLIER_REFUSE':
			case 'ORDER_SUPPLIER_CANCEL':
			case 'ORDER_SUPPLIER_SENTBYMAIL':
			case 'ORDER_SUPPLIER_DISPATCH':
			case 'LINEORDER_SUPPLIER_DISPATCH':
			case 'LINEORDER_SUPPLIER_CREATE':
			case 'LINEORDER_SUPPLIER_UPDATE':

			// Proposals
			case 'PROPAL_CREATE':
			case 'PROPAL_CLONE':
			case 'PROPAL_MODIFY':
			case 'PROPAL_VALIDATE':
			case 'PROPAL_SENTBYMAIL':
			case 'PROPAL_CLOSE_SIGNED':
			case 'PROPAL_CLOSE_REFUSED':
			case 'PROPAL_DELETE':
			case 'LINEPROPAL_INSERT':
			case 'LINEPROPAL_UPDATE':
			case 'LINEPROPAL_DELETE':

			// SupplierProposal
			case 'SUPPLIER_PROPOSAL_CREATE':
			case 'SUPPLIER_PROPOSAL_CLONE':
			case 'SUPPLIER_PROPOSAL_MODIFY':
			case 'SUPPLIER_PROPOSAL_VALIDATE':
			case 'SUPPLIER_PROPOSAL_SENTBYMAIL':
			case 'SUPPLIER_PROPOSAL_CLOSE_SIGNED':
			case 'SUPPLIER_PROPOSAL_CLOSE_REFUSED':
			case 'SUPPLIER_PROPOSAL_DELETE':
			case 'LINESUPPLIER_PROPOSAL_INSERT':
			case 'LINESUPPLIER_PROPOSAL_UPDATE':
			case 'LINESUPPLIER_PROPOSAL_DELETE':

			// Contracts
			case 'CONTRACT_CREATE':
			case 'CONTRACT_ACTIVATE':
			case 'CONTRACT_CANCEL':
			case 'CONTRACT_CLOSE':
			case 'CONTRACT_DELETE':
			case 'LINECONTRACT_INSERT':
			case 'LINECONTRACT_UPDATE':
			case 'LINECONTRACT_DELETE':

			// Bills
			case 'BILL_CREATE':
			case 'BILL_CLONE':
			case 'BILL_MODIFY':
			case 'BILL_VALIDATE':
			case 'BILL_UNVALIDATE':
			case 'BILL_SENTBYMAIL':
			case 'BILL_CANCEL':
			case 'BILL_DELETE':
			case 'BILL_PAYED':
			case 'LINEBILL_INSERT':
			case 'LINEBILL_UPDATE':
			case 'LINEBILL_DELETE':

			//Supplier Bill
			case 'BILL_SUPPLIER_CREATE':
			case 'BILL_SUPPLIER_UPDATE':
			case 'BILL_SUPPLIER_DELETE':
			case 'BILL_SUPPLIER_PAYED':
			case 'BILL_SUPPLIER_UNPAYED':
			case 'BILL_SUPPLIER_VALIDATE':
			case 'BILL_SUPPLIER_UNVALIDATE':
			case 'LINEBILL_SUPPLIER_CREATE':
			case 'LINEBILL_SUPPLIER_UPDATE':
			case 'LINEBILL_SUPPLIER_DELETE':

			// Payments
			case 'PAYMENT_CUSTOMER_CREATE':
			case 'PAYMENT_SUPPLIER_CREATE':
			case 'PAYMENT_ADD_TO_BANK':
			case 'PAYMENT_DELETE':

		    // Online
			case 'PAYMENT_PAYBOX_OK':
			case 'PAYMENT_PAYPAL_OK':

			// Donation
			case 'DON_CREATE':
			case 'DON_UPDATE':
			case 'DON_DELETE':

			// Interventions
			case 'FICHINTER_CREATE':
			case 'FICHINTER_MODIFY':
			case 'FICHINTER_VALIDATE':
			case 'FICHINTER_DELETE':
			case 'LINEFICHINTER_CREATE':
			case 'LINEFICHINTER_UPDATE':
			case 'LINEFICHINTER_DELETE':

			// Members
			case 'MEMBER_CREATE':
			case 'MEMBER_VALIDATE':
			case 'MEMBER_SUBSCRIPTION':
			case 'MEMBER_MODIFY':
			case 'MEMBER_NEW_PASSWORD':
			case 'MEMBER_RESILIATE':
			case 'MEMBER_DELETE':

			// Categories
			case 'CATEGORY_CREATE':
			case 'CATEGORY_MODIFY':
			case 'CATEGORY_DELETE':
			case 'CATEGORY_SET_MULTILANGS':

			// Projects
			case 'PROJECT_CREATE':
			case 'PROJECT_MODIFY':
			case 'PROJECT_DELETE':

			// Project tasks
			case 'TASK_CREATE':
			case 'TASK_MODIFY':
			case 'TASK_DELETE':

			// Task time spent
			case 'TASK_TIMESPENT_CREATE':
			case 'TASK_TIMESPENT_MODIFY':
			case 'TASK_TIMESPENT_DELETE':

			// Shipping
			case 'SHIPPING_CREATE':
			case 'SHIPPING_MODIFY':
			case 'SHIPPING_VALIDATE':
			case 'SHIPPING_SENTBYMAIL':
			case 'SHIPPING_BILLED':
			case 'SHIPPING_CLOSED':
			case 'SHIPPING_REOPEN':
			case 'SHIPPING_DELETE':
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			break;

		}

		return 0;
	}

}
