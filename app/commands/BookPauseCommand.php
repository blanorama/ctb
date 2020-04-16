<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookPauseCommand extends BaseCommand {

	protected $name = 'pause:book';
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
			['end', InputArgument::REQUIRED, 'Stopped working at HHMM'],
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
		$end = handleTimeArgument($this, $this->argument('end'));
		$start = handleTimeArgument($this, $this->argument('start'));
		$date = handleDateArgument($this, $this->argument('date'));

		try {
            $this->info(sprintf('[ACTION] Book pause time %s - %s on %s', $end, $start, getInfoDate($date)));

            $timeCardApi = $phprojekt->getTimecardApi();
            $timeCardApi->logEndWorkingTime($date, $end);
            $timeCardApi->logStartWorkingTime($date, $start);

            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[RESPONSE] Something failed here...');
		}
	}
}
