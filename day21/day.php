<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$player1 = 1;
$player2 = 3;

$score1 = 0;
$score2 = 0;
$rolls = 0;

$dice = 1;
while ( $score1 < 1000 && $score2 < 1000 ) {

	for ($i = 1; $i<=3; $i++) {
		$player1 += $dice;
		$dice++;
		$rolls++;
		if( $dice > 100 ) $dice -= 100;
		if( $player1 > 10 ) $player1 = $player1 % 10;
		if( $player1 === 0 ) $player1 = 10;
	}
	$score1 += $player1;
	if( $score1 >= 1000 ) break;

	for ($i = 1; $i<=3; $i++) {
		$player2 += $dice;
		$dice++;
		$rolls++;
		if( $dice > 100 ) $dice -= 100;
		if( $player2 > 10 ) $player2 = $player2 % 10;
		if( $player2 === 0 ) $player2 = 10;
	}
	$score2 += $player2;
}

$looser = min( $score1, $score2 );

echo 'Part 1: ' . ( $looser * $rolls ) . PHP_EOL;
