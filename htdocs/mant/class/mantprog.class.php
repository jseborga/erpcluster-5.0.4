<?php
/* Copyright (C) 2014-2014 Ramiro Queso <ramiroques@gmail.com>
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
 *  \file       mant/class/mantprog.class.php
 *  \ingroup    mant
 *  \brief      This file is an create work order
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/mant/class/mantprogramming.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");


/**
 *	Put here description of your class
 */
class mantprog extends mantprogramming
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
	 * lista de programaciones para mantenimiento
	 *  @param	int		$fk_poa    Id object
	 *  @return int          	<0 if KO, >0 if OK
	*/
	function list_def_prog()
	{
		global $langs,$conf,$user;

		$nmonth = date('m')*1;
	  //mes actual
		$nday = date('d')*1;
	  //dia actual
	  //fecha actual
		$dateact = dol_now();
		$aDate = dol_getdate($dateact);
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_asset,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_member,";
		$sql.= " t.internal,";
		$sql.= " t.speciality,";
		$sql.= " t.typemant,";
		$sql.= " t.frequency,";
		$sql.= " t.detail_value,";
		$sql.= " t.description,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_last,";
		$sql.= " t.date_next,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."mant_programming as t";
		$sql.= " WHERE t.active = 1";
		$sql.= " ORDER BY t.date_ini ASC, t.date_next ASC";

		dol_syslog(get_class($this)."::list_def_prog sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{

				require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
				require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
				require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
				require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

				$objasset = new Assetsext($this->db);
				$objuser = new User($this->db);
				$i = 0;
				while ($i < $num)
				{
					$nyear = $aDate['year'];
					$nmonth = $aDate['mon'];
					$nday = $aDate['wday'];
					$numDay = $aDate['mday'];
					$monthIncrement = 0;

					//variables para crear orden de trabajo
					$lAdd = false;
					$dayIncrement = 0;
					$obj = $this->db->fetch_object($resql);
					if ($this->db->jdate($obj->date_next) < $dateact || empty($obj->date_next) || is_null($obj->date_next))
					{
						//validamos la frecuencia
						if ($obj->frequency == 'BYMONTHLY' || $obj->frequency == 'SEMIANNUAL' || $obj->frequency == 'QUARTERLY')
						{
							if ($obj->frequency == 'BYMONTHLY') $monthIncrement = 2;
							if ($obj->frequency == 'SEMIANNUAL') $monthIncrement = 6;
							if ($obj->frequency == 'QUARTERLY') $monthIncrement = 3;
							//mensual multiple
							//se procesa cada primer dia del mes
							foreach((array) $object->detail_value AS $k => $value)
							{
								$mesDef = $value;
								if ($mesDef == $nmonth && $nday == 1)
								{
									$lAdd = true;
									$newMonth = $nmonth+$monthIncrement;
									if ($newMonth>12)
									{
										$nyear++;
										$newMonth = 1;
									}
									$newDate = dol_mktime(0,0,1,$newMonth,$numDay,$nyear);
								}
							}
						}
						elseif ($obj->frequency == 'ANNUAL')
						{
							//anual unico
							if ($obj->detail_value == $nmonth && $nday == 1)
							{
								$lAdd = true;
								$newMonth = $nmonth;
								$nyear++;
								$newDate = dol_mktime(0,0,1,$newMonth,$numDay,$nyear);
							}
						}
						elseif($obj->frequency == 'WEEKLY')
						{
							$dayIncrement = 7;
							//dia multiple
							foreach((array) $object->detail_value AS $k => $value)
							{
								$diaDef = $value;
								if ($diaDef == $nday)
								{
									$lAdd = true;
									$aNewdate = dol_getDate(dol_time_plus_duree($dateact, $dayIncrement, 'd'));
									$newDate = dol_mktime(0,0,1,$aNewdate['mon'],$numDay,$aNewdate['year']);
								}
							}
						}
						elseif($obj->frequency == 'MONTHLY')
						{
							$monthIncrement = 1;
							//dia simple
							if ($obj->detail_value == $nday)
							{
								$lAdd = true;
								$newMonth = $nmonth+$monthIncrement;
								if ($newMonth>12)
								{
									$nyear++;
									$newMonth = 1;
								}
								$newDate = dol_mktime(0,0,1,$newMonth,$numDay,$nyear);
							}
						}
						else
						{
							$dayIncrement=1;
							//diario
							$lAdd = true;
							$newDate = dol_time_plus_duree($dateact, $dayIncrement, 'd');
						}
						if ($lAdd)
						{
							$this->db->begin();
							$objjobs = new Mjobsext($this->db);
							$error=0;
							$date_create  = dol_mktime(12, 0, 0, date('m'),  date('d'),  date('Y'));
							$code= generarcodigo(4);
							$objjobs->address_ip     = $_SERVER['REMOTE_ADDR'];
							if (empty($objjobs->address_ip)) $objjobs->address_ip = '0.0.0.0';
							$objjobs->ref            = '(PROV)'.$code;
							$objjobs->fk_member      = $obj->fk_member;
							$objjobs->entity         = $conf->entity;
							$objjobs->fk_equipment   = $obj->fk_asset;
							$objjobs->fk_equipment_prog = $obj->fk_asset;
							$objjobs->typemant       = $obj->typemant;
							$objjobs->typemant_prog  = $obj->typemant;
							$objjobs->internal       = $obj->internal;

							$objjobs->fk_work_request = 0;
							$objjobs->fk_type_repair = 0;

							if ($objasset->fetch($obj->fk_asset)>0)
							{
								if ($objasset->id == $obj->fk_asset)
								{
									$objjobs->fk_property    = $objasset->fk_property;
									$objjobs->fk_location    = $objasset->fk_location;
								}
							}
							if ($objuser->fetch($obj->fk_asset)>0)
							{
								if ($objuser->id == $obj->fk_asset)
									$objjobs->email = $objuser->email;
								else
								{
									//buscamos al usuario creador
									if ($objuser->fetch($obj->fk_user_create)>0)
									{
										if ($objuser->id == $obj->fk_user_create)
											$objjobs->email = $objuser->email;
										else
											$error=101;
									}
								}
							}
							$objjobs->speciality     = $obj->speciality;
							$objjobs->detail_problem = $obj->description;
							$objjobs->fk_user_create = $user->id;
							$objjobs->fk_user_mod = $user->id;
							$objjobs->date_create    = $date_create;
							$objjobs->datec = dol_now();
							$objjobs->datem = dol_now();

							$objjobs->statut         = 1;
							if (empty($objjobs->fk_property))
							{
								$objjobs->fk_property = 0;
								//$error=102;
							}
							if (empty($objjobs->fk_location))
							{
								$objjobs->fk_location=0;
								//$error=103;
							}
							if (empty($objjobs->speciality))
							{
								$error=104;
							}
							if (empty($error))
							{
								$id = $objjobs->create($user);
								if ($id > 0)
								{
									//cambiamos la fecha
									$objnew = new Mantprogramming($this->db);
									if ($objnew->fetch($obj->rowid)>0)
									{
										if ($objnew->id == $obj->rowid)
										{
											//$date_next  = dol_mktime(23, 59, 59, date('m'),  date('d'),  date('Y'));
											$objnew->date_last = dol_now();
											$objnew->date_next = $newDate;
											$res = $objnew->update($user);
											if ($res <=0)
											{
												$error++;
												setEventMessages($objnew->error,$objnew->errors,'errors');
											}
										}
									}
								}
								else
								{
									$error++;
									setEventMessages($objjobs->error,$objjobs->errors,'errors');
								}
							}
							if (!$error) $this->db->commit();
							else $this->db->rollback();
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
			dol_syslog(get_class($this)."::list_def_prog".$this->error, LOG_ERR);
			return -1;
		}
	}
}
?>
