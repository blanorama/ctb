<?php

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class ProjectsCommand extends BaseCommand {

	protected $name = 'project:list';
	protected $description = 'List favorite projects.';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->listProjects($phprojekt);
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	private function listProjects($phprojekt)
	{
		$timeCardApi = $phprojekt->getTimecardApi();
		$projectLog = $timeCardApi->getProjectBookings(new \DateTime());

		$table = new Table(new ConsoleOutput());
		$table->setHeaders(['Project', 'Description', 'Hours']);

		foreach($projectLog as $log) {
			$table->addRow([
				sprintf("%s (%s)", $log->getName(), $log->getProjectIndex()),
				$log->getDescription(),
				$log->getHours()
			]);
			$table->addRow(new TableSeparator());
		}

		$table->addRow([
				'Noch zu buchen',
				'',
				$projectLog->getRemainingWorkLog()
			]);

		$table->addRow([
				'Overall',
				'',
				$projectLog->getBookedHours()
			]);

		echo $table->render();
	}

}
