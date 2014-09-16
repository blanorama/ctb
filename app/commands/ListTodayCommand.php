<?php

class ListTodayCommand extends BaseCommand {

	protected $name = 't:list';
	protected $description = 'List booked working time today.';

	public function fire()
	{
		$phprojekt = new Phprojekt();

		$this->doLogin($phprojekt);
		$this->doListWorkingtime($phprojekt);
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	protected function doListWorkingtime($phprojekt)
	{
		try {
//			$this->info('[Action] Fetch working time...');
			$phprojekt->listWorkingtimeToday();

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] No bookings today...');
		}
	}
}
