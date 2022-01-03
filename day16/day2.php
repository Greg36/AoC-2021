<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
$input = $input[0];

//$input = '9C0141080250320F1802104A08';

// Convert input to binary
$code = '';
$input = str_split( $input );
foreach ( $input as $num ) {
	$num = base_convert( $num, 16, 2 );
	$code .= str_pad( $num, 4, 0, STR_PAD_LEFT );
}

global $v;
$v = 0;

function parse_code( $bin, $parts = [], $limit = [] ) {
	global $v;

	// Parse header
	$version = substr( $bin, 0, 3);
	$type = substr( $bin, 3, 3);
	$bin = substr( $bin, 6);

	$v += (int) base_convert( $version, 2, 10);

	// 4 - litral value
	if( $type ==  '100') {
		$num = '';
		while( true ) {
			$point = $bin[0];
			$num .= substr( $bin, 1, 4);
			$bin = substr( $bin, 5);

			if( $point === '0' ) break;
		}
		$parts[] = base_convert( $num, 2, 10 );

	} else {

		$length = ( $bin[0] === '0' ) ? 15 : 11;
		$packets = base_convert( substr( $bin, 1, $length), 2, 10);
		$bin = substr( $bin, $length + 1 );

		// Detect packet length
		if( $length == 11 ) $packets = detect_packet_length( $bin, $packets );

		$sub_parts = parse_code( substr( $bin, 0, $packets), [] );
		$bin = substr( $bin,  $packets );

		// Process sub-packets
		if( !empty( $sub_parts ) ) {
			$parts[] = parse_sub_parts( $type, $sub_parts );
		}
	}


	if( strlen( $bin ) >= 11 ) {
		$parts = parse_code( $bin, $parts );
	}

	return $parts;
}


function detect_packet_length( $bin, $count ) {
	$length = 0;
	while( $count ) {
		$type = substr( $bin, 3, 3);
		$bin = substr( $bin, 6);

		$length += 6;

		if( $type == '100' ) {
			while( $bin[0] ) {
				$bin = substr( $bin, 5);
				$length += 5;
			}
			$bin = substr( $bin, 5);
			$length += 5;
		} else {
			$l = ( $bin[0] === '0' ) ? 15 : 11;
			$length += $l;
			$bin = substr( $bin, 1);
			$length++;

			$packets = base_convert( substr( $bin, 0, $l), 2, 10);
			if( $l == 15 ) {
				$length += (int) $packets;
				$bin = substr( $bin, ( (int) $packets + $l) );
			} else {
				$sub_length = detect_packet_length( substr( $bin, $l), $packets );
				$length += $sub_length;
				$bin = substr( $bin, $sub_length + $l );
			}
		}

		$count--;
	}

	// Add trailing zeros to length
	if(!  base_convert( substr( $bin, 0, 11), 2, 10) ) {
		$length += strlen( $bin );
	}

	return $length;
}

function parse_sub_parts( $type, $sub_parts ) {

	switch ( $type ) {
		case '000': // 0
			return array_sum( $sub_parts );
		case '001': // 1
			return array_reduce( $sub_parts, function( $carry, $item ){
				if( is_null( $carry ) ) return $item;
				return $carry * $item;
			} );
		case '010': // 2
			return min( $sub_parts );
		case '011': // 3
			return max( $sub_parts );
		case '101': // 5
			return ( $sub_parts[0] > $sub_parts[1] ) ? 1 : 0;
		case '110': // 6
			return ( $sub_parts[0] < $sub_parts[1] ) ? 1 : 0;
		case '111': // 7
			return ( $sub_parts[0] == $sub_parts[1] ) ? 1 : 0;
	}

	return null;
}


$p = parse_code( $code );

echo 'Part 2: ' . array_sum( $p ) . PHP_EOL;