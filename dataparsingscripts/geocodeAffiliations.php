<?php

	$file = fopen("webofscience_cgiar_journalarticles_institutions_12131_checked.txt","r");
	$file2 = fopen("geocoded.csv","r");
	$cnt = 0;
	$obj = array();
	$obj2 = array();

	if ($file2) {
	    while (($buffer = fgets($file2, 4096)) !== false) {
	    	$cnt++;
	   
	       		$data = explode("|", $buffer);
	       		$obj2[] = $data;

	    }
	    if (!feof($file2)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file2);
	}

	$cnt = 0;
	$buffer = null;
	if ($file) {
	    while (($buffer = fgets($file, 4096)) !== false) {
	    	$cnt++;
	    	if ($cnt == 1) {
	    		$header = explode("	", $buffer);
	    		//var_dump($header);
	    	} else {
	       		$data = explode("	", $buffer);
	       		$f = array();
	       		for($i=0;$i<count($header);$i++) {
	       			$f[trim(str_replace("\"","",$header[$i]))] = trim(str_replace("\"","",$data[$i]));
	       		}
	       		$match = false;
	       		foreach($obj2 as $oo) {
	       			if ($oo[0] == $f['UT']) {
	       				$match = true;
	       				break;
	       				
	       			}	
	       		}
	       		if (!$match)
	       			$obj[] = $f;
	       		
	      
	        }

	    }
	    if (!feof($file)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file);
	}
	echo count($obj) . "\n";

	$abc = array();
	foreach($obj as $o) {
		$abc[] = trim($o['C1']);
	}
	echo count(array_unique($abc)) . "\n";





	//var_dump($obj);
	

	$fileout = fopen("geocoded_unique.csv","w");

	foreach($abc as $val) {
	  $geo = geocode($val);
      if (strlen($geo[1]) == 0)  {
          $f = @explode(",", $val, 2);
          if (isset($f[1])) {
              $geo = geocode($f[1]);
              if (strlen($geo[1]) != 0)  {
                  $content = trim($val) . "\n";
              } else {
                  $content = trim($val) . "|" . trim($geo[1]) . "|" . trim($geo[2]) . "|" . trim($geo[3]) . "|" . trim($geo[4]) . "|" . trim($geo[5]) . "\n";     
              }
          } else {
              $content = trim($val) . "\n";
          }
      } else {
          $content = trim($val) . "|" . trim($geo[1]) . "|" . trim($geo[2]) . "|" . trim($geo[3]) . "|" . trim($geo[4]) . "|" . trim($geo[5]) . "\n"; 
      }
      echo $content;
      fwrite($fileout,$content);
	}
	fclose($fileout);



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