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
		vertical-align: top;
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
		foreach ($debug as $key => $attr) {
			$data = "";
			if (array_key_exists("data", $attr)) {
				foreach ($attr["data"] as $key => $value) {
					$data .= sprintf("<div><span class='attr-name'>%s</span> %s</div>", $key, $value);
				}
			}
			if ($data == "") $data = "&nbsp;";

			printf("<tr><td>%s</td><td>%.6f</td><td>%s</td>",
				$key,
				$attr["time"],
				$data
				);
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
