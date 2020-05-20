<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class BookTaskSwitchCommand extends BaseCommand {

	protected $name = 'time:task:switch';
	protected $description = 'Stop and start working time to book a task switch;
	                         "rounded" or "precise" defined by first arg';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doStartWorkingtime($phprojekt);
	}

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['option', InputArgument::REQUIRED, 'arbitrary option strings for special handling in logic'],
        ];
    }

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doStartWorkingtime($phprojekt)
	{
        $option = $this->argument('option');
        $date = getNowDateTime();
        $infoDate = getInfoDate($date);

        try {
            if($option == 'rounded') {
                $time = getRoundedTimestamp(getNowDateTime());
                $this->info(sprintf('[ACTION] Switch task at %s on %s', $time, $infoDate));
                $phprojekt->getTimecardApi()->logEndWorkingTime($date, $time);
                $phprojekt->getTimecardApi()->logStartWorkingTime($date, $time);
            } else if($option == 'precise') {
                $this->info('[ACTION] Switch task on '. $infoDate);
                $phprojekt->getTimecardApi()->workEnd();
                $phprojekt->getTimecardApi()->workStart();
            } else {
			    $this->error(sprintf('[ERROR] Unknown option "%s"; possible values: "rounded", "precise"', $option));
            }
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Working time already started: '.$e);
		}
	}
}
