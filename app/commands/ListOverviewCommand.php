<?php

use PhprojektRemoteApi\Tools\Convert;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use CtbPhprojektRemoteApi as Phprojekt;

class ListOverviewCommand extends BaseCommand {

    protected $name = 'time:overview';
    protected $description = 'List vacation and overtime information.';

    /**
     * @var Phprojekt
     */
    protected $phprojekt;

    public function fire()
    {
        $phprojekt = new Phprojekt(
            getenv('PHPROJEKT_URL'),
            getenv('PHPROJEKT_USERNAME'),
            getenv('PHPROJEKT_PASSWORD')
        );
        $this->phprojekt = $phprojekt;

        $this->doLogin($phprojekt);
        $this->doListOverview();

    }

    protected function doListOverview()
    {
        try {
            $overtime = $this->phprojekt->getPtimecontrolApi()->getOvertimeOverall();
            $vacationDays = $this->phprojekt->getPtimecontrolApi()->getVacationDays();

            $table = new Table(new ConsoleOutput());
            $table->addRow(['Overtime hours', Convert::text2hours($overtime)]);
            $table->addRow(['Vacation days', $vacationDays ]);

            $table->render();
            return;

        } catch(InvalidArgumentException $e) {
            $this->error('[Response] No information retrieved.');
        }
    }
}
