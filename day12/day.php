<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );

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


//global $paths;
//$paths = 0;
//
//function find_path( $node, $dir ) {
//	global $paths;
//
//	// Dead end
//	if( empty( $dir[ $node ] ) ) return;
//
//	$goto = $dir[$node];
//
//	// End found
//	if( $node == 'end' ) {
//		$paths++;
//		return;
//	}
//
//	// Remove small cave
//	if( ctype_lower($node) ) unset( $dir[$node] );
//
//	foreach ( $goto as $route ) {
//		if( $route == $node ) continue;
//		find_path( $route, $dir);
//	}
//
//}
//find_path( 'start', $dir );


// echo 'Part 1: ' . $paths . PHP_EOL;



global $paths;
$paths = 0;

function find_path( $node, $dir, $flag, $r ) {
	global $paths;

	$r[] = $node;

	// Dead end
	if( empty( $dir[ $node ] ) ) return;

	$goto = $dir[$node];

	// Allow entering small cave twice only one time
	if( ctype_lower($node) ) {
		$ct = array_count_values( $r );
		if( isset( $ct[$node] ) && $ct[$node] == 2 ) {
			$flag = true;
			foreach ( $r as $item ) {
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
		return;
	}

	// Remove small cave
	if( ctype_lower($node) && $flag ) {
		unset( $dir[ $node ] );
	} else if( $node == 'start' ) {
		unset( $dir[ $node ] );
	}

	foreach ( $goto as $route ) {
		if( $route == $node ) continue;
		find_path( $route, $dir, $flag, $r );
	}

}
find_path( 'start', $dir, false, [] );

 echo 'Part 2: ' . $paths . PHP_EOL;