<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class ListDateCommand extends BaseCommand {

	protected $name = 'date:list';
	protected $description = 'List booked working time for a specific date.';

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
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['date', InputArgument::REQUIRED, 'Date YYYY-MM-DD to look up time for'],
        ];
    }

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doListWorkingtime($phprojekt)
	{
        $date = trim($this->argument('date'));

        try {

			$timeCardApi = $phprojekt->getTimecardApi();
			$workLog = $timeCardApi->getWorkingHours(DateTime::createFromFormat('Y-m-d', $date));
            ListTodayCommand::renderWorklogTable($workLog);
			exit();

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] No bookings today...');
		}
	}
}
