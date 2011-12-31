<?php
	$reloadTime = rand(5,20);
	echo html::link("home");
	echo "<p>tempo di reload: {$reloadTime} secondi</p>";
	echo "<p>record aggiunti: {$added}</p>";
	echo "<p>record eliminati: {$deleted}</p>";
	echo "<p>totale record effettivamente aggiunti: ".($added - $deleted)."</p>";
?>
<script type="text/javascript" charset="utf-8">
	function doReload() {
		window.location.reload();
	}
	window.setTimeout(function() {
	    doReload();
	}, <?php echo 1000*$reloadTime; ?>);
</script>