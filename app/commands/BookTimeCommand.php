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
		    $times = handleTimeArguments($this->argument('start'), $this->argument('end'));
            $date = handleDateArgument($this->argument('date'));

            $this->info(sprintf('[ACTION] Book working time %s - %s on %s',
                $times[0], $times[1], getInfoDate($date)));
			$phprojekt->getTimecardApi()->logWorkingHours($date, $times[0], $times[1]);
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
