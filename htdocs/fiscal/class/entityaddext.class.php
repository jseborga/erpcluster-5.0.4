<?php
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityadd.class.php';

class Entityaddext extends Entityadd
{
	//$campo solo dos valores  fk_entity o nit
	public function select_entity($campo='fk_entity',$selected='',$active=1,$showempty=0)
	{
		$aCampo = array('fk_entity'=>'fk_entity','nit'=>'nit');
		if (empty($aCampo[$campo])) return array(-1,-1);
		global $conf,$langs;
		$nb = 0;
		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_entity,";
		$sql .= " t.socialreason,";
		$sql .= " t.nit,";
		$sql .= " t.activity,";
		$sql .= " t.address,";
		$sql .= " t.city,";
		$sql .= " t.phone,";
		$sql .= " t.message,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if ($active)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entity as e ON t.fk_entity = e.rowid";
			$sql.= " WHERE e.active = ".$active;
		}
		$sql .= " ORDER BY t.socialreason";

		$result = $this->db->query($sql);
		
		if ($result)
		{
			$var=True;
			$num = $this->db->num_rows($result);
			$i = 0;
			if ($showempty)
				$options = '<option value="0">&nbsp;</option>';
			//armamos con los parametros de la empresa principal
			if ($campo == 'nit' && empty($conf->global->MAIN_INFO_TVAINTRA))
			{
				setEventMessages($langs->trans('The NIT is not defined in Company'),null,'errors');
				return array(-1,-1);
			}
			if ($campo == 'fk_entity')
			{
				if ($selected && 1 == trim($selected))
					$select = 'selected';
			}
			else
				if ($selected && trim($conf->global->MAIN_INFO_TVAINTRA) == trim($selected))
					$select = 'selected';

				$options.= '<option value="'.($campo == 'fk_entity'?1:$conf->global->MAIN_INFO_TVAINTRA).'" '.$select.'>'.$conf->global->MAIN_INFO_TVAINTRA.' - '.$conf->global->MAIN_INFO_SOCIETE_NOM.'</option>';
				$nb++;
				$select = '';
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($result);
					if ($obj->fk_entity != 1)
					{
						$select = '';
						if ($selected && trim($obj->$campo) == trim($selected))
							$select = 'selected';
						$options.= '<option value="'.$obj->$campo.'" '.$select.'>'.$obj->nit.' - '.$obj->socialreason.'</option>'."\n";
						$nb++;
					}
					$i++;
				}
				$this->db->free($result);
			}
			return array($nb,$options);
		}
	}

	?>