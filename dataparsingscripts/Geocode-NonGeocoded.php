<?php

	$file2 = fopen("NotGeocoded.csv","r");	
	$obj2 = array();

	$fileout = fopen("NewGeocoded.csv","w");

	if ($file2) {
	    while (($buffer = fgets($file2, 4096)) !== false) {
       		$d = explode(",",$buffer);
       		$f = $d[count($d)-2] . "," . $d[count($d)-1];
       		//echo trim($f) . "\n";
       		$geo = geocode(trim($f));
       		if (strlen($geo[1]) != 0)  {
       			$content = trim($f) . "|" . trim($geo[1]) . "|" . trim($geo[2]) . "|" . trim($geo[3]) . "|" . trim($geo[4]) . "|" . trim($geo[5]) . "\n"; 
       		} else {
       			$content = trim($f). "\n";
       		}
       		echo $content;
       		fwrite($fileout, $content);
	    }
	    if (!feof($file2)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file2);
	}

	fclose($fileout);
	/*
	echo count($obj2) . "\n";
	$fileout = fopen("NotGeocoded.csv","w");
	foreach($obj2 as $o) {
		fwrite($fileout, $o."\n");
	}
	fclose($fileout);

	echo count($obj) . "\n";
	$fileout = fopen("Geocoded.csv","w");
	foreach($obj as $o) {
		fwrite($fileout, trim($o)."\n");
	}
	fclose($fileout);
	*/


function geocode($address) {
    //global $googlekey;
    $googlekeys = array('AIzaSyA5bjWHx4EfLyO00LMgRmoVlM5QJTHzvuo');
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&key=".$googlekeys[0];

    //echo $url . "\n";
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
  	CURLOPT_RETURNTRANSFER => 1,
  	CURLOPT_URL => $url));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	//$contents = file_get_contents($url);
	$venue = json_decode($resp);
	// var_dump($venue);
	if (isset($venue->results[0]->types)) {
	//var_dump($venue->results[0]);
	$types = array();
	$types[] = $venue->results[0]->types[0];
	$types[] = $venue->results[0]->geometry->location->lat;
	$types[] = $venue->results[0]->geometry->location->lng;

	foreach($venue->results[0]->address_components as $k=>$v) {
	  if (isset($v->types[0]) && $v->types[0] == "country")
	    $types[] = $v->long_name;
	}
	$types[] = $venue->results[0]->formatted_address;
	$types[] = isset($venue->results[0]->location_type) ? $venue->results[0]->location_type : "";
	return $types;
	} else {
	// var_dump($venue);
	return false;
	}
 }

?>
