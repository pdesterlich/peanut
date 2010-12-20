<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $layoutTitle; ?></title>
		<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<style type="text/css" media="screen">
			body {
				background-color: whitesmoke;
			}
			#container {
				background-color: white;
				width: 960px;
				margin: 20px auto;
				border: 1px solid silver;
			}
			#header {
				border-bottom: 1px dotted silver;
				padding: 20px;
			}
		</style>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<h1><?php echo $appname; ?></h1>
			</div>
			<div id="main">
				<?php echo $layoutContent; ?>
			</div>
			<div id="footer">
				
			</div>
		</div>
	</body>
</html>