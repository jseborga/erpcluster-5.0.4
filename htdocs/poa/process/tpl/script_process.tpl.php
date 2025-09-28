<iframe id="iframe" src="actualiza_proc.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
  function CambiarURLFramecuce(id,idReg,rowid,di_){
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
      document.getElementById('iframe').src= 'actualiza_proc.php?id='+rowid+'&di_'+rowid+'='+di_+'&action=updateproc';
      window.location.reload();
}
</script>

<script type="text/javascript">
  function CambiarURLFramecode(id,idReg,rowid,di_){
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
      document.getElementById('iframe').src= 'actualiza_proc.php?id='+rowid+'&df_'+rowid+'='+di_+'&action=updatecode';
      window.location.reload();
}
</script>
