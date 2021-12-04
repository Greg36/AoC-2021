<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );

//$total = count( $input ) / 2;
//
//$gamma = [];
//$epsilon = [];
//
//for ( $i = 0; $i < 12; $i++) {
//	$val = 0;
//
//	foreach ( $input as $item ) {
//		$val += ( bindec( $item ) >> $i ) % 2;
//	}
//
//	if( $val >= $total ) $gamma[] = 1; else $gamma[] = 0;
//
//}
//$epsilon = $gamma;
//$gamma = join( '', array_reverse( $gamma ));
//
//foreach ( $epsilon as $key => $item ) {
//	if( $item ) {
//		$epsilon[ $key ] = '0';
//	} else {
//		$epsilon[ $key ] = '1';
//	}
//}
//
//$epsilon = join( '', array_reverse( $epsilon ) );
//
//
// echo 'Part 1: ' . bindec( $gamma) * bindec( $epsilon) . PHP_EOL;


$input2 = $input;


for ( $i = 0; $i < 12; $i++) {

	if( count( $input ) === 1 ) break;

	// Check most popular
	$val = 0;
	$num = 0;

	foreach ( $input as $item ) {
		$val += (int) $item[$i];
	}

	if( $val >= (count( $input ) / 2) ) {
		// its 1
		$num = 1;
	}

	foreach ( $input as $key => $item ) {
		if( count( $input ) === 1 ) break;
		if( $item[$i] == $num ) {
			unset( $input[$key]);
		}
	}
}
$ox = bindec( array_values( $input)[0] );

$input = $input2;


for ( $i = 0; $i < 12; $i++) {

	if( count( $input ) === 1 ) break;

	// Check most popular
	$val = 0;
	$num = 0;

	foreach ( $input as $item ) {
		$val += (int) $item[$i];
	}

	if( $val < (count( $input ) / 2) ) {
		// its 1
		$num = 1;
	} else {
		$num = 0;
	}

	foreach ( $input as $key => $item ) {
		if( count( $input ) === 1 ) break;
		if( $item[$i] == $num ) {
			unset( $input[$key]);
		}
	}
}

$co = bindec( array_values( $input)[0] );

echo 'Part 2: ' . $ox * $co . PHP_EOL;