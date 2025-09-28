Para tener un mejor control de los CONTRATOS, es necesario contar con los siguientes campos (atributos adicionales)
Ingrese a:
Inicio, Configuracion, Modulos, Contratos Configuracion, Atributos Adicionales:

Crear los siguientes campos:

Orden	Etiqueta	Código atributo	Tipo				Tamaño	Único	Requerido	 
1 	Plazo 		plazo 		Numérico entero 		10 	No 	Sí 	
2 	Tipo Plazo 	cod_plazo 	Lista de selección de table 		No 	Sí

El campo cod_plazo configurar de la siguiente forma:

Etiqueta				Tipo Plazo
Código atributo				cod_plazo
Tipo					Lista de selección de table
Tamaño	
Orden					2	
Valor 					c_type_time_limit:label:rowid:: where active=1 	
Único	
Requerido				Si

Cualquier duda escribir a Ubuntu Bolivia <ramiroques@gmail.com>
 	
