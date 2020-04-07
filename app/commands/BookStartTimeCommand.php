<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookStartTimeCommand extends BaseCommand {

	protected $name = 'time:start:book';
	protected $description = 'Book start of working time, optionally for a specific date';

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
		$dateString = $this->argument('date');
        $date = handleDateArgument($this, $dateString);

		if (strlen($start) != 4) {
			$this->error('[Response] Wrong format... Please use 0100 [1970-01-01] as example.');
			exit();
		}

		try {
			$this->info('[Action] Book working start at '.$start);

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logStartWorkingTime(
                $date,
				$start
			);

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}

		$this->call('t:list', ['date' => $dateString]);
	}
}
