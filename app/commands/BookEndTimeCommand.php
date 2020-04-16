<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookEndTimeCommand extends BaseCommand {

	protected $name = 'time:end:book';
	protected $description = 'Book end of working time, optionally for a specific date';

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
			['end', InputArgument::REQUIRED, 'Ended working at HHMM'],
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
        $date = handleDateArgument($this, $this->argument('date'));

		try {
            $this->info(sprintf('[Action] Book working end at %s on %s', $end, getInfoDate($date)));

			$phprojekt->getTimecardApi()->logEndWorkingTime($date, $end);

            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}
	}
}
