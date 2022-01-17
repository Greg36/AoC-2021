<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$points = [];
foreach ( $input as $item ) {
	$points[] = explode( ',', $item );
}

// Folds from the input
$fold = [
	['x' => 655],
	['y' => 447],
	['x' => 327],
	['y' => 223],
	['x' => 163],
	['y' => 111],
	['x' => 81],
	['y' => 55],
	['x' => 40],
	['y' => 27],
	['y' => 13],
	['y' => 6]
];


// PART 1

function count_dots( $points ) {
	$map = [];
	$dots = 0;
	foreach ( $points as $point ) {
		$map[$point[1]][$point[0]] = 'x';
	}
	foreach ( $map as $row ) {
		$dots += count( $row );
	}
	return $dots;
}

// Do a single fold
$cord = $fold[0];
$from = array_key_first( $cord );
$length = array_values( $cord )[0];

foreach ( $points as $key => $point ) {
	if( $from == 'x' ) {
		if( $point[0] > $length ) {
			$points[$key][0] = $length * 2 - $point[0];
		}
	} else {
		if( $point[1] > $length ) {
			$points[$key][1] = $length * 2 - $point[1];
		}
	}
}

$dots = count_dots( $points );

echo 'Part 1: ' . $dots . PHP_EOL;


// PART 2

foreach ( $fold as $cord ) {
	$length = array_values( $cord )[0];
	$side = ( array_key_first( $cord ) == 'x' ) ? 0 : 1;

	foreach ( $points as $key => $point ) {
		if( $point[$side] > $length ) $points[$key][$side] = $length * 2 - $point[$side];
	}
}

// Plot points on the map
$map = [];
foreach ( $points as $point ) {
	$map[$point[1]][$point[0]] = 'x';
}


echo 'Part 2: ' . PHP_EOL;
// Show the code
for ( $y = 0; $y < 6; $y++ ) {
	for ($x = 0; $x < 40; $x++) {
		if( isset( $map[$y][$x]) ) {
			echo '# ';
		} else {
			echo '. ';
		}
	}
	echo PHP_EOL;
}