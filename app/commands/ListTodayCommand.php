<?php

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class ListTodayCommand extends BaseCommand {

	protected $name = 't:list';
	protected $description = 'List booked working time today.';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doListWorkingtime($phprojekt);
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	protected function doListWorkingtime($phprojekt)
	{
		try {

			list($workingTimeList, $overall) = $phprojekt->listWorkingtimeToday();

			$table = new Table(new ConsoleOutput());
			$table->setHeaders(['Start', 'End', 'Sum']);

			foreach ($workingTimeList as $row) {
				$table->addRow($row);
			}

			$table->addRow(new TableSeparator());

			$table->addRow([
				'',
				'Overall',
				$overall
			]);

			exit($table->render());

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] No bookings today...');
		}
	}
}
