<?php

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
//use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
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

    /**
     * @param Phprojekt $phprojekt
     */
    protected function doListOverview()
    {
        try {
            $overtime = $this->phprojekt->getPtimecontrolApi()->getOvertimeOverall();
            $vacationDays = $this->phprojekt->getPtimecontrolApi()->getVacationDays();

            $table = new Table(new ConsoleOutput());
            $table->addRow(['Overtime', $overtime]);
            $table->addRow(['Vacation days left', $vacationDays ]);

            return $table->render();

        } catch(InvalidArgumentException $e) {
            $this->error('[Response] No information retrieved.');
        }
    }
}
