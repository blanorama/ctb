<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class AddProjectTimeCommand extends BaseCommand {

	protected $name = 'project:book';
	protected $description = 'Book project time.';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doAddProjectTime($phprojekt);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['project', InputArgument::REQUIRED, 'ProjectID'],
			['time', InputArgument::REQUIRED, 'Working on this project for XXXX (hours|minutes)'],
			['description', InputArgument::REQUIRED, 'Description what you did'],
		];
	}

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doAddProjectTime($phprojekt)
	{
		$project = $this->argument('project');
		$time = $this->argument('time');
		$description = $this->argument('description');

		if (strlen($time) !== 4) {
            $this->error('[ERROR] Wrong format... Please use 0100 as example.');
			exit();
		}

		$hours = $time[0] . $time[1];
		$minutes = $time[2] . $time[3];

		try {
			$this->info('[ACTION] Book project time');

			$timeCardApi = $phprojekt->getTimecardApi();
			$timeCardApi->logProjectHours(
				new DateTime(),
				$project,
				$hours + $minutes / 60,
				$description
			);

			$this->call('p:list');

		} catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
			$this->comment($e->getMessage());
		}
	}

}
