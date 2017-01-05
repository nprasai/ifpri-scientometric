<?php

	$file = fopen("../data/webofscience_cgiar_journalarticles_12131_checked.txt","r");
	$cnt = 0;
	$obj = array();

	$countries = getCountries();
	$okeys = getKeywords();
	//var_dump($okeys);
	//var_dump($countries);

	$keywords = array();
	$subjects = array();
	$years = array();
	$yearkeys = array();

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
	       			if(isset($data[$i])) {
		       			if ($header[$i] == "\"AB\"") {
		       				$f[trim(str_replace("\"","",$header[$i]))] = trim(str_replace("\"","",$data[$i]));
		       			} else if ($header[$i] == "\"AU\"") {
		       				$f[trim(str_replace("\"","",$header[$i]))] = array_map('trim', explode(";",ucwords(strtolower(trim(str_replace("\"","",$data[$i]))))));
		       			} else {
		       				$f[trim(str_replace("\"","",$header[$i]))] = ucwords(strtolower(trim(str_replace("\"","",$data[$i]))));
		       			}
		       		}
	       		}
	   
	       		if (isset($f['TI']) && isset($f['PY']) && isset($f['UT']) && (strlen($f['UT']) > 3 || isset($f['DI']))) {

	       			if (strlen($f['PY']) != 4)
	       				$f['PY'] = $previousyear;

	       			$f['SC'] = str_replace("cell biology","biochemistry & molecular/cell biology",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("biochemistry & molecular biology","biochemistry & molecular/cell biology",strtolower(trim($f['SC'])));
	       			
	       			$f['SC'] = str_replace("marine & freshwater biology","fisheries",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("oceanography","fisheries",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("fisheries","fisheries",strtolower(trim($f['SC'])));

	       			$f['SC'] = str_replace("physical geography","asdf",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("geography","asdf",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("remote sensing","asdf",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("asdf","geography & remote sensing",strtolower(trim($f['SC'])));

	       			$f['SC'] = str_replace("sociology","fdsa",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("government & law","fdsa",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("social sciences - other topics","fdsa",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("mathematical methods in social sciences","fdsa",strtolower(trim($f['SC'])));
	       			$f['SC'] = str_replace("fdsa","social sciences & law",strtolower(trim($f['SC'])));
	       			
	       			$keys = isset($f['SC']) ? array_map('trim',explode(";",$f['SC'])) : "";
	       			//$subjects = isset($f['SC']) ? array_map('trim',explode(";",$f['SC'])) : "";
	       			$authors = implode("., ",$f['AU']);
	       			$year = $f['PY'];
	       			$abstract = $f['AB'];
	       			$title = $f['TI'];
	       			$journal = $f['SO'];
	       			//$keys = array_map('trim',explode(";",$f['ID']));
	       			$corresponding = $f['RP'];
	       			$id = isset($f['UT']) ? $f['UT'] : "";
	       			$doi = isset($f['DI']) ? $f['DI'] : "";
	       			$ff = preg_replace('/\[.*?\]/', '', $f['C1']);
					$affils = array_map('trim',explode(".", trim($ff)));
					foreach($keys as $ky) {
						if (strlen($ky) > 0 && in_array(strtolower(trim($ky)),$okeys)) {
							// echo $ky . "\n";
							if(isset($obj[strtolower(trim($ky))]))
								$obj[strtolower(trim($ky))][] = array("journal"=>$journal,"doi"=>$doi,"title"=>$title, "authors"=>$authors,"year"=>$year,"corresponding"=>$corresponding,"affiliations"=>$affils);
							else
								$obj[strtolower(trim($ky))] = array(array("journal"=>$journal,"doi"=>$doi,"title"=>$title, "authors"=>$authors,"year"=>$year,"corresponding"=>$corresponding,"affiliations"=>$affils));
						}
					}


	       			//$obj[] = array("title"=>$title, "authors"=>$authors,"keywords"=>$keys,"year"=>$year,"id"=>strtolower($id),"doi"=>$doi,"corresponding"=>$corresponding,"affiliations"=>$affils);
	       			//,"abstract"=>$abstract

	       			$previousyear = $f['PY'];
	     			
	       		}	
	       		
	        }

	    }
	    if (!feof($file)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file);
	}

	// var_dump($obj);

	$fileout = fopen("../data/stream/articles.json","w");
	fwrite($fileout, json_encode($obj, true));
	fclose($fileout);
	



	//var_dump($obj);
	echo count($obj) . "\n";


	function getCountries() {
		$file = fopen("countries.csv", "r");
		$countries = array();
		if ($file) {
		    while (($buffer = fgets($file, 4096)) !== false) {
		    	$countries[] = trim($buffer);
		    }
		    if (!feof($file)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($file);
		}
		return $countries;
	}

	function getKeywords() {
		$file = fopen("../data/streamgraph.csv", "r");
		$countries = array();
		if ($file) {
		    while (($buffer = fgets($file, 4096)) !== false) {
		    	$a = explode(",",$buffer,2);
		    	$countries[] = $a[0];
		    }
		    if (!feof($file)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($file);
		}
		$a = array_unique($countries);
		asort($a);
		return $a;	
	}
?>