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
</dl>