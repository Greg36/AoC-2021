<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
//$input = file( 'input2.txt', FILE_IGNORE_NEW_LINES );
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


//function test_rotate( $a, $b, $c, $a1, $b1, $c1 ) {
//	$man = [0, 90, 180, 270];
//
//	foreach ( $man as $first ) {
//		foreach ( $man as $second ) {
//			foreach ( $man as $third ) {
//				$xd = rotate( [ [$a, $b, $c] ], $first, $second, $third );
//				if( $xd[0][0] . $xd[0][1] . $xd[0][2] === $a1 . $b1 . $c1 ) {
//					echo $first . ' ' . $second . ' ' . $third . PHP_EOL;
//				}
//			}
//		}
//	}
//}
//test_rotate( 686, 422, 578, -686, 422, -578);
//test_rotate( 88, 113, -1104, -88, 113, 1104 );

//die();

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
					'x' => $translated[$target]['x'] + $rotated[0],
					'y' => $translated[$target]['y'] + $rotated[1],
					'z' => $translated[$target]['z'] + $rotated[2],
					'rotation' => (array($key => $translation['rotate']) + $rotation)
				];
			}
			get_translations( $translations, $key, $rotation, $translation, $key );

		}
	}
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
	return [
		'x' => $x,
		'y' => $y,
		'z' => $z
	];
}



function parse_vector( $translations, $vector ) {

	$stack = [];
	// See if we have the last to zero vector
	if( isset( $translations[0][$vector] ) ) {
		$offset = $translations[0][$vector]['offset'];
	} else {
		$a ='xd';
	}


	return $offset;
}

function translate_scan_by_offset( $scan, $vector, $i, $translations ) {
	$translated = [];

	if( !isset( $vector['rotation'] ) ) {
		$vector['rotation'][] = $translations[0][$i]['rotate'];
	}

	$temp = [];
	foreach ( $scan as $item ) {
		$temp[] = [
			$item['x'], $item['y'], $item['z']
		];

	}
	$rotated = $temp;
	foreach ( $vector['rotation'] as $rot ) {
		$rotated = rotate( $rotated, $rot[0], $rot[1], $rot[2] );
	}
	$scan = [];
	foreach ( $rotated as $item ) {
		$scan[] = [
			'x' => $item[0],
			'y' => $item[1],
			'z' => $item[2]
		];
	}


	foreach ( $scan as $item ) {

		$translated[] = [
			'x' => $item['x'] + $vector['x'],
			'y' => $item['y'] + $vector['y'],
			'z' => $item['z'] + $vector['z']
		];
		$a = 'xd';
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


global $translated;
$translated = [];
get_translations( $translations, 0 );


// Translate scans
for ( $i = 1; $i < ( count( $scans ) ); $i++ ) {
	$scans[$i] = translate_scan_by_offset( $scans[$i], $translated[$i], $i, $translations );
}

$probes = [];
foreach ( $scans as $scan ) {
	foreach ( $scan as $item ) {
		$probes[] =  join( ',', $item ) . PHP_EOL;
	}
}

echo 'Part 1: ' . count( array_count_values( $probes)) . PHP_EOL;


//die();
//
//
//
//
//$temp = [];
//foreach ( $test1 as $item ) {
//	$temp[] = [
//		$item['x'] + 68, $item['y'] + -1246, $item['z'] +  -43
//	];
//
//}
//$rotated = rotate( $temp, 0, 180, 180 );
////$sc = [];
////foreach ( $rotated as $item ) {
////	$sc[] = [
////		'x' => $item[0],
////		'y' => $item[1],
////		'z' => $item[2]
////	];
////}
//
////foreach ( $sc as $item ) {
////
////	$loloza[] = [
////		'x' => $item['x'] + 68,
////		'y' => $item['y'] + -1246,
////		'z' => $item['z'] + -43
////	];
////}
//
//
//$fin  = [];
//
//foreach ( $test1 as $item ) {
//	$fin[] = join( ',', $item );
//}
//
//foreach ( $rotated as $item ) {
//	$fin[] = join( ',', $item );
//}
//
//function get_duplicates ($array) {
//	return array_unique( array_diff_assoc( $array, array_unique( $array ) ) );
//}
//
//$a = 'xd';

// -20,-1133,1061

//var_dump( $translations[1][4]);
//die();

//var_dump( ( $translations[0][1]['offset']['x'] ) . ',' . ( $translations[0][1]['offset']['y'] ) . ',' . ( $translations[0][1]['offset']['z'] ) );
//var_dump( ( $translations[1][4]['x'] - $translations[0][1]['x']  ) . ',' . ( $translations[1][4]['y'] - $translations[0][1]['y'] ) . ',' . ( $translations[1][4]['z'] - $translations[0][1]['z'] ) );


// echo 'Part 1: ' . $correct . PHP_EOL;

// echo 'Part 2: ' . $correct . PHP_EOL;