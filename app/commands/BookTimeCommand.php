<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookTimeCommand extends BaseCommand {

	protected $name = 'time:book';
	protected $description = 'Book working time, optionally for a specific date';

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
			['start', InputArgument::REQUIRED, 'Started working at HHMM'],
			['end', InputArgument::REQUIRED, 'Stopped working at HHMM'],
			['date', InputArgument::OPTIONAL, 'Date YYYY-MM-DD to book time for'],
		];
	}

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doBookTime($phprojekt)
	{
		$start = handleTimeArgument($this->argument('start'));
		$end = handleTimeArgument($this->argument('end'));
	    $dateString = $this->argument('date');
		$date = handleDateArgument($this, $dateString);

		if (strlen($start) != 4 || strlen($end) != 4) {
			$this->error('[Response] Wrong format... Please use 0100 0200 [1970-01-01] as example.');
			exit();
		}

		try {
			$this->info('[Action] Book working time '.$start.' - '.$end.' on '.$date->format('d.m.Y'));

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logWorkingHours(
                $date,
				$start,
				$end
			);
		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}

		$this->call('t:list', ['date' => $dateString]);
	}
}
