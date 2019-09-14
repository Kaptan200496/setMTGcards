<?php  
// Подключить класы бд, телеграма, приватбанка, скрайфол,настройки и класс настроек
$includes = [
	"class-scryfall.php",
	"class-PrivatBank.php",
	"class-TelegramBot.php",
	"class-database.php",
	"class-settings-provider.php",
	];
foreach($includes as $fileToInclude) {
	require_once($fileToInclude);
	}
require_once("settings.php");
// Подключиться к базе
Database::connect();
// Создать пустые переменные  с объектами ботов для дальнейшей работы
$sf = new Scryfall();
$pb = new PrivatBank();
$bot = new TelegramBot("bottoken");

// Получить данные от пользоватля телеграма
$requestText = file_get_contents("php://input");
// Обработать данные о названии карты и поместить ее в переменную для удобства работы с ней
$requestObject = json_decode($requestText);
$message = Database::sanitizeString($requestObject->message->text);
// Вытянуть данные из базы относительно названия карты и поместить их в переменные для работы с ними
$checkDataEx = "SELECT * FROM mtg_cards WHERE name = '{$message}'";
$checkDataExResult = Database::query($checkDataEx);
$cardObject = new stdClass();
$card = new stdClass();
if($checkDataExResult->num_rows > 0) {
	$dataArray = $checkDataExResult->fetch_assoc();
	$cardObject->name = $dataArray["name"];
	$cardObject->manaCost = $dataArray["manaCost"];
	$cardObject->text = $dataArray["text"];
	$cardObject->powerTougnhess = $dataArray["power"] . "/" . $dataArray["toughness"];
}
else {
	print ("Карта не найдена");
}
// Выятнуть все виды типов из базы относительно айди карты
// Выягиваем тип и заносим его в объект
$selectTypeEx = "
		SELECT 
			mtg_types.name 
		FROM mtg_cards
			JOIN mtg_cardTypes ON mtg_cards.id = mtg_cardTypes.card
			JOIN mtg_types ON mtg_cardTypes.type = mtg_types.id
		WHERE mtg_cards.name = '{$message}'
		";
	$selectTypeResult = Database::query($selectTypeEx);
	if($selectTypeResult->num_rows > 0) {
		$typeArray = [];
		for($i = 0; $i < $selectTypeResult->num_rows; $i++) {
		$type = $selectTypeResult->fetch_assoc();
		array_push($typeArray, $type);
		}
		$card->type = $typeArray;
	}
	// Если типа нет, то занросим в обхект пустую строку
	else {
		$card->type = "";
	}
// Вытягиваем подтип и заносим его в объект 
$selectSubtypeEx = "
		SELECT 
			mtg_subtypes.name 
		FROM mtg_cards
			JOIN mtg_cardSubtypes ON mtg_cards.id = mtg_cardSubtypes.card
			JOIN mtg_subtypes ON mtg_cardSubtypes.subtype = mtg_subtypes.id
		WHERE mtg_cards.name = '{$message}'
		";
		$selectSubtypeResult = Database::query($selectSubtypeEx);
		if($selectSubtypeResult->num_rows > 0) {
			// Создаем пустой массив на случай если строк больше 1
			$subtypeArray = [];
			// Создаем цикл для перебора строк 
			for($i = 0; $i < $selectSubtypeResult->num_rows; $i++) {
				$subtype = $selectSubtypeResult->fetch_assoc();
				array_push($subtypeArray, $subtype);
			}
			// Заносим в объект массив 
				$card->subtype = $subtypeArray;		
		}
		// Если база ничего не выдала, то заносим пустую строку в объект
		else {
			$card->subtype = "";
		}
// Создаем запрос и вытягиваем данные о надтипах из базы
$selectSupertypeEx = "
		SELECT 
			mtg_supertypes.name 
		FROM mtg_cards
			JOIN mtg_cardSupertypes ON mtg_cards.id = mtg_cardSupertypes.card
			JOIN mtg_supertypes ON mtg_cardSupertypes.supertype = mtg_supertypes.id
		WHERE mtg_cards.name = '{$message}'
		";
		$selectSupertypeResult = Database::query($selectSupertypeEx);
		if($selectSupertypeResult->num_rows > 0) {
			// Создаем пустой массив на случай если строк больше 1
			$supertypeArray = [];
			// Создаем цикл для перебора строк
			for($i = 0; $i < $selectSupertypeResult->num_rows; $i++) {
				$supertype = $selectSupertypeResult->fetch_assoc();
				array_push($supertyArray, $supertype);
			}
			// Заносим массив в объект
			$card->supertype = $supertyArray;
		}
		// Если база ничего не выдала, то заносим в объект пустую строку
		else {
			$card->supertype = "";
		}
// Создаем сырые аргументы для запроса приват банку так же создаем  пустой объект для помещения туда

$rawArgumentsPB = [
	"json" => 1,
	"exchange" => 1,
	"coursid" => 11 
];
// Отправляем запрос приват банку методом pubinfo и получаем курс гривны и доллара на данный момент
$rateData = $pb->request("pubinfo", $rawArgumentsPB);
$card->rate = $rateData[0]->sale;
// Создаем сырые аргумент для запроса классу scryfall 
// ссылки на изображение и цены карты
$rawArgumentsSF = [
	'json' => 1,
	'exact' => $message
];
$cardData = $sf->request("named", $rawArgumentsSF);
$card->priceUSD = $cardData->prices->usd;
$card->address = $cardData->image_uris->large;
$card->priceUAH = $card->priceUSD * $card->rate;
$cardObject->type = $card->supertype . ' ' . $card->type . ' ' . '-' . $card->subtype;
$cardObject->price = "Цена:" . $card->priceUAH . "(" . $card->priceUSD . ")";
$methodMessage = "sendMessage";
$cardDescriptionRow = $cardObject->name . "\n" . $cardObject->manaCost . "\n" .
 $cardObject->type . "\n" . $cardObject->text . "\n" . $cardObject->powerTougnhess .
 "\n" .$cardObject->price;
$rawArgumentsMessage = [
	"chat_id" => $requestObject->message->chat->id,
	"text" => $cardDescriptionRow
];
$responsePhoto = $bot->request($methodMessage, $rawArgumentsMessage);
$methodPhoto = "sendPhoto";
$rawArgumentsPhoto = [
	"chat_id" => $requestObject->message->chat->id,
	"photo" => $card->address
];
$responseMessage = $bot->request($methodPhoto, $rawArgumentsPhoto);
?>
