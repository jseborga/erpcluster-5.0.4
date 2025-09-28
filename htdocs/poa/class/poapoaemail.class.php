<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2014 Ramiro Queso <ramiroques@gmail.com>
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
 *  \file       poa/class/poapreemail.class.php
 *  \ingroup    poa 
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		Initialy built by build_class_from_table on 2014-04-02 13:53
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapoaemail extends Poapoa
{

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }

    /**
     *  Load object in memory from the database 
     * para envio de email
     *  @param	int		$fk_poa    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function list_poa_email($gestion='')
    {
      global $langs,$conf;
      if (empty($gestion))
	$gestion = date('Y');
      if (empty($month))
	$month = date('m')*1;

      $sql = "SELECT";
      $sql.= " t.rowid,";
      $sql.= " t.rowid AS id,";      
      $sql.= " t.entity,";
      $sql.= " t.gestion,";
      $sql.= " t.fk_structure,";
      $sql.= " t.ref,";
      $sql.= " t.sigla,";
      $sql.= " t.label,";
      $sql.= " t.pseudonym,";
      $sql.= " t.partida,";
      $sql.= " t.amount,";
      $sql.= " t.classification,";
      $sql.= " t.source_verification,";
      $sql.= " t.unit,";
      $sql.= " t.responsible_one,";
      $sql.= " t.responsible_two,";
      $sql.= " t.responsible,";
      $sql.= " t.m_jan,";
      $sql.= " t.m_feb,";
      $sql.= " t.m_mar,";
      $sql.= " t.m_apr,";
      $sql.= " t.m_may,";
      $sql.= " t.m_jun,";
      $sql.= " t.m_jul,";
      $sql.= " t.m_aug,";
      $sql.= " t.m_sep,";
      $sql.= " t.m_oct,";
      $sql.= " t.m_nov,";
      $sql.= " t.m_dec,";
      $sql.= " t.p_jan,";
      $sql.= " t.p_feb,";
      $sql.= " t.p_mar,";
      $sql.= " t.p_apr,";
      $sql.= " t.p_may,";
      $sql.= " t.p_jun,";
      $sql.= " t.p_jul,";
      $sql.= " t.p_aug,";
      $sql.= " t.p_sep,";
      $sql.= " t.p_oct,";
      $sql.= " t.p_nov,";
      $sql.= " t.p_dec,";
      $sql.= " t.fk_area,";
      $sql.= " t.weighting,";
      $sql.= " t.fk_poa_reformulated,";
      $sql.= " t.version,";
      $sql.= " t.statut,";
      $sql.= " t.statut_ref,";
      $sql.= " t.active,";
      $sql.= " s.sigla,";
      $sql.= " s.label AS labelstructure ";

		
      $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
      $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON t.fk_structure = s.rowid";
      $sql.= " WHERE t.gestion = ".$gestion;
      $sql.= " AND t.entity = ".$conf->entity;

      $sql.= " AND t.statut > 0";
      $sql.= " ORDER BY s.sigla ASC,";
      $sql.= " t.partida ASC ";
      dol_syslog(get_class($this)."::list_poa_email sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      $aPoa = array();
      if ($resql)
	{
	  echo '<hr>'.$num = $this->db->num_rows($resql);
	  require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
	  require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoauser.class.php';
	  require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';

	  if ($this->db->num_rows($resql))
	    {
	      $i = 0;
	      while ($i < $num)
		{
		  //variables para envio correo
		  $detail = '';
		  $lVal = true; //poa validado
		  $lInstruction = false;

		  $obj = $this->db->fetch_object($resql);
		  //excluimos el poa reformulado que no esta aprobado
		  if ($obj->version > 0 && $obj->statut_ref == 0)
		    $lVal = false;
		  
		  if ($lVal)
		    {
		      // buscamos los preventivos por poa
		      //si no existe preventivo y la fecha del poa es pasado se envia mail
		      $objprev = new Poapartidapre($this->db);
		      $objprev->getlist_poa($obj->rowid);
		      echo '<hr>count prev: '.count($objprev->array);
		      if (count($objprev->array) > 0)
			{
			  //analizamos si se cumplio la fecha
			  //existe el inicio de proceso
			}
		      else
			{
			  $lInstruction = true;
			  
			  //buscamos al usuario actiovo del poa
			  $objpoauser = new Poapoauser($this->db);
			  $objpoauser->getlist($obj->rowid,1/*solo activo*/);
			  $fk_user = '';
			  echo '<hr>userid: '.count($objpoauser->array);
			  if (count($objpoauser->array) > 0)
			    {
			      foreach((array) $objpoauser->array AS $k => $objdata)
				{
				  $fk_user = $objdata->fk_user;
				}
			    }
			  if (empty($fk_user))
			    $lInstruction = false;
			  
			  //verificamos si esta activo poai
			  if ($conf->poai->enabled)
			    {
			      //verificamos si se genero alguna instruccion para el pac
			      require_once DOL_DOCUMENT_ROOT.'/poai/instruction/class/poaiinstruction.class.php';
			      $objinst = new Poaiinstruction($this->db);
			      $objinst->getlist($obj->id,'POA');
			      if (count($objinst->array) > 0)
				$lInstruction = false;
			    }
			  /////////////////////////////////////////////////////
			  echo '<br>resultado '.$lInstruction;
			  if ($lInstruction)
			    {
			      //creamos la instruccion para hacer seguimiento
			      //buscamos el rating por defecto
			      require_once DOL_DOCUMENT_ROOT.'/poai/poai/class/poaipoai.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/poai/poai/class/poaipoairating.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/poai/poai/class/poaipoairatingdet.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/poai/lib/poai.lib.php';
			      
			      // //armamos el cuerpo de la instruccion
			      // $objpac = new Poapac($this->db);
			      // $objpac->fetch_poa($obj->id);
			      // $htmlpac = '';
			      // echo '<br>antes de revisar pac';
			      // if (count($objpac->array) > 0)
			      // 	{
			      // 	  foreach ((array) $objpac->array AS $m => $objPac)
			      // 	    {
			      // 	      if (!empty($htmlpac)) $htmlpac.= '<br>';
			      // 	      $htmlpac.=$objpac->nom;
			      // 	    }
			      // 	}
			      // echo '<br>pac '.$htmlpac;
			      //mes programado desembolso
			      $htmlmesp='<br>'.$langs->trans('Desembolso:').' ';
			      
			      if ($obj->m_jan) $htmlmesp.=', - '.$langs->trans('Jan').': '.price($obj->m_jan);
			      if ($obj->m_feb) $htmlmesp.=', - '.$langs->trans('Feb').': '.price($obj->m_feb);
			      if ($obj->m_mar) $htmlmesp.=', - '.$langs->trans('Mar').': '.price($obj->m_mar);
			      if ($obj->m_apr) $htmlmesp.=', - '.$langs->trans('Apr').': '.price($obj->m_apr);
			      if ($obj->m_may) $htmlmesp.=', - '.$langs->trans('May').': '.price($obj->m_may);
			      if ($obj->m_jun) $htmlmesp.=', - '.$langs->trans('Jun').': '.price($obj->m_jun);
			      if ($obj->m_jul) $htmlmesp.=', - '.$langs->trans('Jul').': '.price($obj->m_jul);
			      if ($obj->m_aug) $htmlmesp.=', - '.$langs->trans('Aug').': '.price($obj->m_aug);
			      if ($obj->m_sep) $htmlmesp.=', - '.$langs->trans('Sep').': '.price($obj->m_sep);
			      if ($obj->m_oct) $htmlmesp.=', - '.$langs->trans('Oct').': '.price($obj->m_oct);
			      if ($obj->m_nov) $htmlmesp.=', - '.$langs->trans('Nov').': '.price($obj->m_nov);
			      if ($obj->m_dec) $htmlmesp.=', - '.$langs->trans('Dec').': '.price($obj->m_dec);
			      
			      //mes programado ejecucion
			      $htmlmesp.='<br>'.$langs->trans('Ejecucion:').' ';
			      if ($obj->p_jan) $htmlmesp.=', - '.$langs->trans('Jan').'; '.$obj->p_jan;
			      if ($obj->p_feb) $htmlmesp.=', - '.$langs->trans('Feb').'; '.$obj->p_feb;
			      if ($obj->p_mar) $htmlmesp.=', - '.$langs->trans('Mar').'; '.$obj->p_mar;
			      if ($obj->p_apr) $htmlmesp.=', - '.$langs->trans('Apr').'; '.$obj->p_apr;
			      if ($obj->p_may) $htmlmesp.=', - '.$langs->trans('May').'; '.$obj->p_may;
			      if ($obj->p_jun) $htmlmesp.=', - '.$langs->trans('Jun').'; '.$obj->p_jun;
			      if ($obj->p_jul) $htmlmesp.=', - '.$langs->trans('Jul').'; '.$obj->p_jul;
			      if ($obj->p_aug) $htmlmesp.=', - '.$langs->trans('Aug').'; '.$obj->p_aug;
			      if ($obj->p_sep) $htmlmesp.=', - '.$langs->trans('Sep').'; '.$obj->p_sep;
			      if ($obj->p_oct) $htmlmesp.=', - '.$langs->trans('Oct').'; '.$obj->p_oct;
			      if ($obj->p_nov) $htmlmesp.=', - '.$langs->trans('Nov').'; '.$obj->p_nov;
			      if ($obj->p_dec) $htmlmesp.=', - '.$langs->trans('Dec').'; '.$obj->p_dec;
			      
			      $objrat    = new Poaipoairating($this->db);
			      $objratdet = new Poaipoairatingdet($this->db);
			      $objpoai   = new Poaipoai($this->db);
			      echo '<br>'.$htmlmesp;
			      $objratdet->fetch_default($fk_user,$gestion);
			      echo ''.$objratdet->fk_user.' == '.$fk_user.' '.$objratdet->gestion.' '.$gestion;
			      if ($objratdet->fk_user == $fk_user &&
				  $objratdet->gestion == $obj->gestion)
				{
				  $detail = $langs->trans('La actividad registrada bajo el nombre de: ');
				  $detail.= $obj->label;
				  $detail.= ' ('.$langs->trans('Meta').': '.$obj->sigla;
				  $detail.= '; '.$langs->trans('Partida').': '.$obj->partida.') ';
				  $detail.= $langs->trans('designado bajo su responsabilidad, a la fecha no ha registrado ningun avance');
				  $detail.= ' ('.$langs->trans('Preventive').', '.$langs->trans('Committed').', '.$langs->trans('Accrued').'). ';
				  $detail.='<br>'.$langs->trans('Favor su atencion toda vez que ya transcurrieron').' '.date('m').' '.$langs->trans('meses de la gestion');
				  // $detail = $langs->trans('POA sin movimiento: ');
				  // $detail.= '<br>'.$obj->label;
				  // $detail.='<br>'.$langs->trans('Amount').': '.price($obj->amount);
				  // if (!empty($htmlpac))
				  // 	$detail.='<br>'.$langs->trans('con PAC').': '.$htmlpac;
				  // else
				  // 	$detail.='<br>'.$langs->trans('SIN REGISTRO EN EL PAC');
				  // if (!empty($htmlmesp))
				  // 	{
				  // 	  $detail.='<br>'.$langs->trans('Programado para su ejecucion de acuerdo al siguiente cronograma: ');
				  // 	  $detail.=$htmlmesp;
				  // 	}
				  echo '<hr>'.$detail;
				  
				  $fk_poai_rating_det = $objratdet->id;
				  //buscamos el siguiente numero
				  $objinst->getmaxref($gestion,$fk_user);
				  $maximo = $objinst->maximo;
				  if (empty($maximo) || is_null($maximo))
				    $maximo = 1;
				  else
				    $maximo++;
				  
				  $object = new Poaiinstruction($this->db);
				  $error = 0;
				  $commitment_date = dol_mktime(12, 0, 0, date('m'),date('d'),date('Y'));
				  
				  $object->type_instruction = 'POA';
				  $object->fk_id = $obj->id;
				  $object->fk_poai_rating_det = $fk_poai_rating_det;
				  $object->ref = $maximo;
				  
				  $object->detail = $detail;
				  $object->commitment_date = $commitment_date;
				  $object->date_create = dol_now();
				  $object->fk_user_create = $user->id + 0;
				  $object->tms = date('YmdHis');
				  $object->statut = 0;
				  //generando codigo unico de longitud 40 
				  $object->tokenreg = generarcodigopoai(40);
				  
				  $this->db->begin();
				  echo '<hr>newid '.$idnew = $object->create($user);
				  if ($idnew > 0)
				    {
				      $error = '';
				      //creamos el registro en agenda
				      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
				      require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
				      require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
				      require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
				      require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
				      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/cactioncomm.class.php';
				      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
				      require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
				      require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
				      
				      $cactioncomm = new CActionComm($this->db);
				      $actioncomm  = new ActionComm($this->db);
				      $objuser   = new User($this->db);
				      $extrafields = new ExtraFields($this->db);
				      
				      $object->fetch($idnew);
				      $type_inst = $object->type_instruction;
				      $idp = $object->fk_id;
				      echo '<hr>ratdet '.$idrr = $object->fk_poai_rating_det;
				      
				      $objratdet->fetch($object->fk_poai_rating_det);
				      echo '<hr>ratindi '.$objratdet->fk_poai_poai_rating;
				      $objrat->fetch($objratdet->fk_poai_poai_rating);
				      $idr = $objratdet->fk_poai_poai_rating;
				      $objpoai->fetch($objrat->fk_poai_poai);
				      echo '<hr>poai '.$objrat->fk_poai_poai;
				      echo '<hr>userid '.$affectedto = $objpoai->fk_user;
				      
				      if ($contactid)
					{
					  $result=$contact->fetch($contactid);
					}
				      
				      $fulldayevent=1;
				      $percentage=in_array(0,array(0,100))?0:GETPOST("percentage");	// If status is -1 or 100, percentage is not defined and we must use status
				      
				      // Clean parameters
				      $datep=dol_mktime($fulldayevent?'00':$_POST["aphour"], $fulldayevent?'00':$_POST["apmin"], 0, date('m',$object->commitment_date), date('d',$object->commitment_date), date('Y',$object->commitment_date));
				      $datef=dol_mktime($fulldayevent?'23':$_POST["p2hour"], $fulldayevent?'59':$_POST["p2min"], $fulldayevent?'59':'0', $_POST["p2month"], $_POST["p2day"], $_POST["p2year"]);
				      // Check parameters
				      if (! $datef && $percentage == 100)
					{
					  $error++;
					  $action = '';
					  $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("DateEnd")).'</div>';
					}
				      
				      if (empty($conf->global->AGENDA_USE_EVENT_TYPE) && ! $object->detail)
					{
					  $error++;
					  $action = '';
					  $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Title")).'</div>';
					}
				      
				      // Initialisation objet cactioncomm
				      // if (! GETPOST('actioncode'))
				      //   {
				      // 	$error++;
				      // 	$action = 'create';
				      // 	$mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")).'</div>';
				      //   }
				      // else
				      //   {
				      $result=$cactioncomm->fetch('AC_OTH');
				      // }
				      
				      // Initialisation objet actioncomm
				      $actioncomm->type_id = $cactioncomm->id;
				      $actioncomm->type_code = $cactioncomm->code;
				      $actioncomm->priority = GETPOST("priority")?GETPOST("priority"):0;
				      $actioncomm->fulldayevent = (! empty($fulldayevent)?1:0);
				      $actioncomm->location = GETPOST("location");
				      $actioncomm->transparency = (GETPOST("transparency")=='on'?1:0);
				      $actioncomm->label = $langs->trans('POA sin movimiento').': '.trim($obj->label);
				      
				      
				      $actioncomm->fk_element = GETPOST("fk_element");
				      $actioncomm->elementtype = GETPOST("elementtype");
				      if (! $object->detail)
					{
					  if (GETPOST('actioncode') == 'AC_RDV' && $contact->getFullName($langs))
					    {
					      $actioncomm->label = $langs->transnoentitiesnoconv("TaskRDVWith",$contact->getFullName($langs));
					    }
					  else
					    {
					      if ($langs->trans("Action".$actioncomm->type_code) != "Action".$actioncomm->type_code)
						{
						  $actioncomm->label = $langs->transnoentitiesnoconv("Action".$actioncomm->type_code)."\n";
						}
					      else $actioncomm->label = $cactioncomm->libelle;
					    }
					}
				      $actioncomm->fk_project = isset($_POST["projectid"])?$_POST["projectid"]:0;
				      $actioncomm->datep = $datep;
				      $actioncomm->datef = $datef;
				      $actioncomm->percentage = $percentage;
				      $actioncomm->duree=((float) (GETPOST('dureehour') * 60) + (float) GETPOST('dureemin')) * 60;
				      
				      $usertodo=new User($this->db);
				      if ($affectedto > 0)
					{
					  $usertodo->fetch($affectedto);
					}
				      $actioncomm->usertodo = $usertodo;
				      $userdone=new User($this->db);
				      if ($_POST["doneby"] > 0)
					{
					  $userdone->fetch($_POST["doneby"]);
					}
				      $actioncomm->userdone = $userdone;
				      
				      $actioncomm->note = trim($object->detail);
				      
				      // if (isset($_POST["contactid"])) $actioncomm->contact = $contact;
				      // if (GETPOST('socid','int') > 0)
				      // {
				      // 	$societe = new Societe($db);
				      // 	$societe->fetch(GETPOST('socid','int'));
				      // 	$actioncomm->societe = $societe;
				      // }
				      
				      // Special for module webcal and phenix
				      // TODO external modules
				      if (! empty($conf->webcalendar->enabled) && GETPOST('add_webcal') == 'on') $actioncomm->use_webcal=1;
				      if (! empty($conf->phenix->enabled) && GETPOST('add_phenix') == 'on') $actioncomm->use_phenix=1;
				      
				      // Check parameters
				      if ($actioncomm->type_code == 'AC_RDV' && ($datep == '' || ($datef == '' && empty($fulldayevent))))
					{
					  $error++;
					}
				      if (! empty($datea) && GETPOST('percentage') == 0)
					{
					  $error++;
					}
				      
				      $ret = $extrafields->setOptionalsFromPost($extralabels,$actioncomm);
				  
				      if (! $error)
					{
					  
					  //cambiando a validado
					  $object->statut = 1;
					  //update
					  $object->update($user);
					  
					  // On cree l'action
					  $idaction=$actioncomm->add($user);
					  
					  if ($idaction > 0)
					    {
					      if (! $actioncomm->error)
						{
						  $this->db->commit();
						}
					      else
						{
						  // If error
						  $this->db->rollback();
						}
					    }
					  else
					    {
					      $this->db->rollback();
					    }
					}
				      //fin agenda
				    }
				  else
				    $this->db->rollback();
				}
			    }
			  /////////////////////////////////////////////////////
			}
		    }
		  $i++;
		}
	    }
	  $this->db->free($resql);
	  
	  return 1;
	}
      else
	{
	  $this->error="Error ".$this->db->lasterror();
	  dol_syslog(get_class($this)."::list_pac_email ".$this->error, LOG_ERR);
	  return -1;
	}
    }
	
}
?>
