<?php

require('search.php');


// Класс для формирования отчета
class Report

{
	public $url;
	public $reportData;
	public $yandexReport;
	public $googleReport;

	public function __construct($url)
	{
		$this->url = $url;
		$this->reportData = [];
		$this->yandexReport = new YandexSearch($this->url);
		$this->googleReport = new GoogleSearch($this->url);
	}


	/* Метод для формирования отчета: дата, поисковик, нахождение страницы в индексе. 
	В качестве служебной информации сохраняется дата для следующего отчета.
	Вызывается при нажатии кнопки для получения отчета или при проверке даты.
	*/
	public function indexReport()
	{
		$date = date('d.m.Y');
		$nextDate = $this->dateOfNextReport(7);
		$this->reportData['date'] = $date;
		$this->reportData['nextDate'] = $nextDate;
		$this->reportData['yandex'] = $this->yandexReport->checkIndex();
		$this->reportData['google'] = $this->googleReport->checkIndex();
		return $this->reportData;
	}

	// Метод для вычисления даты следующего отчета, $days - периодичность формирования отчета в днях
	public function dateOfNextReport($days)
	{
		return mktime(0, 0, 0, date('m'), date('d')+$days, date('Y'));
	}

	/* Метод вычисляет, не пора ли формировать новый расчет. Если пора, то она его формирует.
		Должен вызываться при загрузке модуля (страницы с модулем).
	*/
	public function isTimeForReport($nextDate)
	{
		$isTime = ( time() >= $nextDate ) ? true : false;
		echo($isTime);
		if ( $isTime ) {
			return $this->indexReport();
		}
	}
	
}