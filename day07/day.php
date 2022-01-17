<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );
$input = explode( ',', $input[0] );


// PART 1

$fuel = [];
for ( $i = min( $input ); $i <= max($input); $i++ ) {
	foreach ( $input as $item ) {
		if( !isset( $fuel[$i] ) ) $fuel[$i] = 0;
		$fuel[$i] += abs(  $item - $i );
	}
}
sort( $fuel);

echo 'Part 1: ' . $fuel[0] . PHP_EOL;


// PART 2

function nth_triangle( $num ) {
	return ( $num * $num + $num ) / 2;
}

$fuel = [];
for ( $i = min( $input ); $i <= max($input); $i++ ) {
	foreach ( $input as $item ) {
		if( !isset( $fuel[$i] ) ) $fuel[$i] = 0;
		$move = abs(  $item - $i );
		$fuel[$i] += nth_triangle( $move );
	}
}
sort( $fuel);

echo 'Part 2: ' . $fuel[0] . PHP_EOL;

