<?php
	session::add(array("chiave1" => mt_rand(1,9999), "chiave2" => mt_rand(1,9999)));
	dump($_SESSION);
	session::remove(array("chiave1", "chiave2"));
	dump($_SESSION);
?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("#hashajax").keypress(function() {
			$.post("index.php", { controller: "peanut", action: "ajaxhashtest", testo: $(this).val() }, function(result) { $("#hashajaxresul").html(result); });
		});
	});
</script>
<h1>Peanut test page</h1>
<dl>
	<dt>uuid
	<dd><?php echo $uuid; ?>
	<dt>hash
	<dd><?php echo $hash; ?>
	<dt>hash (ajax)
	<dd><input type="text" name="hashajax" id="hashajax"><span id="hashajaxresul"></span>
	<dt>implode
	<dd><?php echo $implode; ?>
	<dt>cipher - encrypt
	<dd><?php echo $cipher_encrypt; ?>
	<dt>cipher - decrypt
	<dd><?php echo $cipher_decrypt; ?>
	<dt>select
	<dd><?php echo form::select("test_select", array(1 => "cipolla", 2 => "peperone", 3 => "melanzana"), 2); ?>
</dl>
<ul>
<?php
	echo html::element("li", array("elemento 1", "elemento 2"));
	echo html::element("li", "elemento 3");
?>
</ul>