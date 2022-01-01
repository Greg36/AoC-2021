<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );

$points = [];
foreach ( $input as $item ) {
	$points[] = explode( ',', $item );
}

$fold = [
	['x' => 655],
	['y' => 447],
	['x' => 327],
	['y' => 223],
	['x' => 163],
	['y' => 111],
	['x' => 81],
	['y' => 55],
	['x' => 40],
	['y' => 27],
	['y' => 13],
	['y' => 6]
];

//$fold = [
//	['y' => 7],
//	['x' => 5]
//];

//$a= [0,1,2,3,4,5,6];
//$x = 3;
//
//$k = array_slice( $a, 0, $x);
//$c = array_slice( array_reverse( $a ), 0, $x);

function count_dots( $points ) {
	$map = [];
	$dots = 0;
	foreach ( $points as $point ) {
		$map[$point[1]][$point[0]] = 'x';
	}
	foreach ( $map as $row ) {
		$dots += count( $row );
	}
	return $dots;
}


//foreach ( $fold as $cord ) {
//	$from = array_key_first( $cord );
//	$length = array_values( $cord )[0];
//
//	foreach ( $points as $key => $point ) {
//		if( $from == 'x' ) {
//			if( $point[0] > $length ) {
//				$points[$key][0] = $length * 2 - $point[0];
//			}
//		} else {
//			if( $point[1] > $length ) {
//				$points[$key][1] = $length * 2 - $point[1];
//			}
//		}
//	}
//
//	$dots = count_dots( $points );
//	 echo 'Part 1: ' . $dots . PHP_EOL;
//	 die();
//
//}

foreach ( $fold as $cord ) {
	$from = array_key_first( $cord );
	$length = array_values( $cord )[0];

	foreach ( $points as $key => $point ) {
		if( $from == 'x' ) {
			if( $point[0] > $length ) {
				$points[$key][0] = $length * 2 - $point[0];
			}
		} else {
			if( $point[1] > $length ) {
				$points[$key][1] = $length * 2 - $point[1];
			}
		}
	}
}

$map = [];
foreach ( $points as $point ) {
	$map[$point[1]][$point[0]] = 'x';
}

for ( $y = 0; $y < 6; $y++ ) {
	for ($x = 0; $x < 40; $x++) {
		if( isset( $map[$y][$x]) ) {
			echo '#';
		} else {
			echo '.';
		}
	}
	echo PHP_EOL;
}





// echo 'Part 2: ' . $correct . PHP_EOL;