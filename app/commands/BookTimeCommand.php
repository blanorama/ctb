<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookTimeCommand extends BaseCommand {

	protected $name = 'time:book';
	protected $description = 'Book working time with start and end, optionally for a specific date.';

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
            ['option', InputArgument::REQUIRED, 'arbitrary option strings for special handling in logic'],
            ['start', InputArgument::REQUIRED, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
			['end', InputArgument::REQUIRED, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
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
            $start = handleTimeArgument($this->argument('option'), $this->argument('start'));
            $end = handleTimeArgument($this->argument('option'), $this->argument('end'));
            $date = handleDateArgument($this->argument('date'));

            $this->info(sprintf('[ACTION] Book working time %s - %s on %s', $start, $end, getInfoDate($date)));
			$phprojekt->getTimecardApi()->logWorkingHours($date, $start, $end);
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
