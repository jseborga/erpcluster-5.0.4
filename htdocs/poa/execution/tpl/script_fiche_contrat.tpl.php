<iframe id="iframe" src="actualiza_contrat.php" width="900" height="30" frameborder="0"></iframe>

<script type="text/javascript">
  function CambiarURLFramedp(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      if (di_ ==="")
    {

    }
      else
    {
      document.getElementById(idTwo).innerHTML = di_;
    }
      //cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&dp_'+rowid+'='+di_+'&action=updatedp';
      window.location.reload();
}
</script>
<script type="text/javascript">
  function CambiarURLFramedf(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      if (di_ ==="")
    {

    }
      else
    {
      document.getElementById(idTwo).innerHTML = di_;
    }
      //cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&df_'+rowid+'='+di_+'&action=updatedf';
      window.location.reload();
}
</script>
<script type="text/javascript">
  function CambiarURLFramedn(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      if (di_ ==="")
    {

    }
      else
    {
      document.getElementById(idTwo).innerHTML = di_;
    }
//cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&dn_'+rowid+'='+di_+'&action=updatedn';
      window.location.reload();
}
</script>
<script type="text/javascript">
  function CambiarURLFramenc(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      document.getElementById(idTwo).innerHTML = di_;
      //cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&motif_'+rowid+'='+di_+'&action=updatenc';
}
</script>

<script type="text/javascript">
  function CambiarURLFrametwo(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      var newdate = sumarfecha(nday,di_);
      if (di_ ==="")
    {
    }
      else
    {
      document.getElementById(idTwo).innerHTML = di_;
      document.getElementById(idf).innerHTML = newdate;
    }
      //cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&di_'+rowid+'='+di_+'&action=updateop';
      window.location.reload();
}
</script>
