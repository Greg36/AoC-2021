<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

function print_debug( $diag ){
	for ( $i = 0; $i < 10; $i++ ) {
		for ( $j = 0; $j < 10; $j++ ) {
			if( isset( $diag[$i][$j]) ) {
				echo $diag[$i][$j];
			} else {
				echo '.';
			}
		}
		echo PHP_EOL;
	}
}


// PART 1

$diag = [];

foreach ( $input as $line ) {
	[$from, $to] = explode( ' -> ', $line);

	$from = explode( ',', $from );
	$to =  explode( ',', $to );


	if( $from[0] === $to[0] ) {

		// Vertical
		$min = min( $from[1], $to[1] );
		$max = max( $from[1], $to[1] );

		for( $i = $min; $i <= $max; $i++ ) {
			if( isset( $diag[$i][$from[0]] ) ) {
				$diag[$i][$from[0]]++;
			} else {
				$diag[$i][$from[0]] = 1;
			}
		}

	} else if( $from[1] === $to[1] ) {

		// Horizontal
		$min = min( $from[0], $to[0] );
		$max = max( $from[0], $to[0] );

		for( $i = $min; $i <= $max; $i++ ) {
			if( isset( $diag[$from[1]][$i] ) ) {
				$diag[$from[1]][$i]++;
			} else {
				$diag[$from[1]][$i] = 1;
			}
		}
	}
}

$safe = 0;
foreach ( $diag as $row ) {
	foreach ( $row as $cell ) {
		if( $cell > 1 ) $safe++;
	}
}

echo 'Part 1: ' . $safe . PHP_EOL;


// PART 2

$diag = [];

foreach ( $input as $line ) {
	[$from, $to] = explode( ' -> ', $line);

	$from = explode( ',', $from );
	$to =  explode( ',', $to );

	if( $from[0] === $to[0] ) {

		// Vertical
		$min = min( $from[1], $to[1] );
		$max = max( $from[1], $to[1] );

		for( $i = $min; $i <= $max; $i++ ) {
			if( isset( $diag[$i][$from[0]] ) ) {
				$diag[$i][$from[0]]++;
			} else {
				$diag[$i][$from[0]] = 1;
			}
		}

	} else if( $from[1] === $to[1] ) {

		// Horizontal
		$min = min( $from[0], $to[0] );
		$max = max( $from[0], $to[0] );

		for ( $i = $min; $i <= $max; $i ++ ) {
			if ( isset( $diag[ $from[1] ][ $i ] ) ) {
				$diag[ $from[1] ][ $i ] ++;
			} else {
				$diag[ $from[1] ][ $i ] = 1;
			}
		}

	} else {
		[$x1,$y1] = $from;
		[$x2,$y2] = $to;

		$xDir = $x1 > $x2 ? -1 : 1;
		$yDir = $y1 > $y2 ? -1 : 1;

		for( $x = $x1; $x != ($x2 + $xDir); $x += $xDir ){

			if( ! isset( $diag[$y1][$x] ) ){
				$diag[$y1][$x] = 1;
			} else {
				$diag[$y1][$x] += 1;
			}
			$y1 += $yDir;
		}
	}
}

$safe = 0;
foreach ( $diag as $row ) {
	foreach ( $row as $cell ) {
		if( $cell > 1 ) $safe++;
	}
}

echo 'Part 2: ' . $safe . PHP_EOL;