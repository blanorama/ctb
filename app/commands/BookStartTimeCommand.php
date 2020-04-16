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
		$start = handleTimeArgument($this, $this->argument('start'));
        $date = handleDateArgument($this, $this->argument('date'));

		try {
			$this->info(sprintf('[ACTION] Book working start at %s on %s', $start, getInfoDate($date)));

			$phprojekt->getTimecardApi()->logStartWorkingTime($date, $start);

		    ListTimeCommand::renderWorklogTable($phprojekt, $date);
		} catch(InvalidArgumentException $e) {
			$this->error('[RESPONSE] Something failed here...');
		}
	}
}
