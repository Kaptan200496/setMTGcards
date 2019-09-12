<?php 
require_once("class-database.php");
require_once("class-settings-provider.php");
require_once("settings.php");
Database::connect();
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

for($i = 0; $i < count($dataArray); $i++) {
// перебор массива файлов
	for($j = 0; $j < count($dataArray[$i]->cards); $j++) {
		// Перебор данных в каждом файле
		$currentCard = $dataArray[$i]->cards[$j];
		$name = isset($currentCard->name) ? $currentCard->name : NULL;
		$prepearedName = "'" . Database::sanitizeString($name) . "'";
		$manaCost = 	isset($currentCard->manaCost) ? $currentCard->manaCost : NULL;
		$prepearedManaCost = "'" . Database::sanitizeString($manaCost) . "'";
		$text = isset($currentCard->text) ? $currentCard->text : NULL;
		$prepearedText = "'" . Database::sanitizeString($text) . "'";
		$power = isset($currentCard->power) ? $currentCard->power : NULL;
		$prepearedPower = "'" . Database::sanitizeString($power) . "'";
		$toughness = isset($currentCard->toughness) ? $currentCard->toughness : NULL;
		$prepearedToughness = "'" . Database::sanitizeString($toughness) . "'";

		foreach($currentCard->types as $value) {
			$typesArray = isset($value) ? "'{$value}'" : [];
		}
		foreach($currentCard->subtypes as $value) {
			$subtypesArray = isset($value) ? "'{$value}'" : [];
		}
		foreach($currentCard->supertypes as $value) {
			$supertypesArray = isset($value) ? "'{$value}'" : [];
		}

		//file_put_contents("subtypes.txt", json_encode($subtypesArray));
		//file_put_contents("supertypes.txt", json_encode($supertypesArray));
		//file_put_contents("types.txt", json_encode($typesArray));
		// Запись в базу данных
		$checkNameEx = "SELECT * FROM mtg_cards WHERE name = {$prepearedName}";
		$responseValidation = Database::query($checkNameEx);
		if($responseValidation->num_rows == 0) {
			$insertExCards = "INSERT INTO mtg_cards (
				name,
				manaCost,
				text,
				power,
				toughness
			) VALUES (
				{$prepearedName},
				{$prepearedManaCost},
				{$prepearedText},
				{$prepearedPower},
				{$prepearedToughness}
			)";
			Database::query($insertExCards);		
			$lastId = Database::$connection->insert_id;
		}
		$selectTypeEx = "SELECT * FROM mtg_types WHERE name = {$typesArray}";
		$responseSelectTEx = Database::query($selectTypeEx);
		if($responseSelectTEx->num_rows == 0) {
			$insertTypeEx = "INSERT INTO mtg_types (name) VALUES ({$typesArray})";
			Database::query($insertTypeEx);
		}
		$selectSubTypeEx = "SELECT * FROM mtg_subtypes WHERE name = {$subtypesArray}";
		$responseSelectSubTEx = Database::query($selectSubTypeEx);
		if($responseSelectSubTEx->num_rows == 0) {
			$insertSubTypeEx = "INSERT INTO mtg_subtypes (name) VALUES ({$subtypesArray})";
			Database::query($insertSubTypeEx);
		}
		$selectSuperTypeEx = "SELECT * FROM mtg_supertypes WHERE name = {$supertypesArray}";
		$responseSelectSuperTEx = Database::query($selectSuperTypeEx);
		if($responseSelectSuperTEx->num_rows == 0) {
			$insertSuperTypeEx = "INSERT INTO mtg_supertypes (name) VALUES ({$supertypesArray})";
			Database::query($insertSuperTypeEx);
		}
	}
}

?>
