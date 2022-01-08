<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );

$steps = [];

// Parse steps
foreach ( $input as $line ) {
	$dir = [];
	$line = explode( ' ', $line );
	$dir['signal'] = $line[0] == 'on' ? 1 : -1;

	$line = explode( ',', $line[1] );
	foreach ( $line as $cord ) {
		$cords = explode( '..', substr( $cord, 2 ) );
		$dir[ $cord[0] ] = [ $cords[0], $cords[1] ];
	}

	if( $dir ) $steps[] = $dir;
}


//$mcstart = microtime( true );
//echo "<pre>\n";
//$input = trim( str_replace( "\r", "", file_get_contents( "input.txt" ) ) );
//
//$lines = explode( "\n", $input );
$cubes = [];

foreach ( $steps as $step ) {

	$new = [];
	foreach ( $cubes as $cube ) {
		$ax = max( $step['x'][0], $cube['x'][0] );
		$bx = min( $step['x'][1], $cube['x'][1] );
		$ay = max( $step['y'][0], $cube['y'][0] );
		$by = min( $step['y'][1], $cube['y'][1] );
		$az = max( $step['z'][0], $cube['z'][0] );
		$bz = min( $step['z'][1], $cube['z'][1] );

		// All axis must overlap for cuboids to overlap
		if ( $ax <= $bx && $ay <= $by && $az <= $bz ) {
			$overlap = [
				'signal' => - $cube['signal'],
				'x' => [ $ax, $bx ],
				'y' => [ $ay, $by ],
				'z' => [ $az, $bz ],
			];
			$new[] = $overlap;
		}
	}

	if ( $step['signal'] == 1 ) {
		$new[] = $step;
	}
	$cubes = array_merge( $cubes, $new );

}

$volume = 0;
foreach ( $cubes as $cube ) {
	$volume += $cube['signal'] * ( $cube['x'][1] - $cube['x'][0] + 1 ) * ( $cube['y'][1] - $cube['y'][0] + 1 ) * ( $cube['z'][1] - $cube['z']['0'] + 1 );
}

echo 'Part 2: ' . $volume . PHP_EOL;