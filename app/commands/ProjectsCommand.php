<?php

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class ProjectsCommand extends BaseCommand {

	protected $name = 'p:list';
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
		list($projects, $stillToBook, $overallBookings) = $phprojekt->listProjects();

		$table = new Table(new ConsoleOutput());
		$table->setHeaders(['Project', 'Bookings']);

		foreach($projects as $index => $project) {
			$table->addRow([
					sprintf("%s (%s)", $project['name'], $index),
					implode("\n", $project['bookings'])
				]);
			$table->addRow(new TableSeparator());
		}

		$table->addRow([
				'',
				$stillToBook
			]);

		$table->addRow([
				'Overall',
				$overallBookings
			]);

		echo $table->render();
	}

}
