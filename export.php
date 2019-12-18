<?php

$filename = 'verbrauch.sqlite3';

if(isset($_GET['year'])) {
	$selectYear = preg_replace('/[^0-9]/', '', $_GET['year']);
	$selectYear = trim($selectYear);
	$selectYear = intval($selectYear);
	if(is_int($selectYear) AND $selectYear > 2017 AND $selectYear < 2118) {
		//$queryString = 'SELECT * FROM "data" WHERE date >= "'.$selectYear.'"';
		$queryString = 'SELECT a.date,a.strom,a.gas,a.wasser,a.solar,a.strom_diff/a.days,a.gas_diff/a.days,a.wasser_diff/a.days,a.solar_diff/a.days,a.days FROM (SELECT date,strom,gas,wasser,solar,((strftime("%s",date) - strftime("%s",(LAG(date,1,0) OVER (ORDER BY date))))/86400) AS days,(strom - (LAG(strom,1,0) OVER (ORDER BY date))) AS strom_diff,(gas - (LAG(gas,1,0) OVER (ORDER BY date))) AS gas_diff,(wasser - (LAG(wasser,1,0) OVER (ORDER BY date))) AS wasser_diff,(solar - (LAG(solar,1,0) OVER (ORDER BY date))) AS solar_diff FROM data) AS a WHERE a.date >= "'.$selectYear.'"';
	} else {
		//$queryString = 'SELECT * FROM "data"';
		$queryString = 'SELECT a.date,a.strom,a.gas,a.wasser,a.solar,a.strom_diff/a.days,a.gas_diff/a.days,a.wasser_diff/a.days,a.solar_diff/a.days,a.days FROM (SELECT date,strom,gas,wasser,solar,((strftime("%s",date) - strftime("%s",(LAG(date,1,0) OVER (ORDER BY date))))/86400) AS days,(strom - (LAG(strom,1,0) OVER (ORDER BY date))) AS strom_diff,(gas - (LAG(gas,1,0) OVER (ORDER BY date))) AS gas_diff,(wasser - (LAG(wasser,1,0) OVER (ORDER BY date))) AS wasser_diff,(solar - (LAG(solar,1,0) OVER (ORDER BY date))) AS solar_diff FROM data) AS a';
	}
} else {
	//$queryString = 'SELECT * FROM "data"';
	$queryString = 'SELECT a.date,a.strom,a.gas,a.wasser,a.solar,a.strom_diff/a.days,a.gas_diff/a.days,a.wasser_diff/a.days,a.solar_diff/a.days,a.days FROM (SELECT date,strom,gas,wasser,solar,((strftime("%s",date) - strftime("%s",(LAG(date,1,0) OVER (ORDER BY date))))/86400) AS days,(strom - (LAG(strom,1,0) OVER (ORDER BY date))) AS strom_diff,(gas - (LAG(gas,1,0) OVER (ORDER BY date))) AS gas_diff,(wasser - (LAG(wasser,1,0) OVER (ORDER BY date))) AS wasser_diff,(solar - (LAG(solar,1,0) OVER (ORDER BY date))) AS solar_diff FROM data) AS a';
}

$returned = read_from_db($filename, $queryString);
array_to_csv_download($returned);

function read_from_db($filename, $queryString)
{
	$db = new SQLite3($filename);
	if(!$db)
	{
		SYSLOG(LOG_ERR, "verbrauch: ".$db->lastErrorMsg());
	} 
	else 
	{
		$results = $db->query($queryString);

		$results_array = array();
		while ($res = $results->fetchArray(SQLITE3_ASSOC)) {
			array_push($results_array, $res);	
		}
		$db->close();
	}
	return $results_array;
}

function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
	// open raw memory as file
	$f = fopen('php://memory', 'w'); 
	// loop over the input array
	$first_line = array('Datum', 'Strom', 'Gas', 'Wasser', 'Solar', 'Stromtagesdifferenz', 'Gastagesdifferenz', 'Wassertagesdifferenz', 'Solartagesdifferenz', 'Tagesdifferenzbasis');
	fputcsv($f, $first_line, $delimiter);
	foreach ($array as $line) { 
        	// generate csv lines
        	fputcsv($f, $line, $delimiter); 
	}
	// reset the file pointer to the start of the file
	fseek($f, 0);
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="'.$filename.'";');
	// send the generated csv lines to the browser
	fpassthru($f);
}

?>
