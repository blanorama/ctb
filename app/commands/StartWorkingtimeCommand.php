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
            ['option', InputArgument::OPTIONAL, 'arbitrary option strings for special handling in logic'],
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
                $this->call('t:s:book', ['start' => $start]);
            } else {
                $this->info('[Action] Start working time');
                $phprojekt->getTimecardApi()->workStart();
            }

		} catch(InvalidArgumentException $e) {
			$this->error('[Response] Working time already started');
		}

		$this->call('t:list');
	}
}
