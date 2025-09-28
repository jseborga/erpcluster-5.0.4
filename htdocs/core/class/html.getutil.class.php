<?php
/* Copyright (c) 2016-2017  Ramiro Queso    <ramiroques@gmail.com>
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
 *	\file       htdocs/core/class/html.form.class.php
 *  \ingroup    core
 *	\brief      File of class with all html predefined components
 */


/**
 *	Class to manage generation of HTML components
 *	Only common components must be here.
 *
 *  TODO Merge all function load_cache_* and loadCache* (except load_cache_vatrates) into one generic function loadCacheTable
 */
class getUtil
{
	var $db;
	var $error;
	var $num;
	var $lines;

	var $ref;
	var $label;
	var $id;

	/**
	 * Constructor
	 *
	 * @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	function get_element_element($id,$origin,$type='source')
	{
		$id = $id+0;
		global $conf, $langs;
		$sql = "SELECT rowid,fk_source,sourcetype, fk_target, targettype ";
		$sql.= " FROM ".MAIN_DB_PREFIX."element_element";
		$sql.= " WHERE 1";
		if ($type == 'source')
		{
			$sql.= " AND sourcetype = '".trim($origin)."'";
			$sql.= " AND fk_source = ".$id;
		}
		else
		{
			$sql.= " AND targettype = '".trim($origin)."'";
			$sql.= " AND fk_target = ".$id;
		}

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$i=0;
			$num = $this->db->num_rows($result);
			$this->lines = array();
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				$line = new stdClass();
				$line->fk_source = $obj->fk_source;
				$line->fk_target = $obj->fk_target;
				$line->sourcetype = $obj->sourcetype;
				$line->targettype = $obj->targettype;
				$this->lines[$i] = $line;
				$i++;
			}
			$this->db->free($result);
			return $num;
		}
		return 0;
	}

	function fetch_departament($id,$ref='')
	{
		global $conf, $langs;
		$sql = "SELECT rowid,ref, label ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament";
		if (!empty($ref))
		{
			$sql.= " WHERE ref = '".$ref."'";
			$sql.= " AND entity = ".$conf->entity;
		}
		else
			$sql.= " WHERE rowid = ".$id;

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($result);
			$obj = $this->db->fetch_object($result);

			$this->id = $obj->rowid;
			$this->ref = $obj->ref;
			$this->label = $obj->label;
			$this->db->free($result);
			return $num;
		}
		else
			return -1;
	}



}

