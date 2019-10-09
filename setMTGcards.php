<?php 
// Файл который достает данные из jsonс колодами карт и записывает все данные о каждой карте в таблицы
// и создает отношения между картой типом , картой надтипом  и картой подтипом и записывает их в таблицы
// Подключение нужных файлов и классов
require_once("class-database.php");
require_once("class-settings-provider.php");
require_once("settings.php");
// Подключаемся к базе данных с помощью с метода connect
Database::connect();
// Массив с названиями файлов json с данными колод карт
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
// Создаем пустой массив для заполнения данными из файлов
$dataArray = [];
// Перебираем данные из файлов массива $files  и записываем их в пустой массив созданый ранее
foreach($files as $fileName) {
	$fullFileName = 'data/' . $fileName;
	array_push($dataArray, json_decode(file_get_contents($fullFileName)));
}
// Перебираем массив данных по 1 колоде 
foreach($dataArray as $deck) 	{
	// Перебираем колоду по 1 карте и записываем данные о карте в таблицы
	foreach($deck->cards as $card) {
		// Предзаготавливаем данные и очищаем их для записи в базу данных и чистоты кода
		$name = $card->name;
		$preparedName = "'" . Database::sanitizeString($name) . "'";
		// Создаем выражение для проверки карты в таблице
		$checkNameEx = "SELECT * FROM mtg_cards WHERE name = {$preparedName}";
		// Отсылаем запрос в базу данных
		$checkNameResult = Database::query($checkNameEx);
		$cardId;
		$manaCost = "'" . Database::sanitizeString($card->manaCost) . "'";
		$preparedManaCost = isset($card->manaCost) ? $manaCost : 'NULL';
		$text = "'" . Database::sanitizeString($card->text) . "'";
		$preparedText = isset($card->text) ? $text : 'NULL';
		$power = "'" . Database::sanitizeString($card->power) . "'";
		$preparedPower = isset($card->power) ? $power : 'NULL';
		$toughness = "'" . Database::sanitizeString($card->toughness) . "'";
		$preparedToughness = isset($card->toughness) ? $toughness : 'NULL';
		// Если в таблице ничего нет , то записываем в неё данные о карте, если данные есть, то продолжаем перебор
		if($checkNameResult->num_rows == 0) {
			$insertCardEx = "INSERT INTO mtg_cards (
				name,
				manaCost,
				text,
				power,
				toughness)
				VALUES (
				{$preparedName},
				{$preparedManaCost},
				{$preparedText},
				{$preparedPower},
				{$preparedToughness}
			)";
			Database::query($insertCardEx);
			$cardId = Database::$connection->insert_id;
		}
		else {
			continue;
		}
		// Записываем массив типов , для перебора его и отдельной записи каждого типа в таблицу
		$typesArray = $card->types;
		// Создаем переменную для айди последней записи в таблицу 
		$typeId;
		// Перебираем массив типов
		foreach($typesArray as $type) {
			// Очищаем данные и создаем выражение для проверки имеющегося типа в таблице, отправляем запрос
			$preparedType = "'" . Database::sanitizeString($type) . "'";
			$checkTypeEx = "SELECT * FROM mtg_types WHERE name = {$preparedType}";
			$checkTypeResult = Database::query($checkTypeEx);
			// Если проверка не выявила данных в таблице , то записываем типы в неё, передавая $typeId последний записаный айди
			// Если проверка выявила данные , то записываем айди из базы в предзаготовленую переменную.
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
			// Запись отношения карты к её типу
			$insertRelCardTypeEx = "INSERT INTO mtg_cardTypes (
				card,
				type)
			VALUES (
				{$cardId},
				{$typeId}
			)";
			Database::query($insertRelCardTypeEx);
		}
		//Создаем пермеенную с массивом подтипов так как их может быть не 1 
		$subtypesArray = $card->subtypes;
		// Переменная с айди последней записи
		$subtypeId;
		// Перебираем массив с подтипами
		foreach($subtypesArray as $subtype) {
			// Очищаем данные
			$preparedSubtype = "'" . Database::sanitizeString($subtype) . "'";
			// Вырадение для проверки наличия данных в таблице
			$checkSubtypeEx = "SELECT * FROM mtg_subtypes WHERE name = {$preparedSubtype}";
			$checkSubtypeResult = Database::query($checkSubtypeEx);
			// Если данных не найдено то записываем наши данные в таблицу
			if($checkSubtypeResult->num_rows == 0) {
				$insertSubtypeEx = "INSERT INTO mtg_subtypes (
					name)
					VALUES (
					{$preparedSubtype}
				)";
				Database::query($insertSubtypeEx);
				$subtypeId = Database::$connection->insert_id;
			}
			// Если  данные имеются, то записываем айди из базы в переменную
			else {
				$responseSubtypeArray = $checkSubtypeResult->fetch_assoc();
				$subtypeId = intval($responseSubtypeArray["id"]);
			}
			//Выражение для отношения карты и подтипов, записываем отношение в таблицу
			$insertRelCardSubtypeEx = "INSERT INTO mtg_cardSubtypes (
				card,
				subtype)
				VALUES (
				{$cardId},
				{$subtypeId}
			)";
			Database::query($insertRelCardSubtypeEx);
		}
		//Создаем переменную с массивом Надтипов так как может быть на 1 
		$supertypesArray = $card->supertypes;
		// Переменная для записи айди
		$supertypeId;
		foreach($supertypesArray as $supertype) {
			// Очищаем данные
			$preparedSupertype = "'" . Database::sanitizeString($supertype) . "'";
			// Выражение для проверки наличия данных
			$checkSupertypeEx = "SELECT * FROM mtg_supertypes WHERE name = {$preparedSupertype}";
			// Делаем запрос к базе
			$checkSupertypeResult = Database::query($checkSupertypeEx);
			// Если данных нет то записываем их в таблицу и айди последней запсии в переменную
			if($checkSupertypeResult->num_rows == 0) {
				$insertSupertypeEx = "INSERT INTO mtg_supertypes (
					name)
					VALUES (
					{$preparedSupertype}
				)";
				Database::query($insertSupertypeEx);
				$supertypeId = Database::$connection->insert_id;
			}
			// Если данные есть то записываем в переменную айди из базы данных
			else {
				$responseSupertypeArray = $checkSupertypeResult->fetch_assoc();
				$supertypeId = intval($responseSupertypeArray["id"]);
			}
			// Записываем отношение карты и надтипов
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
