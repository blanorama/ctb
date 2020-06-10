<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class BookTaskSwitchCommand extends BaseCommand {

	protected $name = 'time:task:switch';
	protected $description = 'Stop and start working time to book a task switch; "rounded" or "precise" defined by first
	arg; if the second optional decimal hours arg is there, the task switch will be booked accordingly in the past';

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
            ['time', InputArgument::OPTIONAL, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
        ];
    }

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doStartWorkingtime($phprojekt)
	{
        try {
            $time = handleTimeArgument($this, $this->argument('option'), $this->argument('time'));
            $date = getNowDateTime();
            $infoDate = getInfoDate($date);

            if ($option === 'rounded') {
                $this->info(sprintf('[ACTION] Switch task at %s on %s', $time, $infoDate));
                $time = getRoundedTimestamp(getNowDateTime());
                $phprojekt->getTimecardApi()->logEndWorkingTime($date, $time);
                $phprojekt->getTimecardApi()->logStartWorkingTime($date, $time);
            } else if ($option === 'precise') {
                if ($time !== null) $this->error('[ERROR] Duration in "precise" mode not supported');
                else {
                    $this->info('[ACTION] Switch task on '. $infoDate);
                    $phprojekt->getTimecardApi()->workEnd();
                    $phprojekt->getTimecardApi()->workStart();
                }
            } else {
                $this->error(sprintf('[ERROR] Unknown option "%s"; possible values: "rounded", "precise"', $option));
            }
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] '.$e);
		}
	}
}
