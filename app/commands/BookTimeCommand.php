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
		$date = handleDateArgument($this, $this->argument('date'));

		if (strlen($start) != 4 || strlen($end) != 4) {
			$this->error('[Response] Wrong format... Please use 0100 0200 [1970-01-01] as example.');
			exit();
		}

		try {
            $this->info(sprintf('[Action] Book working time %s - %s on %s', $start, $end, getInfoDate($date)));

			$phprojekt->getTimecardApi()->logWorkingHours($date, $start, $end);

            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}
	}
}
