<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class BookTaskSwitchCommand extends BaseCommand {

	protected $name = 'time:task:switch';
	protected $description = 'Stop and start working time to book a task switch; "rounded" or "precise" defined by first
	arg; if the second optional time/hours arg is there, the task switch will be booked accordingly in the past';

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
            $time = handleTimeArgument($this->argument('option'), $this->argument('time'));
            $date = getNowDateTime();
            $infoDate = getInfoDate($date);

            $this->info(sprintf('[ACTION] Switch task at %s on %s', $time, $infoDate));
            $phprojekt->getTimecardApi()->logEndWorkingTime($date, $time);
            $phprojekt->getTimecardApi()->logStartWorkingTime($date, $time);
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
