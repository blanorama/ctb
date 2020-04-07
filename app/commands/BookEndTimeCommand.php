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
		$end = handleTimeArgument($this->argument('end'));
		$dateString = $this->argument('date');
        $date = handleDateArgument($this, $dateString);

		if (strlen($end) != 4) {
			$this->error('[Response] Wrong format... Please use 0100 [1970-01-01] as example.');
			exit();
		}

		try {
			$this->info('[Action] Book working end at '.$end.' on '.$date->format('d.m.Y'));

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logEndWorkingTime(
                $date,
				$end
			);
		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}

		$this->call('t:list', ['date' => $dateString]);
	}
}
