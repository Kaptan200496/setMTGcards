<?php 
/*Файл предназначен для обращения к API сервиса scryfall*/
 // Ссылка на документацию API https://scryfall.com/docs/api/cards/named
class Scryfall {
	// url сервиса 
	private static $serviceUrl = "https://api.scryfall.com/cards";
	// Метод для перевода данных в удобные для использования в строки запроса
	// $name(string) - название аргумента
	// $value(mix) - значение аргумента
	function generateQueryStringArgument($name, $value) {
		// Переводим данные с помощью urlencode
		$encodedValue = urlencode($value);
		$result = "{$name}={$encodedValue}";
		return $result;
	}
	// Метод для подачи запроса к api сервиса scryfall
	// $method(string) - метод запроса 
	// $rawArguments(array) - "сырые" аргументы запроса
	public function request($method, $rawArguments = []) {
		$arguments = [];
		// Перебираем "сырые" аргументы , пушим их в массив 
		foreach ($rawArguments as $argName => $argValue) {
			array_push($arguments, self::generateQueryStringArgument($argName, $argValue));
		}
		// Разбиваем аргументы для строки запроса
		$argumentsString = implode("&", $arguments);
		// Строка с адресом api
		$serviceUrl = self::$serviceUrl;
		// Собираем строку для подачи запроса
		$apiAddress = "{$serviceUrl}/{$method}?{$argumentsString}";
		// Декодируем полученые данные
		$requestResult = json_decode(file_get_contents($apiAddress));
		return $requestResult;
	}
}
?>
