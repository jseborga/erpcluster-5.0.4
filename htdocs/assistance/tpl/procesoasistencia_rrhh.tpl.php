<?php
/* Copyright (C) 2017 L Miguel Mendoza  <l.mendoza.liet@gmail.com>
 *  Desarrollador PHP, JAVA , ANDROID 
 */

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="reg_Solicitud">';
    print '<input type="hidden" name="id" value="'.$object->id.'">';
    print '<input type="hidden" name="statut" value="'.$object->statut.'">';
       
    dol_fiche_head();

    print '<table class="border centpercent">'."\n";
    	//class="fieldrequired"		
		if($object->statut == 2){
      //Registro de la Hora de salida
      print '<tr  class="fieldrequired" ><td colspan="2" align="center">';
      print 'Registro de Salida ';
      print '</td></tr>';
      print '<tr  align="center"><td class="fieldrequired">';
      if ($user->rights->assistance->lic->modsalida) {
        print $form->select_date((empty($date_ini)?dol_now():$date_ini),'dr_',1,1,1,'date_reg',1);
        print '</td></tr>';
        print '</table>'."\n";
        print '</br>';
        print '<div class="center"><input type="submit" class="button" name="reg_Solicitud" value="'.$langs->trans("Register").'"> &nbsp;
		      <input type="submit" class="button" name="cancel" value="'.$langs->trans("Volver al Listado").'"></div>';
      }
      if ($user->rights->assistance->lic->regini) {
        print dol_print_date(dol_now(),'dayhour');
        print '</td></tr>';
        print '</table>'."\n";
        print '</br>';
        print '<div class="center"><input type="submit" class="button" name="reg_Solicitud" value="'.$langs->trans("Register").'"> &nbsp;
		      <input type="submit" class="button" name="cancel" value="'.$langs->trans("Volver al Listado").'"></div>';
      }
      dol_fiche_end();
    }
    
    if($object->statut == 3){
      //Registro de la Hora de entrada
      print '<tr  class="fieldrequired" ><td colspan="2" align="center">';
		  print 'Registro de Regreso';
		  print '</td></tr>';
      print '<tr  align="center"><td class="fieldrequired">';
      if($user->rights->assistance->lic->modretorno){
        print $form->select_date((empty($date_fin)?dol_now():$date_fin),'dr_',1,1,1,'date_reg',1); 
        print '</td></tr>';
        print '</table>'."\n";
        print '</br>';
        print '<div class="center"><input type="submit" class="button" name="reg_Solicitud" value="'.$langs->trans("Register").'"> &nbsp;
		      <input type="submit" class="button" name="cancel" value="'.$langs->trans("Volver al Listado").'"></div>'; 
      }
      if($user->rights->assistance->lic->regfin){
        print dol_print_date(dol_now(),'dayhour'); 
        print '</td></tr>';
        print '</table>'."\n";
        print '</br>';
        print '<div class="center"><input type="submit" class="button" name="reg_Solicitud" value="'.$langs->trans("Register").'"> &nbsp;
		      <input type="submit" class="button" name="cancel" value="'.$langs->trans("Volver al Listado").'"></div>'; 
      }
        
      dol_fiche_end();
    }
    print '</form>';
?>