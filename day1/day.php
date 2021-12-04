<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );

$inc = 0;
foreach ( $input as $key => $line ) {
	if( $key === 0 ) continue;

	if( $line > $input[$key - 1] ) $inc++;
}

echo 'Part 1: ' . $inc . PHP_EOL;



// PART 2

$sum_inc = 0;
$prev = $input[0] + $input[1] + $input[2];
for ($i = 0; $i < ( count( $input ) - 2 ); $i++ ) {
	$cur = $input[$i] + $input[$i + 1] + $input[$i + 2];
	if( $i === 0 ) continue;
	if( $cur > $prev ) $sum_inc++;
	$prev = $cur;
}

 echo 'Part 2: ' . $sum_inc . PHP_EOL;