<?php

$start = microtime(true);

function run_day( $day ) {
	$day = str_pad( $day, 2, '0', 0 );
	if( file_exists( "./day{$day}/day.php" ) ) {
		$day_time = microtime(true);
		echo "\033[32mDay: " . (int) $day . "\033[0m\n";
		require_once( dirname(__FILE__) . "./day{$day}/day.php" );

		// Some days have separate file for part 2
		if( file_exists( "./day{$day}/day2.php" ) ) {
			(function( $day ) { require_once( dirname(__FILE__) . "./day{$day}/day2.php" ); })( $day );
		}

		echo "\033[34mDay: " . round( microtime(true ) - $day_time, 3 )  . "s\033[0m\n";
		echo '--------' . PHP_EOL;
	}
}

for ( $i = 1; $i <= 25; $i++ ) {
	run_day( $i );
}

echo PHP_EOL . "\033[30;48;5;82m Total time: " . round( microtime(true ) - $start, 3 ) . "s \033[0m\n" . PHP_EOL;