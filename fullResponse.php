<?php  
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
Database::connect();
$requestText = file_get_contents("php://input");
$requestObject = json_decode($requestText);
$message = $requestObject->message->text;
print json_encode($message);
?>
