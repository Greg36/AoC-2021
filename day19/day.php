<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );

$scans = [];
$scan = [];

// Prepare scans arrays
foreach ( $input as $line ) {
	if( strpos( $line, '---') === 0 ) continue;
	if( $line === '' ) {
		$scans[] = $scan;
		$scan = [];
	} else {
		$cords = explode( ',', $line );
		$scan[] = [
			'x' => $cords[0],
			'y' => $cords[1],
			'z' => $cords[2]
		];
	}
}
$scans[] = $scan;


function label( $a, $b ) {
	return "{$a['x']},{$a['y']},{$a['z']}|{$b['x']},{$b['y']},{$b['z']}";
}

function cord_distance( $a, $b ) {
	return sqrt( pow( $b['x'] - $a['x'], 2 ) + pow( $b['y'] - $a['y'], 2 ) + pow( $b['z'] - $a['z'], 2 ) );
}

function find_offset( $a2, $a1, $b2, $b1 ) {
	$offset = [];

	for ($i = 0; $i < 3; $i++) {
		if( $a1[$i] - $a2[$i] == $b1[$i] - $b2[$i] ) {
			$offset[$i] = $a1[$i] - $a2[$i];
		} else {
			$offset[$i] = $a1[$i] + $a2[$i];
		}
	}

	return $offset;
}



$distances = [];

foreach( $scans as $a => $scan ) {
	$distances[$a] = [];
	foreach ( $scan as $from ) {

		foreach ( $scan as $to ) {
			$dist = cord_distance( $from, $to );
			if( $dist > 0 && ! in_array( $dist, $distances[$a] ) ) {
				$distances[$a][ label( $from, $to ) ] = $dist;
			}
		}
	}
}


for ( $s = 1; $s < ( count( $distances ) ); $s++ ) {

	// Find all lines with same length
	$same = [];
	foreach ( $distances[0] as $i => $first ) {
		foreach ( $distances[$s] as $k => $second ) {
			if( $first == $second ) {
				$same[] = [
					$i,
					$k,
					$first
				];
			}
		}
	}

	// Find matching points for each length
	$offsets = [];
	foreach ( $same as $line ) {
		$from = explode( '|', $line[0] );
		$to = explode( '|', $line[1] );

		if( isset( $offsets[$from[0]][$to[0]] ) ) {
			$offsets[$from[0]][$to[0]]++;
		} else {
			$offsets[$from[0]][$to[0]] = 1;
		}

		if( isset( $offsets[$from[0]][$to[1]] ) ) {
			$offsets[$from[0]][$to[1]]++;
		} else {
			$offsets[$from[0]][$to[1]] = 1;
		}

		if( isset( $offsets[$from[1]][$to[0]] ) ) {
			$offsets[$from[1]][$to[0]]++;
		} else {
			$offsets[$from[1]][$to[0]] = 1;
		}

		if( isset( $offsets[$from[1]][$to[1]] ) ) {
			$offsets[$from[1]][$to[1]]++;
		} else {
			$offsets[$from[1]][$to[1]] = 1;
		}
	}

	// Get matching points between scan 0 and given scan
	$relation = [];
	foreach ( $offsets as $key => $offset ) {
		$relation[ $key ] =  array_search( max( $offset ) , $offset );
	}

	// Find offset between scanners
	$pos = find_offset(
		explode( ',', current( $relation ) ),
		explode( ',', key( $relation ) ),
		explode( ',', next( $relation ) ),
		explode( ',', key( $relation ) )
	);


	$a = 'xd';


	die();

}

// echo 'Part 1: ' . $correct . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;