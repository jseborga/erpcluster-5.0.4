

<script>
$("#ex13").slider({
ticks: [ <?php echo $ticks; ?> ],
ticks_labels: [ <?php echo $ticks_label; ?> ],
ticks_snap_bounds: 30
});

var slider = new Slider("#ex13", {
ticks: [ <?php echo $ticks; ?> ],
ticks_labels: [ <?php echo $ticks_label; ?> ],
ticks_snap_bounds: 30
});
</script>