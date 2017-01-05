<?php

	$file = fopen("../data/webofscience_cgiar_journalarticles_12131_checked.txt","r");
	$cnt = 0;
	$obj = array();

	$countries = getCountries();
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
	   
	       		if (isset($f['PY']) && isset($f['SC'])) {
	       			if (strlen($f['PY']) != 4)
	       				$f['PY'] = $previousyear;

	       			$obj[] = $f;
	       			if (!isset($yearkeys[$f['PY']]))
	       				$yearkeys[$f['PY']] = "";

	       			/* if (trim($f['SC']) == "biochemistry & molecular biology")
	       				$ad = "biochemistry & molecular/cell biology";
	       			else
	       				$ad = trim($f['SC']); */

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

	       			$yearkeys[$f['PY']] .= $f['SC'] . "; ";
	       		}	
	       		


	       		/*if(isset($f['PY'])) {
	       			$aa = array_map('trim', explode(';', $f['PY']));
	       			$years = array_merge($years, $aa);
	       			$previousyear = $f['PY'];
	       		}

	       		if(isset($f['ID'])) {
	       			$aa = array_map('trim', explode(';', $f['ID']));
	       			$keywords = array_merge($keywords, $aa);
	       		} */

	       		if(isset($f['SC'])) {
	       			$aa = array_map('trim', explode(';', $f['SC']));
	       			foreach($aa as &$ff) {
	       				if ($ff == "biochemistry & molecular biology")
	       					$ff = "biochemistry & molecular/cell biology";
	       				else if ($ff == "cell biology")
	       					$ff = "biochemistry & molecular/cell biology";
	       				else if ($ff == "oceanography")
	       					$ff = "fisheries";
	       				else if ($ff == "marine & freshwater biology")
	       					$ff = "fisheries";
	       				else if ($ff == "physical geography")
	       					$ff = "geography & remote sensing";
	       				else if ($ff == "remote sensing")
	       					$ff = "geography & remote sensing";
	       				else if ($ff == "geography")
	       					$ff = "geography & remote sensing";
	       				else if ($ff == "mathematical methods in social sciences")
	       					$ff = "social sciences & law";
	       				else if ($ff == "social sciences - other topics")
	       					$ff = "social sciences & law";
	       				else if ($ff == "government & law")
	       					$ff = "social sciences & law";
	       				else if ($ff == "sociology")
	       					$ff = "social sciences & law";
	       			}
	       			$subjects = array_merge($subjects, $aa);
	       		} 
	        }

	    }
	    if (!feof($file)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file);
	}

	//$keywordst = array_count_values($keywords);
	//arsort( $keywordst );

	$keywordst = array_count_values($subjects);
	arsort( $keywordst );
	// $yearss = array_count_values($years);
	// arsort( $yearss );

	//var_dump($yearss);
	//var_dump($keywordst);
	//$file = fopen("subjectCounts.csv","w");
	$keys = array("plant sciences","environmental sciences & ecology","business & economics","water resources","genetics & heredity","food science & technology","public administration","forestry","biotechnology & applied microbiology","nutrition & dietetics","entomology","veterinary sciences","engineering","biochemistry & molecular/cell biology","chemistry","geology","public environmental & occupational health","meteorology & atmospheric sciences","biodiversity & conservation","fisheries","geography & remote sensing","social sciences & law","energy & fuels","anthropology","computer science","mathematics","imaging science & photographic technology","international relations","mathematical & computational biology","information science & library science");
	$cnt=0;
	/*foreach($keywordst as $key=>$val) {
		if (strlen(trim($key)) > 0 && trim($key) && trim($key) != 'agriculture' && trim($key) != 'science & technology - other topics' && trim($key) != 'parasitology' && trim($key) != 'microbiology') {
			/*if (strtolower(trim($key)) == "biochemistry & molecular biology")
	       		$ad = "biochemistry & molecular/cell biology";
	       	else if (strtolower(trim($key)) == "cell biology")
	       		$ad = "biochemistry & molecular/cell biology";
	       	else
	       		$ad = strtolower(trim($key)); 
			$keys[] = strtolower(trim($key));
			$cnt++;
			fwrite($file, trim(strtolower($key)).",".trim($val)."\n");
			if ($cnt == 30)
				break;
		}
	} 
	fclose($file); */
	
	// var_dump($keys);
	$fileout = fopen("../data/streamgraph.csv","w");
	//var_dump($yearkeys);

	foreach($yearkeys as $yr=>$ky) {
		$kys = explode(";",$ky);

		foreach($keys as $key) {
			//echo $key . "\t";
			$cnt = 0;
			foreach($kys as $k) {
				//echo strtolower(trim($k))  . "\t" . strtolower(trim($key)) . "\n";
				if (strtolower(trim($k)) == strtolower(trim($key)))
					$cnt++;
			}
			fwrite($fileout, str_replace(",","",$key).",".$cnt.",01/".(substr($yr,-2)+10)."/".substr($yr,-2)."\n");
			//echo $cnt . "\n";
			//break;
		}
	}
	fclose($fileout);
	
	//var_dump($yearkeys);


	//var_dump($obj);
	//echo count($obj) . "\n";


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
?>