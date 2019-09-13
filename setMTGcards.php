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

foreach($dataArray as $deck) 	{
	foreach($deck->cards as $card) {
		$name = $card->name;
		$preparedName = "'" . Database::sanitizeString($name) . "'";
		$checkNameEx = "SELECT * FROM mtg_cards WHERE name = {$preparedName}";
		$checkNameResult = Database::query($checkNameEx);
		$cardId;
		if($checkNameResult->num_rows == 0) {
			$insertCardEx = "INSERT INTO mtg_cards (
				name)
				VALUES (
				{$preparedName}
			)";
			Database::query($insertCardEx);
			$cardId = Database::$connection->insert_id;
		}
		else {
			continue;
		}

		$typesArray = $card->types;
		$typeId;
		foreach($typesArray as $type) {
			$preparedType = "'" . Database::sanitizeString($type) . "'";
			$checkTypeEx = "SELECT * FROM mtg_types WHERE name = {$preparedType}";
			$checkTypeResult = Database::query($checkTypeEx);
			if($checkTypeResult->num_rows == 0) {
				$insertTypeEx = "INSERT INTO mtg_types (
					name)
				VALUES (
					{$preparedType}
				)";
				Database::query($insertTypeEx);
				$typeId = Database::$connection->insert_id;
			}
			else {
				$responseTypeArray = $checkTypeResult->fetch_assoc();
				$typeId = intval($responseTypeArray["id"]);
			}
			$insertRelCardTypeEx = "INSERT INTO mtg_cardTypes (
				card,
				type)
			VALUES (
				{$cardId},
				{$typeId}
			)";
			Database::query($insertRelCardTypeEx);
		}
		$subtypesArray = $card->subtypes;
		$subtypeId;
		foreach($subtypesArray as $subtype) {
			$preparedSubtype = "'" . Database::sanitizeString($subtype) . "'";
			$checkSubtypeEx = "SELECT * FROM mtg_subtypes WHERE name = {$preparedSubtype}";
			$checkSubtypeResult = Database::query($checkSubtypeEx);
			if($checkSubtypeResult->num_rows == 0) {
				$insertSubtypeEx = "INSERT INTO mtg_subtypes (
					name)
					VALUES (
					{$preparedSubtype}
				)";
				Database::query($insertSubtypeEx);
				$subtypeId = Database::$connection->insert_id;
			}
			else {
				$responseSubtypeArray = $checkSubtypeResult->fetch_assoc();
				$subtypeId = intval($responseSubtypeArray["id"]);
			}
			$insertRelCardSubtypeEx = "INSERT INTO mtg_cardSubtypes (
				card,
				subtype)
				VALUES (
				{$cardId},
				{$subtypeId}
			)";
			Database::query($insertRelCardSubtypeEx);
		}
		$supertypesArray = $card->supertypes;
		$supertypeId;
		foreach($supertypesArray as $supertype) {
			$preparedSupertype = "'" . Database::sanitizeString($supertype) . "'";
			$checkSupertypeEx = "SELECT * FROM mtg_supertypes WHERE name = {$preparedSupertype}";
			$checkSupertypeResult = Database::query($checkSupertypeEx);
			if($checkSupertypeResult->num_rows == 0) {
				$insertSupertypeEx = "INSERT INTO mtg_supertypes (
					name)
					VALUES (
					{$preparedSupertype}
				)";
				Database::query($insertSupertypeEx);
				$supertypeId = Database::$connection->insert_id;
			}
			else {
				$responseSupertypeArray = $checkSupertypeResult->fetch_assoc();
				$supertypeId = intval($responseSupertypeArray["id"]);
			}
			$insertRelCardSupertypeEx = "INSERT INTO mtg_cardSupertypes (
				card,
				supertype)
				VALUES (
				{$cardId},
				{$supertypeId}
			)";
			Database::query($insertRelCardSupertypeEx);
		}
	}

}

?>
