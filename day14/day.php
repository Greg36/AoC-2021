<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );

$rules = [];
foreach ( $input as $item ) {
	$rule = explode( ' -> ', $item );
	$rules[ $rule[0] ] = $rule[1];
}

$poly = 'ONHOOSCKBSVHBNKFKSBK';
//$poly = 'NNCB';

//
//$poly = str_split( $poly );
//
//for ( $i = 0; $i < 10; $i++ ) {
//	for ($p = 0; $p < count( $poly ) - 1; $p++) {
//		$ele = $poly[$p] . $poly[$p + 1];
//		array_splice( $poly, $p + 1, 0, $rules[$ele] );
//		$p++;
//	}
//}
//
//$poly = array_values( array_count_values( $poly ) );
//
//echo 'Part 1: ' . ( max( $poly ) - min( $poly ) ) . PHP_EOL;

$poly = str_split( $poly );
$keys = [];
for ($p = 0; $p < count( $poly ) - 1; $p++) {
	$keys[$poly[$p] . $poly[$p + 1]] = 1;
}
foreach ( $rules as $key => $rule ) {
	if( ! isset( $keys[$key] ) ) $keys[$key] = 0;
}

// process chain
for ( $i = 0; $i < 40; $i++ ) {

	foreach ( $keys as $key => $count ) {
		if( $count < 1 ) continue;
		$letter = $rules[$key];
		$keys[$key] -= $count;
		$keys[ $key[0] . $letter ] += $count;
		$keys[ $letter . $key[1] ] += $count;
	}
}

// Count letters
$letters = [];
foreach ( $keys as $key => $value ) {
	if( !isset( $letters[ $key[0] ] ) ) {
		$letters[ $key[0] ] = $value;
	} else {
		$letters[ $key[0] ] += $value;
	}

	if( !isset( $letters[ $key[1] ] ) ) {
		$letters[ $key[1] ] = $value;
	} else {
		$letters[ $key[1] ] += $value;
	}
}

foreach ( $letters as $key => $letter ) {
	$letters[$key] = ceil( $letter / 2 );
}

echo 'Part 2: ' . ( max( $letters ) - min( $letters ) ) . PHP_EOL;