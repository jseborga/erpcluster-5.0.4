

<?php
print '<script type="text/javascript">
<!--
    function cambiarPestana( pestana ) {
        //Ocultar TODAS las pestañas';
print "        document.getElementById( 'pestana1' ).style.display = 'none';";
print "        document.getElementById( 'pestana2' ).style.display = 'none';";
print "        document.getElementById( 'pestana3' ).style.display = 'none';";
print "        document.getElementById( 'pestana4' ).style.display = 'none';";
        
print '        //Mostrar la pestaña que nos piden';
print "        document.getElementById( 'pestana' + pestana ).style.display = 'block';";
print '    }
-->
</script>';
print '<ol id="toc">
    <li><a href="javascript:cambiarPestana(1)"><span>Page 1</span></a></li>
    <li><a href="javascript:cambiarPestana(2)"><span>Page 2</span></a></li>
    <li><a href="javascript:cambiarPestana(3)"><span>Page 3</span></a></li>
    <li><a href="javascript:cambiarPestana(4)"><span>Page 4</span></a></li>
</ol>';
print '<div id="pestana1" style="display: none">Cosas de la pestaña 1</div>';
print '<div id="pestana2" style="display: none">Cosas de la pestaña 2</div>';
print '<div id="pestana3" style="display: none">Cosas de la pestaña 3</div>';
print '<div id="pestana4" style="display: none">Cosas de la pestaña 4</div>';

?>