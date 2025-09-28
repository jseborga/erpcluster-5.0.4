<?php
//estado
print '<tr>';
print '<th>';
print $langs->trans("E");
print '</th>';
//structure
print '<th>';
print $langs->trans("Meta");
print '</th>';    

    if ($numCol[1]==true)
      {
	print '<th>';
	print $langs->trans("Label");
	print '</th>';
      }
    if ($numCol[2]==true)
      {
	print '<th class="left yellowblack">';
	print $langs->trans("Pseudonym");
	print '</th>';
      }

    //partida
    print '<th>';
    print $langs->trans('Partida');
    print '</th>';

    //presupuesto
    print '<th>';
    if ($numCol[91])
      print $langs->trans('Initialbudget');
    if ($numCol[92])
      print $langs->trans('Approved budget');
    print '</th>';

    if ($numCol[71])
      {
	if ($lVersion)
	  {
	    print '<th class="left yellowblack">';
	    print $langs->trans('Reformulated').' '.$nVersion;
	    print '</th>';
	    print '<th class="left yellowblack">';
	    print $langs->trans('N. Reform');
	    print '</th>';
	  }
	else
	  {
	    print '<th class="left yellowblack">';
	    print $langs->trans('Reformulated').' '.$nVersion;
	    print '</th>';
	    print '<th class="left yellowblack">';
	    print $langs->trans('N. Reform');
	    print '</th>';
	  }
      }

    if ($numCol[72])
      {
	//7 total presup
	if ($numCol[7]==true)
	  {
	    print '<th>';
	    print $langs->trans("Pending approval").' '.$nVersion;
	    print '</th>';
	  }
	else
	  {
	    print '<th>';
	    print $langs->trans("Approved budget").' '.$nVersion;
	    print '</th>';
	  }
      }
    if ($numCol[73])
      {
	print '<th>';
	print $langs->trans("Porcent");
	print '</th>';
      }

    if ($numCol[9]==true)
      {
	print '<th>';
	print $langs->trans("Preventive");
	print '</th>';
      }
    if ($numCol[10]==true)
      {
	print '<th>';
	print $langs->trans("Preventive").'<br>'.'%';
	print '</th>';
      }
    if ($numCol[15]==true)
      {
	print '<th>';
	print $langs->trans("Balance");
	print '</th>';
      }
    if ($numCol[11]==true)
      {
	print '<th>';
	print $langs->trans("Committed");
	print '</th>';
      }
    if ($numCol[12]==true)
      {
	print '<th>';
	print $langs->trans("Committed").'<br>'.'%';
	print '</th>';
      }
    if ($numCol[16]==true)
      {
	print '<th>';
	print $langs->trans("Balancecommitted");
	print '</th>';
      }

    if ($numCol[13]==true)
      {
	print '<th>';
	print $langs->trans("Accrued");
	print '</th>';
      }
    if ($numCol[14]==true)
      {
	print '<th>';
	print $langs->trans("Accrued").'<br>'.'%';
	print '</th>';
      }
    if ($numCol[17]==true)
      {
	print '<th>';
	print $langs->trans("Balanceaccrued");
	print '</th>';
      }
    if ($opver == true)
      {
	print '<th>'.$langs->trans('En').'</th>';
	print '<th>'.$langs->trans('Fe').'</th>';
	print '<th>'.$langs->trans('Ma').'</th>';
	print '<th>'.$langs->trans('Ap').'</th>';
	print '<th>'.$langs->trans('My').'</th>';
	print '<th>'.$langs->trans('Ju').'</th>';
	print '<th>'.$langs->trans('Jl').'</th>';
	print '<th>'.$langs->trans('Au').'</th>';
	print '<th>'.$langs->trans('Se').'</th>';
	print '<th>'.$langs->trans('Oc').'</th>';
	print '<th>'.$langs->trans('No').'</th>';
	print '<th>'.$langs->trans('De').'</th>';
      }

    //user
    print '<th>';
    print $langs->trans("User");
    print '</th>';    

if ($numCol[321])
  {
    print '<th>';
    print $langs->trans("Date");
    print '</th>';    
  }
if ($numCol[322])
  {
    print '<th>';
    print $langs->trans("Seguimiento");
    print '</th>';    
  }
if ($numCol[323])
  {
    print '<th>';
    print $langs->trans("Accion a seguir");
    print '</th>';    
  }
    //instruction
    // if ($conf->poai->enabled)
    //   {
    // 	if ($numCol[93])
    // 	  {
    // 	    print '<div id="instruction" class="left title">';
    // 	    print $langs->trans("Hito");
    // 	    print '</div>';
    // 	  }
    //   }
    //pac
    if ($numCol[81]==true)
      {
	print '<th>';
	print $langs->trans("PAC");
	print '</th>';
      }
    if ($numCol[82]==true)
      {
	print '<th>';
	print $langs->trans("PAC Ini");
	print '</th>';
      }
    if ($numCol[83]==true)
      {
	print '<th>';
	print $langs->trans("PAC Pub");
	print '</th>';
      }
    if ($numCol[84]==true)
      {
	print '<th>';
	print $langs->trans("PAC Total");
	print '</th>';
      }
    if ($numCol[85]==true)
      {
	print '<th>';
	print $langs->trans("PAC Saldo");
	print '</th>';
      }


    //action
    $nAction =2;
    include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/action.tpl.php';

    // print '<div id="action_" class="left title">';
    // print $langs->trans("Action");
    // print '</div>';    

print '</tr>';
    //print '<div class="clear"></div>';

?>