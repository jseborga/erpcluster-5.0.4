<?php
  //buscamos las modalidades de contratacion por el monto

print select_tables($object->fk_type_con,'fk_type_con','',1,0,'05',$object->amount);


?>