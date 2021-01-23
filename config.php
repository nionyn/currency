<?php
	# set timezone
	@date_default_timezone_set("GMT"); 
	
	define('LOGFILE', 'log.txt');
	
	//set location of rates file
	define('LASTRATES', 'rates/lastrates.xml');
	
	define('DATETIME',  time());
	
	# define hash values for raising errors
	define('ERRORARRAY', array(
	1000 => 'Required parameter is missing',
	1100 => 'Parameter not recognized',
	1200 => 'Currency type not recognized',
	1300 => 'Currency amount must be a decimal number',
	1400 => 'Format must be xml or json',
	1500 => 'Error in Service',
	2000 => 'Action not recognized or is missing',
	2100 => 'Currency code in wrong format or is missing',
	2200 => 'Currency code not found for update',
	2300 => 'No rate listed for currency',
	2400 => 'Cannot update base currency',
	2500 => 'Error in service'));
?>