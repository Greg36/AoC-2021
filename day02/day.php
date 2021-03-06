<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );


// PART 1

$y = 0;
$z = 0;

foreach ( $input as $item ) {
	$dir = explode( ' ', $item );
	switch ( $dir[0] ) {
		case 'up':
			$z -= $dir[1];
			break;
		case 'down':
			$z += $dir[1];
			break;
		case 'forward':
			$y += $dir[1];
			break;
	}
}

echo 'Part 1: ' . $y * $z . PHP_EOL;


// PART 2

$aim = 0;
$y = 0;
$z = 0;

foreach ( $input as $item ) {
	$dir = explode( ' ', $item );
	switch ( $dir[0] ) {
		case 'up':
			$aim -= $dir[1];
			break;
		case 'down':
			$aim += $dir[1];
			break;
		case 'forward':
			$y += $dir[1];
			$z += ( $aim * $dir[1] );
			break;
	}
}

echo 'Part 2: ' . $y * $z . PHP_EOL;