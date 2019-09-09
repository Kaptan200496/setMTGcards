<?php 
$files = array(
	'DOM.json', 
	'GRN.json', 
	'M19.json', 
	'M20.json', 
	'RIX.json',
	'RNA.json',
	'WAR.json',
	'XLN.json'
);
foreach($files as $fileToInclude) {
	$data = file_get_contents($fileToInclude);
	$arrayData = [];
	array_push($arrayData, $data);
}

file_put_contents('arrayData.txt', $arrayData);
?>
