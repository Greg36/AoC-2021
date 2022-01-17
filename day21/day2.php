<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$mcstart = microtime( true );

$player1 = 1 - 1;
$player2 = 3 - 1;


// All possible 27 outcomes of 3 rolls
global $states;
$states = array_fill(3, 7, 0);;
for ( $a = 1; $a <= 3; $a ++ ) {
	for ( $b = 1; $b <= 3; $b ++ ) {
		for ( $c = 1; $c <= 3; $c ++ ) {
			$states[ $a + $b + $c ] ++;
		}
	}
}

global $cache;
$cache = [];


function countWins( $p1pos, $p2pos, $p1score, $p2score, $p1turn, $rollSum ) {
	// Single player rolls the dices
	if ($p1turn) {
		$p1pos += $rollSum;
		$p1pos %= 10;
		$p1score += $p1pos + 1;
		$p1turn = ! $p1turn;
	} else {
		$p2pos += $rollSum;
		$p2pos %= 10;
		$p2score += $p2pos + 1;
		$p1turn = ! $p1turn;
	}
	if ($p1score >= 21) {
		return [1,0];
	}
	if ($p2score >= 21) {
		return [0,1];
	}

	// If neither player won roll again
	return forkUniverse( $p1pos, $p2pos, $p1score, $p2score, $p1turn );
}

function forkUniverse( $p1pos, $p2pos, $p1score, $p2score, $p1turn ) {
	global $cache;
	global $states;

	// Save call to this function based on all function arguments and if such call was made before, use result from cache
	$key = join( ',', [$p1pos, $p2pos, $p1score, $p2score, $p1turn ] );
	if ( isset( $cache[$key] ) ) return $cache[$key];


	$wins = [0,0];
	// For each possible roll
	foreach ( $states as $rollSum => $value ) {
		$w = countWins(
			$p1pos,
			$p2pos,
			$p1score,
			$p2score,
			$p1turn,
			$rollSum,
		);

		$wins[0] += $w[0] * $value;
		$wins[1] += $w[0] * $value;

	}

	$cache[$key] = $wins;
	return $wins;
}

$wins = forkUniverse( $player1, $player2, 0, 0, true );

echo 'Part 2: ' . max( $wins ) . PHP_EOL;