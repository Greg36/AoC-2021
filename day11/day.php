<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );


$map = [];
foreach ( $input as $x => $row ) {
	$row = str_split( $row);
	foreach ( $row as $y => $fish ) {
		$map[$x][$y] = (int) $fish;
	}
}


global $flashes;
$flashes = 0;
function flash_fish( $map, $y, $x ) {
	global $flashes;

	// Flash the fish
	$map[$y][$x] = 'x';
	$flashes++;

	// Increase adjecent
	if( isset( $map[$y-1][$x-1] ) && is_numeric( $map[$y-1][$x-1] ) ) $map[$y-1][$x-1]++;
	if( isset( $map[$y-1][$x]   ) && is_numeric( $map[$y-1][$x]   ) ) $map[$y-1][$x]++;
	if( isset( $map[$y-1][$x+1] ) && is_numeric( $map[$y-1][$x+1] ) ) $map[$y-1][$x+1]++;

	if( isset( $map[$y][$x-1]   ) && is_numeric( $map[$y][$x-1]   ) ) $map[$y][$x-1]++;
	if( isset( $map[$y][$x+1]   ) && is_numeric( $map[$y][$x+1]   ) ) $map[$y][$x+1]++;

	if( isset( $map[$y+1][$x-1] ) && is_numeric( $map[$y+1][$x-1] ) ) $map[$y+1][$x-1]++;
	if( isset( $map[$y+1][$x]   ) && is_numeric( $map[$y+1][$x]   ) ) $map[$y+1][$x]++;
	if( isset( $map[$y+1][$x+1] ) && is_numeric( $map[$y+1][$x+1] ) ) $map[$y+1][$x+1]++;

	return $map;
}

function scan_fish( $map ) {
	$pos =  [];
	foreach ( $map as $y => $row ) {
		foreach ( $row as $x => $fish ) {
			if( is_numeric( $fish ) && $fish >= 9 ) $pos[] = [ $y, $x ];
		}
	}
	if( !empty( $pos) ) {
		return $pos;
	} else {
		return false;
	}
}

function reset_map( $map ) {
	foreach ( $map as $y => $row ) {
		foreach ( $row as $x => $fish ) {
			if( $fish === 'y' ) $map[$y][$x] = 0;
		}
	}
	return $map;
}

function check_map( $map, $step ) {
	$sum = 0;
	foreach ( $map as $row ) {
		$sum += array_sum( $row );
	}
	if( $sum === 0 ) {
		echo 'Part 2: ' . ($step + 1) . PHP_EOL;
		die();
	}
}

for ($i = 0; $i < 1000000; $i++) {

	$do_flash = scan_fish( $map);



	if( $do_flash ) {
		// Flash fish
		while ( true ) {
			$scan = scan_fish( $map );

			// No more flashes this step
			if ( $scan === false ) {
				break;
			}
			foreach ( $scan as $pos ) {
				$map = flash_fish( $map, $pos[0], $pos[1] );
			}
		}
	}

	// Increase by 1
	foreach ( $map as $y => $row ) {
		foreach ( $row as $x => $fish ) {
			$map[$y][$x]++;
		}
	}



	$map = reset_map( $map );

	check_map( $map, $i );


//	if( $i == 99 ) {
//		foreach ( $map as $row ) {
//			echo join( '', $row);
//			echo PHP_EOL;
//		}
//		die();
//	}

}


 echo 'Part 1: ' . $flashes . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;