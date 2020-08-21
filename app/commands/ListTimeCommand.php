<?php

use PhprojektRemoteApi\Tools\Convert;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Output\ConsoleOutput;

define("TIME_FORMAT_OUTPUT", "H:i");

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
        try {
            $date = handleDateArgument($this->argument('date'));

            $this->info('[INFO] Listing working time for '. getInfoDate($date));
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
        $rows = $workLog->getLog();
        usort($rows, function ($first, $second) {
            return $first->getStart()->diff($second->getStart())->invert;
        });

        $table = new Table(new ConsoleOutput());
        $table->setHeaders(['Start', 'End', 'Sum', 'Summation']);

        foreach ($rows as $row) {

            $start = $row->getStart();
            $end   = $row->getEnd();

            if ($end !== null) {
                $diff  = $start->diff($end);

                $table->addRow([
                    $start->format(TIME_FORMAT_OUTPUT),
                    $end->format(TIME_FORMAT_OUTPUT),
                    Convert::text2hours($diff->format('%h : %i')),
                    $diff->format('%h h %i m')
                ]);
            } else {
                $table->addRow([
                    $start->format(TIME_FORMAT_OUTPUT)
                ]);
            }

        }

        $table->addRow(new TableSeparator());

        $table->addRow([
            '',
            'Overall',
            $workLog->getOverallTime(),
            $workLog->getOverallTimeString()
        ]);

        $table->render();
    }
}
