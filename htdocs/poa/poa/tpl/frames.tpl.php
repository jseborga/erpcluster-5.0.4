      
<script type="text/javascript">
   function CambiarURLFrame(id,fk_structure,fk_poa_poa,partida,gestion,idReg,reform){
      var idTwo = parseInt(idReg)*100000;
      var idOne = idReg;
      var inputs = getElement(idReg+"_am");
      var amount = inputs.value;
      var inputsb = getElement(idReg+"_ap");
      var valAnt  = inputsb.value;
      var sumaRef = document.getElementById('totrefo').value;
      var sumaTot = 0;
      //alert(sumaRef);
      if (amount == '')
	{
	  alert("monto vacio");
	}
      //recuperando total
      sumaTot = parseFloat(sumaRef) -parseFloat(valAnt) + parseFloat(amount);
      //alert(sumaTot);
      //asignando nuevo valor
      document.getElementById('totref').innerHTML = sumaTot;
      document.getElementById(idTwo).innerHTML = amount;
      document.getElementById(idTwo+'_').innerHTML = reform;
      //cambiando el estado de
      visual_one(idTwo,idOne);
  document.getElementById('iframe').src= 'actualiza_reform.php?id='+id+'&fk_structure='+fk_structure+'&fk_poa_poa='+fk_poa_poa+'&partida='+partida+'&action=create&gestion='+gestion+'&amount='+amount+'&reform='+reform;
}
</script>
<iframe id="iframe" src="actualiza_reform.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
    function CambiarURLFrametwo(id,idReg,pseudonym){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_poaa");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = pseudonym;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframetwo').src= 'actualiza_poa.php?action=update&id='+id+'&pseudonym='+pseudonym;
}
</script>
<iframe id="iframetwo" src="actualiza_poa.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
    function CambiarURLFramew(id,idReg,n,ctx){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_act");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = ctx;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframew').src= 'work.php?action=add&id='+id+'&ctx='+ctx+'&n='+n;
}
</script>
<iframe id="iframew" src="work.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
    function CambiarURLFramep(id,idReg,ctx){
      var idTwo = parseInt(idReg)+100500;
      var idOne = idReg;
      var inputs = getElement(idReg+"_act");
      //asignando nuevo valor
      document.getElementById(idTwo).innerHTML = ctx;
      //cambiando el estado de
      visual_tree(idTwo,idOne);
      document.getElementById('iframep').src= 'priority.php?action=add&id='+id+'&ctx='+ctx;
}
</script>
<iframe id="iframep" src="priority.php" width="0" height="0" frameborder="0"></iframe>
   

 <script type="text/javascript">
   function CambiarURLFrameplan(ida,idb,idc,gestion,id,val){
   var idone = 'pl'+ida+'_'+idb+''+idc;
   var idtwoo = idone+'p';
   var inputs = getElement(idtwoo);
   //asignando nuevo valor
   document.getElementById(idtwoo).innerHTML = val;
   //cambiando el estado de
   visual_str(ida,idb,idc,0);
   
   document.getElementById('iframeplan').src= 'planif.php?action=add&id='+id+'&fkid='+ida+'&gestion='+gestion+'&month='+idc+'&val='+val;
}
</script>
<iframe id="iframeplan" src="planif.php" width="0" height="0" frameborder="0"></iframe>
   
