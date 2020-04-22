<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookMissedPauseCommand extends BaseCommand {

	protected $name = 'pause:missed:book';
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
			['end', InputArgument::REQUIRED, 'Ended working at HHMM'],
            ['option', InputArgument::REQUIRED, 'arbitrary option strings for special handling in logic'],
		];
	}

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doBookTime($phprojekt)
	{
		$end = handleTimeArgument($this, $this->argument('end'));
        $option = $this->argument('option');

		try {
            $info = '[ACTION] Follow up missed pause start at '.$end;
            $date = getNowDateTime();
            $infoDate = getInfoDate($date);
			$phprojekt->getTimecardApi()->logEndWorkingTime($date, $end);

            if($option == 'rounded') {
                $start = getRoundedTimestamp(getNowDateTime());
                $this->info(sprintf('%s, start working at %s on %s', $info, $start, $infoDate));
                $phprojekt->getTimecardApi()->logStartWorkingTime($date, $start);
            } else {
                $this->info(sprintf('%s, start working now on %s', $info, $infoDate));
                $phprojekt->getTimecardApi()->workStart();
            }

            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[RESPONSE] Something failed here...');
		}
	}
}
