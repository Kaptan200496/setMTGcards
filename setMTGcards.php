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
$dataArray = [];
foreach($files as $fileName) {
	$fullFileName = 'data/' . $fileName;
	array_push($dataArray, json_decode(file_get_contents($fullFileName)));
}

file_put_contents('dataArray.txt', json_encode($dataArray));
?>
