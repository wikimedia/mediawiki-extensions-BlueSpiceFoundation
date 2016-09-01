<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../resources/icomoon/icomoon.icons.css" />
		<link rel="stylesheet" href="../resources/bluespice/bluespice.icons.css" />
		<title>BlueSpice Icon set</title>
		<style>
			table {
				width: 100%;
				font-family: arial, sans-serif;
			}

			.icon {
				background-color: #f4f4f4;
				box-shadow: inset 0 -2px 0 rgba(0,0,0,.1);
				padding: 20px;
				width: 30px;
				font-size: 16pt;
			}

			.text {
				background-color: #f4f4f4;
				box-shadow: inset 0 -2px 0 rgba(0,0,0,.1);
				padding: 20px;
				width: 32%;
				font-size: 16pt;
			}

			a {
				text-decoration:none;
				color: black;
				font-size: 16pt;
			}

		</style>
	</head>
	<body>
	<table>
<?php
	$cssFile = file_get_contents( __DIR__ . '/../resources/icomoon/icomoon.icons.css' );
	$lines = explode( "\n", $cssFile );

	echo "\t\t<tr>\n";
	$rowcount = 0;

	for( $i=0; $i < count( $lines ); $i++ ) {
		$className = null;

		 if( eregi( '\.icon-', $lines[$i] ) ) {
			$pos = strpos( $lines[$i],':' );
			$className = substr( $lines[$i],1,$pos - 1 );
 ?>
			<td class="icon"><a href="#" class="<?php echo  $className ;?>"></a></td>
			<td class="text"><p><?php echo $className;?></p></td>
<?php
		 if( $rowcount < 6 )
			{
				$rowcount++;
			}else if ( $rowcount == 6 ){
				$rowcount = 0;
				echo "\t\t</tr>\n\t\t<tr>\n";
			}
		}
	}
?>
	</table>
	</body>
</html>