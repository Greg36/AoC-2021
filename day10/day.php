<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$scores = [
	')' => 3,
	']' => 57,
	'}' => 1197,
	'>' => 25137
];

function swap_symbol( $char ) {
	switch ( $char ) {
		case ')': return '(';
		case ']': return '[';
		case '}': return '{';
		case '>': return '<';
	}
	return false;
}

function swap_symbol_back( $char ) {
	switch ( $char ) {
		case '(': return ')';
		case '[': return ']';
		case '{': return '}';
		case '<': return '>';
	}
	return false;
}


// PART 1

$score = 0;

foreach ( $input as $line ) {
	$stack = [];
	$line = str_split( $line);

	foreach ( $line as $char ) {

		if( preg_match( '/[(<[{]/', $char) ) {
			$stack[] = $char;
			continue;
		}

		if( preg_match( '/[)\]}>]/', $char) ) {
			if( array_slice($stack, -1)[0] == swap_symbol($char) ) {
				array_pop( $stack );
			} else {
				$score += $scores[$char];
				break;
			}
		}
	}
}

echo 'Part 1: ' . $score . PHP_EOL;


// PART 2

$scores = [
	')' => 1,
	']' => 2,
	'}' => 3,
	'>' => 4
];

$totals = [];

foreach ( $input as $key => $line ) {
	$stack = [];
	$line = str_split( $line);
	$corrupted = false;

	foreach ( $line as $char ) {

		if( preg_match( '/[(<[{]/', $char) ) {
			$stack[] = $char;
			continue;
		}

		if( preg_match( '/[)\]}>]/', $char) ) {
			if( array_slice($stack, -1)[0] == swap_symbol($char) ){
				array_pop( $stack );
			} else {
				$corrupted = true;
				break;
			}
		}
	}

	// Incomplete line
	if( ! empty( $stack ) && ! $corrupted ) {
		$score = 0;
		$stack = array_reverse( $stack );

		foreach ( $stack as $item ) {
			$score = $score * 5;
			$score += $scores[ swap_symbol_back( $item ) ];
		}

		$totals[] = $score;
	}
}

sort( $totals );

// Get the middle index
$index = count( $totals );
$index--;
$index = floor($index / 2);

echo 'Part 2: ' . $totals[$index] . PHP_EOL;