<?php

require 'api.php';
// update rates function
function GetRates(){
	
	$currency_array = array();
	//check whether a rates file already exists & whether the rates file is over 2 hours old
	if (file_exists(LASTRATES)){		
		$lastTimeUpdated = filemtime(LASTRATES);
		//check file age
		if ((time() - 3600) > $lastTimeUpdated){
			$ratefile=simplexml_load_file('rates/lastrates.xml');	
			$date = date("d-b-Y-I-M", $lastTimeUpdated);
			rename('rates/lastrates.xml','rates/'.$date.'.xml');
				
			//if currency file already exist, populate array with live currencies
			foreach($ratefile->rate as $r) {
				if ((string) $r['live'] == '1') {
					$currency_array[] = (string) $r['code'];
				}
			}
			$update = 1;
		} else {
			$update = 0;
		}
			
		
	} else {
		
	//if currency file does not exist, populate live array with default currencies
	$update = 1;	
	$currency_array = array(   'CAD','CHF','CNY','DKK',
							   'EUR','GBP','HKD','HUF',
							   'INR','JPY','MXN','MYR',
							   'NOK','NZD','PHP','RUB',
							   'SEK','SGD','THB','TRY',
							   'USD','ZAR');
	}

	if ($update == 1){
		
		$json_rates = file_get_contents(API_KEY)
		or die("Error: Cannot load JSON file from fixer");
		
		//convert json response to php array
		$rates = json_decode($json_rates, true);
		
		//create new XML object
		$rateXML = new SimpleXMLElement("<rates></rates>");
		$rateXML->addAttribute('timestamp', time());
			foreach ($rates["rates"] as $key => $value){
				$rateEle = $rateXML->addChild('rate');
				$rateEle->addAttribute('code', $key);
				$rateEle->addAttribute('rate', $value/$rates['rates']['GBP']);
				//assign the live currencies a value of 1
				$live = (in_array($key, $currency_array)) ? '1':'0';
				$rateEle->addAttribute('live', $live);			
		file_put_contents(LASTRATES, $rateXML->asXML());
		}
	}	
}	

	function LogMessage($msg){
		file_put_contents(LOGFILE, 'Time: '.DATETIME.' Message: '.$msg." \r\n", FILE_APPEND);
	}
	
	function GetFormattedTime($time){
		return date("d M Y H:i", $time);
	}
	
	function ThrowError($errorNum, $format='xml'){
	
		if ($format == 'json'){
			$json = array('conv' => array("code" => $errorNum, "msg" => ERRORARRAY[$errorNum]));
			$out = header('Content-Type: application/json');
			$out .= json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			return $out;
		} else {
			header('Content-type: text/xml');
			$errorXML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><conv></conv>');
			$errorXML->addChild('error');
			$errorXML->error->addChild('code', $errorNum);
			$errorXML->error->addChild('msg', ERRORARRAY[$errorNum]);
			return $errorXML->asXML();
		}
	}

?>
