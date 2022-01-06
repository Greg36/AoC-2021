<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input3.txt', FILE_IGNORE_NEW_LINES );

$scans = [];
$scan = [];

// Prepare scans arrays
foreach ( $input as $line ) {
	if( strpos( $line, '---') === 0 ) continue;
	if( $line === '' ) {
		$scans[] = $scan;
		$scan = [];
	} else {
		$cords = explode( ',', $line );
		$scan[] = [
			'x' => $cords[0],
			'y' => $cords[1],
			'z' => $cords[2]
		];
	}
}
$scans[] = $scan;



function rotate( $points, $pitch, $roll, $yaw ) {
	$cosa = (int) cos( deg2rad( $yaw ) );
	$sina = (int) sin( deg2rad( $yaw ) );

	$cosb = (int) cos( deg2rad( $pitch ) );
	$sinb = (int) sin( deg2rad( $pitch ) );

	$cosc = (int) cos( deg2rad( $roll ) );
	$sinc = (int) sin( deg2rad( $roll ) );

	$Axx = $cosa * $cosb;
	$Axy = $cosa * $sinb * $sinc - $sina * $cosc;
	$Axz = $cosa * $sinb * $cosc + $sina * $sinc;

	$Ayx = $sina * $cosb;
	$Ayy = $sina * $sinb * $sinc + $cosa * $cosc;
	$Ayz = $sina * $sinb * $cosc - $cosa * $sinc;

	$Azx = $sinb * -1;
	$Azy = $cosb * $sinc;
	$Azz = $cosb * $cosc;

	foreach ( $points as &$point ) {
		$px = $point[0];
		$py = $point[1];
		$pz = $point[2];

		$point[0] = $Axx * $px + $Axy * $py + $Axz * $pz;
		$point[1] = $Ayx * $px + $Ayy * $py + $Ayz * $pz;
		$point[2] = $Azx * $px + $Azy * $py + $Azz * $pz;
	}

	return $points;
}

function get_deg( $deg ) {
	if( $deg == 90 ) {
		return [
			's' => 1,
			'c' => 0
		];
	} else if( $deg == 180 ) {
		return [
			's' => 0,
			'c' => -1
		];
	} else if( $deg == 270 ) {
		return [
			's' => -1,
			'c' => 0
		];
	}
	return false;
}

function rotateZ3D( $deg, $points ) {
	$deg = get_deg( $deg );
	foreach ( $points as &$point ) {
		$point[0] = $point[0] * $deg['c'] - $point[1] * $deg['s'];
		$point[1] = $point[1] * $deg['c'] + $point[0] * $deg['s'];
	}
	return $points;
}

function rotateX3D( $deg, $points ) {
	$deg = get_deg( $deg );
	foreach ( $points as &$point ) {
		$point[1] = $point[1] * $deg['c'] - $point[2] * $deg['s'];
		$point[2] = $point[2] * $deg['c'] + $point[1] * $deg['s'];
	}
	return $points;
}

function rotateY3D( $deg, $points ) {
	$deg = get_deg( $deg );
	foreach ( $points as &$point ) {
		$point[0] = $point[0] * $deg['c'] + $point[2] * $deg['s'];
		$point[2] = $point[2] * $deg['c'] - $point[0] * $deg['s'];
	}
	return $points;
}

function flip_col( $a, $col ) {
	foreach ( $col as $flip ) {
		foreach ( $a as &$point ) {
			$point[$flip] = $point[$flip] * -1;
		}
	}
	return $a;
}

function swap_col( $a, $swap ) {
	foreach ( $a as &$point ) {
		$temp = $point;
		$point[$swap[0]] = $temp[$swap[1]];
		$point[$swap[1]] = $temp[$swap[0]];
	}
	return $a;
}

function label( $a, $b ) {
	return "{$a['x']},{$a['y']},{$a['z']}|{$b['x']},{$b['y']},{$b['z']}";
}

function cord_distance( $a, $b ) {
	return sqrt( pow( $b['x'] - $a['x'], 2 ) + pow( $b['y'] - $a['y'], 2 ) + pow( $b['z'] - $a['z'], 2 ) );
}

function calc_distance( $scan ) {
	$d = [];
	foreach ( $scan as $from ) {

		foreach ( $scan as $to ) {
			$dist = cord_distance( $from, $to );
			if( $dist > 0 && ! in_array( $dist, $d ) ) {
				$d[ label( $from, $to ) ] = $dist;
			}
		}
	}
	return $d;
}




function find_offset( $relations ) {
	$a = [];
	$b = [];

	foreach ( $relations as $key => $relation ) {
		$a[] = explode( ',', $key );
		$b[] = explode( ',', $relation );
	}

	$tmp = is_offset_correct( $a, $b );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 90,  0   ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 180, 0   ) );   if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 270, 0   ) );   if( $tmp ) return $tmp;

	$tmp = is_offset_correct( $a, rotate( $b, 0, 0,   90  ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 90,  90  ) );  	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 180, 90  ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 270, 90  ) );	if( $tmp ) return $tmp;

	$tmp = is_offset_correct( $a, rotate( $b, 0, 0,   180 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 90,  180 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 180, 180 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 270, 180 ) );	if( $tmp ) return $tmp;

	$tmp = is_offset_correct( $a, rotate( $b, 0, 0,   270 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 90,  270 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 180, 270 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 0, 270, 270 ) );	if( $tmp ) return $tmp;

	$tmp = is_offset_correct( $a, rotate( $b, 90, 0,   0  ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 90, 90,  0  ) );   if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 90, 180, 0  ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 90, 270, 0  ) );	if( $tmp ) return $tmp;

	$tmp = is_offset_correct( $a, rotate( $b, 270, 0,   0 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 270, 90,  0 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 270, 180, 0 ) );	if( $tmp ) return $tmp;
	$tmp = is_offset_correct( $a, rotate( $b, 270, 270, 0 ) );	if( $tmp ) return $tmp;

//	$origb = $b;
//	for ( $i = 0; $i < 6; $i++ ) {
//		if( $i == 1 ) $b = swap_col( $b, [0,1] );
//		if( $i == 2 ) $b = swap_col( swap_col( $b, [ 0, 2 ] ), [1,2]);
//		if( $i == 3 ) $b = swap_col( $b, [1,2] );
//		if( $i == 4 ) $b = swap_col( swap_col( $b, [ 1, 2 ] ), [0,2]);
//		if( $i == 5 ) $b = swap_col( $b, [0,2] );
//
//		$tmp = is_offset_correct( $a, $b );	if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [0] ) ); if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [1] ) ); if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [2] ) ); if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [0,1] ) ); if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [0,2] ) ); if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [1,2] ) ); if( $tmp ) return $tmp;
//		$tmp = is_offset_correct( $a, flip_col( $b, [0,1,2] ) ); if( $tmp ) return $tmp;
//		$b = $origb;
//	}


//	$tmp = is_offset_correct( $a, rotateX3D( 90, $b ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateX3D( 180, $b ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateX3D( 270, $b ) );	if( $tmp ) return $tmp;

//	$tmp = is_offset_correct( $a, rotateY3D( 90, $b) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 90, rotateX3D( 90, $b  ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 90, rotateX3D( 180, $b ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 90, rotateX3D( 270, $b ) ) );	if( $tmp ) return $tmp;
//
//	$tmp = is_offset_correct( $a, rotateY3D( 180, $b) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 180, rotateX3D( 90, $b  ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 180, rotateX3D( 180, $b ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 180, rotateX3D( 270, $b ) ) );	if( $tmp ) return $tmp;
//
//	$tmp = is_offset_correct( $a, rotateY3D( 270, $b) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 270, rotateX3D( 90, $b  ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 270, rotateX3D( 180, $b ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateY3D( 270, rotateX3D( 270, $b ) ) );	if( $tmp ) return $tmp;
//
//	$tmp = is_offset_correct( $a, rotateZ3D( 90, $b) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateZ3D( 90, rotateX3D( 90, $b  ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateZ3D( 90, rotateX3D( 180, $b ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateZ3D( 90, rotateX3D( 270, $b ) ) );	if( $tmp ) return $tmp;
//
//	$tmp = is_offset_correct( $a, rotateZ3D( 270, $b) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateZ3D( 270, rotateX3D( 90, $b  ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateZ3D( 270, rotateX3D( 180, $b ) ) );	if( $tmp ) return $tmp;
//	$tmp = is_offset_correct( $a, rotateZ3D( 270, rotateX3D( 270, $b ) ) );	if( $tmp ) return $tmp;



	return false;
}

function is_offset_correct( $a, $b ) {
	$x = $a[0][0] - $b[0][0];
	$y = $a[0][1] - $b[0][1];
	$z = $a[0][2] - $b[0][2];

	for ( $i = 1; $i < count( $a ); $i++ ) {
		if( $x - ( $a[$i][0] - $b[$i][0] ) ) return false;
		if( $y - ( $a[$i][1] - $b[$i][1] ) ) return false;
		if( $z - ( $a[$i][2] - $b[$i][2] ) ) return false;
	}
	return [
		'x' => $x,
		'y' => $y,
		'z' => $z
	];
}


function translate_scan_by_offset( $scan, $offset ) {
	$translated = [];
	foreach ( $scan as $item ) {
		$translated[] = [
			'x' => $item['x'] + $offset[0],
			'y' => $item['y'] + $offset[1],
			'z' => $item['z'] + $offset[2]
		];
	}
	return $translated;
}

// Get all distances between points
$distances = [];
foreach( $scans as $a => $scan ) {
	$distances[$a] = calc_distance( $scan );
}


// While we still have untranslated arrays
//while( count( $distances ) > 1 ) {
$translations = [];

for ( $u = 0; $u < ( count( $distances ) ); $u++ ) {

	// Check each scan for overlapping scans
	for ( $s = 0; $s < ( count( $distances ) ); $s++ ) {

		if( $u == $s ) continue;

		// Find all lines with same length
		$same = [];
		foreach ( $distances[$u] as $i => $first ) {
			foreach ( $distances[$s] as $k => $second ) {
				if( $first == $second ) {
					$same[] = [ $i, $k,	$first ];
				}
			}
		}

		// Find matching points for each length
		$offsets = [];
		foreach ( $same as $line ) {
			$from = explode( '|', $line[0] );
			$to = explode( '|', $line[1] );

			$offsets[$from[0]][$to[0]] = ( ( $offsets[$from[0]][$to[0]] ?? 1 ) + 1 ) ?? 1;
			$offsets[$from[0]][$to[1]] = ( ( $offsets[$from[0]][$to[1]] ?? 1 ) + 1 ) ?? 1;
			$offsets[$from[1]][$to[0]] = ( ( $offsets[$from[1]][$to[0]] ?? 1 ) + 1 ) ?? 1;
			$offsets[$from[1]][$to[1]] = ( ( $offsets[$from[1]][$to[1]] ?? 1 ) + 1 ) ?? 1;
		}

		// Get matching points between scan 0 and given scan
		$relation = [];
		foreach ( $offsets as $key => $offset ) {
			$relation[ $key ] =  array_search( max( $offset ) , $offset );
		}

		// Scanners overlap when there are 12 or more overlapping points
		if( count( $relation ) >= 12 ) {
			$translations[$u][$s] = find_offset( $relation );;
		}
	}

}

$a = 'xd';

// -20,-1133,1061

var_dump( $translations[1][4]);
die();

var_dump( ( $translations[0][1]['x'] ) . ',' . ( $translations[0][1]['y'] ) . ',' . ( $translations[0][1]['z'] ) );
var_dump( ( $translations[0][1]['x'] - $translations[1][4]['x'] ) . ',' . ( $translations[0][1]['y'] - $translations[1][4]['y'] ) . ',' . ( $translations[0][1]['z'] - $translations[1][4]['z'] ) );


// echo 'Part 1: ' . $correct . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;