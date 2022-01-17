<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );
$nums = explode( ',', array_shift( $input ) );

$input = join( ' ', $input );
$input = explode( ' ', $input);
foreach ( $input as $key => $item ) {
	if( $item === '' ) unset( $input[$key]);
}
$input = array_chunk( $input, 25);

/*
 * Check each column and row on a board for 5 strikes
 */
function check_board( $board ) {
	$guesses = array_chunk( $board, 5);
	$cols = [];
	foreach ( $guesses as $k => $row ) {
		foreach ( $row as $key => $cell ) {
			$cols[$key][$k] = $cell;
		}
	}

	$guesses = array_merge( $guesses, $cols);

	foreach ( $guesses as $row ) {
		$val = array_count_values( $row );
		if( isset( $val['x'] ) && $val['x'] === 5 ) {
			return true;
		}
	}

	return false;
}


// PART 1

$bingo = false;
foreach ( $nums as $keyr => $num ) {
	if( $bingo ) break;

	// Mark the numbers
	foreach ( $input as $bnum => $board ) {
		if (  ( $key = array_search( $num, $board ) ) !== false ) {
			$input[$bnum][$key] = 'x';
		}
		if( check_board( $board ) ) {
			$bingo = [ $nums[$keyr - 1], $board ];
		}
	}
}

$sum = 0;
foreach ( $bingo[1] as $ele ) {
	if( $ele != 'x' ) $sum += $ele;
}

echo 'Part 1: ' . $sum * $bingo[0] . PHP_EOL;


// PART 2

$bingo = false;
foreach ( $nums as $keyr => $num ) {
	if( $bingo ) break;

	// Mark the numbers
	foreach ( $input as $bnum => $board ) {
		if (  ( $key = array_search( $num, $board ) ) !== false ) {
			$input[$bnum][$key] = 'x';
		}
		if( check_board( $board ) ) {
			if( count( $input) === 1 ) {
				$bingo = [ $nums[$keyr - 1 ], array_values( $input)[0], $nums[$keyr] ];
				break;
			}
			unset( $input[$bnum] );
		}
	}
}

$sum = 0;
foreach ( $bingo[1] as $ele ) {
	if( $ele != 'x' ) $sum += $ele;
}

echo 'Part 2: ' . ( $sum + $bingo[2] ) * $bingo[0] . PHP_EOL;