<script type="text/javascript">

	function popupTicket()
	{
		largeur = 350;
		hauteur = 600;
		opt = 'width='+largeur+', height='+hauteur+', left='+(screen.width - largeur)/2+', top='+(screen.height-hauteur)/2+'';
		window.open('validation_ticket.php?facid=<?php echo $_GET['id']; ?>&ref=<?php echo $_GET['ref']; ?>&vf=<?php echo $_GET['vf']; ?>', 'Reimprimir Ticket (Solo en casos de error)', opt);
		//setTimeout("popup.close()",20000);
	}

	popupTicket();
</script>