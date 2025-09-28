<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *	\file       htdocs/salary/formula/fiche.php
 *	\ingroup    Formulas
 *	\brief      Page fiche salary formulas
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulasdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/poperator.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserbonus.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/puserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictable.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$conf_db_type = $dolibarr_main_db_type;

$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");


$mesg = '';

$object  = new Pformulas($db);
$objectd = new Pformulasdetext($db);
$objecto = new Poperator($db);
$objectUb= new Puserbonus($db);
$objectU = new Puserext($db);
$objectgt= new Pgenerictable($db);
$objectC = new Pconceptext($db);

/*
 * Actions
 */



/*
 * View
 */


$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

$sql = "SELECT fk_operator, type, changefull, andor, sequen ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_formulas_det ";
$sql.= " WHERE fk_formula = ".$id;
$sql.= " AND state = 1 ";
$sql.= " ORDER BY sequen ";
$result = $db->query($sql);
$uid = 1;
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    if ($num) {
      $var=True;
      while ($i <=$num)
	{
	  $obj = $db->fetch_object($result);
	  //buscar operador
	  $objecto->fetch($obj->fk_operator);
	  if ($objecto->operator == "sum()")
	    {
	      $campo = $obj->changefull;
	      If ($obj->type == 'p_concept')
		{
		  $suma = 0;
		  $sql = "SELECT SUM(amount) AS total ";
		  $sql.= " FROM ".MAIN_DB_PREFIX."p_user_bonus"." AS ub" ;
		  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_user AS u ";
		  $sql.= " ON ub.fk_puser = u.rowid ";
		  $sql.= " WHERE u.fk_user = ".$id;
		  $sql.= " AND ub.fk_concept = ".$campo;

		  $sql.= " AND u.state = 1 ";
		  $resql = $db->query($sql);
		  if ($resql)
		    {
		      $num1 = $db->num_rows($resql);
		      $j = 0;
		      if ($num1)
			{
			  $var=True;
			  while ($j <=$num1)
			    {
			      $objsum = $db->fetch_object($resql);
			      $suma += $objsum->total;
			      $j++;
			    }
			}
		    }

		  //se recupera el valor del campo changefull en la tabla user
		  $cFormula.= " + ".$suma;

		}

	    }
	  else
	    {
	      $cFormula .= " ".$objecto->operator." ";

	      $campo = $obj->changefull;

	      If ($obj->type == 'p_users')
		{
		  //se recupera el valor del campo changefull en la tabla user
		  $objectU->fetch($uid);
		  $cFormula.= $objectU->$campo;

		}
	    }
	  $i++;
	}
      eval('$res = '.$cFormula.';');
      echo '<hr>res '.$total = $res;
    }
  }

llxFooter();

$db->close();
?>
