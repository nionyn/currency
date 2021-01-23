<?php

function create_currency_file(){
	#fetch a list of iso currency codes and country names
	$xml=simplexml_load_file('http://www.currency-iso.org/dam/downloads/lists/list_one.xml') or die("Error: Cannot create object");



	# select the currency codes from the xml file
	$codes = $xml->xpath("//CcyNtry/Ccy");
	$ccodes = [];
	# create and populate an array for the currency codes
	foreach ($codes as $code) {
		if (!in_array($code, $ccodes)) {
			$ccodes[] = (string) $code;
		}
	}

	# create a new xml object with the root tag
	$CurrencyXML = new SimpleXMLElement("<currencies></currencies>");

	foreach ($ccodes as $ccode) { 
		$nodes = $xml->xpath("//Ccy[.='$ccode']/parent::*");
		
		$cname =  $nodes[0]->CcyNm;
		
		$currency = $CurrencyXML->addChild('currency');
		
		$currency->addChild('ccode', $ccode);
		$currency->addChild('cname', $cname);
		
		$CurrencyXML->xpath("/currencies");
			
				$last_element = count($nodes) - 1;
				
				
				$countriesused = '';
				foreach ($nodes as $index=>$node) {
					
					$countriesused .= ucwords(strtolower($node->CtryNm));
					if ($index!=$last_element) {$countriesused .=', ';}
				}
				
				$currency->addChild('cntry', $countriesused);
				
	}

	$CurrencyXML->asXML('currencies.xml');
}

?>