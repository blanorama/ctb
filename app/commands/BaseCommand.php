<?php

use Illuminate\Console\Command;
use PhprojektRemoteApi\PhprojektRemoteApi as Phprojekt;

class BaseCommand extends Command {

	/**
	 * @var null|Goutte\Client
	 */
	protected $crawler = null;

    /**
     * @param Phprojekt $phprojekt
     */
	protected function doLogin($phprojekt)
	{
		try {
            setlocale(LC_ALL, 'de_DE.UTF-8');
            date_default_timezone_set("Europe/Berlin");
            $success = $phprojekt->login();

			if (!$success) {
				$this->comment('[RESPONSE] Login Failed!');
				exit();
			}
		} catch(InvalidArgumentException $e) {
		}
	}
}
