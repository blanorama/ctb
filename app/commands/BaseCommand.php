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
			$success = $phprojekt->login();

			if (!$success) {
				$this->comment('[Response] Login Failed!');
				exit();
			}
		} catch(InvalidArgumentException $e) {
		}
	}
}
