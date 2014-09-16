<?php

use Goutte\Client;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;

class Phprojekt {

	private $baseUrl;

	public function __construct()
	{
		$this->crawler = new Client();
		$this->crawler->getClient()->setDefaultOption('verify', false);
		$this->baseUrl = getenv('PHPROJEKT_URL');
	}

	public function login($username, $password)
	{
		if (empty($username) || empty($password)) {
			return 'Sorry you are not allowed to enter.';
		}

		$crawler = $this->crawler->request('GET', $this->baseUrl);
		$xpath = '//*[@id="global-main"]/div[2]/form/fieldset/input[3]';
		$node = $crawler->filterXPath($xpath);
		$form = $node->form();

		$login = $this->crawler->submit($form, [
			'loginstring' => $username,
			'user_pw' => $password
		]);

		try {
			return trim($login->filter('div > fieldset')->text());
		} catch (InvalidArgumentException $e) {
		}
	}

	/**
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	public function stopWorkingtime()
	{
		$timecard = $this->crawler->request('GET', $this->baseUrl . '/timecard/timecard.php');
		$link = $timecard->selectLink('Arbeitszeit Ende')->link();
		$this->crawler->click($link);

		return $timecard;
	}

	/**
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	public function startWorkingtime()
	{
		$timecard = $this->crawler->request('GET', $this->baseUrl . '/timecard/timecard.php');
		$link = $timecard->selectLink('Arbeitszeit Start')->link();
		$this->crawler->click($link);

		return $timecard;
	}

	public function bookTime($start, $end)
	{
		$timecard = $this->crawler->request('GET', $this->baseUrl . '/timecard/timecard.php');

		$xpath = '//*[@name="nachtragen1"]';
		$node = $timecard->filterXPath($xpath);
		$form = $node->form();

		$form->setValues([
			'timestart' => $start,
			'timestop' => $end
		]);

		return $this->crawler->submit($form);
	}

	public function listWorkingtimeToday()
	{
		$timecard = $this->crawler->request('GET', $this->baseUrl . '/timecard/timecard.php');

		$xpath = '//table[@summary=""]/tbody';
		$node = $timecard->filterXPath($xpath);

		$table = new Table(new ConsoleOutput());
		$table->setHeaders(['Start', 'End', 'Sum']);

		$document = new DOMDocument();
		$document->loadHTML($node->html());

		$times = $document->getElementsByTagName('tr');

		foreach ($times as $time) {
			$specifiedTimeRow = $time->getElementsByTagName('td');

			$newTableRow = [];
			foreach($specifiedTimeRow as $timeRow) {
				array_push($newTableRow, $timeRow->textContent);
			}
			$table->addRow(array_filter($newTableRow));
		}

		$xpath = '//table[@summary=""]/tfoot/tr/td[3]';
		$node = $timecard->filterXPath($xpath);

		$table->addRow(new TableSeparator());
		$table->addRow([
			'', 'Overall', $node->html()
		]);

		exit($table->render());
	}

	public function bookProjectTime($command, $project, $hours, $minutes, $description)
	{
		$projectcard = $this->getProjectCard();

		$this->bookProject($project, $hours, $minutes, $description, $projectcard);
	}

	/**
	 * @param $project
	 * @param $hours
	 * @param $minutes
	 * @param $description
	 * @param $projectcard
	 */
	protected function bookProject($project, $hours, $minutes, $description, $projectcard)
	{
		$xpath = '//form[@name="book"]';
		$node = $projectcard->filterXPath($xpath);

		$formProject = $project - 1;
		$this->crawler->submit($node->form(), [
			"note[$formProject]" => $description,
			"h[$formProject]" => $hours,
			"m[$formProject]" => $minutes
		]);
	}

	/**
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	protected function getProjectCard()
	{
		$timecard = $this->crawler->request('GET', $this->baseUrl . '/timecard/timecard.php');
		$link = $timecard->selectLink('Favoriten')->link();
		$projectcard = $this->crawler->click($link);
		return $projectcard;
	}

	/**
	 * @param $command
	 */
	public function listProjects($command)
	{
		$projectCard = $this->getProjectCard();

		$xpath = '//form[@name="book"]/fieldset/table/tbody';
		$node = $projectCard->filterXPath($xpath);
		$dom = new DOMDocument();
		$dom->loadHTML($node->html());
		$projectsInDom = $dom->getElementsByTagName('tr');

		$projects = [];
		$projectIndex = 0;

		foreach($projectsInDom as $index => $projectRow) {
			$projectTextContent = trim($projectRow->textContent);

			if ($projectRow->hasAttribute('class')) {
				$projects[$projectIndex]['bookings'][] = $projectTextContent;
				continue;
			}

			$projectIndex++;
			$projects[$projectIndex] = ['name' => $projectTextContent, 'bookings' => []];
		}

		$table = new Table(new ConsoleOutput());
		$table->setHeaders(['Project', 'Bookings']);

		foreach($projects as $index => $project) {
			$table->addRow([
				sprintf("%s (%s)", $project['name'], $index),
				implode("\n", $project['bookings'])
			]);
			$table->addRow(new TableSeparator());
		}

		$xpath = '//table[@summary=""]/tfoot/tr/td[2]';
		$node = $projectCard->filterXPath($xpath);

		$table->addRow([
			'', $node->html()
		]);

		$xpath = '//table[@summary=""]/tfoot/tr/td[3]';
		$node = $projectCard->filterXPath($xpath);

		$table->addRow([
			'Overall', $node->html()
		]);

		echo $table->render();
	}
}
