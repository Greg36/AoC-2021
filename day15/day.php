<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );

$height = count( $input );
$width = strlen( $input[0] );

$map = [];
foreach ( $input as $item ) {
	$row = str_split( $item );
	$map[] = $row;
}

global $shortest;
$shortest = 99999;

global $min;
$min = [];

$pos = [
	'x' => 0,
	'y' => 0
];

$data = [
	'map' => $map,
	'w' => $width,
	'h' => $height,
	'risk' => 0,
];

function take_step($pos, $data) {
	global $shortest;
	global $min;

	// Add risk
	$data['risk'] += $data['map'][$pos['y']][$pos['x']];
	if( $data['risk'] >= $shortest ) return;


	// Check if it is fastest way to this point
	if( isset( $min[$pos['y']][$pos['x']] ) ) {
		if( $min[$pos['y']][$pos['x']] < $data['risk'] ) return;
	} else {
		$min[$pos['y']][$pos['x']] = $data['risk'];
	}


	// Mark spot visited
	$data['map'][$pos['y']][$pos['x']] = 'x';

	// Finish path
	if( $pos['y'] == $data['w'] - 1 && $pos['x'] == $data['h'] - 1 ) {
		if( $data['risk'] < $shortest ) {
			$shortest = $data['risk'];
			echo $shortest . PHP_EOL;
			return;
		}
	}

	// up
	if( $pos['y'] != 0 && $data['map'][$pos['y'] - 1][$pos['x']] != 'x' ) {
		take_step( [ 'x' => $pos['x'], 'y' => $pos['y'] - 1 ], $data );
	}

	// right
	if( $pos['x'] != $data['w'] - 1 && $data['map'][$pos['y']][$pos['x'] + 1] != 'x' ) {
		take_step( [ 'x' => $pos['x'] + 1, 'y' => $pos['y'] ], $data );
	}

	// down
	if( $pos['y'] != $data['h'] - 1 && $data['map'][$pos['y'] + 1][$pos['x']] != 'x' ) {
		take_step( [ 'x' => $pos['x'], 'y' => $pos['y'] + 1 ], $data );
	}

	// left
	if( $pos['x'] != 0 && $data['map'][$pos['y']][$pos['x'] - 1] != 'x' ) {
		take_step( [ 'x' => $pos['x'] - 1, 'y' => $pos['y'] ], $data );
	}

	return;
}

take_step( $pos, $data);

echo 'Part 1: ' . ($shortest - $map[0][0]) . PHP_EOL;

// 755 is to high

// echo 'Part 2: ' . $correct . PHP_EOL;