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


?>
