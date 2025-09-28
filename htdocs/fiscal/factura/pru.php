<?php
include 'clasec.php';
$abc = new Verhoeff();
    // paso 1
$autoriz = '800400944012';
$factura = '843713';
$nitci = '1692823';
$fecha = '20081219';
$monto = round('86733.49');
echo '<br>fac '.$factura = $abc->verhoeff_recursive($factura, 2);
echo '<br>nit '.$nitci   = $abc->verhoeff_recursive($nitci, 2);
echo '<br>fec '.$fecha   = $abc->verhoeff_recursive($fecha, 2);
echo '<br>mon '.$monto   = $abc->verhoeff_recursive($monto, 2);
echo '<br>suma ant '.$suma = bcadd(bcadd(bcadd($factura, $nitci), $fecha), $monto);
echo '<br>sumafina '.$suma = $abc->verhoeff_recursive($suma, 5);

echo '<br>res '.$num = $abc->verhoeff_recursive(2261862349,5);

?>