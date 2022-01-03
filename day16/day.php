<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
$input = $input[0];

//$input = 'A0016C880162017C3686B18A3D4780';

// Convert input to binary
$code = '';
$input = str_split( $input );
foreach ( $input as $num ) {
	$num = base_convert( $num, 16, 2 );
	$code .= str_pad( $num, 4, 0, STR_PAD_LEFT );
}

global $v;
$v = 0;

function parse_code( $bin, $parts = [] ) {
	global $v;

	// Parse header
	$version = substr( $bin, 0, 3);
	$type = substr( $bin, 3, 3);
	$bin = substr( $bin, 6);

	// Save the version to global
	$v += (int) base_convert( $version, 2, 10);

	// 4 - litral value
	if( $type ==  '100') {

		while( $bin[0] ) $bin = substr( $bin, 5);
		$bin = substr( $bin, 5);

	} else {

		$length = ( $bin[0] === '0' ) ? 15 : 11;
		$packets = base_convert( substr( $bin, 1, $length), 2, 10);
		$bin = substr( $bin, $length + 1 );

		// Detect packet length
		if( $length == 11 ) $packets = detect_packet_length( $bin, $packets );

		parse_code( substr( $bin, 0, $packets) );
		$bin = substr( $bin,  $packets );
	}

	// Parse next section of code if we have data
	if( strlen( $bin ) >= 11 ) {
		parse_code( $bin );
	}

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


parse_code( $code );

echo 'Part 1: ' . $v . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;