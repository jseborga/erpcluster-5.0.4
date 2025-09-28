<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Juanjo Menent        <jmenent@2byte.es>
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
 *  \file       htdocs/comm/action/index.php
 *  \ingroup    agenda
 *  \brief      Home page of calendar events
 */

  
print "<form action=\"liste.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'create')
  print '<input type="hidden" name="action" value="add">';
if ($action == 'edit')
  {
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="id" value="'.$id.'">';
  }
dol_htmloutput_mesg($mesg);

print "<tr $bc[$var]>";
//code
print '<td>';
print select_typeprocedure($obj->code,'code','',1,0,"code");
print '</td>';

//deadlines
print '<td>';
print '<input id="deadlines" type="text" value="'.$obj->deadlines.'" name="deadlines" size="4" maxlength="6">';
print '</td>';

// groups
print '<td>';
print select_tables($obj->groups,'groups','',1,0,"05",0);
print '</td>';
   
//label
print '<td>';
print '<input id="label" type="text" value="'.$obj->label.'" name="label" size="35" maxlength="255">';
print '</td>';
    
//quant
print '<td>';
print '<input id="sequen" type="text" value="'.$obj->sequen.'" name="sequen" size="4" maxlength="6">';
print '</td>';

//quant
print '<td>';
print '<input id="quant" type="text" value="'.$obj->quant.'" name="quant" size="5" maxlength="12">';
print '</td>';

//quant
print '<td>';
print '</td>';

//actions
print '<td>';
if ($action == 'create')
  print '<input type="submit" class="button" value="'.$langs->trans("Create").'">';
if ($action == 'edit')
  print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
print '</td>';
print '</tr>';
print '</form>';


?>