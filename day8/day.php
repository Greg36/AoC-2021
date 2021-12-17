<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );



//$input = [];
//$input[] = 'acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf';



$data = [];

foreach ( $input as $item ) {
	$item = explode( ' | ', $item );
	$data[] = [
		'in' => explode( ' ', $item[0] ),
		'out' => explode( ' ', $item[1] ),
	];
}

//$nums = 0;
//foreach ( $data as $item ) {
//	foreach ( $item['out'] as $signal ) {
//		$count = strlen( trim( $signal ) );
//		if( $count === 2 || $count === 3 || $count === 4 || $count === 7 ) $nums++;
//	}
//}
//
// echo 'Part 1: ' . $nums . PHP_EOL;

/*
 * A 0
 * B 1
 * C 2
 * D 3
 * E 4
 * F 5
 * G 6
 */

$sum = 0;


foreach ( $data as $signal ) {

	$map = [];

	$prop = [];
	foreach ( $signal['in'] as $item ) {
		$prop[strlen( $item ) ][] = str_split( $item );
	}

	// A
	$map['a'] = array_values( array_diff( $prop[3][0], $prop[2][0] ) )[0];

	// F | G
	$fg = array_values( array_diff( $prop[4][0], $prop[2][0] ) );
	foreach ( $prop[5] as $item ) {
		if( isset( array_values( array_diff( $fg, $item  ) )[0] ) ) {
			$map['f'] = array_values( array_diff( $fg, $item  ) )[0];
		}
	}
	$map['g'] = array_values( array_diff( $fg, [$map['f']]  ) )[0];

	//B
	foreach ( $prop[6] as $item ) {
		if( isset( array_values( array_diff( $prop[2][0], $item  ) )[0] ) ) {
			$map['b'] = array_values( array_diff( $prop[2][0], $item  ) )[0];
		}
	}

	// E
	foreach ( $prop[6] as $item ) {
		$let = array_diff( $item, $map);
		if( count( $let ) === 2 ) $var2 = $let;
		if( count( $let ) === 3 ) $var3 = $let;
	}
	$map['e'] = array_values( array_diff( $var3, $var2  ) )[0];

	// C
	$map['c'] = array_values( array_diff( $prop[2][0], $map  ) )[0];

	// D
	$map['d'] = array_values( array_diff( $prop[7][0], $map  ) )[0];

	$map = array_flip( $map);

	// Count
	$nums = '';
	foreach ( $signal['out'] as $out ) {
		$nums .=  count_number( $out, $map);
	}

	$sum += (int) $nums;
}



function count_number( $sig, $map ) {

	$signal = [];
	foreach ( str_split( $sig ) as $letter ) {
		$signal[] = $map[$letter];
	}
	sort( $signal );
	$signal = join( '', $signal);

	switch ($signal) {
		case 'abcdef':    return 0;
		case 'bc':    return 1;
		case 'abdeg':    return 2;
		case 'abcdg':    return 3;
		case 'bcfg':    return 4;
		case 'acdfg':    return 5;
		case 'acdefg':    return 6;
		case 'abc':    return 7;
		case 'abcdefg':    return 8;
		case 'abcdfg':    return 9;
	}
}


echo 'Part 2: ' . $sum . PHP_EOL;