<?php

use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;
use Symfony\Component\Console\Input\InputArgument;

class StartWorkingtimeCommand extends BaseCommand {

	protected $name = 'time:start';
	protected $description = 'Start workingtime.';

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

        try {
            if($option == "round") {
                $start = getRoundedTimestamp(getNowDateTime());
                $this->call('time:start:book', ['start' => $start]);
            } else {
                $date = getNowDateTime();
                $this->info('[ACTION] Start working time on '. getInfoDate($date));
                $phprojekt->getTimecardApi()->workStart();
                ListTimeCommand::renderWorklogTable($phprojekt, $date);
            }
        } catch(InvalidArgumentException $e) {
			$this->error('[RESPONSE] Working time already started');
		}
	}
}
