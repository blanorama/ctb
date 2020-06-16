<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookMissedPauseCommand extends BaseCommand {

	protected $name = 'missed:pause:book';
	protected $description = 'Book missed pause start and automatic working start;
	                         "rounded" or "precise" defined by first arg';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doBookTime($phprojekt);
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
			['end', InputArgument::REQUIRED, 'duration in decimal hours if < 7 or time in [H]H[MM]'],
		];
	}

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doBookTime($phprojekt)
	{
		try {
            $end = handleTimeArgument($this->argument('option'), $this->argument('end'));

            $info = '[ACTION] Follow up missed pause start at '.$end;
            $date = getNowDateTime();
            $infoDate = getInfoDate($date);
			$phprojekt->getTimecardApi()->logEndWorkingTime($date, $end);

            $start = handleTimeArgument($this->argument('option'), null);
            $this->info(sprintf('%s, start working at %s on %s', $info, $start, $infoDate));
            $phprojekt->getTimecardApi()->logStartWorkingTime($date, $start);
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
