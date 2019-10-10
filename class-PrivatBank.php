<?php 
/*Файл предназначен для обращения к API сервиса privatbank*/
 // Ссылка на документацию API https://api.privatbank.ua/#p24/branch
class PrivatBank {
	// url сервиса
	private static $serviceUrl = "https://api.privatbank.ua/p24api";

	function generateQueryStringArgument($name, $value) {
		// Переводим данные в удобные для использования в адресной строке
		$encodedValue = urlencode($value);
		$result = "{$name}={$encodedValue}";
		return $result;
	}

	// Метод для запроса к api приватбанка
	// $method(string) - метод запроса 
	// $rawArguments(array) - "сырые" аргументы запроса
	// Возвращает запрашеваемые данные(расположение отделений или курс валют)
	public function request($method, $rawArguments = []) {
		$arguments = [];
		// Перебор "сырых" аргументов и пуш в пустой массив созданный ранее
		foreach ($rawArguments as $argName => $argValue) {
			array_push($arguments, self::generateQueryStringArgument($argName, $argValue));
		}
		// Разделяем массив через & в строку аргументов для запроса к приватбанку
		$argumentsString = implode("&", $arguments);
		// Ссылка на api приватбанка
		$serviceUrl = self::$serviceUrl;
		// Собираем api address целиком
		$apiAddress = "{$serviceUrl}/{$method}?{$argumentsString}";
		// Подаем запрос, декодируем полученые данные
		$requestResult = json_decode(file_get_contents($apiAddress));
		// Возвращаем результат
		return $requestResult;
	}

}
