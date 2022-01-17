<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );


// PART 1

$total = count( $input ) / 2;

$gamma = [];
for ( $i = 0; $i < 12; $i++) {
	$val = 0;

	foreach ( $input as $item ) {
		$val += ( bindec( $item ) >> $i ) % 2;
	}

	if( $val >= $total ) $gamma[] = 1; else $gamma[] = 0;
}

// Get the opposite numbers from most popular
$epsilon = $gamma;
foreach ( $epsilon as $key => $item ) {
	$epsilon[ $key ] = $item ? '0' : '1';
}

$gamma   = join( '', array_reverse( $gamma ) );
$epsilon = join( '', array_reverse( $epsilon ) );

echo 'Part 1: ' . bindec( $gamma) * bindec( $epsilon) . PHP_EOL;


// PART 2

$input_orig = $input;

for ( $i = 0; $i < 12; $i++) {

	if( count( $input ) === 1 ) break;

	// Check most popular
	$val = 0;
	$num = 0;
	foreach ( $input as $item ) {
		$val += (int) $item[$i];
	}

	// its 1
	if( $val >= ( count( $input ) / 2 ) ) $num = 1;

	foreach ( $input as $key => $item ) {
		if( count( $input ) === 1 ) break;
		if( $item[$i] == $num ) {
			unset( $input[$key]);
		}
	}
}
$oxygen = bindec( array_values( $input )[0] );


$input = $input_orig;

for ( $i = 0; $i < 12; $i++) {

	if( count( $input ) === 1 ) break;

	// Check most popular
	$val = 0;
	$num = 0;
	foreach ( $input as $item ) {
		$val += (int) $item[$i];
	}

	$num = $val < (count( $input ) / 2) ? 1 : 0;

	foreach ( $input as $key => $item ) {
		if( count( $input ) === 1 ) break;
		if( $item[$i] == $num ) {
			unset( $input[$key]);
		}
	}
}

$carbondioxide = bindec( array_values( $input )[0] );

echo 'Part 2: ' . $oxygen * $carbondioxide . PHP_EOL;