<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );

$steps = [];

// Parse steps
foreach ( $input as $line ) {
	$dir = [];
	$line = explode( ' ', $line );
	$dir['signal'] = $line[0] == 'on' ? 1 : 0;

	$line = explode( ',', $line[1] );
	foreach ( $line as $cord ) {
		$cords = explode( '..', substr( $cord, 2 ) );
		$dir[ $cord[0] ] = [ $cords[0], $cords[1] ];

		// Filter cube for part 1
		if( $cords[0] > 50 || $cords[0] < -50 || $cords[1] > 50 || $cords[1] < -50 ) {
			$dir = false;
			break;
		}
	}

	if( $dir ) $steps[] = $dir;
}

// [z][y][x]
$cube = [];

foreach ( $steps as $step ) {
	for ( $z = $step['z'][0]; $z <= $step['z'][1]; $z++ ) {
		for ( $y = $step['y'][0]; $y <= $step['y'][1]; $y++ ) {
			for ( $x = $step['x'][0]; $x <= $step['x'][1]; $x++ ) {
				$cube[$z][$y][$x] = $step['signal'];
			}
		}
	}
}

$on = 0;
foreach ( $cube as $z ) {
	foreach ( $z as $y ) {
		$on += array_sum( $y );
	}
}

echo 'Part 1: ' . $on . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;