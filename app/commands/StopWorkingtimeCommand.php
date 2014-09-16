<?php

class StopWorkingtimeCommand extends BaseCommand {

	protected $name = 't:stop';
	protected $description = 'Stop workingtime.';

	public function fire()
	{
		$phprojekt = new Phprojekt();

		$this->doLogin($phprojekt);
		$this->doStopWorkingtime($phprojekt);
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	protected function doStopWorkingtime($phprojekt)
	{
		try {
			$this->info('[Action] Stop working time');
			$phprojekt->stopWorkingtime();
			$this->info('[Action] Done');

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] No active workingtime found');
		}

		$this->call('t:list');
	}
}
