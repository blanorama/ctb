<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookPauseCommand extends BaseCommand {

	protected $name = 'pause:book';
	protected $description = 'Book pause time with end and re-start working, optionally for a specific date.';

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
            ['end', InputArgument::REQUIRED, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
			['start', InputArgument::REQUIRED, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
			['date', InputArgument::OPTIONAL, 'Date YYYY-MM-DD to book time for'],
		];
	}

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doBookTime($phprojekt)
	{
		try {
            $times = handleTimeArguments($this->argument('end'), $this->argument('start'));
            $date = handleDateArgument($this->argument('date'));

            $this->info(sprintf('[ACTION] Book pause time %s - %s on %s', $times[0], $times[1], getInfoDate($date)));
            $timeCardApi = $phprojekt->getTimecardApi();
            $timeCardApi->logEndWorkingTime($date, $times[0]);
            $timeCardApi->logStartWorkingTime($date, $times[1]);
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
