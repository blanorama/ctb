<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class StartWorkingtimeCommand extends BaseCommand {

	protected $name = 't:start';
	protected $description = 'Start workingtime.';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doStartWorkingtime($phprojekt);
	}



	/**
	 * @param $phprojekt
	 */
	protected function doStartWorkingtime($phprojekt)
	{
		try {
			$this->info('[Action] Start working time');
			$phprojekt->startWorkingtime();
			$this->info('[Action] Done');

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Working time already started');
		}

		$this->call('t:list');
	}

}
