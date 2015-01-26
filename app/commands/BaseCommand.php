<?php

use Illuminate\Console\Command;

class BaseCommand extends Command {

	/**
	 * @var null|Goutte\Client
	 */
	protected $crawler = null;

	/**
	 * @param Phprojekt $phprojekt
	 * @param $username
	 * @param $password
	 */
	protected function doLogin($phprojekt)
	{
//		$this->comment('[Action] Try to login');

		try {
			$success = $phprojekt->login();

			if (!$success) {
				exit($this->comment('[Response] Login Failed!'));
			}

//			$this->comment('[Response] Login successful');
		} catch(InvalidArgumentException $e) {
		}
	}
}
