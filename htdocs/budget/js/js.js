// JavaScript Document
$(document).ready(function() {
	 
	 // ambos procesaran en save.php
	 
	 // servira para editar los de tipo input text.
     $('.text').editable('update_task.inc.php');
	 
	 // servira para editar el cuadro combinado de paises
	 $('.select').editable('update_task.inc.php', { 
		 data   : " {'1':'Argentina','2':'Bolivia','3':'Peru', '4':'Chile'}",
		 type   : 'select',
		 submit : 'OK'
	 });
	 
	 // servira para editar el textarea.
	 $('.textarea').editable('update_task.inc.php', { 
		 type     : 'textarea',
		 submit   : 'OK'
	 });
	 	 
 });