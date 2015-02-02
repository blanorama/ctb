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
            $overtime = $this->getOvertime();
            $vacationDays = $this->getVacationDaysLeft();

            // @TODO: Build table with information above
            $table = new Table(new ConsoleOutput());
            $table->addRow(['Overtime', $overtime]);
            $table->addRow(['Vacation days left', $vacationDays ]);

            return $table->render();

        } catch(InvalidArgumentException $e) {
            $this->error('[Response] No information retrieved.');
        }
    }

    protected function getOvertime()
    {
        // @TODO: Use API when implemented getPtimecontrolApi
        $pTimeControl = $this->phprojekt->getClient()->request(
            'GET',
            $this->phprojekt->getProjectUrl() . '/ptimecontrol/ptc.php'
        );

        $xpath = '//*[@id="global-content"]/table/tr/td[4]';
        $filteredContent = $pTimeControl->filterXPath($xpath);
        return $filteredContent->html();
    }

    protected function getVacationDaysLeft()
    {
        // @TODO: Use API when implemented getPtimecontrolApi
        $pTimeControl = $this->phprojekt->getClient()->request(
            'GET',
            $this->phprojekt->getProjectUrl() . '/vacation/index.php'
        );

        $xpath = '//*[@id="vacation_summary"]/div[2]/table/tr[3]/td[9]';
        $filteredContent = $pTimeControl->filterXPath($xpath);
        return $filteredContent->html();
    }
}
