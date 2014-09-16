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

		$username = getenv('PHPROJEKT_USERNAME');
		$password = getenv('PHPROJEKT_PASSWORD');

		try {
			$error = $phprojekt->login($username, $password);

			if ($error === 'Sorry you are not allowed to enter.') {
				exit($this->comment('[Response] Login Failed!'));
			}

//			$this->comment('[Response] Login successful');
		} catch(InvalidArgumentException $e) {
		}
	}
}
