<style type="text/css" media="screen">
	div#debug {
		padding: 10px;
	}
	div#debug h1 {
		cursor: pointer;
		color: maroon;
		padding-bottom: 10px;
		font-weight: normal;
		font-size: 120%;
	}
	div#debug table {
		width: 100%;
	}
	div#debug table thead td {
		background-color: lightgray;
		color: navy;
	}
	div#debug table td {
		border: 1px dotted silver;
		padding: 1px 5px;
	}
	div#debug div.query {
		font-family: "Courier New", Courier, mono;
		font-size: 1em;
	}
	table#debug {
		display: none;
	}
</style>
<div id="debug">
	<h1>informazioni di debug</h1>
	<table id="debug" width="100%">
		<thead><tr><td style="min-width: 200px">elemento</td><td>tempo (sec.)</td><td>dettagli</td></tr></thead>
		<tbody>
	<?php
		foreach ($debug as $debugItem) {
			echo "<tr><td>{$debugItem['item']}</td><td>{$debugItem['time']}</td><td>{$debugItem['details']}</td></tr>\n";
		}
	?>
		</tbody>
	</table>
</div>
<script type="text/javascript" charset="utf-8">
	$("div#debug h1").click(function() {
		$("table#debug").toggle();
	});
</script>
