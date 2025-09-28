<?php
print '<script type="text/javascript" src="js/webcam.js"></script>	
	<script language="JavaScript">
		webcam.set_api_url( '."'register.php'".' );
		webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		webcam.set_shutter_sound( true ); // play shutter click sound
	</script>
	<script language="JavaScript">
		document.write( webcam.get_html(320, 240) );
	</script>';	

print '<form>';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="add">';

print '<input type=button value="Configure..." onClick="webcam.configure()">';
print '&nbsp;&nbsp;';
print '<input type=button value="Take Snapshot" onClick="take_snapshot()">';
print '</form>';
	
print '
	<script language="JavaScript">
		webcam.set_hook( '."'onComplete'".', '."'my_completion_handler'".' );
		
		function take_snapshot() {
			// take snapshot and upload to server
			document.getElementById('."'upload_results'".').innerHTML = '."'<h1>Uploading...</h1>'".';
			webcam.snap();
		}';
		
print '
		function my_completion_handler(msg) {
			// extract URL out of PHP output
			if (msg.match(/(http\:\/\/\S+)/)) {
				var image_url = RegExp.$1;
				// show JPEG image in page
				document.getElementById('."'upload_results'".').innerHTML = 
					'."'<h1>Upload Successful!</h1>'".' + 
					'."'<h3>JPEG URL: '".' + image_url + '."'</h3>'".' + 
					'."'<img src=".'"'." + image_url + '".'">'."';";
				
print '				// reset camera for another shot
				webcam.reset();
			}
			else alert("PHP Error: " + msg);
		}';

print '	</script>';
?>