<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookTimeCommand extends BaseCommand {

	protected $name = 't:t';
	protected $description = 'Book workingtime.';

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
			['start', InputArgument::REQUIRED, 'Started working at XXXX'],
			['end', InputArgument::REQUIRED, 'Stopped working at XXXX'],
		];
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	protected function doBookTime($phprojekt)
	{
		$start = trim($this->argument('start'));
		$end = trim($this->argument('end'));

		if (strlen($start) != 4 || strlen($end) != 4) {
			exit($this->error('[Response] Wrong format... Please use 0100 as example.'));
		}

		try {
			$this->info('[Action] Book working time');

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logWorkingHours(
				new \DateTime(),
				$start,
				$end
			);

			$this->info('[Action] Done');

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Something failed here...');
		}

		$this->call('t:list');
	}

}
