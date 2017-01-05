<?php

	$argyear = $argv[1];

	$file = fopen("../data/webofscience_cgiar_journalarticles_12131_checked.txt","r");
	$cnt = 0;
	$obj = (Object)array();
	$uniqueaffils = array();

	$affiliations = getAffils();
	// var_dump($affiliations);

	$asdf = array_unique($uniqueaffils);
	asort($asdf);
	//var_dump($asdf);
	/*foreach($asdf as $k) {
		echo $k . "\n";
	} */


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
	       			if (isset($data[$i])) {
		       			if ($header[$i] == "\"AB\"") {
		       				$f[trim(str_replace("\"","",$header[$i]))] = trim(str_replace("\"","",$data[$i]));
		       			} else if ($header[$i] == "\"AU\"") {
		       				$f[trim(str_replace("\"","",$header[$i]))] = array_map('trim', explode(";",ucwords(strtolower(trim(str_replace("\"","",$data[$i]))))));
		       			} else {
		       				$f[trim(str_replace("\"","",$header[$i]))] = (strtolower(trim(str_replace("\"","",$data[$i]))));
		       			}
		       		}
	       		}
	       		if (isset($f['PY']) && isset($f['UT']) && (strlen($f['UT']) > 0) && strlen($f['PY']) == 4) {
	       			$id = $f['UT'];
	       			if (property_exists($affiliations,$id)) {
		       			//echo $id . "\n";
		       			if ($f['PY'] < 2005)
		       				$a =  2004;
		       			else if ($f['PY'] < 2010)
		       				$a = 2009;
		       			else
		       				$a = 2016;
		       			$obj->$id = (Object)array("year"=>$a);
		       			$u = array_unique($affiliations->$id);
		       			$obj->$id->affils = $u;
		       		}
	       		}
	        }

	    }
	    if (!feof($file)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($file);
	}

	/*foreach($obj as $y) {
		if ($y->year == 2000) {
			if (in_array("AfricaRice",$y->affils)) {
				var_dump($y->affils);
			}
			
		}
	} */

	$nc = array();
	foreach($obj as $aff) {
		if ($aff->year == $argyear) {
			if (count($aff->affils) == 1) {
				$c1 = $aff->affils[0];
				if (!isset($nc[$c1])) {
					$nc[$c1] = array();
				}
				if (isset($nc[$c1][$c1])) {
					$nc[$c1][$c1]++;
				} else {
					$nc[$c1][$c1] = 1;
				}
			} else {
				foreach($aff->affils as $c1) {
					foreach($aff->affils as $c2) {
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
	}

	//var_dump($nc);

	
	$file = fopen("../data/chords/crp".$argyear.".csv","w");
	fwrite($file, "who,overlap,years,color\n");
	$r = (Object)array();
	foreach($nc as $a=>$val) {
		$r->$a = array();
		foreach($val as $b=>$v2) {
			$r->$a[] = trim($b). "," . trim($v2) . ",#333333";
			//fwrite($file, $a . "," . $b . "," . trim($v2) . ",#333333\n" );
		}
	}

	$rArrayObject = new ArrayObject($r);
	$rArrayObject->ksort();

	//var_dump($rArrayObject);
	foreach($rArrayObject as $key=>$val) {
		foreach($val as $v) {
			fwrite($file, $key.",".$v."\n");
		}
	}
	fclose($file);


	function getAffils() {
		global $uniqueaffils;
		$f = "/var/www/html/ifpri/data/webofscience_cgiar_journalarticles_institutions_12131_checked.txt";
		$file = fopen($f, "r");
		$de = (Object)array();
		if ($file) {
		    while (($buffer = fgets($file, 4096)) !== false) {
		    	$e = explode("	",$buffer);
		    	$affil = str_replace("\"","",trim($e[3]));
		    	$id = strtolower(str_replace("\"","",trim($e[0])));
		    	if (strlen($affil) == 0) {
		    		$affil = "Non-CGIAR";
		    	}
		    		if(property_exists($de,$id))
		    			$de->$id[] = $affil;
		    		else
		    			$de->$id = array($affil);

		    		$uniqueaffils[] = $affil;
		    	//}
		    }
		    if (!feof($file)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($file);
		}
		return $de;
	}
?>