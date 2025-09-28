function processcalendar(e){
	var iddiv = e.id;
	var fk_calendar = document.getElementById('fk_calendar').value;
	var mes = document.getElementById('mes').value;
	var dia = document.getElementById('dia').value;
	var ano = document.getElementById('ano').value;
	var id = document.getElementById('id').value;
	var type_date = e.value;

	$.ajax({
		url: 'add_calendar.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&mes="+mes+"&dia="+dia+"&ano="+ano+"&fk_calendar="+fk_calendar+"&type_date="+type_date,
		beforeSend: function(){
			$("#listtask").html('en curso...');
		},
		error: function(){
			$("#listtask").html('');
			alert('Ha surgido un error.'+respuesta);
		}

		,
		success: function(respuesta){
			$("#listtask").html(respuesta);
		}
	});
} 
function reemplazacal(id,valor)
{
	$("#d"+id).html(valor);
}

function processline(e,line){
	var iddiv = e.id;
	var durx = 'dur_'+line;
	var sucx = 'suc_'+line;
	var prex = 'pre_'+line;

	var duration = document.getElementById(durx).value;
	var successor = document.getElementById(sucx).value;
	var predecessor = document.getElementById(prex).value;
	var id = document.getElementById('id').value;
	$.ajax({
		url: 'add_budget_duration.php',
		type: 'POST',
		timeout: 100000,
		data: "id="+id+"&fk_budget_task="+line+"&duration="+duration+"&successor="+successor+"&predecessor="+predecessor,
		beforeSend: function(){
			$("#listtask").html('Process...');
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus == 'Unauthorized') {
				alert('custom message. Error: ' + errorThrown);
			} else {
				alert('custom message. Error: ' + errorThrown);
			}
		},
		success: function(respuesta){
			$("#listtask").html(respuesta);
		}
	});
} 