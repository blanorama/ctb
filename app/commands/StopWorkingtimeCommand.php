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
                $this->call('t:e:book', ['end' => $stop]);
            } else {
                $this->info('[Action] Stop working time');
                $phprojekt->getTimecardApi()->workEnd();
            }

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] No active working time found');
		}

		$this->call('t:list');
	}
}
