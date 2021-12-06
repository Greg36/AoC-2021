<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );

$fishes = explode( ',', $input[0] );
foreach ( $fishes as $key => $fish ) {
	$fishes[$key] = (int) $fish;
}

//// Days
//for ($i = 0; $i < 80; $i++) {
//	foreach ( $fishes as $key => $fish ) {
//		if( $fish === 0 ) {
//			$fishes[$key] = 6;
//			$fishes[] = 8;
//		} else {
//			$fishes[$key]--;
//		}
//	}
//}
//
// echo 'Part 1: ' . count( $fishes ) . PHP_EOL;

// Days
for ( $i = 0; $i <= 8; $i++ ) $days[$i] = 0;

// Initial fishes
foreach( explode( ',', $input[0] ) as $fish )	$days[$fish]++;

for($i = 0; $i < 256; $i++) {
	$hatch = array_shift( $days );
	$days[] = $hatch;
	$days[6] += $hatch;
}

echo 'Part 2: ' . array_sum( $days ) . PHP_EOL;