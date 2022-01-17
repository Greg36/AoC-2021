<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );


// PART 1

$inc = 0;
foreach ( $input as $key => $line ) {
	if( $key === 0 ) continue;

	if( $line > $input[$key - 1] ) $inc++;
}

echo 'Part 1: ' . $inc . PHP_EOL;


// PART 2

$sum_inc  = 0;
$previous = $input[0] + $input[1] + $input[2];

for ($i = 0; $i < ( count( $input ) - 2 ); $i++ ) {
	$current = $input[$i] + $input[$i + 1] + $input[$i + 2];
	if( $i === 0 ) continue;

	if( $current > $previous ) $sum_inc++;
	$previous = $current;
}

echo 'Part 2: ' . $sum_inc . PHP_EOL;