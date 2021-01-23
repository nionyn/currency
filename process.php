<?php
	require 'config.php';
	require 'functions.php';
	require 'create_currencies.php';
	require 'convert.php';
	require 'api.php';
	
	define('PARAMS', array('to', 'from', 'amnt', 'format'));

	if (!isset($_GET['format']) || empty($_GET['format'])) {
		$_GET['format'] = 'xml';
	}
	# ensure PARAM values match the keys in $GET
	if (count(array_intersect(PARAMS, array_keys($_GET))) < 4) {
		echo ThrowError(1000, $_GET['format']);
		exit();
	}
	# ensure no extra params
	if (count($_GET) > 4) {
		echo ThrowError(1100, $_GET['format']);
		exit();
	}

// MAIN PROGRAM

GetRates();

echo DoConversion();


?>