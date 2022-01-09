<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );

$instructions = [];
foreach ( $input as $line ) {
	$instructions[] = explode( ' ', $line );
}


function solve( $inst, $part ) {

	$terms = [];
	// Input program is 18 instructions repeated 14 times and their value differs only in 3 of them
	// 14 digit number is the target
	for ( $i = 0; $i < count( $inst ); $i += 18 ) {
		$terms[] = [
			$inst[ $i + 4 ][2],
			$inst[ $i + 5 ][2],
			$inst[ $i + 15 ][2]
		];
	}

	$prevs = [];
	$digits = [];

	foreach ( $terms as $key => $term ) {
		// We don't need to count up or down the whole number and can treat each digit separately as base 26 number
		// in such case if program would process instructions orderly we would add a new digit to the number when
		// 5th instruction would be 1, so we just store the digit position counting from left and store the num
		if( $term[0] == 1 ) {
			$prevs[] = [ $key, $term[2] ];
		} else {

			$pair = array_pop( $prevs );
			$complement = $pair[1] + $term[1];
			if( $part == 1 ) {
				$digits[ $pair[0] ] = min( 9, 9 - $complement );
			} else {
				$digits[ $pair[0] ] = max( 1, 1 - $complement );
			}
			$digits[$key] = $digits[ $pair[0] ] + $complement;
		}
	}

	ksort( $digits );
	return join( '', $digits );
}


echo 'Part 1: ' . solve( $instructions, 1 ) . PHP_EOL;

echo 'Part 2: ' . solve( $instructions, 2 ) . PHP_EOL;

//function aul( $inp, $instructions ) {
//	$inp = str_split( $inp );
//
//	$v['w'] = $v['x'] = $v['y'] = $v['z'] = 0;
//
//	foreach ( $instructions as $i ) {
//		switch ( $i[0] ) {
//			case 'inp':
//				$v[ $i[1] ] = (int) array_shift( $inp );
//				break;
//			case 'add':
//				$v[ $i[1] ] += is_numeric( $i[2] ) ? $i[2] : $v[ $i[2] ];
//				break;
//			case 'mul':
//				$v[ $i[1] ] *= is_numeric( $i[2] ) ? $i[2] : $v[ $i[2] ];
//				break;
//			case 'div':
//				$v[ $i[1] ] = (int) floor( $v[ $i[1] ] / $i[2] );
//				break;
//			case 'mod':
//				$v[ $i[1] ] = $v[ $i[1] ] % $i[2];
//				break;
//			case 'eql':
//				$v[ $i[1] ] = $v[ $i[1] ] == ( is_numeric( $i[2] ) ? $i[2] : $v[ $i[2] ] ) ? 1 : 0;
//				break;
//		}
//	}
//
//	if( $v['z'] === 0 ) {
//		return true;
//	}
//	return false;
//}