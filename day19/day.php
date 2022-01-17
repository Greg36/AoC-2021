<?php

$input = file( dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES );

// Prepare scans arrays
foreach ( $input as $line ) {
	if( strpos( $line, '---') === 0 ) continue;
	if( $line === '' ) {
		$scans[] = $scan;
		$scan = [];
	} else {
		$scan[] = explode( ',', $line );
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

function get_translations( $translations, $target, $rotation = [], $parent = [], $parent_key = 0 ) {
	global $translated;

	foreach ( $translations[ $target ] as $key => $translation ) {
		if( $key == 0 ) continue;
		if( ! isset( $translated[ $key ] ) ) {

			if( $target == 0 ) {
				$translated[ $key ] = $translation['offset'];
			} else {

				$rotated = array_values( $translation['offset'] );
				if( !isset( $rotation[$parent_key] ) ) {
					$rotation  = array($parent_key => $parent['rotate']) + $rotation;
				}

				foreach ( $rotation as $rot ) {
					$rotated = rotate( [$rotated], $rot[0], $rot[1], $rot[2] )[0];
				}

				$translated[ $key ] = [
					$translated[$target][0] + $rotated[0],
					$translated[$target][1] + $rotated[1],
					$translated[$target][2] + $rotated[2],
					'rotation' => (array($key => $translation['rotate']) + $rotation)
				];
			}
			get_translations( $translations, $key, $rotation, $translation, $key );

		}
	}
}

function label( $a, $b ) {
	return join( ',', $a ) . '|' . join( ',', $b );
}

function cord_distance( $a, $b ) {
	return sqrt( pow( $b[0] - $a[0], 2 ) + pow( $b[1] - $a[1], 2 ) + pow( $b[2] - $a[2], 2 ) );
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

	$directions = [
		[0,   0,   0   ],
		[0,   90,  0   ],
		[0,   180, 0   ],
		[0,   270, 0   ],
		[0,   0,   90  ],
        [0,   90,  90  ],
        [0,   180, 90  ],
        [0,   270, 90  ],
		[0,   0,   180 ],
		[0,   90,  180 ],
		[0,   180, 180 ],
		[0,   270, 180 ],
		[0,   0,   270 ],
		[0,   90,  270 ],
		[0,   180, 270 ],
		[0,   270, 270 ],
		[90,  0,   0   ],
		[90,  90,  0   ],
		[90,  180, 0   ],
		[90,  270, 0   ],
		[270, 0,   0   ],
		[270, 90,  0   ],
		[270, 180, 0   ],
		[270, 270, 0   ]
	];

	foreach ( $directions as $direction ) {
		$offseet = is_offset_correct( $a, rotate( $b, $direction[0], $direction[1], $direction[2] ) );
		if( $offseet ) {
			return [
				'offset' => $offseet,
				'rotate' => $direction
			];
		};
	}

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
	return [ $x, $y, $z ];
}

function translate_scan_by_offset( $scan, $vector, $i, $translations ) {
	$translated = [];

	if( !isset( $vector['rotation'] ) ) {
		$vector['rotation'][] = $translations[0][$i]['rotate'];
	}

	foreach ( $vector['rotation'] as $rot ) {
		$scan = rotate( $scan, $rot[0], $rot[1], $rot[2] );
	}

	foreach ( $scan as $item ) {
		$translated[] = [
			$item[0] + $vector[0],
			$item[1] + $vector[1],
			$item[2] + $vector[2]
		];
	}
	return $translated;
}


// PART 1

// Get all distances between points
foreach( $scans as $a => $scan ) {
	$distances[$a] = calc_distance( $scan );
}

// Find all matching points between the scans
for ( $u = 0; $u < ( count( $distances ) ); $u++ ) {

	for ( $s = 0; $s < ( count( $distances ) ); $s++ ) {

		if( $u == $s ) continue;

		// We have overlap
		$overlap = array_intersect( $distances[$u], $distances[$s] );
		if( count( $overlap ) < 12 ) continue;

		// Find all lines with same length
		$same = [];
		foreach ( $overlap as $key => $item ) {
			$same[] = [
				$key,
				array_search( $distances[$u][ $key ], $distances[$s] ),
			];
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
			$translations[$u][$s] = find_offset( $relation );
		}
	}
}

// Translate all vectors
global $translated;
$translated = [];
get_translations( $translations, 0 );

// Translate scans based on vectors
for ( $i = 1; $i < ( count( $scans ) ); $i++ ) {
	$scans[$i] = translate_scan_by_offset( $scans[$i], $translated[$i], $i, $translations );
}

// Get all probes
$probes = [];
foreach ( $scans as $scan ) {
	foreach ( $scan as $item ) {
		$probes[] =  join( ',', $item ) . PHP_EOL;
	}
}

echo 'Part 1: ' . count( array_count_values( $probes) ) . PHP_EOL;


// PART 2

function distance($vector1, $vector2) {
	$n = count($vector1);
	$sum = 0;
	for ($i = 0; $i < $n; $i++) {
		$sum += abs($vector1[$i] - $vector2[$i]);
	}
	return $sum;
}

$translated[] = [ 0, 0,0 ];
foreach ( $translated as $first ) {
	foreach ( $translated  as $second ) {
		unset( $first['rotation'] );
		unset( $second['rotation'] );
		$manhattan[] = [
			array_values( $first ),
			array_values( $second )
		];
	}
}

$max = 0;
foreach ( $manhattan as $key => $route ) {
	$dist = distance( $route[0], $route[1] );
	if( $dist > $max ) $max = $dist;
}

echo 'Part 2: ' . $max . PHP_EOL;