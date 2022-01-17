<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$rules = [];
foreach ( $input as $item ) {
	$rule = explode( ' -> ', $item );
	$rules[ $rule[0] ] = $rule[1];
}

// The inital state from input
$poly = 'ONHOOSCKBSVHBNKFKSBK';


// PART 1

$orig = $poly;
$poly = str_split( $poly );
for ( $i = 0; $i < 10; $i++ ) {
	for ($p = 0; $p < count( $poly ) - 1; $p++) {
		$ele = $poly[$p] . $poly[$p + 1];
		array_splice( $poly, $p + 1, 0, $rules[$ele] );
		$p++;
	}
}
$poly = array_values( array_count_values( $poly ) );

echo 'Part 1: ' . ( max( $poly ) - min( $poly ) ) . PHP_EOL;


// PART 2

$poly = $orig;
$poly = str_split( $poly );
$keys = [];

// Prepare all possible key combinations in the initial poly
for ($p = 0; $p < count( $poly ) - 1; $p++) {
	$keys[$poly[$p] . $poly[$p + 1]] = 1;
}
foreach ( $rules as $key => $rule ) {
	if( ! isset( $keys[$key] ) ) $keys[$key] = 0;
}

// Process chain
// Each pair i.e. AB split by C will result in 2 pairs AC and CB each time
// so there is no need to track their position just number of occurrences,
// last step is to subtract the initial AB pair from the count as after it
// is split it will not be part of the new poly at this position
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

// Divide each pair by 2 as we count the poly string as
// a whole not individual pairs tha occur
foreach ( $letters as $key => $letter ) {
	$letters[$key] = ceil( $letter / 2 );
}

echo 'Part 2: ' . ( max( $letters ) - min( $letters ) ) . PHP_EOL;