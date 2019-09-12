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
		$name = isset($currentCard->name) ? "'{$currentCard->name}'" : "NULL";
		$manaCost = isset($currentCard->manaCost) ? "'{$currentCard->manaCost}'" : "NULL";
		$text = isset($currentCard->text) ? "'{$currentCard->text}'" : "NULL";
		$power = isset($currentCard->power) ? "'{$currentCard->power}'" : "NULL";
		$toughness = isset($currentCard->toughness) ? "'{$currentCard->toughness}'" : "NULL";

		$typesArray = isset($currentCard->types) ? $currentCard->types[0] : [];
		for($k = 0; $k < count($currentCard->subtypes); $k++) {
			$subtypesArray =  isset($currentCard->subtypes[$k]) ? $currentCard->subtypes[$k] : [];
		}
		$supertypesArray = isset($currentCard->supertypes) ? $currentCard->supertypes[0] : [];

		file_put_contents("subtypes.txt", json_encode($subtypesArray));
		file_put_contents("supertypes.txt", json_encode($supertypesArray));
		file_put_contents("types.txt", json_encode($typesArray));
		// Запись в базу данных
		$checkNameEx = "SELECT * FROM mtg_cards WHERE name = {$name}";
		$responseValidation = Database::query($checkNameEx);
		if($responseValidation->num_rows == 0) {
			$insertExCards = "INSERT INTO mtg_cards (
				name,
				manaCost,
				text,
				power,
				toughness
			) VALUES (
				{$name},
				{$manaCost},
				{$text},
				{$power},
				{$toughness}
			)";
		Database::query($insertExCards);		
		$lastId = Database::$connection->insert_id;
		$selectTypeEx = "SELECT * FROM mtg_types WHERE name = {$typesArray}";
		Database::query($selectTypeEx);
		$insertTypeEx = "INSERT INTO mtg_types (name) VALUES ({$typesArray})";
		Database::query($insertTypeEx);

		}
	}
}

?>
