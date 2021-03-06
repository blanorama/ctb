<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class StopWorkingtimeCommand extends BaseCommand {

	protected $name = 'time:stop';
	protected $description = 'Stop working time; "rounded" or "precise" defined by first arg';

	public function fire()
	{
		$phprojekt = new Phprojekt(
			getenv('PHPROJEKT_URL'),
			getenv('PHPROJEKT_USERNAME'),
			getenv('PHPROJEKT_PASSWORD')
		);

		$this->doLogin($phprojekt);
		$this->doStopWorkingtime($phprojekt);
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
	protected function doStopWorkingtime($phprojekt)
	{
        $option = $this->argument('option');

        try {
            if ($option === 'rounded') {
                $stop = getRoundedTimestamp(getNowDateTime());
                $this->call('end:time:book', ['end' => $stop]);
            } else if ($option === 'precise') {
                $date = getNowDateTime();
                $this->info('[ACTION] Stop working time on '. getInfoDate($date));
                $phprojekt->getTimecardApi()->workEnd();
                ListTimeCommand::renderWorklogTable($phprojekt, $date);
            } else {
                $this->error(sprintf('[ERROR] Unknown option "%s"; possible values: "rounded", "precise"', $option));
            }
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] No active working time found: '.$e);
		}
	}
}
