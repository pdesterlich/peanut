<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/helpers/form_helper.php
	 * helper per gestione form
	 **/

	class form
	{

		public static function open ($name, $action, $options = null, $attr = null, $hidden = null)
		{
			$options = arrays::defaults($options, array('id' => $name, 'display' => 'echo', 'method' => 'post'));
			$attributes = arrays::attributes($attr);

			$result = "<form name='{$name}' id='{$options["id"]}' action='{$action}' method='{$options["method"]}' {$attributes}>\n";

			if (is_array($hidden)) {
				foreach ($hidden as $key => $value) {
					$result .= form::hidden($key, $value);
				}
			}

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}

		public static function close ($options = null)
		{
			$options = arrays::defaults($options, array('display' => 'echo'));

			$result = "</form>\n";

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}

		public static function element ($caption, $element, $options = null) {
			$options = arrays::defaults($options, array('id' => $caption, 'display' => 'echo'));

			$result = "<p><label for='{$options["id"]}'>{$caption}</label>{$element}</p>\n";

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}

		public static function hidden ($name, $value, $options = null, $attr = null)
		{
			$options = arrays::defaults($options, array('id' => $name, 'display' => 'return'));
			$attributes = arrays::attributes($attr);

			$result = "<input type='hidden' name='{$name}' id='{$options["id"]}' value='{$value}' {$attributes}>\n";

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}

		public static function input ($name, $value = '', $options = null, $attr = null)
		{
			$options = arrays::defaults($options, array('type' => 'text', 'id' => $name, 'display' => 'return'));
			$attributes = arrays::attributes($attr);

			$htmlAttributes = "";
			if (isset($options["required"])) $htmlAttributes .= "required ";
			if (isset($options["autofocus"])) $htmlAttributes .= "autofocus ";

			$result = "<input type='{$options["type"]}' name='{$name}' id='{$options["id"]}' value='{$value}' {$attributes} {$htmlAttributes}>";
			if (isset($options["label"])) $result = "<label class='label' for='{$name}'>{$options['label']}</label>" . $result;

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}

		public static function textarea ($name, $value = '', $options = null, $attr = null)
		{
			$options = arrays::defaults($options, array('type' => 'text', 'id' => $name, 'display' => 'return'));
			$attributes = arrays::attributes($attr);

			$result = "<textarea name='{$name}' id='{$options["id"]}' {$attributes}>{$value}</textarea>";
			if (isset($options["label"])) $result = "<label for='{$name}'>{$options['label']}</label>" . $result;

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}

		public static function checkbox ($name, $caption, $checked = false, $options = null, $attr = null)
		{
			$options = arrays::defaults($options, array('id' => $name, 'display' => 'return', 'value' => '1'));
			$attributes = arrays::attributes($attr);

			$htmlAttributes = "";
			if ($checked) $htmlAttributes .= "checked ";

			$result = "<input type='checkbox' name='{$name}' id='{$options["id"]}' value='{$options["value"]}' {$attributes} {$htmlAttributes}> {$caption}";
			if (isset($options["label"])) $result = "<label for='{$name}'>{$options['label']}</label>" . $result;

			switch ($options["display"]) {
				case 'return': return $result; break;
				case 'echo': echo $result; break;
			}
		}
	}

?>