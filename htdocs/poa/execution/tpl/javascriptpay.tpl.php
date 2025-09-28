<?php
print '<script type="text/javascript">';
//print '###################JavaScript###################';
print ' $(document).ready(function() {';
//print ' // With JQuery';
print ' $("#ex13").slider({';
print ' ticks: ['.$ticks.'],';
print ' ticks_labels: ['.$ticks_label.'],';
print ' ticks_snap_bounds: 30';
print '});';

//print ' // Without JQuery';
print ' var slider = new Slider("#ex13", {';
print ' ticks: ['.$ticks.'],';
print ' ticks_labels: ['.$ticks_label.'],';
print ' ticks_snap_bounds: 30';
print '});';
print '}';
print '</script>';
?>