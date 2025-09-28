function selydestodos(form,activa)
{
	for(i=0;i<form.elements.length;i++)
	{
		if(form.elements[i].type=="checkbox")
			form.elements[i].checked=activa;
	}
}