<?php

require_once '../lib/lib.php';
ini_set('xdebug.max_nesting_level', 1024);

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );

//$input = [];
//$input[0] = str_split( '[7,[6,[5,[11,[1,2]]]][1,[5,[2,[2,2]]]]]' );
//$input[0] = str_split( '[[[11,7][1,2]][3,4]]' );


function explode_num( $chain ) {
	$nest = 0;
	$did_explode = false;
	do {
		if( current( $chain ) === '[' ) {
			$nest++;
		} else if( current( $chain ) === ']' ) {
			$nest--;
		}

		// Do the explosion
		if( $nest == 5 ) {
			$start = key( $chain );
			do { next( $chain ); } while( current( $chain ) != ']' );
			$end = key($chain);

			// Get the value pair
			$pair = explode( ',', join('', array_slice( $chain, $start + 1, $end - $start - 1)) );

			// Add right
			$chain = insert_number( $chain, $pair[1], 'right' );

			// Replace the exploded pair with zero
			$chain = array_merge( array_slice( $chain, 0, $start), [0] , array_slice( $chain, $end + 1) );

			// Move array pointer to num start
			while (key($chain) !== $start) next($chain);

			// Add left
			$chain = insert_number( $chain, $pair[0], 'left' );

			$did_explode = true;

			break;
		}

	} while( next( $chain ) !== false );

	reset( $chain );

//	if( $did_explode) {
//		echo 'EXP: '.  join( '', $chain) . PHP_EOL;
//	}

	return [
		'chain' => $chain,
		'exploded' => $did_explode
	];
}

// Insert number to first numeric value on left or right from current array pointer position
function insert_number( $chain, $number, $direction ) {
	while( $direction == 'right' ? next( $chain ) !== false : prev( $chain ) !== false ) {
		if( is_numeric( current( $chain ) ) ) {

			// Get start and end array position of the number
			$num_start = $num_end = key( $chain );
			while( is_numeric( $direction == 'right' ? next( $chain ) : prev( $chain ) ) ) {
				if( $direction == 'right' ) { $num_end++; } else { $num_start--; }
			}

			// Get the number and increase it by exploded value
			$num = (int) join( '', array_slice( $chain, $num_start, $num_end - $num_start + 1) ) + (int) $number;

			// Set the new number in chain
			$chain = array_merge( array_slice( $chain, 0, $num_start), str_split( $num ) , array_slice( $chain, $num_end + 1) );
			break;
		}
	}
	reset( $chain );
	return $chain;
}

function split_number( $chain ) {
	$did_split = false;

	do {
		if( is_numeric( current( $chain ) ) ){
			$num_start = $num_end = key( $chain );
			while( is_numeric( next( $chain ) ) ) {
				$num_end++;
			}
			if( $num_start == $num_end ) continue;
			$num = (int) join( '', array_slice( $chain, $num_start, $num_end - $num_start + 1) );
			$num = '[' . (int) floor( $num / 2 ) . ',' . (int) ceil( $num / 2 ) . ']';

			// Replace number with new split pair
			$chain = array_merge( array_slice( $chain, 0, $num_start), str_split( $num ) , array_slice( $chain, $num_end + 1) );
			$did_split = true;
			break;
		}
	} while( next( $chain ) !== false );

//	if( $did_split) {
//		echo 'SPL: '.  join( '', $chain) . PHP_EOL;
//	}

	return [
		'chain' => $chain,
		'splited' => $did_split
	];
}

function reduce_chain( $chain ) {
	// Explode the chain until there are no places to explode
	$explode = explode_num( $chain );
	if( $explode['exploded'] ) {
		$chain = reduce_chain( $explode['chain'] );
	}

	// Split the chain until there are no places to split
	$split = split_number( $chain );
	if( $split['splited'] ) {
		$chain = reduce_chain( $split['chain'] );
	}

	return $chain;
}

function calc_magnitude( $chain, $level = 4 ) {
	$reduced = 0;
	reset( $chain );
	$found = false;
	$nest = 0;

	do {

		if( current( $chain ) === '[' ) {
			$nest++;
		} else if( current( $chain ) === ']' ) {
			$nest--;
		}

		if( $nest == $level ) {
			$start = key( $chain );
			do { next( $chain ); } while( current( $chain ) != ']' );
			$end = key($chain);
			$pair = explode( ',', join('', array_slice( $chain, $start + 1, $end - $start - 1)) );

			$chain = array_merge( array_slice( $chain, 0, $start), [ 3 * $pair[0] + 2 * $pair[1] ] , array_slice( $chain, $end + 1) );
			$found = true;
			break;
		}
	} while( next( $chain ) !== false );

	if( ! $found ) {
		$level--;
	}


	if( $level == 0 ) {
		return $chain[0];
	} else {
		$chain = calc_magnitude( $chain, $level );
	}

	return $chain;
}

$state = array_shift( $input );
foreach ( $input as $line ) {
	$state = join( '', reduce_chain( str_split( '[' . $state . ',' . $line . ']' ) ) );
}



echo 'Part 1: ' . calc_magnitude( str_split( $state ) ) . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;