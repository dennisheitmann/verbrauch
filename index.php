<?php
if(isset($_GET['date']) AND isset($_GET['strom']) AND isset($_GET['gas']) AND isset($_GET['wasser']) AND isset($_GET['solar'])) {
	$date = preg_replace('/[^0-9-]/', '', $_GET['date']);
	$strom = preg_replace('/[^0-9.,]/', '', $_GET['strom']);
	$gas = preg_replace('/[^0-9.,]/', '', $_GET['gas']);
	$wasser = preg_replace('/[^0-9.,]/', '', $_GET['wasser']);
	$solar = preg_replace('/[^0-9]/', '', $_GET['solar']);
	$strom = str_replace(',', '.', $strom);
	$gas = str_replace(',', '.', $gas);
	$wasser = str_replace(',', '.', $wasser);
?>
<html lang="de-DE">                                                                                                                                                                                                
<head>
	<title>Verbrauchswerte</title>
	<link href="https://fonts.googleapis.com/css?family=Red+Hat+Display&display=swap" rel="stylesheet">
  	<meta http-equiv='pragma' content='no-cache' />
  	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  	<meta name='geo.region' content='DE' />
	<link rel='icon' href='favicon.png' type='image/png' sizes='100x100' />
	<link rel='shortcut icon' href='favicon.png' type='image/png' sizes='100x100' />
  	<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.00, maximum-scale=1.00, minimum-scale=1.00' />
  	<meta name="mobile-web-app-capable" content="yes">
  	<meta name="theme-color" content="black" />
	<style type='text/css'>
		body { font-family: 'Red Hat Display', sans-serif; font-size: 22px; color: white; text-align: center; background-color: black; }
		h1 { font-family: 'Red Hat Display', sans-serif; font-size: 24px; color: white; text-align: center; }
		p { font-family: 'Red Hat Display', sans-serif; font-size: 24px; color: red; text-align: center; }
	</style>
</head>
<body>
<h1>Folgende Werte wurden eingetragen:</h1>
<?php
	if ($strom > 0 AND $gas > 0 AND $wasser > 0 AND $solar > 0) {
		$return = write_to_db($date, $strom, $gas, $wasser, $solar);
		if($return == 0) {
			echo "Datum: ";
			echo $date;
			echo "<br />";
			echo "Strom: ";
			echo $strom;
			echo "<br />";
			echo "Gas: ";
			echo $gas;
			echo "<br />";
			echo "Wasser: ";
			echo $wasser;
			echo "<br />";
			echo "Solar: ";
			echo $solar;
			echo "<br />";
			echo "<br />";
		} else {
			echo "<p>Fehler! (Eintrag der Daten in die Datenbank gescheitert)</p>";
		}	
	} else {
		echo "<p>Fehler! (Daten fehlen oder sind ungültig / Null)</p>";
	}	
?>
<a href="index.php">zurück</a>
</body>
</html>
<?php
} else {
?>
<html lang="de-DE">
<head>
	<title>Verbrauchswerte</title>
	<link href="https://fonts.googleapis.com/css?family=Red+Hat+Display&display=swap" rel="stylesheet">
  	<meta http-equiv='pragma' content='no-cache' />
  	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  	<meta name='geo.region' content='DE' />
	<link rel='icon' href='favicon.png' type='image/png' sizes='100x100' />
	<link rel='shortcut icon' href='favicon.png' type='image/png' sizes='100x100' />
  	<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.00, maximum-scale=1.00, minimum-scale=1.00' />
  	<meta name="mobile-web-app-capable" content="yes">
  	<meta name="theme-color" content="black" />
	<style type='text/css'>
		body { font-family: 'Red Hat Display', sans-serif; font-size: 22px; color: white; text-align: center; background-color: black; }
		a { font-family: 'Red Hat Display', sans-serif; font-size: 12px; color: white; text-align: center; background-color: black; }
		input { font-family: 'Red Hat Display', sans-serif; font-size: 22px; color: black; background-color: white; width: 90%; min-width: 140px; max-width: 200px; }
		button { font-family: 'Red Hat Display', sans-serif; font-size: 20px; color: black; background-color: white; width: 95%; max-width: 120px; }
		h1 { font-family: 'Red Hat Display', sans-serif; font-size: 24px; color: white; text-align: center; margin-bottom: -8px; }
		td { padding: 10px; }
		table { font-family: 'Red Hat Display', sans-serif; font-size: 24px; color: white; text-align: center; width: 90%; max-width: 500px; position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); }
	</style>
</head>
<body onload="document.getElementById('dateInput').valueAsDate = new Date();">
<h1>Verbrauchsdaten eintragen</h1>
<span>
	<a href="export.php">Ausgabe der Daten als CSV-Datei</a>
	<a href="plot.html">Verbrauchsdiagramme</a>
</span>
<form lang="de-DE" accept-charset="UTF-8" action="index.php" autocomplete="off" method="GET" target="_self">
	<table>
		<tr><td>Datum</td><td><input required name="date" type="date" id="dateInput" value="" /><span style="color: red;">&ast;</span></td></tr>
		<tr><td>Strom</td><td><input required name="strom" type="number" step="0.1" value="" placeholder="0.0" /><span style="color: red;">&ast;</span></td></tr>
		<tr><td>Gas</td><td><input required name="gas" type="number" step="0.001" value="" placeholder="0.000" /><span style="color: red;">&ast;</span></td></tr>
		<tr><td>Wasser</td><td><input required name="wasser" type="number" step="0.001" value="" placeholder="0.000" /><span style="color: red;">&ast;</span></td></tr>
		<tr><td>Solar</td><td><input required name="solar" type="number" step="1" value="" placeholder="0" /><span style="color: red;">&ast;</span></td></tr>
		<tr><td><button type="submit" value="Submit">Submit</button></td></tr>
	</table>
</form>
</body>
</html>
<?php
}

function write_to_db($date, $strom, $gas, $wasser, $solar)
{
	$ret = 0;
	$db = new SQLite3('verbrauch.sqlite3');
	if(!$db)
	{
		SYSLOG(LOG_ERR, "verbrauch: ".$db->lastErrorMsg());
		$ret = 1;
	} 
	else 
	{
		$lastDate = $db->querySingle("SELECT date FROM data ORDER BY date DESC LIMIT 1");
		if($date != $lastDate) {
			$dbret = $db->exec("INSERT INTO 'data' ('date', 'strom', 'gas', 'wasser', 'solar') VALUES ('$date', $strom, $gas, $wasser, $solar)");
			if ($dbret == false) {
				$ret = 1;
			}
		} else {
			$ret = 1;
		}
		$db->close();
	}
	return $ret;
}

?>
