<?php

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class ListTodayCommand extends BaseCommand {

	protected $name = 'time:list';
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
     * @throws Exception
     */
	protected function doListWorkingtime($phprojekt)
	{
		try {

			$timeCardApi = $phprojekt->getTimecardApi();
			$workLog = $timeCardApi->getWorkingHours(new DateTime());

			$table = new Table(new ConsoleOutput());
			$table->setHeaders(['Start', 'End', 'Sum']);

			foreach ($workLog as $row) {

				$start = $row->getStart();
				$end   = $row->getEnd();
				$diff  = $end->diff($start);

				$table->addRow([
					$start->format('H:i'),
					$end->format('H:i'),
					$diff->format('%h h %i m')
				]);
			}

			$table->addRow(new TableSeparator());

			$table->addRow([
				'',
				'Overall',
				$workLog->getOverallTime()
			]);

            $table->render();
			exit();

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] No bookings today...');
		}
	}
}
