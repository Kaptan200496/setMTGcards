 <?php
class TelegramBot {
	// url сервиса
	private static $serviceUrl = "https://api.telegram.org/bot";
	// Свойство для токена 
	private $token = "";
	
	public function __construct($botToken) {
		$this->token = $botToken;	
	}

	public function request($method, $rawArguments = []) {
		// Переменная с url сервиса к которому подаетсся запрос
		$serviceUrl = self::$serviceUrl;
		// Токен бота для запроса
		$botToken = $this->token;
		// Собираем строку для запроса
		$curl = "{$serviceUrl}{$botToken}/{$method}";
		// Создаем новый сеанс cURL
		$curlRequest = curl_init($curl);
		// Создаем массив с заголовками
		$headers = [
			"Content-Type: application/json"
		];
		// Задаем заголовки
		curl_setopt($curlRequest, CURLOPT_HTTPHEADER, $headers);
		// Указываем, что это POST запрос
		curl_setopt($curlRequest, CURLOPT_POST, true);
		// Указываем, что нам нужно сохранить вывод сервера
		curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);
		// Указываем, что мы его отправляем
		curl_setopt($curlRequest, CURLOPT_POSTFIELDS, json_encode($rawArguments));
		// Отправляем запрос, получаем ответ
		$serverOutput = curl_exec($curlRequest);
		// Закрываем запрос
		curl_close($curlRequest);

	}
}
