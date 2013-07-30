<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<style type="text/css">
			html, body {
				margin: 0px;
				padding: 0px;
				color:#444444;
				font: 12px Arial, Verdana, Geneva, sans-serif;
			}

			body{
				background-color:#ECF1F8 !important;
				background-image:url("img/bs-bg-gradient.png") !important;
				background-position: center top !important;
				background-repeat:repeat-x !important;
			}
			#bs-setup-wrapper {
				width: 650px;
				border: 1px solid #AEC8E8;
				background-color: #FFF;
				padding: 10px;
				margin: 35px auto;
			}

			#bs-setup-footer {
				border-top: 1px solid #AEC8E8;
				text-align: right;
				font-style: italic;
				padding   : 5px;
			}

			fieldset {
				margin: 0px;
				margin-bottom: 10px;
			}

			label {
				display: inline-block;
				width: 200px;
				font-family: "Courier New", monospace;
				text-align: right;
			}
			input, select {
				margin-left: 20px;
				width: 350px;
			}

			input[type="checkbox"] {
				width: 10px;
			}

			input, textarea, select {
				padding: 3px;
				background-color: #EFF4FF;
				border: 1px solid #8393C3;
				margin-bottom: 5px;
			}

			input:focus, textarea:focus {
				background-color: #EFF4FF;
				border: 1px solid #FFAE00;
			}

			input[type="button"], input[type="reset"], input[type="submit"] {
				background-color: #D6E3FF;
				cursor: pointer;
				height: 24px;
				background: url("img/bs-button-bg.png") repeat-x;
			}

			input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover {
				background-color: #F0F5FF;
				background: url("img/bs-button-bg_ro.png") repeat-x;
			}
			#language{
				float:left;
			}
			.warning{
				color: red;
			}
			#updater_msg{
				border: 1px solid #AEC8E8;
				padding: 5px;
				display: none;
			}
			#updater_msg > img{
				display: none;
			}
			#updater_msg_details{
				overflow: scroll;
				display: none;
			}
			#updater_msg > span{
				display: none;
			}
		</style>
		<script type="text/javascript" src="../../resources/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/BsUpdater.js"></script>
		<title>BlueSpice - Updater</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>
	<div id="bs-setup-wrapper">
		<span><a href="?lang=de">Deutsch</a> | <a href="?lang=en">English</a></span>
		<img src="img/bs-logo.png" alt="BlueSpice" style="float: right"/>
		<h1>BlueSpice - Updater</h1>
		<br style="clear:both; margin-bottom: 5px" />
		<?php
		$sExecuted = 'Update.php has been executed!';
			if ( isset( $_REQUEST['lang'] ) && $_REQUEST['lang'] == 'de' ) {
				echo 'Klicken Sie <a id="updater" href="#">hier</a> um das notwendige Update f&uuml;r auszuf&uuml;hren';
				$sExecuted = 'Die update.php wurde ausgef&uuml;hrt!';
			} else {
				echo 'Click <a id="updater" href="#">here</a> to run the neccessary update on the MediaWiki database.';
			}
		?>
		<br/><br/>
		<div id="updater_msg"><img src="img/bs-ajax-loader-pik-blue.gif" class="not-loading"/><span><?php echo $sExecuted ?><br/><a id="details" href="#">Details</a></span><pre id="updater_msg_details"></pre></div>
		<br/><hr/><span><strong>Hallo Welt!</strong> - Medienwerkstatt GmbH, <a href="http://www.hallowelt.biz" title="hallowelt.biz">hallowelt.biz</a><br />Residenzstraße 2, 93047 Regensburg</span>
	</div>
	</body>
</html>