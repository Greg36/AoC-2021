<?php


function permutate($values, $size, $offset) {
	$count = count($values);
	$array = array();
	for ($i = 0; $i < $size; $i++) {
		$selector = ($offset / pow($count,$i)) % $count;
		$array[$i] = $values[$selector];
	}
	return join( '', $array);
}

function permutations($values, $size) {
	$a = array();
	$c = pow(count($values), $size);
	for ($i = 0; $i<$c; $i++) {
		$a[$i] = permutate($values, $size, $i);
	}
	return $a;
}