<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

$all = [];
foreach ( $input as $item ) {
	$all[] = explode( '-', $item );
}

$dir = [];
foreach ( $all as $route ) {
	$dir[ $route[0] ][] = $route[1];
}
foreach ( $all as $route ) {
	$dir[ $route[1] ][] = $route[0];
}


// PART 1
global $paths;
$paths = 0;

function find_path( $node, $dir, $paths = 0 ) {

	// Dead end
	if( empty( $dir[ $node ] ) ) return $paths;

	$goto = $dir[$node];

	// End found
	if( $node == 'end' ) {
		$paths++;
		return $paths;
	}

	// Remove small cave
	if( ctype_lower($node) ) unset( $dir[$node] );

	foreach ( $goto as $route ) {
		if( $route == $node ) continue;
		$paths = find_path( $route, $dir, $paths );
	}

	return $paths;
}
$paths = find_path( 'start', $dir );

echo 'Part 1: ' . $paths . PHP_EOL;


// PART 2

function find_path_allow_twice( $node, $dir, $visited_tiwce = false, $visited = [], $paths = 0 ) {
	$visited[] = $node;

	// Dead end
	if( empty( $dir[ $node ] ) ) return $paths;

	$goto = $dir[$node];

	// Allow entering small cave twice only one time
	if( ctype_lower($node) ) {
		$ct = array_count_values( $visited );

		if( isset( $ct[$node] ) && $ct[$node] == 2 ) {
			$visited_tiwce = true;

			// If we already visited any small cave two times
			// remove all small caves from future routes
			foreach ( $visited as $item ) {
				if( ctype_lower( $item ) ) {
					unset( $goto[$item] );
					unset( $dir[$item] );
				}
			}
		}
	}

	// End found
	if( $node == 'end' ) {
		$paths++;
		return $paths;
	}

	// Remove small and start caves
	if( ctype_lower($node) && $visited_tiwce ) {
		unset( $dir[ $node ] );
	} else if( $node == 'start' ) {
		unset( $dir[ $node ] );
	}

	foreach ( $goto as $route ) {
		if( $route == $node ) continue;
		$paths = find_path_allow_twice( $route, $dir, $visited_tiwce, $visited, $paths );
	}

	return $paths;
}

$paths = find_path_allow_twice( 'start', $dir );

echo 'Part 2: ' . $paths . PHP_EOL;