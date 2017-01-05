<?php

	$file = fopen("../data/webofscience_cgiar_journalarticles_12131_checked.txt","r");
	$cnt = 0;
	$obj = array();

	$countries = getCountries();
	$aaffiliations = getAffils();
	//var_dump($aaffiliations);
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
	   
	       		if (isset($f['DI']) && (strlen($f['DI']) >1) && isset($f['TI']) && isset($f['PY']) && isset($f['UT']) && (strlen($f['UT']) > 3 || isset($f['DI']))) {
	       			if (strlen($f['PY']) != 4)
	       				$f['PY'] = $previousyear;
	       			$keys = isset($f['ID']) ? array_map('trim',explode(";",$f['ID'])) : "";
	       			$subjects = isset($f['SC']) ? array_map('trim',explode(";",$f['SC'])) : "";
	       			$authors = implode(", ",$f['AU']);
	       			$year = $f['PY'];
	       			//$abstract = $f['AB'];
	       			$title = $f['TI'];
	       			//$affils = $f['C1'];

	       			$corresponding = $f['RP'];
	       			$id = isset($f['UT']) ? strtolower($f['UT']) : "";
	       			if (isset($aaffiliations->$id)) {
	       				$a = array_unique($aaffiliations->$id);
	       				$org = implode($a, ",");
	       			} else {
	       				$org = "Non-CGIAR";
	       			}
	       			$doi = "<a target='_blank' href='https://doi.org/".$f['DI']."'>".$f['DI']."</a>";

	       			$obj[] = array("title"=>$title, "authors"=>$authors,"org"=>$org,"year"=>$year,"id"=>strtolower($id),"doi"=>$doi);
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

	//var_dump($obj);

	$fileout = fopen("../data/table/articles.json","w");
	fwrite($fileout, json_encode($obj));
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

	function getAffils() {
		//global $uniqueaffils;
		$f = "/var/www/html/ifpri/data/webofscience_cgiar_journalarticles_institutions_12131_checked.txt";
		$file = fopen($f, "r");
		$de = (Object)array();
		if ($file) {
		    while (($buffer = fgets($file, 4096)) !== false) {
		    	$e = explode("	",$buffer);
		    	$affil = trim(str_replace("\"","",trim($e[3])));
		    	$id = strtolower(str_replace("\"","",trim($e[0])));
		    	if (strlen($affil) == 0) {
		    		$affil = "Non-CGIAR";
		    	}
		    		if(property_exists($de,$id))
		    			$de->$id[] = $affil;
		    		else
		    			$de->$id = array($affil);

		    		//$uniqueaffils[] = $affil;
		    	
		    }
		    if (!feof($file)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($file);
		}
		return $de;
	}
?>