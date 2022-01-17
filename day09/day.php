<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$map = [];
foreach ( $input as $key => $row ) {
	$temp = str_split( $row );
	foreach ( $temp as $item ) {
		$map[ $key ][] = (int) $item;
	}
}


// PART 1

$sec = 0;
foreach ( $map as $y => $row ) {
	foreach ( $row as $x => $item ) {

		// top
		if( isset( $map[$y - 1][$x] ) && $map[$y - 1][$x] <= $item ) continue;

		// right
		if( isset( $map[$y][$x + 1] ) && $map[$y][$x + 1] <= $item ) continue;

		// left
		if( isset( $map[$y][$x - 1] ) && $map[$y][$x - 1] <= $item ) continue;

		// bottom
		if( isset( $map[$y + 1][$x] ) && $map[$y + 1][$x] <= $item ) continue;

		$sec += 1;
		$sec += $item;
	}
}

echo 'Part 1: ' . $sec . PHP_EOL;


// PART 2

$low_points = [];
foreach ( $map as $y => $row ) {
	foreach ( $row as $x => $item ) {

		// top
		if( isset( $map[$y - 1][$x] ) && $map[$y - 1][$x] <= $item ) continue;

		// right
		if( isset( $map[$y][$x + 1] ) && $map[$y][$x + 1] <= $item ) continue;

		// left
		if( isset( $map[$y][$x - 1] ) && $map[$y][$x - 1] <= $item ) continue;

		// bottom
		if( isset( $map[$y + 1][$x] ) && $map[$y + 1][$x] <= $item ) continue;

		$low_points[] = [$y, $x];
	}
}

function fill_adjecent( $map, $x, $y, $start = false ) {
	static $area;
	if( $start ) {
		$area = 1;
		$map[$y][$x] = 'x';
	}
	// top
	if( isset( $map[$y - 1][$x] ) && $map[$y - 1][$x] != 9 && $map[$y - 1][$x] != 'x' ) {
		$map[$y - 1][$x] = 'x';
		$area++;
		$map = fill_adjecent( $map, $x, $y - 1);
	}

	// right
	if( isset( $map[$y][$x + 1] ) && $map[$y][$x + 1] != 9 && $map[$y][$x + 1] != 'x' ) {
		$map[$y][$x + 1] = 'x';
		$area++;
		$map = fill_adjecent( $map, $x + 1, $y);
	}

	// left
	if( isset( $map[$y][$x - 1] ) && $map[$y][$x - 1] != 9 && $map[$y][$x - 1] != 'x' ) {
		$map[$y][$x - 1] = 'x';
		$area++;
		$map = fill_adjecent( $map, $x - 1, $y);
	}

	// bottom
	if( isset( $map[$y + 1][$x] ) && $map[$y + 1][$x] != 9 && $map[$y + 1][$x] != 'x' ) {
		$map[$y + 1][$x] = 'x';
		$area++;
		$map = fill_adjecent( $map, $x, $y + 1);
	}

	if( $start ) return $area;

	return $map;
}

$areas = [];
foreach ( $low_points as $point ) {
	$areas[] = fill_adjecent( $map, $point[1], $point[0], true);
}

// Get 3 largest basins
rsort( $areas );
$areas = array_slice( $areas, 0, 3 );

$sec = 1;
foreach ( $areas as $item ) {
	$sec = $sec * $item;
}

echo 'Part 2: ' . $sec . PHP_EOL;