<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

global $height, $width;
$height = count( $input );
$width  = strlen( $input[0] );

$map = [];
foreach ( $input as $item ) {
	$row = str_split( $item );
	$map[] = $row;
}

define( 'ROW', strlen( $input[0] ) );
define( 'COL', count( $input ) );


// PART 1

// Structure for information of each cell
class cell {
	public $x;
	public $y;
	public $distance;

	public function __construct( $x, $y, $distance ) {
		$this->x = $x;
		$this->y = $y;
		$this->distance = $distance;
	}
}

// Utility method to check whether a point is
// inside the grid or not
function isInsideGrid($i, $j) {
	global $height, $width;
	return ($i >= 0 && $i < $width && $j >= 0 && $j < $height);
}

// Sort routes based on distance
function sr( $a, $b ) {
	if( $a->distance == $b->distance ) {
		if( $a->x != $b->x ) {
			return ( $a->x - $b->x );
		} else {
			return ( $a->y - $b->y );
		}

	}
	return ( $a->distance - $b->distance );
}

// Method returns minimum cost to reach bottom right
// from top left calculating the best distance to
// each cell using Dijkstra
function shortest($grid, $row, $col) {

	$distances = array_fill( 0, $col, array_fill( 0, $row, 1000000000));

    $dx = [-1, 0, 1, 0];
    $dy = [0, 1, 0, -1];

    $st = [];
    $st[] = new cell(0, 0, 0);

    $distances[0][0] = $grid[0][0];

    while ( ! empty( $st ) ) {

	    $k = $st[0];
		unset( $st[0] );

	    for ( $i = 0; $i < 4; $i++) {
	        $x = $k->x + $dx[$i];
	        $y = $k->y + $dy[$i];

	        if (!isInsideGrid($x, $y))
		        continue;

	        if ($distances[$x][$y] > $distances[$k->x][$k->y] + $grid[$x][$y] ) {
		        $distances[$x][$y] = $distances[$k->x][$k->y] + $grid[$x][$y];
		        $st[] = new cell($x, $y, $distances[$x][$y]);
	        }
        }

	    usort($st, 'sr');
    }

    return $distances[$row - 1][$col - 1];
}

$shortest  = shortest( $map, $width, $height );
$shortest -= $map[0][0];

echo 'Part 1: ' . $shortest . PHP_EOL;

// PART 2

$new_map = [];

// Extend the new map by 5x5 tiles
for( $k = 0; $k < 5; $k++ ) {
	foreach ( $map as $row ) {
		$new_row = [];
		for ( $i = 0; $i < 5; $i ++ ) {
			foreach ( $row as $cell ) {
				$ele = $cell + $i + $k;
				if ( $ele > 9 ) {
					$ele = $ele % 9;
				}
				$new_row[] = $ele;
			}
		}
		$new_map[] = $new_row;
	}
}

$width  = $width  * 5;
$height = $height * 5;

$shortest  = shortest( $new_map, $width, $height );
$shortest -= $map[0][0];

echo 'Part 2: ' . $shortest . PHP_EOL;