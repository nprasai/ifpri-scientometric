<?php

	$file = fopen("../data/webofscience_cgiar_journalarticles_12131_checked.txt","r");
	$cnt = 0;
	$obj = array();

	$countries = getCountries();
	//var_dump($countries);


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
	       			if (isset($data[$i]))
	       				$f[trim(str_replace("\"","",$header[$i]))] = ucwords(strtolower(trim(str_replace("\"","",$data[$i]))));
	       		}
	       		if (isset($f['C1']) && isset($f['PY']) && strlen($f['PY']) ==4)
	       			$obj[] = $f;
	        }

	    }
	    if (!feof($file)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file);
	}

	//var_dump($obj);
	//echo count($obj) . "\n";
	$affils = array();
	$unique = array();
	$uniquelocations = array();

	foreach($obj as $o) {
		//echo $o['C1'] . "\n";
		$ff = preg_replace('/\[.*?\]/', '', $o['C1']);
		//echo $ff . "\n";
		$d = explode(".", trim($ff));
		$locations = array();
		foreach($d as $dd) {
			if (strlen(trim($dd)) > 0) {
				$a = explode(",",trim($dd));
				$as = trim($a[count($a)-1]);
				if (substr($as,-3) == "Usa") {
					$locations[] = "USA";
				} else if (substr($as,-12) == "Univ Sao Pau") {
					$locations[] = "Brazil";
				} else if (substr($as,-10) == "Washington") {
					$locations[] = "USA";
				} else if (substr($as,-24) == "Nat Resources Canada Can") {
					$locations[] = "Canada";
				} else if (substr($as,-15) == "Penn State Univ") {
					$locations[] = "USA";
				} else if (substr($as,-15) == "Peoples R China") {
					$locations[] = "China";
				} else if (substr($as,-18) == "Bangladesh Rice Res") {
					$locations[] = "Bangladesh";
				} else if (substr($as,-15) == "B-1200 Brussels") {
					$locations[] = "Belgium";
				} else if (substr($as,-6) == "Israil") {
					$locations[] = "Israel";
				} else if (substr($as,-8) == "Abingdon") {
					$locations[] = "USA";
				} else if (substr($as,-7) == "Ibaraki") {
					$locations[] = "Japan";		
				}  else {
					$locations[] = $as;
				}
			}

			
		}
		$e = array_unique($locations);
		$locations = array();
		foreach($e as $loc) {
			$match = false;
			foreach($countries as $count) {
				if (strtolower($count) == strtolower($loc)) {
					$match = true;
					break;
				}
			}
			if ($match) {
				//echo "MATCH\t" . $val . "\n";
				$locations[] = $loc;
				$uniquelocations[] = $loc;
			} else {
				//echo $val . "\n";
			}
		}
		if (count($locations) > 1) {
			$affils[] = (Object)array("countries"=>$locations, "year"=>$o['PY']);
			//$unique = array_merge($unique, $locations);
			//$prevyear = $o['PY'];
		}
		
	}
	// var_dump($affils);
	$q = array_unique($uniquelocations);
	asort($q);
	$god = (Object)array();
	for($i=2000;$i<2017;$i++) {
		$asdf = ($i."-01");
		$god->$asdf = array();
		foreach($q as $aa) {
			$god->$asdf[$aa] = array();
			foreach($q as $bb) {
				$god->$asdf[$aa][$bb] = 0;
			}
		}
	}
	

	foreach($affils as $locations) {
		$year = $locations->year."-01";
		foreach($locations->countries as $loc1) {
			foreach($locations->countries as $loc2) {
				if ($loc1 != $loc2) {
					$god->$year[$loc1][$loc2]++;
				}
			}
		}
	}

	// var_dump($god);
	$god2 = (Object)array();

	foreach($god as $year=>$locations) {
		$god2->$year = array();
		foreach($locations as $key=>$val) {
			$tmp = array();
			foreach($val as $k=>$l) {
				$tmp[] = ($l > 0) ? $l/100 : 6.37e-06;
			}
			$god2->$year[] = $tmp;
		}
	}
	$fileout = fopen("matrix.json","w");
	fwrite($fileout, json_encode($god2));
	fclose($fileout);


	//var_dump($uniquelocations);
	/* $fileout = fopen("unique_countries.csv","w");
	$q = array_unique($uniquelocations);
	asort($q);
	foreach($q as $aa) {
		fwrite($fileout, trim($aa)."\n");
	}
	fclose($fileout); */

	//var_dump($affils);
	/* $af = array_unique($unique);
	asort($af);
	foreach($af as $val) {
		$match = false;
		foreach($countries as $count) {
			if (strtolower($count) == strtolower($val)) {
				$match = true;
				break;
			}
		}
		if ($match) {
			//echo "MATCH\t" . $val . "\n";
		} else {
			//echo $val . "\n";
		}
	} */
	$nc = array();
	foreach($affils as $aff) {
		if ($aff->year == "2015") {
			foreach($aff->countries as $c1) {
				foreach($aff->countries as $c2) {
					if ($c1 != $c2) {
						if (!isset($nc[$c1])) {
							$nc[$c1] = array();
						}
						if (isset($nc[$c1][$c2])) {
							$nc[$c1][$c2]++;
						} else {
							$nc[$c1][$c2] = 1;
						}
					}
				}
			}
		}
	}

	//var_dump($nc);
	$file = fopen("data.csv","w");
	foreach($nc as $a=>$val) {
		foreach($val as $b=>$v2) {
			fwrite($file, $a . "," . $b . "," . trim($v2) . "\n" );
		}
	}
	fclose($file);


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