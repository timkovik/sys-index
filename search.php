<?php

// базовый класс по работе с поиском, определяющий все методы, которые будут использовать его наследники

abstract class Search 

{

	public $url;

	//в конструктор передаем url конкретной страницы для которой нужно будет получать данные
	public function __construct($url)
	{
		$this->url = $url;
	}

	// метод для отправки любого get-запроса
	public function sendGetRequest($token='')
	{
		$ch = curl_init();
		$url = $this->getIndexUrl();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if ( $token != false ) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
		}

		$output = curl_exec($ch);
		$info = curl_getinfo($ch);

		curl_close($ch);

		if ($output === false || $info['http_code'] != 200) {
			$output = "Что-то пошло не так [". $info['http_code']. "]";
		}

		return $output;
	}

	/* наследники должны реализовать метод получения url на который отправлять get-запрос для проверки
	  нахождения страницы в индексе */
	abstract public function getIndexUrl();


	/* наследники должны реализовать метод проверки ответа поисковика, 
	чтобы определить нахождение страницы в индексе */
	abstract public function checkIndex();

}


// класс для работы с Яндексом
class YandexSearch extends Search 

{

	const YANDEX_PATH = "https://yandex.ru/search/?text=url:";
	const INDICATOR = 'По вашему запросу ничего не нашлось';


	public function getIndexUrl() 
	{
		$indexUrl = self::YANDEX_PATH . $this->url;
		return $indexUrl;
	}

	public function checkIndex()
	{
		$output = $this->sendGetRequest();
		$inIndex = ( stripos($output, self::INDICATOR)  === false ) ? true : false;
		return $inIndex;
	}

}


// класс для работы с Гуглом
class GoogleSearch extends Search 

{

	const GOOGLE_PATH = "https://www.google.ru/search?newwindow=1&source=hp&oq=mm&gs_l=psy-ab.3..0i10i1i67i42k1j0j0i67k1j0l2j0i67k1l2j0l3.82442.82738.0.83367.5.3.0.0.0.0.82.152.2.3.0....0...1.1.64.psy-ab..2.3.234.6..35i39k1j0i131k1.84.HTeli9fiitE&q=site:";
	const INDICATOR = "ничего не найдено";

	public function getIndexUrl() 
	{
		$indexUrl = self::GOOGLE_PATH . $this->url;
		return $indexUrl;
	}

	public function checkIndex()
	{
		$output = $this->sendGetRequest();
		$inIndex = ( stripos($output, self::INDICATOR)  === false ) ? true : false;
		return $inIndex;
	}

}

?>