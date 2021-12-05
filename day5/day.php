<?php

function print_debug( $diag ){
	for ( $i = 0; $i < 10; $i++ ) {
		for ( $j = 0; $j < 10; $j++ ) {
			if( isset( $diag[$i][$j]) ) {
				echo $diag[$i][$j];
			} else {
				echo '.';
			}
		}
		echo PHP_EOL;
	}
}

require_once '../lib/lib.php';

$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );

// [x][y]
//$diag = [];
//
//foreach ( $input as $line ) {
//	$cords = explode( ' -> ', $line);
//
//	$from = explode( ',', $cords[0] );
//	$to =  explode( ',', $cords[1] );
//
//
//	if( $from[0] === $to[0] ) {
//		// Vertical
//		$min = min( $from[1], $to[1] );
//		$max = max( $from[1], $to[1] );
//		for( $i = $min; $i <= $max; $i++ ) {
//			if( isset( $diag[$i][$from[0]] ) ) {
//				$diag[$i][$from[0]]++;
//			} else {
//				$diag[$i][$from[0]] = 1;
//			}
//		}
//
//	} else if( $from[1] === $to[1] ) {
//		// Horizontal
//		$min = min( $from[0], $to[0] );
//		$max = max( $from[0], $to[0] );
//		for( $i = $min; $i <= $max; $i++ ) {
//			if( isset( $diag[$from[1]][$i] ) ) {
//				$diag[$from[1]][$i]++;
//			} else {
//				$diag[$from[1]][$i] = 1;
//			}
//		}
//
//	}
//}
//
//$safe = 0;
//foreach ( $diag as $row ) {
//	foreach ( $row as $cell ) {
//		if( $cell > 1 ) $safe++;
//	}
//}

// echo 'Part 1: ' . $safe . PHP_EOL;




$diag = [];

foreach ( $input as $line ) {
	$cords = explode( ' -> ', $line);

	$from = explode( ',', $cords[0] );
	$to =  explode( ',', $cords[1] );

	if( $from[1] - $from[0] !== 0 ) {
		$slope = ( $to[1] - $to[0] ) / ( $from[1] - $from[0] );
	} else {
		$slope = -1;
	}

	if( $from[0] === $to[0] ) {
		// Vertical
		$min = min( $from[1], $to[1] );
		$max = max( $from[1], $to[1] );
		for( $i = $min; $i <= $max; $i++ ) {
			if( isset( $diag[$i][$from[0]] ) ) {
				$diag[$i][$from[0]]++;
			} else {
				$diag[$i][$from[0]] = 1;
			}
		}

	} else if( $from[1] === $to[1] ) {
		// Horizontal
		$min = min( $from[0], $to[0] );
		$max = max( $from[0], $to[0] );
		for ( $i = $min; $i <= $max; $i ++ ) {
			if ( isset( $diag[ $from[1] ][ $i ] ) ) {
				$diag[ $from[1] ][ $i ] ++;
			} else {
				$diag[ $from[1] ][ $i ] = 1;
			}
		}
	} else {

		$pairs = explode(" -> ",$line);

		list($x1,$y1) = explode(",",$pairs[0]);
		list($x2,$y2) = explode(",",$pairs[1]);

		$xDir = $x1 > $x2 ? -1 : 1;
		$yDir = $y1 > $y2 ? -1 : 1;

		$y = $y1;
		for($x = $x1; $x != ($x2 + $xDir); $x += $xDir){
			// echo "Diagonal point at ($x, $y) ".PHP_EOL;

			if(! isset( $diag[$y][$x] )){
				$diag[$y][$x] = 1;
			}else{
				$diag[$y][$x] += 1;
			}

			$y += $yDir;
		}
	}
}



$safe = 0;
foreach ( $diag as $row ) {
	foreach ( $row as $cell ) {
		if( $cell > 1 ) $safe++;
	}
}


 echo 'Part 2: ' . $safe . PHP_EOL;