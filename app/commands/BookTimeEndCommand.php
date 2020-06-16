<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookTimeEndCommand extends BaseCommand {

	protected $name = 'end:time:book';
	protected $description = 'Book end of working time, optionally for a specific date.';

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
            ['end', InputArgument::OPTIONAL, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
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
            $end = handleTimeArgument($this->argument('option'), $this->argument('end'));
            $date = handleDateArgument($this->argument('date'));

            $this->info(sprintf('[ACTION] Book working end at %s on %s', $end, getInfoDate($date)));
			$phprojekt->getTimecardApi()->logEndWorkingTime($date, $end);
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
