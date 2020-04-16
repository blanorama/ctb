<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class StopWorkingtimeCommand extends BaseCommand {

	protected $name = 'time:stop';
	protected $description = 'Stop workingtime.';

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
            ['option', InputArgument::OPTIONAL, 'arbitrary option strings for special handling in logic'],
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
            if($option == "round") {
                $stop = getRoundedTimestamp(getNowDateTime());
                $this->call('time:end:book', ['end' => $stop]);
            } else {
                $date = getNowDateTime();
                $this->info('[ACTION] Stop working time on '. getInfoDate($date));
                $phprojekt->getTimecardApi()->workEnd();
                ListTimeCommand::renderWorklogTable($phprojekt, $date);
            }
        } catch(InvalidArgumentException $e) {
			$this->error('[RESPONSE] No active working time found');
		}
	}
}
