<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );


$map = [];
foreach ( $input as $row ) {
	$map[] = str_split( $row );
}

$height = count( $map );
$width = count( $map[0] );

$i = 0;
while( true ) {
	$move = 0;

	$temp = $map;
	foreach ( $temp as $y => $row ) {
		foreach ( $row as $x => $cell ) {

			if( $cell == '>' ) {
				// Check map right edge
				if( $x == $width - 1 ) {
					if( $row[0] == '.' ) {
						$map[$y][0] = '>';
						$map[$y][$x] = '.';
						$move++;
					}
				} else {
					if( $row[$x + 1] == '.' ) {
						$map[$y][$x + 1] = '>';
						$map[$y][$x] = '.';
						$move++;
					}
				}
			}
		}
	}


	$a = 'xd';

	$temp = $map;

	foreach ( $temp as $y => $row ) {
		foreach ( $row as $x => $cell ) {
			if( $cell == 'v' ) {

				// Check map bottom edge
				if( $y == $height - 1 ) {
					if( $temp[0][$x] == '.' ) {
						$map[0][$x] = 'v';
						$map[$y][$x] = '.';
						$move++;
					}
				} else {
					if( $temp[$y + 1][$x] == '.' ) {
						$map[$y + 1][$x] = 'v';
						$map[$y][$x] = '.';
						$move++;
					}
				}
			}
		}
	}

	$i++;

	if( $move === 0 ) break;
}

 echo 'Part 1: ' . $i . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;