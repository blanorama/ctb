<?php

use Symfony\Component\Console\Input\InputArgument;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BookMissedTimeCommand extends BaseCommand {

	protected $name = 'missed:time:book';
	protected $description = 'Book missed time start and automatic end; rounded" or "precise" defined by first arg';

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
			['start', InputArgument::REQUIRED, 'Ended working at HHMM'],
		];
	}

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doBookTime($phprojekt)
	{
        $option = $this->argument('option');
		$start = handleTimeArgument($this, $this->argument('start'));

		try {
            $info = '[ACTION] Follow up missed work start at '.$start;
            $date = getNowDateTime();
            $infoDate = getInfoDate($date);
			$phprojekt->getTimecardApi()->logStartWorkingTime($date, $start);

            if($option == 'rounded') {
                $end = getRoundedTimestamp(getNowDateTime());
                $this->info(sprintf('%s, working end at %s on %s', $info, $end, $infoDate));
                $phprojekt->getTimecardApi()->logEndWorkingTime($date, $end);
            } else if($option == 'precise') {
                $this->info(sprintf('%s, working end now on %s', $info, $infoDate));
                $phprojekt->getTimecardApi()->workEnd();
            } else {
                $this->error(sprintf('[ERROR] Unknown option "%s"; possible values: "rounded", "precise"', $option));
            }
            ListTimeCommand::renderWorklogTable($phprojekt, $date);
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Something failed here: '.$e);
		}
	}
}
