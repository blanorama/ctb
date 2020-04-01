<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookDateTimeCommand extends BaseCommand {

	protected $name = 'date:time:book';
	protected $description = 'Book workingtime for a specific date';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doBookTime($phprojekt);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['date', InputArgument::REQUIRED, 'Date YYYY-MM-DD to book time for'],
			['start', InputArgument::REQUIRED, 'Started working at HHMM'],
			['end', InputArgument::REQUIRED, 'Stopped working at HHMM'],
		];
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	protected function doBookTime($phprojekt)
	{
		$date = trim($this->argument('date'));
		$start = trim($this->argument('start'));
		$end = trim($this->argument('end'));

		if (strlen($date) != 10 || strlen($start) != 4 || strlen($end) != 4) {
			$this->error('[Response] Wrong format... Please use 1970-01-01 0100 0200 as example.');
			exit();
		}

		try {
			$this->info('[Action] Book working time');

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logWorkingHours(
				new DateTime(),
				$start,
				$end
			);

			$this->info('[Action] Done');

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}

		$this->call('t:list');
	}

}
