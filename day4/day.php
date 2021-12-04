<?php

require_once '../lib/lib.php';

$input = file( 'input.txt', FILE_IGNORE_NEW_LINES );
$input = join( ' ', $input );
$input = explode( ' ', $input);
foreach ( $input as $key => $item ) {
	if( $item === '' ) unset( $input[$key]);
}
$input = array_chunk( $input, 25);

$nums = ['72','86','73','66','37','76','19','40','77','42','48','62','46','3','95','17','97','41','10','14','83','90','12','23','81','98','11','57','13','69','28','63','5','78','79','58','54','67','60','34','39','84','94','29','20','0','24','38','43','51','64','18','27','52','47','74','59','22','85','65','80','2','99','70','33','91','53','93','9','82','8','50','7','56','30','36','89','71','21','49','31','88','26','96','16','1','75','87','6','61','4','68','32','25','55','44','15','45','92','35'];
//$nums = ['7','4','9','5','11','17','23','2','0','14','21','24','10','16','13','6','15','25','12','22','18','20','8','19','3','26','1'];
//$test = ['22','13','17','11','0','8','2','23','4','24','21','9','14','16','7','6','10','3','18','5','1','12','20','15','19'];

function check_board( $board ) {
	$guesses = array_chunk( $board, 5);
	$cols = [];
	foreach ( $guesses as $k => $row ) {
		foreach ( $row as $key => $cell ) {
			$cols[$key][$k] = $cell;
		}
	}

	$guesses = array_merge( $guesses, $cols);

	foreach ( $guesses as $row ) {
		$val = array_count_values( $row );
		if( isset( $val['x'] ) && $val['x'] === 5 ) {
			return true;
		}
	}

	return false;
}

//$bingo = false;
//foreach ( $nums as $keyr => $num ) {
//	if( $bingo ) break;
//
//	// Mark the numbers
//	foreach ( $input as $bnum => $board ) {
//		if (  ( $key = array_search( $num, $board)) !== false ) {
//			$input[$bnum][$key] = 'x';
//		}
//		if( check_board( $board ) ) {
//			$bingo = [ $nums[$keyr - 1], $board ];
//		}
//	}
//}
//
//$sum = 0;
//foreach ( $bingo[1] as $ele ) {
//	if( $ele != 'x' ) $sum += $ele;
//}var_dump( $bingo[0]);
//
//echo 'Part 1: ' . $sum * $bingo[0] . PHP_EOL;


$bingo = false;
foreach ( $nums as $keyr => $num ) {
	if($bingo ) {
		break;
	}

	// Mark the numbers
	foreach ( $input as $bnum => $board ) {
		if (  ( $key = array_search( $num, $board)) !== false ) {
			$input[$bnum][$key] = 'x';
		}
		if( check_board( $board ) ) {
			if( count( $input) === 1 ) {
				$bingo = [ $nums[$keyr - 1 ], array_values( $input)[0], $nums[$keyr] ];
				break;
			}
			unset( $input[$bnum] );
		}
	}
}


$sum = 0;
foreach ( $bingo[1] as $ele ) {
	if( $ele != 'x' ) $sum += $ele;
}

echo 'Part 2: ' . ($sum + $bingo[2]) * $bingo[0] . PHP_EOL;


// 11571 - to high
// 10875 - to high

// echo 'Part 2: ' . $correct . PHP_EOL;