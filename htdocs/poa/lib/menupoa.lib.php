<?php
print '<div class="left">';
print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () {';
print '$("#r1'.$k.'").change(function() {';
print ' document.fo5.action.value="menuf05";
                document.fo5.submit();
              });
          });';
print '</script>'."\n";

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" id="fo5" name="fo5">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<div class="left">';
print '<fieldset class="fieldset1" >';
print '<legend>Vistas</legend>';
print '<ul>';
print '<li><input type="radio" '.($_SESSION['filtermenupoa']['r1']==1?'checked ':'').' value="1" id="r1" name="r1">&nbsp;<label>Standard</label></li>';
print '<li><input type="radio" '.($_SESSION['filtermenupoa']['r1']==2?'checked ':'').' value="2" id="r1" name="r1">&nbsp;<label>Seguimiento</label></li>';
print '<li><input type="radio" '.($_SESSION['filtermenupoa']['r1']==3?'checked ':'').' value="3" id="r1" name="r1">&nbsp;<label>Nombres</label></li>';
// print '<li><input type="radio" '.($_SESSION['filtermenupoa']['r1']==4?'checked ':'').' value="4" name="r1">&nbsp;<label>Ejecucion</label></li>';
print '</ul>';

print '</fieldset>';
print '</div>';
print '<input type="submit" name="'.$langs->trans('Send').'" value="Enviar" />';

print '</form>';
print '</div>';

print '<div class="left">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" id="fo6" name="fo6">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<fieldset class="fieldset1">';
print '<legend>Columnas</legend>';
print '<ol>';
print '<li><input type="checkbox" '.($_SESSION['filtermenupoa']['a1']?'checked ':'').'name="a1">&nbsp;<label>'.$langs->trans('Ver planificacion productos').'</label></li>';
print '<li><input type="checkbox" '.($_SESSION['filtermenupoa']['a2']?'checked ':'').'name="a2">&nbsp;<label>Ver Tiempos</label></li>';
print '<li><input type="checkbox" '.($_SESSION['filtermenupoa']['a3']?'checked ':'').'name="a3">&nbsp;<label>Ver Efectivo</label></li>';
print '<li><input type="checkbox" '.($_SESSION['filtermenupoa']['a4']?'checked ':'').'name="a4">&nbsp;<label>Ver Ejecucion</label></li>';

print '</ol>';
print '</fieldset>';
print '</form>';
print '</div>';

print '<div class="left">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" id="fo7" name="fo7">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<fieldset class="fieldset1">';
print '<legend>Filas</legend>';
print '<dl>';
print '<dt><input type="checkbox" '.($_SESSION['filtermenupoa']['f1']?'checked ':'').'name="f1">&nbsp;<label>'.$langs->trans('Ver Filas Insumos').'</label></dt>';
print '<dt><input type="checkbox" '.($_SESSION['filtermenupoa']['f2']?'checked ':'').'name="f2">&nbsp;<label>Ver Filas Actividades</label></dt>';
print '<dt><input type="checkbox" '.($_SESSION['filtermenupoa']['f3']?'checked ':'').'name="f3">&nbsp;<label>Ver Actividades Concluidas</label></dt>';
print '<dt><input type="checkbox" '.($_SESSION['filtermenupoa']['f4']?'checked ':'').'name="f4">&nbsp;<label>En Curso</label></dt>';
print '<dt><input type="checkbox" '.($_SESSION['filtermenupoa']['f5']?'checked ':'').'name="f5">&nbsp;<label>Demorados</label></dt>';
print '<dt><input type="checkbox" '.($_SESSION['filtermenupoa']['f6']?'checked ':'').'name="f6">&nbsp;<label>Sin cronograma</label></dt>';

print '</dl>';
print '</fieldset>';
print '</form>';
print '</div>';

print '<div class="left">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" id="fo8" name="fo8">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<fieldset class="fieldset1">';
print '<legend>Filtros</legend>';
print '<ol>';
print '<li>'.'<input type="text" name="search_all" value="'.$_SESSION['filtermenupoa']['search_all'].'" size="30" class="searchBox">'.'</li>';
print '<li>';
//print '<span>'.$langs->trans("User").':</span>';
$aExcluded = array(1=>1);
print $form->select_dolusers($_SESSION['filtermenupoa']['search_login'],'search_login',1,$aExcluded,'','','','',15);
print '</li>';
$aPriority = array(-1 => 'Todos',0=>'No definido',1=>1,2=>2,3=>3,4=>4,5=>5,6=>6);
print '<li><label>Ver Prioridad</label>&nbsp;';
print $form->selectarray('search_priority',$aPriority,$_SESSION['filtermenupoa']['search_priority']);
print '</li>';
// print '<li><input type="number" size="1" min="-1" max="9" name="search_priority" value="'.$_SESSION['filtermenupoa']['search_priority'].'">&nbsp;</li>';

print '</ol>';
print '</fieldset>';
print '</form>';
print '</div>';

print '<div class="tabsActionm">';
print '<input type="submit" name="'.$langs->trans('Send').'" value="Enviar" />';

print '<span  id="'.$idTagps.'" style="visibility:visible; display:block;" onclick="visual_five('.$idTagps2.' , '.$idTagps.')">';
print '</br>';
print '<a href="#" title="'.$langs->trans('Hide').'">'.'<button>'.$langs->trans('Hide').'</button>'.'</a>';
print '</span>';
print '<span>';
print '<a href="'.$_SERVER['PHP_SELF'].'?dol_hide_leftmenu=1" title="'.$langs->trans('Viewtopmenu').'">'.'<button>'.$langs->trans('Viewtopmenu').'</button>'.'</a>';
print '</span>';
print '</div>';
print '</form>';
print '<div class="clear"></div>';
//print '<div id="result"></div>';

?>