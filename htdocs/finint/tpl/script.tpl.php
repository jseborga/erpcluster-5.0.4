<?php
print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () { $("#codeqr").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); });'."\n";

print ' $("#fk_tas").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); });'."\n";
print ' $("#bsubm").click(function() { document.addpc.action.value="addformline"; document.addpc.submit(); });'."\n";
if ($modeaction == 'modifyvalrefr')
	print ' $("#nit").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); });'."\n";
if ($conf->fiscal->enabled)
{
	print ' $("#selectcode_f").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); });'."\n";
}
if ($conf->projet->enabled)
{
	print ' $("#fk_proj").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); });'."\n";
	for ($j = 0; $j < $nProject; $j++)
	{
		print ' $("#fk_projet__'.$j.'").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); }); '."\n";
	}
}
for ($j = 0; $j <= $loop; $j++)
{
	print ' $("#amount__'.$j.'").change(function() { document.addpc.action.value="'.$modeaction.'"; document.addpc.submit(); }); '."\n";
	print ' $("#bsubd__'.$j.'").click(function() { document.addpc.action.value="delformline"; document.addpc.submit(); }); '."\n";
}
print '});'."\n";
print '</script>'."\n";

?>