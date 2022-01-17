<?php

// Input values
$x = [50,76];
$y = [-162, -134];

define( 'X', $x );
define( 'Y', $y );

// X velocity cannot be greater than distance to right side of target's area
$x_max = max( $x );

// X velocity loosing 1 each step must at least reach left side of target's area
$x_min = get_min_x( min( $x ) );


function get_min_x( $x ) {
	$num = 0;
	while( $x >= 0  ) {
		$x -= $num;
		$num++;
	}
	return $num - 1;
}

function in_target( $point ) {
	global $in_target;
	if( $point['x'] >= min(X) && $point['x'] <= max(X) && $point['y'] >= min(Y) && $point['y'] <= max(Y) ) {
		$in_target[] = [$point['x'], $point['y']];
		return true;
	}
	return false;
}

function missed_target( $point ){
	if( $point['x'] > max(X) || $point['y'] < min(Y) ) return true;
	return false;
}

global $in_target;
$in_target = [];

$max_y = 0;
$initial_cords = [];

// For each available x
for ($i = $x_min; $i <= 64; $i++) {

	// Fire with y velocity between 1 and 50
	for ($k = min( Y ); $k <= abs( min( Y ) ) - 1; $k++) {

		$vx = $i;
		$vy = $k;

		$h = 0;

		// Fire the shot
		$point = [ 'x' => 0, 'y' => 0 ];
		while ( true ) {
			if ( missed_target( $point ) ) break;

			// Take step
			$point['x'] += $vx;
			$point['y'] += $vy;

			if( in_target( $point ) ) {
				if( $h > $max_y ) {
					$max_y = $h;
					$initial_cords = [
						'x' => $i,
						'y' => $k
					];
				}
				break;
			}

			// Save height
			if( $point['y'] > $h ) $h = $point['y'];

			// Decrease speeds
			$vy--;
			if( $vx !== 0 ) {
				if( $vx > 0 ) {
					$vx--;
				} else {
					$vx++;
				}
			}

		}
	}
}

echo 'Part 1: ' . $max_y . PHP_EOL;

echo 'Part 2: ' . count( $in_target ) . PHP_EOL;