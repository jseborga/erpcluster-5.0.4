$(function() {

	$('tbody').on('click','td',function() {
		/* Act on the event */
		displayForm($(this));
	});

});

function displayForm(cell){

	var column = cell.attr('class'),
	id = cell.closest('tr').attr('id'),
	cellWidth = cell.css('width'),
	prevContent = cell.text(),
	form = '<form action="javascript: this.preventDefault"><input type="text" name="newValue" size="4" value="'+prevContent+'" /><input type="hidden" name="id" value="'+id+'" /><input type="hidden" name="column" value="'+column+'" /></form>';
	cell.html(form).find('input[type=text]')
	.focus();
	cell.on('click',function() {return false;});

	cell.on ('keydown',function(e) {
		/* Act on the event */
		if (e.keyCode == 13 || e.keyCode == 9) {
		//enter
		changeField(cell, prevContent);
	} else if (e.keyCode == 27){
		cell.text(prevContent);
		cell.off('click');
	}

});

}

function changeField(cell,prevContent) {

	cell.off('keydown');
	//alert('para enviar');
	input = cell.find('form').serialize();
	//alert(input);
	$.ajax({
		url: 'update_task.inc.php',
		type: 'POST',
		timeout: 10000,
		data: input,
		beforeSend: function(){
			cell.html('Enviando registros...');
		},
		error: function(){
			cell.html(prevContent);
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			cell.html(respuesta);
		}
	});
	cell.off('click');

}

