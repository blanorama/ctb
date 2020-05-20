<?php

use PhprojektRemoteApi\Tools\Convert;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Output\ConsoleOutput;

class ListTimeCommand extends BaseCommand {

	protected $name = 'time:list';
	protected $description = 'List booked working time for today, optionally a specific date.';

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
            ['date', InputArgument::OPTIONAL, 'Date YYYY-MM-DD to look up time for'],
        ];
    }

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doListWorkingtime($phprojekt)
	{
        $date = handleDateArgument($this, $this->argument('date'));

        try {
            $this->info('[INFO] Listing working time for '.strftime('%a, %x', $date->getTimestamp()));
            $this->renderWorklogTable($phprojekt, $date);
			exit();
		} catch(InvalidArgumentException $e) {
			$this->error('[ERROR] No bookings today: '.$e);
		}
	}

    /**
     * @param Phprojekt $phprojekt
     * @param DateTime $date
     */
    static function renderWorklogTable($phprojekt, $date) {
        $timeCardApi = $phprojekt->getTimecardApi();
        $workLog = $timeCardApi->getWorkingHours($date);

        $table = new Table(new ConsoleOutput());
        $table->setHeaders(['Start', 'End', 'Sum']);

        foreach ($workLog as $row) {

            $start = $row->getStart();
            $end   = $row->getEnd();

            if($end != null) {
                $diff  = $end->diff($start);

                $table->addRow([
                    $start->format('H:i'),
                    $end->format('H:i'),
                    Convert::text2hours($diff->format('%h : %i'))
                ]);
            } else {
                $table->addRow([
                    $start->format('H:i')
                ]);
            }

        }

        $table->addRow(new TableSeparator());

        $table->addRow([
            '',
            'Overall',
            $workLog->getOverallTimeString()
        ]);

        $table->render();
    }
}
