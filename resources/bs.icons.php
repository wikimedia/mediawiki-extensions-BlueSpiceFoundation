<?php
$iconSet = isset( $_GET['icons'] ) ? $_GET['icons'] : 'bluespice';
switch( $iconSet ){
	case 'icomoon':
		$icons = 'icomoon.css';
		$icons2 = 'icomoon/icomoon.css';
		break;
	case 'entypo':
		$icons = 'entypo.css';
		$icons2 = 'entypo/entypo.css';
		break;
	case 'fontawesome':
		$icons = 'fontawesome.css';
		$icons2 = 'fontawesome/fontawesome.css';
		break;
	case 'bluespice-logo':
		$icons = 'bluespice-logo.css';
		$icons2 = 'bluespice-logo/bluespice-logo.css';
		break;
	case 'bluespice' :
		$icons = 'bluespice.icons.css';
		$icons2 = 'bluespice/bluespice.icons.css';
		break;
}
?><!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="<?php echo $icons2; ?>" />
		<title>BlueSpice Icons</title>
		<style>
			html, body {
				margin: 0;
				padding: 0;
			}

			table {
				width:850px;
				font-family: arial, sans-serif;
			}

			.icon {
				background-color: #f4f4f4;
				box-shadow: inset 0 -2px 0 rgba(0,0,0,.1);
				padding: 20px;
				width: 10px;
				font-size: 16pt;
			}

			.text {
				background-color: #f4f4f4;
				box-shadow: inset 0 -2px 0 rgba(0,0,0,.1);
				padding: 20px;
				width: 40px;
				font-size: 16pt;
			}
			.number {
				background-color: #A9E2F3;
				box-shadow: inset 0 -2px 0 rgba(0,0,0,.1);
				padding: 20px;
				width: 10;
				font-size: 16pt;
			}
			a {
				text-decoration:none;
				color: black;
				font-size: 16pt;
			}

			.setlinks {
				font-family: verdana;
				position:fixed;
				width: 100%;
				background-color: white;
				border-bottom: 1px solid
			}

			.setlinks td {
				padding: 10px;
				text-align: center;
			}

			.setlinks td:hover{
				background-color: #A9E2F3;
				color:white;
			}

			.icontable {
				width: 100%;
				border-collapse: collapse;
			}

			.icontable td {
				border: 1px solid;
				text-align: center;
			}

			.icontable tr:nth-child( 3 ) {
				border-right: 2px;
			}
		</style>
	</head>
	<body>
		<table class="setlinks">
			<tr>
				<td><a href="?icons=bluespice">bluespice</a></td>
				<td><a href="?icons=icomoon">icomoon</a></td>
				<td><a href="?icons=fontawesome">fontawesome</a></td>
				<td><a href="?icons=entypo">entypo</a></td>
				<td><a href="?icons=bluespice-logo">bluespice-logo</a></td>
			</tr>
		</table>
		<table class="icontable">
<?php
	$cssFile = file_get_contents( $iconSet . '/' . $icons );
	$lines = explode( "\n", $cssFile );

	echo "\t\t<tr>\n";
	$rowcount = 0;

	for( $i=0; $i < count( $lines ); $i++ ) {
		$className = null;
		$number = '';

		if( preg_match( '#^\.(bs-)?icon-#', $lines[$i] ) ) {
			$pos = strpos( $lines[$i],':' );
			$className = substr( $lines[$i],1,$pos -1 );

			if( preg_match( '#content: "#', $lines[$i + 1] ) ){
				$pos = strpos( $lines[$i + 1],'"' );
				$number = substr( $lines[$i + 1],12,$pos - 6 );
			}
 ?>
		<td class="icon"><a href="#" class="<?php echo  $className ;?>"></a></td>
		<td class="text"><p><?php echo $className ?></p></td>
		<td class="number"><p><?php echo $number ?></p></td>
<?php
		if( $rowcount < 3 )
			{
				$rowcount++;
			}else if ( $rowcount == 3 ){
				$rowcount = 0;
				echo "\t\t</tr>\n\t\t<tr>\n";
			}
		}
	}
?>
		</table>
	</body>
</html>