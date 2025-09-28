<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *  \file       dev/skeletons/entrepotuser.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2016-08-02 16:26
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/entrepotuser.class.php");


/**
 *	Put here description of your class
 */
class Entrepotuseradd  extends Entrepotuser
{
	public $lines;
	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_entrepot,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.date_create,";
		$sql.= " t.active,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		
		if ($filterstatic) $sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();
		$resql = $this->db->query($sql);
		if ($resql) 
		{
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new EntrepotuserLine();

                $line->id    = $obj->rowid;
                
				$line->fk_entrepot = $obj->fk_entrepot;
				$line->fk_user = $obj->fk_user;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->active = $obj->active;
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

				if ($lView)
				{
               		$this->id    = $obj->rowid;
					$this->fk_entrepot = $obj->fk_entrepot;
					$this->fk_user = $obj->fk_user;
					$this->fk_user_mod = $obj->fk_user_mod;
					$this->date_create = $this->db->jdate($obj->date_create);
					$this->active = $obj->active;
					$this->tms = $this->db->jdate($obj->tms);
					$this->statut = $obj->statut;
				}

				$this->lines[] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}
}

class EntrepotuserLine // extends CommonObject
{

    public $id;
    
	public $fk_entrepot;
	public $fk_user;
	public $fk_user_mod;
	public $date_create='';
	public $active;
	public $tms='';
	public $statut;
}
?>
