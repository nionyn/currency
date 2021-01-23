<?php

require 'create_currencies_xml.php';

function DoConversion(){
	
	# get variables to use
	$to = $_GET['to'];
	$from = $_GET['from'];
	$amnt = $_GET['amnt'];
	$format = $_GET['format'];
	
	
	# check if currencies files exists, if not create it
	if (!file_exists('currencies.xml')){
		//create_currency_file();
	}
	
	# load currency file containing ccodes, country names and currency locations
	$xml=simplexml_load_file('currencies.xml');
	
	# get "to" currency information
	$to_array = $xml->xpath("//ccode[.='$to']/parent::currency");
	#get "from" currency information
	$from_array = $xml->xpath("//ccode[.='$from']/parent::currency");
	
	#load last rates file and pull rates
	$xml=simplexml_load_file('rates/lastrates.xml');
	#find to rate in rates.xml	
	
	
	$line = $xml->xpath("//rate[@code='$to']");
	(float)$to_rate = $line[0]['rate'];
	
	
	
	if ($line[0]['live'] == 0){
		echo ThrowError(1200, $format);
		exit();
	}
	
	#find from rate in rates.xml
	$line = $xml->xpath("//rate[@code='$from']");	
	(float)$from_rate = $line[0]['rate'];		
	
	if ($line[0]['live'] == 0){
		echo ThrowError(1200, $format);
		exit();
	}	
	
	#do conversion
	
	
	(float)$rate = (float)$to_rate/(float)$from_rate;
	


	#create output
	
	# get variables necessary for the output
	$dateandtime = GetFormattedTime(time());
	$from_currency = (string) $from_array[0]->cname;
	$from_location = (string) $from_array[0]->cntry;
	$to_currency = (string) $to_array[0]->cname;
	$to_location = (string) $to_array[0]->cntry;
	
	# output either json or xml
	if ($format == 'json'){
		$json = array('conv' => array("at" => $dateandtime, "rate" => $rate, 
						"from" => array("code" => $from, "curr" => $from_currency, "loc" => $from_location, "amnt" => $amnt), 
						"to" =>	array("code" => $to, "curr" => $to_currency, "loc" => $to_location, "amnt" => $amnt * $rate)
						));
							
		$out = header('Content-Type: application/json');
		$out .= json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	} else {
		header('Content-type: text/xml');
		$xmlOutput = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><conv></conv>');
		$xmlOutput->addChild('at', $dateandtime );
		$xmlOutput->addChild('rate', $rate);
		$xmlOutput->addChild('from');
		$xmlOutput->from->addChild('code', $from);
		$xmlOutput->from->addChild('curr', $from_currency);
		$xmlOutput->from->addChild('location', $from_location );
		$xmlOutput->from->addChild('amount', $amnt);
		
		$xmlOutput->addChild('to');
		$xmlOutput->to->addChild('code', $to);
		$xmlOutput->to->addChild('curr', $to_currency);
		$xmlOutput->to->addChild('location', $to_location);
		$xmlOutput->to->addChild('amount', $amnt * $rate);
		$out = $xmlOutput->asXML();
	}
	
	return $out;
	
}
?>