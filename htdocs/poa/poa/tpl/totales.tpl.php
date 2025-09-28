<?php
print '<tfoot>';
	    //TOTALES
    	print '<tr>';
	//  	print '<td class="left total">&nbsp;</td>';
	    print '<td class="total"></td>';
    	// //Estado

	    if ($numCol[1])
   	  	{
			print '<td class="total">';
			print '<span>'.$ii.'</span>';
			print '</td>';
      	}

	    if ($numCol[2])
    	{
			print '<td class="total">';
			print '<span>'.$ii.'</span>';
			print '</td>';
      	}

	    //partida
    	print '<td class="total"></td>';

	    print '<td class="total">';
    	if ($numCol[91]) print price($sumaPresup);
    	if ($numCol[92]) print price($sumaAprob);
	    print '</td>';


	    if ($numCol[71])
      	{
			print '<td class="total">';
			if ($lVersion)
	  		{
			    print '<input type="hidden" id="totrefo" value="'.$sumaRef1.'">';
		    	print '<span id="totref">'.price($sumaRef1).'</span>';
		    	print '</div>';
			    print '<div id="amount" class="left total">';
		    	print '&nbsp;';
	  		}
			else
	  		{
			    print '</div>';
	    		print '<div id="amount_" class="left total">';
			    print '&nbsp;';
	  		}
			print '</td>';
      	}

    if ($numCol[72])
      {
	print '<td class="total">';
	if ($lVersionAp)
	  {
	    print price($sumaAprob);
	  }
	print '</td>';
      }
    if ($numCol[73])
      {
	print '<td class="total">';
	print price($sumaRef1);
	print '</td>';
      }
    print '<td class="total">';
    if ($numCol[9])
      print price(price2num($sumaPrev,'MT'));
    if ($numCol[10])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaPrevm/$sumaAprob*100,'MT')).' %';
	else
	  print price(0);
      }
    if ($numCol[15])
      print price(price2num($sumaSaldop,'MT'));
    print '</td>';

    print '<td class="total">';
    if ($numCol[11])
      print price($sumaComp);
    if ($numCol[12])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaCompm/$sumaAprob*100,'MT')).' %';
	else
	  print price(0);
      }
    if ($numCol[16])
      print price(price2num($sumaSaldoc,'MT'));
    print '</td>';

    print '<td class="total">';
    if ($numCol[13])
      print price($sumaDeve);
    if ($numCol[14])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaDevem/$sumaAprob*100,'MT')).' %';
	else
	  print price(0);
      }
    if ($numCol[17])
      print price(price2num($sumaSaldod,'MT'));
    print '</td>';

    if ($opver == true && !$lMobile)
      {
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
	print '<td class="total"></td>';
      }

    print '<td class="total">';
    //nombres
    print '</td>';
   	//seguimiento
   	if ($numCol[321]) print '<td class="total"></td>';
   	if ($numCol[322]) print '<td class="total"></td>';
   	if ($numCol[323]) print '<td class="total"></td>';


    // print '<div id="instruction" class="left total">';
    // print '</div>';
    print '<td class="total">';
    print '</td>';

    print '</tr>';
print '</tfoot>';
?>