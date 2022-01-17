<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$algo = $input[0];
$algo = str_replace( array( '#', '.' ), array( 1, 0 ), $algo );
$image = array_slice( $input, 2 );

$pixels = [];

// Prepare the image
foreach ( $image as $y => $row ) {
	$row = str_split( $row );
	foreach ( $row as $x => $item ) {
		if( $item == '#' ) {
			$pixels[ $y ][ $x ] = 1;
		} else {
			$pixels[ $y ][ $x ] = 0;
		}

	}
}

function enchance_image( $input, $algo, $outer ) {
	$output = [];
	$lights = 0;

	for ( $y = -1; $y <= sizeof( $input ); $y++ ) {
		for ( $x = -1; $x <= sizeof( $input[0] ); $x++ ) {
			$code = '';

			// Get 9 values
			for ($i = -1; $i <= 1; $i++ ) {
				for ($k = -1; $k <= 1; $k++ ) {
					$code .= $input[ $y + $i ][ $x + $k ] ?? $outer;
				}
			}

			$value = $algo[ base_convert( $code, 2, 10 ) ];
			$lights += $value;

			// Add 1 as we start from -1 offset
			$output[$y + 1 ][$x + 1] = $value;
		}
	}
	return [ $output, $lights ];
}


// PART 1

$outer = 0;
$original = $pixels;

for ($i = 0; $i < 2; $i++) {
	$output = enchance_image( $pixels, $algo, $outer );
	$pixels = $output[0];
	$lights = $output[1];

	$bin   = str_pad( "", 9, $outer );
	$outer = $algo[ base_convert( $bin, 2, 10 ) ];
}

echo 'Part 1: ' . $lights . PHP_EOL;


// PART 2

$pixels = $original;
$outer = 0;

for ($i = 0; $i < 50; $i++) {
	$output = enchance_image( $pixels, $algo, $outer );
	$pixels = $output[0];
	$lights = $output[1];

	$bin   = str_pad( "", 9, $outer );
	$outer = $algo[ base_convert( $bin, 2, 10 ) ];
}

echo 'Part 2: ' . $lights . PHP_EOL;

