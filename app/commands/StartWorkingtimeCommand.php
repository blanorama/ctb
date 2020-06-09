<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class StartWorkingtimeCommand extends BaseCommand {

	protected $name = 'time:start';
	protected $description = 'Start working time; "rounded" or "precise" defined by first arg';

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
            ['duration', InputArgument::OPTIONAL, 'duration in decimal hours'],
        ];
    }

    /**
     * @param Phprojekt $phprojekt
     * @throws Exception
     */
	protected function doStartWorkingtime($phprojekt)
	{
        $option = $this->argument('option');

        try {
            if ($option == 'rounded') {
                $start = getRoundedTimestamp(getNowDateTime());
                $this->call('start:time:book', ['start' => $start]);
            } else if ($option == 'precise') {
                $date = getNowDateTime();
                $this->info('[ACTION] Start working time on '. getInfoDate($date));
                $phprojekt->getTimecardApi()->workStart();
                ListTimeCommand::renderWorklogTable($phprojekt, $date);
            } else {
                $this->error(sprintf('[ERROR] Unknown option "%s"; possible values: "rounded", "precise"', $option));
            }
        } catch(InvalidArgumentException $e) {
			$this->error('[ERROR] Working time already started: '.$e);
		}
	}
}
